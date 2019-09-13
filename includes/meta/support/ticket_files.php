<?php
function pto_support_files_metabox_callback( $post ) {
 	wp_nonce_field( 
	'support_files_metabox', 
	'support_files_metabox_nonce' );
	$all_attached_files = get_attached_media( '', $post->ID );
	if(empty($all_attached_files)) {
		echo '<p>' . __('There are no files uploaded to this ticket.', 'cqpim') . '</p>';
	} else {
		echo '<table class="cqpim_table"><thead><tr>';
		echo '<th>' . __('File Name', 'cqpim') . '</th><th>' . __('File Type', 'cqpim') . '</th><th>' . __('Uploaded', 'cqpim') . '</th><th>' . __('Uploaded By', 'cqpim') . '</th><th>' . __('Actions', 'cqpim') . '</th>';
		echo '</tr></thead><tbody>';
		foreach($all_attached_files as $file) {
			$file_object = get_post($file->ID);
			$link = get_the_permalink($file->ID);
			$user = get_user_by( 'id', $file->post_author );
			echo '<tr>';
			echo '<td><a href="' . $file->guid . '" target="_blank">' . $file->post_title . '</a></td>';
			echo '<td>' . $file->post_mime_type . '</td>';
			echo '<td>' . $file->post_date . '</td>';
			echo '<td>' . $user->display_name . '</td>';
			echo '<td><a href="' . $file->guid . '" download="' . $file->post_title . '" class="download_file cqpim_button cqpim_small_button border-green font-green op" value="' . $file->ID . '"><i class="fa fa-download" aria-hidden="true"></i></a> <button class="delete_file cqpim_button cqpim_small_button font-red border-red op" data-id="' . $file->ID . '" value=""><i class="fa fa-trash"></i></button></td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}
}