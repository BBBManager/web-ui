<?php
class BBBManager_View_Helper_FormFilters extends Zend_View_Helper_Abstract{
    public function formFilters($filters,$parameters = array()){
        
        
        /*
        $aclNs = new Zend_Session_Namespace('acl');
        $aclAllowedMenuItens = $aclNs->allowedMenuItens;
        
        $allowedMenuItens = array();
        
        foreach($aclAllowedMenuItens as $resourceId){
            $allowedMenuItens[$resourceId] = true;
        }
        */
        
        if(!isset($parameters['musthave']) || strlen(trim($parameters['musthave'])) == 0) {
            $parameters['musthave'] = 'all';
        }
        
        /*
        array_walk($filters,function(&$curr) {
            if(isset($parameters['name'])) {
                $curr['value'] = $parameters['name'];
            } elseif(isset($curr['default'])) {
                $curr['value'] = $curr['default'];
            } else {
                $curr['value'] = '';
            }
        });
        */
        
        if(!isset($parameters['q']) || count($parameters['q']) == 0) {
            $parameters['q'] = array();
            $parameters['q'][] = array('n'=>current(array_keys($filters)),'v'=>'','c'=>'');
        }
        
        return Zend_Layout::getMvcInstance()->getView()->partial('partials/form_filters.phtml', array('filters'=>$filters,'parameters'=>$parameters));
    }
}