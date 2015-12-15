<?php

class Ui_MyRoomsController extends IMDT_Controller_Abstract {

    public function init() {
        $this->filters = array();
        $this->filters['date_start'] = array('name' => 'date_start', 'label' => $this->_helper->translate('Start'), 'type' => 'date');
        $this->filters['recordings_count'] = array('name' => 'recordings_count', 'label' => $this->_helper->translate('teste'), 'type' => 'integer');
        //$this->filters['name'] = array('name' => 'name', 'label' => $this->_helper->translate('Name'), 'type' => 'text');

        $this->view->parameters = IMDT_Util_Url::getThisParams($this->filters);
    }

    public function indexAction() {
        $this->view->parameters['status'] = array('status' => array(BBBManager_Config_Defines::$ROOM_OPENED, BBBManager_Config_Defines::$ROOM_WAITING));
        $this->view->parameters['status_c'] = 'i';

        $this->view->filters = $this->filters;

        try {
            $response = IMDT_Util_Rest::get('/api/my-rooms', $this->view->parameters);

            $collectionFinal = array();

            if (is_array($response['collection']) && (count($response['collection']) > 0)) {
                foreach ($response['collection'] as $room) {
                    if (!isset($collectionFinal[$room['meeting_room_id']])) {
                        $collectionFinal[$room['meeting_room_id']] = $room;
                    }

                    switch ($room['status']) {
                        case BBBManager_Config_Defines::$ROOM_OPENED :
                            $collectionFinal[$room['meeting_room_id']]['button_class'] = 'btn-success';
                            break;
                        case BBBManager_Config_Defines::$ROOM_WAITING :
                            $collectionFinal[$room['meeting_room_id']]['button_class'] = 'btn-warning';
                            break;
                        default :
                            $collectionFinal[$room['meeting_room_id']]['button_class'] = 'btn-danger';
                            break;
                    }

                    /* Get the most power profile in the room linked groups collection */
                    if (!empty($room['group_profile'])) {
                        if ($room['group_profile'] < $collectionFinal[$room['meeting_room_id']]['group_profile']) {
                            $collectionFinal[$room['meeting_room_id']]['group_profile'] = $room['group_profile'];
                        }
                    }

                    /* Get the most power profile in the room linked users collection */
                    if (!empty($room['user_profile'])) {
                        if ($room['user_profile'] < $collectionFinal[$room['meeting_room_id']]['user_profile']) {
                            $collectionFinal[$room['meeting_room_id']]['user_profile'] = $room['user_profile'];
                        }
                    }
                }

                foreach ($collectionFinal as &$room) {
                    /* Detect if the user is administrator or moderator in the room */
                    if (in_array($room['user_profile_in_meeting'], array(BBBManager_Config_Defines::$ROOM_ADMINISTRATOR_PROFILE, BBBManager_Config_Defines::$ROOM_MODERATOR_PROFILE))) {
                        $room['show_actions'] = true;
                    } else {
                        $room['show_actions'] = false;
                    }
                }
            }

            $this->view->collection = $collectionFinal;
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $this->_redirector->goToUrlAndExit('/login/auth/logout');
        } catch (Exception $e) {
            $this->addMessage(array('error' => $e->getMessage()));
        }
    }
    
    public function killmeAction() {
        try {
            $roomId = $this->_getParam('id', null);

            $this->_helper->layout()->setLayout('public');
            
            if ($roomId == null) {
                throw new Exception($this->_helper->translate('Invalid room id'));
            }
            
            $response = IMDT_Util_Rest::delete('/api/my-rooms/' . $roomId);
            
            if(!isset($response['error'])) {
                $this->_redirector->goToUrlAndExit('/my-rooms/go/id/'.$roomId);
            } else {
                $this->addMessage(array('error' => $response['error']));
                $this->_redirector->goToUrlAndExit('/my-rooms/go/id/'.$roomId);
            }
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $this->_redirector->goToUrlAndExit('/login/auth/logout');
        } catch (Exception $e) {
            $this->addMessage(array('error' => $e->getMessage()));
        }
    }

