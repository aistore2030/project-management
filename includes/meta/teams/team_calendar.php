<?php
function pto_team_calendar_metabox_callback( $post ) { 
	$assigned = $post->ID; ?>

	<div class="actions">
		<?php $calendar_filters = isset($_SESSION['cal_filters']) ? $_SESSION['cal_filters'] : array('invoices', 'projects', 'milestones', 'tasks'); ?>
		<?php _e('Show: ', 'cqpim'); ?> &nbsp;&nbsp;
		<input type="checkbox" class="calendar_filter" value="projects" <?php if(in_array('projects', $calendar_filters)) { echo 'checked="checked"'; } ?> /> <?php _e('Projects', 'cqpim'); ?> &nbsp;&nbsp;&nbsp;
		<input type="checkbox" class="calendar_filter" value="milestones" <?php if(in_array('milestones', $calendar_filters)) { echo 'checked="checked"'; } ?> /> <?php _e('Milestones', 'cqpim'); ?> &nbsp;&nbsp;&nbsp;
		<input type="checkbox" class="calendar_filter" value="tasks" <?php if(in_array('tasks', $calendar_filters)) { echo 'checked="checked"'; } ?> /> <?php _e('Tasks', 'cqpim'); ?>
	</div>

	<?php			
	$args = array(
		'post_type' => 'cqpim_project',
		'posts_per_page' => -1,
		'post_status' => 'private'
	);
	$projects = get_posts($args);
	$projects_to_add = array();
	$index = 0;
	foreach($projects as $project) {
		$project_details = get_post_meta($project->ID, 'project_details', true);
		if(current_user_can('cqpim_view_all_projects')) { $index++; 
			if(!empty($project_details['confirmed'])) {
				$projects_to_add[] = $project;
			}
		} else {
			$project_contributors = get_post_meta($project->ID, 'project_contributors', true);
			if(!is_array($project_contributors)) {
				$project_contributors = array($project_contributors);
			}
			$contrib_ids = array();
			foreach($project_contributors as $contrib) {
					$contrib_ids[] = $contrib['team_id'];
			}
			if(in_array($assigned, $contrib_ids)) { $index++; 
				if(!empty($project_details['confirmed'])) {
					$projects_to_add[] = $project;	
				}
			}
		}			
	}	
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
	?>
	<script>
	jQuery(document).ready(function() {
		jQuery('#calendar').fullCalendar({
			lang: "<?php echo substr(get_locale(), 0, 2) ?>",
			events: [
			<?php 
			if(in_array('projects', $calendar_filters)) {
				foreach($projects_to_add as $project) {
					$project_details = get_post_meta($project->ID, 'project_details', true);
					$project_object = get_post($project->ID);
					$url = get_edit_post_link($project->ID);
					$url = str_replace('&amp;', '&', $url);
					$quote_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
					$start_date = isset($project_details['start_date']) ? $project_details['start_date'] : '';
					if(!empty($start_date)) {
						$start_date = date('Y-m-d', $start_date);
					}
					$finish_date = isset($project_details['finish_date']) ? $project_details['finish_date'] : '';
					if(!empty($finish_date)) {
						$finish_date = date('Y-m-d', $finish_date);
					}
					if(is_numeric($finish_date)) {
						$finish_date = $finish_date + 86400;
					}
					if(!empty($start_date) && !empty($finish_date)) {
						echo '{';
						echo 'title : "' . __('PROJECT', 'cqpim') . ': ' . htmlspecialchars($project_object->post_title) . '",';
						echo 'start : "' . $start_date . '",';
						echo 'end : "' . $finish_date . '",';
						echo 'color : "#3B3F51",';
						echo 'url : "' . $url . '"';
						echo '},';
					}
				} 
			}
			if(in_array('milestones', $calendar_filters)) {
				foreach($projects_to_add as $project) {
					$project_elements = get_post_meta($project->ID, 'project_elements', true);
					$url = get_edit_post_link($project->ID);
					$url = str_replace('&amp;', '&', $url);
					if(empty($project_elements)) {
						$project_elements = array();
					}
					foreach($project_elements as $element) {
						$project_object = get_post($project->ID);
						$task_title = isset($element['title']) ? $element['title'] : '';
						$task_start = isset($element['start']) ? $element['start'] : '';
						if(!empty($task_start)) {
							$task_start = date('Y-m-d', $task_start);
						}
						$task_deadline = isset($element['deadline']) ? $element['deadline'] : '';
						if(is_numeric($task_deadline)) {
							$task_deadline = $task_deadline + 86400;
						}
						if(!empty($task_deadline)) {
							$task_deadline = date('Y-m-d', $task_deadline);
						}
						if(!empty($task_start) && !empty($task_deadline)) {
							echo '{';
							echo 'title : "' . __('MILESTONE', 'cqpim') . ': ' . htmlspecialchars($project_object->post_title) . ' - ' . htmlspecialchars($task_title) . '",';										
							if(!empty($task_start)) {
								echo 'start : "' . $task_start . '",';
							}
							if(!empty($task_deadline)) {
								echo 'end : "' . $task_deadline . '",';
							}
							echo 'color : "#337ab7",';
							echo 'url : "' . $url . '"';
							echo '},';
						}
					}
				}
			}
			if(in_array('tasks', $calendar_filters)) {
				foreach($tasks as $task) {
					$task_object = get_post($task->ID);
					$url = get_edit_post_link($task->ID);
					$url = str_replace('&amp;', '&', $url);
					$task_details = get_post_meta($task->ID, 'task_details', true);
					$task_start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
					$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
					if(!empty($task_start)) {
						$task_start = date('Y-m-d', $task_start);
					}
					if(is_numeric($task_deadline)) {
						$task_deadline = $task_deadline + 86400;
					}
					if(!empty($task_deadline)) {
						$task_deadline = date('Y-m-d', $task_deadline);
					}
					if(!empty($task_start) && !empty($task_deadline)) {
						echo '{';
						echo 'title : "' . __('TASK', 'cqpim') . ': ' . htmlspecialchars($task_object->post_title) . '",';
						if(!empty($task_start)) {
							echo 'start : "' . $task_start . '",';
						}
						if(!empty($task_deadline)) {
							echo 'end : "' . $task_deadline . '",';
						}
						echo 'color : "#36c6d3",';
						echo 'url : "' . $url . '"';
						echo '},';
					}
				}
			}
			?>
			],						   
		});
	});
	</script>
	<div class="clear"></div>
	<br />
	<div id="calendar_container">
		<div id="calendar">
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>

<?php }