<?php
class TS_Base
{
	public static function paginatorToModel($modelType,$paginator)
	{		
		$entries = array();
		$modelName = 'Default_Model_'.$modelType;
		foreach($paginator as $row) {
			$model = new $modelName();
			$model->setOptions($row);
			$entries[] = $model;
		}
		return $entries;
	}
	
	public static function tables($name)
	{
		$tablesArray = array(
			'admin'		=> 'j_accounts',
			'comments'	=> 'j_catalog_product_comments'
		);
		
		if(isset($tablesArray['name']))
		{
			return $tablesArray['name']; 
		}
		return null;
	}
	
	public static function magicQuotes($value)
	{
		if(!get_magic_quotes_gpc()){return $value;}
		if(is_array($value))
		{
			$_aux = $array;
			foreach($array as $key => $value){
				$_aux[$key] = stripslashes($value);
			}
			return $_aux;
		}
		else
		{
			return stripslashes($value);
		}
	}
}
?>