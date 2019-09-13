jQuery(document).ready(function() {
	jQuery('#income_control_date').live('change', function(e) {
		e.preventDefault();
		var date = jQuery('#income_control_date').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_edit_income_graph',
			'date' : date,
			'type' : 'date',
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
			},
		}).done(function(response){
			spinner.hide();
			location.reload();	
		});
	});	
});