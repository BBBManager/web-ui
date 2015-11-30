<?php

class Ui_UsersController extends IMDT_Controller_Abstract {

    public function init() {
        $this->filters = array();
        $this->filters['name'] = array('name' => 'name', 'label' => $this->_helper->translate('column-user-name'), 'type' => 'text');
        $this->filters['login'] = array('name' => 'login', 'label' => $this->_helper->translate('column-user-login'), 'type' => 'text');
        $this->filters['email'] = array('name' => 'name', 'label' => $this->_helper->translate('column-user-email'), 'type' => 'text');
        $this->filters['auth_mode_id'] = array('name' => 'auth_mode_id', 'label' => $this->_helper->translate('column-auth_mode-name'), 'type' => 'combo', 'source' => 'auth_mode');
        $this->filters['access_profile_id'] = array('name' => 'access_profile_id', 'label' => $this->_helper->translate('column-access_profile-name'), 'type' => 'combo', 'source' => 'access_profile');
        $this->filters['valid_from'] = array('name' => 'valid_from', 'label' => $this->_helper->translate('column-user-valid_from'), 'type' => 'date');
        $this->filters['valid_to'] = array('name' => 'valid_to', 'label' => $this->_helper->translate('column-user-valid_to'), 'type' => 'date');
        $this->filters['actived'] = array('name' => 'actived', 'label' => $this->_helper->translate('column-user-actived'), 'type' => 'boolean');
        $this->filters['observations'] = array('name' => 'observations', 'label' => $this->_helper->translate('column-user-observations'), 'type' => 'text');
        $this->filters['user_id'] = array('name' => 'user_id', 'label' => $this->_helper->translate('column-user-user_id'), 'type' => 'integer', 'hidden' => true);

        $arrGroupOptions['group'] = array('name' => 'group', 'label' => $this->_helper->translate('column-user-group'), 'type' => 'combo', 'source' => 'group');
        $arrGroupOptions['group_name'] = array('name' => 'group_name', 'label' => $this->_helper->translate('column-user-group_name'), 'type' => 'text');
        $arrGroupOptions['group_auth_mode'] = array('name' => 'group_auth_mode', 'label' => $this->_helper->translate('column-user-group_auth_mode'), 'type' => 'combo', 'source' => 'auth_mode');
        $this->filters['group_options'] = array('name' => 'group_options', 'label' => $this->_helper->translate('column-user-groups'), 'type' => 'optgroup', 'options' => $arrGroupOptions);


        $this->filters['main_name'] = array('main' => true, 'name' => 'name', 'label' => $this->_helper->translate('column-user-name'), 'type' => 'text', 'condition' => 'in');

        $this->api = 'users';
        $this->pkey = 'user_id';

        $this->view->controllerTitle = $this->_helper->translate('Users Management');
        $this->view->newBtn = $this->_helper->translate('New user');
        $this->view->hasFilters = false;
    }

