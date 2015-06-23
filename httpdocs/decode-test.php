<?php

ini_set('display_errors', 'on');
ini_set('error_reporting', E_ALL);

try {
	defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../private/application'));	
	defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
	set_include_path(implode(PATH_SEPARATOR, array(realpath(APPLICATION_PATH . '/../library'))));
	require_once 'Zend/Application.php';
	$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
	$application->bootstrap();
        var_dump(Zend_Json::decode('{"id":"1"}'));
} catch (Exception $e) {
	echo "<pre>ERRO: ".$e->getMessage();
	die;
}
?>