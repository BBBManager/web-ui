<?php

class IMDT_Util_Password {

    static public function generate($tamanho = 6) {
        $letras = "qwertyuiopasdfghjklzxcvbnm";
        $numeros = "1234567890";

        $str = $letras . $numeros;

        $password = '';
        for ($i = 0; $i < $tamanho; $i++) {
            $password .= substr($str, rand(0, strlen($str) - 1), 1);
        }

        return $password;
    }
}