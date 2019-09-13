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
	'page' => sprintf(__('Project %1$s - %2$s (Info Page)', 'cqpim'), get_the_ID(), $title)
);
update_post_meta($assigned, 'client_logs', $client_logs);
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-full grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php _e('Project Information', 'cqpim'); ?></span>
				</div>	
			</div>
			<?php
			$project_info = get_post_meta($post->ID, 'general_project_notes', true);
			if(!empty($project_info['general_project_notes'])) {
				echo wpautop($project_info['general_project_notes']);
			} else {
				_e('No general project information has been added.', 'cqpim');
			}			
			?>
		</div>
	</div>
</div>