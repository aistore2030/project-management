<?php
add_action( "wp_ajax_nopriv_pto_frontend_lead_submission", "pto_frontend_lead_submission");
add_action( "wp_ajax_pto_frontend_lead_submission", "pto_frontend_lead_submission");
function pto_frontend_lead_submission() {
	$data = isset($_POST) ? $_POST : '';
	unset($data['action']);
	if(empty($data)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . __('There is missing data, please try again filling in every field.', 'cqpim') . '</span>',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();	
	} else {
		$new_lead = array(
			'post_type' => 'cqpim_lead',
			'post_status' => 'private',
			'post_content' => '',
			'post_title' => __('New Lead', 'cqpim') . ' - ' . date(get_option('cqpim_date_format') . ' H:i', current_time('timestamp')),
		);	
		$lead_pid = wp_insert_post( $new_lead, true );
		if(!is_wp_error($lead_pid)) {
			update_post_meta($lead_pid, 'lead_date', current_time('timestamp'));
			update_post_meta($lead_pid, 'form_type', 'cqpim');
			update_post_meta($lead_pid, 'leadform_id', $data['leadform_id']);
			unset($data['leadform_id']);
			$uploaded_files = array();
			$summary = '';
			$lead_details = array();
			foreach($data as $key => $field) {
				if(is_array($field)) {
					$field = implode(', ', $field);
				}
				$title = str_replace('_', ' ', $key);
				$title = ucwords($title);
				if(strpos($title, 'Cqpimuploader') !== false) {
					$file_object = get_post($field);
					$title = str_replace('Cqpimuploader ', '', $title);
					$lead_details[] = array(
						'type' => 'file',
						'name' => $title,
						'value' => $file_object->post_title,
						'ID' => $file_object->ID
					);
					$attachment_updated = array(
						'ID' => $field,
						'post_parent' => $lead_pid,
					);
					wp_update_post($attachment_updated);
					update_post_meta($field, 'cqpim', true);
				} else {
					$summary .= '<p><strong>' . $title . ': </strong> ' . $field . '</p>';
					$lead_details[] = array(
						'type' => 'field',
						'name' => $title,
						'value' => $field,
						'ID' => ''
					);
				}
			}
			update_post_meta($lead_pid, 'lead_summary', $summary);
			update_post_meta($lead_pid, 'lead_details', $lead_details);
			$mail = pto_send_lead_notification($lead_pid);
			$return =  array( 
				'error' 	=> false,
				'message' 	=> '<span style="display:block; width:96%; padding:2%; color:#fff; background:#8ec165">' . __('Request submitted, we\'ll get back to you soon!', 'cqpim') . '</span>',
			);
			header('Content-type: application/json');
			echo json_encode($return);	
			exit();
		} else {
			$return =  array( 
				'error' 	=> true,
				'message' 	=> '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . __('Unable to create entry, please try again or contact us.', 'cqpim') . '</span>',
			);
			header('Content-type: application/json');
			echo json_encode($return);	
			exit();					
		}
	}
	exit();	
}
add_action( 'gform_after_submission', 'pto_check_gf_submission', 10, 2 );
function pto_check_gf_submission($entry, $form) {
	$args = array(
		'post_type' => 'cqpim_leadform',
		'posts_per_page' => 1,
		'meta_key' => 'gravity_form',
		'meta_value' => $entry['form_id'],
		'post_status' => 'private',
	);
	$leadforms = get_posts($args);
	if(!empty($leadforms)) {
		$leadform = isset($leadforms[0]) ? $leadforms[0] : array();
		if(!empty($leadform->ID)) {
			$new_lead = array(
				'post_type' => 'cqpim_lead',
				'post_status' => 'private',
				'post_content' => '',
				'post_title' => __('New Lead', 'cqpim') . ' - ' . date(get_option('cqpim_date_format') . ' H:i', current_time('timestamp')),
			);	
			$lead_pid = wp_insert_post( $new_lead, true );
			if(!is_wp_error($lead_pid)) {
				update_post_meta($lead_pid, 'lead_date', current_time('timestamp'));
				update_post_meta($lead_pid, 'form_type', 'gf');
				update_post_meta($lead_pid, 'leadform_id', $leadform->ID);
				update_post_meta($lead_pid, 'gf_submission_id', $entry['id']);
				$mail = pto_send_lead_notification($lead_pid);
			}
		} else {
			exit;
		}
	}
}
function pto_send_lead_notification($lead_id) {
	$emails_to_send = array();
	$emails_to_send[] = array(
		'name' => get_option('company_name'),
		'email' => get_option('company_sales_email'),
	);
	$args = array(
		'post_type' => 'cqpim_teams',
		'posts_per_page' => -1,
		'post_status' => 'private',
	);	
	$team_members = get_posts($args);	
	foreach($team_members as $team_member) {		
		$team_details = get_post_meta($team_member->ID, 'team_details', true);		
		$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';		
		if(!empty($user_id)) {			
			$user = get_user_by('id', $user_id);			
			if(user_can($user, 'edit_cqpim_leads')) {
				pto_add_team_notification($team_member->ID, 'system', $lead_id, 'new_lead');
				$emails_to_send[] = array(
					'name' => $user->display_name,
					'email' => $user->user_email,
				);
			}			
		}		
	}
	if(!empty($emails_to_send)) {		
		foreach($emails_to_send as $email) {
			$email_subject = get_option('new_lead_email_subject');
			$email_content = get_option('new_lead_email_content');
			$email_subject = pto_replacement_patterns($email_subject, $lead_id);
			$email_content = pto_replacement_patterns($email_content, $lead_id);	
			$email_content = str_replace('%%TEAM_NAME%%', $email['name'], $email_content);
			$email_content = str_replace('%%LEAD_URL%%', admin_url() . 'post.php?post=' . $lead_id . '&action=edit', $email_content);
			pto_send_emails($email['email'], $email_subject, $email_content, '', '', 'sales');
		}
	}
}