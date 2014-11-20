<?php
require_once APPLICATION_PUBLIC_PATH.'/library/Needs/Twitter/EpiCurl.php';
require_once APPLICATION_PUBLIC_PATH.'/library/Needs/Twitter/EpiOAuth.php';
require_once APPLICATION_PUBLIC_PATH.'/library/Needs/Twitter/EpiTwitter.php';

class TS_Twitter
{
    const MAX_MESSAGE_LENGTH = 140;
    const HASHTAG = " #sexypiti ";
	
	const CONSUMER_KEY = '4dMjVPxu7RNz3XlHrJo4mw';
	const CONSUMER_SECRET = '1U6hf9Q151CPfnYLvnmc1hEDfsVJ8TKkrVMhld8';

	public $_client;
	public $_account;

	public function  __construct(){
		$twitterObj = new EpiTwitter($this::CONSUMER_KEY, $this::CONSUMER_SECRET);
		$this->_client = $twitterObj;

		if($this->isConnected(1))
		{
			$model = new Default_Model_Twitter();
			$this->_account = $model->find(1);
			$this->_client->setToken($this->_account->getOauthToken(), $this->_account->getOauthTokenSecret());
		}
	}

	public function getAuthorizationUrl()
	{
		return $this->_client->getAuthorizationUrl();
	}

	public function connect($oauthToken)
	{
		$return = false;
		$this->_client->setToken($oauthToken);
		$token = $this->_client->getAccessToken();
		$this->_client->setToken($token->oauth_token, $token->oauth_token_secret);

		$response = $this->_client->get_accountVerify_credentials();
		if(isset($response->response['error']))
		{
			$json = NULL;
			$username = NULL;
		}
		else
		{
			$json = $response->response;
			$username = $response->response['screen_name'];
		}
		$twitterModel = new Default_Model_Twitter();
		$twitterModel->find(1);
		$twitterModel->setUserId('1')
					 ->setUsername($username)
					 ->setJson($json)
					 ->setOauthToken($token->oauth_token)
					 ->setOauthTokenSecret($token->oauth_token_secret)
					 ->setInvalid(0);
		if($twitterModel->save())
		{
			$return = true;
		}
		return $return;
	}

	public function isConnected($userId)
	{
		$return = false;
		$model = new Default_Model_Twitter();
		$select = $model->getMapper()->getDbTable()->select()
				->where('userId = ?', $userId);
		$result = $model->fetchAll($select);
		if(NULL != $result)
		{
			if(is_array($result))
			{
				if(NULL != $result[0]->getOauthToken() && NULL != $result[0]->getOauthTokenSecret())
				{
					$return = true;
				}
			}
		}
		return $return;
	}

    public function post($message, $link = NULL)
    {
        $result = false;
        if (!empty($link))
        {
            $message.= self::HASHTAG;
            $message.= $link;
        }

        // send message
        $response = $this->_client->post_statusesUpdate(array('status' => $message));
        if (!isset($response->response['error']))
        {
			$result = true;
        }

        return $result;
    }

	public function userinfo()
	{
		return $this->_account;
	}
}