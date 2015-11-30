<?php

class BBBManager_View_Helper_FormFilters extends Zend_View_Helper_Abstract {

    public function formFilters($filters, $parameters = array(), $mainFilters = array()) {

        /*
          $aclNs = new Zend_Session_Namespace('acl');
          $aclAllowedMenuItens = $aclNs->allowedMenuItens;

          $allowedMenuItens = array();

          foreach($aclAllowedMenuItens as $resourceId){
          $allowedMenuItens[$resourceId] = true;
          }
         */

        if (!isset($parameters['musthave']) || strlen(trim($parameters['musthave'])) == 0) {
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

        $simpleFilters = array();
        $mainFilters = array();

        foreach ($filters as $keyCurrFilter => $valueCurrFilter) {
            if (isset($valueCurrFilter['main']) and $valueCurrFilter['main'] === true) {
                $mainFilters[$keyCurrFilter] = $valueCurrFilter;
            } else {
                $simpleFilters[$keyCurrFilter] = $valueCurrFilter;
            }
        }

        $totalSimpleParams = 0;
        if (!isset($parameters['q'])) {
            $parameters['q'] = array();
        } elseif (count($parameters['q']) > 0) {
            foreach ($parameters['q'] as $curr) {
                if (in_array($curr['n'], array_keys($simpleFilters))) {
                    $totalSimpleParams++;
                }
            }
        }

        if ($totalSimpleParams == 0) {
            $parameters['q'][] = array('n' => current(array_keys($simpleFilters)), 'v' => '', 'c' => '');
        }

        return Zend_Layout::getMvcInstance()->getView()->partial('partials/form_filters.phtml', array('filters' => $filters, 'parameters' => $parameters));
    }

}
