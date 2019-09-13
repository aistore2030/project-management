<?php
function pto_support_invoices_metabox_callback( $post ) {
 	wp_nonce_field( 
	'support_invoices_metabox', 
	'support_invoices_metabox_nonce' );
	$args = array(
		'post_type' => 'cqpim_invoice',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC',
		'meta_key' => 'invoice_project',
		'meta_value' => $post->ID
	);
	$invoices = get_posts($args);
	if($invoices) {
		$currency = get_option('currency_symbol');
		echo '<table class="cqpim_table">';
		echo '<thead>';
		echo '<tr><th>' . __('Invoice ID', 'cqpim') . '</th><th>' . __('Invoice Date', 'cqpim') . '</th><th>' . __('Due Date', 'cqpim') . '</th><th>' . __('Amount', 'cqpim') . '</th><th>' . __('Outstanding', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th></tr>';
		echo '</thead>';
		echo '<tbody>';
		$i = 0;
		foreach($invoices as $invoice) {
			$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
			$invoice_payments = get_post_meta($invoice->ID, 'invoice_payments', true);
			$total_paid = 0;
			if(empty($invoice_payments)) {
				$invoice_payments = array();
			}
			foreach($invoice_payments as $payment) {
				$amount = isset($payment['amount']) ? $payment['amount'] : 0;
				$total_paid = $total_paid + $amount;
			}				
			$invoice_totals = get_post_meta($invoice->ID, 'invoice_totals', true);
			$invoice_id = get_post_meta($invoice->ID, 'invoice_id', true);
			$total = isset($invoice_totals['total']) ? $invoice_totals['total'] : '';
			$tax_rate = get_option('sales_tax_rate');
			$tax_applicable = get_post_meta($invoice->ID, 'tax_applicable', true);
			if($tax_applicable == 1) {
				$total = $total;
			} else {
				$total = isset($invoice_totals['sub']) ? $invoice_totals['sub'] : '';;
			}
			$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
			if(is_numeric($invoice_date)) { $invoice_date = date(get_option('cqpim_date_format'), $invoice_date); } else { $invoice_date = $invoice_date; }
			$sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
			$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
			$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
			if(is_numeric($due)) { $due_readable = date(get_option('cqpim_date_format'), $due); } else { $due_readable = $due; }
			$current_date = current_time('timestamp');
			$link = get_edit_post_link($invoice->ID);
			if(!$paid) {
				if($current_date > $due) {
					$status = '<div class="cqpim_button cqpim_small_button nolink op border-red font-red"><strong>' . __('OVERDUE', 'cqpim') . '</strong></div>';
				} else {
					if(!$sent) {
						$status = '<div class="cqpim_button cqpim_small_button nolink op border-red font-red">' . __('Not Sent', 'cqpim') . '</div>';
					} else {
						$status = '<div class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . __('Sent', 'cqpim') . '</div>';
					}
				}
			} else {
				$status = '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . __('Paid', 'cqpim') . '</span>';
			}		
			$outstanding = $total - $total_paid;
			echo '<tr>';
			echo '<td><a href="' . $link . '" target="_blank">' . $invoice_id . '</a></td>';
			echo '<td>' . $invoice_date . '</td>';
			echo '<td>' . $due_readable . '</td>';
			echo '<td>' . pto_calculate_currency($invoice->ID, $total) . '</td>';
			echo '<td>' . pto_calculate_currency($invoice->ID, $outstanding) . '</td>';
			echo '<td>' . $status . '</td>';
			echo '</tr>';
			$i++;			
		}
		echo '</tbody>';
		echo '</table>';
	} else {
		echo '<p>' . __('There are no invoices for this support ticket', 'cqpim') . '</p>';
	}
}