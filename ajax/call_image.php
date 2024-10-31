<?php
/**
* load large image for AJAX call
* @author David HOHL <info@fishme.de> www.fishme.de
* @since 0.9.2.1
* @version 0.9.2.1
*			- add imageloader
*			- resize image
* @todo 
*		- storage resize (1.0)
*
* required params - GET: image(image path), pid (postid)
*/

if(!defined('AJAX_CALL')) {
	echo '<h1>loading error #1 - Image</h1>';
	exit;
}                       



$resize_image = true;
$wp_size = 'medium';
$image_url = urldecode($_GET['image']);               
$image = explode('/',$image_url);
// get post
$args = array(
	'post_type' => 'attachment',
	'numberposts' => -1,
	'post_status' => null,
	'post_parent' => $_GET['pid']
	); 
$attachments = get_posts($args);
if ($attachments) {
	foreach ($attachments as $attachment) {
		$_post_meta = get_post_meta($attachment->ID,'_wp_attachment_metadata');  
		if(is_array($_post_meta)) {
			foreach ($_post_meta as $key => $value) {  
				if(is_array($value['sizes'])) {
		        	foreach ($value['sizes'] as $skey => $svalue) {
						if(in_array($svalue['file'], $image) || in_array($value['file'], $image)) {
		                    $_image->url = str_replace($image[count($image) - 1],$value['sizes']['medium']['file'],$image_url);
							$_image->width =  $value['sizes']['medium']['width'];
							$_image->height =  $value['sizes']['medium']['height'];
							$_image->obj = $value;
							break;
						}
		        	}  
				} 
			}        
		}		
	}
}   
if($resize_image) {
	$wp_upload_dir = wp_upload_dir();
	$orginal_image_dir = $wp_upload_dir['basedir'] . '/' . $_image->obj['file'];
	if(file_exists($orginal_image_dir)) {    
		// resize image for the overlay
		$orginal_image_size = getimagesize($orginal_image_dir);
		$overlay_dimensions = wp_constrain_dimensions($orginal_image_size[0],$orginal_image_size[1],get_option('overlay_width'),get_option('overlay_height'));
	   	echo '<img src="'.$wp_upload_dir['baseurl'] . '/' . $_image->obj['file'] . '" width="'.$overlay_dimensions[0].'" height="'.$overlay_dimensions[1].'" />'; 
	} else {
		// output backup image   
		if($_image->url) {
			echo '<img src="'.$image_url.'" />';	
		} else {
			echo '<h1>loading error #2 - Image</h1>';
			exit;
		}
		 
	}
} else {
	echo '<img src="'.$_image->url.'" width="'.$_image->width.'" height="'.$_image->height.'" />'; 
} 
?>