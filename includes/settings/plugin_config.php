<?php
// Add CQPIM settings page
add_action( 'admin_menu' , 'register_pto_settings_page', 31 ); 
function register_pto_settings_page() {
	$mypage = add_submenu_page(	
				'pto-dashboard',
				__('Settings', 'cqpim'),  			
				__('Settings', 'cqpim'),  			
				'edit_cqpim_settings', 				
				'pto-settings', 		
				'pto_settings'
	);
	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
	// register settings
	add_action( 'admin_init', 'register_pto_settings' );
}
// Validate uploaded logo
function pto_validate_image($plugin_options) {
	$i = 0; 
	foreach ( $_FILES as $key => $image ) {    
		if ($key == 'company_logo' && $image['size']) { 
			if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) { 
				$override = array('test_form' => false);      
				$file = wp_handle_upload( $image, $override );
				$plugin_options[$key] = $file['url'];  
			} else {             
				$options = get_option('company_logo');       
				$plugin_options[$key] = $options[$logo];          
				wp_die('No image was uploaded.');     
			}  
		} else {     
			$options = get_option('company_logo');     
			if(!empty($options[$key])) {
				$plugin_options[$key] = $options[$key];
			}   
		}   
		$i++; 
	} 
	return $plugin_options;
}
function pto_validate_logo($plugin_options) { 
	$i = 0; 
	foreach ( $_FILES as $key => $image ) {    
		if ($key == 'cqpim_dash_logo' && $image['size']) { 
			if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) { 
				$override = array('test_form' => false);      
				$file = wp_handle_upload( $image, $override );
				$plugin_options[$key] = $file['url'];  
			} else {             
				$options = get_option('cqpim_dash_logo');       
				$plugin_options[$key] = $options[$logo];          
				wp_die('No image was uploaded.');     
			}  
		} else {     
			$options = get_option('cqpim_dash_logo');     
			if(!empty($options[$key])) {
				$plugin_options[$key] = $options[$key];
			}  
		}   
		$i++; 
	} 
	return $plugin_options;
}
function pto_validate_bg($plugin_options) {
	$i = 0; 
	foreach ( $_FILES as $key => $image ) {    
		if ($key == 'cqpim_dash_bg' && $image['size']) { 
			if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) { 
				$override = array('test_form' => false);      
				$file = wp_handle_upload( $image, $override );
				$plugin_options[$key] = $file['url'];  
			} else {             
				$options = get_option('cqpim_dash_bg');       
				$plugin_options[$key] = $options[$logo];          
				wp_die('No image was uploaded.');     
			}  
		} else {     
			$options = get_option('cqpim_dash_bg');     
			if(!empty($options[$key])) {
				$plugin_options[$key] = $options[$key];
			}   
		}   
		$i++; 
	} 
	return $plugin_options;
}
function pto_validate_invlogo($plugin_options) {
	$i = 0; 
	foreach ( $_FILES as $key => $image ) {    
		if ($key == 'cqpim_invoice_logo' && $image['size']) { 
			if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) { 
				$override = array('test_form' => false);      
				$file = wp_handle_upload( $image, $override );
				$plugin_options[$key] = $file['url'];
			} else {             
				$options = get_option('cqpim_invoice_logo');       
				$plugin_options[$key] = $options[$logo];          
				wp_die('No image was uploaded.');     
			}  
		} else {     
			$options = get_option('cqpim_invoice_logo');  
			if(!empty($options[$key])) {
				$plugin_options[$key] = $options[$key];
			}
		}   
		$i++; 
	} 
	return $plugin_options;
}
function register_pto_settings() {
	register_setting( 'cqpim_settings', 'cqpim_plugin_name', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'cqpim_use_default_icon' );
	register_setting( 'cqpim_settings', 'cqpim_show_docs_link' );
	register_setting( 'cqpim_settings', 'cqpim_date_format', '' );
	register_setting( 'cqpim_settings', 'cqpim_allowed_extensions', '' );
	register_setting( 'cqpim_settings', 'cqpim_timezone', '' );
	register_setting( 'cqpim_settings', 'cqpim_disable_avatars', '' );
	register_setting( 'cqpim_settings', 'cqpim_invoice_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_quote_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_project_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_support_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_task_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_bug_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_subs_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_faq_slug', '' );
	register_setting( 'cqpim_settings', 'enable_quotes', '' );
	register_setting( 'cqpim_settings', 'disable_tickets', '' );
	register_setting( 'cqpim_settings', 'cqpim_enable_messaging', '' );
	register_setting( 'cqpim_settings', 'enable_quote_terms', '' );
	register_setting( 'cqpim_settings', 'enable_project_creation', '' );
	register_setting( 'cqpim_settings', 'enable_project_contracts', '' );
	register_setting( 'cqpim_settings', 'disable_invoices', '' );
	register_setting( 'cqpim_settings', 'invoice_workflow', '' );
	register_setting( 'cqpim_settings', 'auto_send_invoices', '' );
	// Company Settings
	register_setting( 'cqpim_settings', 'team_type' );
	register_setting( 'cqpim_settings', 'company_name', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_address', '' );
	register_setting( 'cqpim_settings', 'company_postcode', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_telephone', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_sales_email', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_accounts_email', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_support_email', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'cqpim_cc_address', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_logo', 'pto_validate_image' );
	register_setting( 'cqpim_settings', 'company_bank_name', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'currency_symbol', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'currency_symbol_position');
	register_setting( 'cqpim_settings', 'currency_symbol_space');
	register_setting( 'cqpim_settings', 'currency_decimal');
	register_setting( 'cqpim_settings', 'allow_client_currency_override', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'allow_project_currency_override', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'allow_quote_currency_override', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'allow_invoice_currency_override', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'allow_supplier_currency_override', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'currency_code' );
	register_setting( 'cqpim_settings', 'company_bank_ac', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_bank_sc', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_bank_iban', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_invoice_terms', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'sales_tax_rate', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'sales_tax_name', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'sales_tax_reg', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'secondary_sales_tax_rate', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'secondary_sales_tax_name', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'secondary_sales_tax_reg', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_number', 'sanitize_text_field' );
	// Business Hours
	register_setting( 'cqpim_settings', 'pto_opening');
	register_setting( 'cqpim_settings', 'pto_support_open_message');
	register_setting( 'cqpim_settings', 'pto_support_closed_message');
	register_setting( 'cqpim_settings', 'pto_support_opening_warning');
	register_setting( 'cqpim_settings', 'pto_shortcode_open_message');
	register_setting( 'cqpim_settings', 'pto_shortcode_closed_message');
	// Lead Settings
	register_setting( 'cqpim_settings', 'new_lead_email_subject');
	register_setting( 'cqpim_settings', 'new_lead_email_content');
	// Client Settings
	register_setting( 'cqpim_settings', 'client_dashboard_type' );
	register_setting( 'cqpim_settings', 'cqpim_login_reg' );
	register_setting( 'cqpim_settings', 'cqpim_login_reg_company' );
	register_setting( 'cqpim_settings', 'pto_dcreg_approve' );
	register_setting( 'cqpim_settings', 'auto_welcome' );
	register_setting( 'cqpim_settings', 'auto_welcome_subject' );
	register_setting( 'cqpim_settings', 'auto_welcome_content' );
	register_setting( 'cqpim_settings', 'client_password_reset_subject' );
	register_setting( 'cqpim_settings', 'client_password_reset_content' );
	register_setting( 'cqpim_settings', 'password_reset_subject' );
	register_setting( 'cqpim_settings', 'password_reset_content' );
	register_setting( 'cqpim_settings', 'added_contact_subject' );
	register_setting( 'cqpim_settings', 'added_contact_content' );
	register_setting( 'cqpim_settings', 'allow_client_settings' );
	register_setting( 'cqpim_settings', 'allow_client_users' );
	register_setting( 'cqpim_settings', 'cqpim_dash_logo', 'pto_validate_logo' );
	register_setting( 'cqpim_settings', 'cqpim_dash_bg', 'pto_validate_bg' );
	// Quote Settings
	register_setting( 'cqpim_settings', 'enable_frontend_anon_quotes' );
	register_setting( 'cqpim_settings', 'enable_client_quotes' );
	register_setting( 'cqpim_settings', 'quote_header' );
	register_setting( 'cqpim_settings', 'quote_footer' );
	register_setting( 'cqpim_settings', 'quote_acceptance_text' );
	register_setting( 'cqpim_settings', 'quote_email_subject', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'quote_default_email' );
	// Project Settings
	register_setting( 'cqpim_settings', 'default_contract_text' );
	register_setting( 'cqpim_settings', 'contract_acceptance_text' );
	register_setting( 'cqpim_settings', 'client_contract_subject' );
	register_setting( 'cqpim_settings', 'client_contract_email' );
	register_setting( 'cqpim_settings', 'client_update_subject' );
	register_setting( 'cqpim_settings', 'client_update_email' );
	register_setting( 'cqpim_settings', 'client_message_subject' );
	register_setting( 'cqpim_settings', 'client_message_email' );
	register_setting( 'cqpim_settings', 'company_message_subject' );
	register_setting( 'cqpim_settings', 'company_message_email' );
	register_setting( 'cqpim_settings', 'auto_contract' );
	register_setting( 'cqpim_settings', 'auto_invoice' );
	register_setting( 'cqpim_settings', 'auto_update' );
	register_setting( 'cqpim_settings', 'auto_completion' );
	// Invoice Settings
	register_setting( 'cqpim_settings', 'cqpim_invoice_template' );
	register_setting( 'cqpim_settings', 'cqpim_invoice_prefix' );
	register_setting( 'cqpim_settings', 'cqpim_invoice_logo', 'pto_validate_invlogo' );
	register_setting( 'cqpim_settings', 'cqpim_clean_main_colour');
	register_setting( 'cqpim_settings', 'cqpim_cool_main_colour');
	register_setting( 'cqpim_settings', 'client_invoice_email_attach' );
	register_setting( 'cqpim_settings', 'client_invoice_after_send_remind_days' );
	register_setting( 'cqpim_settings', 'client_invoice_before_terms_remind_days' );
	register_setting( 'cqpim_settings', 'client_invoice_after_terms_remind_days' );
	register_setting( 'cqpim_settings', 'client_invoice_high_priority' );
	register_setting( 'cqpim_settings', 'client_invoice_paypal_address' );
	register_setting( 'cqpim_settings', 'client_invoice_stripe_key' );
	register_setting( 'cqpim_settings', 'client_invoice_stripe_secret' );
	register_setting( 'cqpim_settings', 'client_invoice_stripe_ideal' );
	register_setting( 'cqpim_settings', 'client_invoice_subject' );
	register_setting( 'cqpim_settings', 'client_invoice_email' );
	register_setting( 'cqpim_settings', 'client_deposit_invoice_subject' );
	register_setting( 'cqpim_settings', 'client_deposit_invoice_email' );
	register_setting( 'cqpim_settings', 'client_invoice_reminder_subject' );
	register_setting( 'cqpim_settings', 'client_invoice_reminder_email' );
	register_setting( 'cqpim_settings', 'client_invoice_overdue_subject' );
	register_setting( 'cqpim_settings', 'client_invoice_overdue_email' );
	register_setting( 'cqpim_settings', 'client_invoice_footer' );
	register_setting( 'cqpim_settings', 'client_deposit_invoice_email' );
	register_setting( 'cqpim_settings', 'client_invoice_allow_partial');
	register_setting( 'cqpim_settings', 'client_invoice_twocheck_sid');
	register_setting( 'cqpim_settings', 'client_invoice_receipt_subject' );
	register_setting( 'cqpim_settings', 'client_invoice_receipt_email' );
	// Teams
	register_setting( 'cqpim_settings', 'team_account_subject' );
	register_setting( 'cqpim_settings', 'team_account_email' );	
	register_setting( 'cqpim_settings', 'team_reset_subject' );
	register_setting( 'cqpim_settings', 'team_reset_email' );
	register_setting( 'cqpim_settings', 'team_project_subject' );
	register_setting( 'cqpim_settings', 'team_project_email' );	
	register_setting( 'cqpim_settings', 'team_assignment_subject' );
	register_setting( 'cqpim_settings', 'team_assignment_email' );	
	// Support
	register_setting( 'cqpim_settings', 'cqpim_disable_ticket_priority' );
	register_setting( 'cqpim_settings', 'client_create_ticket_subject' );
	register_setting( 'cqpim_settings', 'client_create_ticket_email' );
	register_setting( 'cqpim_settings', 'client_update_ticket_subject' );
	register_setting( 'cqpim_settings', 'client_update_ticket_email' );
	register_setting( 'cqpim_settings', 'company_update_ticket_subject' );
	register_setting( 'cqpim_settings', 'company_update_ticket_email' );
	// FAQ 
	register_setting( 'cqpim_settings', 'cqpim_enable_faq' );
	register_setting( 'cqpim_settings', 'cqpim_enable_faq_dash_accordion' );
	register_setting( 'cqpim_settings', 'cqpim_enable_faq_dash_cats' );
	register_setting( 'cqpim_settings', 'cqpim_enable_faq_dash' );
	// Quote Forms
	register_setting( 'cqpim_settings', 'cqpim_frontend_form' );
	register_setting( 'cqpim_settings', 'cqpim_backend_form' );
	register_setting( 'cqpim_settings', 'form_reg_auto_welcome' );
	register_setting( 'cqpim_settings', 'form_auto_welcome' );
	register_setting( 'cqpim_settings', 'new_quote_subject' );
	register_setting( 'cqpim_settings', 'new_quote_email' );
	register_setting( 'cqpim_settings', 'cqpim_dash_css' );
	register_setting( 'cqpim_settings', 'cqpim_logout_url' );
	register_setting( 'cqpim_settings', 'gdpr_pp_page' );
	register_setting( 'cqpim_settings', 'gdpr_pp_page_check' );
	register_setting( 'cqpim_settings', 'gdpr_tc_page' );					
	register_setting( 'cqpim_settings', 'gdpr_tc_page_check' );
	register_setting( 'cqpim_settings', 'gdpr_consent_text' );						
	register_setting( 'cqpim_settings', 'gdpr_consent' );
	register_setting( 'cqpim_settings', 'pto_cquo_approve' );
	register_setting( 'cqpim_settings', 'pto_creg_approve' );
	// Suppliers and Expenses
	register_setting( 'cqpim_settings', 'cqpim_activate_expense_auth' );
	register_setting( 'cqpim_settings', 'cqpim_expense_auth_limit' );
	register_setting( 'cqpim_settings', 'cqpim_expense_auth_members' );
	register_setting( 'cqpim_settings', 'cqpim_auth_email_subject' );
	register_setting( 'cqpim_settings', 'cqpim_auth_email_content' );
	register_setting( 'cqpim_settings', 'cqpim_authorised_email_subject' );
	register_setting( 'cqpim_settings', 'cqpim_authorised_email_content' );
	// Bug Tracker
	register_setting( 'cqpim_settings', 'cqpim_bugs_auto' );
	register_setting( 'cqpim_settings', 'cqpim_new_bug_subject' );
	register_setting( 'cqpim_settings', 'cqpim_new_bug_content' );
	register_setting( 'cqpim_settings', 'cqpim_update_bug_subject' );
	register_setting( 'cqpim_settings', 'cqpim_update_bug_content' );
	// WooCommerce
	register_setting( 'cqpim_settings', 'cqpim_wc_new_project_subject' );
	register_setting( 'cqpim_settings', 'cqpim_wc_new_project_content' );
	// Piping Settings 
	register_setting( 'cqpim_settings', 'cqpim_mail_server' );
	register_setting( 'cqpim_settings', 'cqpim_piping_address' );
	register_setting( 'cqpim_settings', 'cqpim_mailbox_name' );
	register_setting( 'cqpim_settings', 'cqpim_mailbox_pass' );
	register_setting( 'cqpim_settings', 'cqpim_string_prefix' );
	register_setting( 'cqpim_settings', 'cqpim_create_support_on_email' );
	register_setting( 'cqpim_settings', 'cqpim_send_piping_reject' );
	register_setting( 'cqpim_settings', 'cqpim_piping_delete' );
	register_setting( 'cqpim_settings', 'cqpim_bounce_subject' );
	register_setting( 'cqpim_settings', 'cqpim_bounce_content' );
	// Messaging  Settings
	register_setting( 'cqpim_settings', 'cqpim_new_message_subject');
	register_setting( 'cqpim_settings', 'cqpim_new_message_content');
	register_setting( 'cqpim_settings', 'cqpim_messages_allow_client');
	// HTML Email
	register_setting( 'cqpim_settings', 'cqpim_html_email_styles');
	register_setting( 'cqpim_settings', 'cqpim_html_email');
	// Subscriptins
	register_setting( 'cqpim_settings', 'cqpim_new_subscription_subject');
	register_setting( 'cqpim_settings', 'cqpim_new_subscription_content');
	register_setting( 'cqpim_settings', 'cqpim_new_subscription_accept_subject');
	register_setting( 'cqpim_settings', 'cqpim_new_subscription_accept_content');
	register_setting( 'cqpim_settings', 'cqpim_subscription_cancelled_subject');
	register_setting( 'cqpim_settings', 'cqpim_subscription_cancelled_content');
	register_setting( 'cqpim_settings', 'cqpim_subscription_failed_subject');
	register_setting( 'cqpim_settings', 'cqpim_subscription_failed_content');
	register_setting( 'cqpim_settings', 'cqpim_subscription_reminder_subject');
	register_setting( 'cqpim_settings', 'cqpim_subscription_reminder_content');
	register_setting( 'cqpim_settings', 'cqpim_paypal_api_signature');
	register_setting( 'cqpim_settings', 'cqpim_paypal_api_password');
	register_setting( 'cqpim_settings', 'cqpim_paypal_api_username');
	register_setting( 'cqpim_settings', 'cqpim_twocheck_pub_key');
	register_setting( 'cqpim_settings', 'cqpim_twocheck_priv_key');
	register_setting( 'cqpim_settings', 'cqpim_twocheck_account');
}
// Allow CQPIM admins access to these settings
function pto_settings_page_capability( $capability ) {
	return 'edit_cqpim_settings';
}
add_filter( 'option_page_capability_cqpim_settings', 'pto_settings_page_capability' );
function pto_settings() { ?>
	<div class="wrap" id="cqpim-settings"><div id="icon-tools" class="icon32"></div>
		<h1><?php _e('Settings', 'cqpim'); ?></h1>
	<?php 
	$user = wp_get_current_user();
	if(in_array('administrator', $user->roles)) { 
		if(pto_get_team_from_userid($user) == false) { ?>
			<div class="cqpim-dash-item-full grid-item">
				<div class="cqpim_block cqpim-alert cqpim-alert-warning">
					<div style="padding:20px">	
						<h3><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php _e('You need to link your account to a Team Member', 'cqpim'); ?></h3>
						<p><?php _e('It would appear that the Wordpress Administrator account that you are logged in with is not related to a Team Member. In order for the plugin to work correctly, you need to add a Team Member that is linked to your WP User Account.', 'cqpim'); ?></p>
						<p><?php _e('We can do this for you though, just click Create Linked Team Member. You will then be able to add other team members or just work with this account.', 'cqpim') ?></p>
						<button style="margin-left:0" id="create_linked_team" class="cqpim_button bg-amber font-white rounded_2 left" data-uid="<?php echo $user->ID; ?>"><?php $text = __('Create Linked Team Member'); _e('Create Linked Team Member'); ?></button>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		<?php }
	}		
	?>
	<form method="post" action="options.php" enctype="multipart/form-data">
			<div id="main-container" class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<i class="fa fa-cog font-green-sharp" aria-hidden="true"></i>
						<span class="caption-subject font-green-sharp sbold"><?php _e('Plugin Settings', 'cqpim'); ?> </span>
					</div>
				</div>
				<?php 
				$option_group = 'cqpim_settings';
				settings_fields( $option_group ); ?>
				<div id="tabs">
					<ul>
						<li><a href="#tabs-11"><?php _e('Plugin Settings', 'cqpim'); ?></a></li>
						<li><a href="#tabs-1"><?php _e('Your Company', 'cqpim'); ?></a></li>
						<li><a href="#tabs-19"><?php _e('Business Hours', 'cqpim'); ?></a></li>
						<li><a href="#tabs-23"><?php _e('Leads', 'cqpim'); ?></a></li>
						<li><a href="#tabs-2"><?php _e('Clients', 'cqpim'); ?></a></li>
						<li><a href="#tabs-9"><?php _e('Client Dashboard', 'cqpim'); ?></a></li>
						<li><a href="#tabs-3"><?php _e('Quotes / Estimates', 'cqpim'); ?></a></li>
						<li><a href="#tabs-4"><?php _e('Projects', 'cqpim'); ?></a></li>
						<li><a href="#tabs-18"><?php _e('Bug Tracker', 'cqpim'); ?></a></li>
						<li><a href="#tabs-5"><?php _e('Invoices', 'cqpim'); ?></a></li>
						<li><a href="#tabs-20"><?php _e('Subscriptions', 'cqpim'); ?></a></li>
						<?php if(0) { ?>
						<li><a href="#tabs-22"><?php _e('WooCommerce', 'cqpim'); ?></a></li>
						<?php } ?>
						<li><a href="#tabs-21"><?php _e('API / External Services', 'cqpim'); ?></a></li>						
						<li><a href="#tabs-6"><?php _e('Team Members', 'cqpim'); ?></a></li>
						<li><a href="#tabs-10"><?php _e('Tasks', 'cqpim'); ?></a></li>								
						<li><a href="#tabs-7"><?php _e('Support Tickets', 'cqpim'); ?></a></li>
						<li><a href="#tabs-24"><?php _e('FAQ', 'cqpim'); ?></a></li>
						<li><a href="#tabs-8"><?php _e('Forms', 'cqpim'); ?></a></li>
						<li><a href="#tabs-16"><?php _e('Suppliers / Expenses', 'cqpim'); ?></a></li>
						<li><a href="#tabs-17"><?php _e('Reporting', 'cqpim'); ?></a></li>
						<li><a href="#tabs-12"><?php _e('Email Piping', 'cqpim'); ?></a></li>
						<li><a href="#tabs-14"><?php _e('Messaging System', 'cqpim'); ?></a></li>
						<li><a href="#tabs-15"><?php _e('HTML Email Template', 'cqpim'); ?></a></li>
						<?php 
						$user = wp_get_current_user();
						if(in_array('administrator', $user->roles)) { ?>
							<li><a href="#tabs-13"><?php _e('Plugin Reset', 'cqpim'); ?></a></li>
						<?php } ?>
					</ul>
					<div id="tabs-11">
						<h3><?php _e('Plugin Name', 'cqpim'); ?></h3>
						<p><?php _e('If you\'d like to rename the plugin in the admin menu, you can do so here.', 'cqpim'); ?></p>
						<table>
							<tr>
								<td class="title"><?php _e('Plugin Name:', 'cqpim'); ?></td>
								<td>
									<?php $value = get_option('cqpim_plugin_name'); ?>
									<input type="text" name="cqpim_plugin_name" value="<?php echo $value?$value:'Projectopia'; ?>" />
								</td>
							</tr>
						</table>
						<h3><?php _e('Plugin Icon', 'cqpim'); ?></h3>
						<table>
							<tr>
								<td class="title"><?php _e('Use WordPress Default Admin Menu Icon (Cog)', 'cqpim'); ?></td>
								<td>
									<?php $disable = get_option('cqpim_use_default_icon'); ?>
									<input type="checkbox" name="cqpim_use_default_icon" value="1" <?php if(!empty($disable)) { ?> checked="checked"<?php } ?>/>
								</td>
							</tr>
						</table>	
						<h3><?php _e('Documentation Link', 'cqpim'); ?></h3>
						<table>
							<tr>
								<td class="title"><?php _e('Show Documentation Link in Admin Menu?', 'cqpim'); ?></td>
								<td>
									<?php $disable = get_option('cqpim_show_docs_link'); ?>
									<input type="checkbox" name="cqpim_show_docs_link" value="1" <?php if(!empty($disable)) { ?> checked="checked"<?php } ?>/>
								</td>
							</tr>
						</table>						
						<h3><?php _e('Date', 'cqpim'); ?></h3>
						<table>
							<tr>
								<td class="title"><?php _e('Date Format:', 'cqpim'); ?></td>
								<td>
									<?php $value = get_option('cqpim_date_format'); ?>
									<select name="cqpim_date_format">
										<option value=""><?php _e('Choose a date format', 'cqpim'); ?></option>
										<option value="Y-m-d" <?php selected( $value, "Y-m-d" ); ?>>Y-m-d (<?php echo date('Y-m-d'); ?>)</option>
										<option value="m/d/Y" <?php selected( $value, "m/d/Y" ); ?>>m/d/Y (<?php echo date('m/d/Y'); ?>)</option>
										<option value="d/m/Y" <?php selected( $value, "d/m/Y" ); ?>>d/m/Y (<?php echo date('d/m/Y'); ?>)</option>
										<option value="d.m.Y" <?php selected( $value, "d.m.Y" ); ?>>d.m.Y (<?php echo date('d.m.Y'); ?>)</option>
									</select>
								</td>
							</tr>
						</table>
						<h3><?php _e('Manage Categories', 'cqpim'); ?></h3>
						<a href="<?php echo admin_url(); ?>edit-tags.php?taxonomy=cqpim_project_cat"><?php _e('Manage Project Categories', 'cqpim'); ?></a><br />
						<a href="<?php echo admin_url(); ?>edit-tags.php?taxonomy=cqpim_client_cat"><?php _e('Manage Client Categories', 'cqpim'); ?></a><br />
						<a href="<?php echo admin_url(); ?>edit-tags.php?taxonomy=cqpim_lead_cat"><?php _e('Manage Lead Categories', 'cqpim'); ?></a><br />
						<a href="<?php echo admin_url(); ?>edit-tags.php?taxonomy=cqpim_faq_cat"><?php _e('Manage FAQ Categories', 'cqpim'); ?></a>
						<?php if(pto_check_addon_status('expenses')) { ?>
							<br /><a href="<?php echo admin_url(); ?>edit-tags.php?taxonomy=cqpim_supplier_cat"><?php _e('Manage Supplier Categories', 'cqpim'); ?></a><br />
							<a href="<?php echo admin_url(); ?>edit-tags.php?taxonomy=cqpim_expense_cat"><?php _e('Manage Expense Categories', 'cqpim'); ?></a>
						<?php } ?>
						<?php if(pto_check_addon_status('subscriptions')) { ?>
							<br /><a href="<?php echo admin_url(); ?>edit-tags.php?taxonomy=cqpim_plan_cat"><?php _e('Manage Subscription Plan Categories', 'cqpim'); ?></a><br />
							<a href="<?php echo admin_url(); ?>edit-tags.php?taxonomy=cqpim_subscription_cat"><?php _e('Manage Subscription Categories', 'cqpim'); ?></a>						
						<?php } ?>
						<h3><?php _e('Avatars', 'cqpim'); ?></h3>
						<p><?php _e('Projectopia uses the WordPress avatar to show thumbnails of users. By default this uses Gravatar.org, but you can also use plugins to upload custom avatars.', 'cqpim'); ?></p>
						<table>
							<tr>
								<td class="title"><?php _e('Disable Avatars', 'cqpim'); ?></td>
								<td>
									<?php $disable = get_option('cqpim_disable_avatars'); ?>
									<input type="checkbox" name="cqpim_disable_avatars" value="1" <?php if(!empty($disable)) { ?> checked="checked"<?php } ?>/>
								</td>
							</tr>
						</table>
						<h3><?php _e('URL Rewrites', 'cqpim'); ?></h3>
						<p><?php _e('By default, invoices, quotes and projects will have Projectopia based URL\'s. This is done for compatibility so that you don\'t end up with duplicate slugs in Wordpress. You can change these here. Make sure that the URL slug that you choose does not exist on your site already, and also make sure that you flush your permalinks when these are updated, otherwise you may experience 404 errors.', 'cqpim'); ?></p>
						<table>
							<tr>
								<td class="title"><?php _e('Invoices:', 'cqpim'); ?></td>
								<td>
									<?php $value = get_option('cqpim_invoice_slug'); ?>
									<input type="text" name="cqpim_invoice_slug" value="<?php echo $value; ?>" />
								</td>
							</tr>
							<tr>
								<td class="title"><?php _e('Quotes:', 'cqpim'); ?></td>
								<td>
									<?php $value = get_option('cqpim_quote_slug'); ?>
									<input type="text" name="cqpim_quote_slug" value="<?php echo $value; ?>" />
								</td>
							</tr>
							<tr>
								<td class="title"><?php _e('Projects:', 'cqpim'); ?></td>
								<td>
									<?php $value = get_option('cqpim_project_slug'); ?>
									<input type="text" name="cqpim_project_slug" value="<?php echo $value; ?>" />
								</td>
							</tr>
							<tr>
								<td class="title"><?php _e('Support Tickets:', 'cqpim'); ?></td>
								<td>
									<?php $value = get_option('cqpim_support_slug'); ?>
									<input type="text" name="cqpim_support_slug" value="<?php echo $value; ?>" />
								</td>
							</tr>
							<tr>
								<td class="title"><?php _e('Tasks:', 'cqpim'); ?></td>
								<td>
									<?php $value = get_option('cqpim_task_slug'); ?>
									<input type="text" name="cqpim_task_slug" value="<?php echo $value; ?>" />
								</td>
							</tr>
							<tr>
								<td class="title"><?php _e('FAQ:', 'cqpim'); ?></td>
								<td>
									<?php $value = get_option('cqpim_faq_slug'); ?>
									<input type="text" name="cqpim_faq_slug" value="<?php echo $value; ?>" />
								</td>
							</tr>
							<?php if(pto_check_addon_status('bugs')) { ?>
								<tr>
									<td class="title"><?php _e('Bugs:', 'cqpim'); ?></td>
									<td>
										<?php $value = get_option('cqpim_bug_slug'); ?>
										<input type="text" name="cqpim_bug_slug" value="<?php echo $value; ?>" />
									</td>
								</tr>
							<?php } ?>
							<?php if(pto_check_addon_status('subscriptions')) { ?>
								<tr>
									<td class="title"><?php _e('Subscriptions:', 'cqpim'); ?></td>
									<td>
										<?php $value = get_option('cqpim_subs_slug'); ?>
										<input type="text" name="cqpim_subs_slug" value="<?php echo $value; ?>" />
									</td>
								</tr>
							<?php } ?>
						</table>							
						<div class="clear"></div>
						<h3><?php _e('Workflow', 'cqpim'); ?></h3>
						<h4><?php _e('Project Workflow', 'cqpim'); ?></h4>
						<?php $checked = get_option('enable_quotes'); ?>
						<input type="checkbox" name="enable_quotes" value="1" <?php checked($checked, 1, true); ?> /> <?php _e('Enable the quotes system.', 'cqpim'); ?><br /><br />
						<?php $checked = get_option('enable_quote_terms'); ?>
						<input type="checkbox" name="enable_quote_terms" value="1" <?php checked($checked, 1, true); ?> /> <?php _e('Add Terms & Conditions section to quotes.', 'cqpim'); ?><br /><br />
						<?php $checked = get_option('enable_project_creation'); ?>
						<input type="checkbox" name="enable_project_creation" value="1" <?php checked($checked, 1, true); ?> /> <?php _e('Create a project automatically when a quote is accepted.', 'cqpim'); ?><br /><br />
						<?php $checked = get_option('enable_project_contracts'); ?>
						<input type="checkbox" name="enable_project_contracts" value="1" <?php checked($checked, 1, true); ?> /> <?php _e('Enable the contracts feature in projects.', 'cqpim'); ?><br /><br />
						<?php $checked = get_option('auto_contract'); ?>
						<input type="checkbox" name="auto_contract" id="auto_contract" value="1" <?php checked($checked, 1, true); ?> /> <?php _e('Send the project contract automatically when a quote is accepted and the project is created.', 'cqpim'); ?>
						<br /><br />
						<h4><?php _e('Invoice Workflow', 'cqpim'); ?></h4>
						<?php $checked = get_option('disable_invoices'); ?>
						<input type="checkbox" name="disable_invoices" id="disable_invoices" value="1" <?php checked($checked, 1, true); ?> /> <?php _e('Disable the Invoice section.', 'cqpim'); ?><br /><br />
						<?php $checked = get_option('invoice_workflow'); ?>
						<table>
							<tr><td><input type="radio" name="invoice_workflow" value="0" <?php checked($checked, 0, true); ?> /></td><td><?php _e('Create deposit invoice (if deposit amount selected) automatically when contract is signed (contract mode only) or when project is created from quote (no contract mode). Create a completion invoice (project total minus deposit) when project is marked as signed off.', 'cqpim'); ?></td></tr>
							<tr><td colspan="2"></td></tr>
							<tr><td><input type="radio" name="invoice_workflow" value="1" <?php checked($checked, 1, true); ?> /></td><td><?php _e('Create deposit invoice (if deposit amount selected) automatically when contract is signed (contract mode only) or when project is created from quote (no contract mode). Create a new invoice when milestones are marked as complete for the total milestone fee minus the deposit percentage.', 'cqpim'); ?></td></tr>
						</table>
						<br />
						<?php $checked = get_option('auto_send_invoices'); ?>
						<input type="checkbox" name="auto_send_invoices" value="1" <?php checked($checked, 1, true); ?> /> <?php _e('Send invoices to the client automatically when they are created.', 'cqpim'); ?>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>						
					</div>
					<div id="tabs-1">
						<table>
							<tr>
								<td colspan="2"><h3><?php _e('Company Details', 'cqpim'); ?></h3></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Company Name:', 'cqpim'); ?></td>
								<td><input type="text" id="company_name" name="company_name" value="<?php echo get_option('company_name'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Company Address:', 'cqpim'); ?></td>
								<td><textarea id="company_address" name="company_address"><?php echo get_option('company_address'); ?></textarea></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Company Postcode:', 'cqpim'); ?></td>
								<td><input type="text" id="company_postcode" name="company_postcode" value="<?php echo get_option('company_postcode'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Company Telephone:', 'cqpim'); ?></td>
								<td><input type="text" id="company_telephone" name="company_telephone" value="<?php echo get_option('company_telephone'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Sales Email:', 'cqpim'); ?></td>
								<td><input type="text" id="company_sales_email" name="company_sales_email" value="<?php echo get_option('company_sales_email'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Accounts Email:', 'cqpim'); ?></td>
								<td><input type="text" id="company_accounts_email" name="company_accounts_email" value="<?php echo get_option('company_accounts_email'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Support Email (For Support Tickets):', 'cqpim'); ?></td>
								<td><input type="text" id="company_support_email" name="company_support_email" value="<?php echo get_option('company_support_email'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Outgoing Email BCC Address (copies all outgoing emails):', 'cqpim'); ?></td>
								<td><input type="text" id="cqpim_cc_address" name="cqpim_cc_address" value="<?php echo get_option('cqpim_cc_address'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Company Number:', 'cqpim'); ?></td>
								<td><input type="text" id="company_number" name="company_number" value="<?php echo get_option('company_number'); ?>" /></td>
							</tr>
							<tr>
								<td colspan="2"><h3><?php _e('Company Logo:', 'cqpim'); ?></h3></td>
							</tr>
							<?php 
							$logo = get_option('company_logo'); 
							if($logo) { ?>
							<tr>
								<td colspan="2"><img style="max-width:400px; margin:20px 0; background:#ececec" src="<?php echo $logo['company_logo']; ?>" />
								<br />
								<button class="remove_logo cqpim_button mt-20 op bg-red font-white rounded_2" data-type="company_logo"><?php _e('Remove', 'cqpim'); ?></button>
								<br /><br />
								</td>
							</tr>
							<?php } ?>
							<tr>
								<td colspan="2"><input type="file" name="company_logo" /></td>
							</tr>
							<tr>
								<td colspan="2"><h3><?php _e('Financial Details', 'cqpim'); ?></h3></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Currency Symbol:', 'cqpim'); ?></td>
								<td><input type="text" id="currency_symbol" name="currency_symbol" value="<?php echo get_option('currency_symbol'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Currency Symbol Position:', 'cqpim'); ?></td>
								<td>
									<?php
										$value = get_option('currency_symbol_position');
									?>
									<select name="currency_symbol_position">
										<option value="l" <?php if($value == 'l') { echo 'selected="selected"'; } ?>><?php _e('Before Amount', 'cqpim'); ?></option>
										<option value="r" <?php if($value == 'r') { echo 'selected="selected"'; } ?>><?php _e('After Amount', 'cqpim'); ?></option>
									</select>
								</td>
							</tr>								
							<tr style="margin:5px 0">
								<?php
									$value = get_option('currency_symbol_space');
								?>
								<td class="title"><?php _e('Add Space Between Amount and Currency Symbol', 'cqpim'); ?></td>
								<td><input type="checkbox" id="currency_symbol_space" name="currency_symbol_space" value="1" <?php if($value == '1') { echo 'checked'; } ?> /></td>
							</tr>
							<tr style="margin:5px 0">
								<?php
									$value = get_option('currency_decimal');
								?>
								<td class="title"><?php _e('Remove Decimals in Currency', 'cqpim'); ?></td>
								<td><input type="checkbox" id="currency_decimal" name="currency_decimal" value="1" <?php if($value == '1') { echo 'checked'; } ?> /></td>
							</tr>
							<tr style="margin:5px 0">
								<?php
									$value = get_option('allow_client_currency_override');
								?>
								<td class="title"><?php _e('Allow Currency to be Set Per Client:', 'cqpim'); ?></td>
								<td><input type="checkbox" id="allow_client_currency_override" name="allow_client_currency_override" value="1" <?php if($value == '1') { echo 'checked'; } ?> /></td>
							</tr>
							<tr style="margin:5px 0">
								<?php
									$value = get_option('allow_quote_currency_override');
								?>
								<td class="title"><?php _e('Allow Currency to be Set Per Quote:', 'cqpim'); ?></td>
								<td><input type="checkbox" id="allow_quote_currency_override" name="allow_quote_currency_override" value="1" <?php if($value == '1') { echo 'checked'; } ?> /></td>
							</tr>
							<tr style="margin:5px 0">
								<?php
									$value = get_option('allow_project_currency_override');
								?>
								<td class="title"><?php _e('Allow Currency to be Set Per Project:', 'cqpim'); ?></td>
								<td><input type="checkbox" id="allow_project_currency_override" name="allow_project_currency_override" value="1" <?php if($value == '1') { echo 'checked'; } ?> /></td>
							</tr>
							<tr style="margin:5px 0">
								<?php
									$value = get_option('allow_invoice_currency_override');
								?>
								<td class="title"><?php _e('Allow Currency to be Set Per Invoice:', 'cqpim'); ?></td>
								<td><input type="checkbox" id="allow_invoice_currency_override" name="allow_invoice_currency_override" value="1" <?php if($value == '1') { echo 'checked'; } ?> /></td>
							</tr>
							<?php if(is_plugin_active('cqpim-expenses/cqpim-expenses.php')) { ?>
								<tr style="margin:5px 0">
									<?php
										$value = get_option('allow_supplier_currency_override');
									?>
									<td class="title"><?php _e('Allow Currency to be Set Per Supplier:', 'cqpim'); ?></td>
									<td><input type="checkbox" id="allow_supplier_currency_override" name="allow_supplier_currency_override" value="1" <?php if($value == '1') { echo 'checked'; } ?> /></td>
								</tr>
							<?php } ?>
							<tr>
								<td class="title"><?php _e('Currency Code (Used for Payment Gateways):', 'cqpim'); ?></td>
								<td>
									<?php $accode = get_option('currency_code'); ?>
									<select name="currency_code" id="currency_code">
										<option value="0"><?php _e('Choose a currency', 'cqpim'); ?></option>
										<?php $codes = pto_return_currency_select();
										foreach($codes as $key => $code) {
											if($key == $accode) { $checked = 'selected="selected"'; } else { $checked = ''; };
											echo '<option value="' . $key . '" ' . $checked . '>' . $code . '</option>';
										}
										?>
									</select>								
								</td>
							</tr>
							<tr>
								<td class="title"><?php _e('Account Name:', 'cqpim'); ?></td>
								<td><input type="text" id="company_bank_name" name="company_bank_name" value="<?php echo get_option('company_bank_name'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Account Number:', 'cqpim'); ?></td>
								<td><input type="text" id="company_bank_ac" name="company_bank_ac" value="<?php echo get_option('company_bank_ac'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Sort Code:', 'cqpim'); ?></td>
								<td><input type="text" id="company_bank_sc" name="company_bank_sc" value="<?php echo get_option('company_bank_sc'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('IBAN:', 'cqpim'); ?></td>
								<td><input type="text" id="company_bank_iban" name="company_bank_iban" value="<?php echo get_option('company_bank_iban'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Invoice Terms:', 'cqpim'); ?></td>
								<td>
								<?php $terms = get_option('company_invoice_terms'); ?>
								<select id="company_invoice_terms" name="company_invoice_terms">
									<option value="1" <?php if($terms == 1) { echo 'selected'; } ?>><?php $text = __('Due on Receipt', 'cqpim'); _e('Due on Receipt', 'cqpim'); ?></option>
									<option value="7" <?php if($terms == 7) { echo 'selected'; } ?>>7 <?php $text = __('days', 'cqpim'); _e('days', 'cqpim'); ?></option>
									<option value="14" <?php if($terms == 14) { echo 'selected'; } ?>>14 <?php $text = __('days', 'cqpim'); _e('days', 'cqpim'); ?></option>
									<option value="28" <?php if($terms == 28) { echo 'selected'; } ?>>28 <?php $text = __('days', 'cqpim'); _e('days', 'cqpim'); ?></option>
									<option value="30" <?php if($terms == 30) { echo 'selected'; } ?>>30 <?php $text = __('days', 'cqpim'); _e('days', 'cqpim'); ?></option>
									<option value="60" <?php if($terms == 60) { echo 'selected'; } ?>>60 <?php $text = __('days', 'cqpim'); _e('days', 'cqpim'); ?></option>
									<option value="90" <?php if($terms == 90) { echo 'selected'; } ?>>90 <?php $text = __('days', 'cqpim'); _e('days', 'cqpim'); ?></option>
								</select>
								</td>
							</tr>
							<tr>
								<td colspan="2"><h3><?php _e('Sales Tax', 'cqpim'); ?></h3></td>
							</tr>
							<tr>
								<td colspan="2"><?php _e('These settings apply to sales tax, such as VAT. If you do not charge sales tax, then leave these fields blank.', 'cqpim'); ?></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Tax Percentage (eg. 20):', 'cqpim'); ?></td>
								<td><input type="text" name="sales_tax_rate" id="sales_tax_rate" value="<?php echo get_option('sales_tax_rate'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Tax Name (eg. VAT):', 'cqpim'); ?></td>
								<td><input type="text" name="sales_tax_name" id="sales_tax_name" value="<?php echo get_option('sales_tax_name'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Tax Reg Number:', 'cqpim'); ?></td>
								<td><input type="text" name="sales_tax_reg" id="sales_tax_reg" value="<?php echo get_option('sales_tax_reg'); ?>" /></td>
							</tr>
							<tr>
								<td colspan="2"><h3><?php _e('Secondary Sales Tax', 'cqpim'); ?></h3></td>
							</tr>
							<tr>
								<td colspan="2"><?php _e('These settings apply to a secondary sales tax. If you do not charge a secondary sales tax, then leave these fields blank.', 'cqpim'); ?></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Secondary  Tax Percentage (eg. 20):', 'cqpim'); ?></td>
								<td><input type="text" name="secondary_sales_tax_rate" id="secondary_sales_tax_rate" value="<?php echo get_option('secondary_sales_tax_rate'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Secondary  Tax Name (eg. VAT):', 'cqpim'); ?></td>
								<td><input type="text" name="secondary_sales_tax_name" id="secondary_sales_tax_name" value="<?php echo get_option('secondary_sales_tax_name'); ?>" /></td>
							</tr>
							<tr>
								<td class="title"><?php _e('Secondary  Tax Reg Number:', 'cqpim'); ?></td>
								<td><input type="text" name="secondary_sales_tax_reg" id="secondary_sales_tax_reg" value="<?php echo get_option('secondary_sales_tax_reg'); ?>" /></td>
							</tr>
						</table>
						<div class="clear"></div>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>					
					</div>
					<div id="tabs-19">
						<h3><?php _e('Business Hours', 'cqpim'); ?></h3>
						<?php $business = get_option('pto_opening'); ?>
						<table class="pto_business_hours">
							<thead>
								<tr>
									<th><?php _e('Day', 'cqpim'); ?></th>
									<th><?php _e('Opening Time', 'cqpim'); ?></th>
									<th><?php _e('Closing Time', 'cqpim'); ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><input type="checkbox" name="pto_opening[mon][active]" value="1" <?php checked(1, isset($business['mon']['active']) ? $business['mon']['active'] : 0); ?>/> <?php _e('Monday', 'cqpim'); ?></td>									
									<td><input type="text" name="pto_opening[mon][open]" class="timepicker" value="<?php echo isset($business['mon']['open']) ? $business['mon']['open'] : ''; ?>" autocomplete="off" /></td>
									<td><input type="text" name="pto_opening[mon][close]" class="timepicker" value="<?php echo isset($business['mon']['close']) ? $business['mon']['close'] : ''; ?>" autocomplete="off" /></td>
								</tr>
								<tr>
									<td><input type="checkbox" name="pto_opening[tue][active]" value="1" <?php checked(1, isset($business['tue']['active']) ? $business['tue']['active'] : 0); ?>/> <?php _e('Tuesday', 'cqpim'); ?></td>									
									<td><input type="text" name="pto_opening[tue][open]" class="timepicker" value="<?php echo isset($business['tue']['open']) ? $business['tue']['open'] : ''; ?>" autocomplete="off" /></td>
									<td><input type="text" name="pto_opening[tue][close]" class="timepicker" value="<?php echo isset($business['tue']['close']) ? $business['tue']['close'] : ''; ?>" autocomplete="off" /></td>
								</tr>
								<tr>
									<td><input type="checkbox" name="pto_opening[wed][active]" value="1" <?php checked(1, isset($business['wed']['active']) ? $business['wed']['active'] : 0); ?>/> <?php _e('Wednesday', 'cqpim'); ?></td>									
									<td><input type="text" name="pto_opening[wed][open]" class="timepicker" value="<?php echo isset($business['wed']['open']) ? $business['wed']['open'] : ''; ?>" autocomplete="off" /></td>
									<td><input type="text" name="pto_opening[wed][close]" class="timepicker" value="<?php echo isset($business['wed']['close']) ? $business['wed']['close'] : ''; ?>" autocomplete="off" /></td>
								</tr>
								<tr>
									<td><input type="checkbox" name="pto_opening[thu][active]" value="1" <?php checked(1, isset($business['thu']['active']) ? $business['thu']['active'] : 0); ?>/> <?php _e('Thursday', 'cqpim'); ?></td>									
									<td><input type="text" name="pto_opening[thu][open]" class="timepicker" value="<?php echo isset($business['thu']['open']) ? $business['thu']['open'] : ''; ?>" autocomplete="off" /></td>
									<td><input type="text" name="pto_opening[thu][close]" class="timepicker" value="<?php echo isset($business['thu']['close']) ? $business['thu']['close'] : ''; ?>" autocomplete="off" /></td>
								</tr>
								<tr>
									<td><input type="checkbox" name="pto_opening[fri][active]" value="1" <?php checked(1, isset($business['fri']['active']) ? $business['fri']['active'] : 0); ?>/> <?php _e('Friday', 'cqpim'); ?></td>									
									<td><input type="text" name="pto_opening[fri][open]" class="timepicker" value="<?php echo isset($business['fri']['open']) ? $business['fri']['open'] : ''; ?>" autocomplete="off" /></td>
									<td><input type="text" name="pto_opening[fri][close]" class="timepicker" value="<?php echo isset($business['fri']['close']) ? $business['fri']['close'] : ''; ?>" autocomplete="off" /></td>
								</tr>
								<tr>
									<td><input type="checkbox" name="pto_opening[sat][active]" value="1" <?php checked(1, isset($business['sat']['active']) ? $business['sat']['active'] : 0); ?>/> <?php _e('Saturday', 'cqpim'); ?></td>									
									<td><input type="text" name="pto_opening[sat][open]" class="timepicker" value="<?php echo isset($business['sat']['open']) ? $business['sat']['open'] : ''; ?>" autocomplete="off" /></td>
									<td><input type="text" name="pto_opening[sat][close]" class="timepicker" value="<?php echo isset($business['sat']['close']) ? $business['sat']['close'] : ''; ?>" autocomplete="off" /></td>
								</tr>
								<tr>
									<td><input type="checkbox" name="pto_opening[sun][active]" value="1" <?php checked(1, isset($business['sun']['active']) ? $business['sun']['active'] : 0); ?>/> <?php _e('Sunday', 'cqpim'); ?></td>									
									<td><input type="text" name="pto_opening[sun][open]" class="timepicker" value="<?php echo isset($business['sun']['open']) ? $business['sun']['open'] : ''; ?>" autocomplete="off" /></td>
									<td><input type="text" name="pto_opening[sun][close]" class="timepicker" value="<?php echo isset($business['sun']['close']) ? $business['sun']['close'] : ''; ?>" autocomplete="off" /></td>
								</tr>
							</tbody>
						</table>
						<br />
						<?php 
						if(pto_return_open() == 1) {
							echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('You are currently closed.', 'cqpim') . '</div>';
						}
						if(pto_return_open() == 2) {
							echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('You are currently open.', 'cqpim') . '</div>';
						}							
						if(pto_return_open() == 3) {
							echo '<div class="cqpim-alert cqpim-alert-warning alert-display">' . __('Unable to calculate opening status for today, ensure you have completed both opening and closing time.', 'cqpim') . '</div>';
						}
						?>
						<div class="clear"></div>
						<h3><?php _e('Opening Times Warnings', 'cqpim'); ?></h3>
						<p><strong><?php _e('Support Ticket Warning', 'cqpim'); ?></strong></p>
						<?php $option = get_option('pto_support_opening_warning'); ?>
						<input type="checkbox" name="pto_support_opening_warning" value="1" <?php checked(1, $option); ?>/> <?php _e('Show an open / closed warning on support ticket pages of the client dashboard.', 'cqpim'); ?>
						<br />
						<p><?php _e('Open Message', 'cqpim'); ?></p>
						<textarea style="width:100%" class="cqpim-alert cqpim-alert-info" name="pto_support_open_message"><?php echo get_option('pto_support_open_message'); ?></textarea>
						<br />
						<p><?php _e('Closed Message', 'cqpim'); ?></p>
						<textarea style="width:100%" class="cqpim-alert cqpim-alert-warning" name="pto_support_closed_message"><?php echo get_option('pto_support_closed_message'); ?></textarea>					
						<br /><br />
						<p><strong><?php _e('Shortcode Warning', 'cqpim'); ?></strong></p>
						<p><?php _e('You can display the [pto_opening_hours] shortcode anywhere on your site, and it will display the alerts entered below depending on whether you are open or closed.', 'cqpim'); ?></p>
						<p><?php _e('Open Message', 'cqpim'); ?></p>
						<textarea style="width:100%" class="cqpim-alert cqpim-alert-info" name="pto_shortcode_open_message"><?php echo get_option('pto_shortcode_open_message'); ?></textarea>
						<br />
						<p><?php _e('Closed Message', 'cqpim'); ?></p>
						<textarea style="width:100%" class="cqpim-alert cqpim-alert-warning" name="pto_shortcode_closed_message"><?php echo get_option('pto_shortcode_closed_message'); ?></textarea>					
						<br /><br />
						<p class="submit">
							<input type="submit" class="button-primary save-business" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>						
					</div>
					<div id="tabs-9">
						<h3><?php _e('Client Dashboard', 'cqpim'); ?></h3>
						<p><?php _e('Projectopia has a full width theme included that is used on the Client Dashboard. If you prefer, you can have the Client Dashboard load inside your active WordPress theme instead.', 'cqpim'); ?></p>
						<?php $type = get_option('client_dashboard_type'); 
							$theme = wp_get_theme();
						?>
						<select id="client_dashboard_type" name="client_dashboard_type">
								<option value="inc" <?php if($type == 'inc') { echo 'selected'; } ?>><?php _e('Projectopia Client Dashboard Theme', 'cqpim'); ?></option>
								<option value="active" <?php if($type != 'inc') { echo 'selected'; } ?>><?php _e('Current Active WP Theme', 'cqpim'); ?> (<?php echo $theme->name; ?>)</option>									
						</select>
						<p><strong><?php _e('Client Dashboard Logout URL', 'cqpim'); ?></strong></p>
						<p><?php _e('This must be on the same domain as the plugin, otherwise this setting will not work', 'cqpim'); ?></p>
						<?php $logout = get_option('cqpim_logout_url'); ?>
						<input type="text" name="cqpim_logout_url" id="cqpim_logout_url" value="<?php echo $logout; ?>" />
						<div class="clear"></div>
						<br /><br />
						<?php $client_settings = get_option('allow_client_settings'); ?>
						<input type="checkbox" name="allow_client_settings" id="allow_client_settings" value="1" <?php checked($client_settings, 1, true); ?>/> <?php _e('Allow Clients to update their details from their dashboard', 'cqpim'); ?>
						<br /><br />
						<div class="clear"></div>
						<?php $client_settings = get_option('allow_client_users'); ?>
						<input type="checkbox" name="allow_client_users" id="allow_client_users" value="1" <?php checked($client_settings, 1, true); ?>/> <?php _e('Allow Clients to grant access to their dashboard & create users for other team members/colleagues.', 'cqpim'); ?>
						<br /><br />
						<div class="clear"></div>
						<h3><?php _e('Client Registration', 'cqpim'); ?></h3>
						<?php $client_settings = get_option('cqpim_login_reg'); ?>
						<input type="checkbox" name="cqpim_login_reg" id="cqpim_login_reg" value="1" <?php checked($client_settings, 1, true); ?>/> <?php _e('Allow Clients to Register from the Login Screen', 'cqpim'); ?>
						<br /><br />
						<?php $client_settings = get_option('cqpim_login_reg_company'); ?>
						<input type="checkbox" name="cqpim_login_reg_company" id="cqpim_login_reg_company" value="1" <?php checked($client_settings, 1, true); ?>/> <?php _e('Require a Company Name to be entered on the registration form', 'cqpim'); ?>
						<br /><br />
						<input type="checkbox" name="pto_dcreg_approve" value="1" <?php checked(get_option('pto_dcreg_approve'), 1); ?> /> <?php _e('Do not send login details until client is approved by admin', 'cqpim'); ?>
						<br /><br />
						<h3><?php _e('Dashboard Password Reset', 'cqpim'); ?></h3>
						<p><?php _e('This email is sent when a client requests a password reset.', 'cqpim'); ?></p>
						<p><strong><?php _e('Password Reset Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="client_password_reset_subject" name="client_password_reset_subject" value="<?php echo get_option('client_password_reset_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('Password Reset Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="client_password_reset_content" name="client_password_reset_content"><?php echo get_option('client_password_reset_content'); ?></textarea>
						<h3><?php _e('Dashboard Logo', 'cqpim'); ?></h3>
							<?php 
							$logo = get_option('cqpim_dash_logo'); 
							if($logo) { ?>
								<img style="max-width:400px; margin:20px 0; background:#ececec" src="<?php echo $logo['cqpim_dash_logo']; ?>" />
								<br />
								<button class="remove_logo cqpim_button mt-20 op bg-red font-white rounded_2" data-type="cqpim_dash_logo"><?php _e('Remove', 'cqpim'); ?></button>
								<br /><br />
							<?php } ?>
							<input type="file" name="cqpim_dash_logo" />
						<br /><br />
						<div class="clear"></div>	
						<h3><?php _e('Dashboard Background', 'cqpim'); ?></h3>
							<?php 
							$logo = get_option('cqpim_dash_bg'); 
							if($logo) { ?>
								<img style="max-width:600px; margin:20px 0; background:#ececec" src="<?php echo $logo['cqpim_dash_bg']; ?>" />
								<br />
								<button class="remove_logo cqpim_button mt-20 op bg-red font-white rounded_2" data-type="cqpim_dash_bg"><?php _e('Remove', 'cqpim'); ?></button>
								<br /><br />
							<?php } ?>
							<input type="file" name="cqpim_dash_bg" />
						<br /><br />
						<div class="clear"></div>	
						<h3><?php _e('Built-In Client Dashboard Custom CSS', 'cqpim'); ?></h3>
						<textarea style="width:100%; height:500px" name="cqpim_dash_css" id="cqpim_dash_css"><?php echo get_option('cqpim_dash_css'); ?></textarea>
						<br /><br />
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>						
					</div>
					<div id="tabs-23">						
						<h3><?php _e('New Lead Email Notification', 'cqpim'); ?></h3>
						<p><?php _e('This email is sent to all users who have access to the Leads system when a new lead is submitted via a form', 'cqpim'); ?></p>
						<p><strong><?php _e('New Lead Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="new_lead_email_subject" name="new_lead_email_subject" value="<?php echo get_option('new_lead_email_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('New Lead Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="new_lead_email_content" name="new_lead_email_content"><?php echo get_option('new_lead_email_content'); ?></textarea>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>
					</div>
					<div id="tabs-2">						
						<h3><?php _e('Client Settings', 'cqpim'); ?></h3>
						<p><?php _e('When a Client is created, a user account is also created to allow the new client to log in to their dashboard. On this page you can choose whether or not to send an automated welcome email when the client\'s account has been added.', 'cqpim'); ?></p>
						<p><strong><?php _e('Welcome Email', 'cqpim'); ?></strong></p>
						<?php $auto_welcome = get_option('auto_welcome'); ?>
						<input type="checkbox" name="auto_welcome" id="auto_welcome" value="1" <?php checked($auto_welcome, 1, true); ?>/> <?php _e('Send the client a welcome email with login details to their dashboard (Recommended).', 'cqpim'); ?>
						<br /><br />
						<p><strong><?php _e('Welcome Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="auto_welcome_subject" name="auto_welcome_subject" value="<?php echo get_option('auto_welcome_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('Welcome Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="auto_welcome_content" name="auto_welcome_content"><?php echo get_option('auto_welcome_content'); ?></textarea>
						<h3><?php _e('Password Reset', 'cqpim'); ?></h3>
						<p><strong><?php _e('Password Reset Email', 'cqpim'); ?></strong></p>
						<p><?php _e('This email is optionally sent to the client when an admin resets their password.', 'cqpim'); ?></p>
						<p><strong><?php _e('Password Reset Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="password_reset_subject" name="password_reset_subject" value="<?php echo get_option('password_reset_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('Password Reset Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="password_reset_content" name="password_reset_content"><?php echo get_option('password_reset_content'); ?></textarea>
						<h3><?php _e('New Contact Settings', 'cqpim'); ?></h3>
						<p><strong><?php _e('New Contact Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="added_contact_subject" name="added_contact_subject" value="<?php echo get_option('added_contact_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('New Contact Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="added_contact_content" name="added_contact_content"><?php echo get_option('added_contact_content'); ?></textarea>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>
					</div>
					<div id="tabs-3">
						<h3><?php _e('Header & Footer', 'cqpim'); ?></h3>
						<div class="settings-box">
						<p><strong><?php _e('Default Quote Header Text', 'cqpim'); ?></strong></p>
						<p><?php _e('The contents of this field will appear at the top of quotes/estimates. It can also be overridden on an individual basis when creating quotes/estimates.', 'cqpim'); ?></p>
						<?php
						$content = get_option( 'quote_header' );
						?>	
						<textarea style="width:100%; height:200px" id="quote_header" name="quote_header"><?php echo $content; ?></textarea>
						<div class="clear"></div>
						</div>
						<div class="settings-box">
						<p><strong><?php _e('Default Quote Footer Text', 'cqpim'); ?></strong></p>
						<p><?php _e('The contents of this field will appear at the bottom of quotes/estimates, just before the quote/estimate acceptance text. It can also be overridden on an individual basis when creating quotes/estimates.', 'cqpim'); ?></p>
						<?php
						$content = get_option( 'quote_footer' );
						?>	
						<textarea style="width:100%; height:200px" id="quote_footer" name="quote_footer"><?php echo $content; ?></textarea>
						</div>
						<div class="clear"></div>
						<h3><?php _e('Default Quote Acceptance Text', 'cqpim'); ?></h3>
						<p><strong><?php _e('Quote Acceptance Text', 'cqpim'); ?></strong></p>
						<p><?php _e('The contents of this field will appear alongside the form that clients will use to accept quotes/estimates. It should include instructions on how to proceed.', 'cqpim'); ?></p>
						<div class="settings-box">
						<?php
						$content   = get_option( 'quote_acceptance_text' );
						?>	
						<textarea style="width:100%; height:200px" id="quote_acceptance_text" name="quote_acceptance_text"><?php echo $content; ?></textarea>
						</div>
						<div class="clear"></div>
						<h3><?php _e('Quote Emails', 'cqpim'); ?></h3>
						<div class="settings-box">
						<p><?php _e('When a quote is sent to a client by email, these fields will be used for the subject and content of the email.', 'cqpim'); ?></p>
						<p><strong><?php _e('Quote Default Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" name="quote_email_subject" id="quote_email_subject" value="<?php echo get_option('quote_email_subject'); ?>"/>
						<p><strong><?php _e('Quote Default Email Content', 'cqpim'); ?></strong></p>
						<?php
						$content   = get_option( 'quote_default_email' );
						?>
						<textarea style="width:100%; height:200px" id="quote_default_email" name="quote_default_email"><?php echo $content; ?></textarea>
						</div>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>
					</div>
					<div id="tabs-4">
						<h3><?php _e('Terms & Conditions', 'cqpim'); ?></h3>
						<div class="settings-box">
						<p><strong><?php _e('Default Contract Terms & Conditions', 'cqpim'); ?></strong></p>
						<p><?php _e('These Terms & Conditions will appear on the contract that is sent to your clients. You can add templates by clicking Terms Templates in the Projectopia menu.', 'cqpim'); ?></p>
						<?php
						$content = get_option( 'default_contract_text' ); 
						?>
						<select name="default_contract_text">
							<?php 
							$args = array(
								'post_type' => 'cqpim_terms',
								'posts_per_page' => -1,
								'post_status' => 'private'
							);
							$terms = get_posts($args);
							foreach($terms as $term) {
								if($term->ID == $content) {
									$selected = 'selected="selected"';
								} else {
									$selected = '';
								}
								echo '<option value="' . $term->ID . '" ' . $selected . '>' . $term->post_title . '</option>';
							}
							?>
						</select>
						</div>
						<h3><?php _e('Contract Acceptance Text', 'cqpim'); ?></h3>
						<p><?php _e('This will appear on the contract alongside the form that clients will use to e-sign. It should include instructions on how to proceed.', 'cqpim'); ?></p>
						<div class="settings-box">
						<?php
						$content   = get_option( 'contract_acceptance_text' );
						?>
						<textarea style="width:100%; height:200px" id="contract_acceptance_text" name="contract_acceptance_text"><?php echo $content; ?></textarea>
						</div>
						<div class="clear"></div>
						<h3><?php _e('Contract & Update Emails', 'cqpim'); ?></h3>
						<div class="settings-box">
						<p><strong><?php _e('Client Contract Email', 'cqpim'); ?></strong></p>
						<p><?php _e('This email is sent out when a project has been created and should contain information on what a client needs to do in order to sign their contract.', 'cqpim'); ?></p>
						<p><strong><?php _e('Client Contract Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="client_contract_subject" name="client_contract_subject" value="<?php echo get_option('client_contract_subject'); ?>" />
						<p><strong><?php _e('Client Contract Email Content', 'cqpim'); ?></strong></p>
						<?php
						$content   = get_option( 'client_contract_email' );
						?>
						<textarea style="width:100%; height:200px" id="client_contract_email" name="client_contract_email"><?php echo $content; ?></textarea>
						</div>
						<div class="clear"></div>
						<br />
						<hr />
						<h3><?php _e('New Message Emails', 'cqpim'); ?></h3>
						<div class="settings-box">
						<p><strong><?php _e('Client Message Email', 'cqpim'); ?></strong></p>
						<p><?php _e('This email is sent to your client when you send a new project message.', 'cqpim'); ?></p>
						<p><strong><?php _e('Client Message Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="client_message_subject" name="client_message_subject" value="<?php echo get_option('client_message_subject'); ?>" />
						<p><strong><?php _e('Client Message Email Content', 'cqpim'); ?></strong></p>
						<?php
						$content   = get_option( 'client_message_email' );
						?>
						<textarea style="width:100%; height:200px" id="client_message_email" name="client_message_email"><?php echo $content; ?></textarea>
						</div>
						<div class="clear"></div>
						<br /><hr />
						<div class="settings-box">
						<p><strong><?php _e('Company Message Email', 'cqpim'); ?></strong></p>
						<p><?php _e('This email is sent to you when a client has sent a new project message.', 'cqpim'); ?></p>
						<p><strong><?php _e('Company Message Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="company_message_subject" name="company_message_subject" value="<?php echo get_option('company_message_subject'); ?>" />
						<p><strong><?php _e('Company Message Email Content', 'cqpim'); ?></strong></p>
						<?php
						$content = get_option( 'company_message_email' );
						?>
						<textarea style="width:100%; height:200px" id="company_message_email" name="company_message_email"><?php echo $content; ?></textarea>
						</div>
						<div class="clear"></div>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>					
					</div>
					<div id="tabs-18">
						<?php if(pto_check_addon_status('bugs')) { ?>
							<h3><?php _e('Bug Tracker', 'cqpim'); ?></h3>
							<?php $value = get_option('cqpim_bugs_auto'); ?>
							<input type="checkbox" name="cqpim_bugs_auto" value="1" <?php checked($value, 1); ?> /> <?php _e('Automatically activate bug tracker in new projects', 'cqpim'); ?>
							<div class="clear"></div>
							<p><strong><?php _e('New Bug Email', 'cqpim'); ?></strong></p>
							<p><?php _e('This email is sent to the PM, assignee and client when a bug is raised against a project.', 'cqpim'); ?></p>
							<p><strong><?php _e('New Bug Email Subject', 'cqpim'); ?></strong></p>
							<input type="text" id="cqpim_new_bug_subject" name="cqpim_new_bug_subject" value="<?php echo get_option('cqpim_new_bug_subject'); ?>" />
							<p><strong><?php _e('New Bug Email Content', 'cqpim'); ?></strong></p>
							<?php
							$content = get_option( 'cqpim_new_bug_content' );
							?>
							<textarea style="width:100%; height:200px" id="cqpim_new_bug_content" name="cqpim_new_bug_content"><?php echo $content; ?></textarea>
							<div class="clear"></div>
							<p><strong><?php _e('Updated Bug Email', 'cqpim'); ?></strong></p>
							<p><?php _e('This email is sent to the PM, assignee and client when a bug is updated by a team member or a client.', 'cqpim'); ?></p>
							<p><strong><?php _e('Updated Bug Email Subject', 'cqpim'); ?></strong></p>
							<input type="text" id="cqpim_update_bug_subject" name="cqpim_update_bug_subject" value="<?php echo get_option('cqpim_update_bug_subject'); ?>" />
							<p><strong><?php _e('Updated Bug Email Content', 'cqpim'); ?></strong></p>
							<?php
							$content = get_option( 'cqpim_update_bug_content' );
							?>
							<textarea style="width:100%; height:200px" id="cqpim_update_bug_content" name="cqpim_update_bug_content"><?php echo $content; ?></textarea>
							<div class="clear"></div>
							<p class="submit">
								<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
							</p>	
						<?php } else { ?>
							<h3><?php _e('Bug Tracker Add-On Not Found', 'cqpim'); ?></h3>
							<?php $link = '<a href="https://projectopia.io/projectopia-bug-tracker-add-on/" target="_blank">https://projectopia.io/projectopia-bug-tracker-add-on/</a>'; ?>
							<p><?php printf(__('To use the Bug Tracker part of the plugin, you need to purchase the Bug Tracker Add-On. Please visit %1$s for more information.', 'cqpim'), $link); ?></p>
						<?php } ?>						
					</div>
					<div id="tabs-5">
						<h3><?php _e('Invoice Prefix', 'cqpim'); ?></h3>
						<p><?php _e('If you\'d like to add a prefix to your invoice numbering, enter one here. This will appear before the invoice number on all invoices, but can be overriden per client if needed', 'cqpim'); ?></p>
						<input type="text" name="cqpim_invoice_prefix" value="<?php echo get_option('cqpim_invoice_prefix'); ?>" />
						<h3><?php _e('Invoice Template', 'cqpim'); ?></h3>
						<?php 
						$checked = get_option('cqpim_invoice_template'); 
						if(empty($checked)) {
							update_option('cqpim_invoice_template', 1);
						}
						?>
						<table>
							<tr><td><input type="radio" name="cqpim_invoice_template" value="1" <?php checked($checked, 1, true); ?> /></td><td><?php _e('Default', 'cqpim'); ?></td></tr>
							<tr><td colspan="2"></td></tr>
							<tr><td><input type="radio" name="cqpim_invoice_template" value="2" <?php checked($checked, 2, true); ?> /></td><td><?php _e('Clean', 'cqpim'); ?> | <?php _e('Main Colour (HEX, eg. #333333)', 'cqpim'); ?>: <input type="text" style="width:100px" name="cqpim_clean_main_colour" value="<?php echo get_option('cqpim_clean_main_colour'); ?>" /></td></tr>
							<tr><td colspan="2"></td></tr>
							<tr><td><input type="radio" name="cqpim_invoice_template" value="3" <?php checked($checked, 3, true); ?> /></td><td><?php _e('Space', 'cqpim'); ?> | <?php _e('Main Colour (HEX, eg. #333333)', 'cqpim'); ?>: <input type="text" style="width:100px" name="cqpim_cool_main_colour" value="<?php echo get_option('cqpim_cool_main_colour'); ?>" /></td></tr>
						</table>
						<h3><?php _e('Invoice Logo', 'cqpim'); ?></h3>
						<p><?php _e('By default, the invoice will use the global company logo. You can override the invoice logo here if you wish', 'cqpim'); ?></p>
						<?php 
						$logo = get_option('cqpim_invoice_logo'); 
						if($logo) { ?>
							<img style="max-width:400px; margin:20px 0; background:#ececec" src="<?php echo $logo['cqpim_invoice_logo']; ?>" />
							<br />
							<button class="remove_logo cqpim_button mt-20 op bg-red font-white rounded_2" data-type="cqpim_invoice_logo"><?php _e('Remove', 'cqpim'); ?></button>
							<br /><br />
						<?php } ?>	
						<p><input type="file" name="cqpim_invoice_logo" /></p>
						<h3><?php _e('PDF Invoice Email Attachments', 'cqpim'); ?></h3>
						<?php $client_invoice_email_attach = get_option('client_invoice_email_attach'); ?>
						<p><strong><?php _e('IMPORTANT:', 'cqpim'); ?></strong> <?php _e('PDF Invoice attachments require the PHP cURL Extension. If you experience blank PDFs then check that you have this installed and your host is not blocking the requests.', 'cqpim'); ?></p>
						<input type="checkbox" name="client_invoice_email_attach" id="client_invoice_email_attach" value="1" <?php checked($client_invoice_email_attach, 1, true); ?>/> <?php $text = __('Attach a PDF Invoice to Client Emails	', 'cqpim'); _e('Attach a PDF Invoice to Client Emails	', 'cqpim'); ?>					
						<h3><?php _e('Partial Invoice Payments', 'cqpim'); ?></h3>
						<?php $client_invoice_allow_partial = get_option('client_invoice_allow_partial'); ?>
						<input type="checkbox" name="client_invoice_allow_partial" id="client_invoice_allow_partial" value="1" <?php checked($client_invoice_allow_partial, 1, true); ?>/> <?php _e('Allow partial invoice payments (This is a global setting and can be overridden on a per invoice basis. Deposit invoices do not allow partial payments.)', 'cqpim'); ?>
						<h3><?php _e('Invoice Reminder Settings', 'cqpim'); ?></h3>
						<p><strong><?php _e('IMPORTANT:', 'cqpim'); ?> </strong> <?php _e('The following settings require the use of WP Cron, which some hosts block access to. Please check that you have access to wp cron, otherwise these settings may not work properly.', 'cqpim'); ?></p>
						<table>
							<tr>
								<td class="title"><?php _e('Reminder email after invoice sent:', 'cqpim'); ?></td>
								<td>
								<?php $terms = get_option('client_invoice_after_send_remind_days');
								$days = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20); ?>
								<select id="client_invoice_after_send_remind_days" name="client_invoice_after_send_remind_days">
									<option value="" <?php if(!$terms) { echo 'selected'; } ?>><?php _e('Choose...', 'cqpim') ?></option>
									<?php foreach($days as $day) { ?>
										<option value="<?php echo $day; ?>" <?php if($terms == $day) { echo 'selected'; } ?>><?php echo $day; ?> day<?php if($day != 1) { echo 's'; } ?></option>
									<?php } ?>
								</select>
								</td>
							</tr>
							<tr>
								<td class="title"><?php _e('Reminder email before due date:', 'cqpim'); ?></td>
								<td>
								<?php $terms = get_option('client_invoice_before_terms_remind_days');
								$days = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20); ?>
								<select id="client_invoice_before_terms_remind_days" name="client_invoice_before_terms_remind_days">
									<option value="" <?php if(!$terms) { echo 'selected'; } ?>><?php _e('Choose...', 'cqpim') ?></option>
									<?php foreach($days as $day) { ?>
										<option value="<?php echo $day; ?>" <?php if($terms == $day) { echo 'selected'; } ?>><?php echo $day; ?> day<?php if($day != 1) { echo 's'; } ?></option>
									<?php } ?>
								</select>
								</td>
							</tr>
							<tr>
								<td class="title"><?php _e('Overdue email after due date:', 'cqpim'); ?></td>
								<td>
								<?php $terms = get_option('client_invoice_after_terms_remind_days');
								$days = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20); ?>
								<select id="client_invoice_after_terms_remind_days" name="client_invoice_after_terms_remind_days">
									<option value="" <?php if(!$terms) { echo 'selected'; } ?>><?php _e('Choose...', 'cqpim') ?></option>
									<?php foreach($days as $day) { ?>
										<option value="<?php echo $day; ?>" <?php if($terms == $day) { echo 'selected'; } ?>><?php echo $day; ?> day<?php if($day != 1) { echo 's'; } ?></option>
									<?php } ?>
								</select>
								</td>
							</tr>
						</table>
						<br />
						<?php $client_invoice_high_priority = get_option('client_invoice_high_priority'); ?>
						<input type="checkbox" name="client_invoice_high_priority" id="client_invoice_high_priority" value="1" <?php checked($client_invoice_high_priority, 1, true); ?>/> Mark invoice reminder/overdue emails as high priority?
						<h3><?php _e('Invoice Footer', 'cqpim'); ?></h3>
						<p><?php _e('This text will appear at the bottom of invoices, so should contain instructions for payment etc.', 'cqpim'); ?></p>
						<div class="settings-box">
						<?php
						$content   = get_option( 'client_invoice_footer' );
						?>
						<textarea style="width:100%; height:200px" id="client_invoice_footer" name="client_invoice_footer"><?php echo $content; ?></textarea>
						</div>
						<h3><?php _e('Emails', 'cqpim'); ?></h3>
						<div class="settings-box">
						<p><strong><?php _e('Client Invoice Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="client_invoice_subject" name="client_invoice_subject" value="<?php echo get_option('client_invoice_subject'); ?>" />
						<p><strong><?php _e('Client Invoice Email Content', 'cqpim'); ?></strong></p>
						<?php
						$content   = get_option( 'client_invoice_email' ); 
						?>
						<textarea style="width:100%; height:200px" id="client_invoice_email" name="client_invoice_email"><?php echo $content; ?></textarea>
						</div>
						<br />
						<hr />
						<div class="settings-box">
						<p><strong><?php _e('Client Deposit Invoice Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="client_deposit_invoice_subject" name="client_deposit_invoice_subject" value="<?php echo get_option('client_deposit_invoice_subject'); ?>" />
						<p><strong><?php _e('Client Deposit Invoice Email', 'cqpim'); ?></strong></p>
						<?php
						$content   = get_option( 'client_deposit_invoice_email' );
						?>
						<textarea style="width:100%; height:200px" id="client_deposit_invoice_email" name="client_deposit_invoice_email"><?php echo $content; ?></textarea>
						</div>
						<br />
						<hr />
						<div class="settings-box">
						<p><strong><?php _e('Client Invoice Reminder Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="client_invoice_reminder_subject" name="client_invoice_reminder_subject" value="<?php echo get_option('client_invoice_reminder_subject'); ?>" />
						<p><strong><?php _e('Client Invoice Reminder Email', 'cqpim'); ?></strong></p>
						<?php
						$content   = get_option( 'client_invoice_reminder_email' );
						?>
						<textarea style="width:100%; height:200px" id="client_invoice_reminder_email" name="client_invoice_reminder_email"><?php echo $content; ?></textarea>
						</div>
						<br />
						<hr />
						<div class="settings-box">
						<p><strong><?php _e('Client Invoice Overdue Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="client_invoice_overdue_subject" name="client_invoice_overdue_subject" value="<?php echo get_option('client_invoice_overdue_subject'); ?>" />
						<p><strong><?php _e('Client Invoice Overdue Email', 'cqpim'); ?></strong></p>
						<?php
						$content   = get_option( 'client_invoice_overdue_email' ); 
						?>
						<textarea style="width:100%; height:200px" id="client_invoice_overdue_email" name="client_invoice_overdue_email"><?php echo $content; ?></textarea>
						</div>
						<br />
						<hr />
						<div class="settings-box">
						<p><strong><?php _e('Client Payment Notication Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="client_invoice_receipt_subject" name="client_invoice_receipt_subject" value="<?php echo get_option('client_invoice_receipt_subject'); ?>" />
						<p><strong><?php _e('Client Payment Notication Email', 'cqpim'); ?></strong></p>
						<?php
						$content   = get_option( 'client_invoice_receipt_email' ); 
						?>
						<textarea style="width:100%; height:200px" id="client_invoice_receipt_email" name="client_invoice_receipt_email"><?php echo $content; ?></textarea>
						</div>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>
					</div>
					<div id="tabs-20">
						<?php if(pto_check_addon_status('subscriptions')) { ?>
							<h3><?php _e('Subscriptions', 'cqpim'); ?></h3>
							<p><strong><?php _e('New Subscription Email', 'cqpim'); ?></strong></p>
							<p><?php _e('This email is sent to the client when a new subscription has been created for them to accept.', 'cqpim'); ?></p>
							<p><strong><?php _e('Subject', 'cqpim'); ?></strong></p>
							<input type="text" id="cqpim_new_subscription_subject" name="cqpim_new_subscription_subject" value="<?php echo get_option('cqpim_new_subscription_subject'); ?>" />
							<p><strong><?php _e('Content', 'cqpim'); ?></strong></p>
							<?php $content   = get_option( 'cqpim_new_subscription_content' ); ?>
							<textarea style="width:100%; height:200px" id="cqpim_new_subscription_content" name="cqpim_new_subscription_content"><?php echo $content; ?></textarea>							
							<br />
							<p><strong><?php _e('New Subscription Accepted Email', 'cqpim'); ?></strong></p>
							<p><?php _e('This email is sent to the selected Team Members when a new subscription has been accepted and activated by the client.', 'cqpim'); ?></p>
							<p><strong><?php _e('Subject', 'cqpim'); ?></strong></p>
							<input type="text" id="cqpim_new_subscription_accept_subject" name="cqpim_new_subscription_accept_subject" value="<?php echo get_option('cqpim_new_subscription_accept_subject'); ?>" />
							<p><strong><?php _e('Content', 'cqpim'); ?></strong></p>
							<?php $content   = get_option( 'cqpim_new_subscription_accept_content' ); ?>
							<textarea style="width:100%; height:200px" id="cqpim_new_subscription_accept_content" name="cqpim_new_subscription_accept_content"><?php echo $content; ?></textarea>	
							<br />
							<p><strong><?php _e('Subscription Cancelled Email', 'cqpim'); ?></strong></p>
							<p><?php _e('This email is sent to the selected Team Members and the Client when a subscription has been cancelled.', 'cqpim'); ?></p>
							<p><strong><?php _e('Subject', 'cqpim'); ?></strong></p>
							<input type="text" id="cqpim_subscription_cancelled_subject" name="cqpim_subscription_cancelled_subject" value="<?php echo get_option('cqpim_subscription_cancelled_subject'); ?>" />
							<p><strong><?php _e('Content', 'cqpim'); ?></strong></p>
							<?php $content   = get_option( 'cqpim_subscription_cancelled_content' ); ?>
							<textarea style="width:100%; height:200px" id="cqpim_subscription_cancelled_content" name="cqpim_subscription_cancelled_content"><?php echo $content; ?></textarea>	
							<br />
							<p><strong><?php _e('Subscription Failed Email', 'cqpim'); ?></strong></p>
							<p><?php _e('This email is sent to the selected Team Members and the Client when a subscription payment has failed.', 'cqpim'); ?></p>
							<p><strong><?php _e('Subject', 'cqpim'); ?></strong></p>
							<input type="text" id="cqpim_subscription_failed_subject" name="cqpim_subscription_failed_subject" value="<?php echo get_option('cqpim_subscription_failed_subject'); ?>" />
							<p><strong><?php _e('Content', 'cqpim'); ?></strong></p>
							<?php $content   = get_option( 'cqpim_subscription_failed_content' ); ?>
							<textarea style="width:100%; height:200px" id="cqpim_subscription_failed_content" name="cqpim_subscription_failed_content"><?php echo $content; ?></textarea>							
							<br />
							<p><strong><?php _e('Subscription Reminder Email', 'cqpim'); ?></strong></p>
							<p><?php _e('This email is sent to the client in advance of a payment being taken, if this has been configured in the subscription.', 'cqpim'); ?></p>
							<p><strong><?php _e('Subject', 'cqpim'); ?></strong></p>
							<input type="text" id="cqpim_subscription_reminder_subject" name="cqpim_subscription_reminder_subject" value="<?php echo get_option('cqpim_subscription_reminder_subject'); ?>" />
							<p><strong><?php _e('Content', 'cqpim'); ?></strong></p>
							<?php $content   = get_option( 'cqpim_subscription_reminder_content' ); ?>
							<textarea style="width:100%; height:200px" id="cqpim_subscription_reminder_content" name="cqpim_subscription_reminder_content"><?php echo $content; ?></textarea>							
							<p class="submit">
								<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
							</p>
						<?php } else { ?>
							<h3><?php _e('Subscriptions Add-On Not Found', 'cqpim'); ?></h3>
							<?php $link = '<a href="https://projectopia.io/projectopia-subscriptions-add-on/" target="_blank">https://projectopia.io/projectopia-subscriptions-add-on/</a>'; ?>
							<p><?php printf(__('To use the Subscriptions part of the plugin, you need to purchase the Projectopia Subscriptions Add-On. Please visit %1$s for more information.', 'cqpim'), $link); ?></p>
						<?php } ?>						
					</div>
					<?php if(0) { ?>
					<div id="tabs-22">
						<?php if(pto_check_addon_status('woocommerce')) { ?>
							<h3><?php _e('WooCommerce', 'cqpim'); ?></h3>
							<p><strong><?php _e('New Project Notification Email', 'cqpim'); ?></strong></p>
							<p><?php _e('This email is sent to the client when a new order has been processed and a project created for them.', 'cqpim'); ?></p>
							<p><strong><?php _e('Subject', 'cqpim'); ?></strong></p>
							<input type="text" id="cqpim_wc_new_project_subject" name="cqpim_wc_new_project_subject" value="<?php echo get_option('cqpim_wc_new_project_subject'); ?>" />
							<p><strong><?php _e('Content', 'cqpim'); ?></strong></p>
							<?php $content   = get_option( 'cqpim_wc_new_project_content' ); ?>
							<textarea style="width:100%; height:200px" id="cqpim_wc_new_project_content" name="cqpim_wc_new_project_content"><?php echo $content; ?></textarea>
							<p class="submit">
								<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
							</p>
						<?php } else { ?>
							<h3><?php _e('WooCommerce Add-On Not Found', 'cqpim'); ?></h3>
							<?php $link = '<a href="https://projectopia.io/projectopia-woocommerce-add-on/" target="_blank">https://projectopia.io/projectopia-woocommerce-add-on/</a>'; ?>
							<p><?php printf(__('To use the WooCommerce part of the plugin, you need to purchase the Projectopia WooCommerce Add-On. Please visit %1$s for more information.', 'cqpim'), $link); ?></p>
						<?php } ?>						
					</div>
					<?php } ?>
					<div id="tabs-21">
						<h3><?php _e('Payment Gateways', 'cqpim'); ?></h3>
						<p><strong><?php _e('Paypal', 'cqpim'); ?></strong></p>
						<p><?php _e('To allow clients to pay invoices via Paypal, enter your Paypal email address below.', 'cqpim'); ?></p>
						<input type="text" name="client_invoice_paypal_address" id="client_invoice_paypal_address" value="<?php echo get_option('client_invoice_paypal_address'); ?>" />						
						<?php if(pto_check_addon_status('subscriptions')) { ?>
							<p><?php _e('To allow clients to set up subscriptions via Paypal, enter your Paypal API credentials below.', 'cqpim'); ?></p>
							<p><?php _e('Paypal API Username', 'cqpim'); ?></p>
							<input type="text" name="cqpim_paypal_api_username" id="cqpim_paypal_api_username" value="<?php echo get_option('cqpim_paypal_api_username'); ?>" />
							<p><?php _e('Paypal API Password', 'cqpim'); ?></p>
							<input type="text" name="cqpim_paypal_api_password" id="cqpim_paypal_api_password" value="<?php echo get_option('cqpim_paypal_api_password'); ?>" />						
							<p><?php _e('Paypal API Signature', 'cqpim'); ?></p>
							<input type="text" name="cqpim_paypal_api_signature" id="cqpim_paypal_api_signature" value="<?php echo get_option('cqpim_paypal_api_signature'); ?>" />						

						<?php } ?>						
						<p><strong><?php _e('Stripe', 'cqpim'); ?></strong></p>
						<?php if(pto_check_addon_status('subscriptions')) { ?>
							<p><?php _e('To allow clients to pay invoices and set up subscriptions via Stripe, enter your Stripe Publishable Key below.', 'cqpim'); ?></p>
						<?php } else { ?>
							<p><?php _e('To allow clients to pay invoices via Stripe, enter your Stripe Publishable Key below.', 'cqpim'); ?></p>
						<?php } ?>
						<p><?php _e('Stripe Public Key', 'cqpim'); ?></p>
						<input type="text" name="client_invoice_stripe_key" id="client_invoice_stripe_key" value="<?php echo get_option('client_invoice_stripe_key'); ?>" />
						<p><?php _e('Stripe Secret Key', 'cqpim'); ?></p>
						<input type="text" name="client_invoice_stripe_secret" id="client_invoice_stripe_secret" value="<?php echo get_option('client_invoice_stripe_secret'); ?>" />
						<br /><br />
						<input type="checkbox" name="client_invoice_stripe_ideal" id="client_invoice_stripe_ideal" value="1" <?php checked(1, get_option('client_invoice_stripe_ideal')); ?> /> <?php _e('If you have iDEAL activated on your Stripe account, check this box to enable it as a payment gateway', 'cqpim'); ?>
						<?php
							if(function_exists('pto_twocheck_return_settings')) {
								echo pto_twocheck_return_settings();
							}
						?>	
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>					
					</div>
					<div id="tabs-6">
						<h3><?php _e('New Account', 'cqpim'); ?></h3>
						<p><strong><?php _e('New Account Email', 'cqpim'); ?></strong></p>
						<p><?php _e('This email is sent out when an admin creates a new team member.', 'cqpim'); ?></p>
						<p><strong><?php _e('New Account Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="team_account_subject" name="team_account_subject" value="<?php echo get_option('team_account_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('New Account Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="team_account_email" name="team_account_email"><?php echo get_option('team_account_email'); ?></textarea>
						<h3><?php _e('Password Reset', 'cqpim'); ?></h3>
						<p><strong><?php _e('Password Reset Email', 'cqpim'); ?></strong></p>
						<p><?php _e('This email is optionally sent to the team member when an admin resets their password.', 'cqpim'); ?></p>
						<p><strong><?php _e('Password Reset Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="team_reset_subject" name="team_reset_subject" value="<?php echo get_option('team_reset_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('Password Reset Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="team_reset_email" name="team_reset_email"><?php echo get_option('team_reset_email'); ?></textarea>
						<h3><?php _e('New Project', 'cqpim'); ?></h3>
						<p><strong><?php _e('New Project Email', 'cqpim'); ?></strong></p>
						<p><?php _e('This email is sent to a team member when an admin adds them to a project.', 'cqpim'); ?></p>
						<p><strong><?php _e('New Project Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="team_project_subject" name="team_project_subject" value="<?php echo get_option('team_project_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('New Project Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="team_project_email" name="team_project_email"><?php echo get_option('team_project_email'); ?></textarea>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>
					</div>
					<div id="tabs-10">
						<h3><?php _e('Task Update Email', 'cqpim'); ?></h3>
						<p><?php _e('This email is sent to the client and all watchers when a task is updated.', 'cqpim'); ?></p>
						<p><strong><?php _e('Task Update Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="team_assignment_subject" name="team_assignment_subject" value="<?php echo get_option('team_assignment_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('Task Update Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="team_assignment_email" name="team_assignment_email"><?php echo get_option('team_assignment_email'); ?></textarea>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>						
					</div>
					<div id="tabs-7">
						<h3><?php _e('Disable Support Tickets', 'cqpim'); ?></h3>
						<?php $checked = get_option('disable_tickets'); ?>
						<input type="checkbox" name="disable_tickets" value="1" <?php checked($checked, 1, true); ?> /> <?php _e('Disable the support ticket system.', 'cqpim'); ?><br /><br />
						<h3><?php _e('New Ticket Email', 'cqpim'); ?></h3>
						<p><?php _e('This email is sent to your Support email address when a client raises a new ticket.', 'cqpim'); ?></p>
						<p><strong><?php _e('New Ticket Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="client_create_ticket_subject" name="client_create_ticket_subject" value="<?php echo get_option('client_create_ticket_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('New Ticket Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="client_create_ticket_email" name="client_create_ticket_email"><?php echo get_option('client_create_ticket_email'); ?></textarea>
						<h3><?php _e('Updated Ticket Email', 'cqpim'); ?></h3>
						<p><?php $text = __('This email is sent to the owner, client and watchers when a ticket is updated', 'cqpim'); _e('This email is sent to the owner, client and watchers when a ticket is updated', 'cqpim'); ?></p>
						<p><strong><?php _e('Updated Ticket Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="client_update_ticket_subject" name="client_update_ticket_subject" value="<?php echo get_option('client_update_ticket_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('Client Updated Ticket Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="client_update_ticket_email" name="client_update_ticket_email"><?php echo get_option('client_update_ticket_email'); ?></textarea>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>						
					</div>
					<div id="tabs-24">
						<h3><?php _e('FAQ Settings', 'cqpim'); ?></h3>
						<?php $checked = get_option('cqpim_enable_faq'); ?>
						<input type="checkbox" name="cqpim_enable_faq" value="1" <?php checked($checked, 1, true); ?> /> <?php _e('Enable the FAQ system', 'cqpim'); ?>
						<h3><?php _e('FAQ Categories', 'cqpim'); ?></h3>
						<a href="<?php echo admin_url(); ?>edit-tags.php?taxonomy=cqpim_faq_cat"><?php _e('Manage FAQ Categories', 'cqpim'); ?></a>
						<h3><?php _e('Client Dashboard', 'cqpim'); ?></h3>
						<?php $checked = get_option('cqpim_enable_faq_dash'); ?>
						<input type="checkbox" name="cqpim_enable_faq_dash" value="1" <?php checked($checked, 1, true); ?> /> <?php _e('Show the FAQ tab in the Client Dashboard Menu', 'cqpim'); ?><br />
						<?php $checked = get_option('cqpim_enable_faq_dash_cats'); ?>
						<input type="checkbox" name="cqpim_enable_faq_dash_cats" value="1" <?php checked($checked, 1, true); ?> /> <?php _e('Group FAQ by category', 'cqpim'); ?><br />						<h3><?php _e('FAQ Shortcode', 'cqpim'); ?></h3>
						<p><?php _e('You can display your FAQ anywhere on your site with the following shortcodes - ', 'cqpim'); ?></p>
						<p>
							<?php _e('Display a plain FAQ List', 'cqpim'); ?> - <strong>[pto_faq]</strong><br />
							<?php _e('Display the FAQ, grouped by category', 'cqpim'); ?> - <strong>[pto_faq category="1"]</strong><br />
						</p>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>			
					</div>
					<div id="tabs-8">
						<?php
							$value = get_option('gdpr_consent_text');
							if(empty($value)) {
								update_option('gdpr_consent_text', 'I give consent for my personal data to be stored for the purpose of carrying out business activities.');
							}
							$args = array(
								'sort_order' => 'asc',
								'sort_column' => 'post_title',
								'hierarchical' => 1,
								'exclude' => '',
								'include' => '',
								'meta_key' => '',
								'meta_value' => '',
								'authors' => '',
								'child_of' => 0,
								'parent' => -1,
								'exclude_tree' => '',
								'number' => '',
								'offset' => 0,
								'post_type' => 'page',
								'post_status' => 'publish'
							); 							
							$pages = get_pages( $args );
						?>
						<h3><?php _e('Frontend Quote Form', 'cqpim'); ?></h3>
						<p><?php _e('This form can be displayed anywhere in your theme with the [cqpim_frontend_form] shortcode. Completion of the form will send an email to the sales email address, create a new client and a new quote and will copy any additional form fields into the Project Brief within the quote.', 'cqpim'); ?></p>
						<?php 
						$value = get_option('cqpim_frontend_form'); 
						$args = array(
							'post_type' => 'cqpim_forms',
							'posts_per_page' => -1,
							'meta_key' => 'form_type',
							'meta_value' => 'anonymous_frontend',
							'post_status' => 'private'
						);
						$forms = get_posts($args);
						?>
						<select name="cqpim_frontend_form" id="cqpim_frontend_form">
							<option value=""><?php _e('Choose a form', 'cqpim') ?></option>
							<?php foreach($forms as $form) {
								if($form->ID == $value) {
									$selected = 'selected="selected"';
								} else {
									$selected = '';
								}
								echo '<option value="' . $form->ID . '" ' . $selected . '>' . $form->post_title . '</option>';
							} ?>
						</select>
						<br /> <br />
						<?php $auto_welcome = get_option('form_auto_welcome'); ?>
						<input type="checkbox" name="form_auto_welcome" id="form_auto_welcome" value="1" <?php checked($auto_welcome, 1, true); ?>/> <?php _e('Send the client a welcome email with login details to their dashboard (Recommended).', 'cqpim'); ?>
						<br />
						<input type="checkbox" name="pto_cquo_approve" value="1" <?php checked(get_option('pto_cquo_approve'), 1); ?> /> <?php _e('Do not send login details until client is approved by admin', 'cqpim'); ?>						
						<h3><?php _e('Client Registration Form (No quote Required)', 'cqpim'); ?></h3>
						<p><?php _e('If you would like to add a form for clients to register without creating a quote, you can use the [cqpim_registration_form] shortcode anywhere on your site. ', 'cqpim'); ?></p>
						<?php $auto_welcome = get_option('form_reg_auto_welcome'); ?>
						<input type="checkbox" name="form_reg_auto_welcome" id="form_reg_auto_welcome" value="1" <?php checked($auto_welcome, 1, true); ?>/> <?php $text = __('Send the client a welcome email with login details to their dashboard (Recommended).', 'cqpim'); _e('Send the client a welcome email with login details to their dashboard (Recommended).', 'cqpim'); ?>
						<br />
						<input type="checkbox" name="pto_creg_approve" value="1" <?php checked(get_option('pto_creg_approve'), 1); ?> /> <?php _e('Do not send login details until client is approved by admin', 'cqpim'); ?>									
						<h3><?php _e('Dashboard Quote Form', 'cqpim'); ?></h3>
						<p><?php _e('This form will be displayed in the client Dashboard. Completion of the form will send an email to the sales email address, create a new quote and will copy the form fields into the Project Brief within the quote. Leave this field blank to disable the client Dashboard form', 'cqpim'); ?></p>
						<?php 
						$value = get_option('cqpim_backend_form');
						$args = array(
							'post_type' => 'cqpim_forms',
							'posts_per_page' => -1,
							'meta_key' => 'form_type',
							'meta_value' => 'client_dashboard',
							'post_status' => 'private'
						);
						$forms = get_posts($args);
						?>
						<select name="cqpim_backend_form" id="cqpim_backend_form">
							<option value=""><?php _e('Choose a form', 'cqpim') ?></option>
							<?php foreach($forms as $form) {
								if($form->ID == $value) {
									$selected = 'selected="selected"';
								} else {
									$selected = '';
								}
								echo '<option value="' . $form->ID . '" ' . $selected . '>' . $form->post_title . '</option>';
							} ?>
						</select>
						<h3><?php _e('Form Confirmation Fields', 'cqpim'); ?></h3>
						<p><strong><?php _e('Link to Terms & Conditions Page', 'cqpim'); ?></strong></p>
						<input type="checkbox" name="gdpr_tc_page_check" value="1" <?php checked(get_option('gdpr_tc_page_check'), 1); ?> /> <?php _e('Add a checkbox to confirm that the client has read the Terms & Conditions Page', 'cqpim'); ?><br />
						<p><strong><?php _e('Terms & Conditions Page', 'cqpim'); ?></strong></p>
						<?php $tc_id = get_option('gdpr_tc_page'); ?>
						<select name="gdpr_tc_page">
							<option value=""><?php _e('Choose...', 'cqpim'); ?></option>
							<?php foreach($pages as $page) {
								echo '<option value="' . $page->ID . '" ' . selected($tc_id, $page->ID) . '>' . $page->post_title . '</option>';
							} ?>
						</select>
						<p><strong><?php _e('Link to Privacy Policy Page', 'cqpim'); ?></strong></p>
						<input type="checkbox" name="gdpr_pp_page_check" value="1" <?php checked(get_option('gdpr_pp_page_check'), 1); ?> /> <?php _e('Add a checkbox to confirm that the client has read the Privacy Policy Page', 'cqpim'); ?><br />
						<p><strong><?php _e('Privacy Policy Page', 'cqpim'); ?></strong></p>
						<?php $pp_id = get_option('gdpr_pp_page'); ?>
						<select name="gdpr_pp_page">
							<option value=""><?php _e('Choose...', 'cqpim'); ?></option>
							<?php foreach($pages as $page) {
								echo '<option value="' . $page->ID . '" ' . selected($pp_id, $page->ID) . '>' . $page->post_title . '</option>';
							} ?>
						</select>						
						<br />				
						<h3><?php _e('Form Emails', 'cqpim'); ?></h3>
						<p><?php _e('This email will be sent to your sales email address when a quote has been requested.', 'cqpim'); ?></p>
						<p><strong><?php _e('New Quote Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="new_quote_subject" name="new_quote_subject" value="<?php echo get_option('new_quote_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('New Quote Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="new_quote_email" name="new_quote_email"><?php echo get_option('new_quote_email'); ?></textarea>	
						
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>						
					</div>
					<div id="tabs-16">
						<?php if(pto_check_addon_status('expenses')) { ?>
							<h3><?php _e('Suppliers / Expenses', 'cqpim'); ?></h3>
							<p><strong><?php _e('Expenses Authorisation', 'cqpim'); ?></strong></p>
							<?php $auth = get_option('cqpim_activate_expense_auth'); ?>
							<input type="checkbox" name="cqpim_activate_expense_auth" value="1" <?php checked($auth, 1); ?>/> <?php _e('Expenses should be authorised by an Admin', 'cqpim'); ?>
							<p><strong><?php _e('Expenses Authorisation Limit', 'cqpim'); ?></strong></p>
							<p><?php _e('If you\'d like to skip authorisation for smaller value expenses, enter the limit here. Any expenses with a value less than entered here will not require authorisation. Leave this blank if you would prefer not to set a limit.', 'cqpim'); ?></p>
							<input type="number" name="cqpim_expense_auth_limit" value="<?php echo get_option('cqpim_expense_auth_limit'); ?>" />
							<p><strong><?php _e('Permissions', 'cqpim'); ?></strong></p>
							<p><?php _e('To control who can authorise expenses, and who can skip authorisation, please visit the plugin Roles & Permissions page.', 'cqpim'); ?></p>
							<h3><?php _e('Authorisation Emails', 'cqpim'); ?></h3>
							<p><strong><?php $text = __('Authorisation Email Subject', 'cqpim'); _e('Authorisation Email Subject', 'cqpim'); ?></strong></p>
							<input type="text" id="cqpim_auth_email_subject" name="cqpim_auth_email_subject" value="<?php echo get_option('cqpim_auth_email_subject'); ?>" />
							<br /><br />
							<p><strong><?php $text = __('Authorisation Email Content', 'cqpim'); _e('Authorisation Email Content', 'cqpim'); ?></strong></p>
							<textarea style="width:100%; height:200px" id="cqpim_auth_email_content" name="cqpim_auth_email_content"><?php echo get_option('cqpim_auth_email_content'); ?></textarea>														
							<p><strong><?php $text = __('Authorised Email Subject', 'cqpim'); _e('Authorised Email Subject', 'cqpim'); ?></strong></p>
							<input type="text" id="cqpim_authorised_email_subject" name="cqpim_authorised_email_subject" value="<?php echo get_option('cqpim_authorised_email_subject'); ?>" />
							<br /><br />
							<p><strong><?php $text = __('Authorised Email Content', 'cqpim'); _e('Authorised Email Content', 'cqpim'); ?></strong></p>
							<textarea style="width:100%; height:200px" id="cqpim_authorised_email_content" name="cqpim_authorised_email_content"><?php echo get_option('cqpim_authorised_email_content'); ?></textarea>
							<p class="submit">
								<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
							</p>						
						<?php } else { ?>
							<h3><?php _e('Suppliers / Expenses Add-On Not Found', 'cqpim'); ?></h3>
							<?php $link = '<a href="https://projectopia.io/projectopia-suppliers-expenses-add-on/" target="_blank">https://projectopia.io/projectopia-suppliers-expenses-add-on/</a>'; ?>
							<p><?php printf(__('To use the Suppliers / Expenses part of the plugin, you need to purchase the  Suppliers / Expenses Add-On. Please visit %1$s for more information.', 'cqpim'), $link); ?></p>
						<?php } ?>						
					</div>
					<div id="tabs-17">
						<?php if(pto_check_addon_status('reporting')) { ?>
							<h3><?php _e('Reporting', 'cqpim'); ?></h3>
							<p><?php _e('Reporting Add-On Enabled. No settings required.', 'cqpim'); ?></p>
						<?php } else { ?>
							<h3><?php _e('Reporting Add-On Not Found', 'cqpim'); ?></h3>
							<?php $link = '<a href="https://projectopia.io/projectopia-reporting-add-on/" target="_blank">https://projectopia.io/projectopia-reporting-add-on/</a>'; ?>
							<p><?php printf(__('To use the Reporting part of the plugin, you need to purchase the Projectopia Reporting Add-On. Please visit %1$s for more information.', 'cqpim'), $link); ?></p>
						<?php } ?>						
					</div>
					<div id="tabs-12">
						<h3><?php _e('Email Piping', 'cqpim'); ?></h3>
						<p><?php _e('Email Piping works by scanning your mailbox and parsing new emails into the relevant Task/Support Ticket/Project.', 'cqpim'); ?></p>
						<p><?php _e('We highly recommend creating a new mailbox for this process, as any incoming email (that doesn\'t relate to an existing item) with an address that is in the system as a client will register a new Support Ticket (If configured).', 'cqpim'); ?></p>
						<p><?php _e('It is also critical to place the %%PIPING_ID%% tag in the subject line of all emails related to Support Tickets, Tasks and Project Messages.', 'cqpim'); ?></p>
						<p><?php _e('You should also check that the emails contain the latest update message. You can check for the correct tag by clicking the "View Sample Content" button next to each email.', 'cqpim'); ?></p>
						<h3><?php _e('Mail Settings', 'cqpim'); ?></h3>
						<?php $value = get_option('cqpim_mail_server'); ?>
						<p><?php _e('Mail Server Address (including port and path if necessary. eg. for Gmail, it would be "imap.gmail.com:993/imap/ssl"', 'cqpim'); ?></p>
						<input type="text" name="cqpim_mail_server" id="cqpim_mail_server" value="<?php echo $value; ?>" />
						<br /><br />
						<?php $value = get_option('cqpim_piping_address'); ?>
						<p><?php _e('Email Address (If Piping is active, this address will be the reply address of all ticket/task emails. Ensure it matches the mailbox below)', 'cqpim'); ?></p>
						<input type="text" name="cqpim_piping_address" id="cqpim_piping_address" value="<?php echo $value; ?>" />
						<div class="cqpim-alert cqpim-alert-warning alert-display"><?php _e('Do NOT use the same email address that is used as the "Support Email" address on the company details tab. This will cause a loop as the addresses will keep emailing each other. Use a dedicated mailbox solely for email piping.', 'cqpim'); ?></div>
						<p><?php _e('Email Username (often the same as the email address)', 'cqpim'); ?></p>
						<?php $value = get_option('cqpim_mailbox_name'); ?>
						<input type="text" name="cqpim_mailbox_name" id="cqpim_mailbox_name" value="<?php echo $value; ?>" />
						<br /><br />
						<p><?php _e('Email Password', 'cqpim'); ?></p>
						<?php $value = get_option('cqpim_mailbox_pass'); ?>
						<input type="password" name="cqpim_mailbox_pass" id="cqpim_mailbox_pass" value="<?php echo $value; ?>" /> <button id="test_piping" /><?php _e('Test Settings', 'cqpim'); ?></button> <div id="test_apinner" class="ajax_spinner" style="display:none"></div>
						<br /><br />
						<p><?php _e('ID Prefix', 'cqpim'); ?></p>
						<p><?php _e('The ID Prefix is used in the Piping ID tag and helps the system to identify where updates should go. For example, if you enter "ID" in this field, the result of the %%PIPING_ID%% tag would be "[ID:1234]".','cqpim'); ?></p>
						<?php $value = get_option('cqpim_string_prefix'); ?>
						<input type="text" name="cqpim_string_prefix" value="<?php echo $value; ?>" />
						<br /><br />
						<h3><?php _e('Other Settings', 'cqpim'); ?></h3>
						<?php $value = get_option('cqpim_create_support_on_email'); ?>
						<input type="checkbox" name="cqpim_create_support_on_email" <?php if($value == 1) { echo 'checked="checked"'; } ?> value="1" /> <?php _e('Create a new support ticket if an email arrives from an address registered to a client in the system', 'cqpim'); ?><br /><br />
						<?php $value = get_option('cqpim_send_piping_reject'); ?>
						<input type="checkbox" name="cqpim_send_piping_reject" <?php if($value == 1) { echo 'checked="checked"'; } ?> value="1" /> <?php _e('Send a reject email (below) if an email is received that doesn\'t match a client in the system.', 'cqpim'); ?>
						<br /><br />
						<?php $value = get_option('cqpim_piping_delete'); ?>
						<input type="checkbox" name="cqpim_piping_delete" <?php if($value == 1) { echo 'checked="checked"'; } ?> value="1" /> <?php _e('Delete the email from the Piping inbox once it has been processed.', 'cqpim'); ?>
						<br /><br />
						<h3><?php _e('Reject Email', 'cqpim'); ?></h3>
						<p><?php _e('This email will be sent to the sender if the from email address is not registered in Projectopia.', 'cqpim'); ?></p>
						<p><strong><?php _e('Reject Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="cqpim_bounce_subject" name="cqpim_bounce_subject" value="<?php echo get_option('cqpim_bounce_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('Reject Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="cqpim_bounce_content" name="cqpim_bounce_content"><?php echo get_option('cqpim_bounce_content'); ?></textarea>		
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>							
					</div>
					<?php 
					$user = wp_get_current_user();
					if(in_array('administrator', $user->roles)) { ?>
					<div id="tabs-13">
						<h3><?php _e('Plugin Reset', 'cqpim'); ?></h3>
						<p><?php _e('If you would like to reset the plugin, click the reset button. This will deactivate the plugin and remove ALL data, including settings, roles, permissions and all posts (projects, clients etc). This cannot be undone.', 'cqpim'); ?></p>
						<p><strong><?php _e('IMPORTANT: Any users who have a plugin role will need to have their role reassigned and will not be able to access the site until this is done.', 'cqpim'); ?></strong></p>
						<br /><br />
						<button id="reset-cqpim" class="cqpim_button cqpim_large_button bg-red font-white rounded_2"><?php _e('Reset plugin and remove ALL data', 'cqpim'); ?></button>
						<br /><br />
						<div id="reset_cqpim_container" style="display:none">
							<div id="reset_cqpim">
								<div style="padding:12px">
									<h3><?php _e('Are you sure?', 'cqpim'); ?></h3>
									<p><?php _e('Are you sure you want to deactivate the plugin and remove ALL associated data and settings?', 'cqpim'); ?></p>
									<br />
									<a class="cancel-colorbox  cqpim_button cqpim_small_button bg-red font-white rounded_2 trsp"><?php _e('Cancel', 'cqpim'); ?></a>
									<button class="reset-cqpim-conf cqpim_button cqpim_small_button bg-green font-white rounded_2 right trsp"><?php _e('Confirm', 'cqpim'); ?></button>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
					<div id="tabs-14">
						<h3><?php _e('Enable Messaging System', 'cqpim'); ?></h3>
						<?php $checked = get_option('cqpim_enable_messaging'); ?>
						<input type="checkbox" name="cqpim_enable_messaging" value="1" <?php checked($checked, 1, true); ?> /> <?php _e('Enable Messaging System for Team Members', 'cqpim'); ?>
						<br /><br />
						<?php $checked = get_option('cqpim_messages_allow_client'); ?>
						<input type="checkbox" name="cqpim_messages_allow_client" value="1" <?php checked($checked, 1, true); ?> /> <?php _e('Enable Messaging System for Clients', 'cqpim'); ?>
						<h3><?php _e('New Message Notication', 'cqpim'); ?></h3>
						<p><strong><?php _e('New Message Email Subject', 'cqpim'); ?></strong></p>
						<input type="text" id="cqpim_new_message_subject" name="cqpim_new_message_subject" value="<?php echo get_option('cqpim_new_message_subject'); ?>" />
						<br /><br />
						<p><strong><?php _e('New Message Email Content', 'cqpim'); ?></strong></p>
						<textarea style="width:100%; height:200px" id="cqpim_new_message_content" name="cqpim_new_message_content"><?php echo get_option('cqpim_new_message_content'); ?></textarea>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>						
					</div>
					<div id="tabs-15">
						<h3><?php _e('HTML Email Template', 'cqpim'); ?></h3>
						<p><?php _e('If you would like to customise the outgoing emails from Projectopia with an HTML email template, you can build one here.', 'cqpim'); ?></p>
						<p><?php _e('You can use the %%EMAIL_CONTENT%% tag to render the content of the email in your template', 'cqpim'); ?></p>
						<p><?php _e('You can use the %%LOGO%% tag to render the Company Logo', 'cqpim'); ?></p>
						<h3><?php _e('HTML Email Styles', 'cqpim'); ?></h3>
						<textarea style="width:100%; height:300px" name="cqpim_html_email_styles"><?php echo get_option('cqpim_html_email_styles'); ?></textarea>
						<h3><?php _e('HTML Email Markup', 'cqpim'); ?></h3>
						<textarea style="width:100%; height:500px" name="cqpim_html_email"><?php echo get_option('cqpim_html_email'); ?></textarea>
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
						</p>						
					</div>	
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div><!-- eof #main-container -->
			<div class="clear"></div>
		</form>
		<div class="clear"></div>
	</div><!-- eof.wrap -->
<?php } 