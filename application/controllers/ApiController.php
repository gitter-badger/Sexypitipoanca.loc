<?php
class ApiController extends Zend_Controller_Action
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

        $this->getResponse()->setHeader('Content-Type', 'application/json');
    }

    public function indexAction()
    {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);

        echo Zend_Json_Encoder::encode([
            'data' => $data,
            'version' => '0.1'
        ]);
    }

    public function categoryAction()
    {
        $model = new Default_Model_CatalogCategories();
        $select = $model->getMapper()->getDbTable()->select()
            ->where('status = ?', 1);
        $categories = $model->fetchAll($select);

        if ($categories) {
            echo Zend_Json_Encoder::encode(
                $this->parseCategoryData($categories)
            );
            $this->getResponse()->setHttpResponseCode(200);
        } else {
            $this->getResponse()->setHttpResponseCode(204);
        }
    }

    public function postAction()
    {
        $model = new Default_Model_CatalogProducts();
        $select = $model->getMapper()->getDbTable()->select()
            ->where("(`type` = 'gallery' OR `type` = 'embed' OR `type` = 'video')")
            ->where('status = ?', '1')
            ->order('added DESC');
        $posts = $model->fetchAll($select);

        if ($posts) {
            echo Zend_Json_Encoder::encode(
                $this->parsePostData($posts)
            );
            $this->getResponse()->setHttpResponseCode(200);
        } else {
            $this->getResponse()->setHttpResponseCode(204);
        }
    }

    /**
     * prepare the post data as api response
     * @param $posts
     * @return array
     */
    protected function parsePostData($posts)
    {
        $result = [];
        foreach ($posts as $post) {
            $result[] = [
                'id' => $post->getId(),
                'parentId' => $post->getParent_id(),
                'userId' => $post->getUser_id(),
                'categoryId' => $post->getCategory_id(),
                'type' => $post->getType(),
                'position' => $post->getPosition(),
                'name' => $post->getName(),
                'description' => $post->getDescription(),
                'visits' => $post->getVisits(),
                'rating' => $post->getRating(),
                'ratingNumber' => $post->getRatingNumber(),
                'status' => $post->getStatus(),
                'added' => $post->getAdded() ? date('d-m-Y H:i:s', $post->getAdded()) : null
            ];
        }

        return $result;
    }

    /**
     * prepare the category data as api response
     * @param $categories
     * @return array
     */
    protected function parseCategoryData($categories)
    {
        $result = [];
        foreach ($categories as $category) {
            $result[] = [
                'id' => $category->getId(),
                'parentId' => $category->getParent_id(),
                'position' => $category->getPosition(),
                'name' => $category->getName(),
                'status' => $category->getStatus() ? 'active' : 'inactive',
                'added' => $category->getAdded() ? date('d-m-Y H:i:s', $category->getAdded()) : null
            ];
        }
        return $result;
    }
}