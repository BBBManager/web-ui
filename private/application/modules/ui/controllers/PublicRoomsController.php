<?php
class Ui_PublicRoomsController extends IMDT_Controller_Abstract{
    public function indexAction() {
	$this->_disableLayout();
	try{
	    $publicRooms = IMDT_Util_Rest::get('api/public-rooms', array('status' => array(BBBManager_Config_Defines::$ROOM_OPENED, BBBManager_Config_Defines::$ROOM_WAITING)));
	    $this->view->collection = $publicRooms['collection'];
	}catch(IMDT_Controller_Exception_InvalidToken $e1){
	    $this->addMessage(array('error'=>$e1->getMessage()));
	}catch(IMDT_Controller_Exception_AccessDennied $e2){
	    $this->addMessage(array('error'=>$e2->getMessage()));
	}catch(Exception $e) {
	    $this->addMessage(array('error'=>$e->getMessage()));
	}
    }
    
    public function enterAction(){
	$this->_helper->layout()->setLayout('public');
	$this->view->headLink()->appendStylesheet('/resources/css/persona/buttons.css')
			       ->appendStylesheet('/resources/css/public.css');
	
	$this->view->jQuery()->addJavascriptFile('https://login.persona.org/include.js')
			     ->addJavascriptFile('/resources/js/persona-callback.js');
	
	try{
	    
	    $meetingRoomId = $this->_getParam('id', null);
	    
	    if($meetingRoomId == null){
		throw new Exception($this->_helper->translate('Invalid meeting room id') . '.');
	    }
	    
	    $publicRoom = IMDT_Util_Rest::get('api/public-rooms/' . $meetingRoomId);
	    
	    $this->view->room = current($publicRoom['collection']);
	}catch(IMDT_Controller_Exception_InvalidToken $e1){
	    $this->addMessage(array('error'=>$e1->getMessage()));
	}catch(IMDT_Controller_Exception_AccessDennied $e2){
	    $this->addMessage(array('error'=>$e2->getMessage()));
	}catch(Exception $e) {
	    $this->addMessage(array('error'=>$e->getMessage()));
	}
    }

}