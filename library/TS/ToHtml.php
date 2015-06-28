<?php
class TS_ToHtml
{
	private static function datetimeTranslate($type, $number)
	{
		$monthArray = array(
			'1'		=> 'Ianuarie',
			'2'		=> 'Februarie',
			'3'		=> 'Martie',
			'4'		=> 'Aprilie',
			'5'		=> 'Mai',
			'6'		=> 'Iunie',
			'7'		=> 'Iulie',
			'8'		=> 'August',
			'9'		=> 'Septembrie',
			'10'	=> 'Octombrie',
			'11'	=> 'Noiembrie',
			'12'	=> 'Decembrie'
		);
		if($type == 'month')
		{
			return $monthArray[$number];
		}
	}
	
	public static function userFriends(Default_Model_SocialUserConnections $request, $userId)
	{
		$html = "";
		$userModel = new Default_Model_AccountUsers();
		if($request->getInitiatorUserId() == $userId) $friendId = $request->getReceiverUserId();
		else $friendId = $request->getInitiatorUserId();
		
		if($userModel->find($friendId))
		{
			if($userModel->getAvatar()){
				$userAvatar = "<img src=\"/media/avatar/big/{$userModel->getAvatar()}\" alt=\"{$userModel->getUsername()}\" />";
			}else{
				$userAvatar = "<img src=\"/theme/default/images/user.gif\" alt=\"avatar\" />";
			}
$html = <<<EOD
<div class="friend">
	<a class="f-avatar" href="/user/{$userModel->getUsername()}" title="{$userModel->getUsername()}">
		{$userAvatar}
	</a>
	<a class="f-name" href="/user/{$userModel->getUsername()}">
		{$userModel->getUsername()}
	</a>
</div>
EOD;
		}
		echo $html;
	}
	
	public static function userRequest(Default_Model_SocialUserConnections $request)
	{
		$html = "";
		$userModel = new Default_Model_AccountUsers();
		
		if($userModel->find($request->getInitiatorUserId()))
		{
			if($userModel->getAvatar()){
				$userAvatar = "<img src=\"/media/avatar/big/{$userModel->getAvatar()}\" alt=\"{$userModel->getUsername()}\" />";
			}else{
				$userAvatar = "<img src=\"/theme/default/images/user.gif\" alt=\"avatar\" />";
			}
			$html .= "
			<div class=\"usr-request\">
				<div class=\"usrr-avatar\">
					{$userAvatar}
				</div>
				<div class=\"usrr-name\">
					{$userModel->getUsername()}
				</div>
				<div class=\"usrr-actions\">
					<a class=\"usr-action\" href=\"/account/accept-request/id/{$request->getId()}\" />Accepta</a>
					<a class=\"usr-action\" href=\"/account/deny-request/id/{$request->getId()}\" />Sterge</a>
				</div>
				<div class=\"clear\"></div>
			</div>
			";
		}
		echo $html;
	}
	
	private static function timePlurals($number, $timeComponent)
	{
		$tC = array(
			'secunda'	=> ' secunde',
			'minut'		=> ' minute',
			'ora'		=> ' ore',
			'zi'		=> ' zile',
			'saptamana' => ' saptamani'
		);
		if($number != 1)
			return $tC[$timeComponent];
	}
	public static function relativeTime($timestamp)
	{
		if(NULL == $timestamp)
			return "acum";
		
		$diff = time() - $timestamp;
		if($diff<60)
			return "in urma cu " . $diff . self::timePlurals($diff, 'secunda');
		
		$diff = round($diff / 60);
		if($diff<60)
			return "in urma cu " . $diff . self::timePlurals($diff, 'minut');
		
		$diff = round($diff / 60);
		if($diff<24)
			return "in urma cu " . $diff . self::timePlurals($diff, 'ora');
		
		$diff = round($diff / 24);
		if($diff<7)
			return "in urma cu " . $diff . self::timePlurals($diff, 'zi');
		
		$diff = round($diff / 7);
		if($diff<4)
			return  "in urma cu " . $diff . self::timePlurals($diff, 'saptamana');
		
		return "in ".self::datetimeTranslate('month', date('n', $timestamp)). date(" j, Y", $timestamp);
	}
	
