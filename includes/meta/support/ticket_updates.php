<?php
function pto_support_history_metabox_callback( $post ) {
 	wp_nonce_field( 
	'support_history_metabox', 
	'support_history_metabox_nonce' );
	$ticket_client = get_post_meta($post->ID, 'ticket_client', true);
	$ticket_updates = get_post_meta($post->ID, 'ticket_updates', true);
	$ticket_status = get_post_meta($post->ID, 'ticket_status', true);
	$ticket_priority = get_post_meta($post->ID, 'ticket_priority', true);
	echo '<div class="project_messages" style="max-height:600px; overflow:auto">';
	if(empty($ticket_updates)) {
		$ticket_updates = array();
		echo '<p>' . __('No updates have been posted to this ticket', 'cqpim') . '</p>';
	}
	$ticket_updates = array_reverse($ticket_updates);
	echo '<ul class="project_summary_progress" style="margin:0">';
	foreach($ticket_updates as $key => $update) {
		if($update['type'] == 'client') {
			$user = get_post_meta($update['user'], 'client_details', true);
			$email = isset($user['client_email']) ? $user['client_email'] : '';
			$name = isset($user['client_contact']) ? $user['client_contact'] : '';
		} else {
			$user = get_post_meta($update['user'], 'team_details', true);
			$email = isset($user['team_email']) ? $user['team_email'] : '';
			$name = isset($user['team_name']) ? $user['team_name'] : '';
		}
		$changes = isset($update['changes']) ? $update['changes'] : '';
		$size = 80;
		if(isset($update['email'])) {
			$email = $update['email'];
		}		
		?>
		<li style="margin-bottom:0">
			<div class="timeline-entry">
				<?php 
				$avatar = get_option('cqpim_disable_avatars');
				if(empty($avatar)) {
					echo '<div class="update-who">';
					echo get_avatar( $user['user_id'], 60, '', false, array('force_display' => true) );
					echo '</div>';
				} ?>
				<?php if(empty($avatar)) { ?>
					<div class="update-data">
				<?php } else { ?>
					<div style="width:100%; float:none" class="update-data">
				<?php } ?>
					<div class="timeline-body-arrow"> </div>
					<div class="timeline-by font-blue-madison sbold">
						<?php if(isset($update['name'])) {
							echo $update['name'];
						} else {
							echo $name; 
						} ?>
						<button class="delete_message cqpim_button cqpim_small_button font-red border-red right op cqpim_tooltip" data-id="<?php echo $key; ?>"><i class="fa fa-trash"></i></button>
					</div>
					<div class="clear"></div>
					<div class="timeline-update font-grey-cascade"><?php echo wpautop($update['details']); ?></div>
					<div class="clear"></div>
					<div class="timeline-date font-grey-cascade">
						<?php 
						if(is_numeric($update['time'])) { $time = date(get_option('cqpim_date_format') . ' H:i', $update['time']); } else { $time = $update['time']; }
						echo $time; 
						if($changes) {
							foreach($changes as $change) {
								echo '<br />' . $change;
							}
						}
						if(!empty($update['seen'])) {
							$user = get_user_by('id', $update['seen']['user']);
							echo '<br />'; printf(__('Seen by %1$s on %2$s', 'cqpim'), $user->display_name, date(get_option('cqpim_date_format') . ' H:i:s', $update['seen']['time']));
						} ?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</li>
		<?php			
	}
	echo '</ul>';
	echo '</div>';	
}