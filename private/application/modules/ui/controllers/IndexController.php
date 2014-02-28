<?php

class Ui_IndexController extends IMDT_Controller_Abstract{

    /*public function init(){
		parent::init();
	}*/

    public function indexAction(){
        $this->view->mustUpdateUsersAccessProfile = false;
        
        $accessProfileId = IMDT_Util_Auth::getInstance()->get('user_access_profile');
        
        if($accessProfileId == BBBManager_Config_Defines::$SYSTEM_ADMINISTRATOR_PROFILE){
            try{
                $mustUpdateResponse = IMDT_Util_Rest::get('/api/access-profiles-update');
                $this->view->mustUpdateUsersAccessProfile = (isset($mustUpdateResponse['mustValidate']) ? $mustUpdateResponse['mustValidate'] : '0');
            } catch (Exception $ex) {
                die($ex->getMessage());
            }
        }
    }
}

