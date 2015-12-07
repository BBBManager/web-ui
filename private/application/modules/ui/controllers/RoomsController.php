<?php

class Ui_RoomsController extends IMDT_Controller_Abstract {

    public function init() {
        $this->filters = array();
        $this->filters['name'] = array('name' => 'name', 'label' => $this->_helper->translate('column-meeting_room-name'), 'type' => 'text');
        $this->filters['date_start'] = array('name' => 'date_start', 'label' => $this->_helper->translate('column-meeting_room-date_start'), 'type' => 'datetime');
        $this->filters['date_end'] = array('name' => 'date_end', 'label' => $this->_helper->translate('column-meeting_room-date_end'), 'type' => 'datetime');
        $this->filters['timezone'] = array('name' => 'timezone', 'label' => $this->_helper->translate('column-meeting_room-timezone'), 'type' => 'combo', 'source' => 'timezone');
        $this->filters['privacy_policy'] = array('name' => 'privacy_policy', 'label' => $this->_helper->translate('column-meeting_room-privacy_policy'), 'type' => 'combo', 'source' => 'privacy_policy');
        $this->filters['url'] = array('name' => 'url', 'label' => $this->_helper->translate('column-meeting_room-url'), 'type' => 'text');
        $this->filters['encrypted'] = array('name' => 'encrypted', 'label' => $this->_helper->translate('column-meeting_room-encrypted'), 'type' => 'boolean');
        $this->filters['record'] = array('name' => 'record', 'label' => $this->_helper->translate('column-meeting_room-record'), 'type' => 'boolean');
        $this->filters['has_recordings'] = array('name' => 'has_recordings', 'label' => $this->_helper->translate('meeting_room-has_recordings'), 'type' => 'boolean');
        $this->filters['participants_limit'] = array('name' => 'participants_limit', 'label' => $this->_helper->translate('column-meeting_room-participants_limit'), 'type' => 'integer');
        $this->filters['meeting_room_category_id'] = array('name' => 'meeting_room_category_id', 'label' => $this->_helper->translate('column-meeting_room-category'), 'type' => 'combo', 'source' => 'categories');

        $arrBbbOptions['meeting_mute_on_start'] = array('name' => 'meeting_mute_on_start', 'label' => $this->_helper->translate('column-meeting_room-meeting_mute_on_start'), 'type' => 'boolean');
        $arrBbbOptions['meeting_lock_on_start'] = array('name' => 'meeting_lock_on_start', 'label' => $this->_helper->translate('column-meeting_room-meeting_lock_on_start'), 'type' => 'boolean');
        $arrBbbOptions['lock_disable_public_chat_for_locked_users'] = array('name' => 'lock_disable_public_chat_for_locked_users', 'label' => $this->_helper->translate('column-meeting_room-lock_disable_public_chat_for_locked_users'), 'type' => 'boolean');
        $arrBbbOptions['lock_disable_private_chat_for_locked_users'] = array('name' => 'lock_disable_private_chat_for_locked_users', 'label' => $this->_helper->translate('column-meeting_room-lock_disable_private_chat_for_locked_users'), 'type' => 'boolean');
        $arrBbbOptions['lock_layout_for_locked_users'] = array('name' => 'lock_layout_for_locked_users', 'label' => $this->_helper->translate('column-meeting_room-lock_layout_for_locked_users'), 'type' => 'boolean');
        $this->filters['blocking_options'] = array('name' => 'admin', 'label' => $this->_helper->translate('column-meeting_room-blocking_options'), 'type' => 'optgroup', 'options' => $arrBbbOptions);

        $arrAdminOptions['user_admin'] = array('name' => 'user_admin', 'label' => $this->_helper->translate('column-meeting_room-user_admin'), 'type' => 'combo-remote', 'url' => array('controller' => 'users', 'action' => 'remote-search'));
        $arrAdminOptions['user_admin_login'] = array('name' => 'user_admin_login', 'label' => $this->_helper->translate('column-meeting_room-user_admin_login'), 'type' => 'text');
        $arrAdminOptions['user_admin_name'] = array('name' => 'user_admin_name', 'label' => $this->_helper->translate('column-meeting_room-user_admin_name'), 'type' => 'text');
        $arrAdminOptions['user_admin_auth_mode'] = array('name' => 'user_admin_auth_mode', 'label' => $this->_helper->translate('column-meeting_room-user_admin_auth_mode'), 'type' => 'combo', 'source' => 'auth_mode');
        $arrAdminOptions['group_admin'] = array('name' => 'group_admin', 'label' => $this->_helper->translate('column-meeting_room-group_admin'), 'type' => 'combo', 'source' => 'group');
        $arrAdminOptions['group_admin_name'] = array('name' => 'group_admin_name', 'label' => $this->_helper->translate('column-meeting_room-group_admin_name'), 'type' => 'text');
        $arrAdminOptions['group_admin_auth_mode'] = array('name' => 'group_admin_auth_mode', 'label' => $this->_helper->translate('column-meeting_room-group_admin_auth_mode'), 'type' => 'combo', 'source' => 'auth_mode');
        $this->filters['admin'] = array('name' => 'admin', 'label' => $this->_helper->translate('column-meeting_room-admin'), 'type' => 'optgroup', 'options' => $arrAdminOptions);

        $arrModeratorOptions['user_moderator'] = array('name' => 'user_moderator', 'label' => $this->_helper->translate('column-meeting_room-user_moderator'), 'type' => 'combo-remote', 'url' => array('controller' => 'users', 'action' => 'remote-search'));
        $arrModeratorOptions['user_moderator_login'] = array('name' => 'user_moderator_login', 'label' => $this->_helper->translate('column-meeting_room-user_moderator_login'), 'type' => 'text');
        $arrModeratorOptions['user_moderator_name'] = array('name' => 'user_moderator_name', 'label' => $this->_helper->translate('column-meeting_room-user_moderator_name'), 'type' => 'text');
        $arrModeratorOptions['user_moderator_auth_mode'] = array('name' => 'user_moderator_auth_mode', 'label' => $this->_helper->translate('column-meeting_room-user_moderator_auth_mode'), 'type' => 'combo', 'source' => 'auth_mode');
        $arrModeratorOptions['group_moderator'] = array('name' => 'group_moderator', 'label' => $this->_helper->translate('column-meeting_room-group_moderator'), 'type' => 'combo', 'source' => 'group');
        $arrModeratorOptions['group_moderator_name'] = array('name' => 'group_moderator_name', 'label' => $this->_helper->translate('column-meeting_room-group_moderator_name'), 'type' => 'text');
        $arrModeratorOptions['group_moderator_auth_mode'] = array('name' => 'group_moderator_auth_mode', 'label' => $this->_helper->translate('column-meeting_room-group_moderator_auth_mode'), 'type' => 'combo', 'source' => 'auth_mode');
        $this->filters['moderator'] = array('name' => 'moderator', 'label' => $this->_helper->translate('column-meeting_room-moderator'), 'type' => 'optgroup', 'options' => $arrModeratorOptions);

        $arrPresenterOptions['user_presenter'] = array('name' => 'user_presenter', 'label' => $this->_helper->translate('column-meeting_room-user_presenter'), 'type' => 'combo-remote', 'url' => array('controller' => 'users', 'action' => 'remote-search'));
        $arrPresenterOptions['user_presenter_login'] = array('name' => 'user_presenter_login', 'label' => $this->_helper->translate('column-meeting_room-user_presenter_login'), 'type' => 'text');
        $arrPresenterOptions['user_presenter_name'] = array('name' => 'user_presenter_name', 'label' => $this->_helper->translate('column-meeting_room-user_presenter_name'), 'type' => 'text');
        $arrPresenterOptions['user_presenter_auth_mode'] = array('name' => 'user_presenter_auth_mode', 'label' => $this->_helper->translate('column-meeting_room-user_presenter_auth_mode'), 'type' => 'combo', 'source' => 'auth_mode');
        $arrPresenterOptions['group_presenter'] = array('name' => 'group_presenter', 'label' => $this->_helper->translate('column-meeting_room-group_presenter'), 'type' => 'combo', 'source' => 'group');
        $arrPresenterOptions['group_presenter_name'] = array('name' => 'group_presenter_name', 'label' => $this->_helper->translate('column-meeting_room-group_presenter_name'), 'type' => 'text');
        $arrPresenterOptions['group_presenter_auth_mode'] = array('name' => 'group_presenter_auth_mode', 'label' => $this->_helper->translate('column-meeting_room-group_presenter_auth_mode'), 'type' => 'combo', 'source' => 'auth_mode');
        $this->filters['presenter'] = array('name' => 'presenter', 'label' => $this->_helper->translate('column-meeting_room-presenter'), 'type' => 'optgroup', 'options' => $arrPresenterOptions);

        $arrAttendeeOptions['user_attendee'] = array('name' => 'user_attendee', 'label' => $this->_helper->translate('column-meeting_room-user_attendee'), 'type' => 'combo-remote', 'url' => array('controller' => 'users', 'action' => 'remote-search'));
        $arrAttendeeOptions['user_attendee_login'] = array('name' => 'user_attendee_login', 'label' => $this->_helper->translate('column-meeting_room-user_attendee_login'), 'type' => 'text');
        $arrAttendeeOptions['user_attendee_name'] = array('name' => 'user_attendee_name', 'label' => $this->_helper->translate('column-meeting_room-user_attendee_name'), 'type' => 'text');
        $arrAttendeeOptions['user_attendee_auth_mode'] = array('name' => 'user_attendee_auth_mode', 'label' => $this->_helper->translate('column-meeting_room-user_attendee_auth_mode'), 'type' => 'combo', 'source' => 'auth_mode');
        $arrAttendeeOptions['group_attendee'] = array('name' => 'group_attendee', 'label' => $this->_helper->translate('column-meeting_room-group_attendee'), 'type' => 'combo', 'source' => 'group');
        $arrAttendeeOptions['group_attendee_name'] = array('name' => 'group_attendee_name', 'label' => $this->_helper->translate('column-meeting_room-group_attendee_name'), 'type' => 'text');
        $arrAttendeeOptions['group_attendee_auth_mode'] = array('name' => 'group_attendee_auth_mode', 'label' => $this->_helper->translate('column-meeting_room-group_attendee_auth_mode'), 'type' => 'combo', 'source' => 'auth_mode');
        $this->filters['attendee'] = array('name' => 'attendee', 'label' => $this->_helper->translate('column-meeting_room-attendee'), 'type' => 'optgroup', 'options' => $arrAttendeeOptions);

        //$this->view->mainFilters = array();
        //$this->view->mainFilters['main_date_start'] = array('name'=>'date_start', 'label'=>$this->_helper->translate('column-access_log-create_date'), 'type'=>'datetime', 'condition'=>'b');
        $this->filters['main_date_start'] = array('main' => true, 'name' => 'date_start', 'label' => $this->_helper->translate('column-access_log-create_date'), 'type' => 'datetime', 'condition' => 'b');

        /*
          Permissões - Administradores
          -Usuário (Lista)-> abre seleção dos usuários possíveis
          -Usuário (Login)-> Texto
          -Usuário (Nome) -> Texto
          -Usuário (Tipo) -> abre combo com "LDAP" e "local"
          -Grupo (Lista)  -> abre seleção dos grupos possíveis
          -Grupo (Nome)   -> Texto
          -Grupo (Tipo)   -> abre combo com "LDAP" e "local"

          /*
          Filtros a utilizar:
          - Nome : texto
          - Hora de Início : data/hora
          - Hora de Fim : data/hora
          - Fuso Horário : combo
          - Tipo da Sala : combo ("Apenas convidados", "Apenas usuários autenticados", "Pública")
          - URL da sala : texto
          - Quantidade Máxima de Participantes : inteiro
          - Permissões - Administradores (usuários)  : usuários
          - Permissões - Administradores (grupos)  : grupos
          - Permissões - Moderadores (usuários)  : usuários
          - Permissões - Moderadores (grupos)  : grupos
          - Permissões - Palestrantes (usuários)  : usuários
          - Permissões - Palestrantes (grupos)  : grupos
          - Permissões - Participantes (usuários)  : usuários
          - Permissões - Participantes (grupos)  : grupos
          - Observações : texto
         */


        /*
          group_local
          group_ldap
          user_local
          user_ldap
         */

        //default

        $this->api = 'rooms';
        $this->pkey = 'meeting_room_id';

        $this->logsFilters = array();
        $this->logsFilters['meeting_room_id'] = array('name' => 'meeting_room_id', 'label' => $this->_helper->translate('column-meeting_room_log-meeting_room_id'), 'type' => 'text', 'value' => null);
        $this->logsFilters['user'] = array('name' => 'user', 'label' => $this->_helper->translate('column-user-name'), 'type' => 'combo', 'source' => 'user');
        $this->logsFilters['user_name'] = array('name' => 'user_name', 'label' => $this->_helper->translate('column-meeting_room_log-user_name'), 'type' => 'text');
        $this->logsFilters['user_login'] = array('name' => 'user_login', 'label' => $this->_helper->translate('column-meeting_room_log-user_login'), 'type' => 'text');
        $this->logsFilters['meeting_room_action_id'] = array('name' => 'meeting_room_action_id', 'label' => $this->_helper->translate('column-meeting_room_action-name'), 'type' => 'combo', 'source' => 'action');
        $this->logsFilters['create_date'] = array('name' => 'create_date', 'label' => $this->_helper->translate('column-meeting_room_log-create_date'), 'type' => 'datetime', 'value' => null);
        $this->logsFilters['ip_address'] = array('name' => 'ip_address', 'label' => $this->_helper->translate('column-meeting_room_log-ip_address'), 'type' => 'text');
        $this->view->hasFilters = false;
    }

