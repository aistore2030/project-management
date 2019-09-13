jQuery(document).ready(function() {
	jQuery('.disable_email').on('change', function(e) {
		var project_id = jQuery('#post_ID').val();
		var spinner = jQuery('#cqpim_overlay');
		var demail = jQuery(this).is(":checked");
		if(demail == true) {
			demail = 1;
		} else {
			demail = 0;
		}
		var key = jQuery(this).data('key');
		var data = {
			'action' : 'pto_update_project_team_email',
			'project_id' : project_id,
			'demail' : demail,
			'key' : key
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
			}
		});		
	});
	jQuery('.project_manager').on('change', function(e) {
		var project_id = jQuery('#post_ID').val();
		var spinner = jQuery('#cqpim_overlay');
		var pm = jQuery(this).is(":checked");
		if(pm == true) {
			pm = 1;
		} else {
			pm = 0;
		}
		var key = jQuery(this).data('key');
		var data = {
			'action' : 'pto_update_project_team_pm',
			'project_id' : project_id,
			'pm' : pm,
			'key' : key
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
			}
		});		
	});
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
	jQuery('#add_team_member_ajax').click(function(e) {
		e.preventDefault();
		var team_id = jQuery('#team_members').val();
		var pm = jQuery('#pm').is(":checked");
		if(pm == true) {
			pm = 1;
		} else {
			pm = 0;
		}
		var project_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_add_team_to_project',
			'team_id' : team_id,
			'project_id' : project_id,
			'pm' : pm
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#add_team_member_ajax').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#add_team_member_ajax').prop('disabled', false);
				jQuery('#add_team_messages').html(response.message);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('#add_team_member_ajax').prop('disabled', false);
				jQuery('#add_team_messages').html(response.message);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('.delete_team').click(function(e) {
		e.preventDefault();
		var key = jQuery(this).val();
		var team = jQuery(this).data('team');
		var project_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_remove_team_member',
			'key' : key,
			'project_id' : project_id,
			'team' : team
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.delete_team').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.delete_team').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('.delete_team').prop('disabled', false);
				location.reload();
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
		var type = 'project';
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
			'type' : type,
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
		var ms_id = jQuery(this).siblings('#task_milestone_id_' + id).val();
		var project_id = jQuery(this).siblings('#task_project_id_' + id).val();
		var owner = jQuery(this).siblings('#task_owner_' + id).val();
		var deadline = jQuery(this).siblings('#task_finish_' + id).val();
		var start = jQuery(this).siblings('#task_start_' + id).val();
		var task_time = jQuery(this).siblings('#task_time_' + id).val();
		var description = jQuery(this).siblings('#task_description_' + id).val();
		var project_post_id = jQuery('#post_ID').val();
		var type = 'project';
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_create_task',
			'task_weight' : weight,
			'task_finish' : deadline,
			'task_project_id' : project_id,
			'task_title' : title,
			'task_milestone_id' : ms_id,
			'type' : type,
			'ppid' : project_post_id,
			'owner' : owner,
			'start' : start,
			'description' : description,
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
			jQuery('#task-messages-' + ms_id).html('<p style="color:#F00">Title is required</p>');
		}
	});
	jQuery('button.save-subtask').on('click', function(e){
		e.preventDefault();
		var id = jQuery(this).val();
		var weight = jQuery(this).siblings('#task_weight_' + id).val();
		var parent = jQuery(this).siblings('#task_parent_id_' + id).val();
		var title = jQuery(this).siblings('#task_title_' + id).val();
		var description = jQuery(this).siblings('#task_description_' + id).val();
		var start = jQuery(this).siblings('#task_start_' + id).val();
		var ms_id = jQuery(this).siblings('#task_milestone_id_' + id).val();
		var project_id = jQuery(this).siblings('#task_project_id_' + id).val();
		var deadline = jQuery(this).siblings('#task_finish_' + id).val();
		var task_time = jQuery(this).siblings('#task_time_' + id).val();
		var owner = jQuery(this).siblings('#task_owner_' + id).val();
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
			'task_time' : task_time,
			'owner' : owner
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
			jQuery('#task-messages-' + id).html('<p style="color:#F00">Title is required</p>');
		}
	});
	jQuery('button.delete_task').on('click', function(e){
		e.preventDefault();
		var task_id = jQuery(this).val();
		var type = 'project';
		var project_post_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_task',
			'task_id' : task_id,
			'type' : type,
			'ppid' : project_post_id
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
	jQuery('button.item_complete').on('click', function(e){
		e.preventDefault();
		var item_id = jQuery(this).val();
		var type = jQuery(this).data('type');
		var project_post_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_mark_item_complete',
			'item_id' : item_id,
			'type' : type,
			'ppid' : project_post_id
		};
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('button.item_complete').prop('disabled', true);
				},
			}).always(function(response){
				console.log(response);
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('button.item_complete').prop('disabled', false);
					alert(response.errors);
				} else {
					spinner.hide();
					jQuery('button.item_complete').prop('disabled', false);
					location.reload();
				}
			});		
	});
	jQuery('#toggle_all_tasks').on('click', function(e){
		e.preventDefault();
		var project_post_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_toggle_complete',
			'ppid' : project_post_id
		};
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('#toggle_all_tasks').prop('disabled', true);
				},
			}).always(function(response){
				console.log(response);
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('#toggle_all_tasks').prop('disabled', false);
					alert(response.errors);
				} else {
					spinner.hide();
					jQuery('#toggle_all_tasks').prop('disabled', false);
					location.reload();
				}
			});		
	});
	jQuery('#send_contract').click(function(e) {
		e.preventDefault();
		jQuery('#messages').html('');
		var project_id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_process_contract_emails',
			'project_id' : project_id
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#send_contract').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#send_contract').prop('disabled', false);
				jQuery('#messages').html(response.errors);
			} else {
				spinner.hide();
				jQuery('#send_contract').prop('disabled', false);
				jQuery('#messages').html(response.message);
				jQuery('#publish').trigger('click');
			}
		});
	});
	jQuery('#signed_off').click(function(e) {
		e.preventDefault();
		var project_id = jQuery(this).data('id');
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_mark_project_complete',
			'project_id' : project_id
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#signed_off').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);			
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#signed_off').prop('disabled', false);
				jQuery('#messages').html(response.errors);
			} else {
				spinner.hide();
				jQuery('#signed_off').prop('disabled', false);
				jQuery('#messages').html(response.messages);
				location.reload();
			}
		});
	});
	jQuery('.save-unsigned').click(function(e) {
		e.preventDefault();
		var project_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_mark_project_incomplete',
			'project_id' : project_id
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.save-unsigned').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);			
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('.save-unsigned').prop('disabled', false);
				jQuery('#unsign-error').html(response.messages);
			} else {
				spinner.hide();
				jQuery('.save-unsigned').prop('disabled', false);
				jQuery('#unsign-error').html(response.messages);
				location.reload();
			}
		});
	});
	jQuery('#close_off').click(function(e) {
		e.preventDefault();
		var project_id = jQuery(this).data('id');
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_mark_project_closed',
			'project_id' : project_id
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#close_off').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#close_off').prop('disabled', false);
				jQuery('#messages').html(response.errors);
			} else {
				spinner.hide();
				jQuery('#close_off').prop('disabled', false);
				jQuery('#messages').html(response.messages);
				location.reload();
			}
		});
	});
	jQuery('#unclose_off').click(function(e) {
		e.preventDefault();
		var project_id = jQuery(this).data('id');
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_mark_project_open',
			'project_id' : project_id
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#unclose_off').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#unclose_off').prop('disabled', false);
				jQuery('#messages').html(response.errors);
			} else {
				spinner.hide();
				jQuery('#unclose_off').prop('disabled', false);
				jQuery('#messages').html(response.messages);
				location.reload();
			}
		});
	});
	jQuery('#add_message_ajax').click(function(e) {
		e.preventDefault();
		var visibility = jQuery('#add_message_visibility').val();
		var message = jQuery('#add_message_text').val();
		var project_id = jQuery('#post_ID').val();
		var who = jQuery('#message_who').val();
		if(jQuery('#send_to_team').is(':checked')) {
			send_to_team = 1;
		} else {
			send_to_team = 0
		}
		if(jQuery('#send_to_client').is(':checked')) {
			send_to_client = 1;
		} else {
			send_to_client = 0
		}
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_add_message_to_project',
			'visibility' : visibility,
			'message' : message,
			'project_id' : project_id,
			'who' : who,
			'send_to_team' : send_to_team,
			'send_to_client' : send_to_client
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#add_message_trigger').prop('disabled', true);
				jQuery.colorbox.resize();
			},
		}).always(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#add_message_ajax').prop('disabled', false);
				jQuery('#message_messages').html('<p>' + response.errors + '</p>');
			} else {
				spinner.hide();
				jQuery('#add_message_ajax').prop('disabled', false);
				jQuery('#message_messages').html('<p>' + response.errors + '</p>');
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('button.delete_message').click(function(e) {
		e.preventDefault();
		var project_id = jQuery('#post_ID').val();
		var key = jQuery(this).data('id');
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_project_message',
			'project_id' : project_id,
			'key' : key
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#button.delete_message').prop('disabled', true);
			},
		}).done(function(){
				location.reload();
		});
	});
	jQuery('a.time_remove').click(function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('task');
		var key = jQuery(this).data('key');
		var element = jQuery(this);
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_remove_time_entry',
			'task_id' : task_id,
			'key' : key
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('a.time_remove').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('a.time_remove').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('a.time_remove').prop('disabled', false);
				jQuery(element).parents('li').fadeOut('slow');
				jQuery(element).parents('li').remove();
			}				
		});
	});
	jQuery('#template_choice').on('change', function(e) {
		e.preventDefault();
		jQuery('#template_team_warning').hide();
		var project_id = jQuery('#post_ID').val();
		var template = jQuery('#template_choice').val();
		var spinner = jQuery('#cqpim_overlay');
		var domain = document.domain;
		var data = {
			'action' : 'pto_check_template_assignees',
			'project_id' : project_id,
			'template' : template
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
		}).always(function(response) {
			console.log(response);		
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#template_team_warning').show();
				jQuery('#template_team_warning').html(response.message);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery.colorbox.resize();
			}
		});
	});
	jQuery('#apply-template-action').click(function(e) {
		e.preventDefault();
		jQuery('#apply-template-messages').html('');
		var quote_id = jQuery(this).val();
		var type = jQuery(this).attr('data-type');
		var hid = jQuery(this).data('hid');
		var hwe = jQuery(this).data('hwe');
		var template = jQuery('#template_choice').val();
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
	jQuery('#send_deposit').click(function(e) {
		e.preventDefault();
		var project_id = jQuery('#post_ID').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_create_deposit_invoice',
			'project_id' : project_id,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#send_deposit').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#send_deposit').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('#send_deposit').prop('disabled', false);
				location.reload();
			}
		});
	});
	jQuery('.assign-all-confirm').click(function(e) {
		e.preventDefault();
		var ms = jQuery('#assign_all_ms').val();
		var assignee = jQuery('#assign_all_assignee').val()
		var notify = jQuery('#assign_all_notify').is(":checked");
		if(notify == true) {
			notify = 1;
		} else {
			notify = 0;
		}
		var project_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_assign_all_ms',
			'project_id' : project_id,
			'assignee' : assignee,
			'ms' : ms,
			'notify' : notify
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.assign-all-confirm').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.assign-all-confirm').prop('disabled', false);
				jQuery('#assign-all-message').html(response.message);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('.assign-all-confirm').prop('disabled', false);
				jQuery('#assign-all-message').html(response.message);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('.toggle_tasks').click(function(e) {
		e.preventDefault();
		var direction = jQuery(this).val();
		var ms = jQuery(this).data('ms')
		var project = jQuery(this).data('project');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_toggle_project_ms',
			'project' : project,
			'ms' : ms,
			'direction' : direction
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.toggle_tasks').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.toggle_tasks').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('.toggle_tasks').prop('disabled', false);
				if(response.status == 'on') {
					jQuery('#ms-' + response.ms).removeClass('ms-toggled');
					jQuery('#toggle-' + response.ms + ' i.fa').removeClass('fa-chevron-circle-down');
					jQuery('#toggle-' + response.ms + ' i.fa').addClass('fa-chevron-circle-up');
					jQuery('#toggle-' + response.ms).val('hide');
				} else {
					jQuery('#ms-' + response.ms).addClass('ms-toggled');
					jQuery('#toggle-' + response.ms + ' i.fa').addClass('fa-chevron-circle-down');
					jQuery('#toggle-' + response.ms + ' i.fa').removeClass('fa-chevron-circle-up');
					jQuery('#toggle-' + response.ms).val('show');
				}
			}
		});
	});
	jQuery('.start_editable').on('change', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('id');
		var date = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_editable_start',
			'task_id' : task_id,
			'date' : date,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.start_editable').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.start_editable').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('.start_editable').prop('disabled', false);
			}
		});
	});
	jQuery('.end_editable').on('change', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('id');
		var date = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_editable_end',
			'task_id' : task_id,
			'date' : date,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.end_editable').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.end_editable').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('.end_editable').prop('disabled', false);
			}
		});
	});
	jQuery('.assignee_editable').on('change', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('id');
		var assignee = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_editable_assignee',
			'task_id' : task_id,
			'assignee' : assignee,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.assignee_editable').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.assignee_editable').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('.assignee_editable').prop('disabled', false);
			}
		});
	});
});