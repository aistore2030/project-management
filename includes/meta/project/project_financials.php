<?php
function pto_project_financials_metabox_callback( $post ) {
 	wp_nonce_field( 
	'project_financials_metabox', 
	'project_financials_metabox_nonce' ); 
	$args = array(
		'post_type' => 'cqpim_invoice',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC'
	);
	$invoices = get_posts($args);
	$total_paid = 0;
	foreach($invoices as $invoice) {
		$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
		$invoice_payments = get_post_meta($invoice->ID, 'invoice_payments', true);
		if(empty($invoice_payments)) {
			$invoice_payments = array();
		}
		$pid = isset($invoice_details['project_id']) ? $invoice_details['project_id'] : '';
		if(!empty($pid) &&  $pid == $post->ID) {
			foreach($invoice_payments as $payment) {
				$amount = isset($payment['amount']) ? $payment['amount'] : 0;
				$total_paid = $total_paid + $amount;
			}
		}
	}
	$quote_elements = get_post_meta($post->ID, 'project_elements', true);
	$quote_extras = get_post_meta($post->ID, 'project_extras', true);
	$quote_details = get_post_meta($post->ID, 'project_details', true);
	$type = isset($quote_details['quote_type']) ? $quote_details['quote_type'] : '';
	if($quote_elements) {
		echo '<table class="cqpim_table"><thead><tr>';
		echo '<th>' . __('Milestone', 'cqpim') . '</th>';
		if($type == 'estimate') {
			echo '<th>' . __('Estimated Cost', 'cqpim') . '</th>';
			echo '<th>' . __('Final Cost', 'cqpim') . '</th>';
		} else {
			echo '<th>' . __('Cost', 'cqpim') . '</th>';
			echo '<th>' . __('Final Cost', 'cqpim') . '</th>';
		}
		echo '</tr></thead>';
		echo '<tbody>';
		$subtotal = 0;
		$asubtotal = 0;
		$currency = get_option('currency_symbol');
		$ordered = array();
		$i = 0;
		$mi = 0;
		foreach($quote_elements as $key => $element) {
			$weight = isset($element['weight']) ? $element['weight'] : $mi;
			$ordered[$weight] = $element;
			$mi++;
		}
		ksort($ordered);
		foreach($ordered as $element) {
			$acost = isset($element['acost']) ? $element['acost'] : '';
			$cost = preg_replace("/[^\\d.]+/","", $element['cost']);
			$acost = preg_replace("/[^\\d.]+/","", $acost);
			if(empty($acost)) {
				$acost = 0;
			}
			if(!empty($cost)) {
				$subtotal = $subtotal + $cost;
			}
			if(!empty($acost)) {
				$asubtotal = $asubtotal + $acost;
			}
			echo '<tr><td>' . $element['title'] . '</td>';
			if(1) {
				echo '<td>' . pto_calculate_currency($post->ID, $cost) . '</td>';
				if(!empty($acost) || $acost === 0) {
					if($acost > $cost) {
						$class = 'font-white bg-red sbold cqpim_tooltip';
						$title = __('Over Budget', 'cqpim');
					}
					if($acost < $cost) {
						$class = 'font-white bg-green cqpim_tooltip';
						$title = __('Under Budget', 'cqpim');
					}
					if($acost == $cost) {
						$class = 'font-white bg-green cqpim_tooltip';
						$title = __('On Budget', 'cqpim');
					}
					echo '<td class="' . $class . '" title="' . $title . '">' . pto_calculate_currency($post->ID, $acost) . '</td>';
				} else {
					echo '<td><span class="font-red">' . __('PENDING', 'cqpim') . '</span></td>';
				}
			} else {
				echo '<td>' . pto_calculate_currency($post->ID, $cost) . '</td></tr>';
			}
		}
			$span = '';
		if($asubtotal > $subtotal) {
			$class = 'font-white bg-red cqpim_tooltip';
			$title = __('Over Budget', 'cqpim');
		}
		if($asubtotal < $subtotal) {
			$class = 'font-white bg-green cqpim_tooltip';
			$title = __('Under Budget', 'cqpim');
		}
		if($asubtotal == $subtotal) {
			$class = 'font-white bg-green cqpim_tooltip';
			$title = __('On Budget', 'cqpim');
		}			
		$vat = get_post_meta($post->ID, 'tax_applicable', true);			
		if(!empty($vat)) {
			$vat_rate = get_option('sales_tax_rate');
			$stax_rate = get_option('secondary_sales_tax_rate');
			if(!empty($vat_rate)) {
				$total_vat = $subtotal / 100 * $vat_rate;
			}
			if(!empty($stax_rate)) {
			$total_stax = $subtotal / 100 * $stax_rate;
			}
			$stax_applicable = get_post_meta($post->ID, 'stax_applicable', true);
			if(!empty($stax_applicable)) {
				$total = $subtotal + $total_vat + $total_stax;
			} else {
				$total = $subtotal + $total_vat;
			}
			$tax_name = get_option('sales_tax_name');
			$stax_name = get_option('secondary_sales_tax_name');
			$outstanding = $total - $total_paid;
			if(!empty($vat_rate)) {
			$atotal_vat = $asubtotal / 100 * $vat_rate;
			}
			if(!empty($stax_rate)) {
			$atotal_stax = $asubtotal / 100 * $stax_rate;
			}
			if(!empty($stax_applicable)) {
				$atotal = $asubtotal + $atotal_vat + $atotal_stax;
			} else {
				$atotal = $asubtotal + $atotal_vat;
			}
			$aoutstanding = $atotal - $total_paid;
			echo '<tr><td ' . $span . ' align="right" class="quote-align-right">' . __('Subtotal:', 'cqpim') . '</td>';
			echo '<td class="subtotal">' . $currency . '' . $subtotal . '</td>';
			echo '<td class="subtotal ' . $class . '" title="' . $title . '">' . pto_calculate_currency($post->ID, $asubtotal) . '</td>';			
			echo '</tr>';
			echo '<tr><td ' . $span . ' align="right" class="quote-align-right">' . $tax_name . ': </td>';
			echo '<td class="subtotal">' . pto_calculate_currency($post->ID, $total_vat) . '</td>';
				echo '<td class="subtotal">' . pto_calculate_currency($post->ID, $atotal_vat) . '</td>';			
			echo '</tr>';
			if(!empty($stax_applicable)) {
				echo '<tr><td ' . $span . ' align="right" class="quote-align-right">' . $stax_name . ': </td>';
				echo '<td class="subtotal">' . pto_calculate_currency($post->ID, $total_stax) . '</td>';
					echo '<td class="subtotal">' . pto_calculate_currency($post->ID, $atotal_stax) . '</td>';			
				echo '</tr>';
			}
			echo '<tr><td ' . $span . ' align="right" class="quote-align-right">' . __('TOTAL:', 'cqpim') . '</td>';
			echo '<td class="subtotal">' . pto_calculate_currency($post->ID, $total) . '</td>';
				echo '<td class="subtotal">' . pto_calculate_currency($post->ID, $atotal) . '</td>';			
				$aatotal = $atotal;
			echo '</tr>';
				echo '<tr><td colspan="2" align="right" class="quote-align-right">' . __('Received:', 'cqpim') . '</td>';
				echo '<td class="subtotal">' . pto_calculate_currency($post->ID, $total_paid) . '</td>';			
			echo '</tr>';
				echo '<tr><td colspan="2" align="right" class="quote-align-right">' . __('Outstanding:', 'cqpim') . '</td>';
				echo '<td class="subtotal">' . pto_calculate_currency($post->ID, $aoutstanding) . '</td>';			
			echo '</tr>';
		} else {
			$atotal = $asubtotal;
			$aoutstanding = $atotal - $total_paid;
			$span = 'colspan="2"';
			echo '<tr><td align="right" class="quote-align-right">' . __('TOTAL:', 'cqpim') . '</td><td class="subtotal">' . pto_calculate_currency($post->ID, $subtotal) . '</td><td class="subtotal ' . $class . '">' . pto_calculate_currency($post->ID, $asubtotal) . '</td></tr>';
			echo '<tr><td ' . $span . ' align="right" class="quote-align-right">' . __('Received:', 'cqpim') . '</td><td class="subtotal">' . pto_calculate_currency($post->ID, $total_paid) . '</td></tr>';				
			echo '<tr><td ' . $span . ' align="right" class="quote-align-right">' . __('Outstanding:', 'cqpim') . '</td><td class="subtotal">' . pto_calculate_currency($post->ID, $aoutstanding) . '</td></tr>';
		}
		if(pto_check_addon_status('expenses')) {
			$args = array(
				'post_type' => 'cqpim_expense',
				'posts_per_page' => -1,
				'post_status' => 'private',
				'meta_key' => 'project_id',
				'meta_value' => $post->ID
			);
			$expenses = get_posts($args);
			if(!empty($expenses)) {
				echo '<tr><th colspan="3">' . __('Project Expenses', 'cqpim') . '</th></tr>';
				echo '<tr><th colspan="2">' . __('Expense', 'cqpim') . '</th><th>' . __('Cost', 'cqpim') . '</th></tr>';
				$expense_total = 0;
				foreach($expenses as $expense) {
					unset($auth);
					$invoice_totals = get_post_meta($expense->ID, 'invoice_totals', true); 
					$invoice_total = isset($invoice_totals['total']) ? $invoice_totals['total'] : 0;
					$expense_total = $expense_total + $invoice_total;
					$auth = get_post_meta($expense->ID, 'auth_active', true);
					$auth_limit = get_option('cqpim_expense_auth_limit');
					$authorised = get_post_meta($expense->ID, 'authorised', true);	
					if(empty($auth) || !empty($auth) && !empty($authorised) && $authorised == 1 || !empty($auth) && empty($authorised) && !empty($auth_limit) && $auth_limit > $invoice_total) {							
						echo '<tr><td colspan="2">' . $expense->post_title . '</td><td>' . pto_calculate_currency($expense->ID, $invoice_total) . '</td></tr>';
					}
				}
				echo '<tr><td colspan="2">' . __('Total:', 'cqpim') . '</td><td>' . pto_calculate_currency($post->ID, $expense_total) . '</td></tr>';
				if(!empty($vat)) {
					if(!empty($stax_applicable)) {
						$total = $aatotal - $atotal_vat - $atotal_stax;
					} else {
						$total = $aatotal - $atotal_vat;
					}
				} else {
					$total = $asubtotal;
				}
				$profit = $total - $expense_total;
				echo '<tr><td colspan="2"><strong>' . __('Profit:', 'cqpim') . '</strong></td><td><strong>' . pto_calculate_currency($post->ID, $profit) . '</strong></td></tr>';
			}				
		}
		echo '</tbody></table>';
	} else {
		echo '<p>' . __('You have not added any milestones. Please add at least one milestone to enable this section', 'cqpim') . '</p>';
	} 
}