<?php
class TS_SeoSerp
{
	const BASE_HTTP = "http://sexypitipoanca.ro";

// BEGIN: Sitemap generator
	/**
	 * function setHeader
	 * return sitemap header
	 */
	public static function setHeader()
	{
		$header = "<?xml version='1.0' encoding='utf-8'?>\n";
		$header .= "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9' xmlns:image='http://www.google.com/schemas/sitemap-image/1.1'>\n";
		return $header;
	}
	
	/**
	 * function openFile 
	 * creates sitemap file and handle
	 */	
	public static function openFile($permisions)
	{
		// 'w+' to create, 'a' to append
		$handle = fopen(APPLICATION_PUBLIC_PATH.'/sitemap.xml', $permisions);
		return $handle;
	}
	
	public static function writeToFile($handle, $content)
	{
		fwrite($handle, $content);
	}
	
	public static function itemsCount($type)
	{
		if($type == 'categories')
		{
			$model = new Default_Model_CatalogCategories();
			$select = $model->getMapper()->getDbTable()->select()
				->from(array('j_catalog_categories'), array('id'=>'COUNT(id)'))
				->where('status = ?', 1);
			$result = $model->fetchAll($select);
			if($result)
			{
				return $result[0]->getId();
			}
		}
		elseif($type == 'tags')
		{
			$model = new Default_Model_Tags();
			$select = $model->getMapper()->getDbTable()->select()
				->from(array('j_tags'), array('id'=>'COUNT(id)'));
			$result = $model->fetchAll($select);
			if($result)
			{
				return $result[0]->getId();
			}
		}
		elseif($type == 'posts')
		{
			$model = new Default_Model_CatalogProducts();
			$select = $model->getMapper()->getDbTable()->select()
				->from(array('j_catalog_products'), array('id'=>'COUNT(id)'));
			$result = $model->fetchAll($select);
			if($result)
			{
				return $result[0]->getId();
			}
		}
		
		return null;
	}
	
	public static function itemsIterator($type, $iterator)
	{
		if($type == 'writeContentCategories')
		{
			$model = new Default_Model_CatalogCategories();
			$select = $model->getMapper()->getDbTable()->select()
				->where('status = ?', 1)
				->limit(1, $iterator);
		}
		elseif($type == 'writeContentTags')
		{
			$model = new Default_Model_Tags();
			$select = $model->getMapper()->getDbTable()->select()
				->limit(1, $iterator);
		}
		elseif($type == 'writeContentPosts')
		{
			$model = new Default_Model_CatalogProducts();
			$select = $model->getMapper()->getDbTable()->select()
				->where('status = ?', 1)
				->limit(1, $iterator);
		}
		$result = $model->fetchAll($select);
		if($result)
		{
			return $result[0];
		}
		return null;
	}
	
	// BEGIN: Image class components
		public static function getCategoryNameByGalleryId($galleryId){
			$id = (int) $galleryId;
			$modelProducts = new Default_Model_CatalogProducts();
			$select = $modelProducts->getMapper()->getDbTable()->select()
				->from(array('p'=>'j_catalog_products'), array('p.category_id', 'p.id'))
				->join(array('c'=>'j_catalog_categories'), ('p.category_id = c.id'), array('name'))
				->where('p.id = ?', $galleryId)
				->setIntegrityCheck(false);
			$result = $modelProducts->fetchAll($select);
			if($result){
				return $result[0]->getName();
			}else{
				return null;
			}
		}
		
		/* fetchImages
		 * $galleryId
		 * fetches all images for a gallery
		 */
		public static function fetchGalleryImages($galleryId)
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
		public static function bigImageHrefLocation($galleryImageName, $galleryName, $userId)
		{
			$allowed = "/[^a-z0-9\\-\\_]+/i";
			$name = preg_replace($allowed,"-", strtolower($galleryName));
	
			$result = '/media/catalog/products/'.$userId.'/'.$name.'/big/'.$galleryImageName;
			return $result;
		}
		public static function getAuthorIdByGalleryId($galleryId){
			$id = (int) $galleryId;
			$model = new Default_Model_CatalogProducts();
			if($model->find($id)){
				return $model->getUser_id();
			}else{
				return 0;// admin id
			}
		}
	// END: Image class components
	
	/**
	 * function closeFile
	 * closes sitemap file
	 * @param unknown_type $handle
	 */
	public static function closeFile($handle)
	{
		fclose($handle);
		return false;
	}
	
	/**
	 * function setFooter
	 * return sitemap footer
	 */
	public static function setFooter()
	{
		return "</urlset>";
	}
	
	public static function staticPages()
	{
		$now = date("Y-m-d", time());
		
		$sitemapContent = self::set(self::BASE_HTTP."/", $now, 'daily', 1);
		$sitemapContent .= self::set(self::BASE_HTTP."/account/new", $now, 'daily', 0.9);
		$sitemapContent .= self::set(self::BASE_HTTP."/account/terms", $now, 'daily', 0.9);
		$sitemapContent .= self::set(self::BASE_HTTP."/contact", $now, 'daily', 0.9);
		return $sitemapContent;
	}
	
	/**
	 * function set
	 * function for setting a page in sitemap
	 * @param uri $loc
	 * @param date("Y-m-d") $lastmod
	 * @param daily/monthly/... $changefreq
	 * @param 0.1->1 $priority
	 */
	public static function set($loc, $lastmod = null, $changefreq = null, $priority = null, $terminate = true){
		$response = "<url>\n";
		$response .= "\t<loc>".$loc."</loc>\n";
		if(null != $lastmod)
			$response .= "\t<lastmod>".$lastmod."</lastmod>\n";
		if(null != $changefreq)
			$response .= "\t<changefreq>".$changefreq."</changefreq>\n";
		if(null != $priority)
			$response .= "\t<priority>".$priority."</priority>\n";
		if($terminate)
			$response .= "</url>\n";
		return $response;
	}
	
	/**
	 * function setImage
	 * function for setting an image in sitemap
	 * @param uri $loc
	 * @param string $title
	 */
	public static function setImage($loc, $title = null)
	{
		$response = "<image:image>\n<image:loc>".$loc."</image:loc>\n";
		if(null != $title){
			$response .= "<image:title> ".$title." </image:title>\n";
		}
		$response .= "</image:image>\n";
		return $response;
	}
// END: Sitemap generator
}