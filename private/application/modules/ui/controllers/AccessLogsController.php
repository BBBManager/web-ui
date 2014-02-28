<?php

class Ui_AccessLogsController extends IMDT_Controller_Abstract {
    
    public function init() {
        $this->filters = array();
        $this->filters['user'] = array('column'=>'user','label'=>$this->_helper->translate('column-access_log-user'),'type'=>'combo-remote','url'=>array('controller'=>'users','action'=>'remote-search'));
        $this->filters['user_login'] = array('column'=>'user_login','label'=>$this->_helper->translate('column-access_log-user_login'),'type'=>'text');
        $this->filters['user_name'] = array('column'=>'user_name','label'=>$this->_helper->translate('column-access_log-user_name'),'type'=>'text');
        $this->filters['user_auth_mode'] = array('column'=>'user_auth_mode','label'=>$this->_helper->translate('column-access_log-user_auth_mode'),'type'=>'combo','source'=>'auth_mode');
        
        $this->filters['create_date'] = array('column'=>'create_date','label'=>$this->_helper->translate('column-access_log-create_date'),'type'=>'datetime');
        $this->filters['ip_address'] = array('column'=>'ip_address','label'=>$this->_helper->translate('column-access_log-ip_address'),'type'=>'ipaddress');
        //$this->filters['user_id'] = array('column'=>'user_id','label'=>$this->_helper->translate('column-user-name'),'type'=>'text');
        
        //$this->filters['user_id'] = array('column'=>'user_id','label'=>$this->_helper->translate('column-user-name'),'type'=>'combo','source'=>'user');
        $this->filters['description_hash'] = array('name'=>'description_hash','label'=>$this->_helper->translate('column-access_log_description-description'),'type'=>'combo','source'=>'access_log_description');
        $this->filters['detail'] = array('column'=>'detail','label'=>$this->_helper->translate('column-access_log-detail'),'type'=>'text');
        
        $this->api = 'access-logs';
        $this->pkey = 'access_log_id';
    }
    
    public function testeAction() {
        //$response = IMDT_Util_Rest::get('/api/access-log-descriptions.json');
        $response = IMDT_Util_Rest::get('/api/access-logs/185.json');
        
        //debug($response);
        
        $old = array();
        $new = array();
        
        if(!empty($response['row']['old'])) {
            $old = json_decode($response['row']['old'],true); 
        }
        
        if(!empty($response['row']['new'])) {
            $new = json_decode($response['row']['new'],true);
        }
        
        if(count($old) == 0 && count($new) > 0) {
            foreach($new as $col=>$val) {
                $old[$col] = '';
            }
        }
        
        $html = new Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/modules/ui/views/scripts/access-logs/');
        $html->old = $old;
        $html->new = $new;
        $content = $html->render('popup-info.phtml');
        
        echo $content;
        die;
    }
        
    public function indexAction() {
        $this->view->tableSource = array('module'=>$this->_request->getModuleName(),'controller'=>$this->_request->getControllerName(),'action'=>'table-content');
        $this->view->uriPage = array('module'=>$this->_request->getModuleName(),'controller'=>$this->_request->getControllerName(),'action'=>'index');
        $this->view->uriExport = array('module'=>$this->_request->getModuleName(),'controller'=>$this->_request->getControllerName(),'action'=>'export');
        $this->view->uriExportPdf = array('module'=>$this->_request->getModuleName(),'controller'=>$this->_request->getControllerName(),'action'=>'export-pdf');
        
        if(!$this->_request->getParam('create_date')) {
            $this->_request->setParam('create_date',IMDT_Util_Date::filterDatetimeToCurrentLang(date('Y-m-d H:i'), false));
            $this->_request->setParam('create_date_2',IMDT_Util_Date::filterDatetimeToCurrentLang(date('Y-m-d H:i', strtotime('+5 minutes')), false));
            $this->_request->setParam('create_date_c','b');
        }
        
        $this->view->parameters = IMDT_Util_Url::getThisParams($this->filters);
        
        $this->view->filters = $this->filters;
        
        $this->view->jQuery()->addOnload('$(\'#detailsModal\').on(\'hidden\', function() { $(\'#detailsModal .modal-body\').html(\'\'); });');
        
    }

    public function detailsAction() {
        $id = $this->_getParam('id', null);
        
        $response = IMDT_Util_Rest::get('/api/access-logs/'.$id.'.json');
        
        $old = array();
        $new = array();
        
        if(!empty($response['row']['old'])) {
            $old = json_decode($response['row']['old'],true); 
        }
        
        if(!empty($response['row']['new'])) {
            $new = json_decode($response['row']['new'],true);
        }
        
        if(count($old) == 0 && count($new) > 0) {
            foreach($new as $col=>$val) {
                $old[$col] = '';
            }
        }
        
        
        $html = new Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/modules/ui/views/scripts/access-logs/');
        $html->old = $old;
        $html->new = $new;
        $content = $html->render('popup-info.phtml');
        
        echo $content;
        die;
    }
    
