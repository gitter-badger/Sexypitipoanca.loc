<?php
class TS_AdminUsers
{
	public static function fetchNumberOfPosts($userId)
	{
		$post = new Default_Model_CatalogProducts();
		$select = $post->getMapper()->getDbTable()->select()
				->from(array('j_catalog_products'), array('id'=>'COUNT(id)', 'user_id'))
				->where('user_id = ?', $userId);
		$result = $post->fetchAll($select);
		if(null != $result)
		{
			return $result[0]->getId();
		}
		else
		{
			return 0;
		}
	}
}