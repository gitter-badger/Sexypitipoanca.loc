<?php
class Admin_CatalogController extends Base_Controller_Action
{
	public function indexAction()
	{
		$page = $this->_getParam('page')?$this->_getParam('page'):1;
		$this->view->pagina = $page;
		
		$model = new Default_Model_CatalogProducts();
		$select = $model->getMapper()->getDbTable()->select();
			if(isset($_SESSION['catalogFilter'])){
				$select->where('category_id = ?', $_SESSION['catalogFilter']);
			}
			$select->order('added DESC');

		$this->paginateSelect($select);

		// BEGIN: FILTERS
		$model = new Default_Model_CatalogCategories();
		$select = $model->getMapper()->getDbTable()->select()
				->order('position ASC');
		$result = $model->fetchAll($select);
		if(null != $result) {
			$this->view->categories = $result;
		}
		// END: FILTERS
	}
	
	public function activateAction()
	{
		$id = $this->getRequest()->getParam('id');
		$model = new Default_Model_CatalogProducts();
		if($model->find($id))
			$model->activate();
		$pagina = $this->getRequest()->getParam('pagina')?$this->getRequest()->getParam('pagina'):1;
		$this ->_redirect("/admin/catalog/index/page/".$pagina);
	}
	
	public function productsAddVideoAction()
	{
		$form = new Admin_Form_Post();
		$form->addVideo();
		$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/addVideo.phtml'))));
		$this->view->form = $form;
		
		if($this->getRequest()->isPost()){
			if($form->isValid($this->getRequest()->getPost())){
				$name = TS_Products::formatName($form->getValue('name'));
				
				$model = new Default_Model_CatalogProducts();
				$model->setOptions($form->getValues());
				if(NULL != $form->getValue('url'))
				{
					$model->setType('embed');
				}
				else
				{
					$model->setType('video');
				}
				$model->setName($name);
				$productId = $model->save();
				if($productId)
				{
					$model = new Default_Model_Video();
					$model->setProductId($productId);
					$model->setUrl($form->getValue('url'));
					$model->setEmbed($form->getValue('embed'));

					if($form->image->receive()) {
						if($form->image->getFileName()){
							$tmp = pathinfo($form->image->getFileName());
							$extension = (!empty($tmp['extension']))?$tmp['extension']:null;
							$filename = md5(uniqid(mt_rand(), true)).'.'.$extension;
							if(@copy($form->image->getFileName(), APPLICATION_PUBLIC_PATH.'/media/catalog/video/'.$filename)) {
								require_once APPLICATION_PUBLIC_PATH.'/library/Needs/tsThumb/ThumbLib.inc.php';
								$thumb = PhpThumbFactory::create(APPLICATION_PUBLIC_PATH.'/media/catalog/video/'.$filename);
								$thumb->resize(600, 600)
									  ->tsWatermark(APPLICATION_PUBLIC_PATH."/media/watermark-small.png")
									  ->save(APPLICATION_PUBLIC_PATH.'/media/catalog/video/big/'.$filename);
								$thumb->tsResizeWithFill(150, 150, 'ffffff')->save(APPLICATION_PUBLIC_PATH.'/media/catalog/video/small/'.$filename);
                                $this->safeDelete(APPLICATION_PUBLIC_PATH.'/media/catalog/video/'.$filename);
								$model->setImage($filename);
							}
						}
					}

					if($model->save())
					{
						$this->_redirect('/admin/catalog');
					}
				}
			}
		}
	}

