jQuery(document).ready(function() {
	jQuery('.menu-open').on('click', function(e) {
		e.preventDefault;
		jQuery(this).hide();
		jQuery('#cqpim-dash-sidebar').show();
		jQuery('.menu-close').show();
	});
	jQuery('.menu-close').on('click', function(e) {
		e.preventDefault;
		jQuery(this).hide();
		jQuery('#cqpim-dash-sidebar').hide();
		jQuery('.menu-open').show();
	});
	if (jQuery(window).width() > 899) {
		var height = jQuery(document).innerHeight();
		jQuery('#cqpim-dash-menu').css('height', height);
	}
	jQuery(window).resize(function() {
		if (jQuery(window).width() > 899) {
			var height = jQuery(document).innerHeight();
			jQuery('#cqpim-dash-menu').css('height', height);
		} else {
			jQuery('#cqpim-dash-menu').css('height', 'auto');		
		}
	});
	// Client Accept Quote
	jQuery('#accept_quote').click(function(e) {
		e.preventDefault();
		jQuery('#messages').html('');
		var quote_id = jQuery('#quote_id').val();
		var name = jQuery('#conf_name').val();
		var pm_name = jQuery('#pm_name').val();
		var spinner = jQuery('#overlay');
		var messages = jQuery('#messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_client_accept_quote',
			'quote_id' : quote_id,
			'name' : name,
			'pm_name' : pm_name
		};
		if(!name) {
			alert('You must enter your name');
		} else {
			jQuery.ajax({
				url: localisation.ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					// show spinner
					spinner.show();
					// disable form elements while awaiting data
					jQuery('#accept_quote').prop('disabled', true);
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					// re-enable form elements so that new enquiry can be posted
					jQuery('#accept_quote').prop('disabled', false);
					jQuery('#messages').html('<p>' + response.errors + '</p>');
				} else {
					spinner.hide();
					// re-enable form elements so that new enquiry can be posted
					jQuery('#accept_quote').prop('disabled', false);
					location.reload();
				}
			});
		}
	});
});