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
    
    $mail = new Zend_Mail('utf-8');
    $transport = new Zend_Mail_Transport_Smtp('10.30.10.10',array('username' => 'uai_bbb_hml@mprs.mp.br', 'password' => 'h0m0l0g_BBB', 'port' => '8587',  'ssl' => 'tls', 'auth' => 'login'));

    $mail->addTo('diogo@imdt.com.br');
    $mail->addTo('tiago@imdt.com.br');
    $mail->addTo('diogo.jacobs@gmail.com');
    $mail->setBodyHtml('<html><body><p align="center">Hello!</p></body></html>');
    $mail->send($transport);
} catch (Exception $e) {
    echo "<pre>ERRO: " . $e->getMessage();
    die;
}