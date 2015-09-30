<?php
/**
 * Created by PhpStorm.
 * User: sergiu
 * Date: 9/23/15
 * Time: 2:04 PM
 */
class TS_Controller_Action extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $bootstrap = $this->getInvokeArg('bootstrap');
        if ($bootstrap->hasResource('db')) {
            $this->db = $bootstrap->getResource('db');
        }

        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
    }
}