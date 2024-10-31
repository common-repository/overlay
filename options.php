<?php
/*
Description: Option page
Features:
	- select theme
	- background mask
Author: David Hohl
Author URI: http://www.fishme.de
Version:  
0.9.3
	- remove auto resize  
	- add own backgroundcolor
	- select effect      
	- add 4 new Themes
0.9.2
	- add Widget selection
	- new Class Structur (but not php5 for old server)
0.9.1 
	- add theme selection
	- add background mask
	- add load external page
	- add basic animations
*/ 

if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

$overlay_current_version = '0.9.3';
// all settings
$overlay_theme = array(
		'transparent'	   	=> 'default value - transparent', // default form jquery Tools
		'gray'				=> 'gray',		// default form jquery Tools
		'petrol'			=> 'petrol',	// default from jquery Tools
		'orange'			=> 'orange', 			// from fishme.de   0.9.1
		'darkred'			=> 'darkred',			// from fishme.de   0.9.1
		'gradientorange'	=> 'gradient orange',	// from fishme.de   0.9.1
		'greyyellow'		=> 'grey yellow',		// from fishme.de   0.9.3
		'greyblue'			=> 'grey blue',			// from fishme.de   0.9.3
		'greygreen'			=> 'grey green',			// from fishme.de   0.9.3
		'greyorange'		=> 'grey orange',			// from fishme.de   0.9.3
		'own'	 			=> 'own backgroundcolor', // own theme		
	);
$overlay_effect = array(
		'apple'				=> 'Apple fadeout effect',
		'no_effect'			=> 'without effect'
   	);

include_once(WP_PLUGIN_DIR . '/overlay/lib/overlayLib.php');
$overlayLib = new overlayLib();
$overlayLib->init_overlay();  

