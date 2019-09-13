<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-users font-green-sharp" aria-hidden="true"></i> <?php _e('Manage Contacts', 'cqpim'); ?>
		</div>
	</div>
	<div class="cqpim-dash-item-inside">
		<?php 
		$client_settings = get_option('allow_client_users');
		$user = wp_get_current_user();
		$client_logs = get_post_meta($assigned, 'client_logs', true);
		if(empty($client_logs)) {
			$client_logs = array();
		}
		$now = time();
		$client_logs[$now] = array(
			'user' => $user->ID,
			'page' => __('Client Dashboard Contacts Page', 'cqpim')
		);
		update_post_meta($assigned, 'client_logs', $client_logs);
		if($client_settings == 1) { ?>
			<div class="cqpim-dash-item-inside">
				<p><?php _e('If you would like to give multiple users at your organisation access to your client dashboard, you can do so here.', 'cqpim'); ?></p>
				<?php 
				$client_contacts = get_post_meta($assigned, 'client_contacts', true);
				if(empty($client_contacts)) {
					echo '<p>' . __('You have not added any additional contacts', 'cqpim') . '</p>';
				} else {
					echo '<br />';
					foreach($client_contacts as $key => $contact) {
						$user = get_user_by('id', $contact['user_id']);
						echo '<div style="width:190px" class="team_member">';
						$value = get_option('cqpim_disable_avatars');
						if(empty($value)) {
							echo '<div class="cqpim_gravatar">';
								echo get_avatar( $user->ID, 80, '', false, array('force_display' => true) ); 
							echo '</div>';
						} 
						echo '<div class="team_details">';
						echo '<span class="team_name block">' . $contact['name'] . '</span>';
						echo '<i class="fa fa-envelope" aria-hidden="true"></i> ' . $contact['email'] . '<br />';
						echo '<i class="fa fa-phone" aria-hidden="true"></i> ' . $contact['telephone'] . '<br />';
						echo '</div>';
						echo '<br />';
						echo '<div class="team_delete"><button class="edit-milestone cqpim_button cqpim_small_button border-amber font-amber" value="'. $key . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button><button class="delete_team cqpim_button cqpim_small_button border-red font-red" value="' . $key . '"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
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
									<label for="contact_email_<?php echo $key; ?>"><?php _e('Contact Email', 'cqpim'); ?></label>
									<input type="text" id="contact_email_<?php echo $key; ?>" name="contact_email_<?php echo $key; ?>" value="<?php echo $contact['email']; ?>" />
									<label for="contact_telephone_<?php echo $key; ?>"><?php _e('Contact Telephone', 'cqpim'); ?></label>
									<input type="text" id="contact_telephone_<?php echo $key; ?>" name="contact_telephone_<?php echo $key; ?>" value="<?php echo $contact['telephone']; ?>" />
									<h3><?php _e('Reset Password', 'cqpim'); ?></h3>
									<input class="pass" type="password" id="new_password_<?php echo $key; ?>" name="new_password_<?php echo $key; ?>" placeholder="<?php _e('Enter new password', 'cqpim'); ?>" />
									<input class="pass" type="password" id="confirm_password_<?php echo $key; ?>" name="confirm_password_<?php echo $key; ?>" placeholder="<?php _e('Confirm new password', 'cqpim'); ?>" />
									<input type="checkbox" id="send_new_password_<?php echo $key; ?>" name="send_new_password_<?php echo $key; ?>" value="1" /> <?php _e('Send the contact\'s new password by email', 'cqpim'); ?>
									<input class="pass" type="hidden" id="pass_type_<?php echo $key; ?>" name="pass_type_<?php echo $key; ?>" value="contact" />
									<div id="client_team_messages_<?php echo $key; ?>"></div>							
									<button class="cancel-colorbox cqpim_button font-red border-red op mt-20"><?php _e('Cancel', 'cqpim'); ?></button>
									<button id="contact_edit_submit_<?php echo $key; ?>" class="cqpim_button mt-20 font-green border-green right op contact_edit_submit" value="<?php echo $key; ?>"><?php _e('Edit Contact', 'cqpim'); ?><span id="ajax_spinner_contact_<?php echo $key; ?>" class="ajax_loader" style="display:none"></span></button>
								</div>
							</div>
						</div>
					<?php
					}
				} ?>
			</div>
		<?php } if($client_settings == 1) { ?>
			<br /><br />
			<button style="float:left; margin-left:0" id="add_client_team" class="cqpim_button bg-blue font-white rounded_2"><?php _e('Add Contact', 'cqpim'); ?></button>
		<?php } ?>
		<div class="clear"></div>
		<div id="add_client_team_ajax_container" style="display:none">
			<div id="add_client_team_ajax">
				<div style="padding:12px">
					<h3><?php _e('Add Contact', 'cqpim'); ?></h3>
					<p><?php _e('Adding a contact will create a new login and give the user access to the client dashboard.', 'cqpim'); ?></p>
					<label for="contact_name"><?php _e('Contact Name', 'cqpim'); ?></label>
					<input type="text" id="contact_name" name="contact_name" />
					<label for="contact_email"><?php _e('Contact Email', 'cqpim'); ?></label>
					<input type="text" id="contact_email" name="contact_email" />
					<label for="contact_telephone"><?php _e('Contact Telephone', 'cqpim'); ?></label>
					<input type="text" id="contact_telephone" name="contact_telephone" />
					<input type="checkbox" id="send_contact_details" name="send_contact_details" value="1" /> <?php _e('Send the contact login details by email', 'cqpim'); ?>
					<input type="hidden" id="post_ID" name="post_id" value="<?php echo $assigned; ?>" />
					<div id="client_team_messages"></div>
					<button class="cancel-colorbox cqpim_button bg-red font-white rounded_2"><?php _e('Cancel', 'cqpim'); ?></button>
					<button id="add_client_team_submit" class="cqpim_button bg-blue font-white right rounded_2"><?php _e('Add Client Contact', 'cqpim'); ?><span id="ajax_spinner_add_contact" class="ajax_loader" style="display:none"></span></button>
				</div>
			</div>
		</div>
	</div>
</div>	