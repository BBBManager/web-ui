<?php
class IMDT_Util_Xml {

    public static function xmlToArrayAntigo($xml, $recursive = false) {
        if (!$recursive) {
            $array = simplexml_load_string($xml);
        } else {
            $array = $xml;
        }

        $newArray = array();
        $array = $array;

        foreach ($array as $key => $value) {
            $value = (array) $value;

            if (isset($value[0])) {
                $newArray[$key] = trim($value[0]);
            } else {
                $arr = self::xmlToArrayAntigo($value, true);
				if(count($arr) == 0) {
					$newArray[$key] = null;
				} else {
					$newArray[$key][] = $arr;
				}
				
            }
        }
        return $newArray;
    }
	
	public static function xmlToArray($xml) {
		$arr = array();
		
		if(!$xml instanceof SimpleXMLElement) {
			$xml = simplexml_load_string($xml);
		}
	  	
		foreach ($xml->children() as $element) {
		    $tag = $element->getName();
		    $e = get_object_vars($element);
			if (!empty($e)) {
				$arrChild = $element instanceof SimpleXMLElement ? self::xmlToArray($element) : $e;
				if(empty($arrChild)) {
					$arr[$tag] = null;
				} elseif(count($arrChild) == 1) {
					$arr[$tag] = $arrChild;
				} else {
					$arr[$tag][] = $arrChild;
				}
			} else {
				$arr[$tag] = trim($element);
			}
		}
		
		return $arr;
	}
	
}