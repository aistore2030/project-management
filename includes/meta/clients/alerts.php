<?php
function pto_client_alerts_metabox_callback( $post ) {
 	wp_nonce_field( 
	'client_alerts_metabox', 
	'client_alerts_metabox_nonce' );
	_e('If you would like to display custom alerts on the dashboard for this client, you can add them here. You will be able to see once the client has seen the alert and marked it as seen.', 'cqpim');
	$custom_alerts = get_post_meta($post->ID, 'custom_alerts', true);
	$alert_names = pto_get_alert_names();
	if(empty($custom_alerts)) {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('You have not added any custom alerts for this client.', 'cqpim') . '</div>';
	} else {
		echo '<table class="datatable_style dataTable">';
		echo '<thead><tr><th>' . __('ID', 'cqpim') . '</th><th>' . __('Message', 'cqpim') . '</th><th>' . __('Seen', 'cqpim') . '</th><th>' . __('Cleared', 'cqpim') . '</th><th>' . __('Global', 'cqpim') . '</th><th>' . __('Actions', 'cqpim') . '</th></tr></thead>';
		echo '<tbody>';
		foreach($custom_alerts as $key => $alert) { 
			$seen = isset($alert['seen']) ? $alert['seen'] : '';
			$cleared = isset($alert['cleared']) ? $alert['cleared'] : '';
			?>
			<tr>
				<td><?php if(empty($alert['global'])) { echo $post->ID . '-'; } ?><?php echo $key; ?></td>
				<td><div class="cqpim-alert cqpim-alert-<?php echo $alert['level']; ?>"><?php echo $alert['message']; ?></div></td>
				<td><?php if(is_numeric($seen)) { echo date(get_option('cqpim_date_format') . ' H:i', $seen); } else { echo $seen; } ?></td>
				<td><?php if(is_numeric($cleared)) { echo date(get_option('cqpim_date_format') . ' H:i', $cleared); } else { echo $cleared; } ?></td>
				<td><?php if(!empty($alert['global'])) { ?><i style="font-size:18px" class="fa fa-check" aria-hidden="true"></i><?php } else { ?><i style="font-size:18px" class="fa fa-times" aria-hidden="true"></i><?php } ?></td>
				<td><button class="edit_alert cqpim_button cqpim_small_button font-amber border-amber" value="<?php echo $key; ?>" data-global="<?php echo $alert['global']; ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button> <button class="delete_alert cqpim_button cqpim_small_button font-red border-red" value="<?php echo $key; ?>" data-global="<?php echo $alert['global']; ?>"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
			</tr>
			<div id="edit_client_alert_<?php echo $key; ?>_ajax_container" style="display:none">
				<div id="edit_client_alert_<?php echo $key; ?>_ajax">
					<div style="padding:12px">
						<h3><?php _e('Edit Custom Alert', 'cqpim'); ?></h3>
						<label for="alert_level"><?php _e('Alert Level', 'cqpim'); ?></label><br />
						<select id="alert_level_<?php echo $key; ?>" name="alert_level">
							<option value="0"><?php _e('Choose... ', 'cqpim'); ?></option>
							<option value="info" <?php if($alert['level'] == 'info') { echo 'selected="selected"'; } ?>><?php _e('Notice (Blue)', 'cqpim'); ?></option>
							<option value="success" <?php if($alert['level'] == 'success') { echo 'selected="selected"'; } ?>><?php _e('Success (Green)', 'cqpim'); ?></option>
							<option value="warning" <?php if($alert['level'] == 'warning') { echo 'selected="selected"'; } ?>><?php _e('Warning (Amber)', 'cqpim'); ?></option>
							<option value="danger" <?php if($alert['level'] == 'danger') { echo 'selected="selected"'; } ?>><?php _e('Error (Red)', 'cqpim'); ?></option>
						</select>
						<br /><br />
						<label for="alert_message_<?php echo $key; ?>"><?php _e('Message', 'cqpim'); ?></label><br />
						<input style="min-width:350px" type="text" id="alert_message_<?php echo $key; ?>" name="alert_message" value="<?php echo isset($alert['message']) ? $alert['message'] : ''; ?>" />
						<br /><br />
						<div id="client_alert_messages_<?php echo $key; ?>"></div>
						<button class="cancel-colorbox cqpim_button cqpim_small_button border-red font-red op mt-10"><?php _e('Cancel', 'cqpim'); ?></button>			
						<button id="edit_client_alert_<?php echo $key; ?>_submit" data-key="<?php echo $key; ?>" class="edit_alert_submit cqpim_button cqpim_small_button border-green font-green contact_edit_submit op mt-10 right"><?php _e('Edit Custom Alert', 'cqpim'); ?><span id="ajax_spinner_add_contact" class="ajax_loader" style="display:none"></span></button>
					</div>
				</div>
			</div>
		<?php }
		echo '</tbody>';
		echo '</table>';
	} ?>
	<button id="add_client_alert" class="mt-20 cqpim_button cqpim_small_button border-blue font-blue right" value="<?php echo $post->ID; ?>"><?php _e('Add Custom Alert', 'cqpim'); ?></button>
	<div class="clear"></div>
	<div id="add_client_alert_ajax_container" style="display:none">
		<div id="add_client_alert_ajax">
			<div style="padding:12px">
				<h3><?php _e('Add Custom Alert', 'cqpim'); ?></h3>
				<label for="alert_level"><?php _e('Alert Level', 'cqpim'); ?></label><br />
				<select id="alert_level" name="alert_level">
					<option value="0"><?php _e('Choose... ', 'cqpim'); ?></option>
					<option value="info"><?php _e('Notice (Blue)', 'cqpim'); ?></option>
					<option value="success"><?php _e('Success (Green)', 'cqpim'); ?></option>
					<option value="warning"><?php _e('Warning (Amber)', 'cqpim'); ?></option>
					<option value="danger"><?php _e('Error (Red)', 'cqpim'); ?></option>
				</select>
				<br /><br />
				<label for="alert_message"><?php _e('Message', 'cqpim'); ?></label><br />
				<input style="min-width:350px" type="text" id="alert_message" name="alert_message" />
				<br /><br />
				<input type="checkbox" id="alert_global" name="alert_global" /> <?php _e('Make this a global alert (Add to ALL Client\'s Dashboards)', 'cqpim'); ?>
				<br /><br />
				<div id="client_alert_messages"></div>
				<button class="cancel-colorbox cqpim_button cqpim_small_button border-red font-red op mt-10"><?php _e('Cancel', 'cqpim'); ?></button>			
				<button id="add_client_alert_submit" class="cqpim_button cqpim_small_button border-green font-green contact_edit_submit op mt-10 right"><?php _e('Add Custom Alert', 'cqpim'); ?><span id="ajax_spinner_add_contact" class="ajax_loader" style="display:none"></span></button>
			</div>
		</div>
	</div>
<?php }