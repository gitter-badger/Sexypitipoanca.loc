<?php
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Define path to application directory
defined('APPLICATION_PUBLIC_PATH') || define('APPLICATION_PUBLIC_PATH', realpath(dirname(__FILE__)));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'staging'));

//DEFINE THEME PATH
defined('THEME_PATH') || define('THEME_PATH', APPLICATION_PUBLIC_PATH.'/theme/default/');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
	APPLICATION_PUBLIC_PATH . '/library',
	APPLICATION_PUBLIC_PATH . '/library/App/lib',
//	get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
	APPLICATION_ENV,
	APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()
			->run();