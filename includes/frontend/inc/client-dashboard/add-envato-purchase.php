<br />
<div class="cqpim_block">
<div class="cqpim_block_title">
	<div class="caption">
		<span class="caption-subject font-green-sharp sbold"> <?php _e('My Envato Items', 'cqpim'); ?></span>
	</div>
</div>
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
	echo '<table class="cqpim_table dash">';
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
					echo '<td>' . __('EXPIRED', 'cqpim-envato') . '</td>';
				}
				echo '<td>' . $purchase['purchase_code'] . '</td>';
				echo '</tr>';
			}
		echo '</tbody>';
	echo '</table>';	
} ?>
<a href="#" id="add-envato-code-trigger" class="cqpim_button font-white bg-blue rounded_2 mt-20"><?php _e('Add Purchase Code', 'cqpim'); ?></a>
</div>
<div id="add-envato-code-container" style="display:none">
	<div id="add-envato-code-div">
		<div style="padding:12px">
			<h3><?php _e('Add Envato Purchase Code', 'cqpim'); ?></h3>
			<p><?php _e('Enter the purchase code of the item that you would like to add to your account.', 'cqpim'); ?></p>
			<input style="width:92%" type="text" id="purchase_code" />
			<br />
			<button class="cancel-colorbox mt-20 cqpim_button font-red border-red op"><?php _e('Cancel', 'cqpim'); ?></button>
			<button id="add-envato-code" class="cqpim_button mt-20 font-green border-green right op"><?php _e('Add Purchase Code', 'cqpim'); ?></button>
			<div class="clear"></div>
			<div id="login_messages"></div>
		</div>
	</div>
</div>