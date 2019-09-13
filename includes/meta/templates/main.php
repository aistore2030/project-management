<?php
add_action( 'add_meta_boxes', 'add_pto_templates_cpt_metaboxes' );
function add_pto_templates_cpt_metaboxes(){
	add_meta_box( 
		'templates_template', 
		__('Milestones & Tasks', 'cqpim'),
		'pto_templates_metabox_callback', 
		'cqpim_templates', 
		'normal',
		'high'
	);
	if(!current_user_can('publish_cqpim_templates')) {
		remove_meta_box( 'submitdiv', 'cqpim_templates', 'side' );
	}
}
function pto_templates_metabox_callback( $post ) {
	$args = array(
		'post_type' => 'cqpim_templates',
		'post_status' => 'private',
		'posts_per_page' => -1,
	);
	$templates = get_posts($args);
	foreach($templates as $template) {
		$mstemplate = get_post_meta($template->ID, 'project_template', true);
		if(!empty($mstemplate)) {
			$milestones = isset($mstemplate['milestones']) ? $mstemplate['milestones'] : array();
			if(empty($milestones)) {
				$new_format = array();
				$msids = array(0);
				foreach($mstemplate as $key => $milestone) {
					$msids[] = $milestone['id'];
					$tasks = isset($milestone['tasks']) ? $milestone['tasks'] : array();
					unset($milestone['tasks']);
					$new_format['milestones'][$key] = $milestone;
					if(!empty($tasks)) {
						$check = isset($milestone['tasks']['task_arrays']) ? $milestone['tasks']['task_arrays'] : array();
						if(empty($check)) {
							$tids = array(0);
							foreach($tasks as $tkey => $task) {
								$tids[] = $task['id'];
								$subtasks = isset($task['subtasks']) ? $task['subtasks'] : array();
								$check2 = isset($task['subtasks']['task_arrays']) ? $task['subtasks']['task_arrays'] : array();
								unset($task['subtasks']);
								$new_format['milestones'][$key]['tasks']['task_arrays'][$tkey] = $task;
								if(!empty($subtasks)) {
									if(empty($check2)) {
										$stids = array(0);
										foreach($subtasks as $stkey => $subtask) {
											$new_format['milestones'][$key]['tasks']['task_arrays'][$tkey]['subtasks']['task_arrays'][$stkey] = $subtask;
											$stids[] = $subtask['id'];
										}
										$high_stid = max($stids);
										$new_format['milestones'][$key]['tasks']['task_arrays'][$tkey]['subtasks']['task_id'] = $high_stid + 1;										
									}
								}
							}
							$high_tid = max($tids);
							$new_format['milestones'][$key]['tasks']['task_id'] = $high_tid + 1;
						}
					}
				}
				$high_id = max($msids);
				$new_format['ms_key'] = $high_id + 1;
				update_post_meta($template->ID, 'project_template', $new_format);
			}
		}
	}
	$args = array(
		'post_type' => 'cqpim_teams',
		'posts_per_page' => -1,
		'post_status' => 'private',	
	);
	$team_array = array();
	$team_members = get_posts($args);
	foreach($team_members as $team) {
		$team_details = get_post_meta($team->ID, 'team_details', true);
		$team_array[$team->ID] = array(
			'id' => $team->ID,
			'name' => isset($team_details['team_name']) ? $team_details['team_name'] : __('Name not set', 'cqpim'),
		);
	}
 	wp_nonce_field( 
	'templates_metabox', 
	'templates_metabox_nonce' );
	$template = get_post_meta($post->ID, 'project_template', true);
	if(empty($template)) {
		$template = array();
	}
	$milestone_key = isset($template['ms_key']) ? $template['ms_key'] : 1;
	$milestones = isset($template['milestones']) ? $template['milestones'] : '';
	$mstotal = count($template);
	$mstotal = $mstotal + 1;
	if(empty($mstotal)) {
		$mstotal = 1;
	}
	$title_set = get_post_meta($post->ID, 'title_set', true);
	echo '<input type="hidden" id="title_set" value="' . $title_set . '" />';
	if(empty($milestones)) {
		echo '<p>' . __('You haven\'t added any milestones or tasks to this template.', 'cqpim') . '</p>';
	} else {
		echo '<input type="submit" class="cqpim_button font-blue border-blue right op" value="' . __('Update Milestone Template', 'cqpim') . '"/><div class="clear"></div><br />';
		$currency = get_option('currency_symbol');
		$cost_title = __('Cost', 'cqpim');
		$ordered = array();
		foreach($milestones as $key => $element) { 
			$ordered[$element['weight']] = $element;
		}
		ksort($ordered);
		echo '<div id="dd-container">';
		foreach($ordered as $key => $element) { 
			$cost = preg_replace("/[^\\d.]+/","", $element['cost']); 
			$task_deadline = isset($element['deadline']) ? $element['deadline'] : '';
			?>
			<div class="dd-milestone">
				<input type="hidden" class="element_weight" name="element_weight[<?php echo $element['id']; ?>]" id="element_weight[<?php echo $element['id']; ?>]" value="<?php echo $element['weight']; ?>" />
				<div class="dd-milestone-title">
					<span class="cqpim_button cqpim_small_button font-white bg-blue-madison nolink op rounded_2"><?php _e('Milestone', 'cqpim'); ?></span> <span class="ms-title"><?php echo $element['title']; ?></span>
					<div class="dd-milestone-actions">
						<?php if(empty($quote_details['confirmed'])) { ?>
							<button class="edit-milestone cqpim_button cqpim_small_button font-white bg-amber op rounded_2 cqpim_tooltip" value="<?php echo $element['id']; ?>" title="<?php _e('Edit Milestone', 'cqpim'); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button> <button class="delete_stage_conf cqpim_button cqpim_small_button font-white bg-red op rounded_2 cqpim_tooltip" data-id="<?php echo $element['id']; ?>" value="<?php echo $element['id']; ?>"  title="<?php _e('Delete Milestone', 'cqpim'); ?>"><i class="fa fa-trash" aria-hidden="true"></i></button> <button class="add_task cqpim_button cqpim_small_button font-white bg-green op rounded_2 cqpim_tooltip" data-ms="<?php echo $element['id']; ?>" data-project="<?php echo $post->ID; ?>" value="<?php echo $element['id']; ?>" title="<?php _e('Add Task to Milestone', 'cqpim'); ?>"><i class="fa fa-plus" aria-hidden="true"></i></button> <button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue op rounded_2 cqpim_tooltip" value="<?php echo $element['id']; ?>" title="<?php _e('Reorder Milestone', 'cqpim'); ?>"><i class="fa fa-sort" aria-hidden="true"></i></button>
						<?php } else { ?>
							<span class="task_over"><?php _e('LOCKED', 'cqpim'); ?></span>
						<?php } ?>							
					</div>
					<div class="clear"></div>
				</div>
				<div class="dd-tasks" data-ms="<?php echo $key; ?>">
					<?php 
					$task_id = isset($element['tasks']['task_id']) ? $element['tasks']['task_id'] : 1;
					$tasks = isset($element['tasks']['task_arrays']) ? $element['tasks']['task_arrays'] : array();	
					$tordered = array();
					foreach($tasks as $task) {
						$tordered[$task['weight']] = $task;
					}
					ksort($tordered);
					foreach($tordered as $tkey => $task) { 
						?>	
						<div class="dd-task">
							<input class="task_weight" type="hidden" value="<?php echo $task['weight']; ?>" />
							<input class="task_id" type="hidden" value="<?php echo $task['id']; ?>" />
							<input class="ms_id" type="hidden" value="<?php echo $element['id']; ?>" />
							<span class="table-task cqpim_button cqpim_small_button font-white bg-grey-cascade op nolink rounded_2"><?php _e('Task', 'cqpim'); ?></span> <span class="ms-title"><?php echo $task['title']; ?></span> <?php if(!empty($task['assignee'])) { ?>(<?php printf(__('Assigned to %1$s', 'cqpim'), $team_array[$task['assignee']]['name']); ?>)<?php } ?>
							<div class="dd-task-actions">
								<button class="edit-task cqpim_button cqpim_small_button font-white bg-amber op rounded_2 cqpim_tooltip" data-ms="<?php echo $element['id']; ?>" value="<?php echo $task['id']; ?>" title="<?php _e('Edit Task', 'cqpim'); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button> <button class="delete_task cqpim_button cqpim_small_button font-white bg-red op rounded_2 cqpim_tooltip" data-tid="<?php echo $post->ID; ?>" data-ms="<?php echo $element['id']; ?>" value="<?php echo $task['id']; ?>" title="<?php _e('Delete Task', 'cqpim'); ?>"><i class="fa fa-trash" aria-hidden="true"></i></button> <button class="add_subtask cqpim_button cqpim_small_button font-white bg-green op rounded_2 cqpim_tooltip" data-ms="<?php echo $element['id']; ?>" data-project="<?php echo $post->ID; ?>" value="<?php echo $element['id']; ?><?php echo $task['id']; ?>" title="<?php _e('Add Subtask', 'cqpim'); ?>"><i class="fa fa-plus" aria-hidden="true"></i></button> <button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue op rounded_2 cqpim_tooltip" value="<?php echo $tkey; ?>"  title="<?php _e('Reorder Task', 'cqpim'); ?>"><i class="fa fa-sort" aria-hidden="true"></i></button>
							</div>
							<div class="clear"></div>	
							<?php
							$subtask_id = isset($task['subtasks']['task_id']) ? $task['subtasks']['task_id'] : 1;
							$subtasks = isset($task['subtasks']['task_arrays']) ? $task['subtasks']['task_arrays'] : array();	
							$ttordered = array();
							foreach($subtasks as $subtask) {
								$ttordered[$subtask['weight']] = $subtask;
							}
							ksort($ttordered);
							if(!empty($ttordered)) {
								ksort($ttordered);
								echo '<div class="dd-subtasks">';
								foreach($ttordered as $stkey => $subtask) { 
									$sweight = isset($subtask['weight']) ? $subtask['weight'] : ''; ?>
									<div class="dd-subtask">
										<input class="task_weight" type="hidden" value="<?php echo $sweight; ?>" />
										<input class="task_id" type="hidden" value="<?php echo $subtask['id']; ?>" />
										<input class="ms_id" type="hidden" value="<?php echo $element['id']; ?>" />
										<input class="parent_id" type="hidden" value="<?php echo $task['id']; ?>" />
										<span class="table-task cqpim_button cqpim_small_button font-white bg-grey-cascade op nolink rounded_2"><?php _e('Subtask', 'cqpim'); ?></span> <span class="ms-title"><?php echo $subtask['title']; ?></span> <?php if(!empty($subtask['assignee'])) { ?>(<?php printf(__('Assigned to %1$s', 'cqpim'), $team_array[$subtask['assignee']]['name']); ?>)<?php } ?>
										<div class="dd-task-actions">
											<button class="edit-subtask cqpim_button cqpim_small_button font-white bg-amber op rounded_2 cqpim_tooltip" data-ms="<?php echo $element['id']; ?>" data-parent="<?php echo $task['id']; ?>" value="<?php echo $subtask['id']; ?>" title="<?php _e('Edit Task', 'cqpim'); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button> <button class="delete_subtask cqpim_button cqpim_small_button font-white bg-red op rounded_2 cqpim_tooltip" data-tid="<?php echo $post->ID; ?>" data-ms="<?php echo $element['id']; ?>" data-parent="<?php echo $task['id']; ?>" value="<?php echo $subtask['id']; ?>" title="<?php _e('Delete Task', 'cqpim'); ?>"><i class="fa fa-trash" aria-hidden="true"></i></button> <button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue op rounded_2 cqpim_tooltip" value="<?php echo $stkey; ?>"  title="<?php _e('Reorder Task', 'cqpim'); ?>"><i class="fa fa-sort" aria-hidden="true"></i></button>
										</div>
										<div class="clear"></div>	
									</div>
									<div id="edit-subtask-div-<?php echo $element['id']; ?><?php echo $task['id'] ?><?php echo $subtask['id']; ?>-container" style="display:none">
										<div id="edit-subtask-div-<?php echo $element['id']; ?><?php echo $task['id'] ?><?php echo $subtask['id']; ?>" class="edit-task-div">
											<div style="padding:12px">
												<h3><?php _e('Edit Task', 'cqpim'); ?></h3>
												<span class="label"><strong><?php _e('Title:', 'cqpim'); ?></strong></span>
												<input type="hidden" id="task_id_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>" name="task_id_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>" value="<?php echo $subtask['id']; ?>" />
												<input type="hidden" id="task_ms_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>" name="task_ms_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>" value="<?php echo $element['id']; ?>" />
												<input type="text" name="task_title_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>" id="task_title_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>" value="<?php echo $subtask['title']; ?>" placeholder="<?php _e('Task title', 'cqpim'); ?>"/>							
												<br /><br />
												<span class="label"><strong><?php _e('Description:', 'cqpim'); ?></strong></span>
												<textarea style="width:100%;height:100px" name="task_description_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>" id="task_description_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>" placeholder="<?php _e('Task description', 'cqpim'); ?>"><?php echo $subtask['description']; ?></textarea>
												<br /><br />
												<select name="sub_task_assignee_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>" id="sub_task_assignee_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>">
													<option value="0"><?php _e('Choose a team member', 'cqpim'); ?></option>
													<?php foreach($team_array as $team_member) { ?>
														<option value="<?php echo $team_member['id']; ?>" <?php selected($subtask['assignee'], $team_member['id']); ?>><?php echo $team_member['name']; ?></option>
													<?php } ?>
												</select>
												<br /><br />
												<span class="label"><strong><?php _e('Start:', 'cqpim'); ?></strong></span>
												<input class="datepicker" type="text" name="task_start_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>" id="task_start_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>" value="<?php if(is_numeric($task['start'])) { echo date(get_option('cqpim_date_format'), $subtask['start']); } else { echo $subtask['start']; } ?>" placeholder="<?php _e('Start Date', 'cqpim'); ?>"/>														
												<br /><br />
												<span class="label"><strong><?php _e('Deadline:', 'cqpim'); ?></strong></span>
												<input class="datepicker" type="text" name="task_finish_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>" id="task_finish_<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>" value="<?php if(is_numeric($task['deadline'])) { echo date(get_option('cqpim_date_format'), $subtask['deadline']); } else { echo $subtask['deadline']; } ?>" placeholder="<?php _e('Deadline', 'cqpim'); ?>"/>	
												<br /><br />
												<button class="cancel-colorbox mt-20 cqpim_button border-red font-red op"><?php _e('Cancel', 'cqpim'); ?></button>
												<button class="update-subtask mt-20 cqpim_button border-green font-green op right" data-ms="<?php echo $element['id']; ?>" data-parent="<?php echo $task['id']; ?>" value="<?php echo $subtask['id']; ?>"><?php _e('Save', 'cqpim'); ?></button>
												<div class="clear"></div>
												<div id="subtask-messages-<?php echo $element['id']; ?><?php echo $task['id']; ?><?php echo $subtask['id']; ?>"></div>
											</div>
										</div>	
									</div>
								<?php }
								echo '</div>';
							} ?>
							<div id="add-subtask-div-<?php echo $element['id']; ?><?php echo $task['id']; ?>-container" style="display:none">
								<div id="add-subtask-div-<?php echo $element['id']; ?><?php echo $task['id']; ?>" class="add-task-div">
									<div style="padding:12px">
										<h3><?php _e('Add Subtask', 'cqpim'); ?></h3>
										<input type="hidden" id="sub_task_id_<?php echo $element['id']; ?><?php echo $task['id']; ?>" name="sub_task_id_<?php echo $element['id']; ?><?php echo $task['id']; ?>" value="<?php echo $subtask_id; ?>" />
										<input type="hidden" id="sub_task_weight_<?php echo $element['id']; ?><?php echo $task['id']; ?>" name="sub_task_weight_<?php echo $element['id']; ?><?php echo $task['id']; ?>" value="<?php echo $subtask_id; ?>" />
										<input type="hidden" id="sub_task_milestone_id_<?php echo $element['id']; ?><?php echo $task['id']; ?>" name="sub_task_milestone_id_<?php echo $element['id']; ?><?php echo $task['id']; ?>" value="<?php echo $element['id']; ?>" />
										<input type="hidden" id="sub_task_project_id_<?php echo $element['id']; ?><?php echo $task['id']; ?>" name="sub_task_project_id_<?php echo $element['id']; ?><?php echo $task['id']; ?>" value="<?php echo $post->ID; ?>" />
										<input id="sub_task_parent_id_<?php echo $element['id']; ?><?php echo $task['id']; ?>" name="sub_task_parent_id_<?php echo $element['id']; ?><?php echo $task['id']; ?>" type="hidden" value="<?php echo $task['id']; ?>" />
										<input style="width:100%" type="text" name="sub_task_title_<?php echo $element['id']; ?><?php echo $task['id']; ?>" id="sub_task_title_<?php echo $element['id']; ?><?php echo $task['id']; ?>" placeholder="<?php _e('Task title', 'cqpim'); ?>"/><br /><br />					
										<textarea style="width:100%;height:100px" name="sub_task_description_<?php echo $element['id']; ?><?php echo $task['id']; ?>" id="sub_task_description_<?php echo $element['id']; ?><?php echo $task['id']; ?>" placeholder="<?php _e('Task description', 'cqpim'); ?>"></textarea><br /><br />
										<select name="sub_task_assignee_<?php echo $element['id']; ?><?php echo $task['id']; ?>" id="sub_task_assignee_<?php echo $element['id']; ?><?php echo $task['id']; ?>">
											<option value="0"><?php _e('Choose a team member', 'cqpim'); ?></option>
											<?php foreach($team_array as $team_member) { ?>
												<option value="<?php echo $team_member['id']; ?>"><?php echo $team_member['name']; ?></option>
											<?php } ?>
										</select>
										<br /><br />										
										<input style="width:100%" class="datepicker" type="text" name="sub_task_start_<?php echo $element['id']; ?><?php echo $task['id']; ?>" id="sub_task_start_<?php echo $element['id']; ?><?php echo $task['id']; ?>" placeholder="<?php _e('Start Date', 'cqpim'); ?>"/>	
										<br /><br />
										<input style="width:100%" class="datepicker" type="text" name="sub_task_finish_<?php echo $element['id']; ?><?php echo $task['id']; ?>" id="sub_task_finish_<?php echo $element['id']; ?><?php echo $task['id']; ?>" placeholder="<?php _e('Deadline', 'cqpim'); ?>"/>	
										<div class="clear"></div>
										<br /><br />
										<button class="cancel-colorbox mt-20 cqpim_button border-red font-red op"><?php _e('Cancel', 'cqpim'); ?></button>
										<button class="save-subtask mt-20 cqpim_button border-green font-green op right" value="<?php echo $element['id']; ?><?php echo $task['id']; ?>"><?php _e('Add Subtask', 'cqpim'); ?></button>
										<div class="clear"></div>
										<div id="subtask-messages-<?php echo $task['id']; ?>"></div>
									</div>
								</div>	
							</div>
						</div>
						<div id="edit-task-div-<?php echo $element['id']; ?><?php echo $task['id'] ?>-container" style="display:none">
							<div id="edit-task-div-<?php echo $element['id']; ?><?php echo $task['id'] ?>" class="edit-task-div">
								<div style="padding:12px">
									<h3><?php _e('Edit Task', 'cqpim'); ?></h3>
									<span class="label"><strong><?php _e('Title:', 'cqpim'); ?></strong></span>
									<input type="hidden" id="task_id_<?php echo $element['id']; ?><?php echo $task['id']; ?>" name="task_id_<?php echo $element['id']; ?><?php echo $task['id']; ?>" value="<?php echo $task['id']; ?>" />
									<input type="hidden" id="task_ms_<?php echo $element['id']; ?><?php echo $task['id']; ?>" name="task_ms_<?php echo $element['id']; ?><?php echo $task['id']; ?>" value="<?php echo $element['id']; ?>" />
									<input type="text" name="task_title_<?php echo $element['id']; ?><?php echo $task['id']; ?>" id="task_title_<?php echo $element['id']; ?><?php echo $task['id']; ?>" value="<?php echo $task['title']; ?>" placeholder="<?php _e('Task title', 'cqpim'); ?>"/>							
									<br /><br />
									<span class="label"><strong><?php _e('Description:', 'cqpim'); ?></strong></span>
									<textarea style="width:100%;height:100px" name="task_description_<?php echo $element['id']; ?><?php echo $task['id']; ?>" id="task_description_<?php echo $element['id']; ?><?php echo $task['id']; ?>" placeholder="<?php _e('Task description', 'cqpim'); ?>"><?php echo $task['description']; ?></textarea>
									<br /><br />
									<select name="task_assignee_<?php echo $element['id']; ?><?php echo $task['id']; ?>" id="task_assignee_<?php echo $element['id']; ?><?php echo $task['id']; ?>">
										<option value="0"><?php _e('Choose a team member', 'cqpim'); ?></option>
										<?php foreach($team_array as $team_member) { ?>
											<option value="<?php echo $team_member['id']; ?>" <?php selected($task['assignee'], $team_member['id']); ?>><?php echo $team_member['name']; ?></option>
										<?php } ?>
									</select>
									<br /><br />
									<span class="label"><strong><?php _e('Start:', 'cqpim'); ?></strong></span>
									<input class="datepicker" type="text" name="task_start_<?php echo $element['id']; ?><?php echo $task['id']; ?>" id="task_start_<?php echo $element['id']; ?><?php echo $task['id']; ?>" value="<?php if(is_numeric($task['start'])) { echo date(get_option('cqpim_date_format'), $task['start']); } else { echo $task['start']; } ?>" placeholder="<?php _e('Start Date', 'cqpim'); ?>"/>														
									<br /><br />
									<span class="label"><strong><?php _e('Deadline:', 'cqpim'); ?></strong></span>
									<input class="datepicker" type="text" name="task_finish_<?php echo $element['id']; ?><?php echo $task['id']; ?>" id="task_finish_<?php echo $element['id']; ?><?php echo $task['id']; ?>" value="<?php if(is_numeric($task['deadline'])) { echo date(get_option('cqpim_date_format'), $task['deadline']); } else { echo $task['deadline']; } ?>" placeholder="<?php _e('Deadline', 'cqpim'); ?>"/>	
									<br /><br />
									<button class="cancel-colorbox mt-20 cqpim_button border-red font-red op"><?php _e('Cancel', 'cqpim'); ?></button>
									<button class="update-task mt-20 cqpim_button border-green font-green op right" data-ms="<?php echo $element['id']; ?>" value="<?php echo $task['id']; ?>"><?php _e('Save', 'cqpim'); ?></button>
									<div class="clear"></div>
									<div id="task-messages-<?php echo $key; ?><?php echo $task['id']; ?>"></div>
								</div>
							</div>	
						</div>
					<?php } 
					?>
				</div>
			</div>
			<div id="delete-milestone-div-<?php echo $element['id']; ?>-container" style="display:none">
				<div id="delete-milestone-div-<?php echo $element['id']; ?>" class="delete-milestone-div">
					<div style="padding:12px">
						<h3><?php _e('Are you sure?', 'cqpim'); ?></h3>
						<p><?php _e('Deleting this milestone will also delete related tasks. Are you sure you want to do this?', 'cqpim'); ?></p>
						<button class="cancel_delete_stage mt-20 cqpim_button border-red font-red op"><?php _e('Cancel', 'cqpim'); ?></button> <button class="delete_stage mt-20 cqpim_button border-green font-green op right" data-id="<?php echo $element['id']; ?>" value="<?php echo $element['id']; ?>"><?php _e('Delete', 'cqpim'); ?></button>
					</div>
				</div>
			</div>
			<div id="add-task-div-<?php echo $element['id']; ?>-container" style="display:none">
				<div id="add-task-div-<?php echo $element['id']; ?>" class="add-task-div">
					<div style="padding:12px">
						<h3><?php _e('Add Task', 'cqpim'); ?></h3>
						<input type="hidden" id="task_id_<?php echo $element['id']; ?>" name="task_id_<?php echo $element['id']; ?>" value="<?php echo $task_id; ?>" />
						<input type="hidden" id="task_weight_<?php echo $element['id']; ?>" name="task_weight_<?php echo $element['id']; ?>" value="<?php echo $task_id; ?>" />
						<input type="hidden" id="task_milestone_id_<?php echo $element['id']; ?>" name="task_milestone_id_<?php echo $element['id']; ?>" value="<?php echo $element['id']; ?>" />
						<input type="hidden" id="task_project_id_<?php echo $element['id']; ?>" name="task_project_id_<?php echo $element['id']; ?>" value="<?php echo $post->ID; ?>" />
						<input style="width:100%" type="text" name="task_title_<?php echo $element['id']; ?>" id="task_title_<?php echo $element['id']; ?>" placeholder="<?php _e('Task title', 'cqpim'); ?>"/><br /><br />					
						<textarea style="width:100%;height:100px" name="task_description_<?php echo $element['id']; ?>" id="task_description_<?php echo $element['id']; ?>" placeholder="<?php _e('Task description', 'cqpim'); ?>"></textarea><br /><br />
						<select name="task_assignee_<?php echo $element['id']; ?>" id="task_assignee_<?php echo $element['id']; ?>">
							<option value="0"><?php _e('Choose a team member', 'cqpim'); ?></option>
							<?php foreach($team_array as $team_member) { ?>
								<option value="<?php echo $team_member['id']; ?>"><?php echo $team_member['name']; ?></option>
							<?php } ?>
						</select>
						<br /><br />
						<input style="width:100%" class="datepicker" type="text" name="task_start_<?php echo $element['id']; ?>" id="task_start_<?php echo $element['id']; ?>" placeholder="<?php _e('Start Date', 'cqpim'); ?>"/>	
						<Br /><br />
						<input style="width:100%" class="datepicker" type="text" name="task_finish_<?php echo $element['id']; ?>" id="task_finish_<?php echo $element['id']; ?>" placeholder="<?php _e('Deadline', 'cqpim'); ?>"/>	
						<div class="clear"></div>
						<br /><br />
						<button class="cancel-colorbox mt-20 cqpim_button border-red font-red op"><?php _e('Cancel', 'cqpim'); ?></button>
						<button class="save-task mt-20 cqpim_button border-green font-green op right" value="<?php echo $element['id']; ?>"><?php _e('Add Task', 'cqpim'); ?></button>
						<div class="clear"></div>
						<div id="task-messages-<?php echo $key; ?>"></div>
					</div>
				</div>	
			</div>
			<div id="edit-milestone-<?php echo $element['id']; ?>-container" style="display:none">
				<div id="edit-milestone-<?php echo $element['id']; ?>" class="edit_milestone <?php echo $element['id']; ?>">
					<div style="padding:12px">
						<h3><?php _e('Edit Milestone', 'cqpim'); ?></h3>
						<input type="hidden" name="added_element_milestone_id[<?php echo $element['id']; ?>]" id="added_element_milestone_id[<?php echo $element['id']; ?>]" value="<?php echo $element['id']; ?>" />
						<span class="label"><strong><?php _e('Title:', 'cqpim'); ?></strong></span>
						<input style="width:100%" type="text" name="added_element_title[<?php echo $element['id']; ?>]" id="added_element_title[<?php echo $element['id']; ?>]" value="<?php echo $element['title']; ?>" />
						<br /><br />
						<span class="label"><strong><?php _e('Start Date:', 'cqpim'); ?></strong></span>
						<input style="width:100%" class="datepicker" type="text" name="added_element_start[<?php echo $element['id']; ?>]" id="added_element_start[<?php echo $element['id']; ?>]" value="<?php if(is_numeric($element['start'])) { echo date(get_option('cqpim_date_format'), $element['start']); } else { echo $element['start']; } ?>" />
						<br /><br />
						<span class="label"><strong><?php _e('Deadline:', 'cqpim'); ?></strong></span>
						<input style="width:100%" class="datepicker" type="text" name="added_element_finish[<?php echo $element['id']; ?>]" id="added_element_finish[<?php echo $element['id']; ?>]" value="<?php if(is_numeric($element['deadline'])) { echo date(get_option('cqpim_date_format'), $element['deadline']); } else { echo $element['deadline']; } ?>" />
						<br /><br />
						<span class="label"><strong><?php _e('Estimated Cost:', 'cqpim'); ?></strong></span>
						<input style="width:100%" type="text" name="added_element_cost[<?php echo $element['id']; ?>]" id="added_element_cost[<?php echo $element['id']; ?>]" value="<?php echo $element['cost']; ?>" />
						<br /><br />
						<div id="update-ms-message-<?php echo $element['id']; ?>"></div>
						<button class="cancel-colorbox mt-20 cqpim_button border-red font-red op"><?php _e('Cancel', 'cqpim'); ?></button>
						<button class="save-milestone mt-20 cqpim_button border-green font-green op right" value="<?php echo $element['id']; ?>"><?php _e('Save', 'cqpim'); ?></button>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		<?php
		}
		echo '</div>';		
	}
	?>
	<?php echo '<input type="submit" class="cqpim_button mt-20 font-blue border-blue right op" value="' . __('Update Milestone Template', 'cqpim') . '"/>'; ?>
	<a href="#add-milestone-div" id="add-milestone" class="cqpim_button mt-20 font-blue border-blue right op colorbox"><?php _e('Add Milestone', 'cqpim'); ?></a>
	<a href="#clear-all-div" id="clear-all" class="cqpim_button mt-20 font-blue border-blue right op colorbox"><?php _e('Clear All', 'cqpim'); ?></a>
	<div class="clear"></div>
	<div id="add-milestone-div-container" style="display:none">
		<div id="add-milestone-div">
			<div style="padding:12px">
				<h3><?php _e('Add Milestone', 'cqpim'); ?></h3>
				<input style="width:100%" type="text" name="quote_element_title" id="quote_element_title" placeholder="<?php _e('Milestone title, eg. \'Design Phase\'', 'cqpim'); ?>"/>
				<input type="hidden" id="add_milestone_id" name="add_milestone_id" value="<?php echo $milestone_key; ?>" />
				<input type="hidden" id="add_milestone_order" name="add_milestone_order" value="<?php echo $milestone_key; ?>" />
				<input style="width:100%" class="datepicker" type="text" name="quote_element_start" id="quote_element_start" placeholder="<?php _e('Start', 'cqpim'); ?>"/>
				<input style="width:100%" class="datepicker" type="text" name="quote_element_finish" id="quote_element_finish" placeholder="<?php _e('Deadline', 'cqpim'); ?>"/>
				<input style="width:100%" type="text" name="quote_element_cost" id="quote_element_cost" placeholder="<?php _e('Estimated Cost', 'cqpim'); ?>"/>
				<br /><br />
				<button class="cancel-colorbox mt-20 cqpim_button border-red font-red op"><?php _e('Cancel', 'cqpim'); ?></button>
				<button id="add_quote_element" class="mt-20 cqpim_button border-green font-green op right"><?php _e('Add milestone to Project Template', 'cqpim'); ?></button>
			</div>
		</div>	
	</div>
	<div id="clear-all-div-container" style="display:none">
		<div id="clear-all-div">
			<div style="padding:12px">
				<h3><?php _e('Clear All', 'cqpim'); ?></h3>
				<p><?php _e('Are you sure you want to clear all Milestones and Tasks? This cannot be undone.', 'cqpim'); ?></p>
				<br /><br />
				<button class="cancel-colorbox mt-20 cqpim_button border-red font-red op"><?php _e('Cancel', 'cqpim'); ?></button>
				<button id="clear-all-action" class="mt-20 cqpim_button border-green font-green op right" value="<?php echo $post->ID; ?>"><?php _e('Clear All', 'cqpim'); ?></button>
			</div>
		</div>	
	</div>
	<div id="set-title-div-container" style="display:none">
		<div id="set-title-div">
			<div style="padding:12px">
				<h3><?php _e('Template Title', 'cqpim'); ?></h3>
				<p><?php _e('Please set a title for this Milestone Template', 'cqpim'); ?></p>
				<input type="text" name="set-title" id="set-title" />
				<br /><br />
				<button id="set-title-action" class="mt-20 cqpim_button border-green font-green op right" value="<?php echo $post->ID; ?>"><?php _e('Set Title', 'cqpim'); ?></button>
			</div>
		</div>	
	</div>
	<?php
}
add_action( 'save_post', 'save_pto_templates_metabox_data' );
function save_pto_templates_metabox_data( $post_id ){
	if ( ! isset( $_POST['templates_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['templates_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'templates_metabox' ) )
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
	if(isset($_POST['added_element_title'])) {
		$title_array = $_POST['added_element_title'];
		$quote_elements = get_post_meta($post_id, 'project_template', true);
		foreach($title_array as $key => $to_add) {
			$title = $to_add;
			$milestone_id = $_POST['added_element_milestone_id'][$key];
			$milestone_start = $_POST['added_element_start'][$key];	
			$milestone_start = pto_convert_date($milestone_start);
			$milestone_deadline = $_POST['added_element_finish'][$key];
			$milestone_weight = $_POST['element_weight'][$key];
			$milestone_deadline = pto_convert_date($milestone_deadline);
			$cost = $_POST['added_element_cost'][$key];
			$quote_elements['milestones'][$key] = array(
				'deadline' => $milestone_deadline,
				'title' => $title,
				'start' => $milestone_start,
				'id' => $milestone_id,
				'cost' => $cost,
				'tasks' => $quote_elements['milestones'][$key]['tasks'],
				'weight' => $milestone_weight
			);
		}
		update_post_meta($post_id, 'project_template', $quote_elements);
	}
	if(isset($_POST['delete_stage'])) {
		$stages_to_delete = $_POST['delete_stage'];
		$quote_elements = get_post_meta($post_id, 'project_template', true);
		foreach($stages_to_delete as $key => $delete) {
			unset($quote_elements['milestones'][$delete]);
		}
		update_post_meta($post_id, 'project_template', $quote_elements);
	}
	if(!empty($_POST['set-title'])) {
		$quote_updated = array(
			'ID' => $post_id,
			'post_title' => $_POST['set-title'],
			'post_name' => $post_id,
		);
		if ( ! wp_is_post_revision( $post_id ) ){
			remove_action('save_post', 'save_pto_templates_metabox_data');
			wp_update_post( $quote_updated );
			add_action('save_post', 'save_pto_templates_metabox_data');
		}	
		update_post_meta($post_id, 'title_set', 1);
	}
}