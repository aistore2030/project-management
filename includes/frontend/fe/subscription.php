<?php
$user = wp_get_current_user();
$client_id = get_post_meta($post->ID, 'subscription_client', true);
$looper = get_post_meta($post->ID, 'looper', true);
$looper = $looper?$looper:0;
$title = get_the_title(); 
if(time() - $looper > 5 && in_array('cqpim_client', $user->roles)) {
	$client_logs = get_post_meta($client_id, 'client_logs', true);
	if(empty($client_logs)) {
		$client_logs = array();
	}
	$now = current_time('timestamp');
	$client_logs[$now] = array(
		'user' => $user->ID,
		'page' => sprintf(__('Subscription - %1$s', 'cqpim'), $title)
	);
	update_post_meta($client_id, 'client_logs', $client_logs);
	update_post_meta($post->ID, 'looper', time());
}
include('header.php');
?>
<div id="cqpim-cdash-inside">
	<?php
	if($assigned == $client_id) { 
		pto_return_subscription_fe($post->ID);
	} else { ?>
		<br />
		<div class="cqpim-dash-item-full grid-item">
			<div class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<span class="caption-subject font-green-sharp sbold"><?php _e('Access Denied', 'cqpim'); ?></span>
					</div>
				</div>
				<p><?php _e('Cheatin\' uh? We can\'t let you see this item because it\'s not yours', 'cqpim'); ?></p>
			</div>
		</div>
	<?php } ?>	
</div>
<?php include('footer.php'); ?>