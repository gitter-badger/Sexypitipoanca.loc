<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initRoutes()
    {
        include APPLICATION_PATH . "/configs/routes.php";
    }

	protected function _initView() {
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$view->doctype('HTML5');
	}
	
    protected function _initAutoload()
    {
		Zend_Loader_Autoloader::getInstance()->registerNamespace('J_');
		Zend_Loader_Autoloader::getInstance()->registerNamespace('TS_');
        $autoLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default_',
            'basePath'  => dirname(__FILE__),
        ));
        return $autoLoader;
    }
}