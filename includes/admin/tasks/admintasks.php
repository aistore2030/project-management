<?php
add_action( 'admin_menu' , 'register_pto_task_admin_page', 9 ); 
function register_pto_task_admin_page() {
	$mypage = add_submenu_page(	
				'pto-dashboard',
				__('All Tasks (Admin)', 'cqpim'),			
				__('All Tasks (Admin)', 'cqpim'), 			
				'cqpim_dash_view_all_tasks', 			
				'pto-alltasks', 		
				'pto_all_tasks'
	);
	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
}
function pto_all_tasks() { ?>
	<br />
	<div class="cqpim-dash-item-full tasks-box" style="padding-right:10px">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<i class="fa fa-pencil-square-o font-green-sharp" aria-hidden="true"></i>
					<span class="caption-subject font-green-sharp sbold"><?php _e('All Tasks (Admin)', 'cqpim'); ?> </span>
				</div>
				<div class="actions">
					<a class="cqpim_button cqpim_small_button border-green-sharp font-green-sharp rounded_2 sbold" href="<?php echo admin_url(); ?>post-new.php?post_type=cqpim_tasks"><?php _e('Add Task', 'cqpim') ?></a>
				</div>
			</div>
			<?php 
			$filter = isset($_SESSION['task_status']) ? $_SESSION['task_status'] : array('pending', 'progress');
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
			if(empty($assigned)) {
				$assigned = '';
			}
			$args = array(
				'post_type' => 'cqpim_tasks',
				'posts_per_page' => -1,
			);				
			$tasks = get_posts($args);
			$ordered = array();
			foreach($tasks as $task) {
				$task_details = get_post_meta($task->ID, 'task_details', true);
				$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
				if(!empty($task_deadline)) {
					$ordered[$task_deadline] = $task;
				}
			}
			ksort($ordered);
			foreach($tasks as $task) {
				$task_details = get_post_meta($task->ID, 'task_details', true);
				$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
				if(empty($task_deadline)) {
					$ordered[] = $task;
				}
			}
			if($tasks) { ?>
				<div style="text-align:right">
					<br />
					<?php _e('Filter by Status:', 'cqpim');
					$sess_status = isset($_SESSION['task_status']) ? $_SESSION['task_status'] : array('pending', 'progress');
					?>
					<select id="task_status_filter">
						<option value="" <?php if(in_array('pending', $sess_status) && in_array('progress', $sess_status)) { echo 'selected="selected"'; } ?>><?php _e('Default (Pending & In Progress)', 'cqpim'); ?></option>
						<option value="pending" <?php if(in_array('pending', $sess_status) && !in_array('progress', $sess_status)) { echo 'selected="selected"'; } ?>><?php _e('Pending', 'cqpim'); ?></option>
						<option value="on_hold" <?php if(in_array('on_hold', $sess_status)) { echo 'selected="selected"'; } ?>><?php _e('On Hold', 'cqpim'); ?></option>
						<option value="progress" <?php if(in_array('progress', $sess_status) && !in_array('pending', $sess_status)) { echo 'selected="selected"'; } ?>><?php _e('In Progress', 'cqpim'); ?></option>
						<option value="complete" <?php if(in_array('complete', $sess_status)) { echo 'selected="selected"'; } ?>><?php _e('Complete', 'cqpim'); ?></option>					
						<option value="all" <?php if(in_array('pending', $sess_status) && in_array('progress', $sess_status) && in_array('on_hold', $sess_status) && in_array('complete', $sess_status)) { echo 'selected="selected"'; } ?>><?php _e('Show All', 'cqpim'); ?></option>
					</select>
					<div id="tasks_filter_spinner" style="display:none" class="ajax_spinner"></div>
				</div>
				<table class="datatable_style dataTable">
					<thead>
						<th><?php _e('Task Title', 'cqpim'); ?></th>
						<th><?php _e('Project / Ticket', 'cqpim'); ?></th>
						<th><?php _e('Assigned To', 'cqpim'); ?></th>
						<th><?php _e('Deadline', 'cqpim'); ?></th>
						<th><?php _e('Time Spent', 'cqpim'); ?></th>
						<th><?php _e('Add Time', 'cqpim'); ?></th>
						<th><?php _e('Status', 'cqpim'); ?></th>
					</thead>
					<tbody>
						<?php foreach($ordered as $task) { 
							$task_details = get_post_meta($task->ID, 'task_details', true); 
							$task_owner = get_post_meta($task->ID, 'owner', true);
							$task_owner_id = get_post_meta($task->ID, 'owner', true);
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
								$watching = '<img title="' . __('Watched Task', 'cqpim') . '" src="' . PTO_PLUGIN_URL . '/img/watching.png" />';
							} else {
								$watching = '';
							}
							$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
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
							if(!empty($task->post_parent)) {
								$parent_object = get_post($task->post_parent);
							}
							if(!empty($active) && in_array($task_status, $sess_status)) {
							?>
							<script>
								jQuery(document).ready(function() {
									jQuery( "#progressbar-<?php echo $task->ID; ?>" ).progressbar({
										value: <?php echo number_format((float)$task_pc, 2, '.', ''); ?>
									});
								});
							</script>
							<tr<?php if(pto_is_task_overdue($task->ID) == 1) { ?> class="overdue"<?php } ?>>
								<td><span class="table-task font-blue-madison border-blue-madison cqpim_button cqpim_xs_button nolink op"> <?php if(empty($task->post_parent)) { _e('Task', 'cqpim'); } else { _e('Subtask', 'cqpim'); } ?></span> <span class="cqpim_mobile"><?php _e('Title:', 'cqpim'); ?></span> <a href="<?php echo get_edit_post_link($task->ID); ?>"><?php echo $task->post_title; ?></a> <?php if(!empty($task->post_parent)) { ?> <br /> <?php _e('Parent Task:', 'cqpim'); ?> <a href="<?php echo get_edit_post_link($parent_object->ID); ?>"><?php echo get_the_title($parent_object->ID); ?></a><?php } ?></td>
								<?php if(empty($project_ref)) { ?>
									<td><span class="cqpim_mobile"><?php _e('Project / Ticket:', 'cqpim'); ?></span> <?php _e('Ad-Hoc Task', 'cqpim'); ?></td>
								<?php } else { 
									$type = isset($project_object->post_type) ? $project_object->post_type : ''; ?>
									<td><span class="cqpim_mobile"><?php _e('Project / Ticket:', 'cqpim'); ?></span> <?php if($type == 'cqpim_project') { _e('Project: ', 'cqpim'); } else { _e('Ticket: ', 'cqpim'); } ?><a href="<?php echo $project_url; ?>"><?php echo $project_ref; ?></td>
								<?php } ?>
								<td>
									<span class="cqpim_mobile"><?php _e('Assignee:', 'cqpim'); ?></span> 
									<select class="admin_task_assignee" data-task="<?php echo $task->ID; ?>">
										<option value=""><?php _e('Choose...', 'cqpim'); ?></option>
										<?php 
										$assignees = pto_get_available_assignees($task->ID); 
										if(!empty($assignees)) {
											foreach($assignees as $available) { 
												$av_team_details = get_post_meta($available, 'team_details', true); 
												$av_team_name = isset($av_team_details['team_name']) ? $av_team_details['team_name'] : ''; ?>
												<option value="<?php echo $available; ?>" <?php selected($task_owner_id, $available); ?>><?php echo $av_team_name; ?></option>
											<?php }
										} else { ?>	
											<option value="<?php echo $task_owner; ?>" <?php selected($task_owner_id, $task_owner_id); ?>><?php echo $team_name; ?></option>
										<?php } ?>
									</select>
									<?php echo isset($watching) ? $watching : ''; ?>
								</td>
								<td data-order="<?php echo $task_deadline; ?>"><span class="cqpim_mobile"><?php _e('Deadline:', 'cqpim'); ?></span> <?php if(is_numeric($task_deadline)) { echo date(get_option('cqpim_date_format'), $task_deadline); } else { echo $task_deadline; } ?></td>
								<td><span class="cqpim_mobile"><?php _e('Time Spent:', 'cqpim'); ?></span> <?php echo $time; ?></td>
								<td><button class="add_timer legacy_button border-green font-green cqpim_button cqpim_xs_button op" value="<?php echo $task->ID; ?>"><i class="fa fa-clock-o" aria-hidden="true" title="<?php _e('Add Time', 'cqpim'); ?>"></i></button></td>
								<td style="width:200px">
									<div class="progress <?php echo $progress_class; ?>">
										<div id="progressbar-<?php echo $task->ID; ?>" title="<?php echo $milestone_status_string; ?>%"></div>
									</div>	
								</td>
							</tr>
						<?php 	}
						} ?>
					</tbody>
				</table>
			<?php } else { ?> 
				<div style="padding:5px">	
						<br />
						<h2 style="margin:0"><?php _e('Nothing Here!', 'cqpim'); ?></h2>
						<span><?php _e('No tasks to show...', 'cqpim'); ?></span>						
				</div>				
			<?php } ?>
			<div class="clear"></div>
		</div>
		<div style="display:none">
			<div id="add-time-div" class="add-time-div">
				<div style="padding:12px;">
					<h3><?php _e('Add Time', 'cqpim'); ?></h3>
					<input style="width:250px" id="task_time_value" type="text" name="timer" class="form-control timer" placeholder="<?php _e('0 sec', 'cqpim'); ?>" />
					<div class="clear"></div>
					<div style="padding-top:6px">
						<button class="cqpim_button cqpim_small_button border-green font-green start-timer-btn"><i class="fa fa-play" aria-hidden="true" title="<?php _e('Start Timer', 'cqpim'); ?>"></i></button>
						<button class="cqpim_button cqpim_small_button border-green font-green resume-timer-btn hidden"><i class="fa fa-play" aria-hidden="true" title="<?php _e('Resume Timer', 'cqpim'); ?>"></i></button>
						<button class="cqpim_button cqpim_small_button border-amber font-amber pause-timer-btn hidden"><i class="fa fa-pause" aria-hidden="true" title="<?php _e('Pause Timer', 'cqpim'); ?>"></i></button>
						<button class="cqpim_button cqpim_small_button border-red font-red remove-timer-btn hidden"><i class="fa fa-trash" aria-hidden="true" title="<?php _e('Remove Timer', 'cqpim'); ?>"></i></button>					
					</div>
					<div class="clear"></div>
					<input type="hidden" id="task_time_task" value="" />
					<div class="clear"></div>
					<div id="time_messages" class="alert-display"></div>
					<button id="add_time_ajax" class="cqpim_button border-green font-green right op"><?php _e('Add Time', 'cqpim'); ?> <span id="add_time_loader" class="ajax_loader" style="display:none"></span></button>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>
<?php }