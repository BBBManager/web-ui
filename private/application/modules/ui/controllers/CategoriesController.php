<?php

class Ui_CategoriesController extends IMDT_Controller_Abstract {

    protected $_meetingRoomCategoryDbTable;

    public function init() {
        $this->_meetingRoomCategoryDbTable = new Ui_Model_MeetingRoomCategory();
        $this->view->headScript()->appendFile($this->view->baseUrl('/resources/js/categorias.js'));
    }

    public function indexAction() {

        $this->view->arrCategorias = array(
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
            ),
            array(
                'id' => 1,
                'description' => 'Treinamentos',
                'parentId' => ''
            ),
array(
                'id' => 15,
                'description' => 'Administrativos',
                'parentId' => 1,
                'hierarchy' => '1'
            ),
            array(
                'id' => 17,
                'description' => 'CIPA',
                'parentId' => 15,
                'hierarchy' => '1-15'
            ),
            array(
                'id' => 16,
                'description' => 'Gestão',
                'parentId' => 15,
                'hierarchy' => '1-15'
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
                'id' => 18,
                'description' => 'Integração',
                'parentId' => 1,
                'hierarchy' => '1'
            )
        );

        $nomeCategorias = array();

        foreach ($this->view->arrCategorias as $categoria) {
            $nomeCategorias[] = $categoria['description'];
        }

        $this->view->nomeCategorias = $nomeCategorias;
    }

    public function editAction() {

        $catId = $this->_getParam('catId', null);
        $parentId = $this->_getParam('parentId', null);
        $isEdit = is_null($catId) ? false : true;

        if (!$isEdit) {
            $insert = array(
                'name' => $this->_getParam('catName', null),
                'create_date' => date("Y-m-d H:i:s"),
                'parent_id' => $parentId
            );
            $newRoomId = $this->_meetingRoomCategoryDbTable->insert($insert);
            if ($this->_request->isXmlHttpRequest()) {
                $this->addMessage(array('success' => 'Categoria adicionda com sucesso'));
                $this->_helper->json(array('redirectTo' => '/categories/index'));
            }
        }
    }

}

