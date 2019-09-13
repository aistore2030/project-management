<?php
function pto_team_details_metabox_callback( $post ) {
 	wp_nonce_field( 
	'team_details_metabox', 
	'team_details_metabox_nonce' ); 
	$team_details = get_post_meta($post->ID, 'team_details', true);
	$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
	$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
	$team_telephone = isset($team_details['team_telephone']) ? $team_details['team_telephone'] : '';
	$team_job = isset($team_details['team_job']) ? $team_details['team_job'] : '';
	$team_perms = isset($team_details['team_perms']) ? $team_details['team_perms'] : '';
	if(is_array($team_perms)) { $team_perms = $team_perms[0]; }
	$team_user = isset($team_details['user_id']) ? $team_details['user_id'] : '';
	$team_user_object = get_user_by('id', $team_user);
	$user_taken = isset($team_details['user_taken']) ? $team_details['user_taken'] : '';
	$email_taken = isset($team_details['email_exists']) ? $team_details['email_exists'] : '';
	?>
	<p><?php _e('Name', 'cqpim'); ?></p>
	<input type="text" id="team_name" name="team_name" value="<?php echo $team_name; ?>" required />
	<p><?php _e('Email', 'cqpim'); ?></p>
	<input type="text"  id="team_email" name="team_email" value="<?php echo $team_email; ?>" required />
	<p><?php _e('Telephone', 'cqpim'); ?></p>
	<input type="text"  id="team_telephone" name="team_telephone" value="<?php echo $team_telephone; ?>" />
	<p><?php _e('Job Title', 'cqpim'); ?></p>
	<input type="text"  id="team_job" name="team_job" value="<?php echo $team_job; ?>" />
	<?php if(current_user_can('cqpim_grant_admin_role')) { ?>
		<p><?php _e('Permissions Level', 'cqpim'); ?></p>
		<?php if($team_perms == 'administrator' || !empty($team_user_object->roles) && in_array('administrator', $team_user_object->roles)) { ?>
			<br />
			<p><?php _e('This Team Member is a WordPress Administrator, you cannot change their role here.', 'cqpim'); ?></p>
		<?php } else if(!current_user_can('cqpim_grant_admin_role')) { ?>
			<br />
			<p><?php _e('You do not have permission to edit roles.', 'cqpim'); ?></p>
		<?php } else { ?>
		<select id="team_perms" name="team_perms">
			<?php 
			$plugin_roles = get_option('cqpim_roles');
			foreach($plugin_roles as $plugin_role) {
				if($plugin_role == 'cqpim_admin') { ?>
					<option value="<?php echo $plugin_role; ?>" <?php if($team_perms == $plugin_role) { echo 'selected="selected"'; } ?>>PTO Admin</option>				
				<?php } else {
					$plugin_role_machine = 'cqpim_' . $plugin_role;
					$role_name = str_replace('_', ' ', $plugin_role_machine);
					$role_name = str_replace('cqpim', 'PTO', $role_name);
					$role_name = ucwords($role_name); ?>			
					<option value="<?php echo $plugin_role_machine; ?>" <?php if($team_perms == $plugin_role_machine) { echo 'selected="selected"'; } ?>><?php echo $role_name; ?></option>
			<?php }						
			}
			?>
		</select>
		<?php } ?>
	<?php } else { 
		$roles = isset($team_user_object->roles) ? $team_user_object->roles : array();
		if(in_array('administrator', $roles)) {
			echo '<input type="hidden" name="team_perms" value="administrator" />';
		}
	} ?>
	<?php if($user_taken || $email_taken) {
		$team_details = get_post_meta($post->ID, 'team_details', true); 
		unset($team_details['user_taken']);
		update_post_meta($post->ID, 'team_details', $team_details); ?>
		<div class="cqpim-alert cqpim-alert-danger alert-display"><?php _e('EMAIL UPDATE FAILED: There is already a user with that email address, please try a different one.', 'cqpim'); ?></div>
	<?php } ?>
	<?php if(current_user_can('publish_cqpim_teams')) { ?>
		<a class="cqpim_button font-white bg-blue block mt-10 rounded_2 block save cqpim_button_link" href="#"><?php _e('Update Team Member', 'cqpim'); ?></a>
	<?php } ?>
	<?php if($team_user && current_user_can('cqpim_reset_team_passwords') && $team_perms != 'administrator') { ?>
	<a class="cqpim_button font-white bg-blue block mt-10 rounded_2 block cqpim_button_link reset-password" href="#"><?php _e('Reset User\'s Password', 'cqpim'); ?></a>
	<div id="password_reset_container" style="display:none">
		<div id="password_reset">
			<div style="padding:12px">
				<h3><?php _e('Reset Password', 'cqpim'); ?></h3>
				<p><?php _e('If you would like to reset the user\'s password, please enter and confirm the new password below.', 'cqpim'); ?> <br />
				<?php _e('This will be encrypted and saved to the database, you can however choose to send an email with the <br />
				new password before the encryption takes place.', 'cqpim'); ?></p>
				<input class="pass" type="password" id="new_password" name="new_password" placeholder="<?php _e('Enter new password', 'cqpim'); ?>" />
				<br /><br />
				<input class="pass" type="password" id="confirm_password" name="confirm_password" placeholder="<?php _e('Confirm new password', 'cqpim'); ?>" />
				<br /><br />
				<input type="checkbox" id="send_new_password" name="send_new_password" value="1" /> <?php _e('Send the user\'s new password by email', 'cqpim'); ?>
				<br /><br />
				<input class="pass" type="hidden" id="pass_type" name="pass_type" value="team" />
				<div id="password_messages"></div>
				<button class="cancel-colorbox cqpim_button font-red border-red mt-10 op"><?php _e('Cancel', 'cqpim'); ?></button>
				<button id="reset_pass_ajax" class="cqpim_button font-green border-green mt-10 op right" value="<?php echo $team_user; ?>"><?php _e('Reset Password', 'cqpim'); ?></button><div class="ajax_spinner" style="display:none"></div>
			</div>
		</div>
	</div>
	<?php }
}
add_action( 'save_post', 'save_pto_team_details_metabox_data' );
function save_pto_team_details_metabox_data( $post_id ){
	if ( ! isset( $_POST['team_details_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['team_details_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'team_details_metabox' ) )
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
	$team_details = get_post_meta($post_id, 'team_details', true);
	if(!$team_details) {
		$team_details = array();
	}
	if(isset($_POST['team_name'])) {
		$team_details['team_name'] = $_POST['team_name'];
	}
	if(isset($_POST['team_email'])) {
		$team_details['team_email'] = $_POST['team_email'];
	}
	if(isset($_POST['team_telephone'])) {
		$team_details['team_telephone'] = $_POST['team_telephone'];
	}
	if(isset($_POST['team_job'])) {
		$team_details['team_job'] = $_POST['team_job'];
	}
	if(isset($_POST['team_perms'])) {
		$team_details['team_perms'] = $_POST['team_perms'];
	}
	if(empty($_POST['team_perms'])) {
		$team_details['team_perms'] = 'cqpim_user';
	}	
	$wp_user_id = $team_details['user_id'];
	$user = get_user_by('id',  $wp_user_id );
	if(!$user) {
		if($team_details['team_email']) {
			$login = $team_details['team_email'];
			$passw = pto_random_string(10);
			$email = $team_details['team_email'];
			if ( !username_exists( $login )  && !email_exists( $email ) ) {
				unset($team_details['user_taken']);
				$user_id = wp_create_user( $login, $passw, $email );
				$user = new WP_User( $user_id );
				$user->set_role( $team_details['team_perms'] );
				$team_details['user_id'] = $user_id;
				update_post_meta($post_id, 'team_details', $team_details);
				send_pto_team_email($post_id, $passw);
				$user_data = array(
					'ID' => $user_id,
					'display_name' => $team_details['team_name'],
					'first_name' => $team_details['team_name'],
				);
				wp_update_user($user_data);
			} else {
				$team_details['user_taken'] = true;
				update_post_meta($post_id, 'team_details', $team_details);
			}
		}
	} else {
		$team_details_old = get_post_meta($post_id, 'team_details', true);
		if($team_details['team_email'] != $team_details_old['team_email']) {
			$login = $team_details['team_email'];
			$email = $team_details['team_email'];	
			if ( !email_exists( $email ) ) {
				unset($team_details['user_taken']);	
				$user_data = array(
					'ID' => $user->ID,
					'display_name' => $team_details['team_name'],
					'first_name' => $team_details['team_name'],
					'user_email' => $team_details['team_email'],
				);
				wp_update_user($user_data);	
				if(!in_array('administrator', $user->roles)) {
					$user->set_role( $team_details['team_perms'] );
				}
				update_post_meta($post_id, 'team_details', $team_details);
			} else {
				$team_details['team_email'] = $team_details_old['team_email'];
				$team_details['user_taken'] = true;
				update_post_meta($post_id, 'team_details', $team_details);
			}
		} else {
			$user_data = array(
				'ID' => $user->ID,
				'display_name' => $team_details['team_name'],
				'first_name' => $team_details['team_name'],
			);
			wp_update_user($user_data);
			if(!in_array('administrator', $user->roles)) {
				$user->set_role( $team_details['team_perms'] );
			}
			update_post_meta($post_id, 'team_details', $team_details);			
		}
	}
	$title = get_the_title($post_id);
	$name_token = '%%NAME%%';
	$team_details = get_post_meta($post_id, 'team_details', true);
	$team_name = $team_details['team_name'];
	$title = str_replace($name_token, $team_name, $title);
	$client_updated = array(
		'ID' => $post_id,
		'post_title' => $title,
		'post_name' => $post_id,
	);	
	if ( ! wp_is_post_revision( $post_id ) ){
		remove_action('save_post', 'save_pto_team_details_metabox_data');
		wp_update_post( $client_updated );
		add_action('save_post', 'save_pto_team_details_metabox_data');
	}
	$team_details = get_post_meta($post_id, 'team_details', true);
	$title = $team_details['team_name'];
	$slug = $title;
	$slug = strtolower($slug);
	$slug = preg_replace("/[^a-z0-9_\s-]/", "", $slug);
	$slug = preg_replace("/[\s-]+/", " ", $slug);
	$slug = preg_replace("/[\s_]/", "-", $slug);
	$client_updated = array(
		'ID' => $post_id,
		'post_title' => $title,
		'post_name' => $slug,
	);	
	if ( ! wp_is_post_revision( $post_id ) ){
		remove_action('save_post', 'save_pto_team_details_metabox_data');
		wp_update_post( $client_updated );
		add_action('save_post', 'save_pto_team_details_metabox_data');
	}	
}