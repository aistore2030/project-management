<?php
add_action( "wp_ajax_pto_add_step_to_template", "pto_add_step_to_template");
function pto_add_step_to_template() {
	$quote_id = isset($_POST['ID']) ? $_POST['ID'] : '';
	$title = isset($_POST['title']) ? $_POST['title'] : '';
	$start = isset($_POST['start']) ? $_POST['start'] : '';
	if(!empty($start)) {
		$start = DateTime::createFromFormat(get_option('cqpim_date_format'), $start)->getTimestamp();
	}
	$deadline = isset($_POST['deadline']) ? $_POST['deadline'] : '';
	if(!empty($deadline)) {
		$deadline = DateTime::createFromFormat(get_option('cqpim_date_format'), $deadline)->getTimestamp();
	}
	$milestone_id = isset($_POST['milestone_id']) ? $_POST['milestone_id'] : '';
	$cost = isset($_POST['cost']) ? $_POST['cost'] : '';
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	$order = isset($_POST['order']) ? $_POST['order'] : '';
	if($title) {
		$quote_elements = get_post_meta($quote_id, 'project_template', true);
		if(empty($quote_elements)) {
			$quote_elements = array();
		}
		$i = 0;
		if(!empty($quote_elements)) {
			foreach($quote_elements as $element) {
				$i++;
			}
		}
		$element_to_add = array(
			'title' => $title,
			'id' => $quote_id . '-' . $milestone_id,
			'deadline' => $deadline,
			'start' => $start,
			'cost' => $cost,
			'weight' => $order,
			'tasks' => array(),
		);
		$quote_elements['ms_key'] = $milestone_id + 1;
		$quote_elements['milestones'][$quote_id . '-' . $milestone_id] = $element_to_add;
		update_post_meta($quote_id, 'project_template', $quote_elements);
		$return =  array( 
			'error' 	=> false,
			'errors' 	=> __('Milestone Added.', 'cqpim')
		);
		header('Content-type: application/json');
		echo json_encode($return);
	} else {
		$return =  array( 
			'error' 	=> true,
			'errors' 	=> __('You must fill in the title as a minimum.', 'cqpim')
		);
		header('Content-type: application/json');
		echo json_encode($return);
	}
	exit();
}
add_action( "wp_ajax_pto_create_task_template", "pto_create_task_template");
function pto_create_task_template() {
	if(isset($_POST['task_title'])) {
		$task_title = isset($_POST['task_title']) ? $_POST['task_title'] : '';
		$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
		$task_milestone_id = isset($_POST['task_milestone_id']) ? $_POST['task_milestone_id'] : '';
		$task_deadline = isset($_POST['task_finish']) ? $_POST['task_finish'] : '';
		$assignee = isset($_POST['assignee']) ? $_POST['assignee'] : '';
		if(!empty($task_deadline)) {
			$task_deadline = DateTime::createFromFormat(get_option('cqpim_date_format'), $task_deadline)->getTimestamp();
		}
		$start = isset($_POST['start']) ? $_POST['start'] : '';
		if(!empty($start)) {
			$start = DateTime::createFromFormat(get_option('cqpim_date_format'), $start)->getTimestamp();
		}
		$description = isset($_POST['description']) ? $_POST['description'] : '';
		$ppid = isset($_POST['task_project_id']) ? $_POST['task_project_id'] : '';
		$weight = isset($_POST['task_weight']) ? $_POST['task_weight'] : '';
		$milestones = get_post_meta($ppid, 'project_template', true);
		$milestones['milestones'][$task_milestone_id]['tasks']['task_id'] = $task_id + 1;
		$milestones['milestones'][$task_milestone_id]['tasks']['task_arrays'][$task_id] = array(
			'id' => $task_id,
			'title' => $task_title,
			'description' => $description,
			'start' => $start,
			'deadline' => $task_deadline,
			'weight' => $weight,
			'assignee' => $assignee,
		);
		update_post_meta($ppid, 'project_template', $milestones);
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
	exit();
}
add_action( "wp_ajax_pto_create_subtask_template", 
		"pto_create_subtask_template");
function pto_create_subtask_template() {
	if(isset($_POST['task_title'])) {
		$task_title = isset($_POST['task_title']) ? $_POST['task_title'] : '';
		$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
		$task_milestone_id = isset($_POST['task_milestone_id']) ? $_POST['task_milestone_id'] : '';
		$parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : '';
		$task_deadline = isset($_POST['task_finish']) ? $_POST['task_finish'] : '';
		$assignee = isset($_POST['assignee']) ? $_POST['assignee'] : '';
		if(!empty($task_deadline)) {
			$task_deadline = DateTime::createFromFormat(get_option('cqpim_date_format'), $task_deadline)->getTimestamp();
		}
		$start = isset($_POST['start']) ? $_POST['start'] : '';
		if(!empty($start)) {
			$start = DateTime::createFromFormat(get_option('cqpim_date_format'), $start)->getTimestamp();
		}
		$description = isset($_POST['description']) ? $_POST['description'] : '';
		$ppid = isset($_POST['task_project_id']) ? $_POST['task_project_id'] : '';
		$weight = isset($_POST['task_weight']) ? $_POST['task_weight'] : '';
		$milestones = get_post_meta($ppid, 'project_template', true);
		$milestones['milestones'][$task_milestone_id]['tasks']['task_arrays'][$parent_id]['subtasks']['task_id'] = $task_id + 1;
		$milestones['milestones'][$task_milestone_id]['tasks']['task_arrays'][$parent_id]['subtasks']['task_arrays'][$task_id] = array(
			'id' => $task_id,
			'title' => $task_title,
			'description' => $description,
			'start' => $start,
			'deadline' => $task_deadline,
			'weight' => $weight,
			'assignee' => $assignee,
		);
		update_post_meta($ppid, 'project_template', $milestones);
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
	exit();
}
add_action( "wp_ajax_pto_update_task_weight_template", "pto_update_task_weight_template");
function pto_update_task_weight_template() {
	$template_id = isset($_POST['template_id']) ? $_POST['template_id'] : '';
	$weights = isset($_POST['weights']) ? $_POST['weights'] : '';
	$template = get_post_meta($template_id, 'project_template', true);
	foreach($weights as $weight) {
		$template['milestones'][$weight['ms_id']]['tasks']['task_arrays'][$weight['task_id']]['weight'] = $weight['weight'];
	}
	update_post_meta($template_id, 'project_template', $template);
	$return =  array( 
		'error' 	=> false,
		'errors' 	=> 'Task updated.'
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit;
}
add_action( "wp_ajax_pto_update_subtask_weight_template", "pto_update_subtask_weight_template");
function pto_update_subtask_weight_template() {
	$template_id = isset($_POST['template_id']) ? $_POST['template_id'] : '';
	$weights = isset($_POST['weights']) ? $_POST['weights'] : '';
	$template = get_post_meta($template_id, 'project_template', true);
	foreach($weights as $weight) {
		$template['milestones'][$weight['ms_id']]['tasks']['task_arrays'][$weight['parent_id']]['subtasks']['task_arrays'][$weight['task_id']]['weight'] = $weight['weight'];
	}
	update_post_meta($template_id, 'project_template', $template);
	$return =  array( 
		'error' 	=> false,
		'errors' 	=> 'Task updated.'
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit;
}
add_action( "wp_ajax_pto_update_task_template", 
		"pto_update_task_template");
function pto_update_task_template() {
	$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	$ms = isset($_POST['ms']) ? $_POST['ms'] : '';
	$title = isset($_POST['title']) ? $_POST['title'] : '';
	$description = isset($_POST['description']) ? $_POST['description'] : '';
	$start = isset($_POST['start']) ? $_POST['start'] : '';
	$deadline = isset($_POST['deadline']) ? $_POST['deadline'] : '';
	$assignee = isset($_POST['assignee']) ? $_POST['assignee'] : '';
	if(!empty($deadline)) {
		$deadline = DateTime::createFromFormat(get_option('cqpim_date_format'), $deadline)->getTimestamp();
	}
	if(!empty($start)) {
		$start = DateTime::createFromFormat(get_option('cqpim_date_format'), $start)->getTimestamp();
	}
	if(0) {
		$return =  array( 
			'error' 	=> true,
			'errors' 	=> 'Task ID cannot be found.'
		);
		header('Content-type: application/json');
		echo json_encode($return);			
	} else {
		$template = get_post_meta($tid, 'project_template', true);
		$template['milestones'][$ms]['tasks']['task_arrays'][$task_id]['title'] = $title;
		$template['milestones'][$ms]['tasks']['task_arrays'][$task_id]['description'] = $description;
		$template['milestones'][$ms]['tasks']['task_arrays'][$task_id]['start'] = $start;
		$template['milestones'][$ms]['tasks']['task_arrays'][$task_id]['deadline'] = $deadline;
		$template['milestones'][$ms]['tasks']['task_arrays'][$task_id]['assignee'] = $assignee;
		update_post_meta($tid, 'project_template', $template);
		$return =  array( 
			'error' 	=> false,
			'errors' 	=> 'Task updated.'
		);
		header('Content-type: application/json');
		echo json_encode($return);	
	}
	exit();
}
add_action( "wp_ajax_pto_update_subtask_template", "pto_update_subtask_template");
function pto_update_subtask_template() {
	$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	$ms = isset($_POST['ms']) ? $_POST['ms'] : '';
	$parent = isset($_POST['parent']) ? $_POST['parent'] : '';
	$title = isset($_POST['title']) ? $_POST['title'] : '';
	$description = isset($_POST['description']) ? $_POST['description'] : '';
	$start = isset($_POST['start']) ? $_POST['start'] : '';
	$deadline = isset($_POST['deadline']) ? $_POST['deadline'] : '';
	$assignee = isset($_POST['assignee']) ? $_POST['assignee'] : '';
	if(!empty($deadline)) {
		$deadline = DateTime::createFromFormat(get_option('cqpim_date_format'), $deadline)->getTimestamp();
	}
	if(!empty($start)) {
		$start = DateTime::createFromFormat(get_option('cqpim_date_format'), $start)->getTimestamp();
	}
	if(0) {
		$return =  array( 
			'error' 	=> true,
			'errors' 	=> 'Task ID cannot be found.'
		);
		header('Content-type: application/json');
		echo json_encode($return);			
	} else {
		$template = get_post_meta($tid, 'project_template', true);
		$template['milestones'][$ms]['tasks']['task_arrays'][$parent]['subtasks']['task_arrays'][$task_id]['title'] = $title;
		$template['milestones'][$ms]['tasks']['task_arrays'][$parent]['subtasks']['task_arrays'][$task_id]['description'] = $description;
		$template['milestones'][$ms]['tasks']['task_arrays'][$parent]['subtasks']['task_arrays'][$task_id]['start'] = $start;
		$template['milestones'][$ms]['tasks']['task_arrays'][$parent]['subtasks']['task_arrays'][$task_id]['deadline'] = $deadline;
		$template['milestones'][$ms]['tasks']['task_arrays'][$parent]['subtasks']['task_arrays'][$task_id]['assignee'] = $assignee;
		update_post_meta($tid, 'project_template', $template);
		$return =  array( 
			'error' 	=> false,
			'errors' 	=> 'Task updated.'
		);
		header('Content-type: application/json');
		echo json_encode($return);	
	}
	exit();
}
add_action( "wp_ajax_pto_delete_task_template", "pto_delete_task_template");
function pto_delete_task_template() {
	$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
	$ms = isset($_POST['ms']) ? $_POST['ms'] : '';
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	$template = get_post_meta($tid, 'project_template', true);	
	unset($template['milestones'][$ms]['tasks']['task_arrays'][$task_id]);
	if(empty($template['milestones'][$ms]['tasks']['task_arrays'])) {
		unset($template['milestones'][$ms]['tasks']['task_id']);
	}
	update_post_meta($tid, 'project_template', $template);		
	$return =  array( 
		'error' 	=> false,
		'messages' 	=> __('The task was successfully deleted.', 'cqpim')
	);
	header('Content-type: application/json');
	echo json_encode($return);			
	exit();
}
add_action( "wp_ajax_pto_delete_subtask_template", "pto_delete_subtask_template");
function pto_delete_subtask_template() {
	$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
	$ms = isset($_POST['ms']) ? $_POST['ms'] : '';
	$parent = isset($_POST['parent']) ? $_POST['parent'] : '';
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	$template = get_post_meta($tid, 'project_template', true);	
	unset($template['milestones'][$ms]['tasks']['task_arrays'][$parent]['subtasks']['task_arrays'][$task_id]);
	if(empty($template['milestones'][$ms]['tasks']['task_arrays'][$parent]['subtasks']['task_arrays'])) {
		unset($template['milestones'][$ms]['tasks']['task_arrays'][$parent]['subtasks']['task_id']);
	}
	update_post_meta($tid, 'project_template', $template);		
	$return =  array( 
		'error' 	=> false,
		'messages' 	=> __('The task was successfully deleted.', 'cqpim')
	);
	header('Content-type: application/json');
	echo json_encode($return);			
	exit();
}
add_action( "wp_ajax_pto_clear_all_template", "pto_clear_all_template");
function pto_clear_all_template() {
	$tid = isset($_POST['tid']) ? $_POST['tid'] : '';
	delete_post_meta($tid, 'project_template');
	$return =  array( 
		'error' 	=> false,
		'messages' 	=> __('The template was successfully cleared.', 'cqpim')
	);
	header('Content-type: application/json');
	echo json_encode($return);			
	exit();		
}
add_action( "wp_ajax_pto_apply_template", "pto_apply_template");
function pto_apply_template() {
	$item_ref = isset($_POST['quote_id']) ? $_POST['quote_id'] : '';
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	$template = isset($_POST['template']) ? $_POST['template'] : '';
	// We need to send over the highest MS ID and weight
	$hid = isset($_POST['hid']) ? $_POST['hid'] : 0;
	$hwe = isset($_POST['hwe']) ? $_POST['hwe'] : 0;
	if(empty($template)) {
		$return =  array( 
			'error' 	=> true,
			'errors' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('You must choose a template!', 'cqpim') . '</div>'
		);
		header('Content-type: application/json');
		echo json_encode($return);			
		exit();		
	} else {
		if($type == 'quote') {
			$elements = get_post_meta($item_ref, 'quote_elements', true);
		} else {
			$elements = get_post_meta($item_ref, 'project_elements', true);
			$contract_status = pto_get_contract_status($item_ref);
		}
		$template_contents = get_post_meta($template, 'project_template', true);
		if(empty($template_contents['milestones'])) {
			$return =  array( 
				'error' 	=> true,
				'errors' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('The chosen template does not contain any Milestones or Tasks.', 'cqpim') . '</div>'
			);
			header('Content-type: application/json');
			echo json_encode($return);			
			exit();			
		} else {
			if(!empty($elements)) {
				$milestones = $elements;
			} else {
				$milestones = array();
			}
			if(!empty($hid)) {
				$i = $hid;
			} else {
				$i = 0;
			}
			foreach($template_contents['milestones'] as $milestone) {
				$mtitle = isset($milestone['title']) ? $milestone['title'] : '';
				$mdeadline = isset($milestone['deadline']) ? $milestone['deadline'] : '';
				$mstart = isset($milestone['start']) ? $milestone['start'] : '';
				$mcost = isset($milestone['cost']) ? $milestone['cost'] : '';
				if(!empty($hwe)) {
					$mweight = isset($milestone['weight']) ? $milestone['weight'] : 0;
					$mweight = $mweight + $hwe;
				} else {
					$mweight = isset($milestone['weight']) ? $milestone['weight'] : 0;
				}
				$milestones[$item_ref . '-' . $i] = array(
					'title' => $milestone['title'],
					'id' => $item_ref . '-' . $i,
					'deadline' => $milestone['deadline'],
					'start' => $milestone['start'],
					'cost' => $milestone['cost'],
					'weight' => $mweight,
				);
				if(!empty($milestone['tasks']['task_arrays'])) {
					foreach($milestone['tasks']['task_arrays'] as $task) {
						$title = isset($task['title']) ? $task['title'] : '';
						$description = isset($task['description']) ? $task['description'] : '';
						$start = isset($task['start']) ? $task['start'] : '';
						$deadline = isset($task['deadline']) ? $task['deadline'] : '';
						$weight = isset($task['weight']) ? $task['weight'] : '';
						$assignee = isset($task['assignee']) ? $task['assignee'] : '';
						$new_task = array(
							'post_type' => 'cqpim_tasks',
							'post_status' => 'publish',
							'post_content' => '',
							'post_title' => $title,
							'post_password' => pto_random_string(10),
						);
						$task_pid = wp_insert_post( $new_task, true );
						if( is_wp_error( $task_pid ) ){
							$return =  array( 
								'error' 	=> true,
								'errors' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('There was a problem creating the tasks. Please try again later.', 'cqpim') . '</div>'
							);
							header('Content-type: application/json');
							echo json_encode($return);			
							exit();							
						} else {
							$task_updated = array(
								'ID' => $task_pid,
								'post_name' => $task_pid,
							);						
							wp_update_post( $task_updated );
							update_post_meta($task_pid, 'milestone_id', $item_ref . '-' . $i);	
							if($type == 'project') {
								update_post_meta($task_pid, 'project_id', $item_ref);
								if(!empty($contract_status) && $contract_status == 2) {
									update_post_meta($task_pid, 'active', true);
								}
							} else {
								update_post_meta($task_pid, 'project_id', 0);
							}
							$task_details = array(
								'deadline' => $deadline,
								'status' => 'pending',
								'task_start' => $start,
								'task_description' => $description,
								'task_pc' => 0,
								'task_priority' => 'normal',
								'weight' => $weight,
							);
							update_post_meta($task_pid, 'task_details', $task_details);	
							if(!empty($assignee) && pto_is_team_on_project($item_ref, $assignee)) {
								update_post_meta($task_pid, 'owner', $assignee);
							}							
						}
						if(!empty($task['subtasks']['task_arrays'])) {
							foreach($task['subtasks']['task_arrays'] as $subtask) {
								$title = isset($subtask['title']) ? $subtask['title'] : '';
								$description = isset($subtask['description']) ? $subtask['description'] : '';
								$start = isset($subtask['start']) ? $subtask['start'] : '';
								$deadline = isset($subtask['deadline']) ? $subtask['deadline'] : '';
								$weight = isset($subtask['weight']) ? $subtask['weight'] : '';
								$assignee = isset($subtask['assignee']) ? $subtask['assignee'] : '';
								$new_subtask = array(
									'post_type' => 'cqpim_tasks',
									'post_status' => 'publish',
									'post_content' => '',
									'post_title' => $title,
									'post_parent' => $task_pid,
									'post_password' => pto_random_string(10),
								);
								$subtask_pid = wp_insert_post( $new_subtask, true );
								if( is_wp_error( $subtask_pid ) ){
									$return =  array( 
										'error' 	=> true,
										'errors' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('There was a problem creating the tasks. Please try again later.', 'cqpim') . '</div>'
									);
									header('Content-type: application/json');
									echo json_encode($return);			
									exit();							
								} else {
									$task_updated = array(
										'ID' => $subtask_pid,
										'post_name' => $subtask_pid,
									);						
									wp_update_post( $task_updated );
									update_post_meta($subtask_pid, 'milestone_id', $item_ref . '-' . $i);	
									if($type == 'project') {
										update_post_meta($subtask_pid, 'project_id', $item_ref);
										if(!empty($contract_status) && $contract_status == 2) {
											update_post_meta($subtask_pid, 'active', true);
										}
									} else {
										update_post_meta($subtask_pid, 'project_id', 0);
									}
									$task_details = array(
										'deadline' => $deadline,
										'status' => 'pending',
										'task_start' => $start,
										'task_description' => $description,
										'task_pc' => 0,
										'task_priority' => 'normal',
										'weight' => $weight,
									);
									update_post_meta($subtask_pid, 'task_details', $task_details);	
									if(!empty($assignee) && pto_is_team_on_project($item_ref, $assignee)) {
										update_post_meta($subtask_pid, 'owner', $assignee);
									}									
								}									
							}
						}
					}
				}
				$i++;
			}
			if($type == 'quote') {
				update_post_meta($item_ref, 'quote_elements', $milestones);
			} else {
				update_post_meta($item_ref, 'project_elements', $milestones);
			}
			$return =  array( 
				'error' 	=> false,
				'messages' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('The template was successfully applied.', 'cqpim') . '</div>'
			);
			header('Content-type: application/json');
			echo json_encode($return);			
			exit();
		}	
	}
}
add_action( "wp_ajax_pto_check_template_assignees", "pto_check_template_assignees");
function pto_check_template_assignees() {
	$project = isset($_POST['project_id']) ? $_POST['project_id'] : '';
	$template = isset($_POST['template']) ? $_POST['template'] : '';
	$project_contributors = get_post_meta($project, 'project_contributors', true);
	if(empty($project_contributors)) {
		$project_contributors = array();
	}
	$template = get_post_meta($template, 'project_template', true);
	if(empty($template)) {
		$template = array();
	}
	$milestones = isset($template['milestones']) ? $template['milestones'] : '';
	$assignees = array();
	foreach($milestones as $key => $element) {
		$tasks = isset($element['tasks']['task_arrays']) ? $element['tasks']['task_arrays'] : array();
		foreach($tasks as $task) {
			if(!empty($task['assignee'])) {
				$assignees[] = $task['assignee'];
			}
			$subtasks = isset($task['subtasks']['task_arrays']) ? $task['subtasks']['task_arrays'] : array();	
			foreach($subtasks as $subtask) {
				if(!empty($subtask['assignee'])) {
					$assignees[] = $subtask['assignee'];
				}			
			}			
		}
	}
	$assignees = array_unique($assignees);
	foreach($assignees as $key => $assignee) {
		foreach($project_contributors as $contributor) {
			if($contributor['team_id'] == $assignee) {
				unset($assignees[$key]);
			}
		}
	}
	if(!empty($assignees)) {
		$message = '<div class="cqpim-alert cqpim-alert-warning alert-display">' . __('The selected template contains tasks that are assigned to team members who have not been added to this project. Please either add the following team members to the project or click Apply Template to skip assignment of the affected tasks:', 'cqpim');	
		$message .= '<br /><br />';
		foreach($assignees as $assignee) {
			$team_details = get_post_meta($assignee, 'team_details', true);
			$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : __('Team Member name not set', 'cqpim');
			$message .= $team_name . '<br />';
		}
		$return =  array( 
			'error' 	=> true,
			'message' 	=> $message . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);			
		exit();
	}
	$return =  array( 
		'error' 	=> false,
		'message' 	=> ''
	);
	header('Content-type: application/json');
	echo json_encode($return);			
	exit();
}