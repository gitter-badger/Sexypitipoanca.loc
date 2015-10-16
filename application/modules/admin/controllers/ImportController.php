<?php

/**
 * Admin_ImportController
 * @author Sergiu Tomsa <tsergium@gmail.com>
 * @property Zend_Controller_Action_Helper_Abstract _flashMessenger
 */
class Admin_ImportController extends Zend_Controller_Action
{
    /**
     * @var Admin_Model_ImportSource $model
     */
    protected $model;

    /**
     * init, initializes flash messenger
     */
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();

        $this->model = new Admin_Model_ImportSource();
    }

    /**
     * index, list and choose between import sources
     */
	public function indexAction()
	{
        $select = $this->model->getMapper()->getDbTable()->select()
            ->order('created DESC');
        $sources = $this->model->fetchAll($select);
        $this->view->sources = $sources;
	}

    /**
     * add source, add an import source
     */
    public function addSourceAction()
    {
        $form = new Admin_Form_ImportSource();
        $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/import/addSource.phtml'))));

        if  ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->model->setOptions($form->getValues());
                if ($this->model->save()) {
                    $this->_flashMessenger->addMessage('<div class="mess-true">Success! Data created.</div>');
                    $this->_redirect('/admin/import');
                } else {
                    $this->_flashMessenger->addMessage('<div class="mess-false">Error! Could not save.</div>');
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * edit source, edit an import source
     */
    public function editSourceAction()
    {
        $form = new Admin_Form_ImportSource();
        if ($this->model->find($this->getRequest()->getParam('id'))) {
            $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/import/addSource.phtml'))));
            $form->edit($this->model);
            $this->view->form = $form;
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->model->setOptions($form->getValues());
                if ($this->model->save()) {
                    $this->_flashMessenger->addMessage('<div class="mess-true">Success! Data modified.</div>');
                    $this->_redirect('/admin/import');
                } else {
                    $this->_flashMessenger->addMessage('<div class="mess-false">Error! Could not modify.</div>');
                }
            }
        }
    }

    /**
     * delete source, delete an import source
     */
    public function deleteSourceAction()
    {
        if ($this->model->find($this->getRequest()->getParam('id'))) {
            if ($this->model->delete()) {
                $this->_flashMessenger->addMessage('<div class="mess-true">Success! Data modified.</div>');
            } else {
                $this->_flashMessenger->addMessage('<div class="mess-false">Error! Could not modify.</div>');
            }
        }
        $this->_redirect('/admin/import');
    }

    public function importItemAction()
    {
        $id = $this->getRequest()->getParam('id');
        $source = new Admin_Model_ImportSource();

        if ($source->find($id)) {
            $item = new Admin_Model_ImportItem();
            $select = $item->getMapper()->getDbTable()->select()
                ->where('sourceId = ?', $id);
            $items = $item->fetchAll($select);
            $this->view->items = $items;
        }
    }

    public function importElementAction()
    {
        $id = $this->getRequest()->getParam('id');
        $item = new Admin_Model_ImportItem();

        if ($item->find($id)) {
            $element = new Admin_Model_ImportElement();
            $select = $element->getMapper()->getDbTable()->select()
                ->where('itemId = ?', $id);
            $elements = $element->fetchAll($select);
            $this->view->elements = $elements;
        }
    }

    public function importAction()
    {

    }

    public function importMovieAction()
    {

    }
}