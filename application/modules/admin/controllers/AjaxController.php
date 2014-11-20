<?php
class Admin_AjaxController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		$bootstrap = $this->getInvokeArg('bootstrap');
		if($bootstrap->hasResource('db')) {
			$this->db = $bootstrap->getResource('db');
		}
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->message = $this->_flashMessenger->getMessages();
	}

	public function catalogProductsSortableAction()
	{
		$items = $this->getRequest()->getParam('listItems');
		$i=1; foreach($items as $value) {
			$model = new Default_Model_CatalogProducts();
			if($model->find($value)) {
				$model->setPosition($i);
				$model->save();
			}
		$i++; }
	}

	public function catalogProductsImagesSortableAction()
	{
		$items = $this->getRequest()->getParam('listItems');
		$i=1; foreach($items as $value) {
			$model = new Default_Model_CatalogProductImages();
			if($model->find($value)) {
				$model->setPosition($i);
				$model->save();
			}
		$i++; }
	}

	public function catalogCategoriesSortableAction()
	{
		$items = $this->getRequest()->getParam('listItems');
		$i=1; foreach($items as $value) {
			$model = new Default_Model_CatalogCategories();
			if($model->find($value)) {
				$model->setPosition($i);
				$model->save();
			}
		$i++; }
	}

	public function firstImageAction()
	{
		$response = "";
		$id = (int) $this->getRequest()->getParam('id');
		$type = $this->getRequest()->getParam('type');

		if($type == 'video')
		{
			$model = new Default_Model_Video();
			$select = $model->getMapper()->getDbTable()->select()
					->from(array('video'), array('productId', 'image'))
					->where('productId = ?', $id)
					->limit(1);
			$result = $model->fetchAll($select);
			if(NULL != $result)
			{
				if(NULL != $result[0]->getImage())
				$response = '/media/catalog/video/small/'.$result[0]->getImage();
			}
		}elseif($type == 'gallery')
		{
			$model = new Default_Model_CatalogProductImages();
			$select = $model->getMapper()->getDbTable()->select()
					->where('product_id = ?', $id)
					->order('position ASC')
					->limit(1);
			$result = $model->fetchAll($select);
			if(null != $result){
				if(isset($result[0])){
					$userId = $result[0]->getProduct()->getUser_id();
					$imageName = $result[0]->getName();

					$allowed = "/[^a-z0-9\\-\\_]+/i";
					$productName = preg_replace($allowed,"-", strtolower($result[0]->getProduct()->getName()));
					if($userId){
						$response = '/media/catalog/products/'.$userId.'/'.$productName.'/small/'.$imageName;
					}else{
						$response = '/media/catalog/products/0/'.$productName.'/small/'.$imageName;
					}
				}
			}
		}
		echo Zend_Json_Encoder::encode($response);
	}
	
	public function userAutocompleteAction()
	{
		$response = array();
		$params = $this->getRequest()->getParam('term');
		$model = new Default_Model_AccountUsers();
		$select = $model->getMapper()->getDbTable()->select();
		if($params){
//			$select->where("username LIKE '%".$params."%' OR username LIKE '".$params."%' OR username LIKE '%".$params."'");
			$select->where("username LIKE '%".$params."%'");
		}
		$select->order('created DESC');
		$select->limit(8);
		$result = $model->fetchAll($select);
		if(null != $result) {
			foreach ($result as $value) {
				$response[]['name'] = $value->getUsername();
			}
		}
		echo Zend_Json_Encoder::encode($response);
	}
	
	public function sitemapGeneratorAction()
	{
		$response = "";
		$type = $this->getRequest()->getParam('type');
		
		if($type == 'writeHeader')
		{
			$handle = TS_SeoSerp::openFile('w+');
			$header = TS_SeoSerp::setHeader();
			TS_SeoSerp::writeToFile($handle, $header);
			TS_SeoSerp::closeFile($handle);
			$response .= $type;
		}
		elseif($type == 'writeContentStatic')
		{
			$handle = TS_SeoSerp::openFile('a');
			$content = TS_SeoSerp::staticPages();
			TS_SeoSerp::writeToFile($handle, $content);
			TS_SeoSerp::closeFile($handle);
			$response .= $type;
		}
		elseif($type == 'writeContentCategories')
		{
			$response .= 'ok';
		}
		elseif($type == 'writeContentTags')
		{
			$response .= 'ok';
		}
		elseif($type == 'writeContentPosts')
		{
			$response .= TS_SeoSerp::itemsCount('posts');
		}
		elseif($type == 'writeFooter')
		{
			$handle = TS_SeoSerp::openFile('a');
			$footer = TS_SeoSerp::setFooter();
			TS_SeoSerp::writeToFile($handle, $footer);
			TS_SeoSerp::closeFile($handle);
			$response .= $type;
		}
		else
		{
			exit();
		}
		echo Zend_Json_Encoder::encode($response);
	}
	
	public function sitemapGeneratorItemAction()
	{
		$response = "";
		$type = $this->getRequest()->getParam('type');
		$iterator = $this->getRequest()->getParam('iterator');
		
		if($type == 'writeContentCategories')
		{
			$currentItem = TS_SeoSerp::itemsIterator($type, $iterator);
			if($currentItem)
			{
				$handle = TS_SeoSerp::openFile('a');
				$zendView = new Zend_View;
				$loc = TS_SeoSerp::BASE_HTTP.$zendView->url(array(
					'categoryName' => preg_replace('/\s+/','-', strtolower($currentItem->getName())),
					'id' => $currentItem->getId()),
					'category')
				;
				$lastmod = date("Y-m-d", $currentItem->getAdded());
				$footer = TS_SeoSerp::set($loc, $lastmod, 'daily', '0.9');
				TS_SeoSerp::writeToFile($handle, $footer);
				TS_SeoSerp::closeFile($handle);
			}
		}
		elseif($type == 'writeContentTags')
		{
			$currentItem = TS_SeoSerp::itemsIterator($type, $iterator);
			if($currentItem)
			{
				$handle = TS_SeoSerp::openFile('a');
				$zendView = new Zend_View;
				$loc = TS_SeoSerp::BASE_HTTP.$zendView->url(array(
					'tag' => $currentItem->getName()
					), 'tag')
				;
				$lastmod = date("Y-m-d", $currentItem->getAdded());
				$footer = TS_SeoSerp::set($loc, $lastmod, 'daily', '0.9');
				TS_SeoSerp::writeToFile($handle, $footer);
				TS_SeoSerp::closeFile($handle);
			}
		}
		elseif($type == 'writeContentPosts')
		{
			$tsImage = new TS_Image();
			$currentItem = TS_SeoSerp::itemsIterator($type, $iterator);
			if($currentItem)
			{
				$handle = TS_SeoSerp::openFile('a');
				$zendView = new Zend_View;
				$categoryName = TS_SeoSerp::getCategoryNameByGalleryId($currentItem->getId());
				$loc = TS_SeoSerp::BASE_HTTP.$zendView->url(array(
					'categoryName' => preg_replace('/[^a-zA-Z0-9]+/','-', strtolower($categoryName)),
					'productName' => preg_replace('/[^a-zA-Z0-9]+/','-', strtolower($currentItem->getName())),
					'id' => $currentItem->getId()
					),'product')
				;
				$lastmod = date("Y-m-d", $currentItem->getAdded());
				// BEGIN: Save images
				$images = TS_SeoSerp::fetchGalleryImages($currentItem->getId());
				if($images)
				{
					$footer = TS_SeoSerp::set($loc, $lastmod, 'daily', '0.9', false);
					foreach($images as $image)
					{
						$authorId = TS_SeoSerp::getAuthorIdByGalleryId($image->getProduct_id());
						$imageLocation = TS_SeoSerp::bigImageHrefLocation($image->getName(), $currentItem->getName(), $authorId);
						$footer .= TS_SeoSerp::setImage(TS_SeoSerp::BASE_HTTP.$imageLocation, $currentItem->getName());
					}
					$footer .= "</url>\n";
				}
				else
				{
					$footer = TS_SeoSerp::set($loc, $lastmod, 'daily', '0.9');
				}
				TS_SeoSerp::writeToFile($handle, $footer);
				// END: Save images
				TS_SeoSerp::closeFile($handle);
			}
		}
		
		$response .= $type;
		echo Zend_Json_Encoder::encode($response);
	}
}