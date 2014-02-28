<?php

class IMDT_Util_Date {

    public static function filterStringToDate($date) {
        $currentFormat = IMDT_Util_Translate::_('dateFormat-php');

        if (strlen($date) == 0) {
            return false;
        } elseif ($date[2] == '/' && preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $date)) {
            return Datetime::createFromFormat($currentFormat, $date);
        } elseif ($date[2] == '-' && preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $date)) {
            return Datetime::createFromFormat($currentFormat, str_replace('-', '/', $date));
        } elseif ($date[4] == '-' && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date)) {
            return Datetime::createFromFormat('Y-m-d', $date);
        } elseif ($date[4] == '-' && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date)) {
            return Datetime::createFromFormat('Y-m-d', $date);
        } else {
            return false;
        }
    }

    public static function filterStringToDatetime($datetime) {
        $splited = split(' ', $datetime);
        if (count($splited) == 2) {
            list($date, $hms) = $splited;
        } else {
            $date = $splited[0];
            $hms = '';
        }

        $dateObject = self::filterStringToDate($date);
        if (!$dateObject)
            return false;

        if (strlen($hms) == 0) {
            $dateObject->setTime(0, 0, 0);
        } elseif (preg_match('/^[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/', $hms)) {
            list($hour, $min, $sec) = split(':', $hms);
            $hour = (strlen($hour) == 2) ? $hour : '0' . $hour;
            $min = (strlen($min) == 2) ? $min : '0' . $min;
            $sec = (strlen($sec) == 2) ? $sec : '0' . $sec;

            $dateObject->setTime($hour, $min, $sec);
        } elseif (preg_match('/^[0-9]{1,2}:[0-9]{1,2}$/', $hms)) {
            list($hour, $min) = split(':', $hms);
            $hour = (strlen($hour) == 2) ? $hour : '0' . $hour;
            $min = (strlen($min) == 2) ? $min : '0' . $min;

            $dateObject->setTime($hour, $min, 0);
        }

        return $dateObject;
    }

    public static function filterDateToApi($date) {
        $outputFormat = 'Y-m-d';

        $dateObject = self::filterStringToDate($date);
        return $dateObject ? $dateObject->format($outputFormat) : '';
    }

    public static function filterDateToMysql($date) {
        if ($dateObject = self::filterStringToDate($date)) {
            return new Zend_Db_Expr('STR_TO_DATE(\'' . $dateObject->format('Y-m-d') . '\', \'%Y-%m-%d\')');
        } else {
            return '';
        }
    }

    public static function filterDateToCurrentLang($date) {
        $outputFormat = IMDT_Util_Translate::_('dateFormat-php');

        $dateObject = self::filterStringToDate($date);
        return $dateObject ? $dateObject->format($outputFormat) : '';
    }

    public static function filterDatetimeToApi($datetime) {
        $outputFormat = 'Y-m-d H:i:s';

        $dateObject = self::filterStringToDatetime($datetime);
        return $dateObject ? $dateObject->format($outputFormat) : '';
    }

    public static function filterDatetimeToMysql($datetime) {
        if ($dateObject = self::filterStringToDatetime($datetime)) {
            return new Zend_Db_Expr('STR_TO_DATE(\'' . $dateObject->format('Y-m-d H:i:s') . '\', \'%Y-%m-%d %T\')');
        } else {
            return '';
        }
    }

    public static function filterDatetimeToCurrentLang($datetime, $seconds = true) {
        if ($seconds) {
            $outputFormat = IMDT_Util_Translate::_('dateFormat-php') . ' H:i:s';
        } else {
            $outputFormat = IMDT_Util_Translate::_('dateFormat-php') . ' H:i';
        }

        $dateObject = self::filterStringToDatetime($datetime);
        return $dateObject ? $dateObject->format($outputFormat) : '';
    }

    public static function dateInformal($tsDate){
        $inputDate = date('d/m/Y', $tsDate);
        
        if ($inputDate == date("d/m/Y")) {
            return IMDT_Util_Translate::_('Today');
        } elseif ($inputDate == date("d/m/Y", time() + 86400)) {
            return IMDT_Util_Translate::_('Tomorrow');
        } elseif ($inputDate == date("d/m/Y", time() - 86400)) {
            return IMDT_Util_Translate::_('Yesterday');
        } else {
            return self::weekDayName(date("w", $tsDate)) . " " . date("d/m", $tsDate); // Ex. Segunda-feira 18/11
        }
    }
    
    public static function weekDaysName($shortName = false) {
	$rDdays = array();
        
	if($shortName == true){
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameSunday');
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameMondayShort');
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameTuesdayShort');
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameWednesdayShort');
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameThursdayShort');
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameFridayShort');
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameSaturday');
	} else {
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameSunday');
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameMonday');
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameTuesday');
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameWednesday');
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameThursday');
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameFriday');
            $rDdays[] = IMDT_Util_Translate::_('weekdayNameSaturday');
	}
        
	return $rDdays;
    }
    
    public static function weekDayName($weekDayNumber,$shortName = false) {
	$rDays = self::weekDaysName($shortName);
	return $rDays[$weekDayNumber];
    }
}
