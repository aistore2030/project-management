<?php
function pto_project_expenses_metabox_callback( $post ) {
	$args = array(
		'post_type' => 'cqpim_expense',
		'posts_per_page' => -1,
		'post_status' => 'private',
		'meta_key' => 'project_id',
		'meta_value' => $post->ID
	);
	$expenses = get_posts($args);	
	if(empty($expenses)) { ?>
		<p><?php _e('There are no expenses on this project.', 'cqpim'); ?></p>
	<?php } else { ?>
		<table class="datatable_style dataTable-PE">
			<thead>
				<tr>
					<th><?php _e('ID', 'cqpim-expenses'); ?></th>
					<th><?php _e('Title', 'cqpim-expenses'); ?></th>
					<th style="display:none"><?php _e('Stamp', 'cqpim-expenses'); ?></th>
					<th><?php _e('Date', 'cqpim-expenses'); ?></th>
					<th><?php _e('Supplier', 'cqpim-expenses'); ?></th>
					<th><?php _e('Amount', 'cqpim-expenses'); ?></th>
					<th><?php _e('Status', 'cqpim-expenses'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($expenses as $expense) { 
					$expense_date = get_post_meta($expense->ID, 'expense_date', true);
					$auth = get_post_meta($expense->ID, 'auth_active', true);
					$auth_limit = get_option('cqpim_expense_auth_limit');
					$totals = get_post_meta($expense->ID, 'invoice_totals', true);
					$total = isset($totals['total']) ? $totals['total'] : '';
					$authorised = get_post_meta($expense->ID, 'authorised', true); 
					$auth_requested = get_post_meta($expense->ID, 'auth_requested', true);
					$supplier_id = get_post_meta($expense->ID, 'supplier_id', true); 
					$supplier = get_post($supplier_id);
					if(empty($auth)) {
						$status = array(
							'color' => 'green',
							'message' => __('Live', 'cqpim-expenses'),
						);
					} else {
						if(!empty($authorised)) {
							if($authorised == 1) {
								$status = array(
									'color' => 'green',
									'message' => __('Live (Authorised)', 'cqpim-expenses'),
								);		
							}
							if($authorised == 2) {
								$status = array(
									'color' => 'red',
									'message' => __('Authorisation Declined', 'cqpim-expenses'),
								);		
							}
						} else {
							if(!empty($auth_requested)) {
								$status = array(
									'color' => 'amber',
									'message' => __('Awaiting Authorisation', 'cqpim-expenses'),
								);												
							} else {
								if(!empty($total)) {
									if(!empty($auth_limit)) {
										if($total < $auth_limit) {
											$status = array(
												'color' => 'green',
												'message' => __('Live (Authorisation Not Required)', 'cqpim-expenses'),
											);															
										} else {
											$status = array(
												'color' => 'red',
												'message' => __('Requires Authorisation', 'cqpim-expenses'),
											);															
										}
									} else {
										$status = array(
											'color' => 'red',
											'message' => __('Requires Authorisation', 'cqpim-expenses'),
										);														
									}
								} else {
									$status = array(
										'color' => 'grey-cascade',
										'message' => __('New', 'cqpim-expenses'),
									);													
								}
							}
						}									
					}
					?>
					<tr>
						<td><a href="<?php echo get_edit_post_link($expense->ID); ?>"><?php echo $expense->ID; ?></a></td>
						<td><a href="<?php echo get_edit_post_link($expense->ID); ?>"><?php echo $expense->post_title; ?></a></td>
						<td style="display:none"><?php echo $expense_date; ?></td>
						<td><?php echo date(get_option('cqpim_date_format'), $expense_date); ?></td>
						<td><?php echo isset($supplier->post_title) ? $supplier->post_title : ''; ?></td>
						<td><?php echo pto_calculate_currency($expense->ID, $total); ?></td>
						<td><div class="cqpim_button cqpim_small_button nolink font-<?php echo $status['color']; ?> border-<?php echo $status['color']; ?> op"><?php echo $status['message']; ?></div></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php } ?>		
<?php }