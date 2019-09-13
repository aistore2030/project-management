<?php
function pto_client_invoices_metabox_callback( $post ) {
 	wp_nonce_field( 
	'client_invoices_metabox', 
	'client_invoices_metabox_nonce' ); 
	$args = array(
		'post_type' => 'cqpim_invoice',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'meta_key' => 'invoice_client',
		'meta_value' => $post->ID
	);
	$invoices = get_posts($args);
	if($invoices) {
		echo '<table class="datatable_style dataTable" data-sort="[[ 0, \'desc\' ]]" data-rows="5">';
		echo '<thead>';
		echo '<tr><th>' . __('Invoice ID', 'cqpim') . '</th><th>' . __('Project Ref', 'cqpim') . '</th><th>' . __('Invoice Date', 'cqpim') . '</th><th>' . __('Due Date', 'cqpim') . '</th><th>' . __('Amount', 'cqpim') . '</th><th>' . __('Outstanding', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th></tr>';
		echo '</thead>';
		foreach($invoices as $invoice) {
				$invoice_link = get_edit_post_link($invoice->ID);
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
				$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
				if(is_numeric($invoice_date)) { $invoice_date = date(get_option('cqpim_date_format'), $invoice_date); } else { $invoice_date = $invoice_date; }
				$total = isset($invoice_totals['total']) ? $invoice_totals['total'] : '';
				$subtotal = isset($invoice_totals['sub']) ? $invoice_totals['sub'] : '';
				$tax_rate = get_option('sales_tax_rate');
				$tax_applicable = get_post_meta($invoice->ID, 'tax_applicable', true);
				if($tax_applicable == 1) {
					$total = $total;
				} else {
					$total = $subtotal;
				}
				$project_id = isset($invoice_details['project_id']) ? $invoice_details['project_id'] : '';
				$project_details = get_post_meta($project_id, 'project_details', true);
				$project_object = get_post($project_id);
				$project_ref = $project_object->post_title;
				$project_link = get_edit_post_link($project_id);
				$on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';
				$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
				if(empty($on_receipt)) {
					$due_string = date(get_option('cqpim_date_format'), $due);
				} else {
					$due_string = __('Due on Receipt', 'cqpim');
				}
				$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
				$sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
				$now = time();
				if(!$paid) {
					if($due) {
						if($now > $due) {
							if(empty($on_receipt)) {
								$class = 'overdue';
								$status = '<span class="cqpim_button cqpim_xs_button border-red font-red nolink op upper"><strong>' . __('OVERDUE', 'cqpim') . '</strong></span>';
							} else {
								$class = 'overdue';
								$status = '<span class="cqpim_button cqpim_xs_button border-red font-red nolink op"><strong>' . __('DUE ON RECEIPT', 'cqpim') . '</strong></span>';
							}		
						} else {
							if($sent) {
								$class = 'sent';
								$status = '<span class="cqpim_button cqpim_xs_button border-amber font-amber nolink op">' . __('Sent', 'cqpim') . '</span>';							
							} else {
								$class = 'not-sent';
								$status = '<span class="cqpim_button cqpim_xs_button border-red font-red nolink op">' . __('Not Sent', 'cqpim') . '</span>';							
							}
						}
					}
				} else {
					$class = 'paid';
					$status = '<span class="cqpim_button cqpim_xs_button border-green font-green nolink op">' . __('Paid', 'cqpim') . '</span>';
				}
				$outstanding = $total - $total_paid;
				echo '<tr>';
				echo '<td><span class="cqpim_mobile">' . __('Invoice ID:', 'cqpim') . '</span> <a href="' . $invoice_link . '">' . $invoice_id . '</a></td>';
				if($project_ref) {
					echo '<td><span class="cqpim_mobile">' . __('Project / Ticket:', 'cqpim') . '</span> <a href="' . $project_link . '">' . $project_ref . '</a></td>';
				} else {
					echo '<td><span class="cqpim_mobile">' . __('Project / Ticket:', 'cqpim') . '</span> ' . __('N/A', 'cqpim') . '</td>';
				}
				echo '<td><span class="cqpim_mobile">' . __('Invoice Date:', 'cqpim') . '</span> ' . $invoice_date . '</td>';
				echo '<td><span class="cqpim_mobile">' . __('Due:', 'cqpim') . '</span> ' . $due_string . '</td>';
				echo '<td><span class="cqpim_mobile">' . __('Total:', 'cqpim') . '</span> ' . pto_calculate_currency($invoice->ID, $total) . '</td>';
				echo '<td><span class="cqpim_mobile">' . __('Outstanding:', 'cqpim') . '</span> ' . pto_calculate_currency($invoice->ID, $outstanding) . '</td>';
				echo '<td class="' . $class . '"><span class="cqpim_mobile">' . __('Status:', 'cqpim') . '</span> ' . $status . '</td>';
				echo '</tr>';				
		}
		echo '</table>';
	} else {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('This client does not have any invoices assigned...', 'cqpim') . '</div>';
	}
}