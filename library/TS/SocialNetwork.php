<?php
class TS_SocialNetwork
{
    /**
     * user activity, get all articles by this user
     * @param $userId
     * @param $limit
     * @return null
     * ToDo: remove this method
     */
	public static function userActivity($userId, $limit)
	{
		$return = null;
		$catalogModel = new Default_Model_CatalogProducts();
		$select = $catalogModel->getMapper()->getDbTable()->select()
				->where('user_id = ?', $userId)

				->order('added DESC')
				->limit($limit);
		
		$result = $catalogModel->fetchAll($select);
		if(NULL != $result)
		{
			$return = $result;
		}
		return $return;
	}
	
	public static function userDataById($userId)
	{
		$return = NULL;
		$userModel = new Default_Model_AccountUsers();
		if($userModel->find($userId))
		{
			$return = $userModel;
		}
		return $return;
	}
	
	public static function userMenuGalleryCount($userId)
	{
		$return = null;
		$catalogModel = new Default_Model_CatalogProducts();
		$select = $catalogModel->getMapper()->getDbTable()->select()
				->from(array('j_catalog_products'), array('id' => 'COUNT(id)'))
				->where('type = ?', 'gallery')
				->where('user_id = ?', $userId);
		
		$result = $catalogModel->fetchAll($select);
		if(NULL != $result)
		{
			$return = $result[0]->getId();
		}
		return $return;	
	}
	
	public static function userMenuEmbedCount($userId)
	{
		$return = null;
		$catalogModel = new Default_Model_CatalogProducts();
		$select = $catalogModel->getMapper()->getDbTable()->select()
				->from(array('j_catalog_products'), array('id' => 'COUNT(id)'))
				->where("(`type` = 'video' OR `type` = 'embed')")
				->where('user_id = ?', $userId);
		
		$result = $catalogModel->fetchAll($select);
		if(NULL != $result)
		{
			$return = $result[0]->getId();
		}
		return $return;			
	}
	
	public static function userMenuFavoritesCount($userId)
	{
		$return = null;
		$catalogModel = new Default_Model_CatalogProductFavorites();
		$select = $catalogModel->getMapper()->getDbTable()->select()
				->from(array('j_catalog_product_favorites'), array('id' => 'COUNT(id)'))
				->where('type = ?', 'favorite')
				->where('userId = ?', $userId);
		
		$result = $catalogModel->fetchAll($select);
		if(NULL != $result)
		{
			$return = $result[0]->getId();
		}
		return $return;			
	}
	
	public static function userFriendsCount($userId)
	{
		$return = null;
		$model = new Default_Model_SocialUserConnections();
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('social_user_connections'), array('id' => 'COUNT(id)'))
				->where("(initiatorUserId = {$userId} OR receiverUserId = {$userId})")
				->where('isConfirmed = ?', 1);
		$result = $model->fetchAll($select);
		if(null != $result)
		{
			$return = $result[0]->getId();
		}
		return $return;
	}
	
	public static function categoryNameByCategoryId($categoryId)
	{
		$return = null;
		$model = new Default_Model_CatalogCategories();
		if($model->find($categoryId))
		{
			$return = $model->getName();
		}
		echo $return;
	}
	
	public static function countNumberOfPhotos($galleryId)
	{
		$return = NULL;
		$model = new Default_Model_CatalogProductImages();
		$select = $model->getMapper()->getdbTable()->select()
				->from(array('j_catalog_product_images'), array('id' => 'COUNT(id)'))
				->where('product_id = ?', $galleryId);
		$result = $model->fetchAll($select);
		if(NULL != $result)
		{
			$return = $result[0]->getId();
		}
		return $return;
	}

    // ToDo: Remove method
	public static function usernameToUserModel($username)
	{
		$return = NULL;
		if($username)
		{
			$model = new Default_Model_AccountUsers();
			$select = $model->getMapper()->getDbTable()->select()
					->where('username = ?', $username);
			$result = $model->fetchAll($select);
			if(NULL != $result)
			{
				$return = $result[0];
			}
		}
		return $return;
	}
	
	public static function userExists($id)
	{
		$return = false;
		$model = new Default_Model_AccountUsers();
		if($model->find($id))
		{
			$return = $model;
		}
		return $return;
	}
	
	public static function checkIfFriends($userId, $friendId)
	{
		$return = null;
		$model = new Default_Model_SocialUserConnections();
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('social_user_connections'), array('id' => 'COUNT(id)'))
				->where("
				(initiatorUserId = {$userId} AND receiverUserId = {$friendId}) OR 
				(initiatorUserId = {$friendId} AND receiverUserId = {$userId})
				");
				//->where('isConfirmed = ?', 1); // unable to send second request until first is deleted
		$result = $model->fetchAll($select);
		if(null != $result)
		{
			$return = $result[0]->getId();
		}
		return $return;
	}

    // ToDo: remove this
	public static function userAddedGalleries($userId)
	{
		$return = null;
		$model = new Default_Model_CatalogProducts();
		$select = $model->getMapper()->getDbTable()->select()
				->where('user_id = ?', $userId)
				->where('type = ?', 'gallery')
				->order('added DESC');
		$result = $model->fetchAll($select);
		if(null != $result)
		{
			$return = $result;
		}
		return $result;
	}
	
	public static function userAddedVideo($userId)
	{
		$return = null;
		$model = new Default_Model_CatalogProducts();
		$select = $model->getMapper()->getDbTable()->select()
				->where('user_id = ?', $userId)
				->where("type = 'video' OR type = 'embed'")
				->order('added DESC');
		$result = $model->fetchAll($select);
		if(null != $result)
		{
			$return = $result;
		}
		return $result;
	}
}