    public function goAction() {
        $this->_helper->layout()->setLayout('public');
        $this->view->headLink()->appendStylesheet('/resources/css/public.css');

        try {
            $roomId = $this->_getParam('id', null);

            if ($roomId == null) {
                throw new Exception($this->_helper->translate('Invalid room id'));
            }

            $response = IMDT_Util_Rest::get('/api/my-rooms/' . $roomId);

            if (isset($response['accessable']) && ($response['accessable'] == '0')) {
                $this->view->validRoom = false;
                $this->view->roomName = $response['roomName'];
                $this->view->errorMessage = true;
                $this->view->accessable = false;
                $this->addMessage(array('error' => IMDT_Util_Translate::_('You don\'t have access to this room.')));
                return;
            }

            $this->view->already_in_meeting = false;
            $this->view->validRoom = false;
            $this->view->roomName = $response['roomName'];
            $this->view->killExistingSessionUrl = '/my-rooms/killme/id/'.$roomId;

            $joinUrl = '';
            $errorMessage = '';

            if ($response['meetingRoomStatus'] == BBBManager_Config_Defines::$ROOM_OPENED) {
                $bbbApiResponse = Zend_Json::decode($response['bbbApiResponse']);

                if (isset($bbbApiResponse['joinURL'])) {
                    $joinUrl = trim($bbbApiResponse['joinURL']);
                } else {
                    $this->view->acessable = true;
                    $errorMessage = $bbbApiResponse['error'];
                    
                    if($errorMessage === 'The user is already in meeting') {
                        $this->view->already_in_meeting = true;
                    }
                }
            }

            if ($response['success'] == '1' && $joinUrl != '') {
                header('Location: ' . $joinUrl);
            } elseif ($response['success'] == '1' && trim($errorMessage) != '') {
                throw new Exception(trim($this->_helper->translate($errorMessage)));
            } elseif ($response['success'] == '1' && trim($response['timeToWait']) != '') {
                $this->view->timeToWait = $response['timeToWait'];
                $this->view->validRoom = true;
                $this->view->jQuery()->addJavascriptFile('/resources/js/jquery/plugins/countdown/jquery.countdown.js');
            } elseif ($response['success'] == '1' && trim($response['joinUrl']) == '') {
                throw new Exception($response['msg']);
            } elseif ($response['success'] == '0') {
                throw new Exception($response['msg']);
            }
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $this->_redirector->goToUrlAndExit('/login/auth/logout');
        } catch (IMDT_Controller_Exception_AccessDennied $e2) {
            $this->addMessage(array('error' => $e2->getMessage()));
        } catch (Exception $e) {
            $this->addMessage(array('error' => $e->getMessage()));
            $this->view->errorMessage = true;
        }
    }

