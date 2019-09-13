<?php
function pto_project_messages_metabox_callback( $post ) {
 	wp_nonce_field( 
	'project_messages_metabox', 
	'project_messages_metabox_nonce' );
	$project_messages = get_post_meta($post->ID, 'project_messages', true);
	if(!$project_messages) {
		echo '<p>' . _e('No messages to show...', 'cqpim') . '</p>';
	} else {
		$project_messages = array_reverse($project_messages); ?>
		<div style="max-height:500px; overflow:auto">
			<ul class="project_summary_progress" style="margin:0">		
				<?php foreach($project_messages as $key => $message) { 
					$user = get_user_by('id', $message['author']);
					$email = $user->user_email;
					$size = 80;		
					?>
					<li style="margin-bottom:0">
						<div class="timeline-entry">
							<?php 
							$avatar = get_option('cqpim_disable_avatars');
							if(empty($avatar)) {
								echo '<div class="update-who">';
								echo get_avatar( $user->ID, 60, '', false, array('force_display' => true) );
								echo '</div>';
							} ?>
							<?php if(empty($avatar)) { ?>
								<div class="update-data">
							<?php } else { ?>
								<div style="width:100%; float:none" class="update-data">
							<?php } ?>
								<div class="timeline-body-arrow"> </div>
								<div class="timeline-by font-blue-madison sbold">
									<?php echo $message['by']; ?>
									<?php if(current_user_can('cqpim_edit_project_dates')) { ?>
										<button class="delete_message cqpim_button cqpim_small_button font-red border-red right op cqpim_tooltip" data-id="<?php echo $key; ?>"><i class="fa fa-trash"></i></button>
									<?php } ?>
								</div>
								<div class="clear"></div>
								<div class="timeline-update font-grey-cascade"><?php echo wpautop($message['message']); ?></div>
								<div class="clear"></div>
								<div class="timeline-date font-grey-cascade"><?php echo date(get_option('cqpim_date_format') . ' H:i', $message['date']); ?></div>
							</div>
							<div class="clear"></div>
						</div>
					</li>
				<?php } ?>
			</ul>
		</div>	
	<?php } ?>
	<button id="add_message_trigger" class="mt-20 cqpim_button cqpim_small_button border-blue font-blue right"><?php _e('Send Message', 'cqpim'); ?></button>
	<div class="clear"></div>
	<div id="add_message_container" style="display:none">
		<div id="add_message">
			<div style="padding:12px">
				<h3><?php _e('Send Message', 'cqpim'); ?></h3>
				<p><strong><?php _e('Message Visibility', 'cqpim'); ?></strong></p>
				<input type="hidden" id="message_who" value="admin" />
				<select id="add_message_visibility" name="add_message_visibility">
					<option value="all"><?php _e('Visible to All', 'cqpim'); ?></option>
					<option value="internal"><?php _e('Internal Message (Client cannot see this)', 'cqpim'); ?></option>
				</select>
				<br />
				<p><strong><?php _e('Message Notifications', 'cqpim'); ?></strong></p>
				<input type="checkbox" id="send_to_team" name="send_to_team" /> <?php _e('Send a notification to the Project Team', 'cqpim'); ?>
				<br />
				<input type="checkbox" id="send_to_client" name="send_to_client" /> <?php _e('Send a notification to the client', 'cqpim'); ?>
				<br />
				<p><strong><?php _e('Message', 'cqpim'); ?></strong></p>
				<textarea style="width:100%; height:200px;min-width:400px" id="add_message_text" name="add_message_text"></textarea>
				<br />
				<div id="message_messages"></div>
				<button id="add_message_ajax" class="mt-20 cqpim_button cqpim_small_button border-green font-green right op"><?php _e('Send Message', 'cqpim'); ?></button>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<?php
}