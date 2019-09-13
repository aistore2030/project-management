<?php
add_action('admin_init','pto_add_role_caps', 100);
function pto_add_role_caps() {
	$client_role = get_role('cqpim_client');
	if(!empty($client_role)) {
		$client_role->remove_cap('upload_files');
	}
	$roles = array('cqpim_admin', 'administrator');
	foreach($roles as $the_role) { 
		$role = get_role($the_role);
		if(!empty($role)) {
			$role->add_cap('cqpim_edit_project_colours');
			$role->add_cap('cqpim_view_all_files');
			$role->add_cap('cqpim_view_dashboard');
			$role->add_cap('cqpim_view_tickets');
			$role->add_cap('edit_cqpim_permissions');
			$role->add_cap('cqpim_create_new_client');
			$role->add_cap('cqpim_create_new_lead');
			$role->add_cap('cqpim_create_new_leadform');
			$role->add_cap('cqpim_create_new_form');
			$role->add_cap('cqpim_reset_client_passwords');
			$role->add_cap('cqpim_create_new_team');
			$role->add_cap('cqpim_reset_team_passwords');
			$role->add_cap('edit_cqpim_help');
			$role->add_cap('edit_cqpim_settings');
			$role->add_cap('cqpim_grant_admin_role');
			$role->add_cap('cqpim_create_new_quote');
			$role->add_cap('cqpim_send_quotes');
			$role->add_cap('cqpim_view_project_financials');
			$role->add_cap('cqpim_view_all_projects');
			$role->add_cap('cqpim_publish_all_projects');
			$role->add_cap('cqpim_edit_project_brief');
			$role->add_cap('cqpim_send_project_messages');
			$role->add_cap('cqpim_edit_project_milestones');
			$role->add_cap('cqpim_edit_project_members');
			$role->add_cap('cqpim_edit_project_dates');
			$role->add_cap('cqpim_dash_view_all_tasks');
			$role->add_cap('cqpim_create_new_invoice');			
			$role->add_cap('cqpim_send_invoices');
			$role->add_cap('cqpim_mark_invoice_paid');			
			$role->add_cap('cqpim_view_project_contract');
			$role->add_cap('cqpim_view_project_client_page');
			$role->add_cap('cqpim_mark_project_signedoff');
			$role->add_cap('cqpim_mark_project_closed');
			$role->add_cap('cqpim_create_new_project');
			$role->add_cap('cqpim_create_new_terms');
			$role->add_cap('cqpim_create_new_templates');
			$role->add_cap('cqpim_apply_project_templates');
			$role->add_cap('cqpim_create_new_supports');
			$role->add_cap('cqpim_create_new_faqs');	
			$role->add_cap('cqpim_dash_view_whos_online');
			$role->add_cap('cqpim_view_project_client_info');
			$role->add_cap('cqpim_assign_adhoc_tasks');
			$role->add_cap('cqpim_delete_assigned_tasks');
			$role->add_cap('access_cqpim_messaging');
			$role->add_cap('access_cqpim_messaging_admin');
			$role->add_cap('cqpim_message_all_clients');
			$role->add_cap('cqpim_message_clients_from_projects');
			$role->add_cap('cqpim_team_edit_profile');
			$role->add_cap('edit_cqpim_quote_template');
			$role->add_cap('edit_cqpim_contract_template');
			$role->add_cap('view_posts');
			$role->add_cap('read_private_posts');
			$role->add_cap('edit_posts');
			$role->add_cap('edit_private_posts');			
			$role->add_cap('cqpim_do_all');						
			$role->add_cap('upload_files');
			$role->add_cap('manage_categories');
			// CPT Permissions
			$role->add_cap( 'read_cqpim_client');
			$role->add_cap( 'read_private_cqpim_clients' );
			$role->add_cap( 'edit_cqpim_clients' );
			$role->add_cap( 'edit_others_cqpim_clients' );
			$role->add_cap( 'edit_published_cqpim_clients' );
			$role->add_cap( 'edit_private_cqpim_clients' );
			$role->add_cap( 'publish_cqpim_clients' );
			$role->add_cap( 'delete_cqpim_clients' );
			$role->add_cap( 'delete_others_cqpim_clients' );
			$role->add_cap( 'delete_private_cqpim_clients' );
			$role->add_cap( 'delete_published_cqpim_clients' );		
			$role->add_cap( 'read_cqpim_lead');
			$role->add_cap( 'read_private_cqpim_leads' );
			$role->add_cap( 'edit_cqpim_leads' );
			$role->add_cap( 'edit_others_cqpim_leads' );
			$role->add_cap( 'edit_published_cqpim_leads' );
			$role->add_cap( 'edit_private_cqpim_leads' );
			$role->add_cap( 'publish_cqpim_leads' );
			$role->add_cap( 'delete_cqpim_leads' );
			$role->add_cap( 'delete_others_cqpim_leads' );
			$role->add_cap( 'delete_private_cqpim_leads' );
			$role->add_cap( 'delete_published_cqpim_leads' );
			$role->add_cap( 'read_cqpim_leadform');
			$role->add_cap( 'read_private_cqpim_leadforms' );
			$role->add_cap( 'edit_cqpim_leadforms' );
			$role->add_cap( 'edit_others_cqpim_leadforms' );
			$role->add_cap( 'edit_published_cqpim_leadforms' );
			$role->add_cap( 'edit_private_cqpim_leadforms' );
			$role->add_cap( 'publish_cqpim_leadforms' );
			$role->add_cap( 'delete_cqpim_leadforms' );
			$role->add_cap( 'delete_others_cqpim_leadforms' );
			$role->add_cap( 'delete_private_cqpim_leadforms' );
			$role->add_cap( 'delete_published_cqpim_leadforms' );
			$role->add_cap( 'read_cqpim_task');
			$role->add_cap( 'read_private_cqpim_tasks' );
			$role->add_cap( 'edit_cqpim_tasks' );
			$role->add_cap( 'edit_others_cqpim_tasks' );
			$role->add_cap( 'edit_published_cqpim_tasks' );
			$role->add_cap( 'edit_private_cqpim_tasks' );
			$role->add_cap( 'publish_cqpim_tasks' );
			$role->add_cap( 'read_cqpim_template');
			$role->add_cap( 'read_private_cqpim_templates' );
			$role->add_cap( 'edit_cqpim_templates' );
			$role->add_cap( 'edit_others_cqpim_templates' );
			$role->add_cap( 'edit_published_cqpim_templates' );
			$role->add_cap( 'edit_private_cqpim_templates' );
			$role->add_cap( 'publish_cqpim_templates' );
			$role->add_cap( 'delete_cqpim_templates' );
			$role->add_cap( 'delete_others_cqpim_templates' );
			$role->add_cap( 'delete_private_cqpim_templates' );
			$role->add_cap( 'delete_published_cqpim_templates' );
			$role->add_cap( 'read_cqpim_term');
			$role->add_cap( 'read_private_cqpim_terms' );
			$role->add_cap( 'edit_cqpim_terms' );
			$role->add_cap( 'edit_others_cqpim_terms' );
			$role->add_cap( 'edit_published_cqpim_terms' );
			$role->add_cap( 'edit_private_cqpim_terms' );
			$role->add_cap( 'publish_cqpim_terms' );
			$role->add_cap( 'delete_cqpim_terms' );
			$role->add_cap( 'delete_others_cqpim_terms' );
			$role->add_cap( 'delete_private_cqpim_terms' );
			$role->add_cap( 'delete_published_cqpim_terms' );
			$role->add_cap( 'read_cqpim_form');
			$role->add_cap( 'read_private_cqpim_forms' );
			$role->add_cap( 'edit_cqpim_forms' );
			$role->add_cap( 'edit_others_cqpim_forms' );
			$role->add_cap( 'edit_published_cqpim_forms' );
			$role->add_cap( 'edit_private_cqpim_forms' );
			$role->add_cap( 'publish_cqpim_forms' );
			$role->add_cap( 'delete_cqpim_forms' );
			$role->add_cap( 'delete_others_cqpim_forms' );
			$role->add_cap( 'delete_private_cqpim_forms' );
			$role->add_cap( 'delete_published_cqpim_forms' );
			$role->add_cap( 'read_cqpim_team');
			$role->add_cap( 'read_private_cqpim_teams' );
			$role->add_cap( 'edit_cqpim_teams' );
			$role->add_cap( 'edit_others_cqpim_teams' );
			$role->add_cap( 'edit_published_cqpim_teams' );
			$role->add_cap( 'edit_private_cqpim_teams' );
			$role->add_cap( 'publish_cqpim_teams' );
			$role->add_cap( 'delete_cqpim_teams' );
			$role->add_cap( 'delete_others_cqpim_teams' );
			$role->add_cap( 'delete_private_cqpim_teams' );
			$role->add_cap( 'delete_published_cqpim_teams' );
			$role->add_cap( 'read_cqpim_support');
			$role->add_cap( 'read_private_cqpim_supports' );
			$role->add_cap( 'edit_cqpim_supports' );
			$role->add_cap( 'edit_others_cqpim_supports' );
			$role->add_cap( 'edit_published_cqpim_supports' );
			$role->add_cap( 'edit_private_cqpim_supports' );
			$role->add_cap( 'publish_cqpim_supports' );
			$role->add_cap( 'delete_cqpim_supports' );
			$role->add_cap( 'delete_others_cqpim_supports' );
			$role->add_cap( 'delete_private_cqpim_supports' );
			$role->add_cap( 'delete_published_cqpim_supports' );
			$role->add_cap( 'read_cqpim_project');
			$role->add_cap( 'read_private_cqpim_projects' );
			$role->add_cap( 'edit_cqpim_projects' );
			$role->add_cap( 'edit_others_cqpim_projects' );
			$role->add_cap( 'edit_published_cqpim_projects' );
			$role->add_cap( 'edit_private_cqpim_projects' );
			$role->add_cap( 'publish_cqpim_projects' );
			$role->add_cap( 'delete_cqpim_projects' );
			$role->add_cap( 'delete_others_cqpim_projects' );
			$role->add_cap( 'delete_private_cqpim_projects' );
			$role->add_cap( 'delete_published_cqpim_projects' );
			$role->add_cap( 'read_cqpim_quote');
			$role->add_cap( 'read_private_cqpim_quotes' );
			$role->add_cap( 'edit_cqpim_quotes' );
			$role->add_cap( 'edit_others_cqpim_quotes' );
			$role->add_cap( 'edit_published_cqpim_quotes' );
			$role->add_cap( 'edit_private_cqpim_quotes' );
			$role->add_cap( 'publish_cqpim_quotes' );
			$role->add_cap( 'delete_cqpim_quotes' );
			$role->add_cap( 'delete_others_cqpim_quotes' );
			$role->add_cap( 'delete_private_cqpim_quotes' );
			$role->add_cap( 'delete_published_cqpim_quotes' );
			
			
			$role->add_cap( 'read_cqpim_invoice');
			$role->add_cap( 'read_private_cqpim_invoices' );
			$role->add_cap( 'edit_cqpim_invoices' );
			$role->add_cap( 'edit_others_cqpim_invoices' );
			$role->add_cap( 'edit_published_cqpim_invoices' );
			$role->add_cap( 'edit_private_cqpim_invoices' );
			$role->add_cap( 'publish_cqpim_invoices' );
			$role->add_cap( 'delete_cqpim_invoices' );
			$role->add_cap( 'delete_others_cqpim_invoices' );
			$role->add_cap( 'delete_private_cqpim_invoices' );
			$role->add_cap( 'delete_published_cqpim_invoices' );
			
			
			$role->add_cap( 'read_cqpim_faq');
			$role->add_cap( 'read_private_cqpim_faqs' );
			$role->add_cap( 'edit_cqpim_faqs' );
			$role->add_cap( 'edit_others_cqpim_faqs' );
			$role->add_cap( 'edit_published_cqpim_faqs' );
			$role->add_cap( 'edit_private_cqpim_faqs' );
			$role->add_cap( 'publish_cqpim_faqs' );
			$role->add_cap( 'delete_cqpim_faqs' );
			$role->add_cap( 'delete_others_cqpim_faqs' );
			$role->add_cap( 'delete_private_cqpim_faqs' );
			$role->add_cap( 'delete_published_cqpim_faqs' );
			if(pto_check_addon_status('expenses')) {
				// Custom
				$role->add_cap('cqpim_view_expenses');
				$role->add_cap('cqpim_bypass_expense_auth');
				$role->add_cap('cqpim_auth_expense');
				$role->add_cap('cqpim_create_new_expense');
				$role->add_cap('cqpim_create_new_supplier');
				$role->add_cap('cqpim_view_expenses_admin');
				$role->add_cap( 'read_cqpim_supplier');
				$role->add_cap( 'read_private_cqpim_suppliers' );
				$role->add_cap( 'edit_cqpim_suppliers' );
				$role->add_cap( 'edit_others_cqpim_suppliers' );
				$role->add_cap( 'edit_published_cqpim_suppliers' );
				$role->add_cap( 'edit_private_cqpim_suppliers' );
				$role->add_cap( 'publish_cqpim_suppliers' );
				$role->add_cap( 'delete_cqpim_suppliers' );
				$role->add_cap( 'delete_others_cqpim_suppliers' );
				$role->add_cap( 'delete_private_cqpim_suppliers' );
				$role->add_cap( 'delete_published_cqpim_suppliers' );
				$role->add_cap( 'read_cqpim_expense');
				$role->add_cap( 'read_private_cqpim_expenses' );
				$role->add_cap( 'edit_cqpim_expenses' );
				$role->add_cap( 'edit_others_cqpim_expenses' );
				$role->add_cap( 'edit_published_cqpim_expenses' );
				$role->add_cap( 'edit_private_cqpim_expenses' );
				$role->add_cap( 'publish_cqpim_expenses' );
				$role->add_cap( 'delete_cqpim_expenses' );
				$role->add_cap( 'delete_others_cqpim_expenses' );
				$role->add_cap( 'delete_private_cqpim_expenses' );
				$role->add_cap( 'delete_published_cqpim_expenses' );
			}
			if(pto_check_addon_status('reporting')) {
				$role->add_cap('cqpim_access_reporting');
			}
			if(pto_check_addon_status('bugs')) {
				$role->add_cap( 'read_cqpim_bug');
				$role->add_cap( 'read_private_cqpim_bugs' );
				$role->add_cap( 'edit_cqpim_bugs' );
				$role->add_cap( 'edit_others_cqpim_bugs' );
				$role->add_cap( 'edit_published_cqpim_bugs' );
				$role->add_cap( 'edit_private_cqpim_bugs' );
				$role->add_cap( 'publish_cqpim_bugs' );
				$role->add_cap( 'delete_cqpim_bugs' );
				$role->add_cap( 'delete_others_cqpim_bugs' );
				$role->add_cap( 'delete_private_cqpim_bugs' );
				$role->add_cap( 'delete_published_cqpim_bugs' );
				$role->add_cap( 'cqpim_view_bugs' );
				$role->add_cap( 'cqpim_view_all_bugs' );
				$role->add_cap( 'cqpim_create_new_bug' );	
				$role->add_cap('cqpim_activate_bugs');
			}
			if(pto_check_addon_status('subscriptions')) {
				$role->add_cap( 'read_cqpim_plan');
				$role->add_cap( 'read_private_cqpim_plans' );
				$role->add_cap( 'edit_cqpim_plans' );
				$role->add_cap( 'edit_others_cqpim_plans' );
				$role->add_cap( 'edit_published_cqpim_plans' );
				$role->add_cap( 'edit_private_cqpim_plans' );
				$role->add_cap( 'publish_cqpim_plans' );
				$role->add_cap( 'delete_cqpim_plans' );
				$role->add_cap( 'delete_others_cqpim_plans' );
				$role->add_cap( 'delete_private_cqpim_plans' );
				$role->add_cap( 'delete_published_cqpim_plans' );
				$role->add_cap( 'cqpim_create_new_plan' );
				$role->add_cap( 'read_cqpim_subscription');
				$role->add_cap( 'read_private_cqpim_subscriptions' );
				$role->add_cap( 'edit_cqpim_subscriptions' );
				$role->add_cap( 'edit_others_cqpim_subscriptions' );
				$role->add_cap( 'edit_published_cqpim_subscriptions' );
				$role->add_cap( 'edit_private_cqpim_subscriptions' );
				$role->add_cap( 'publish_cqpim_subscriptions' );
				$role->add_cap( 'delete_cqpim_subscriptions' );
				$role->add_cap( 'delete_others_cqpim_subscriptions' );
				$role->add_cap( 'delete_private_cqpim_subscriptions' );
				$role->add_cap( 'delete_published_cqpim_subscriptions' );
				$role->add_cap( 'cqpim_create_new_subscription' );
				$role->add_cap( 'view_cqpim_subscriptions' );
			}
			if(pto_check_addon_status('woocommerce')) {
				$role->add_cap('view_cqpim_woocommerce');
			}
		}
	}
}
// Custom Role Caps
add_action('admin_init','pto_custom_role_caps');
function pto_custom_role_caps() {
	$cqpim_roles = get_option('cqpim_roles');
	$cqpim_perms = get_option('cqpim_permissions');
	if(!is_array($cqpim_roles)) {
		$cqpim_roles = array(get_option('cqpim_roles'));
	}	
	$roles_to_assign = array();
	foreach($cqpim_roles as $cqpim_role) {
		if($cqpim_role != 'cqpim_admin') {
			$roles_to_assign[] = 'cqpim_' . $cqpim_role;
		}
	}
	foreach($roles_to_assign as $the_role) {
		$role = get_role($the_role);
		if(!empty($role)) {
			$role->add_cap('view_posts');
			$role->add_cap('read_private_posts');
			$role->add_cap('edit_posts');
			$role->add_cap('edit_private_posts');
			$role->add_cap('cqpim_view_dashboard');				
			$role->add_cap('access_cqpim_messaging');
			// Grant Read/Update on all Projects that are assigned
			$role->add_cap( 'read_cqpim_project');
			$role->add_cap( 'read_private_cqpim_projects' );
			$role->add_cap( 'edit_cqpim_projects' );
			$role->add_cap( 'edit_others_cqpim_projects' );
			$role->add_cap( 'edit_published_cqpim_projects' );
			$role->add_cap( 'edit_private_cqpim_projects' );
			$role->add_cap( 'publish_cqpim_projects' );
			$role->add_cap( 'read_cqpim_task');
			$role->add_cap( 'read_private_cqpim_tasks' );
			$role->add_cap( 'edit_cqpim_tasks' );
			$role->add_cap( 'edit_others_cqpim_tasks' );
			$role->add_cap( 'edit_published_cqpim_tasks' );
			$role->add_cap( 'edit_private_cqpim_tasks' );
			$role->add_cap( 'publish_cqpim_tasks' );
			$role->add_cap('upload_files');
			$role_to_check = str_replace('cqpim_', '', $the_role);
			if($cqpim_perms) {
				foreach($cqpim_perms as $key => $perm) {
					if(in_array($role_to_check, $perm)) {
						$role->add_cap($key);
						if($key == 'cqpim_view_tickets') {
							$role->add_cap( 'read_cqpim_support');
							$role->add_cap( 'read_private_cqpim_supports' );
							$role->add_cap( 'edit_cqpim_supports' );
							$role->add_cap( 'edit_others_cqpim_supports' );
							$role->add_cap( 'edit_published_cqpim_supports' );
							$role->add_cap( 'edit_private_cqpim_supports' );						
						}
						if($key == 'delete_cqpim_supports') {
							$role->add_cap( 'delete_others_cqpim_supports' );
							$role->add_cap( 'delete_private_cqpim_supports' );
							$role->add_cap( 'delete_published_cqpim_supports' );						
						}
						if($key == 'cqpim_view_bugs') {
							$role->add_cap( 'read_cqpim_bug');
							$role->add_cap( 'read_private_cqpim_bugs' );
							$role->add_cap( 'edit_cqpim_bugs' );
							$role->add_cap( 'edit_others_cqpim_bugs' );
							$role->add_cap( 'edit_published_cqpim_bugs' );
							$role->add_cap( 'edit_private_cqpim_bugs' );						
						}
						if($key == 'delete_cqpim_bugs') {
							$role->add_cap( 'delete_others_cqpim_bugs' );
							$role->add_cap( 'delete_private_cqpim_bugs' );
							$role->add_cap( 'delete_published_cqpim_bugs' );						
						}
						if($key == 'read_cqpim_client') {
							$role->add_cap( 'read_private_cqpim_clients' );
							$role->add_cap( 'edit_cqpim_clients' );
							$role->add_cap( 'edit_others_cqpim_clients' );
							$role->add_cap( 'edit_published_cqpim_clients' );
							$role->add_cap( 'edit_private_cqpim_clients' );					
						}
						if($key == 'delete_cqpim_clients') {
							$role->add_cap( 'delete_others_cqpim_clients' );
							$role->add_cap( 'delete_private_cqpim_clients' );
							$role->add_cap( 'delete_published_cqpim_clients' );						
						}
						if($key == 'read_cqpim_lead') {
							$role->add_cap( 'read_private_cqpim_leads' );
							$role->add_cap( 'edit_cqpim_leads' );
							$role->add_cap( 'edit_others_cqpim_leads' );
							$role->add_cap( 'edit_published_cqpim_leads' );
							$role->add_cap( 'edit_private_cqpim_leads' );					
						}
						if($key == 'delete_cqpim_leads') {
							$role->add_cap( 'delete_others_cqpim_leads' );
							$role->add_cap( 'delete_private_cqpim_leads' );
							$role->add_cap( 'delete_published_cqpim_leads' );						
						}
						if($key == 'read_cqpim_leadform') {
							$role->add_cap( 'read_private_cqpim_leadforms' );
							$role->add_cap( 'edit_cqpim_leadforms' );
							$role->add_cap( 'edit_others_cqpim_leadforms' );
							$role->add_cap( 'edit_published_cqpim_leadforms' );
							$role->add_cap( 'edit_private_cqpim_leadforms' );					
						}
						if($key == 'delete_cqpim_leadforms') {
							$role->add_cap( 'delete_others_cqpim_leadforms' );
							$role->add_cap( 'delete_private_cqpim_leadforms' );
							$role->add_cap( 'delete_published_cqpim_leadforms' );						
						}
						if($key == 'read_cqpim_faq') {
							$role->add_cap( 'read_private_cqpim_faqs' );
							$role->add_cap( 'edit_cqpim_faqs' );
							$role->add_cap( 'edit_others_cqpim_faqs' );
							$role->add_cap( 'edit_published_cqpim_faqs' );
							$role->add_cap( 'edit_private_cqpim_faqs' );					
						}
						if($key == 'delete_cqpim_faqs') {
							$role->add_cap( 'delete_others_cqpim_faqs' );
							$role->add_cap( 'delete_private_cqpim_faqs' );
							$role->add_cap( 'delete_published_cqpim_faqs' );						
						}
						if($key == 'read_cqpim_templates') {
							$role->add_cap( 'read_private_cqpim_templates' );
							$role->add_cap( 'edit_cqpim_templates' );
							$role->add_cap( 'edit_others_cqpim_templates' );
							$role->add_cap( 'edit_published_cqpim_templates' );
							$role->add_cap( 'edit_private_cqpim_templates' );					
						}
						if($key == 'delete_cqpim_templates') {
							$role->add_cap( 'delete_others_cqpim_templates' );
							$role->add_cap( 'delete_private_cqpim_templates' );
							$role->add_cap( 'delete_published_cqpim_templates' );						
						}
						if($key == 'read_cqpim_terms') {
							$role->add_cap( 'read_private_cqpim_terms' );
							$role->add_cap( 'edit_cqpim_terms' );
							$role->add_cap( 'edit_others_cqpim_terms' );
							$role->add_cap( 'edit_published_cqpim_terms' );
							$role->add_cap( 'edit_private_cqpim_terms' );					
						}
						if($key == 'delete_cqpim_terms') {
							$role->add_cap( 'delete_others_cqpim_terms' );
							$role->add_cap( 'delete_private_cqpim_terms' );
							$role->add_cap( 'delete_published_cqpim_terms' );						
						}
						if($key == 'read_cqpim_team') {
							$role->add_cap( 'read_private_cqpim_teams' );
							$role->add_cap( 'edit_cqpim_teams' );
							$role->add_cap( 'edit_others_cqpim_teams' );
							$role->add_cap( 'edit_published_cqpim_teams' );
							$role->add_cap( 'edit_private_cqpim_teams' );					
						}
						if($key == 'delete_cqpim_teams') {
							$role->add_cap( 'delete_others_cqpim_teams' );
							$role->add_cap( 'delete_private_cqpim_teams' );
							$role->add_cap( 'delete_published_cqpim_teams' );						
						}
						if($key == 'read_cqpim_quote') {
							$role->add_cap( 'read_private_cqpim_quotes' );
							$role->add_cap( 'edit_cqpim_quotes' );
							$role->add_cap( 'edit_others_cqpim_quotes' );
							$role->add_cap( 'edit_published_cqpim_quotes' );
							$role->add_cap( 'edit_private_cqpim_quotes' );					
						}
						if($key == 'delete_cqpim_quotes') {
							$role->add_cap( 'delete_others_cqpim_quotes' );
							$role->add_cap( 'delete_private_cqpim_quotes' );
							$role->add_cap( 'delete_published_cqpim_quotes' );						
						}
						if($key == 'read_cqpim_form') {
							$role->add_cap( 'read_private_cqpim_forms' );
							$role->add_cap( 'edit_cqpim_forms' );
							$role->add_cap( 'edit_others_cqpim_forms' );
							$role->add_cap( 'edit_published_cqpim_forms' );
							$role->add_cap( 'edit_private_cqpim_forms' );					
						}
						if($key == 'delete_cqpim_forms') {
							$role->add_cap( 'delete_others_cqpim_forms' );
							$role->add_cap( 'delete_private_cqpim_forms' );
							$role->add_cap( 'delete_published_cqpim_forms' );						
						}
						if($key == 'delete_cqpim_projects') {
							$role->add_cap( 'delete_others_cqpim_projects' );
							$role->add_cap( 'delete_private_cqpim_projects' );
							$role->add_cap( 'delete_published_cqpim_projects' );						
						}
						if($key == 'read_cqpim_invoice') {
							$role->add_cap( 'read_private_cqpim_invoices' );
							$role->add_cap( 'edit_cqpim_invoices' );
							$role->add_cap( 'edit_others_cqpim_invoices' );
							$role->add_cap( 'edit_published_cqpim_invoices' );
							$role->add_cap( 'edit_private_cqpim_invoices' );					
						}
						if($key == 'delete_cqpim_invoices') {
							$role->add_cap( 'delete_others_cqpim_invoices' );
							$role->add_cap( 'delete_private_cqpim_invoices' );
							$role->add_cap( 'delete_published_cqpim_invoices' );						
						}
						if($key == 'read_cqpim_supplier') {
							$role->add_cap( 'read_private_cqpim_suppliers' );
							$role->add_cap( 'edit_cqpim_suppliers' );
							$role->add_cap( 'edit_others_cqpim_suppliers' );
							$role->add_cap( 'edit_published_cqpim_suppliers' );
							$role->add_cap( 'edit_private_cqpim_suppliers' );					
						}
						if($key == 'delete_cqpim_suppliers') {
							$role->add_cap( 'delete_others_cqpim_suppliers' );
							$role->add_cap( 'delete_private_cqpim_suppliers' );
							$role->add_cap( 'delete_published_cqpim_suppliers' );						
						}
						if($key == 'read_cqpim_expense') {
							$role->add_cap( 'cqpim_view_expenses' );
							$role->add_cap( 'read_private_cqpim_expenses' );
							$role->add_cap( 'edit_cqpim_expenses' );
							$role->add_cap( 'edit_others_cqpim_expenses' );
							$role->add_cap( 'edit_published_cqpim_expenses' );
							$role->add_cap( 'edit_private_cqpim_expenses' );					
						}
						if($key == 'delete_cqpim_expenses') {
							$role->add_cap( 'delete_others_cqpim_expenses' );
							$role->add_cap( 'delete_private_cqpim_expenses' );
							$role->add_cap( 'delete_published_cqpim_expenses' );						
						}
						if($key == 'read_cqpim_subscription') {
							$role->add_cap( 'read_private_cqpim_subscriptions' );
							$role->add_cap( 'edit_cqpim_subscriptions' );
							$role->add_cap( 'edit_others_cqpim_subscriptions' );
							$role->add_cap( 'edit_published_cqpim_subscriptions' );
							$role->add_cap( 'edit_private_cqpim_subscriptions' );
							$role->add_cap( 'view_cqpim_subscriptions' );	
						}
						if($key == 'delete_cqpim_subscriptions') {
							$role->add_cap( 'delete_others_cqpim_subscriptions' );
							$role->add_cap( 'delete_private_cqpim_subscriptions' );
							$role->add_cap( 'delete_published_cqpim_subscriptions' );						
						}
						if($key == 'read_cqpim_plan') {
							$role->add_cap( 'read_private_cqpim_plans' );
							$role->add_cap( 'edit_cqpim_plans' );
							$role->add_cap( 'edit_others_cqpim_plans' );
							$role->add_cap( 'edit_published_cqpim_plans' );
							$role->add_cap( 'edit_private_cqpim_plans' );					
						}
						if($key == 'delete_cqpim_plans') {
							$role->add_cap( 'delete_others_cqpim_plans' );
							$role->add_cap( 'delete_private_cqpim_plans' );
							$role->add_cap( 'delete_published_cqpim_plans' );						
						}
					} else {
						$role->remove_cap($key);
						if($key == 'read_cqpim_subscription') {
							$role->remove_cap( 'read_private_cqpim_subscriptions' );
							$role->remove_cap( 'edit_cqpim_subscriptions' );
							$role->remove_cap( 'edit_others_cqpim_subscriptions' );
							$role->remove_cap( 'edit_published_cqpim_subscriptions' );
							$role->remove_cap( 'edit_private_cqpim_subscriptions' );
							$role->remove_cap( 'view_cqpim_subscriptions' );
						}
						if($key == 'delete_cqpim_subscriptions') {
							$role->remove_cap( 'delete_others_cqpim_subscriptions' );
							$role->remove_cap( 'delete_private_cqpim_subscriptions' );
							$role->remove_cap( 'delete_published_cqpim_subscriptions' );						
						}
						if($key == 'read_cqpim_plan') {
							$role->remove_cap( 'read_private_cqpim_plans' );
							$role->remove_cap( 'edit_cqpim_plans' );
							$role->remove_cap( 'edit_others_cqpim_plans' );
							$role->remove_cap( 'edit_published_cqpim_plans' );
							$role->remove_cap( 'edit_private_cqpim_plans' );					
						}
						if($key == 'delete_cqpim_plans') {
							$role->remove_cap( 'delete_others_cqpim_plans' );
							$role->remove_cap( 'delete_private_cqpim_plans' );
							$role->remove_cap( 'delete_published_cqpim_plans' );						
						}
						if($key == 'cqpim_view_bugs') {
							$role->remove_cap( 'read_cqpim_bug');
							$role->remove_cap( 'read_private_cqpim_bugs' );
							$role->remove_cap( 'edit_cqpim_bugs' );
							$role->remove_cap( 'edit_others_cqpim_bugs' );
							$role->remove_cap( 'edit_published_cqpim_bugs' );
							$role->remove_cap( 'edit_private_cqpim_bugs' );						
						}
						if($key == 'delete_cqpim_bugs') {
							$role->remove_cap( 'delete_others_cqpim_bugs' );
							$role->remove_cap( 'delete_private_cqpim_bugs' );
							$role->remove_cap( 'delete_published_cqpim_bugs' );						
						}
						if($key == 'cqpim_view_tickets') {
							$role->remove_cap( 'read_cqpim_support');
							$role->remove_cap( 'read_private_cqpim_supports' );
							$role->remove_cap( 'edit_cqpim_supports' );
							$role->remove_cap( 'edit_others_cqpim_supports' );
							$role->remove_cap( 'edit_published_cqpim_supports' );
							$role->remove_cap( 'edit_private_cqpim_supports' );						
						}
						if($key == 'delete_cqpim_supports') {
							$role->remove_cap( 'delete_others_cqpim_supports' );
							$role->remove_cap( 'delete_private_cqpim_supports' );
							$role->remove_cap( 'delete_published_cqpim_supports' );						
						}
						if($key == 'read_cqpim_client') {
							$role->remove_cap( 'read_private_cqpim_clients' );
							$role->remove_cap( 'edit_cqpim_clients' );
							$role->remove_cap( 'edit_others_cqpim_clients' );
							$role->remove_cap( 'edit_published_cqpim_clients' );
							$role->remove_cap( 'edit_private_cqpim_clients' );					
						}
						if($key == 'delete_cqpim_clients') {
							$role->remove_cap( 'delete_others_cqpim_clients' );
							$role->remove_cap( 'delete_private_cqpim_clients' );
							$role->remove_cap( 'delete_published_cqpim_clients' );						
						}
						if($key == 'read_cqpim_lead') {
							$role->remove_cap( 'read_private_cqpim_leads' );
							$role->remove_cap( 'edit_cqpim_leads' );
							$role->remove_cap( 'edit_others_cqpim_leads' );
							$role->remove_cap( 'edit_published_cqpim_leads' );
							$role->remove_cap( 'edit_private_cqpim_leads' );					
						}
						if($key == 'delete_cqpim_leads') {
							$role->remove_cap( 'delete_others_cqpim_leads' );
							$role->remove_cap( 'delete_private_cqpim_leads' );
							$role->remove_cap( 'delete_published_cqpim_leads' );						
						}
						if($key == 'read_cqpim_leadform') {
							$role->remove_cap( 'read_private_cqpim_leadforms' );
							$role->remove_cap( 'edit_cqpim_leadforms' );
							$role->remove_cap( 'edit_others_cqpim_leadforms' );
							$role->remove_cap( 'edit_published_cqpim_leadforms' );
							$role->remove_cap( 'edit_private_cqpim_leadforms' );					
						}
						if($key == 'delete_cqpim_leadforms') {
							$role->remove_cap( 'delete_others_cqpim_leadforms' );
							$role->remove_cap( 'delete_private_cqpim_leadforms' );
							$role->remove_cap( 'delete_published_cqpim_leadforms' );						
						}
						if($key == 'read_cqpim_faq') {
							$role->remove_cap( 'read_private_cqpim_faqs' );
							$role->remove_cap( 'edit_cqpim_faqs' );
							$role->remove_cap( 'edit_others_cqpim_faqs' );
							$role->remove_cap( 'edit_published_cqpim_faqs' );
							$role->remove_cap( 'edit_private_cqpim_faqs' );					
						}
						if($key == 'delete_cqpim_faqs') {
							$role->remove_cap( 'delete_others_cqpim_faqs' );
							$role->remove_cap( 'delete_private_cqpim_faqs' );
							$role->remove_cap( 'delete_published_cqpim_faqs' );						
						}
						if($key == 'read_cqpim_templates') {
							$role->remove_cap( 'read_private_cqpim_templates' );
							$role->remove_cap( 'edit_cqpim_templates' );
							$role->remove_cap( 'edit_others_cqpim_templates' );
							$role->remove_cap( 'edit_published_cqpim_templates' );
							$role->remove_cap( 'edit_private_cqpim_templates' );					
						}
						if($key == 'delete_cqpim_templates') {
							$role->remove_cap( 'delete_others_cqpim_templates' );
							$role->remove_cap( 'delete_private_cqpim_templates' );
							$role->remove_cap( 'delete_published_cqpim_templates' );						
						}
						if($key == 'read_cqpim_terms') {
							$role->remove_cap( 'read_private_cqpim_terms' );
							$role->remove_cap( 'edit_cqpim_terms' );
							$role->remove_cap( 'edit_others_cqpim_terms' );
							$role->remove_cap( 'edit_published_cqpim_terms' );
							$role->remove_cap( 'edit_private_cqpim_terms' );					
						}
						if($key == 'delete_cqpim_terms') {
							$role->remove_cap( 'delete_others_cqpim_terms' );
							$role->remove_cap( 'delete_private_cqpim_terms' );
							$role->remove_cap( 'delete_published_cqpim_terms' );						
						}
						if($key == 'read_cqpim_team') {
							$role->remove_cap( 'read_private_cqpim_teams' );
							$role->remove_cap( 'edit_cqpim_teams' );
							$role->remove_cap( 'edit_others_cqpim_teams' );
							$role->remove_cap( 'edit_published_cqpim_teams' );
							$role->remove_cap( 'edit_private_cqpim_teams' );					
						}
						if($key == 'delete_cqpim_teams') {
							$role->remove_cap( 'delete_others_cqpim_teams' );
							$role->remove_cap( 'delete_private_cqpim_teams' );
							$role->remove_cap( 'delete_published_cqpim_teams' );						
						}
						if($key == 'read_cqpim_quote') {
							$role->remove_cap( 'read_private_cqpim_quotes' );
							$role->remove_cap( 'edit_cqpim_quotes' );
							$role->remove_cap( 'edit_others_cqpim_quotes' );
							$role->remove_cap( 'edit_published_cqpim_quotes' );
							$role->remove_cap( 'edit_private_cqpim_quotes' );					
						}
						if($key == 'delete_cqpim_quotes') {
							$role->remove_cap( 'delete_others_cqpim_quotes' );
							$role->remove_cap( 'delete_private_cqpim_quotes' );
							$role->remove_cap( 'delete_published_cqpim_quotes' );						
						}
						if($key == 'read_cqpim_form') {
							$role->remove_cap( 'read_private_cqpim_forms' );
							$role->remove_cap( 'edit_cqpim_forms' );
							$role->remove_cap( 'edit_others_cqpim_forms' );
							$role->remove_cap( 'edit_published_cqpim_forms' );
							$role->remove_cap( 'edit_private_cqpim_forms' );					
						}
						if($key == 'delete_cqpim_forms') {
							$role->remove_cap( 'delete_others_cqpim_forms' );
							$role->remove_cap( 'delete_private_cqpim_forms' );
							$role->remove_cap( 'delete_published_cqpim_forms' );						
						}
						if($key == 'delete_cqpim_projects') {
							$role->remove_cap( 'delete_others_cqpim_projects' );
							$role->remove_cap( 'delete_private_cqpim_projects' );
							$role->remove_cap( 'delete_published_cqpim_projects' );						
						}
						if($key == 'read_cqpim_invoice') {
							$role->remove_cap( 'read_private_cqpim_invoices' );
							$role->remove_cap( 'edit_cqpim_invoices' );
							$role->remove_cap( 'edit_others_cqpim_invoices' );
							$role->remove_cap( 'edit_published_cqpim_invoices' );
							$role->remove_cap( 'edit_private_cqpim_invoices' );					
						}
						if($key == 'delete_cqpim_invoices') {
							$role->remove_cap( 'delete_others_cqpim_invoices' );
							$role->remove_cap( 'delete_private_cqpim_invoices' );
							$role->remove_cap( 'delete_published_cqpim_invoices' );						
						}
						if($key == 'read_cqpim_supplier') {
							$role->remove_cap( 'read_private_cqpim_suppliers' );
							$role->remove_cap( 'edit_cqpim_suppliers' );
							$role->remove_cap( 'edit_others_cqpim_suppliers' );
							$role->remove_cap( 'edit_published_cqpim_suppliers' );
							$role->remove_cap( 'edit_private_cqpim_suppliers' );					
						}
						if($key == 'delete_cqpim_suppliers') {
							$role->remove_cap( 'delete_others_cqpim_suppliers' );
							$role->remove_cap( 'delete_private_cqpim_suppliers' );
							$role->remove_cap( 'delete_published_cqpim_suppliers' );						
						}
						if($key == 'read_cqpim_expense') {
							$role->remove_cap( 'cqpim_view_expenses' );
							$role->remove_cap( 'read_private_cqpim_expenses' );
							$role->remove_cap( 'edit_cqpim_expenses' );
							$role->remove_cap( 'edit_others_cqpim_expenses' );
							$role->remove_cap( 'edit_published_cqpim_expenses' );
							$role->remove_cap( 'edit_private_cqpim_expenses' );					
						}
						if($key == 'delete_cqpim_expenses') {
							$role->remove_cap( 'delete_others_cqpim_expenses' );
							$role->remove_cap( 'delete_private_cqpim_expenses' );
							$role->remove_cap( 'delete_published_cqpim_expenses' );						
						}
					}
				}
			}
		}
	}
}

