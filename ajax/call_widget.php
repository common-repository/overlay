<?php
/**
* get widget about AJAX
* @author David HOHL <info@fishme.de> www.fishme.de
* @since 0.9.2.1
* @version 0.9.2.1
*			- add file
*/

if(!AJAX_CALL) exit;

if(!$_GET['tid'] || !$_GET['widget']) {
	echo '<h1>loading error #1 - Widget</h1>';
	exit;
}

$load_overlay_widget = $_GET['widget'];

// set sidebar into theme
register_sidebar( array(
	'name' => 'Drag Overlay Widgets',
	'id' => 'overlay_view',
	'description' => 'You need this sidebar for all Widgets inside overlay',
	'before_widget' => '<div id="overlay_widgetloader">',
	'after_widget'  => '</div>',
));

// check if _GET widget exists in the overlay settings
if(!$overlayLib->get_external_page($load_overlay_widget)) {
	echo '<h1>loading error #2 - Widget</h1>';
	exit;
}

function o_dynamic_sidebar($index = 1) {
	global $wp_registered_sidebars, $wp_registered_widgets;
	// add by David HOHL 28.07.2010 v0.9.2
	global $load_overlay_widget;
	// end by David HOHL 28.07.2010 v0.9.2
	
	if ( is_int($index) ) {
		$index = "sidebar-$index";
	} else {
		$index = sanitize_title($index);
		foreach ( (array) $wp_registered_sidebars as $key => $value ) {
			if ( sanitize_title($value['name']) == $index ) {
				$index = $key;
				break;
			}
		}
	}

	$sidebars_widgets = wp_get_sidebars_widgets();
	if ( empty($wp_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_widgets) || !is_array($sidebars_widgets[$index]) || empty($sidebars_widgets[$index]) )
		return false;

	$sidebar = $wp_registered_sidebars[$index];

	$did_one = false;
	
	// add by David HOHL 28.07.2010 v0.9.2
	// rebuild sidebar widgets
	unset($sidebars_widgets[$index]);
	$sidebars_widgets[$index][0] = $load_overlay_widget;
	// end by David HOHL 28.07.2010 v0.9.2
	
	foreach ( (array) $sidebars_widgets[$index] as $id ) {

		if ( !isset($wp_registered_widgets[$id]) ) continue;

		$params = array_merge(
			array( array_merge( $sidebar, array('widget_id' => $id, 'widget_name' => $wp_registered_widgets[$id]['name']) ) ),
			(array) $wp_registered_widgets[$id]['params']
		);

		// Substitute HTML id and class attributes into before_widget
		$classname_ = '';
		foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
			if ( is_string($cn) )
				$classname_ .= '_' . $cn;
			elseif ( is_object($cn) )
				$classname_ .= '_' . get_class($cn);
		}
		$classname_ = ltrim($classname_, '_');
		$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);

		$params = apply_filters( 'dynamic_sidebar_params', $params );

		$callback = $wp_registered_widgets[$id]['callback'];

		do_action( 'dynamic_sidebar', $wp_registered_widgets[$id] );

		if ( is_callable($callback) ) {
			call_user_func_array($callback, $params);
			$did_one = true;
		}
	}

	return $did_one;
}
o_dynamic_sidebar('overlay_view');

?>