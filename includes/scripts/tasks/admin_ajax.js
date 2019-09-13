jQuery(document).ready(function() {
	jQuery('#task_project_id').change(function(e) {
		e.preventDefault();
		update_milestone_dropdown();
	});
	jQuery('.time_remove').live('click', function(e) {
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
				location.reload();
			}				
		});
	});
	jQuery('#add_time_ajax').live('click', function(e) {
		e.preventDefault();
		var task_id = jQuery('#task_time_task').val();
		var time = jQuery('#task_time_value').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_add_timer_time',
			'task_id' : task_id,
			'time' : time
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#add_time_ajax').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#add_time_ajax').prop('disabled', false);
				jQuery('#time_messages').html(response.message);
			} else {
				spinner.hide();
				jQuery('#add_time_ajax').prop('disabled', false);
				jQuery('#time_messages').html(response.message);
				location.reload();
			}				
		});
	});
	jQuery('#add_mtime_ajax').live('click', function(e) {
		e.preventDefault();
		var task_id = jQuery('#task_time_task').val();
		var hours = jQuery('#add_time_hours').val();
		var minutes = jQuery('#add_time_minutes').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_add_manual_task_time',
			'task_id' : task_id,
			'hours' : hours,
			'minutes' : minutes,
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#add_mtime_ajax').prop('disabled', true);
			},
		}).always(function(response){
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
			spinner.hide();
				jQuery('#add_mtime_ajax').prop('disabled', false);
				alert(response.errors);
			} else {
				spinner.hide();
				jQuery('#add_mtime_ajax').prop('disabled', false);
				location.reload();
			}				
		});
	});
	jQuery('#delete_task').live('click', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('id');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_delete_task_page',
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
			'action' : 'pto_delete_task_message',
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
});
function update_milestone_dropdown() {
		var project_id = jQuery('#task_project_id').val();
		var domain = document.domain;
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_populate_project_milestone',
			'ID' : project_id
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#task_project_id').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#task_project_id').prop('disabled', false);
				jQuery('#task_milestone_id').html(response.options);
				jQuery('#task_owner').html(response.team_options);
			} else {
				spinner.hide();
				jQuery('#task_project_id').prop('disabled', false);
				jQuery('#task_milestone_id').html(response.options);
				jQuery('#task_owner').html(response.team_options);
			}
		});
}