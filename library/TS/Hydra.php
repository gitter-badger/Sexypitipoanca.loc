<?php
class TS_Hydra
{
	/**
	 * fetchPage
	 * fetch the content of a page with cURL
	 * @param varchar $URL - urlencoded
	 * @return text $output - page content
	 * http://ziza.qip.ru/2009/03/17/chudesa_na_uralskikh_avialinijakh_60_foto_nju.html
	 */
	public function fetchPage($URL)
	{
		$link = urldecode($URL);
		$output = "";
		$ch = curl_init();
		$userAgent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)";
		
		curl_setopt($ch, CURLOPT_URL, $link);
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		
		return $output;
	}
	
	public function fetchImageList($data)
	{
		$images = array();
		preg_match_all ('/(img|src)\=(\"|\')[^\"\'\>]+/i', $data, $media);
		unset ($data);
		$data = preg_replace('/(img|src)(\"|\'|\=\"|\=\')(.*)/i', "$3", $media[0]);		
		foreach($data as $url)
		{
			$info = pathinfo($url);
			if (isset($info['extension']))
			{
				if (($info['extension'] == 'jpg') ||
				($info['extension'] == 'jpeg') ||
				($info['extension'] == 'gif') ||
				($info['extension'] == 'png'))
				array_push($images, $url);
			}
		}
		return array_unique($images);
	}
}

//http://curl.haxx.se/libcurl/php/examples/simplepost.html

/*
  you can use it in your webpage like this:
  <img src=resizejpg.php?size=60&filename=http://anydomain.com/anyimage.jpg>

  Hope the code helps you! / Michael

*/

//function LoadJpeg ($imgname) {
//$im = @ImageCreateFromJPEG ($imgname); /* Attempt to open */
//if (!$im) { /* See if it failed */
//  $im = ImageCreate (150, 30); /* Create a blank image */
//  $bgc = ImageColorAllocate ($im, 255, 255, 255);
//  $tc = ImageColorAllocate ($im, 0, 0, 0);
//  ImageFilledRectangle ($im, 0, 0, 150, 30, $bgc);
//  /* Output an errmsg */
//  ImageString ($im, 1, 5, 5, "Error $imgname", $tc);
//}
//  return $im;
//}
//
//$id=$filename;
//$sz=$size;
//$savefile="tempimg/".time().".jpg";
//
//$ch = curl_init ($id);
//$fp = fopen ($savefile, "w");
//curl_setopt ($ch, CURLOPT_FILE, $fp);
//curl_setopt ($ch, CURLOPT_HEADER, 0);
//curl_exec ($ch);
//curl_close ($ch);
//fclose ($fp);
//
//$im=LoadJpeg($savefile);
//
//// output
//$im_width=imageSX($im);
//$im_height=imageSY($im);
//
//// work out new sizes
//if($im_width >= $im_height)
//{
//  $factor = $sz/$im_width;
//  $new_width = $sz;
//  $new_height = $im_height * $factor;
//}
//else
//{
//  $factor = $sz/$im_height;
//  $new_height = $sz;
//  $new_width = $im_width * $factor;
//}
//
//// resize
//$new_im=ImageCreate($new_width,$new_height);
//ImageCopyResized($new_im,$im,0,0,0,0,
//                 $new_width,$new_height,$im_width,$im_height);
//
//// output
//Header("Content-type: image/jpeg");
//header( "Content-Disposition:attachment;filename=$filename" );
//header( "Content-Description:PHP Generated Image" );
//Imagejpeg($new_im,'',75); // quality 75
//
//// cleanup
//ImageDestroy($im);
//ImageDestroy($new_im);
//unlink($savefile);
?>