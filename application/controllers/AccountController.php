<?php

/**
 * @property Zend_Controller_Action_Helper_Abstract _flashMessenger
 */
class AccountController extends Base_Controller_Action
{
	public function indexAction()
	{
		if ($authAccount = $this->isAuthenticated()) {
            $account = new Default_Model_AccountUsers();
            $account->find($authAccount->getId());
            $this->view->result = $account;
            $this->_redirect('/account/edit');
		}

        $this->_redirect('/account/new');
	}

    /**
     * gallerii, show the galleries for the selected user
     */
	public function galeriiAction()
	{
		$userId = $this->findUserByUsername();

		$model = new Default_Model_CatalogProducts();
		$select = $model->getMapper()->getDbTable()->select()
			->where('user_id = ?', $userId)
			->where('type = ?', 'gallery')
			->where('status = ?', '1')
			->order('added DESC');

		// paginate the result
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setPageRange(5);
		$this->view->galleries = $paginator;
		$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
		$this->view->totalItemCount = $paginator->getTotalItemCount();

		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');
	}
	
	public function clipuriAction()
	{
		$userId = $this->findUserByUsername();

		$model = new Default_Model_CatalogProducts();
		$select = $model->getMapper()->getDbTable()->select()
			->where('user_id = ?', $userId)
			->where("type = 'video' OR type = 'embed'")
			->where('status = ?', '1')
			->order('added DESC');

		// paginate the result
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setPageRange(5);
		$this->view->galleries = $paginator;
		$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
		$this->view->totalItemCount = $paginator->getTotalItemCount();

		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');
	}

	protected function findUserByUsername()
	{
		if (!Zend_Registry::isRegistered('currentUser')) {
			throw new Zend_Controller_Action_Exception('No registered user with that username', 404);
		}

		$username = $this->getRequest()->getParam('username');
		$model = new Default_Model_AccountUsers();
		$select = $model->getMapper()->getDbTable()->select()
			->where('username = ?', $username);
		$user = $model->fetchRow($select);

		if ($user) {
			$this->view->currentUser = $user;
			return $user->getId();
		}

		return null;
	}
	
	public function pmAction()
	{
		$uid = $this->getRequest()->getParam('uid');
		$account = new Default_Model_AccountUsers();
		if($account->find($uid))
		{
			$this->view->result = $account;
			
			$messages = TS_Message::getMessagesFromUser(Zend_Registry::get('authUser')->getId(), $account->getId());
			$this->view->messages = $messages;
			
			$form = new Default_Form_Message();
			$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/message.phtml'))));
			$this->view->form = $form;
			if($this->getRequest()->isPost())
			{
				if($form->isValid($this->getRequest()->getPost()))
				{
					$authUser = Zend_Registry::get('authUser');
					$messageModel = new Default_Model_Message();
					$messageModel->setSentTo($uid);
					$messageModel->setFrom($authUser->getId());
					$messageModel->setContent($form->getValue('message'));
					if($messageModel->save())
					{
						$this->_redirect('/account/pm/uid/'.$uid);
					}
				}
			}
		}else
		{
			$this->_redirect('/');
		}
	}
	
	/**
	 * ToDo: move to AjaxController
	 */
	public function befriendAction()
	{
		$userId = $this->getRequest()->getParam('uid');
		$user = TS_SocialNetwork::userExists($userId);
		
		if($user && Zend_Registry::get('isAuthUser'))
		{
			$socialModel = new Default_Model_SocialUserConnections();
			$socialModel->setInitiatorUserId(Zend_Registry::get('authUser')->getId());
			$socialModel->setReceiverUserId($userId);
			$socialModel->setIsConfirmed(0);
			$socialModel->setType('friend');
			if($socialModel->save())
			{
				$this->_redirect('/user/'.$user->getUsername());
			}
		}
	}

    /**
     * Show friendship requests
     */
    public function userRequestsAction()
	{
		if ($authAccount = $this->isAuthenticated()) {
			$model = new Default_Model_SocialUserConnections();
			$select = $model->getMapper()->getDbTable()->select()
					->where('receiverUserId = ?', $authAccount->getId())
					->where('isConfirmed IS FALSE');
			$result = $model->fetchAll($select);
			if(NULL != $result)
			{
				$this->view->result = $result;
			}
		}
	}
	
