<?php
class BBBManager_Plugin_RecordingVisualization extends Zend_Controller_Plugin_Abstract {
    public function preDispatch(\Zend_Controller_Request_Abstract $request) {
        $recordingVisualization = (($request->getModuleName() == 'ui') && ($request->getControllerName() == 'recordings') && ($request->getActionName() == 'view'));

        if($recordingVisualization){
            Zend_Registry::set('recordingVisualization', true);
        }
    }
}