    public function indexAction() {
        $this->view->tableSource = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'table-content');
        $this->view->uriPage = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => $this->_request->getActionName());
        $this->view->uriExport = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'export');
        $this->view->uriExportPdf = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'export-pdf');


        /*
          if (!$this->_request->getParam('date_start')) {
          $this->_request->setParam('date_start', IMDT_Util_Date::filterDatetimeToCurrentLang(date('Y-m-d H:i', strtotime('-30 days'))));
          $this->_request->setParam('date_start_2', IMDT_Util_Date::filterDatetimeToCurrentLang(date('Y-m-d H:i', strtotime('+30 days'))));
          $this->_request->setParam('date_start_c', 'b');
          }
         */

        //Set defaults
        $existsDateStart = false;
        $q = $this->_request->getParam('q', array());
        if (count($q) > 0) {
            foreach ($q as $currQuery)
                if ($currQuery['n'] == 'main_date_start')
                    $existsDateStart = true;
        }
        if ($existsDateStart == false) {
            $q[] = array('n' => 'main_date_start', 'c' => 'b', 'v' => IMDT_Util_Date::filterDatetimeToCurrentLang(date('Y-m-d H:i', strtotime('-30 days'))), 'u' => IMDT_Util_Date::filterDatetimeToCurrentLang(date('Y-m-d H:i', strtotime('+30 days'))));
            $this->_request->setParam('q', $q);
        }



        $params = $this->_request->getParam('q');

        $this->view->parameters = IMDT_Util_Url::getThisParams($this->filters);

        $this->view->parametersString = http_build_query($this->view->parameters);

        $this->view->filters = $this->filters;

        //debug($this->view->parameters);

        $this->view->hasFilters = ((isset($this->view->parameters['q']) && (count($this->view->parameters['q']) > 0)) ? true : false);
    }

    public function exportAction() {
        $this->_disableViewAndLayout();

        $params = IMDT_Util_Url::getThisParams($this->filters);
        $params['export'] = 'csv';

        $headers = array();
        $headers['columns-leach'] = 'meeting_room_id,name,date_start,date_end,timezone,encrypted,privacy_policy,url,participants_limit,record,group_admin_local,group_admin_ldap,group_moderator_local,group_moderator_ldap,group_presenter_local,';
        $headers['columns-leach'] .= 'group_presenter_ldap,group_attendee_local,group_attendee_ldap,user_admin_local,user_admin_ldap,user_moderator_local,user_moderator_ldap,user_presenter_local,user_presenter_ldap,user_attendee_local,user_attendee_ldap,';
        $headers['columns-leach'] .= 'meeting_mute_on_start,meeting_lock_on_start,lock_disable_mic_for_locked_users,lock_disable_cam_for_locked_users,lock_disable_public_chat_for_locked_users,lock_disable_private_chat_for_locked_users,lock_layout_for_locked_users';

        // $headers['columns-desc'] = $this->_helper->translate('column-meeting_room-id');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-name');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-date_start');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-date_end');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-timezone');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-encrypted');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-privacy_policy');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-url');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-participants_limit');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-record');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-group_admin_local');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-group_admin_ldap');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-group_moderator_local');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-group_moderator_ldap');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-group_presenter_local');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-group_presenter_ldap');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-group_attendee_local');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-group_attendee_ldap');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-user_admin_local');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-user_admin_ldap');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-user_moderator_local');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-user_moderator_ldap');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-user_presenter_local');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-user_presenter_ldap');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-user_attendee_local');
        // $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-user_attendee_ldap');

        $response = IMDT_Util_Rest::get('/api/rooms.json', $params, $headers);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        if ($this->_request->getParam('utf8', 0)) {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_CSV . '; charset=utf-8');
        } else {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_CSV);
        }
        header('Content-Disposition: attachment; filename="' . $this->_request->getControllerName() . '.csv"');
        echo file_get_contents($response['url']);
        exit;
    }

    public function exportPdfAction() {
        $this->_disableViewAndLayout();

        $params = IMDT_Util_Url::getThisParams($this->filters);
        $params['export'] = 'pdf';
        $params['pdf-title'] = $this->_helper->translate('Meeting Rooms Management');

        $headers = array();
        $headers['columns-leach'] = 'name,date_start,date_end';
        $headers['columns-desc'] = $this->_helper->translate('column-meeting_room-name');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-date_start');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room-date_end');

        $headers['columns-format'] = 'null';
        $headers['columns-format'] .= ',' . 'datetime-no-seconds';
        $headers['columns-format'] .= ',' . 'datetime-no-seconds';

        $response = IMDT_Util_Rest::get('/api/rooms.json', $params, $headers);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        if ($this->_request->getParam('utf8', 0)) {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_PDF . '; charset=utf-8');
        } else {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_PDF);
        }
        header('Set-Cookie: fileDownload=true; path=/');
        header('Content-Disposition: attachment; filename="' . $this->_request->getControllerName() . '.pdf"');
        echo file_get_contents($response['url']);
        exit;
    }

    public function editAction() {
        $response = IMDT_Util_Rest::get('/api/my-rooms', array('meeting_room_id' => $this->_getParam('id', -1)));

        if (!isset($response['collection'][$this->_getParam('id', -1)])) {
            throw new Exception($this->_helper->translate('Nenhuma sala encontrada para o id informado.'));
        }

        $response = IMDT_Util_Rest::get('/api/categories');
        $categories = $response['collection'];
        $this->view->categories = $categories;

        $this->view->id = $this->_getParam('id', null);
        $this->view->title = $this->_helper->translate('Edit');
    }

    public function manageAttendeesAction() {
        $this->_skipAcl();

        $this->view->id = $this->_getParam('id', null);
        $this->view->title = $this->_helper->translate('Manage attendees');

        $response = IMDT_Util_Rest::get('/api/my-rooms', array('meeting_room_id' => $this->view->id));

        if (!isset($response['collection'][$this->view->id])) {
            throw new Exception('Nenhuma sala encontrada para o id informado.');
        }

        $room = $response['collection'][$this->view->id];
        $this->view->profile = $room['user_profile_in_meeting'];

        $response = IMDT_Util_Rest::get('/api/categories');
        $categories = $response['collection'];
        $this->view->categories = $categories;
    }

    public function formManageAttendeesPostAction() {
        $this->_skipAcl();

        $objResponse = new stdClass();

        try {
            $id = $this->_request->getParam('id', null);

            $response = IMDT_Util_Rest::get('/api/my-rooms', array('meeting_room_id' => $this->view->id));

            $room = $response['collection'][$id];

            $profileId = $room['user_profile_in_meeting'];

            if (!$this->_request->isPost())
                throw new Exception($this->_helper->translate('Invalid Request'));
            if ($id == null)
                throw new Exception($this->_helper->translate('Invalid Id'));

            $data = array();

            if ($profileId == BBBManager_Config_Defines::$ROOM_ADMINISTRATOR_PROFILE) {
                $data['group_admin_local'] = $this->_request->getPost('group_admin_local', '');
                $data['group_admin_ldap'] = $this->_request->getPost('group_admin_ldap', '');
                $data['group_moderator_local'] = $this->_request->getPost('group_moderator_local', '');
                $data['group_moderator_ldap'] = $this->_request->getPost('group_moderator_ldap', '');
                $data['user_admin_local'] = $this->_request->getPost('user_admin_local', '');
                $data['user_admin_ldap'] = $this->_request->getPost('user_admin_ldap', '');
                $data['user_moderator_local'] = $this->_request->getPost('user_moderator_local', '');
                $data['user_moderator_ldap'] = $this->_request->getPost('user_moderator_ldap', '');
            }

            if (in_array($profileId, array(BBBManager_Config_Defines::$ROOM_ADMINISTRATOR_PROFILE, BBBManager_Config_Defines::$ROOM_MODERATOR_PROFILE))) {
                $data['group_presenter_local'] = $this->_request->getPost('group_presenter_local', '');
                $data['group_presenter_ldap'] = $this->_request->getPost('group_presenter_ldap', '');
                $data['group_attendee_local'] = $this->_request->getPost('group_attendee_local', '');
                $data['group_attendee_ldap'] = $this->_request->getPost('group_attendee_ldap', '');
                $data['user_presenter_local'] = $this->_request->getPost('user_presenter_local', '');
                $data['user_presenter_ldap'] = $this->_request->getPost('user_presenter_ldap', '');
                $data['user_attendee_local'] = $this->_request->getPost('user_attendee_local', '');
                $data['user_attendee_ldap'] = $this->_request->getPost('user_attendee_ldap', '');
            }

            if ($this->_request->getPost('category_id')) {
                $data['meeting_room_category_id'] = $this->_request->getPost('category_id');
            }

            $arrRestResponse = IMDT_Util_Rest::put('/api/rooms/' . $id, $data);

            $objResponse->success = '1';
            $objResponse->msg = $arrRestResponse['msg'];
            $objResponse->redirect = $this->view->url(array('action' => 'index', 'controller' => 'my-rooms'));
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

    public function viewAction() {
        $this->view->id = $this->_getParam('id', null);
        $this->view->logsFilters = $this->logsFilters;

        $this->view->jQuery()->addOnload('CKEDITOR.replace("body");');
        $this->view->jQuery()->addOnload('$("#invite_template_id").on("change", function(e) {
                                            var jsondata = $("#invite_template_id").select2("data");
                                            $(\'input#subject\').val(jsondata.subject);
                                            CKEDITOR.instances[\'body\'].setData(jsondata.body);
                                            });
                                         ');
    }

    public function newAction() {
        try {
            $response = IMDT_Util_Rest::get('/api/categories');
            $categories = $response['collection'];
            $this->view->categories = $categories;
        } catch (Exception $ex) {
            
        }
        $this->view->id = 'new';
        $this->view->title = $this->_helper->translate('New');
        $this->_helper->viewRenderer('edit');
    }

    public function tableContentAction() {
        $objResponse = new stdClass();

        try {
            $params = IMDT_Util_Url::getThisParams($this->filters);
            $response = IMDT_Util_Rest::get('/api/rooms.json', $params);

            $arrTable = array();

            foreach ($response['collection'] as $curr) {
                $row = array();
                if ($curr['_removable'] == '1') {
                    $row[] = '<input name="deleteRow[]" class="cboxSelectRow" type="checkbox" value="' . $curr[$this->pkey] . '" />';
                } else {
                    $row[] = '<input class="cboxSelectRow" type="checkbox" disabled="disabled" />';
                }
                $row[] = $curr['name'];
                $row[] = IMDT_Util_Date::filterDatetimeToCurrentLang($curr['date_start'], false);
                $row[] = IMDT_Util_Date::filterDatetimeToCurrentLang($curr['date_end'], false);
                $actions = '<td nowrap="nowrap">';
                $actions .= '<a title="' . $this->_helper->translate('View') . '" data-toggle="tooltip" class="btn btn-mini" href="/ui/rooms/view/id/' . $curr[$this->pkey] . '" data-original-title="' . $this->_helper->translate('View') . '"><i class="icon-eye-open"></i></a>';
                if ($curr['_editable'] == '1') {
                    $actions .= '<a title="' . $this->_helper->translate('Edit') . '" data-toggle="tooltip" class="btn btn-mini" href="/ui/rooms/edit/id/' . $curr[$this->pkey] . '" data-original-title="' . $this->_helper->translate('Edit') . '"><i class="icon-pencil"></i></a>';
                } else {
                    $actions .= '<a title="' . $this->_helper->translate('Edit') . '" data-toggle="tooltip" class="btn btn-mini" disabled="disabled" data-original-title="' . $this->_helper->translate('Edit') . '"><i class="icon-pencil"></i></a>';
                }

                if (IMDT_Util_Acl::getInstance()->isAllowed('rooms', 'duplicate')) {
                    $actions .= '<a title="' . $this->_helper->translate('Duplicate') . '" data-toggle="tooltip" class="btn btn-mini" onClick="duplicateRow({duplicated_id : ' . $curr[$this->pkey] . ', name : \'Copy of ' . str_replace('\'', '\\\'', $curr['name']) . '\', url : \'' . $curr['url'] . '\'});"><i class="icon-file"></i></a>';
                } else {
                    $actions .= '<a title="' . $this->_helper->translate('Duplicate') . '" data-toggle="tooltip" class="btn btn-mini" disabled="disabled"><i class="icon-file"></i></a>';
                }

                if ($curr['_removable'] == '1') {
                    $actions .= '<a title="' . $this->_helper->translate('Delete') . '" data-toggle="tooltip" class="btn btn-mini btn-delete" href="" data-original-title="Excluir" onclick="confirmMeetingDelete(\'#records\',\'row_' . $curr[$this->pkey] . '\',\'/ui/rooms/delete/id/' . $curr[$this->pkey] . '\',' . $curr['recordings_count'] . '); return false;"><i class="icon-trash"></i></a>';
                } else {
                    $actions .= '<a title="' . $this->_helper->translate('Delete') . '" data-toggle="tooltip" class="btn btn-mini btn-delete" disabled="disabled" data-original-title="Excluir"><i class="icon-trash"></i></a>';
                }

                if ($curr['status'] == BBBManager_Config_Defines::$ROOM_CLOSED) {
                    $actions .= '<a title="' . $this->_helper->translate('History') . '" data-toggle="tooltip" class="btn btn-mini" href="/ui/rooms/history/id/' . $curr[$this->pkey] . '" data-original-title="' . $this->_helper->translate('History') . '"><i class="icon-list-alt"></i></a>';
                }

                if ($curr['recordings_count'] > 0) {
                    $actions .= '<a title="' . $this->_helper->translate('Manage Recording') . '" data-toggle="tooltip" class="btn btn-mini" href="/ui/rooms/manage-recording/id/' . $curr[$this->pkey] . '" data-original-title="' . $this->_helper->translate('Manage Recording') . '"><i class="icon-facetime-video"></i></a>';
                }

                $actions .= '</td>';
                $row[] = $actions;
                $row['DT_RowId'] = 'row_' . $curr[$this->pkey];
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

    public function formContentAction() {
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

                $arrForm['url'] = uniqid();

                //Default bbb options
                $arrRestResponse = IMDT_Util_Rest::get('/api/bbb-configs.json');
                $arrBbbConfigs = $arrRestResponse['collection'];
                $arrForm['meeting_mute_on_start'] = $arrBbbConfigs['meetingMuteOnStart'];
                $arrForm['meeting_lock_on_start'] = $arrBbbConfigs['meetingLockOnStart'];
                $arrForm['lock_disable_mic_for_locked_users'] = $arrBbbConfigs['lockDisableMicForLockedUsers'];
                $arrForm['lock_disable_cam_for_locked_users'] = $arrBbbConfigs['lockDisableCamForLockedUsers'];
                $arrForm['lock_disable_public_chat_for_locked_users'] = $arrBbbConfigs['lockDisablePublicChatForLockedUsers'];
                $arrForm['lock_disable_private_chat_for_locked_users'] = $arrBbbConfigs['lockDisablePrivateChatForLockedUsers'];
                $arrForm['lock_layout_for_locked_users'] = $arrBbbConfigs['lockLayoutForLockedUsers'];


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
                $arrForm['observations'] = $row['observations'];
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
                $arrForm['meeting_mute_on_start'] = $row['meeting_mute_on_start'];
                $arrForm['meeting_lock_on_start'] = $row['meeting_lock_on_start'];
                $arrForm['lock_disable_mic_for_locked_users'] = $row['lock_disable_mic_for_locked_users'];
                $arrForm['lock_disable_cam_for_locked_users'] = $row['lock_disable_cam_for_locked_users'];
                $arrForm['lock_disable_public_chat_for_locked_users'] = $row['lock_disable_public_chat_for_locked_users'];
                $arrForm['lock_disable_private_chat_for_locked_users'] = $row['lock_disable_private_chat_for_locked_users'];
                $arrForm['lock_layout_for_locked_users'] = $row['lock_layout_for_locked_users'];
                $arrForm['category_id'] = $row['meeting_room_category_id'];
                $arrForm['category_name'] = $row['meeting_room_category_name'];

                $objResponse->form = $arrForm;
            }

            $arrSelect2 = array('group_local' => array(), 'group_ldap' => array(), 'user_local' => array(), 'user_ldap' => array(), 'timezone' => array());


            $arrRestResponse = IMDT_Util_Rest::get('/api/timezones.json', array(array('columns-leach' => 'id,name,default')));
            foreach ($arrRestResponse['collection'] as $obj) {
                $nRow = array();
                $nRow['id'] = $obj['id'];
                $nRow['text'] = $obj['name'];

                $arrSelect2['timezone'][] = $nRow;

                if ($id == 'new' && $obj['default']) {
                    $objResponse->form['timezone'] = $nRow['id'];
                }
            }

            $collectionGroups = IMDT_Util_Rest::get('/api/groups.json');
            foreach ($collectionGroups['collection'] as $obj) {
                $nRow = array();
                $nRow['id'] = $obj['group_id'];
                $nRow['text'] = $obj['name'];

                if ($obj['auth_mode_id'] == BBBManager_Config_Defines::$LOCAL_AUTH_MODE) {// Local
                    $arrSelect2['group_local'][] = $nRow;
                } elseif ($obj['auth_mode_id'] == BBBManager_Config_Defines::$LDAP_AUTH_MODE && $obj['visible'] == true) { //Ldap
                    $arrSelect2['group_ldap'][] = $nRow;
                }
            }


            //user ldap is being done remotly

            $collectionUsers = IMDT_Util_Rest::get('/api/users.json', array('auth_mode_id' => BBBManager_Config_Defines::$LOCAL_AUTH_MODE));
            foreach ($collectionUsers['collection'] as $obj) {
                $nRow = array();
                $nRow['id'] = $obj['user_id'];
                $nRow['text'] = $obj['name'];
                //$nRow['auth_mode_id'] = $obj['auth_mode_id'];
                $arrSelect2['user_local'][] = $nRow;
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

    public function formPostAction() {
        $objResponse = new stdClass();

        try {
            $id = $this->_request->getParam('id', null);

            if (!$this->_request->isPost())
                throw new Exception($this->_helper->translate('Invalid Request'));
            if ($id == null)
                throw new Exception($this->_helper->translate('Invalid Id'));

            $data = array();
            $data['name'] = $this->_request->getPost('name', null);
            $data['date_start'] = IMDT_Util_Date::filterDatetimeToApi($this->_request->getPost('date_start', ''));
            $data['date_end'] = IMDT_Util_Date::filterDatetimeToApi($this->_request->getPost('date_end', ''));
            $data['timezone'] = $this->_request->getPost('timezone', '');
            $data['encrypted'] = $this->_request->getPost('encrypted', '');
            $data['privacy_policy'] = $this->_request->getPost('privacy_policy', '');
            $data['record'] = $this->_request->getPost('record', '');
            $data['url'] = $this->_request->getPost('url', '');
            $data['observations'] = $this->_request->getPost('observations', null);
            $data['meeting_mute_on_start'] = $this->_request->getPost('meeting_mute_on_start', '0');
            $data['meeting_lock_on_start'] = $this->_request->getPost('meeting_lock_on_start', '0');
            $data['lock_disable_mic_for_locked_users'] = $this->_request->getPost('lock_disable_mic_for_locked_users', '0');
            $data['lock_disable_cam_for_locked_users'] = $this->_request->getPost('lock_disable_cam_for_locked_users', '0');
            $data['lock_disable_public_chat_for_locked_users'] = $this->_request->getPost('lock_disable_public_chat_for_locked_users', '0');
            $data['lock_disable_private_chat_for_locked_users'] = $this->_request->getPost('lock_disable_private_chat_for_locked_users', '0');
            $data['lock_layout_for_locked_users'] = $this->_request->getPost('lock_layout_for_locked_users', '0');
            $data['participants_limit'] = $this->_request->getPost('participants_limit', '');
            $data['group_admin_local'] = $this->_request->getPost('group_admin_local', '');
            $data['group_admin_ldap'] = $this->_request->getPost('group_admin_ldap', '');
            $data['group_moderator_local'] = $this->_request->getPost('group_moderator_local', '');
            $data['group_moderator_ldap'] = $this->_request->getPost('group_moderator_ldap', '');
            $data['group_presenter_local'] = $this->_request->getPost('group_presenter_local', '');
            $data['group_presenter_ldap'] = $this->_request->getPost('group_presenter_ldap', '');
            $data['group_attendee_local'] = $this->_request->getPost('group_attendee_local', '');
            $data['group_attendee_ldap'] = $this->_request->getPost('group_attendee_ldap', '');
            $data['user_admin_local'] = $this->_request->getPost('user_admin_local', '');
            $data['user_admin_ldap'] = $this->_request->getPost('user_admin_ldap', '');
            $data['user_moderator_local'] = $this->_request->getPost('user_moderator_local', '');
            $data['user_moderator_ldap'] = $this->_request->getPost('user_moderator_ldap', '');
            $data['user_presenter_local'] = $this->_request->getPost('user_presenter_local', '');
            $data['user_presenter_ldap'] = $this->_request->getPost('user_presenter_ldap', '');
            $data['user_attendee_local'] = $this->_request->getPost('user_attendee_local', '');
            $data['user_attendee_ldap'] = $this->_request->getPost('user_attendee_ldap', '');

            if ($this->_request->getPost('category_id')) {
                $data['meeting_room_category_id'] = $this->_request->getPost('category_id');
            }

            if ($id == 'new') {
                $arrRestResponse = IMDT_Util_Rest::post('/api/rooms', $data);
            } else {
                $arrRestResponse = IMDT_Util_Rest::put('/api/rooms/' . $id, $data);
            }

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

    public function deleteAction() {
        $objResponse = new stdClass();

        try {
            $id = $this->_request->getParam('id');
            IMDT_Util_Rest::delete('/api/rooms/' . $id . '.json');

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

    public function duplicateAction() {
        $objResponse = new stdClass();

        try {
            $duplicatedRowId = $this->_getParam('duplicated_id');
            $name = $this->_getParam('name');
            $url = $this->_getParam('url');

            $response = IMDT_Util_Rest::get('/api/rooms/' . $duplicatedRowId . '.json');
            unset($response['row']['meeting_room_id']);
            $response['row']['name'] = $name;
            $response['row']['url'] = $url;

            $postResponse = IMDT_Util_Rest::post('/api/rooms.json', $response['row']);

            $objResponse->success = '1';
            $objResponse->msg = '';
            $objResponse->edit_url = $this->view->url(array('action' => 'edit', 'id' => $postResponse['id']));
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

    public function tableLogsAction() {
        $this->_skipAcl();
        $objResponse = new stdClass();

        try {
            $params = IMDT_Util_Url::getThisParams($this->logsFilters);
            $response = IMDT_Util_Rest::get('/api/room-logs.json', $params);

            $arrTable = array();
            foreach ($response['collection'] as $curr) {
                $row = array();
                $row[] = $curr['user_name'];
                $row[] = $curr['meeting_room_action_name'];
                $row[] = IMDT_Util_Date::filterDatetimeToCurrentLang($curr['create_date']);
                $row[] = $curr['ip_address'];
                $row['DT_RowId'] = 'row_' . $curr['id'];
                $arrTable[] = $row;
            }

            $objResponse->success = '1';
            $objResponse->msg = '';
            $objResponse->aaData = $arrTable;
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
            $objResponse->aaData = array();
        }

        $this->_helper->json($objResponse);
    }

    public function logsExportAction() {
        $this->_skipAcl();
        $this->_disableViewAndLayout();

        $filters = array();
        $params = IMDT_Util_Url::getThisParams($this->logsFilters);
        $params['export'] = 'csv';

        $headers = array();
        $headers['columns-leach'] = 'user_name,meeting_room_action_name,create_date,ip_address';
        /* $headers['columns-desc'] = $this->_helper->translate('column-user-name');
          $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room_action-name');
          $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room_log-create_date');
          $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room_log-ip_address'); */


        $response = IMDT_Util_Rest::get('/api/room-logs.json', $params, $headers);
        if ($this->_request->getParam('utf8', 0)) {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_CSV . '; charset=utf-8');
        } else {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_CSV);
        }
        header('Content-Disposition: attachment; filename="room-events-'.$params['meeting_room_id'].'.csv"');
        echo file_get_contents($response['url']);
    }

    public function logsExportPdfAction() {
        $this->_skipAcl();
        $this->_disableViewAndLayout();
        $params = IMDT_Util_Url::getThisParams($this->logsFilters);

        $params['export'] = 'pdf';
        $params['pdf-title'] = $this->_helper->translate('Meeting Rooms Management') . ' - ' . $this->_helper->translate('History') . ' #'.$params['meeting_room_id'];

        $headers = array();
        $headers['columns-leach'] = 'user_name,meeting_room_action_name,create_date,ip_address';
        $headers['columns-desc'] = $this->_helper->translate('column-user-name');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room_action-name');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room_log-create_date');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-meeting_room_log-ip_address');

        $response = IMDT_Util_Rest::get('/api/room-logs.json', $params, $headers);

//        Zend_Debug::dump($params);
//        Zend_Debug::dump($headers);
//        Zend_Debug::dump($response);
//        die;

        if ($this->_request->getParam('utf8', 0)) {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_PDF . '; charset=utf-8');
        } else {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_PDF);
        }
        header('Set-Cookie: fileDownload=true; path=/');
        header('Content-Disposition: attachment; filename="room-events-'.$params['meeting_room_id'].'.pdf"');
        echo file_get_contents($response['url']);
    }

    public function logsReportFilterAction() {
        $this->_skipAcl();
        $objResponse = new stdClass();

        try {
            $arrSelect = array('user' => array(), 'action' => array());

            $id = $this->_request->getParam('id');

            //Users
            $arrRestResponse = IMDT_Util_Rest::get('/api/room-logs.json', array('meeting_room_id' => $id, array('columns-leach' => 'user_id,user_name')));
            $arr = $arrRestResponse['collection'];

            $arrUsers = array();
            foreach ($arr as $obj) {
                $arrUsers[$obj['user_id']] = $obj['user_name'];
            }
            natcasesort($arrUsers);

            $strUsers = '';
            foreach ($arrUsers as $id => $name) {
                $strUsers .= '<option value="' . $id . '">' . $name . '</option>';
            }
            $arrSelect['user'] = $strUsers;

            //Actions
            $arrRestResponse = IMDT_Util_Rest::get('/api/room-actions.json');
            $arr = $arrRestResponse['collection'];
            $strActions = '';
            foreach ($arr as $obj) {
                $strActions .= '<option value="' . $obj['meeting_room_action_id'] . '">' . $obj['name'] . '</option>';
            }
            $arrSelect['action'] = $strActions;

            $objResponse->form = array();
            $objResponse->select = $arrSelect;
            $objResponse->success = '1';
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

    public function formInviteContentAction() {
        $objResponse = new stdClass();
        $this->_skipAcl();

        try {
            $objResponse = new stdClass();
            $id = $this->_request->getParam('id');

            $response = IMDT_Util_Rest::get('/api/room-invites/' . $id . '.json');
            $row = $response['row'];
            $arrForm['subject'] = $row['last_invite_subject'] ? : '';
            $arrForm['body'] = $row['last_invite_body'] ? : '';
            $arrForm['invite_template_id'] = '-1';

            $objResponse->form = $arrForm;
            $arrSelect2 = array('templates' => array());

            $arrLastSent = array();
            $arrLastSent['id'] = '-1';
            $arrLastSent['text'] = $this->_helper->translate('Last sent invitation');
            $arrLastSent['subject'] = $row['last_invite_subject'];
            $arrLastSent['body'] = $row['last_invite_body'];
            $arrSelect2['templates'][] = $arrLastSent;

            $collectionTemplates = IMDT_Util_Rest::get('/api/invite-templates.json');
            foreach ($collectionTemplates['collection'] as $obj) {
                $nRow = array();
                $nRow['id'] = $obj['invite_template_id'];
                $nRow['text'] = $obj['name'];
                $nRow['subject'] = $obj['subject'];
                $nRow['body'] = $obj['body'];

                $arrSelect2['templates'][] = $nRow;
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

    public function formInvitePostAction() {
        $this->_skipAcl();
        $objResponse = new stdClass();

        try {
            $data = array();
            $data['meeting_room_id'] = $this->_getParam('meeting_room_id');
            $data['subject'] = $this->_getParam('subject');
            $data['body'] = $this->_getParam('body');

            if ($this->_getParam('max_rcpt_confirmed', null) != null) {
                $data['max_rcpt_confirmed'] = '1';
            }

            //$response = IMDT_Util_Rest::get('/api/rooms/' . $duplicatedRowId . '.json');
            //unset($response['row']['meeting_room_id']);
            //$response['row']['name'] = $name;
            //$response['row']['url'] = $url;

            $postResponse = IMDT_Util_Rest::post('/api/room-invites.json', $data);

            if ($postResponse['success'] == '1' && isset($postResponse['data']) && isset($postResponse['data']['toCount'])) {
                $objResponse->success = '1';
                $objResponse->msg = $postResponse['msg'];
                $objResponse->mustConfirm = '1';
            } else {
                $objResponse->success = '1';
                $objResponse->msg = $postResponse['msg'];
            }
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

    public function audienceReportAction() {
        $this->_skipAcl();
        $this->_disableViewAndLayout();

        $params = array();
        $params['meeting_room_id'] = $this->_request->getParam('id', null);
        $params['export'] = 'csv';

        $headers = array();
        $headers['columns-leach'] = 'auth_mode,user_id,login,name,online_time,ip_address,date_join,date_left';
        /* $headers['columns-desc'] = $this->_helper->translate('column-auth_mode-name');
          $headers['columns-desc'] .= ',' . $this->_helper->translate('column-user-id');
          $headers['columns-desc'] .= ',' . $this->_helper->translate('column-user-login');
          $headers['columns-desc'] .= ',' . $this->_helper->translate('column-user-name');
          $headers['columns-desc'] .= ',' . $this->_helper->translate('column-audience-online_time');
          $headers['columns-desc'] .= ',' . $this->_helper->translate('column-audience-ip_address');
          $headers['columns-desc'] .= ',' . $this->_helper->translate('column-audience-date_join');
          $headers['columns-desc'] .= ',' . $this->_helper->translate('column-audience-date_left'); */

        $response = IMDT_Util_Rest::get('/api/rooms-audience.json', $params, $headers);
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        header('Content-type: text/csv');
        //header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="room-audience.csv"');
        echo file_get_contents($response['url']);
    }

    public function audienceReportPdfAction() {
        $this->_skipAcl();
        $this->_disableViewAndLayout();

        $params = array();
        $params['meeting_room_id'] = $this->_request->getParam('id', null);
        $params['export'] = 'pdf';

        $headers = array();
        $headers['columns-leach'] = 'auth_mode,user_id,login,name,online_time,ip_address,date_join,date_left';

        $headers['columns-desc'] = $this->_helper->translate('column-auth_mode-name');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-user-id');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-user-login');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-user-name');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-audience-online_time');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-audience-ip_address');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-audience-date_join');
        $headers['columns-desc'] .= ',' . $this->_helper->translate('column-audience-date_left');

        $response = IMDT_Util_Rest::get('/api/rooms-audience.json', $params, $headers);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        if ($this->_request->getParam('utf8', 0)) {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_PDF . '; charset=utf-8');
        } else {
            header('Content-type: ' . BBBManager_Config_Defines::$CONTENT_TYPE_PDF);
        }
        header('Set-Cookie: fileDownload=true; path=/');
        header('Content-Disposition: attachment; filename="room-audience.pdf"');
        echo file_get_contents($response['url']);
        exit;
    }

    public function advSearchFilterAction() {
        $this->_skipAcl();
        $objResponse = new stdClass();

        try {
            $arrSelect = array('privacy_policy' => '', 'timezone' => '', 'auth_mode' => '', 'group' => '', 'user' => '',);
            //$arr = $arrRestResponse['collection'];
            $arr = BBBManager_Config_Defines::getMeetingRoomPrivacyPolicy();
            $strOptions = '';
            foreach ($arr as $iObj => $obj) {
                $strOptions .= '<option value="' . $iObj . '">' . $obj . '</option>';
            }
            $arrSelect['privacy_policy'] = $strOptions;

            $arrRestResponse = IMDT_Util_Rest::get('/api/timezones.json', array(array('columns-leach' => 'id,name')));
            $arr = $arrRestResponse['collection'];
            $strOptions = '';
            foreach ($arr as $obj) {
                $strOptions .= '<option value="' . $obj['id'] . '">' . $obj['name'] . '</option>';
            }
            $arrSelect['timezone'] = $strOptions;

            $arrAuthMode = array();
            $arrAuthMode[BBBManager_Config_Defines::getAuthMode(BBBManager_Config_Defines::$LOCAL_AUTH_MODE)] = array('id' => BBBManager_Config_Defines::$LOCAL_AUTH_MODE, 'name' => BBBManager_Config_Defines::getAuthMode(BBBManager_Config_Defines::$LOCAL_AUTH_MODE));
            $arrAuthMode[BBBManager_Config_Defines::getAuthMode(BBBManager_Config_Defines::$LDAP_AUTH_MODE)] = array('id' => BBBManager_Config_Defines::$LDAP_AUTH_MODE, 'name' => BBBManager_Config_Defines::getAuthMode(BBBManager_Config_Defines::$LDAP_AUTH_MODE));
            $arrAuthMode[BBBManager_Config_Defines::getAuthMode(BBBManager_Config_Defines::$PERSONA_AUTH_MODE)] = array('id' => BBBManager_Config_Defines::$PERSONA_AUTH_MODE, 'name' => BBBManager_Config_Defines::getAuthMode(BBBManager_Config_Defines::$PERSONA_AUTH_MODE));
            ksort($arrAuthMode);

            $strOptions = '';
            foreach ($arrAuthMode as $obj) {
                $strOptions .= '<option value="' . $obj['id'] . '">' . $obj['name'] . '</option>';
            }
            $arrSelect['auth_mode'] = $strOptions;


            $arrRestResponse = IMDT_Util_Rest::get('/api/groups.json', array(array('columns-leach' => 'group_id,name')));
            $arr = $arrRestResponse['collection'];
            $strOptions = '';
            foreach ($arr as $obj) {
                $strOptions .= '<option value="' . $obj['group_id'] . '">' . $obj['name'] . '</option>';
            }
            $arrSelect['group'] = $strOptions;

            $arrRestResponse = IMDT_Util_Rest::get('/api/categories/leaf');
            $arr = $arrRestResponse['collection'];
            $strOptions = '';
            foreach ($arr as $obj) {
                $strOptions .= '<option value="' . $obj['meeting_room_category_id'] . '">' . $obj['path'] . '</option>';
            }
            $arrSelect['categories'] = $strOptions;

            /* via remote query
              $arrRestResponse = IMDT_Util_Rest::get('/api/users.json', array(array('columns-leach' => 'user_id,name')));
              $arr = $arrRestResponse['collection'];
              $strOptions = '';
              foreach ($arr as $obj) {
              $strOptions .= '<option value="'.$obj['user_id'].'">'.$obj['name'].'</option>';
              }
              $arrSelect['user'] = $strOptions;
             * 
             */

            $objResponse->form = array();
            $objResponse->select = $arrSelect;
            $objResponse->success = '1';
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

    public function historyAction() {
        $this->_skipAcl();
        $this->view->id = $this->_getParam('id', null);
        $params = $this->_request->getParam('q');
        $this->view->parameters = IMDT_Util_Url::getThisParams($this->filters);
        $this->view->parametersString = http_build_query($this->view->parameters);

        $this->view->uriExport = array('meeting_room_id' => $this->view->id, 'module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'logs-export');
        $this->view->uriExportPdf = array('meeting_room_id' => $this->view->id,'module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'logs-export-pdf');

        unset($this->logsFilters['user']);
        $this->view->filters = $this->filters;
        $this->view->logsFilters = $this->logsFilters;
    }

    public function importAction() {
        $this->_disableViewAndLayout();
        $objResponse = new stdClass();

        try {
            $csvFile = $this->_getParam('files', null);

            if ($csvFile == null) {
                throw new Exception(IMDT_Util_Translate::_('You must provide a CSV file'));
            }

            $csvFileUrl = current($csvFile);
            $decodedCsvFileUrl = urldecode($csvFileUrl);
            $csvFileRelativeToDocumentRootPath = str_replace(IMDT_Util_Config::getInstance()->get('web_base_url'), '', $decodedCsvFileUrl);
            $csvFileFullPath = realpath($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $csvFileRelativeToDocumentRootPath);

            if ($csvFileFullPath == false) {
                throw new Exception(IMDT_Util_Translate::_('CSV file not found'));
            }

            $csvFileContents = file_get_contents($csvFileFullPath);

            $data = array(
                'file-contents' => utf8_encode($csvFileContents)
            );

            $arrRestResponse = IMDT_Util_Rest::post('/api/' . $this->api . '/index/import/rooms', $data);

            $objResponse->success = '1';
            $objResponse->msg = $arrRestResponse['msg'];
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

    public function importUsersAction() {
        $this->_disableViewAndLayout();
        $objResponse = new stdClass();

        try {
            if ($this->_getParam('step', 'check') == 'check') {
                $csvFile = $this->_getParam('files', null);

                if ($csvFile == null) {
                    throw new Exception(IMDT_Util_Translate::_('You must provide a CSV file'));
                }

                $csvFileUrl = current($csvFile);
                $decodedCsvFileUrl = urldecode($csvFileUrl);
                $csvFileRelativeToDocumentRootPath = str_replace(IMDT_Util_Config::getInstance()->get('web_base_url'), '', $decodedCsvFileUrl);
                $csvFileFullPath = realpath($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $csvFileRelativeToDocumentRootPath);

                if ($csvFileFullPath == false) {
                    throw new Exception(IMDT_Util_Translate::_('CSV file not found'));
                }

                $csvFileContents = file_get_contents($csvFileFullPath);

                $data = array(
                    'file-contents' => base64_encode(utf8_encode($csvFileContents)),
                    'step' => 'check'
                );

                $arrRestResponse = IMDT_Util_Rest::post('/api/room-users-import/index/import/users', $data);

                $objResponse->success = '1';
                $objResponse->msg = $arrRestResponse['msg'];
                if (isset($arrRestResponse['data'])) {
                    $objResponse->data = $arrRestResponse['data'];
                }
            }/* else{
              $data = array(
              'users'         => $this->_getParam('users'),
              'step'          => $this->_getParam('step'),
              'permission'    => $this->_getParam('permission'),
              'roomId'        => $this->_getParam('id'),
              );

              $arrRestResponse = IMDT_Util_Rest::post('/api/room-users-import/index/import/users',$data);

              $objResponse->success = '1';
              $objResponse->msg = $arrRestResponse['msg'];
              } */
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

    public function manageRecordingAction() {
        $this->_skipAcl();
        try {
            $meetingRoomId = $this->_getParam('id', null);

            if ($meetingRoomId == null) {
                throw new Exception('Invalid room id');
            }

            $parameters['meeting_room_id'] = $meetingRoomId;
            $parameters['meeting_room_id_c'] = 'e';

            $response = IMDT_Util_Rest::get('/api/recordings', $parameters);

            if (is_array($response['collection']) && (count($response['collection']) > 0)) {
                foreach ($response['collection'] as &$recording) {
                    $start_date = new DateTime(date('Y-m-d H:i:s', strtotime($recording['date_start'])));
                    $end_date = new DateTime(date('Y-m-d H:i:s', strtotime($recording['date_end'])));
                    $interval = $start_date->diff($end_date);
                    $hours = $interval->format('%h');
                    $minutes = $interval->format('%i');
                    $seconds = $interval->format('%s');

                    //$recording['duration'] = sprintf('%s:%s',$hours,$minutes);
                    $recording['duration'] = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                }
            }

            $this->view->recordings = $response['collection'];
        } catch (Exception $ex) {
            
        }

        $this->view->id = $meetingRoomId;
        $this->view->title = $this->_helper->translate('Recording Management');
    }

}
