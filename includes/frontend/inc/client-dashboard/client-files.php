<br />
<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-file font-green-sharp" aria-hidden="true"></i>
			<span class="caption-subject font-green-sharp sbold"> <?php _e('Client Files', 'cqpim'); ?></span>
		</div>
	</div>
	<?php 
	$user = wp_get_current_user();
	$client_logs = get_post_meta($assigned, 'client_logs', true);
	if(empty($client_logs)) {
		$client_logs = array();
	}
	$now = current_time('timestamp');
	$client_logs[$now] = array(
		'user' => $user->ID,
		'page' => __('Client Dashboard Files Page', 'cqpim')
	);
	update_post_meta($assigned, 'client_logs', $client_logs);
	$client_details = get_post_meta($assigned, 'client_details', true);
	$fe_files = get_post_meta($assigned, 'fe_files', true);
	$all_attached_files = get_attached_media( '', $assigned );
	$fe_files = get_post_meta($assigned, 'fe_files', true); ?>
	<div id="cqpim_backend_quote">
		<?php
		if(!$all_attached_files) {
			echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('There are no client files available to view.', 'cqpim') . '</div>';
		} else {
			$sort = "[[3, 'desc']]";
			echo '<table id="client_files_table" class="datatable_style dataTable" data-ordering="' . $sort . '" data-rows="10"><thead><tr>';
			echo '<th>' . __('File Name', 'cqpim') . '</th><th>' . __('File Type', 'cqpim') . '</th><th>' . __('Uploaded', 'cqpim') . '</th><th>' . __('Uploaded By', 'cqpim') . '</th><th>' . __('Actions', 'cqpim') . '</th>';
			echo '</tr></thead><tbody>';
			foreach($all_attached_files as $file) {
				$file_object = get_post($file->ID);
				$link = get_the_permalink($file->ID);
				$path = get_attached_file($file->ID);
				$type = wp_check_filetype($path);
				$user = get_user_by( 'id', $file->post_author );				
				if($fe_files[$file->ID] == 1) {				
					echo '<tr>';
					echo '<td><a href="' . $file->guid . '" target="_blank">' . $file->post_title . '</a></td>';
					echo '<td>' . $type['ext'] . '</td>';
					echo '<td>' . $file->post_date . '</td>';
					echo '<td>' . $user->display_name . '</td>';
					echo '<td><a href="' . $file->guid . '" download="' . $file->post_title . '" class="cqpim_button cqpim_small_button border-green font-green" value="' . $file->ID . '"><i class="fa fa-download" aria-hidden="true"></i></a></td>';
					echo '</tr>';				
				}
			}
			echo '</tbody></table>';
		} ?>
		<br />
		<h3><?php _e('Upload Files', 'cqpim'); ?></h3>
		<form id="client_fe_files">
			<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
			<div id="upload_attachments"></div>
			<div class="clear"></div>
			<br />
			<input type="hidden" name="image_id" id="upload_attachment_ids" value="" />
			<input type="hidden" name="client_id" id="client_id" value="<?php echo $assigned; ?>" />
			<input type="submit" id="client_fe_files_submit" class="cqpim_button font-white bg-blue rounded_2 op" value="Submit Files">
		</form>
	</div>	
</div>