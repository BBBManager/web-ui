<?php
class BBBManager_Plugin_RedirectToReferer extends Zend_Controller_Plugin_Abstract {
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
		
	if(Zend_Auth::getInstance()->hasIdentity() == false){
	    return;
	}
	
	$redirectToRefererNs = new Zend_Session_Namespace('redirectToReferer');
	
	if($redirectToRefererNs->uri != null){
	    $this->getResponse()->setRedirect($redirectToRefererNs->uri);
	    $redirectToRefererNs->uri = null;
	}
    }
}