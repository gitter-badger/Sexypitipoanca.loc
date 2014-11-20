<?php
class TS_Cache extends TS_Base
{
	protected static $settings_array  = array(
		"CACHE_INDEX",
		"CACHE_INDEX_EXP",
		"CACHE_MENU_BOTOM",
		"CACHE_MENU_TOP",
		"CACHE_TAGCLOUD",
		"CACHE_FOOTER"
	);
	
	private static $instance; 
	protected $_settings;
	
	protected function __construct()
	{
		$this->settings();
	}
	
	public static function getInstance(){
		if(!self::$instance)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	protected function settings()
	{
		$settingsModel = new Default_Model_Options();
		$select = $settingsModel->getMapper()->getDbTable()->select()
				->from(array('options'), array('value'))
				->where('name IN (?)', self::$settings_array);
		$settings = $settingsModel->fetchAll($select);
		if(NULL != $settings)
		{
			foreach($settings as $value)
			{
				$this->_settings[$value->name] = $value;
			}
		}
		else
		{
			throw new Exception('Cache settings not found!');
		}
		return true;
	}
	
	public function clear($component)
	{
		$frontendOptions = array('lifetime' => 86400, 'automatic_serialization' => TRUE, 'caching' => TRUE);
		$backendOptions = array('cache_dir' => './data/cache/');
		$cache = Zend_Cache::factory('Output', 'File', $frontendOptions, $backendOptions);
		if($cache->remove($component))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
}
?>