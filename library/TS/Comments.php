<?php
class TS_Comments
{
	public function getCommentById($id)
	{
		
	}

	public static function getCommentsNumberById($galleryId)
	{
		$return = '0';
		$model = new Default_Model_CatalogProductComments();
		$select = $model->getMapper()->getDbTable()->select()
				->from($model->getMapper()->getDbTable(), array('id'=>'COUNT(id)'))
				->where('product_id = ?', $galleryId);
		if(($result = $model->fetchAll($select))) {
			$return = $result[0]->getId();
		}
		return $return;
	}

	public static function getAllCommentsById($galleryId)
	{
		$model = new Default_Model_CatalogProductComments();
		$select = $model->getMapper()->getDbTable()->select()
				->where('parentId IS NULL')
				->where('product_id = ?', $galleryId)
				->order('added DESC');
		if(($result = $model->fetchAll($select))) {
			return $result;
		}
	}

	public function getAvatarByUserId($userId)
	{
		$user = new Default_Model_AccountUsers();
		if($user->find($userId))
		{
			$avatar = $user->getAvatar();
			unset($user);
			if($avatar != NULL)
			{
				return $avatar;
			}
			else
			{
				return false;
			}
		}else{
			return false;
		}
	}

	public static function getChildComments($commentId)
	{
		$return = false;
		$model = new Default_Model_CatalogProductComments();
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('j_catalog_product_comments'), array('id','userId','name','comment','added'))
				->where('parentId = ?', $commentId)
				->order('added ASC');
		$result = $model->fetchAll($select);
		if($result)
		{
			$return = $result;
		}
		return $return;
	}
}