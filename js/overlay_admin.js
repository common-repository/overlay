// @author David HOHL <www.fishme.de>
// @version 0.9.2
//				- add external page settings
$(document).ready(function(){
	$('.overlay_delete_external_page').live('click',function() {
		var d = $(this);
		var api = $(this).overlay({
			mask: {
				color: '#B2BEB5',
				loadSpeed: 200,
				opacity: 0.9
			},
			closeOnClick: false,
			load: true
		});
		var buttons = $("#yesno button").click(function(e) {
			var feedback = buttons.index(this) === 0;
			if(feedback) {
				d.parent('td').parent('tr').remove();
			}	
		});
	});
	
	$('.newline').append('<a href="#add_newline" class="overlay_add_external_page" name="add_newline">add new line</a>');
	var ocounter = $('#new_line_table').attr('rel');
	$('.overlay_add_external_page').live('click', function() {
		var e = $('.overlay_external_newline').clone().attr({'class':'generate_line_'+ocounter});
		var path = '';
		var selector = '';
		var widget = '';
		$('.overlay_external_newline input').each(function() {
			if( $(this).attr('name') == 'overlay_ajax[0][selector]') {
				selector = $(this).attr('value');
			}
			if( $(this).attr('name') == 'overlay_ajax[0][path]') {
				path = $(this).attr('value');
			}
		});
		innerhtml = e.html();
		e.html(innerhtml.replace(/[0]/gi,ocounter));
		
		e.appendTo('#external_page tbody');
			$('.generate_line_'+ocounter +' input').each(function() {
				if( $(this).attr('name') == 'overlay_ajax['+ocounter+'][selector]') {
					$(this).attr('value',selector);
				}
				if( $(this).attr('name') == 'overlay_ajax['+ocounter+'][path]') {
					$(this).attr('value',path);
				}
			});
		$(e).children('.newline').children('.overlay_add_external_page').remove();
		$(e).children('.newline').children('.overlay_delete_external_page').show();
		$('.overlay_external_newline input').attr('value','');	
		ocounter++;	
	});
	function _log(msg) {
	    if (window.console && window.console.log)
	        window.console.log(msg);
	}
});