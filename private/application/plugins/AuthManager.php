<?php

class BBBManager_Plugin_AuthManager extends Zend_Controller_Plugin_Abstract {

    private $_authProvider;
    private $_originalRequestedUrl;

    public function __construct() {
        $this->_authProvider = Zend_Auth::getInstance();
        $this->_originalRequestedUrl = $_SERVER['REQUEST_URI'];
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        /* echo '<pre>';
          var_dump($request->getParams());
          die; */
        /* $requestingAuth = ($request->getModuleName() == 'login' && $request->getControllerName() == 'auth' && $request->getActionName() == 'auth');
          parse_str(file_get_contents('php://input'), $arguments);

          //$hasUserAndPassword = ((isset($arguments['user']) && $arguments['user'] != '') && ((isset($arguments['password']) && $arguments['password'] != '')));
          $hasUserIdAndToken = ((isset($arguments['userId']) && $arguments['userId'] != '') && ((isset($arguments['token']) && $arguments['token'] != ''))); */

        $autoJoinToken = $request->getParam('aj', null);
        $uid = $request->getParam('uid', null);

        if ($autoJoinToken != null) {
            IMDT_Util_Auth::getInstance()->set('token', $autoJoinToken);
            IMDT_Util_Auth::getInstance()->set('userId', $uid);
            $apiResponse = IMDT_Util_Rest::get('/api/auto-join/' . $autoJoinToken);
            if ($apiResponse['success'] == '1') {
                Zend_Auth::getInstance()->getStorage()->write($apiResponse['data']);
                $redirectToRefererNs = new Zend_Session_Namespace('redirectToReferer');
                /* var_dump($redirectToRefererNs->uri);
                  var_dump( Zend_Auth::getInstance()->hasIdentity());die;
                  $this->getResponse()->setRedirect('/ui/my-rooms/go/id/' . $request->getParam('id')); */
                $redirectToRefererNs->uri = '/ui/my-rooms/go/id/' . $request->getParam('id');
            }
        }
        $requestingPublicResource = ($request->getModuleName() == 'ui' && $request->getControllerName() == 'assets');
        $requestingPublicResource = $requestingPublicResource || ($request->getModuleName() == 'ui' && $request->getControllerName() == 'public-rooms');
        $requestingPublicResource = $requestingPublicResource || ($request->getModuleName() == 'login' && $request->getControllerName() == 'auth' && $request->getActionName() == 'auth');
        $requestingPublicResource = $requestingPublicResource || ($request->getModuleName() == 'login' && $request->getControllerName() == 'auth' && $request->getActionName() == 'forget-pass');
        $requestingPublicResource = $requestingPublicResource || ($request->getModuleName() == 'login' && $request->getControllerName() == 'auth' && $request->getActionName() == 'persona');

        /* $hasUserAndPassword = (($request->getParam('user', null) != null) && (($request->getParam('password', null) != null)));
          //$hasUserIdAndToken = (($request->getParam('userId', null) != null) && (($request->getParam('token', null) != null)));
          $hasUserIdAndToken = (($request->getHeader('userId', null) != null) && (($request->getHeader('token', null) != null)));

          //TODO handler for invalid token
          if($hasUserIdAndToken) {
          return;
          } */

        $authData = Zend_Auth::getInstance()->getStorage()->read();

        if ((!isset($authData['id'])) && (!$requestingPublicResource)) {
            $request->setModuleName('login')
                    ->setControllerName('auth')
                    ->setActionName('index');

            Zend_Layout::getMvcInstance()->setLayout('auth');

            $ignoredRedirects = array(
                '/favicon.ico'
            );

            if (!in_array($this->_originalRequestedUrl, $ignoredRedirects)) {
                $redirectToRefererNs = new Zend_Session_Namespace('redirectToReferer');
                $redirectToRefererNs->uri = $this->_originalRequestedUrl;
            }
        }
    }

}
