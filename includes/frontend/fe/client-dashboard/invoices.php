<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-credit-card-alt font-green-sharp" aria-hidden="true"></i> <?php _e('Invoices', 'cqpim'); ?>
		</div>
	</div>
	<div class="cqpim-dash-item-inside">
		<?php
		$args = array(
		'post_type' => 'cqpim_invoice',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC'
		);
		$invoices = get_posts($args);
		if($invoices) {
			$currency = get_option('currency_symbol');
			echo '<table class="datatable_style dataTable-CI">';
			echo '<thead>';
			echo '<tr><th>' . __('Invoice ID', 'cqpim') . '</th><th>' . __('Owner', 'cqpim') . '</th><th>' . __('Invoice Date', 'cqpim') . '</th><th>' . __('Due Date', 'cqpim') . '</th><th>' . __('Amount', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th></tr>';
			echo '</thead>';
			echo '<tbody>';
			$i = 0;
			foreach($invoices as $invoice) {
				$invoice_id = get_post_meta($invoice->ID, 'invoice_id', true);
				$client_contact = get_post_meta($invoice->ID, 'client_contact', true);
				$owner = get_user_by('id', $client_contact);
				$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
				$client_id = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
				$client_details = get_post_meta($client_id, 'client_details', true);
				$client_ids = get_post_meta($client_id, 'client_ids', true);
				$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
				$invoice_totals = get_post_meta($invoice->ID, 'invoice_totals', true);
				$tax_rate = get_option('sales_tax_rate');
				if(!empty($tax_rate)) {
					$total = isset($invoice_totals['total']) ? $invoice_totals['total'] : '';
				} else {
					$total = isset($invoice_totals['sub']) ? $invoice_totals['sub'] : '';
				}					
				$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
				if(is_numeric($invoice_date)) { $invoice_date = date(get_option('cqpim_date_format'), $invoice_date); } else { $invoice_date = $invoice_date; }
				$sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
				$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
				$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
				$on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';
				$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
				if(empty($on_receipt)) {
					$due_readable = date(get_option('cqpim_date_format'), $due);
				} else {
					$due_readable = __('Due on Receipt', 'cqpim');
				}
				$current_date = time();
				$link = get_the_permalink($invoice->ID);
				$password = md5($invoice->post_password);
				$url = $link;
				if(!$paid) {
					if($current_date > $due) {
						if(empty($on_receipt)) {
							$status = '<span class="task_over"><strong>' . __('OVERDUE', 'cqpim') . '</strong></span>';
						} else {
							$status = '<span class="task_over"><strong>' . __('Due on Receipt', 'cqpim') . '</strong></span>';
						}
					} else {
						if(!$sent) {
							$status = '<span class="task_over">' . __('New', 'cqpim') . '</span>';
						} else {
							$status = '<span class="task_pending">' . __('Outstanding', 'cqpim') . '</span>';
						}
					}
				} else {
					$status = '<span class="task_complete">' . __('PAID', 'cqpim') . '</span>';
				}		
				if(!is_array($client_ids)) {
					$client_ids = array($client_ids);
				}
				if($client_user_id == $user->ID && !empty($sent) || in_array($user->ID, $client_ids)) {
					echo '<tr>';
					echo '<td><span class="nodesktop"><strong>' . __('ID', 'cqpim') . '</strong>: </span> <a class="cqpim-link" href="' . $url . '" >' . $invoice_id . '</a></td>';
					echo '<td><span class="nodesktop"><strong>' . __('Owner', 'cqpim') . '</strong>: </span> ' . $owner->display_name . '</td>';
					echo '<td><span class="nodesktop"><strong>' . __('Date', 'cqpim') . '</strong>: </span> ' . $invoice_date . '</td>';
					echo '<td><span class="nodesktop"><strong>' . __('Due', 'cqpim') . '</strong>: </span> ' . $due_readable . '</td>';
					echo '<td><span class="nodesktop"><strong>' . __('Amount', 'cqpim') . '</strong>: </span> ' . $currency . '' . $total . '</td>';
					echo '<td><span class="nodesktop"><strong>' . __('Status', 'cqpim') . '</strong>: </span> ' . $status . '</td>';
					echo '</tr>';
					$i++;
				}
			}
			if($i == 0) {
				echo '<tr><td>' . __('You do not have any invoices to show.', 'cqpim') . '</td><td></td><td></td><td></td><td></td><td></td></tr>';
			}
			echo '</tbody>';
			echo '</table>';
		} else {
			echo '<table class="datatable_style dataTable-CI">';
			echo '<thead>';
			echo '<tr><th>' . __('Invoice ID', 'cqpim') . '</th><th>' . __('Owner', 'cqpim') . '</th><th>' . __('Invoice Date', 'cqpim') . '</th><th>' . __('Due Date', 'cqpim') . '</th><th>' . __('Amount', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th></tr>';
			echo '</thead>';
			echo '<tbody>';	
			echo '<tr><td>' . __('You do not have any invoices to show.', 'cqpim') . '</td><td></td><td></td><td></td><td></td><td></td></tr>';
			echo '</tbody>';
			echo '</table>';		
		}
		?>
	</div>
</div>	