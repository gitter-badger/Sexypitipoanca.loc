<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
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

//BEGIN: ROUTER ROUTE REGEX
	$ctrl  = Zend_Controller_Front::getInstance();
	$router = $ctrl->getRouter();
	// BEGIN: Redirect old to new tags
	$rewriteTags = new Zend_Controller_Router_Route_Regex(
		'taguri/([^_]*)\.html',
		array(
			'module'=>'default',
			'controller'=>'redirect',
			'action'=>'new-tags',
		),
		array(
			1 => 'tag'
		),
		'taguri/%s.html'
	);
	$router->addRoute('rewriteTags', $rewriteTags);
	// END: Redirect old to new tags

	// Article details
	$article = new Zend_Controller_Router_Route_Regex(
	'([^_]*)/([^_]*)-([^_]*)\.html',
	array(
			'module' => 'default',
			'controller' => 'catalog',
			'action' => 'product-details'
	),
	array(
			1 => 'categoryName',
			2 => 'productName',
			3 => 'id'
	),
	'%s/%s-%d.html'
	);
	$router->addRoute('product', $article);

	// BEGIN: CATEGORY
	$article = new Zend_Controller_Router_Route_Regex(
	'categorii/([^_]*)-([^_]*)\.html',
	array(
			'module' => 'default',
			'controller' => 'catalog',
			'action' => 'categories'
	),
	array(
			1 => 'categoryName',
			2 => 'id'
	),
	'categorii/%s-%d.html'
	);
	$router->addRoute('categoryDISABLED', $article);
	// END: CATEGORY
	//BEGIN: PRODUCT CATEGORY ROUTE WITH PAGINATION
	$testRoute = new Zend_Controller_Router_Route_Regex(
		'categorii/([^_]*)-([^-]*)/pagina-([^-]*)\.html',
		array(
			'module'=>'default',
			'controller'=>'catalog',
			'action'=>'categories',
			'page'=>'1'
		),
		array(
			1 => 'categoryName',
			2 => 'id',
			3 => 'page'
		),
		'categorii/%s-%d/pagina-%d.html'
	);
	$router->addRoute('category', $testRoute);
	//END: PRODUCT CATEGORY ROUTE WITH PAGINATION
	// BEGIN: TAG CLOUD
	$article = new Zend_Controller_Router_Route_Regex(
	'tag-cloud/([^_]*)\.html',
	array(
			'module' => 'default',
			'controller' => 'catalog',
			'action' => 'cloudtag'
	),
	array(
			1 => 'tag',
	),
	'tag-cloud/%s.html'
	);
	$router->addRoute('cloudtag', $article);
	// END: TAG CLOUD

	// BEGIN: Tags route with pagination
	$tagsRoute = new Zend_Controller_Router_Route_Regex(
		'taguri/([^_]*)/pagina-([^-]*)\.html',
		array(
			'module'=>'default',
			'controller'=>'catalog',
			'action'=>'tags',
			'page'=>'1'
		),
		array(
			1 => 'tag',
			2 => 'page'
		),
		'taguri/%s/pagina-%d.html'
	);
	$router->addRoute('tag', $tagsRoute);
	// END: Tags route with pagination

	// BEGIN: WALL
	$wall = new Zend_Controller_Router_Route_Regex(
	'user/([^_]*)',
	array(
			'module' => 'default',
			'controller' => 'account',
			'action' => 'wall'
	),
	array(
			1 => 'username',
	),
	'user/%s'
	);
	$router->addRoute('wall', $wall);
	// END: WALL
	/////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////
	// BEGIN: FRIENDS
	$friends = new Zend_Controller_Router_Route_Regex(
	'friends/([^_]*)',
	array(
			'module' => 'default',
			'controller' => 'account',
			'action' => 'prieteni'
	),
	array(
			1 => 'username',
	),
	'friends/%s'
	);
	$router->addRoute('friends', $friends);
	// END: FRIENDS
	/////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////
	// BEGIN: Top rated galleries
	$friends = new Zend_Controller_Router_Route_Regex(
	'score',
	array(
			'module' => 'default',
			'controller' => 'catalog',
			'action' => 'score'
	),
	/*array(
			1 => '',
	),*/
	'score'
	);
	$router->addRoute('score', $friends);
	// END: Top rated galleries
	/////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////
	// BEGIN: Clipuri adaugate
	$clipuri_adaugate = new Zend_Controller_Router_Route_Regex(
	'clipuri-adaugate/([^_]*)',
	array(
			'module' => 'default',
			'controller' => 'account',
			'action' => 'clipuri'
	),
	array(
			1 => 'username',
	),
	'clipuri-adaugate/%s'
	);
	$router->addRoute('clipuri-adaugate', $clipuri_adaugate);
	// END: Clipuri adaugate
	/////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////
	// BEGIN: Galerii adaugate
	$galerii_adaugate = new Zend_Controller_Router_Route_Regex(
	'galerii-adaugate/([^_]*)/pagina-([^-]*)\.html',
	array(
			'module' => 'default',
			'controller' => 'account',
			'action'    => 'galerii',
            'page'      => 1
	),
	array(
			1 => 'username',
            2 => 'page'
	),
	'galerii-adaugate/%s/pagina-%d.html'
	);
	$router->addRoute('galerii-adaugate', $galerii_adaugate);
	// END: Galerii adaugate
$tagsRoute = new Zend_Controller_Router_Route_Regex(
    'taguri/([^_]*)/pagina-([^-]*)\.html',
    array(
        'module'=>'default',
        'controller'=>'catalog',
        'action'=>'tags',
        'page'=>'1'
    ),
    array(
        1 => 'tag',
        2 => 'page'
    ),
    'taguri/%s/pagina-%d.html'
);
$router->addRoute('tag', $tagsRoute);

	/////////////////////////////////////////////////////////
	// BEGIN: Galerii favorite
	$favorites_galleries = new Zend_Controller_Router_Route_Regex(
	'favorite/([^_]*)',
	array(
			'module' => 'default',
			'controller' => 'account',
			'action' => 'favorites'
	),
	array(
			1 => 'username',
	),
	'favorite/%s'
	);
	$router->addRoute('favorite', $favorites_galleries);
	// END: Galerii favorite
	/////////////////////////////////////////////////////////
//END: ROUTER ROUTE REGEX