	public function prieteniAction()
	{
		// BEGIN: DISPLAY SEARCH FORM
		$form = new Default_Form_UserSearch();
		$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/userSearch.phtml'))));
		$this->view->searchForm = $form;
		// END: DISPLAY SEARCH FORM
		
		// BEGIN: Set display user id
		$username = $this->getRequest()->getParam('username');
		$currentUser = TS_SocialNetwork::usernameToUserModel($username);
		// END: Set display user id
		$type = 'friends';
		
		// BEGIN: Search
		$txtSearch = $this->getRequest()->getParam('txtHeaderSearch');
		if($txtSearch)
		{		
			$type = 'users';	
			if($this->getRequest()->getPost('txtHeaderSearch')){
				$txtSearch = $this->getRequest()->getPost('txtHeaderSearch');
			}		
			$form->txtHeaderSearch->setValue($txtSearch);
			
			$model = new Default_Model_AccountUsers();
			$select = $model->getMapper()->getDbTable()->select();			
			$select->where("username LIKE '%".$txtSearch."%'");
			$select->order('username DESC');
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
			$paginator->setItemCountPerPage(50);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			$paginator->setPageRange(5);
			$this->view->customers = $paginator;
			$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
			$this->view->totalItemCount = $paginator->getTotalItemCount();
	
			$param = array();
			if($txtSearch){
				$param = array('txtHeaderSearch' => $txtSearch);
			}
			
			Zend_Paginator::setDefaultScrollingStyle('Sliding');
			Zend_View_Helper_PaginationControl::setDefaultViewPartial(array('_pagination.phtml', $param));
			
		}

		// END: Search
		else
		{
			if($currentUser != NULL)
			{
				$userId = $currentUser->getId();
				$model = new Default_Model_SocialUserConnections();
				$select = $model->getMapper()->getDbTable()->select()
						->where("(receiverUserId = {$userId} OR initiatorUserId = {$userId})")
						->where('isConfirmed IS TRUE');
				$result = $model->fetchAll($select);
				if(NULL != $result)
				{
					$this->view->userId = $userId;
					$this->view->result = $result;
				}
			}
		}
		$this->view->type = $type;
	}
	
	public function unfriendAction()
	{
		$user = Zend_Registry::get('authUser');
		
		if(NULL != $user)
		{
			$friendId = $this->getRequest()->getParam('uid');
			$model = new Default_Model_SocialUserConnections();
			$select = $model->getMapper()->getDbTable()->select()
					->where('initiatorUserId = ?', $user->getId())
					->where('receiverUserId = ?', $friendId)
					->orwhere('initiatorUserId = ?', $friendId)
					->where('receiverUserId = ?', $user->getId());
			$result = $model->fetchAll($select);
			if(null != $result)
			{
				$result[0]->delete();
			}
		}
		$this->_redirect('/friends/'.$user->getUsername());
	}
	
	public function acceptRequestAction()
	{
		$this->handleRequest(true);
	}
	
	public function denyRequestAction()
	{
        $this->handleRequest(false);
	}
	
	public function messagesAction()
	{
		// BEGIN: Get logged in user
		$sentTo = NULL;
		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if(null != $authAccount)
		{
			if(null != $authAccount->getId())
			{
				$sentTo = $authAccount->getId();
			}
		}else{
			$this->_redirect('/');
		}
		// END: Get logged in user
		
		if(NULL != $sentTo)
		{
			$this->view->messages = TS_Message::getMessageNotificationsFromUser($sentTo);
		}
	}

