<?php
add_action( "wp_ajax_pto_remove_time_entry", "pto_remove_time_entry");
function pto_remove_time_entry() {
	$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
	$key = isset($_POST['key']) ? $_POST['key'] : '';		
	if(!$task_id) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> __('There is some missing data. Delete unsuccessful.', 'cqpim'),
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();			
	} else {
		$task_time = get_post_meta($task_id, 'task_time_spent', true);
		unset($task_time[$key]);
		$task_time = array_filter($task_time);
		update_post_meta($task_id, 'task_time_spent', $task_time);
		$return =  array( 
			'error' 	=> false,
			'message' 	=> '',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();	
	}
	exit();
}
add_action( "wp_ajax_nopriv_pto_create_task", "pto_create_task");
add_action( "wp_ajax_pto_create_task", "pto_create_task");
function pto_create_task() {
	if(isset($_POST['task_title'])) {
		$task_weight = isset($_POST['task_weight']) ? $_POST['task_weight'] : '';
		$task_title = isset($_POST['task_title']) ? $_POST['task_title'] : '';
		$task_milestone_id = isset($_POST['task_milestone_id']) ? $_POST['task_milestone_id'] : '';
		$task_project_id = isset($_POST['task_project_id']) ? $_POST['task_project_id'] : '';
		$task_deadline = isset($_POST['task_finish']) ? $_POST['task_finish'] : '';
		$task_time = isset($_POST['task_time']) ? $_POST['task_time'] : '';
		if(!empty($task_deadline)) {
			$task_deadline = DateTime::createFromFormat(get_option('cqpim_date_format'), $task_deadline)->getTimestamp();
		}
		$start = isset($_POST['start']) ? $_POST['start'] : '';
		if(!empty($start)) {
			$start = DateTime::createFromFormat(get_option('cqpim_date_format'), $start)->getTimestamp();
		}
		$description = isset($_POST['description']) ? $_POST['description'] : '';
		$owner = isset($_POST['owner']) ? $_POST['owner'] : '';
		$type = isset($_POST['type']) ? $_POST['type'] : '';
		$ppid = isset($_POST['ppid']) ? $_POST['ppid'] : '';
		$new_task = array(
			'post_type' => 'cqpim_tasks',
			'post_status' => 'publish',
			'post_content' => '',
			'post_title' => $task_title,
			'post_password' => pto_random_string(10),
		);
		$task_pid = wp_insert_post( $new_task, true );
		if( ! is_wp_error( $task_pid ) ){
			$task_updated = array(
				'ID' => $task_pid,
				'post_name' => $task_pid,
			);						
			wp_update_post( $task_updated );
			update_post_meta($task_pid, 'project_id', $task_project_id);
			if(!empty($task_project_id)) {
				update_post_meta($task_pid, 'active', true);
				update_post_meta($task_pid, 'published', true);
			}
			update_post_meta($task_pid, 'milestone_id', $task_milestone_id);
			if(!empty($owner)) {
				update_post_meta($task_pid, 'owner', $owner);
			} else {
				$assigned = '';
				$owner = wp_get_current_user();
				$args = array(
					'post_type' => 'cqpim_teams',
					'posts_per_page' => -1,
					'post_status' => 'private'
				);
				$members = get_posts($args);
				foreach($members as $member) {
					$team_details = get_post_meta($member->ID, 'team_details', true);
					if($team_details['user_id'] == $owner->ID) {
						$assigned = $member->ID;
					}
				}
				update_post_meta($task_pid, 'owner', $assigned);
			}
			// Add details
			$task_details = array(
				'weight' => $task_weight,
				'deadline' => $task_deadline,
				'status' => 'pending',
				'task_start' => $start,
				'task_description' => $description,
				'task_pc' => 0,
				'task_priority' => 'normal',
				'task_est_time' => $task_time,
			);
			update_post_meta($task_pid, 'task_details', $task_details);
			if($type == 'project') {
				$current_user = wp_get_current_user();
				$current_user = $current_user->display_name;
				$project_progress = get_post_meta($ppid, 'project_progress', true);
				$project_progress = $project_progress&&is_array($project_progress)?$project_progress:array();
				$project_progress[] = array(
					'update' => __('Task Created', 'cqpim') . ': ' . $task_title,
					'date' => current_time('timestamp'),
					'by' => $current_user
				);
				update_post_meta($ppid, 'project_progress', $project_progress );
			}
			if(!empty($task_project_id)) {
				pto_send_task_updates($task_pid, $task_project_id, $owner, array(), '');
			}
			$return =  array( 
				'error' 	=> false,
				'errors' 	=> __('The Task was successfully created.', 'cqpim')
			);
			header('Content-type: application/json');
			echo json_encode($return);
		} else {
			$return =  array( 
				'error' 	=> true,
				'errors' 	=> __('The Task could not be created at this time, please try again.', 'cqpim')
			);
			header('Content-type: application/json');
			echo json_encode($return);				
		}
	}
	exit();
}
add_action( "wp_ajax_nopriv_pto_create_subtask", "pto_create_subtask");
add_action( "wp_ajax_pto_create_subtask", "pto_create_subtask");
function pto_create_subtask() {
	if(isset($_POST['task_title'])) {
		$task_weight = isset($_POST['task_weight']) ? $_POST['task_weight'] : '';
		$task_title = isset($_POST['task_title']) ? $_POST['task_title'] : '';
		$task_milestone_id = isset($_POST['task_milestone_id']) ? $_POST['task_milestone_id'] : '';
		$task_parent_id = isset($_POST['parent']) ? $_POST['parent'] : '';
		$task_project_id = isset($_POST['task_project_id']) ? $_POST['task_project_id'] : '';
		$task_deadline = isset($_POST['task_finish']) ? $_POST['task_finish'] : '';
		$task_time = isset($_POST['task_time']) ? $_POST['task_time'] : '';
		if(!empty($task_deadline)) {
			$task_deadline = DateTime::createFromFormat(get_option('cqpim_date_format'), $task_deadline)->getTimestamp();
		}
		$start = isset($_POST['start']) ? $_POST['start'] : '';
		if(!empty($start)) {
			$start = DateTime::createFromFormat(get_option('cqpim_date_format'), $start)->getTimestamp();
		}
		$description = isset($_POST['description']) ? $_POST['description'] : '';
		$owner = isset($_POST['owner']) ? $_POST['owner'] : '';
		$type = isset($_POST['type']) ? $_POST['type'] : '';
		$ppid = isset($_POST['ppid']) ? $_POST['ppid'] : '';
		$new_task = array(
			'post_type' => 'cqpim_tasks',
			'post_status' => 'publish',
			'post_content' => '',
			'post_title' => $task_title,
			'post_parent' => $task_parent_id,
			'post_password' => pto_random_string(10),
		);
		$task_pid = wp_insert_post( $new_task, true );
		if( ! is_wp_error( $task_pid ) ){
			$task_updated = array(
				'ID' => $task_pid,
				'post_name' => $task_pid,
			);						
			wp_update_post( $task_updated );
			update_post_meta($task_pid, 'project_id', $task_project_id);
			if(!empty($task_project_id)) {
				update_post_meta($task_pid, 'active', true);
				update_post_meta($task_pid, 'published', true);
			}
			update_post_meta($task_pid, 'milestone_id', $task_milestone_id);
			if(!empty($owner)) {
				update_post_meta($task_pid, 'owner', $owner);
			} else {
				$assigned = '';
				$owner = wp_get_current_user();
				$args = array(
					'post_type' => 'cqpim_teams',
					'posts_per_page' => -1,
					'post_status' => 'private'
				);
				$members = get_posts($args);
				foreach($members as $member) {
					$team_details = get_post_meta($member->ID, 'team_details', true);
					if($team_details['user_id'] == $owner->ID) {
						$assigned = $member->ID;
					}
				}
				update_post_meta($task_pid, 'owner', $assigned);
			}
			$task_details = array(
				'weight' => $task_weight,
				'deadline' => $task_deadline,
				'status' => 'pending',
				'task_start' => $start,
				'task_description' => $description,
				'task_pc' => 0,
				'task_priority' => 'normal',
				'task_est_time' => $task_time,
			);
			update_post_meta($task_pid, 'task_details', $task_details);
			if($type == 'project') {
				$current_user = wp_get_current_user();
				$current_user = $current_user->display_name;
				$project_progress = get_post_meta($ppid, 'project_progress', true);
				$project_progress[] = array(
					'update' => __('Task Created', 'cqpim') . ': ' . $task_title,
					'date' => current_time('timestamp'),
					'by' => $current_user
				);
				update_post_meta($ppid, 'project_progress', $project_progress );
			}
			if(!empty($task_project_id)) {
				pto_send_task_updates($task_pid, $task_project_id, $owner, array(), '');
			}
			$return =  array( 
				'error' 	=> false,
				'errors' 	=> __('The Task was successfully created.', 'cqpim')
			);
			header('Content-type: application/json');
			echo json_encode($return);
		} else {
			$return =  array( 
				'error' 	=> true,
				'errors' 	=> __('The Task could not be created at this time, please try again.', 'cqpim')
			);
			header('Content-type: application/json');
			echo json_encode($return);				
		}
	}
	exit();
}
add_action( "wp_ajax_nopriv_pto_update_task", "pto_update_task");
add_action( "wp_ajax_pto_update_task", "pto_update_task");
function pto_update_task() {
	$current_user = wp_get_current_user();
	$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	$ppid = isset($_POST['ppid']) ? $_POST['ppid'] : '';
	$status = isset($_POST['status']) ? $_POST['status'] : '';
	$time = isset($_POST['time']) ? $_POST['time'] : '';
	$owner = isset($_POST['owner']) ? $_POST['owner'] : '';
	$changes = '';
	if(!$task_id) {
		$return =  array( 
			'error' 	=> true,
			'errors' 	=> 'Task ID cannot be found.'
		);
		header('Content-type: application/json');
		echo json_encode($return);			
	} else {
		$old_owner = get_post_meta($task_id, 'owner', true);
		$old_team_details = get_post_meta($old_owner, 'team_details', true);
		$new_team_details = get_post_meta($owner, 'team_details', true);			
		update_post_meta($task_id, 'owner', $owner);
		if($owner != $old_owner) {
			if(empty($old_owner)) {
				$changes .= sprintf(__('Assignee changed to %1$s', 'cqpim'), $new_team_details['team_name']) . "\r\n";
			} elseif(empty($owner)){
				$changes .= sprintf(__('Assignee changed %1$s to unassigned', 'cqpim'), $old_team_details['team_name']) . "\r\n";
			} else {
				$changes .= sprintf(__('Assignee changed from %1$s to %2$s', 'cqpim'), $old_team_details['team_name'], $new_team_details['team_name']) . "\r\n";
			}
		}
		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$deadline = isset($_POST['deadline']) ? $_POST['deadline'] : '';
		if(!empty($deadline)) {
			$deadline = DateTime::createFromFormat(get_option('cqpim_date_format'), $deadline)->getTimestamp();
		}
		$start = isset($_POST['start']) ? $_POST['start'] : '';
		if(!empty($start)) {
			$start = DateTime::createFromFormat(get_option('cqpim_date_format'), $start)->getTimestamp();
		}
		$description = isset($_POST['description']) ? $_POST['description'] : '';
		$watchers = isset($_POST['watchers']) ? $_POST['watchers'] : '';
		$updated_task = array(
			'ID'           => $task_id,
			'post_title'   => $title,
		);
		$updated = wp_update_post( $updated_task );
		if($updated != false) {
			$task_details = get_post_meta($task_id, 'task_details', true);
			$task_details['deadline'] = $deadline;
			$task_details['status'] = $status;
			$task_details['task_start'] = $start;
			$task_details['task_description'] = $description;
			update_post_meta($task_id, 'task_details', $task_details);
			if($time) {
				$time_spent = get_post_meta($task_id, 'task_time_spent', true);
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
				if(!$time_spent) {
					$time_spent = array();
				}
				$time_spent[] = array(
					'team' => $user->display_name,
					'team_id' => $assigned,
					'time' => $time,
				);
				update_post_meta($task_id, 'task_time_spent', $time_spent);
			}
			if($type == 'project') {
				if($status == 'complete') {
					$current_user = wp_get_current_user();
					$current_user = $current_user->display_name;
					$project_progress = get_post_meta($ppid, 'project_progress', true);
					$project_progress[] = array(
						'update' => __('Task Completed', 'cqpim') . ': ' . $title,
						'date' => current_time('timestamp'),
						'by' => $current_user
					);
					update_post_meta($ppid, 'project_progress', $project_progress );									
				} else {
					$current_user = wp_get_current_user();
					$current_user = $current_user->display_name;
					$project_progress = get_post_meta($ppid, 'project_progress', true);
					$project_progress[] = array(
						'update' => __('Task Updated', 'cqpim') . ': ' . $title,
						'date' => current_time('timestamp'),
						'by' => $current_user
					);
					update_post_meta($ppid, 'project_progress', $project_progress );
				}
			}
			$return =  array( 
				'error' 	=> false,
				'messages' 	=> __('The task was successfully updated', 'cqpim')
			);
			header('Content-type: application/json');
			echo json_encode($return);
		} else {
			$return =  array( 
				'error' 	=> true,
				'errors' 	=> __('Task could not be updated at this time, please try again.', 'cqpim')
			);
			header('Content-type: application/json');
			echo json_encode($return);
		}
	}
	exit();
}
add_action( "wp_ajax_nopriv_pto_delete_task", "pto_delete_task");
add_action( "wp_ajax_pto_delete_task", "pto_delete_task");
function pto_delete_task() {
	$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	$ppid = isset($_POST['ppid']) ? $_POST['ppid'] : '';
	if($type == 'project') {
		$current_user = wp_get_current_user();
		$current_user = $current_user->display_name;
		$project_progress = get_post_meta($ppid, 'project_progress', true);
		$task_object = get_post($task_id);
		$task_title = $task_object->post_title;
		$project_progress[] = array(
			'update' => __('Task Deleted', 'cqpim') . ': ' . $task_title,
			'date' => current_time('timestamp'),
			'by' => $current_user
		);
		update_post_meta($ppid, 'project_progress', $project_progress );
	}
	$args = array(
		'post_type' => 'cqpim_tasks',
		'posts_per_page' => -1,			
		'post_parent' => $task_id,
		'orderby' => 'date',
		'order' => 'ASC'
	);
	$subtasks = get_posts($args);
	foreach($subtasks as $subtask) {
		$sdeleted = wp_delete_post($subtask->ID);
	}
	$deleted = wp_delete_post($task_id);
	if($deleted == false) {
		$return =  array( 
			'error' 	=> true,
			'errors' 	=> __('The Task could not be deleted at this time, please try again.', 'cqpim')
		);
		header('Content-type: application/json');
		echo json_encode($return);		
	} else {
		$return =  array( 
			'error' 	=> false,
			'messages' 	=> __('The task was successfully deleted.', 'cqpim')
		);
		header('Content-type: application/json');
		echo json_encode($return);			
	}
	exit();
}
add_action( "wp_ajax_nopriv_pto_add_timer_time", "pto_add_timer_time");
add_action( "wp_ajax_pto_add_timer_time", "pto_add_timer_time");
function pto_add_timer_time() {
	$time = isset($_POST['time']) ? $_POST['time'] : '';
	$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
	if(empty($task_id)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> __('The Task ID is missing, make sure you have selected a from the list.', 'cqpim'),
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();			
	} else {
		if(empty($time)) {
			$return =  array( 
				'error' 	=> true,
				'message' 	=> __('There is no time to add.', 'cqpim'),
			);
			header('Content-type: application/json');
			echo json_encode($return);	
			exit();				
		} else {
			$time = explode(':', $time);
			$hours = $time[0];
			$hours = str_replace('00', '0', $hours);
			$minutes = str_replace('0', '', $time[1]);
			$min_dec = $minutes / 60;
			$time = $min_dec + $hours;
			$time_spent = get_post_meta($task_id, 'task_time_spent', true);
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
			$time_spent = $time_spent&&is_array($time_spent)?$time_spent:array();
			$time_spent[] = array(
				'team' => $user->display_name,
				'team_id' => $assigned,
				'time' => bcdiv($time, 1, 2),
				'stamp' => time()
			);
			update_post_meta($task_id, 'task_time_spent', $time_spent);
			$return =  array( 
				'error' 	=> false,
				'message' 	=> __('Time added successfully.', 'cqpim'),
			);
			header('Content-type: application/json');
			echo json_encode($return);	
			exit();	
		}
	}
}
add_action( "wp_ajax_nopriv_pto_populate_project_milestone", "pto_populate_project_milestone");
add_action( "wp_ajax_pto_populate_project_milestone", "pto_populate_project_milestone");
function pto_populate_project_milestone() {
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
	$project_id = isset($_POST['ID']) ? $_POST['ID']: '';
	$milestones = get_post_meta($project_id, 'project_elements', true);
	$milestones_to_display = '';
	if(empty($milestones)) {
		$milestones = array();
	}
	foreach($milestones as $milestone) {
		$milestones_to_display .= '<option value="' . $milestone['id'] . '">' . $milestone['title'] . '</option>';
	}
	$project_contributors = get_post_meta($project_id, 'project_contributors', true);
	$project_contributors_to_display = '';
	if(empty($project_contributors)) {
		$project_contributors = array();
	}
	foreach($project_contributors as $contributor) {
		$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
		$team_job = isset($team_details['team_job']) ? $team_details['team_job'] : '';
		$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
		$project_contributors_to_display .= '<option value="' . $contributor . '">' . $team_name . ' - ' . $team_job . '</option>';
	}
	if(!$milestones_to_display && !$project_contributors_to_display) {
		$return =  array( 
			'error' 	=> true,
			'options' 	=> '<option value="">' . __('No milestones available', 'cqpim') . '</option>',
			'team_options' 	=> '<option value="' . $assigned . '">' . __('Me', 'cqpim') . '</option>'
		);
		header('Content-type: application/json');
		echo json_encode($return);
	} else if(!$milestones_to_display && $project_contributors_to_display) {
		$return =  array( 
			'error' 	=> true,
			'options' 	=> '<option value="">' . __('No milestones available', 'cqpim') . '</option>',
			'team_options' 	=> '<option value="">' . __('Choose a team member', 'cqpim') . '</option>' . $project_contributors_to_display
		);
		header('Content-type: application/json');
		echo json_encode($return);
	} else if(!$project_contributors_to_display && $milestones_to_display) {
		$return =  array( 
			'error' 	=> true,
			'options' 	=> $milestones_to_display,
			'team_options' 	=> '<option value="">' . __('No team members available', 'cqpim') . '</option>'
		);
		header('Content-type: application/json');
		echo json_encode($return);
	} else if($project_contributors_to_display && $milestones_to_display) {
		$return =  array( 
			'error' 	=> true,
			'options' 	=> $milestones_to_display,
			'team_options' 	=> '<option value="' . $assigned . '">' . __('Choose a team member', 'cqpim') . '</option>' . $project_contributors_to_display
		);
		header('Content-type: application/json');
		echo json_encode($return);
	}
	exit();
}
add_action( "wp_ajax_nopriv_pto_delete_task_page", "pto_delete_task_page");
add_action( "wp_ajax_pto_delete_task_page", "pto_delete_task_page");
function pto_delete_task_page() {
	$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
	$ppid = get_post_meta($task_id, 'project_id', true);
	if(!empty($ppid)) {
		$current_user = wp_get_current_user();
		$project_progress = get_post_meta($ppid, 'project_progress', true);
		$project_progress = $project_progress&&is_array($project_progress)?$project_progress:array();
		$task_object = get_post($task_id);
		$task_title = $task_object->post_title;
		$project_progress[] = array(
			'update' => __('Task Deleted', 'cqpim') . ': ' . $task_title,
			'date' => current_time('timestamp'),
			'by' => $current_user->display_name
		);
		update_post_meta($ppid, 'project_progress', $project_progress );		
	}
	wp_delete_post($task_id, true);
	$return =  array( 
		'error' 	=> false,
		'redirect' 	=> admin_url() . 'admin.php?page=pto-tasks'
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();
}
add_action( "wp_ajax_nopriv_pto_client_update_task", "pto_client_update_task");
add_action( "wp_ajax_pto_client_update_task", "pto_client_update_task");
function pto_client_update_task() {
	$task_id = isset ( $_POST['file_task_id'] ) ? sanitize_text_field( $_POST['file_task_id'] ) : '';
	$message = isset($_POST['add_task_message']) ? sanitize_text_field($_POST['add_task_message']) : '';
	if(empty($message)) {
		$return =  array( 
			'error' 	=> true,
			'message' => __('You must enter a message.', 'cqpim'),
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();					
	} else {
		$custom_fields = get_option('cqpim_custom_fields_task');	
		$custom_fields = str_replace('\"', '"', $custom_fields);
		$custom_fields = json_decode($custom_fields);
		$custom = isset($_POST['custom']) ? $_POST['custom'] : array();
		foreach($custom_fields as $custom_field) {
			if(empty($custom[$custom_field->name]) && !empty($custom_field->required)) {
				$return =  array( 
					'error' 	=> true,
					'title' 	=> __('Required Fields Missing', 'cqpim'),
					'message' 	=> __('Please complete all required fields.', 'cqpim'),
				);
				header('Content-type: application/json');
				echo json_encode($return);	
				exit();						
			}
		}
		$custom = isset($_POST['custom']) ? $_POST['custom'] : array();
		update_post_meta($task_id, 'custom_fields', $custom);
		$message = make_clickable($message);
		$owner = isset($_POST['task_owner']) ? sanitize_text_field($_POST['task_owner']) : '';
		$task_owner = get_post_meta($task_id, 'owner', true);
		update_post_meta($task_id, 'owner', $owner);
		$project_id = get_post_meta($task_id, 'project_id', true);
		$task_owner = get_post_meta($task_id, 'owner', true);
		$task_watchers = get_post_meta($task_id, 'task_watchers', true);
		$task_link = get_the_permalink($task_id);
		$task_object = get_post($task_id);
		$task_link = '<a class="cqpim-link" href="' . $task_link . '">' . $task_object->post_title . '</a>';
		$attachments = isset($_POST['files']) ? $_POST['files'] : array();
		$ticket_changes = array();
		if(!empty($attachments)) {
			$attachments = explode(',', $attachments);
			$attachments_to_send = array();
			foreach($attachments as $attachment) {
				global $wpdb;
				$wpdb->query(
					"
					UPDATE $wpdb->posts 
					SET post_parent = $task_id
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
		$task_messages = get_post_meta($task_id, 'task_messages', true);
		$task_messages = $task_messages&&is_array($task_messages)?$task_messages:array();
		$date = current_time('timestamp');
		$current_user = wp_get_current_user();
		if(empty($message)) {
			$message = '';
		}
		$task_messages[] = array(
			'date' => $date,
			'message' => $message,
			'by' => $current_user->display_name,
			'author' => $current_user->ID,
			'changes' => $ticket_changes,
		);		
		update_post_meta($task_id, 'task_messages', $task_messages);
		$project_progress = get_post_meta($project_id, 'project_progress', true);
		$project_progress[] = array(
			'update' => sprintf(__('Message sent in task: %1$s', 'cqpim'), $task_object->post_title),
			'date' => current_time('timestamp'),
			'by' => $current_user->display_name
		);
		if(!empty($attachments)) {
			foreach($attachments as $attachment) {
				$post = get_post($attachment);
				$project_progress[] = array(
					'update' => sprintf(__('File "%1$s" uploaded to task: %2$s', 'cqpim'), $post->post_title, $task_object->post_title),
					'date' => current_time('timestamp'),
					'by' => $current_user->display_name
				);
			}
		}
		update_post_meta($project_id, 'project_progress', $project_progress );
	}
	update_post_meta($task_id, 'client_updated', true);
	update_post_meta($task_id, 'team_updated', false);
	pto_send_task_updates($task_id, $project_id, $task_owner, $task_watchers, $message, '', $attachments_to_send);
	$return =  array( 
		'error' 	=> false
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();		
}
function pto_send_task_updates($post_id, $project_id = NULL, $task_owner = NULL, $task_watchers = array(), $message = NULL, $user = null, $attachments = array()) {
	$emails_to_send = array();
	$project_details = get_post_meta($project_id, 'project_details', true);
	if(is_object($task_owner)) {
		$task_owner = isset($task_owner->ID) ? $task_owner->ID : '';
	} else {
		$task_owner = isset($task_owner) ? $task_owner : '';
	}
	$client_check = preg_replace('/[0-9]+/', '', $task_owner);
	$client = false;
	if($client_check == 'C') {
		$client = true;
	}
	if($task_owner) {
		if($client == true) {
			$id = preg_replace("/[^0-9,.]/", "", $task_owner);
			$client = get_user_by('id', $id);
			$client_email = $client->user_email;
		} else {
			$emails_to_send[] = $task_owner;
		}
	} else {
		$task_owner = '';
	}			
	if(empty($client_email)) {
		$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
		$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
		$client = get_user_by('id', $client_contact);
		$args = array(
			'post_type' => 'cqpim_client',
			'posts_per_page' => -1,
			'post_status' => 'private'
		);
		$members = get_posts($args);
		foreach($members as $member) {
			$team_details = get_post_meta($member->ID, 'client_details', true);
			if($team_details['user_id'] == $client->ID) {
				$notifications = get_post_meta($member->ID, 'client_notifications', true);
			}
		}
		if(empty($assigned)) {
			foreach($members as $member) {
				$client_contacts = get_post_meta($member->ID, 'client_contacts', true);
				if(empty($client_contacts)) {
					$client_contacts = array();
				}
				foreach($client_contacts as $contact) {
					if($contact['user_id'] == $client->ID) {
						$notifications = isset($contact['notifications']) ? $contact['notifications'] : array();
					}
				}
			} 			
		}
		$no_tasks = isset($notifications['no_tasks']) ? $notifications['no_tasks']: 0;
		$no_tasks_comment = isset($notifications['no_tasks_comment']) ? $notifications['no_tasks_comment']: 0;			
		$client_email = $client->user_email;
		if(!empty($no_tasks)) {
			$client_email = '';
		}
		if(!empty($no_tasks_comment) && empty($message)) {
			$client_email = '';
		}
	}
	if(empty($user)) {
		$user = wp_get_current_user();
	}
	if(!empty($client_email)) {
		$subject = get_option('team_assignment_subject');
		$content = get_option('team_assignment_email');
		$url = get_the_permalink($post_id);
		$subject = str_replace('%%PIPING_ID%%', '[' . get_option('cqpim_string_prefix') . ':' . $post_id . ']', $subject);
		$content = str_replace('%%TASK_UPDATE%%', $message, $content);
		$content = str_replace('%%CURRENT_USER%%', $user->display_name, $content);
		$content = str_replace('%%NAME%%', $client->display_name, $content);
		$content = str_replace('%%TASK_URL%%', $url, $content);
		$subject = pto_replacement_patterns($subject, $post_id, 'task');
		$content = pto_replacement_patterns($content, $post_id, 'task');
		if($user->user_email != $client_email) {
			pto_add_team_notification($client_id, $user->ID, $post_id, 'task');
			pto_send_emails($client_email, $subject, $content, '', $attachments, 'sales');
		}
	}
	$project_contributors = get_post_meta($project_id, 'project_contributors', true);
	if(empty($project_contributors)) {
		$project_contributors = array();
	}
	foreach($project_contributors as $contrib) {
		if($contrib['pm'] == 1) {
			$emails_to_send[] = $contrib['team_id'];
		}
	}	
	if(empty($task_watchers)) {
		$task_watchers = array();
	} else {
		$task_watchers = $task_watchers;
	}
	foreach($task_watchers as $watcher) {
		$emails_to_send[] = $watcher;
	}
	$emails_to_send = array_unique($emails_to_send);
	foreach($emails_to_send as $email) {
		$team_details = get_post_meta($email, 'team_details', true);
		$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
		if($user->user_email != $team_email) {
			pto_add_team_notification($email, $user->ID, $post_id, 'task');
		}
	}
	foreach($emails_to_send as $key => $email) {
		foreach($project_contributors as $contrib) {
			if($contrib['team_id'] == $email) {
				if($contrib['demail'] == 1) {
					unset($emails_to_send[$key]);
				}
			}
		}
	}
	foreach($emails_to_send as $email) {
		$team_details = get_post_meta($email, 'team_details', true);
		$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
		$subject = get_option('team_assignment_subject');
		$subject = str_replace('%%PIPING_ID%%', '[' . get_option('cqpim_string_prefix') . ':' . $post_id . ']', $subject);
		$content = get_option('team_assignment_email');
		$url = admin_url() . 'post.php?post=' . $post_id . '&action=edit';
		$content = str_replace('%%TASK_URL%%', $url, $content);
		$content = str_replace('%%NAME%%', $team_details['team_name'], $content);
		$content = str_replace('%%CURRENT_USER%%', $user->display_name, $content);
		$content = str_replace('%%TASK_UPDATE%%', $message, $content);
		$subject = pto_replacement_patterns($subject, $post_id, 'task');
		$content = pto_replacement_patterns($content, $post_id, 'task');
		if($user->user_email != $team_email) {
			pto_send_emails($team_email, $subject, $content, '', $attachments, 'sales');
		}
	}
}
add_action( "wp_ajax_nopriv_pto_delete_task_message", "pto_delete_task_message");
add_action( "wp_ajax_pto_delete_task_message", "pto_delete_task_message");	
function pto_delete_task_message() {
	$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
	$key = isset($_POST['key']) ? $_POST['key'] : '';
	$project_messages = get_post_meta($project_id, 'task_messages', true);
	$project_messages = array_reverse($project_messages);
	unset($project_messages[$key]);
	$project_messages = array_filter($project_messages);
	$project_messages = array_reverse($project_messages);
	update_post_meta($project_id, 'task_messages', $project_messages);
	exit();
}
add_action( "wp_ajax_pto_add_manual_task_time", "pto_add_manual_task_time");
function pto_add_manual_task_time() {
	$hours = isset($_POST['hours']) ? $_POST['hours'] : '';
	$minutes = isset($_POST['minutes']) ? $_POST['minutes'] : '';	
    $time = $hours + round($minutes / 60, 2);
	$post_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
	if(empty($time)) {
		$return =  array( 
			'error' 	=> true,
			'errors' => __('You must enter how many hours have been completed', 'cqpim')
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();		
	}
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
	$team_details = get_post_meta($assigned, 'team_details', true);
	$time_spent = get_post_meta($post_id, 'task_time_spent', true);
	if(empty($time_spent)) {
		$time_spent = array();
	}
	$time_spent[] = array(
		'team' => $team_details['team_name'],
		'team_id' => $assigned,
		'time' => $time,
		'stamp' => time(),
	);
	update_post_meta($post_id, 'task_time_spent', $time_spent);
	$return =  array( 
		'error' 	=> false
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();
}
add_action( "wp_ajax_nopriv_pto_filter_tasks", "pto_filter_tasks");
add_action( "wp_ajax_pto_filter_tasks", "pto_filter_tasks");	
function pto_filter_tasks() {
	$filter = isset($_POST['filter']) ? $_POST['filter'] : '';
	if(empty($filter)) {
		$_SESSION['task_status'] = array('pending', 'progress');
	} elseif($filter == 'all') {
		$_SESSION['task_status'] = array('pending', 'progress', 'on_hold', 'complete');
	}else {
		$filter_arr = array();
		$filter_arr[] = $filter;
		$_SESSION['task_status'] = $filter_arr;
	}
	$return =  array( 
		'error' 	=> false
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();
}
function pto_get_available_assignees($task_id) {
	$task_project = get_post_meta($task_id, 'project_id', true);
	$task_project = get_post($task_project);
	$parent_type = isset($task_project->post_type) ? $task_project->post_type : '';
	$user = wp_get_current_user();
	$args = array(
		'post_type' => 'cqpim_teams',
		'posts_per_page' => -1,
		'post_status' => 'private'
	);
	$team_members = get_posts($args);
	$available_members = array();
	if(empty($task_project)) {
		foreach($team_members as $team_member) {
			$available_members[] = $team_member->ID;
		}
	} else {
		if($parent_type == 'cqpim_project') {			
			$contribs = get_post_meta($task_project->ID, 'project_contributors', true);
			if(!empty($contribs)) {
				foreach($contribs as $contrib) {
					$available_members[] = $contrib['team_id'];
				}
			}	
		} else {
			foreach($team_members as $team_member) {
				$team_details = get_post_meta($team_member->ID, 'team_details', true);
				$user = get_user_by('id', $team_details['user_id']);
				$caps = $user->allcaps;
				if(!empty($caps['cqpim_view_tickets'])) {
					$available_members[] = $team_member->ID;
				}
			}
		}
	}
	return $available_members;
}
add_action( "wp_ajax_pto_edit_assignee_from_admin", "pto_edit_assignee_from_admin");
function pto_edit_assignee_from_admin() {
	$data = isset($_POST) ? $_POST : array();
	$task_id = isset($data['task_id']) ? $data['task_id'] : '';
	$assignee = isset($data['assignee']) ? $data['assignee'] : '';
	$project = get_post_meta($task_id, 'project_id', true); 
	$project = $project?$project:0;
	update_post_meta($task_id, 'owner', $assignee);
	pto_send_task_updates($task_id, $project, $assignee);
	$return =  array( 
		'error' 	=> false
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();
}
add_action( "wp_ajax_pto_assign_all_ms", "pto_assign_all_ms");
function pto_assign_all_ms() {
	$data = isset($_POST) ? $_POST : array();
	$ms = isset($data['ms']) ? $data['ms'] : '';
	$assignee = isset($data['assignee']) ? $data['assignee'] : '';
	$project = isset($data['project_id']) ? $data['project_id'] : '';
	$notify = isset($data['notify']) ? $data['notify'] : '';
	if(empty($ms) || empty($project) || empty($assignee)) {
		$return =  array( 
			'error' => true,
			'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . __('There is missing data, please try again', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();		
	}
	$args = array(
		'post_type' => 'cqpim_tasks',
		'posts_per_page' => -1,
		'meta_key' => 'milestone_id',
		'meta_value' => $ms,
		'orderby' => 'date',
		'order' => 'ASC'
	);
	$tasks = get_posts($args);
	foreach($tasks as $task) {
		update_post_meta($task->ID, 'owner', $assignee);
		if(!empty($notify)) {
			pto_send_task_updates($task->ID, $project, $assignee);
		}
		$args = array(
			'post_type' => 'cqpim_tasks',
			'posts_per_page' => -1,
			'meta_key' => 'milestone_id',
			'meta_value' => $ms,
			'post_parent' => $task->ID,
			'orderby' => 'date',
			'order' => 'ASC'
		);
		$subtasks = get_posts($args);
		foreach($subtasks as $subtask) {
			update_post_meta($subtask->ID, 'owner', $assignee);
			if(!empty($notify)) {
				pto_send_task_updates($subtask->ID, $project, $assignee);
			}
		}
	}
	$return =  array( 
		'error' => false,
		'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('The Assignees have been updated.', 'cqpim') . '</div>',
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();
}
add_action( "wp_ajax_pto_editable_start", "pto_editable_start");
function pto_editable_start() {
	$data = isset($_POST) ? $_POST : array();
	if(empty($data['task_id']) || empty($data['date'])) {
		$return =  array( 
			'error' => true,
			'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . __('There is missing data, please try again', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();	
	}
	$date = pto_convert_date($data['date']);
	$task_details = get_post_meta($data['task_id'], 'task_details', true);
	$task_details['task_start'] = $date;
	update_post_meta($data['task_id'], 'task_details', $task_details);
	$return =  array( 
		'error' => false,
		'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . __('Task Successfully Updated', 'cqpim') . '</div>',
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();
}
add_action( "wp_ajax_pto_editable_end", "pto_editable_end");
function pto_editable_end() {
	$data = isset($_POST) ? $_POST : array();
	if(empty($data['task_id']) || empty($data['date'])) {
		$return =  array( 
			'error' => true,
			'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . __('There is missing data, please try again', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();	
	}
	$date = pto_convert_date($data['date']);
	$task_details = get_post_meta($data['task_id'], 'task_details', true);
	$task_details['deadline'] = $date;
	update_post_meta($data['task_id'], 'task_details', $task_details);
	$return =  array( 
		'error' => false,
		'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . __('Task Successfully Updated', 'cqpim') . '</div>',
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();
}
add_action( "wp_ajax_pto_editable_assignee", "pto_editable_assignee");
function pto_editable_assignee() {
	$data = isset($_POST) ? $_POST : array();
	if(empty($data['task_id']) || empty($data['assignee'])) {
		$return =  array( 
			'error' => true,
			'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . __('There is missing data, please try again', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();	
	}
	update_post_meta($data['task_id'], 'owner', $data['assignee']);
	$current_user = wp_get_current_user();
	pto_add_team_notification($data['assignee'], $current_user->ID, $data['task_id'], 'task_assignee', $ctype = '');
	$return =  array( 
		'error' => false,
		'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . __('Task Successfully Updated', 'cqpim') . '</div>',
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();
}
function pto_is_task_overdue($task_id) {
	$task_details = get_post_meta($task_id, 'task_details', true);
	$task_start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
	$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
	$task_status = isset($task_details['status']) ? $task_details['status'] : '';
	if(!empty($task_deadline)) { 
		$now = current_time('timestamp');
		if($task_deadline < $now && $task_status != 'complete') {
			return 1;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}