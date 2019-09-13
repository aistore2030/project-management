<?php
function pto_invoice_payments_metabox_callback( $post ) {
 	wp_nonce_field( 
	'invoice_payments_metabox', 
	'invoice_payments_metabox_nonce' ); 
	$invoice_payments = get_post_meta($post->ID, 'invoice_payments', true);
	if(!empty($invoice_payments)) {
		echo '<table class="cqpim_table">';
		echo '<thead>';
		echo '<tr><th>' . __('Payment Date', 'cqpim') . '</th><th>' . __('Payment Amount', 'cqpim') . '</th><th>' . __('By', 'cqpim') . '</th><th>' . __('Notes', 'cqpim') . '</th><th>' . __('Actions', 'cqpim') . '</th></tr>';
		echo '</thead>';
		echo '<tbody>';
		foreach($invoice_payments as $key => $payment) {
			$notes = isset($payment['notes']) ? $payment['notes'] : '';
			echo '<tr>';
			echo '<td><span class="cqpim_mobile">' . __('Date:', 'cqpim') . '</span> ' . date(get_option('cqpim_date_format'), $payment['date']) . '</td>';
			echo '<td><span class="cqpim_mobile">' . __('Amount:', 'cqpim') . '</span> ' . get_option('currency_symbol') . $payment['amount'] . '</td>';
			echo '<td><span class="cqpim_mobile">' . __('By:', 'cqpim') . '</span> ' . $payment['by'] . '</td>';
			echo '<td><span class="cqpim_mobile">' . __('Notes:', 'cqpim') . '</span> ' . $notes . '</td>';
			echo '<td>'; ?> 
				<button class="edit-milestone cqpim_button cqpim_small_button font-amber border-amber op cqpim_tooltip" value="<?php echo $key; ?>" title="<?php _e('Edit Payment', 'cqpim'); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button> <button class="delete_stage_conf cqpim_button cqpim_small_button font-red border-red op cqpim_tooltip" data-id="<?php echo $key; ?>" value="<?php echo $key; ?>" title="<?php _e('Delete Payment', 'cqpim'); ?>"><i class="fa fa-trash"></i></button>		
			<?php echo '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';		
	} else {
		_e('No payments made on this invoice', 'cqpim');
	}
	if(!empty($invoice_payments)) {
		foreach($invoice_payments as $key => $payment) {
		?>
			<div id="invoice_payment_container_<?php echo $key; ?>" style="display:none">
				<div id="invoice_payment_<?php echo $key; ?>" class="invoice_payment_edit">
					<div style="padding:12px">
						<h3><?php _e('Edit Payment', 'cqpim'); ?></h3>
						<p><?php _e('Payment Amount:', 'cqpim'); ?></p>
						<input style="width:270px" type="text" id="payment_amount_<?php echo $key; ?>" value="<?php echo get_option('currency_symbol'); ?><?php echo $payment['amount']; ?>" />
						<p><?php _e('Payment Date:', 'cqpim'); ?></p>
						<input style="width:270px" class="datepicker" type="text" id="payment_date_<?php echo $key; ?>" value="<?php echo date(get_option('cqpim_date_format'), $payment['date']); ?>" />
						<p><?php _e('Payment Notes:', 'cqpim'); ?></p>
						<textarea style="width:400px; height:150px" id="payment_notes_<?php echo $key; ?>"><?php echo isset($payment['notes']) ? $payment['notes'] : ''; ?></textarea>
						<div class="clear"></div>
						<?php echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="edit_paid" data-key="' . $key . '" data-id="' . $post->ID . '">' . __('Save Payment', 'cqpim') . '</button><div style="display:none" class="ajax_spinner"></div>'; ?>								
						<div class="clear"></div>
						<br />
					</div>
				</div>
			</div>		
		<?php
		}
	}
}