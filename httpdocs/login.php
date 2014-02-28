<?
ini_set('display_errors', 'on');
ini_set('error_reporting', E_ALL);

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath('D:\\@work\\00-customers\\MP-RS\wwwroot-api\\private\\application'));	
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
set_include_path(implode(PATH_SEPARATOR, array(realpath(APPLICATION_PATH . '/../library'))));
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap();

$assertion = $_REQUEST['personaAssertion'];

$persona = new IMDT_Service_Auth_Adapter_Persona();
$personaResponse = $persona->verifyAssertion($assertion);

echo '<pre>';
var_dump($personaResponse);
echo '</pre>';
?>