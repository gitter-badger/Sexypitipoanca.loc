<?php
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    protected $namespaces = ['Base_', 'ZG_', 'J_', 'TS_'];

    protected function _initAutoload()
    {
        foreach ($this->namespaces as $namespace) {
            Zend_Loader_Autoloader::getInstance()->registerNamespace($namespace);
        }

        $autoLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Admin_',
            'basePath'  => dirname(__FILE__),
        ));
        return $autoLoader;
    }
}