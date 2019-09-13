jQuery(document).ready(function() {
	jQuery('#tabs').tabs();
	jQuery('.timepicker').timepicker({ 'scrollDefault': 'now' });
	jQuery('#create_linked_team').on('click', function(e) {
		e.preventDefault();
		var user_id = jQuery(this).data('uid');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_create_team_from_admin',
			'user_id' : user_id,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#create_linked_team').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#create_linked_team').prop('disabled', false);
				alert(localisation.teams.link_error);
			} else {
				spinner.hide();
				jQuery('#create_linked_team').prop('disabled', false);
				location.reload();
			}
		});	
	});
	jQuery('.cancel-colorbox').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	})
	jQuery('#reset-cqpim').on('click', function(e){
		e.preventDefault();
			jQuery.colorbox({
				'maxWidth':'95%',
				'inline': true,
				'href': '#reset_cqpim',							
				'opacity': '0.5',	
			});	
			jQuery.colorbox.resize();	
	});
	jQuery('.reset-cqpim-conf').on('click', function(e) {
		e.preventDefault();
		jQuery.colorbox.close();
		var data = {
			'action' : 'pto_remove_all_data',
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('.reset-cqpim-conf').prop('disabled', true);
			},
		}).done(function(response){
			location.replace(response.redirect);
		});	
	});
	jQuery('.remove_logo').on('click', function(e) {
		e.preventDefault();
		var type = jQuery(this).data('type');		
		var data = {
			'action' : 'pto_remove_logo',
			'type' : type,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('#cqpim_overlay').show();
			},
		}).done(function(response){
			location.reload()
		});	
	});
	jQuery('.pto_update_details').on('click', function(e) {
		e.preventDefault();
		var team = jQuery('#team_id').val();
		var type = jQuery(this).data('type');
		var name = jQuery('#team_name').val();
		var email = jQuery('#team_email').val();
		var phone = jQuery('#team_telephone').val();
		var job = jQuery('#team_job').val();
		var photo = jQuery('#upload_attachment_ids').val();
		var password = jQuery('#password').val();
		var password2 = jQuery('#password2').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_update_team_profile',
			'team' : team,
			'type' : type,
			'name' : name,
			'email' : email,
			'phone' : phone,
			'job' : job,
			'photo' : photo,
			'password' : password,
			'password2' : password2
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.pto_update_details').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				alert(response.message);
				jQuery('.pto_update_details').prop('disabled', false);
			} else {
				spinner.hide();
				alert(response.message);
				jQuery('.pto_update_details').prop('disabled', false);
				location.reload();
			}
		});	
	});
	jQuery('.pto_remove_current_photo').on('click', function(e) {
		e.preventDefault();
		var team = jQuery('#team_id').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_remove_current_photo',
			'team' : team
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.pto_remove_current_photo').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				alert(response.message);
				jQuery('.pto_remove_current_photo').prop('disabled', false);
			} else {
				spinner.hide();
				alert(response.message);
				jQuery('.pto_remove_current_photo').prop('disabled', false);
				location.reload();
			}
		});	
	});
	jQuery('.cqpim_wc_repeater').repeater();
	
	
	
	
	
	jQuery('#add_rec_inv').click(function(e) {
		e.preventDefault();
		var client_id = jQuery(this).val();
		var title = jQuery('#rec-inv-title').val();
		var start = jQuery('#rec-inv-start').val();
		var end = jQuery('#rec-inv-end').val();
		var frequency = jQuery('#rec-inv-frequency').val();
		var status = jQuery('#rec-inv-status').val();
		var contact = jQuery('#client_contact_select').val();
		var spinner = jQuery('#cqpim_overlay');
		var items = jQuery('input[name^="ngroup-a"]').map(function(){return jQuery(this).val();}).get();
		if(jQuery('#rec-inv-auto').is(':checked')) {
			auto = 1;
		} else {
			auto = 0
		}
		if(jQuery('#rec-inv-partial').is(':checked')) {
			partial = 1;
		} else {
			partial = 0
		}
		var data = {
			'action' : 'pto_add_new_recurring_invoice',
			'client_id' : client_id,
			'title' : title,
			'start' : start,
			'end' : end,
			'frequency' : frequency,
			'status' : status,
			'contact' : contact,
			'auto' : auto,
			'items' : items,
			'partial' : partial,
		};
		if(title && frequency) {
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('#add_rec_inv').prop('disabled', true);
					jQuery.colorbox.resize();
				},
			}).always(function(response){
				console.log(response);
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('#add_rec_inv').prop('disabled', false);
					jQuery('.rec-inv-messages').html(response.message);
					jQuery.colorbox.resize();
				} else {
					spinner.hide();
					jQuery('#add_rec_inv').prop('disabled', false);
					jQuery('.rec-inv-messages').html(response.message);
					jQuery.colorbox.resize();
					location.reload();
				}
			});
		} else {
			jQuery('.rec-inv-messages').html('<div class="cqpim-alert cqpim-alert-danger alert-display">You must enter a title and a frequency.</div>');
			jQuery.colorbox.resize();
		}
	});
	// Delete Rec Invoice
	jQuery('.delete_rec').click(function(e) {
		e.preventDefault();
		var client_id = jQuery(this).data('client');
		var key = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_recurring_invoice',
			'client_id' : client_id,
			'key' : key,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.delete_task').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
			} else {
				location.reload();
			}
		});
	});
	// Open Edit Rec Inv Colorbox
	jQuery('.edit_rec').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).val();
		jQuery.colorbox({
			'maxWidth':'95%',
			'inline': true,
			'fixed': true,
			'href': '#edit-recurring-invoice-' + key,	
			'opacity': '0.5',
		});	
	});	
	// Edit Rec Inv
	jQuery('.edit-rec-inv-btn').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).data('key');
		var client_id = jQuery(this).val();
		var title = jQuery('#rec-inv-title-' + key).val();
		var start = jQuery('#rec-inv-start-' + key).val();
		var end = jQuery('#rec-inv-end-' + key).val();
		var frequency = jQuery('#rec-inv-frequency-' + key).val();
		var status = jQuery('#rec-inv-status-' + key).val();
		var contact = jQuery('#client_contact_select_' + key).val();
		var spinner = jQuery('#cqpim_overlay');
		var items = jQuery('input[name^="group' + key + '-a"]').map(function(){return jQuery(this).val();}).get();
		if(jQuery('#rec-inv-auto-' + key).is(':checked')) {
			auto = 1;
		} else {
			auto = 0
		}
		if(jQuery('#rec-inv-partial-' + key).is(':checked')) {
			partial = 1;
		} else {
			partial = 0
		}
		var data = {
			'action' : 'pto_edit_recurring_invoice',
			'key' : key,
			'client_id' : client_id,
			'title' : title,
			'start' : start,
			'end' : end,
			'frequency' : frequency,
			'status' : status,
			'contact' : contact,
			'auto' : auto,
			'items' : items,
			'partial' : partial,
		};
		if(title && frequency) {
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('.edit-rec-inv-btn').prop('disabled', true);
					jQuery.colorbox.resize();
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('.edit-rec-inv-btn').prop('disabled', false);
					jQuery('.edit-inv-messages').html(response.message);
					jQuery.colorbox.resize();
				} else {
					spinner.hide();
					jQuery('.edit-rec-inv-btn').prop('disabled', false);
					jQuery('.edit-inv-messages').html(response.message);
					jQuery.colorbox.resize();
					location.reload();
				}
			});
		} else {
			jQuery('.rec-inv-messages').html('<div class="cqpim-alert cqpim-alert-danger alert-display">You must enter a title and a frequency.</div>');
			jQuery.colorbox.resize();
		}
	});
});