<?php
class RedirectController extends Base_Controller_Action
{
	public function newTagsAction()
	{
		$tag = $this->getRequest()->getParam('tag');
		
		$view = new Zend_View();
		$link = $view->url(array('tag' => $tag), 'tag');
		$this->_redirect($link, array('code'=>301));
	}
}