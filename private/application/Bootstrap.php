<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    private $_view;
    
    protected function _initDateCache() {
        $frontendOptions = array(
            'lifetime' => 600, // 10 minutes
            'automatic_serialization' => true
        );
        
        $cacheDir = APPLICATION_PATH . '/../tmp/cache';
        IMDT_Util_File::checkPath($cacheDir, true);

        $backendOptions = array('cache_dir' => $cacheDir);

        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        Zend_Date::setOptions(array('cache' => $cache));
    }

    protected function _initSession() {
        $sessionSavePath = APPLICATION_PATH .  DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'session';
        IMDT_Util_File::checkPath($sessionSavePath, true);
        $opcoes = array(
            'save_path' => $sessionSavePath,
            'strict' => 'on'
        );
        Zend_Session::setOptions($opcoes);
        $sid = isset($_POST['PHPSESSID']) ? $_POST['PHPSESSID'] : '';
        if (!empty($sid)) {
            Zend_Session::setId($sid);
        }
        Zend_Session::start();
    }
    
    protected function _initRouter(){
	/*$this->bootstrap('locale');
	
	$router = Zend_Controller_Front::getInstance()->getRouter();
	$router->addDefaultRoutes();
	
	$defaultRoute = $router->getRoute('default');
	$dynamicRoute = new IMDT_Controller_Router_DynamicRoute();
	
	//$router->addRoute('dynamic', $defaultRoute->chain($dynamicRoute));*/
	$router = Zend_Controller_Front::getInstance()->getRouter();
	$dynamicRoute = new IMDT_Controller_Router_DynamicRoute();
	$router->addRoute('dynamic', $dynamicRoute);
    }
    
    protected function _initActionHelpers(){
        Zend_Controller_Action_HelperBroker::addHelper(new IMDT_Controller_Helper_Params());
        Zend_Controller_Action_HelperBroker::addHelper(new IMDT_Controller_Helper_RestContexts());
        Zend_Controller_Action_HelperBroker::addHelper(new IMDT_Controller_Helper_ContextSwitch());
        Zend_Controller_Action_HelperBroker::addHelper(new IMDT_Controller_Helper_Translate());
    }
    /*
    protected function _initActionHelpers(){
        $contextSwitch = new REST_Controller_Action_Helper_ContextSwitch();
        Zend_Controller_Action_HelperBroker::addHelper($contextSwitch);

        $restContexts = new REST_Controller_Action_Helper_RestContexts();
        Zend_Controller_Action_HelperBroker::addHelper($restContexts);
    }*/

    protected function _initErrorHandler() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(
            array(
                'module' => 'error',
                'controller' => 'error',
                'action' => 'error'
            )
        ));
		
        $front->registerPlugin(new BBBManager_Plugin_ErrorControllerSwitcher());
    }
	
    protected function _initViewHelpers() {
        $this->bootstrap('view');
        $this->_view = $this->getResource('view');
		
		$this->_view->addHelperPath('EasyBib/View/Helper', 'EasyBib_View_Helper');
    }
    
    protected function _initResourceLoader(){
        $this->_resourceLoader->addResourceType( 'cache', 'cache/', 'Cache' );
        $this->_resourceLoader->addResourceType( 'config', 'configs/', 'Config' );
        $this->_resourceLoader->addResourceType( 'util', 'utils/', 'Util' );
    }

    protected function _initViewHeadDoctype() {
        $this->_view->doctype('HTML5');
    }

    protected function _initViewHeadTitle() {
	
	$defaultTitle = 'Missing or invalid skinning.ini file';
	
	if(BBBManager_Util_Skinning::getInstance()->get('system_title') != null){
	    $defaultTitle = BBBManager_Util_Skinning::getInstance()->get('system_title');
	}
	
	if(BBBManager_Util_Skinning::getInstance()->get('company_name') != null){
	    $defaultTitle .= ' ' . BBBManager_Util_Skinning::getInstance()->get('company_name');
	}
	
        $this->_view->headTitle()->exchangeArray(array());
        $this->_view->headTitle($defaultTitle)
                    ->setSeparator(' | ');
    }

    protected function _initViewHeadMeta() {
        $this->_view->headMeta()->exchangeArray(array());
        $this->_view->headMeta()->setCharset('UTF-8')
                ->setHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
                ->setHttpEquiv('Content-Language', 'pt-BR')
                ->setHttpEquiv('X-UA-Compatible', 'IE=edge,chrome=1')
                ->setName('keywords', '')
                ->setName('description', '')
                ->setName('author', 'iMDT - Negócios Inteligentes <contato@imdt.com.br>')
                ->setName('viewport', 'width=device-width')
                ->setName('og:title', '')
                ->setName('og:type', 'website')
                ->setName('og:site_name', 'IMDb')
                ->setName('og:description', '');
    }

    protected function _initJquery() {
        $this->_view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");

        $this->_view->jQuery()->setLocalPath($this->_view->baseUrl('/resources/js/jquery/jquery-1.8.2.min.js'))
                              ->setUiLocalPath($this->_view->baseUrl('/resources/js/jquery/ui/jquery.ui.custom.min-1.9.1.js'));

        ZendX_JQuery::enableView($this->_view);

        $this->_view->jQuery()->enable();
        $this->_view->jQuery()->uiEnable();
    }

    protected function _initCss() {
        $this->_view->headLink()->exchangeArray(array());
        $this->_view->headLink()
                    ->appendStylesheet($this->_view->baseUrl('/resources/css/bootstrap/bootstrap.min.css'))
                    ->appendStylesheet($this->_view->baseUrl('/resources/css/bootstrap/bootstrap-responsive.min.css'))
                    ->appendStylesheet($this->_view->baseUrl('/resources/css/bootstrap/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css'))
                    ->appendStylesheet($this->_view->baseUrl('/resources/js/jquery/plugins/select2/select2.css'))
                    ->appendStylesheet($this->_view->baseUrl('/resources/js/jquery/plugins/datatables/css/jquery.dataTables.css'))
                    ->appendStylesheet($this->_view->baseUrl('/resources/js/jquery/plugins/treetable/css/jquery.treeTable.2.3.0.css'))
                    ->appendStylesheet($this->_view->baseUrl('/resources/css/imdt-select2-bootstrap.css'))
                    ->appendStylesheet($this->_view->baseUrl('/resources/css/style.css'))
		    ->appendStylesheet($this->_view->baseUrl('/resources/css/bootstrap/bootstrap.min.ie.css'),'screen', 'IE'); 
    }

    protected function _initJqueryPlugins() {
        $this->_view->jQuery()
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/bootstrap/bootstrap.min.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/bootstrap/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js'))
		        ->addJavascriptFile($this->_view->baseUrl('/resources/js/bootstrap/bootstrap-datetimepicker/bootstrap-datetimepicker.pt-BR.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/jquery/plugins/select2/select2.min.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/jquery/plugins/select2/select2_locale_pt-BR.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/ckeditor/ckeditor.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/jquery/plugins/datatables/jquery.dataTables.min.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/jquery/plugins/treetable/jquery.treeTable.2.3.0.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/jquery/plugins/maskedinput/jquery.maskedinput.min.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/xml2json/xml2json.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/jquery/ui/jquery.ui.widget.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/jquery/plugins/fileupload/jquery.iframe-transport.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/jquery/plugins/fileupload/jquery.fileupload.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/jquery/plugins/autosize/jquery.autosize.min.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/jquery/plugins/filedownload/jquery.fileDownload.js'))
                ->addJavascriptFile($this->_view->baseUrl('/assets/langjs'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/app.js'))
                ->addJavascriptFile($this->_view->baseUrl('/resources/js/main.js'));
                /*->addStylesheet($this->_view->baseUrl('/resources/js/jquery/shadowbox/jquery.shadowbox-3.0.3.css'));*/
    }
    
    protected function _initJavascript(){
        /*$this->_view->headScript()->appendFile($this->view->baseUrl('/resources/js/bootstrap/bootstrap-alert.js'))
                                  ->appendFile($this->view->baseUrl('/resources/js/bootstrap/bootstrap-button.js'))
                                  ->appendFile($this->view->baseUrl('/resources/js/bootstrap/bootstrap-carousel.js'))
                                  ->appendFile($this->view->baseUrl('/resources/js/bootstrap/bootstrap-collapse.js'))
                                  ->appendFile($this->view->baseUrl('/resources/js/bootstrap/bootstrap-dropdown.js'))
                                  ->appendFile($this->view->baseUrl('/resources/js/bootstrap/bootstrap-modal.js'))
                                  ->appendFile($this->view->baseUrl('/resources/js/bootstrap/bootstrap-popover.js'))
                                  ->appendFile($this->view->baseUrl('/resources/js/bootstrap/bootstrap-scrollspy.js'))
                                  ->appendFile($this->view->baseUrl('/resources/js/bootstrap/bootstrap-tab.js'))
                                  ->appendFile($this->view->baseUrl('/resources/js/bootstrap/bootstrap-tooltip.js'))
                                  ->appendFile($this->view->baseUrl('/resources/js/bootstrap/bootstrap-transition.js'))
                                  ->appendFile($this->view->baseUrl('/resources/js/bootstrap/bootstrap-typeahead.js'))
                                  ->appendFile($this->view->baseUrl('/resources/js/bootstrap/bootstrap.min.js'));*/
    }

    protected function _initDefaultLayout() {
        $layout = Zend_Layout::startMvc();

        $layout->setLayoutPath(APPLICATION_PATH . '/views/scripts/layout')->setLayout('main');
        
        return $layout;
    }
}