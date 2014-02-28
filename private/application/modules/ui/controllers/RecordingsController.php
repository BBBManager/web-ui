<?php

class Ui_RecordingsController extends IMDT_Controller_Abstract {

    protected $_recordDbTable;
    protected $_recordGroupDbTable;
    protected $_recordUserDbTable;
    protected $_groupDbTable;
    protected $_userDbTable;
    protected $_recordTagDbTable;

    public function init() {

        $this->_recordDbTable = new Ui_Model_Record();
        $this->_recordGroupDbTable = new Ui_Model_RecordGroup();
        $this->_groupDbTable = new Ui_Model_Group();
        $this->_userDbTable = new Ui_Model_User();
        $this->_recordUserDbTable = new Ui_Model_RecordUser();
        $this->_recordTagDbTable = new Ui_Model_RecordTag();
        
        $this->view->headScript()->appendFile($this->view->baseUrl('/resources/js/recordings.js'));
    }

    public function indexAction() {

        $events = $this->_recordDbTable->findAllEvents();
        $events = (!is_null($events) ? $events->toArray() : array());
        $this->view->recordings = $events;
    }

    public function listAction() {
        /*$this->view->jQuery()->addOnload('$("#tags").select2();$("#participantes-grupos-ldap").select2();$("#participantes-grupos-locais").select2();$("#participantes-usuarios-ldap").select2();$("#participantes-usuarios-locais").select2();');*/
        $this->view->arrCategorias = array(
            array(
                'id' => 1,
                'description' => 'Treinamentos',
                'parentId' => ''
            ),
            array(
                'id' => 2,
                'description' => 'Jurídicos',
                'parentId' => 1,
                'hierarchy' => '1'
            ),
            array(
                'id' => 3,
                'description' => 'Cível',
                'parentId' => 2,
                'hierarchy' => '1-2'
            ),
            array(
                'id' => 4,
                'description' => 'Atualização de Processo Civil',
                'parentId' => 3,
                'hierarchy' => '1-2-3'
            ),
            array(
                'id' => 5,
                'description' => 'Aula 1/3',
                'parentId' => 4,
                'hierarchy' => '1-2-3-4'
            ),
            array(
                'id' => 6,
                'description' => 'Aula 2/3',
                'parentId' => 4,
                'hierarchy' => '1-2-3-4'
            ),
            array(
                'id' => 7,
                'description' => 'Aula 3/3',
                'parentId' => 4,
                'hierarchy' => '1-2-3-4'
            ),
            array(
                'id' => 8,
                'description' => 'Direito do Consumidor',
                'parentId' => 3,
                'hierarchy' => '1-2-3'
            ),
            array(
                'id' => 9,
                'description' => 'Aula 1/2',
                'parentId' => 8,
                'hierarchy' => '1-2-3-8'
            ),
            array(
                'id' => 10,
                'description' => 'Aula 2/2',
                'parentId' => 8,
                'hierarchy' => '1-2-3-8'
            ),
            array(
                'id' => 11,
                'description' => 'Constitucional',
                'parentId' => 2,
                'hierarchy' => '1-2'
            ),
            array(
                'id' => 12,
                'description' => 'Criminal',
                'parentId' => 2,
                'hierarchy' => '1-2'
            ),
            array(
                'id' => 13,
                'description' => 'Crimes Ambientais',
                'parentId' => 12,
                'hierarchy' => '1-2-12'
            ),
            array(
                'id' => 14,
                'description' => 'Efeitos da PEC/37',
                'parentId' => 12,
                'hierarchy' => '1-2-12'
            ),
            array(
                'id' => 15,
                'description' => 'Administrativos',
                'parentId' => 1,
                'hierarchy' => '1'
            ),
            array(
                'id' => 16,
                'description' => 'Gestão',
                'parentId' => 15,
                'hierarchy' => '1-15'
            ),
            array(
                'id' => 17,
                'description' => 'CIPA',
                'parentId' => 15,
                'hierarchy' => '1-15'
            ),
            array(
                'id' => 18,
                'description' => 'Integração',
                'parentId' => 1,
                'hierarchy' => '1'
            ),
            array(
                'id' => 19,
                'description' => 'Debates',
                'parentId' => ''
            ),
            array(
                'id' => 20,
                'description' => 'Eleições',
                'parentId' => 19,
                'hierarchy' => '19'
            ),
            array(
                'id' => 21,
                'description' => 'Outros',
                'parentId' => 19,
                'hierarchy' => '19'
            )
        );

        $nomeCategorias = array();

        foreach ($this->view->arrCategorias as $categoria) {
            $nomeCategorias[] = $categoria['description'];
        }

        $this->view->nomeCategorias = $nomeCategorias;
    }

