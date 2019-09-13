<?php
function pto_team_expenses_metabox_callback( $post ) {
	$team_details = get_post_meta($post->ID, 'team_details', true);
	$team_user = isset($team_details['user_id']) ? $team_details['user_id'] : '';
	$team_user_object = get_user_by('id', $team_user); 
	$args = array(
		'post_type' => 'cqpim_expense',
		'posts_per_page' => -1,
		'post_status' => 'private',
		'author' => isset($team_user_object->ID) ? $team_user_object->ID : 0,
	);
	$expenses = get_posts($args);	
	if(empty($expenses)) { ?>
		<p><?php _e('This team member has not added any expenses', 'cqpim'); ?></p>
	<?php } else { ?>
		<table class="datatable_style dataTable" data-ordering="[[2,'desc']]" data-rows="10">
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
	<p class="underline"><?php _e('Team Member Expenses by Month', 'cqpim'); ?></p>
	<br />
	<div class="income_controls">
		<?php $selected = isset($_SESSION['invoice_year']) ? $_SESSION['invoice_year'] : date('Y'); ?>
		<select id="income_control_date">
			<?php $date = date('Y'); ?>
			<option value="<?php echo $date ?>" <?php if($date == $selected) { ?> selected="selected"<?php } ?>><?php echo $date ?></option>
			<?php $date = date('Y', strtotime("-1 year")); ?>
			<option value="<?php echo $date ?>" <?php if($date == $selected) { ?> selected="selected"<?php } ?>><?php echo $date ?></option>
			<?php $date = date('Y', strtotime("-2 years")); ?>
			<option value="<?php echo $date ?>" <?php if($date == $selected) { ?> selected="selected"<?php } ?>><?php echo $date ?></option>
			<?php $date = date('Y', strtotime("-3 years")); ?>
			<option value="<?php echo $date ?>" <?php if($date == $selected) { ?> selected="selected"<?php } ?>><?php echo $date ?></option>
		</select>
	</div>
	<div id="graph_container">
		<canvas id="income_graph"></canvas>
		<?php 
		if(empty($_SESSION['invoice_year'])) { $_SESSION['invoice_year'] = date('Y'); } 
		$invoices_generated = array();
		$args = array(
			'post_type' => 'cqpim_expense',
			'posts_per_page' => -1,
			'post_status' => 'private',
			'author' => isset($team_user_object->ID) ? $team_user_object->ID : 0,
		);
		$invoices = get_posts($args);
		$invoices_generated = array();
		foreach($invoices as $invoice) {
			unset($auth);
			$invoice_date = $expense_date = get_post_meta($invoice->ID, 'expense_date', true);
			$invoice_totals = get_post_meta($invoice->ID, 'invoice_totals', true); 
			$invoice_total = isset($invoice_totals['total']) ? $invoice_totals['total'] : 0;
			$invoice_date = date('d,m,Y', $invoice_date);
			$invoice_date = explode(',', $invoice_date);
			$auth = get_post_meta($invoice->ID, 'auth_active', true);
			$auth_limit = get_option('cqpim_expense_auth_limit');
			$authorised = get_post_meta($invoice->ID, 'authorised', true);
			if(empty($invoices_generated[$invoice_date[2]][$invoice_date[1]])) {
				$invoices_generated[$invoice_date[2]][$invoice_date[1]] = 0;
			}
			if(empty($auth) || !empty($auth) && !empty($authorised) && $authorised == 1 || !empty($auth) && empty($authorised) && !empty($auth_limit) && $auth_limit > $invoice_total) {							
				$invoices_generated[$invoice_date[2]][$invoice_date[1]] = $invoices_generated[$invoice_date[2]][$invoice_date[1]] + $invoice_total;
			}
		}
		$data = isset($invoices_generated[$_SESSION['invoice_year']]) ? $invoices_generated[$_SESSION['invoice_year']] : '';
		$amounts = array();
		$months = array('01','02','03','04','05','06','07','08','09','10','11','12');
		foreach($months as $month) {
			if(empty($data[$month])) {
				$data[$month] = 0;
			}
		}
		$data = is_array($data)?$data:array();
		ksort($data);
		foreach($data as $key => $month) {
			$amounts[] = $month;
		}
		$data = implode(', ', $amounts);
		?>
		<script>
			jQuery(document).ready(function() {
				var ctx = document.getElementById("income_graph");
				ctx.height = 150;
				var income_graph = new Chart(ctx, {
					type: 'bar',
					responsive: true,
					maintainAspectRatio: false,
					data: {
						labels: ["<?php _e('January', 'cqpim-expenses'); ?>", "<?php _e('February', 'cqpim'); ?>", "<?php _e('March', 'cqpim'); ?>", "<?php _e('April', 'cqpim'); ?>", "<?php _e('May', 'cqpim'); ?>", "<?php _e('June', 'cqpim'); ?>", "<?php _e('July', 'cqpim'); ?>", "<?php _e('August', 'cqpim'); ?>", "<?php _e('September', 'cqpim'); ?>", "<?php _e('October', 'cqpim'); ?>", "<?php _e('November', 'cqpim'); ?>", "<?php _e('December', 'cqpim'); ?>"],
						datasets: [{
							label: '<?php _e('Expenses by Month', 'cqpim-expenses'); ?>: <?php echo $_SESSION['invoice_year']; ?>',
							data: [<?php echo $data; ?>],
							backgroundColor: [
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
							],
							borderColor: [
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
								'rgb(59,63,81)',
							],
							borderWidth: 1
						}]
					},
					options: {
						scales: {
							yAxes: [{
								ticks: {
									beginAtZero:true
								}
							}]
						}
					}
				});		
			});
		</script>
	</div>
<?php } ?>