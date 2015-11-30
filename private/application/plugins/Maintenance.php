<?php

class BBBManager_Plugin_Maintenance extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $maintenanceCheck = IMDT_Util_Rest::get('/api/maintenance/maintenance.json');

        $maintenanceAuthorizationHash = $request->get('mah', null);
        $authData = Zend_Auth::getInstance()->getStorage()->read();

        $maintenanceSafeAccess = true;

        if (isset($maintenanceCheck['row']) && isset($maintenanceCheck['row']['active']) && $maintenanceCheck['row']['active'] == '1') {
            /* If the session is marked as maintenance safe access, and authorization hash is passed, check if it is valid */
            if (isset($authData['maintenanceAccessAuthorized'])) {
                if ($maintenanceAuthorizationHash != null && $maintenanceAuthorizationHash != $maintenanceCheck['row']['hash']) {
                    unset($authData['maintenanceAccessAuthorized']);
                    Zend_Auth::getInstance()->getStorage()->write($authData);
                    $maintenanceSafeAccess = false;
                }
            } elseif ($maintenanceAuthorizationHash != $maintenanceCheck['row']['hash']) {
                $maintenanceSafeAccess = false;
            } elseif ($maintenanceAuthorizationHash == $maintenanceCheck['row']['hash']) {
                $authData['maintenanceAccessAuthorized'] = true;
                Zend_Auth::getInstance()->getStorage()->write($authData);
            }

            if ($maintenanceSafeAccess === false) {
                Zend_Layout::getMvcInstance()->disableLayout();
                $maintenanceMessage = BBBManager_Util_Maintenance::renderMessage($maintenanceCheck['row']);
                header('Content-type: text/html; charset=UTF-8');
                header("Content-Length: " . strlen($maintenanceMessage) . "\n\n");
                echo $maintenanceMessage;
                die;
            }
        }
    }

}