    public function exportAction() {
        $this->_disableViewAndLayout();
        
        $params = IMDT_Util_Url::getThisParams($this->filters);
        $params['export'] = 'csv';
        
        $headers = array();
        $headers['columns-leach'] = 'user,description,detail,ip_address,create_date';
        // $headers['columns-desc'] = $this->_helper->translate('column-invite_template-id');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-invite_template-name');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-invite_template-subject');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-invite_template-body');
        
        $response = IMDT_Util_Rest::get('/api/'.$this->api.'.json',$params,$headers);
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        if($this->_request->getParam('utf8',0)) {
            header('Content-type: '.BBBManager_Config_Defines::$CONTENT_TYPE_CSV.'; charset=utf-8');
        } else {
            header('Content-type: '.BBBManager_Config_Defines::$CONTENT_TYPE_CSV);
        }
        header('Content-Disposition: attachment; filename="'.$this->_request->getControllerName().'.csv"');
        echo file_get_contents($response['url']);
        exit;
    }
    
    public function exportPdfAction() {
        $this->_disableViewAndLayout();
        
        $params = IMDT_Util_Url::getThisParams($this->filters);
        $params['export'] = 'pdf';
        
        $headers = array();
        $headers['columns-leach'] = 'user,description,detail,ip_address,create_date';
        $headers['columns-desc'] = $this->_helper->translate('column-user-name');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-access_log_description-description');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-access_log-detail');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-access_log-ip_address');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-access_log-create_date');
	   
    	$headers['columns-format'] = 'null';
    	$headers['columns-format'] .= ',' . 'null';
    	$headers['columns-format'] .= ',' . 'null';
    	$headers['columns-format'] .= ',' . 'null';
    	$headers['columns-format'] .= ',' . 'datetime';
        
        $response = IMDT_Util_Rest::get('/api/'.$this->api.'.json',$params,$headers);
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        if($this->_request->getParam('utf8',0)) {
            header('Content-type: '.BBBManager_Config_Defines::$CONTENT_TYPE_PDF.'; charset=utf-8');
        } else {
            header('Content-type: '.BBBManager_Config_Defines::$CONTENT_TYPE_PDF);
        }
        header('Content-Disposition: attachment; filename="'.$this->_request->getControllerName().'.pdf"');
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
            $response = IMDT_Util_Rest::get('/api/'.$this->api.'.json',$params);
            
            $arrTable = array();
            foreach($response['collection'] as $curr) {
                $row = array();
                $row[] = $curr['user'];
                $row[] = $curr['description'];
                $detail = $curr['detail'];
                if(strlen(trim($curr['old'])) > 0 || strlen(trim($curr['new'])) > 0) {
                    $detail .= ' <a title="' . $this->_helper->translate('Details') . '" data-toggle="modal" class="btn btn-mini" href="'.$this->view->url(array('action'=>'details','id'=>$curr[$this->pkey])).'" data-target="#detailsModal"><i class="icon-list-alt"></i></a>';
                }
                $row[] = $detail;
                $row[] = $curr['ip_address'];
                $row[] = IMDT_Util_Date::filterDatetimeToCurrentLang($curr['create_date'], true);
                
                $row['DT_RowId'] = 'row_'.$curr[$this->pkey];
                $arrTable[] = $row;
            }
            
            $objResponse->success = '1';
            $objResponse->msg = '';
            $objResponse->aaData = $arrTable;
        }catch(IMDT_Controller_Exception_InvalidToken $e1){
            $objResponse->success = '-1';
            $objResponse->aaData = array();
        }catch(Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
            $objResponse->aaData = array();
        }
        $this->_helper->json($objResponse);
    }

    
    public function advSearchFilterAction() {
        $objResponse = new stdClass();

        try {
            $arrSelect = array('user' => '', 'auth_mode'=>'', 'access_log_description'=>'');

            $id = $this->_request->getParam('id');
            
            /*
            $arrRestResponse = IMDT_Util_Rest::get('/api/users.json');
            $arr = $arrRestResponse['collection'];
            $strUsers = '';
            foreach ($arr as $obj) {
                $strUsers .= '<option value="'.$obj['user_id'].'">'.$obj['name'].'</option>';
            }
            $arrSelect['user'] = $strUsers;
            */
            
            $arrAuthMode = array();
            foreach(BBBManager_Config_Defines::getAuthMode() as $value=>$name) {
                $arrAuthMode[$name] = array('id'=>$value,'name'=>$name);
            }
            ksort($arrAuthMode);
            
            $strOptions = '';
            foreach ($arrAuthMode as $obj) {
                $strOptions .= '<option value="'.$obj['id'].'">'.$obj['name'].'</option>';
            }
            $arrSelect['auth_mode'] = $strOptions;
            
            
            $arrRestResponse = IMDT_Util_Rest::get('/api/access-log-descriptions.json');
            $arr = $arrRestResponse['collection'];
            $strUsers = '';
            foreach ($arr as $obj) {
                $hash = substr(md5($obj['controller'].'#'.$obj['action']), 0, 8);
                //$description = (strlen($obj['description']) > 0) ? $obj['description'] : $obj['controller'].' - '.$obj['action'];
                if((strlen($obj['description']) == 0)) continue;
                $strUsers .= '<option value="'.$hash.'">'.$obj['description'].'</option>';
            }
            $arrSelect['access_log_description'] = $strUsers;
            
            $objResponse->form = array();
            $objResponse->select = $arrSelect;
            $objResponse->success = '1';
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

