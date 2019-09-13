jQuery(document).ready(function() {
	var type = jQuery('#title_set').val();
	if(!jQuery('.subsubsub').length) {
		if(!type) {
			jQuery.colorbox({
				'width' : '500px',
				'maxWidth':'95%',
				'inline': true,
				'href': '#set-title-div',							
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
	jQuery('#set-title-action').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
		setTimeout(function(){
			jQuery('#publish').trigger('click');
		}, 500);
	});
	jQuery('#dd-container').sortable({
		handle: '.dd-reorder',
		cancel: '',
		update: function( event, ui ) {
			var i = 1;
			jQuery('.dd-milestone').each(function() {
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
			var template_id = jQuery('#post_ID').val();
			var i = 1;
			weights = {};
			jQuery(tasks).each(function() {
				var ms_id = jQuery(this).children('input.ms_id').val();
				var task_id = jQuery(this).children('input.task_id').val();
				jQuery(this).children('input.task_weight').val(i);
				weights[task_id] = {
					'task_id' : task_id,
					'ms_id' : ms_id,
					'weight' : i,
				};
				i = i + 1;
			});
			var data = {
				'action' : 'pto_update_task_weight_template',
				'weights' : weights,
				'template_id' : template_id
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
	jQuery('.dd-subtasks').sortable({
		handle: '.dd-reorder',
		cancel: '',
		update: function( event, ui ) {
			var tasks = jQuery(this).find('.dd-subtask');
			var template_id = jQuery('#post_ID').val();
			var i = 1;
			weights = {};
			jQuery(tasks).each(function() {
				var ms_id = jQuery(this).children('input.ms_id').val();
				var parent_id = jQuery(this).children('input.parent_id').val();
				var task_id = jQuery(this).children('input.task_id').val();
				jQuery(this).children('input.task_weight').val(i);
				weights[task_id] = {
					'task_id' : task_id,
					'ms_id' : ms_id,
					'parent_id' : parent_id,
					'weight' : i,
				};
				i = i + 1;
			});
			var data = {
				'action' : 'pto_update_subtask_weight_template',
				'weights' : weights,
				'template_id' : template_id
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
	jQuery('button.cancel-colorbox').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
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
	jQuery('.add_subtask').on('click', function(e){
		e.preventDefault();
		var id = jQuery(this).val();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#add-subtask-div-' + id,
			'opacity': '0.5',
		});		
	});
	jQuery('button.edit-task').on('click', function(e){
		e.preventDefault();
		var ms = jQuery(this).attr('data-ms');
		var key = jQuery(this).val();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#edit-task-div-' + ms + key,
			'opacity': '0.5',
		});	
	});
	jQuery('button.edit-subtask').on('click', function(e){
		e.preventDefault();
		var ms = jQuery(this).attr('data-ms');
		var parent = jQuery(this).attr('data-parent');
		var key = jQuery(this).val();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#edit-subtask-div-' + ms + parent + key,
			'opacity': '0.5',
		});	
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
			'action' : 'pto_add_step_to_template',
			'title' : title,
			'start' : start,
			'deadline' : deadline,
			'ID' : post_id,
			'cost' : cost,
			'milestone_id' : milestone_id,
			'order' : milestone_order,
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
		jQuery('#cqpim_overlay').show();
		var element = jQuery(this);
		var idToRemove = jQuery(element).val();
		var hiddenfield = '<input type="hidden" name="delete_stage[]" value="' + idToRemove + '" data-id="' + idToRemove + '" >';		
		jQuery(hiddenfield).appendTo('#add-milestone');
		jQuery(this).parents('div.quote_element_add').css('display', 'none');
		jQuery('#publish').trigger('click');
	});
	jQuery('button.save-task').on('click', function(e){
		e.preventDefault();
		var id = jQuery(this).val();
		var tid = jQuery(this).siblings('#task_id_' + id).val();
		var title = jQuery(this).siblings('#task_title_' + id).val();
		var description = jQuery(this).siblings('#task_description_' + id).val();
		var start = jQuery(this).siblings('#task_start_' + id).val();
		var assignee = jQuery(this).siblings('#task_assignee_' + id).val();
		var ms_id = jQuery(this).siblings('#task_milestone_id_' + id).val();
		var project_id = jQuery(this).siblings('#task_project_id_' + id).val();
		var deadline = jQuery(this).siblings('#task_finish_' + id).val();
		var weight = jQuery(this).siblings('#task_weight_' + id).val();
		var template_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_create_task_template',
			'task_finish' : deadline,
			'task_project_id' : project_id,
			'task_title' : title,
			'task_milestone_id' : ms_id,
			'task_weight' : weight,
			'task_id' : tid,
			'description' : description,
			'start' : start,
			'assignee' : assignee,
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
		var tid = jQuery(this).siblings('#sub_task_id_' + id).val();
		var title = jQuery(this).siblings('#sub_task_title_' + id).val();
		var description = jQuery(this).siblings('#sub_task_description_' + id).val();
		var start = jQuery(this).siblings('#sub_task_start_' + id).val();
		var ms_id = jQuery(this).siblings('#sub_task_milestone_id_' + id).val();
		var parent_id = jQuery(this).siblings('#sub_task_parent_id_' + id).val();
		var project_id = jQuery(this).siblings('#sub_task_project_id_' + id).val();
		var deadline = jQuery(this).siblings('#sub_task_finish_' + id).val();
		var weight = jQuery(this).siblings('#sub_task_weight_' + id).val();
		var assignee = jQuery(this).siblings('#sub_task_assignee_' + id).val();
		var template_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_create_subtask_template',
			'task_finish' : deadline,
			'task_project_id' : project_id,
			'task_title' : title,
			'task_milestone_id' : ms_id,
			'task_weight' : weight,
			'task_id' : tid,
			'parent_id' : parent_id,
			'description' : description,
			'start' : start,
			'assignee' : assignee
		};
		if(title) {
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('button.save-subtask').prop('disabled', true);
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('button.save-subtask').prop('disabled', false);
					alert(response.errors);
				} else {
					spinner.hide();
					jQuery('button.save-subtask').prop('disabled', false);
					location.reload();
				}
			});		
		} else {
			jQuery('#subtask-messages-' + parent_id).html('<p style="color:#F00">Title is required</p>');
		}
	});
	jQuery('button.update-task').on('click', function(e){
		e.preventDefault();
		var task_id = jQuery(this).val();
		var ms = jQuery(this).attr('data-ms');
		var tid = jQuery('#post_ID').val();
		var title = jQuery('#task_title_' + ms + task_id).val();
		var deadline = jQuery('#task_finish_' + ms + task_id).val();
		var start = jQuery('#task_start_' + ms + task_id).val();
		var description = jQuery('#task_description_' + ms + task_id).val();
		var assignee = jQuery(this).siblings('#task_assignee_' + ms + task_id).val();
		var template_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_update_task_template',
			'ms' : ms,
			'tid' : tid,
			'task_id' : task_id,
			'title' : title,
			'deadline' : deadline,
			'start' : start,
			'description' : description,
			'assignee' : assignee
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
	jQuery('button.update-subtask').on('click', function(e){
		e.preventDefault();
		var task_id = jQuery(this).val();
		var ms = jQuery(this).attr('data-ms');
		var parent = jQuery(this).attr('data-parent');
		var tid = jQuery('#post_ID').val();
		var title = jQuery('#task_title_' + ms + parent + task_id).val();
		var deadline = jQuery('#task_finish_' + ms + parent + task_id).val();
		var start = jQuery('#task_start_' + ms + parent + task_id).val();
		var description = jQuery('#task_description_' + ms + parent + task_id).val();
		var assignee = jQuery('#sub_task_assignee_' + ms + parent + task_id).val();
		var template_id = jQuery('#post_ID').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_update_subtask_template',
			'ms' : ms,
			'parent' : parent,
			'tid' : tid,
			'task_id' : task_id,
			'title' : title,
			'deadline' : deadline,
			'start' : start,
			'description' : description,
			'assignee' : assignee
		};
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('button.update-subtask').prop('disabled', true);
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('button.update-subtask').prop('disabled', false);
					alert(response.errors);
				} else {
					spinner.hide();
					jQuery('button.update-subtask').prop('disabled', false);
					location.reload();
				}
			});			
	});
	jQuery('button.delete_task').on('click', function(e){
		e.preventDefault();
		var tid = jQuery(this).attr('data-tid');
		var ms = jQuery(this).attr('data-ms');
		var task_id = jQuery(this).val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_task_template',
			'task_id' : task_id,
			'tid' : tid,
			'ms' : ms
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
	jQuery('button.delete_subtask').on('click', function(e){
		e.preventDefault();
		var tid = jQuery(this).attr('data-tid');
		var ms = jQuery(this).attr('data-ms');
		var parent = jQuery(this).attr('data-parent');
		var task_id = jQuery(this).val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_subtask_template',
			'task_id' : task_id,
			'tid' : tid,
			'ms' : ms,
			'parent' : parent,
		};
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('button.delete_subtask').prop('disabled', true);
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('button.delete_subtask').prop('disabled', false);
					alert(response.errors);
				} else {
					spinner.hide();
					jQuery('button.delete_subtask').prop('disabled', false);
					location.reload();
				}
			});		
	});
	jQuery('button#clear-all-action').on('click', function(e){
		e.preventDefault();
		var tid = jQuery(this).val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_clear_all_template',
			'tid' : tid,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('button#clear-all-action').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('button#clear-all-action').prop('disabled', false);
				alert(response.errors);
			} else {
				spinner.hide();
				jQuery('button#clear-all-action').prop('disabled', false);
				location.reload();
			}
		});		
	});
});