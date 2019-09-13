<?php
function pto_support_details_metabox_callback( $post ) {
 	wp_nonce_field( 
	'support_details_metabox', 
	'support_details_metabox_nonce' );
	$ticket_author = $post->post_author;
	$author_details = get_user_by('id', $ticket_author);
	$ticket_client = get_post_meta($post->ID, 'ticket_client', true);
	$ticket_owner = get_post_meta($post->ID, 'ticket_owner', true);
	$owner_details = get_post_meta($ticket_owner, 'team_details', true);
	$owner_name = isset($owner_details['team_name']) ? $owner_details['team_name'] : '';
	$client_details = get_post_meta($ticket_client, 'client_details', true);
	$client_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
	$ticket_status = get_post_meta($post->ID, 'ticket_status', true);
	$ticket_priority = get_post_meta($post->ID, 'ticket_priority', true);
	$ticket_updated = get_post_meta($post->ID, 'last_updated', true);
	$activate_ms = get_post_meta($post->ID, 'activate_ms', true);
	if(is_numeric($ticket_updated)) { $ticket_updated = date(get_option('cqpim_date_format') . ' H:i', $ticket_updated); } else { $ticket_updated = $ticket_updated; }
	if(empty($ticket_status)) {
		$status = '<div class="cqpim_button cqpim_small_button nolink op font-grey-cascade border-grey-cascade">' . __('Unpublished', 'cqpim') . '</div>';
	}
	if($ticket_status == 'open') {
		$status = '<div class="cqpim_button cqpim_small_button font-amber border-amber op nolink">' . __('Open', 'cqpim') . '</div>';
	} else if($ticket_status == 'resolved') {
		$status = '<div class="cqpim_button cqpim_small_button font-green border-green op nolink">' . __('Resolved', 'cqpim') . '</div>';
	} else if($ticket_status == 'hold') {
		$status = '<div class="cqpim_button cqpim_small_button font-red border-red op nolink">' . __('On Hold', 'cqpim') . '</div>';
	} else if($ticket_status == 'waiting') {
		$status = '<div class="cqpim_button cqpim_small_button font-purple border-purple op nolink">' . __('Awaiting Response', 'cqpim') . '</div>';
	}
	if(empty($ticket_priority)) {
		$priority = '<div class="cqpim_button cqpim_small_button nolink op font-grey-cascade border-grey-cascade">' . __('Unpublished', 'cqpim') . '</div>';
	}
	if($ticket_priority == 'low') {
		$priority = '<div class="cqpim_button cqpim_small_button font-blue-sharp border-blue-sharp op nolink">' . __('Low', 'cqpim') . '</div>';
	} else if($ticket_priority == 'normal') {
		$priority = '<div class="cqpim_button cqpim_small_button font-green border-green op nolink">' . __('Normal', 'cqpim') . '</div>';
	} else if($ticket_priority == 'high') {
		$priority = '<div class="cqpim_button cqpim_small_button font-red border-red op nolink">' . __('High', 'cqpim') . '</div>';
	} else if($ticket_priority == 'immediate') {
		$priority = '<div class="cqpim_button cqpim_small_button font-red border-red op nolink sbold">' . __('Immediate', 'cqpim') . '</div>';
	}
	?>
	<?php if(empty($ticket_client)) { ?>
		<div class="cqpim-alert cqpim-alert-danger alert-display">
			<p><?php _e('Select a Client', 'cqpim'); ?>:</p>
			<?php 
			$args = array(
				'post_type' => 'cqpim_client',
				'posts_per_page' => -1,
				'post_status' => 'private',
				'orderby' => 'title',
				'order' => 'asc',
			);
			$clients = get_posts($args);
			echo '<select id="ticket_client" name="ticket_client" required>';
			echo '<option value="">' . __('Select a Client:', 'cqpim') . '</option>';
			foreach($clients as $client) {
				$selected = '';
				$client_details = get_post_meta($client->ID, 'client_details', true);
				if($client->ID == $ticket_client) {
					$selected = 'selected';
				}
				echo '<option value="' . $client->ID . '" ' . $selected . '>' . $client_details['client_company'] . '</option>';
			}
			echo '</select>';
			echo '<br />';
			$client_id = isset($ticket_client) ? $ticket_client : '';
			$client_details = get_post_meta($client_id, 'client_details', true);
			$client_contacts = get_post_meta($client_id, 'client_contacts', true);
			$client_contact = get_post_meta($post->ID, 'client_contact', true);
			if(!empty($client_id)) { ?>
				<select style="border-top:0" name="client_contact" id="client_contact">
					<option value=""><?php _e('Select a Contact', 'cqpim'); ?></option>
					<option value="<?php echo $client_details['user_id']; ?>" <?php if($client_contact == $client_details['user_id']) { echo 'selected="selected"'; } ?>><?php echo $client_details['client_contact']; ?> <?php _e('(Main Contact)', 'cqpim'); ?></option>
					<?php 
					foreach($client_contacts as $contact) { ?>
						<option value="<?php echo $contact['user_id']; ?>" <?php if($client_contact == $contact['user_id']) { echo 'selected="selected"'; } ?>><?php echo $contact['name']; ?></option>							
					<?php }
					?>
				</select>					
			<?php } else { ?>
				<select style="width:100%" name="client_contact" id="client_contact" disabled >
					<option value=""><?php _e('Select a Contact', 'cqpim'); ?></option>
				</select>
			<?php } ?>
		</div>
	<?php } ?>
	<table class="cqpim_table">
		<tbody>
			<tr>
				<td><strong><?php _e('Ticket ID', 'cqpim'); ?></strong></td>
				<td><?php echo $post->ID; ?></td>
			</tr>
			<?php if(!empty($ticket_client)) { ?>
				<tr>
					<td><strong><?php _e('Client Name', 'cqpim'); ?></strong></td>
					<td><?php echo '<a href="' . get_edit_post_link($ticket_client) . '">' . $client_name . '</a>'; ?></td>					
				</tr>
				<tr>
					<td><strong><?php _e('Contact', 'cqpim'); ?></strong></td>
					<td><?php echo $author_details->display_name; ?></td>					
				</tr>
			<?php } ?>
			<tr>
				<td><strong><?php _e('Created', 'cqpim'); ?></strong></td>
				<td><?php echo get_the_date(get_option('cqpim_date_format') . ' H:i'); ?></td>
			</tr>
			<tr>
				<td><strong><?php _e('Last Updated', 'cqpim'); ?></strong></td>
				<td><?php echo $ticket_updated; ?></td>
			</tr>
			<tr>
				<td><strong><?php _e('Priority', 'cqpim'); ?></strong></td>
				<td><?php echo $priority; ?></td>
			</tr>
			<tr>
				<td><strong><?php _e('Status', 'cqpim'); ?></strong></td>
				<td><?php echo $status; ?></td>
			</tr>
			<tr>
				<td><strong><?php _e('Assigned To', 'cqpim'); ?></strong></td>
				<td><?php echo $owner_name; ?></td>
			</tr>
			<tr>
				<td colspan="2"><input type="checkbox" name="activate_ms" value="1" <?php if(!empty($activate_ms)) { ?>checked="checked"<?php } ?> /><?php _e('Activate Milestones, Tasks and Invoicing', 'cqpim'); ?></td>
			</tr>
		</tbody>
	</table>
	<?php if(current_user_can('delete_cqpim_supports')) { ?>
	<button class="s_buttond cqpim_button font-white bg-red block mt-10 rounded_2 block" data-id="<?php echo $post->ID; ?>" id="delete_task"><?php _e('DELETE TICKET', 'cqpim'); ?></button>
	<?php } ?>
	<button class="s_button cqpim_button font-white bg-blue block mt-10 rounded_2 block" data-id="<?php echo $post->ID; ?>"><?php _e('Update Ticket', 'cqpim'); ?></button><div id="ajax_spinner_task" class="ajax_spinner_2" style="display:none"></div>
	<?php
}