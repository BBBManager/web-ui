<?php

class Ui_CategoriesController extends IMDT_Controller_Abstract {

    public function init() {
        $this->view->headScript()->appendFile($this->view->baseUrl('/resources/js/categorias.js'));
        $this->api = 'categories';
    }

    public function indexAction() {
        $objResponse = new stdClass();
        try {
            $response = IMDT_Util_Rest::get('/api/categories');

            if ($response['success'] != '1') {
                throw new Exception($response['msg']);
            }

            $this->view->rCategories = array();
            $this->view->rCategoriesName = array();

            foreach ($response['collection'] as $category) {

                if (isset($category['hierarchy'])) {
                    $this->view->rCategories[] = array(
                        'id' => $category['meeting_room_category_id'],
                        'name' => $category['name'],
                        'parent_id' => $category['parent_id'],
                        'hierarchy' => $category['hierarchy'],
                        'path' => $category['path']
                    );
                } else {
                    $this->view->rCategories[] = array(
                        'id' => $category['meeting_room_category_id'],
                        'name' => $category['name'],
                        'parent_id' => $category['parent_id']
                    );
                }

                $this->view->rCategoriesName[] = $category['name'];
            }
        } catch (Exception $ex) {
            $objResponse->success = '0';
            $objResponse->msg = $ex->getMessage();
        }
        $this->view->uriExport = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'export');
        $this->view->uriExportPdf = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'export-pdf');
    }

    public function formPostAction() {
        $this->_disableViewAndLayout();

        $objResponse = new stdClass();

        try {
            $id = $this->_request->getParam('id', null);

            if (!$this->_request->isPost())
                throw new Exception($this->_helper->translate('Invalid Request'));

            $data = array();
            $data['name'] = $this->_request->getPost('name', null);
            $data['parent_id'] = $this->_request->getPost('parent_id', null);

            if ($id == null) {
                $arrRestResponse = IMDT_Util_Rest::post('/api/categories', $data);
            } else {
                $arrRestResponse = IMDT_Util_Rest::put('/api/categories/' . $id, $data);
            }

            $objResponse->success = '1';
            $objResponse->msg = $arrRestResponse['msg'];
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
            $id = $this->_request->getParam('id', null);

            if ($id == null || (trim($id)) == '') {
                throw new Exception(IMDT_Util_Translate::_('Invalid category') . '.');
            }

            $response = IMDT_Util_Rest::delete('/api/categories/' . $id);

            $objResponse->success = '1';
            $objResponse->msg = $response['msg'];
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

    public function exportAction() {
        $this->_disableViewAndLayout();

        $params = array();
        $params['export'] = 'csv';

        $headers = array();
        $headers['columns-leach'] = 'meeting_room_category_id,name,parent_id';

        $response = IMDT_Util_Rest::get('/api/categories.json', $params, $headers);

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

        $params = array();
        $params['export'] = 'pdf';
        $params['pdf-title'] = $this->_helper->translate('Categories Management');

        $headers = array();
        $headers['columns-leach'] = 'meeting_room_category_id,name,parent_id';
        $headers['columns-desc'] = $this->_helper->translate('column-category-id');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-category-name');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-category-parent_id');

        $headers['columns-format'] = 'null';
        $headers['columns-format'] .= ',' . 'null';
        $headers['columns-format'] .= ',' . 'null';

        $response = IMDT_Util_Rest::get('/api/categories.json', $params, $headers);

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

}
