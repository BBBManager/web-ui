<?php

abstract class IMDT_Controller_Abstract extends Zend_Controller_Action {

    protected $_redirector;
    protected $_authData;
    protected $_flashMessenger = null;
    protected $_aclSkipActions = array();
    protected $_shouldSkipAcl = false;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        parent::__construct($request, $response, $invokeArgs);

        $this->_authData = Zend_Auth::getInstance()->getStorage()->read();
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->view->currentUrl = '/' . $request->getModuleName() . '/' . $request->getControllerName() . '/' . $request->getActionName();

        if($this->_getParam('id', -1) !== -1) {
            $this->view->breadcrumbIdLink = join('', array(
                '<a href="',
                $this->view->url(array('id'=>$this->_getParam('id'))),
                '">#',
                $this->_getParam('id'),
                '</a>'
            ));
        }
    }

    protected function _disableLayout() {
        $this->_helper->layout()->disableLayout();
    }

    protected function _disableView() {
        $this->_helper->viewRenderer->setNoRender();
    }

    protected function _disableViewAndLayout() {
        $this->_disableLayout();
        $this->_disableView();
        $this->_skipAcl();
    }

    protected function _skipAcl() {
        $this->_shouldSkipAcl = true;
    }

    /* public function preDispatch() {
      if(!$this->_request->isPost()) {
      $this->view->messages = $this->_flashMessenger->getMessages();
      }
      } */

    /* public function postDispatch() {
      if($this->_request->isPost()) {
      $this->view->messages = $this->_flashMessenger->getCurrentMessages();
      $this->_flashMessenger->clearCurrentMessages();
      }

      if($this->_flashMessenger->hasMessages()){
      $this->view->messages = $this->_flashMessenger->getMessages();
      }
      } */

    public function addMessage($message) {
        $this->getFlashMessenger()->addMessage($message);
    }

    private function getFlashMessenger() {
        if ($this->_flashMessenger == null) {
            $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        }

        return $this->_flashMessenger;
    }

    public function postDispatch() {
        $this->view->messages = array_merge($this->getFlashMessenger()->getMessages(), $this->getFlashMessenger()->getCurrentMessages());
        $this->getFlashMessenger()->clearCurrentMessages();
        $this->getFlashMessenger()->clearMessages();

        if(isset($this->api)) {
            if(!in_array($this->getRequest()->getActionName(), $this->_aclSkipActions)
                && !$this->_shouldSkipAcl) {

                $type = $this->getRequest()->getActionName();
                switch($type) {
                    case 'new':
                        $type = 'insert';
                    break;
                    case 'index':
                        $type = 'list';
                    break;
                }

                if (!IMDT_Util_Acl::getInstance()->isAllowed($this->api, $type)) {
                    throw new IMDT_Controller_Exception_AccessDennied($this->_helper->translate('You don\'t have access to the requested resource'));
                }
            }
        }
    }

}

?>