	public function productsEditVideoAction()
	{
        // ToDo: remove this
		$oldAvatar = "nush, nu e definit";

		$id = $this->getRequest()->getParam('id');
		$model = new Default_Model_CatalogProducts();
		if($model->find($id))
		{
			$form = new Admin_Form_Post();
			$form->addVideo();
			$form->editVideo($model);
			$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/addVideo.phtml'))));
			$this->view->form = $form;
			
			if($this->getRequest()->isPost()){
				if($form->isValid($this->getRequest()->getPost())){
					$name = TS_Products::formatName($form->getValue('name'));

					$model->setOptions($form->getValues());
					if(NULL != $form->getValue('url'))
					{
						$model->setType('embed');
					}
					else
					{
						$model->setType('video');
					}
					$model->setName($name);
					$productId = $model->save();
					if($productId)
					{
						$video = new Default_Model_Video();
						$select = $video->getMapper()->getDbTable()->select()
								->where('productId = ?', $productId);
						$result = $video->fetchAll($select);
						if(NULL != $result)
						{
							$result[0]->setProductId($productId);
							$result[0]->setEmbed($form->getValue('embed'));
							$result[0]->setUrl($form->getValue('url'));

							// BEGIN: Replace old image
							$oldImage = $result[0]->getImage();
							if($form->image->receive()){
								if($form->image->getFileName()){
									$tmp = pathinfo($form->image->getFileName());
									$extension = (!empty($tmp['extension']))?$tmp['extension']:null;
									$filename = md5(uniqid(mt_rand(), true)).'.'.$extension;
									if(@copy($form->image->getFileName(), APPLICATION_PUBLIC_PATH.'/media/catalog/video/'.$filename)) {
										require_once APPLICATION_PUBLIC_PATH.'/library/Needs/tsThumb/ThumbLib.inc.php';
										$thumb = PhpThumbFactory::create(APPLICATION_PUBLIC_PATH.'/media/catalog/video/'.$filename);
										$thumb->resize(600, 600)
											  ->tsWatermark(APPLICATION_PUBLIC_PATH."/media/watermark-small.png")
											  ->save(APPLICATION_PUBLIC_PATH.'/media/catalog/video/big/'.$filename);
										$thumb->tsResizeWithFill(150, 150, 'ffffff')->save(APPLICATION_PUBLIC_PATH.'/media/catalog/video/small/'.$filename);

										$result[0]->setImage($filename);
                                        $this->safeDelete(APPLICATION_PUBLIC_PATH.'/media/catalog/video/'.$filename);
										if(null != $oldAvatar){
                                            $this->safeDelete([
                                                APPLICATION_PUBLIC_PATH.'/media/catalog/video/big/'.$oldImage,
                                                APPLICATION_PUBLIC_PATH.'/media/catalog/video/small/'.$oldImage
                                            ]);
										}
									}
								}
							}
							// END: Replace old image
							
							if($result[0]->save())
							{
								$this->_flashMessenger->addMessage('<div class="mess-true">Success! Data modified.</div>');
								$this->_redirect('/admin/catalog');
							}
						}
					}
				}
			}
		}
		else
		{
			$this->_flashMessenger->addMessage('<div class="mess-false">Error! Data could not be modified.</div>');
			$this->_redirect('/admin/catalog');
		}
	}

