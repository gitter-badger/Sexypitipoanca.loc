<?php
class App_Controller_Plugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
		// GET MODULE/CONTROLLER/ACTION
		$module			= $request->getModuleName();
		$controller		= $request->getControllerName();
		$action			= $request->getActionName();

		// BEGIN: Send module/controller/action
		$layout	= Zend_Layout::getMvcInstance();
		$layout->getView()->module			= $module;
		$layout->getView()->controller		= $controller;
		$layout->getView()->action			= $action;
		// END: Send module/controller/action
		
		// BEGIN: Test messages
//		$to = 8;
//		$from = 1;
//		$message = "Lorem ipsum dolor sit amet";
//		TS_Message::sendMessage($to, $from, $message);
		// END: Test messages
		
		// BEGIN: Send constants
		$option = new Default_Model_Options();
		$select = $option->getMapper()->getDbTable()->select();
		$options = $option->fetchAll($select);
		if($options)
		{
			foreach($options as $value)
			{
				defined($value->getName()) || define($value->getName(), $value->getValue());
			}
		}
		// END: Send constants

		// BEGIN: Adjust duplicate content
		if($module == 'default' && $controller == 'catalog' && $action == 'cloudtag')
		{
			$tag = $this->getRequest()->getParam('tag');
			$layout->getView()->canonical = "<link rel='canonical' href='http://sexypitipoanca.ro/taguri/{$tag}.html' />";
		}
		// END: Adjust duplicate content

		$acl = new Zend_Acl();
		$acl->add(new Zend_Acl_Resource('default:auth'));
		$acl->add(new Zend_Acl_Resource('admin:auth'));
		$acl->add(new Zend_Acl_Resource('admin:index'));
		$acl->add(new Zend_Acl_Resource('admin:dashboard'));
		$acl->add(new Zend_Acl_Resource('admin:catalog'));
		$acl->add(new Zend_Acl_Resource('admin:user'));
		$acl->add(new Zend_Acl_Resource('admin:comments'));
		$acl->add(new Zend_Acl_Resource('admin:newsletter'));
		$acl->add(new Zend_Acl_Resource('admin:cms'));
		$acl->add(new Zend_Acl_Resource('admin:settings'));
		$acl->add(new Zend_Acl_Resource('admin:test'));
		$acl->add(new Zend_Acl_Resource('admin:marketing'));
		$acl->add(new Zend_Acl_Resource('admin:notifications'));

		$acl->addRole(new Zend_Acl_Role('guest'));
		$acl->addRole(new Zend_Acl_Role('admin'));
		$acl->addRole(new Zend_Acl_Role('user'));

		$acl->allow('guest', 'admin:auth', 'login');
		$acl->allow('guest', 'default:auth', 'login');

		$acl->deny('admin', 'admin:auth', 'login');
		$acl->deny('admin', 'default:auth', 'login');
		$acl->allow('admin');

		$accountRole = 'guest';
		if($module == 'admin') {
			$auth = Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('admin'));
		} else {
			$auth = Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('user'));
		}
		if($auth->hasIdentity()) {
			$accountAuth = $auth->getStorage()->read();
			if(null !== $accountAuth->getRole()) {
				$accountRole = $accountAuth->getRole()->getName();
			}
		}

		$galleryName = "";
		if($module == "admin" && $controller == "catalog" && $action == "products-details"){
			$id = $this->getRequest()->getParam('id');
			if(null != $id){
				$catalog = new Default_Model_CatalogProducts();
				if($catalog->find($id)){
					$galleryName = $catalog->getName();
				}
			}
		}

		if(!function_exists('getUserById')){
			function getUserById($userId){
				if(null != $userId){
					$model = new Default_Model_AccountUsers();
					if($model->find($userId)){
						return $model;
					}else{
						return null;
					}
				}else{
					return null;
				}
			}
		}

		// BEGIN: STATISTICA
		if($module == 'default'){
			$tsStatistics = new TS_Statistics();
			$reconectUser = $tsStatistics->findStatistics();
			if(null != $reconectUser){
				$statisticsId = $reconectUser->getId();
			}else{
				$statisticsId = $tsStatistics->setStatistics();
			}
			
			if($controller == 'catalog' && $action == 'product-details' && $statisticsId != false){
				$productId = $this->getRequest()->getParam('id');
				if(null != $productId){
					$tsStatistics->setStatisticsVisit($statisticsId, $productId);
				}
			}
		}
		// END: STATISTICA

    	switch($module){
    		case 'admin' :
				$layout->setLayout('admin');
				$pages = array(
					///////BEGIN: DASHBOARD///////
					array(
						'label'			=> 'Dashboard',
						'title'			=> 'Dashboard',
						'module'		=> 'admin',
						'controller'	=> 'dashboard',
						'resource'		=> 'admin:dashboard',
					),
					///////END: DASHBOARD///////
					//
					///////END: GALLERY///////
					array(
						'label'			=> 'Administrare catalog',
						'title'			=> 'Administrare catalog',
						'module'		=> 'admin',
						'controller'	=> 'catalog',
						'resource'		=> 'admin:catalog',
						'pages'			=> array(
							array(
								'label'			=> 'Catalog',
								'title'			=> 'Catalog',
								'module'		=> 'admin',
								'controller'	=> 'catalog',
								'action'		=> 'index',
								'pages'			=> array(
									array(
										'label'			=> 'Adauga galerie',
										'title'			=> 'Adauga galerie',
										'module'		=> 'admin',
										'controller'	=> 'catalog',
										'action'		=> 'products-add',
										'visible'		=> false,
									),
									array(
										'label'			=> 'Adauga video',
										'title'			=> 'Adauga video',
										'module'		=> 'admin',
										'controller'	=> 'catalog',
										'action'		=> 'products-add-video',
										'visible'		=> false,
									),
									array(
										'label'			=> 'Modifica galeria',
										'title'			=> 'Modifica galeria',
										'module'		=> 'admin',
										'controller'	=> 'catalog',
										'action'		=> 'products-edit',
										'visible'		=> false,
									),
									array(
										'label'			=> $galleryName,
										'title'			=> $galleryName,
										'module'		=> 'admin',
										'controller'	=> 'catalog',
										'action'		=> 'products-details',
										'visible'		=> false,
									),
									array(
										'label'			=> 'Gallery info',
										'title'			=> 'Gallery Info',
										'module'		=> 'admin',
										'controller'	=> 'catalog',
										'action'		=> 'info',
										'visible'		=> false,
									),
								),
							),
							array(
								'label'			=> 'Categorii',
								'title'			=> 'Categorii',
								'module'		=> 'admin',
								'controller'	=> 'catalog',
								'action'		=> 'categories',
								'resource'		=> 'admin:catalog',
								'pages'			=> array(
									array(
										'label'			=> 'add',
										'title'			=> 'add',
										'module'		=> 'admin',
										'controller'	=> 'catalog',
										'action'		=> 'categories-add',
										'resource'		=> 'admin:catalog',
										'visible'		=> false
									),
									array(
										'label'			=> 'edit',
										'title'			=> 'edit',
										'module'		=> 'admin',
										'controller'	=> 'catalog',
										'action'		=> 'categories-edit',
										'resource'		=> 'admin:catalog',
										'visible'		=> false
									),
								),
							),
							array(
										'label'			=> 'Tags',
										'title'			=> 'Tags',
										'module'		=> 'admin',
										'controller'	=> 'catalog',
										'action'		=> 'tags',
										'resource'		=> 'admin:catalog',
										'pages'			=> array(
											array(
												'label'			=> 'Edit tag',
												'title'			=> 'Edit tag',
												'module'		=> 'admin',
												'controller'	=> 'catalog',
												'action'		=> 'tagsedit',
												'resource'		=> 'admin:catalog',
												'visible'		=> false
											),
										),
							),
							array(
								'label'			=> 'Import',
								'title'			=> 'Import',
								'module'		=> 'admin',
								'controller'	=> 'catalog',
								'action'		=> 'import',
								'visible'		=> true,
							),
							array(
								'label'			=> 'Import Movie',
								'title'			=> 'Import Movie',
								'module'		=> 'admin',
								'controller'	=> 'catalog',
								'action'		=> 'import-movie',
								'visible'		=> true,
							),
						),
					),
					///////END: GALLERY///////
					//
					///////BEGIN: USERS///////
					array(
						'label'			=> 'Users',
						'title'			=> 'Users',
						'module'		=> 'admin',
						'controller'	=> 'user',
						'resource'		=> 'admin:user',
						'pages'			=> array(
							array(
								'label'			=> 'Manage Users',
								'title'			=> 'Manage Users',
								'module'		=> 'admin',
								'controller'	=> 'user',
								'action'		=> 'index',
								'resource'		=> 'admin:user',
								'pages'			=> array(
									array(
										'label'			=> 'User Details',
										'title'			=> 'User Details',
										'module'		=> 'admin',
										'controller'	=> 'user',
										'action'		=> 'details',
										'resource'		=> 'admin:user',
										'visible'		=> false,
									),
									array(
										'label'			=> 'Send message',
										'title'			=> 'Send message',
										'module'		=> 'admin',
										'controller'	=> 'user',
										'action'		=> 'message',
										'resource'		=> 'admin:user',
										'visible'		=> FALSE,
									),
								),
							),
						),
					),
					///////END: USERS///////
					//
					///////BEGIN: COMMENTS///////
					array(
						'label'			=> 'Comments',
						'title'			=> 'Comments',
						'module'		=> 'admin',
						'controller'	=> 'comments',
						'resource'		=> 'admin:comments',
						'pages'			=> array(
							array(
								'label'			=> 'Manage Comments',
								'title'			=> 'Manage Comments',
								'module'		=> 'admin',
								'controller'	=> 'comments',
								'action'		=> 'index',
								'resource'		=> 'admin:comments',
							),
							array(
								'label'			=> 'Moderate Comments',
								'title'			=> 'Moderate Comments',
								'module'		=> 'admin',
								'controller'	=> 'comments',
								'action'		=> 'edit',
								'resource'		=> 'admin:comments',
								'visible'		=> false,
							),
						),
					),
					///////END: COMMENTS///////
					///////BEGIN: NEWSLETTER///////
					array(
						'label'			=> 'Newsletter',
						'title'			=> 'Newsletter',
						'module'		=> 'admin',
						'controller'	=> 'newsletter',
						'resource'		=> 'admin:newsletter',
						'pages'			=> array(
							array(
								'label'			=> 'Subscribers',
								'title'			=> 'Subscribers',
								'module'		=> 'admin',
								'controller'	=> 'newsletter',
								'action'		=> 'subscribers',
								'resource'		=> 'admin:newsletter',
							),
						),
					),
					///////END: NEWSLETTER///////
					//
					///////BEGIN: SETTINGS///////
					array(
						'label'			=> 'Settings',
						'title'			=> 'Settings',
						'module'		=> 'admin',
						'controller'	=> 'settings',
						'resource'		=> 'admin:settings',
						'pages'			=> array(
							array(
								'label'			=> 'Website settings',
								'title'			=> 'Website settings',
								'module'		=> 'admin',
								'controller'	=> 'settings',
								'action'		=> 'index',
								'resource'		=> 'admin:settings',
							),
							array(
								'label'			=> 'Sitemap generator',
								'title'			=> 'Sitemap generator',
								'module'		=> 'admin',
								'controller'	=> 'settings',
								'action'		=> 'sitemapgenerator',
								'resource'		=> 'admin:settings',
							),
							array(
								'label'			=> 'Cache',
								'title'			=> 'Cache',
								'module'		=> 'admin',
								'controller'	=> 'settings',
								'action'		=> 'cache',
								'resource'		=> 'admin:settings',
								'pages'			=> array(
									array(
										'label'			=> 'Cache Times',
										'title'			=> 'Cache Times',
										'module'		=> 'admin',
										'controller'	=> 'settings',
										'action'		=> 'cache-times',
										'resource'		=> 'admin:settings',
										'visible'		=> false,
									),
								),
							),
							array(
								'label'			=> 'Generate thumbs',
								'title'			=> 'Generate thumbs',
								'module'		=> 'admin',
								'controller'	=> 'settings',
								'action'		=> 'thumbs',
								'resource'		=> 'admin:settings',
							),
							array(
								'label'			=> 'Player settings',
								'title'			=> 'Player settings',
								'module'		=> 'admin',
								'controller'	=> 'settings',
								'action'		=> 'player',
								'resource'		=> 'admin:settings',
							),
						),
					),
					///////END: SETTINGS///////
					//
					///////BEGIN: Marketing///////
					array(
						'label'			=> 'Marketing',
						'title'			=> 'Marketing',
						'module'		=> 'admin',
						'controller'	=> 'marketing',
						'resource'		=> 'admin:marketing',
						'pages'			=> array(
							array(
								'label'			=> 'Commercials',
								'title'			=> 'Commercials',
								'module'		=> 'admin',
								'controller'	=> 'marketing',
								'action'		=> 'index',
								'resource'		=> 'admin:marketing',
								'visible'		=> true,
							),
							array(
								'label'			=> 'Google analytics',
								'title'			=> 'Google analytics',
								'module'		=> 'admin',
								'controller'	=> 'marketing',
								'action'		=> 'analytics',
								'resource'		=> 'admin:marketing',
							),
						),
					),
					///////END: Marketing///////
					//
					///////BEGIN: NOTIFICATIONS///////
					array(
						'label'			=> 'Notifications',
						'title'			=> 'Notifications',
						'module'		=> 'admin',
						'controller'	=> 'notifications',
						'resource'		=> 'admin:notifications',
						'pages'			=> array(
							array(
								'label'			=> 'Send Notifications',
								'title'			=> 'Send Notifications',
								'module'		=> 'admin',
								'controller'	=> 'notifications',
								'action'		=> 'index',
								'resource'		=> 'admin:notifications',
							),
							array(
								'label'			=> 'Sentbox',
								'title'			=> 'Sentbox',
								'module'		=> 'admin',
								'controller'	=> 'notifications',
								'action'		=> 'sent',
								'resource'		=> 'admin:notifications',
							),
							array(
								'label'			=> 'Inbox',
								'title'			=> 'Inbox',
								'module'		=> 'admin',
								'controller'	=> 'notifications',
								'action'		=> 'inbox',
								'resource'		=> 'admin:notifications',
								'visible'		=> false,
								'pages'			=> array(
									array(
									'label'			=> 'Details',
									'title'			=> 'Details',
									'module'		=> 'admin',
									'controller'	=> 'notifications',
									'action'		=> 'details',
									'resource'		=> 'admin:notifications',
									'visible'		=> false,
									),
								),
							),

						),
					),
					///////END: NOTIFICATIONS///////
					//
					///////BEGIN: TEST///////
					array(
						'label'			=> 'Test',
						'title'			=> 'Test',
						'module'		=> 'admin',
						'controller'	=> 'test',
						'resource'		=> 'admin:test',
						'pages'			=> array(
							array(
								'label'			=> 'Twitter',
								'title'			=> 'Twitter',
								'module'		=> 'admin',
								'controller'	=> 'test',
								'action'		=> 'index',
								'resource'		=> 'admin:test',
							),
							array(
								'label'			=> 'Google weather',
								'title'			=> 'Google weather',
								'module'		=> 'admin',
								'controller'	=> 'test',
								'action'		=> 'weather',
								'resource'		=> 'admin:test',
							),
						),
					),
					///////END: TEST///////
					//
					///////BEGIN: LOGOUT///////
					array(
						'label'      => 'Logout',
						'title'      => 'Logout',
						'module'     => 'admin',
						'controller' => 'auth',
						'action'     => 'logout',
						'resource'	 => 'admin:auth',
						'privilege'	 => 'logout',
					),
					///////END: LOGOUT///////
				);

				// Create container from array
				$container = new Zend_Navigation($pages);
				$layout->getView()->navigation($container)->setAcl($acl)->setRole($accountRole);
				$layout->getView()->headTitle('Panou de administrare', 'SET');
				$layout->getView()->headLink()->prependStylesheet('/theme/admin/css/style-admin.css')
											  ->appendStylesheet('/theme/admin/js/jquery-ui/css/blitzer/jquery-ui-1.8.19.custom.css');
				$layout->getView()->headScript()->prependFile('/theme/default/js/jquery.js')
												->appendFile('/theme/default/js/jquery-ui.js')
												->appendFile('/theme/mediaplayer-5.9/jwplayer.min.js');

				switch($controller) {
					case 'error':
						switch($action) {
							case 'error' :
								$layout->setLayout('error');
								break;
							default:
								break;
						}
						break;
					case 'auth':
						$layout->setLayout('admin_auth');
						switch($action) {
							case 'login' :
								$layout->getView()->headTitle('Login', 'SET');
								if(!$acl->isAllowed($accountRole,'admin:auth', 'login')) {
									$this->_response->setRedirect('/admin/index');
								}
								break;
							default:
								break;
						}
						break;
					case 'export':
						$layout->setLayout('export');
						break;
					default :
						$layout->setLayout('admin');
						if(!$acl->isAllowed($accountRole,'admin:index')) {
							$this->_response->setRedirect('/admin/auth/login');
						}
						break;
				}
				break;

			default :
			$pages = array(
				//////INDEX//////
				array(
					'label'      => '<b><b>Home</b></b>',
					'title'      => 'Home',
					'module'     => 'default',
					'controller' => 'index',
					'action'	 => 'index',
					'pages'		 => array(
						array(
							'label'		=> 'a',
							'title'		=> 'a',
							'module'	=> 'default',
							'controller'=> 'catelog',
							'action'	=> 'categories',
							'param'		=> array('page'=>1),
						),
					),
				),
			);

			if($module == 'default')
			{
				// BEGIN: SEO SERP DYNAMIC PARAM
				$seoId = NULL;
				$seoPage = NULL;
				switch($controller){
					case 'index':
						switch ($action){
							case 'index':
								$seoPage = $this->getRequest()->getParam('page');
							break;
						}
					case "catalog":
						switch ($action){
							case "product-details": $seoId = $this->getRequest()->getParam('id');
							break;
							case "categories":
								$seoId = $this->getRequest()->getParam('id');
								$seoPage = $this->getRequest()->getParam('page');
							break;
							case "tags":
								$seoId = $this->getRequest()->getParam('tag');
								$seoPage = $this->getRequest()->getParam('page');
							break;
							case "addtofavorites":
								$seoId = $this->getRequest()->getParam('id');
							break;
							case "cloudtag": $seoId = $this->getRequest()->getParam('tag');
							break;
						}
					break;
				}
				// END: SEO SERP DYNAMIC PARAM
				
				// BEGIN: SEO
				require_once APPLICATION_PUBLIC_PATH.'/library/Needs/SeoSerp.php';
				$seoSerp = new SeoSerp();
				$seoSerp->setSeo($controller, $action, $seoId, $seoPage);
				// END: SEO

				$container = new Zend_Navigation($pages);				
				$layout->getView()->navigation($container)->setAcl($acl)->setRole($accountRole);
				$layout->getView()->headTitle($seoSerp->getTitle(), 'SET');
				$layout->getView()->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');

				$layout->getView()->headMeta()->appendName('description', $seoSerp->getDescription());
				$layout->getView()->headMeta()->appendName('keywords', $seoSerp->getKeywords());

				// BEGIN: Facebook share metas (og)
				if($module == 'default' && $controller == 'catalog' && $action == 'product-details'){
					$titleG = '';
					$firstI = '';
					$idG = $this->getRequest()->getParam('id');
					$model = new Default_Model_CatalogProducts();
					$select = $model->getMapper()->getDbTable()->select()
							->from(array('j_catalog_products'), array('name','type'))
							->where('id = ?', $idG);
					if(($result = $model->fetchAll($select))) {
						$titleG = $result[0]->getName();
						$type = $result[0]->getType();
						if($type == 'embed'){
							$firstI = TS_ToHtml::videoThumb($idG, NULL, 1);
						 }elseif($type == 'video'){
							 $firstI = TS_ToHtml::imageForVideo($idG,1);
						 }else{
						 	$firstI = 'http://'.$_SERVER['SERVER_NAME'].$tsStatistics->firstImage($idG);
						 }						
					}

					$productClass = new J_Product();
					$productClass->Product($idG, array('category'));

					$url = 'http://'.$_SERVER['SERVER_NAME'].'/'.preg_replace('/\s+/','-', strtolower($productClass->getCategory())).'/'.preg_replace('/\s+/','-', strtolower($productClass->getName())).'-'.$idG.'.html';
					$layout->getView()->facebookUrl = $url;
					$layout->getView()->ogUrl = $url;
					$layout->getView()->ogTitle = $titleG;
					$layout->getView()->ogImage = $firstI;
//					$layout->getView()->headMeta()->setProperty('og:url', $url);
//					$layout->getView()->headMeta()->setProperty('og:title', $titleG);
//					$layout->getView()->headMeta()->setProperty('og:type', 'website');
//					$layout->getView()->headMeta()->setProperty('og:site_name', 'SexyPitipoanca');
//					$layout->getView()->headMeta()->setProperty('og:image:', $firstI);
				}
				// END: Facebook share metas (og)
			}
				
				if($module == 'default' && $controller == 'iframe')
				{
					$layout->getView()->headLink()->appendStylesheet('/theme/default/css/iframe.css')
						   ->appendStylesheet('/theme/default/js/validationEngine/validationEngine.css')
						   ->appendStylesheet('/theme/default/js/jquery_ui/css/jquery.ui.all.css');
					$layout->getView()->headScript()->prependFile('/theme/default/js/jquery-1.7.2.js');
				}
				else
				{
//					$layout->getView()->headLink()->appendStylesheet('/theme/default/css/tsGlobal.css')
//						   ->appendStylesheet('/theme/default/js/jquery-ui/css/blitzer/jquery-ui-1.8.19.custom.css')
//						   ->appendStylesheet('/theme/default/js/jquery_ui/css/jquery.ui.selectmenu.css');
				}
//
//				$layout->getView()->headLink()->appendStylesheet('/theme/default/css/style.css')
//											  ->appendStylesheet('/theme/default/js/validationEngine/validationEngine.css');
//				$layout->getView()->headScript()->prependFile('/theme/default/js/global.js');
//				$layout->getView()->headScript()->prependFile('/theme/default/js/jquery-1.7.2.js')
//												->appendFile('/theme/default/js/jquery_ui/ui/jquery.ui.core.js') // jQuery UI
//												->appendFile('/theme/default/js/jquery_ui/ui/jquery.ui.widget.js')
//												->appendFile('/theme/default/js/jquery_ui/ui/jquery.ui.position.js')
//												->appendFile('/theme/default/js/jquery_ui/ui/jquery.ui.autocomplete.js')
//												->appendFile('/theme/default/js/jquery_ui/ui/jquery.ui.menu.js')
//												->appendFile('/theme/default/js/jquery_ui/ui/jquery.ui.selectmenu.js')
//												->appendFile('/theme/default/js/jquery_ui/ui/jquery.ui.tabs.js')
//												->appendFile('/theme/default/js/validationEngine/validationEngine.js')
//												->appendFile('/theme/default/js/validationEngine/ro.js')
//												->appendFile('/theme/mediaplayer-5.9/jwplayer.min.js');

				// BEGIN: Fancybox
				$layout->getView()->headLink()->appendStylesheet('/theme/default/js//jquery.fancybox/fancybox/jquery.fancybox-1.3.4.css');
				$layout->getView()->headScript()->appendFile('/theme/default/js/jquery.fancybox/fancybox/jquery.mousewheel-3.0.4.pack.js')
												->appendFile('/theme/default/js/jquery.fancybox/fancybox/jquery.fancybox-1.3.4.pack.js');
				// END: Fancybox
				
				$layout->setLayout('two_columns_left');

				$formAuth = new Default_Form_Auth();
				$formAuth->login();
				$formAuth->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/auth/login.phtml'))));
				$layout->getView()->formAuth = $formAuth;

				$formSearch = new Default_Form_Search();
				$formSearch->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/catalog/search.phtml'))));
				$layout->getView()->formSearch = $formSearch;

				$formNewsletter = new Default_Form_Newsletter();
				$formNewsletter->subscribe();
				$formNewsletter->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/subscribe.phtml'))));
				$layout->getView()->formNewsletter = $formNewsletter;

				$auth = Zend_Auth::getInstance();
				$authAccount = $auth->getStorage()->read();
				if(null != $authAccount) {
					if(null != $authAccount->getId()) {
						$account = new Default_Model_AccountUsers();
						$account->find($authAccount->getId());
						$layout->getView()->account = $account;
					}
				}
				
// BEGIN: Check if user logged in
	$isAuthUser = FALSE;
	Zend_Registry::set('isAuthUser', FALSE);
	$auth = Zend_Auth::getInstance();
	$authAccount = $auth->getStorage()->read();
	if(null != $authAccount)
	{
		if(null != $authAccount->getId())
		{
			$isAuthUser = TRUE;
		}
	}
	Zend_Registry::set('isAuthUser', $isAuthUser);
// END: Check if user logged in

// BEGIN: Set logged in user data
	if($isAuthUser)
	{
		$account = new Default_Model_AccountUsers();
		if($account->find($authAccount->getId()))
		{
			Zend_Registry::set('authUser', $account);	
		}
	}
// END: Set logged in user data

// BEGIN: Check if logged in user or display user
	if($controller == 'account')
	{
		$username = $this->getRequest()->getParam('username');
		$userModel = TS_SocialNetwork::usernameToUserModel($username);
		if($userModel)
		{
			Zend_Registry::set('currentUser', $userModel);
			// old
			$layout->getView()->display_user = $userModel;
		}
	}
// END: Check if logged in user or display user
	
				
				// begin: catalog categories
				$model = new Default_Model_CatalogCategories();
				$select = $model->getMapper()->getDbTable()->select()
						->where('status = ?', '1')
						->order(array('position ASC'));
				if(($result = $model->fetchAll($select))) {
					$layout->getView()->catalogCategories = $result;
				}
				// end: catalog categories
				
				// begin: most 5 visited
				$model = new Default_Model_CatalogProducts();
				$select = $model->getMapper()->getDbTable()->select()
						->where('status = ?', 1)
						->order(array('visits DESC'))
						->limit('5');
				if(($result = $model->fetchAll($select))) {
					$layout->getView()->most5visited = $result;
				}
				// end: most 5 visited
				
				// begin: Cms
				$model = new Default_Model_Cms();
				$select = $model->getMapper()->getDbTable()->select()
						->where('status = ?', '1')
						->order(array('position ASC'))
						;
				if(($result = $model->fetchAll($select))) {
					$layout->getView()->pages = $result;
				}
				// end: Cms
				
				switch($controller) {
					case 'error':
						switch($action) {
							case 'error' :
								$layout->setLayout('error');
								break;
							default:
								break;
						}
						break;
					case 'index' :
						switch($action) {
							case 'error' :
								$layout->setLayout('error');
								break;
							default :
								break;
						}
						break;
					case 'iframe' :
						$layout->setLayout('iframe');
						break;
					case 'cms':
						switch($action) {
							case 'error' :
								$layout->setLayout('error');
								break;
							case 'view' :
								$param = $this->getRequest()->getParam('page');
								switch($param) {
									case $param :
										$model = new Default_Model_Cms();
										$select = $model->getMapper()->getDbTable()->select()
												->where('link = ?', $param)
												;
										if(($result = $model->fetchAll($select))) {
											foreach($result as $value) {
												$layout->setLayout($value->getLayout());
											}
										}
										break;
									default:
										break;
								}
								break;
							default:
								break;
						}
						break;
					case 'auth':
						switch($action) {
							case 'index' :
								if(!$acl->isAllowed($accountRole,'default:auth', 'login')) {
									$this->_response->setRedirect('/index');
								}
								break;
							default :
								break;
						}
						break;
					case 'export':
						$layout->setLayout('export');
						break;
					default :
						break;
				}
				break;
		}
	}
}