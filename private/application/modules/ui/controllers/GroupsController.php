<?php

class Ui_GroupsController extends IMDT_Controller_Abstract {

    public function init() {
        $this->filters = array();
        $this->filters['name'] = array('name' => 'name', 'label' => $this->_helper->translate('column-group-name'), 'type' => 'text');
        $this->filters['auth_mode_id'] = array('name' => 'auth_mode_id', 'label' => $this->_helper->translate('column-group-auth_mode-name'), 'type' => 'combo', 'source' => 'group_auth_mode');
        $this->filters['access_profile_id'] = array('name' => 'access_profile_id', 'label' => $this->_helper->translate('column-access_profile-name'), 'type' => 'combo', 'source' => 'access_profile');
        $this->filters['user_attendee'] = array('name' => 'user_attendee', 'label' => $this->_helper->translate('column-group-user_attendee'), 'type' => 'combo-remote', 'url' => array('controller' => 'users', 'action' => 'remote-search'));
        $this->filters['user_attendee_login'] = array('name' => 'user_attendee_login', 'label' => $this->_helper->translate('column-group-user_attendee_login'), 'type' => 'text');
        $this->filters['user_attendee_name'] = array('name' => 'user_attendee_name', 'label' => $this->_helper->translate('column-group-user_attendee_name'), 'type' => 'text');
        $this->filters['user_attendee_auth_mode'] = array('name' => 'user_attendee_auth_mode', 'label' => $this->_helper->translate('column-group-user_attendee_auth_mode'), 'type' => 'combo', 'source' => 'auth_mode');
        $this->filters['group_attendee'] = array('name' => 'group_attendee', 'label' => $this->_helper->translate('column-group-group_attendee'), 'type' => 'combo', 'source' => 'group');
        $this->filters['group_attendee_name'] = array('name' => 'group_attendee_name', 'label' => $this->_helper->translate('column-group-group_attendee_name'), 'type' => 'text');
        $this->filters['group_attendee_auth_mode'] = array('name' => 'group_attendee_auth_mode', 'label' => $this->_helper->translate('column-group-group_attendee_auth_mode'), 'type' => 'combo', 'source' => 'group_auth_mode');
        $this->filters['observations'] = array('name' => 'observations', 'label' => $this->_helper->translate('column-group-observations'), 'type' => 'text');

        $this->filters['main_name'] = array('main' => true, 'name' => 'name', 'label' => $this->_helper->translate('column-group-name'), 'type' => 'text', 'condition' => 'in');

        $this->api = 'groups';
        $this->pkey = 'group_id';

        $this->view->controllerTitle = $this->_helper->translate('Groups Management');
        $this->view->newBtn = $this->_helper->translate('New group');

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
        $headers['columns-leach'] = 'group_id,name,auth_mode_id,access_profile_id,user_attendee,group_attendee_local,group_attendee_ldap';
        $arrDesc = array();
        $arrDesc[] = $this->_helper->translate('column-group-id');
        $arrDesc[] = $this->_helper->translate('column-group-name');
        $arrDesc[] = $this->_helper->translate('column-group-auth_mode_id');
        $arrDesc[] = $this->_helper->translate('column-group-access_profile_id');
        $arrDesc[] = $this->_helper->translate('column-group-user_attendee');
        $arrDesc[] = $this->_helper->translate('column-group-group_attendee_local');
        $arrDesc[] = $this->_helper->translate('column-group-group_attendee_ldap');
        //$headers['columns-desc'] = implode(',',$arrDesc);

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
        $params['pdf-title'] = $this->_helper->translate('Groups Management');

        $headers = array();
        $headers['columns-leach'] = 'name,auth_mode,access_profile';
        $arrDesc = array();
        $arrDesc[] = $this->_helper->translate('column-group-name');
        $arrDesc[] = $this->_helper->translate('column-auth_mode-name');
        $arrDesc[] = $this->_helper->translate('column-access_profile-name');
        $headers['columns-desc'] = implode(',', $arrDesc);

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

    public function exportUserAttendeeAction() {
        $this->_disableViewAndLayout();

        $params = array();
        $params['user_group'] = $this->_request->getParam('id');
        $params['export'] = 'csv';

        $headers = array();
        $headers['columns-leach'] = 'user_id,name';
        $arrHeader = array();
        $arrHeader[] = $this->_helper->translate('column-user-id');
        $arrHeader[] = $this->_helper->translate('column-user-name');
        $headers['columns-desc'] = implode(',', $arrHeader);

        $response = IMDT_Util_Rest::get('/api/users.json', $params, $headers);

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

    public function exportGroupAttendeeAction() {
        $this->_disableViewAndLayout();

        $params = array();
        $params['auth_mode_id'] = $this->_request->getParam('auth_mode');
        $params['group_group'] = $this->_request->getParam('id');
        $params['export'] = 'csv';

        $headers = array();
        $headers['columns-leach'] = 'group_id,name';
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

        $response = IMDT_Util_Rest::get('/api/' . $this->api . '/' . $this->view->id . '.json');
        if ($response['row']['auth_mode_id'] == BBBManager_Config_Defines::$LDAP_AUTH_MODE) {
            $this->_forward('view');
        }
    }

    public function viewAction() {
        $this->view->id = $this->_getParam('id', null);

        $response = IMDT_Util_Rest::get('/api/' . $this->api . '/' . $this->view->id . '.json');
        if ($response['row']['auth_mode_id'] == BBBManager_Config_Defines::$LDAP_AUTH_MODE) {
            $this->_helper->viewRenderer('view-ldap');
        }
    }

    public function newAction() {
        $this->view->id = 'new';
        $this->view->title = $this->_helper->translate('New');
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
                $row[] = $curr['auth_mode'];
                $row[] = $curr['access_profile'];
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

            if ($id == 'new') {
                $objResponse->form = array();
            } elseif ($id) {
                $response = IMDT_Util_Rest::get('/api/' . $this->api . '/' . $id . '.json');

                $row = $response['row'];

                $arrForm['name'] = $row['name'];
                $arrForm['auth_mode_id'] = $row['auth_mode_id'];
                $arrForm['access_profile_id'] = $row['access_profile_id'];
                $arrForm['observations'] = $row['observations'];
                $arrForm['user_attendee'] = $row['user_attendee'];
                $arrForm['group_attendee_local'] = $row['group_attendee_local'];
                $arrForm['group_attendee_ldap'] = $row['group_attendee_ldap'];

                $objResponse->form = $arrForm;
            }


            $collectionGroups = IMDT_Util_Rest::get('/api/access-profiles.json');


            $arrSelect2 = array('group_local' => array(), 'group_ldap' => array(), 'user' => array(), 'access_profile' => array());

            foreach ($collectionGroups['collection'] as $obj) {
                $nRow = array();
                $nRow['id'] = $obj['access_profile_id'];
                $nRow['text'] = $obj['name'];

                $arrSelect2['access_profile'][] = $nRow;
            }


            $collectionGroups = IMDT_Util_Rest::get('/api/groups.json');

            foreach ($collectionGroups['collection'] as $obj) {
                if ($obj['group_id'] == $id)
                    continue;

                $nRow = array();
                $nRow['id'] = $obj['group_id'];
                $nRow['text'] = $obj['name'];

                if ($obj['auth_mode_id'] == BBBManager_Config_Defines::$LOCAL_AUTH_MODE) { // Local
                    $arrSelect2['group_local'][] = $nRow;
                } elseif ($obj['auth_mode_id'] == BBBManager_Config_Defines::$LDAP_AUTH_MODE && $obj['visible'] == true) { //Ldap
                    $arrSelect2['group_ldap'][] = $nRow;
                }
            }

            /*
              $collectionUsers = IMDT_Util_Rest::get('/api/users.json');
              foreach($collectionUsers['collection'] as $obj) {
              $nRow = array();
              $nRow['id'] = $obj['user_id'];
              $nRow['text'] = $obj['name'];
              //$nRow['auth_mode_id'] = $obj['auth_mode_id'];

              $arrSelect2['user'][] = $nRow;
              }
             */

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
            $data['access_profile_id'] = $this->_request->getPost('access_profile_id', null);
            $data['observations'] = $this->_request->getPost('observations', null);
            $data['user_attendee'] = $this->_request->getPost('user_attendee', null);
            $data['group_attendee_local'] = $this->_request->getPost('group_attendee_local', null);
            $data['group_attendee_ldap'] = $this->_request->getPost('group_attendee_ldap', null);

            if ($id == 'new') {
                $data['auth_mode_id'] = 1;
                $arrRestResponse = IMDT_Util_Rest::post('/api/' . $this->api, $data);
            } else {
                $arrRestResponse = IMDT_Util_Rest::put('/api/' . $this->api . '/' . $id, $data);
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

    public function advSearchFilterAction() {
        $objResponse = new stdClass();

        try {
            $arrSelect = array('auth_mode' => '', 'access_profile' => '', 'user' => '', 'group' => '');

            $id = $this->_request->getParam('id');


            $arrRestResponse = IMDT_Util_Rest::get('/api/access-profiles.json', array(array('columns-leach' => 'access_profile_id,name')));
            $arr = $arrRestResponse['collection'];
            $strOptions = '';
            foreach ($arr as $obj) {
                if ($obj['access_profile_id'] == BBBManager_Config_Defines::$NA_PROFILE)
                    continue;
                $strOptions .= '<option value="' . $obj['access_profile_id'] . '">' . $obj['name'] . '</option>';
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


            $strOptions = '';
            foreach ($arrAuthMode as $obj) {
                if ($obj['id'] == BBBManager_Config_Defines::$PERSONA_AUTH_MODE) {
                    continue;
                }

                $strOptions .= '<option value="' . $obj['id'] . '">' . $obj['name'] . '</option>';
            }
            $arrSelect['group_auth_mode'] = $strOptions;

            $arrRestResponse = IMDT_Util_Rest::get('/api/groups.json', array(array('columns-leach' => 'group_id,name')));
            $arr = $arrRestResponse['collection'];
            $strOptions = '';
            foreach ($arr as $obj) {
                $strOptions .= '<option value="' . $obj['group_id'] . '">' . $obj['name'] . '</option>';
            }
            $arrSelect['group'] = $strOptions;


            $arrRestResponse = IMDT_Util_Rest::get('/api/users.json', array(array('columns-leach' => 'user_id,name')));
            $arr = $arrRestResponse['collection'];
            $strOptions = '';
            foreach ($arr as $obj) {
                $strOptions .= '<option value="' . $obj['user_id'] . '">' . $obj['name'] . '</option>';
            }
            $arrSelect['user'] = $strOptions;


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

            $arrRestResponse = IMDT_Util_Rest::post('/api/' . $this->api . '/index/import/groups', $data);

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
