<br />
<div class="cqpim_block">
<div class="cqpim_block_title">
	<div class="caption">
		<i class="fa fa-life-ring font-green-sharp" aria-hidden="true"></i>
		<span class="caption-subject font-green-sharp sbold"> <?php _e('Support Tickets', 'cqpim'); ?></span>
	</div>
	<div class="actions">
		<a class="cqpim_button cqpim_small_button border-green-sharp font-green-sharp rounded_2 sbold" href="<?php echo get_the_permalink($client_dash) . '?page=add-support-ticket'; ?>"><?php _e('Add Support Ticket', 'cqpim'); ?></a>
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
	<br />
	<?php 
	$user = wp_get_current_user();
	$client_logs = get_post_meta($assigned, 'client_logs', true);
	if(empty($client_logs)) {
		$client_logs = array();
	}
	$now = current_time('timestamp');
	$client_logs[$now] = array(
		'user' => $user->ID,
		'page' => __('Client Dashboard Support Tickets Page', 'cqpim')
	);
	update_post_meta($assigned, 'client_logs', $client_logs);
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
		echo '<table class="datatable_style files dataTable-CST">';
		echo '<thead><tr><th>' . __('ID', 'cqpim') . '</th><th>' . __('Ticket Title', 'cqpim') . '</th><th>' . __('Ticket Owner', 'cqpim') . '</th><th>' . __('Priority', 'cqpim') . '</th><th>' . __('Created', 'cqpim') . '</th><th>' . __('Last Updated', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th></tr></thead>';
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
		if(is_array($status)) {
			echo '<p>' . __('No open support tickets found...', 'cqpim') . '</p>';
		} else if($status == 'resolved') {
			echo '<p>' . __('No resolved support tickets found...', 'cqpim') . '</p>';
		}
	}
	if($status == 'resolved') { ?>
	<a id="switch_to_resolved" class="cqpim_button font-white bg-blue rounded_2 mt-20"><?php _e('View Open Tickets', 'cqpim'); ?> <div id="support_loader_2" class="ajax_loader" style="display:none"></div></a>
	<?php } else { ?>
	<a id="switch_to_resolved" class="cqpim_button font-white bg-blue rounded_2 mt-20"><?php _e('View Resolved Tickets', 'cqpim'); ?> <div id="support_loader_2" class="ajax_loader" style="display:none"></div></a>
	<?php } ?>
	<div class="clear"></div>
</div>