<?php
function pto_contact_details_metabox_callback( $post ) {
 	wp_nonce_field( 
	'contact_details_metabox', 
	'contact_details_metabox_nonce' );
	$client_details = get_post_meta($post->ID, 'client_details', true);
	$notifications = get_post_meta($post->ID, 'client_notifications', true);
	$client_ref = isset($client_details['client_ref']) ? $client_details['client_ref'] : '';
	$client_company = isset($client_details['client_company']) ? $client_details['client_company'] : '';
	$client_contact = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
	$client_address = isset($client_details['client_address']) ? $client_details['client_address'] : '';
	$client_postcode = isset($client_details['client_postcode']) ? $client_details['client_postcode'] : '';
	$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
	$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
	$client_user = isset($client_details['user_id']) ? $client_details['user_id'] : '';
	$user_taken = isset($client_details['user_taken']) ? $client_details['user_taken'] : '';
	$email_taken = isset($client_details['email_exists']) ? $client_details['email_exists'] : '';
	$avatar = get_option('cqpim_disable_avatars');	
	?>
	<div style="text-align:center">
		<?php if(empty($avatar)) { ?>
			<div class="cqpim_avatar">
				<?php echo get_avatar( $client_user, 80, '', false, array('force_display' => true) ); ?>
			</div>
		<?php } ?>
	</div>
	<p><?php _e('Client Number:', 'cqpim'); ?> </p>
	<input type="text" name="client_ref" id="client_ref" value="<?php if(!$client_ref) { echo $post->ID; } else { echo $client_ref; } ?>" required />
	<p><?php _e('Company Name:', 'cqpim'); ?> </p>
	<input type="text" name="client_company" id="client_company" value="<?php echo $client_company; ?>" required />
	<p><?php _e('Main Contact Name:', 'cqpim'); ?> </p>
	<input type="text" name="client_contact" id="client_contact" value="<?php echo $client_contact; ?>" required />
	<p><?php _e('Client Address:', 'cqpim'); ?> </p>
	<textarea name="client_address" id="client_address" required><?php echo $client_address; ?></textarea>
	<p><?php _e('Client Postcode:', 'cqpim'); ?> </p>
	<input type="text" name="client_postcode" id="client_postcode" value="<?php echo $client_postcode; ?>" required />
	<p><?php _e('Client Telephone:', 'cqpim'); ?> </p>
	<input type="text" name="client_telephone" id="client_telephone" value="<?php echo $client_telephone; ?>" />
	<p><?php _e('Client Email:', 'cqpim'); ?> </p>
	<input type="text" name="client_email" id="client_email" value="<?php echo $client_email; ?>" required />
	<?php if($user_taken || $email_taken) { 
		$client_details = get_post_meta($post->ID, 'client_details', true); 
		unset($client_details['user_taken']);
		update_post_meta($post->ID, 'client_details', $client_details); ?>
		<div class="cqpim-alert cqpim-alert-danger sbold alert-display"><?php _e('EMAIL UPDATE FAILED:  There is already a user with that email address, please try a different one.', 'cqpim'); ?></div>
	<?php } ?>
	<?php if(current_user_can('publish_cqpim_clients')) { 
	$pending = get_post_meta($post->ID, 'pending', true); 
	if(!empty($pending)) {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('This client is pending approval. Update the client to activate the account and send login details.', 'cqpim') . '</div>';
	}?>
	<?php $screen = get_current_screen(); ?>
	<a class="cqpim_button cqpim_button_link font-white bg-blue block mt-10 rounded_2 save" href="#"><?php $screen->action == 'add' ? _e('Add Client', 'cqpim') : _e('Update Client', 'cqpim'); ?></a>
	<?php } ?>
	<?php if($client_user && current_user_can('cqpim_reset_client_passwords')) { ?>
		<a class="cqpim_button cqpim_button_link font-white bg-blue block mt-10 rounded_2 reset-password" href="#"><?php _e('Reset Client\'s Password', 'cqpim'); ?></a>
		<div id="password_reset_container" style="display:none">
			<div id="password_reset">
				<div style="padding:12px">
					<h3><?php _e('Reset Password', 'cqpim'); ?></h3>
					<p><?php _e('If you would like to reset the client\'s password, please enter and confirm the new password below.', 'cqpim'); ?> <br />
					<?php _e('This will be encrypted and saved to the database, you can however choose to send an email with the <br />
					new password before the encryption takes place.', 'cqpim'); ?></p>
					<input class="pass" type="password" id="new_password" name="new_password" placeholder="<?php _e('Enter new password', 'cqpim'); ?>" />
					<br /><br />
					<input class="pass" type="password" id="confirm_password" name="confirm_password" placeholder="<?php _e('Confirm new password', 'cqpim'); ?>" />
					<br /><br />
					<input type="checkbox" id="send_new_password" name="send_new_password" value="1" /> <?php _e('Send the client\'s new password by email', 'cqpim'); ?>
					<br /><br />
					<input class="pass" type="hidden" id="pass_type" name="pass_type" value="client" />
					<div id="password_messages"></div>
					<button class="cancel-colorbox cqpim_button font-red border-red op"><?php _e('Cancel', 'cqpim'); ?></button>
					<button id="reset_pass_ajax" class="cqpim_button font-green border-green right op" value="<?php echo $client_user; ?>"><?php _e('Reset Password', 'cqpim'); ?></button>
				</div>
			</div>
		</div>
	<?php } ?>
	<?php	
}
add_action( 'save_post', 'save_pto_contact_details_metabox_data' );
function save_pto_contact_details_metabox_data( $post_id ){
	if ( ! isset( $_POST['contact_details_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['contact_details_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'contact_details_metabox' ) )
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
	$looper = get_post_meta($post_id, 'looper', true);
	$looper = $looper?$looper:0;
	if(time() - $looper > 5) {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$client_details = $client_details&&is_array($client_details)?$client_details:array();
		$client_details['client_ref'] = isset($_POST['client_ref']) ? $_POST['client_ref'] : '';
		$client_details['client_company'] = isset($_POST['client_company']) ? $_POST['client_company'] : '';
		$client_details['client_contact'] = isset($_POST['client_contact']) ? $_POST['client_contact'] : '';
		$client_details['client_address'] = isset($_POST['client_address']) ? $_POST['client_address'] : '';
		$client_details['client_postcode'] = isset($_POST['client_postcode']) ? $_POST['client_postcode'] : '';
		$client_details['client_telephone'] = isset($_POST['client_telephone']) ? $_POST['client_telephone'] : '';
		$client_details['client_email'] = isset($_POST['client_email']) ? $_POST['client_email'] : '';
		$wp_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
		$user = get_user_by( 'id', $wp_user_id );
		if(empty($user->ID)) {
			if($client_details['client_email']) {
				$login = $client_details['client_email'];
				$passw = pto_random_string(10);
				$email = $client_details['client_email'];
				if ( !username_exists( $login )  && !email_exists( $email ) ) {
					unset($client_details['user_taken']);
					update_post_meta($post_id, 'pending', false);
					$user_id = wp_create_user( $login, $passw, $email );
					$user = new WP_User( $user_id );
					$user->set_role( 'cqpim_client' );
					$client_details['user_id'] = $user_id;
					$client_ids = array();
					$client_ids[] = $user_id;
					update_post_meta($post_id, 'client_ids', $client_ids);
					update_post_meta($post_id, 'client_details', $client_details);
					$auto_welcome = get_option('auto_welcome');
					if($auto_welcome == 1) {
						send_pto_welcome_email($post_id, $passw);
					}
					$user_data = array(
						'ID' => $user_id,
						'display_name' => $client_details['client_contact'],
						'first_name' => $client_details['client_contact'],
					);
					wp_update_user($user_data);
				} else {
					wp_die('You cannot use that email address because there is already a user in the system with that address. You should convert the existing user to a PTO Client in the WP Users page');
				}
			}
		} else {
			$client_details_old = get_post_meta($post_id, 'client_details', true);
			if($client_details['client_email'] != $client_details_old['client_email']) {
				$login = $client_details['client_email'];
				$email = $client_details['client_email'];	
				if ( !email_exists( $email ) ) {
					unset($client_details['user_taken']);	
					$user_data = array(
						'ID' => $user->ID,
						'display_name' => $client_details['client_contact'],
						'first_name' => $client_details['client_contact'],
						'user_email' => $client_details['client_email'],
					);
					wp_update_user($user_data);	
					update_post_meta($post_id, 'client_details', $client_details);
				} else {
					$client_details['client_email'] = $client_details_old['client_email'];
					$client_details['user_taken'] = true;
					update_post_meta($post_id, 'client_details', $client_details);
				}
			} else {
				$user_data = array(
					'ID' => $user->ID,
					'display_name' => $client_details['client_contact'],
					'first_name' => $client_details['client_contact'],
				);
				wp_update_user($user_data);	
				update_post_meta($post_id, 'client_details', $client_details);				
			}
		}
		$title = get_the_title($post_id);
		$company_token = '%%CLIENT_COMPANY%%';
		$client_token = '%%CLIENT_NUMBER%%';
		$client_details = get_post_meta($post_id, 'client_details', true);
		$client_company = $client_details['client_company'];
		$client_ref = $client_details['client_ref'];
		$title = str_replace($company_token, $client_company, $title);
		$title = str_replace($client_token, $client_ref, $title);		
		$client_updated = array(
			'ID' => $post_id,
			'post_title' => $title,
			'post_name' => $post_id,
		);	
		if ( ! wp_is_post_revision( $post_id ) ){
			remove_action('save_post', 'save_pto_contact_details_metabox_data');
			wp_update_post( $client_updated );
			add_action('save_post', 'save_pto_contact_details_metabox_data');
		}
		update_post_meta($post_id, 'looper', time());		
	}
}