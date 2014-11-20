<?php
class TS_Products
{
	public function calcTopGallRatting($galleryId)
	{
		// returneaza array($nrDeVoturi=>$medieVoturi) sau NULL
		$response = array();
		if(NULL == $galleryId) return NULL;
		$model = new Default_Model_StatisticsRatings();
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('j_statistics_ratings'), array('productId', 'rating'=>'SUM(rating)/COUNT(id)', 'statisticsId'=>'COUNT(id)'))
				->where('productId = ?', $galleryId);
		$result = $model->fetchAll($select);

		if(NULL != $result)
		{
			$response['numarVoturi'] = $result[0]->getStatisticsId();
			$response['medieVoturi'] = $result[0]->getRating();
		}
		return $response;
	}

	public function calcTotGallVisits($galleryId)
	{
		// returneaza (int) $nrVisits sau NULL
		if(NULL == $galleryId) return NULL;
		$model = new Default_Model_StatisticsVisits();
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('j_statistics_visits'), array('productId', 'statisticsId'=>'COUNT(id)'))
				->where('productId = ?', $galleryId);
		$result = $model->fetchAll($select);

		if(null != $result) return $result[0]->getStatisticsId();
		else return NULL;
	}

	public function calcTotGallComments($galleryId)
	{
		// returneaza (int) $nrComments sau NULL
		if(NULL == $galleryId) return NULL;
		$model = new Default_Model_CatalogProductComments();
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('j_catalog_product_comments'), array('product_id', 'userId'=>'COUNT(id)'))
				->where('product_id = ?', $galleryId);
		$result = $model->fetchAll($select);

		if(null != $result) return $result[0]->getUserId();
		else return NULL;
	}

	public function getTopGalleries($ranking, $type, $limit = null)
	{
		$topGallery = array();

		$model = new Default_Model_CatalogProducts();
		$select = $model->getMapper()->getDbTable()->select();

		if($ranking == 'rating')
			$select->from(array('j_catalog_products'), array('id', 'rating'));
		elseif($ranking == 'visits')
			$select->from(array('j_catalog_products'), array('id', 'visits'));
		elseif($ranking == 'comments')
			$select->from(array('j_catalog_products'), array('id', 'visits'));
		else return NULL;

		if($type == 'video')
			$select->where("(`type` = 'video' OR `type` = 'embed')");
		else $select->where('type = ?', 'gallery');

		$select->where('status = ?', '1');

		$result = $model->fetchAll($select);
		if(null != $result){
			foreach($result as $value){
				if($ranking == 'rating'){
					$rating = $value->getRating();
					$ratingNr = $value->getRatingNumber();
					
					$calcRating = $this->calcTopGallRatting($value->getId());

					if(!empty($rating) && !empty($ratingNr) && !empty($calcRating['numarVoturi']) && !empty($calcRating['medieVoturi'])){
						$ratingTotal = ((($rating*$ratingNr)+($calcRating['numarVoturi']*$calcRating['medieVoturi']))/($ratingNr+$calcRating['numarVoturi']));
					}elseif(NULL == $rating || NULL == $ratingNr){
						$ratingTotal = $calcRating['medieVoturi'];
					}else{
						$ratingTotal = 0;
					}

					$topGallery[$value->getId()] = $ratingTotal;
				}elseif($ranking == 'visits')
					$topGallery[$value->getId()] = $value->getVisits();
				elseif($ranking == 'comments')
					$topGallery[$value->getId()] = $this->calcTotGallComments($value->getId());
				else return NULL;
			}
		}
		arsort($topGallery);
		if(NULL != $limit){
			$i = 1;
			foreach($topGallery as $key => $value){
				if($i > $limit){
					unset($topGallery[$key]);
				}
				$i++;
			}
			$i = 1;
		}
		return $topGallery;
	}

	public static function formatName($string)
	{
		$regex = "/[^a-z0-9\\-\\_]+/i";  
		$string = preg_replace($regex," ", $string);
		$string = trim($string);
		return $string;
	}
	
	public static function getVideo($productId)
	{
		$model = new Default_Model_Video;
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('video'), array('productId', 'embed', 'image'))
				->where('productId = ?', $productId);
		$result = $model->fetchAll($select);
		if(NULL != $result)
		{
			return $result[0];
		}
	}
	
	public static function favoriteProducts($userId)
	{
		$return = null;
		
		$model = new Default_Model_CatalogProductFavorites();
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('j_catalog_product_favorites'), array('productId', 'userId', 'created'))
				->where('userId = ?', $userId)
				->where('type = ?', 'favorite')
				->order('created DESC');
		$result = $model->fetchAll($select);
		if(NULL != $result)
		{
			foreach($result as $value)
			{
				$favorites[] = $value->getProductId();
			}
			
			if($favorites)
			{
				$model = new Default_Model_CatalogProducts();
				$select = $model->getMapper()->getDbTable()->select()
						->where('id IN (?)', $favorites);
				$result = $model->fetchAll($select);
				if(NULL != $result)
				{
					$return = $result;
				}
			}
		}
		return $return;
	}

	public static function topRated($limit = null)
	{
		$model = new Default_Model_StatisticsRatings();
		$select = $model->getMapper()->getDbTable()->select()
				->from(array($model->getMapper()->getDbTable()->getName()), array('id'=>'COUNT(id)', 'productId'=>'productId', 'rating'=>'SUM(rating)'))
				->group('productId')
				->order('rating DESC')
				->order('id DESC');
		$result = $model->fetchAll($select);
		if(null != $result)
		{
			
		}
	}
}