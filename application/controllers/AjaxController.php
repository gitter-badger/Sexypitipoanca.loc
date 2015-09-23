<?php
class AjaxController extends TS_Controller_Action
{
    public function addtofavoritesAction()
    {
    	$response = "";
    	$articleId = (int) $this->getRequest()->getParam('id');
    	$type = $this->getRequest()->getParam('type');
    	if(Zend_Registry::isRegistered('authUser')) // check if user is logged in
    	{
    	    $model = new Default_Model_CatalogProducts();
			if($model->find($articleId)){
				// BEGIN: Check if already favorited
				$favorites = new Default_Model_CatalogProductFavorites();
				$select = $favorites->getMapper()->getDbTable()->select()
					->where('userId = ?', Zend_Registry::get('authUser')->getId())
					->where('productId = ?', $articleId);
				$result = $favorites->fetchAll($select);
				// END: Check if already favorited
				if(!$result)
				{
					// BEGIN: Add to favorites
					$favorites->setUserId(Zend_Registry::get('authUser')->getId());
					$favorites->setProductId($articleId);
					$favorites->setType($type);
					if($favorites->save())
					{
						$response = "ok";
					}
					// END: Add to favorites
				}
			}
    	}
    	echo Zend_Json_Encoder::encode($response);
    }

	public function statisticsRatingAction()
	{
		// BEGIN: SET/FETCH PARAMS
		$response = array();
		$id = (int) $this->getRequest()->getParam('id');
		$score = (float) $this->getRequest()->getParam('score');
		// END: SET/FETCH PARAMS
		
		require_once APPLICATION_PUBLIC_PATH.'/library/Needs/Statistics.php';
		$tsStatistics = new tsStatistics();
		$statistics = $tsStatistics->findStatistics();

		if(null != $id && null != $score && null != $statistics){
			$statisticsId = $statistics->getId();
			if($tsStatistics->alreadyRated($statisticsId, $id) == false){
				$model = new Default_Model_CatalogProducts();
				if($model->find($id)){
					$modelStat = new Default_Model_StatisticsRatings();
					$modelStat->setStatisticsId($statisticsId);
					$modelStat->setProductId($id);
					$modelStat->setRating($score);
					$modelStat->save();
				}
			}
		}
		echo Zend_Json_Encoder::encode($response);
	}
	
	public function subscribeAction()
	{
		$response = "";
		$email = $this->getRequest()->getParam('email');
		if(null != $email)
		{
			$validEmail = new Zend_Validate_EmailAddress();
			if ($validEmail->isValid($email))	
			{
				$validRecord = new Zend_Validate_Db_NoRecordExists 
				(
					array
					(
						'table' => 'j_newsletter_subscribers',
						'field' => 'email'
					)
				);
				if ($validRecord->isValid($email)) 
				{
					$model = new Default_Model_NewsletterSubscribers();
					$model->setEmail($email);
					if($model->save())
					{
						$response = '<div class="submess-true">Adresa ta a fost adaugata!</div>';
					}else{
						$response = '<div class="submess-false">Eroare adaugare adresa!</div>';
					}
				}else{
					$response = '<div class="submess-false">Aceasta adresa exista deja in baza de date!</div>';
				}
			}else{
				$response = '<div class="submess-false">Adresa de email eronata!</div>';
			}	
		}
		else{
			$response = '<div class="submess-false">Adresa de email eronata!</div>';
		}
		echo Zend_Json_Encoder::encode($response);
	}	
	
