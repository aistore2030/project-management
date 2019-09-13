<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-sitemap font-green-sharp" aria-hidden="true"></i> <?php _e('My Envato Purchases', 'cqpim'); ?>
		</div>
	</div>
	<div class="cqpim-dash-item-inside">
		<?php
		$user = wp_get_current_user();
		$client_logs = get_post_meta($assigned, 'client_logs', true);
		if(empty($client_logs)) {
			$client_logs = array();
		}
		$now = time();
		$client_logs[$now] = array(
			'user' => $user->ID,
			'page' => __('Client Dashboard Add Envato Item Page', 'cqpim')
		);
		update_post_meta($assigned, 'client_logs', $client_logs);
		$user = wp_get_current_user();
		$args = array(
			'post_type' => 'cqpim_client',
			'posts_per_page' => -1,
			'post_status' => 'private',
		);
		$clients = get_posts($args);
		foreach($clients as $client) {
			$client_details = get_post_meta($client->ID, 'client_details', true);
			$client_user_id = $client_details['user_id'];
			if($user->ID === $client_user_id) {
				$client_object_id = $client->ID;
			}
		}
		if(empty($client_object_id)) {
			foreach($clients as $client) {
				$client_ids = get_post_meta($client->ID, 'client_ids', true);
				if(in_array($user->ID, $client_ids)) {
					$client_object_id = $client->ID;
					$client_type = 'contact';
				}
			} 			
		}
		$envato_details = get_post_meta($client_object_id, 'envato_details', true);	
		?>
		<?php if(empty($envato_details)) { ?>
			<p><?php _e('You do not have a valid licence for any of our items, or you haven\'t registered them', 'cqpim'); ?></p>
		<?php } else {
			echo '<table class="milestones dash">';
				echo '<thead>';
					echo '<tr><th style="border-left:1px solid #ececec">' . __('Item ID', 'cqpim') . '</th><th>' . __('Item Name', 'cqpim') . '</th><th>' . __('Purchase Date', 'cqpim') . '</th><th>' . __('Licence', 'cqpim') . '</th><th>' . __('Support Expiry', 'cqpim') . '</th><th>' . __('Purchase Code', 'cqpim') . '</th></tr>';
				echo '</thead>';
				echo '<tbody>';
					foreach($envato_details as $purchase) {
						echo '<tr>';
						echo '<td style="border-left:1px solid #ececec">' . $purchase['item_id'] . '</td>';
						echo '<td>' . $purchase['item_name'] . '</td>';
						echo '<td>' . $purchase['created_at'] . '</td>';
						echo '<td>' . $purchase['licence'] . '</td>';
						if(!empty($purchase['supported_until'])) {
							echo '<td>' . $purchase['supported_until'] . '</td>';
						} else {
							echo '<td>' . __('EXPIRED', 'cqpim') . '</td>';
						}
						echo '<td>' . $purchase['purchase_code'] . '</td>';
						echo '</tr>';
					}
				echo '</tbody>';
			echo '</table>';	
		} ?>
		<br /><br />
		<a href="#" id="add-envato-code-trigger" class="cqpim_button bg-blue font-white rounded_2"><?php _e('Add Purchase Code', 'cqpim'); ?></a>
		<div id="add-envato-code-container" style="display:none">
			<div id="add-envato-code-div">
				<div style="padding:12px">
					<h3><?php _e('Add Envato Purchase Code', 'cqpim'); ?></h3>
					<p><?php _e('Enter the purchase code of the item that you would like to add to your account.', 'cqpim'); ?></p>
					<input style="width:92%; padding:2.5%" type="text" id="purchase_code" />
					<br /><br />
					<button class="cancel-colorbox"><?php _e('Cancel', 'cqpim'); ?></button>
					<button id="add-envato-code" class="cqpim_button bg-blue font-white rounded_2"><?php _e('Add Purchase Code', 'cqpim'); ?></button>
					<div class="clear"></div>
					<div style="margin-top:20px; display:none" id="login_messages"></div>
				</div>
			</div>
		</div>
	</div>
</div>	