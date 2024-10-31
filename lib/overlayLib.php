<?php
/**
* overlay class lib - PHP 4+ :(
* @author David HOHL <info@fishme.de> www.fishme.de
* @since 0.9.2
* @version  0.9.3
* @changelog
*		0.9.3
*			- change save_settings  
*			- add documentation
*			
*		0.9.2
*			- add class and functions
*/
class overlayLib {
	
	var $sidebar = 'overlay_view';
	var $wp_registered_widgets;
	var $overlay_widgets;     
	
	/**
	* php4 constructor - init overlayLib
	* @author David Hohl <info@fishme.de>
	* @version 0.9.2
	*/
	function init_overlay() {
		global $wp_registered_widgets;
		$this->wp_registered_widgets = $wp_registered_widgets;
		$this->get_overlay_sidebar_widgets();
		
	} // init_overlay 
	
	/**
	* helperfunction: for jstempalte output
	* @author David Hohl <info@fishme.de>
	* @version 0.9.2
	* @param 	string		$value		
	*			
	* @return boolean	boolean output
	*/	
	function get_js_value($v) {
		if($v==1) return 'true';
		if(!$v) return 'false';	
		return $v;
	} // get_js_value  
	
	/**
	* return widgetline (selector, path, widget)
	* @author David Hohl <info@fishme.de>
	* @version 0.9.2
	* @param 	integer		$key	internal key
	*			string		$value	preselected value			
	* @return output line
	*/
	function external_page_line($key, $value = '') {
		?>
			<tr <?php if($value == 'newline') echo 'class="overlay_external_newline"';?>>
				<td><input type="text" name="overlay_ajax[<?php echo $key;?>][selector]" value="<?php if($value != 'newline') echo $value['selector'];?>" /> <sup>*1)</sup></td>
				<td><input type="text" name="overlay_ajax[<?php echo $key;?>][path]" value="<?php if($value != 'newline') echo $value['path']; ?>" /></td>
				<td>
					<select name="overlay_ajax[<?php echo $key;?>][widget]">
						<option value="overlay_default">--select a Widget--</option>
						<?php   
						if(is_array($this->overlay_widgets)) {
							foreach($this->overlay_widgets as $widget_id => $widget_value) {
								?><option value="<?php echo $widget_id; ?>" <?php if($value['widget'] == $widget_id) echo ' selected'; ?> title="<?php echo strip_tags($widget_value['description']); ?>"><?php echo $widget_value['name']; ?></option><?php
							}
						}
						?>
					</select>
				</td>
				<td <?php if($value == 'newline') echo 'class="newline"'; ?>>
					<a href="#delete_line" name="delete_line" class="overlay_delete_external_page" rel="#yesno"  <?php if($value == 'newline') echo 'style="display:none"'; ?>>delete line</a> 
				</td>
			</tr>
		<?php
	} // external_page_line
	
	/**
	* get all widgets form the overlay sidebar
	* @author David Hohl <info@fishme.de>
	* @version 0.9.3.1
	* @todo get user setting Title
	* @changlog 
	*		0.9.3.1
	*			- handle call without widgets
	*/
	function get_overlay_sidebar_widgets() {
		$sidebars_widgets = wp_get_sidebars_widgets();
	    if(is_array($sidebars_widgets) && $sidebars_widgets[$this->sidebar]) {
			foreach($sidebars_widgets[$this->sidebar] as $key => $value) {
				$this->overlay_widgets[$this->wp_registered_widgets[$value]['id']] = array('name'=>$this->wp_registered_widgets[$value]['name'], 'description'=> $this->wp_registered_widgets[$value]['description']);
			}
	    }
	} // get_overlay_sidebar_widgets
	
	/**
	* get overlay setting - external_settings (for the widgetloader)
	* @author David Hohl <info@fishme.de>
	* @version 0.9.2
	* @return array external_settings
	*/
	function get_external_pages() {
		return $this->get_settings('external_settings');
	} // get_external_pages
	
	/**
	* check if widget in use 
	* @author David Hohl <info@fishme.de>
	* @version 0.9.2
	* @param string $widget widgetname
	* @return boolean found widget?
	*/
	function get_external_page($widget) {
		$widgets = $this->get_external_pages();
		foreach($widgets as $key => $value) {
			if($value['widget'] == $widget) return $value;
		}
		return false;
	} // get_external_page 
	
	/**
	* save overlay settings - generatre js and css file    
	* @author David Hohl <info@fishme.de>
	* @version 0.9.3
	* @changelog 
	*			0.9.3
	*		 	- add effectselection
	*			- add background-color
	*			- remove selection autoresize
	*			- add own styles
	*			0.9.2
	*			- add function
	*/
	function save_settings() {
		
		update_option('overlay_theme', $_POST['overlay_theme']);
	    update_option('overlay_width', $_POST['overlay_width']);
	    update_option('overlay_height', $_POST['overlay_height']);
	    update_option('overlay_image_auto_resize', $_POST['overlay_image_auto_resize']);
	    update_option('overlay_animation', serialize($_POST['overlay_animation']));
		unset($_POST['overlay_ajax'][0]); 
		
		if(is_array($_POST['overlay_ajax'])) {
			update_option('external_settings', serialize($_POST['overlay_ajax']));
		} else {
			update_option('external_settings', '');
		}    
		
		// get default Overlay JS Template
		ob_start();
		include_once(WP_PLUGIN_DIR . '/overlay/overlay_js_template.php');
		$overlay_js_template = ob_get_contents();
		ob_end_clean();

		if($animation_default_settings = get_option('overlay_default_animation')) {
			if(!is_array($animation_default_settings)) 
				$animation_default_settings = unserialize($animation_default_settings);
		}

		// since 0.9.3 - select effect style
		if($_POST['overlay_animation']['effect'] && $_POST['overlay_animation']['effect'] != 'no_effect') {
			$js_options .= "effect: '" . $_POST['overlay_animation']['effect'] . "', ";  
		}
		
		// add animation effects (options)
		if($_POST['overlay_animation']['closeonEsc']) {
			$js_options .= ' closeOnEsc: true';
		} else {
			$js_options .= ' closeOnEsc: '. $this->get_js_value($animation_default_settings['closeonEsc']);
		}

		if($_POST['overlay_animation']['closeonClick']) {
			$js_options .= ', closeOnClick: true';
		} else {
			$js_options .= ', closeOnClick: '. $this->get_js_value($animation_default_settings['closeonClick']);
		}

		if($_POST['overlay_animation']['load']) {
			$js_options .= ', load: true';
		} else {
			$js_options .= ', load: ' . $this->get_js_value($animation_default_settings['load']);
		}

		if($_POST['overlay_animation']['fixed']) {
			$js_options .= ', fixed: true';
		} else {
			$js_options .= ', fixed: ' . $this->get_js_value($animation_default_settings['fixed']);
		}

		if($_POST['overlay_animation']['speed']) {
			if(is_numeric($animation_default_settings['speed'])) {
				$js_options .= ", speed: " .  $_POST['overlay_animation']['speed'];
			} else {
				$js_options .= ", speed: '" .  $_POST['overlay_animation']['speed'] ."'";	
			}
		} else {
			$js_options .= ", speed: '". $animation_default_settings['speed'] ."'";
		}		

		if($_POST['overlay_animation']['top']) {
			$js_options .= ", top: '".  $_POST['overlay_animation']['top'] ."'";
		} else {
			$js_options .= ", top: '". $animation_default_settings['top'] ."'";
		}

		if($_POST['overlay_animation']['mask_state'] && $_POST['overlay_animation']['mask']) {
			$js_options .= ", mask: '" .  $_POST['overlay_animation']['mask'] . "'";
		} 	

		if($_POST['overlay_width']) {
			$overlay_js_template = str_replace('##WIDTH##', str_replace('%','',str_replace('px','',$_POST['overlay_width'])),$overlay_js_template);
		}

		$ajax_call_url = get_overlay_plugin_url().'ajax/call.php';
		$overlay_js_template = str_replace('##AJAX_CALL_URL##', $ajax_call_url, $overlay_js_template);
		
		// init widgetloader
		$widget_loader_js = '';
		$external_pages = $this->get_external_pages();
		if(is_array($external_pages)) {
			$widget_loader_js = 'var overlay_widgetloader = {';
			foreach($external_pages as $key => $value) {
				$widget_loader_js .= "'" . $value['selector'] . "'  : '" . $ajax_call_url .'?tid=' . time() . '&widget=' . $value['widget'] . "',";
			}
			$widget_loader_js .= "};";
		}
		
		$overlay_js_template = str_replace('##WIDGETLOADER##', $widget_loader_js, $overlay_js_template);
		
		// rewrite overlay JS Loader
		$overlay_js_template = str_replace('##OPTIONS##', $js_options .', ', $overlay_js_template);
		$overlay_js_template = str_replace('##WIDGETLOADER_OPTIONS##', $js_options .', ', $overlay_js_template);
		$overlayJS = WP_PLUGIN_DIR . "/overlay/js/overlay.js";
		if(file_exists($overlayJS)) {
			$fh = fopen($overlayJS, 'w');
			fwrite($fh, $overlay_js_template);
			fclose($fh);	
		} else {
			echo '<b>File does not exists: ' . $overlayJS . '</b>';
		}

		// get default Overlay CSS Template
		ob_start();
		include_once(WP_PLUGIN_DIR . '/overlay/overlay_css_template.php');
		$overlay_css_template = ob_get_contents();
		ob_end_clean();


		if($_POST['overlay_width']) {
			$overlay_css_template = str_replace('##WIDTH##', str_replace('%','',str_replace('px','',$_POST['overlay_width'])),$overlay_css_template);
		}

		if($_POST['overlay_theme']) { 
			$theme_background = '';                        
			if($_POST['overlay_theme'] != 'own') {
				$theme_background = 'background-image:url(../images/'.$_POST['overlay_theme'].'.png);';	
			} else {
				if($_POST['overlay_animation']['overlay_background_color']) {
					$theme_background = 'background-color: ' . $_POST['overlay_animation']['overlay_background_color'] . ';';	
				}
			}
			$overlay_css_template = str_replace('##THEME##', $theme_background,$overlay_css_template);
		}

		// rewrite overlay CSS
		$overlayCSS = WP_PLUGIN_DIR . "/overlay/styles/overlay.css";
		if(file_exists($overlayCSS)) {    
			$fh = fopen($overlayCSS, 'w');
			fwrite($fh, $overlay_css_template);
			fclose($fh);	
		} else {
			echo '<b>File does not exists: ' . $overlayCSS . '</b>';
		}
	} // save_settings
	
	function get_settings($value) {
		if($settings = get_option($value)) {
			if(!is_array($settings)) 
				$settings = unserialize($settings);
		}
		return $settings;
	} // get_settings
} // overlayLib
?>