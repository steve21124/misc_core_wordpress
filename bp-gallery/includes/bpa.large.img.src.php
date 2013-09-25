<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
define ( 'BP_GALLPLUS_PLUGIN_URL', WP_PLUGIN_URL.'/bp-gallery/');
define('BP_PLUGIN_PATH', WP_PLUGIN_DIR.'/bp-gallery/');
ini_set("memory_limit","128M");

bpl_logdebug('bpa.large.img.src : '.$_SERVER['DOCUMENT_ROOT']);
function bp_gallplus_get_image($image_id){
		global $wpdb;
		$table_name = $wpdb->base_prefix . 'bp_gallplus_album';
			$sql = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d",$image_id) ;
//	bpl_logdebug('bp_gallplus_get_image sql : '.$sql);
			$result = $wpdb->get_results( $sql );
			return $result;
	}
	

function watermarkImage ($SourceFile, $WaterMarkText, $DestinationFile='') {
//$SourceFile is source of the image file to be watermarked
//$WaterMarkText is the text of the watermark
//$DestinationFile is the destination location where the watermarked images will be placed
 
	//Delete if destinaton file already exists
	@unlink($DestinationFile);

 
	//This is the vertical center of the image
	list($width, $height) = getimagesize($SourceFile);
 
	 
	$image = imagecreatefromjpeg($SourceFile);
	if(isset($WaterMarkText) && (strlen($WaterMarkText) > 0))
	{
		$image_p = imagecreatetruecolor($width, $height);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width, $height);
 
		//Path to the font file on the server. Do not miss to upload the font file
		$font = BP_PLUGIN_PATH.'includes/css/Nunito-Regular.ttf';
 
		//Font size
		$font_size = 16;
 		$bbox = imagettfbbox($font_size, 0, $font,  $WaterMarkText);
    $line_width = $bbox[0]+$bbox[2]+ 10; 
    $line_height = $bbox[1]-$bbox[7] - 10; 
		$top = $height - $line_height;
		$left = $width - $line_width;
		//Give a white shadow
		$white = imagecolorallocate($image_p, 255, 255, 255);
		imagettftext($image_p, $font_size, 0, $left, $top, $white, $font, $WaterMarkText);
 
		//Print in black color
		$black = imagecolorallocate($image_p, 0, 0, 0);
		imagettftext($image_p, $font_size, 0, $left-2, $top-1, $black, $font, $WaterMarkText);
 
		if ($DestinationFile<>'') {
 
			imagejpeg ($image_p, $DestinationFile, 100);
 
		} 
		else {
 
		header('Content-Type: image/jpeg');
 
		imagejpeg($image_p, null, 100);
 
		}
		imagedestroy($image_p);
	}
	else {
 		header('Content-Type: image/jpeg');
 		imagejpeg($image, null, 100);
 
	}
 
	imagedestroy($image);
 
 
};

if(isset($_GET['token']))
{
	$token = intval(base64_decode($_GET['token']));
	$utc_str = gmdate("M d Y", time());
  							 $utc = strtotime($utc_str);
	$image_id = intval($token) -$utc;
	$image_data = bp_gallplus_get_image($image_id);

	if($image_data)
	{
		if(get_site_option( 'bp_gallplus_use_watermark' ))
		{
			watermarkImage($image_data[0]->pic_org_path,get_site_option( 'bp_gallplus_watermark_text' ));
		}
		else
		{
			watermarkImage($image_data[0]->pic_org_path,'');
		}
	}
	
}
	function bpl_logdebug($debugStr)
	{
				if(!is_dir(BP_PLUGIN_PATH.'debug'))
				{
					mkdir(BP_PLUGIN_PATH.'debug');
				}
				global $wp_query;
		   	$BP_DEBUG_DIR = BP_PLUGIN_PATH.'debug/bpldebug'.date('dmY').'.log'; 
		
	    	$date = date('d.m.Y H:i:s'); 
    		$log = $date." : [BP] ".$debugStr."\n"; 
    		error_log($log, 3, $BP_DEBUG_DIR); 
	
	}

?>