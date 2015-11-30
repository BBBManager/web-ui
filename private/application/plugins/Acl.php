<?php

class BBBManager_Plugin_Acl extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $authData = Zend_Auth::getInstance()->getStorage()->read();

        if (!isset($authData['id'])) {
            return;
        }

        $aclNs = new Zend_Session_Namespace('acl');

        if ($aclNs->aclRules == null) {
            $securityResponse = IMDT_Util_Rest::get('api/security');

            $aclNs->allowedMenuItens = $securityResponse['allowedMenuItens'];
            $aclNs->aclRules = unserialize(base64_decode($securityResponse['acl']));
        }
    }

}
