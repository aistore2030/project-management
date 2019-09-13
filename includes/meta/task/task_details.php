<?php
function pto_task_details_metabox_callback( $post ) {
 	wp_nonce_field( 
	'task_details_metabox', 
	'task_details_metabox_nonce' );
	$pid = get_post_meta($post->ID, 'project_id', true);
	$mid = get_post_meta($post->ID, 'milestone_id', true);
	$project_details = get_post_meta($pid, 'project_details', true);
	$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
	$client_ids = get_post_meta($client_id, 'client_ids', true);
	$owner = get_post_meta($post->ID, 'owner', true);
	$task_details = get_post_meta($post->ID, 'task_details', true);
	$task_watchers = get_post_meta($post->ID, 'task_watchers', true);
	$task_description = isset($task_details['task_description']) ? $task_details['task_description'] : '';
	$task_status = isset($task_details['status']) ? $task_details['status'] : '';
	$task_priority = isset($task_details['task_priority']) ? $task_details['task_priority'] : '';
	$task_start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
	$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
	$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : '';
	$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '';
	$project_contributors = get_post_meta($pid, 'project_contributors', true); ?>
	<p class="underline"><strong><?php _e('Details', 'cqpim'); ?></strong></p>
	<p><?php _e('Description', 'cqpim'); ?></p>
	<textarea name="task_description"><?php echo $task_description; ?></textarea> 
	<br /><br />
	<div class="cqpim-meta-left">
		<p><?php _e('Task Status', 'cqpim'); ?></p>
		<select id="task_status" name="task_status">
			<option value="pending" <?php if($task_status == 'pending') { echo 'selected="selected"'; } ?>><?php _e('Pending', 'cqpim'); ?></option>
			<option value="on_hold" <?php if($task_status == 'on_hold') { echo 'selected="selected"'; } ?>><?php _e('On Hold', 'cqpim'); ?></option>
			<option value="progress" <?php if($task_status == 'progress') { echo 'selected="selected"'; } ?>><?php _e('In Progress', 'cqpim'); ?></option>
			<option value="complete" <?php if($task_status == 'complete') { echo 'selected="selected"'; } ?>><?php _e('Complete', 'cqpim'); ?></option>
		</select>
	</div>
	<div class="cqpim-meta-right">
		<p><?php _e('Task Priority', 'cqpim'); ?></p>
		<select id="task_priority" name="task_priority">
			<option value="normal" <?php if($task_priority == 'normal') { echo 'selected="selected"'; } ?>><?php _e('Normal', 'cqpim'); ?></option>
			<option value="low" <?php if($task_priority == 'low') { echo 'selected="selected"'; } ?>><?php _e('Low', 'cqpim'); ?></option>
			<option value="high" <?php if($task_priority == 'high') { echo 'selected="selected"'; } ?>><?php _e('High', 'cqpim'); ?></option>
			<option value="immediate" <?php if($task_priority == 'immediate') { echo 'selected="selected"'; } ?>><?php _e('Immediate', 'cqpim'); ?></option>
		</select>	
	</div>
	<div class="clear"></div>
	<div class="cqpim-meta-left">
		<p><?php _e('Start Date', 'cqpim'); ?></p>
		<input class="datepicker" type="text" name="task_start" value="<?php if(is_numeric($task_start)) { echo date(get_option('cqpim_date_format'), $task_start); } else { echo $task_start; } ?>" />
	</div>
	<div class="cqpim-meta-right">
		<p><?php _e('Deadline', 'cqpim'); ?></p>
		<input class="datepicker" type="text" name="task_deadline" value="<?php if(is_numeric($task_deadline)) { echo date(get_option('cqpim_date_format'), $task_deadline); } else { echo $task_deadline; } ?>" />	
	</div>
	<div class="clear"></div>
	<div class="cqpim-meta-left">
		<p><?php _e('Estimated Time (Hours)', 'cqpim'); ?></p>
		<input type="text" name="task_est_time" value="<?php echo $task_est_time; ?>" />
	</div>
	<div class="cqpim-meta-right">
		<p><?php _e('Percentage Complete', 'cqpim'); ?></p>
		<select id="task_pc" name="task_pc">
			<option value="0" <?php if($task_pc == '0') { echo 'selected="selected"'; } ?>><?php _e('0%', 'cqpim'); ?></option>
			<option value="10" <?php if($task_pc == '10') { echo 'selected="selected"'; } ?>><?php _e('10%', 'cqpim'); ?></option>
			<option value="20" <?php if($task_pc == '20') { echo 'selected="selected"'; } ?>><?php _e('20%', 'cqpim'); ?></option>
			<option value="30" <?php if($task_pc == '30') { echo 'selected="selected"'; } ?>><?php _e('30%', 'cqpim'); ?></option>
			<option value="40" <?php if($task_pc == '40') { echo 'selected="selected"'; } ?>><?php _e('40%', 'cqpim'); ?></option>
			<option value="50" <?php if($task_pc == '50') { echo 'selected="selected"'; } ?>><?php _e('50%', 'cqpim'); ?></option>
			<option value="60" <?php if($task_pc == '60') { echo 'selected="selected"'; } ?>><?php _e('60%', 'cqpim'); ?></option>
			<option value="70" <?php if($task_pc == '70') { echo 'selected="selected"'; } ?>><?php _e('70%', 'cqpim'); ?></option>
			<option value="80" <?php if($task_pc == '80') { echo 'selected="selected"'; } ?>><?php _e('80%', 'cqpim'); ?></option>
			<option value="90" <?php if($task_pc == '90') { echo 'selected="selected"'; } ?>><?php _e('90%', 'cqpim'); ?></option>
			<option value="100" <?php if($task_pc == '100') { echo 'selected="selected"'; } ?>><?php _e('100%', 'cqpim'); ?></option>
		</select>	
	</div>
	<div class="clear"></div><br />
	<?php
	$data = get_option('cqpim_custom_fields_task');
	$data = str_replace('\"', '"', $data);
	if(!empty($data)) {
		$form_data = json_decode($data);
		$fields = $form_data;
	}
	$values = get_post_meta($post->ID, 'custom_fields', true);
	if(!empty($fields)) {
		echo '<p class="underline"><strong>' . __('Custom Fields', 'cqpim') . '</strong></p>';
		echo '<div id="cqpim-custom-fields">';
		foreach($fields as $field) {
			$value = isset($values[$field->name]) ? $values[$field->name] : '';
			$id = strtolower($field->label);
			$id = str_replace(' ', '_', $id);
			$id = str_replace('-', '_', $id);
			$id = preg_replace('/[^\w-]/', '', $id);
			if(!empty($field->required) && $field->required == 1) {
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
				echo '<' . $field->subtype . ' class="' . $field->className . '">' . $field->label . '</' . $field->subtype . '>';
			} elseif($field->type == 'text') {			
				echo '<input type="text" class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'website') {
				echo '<input type="url" class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'number') {
				echo '<input type="number" class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'textarea') {
				echo '<textarea class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']">' . $value . '</textarea>';
			} elseif($field->type == 'date') {
				echo '<input class="' . $field->className . ' datepicker" type="text" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'email') {
				echo '<input type="email" class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
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
					echo '<input type="checkbox" class="' . $field->className . '" value="' . $option->value . '" name="custom-field[' . $field->name . '][]" ' . $checked . ' /> ' . $option->label . '<br />';
				}
			} elseif($field->type == 'radio-group') {
				$options = $field->values;
				foreach($options as $option) {
					if($value == $option->value) {
						$checked = 'checked="checked"';
					} else {
						$checked = '';
					}
					echo '<input type="radio" class="' . $field->className . '" value="' . $option->value . '" name="custom-field[' . $field->name . ']" ' . $required . ' ' . $checked . ' /> ' . $option->label . '<br />';
				}
			} elseif($field->type == 'select') {
				$options = $field->values;
				echo '<select class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']">';
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
				echo __('Other:', 'cqpim') . '<input style="width:100%" type="text" id="' . $id . '_other" name="custom-field[' . $field->name . '_other]" />';
			}
			if(!empty($field->description)) {
				echo '<span class="cqpim-field-description">' . $field->description . '</span>';
			}
			echo '</div>';
		}
		echo '</div>';
	}
	?>
	<br />
	<?php	
	$task_details = get_post_meta($post->ID, 'project_id', true);
	$parent_object = get_post($task_details);
	$parent_type = isset($parent_object->post_type) ? $parent_object->post_type : '';
	?>
	<?php if(!empty($task_details) && $parent_type == 'cqpim_project') { ?>
		<p class="underline"><strong><?php _e('Project / Milestone', 'cqpim'); ?></strong></p>
		<div class="cqpim-meta-left">
			<p><?php _e('Assigned Project', 'cqpim'); ?></p>
			<?php 					
			$args = array(
				'post_type' => 'cqpim_project',
				'posts_per_page' => -1,
				'post_status' => 'private',
			);
			$projects = get_posts($args);
			if(current_user_can('cqpim_view_all_projects')) { ?>
				<select id="task_project_id" name="task_project_id">
					<?php if($projects) { ?>
						<option value=""><?php _e('Do not assign to a project (Personal Task)', 'cqpim'); ?></option>
					<?php } else { ?>
						<option value=""><?php _e('Do not assign to a project (Personal Task)', 'cqpim'); ?></option>
						<option value=""><?php _e('No projects available', 'cqpim'); ?></option>
					<?php } ?>
					<?php foreach($projects as $project) { 
					$project_details = get_post_meta($project->ID, 'project_details', true);
					if(empty($project_details['closed'])) { ?>
						<option value="<?php echo $project->ID; ?>" <?php if($pid == $project->ID) { echo 'selected="selected"'; } ?>><?php echo $project->post_title; ?></option>
					<?php }
					} ?>
				</select>			
			<?php } else {
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
				} ?>
				<select id="task_project_id" name="task_project_id">
					<?php $options = ''; $i = 0; foreach($projects as $project) { 
					$project_details = get_post_meta($project->ID, 'project_details', true);
					$project_contributors = get_post_meta($project->ID, 'project_contributors', true);
					$project_contrib_ids = array();
					foreach($project_contributors as $contrib) {
						$project_contrib_ids[] = $contrib['team_id'];
					}
					if(!$project_details['closed']) { 
						if($pid == $project->ID) { $selected = 'selected="selected"'; } else { $selected = ''; }
						if(in_array($assigned, $project_contrib_ids)) {
						$options .= '<option value="' . $project->ID . '" ' . $selected . '>' . $project->post_title . '</option>';
					 $i++; }				
					}
					} 
					if($i != 0) { ?>
						<option value="0"><?php _e('Do not assign to a project (Personal Task)', 'cqpim'); ?></option>
						<?php echo $options; ?>
					<?php } else { ?>
						<option value="0"><?php _e('Do not assign to a project (Personal Task)', 'cqpim'); ?></option>
						<option value="0"><?php _e('No projects assigned to you', 'cqpim'); ?></option>
					<?php } ?>
				</select>		
			<?php } ?>
		</div>
		<div class="cqpim-meta-right">
			<p><?php _e('Assigned Milestone', 'cqpim'); ?></p>
			<select id="task_milestone_id" name="task_milestone_id">
				<?php
				$milestones = get_post_meta($pid, 'project_elements', true);
				if(!empty($milestones)) {
					foreach($milestones as $milestone) {
						if($mid == $milestone['id']) { $selected = 'selected="selected"'; } else { $selected = ''; }
						echo '<option value="' . $milestone['id'] . '" ' . $selected . '>' . $milestone['title'] . '</option>';
					}
				} else { ?>
				<option value=""><?php _e('No milestones available (Choose a project first)', 'cqpim'); ?></option>
				<?php } ?>			
			</select>
		</div>
		<br />
		<div class="clear"></div>
		<?php
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
		?>
		<br />
		<div class="cqpim-meta-left">
			<p class="underline"><strong><?php _e('Main Assignee', 'cqpim'); ?></strong></p>
			<p><?php _e('If you assign a task to a client you should add yourself as a Secondary Assignee to continue to receive updates.', 'cqpim'); ?></p>
			<select id="task_owner" name="task_owner">
				<option value="0"><?php _e('Choose...', 'cqpim'); ?></option>
				<optgroup label="<?php _e('Project Team Members', 'cqpim'); ?>">
				<?php if(empty($pid)) { ?>
					<option value="<?php echo $assigned; ?>"><?php _e('Me', 'cqpim'); ?></option>
				<?php } else {
					$contribs = get_post_meta($pid, 'project_contributors', true);
					if(!empty($contribs)) {
						foreach($contribs as $contrib) {
							$team_details = get_post_meta($contrib['team_id'], 'team_details', true);
							if($owner == $contrib['team_id']) { $selected = 'selected="selected"'; } else { $selected = ''; }
							echo '<option value="' . $contrib['team_id'] . '" ' . $selected . '>' . $team_details['team_name'] . '</option>';
						}
					} else {
						echo '<option value="' . $assigned . '">' . __('Me', 'cqpim') . '</option>';
					}
				}
				?>
				</optgroup>
				<optgroup label="<?php _e('Client', 'cqpim'); ?>">
					<?php foreach($client_ids as $id) { ?>
						<?php 
						$client = get_user_by('id', $id); 
						if($owner == 'C' . $client->ID) { $selected = 'selected="selected"'; } else { $selected = ''; }
						?>
						<option value="C<?php echo $client->ID; ?>" <?php echo $selected; ?>><?php echo $client->display_name; ?></option>
					<?php } ?>
				</optgroup>
			</select>
		</div>
		<div class="clear"></div>
		<?php
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
		if(!empty($pid)) {
			echo '<br />';
			echo '<p class="underline"><strong>' . __('Secondary Assignees', 'cqpim') . '</strong></p>';
			echo '<p>' . __('People other than the Assignee and Admins who can view/update this task and get notifications', 'cqpim') . '</p>';
			$args = array(
				'post_type' => 'cqpim_teams',
				'posts_per_page' => -1,
				'post_status' => 'private'
			);
			$members = get_posts($args);
			if(empty($task_watchers)) {
				$task_watchers = array();
			}
			$project_contributors = get_post_meta($pid, 'project_contributors', true);
			foreach($members as $member) {
				$team_details = get_post_meta($member->ID, 'team_details', true);
				if(in_array($member->ID, $task_watchers)) { $checked = 'checked="checked"'; } else { $checked = ''; }
				$project_contrib_ids = array();
				if(empty($project_contributors)) {
					$project_contributors = array();
				}
				foreach($project_contributors as $contrib) {
					$project_contrib_ids[] = $contrib['team_id'];
				}			
				if(!empty($project_contrib_ids) && in_array($member->ID, $project_contrib_ids)) {
					echo '<div class="task_watcher"><input type="checkbox" value="' . $member->ID . '" name="task_watchers[]" ' . $checked . ' /> ' . $team_details['team_name'] . '</div>'; 
				}
			}
		}
		?>
		<div class="clear"></div>
	<?php } else { ?>
		<?php
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
		?>
		<?php if(empty($task_details)) { ?>
			<?php 
			$user = wp_get_current_user();
			if(in_array('administrator', $user->roles) || in_array('cqpim_admin', $user->roles) || current_user_can('cqpim_assign_adhoc_tasks')) {
			?>
			<br />
			<div class="cqpim-meta-left">
				<p class="underline"><strong><?php _e('Main Assignee', 'cqpim'); ?></strong></p>
				<select id="task_owner" name="task_owner">
					<option value=""><?php _e('Choose...', 'cqpim'); ?></option>
					<?php foreach($members as $member) {
						$team_details = get_post_meta($member->ID, 'team_details', true);
						if($member->ID == $owner) { $selected = 'selected="selected"'; } else { $selected = ''; }
						echo '<option value="' . $member->ID . '" ' . $selected . '> ' . $team_details['team_name'] . '</option>'; 
					} ?>			
				</select>	
			</div>
			<div class="clear"></div>
			<?php
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
			echo '<br />';
			echo '<p class="underline"><strong>' . __('Secondary Assignees', 'cqpim') . '</strong></p>';
			echo '<p>' . __('People other than the Assignee and Admins who can view/update this task and get notifications', 'cqpim') . '</p>';
			$args = array(
				'post_type' => 'cqpim_teams',
				'posts_per_page' => -1,
				'post_status' => 'private'
			);
			$members = get_posts($args);
			if(empty($task_watchers)) {
				$task_watchers = array();
			}
			foreach($members as $member) {
				$team_details = get_post_meta($member->ID, 'team_details', true);
				if(in_array($member->ID, $task_watchers)) { $checked = 'checked="checked"'; } else { $checked = ''; }
				echo '<div class="task_watcher"><input type="checkbox" value="' . $member->ID . '" name="task_watchers[]" ' . $checked . ' /> ' . $team_details['team_name'] . '</div>'; 
			}			
			?>
			<?php } else { ?>
				<input type="hidden" name="task_owner" value="<?php echo $assigned; ?>" />
			<?php } ?>
		<?php } else { ?>
			<br />
			<div class="cqpim-meta-left">
				<p class="underline"><strong><?php _e('Main Assignee', 'cqpim'); ?></strong></p>
				<select id="task_owner" name="task_owner">
					<option value="0"><?php _e('Choose...', 'cqpim'); ?></option>
					<optgroup label="<?php _e('Support Team Members', 'cqpim'); ?>">
						<?php foreach($members as $member) {
							$team_details = get_post_meta($member->ID, 'team_details', true);
							$user = get_user_by('id', $team_details['user_id']);
							$caps = $user->allcaps;
							if(!empty($caps['cqpim_view_tickets'])) {
								if($member->ID == $owner) { $selected = 'selected="selected"'; } else { $selected = ''; }
								echo '<option value="' . $member->ID . '" ' . $selected . '> ' . $team_details['team_name'] . '</option>'; 
							}
						} ?>
					</optgroup>
				</select>
			</div>
			<div class="clear"></div>
			<?php
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
			if(!empty($pid)) {
				echo '<br />';
				echo '<p class="underline"><strong>' . __('Secondary Assignees', 'cqpim') . '</strong></p>';
				echo '<p>' . __('People other than the Assignee and Admins who can view/update this task and get notifications', 'cqpim') . '</p>';
				$args = array(
					'post_type' => 'cqpim_teams',
					'posts_per_page' => -1,
					'post_status' => 'private'
				);
				$members = get_posts($args);
				if(empty($task_watchers)) {
					$task_watchers = array();
				}
				foreach($members as $member) {
					$team_details = get_post_meta($member->ID, 'team_details', true);
					$user = get_user_by('id', $team_details['user_id']);
					$caps = $user->allcaps;
					if(!empty($caps['cqpim_view_tickets'])) {
						if(in_array($member->ID, $task_watchers)) { $checked = 'checked="checked"'; } else { $checked = ''; }
						echo '<div class="task_watcher"><input type="checkbox" value="' . $member->ID . '" name="task_watchers[]" ' . $checked . ' /> ' . $team_details['team_name'] . '</div>'; 
					}
				}			
			} ?>
		<?php } ?>
		<div class="clear"></div>
	<?php } ?>
	<?php if(!empty($task_details) && $parent_type == 'cqpim_support') { ?>
		<input type="hidden" name="task_project_id" value="<?php echo $pid; ?>" />
		<input type="hidden" name="task_milestone_id" value="<?php echo $mid; ?>" />
	<?php }
}
add_action( 'save_post', 'save_pto_task_details_metabox_data' );
function save_pto_task_details_metabox_data( $post_id ){
	if ( ! isset( $_POST['task_details_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['task_details_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'task_details_metabox' ) )
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
	$duplicate = $duplicate?$duplicate:0;
	$now = time();
	$diff = $now - $duplicate;
	if($diff > 3) {
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
		$task_details_old = get_post_meta($post_id, 'task_details', true);
		$task_details_new = get_post_meta($post_id, 'task_details', true);
		$task_details_new = $task_details_new?$task_details_new:array();
		$published = get_post_meta($post_id, 'published', true);
		$ticket_changes = array();
		if(!empty($_POST['custom-field'])) {
			update_post_meta($post_id, 'custom_fields', $_POST['custom-field']);
		}
		if(isset($_POST['task_status'])) {
			$task_details_new['status'] = $_POST['task_status'];
		}
		if(isset($_POST['task_priority'])) {
			$task_details_new['task_priority'] = $_POST['task_priority'];
		}	
		if(isset($_POST['task_start'])) {
			$task_details_new['task_start'] = $_POST['task_start'];
			$task_details_new['task_start'] = pto_convert_date($task_details_new['task_start']);
		}
		if(isset($_POST['task_deadline'])) {
			$task_details_new['deadline'] = $_POST['task_deadline'];
			$task_details_new['deadline'] = pto_convert_date($task_details_new['deadline']);
		}
		if(isset($_POST['task_est_time'])) {
			$task_details_new['task_est_time'] = $_POST['task_est_time'];
		}
		if(isset($_POST['task_pc'])) {
			$task_details_new['task_pc'] = $_POST['task_pc'];
		}
		if(isset($_POST['task_owner'])) {
			update_post_meta($post_id, 'owner', $_POST['task_owner']);
		}
		if(isset($_POST['task_project_id'])) {
			if($published == true) {
				$ppid = $_POST['task_project_id'];
				$current_user = wp_get_current_user();;
				$project_progress = get_post_meta($ppid, 'project_progress', true);
				$task_object = get_post($post_id);
				$task_title = $task_object->post_title;
				$text = sprintf(__('Task Updated: %1$s', 'cqpim'), $task_title );
				$project_progress[] = array(
					'update' => $text,
					'date' => current_time('timestamp'),
					'by' => $current_user->display_name
				);
				update_post_meta($ppid, 'project_progress', $project_progress );
			}
			update_post_meta($post_id, 'project_id', $_POST['task_project_id']);
		}
		if(isset($_POST['task_milestone_id'])) {
			update_post_meta($post_id, 'milestone_id', $_POST['task_milestone_id']);
		}
		$task_watchers = isset($_POST['task_watchers']) ? $_POST['task_watchers'] : array();
		update_post_meta($post_id, 'task_watchers', $task_watchers);
		if(isset($_POST['task_description'])) {
			$task_details_new['task_description'] = $_POST['task_description'];
		}
		update_post_meta($post_id, 'task_details', $task_details_new);
		$attachments = isset($_POST['image_id']) ? $_POST['image_id'] : '';
		$attachments_to_send = array();
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
				$filename = basename( get_attached_file( $attachment ) );
				$attachments_to_send[] = get_attached_file( $attachment );
				$ticket_changes[] = sprintf(__('Uploaded file: %1$s', 'cqpim'), $filename);
			}
		}
		if(!empty($_POST['add_task_message'])) {
			$message_tbs = isset($_POST['add_task_message']) ? $_POST['add_task_message'] : '';
			$message = isset($_POST['add_task_message']) ? sanitize_text_field($_POST['add_task_message']) : '';
			$message = make_clickable($message);
		}
		if(empty($_POST['delete_file'])) {
			$message_tbs = isset($message_tbs) ? $message_tbs : '';
			$project_id = isset($_POST['task_project_id']) ? $_POST['task_project_id'] : '';
			$task_owner = isset($_POST['task_owner']) ? $_POST['task_owner'] : '';
			$task_watchers = isset($_POST['task_watchers']) ? $_POST['task_watchers'] : '';
			update_post_meta($post_id, 'client_updated', false);
			update_post_meta($post_id, 'team_updated', true);
			pto_send_task_updates($post_id, $project_id, $task_owner, $task_watchers, $message_tbs, '', $attachments_to_send);
		}
		if(!empty($_POST['task_project_id']) && $published != true && empty($_POST['delete_file'])) {
			$ppid = $_POST['task_project_id'];
			$current_user = wp_get_current_user();;
			$project_progress = get_post_meta($ppid, 'project_progress', true);
			$project_progress = $project_progress&&is_array($project_progress)?$project_progress:array();
			$task_object = get_post($post_id);
			$task_title = $task_object->post_title;
			$text = sprintf(__('Task Updated: %1$s', 'cqpim'), $task_title );
			$project_progress[] = array(
				'update' => $text,
				'date' => current_time('timestamp'),
				'by' => $current_user->display_name
			);
			update_post_meta($ppid, 'project_progress', $project_progress );
		}	
		update_post_meta($post_id, 'published', true);
		update_post_meta($post_id, 'active', true);
	if(!empty($_POST['add_task_message']) || !empty($ticket_changes)) {
		$message = isset($_POST['add_task_message']) ? $_POST['add_task_message'] : '';
		$message = make_clickable($message);
		$task_messages = get_post_meta($post_id, 'task_messages', true);
		$task_messages = $task_messages&&is_array($task_messages)?$task_messages:array();
		$date = current_time('timestamp');
		$current_user = wp_get_current_user();
		$task_messages[] = array(
			'date' => $date,
			'message' => $message,
			'by' => $current_user->display_name,
			'author' => $current_user->ID,
			'changes' => $ticket_changes
		);		
		update_post_meta($post_id, 'task_messages', $task_messages);
	}
	if( isset( $_POST['delete_file'] ) ){
		$att_to_delete = $_POST['delete_file'];
		foreach ( $att_to_delete as $key => $attID ) {
			$file = get_post($attID);
			$task_object = get_post($post_id);
			$task_link = '<a class="cqpim-link" href="' . get_the_permalink($post_id) . '">' . $task_object->post_title . '</a>';
			$current_user = wp_get_current_user();
			$project_id = get_post_meta($post_id, 'project_id', true);
			$project_progress = get_post_meta($project_id, 'project_progress', true);
			$project_progress[] = array(
				'update' => sprintf(__('File "%1$s" Deleted from - %2$s', 'cqpim'), $file->post_title, $task_link),
				'date' => current_time('timestamp'),
				'by' => $current_user->display_name
			);
			update_post_meta($project_id, 'project_progress', $project_progress );
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
		update_post_meta($post_id, 'duplicate', time());
	}
}