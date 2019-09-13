<?php 
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if(empty($client_logs)) {
	$client_logs = array();
}
$now = current_time('timestamp');
$client_logs[$now] = array(
	'user' => $user->ID,
	'page' => __('Client Dashboard Quotes Page', 'cqpim')
);
update_post_meta($assigned, 'client_logs', $client_logs);
?>
<br />
<div class="cqpim_block">
	<?php echo pto_return_subs_plans_page(); ?>
</div>