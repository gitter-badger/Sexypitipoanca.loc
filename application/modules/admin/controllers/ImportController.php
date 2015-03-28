<?php

/**
 * Admin_ImportController
 * @author Sergiu Tomsa <tsergium@gmail.com>
 * @property Zend_Controller_Action_Helper_Abstract _flashMessenger
 */
class Admin_ImportController extends Zend_Controller_Action
{
    /**
     * init, initializes flash messenger
     */
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
    }

    /**
     * index, list and choose between import sources
     */
	public function indexAction()
	{

	}

    /**
     * add source, add an import source
     */
    public function addSourceAction()
    {

    }

    /**
     * edit source, edit an import source
     */
    public function editSourceAction()
    {

    }

    /**
     * delete source, delete an import source
     */
    public function deleteSourceAction()
    {

    }
}