<?php
class RedirectController extends Zend_Controller_Action
{
	public function init()
    {
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->message = $this->_flashMessenger->getMessages();
    }

	public function newTagsAction()
	{
		$tag = $this->getRequest()->getParam('tag');
		
		$view = new Zend_View();
		$link = $view->url(array('tag' => $tag), 'tag');
		$this->_redirect($link, array('code'=>301));
	}

	public function newClipsPageAction()
	{
		$tag = $this->getRequest()->getParam('username');

		$view = new Zend_View();
		$link = $view->url(array('tag' => $tag), 'tag');
		$this->_redirect($link, array('code'=>301));
	}
}