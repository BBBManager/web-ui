<?php

class BBBManager_Util_Skinning{

    private static $_instance;
    private $_config;

    public static function getInstance() {
	if (self::$_instance == null) {
	    self::$_instance = new BBBManager_Util_Skinning();
	}

	return self::$_instance;
    }

    public function __construct() {
	$skinningConfig = new Zend_Config_Ini(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'skinning.ini');
	$this->_config = ($skinningConfig instanceof Zend_Config ? $skinningConfig->toArray() : array());
    }

    public function get($key) {
	return (isset($this->_config[$key]) ? $this->_config[$key] : null);
    }

}