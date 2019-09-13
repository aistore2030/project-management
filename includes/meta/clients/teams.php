<?php
function pto_client_team_metabox_callback( $post ) {
 	wp_nonce_field( 
	'client_team_metabox', 
	'client_team_metabox_nonce' );
	$client_contacts = get_post_meta($post->ID, 'client_contacts', true);
	$client_contacts = $client_contacts && is_array($client_contacts)?$client_contacts:array();
	$client_ids = get_post_meta($post->ID, 'client_ids', true);
	if(empty($client_contacts)) {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('This client does not have any additional contacts. If you add a new contact then they will be given access to the client dashboard. You will also be able to assign quotes/projects to individual contacts.', 'cqpim') . '</div>';
	} else {		
		foreach($client_contacts as $key => $contact) {
			$size = 100;
			echo '<div class="team_member">';
			$value = get_option('cqpim_disable_avatars');
			if(empty($value)) {
				echo '<div class="cqpim_gravatar">';
					echo get_avatar( $contact['user_id'], 80, '', false, array('force_display' => true) ); 
				echo '</div>';
			} 
			echo '<div class="team_details">';
			echo '<span class="team_name block">' . $contact['name'] . '</span>';
			echo '<a href="mailto:' . $contact['email'] . '" class="cqpim_tooltip" title="' . $contact['email'] . '"><i class="fa fa-envelope" aria-hidden="true"></i></a><br />';
			echo '<i class="fa fa-phone" aria-hidden="true"></i> ' . $contact['telephone'] . '<br />';
			echo '</div>';
			if(current_user_can('publish_cqpim_clients')) {
				echo '<br />';
				echo '<div class="team_delete"><button class="edit-milestone cqpim_button cqpim_small_button border-amber font-amber" value="'. $key . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button><button class="delete_team cqpim_button cqpim_small_button border-red font-red" value="' . $key . '"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
			}
			echo '<div class="clear"></div>';
			echo '</div>';			
		}
		echo '<div class="clear"></div>';
		foreach($client_contacts as $key => $contact) { ?>
			<div id="contact_edit_container_<?php echo $key; ?>" style="display:none">
				<div id="contact_edit_<?php echo $key; ?>" class="contact_edit">
					<div style="padding:12px">
						<h3><?php _e('Edit Contact', 'cqpim'); ?> - <?php echo $contact['name']; ?></h3>
						<label for="contact_name_<?php echo $key; ?>"><?php _e('Contact Name', 'cqpim'); ?></label>
						<input type="text" id="contact_name_<?php echo $key; ?>" name="contact_name_<?php echo $key; ?>" value="<?php echo $contact['name']; ?>" />
						<br />
						<label for="contact_email_<?php echo $key; ?>"><?php _e('Contact Email', 'cqpim'); ?></label>
						<input type="text" id="contact_email_<?php echo $key; ?>" name="contact_email_<?php echo $key; ?>" value="<?php echo $contact['email']; ?>" />
						<br />
						<label for="contact_telephone_<?php echo $key; ?>"><?php _e('Contact Telephone', 'cqpim'); ?></label>
						<input type="text" id="contact_telephone_<?php echo $key; ?>" name="contact_telephone_<?php echo $key; ?>" value="<?php echo $contact['telephone']; ?>" />
						<br />
						<label for="new_password_<?php echo $key; ?>"><?php _e('Change Password', 'cqpim'); ?></label>
						<input class="pass" type="password" id="new_password_<?php echo $key; ?>" name="new_password_<?php echo $key; ?>" placeholder="<?php _e('Enter new password', 'cqpim'); ?>" />
						<input style="margin:6px 0"; class="pass" type="password" id="confirm_password_<?php echo $key; ?>" name="confirm_password_<?php echo $key; ?>" placeholder="<?php _e('Confirm new password', 'cqpim'); ?>" />
						<br />
						<input type="checkbox" id="send_new_password_<?php echo $key; ?>" name="send_new_password_<?php echo $key; ?>" value="1" /> <?php _e('Send the contact\'s new password by email', 'cqpim'); ?>
						<br />
						<p><?php _e('Email Preferences', 'cqpim'); ?></p>
						<?php
						$no_tasks = isset($contact['notifications']['no_tasks']) ? $contact['notifications']['no_tasks'] : '';
						if($no_tasks == 1) { $nt_check = 'checked'; $ntc_disabled = 'disabled'; } else { $nt_check = ''; $ntc_disabled = ''; }
						$no_tasks_comment = isset($contact['notifications']['no_tasks_comment']) ? $contact['notifications']['no_tasks_comment'] : '';
						if($no_tasks_comment == 1) { $ntc_check = 'checked'; } else { $ntc_check = ''; }
						$no_tickets = isset($contact['notifications']['no_tickets']) ? $contact['notifications']['no_tickets'] : '';
						if($no_tickets == 1) { $nti_check = 'checked'; $ntic_disabled = 'disabled'; } else { $nti_check = ''; $ntic_disabled = ''; }
						$no_tickets_comment = isset($contact['notifications']['no_tickets_comment']) ? $contact['notifications']['no_tickets_comment'] : '';
						if($no_tickets_comment == 1) { $ntic_check = 'checked'; } else { $ntic_check = ''; }
						$no_bugs = isset($contact['notifications']['no_bugs']) ? $contact['notifications']['no_bugs'] : '';
						if($no_bugs == 1) { $bi_check = 'checked'; $bic_disabled = 'disabled'; } else { $bi_check = ''; $bic_disabled = ''; }
						$no_bugs_comment = isset($contact['notifications']['no_bugs_comment']) ? $contact['notifications']['no_bugs_comment'] : '';
						if($no_bugs_comment == 1) { $bic_check = 'checked'; } else { $bic_check = ''; }
						echo '<strong>' . __('Tasks:', 'cqpim') . ' </strong><br />';
						echo '<input type="checkbox" name="no_tasks_' . $key . '" id="no_tasks_' . $key . '" value="1" ' . $nt_check . ' />' . __('Do not send task update emails.', 'cqpim');
						echo '<br /><input type="checkbox" name="no_tasks_comment_' . $key . '" id="no_tasks_comment_' . $key . '" value="1" ' . $ntc_check . ' ' . $ntc_disabled . ' />' . __('Notify new comments only.', 'cqpim');
						echo '<br /><br /><strong>' . __('Tickets:', 'cqpim') . ' </strong><br />';
						echo '<input type="checkbox" name="no_tickets_' . $key . '" id="no_tickets_' . $key . '" value="1" ' . $nti_check . ' />' . __('Do not send ticket update emails.', 'cqpim');
						echo '<br /><input type="checkbox" name="no_tickets_comment_' . $key . '" id="no_tickets_comment_' . $key . '" value="1" ' . $ntic_check . ' ' . $ntic_disabled . ' />' . __('Notify new comments only.', 'cqpim');					
						if(is_plugin_active('cqpim-bugs/cqpim-bugs.php')) {
							echo '<br /><br /><strong>' . __('Bugs:', 'cqpim') . ' </strong><br />';
							echo '<input type="checkbox" name="no_bugs_' . $key . '" id="no_bugs_' . $key . '" value="1" ' . $bi_check . ' />' . __('Do not send bug update emails.', 'cqpim');
							echo '<br /><input type="checkbox" name="no_bugs_comment_' . $key . '" id="no_bugs_comment_' . $key . '" value="1" ' . $bic_check . ' ' . $bic_disabled . ' />' . __('Notify new comments only.', 'cqpim');					
						}
						?>							
						<input class="pass" type="hidden" id="pass_type_<?php echo $key; ?>" name="pass_type_<?php echo $key; ?>" value="contact" />
						<div id="client_team_messages_<?php echo $key; ?>"></div>							
						<button class="cancel-colorbox cqpim_button cqpim_small_button border-red font-red op mt-10"><?php _e('Cancel', 'cqpim'); ?></button>
						<button id="contact_edit_submit_<?php echo $key; ?>" class="cqpim_button cqpim_small_button border-green font-green contact_edit_submit op mt-10 right" value="<?php echo $key; ?>"><?php _e('Edit Contact', 'cqpim'); ?></button>
					</div>
				</div>
			</div>
		<?php
		}
	}
	if(current_user_can('publish_cqpim_clients')) { ?>
		<button id="add_client_team" class="mt-20 cqpim_button cqpim_small_button border-blue font-blue right" value=""><?php _e('Add Client Contact', 'cqpim'); ?></button>
	<?php } ?>
	<div class="clear"></div>
	<div id="add_client_team_ajax_container" style="display:none">
		<div id="add_client_team_ajax">
			<div style="padding:12px">
				<h3><?php _e('Add Client Contact', 'cqpim'); ?></h3>
				<p><?php _e('Adding a client contact will create a new login and give the user access to the client dashboard for this client. <br />You will also be able to assign quotes and projects to this contact', 'cqpim'); ?></p>
				<label for="contact_name"><?php _e('Contact Name', 'cqpim'); ?></label>
				<input type="text" id="contact_name" name="contact_name" />
				<br /><br />
				<label for="contact_email"><?php _e('Contact Email', 'cqpim'); ?></label>
				<input type="text" id="contact_email" name="contact_email" />
				<br /><br />
				<label for="contact_telephone"><?php _e('Contact Telephone', 'cqpim'); ?></label>
				<input type="text" id="contact_telephone" name="contact_telephone" />
				<br /><br />
				<input type="checkbox" id="send_contact_details" name="send_contact_details" value="1" /> <?php _e('Send the contact login details by email', 'cqpim'); ?>
				<br /><br />
				<div id="client_team_messages"></div>
				<button class="cancel-colorbox cqpim_button cqpim_small_button border-red font-red op mt-10"><?php _e('Cancel', 'cqpim'); ?></button>			
				<button id="add_client_team_submit" class="cqpim_button cqpim_small_button border-green font-green contact_edit_submit op mt-10 right"><?php _e('Add Client Contact', 'cqpim'); ?><span id="ajax_spinner_add_contact" class="ajax_loader" style="display:none"></span></button>
			</div>
		</div>
	</div>
	<?php
}