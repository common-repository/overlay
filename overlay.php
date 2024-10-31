<?php
/*
Plugin Name: Overlay
Plugin URI: http://www.fishme.de/overlay
Description: jQuery Tools Overlay Plugin  (just another and better Lightbox)
Version: 0.9.3.2
Author: David Hohl
Author URI: http://www.fishme.de
*/

/*
Todo:
	- Problems with Wordpress command - add script - jquery ... ???
	- add Translations
	
Changelog:
0.9.3.2
	- add new jquery tool lib
    - fix widget loader
0.9.3.1
	- Bugfix: Save without widgets
0.9.3
	- add ajax image load and resize functions
	- add own Theme   
	- add disable Apple Effect
	- fix IE 8 and Chrome Bug
	- fix Regex Bugs 
0.9.2
	- add documentation and code redesign
	- add admin overlay css
	- add widgetloader
0.9.1.1
	- delete own jquery call
0.9.1
	- add basic structur
*/


// set default options
add_option('overlay_theme', 'transparent');
add_option('overlay_width', '570px');
add_option('overlay_height', '350px');
add_option('overlay_image_auto_resize', 1);
add_option('overlay_external_page', '');

// default animation settings for overlay.js
$default_overlay_js_settings = serialize(
	array(
		'effect'		=>	'apple',  
		'closeonEsc'	=>	1,
		'closeonClick'	=>	1,
		'speed'			=>	'normal',
		'top'			=>	'15%',
		'fixed'			=>	1,
		'load'			=>	0,
		'mask_state'	=>	0,
		'mask_color'	=>	'#000000',
	));
add_option('overlay_default_animation', $default_overlay_js_settings);
add_option('overlay_animation', $default_overlay_js_settings);


/**
* parse post for img tag and add the rel tag (selector in overlay.js)
* @version 0.9.3
* @author David Hohl <info@fishme.de>
* @changelog
*		0.9.1 - 24.07.2010 by David Hohl
*			- add function
*		0.9.2 - 25.07.2010 - 29.07.2010 by David Hohl
*			- add documentation
*		0.9.3 - 01.08.2010 - 07.08.2010 by David Hohl
*			- remove regex for img tag
* @return string content
* @param string $content
*/
function add_image_rel ($content) {
	global $post;
	$content = str_replace('<img ', '<img rel="overlay_image_'.$post->ID.'"', $content);
	return $content;
} // add_image_rel

add_filter('the_content', 'add_image_rel', 99);
add_filter('the_excerpt', 'add_image_rel', 99);


/**
* add admin and website overlay stylesheets
* @version 0.9.2
* @author David Hohl <info@fishme.de>
* @changelog
*		0.9.1 - 24.07.2010 by David Hohl
*			- add function
*		0.9.2 - 25.07.2010 by David Hohl
*			- add documentation
*			- change hardcoded path url
*/
function add_overlay_stylesheet() {
	$stylesheet_file_name = 'overlay.css';
	if(is_admin()) 
    	$stylesheet_file_name = 'overlay_admin.css';
	
	$overlayStyleUrl = get_overlay_plugin_url() . '/styles/' . $stylesheet_file_name;
	$overlayStyleFile = get_overlay_plugin_path() . '/styles/' . $stylesheet_file_name;	
    if ( file_exists($overlayStyleFile) ) {
        wp_register_style('overlayStyleSheet', $overlayStyleUrl);
        wp_enqueue_style( 'overlayStyleSheet');
    }
} // add_overlay_stylesheet


/**
* Get overlay plugin url
* @version 0.9.2
* @author David Hohl <info@fishme.de>
* @changelog
*		0.9.1 - 24.07.2010 by David Hohl
*			- add function
*		0.9.2 - 25.07.2010 by David Hohl
*			- add documentation
* @return string	Plugin url
*/
function get_overlay_plugin_url() {
	return WP_PLUGIN_URL."/overlay/"; 
} // get_overlay_plugin_url


/**
* Get overlay plugin path
* @version 0.9.2
* @author David Hohl <info@fishme.de>
* @changelog
*		0.9.2 - 25.07.2010 by David Hohl
*			- add function
*			- add documentation
* @return string	Plugin path
*/
function get_overlay_plugin_path() {
	return WP_PLUGIN_DIR."/overlay/"; 
} // get_overlay_plugin_path


/**
* Init overlay admin
* @version 0.9.2
* @author David Hohl <info@fishme.de>
* @todo
*		- check wp_enqueue_script('jquery')
* @changelog
*		0.9.2 - 25.07.2010 by David Hohl
*			- add function
*/
function overlay_admin_init() {
	add_overlay_stylesheet();
	add_action('init', create_function('', 'load_plugin_textdomain(\'overlay\');') );
	// add styles and scripts
	//	wp_enqueue_script('jquery'); 
//	wp_enqueue_script('myjquery', (get_overlay_plugin_url() . 'js/jquery.js'));
	wp_enqueue_script('jquerytools', (get_overlay_plugin_url() . 'js/jquery.tools.min.js'));	
	wp_enqueue_script('overlay_admin', (get_overlay_plugin_url() . 'js/overlay_admin.js'));
} // overlay_admin_init


/* ---- INIT Calls ----- */

if (!is_admin()) { 
	// jquery?? what's up?
//	wp_enqueue_script('jquery'); 
	wp_enqueue_script('jquerytools', (get_overlay_plugin_url() . 'js/jquery.tools.min.js'));
	wp_enqueue_script('overlay', (get_overlay_plugin_url() . 'js/overlay.js'));
	add_action('wp_print_styles', 'add_overlay_stylesheet');
} else {
	/* Adminstration Part */
	
	$options_page = get_option('siteurl') . '/wp-admin/admin.php?page=overlay/options.php';
	/* Adds our admin options under "Options" */
	function overlay_options_page() {
		add_options_page('Overlay Options', 'Overlay', 10, 'overlay/options.php');
	}
	add_action('admin_init', 'overlay_admin_init');	// init all admin commands
	add_action('admin_menu', 'overlay_options_page');
	register_sidebar( array(
		'name' => 'Drag Overlay Widgets',
		'id' => 'overlay_view',
		'description' => 'You need this sidebar for all Widgets inside overlay'
	) );
}
?>
