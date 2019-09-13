<?php
function pto_support_elements_metabox_callback( $post ) {
 	wp_nonce_field( 
	'support_elements_metabox', 
	'support_elements_metabox_nonce' );
	$quote_elements = get_post_meta($post->ID, 'quote_elements', true);
	$quote_details = get_post_meta($post->ID, 'project_details', true);
	if(1) {
		if(empty($quote_elements)) {
			echo '<p>' . __('You have not added any milestones to this support ticket, please do so below', 'cqpim') . '</p>';
			$i = 0;
		} else {
			$milestones_added = true;
			$project_total_time_spent = 0;
			$i = 0;
			$currency = get_option('currency_symbol');
			$cost_title = __('Cost', 'cqpim');
			$ordered = array();
			$i = 0;
			$mi = 0;
			$used_keys = array();
			$used_weights = array();
			foreach($quote_elements as $key => $element) {
				$weight = isset($element['weight']) ? $element['weight'] : $mi;
				$ordered[$weight] = $element;
				$used_key = substr($key, strpos($key, "-") + 1); 
				$used_keys[] = $used_key;
				$used_weights[] = $weight;
				$mi++;
			}
			ksort($ordered);
			$highest_key = max($used_keys);		
			$highest_weight = max($used_weights);
			echo '<div id="dd-container">';
			foreach($ordered as $key => $element) {
				$eweight = isset($element['weight']) ? $element['weight'] : '';
				$cost = preg_replace("/[^\\d.]+/","", $element['cost']); 
				$task_status = isset($element['status']) ? $element['status'] : '';
				$task_deadline = isset($element['deadline']) ? $element['deadline'] : '';
				if(!is_numeric($task_deadline)) {
					$str_deadline = str_replace('/','-', $task_deadline);
					$deadline_stamp = strtotime($str_deadline);
				} else {
					$deadline_stamp = $task_deadline;
				}
				$now = time();
				if($task_status != 'complete') {
					if(!empty($deadline_stamp) && $now > $deadline_stamp) {
						$milestone_status_string = '<span class="cqpim_button cqpim_small_button nolink op border-red font-red rounded_2">' . __('Overdue', 'cqpim') . '</span>';
					} else {
						$milestone_status_string = isset($element['status']) ? $element['status'] : '';
						if(!$milestone_status_string || $milestone_status_string == 'pending') {
							$milestone_status_string = '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber rounded_2">' . __('Pending', 'cqpim') . '</span>';
						} else if($milestone_status_string == 'on_hold') {
							$milestone_status_string = '<span class="cqpim_button cqpim_small_button nolink op border-red font-red rounded_2">' . __('On Hold', 'cqpim') . '</span>';
						}
					}
				} else {
					$milestone_status_string = '<span class="cqpim_button cqpim_small_button nolink op border-green font-green rounded_2">' . __('Complete', 'cqpim') . '</span>';
				} ?>	
				<div class="dd-milestone">
					<input type="hidden" class="element_weight" name="element_weight[<?php echo $element['id']; ?>]" id="element_weight[<?php echo $element['id']; ?>]" value="<?php if(!empty($eweight)) { echo $eweight; } else { echo $i; } ?>" />
					<div class="dd-milestone-title">
						<span class="cqpim_button cqpim_small_button font-white bg-blue-madison nolink op rounded_2"><?php _e('Milestone', 'cqpim'); ?></span> <span class="ms-title"><?php echo $element['title']; ?></span>
						<?php if(current_user_can('cqpim_edit_project_milestones')) { ?>
							<div class="dd-milestone-actions">
								<button class="edit-milestone cqpim_button cqpim_small_button font-white bg-amber op rounded_2 cqpim_tooltip" value="<?php echo $element['id']; ?>" title="<?php _e('Edit Milestone', 'cqpim'); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button> <button class="delete_stage_conf cqpim_button cqpim_small_button font-white bg-red op rounded_2 cqpim_tooltip" data-id="<?php echo $element['id']; ?>" value="<?php echo $element['id']; ?>"  title="<?php _e('Delete Milestone', 'cqpim'); ?>"><i class="fa fa-trash" aria-hidden="true"></i></button> <button class="add_task cqpim_button cqpim_small_button font-white bg-green op rounded_2 cqpim_tooltip" data-ms="<?php echo $element['id']; ?>" data-project="<?php echo $post->ID; ?>" value="<?php echo $element['id']; ?>" title="<?php _e('Add Task to Milestone', 'cqpim'); ?>"><i class="fa fa-plus" aria-hidden="true"></i></button> <button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue op rounded_2 cqpim_tooltip" value="<?php echo $element['id']; ?>" title="<?php _e('Reorder Milestone', 'cqpim'); ?>"><i class="fa fa-sort" aria-hidden="true"></i></button>
							</div>
						<?php } ?>
						<div class="dd-milestone-status">
							<?php echo $milestone_status_string; ?>
						</div>
						<div class="clear"></div>
						<div class="dd-milestone-info">
							<?php if(current_user_can('cqpim_view_project_financials')) { ?>
								<?php if(!empty($element['cost'])) { ?>
									<strong><?php _e('Cost:', 'cqpim'); ?></strong> <?php echo pto_calculate_currency($post->ID, $element['cost']); ?>
								<?php } ?>
							<?php } ?>
							<?php if(!empty($element['start'])) { ?>
								<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Start Date:', 'cqpim'); ?></strong> <?php echo date(get_option('cqpim_date_format'), $element['start']); ?>
							<?php } ?>
							<?php if(!empty($element['deadline'])) { ?>
								<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Deadline:', 'cqpim'); ?></strong> <?php echo date(get_option('cqpim_date_format'), $element['deadline']); ?>
							<?php } ?>	
						</div>
						<div class="clear"></div>
					</div>
					<div class="dd-tasks" data-ms="<?php echo $element['id']; ?>">
						<?php 
						$args = array(
							'post_type' => 'cqpim_tasks',
							'posts_per_page' => -1,
							'meta_key' => 'milestone_id',
							'meta_value' => $element['id'],
							'orderby' => 'date',
							'order' => 'ASC'
						);
						$tasks = get_posts($args);
						$ti = 0;
						$ordered = array();
						$wi = 0;
						foreach($tasks as $task) {
							$task_details = get_post_meta($task->ID, 'task_details', true);
							$weight = isset($task_details['weight']) ? $task_details['weight'] : $wi;
							if(empty($task->post_parent)) {
								$ordered[$weight] = $task;
							}
							$wi++;
						}
						ksort($ordered);
						foreach($ordered as $task) {
							$task_details = get_post_meta($task->ID, 'task_details', true);
							$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
							$start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
							$description = isset($task_details['task_description']) ? $task_details['task_description'] : '';
							$weight = isset($task_details['weight']) ? $task_details['weight'] : 0;
							$task_owner = get_post_meta($task->ID, 'owner', true);
							$client_check = preg_replace('/[0-9]+/', '', $task_owner);
							unset($client);
							if($client_check == 'C') {
								$client = true;
							}
							if($task_owner) {
								if(!empty($client) && $client == true) {
									$id = preg_replace("/[^0-9,.]/", "", $task_owner);
									$client_object = get_user_by('id', $id);
									$task_owner = $client_object->display_name;
								} else {
									$team_details = get_post_meta($task_owner, 'team_details', true);
									$team_name = isset($team_details['team_name']) ? $team_details['team_name']: '';
									if(!empty($team_name)) {
										$task_owner = $team_name;
									}
								}
							} else {
								$task_owner = '';
							}
							$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
							$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '0';
							$task_status = isset($task_details['status']) ? $task_details['status'] : '';
							$task_time = isset($task_details['task_time']) ? $task_details['task_time'] : '';
							$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : '';
							$task_priority = isset($task_details['task_priority']) ? $task_details['task_priority'] : '';
							$time_spent_total = 0;
							$time_spent = get_post_meta($task->ID, 'task_time_spent', true);
							if(!empty($time_spent)) {
								foreach($time_spent as $time) {
									$timer = isset($time['time']) ? $time['time']: 0;
									$time_spent_total = $time_spent_total + $timer;
									$project_total_time_spent = $project_total_time_spent + $timer;
								}
							}
							if(!is_numeric($task_deadline)) {
								$str_deadline = str_replace('/','-', $task_deadline);
								$deadline_stamp = strtotime($str_deadline);
							} else {
								$deadline_stamp = $task_deadline;
							}
							$now = time();
							if($task_status != 'complete') {
								if(!empty($deadline_stamp) && $now > $deadline_stamp) {
									$task_status_string = '<span class="cqpim_button cqpim_small_button font-red border-red op nolink rounded_2">' . __('OVERDUE', 'cqpim') . '</span>';
								} else {
									$task_status_string = isset($task_details['status']) ? $task_details['status'] : '';
									if(!$task_status_string || $task_status_string == 'pending') {
										$task_status_string = '<span class="cqpim_button cqpim_small_button font-amber border-amber op nolink rounded_2">' . __('Pending', 'cqpim') . '</span>';
									} else if($task_status_string == 'on_hold') {
										$task_status_string = '<span class="cqpim_button cqpim_small_button font-red border-red op nolink rounded_2">' . __('On Hold', 'cqpim') . '</span>';
									} else if($task_status_string == 'progress') {
										$task_status_string = '<span class="cqpim_button cqpim_small_button font-amber border-amber op nolink rounded_2">' . __('In Progress', 'cqpim') . '</span>';
									}
								}
							} else {
								$task_status_string = '<span class="cqpim_button cqpim_small_button font-green border-green op nolink rounded_2">' . __('Complete', 'cqpim') . '</span>';
							}
							if($task_priority == 'low') {
								$priority_string = '<span class="task_complete">' . __('Low', 'cqpim') . '</span>';
							} elseif($task_priority == 'normal') {
								$priority_string = '<span class="task_complete">' . __('Normal', 'cqpim') . '</span>';
							} elseif($task_priority == 'high') {
								$priority_string = '<span class="task_pending">' . __('High', 'cqpim') . '</span>';
							} elseif($task_priority == 'immediate') {
								$priority_string = '<span class="task_over">' . __('Immediate', 'cqpim') . '</span>';
							} elseif(empty($task_priority)) {
								$priority_string = '';
							}
							unset($hide);							
							if(!empty($quote_details['hide_complete']) && $task_status == 'complete') {
								$hide = true;
							} ?>
							<div <?php if(!empty($hide)) { ?>style="display:none"<?php } ?> class="dd-task">
								<input class="task_weight" type="hidden" name="task_weight_<?php echo $task->ID; ?>" id="task_weight_<?php echo $task->ID; ?>" value="<?php echo $weight; ?>" />
								<input class="task_id" type="hidden" name="task_weight_<?php echo $task->ID; ?>" id="task_weight_<?php echo $task->ID; ?>" value="<?php echo $task->ID; ?>" />
								<span class="table-task cqpim_button cqpim_small_button font-white bg-grey-cascade op nolink rounded_2"><?php _e('Task', 'cqpim'); ?></span> <span class="ms-title"><a href="<?php echo get_edit_post_link($task->ID); ?>"><?php echo $task->post_title; ?></a></span>
								<?php if(current_user_can('cqpim_edit_project_milestones')) { ?>
									<div class="dd-task-actions">
										<button class="delete_task cqpim_button cqpim_small_button font-white bg-red op rounded_2 cqpim_tooltip" data-id="<?php echo $task->ID; ?>" value="<?php echo $task->ID; ?>" title="<?php _e('Delete Task', 'cqpim'); ?>"><i class="fa fa-trash" aria-hidden="true"></i></button> <button class="item_complete cqpim_button cqpim_small_button font-white bg-grey-cascade op rounded_2 cqpim_tooltip" data-type="task" value="<?php echo $task->ID; ?>"title="<?php _e('Mark Task Complete', 'cqpim'); ?>"><i class="fa fa-check" aria-hidden="true"></i></button> <button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue op rounded_2 cqpim_tooltip" value="<?php echo $element['id']; ?>" title="<?php _e('Reorder Task', 'cqpim'); ?>"><i class="fa fa-sort" aria-hidden="true"></i></button>
									</div>
								<?php } ?>
								<div class="dd-task-status">
									<?php echo $task_status_string; ?>
								</div>
								<div class="dd-task-info">
									<?php if(!empty($start)) { ?>
										<strong><?php _e('Start Date:', 'cqpim') ?></strong> <?php echo date(get_option('cqpim_date_format'), $start); ?>
									<?php } ?>
									<?php if(!empty($task_deadline)) { ?>
										<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Deadline:', 'cqpim') ?></strong> <?php echo date(get_option('cqpim_date_format'), $task_deadline); ?>
									<?php } ?>	
									<?php if(!empty($task_est_time)) { ?>
										<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Est. Time:', 'cqpim') ?></strong> <?php echo $task_est_time; ?>
									<?php } ?>
									<?php if(!empty($time_spent_total)) { ?>
										<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Time Spent:', 'cqpim') ?></strong> <?php echo $time_spent_total; ?>	
									<?php } ?>
									<?php if(!empty($task_owner)) { ?>
										<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Assigned To:', 'cqpim') ?></strong> <?php echo $task_owner; ?>
									<?php } ?>
								</div>
								<div class="clear"></div>
								<?php if($task_status != 'pending') { ?>
									<script>
										jQuery(document).ready(function() {
											jQuery( "#progressbar-<?php echo $task->ID; ?>" ).progressbar({
												value: <?php echo number_format((float)$task_pc, 2, '.', ''); ?>
											});
										});
									</script>
									<div class="progress <?php if($task_pc == 100 && $task_status != 'overdue') { echo 'prog-complete'; } ?> <?php if($task_pc != 100 && !empty($deadline_stamp) && $now > $deadline_stamp) { echo 'prog-overdue'; } ?>">
										<div id="progressbar-<?php echo $task->ID; ?>" title="<?php echo $task_pc; ?>%"></div>
									</div>
								<?php } ?>
								<div class="clear"></div>								
								<div class="dd-subtasks">
									<?php 
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
									$subordered = array();
									$sti = 0;
									$ssti = 0;
									foreach($subtasks as $subtask) {
										$task_details = get_post_meta($subtask->ID, 'task_details', true);
										$sweight = isset($task_details['weight']) ? $task_details['weight'] : $sti;
										$subordered[$sweight] = $subtask;
										$sti++;
									}
									ksort($subordered);
									foreach($subordered as $subtask) {
										$task_details = get_post_meta($subtask->ID, 'task_details', true);
										$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
										$start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
										$description = isset($task_details['task_description']) ? $task_details['task_description'] : '';
										$sweight = isset($task_details['weight']) ? $task_details['weight'] : 0;
										$task_owner = get_post_meta($subtask->ID, 'owner', true);
										$client_check = preg_replace('/[0-9]+/', '', $task_owner);
										unset($client);
										if($client_check == 'C') {
											$client = true;
										}
										if($task_owner) {
											if(!empty($client) && $client == true) {
												$id = preg_replace("/[^0-9,.]/", "", $task_owner);
												$client_object = get_user_by('id', $id);
												$task_owner = $client_object->display_name;
											} else {
												$team_details = get_post_meta($task_owner, 'team_details', true);
												$team_name = isset($team_details['team_name']) ? $team_details['team_name']: '';
												if(!empty($team_name)) {
													$task_owner = $team_name;
												}
											}
										} else {
											$task_owner = '';
										}
										$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
										$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '0';
										$task_status = isset($task_details['status']) ? $task_details['status'] : '';
										$task_time = isset($task_details['task_time']) ? $task_details['task_time'] : '';
										$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : '';
										$task_priority = isset($task_details['task_priority']) ? $task_details['task_priority'] : '';
										$time_spent_total = 0;
										$time_spent = get_post_meta($subtask->ID, 'task_time_spent', true);
										if(!empty($time_spent)) {
											foreach($time_spent as $time) {
												$timer = isset($time['time']) ? $time['time']: 0;
												$time_spent_total = $time_spent_total + $timer;
												$project_total_time_spent = $project_total_time_spent + $timer;
											}
										}
										if(!is_numeric($task_deadline)) {
											$str_deadline = str_replace('/','-', $task_deadline);
											$deadline_stamp = strtotime($str_deadline);
										} else {
											$deadline_stamp = $task_deadline;
										}
										$now = time();
										if($task_status != 'complete') {
										if(!empty($deadline_stamp) && $now > $deadline_stamp) {
											$task_status_string = '<span class="cqpim_button cqpim_small_button font-red border-red op nolink rounded_2">' . __('OVERDUE', 'cqpim') . '</span>';
										} else {
											$task_status_string = isset($task_details['status']) ? $task_details['status'] : '';
											if(!$task_status_string || $task_status_string == 'pending') {
												$task_status_string = '<span class="cqpim_button cqpim_small_button font-amber border-amber op nolink rounded_2">' . __('Pending', 'cqpim') . '</span>';
											} else if($task_status_string == 'on_hold') {
												$task_status_string = '<span class="cqpim_button cqpim_small_button font-red border-red op nolink rounded_2">' . __('On Hold', 'cqpim') . '</span>';
											} else if($task_status_string == 'progress') {
												$task_status_string = '<span class="cqpim_button cqpim_small_button font-amber border-amber op nolink rounded_2">' . __('In Progress', 'cqpim') . '</span>';
											}
										}
									} else {
										$task_status_string = '<span class="cqpim_button cqpim_small_button font-green border-green op nolink rounded_2">' . __('Complete', 'cqpim') . '</span>';
									}
										if($task_priority == 'low') {
											$priority_string = '<span class="task_complete">' . __('Low', 'cqpim') . '</span>';
										} elseif($task_priority == 'normal') {
											$priority_string = '<span class="task_complete">' . __('Normal', 'cqpim') . '</span>';
										} elseif($task_priority == 'high') {
											$priority_string = '<span class="task_pending">' . __('High', 'cqpim') . '</span>';
										} elseif($task_priority == 'immediate') {
											$priority_string = '<span class="task_over">' . __('Immediate', 'cqpim') . '</span>';
										} elseif(empty($task_priority)) {
											$priority_string = '';
										}
										unset($hide);							
										if(!empty($quote_details['hide_complete']) && $task_status == 'complete') {
											$hide = true;
										} ?>
										<div <?php if(!empty($hide)) { ?>style="display:none"<?php } ?> class="dd-subtask">
											<input class="task_weight" type="hidden" name="task_weight_<?php echo $subtask->ID; ?>" id="task_weight_<?php echo $subtask->ID; ?>" value="<?php echo $sweight; ?>" />
											<input class="task_id" type="hidden" name="task_weight_<?php echo $subtask->ID; ?>" id="task_weight_<?php echo $subtask->ID; ?>" value="<?php echo $subtask->ID; ?>" />
											<span class="table-task cqpim_button cqpim_small_button font-white bg-grey-cascade op nolink rounded_2"><?php _e('Subtask', 'cqpim'); ?></span>  <span class="ms-title"><a href="<?php echo get_edit_post_link($subtask->ID); ?>"><?php echo $subtask->post_title; ?></a></span>
											<?php if(current_user_can('cqpim_edit_project_milestones')) { ?>
												<div class="dd-task-actions">
													<button class="delete_task cqpim_button cqpim_small_button font-white bg-red op rounded_2 cqpim_tooltip" data-id="<?php echo $subtask->ID; ?>" value="<?php echo $subtask->ID; ?>"><i class="fa fa-trash" aria-hidden="true" title="<?php _e('Delete Task', 'cqpim'); ?>"></i></button> <button class="item_complete cqpim_button cqpim_small_button font-white bg-grey-cascade op rounded_2 cqpim_tooltip" data-type="task" value="<?php echo $subtask->ID; ?>" title="<?php _e('Mark Task Complete', 'cqpim'); ?>"><i class="fa fa-check" aria-hidden="true"></i></button> <button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue op rounded_2 cqpim_tooltip" value="<?php echo $element['id']; ?>" title="<?php _e('Reorder Task', 'cqpim'); ?>"><i class="fa fa-sort" aria-hidden="true"></i></button>
												</div>
											<?php } ?>
											<div class="dd-task-status">
												<?php echo $task_status_string; ?>
											</div>
											<div class="dd-task-info">
												<?php if(!empty($start)) { ?>
													<strong><?php _e('Start Date:', 'cqpim') ?></strong> <?php echo date(get_option('cqpim_date_format'), $start); ?>
												<?php } ?>
												<?php if(!empty($task_deadline)) { ?>
													<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Deadline:', 'cqpim') ?></strong> <?php echo date(get_option('cqpim_date_format'), $task_deadline); ?>
												<?php } ?>	
												<?php if(!empty($task_est_time)) { ?>
													<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Est. Time:', 'cqpim') ?></strong> <?php echo $task_est_time; ?>
												<?php } ?>
												<?php if(!empty($time_spent_total)) { ?>
													<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Time Spent:', 'cqpim') ?></strong> <?php echo $time_spent_total; ?>	
												<?php } ?>
												<?php if(!empty($task_owner)) { ?>
													<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Assigned To:', 'cqpim') ?></strong> <?php echo $task_owner; ?>
												<?php } ?>
											</div>
											<div class="clear"></div>
											<?php if($task_status != 'pending') { ?>
												<script>
													jQuery(document).ready(function() {
														jQuery( "#progressbar-<?php echo $subtask->ID; ?>" ).progressbar({
															value: <?php echo number_format((float)$task_pc, 2, '.', ''); ?>
														});
													});
												</script>
												<div class="progress <?php if($task_pc == 100 && $task_status != 'overdue') { echo 'prog-complete'; } ?> <?php if($task_pc != 100 && !empty($deadline_stamp) && $now > $deadline_stamp) { echo 'prog-overdue'; } ?>">
													<div id="progressbar-<?php echo $subtask->ID; ?>" title="<?php echo $task_pc; ?>%"></div>
												</div>
											<?php } ?>
											<div class="clear"></div>
										</div>
									<?php $ssti++; } ?>
								</div>	
								<div id="add-subtask-div-<?php echo $task->ID; ?>-container" style="display:none">
									<div id="add-subtask-div-<?php echo $task->ID; ?>" class="add-task-div">
										<div style="padding:12px">
											<h3><?php _e('Add Subtask', 'cqpim'); ?></h3>
											<input class="task_weight" type="hidden" name="task_weight_<?php echo $task->ID; ?>" id="task_weight_<?php echo $task->ID; ?>" value="<?php echo $sweight + 1; ?>" />
											<input type="hidden" id="task_parent_id_<?php echo $task->ID; ?>" name="task_parent_id_<?php echo $task->ID; ?>" value="<?php echo $task->ID; ?>" />
											<input type="hidden" id="task_milestone_id_<?php echo $task->ID; ?>" name="task_milestone_id_<?php echo $task->ID; ?>" value="<?php echo $element['id']; ?>" />
											<input type="hidden" id="task_project_id_<?php echo $task->ID; ?>" name="task_project_id_<?php echo $task->ID; ?>" value="<?php echo $post->ID; ?>" />
											<input style="width:100%" type="text" name="task_title_<?php echo $task->ID; ?>" id="task_title_<?php echo $task->ID; ?>" placeholder="<?php _e('Task title', 'cqpim'); ?>"/><br /><br />					
											<textarea style="width:100%;height:100px" name="task_description_<?php echo $task->ID; ?>" id="task_description_<?php echo $task->ID; ?>" placeholder="<?php _e('Task description', 'cqpim'); ?>"></textarea><br /><br />
											<input style="width:100%" class="datepicker" type="text" name="task_start_<?php echo $task->ID; ?>" id="task_start_<?php echo $task->ID; ?>" placeholder="<?php _e('Start Date', 'cqpim'); ?>"/>
											<input style="width:100%; border-top:0" class="datepicker" type="text" name="task_finish_<?php echo $task->ID; ?>" id="task_finish_<?php echo $task->ID; ?>" placeholder="<?php _e('Deadline', 'cqpim'); ?>"/><br /><br />
											<input style="width:100%" type="text" name="task_time_<?php echo $task->ID; ?>" id="task_time_<?php echo $task->ID; ?>" placeholder="<?php _e('Estimated Time (in decimal format, eg. 4.5 hours)', 'cqpim'); ?>"/>																		
											<div class="clear"></div>
											<?php
											$project_contributors = get_post_meta($post->ID, 'project_contributors', true);
											?>
											<br />
											<select name="task_owner_<?php echo $task->ID; ?>" id="task_owner_<?php echo $task->ID; ?>">
											<optgroup label="<?php _e('Team Members', 'cqpim'); ?>">
												<?php if(empty($project_contributors)) { ?>
													<option value=""><?php _e('Me', 'cqpim'); ?></option>
												<?php } ?>
												<?php
												foreach($project_contributors as $contributor) {
													$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
													$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
													$team_job = isset($team_details['team_job']) ? $team_details['team_job'] : '';
													echo '<option value="' . $contributor['team_id'] . '">' . $team_name . ' - ' . $team_job . '</option>';
												}
												?>
											</optgroup>
											<optgroup label="<?php _e('Client', 'cqpim'); ?>">
												<?php
												$project_details = get_post_meta($post->ID, 'project_details', true);
												$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
												$client_ids = get_post_meta($client_id, 'client_ids', true);
												?>
												<?php foreach($client_ids as $id) { ?>
													<?php 
													$client = get_user_by('id', $id); 
													if($owner == 'C' . $client->ID) { $selected = 'selected="selected"'; } else { $selected = ''; }
													?>
													<option value="C<?php echo $client->ID; ?>" <?php echo $selected; ?>><?php echo $client->display_name; ?></option>
												<?php } ?>
											</optgroup>
											</select>												
											<div class="clear"></div>
											<br /><br />
											<button class="cancel-colorbox cqpim_button op font-red border-red"><?php _e('Cancel', 'cqpim'); ?></button>
											<button class="save-subtask metabox-add-button cqpim_button font-green border-green right op" value="<?php echo $task->ID; ?>"><?php _e('Add Subtask', 'cqpim'); ?></button>
											<div class="clear"></div>
											<div id="task-messages-<?php echo $task->ID; ?>"></div>
										</div>
									</div>	
								</div>									
							</div>
						<?php $ti++;
						} 
						if($ti == 0) {
							_e('No tasks added to this Milestone', 'cqpim');
						}
						?>
						<?php if(!empty($quote_details['hide_complete']) && $quote_details['hide_complete']) { ?>
							<div class="dd-task">
								<?php _e('Completed tasks are hidden. To see all tasks, click "Show Completed Tasks"', 'cqpim'); ?>
							</div>
						<?php } ?>
					</div>
				</div>
				<?php if(current_user_can('cqpim_edit_project_milestones')) { ?>
					<div id="delete-milestone-div-<?php echo $element['id']; ?>-container" style="display:none">
						<div id="delete-milestone-div-<?php echo $element['id']; ?>" class="delete-milestone-div">
							<div style="padding:12px">
								<h3><?php _e('Are you sure?', 'cqpim'); ?></h3>
								<p><?php _e('Deleting this milestone will also delete related tasks. Are you sure you want to do this?', 'cqpim'); ?></p>
								<button class="cancel_delete_stage cqpim_button op font-red border-red"><?php _e('Cancel', 'cqpim'); ?></button> <button class="delete_stage metabox-add-button cqpim_button font-green border-green right op" data-id="<?php echo $element['id']; ?>" value="<?php echo $element['id']; ?>"><?php _e('Delete', 'cqpim'); ?></button>
							</div>
						</div>
					</div>
					<div id="add-task-div-<?php echo $element['id']; ?>-container" style="display:none">
						<div id="add-task-div-<?php echo $element['id']; ?>" class="add-task-div">
							<div style="padding:12px">
								<h3><?php _e('Add Task', 'cqpim'); ?></h3>
								<input class="task_weight" type="hidden" name="task_weight_<?php echo $element['id']; ?>" id="task_weight_<?php echo $element['id']; ?>" value="<?php echo $weight + 1; ?>" />
								<input type="hidden" id="task_milestone_id_<?php echo $element['id']; ?>" name="task_milestone_id_<?php echo $element['id']; ?>" value="<?php echo $element['id']; ?>" />
								<input type="hidden" id="task_project_id_<?php echo $element['id']; ?>" name="task_project_id_<?php echo $element['id']; ?>" value="<?php echo $post->ID; ?>" />
								<input style="width:100%" type="text" name="task_title_<?php echo $element['id']; ?>" id="task_title_<?php echo $element['id']; ?>" placeholder="<?php _e('Task title', 'cqpim'); ?>"/><br /><br />					
								<textarea style="width:100%;height:100px" name="task_description_<?php echo $element['id']; ?>" id="task_description_<?php echo $element['id']; ?>" placeholder="<?php _e('Task description', 'cqpim'); ?>"></textarea><br /><br />
								<input style="width:100%;" class="datepicker" type="text" name="task_start_<?php echo $element['id']; ?>" id="task_start_<?php echo $element['id']; ?>" placeholder="<?php _e('Start Date', 'cqpim'); ?>"/>
								<input style="width:100%; border-top:0" class="datepicker" type="text" name="task_finish_<?php echo $element['id']; ?>" id="task_finish_<?php echo $element['id']; ?>" placeholder="<?php _e('Deadline', 'cqpim'); ?>"/><br /><br />
								<input style="width:100%" type="text" name="task_time_<?php echo $element['id']; ?>" id="task_time_<?php echo $element['id']; ?>" placeholder="<?php _e('Estimated Time (in decimal format, eg. 4.5 hours)', 'cqpim'); ?>"/>									
								<div class="clear"></div>
								<?php
								$project_contributors = get_post_meta($post->ID, 'project_contributors', true);
								?>
								<br />
								<select name="task_owner_<?php echo $element['id']; ?>" id="task_owner_<?php echo $element['id']; ?>">
								<optgroup label="<?php _e('Team Members', 'cqpim'); ?>">
									<?php if(empty($project_contributors)) { ?>
										<option value=""><?php _e('Me', 'cqpim'); ?></option>
									<?php } ?>
									<?php
									foreach($project_contributors as $contributor) {
										$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
										$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
										$team_job = isset($team_details['team_job']) ? $team_details['team_job'] : '';
										echo '<option value="' . $contributor['team_id'] . '">' . $team_name . ' - ' . $team_job . '</option>';
									}
									?>
								</optgroup>
								<optgroup label="<?php _e('Client', 'cqpim'); ?>">
									<?php
									$project_details = get_post_meta($post->ID, 'project_details', true);
									$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
									$client_ids = get_post_meta($client_id, 'client_ids', true);
									?>
									<?php foreach($client_ids as $id) { ?>
										<?php 
										$client = get_user_by('id', $id); 
										if($owner == 'C' . $client->ID) { $selected = 'selected="selected"'; } else { $selected = ''; }
										?>
										<option value="C<?php echo $client->ID; ?>" <?php echo $selected; ?>><?php echo $client->display_name; ?></option>
									<?php } ?>
								</optgroup>
								</select>
								<br /><br />
								<button class="cancel-colorbox cqpim_button op font-red border-red"><?php _e('Cancel', 'cqpim'); ?></button>
								<button class="save-task metabox-add-button cqpim_button font-green border-green right op" value="<?php echo $element['id']; ?>"><?php _e('Add Task', 'cqpim'); ?></button>
								<div class="clear"></div>
								<div id="task-messages-<?php echo $element['id']; ?>"></div>
							</div>
						</div>	
					</div>
					<div id="edit-milestone-<?php echo $element['id']; ?>-container" style="display:none">
						<div id="edit-milestone-<?php echo $element['id']; ?>" class="edit_milestone">
							<div style="padding:12px">
								<h3><?php _e('Edit Milestone', 'cqpim'); ?></h3>
								<input type="hidden" name="added_element_milestone_id[<?php echo $element['id']; ?>]" class="element_id" id="added_element_milestone_id[<?php echo $element['id']; ?>]" value="<?php echo $element['id']; ?>" />
								<span class="label"><strong><?php _e('Title:', 'cqpim'); ?></strong></span>
								<input type="text" name="added_element_title[<?php echo $element['id']; ?>]" id="added_element_title[<?php echo $element['id']; ?>]" value="<?php echo $element['title']; ?>" />
								<br /><br />
								<span class="label"><strong><?php _e('Start Date:', 'cqpim'); ?></strong></span>
								<input class="datepicker" type="text" name="added_element_start[<?php echo $element['id']; ?>]" id="added_element_start[<?php echo $element['id']; ?>]" value="<?php if(is_numeric($element['start'])) { echo date(get_option('cqpim_date_format'), $element['start']); } else { echo $element['start']; } ?>" />
								<br /><br />
								<span class="label"><strong><?php _e('Deadline:', 'cqpim'); ?></strong></span>
								<input class="datepicker" type="text" name="added_element_finish[<?php echo $element['id']; ?>]" id="added_element_finish[<?php echo $element['id']; ?>]" value="<?php if(is_numeric($element['deadline'])) { echo date(get_option('cqpim_date_format'), $element['deadline']); } else { echo $element['deadline']; } ?>" />
								<br /><br />
								<span class="label"><strong><?php _e('Cost:', 'cqpim'); ?></strong></span>
								<?php if(current_user_can('cqpim_view_project_financials')) { ?>
								<input type="text" name="added_element_cost[<?php echo $element['id']; ?>]" id="added_element_cost[<?php echo $element['id']; ?>]" value="<?php echo $element['cost']; ?>" />
								<br /><br />
								<?php $acost = isset($element['acost']) ? $element['acost'] : ''; ?>
								<span class="label"><strong><?php _e('Finished Cost:', 'cqpim'); ?></strong></span>
								<input class="finished-<?php echo $key; ?>" type="text" name="added_element_acost[<?php echo $element['id']; ?>]" id="added_element_acost[<?php echo $element['id']; ?>]" value="<?php echo $acost; ?>" />
								<?php } ?>
								<br /><br />							
								<span class="label"><strong><?php _e('Status:', 'cqpim'); ?></strong></span>
								<?php if(empty($element['status'])) { $element['status'] = 'pending'; } ?>
								<select class="status-<?php echo $key; ?>" name="added_element_status[<?php echo $element['id']; ?>]" id="added_element_status[<?php echo $element['id']; ?>]">
									<option value="pending" <?php if(!$element['status'] || $element['status'] == 'pending') { echo 'selected="selected"'; } ?>><?php _e('Pending', 'cqpim'); ?></option>
									<option value="on_hold" <?php if($element['status'] == 'on_hold') { echo 'selected="selected"'; } ?>><?php _e('On Hold', 'cqpim'); ?></option>
									<option value="complete" <?php if($element['status'] == 'complete') { echo 'selected="selected"'; } ?>><?php _e('Complete', 'cqpim'); ?></option>
								</select>
								<?php 
								$checked = get_option('invoice_workflow');
								if($checked == 1) {
									echo '<p>' . __('Marking this milestone as complete will generate an invoice for the Finished cost, <br />minus the deposit percentage (if set)', 'cqpim') . '</p>';
								}
								?>
								<br /><br />
								<div id="update-ms-message-<?php echo $key; ?>"></div>
								<button class="cancel-colorbox cqpim_button op font-red border-red"><?php _e('Cancel', 'cqpim'); ?></button>
								<button class="save-milestone metabox-add-button cqpim_button font-green border-green right op"><?php _e('Save', 'cqpim'); ?></button>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php $i++;
			}
			echo '</div>';
			?>
		<?php }
	} else {
		_e('Please update this post before adding milestones', 'cqpim');
	}
	$quote_details = get_post_meta($post->ID, 'project_details', true); ?>
	<?php if(!empty($project_total_time_spent)) { ?>
		<div id="project_total_time" class="dd-milestone">
			<?php echo sprintf(__('Total Ticket Time: %1$s hours', 'cqpim'), $project_total_time_spent); ?>
		</div>
	<?php } ?>
	<?php if(!empty($milestones_added)) { ?>
		<a id="send_ticket_invoice" class="mt-20 cqpim_button cqpim_small_button border-blue font-blue right" <?php if(!empty($outstanding)) { ?>disabled="true"<?php } ?>><?php _e('Generate Invoice', 'cqpim'); ?></a> 
	<?php } ?>
	<?php if(empty($quote_details['hide_complete'])) { ?>
		<a id="toggle_all_tasks" class="mt-20 cqpim_button cqpim_small_button border-blue font-blue right"><?php _e('Hide Completed Tasks', 'cqpim'); ?></a> 
	<?php } else {
		if(!empty($quote_details['hide_complete']) && $quote_details['hide_complete'] == true) { ?>
			<a id="toggle_all_tasks" class="mt-20 cqpim_button cqpim_small_button border-blue font-blue right"><?php _e('Show Completed Tasks', 'cqpim'); ?></a> 
		<?php } else { ?>
			<a id="toggle_all_tasks" class="mt-20 cqpim_button cqpim_small_button border-blue font-blue right"><?php _e('Show Completed Tasks', 'cqpim'); ?></a> 
		<?php }
	} ?>
	<?php if(current_user_can('cqpim_apply_project_templates')) { ?>
		<a href="#apply-template" id="apply-template" class="mt-20 cqpim_button cqpim_small_button border-blue font-blue right colorbox"><?php _e('Apply Milestone Template', 'cqpim'); ?></a>
	<?php } ?>
	<a href="#add-milestone-div" id="add-milestone" class="mt-20 cqpim_button cqpim_small_button border-blue font-blue right colorbox"><?php _e('Add Milestone', 'cqpim'); ?></a>
	<a href="#clear-all" id="clear-all" class="mt-20 cqpim_button cqpim_small_button border-blue font-blue right colorbox"><?php _e('Clear All Milestones / Tasks', 'cqpim'); ?></a>
	<div class="clear"></div>
	<div id="apply-template-div-container" style="display:none">
		<div id="apply-template-div">
			<div style="padding:12px">
				<h3><?php _e('Apply Milestone / Task Template', 'cqpim'); ?></h3>
				<?php
				$elements = get_post_meta($post->ID, 'quote_elements', true);
				if(empty($elements)) {
					echo '<p>' . _e('Choose a template to apply Milestones and Tasks from.', 'cqpim') . '</p>';
				} else {
					echo '<p>' . _e('You already have Milestones and Tasks on this ticket, applying a template will overwrite them with the contents of the template.', 'cqpim') . '</p>';						
				}
				$args = array(
					'post_type' => 'cqpim_templates',
					'posts_per_page' => -1,
					'post_status' => 'private'
				);
				$templates = get_posts($args);
				?>
				<select id="template_choice">
					<option value=""><?php _e('Choose a template...', 'cqpim'); ?></option>
					<?php foreach($templates as $template) { ?>
						<option value="<?php echo $template->ID; ?>"><?php echo $template->post_title; ?></option>
					<?php } ?>
				</select>
				<br /><br />
				<button class="cancel-colorbox cqpim_button op font-red border-red"><?php _e('Cancel', 'cqpim'); ?></button>
				<button id="apply-template-action" class="metabox-add-button cqpim_button font-green border-green right op" data-type="project" value="<?php echo $post->ID; ?>"><?php _e('Apply Template', 'cqpim'); ?></button> <div id="template_spinner" class="ajax_spinner" style="display:none"></div>
				<div class="clear"></div>
				<br />
				<div id="apply-template-messages"></div>
			</div>
		</div>
	</div>
	<div id="clear-all-div-container" style="display:none">
		<div id="clear-all-div">
			<div style="padding:12px">
				<h3><?php _e('Clear All', 'cqpim'); ?></h3>
				<p><?php _e('Are you sure you want to clear all Milestones and Tasks? This cannot be undone.', 'cqpim'); ?></p>
				<br /><br />
				<button class="cancel-colorbox cqpim_button op font-red border-red"><?php _e('Cancel', 'cqpim'); ?></button>
				<button id="clear-all-action" class="metabox-add-button cqpim_button font-green border-green right op" data-type="project" value="<?php echo $post->ID; ?>"><?php _e('Clear All', 'cqpim'); ?></button> <div id="clear_spinner" class="ajax_spinner" style="display:none"></div>
				<div class="clear"></div>
				<br />
				<div id="clear-all-messages"></div>
				</div>
		</div>
	</div>
	<div id="add-milestone-div-container" style="display:none">
		<div id="add-milestone-div">
			<div style="padding:12px">
				<h3><?php _e('Add Milestone', 'cqpim'); ?></h3>
				<input style="width:100%" type="text" name="quote_element_title" id="quote_element_title" placeholder="<?php _e('Milestone title, eg. \'Design Phase\'', 'cqpim'); ?>"/>
				<?php 
				$new_id = isset($highest_key) ?  $highest_key + 1 : 0; 
				$new_weight = isset($highest_key) ? $highest_key + 1 : 0;
				?>
				<input type="hidden" id="add_milestone_id" name="add_milestone_id" value="<?php echo $post->ID . '-' . $new_id; ?>" />
				<input type="hidden" id="add_milestone_order" name="add_milestone_order" value="<?php echo $new_weight; ?>" />
				<input style="width:100%" class="datepicker" type="text" name="quote_element_start" id="quote_element_start" placeholder="<?php _e('Start', 'cqpim'); ?>"/>
				<input style="width:100%" class="datepicker" type="text" name="quote_element_finish" id="quote_element_finish" placeholder="<?php _e('Deadline', 'cqpim'); ?>"/>
				<input style="width:100%" type="text" name="quote_element_cost" id="quote_element_cost" placeholder="<?php _e('Cost', 'cqpim'); ?>"/>
				<br /><br />
				<button class="cancel-colorbox cqpim_button op font-red border-red"><?php _e('Cancel', 'cqpim'); ?></button>
				<button id="add_quote_element" class="metabox-add-button cqpim_button font-green border-green right op"><?php _e('Add milestone to Ticket', 'cqpim'); ?></button> <div class="ajax_spinner" style="display:none"></div>
			</div>
		</div>	
	</div>
	<?php
}
add_action( 'save_post', 'save_pto_support_elements_metabox_data' );
function save_pto_support_elements_metabox_data( $post_id ){
	if ( ! isset( $_POST['support_elements_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['support_elements_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'support_elements_metabox' ) )
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
	$quote_elements = get_post_meta($post_id, 'quote_elements', true);
	if(isset($_POST['added_element_title'])) {
		$title_array = $_POST['added_element_title'];
		$quote_elements = get_post_meta($post_id, 'quote_elements', true);
		foreach($title_array as $key => $to_add) {
			$title = $to_add;
			$milestone_id = $_POST['added_element_milestone_id'][$key];
			$milestone_start = $_POST['added_element_start'][$key];			
			$milestone_start = pto_convert_date($milestone_start);
			$milestone_deadline = $_POST['added_element_finish'][$key];
			$milestone_deadline = pto_convert_date($milestone_deadline);
			$milestone_weight = $_POST['element_weight'][$key];
			$cost = $_POST['added_element_cost'][$key];
			$acost = $_POST['added_element_acost'][$key];
			$status = $_POST['added_element_status'][$key];
			$already_comp = isset($quote_elements[$key]['already_comp']) ? $quote_elements[$key]['already_comp'] : false;
			$quote_elements[$key] = array(
				'deadline' => $milestone_deadline,
				'title' => $title,
				'start' => $milestone_start,
				'id' => $milestone_id,
				'cost' => $cost,
				'acost' => $acost,
				'status' => $status,
				'already_comp' => $already_comp,
				'weight' => $milestone_weight
			);
		}
		update_post_meta($post_id, 'quote_elements', $quote_elements);
	}
	if(isset($_POST['delete_stage'])) {
		$stages_to_delete = $_POST['delete_stage'];
		$quote_elements = get_post_meta($post_id, 'quote_elements', true);
		foreach($stages_to_delete as $key => $delete) {
			unset($quote_elements[$delete]);
			$args = array(
				'post_type' => 'cqpim_tasks',
				'posts_per_page' => -1,
				'meta_key' => 'milestone_id',
				'meta_value' => $delete,
				'orderby' => 'date',
				'order' => 'ASC'
			);
			$tasks = get_posts($args);
			foreach($tasks as $task) {
				wp_delete_post($task->ID);		
			}
		}
		$new_quote_elements = array();
		foreach($quote_elements as $key => $element) {
			$new_quote_elements[$key] = $element;
		}
		update_post_meta($post_id, 'quote_elements', $new_quote_elements);
	}
}