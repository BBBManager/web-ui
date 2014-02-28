<?php
class IMDT_Util_Auth{
    private static $_instance;
    private $_authData;
    
    public static function getInstance(){
        if(self::$_instance == null){
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    public function __construct(){
        $this->_authData = Zend_Auth::getInstance()->getStorage()->read();
    }
    
    public function get($key){
        return (isset($this->_authData[$key]) ? $this->_authData[$key] : null);
    }
    
    public function getData(){
	return $this->_authData;
    }
}