<?php
function pto_client_tickets_metabox_callback( $post ) {
 	wp_nonce_field( 
	'client_tickets_metabox', 
	'client_tickets_metabox_nonce' );
	$client_details = get_post_meta($post->ID, 'client_details', true);
	$ticket_assignee = isset($client_details['ticket_assignee']) ? $client_details['ticket_assignee']: '';
	?>
	<label for="ticket_assignee"><?php _e('Default Ticket Assignee:', 'cqpim'); ?> </label>
	<select id="ticket_assignee" name="ticket_assignee">
		<option value=""><?php _e('No Default (Tickets will be unassigned until claimed)', 'cqpim'); ?> </option>
		<?php
		$args = array(
			'post_type' => 'cqpim_teams',
			'posts_per_page' => -1,
			'post_status' => 'any'
		);
		$team_members = get_posts($args);
		foreach($team_members as $member) {
			$team_details = get_post_meta($member->ID, 'team_details', true);
			$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
			if(!empty($user_id)) {
				$user = get_user_by('id', $user_id);
				$caps = $user->allcaps;
				foreach($caps as $key => $cap) {
					if($key == 'edit_cqpim_supports' && $cap == 1) {
						if($member->ID == $ticket_assignee) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						echo '<option value="' . $member->ID . '" ' . $selected . '>' . $team_details['team_name'] . '</option>';
					}
				}
			}
		}
		?>
	</select>	
	<?php
	$args = array(
		'post_type' => 'cqpim_support',
		'posts_per_page' => -1,
		'post_status' => 'private',
		'meta_key' => 'ticket_client',
		'meta_value' => $post->ID
	);
	$tickets = get_posts($args);
	if($tickets) {
		echo '<table class="datatable_style dataTable" data-sort="[[ 0, \'desc\' ]]" data-rows="5">';
		echo '<thead>';
		echo '<tr><th>' . __('Ticket ID', 'cqpim') . '</th><th>' . __('Title', 'cqpim') . '</th><th>' . __('Assigned To', 'cqpim') . '</th><th>' . __('Priority', 'cqpim') . '</th><th>' . __('Created', 'cqpim') . '</th><th>' . __('Updated', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th></tr>';
		echo '</thead>';
		foreach($tickets as $ticket) {
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
					$tstatus = '<span class="cqpim_button cqpim_xs_button border-blue font-blue nolink op">' . __('Open', 'cqpim') . '</span>';
				} else if($ticket_status == 'resolved') {
					$tstatus = '<span class="cqpim_button cqpim_xs_button border-green font-green nolink op">' . __('Resolved', 'cqpim') . '</span>';
				} else if($ticket_status == 'hold') {
					$tstatus = '<span class="cqpim_button cqpim_xs_button border-amber font-amber nolink op">' . __('On Hold', 'cqpim') . '</span>';
				}
				$ticket_priority = get_post_meta($ticket->ID, 'ticket_priority', true);
				$ticket_updated = get_post_meta($ticket->ID, 'last_updated', true);
				if(is_numeric($ticket_updated)) { $ticket_updated = date(get_option('cqpim_date_format') . ' H:i', $ticket_updated); } else { $ticket_updated = $ticket_updated; }
				if($ticket_priority == 'low') {
					$priority = '<span class="cqpim_button cqpim_xs_button border-blue font-blue nolink op">' . __('Low', 'cqpim') . '</span>';
				} else if($ticket_priority == 'normal') {
					$priority = '<span class="cqpim_button cqpim_xs_button border-amber font-amber nolink op">' . __('Normal', 'cqpim') . '</span>';
				} else if($ticket_priority == 'high') {
					$priority = '<span class="cqpim_button cqpim_xs_button border-red font-red nolink op">' . __('High', 'cqpim') . '</span>';
				} else if($ticket_priority == 'immediate') {
					$priority = '<span class="cqpim_button cqpim_xs_button border-red font-red nolink op upper sbold">' . __('Immediate', 'cqpim') . '</span>';
				}
				echo '<tr>';
				echo '<td><span class="cqpim_mobile">' . __('Ticket ID:', 'cqpim') . '</span> ' . $ticket->ID . '</td>';
				echo '<td><span class="cqpim_mobile">' . __('Title:', 'cqpim') . '</span> <a href="' . get_edit_post_link($ticket->ID) . '">' . $ticket->post_title . '</a></td>';
				echo '<td><span class="cqpim_mobile">' . __('Assignee:', 'cqpim') . '</span> ' . $owner_name . '</td>';
				echo '<td><span class="cqpim_mobile">' . __('Priority:', 'cqpim') . '</span> ' . $priority . '</td>';
				echo '<td><span class="cqpim_mobile">' . __('Raised:', 'cqpim') . '</span> ' . get_the_date(get_option('cqpim_date_format') . ' H:i', $ticket->ID) . '</td>';
				echo '<td><span class="cqpim_mobile">' . __('Updated:', 'cqpim') . '</span> ' . $ticket_updated . '</td>';
				echo '<td><span class="cqpim_mobile">' . __('Status:', 'cqpim') . '</span> ' . $tstatus . '</td>';
				echo '</tr>';				
		}
		echo '</table>';
	} else {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('This client does not have any support tickets', 'cqpim') . '</div>';
	}		
}
add_action( 'save_post', 'save_pto_client_tickets_metabox_data' );
function save_pto_client_tickets_metabox_data( $post_id ){
	if ( ! isset( $_POST['client_tickets_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['client_tickets_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'client_tickets_metabox' ) )
	    return $post_id;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	    return $post_id;
	if ( 'page' == $_POST['post_type'] ) {
	    if ( ! current_user_can( 'edit_page', $post_id ) )
	        return $post_id;
	  	} else {
	    if ( ! current_user_can( 'edit_post', $post_id ) )
	        return $post_id;
	}
	if(isset($_POST['ticket_assignee'])) {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$ticket_assignee = $_POST['ticket_assignee'];
		$client_details['ticket_assignee'] = $ticket_assignee;
		update_post_meta($post_id, 'client_details', $client_details);
	}
}