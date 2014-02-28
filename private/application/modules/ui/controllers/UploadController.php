<?php

class Ui_UploadController extends IMDT_Controller_Abstract {
    public function postAction() {
	$this->_disableViewAndLayout();
	$upload_handler = new JQueryUploader_UploadHandler(null, false);
        $response = $upload_handler->post(false);
        
        $newResponse = array();
        foreach($response as $key => $value){
            $newResponse[$key] = $value;
        }
        
        $newFiles = $newResponse['files'];
        
        foreach($newFiles as $indice => &$file){
            $fileName = $_FILES['files']['name'][$indice];
            $file->displayName = $fileName;
        }
        
        echo json_encode($newResponse);
    }
}