<?php
class IMDT_Util_Numeric {
	
	public function filter($value) {
		
		if(!empty($value) && !is_null($value)) {
		    if(strpos($value,',')) {
		        $value = str_replace(array('.',','),array('','.'),$value);
		    }
			
			if(is_numeric($value)) {
				$value = $value + 0;
				return $value;
			}
		} elseif($value == '0') {
		    return '0';
		}
		
		return null;
	}
    
    public function filterUs($value) {
        
        if(!empty($value) && !is_null($value)) {
            if(is_numeric($value)) {
                $value = $value + 0;
                return $value;
            }
        }
        
        return null;
    }
	
	
	
	function formatMinPrecision($number, $min=2) {
	    $number = (string) $number+0;
	    $parts = explode('.', $number);
		
	    $precision = (isset($parts[1])) ? strlen($parts[1]) : 0;
	    if ($precision < $min) $precision = $min;
	    return number_format($number, $precision, ',', '.');
	}
}