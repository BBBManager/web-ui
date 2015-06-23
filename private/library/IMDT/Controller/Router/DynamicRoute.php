<?php
class IMDT_Controller_Router_DynamicRoute extends Zend_Controller_Router_Route_Abstract{
    public function assemble($data = array(), $reset = false, $encode = false) {
	
    }

    public static function getInstance(\Zend_Config $config) {
	
    }

    public function match($path) {
	$roomUrl = $path->getRequestUri();
	
	if(substr($roomUrl,0,1) == '/'){
	    $roomUrl = substr($roomUrl,1);
	}
	
	if(substr($roomUrl,-1) == '/'){
	    $roomUrl = substr($roomUrl,0,-1);
	}
	
	try{
	    $apiResponse = IMDT_Util_Rest::get('/api/room-by-url/' . base64_encode($roomUrl));
	    $roomData = $apiResponse['data'];
	    
	    if(!isset($roomData['meeting_room_id'])){
		return false;
	    }
	    
	    return array(
		'module'	=> 'ui',
		'controller'	=> 'my-rooms',
		'action'	=> 'go',
		'id'		=> $roomData['meeting_room_id']
	    );
	}catch(Exception $e){
            return false;
	}
    }
}