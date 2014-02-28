<?php
class BBBManager_Plugin_SystemUser extends Zend_Controller_Plugin_Abstract {
    public function preDispatch(\Zend_Controller_Request_Abstract $request) {
	if(IMDT_Util_Auth::getInstance()->get('systemUser')){
	    $goingToIndex = (($request->getModuleName() == 'ui') && ($request->getControllerName() == 'index') && ($request->getActionName() == 'index'));
	    
	    if($goingToIndex){
		$request->setModuleName('ui')
			->setControllerName('my-rooms')
			->setActionName('index');
	    }
	}
    }
}
