<?php

abstract class IMDT_Controller_Abstract extends Zend_Controller_Action {

    protected $_redirector;
    protected $_authData;
    protected $_flashMessenger = null;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        parent::__construct($request, $response, $invokeArgs);

        $this->_authData = Zend_Auth::getInstance()->getStorage()->read();
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->view->currentUrl = '/' . $request->getModuleName() . '/' . $request->getControllerName() . '/' . $request->getActionName();
    }

    /* public function init() {
      $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
      } */

    protected function _disableLayout() {
        $this->_helper->layout()->disableLayout();
    }

    protected function _disableView() {
        $this->_helper->viewRenderer->setNoRender();
    }

    protected function _disableViewAndLayout() {
        $this->_disableLayout();
        $this->_disableView();
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
    }

}

?>