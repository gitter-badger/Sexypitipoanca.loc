<?php
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    protected function _initAutoload()
    {
		Zend_Loader_Autoloader::getInstance()->registerNamespace('ZG_');
		Zend_Loader_Autoloader::getInstance()->registerNamespace('J_');
		Zend_Loader_Autoloader::getInstance()->registerNamespace('TS_');
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Admin_',
            'basePath'  => dirname(__FILE__),
        ));
        return $autoloader;
    }
}