	public function wallAction()
	{
		if (!Zend_Registry::isRegistered('currentUser')) {
			throw new Zend_Controller_Action_Exception('No registered user with that username', 404);
		} else {
            // get selected username
            $currentUser = Zend_Registry::get('currentUser');
            $this->view->currentUser = $currentUser;

            // Begin: Show user activity
            $catalogModel = new Default_Model_CatalogProducts();
            $select = $catalogModel->getMapper()->getDbTable()->select()
                ->where('user_id = ?', $currentUser->getId())
                ->where('status = ?', '1')
                ->order('added DESC')
                ->limit(10);

            // paginate the result
            $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
            $paginator->setItemCountPerPage(10);
            $paginator->setCurrentPageNumber($this->_getParam('page'));
            $paginator->setPageRange(5);
            $this->view->activity = $paginator;
            $this->view->itemCountPerPage = $paginator->getItemCountPerPage();
            $this->view->totalItemCount = $paginator->getTotalItemCount();
            Zend_Paginator::setDefaultScrollingStyle('Sliding');
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');
            // End: Show user activity


			$username = $this->getRequest()->getParam('username');
			if($this->getRequest()->isPost())
			{
				$userId = Zend_Registry::get('authUser')->getId();
				if($this->getRequest()->getPost('action') == 'status')
				{
					$modelArticle = new Default_Model_CatalogProducts();
					$modelArticle->setUser_id($userId);
					$modelArticle->setDescription($this->getRequest()->getPost('updateStatus'));
					$modelArticle->setType('status');
					$modelArticle->setStatus(1);
					if($modelArticle->save())
					{
						$this->_redirect('/user/'.$username);
					}
				}
				elseif($this->getRequest()->getPost('action') == 'gallery')
				{
					// BEGIN: Save article
					$modelArticle = new Default_Model_CatalogProducts();
					$modelArticle->setUser_id($userId);
					$modelArticle->setName($this->getRequest()->getPost('photo_title'));
					$modelArticle->setDescription($this->getRequest()->getPost('photo_description'));
					$modelArticle->setCategory_id($this->getRequest()->getPost('photo_categ'));
					$modelArticle->setType('gallery');
					$modelArticle->setStatus(1);
					$productId = $modelArticle->save();
					// END: Save article
					
					if($productId)
					{
						// BEGIN: Save tags
						$tags = $this->getRequest()->getPost('photo_tags');
						TS_Catalog::saveTags($productId, $tags);
						// END: Save tags
						
						// BEGIN: Save images
						$allowed = "/[^a-z0-9\\-\\_]+/i";  
						$folderName = preg_replace($allowed,"-", strtolower(trim($this->getRequest()->getPost('photo_title'))));	
						$folderName = trim($folderName,'-');
						if(!file_exists(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $userId. '/' . $folderName . '/')) {
							mkdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $userId . '/' . $folderName . '/', 0777, true);
							mkdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $userId . '/' . $folderName . '/big', 0777, true);
							mkdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/' . $userId . '/' . $folderName . '/small', 0777, true);
						}
						
						$imageCount = $this->getRequest()->getPost('uploader_count');
						for ($i = 0; $i < $imageCount; $i++)
						{
							$imageName = $this->getRequest()->getPost('uploader_'.$i.'_name');
							$tmpName = $this->getRequest()->getPost('uploader_'.$i.'_tmpname');
							TS_Catalog::saveImage($productId, $imageName, $tmpName, $folderName);
						}
						// END: Save images
					}
					$this->_redirect('/user/'.$username);
				}
				elseif($this->getRequest()->getPost('action') == 'video')
				{
					$modelArticle = new Default_Model_CatalogProducts();
					$modelArticle->setUser_id($userId);
					$modelArticle->setName($this->getRequest()->getPost('video_title'));
					$description = $this->getRequest()->getPost('video_description');
					if($description != 'Descriere. . . '){
						$modelArticle->setDescription($description);
					}				
					$modelArticle->setCategory_id($this->getRequest()->getPost('video_categ'));
					$modelArticle->setType('embed');
					$modelArticle->setStatus(1);
					$productId = $modelArticle->save();
					if($productId)
					{
						// BEGIN: Save tags
						$tags = $this->getRequest()->getPost('video_tags');
						if($tags != 'Adauga tag-uri separe prin virgula'){
							TS_Catalog::saveTags($productId, $tags);
						}
						// END: Save tags
						$modelVideo = new Default_Model_Video();
						$modelVideo->setProductId($productId);
						$modelVideo->setUrl($this->getRequest()->getPost('video_url'));
						if($modelVideo->save())
						{
							$this->_redirect('/user/'.$username);
						}
					}
				}
			}
			
