<?php
class TS_Catalog
{
	public static function displayVideoThumbMenuLeft($id, $name, $type, $link)
	{
		?>
		<a class="videResize" href="<?php echo $link;?>" title="<?php echo $name;?>">
			<?php if($type == 'embed'){?>
				<img width="100" alt="" src="<?php echo TS_ToHtml::videoThumb($id, NULL, 1)?>">
			<?php }else{?>
				<?php TS_ToHtml::imageForVideo($id);?>
			<?php }?>
		</a>
		<?php
	}
	
	public static function checkIfFavorited($userId, $galleryId, $type)
	{
		if(null != $userId && null != $galleryId){
			$model = new Default_Model_CatalogProductFavorites();
			$select = $model->getMapper()->getDbTable()->select()
				->where('type = ?', $type)
				->where('userId = ?', $userId)
				->where('productId = ?', $galleryId);
			$result = $model->fetchAll($select);
			if(null != $result){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public static function deleteArticle($articleId)
	{
		$return = false;
		$modelArticle = new Default_Model_CatalogProducts();
		if($modelArticle->find($articleId))
		{
			// BEGIN: Delete images and image folders
			$modelArticleImages = new Default_Model_CatalogProductImages();
			$selectArticleImages = $modelArticleImages->getMapper()->getDbTable()->select()
				->where('product_id = ?', $modelArticle->getId());
			$resultArticleImages = $modelArticleImages->fetchAll($selectArticleImages);
			if($resultArticleImages)
			{
				foreach($resultArticleImages as $value)
				{					
					$image = $value->getName();					
					if($value->delete())
					{
						$allowed = "/[^a-z0-9\\-\\_]+/i";  
						$folderName = preg_replace($allowed,"-", strtolower(trim($modelArticle->getName())));	
						$folderName = trim($folderName,'-');
						@unlink('media/catalog/products/'.($modelArticle->getUser_id()?$modelArticle->getUser_id():'0').'/'.$folderName.'/big/'.$image);
						@unlink('media/catalog/products/'.($modelArticle->getUser_id()?$modelArticle->getUser_id():'0').'/'.$folderName.'/small/'.$image);						
					}
				}
				rmdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/'.($modelArticle->getUser_id()?$modelArticle->getUser_id():'0').'/'.$folderName.'/big');
				rmdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/'.($modelArticle->getUser_id()?$modelArticle->getUser_id():'0').'/'.$folderName.'/small');
				rmdir(APPLICATION_PUBLIC_PATH . '/media/catalog/products/'.($modelArticle->getUser_id()?$modelArticle->getUser_id():'0').'/'.$folderName);
			}
			// END: Delete images and image folders
			
			// BEGIN: Delete article tags
			$modelTags = new Default_Model_CatalogProductTags();
			$whereTags = $modelTags->getMapper()->getDbTable()->getAdapter()
				->quoteInto('product_id = ?', $modelArticle->getId());				
			$modelTags->getMapper()->getDbTable()->delete($whereTags);
			// END: Delete article tags
			
			// BEGIN: Delete article comments
			$modelTags = new Default_Model_CatalogProductComments();
			$whereTags = $modelTags->getMapper()->getDbTable()->getAdapter()
				->quoteInto('product_id = ?', $modelArticle->getId());				
			$modelTags->getMapper()->getDbTable()->delete($whereTags);
			// END: Delete article comments
			
			// BEGIN: Delete article visits
			$modelCatalogVisits = new Default_Model_CatalogProductVisits();
			$whereCatalogVisits = $modelCatalogVisits->getMapper()->getDbTable()->getAdapter()
				->quoteInto('product_id = ?', $modelArticle->getId());				
			$modelCatalogVisits->getMapper()->getDbTable()->delete($whereCatalogVisits);
			// END: Delete article visits
			
			// BEGIN: Delete article statistics ratings
			$modelRatings = new Default_Model_StatisticsRatings();
			$whereRatings = $modelRatings->getMapper()->getDbTable()->getAdapter()
				->quoteInto('productId = ?', $modelArticle->getId());				
			$modelRatings->getMapper()->getDbTable()->delete($whereRatings);
			// END: Delete article statistics ratings
			
			// BEGIN: Delete article statistics visits
			$modelVisits = new Default_Model_StatisticsVisits();
			$whereVisits = $modelRatings->getMapper()->getDbTable()->getAdapter()
				->quoteInto('productId = ?', $modelArticle->getId());				
			$modelVisits->getMapper()->getDbTable()->delete($whereVisits);
			// END: Delete article statistics visits
			
			// BEGIN: Delete article video
			if($modelArticle->getType() == 'video' || $modelArticle->getType() == 'embed')
			{
				$modelVideo = new Default_Model_Video();
				$whereVideo = $modelRatings->getMapper()->getDbTable()->getAdapter()
					->quoteInto('productId = ?', $modelArticle->getId());				
				$modelVideo->getMapper()->getDbTable()->delete($whereVideo);
			}
			// END: Delete article video
			
			$modelArticle->delete();
		}
		return $return;
	}

// BEGIN: Media uploaders
	public static function saveImage($productId, $imageName, $tmpName, $folderName)
	{
		$userId = Zend_Registry::get('authUser')->getId();
		$rand = rand(99, 9999);
		$tmp = pathinfo($tmpName);
		$extension = (!empty($tmp['extension']))?$tmp['extension']:null;
		$pozaNume = $folderName.'-'.$rand.'.'.$extension;
		$model2 = new Default_Model_CatalogProductImages();
		$model2->setProduct_id($productId);
		$model2->setPosition('999');
		$model2->setName($pozaNume);
		if($model2->save()) {
			require_once APPLICATION_PUBLIC_PATH.'/library/Needs/tsThumb/ThumbLib.inc.php';
			$thumb = PhpThumbFactory::create(APPLICATION_PUBLIC_PATH.'/media/temp/'.$tmpName);
			$thumb->resize(600, 600)
				  ->tsWatermark(APPLICATION_PUBLIC_PATH."/media/watermark-small.png")
				  ->save(APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$userId.'/'.$folderName.'/big/'.$pozaNume);
			$thumb->tsResizeWithFill(100, 100, "ffffff")->save(APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$userId.'/'.$folderName.'/small/'.$pozaNume);
			@unlink('media/temp/'.$tmpName);
		}
	}
	
	public static function saveTags($productId, $tags)
	{
		if($tags)
		{
			$tags = explode(',', $tags);
			foreach($tags as $tag)
			{
				$tag = trim($tag);
				$model2 = new Default_Model_Tags();
				$select = $model2->getMapper()->getDbTable()->select()
						->where('name = ?', $tag);
				$result = $model2->fetchAll($select);
				if($result)
				{
					$model3 = new Default_Model_CatalogProductTags();
					$model3->setProduct_id($productId);
					$model3->setTag_id($result[0]->getId());
					$model3->save();
				} else {
					$model3 = new Default_Model_Tags();
					$model3->setName($tag);
					$tagId = $model3->save();
					if($tagId) {
						$model4 = new Default_Model_CatalogProductTags();
						$model4->setProduct_id($productId);
						$model4->setTag_id($tagId);
						$model4->save();
					}
				}
			}
			return true;
		}
		return false;
	}
	
	public static function multipleUploaderCustom($formId)
	{
		?>
		<!-- Third party script for BrowserPlus runtime (Google Gears included in Gears runtime now) -->
		<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
		
		<!-- Load plupload and all it's runtimes and finally the jQuery queue widget -->
		<script type="text/javascript" src="<?php echo "/theme/default/js/plupload/js/plupload.full.js";?>"></script>
		
		<script type="text/javascript">
		// Custom example logic
		$(function() {
			var uploader = new plupload.Uploader({
				runtimes : 'gears,html5,flash,silverlight,browserplus',
				browse_button : 'pickfiles',
				container : 'container',
				max_file_size : '10mb',
				url : 'upload.php',
				flash_swf_url : '/plupload/js/plupload.flash.swf',
				silverlight_xap_url : '/plupload/js/plupload.silverlight.xap',
				filters : [
					{title : "Image files", extensions : "jpg,gif,png"},
					{title : "Zip files", extensions : "zip"}
				],
				resize : {width : 320, height : 240, quality : 90}
			});
		
			uploader.bind('Init', function(up, params) {
				$('#filelist').html("<div>Current runtime: " + params.runtime + "</div>");
			});
		
			$('#uploadfiles').click(function(e) {
				uploader.start();
				e.preventDefault();
			});
		
			uploader.init();
		
			uploader.bind('FilesAdded', function(up, files) {
				$.each(files, function(i, file) {
					$('#filelist').append(
						'<div id="' + file.id + '">' +
						file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
					'</div>');
				});
		
				up.refresh(); // Reposition Flash/Silverlight
			});
		
			uploader.bind('UploadProgress', function(up, file) {
				$('#' + file.id + " b").html(file.percent + "%");
			});
		
			uploader.bind('Error', function(up, err) {
				$('#filelist').append("<div>Error: " + err.code +
					", Message: " + err.message +
					(err.file ? ", File: " + err.file.name : "") +
					"</div>"
				);
		
				up.refresh(); // Reposition Flash/Silverlight
			});
		
			uploader.bind('FileUploaded', function(up, file) {
				$('#' + file.id + " b").html("100%");
			});
		});
		</script>
		<h3>Custom example</h3>
		<div id="container">
			<div id="filelist">No runtime found.</div>
			<br />
			<a id="pickfiles" href="#">[Select files]</a>
			<a id="uploadfiles" href="#">[Upload files]</a>
		</div>
	<?php
	}
	public static function multipleUploaderJqueryUI($formId)
	{
		?>
		<link rel="stylesheet" href="<?php echo "/theme/default/js/plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css";?>" type="text/css" />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
		<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
		
		<script type="text/javascript" src="<?php echo "/theme/default/js/plupload/js/plupload.js";?>"></script>
		<script type="text/javascript" src="<?php echo "/theme/default/js/plupload/js/plupload.gears.js";?>"></script>
		<script type="text/javascript" src="<?php echo "/theme/default/js/plupload/js/plupload.silverlight.js";?>"></script>
		<script type="text/javascript" src="<?php echo "/theme/default/js/plupload/js/plupload.flash.js";?>"></script>
		<script type="text/javascript" src="<?php echo "/theme/default/js/plupload/js/plupload.browserplus.js";?>"></script>
		<script type="text/javascript" src="<?php echo "/theme/default/js/plupload/js/plupload.html4.js";?>"></script>
		<script type="text/javascript" src="<?php echo "/theme/default/js/plupload/js/plupload.html5.js";?>"></script>
		<script type="text/javascript" src="<?php echo "/theme/default/js/plupload/js/jquery.ui.plupload/jquery.ui.plupload.js";?>"></script>
		<style type="text/css">
		.plupload_content{background: #fff;}
		.plupload_header_content{display: none;}
		#uploader{border: 1px solid #cecece;border-radius: 5px;}
		.plupload_filelist{border-bottom: 1px solid #cecece;background: #ddd;}
		.plupload_filelist_footer{border-top: 1px solid #cecece;background: #ddd;}
		.plupload_file{border-bottom: 1px solid #cecece;}
		.plupload_button{border: 1px solid #aaa;border-radius: 3px;}
		</style>
		<div class="photo_uploader" id="uploader">
			<p>You browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
		</div>
		<script>
			$(function() {
				$("#uploader").plupload({
					runtimes : 'flash,html5,browserplus,silverlight,gears,html4',
					url : '<?php echo "/upload";?>',
					max_file_size : '20mb',
					max_file_count: 20,
					chunk_size : '1mb',
					unique_names : true,
					multiple_queues : true,
					resize : {width : 600, height : 600, quality : 100},
					//crename: true,
					sortable: true,
					filters : [
						{title : "Image files", extensions : "jpg,gif,png"},
						{title : "Zip files", extensions : "zip,avi"}
					],
					flash_swf_url : '<?php echo "/theme/default/js/plupload/js/plupload.flash.swf";?>',
					silverlight_xap_url : '<?php echo "/theme/default/js/plupload/js/plupload.silverlight.xap";?>'
				});

				var uploader = $('#uploader').plupload('getUploader');
			    uploader.bind('FilesAdded', function(up, files) {
    		        $.each(files, function(i, file) {
    		            $('#filelist').append(
    		                '<div id="' + file.id + '">' +
    		                file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
    		            '</div>');
    		        });
    		        up.refresh(); // Reposition Flash/Silverlight
    		    });
				$('#<?php echo $formId;?>').submit(function(e) {
			
			        if (uploader.files.length > 0) {
			            uploader.bind('StateChanged', function() {
			                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
			                    $('form')[0].submit();
			                }
			            });
			                
			            uploader.start();
			        } else
			            alert('You must at least upload one file.');
			
			        return false;
			    });
			});
		</script>
		<?php
	}
// END: Media uploaders
	
	public static function show()
	{
		
	}
	
	public static function getCategories()
	{
		
		$model = new Default_Model_CatalogCategories();
		$select = $model->getMapper()->getDbTable()->select()
			->where('status = ?', '1');
		$result = $model->fetchAll($select);
		if($result)
		{
			return $result;
		}
		return null;
	}
// BEGIN: Comments
	
	public static function galleryComments($galleryId)
	{
		$model = new Default_Model_CatalogProductComments();
		$select = $model->getMapper()->getDbTable()->select()
			->where('product_id = ?', $galleryId)
			->order('added DESC');
		$result = $model->fetchAll($select);
		if($result)
		{
			return $result;
		}
		else
		{
			return null;
		}
	}
// END: Comments

// BEGIN: Favorites
	public static function galleryFavorites($galleryId)
	{
		$model = new Default_Model_CatalogProductFavorites();
		$select = $model->getMapper()->getDbTable()->select()
			->where('productId = ?', $galleryId);
		$result = $model->fetchAll($select);
		if($result)
		{
			return $result;
		}
		return null;
	}
// END: Favorites

// BEGIN: Top viewed galleries this week
	public static function topViewedGalleries($type, $limit)
	{
		$model = new Default_Model_StatisticsVisits();
		$select = $model->getMapper()->getDbTable()->select()
			->from(array('v'=>'j_statistics_visits'), array('statisticsId'=>'COUNT(productId)', 'productId', 'created'))
			->join(array('g'=>'j_catalog_products'), ('v.productId = g.id'), array('galleryType'=>'g.type'))
			->where('v.created > ?', date('Y-m-d H:i:s', time()-604800))
			->where('g.type = ?', $type)
			->group('v.productId')
			->order('v.statisticsId ASC')
			->limit($limit)
			->setIntegrityCheck(false);
		echo $select;die();
		$result = $model->fetchAll($select);
		if($result)
		{
			return $result;
		}
		return null;
	}
// END: Top viewed galleries this week

// BEGIN: Tools
	public static function userIdToModel($userId)
	{
		$model = new Default_Model_AccountUsers();
		if($model->find($userId))
		{
			return $model;
		}
		return null;
	}
	
	public static function galleryIdToName($galleryId)
	{
		
	}
// END: Tools
}