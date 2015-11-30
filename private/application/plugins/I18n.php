<?php

class BBBManager_Plugin_I18n extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(\Zend_Controller_Request_Abstract $request) {
        try {
            $systemLocale = new Zend_Locale(Zend_Locale::BROWSER);
        } catch (Exception $e) {
            $systemLocale = new Zend_Locale('en_US');
        }
        $translate = Zend_Registry::get('Zend_Translate');

        if (!$translate->isAvailable($systemLocale->getLanguage())) {
            $systemLocale = new Zend_Locale('en_US');
        }

        //$systemLocale = new Zend_Locale('en_US');

        $systemLocale->setLocale($systemLocale);
        $translate->setLocale($systemLocale);

        Zend_Registry::set('Zend_Locale', $systemLocale);
        Zend_Registry::set('Zend_Translate', $translate);
    }

}
