<?php
class Admin_SettingsController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
    }

	public function indexAction()
	{
		// BEGIN: Save changes
		if($this->getRequest()->isPost()){
			$options = $this->getRequest()->getParam('option');
			if(NULL != $options)
			{
				foreach($options as $key => $value)
				{
					$option = new Default_Model_Options();
					if($option->find($key))
					{
						$option->setValue($value);
						$option->save();
					}
				}
			}
		}
		// END: Save changes

		// BEGIN: Fetch options
		$option = new Default_Model_Options();
		$select = $option->getMapper()->getDbTable()->select()
				->order('name ASC');
		$options = $option->fetchAll($select);
		if($options)
		{
			$this->view->result = $options;
		}
		// END: Fetch options
	}

	public function sitemapgeneratorAction()
	{	
		if(0){
			$msg = $this->getRequest()->getParam('msg');
			$nr = $this->getRequest()->getParam('nr');
			
			function set($loc, $lastmod = null, $changefreq = null, $priority = null){
				$response = "<loc>".$loc."</loc>\n";
				if(null != $lastmod)
					$response .= "<lastmod>".$lastmod."</lastmod>\n";
				if(null != $changefreq)
					$response .= "<changefreq>".$changefreq."</changefreq>\n";
				if(null != $priority)
					$response .= "<priority>".$priority."</priority>\n";
				return $response;
			}
	
			function setImage($loc, $title = null)
			{
				$response = "<image:image>\n<image:loc>".$loc."</image:loc>\n";
				if(null != $title){
					$response .= "<image:title> ".$title." </image:title>\n";
				}
				$response .= "</image:image>\n";
				return $response;
			}
			
			if(null != $msg){
				// BEGIN: DISPLAY MESSAGE
				if($msg == true){
					$this->view->msg = '<div class="mess-true">Sitemap generated. <small>('.$nr.' pages)</small></div>';
				}else{
					$this->view->msg = '<div class="mess-true">Eroare generating sitemap!</div>';
				}
				// END: DISPLAY MESSAGE
			}else{
				// BEGIN: CATALOG CATEGORIES
				$model = new Default_Model_CatalogCategories();
				$select = $model->getMapper()->getDbTable()->select()
						->where("status = ?", "1");
				$result = $model->fetchAll($select);
				if(null != $result){
					$this->view->categories = $result;
				}
				// END: CATALOG CATEGORIES
	
				// BEGIN: CATALOG TAGS
				$model = new Default_Model_Tags();
				$select = $model->getMapper()->getDbTable()->select()
						->order('name DESC');
				$result = $model->fetchAll($select);
				if(null != $result){
					$this->view->tags = $result;
				}
				// END: CATALOG TAGS
	
				// BEGIN: CATALOG PRODUCTS
				$model = new Default_Model_CatalogProducts();
				$select = $model->getMapper()->getDbTable()->select()
						->where('status = ?', '1')
						->order('added DESC');
				$result = $model->fetchAll($select);
				if(null != $result){
					$this->view->catalog = $result;
				}
				// END: CATALOG PRODUCTS
			}
		}
	}
	
	public function cacheAction()
	{	
		$clearCache = $this->getRequest()->getParam('clear');
		
		if(NULL != $clearCache)
		{
			$frontendOptions = array('lifetime' => 86400, 'automatic_serialization' => TRUE, 'caching' => TRUE);
			$backendOptions = array('cache_dir' => './data/cache/');
			$cache = Zend_Cache::factory('Output', 'File', $frontendOptions, $backendOptions);
			if($clearCache == 'menu_left_top')
			{
				$cache->remove('menu_left_top');
				$this->_flashMessenger->addMessage('<div class="mess-true">Menu left top cache cleared!</div>');
			}
			elseif($clearCache == 'menu_left_bottom')
			{
				$cache->remove('menu_left_bottom');
				$this->_flashMessenger->addMessage('<div class="mess-true">Menu left bottom cache cleared!</div>');
			}
			elseif($clearCache == 'index')
			{
				$cache->remove('index');
				$this->_flashMessenger->addMessage('<div class="mess-true">Index cache cleared!</div>');
			}
			elseif($clearCache == 'footer')
			{
				$cache->remove('footer');
				$this->_flashMessenger->addMessage('<div class="mess-true">Footer cache cleared!</div>');
			}
			elseif($clearCache == 'tagcloud')
			{
				$cache->remove('tagcloud');
				$this->_flashMessenger->addMessage('<div class="mess-true">Tagcloud cache cleared!</div>');
			}
			else
			{
				$this->_flashMessenger->addMessage('<div class="mess-true">Invalid cache resource!</div>');
			}
			$this->_redirect('/admin/settings/cache');
		}
	}

	public function thumbsAction()
	{
		$id = $this->getRequest()->getParam('id');
		$post = new Default_Model_CatalogProducts();
		if($post->find($id)){
			$nameC = TS_Products::formatName($post->getName());
			$allowed = "/[^a-z0-9\\-\\_]+/i";
			$folderName = preg_replace($allowed,"-", strtolower(trim($nameC)));
			$folderName = trim($folderName,'-');

			$image = new Default_Model_CatalogProductImages();
			$select = $image->getMapper()->getDbTable()->select()
					->where('product_id = ?', $id);
			$result = $image->fetchAll($select);
			if(NULL != $result)
			{
				foreach($result as $value)
				{
					require_once APPLICATION_PUBLIC_PATH.'/library/Needs/tsThumb/ThumbLib.inc.php';
					@unlink(APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$post->getUser_id().'/'.$folderName.'/small/'.$value->getName());
					$thumb = PhpThumbFactory::create(APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$post->getUser_id().'/'.$folderName.'/big/'.$value->getName());
					$thumb->tsResizeWithFill(100, 100, "ffffff")->save(APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$post->getUser_id().'/'.$folderName.'/small/'.$value->getName());
				}
			}
		}
	}

	public function playerAction()
	{
		
	}
	
	public function cacheTimesAction()
	{
		$cacheAction = $this->getRequest()->getParam('act');
		$cacheComponent = $this->getRequest()->getParam('com');
		
		if($cacheAction == "clear")
		{
			$cache = TS_Cache::getInstance();

			if($cache->clear($cacheComponent))
			{
				$this->_flashMessenger->addMessage('<div class="mess-true">Cache cleared!</div>');
			}
			else
			{
				$this->_flashMessenger->addMessage('<div class="mess-false">Error clearing cache!</div>');
			}
			$this->_redirect('/admin/settings/cache');
		}
		elseif($cacheAction == "modify")
		{
			$this->view->cacheComponent = $cacheComponent;
		}
		
		if($this->getRequest()->isPost()){
			$options = $this->getRequest()->getParam('option');
			if(NULL != $options)
			{
				foreach($options as $key => $value)
				{
					$option = new Default_Model_Options();
					if($option->find($key))
					{
						$option->setValue($value);
						$option->save();
					}
				}
			}
		}
	}
}