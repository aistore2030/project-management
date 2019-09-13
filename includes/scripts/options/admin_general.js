jQuery(document).ready(function() {
	jQuery('.cqpim_tooltip').tooltip();
	jQuery('.cqpim_picker').wpColorPicker();
	jQuery('.dataTable').dataTable({
		"pageLength": jQuery(this).data('rows'),
		"order" : jQuery(this).data('ordering'),
		"language": {
			"processing":     localisation.datatables.sProcessing,
			"search":         localisation.datatables.sSearch,
			"lengthMenu":     localisation.datatables.sLengthMenu,
			"info":           localisation.datatables.sInfo,
			"infoEmpty":      localisation.datatables.sInfoEmpty,
			"infoFiltered":   localisation.datatables.sInfoFiltered,
			"infoPostFix":    localisation.datatables.sInfoPostFix,
			"loadingRecords": localisation.datatables.sLoadingRecords,
			"zeroRecords":    localisation.datatables.sZeroRecords,
			"emptyTable":     localisation.datatables.sEmptyTable,
			"paginate": {
				"first":      localisation.datatables.sFirst,
				"previous":   localisation.datatables.sPrevious,
				"next":       localisation.datatables.sNext,
				"last":       localisation.datatables.sLast
			},
			"aria": {
				"sortAscending":  localisation.datatables.sSortAscending,
				"sortDescending": localisation.datatables.sSortDescending
			}
		}	
	});
	jQuery('.dataTable-ST').dataTable({
		"pageLength": 20,
		"order" : [[ 6, "desc" ]],
		"language": {
			"processing":     localisation.datatables.sProcessing,
			"search":         localisation.datatables.sSearch,
			"lengthMenu":     localisation.datatables.sLengthMenu,
			"info":           localisation.datatables.sInfo,
			"infoEmpty":      localisation.datatables.sInfoEmpty,
			"infoFiltered":   localisation.datatables.sInfoFiltered,
			"infoPostFix":    localisation.datatables.sInfoPostFix,
			"loadingRecords": localisation.datatables.sLoadingRecords,
			"zeroRecords":    localisation.datatables.sZeroRecords,
			"emptyTable":     localisation.datatables.sEmptyTable,
			"paginate": {
				"first":      localisation.datatables.sFirst,
				"previous":   localisation.datatables.sPrevious,
				"next":       localisation.datatables.sNext,
				"last":       localisation.datatables.sLast
			},
			"aria": {
				"sortAscending":  localisation.datatables.sSortAscending,
				"sortDescending": localisation.datatables.sSortDescending
			}
		}	
	});
	jQuery('.dataTable-WC').dataTable({
		"order" : [[ 0, "desc" ]],
		"language": {
			"processing":     localisation.datatables.sProcessing,
			"search":         localisation.datatables.sSearch,
			"lengthMenu":     localisation.datatables.sLengthMenu,
			"info":           localisation.datatables.sInfo,
			"infoEmpty":      localisation.datatables.sInfoEmpty,
			"infoFiltered":   localisation.datatables.sInfoFiltered,
			"infoPostFix":    localisation.datatables.sInfoPostFix,
			"loadingRecords": localisation.datatables.sLoadingRecords,
			"zeroRecords":    localisation.datatables.sZeroRecords,
			"emptyTable":     localisation.datatables.sEmptyTable,
			"paginate": {
				"first":      localisation.datatables.sFirst,
				"previous":   localisation.datatables.sPrevious,
				"next":       localisation.datatables.sNext,
				"last":       localisation.datatables.sLast
			},
			"aria": {
				"sortAscending":  localisation.datatables.sSortAscending,
				"sortDescending": localisation.datatables.sSortDescending
			}
		}	
	});
	jQuery('.dataTable-RIV').dataTable({
		"pageLength": 25,
		"order" : [[ 6, "asc" ]],
		"language": {
			"processing":     localisation.datatables.sProcessing,
			"search":         localisation.datatables.sSearch,
			"lengthMenu":     localisation.datatables.sLengthMenu,
			"info":           localisation.datatables.sInfo,
			"infoEmpty":      localisation.datatables.sInfoEmpty,
			"infoFiltered":   localisation.datatables.sInfoFiltered,
			"infoPostFix":    localisation.datatables.sInfoPostFix,
			"loadingRecords": localisation.datatables.sLoadingRecords,
			"zeroRecords":    localisation.datatables.sZeroRecords,
			"emptyTable":     localisation.datatables.sEmptyTable,
			"paginate": {
				"first":      localisation.datatables.sFirst,
				"previous":   localisation.datatables.sPrevious,
				"next":       localisation.datatables.sNext,
				"last":       localisation.datatables.sLast
			},
			"aria": {
				"sortAscending":  localisation.datatables.sSortAscending,
				"sortDescending": localisation.datatables.sSortDescending
			}
		}	
	});
	if(jQuery('body').hasClass('post-type-cqpim_tasks') 
		|| jQuery('body').hasClass('post-type-cqpim_support')
		|| jQuery('body').hasClass('post-type-cqpim_expense')
		|| jQuery('body').hasClass('post-type-cqpim_bug')
	) {
		cqpim_simulate_parent();
	};
	jQuery('.datepicker').datepicker({ 
		showButtonPanel: true,
		closeText: localisation.calendar.closeText,
		currentText: localisation.calendar.currentText,
		monthNames: localisation.calendar.monthNames,
		monthNamesShort: localisation.calendar.monthNamesShort,
		dayNames: localisation.calendar.dayNames,
		dayNamesShort: localisation.calendar.dayNamesShort,
		dayNamesMin: localisation.calendar.dayNamesMin,
		dateFormat: localisation.calendar.dateFormat,
		firstDay: localisation.calendar.firstDay,
	});
	jQuery('#test_piping').click(function(e) {
		e.preventDefault();
		var spinner = jQuery('#cqpim_overlay');	
		var cqpim_mail_server = jQuery('#cqpim_mail_server').val();
		var cqpim_piping_address = jQuery('#cqpim_piping_address').val();
		var cqpim_mailbox_name = jQuery('#cqpim_mailbox_name').val();
		var cqpim_mailbox_pass = jQuery('#cqpim_mailbox_pass').val();
		var data = {
			'action' : 'pto_test_piping',
			'cqpim_mail_server' : cqpim_mail_server,
			'cqpim_mailbox_name' : cqpim_mailbox_name,
			'cqpim_mailbox_pass' : cqpim_mailbox_pass,
		}
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				jQuery('#test_piping').prop('disabled', false);
				spinner.show();
			},
		}).always(function(response) {
			console.log(response);
		}).done(function(response){
			if(response.error == true) {
				spinner.hide();
				jQuery('#test_piping').prop('disabled', false);
				alert(response.message);
			} else {
				spinner.hide();
				jQuery('#test_piping').prop('disabled', false);
				alert(response.message);
			}
		});
	});
	jQuery('#switch_to_resolved').click(function(e) {
		e.preventDefault();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_switch_resolved_tickets'
		};
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(){
				spinner.show();
				jQuery('#switch_to_resolved').prop('disabled', true);
			},
		}).done(function(response){
			spinner.hide();
			jQuery('#switch_to_resolved').prop('disabled', false);
			location.reload();
		});	
	});
	jQuery('#pto_admin_menu_mobile').on('change', function(e) {
		var url = jQuery(this).val();
		window.location.replace(url);		
	});
	jQuery('#sub_status').on('change', function(e) {
		e.preventDefault();
		var status = jQuery('#sub_status').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_change_dash_sub_status',
			'status' : status,
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
	jQuery('#sub_renewals_in').on('change', function(e) {
		e.preventDefault();
		var status = jQuery('#sub_renewals_in').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_change_dash_sub_due',
			'status' : status,
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
	jQuery('#orders_from').on('change', function(e) {
		e.preventDefault();
		var status = jQuery('#orders_from').val();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_woocommerce_dash_orders_filter',
			'status' : status,
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
	jQuery('.cqpim_notifications').on('click', function(e) {
		e.preventDefault;
		jQuery('#cqpim_notifications').toggle();
	});
	jQuery('.nf_remove_button').on('click', function(e) {		
		e.preventDefault();
		var element = jQuery(this);
		var key = jQuery(this).data('key');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_notifications_remove_nf',
			'key' : key,
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
			jQuery(element).closest('li').slideUp("slow");	
		});	
	});
	jQuery('.notification_item').on('click', function(e) {		
		e.preventDefault();
		var element = jQuery(this);
		var item = jQuery(this).data('item');
		var key = jQuery(this).data('key');
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_notifications_item',
			'key' : key,
			'item' : item
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
			if(response.error == true) {
				alert(response.message);
			} else {
				var redirect = response.redirect.replace("&amp;", "&");
				window.location.replace(redirect);
			}
		});	
	});	
	jQuery('#clear_all_read_nf').on('click', function(e) {		
		e.preventDefault();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_clear_all_read_nf',
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
			jQuery('#notifications_ul li').each(function() {
				if(!jQuery(this).hasClass('unread')) {
					jQuery(this).slideUp("slow");
				}				
			});
		});	
	});
	jQuery('#mark_all_read_nf').on('click', function(e) {		
		e.preventDefault();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_mark_all_read_nf',
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
			jQuery('#notifications_ul li').each(function() {
				jQuery(this).removeClass("unread");
				jQuery('#nf_counter').remove();
			});
		});	
	});
	jQuery('#clear_all_nf').on('click', function(e) {		
		e.preventDefault();
		var spinner = jQuery('#cqpim_overlay');
		var data = {
			'action' : 'pto_clear_all_nf',
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
			jQuery('#notifications_ul').remove();
			jQuery('#nf_counter').remove();
			jQuery('#notification_list').html(response.html);
		});	
	});
});
function cqpim_simulate_parent() {
	var ul = jQuery('#toplevel_page_cqpim-dashboard');
	var link = jQuery('#toplevel_page_cqpim-dashboard a.menu-top');
	jQuery(ul).removeClass();
	jQuery(link).removeClass();
	jQuery(ul).addClass('wp-has-submenu wp-has-current-submenu wp-menu-open menu-top menu-icon-generic toplevel_page_cqpim-dashboard');
	jQuery(link).addClass('wp-has-submenu wp-has-current-submenu wp-menu-open menu-top menu-icon-generic toplevel_page_cqpim-dashboard');
}