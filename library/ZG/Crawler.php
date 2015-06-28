<?php

class ZG_Crawler
{
	private $_url;
	private $_html;
	
	public function setUrl($url)
	{
		$this->_url = $url;
		return $this;
	}
	
	public function fetch()
	{
		require_once 'simple_html_dom.php';
		$this->_html = file_get_html($this->_url);
		return $this->_html;
	}
}
