<?php

class Ui_MaintenanceController extends IMDT_Controller_Abstract {

    protected $_maintenanceDbTable;

    public function init() {
        $this->api = 'maintenance';
        $this->pkey = 'maintenance_id';
    }

    public function indexAction() {
        $this->_helper->viewRenderer('index-enabled');

        try {
            //$params = IMDT_Util_Url::getThisParams($this->filters);
            $response = IMDT_Util_Rest::get('/api/' . $this->api . '/maintenance.json');

            if ($response['row']['active'] == '1') {
                $this->view->maintenance = $response['row'];
            } else {
                $this->_helper->viewRenderer('index-disable');
            }

            $this->view->jQuery()->addOnload('CKEDITOR.replace("description");');
            $this->view->jQuery()->addOnload('$(\'button[name="downloadBtn"]\').click(function(){$(\'input[name="download"]\').val(\'1\');});');
        } catch (IMDT_Controller_Exception_InvalidToken $e) {
            $this->_helper->redirector->gotoRoute(array('module' => 'login', 'controller' => 'auth', 'action' => 'logout'));
        } catch (Exception $e) {
            $this->addMessage(array('error' => $e->getMessage()));
        }
    }

    public function formPostAction() {
        $objResponse = new stdClass();

        try {
            if (!$this->_request->isPost())
                throw new Exception($this->_helper->translate('Invalid Request'));

            $data = array();
            $data['description'] = $this->_request->getPost('description', null);
            $data['active'] = $this->_request->getPost('active', null);

            IMDT_Util_Rest::put('/api/' . $this->api . '/' . 'maintenance.json', $data);

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

    public function downloadAction() {
        try {
            $response = IMDT_Util_Rest::get('/api/' . $this->api . '/maintenance.json');

            if ($response['row']['active'] == '1') {
                $maintenanceInfo = $response['row'];
            } else {
                throw new Exception(IMDT_Util_Translate::_('The maintenance mode is inactive.'));
            }

            $this->_disableViewAndLayout();

            $maintenanceMessage = BBBManager_Util_Maintenance::renderMessage($maintenanceInfo);

            header('Content-type: text/html; charset=UTF-8');
            header("Content-Length: " . strlen($maintenanceMessage) . "\n\n");
            header("Content-Disposition: attachment; filename=\"maintenance.html\"");
            echo $maintenanceMessage;
        } catch (Exception $e) {
            $this->addMessage(array('error' => $e->getMessage()));
            $this->_redirector->goToUrl('/ui/maintenance');
        }
    }

}
