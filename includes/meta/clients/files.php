<?php
function pto_client_files_metabox_callback( $post ) {
 	wp_nonce_field( 
	'client_files_metabox', 
	'client_files_metabox_nonce' );
	$client_details = get_post_meta($post->ID, 'client_details', true);
	$fe_files = get_post_meta($post->ID, 'fe_files', true);
	$fe_files = $fe_files&&is_array($fe_files)?$fe_files:array();
	$all_attached_files = get_attached_media( '', $post->ID );
	if(!$all_attached_files) {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('There are no files attached to this client.', 'cqpim') . '</div>';
	} else {
		$sort = "[[3, 'desc']]";
		echo '<table id="client_files_table" class="datatable_style dataTable" data-ordering="' . $sort . '" data-rows="10"><thead><tr>';
		echo '<th>' . __('File Name', 'cqpim') . '</th><th>' . __('File Type', 'cqpim') . '</th><th>' . __('Uploaded', 'cqpim') . '</th><th>' . __('Uploaded By', 'cqpim') . '</th><th>' . __('Show in Dashboard', 'cqpim') . '</th><th>' . __('Actions', 'cqpim') . '</th>';
		echo '</tr></thead><tbody>';
		foreach($all_attached_files as $file) {
			if(!empty($fe_files[$file->ID])) {
				$checked = 1;
			} else {
				$checked = 0;
			}
			$file_object = get_post($file->ID);
			$link = get_the_permalink($file->ID);
			$path = get_attached_file($file->ID);
			$type = wp_check_filetype($path);
			$user = get_user_by( 'id', $file->post_author );
			echo '<tr>';
			echo '<td><a href="' . $file->guid . '" target="_blank">' . $file->post_title . '</a></td>';
			echo '<td>' . $type['ext'] . '</td>';
			echo '<td>' . $file->post_date . '</td>';
			echo '<td>' . $user->display_name . '</td>';
			echo '<td><input class="fe_file" type="checkbox" value="1" data-client="' . $post->ID . '" data-file="' . $file->ID . '" ' . checked($checked, 1, false) . '/></td>';
			echo '<td><a href="' . $file->guid . '" download="' . $file->post_title . '" class="cqpim_button cqpim_small_button border-green font-green" value="' . $file->ID . '"><i class="fa fa-download" aria-hidden="true"></i></a> <button class="delete_file cqpim_button cqpim_small_button border-red font-red" data-id="' . $file->ID . '" value=""><i class="fa fa-trash" aria-hidden="true"></i></button></td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	} ?>
	<br />
	<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
	<div id="upload_attachments"></div>
	<div class="clear"></div>
	<input type="hidden" name="image_id" id="upload_attachment_ids">
	<?php
}
add_action( 'save_post', 'save_pto_client_files_metabox_data' );
function save_pto_client_files_metabox_data( $post_id ){
	if ( ! isset( $_POST['client_files_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['client_files_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'client_files_metabox' ) )
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
	$attachments = isset($_POST['image_id']) ? $_POST['image_id'] : '';
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
		}
	}
	if( isset( $_POST['delete_file'] ) ){
		$att_to_delete = $_POST['delete_file'];
		foreach ( $att_to_delete as $key => $attID ) {
			$file = get_post($attID);
			$task_object = get_post($post_id);
			$task_link = '<a class="cqpim-link" href="' . get_the_permalink($post_id) . '">' . $task_object->post_title . '</a>';
			global $wpdb;
			$wpdb->query(
				"
				UPDATE $wpdb->posts 
				SET post_parent = ''
				WHERE ID = $attID
				AND post_type = 'attachment'
				"
			);
		}
	}	
}