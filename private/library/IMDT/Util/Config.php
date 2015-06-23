<?php
class IMDT_Util_Config{
    private static $_instance;
    private $_appConfig;
    
    public static function getInstance(){
	if(self::$_instance == null){
	    self::$_instance = new IMDT_Util_Config();
	}
	
	return self::$_instance;
    }
    
    public function __construct(){
	$appConfig = new Zend_Config_Ini(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'application.ini', APPLICATION_ENV);
	$this->_appConfig = ($appConfig instanceof Zend_Config ? $appConfig->toArray() : array());
    }
    
    public function get($key){
	return (isset($this->_appConfig[$key]) ? $this->_appConfig[$key] : null);
    }
}