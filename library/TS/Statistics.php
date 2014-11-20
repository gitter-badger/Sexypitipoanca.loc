<?php
class TS_Statistics
{
	public function findStatistics(){
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date('Y-m-d');
		if(null != $ip && null != $date){
			$model = new Default_Model_Statistics();
			$select = $model->getMapper()->getDbTable()->select()
					->where('ip =?', $ip)
					->where('DATE(created) =?', $date)
					->limit(1);
			$result = $model->fetchAll($select);
			if(null != $result){
				return $result[0];
			}
		}else{
			return null;
		}
	}

	public function setStatistics(){
		$model = new Default_Model_Statistics();
		$ip = $_SERVER['REMOTE_ADDR'];
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$referer = $_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:"direct";

		if($model->notduplicate($ip) == true){
			$model->setIp($ip);
			$model->setUseragent($useragent);
			$model->setReferer($referer);
			$model->setCreated(time());
			if(($statisticsId = $model->save())){
				return $statisticsId;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public function setStatisticsVisit($statisticsId, $productId){
		$model = new Default_Model_StatisticsVisits();
		if($model->notduplicate($statisticsId, $productId) == true){
			$model->setStatisticsId($statisticsId);
			$model->setProductId($productId);
			$model->setCreated(time());
			if($model->save()){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public function getTags()
	{
		$model = new Default_Model_Tags();
		$select = $model->getMapper()->getDbTable()->select()
				->order('RAND()')
				->limit('18');
		$result = $model->fetchAll($select);
		if(null != $result){
			return $result;
		}else{
			return null;
		}
	}

	public function getTagsByLetter($letter)
	{
		$model = new Default_Model_Tags();
		$select = $model->getMapper()->getDbTable()->select()
//				->from($model->getMapper()->getDbTable(), array('name'=>'name', 'id'=>'COUNT(id)'))
				->where('name LIKE (?)', $letter.'%')
				->order('id DESC');
		$result = $model->fetchAll($select);
		if(null != $result){
			return $result;
		}else{
			return null;
		}
	}

	public function getTagsNumbersInProductsById($id)
	{
		$model = new Default_Model_CatalogProductTags();
		$select = $model->getMapper()->getDbTable()->select()
				->from($model->getMapper()->getDbTable(), array('id'=>'id', 'tag_id'=>'COUNT(id)'))
				->where('tag_id = ?', $id);
		$result = $model->fetchAll($select);
		if(null != $result){
			if(isset($result[0])){
				return $result[0]->getTag_id();
			}else{
				return null;
			}
			
		}else{
			return null;
		}
	}

	public function getSuperTag()
	{
		$model = new Default_Model_CatalogProductTags();
		$select = $model->getMapper()->getDbTable()->select()
				->from($model->getMapper()->getDbTable(), array('id'=>'id', 'tag_id'=>'COUNT(id)'))
				->group('tag_id')
				->order('tag_id DESC')
				->limit(1);
		$result = $model->fetchAll($select);
		if(null != $result){
			if(isset($result[0])){
				return $result[0];
			}else{
				return null;
			}
		}else{
			return null;
		}
	}

	public function tagWeight($tagNr, $superTagNr)
	{
		$result = null;
		if($superTagNr != null && $tagNr != null){
			$result = ($tagNr*100)/$superTagNr;
			return (int) $result;
		}else{
			return $result;
		}
	}

	public function getTagNameById($id)
	{
		$tagId = (int) $id;
		$model = new Default_Model_Tags();
		if($model->find($tagId)){
			return $model->getName();
		}else{
			return null;
		}
	}

	public function getCommentsNumberByGalleryId($galleryId)
	{
		if(null != $galleryId){
			$model = new Default_Model_CatalogProductComments();
			$select = $model->getMapper()->getDbTable()->select()
					->where('product_id = ?', $galleryId);
			$result = $model->fetchAll($select);
			if(null != $result){
				$numberOfCommets = 0;
				foreach($result as $value){
					$numberOfCommets++;
				}
				return $numberOfCommets;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}

	public function getImagesNumberByGalleryId($galleryId)
	{
		if(null != $galleryId){
			$model = new Default_Model_CatalogProductImages();
			$select = $model->getMapper()->getDbTable()->select()
					->where('product_id = ?', $galleryId);
			$result = $model->fetchAll($select);
			if(null != $result){
				$imagesNumber = 0;
				foreach($result as $value){
					$imagesNumber++;
				}
				return $imagesNumber;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
	
	public function prodDescSummary($description, $limit)
	{
		if(null != $description && null != $limit) {
			$summary = strip_tags($description);
			if(strlen($summary) > $limit) {
				$summary = substr($summary,0,strrpos(substr($summary,0,$limit),' ')).'&nbsp;...&nbsp;';
			}
			return $summary;
		} else {
			return null;
		}
	}

	public function getCustomerNameById($customerId)
	{
		if(null != $customerId){
			$model = new Default_Model_AccountUsers();
			$select = $model->getMapper()->getDbTable()->select()
					->where('id = ?', $customerId);
			$result = $model->fetchAll($select);
			if(null != $result){
				$customer = $result[0]->getUsername();
				return $customer;
			}else{
				return null;
			}
		}else{
			return null;
		}
	}

	public function checkIfFavorited($userId, $galleryId)
	{
		if(null != $userId && null != $galleryId){
			$model = new Default_Model_CatalogProductFavorites();
			$select = $model->getMapper()->getDbTable()->select()
					->where('userId = ?', $userId)
					->where('productId = ?', $galleryId);
			$result = $model->fetchAll($select);
			if(null != $result){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public static function calculateVisits($galleryId){
		$catalogProduct = (int) $galleryId;
		$firstEstimate = 0;
		$secondEstimate = 0;
		
		if(null != $catalogProduct){
			$model = new Default_Model_CatalogProducts();
			if($model->find($catalogProduct)){
				$firstEstimate = $model->getVisits();
			}
		}

		$model = new Default_Model_StatisticsVisits();
		$select = $model->getMapper()->getDbTable()->select()
				->where('productId = ?', $catalogProduct);
		$result = $model->fetchAll($select);
		if(null != $result){
			foreach($result as $value){
				$secondEstimate++;
			}
		}
		
		return $firstEstimate+$secondEstimate;
	}

	public function calculateRating($galleryId){
		$catalogProduct = (int) $galleryId;
		$productScore = 0;
		$productNumber = 0;
		$statScore = 0;
		$statNumber = 0;

		if(null != $catalogProduct){
			$model = new Default_Model_CatalogProducts();
			if($model->find($catalogProduct)){
				$productNumber = $model->getRatingNumber();
				$productScore = $model->getRating()*$productNumber;
			}
		}

		$model = new Default_Model_StatisticsRatings();
		$select = $model->getMapper()->getDbTable()->select()
				->where('productId = ?', $catalogProduct);
		$result = $model->fetchAll($select);
		if(null != $result){
			foreach($result as $value){
				$statScore += $value->getRating();
				$statNumber++;
			}
		}

		if(($statScore+$productScore)>0 && ($statNumber+$productNumber)>0){
			$result = ($statScore+$productScore)/($statNumber+$productNumber); //Media ponderata
		}else{
			$result = 0;
		}
		return number_format($result, 1);
	}

	public function alreadyRated($statisticsId, $productId)
	{
		$statisticsId = (int) $statisticsId;
		$productId = (int) $productId;
		$date = date('Y-m-d');

		if(null != $statisticsId && null != $date){
			$model = new Default_Model_StatisticsRatings();
			$select = $model->getMapper()->getDbTable()->select()
					->where('statisticsId =?', $statisticsId)
					->where('productId =?', $productId)
					->where('DATE(created) =?', $date)
					->limit(1);
			$result = $model->fetchAll($select);
			if(null != $result){
				return true;
			}
		}else{
			return false;
		}
	}

	public function calculateStatisticsVisitsForCustomGallery($galleryId)
	{
		$galleryId = (int) $galleryId;
		if(null != $galleryId){
			$model = new Default_Model_StatisticsVisits();
			$select = $model->getMapper()->getDbTable()->select()
					->from(array('j_statistics_visits'), array('id'=>'COUNT(id)'))
					->where('productId = ?', $galleryId);
			$result = $model->fetchAll($select);
			if(null != $result){
				if(isset($result[0])){
					return $result[0]->getId();
				}else{
					return 0;
				}
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}

	public function getTopVisited($limit = null)
	{
		$topVisits = array();
		$topVisitsModels = array();

		$model = new Default_Model_CatalogProducts();
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('j_catalog_products'), array('id', 'visits'))
				->where('status = ?', '1')
				->order('visits DESC');
		$result = $model->fetchAll($select);
		if(null != $result){
			foreach($result as $value){
				$topVisits[$value->getId()] = $value->getVisits()+$this->calculateStatisticsVisitsForCustomGallery($value->getId());
			}
		}
		arsort($topVisits);
		if(null != $limit){
			$i = 1;
			foreach($topVisits as $key => $value){
				if($i > $limit){
					unset($topVisits[$key]);
				}
				$i++;
			}
		}
		return $topVisits;
	}

	public static function customerModelById($id, $options = array())
	{
		$id = (int) $id;
		
		if(null != $id){
			$model = new Default_Model_AccountUsers();
			if(null == $options){
				if($model->find()){
					return $model;
				}else{
					return null;
				}
			}else{
				$select = $model->getMapper()->getDbTable()->select()
						->from(array('j_account_users'), $options)
						->where('id = ?', $id);
				$result = $model->fetchAll($select);
				if(null != $result){
					if(isset($result[0])){
						return $result[0];
					}else{
						return null;
					}
				}else{
					return null;
				}
			}

		}else{
			return null;
		}
	}

	public static function firstImage($id,$big = null)
	{
		$response = "";
		$id = (int) $id;
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
					if($big != null){
						$response = '/media/catalog/products/'.$userId.'/'.$productName.'/big/'.$imageName;
					}else
					{
						$response = '/media/catalog/products/'.$userId.'/'.$productName.'/small/'.$imageName;
					}
					
				}else{
					if($big != null){
						$response = '/media/catalog/products/0/'.$productName.'/big/'.$imageName;
					}else
					{
						$response = '/media/catalog/products/0/'.$productName.'/small/'.$imageName;
					}
					
				}
			}
		}
		return $response;
	}

	public static function getSimilarGalleries($id = null, $limit = null)
	{
		$id = (int) $id;
		$limit = (int) $limit;
		$similar = null;

		if(null != $id){
			$model = new Default_Model_CatalogProducts();
			if($model->find($id)){
				$similar = $model;
			}
		}
		
		$model = new Default_Model_CatalogProducts();
		$select = $model->getMapper()->getDbTable()->select();
		if(null != $similar){
			$select->where('category_id = ?', $similar->getCategory_id());
		}
		$select->order('RAND()');
		if(null != $limit){
			$select->limit($limit);
		}
		$result = $model->fetchAll($select);
		if(null != $result){
			return $result;
		}else{
			return null;
		}
	}
	
	public function getLastComments($limit = null){
		$limit = (int) $limit;

		$model = new Default_Model_CatalogProductComments();
		$select = $model->getMapper()->getDbTable()->select()
				->order('added DESC');
		if($limit != null){
			$select->limit($limit);
		}
		$result = $model->fetchAll($select);
		if($result != null){
			return $result;
		}else{
			return null;
		}
	}
}