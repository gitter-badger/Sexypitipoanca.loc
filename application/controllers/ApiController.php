<?php
class ApiController extends TS_Controller_Action
{
    public function indexAction()
    {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);

        $this->printJson([
                'data' => $data,
                'version' => '0.1'
            ],
            200
        );
    }

    public function loginAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');

        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);

        $dbAdapter = new Zend_Auth_Adapter_DbTable($this->db, 'j_account_users', 'username', 'password', 'MD5(?) AND status = "1"');
        $dbAdapter->setIdentity($data['username'])
            ->setCredential($data['password']);

        $auth = Zend_Auth::getInstance($data['password']);
        $result = $auth->authenticate($dbAdapter);
        if(!$result->isValid())
        {
            switch($result->getCode())
            {
                case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                    ;
                    break;
                default:
                    ;
                    break;
            }
            $response = [
                'code'          => 401,
                'message'       => 'There was an error',
                'description'   => 'So...I don\'t know what happened but...it failed'
            ];
            $this->printJson($response, 401);
        }
        else
        {
            $account = $dbAdapter->getResultRowObject();
            $model = new Default_Model_AccountUsers();
            $model->find($account->id);
            $model->saveLastLogin();
            $storage = $auth->getStorage();
            $storage->write($model);
            $this->getResponse()->setHttpResponseCode(200);
        }
    }

    public function favoritesAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');

        $auth = Zend_Auth::getInstance();
        $authAccount = $auth->getStorage()->read();
        if (null != $authAccount) {
            $model = new Default_Model_CatalogProducts();
            $select = $model->getMapper()->getDbTable()->select()
                ->from(['p' => 'j_catalog_products'])
                ->joinInner(['f' => 'j_catalog_product_favorites'], 'p.id = f.productId')
                ->where('f.userId = ?', $authAccount->getId())
                ->where('f.type = ?', 'favorite')
                ->order('f.created DESC')
                ->setIntegrityCheck(false);
            $posts = $model->fetchAll($select);
            if ($posts) {
                $this->printJson($this->parsePostData($posts), 200);
            } else {
                $this->getResponse()->setHttpResponseCode(204);
            }
        } else {
            $response = [
                'code'          => 401,
                'message'       => 'There was an error',
                'description'   => 'So...I don\'t know what happened but...it failed'
            ];
            $this->printJson($response, 401);
        }
    }

    /**
     * fetch all categories
     * @throws Zend_Controller_Response_Exception
     */
    public function categoryAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');

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

    /**
     * fetch all posts or paginate
     * @throws Zend_Controller_Response_Exception
     */
    public function postAction()
    {
        $start = $this->getRequest()->getParam('start');
        $count = $this->getRequest()->getParam('count');

        $model = new Default_Model_CatalogProducts();
        $select = $model->getMapper()->getDbTable()->select();
        $select->where("(`type` = 'gallery' OR `type` = 'embed' OR `type` = 'video')");
        $select->where('status = ?', '1');
        $select->order('added DESC');
        if ($start || $count) {
            $select->limit($start, $count);
        }
        $posts = $model->fetchAll($select);

        if ($posts) {
            $this->printJson(
                $this->parsePostData($posts),
                200
            );
        } else {
            $this->getResponse()->setHttpResponseCode(204);
        }
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $response = [
                'code'          => 401,
                'message'       => 'There was an error',
                'description'   => 'So...I don\'t know what happened but...it failed'
            ];
            $this->printJson($response, 401);
        } else {
            $auth->clearIdentity();
            $this->printJson('', 200);
        }
    }

    public function successAction()
    {
        $response = [
            'name'          => 'New resource created',
            'description'   => 'There is no description yet.'
        ];
        $this->printJson($response, 200);
    }

    public function errorAction()
    {
        $response = [
            'code'          => 400,
            'message'       => 'There was an error',
            'description'   => 'So...I don\'t know what happened but...it failed'
        ];
        $this->printJson($response, 400);
    }


    public function testPostAction()
    {
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);

        if ($data['result'] === true) {
            $this->printJson($data, 200);
        } else {
            $response = [
                'code'          => 400,
                'message'       => 'There was an error',
                'description'   => 'So...I don\'t know what happened but...it failed'
            ];
            $this->printJson($response, 400);
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