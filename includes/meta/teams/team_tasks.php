<?php
function pto_team_tasks_metabox_callback( $post ) {
	$team_details = get_post_meta($post->ID, 'team_details', true);
	$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
	if(!empty($team_details['user_id'])) {
		$user = get_user_by('id', $team_details['user_id']);
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
	}
	if(empty($assigned)) {
		$assigned = '0';
	}
	$sess_status = isset($_SESSION['task_status']) ? $_SESSION['task_status'] : array('pending', 'progress');
	$args = array(
		'post_type' => 'cqpim_tasks',
		'posts_per_page' => -1,
		'meta_query'        => array(
			'relation'  => 'OR',
			array(
				'key'       => 'owner',
				'value'     => $assigned,
				'compare'   => '='
			),
			array(
				'key'       => 'task_watchers',
				'value'     => $assigned,
				'compare'   => 'LIKE'
			)
		)
	);				
	$tasks = get_posts($args);
	$own_tasks = array();
	foreach($tasks as $task) {
		$active = get_post_meta($task->ID, 'active', true);
		$task_details = get_post_meta($task->ID, 'task_details', true);
		$owner = get_post_meta($task->ID, 'owner', true);
		$watchers = get_post_meta($task->ID, 'task_watchers', true);
		if(empty($watchers)) {
			$watchers = array();
		}
		$task_status = isset($task_details['status']) ? $task_details['status'] : '';					
		if(!empty($active) && $task_status != 'complete' && $owner == $assigned || !empty($active) && $task_status != 'complete' && in_array($assigned, $watchers)) {
			$own_tasks[] = $task;
		}
	}
	if(!empty($own_tasks)) { 
		$ordered = array();
		foreach($own_tasks as $task) {
			$task_details = get_post_meta($task->ID, 'task_details', true);
			$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
			if(!empty($task_deadline)) {
				$ordered[$task_deadline] = $task;
			}
		}
		ksort($ordered);
		foreach($own_tasks as $task) {
			$task_details = get_post_meta($task->ID, 'task_details', true);
			$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
			if(empty($task_deadline)) {
				$ordered[] = $task;
			}
		}
		?>
		<table class="datatable_style dataTable" data-ordering="[[ 3, 'asc' ]]" data-rows="10">
			<thead>
				<th><?php _e('Task Title', 'cqpim'); ?></th>
				<th><?php _e('Assigned To', 'cqpim'); ?></th>
				<th><?php _e('Project / Ticket', 'cqpim'); ?></th>
				<th><?php _e('Deadline', 'cqpim'); ?></th>
				<th><?php _e('Progress', 'cqpim'); ?></th>
			</thead>
			<tbody>
				<?php $styles = array(); foreach($ordered as $task) { 
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
					$watchers = get_post_meta($task->ID, 'task_watchers', true); 
					if(empty($watchers)) {
						$watchers = array();
					}								
					if(in_array($assigned, $watchers)) {
						$watching = '<img title="' . __('Watched Task', 'cqpim') . '" src="' . plugin_dir_url( __FILE__ ) . '../../img/watching.png" />';
					} else {
						$watching = '';
					}
					$project = get_post_meta($task->ID, 'project_id', true); 
					$active = get_post_meta($task->ID, 'active', true);
					$project_details = get_post_meta($project, 'project_details', true);
					$project_object = get_post($project);
					$project_ref = isset($project_object->post_title) ? $project_object->post_title : '';
					$project_url = get_edit_post_link($project);
					$task_status = isset($task_details['status']) ? $task_details['status'] : '';
					$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
					$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '0';
					if(!is_numeric($task_deadline)) {
						$str_deadline = str_replace('/','-', $task_deadline);
						$deadline_stamp = strtotime($str_deadline);
					} else {
						$deadline_stamp = $task_deadline;
					}
					$time_spent = get_post_meta($task->ID, 'task_time_spent', true);
					$total = (int) 0;
					if($time_spent) {
						foreach($time_spent as $key => $time) {
							$total = $total + $time['time'];
						}
						$total = str_replace(',','.', $total);
						$time_split = explode('.', $total);
						$minutes = '0.' . $time_split[1];
						$minutes = $minutes * 60;
						$minutes = number_format((float)$minutes, 0, '.', '');
						if($time_split[0] > 1) {
							$hours  = __('hours', 'cqpim');
						} else {
							$hours = __('hour', 'cqpim');
						}
						$time =  '<span><strong>' . number_format((float)$total, 2, '.', '') . ' ' . __('hours', 'cqpim') . '</strong> (' . $time_split[0] . ' ' . $hours . ' + ' . $minutes . ' ' . __('minutes', 'cqpim') . ')</span> <div id="ajax_spinner_remove_time_'. $task->ID .'" class="ajax_spinner" style="display:none"></div>';
					} else {
						$time =  '<span>0</span>';
					}
					$now = time();
					if($task_status != 'complete') {
						if($deadline_stamp && $now > $deadline_stamp) {
							$progress_class = 'red';
							$milestone_status_string = __('OVERDUE', 'cqpim') . ' - ' . $task_pc;
						} else {
							$milestone_status_string = isset($task_details['status']) ? $task_details['status'] : '';
							if(!$milestone_status_string || $milestone_status_string == 'pending') {
								$progress_class = 'amber';
								$milestone_status_string = __('Pending', 'cqpim') . ' - ' . $task_pc;
							} else if($milestone_status_string == 'on_hold') {
								$progress_class = 'green';
								$milestone_status_string = __('On Hold', 'cqpim') . ' - ' . $task_pc;
							} else if($milestone_status_string == 'progress') {
								$progress_class = 'green';
								$milestone_status_string = __('In Progress', 'cqpim') . ' - ' . $task_pc;
							}
						}
					} else {
						$milestone_status_string = __('Complete', 'cqpim') . ' - ' . $task_pc;
					}	
					if(empty($progress_class)) {
						$progress_class = 'green';
					}
					?>
					<script>
						jQuery(document).ready(function() {
							jQuery( "#progressbar-<?php echo $task->ID; ?>" ).progressbar({
								value: <?php echo number_format((float)$task_pc, 2, '.', ''); ?>
							});
						});
					</script>
					<tr<?php if(pto_is_task_overdue($task->ID) == 1) { ?> class="overdue"<?php } ?>>
						<td><span class="cqpim_mobile"><?php _e('Title:', 'cqpim'); ?></span> <a href="<?php echo get_edit_post_link($task->ID); ?>"><?php echo $task->post_title; ?></a></td>
						<td><span class="cqpim_mobile"><?php _e('Assignee:', 'cqpim'); ?></span> <?php echo $task_owner; ?> <?php echo $watching; ?></td>
						<?php if(empty($project_ref) || $project_object->post_type == 'cqpim_teams') { ?>
							<td><?php _e('Ad-Hoc Task', 'cqpim'); ?></td>
						<?php } else { 
							$type = isset($project_object->post_type) ? $project_object->post_type : ''; ?>
							<td><span class="cqpim_mobile"><?php _e('Project / Ticket:', 'cqpim'); ?></span> <?php if($type == 'cqpim_project') { _e('Project: ', 'cqpim'); } else { _e('Ticket: ', 'cqpim'); } ?><a href="<?php echo $project_url; ?>"><?php echo $project_ref; ?></td>
						<?php } ?>
						<td data-sort="<?php echo $task_deadline; ?>"><span class="cqpim_mobile"><?php _e('Deadline:', 'cqpim'); ?></span> <?php if(is_numeric($task_deadline)) { echo date(get_option('cqpim_date_format'), $task_deadline); } else { echo $task_deadline; } ?></td>
						<td>
							<div class="progress <?php echo $progress_class; ?>">
								<div id="progressbar-<?php echo $task->ID; ?>" class="cqpim_tooltip" title="<?php echo $milestone_status_string; ?>%"></div>
							</div>									
						</td>
					</tr>
				<?php 
				} ?>
			</tbody>
		</table>
	<?php } else { ?> 
		<div class="cqpim-alert cqpim-alert-info alert-display">
				<span><?php _e('No tasks to show...', 'cqpim'); ?></span>						
		</div>				
	<?php } 
} ?>