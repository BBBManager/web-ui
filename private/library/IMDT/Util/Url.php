<?php
class IMDT_Util_Url{
    public static function baseUrl(){
        return ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] != '')) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/';
    }
	
	public static function arrayToUrl($arr) {
		$str = '';
		if(count($arr) > 0) {
			foreach($arr as $key=>$val) {
				$str .= empty($str) ? '?' : '&';
				$str .= $key.'='.$val;
			}
		}
		
		$str = str_replace(' ','+',$str);
		
		return $str;
	}
	
	
	public static function getThisParams($arrFiltersReceived) {
	    $arrFilters = array();
	    foreach($arrFiltersReceived as $filterName=>$currFilter) {
	        if($currFilter['type'] == 'optgroup') {
	            foreach($currFilter['options'] as $childName=>$childFilter) {
	                $arrFilters[$childName] = $childFilter;
	            }
	        } else {
	            $arrFilters[$filterName] = $currFilter;
	        }
	    }
        
        
		$arrParams = array();
		
		$controller = Zend_Controller_Front::getInstance();
		$request = $controller->getRequest();
        
        $mustHave = $request->getParam('musthave','all');
        $arrParams['musthave'] = $mustHave;
        $arrParams['q'] = array();
        
        $query = $request->getParam('q',array());
        
        if(is_array($query) && count($query) > 0) {
            foreach($query as $curr) {
                $name = $curr['n'];
                $value = $curr['v'];
                $condition = $curr['c'];
                $until = isset($curr['u']) ? $curr['u'] : '';
                
                if(is_array($value)) {
                    $value = implode(',',$value);
                }
                
                if(!in_array($condition,array('empty','nempty')) && strlen(trim($value)) == 0) continue;
                if(!in_array($name,array_keys($arrFilters))) continue;
                
                $type = $arrFilters[$name]['type'];
                
                $newParam = array();
                $newParam['n'] = $name;
                $newParam['c'] = $condition;
                $newParam['v'] = $value;
                
                if($type == 'date') {
                    if($strDate = IMDT_Util_Date::filterDateToApi($value)) {
                        $newParam['v'] = $strDate;
                    }
                    
                    if($condition == 'b' && $strDate2 = IMDT_Util_Date::filterDateToApi($until)) {
                        $newParam['u'] = $strDate2;
                    }
                } elseif($type == 'datetime') {
                    if($strDate = IMDT_Util_Date::filterDatetimeToApi($value)) {
                        $newParam['v'] = $strDate;
                    }
                    
                    if($condition == 'b' && $strDate2 = IMDT_Util_Date::filterDatetimeToApi($until)) {
                        $newParam['u'] = $strDate2;
                    }
                }
                
                $arrParams['q'][] = $newParam;
            }
        }

        //Modo antigo (manter)
        foreach($arrFilters as $column=>$curr) {
            $value = $request->getParam($column,'');
            $condition = $request->getParam($column.'_c','e');
            
            if(is_array($value)) {
                $value = implode(',',$value);
            }
            
            if(strlen(trim($value)) == 0) continue;
            
            if(in_array($curr['type'],array('text','integer','ipaddress','combo','boolean'))) {
                $arrParams[$column] = $value;
                $arrParams[$column.'_c'] = $condition;
            } elseif($curr['type'] == 'date') {
                if($strDate = IMDT_Util_Date::filterDateToApi($value)) {
                    $arrParams[$column] = $strDate;
                    $arrParams[$column.'_c'] = $condition;
                }
                
                if($condition == 'b' && $strDate2 = IMDT_Util_Date::filterDateToApi($request->getParam($column.'_2',''))) {
                    $arrParams[$column.'_2'] = $strDate2;
                }
            } elseif($curr['type'] == 'datetime') {
                if($strDate = IMDT_Util_Date::filterDatetimeToApi($value)) {
                    $arrParams[$column] = $strDate;
                    $arrParams[$column.'_c'] = $condition;
                }
                
                if($condition == 'b' && $strDate2 = IMDT_Util_Date::filterDatetimeToApi($request->getParam($column.'_2',''))) {
                    $arrParams[$column.'_2'] = $strDate2;
                }
            }
        }
		
		return $arrParams;
	}

	

}