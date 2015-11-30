<?php

class Login_AuthController extends IMDT_Controller_Abstract {

    public function init() {
        parent::init();

        $this->view->headLink()->exchangeArray(array());
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/resources/css/bootstrap/bootstrap.min.css'))
                ->appendStylesheet($this->view->baseUrl('/resources/css/bootstrap/bootstrap-responsive.min.css'))
                ->appendStylesheet($this->view->baseUrl('/resources/css/style-login.css'));
    }

    public function indexAction() {
        $this->view->jQuery()->addOnload('$("#public-rooms").click(function(e){e.stopPropagation();e.preventDefault();$("#modalPublicRooms").modal().css({
            "width": function () {
                return ($(document).width() * .5) + "px";
            },
            "margin-left": function () {
                return -($(this).width() / 2);
            }})});');

        $this->view->jQuery()->addOnload('$("#forget-my-pass").click(function(e){forgetPassClickHandler(e);});');
    }

    public function authAction() {
        try {
            $username = $this->_getParam('user', '');
            $password = $this->_getParam('password', '');

            if (($username == '') && ($password == '')) {
                throw new Exception('Você deve informar os dados para efetuar o login.');
            }

            $loginResponse = IMDT_Util_Rest::get('api/login', array('username' => $username, 'password' => $password));

            $currentAuthData = Zend_Auth::getInstance()->getStorage()->read();

            /* Defined in BBBManager_Plugin_Maintenance */
            if (isset($currentAuthData['maintenanceAccessAuthorized'])) {
                $loginResponse['data']['maintenanceAccessAuthorized'] = $currentAuthData['maintenanceAccessAuthorized'];
            }

            Zend_Auth::getInstance()->getStorage()->write($loginResponse['data']);
        } catch (Exception $e) {
            $this->addMessage(array('error' => $e->getMessage()));
        }


        if (($this->_getParam('meetingRoomId', null) != null) && (isset($loginResponse['data']))) {
            $redirectToRefererNs = new Zend_Session_Namespace('redirectToReferer');
            $redirectToRefererNs->uri = '/ui/my-rooms/go/id/' . $this->_getParam('meetingRoomId');
        }

        $this->_redirector->gotoUrl('/');
    }

    public function personaAction() {
        $this->_disableViewAndLayout();
        try {
            $assertion = $this->_getParam('personaAssertion', null);
            $userName = $this->_getParam('userName', '');

            if ($assertion == null) {
                throw new Exception('Você deve informar os dados para efetuar o login.');
            }

            $apiRequestData = array(
                'personaAssertion' => $assertion
            );

            if ($userName != '') {
                $apiRequestData['userName'] = $userName;
            }

            $loginResponse = IMDT_Util_Rest::get('api/login', $apiRequestData);

            if ((!isset($loginResponse['extraInfo']) || ($loginResponse['extraInfo'] != '1'))) {
                Zend_Auth::getInstance()->getStorage()->write($loginResponse['data']);
            }

            $this->_helper->json($loginResponse);
        } catch (Exception $e) {
            $loginResponse = array('success' => '0', 'msg' => $e->getMessage());
        }

        $this->_helper->json($loginResponse);
    }

    public function logoutAction() {
        $this->_disableViewAndLayout();
        Zend_Auth::getInstance()->clearIdentity();

        Zend_Session::destroy(true);
        //Zend_Session::namespaceUnset('acl');

        $this->_redirector->gotoUrl('/');
    }

    public function forgetPassAction() {
        if ($this->_request->isPost()) {
            $this->_disableViewAndLayout();
            $captcha = $this->_getParam('captcha', null);
            $email = $this->_getParam('email', '');

            if (($captcha == null) || ($email == '')) {
                $this->addMessage(array('error' => $this->_helper->translate('Invalid informations') . '.'));
                $this->_redirector->gotoUrl('/');
            }

            $captchaDigitado = $captcha['input'];
            $captchaId = $captcha['id'];

            $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_' . $captchaId);
            $captchaIterator = $captchaSession->getIterator();
            $captchaGerado = $captchaIterator['word'];

            if ($captchaGerado == $captchaDigitado) {
                try {
                    $resetPassResponse = IMDT_Util_Rest::get('/api/users-reset-password', array('email' => $email));
                    $this->addMessage(array('success' => $resetPassResponse['msg']));
                } catch (Exception $e) {
                    $this->addMessage(array('error' => $e->getMessage()));
                }
            } else {
                $this->addMessage(array('error' => $this->_helper->translate('Invalid confirmation code') . '.'));
            }
            $this->_redirector->gotoUrl('/');
        } else {
            $this->_disableLayout();

            $captchaElement = new Zend_Form_Element_Captcha(
                    'captcha', array(
                'label' => $this->_helper->translate('Confirmation code'),
                'captcha' => array(
                    'captcha' => 'Image',
                    'wordLen' => 6,
                    'fontSize' => 30,
                    'timeout' => 300,
                    'lineNoiseLevel' => 1,
                    'width' => 180,
                    'height' => 90,
                    'font' => APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'httpdocs' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'League_Gothic-webfont.ttf',
                    'imgDir' => APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'httpdocs' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'captcha',
                    'imgUrl' => '/tmp/captcha'
                )
            ));

            $this->view->captcha = $captchaElement;
        }
    }

}