    public function allAction() {
        $this->view->tableSource = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'table-content');
        $this->view->uriPage = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => $this->_request->getActionName());
        $this->view->uriExport = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'export');

        $this->view->parameters = IMDT_Util_Url::getThisParams($this->filters);

        $hideWithoutRecordings = $this->_getParam('hide-without-recordings', '0');

        if ($hideWithoutRecordings != '0') {
            $this->view->parameters['recordings_count'] = '1';
            $this->view->parameters['recordings_count_c'] = 'g';
        }

        if ($this->_getParam('view-mode') == 'tree') {

            if ($this->_getParam('hide-rooms-without-recordings', '0') != '0') {
                $this->view->parameters['recordings_count'] = '1';
                $this->view->parameters['recordings_count_c'] = 'g';
                $hideWithoutRecordings = '1';
            }

            if (($this->_getParam('min-date', '') != '') && ($this->_getParam('max-date', '') != '')) {
                $this->view->parameters['date_start'] = IMDT_Util_Date::filterDateToApi($this->_getParam('min-date'));
                $this->view->parameters['date_start_2'] = IMDT_Util_Date::filterDateToApi($this->_getParam('max-date'));
                $this->view->parameters['date_start_c'] = 'b';
            }

            //$this->filters['date_start'] = array('name' => 'date_start', 'label' => $this->_helper->translate('Start'), 'type' => 'date');

            $categoriesResponse = IMDT_Util_Rest::get('/api/categories');
            $roomsResponse = IMDT_Util_Rest::get('/api/my-rooms', $this->view->parameters);

            $collection = array(
                array(
                    'id' => -1,
                    'name' => $this->_helper->translate('Uncategorised'),
                    'parent_id' => null,
                    'isCategory' => true
                )
            );

            foreach ($roomsResponse['collection'] as $room) {
                if ($room['meeting_room_category_id'] == NULL) {
                    $collection[] = array(
                        'id' => $room['meeting_room_id'],
                        'name' => $room['name'],
                        'hierarchy' => '-1',
                        'parent_id' => -1,
                        'status' => $room['status'],
                        'isCategory' => false,
                        'date_start' => IMDT_Util_Date::filterDatetimeToCurrentLang($room['date_start'], false),
                        'date_end' => IMDT_Util_Date::filterDatetimeToCurrentLang($room['date_end'], false),
                        'recordings_count' => $room['recordings_count'],
                    );
                }
            }

            foreach ($categoriesResponse['collection'] as $category) {
                if (isset($category['hierarchy'])) {
                    $collection[] = array(
                        'id' => $category['meeting_room_category_id'],
                        'name' => $category['name'],
                        'parent_id' => $category['parent_id'],
                        'hierarchy' => $category['hierarchy'],
                        'isCategory' => true
                    );
                } else {
                    $collection[] = array(
                        'id' => $category['meeting_room_category_id'],
                        'name' => $category['name'],
                        'parent_id' => $category['parent_id'],
                        'isCategory' => true
                    );
                }



                foreach ($roomsResponse['collection'] as $room) {
                    if ($room['meeting_room_category_id'] == $category['meeting_room_category_id']) {
                        if (!isset($categoriesResponse['collection'][$room['meeting_room_category_id']]['hierarchy'])) {
                            $hierarchy = $room['meeting_room_category_id'];
                        } else {
                            $hierarchy = $categoriesResponse['collection'][$room['meeting_room_category_id']]['hierarchy'] . '-' . $room['meeting_room_category_id'];
                        }

                        $collection[] = array(
                            'id' => $room['meeting_room_id'],
                            'name' => $room['name'],
                            'hierarchy' => $hierarchy,
                            'parent_id' => $room['meeting_room_category_id'],
                            'status' => $room['status'],
                            'isCategory' => false,
                            'date_start' => IMDT_Util_Date::filterDatetimeToCurrentLang($room['date_start'], false),
                            'date_end' => IMDT_Util_Date::filterDatetimeToCurrentLang($room['date_end'], false),
                            'recordings_count' => $room['recordings_count'],
                        );
                    }
                }
            }

            $this->view->collection = $collection;
            $this->view->view_mode = 'tree';
        }else {
            $this->view->view_mode = 'list';
        }

        $this->view->filters = $this->filters;
        $this->view->ui_state = array_merge(array('view-mode' => $this->view->view_mode, 'hide-without-recordings' => $hideWithoutRecordings), $this->view->parameters);
    }

    public function tableContentAction() {
        $objResponse = new stdClass();

        try {
            $params = IMDT_Util_Url::getThisParams($this->filters);

            $response = IMDT_Util_Rest::get('/api/my-rooms', $params);
            $arrTable = array();
            foreach ($response['collection'] as $curr) {
                $row = array();
                $row[] = $curr['name'];
                $row[] = IMDT_Util_Date::filterDatetimeToCurrentLang($curr['date_start'], false);
                $row[] = IMDT_Util_Date::filterDatetimeToCurrentLang($curr['date_end'], false);
                $actions = '<td nowrap="nowrap">';
                $showHistory = (in_array($curr['user_profile_in_meeting'], array(BBBManager_Config_Defines::$ROOM_MODERATOR_PROFILE, BBBManager_Config_Defines::$ROOM_ADMINISTRATOR_PROFILE)) != false);

                if ($curr['recordings_count'] > 0) {
                    $actions .= '<a title="' . $this->_helper->translate('View Recording') . '" data-toggle="tooltip" class="btn btn-mini" href="/ui/recordings/view/id/' . $curr['meeting_room_id'] . ((isset($params['recordings_count']) && ($params['recordings_count'] != '0')) ? '/hide-without-recordings/1' : '') . '" data-original-title="' . $this->_helper->translate('View Recording') . '"><i class="icon-eye-open"></i></a>';
                }

                if ($showHistory) {
                    $actions .= '<a title="' . $this->_helper->translate('History') . '" data-toggle="tooltip" class="btn btn-mini" href="/ui/rooms/history/id/' . $curr['meeting_room_id'] . '" data-original-title="' . $this->_helper->translate('History') . '"><i class="icon-list-alt"></i></a>';
                }

                $actions .= '</td>';
                $row[] = $actions;
                $row['DT_RowId'] = 'row_' . $curr['meeting_room_id'];
                $row['DT_RowClass'] = 'meetingRoomStatus' . $curr['status'];
                $arrTable[] = $row;
            }

            $objResponse->success = '1';
            $objResponse->msg = '';
            $objResponse->aaData = $arrTable;
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->aaData = array();
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
            $objResponse->aaData = array();
        }
        $this->_helper->json($objResponse);
    }
}
