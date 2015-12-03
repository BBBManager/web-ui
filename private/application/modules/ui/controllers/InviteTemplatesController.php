<?php

class Ui_InviteTemplatesController extends IMDT_Controller_Abstract {

    public function init() {
        $this->filters = array();
        $this->filters['name'] = array('name' => 'name', 'label' => $this->_helper->translate('column-invite_template-name'), 'type' => 'text');
        $this->filters['subject'] = array('name' => 'subject', 'label' => $this->_helper->translate('column-invite_template-subject'), 'type' => 'text');
        $this->filters['create_date'] = array('name' => 'create_date', 'label' => $this->_helper->translate('column-invite_template-create_date'), 'type' => 'datetime');

        $this->filters['main_name'] = array('main' => true, 'name' => 'name', 'label' => $this->_helper->translate('column-invite_template-name'), 'type' => 'text', 'condition' => 'in');

        $this->api = 'invite-templates';
        $this->pkey = 'invite_template_id';
    }

    public function indexAction() {
        $this->view->tableSource = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'table-content');
        $this->view->uriPage = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'index');
        $this->view->uriExport = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'export');
        $this->view->uriExportPdf = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'export-pdf');
        $this->view->filters = $this->filters;

        $this->view->jQuery()->addOnload('CKEDITOR.replace("bodytext");');

        $this->view->parameters = IMDT_Util_Url::getThisParams($this->filters);
        $this->view->parametersString = http_build_query($this->view->parameters);
    }

    public function exportAction() {
        $this->_disableViewAndLayout();

        $params = IMDT_Util_Url::getThisParams($this->filters);
        $params['export'] = 'csv';

        $headers = array();
        $headers['columns-leach'] = 'invite_template_id,name,subject,body';
        // $headers['columns-desc'] = $this->_helper->translate('column-invite_template-id');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-invite_template-name');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-invite_template-subject');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-invite_template-body');

        $response = IMDT_Util_Rest::get('/api/' . $this->api . '.json', $params, $headers);

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
        $params['pdf-title'] = $this->_helper->translate('Invite Templates Management');

        $headers = array();
        $headers['columns-leach'] = 'name,subject,create_date';
        $headers['columns-desc'] = $this->_helper->translate('column-invite_template-name');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-invite_template-subject');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-invite_template-create_date');

        $response = IMDT_Util_Rest::get('/api/' . $this->api . '.json', $params, $headers);

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

        $this->view->jQuery()->addOnload('CKEDITOR.replace("body");');
    }

    public function viewAction() {
        $this->view->id = $this->_getParam('id', null);
        $this->view->jQuery()->addOnload('CKEDITOR.replace("body");');
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
            $response = IMDT_Util_Rest::get('/api/' . $this->api . '.json', $params);

            $arrTable = array();
            foreach ($response['collection'] as $curr) {
                $row = array();
                $row[] = '<input name="deleteRow[]" class="cboxSelectRow" type="checkbox" value="' . $curr[$this->pkey] . '" />';
                $row[] = $curr['name'];
                $row[] = $curr['subject'];
                $row[] = IMDT_Util_Date::filterDatetimeToCurrentLang($curr['create_date']);

                $actions = '<td nowrap="nowrap">';
                $actions .= '<a title="' . $this->_helper->translate('View') . '" data-toggle="tooltip" class="btn btn-mini" href="/ui/' . $this->_request->getControllerName() . '/view/id/' . $curr[$this->pkey] . '" data-original-title="' . $this->_helper->translate('Edit') . '"><i class="icon-eye-open"></i></a>';
                if ($curr['_editable'] == '1') {
                    $actions .= '<a title="' . $this->_helper->translate('Edit') . '" data-toggle="tooltip" class="btn btn-mini" href="/ui/' . $this->_request->getControllerName() . '/edit/id/' . $curr[$this->pkey] . '" data-original-title="' . $this->_helper->translate('Edit') . '"><i class="icon-pencil"></i></a>';
                } else {
                    $actions .= '<a title="' . $this->_helper->translate('Edit') . '" data-toggle="tooltip" class="btn btn-mini" disabled="disabled" data-original-title="' . $this->_helper->translate('Edit') . '"><i class="icon-pencil"></i></a>';
                }

                if ($curr['_removable'] == '1') {
                    $actions .= '<a title="' . $this->_helper->translate('Delete') . '" data-toggle="tooltip" class="btn btn-mini btn-delete" href="" data-original-title="Excluir" onclick="deleteRow(\'#records\',\'row_' . $curr[$this->pkey] . '\',\'/ui/' . $this->_request->getControllerName() . '/delete/id/' . $curr[$this->pkey] . '\'); return false;"><i class="icon-trash"></i></a>';
                } else {
                    $actions .= '<a title="' . $this->_helper->translate('Delete') . '" data-toggle="tooltip" class="btn btn-mini btn-delete" disabled="disabled" data-original-title="Excluir"><i class="icon-trash"></i></a>';
                }

                $actions .= '</td>';
                $row[] = $actions;
                $row['DT_RowId'] = 'row_' . $curr[$this->pkey];
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

    public function formContentAction() {
        $objResponse = new stdClass();

        try {
            $objResponse = new stdClass();
            $id = $this->_request->getParam('id');

            if ($id == 'new') {
                $objResponse->form = array();
            } elseif ($id) {
                $response = IMDT_Util_Rest::get('/api/' . $this->api . '/' . $id . '.json');

                $row = $response['row'];
                $arrForm['subject'] = $row['subject'];
                $arrForm['body'] = $row['body'];
                $arrForm['name'] = $row['name'];
                //$arrForm['_editable'] = $row['_editable'];
                //$arrForm['_removable'] = $row['_removable'];
                //$arrForm['create_date'] = IMDT_Util_Date::filterDatetimeToCurrentLang($row['create_date'], false);

                $objResponse->form = $arrForm;
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

    public function formPostAction() {
        $objResponse = new stdClass();

        try {
            $id = $this->_request->getParam('id', null);

            if (!$this->_request->isPost())
                throw new Exception($this->_helper->translate('Invalid Request'));
            if ($id == null)
                throw new Exception($this->_helper->translate('Invalid Id'));

            $subject = $this->_request->getPost('subject', null);
            $body = $this->_request->getPost('body', null);

            $tags = array(
                '__ROOM_START__',
                '__ROOM_END__',
                '__ROOM_NAME__',
                '__ROOM_URL__',
                '__ROOM_PRESENTER__'
            );

            foreach ($tags as $tag) {
                $body = str_replace($this->_helper->translate($tag), $tag, $body);
                $subject = str_replace($this->_helper->translate($tag), $tag, $subject);
            }

            $data = array();
            $data['subject'] = $subject;
            $data['body'] = $body;
            $data['name'] = $this->_request->getPost('name', null);

            if ($id == 'new') {
                $arrRestResponse = IMDT_Util_Rest::post('/api/' . $this->api, $data);
            } else {
                $arrRestResponse = IMDT_Util_Rest::put('/api/' . $this->api . '/' . $id, $data);
            }

            $objResponse->success = '1';
            $objResponse->msg = $this->_helper->translate('Data was saved successfully');
            $objResponse->redirect = $this->view->url(array('action' => 'index'));
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
            IMDT_Util_Rest::delete('/api/' . $this->api . '/' . $id . '.json');

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
