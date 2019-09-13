<?php 
include('header.php');
$looper = get_post_meta($post->ID, 'looper', true);
$looper = $looper?$looper:0;
if(time() - $looper > 5) {
	$user = wp_get_current_user();
	$client_logs = get_post_meta($assigned, 'client_logs', true);
	if(empty($client_logs)) {
		$client_logs = array();
	}
	$now = current_time('timestamp');
	$title = $post->post_title;
	$client_logs[$now] = array(
		'user' => $user->ID,
		'page' => sprintf(__('FAQ - %1$s', 'cqpim'), $title)
	);
	update_post_meta($assigned, 'client_logs', $client_logs);
	update_post_meta($post->ID, 'looper', time());
}
$ticket_client = get_post_meta($post->ID, 'ticket_client', true);
?>
<div id="cqpim-dash-sidebar-back"></div>
<div class="cqpim-dash-content">
	<div id="cqpim-dash-sidebar">
		<?php include('sidebar.php'); ?>
	</div>
	<div id="cqpim-dash-content">
		<div id="cqpim_admin_title">
			<?php echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> <a href="' . get_the_permalink($client_dash) . '?page=faq">' . __('FAQ', 'cqpim') . '</a> <i class="fa fa-circle"></i> '; echo $post->post_title; ?>
		</div>
		<div id="cqpim-cdash-inside">
			<div class="masonry-grid">
				<div class="grid-sizer"></div>
				<div class="cqpim-dash-item-full grid-item">
					<div class="cqpim_block">
						<div class="cqpim_block_title">
							<div class="caption">
								<span class="caption-subject font-green-sharp sbold"><?php echo $post->post_title; ?> </span>
							</div>
						</div>
						<?php $terms = get_post_meta($post->ID, 'terms', true);
						echo wpautop($terms);
						?>
					</div>
				</div>	
			</div>
		</div>
<?php include('footer_inc.php'); ?>