<?php
function pto_invoice_items_metabox_callback( $post ) {
 	wp_nonce_field( 
	'invoice_items_metabox', 
	'invoice_items_metabox_nonce' ); 
	$line_items = get_post_meta($post->ID, 'line_items', true); 
	$client = get_post_meta($post->ID, 'invoice_client', true);
	$tax_name = get_option('sales_tax_name');
	$stax_name = get_option('secondary_sales_tax_name');
	$tax_applicable = get_post_meta($post->ID, 'tax_applicable', true);
	$stax_applicable = get_post_meta($post->ID, 'stax_applicable', true);
	if(!empty($client)) {
	?>
	<table style="table-layout: fixed" class="cqpim_table">
		<thead>
			<tr>
				<th style="width:10%"><?php _e('Qty', 'cqpim'); ?></th>
				<th style="width:45%"><?php _e('Description', 'cqpim'); ?></th>
				<th style="width:12.5%"><?php _e('Price', 'cqpim'); ?> (<?php echo pto_calculate_currency($post->ID); ?>)</th>
				<th style="width:12.5%"><?php _e('Total', 'cqpim'); ?> (<?php echo pto_calculate_currency($post->ID); ?>)</th>
				<?php if(!empty($tax_applicable)) { ?>
					<th style="width:7.5%"><?php printf(__('Ex. %1$s', 'cqpim'), $tax_name); ?></th>
					<?php $tax = true; ?>
				<?php } ?>
				<?php if(!empty($stax_applicable)) { ?>
					<th style="width:7.5%"><?php printf(__('Ex. %1$s', 'cqpim'), $stax_name); ?></th>
					<?php $stax = true; ?>
				<?php } ?>
				<?php
					$width = 5;
					if(empty($tax)) {
						$width = 12.5;
					}
					if(empty($tax) && empty($stax)) {
						$width = 20;
					}
				?>
				<th style="width:<?php echo $width; ?>%">&nbsp;</th>
			</tr>
		</thead>
		</table>
	<div class="repeater">	
		<div data-repeater-list="group-a">
				<?php if($line_items) { 
					$i = 0;
					foreach($line_items as $item) { ?>
						<div class="line_item" data-repeater-item>
							<table style="table-layout: fixed" class="cqpim_table invoice-items">
								<tbody>
									<tr>							
										<td style="width:10%"><span class="cqpim_mobile"><?php _e('Qty', 'cqpim'); ?></span><input data-row="<?php echo $i; ?>" id="invoice_qty_<?php echo $i; ?>" class="invoice_qty" type="text" name="qty" value="<?php echo $item['qty']; ?>" placeholder="<?php _e('Quantity', 'cqpim'); ?>" required /></td>
										<td style="width:45%"><span class="cqpim_mobile"><?php _e('Description', 'cqpim'); ?></span><input data-row="<?php echo $i; ?>" id="invoice_desc_<?php echo $i; ?>" class="invoice_desc" type="text" name="desc" value="<?php echo $item['desc']; ?>" placeholder="<?php _e('Description', 'cqpim'); ?>" required /></td>
										<td style="width:12.5%"><span class="cqpim_mobile"><?php _e('Price', 'cqpim'); ?></span><input data-row="<?php echo $i; ?>" id="invoice_price_<?php echo $i; ?>" class="invoice_price" type="text" name="price" value="<?php echo $item['price']; ?>" placeholder="<?php _e('Price', 'cqpim'); ?> (<?php echo pto_calculate_currency($post->ID); ?>)" required /></td>
										<td style="width:12.5%"><span class="cqpim_mobile"><?php _e('Line Total', 'cqpim'); ?></span><input data-row="<?php echo $i; ?>" id="invoice_line_total_<?php echo $i; ?>" class="invoice_line_total" type="text" name="line_total" value="<?php echo $item['sub']; ?>" placeholder="<?php _e('Subtotal', 'cqpim'); ?>" readonly /></td>
										<?php if(!empty($tax_applicable)) { ?>
											<td style="width:7.5%"><span class="cqpim_mobile"><?php printf(__('Exclude %1$s', 'cqpim'), $tax_name); ?></span> <input data-row="<?php echo $i; ?>" id="invoice_line_tax_<?php echo $i; ?>" name="line_tax" class="line_tax" value="1" style="width:auto" type="checkbox" <?php if(!empty($item['tax_ex']) && $item['tax_ex'] == 1) { echo 'checked="checked"'; } ?> /></td>
										<?php } ?>
										<?php if(!empty($stax_applicable)) { ?>
											<td style="width:7.5%"><span class="cqpim_mobile"><?php printf(__('Exclude %1$s', 'cqpim'), $stax_name); ?></span> <input data-row="<?php echo $i; ?>" id="invoice_line_stax_<?php echo $i; ?>" name="line_stax" class="line_stax" value="1" style="width:auto" type="checkbox" <?php if(!empty($item['stax_ex']) && $item['stax_ex'] == 1) { echo 'checked="checked"'; } ?> /></td>		
										<?php } ?>
										<td style="padding:0; text-align:center; width:<?php echo $width; ?>%"><input data-row="<?php echo $i; ?>" class="line_delete cqpim_button cqpim_small_button bg-red rounded_2 border-red op" data-repeater-delete type="button" value=""/></td>
									</tr>
								</tbody>
							</table>
						</div>
				<?php $i++;
					} 
				} else { ?>
					<div class="line_item" data-repeater-item>
						<table style="table-layout: fixed" class="cqpim_table invoice-items">
							<tbody>
								<tr>
									<td style="width:10%"><input data-row="0" id="invoice_qty" class="invoice_qty" type="text" name="qty" value="" placeholder="<?php _e('Quantity', 'cqpim'); ?>" required /></td>
									<td style="width:45%"><input data-row="0" id="invoice_desc" class="invoice_desc" type="text" name="desc" value="" placeholder="<?php _e('Description', 'cqpim'); ?>" required /></td>
									<td style="width:12.5%"><input data-row="0" id="invoice_price" class="invoice_price" type="text" name="price" value="" placeholder="<?php _e('Price', 'cqpim'); ?> (<?php echo pto_calculate_currency($post->ID); ?>)" required /></td>
									<td style="width:12.5%"><input data-row="0" id="invoice_line_total" class="invoice_line_total" type="text" name="line_total" value="" placeholder="<?php _e('Subtotal', 'cqpim'); ?>" readonly required /></td>
									<?php if(!empty($tax_applicable)) { ?>
										<td style="width:7.5%"><input data-row="0" id="invoice_line_tax" name="line_tax" class="line_tax" style="width:auto" type="checkbox" value="1" /></td>
									<?php } ?>
									<?php if(!empty($stax_applicable)) { ?>
										<td style="width:7.5%"><input data-row="0" id="invoice_line_stax" name="line_stax" class="line_stax" style="width:auto" type="checkbox" value="1" /></td>		
									<?php } ?>
									<td style="padding:0; text-align:center; width:<?php echo $width; ?>%"><input data-row="0" class="line_delete cqpim_button cqpim_small_button bg-red rounded_2 border-red op" data-repeater-delete type="button" value=""/></td>
								</tr>
							</tbody>
						</table>
					</div>
				<?php } ?>
		</div>
		<input class="add_line_item_row cqpim_button bg-green rounded_2 border-green op" data-repeater-create type="button" value=""/>
	</div>
	<p class="underline"><?php _e('Invoice Totals', 'cqpim'); ?></p>
	<div class="totals">
		<?php 
			$vat_rate = get_post_meta($post->ID, 'tax_rate', true);
			$vat_active = get_option('sales_tax_rate');
			$svat_active = get_option('secondary_sales_tax_rate');
			if(!empty($vat_rate) || pto_is_edit_page('new') && !empty($vat_active)) {
				$subtotal = __('Subtotal', 'cqpim');
			} else {
				$subtotal = __('Total', 'cqpim');
			}
			$totals = get_post_meta($post->ID, 'invoice_totals', true);
			$sub = isset($totals['sub']) ? $totals['sub'] : 0;
			$vat = isset($totals['tax']) ? $totals['tax'] : 0;
			$svat = isset($totals['stax']) ? $totals['stax'] : 0;
			$total = isset($totals['total']) ? $totals['total'] : 0;
		?>
		<p><strong><?php echo $subtotal; ?> (<?php echo pto_calculate_currency($post->ID); ?>)</strong></p>
		<input class="total_fields" type="text" name="invoice_subtotal" id="invoice_subtotal" value="<?php echo $sub; ?>" />
		<input type="hidden" id="stax_rate" name="stax_rate" value="<?php echo isset($svat_active) ? $svat_active : 0; ?>" />
		<input type="hidden" id="tax_rate" name="tax_rate" value="<?php echo isset($vat_active) ? $vat_active : 0; ?>" />
		<?php if(!empty($vat_rate) || pto_is_edit_page('new') && !empty($vat_active)) { 
				$tax_name = get_option('sales_tax_name');
				$stax_name = get_option('secondary_sales_tax_name');
				$tax_applicable = get_post_meta($post->ID, 'tax_applicable', true);
				$stax_applicable = get_post_meta($post->ID, 'stax_applicable', true);
				if(!empty($tax_applicable)) { ?>
				<p><strong><?php echo $tax_name; ?> (<?php echo pto_calculate_currency($post->ID); ?>)</strong></p>
				<input class="total_fields" type="text" name="invoice_vat" id="invoice_vat" value="<?php echo $vat; ?>" />
				<?php } ?>
				<?php if(!empty($stax_applicable)) { ?>
					<p><strong><?php echo $stax_name; ?> (<?php echo pto_calculate_currency($post->ID); ?>)</strong></p>
					<input class="total_fields" type="text" name="invoice_svat" id="invoice_svat" value="<?php echo $svat; ?>" />
				<?php } ?>
				<p><strong><?php _e('Total', 'cqpim'); ?> (<?php echo pto_calculate_currency($post->ID); ?>)</strong></p>
				<input class="total_fields" type="text" name="invoice_total" id="invoice_total" value="<?php echo $total; ?>" />
		<?php } else {
			$total = $sub;
		}
		$received = 0;
		$payments = get_post_meta($post->ID, 'invoice_payments', true);
		if(empty($payments)) {
			$payments = array();
		}
		foreach($payments as $payment) {
			$amount = isset($payment['amount']) ? $payment['amount'] : 0;
			$received = $received + $amount;
		}
		$outstanding = $total - $received;
		?>
		<p><strong><?php _e('Payments / Deductions', 'cqpim'); ?> (<?php echo pto_calculate_currency($post->ID); ?>)</strong></p>
		<input class="total_fields" type="text" name="payments_received" id="payments_received" value="<?php echo number_format((float)$received, 2, '.', ''); ?>" readonly />
		<p><strong><?php _e('Total Outstanding', 'cqpim'); ?> (<?php echo pto_calculate_currency($post->ID); ?>)</strong></p>
		<input class="total_fields" type="text" name="total_outstanding" id="total_outstanding" value="<?php echo number_format((float)$outstanding, 2, '.', ''); ?>" readonly />
	</div>
	<?php } else { ?>
		<?php _e('You must select a client before you can add line items.', 'cqpim'); ?>
	<?php } ?>
	<div class="clear"></div>
	<?php
}
add_action( 'save_post', 'save_pto_invoice_items_metabox_data' );
function save_pto_invoice_items_metabox_data( $post_id ){
	if ( ! isset( $_POST['invoice_items_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['invoice_items_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'invoice_items_metabox' ) )
	    return $post_id;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	    return $post_id;
	if ( 'page' == $_POST['post_type'] ) {
	    if ( ! current_user_can( 'edit_page', $post_id ) )
	        return $post_id;
	  	} else {
	    if ( ! current_user_can( 'edit_post', $post_id ) )
	        return $post_id;
	}
	$line_items = array();
	if(isset($_POST['group-a'])) {
		$items_to_add = $_POST['group-a'];
		$i = 0;
		foreach($items_to_add as $item) {
			$line_items[] = array(
				'qty' => $item['qty'],
				'desc' => $item['desc'],
				'price' => $item['price'],
				'sub' => $item['line_total'],
				'tax_ex' => isset($item['line_tax'][0]) ? $item['line_tax'][0] : 0,
				'stax_ex' => isset($item['line_stax'][0]) ? $item['line_stax'][0] : 0,
			);
			$i++;
		}
		update_post_meta($post_id, 'line_items', $line_items);
		$sub = isset($_POST['invoice_subtotal']) ? $_POST['invoice_subtotal'] : '';
		$tax_rate = get_option('sales_tax_rate');
		$invoice_details = get_post_meta($post_id, 'invoice_details', true);
		$client_id = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
		$client_details = get_post_meta($client_id, 'client_details', true);
		$client_tax = isset($client_details['tax_disabled']) ? $client_details['tax_disabled'] : '';
		$client_stax = isset($client_details['stax_disabled']) ? $client_details['stax_disabled'] : '';
		if(!empty($tax_rate) && empty($client_tax)) {
			$tax = 0;
			foreach($items_to_add as $item) {
				if(empty($item['line_tax'][0])) {
					$amount = $item['line_total'];
					$tax_amount = $amount / 100 * $tax_rate;
					$tax = $tax + $tax_amount;
				}
			}
			$stax_rate = get_option('secondary_sales_tax_rate');
			if(!empty($stax_rate) && empty($client_stax)) {
				$stax = 0;
				foreach($items_to_add as $item) {
					if(empty($item['line_stax'][0])) {
						$amount = $item['line_total'];
						$stax_amount = $amount / 100 * $stax_rate;
						$stax = $stax + $stax_amount;
					}
				}
				$total = $sub + $tax + $stax;
			} else {
				$stax = 0;
				$total = $sub + $tax;
			}
		} else {
			$tax = 0;
			$stax = 0;
			$total = $sub;
		}
		$invoice_totals = array(
			'sub' => number_format((float)$sub, 2, '.', ''),
			'tax' => number_format((float)$tax, 2, '.', ''),
			'stax' => number_format((float)$stax, 2, '.', ''),
			'total' => number_format((float)$total, 2, '.', '')
		);
		update_post_meta($post_id, 'invoice_totals', $invoice_totals);
	} else {
		delete_post_meta($post_id, 'line_items');
		delete_post_meta($post_id, 'invoice_totals');
	}
}