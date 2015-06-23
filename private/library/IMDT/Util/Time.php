<?php

class IMDT_Util_Time{

    public static function hhmmssmilToMilliseconds($hhmmssmil) {
        preg_match_all('/([0-9]+):([0-9]+):([0-9]+).([0-9]+)/', $hhmmssmil, $pieces);
        
        $hoursInMilliseconds = (isset($pieces[1][0]) ? $pieces[1][0] * 60 * 60 * 1000 : 0);
        $minutesInMilliseconds = (isset($pieces[2][0]) ? $pieces[2][0] * 60 * 1000 : 0);
        $secondsInMilliseconds = (isset($pieces[3][0]) ? $pieces[3][0] * 1000 : 0);
        $milliseconds = (isset($pieces[4][0]) ? $pieces[4][0] : 0);
        
        return $hoursInMilliseconds + $minutesInMilliseconds + $secondsInMilliseconds + $milliseconds;
    }
    
    public static function millisecondsTohhmmssmil($milliseconds){
        $seconds = floor($milliseconds / 1000);
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $milliseconds = $milliseconds % 1000;
        $seconds = $seconds % 60;
        $minutes = $minutes % 60;

        $format = '%02u:%02u:%02u.%03u';
        return sprintf($format, $hours, $minutes, $seconds, $milliseconds);
    }
}