    public function indexAction() {
        $this->view->tableSource = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'table-content');
        $this->view->uriPage = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'index');
        $this->view->uriExport = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'export');
        $this->view->uriExportPdf = array('module' => $this->_request->getModuleName(), 'controller' => $this->_request->getControllerName(), 'action' => 'export-pdf');
        $this->view->filters = $this->filters;
        $this->view->deleteUrl = '/' . $this->_request->getModuleName() . '/' . $this->_request->getControllerName() . '/delete/id/';

        $this->view->parameters = IMDT_Util_Url::getThisParams($this->filters);
        $this->view->parametersString = http_build_query($this->view->parameters);
        $this->view->hasFilters = ((isset($this->view->parameters['q']) && (count($this->view->parameters['q']) > 0)) ? true : false);
    }

    public function exportAction() {
        $this->_disableViewAndLayout();

        $params = IMDT_Util_Url::getThisParams($this->filters);
        $params['export'] = 'csv';

        $headers = array();
        $headers['columns-leach'] = 'user_id,name,email,login,auth_mode_id,access_profile_id,access_profile,groups,valid_from,valid_to';
        //$headers['columns-desc'] = $this->_helper->translate('column-invite_template-id').','.$this->_helper->translate('column-invite_template-subject');
        $arrHeader = array();
        $arrHeader[] = $this->_helper->translate('column-user-id');
        $arrHeader[] = $this->_helper->translate('column-user-name');
        $arrHeader[] = $this->_helper->translate('column-user-email');
        $arrHeader[] = $this->_helper->translate('column-user-login');
        $arrHeader[] = $this->_helper->translate('column-user-auth_mode_id');
        $arrHeader[] = $this->_helper->translate('column-user-access_profile_id');
        $arrHeader[] = $this->_helper->translate('column-access_profile-name');
        $arrHeader[] = $this->_helper->translate('column-user-groups');
        $arrHeader[] = $this->_helper->translate('column-user-valid_from');
        $arrHeader[] = $this->_helper->translate('column-user-valid_to');
        //$headers['columns-desc'] = implode(',',$arrHeader);

        $response = IMDT_Util_Rest::get('/api/' . $this->api . '.json', $params, $headers);

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

        $headers = array();
        $headers['columns-leach'] = 'name,login,access_profile,actived';
        //$headers['columns-desc'] = $this->_helper->translate('column-invite_template-id').','.$this->_helper->translate('column-invite_template-subject');
        $arrHeader = array();
        $arrHeader[] = $this->_helper->translate('column-user-name');
        $arrHeader[] = $this->_helper->translate('column-user-login');
        $arrHeader[] = $this->_helper->translate('column-access_profile-name');
        $arrHeader[] = $this->_helper->translate('column-user-actived');
        $headers['columns-desc'] = implode(',', $arrHeader);

        $response = IMDT_Util_Rest::get('/api/' . $this->api . '.json', $params, $headers);

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

    public function exportGroupsAction() {
        $this->_disableViewAndLayout();
        //$params = IMDT_Util_Url::getThisParams($this->filters);
        $params = array();
        $params['user_group'] = $this->_request->getParam('id');
        $params['export'] = 'csv';

        $headers = array();
        $headers['columns-leach'] = 'group_id,name';
        //$headers['columns-desc'] = $this->_helper->translate('column-invite_template-id').','.$this->_helper->translate('column-invite_template-subject');
        $arrHeader = array();
        $arrHeader[] = $this->_helper->translate('column-group-id');
        $arrHeader[] = $this->_helper->translate('column-group-name');
        $headers['columns-desc'] = implode(',', $arrHeader);

        $response = IMDT_Util_Rest::get('/api/groups.json', $params, $headers);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        //header('Content-type: text/csv');
        $useUtf8 = $this->_request->getParam('utf8', 0);
        if ($useUtf8) {
            header("Content-Type: text/csv; charset=utf-8");
        } else {
            header("Content-Type: text/csv");
        }

        //header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $this->_request->getControllerName() . '.csv"');
        echo file_get_contents($response['url']);
        exit;
    }

    public function finishedAction() {
        
    }

    public function editAction() {
        $this->view->id = $this->_getParam('id', null);
        $this->view->title = $this->_helper->translate('Edit');

        if ($this->getParam('auth_mode', BBBManager_Config_Defines::$LOCAL_AUTH_MODE) == BBBManager_Config_Defines::$LDAP_AUTH_MODE) {
            $this->_helper->viewRenderer('edit-ldap');
        }

        if ($this->getParam('auth_mode', BBBManager_Config_Defines::$LOCAL_AUTH_MODE) == BBBManager_Config_Defines::$PERSONA_AUTH_MODE) {
            $this->_helper->viewRenderer('edit-persona');
        }
    }

    public function viewAction() {
        $this->view->id = $this->_getParam('id', null);

        if ($this->getParam('auth_mode', BBBManager_Config_Defines::$LOCAL_AUTH_MODE) == BBBManager_Config_Defines::$LDAP_AUTH_MODE) {
            $this->_helper->viewRenderer('view-ldap');
        }

        if ($this->getParam('auth_mode', BBBManager_Config_Defines::$LOCAL_AUTH_MODE) == BBBManager_Config_Defines::$PERSONA_AUTH_MODE) {
            $this->_helper->viewRenderer('view-persona');
        }
    }

    public function newAction() {
        $this->view->id = 'new';
        $this->view->title = $this->_helper->translate('New');

        if (IMDT_Util_Auth::getInstance()->get('new_user_prefix')) {
            $this->view->new_user_prefix = IMDT_Util_Auth::getInstance()->get('new_user_prefix');
        }

        $this->_helper->viewRenderer('edit');
    }

    public function tableContentAction() {
        $objResponse = new stdClass();

        try {
            $params = IMDT_Util_Url::getThisParams($this->filters);
            $response = IMDT_Util_Rest::get('/api/' . $this->api . '.json', $params);

            $arrTable = array();
            foreach ($response['collection'] as $curr) {
                $row = array();
                if ($curr['_removable'] == '1') {
                    $row[] = '<input name="deleteRow[]" class="cboxSelectRow" type="checkbox" value="' . $curr[$this->pkey] . '" />';
                } else {
                    $row[] = '<input class="cboxSelectRow" type="checkbox" disabled="disabled" />';
                }
                $row[] = $curr['name'];
                $row[] = $curr['login'];
                $row[] = $curr['access_profile'];
                $row[] = $curr['actived'];
                $actions = '<td nowrap="nowrap">';

                $actions .= '<a title="' . $this->_helper->translate('View') . '" data-toggle="tooltip" class="btn btn-mini" href="/ui/' . $this->_request->getControllerName() . '/view/id/' . $curr[$this->pkey] . '/auth_mode/' . $curr['auth_mode_id'] . '" data-original-title="' . $this->_helper->translate('View') . '"><i class="icon-eye-open"></i></a>';

                if ($curr['_editable'] == '1') {
                    $actions .= '<a title="' . $this->_helper->translate('Edit') . '" data-toggle="tooltip" class="btn btn-mini" href="/ui/' . $this->_request->getControllerName() . '/edit/id/' . $curr[$this->pkey] . '/auth_mode/' . $curr['auth_mode_id'] . '" data-original-title="' . $this->_helper->translate('Edit') . '"><i class="icon-pencil"></i></a>';
                } else {
                    $actions .= '<a title="' . $this->_helper->translate('Edit') . '" data-toggle="tooltip" class="btn btn-mini" disabled="disabled" data-original-title="' . $this->_helper->translate('Edit') . '"><i class="icon-pencil"></i></a>';
                }

                if ($curr['_removable'] == '1') {
                    $actions .= '<a title="' . $this->_helper->translate('Delete') . '" data-toggle="tooltip" class="btn btn-mini btn-delete" href="" data-original-title="Excluir" onclick="deleteRow(\'#records\',\'row_' . $curr[$this->pkey] . '\',\'/ui/' . $this->_request->getControllerName() . '/delete/id/' . $curr[$this->pkey] . '\'); return false;"><i class="icon-trash"></i></a>';
                } else {
                    $actions .= '<a title="' . $this->_helper->translate('Delete') . '" data-toggle="tooltip" class="btn btn-mini btn-delete" disabled="disabled" data-original-title="Excluir"><i class="icon-trash"></i></a>';
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

            $arrForm = array();

            if ($id == 'new') {

                if (IMDT_Util_Auth::getInstance()->get('user_access_profile') == BBBManager_Config_Defines::$SYSTEM_PRIVILEGED_USER_PROFILE) {
                    $arrForm['access_profile_id'] = BBBManager_Config_Defines::$SYSTEM_USER_PROFILE;
                }

                $objResponse->form = $arrForm;
            } elseif ($id) {
                $response = IMDT_Util_Rest::get('/api/' . $this->api . '/' . $id . '.json');

                $row = $response['row'];

                $arrForm['name'] = $row['name'];
                $arrForm['login'] = $row['login'];
                $arrForm['email'] = $row['email'];
                $arrForm['observations'] = $row['observations'];
                $arrForm['access_profile_id'] = $row['access_profile_id'];
                $arrForm['auth_mode_id'] = $row['auth_mode_id'];
                $arrForm['groups'] = $row['groups'];
                $arrForm['valid_from'] = IMDT_Util_Date::filterDateToCurrentLang($row['valid_from'], false);
                $arrForm['valid_to'] = IMDT_Util_Date::filterDateToCurrentLang($row['valid_to'], false);
                $arrForm['access_profile_name'] = BBBManager_Config_Defines::getAccessProfile($row['access_profile_id']);
                $arrForm['actived'] = $row['actived'];

                if (isset($row['ldapGroups'])) {
                    $arrForm['ldapGroups'] = $row['ldapGroups'];
                }

                $objResponse->form = $arrForm;
            }


            $arrSelect2 = array('auth_mode' => array(), 'group' => array()/* ,'access_profile'=>array() */, 'ldapGroup' => array());

            if (isset($arrForm['ldapGroups']) && (count($arrForm['ldapGroups']) > 0)) {
                foreach ($arrForm['ldapGroups'] as $ldapGroup) {
                    $arrSelect2['ldapGroup'][] = array('id' => $ldapGroup, 'text' => $ldapGroup);
                }
            }

            $arrSelect2['auth_mode'][] = array('id' => BBBManager_Config_Defines::$LOCAL_AUTH_MODE, 'text' => BBBManager_Config_Defines::getAuthMode(BBBManager_Config_Defines::$LOCAL_AUTH_MODE));
            $arrSelect2['auth_mode'][] = array('id' => BBBManager_Config_Defines::$LDAP_AUTH_MODE, 'text' => BBBManager_Config_Defines::getAuthMode(BBBManager_Config_Defines::$LDAP_AUTH_MODE));
            $arrSelect2['auth_mode'][] = array('id' => BBBManager_Config_Defines::$PERSONA_AUTH_MODE, 'text' => BBBManager_Config_Defines::getAuthMode(BBBManager_Config_Defines::$PERSONA_AUTH_MODE));

            /*
              foreach ($arr as $acessProfileId=>$name) {
              if(IMDT_Util_Auth::getInstance()->get('user_access_profile') == BBBManager_Config_Defines::$SYSTEM_PRIVILEGED_USER_PROFILE && $acessProfileId != BBBManager_Config_Defines::$SYSTEM_USER_PROFILE) continue;

              $nRow = array();
              $nRow['id'] = $acessProfileId;
              $nRow['text'] = $name;

              $arrForm['access_profile_name'] = $name;
              $objResponse->form = $arrForm;
              }
             */
            /*
              $collectionAccessProfiles = IMDT_Util_Rest::get('/api/access-profiles.json');
              foreach($collectionAccessProfiles['collection'] as $obj) {
              $nRow = array();
              $nRow['id'] = $obj['access_profile_id'];
              $nRow['text'] = $obj['name'];

              $arrSelect2['access_profile'][] = $nRow;
              }
             */


            $collectionGroups = IMDT_Util_Rest::get('/api/groups.json', array('auth_mode_id' => '1'));
            foreach ($collectionGroups['collection'] as $obj) {
                $nRow = array();
                $nRow['id'] = $obj['group_id'];
                $nRow['text'] = $obj['name'];

                $arrSelect2['group'][] = $nRow;
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
            $data['login'] = $this->_request->getPost('login', null);
            $data['email'] = $this->_request->getPost('email', null);
            $data['valid_from'] = IMDT_Util_Date::filterDateToApi($this->_request->getPost('valid_from', ''));
            $data['valid_to'] = IMDT_Util_Date::filterDateToApi($this->_request->getPost('valid_to', ''));
            $data['observations'] = $this->_request->getPost('observations', null);
            $data['access_profile_id'] = $this->_request->getPost('access_profile_id', null);
            $data['auth_mode_id'] = $this->_request->getPost('auth_mode_id', null);
            $data['groups'] = $this->_request->getPost('groups', null);

            if ($id == 'new') {
                $arrRestResponse = IMDT_Util_Rest::post('/api/' . $this->api, $data);
            } else {
                $arrRestResponse = IMDT_Util_Rest::put('/api/' . $this->api . '/' . $id, $data);
            }

            if ($this->_request->getPost('send_password', null) == '1') {
                IMDT_Util_Rest::get('/api/users-reset-password', array('user_id' => $arrRestResponse['id']));
            }

            $objResponse->success = '1';
            $objResponse->msg = $this->_helper->translate('Data was saved successfully');
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
            IMDT_Util_Rest::delete('/api/' . $this->api . '/' . $id . '.json');

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

    public function postResetPasswordAction() {
        $objResponse = new stdClass();

        try {
            if ($this->_request->isPost()) {
                $userId = $this->_request->getPost('user_id', null);

                $resetPassResponse = IMDT_Util_Rest::get('/api/users-reset-password', array('user_id' => $userId));

                $objResponse->success = '1';
                $objResponse->msg = $resetPassResponse['msg'];
            }
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

    public function editMeAction() {
        $this->view->isLdapUser = ((IMDT_Util_Auth::getInstance()->get('auth_mode') == BBBManager_Config_Defines::$LDAP_AUTH_MODE) ? true : false);

        try {
            if ($this->_request->isPost()) {
                $name = $this->_getParam('name', null);
                $login = $this->_getParam('login', null);
                $email = $this->_getParam('myemail', null);
                $password = $this->_getParam('mypassword', null);
                $password_confirm = $this->_getParam('mypassword_confirmation', null);

                if ($password != $password_confirm) {
                    throw new Exception($this->_helper->translate('Password and password confirm does not match') . '.');
                }

                $userData = array(
                    'name' => $name,
                    'login' => $login,
                    'email' => $email
                );

                if ($password != null) {
                    $userData['password'] = $password;
                }

                $responseData = IMDT_Util_Rest::put('/api/users/' . IMDT_Util_Auth::getInstance()->get('id'), $userData);

                $authData = Zend_Auth::getInstance()->getStorage()->read();

                $authData['full_name'] = $responseData['data']['name'];
                $authData['login'] = $responseData['data']['login'];
                $authData['email'] = $responseData['data']['email'];

                Zend_Auth::getInstance()->getStorage()->write($authData);

                $this->addMessage(array('success' => $responseData['msg']));

                $this->_redirector->gotoUrl('/');
            }
        } catch (Exception $e) {
            $this->addMessage(array('error' => $e->getMessage()));
        }
    }

    public function editmeFormContentAction() {
        $objResponse = new stdClass();

        try {
            $objResponse = new stdClass();
            $id = $id = IMDT_Util_Auth::getInstance()->get('id');
            $response = IMDT_Util_Rest::get('/api/' . $this->api . '/' . $id . '.json');

            $row = $response['row'];
            $arrForm['name'] = $row['name'];
            $arrForm['login'] = $row['login'];
            $arrForm['myemail'] = $row['email'];

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

    public function editmeFormPostAction() {
        $objResponse = new stdClass();

        try {
            $id = IMDT_Util_Auth::getInstance()->get('id');

            if (!$this->_request->isPost())
                throw new Exception($this->_helper->translate('Invalid Request'));

            $data = array();
            $data['name'] = $this->_request->getPost('name', null);
            $data['login'] = $this->_request->getPost('login', null);
            $data['email'] = $this->_request->getPost('myemail', null);

            $password = $this->_getParam('mypassword', null);
            $password_confirm = $this->_getParam('mypassword_confirmation', null);
            if (strlen($password) > 0) {
                if ($password != $password_confirm) {
                    throw new Exception($this->_helper->translate('Password and password confirm does not match') . '.');
                }

                $data['password'] = $password;
            }

            $arrRestResponse = IMDT_Util_Rest::put('/api/' . $this->api . '/' . $id, $data);

            $authData = Zend_Auth::getInstance()->getStorage()->read();
            $authData['full_name'] = $data['name'];
            $authData['login'] = $data['login'];
            $authData['email'] = $data['email'];
            Zend_Auth::getInstance()->getStorage()->write($authData);

            $objResponse->success = '1';
            $objResponse->msg = $this->_helper->translate('Data was saved successfully');
            $objResponse->redirect = $this->view->url(array('action' => 'edit-me'));
        } catch (IMDT_Controller_Exception_InvalidToken $e1) {
            $objResponse->success = '-1';
            $objResponse->msg = '';
        } catch (Exception $e) {
            $objResponse->success = '0';
            $objResponse->msg = $e->getMessage();
        }

        $this->_helper->json($objResponse);
    }

    public function advSearchFilterAction() {
        $objResponse = new stdClass();

        try {
            $arrSelect = array('auth_mode' => '', 'access_profile' => '', 'group' => '');

            $id = $this->_request->getParam('id');


            $arrRestResponse = IMDT_Util_Rest::get('/api/access-profiles.json', array(array('columns-leach' => 'access_profile_id,name')));
            $arr = $arrRestResponse['collection'];
            $strOptions = '';
            foreach ($arr as $obj) {
                if (IMDT_Util_Auth::getInstance()->get('user_access_profile') == BBBManager_Config_Defines::$SYSTEM_PRIVILEGED_USER_PROFILE && $obj['access_profile_id'] != BBBManager_Config_Defines::$SYSTEM_USER_PROFILE)
                    continue;

                if ($obj['access_profile_id'] == BBBManager_Config_Defines::$NA_PROFILE)
                    continue;

                $strOptions .= '<option value="' . $obj['access_profile_id'] . '">' . BBBManager_Config_Defines::getAccessProfile($obj['access_profile_id']) . '</option>';
            }
            $arrSelect['access_profile'] = $strOptions;



            $arrAuthMode = array();
            foreach (BBBManager_Config_Defines::getAuthMode() as $value => $name) {
                $arrAuthMode[$name] = array('id' => $value, 'name' => $name);
            }
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

    public function remoteSearchAction() {
        if ($q = $this->getParam('q', false)) {
            $this->setParam('name', $q);
            $this->setParam('name_c', 'i');
        }

        if ($currvalue = $this->getParam('currvalue')) {
            $this->setParam('user_id', $currvalue);
            $this->setParam('user_id_c', 'i');
        }

        $this->setParam('actived', '1');
        $this->setParam('actived_c', 'e');

        $params = IMDT_Util_Url::getThisParams($this->filters);

        $collectionUsers = IMDT_Util_Rest::get('/api/users.json', $params);
        $i = 0;

        $arrReturn = array();

        foreach ($collectionUsers['collection'] as $obj) {
            /* if($i++ > 11) continue; */
            $nRow = array();
            $nRow['id'] = $obj['user_id'];
            $nRow['text'] = $obj['name'];

            $arrReturn[] = $nRow;
        }

        $this->_helper->json($arrReturn);
    }

    public function remoteLoadUserAction() {
        $q = $this->getParam('id');
        $this->setParam('user_id', $q);
        $this->setParam('user_id_c', 'i');
        $this->setParam('actived', '1');
        $this->setParam('actived_c', 'e');

        $params = IMDT_Util_Url::getThisParams($this->filters);
        $collectionUsers = IMDT_Util_Rest::get('/api/users.json', $params);
        $i = 0;

        $arrReturn = array();

        foreach ($collectionUsers['collection'] as $obj) {
            if ($i++ > 11)
                continue;

            $nRow = array();
            $nRow['id'] = $obj['user_id'];
            $nRow['text'] = $obj['name'];
            //$nRow['auth_mode_id'] = $obj['auth_mode_id'];

            $arrReturn[] = $nRow;
        }

        $this->_helper->json($arrReturn);



        $response = IMDT_Util_Rest::get('/api/' . $this->api . '/' . $id . '.json');
        $row = $response['row'];

        $arrReturn = array();
        $arrReturn[0]['id'] = $row['user_id'];
        $arrReturn[0]['text'] = $row['name'];

        $this->_helper->json($arrReturn);
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

            $arrRestResponse = IMDT_Util_Rest::post('/api/' . $this->api . '/index/import/users', $data);

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

}
