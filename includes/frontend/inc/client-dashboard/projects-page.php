<?php 
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if(empty($client_logs)) {
	$client_logs = array();
}
$now = current_time('timestamp');
$client_logs[$now] = array(
	'user' => $user->ID,
	'page' => __('Client Dashboard Projects Page', 'cqpim')
);
update_post_meta($assigned, 'client_logs', $client_logs);
$client_contract = get_post_meta($assigned, 'client_contract', true);
?>
<br />
<div class="cqpim_block">
<div class="cqpim_block_title">
	<div class="caption">
		<i class="fa fa-th font-green-sharp" aria-hidden="true"></i>
		<span class="caption-subject font-green-sharp sbold"><?php _e('Projects', 'cqpim'); ?></span>
	</div>
</div>
<br />
<table class="datatable_style dataTable-CP">
	<thead>
		<tr>
			<th><?php _e('Owner', 'cqpim'); ?></th>
			<th><?php _e('Title', 'cqpim'); ?></th>
			<th><?php _e('Actions', 'cqpim'); ?></th>
			<th><?php _e('Progress', 'cqpim'); ?></th>
			<th><?php _e('Open Tasks', 'cqpim'); ?></th>
			<th><?php _e('Days Until Launch', 'cqpim'); ?></th>
			<th><?php _e('Status', 'cqpim'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$args = array(
			'post_type' => 'cqpim_project',
			'posts_per_page' => -1,
			'post_status' => 'private',
		);
		$projects = get_posts($args);
		$i = 0;
		foreach($projects as $project) { 
			$url = get_the_permalink($project->ID); 
			$contract = $url . '?page=contract';
			$summary = $url . '?page=summary&sub=updates';
			$project_details = get_post_meta($project->ID, 'project_details', true); 
			$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
			$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
			$owner = get_user_by('id', $client_contact);
			$client_details = get_post_meta($client_id, 'client_details', true);
			$client_ids = get_post_meta($client_id, 'client_ids', true);
			$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
			$sent = isset($project_details['sent']) ? $project_details['sent'] : ''; 
			$confirmed = isset($project_details['confirmed']) ? $project_details['confirmed'] : ''; 
			$signoff = isset($project_details['signoff']) ? $project_details['signoff'] : ''; 
			$closed = isset($project_details['closed']) ? $project_details['closed'] : ''; 
			$finish_date = isset($project_details['finish_date']) ? $project_details['finish_date'] : '';
			if(!is_numeric($finish_date)) {
				$str_finish_date = str_replace('/','-', $finish_date);
				$unix_finish_date = strtotime($str_finish_date);
			} else {
				$unix_finish_date = $finish_date;
			}
			$current_date = time();
			$days_to_due = round(abs($current_date - $unix_finish_date) / 86400);
			$project_elements = get_post_meta($project->ID, 'project_elements', true); 
			if(empty($project_elements)) {
				$project_elements = array();
			}
			$contract_status = get_post_meta($project->ID, 'contract_status', true); 
			$task_count = 0;
			$task_total_count = 0;
			$task_complete_count = 0;
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
					$task_total_count++;
					$task_details = get_post_meta($task->ID, 'task_details', true);
					$status = isset($task_details['status']) ? $task_details['status']: '';
					if($status != 'complete') {
						$task_count++;
					}
					if($status == 'complete') {
						$task_complete_count++;
					}
				}
			}
			if($task_total_count != 0) {
				$pc_per_task = 100 / $task_total_count;
				$pc_complete = $pc_per_task * $task_complete_count;
			} else {
				$pc_complete = 0;
			}
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
			if(!is_array($client_ids)) {
				$client_ids = array($client_ids);
			}
			if($client_user_id == $user->ID || in_array($user->ID, $client_ids)) {
				if($contract_status == 2 || $contract_status == 1 && !empty($sent) || $contract_status == 1 && !empty($confirmed)) { ?>						
					<tr>
						<td><span class="nodesktop"><strong><?php _e('Owner', 'cqpim'); ?></strong>: </span> <?php echo isset($owner->display_name) ? $owner->display_name : ''; ?></td>									
						<td><span class="nodesktop"><strong><?php _e('Title', 'cqpim'); ?></strong>: </span> <a class="cqpim-link" href="<?php echo $summary; ?>"><?php echo $project->post_title; ?></a></td>
						<?php if($contract_status == 1) { ?>
							<td><span class="nodesktop"><strong><?php _e('Contract', 'cqpim'); ?></strong>: </span> <a class="cqpim-link" href="<?php echo $contract; ?>"><?php _e('View Contract', 'cqpim'); ?></a></td>
						<?php } else { ?>
							<td></td>
						<?php } ?>
						<td><span class="nodesktop"><strong><?php _e('Progress', 'cqpim'); ?></strong>: </span> <?php echo number_format((float)$pc_complete, 2, '.', ''); ?>%</td>
						<td><span class="nodesktop"><strong><?php _e('Open Tasks', 'cqpim'); ?></strong>: </span> <?php echo $task_count; ?></td>
						<td><span class="nodesktop"><strong><?php _e('Days to Launch', 'cqpim'); ?></strong>: </span> <?php echo $days_to_due; ?></td>
						<td><span class="nodesktop"><strong><?php _e('Status', 'cqpim'); ?></strong>: </span> <?php echo $status; ?></td>
					</tr>					
					<?php 
					$i++;
				}
			}
		} 
		if($i == 0) {
			echo '<tr><td>' . __('You do not have any current or past Projects', 'cqpim') . '</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
		}
		?>
	</tbody>
</table>
</div>