<?php
ini_set('display_errors', 'on');
ini_set('error_reporting', E_ALL);

try {
	defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../private/application'));	
	defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
	set_include_path(implode(PATH_SEPARATOR, array(realpath(APPLICATION_PATH . '/../library'))));
	require_once 'Zend/Application.php';
	$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
	$application->bootstrap();
        
        /*
            'port'          => '23306',
            'password'      => 'fnocb3w', 
         */
        
        $adapter = Zend_Db::factory('pdo_mysql',array(
            'host'          => '10.30.10.10',
            'port'          => '33306',
            'database'      => 'bbb',
            'username'      => 'bbb',
            'password'      => 'bbb',
            'dbname'        => 'bbb'
        ));
        
        Zend_Db_Table_Abstract::setDefaultAdapter($adapter);
        
        $model = new Zend_Db_Table('access_log_description');
        
        $rLogDescriptions = array(
            array(
                'controller'    => 'access-logs',
                'action'        => 'index',
                'description'   => 'Visualização de logs do sistema'
            ),
            array(
                'controller'    => 'access-profiles',
                'action'        => 'index',
                'description'   => 'Obtenção dos perfis de acesso'
            ),
            array(
                'controller'    => 'error',
                'action'        => 'index',
                'description'   => 'Tela de erro'
            ),
            array(
                'controller'    => 'groups',
                'action'        => 'delete',
                'description'   => 'Exclusão de grupo'
            ),
            array(
                'controller'    => 'groups',
                'action'        => 'get',
                'description'   => 'Visualização de grupo'
            ),
            array(
                'controller'    => 'groups',
                'action'        => 'index',
                'description'   => 'Listagem de grupos'
            ),
            array(
                'controller'    => 'groups',
                'action'        => 'post',
                'description'   => 'Criação de grupo'
            ),
            array(
                'controller'    => 'groups',
                'action'        => 'put',
                'description'   => 'Atualização de grupo'
            ),
            array(
                'controller'    => 'invite-templates',
                'action'        => 'delete',
                'description'   => 'Exclusão de modelo de convite'
            ),
            array(
                'controller'    => 'invite-templates',
                'action'        => 'get',
                'description'   => 'Visualização de modelo de convite'
            ),
            array(
                'controller'    => 'invite-templates',
                'action'        => 'index',
                'description'   => 'Listagem de modelos de convite'
            ),
            array(
                'controller'    => 'invite-templates',
                'action'        => 'post',
                'description'   => 'Criação de modelo de convite'
            ),
            array(
                'controller'    => 'invite-templates',
                'action'        => 'put',
                'description'   => 'Atualização de modelo de convite'
            ),
            array(
                'controller'    => 'index',
                'action'        => 'index',
                'description'   => 'Tela inicial do sistema'
            ),
            array(
                'controller'    => 'login',
                'action'        => 'index',
                'description'   => 'Efetuou login'
            ),
            array(
                'controller'    => 'maintenance',
                'action'        => 'index',
                'description'   => 'Modo de manutenção'
            ),
            array(
                'controller'    => 'my-rooms',
                'action'        => 'get',
                'description'   => 'Entrada em sala'
            ),
            array(
                'controller'    => 'my-rooms',
                'action'        => 'index',
                'description'   => 'Listagem das minhas salas'
            ),
            array(
                'controller'    => 'record-tags',
                'action'        => 'delete',
                'description'   => 'Exclusão de tag'
            ),
            array(
                'controller'    => 'record-tags',
                'action'        => 'get',
                'description'   => 'Visualização de tag'
            ),
            array(
                'controller'    => 'record-tags',
                'action'        => 'index',
                'description'   => 'Listagem de tags'
            ),
            array(
                'controller'    => 'record-tags',
                'action'        => 'post',
                'description'   => 'Criação de tag'
            ),
            array(
                'controller'    => 'record-tags',
                'action'        => 'put',
                'description'   => 'Atualização de tag'
            ),
            array(
                'controller'    => 'room-actions',
                'action'        => 'index',
                'description'   => 'Lista de tipos de eventos da sala'
            ),
            array(
                'controller'    => 'room-by-url',
                'action'        => 'get',
                'description'   => 'Busca de sala por url'
            ),
            array(
                'controller'    => 'room-by-url',
                'action'        => 'index',
                'description'   => 'Consulta de salas por url'
            ),
            array(
                'controller'    => 'room-invites',
                'action'        => 'get',
                'description'   => 'Visualização de convite'
            ),
            array(
                'controller'    => 'room-invites',
                'action'        => 'post',
                'description'   => 'Envio de convite'
            ),
            array(
                'controller'    => 'room-logs',
                'action'        => 'index',
                'description'   => 'Histórico da sala'
            ),
            array(
                'controller'    => 'rooms',
                'action'        => 'delete',
                'description'   => 'Exclusão de sala'
            ),
            array(
                'controller'    => 'rooms',
                'action'        => 'get',
                'description'   => 'Visualização de sala'
            ),
            array(
                'controller'    => 'rooms',
                'action'        => 'index',
                'description'   => 'Listagem de salas'
            ),
            array(
                'controller'    => 'rooms',
                'action'        => 'post',
                'description'   => 'Criação de sala'
            ),
            array(
                'controller'    => 'rooms',
                'action'        => 'put',
                'description'   => 'Atualização de sala'
            ),
            array(
                'controller'    => 'rooms-audience',
                'action'        => 'index',
                'description'   => 'Relatório de audiência'
            ),
            array(
                'controller'    => 'security',
                'action'        => 'index',
                'description'   => 'Obtenção de regras de ACL'
            ),
            array(
                'controller'    => 'users-reset-password',
                'action'        => 'index',
                'description'   => 'Geração de nova senha'
            ),
            array(
                'controller'    => 'speed-profiles',
                'action'        => 'delete',
                'description'   => 'Exclusão de perfil de velocidade'
            ),
            array(
                'controller'    => 'speed-profiles',
                'action'        => 'get',
                'description'   => 'Visualização de perfil de velocidade'
            ),
            array(
                'controller'    => 'speed-profiles',
                'action'        => 'index',
                'description'   => 'Listagem de perfil de velocidade'
            ),
            array(
                'controller'    => 'speed-profiles',
                'action'        => 'post',
                'description'   => 'Criação de perfil de velocidade'
            ),
            array(
                'controller'    => 'speed-profiles',
                'action'        => 'put',
                'description'   => 'Atualização de perfil de velocidade'
            ),
            array(
                'controller'    => 'users',
                'action'        => 'delete',
                'description'   => 'Exclusão de usuário'
            ),
            array(
                'controller'    => 'users',
                'action'        => 'get',
                'description'   => 'Visualização de usuário'
            ),
            array(
                'controller'    => 'users',
                'action'        => 'index',
                'description'   => 'Listagem de usuários'
            ),
            array(
                'controller'    => 'users',
                'action'        => 'post',
                'description'   => 'Criação de usuário'
            ),
            array(
                'controller'    => 'users',
                'action'        => 'put',
                'description'   => 'Atualização de usuário'
            )
        );
        
        foreach($rLogDescriptions as $logDescription){
            $model->insert($logDescription);
        }
        
} catch (Exception $e) {
	echo "<pre>ERRO: ".$e->getMessage();
	die;
}
