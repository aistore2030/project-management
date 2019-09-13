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
	'page' => sprintf(__('Project %1$s - %2$s (Update / Status Page)', 'cqpim'), get_the_ID(), $title)
);
update_post_meta($assigned, 'client_logs', $client_logs);
$contract_status = get_post_meta($post->ID, 'contract_status', true); 
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-double grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php echo $title; ?></span>
				</div>	
			</div>
			<ul class="project_stats">
				<li><strong><span class="project_stat_head"><?php _e('Open Tasks: ' , 'cqpim') ?></span></strong><br /><span class="project_stat"><?php echo $task_count; ?></span></li>
				<li><strong><span class="project_stat_head"><?php _e('Complete: ' , 'cqpim') ?></span></strong><br /><span class="project_stat"><?php echo number_format((float)$pc_complete, 2, '.', ''); ?>%</span></li>
				<li><strong><span class="project_stat_head"><?php _e('Days to Launch!', 'cqpim'); ?></span></strong><br /><span class="project_stat"><?php echo $days_to_due; ?></span></li>
			</ul>
		</div>
	</div>
	<div class="cqpim-dash-item-triple grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php _e('Project Status', 'cqpim'); ?></span>
				</div>	
			</div>
			<table class="cqpim_table sum-status">
				<tbody>
					<?php if($contract_status == 1 || $deposit && $deposit != 'none' && get_option('disable_invoices') != 1) { ?>
						<tr>
							<th style="border-top:0;" colspan="2"><?php _e('Prerequisites', 'cqpim'); ?></th>
						</tr>
						<?php $status = ( !empty( $sent ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . __('Complete', 'cqpim') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . __('Pending', 'cqpim') . '</span>'; ?>
						<?php $checked = get_option('enable_project_contracts'); 
						if(!empty($checked) && empty($client_contract)) { ?>
							<tr>
								<td><?php _e('Contract Sent', 'cqpim'); ?></td>
								<td><?php echo $status; ?></td>
							</tr>
							<?php $status = ( !empty( $confirmed ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . __('Complete', 'cqpim') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . __('Pending', 'cqpim') . '</span>'; ?>
							<tr>
								<td><a href="<?php echo get_the_permalink($post->ID) . '?page=contract'; ?>"><?php _e('Contract Signed', 'cqpim'); ?></a></td>
								<td><?php echo $status; ?></td>
							</tr>
						<?php } ?>
						<?php if($deposit && $deposit != 'none' && get_option('disable_invoices') != 1) { ?>
						<?php $status = ( !empty( $deposit_invoice_id ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . __('Complete', 'cqpim') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . __('Pending', 'cqpim') . '</span>'; ?>
						<tr>
							<td><?php _e('Deposit Invoice Created', 'cqpim'); ?></td>
							<td><?php echo $status; ?></td>
						</tr>
						<?php $status = ( !empty( $deposit_sent ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . __('Complete', 'cqpim') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . __('Pending', 'cqpim') . '</span>'; ?>
						<tr>
							<td><?php _e('Deposit Invoice Sent', 'cqpim'); ?></td>
							<td><?php echo $status; ?></td>
						</tr>	
						<?php $status = ( !empty( $deposit_paid ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . __('Complete', 'cqpim') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . __('Pending', 'cqpim') . '</span>'; ?>
						<tr>
							<td><?php _e('Deposit Paid', 'cqpim'); ?></td>
							<td><?php echo $status; ?></td>
						</tr>						
						<?php } ?>
					<?php } ?>
					<tr>
						<th colspan="2"><?php _e('Milestones', 'cqpim'); ?></th>
					</tr>
					<?php
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
					$status = isset($element['status']) ? $element['status'] : ''; ?>
					<?php $status = (  $status == 'complete' ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . __('Complete', 'cqpim') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . __('Pending', 'cqpim') . '</span>'; ?>
					<tr>
						<td><?php echo $element['title']; ?></td>
						<td><?php echo $status; ?></td>
					</tr>
					<?php } ?>
					<tr>
						<th colspan="2"><?php _e('Completion', 'cqpim'); ?></th>
					</tr>	
					<?php $status = ( !empty( $signoff ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . __('Complete', 'cqpim') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . __('Pending', 'cqpim') . '</span>'; ?>
					<tr>
						<td><?php _e('Signed Off / Launched', 'cqpim'); ?></td>
						<td><?php echo $status; ?></td>
					</tr>
					<?php if(get_option('disable_invoices') != 1 && get_option('invoice_workflow') != 1) { ?>
					<?php $status = ( !empty( $completion_invoice_id ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . __('Complete', 'cqpim') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . __('Pending', 'cqpim') . '</span>'; ?>
					<tr>
						<td><?php _e('Completion Invoice Created', 'cqpim'); ?></td>
						<td><?php echo $status; ?></td>
					</tr>
					<?php $status = ( !empty( $completion_sent ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . __('Complete', 'cqpim') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . __('Pending', 'cqpim') . '</span>'; ?>
					<tr>
						<td><?php _e('Completion Invoice Sent', 'cqpim'); ?></td>
						<td><?php echo $status; ?></td>
					</tr>	
					<?php $status = ( !empty( $completion_paid ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . __('Complete', 'cqpim') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . __('Pending', 'cqpim') . '</span>'; ?>
					<tr>
						<td><?php _e('Completion Invoice Paid', 'cqpim'); ?></td>
						<td><?php echo $status; ?></td>
					</tr>	
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="cqpim-dash-item-double grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php _e('Project Updates', 'cqpim'); ?></span>
				</div>	
			</div>
			<?php if($project_progress) {
				$project_progress = array_reverse($project_progress);
				echo '<ul style="max-height:500px; overflow:auto" class="project_summary_progress">';
				foreach($project_progress as $progress) {
					if(is_numeric($progress['date'])) { $progress['date'] = date(get_option('cqpim_date_format') . ' H:i', $progress['date']); } else { $progress['date'] = $progress['date']; }		
					?>
						<li style="margin-bottom:0">
							<div class="timeline-entry">
								<?php if(empty($avatar)) {
									echo '<div class="update-who">';
									echo get_avatar( pto_get_user_id_by_display_name($progress['by']), 60, '', false, array('force_display' => true) );
									echo '</div>';
								} ?>
								<?php if(empty($avatar)) { ?>
									<div class="update-data">
								<?php } else { ?>
									<div style="width:100%; float:none" class="update-data">
								<?php } ?>
									<div class="timeline-body-arrow"> </div>
									<div class="timeline-by font-blue-madison sbold"><?php echo $progress['by']; ?></div>
									<div class="clear"></div>
									<div class="timeline-update font-grey-cascade"><?php echo $progress['update']; ?></div>
									<div class="clear"></div>
									<div class="timeline-date font-grey-cascade"><?php echo $progress['date']; ?></div>
								</div>
								<div class="clear"></div>
							</div>
						</li>						
					<?php
				}
				echo '</ul>';
			} ?>
		</div>
	</div>		
</div>