    public function editAction() {

        $eventId = $this->_getParam('recordId');

        if ($this->_request->isPost()) {
            $event = array(
                'name' => $this->_getParam('nome'),
                'date_start' =>date("Y-m-d H:i:s",strtotime(str_replace('/','-',$this->_getParam('data-inicio')))),
                'date_end' =>date("Y-m-d H:i:s",strtotime(str_replace('/','-',$this->_getParam('data-fim')))),
                'public' => $this->_getParam('tipo-sala'),
                'create_date' => date('Y-m-d H:i:s')
            );

            $localGroups = $this->_getParam('participantes-grupos-locais');
            $ldapGroups = $this->_getParam('participantes-grupos-ldap');
            $localUser = $this->_getParam('participantes-usuarios-locais');
            $ldapUser = $this->_getParam('participantes-usuarios-ldap');



            if (is_null($eventId)) {

                // CÓDIGO QUE ADICIONA EVENTOS
            } else {
                $this->_recordDbTable->update($event, $this->_recordDbTable->getAdapter()->quoteInto('record_id=?', $eventId));
                $this->_recordGroupDbTable->delete($this->_recordGroupDbTable->getAdapter()->quoteInto('record_id=?', $eventId));
                $this->_recordUserDbTable->delete($this->_recordGroupDbTable->getAdapter()->quoteInto('record_id=?', $eventId));
                if (!is_null($localGroups)) {
                    foreach ($localGroups as $local) {
                        $insert = array(
                            'record_id' => $eventId,
                            'group_Id' => $local,
                            'auth_mode_id' => 1,
                            'create_date' => date('Y-m-d H:i:s')
                        );
                        $this->_recordGroupDbTable->insert($insert);
                    }
                }
                if (!is_null($ldapGroups)) {
                    foreach ($ldapGroups as $ldaps) {
                        $insert = array(
                            'record_id' => $eventId,
                            'group_Id' => $ldaps,
                            'auth_mode_id' => 2,
                            'create_date' => date('Y-m-d H:i:s')
                        );
                        $this->_recordGroupDbTable->insert($insert);
                    }
                }
                if (!is_null($localUser)) {
                    foreach ($localUser as $lUser) {
                        $insert = array(
                            'record_id' => $eventId,
                            'user_id' => $lUser,
                            'create_date' => date('Y-m-d H:i:s')
                        );
                        $this->_recordUserDbTable->insert($insert);
                    }
                }
                if (!is_null($ldapUser)) {
                    foreach ($ldapUser as $ldapsU) {
                        $insert = array(
                            'record_id' => $eventId,
                            'user_id' => $ldapsU,
                            'create_date' => date('Y-m-d H:i:s')
                        );
                        $this->_recordUserDbTable->insert($insert);
                    }
                }
            }
        }


        $eventInfo = $this->_recordDbTable->findEventById($eventId);
        $lGroup = $this->_groupDbTable->findGroupByAuth(1, null, null, $eventId);
        $ldapG = $this->_groupDbTable->findGroupByAuth(2, null, null, $eventId);
        $lUser = $this->_userDbTable->findUserByAuth(1, null, $eventId);
        $ldapUser = $this->_userDbTable->findUserByAuth(2, null, $eventId);

        $eventInfo = (!is_null($eventInfo) ? $eventInfo->toArray() : array());
        $lGroup = (!is_null($lGroup) ? $lGroup->toArray() : array());
        $ldapG = (!is_null($ldapG) ? $ldapG->toArray() : array());
        $lUser = (!is_null($lUser) ? $lUser->toArray() : array());
        $ldapUser = (!is_null($ldapUser) ? $ldapUser->toArray() : array());


        $this->view->jQuery()->addOnload('$("#participantes-grupos-locais").select2();$("#participantes-grupos-ldap").select2();$("#participantes-usuarios-locais").select2();$("#participantes-usuarios-ldap").select2();$("#tags").select2();');
        $this->view->eventInfo = $eventInfo;
        $this->view->lGroup = $lGroup;
        $this->view->ldapG = $ldapG;
        $this->view->lUser = $lUser;
        $this->view->ldapUser = $ldapUser;
        
        $tags = $this->_recordTagDbTable->fetchAll(null,array('name'));
        $this->view->tags = ($tags != null ? $tags->toArray() : array());

    }

    public function deleteAction() {
        $this->_disableViewAndLayout();
        $eventId = $this->_getParam('recordId');
        if (is_null($eventId)) {
            $eventId = $this->_getParam('selecionados');
        }
        if (is_array($eventId)) {
            foreach ($eventId as $event) {
                $this->_recordDbTable->delete($this->_recordDbTable->getAdapter()->quoteInto('record_id=?', $event));
            }
        } else {
            $this->_recordDbTable->delete($this->_recordDbTable->getAdapter()->quoteInto('record_id=?', $eventId));
        }
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->json(array('redirectTo' => '/ui/recordings/index'));
        } else {
            $this->_helper->redirector->gotoRoute(array('module' => 'ui', 'controller' => 'recordings', 'action' => 'index'));
        }
    }

}

