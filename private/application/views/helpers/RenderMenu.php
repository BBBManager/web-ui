<?php
class BBBManager_View_Helper_RenderMenu extends Zend_View_Helper_Abstract{
    public function renderMenu($currentUrl){
	$aclNs = new Zend_Session_Namespace('acl');
	$aclAllowedMenuItens = $aclNs->allowedMenuItens;
	
	$allowedMenuItens = array();
	
	foreach($aclAllowedMenuItens as $resourceId){
	    $allowedMenuItens[$resourceId] = true;
	}
	
	return Zend_Layout::getMvcInstance()->getView()->partial('partials/menu.phtml', array('allowedMenuItens' => $allowedMenuItens, 'currentUrl' => $currentUrl));
    }
}