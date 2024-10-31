<?php
/**
* overlay class lib - PHP 4+ :(
* @author David HOHL <info@fishme.de> www.fishme.de
* @since 0.9.2
* @version 0.9.2
*			- add widgetloader
*			- add imageloader
*/
header('Content-Type: text/html; charset=utf-8');
define('AJAX_CALL','set');
// Default wp loading methode
require_once ('../../../../wp-load.php');
// load overlayLib
include_once(WP_PLUGIN_DIR . '/overlay/lib/overlayLib.php');
$overlayLib = new overlayLib();
$overlayLib->init_overlay();


if($_GET['widget']) {
	require_once('call_widget.php');
} elseif($_GET['image'] && is_numeric($_GET['pid']) && $_GET['pid']) {    
	require_once('call_image.php');
} else {
	echo '<h1>loading error #1 - Call</h1>';
	exit;
}


?>