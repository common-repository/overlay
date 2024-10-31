// overlay Loader
// Author: David Hohl
// Version: 0.9.3
// License: Dual licensed under MIT or GPL licenses.
$(document).ready(function(){
	var overlay_counter = 1;
	var overlay_call = 'http://'+document.domain+'/wp-content/plugins/overlay/ajax/call.php';
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
			effect: 'apple',  closeOnEsc: true, closeOnClick: true, load: false, fixed: true, speed: 'normal', top: '15%', mask: '#5D8AA8',  
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
});