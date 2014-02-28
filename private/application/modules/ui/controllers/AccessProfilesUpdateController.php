<?php

class Ui_AccessProfilesUpdateController extends IMDT_Controller_Abstract{
    public function init(){
        session_write_close();
    }
    
    public function indexAction(){
        $accessProfileId = IMDT_Util_Auth::getInstance()->get('user_access_profile');
        
        if($accessProfileId == BBBManager_Config_Defines::$SYSTEM_ADMINISTRATOR_PROFILE){
            try{
                IMDT_Util_Rest::put('/api/access-profiles-update',array('r'=>$this->_getParam('r', '')),null,3000);
                $this->_redirector->gotoUrl('/');
            } catch (Exception $ex) {
                die($ex->getMessage());
            }
        }
    }
    
    public function checkAction(){
        $this->_disableViewAndLayout();
        try{
            $response = IMDT_Util_Rest::get('/api/access-profiles-update/1', array('p'=>$this->_getParam('p', '0'),'r'=>$this->_getParam('r', '')));    
        } catch (Exception $ex) {
            $response = array(
                'success'   => '0',
                'msg'       => $ex->getMessage()
            );
        }
        
        $this->_response->setBody(Zend_Json::encode($response));
    }
}

