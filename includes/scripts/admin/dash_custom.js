jQuery(document).ready(function() {
	jQuery('.masonry-grid').masonry({
		columnWidth: '.grid-sizer',
		itemSelector: '.grid-item',
		percentPosition: true
	});
	jQuery('.add_timer').on('click', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).val();
		var task_title = jQuery(this).data('title');
		jQuery.colorbox({
			'maxWidth':'95%',
			'inline': true,
			'href': '#add-time-div',							
			'opacity': '0.5',	
			'overlayClose'  : false, 
			'escKey' : false,
			'onCleanup' : function() {
				jQuery('.timer').timer('remove');
			}
		});	
		jQuery.colorbox.resize();	
		jQuery('#task_time_task').val(task_id);
	});
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
				alert('Something went wrong, pleae check your WordPress account to make sure the Display Name field has been completed. Then try again.');
			} else {
				spinner.hide();
				jQuery('#create_linked_team').prop('disabled', false);
				location.reload();
			}
		});	
	});
	jQuery('.colorbox').on('click', function(e) {
		e.preventDefault();
		var anc = jQuery(this).attr('href');
		var id = anc.replace('#', '');
		var thediv = jQuery('#' + id);
		jQuery(thediv).parent('div').attr('id', id + '_container');
		jQuery.colorbox({
			'inline': true,
			'href': '#' + id,
			'opacity': '0.5',
		});	
	});
	jQuery('body').on('focus',".datepicker", function(){
		jQuery(this).datepicker({ dateFormat: 'dd/mm/yy' });
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
				jQuery.colorbox.resize();
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
				jQuery.colorbox.resize();
			}				
		});
	});
	var hasTimer = false;
	jQuery('.start-timer-btn').on('click', function() {
		hasTimer = true;
		jQuery('.timer').timer({
			editable: false,
			format: '%H:%M:%S' 
		});
		jQuery(this).addClass('hidden');
		jQuery('.pause-timer-btn, .remove-timer-btn').removeClass('hidden');
	});
	jQuery('.resume-timer-btn').on('click', function() {
		jQuery('.timer').timer('resume');
		jQuery(this).addClass('hidden');
		jQuery('.pause-timer-btn, .remove-timer-btn').removeClass('hidden');
	});
	jQuery('.pause-timer-btn').on('click', function() {
		jQuery('.timer').timer('pause');
		jQuery(this).addClass('hidden');
		jQuery('.resume-timer-btn').removeClass('hidden');
	});
	jQuery('.remove-timer-btn').on('click', function() {
		hasTimer = false;
		jQuery('.timer').timer('remove');
		jQuery(this).addClass('hidden');
		jQuery('.start-timer-btn').removeClass('hidden');
		jQuery('.pause-timer-btn, .resume-timer-btn').addClass('hidden');
	});
	jQuery('.timer').on('focus', function() {
		if(hasTimer) {
			jQuery('.pause-timer-btn').addClass('hidden');
			jQuery('.resume-timer-btn').removeClass('hidden');
		}
	});
	jQuery('.timer').on('blur', function() {
		if(hasTimer) {
			jQuery('.pause-timer-btn').removeClass('hidden');
			jQuery('.resume-timer-btn').addClass('hidden');
		}
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
				jQuery.colorbox.resize();
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#add_time_ajax').prop('disabled', false);
				jQuery('#time_messages').html('<div class="cqpim-alert cqpim-alert-danger">' + response.message + '</div>');
				jQuery.colorbox.resize();
			} else {
				spinner.hide();
				jQuery('#add_time_ajax').prop('disabled', false);
				jQuery('#time_messages').html('<div class="cqpim-alert cqpim-alert-success">' + response.message + '</div>');
				jQuery.colorbox.resize();
				location.reload();
			}				
		});
	});
	jQuery('#task_status_filter').live('change', function(e) {
		e.preventDefault();
		var filter = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_filter_tasks',
			'filter' : filter,
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
				location.reload();
			}				
		});
	});	
	jQuery('.calendar_filter').live('change', function(e) {
		e.preventDefault();
		var filters = jQuery('.calendar_filter:checkbox:checked').map(function() {
			return this.value;
		}).get();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_filter_calendar',
			'filters' : filters,
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
			location.reload();	
		});
	});	
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
	jQuery('#income_control_type').live('change', function(e) {
		e.preventDefault();
		var date = jQuery('#income_control_type').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_edit_income_graph',
			'date' : date,
			'type' : 'type',
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
	jQuery('.dataTable').on('change', '.admin_task_assignee', function(e) {
		e.preventDefault();
		var task_id = jQuery(this).data('task');
		var assignee = jQuery(this).val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_edit_assignee_from_admin',
			'task_id' : task_id,
			'assignee' : assignee
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('.admin_task_assignee').prop('disabled', true);
			},
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('.admin_task_assignee').prop('disabled', false);
			} else {
				spinner.hide();
				jQuery('.admin_task_assignee').prop('disabled', false);
			}
		});	
	});	
});