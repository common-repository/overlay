// overlay Loader
// Author: David Hohl
// Version: 0.9.3.1
// License: Dual licensed under MIT or GPL licenses.
$(document).ready(function(){
	var overlay_counter = 1;
	var overlay_call = '##AJAX_CALL_URL##';
	$("a img[rel], img[rel]").each(function() {
		$(this).attr('rel','#'+$(this).attr('rel') + '_' + overlay_counter);
		var overlay_id = $(this).attr('rel');
		overlay_id = overlay_id.replace('#','');
		if($('#'+overlay_id).length == 0) {
			$('body').append($('<div />').attr({'class': 'apple_overlay', 'id': overlay_id}).html('<div class="ovelay_widgetloader"></div>'));
		}
		overlay_counter++;	
	});
	$("img[rel]").overlay({
			##OPTIONS## 
			onBeforeLoad: function() {
				var wrap = this.getOverlay().find(".ovelay_widgetloader");
				var overlay_id = this.getTrigger().attr('rel'); 
				if(!overlay_id) {
					return false;
				}                                            
				var image_post_info = overlay_id.split('_');                                                                  
				if(image_post_info[2]) {
					wrap.load(overlay_call + '?tid=1&image=' + escape(this.getTrigger().attr("src")) +'&pid=' + image_post_info[2]);
				} 
			}
	});
	
	// widget loader
	##WIDGETLOADER##         
	if(overlay_widgetloader) {
		for(value in overlay_widgetloader) {
			$(value).attr({'href': overlay_widgetloader[ value ],'rel':'#overlay_widgetloader'}).addClass('overlayloader');
		}		
	}
	$("a.overlayloader[rel]").each(function() {
		var overlay_id = $(this).attr('rel');
		overlay_id = overlay_id.replace('#','');
		if($('#'+overlay_id).length == 0) {
			$('body').append($('<div />').attr({'class': 'apple_overlay', 'id': overlay_id}).html('<div class="ovelay_widgetloader"></div>'));
		}
		overlay_counter++;	
	});
	$("a.overlayloader[rel]").overlay({
			##WIDGETLOADER_OPTIONS## 
			onBeforeLoad: function() {
				var wrap = this.getOverlay().find(".ovelay_widgetloader");
				wrap.load(this.getTrigger().attr("href"));
			}
	});
});