// progress - save 
if ($_POST['overlay-settings-save']) {
    $overlayLib->save_settings();
	echo '<div id="message" class="updated below-h2"><p>Save was successfully!</p></div>';
}
if($_POST['overlay_debug']) {?> 
	<h1>DEBUG</h1>
	<strong>This issue contains no security information</strong><br />
	Please copy this and mail to: <a href="mailto:overlay@fishme.de">overlay@fishme.de</a><br />  
	additional - add the testurl (from your website) (http://www.yourblog.com)
	<div id="message" class="updated below-h2">
		<table>
			<tr>
				 <td>Date</td>
				 <td><?php echo date('d.M.Y H:i',time());?></td>
			</tr>
			 
			<tr>
				 <td>SERVER_SOFTWARE</td>
				 <td><?php echo $_SERVER['SERVER_SOFTWARE']?></td>
			</tr>			
			<tr>
				 <td>REQUEST_METHOD</td>
				 <td><?php echo $_SERVER['REQUEST_METHOD']?></td>
			</tr>
			<tr>
				 <td>SERVER_PROTOCOL</td>
				 <td><?php echo $_SERVER['SERVER_PROTOCOL']?></td>
			</tr>
			<tr>
				<td colspan="2">
					<?php
					echo '<pre>';
				    print_r($_POST);
				    echo '</pre>';
					?>
				</td>
		</table>
	</div>
	<?php
}
?>
<div class="wrap" id="overlay_options">
	<h2>Global Settings Overlay Settings</h2>
	<form action="" method="post" name="overlay-globalsettings">
		<h3>Default Settings</h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">fixed Width</th>
					<td>
						<input type="text" name="overlay_width" value="<? echo get_option('overlay_width'); ?>" /> <strong>(Required)</strong>
						<p> Specifies the width of the overlay box.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">fixed Height</th>
					<td>
						<input type="text" name="overlay_height" value="<? echo get_option('overlay_height'); ?>" />  <strong>(Required)</strong> 
						<p>Specifies the maximum image height</p>
					</td>
				</tr> 
				<tr valign="top">
					<th scope="row">Debugmode</th>
					<td>
						<input type="checkbox" name="overlay_debug"  />
						<p>If you have some problems with the overlay, activate the debugmode and send the output (on the top after save) to <a href="mailto:overlay@fishme.de">overlay@fishme.de</a> or <a href="http://fishme.wufoo.com/forms/z7x4a9/" target="_blank">fill out follow form</a></p>
					</td>
				</tr>				   			
			</tbody>
		</table>
		<div class="postbox">
			<h3>Animation Settings</h3>
			<?php
			$animation_settings = $overlayLib->get_settings('overlay_animation');
			?>
			<table class="form-table">
				<tbody>
		  			<tr valign="top">
						<th scope="row">Theme</th>
						<td>
							<select name="overlay_theme">
								<?php
								$currentTheme = get_option('overlay_theme');
								foreach($overlay_theme as $key => $value) {
									?>
									<option value="<?php echo $key; ?>" <?php if($key == $currentTheme) echo ' selected'; ?>><?php echo $value; ?></option>
									<?php
								}
								?>
							</select> 
							<p class="help-description">
								select a pre-installed theme. The theme is only for the Box (backgroundImage)
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Overlay Background Color</th>
						<td>
							<input type="text" name="overlay_animation[overlay_background_color]" value="<? echo $animation_settings['overlay_background_color']; ?>" />
							<p class="help-description">
								is only in use if you select by the theme - <strong>own background color</strong>
								ex.: #000000 or black ... (RGB values (HEX) or default webcolors)<br>
								for more information have a look on <a href="http://en.wikipedia.org/wiki/List_of_colors" target="_blank">Wikipedia</a>
							</p>
						</td>
					</tr>								   
		  			<tr valign="top">
						<th scope="row">Effect</th>
						<td>
							<select name="overlay_animation[effect]">
								<?php
								foreach($overlay_effect as $key => $value) {
									?>
									<option value="<?php echo $key; ?>" <?php if($key == $animation_settings['effect']) echo ' selected'; ?>><?php echo $value; ?></option>
									<?php
								}
								?>
							</select> 
							<p class="help-description">
								select a pre-installed effect.
							</p>
						</td>
					</tr>			
					<tr valign="top">
						<th scope="row">Mask Layer State</th>
						<td>
							<input type="checkbox" name="overlay_animation[mask_state]" value="1" <?php if($animation_settings['mask_state'] == 1) echo 'checked';?> />
							<p class="help-description">Set the background color responsible for dimming the page over with the layer is displayed to emphasize the overlay.</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Mask Layer Color</th>
						<td>
							<input type="text" name="overlay_animation[mask]" value="<? echo $animation_settings['mask']; ?>" />
							<p class="help-description">
								ex.: #000000 or black ... (RGB values (HEX) or default webcolors)<br>
								for more information have a look on <a href="http://en.wikipedia.org/wiki/List_of_colors" target="_blank">Wikipedia</a> 
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">close on Esc</th>
						<td><input type="checkbox" name="overlay_animation[closeonEsc]" value="1" <?php if($animation_settings['closeonEsc'] == 1) echo 'checked';?> />
						<p class="help-description">Check to close the overlay by pressing ESC</p>	
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">close on Click</th>
						<td>
							<input type="checkbox" name="overlay_animation[closeonClick]" value="1" <?php if($animation_settings['closeonClick'] == 1) echo 'checked';?> />
							<p class="help-description">Check to close the overlay with a click anywhere on the overlay</p>   
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">load by Site load</th>
						<td>
							<input type="checkbox" name="overlay_animation[load]" value="1" <?php if($animation_settings['load'] == 1) echo 'checked';?> />
							<p class="help-description">Check to automatically show the overlay. Might come handy for pages with single images.</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">fixed position</th>
						<td>
							<input type="checkbox" name="overlay_animation[fixed]" value="1" <?php if($animation_settings['fixed'] == 1) echo 'checked';?> />
							<p class="help-description">
								whether overlay stays in the same position while the screen is scrolled. This is the default behaviour for all browsers except IE6 and below. IE6 does not support fixed positioning. If this property is set to false then the overlay is positioned in relationship to the document so that when the screen is scrolled then the overlay moves along with the document.
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Speed</th>
						<td><input type="text" name="overlay_animation[speed]" value="<?php echo $animation_settings['speed'];?>" />
							<p class="help-description">Set the speed of the animation in milliseconds. Available options: slow, normal (default), fast or custom (1000 equals 1 second)</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Top</th>
						<td><input type="text" name="overlay_animation[top]" value="<?php echo $animation_settings['top'];?>" />
							<p class="help-description">
								Specifies how far from the top edge of the screen the overlay should be placed. <br />Acceptable values are an integer number specifying a distance in pixels, a string (such as '15%') <br />specifying a percentage value or "center" in which case the overlay is vertically centered. Percentage values work consistently at different screen resolutions.
							</p>
						</td>
					</tr>				
				</tbody>
			</table> 
		</div>
		<div class="postbox">
			<h4>External Page Settings</h4>
			<p>
				* First of all, check the "Overlay Sidebar" in your <a href="/wp-admin/widgets.php">Widgetsettings</a>
			</p>
			<div class="inside"><?php
				$external_settings = $overlayLib->get_settings('external_settings');
				?>
				<table class="form-table" id="external_page" >
					
					<tbody>
						<tr valign="top">
							<th scope="row">Selector or class</th>
							<th scope="row">External Page Path (not in use)</th>
							<th scope="row">Widget *</th>
							<th scope="row"> </th>
						</tr>
						<?php
						if(is_array($external_settings)) {
							foreach($external_settings as $key => $value) {
								$overlayLib->external_page_line($key,$value);
							}
						} 
						?>
					</tbody>
				</table>
				<table class="form-table" id="new_line_table" rel="<?echo $key+1;?>">
					<tbody>
						<?php $overlayLib->external_page_line('0','newline'); ?>
					</tbody>
				</table>
				<p class="help-description">
					*1) use jquery Selector ex.:  by CSS Class - .myclass or by ID - #myid for more information about the jquery selectors <a href="http://api.jquery.com/category/selectors/" target="_blank">jQuery</a>
				</p>
			</div>
		</div>
		<p class="submit">
			<input type="submit" name="overlay-settings-save" value="Save all">
		</p>
	</form>
	<div class="modal" id="yesno">
		<h2>Delete Line</h2>
		<p>
			Would you really want to delete the line?
		</p>
		<!-- yes/no buttons -->
		<input type="hidden" value="0" id="modal_delete_line" />
		<p>
			<button class="close" rel="yes"> Yes </button>
			<button class="close" rel="no"> No </button>
		</p>
	</div>
</div>