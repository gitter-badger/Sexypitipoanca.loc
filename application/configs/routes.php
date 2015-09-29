<?php
/**
 * Created by PhpStorm.
 * User: tsergium
 * Date: 10/1/2015
 * Time: 7:37 AM
 */

$router = Zend_Controller_Front::getInstance()->getRouter();

/**
 * old tags to new route
 */
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

/**
 * article details route
 */
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

/**
 * category route with pagination
 */
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

/**
 * tag cloud route
 */
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

/**
 * tags route with pagination
 */
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

/**
 * wall route
 */
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

/**
 * friends route
 */
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

/**
 * top rated galleries route
 */
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

/**
 * clips added route
 */
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

/**
 * added galleries route
 */
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

/**
 * tags route
 */
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

/**
 * favorite galleries route
 */
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