<?php

class IMDT_Util_Translate {

    public static $lang = null;

    public static function _($key) {
        if (is_null(self::$lang)) {
            self::$lang = Zend_Registry::get('Zend_Translate');
        }

        return self::$lang->translate($key);
    }

    public static function isTranslated($key) {
        if (is_null(self::$lang)) {
            self::$lang = Zend_Registry::get('Zend_Translate');
        }

        return self::$lang->isTranslated($key);
    }

}
