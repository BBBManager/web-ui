<?php

class BBBManager_Plugin_RedirectToReferer extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $authData = Zend_Auth::getInstance()->getStorage()->read();

        if (!isset($authData['id'])) {
            return;
        }

        $redirectToRefererNs = new Zend_Session_Namespace('redirectToReferer');

        
        if ($redirectToRefererNs->uri != null && strpos($redirectToRefererNs->uri, '/resources/') === FALSE) {
            $this->getResponse()->setRedirect($redirectToRefererNs->uri);
            $redirectToRefererNs->uri = null;
        }
    }

}
