<?php
function pto_get_client_from_userid($user = NULL) {
	if(empty($user)) {
		$user = wp_get_current_user();
	}
	$args = array(
		'post_type' => 'cqpim_client',
		'posts_per_page' => -1,
		'post_status' => 'private'
	);
	$members = get_posts($args);
	foreach($members as $member) {
		$team_details = get_post_meta($member->ID, 'client_details', true);
		if(!empty($team_details['user_id']) &&  $team_details['user_id'] == $user->ID) {
			$assigned = $member->ID;
			$client_type = 'admin';
		}
	} 
	if(empty($assigned)) {
		foreach($members as $member) {
			$team_ids = get_post_meta($member->ID, 'client_ids', true);
			if(!is_array($team_ids)) {
				$team_ids = array($team_ids);
			}
			if(in_array($user->ID, $team_ids)) {
				$assigned = $member->ID;
				$client_type = 'contact';
			}
		} 			
	}
	if(!empty($assigned)) {
		return array(
			'assigned' => $assigned,
			'type' => $client_type,
		);
	}
	return false;
}
add_action( 'profile_update', 'pto_client_user_update_profile', 10, 2 );
function pto_client_user_update_profile( $user_id, $old_user_data ) {
	$user = get_userdata( $user_id );
	$args = array(
		'post_type' => 'cqpim_client',
		'posts_per_page' => -1,
		'post_status' => 'private',
	);
	$clients = get_posts($args);
	foreach($clients as $client) {
		$team_details = get_post_meta($client->ID, 'client_details', true);
		if($team_details['user_id'] == $user_id) {
			$team_details['client_contact'] = $user->display_name;
			$team_details['client_email'] = $user->user_email;
			update_post_meta($client->ID, 'client_details', $team_details);
		}
	}
}
function send_pto_welcome_email($client_id, $password) {
	$client_details = get_post_meta($client_id, 'client_details', true);
	$email_subject = get_option('auto_welcome_subject');
	$email_content = get_option('auto_welcome_content');
	$email_subject = pto_replacement_patterns($email_subject, $client_id, '');
	$email_content = pto_replacement_patterns($email_content, $client_id, '');
	$email_content = str_replace('%%CLIENT_PASSWORD%%', $password, $email_content);
	$to = $client_details['client_email'];
	$attachments = array();
	pto_send_emails( $to, $email_subject, $email_content, '', $attachments, 'sales' );
}
function pto_create_client_from_user($actions, $user_object) {
	$user = get_user_by('id', $user_object->ID);
	$roles = $user->roles;
	$role = isset($roles[0]) ? $roles[0] : '';
	if(strpos($role, 'cqpim') !== false) {
		$cqpim_role = 1;
	}
	if(!in_array('administrator', $roles) && !in_array('ptouploader', $roles) && empty($cqpim_role)) {
		$actions['add_cqpim_client'] = "<a class='create_client' href='" . admin_url( "users.php?action=create_cqpim_client&amp;user=$user_object->ID") . "'>" . __( 'Convert to PTO Client', 'cqpim' ) . "</a>";
	}
	return $actions;
}
add_filter('user_row_actions', 'pto_create_client_from_user', 10, 2);
add_action('current_screen', 'pto_create_client_from_user_callback');
function pto_create_client_from_user_callback() {
	$screen = get_current_screen();
	$base = isset($screen->base) ? $screen->base : '';
	$action = isset($_GET['action']) ? $_GET['action'] : '';
	if($base == 'users' && $action == 'create_cqpim_client') {
		$user_id = isset($_GET['user']) ? $_GET['user'] : '';
		$user = get_user_by('id', $user_id);
		$new_client = array(
			'post_type' => 'cqpim_client',
			'post_status' => 'private',
			'post_content' => '',
			'post_title' => $user->display_name,
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
				'client_contact' => $user->display_name,
				'client_company' => $user->display_name,
				'client_email' => $user->user_email,
			);
			$client_details['user_id'] = $user_id;
			$client_ids = array();
			$client_ids[] = $user_id;
			update_post_meta($client_pid, 'client_details', $client_details);
			update_post_meta($client_pid, 'client_ids', $client_ids);	
			$user_data = array(
				'ID' => $user_id,
				'role' => 'cqpim_client'
			);
			wp_update_user($user_data);	
		}
	}
}
add_filter( 'post_row_actions', 'pto_edit_client_trash_action', 10, 1 );
function pto_edit_client_trash_action( $actions ) {
	if( get_post_type() === 'cqpim_client'  && current_user_can('delete_cqpim_clients')) {
		global $post;
		$actions['trash'] = '<a class="delete_client" data-id="' . $post->ID . '" href="#">' . __('Delete Client' , 'cqpim') . '</a>'; ?>
			<div id="delete_client_warning_container_<?php echo $post->ID; ?>" style="display:none">
				<div id="delete_client_warning_<?php echo $post->ID; ?>" class="contact_edit">
					<div style="padding:12px">
						<h3><?php _e('Delete Client Warning', 'cqpim'); ?></h3>
						<p><?php _e('Deleting this client will also delete the associated user account and the user account of all related contacts. <br /><br />If this is not desired then you should first unlink the user account from the client.', 'cqpim'); ?></p>						
						<br />
						<button class="cqpim_button bg-blue font-white rounded_2 left op uldc" data-id="<?php echo $post->ID; ?>"><?php _e('Unlink User and Delete Client', 'cqpim'); ?></button><button class="cqpim_button bg-blue font-white rounded_2 right op dcu" data-id="<?php echo $post->ID; ?>"><?php _e('Delete Client and User', 'cqpim'); ?></button>
						<div class="clear"></div>
						<br />
						<div id="client_messages_<?php echo $post->ID; ?>"></div>
						<div id="client_spinner_<?php echo $post->ID; ?>" class="ajax_spinner" style="display:none"></div>
					</div>
				</div>
			</div>
		<?php
	}
	return $actions;
}
add_action( "wp_ajax_pto_unlink_delete_client", "pto_unlink_delete_client");
function pto_unlink_delete_client() {
	$client_id = isset($_POST['id']) ? $_POST['id'] : '';
	if(empty($client_id)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<span class="task_over">' . __('The Client ID is missing. The client could not be deleted', 'cqpim') . '</span>'
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();
	} else {
		$client_details = get_post_meta($client_id, 'client_details', true);
		unset($client_details['user_id']);
		update_post_meta($client_id, 'client_details', $client_details);
		wp_delete_post($client_id, true);
		$return =  array( 
			'error' 	=> false,
			'message' 	=> '<span class="task_complete">' . __('The user was successfully unlinked and the client was deleted', 'cqpim') . '</span>'
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();
	}
}
add_action( "wp_ajax_pto_delete_client_user_confirm", "pto_delete_client_user_confirm");
function pto_delete_client_user_confirm() {
	$client_id = isset($_POST['id']) ? $_POST['id'] : '';
	if(empty($client_id)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<span class="task_over">' . __('The Client ID is missing. The client could not be deleted', 'cqpim') . '</span>'
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();
	} else {
		$client_details = get_post_meta($client_id, 'client_details', true);
		$user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
		$client_contacts = get_post_meta($client_id, 'client_contacts', true);
		wp_delete_user($user_id);
		foreach($client_contacts as $key => $contact) {
			wp_delete_user($key);
		}
		wp_delete_post($client_id, true);
		$return =  array( 
			'error' 	=> false,
			'message' 	=> '<span class="task_complete">' . __('The user and the client was deleted successfully', 'cqpim') . '</span>'
		);
		header('Content-type: application/json');
		echo json_encode($return);			
		exit();	
	}
}
add_action( "wp_ajax_nopriv_pto_client_add_contact", "pto_client_add_contact");
add_action( "wp_ajax_pto_client_add_contact", "pto_client_add_contact");
function pto_client_add_contact() {
	$data = isset($_POST) ? $_POST : '';
	$client_id = isset($data['entity_id']) ? $data['entity_id'] : '';
	$contact_name = isset($data['contact_name']) ? $data['contact_name'] : '';
	$contact_telephone = isset($data['contact_telephone']) ? $data['contact_telephone'] : '';
	$contact_email = isset($data['contact_email']) ? $data['contact_email'] : '';
	$send = isset($data['send']) ? $data['send'] : '';
	if(empty($client_id) || empty($contact_name) || empty($contact_telephone) || empty($contact_email)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('Please complete all fields.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();			
	} else {
		$email = email_exists($contact_email);
		$username = username_exists($contact_email);
		if(!empty($email)  || !empty($username)) {
			$return =  array( 
				'error' 	=> true,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('The email address entered is already in the system, please try another.', 'cqpim') . '</div>',
			);
			header('Content-type: application/json');
			echo json_encode($return);
			exit();				
		} else {
			$password = pto_random_string(10);
			$user_id = wp_create_user( $contact_email, $password, $contact_email );
			$user = new WP_User( $user_id );
			$user->set_role( 'cqpim_client' );
			$user_data = array(
				'ID' => $user_id,
				'display_name' => $contact_name,
				'first_name' => $contact_name,
			);
			wp_update_user($user_data);
			$contacts = get_post_meta($client_id, 'client_contacts', true);
			$contacts = $contacts&&is_array($contacts)?$contacts:array();
			$contacts[$user->ID] = array(
				'user_id' => $user->ID,
				'name' => $contact_name,
				'email' => $contact_email,
				'telephone' => $contact_telephone
			);
			update_post_meta($client_id, 'client_contacts', $contacts);
			$ids = get_post_meta($client_id, 'client_ids', true);
			$ids = $ids&&is_array($ids)?$ids:array();
			$ids[] = $user_id;
			update_post_meta($client_id, 'client_ids', $ids);
			if($send == 1) {							
				$email_subject = get_option('added_contact_subject');
				$email_content = get_option('added_contact_content');
				$email_subject = pto_replacement_patterns($email_subject, $client_id, '');
				$email_content = pto_replacement_patterns($email_content, $client_id, '');
				$email_content = str_replace('%%CONTACT_NAME%%', $contact_name, $email_content);
				$email_content = str_replace('%%CONTACT_EMAIL%%', $contact_email, $email_content);
				$email_content = str_replace('%%CONTACT_PASSWORD%%', $password, $email_content);
				$to = $contact_email;
				$attachments = array();
				if(pto_send_emails( $to, $email_subject, $email_content, '', $attachments, 'sales' )) {
					$return =  array( 
						'error' 	=> false,
						'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Contact has been created. An email has been sent to the contact.', 'cqpim') . '</div>',
					);
					header('Content-type: application/json');
					echo json_encode($return);	
					exit();	
				} else {
					$return =  array( 
						'error' 	=> true,
						'message' 	=> '<div class="cqpim-alert cqpim-alert-warning alert-display">' . __('The contact was added but the email failed to send. Check you have completed the email subject and content fields in the plugin settings.', 'cqpim') . '</div>',
					);
					header('Content-type: application/json');
					echo json_encode($return);
					exit();						
				}
			} else {
				$return =  array( 
					'error' 	=> false,
					'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Contact has been created. No email has been sent.', 'cqpim') . '</div>',
				);
				header('Content-type: application/json');
				echo json_encode($return);	
				exit();	
			}
		}
	}
}
add_action( "wp_ajax_nopriv_pto_remove_client_contact", "pto_remove_client_contact");
add_action( "wp_ajax_pto_remove_client_contact", "pto_remove_client_contact");
function pto_remove_client_contact() {
	$key = isset($_POST['key']) ? $_POST['key'] : '';
	$client_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	$user_id = $client_contacts[$key]['user_id'];
	wp_delete_user($user_id);
	if(!empty($client_contacts[$key])) {
		unset($client_contacts[$key]);
	}
	$client_contacts = array_filter($client_contacts);
	update_post_meta($client_id, 'client_contacts', $client_contacts);
	$client_ids = get_post_meta($client_id, 'client_ids', true);
	if(!is_array($client_ids)) {
		$client_ids = array($client_ids);
	}
	foreach($client_ids as $key => $client_id_ind) {
		if($client_id_ind == $user_id) {
			unset($client_ids[$key]);
		}
	}
	update_post_meta($client_id, 'client_ids', $client_ids);
	$return =  array( 
		'error' 	=> false,
		'message' 	=> '',
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();	
}
add_action( "wp_ajax_nopriv_pto_edit_client_contact", "pto_edit_client_contact");
add_action( "wp_ajax_pto_edit_client_contact", "pto_edit_client_contact");
function pto_edit_client_contact() {
	$data = isset($_POST) ? $_POST : '';
	$client_id = isset($data['project_id']) ? $data['project_id'] : '';
	$key = isset($data['key']) ? $data['key'] : '';
	$admin = isset($_POST['admin']) ? $_POST['admin'] : '';
	$contact_name = isset($data['name']) ? $data['name'] : '';
	$contact_telephone = isset($data['phone']) ? $data['phone'] : '';
	$contact_email = isset($data['email']) ? $data['email'] : '';
	$password = isset($data['password']) ? $data['password'] : '';
	$password2 = isset($data['password2']) ? $data['password2'] : '';
	$send = isset($data['send']) ? $data['send'] : '';
	$no_tasks = isset($data['no_tasks']) ? $data['no_tasks']: 0;
	$no_tasks_comment = isset($data['no_tasks_comment']) ? $data['no_tasks_comment']: 0;
	$no_tickets = isset($data['no_tickets']) ? $data['no_tickets']: 0;
	$no_tickets_comment = isset($data['no_tickets_comment']) ? $data['no_tickets_comment']: 0;
	$no_bugs = isset($data['no_bugs']) ? $data['no_bugs']: 0;
	$no_bugs_comment = isset($data['no_bugs_comment']) ? $data['no_bugs_comment']: 0;
	if(empty($contact_name) || empty($contact_telephone) || empty($contact_email)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('Missing Data. Please ensure you complete all fields.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();			
	} else {
		$contacts = get_post_meta($client_id, 'client_contacts', true);
		$user_id = $contacts[$key]['user_id'];
		$email = email_exists($contact_email);
		$username = username_exists($contact_email);
		if( !empty($email) && $email != $user_id  || !empty($username) && $username != $user_id ) {
			$return =  array( 
				'error' 	=> true,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('The email address entered is already in the system, please try another.', 'cqpim') . '</div>',
			);
			header('Content-type: application/json');
			echo json_encode($return);
			exit();				
		} else {
			if(!empty($password) || !empty($password2)) {
				if($password != $password2) {
					$return =  array( 
						'error' 	=> true,
						'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('The passwords do not match.', 'cqpim') . '</div>',
					);
					header('Content-type: application/json');
					echo json_encode($return);
					exit();				
				} else {
					wp_set_password($password2, $user_id);
					if(empty($admin)) {
						wp_set_auth_cookie( $user_id, '', '' );
					}
					if($send == 1) {
						$email_subject = get_option('password_reset_subject');
						$email_content = get_option('password_reset_content');
						$email_content = str_replace('%%CLIENT_NAME%%', $contact_name, $email_content);
						$email_content = str_replace('%%CLIENT_EMAIL%%', $contact_email, $email_content);
						$email_subject = pto_replacement_patterns($email_subject, $entity_id, '');
						$email_content = pto_replacement_patterns($email_content, $entity_id, '');
						$to = $contact_email;
						$email_content = str_replace('%%NEW_PASSWORD%%', $password2, $email_content);
						$attachments = array();
						pto_send_emails($to, $email_subject, $email_content, '', $attachments, 'sales');						
					}
				}
			}
			$contacts[$key] = array(
				'user_id' => $user_id,
				'name' => $contact_name,
				'email' => $contact_email,
				'telephone' => $contact_telephone			
			);
			$contacts[$key]['notifications'] = array(
				'no_tasks' => $no_tasks,
				'no_tasks_comment' => $no_tasks_comment,
				'no_tickets' => $no_tickets,
				'no_tickets_comment' => $no_tickets_comment,
				'no_bugs' => $no_bugs,
				'no_bugs_comment' => $no_bugs_comment,
			);	
			update_post_meta($client_id, 'client_contacts', $contacts);
			$user_details = array(
				'ID' => $user_id,
				'display_name' => $contact_name,
				'first_name' => $contact_name,
				'user_email' => $contact_email,				
			);
			wp_update_user($user_details);
			$return =  array( 
				'error' 	=> false,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Update Successful.', 'cqpim') . '</div>',
			);
			header('Content-type: application/json');
			echo json_encode($return);	
			exit();	
		}
	}
}
add_action( "wp_ajax_nopriv_pto_client_update_details", "pto_client_update_details");
add_action( "wp_ajax_pto_client_update_details", "pto_client_update_details");
function pto_client_update_details() {
	$data = isset($_POST) ? $_POST : '';
	$user_id = isset($data['user_id']) ? $data['user_id']: '';
	$client_object = isset($data['client_object']) ? $data['client_object']: '';
	$client_type = isset($data['client_type']) ? $data['client_type']: '';
	$client_email = isset($data['client_email']) ? $data['client_email']: '';
	$client_phone = isset($data['client_phone']) ? $data['client_phone']: '';
	$client_name = isset($data['client_name']) ? $data['client_name']: '';
	$company_name = isset($data['company_name']) ? $data['company_name']: '';
	$company_address = isset($data['company_address']) ? $data['company_address']: '';
	$company_postcode = isset($data['company_postcode']) ? $data['company_postcode']: '';
	$client_pass = isset($data['client_pass']) ? $data['client_pass']: '';
	$client_pass_rep = isset($data['client_pass_rep']) ? $data['client_pass_rep']: '';
	$photo = isset($data['photo']) ? $data['photo']: '';
	$no_tasks = isset($data['no_tasks']) ? $data['no_tasks']: 0;
	$no_tasks_comment = isset($data['no_tasks_comment']) ? $data['no_tasks_comment']: 0;
	$no_tickets = isset($data['no_tickets']) ? $data['no_tickets']: 0;
	$no_tickets_comment = isset($data['no_tickets_comment']) ? $data['no_tickets_comment']: 0;
	$no_bugs = isset($data['no_bugs']) ? $data['no_bugs']: 0;
	$no_bugs_comment = isset($data['no_bugs_comment']) ? $data['no_bugs_comment']: 0;
	$custom_fields = get_option('cqpim_custom_fields_client');	
	$custom_fields = str_replace('\"', '"', $custom_fields);
	$custom_fields = json_decode($custom_fields);
	$custom = isset($data['custom']) ? $data['custom'] : array();
	foreach($custom_fields as $custom_field) {
		if(empty($custom[$custom_field->name]) && !empty($custom_field->required)) {
			$return =  array( 
				'error' 	=> true,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('Please complete all required fields', 'cqpim') . '</div>',
				'custom_field' => $custom_field,
				'custom' => $custom,
			);
			header('Content-type: application/json');
			echo json_encode($return);	
			exit();						
		}
	}
	update_post_meta($client_object, 'custom_fields', $custom);
	if(empty($user_id) || empty($client_object) || empty($client_type)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('There is some missing data. The update has failed.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();		
	} else {
		$email = email_exists($client_email);
		$username = username_exists($client_email);
		if( !empty($email) && $email != $user_id  || !empty($username) && $username != $user_id ) {
			$return =  array( 
				'error' 	=> true,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('The email address entered is already in the system, please try another.', 'cqpim') . '</div>',
			);
			header('Content-type: application/json');
			echo json_encode($return);
			exit();			
		} else {
			if(!empty($client_pass) || !empty($client_pass_rep)) {
				if($client_pass != $client_pass_rep) {
					$return =  array( 
						'error' 	=> true,
						'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('The Passwords do not match. Please try again.', 'cqpim') . '</div>',
					);
					header('Content-type: application/json');
					echo json_encode($return);
					exit();						
				} else {
					$user = wp_get_current_user();
					if($user->ID != $user_id) {
						$return =  array( 
							'error' 	=> true,
							'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('Cheatin\' uh? Better luck next time.', 'cqpim') . '</div>',
						);
						header('Content-type: application/json');
						echo json_encode($return);
						exit();							
					} else {
						wp_set_password( $client_pass, $user_id );
						wp_set_auth_cookie( $user_id, '', '' );
					}
				}
			}
			$user_data = array(
				'ID' => $user_id,
				'display_name' => $client_name,
				'first_name' => $client_name,
				'user_email' => $client_email,
			);
			wp_update_user($user_data);	
			$client_notifications = array(
				'no_tasks' => $no_tasks,
				'no_tasks_comment' => $no_tasks_comment,
				'no_tickets' => $no_tickets,
				'no_tickets_comment' => $no_tickets_comment,
				'no_bugs' => $no_bugs,
				'no_bugs_comment' => $no_bugs_comment,
			);	
			if($client_type == 'admin') {
				$client_details = get_post_meta($client_object, 'client_details', true);
				$client_details['client_contact'] = $client_name;
				$client_details['client_telephone'] = $client_phone;
				$client_details['client_email'] = $client_email;
				$client_details['client_company'] = $company_name;
				$client_details['client_address'] = $company_address;
				$client_details['client_postcode'] = $company_postcode;
				update_post_meta($client_object, 'client_details', $client_details);
				$client_updated = array(
					'ID' => $client_object,
					'post_title' => $company_name,
				);
				wp_update_post($client_updated);				
				update_post_meta($client_object, 'client_notifications', $client_notifications);
				if(!empty($photo)) {
					update_post_meta($client_object, 'team_avatar', $photo);
				}
			} else {
				$user = wp_get_current_user();
				$client_contacts = get_post_meta($client_object, 'client_contacts', true);
				$client_contacts[$user->ID]['telephone'] = $client_phone;
				$client_contacts[$user->ID]['name'] = $client_name;
				$client_contacts[$user->ID]['email'] = $client_email;
				$client_contacts[$user->ID]['notifications'] = $client_notifications;
				if(!empty($photo)) {
					$client_contacts[$user->ID]['team_avatar'] = $photo;
				}
				update_post_meta($client_object, 'client_contacts', $client_contacts);
			}
			$return =  array( 
				'error' 	=> false,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Details successfully updated.', 'cqpim') . '</div>',
			);
			header('Content-type: application/json');
			echo json_encode($return);
			exit();	
		}
	}			
}
add_action( "wp_ajax_pto_client_add_alert", "pto_client_add_alert");
function pto_client_add_alert() {
	$post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
	$level = isset($_POST['alert_level']) ? $_POST['alert_level'] : '';
	$message = isset($_POST['alert_message']) ? $_POST['alert_message'] : '';
	$global = isset($_POST['global']) ? $_POST['global'] : '';
	if(empty($level) || empty($message)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-warning alert-display">' . __('You must choose a level and add a message.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();		
	}
	if(empty($global)) {
		$custom_alerts = get_post_meta($post_id, 'custom_alerts', true);
		if(empty($custom_alerts)) {
			$custom_alerts = array();
		}
		$custom_alerts[] = array(
			'level' => $level,
			'message' => $message,
			'seen' => '',
			'cleared' => '',
			'global' => 0,
		);
		update_post_meta($post_id, 'custom_alerts', $custom_alerts);
	} else {
		$args = array(
			'post_type' => 'cqpim_client',
			'posts_per_page' => -1,
			'post_status' => 'private'
		);
		$clients = get_posts($args);
		$digits = 5;
		$gid = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
		foreach($clients as $client) {
			$custom_alerts = get_post_meta($client->ID, 'custom_alerts', true);
			if(empty($custom_alerts)) {
				$custom_alerts = array();
			}
			$custom_alerts['G-' . $gid] = array(
				'level' => $level,
				'message' => $message,
				'seen' => '',
				'cleared' => '',
				'global' => 1,
			);
			update_post_meta($client->ID, 'custom_alerts', $custom_alerts);		
		}		
	}
	$return =  array( 
		'error' 	=> false,
		'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Alert Added Successfully', 'cqpim') . '</div>',
	);
	header('Content-type: application/json');
	echo json_encode($return);
	exit();		
}
add_action( "wp_ajax_pto_client_edit_alert", "pto_client_edit_alert");
function pto_client_edit_alert() {
	$post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
	$level = isset($_POST['alert_level']) ? $_POST['alert_level'] : '';
	$message = isset($_POST['alert_message']) ? $_POST['alert_message'] : '';
	$key = isset($_POST['key']) ? $_POST['key'] : '';
	if(empty($level) || empty($message)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-warning alert-display">' . __('You must choose a level and add a message.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();		
	}
	$custom_alerts = get_post_meta($post_id, 'custom_alerts', true);
	if(empty($custom_alerts[$key]['global'])) {
		$custom_alerts[$key]['level'] = $level;
		$custom_alerts[$key]['message'] = $message;
		update_post_meta($post_id, 'custom_alerts', $custom_alerts);				
	} else {		
		$args = array(
			'post_type' => 'cqpim_client',
			'posts_per_page' => -1,
			'post_status' => 'private'
		);
		$clients = get_posts($args);
		foreach($clients as $client) {
			$custom_alerts = get_post_meta($client->ID, 'custom_alerts', true);
			$custom_alerts[$key]['level'] = $level;
			$custom_alerts[$key]['message'] = $message;			
			update_post_meta($client->ID, 'custom_alerts', $custom_alerts);		
		}		
	}
	$return =  array( 
		'error' 	=> false,
		'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Alert Edited Successfully', 'cqpim') . '</div>',
	);
	header('Content-type: application/json');
	echo json_encode($return);
	exit();		
}
add_action( "wp_ajax_pto_client_delete_alert", "pto_client_delete_alert");
function pto_client_delete_alert() {
	$post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
	$key = isset($_POST['key']) ? $_POST['key'] : '';
	$global = isset($_POST['global']) ? $_POST['global'] : '';
	if(empty($global)) {
		$custom_alerts = get_post_meta($post_id, 'custom_alerts', true);
		unset($custom_alerts[$key]);
		update_post_meta($post_id, 'custom_alerts', $custom_alerts);
	} else {
		$args = array(
			'post_type' => 'cqpim_client',
			'posts_per_page' => -1,
			'post_status' => 'private'
		);
		$clients = get_posts($args);
		foreach($clients as $client) {
			$custom_alerts = get_post_meta($client->ID, 'custom_alerts', true);
			if(!empty($custom_alerts[$key])) {
				unset($custom_alerts[$key]);
			}
			update_post_meta($client->ID, 'custom_alerts', $custom_alerts);		
		}		
	}
	$return =  array( 
		'error' 	=> false,
		'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Alert Deleted Successfully', 'cqpim') . '</div>',
	);
	header('Content-type: application/json');
	echo json_encode($return);
	exit();		
}
function pto_get_alert_names() {
	$alerts = array(
		'info' => __('Notice', 'cqpim'),
		'success' => __('Success', 'cqpim'),
		'warning' => __('Warning', 'cqpim'),
		'danger' => __('Error', 'cqpim'),
	);
	return $alerts;
}
function pto_filter_avatar_client( $avatar, $id ) {
    $user = get_user_by('id', $id);
	$team = pto_get_client_from_userid($user);
	$assigned = $team['assigned'];
	$type = $team['type'];
	if($type == 'admin') {
		$team_avatar = get_post_meta($assigned, 'team_avatar', true);
		if(!empty($team_avatar)) {
			return wp_get_attachment_image($team_avatar, array(50,50), false, '' );
		}
	} else {
		$client_contacts = get_post_meta($assigned, 'client_contacts', true);
		$client_contacts = $client_contacts&&is_array($client_contacts)?$client_contacts:array();
		foreach($client_contacts as $key => $contact) {
			if($key == $user->ID && !empty($contact['team_avatar'])) {
				$team_avatar = $contact['team_avatar'];
			}
		}
		if(!empty($team_avatar)) {
			return wp_get_attachment_image($team_avatar, array(50,50), false, '' );
		}
	}
	return $avatar;
}
add_filter( 'get_avatar', 'pto_filter_avatar_client', 1, 5 );
add_action( "wp_ajax_nopriv_pto_remove_current_client_photo", "pto_remove_current_client_photo");
add_action( "wp_ajax_pto_remove_current_client_photo", "pto_remove_current_client_photo");
function pto_remove_current_client_photo() {
	$user = wp_get_current_user();
	$team = pto_get_client_from_userid($user);
	$assigned = $team['assigned'];
	$type = $team['type'];
	if($type == 'admin') {
		update_post_meta($assigned, 'team_avatar', '');
	} else {
		$client_contacts = get_post_meta($assigned, 'client_contacts', true);
		$client_contacts[$user->ID]['team_avatar'] = '';
		update_post_meta($assigned, 'client_contacts', $client_contacts);
	}
	$return =  array( 
		'error' 	=> false,
		'message' 	=> __('The photo was removed successfully', 'cqpim'),
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();	
}
add_action( "wp_ajax_pto_manage_client_fe_files", "pto_manage_client_fe_files");
function pto_manage_client_fe_files() {
	$client = isset($_POST['post']) ? $_POST['post'] : '';
	$file = isset($_POST['file']) ? $_POST['file'] : '';
	$fe = isset($_POST['fe']) ? $_POST['fe'] : '';
	$fe_files = get_post_meta($client, 'fe_files', true);
	$fe_files = $fe_files&&is_array($fe_files)?$fe_files:array();
	$fe_files[$file] = $fe;
	update_post_meta($client, 'fe_files', $fe_files);
	$return =  array( 
		'error' 	=> false,
		'message' 	=> __('Operation Completed', 'cqpim'),
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();	
}