	public function productsAddAction()
	{
		$form = new Admin_Form_Catalog();
		$form->productAdd();
		$this->view->form = $form;

		if($this->getRequest()->isPost()) {
			if($form->isValid($this->getRequest()->getPost())) {				
				if(!file_exists(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $form->getValue('user') . '/')) {
					mkdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $form->getValue('user'), 0777, true);
				}
				
				$allowed = "/[^a-z0-9\\-\\_]+/i";  
				$folderName = preg_replace($allowed,"-", strtolower(trim($form->getValue('name'))));	
				$folderName = trim($folderName,'-');
				if(!file_exists(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $form->getValue('user') . '/' . $folderName . '/')) {
					mkdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $form->getValue('user') . '/' . $folderName . '/', 0777, true);
					mkdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $form->getValue('user') . '/' . $folderName . '/big', 0777, true);
					mkdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $form->getValue('user') . '/' . $folderName . '/small', 0777, true);
				}

				$model = new Default_Model_CatalogProducts();
				$model->setUser_id($form->getValue('user'));
				$model->setCategory_id($form->getValue('category'));
				$nameC = TS_Products::formatName($form->getValue('name'));
				$model->setName($nameC);
				$model->setType('gallery');
				$model->setDescription($form->getValue('description'));
				
				$model->setStatus($form->getValue('status'));
				$productId = $model->save();		
				if($productId){

					if($form->getValue('tags')) {
						$tags = explode(',', trim($form->getValue('tags')));
						foreach($tags as $tag) {
							$tag = trim($tag);
							$model2 = new Default_Model_Tags();
							$select = $model2->getMapper()->getDbTable()->select()
									->where('name = ?', $tag);
							$result = $model2->fetchAll($select);
							if($result) {
								$model3 = new Default_Model_CatalogProductTags();
								$model3->setProduct_id($productId);
								$model3->setTag_id($result[0]->getId());
								$model3->save();
							} else {
								$model3 = new Default_Model_Tags();
								$model3->setName($tag);
								$tagId = $model3->save();
								if($tagId) {
									$model4 = new Default_Model_CatalogProductTags();
									$model4->setProduct_id($productId);
									$model4->setTag_id($tagId);
									$model4->save();
								}
							}
						}
					}
					
					$upload = new Zend_File_Transfer_Adapter_Http();
					$upload->addValidator('Size', false, 2000000, 'image');
					$upload->setDestination('media/catalog/products/'.$form->getValue('user').'/'.$folderName.'/');
					$files = $upload->getFileInfo();
					$i = 1;
					$rand = '';
					foreach($files as $file => $info) {
						if($i<=20){
							if($upload->isValid($file)) {							
								if($upload->receive($file)){
									$rand = rand(99, 9999);
									$tmp = pathinfo($info['name']);
									$extension = (!empty($tmp['extension']))?$tmp['extension']:null;
									$pozaNume = $folderName.'-'.$rand.'.'.$extension;
									$model2 = new Default_Model_CatalogProductImages();
									$model2->setProduct_id($productId);
									$model2->setPosition('999');
									$model2->setName($pozaNume);
									if($model2->save()) {
										require_once APPLICATION_PUBLIC_PATH.'/library/Needs/tsThumb/ThumbLib.inc.php';
										$thumb = PhpThumbFactory::create(APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$form->getValue('user').'/'.$folderName.'/'.$info['name']);
										$thumb->resize(600, 600)
											  ->tsWatermark(APPLICATION_PUBLIC_PATH."/media/watermark-small.png")
											  ->save(APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$form->getValue('user').'/'.$folderName.'/big/'.$pozaNume);
										$thumb->resize(150)->save(APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$form->getValue('user').'/'.$folderName.'/small/'.$pozaNume);
										$this->safeDelete('media/catalog/products/'.$form->getValue('user').'/'.$folderName.'/'.$info['name']);
									}
								}else{
									$this->_flashMessenger->addMessage('<div class="mess-info">Eroare upload.</div>');
									$this->_redirect('/admin/catalog');	
								}
							}else{								
								$this->_flashMessenger->addMessage('<div class="mess-info">'.print_r($upload->getMessages()).'</div>');
								$this->_redirect('/admin/catalog');									
							}	
						}else{
							$this->_flashMessenger->addMessage('<div class="mess-info">Au fost adaugate numai primele 20 de poze.</div>');
							$this->_redirect('/admin/catalog');
						}						
						$i++;
					}
				}
				$this->_redirect('/admin/catalog');
			}
		}
	}

	public function productsEditAction()
	{
		$product = new Default_Model_CatalogProducts();
		if(!$product->find($this->getRequest()->getParam('id'))) {
			$this->_flashMessenger->addMessage('<div class="mess-error">Product does not exist!</div>');
			$this->_redirect('/admin/catalog/');
		}

		$this->view->productId = $product->getId();
		$form = new Admin_Form_Catalog();
		$form->productAdd();
		$form->productEdit($product);
		$this->view->form = $form;

		$model2 = new Default_Model_CatalogProductTags();
		$select = $model2->getMapper()->getDbTable()->select()
				->where('product_id = ?', $product->getId());
		$result = $model2->fetchAll($select);
		if ($result) {
			$this->view->tags = $result;
		}

		$eImages = array();
		$imagesForEdit = array();
		$model2 = new Default_Model_CatalogProductImages();
		$select = $model2->getMapper()->getDbTable()->select()
				->where('product_id = ?', $product)
				->order('position ASC');
		$result = $model2->fetchAll($select);
		if ($result) {
			$imagesForEdit = $result;
			$this->view->imagini = $result;
			foreach($result as $value) {
				$eImages[$value->getId()] = $value->getName();
			}
			$this->view->images = $eImages;
		}

		if ($this->getRequest()->isPost()) {
			if ($form->isValid($this->getRequest()->getPost())) {
				$oldName = '';
				if ($product->getName() != $form->getValue('name')) {
					$oldName = $product->getName();
					$nameC = TS_Products::formatName($form->getValue('name'));
                    $product->setName($nameC);
				}
                $product->setCategory_id($form->getValue('category'));
                $product->setStatus($form->getValue('status'));
                $product->setDescription($form->getValue('description'));
				if ($product->save()) {
					$this->_flashMessenger->addMessage('<div class="mess-true">Modificarile au fost efectuate cu succes!</div>');
					if ($form->getValue('tags')) {
						$tags = explode(',', trim($form->getValue('tags')));
						foreach($tags as $tag) {
							$tag = trim($tag);
							$model2 = new Default_Model_Tags();
							$select = $model2->getMapper()->getDbTable()->select()
									->where('name = ?', $tag);
							$result = $model2->fetchAll($select);
							if ($result) {
								$model3 = new Default_Model_CatalogProductTags();
								$select = $model3->getMapper()->getDbTable()->select()
										->where('product_id = ?', $product->getId())
										->where('tag_id = ?', $result[0]->getId())
										;
								$result2 = $model3->fetchAll($select);
								if (!$result2) {
									$model4 = new Default_Model_CatalogProductTags();
									$model4->setProduct_id($product->getId());
									$model4->setTag_id($result[0]->getId());
									$model4->save();
								}
							} else {
								$model3 = new Default_Model_Tags();
								$model3->setName($tag);
								$tagId = $model3->save();
								if ($tagId)
								{
									$model4 = new Default_Model_CatalogProductTags();
									$model4->setProduct_id($product->getId());
									$model4->setTag_id($tagId);
									$model4->save();
								}
							}
						}
					}

					$userId = $form->getValue('user');
					if (!$form->getValue('user')) {
						$userId = $product->getUser_id();
					}

					if (!empty($oldName)) {
						//daca a fost modificat user-ul si user-ul nou nu are inca folder creat
						if(!file_exists(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $userId . '/')) {
							mkdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $userId , 0777, true);
						}

						//cream folderele cu numele galerie noua
						$allowed = "/[^a-z0-9\\-\\_]+/i";
						$folderName = preg_replace($allowed,"-", strtolower(trim($form->getValue('name'))));
						$folderName = trim($folderName,'-');
						if (!file_exists(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $userId . '/' . $folderName . '/')) {
							mkdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $userId . '/' . $folderName . '/', 0777, true);
							mkdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $userId . '/' . $folderName . '/big', 0777, true);
							mkdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $userId . '/' . $folderName . '/small', 0777, true);
						}

						//copiem pozele din folderul vechi
						$folderName2 = preg_replace($allowed,"-", strtolower(trim($oldName)));
						$folderName2 = trim($folderName2,'-');

						foreach ($imagesForEdit as $valueImg) {
							$model2 = new Default_Model_CatalogProductImages();
							$model2->find($valueImg->getId());
							$oldPozaNume = $model2->getName();
							$oldPath = 'media/catalog/products/'.($product->getUser_id()?$product->getUser_id():'0').'/'.$folderName2.'/big/'.$oldPozaNume;
							$tmp = pathinfo($oldPath);
							$extension = (!empty($tmp['extension']))?$tmp['extension']:null;
							$pozaNume = $folderName.'-'.rand(99, 9999).'.'.$extension;
							$model2->setName($pozaNume);
							if ($model2->save()) {
								copy(APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$userId.'/'.$folderName2.'/big/'.$oldPozaNume, APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$form->getValue('user').'/'.$folderName.'/big/'.$pozaNume);
								copy((APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$userId.'/'.$folderName2.'/small/'.$oldPozaNume), APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$form->getValue('user').'/'.$folderName.'/small/'.$pozaNume);

								$this->safeDelete([
									APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$userId.'/'.$folderName2.'/big/'.$oldPozaNume,
									APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$userId.'/'.$folderName2.'/small/'.$oldPozaNume
								]);
							}
						}

						$this->safeRemoveDir([
							APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$userId.'/'.$folderName2.'/big',
							APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$userId.'/'.$folderName2.'/small',
							APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$userId.'/'.$folderName2
						]);
					} else {
						$allowed = "/[^a-z0-9\\-\\_]+/i";
						$folderName = preg_replace($allowed,"-", strtolower(trim($product->getName())));
						$folderName = trim($folderName,'-');
					}

					$upload = new Zend_File_Transfer_Adapter_Http();
					$upload->addValidator('Size', false, 2000000, 'image');
					$upload->setDestination('media/catalog/products/'.$userId.'/'.$folderName.'/');
					$files = $upload->getFileInfo();
					$i = 1;
					foreach ($files as $file => $info) {
                        if ($upload->isValid($file)) {
                            if($upload->receive($file)){
                                $tmp = pathinfo($info['name']);
                                $extension = (!empty($tmp['extension']))?$tmp['extension']:null;
                                $pozaNume = $folderName.'-'.rand(99, 9999).'.'.$extension;
                                $model2 = new Default_Model_CatalogProductImages();
                                $model2->setProduct_id($product->getId());
                                $model2->setPosition('999');
                                $model2->setName($pozaNume);
                                if ($model2->save()) {
                                    require_once APPLICATION_PUBLIC_PATH.'/library/Needs/tsThumb/ThumbLib.inc.php';
                                    $thumb = PhpThumbFactory::create(APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$userId.'/'.$folderName.'/'.$info['name']);
                                    $thumb->resize(600, 600)
                                          ->tsWatermark(APPLICATION_PUBLIC_PATH."/media/watermark-small.png")
                                          ->save(APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$userId.'/'.$folderName.'/big/'.$pozaNume);
                                    $thumb->tsResizeWithFill(150, 150, "ffffff")->save(APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$userId.'/'.$folderName.'/small/'.$pozaNume);
                                    $this->safeDelete('media/catalog/products/'.$userId.'/'.$folderName.'/'.$info['name']);
                                }
                            } else {
                                $this->_flashMessenger->addMessage('<div class="mess-info">Eroare upload!</div>');
                                $this->_redirect('/admin/catalog/products-edit/id/'.$product->getId());
                            }
                        }
						$i++;
					}
				}
				$this->_redirect('/admin/catalog/products-edit/id/'.$product->getId());
			}
		}
	}

	public function productsDetailsAction()
	{
		$id = $this->getRequest()->getParam('id');
		$this->view->id = $id;
		$model = new Default_Model_CatalogProducts();
		if($model->find($id)){
			$this->view->result = $model;
			if($model->getType() == 'gallery')
			{
				$image = new Default_Model_CatalogProductImages();
				$select = $image->getMapper()->getDbTable()->select()
						->where('product_id = ?', $id)
						->order('position ASC');
				$images = $image->fetchAll($select);
				$this->view->images = $images;
			}
		}
	}

	public function productsStatusAction()
	{
		$id = $this->getRequest()->getParam('id');
		$model = new Default_Model_CatalogProducts();
		if($model->find($id)) {
			$model->setStatus($model->getStatus()?'0':'1');
			$model->save();
		}
		$pagina = $this->getRequest()->getParam('pagina')?$this->getRequest()->getParam('pagina'):1;
		$this ->_redirect("/admin/catalog/index/page/".$pagina);
	}

	public function productsDeleteAction()
	{
		$articleId = $this->getRequest()->getParam('id');
		if(TS_Catalog::deleteArticle($articleId))
			$this->_flashMessenger->addMessage('<div class="mess-true">Articolul a fost sters!</div>');
		else
			$this->_flashMessenger->addMessage('<div class="mess-false">Eroare! Articolul nu a fost sters.</div>');
		$pagina = $this->getRequest()->getParam('pagina')?$this->getRequest()->getParam('pagina'):1;
		$this ->_redirect("/admin/catalog/index/page/".$pagina);
	}

	public function productsDeleteImagesAction()
	{
		$id = $this->getRequest()->getParam('id');
		$product_id = $this->getRequest()->getParam('productid');
		$model = new Default_Model_CatalogProductImages();
		if($model->find($id)){
			$image = $model->getName();
			$model2 = new Default_Model_CatalogProducts();
			if($model2->find($model->getProduct_id())) {
				if($model->delete()) {
					$allowed = "/[^a-z0-9\\-\\_]+/i";  
					$folderName = preg_replace($allowed,"-", strtolower(trim($model2->getName())));	
					$folderName = trim($folderName,'-');
                    $this->safeDelete([
					    'media/catalog/products/'.($model2->getUser_id()?$model2->getUser_id():'0').'/'.$folderName.'/big/'.$image,
					    'media/catalog/products/'.($model2->getUser_id()?$model2->getUser_id():'0').'/'.$folderName.'/small/'.$image
                    ]);
					$this->_flashMessenger->addMessage('<div class="mess-true">Poza a fost stearsa cu succes!</div>');
				} else {
					$this->_flashMessenger->addMessage('<div class="mess-false">Eroare stergere poza!</div>');
				}
			}
		}
		$this->_redirect('/admin/catalog/products-edit/id/'.$product_id."#tabs-3");
	}

	public function productsDeleteTagsAction()
	{
		$id = $this->getRequest()->getParam('id');
		$product_id = $this->getRequest()->getParam('productid');
		$model = new Default_Model_CatalogProductTags();
		if($model->find($id)) {			
			if($model->delete()) {
				$this->_flashMessenger->addMessage('<div class="mess-true">Tagul a fost sters cu succes</div>');
			} else {
				$this->_flashMessenger->addMessage('<div class="mess-false">Eraore stergere tag!</div>');
			}
		}
		$this->_redirect('/admin/catalog/products-edit/id/'.$product_id."#tabs-2");
	}

	public function categoriesAction()
	{
		$model = new Default_Model_CatalogCategories();
		$select = $model->getMapper()->getDbTable()->select()
				->order('position ASC');
		$result = $model->fetchAll($select);
		if($result)
		{
			$this->view->result = $result;
		}
	}

	public function categoriesAddAction()
	{
		$form = new Admin_Form_Catalog();
		$form->categoriesAdd();
		$this->view->form = $form;

		if($this->getRequest()->isPost()) {
			if($form->isValid($this->getRequest()->getPost())) {
				$model = new Default_Model_CatalogCategories();
				$model->setOptions($form->getValues());
				if($model->save()) {
					$this->_flashMessenger->addMessage('<div class="mess-true">success</div>');
				} else {
					$this->_flashMessenger->addMessage('<div class="mess-false">error</div>');
				}
			}
			$this->_redirect('/admin/catalog/categories');
		}
	}

	public function categoriesEditAction()
	{
		$id = $this->getRequest()->getParam('id');
		$page = $this->getRequest()->getParam('page');
		$model = new Default_Model_CatalogCategories();
		if($model->find($id)) {
			$form = new Admin_Form_Catalog();
			$form->categoriesAdd();
			$form->categoriesEdit($model);
			$this->view->form = $form;

			if($this->getRequest()->isPost()) {
				if($form->isValid($this->getRequest()->getPost())) {
					$model->setOptions($form->getValues());
					if($model->save()) {
						$this->_flashMessenger->addMessage('<div class="mess-true">success</div>');
					} else {
						$this->_flashMessenger->addMessage('<div class="mess-false">error</div>');
					}
				}
				$this->_redirect('/admin/catalog/categories');
			}
		}
	}

	public function categoriesStatusAction()
	{
		$id = $this->getRequest()->getParam('id');
		$page = $this->getRequest()->getParam('page');
		$model = new Default_Model_CatalogCategories();
		if($model->find($id)) {
			$model->setStatus($model->getStatus()?'0':'1');
			$model->save();
		}
		$this->_redirect('/admin/catalog/categories');
	}

	public function categoriesDeleteAction()
	{
		$id = $this->getRequest()->getParam('id');
		$page = $this->getRequest()->getParam('page');
		$model = new Default_Model_CatalogCategories();
		if($model->find($id)) {
			if($model->delete()) {
				$this->_flashMessenger->addMessage('<div class="mess-true">success</div>');
			} else {
				$this->_flashMessenger->addMessage('<div class="mess-false">error</div>');
			}
		}
		$this->_redirect('/admin/catalog/categories');
	}

	public function productsVisitsAddAction()
	{
		$form = new Admin_Form_Catalog();
		$form->visitsAdd();
		$this->view->form = $form;

		if($this->getRequest()->isPost()) {
			if($form->isValid($this->getRequest()->getPost())) {

				
				
				
				if($form->getValue('all') == 1) {
					$model = new Default_Model_CatalogProductVisits();
					$select = $model->getMapper()->getDbTable()->select();
					$result = $model->fetchAll($select);
					if($result)
					{
						foreach($result as $value)
						{
							if($form->getValue('fixed'))
							{
								$visits = $value->getVisits() + $form->getValue('fixed');
							}
							elseif($form->getValue('min') && $form->getValue('max'))
							{
								$visits = $value->getVisits() + rand($form->getValue('min'), $form->getValue('max'));
							}
							else
							{
								$this->_flashMessenger->addMessage('<div class="mess-true">suj</div>');
								$this->_flashMessenger->addMessage('<div class="mess-false">insereaza valoare min/max sau fixed</div>');
								$this->_redirect('/admin/catalog/products-visits-add');
							}
							$value->setVisits($visits);
							$value->save();
						}
					}
				} elseif($form->getValue('products')) {
					foreach($form->getValue('products') as $product) {
						$model = new Default_Model_CatalogProductVisits();
						$select = $model->getMapper()->getDbTable()->select()
								->where('product_id = ?', $product);
						$result = $model->fetchAll($select);
						if($result)
						{
							foreach($result as $value)
							{
								if($form->getValue('fixed'))
								{
									$visits = $value->getVisits() + $form->getValue('fixed');
								}
								elseif($form->getValue('min') && $form->getValue('max'))
								{
									$visits = $value->getVisits() + rand($form->getValue('min'), $form->getValue('max'));
								}
								else
								{
									$this->_flashMessenger->addMessage('<div class="mess-true">suj</div>');
									$this->_flashMessenger->addMessage('<div class="mess-false">insereaza valoare min/max sau fixed</div>');
									$this->_redirect('/admin/catalog/products-visits-add');
								}
								$value->setVisits($visits);
								$value->save();
							}
						}
					}
				} else {
					$this->_flashMessenger->addMessage('<div class="mess-true">suj</div>');
					$this->_flashMessenger->addMessage('<div class="mess-false">selecteaza produse sau da-i check all</div>');
					$this->_redirect('/admin/catalog/products-visits-add');
				}
				$this->_flashMessenger->addMessage('<div class="mess-true">success</div>');
				$this->_redirect('/admin/catalog');
			}
		}
	}

	public function filterAction(){
		$id = $this->getRequest()->getParam('id');

		$model = new Default_Model_CatalogCategories();
		if($model->find($id)){
			if($_SESSION['catalogFilter'] == $id){
				unset($_SESSION['catalogFilter']);
			}else{
				$_SESSION['catalogFilter'] = $model->getId();
			}
		}
		$this->_redirect('/admin/catalog');
	}
	
	public function changecatalogAction(){
		$model = new Default_Model_CatalogProducts();
		$select = $model->getMapper()->getDbTable()->select();
		$result = $model->fetchAll($select);
		if($result)
		{
			foreach($result as $value)
			{				
				$model2 = new Default_Model_CatalogProducts();
				$model2->find($value->getId());
				
				$allowed = "/[^a-z0-9\\-\\_]+/i";  
				$folderName = preg_replace($allowed,"-", strtolower(trim($model2->getName())));
				$folderName = trim($folderName,'-');
				if(file_exists(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $model2->getUser_id() . '/' . $model2->getName())) {
					if(rename(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $model2->getUser_id() . '/' . $model2->getName(),APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $model2->getUser_id() . '/' . $folderName)){
						$model3 = new Default_Model_CatalogProductImages();
						$select2 = $model3->getMapper()->getDbTable()->select()
								->where('product_id = ?',$value->getId());
						$result2 = $model3->fetchAll($select2);
						if($result2)
						{
							$i=1;
							foreach($result2 as $value2)
							{
								$tmp = pathinfo(APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$model2->getUser_id().'/'.$folderName.'/big/'.$value2->getName());
								$extension = (!empty($tmp['extension']))?$tmp['extension']:null;							
								
								$pozaNume = $folderName.'-'.$i.'.'.$extension;
								$model4 =  new Default_Model_CatalogProductImages();
								if($model4->find($value2->getId())){
									rename(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $model2->getUser_id() . '/' .$folderName.'/big/'.$value2->getName(),APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $model2->getUser_id() . '/' . $folderName.'/big/'.$pozaNume);
									rename(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $model2->getUser_id() . '/' .$folderName.'/small/'.$value2->getName(),APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $model2->getUser_id() . '/' . $folderName.'/small/'.$pozaNume);
									$model4->setName($pozaNume);
									$model4->save();
								}
								$i++;
							}							
						}						
					}

				}
			}
		}	
		$this->_redirect('/admin/catalog');
	}

	public function tagsAction()
	{
		$model = new Default_Model_Tags();
		$select = $model->getMapper()->getDbTable()->select()
				->order('name ASC');
		$result = $model->fetchAll($select);
		if(NULL != $result)
		{
            $this->paginateResult($result);
		}
	}
	
	public function tagseditAction()
	{
		$id = (int) $this->getRequest()->getParam('id');
		$model = new Default_Model_Tags();
		if($model->find($id))
		{
			$form = new Admin_Form_Tags();
			$form->edit($model);
			$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/tagsEdit.phtml'))));
			$this->view->form = $form;
			
			if($this->getRequest()->isPost()) {
				if($form->isValid($this->getRequest()->getPost())) {
					$model->setOptions($form->getValues());
					if($model->save()) {
						$this->_flashMessenger->addMessage('<div class="mess-true">Success! Tag was modified.</div>');
					} else {
						$this->_flashMessenger->addMessage('<div class="mess-false">Error! Tag was not modified.</div>');
					}
				}
				$this->_redirect('/admin/catalog/tags');
			}
		}
		else
		{
			$this->_flashMessenger->addMessage('<div class="mess-false">Error! Invalid selection.</div>');
			$this->_redirect('/admin/catalog/tags');
		}
	}

	public function tagsdeleteAction()
	{
		$id = $this->getRequest()->getParam('id');
		$model = new Default_Model_Tags();
		$asocNr = 0;
		if($model->find($id))
		{
			if($model->delete())
			{
				$asoc = new Default_Model_CatalogProductTags();
				$select = $asoc->getMapper()->getDbTable()->select()
						->where('tag_id = ?', $id);
				$result = $asoc->fetchAll($select);
				if(NULL != $result)
				{
					foreach($result as $value)
					{
						if($value->delete())
						{
							$asocNr++;
						}
					}
				}
				$this->_flashMessenger->addMessage('<div class="mess-true">Success! Tag and '.$asocNr.' product asociations deleted.</div>');
			}
		}
		$this->_redirect('/admin/catalog/tags');
	}
	
	public function infoAction()
	{
		$id = $this->getRequest()->getParam('id');
		$model = new Default_Model_CatalogProducts();
		if($model->find($id))
		{
			$this->view->gallery = $model;
		}
		else
		{
			$this->_flashMessenger->addMessage('<div class="mess-false">Error! Invalid gallery selected.</div>');
			$this->_redirect('/admin/catalog');
		}
	}
}