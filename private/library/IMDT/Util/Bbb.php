<?php

class IMDT_Util_Bbb {

    private static $_instance;
    private $_bbbConfig;

    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct() {
        $this->_bbbConfig = $this->_loadConfig();
    }

    private function _loadConfig() {
        $frontend = array(
            'lifetime' => 7200,
            'automatic_serialization' => true
        );

        $cacheDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'cache';
        IMDT_Util_File::checkPath($cacheDir, true);

        $backend = array(
            'cache_dir' => $cacheDir
        );

        $cache = Zend_Cache::factory(
                        'Core', 'File', $frontend, $backend
        );

        $bbbConfigIniFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'bbb.ini';

        if ($cache->load('bbbConfig') != null) {
            $bbbConfig = $cache->load('bbbConfig');
        } elseif (file_exists($bbbConfigIniFile)) {
            $bbbConfigIni = new Zend_Config_Ini($bbbConfigIniFile);

            if ($bbbConfigIni instanceof Zend_Config) {
                $bbbConfig = $bbbConfigIni->toArray();
                $cache->save($bbbConfig, 'bbbConfig');
            }
        }

        return $bbbConfig;
    }

    public function get($key) {
        return (isset($this->_bbbConfig[$key]) ? $this->_bbbConfig[$key] : null);
    }

}
