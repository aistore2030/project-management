<?php
function pto_contact_prefs_metabox_callback( $post ) {
 	wp_nonce_field( 
	'contact_prefs_metabox', 
	'contact_prefs_metabox_nonce' );
	$notifications = get_post_meta($post->ID, 'client_notifications', true);
	$no_tasks = isset($notifications['no_tasks']) ? $notifications['no_tasks']: 0;
	$no_tasks_comment = isset($notifications['no_tasks_comment']) ? $notifications['no_tasks_comment']: 0;
	$no_tickets = isset($notifications['no_tickets']) ? $notifications['no_tickets']: 0;
	$no_tickets_comment = isset($notifications['no_tickets_comment']) ? $notifications['no_tickets_comment']: 0;
	$no_bugs = isset($notifications['no_bugs']) ? $notifications['no_bugs']: 0;
	$no_bugs_comment = isset($notifications['no_bugs_comment']) ? $notifications['no_bugs_comment']: 0;
	?>
	<p><?php _e('Email Preferences (Main Contact):', 'cqpim'); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php _e('By default, clients receive an email notification whenever a task or ticket is updated. You can use these settings to disable those notifications or to limit them to be sent only when a new comment has been added in the task or ticket. You can configure these settings for additional client contacts in the Client Contacts box.', 'cqpim'); ?>"></i></p>		
	<p><strong><?php _e('Tasks', 'cqpim'); ?></strong></p>
	<input type="checkbox" name="no_tasks" id="no_tasks" value="1" <?php if($no_tasks == 1) { echo 'checked="checked"'; } ?> /> <?php _e('Do not send task update emails.', 'cqpim'); ?><br />
	<input type="checkbox" name="no_tasks_comment" id="no_tasks_comment" value="1" <?php if($no_tasks_comment == 1) { echo 'checked="checked"'; } ?> <?php if($no_tasks == 1) { echo 'disabled'; } ?> /> <?php _e('Notify new comments only.', 'cqpim'); ?>
	<p><strong><?php _e('Support Tickets', 'cqpim'); ?></strong></p>
	<input type="checkbox" name="no_tickets" id="no_tickets" value="1" <?php if($no_tickets == 1) { echo 'checked="checked"'; } ?>  /> <?php _e('Do not send ticket update emails.', 'cqpim'); ?><br />
	<input type="checkbox" name="no_tickets_comment" id="no_tickets_comment" value="1" <?php if($no_tickets_comment == 1) { echo 'checked="checked"'; } ?> <?php if($no_tickets == 1) { echo 'disabled'; } ?> /> <?php _e('Notify new comments only.', 'cqpim'); ?>
	<?php if(pto_check_addon_status('bugs')) { ?>
		<p><strong><?php _e('Bugs', 'cqpim'); ?></strong></p>
		<input type="checkbox" name="no_bugs" id="no_bugs" value="1" <?php if($no_bugs == 1) { echo 'checked="checked"'; } ?>  /> <?php _e('Do not send bug update emails.', 'cqpim'); ?><br />
		<input type="checkbox" name="no_bugs_comment" id="no_bugs_comment" value="1" <?php if($no_bugs_comment == 1) { echo 'checked="checked"'; } ?> <?php if($no_bugs == 1) { echo 'disabled'; } ?> /> <?php _e('Notify new comments only.', 'cqpim'); ?>
	<?php }
}
add_action( 'save_post', 'save_pto_contact_prefs_metabox_data' );
function save_pto_contact_prefs_metabox_data( $post_id ){
	if ( ! isset( $_POST['contact_prefs_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['contact_prefs_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'contact_prefs_metabox' ) )
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
	$no_tasks = isset($_POST['no_tasks']) ? $_POST['no_tasks']: 0;
	$no_tasks_comment = isset($_POST['no_tasks_comment']) ? $_POST['no_tasks_comment']: 0;
	$no_tickets = isset($_POST['no_tickets']) ? $_POST['no_tickets']: 0;
	$no_tickets_comment = isset($_POST['no_tickets_comment']) ? $_POST['no_tickets_comment']: 0;
	$no_bugs = isset($_POST['no_bugs']) ? $_POST['no_bugs']: 0;
	$no_bugs_comment = isset($_POST['no_bugs_comment']) ? $_POST['no_bugs_comment']: 0;
	$client_notifications = array(
		'no_tasks' => $no_tasks,
		'no_tasks_comment' => $no_tasks_comment,
		'no_tickets' => $no_tickets,
		'no_tickets_comment' => $no_tickets_comment,
		'no_bugs' => $no_bugs,
		'no_bugs_comment' => $no_bugs_comment,
	);
	update_post_meta($post_id, 'client_notifications', $client_notifications);
}