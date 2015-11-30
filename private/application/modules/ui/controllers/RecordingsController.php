<?php

class Ui_RecordingsController extends IMDT_Controller_Abstract {

    public function init() {
        
    }

    public function indexAction() {
        
    }

    public function listAction() {
        
    }

    public function editAction() {
        
    }

    public function deleteAction() {
        
    }

    public function viewAction() {
        $meetingRoomId = $this->_getParam('id');

        $parameters['meeting_room_id'] = $meetingRoomId;
        $parameters['meeting_room_id_c'] = 'e';

        $parameters['sync_done'] = '1';
        $parameters['sync_done_c'] = 'e';

        try {
            $response = IMDT_Util_Rest::get('/api/recordings', $parameters);

            $recordingsCollection = $response['collection'];

            foreach ($recordingsCollection as $i => $recording) {
                $recording['playback_url'] = preg_replace('/\/presentation/', '', $recording['playback_url']);

                if (isset($_SERVER['HTTPS']) && ( $_SERVER['HTTPS'] != '')) {
                    $recording['playback_url'] = preg_replace('/http\:\//', 'https:/', $recording['playback_url']);
                }

                $start_date = new DateTime(date('Y-m-d H:i:s', strtotime($recording['date_start'])));
                $end_date = new DateTime(date('Y-m-d H:i:s', strtotime($recording['date_end'])));
                $interval = $start_date->diff($end_date);
                $hours = $interval->format('%h');
                $minutes = $interval->format('%i');
                $seconds = $interval->format('%s');

                //$recording['duration'] = sprintf('%s:%s',$hours,$minutes);
                $recording['duration'] = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

                if ($i == 0) {
                    $this->view->firstRecording = $recording;
                    $this->view->recordings = array();
                }

                $this->view->recordings[] = $recording;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $this->view->jQuery()->addOnload('checkWebMSupport();');

        $uiState = array();

        if ($this->_getParam('hide-without-recordings', '0') == '1') {
            $uiState['hide-without-recordings'] = '1';
        }

        if ($this->_getParam('view-mode', 'list') == 'tree') {
            $uiState['view-mode'] = 'tree';
        }

        if ($this->_getParam('date_start', '') != '') {
            $uiState['min-date'] = $this->_getParam('date_start');
        }

        if ($this->_getParam('date_start_2', '') != '') {
            $uiState['max-date'] = $this->_getParam('date_start_2');
        }

        $this->view->backUrl = $this->view->url(array_merge(array('module' => 'ui', 'controller' => 'my-rooms', 'action' => 'all'), $uiState), 'default', true);
    }

    public function infoAction() {
        $this->_disableViewAndLayout();
        $objResponse = new stdClass();

        try {
            $id = $this->_getParam('id', null);
            if ($id == null) {
                throw new Exception('Invalid recording id');
            }

            $response = IMDT_Util_Rest::get('/api/recordings/' . $id);
            $row = $response['row'];

            $arrForm['record_id'] = $row['record_id'];
            $arrForm['meeting_room_id'] = $row['meeting_room_id'];
            $arrForm['name'] = $row['name'];
            $arrForm['date_start'] = IMDT_Util_Date::filterDatetimeToCurrentLang($row['date_start'], false);
            $arrForm['date_end'] = IMDT_Util_Date::filterDatetimeToCurrentLang($row['date_end'], false);

            $start_date = new DateTime(date('Y-m-d H:i:s', strtotime($row['date_start'])));
            $end_date = new DateTime(date('Y-m-d H:i:s', strtotime($row['date_end'])));
            $interval = $start_date->diff($end_date);

            $arrForm['duration'] = sprintf('%02d:%02d:%02d', $interval->format('%h'), $interval->format('%i'), $interval->format('%s'));

            $objResponse->form = $arrForm;

            $objResponse->success = '1';
            $objResponse->msg = '';
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

    public function formPostAction() {
        $objResponse = new stdClass();
        try {
            $id = $this->_request->getParam('record_id', null);
            $meetingRoomId = $this->_request->getParam('meeting_room_id', null);

            if (!$this->_request->isPost())
                throw new Exception($this->_helper->translate('Invalid Request'));
            if ($id == null)
                throw new Exception($this->_helper->translate('Invalid Id'));

            $data = array();
            $data['name'] = $this->_request->getPost('name', null);

            $arrRestResponse = IMDT_Util_Rest::put('/api/recordings/' . $id, $data);

            $objResponse->success = '1';
            $objResponse->msg = $arrRestResponse['msg'];
            $objResponse->redirect = $this->view->url(array('module' => 'ui', 'controller' => 'rooms', 'action' => 'manage-recording', 'id' => $meetingRoomId));
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

}
