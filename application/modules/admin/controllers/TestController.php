<?php
class Admin_TestController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
    }

	public function indexAction()
	{
		$twitter = new TS_Twitter();
//		$twitter->post("Infusion - Legacy", "http://rexy.loc/catalog/product-details/id/394/#comment");
		$this->view->account = $twitter->_account;
		$this->view->authURL = $twitter->getAuthorizationUrl();
		$this->view->isConnected = $twitter->isConnected(1);
		
		$oauthToken = $this->getRequest()->getParam('oauth_token');
		if($oauthToken)
		{
			if($twitter->connect($oauthToken))
			{
				$this->_flashMessenger->addMessage('<div class="mess-info">Ai fost conectat la Twitter!</div>');
			}
			else
			{
				$this->_flashMessenger->addMessage('<div class="mess-false">Eroare conectare la Twitter!</div>');
			}
			$this->_redirect('/admin/test');
		}
	}

	public function disconnectAction()
	{
		$userId = $this->getRequest()->getParam('userId');
		$model = new Default_Model_Twitter();
		$select = $model->getMapper()->getDbTable()->select()
				->where('userId = ?', $userId);
		$result = $model->fetchAll($select);
		if($result)
		{
			if($model->find($result[0]->getUserId()))
			{
				$model->setInvalid(1);
				$model->setOauthToken();
				$model->setOauthTokenSecret();
				if($model->save())
				{
					$this->_flashMessenger->addMessage('<div class="mess-true">You were disconnected from Twitter!</div>');
				}
				else
				{
					$this->_flashMessenger->addMessage('<div class="mess-false">Error disconnecting from Twitter!</div>');
				}
				$this->_redirect('/admin/test');
			}
		}
	}
	
	public function hydraAction()
	{
		$Hydra = new TS_Hydra;
		$url = "http://9gag.com";
		$page = $Hydra->fetchPage($url);
		$images = $Hydra->fetchImageList($page);
		foreach($images as $key => $value)
		{
			echo "<img height='150' src=".$value." />";
		}
		die();
	}
	
	public function weatherAction()
	{
		/*
		 * @description    Gets information using Google's Weather API and returns an array with the weather information
		 * @param          String containing the location.
		 * @return         Array
		 */
		function get_weather_from_city( $location ) {
		    // load the XML feeds for the Google Weather API
		    $xml = simplexml_load_file('http://www.google.com/ig/api?weather='.urlencode( $location ));
		    $current = $xml->xpath("/xml_api_reply/weather/current_conditions");
		    $forecast = $xml->xpath("/xml_api_reply/weather/forecast_conditions");
		 
		    $weather = array();
		 
		    //Get latitude and longitude by city and sunrise
		    $now = time();
		 
		    $location = str_replace(" ", "+", $location);
		    $region = 'romania';
		    $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$location&sensor=false&region=$region");
		    $json = json_decode($json);
		 
		    $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
		    $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
		 
		    $sunrise = date_sunrise($now, SUNFUNCS_RET_TIMESTAMP, $lat, $long, 90.583333, 0);
		    $sunset = date_sunset($now, SUNFUNCS_RET_TIMESTAMP, $lat, $long, 90.583333, 0);
		 
		    if (($now > $sunrise) && ($now < $sunset)){ //day
		            $weather['time'] = 'day';
		    }else{ //night
		        $weather['time'] = 'night';
		    };
		 
		    // do a basic error check to see if we can get the current weather condition for the given location
		    // if no return an error.
		    if(!$current[0]->condition['data']){
		        $error = "Couldn't determine this location";
		        $weather['error'] = $error;
		    }
		    $weather['current_temperature'] = $current[0]->temp_c['data'];
		    $weather['weather'] = strtolower($current[0]->condition['data']);
		    $weather['json'] = $current;
		    $weather['current'] = $current;
		    $weather['forecast'] = $forecast;
		    return $weather;
		}
		
		function format_weather($weather){
			if(!isset($weather['error']))
			{
				//echo "<pre>".print_r($weather, 1)."</pre>";
				$icon = $weather['current'][0]->icon['data'];
				echo "
				<div style='width: 200px;border: 1px solid #dfdfdf;'>
					Today
					<img src='http://www.google.com{$icon}' />
					{$weather['current_temperature']} Celsius<br />
					{$weather['weather']}
				</div>
				";
			}
		}
	}
}



