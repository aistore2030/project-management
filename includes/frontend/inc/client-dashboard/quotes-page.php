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
<div class="cqpim_block_title">
	<div class="caption">
		<i class="fa fa-file-text font-green-sharp" aria-hidden="true"></i>
		<span class="caption-subject font-green-sharp sbold"> <?php _e('Quotes / Estimates', 'cqpim'); ?></span>
	</div>
</div>
<br />
<table class="datatable_style dataTable-CQ">
	<thead>
		<tr>
			<th><?php _e('Owner', 'cqpim'); ?></th>
			<th><?php _e('Title', 'cqpim'); ?></th>
			<th><?php _e('Created', 'cqpim'); ?></th>
			<th><?php _e('Status', 'cqpim'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$args = array(
			'post_type' => 'cqpim_quote',
			'posts_per_page' => -1,
			'post_status' => 'private',
		);
		$quotes = get_posts($args);
		$i = 0;
		foreach($quotes as $quote) { 
			$url = get_the_permalink($quote->ID); 
			$quote_details = get_post_meta($quote->ID, 'quote_details', true); 
			$client_contact = isset($quote_details['client_contact']) ? $quote_details['client_contact'] : '';
			$client_id = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
			$client_details = get_post_meta($client_id, 'client_details', true);
			$client_contacts = get_post_meta($client_id, 'client_contacts', true);
			$client_ids = get_post_meta($client_id, 'client_ids', true);
			$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
			if($client_contact == $client_user_id) {
				$fao = get_user_by('id', $client_contact);
				$fao = $fao->display_name;
			} else {
				$fao = isset($client_contacts[$client_contact]['name']) ? $client_contacts[$client_contact]['name'] : 'N/A';
			}
			if(empty($client_ids)) {
				$client_ids = array();
			}
			if(!is_array($client_ids)) {
				$client_ids = array();
			}
			$sent = isset($quote_details['sent']) ? $quote_details['sent'] : ''; 
			$confirmed = isset($quote_details['confirmed']) ? $quote_details['confirmed'] : ''; 
			if(!$confirmed) {
				if(!$sent) {
					$status = '<span class="cqpim_button cqpim_small_button op nolink border-red font-red">' . __('Not Sent', 'cqpim') . '</span>';
				} else {
					$status = '<span class="cqpim_button cqpim_small_button op nolink border-amber font-amber">' . __('New', 'cqpim') . '</span>';
				}
			} else {
				$status = '<span class="cqpim_button cqpim_small_button op nolink border-green font-green">' . __('Accepted', 'cqpim') . '</span>';
			}
			if($client_user_id == $user->ID && !empty($sent) || in_array($user->ID, $client_ids) && !empty($sent)) {
			?>						
				<tr>	
					<td><span class="nodesktop"><strong><?php _e('Owner', 'cqpim'); ?></strong>: </span> <?php echo $fao; ?></td>
					<td><span class="nodesktop"><strong><?php _e('Title', 'cqpim'); ?></strong>: </span> <a class="cqpim-link" href="<?php echo $url; ?>?page=quote"><?php echo $quote->post_title; ?></a></td>
					<td><span class="nodesktop"><strong><?php _e('Created', 'cqpim'); ?></strong>: </span> <?php echo get_the_date(get_option('cqpim_date_format') . ' H:i', $quote->ID); ?></td>
					<td><span class="nodesktop"><strong><?php _e('Status', 'cqpim'); ?></strong>: </span> <?php echo $status; ?></td>
				</tr>
			<?php 
				$i++;
			}
		} 
		if($i == 0) {
			echo '<tr><td>' . __('You do not have any current or past quotes', 'cqpim') . '</td><td></td><td></td><td></td></tr>';
		}
		?>
	</tbody>
</table>
</div>