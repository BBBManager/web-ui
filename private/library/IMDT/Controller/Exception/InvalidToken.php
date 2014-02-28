<?
class IMDT_Controller_Exception_InvalidToken extends Zend_Controller_Action_Exception {
    public function __construct($msg = '', $code = 0, \Exception $previous = null) {
	parent::__construct($msg, $code, $previous);
    }
}