			$model = new Default_Model_AccountUsers();
			$select = $model->getMapper()->getDbTable()->select()
					->where('username = ?', $username);
			$result = $model->fetchAll($select);
			if(NULL != $result)
			{
				$this->view->result = $result[0];
			}
		}
	}

	public function termsAction()
	{
		
	}

	public function newAction()
	{
		if ($authAccount = $this->isAuthenticated()) {
            $account = new Default_Model_AccountUsers();
            $account->find($authAccount->getId());
            $this->_redirect('/account');
		} else {
			$form = new Default_Form_Account();
			$form->add();
			$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/account/new.phtml'))));
			$this->view->form = $form;

			if($this->getRequest()->isPost()) {
				if($this->getRequest()->getPost('control') == 'new') {
					if($form->isValid($this->getRequest()->getPost())) {
						if($form->getValue('terms') == '1') {
							$birthday = mktime(date('j'), date('i'), date('s'), $form->getValue('birth_month'), $form->getValue('birth_day'), $form->getValue('birth_year'));
							$allowed = mktime(date('j'), date('i'), date('s'), date('m'), date('d'), date('Y')-18);
							if($birthday <= $allowed) {
								$model = new Default_Model_AccountUsers();
								$model->setOptions($form->getValues());
								$model->setRoleId('3');
								$model->setBirth_day($form->getValue('birth_year').'-'.$form->getValue('birth_month').'-'.$form->getValue('birth_day'));
								$model->setPassword(md5($form->getValue('passwordnew')));
								if($form->image->receive())
								{
									if($form->image->getFileName())
									{
										$tmp = pathinfo($form->image->getFileName());
										$extension = (!empty($tmp['extension']))?$tmp['extension']:null;
										$filename = md5(uniqid(mt_rand(), true)).'.'.$extension;
										if(@copy($form->image->getFileName(), APPLICATION_PUBLIC_PATH.'/media/avatar/'.$filename))
										{
											require_once APPLICATION_PUBLIC_PATH.'/library/Needs/tsThumb/ThumbLib.inc.php';
											$thumb = PhpThumbFactory::create(APPLICATION_PUBLIC_PATH.'/media/avatar/'.$filename);
											$thumb->resize(233, 176)->save(APPLICATION_PUBLIC_PATH.'/media/avatar/big/'.$filename);
											$thumb->tsResizeWithFill(44, 44, 'ffffff')->save(APPLICATION_PUBLIC_PATH.'/media/avatar/small/'.$filename);
                                            if (file_exists(APPLICATION_PUBLIC_PATH.'/media/avatar/'.$filename)) {
                                                unlink(APPLICATION_PUBLIC_PATH.'/media/avatar/'.$filename);
                                            } else {
                                                // ToDo: catch exception
                                                echo 'file not found';
                                            }
											$model->setAvatar($filename);
										}
									}
								}
								$activation_code = substr(md5(uniqid(mt_rand(), true)),0,10);
								$model->setActivationcode($activation_code);
								$model->setStatus('0');
								if($model->save()) {
									if($form->getValue('newsletter') == '1') {
										$model2 = new Default_Model_NewsletterSubscribers();										
										$model2->setEmail($form->getValue('email'));										
										$model2->save();
									}
									
									//BEGIN:ACTIVARE
									$url            = "http://".$_SERVER['SERVER_NAME'];
									$username		= $model->getUsername();
                                    $email          = $model->getEmail();
									$activationlink = '<a href="'.$url.'/auth/activation?code='.$activation_code.'">Activare</a>';
									
									$signup = new Default_Model_Templates();
									$signup->find('signUp');
									
									$subject = $signup->getSubjectro();
									$message = $signup->getValuero();
									
									$message = str_replace('{'.'$'.'username}', $username, $message);
									$message = str_replace('{'.'$'.'email}', $email, $message);
									$message = str_replace('{'.'$'.'activationlink}', $activationlink, $message);	
									
									// ToDo: change hardcoded variables
									$emailcompany = 'contact@sexypitipoanca.ro';
									$institution = 'SexyPitipoanca.ro';
									
									$mail = new Zend_Mail();
									$mail->setFrom($emailcompany, $institution);
									$mail->setSubject($subject);
									$mail->setBodyHtml($message);
									$mail->addTo($model->getEmail());
									$mail->send();
									//END:ACTIVARE
									
									$this->_flashMessenger->addMessage('<span class="mess-true">Contul a fost creat cu succes. Un email cu codul de activare a fost trimis!</span>');
									$this->_redirect('/account/new');
								}else{
									$this->_flashMessenger->addMessage('<span class="mess-false">Eroare! Contul nu a putut fi creat! Va rugam incercati mai tarziu!</span>');
								}
							} else {							
								$this->_flashMessenger->addMessage('<span class="mess-false">Eroare! Contul nu a putut fi creat! Trebuie sa aveti peste 18 ani!</span>');
								$this->_redirect('/account/new');
							}
						}
					}
				}
			}
		}
	}

	public function editAction()
	{
        if ($authAccount = $this->isAuthenticated()) {
            $account = new Default_Model_AccountUsers();
            $form = new Default_Form_Account();
            $form->add();
            $form->edit($account);
            $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/account/edit.phtml'))));
            $this->view->form = $form;

            $formPassword = new Default_Form_Account();
            $formPassword->editPassword();
            $formPassword->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/account/editPassword.phtml'))));
            $this->view->formPassword = $formPassword;

            if($this->getRequest()->isPost()) {
                if($this->getRequest()->getPost('control') == 'edit'){
                    if($form->isValid($this->getRequest()->getPost())){
                        $birthday = mktime(date('j'), date('i'), date('s'), $form->getValue('birth_month'), $form->getValue('birth_day'), $form->getValue('birth_year'));
                        $allowed = mktime(date('j'), date('i'), date('s'), date('m'), date('d'), date('Y')-18);
                        if($birthday <= $allowed) {
                            $account->setOptions($form->getValues());
                            $account->setBirth_day($form->getValue('birth_year').'-'.$form->getValue('birth_month').'-'.$form->getValue('birth_day'));
                            $oldAvatar = $account->getAvatar();

                            if($form->image->receive())
                            {
                                if($form->image->getFileName())
                                {
                                    $tmp = pathinfo($form->image->getFileName());
                                    $extension = (!empty($tmp['extension']))?$tmp['extension']:null;
                                    $filename = md5(uniqid(mt_rand(), true)).'.'.$extension;
                                    if(@copy($form->image->getFileName(), APPLICATION_PUBLIC_PATH.'/media/avatar/'.$filename))
                                    {
                                        require_once APPLICATION_PUBLIC_PATH.'/library/Needs/tsThumb/ThumbLib.inc.php';
                                        $thumb = PhpThumbFactory::create(APPLICATION_PUBLIC_PATH.'/media/avatar/'.$filename);
                                        $thumb->adaptiveResize(233, 176)->save(APPLICATION_PUBLIC_PATH.'/media/avatar/big/'.$filename);
                                        $thumb->tsResizeWithFill(44, 44, 'ffffff')->save(APPLICATION_PUBLIC_PATH.'/media/avatar/small/'.$filename);
                                        if (file_exists(APPLICATION_PUBLIC_PATH.'/media/avatar/'.$filename)) {
                                            unlink(APPLICATION_PUBLIC_PATH.'/media/avatar/'.$filename);
                                        } else {
                                            // ToDo: catch exception
                                            echo 'file not found';
                                        }
                                        $account->setAvatar($filename);
                                    }
                                }
                            }

                            if($account->save()) {
                                if(null != $oldAvatar){
                                    if (file_exists(APPLICATION_PUBLIC_PATH.'/media/avatar/big/'.$oldAvatar)) {
                                        unlink(APPLICATION_PUBLIC_PATH.'/media/avatar/big/'.$oldAvatar);
                                    } else {
                                        // ToDo: catch exception
                                        echo 'file not found';
                                    }
                                    if (file_exists(APPLICATION_PUBLIC_PATH.'/media/avatar/small/'.$oldAvatar)) {
                                        unlink(APPLICATION_PUBLIC_PATH.'/media/avatar/small/'.$oldAvatar);
                                    } else {
                                        // ToDo: catch exception
                                        echo 'file not found';
                                    }
                                }
                                $this->_flashMessenger->addMessage('<span class="mess-true">Modficarile au fost efectuate cu succes</span>');
                            }else{
                                $this->_flashMessenger->addMessage('<span class="mess-false">Eroare! Modificarile nu au fost facute!</span>');
                            }
                        } else {
                            $this->_flashMessenger->addMessage('<span class="mess-false">Eroare! Modificarile nu au fost facute! Trebuie sa aveti peste 18 ani.</span>');
                        }
                    }
                } elseif($this->getRequest()->getPost('control') == 'editPassword') {
                    $this->changePassword($formPassword, $account);
                }
                $this->_redirect('/account/edit');
            }
		} else {
			$this->_redirect('/account');
		}
	}
	
	public function favoritesAction()
	{
		if (!Zend_Registry::isRegistered('currentUser')) {
			throw new Zend_Controller_Action_Exception('No registered user with that username', 404);
		} else {
			$userId = Zend_Registry::get('currentUser')->getId();
				$result = TS_Products::favoriteProducts($userId);
				if (NULL != $result) {
					$paginator = Zend_Paginator::factory($result);
					$paginator->setItemCountPerPage(15);
					$paginator->setCurrentPageNumber($this->_getParam('page'));
					$paginator->setPageRange(5);
					$this->view->result = $paginator;
					$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
					$this->view->totalItemCount = $paginator->getTotalItemCount();

					Zend_Paginator::setDefaultScrollingStyle('Sliding');
					Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');
				}
		}	
	}

	public function deletefavoritesAction(){
		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if ($authAccount) {
			if ($authAccount->getId()) {
				$account = new Default_Model_AccountUsers();
				$account->find($authAccount->getId());
				$id = (int) $this->getRequest()->getParam('id');

				$model = new Default_Model_CatalogProductFavorites();
				$select = $model->getMapper()->getDbTable()->select()
						->where('userId = ?', $account->getId())
						->where('productId = ?', $id)
						->limit(1);
				$result = $model->fetchAll($select);
				if(null != $result){
					$result[0]->delete();
				}
				$this->_redirect('/favorite/'.$account->getUsername());
			}else{
				$this->_redirect('/account/new');
			}
		}
	}

	public function uploadAction()
	{
		
	}

    protected function changePassword($formPassword, $account)
    {
        if($formPassword->isValid($this->getRequest()->getPost())) {
            $post = $this->getRequest()->getPost();
            if (md5($post['oldPassword']) == $account->getPassword()) {
                $account->setPassword(md5($post['password']));
                if($account->save()) {
                    $this->_flashMessenger->addMessage('<span class="mess-true">Modificarile au fost efectuate cu succes</span>');
                }else{
                    $this->_flashMessenger->addMessage('<span class="mess-true">Eroare! Parola nu a putut fi modificata.</span>');
                }
            } else{
                $this->_flashMessenger->addMessage('<span class="mess-false">Eroare! Parola veche eronata!</span>');
            }
        }
    }

    /**
     * handle request, used to handle social request between users
     * @param bool $accept
     * @throws Exception
     */
    protected function handleRequest($accept = true)
    {
        $id = $this->getRequest()->getParam('id');
        $model = new Default_Model_SocialUserConnections();
        if ($model->find($id)) {
            if ($accept) {
                $model->setIsConfirmed(1);
                $model->save();
            } else {
                $model->delete();
            }
        }
        $this->_redirect('/account/user-requests');
    }

    protected function isAuthenticated()
    {
        $auth = Zend_Auth::getInstance();
        $authAccount = $auth->getStorage()->read();
        if (null != $authAccount) {
            $result = $authAccount;
        } else {
            $result = false;
        }

        return $result;
    }
}