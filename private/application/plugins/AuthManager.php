<?php
class BBBManager_Plugin_AuthManager extends Zend_Controller_Plugin_Abstract {
    private $_authProvider;
    private $_originalRequestedUrl;
    
    public function __construct(){
        $this->_authProvider = Zend_Auth::getInstance();
	$this->_originalRequestedUrl = $_SERVER['REQUEST_URI'];
    }
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        /*echo '<pre>';
        var_dump($request->getParams());
        die;*/
        /*$requestingAuth = ($request->getModuleName() == 'login' && $request->getControllerName() == 'auth' && $request->getActionName() == 'auth');
        parse_str(file_get_contents('php://input'), $arguments);
        
        //$hasUserAndPassword = ((isset($arguments['user']) && $arguments['user'] != '') && ((isset($arguments['password']) && $arguments['password'] != '')));
        $hasUserIdAndToken = ((isset($arguments['userId']) && $arguments['userId'] != '') && ((isset($arguments['token']) && $arguments['token'] != '')));*/
        
        $requestingPublicResource = ($request->getModuleName() == 'ui' && $request->getControllerName() == 'assets');
        $requestingPublicResource = $requestingPublicResource || ($request->getModuleName() == 'ui' && $request->getControllerName() == 'public-rooms');
        $requestingPublicResource = $requestingPublicResource || ($request->getModuleName() == 'login' && $request->getControllerName() == 'auth' && $request->getActionName() == 'auth');
        $requestingPublicResource = $requestingPublicResource || ($request->getModuleName() == 'login' && $request->getControllerName() == 'auth' && $request->getActionName() == 'forget-pass');
        $requestingPublicResource = $requestingPublicResource || ($request->getModuleName() == 'login' && $request->getControllerName() == 'auth' && $request->getActionName() == 'persona');
	
	/*$hasUserAndPassword = (($request->getParam('user', null) != null) && (($request->getParam('password', null) != null)));
        //$hasUserIdAndToken = (($request->getParam('userId', null) != null) && (($request->getParam('token', null) != null)));
        $hasUserIdAndToken = (($request->getHeader('userId', null) != null) && (($request->getHeader('token', null) != null)));
	
		//TODO handler for invalid token
        if($hasUserIdAndToken) {
            return;
        }*/
        
        if((! Zend_Auth::getInstance()->hasIdentity()) && (! $requestingPublicResource)) {
            $request->setModuleName('login')
                    ->setControllerName('auth')
                    ->setActionName('index');
            
            Zend_Layout::getMvcInstance()->setLayout('auth');
	    
	    $redirectToRefererNs = new Zend_Session_Namespace('redirectToReferer');
	    $redirectToRefererNs->uri = $this->_originalRequestedUrl;
        }
    }
}