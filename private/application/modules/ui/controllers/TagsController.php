<?php

class Ui_TagsController extends IMDT_Controller_Abstract {

    public function init() {
        $this->filters = array();
        $this->filters['name'] = array('name' => 'name', 'type' => 'text');
    }

    public function indexAction() {
        $this->view->tableSource = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'table-content');
        $this->view->uriPage = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => $this->_request->getActionName());
        $this->view->uriExport = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'export');
        $this->view->uriExportPdf = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'export-pdf');
        $this->view->parameters = IMDT_Util_Url::getThisParams($this->filters);
        $this->view->filters = $this->filters;
    }

    public function exportAction() {
        $this->_disableViewAndLayout();

        $params = IMDT_Util_Url::getThisParams($this->filters);
        $params['export'] = 'csv';

        $headers = array();
        $headers['columns-leach'] = 'record_tag_id,name';
        //$headers['columns-desc'] = $this->_helper->translate('column-record_tag-id').','.$this->_helper->translate('column-record_tag-name');

        $response = IMDT_Util_Rest::get('/api/record-tags.json', $params, $headers);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        if ($this->_request->getParam('utf8', 0)) {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_CSV . '; charset=utf-8');
        } else {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_CSV);
        }
        header('Content-Disposition: attachment; filename="' . $this->_request->getControllerName() . '.csv"');
        echo file_get_contents($response['url']);
        exit;
    }

    public function exportPdfAction() {
        $this->_disableViewAndLayout();

        $params = IMDT_Util_Url::getThisParams($this->filters);
        $params['export'] = 'pdf';

        $headers = array();
        $headers['columns-leach'] = 'name';
        $headers['columns-desc'] = $this->_helper->translate('column-record_tag-name');

        $response = IMDT_Util_Rest::get('/api/record-tags.json', $params, $headers);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        if ($this->_request->getParam('utf8', 0)) {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_PDF . '; charset=utf-8');
        } else {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_PDF);
        }
        header('Set-Cookie: fileDownload=true; path=/');
        header('Content-Disposition: attachment; filename="' . $this->_request->getControllerName() . '.pdf"');
        echo file_get_contents($response['url']);
        exit;
    }

    public function finishedAction() {
        
    }

    public function editAction() {
        $this->view->id = $this->_getParam('id', null);
        $this->view->title = $this->_helper->translate('Edit');
    }

    public function viewAction() {
        $this->view->id = $this->_getParam('id', null);
    }

    public function newAction() {
        $this->view->id = 'new';
        $this->view->title = $this->_helper->translate('New');
        $this->_helper->viewRenderer('edit');
    }

    public function tableContentAction() {
        $objResponse = new stdClass();

        try {
            $params = IMDT_Util_Url::getThisParams($this->filters);
            $response = IMDT_Util_Rest::get('/api/record-tags.json', $params);

            $arrTable = array();
            $key = 'record_tag_id';
            foreach ($response['collection'] as $curr) {
                $row = array();
                $row[] = '<input name="deleteRow[]" class="cboxSelectRow" type="checkbox" value="' . $curr[$key] . '" />';
                $row[] = $curr['name'];
                $actions = '<td nowrap="nowrap">';
                if ($curr['_editable'] == '1') {
                    $actions .= '<a title="' . $this->_helper->translate('Edit') . '" data-toggle="tooltip" class="btn btn-mini"  open-modal-form="#modalNewRecord" form-data=\'{"name": "' . $curr['name'] . '", "id" : "' . $curr[$key] . '"}\' data-original-title="' . $this->_helper->translate('Edit') . '"><i class="icon-pencil"></i></a>';
                } else {
                    $actions .= '<a title="' . $this->_helper->translate('Edit') . '" data-toggle="tooltip" class="btn btn-mini" disabled="disabled" data-original-title="' . $this->_helper->translate('Edit') . '"><i class="icon-pencil"></i></a>';
                }

                if ($curr['_removable'] == '1') {
                    $actions .= '<a title="' . $this->_helper->translate('Delete') . '" data-toggle="tooltip" class="btn btn-mini btn-delete" href="" data-original-title="Excluir" onclick="deleteRow(\'#records\',\'row_' . $curr[$key] . '\',\'/ui/tags/delete/id/' . $curr[$key] . '\'); return false;"><i class="icon-trash"></i></a>';
                } else {
                    $actions .= '<a title="' . $this->_helper->translate('Delete') . '" data-toggle="tooltip" class="btn btn-mini btn-delete" disabled="disabled" data-original-title="Excluir"><i class="icon-trash"></i></a>';
                }

                $actions .= '</td>';
                $row[] = $actions;
                $row['DT_RowId'] = 'row_' . $curr[$key];
                $arrTable[] = $row;
            }

            $objResponse->success = '1';
            $objResponse->msg = '';
            $objResponse->aaData = $arrTable;
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->aaData = array();
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
            $objResponse->aaData = array();
        }
        $this->_helper->json($objResponse);
    }

    public function formPostAction() {
        $objResponse = new stdClass();

        try {
            $id = $this->_request->getParam('id', null);

            if (!$this->_request->isPost())
                throw new Exception($this->_helper->translate('Invalid Request'));

            $data = array();
            $data['name'] = $this->_request->getPost('tag-name', null);
            $data['description'] = $this->_request->getPost('description', null);
            $data['record_id'] = $this->_request->getPost('record_id', null);
            $data['start_time'] = $this->_request->getPost('start_time', null);

            if ($id == 'new') {
                $arrRestResponse = IMDT_Util_Rest::post('/api/record-tags', $data);
            } else {
                $arrRestResponse = IMDT_Util_Rest::put('/api/record-tags/' . $id, $data);
            }

            $objResponse->success = '1';
            $objResponse->msg = $this->_helper->translate('Data was saved successfully');
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

    public function deleteAction() {
        $objResponse = new stdClass();

        try {
            $id = $this->_request->getParam('id');
            IMDT_Util_Rest::delete('/api/record-tags/' . $id . '.json');

            $objResponse->success = '1';
            $objResponse->msg = '';
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

    public function getTagsByRecordingAction() {
        $this->_disableViewAndLayout();

        $objResponse = new stdClass();

        try {
            $recordingId = $this->_getParam('id', null);
            $asHtml = $this->_getParam('asHtml', false);

            $parameters['record_id'] = $recordingId;
            $parameters['record_id_c'] = 'e';

            $response = IMDT_Util_Rest::get('/api/record-tags', $parameters);

            if ($asHtml != false) {
                $htmlStr = '';

                foreach ($response['collection'] as $item) {
                    $htmlStr .= '<tr>';
                    $htmlStr .= '<td>' . $item['name'] . '</td>';
                    $htmlStr .= '<td>' . $item['description'] . '</td>';
                    $htmlStr .= '<td>' . IMDT_Util_Time::millisecondsTohhmmssmil($item['start_time']) . '</td>';
                    $htmlStr .= '<td>';
                    $htmlStr .= '<a title="' . $this->_helper->translate('Edit') . '" data-toggle="tooltip" class="btn btn-mini" href="javascript:void(0);" open-modal-form="#modalTagEditor" form-data=\'' . json_encode(array('tag-name' => $item['name'], 'description' => $item['description'], 'start_time' => IMDT_Util_Time::millisecondsTohhmmssmil($item['start_time']), 'id' => $item['record_tag_id'])) . '\' data-original-title="' . $this->_helper->translate('Edit') . '"><i class="icon-pencil"></i></a>';
                    $htmlStr .= '<a title="' . $this->_helper->translate('Delete') . '" data-toggle="tooltip" class="btn btn-mini tag-delete" href="javascript:void(0);" data-tag-id="' . $item['record_tag_id'] . '" data-original-title="' . $this->_helper->translate('Delete') . '" ><i class="icon-trash"></i></a>';
                    $htmlStr .= '</td>';
                    $htmlStr .= '</tr>';
                }

                $objResponse->html = $htmlStr;
            } else {
                $objResponse->collection = $response['collection'];
            }

            $objResponse->success = '1';
            $objResponse->msg = '';
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

}
