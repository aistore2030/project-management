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
	'page' => sprintf(__('Project %1$s - %2$s (Milestones Page)', 'cqpim'), get_the_ID(), $title)
);
update_post_meta($assigned, 'client_logs', $client_logs);
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-full grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php _e('Milestones & Tasks', 'cqpim'); ?></span>
				</div>	
			</div>
		<?php if(empty($project_elements)) {
			echo '<p style="padding:30px">' . __('There are no Milestones or tasks on this project', 'cqpim') . '</p>';
			$milestone_counter = 0;
		} else {
			$milestone_counter = 0;
			$currency = get_option('currency_symbol');
			if($type == 'estimate') { 
				$cost_title = __('Estimated Cost', 'cqpim');
			} else {
				$cost_title = __('Cost', 'cqpim');
			}
			echo '<table class="cqpim_table">';
			echo '<thead>';
			echo '<tr><th>' . __('Type', 'cqpim') . '</th><th>' . __('Title', 'cqpim') . '</th><th>' . __('Start Date', 'cqpim') . '</th><th>' . __('Deadline', 'cqpim') . '</th><th>' . __('Progress / Status', 'cqpim') . '</th>';
			echo '</thead>';
			$ordered = array();
			$i = 0;
			$mi = 0;
			foreach($project_elements as $key => $element) {
				$weight = isset($element['weight']) ? $element['weight'] : $mi;
				$ordered[$weight] = $element;
				$mi++;
			}
			ksort($ordered);						
			foreach($ordered as $element) { 
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
					if($deadline_stamp && $now > $deadline_stamp) {
						$milestone_status_string = '<span class="cqpim_button cqpim_small_button border-red font-red sbold op nolink">' . __('OVERDUE', 'cqpim') . '</span>';
					} else {
						$milestone_status_string = isset($element['status']) ? $element['status'] : '';
						if(!$milestone_status_string || $milestone_status_string == 'pending') {
							$milestone_status_string = '<span class="cqpim_button cqpim_small_button border-amber font-amber op nolink">' . __('Pending', 'cqpim') . '</span>';
						} else if($milestone_status_string == 'on_hold') {
							$milestone_status_string = '<span class="cqpim_button cqpim_small_button border-blue font-blue op nolink">' . __('On Hold', 'cqpim') . '</span>';
						}
					}
				} else {
					$milestone_status_string = '<span class="cqpim_button cqpim_small_button border-green font-green op nolink">' . __('Complete', 'cqpim') . '</span>';
				}
				if(is_numeric($element['start'])) { $start = date(get_option('cqpim_date_format'), $element['start']); } else { $start = $element['start']; }
				if(is_numeric($element['deadline'])) { $deadline = date(get_option('cqpim_date_format'), $element['deadline']); } else { $deadline = $element['deadline']; }
				?>
				<tr class="milestone">
					<td><span class="cqpim_button cqpim_small_button bg-dark-blue font-white op nolink rounded_2"><?php _e('Milestone', 'cqpim'); ?></span></td>
					<td><span class="nodesktop"><strong><?php _e('Title', 'cqpim'); ?></strong>: </span> <?php echo $element['title']; ?></td>
					<td><span class="nodesktop"><strong><?php _e('Start Date', 'cqpim'); ?></strong>: </span> <?php echo $start; ?></td>
					<td><span class="nodesktop"><strong><?php _e('Deadline', 'cqpim'); ?></strong>: </span> <?php echo $deadline; ?></td>
					<td><?php echo $milestone_status_string; ?></td>
				</tr>
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
					$task_owner = get_post_meta($task->ID, 'owner', true);
					$client_check = preg_replace('/[0-9]+/', '', $task_owner);
					if($client_check == 'C') {
						$client = true;
					} else {
						$client = false;
					}
					if($task_owner) {
						if($client == true) {
							$id = preg_replace("/[^0-9,.]/", "", $task_owner);
							$client_object = get_user_by('id', $id);
							$task_owner = ' (' . $client_object->display_name . ')';
						} else {
							$team_details = get_post_meta($task_owner, 'team_details', true);
							$team_name = isset($team_details['team_name']) ? $team_details['team_name']: '';
							if(!empty($team_name)) {
								$task_owner = ' (' . $team_name . ')';
							}
						}
					} else {
						$task_owner = '';
					}
					$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
					$task_status = isset($task_details['status']) ? $task_details['status'] : '';
					$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '0';
					if(!is_numeric($task_deadline)) {
						$str_deadline = str_replace('/','-', $task_deadline);
						$deadline_stamp = strtotime($str_deadline);
					} else {
						$deadline_stamp = $task_deadline;
					}
					if(is_numeric($task_deadline)) { $task_deadline = date(get_option('cqpim_date_format'), $task_deadline); } else { $task_deadline = $task_deadline; }
					$now = time();
					if($task_status != 'complete') {
						if($deadline_stamp && $now > $deadline_stamp) {
							$task_status_string = '<span class="cqpim_button cqpim_small_button border-red font-red sbold op nolink">' . __('OVERDUE', 'cqpim') . ' - ' . $task_pc . '%</span>';
						} else {
							$task_status_string = isset($task_details['status']) ? $task_details['status'] : '';
							if(!$task_status_string || $task_status_string == 'pending') {
								$task_status_string = '<span class="cqpim_button cqpim_small_button border-amber font-amber op nolink">' . __('Pending', 'cqpim') . ' - ' . $task_pc . '%</span>';
							} else if($task_status_string == 'on_hold') {
								$task_status_string = '<span class="cqpim_button cqpim_small_button border-blue font-blue op nolink">' . __('On Hold', 'cqpim') . ' - ' . $task_pc . '%</span>';
							} else if($task_status_string == 'progress') {
								$task_status_string = '<span class="cqpim_button cqpim_small_button border-green font-green op nolink">' . __('In Progress', 'cqpim') . ' - ' . $task_pc . '%</span>';
							}
						}
					} else {
						$task_status_string = '<span class="cqpim_button cqpim_small_button border-green font-green op nolink">' . __('Complete', 'cqpim') . ' - ' . $task_pc . '%</span>';
					}
					?>						
					<tr>
						<td><span class="cqpim_button cqpim_small_button bg-grey-cascade font-white op nolink rounded_2"><?php _e('Task', 'cqpim'); ?></span></td>
						<td colspan="2"><span class="nodesktop"><strong><?php _e('Title', 'cqpim'); ?></strong>: </span><a class="cqpim-link" href="<?php echo get_the_permalink($task->ID); ?>"><?php echo $task->post_title; ?></a><br /><?php echo $task_owner; ?></td>
						<td><span class="nodesktop"><strong><?php _e('Deadline', 'cqpim'); ?></strong>: </span><?php echo $task_deadline; ?></td>
						<td style="text-transform:capitalize"><?php echo $task_status_string; ?></td>
					</tr>
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
					$sti = 0;
					$subordered = array();
					$swi = 0;
					foreach($subtasks as $subtask) {
						$stask_details = get_post_meta($subtask->ID, 'task_details', true);
						$sweight = isset($stask_details['weight']) ? $stask_details['weight'] : $swi;
						$subordered[$sweight] = $subtask;
						$swi++;
					}
					ksort($subordered);
					foreach($subordered as $subtask) {
						$task_details = get_post_meta($subtask->ID, 'task_details', true);
						$task_owner = get_post_meta($subtask->ID, 'owner', true);
						$client_check = preg_replace('/[0-9]+/', '', $task_owner);
						if($client_check == 'C') {
							$client = true;
						}
						if($task_owner) {
							if($client == true) {
								$id = preg_replace("/[^0-9,.]/", "", $task_owner);
								$client_object = get_user_by('id', $id);
								$task_owner = ' (' . $client_object->display_name . ')';
							} else {
								$team_details = get_post_meta($task_owner, 'team_details', true);
								$team_name = isset($team_details['team_name']) ? $team_details['team_name']: '';
								if(!empty($team_name)) {
									$task_owner = ' (' . $team_name . ')';
								}
							}
						} else {
							$task_owner = '';
						}
						$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
						$task_status = isset($task_details['status']) ? $task_details['status'] : '';
						$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '0';
						if(!is_numeric($task_deadline)) {
							$str_deadline = str_replace('/','-', $task_deadline);
							$deadline_stamp = strtotime($str_deadline);
						} else {
							$deadline_stamp = $task_deadline;
						}
						if(is_numeric($task_deadline)) { $task_deadline = date(get_option('cqpim_date_format'), $task_deadline); } else { $task_deadline = $task_deadline; }
						$now = time();
						if($task_status != 'complete') {
							if($deadline_stamp && $now > $deadline_stamp) {
								$task_status_string = '<span class="cqpim_button cqpim_small_button border-red font-red sbold op nolink">' . __('OVERDUE', 'cqpim') . ' - ' . $task_pc . '%</span>';
							} else {
								$task_status_string = isset($task_details['status']) ? $task_details['status'] : '';
								if(!$task_status_string || $task_status_string == 'pending') {
									$task_status_string = '<span class="cqpim_button cqpim_small_button border-amber font-amber op nolink">' . __('Pending', 'cqpim') . ' - ' . $task_pc . '%</span>';
								} else if($task_status_string == 'on_hold') {
									$task_status_string = '<span class="cqpim_button cqpim_small_button border-blue font-blue op nolink">' . __('On Hold', 'cqpim') . ' - ' . $task_pc . '%</span>';
								} else if($task_status_string == 'progress') {
									$task_status_string = '<span class="cqpim_button cqpim_small_button border-green font-green op nolink">' . __('In Progress', 'cqpim') . ' - ' . $task_pc . '%</span>';
								}
							}
						} else {
							$task_status_string = '<span class="cqpim_button cqpim_small_button border-green font-green op nolink">' . __('Complete', 'cqpim') . ' - ' . $task_pc . '%</span>';
						}	
						?>						
						<tr>
							<td><span class="cqpim_button cqpim_small_button bg-grey-cascade font-white op nolink rounded_2"><?php _e('Subtask', 'cqpim'); ?></span></td>
							<td colspan="2"><span class="nodesktop"><strong><?php _e('Title', 'cqpim'); ?></strong>: </span><a class="cqpim-link" href="<?php echo get_the_permalink($subtask->ID); ?>"><?php echo $subtask->post_title; ?></a><br /><?php echo $task_owner; ?></td>
							<td><span class="nodesktop"><strong><?php _e('Deadline', 'cqpim'); ?></strong>: </span><?php echo $task_deadline; ?></td>
							<td style="text-transform:capitalize"><?php echo $task_status_string; ?></td>
						</tr>							
						<?php
					}					
				} ?>
			<?php $milestone_counter++;
			} 
			echo '</table>';
			}
			?>
		</div>
	</div>
</div>