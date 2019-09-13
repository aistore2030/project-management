<?php
add_action( 'admin_menu' , 'register_pto_support_page', 28 ); 
function register_pto_support_page() {
	$mypage = add_submenu_page(	
				'pto-dashboard',
				__('Support Tickets', 'cqpim'), 			
				__('Support Tickets', 'cqpim'),  			
				'cqpim_view_tickets', 			
				'pto-tickets', 		
				'pto_support'
	);
	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
}
function pto_support() { ?>
	<div class="tasks-box" style="padding-right:20px">
		<br />
		<div class="cqpim_block cqpim_roles">
			<div class="cqpim_block_title">
				<div class="caption">
					<i class="fa fa-life-ring font-green-sharp" aria-hidden="true"></i>
					<span class="caption-subject font-green-sharp sbold"><?php _e('Support Tickets', 'cqpim'); ?> </span>
				</div>
				<div class="actions">
					<a class="cqpim_button cqpim_small_button border-green-sharp font-green-sharp rounded_2 sbold" href="<?php echo admin_url(); ?>post-new.php?post_type=cqpim_support"><?php _e('Add Support Ticket', 'cqpim') ?></a>
				</div>
			</div>
			<?php 
			$user = wp_get_current_user();
			$status = isset($_SESSION['ticket_status']) ? $_SESSION['ticket_status'] : array('open','hold','waiting');
			$args = array(
				'post_type' => 'cqpim_support',
				'posts_per_page' => -1,
				'post_status' => 'private',
				'meta_key' => 'ticket_status',
				'meta_value' => $status
			); 
			$tickets = get_posts($args);
			$total_tickets = count($tickets);
			$ordered = array();
			foreach($tickets as $ticket) {
				$ticket_updated = get_post_meta($ticket->ID, 'last_updated', true);
				if(!empty($ticket_updated)) {
					$ordered[$ticket_updated] = $ticket;
				}
			}
			krsort($ordered);
			if(!empty($tickets)) {
				$ordering = "";
				echo '<table class="datatable_style dataTable-ST" data-ordering="true" data-rows="10">';
				echo '<thead><tr><th>' . __('ID', 'cqpim') . '</th><th>' . __('Ticket Title', 'cqpim') . '</th><th>' . __('Assigned To', 'cqpim') . '</th><th>' . __('Client Name', 'cqpim') . '</th><th>' . __('Priority', 'cqpim') . '</th><th>' . __('Created', 'cqpim') . '</th><th>' . __('Last Updated', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th></tr></thead>';
				echo '<tbody>';
				foreach($ordered as $ticket) {
					$ticket_author = $ticket->post_author;
					$author_details = get_user_by('id', $ticket_author);
					$ticket_owner = get_post_meta($ticket->ID, 'ticket_owner', true);
					$owner_details = get_post_meta($ticket_owner, 'team_details', true);
					$owner_name = isset($owner_details['team_name']) ? $owner_details['team_name'] : '';
					$ticket_client = get_post_meta($ticket->ID, 'ticket_client', true);
					$client_details = get_post_meta($ticket_client, 'client_details', true);
					$client_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
					$ticket_status = get_post_meta($ticket->ID, 'ticket_status', true);
					if($ticket_status == 'open') {
						$tstatus = '<div class="cqpim_button cqpim_small_button font-amber border-amber op nolink">' . __('Open', 'cqpim') . '</div>';
					} else if($ticket_status == 'resolved') {
						$tstatus = '<div class="cqpim_button cqpim_small_button font-green border-green op nolink">' . __('Resolved', 'cqpim') . '</div>';
					} else if($ticket_status == 'hold') {
						$tstatus = '<div class="cqpim_button cqpim_small_button font-red border-red op nolink">' . __('On Hold', 'cqpim') . '</div>';
					} else if($ticket_status == 'waiting') {
						$tstatus = '<div class="cqpim_button cqpim_small_button font-purple border-purple op nolink">' . __('Awaiting Response', 'cqpim') . '</div>';
					}
					$ticket_priority = get_post_meta($ticket->ID, 'ticket_priority', true);
					$ticket_updated = get_post_meta($ticket->ID, 'last_updated', true);
					$ticket_updated_stamp = get_post_meta($ticket->ID, 'last_updated', true);
					if(is_numeric($ticket_updated)) { $ticket_updated = date(get_option('cqpim_date_format') . ' H:i', $ticket_updated); } else { $ticket_updated = $ticket_updated; }
					if($ticket_priority == 'low') {
						$priority = '<div class="cqpim_button cqpim_small_button font-blue-sharp border-blue-sharp op nolink">' . __('Low', 'cqpim') . '</div>';
					} else if($ticket_priority == 'normal') {
						$priority = '<div class="cqpim_button cqpim_small_button font-green border-green op nolink">' . __('Normal', 'cqpim') . '</div>';
					} else if($ticket_priority == 'high') {
						$priority = '<div class="cqpim_button cqpim_small_button font-red border-red op nolink">' . __('High', 'cqpim') . '</div>';
					} else if($ticket_priority == 'immediate') {
						$priority = '<div class="cqpim_button cqpim_small_button font-red border-red op nolink sbold">' . __('Immediate', 'cqpim') . '</div>';
					}
					echo '<tr>';
					echo '<td><span class="cqpim_mobile">' . __('Ticket ID:', 'cqpim') . '</span> ' . $ticket->ID . '</td>';
					echo '<td><span class="cqpim_mobile">' . __('Title:', 'cqpim') . '</span> <a href="' . get_edit_post_link($ticket->ID) . '">' . $ticket->post_title . '</a></td>';
					echo '<td><span class="cqpim_mobile">' . __('Assignee:', 'cqpim') . '</span> ' . $owner_name . '</td>';
					echo '<td><span class="cqpim_mobile">' . __('Client:', 'cqpim') . '</span> <a href="' . get_edit_post_link($ticket_client) . '">' . $client_name . '</a></td>';
					echo '<td><span class="cqpim_mobile">' . __('Priority:', 'cqpim') . '</span> ' . $priority . '</td>';
					echo '<td data-order="' . get_the_date(get_option('cqpim_date_format') . ' H:i', $ticket->ID) . '"><span class="cqpim_mobile">' . __('Raised:', 'cqpim') . '</span> ' . get_the_date(get_option('cqpim_date_format') . ' H:i', $ticket->ID) . '</td>';
					echo '<td data-order="' . $ticket_updated_stamp . '"><span class="cqpim_mobile">' . __('Updated:', 'cqpim') . '</span> ' . $ticket_updated . '</td>';
					echo '<td><span class="cqpim_mobile">' . __('Status:', 'cqpim') . '</span> ' . $tstatus . '</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
			} else {
				if(is_array($status)) {
					echo '<table style="background:#fff" class="datatable_style files dataTable">';
					echo '<thead><tr><th>' . __('ID', 'cqpim') . '</th><th>' . __('Ticket Title', 'cqpim') . '</th><th>' . __('Client Name', 'cqpim') . '</th><th>' . __('Priority', 'cqpim') . '</th><th>' . __('Created', 'cqpim') . '</th><th>' . __('Last Updated', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th></tr></thead>';
					echo '<tbody>';
					echo '</tbody>';
					echo '</table>';
				} else if($status == 'resolved') {
					echo '<table style="background:#fff" class="datatable_style files dataTable">';
					echo '<thead><tr><th>' . __('ID', 'cqpim') . '</th><th>' . __('Ticket Title', 'cqpim') . '</th><th>' . __('Client Name', 'cqpim') . '</th><th>' . __('Priority', 'cqpim') . '</th><th>' . __('Created', 'cqpim') . '</th><th>' . __('Last Updated', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th></tr></thead>';
					echo '<tbody>';
					echo '</tbody>';
					echo '</table>';
				}
			}
			if($status == 'resolved') { ?>
			<button id="switch_to_resolved" class="cqpim_button mt-20 font-blue-sharp border-blue-sharp op"><?php _e('View Open Tickets', 'cqpim'); ?></button>
			<?php } else { ?>
			<button id="switch_to_resolved" class="cqpim_button mt-20 font-blue-sharp border-blue-sharp op"><?php _e('View Resolved Tickets', 'cqpim'); ?></button>
			<?php } ?>
			<div class="clear"></div>	
		</div>
	</div>
<?php } 