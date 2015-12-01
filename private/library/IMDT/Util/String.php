<?php

class IMDT_Util_String {

    public static function underscoreToCamelCase($str) {
        return lcfirst(implode('', array_map('ucfirst', array_map('strtolower', explode('_', $str)))));
    }

    public static function reverse($str, $encoding = null) {
        if ($encoding === null) {
            $encoding = mb_detect_encoding($str);
        }

        $length = mb_strlen($str, $encoding);
        $reversed = '';
        while ($length-- > 0) {
            $reversed .= mb_substr($str, $length, 1, $encoding);
        }

        return $reversed;
    }

    public static function replaceTags($string, $rTags) {
        return preg_replace_callback(
                '/\\{\\{([^{}]+)\}\\}/', function($matches) use ($rTags) {
            $key = $matches[1];
            return array_key_exists($key, $rTags) ? $rTags[$key] : '';
        }
                , $string
        );
    }

    public static function toCleanHtml($string) {
        return preg_replace('/(?<=>)\s+|\s+(?=<)/', '', $string);
    }

    public static function camelize($string) {
        //return texto.decode('utf-8').title().replace(' Em ', ' em ').replace(' Da ', ' da ').replace(' De ', ' de ').replace(' Do ', ' do ').replace(' Das ', ' das ').replace(' Dos ', ' dos ').replace(' E ', ' e ').replace(' Para ', ' para ').replace(' Ii', ' II').encode('utf-8')

        /* $inputEncoding = mb_detect_encoding($string);
          $str = mb_convert_case($string, MB_CASE_TITLE); */

        $str = ucwords(strtolower($string));
        $str = implode('\'', array_map('ucwords', explode('\'', $str)));

        $str = str_replace(' Em ', ' em ', $str);
        $str = str_replace(' Da ', ' da ', $str);
        $str = str_replace(' De ', ' de ', $str);
        $str = str_replace(' Do ', ' do ', $str);
        $str = str_replace(' Das ', ' das ', $str);
        $str = str_replace(' Dos ', ' dos ', $str);
        $str = str_replace(' E ', ' e ', $str);
        $str = str_replace(' Para ', ' para ', $str);
        $str = str_replace(' Ii ', ' II ', $str);

        return $str;
    }

}

/*

class CurlyVariables {

  private static $_matchable = array();
  private static $_caseInsensitive = true;

  private static function var_match($matches)
  {
    $match = $matches[1];

    if (self::$_caseInsensitive) {
      $match = strtolower($match);
    }

    if (isset(self::$_matchable[$match]) && !is_array(self::$_matchable[$match])) {
      return self::$_matchable[$match];
    }

    return '';
  }

  public static function Replace($needles, $haystack, $caseInsensitive = true) {
    if (is_array($needles)) {
      self::$_matchable = $needles;
    }

    if ($caseInsensitive) {
      self::$_caseInsensitive = true;
      self::$_matchable = array_change_key_case(self::$_matchable);
    }
    else {
      self::$_caseInsensitive = false;
    }

    $out = preg_replace_callback("/{(\w+)}/", array(__CLASS__, 'var_match'), $haystack);

    self::$_matchable = array();

    return $out;
  }
}

echo CurlyVariables::Replace(array('this' => 'joe', 'that' => 'home'), '{This} goes {that}', true);


string tmp = "{myClass.myVar}";



class myClass
{
    public static $myVar = "some value";
}

*/