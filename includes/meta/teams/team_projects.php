<?php
function pto_team_projects_metabox_callback( $post ) {
	$args = array(
		'post_type' => 'cqpim_project',
		'posts_per_page' => -1,
		'post_status' => 'private',
	);				
	$projects = get_posts($args);
	if($projects) {
		$i = 0;
		?><table class="datatable_style dataTable" data-ordering="false" data-rows="10">
		<?php
		echo '<thead>';
		echo '<th>' . __('Project Title', 'cqpim') . '</th><th>' . __('Open Tasks', 'cqpim') . '</th><th>' . __('Progress', 'cqpim') . '</th><th>' . __('Team Members', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th>';
		echo '</thead>';
		echo '<tbody>';
		foreach($projects as $project) {
			$project_contributors = get_post_meta($project->ID, 'project_contributors', true);
			if(empty($project_contributors)) {
				$project_contributors = array();
			}
			$project_contributors_id = array();
			foreach($project_contributors as $contributor) {
				if(!empty($contributor['team_id'])) {
					$project_contributors_id[] = $contributor['team_id'];
				}
			}
			if(in_array($post->ID, $project_contributors_id)) {
				$project_details = get_post_meta($project->ID, 'project_details', true);
				$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
				$project_edit = get_edit_post_link($project->ID);
				$project_title = get_the_title($project->ID);
				$project_elements = get_post_meta($project->ID, 'project_elements', true);
				$sent = isset($project_details['sent']) ? $project_details['sent'] : '';
				$confirmed = isset($project_details['confirmed']) ? $project_details['confirmed'] : '';
				$signoff = isset($project_details['signoff']) ? $project_details['signoff'] : '';
				$closed = isset($project_details['closed']) ? $project_details['closed'] : '';
				$start_date = isset($project_details['start_date']) ? $project_details['start_date'] : '';
				$contract_status = get_post_meta($project->ID, 'contract_status', true);
				if(!empty($client_id)) {
					if(!$closed) {
						if(!$signoff) {
							if($contract_status == 1) {
								if(!$confirmed) {
									if(!$sent) {
										$status = '<span class="cqpim_button cqpim_small_button nolink op border-red font-red">' . __('New', 'cqpim') . '</span>';
									} else {
										$status = '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . __('Awaiting Contracts', 'cqpim') . '</span>';
									}
								} else {
									$status = '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . __('In Progress', 'cqpim') . '</span>';
								}
							} else {
								$status = '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . __('In Progress', 'cqpim') . '</span>';
							}
						} else {
							$status = '<span class="cqpim_button cqpim_small_button nolink op border-blue font-blue">' . __('Signed Off', 'cqpim') . '</span>';
						}
					} else {
						$status = '<span class="cqpim_button cqpim_small_button nolink op border-grey-cascade font-grey-cascade">' . __('Closed', 'cqpim') . '</span>';
					}
				} else {
					if(!$project_details['closed']) {
						$status = '<div class="cqpim_button cqpim_small_button font-blue border-blue op nolink">' . __('In Progress', 'cqpim') . '</div>';
					} else {
						$status = '<div class="cqpim_button cqpim_small_button font-grey-cascade border-grey-cascade op nolink">' . __('Closed', 'cqpim') . '</div>';
					}
				}
				$task_count = 0;
				$task_total_count = 0;
				$task_complete_count = 0;
				if(empty($project_elements)) {
					$project_elements = array();
				}
				foreach ($project_elements as $element) {
					$args = array(
						'post_type' => 'cqpim_tasks',
						'posts_per_page' => -1,
						'meta_key' => 'milestone_id',
						'meta_value' => $element['id'],
						'orderby' => 'date',
						'order' => 'ASC'
					);
					$tasks = get_posts($args);
					foreach($tasks as $task) {
						$task_details = get_post_meta($task->ID, 'task_details', true);
						$task_total_count++;
						$task_details = get_post_meta($task->ID, 'task_details', true);
						if($task_details['status'] != 'complete') {
							$task_count++;
						}
						if($task_details['status'] == 'complete') {
							$task_complete_count++;
						}
						$pc_per_task = 100 / $task_total_count;
						$pc_complete = $pc_per_task * $task_complete_count;
					}
				}
				if(empty($pc_complete)) {
					$pc_complete = 0;
				}
				echo '<tr>';
				echo '<td><span class="cqpim_mobile">' . __('Title:', 'cqpim') . '</span> <a href="' . $project_edit . '" target="_blank">' . $project_title . '</a></td>';
				echo '<td><span class="cqpim_mobile">' . __('Open Tasks:', 'cqpim') . '</span> ' . $task_count . '</td>';
				echo '<td><span class="cqpim_mobile">' . __('Complete:', 'cqpim') . '</span> ' . number_format((float)$pc_complete, 2, ".", "") . '%</td>';
				echo '<td>';
				if($project_contributors) {
					echo '<ul>';
					foreach($project_contributors as $contributor) {
						$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
						$team_edit = get_edit_post_link($contributor['team_id']);
						echo '<li><a href="' . $team_edit . '" target="_blank">' . $team_details['team_name'] . '</a></li>';
					}
					echo '</ul>';
				}
				echo '</td>';
				echo '<td>' . $status . '</td>';
				echo '</tr>';						
				$i++;
			}
		}
		echo '</tbody>';
		echo '</table>';
		if($i == 0) {
			echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('This team member has not been assigned to any projects.', 'cqpim') . '</div>';
		}
	} else {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('There are no active projects available', 'cqpim') . '</div>';
	}
}