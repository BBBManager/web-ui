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

            $this->view->validRoom = false;
            $this->view->roomName = $response['roomName'];

            $joinUrl = '';
            $errorMessage = '';

            if ($response['meetingRoomStatus'] == BBBManager_Config_Defines::$ROOM_OPENED) {
                $bbbApiResponse = Zend_Json::decode($response['bbbApiResponse']);
                $propertyName = $bbbApiResponse['properties'][0]['property']['@name'];

                if ($propertyName == 'error') {
                    $errorMessage = IMDT_Util_Translate::_($bbbApiResponse['properties'][0]['property']['@value']);
                } elseif ($propertyName == 'joinURL') {
                    $joinUrl = trim($bbbApiResponse['properties'][0]['property']['@value']);
                }
            }

            if ($response['success'] == '1' && $joinUrl != '') {
                header('Location: ' . $joinUrl);
            } elseif ($response['success'] == '1' && trim($errorMessage) != '') {
                throw new Exception(trim($errorMessage));
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
            //$this->_redirector->goToUrlAndExit('/ui/my-rooms');
        } catch (Exception $e) {
            $this->addMessage(array('error' => $e->getMessage()));
            $this->view->errorMessage = true;
            //$this->_redirector->goToUrlAndExit('/ui/my-rooms');
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

            $collection = array();

            foreach ($categoriesResponse['collection'] as $category) {
                if (isset($category['hierarchy'])) {
                    $collection[] = array(
                        'id' => $category['meeting_room_category_id'],
                        'name' => $category['name'],
                        'parent_id' => $category['parent_id'],
                        'hierarchy' => $category['hierarchy']
                    );
                } else {
                    $collection[] = array(
                        'id' => $category['meeting_room_category_id'],
                        'name' => $category['name'],
                        'parent_id' => $category['parent_id']
                    );
                }



                foreach ($roomsResponse['collection'] as $room) {
                    if ($room['meeting_room_category_id'] == $category['meeting_room_category_id']) {

                        if (!isset($categoriesResponse['collection'][$room['meeting_room_category_id']]['hierarchy']))
                            continue;

                        $collection[] = array(
                            'id' => $room['meeting_room_id'],
                            'name' => $room['name'],
                            'hierarchy' => $categoriesResponse['collection'][$room['meeting_room_category_id']]['hierarchy'] . '-' . $room['meeting_room_category_id'],
                            'parent_id' => $room['meeting_room_category_id'],
                            'status' => $room['status'],
                            'date_start' => IMDT_Util_Date::filterDatetimeToCurrentLang($room['date_start'], false),
                            'date_end' => IMDT_Util_Date::filterDatetimeToCurrentLang($room['date_end'], false)
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

            /* $params['status'] = BBBManager_Config_Defines::$ROOM_CLOSED;
              $params['status_c'] = 'e'; */

            /* echo '<pre>';
              var_dump($params);
              echo '</pre>';;die; */
            $response = IMDT_Util_Rest::get('/api/my-rooms', $params);
            $arrTable = array();
            foreach ($response['collection'] as $curr) {
                $row = array();
                $row[] = $curr['name'];
                $row[] = IMDT_Util_Date::filterDatetimeToCurrentLang($curr['date_start'], false);
                $row[] = IMDT_Util_Date::filterDatetimeToCurrentLang($curr['date_end'], false);
                $actions = '<td nowrap="nowrap">';
                //$actions .= '<a title="' . $this->_helper->translate('View') . '" data-toggle="tooltip" class="btn btn-mini" href="/ui/rooms/view/id/' . $curr['meeting_room_id'] . '" data-original-title="' . $this->_helper->translate('View') . '"><i class="icon-eye-open"></i></a>';
                $showHistory = (in_array($curr['user_profile_in_meeting'], array(BBBManager_Config_Defines::$ROOM_MODERATOR_PROFILE, BBBManager_Config_Defines::$ROOM_ADMINISTRATOR_PROFILE)) != false);
                /* $showHistory = $showHistory || (IMDT_Util_Auth::getInstance()->get('globalRead') == true);
                  $showHistory = $showHistory || (IMDT_Util_Auth::getInstance()->get('globalWrite') == true); */

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

    /*
      public function formManageAttendessContentAction() {
      $objResponse = new stdClass();

      try {
      $objResponse = new stdClass();
      $id = $this->_request->getParam('id');

      if ($id == 'new') {
      $oAuth = Zend_Auth::getInstance();
      if ($oAuth->hasIdentity()) {
      $auth = $oAuth->getStorage()->read();

      if ($auth['auth_mode'] == BBBManager_Config_Defines::$LOCAL_AUTH_MODE) {
      $arrForm['user_admin_local'] = $auth['id'];
      } else if ($auth['auth_mode'] == BBBManager_Config_Defines::$LDAP_AUTH_MODE) {
      $arrForm['user_admin_ldap'] = $auth['id'];
      }
      }

      $objResponse->form = $arrForm;
      } elseif ($id) {
      $response = IMDT_Util_Rest::get('/api/rooms/' . $id . '.json');

      $row = $response['row'];
      $arrForm['name'] = $row['name'];
      $arrForm['date_start'] = IMDT_Util_Date::filterDatetimeToCurrentLang($row['date_start'], false);
      $arrForm['date_end'] = IMDT_Util_Date::filterDatetimeToCurrentLang($row['date_end'], false);
      $arrForm['timezone'] = $row['timezone'];
      $arrForm['encrypted'] = $row['encrypted'];
      $arrForm['privacy_policy'] = $row['privacy_policy'];
      $arrForm['record'] = $row['record'];
      $arrForm['url'] = $row['url'];
      $arrForm['participants_limit'] = $row['participants_limit'];
      $arrForm['group_admin_local'] = $row['group_admin_local'];
      $arrForm['group_admin_ldap'] = $row['group_admin_ldap'];
      $arrForm['group_moderator_local'] = $row['group_moderator_local'];
      $arrForm['group_moderator_ldap'] = $row['group_moderator_ldap'];
      $arrForm['group_presenter_local'] = $row['group_presenter_local'];
      $arrForm['group_presenter_ldap'] = $row['group_presenter_ldap'];
      $arrForm['group_attendee_local'] = $row['group_attendee_local'];
      $arrForm['group_attendee_ldap'] = $row['group_attendee_ldap'];
      $arrForm['user_admin_local'] = $row['user_admin_local'];
      $arrForm['user_admin_ldap'] = $row['user_admin_ldap'];
      $arrForm['user_moderator_local'] = $row['user_moderator_local'];
      $arrForm['user_moderator_ldap'] = $row['user_moderator_ldap'];
      $arrForm['user_presenter_local'] = $row['user_presenter_local'];
      $arrForm['user_presenter_ldap'] = $row['user_presenter_ldap'];
      $arrForm['user_attendee_local'] = $row['user_attendee_local'];
      $arrForm['user_attendee_ldap'] = $row['user_attendee_ldap'];

      $objResponse->form = $arrForm;
      }

      $arrSelect2 = array('group_local' => array(), 'group_ldap' => array(), 'user_local' => array(), 'user_ldap' => array());

      $collectionGroups = IMDT_Util_Rest::get('/api/groups.json');
      foreach ($collectionGroups['collection'] as $obj) {
      $nRow = array();
      $nRow['id'] = $obj['group_id'];
      $nRow['text'] = $obj['name'];

      if ($obj['auth_mode_id'] == '1') {// Local
      $arrSelect2['group_local'][] = $nRow;
      } elseif ($obj['auth_mode_id'] == '2') {//Ldap
      $arrSelect2['group_ldap'][] = $nRow;
      }
      }

      $collectionUsers = IMDT_Util_Rest::get('/api/users.json');
      foreach ($collectionUsers['collection'] as $obj) {
      $nRow = array();
      $nRow['id'] = $obj['user_id'];
      $nRow['text'] = $obj['name'];
      //$nRow['auth_mode_id'] = $obj['auth_mode_id'];

      if ($obj['auth_mode_id'] == BBBManager_Config_Defines::$LOCAL_AUTH_MODE) {// Local
      $arrSelect2['user_local'][] = $nRow;
      } elseif ($obj['auth_mode_id'] == BBBManager_Config_Defines::$LDAP_AUTH_MODE) {//Ldap
      $arrSelect2['user_ldap'][] = $nRow;
      }
      }

      $objResponse->select2 = $arrSelect2;
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

      public function formManageAttendessPostAction() {
      $objResponse = new stdClass();

      try {
      $id = $this->_request->getParam('meeting_room_id', null);
      $profileId = $this->_request->getParam('meeting_room_profile_id', null);

      if (!$this->_request->isPost())
      throw new Exception($this->_helper->translate('Invalid Request'));
      if ($id == null)
      throw new Exception($this->_helper->translate('Invalid Id'));

      $data = array();

      if($profileId == BBBManager_Config_Defines::$ROOM_ADMINISTRATOR_PROFILE) {
      $data['group_admin_local'] = $this->_request->getPost('group_admin_local', '');
      $data['group_admin_ldap'] = $this->_request->getPost('group_admin_ldap', '');
      $data['group_moderator_local'] = $this->_request->getPost('group_moderator_local', '');
      $data['group_moderator_ldap'] = $this->_request->getPost('group_moderator_ldap', '');
      $data['user_admin_local'] = $this->_request->getPost('user_admin_local', '');
      $data['user_admin_ldap'] = $this->_request->getPost('user_admin_ldap', '');
      $data['user_moderator_local'] = $this->_request->getPost('user_moderator_local', '');
      $data['user_moderator_ldap'] = $this->_request->getPost('user_moderator_ldap', '');
      }

      if(in_array($profileId, array(BBBManager_Config_Defines::$ROOM_ADMINISTRATOR_PROFILE, BBBManager_Config_Defines::$ROOM_MODERATOR_PROFILE))){
      $data['group_presenter_local'] = $this->_request->getPost('group_presenter_local', '');
      $data['group_presenter_ldap'] = $this->_request->getPost('group_presenter_ldap', '');
      $data['group_attendee_local'] = $this->_request->getPost('group_attendee_local', '');
      $data['group_attendee_ldap'] = $this->_request->getPost('group_attendee_ldap', '');
      $data['user_presenter_local'] = $this->_request->getPost('user_presenter_local', '');
      $data['user_presenter_ldap'] = $this->_request->getPost('user_presenter_ldap', '');
      $data['user_attendee_local'] = $this->_request->getPost('user_attendee_local', '');
      $data['user_attendee_ldap'] = $this->_request->getPost('user_attendee_ldap', '');
      }

      $arrRestResponse = IMDT_Util_Rest::put('/api/rooms/' . $id, $data);

      $objResponse->success = '1';
      $objResponse->msg = $arrRestResponse['msg'];
      $objResponse->redirect = $this->view->url(array('action' => 'index'));
      } catch (IMDT_Controller_Exception_InvalidToken $e1) {
      $objResponse->success = '-1';
      $objResponse->msg = '';
      } catch (Exception $e) {
      $objResponse->success = '0';
      $objResponse->msg = $e->getMessage();
      }

      $this->_helper->json($objResponse);
      }
     * *
     */
}