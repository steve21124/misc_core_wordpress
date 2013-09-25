<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
define ( 'BP_GALLPLUS_PLUGIN_URL', WP_PLUGIN_URL.'/bp-gallery/');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>title</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
  </head>
  <body>
  <script>
  	var message="Sorry, right-click has been disabled"; 
/////////////////////////////////// 
function clickIE() {if (document.all) {(message);return false;}} 
function clickNS(e) {if 
(document.layers||(document.getElementById&&!document.all)) { 
if (e.which==2||e.which==3) {(message);return false;}}} 
if (document.layers) 
{document.captureEvents(Event.MOUSEDOWN);document.onmousedown=clickNS;} 
else{document.onmouseup=clickNS;document.oncontextmenu=clickIE;} 
document.oncontextmenu=new Function("return false") 
	</script>
<?php
	if(is_user_logged_in())
	{
		if(isset($_GET['token']))
		{
			$imagURL = BP_GALLPLUS_PLUGIN_URL.'includes/bpa.large.img.src.php?token='.$_GET['token'];
			echo '<img src="'.$imagURL.'" />';
		}
	}
	else
	{
		echo 'Sorry you must be logged into <a href="'.get_site_url().'">'.get_site_url().' </a> to view this image';
	}
?>    <!-- page content -->
  </body>
</html>
