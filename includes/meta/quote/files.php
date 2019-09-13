<?php
function pto_quote_files_metabox_callback( $post ) {
 	wp_nonce_field( 
	'quote_files_metabox', 
	'quote_files_metabox_nonce' );
	$all_attached_files = get_attached_media( '', $post->ID );
	if(!$all_attached_files) {
		echo '<p>' . __('There are no files attached to this quote.', 'cqpim') . '</p>';
	} else {
		echo '<table class="datatable_style dataTable"><thead><tr>';
		echo '<th>' . __('File Name', 'cqpim') . '</th><th>' . __('File Type', 'cqpim') . '</th><th>' . __('Uploaded', 'cqpim') . '</th><th>' . __('Uploaded By', 'cqpim') . '</th><th>' . __('Actions', 'cqpim') . '</th>';
		echo '</tr></thead><tbody>';
		foreach($all_attached_files as $file) {
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
			echo '<td><a href="' . $file->guid . '" download="' . $file->post_title . '" class="download_file cqpim_button cqpim_small_button border-green font-green op" value="' . $file->ID . '"><i class="fa fa-download" aria-hidden="true"></i></a> <button class="delete_file cqpim_button cqpim_small_button font-red border-red op" data-id="' . $file->ID . '" value=""><i class="fa fa-trash"></i></button></td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	} ?>
	<h4><?php _e('Upload Files', 'cqpim'); ?></h4>
	<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
	<div id="upload_attachments"></div>
	<div class="clear"></div>
	<input type="hidden" name="image_id" id="upload_attachment_ids">
	<?php
}
add_action( 'save_post', 'save_pto_quote_files_metabox_data' );
function save_pto_quote_files_metabox_data( $post_id ){
	if ( ! isset( $_POST['quote_files_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['quote_files_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'quote_files_metabox' ) )
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