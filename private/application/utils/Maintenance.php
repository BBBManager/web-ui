<?php
class BBBManager_Util_Maintenance{
    public static function renderMessage($maintenanceInfo) {
        $maintenanceTemplateFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'maintenance.html';

        $maintenanceHtml = file_get_contents($maintenanceTemplateFile);
        $replacedMaintenanceHtml = IMDT_Util_String::replaceTags($maintenanceHtml, array('title' => Zend_Layout::getMvcInstance()->getView()->headTitle(), 'message' => $maintenanceInfo['description'], 'baseUrl' => IMDT_Util_Config::getInstance()->get('web_base_url')));
        
        return IMDT_Util_String::toCleanHtml($replacedMaintenanceHtml);
    }
}