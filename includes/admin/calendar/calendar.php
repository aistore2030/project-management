<?php
add_action( 'admin_menu' , 'register_pto_calendar_page', 10 ); 
function register_pto_calendar_page() {
	$mypage = add_submenu_page(	
				'pto-dashboard',
				__('My Calendar', 'cqpim'), 			
				__('My Calendar', 'cqpim'), 			
				'edit_cqpim_projects', 			
				'pto-calendar', 		
				'pto_calendar'
	);
	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
}
function pto_calendar() { 
	$user = wp_get_current_user(); 
	$roles = $user->roles;
	$assigned = pto_get_team_from_userid();
	?>
	<div class="masonry-grid">
		<div class="cqpim-dash-item-full grid-item tasks-box">
			<div class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<i class="fa fa-calendar font-green-sharp" aria-hidden="true"></i>
						<span class="caption-subject font-green-sharp sbold"> <?php _e('My Calendar', 'cqpim'); ?></span>
					</div>
					<div class="actions">
						<?php $calendar_filters = isset($_SESSION['cal_filters']) ? $_SESSION['cal_filters'] : array('invoices', 'projects', 'milestones', 'tasks'); ?>
						<?php _e('Show: ', 'cqpim'); ?> &nbsp;&nbsp;&nbsp;&nbsp;
						<?php if(current_user_can('edit_cqpim_invoices')) { ?>
						<input type="checkbox" class="calendar_filter" value="invoices" <?php if(in_array('invoices', $calendar_filters)) { echo 'checked="checked"'; } ?> /> <?php _e('Invoices', 'cqpim'); ?> &nbsp;&nbsp;&nbsp;
						<?php } ?>
						<input type="checkbox" class="calendar_filter" value="projects" <?php if(in_array('projects', $calendar_filters)) { echo 'checked="checked"'; } ?> /> <?php _e('Projects', 'cqpim'); ?> &nbsp;&nbsp;&nbsp;
						<input type="checkbox" class="calendar_filter" value="milestones" <?php if(in_array('milestones', $calendar_filters)) { echo 'checked="checked"'; } ?> /> <?php _e('Milestones', 'cqpim'); ?> &nbsp;&nbsp;&nbsp;
						<input type="checkbox" class="calendar_filter" value="tasks" <?php if(in_array('tasks', $calendar_filters)) { echo 'checked="checked"'; } ?> /> <?php _e('Tasks', 'cqpim'); ?>
					</div>
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
						$contract_status = pto_get_contract_status($project->ID);
						if(!empty($project_details['confirmed']) || $contract_status == 2 ) {
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
							$contract_status = pto_get_contract_status($project->ID);
							if(!empty($project_details['confirmed']) || $contract_status == 2 ) {
								$projects_to_add[] = $project;	
							}
						}
					}			
				}
				$args = array(
					'post_type' => 'cqpim_invoice',
					'posts_per_page' => -1,
					'post_status' => 'publish'
				);
				$invoices = get_posts($args);
				$this_client = array();
				foreach($invoices as $invoice) {
					$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
					$paid = isset($invoice_details['paid']) ? $invoice_details['paid']: '';
					if(1) {
						$this_client[] = $invoice;
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
								$project_colours = get_post_meta($project->ID, 'project_colours', true);
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
									if(!empty($project_colours['project_colour'])) {
										echo 'color : "' . $project_colours['project_colour'] . '",';
									} else {
										echo 'color : "#3B3F51",';
									}
									echo 'url : "' . $url . '"';
									echo '},';
								}
							} 
						}
						if(in_array('milestones', $calendar_filters)) {
							foreach($projects_to_add as $project) {
								$project_elements = get_post_meta($project->ID, 'project_elements', true);
								$project_colours = get_post_meta($project->ID, 'project_colours', true);
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
										if(!empty($project_colours['ms_colour'])) {
											echo 'color : "' . $project_colours['ms_colour'] . '",';
										} else {
											echo 'color : "#337ab7",';
										}
										echo 'url : "' . $url . '"';
										echo '},';
									}
								}
							}
						}
						if(current_user_can('edit_cqpim_invoices') && in_array('invoices', $calendar_filters)) {
							foreach($this_client as $invoice) {
								$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
								$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';	
								$invoice_id = get_post_meta($invoice->ID, 'invoice_id', true);
								$due = date('Y-m-d', $due);
								$url = get_edit_post_link($invoice->ID);
								$url = str_replace('&amp;', '&', $url);
								echo '{';
								echo 'title : "' . __('INVOICE DUE', 'cqpim') . ': ' . $invoice_id . '",';								
								echo 'start : "' . $due . '",';
								echo 'end : "' . $due . '",';
								echo 'color : "#F1C40F",';
								echo 'url : "' . $url . '"';
								echo '},';									
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
								$pid = get_post_meta($task->ID, 'project_id', true);
								$project_colours = get_post_meta($pid, 'project_colours', true);
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
									if(pto_is_task_overdue($task->ID) == 1) {
										echo 'color : "#e7505a",';
									} else {
										if(!empty($project_colours['task_colour'])) {
											echo 'color : "' . $project_colours['task_colour'] . '",';
										} else {
											echo 'color : "#36c6d3",';
										}
									}
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
			</div>
		</div>
	</div>	
<?php }