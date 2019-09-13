<?php
add_action( "wp_ajax_nopriv_pto_frontend_register_submission", "pto_frontend_register_submission");
add_action( "wp_ajax_pto_frontend_register_submission", "pto_frontend_register_submission");
function pto_frontend_register_submission() {
	$data = isset($_POST) ? $_POST : '';
	if(empty($data)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . __('There is missing data, please try again filling in every field.', 'cqpim') . '</span>',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();			
	} else {
		unset($data['action']);
		$name = isset($data['name']) ? $data['name'] : '';
		unset($data['name']);
		$company = isset($data['company']) ? $data['company'] : '';
		unset($data['company']);
		$address = isset($data['address']) ? $data['address'] : '';
		unset($data['address']);
		$postcode = isset($data['postcode']) ? $data['postcode'] : '';
		unset($data['postcode']);
		$telephone = isset($data['telephone']) ? $data['telephone'] : '';
		unset($data['telephone']);
		$email = isset($data['email']) ? $data['email'] : '';
		unset($data['email']);
		if ( username_exists( $email ) || email_exists( $email ) ) {
			$return =  array( 
				'error' 	=> true,
				'message' 	=> '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . __('The email address entered is already in our system, please try again with a different email address or contact us.', 'cqpim') . '</span>',
			);
			header('Content-type: application/json');
			echo json_encode($return);	
			exit();				
		} else {
			$new_client = array(
				'post_type' => 'cqpim_client',
				'post_status' => 'private',
				'post_content' => '',
				'post_title' => $company,
			);
			$client_pid = wp_insert_post( $new_client, true );
			if( ! is_wp_error( $client_pid ) ){
				$client_updated = array(
					'ID' => $client_pid,
					'post_name' => $client_pid,
				);						
				wp_update_post( $client_updated );
				$client_details = array(
					'client_ref' => $client_pid,
					'client_company' => $company,
					'client_contact' => $name,
					'client_address' => $address,
					'client_postcode' => $postcode,
					'client_telephone' => $telephone,
					'client_email' => $email,
				);
				update_post_meta($client_pid, 'client_details', $client_details);				
				$require_approval = get_option('pto_creg_approve');
				if($require_approval == 1) {
					update_post_meta($client_pid, 'pending', 1);
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
						if(!empty($user_obj) && user_can($user_obj, 'edit_cqpim_clients')) {
							pto_add_team_notification($member->ID, 'system', $client_pid, 'creg_auth');
						}
					}
				} else {
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
						if(!empty($user_obj) && user_can($user_obj, 'edit_cqpim_clients')) {
							pto_add_team_notification($member->ID, 'system', $client_pid, 'creg_noauth');
						}
					}
					$passw = pto_random_string(10);
					$login = $email;
					$user_id = wp_create_user( $login, $passw, $email );
					$user = new WP_User( $user_id );
					$user->set_role( 'cqpim_client' );
					$client_details = get_post_meta($client_pid, 'client_details', true);
					$client_details['user_id'] = $user_id;
					update_post_meta($client_pid, 'client_details', $client_details);
					$client_ids = array();
					$client_ids[] = $user_id;				
					update_post_meta($client_pid, 'client_ids', $client_ids);
					$user_data = array(
						'ID' => $user_id,
						'display_name' => $name,
						'first_name' => $name,
					);
					wp_update_user($user_data);	
					$form_auto_welcome = get_option('form_reg_auto_welcome');
					if($form_auto_welcome == 1) {
						send_pto_welcome_email($client_pid, $passw);
					}	
				}
				$return =  array( 
					'error' 	=> false,
					'message' 	=> '<span style="display:block; width:96%; padding:2%; color:#fff; background:#8ec165">' . __('Account created, please check your email for your password.', 'cqpim') . '</span>',
				);
				header('Content-type: application/json');
				echo json_encode($return);	
				exit();						
			} else {
				$return =  array( 
					'error' 	=> true,
					'message' 	=> '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . __('Unable to create client entry, please try again or contact us.', 'cqpim') . '</span>',
				);
				header('Content-type: application/json');
				echo json_encode($return);	
				exit();	
			}				
		}
	}
	exit();	
}
add_action( "wp_ajax_nopriv_pto_frontend_quote_submission", "pto_frontend_quote_submission");
add_action( "wp_ajax_pto_frontend_quote_submission", "pto_frontend_quote_submission");
function pto_frontend_quote_submission() {
	$data = isset($_POST) ? $_POST : '';
	if(empty($data)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . __('There is missing data, please try again filling in every field.', 'cqpim') . '</span>',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();			
	} else {
		unset($data['action']);
		$name = isset($data['name']) ? $data['name'] : '';
		unset($data['name']);
		$company = isset($data['company']) ? $data['company'] : '';
		unset($data['company']);
		$address = isset($data['address']) ? $data['address'] : '';
		unset($data['address']);
		$postcode = isset($data['postcode']) ? $data['postcode'] : '';
		unset($data['postcode']);
		$telephone = isset($data['telephone']) ? $data['telephone'] : '';
		unset($data['telephone']);
		$email = isset($data['email']) ? $data['email'] : '';
		unset($data['email']);
		if ( username_exists( $email ) || email_exists( $email ) ) {
			$return =  array( 
				'error' 	=> true,
				'message' 	=> '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . __('The email address entered is already in our system, please try again with a different email address or contact us.', 'cqpim') . '</span>',
			);
			header('Content-type: application/json');
			echo json_encode($return);	
			exit();				
		} else {
			$new_client = array(
				'post_type' => 'cqpim_client',
				'post_status' => 'private',
				'post_content' => '',
				'post_title' => $company,
			);
			$client_pid = wp_insert_post( $new_client, true );
			if( ! is_wp_error( $client_pid ) ){
				$client_updated = array(
					'ID' => $client_pid,
					'post_name' => $client_pid,
				);						
				wp_update_post( $client_updated );
				$client_details = array(
					'client_ref' => $client_pid,
					'client_company' => $company,
					'client_contact' => $name,
					'client_address' => $address,
					'client_postcode' => $postcode,
					'client_telephone' => $telephone,
					'client_email' => $email,
				);
				update_post_meta($client_pid, 'client_details', $client_details);
				$require_approval = get_option('pto_cquo_approve');
				if($require_approval == 1) {
					update_post_meta($client_pid, 'pending', 1);
				} else {
					$passw = pto_random_string(10);
					$login = $email;
					$user_id = wp_create_user( $login, $passw, $email );
					$user = new WP_User( $user_id );
					$user->set_role( 'cqpim_client' );
					$client_details = get_post_meta($client_pid, 'client_details', true);
					$client_details['user_id'] = $user_id;
					update_post_meta($client_pid, 'client_details', $client_details);
					$client_ids = array();
					$client_ids[] = $user_id;
					update_post_meta($client_pid, 'client_ids', $client_ids);
					$user_data = array(
						'ID' => $user_id,
						'display_name' => $name,
						'first_name' => $name,
					);
					wp_update_user($user_data);	
					$form_auto_welcome = get_option('form_auto_welcome');
					if($form_auto_welcome == 1) {
						send_pto_welcome_email($client_pid, $passw);
					}
				}
				$new_quote = array(
					'post_type' => 'cqpim_quote',
					'post_status' => 'private',
					'post_content' => '',
					'post_title' => '',
				);
				$quote_pid = wp_insert_post( $new_quote, true );
				if( ! is_wp_error( $quote_pid ) ){					
					$title = $company . ' - ' . __('Quote', 'cqpim') . ': ' . $quote_pid;
					$quote_updated = array(
						'ID' => $quote_pid,
						'post_title' => $title,
						'post_name' => $quote_pid,
					);						
					wp_update_post( $quote_updated );
					$uploaded_files = array();
					$summary = '';
					foreach($data as $key => $field) {
						if(is_array($field)) {
							$field = implode(', ', $field);
						}
						$title = str_replace('_', ' ', $key);
						$title = ucwords($title);
						if(strpos($title, 'Cqpimuploader') !== false) {
							$file_object = get_post($field);
							$title = str_replace('Cqpimuploader ', '', $title);
							$summary .= '<p><strong>' . $title . ': </strong> ' . $file_object->post_title . '</p>';
							$attachment_updated = array(
								'ID' => $field,
								'post_parent' => $quote_pid,
							);
							wp_update_post($attachment_updated);
							update_post_meta($field, 'cqpim', true);
						} else {
							$summary .= '<p><strong>' . $title . ': </strong> ' . $field . '</p>';
						}
					}
					$header = get_option( 'quote_header' );
					$header = str_replace('%%CLIENT_NAME%%', $name, $header);
					$footer = get_option( 'quote_footer' );
					$footer = str_replace('%%CURRENT_USER%%', '', $footer);
					$currency = get_option('currency_symbol');
					$currency_code = get_option('currency_code');
					$currency_position = get_option('currency_symbol_position');
					$currency_space = get_option('currency_symbol_space'); 
					update_post_meta($quote_pid, 'currency_symbol', $currency);
					update_post_meta($quote_pid, 'currency_code', $currency_code);
					update_post_meta($quote_pid, 'currency_position', $currency_position);
					update_post_meta($quote_pid, 'currency_space', $currency_space);
					$quote_details = array(
						'quote_type' => 'quote',
						'quote_ref' => $quote_pid,
						'client_id' => $client_pid,
						'quote_summary' => $summary,
						'quote_header' => $header,
						'quote_footer' => $footer,
						'client_contact' => $user_id
					);
					update_post_meta($quote_pid, 'quote_details', $quote_details);
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
							pto_add_team_notification($member->ID, $user_id, $quote_pid, 'new_quote');
						}
					}
					$to = get_option('company_sales_email');
					$attachments = array();
					$subject = get_option('new_quote_subject');
					$content = get_option('new_quote_email');
					$name_tag = '%%NAME%%';
					$link_tag = '%%QUOTE_URL%%';
					$company_tag = '%%COMPANY_NAME%%';
					$quote_link = admin_url() . 'post.php?post=' . $quote_pid . '&action=edit';
					$subject = str_replace($name_tag, $name, $subject);
					$content = str_replace($name_tag, $name, $content);
					$content = str_replace($link_tag, $quote_link, $content);
					$content = str_replace($company_tag, get_option('company_name'), $content);
					pto_send_emails( $to, $subject, $content, '', $attachments, 'sales' );
					$return =  array( 
						'error' 	=> false,
						'message' 	=> '<span style="display:block; width:96%; padding:2%; color:#fff; background:#8ec165">' . __('Quote request submitted, we\'ll get back to you soon!', 'cqpim') . '</span>',
					);
					header('Content-type: application/json');
					echo json_encode($return);	
					exit();						
				} else {
					$return =  array( 
						'error' 	=> true,
						'message' 	=> '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . __('Unable to create quote, please try again or contact us.', 'cqpim') . '</span>',
					);
					header('Content-type: application/json');
					echo json_encode($return);	
					exit();						
				}
			} else {
				$return =  array( 
					'error' 	=> true,
					'message' 	=> '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . __('Unable to create client entry, please try again or contact us.', 'cqpim') . '</span>',
				);
				header('Content-type: application/json');
				echo json_encode($return);	
				exit();	
			}				
		}
	}
	exit();	
}
add_action( "wp_ajax_nopriv_pto_backend_quote_submission", "pto_backend_quote_submission");
add_action( "wp_ajax_pto_backend_quote_submission", "pto_backend_quote_submission");
function pto_backend_quote_submission() {
	$data = isset($_POST) ? $_POST : '';
	if(empty($data)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('There is missing data, please try again filling in every field.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();			
	} else {
		unset($data['action']);
		$client = isset($data['client']) ? $data['client'] : '';
		unset($data['client']);
		$client_details = get_post_meta($client, 'client_details', true);
		$name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
		$company = isset($client_details['client_company']) ? $client_details['client_company'] : '';
		if ( empty($client) ) {
			$return =  array( 
				'error' 	=> true,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('Missing Client ID, please try again.', 'cqpim') . '</div>',
			);
			header('Content-type: application/json');
			echo json_encode($return);	
			exit();				
		} else {	
			$new_quote = array(
				'post_type' => 'cqpim_quote',
				'post_status' => 'private',
				'post_content' => '',
				'post_title' => '',
			);
			$quote_pid = wp_insert_post( $new_quote, true );
			if( ! is_wp_error( $quote_pid ) ){					
				$title = $company . ' - ' . __('Quote', 'cqpim') . ': ' . $quote_pid;
				$quote_updated = array(
					'ID' => $quote_pid,
					'post_title' => $title,
					'post_name' => $quote_pid,
				);						
				wp_update_post( $quote_updated );
				$summary = '';
				foreach($data as $key => $field) {
					if(is_array($field)) {
						$field = implode(', ', $field);
					}
					$title = str_replace('_', ' ', $key);
					$title = ucwords($title);
					if(strpos($title, 'Cqpimuploader') !== false) {
						$file_object = get_post($field);
						$title = str_replace('Cqpimuploader ', '', $title);
						$summary .= '<p><strong>' . $title . ': </strong> ' . $file_object->post_title . '</p>';
						$attachment_updated = array(
							'ID' => $field,
							'post_parent' => $quote_pid,
						);
						wp_update_post($attachment_updated);
						update_post_meta($field, 'cqpim', true);
					} else {
						$summary .= '<p><strong>' . $title . ': </strong> ' . $field . '</p>';
					}
				}
				$user = wp_get_current_user();
				$header = get_option( 'quote_header' );
				$header = str_replace('%%CLIENT_NAME%%', $user->display_name, $header);
				$footer = get_option( 'quote_footer' );
				$footer = str_replace('%%CURRENT_USER%%', '', $footer);
				$currency = get_option('currency_symbol');
				$currency_code = get_option('currency_code');
				$currency_position = get_option('currency_symbol_position');
				$currency_space = get_option('currency_symbol_space'); 
				update_post_meta($quote_pid, 'currency_symbol', $currency);
				update_post_meta($quote_pid, 'currency_code', $currency_code);
				update_post_meta($quote_pid, 'currency_position', $currency_position);
				update_post_meta($quote_pid, 'currency_space', $currency_space);
				$quote_details = array(
					'quote_type' => 'quote',
					'quote_ref' => $quote_pid,
					'client_id' => $client,
					'quote_summary' => $summary,
					'quote_header' => $header,
					'quote_footer' => $footer,
					'client_contact' => $user->ID
				);
				update_post_meta($quote_pid, 'quote_details', $quote_details);
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
						pto_add_team_notification($member->ID, $user->ID, $quote_pid, 'new_quote');
					}
				}
				$to = get_option('company_sales_email');
				$attachments = array();
				$subject = get_option('new_quote_subject');
				$content = get_option('new_quote_email');
				$name_tag = '%%NAME%%';
				$link_tag = '%%QUOTE_URL%%';
				$company_tag = '%%COMPANY_NAME%%';
				$quote_link = admin_url() . 'post.php?post=' . $quote_pid . '&action=edit';
				$subject = str_replace($name_tag, $user->display_name, $subject);
				$content = str_replace($name_tag, $user->display_name, $content);
				$content = str_replace($link_tag, $quote_link, $content);
				$content = str_replace($company_tag, $sender_name, $content);
				pto_send_emails( $to, $subject, $content, '', $attachments, 'sales' );
				$return =  array( 
					'error' 	=> false,
					'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Quote request submitted, we\'ll get back to you soon!', 'cqpim') . '</div>',
				);
				header('Content-type: application/json');
				echo json_encode($return);	
				exit();						
			} else {
				$return =  array( 
					'error' 	=> true,
					'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('Unable to create quote, please try again or contact us.', 'cqpim') . '</div>',
				);
				header('Content-type: application/json');
				echo json_encode($return);	
				exit();						
			}				
		}
	}
	exit();	
}