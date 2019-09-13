<?php
function pto_client_logs_metabox_callback( $post ) {
 	wp_nonce_field( 
	'client_logs_metabox', 
	'client_logs_metabox_nonce' );
	$client_logs = get_post_meta($post->ID, 'client_logs', true); 
	if(empty($client_logs)) {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('The client has not accessed their dashboard yet', 'cqpim') . '</div>'; 
	} else { 
	krsort($client_logs); ?>
	<div>
		<table class="datatable_style dataTable" data-sort="[[ 0, \'desc\' ]]" data-rows="5">
			<thead>
				<tr>
					<th><?php _e('Date', 'cqpim'); ?></th>
					<th><?php _e('User', 'cqpim'); ?></th>
					<th><?php _e('Page', 'cqpim'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($client_logs as $key => $log) { 
					$user = get_user_by('id', $log['user']); ?>
					<tr>
						<td data-sort="<?php echo $key; ?>"><span class="cqpim_mobile"><?php _e('Date / Time:', 'cqpim'); ?></span> <?php echo date(get_option('cqpim_date_format') . ' H:i:s', $key); ?></td>
						<td><span class="cqpim_mobile"><?php _e('User:', 'cqpim'); ?></span> <?php echo isset($user->display_name) ? $user->display_name : ''; ?></td>
						<td><span class="cqpim_mobile"><?php _e('Page Visited:', 'cqpim'); ?></span> <?php echo $log['page']; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php }
}