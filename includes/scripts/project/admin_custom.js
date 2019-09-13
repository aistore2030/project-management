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
	jQuery('#project_contributors .inside').each(function() {
		jQuery(this).children('.team_member').matchHeight();
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
	jQuery('.dd-subtasks').sortable({
		handle: '.dd-reorder',
		cancel: '',
		update: function( event, ui ) {
			var tasks = jQuery(this).find('.dd-subtask');
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
	jQuery('.author-other.no_access td.column-title span.post-state').remove();
	jQuery('.author-other.no_access td.column-title a').remove();
	jQuery('.author-other.no_access td.column-title a').remove();
	jQuery('.author-other.no_access td.column-title .row-actions').remove();
	jQuery('.author-other.no_access td.column-title strong').html(localisation.projects.assign_error);
	jQuery('.author-other.no_access').remove();
	jQuery('button.cancel-colorbox').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox.close();
	});
	var type = jQuery('#project_ref_for_basics').val();
	if(!jQuery('.subsubsub').length && jQuery('#project_details').length) {
		if(!type) {
			jQuery.colorbox({
				'maxWidth':'95%',
				'inline': true,
				'href': '#quote_basics',							
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
	jQuery('button.save-basics').click(function(e){
		e.preventDefault();
		var ref = jQuery('#quote_ref').val();
		var start = jQuery('#start_date').val();
		var finish = jQuery('#finish_date').val();
		if(ref && start && finish) {
			jQuery.colorbox.close();
			setTimeout(function(){
				jQuery('#publish').trigger('click');
			}, 500);
		} else {
			jQuery('#basics-error').html('<div class="cqpim-alert cqpim-alert-danger alert-display">' + localisation.projects.project_dates + '</div><div class="clear"></div>');
			jQuery.colorbox.resize();
		}
	});
	jQuery('#edit-quote-details').on('click', function(e){
		e.preventDefault();
			jQuery.colorbox({
				'width' : '500px',
				'maxWidth':'95%',
				'inline': true,
				'href': '#quote_basics',							
				'opacity': '0.5',	
			});	
			jQuery.colorbox.resize();	
	});
	jQuery('#unsigned_off').on('click', function(e){
		e.preventDefault();
			jQuery.colorbox({
				'width' : '500px',
				'maxWidth':'95%',
				'inline': true,
				'href': '#quote_unsign',							
				'opacity': '0.5',	
			});	
			jQuery.colorbox.resize();	
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
	jQuery('button#add_team_member').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#add_team_member_div',
			'opacity': '0.5',
		});
	});
	jQuery('button#add_message_trigger').on('click', function(e){
		e.preventDefault();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#add_message',
			'opacity': '0.5',
		});
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
		var key = jQuery(this).val();
		jQuery.colorbox({
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#edit-task-div-' + key,
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
	jQuery(document).on('click', 'button.delete_stage_conf', function(e){
		e.preventDefault();
		var id = jQuery(this).val();
			jQuery.colorbox({
			'width' : '500px',
				'maxWidth':'95%',
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
		jQuery('#cqpim_overlay').show();
		var hiddenField = '<input type="hidden" name="delete_file[]" value="' + attID + '" />';
		jQuery(this).parents('div.inside').prepend(hiddenField);
		jQuery('#publish').trigger('click');
	});
	jQuery('.assign_all').on('click', function(e){
		e.preventDefault();
		var ms = jQuery(this).data('ms');
		jQuery.colorbox({			
			'width' : '500px',
			'maxWidth':'95%',
			'inline': true,
			'href': '#assign-all',
			'opacity': '0.5',
		});	
		jQuery.colorbox.resize();
		jQuery('#assign_all_ms').val(ms);
	});
});