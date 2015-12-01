<?php

class IMDT_Util_Acl {

    private static $_instance;
    private $_aclRules;

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct() {
        $aclNs = new Zend_Session_Namespace('acl');
        $this->_aclRules = $aclNs->aclRules;
    }

    public function isAllowed($resource, $privilege) {
        return $this->_aclRules->isAllowed('loggedUser', strtoupper($resource), strtoupper($privilege));
    }

    public function getAclRules() {
        return $this->_aclRules;
    }

}
