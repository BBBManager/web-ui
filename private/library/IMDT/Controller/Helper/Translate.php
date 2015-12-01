<?
class IMDT_Controller_Helper_Translate extends Zend_Controller_Action_Helper_Abstract {
public function direct($string) {
return Zend_Registry::get('Zend_Translate')->_($string);
}
}