	public function uploadAction()
	{
		if (!empty($_FILES)) {
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
			$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];

			// $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
			// $fileTypes  = str_replace(';','|',$fileTypes);
			// $typesArray = split('\|',$fileTypes);
			// $fileParts  = pathinfo($_FILES['Filedata']['name']);

			// if (in_array($fileParts['extension'],$typesArray)) {
				// Uncomment the following line if you want to make the directory if it doesn't exist
				// mkdir(str_replace('//','/',$targetPath), 0755, true);

				move_uploaded_file($tempFile,$targetFile);
				echo Zend_Json_Encoder::encode(str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile));
			// } else {
			// 	echo 'Invalid file type.';
			// }
		}
	}

	public function fbshareAction()
	{
		$response = '<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fsexypitipoanca.ro%2F&amp;send=false&amp;layout=standard&amp;width=220&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font=arial&amp;height=80&amp;appId=269917363026754" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:220px; height:80px;" allowTransparency="true"></iframe>';
		echo Zend_Json_Encoder::encode($response);
	}

	public function addcommentAction()
	{
		$response = array();
		$response['success'] = 'false';
		$id = $this->getRequest()->getParam('product_id');
		$parentId = $this->getRequest()->getParam('parentId');
		$email = $this->getRequest()->getParam('email');
		$nume = $this->getRequest()->getParam('nume');
		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		$account = new Default_Model_AccountUsers();
		if($authAccount){
			if($authAccount->getId()){
				$userId = $authAccount->getId();				
				$account->find($authAccount->getId());
			}
		}
		if($id != NULL){
			$model = new Default_Model_CatalogProductComments();
			$model->setProduct_id($id);
			if($parentId != null)
			{
				$model->setParentId($parentId);
				$response['parentId'] = $parentId;
			}
			if(!empty($userId))
			{
				$model->setUserId($userId);
			}
			
			$model->setName($nume);
			$model->setEmail($email);
			$model->setComment($this->getRequest()->getParam('message'));
			if($commId = $model->save()){
				$model = new Default_Model_CatalogProductComments();
				$model->find($commId);
				if($parentId)
				{
					$response['message']  ="
					<div class=\"raspunsCommentariu\">
					<div class=\"left_comentariu_raspuns\">	";
					if($account !== null && null != $account->getAvatar()) {
						$response['message'].= "<img width=\"44\" height=\"44\" src='/media/avatar/small/".$account->getAvatar()."' alt=\"user\" />";
					 }else{
						$response['message'].= "<img width=\"44\" height=\"44\" src=\"/theme/default/images/user.gif\" alt=\"user\" />";
					}
					$response['message'].= "</div>
						<div class=\"right_comentariu_raspuns\">
							<p class=\"nume\">
								<a href=\"#\">{$model->getName()}</a> l <span>".date('d/m/Y H:i', $model->getAdded()) ."</span>
							</p>
							<p class=\"descriere\">{$model->getComment()}</p>
						</div>
						<div class=\"clear\"></div>
					</div>";
				}
				else
				{
					$response['message'] = '<div class="mess-true">Comentariul tau a fost salvat</div>';
					
					$response['comment'] = '
						<div class="modul_comentariu">
							<div class="left_comentariu">';
							if($account !== null && null != $account->getAvatar()) {
								$response['comment'].= "<img width=\"44\" height=\"44\" src='/media/avatar/small/".$account->getAvatar()."' alt=\"user\" />";
							}else{
								$response['comment'].= "<img width=\"44\" height=\"44\" src=\"/theme/default/images/user.gif\" alt=\"user\" />";
							}							
							$response['comment'].= '</div>
							<div class="right_comentariu">
								<p class="nume">
									<a href="#">'.$model->getName().'</a> l <span>'.date('d/m/Y H:i', $model->getAdded()) .'</span>
								</p>
								<p class="descriere">'.$model->getComment().'</p>
								<div id="all_raspunsCommentariu_<?php echo $comment->getId() ?>">
								</div>
								<div class="footer_comentariu">
									<div class="left_footer_comentariu">
									</div>
									<div class="right_footer_comentariu">
										<p>
											<a href="javascript: void(0)" rel='.$model->getId().' class="showHideForm" id="showHideForm_'.$model->getId().'">
												Raspunde
												<img src="/theme/default/images/default/sageata.png"/>
											</a>
										</p>

									</div>
								</div>
								<div class="modul_coment1 jsToggleComment-'.$model->getId().'" id="jsToggleComment-'.$model->getId().'" style="display: none">
									<div id="jsToggleCommentMessage-'.$model->getId().'">
									</div>
									<form method="POST" action="" class="replyForm" rel="'.$model->getId().'">
										<div>
											<input type="hidden" id="productId_'.$model->getId().'" value="'.$id.'" name="product_id">
											<input type="hidden" id="parentId_'.$model->getId().'" value="'.$model->getId().'" name="parentId" class="f5"  />';
								if($account){
									$response['comment'].='<input name="userIdComment" type="hidden" id="userIdComment" value="'.$account->getId().'" />
												<input name="nume" type="hidden"  value="'.$account->getUsername().'" />
												<input name="email" type="hidden"  value="'.$account->getEmail().'"/>';
								 }  else { 
											$response['comment'].='<input name="nume" id="nume_'.$model->getId().'" type="text"  class="f5 validate[required]" value="Nume" onfocus="if(this.value == \'Nume\') {this.value = \'\';}"  onblur="if (this.value == \'\') {this.value = \'Nume\';}" />
												<input name="email" id="email_<?php echo $comment->getId() ?>" type="text"  class="f5 ml20 validate[required]" value="Email" onfocus="if(this.value == \'Email\') {this.value = \'\';}"  onblur="if (this.value == \'\') {this.value = \'Email\';}" />';
								 } ;

										$response['comment'].='</div>
										<textarea name="message" cols="" rows="" id="message_'.$model->getId().'" class="txt" maxlenght="250" onfocus="if(this.value == \'Comentariu\') {this.value = \'\';}"  onblur="if (this.value == \'\') {this.value = \'Comentariu\';}">Comentariu</textarea>
										<input name="trimite" type="submit" value="Trimite" class="bt_trimite" />
									</form>
									<div class="clear"></div>
								</div>
							</div>
							<div style="clear:both; width:1px"></div>
						</div>
';
				}
				
				$response['success'] = 'true';
			}else{
				$response['message'] = '<div class="mess-false">Comentariul tau a nu fost salvat</div>';
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
}