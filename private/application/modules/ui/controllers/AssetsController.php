<?php
class Ui_AssetsController extends IMDT_Controller_Abstract{

    /*public function init(){
		parent::init();
	}*/
	
    public function langjsAction() {
    	$this->_helper->layout->disableLayout();
	$this->_response->setHeader('Content-Type', 'application/x-javascript');
    }
}

