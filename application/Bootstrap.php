<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected $namespaces = ['J_', 'TS_'];

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
        foreach ($this->namespaces as $namespace) {
            Zend_Loader_Autoloader::getInstance()->registerNamespace($namespace);
        }

        $autoLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default_',
            'basePath'  => dirname(__FILE__),
        ));
        return $autoLoader;
    }
}