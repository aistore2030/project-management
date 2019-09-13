<?php
function pto_task_messages_metabox_callback($post) {
	wp_nonce_field( 
	'task_messages_metabox', 
	'task_messages_metabox_nonce' );
	$messages = get_post_meta($post->ID, 'task_messages', true);
	if(empty($messages)) {
		echo '<p>' . __('No messages to show', 'cqpim') . '</p>';
		echo '<h4>' . __('Add Message', 'cqpim') . '</h4>';
		echo '<textarea name="add_task_message"></textarea>';
		echo '<button class="s_button mt-20 cqpim_button cqpim_small_button border-blue font-blue right">' . __('Add Message', 'cqpim') . '</button>';
		echo '<div class="clear"></div>';
	} else {
		$project_messages = array_reverse($messages); ?>
		<div style="max-height:500px; overflow:auto">
			<ul class="project_summary_progress" style="margin:0">		
				<?php foreach($project_messages as $key => $message) { 
					$changes = isset($message['changes']) ? $message['changes'] : array();
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
									<button class="delete_message cqpim_button cqpim_small_button font-red border-red right op cqpim_tooltip" data-id="<?php echo $key; ?>"><i class="fa fa-trash"></i></button>
								</div>
								<div class="clear"></div>
								<div class="timeline-update font-grey-cascade"><?php echo wpautop($message['message']); ?></div>
								<div class="clear"></div>
								<div class="timeline-date font-grey-cascade"><?php echo date(get_option('cqpim_date_format') . ' H:i', $message['date']); ?>
								<?php if(!empty($changes)) {
									foreach($changes as $change) {
										echo ' | ' . $change;
									}
								} ?>								
								</div>
							</div>
							<div class="clear"></div>
						</div>
					</li>
				<?php } ?>
			</ul>
		</div>
		<?php 
		echo '<h4>' . __('Add Message', 'cqpim') . '</h4>';
		echo '<textarea name="add_task_message"></textarea>';
		echo '<button class="s_button mt-20 cqpim_button cqpim_small_button border-blue font-blue right">' . __('Add Message', 'cqpim') . '</button>';
		echo '<div class="clear"></div>';		
	}
}
add_action( 'save_post', 'save_pto_task_messages_metabox_data' );
function save_pto_task_messages_metabox_data( $post_id ){
	if ( ! isset( $_POST['task_messages_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['task_messages_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'task_messages_metabox' ) )
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
}