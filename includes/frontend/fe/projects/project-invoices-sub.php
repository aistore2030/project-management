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
	'page' => sprintf(__('Project %1$s - %2$s (Invoices Page)', 'cqpim'), get_the_ID(), $title)
);
update_post_meta($assigned, 'client_logs', $client_logs);
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-full grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php _e('Ongoing Costs', 'cqpim'); ?></span>
				</div>	
			</div>
			<?php
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
				if(!empty($invoice_details['project_id']) && $invoice_details['project_id'] == $post->ID) {
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
						if(!empty($cost)) {
						$subtotal = $subtotal + $cost;
						}

						if(!empty($acost)) {
						$asubtotal = $asubtotal + $acost;
						}

					echo '<tr><td>' . $element['title'] . '</td>';
					if(1) {
						echo '<td>' . pto_calculate_currency($post->ID, $cost) . '</td>';
						if($acost) {
							if($acost > $cost) {
								$class = 'over';
							}
							if($acost < $cost) {
								$class = 'under';
							}
						if($acost == $cost) {
							$class = 'under';
						}
							echo '<td class="' . $class . '">' . pto_calculate_currency($post->ID, $acost) . '</td>';
						} else {
							echo '<td><span style="color:#d9534f">' . __('PENDING', 'cqpim') . '</span></td>';
						}
					} else {
						echo '<td>' . $currency . '' . $cost . '</td></tr>';
					}
				}
					$span = '';
				if($asubtotal > $subtotal) {
					$class = 'over';
				}
				if($asubtotal < $subtotal) {
					$class = 'under';
				}
				if($asubtotal == $subtotal) {
					$class = 'under';
				}			
				$vat = get_post_meta($post->ID, 'tax_applicable', true);			
				if(!empty($vat)) {
					$vat_rate = get_option('sales_tax_rate');
					$stax_rate = get_option('secondary_sales_tax_rate');
					// Estimated
					$total_vat = $subtotal / 100 * $vat_rate;
					$total_stax = $subtotal / 100 * $stax_rate;
					$stax_applicable = get_post_meta($post->ID, 'stax_applicable', true);
					if(!empty($stax_applicable)) {
						$total = $subtotal + $total_vat + $total_stax;
					} else {
						$total = $subtotal + $total_vat;
					}
					$tax_name = get_option('sales_tax_name');
					$stax_name = get_option('secondary_sales_tax_name');
					$outstanding = $total - $total_paid;
					// Actual
					$atotal_vat = $asubtotal / 100 * $vat_rate;
					$atotal_stax = $asubtotal / 100 * $stax_rate;
					if(!empty($stax_applicable)) {
						$atotal = $asubtotal + $atotal_vat + $atotal_stax;
					} else {
						$atotal = $asubtotal + $atotal_vat;
					}
					$aoutstanding = $atotal - $total_paid;
					echo '<tr><td ' . $span . ' align="right" class="quote-align-right">' . __('Subtotal:', 'cqpim') . '</td>';
					echo '<td class="subtotal">' . pto_calculate_currency($post->ID, $subtotal) . '</td>';
					echo '<td class="subtotal ' . $class . '">' . pto_calculate_currency($post->ID, $asubtotal) . '</td>';			
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
				echo '</tbody></table>';
			} else {
				echo '<p style="padding:30px">';
				_e( 'You have not added any milestones. Please add at least one milestone to enable this section', 'cqpim');
				echo '</p>';
			}
			?>
		</div>
	</div>
	<div class="cqpim-dash-item-full grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php _e('Project Invoices', 'cqpim'); ?></span>
				</div>	
			</div>
		<?php
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
			echo '<tr><th>' . __('Invoice ID', 'cqpim') . '</th><th>' . __('Invoice Date', 'cqpim') . '</th><th>' . __('Due Date', 'cqpim') . '</th><th>' . __('Amount', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th></tr>';
			echo '</thead>';
			echo '<tbody>';
			$i = 0;
			foreach($invoices as $invoice) {
				$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
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
				$due_readable = date(get_option('cqpim_date_format'), $due);
				$current_date = time();
				$link = get_the_permalink($invoice->ID);
				$url = $link;
				if(!$paid) {
					if($current_date > $due) {
						$status = '<span class="cqpim_button cqpim_small_button font-red border-red sbold nolink op"><strong>' . __('OVERDUE', 'cqpim') . '</strong></span>';
					} else {
						if(!$sent) {
							$status = '<span class="cqpim_button cqpim_small_button font-red border-red nolink op">' . __('NOT SENT', 'cqpim') . '</span>';
						} else {
							$status = '<span class="cqpim_button cqpim_small_button font-amber border-amber nolink op">' . __('SENT', 'cqpim') . '</span>';
						}
					}
				} else {
					$status = '<span class="cqpim_button cqpim_small_button font-green border-green nolink op">' . __('PAID', 'cqpim') . '</span>';
				}				
				echo '<tr>';
				echo '<td><span class="nodesktop"><strong>' . __('ID', 'cqpim') . '</strong>: </span> <a class="cqpim-link" href="' . $url . '" target="_blank">' . $invoice_id . '</a></td>';
				echo '<td><span class="nodesktop"><strong>' . __('Date', 'cqpim') . '</strong>: </span> ' . $invoice_date . '</td>';
				echo '<td><span class="nodesktop"><strong>' . __('Due', 'cqpim') . '</strong>: </span> ' . $due_readable . '</td>';
				echo '<td><span class="nodesktop"><strong>' . __('Amount', 'cqpim') . '</strong>: </span> ' . pto_calculate_currency($invoice->ID, $total) . '</td>';
				echo '<td>' . $status . '</td>';
				echo '</tr>';
				$i++;			
			}
			echo '</tbody>';
			echo '</table>';
		} else {
			echo '<p>' . __('There are no invoices for this project', 'cqpim') . '</p>';
		}
		?>
		</div>
	</div>
</div>