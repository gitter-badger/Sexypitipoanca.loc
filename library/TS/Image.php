<?php
class TS_Image
{
	/* fetchImages
	 * $galleryId
	 * fetches all images for a gallery
	 */
	public function fetchGalleryImages($galleryId)
	{
		$id = (int) $galleryId;
		$model = new Default_Model_CatalogProductImages();
		$select = $model->getMapper()->getDbTable()->select()
				->where('product_id = ?', $galleryId)
				->order('position DESC');
		$result = $model->fetchAll($select);
		if(null != $result){
			return $result;
		}else{
			return null;
		}
	}

	/* smallImageHrefLocation
	 * $userId - id for user that created the gallery
	 * $galleryName - name of the gallery
	 * $galleryImageName - name of the current image from gallery
	 * returns the href for the current small image from gallery
	 */
	public function smallImageHrefLocation($galleryImageName, $galleryName, $userId)
	{
		$allowed = "/[^a-z0-9\\-\\_]+/i";
		$name = preg_replace($allowed,"-", strtolower($galleryName));
		
		$result = '/media/catalog/products/'.$userId.'/'.$name.'/small/'.$galleryImageName;
		return $result;
	}
	
	public function bigImageHrefLocation($galleryImageName, $galleryName, $userId)
	{
		$allowed = "/[^a-z0-9\\-\\_]+/i";
		$name = preg_replace($allowed,"-", strtolower($galleryName));

		$result = '/media/catalog/products/'.$userId.'/'.$name.'/big/'.$galleryImageName;
		return $result;
	}

	public function getAuthorIdByGalleryId($galleryId){
		$id = (int) $galleryId;
		$model = new Default_Model_CatalogProducts();
		if($model->find($id)){
			return $model->getUser_id();
		}else{
			return 0;// admin id
		}
	}

	public function getCategoryNameByGalleryId($galleryId){
		$id = (int) $galleryId;
		$model = new Default_Model_CatalogCategories();
		if($model->find($id)){
			return $model->getName();
		}else{
			return null;
		}
	}
}