add_action('admin_init', 'pto_manage_roles');
function pto_manage_roles() {
	$roles = get_option('cqpim_roles'); 
	if(!is_array($roles)) {
		$roles = array(get_option('cqpim_roles'));
	}
	if($roles) {
		foreach($roles as $role) {
			if($role != 'cqpim_admin') {
				$cqpim_role = 'cqpim_' . $role;
				$role_exists = get_role($cqpim_role);
				if(!$role_exists) {
					$caps = array(
						'read' => true,
						'upload_files' => true,
					);
					$role_name = str_replace('_', ' ', $cqpim_role);
					$role_name = str_replace('cqpim', 'Projectopia', $role_name);
					$role_name = ucwords($role_name);
					add_role($cqpim_role, $role_name, $caps);
				}
			}
		}
	}
	if ( !function_exists('get_editable_roles') ) {
	require_once( ABSPATH . '/wp-admin/includes/user.php' );
	}
	$system_roles = get_editable_roles();
	$plugin_roles = get_option('cqpim_roles');
	if(!is_array($plugin_roles)) {
		$plugin_roles = array(get_option('cqpim_roles'));
	}
	$plugin_roles_ready = array();
	foreach($plugin_roles as $plugin_role) {
		if($plugin_role == 'cqpim_admin') {
			$plugin_roles_ready[] = $plugin_role;
		} else {
			$plugin_roles_ready[] = 'cqpim_' . $plugin_role;
		}
	}	
	foreach($system_roles as $key => $system_role) {
		if(strpos($key,'cqpim_') !== false && !in_array($key, $plugin_roles_ready)) {
			if($key != 'cqpim_client') {
				remove_role($key);
			}
		}
	}	
}
add_action('wp','pto_restrict_uploads', 20);
function pto_restrict_uploads() {
	global $post;
	$user = wp_get_current_user();
	$roles = $user->roles;
	if(in_array('cqpim_client', $roles) && !empty($post)) {
		$dash_page = get_option('cqpim_client_page');
		$form_page = get_option('cqpim_form_page');
		if('cqpim_tasks' == $post->post_type || 'cqpim_bug' == $post->post_type ||'cqpim_support' == $post->post_type || 'cqpim_project' == $post->post_type || $post->ID == $dash_page || $post->ID == $form_page) {
			if(!empty($user)) {
				$user->add_cap('upload_files');
			}
		} else {
			if(!empty($user)) {
				$user->remove_cap('upload_files');
			}	
		}
	}
}