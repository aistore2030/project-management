<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-life-ring font-green-sharp" aria-hidden="true"></i> <?php _e('Support Tickets', 'cqpim'); ?>
		</div>
		<div class="actions">
			<a href="<?php echo get_the_permalink($client_dash) . '?page=add-support-ticket'; ?>" class="cqpim_button cqpim_small_button font-green-sharp border-green-sharp op"><?php _e('Add Support Ticket', 'cqpim'); ?></a>
		</div>
	</div>
	<?php 
	$show_open_warning = get_option('pto_support_opening_warning');
	if(!empty($show_open_warning)) {
		$open = pto_return_open();
		if($open == 1) {
			$message = get_option('pto_support_closed_message');
			echo '<div class="cqpim-alert cqpim-alert-warning alert-display">' . $message . '</div>';
		} else if($open == 2) {
			$message = get_option('pto_support_open_message');
			echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . $message . '</div>';
		}
	}
	?>
	<div class="cqpim-dash-item-inside">
		<div id="ticket_container" class="content">
			<?php 
			$user = wp_get_current_user();
			$status = isset($_SESSION['ticket_status']) ? $_SESSION['ticket_status'] : array('open','hold','waiting');
			$args = array(
			'post_type' => 'cqpim_support',
			'posts_per_page' => -1,
			'post_status' => 'private',
			'author__in' => $client_ids_untouched,
			'meta_key' => 'ticket_status',
			'meta_value' => $status
			); 
			$tickets = get_posts($args);
			$total_tickets = count($tickets);
			if($tickets) {
				echo '<table class="datatable_style dataTable-CST">';
				echo '<thead><tr><th>' . __('ID', 'cqpim') . '</th><th>' . __('Ticket Title', 'cqpim') . '</th><th>' . __('Ticket Owner', 'cqpim') . '</th><th>' . __('Priority', 'cqpim') . '</th><th>' . __('Created', 'cqpim') . '</th><th>' . __('Last Updated', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th></tr></thead>';
				echo '<tbody>';
				foreach($tickets as $ticket) {
				$ticket_status = get_post_meta($ticket->ID, 'ticket_status', true);
				$owner = get_user_by('id', $ticket->post_author);
				if($ticket_status == 'open') {
					$tstatus = '<span class="cqpim_button cqpim_small_button op border-blue font-blue">' . __('Open', 'cqpim') . '</span>';
				} else if($ticket_status == 'resolved') {
					$tstatus = '<span class="cqpim_button cqpim_small_button op border-green font-green">' . __('Resolved', 'cqpim') . '</span>';
				} else if($ticket_status == 'hold') {
					$tstatus = '<span class="cqpim_button cqpim_small_button op border-amber font-amber">' . __('On Hold', 'cqpim') . '</span>';
				} else if($ticket_status == 'waiting') {
					$tstatus = '<span class="cqpim_button cqpim_small_button op border-purple font-purple">' . __('Awaiting Response', 'cqpim') . '</span>';
				}
				$ticket_priority = get_post_meta($ticket->ID, 'ticket_priority', true);
				$ticket_updated = get_post_meta($ticket->ID, 'last_updated', true);
				if(is_numeric($ticket_updated)) { $ticket_updated = date(get_option('cqpim_date_format') . ' H:i', $ticket_updated); } else { $ticket_updated = $ticket_updated; }
				if($ticket_priority == 'low') {
					$priority = '<span class="cqpim_button cqpim_small_button op border-blue font-blue">' . __('Low', 'cqpim') . '</span>';
				} else if($ticket_priority == 'normal') {
					$priority = '<span class="cqpim_button cqpim_small_button op border-amber font-amber">' . __('Normal', 'cqpim') . '</span>';
				} else if($ticket_priority == 'high') {
					$priority = '<span class="cqpim_button cqpim_small_button op border-red font-red">' . __('High', 'cqpim') . '</span>';
				} else if($ticket_priority == 'immediate') {
					$priority = '<span class="cqpim_button cqpim_small_button op border-red font-red sbold">' . __('Immediate', 'cqpim') . '</span>';
				}
				echo '<tr>';
				echo '<td><span class="nodesktop"><strong>' . __('Ticket ID', 'cqpim') . '</strong>: </span> ' . $ticket->ID . '</td>';
				echo '<td><span class="nodesktop"><strong>' . __('Title', 'cqpim') . '</strong>: </span> <a class="cqpim-link" href="' . get_the_permalink($ticket->ID) . '">' . $ticket->post_title . '</a></td>';
				echo '<td><span class="nodesktop"><strong>' . __('Owner', 'cqpim') . '</strong>: </span> ' . $owner->display_name . '</td>';
				echo '<td><span class="nodesktop"><strong>' . __('Priority', 'cqpim') . '</strong>: </span> ' . $priority . '</td>';
				echo '<td><span class="nodesktop"><strong>' . __('Created', 'cqpim') . '</strong>: </span> ' . get_the_date(get_option('cqpim_date_format') . ' H:i', $ticket->ID) . '</td>';
				echo '<td><span class="nodesktop"><strong>' . __('Updated', 'cqpim') . '</strong>: </span> ' . $ticket_updated . '</td>';
				echo '<td><span class="nodesktop"><strong>' . __('Status', 'cqpim') . '</strong>: </span> ' . $tstatus . '</td>';
				echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
			} else {
				echo '<table class="datatable_style dataTable-CST">';
				echo '<thead><tr><th>' . __('ID', 'cqpim') . '</th><th>' . __('Ticket Title', 'cqpim') . '</th><th>' . __('Ticket Owner', 'cqpim') . '</th><th>' . __('Priority', 'cqpim') . '</th><th>' . __('Created', 'cqpim') . '</th><th>' . __('Last Updated', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th></tr></thead>';
				echo '<tbody>';
				echo '<tr>';
				if(is_array($status)) {
					echo '<td>' . __('No open support tickets found', 'cqpim') . '</td><td></td><td></td><td></td><td></td><td></td><td></td>';
				} else if($status == 'resolved') {
					echo '<td>' . __('No resolved support tickets found', 'cqpim') . '</td><td></td><td></td><td></td><td></td><td></td><td></td>';
				}				
				echo '</tr>';
				echo '</tbody>';
				echo '</table>';
			}
			if($status == 'resolved') { ?>
				<br />
				<button id="switch_to_resolved" class="cqpim_button cqpim_small_button font-green-sharp border-green-sharp op"><?php _e('View Open Tickets', 'cqpim'); ?></button>
			<?php } else { ?>
				<br />
				<button id="switch_to_resolved" class="cqpim_button cqpim_small_button font-green-sharp border-green-sharp op"><?php _e('View Resolved Tickets', 'cqpim'); ?></button>
			<?php } ?>		
			<div class="clear"></div>
		</div>
	</div>
</div>	