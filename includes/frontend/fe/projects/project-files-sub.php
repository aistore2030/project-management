<?php
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if(empty($client_logs)) {
	$client_logs = array();
}
$title = get_the_title();
$title = str_replace('Private:', '', $title);
$now = current_time('timestamp');
$client_logs[$now] = array(
	'user' => $user->ID,
	'page' => sprintf(__('Project %1$s - %2$s (Files Page)', 'cqpim'), get_the_ID(), $title)
);
update_post_meta($assigned, 'client_logs', $client_logs);
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-full grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php _e('Project Files', 'cqpim'); ?></span>
				</div>	
			</div>
			<?php
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
					echo '<p style="padding:20px">' . __('There are no files uploaded to this project.', 'cqpim') . '</p>';
				} else {
					echo '<table class="cqpim_table files"><thead><tr>';
					echo '<th>' . __('File Name', 'cqpim') . '</th><th>' . __('Related Task', 'cqpim') . '</th><th>' . __('File Type', 'cqpim') . '</th><th>' . __('Uploaded', 'cqpim') . '</th><th>' . __('Uploaded By', 'cqpim') . '</th><th>' . __('Actions', 'cqpim') . '</th>';
					echo '</tr></thead><tbody>';
					foreach($all_attached_files as $file) {
						$file_object = get_post($file->ID);
						$url = get_the_permalink($file->ID);
						$parent = $file->post_parent;
						$parent_title = get_the_title($parent);
						$parent_title = str_replace('Protected: ', '', $parent_title);
						$parent_url = get_the_permalink($parent);
						$user = get_user_by( 'id', $file->post_author );
						echo '<tr>';
						echo '<td><span class="nodesktop"><strong>' . __('Title', 'cqpim') . '</strong>: </span> <a class="cqpim-link" href="' . $file->guid . '" download="' . $file->post_title . '">' . $file->post_title . '</a></td>';
						echo '<td><span class="nodesktop"><strong>' . __('Task', 'cqpim') . '</strong>: </span> <a class="cqpim-link" href="' . $parent_url . '">' . $parent_title . '</a></td>';
						echo '<td><span class="nodesktop"><strong>' . __('Type', 'cqpim') . '</strong>: </span> ' . $file->post_mime_type . '</td>';
						echo '<td><span class="nodesktop"><strong>' . __('Added', 'cqpim') . '</strong>: </span> ' . $file->post_date . '</td>';
						echo '<td><span class="nodesktop"><strong>' . __('Added By', 'cqpim') . '</strong>: </span> ' . $user->display_name . '</td>';
						echo '<td><a href="' . $file->guid . '" download="' . $file->post_title . '" class="cqpim_button cqpim_small_button border-green font-green op" value="' . $file->ID . '"><i class="fa fa-download" aria-hidden="true"></i></a></td>';
						echo '</tr>';
					}
					echo '</tbody></table>';
				}
			?>
			<br />
			<h3><?php _e('Upload Files', 'cqpim'); ?></h3>
			<form id="project_fe_files">
				<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
				<div id="upload_attachments"></div>
				<div class="clear"></div>
				<br />
				<select id="task_id">
					<?php
					$ordered = array();
					$project_elements = get_post_meta($post->ID, 'project_elements', true);
					if(empty($project_elements)) {
						echo '<option value="0">' . __('There are no tasks available to add this file to.', 'cqpim') . '</option>';
					} else {
						echo '<option value="0">' . __('Choose a Task', 'cqpim') . '</option>';
						foreach($project_elements as $key => $element) {
							$weight = isset($element['weight']) ? $element['weight'] : '';
							$ordered[$weight] = $element;
						}
						ksort($ordered);
						foreach($ordered as $key => $element) {
							echo '<optgroup label="' . $element['title'] . '">';
								$args = array(
									'post_type' => 'cqpim_tasks',
									'posts_per_page' => -1,
									'meta_key' => 'milestone_id',
									'meta_value' => $element['id'],
									'orderby' => 'date',
									'order' => 'ASC'
								);
								$tasks = get_posts($args);
								$ordered = array();
								foreach($tasks as $task) {
									$task_details = get_post_meta($task->ID, 'task_details', true);
									$weight = isset($task_details['weight']) ? $task_details['weight'] : $wi;
									if(empty($task->post_parent)) {
										$ordered[$weight] = $task;
									}
								}
								ksort($ordered);
								foreach($ordered as $task) {							
									echo '<option value="' . $task->ID . '">' . __('TASK', 'cqpim') . ': ' . $task->post_title . '</option>';
									$args = array(
										'post_type' => 'cqpim_tasks',
										'posts_per_page' => -1,
										'meta_key' => 'milestone_id',
										'meta_value' => $element['id'],
										'post_parent' => $task->ID,
										'orderby' => 'date',
										'order' => 'ASC'
									);
									$subtasks = get_posts($args);
									if(!empty($subtasks)) {
										$subordered = array();
										foreach($subtasks as $subtask) {
											$task_details = get_post_meta($subtask->ID, 'task_details', true);
											$sweight = isset($task_details['weight']) ? $task_details['weight'] : $sti;
											$subordered[$sweight] = $subtask;
										}
										ksort($subordered);
										foreach($subordered as $subtask) {
											echo '<option value="' . $subtask->ID . '">' . __('SUBTASK', 'cqpim') . ': ' . $subtask->post_title . '</option>';
										}
									}
								}
							echo '</optgroup>';							
						}
					}
					?>
				</select>
				<br /><br />
				<input type="hidden" name="image_id" id="upload_attachment_ids" value="" />
				<input type="hidden" name="project_id" id="project_id" value="<?php echo $post->ID; ?>" />
				<input type="submit" id="client_fe_files_submit" class="cqpim_button font-white bg-blue rounded_2 op" value="Submit Files">
			</form>
		</div>
	</div>
</div>