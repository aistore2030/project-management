jQuery(document).ready(function() {
	var scrollTop = localStorage.getItem('scrollTop');
    if (scrollTop !== null) {
        jQuery(window).scrollTop(Number(scrollTop));
        localStorage.removeItem('scrollTop');
    }
	jQuery('#publish').click(function(event) {
		localStorage.setItem('scrollTop', jQuery(window).scrollTop());
		return true;
	});
	jQuery('button.s_button').on('click', function(e){
		e.preventDefault();
		setTimeout(function(){
			jQuery('#publish').trigger('click');
		}, 500);
	});
	jQuery('button.save').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
		setTimeout(function(){
			jQuery('#publish').trigger('click');
		}, 500);
	});
	jQuery('button.save-milestone').on('click', function(e){
		e.preventDefault();
		var key = jQuery(this).val();
		var finished = jQuery('.finished-' + key).val();
		var status = jQuery('.status-' + key).val();
		if(status != 'complete') {
			jQuery.colorbox.close();
			setTimeout(function(){
				jQuery('#publish').trigger('click');
			}, 500);
		} else { 
			if(finished) {
				jQuery.colorbox.close();
				setTimeout(function(){
					jQuery('#publish').trigger('click');
				}, 500);			
			} else {
				jQuery('#update-ms-message-' + key).html('<div class="cqpim-alert cqpim-alert-danger alert-display">' + localisation.projects.ms_complete + '</div>');
				jQuery.colorbox.resize();
			}
		}
	});
	jQuery('button.cancel-colorbox').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery(document).on('click', 'button.delete_stage_conf', function(e){
		e.preventDefault();
		var id = jQuery(this).val();
			jQuery.colorbox({
				'inline': true,
				'fixed': true,
				'href': '#delete-milestone-div-' + id,	
				'opacity': '0.5',
			});			
	});
	jQuery(document).on('click', 'button.cancel_delete_stage', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	jQuery(document).on('click', 'button.delete_stage', function(e){
		e.preventDefault();
		var element = jQuery(this);
		var idToRemove = jQuery(element).val();
		var hiddenfield = '<input type="hidden" name="delete_stage[]" value="' + idToRemove + '" data-id="' + idToRemove + '" >';		
		jQuery(hiddenfield).appendTo('#add-milestone');
		jQuery(this).parents('div.quote_element_add').css('display', 'none');
		jQuery('#publish').trigger('click');
	});
	jQuery(document).on('click', 'button.delete_file', function(e){		
		e.preventDefault();
		var attID = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_file',
			'ID' : attID
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
	jQuery('a.save').on('click', function(e) {
		e.preventDefault();
		jQuery('#cqpim_overlay').show();
		jQuery('#publish').trigger('click');
	});
	jQuery('#delete_task').live('click', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_support_page',
			'task_id' : task_id,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#delete_task').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#delete_task').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('#delete_task').prop('disabled', false);
				window.location.href = response.redirect;			
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
			'action' : 'pto_delete_support_message',
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
			'weight' : milestone_order
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
		var type = 'ticket';
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
	jQuery('#apply-template-action').click(function(e) {
		e.preventDefault();
		jQuery('#apply-template-messages').html('');
		var quote_id = jQuery(this).val();
		var type = 'quote';
		var template = jQuery('#template_choice').val();
		var spinner = jQuery('#cqpim_overlay');
		var messages = jQuery('#apply-template-messages');
		var domain = document.domain;
		var data = {
			'action' : 'pto_apply_template',
			'quote_id' : quote_id,
			'type' : type,
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
				jQuery('#apply-template-action').prop('disabled', true);
			},
		}).always(function(response) {
			console.log(response);		
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#apply-template-action').prop('disabled', false);
				jQuery(messages).html(response.errors);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('#apply-template-action').prop('disabled', false);
				jQuery(messages).html(response.messages);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('#clear-all-action').click(function(e) {
		e.preventDefault();
		jQuery('#apply-template-messages').html('');
		var quote_id = jQuery(this).val();
		var type = 'quote';
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
				jQuery(messages).html(response.errors);
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('#clear-all-action').prop('disabled', false);
				jQuery(messages).html(response.messages);
				jQuery.colorbox.resize();
				location.reload();
			}
		});
	});
	jQuery('#dd-container').sortable({
		handle: '.dd-reorder',
		cancel: '',
		update: function( event, ui ) {
			var i = 0;
			jQuery('.dd-milestone').each(function(i) {
				jQuery(this).children('input.element_weight').val(i);
				i = i + 1;
			});
		}
	});
	jQuery('.dd-tasks').sortable({
		handle: '.dd-reorder',
		cancel: '',
		update: function( event, ui ) {
			var tasks = jQuery(this).find('.dd-task');
			var i = 0;
			weights = {};
			jQuery(tasks).each(function(i) {
				var task_id = jQuery(this).children('input.task_id').val();
				jQuery(this).children('input.task_weight').val(i);
				weights[task_id] = {
					'task_id' : task_id,
					'weight' : i,
				};
				i = i + 1;
			});
			var data = {
				'action' : 'pto_update_task_weight',
				'weights' : weights,
			};		
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json'
			}).always(function(response){
				console.log(response);
			});
		}
	});
	jQuery('a#add-milestone').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#add-milestone-div',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('a#apply-template').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#apply-template-div',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('a#clear-all').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#clear-all-div',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('button.edit-milestone').on('click', function(e){
		e.preventDefault();
		var key = jQuery(this).val();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#edit-milestone-' + key,
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
	});
	jQuery('.add_task').on('click', function(e){
		e.preventDefault();
		var id = jQuery(this).data('ms');
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#add-task-div-' + id,
			'opacity': '0.5',
		});		
	});
	jQuery('button.edit-task').on('click', function(e){
		e.preventDefault();
		var key = jQuery(this).val();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#edit-task-div-' + key,
			'opacity': '0.5',
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
	jQuery('#send_ticket_invoice').on('click', function(e){
		e.preventDefault();
		var pid = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_send_ticket_invoice',
			'pid' : pid
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#send_ticket_invoice').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#send_ticket_invoice').prop('disabled', false);
				alert(response.errors);
			} else {
				spinner.hide();
				jQuery('#send_ticket_invoice').prop('disabled', false);
				location.reload();
			}
		});		
	});
	jQuery('#ticket_client').live('change', function(e) {
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
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
			} else {
				spinner.hide();
				jQuery('#client_contact').prop('disabled', false);
				jQuery('#client_contact').html(response.contacts);
			}
		});
	});
});