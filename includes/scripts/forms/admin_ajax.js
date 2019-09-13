jQuery(document).ready(function() {
	jQuery('a[data-field-type="time"]').remove();
	jQuery('a[data-field-type="price"]').remove();
	jQuery('a[data-field-type="address"]').remove();
	jQuery('a[data-field-type="section_break"]').remove();
	jQuery('.multiple-wrap').remove()
	var type = jQuery('#form_type').val();
	if(!jQuery('.subsubsub').length) {
		if(!type) {
			jQuery.colorbox({
				'maxWidth':'95%',
				'inline': true,
				'href': '#form_basics',							
				'overlayClose'  : false, 
				'escKey' : false,
				'opacity': '0.5',	
				'onLoad': function() {
					jQuery('#cboxClose').remove();
				},
			});	
			jQuery.colorbox.resize();
		}
	}
	jQuery('#edit_form_type').on('click', function(e){
		e.preventDefault();
			jQuery.colorbox({
				'maxWidth':'95%',
				'inline': true,
				'href': '#form_basics',							
				'opacity': '0.5',	
			});	
			jQuery.colorbox.resize();	
	});
	jQuery('button.save-basics').click(function(e){
		e.preventDefault();
		var type = jQuery('#form_type').val();
		var title = jQuery('#form_title').val(); 
		var actitle = jQuery('#title').val();
		if(type && title || actitle) {
			jQuery.colorbox.close();
			setTimeout(function(){
				jQuery('#publish').trigger('click');
			}, 500);
		} else {
			jQuery('#basics-error').html('<span style="display:block; width:95%" class="unsent_quote">You must enter a title and choose a Type.</span><div class="clear"></div>');
			jQuery.colorbox.resize();
		}
	});
});