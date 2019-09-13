<?php
function pto_project_files_metabox_callback( $post ) {
 	wp_nonce_field( 
	'project_files_metabox', 
	'project_files_metabox_nonce' ); 
	$all_attached_files = get_attached_media( '', $post->ID );
	$args = array(
		'post_type' => 'cqpim_tasks',
		'posts_per_page' => -1,
		'meta_key' => 'project_id',
		'meta_value' => $post->ID
	);
	$tasks = get_posts($args);
	foreach($tasks as $task) {
		$args = array(
			'post_parent' => $task->ID,
			'post_type' => 'attachment',
			'numberposts' => -1
		);
		$children = get_children($args);
		foreach($children as $child) {
			$all_attached_files[] = $child;
		}
	}
	if(!$all_attached_files) {
		echo '<p>' . __('There are no files uploaded to this project.', 'cqpim') . '</p>';
	} else {
		echo '<table class="datatable_style dataTable"><thead><tr>';
		echo '<th>' . __('File Name', 'cqpim') . '</th><th>' . __('Related Task', 'cqpim') . '</th><th>' . __('File Type', 'cqpim') . '</th><th>' . __('Uploaded', 'cqpim') . '</th><th>' . __('Uploaded By', 'cqpim') . '</th><th>' . __('Actions', 'cqpim') . '</th>';
		echo '</tr></thead><tbody>';
		foreach($all_attached_files as $file) {
			$file_object = get_post($file->ID);
			$url = get_the_permalink($file->ID);
			$parent = $file->post_parent;
			$parent_title = get_the_title($parent);
			$parent_title = str_replace('Protected: ', '', $parent_title);
			$parent_url = get_edit_post_link($parent);
			$user = get_user_by( 'id', $file->post_author );
			echo '<tr>';
			echo '<td><span class="cqpim_mobile">' . __('File Name:', 'cqpim') . '</span> <a class="cqpim-link" href="' . $file->guid . '">' . $file->post_title . '</a></td>';
			echo '<td><span class="cqpim_mobile">' . __('Task:', 'cqpim') . '</span> <a class="cqpim-link" href="' . $parent_url . '">' . $parent_title . '</a></td>';
			echo '<td><span class="cqpim_mobile">' . __('Type:', 'cqpim') . '</span> ' . $file->post_mime_type . '</td>';
			echo '<td><span class="cqpim_mobile">' . __('Uploaded:', 'cqpim') . '</span> ' . $file->post_date . '</td>';
			echo '<td><span class="cqpim_mobile">' . __('User:', 'cqpim') . '</span> ' . $user->display_name . '</td>';
			echo '<td><a href="' . $file->guid . '" download="' . $file->post_title . '" class="cqpim_button cqpim_small_button border-green font-green op" value="' . $file->ID . '"><i class="fa fa-download" aria-hidden="true"></i></a> <button class="delete_file cqpim_button cqpim_small_button border-red font-red op" data-id="' . $file->ID . '" value=""><i class="fa fa-trash" aria-hidden="true"></i></button></td>';
			echo '</tr>';
		}
		echo '</tbody></table>'; ?>
		<div class="clear"></div>		
		<?php
	}	
}
add_action( 'save_post', 'save_pto_project_files_metabox_data' );
function save_pto_project_files_metabox_data( $post_id ){
	if ( ! isset( $_POST['project_files_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['project_files_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'project_files_metabox' ) )
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
	$duplicate = get_post_meta($post_id, 'duplicate', true);
	$duplicate = isset($duplicate) ? $duplicate : 0;
	$now = time();
	$diff = $now - $duplicate;
	if($diff > 3) {
		if( isset( $_POST['delete_file'] ) ){
			$att_to_delete = $_POST['delete_file'];
			foreach ( $att_to_delete as $key => $attID ) {
				$file = get_post($attID);
				$current_user = wp_get_current_user();
				$project_progress = get_post_meta($post_id, 'project_progress', true);
				$project_progress[] = array(
					'update' => sprintf(__('File Deleted: %1$s', 'cqpim'), $file->post_title),
					'date' => current_time('timestamp'),
					'by' => $current_user->display_name
				);
				update_post_meta($post_id, 'project_progress', $project_progress );
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
		$attachments_to_add = isset($_POST['meta_image']) ? $_POST['meta_image'] : '';
		if(!empty($attachments_to_add)) {
			$image_urls = explode(',', $attachments_to_add);
			$image_urls = array_filter($image_urls);
			foreach($image_urls as $image_url) {
				$image_id = pto_get_image_id_by_url ( $image_url );
				$file = get_post($image_id);
				$current_user = wp_get_current_user();
				$project_progress = get_post_meta($post_id, 'project_progress', true);
				$project_progress[] = array(
					'update' => sprintf(__('File Uploaded: %1$s', 'cqpim'), $file->post_title),
					'date' => current_time('timestamp'),
					'by' => $current_user->display_name
				);
				update_post_meta($post_id, 'project_progress', $project_progress );
				global $wpdb;
				$wpdb->query(
					"
					UPDATE $wpdb->posts 
					SET post_parent = $post_id
					WHERE ID = $image_id
					AND post_type = 'attachment'
					"
				);
				$meta = wp_get_attachment_metadata($image_id);
				if (!$meta) {
					$file = get_attached_file($image_id);
					if (!empty($file)) {
						$info = getimagesize($file);
						$meta = array (
							'width' => $info[0],
							'height' => $info[1],
							'hwstring_small' => "height='{$info[1]}' width='{$info[0]}'",
							'file' => basename($file),
							'sizes' => array(),
							'image_meta' => array(),
						);
						update_post_meta($id, '_wp_attachment_metadata', $meta);
					}
				}		
			}	
		}
		update_post_meta($post_id, 'duplicate', time());
	}
}