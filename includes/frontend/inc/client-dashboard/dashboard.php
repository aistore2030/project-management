<?php 
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if(empty($client_logs)) {
	$client_logs = array();
}
$now = current_time('timestamp');
$client_logs[$now] = array(
	'user' => $user->ID,
	'page' => __('Client Dashboard', 'cqpim')
);
update_post_meta($assigned, 'client_logs', $client_logs);
$stickets = get_option('disable_tickets');
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-full grid-item">
		<div class="cqpim_block">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-exclamation-triangle font-green-sharp" aria-hidden="true"></i>
				<span class="caption-subject font-green-sharp sbold"> <?php _e('Alerts', 'cqpim'); ?></span>
			</div>
		</div>
		<?php 
			$alerts = array();
			// Get Custom
			$custom_alerts = get_post_meta($assigned, 'custom_alerts', true);
			if(!empty($custom_alerts)) {
				foreach($custom_alerts as $key => $custom_alert) {
					if(empty($custom_alert['cleared'])) {
						$alerts[] = array(
							'type' => 'message',
							'custom' => true,
							'alert_id' => $key,
							'seen' => $custom_alert['seen'],
							'level' => $custom_alert['level'],
							'data' => $custom_alert['message'],
						);
					}
				}
			}
			// Show the Alerts
			if(!empty($alerts)) {
				echo '<ul class="cqpim_alerts">';
				foreach($alerts as $alert) {
					if(!empty($alert['custom'])) {
						if(empty($alert['seen'])) {
							$custom_alerts = get_post_meta($assigned, 'custom_alerts', true);
							$custom_alerts[$alert['alert_id']]['seen'] = current_time('timestamp');
							update_post_meta($assigned, 'custom_alerts', $custom_alerts);
						}
						echo '<li class="' . $alert['type'] . '"><div class="cqpim-alert cqpim-alert-' . $alert['level'] . ' alert-display">' . $alert['data'] . '<a class="cqpim_alert_clear" href="#" data-client="' . $assigned . '" data-alert="' . $alert['alert_id'] . '"><i class="fa fa-times"></i></a></div></li>';
					} else {
						echo '<li class="' . $alert['type'] . '"><div class="cqpim-alert cqpim-alert-' . $alert['level'] . ' alert-display">' . $alert['data'] . '</div></li>';
					}
				}
				echo '</ul>';
			} else {
				echo '<p>' . __('You have no active alerts.', 'cqpim') . '</p>';
			}
		?>
	</div>
	</div>
	<?php  if(get_option('disable_invoices') != 1) { ?>
	<div class="cqpim-dash-item-triple grid-item">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<i class="fa fa-credit-card-alt font-green-sharp" aria-hidden="true"></i>
					<span class="caption-subject font-green-sharp sbold"> <?php _e('Outstanding Invoices', 'cqpim'); ?></span>
				</div>
			</div>
			<?php
				$args = array(
					'post_type' => 'cqpim_invoice',
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'orderby' => 'date',
					'order' => 'DESC'
				);
				$invoices = get_posts($args);
				$currency = get_option('currency_symbol');
				echo '<br /><table class="cqpim_table dash">';
				echo '<thead>';
				echo '<tr><th>' . __('Invoice ID', 'cqpim') . '</th><th>' . __('Owner', 'cqpim') . '</th><th>' . __('Due Date', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th></tr>';
				echo '</thead>';
				echo '<tbody>';
				$i = 0;
				foreach($invoices as $invoice) {
					$invoice_id = get_post_meta($invoice->ID, 'invoice_id', true);
					$client_contact = get_post_meta($invoice->ID, 'client_contact', true);
					$owner = get_user_by('id', $client_contact);
					$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
					$client_id = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
					$client_details = get_post_meta($client_id, 'client_details', true);
					$client_ids = get_post_meta($client_id, 'client_ids', true);
					if(empty($client_ids)) {
						$client_ids = array();
					}									
					$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
					$invoice_totals = get_post_meta($invoice->ID, 'invoice_totals', true);
					$tax_rate = get_option('sales_tax_rate');
					if(!empty($tax_rate)) {
						$total = isset($invoice_totals['total']) ? $invoice_totals['total'] : '';
					} else {
						$total = isset($invoice_totals['sub']) ? $invoice_totals['sub'] : '';
					}					
					$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
					$sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
					$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
					$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
					$on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';
					$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
					if(empty($on_receipt)) {
						$due_readable = date('d/m/Y', $due);
					} else {
						$due_readable = __('Due on Receipt', 'cqpim');
					}
					$current_date = time();
					$link = get_the_permalink($invoice->ID);
					$password = md5($invoice->post_password);
					$url = $link;
					if(!$paid) {
						if($current_date > $due) {
							if(empty($on_receipt)) {
								$status = '<span class="cqpim_button cqpim_small_button border-red font-red sbold nolink op"><strong>' . __('Overdue', 'cqpim') . '</strong></span>';
							} else {
								$status = '<span class="cqpim_button cqpim_small_button border-red font-red nolink op"><strong>' . __('On Receipt', 'cqpim') . '</strong></span>';
							}
						} else {
							if(!$sent) {
								$status = '<span class="cqpim_button cqpim_small_button border-red font-red nolink op">' . __('New', 'cqpim') . '</span>';
							} else {
								$status = '<span class="cqpim_button cqpim_small_button border-amber font-amber nolink op">' . __('Outstanding', 'cqpim') . '</span>';
							}
						}
					} else {
						$status = '<span class="cqpim_button cqpim_small_button border-green font-green nolink op">' . __('Paid', 'cqpim') . '</span>';
					}		
					if($client_user_id == $user->ID && empty($paid) && !empty($sent) || in_array($user->ID, $client_ids) && empty($paid) && !empty($sent)) {
						echo '<tr>';
						echo '<td><span class="nodesktop"><strong>' . __('ID', 'cqpim') . '</strong>: </span> <a class="cqpim-link" href="' . $url . '" >' . $invoice_id . '</a></td>';
						echo '<td><span class="nodesktop"><strong>' . __('Owner', 'cqpim') . '</strong>: </span> ' . $owner->display_name . '</td>';
						echo '<td><span class="nodesktop"><strong>' . __('Due', 'cqpim') . '</strong>: </span> ' . $due_readable . '</td>';
						echo '<td><span class="nodesktop"><strong>' . __('Status', 'cqpim') . '</strong>: </span> ' . $status . '</td>';
						echo '</tr>';
						$i++;
					}
				}
				if($i == 0) {
					echo '<tr>';
					echo '<td colspan="4">' . __('You do not have any outstanding invoices', 'cqpim') . '</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
			?>
		</div>
	</div>
	<?php } ?>
	<div class="cqpim-dash-item-triple grid-item">
		<div class="cqpim_block">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-th font-green-sharp" aria-hidden="true"></i>
				<span class="caption-subject font-green-sharp sbold"> <?php _e('Open Projects', 'cqpim'); ?></span>
			</div>
		</div>
		<br />
		<table class="cqpim_table dash">
			<thead>
				<tr>
					<th><?php _e('Owner', 'cqpim'); ?></th>
					<th><?php _e('Title', 'cqpim'); ?></th>
					<th><?php _e('Progress', 'cqpim'); ?></th>
					<th><?php _e('Links', 'cqpim'); ?></th>
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
					$summary = $url . '?page=summary&sub=updates';
					$contract = $url . '?page=contract';
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
					$str_finish_date = str_replace('/','-', $finish_date);
					$unix_finish_date = strtotime($str_finish_date);
					$current_date = time();
					$days_to_due = round(abs($current_date - $unix_finish_date) / 86400);
					$project_elements = get_post_meta($project->ID, 'project_elements', true); 
					$contract_status = get_post_meta($project->ID, 'contract_status', true); 
					if(empty($project_elements)) {
						$project_elements = array();
					}
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
							if(!empty($task_details['status']) && $task_details['status'] != 'complete') {
								$task_count++;
							}
							if(!empty($task_details['status']) && $task_details['status'] == 'complete') {
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
					if(empty($client_ids)) { $client_ids = array(); }
					if($client_user_id == $user->ID && empty($closed) || in_array($user->ID, $client_ids) && empty($closed)) {
						if($contract_status == 2 || $contract_status == 1 && !empty($sent) || $contract_status == 1 && !empty($confirmed)) {
							?>						
							<tr>
								<td><span class="nodesktop"><strong><?php _e('Owner', 'cqpim'); ?></strong>: </span> <?php echo isset($owner->display_name) ? $owner->display_name : ''; ?></td>
								<td><span class="nodesktop"><strong><?php _e('Title', 'cqpim'); ?></strong>: </span> <a class="cqpim-link" href="<?php echo $summary; ?>"><?php echo $project->post_title; ?></a></td>
								<td><span class="nodesktop"><strong><?php _e('Progress', 'cqpim'); ?></strong>: </span> <?php echo number_format((float)$pc_complete, 2, '.', ''); ?>%</td>
								<?php if($contract_status == 1) { ?>
									<td><span class="nodesktop"><strong><?php _e('Contract', 'cqpim'); ?></strong>: </span> <a class="cqpim-link" href="<?php echo $contract; ?>"><?php _e('View Contract', 'cqpim'); ?></a></td>
								<?php } else { ?>
									<td></td>
								<?php } ?>
								<td><span class="nodesktop"><strong><?php _e('Status', 'cqpim'); ?></strong>: </span> <?php echo $status; ?></td>
							</tr>
							<?php 
							$i++;
						}
					}
				} 
				if($i == 0) {
					echo '<tr><td colspan="5">' . __('You do not have any open projects', 'cqpim') . '</td></tr>';
				}
				?>
			</tbody>
		</table>
		</div>
	</div>
	<?php if(get_option('enable_quotes') == 1) { ?>
	<div class="cqpim-dash-item-double grid-item">
		<div class="cqpim_block">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-file-text font-green-sharp" aria-hidden="true"></i>
				<span class="caption-subject font-green-sharp sbold"> <?php _e('Open Quotes / Estimates', 'cqpim'); ?></span>
			</div>
		</div>
		<br />
		<table class="cqpim_table dash">
			<thead>
				<tr>
					<th><?php _e('Title', 'cqpim'); ?></th>
					<th><?php _e('Status', 'cqpim'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$args = array(
					'post_type' => 'cqpim_quote',
					'posts_per_page' => -1,
					'post_status' => 'private',
				);
				$quotes = get_posts($args);
				$i = 0;
				foreach($quotes as $quote) { 
					$url = get_the_permalink($quote->ID); 
					$quote_details = get_post_meta($quote->ID, 'quote_details', true); 
					$client_id = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
					$client_details = get_post_meta($client_id, 'client_details', true);
					$client_ids = get_post_meta($client_id, 'client_ids', true);
					if(empty($client_ids)) {
						$client_ids = array();
					}								
					$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
					$sent = isset($quote_details['sent']) ? $quote_details['sent'] : ''; 
					$confirmed = isset($quote_details['confirmed']) ? $quote_details['confirmed'] : ''; 
					if(!$confirmed) {
						if(!$sent) {
							$status = '<span class="cqpim_button cqpim_small_button nolink op font-red border-red">' . __('Not Sent', 'cqpim') . '</span>';
						} else {
							$status = '<span class="cqpim_button cqpim_small_button nolink op font-amber border-amber">' . __('New', 'cqpim') . '</span>';
						}
					} else {
						$status = '<span class="cqpim_button cqpim_small_button nolink op font-green border-green">' . __('Accepted', 'cqpim') . '</span>';
					}
					if($client_user_id == $user->ID && empty($confirmed) && !empty($sent) || in_array($user->ID, $client_ids) && empty($confirmed) && !empty($sent)) {
					?>						
						<tr>	
							<td><span class="nodesktop"><strong><?php _e('Title', 'cqpim'); ?></strong>: </span> <a class="cqpim-link" href="<?php echo $url; ?>?page=quote"><?php echo $quote->post_title; ?></a></td>
							<td><span class="nodesktop"><strong><?php _e('Status', 'cqpim'); ?></strong>: </span> <?php echo $status; ?></td>
						</tr>
					<?php 
						$i++;
					}
				} 
				if($i == 0) {
					echo '<tr><td>' . __('You do not have any open quotes', 'cqpim') . '</td><td></td></tr>';
				}
				?>
			</tbody>
		</table>					
		</div>
	</div>
	<?php } ?>
	<?php if(empty($stickets)) { ?>
		<div class="cqpim-dash-item-triple grid-item">
			<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<i class="fa fa-life-ring font-green-sharp" aria-hidden="true"></i>
					<span class="caption-subject font-green-sharp sbold"> <?php _e('Open Support Tickets', 'cqpim'); ?></span>
				</div>
				<div class="actions">
					<a class="cqpim_button cqpim_small_button border-green-sharp font-green-sharp rounded_2 sbold" href="<?php echo get_the_permalink($client_dash) . '?page=add-support-ticket'; ?>"><?php _e('Add Support Ticket', 'cqpim'); ?></a>
				</div>
			</div>
			<br />
				<?php 
				$user = wp_get_current_user();
				$args = array(
					'post_type' => 'cqpim_support',
					'posts_per_page' => -1,
					'post_status' => 'private',
					'author__in' => $client_ids_untouched
				); 
				$tickets = get_posts($args);
				$total_tickets = count($tickets);
				if($tickets) {
					$i = 0;
					echo '<table class="cqpim_table files dash">';
					echo '<thead><tr><th>' . __('Ticket Title', 'cqpim') . '</th><th>' . __('Owner', 'cqpim') . '</th><th>' . __('Priority', 'cqpim') . '</th><th>' . __('Last Updated', 'cqpim') . '</th></tr></thead>';
					echo '<tbody>';
					foreach($tickets as $ticket) {
						$ticket_status = get_post_meta($ticket->ID, 'ticket_status', true);
						$owner = get_user_by('id', $ticket->post_author);
						if($ticket_status == 'open') {
							$tstatus = '<span class="cqpim_button cqpim_small_button font-amber border-amber op nolink">' . __('Open', 'cqpim') . '</span>';
						} else if($ticket_status == 'resolved') {
							$tstatus = '<span class="cqpim_button cqpim_small_button font-green border-green op nolink">' . __('Resolved', 'cqpim') . '</span>';
						} else if($ticket_status == 'hold') {
							$tstatus = '<span class="cqpim_button cqpim_small_button font-red border-red op nolink">' . __('On Hold', 'cqpim') . '</span>';
						} else if($ticket_status == 'waiting') {
							$tstatus = '<span class="cqpim_button cqpim_small_button font-purple border-purple op nolink">' . __('Awaiting Response', 'cqpim') . '</span>';
						}
						$ticket_priority = get_post_meta($ticket->ID, 'ticket_priority', true);
						$ticket_updated = get_post_meta($ticket->ID, 'last_updated', true);
						if(is_numeric($ticket_updated)) { $ticket_updated = date(get_option('cqpim_date_format') . ' H:i', $ticket_updated); } else { $ticket_updated = $ticket_updated; }
						if($ticket_priority == 'low') {
							$priority = '<span class="cqpim_button cqpim_small_button font-blue-sharp border-blue-sharp op nolink">' . __('Low', 'cqpim') . '</span>';
						} else if($ticket_priority == 'normal') {
							$priority = '<span class="cqpim_button cqpim_small_button font-green border-green op nolink">' . __('Normal', 'cqpim') . '</span>';
						} else if($ticket_priority == 'high') {
							$priority = '<span class="cqpim_button cqpim_small_button font-red border-red op nolink">' . __('High', 'cqpim') . '</span>';
						} else if($ticket_priority == 'immediate') {
							$priority = '<span class="cqpim_button cqpim_small_button font-red border-red op nolink sbold">' . __('Immediate', 'cqpim') . '</span>';
						}
						if($ticket_status == 'open' || $ticket_status == 'hold' || $ticket_status == 'waiting') {
							echo '<tr>';
							echo '<td><span class="nodesktop"><strong>' . __('Title', 'cqpim') . '</strong>: </span> <a class="cqpim-link" href="' . get_the_permalink($ticket->ID) . '">' . $ticket->post_title . '</a></td>';
							echo '<td><span class="nodesktop"><strong>' . __('Owner', 'cqpim') . '</strong>: </span> ' . $owner->display_name . '</td>';
							echo '<td><span class="nodesktop"><strong>' . __('Priority', 'cqpim') . '</strong>: </span> ' . $priority . '</td>';
							echo '<td><span class="nodesktop"><strong>' . __('Updated', 'cqpim') . '</strong>: </span> ' . $ticket_updated . '</td>';
							echo '</tr>';
							$i++;
						}
					}
					echo '</tbody>';
					echo '</table>';
				} else {
					echo '<table class="cqpim_table files dash">';
					echo '<thead><tr><th>' . __('Ticket Title', 'cqpim') . '</th><th>' . __('Owner', 'cqpim') . '</th><th>' . __('Priority', 'cqpim') . '</th><th>' . __('Last Updated', 'cqpim') . '</th></tr></thead>';
					echo '<tbody>';	
					echo '<tr>';
					echo '<td colspan="4">' . __('You do not have any open support tickets', 'cqpim') . '</td>';
					echo '</tr>';	
					echo '</tbody>';
					echo '</table>';					
				}
				?>
				<div class="clear"></div>
			</div>
		</div>
	<?php } ?>
	<div class="cqpim-dash-item-double grid-item">
		<div class="cqpim_block" style="max-height:500px; overflow:auto">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-tasks font-green-sharp" aria-hidden="true"></i>
				<span class="caption-subject font-green-sharp sbold"> <?php _e('Project Updates', 'cqpim'); ?></span>
			</div>
		</div>
		<br />
		<?php 
			$args = array(
				'post_type' => 'cqpim_project',
				'post_status' => 'private',
				'posts_per_page' => -1
			);
			$projects = get_posts($args);
			$updates = array();
			foreach($projects as $project) {
				$project_details = get_post_meta($project->ID, 'project_details', true);
				$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
				$client_details = get_post_meta($client_id, 'client_details', true);
				$client_ids = get_post_meta($client_id, 'client_ids', true);							
				$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
				if(empty($client_ids)) { $client_ids = array(); }
				if($client_user_id == $user->ID || in_array($user->ID, $client_ids)) {
					$project_updates = get_post_meta($project->ID, 'project_progress', true);
					if(empty($project_updates)) {
						$project_updates = array();
					}
					$i = 0;
					foreach($project_updates as $update) {
						if(!is_numeric($update['date'])) {
							$str_deadline = str_replace('/','-', $update['date']);
							$date_stamp = strtotime($str_deadline);
						} else {
							$date_stamp = $update['date'];
						}								
						$updates[$date_stamp . $i] = array(
							'pid' => $project->ID,
							'by' => $update['by'],
							'date' => $update['date'],
							'update' => $update['update'],
						);
						$i++;
					}
				}
			}
			ksort($updates);
			$updates = array_reverse($updates);
			echo '<ul class="project_summary_progress">';
			foreach($updates as $pupdate) {
				$project_details = get_post_meta($pupdate['pid'], 'project_details', true);
				$url = get_the_permalink($pupdate['pid']);
				$project_obj = get_post($pupdate['pid']);
				$project_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
				if(is_numeric($pupdate['date'])) { $pupdate['date'] = date(get_option('cqpim_date_format') . ' H:i', $pupdate['date']); } else { $pupdate['date'] = $pupdate['date']; }
				echo '<li style="margin-bottom:0">'; ?>
					<div class="timeline-entry">
						<?php if(empty($avatar)) {
							echo '<div class="update-who">';
							echo get_avatar( pto_get_user_id_by_display_name($pupdate['by']), 60, '', false, array('force_display' => true) );
							echo '</div>';
						} ?>
						<?php if(empty($avatar)) { ?>
							<div class="update-data">
						<?php } else { ?>
							<div style="width:100%; float:none" class="update-data">
						<?php } ?>
							<div class="timeline-body-arrow"> </div>
							<div class="timeline-by font-blue-madison sbold"><?php echo $pupdate['by']; ?></div>
							<div class="clear"></div>
							<div class="timeline-update font-grey-cascade"><a class="cqpim-link font-grey-cascade" href="<?php echo $url; ?>?page=summary&sub=updates"><?php echo $project_obj->post_title; ?></a> - <?php echo $pupdate['update']; ?></div>
							<div class="clear"></div>
							<div class="timeline-date font-grey-cascade"><?php echo $pupdate['date']; ?></div>
						</div>
						<div class="clear"></div>
					</div>
				<?php echo '</li>';
			}
			echo '</ul>';
			if(empty($updates)) {
				echo '<p>' . __('There are no project updates to show.', 'cqpim') . '</p>';
			}
		?>
		</div>
	</div>
</div>