<?php
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if(empty($client_logs)) {
	$client_logs = array();
}
$title = get_the_title();
$title = str_replace('Private:', '', $title);
$now = current_time('timestamp');
$client_logs[$now] = array(
	'user' => $user->ID,
	'page' => sprintf(__('Project %1$s - %2$s (Add Bug Page)', 'cqpim'), get_the_ID(), $title)
);
update_post_meta($assigned, 'client_logs', $client_logs);
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-full grid-item tasks-box">
		<?php if(function_exists('pto_add_project_bug')) {
			pto_add_project_bug($post->ID); 
		} ?>
	</div>
</div>