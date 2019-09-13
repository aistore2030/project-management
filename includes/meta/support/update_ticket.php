<?php
function pto_support_update_metabox_callback( $post ) {
 	wp_nonce_field( 
	'support_update_metabox', 
	'support_update_metabox_nonce' );
	$ticket_client = get_post_meta($post->ID, 'ticket_client', true);
	$ticket_updates = get_post_meta($post->ID, 'ticket_updates', true);
	$ticket_status = get_post_meta($post->ID, 'ticket_status', true);
	$ticket_priority = get_post_meta($post->ID, 'ticket_priority', true);
	?>
	<div class="cqpim-meta-left">
		<h4><?php _e('Ticket Status:', 'cqpim'); ?></h4>
		<select style="width:100%" id="ticket_status_new" name="ticket_status_new">
			<option value="open" <?php if($ticket_status == 'open') { echo 'selected="selected"'; } ?>><?php _e('Open', 'cqpim'); ?></option>
			<option value="resolved" <?php if($ticket_status == 'resolved') { echo 'selected="selected"'; } ?>><?php _e('Resolved', 'cqpim'); ?></option>
			<option value="hold" <?php if($ticket_status == 'hold') { echo 'selected="selected"'; } ?>><?php _e('On Hold', 'cqpim'); ?></option>
			<option value="waiting" <?php if($ticket_status == 'waiting') { echo 'selected="selected"'; } ?>><?php _e('Awaiting Response', 'cqpim'); ?></option>
		</select>
	</div>
	<div class="cqpim-meta-right">			
		<h4><?php _e('Ticket Priority:', 'cqpim'); ?></h4>
		<select style="width:100%" id="ticket_priority_new" name="ticket_priority_new">
			<option value="low" <?php if($ticket_priority == 'low') { echo 'selected="selected"'; } ?>><?php _e('Low', 'cqpim'); ?></option>
			<option value="normal" <?php if($ticket_priority == 'normal') { echo 'selected="selected"'; } ?>><?php _e('Normal', 'cqpim'); ?></option>
			<option value="high" <?php if($ticket_priority == 'high') { echo 'selected="selected"'; } ?>><?php _e('High', 'cqpim'); ?></option>
			<option value="immediate" <?php if($ticket_priority == 'immediate') { echo 'selected="selected"'; } ?>><?php _e('Immediate', 'cqpim'); ?></option>
		</select>
	</div>
	<div class="clear"></div>
	<div class="cqpim-meta-left">
		<h4><?php _e('Assigned To', 'cqpim'); ?></h4>
		<?php
		$owner = get_post_meta($post->ID, 'ticket_owner', true);
		$args = array(
			'post_type' => 'cqpim_teams',
			'posts_per_page' => -1,
			'post_status' => 'private'
		);
		$members = get_posts($args);
		if(empty($task_watchers)) {
			$task_watchers = array();
		}
		echo '<select name="ticket_owner">';
		echo '<option value="">' . __('Leave unassigned', 'cqpim') . '</option>';
		foreach($members as $member) {
			$team_details = get_post_meta($member->ID, 'team_details', true);
			$user = get_user_by('id', $team_details['user_id']);
			$caps = $user->allcaps;
			if(!empty($caps['cqpim_view_tickets'])) {
				if($member->ID == $owner) { $selected = 'selected="selected"'; } else { $selected = ''; }
				echo '<option value="' . $member->ID . '" ' . $selected . '> ' . $team_details['team_name'] . '</option>'; 
			}
		}
		echo '</select>';
		?>
	</div>
	<div class="cqpim-meta-right">
		<h4><?php _e('Upload Files', 'cqpim'); ?></h4>
		<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
		<div id="upload_attachments"></div>
		<div class="clear"></div>
		<input type="hidden" name="image_id" id="upload_attachment_ids">
	</div>
	<div class="clear"></div>
	<h4><?php _e('Watchers (People other than the Assignee and client who will get update notifications)', 'cqpim'); ?></h4>				
	<?php
	$task_watchers = get_post_meta($post->ID, 'ticket_watchers', true);
	$args = array(
		'post_type' => 'cqpim_teams',
		'posts_per_page' => -1,
		'post_status' => 'private'
	);
	$members = get_posts($args);
	if(empty($task_watchers)) {
		$task_watchers = array();
	}
	$i = 0;
	foreach($members as $member) {
		$team_details = get_post_meta($member->ID, 'team_details', true);
		$user = get_user_by('id', $team_details['user_id']);
		$caps = $user->allcaps;
		if(!empty($caps['cqpim_view_tickets'])) {
			if(in_array($member->ID, $task_watchers)) { $checked = 'checked="checked"'; } else { $checked = ''; }
			echo '<div class="task_watcher"><input type="checkbox" value="' . $member->ID . '" name="task_watchers[]" ' . $checked . ' /> ' . $team_details['team_name'] . '</div>';
			$i++;
		}
	}
	if(empty($i)) {
		echo '<p>' . __('There are no other team members who have permission to view support tickets', 'cqpim') . '</p>';
	}
	echo '<div class="clear"></div><br />';
	$data = get_option('cqpim_custom_fields_support');
	$data = str_replace('\"', '"', $data);
	if(!empty($data)) {
		$form_data = json_decode($data);
		$fields = $form_data;
	}
	$values = get_post_meta($post->ID, 'custom_fields', true);
	if(!empty($fields)) {
		echo '<div id="cqpim-custom-fields">';
		foreach($fields as $field) {
			$value = isset($values[$field->name]) ? $values[$field->name] : '';
			$id = strtolower($field->label);
			$id = str_replace(' ', '_', $id);
			$id = str_replace('-', '_', $id);
			$id = preg_replace('/[^\w-]/', '', $id);
			if(!empty($field->required) && $field->required == 1) {
				$required = 'required';
				$ast = '<span style="color:#F00">*</span>';
			} else {
				$required = '';
				$ast = '';
			}
			echo '<div style="padding-bottom:12px" class="cqpim_form_item">';
			if($field->type != 'header') {
				echo '<label style="display:block; padding-bottom:5px" for="' . $id . '">' . $field->label . ' ' . $ast . '</label>';
			}
			if($field->type == 'header') {
				echo '<' . $field->subtype . ' class="' . $field->className . '">' . $field->label . '</' . $field->subtype . '>';
			} elseif($field->type == 'text') {			
				echo '<input type="text" class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'website') {
				echo '<input type="url" class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'number') {
				echo '<input type="number" class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'textarea') {
				echo '<textarea class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']">' . $value . '</textarea>';
			} elseif($field->type == 'date') {
				echo '<input class="' . $field->className . ' datepicker" type="text" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'email') {
				echo '<input type="email" class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'checkbox-group') {
				if(!is_array($value)) {
					$value = array($value);
				}
				$options = $field->values;
				foreach($options as $option) {
					if(in_array($option->value, $value)) {
						$checked = 'checked="checked"';
					} else {
						$checked = '';
					}
					echo '<input type="checkbox" class="' . $field->className . '" value="' . $option->value . '" name="custom-field[' . $field->name . '][]" ' . $checked . ' /> ' . $option->label . '<br />';
				}
			} elseif($field->type == 'radio-group') {
				$options = $field->values;
				foreach($options as $option) {
					if($value == $option->value) {
						$checked = 'checked="checked"';
					} else {
						$checked = '';
					}
					echo '<input type="radio" class="' . $field->className . '" value="' . $option->value . '" name="custom-field[' . $field->name . ']" ' . $required . ' ' . $checked . ' /> ' . $option->label . '<br />';
				}
			} elseif($field->type == 'select') {
				$options = $field->values;
				echo '<select class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']">';
					foreach($options as $option) {	
						if($value == $option->value) {
							$checked = 'selected="selected"';
						} else {
							$checked = '';
						}
						echo '<option value="' . $option->value . '" ' . $checked . '>' . $option->label . '</option>';
					}
				echo '</select>';
			}
			if(!empty($field->other) && $field->other == 1) {
				echo '<br />';
				echo __('Other:', 'cqpim') . '<input style="width:100%" type="text" id="' . $id . '_other" name="custom-field[' . $field->name . '_other]" />';
			}
			if(!empty($field->description)) {
				echo '<span class="cqpim-field-description">' . $field->description . '</span>';
			}
			echo '</div>';
		}
		echo '</div>';
	}
	?>
	<h4><?php _e('Add Message', 'cqpim'); ?></h4>
	<textarea style="width:100%; height:300px" name="ticket_update_new"></textarea>
	<div class="clear"></div>
	<a class="cqpim_button font-green border-green op right mt-20 save" href="#"><?php _e('Update Ticket', 'cqpim'); ?> <div id="support_spinner_1" class="ajax_loader" style="display:none"></div></a>
	<div class="clear"></div>
	<?php
}
add_action( 'save_post', 'save_pto_support_update_metabox_data' );
function save_pto_support_update_metabox_data( $post_id ){
	if ( ! isset( $_POST['support_update_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['support_update_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'support_update_metabox' ) )
	    return $post_id;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	    return $post_id;
	if ( 'page' == $_POST['post_type'] ) {
	    if ( ! current_user_can( 'edit_page', $post_id ) )
	        return $post_id;
	  	} else {
	    if ( ! current_user_can( 'edit_post', $post_id ) )
	        return $post_id;
	}
	$data = $_POST;
	$files = isset($data['image_id']) ? $data['image_id'] : '';
	$type = 'admin';
	$duplicate = get_post_meta($post_id, 'duplicate', true);
	$now = time();
	$duplicate = $duplicate?$duplicate:0;
	$diff = $now - $duplicate;
	if($diff > 3) {
		if(!empty($data['custom-field'])) {
			update_post_meta($post_id, 'custom_fields', $data['custom-field']);
		}
		if(!empty($data['activate_ms'])) {
			update_post_meta($post_id, 'activate_ms', $data['activate_ms']);
		}
		if(!empty($data['ticket_client'])) {
			$ticket_id = $post_id;
			$client = isset($data['ticket_client']) ? $data['ticket_client'] : '';
			$owner = isset($data['ticket_owner']) ? $data['ticket_owner'] : '';
			update_post_meta($ticket_id, 'ticket_owner', $owner);
			update_post_meta($ticket_id, 'ticket_status', $data['ticket_status_new']);	
			update_post_meta($ticket_id, 'ticket_priority', $data['ticket_priority_new']);
			update_post_meta($ticket_id, 'ticket_client', $client);
			$client_contact = isset($data['client_contact']) ? $data['client_contact'] : '';
			$contact = get_user_by('id', $client_contact);
			$current_user = wp_get_current_user();
			$args = array(
				'post_type' => 'cqpim_teams',
				'posts_per_page' => -1,
				'post_status' => 'private'
			);
			$members = get_posts($args);
			foreach($members as $member) {
				$team_details = get_post_meta($member->ID, 'team_details', true);
				if($team_details['user_id'] == $current_user->ID) {
					$assigned = $member->ID;
				}
			}
			$ticket_changes = array();
			$attachments = isset($data['image_id']) ? $data['image_id'] : '';
			if(!empty($attachments)) {
				$attachments = explode(',', $attachments);
				foreach($attachments as $attachment) {
					global $wpdb;
					$wpdb->query(
						"
						UPDATE $wpdb->posts 
						SET post_parent = $post_id
						WHERE ID = $attachment
						AND post_type = 'attachment'
						"
					);
					update_post_meta($attachment, 'cqpim', true);
					$filename = basename( get_attached_file( $attachment ) );
					$ticket_changes[] = sprintf(__('Uploaded file: %1$s', 'cqpim'), $filename);
				}
			}
			$ticket_updates = get_post_meta($ticket_id, 'ticket_updates', true);
			if(empty($ticket_updates)) {
				$ticket_updates = array();
			}
			$details = isset($data['ticket_update_new']) ? $data['ticket_update_new'] : '';
			if(!empty($ticket_changes) || !empty($details)) {
				$ticket_updates[] = array(
					'details' => $details,
					'time' => current_time('timestamp'),
					'name' => $current_user->display_name,
					'email' => $current_user->user_email,
					'user' => $assigned,
					'type' => 'admin',
					'changes' => $ticket_changes
				);
				update_post_meta($ticket_id, 'ticket_updates', $ticket_updates);
			}
			$currency = get_option('currency_symbol');
			$currency_code = get_option('currency_code');
			$currency_position = get_option('currency_symbol_position');
			$currency_space = get_option('currency_symbol_space'); 
			$client_currency = get_post_meta($client, 'currency_symbol', true);
			$client_currency_code = get_post_meta($client, 'currency_code', true);
			$client_currency_space = get_post_meta($client, 'currency_space', true);		
			$client_currency_position = get_post_meta($client, 'currency_position', true);
			if(!empty($client_currency)) {
				update_post_meta($ticket_id, 'currency_symbol', $client_currency);
			} else {
				update_post_meta($ticket_id, 'currency_symbol', $currency);
			}
			if(!empty($client_currency_code)) {
				update_post_meta($ticket_id, 'currency_code', $client_currency_code);
			} else {
				update_post_meta($ticket_id, 'currency_code', $currency_code);
			}
			if(!empty($client_currency_space)) {
				update_post_meta($ticket_id, 'currency_space', $client_currency_space);
			} else {
				update_post_meta($ticket_id, 'currency_space', $currency_space);
			}
			if(!empty($client_currency_position)) {
				update_post_meta($ticket_id, 'currency_position', $client_currency_position);
			} else {
				update_post_meta($ticket_id, 'currency_position', $currency_position);
			}
			$watchers = isset($data['task_watchers']) ? $data['task_watchers'] : '';
			update_post_meta($ticket_id, 'ticket_watchers', $watchers);
			$last_updated = current_time('timestamp');
			update_post_meta($ticket_id, 'last_updated', $last_updated);
			$ticket_updated = array(
				'ID' => $post_id,
				'post_author' => $client_contact,
			);
			if ( ! wp_is_post_revision( $post_id ) ){
				remove_action('save_post', 'save_pto_support_update_metabox_data');
				wp_update_post( $ticket_updated );
				add_action('save_post', 'save_pto_support_update_metabox_data');
			}
			$attachments = array();
			if($data['ticket_priority_new'] == 'high' || $data['ticket_priority_new'] == 'immediate') {
				add_filter('phpmailer_init','update_priority_mailer');
			}
			$email_subject = get_option('client_update_ticket_subject');
			$email_content = get_option('client_update_ticket_email');
			$email_subject = pto_replacement_patterns($email_subject, $ticket_id, 'ticket');
			$email_content = pto_replacement_patterns($email_content, $ticket_id, 'ticket');
			$addresses_to_send = array();
			// Get client email
			$addresses_to_send[] = array(
				'email' => $contact->user_email,
				'name' => $contact->display_name
			);
			// Get Assigned email
			$owner_send = get_post_meta($ticket_id, 'ticket_owner', true);
			$owner_details = get_post_meta($data['ticket_owner'], 'team_details', true);
			$owner_email = isset($owner_details['team_email']) ? $owner_details['team_email'] : '';
			$addresses_to_send[] = array(
				'email' => $owner_email,
				'name' => $owner_details['team_name']
			);
			// Get Watcher emails_to_send
			$watchers_send = get_post_meta($ticket_id, 'ticket_watchers', true);
			if(empty($watchers_send)) {
				$watchers_send = array();
			}
			foreach($watchers_send as $watcher) {
				$watcher_details = get_post_meta($watcher, 'team_details', true);
				$watcher_email = isset($watcher_details['team_email']) ? $watcher_details['team_email'] : '';
				$addresses_to_send[] = array(
					'email' => $watcher_email,
					'name' => $watcher_details['team_name']
				);
			}
			$i = 0;
			$current = wp_get_current_user();
			$current_name = $current->display_name;
			foreach($addresses_to_send as $address) {
				$name = $address['name'];
				${"email_content_" . $i} = str_replace('%%NAME%%', $name, $email_content);
				${"email_content_" . $i} = str_replace('%%UPDATER_NAME%%', $current_name, ${"email_content_" . $i});
				${"email_subject_" . $i} = str_replace('%%UPDATER_NAME%%', $current_name, $email_subject);
				${"email_subject_" . $i} = str_replace('%%PIPING_ID%%', '[' . get_option('cqpim_string_prefix') . ':' . $ticket_id . ']', ${"email_subject_" . $i});
				pto_send_emails($address['email'], ${"email_subject_" . $i}, ${"email_content_" . $i}, '', $attachments, 'support');
				$i++;
			}
		} else {
			pto_update_support_ticket($post_id, $data, $files, 'admin');
		}
		update_post_meta($post_id, 'duplicate', time());
	}
}