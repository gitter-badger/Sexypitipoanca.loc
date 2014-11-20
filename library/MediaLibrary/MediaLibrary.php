<?php
class MediaLibrary
{
	private function init()
	{
		
	}
	
	private function activate()
	{
		
	}
	
	public function gallerySmallThumb($id, $userId, $name)
	{
		$allowed = "/[^a-z0-9\\-\\_]+/i";
		$name = preg_replace($allowed,"-", strtolower($name));

		$model = new Default_Model_CatalogProductImages();
		$select = $model->getMapper()->getDbTable()->select()
				->where('product_id = ?', $id)
				->order('position ASC')
				->limit('1');
		$result = $model->fetchAll($select);
		if(null != $result)
		{
			if($userId) {
				$smallImagePath = APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$userId.'/'.$name.'/small/'.$result[0]->getName();
				if(file_exists($smallImagePath))
				{
					$smallImage = '/media/catalog/products/'.$userId.'/'.$name.'/small/'.$result[0]->getName();
				}
				else
				{
					$smallImage = '/theme/default/images/no-pic-150.gif';
				}
			}
			else
			{
				$smallImagePath = APPLICATION_PUBLIC_PATH.'/media/catalog/products/0/'.$name.'/small/'.$result[0]->getName();
				if(file_exists($smallImagePath))
				{
					$smallImage = '/media/catalog/products/0/'.$name.'/small/'.$result[0]->getName();
				}
				else
				{
					$smallImage = '/theme/default/images/no-pic-150.gif';
				}
			}
			return $smallImage;
		}
		return false;
	}
}