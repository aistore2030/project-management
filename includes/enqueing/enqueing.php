<?php
function pto_return_localisation() {
	global $wp_locale;
	$localisation = array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'stripe_key' => get_option('client_invoice_stripe_key'),
		'calendar' => array(
			'closeText'         => __( 'Done', 'cqpim' ),
			'currentText'       => __( 'Today', 'cqpim' ),
			'monthNames'        => array_values( $wp_locale->month ),
			'monthNamesShort'   => array_values( $wp_locale->month_abbrev ),
			'monthStatus'       => __( 'Show a different month', 'cqpim' ),
			'dayNames'          => array_values( $wp_locale->weekday ),
			'dayNamesShort'     => array_values( $wp_locale->weekday_abbrev ),
			'dayNamesMin'       => array_values( $wp_locale->weekday_initial ),
			'dateFormat'        => pto_date_format_php_to_js( get_option( 'cqpim_date_format' ) ),
			'firstDay'          => get_option( 'start_of_week' )
		),
		'datatables' => array(
			'sEmptyTable' => __('No data available in table', 'cqpim'),
			'sInfo' => __('Showing _START_ to _END_ of _TOTAL_', 'cqpim'),
			'sInfoEmpty' => __('Showing 0 to 0 of 0', 'cqpim'),
			'sInfoFiltered' => __('(filtered from _MAX_ total entries)', 'cqpim'),
			'sInfoPostFix' => '',
			'sInfoThousands' => ',',
			'sLengthMenu' => __('Show _MENU_', 'cqpim'),
			'sLoadingRecords' => __('Loading...', 'cqpim'),
			'sProcessing' => __('Processing...', 'cqpim'),
			'sSearch' => __('Search:', 'cqpim'),
			'sZeroRecords' => __('No matching records found', 'cqpim'),
				'sFirst' => __('First', 'cqpim'),
				'sLast' => __('Last', 'cqpim'),
				'sNext' => __('Next', 'cqpim'),
				'sPrevious' => __('Previous', 'cqpim'),
				'sSortAscending' =>  __(': activate to sort column ascending', 'cqpim'),
				'sSortDescending' => __(': activate to sort column descending', 'cqpim'),
		),
		'cf_alerts' => array(
			'done' => __('Custom fields updated successfully', 'cqpim'),
			'fail' => __('There was a problem updating the fields. Perhaps you didn\'t add any?', 'cqpim')
		),	
		'quote_vars' => array(
			'assign_error' => __('You are not assigned to this project', 'cqpim'),
			'project_dates' => __('Please choose the Type, Client, Ref and Dates for this Quote / Estimate', 'cqpim'),
		),
		'project_vars' => array(
			'assign_error' => __('You are not assigned to this project', 'cqpim'),
			'project_dates' => __('Please add a Ref and Dates for this Project', 'cqpim'),
			'ms_complete' => __('You cannot mark a milestone as complete until you have completed the finished cost field.', 'cqpim')
		),
		'teams' => array(
			'link_error' => __('Something went wrong, pleae check your WordPress account to make sure the Display Name field has been completed. Then try again.', 'cqpim'),
		),
		'uploads' => array(
			'upload_url' => admin_url('async-upload.php'),
			'ajax_url'   => admin_url('admin-ajax.php'),
			'nonce'      => wp_create_nonce('media-form'),
			'strings' => array(
				'uploading' => __('Uploading...', 'cqpim'),
				'success' => __('Successfully uploaded', 'cqpim'),
				'change' => __('Remove', 'cqpim'),
				'error' => __('Failed to upload file. It may not be on our list of allowed extensions. Please try again.', 'cqpim')
			),
			'client_up_fail' => __('The file(s) could not be uploaded or no files were selected for upload, please try again', 'cqpim'),
			'client_up_success' => __('The file(s) were successfully uploaded.', 'cqpim'),
		),
		'messaging' => array(
			'dialogs' => array(
				'deleteconv' => __('Delete Conversation', 'cqpim'),
				'leaveconv' => __('Leave Conversation', 'cqpim'),
				'removeconv' => __('Remove User', 'cqpim'),
				'addconv' => __('Add User', 'cqpim'),
				'cancel' => __('Cancel', 'cqpim'),
			),
		),
		'quotes' => array(
			'assign_error' => __('You are not assigned to this project', 'cqpim'),
			'project_dates' => __('Please choose the Type, Client, Ref and Dates for this Quote / Estimate', 'cqpim'),
		),
		'projects' => array(
			'assign_error' => __('You are not assigned to this project', 'cqpim'),
			'project_dates' => __('Please add a Ref and Dates for this Project', 'cqpim'),
			'ms_complete' => __('You cannot mark a milestone as complete until you have completed the finished cost field.', 'cqpim')
		),
	);
	return $localisation;
}
add_action( 'wp_loaded', 'pto_register_front_and_back_scripts' );
function pto_register_front_and_back_scripts(){
	wp_register_style( 
		'pto_fontawesome', 
		'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_colorbox', 
		'https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.1/jquery.colorbox-min.js',
		array('jquery'), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_equalheights', 
		'https://cdnjs.cloudflare.com/ajax/libs/jquery.matchHeight/0.7.2/jquery.matchHeight-min.js',
		array('jquery'), 
		PTO_VERSION, 
		'all' 
	);	
	wp_register_script( 
		'pto_ppjs', 
		'https://www.paypalobjects.com/api/checkout.js',
		array('jquery'), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_multi_upload', 
		PTO_PLUGIN_URL  . '/includes/scripts/upload/multi_upload.js',
		array('jquery'),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_upload_avatar',
		PTO_PLUGIN_URL  . '/includes/scripts/upload/avatar_upload.js',
		array('jquery'),
		PTO_VERSION,
		TRUE
	);
	wp_register_style( 
		'pto_colorbox_styles', 
		PTO_PLUGIN_URL  . '/includes/css/colorbox.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_style( 
		'pto_autocomplete_styles', 
		PTO_PLUGIN_URL  . '/includes/css/autocomplete.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_datatables',
		'https:////cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js',
		array('jquery'),
		PTO_VERSION,
		TRUE
	);
	wp_register_style( 
		'pto_datatables_styles', 
		'https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_tokeninput',
		'https://cdnjs.cloudflare.com/ajax/libs/jquery-tokeninput/1.7.0/jquery.tokeninput.js',
		array('jquery'),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_masonry',	
		'https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.js',
		array('jquery'),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_jquery_ui',
		'https://code.jquery.com/ui/1.12.0/jquery-ui.min.js',
		array('jquery'),
		PTO_VERSION,
		TRUE
	);
	wp_register_style( 
		'pto_jquery_ui_styles', 
		'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
		array(), 
		PTO_VERSION, 
		'all'
	);
	wp_register_script( 
		'pto_timepicker',
		'https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.2.17/jquery.timepicker.min.js',
		array('jquery'),
		PTO_VERSION,
		TRUE
	);	
	wp_register_style( 
		'pto_timepicker_styles', 
		'https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.2.17/jquery.timepicker.min.css',
		array(), 
		PTO_VERSION,
		'all' 
	);	
	wp_register_script( 
		'pto_timer',
		'https://cdnjs.cloudflare.com/ajax/libs/timer.jquery/0.7.1/timer.jquery.min.js',
		array('jquery'),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_stripe_js',
		'https://js.stripe.com/v3/',
		array('jquery'),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_stripe_ideal', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/stripe_ideal.js',
		array('jquery'),
		PTO_VERSION, 
		'all' 
	);
}
add_action( 'wp_enqueue_scripts', 'pto_register_all_non_admin_scripts' );
function pto_register_all_non_admin_scripts() {
	wp_register_script( 
		'pto_fe_jquery', 
		'https://code.jquery.com/jquery-3.2.1.min.js',
		array('jquery'), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_form_upload', 
		PTO_PLUGIN_URL  . '/includes/scripts/upload/form_upload.js',
		array('jquery'),
		PTO_VERSION, 
		'all' 
	);
	wp_register_style( 
		'pto_client_styles', 
		PTO_PLUGIN_URL  . '/includes/css/pto-client-styles.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_style( 
		'pto_raleway_font', 
		'https://fonts.googleapis.com/css?family=Open+Sans',
		array(), 
		PTO_VERSION,
		'all'
	);
	wp_register_style( 
		'pto_client_fe_styles', 
		PTO_PLUGIN_URL  . '/includes/css/pto-client-styles-fe.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_style( 
		'pto_client_inc_styles', 
		PTO_PLUGIN_URL  . '/includes/css/pto-client-styles-inc.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_dash_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/dashboard_custom.js',
		array('jquery'),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_bugs_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/bugs_custom.js',
		array('jquery'),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_client_messaging', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/messaging_client_custom.js',
		array('jquery'), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_quote_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/quote_custom.js',
		array('jquery'),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_register_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/register_ajax.js',
		array('jquery'),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_project_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/project_custom.js',
		array('jquery'),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_tasks_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/tasks_custom.js',
		array('jquery'),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_tickets_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/tickets_custom.js',
		array('jquery'),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_subs_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/subs_custom.js',
		array('jquery'),
		PTO_VERSION, 
		'all' 
	);
}
add_action( 'admin_enqueue_scripts', 'pto_register_all_admin_scripts' );
function pto_register_all_admin_scripts(){
	wp_register_style( 
		'pto_admin_styles', 
		PTO_PLUGIN_URL  . '/includes/css/pto-admin-styles.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_options',
		PTO_PLUGIN_URL  . '/includes/scripts/options/plugin_options.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_client_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/client/admin_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_admin_general',
		PTO_PLUGIN_URL  . '/includes/scripts/options/admin_general.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_admin_dash',
		PTO_PLUGIN_URL  . '/includes/scripts/admin/dash_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_admin_messaging',
		PTO_PLUGIN_URL  . '/includes/scripts/admin/messaging_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_admin_permissions',
		PTO_PLUGIN_URL  . '/includes/scripts/admin/permissions_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_admin_tickets',
		PTO_PLUGIN_URL  . '/includes/scripts/tickets/tickets_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_quotes_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/quote/admin_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_quotes_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/quote/admin_ajax.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_forms_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/forms/admin_ajax.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_leads_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/leads/admin_ajax.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_leadforms_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/leadforms/admin_ajax.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_fields_general',
		PTO_PLUGIN_URL  . '/includes/scripts/options/fields_general.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_invoice_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/invoice/admin_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_invoice_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/invoice/admin_ajax.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_project_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/project/admin_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_project_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/project/admin_ajax.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_tasks_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/tasks/admin_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_bugs_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/bugs/admin_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_tasks_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/tasks/admin_ajax.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_templates_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/templates/admin_ajax.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_suppliers_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/suppliers/admin_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_suppliers_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/suppliers/admin_ajax.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_expenses_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/expenses/admin_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_expenses_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/expenses/admin_ajax.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_subscriptions_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/subscriptions/admin_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_subscriptions_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/subscriptions/admin_ajax.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_plans_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/plans/admin_custom.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_plans_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/plans/admin_ajax.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_repeater', 
		'https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.js',
		array(), 
		PTO_VERSION, 
		'all'
	);
	wp_register_script( 
		'pto_charts', 
		'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js',
		array(), 
		PTO_VERSION, 
		'all'
	);
	wp_register_script( 
		'pto_fullcal_moment',
		PTO_PLUGIN_URL . '/assets/fullcalendar/lib/moment.min.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_fullcal',
		PTO_PLUGIN_URL  . '/assets/fullcalendar/fullcalendar.min.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_style( 
		'pto_fullcal_styles', 
		PTO_PLUGIN_URL  . '/assets/fullcalendar/fullcalendar.min.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script(
		'pto_fullcal_locale',
		PTO_PLUGIN_URL  . '/assets/fullcalendar/lang/' . strtolower(str_replace('_','-', get_locale())) . '.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_formbuilder',
		PTO_PLUGIN_URL . '/assets/formbuilder/assets/js/form-builder.min.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_formbuilder_render',
		PTO_PLUGIN_URL . '/assets/formbuilder/assets/js/form-render.min.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_style( 
		'pto_formbuilder_styles', 
		PTO_PLUGIN_URL . '/assets/formbuilder/assets/css/form-builder.min.css',
		array(), 
		PTO_VERSION,
		'all' 
	);
}
add_action( 'wp_enqueue_scripts', 'pto_enqueue_scripts_where_required', 99 );
function pto_enqueue_scripts_where_required() {
	$login_page = get_option('cqpim_login_page');
	$dash_page = get_option('cqpim_client_page');
	$reset_page = get_option('cqpim_reset_page');
	$register_page = get_option('cqpim_register_page');
	if(is_page($login_page) || is_page($dash_page) || is_page($reset_page) || is_page($register_page)) {
		pto_enqueue_all_frontend();
		wp_enqueue_script('pto_form_upload');
		wp_localize_script('pto_form_upload', 'localisation', pto_return_localisation());
	}
	if(is_page($dash_page)) {
		wp_enqueue_script('pto_fe_register_custom');
		wp_localize_script('pto_fe_register_custom', 'localisation', pto_return_localisation());
		wp_enqueue_script('pto_upload_avatar');
		wp_localize_script('pto_upload_avatar', 'localisation', pto_return_localisation());	
	}
	if(pto_check_addon_status('envato')) {
		$register_page = get_option('cqpim_envato_register_page');
		if(is_page($register_page)) {
			pto_enqueue_all_frontend();	
			wp_enqueue_script('pto_fe_register_custom');
			wp_localize_script('pto_fe_register_custom', 'localisation', pto_return_localisation());				
		}
	}
	if(is_singular('cqpim_quote')) {
		pto_enqueue_all_frontend();	
		wp_enqueue_script('pto_fe_quote_custom');
		wp_localize_script('pto_fe_quote_custom', 'localisation', pto_return_localisation());	
	}
	if(is_singular('cqpim_project')) {
		pto_enqueue_all_frontend();	
		wp_enqueue_script('pto_fe_project_custom');
		wp_localize_script('pto_fe_project_custom', 'localisation', pto_return_localisation());	
		if(pto_check_addon_status('bugs')) {
			wp_enqueue_script('pto_fe_bugs_custom');
			wp_localize_script('pto_fe_bugs_custom', 'localisation', pto_return_localisation());
		}
	}
	if(pto_check_addon_status('bugs')) {
		if(is_singular('cqpim_bug')) {
			pto_enqueue_all_frontend();	
			wp_enqueue_script('pto_fe_bugs_custom');
			wp_localize_script('pto_fe_bugs_custom', 'localisation', pto_return_localisation());
		}
	}
	if(is_singular('cqpim_tasks')) {
		pto_enqueue_all_frontend();
		wp_enqueue_script('pto_fe_tasks_custom');
		wp_localize_script('pto_fe_tasks_custom', 'localisation', pto_return_localisation());	
	}
	if(is_singular('cqpim_support')) {
		pto_enqueue_all_frontend();
		wp_enqueue_script('pto_fe_tickets_custom');
		wp_localize_script('pto_fe_tickets_custom', 'localisation', pto_return_localisation());	
	}
	if(is_singular('cqpim_invoice')) {
		pto_enqueue_all_frontend();
	}
	if(is_singular('cqpim_faq')) {
		pto_enqueue_all_frontend();
	}
	if(is_singular('cqpim_subscription')) {
		pto_enqueue_all_frontend();	
		wp_enqueue_script('pto_fe_subs_custom');
		wp_localize_script('pto_fe_subs_custom', 'localisation', pto_return_localisation());
	}
}
function pto_enqueue_all_frontend() {
	$theme = get_option('client_dashboard_type');
	if($theme == 'inc') {
		$user = wp_get_current_user();
		$login_page = get_option('cqpim_login_page');
		$dash_page = get_option('cqpim_client_page');
		$reset_page = get_option('cqpim_reset_page');
		$register_page = get_option('cqpim_register_page');
		$eregister_page = get_option('cqpim_envato_register_page');
		if(is_singular('cqpim_project') || is_singular('cqpim_quote') || is_singular('cqpim_invoice') || is_singular('cqpim_subscription') || is_singular('cqpim_bug') || is_singular('cqpim_support') || is_singular('cqpim_tasks') || is_singular('cqpim_faq') && in_array('cqpim_client', $user->roles) || is_page($login_page) || is_page($dash_page) || is_page($reset_page) || is_page($register_page)) {
			global $wp_styles;
			foreach( $wp_styles->queue as $handle ) {
				wp_dequeue_style($handle);
			}
			global $wp_scripts;
			foreach( $wp_scripts->queue as $handle ) {
				wp_dequeue_script($handle);
			}
			wp_enqueue_script('jquery');
			wp_enqueue_script('pto_fe_jquery');
			wp_enqueue_style('pto_client_inc_styles');
			wp_enqueue_style('pto_raleway_font');
		}
	} else {
		wp_enqueue_style('pto_client_fe_styles');
	}
	wp_enqueue_script('pto_jquery_ui');
	wp_enqueue_style('pto_jquery_ui_styles');
	wp_enqueue_script('pto_colorbox');
	wp_enqueue_style('pto_colorbox_styles');
	wp_enqueue_script('pto_masonry');
	wp_enqueue_script('pto_datatables');
	wp_enqueue_style('pto_datatables_styles');
	wp_enqueue_style('pto_fontawesome');
	wp_enqueue_script('pto_tokeninput');
	wp_enqueue_script('pto_fe_dash_custom');
	wp_enqueue_script('pto_stripe_js');
	$stripe = get_option('client_invoice_stripe_ideal');
	if(!empty($stripe)) {
		wp_enqueue_script('pto_stripe_ideal');
	}
	wp_localize_script('pto_fe_dash_custom', 'localisation', pto_return_localisation());
	wp_enqueue_script('pto_multi_upload');
	wp_localize_script('pto_multi_upload', 'localisation', pto_return_localisation());
	wp_enqueue_script('pto_client_messaging');
	wp_localize_script('pto_client_messaging', 'localisation', pto_return_localisation());
	wp_enqueue_style('pto_autocomplete_styles');
}
add_action( 'admin_enqueue_scripts', 'pto_enqueue_admin_js', 25 );
function pto_enqueue_admin_js() {
	global $post_type;
	switch ( $post_type ) {	
		case 'cqpim_tasks':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_tasks_custom');
			wp_localize_script('pto_tasks_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_tasks_ajax');
			wp_localize_script('pto_tasks_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_forms':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_formbuilder');
			wp_enqueue_script('pto_formbuilder_render');
			wp_enqueue_style('pto_formbuilder_styles');
			wp_enqueue_script('pto_forms_ajax');
			wp_localize_script('pto_forms_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_client':
			pto_enqueue_all_admin();
			break;
		case 'cqpim_lead':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_leads_ajax');
			wp_localize_script('pto_leads_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_leadform':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_formbuilder');
			wp_enqueue_script('pto_formbuilder_render');
			wp_enqueue_style('pto_formbuilder_styles');
			wp_enqueue_script('pto_leadforms_ajax');
			wp_localize_script('pto_leadforms_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_teams':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_charts');
			wp_enqueue_script('pto_fullcal_moment');
			wp_enqueue_script('pto_fullcal');
			wp_enqueue_script('pto_fullcal_locale');
			wp_enqueue_style('pto_fullcal_styles');
			break;
		case 'cqpim_terms':
			pto_enqueue_all_admin();
			break;
		case 'cqpim_faq':
			pto_enqueue_all_admin();
			break;
		case 'cqpim_bug':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_bugs_custom');
			wp_localize_script('pto_bugs_custom', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_quote':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_quotes_custom');
			wp_localize_script('pto_quotes_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_quotes_ajax');
			wp_localize_script('pto_quotes_ajax', 'localisation', pto_return_localisation());
			break;	
		case 'cqpim_templates':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_templates_ajax');
			wp_localize_script('pto_templates_ajax', 'localisation', pto_return_localisation());
			break;	
		case 'cqpim_project':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_project_custom');
			wp_localize_script('pto_project_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_project_ajax');
			wp_localize_script('pto_project_ajax', 'localisation', pto_return_localisation());
			if(pto_check_addon_status('bugs')) {
				wp_enqueue_script('pto_bugs_custom');
				wp_localize_script('pto_bugs_custom', 'localisation', pto_return_localisation());
			}
			break;	
		case 'cqpim_invoice':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_invoice_custom');
			wp_localize_script('pto_invoice_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_invoice_ajax');
			wp_localize_script('pto_invoice_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_support':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_admin_tickets');
			wp_localize_script('pto_admin_tickets', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_supplier':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_charts');
			wp_enqueue_script('pto_suppliers_custom');
			wp_localize_script('pto_suppliers_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_suppliers_ajax');
			wp_localize_script('pto_suppliers_ajax', 'localisation', pto_return_localisation());
			break;	
		case 'cqpim_expense':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_expenses_custom');
			wp_localize_script('pto_expenses_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_expenses_ajax');
			wp_localize_script('pto_expenses_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_subscription':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_subscriptions_custom');
			wp_localize_script('pto_subscriptions_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_subscriptions_ajax');
			wp_localize_script('pto_subscriptions_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_plan':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_plans_custom');
			wp_localize_script('pto_plans_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_plans_ajax');
			wp_localize_script('pto_plans_ajax', 'localisation', pto_return_localisation());
			break;				
		default:
			break;
	}
}
function pto_enqueue_all_admin() {
	wp_enqueue_script('pto_jquery_ui');
	wp_enqueue_script('pto_colorbox');
	wp_enqueue_style( 'wp-color-picker');
	wp_enqueue_script( 'wp-color-picker');
	wp_enqueue_script('pto_admin_general');
	wp_localize_script('pto_admin_general', 'localisation', pto_return_localisation());
	wp_enqueue_script('pto_client_custom');
	wp_localize_script('pto_client_custom', 'localisation', pto_return_localisation());
	wp_enqueue_script('pto_multi_upload');
	wp_localize_script('pto_multi_upload', 'localisation', pto_return_localisation());
	wp_enqueue_script('pto_datatables');
	wp_enqueue_script('pto_repeater');
	wp_enqueue_style('pto_datatables_styles');
	wp_enqueue_style('pto_fontawesome');
	wp_enqueue_style('pto_admin_styles');
	wp_enqueue_style('pto_jquery_ui_styles');
	wp_enqueue_style('pto_colorbox_styles');	
	wp_enqueue_script('pto_timer');
	wp_enqueue_script('pto_equalheights');
}
function pto_enqueue_plugin_option_scripts() {
	add_action( 'admin_enqueue_scripts', 'pto_enqueue_plugin_option_scripts_now' );		
}
function pto_enqueue_plugin_option_scripts_now() {
	$scripts = array(
		'pto_jquery_ui',
		'pto_colorbox',
		'pto_masonry',
		'pto_datatables',
		'pto_tokeninput',
		'pto_repeater',
		'pto_charts',
		'pto_fullcal_moment',
		'pto_fullcal',
		'pto_fullcal_locale',
		'pto_formbuilder',
		'pto_formbuilder_render',
		'pto_options',
		'pto_admin_general',
		'pto_admin_dash',
		'pto_admin_messaging',
		'pto_admin_permissions',
		'pto_fields_general',
		'pto_multi_upload',
		'pto_expenses_custom',
		'pto_expenses_ajax',
		'pto_timepicker',
		'pto_upload_avatar',
		'pto_timer',
		'wp-color-picker'
	);
	pto_custom_enqueue_scripts( $scripts );
	$styles = array(
		'pto_fontawesome',
		'pto_admin_styles',
		'pto_jquery_ui_styles',
		'pto_colorbox_styles',
		'pto_datatables_styles',
		'pto_formbuilder_styles',
		'pto_fullcal_styles',
		'pto_autocomplete_styles',
		'pto_timepicker_styles',
		'wp-color-picker'
	);
	pto_custom_enqueue_styles( $styles );
}
function pto_custom_enqueue_scripts( $scripts ){
	if( ! is_array( $scripts ) ){ return; }
	foreach ( $scripts as $script ) {
		wp_enqueue_script( $script );
		wp_localize_script( $script, 'localisation', pto_return_localisation() );
	}
}
function pto_custom_enqueue_styles( $styles ){
	if( ! is_array( $styles ) ){ return; }
	foreach ( $styles as $style ) {
		wp_enqueue_style( $style );
	}
}