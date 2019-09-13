jQuery(document).ready(function() {
	jQuery('#quote_client').live('change', function(e) {
		e.preventDefault();
		var client_id = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_update_client_contacts',
			'client_id' : client_id
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery.colorbox.resize();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
			} else {
				spinner.hide();
				jQuery('#client_contact').prop('disabled', false);
				jQuery('#client_contact').html(response.contacts);
				jQuery.colorbox.resize();
			}
		});
	});
	jQuery('#add_quote_element').click(function(e) {
		e.preventDefault();
		var title = jQuery('#quote_element_title').val();
		var start = jQuery('#quote_element_start').val();
		var deadline = jQuery('#quote_element_finish').val();
		var cost = jQuery('#quote_element_cost').val();
		var milestone_id = jQuery('#add_milestone_id').val();
		var milestone_order = jQuery('#add_milestone_order').val();
		var post_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_add_step_to_quote',
			'title' : title,
			'start' : start,
			'deadline' : deadline,
			'ID' : post_id,
			'cost' : cost,
			'milestone_id' : milestone_id,
			'weight' : milestone_order,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#add_quote_element').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#add_quote_element').prop('disabled', false);
				alert(response.errors);
			} else {
				spinner.hide();
				jQuery('#add_quote_element').prop('disabled', false);
				location.reload();
			}
		});
	});
	jQuery('button.save-task').on('click', function(e){
		e.preventDefault();
		var id = jQuery(this).val();
		var weight = jQuery(this).siblings('#task_weight_' + id).val();
		var title = jQuery(this).siblings('#task_title_' + id).val();
		var description = jQuery(this).siblings('#task_description_' + id).val();
		var start = jQuery(this).siblings('#task_start_' + id).val();
		var ms_id = jQuery(this).siblings('#task_milestone_id_' + id).val();
		var project_id = '0';
		var deadline = jQuery(this).siblings('#task_finish_' + id).val();
		var task_time = jQuery(this).siblings('#task_time_' + id).val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_create_task',
			'task_weight' : weight,
			'task_finish' : deadline,
			'task_project_id' : project_id,
			'task_title' : title,
			'task_milestone_id' : ms_id,
			'description' : description,
			'start' : start,
			'task_time' : task_time
		};
		if(title) {
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('button.save-task').prop('disabled', true);
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('button.save-task').prop('disabled', false);
					alert(response.errors);
				} else {
					spinner.hide();
					jQuery('button.save-task').prop('disabled', false);
					location.reload();
				}
			});		
		} else {
			jQuery('#task-messages-' + ms_id).html('<div class="cqpim-alert cqpim-alert-danger alert-display">Title is required</div>');
		}
	});
	jQuery('button.save-subtask').on('click', function(e){
		e.preventDefault();
		var id = jQuery(this).val();
		var weight = jQuery(this).siblings('#subtask_weight_' + id).val();
		var parent = jQuery(this).siblings('#subtask_parent_id_' + id).val();
		var title = jQuery(this).siblings('#subtask_title_' + id).val();
		var description = jQuery(this).siblings('#subtask_description_' + id).val();
		var start = jQuery(this).siblings('#subtask_start_' + id).val();
		var ms_id = jQuery(this).siblings('#subtask_milestone_id_' + id).val();
		var project_id = '0';
		var deadline = jQuery(this).siblings('#subtask_finish_' + id).val();
		var task_time = jQuery(this).siblings('#subtask_time_' + id).val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_create_subtask',
			'task_weight' : weight,
			'parent' : parent,
			'task_finish' : deadline,
			'task_project_id' : project_id,
			'task_title' : title,
			'task_milestone_id' : ms_id,
			'description' : description,
			'start' : start,
			'task_time' : task_time
		};
		if(title) {
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('button.save-task').prop('disabled', true);
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('button.save-task').prop('disabled', false);
					alert(response.errors);
				} else {
					spinner.hide();
					jQuery('button.save-task').prop('disabled', false);
					location.reload();
				}
			});		
		} else {
			jQuery('#task-messages-' + id).html('<div class="cqpim-alert cqpim-alert-danger alert-display">Title is required</div>');
		}
	});
	jQuery('button.delete_task').on('click', function(e){
		e.preventDefault();
		var task_id = jQuery(this).val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_task',
			'task_id' : task_id,
		};
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('button.delete_task').prop('disabled', true);
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('button.delete_task').prop('disabled', false);
					alert(response.errors);
				} else {
					spinner.hide();
					jQuery('button.delete_task').prop('disabled', false);
					location.reload();
				}
			});		
	});
	jQuery('button.update-task').on('click', function(e){
		e.preventDefault();
		var task_id = jQuery(this).val();
		var title = jQuery('#task_title_' + task_id).val();
		var deadline = jQuery('#task_finish_' + task_id).val();
		var start = jQuery('#task_start_' + task_id).val();
		var description = jQuery('#task_description_' + task_id).val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_update_task',
			'task_id' : task_id,
			'title' : title,
			'deadline' : deadline,
			'start' : start,
			'description' : description
		};
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('button.update-task').prop('disabled', true);
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('button.update-task').prop('disabled', false);
					alert(response.errors);
				} else {
					spinner.hide();
					jQuery('button.update-task').prop('disabled', false);
					location.reload();
				}
			});		
	});
	jQuery('#send_quote').click(function(e) {
		e.preventDefault();
		jQuery('#messages').html('');
		var quote_id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_process_quote_emails',
			'quote_id' : quote_id
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#send_quote').prop('disabled', true);
			},
		}).always(function(response) {
		console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#send_quote').prop('disabled', false);
				jQuery('#messages').html(response.errors);
			} else {
				spinner.hide();
				jQuery('#send_quote').prop('disabled', false);
				jQuery('#messages').html(response.message);
				location.reload();
			}
		});
	});
	jQuery('#apply-template-action').click(function(e) {
		e.preventDefault();
		jQuery('#apply-template-messages').html('');
		var quote_id = jQuery(this).val();
		var type = jQuery(this).attr('data-type');
		var template = jQuery('#template_choice').val();
		var hid = jQuery(this).data('hid');
		var hwe = jQuery(this).data('hwe');
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#apply-template-messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_apply_template',
			'quote_id' : quote_id,
			'type' : type,
			'template' : template,
			'hid' : hid,
			'hwe' : hwe
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery.colorbox.resize();
				jQuery('#apply-template-action').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);		
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#apply-template-action').prop('disabled', false);
				jQuery(messages).html('<p>' + response.errors + '</p>');
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('#apply-template-action').prop('disabled', false);
				jQuery(messages).html('<p>' + response.messages + '</p>');
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('#clear-all-action').click(function(e) {
		e.preventDefault();
		jQuery('#apply-template-messages').html('');
		var quote_id = jQuery(this).val();
		var type = jQuery(this).attr('data-type');
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#clear-all-messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_clear_all_action',
			'quote_id' : quote_id,
			'type' : type
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery.colorbox.resize();
				jQuery('#clear-all-action').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);		
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#clear-all-action').prop('disabled', false);
				jQuery(messages).html('<p>' + response.errors + '</p>');
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('#clear-all-action').prop('disabled', false);
				jQuery(messages).html('<p>' + response.messages + '</p>');
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('.convert_confirm').click(function(e) {
		e.preventDefault();
		jQuery('#convert-error').html('');
		var quote_id = jQuery(this).val();
		var messages = jQuery('#convert-error');
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_manual_quote_convert',
			'quote_id' : quote_id,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery.colorbox.resize();
				jQuery('.convert_confirm').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);		
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.convert_confirm').prop('disabled', false);
				jQuery(messages).html(response.message);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('.convert_confirm').prop('disabled', false);
				jQuery(messages).html(response.message);
				jQuery.colorbox.resize();
				var url = response.url.replace("&amp;", "&");
				console.log(url);
				window.location.replace(url);
			}
		});
	});
});