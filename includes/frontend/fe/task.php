<?php
include('header.php');
$looper = get_post_meta($post->ID, 'looper', true);
$looper = $looper?$looper:0;
if(time() - $looper > 5) {
	$user = wp_get_current_user();
	$client_logs = get_post_meta($client_id, 'client_logs', true);
	if(empty($client_logs)) {
		$client_logs = array();
	}
	$now = current_time('timestamp');
	$title = get_the_title();
	$title = str_replace('Protected:', '', $title);
	$client_logs[$now] = array(
		'user' => $user->ID,
		'page' => sprintf(__('Task - %1$s', 'cqpim'), $title)
	);
	update_post_meta($client_id, 'client_logs', $client_logs);
	update_post_meta($post->ID, 'looper', time());
}
$client_details = get_post_meta($assigned, 'client_details', true);
$client_ids = get_post_meta($assigned, 'client_ids', true);
$ppid = get_post_meta($post->ID, 'project_id', true); 
$project_details = get_post_meta($ppid, 'project_details', true);
$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
if($assigned == $client_id) { ?>
	<div class="masonry-grid">
		<div class="grid-sizer"></div>
		<div class="cqpim-dash-item-double grid-item">
			<div class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<span class="caption-subject font-green-sharp sbold"><?php _e('Task Details', 'cqpim'); ?> </span>
					</div>
				</div>
				<?php
				$pid = get_post_meta($post->ID, 'project_id', true);
				$mid = get_post_meta($post->ID, 'milestone_id', true);
				$owner = get_post_meta($post->ID, 'owner', true);
				$client_check = preg_replace('/[0-9]+/', '', $owner);
				if($client_check == 'C') {
					$client = true;
				} else {
					$client = false;
				}
				if($owner) {
					if($client == true) {
						$id = preg_replace("/[^0-9,.]/", "", $owner);
						$client_object = get_user_by('id', $id);
						$task_owner = $client_object->display_name;
					} else {
						$team_details = get_post_meta($owner, 'team_details', true);
						$team_name = isset($team_details['team_name']) ? $team_details['team_name']: '';
						if(!empty($team_name)) {
							$task_owner = $team_name;
						}
					}
				} else {
					$task_owner = '';
				}
				$task_details = get_post_meta($post->ID, 'task_details', true);
				$task_watchers = get_post_meta($post->ID, 'task_watchers', true);
				$task_description = isset($task_details['task_description']) ? $task_details['task_description'] : '';
				$task_status = isset($task_details['status']) ? $task_details['status'] : '';
				$task_priority = isset($task_details['task_priority']) ? $task_details['task_priority'] : '';
				$task_start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
				$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
				if(is_numeric($task_start)) { $task_start = date(get_option('cqpim_date_format'), $task_start); } else { $task_start = $task_start; }
				if(is_numeric($task_deadline)) { $task_deadline = date(get_option('cqpim_date_format'), $task_deadline); } else { $task_deadline = $task_deadline; }
				$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : '';
				$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '';
				echo '<p><strong>' . __('Description', 'cqpim') . ':</strong></p>';
				echo wpautop($task_description); 
				?>
				<div class="">
					<p><strong>
					<?php 
					_e('Assigned To', 'cqpim');
					echo ':</strong> '							
					?>								
					<select id="task_owner" name="task_owner">
						<?php 
							$contribs = get_post_meta($pid, 'project_contributors', true);
							if(!empty($contribs)) { ?>
								<optgroup label="<?php _e('Team Members', 'cqpim'); ?>">
								<?php foreach($contribs as $contrib) {
									$team_details = get_post_meta($contrib['team_id'], 'team_details', true);
									if($owner == $contrib['team_id']) { $selected = 'selected="selected"'; } else { $selected = ''; }
									echo '<option value="' . $contrib['team_id'] . '" ' . $selected . '>' . $team_details['team_name'] . '</option>';
								} ?>
								</optgroup>
							<?php }
						?>
						<optgroup label="<?php _e('Client', 'cqpim'); ?>">
							<?php foreach($client_ids as $id) { ?>
								<?php 
								$client = get_user_by('id', $id); 
								if($owner == 'C' . $client->ID) { $selected = 'selected="selected"'; } else { $selected = ''; }
								if(!empty($client)) {?>
								<option value="C<?php echo $client->ID; ?>" <?php echo $selected; ?>><?php echo $client->display_name; ?></option>
							<?php } } ?>
						</optgroup>
					</select>	
					</p>
				</div>
				<div class="clear"></div>
				<div class="">
					<p><strong>
					<?php 
					_e('Task Status', 'cqpim'); 
					if($task_status == 'pending') { $task_status = __('Pending', 'cqpim'); } 
					if($task_status == 'progress') { $task_status = __('In Progress', 'cqpim'); } 
					if($task_status == 'complete') { $task_status = __('Complete', 'cqpim'); } 
					if($task_status == 'on_hold') { $task_status = __('On Hold', 'cqpim'); } 
					echo ':</strong> ' . ucwords($task_status);								
					?>
					</p>
				</div>
				<div class="">
					<p><strong>
					<?php 
					_e('Task Priority', 'cqpim');  
					if($task_priority == 'normal') { $task_priority = __('Normal', 'cqpim'); } 
					if($task_priority == 'low') { $task_priority = __('Low', 'cqpim'); } 
					if($task_priority == 'high') { $task_priority = __('High', 'cqpim'); } 
					if($task_priority == 'immediate') { $task_priority = __('Immediate', 'cqpim'); } 
					echo ':</strong> ' . ucwords($task_priority);									
					?>
					</p>
				</div>
				<div class="clear"></div>
				<div class="">
					<p><strong>
					<?php 
					_e('Start Date', 'cqpim');  
					echo ':</strong> ' . ucwords($task_start);								
					?>
					</p>
				</div>
				<div class="">
					<p><strong>
					<?php 
					_e('Deadline', 'cqpim');  
					echo ':</strong> ' . ucwords($task_deadline);								
					?>
					</p>
				</div>
				<div class="clear"></div>
				<div class="">
					<p><strong>
					<?php 
					_e('Estimated Time (Hours)', 'cqpim'); 
					echo ':</strong> ' . ucwords($task_est_time);								
					?>
					</p>
				</div>
				<div class="">
					<p><strong>
					<?php 
					_e('Percentage Complete', 'cqpim');
					echo ':</strong> ' . ucwords($task_pc) . '%';								
					?>
					</p>	
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="cqpim-dash-item-triple grid-item">
			<div class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<span class="caption-subject font-green-sharp sbold"><?php _e('Task Messages', 'cqpim'); ?></span>
					</div>
				</div>
				<?php
				$string = pto_random_string(10);
				$messages = get_post_meta($post->ID, 'task_messages', true);
				if(empty($messages)) {
					echo '<p>' . __('No messages to show', 'cqpim') . '</p>';
				} else { ?>
					<div style="max-height:500px; overflow:auto">
						<ul class="project_summary_progress" style="margin:0">
						<?php $messages = array_reverse($messages);
						foreach($messages as $key => $message) { 
							$user = get_user_by('id', $message['author']);
							$email = $user->user_email;
							$size = 80;		
							?>
							<li style="margin-bottom:0">
								<div class="timeline-entry">
									<?php 
									$avatar = get_option('cqpim_disable_avatars');
									if(empty($avatar)) {
										echo '<div class="update-who">';
										echo get_avatar( $user->ID, 60, '', false, array('force_display' => true) );
										echo '</div>';
									} ?>
									<?php if(empty($avatar)) { ?>
										<div class="update-data">
									<?php } else { ?>
										<div style="width:100%; float:none" class="update-data">
									<?php } ?>
										<div class="timeline-body-arrow"> </div>
										<div class="timeline-by font-blue-madison sbold">
											<?php echo $message['by']; ?>
										</div>
										<div class="clear"></div>
										<div class="timeline-update font-grey-cascade"><?php echo wpautop($message['message']); ?></div>
										<div class="clear"></div>
										<div class="timeline-date font-grey-cascade"><?php echo date(get_option('cqpim_date_format') . ' H:i', $message['date']); ?></div>
									</div>
									<div class="clear"></div>
								</div>
							</li>
						<?php } ?>	
						</ul>
					</div>
				<?php } ?>
				<div class="cqpim_block_title">
					<div class="caption">
						<span class="caption-subject font-green-sharp sbold"><?php _e('Update Task', 'cqpim'); ?></span>
					</div>
				</div>				
				<?php
				$data = get_option('cqpim_custom_fields_task');
				$data = str_replace('\"', '"', $data);
				if(!empty($data)) {
					$form_data = json_decode($data);
					$fields = $form_data;
				}
				$values = get_post_meta($post->ID, 'custom_fields', true);
				if(!empty($fields)) {
					echo '<div id="cqpim-custom-fields">';
					foreach($fields as $field) {
						$classname = isset($field->className) ? $field->className : '';
						$value = isset($values[$field->name]) ? $values[$field->name] : '';
						$id = strtolower($field->label);
						$id = str_replace(' ', '_', $id);
						$id = str_replace('-', '_', $id);
						$id = preg_replace('/[^\w-]/', '', $id);
						if(!empty($field->required)) {
							$required = 'required';
							$ast = '<span style="color:#F00">*</span>';
						} else {
							$required = '';
							$ast = '';
						}
						echo '<div style="padding-bottom:12px" class="cqpim_form_item">';
						if($field->type != 'header') {
							echo '<label style="display:block; padding-bottom:5px" for="' . $id . '">' . $field->label . ' ' . $ast . '</label>';
						}
						if($field->type == 'header') {
							echo '<' . $field->subtype . ' class="cqpim-custom ' . $classname . '">' . $field->label . '</' . $field->subtype . '>';
						} elseif($field->type == 'text') {			
							echo '<input type="text" class="cqpim-custom ' . $classname . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
						} elseif($field->type == 'website') {
							echo '<input type="url" class="cqpim-custom ' . $classname . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
						} elseif($field->type == 'number') {
							echo '<input type="number" class="cqpim-custom ' . $classname . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
						} elseif($field->type == 'textarea') {
							echo '<textarea class="cqpim-custom ' . $classname . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '">' . $value . '</textarea>';
						} elseif($field->type == 'date') {
							echo '<input class="cqpim-custom ' . $classname . ' datepicker" type="text" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
						} elseif($field->type == 'email') {
							echo '<input type="email" class="cqpim-custom ' . $classname . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
						} elseif($field->type == 'checkbox-group') {
							if(!is_array($value)) {
								$value = array($value);
							}
							$options = $field->values;
							foreach($options as $option) {
								if(in_array($option->value, $value)) {
									$checked = 'checked="checked"';
								} else {
									$checked = '';
								}
								echo '<input type="checkbox" class="cqpim-custom ' . $classname . '" value="' . $option->value . '" name="' . $field->name . '" ' . $checked . ' /> ' . $option->label . '<br />';
							}
						} elseif($field->type == 'radio-group') {
							$options = $field->values;
							foreach($options as $option) {
								if($value == $option->value) {
									$checked = 'checked="checked"';
								} else {
									$checked = '';
								}
								echo '<input type="radio" class="cqpim-custom ' . $classname . '" value="' . $option->value . '" name="' . $field->name . '" ' . $required . ' ' . $checked . ' /> ' . $option->label . '<br />';
							}
						} elseif($field->type == 'select') {
							$options = $field->values;
							echo '<select class="cqpim-custom ' . $classname . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '">';
								foreach($options as $option) {	
									if($value == $option->value) {
										$checked = 'selected="selected"';
									} else {
										$checked = '';
									}
									echo '<option value="' . $option->value . '" ' . $checked . '>' . $option->label . '</option>';
								}
							echo '</select>';
						}
						if(!empty($field->other) && $field->other == 1) {
							echo '<br />';
							echo __('Other:', 'cqpim') . '<input class="cqpim-custom " style="width:100%" type="text" id="' . $id . '_other" name="custom-field[' . $field->name . '_other]" />';
						}
						if(!empty($field->description)) {
							echo '<span class="cqpim-field-description">' . $field->description . '</span>';
						}
						echo '</div>';
					}
					echo '</div>';
				}
				?>
				<h4><?php _e('Upload Files', 'cqpim'); ?></h4>
				<input type="hidden" id="file_task_id" name="file_task_id" value="<?php echo $post->ID; ?>" />
				<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
				<div id="upload_attachments"></div>
				<div class="clear"></div>
				<input type="hidden" name="image_id" id="upload_attachment_ids">
				<?php echo '<h4>' . __('Add Message', 'cqpim') . '</h4>'; ?>
				<textarea id="add_task_message" name="add_task_message"></textarea>
				<a href="#" id="update_task" class="cqpim_button font-white bg-blue mt-20 right op rounded_2"><?php _e('Update Task', 'cqpim'); ?></a>
				<div class="clear"></div>							
			</div>
		</div>
		<?php $hide_time = get_post_meta($post->ID, 'hide_front', true);
		if(empty($hide_time)) { ?>
			<div class="cqpim-dash-item-double grid-item">
				<div class="cqpim_block">
					<div class="cqpim_block_title">
						<div class="caption">
							<span class="caption-subject font-green-sharp sbold"><?php _e('Time Entries', 'cqpim'); ?></span>
						</div>
					</div>
					<?php
					$time_spent = get_post_meta($post->ID, 'task_time_spent', true);
					if($time_spent) {
						$total = 0;
						echo '<ul class="time_spent">';
						foreach($time_spent as $key => $time) {
							$user = wp_get_current_user();
							$args = array(
								'post_type' => 'cqpim_teams',
								'posts_per_page' => -1,
								'post_status' => 'private'
							);
							$members = get_posts($args);
							foreach($members as $member) {
								$team_details = get_post_meta($member->ID, 'team_details', true);
								if($team_details['user_id'] == $user->ID) {
									$assigned = $member->ID;
								}
							}
							if($assigned == $time['team_id'] || current_user_can('cqpim_dash_view_all_tasks')) {
								$delete = ' - <a class="time_remove" href="#" data-key="'. $key .'" data-task="'. $post->ID .'">' . __('REMOVE', 'cqpim') . '</a>';
							} else {
								$delete = '';
							}
							echo '<li>' . $time['team'] . ' <span style="float:right" class="right"><strong>' . number_format((float)$time['time'], 2, '.', '') . ' ' . __('HOURS', 'cqpim') . '</strong> ' . $delete . '</span></li>';
							$total = $total + $time['time'];
						}
						echo '</ul>';
						$total = str_replace(',','.', $total);
						$time_split = explode('.', $total);
						if(!empty($time_split[1])) {
							$minutes = '0.' . $time_split[1];
							$minutes = $minutes * 60;
							$minutes = number_format((float)$minutes, 0, '.', '');
						} else {
							$minutes = '0';
						}
						if($time_split[0] > 1) {
							$hours  = 'hours';
						} else {
							$hours = 'hour';
						}
						echo '<br /><span><strong>TOTAL: ' . number_format((float)$total, 2, '.', '') . ' ' . __('hours', 'cqpim') . '</strong> (' . $time_split[0] . ' ' . $hours . ' + ' . $minutes . ' ' . __('minutes', 'cqpim') . ')</span> <div id="ajax_spinner_remove_time_'. $post->ID .'" class="ajax_spinner" style="display:none"></div>';
					} else {
						echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('This task does not have any time assigned to it', 'cqpim') . '</div>';
					}
					?>
				</div>
			</div>
		<?php } ?>
		<div class="cqpim-dash-item-double grid-item">
			<div class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<span class="caption-subject font-green-sharp sbold"><?php _e('Task Files', 'cqpim'); ?></span>
					</div>
				</div>
				<div id="uploaded_files">
				<?php 
				$all_attached_files = get_attached_media( '', $post->ID );
				if(!$all_attached_files) {
					echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('There are no files uploaded to this task.', 'cqpim') . '</div>';
				} else {
					echo '<br /><table class="cqpim_table"><thead><tr>';
					echo '<th>' . __('File Name', 'cqpim') . '</th><th>' . __('Actions', 'cqpim') . '</th>';
					echo '</tr></thead><tbody>';
					foreach($all_attached_files as $file) {
						$file_object = get_post($file->ID);
						$link = get_the_permalink($file->ID);
						$user = get_user_by( 'id', $file->post_author );
						echo '<tr>';
						echo '<td><a class="cqpim-link" href="' . $file->guid . '" download="' . $file->post_title . '">' . $file->post_title . '</a><p>' . __('Uploaded on', 'cqpim') . ' ' . $file->post_date . ' ' . __('by', 'cqpim') . ' ' . $user->display_name . '</p></td>';
						echo '<td><a href="' . $file->guid . '" download="' . $file->post_title . '" class="cqpim_button cqpim_small_button border-green font-green op" value="' . $file->ID . '"><i class="fa fa-download" aria-hidden="true"></i></a></td>';
						echo '</tr>';
					}
					echo '</tbody></table>';
				}
				?>
				</div>
			</div>
		</div>					
	</div>
	<?php } else {
		echo '<h1 style="margin-top:0">' . __('ACCESS DENIED', 'cqpim') . '</h1>';
	} ?>
<?php include('footer.php'); ?>