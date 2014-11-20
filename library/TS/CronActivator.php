<?php
class TS_CronActivator
{
	protected $_items;

	public function __construct() {
		$exception = array('0'=>'5'); // array with exceptions of categories
		$limit = 1; // number of galleries per category to activate
		
		$this->findItems($limit, NULL); // find items
		echo $this->activateItems($this->_items); // activate items
	}
	
	protected function findItems($limit, $exception)
	{
		$categories = array();
		$model = new Default_Model_CatalogCategories();
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('j_catalog_categories'), array('id', 'status'))
				->where('status = ?', '1');
		if($exception)
		{
			$select->where('id NOT IN (?)', $exception);
		}
		
		$result = $model->fetchAll($select);
		if(null != $result){
			foreach($result as $value){
				$categories[] = $value->getId();
				unset($value);
			}
		}
		unset($result);
		
		foreach($categories as $category)
		{
			$model = new Default_Model_CatalogProducts();
			$select = $model->getMapper()->getDbTable()->select()
					->from(array('j_catalog_products'), array('id', 'status'))
					->where('category_id = ?', $category)
					->where('status = ?', '0')
					->limit($limit)
					->order('added ASC');
			$result = $model->fetchAll($select);
			
			if(NULL != $result)
			{
				foreach($result as $value)
				{
					$this->_items[] = $value->getId();
					unset($value);
				}
			}
		}

		unset($model);
		unset($select);
		unset($result);
	}
	
	protected function activateItems($items)
	{
		$nrOfItems = 0;
		
		if(is_array($items))
		{
			foreach($items as $value)
			{
				$model = new Default_Model_CatalogProducts();
				if($model->find($value))
				{
					if($model->activate()){
						$nrOfItems++;
						$this->socialTweet($model);
					}
				}
			}
		}
		echo $nrOfItems;
	}

	protected function socialTweet(Default_Model_CatalogProducts $model)
	{
		$product = new J_Product();
		$product->Product($model->getId(), array('category', 'image', 'commentsNo'));
		$zendView = new Zend_View;
		$link = $zendView->url(array('categoryName' => preg_replace('/\s+/','-', strtolower($product->getCategory())), 'productName' => preg_replace('/\s+/','-', strtolower($product->getName())), 'id' => $product->getId()), 'product');

		$domain = "http://sexypitipoanca.ro";
		$twitter = new TS_Twitter();
		$twitter->post($model->getName()."   ", $domain.$link);
	}
}
?>