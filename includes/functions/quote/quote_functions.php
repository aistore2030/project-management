<?php
add_action( "wp_ajax_pto_update_client_contacts", "pto_update_client_contacts");
function pto_update_client_contacts() {
	$data = isset($_POST) ? $_POST : array();
	$client_id = isset($data['client_id']) ? $data['client_id'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_contact = '';
	$main_user_id = $client_details['user_id'];
	$client_contact .= '<option value="' . $main_user_id . '">' . $client_details['client_contact'] . ' - ' . sprintf(__('Main Contact', 'cqpim')) . '</option>';
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	if(empty($client_contacts)) {
		$client_contacts = array();
	}
	foreach($client_contacts as $contact) {
		$client_contact .= '<option value="' . $contact['user_id'] . '">' . $contact['name'] . '</option>';
	}
	$return =  array( 
		'error' 	=> false,
		'contacts' 	=> $client_contact,
	);
	header('Content-type: application/json');
	echo json_encode($return);
	exit();
}
add_action( "wp_ajax_pto_add_step_to_quote", "pto_add_step_to_quote");
function pto_add_step_to_quote() {
	$quote_id = isset($_POST['ID']) ? $_POST['ID'] : '';
	$title = isset($_POST['title']) ? $_POST['title'] : '';
	$start = isset($_POST['start']) ? $_POST['start'] : '';
	$weight = isset($_POST['weight']) ? $_POST['weight'] : 1;
	if(!empty($start)) {
		$start = DateTime::createFromFormat(get_option('cqpim_date_format'), $start)->getTimestamp();
	}
	$deadline = isset($_POST['deadline']) ? $_POST['deadline'] : '';
	if(!empty($deadline)) {
		$deadline = DateTime::createFromFormat(get_option('cqpim_date_format'), $deadline)->getTimestamp();
	}
	$milestone_id = isset($_POST['milestone_id']) ? $_POST['milestone_id'] : '';
	$cost = isset($_POST['cost']) ? $_POST['cost'] : '';
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	if($title && $deadline) {
		if($type == 'project') {
			$quote_elements = get_post_meta($quote_id, 'project_elements', true);
		} else {
			$quote_elements = get_post_meta($quote_id, 'quote_elements', true);
		}
		$i = 0;
		$quote_elements = $quote_elements&&is_array($quote_elements)?$quote_elements:array();
		foreach($quote_elements as $element) {
			$i++;
		}
		$element_to_add = array(
			'title' => $title,
			'id' => $milestone_id,
			'deadline' => $deadline,
			'start' => $start,
			'cost' => $cost,
			'weight' => $weight,
		);
		$quote_elements[$milestone_id] = $element_to_add;
		if($type == 'project') {
			update_post_meta($quote_id, 'project_elements', $quote_elements);
		} else {
			update_post_meta($quote_id, 'quote_elements', $quote_elements);
		}
		if($type == 'project') {
			$current_user = wp_get_current_user();
			$current_user = $current_user->display_name;
			$project_progress = get_post_meta($quote_id, 'project_progress', true);
			$project_progress = $project_progress&&is_array($project_progress)?$project_progress:array();			
			$project_progress[] = array(
				'update' => __('Milestone Created', 'cqpim') . ': ' . $title,
				'date' => current_time('timestamp'),
				'by' => $current_user
			);
			update_post_meta($quote_id, 'project_progress', $project_progress );
		}
		return true;
	} else {
		$return =  array( 
			'error' 	=> true,
			'errors' 	=> __('You must fill in the title and deadline as a minimum.', 'cqpim')
		);
		header('Content-type: application/json');
		echo json_encode($return);
	}
	exit();
}
add_action( "wp_ajax_pto_process_quote_emails", "pto_process_quote_emails");
function pto_process_quote_emails() {
	$user = wp_get_current_user();
	$quote_id = $_POST['quote_id'];
	$quote_object = get_post($quote_id);
	$quote_details = get_post_meta($quote_id, 'quote_details', true);
	$client_id = $quote_details['client_id'];
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	$client_contact = isset($quote_details['client_contact']) ? $quote_details['client_contact'] : $client_details['user_id'];
	$client_main_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
	pto_add_team_notification($client_id, $user->ID, $quote_id, 'quote_sent', 'quote');
	if(empty($client_contacts)) {
		$client_contacts = array();
	}
	if(!empty($client_contact)) {
		if($client_contact == $client_main_id) {
			$to = $client_details['client_email'];
		} else {
			$to = $client_contacts[$client_contact]['email'];
		}
	} else {
		$to = $client_details['client_email'];
	}
	$email_content = get_option('quote_default_email');
	if($client_contact == $client_main_id) {
		$email_content = str_replace('%%CLIENT_NAME%%', $client_details['client_contact'], $email_content);
		$email_content = str_replace('%%CLIENT_EMAIL%%', $client_details['client_email'], $email_content);
	} else {
		$email_content = str_replace('%%CLIENT_NAME%%', isset($client_contacts[$client_contact]['name']) ? $client_contacts[$client_contact]['name'] : '', $email_content);
		$email_content = str_replace('%%CLIENT_EMAIL%%', isset($client_contacts[$client_contact]['name']) ? $client_contacts[$client_contact]['email'] : '', $email_content);
	}
	$message = pto_replacement_patterns($email_content, $quote_id, 'quote');
	$subject = get_option('quote_email_subject');
	if($client_contact == $client_main_id) {
		$subject = str_replace('%%CLIENT_NAME%%', $client_details['client_contact'], $subject);
	} else {
		$subject = str_replace('%%CLIENT_NAME%%', isset($client_contacts[$client_contact]['name']) ? $client_contacts[$client_contact]['name'] : '', $subject);
	}
	$subject = pto_replacement_patterns($subject, $quote_id, 'quote');
	$attachments = array();
	if( $to && $subject && $message ){
		if( pto_send_emails( $to, $subject, $message, '', $attachments, 'sales' ) ) :
			$current_user = wp_get_current_user();
			$current_user = $current_user->display_name;
			$quote_details = get_post_meta($quote_id, 'quote_details', true);
			$quote_details['sent_details'] = array(
					'date' 	=> current_time('timestamp'),
					'by'	=> $current_user,
					'to'    => $to,
			);
			unset($quote_details['confirmed']);
			unset($quote_details['confirmed_details']);
			$quote_details['sent'] = true;
			update_post_meta($quote_id, 'quote_details', $quote_details );	
			$return =  array( 
				'error' 	=> false,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Email sent successfully...', 'cqpim') . '</div>'
			);
			header('Content-type: application/json');
			echo json_encode($return);		
		else :
			$return =  array( 
				'error' 	=> true,
				'errors' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('There was a problem with WP Mail, check that your installation is able to send emails and try again.', 'cqpim') . '</div>'
			);
			header('Content-type: application/json');
			echo json_encode($return);
		endif;	
	} else {
		$return =  array( 
			'error' 	=> true,
			'errors' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('There was a problem sending the email, check that you have completed ALL email subject and content fields in the settings.', 'cqpim') . '</div>'
		);
		header('Content-type: application/json');
		echo json_encode($return);
	}
	exit();
}
add_action("wp_ajax_nopriv_pto_client_accept_quote", "pto_client_accept_quote");
add_action("wp_ajax_pto_client_accept_quote", "pto_client_accept_quote");
function pto_client_accept_quote() {
	$user = wp_get_current_user();
	$quote_id = isset( $_POST['quote_id'] ) ? (int) $_POST['quote_id'] : 0;
	$signed_name = isset( $_POST['name'] ) ? $_POST['name'] : '';
	$pm_name = isset( $_POST['pm_name'] ) ? $_POST['pm_name'] : '';
	$quote_details = get_post_meta( $quote_id, 'quote_details', true );
	$quote_ref = $quote_details['quote_ref'];
	$quote_type = $quote_details['quote_type'];
	$ip = pto_get_client_ip();
	if($signed_name) {
		$quote_details['confirmed_details'] = array(
			'date' 	=> current_time('timestamp'),
			'by'	=> $signed_name,
			'ip'	=> $ip,
		);
		$quote_details['confirmed'] = true;
		update_post_meta( $quote_id, 'quote_details', $quote_details );
		$sender_email = get_option('company_sales_email');
		$to = $sender_email;
		$attachments = array();
		$admin_quote = admin_url() . 'post.php?post=' . $quote_id . '&action=edit';
		$subject = sprintf(__('%1$s has just accepted Quote: %2$s', 'cqpim'), $signed_name, $quote_ref);
		$content = sprintf(__('%1$s has just accepted Quote: %2$s. You can view the details by clicking here - %3$s', 'cqpim'), $signed_name, $quote_ref, $admin_quote);
		$args = array(
			'post_type' => 'cqpim_teams',
			'posts_per_page' => -1,
			'post_status' => 'private'
		);
		$team_members = get_posts($args); 
		foreach($team_members as $member) {
			$team_details = get_post_meta($member->ID, 'team_details', true);
			$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
			$user_obj = get_user_by('id', $user_id);
			if(!empty($user_obj) && user_can($user_obj, 'edit_cqpim_quotes')) {
				pto_add_team_notification($member->ID, $user->ID, $quote_id, 'quote_accepted');
			}
		}
		pto_send_emails( $to, $subject, $content, '', $attachments, 'sales' );
		$return =  array( 
			'error' 	=> false,
			'message' 	=> __('All good!', 'cqpim')
		);
		header('Content-type: application/json');
		echo json_encode($return);
		if(get_option('enable_project_creation') == 1) {
			pto_create_project_from_quote($quote_id, $pm_name);
		}
	} else {
		$return =  array( 
			'error' 	=> true,
			'errors' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('There was a problem, please try again.', 'cqpim') . '</div>'
		);
		header('Content-type: application/json');
		echo json_encode($return);		
	}
	die();
}
function pto_create_project_from_quote($quote_id, $pm_name = NULL) {
	$quote_details = get_post_meta($quote_id, 'quote_details', true);
	$quote_milestones = get_post_meta($quote_id, 'quote_elements', true);
	$tax_app = get_post_meta($quote_id, 'tax_applicable', true);	
	$tax_rate = get_post_meta($quote_id, 'tax_rate', true);
	$stax_app = get_post_meta($quote_id, 'stax_applicable', true);	
	$stax_rate = get_post_meta($quote_id, 'stax_rate', true);
	$quote_ref = isset($quote_details['quote_ref']) ? $quote_details['quote_ref'] : '';
	$quote_type = isset($quote_details['quote_type']) ? $quote_details['quote_type'] : '';
	$deposit = isset($quote_details['deposit_amount']) ? $quote_details['deposit_amount'] : '';
	$client_id = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
	$client_contact = isset($quote_details['client_contact']) ? $quote_details['client_contact'] : '';
	$contract = isset($quote_details['default_contract_text']) ? $quote_details['default_contract_text'] : '';
	$start_date = isset($quote_details['start_date']) ? $quote_details['start_date'] : '';
	$finish_date = isset($quote_details['finish_date']) ? $quote_details['finish_date'] : '';
	$project_summary = isset($quote_details['quote_summary']) ? $quote_details['quote_summary'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_company = isset($client_details['client_company']) ? $client_details['client_company'] : '';
	$project_title = sprintf(__('%1$s - Project: %2$s', 'cqpim'), $client_company, $quote_ref);
	$new_project = array(
		'post_type' => 'cqpim_project',
		'post_status' => 'private',
		'post_content' => '',
		'post_title' => $project_title
	);
	$project_pid = wp_insert_post( $new_project, true );
	if( ! is_wp_error( $project_pid ) ){
		$project_updated = array(
			'ID' => $project_pid,
			'post_name' => $project_pid,
		);						
		wp_update_post( $project_updated );
		$project_details = array(
			'client_id' => $client_id,
			'quote_ref' => $quote_ref,
			'start_date' => $start_date,
			'finish_date' => $finish_date,
			'pm_name' => $pm_name,
			'deposit_amount' => $deposit,
			'default_contract_text' => $contract,
			'quote_id' => $quote_id,
			'quote_type' => $quote_type,
			'client_contact' => $client_contact,
			'project_summary' => $project_summary,
		);
		$project_progress = array();
		$project_progress[] = array(
			'update' => __('Project created', 'cqpim'),
			'date' => current_time('timestamp'),
			'by' => __('System', 'cqpim')
		);
		update_post_meta($project_pid, 'project_details', $project_details);
		update_post_meta($project_pid, 'tax_applicable', $tax_app);
		update_post_meta($project_pid, 'tax_set', 1);
		update_post_meta($project_pid, 'tax_rate', $tax_rate);	
		update_post_meta($project_pid, 'stax_applicable', $stax_app);
		update_post_meta($project_pid, 'stax_set', 1);
		update_post_meta($project_pid, 'stax_rate', $stax_rate);
		update_post_meta($project_pid, 'project_elements', $quote_milestones);
		update_post_meta($project_pid, 'project_progress', $project_progress);
		$contract = pto_get_contract_status($project_pid);
		update_post_meta($project_pid, 'contract_status', $contract);
		$currency = get_option('currency_symbol');
		$currency_code = get_option('currency_code');
		$currency_position = get_option('currency_symbol_position');
		$currency_space = get_option('currency_symbol_space'); 
		$client_currency = get_post_meta($project_details['client_id'], 'currency_symbol', true);
		$client_currency_code = get_post_meta($project_details['client_id'], 'currency_code', true);
		$client_currency_space = get_post_meta($project_details['client_id'], 'currency_space', true);		
		$client_currency_position = get_post_meta($project_details['client_id'], 'currency_position', true);
		$quote_currency = get_post_meta($project_details['quote_id'], 'currency_symbol', true);
		$quote_currency_code = get_post_meta($project_details['quote_id'], 'currency_code', true);
		$quote_currency_space = get_post_meta($project_details['quote_id'], 'currency_space', true);	
		$quote_currency_position = get_post_meta($project_details['quote_id'], 'currency_position', true);
		if(!empty($quote_currency)) {
			update_post_meta($project_pid, 'currency_symbol', $quote_currency);
		} else {
			if(!empty($client_currency)) {
				update_post_meta($project_pid, 'currency_symbol', $client_currency);
			} else {
				update_post_meta($project_pid, 'currency_symbol', $currency);
			}
		}
		if(!empty($quote_currency_code)) {
			update_post_meta($project_pid, 'currency_code', $quote_currency_code);
		} else {
			if(!empty($client_currency_code)) {
				update_post_meta($project_pid, 'currency_code', $client_currency_code);
			} else {
				update_post_meta($project_pid, 'currency_code', $currency_code);
			}
		}
		if(!empty($quote_currency_space)) {
			update_post_meta($project_pid, 'currency_space', $quote_currency_space);
		} else {
			if(!empty($client_currency_space)) {
				update_post_meta($project_pid, 'currency_space', $client_currency_space);
			} else {
				update_post_meta($project_pid, 'currency_space', $currency_space);
			}
		}
		if(!empty($quote_currency_position)) {
			update_post_meta($project_pid, 'currency_position', $quote_currency_position);
		} else {
			if(!empty($client_currency_position)) {
				update_post_meta($project_pid, 'currency_position', $client_currency_position);
			} else {
				update_post_meta($project_pid, 'currency_position', $currency_position);
			}
		}		
		if(pto_check_addon_status('bugs')) {
			$option = get_option('cqpim_bugs_auto');
			update_post_meta($project_pid, 'bugs_activated', $option);
		}
		$client_contracts = get_post_meta($client_id, 'client_contract', true);
		$auto_contract = get_option('auto_contract');
		$checked = get_option('enable_project_contracts'); 	
		if($auto_contract && $checked == 1 && empty($client_contracts)) {
			pto_process_contract_emails($project_pid);
		}		
		if(empty($checked) || !empty($client_contracts)) {
			$project_details = get_post_meta($project_pid, 'project_details', true);
			$project_details['sent'] = true;
			update_post_meta($project_pid, 'project_details', $project_details);
			$project_elements = get_post_meta($project_pid, 'project_elements', true);
			if(empty($project_elements)) {
				$project_elements = array();
			}
			foreach($project_elements as $element) {
				$args = array(
					'post_type' => 'cqpim_tasks',
					'posts_per_page' => -1,
					'meta_key' => 'milestone_id',
					'meta_value' => $element['id'],
					'orderby' => 'date',
					'order' => 'ASC',
				);
				$tasks = get_posts($args);
				foreach($tasks as $task) {
					update_post_meta($task->ID, 'project_id', $project_pid);
					update_post_meta($task->ID, 'active', true);
					$args = array(
						'post_type' => 'cqpim_tasks',
						'posts_per_page' => -1,
						'meta_key' => 'milestone_id',
						'meta_value' => $element['id'],
						'post_parent' => $task->ID,
						'orderby' => 'date',
						'order' => 'ASC'
					);
					$subtasks = get_posts($args);
					foreach($subtasks as $subtask) {
						update_post_meta($subtask->ID, 'project_id', $project_pid);
						update_post_meta($subtask->ID, 'active', true);						
					}
				}					
			}
			if(!empty($deposit) && $deposit != 'none') {
				pto_create_deposit_invoice($project_pid);
			}
		} else {
			$project_elements = get_post_meta($project_pid, 'project_elements', true);
			if(empty($project_elements)) {
				$project_elements = array();
			}
			foreach($project_elements as $element) {
				$args = array(
					'post_type' => 'cqpim_tasks',
					'posts_per_page' => -1,
					'meta_key' => 'milestone_id',
					'meta_value' => $element['id'],
					'orderby' => 'date',
					'order' => 'ASC',
				);
				$tasks = get_posts($args);
				foreach($tasks as $task) {
					update_post_meta($task->ID, 'project_id', $project_pid);
					$args = array(
						'post_type' => 'cqpim_tasks',
						'posts_per_page' => -1,
						'meta_key' => 'milestone_id',
						'meta_value' => $element['id'],
						'post_parent' => $task->ID,
						'orderby' => 'date',
						'order' => 'ASC'
					);
					$subtasks = get_posts($args);
					foreach($subtasks as $subtask) {
						update_post_meta($subtask->ID, 'project_id', $project_pid);					
					}
				}					
			}			
		}
		return $project_pid;
	} else {
		exit();
	}
}
add_action("wp_ajax_pto_manual_quote_convert", "pto_manual_quote_convert");
function pto_manual_quote_convert() {
	$quote_id = isset($_POST['quote_id']) ? $_POST['quote_id'] : '';
	if(empty($quote_id)) {		
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('The project could not be created. The Quote ID is missing.', 'cqpim') . '</div>'
		);
		header('Content-type: application/json');
		echo json_encode($return);			
		exit;
	} 
	$url = pto_create_project_from_quote($quote_id);
	$url = get_edit_post_link($url);
	$return =  array( 
		'error' 	=> false,
		'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('The project has been created, redirecting now...', 'cqpim') . '</div>',
		'url' => $url,
	);
	header('Content-type: application/json');
	echo json_encode($return);			
	exit;	
}