	public static function videoPlayer($postId, $width, $height)
	{
		$html = "";
		$videoModel = new Default_Model_Video();
		$select = $videoModel->getMapper()->getDbTable()->select()
				->where('productId = ?', $postId);
		$video = $videoModel->fetchAll($select);
		if(NULL != $video)
		{
			$url = $video[0]->getUrl();
			$html .= "
				<div id='mediaspace'>This text will be replaced</div>
				<script type='text/javascript'>
				jwplayer('mediaspace').setup({
					'flashplayer': '/theme/default/js/jwp/player.swf',
					'file': '".$url."',
					'controlbar': 'bottom',
					'width': '".$width."',
					'height': '".$height."',
					plugins:
					{
						viral: {
							onpause: false,
							oncomplete: false
						}
					}
				});
				</script>
			";
		}
		echo $html;
	}

	public static function videoThumb($postId, $name = NULL, $justLink = NULL)
	{
		$hqtumb = "<img src='http://i1.ytimg.com/vi/%s/hqdefault.jpg' alt='%s' title='%s' />";
		$hqUrl = "http://i1.ytimg.com/vi/%s/hqdefault.jpg";
		$html = "";
		
		$videoModel = new Default_Model_Video();
		$select = $videoModel->getMapper()->getDbTable()->select()
				->where('productId = ?', $postId);
		$video = $videoModel->fetchAll($select);

		if(NULL != $video)
		{
			$url = $video[0]->getUrl();
			$match = array();
			if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match))
			{
				$video_id = $match[1];
				$hqUrl = sprintf($hqUrl, $video_id);
				$html .= sprintf($hqtumb, $video_id, $name, $name);
			}
		}
		
		if(NULL != $justLink)
		{
			return $hqUrl;
		}else{
			echo $html;
		}
		
	}

	public static function article(Default_Model_CatalogProducts $model, $protected = false)
	{
		$product = new J_Product();
		$tsStatistics = new TS_Statistics();
		$product->Product($model->getId(), array('category', 'image', 'commentsNo', 'description'));
		$zendView = new Zend_View;
		$link = $zendView->url(array('categoryName' => preg_replace('/\s+/','-', strtolower($product->getCategory())), 'productName' => preg_replace('/\s+/','-', strtolower($product->getName())), 'id' => $product->getId()), 'product');
		if($protected) $noFollow = "rel=\"nofollow\"";
		else $noFollow = "";

		echo "<div class='modul'>
			<div class='header_modul'>";
        echo "
		<h1>
			<a href='".$link."'>".$product->getName()."</a>
		</h1>";
        echo "</div>";

		if($model->getType() == 'gallery')
		{
			echo "<a href='".$link."' title='".$product->getName()."'>";
				if($product->getBigImage())
				{
					echo "<img src='".$product->getBigImage()."' alt='".$product->getName()."' title='".$product->getName()."' />";
				}
				else
				{
					echo "<img src='/theme/default/images/no-pic.jpg' alt='no image' title='no image found' />";
				}
			echo "</a>";
		}
		elseif($model->getType() == 'video' && $protected == TRUE)
		{
			$videoImage = TS_Products::getVideo($product->getId())->getImage();
			if(NULL != $videoImage)
			{
				echo "<a href='".$link."' title='".$product->getName()."'>";
					echo "<img src='/media/catalog/video/big/".$videoImage."' alt='".$product->getName()."' title='".$product->getName()."' />";
				echo "</a>";
			}
			else
			{
				echo "<a href='".$link."' title='".$product->getName()."'>";
					echo "<img src='/theme/default/images/no-video.jpg' alt='".$product->getName()."' title='".$product->getName()."' />";
				echo "</a>";
			}
		}
		elseif($model->getType() == 'embed' && $protected == TRUE)
		{
			$videoImage = TS_Products::getVideo($product->getId())->getImage();
			echo "<a href='".$link."' title='".$product->getName()."'>";
				//echo TS_ToHtml::videoThumb($product->getId());
			echo "<img src='/media/catalog/video/big/".$videoImage."' alt='".$product->getName()."' title='".$product->getName()."' />";
			echo "</a>";
		}
		elseif($model->getType() == 'embed')
		{
			TS_ToHtml::videoPlayer($product->getId(), PLAYER_WIDTH, PLAYER_HEIGHT);
		}
		elseif($model->getType() == 'status')
		{
			$description = nl2br($product->getDescription());
			echo "
			{$description}
			";
		}
		else
		{
			echo TS_Products::getVideo($model->getId())->getEmbed();
		}
		echo "<div class='footer_modul'>";
			echo "<div class='left_footer_modul'>";
				echo "<div class='coment'>";
					echo "<p>Comentari: <span>".$product->getCommentsNo()."</span></p>";
					echo "<p>Vizualizari: <span>".$tsStatistics->calculateVisits($product->getId())."</span></p>";
				echo "</div>";
				echo "<div class='photo'>";
			   		echo "<img src='/theme/default/images/photo.gif' alt='numar poze in galerie'/>";
			   		echo "<p>".$tsStatistics->getImagesNumberByGalleryId($product->getId())."</p>";
				echo "</div>";
//				echo "<div class='adaugat'>
//				<p>adaugat de:<br/><span>nkjdanksd</span></p>
//				</div>";
			echo "</div>
		</div>
	</div>";
	}

	public static function socialShare($facebookUrl)
	{
		$html = '
		<div class="google-plus-container">
			<div class="g-plusone" data-size="medium"></div>
			<script type="text/javascript">
				window.___gcfg = {lang: \'ro\'};

				(function() {
				var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
				po.src = \'https://apis.google.com/js/plusone.js\';
				var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
				})();
			</script>
		</div>

		<div class="facebook-like-container">
			<div class="fb-like" data-href="'.$facebookUrl.'" data-send="false" data-layout="button_count" data-width="45" data-show-faces="false" data-font="arial"></div>
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) {return;}
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=269917363026754";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, \'script\', \'facebook-jssdk\'));</script>
		</div>
		';
		echo $html;
	}

	public static function similarGalleries($galleryId, $nrOfItems)
	{
		$similarGalleries = TS_Statistics::getSimilarGalleries($galleryId, $nrOfItems);
		if(null != $similarGalleries){
			$html = '
		<h1 class="galerii_similare">Galerii similare</h1>
		<div class="similare">';
			foreach($similarGalleries as $value){
				$html .= '
				<div class="modul_similare">
					<div>';
						$product = new J_Product();
						$product->Product($value->getId(), array('category', 'image'));
						$zendView = new Zend_View;
						$link = $zendView->url(array('categoryName' => preg_replace('/\s+/','-', strtolower($product->getCategory())), 'productName' => preg_replace('/\s+/','-', strtolower($product->getName())), 'id' => $product->getId()), 'product');
						$html .= '<a href="'.$link.'">';
						if($product->getType() == 'embed'){
							$img = TS_ToHtml::videoThumb($value->getId(), NULL, 1);
							$html .= "<img alt='{$product->getName()}' src='{$img}'";
						}elseif($product->getBigImage()){
							$html .= '<img src="'.$product->getSmallImage().'" alt="'.$product->getName().'" title="'.$product->getName().'" />';
						}else{
							$html .= '<img src="/theme/default/images/no-photo-small.jpg" alt="no image" title="no image found" />';
						}
						$html .= '</a>
					</div>
					<ul>
						<li>
							<h1>
								<a href="'.$link.'" title="'.$product->getName().'">
									'.$value->getName().'
								</a>
							</h1>
						</li>
						<li><p class="vizualizari">Vizualizari: <span>'.TS_Statistics::calculateVisits($value->getId()).'</span></p></li>
					</ul>
				</div>';
			}
		$html .= '</div>';
		}
		echo $html;
	}

	public static function comments($galleryId, $comentsNr, $form)
	{
		$tsComments = new TS_Comments();
		$commentsNr = $tsComments->getCommentsNumberById($galleryId);
		$comments = $tsComments->getAllCommentsById($galleryId);
		$html = "
		<div class=\"comentarii\">
			<div class=\"lasa_coment\">
				{$form}
			</div>";

		if($commentsNr > 0)
		{
			$html .= "
			<div class=\"header_comentarii\">
				<p>";
				if($comentsNr == '1')
				{
					$html .= "{$comentsNr} comentariu";
				}
				else
				{
					$html .= "{$comentsNr} comentarii";
				}
				$html .= "</p>
		</div>";
		}
		if($comments)
		{
			foreach($comments as $value)
			{
				$customer = TS_Statistics::customerModelById($value->getUserId(), array('id'=>'id', 'username'=>'username', 'avatar'=>'avatar'));
				$html .= "
			<div class=\"modul_comentariu\">
				<div class=\"left_comentariu\">";
				if(null != $customer->getAvatar())
				{
					$html .= "<img width=\"44\" height=\"44\" src=\"/media/avatar/small/{$customer->getAvatar()}\" alt=\"user\" />";
				}
				else
				{
					$html .= "<img width=\"44\" height=\"44\" src=\"/theme/default/images/user.gif\" alt=\"user\" />";
				}
				$datePosted = date('d/m/Y H:i', $value->getAdded());
				$html .= "</div>
				<div class=\"right_comentariu\">
					<p class=\"nume\">
						<a href=\"#\">{$value->getName()}</a> l <span>{$datePosted}</span></p>
					<p class=\"descriere\">{$value->getComment()}</p>
				</div>
				<div style=\"clear:both; width:1px\"></div>
			</div>";
			}
		}
	$html .= "</div>";
	echo $html;
	}

	public static function productDetails($bigImage, $bigImages, $result)
	{
		$html = "
		<div class=\"galerie\">";
			if($bigImage){
				foreach($bigImages as $image){
					$html .= "<img src=\"{$image}\" alt=\"{$result->getName()}\" title=\"{$result->getName()}\" />";
				}
			}
			else
			{
				$html .= "<img src=\"/theme/default/images/no-pic.jpg\" alt=\"fara imagine\" title=\"Poza nu a fost gasita\" />";
			}
		$html .= "</div>";
	}
	
	public static function imageForVideo($galleryId, $link = null)
	{
		if($link)
		{
			$html = "http://{$_SERVER['SERVER_NAME']}/theme/admin/icons/icon-video.png";
		}else{
			$html = "<img height='100' src='/theme/admin/icons/icon-video.png' alt='no image' title='no image found' />";
		}
		
		$video = new Default_Model_Video();
		$select = $video->getMapper()->getDbTable()->select()
				->from(array('video'), array('productId', 'image'))
				->where('productId = ?', $galleryId);
		$result = $video->fetchAll($select);
		if(null != $result)
		{
			if(NULL != $result[0]->getImage())
			{
				if($link)
				{
					$html = "http://{$_SERVER['SERVER_NAME']}/media/catalog/video/small/{$result[0]->getImage()}";
				}else{
					$html = "<img height='100' src='/media/catalog/video/small/{$result[0]->getImage()}' alt='{$result[0]->getImage()}' title='{$result[0]->getImage()}' />";
				}
				
			}
		}
		if($link)
		{
			
			return $html;
		}else{
			echo $html;
		}
	}
}