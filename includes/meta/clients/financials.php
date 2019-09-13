<?php
function pto_client_financials_metabox_callback( $post ) {
 	wp_nonce_field( 
	'client_financials_metabox', 
	'client_financials_metabox_nonce' );
	$client_details = get_post_meta($post->ID, 'client_details', true);
	$invoice_terms = isset($client_details['invoice_terms']) ? $client_details['invoice_terms']: '';
	$billing_email = isset($client_details['billing_email']) ? $client_details['billing_email']: '';
	$tax_disabled = isset($client_details['tax_disabled']) ? $client_details['tax_disabled']: '';
	$stax_disabled = isset($client_details['stax_disabled']) ? $client_details['stax_disabled']: '';
	$client_tax_reg = isset($client_details['client_tax_reg']) ? $client_details['client_tax_reg']: '';
	$client_stax_reg = isset($client_details['client_stax_reg']) ? $client_details['client_stax_reg']: '';
	$client_tax_name = isset($client_details['client_tax_name']) ? $client_details['client_tax_name']: '';
	$client_stax_name = isset($client_details['client_stax_name']) ? $client_details['client_stax_name']: '';
	$client_invoice_prefix = isset($client_details['client_invoice_prefix']) ? $client_details['client_invoice_prefix']: '';
	$currency_override = get_option('allow_client_currency_override');
	$currency = get_option('currency_symbol');
	$currency_code = get_option('currency_code');
	$currency_position = get_option('currency_symbol_position');
	$currency_space = get_option('currency_symbol_space');
	$client_currency_symbol = get_post_meta($post->ID, 'currency_symbol', true);
	$client_currency_symbol = isset($client_currency_symbol) ? $client_currency_symbol : '';
	$client_currency_code = get_post_meta($post->ID, 'currency_code', true);
	$client_currency_code = isset($client_currency_code) ? $client_currency_code : '';
	$client_currency_space = get_post_meta($post->ID, 'currency_space', true);
	$client_currency_space = isset($client_currency_space) ? $client_currency_space : '';
	$client_currency_position = get_post_meta($post->ID, 'currency_position', true);
	$client_currency_position = isset($client_currency_position) ? $client_currency_position : '';
	$system_invoice_terms = get_option('company_invoice_terms');
	$system_tax_name = get_option('sales_tax_name');
	$system_tax_rate = get_option('sales_tax_rate');
	$system_stax_name = get_option('secondary_sales_tax_name');
	$system_stax_rate = get_option('secondary_sales_tax_rate');
	?>
	<div class="cqpim-meta-left">
		<p class="sbold"><?php _e('Billing Email Address', 'cqpim'); ?> <i class="fa fa-question-circle tooltip" aria-hidden="true" title="<?php _e('If you would like to override the email address that invoices are sent to for this client, enter one here.', 'cqpim'); ?>"></i></p>
		<input type="text" name="billing_email_address" value="<?php echo $billing_email; ?>" placeholder="<?php _e('Billing Email Address', 'cqpim'); ?>" /><br />

		<p class="sbold"><?php _e('Invoice Prefix', 'cqpim'); ?> <i class="fa fa-question-circle tooltip" aria-hidden="true" title="<?php _e('If you want to override the system invoice prefix for this client, enter one here.', 'cqpim'); ?>"></i></p>
		<input type="text" name="client_invoice_prefix" value="<?php echo $client_invoice_prefix; ?>" placeholder="<?php _e('Invoice prefix', 'cqpim'); ?>" /><br />
		<p class="sbold"><?php _e('Client Tax Details', 'cqpim'); ?> <i class="fa fa-question-circle tooltip" aria-hidden="true" title="<?php _e('If you want to show your client\'s tax registration numbers on invoices, you can enter them here.', 'cqpim'); ?>"></i></p>
		<p><?php _e('Tax 1 Name / Reg Number: ', 'cqpim'); ?> </p>
		<input type="text" name="client_tax_name" value="<?php echo $client_tax_name; ?>" placeholder="<?php _e('Tax name', 'cqpim'); ?>" /><br /><input style="border-top:0" type="text" name="client_tax_reg" value="<?php echo $client_tax_reg; ?>" placeholder="<?php _e('Reg number', 'cqpim'); ?>" /><br />
		<p><?php _e('Tax 2 Name / Reg Number: ', 'cqpim'); ?> </p>
		<input type="text" name="client_stax_name" value="<?php echo $client_stax_name; ?>" placeholder="<?php _e('Tax name', 'cqpim'); ?>" /><br /><input style="border-top:0" type="text" name="client_stax_reg" value="<?php echo $client_stax_reg; ?>" placeholder="<?php _e('Reg number', 'cqpim'); ?>" />			
		<br /><br />
		<?php if(!empty($system_tax_rate)) { ?>
			<input type="checkbox" name="tax_disabled" value="1" <?php if($tax_disabled == 1) { echo 'checked="checked"'; } ?>/> <?php printf(__('This client should NOT be charged %1$s.','cqpim'), $system_tax_name, $system_tax_rate); ?><br /><br />
		<?php } ?>
		<?php if(!empty($system_stax_rate)) { ?>
			<input type="checkbox" name="stax_disabled" value="1" <?php if($stax_disabled == 1) { echo 'checked="checked"'; } ?> <?php if($tax_disabled == 1) { echo 'checked="checked" disabled="disabled"'; } ?>/> <?php printf(__('This client should NOT be charged %1$s.','cqpim'), $system_stax_name, $system_stax_rate); ?><br /><br />
		<?php } ?>
		<label for="invoice_terms"><?php _e('Invoice Terms:', 'cqpim'); ?> </label>
		<select id="invoice_terms" name="invoice_terms">
			<option value=""><?php _e('Use Company Terms', 'cqpim')?> <?php printf(_n('(%1$s day)', '(%1$s days)', $system_invoice_terms, 'cqpim'), $system_invoice_terms); ?></option>
			<option value="1" <?php if($invoice_terms == 1) { echo 'selected'; } ?>><?php _e('Due on Receipt', 'cqpim'); ?></option>
			<option value="7" <?php if($invoice_terms == 7) { echo 'selected'; } ?>><?php _e('7 days', 'cqpim'); ?></option>
			<option value="14" <?php if($invoice_terms == 14) { echo 'selected'; } ?>><?php _e('14 days', 'cqpim'); ?></option>
			<option value="28" <?php if($invoice_terms == 28) { echo 'selected'; } ?>><?php _e('28 days', 'cqpim'); ?></option>
			<option value="30" <?php if($invoice_terms == 30) { echo 'selected'; } ?>><?php _e('30 days', 'cqpim'); ?></option>
			<option value="60" <?php if($invoice_terms == 60) { echo 'selected'; } ?>><?php _e('60 days', 'cqpim'); ?></option>
			<option value="90" <?php if($invoice_terms == 90) { echo 'selected'; } ?>><?php _e('90 days', 'cqpim'); ?></option>
		</select>
	</div>
	<?php if($currency_override == 1) { ?>
		<div class="cqpim-meta-right">
			<p class="sbold"><?php _e('Currency Override', 'cqpim'); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php _e('If you would like to override the system currency settings for this client you can do so here. The settings here will take precedence over system settings and will be applied to any quote/estimate, project and invoice that is assigned to this client, unless those are overriden manually.', 'cqpim'); ?>"></i></p>
			<div class="cqpim-alert cqpim-alert-info alert-display">
				<strong><?php _e('System Currency Settings', 'cqpim'); ?></strong><br />
				<?php _e('Currency Symbol:', 'cqpim'); ?> <?php echo $currency; ?><br />
				<?php _e('Currency Code:', 'cqpim'); ?> <?php echo $currency_code; ?><br />
				<?php _e('Currency Position:', 'cqpim'); ?> <?php if($currency_position == 'l') { _e('Before Amount', 'cqpim'); } else { _e('After Amount', 'cqpim'); } ?><br />
				<?php _e('Currency Space:', 'cqpim'); ?> <?php if($currency_space == '1') { _e('Yes', 'cqpim'); } else { _e('No', 'cqpim'); } ?>
			</div>
			<table class="milestones">
				<tr>
					<td>
						<?php _e('Client Currency Symbol:', 'cqpim'); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="Leave blank to use system currency symbol"></i>
					</td>
					<td>
						<input style="width:100px" type="text" name="currency_symbol" value="<?php echo $client_currency_symbol; ?>" />
					</td>
				</tr>
				<tr>
					<td>
						<?php _e('Client Currency Code:', 'cqpim'); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="Leave blank to use system currency symbol"></i>		
					</td>
					<td>
						<select name="currency_code" id="currency_code">
							<option value="0"><?php _e('Choose a currency', 'cqpim'); ?></option>
							<?php $codes = pto_return_currency_select();
							foreach($codes as $key => $code) {
								if($key == $client_currency_code) { $checked = 'selected="selected"'; } else { $checked = ''; };
								echo '<option value="' . $key . '" ' . $checked . '>' . $code . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<?php _e('Client Currency Symbol Position: ', 'cqpim'); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="Leave blank to use system currency symbol"></i>
					</td>
					<td>
						<select name="currency_position">
							<option value=""><?php _e('Choose...', 'cqpim'); ?></option>
							<option value="l" <?php if($client_currency_position == 'l') { echo 'selected'; } ?>><?php _e('Before Amount', 'cqpim'); ?></option>
							<option value="r" <?php if($client_currency_position == 'r') { echo 'selected'; } ?>><?php _e('After Amount', 'cqpim'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<?php _e('Add a space between the currency symbol and amount.', 'cqpim'); ?>
					</td>
					<td>
						<input type="checkbox" id="currency_space" name="currency_space" value="1" <?php if($client_currency_space == '1') { echo 'checked'; } ?> />
					</td>
				</tr>
			</table>
		</div>
	<?php }
	echo '<div class="clear"></div>';
}
add_action( 'save_post', 'save_pto_client_financials_metabox_data' );
function save_pto_client_financials_metabox_data( $post_id ){
	if ( ! isset( $_POST['client_financials_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['client_financials_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'client_financials_metabox' ) )
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
	$currency_symbol = isset($_POST['currency_symbol']) ? $_POST['currency_symbol'] : '';
	$currency_code = isset($_POST['currency_code']) ? $_POST['currency_code'] : '';
	$currency_space = isset($_POST['currency_space']) ? $_POST['currency_space'] : '';
	$currency_position = isset($_POST['currency_position']) ? $_POST['currency_position'] : '';
	update_post_meta($post_id, 'currency_symbol', $currency_symbol);
	update_post_meta($post_id, 'currency_code', $currency_code);
	update_post_meta($post_id, 'currency_space', $currency_space);
	update_post_meta($post_id, 'currency_position', $currency_position);
	if(isset($_POST['invoice_terms'])) {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$invoice_terms = $_POST['invoice_terms'];
		$client_details['invoice_terms'] = $invoice_terms;
		update_post_meta($post_id, 'client_details', $client_details);
	}	
	if(isset($_POST['client_invoice_prefix'])) {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$invoice_terms = $_POST['client_invoice_prefix'];
		$client_details['client_invoice_prefix'] = $invoice_terms;
		update_post_meta($post_id, 'client_details', $client_details);
	}
	if(isset($_POST['billing_email_address'])) {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$billing_email = $_POST['billing_email_address'];
		$client_details['billing_email'] = $billing_email;
		update_post_meta($post_id, 'client_details', $client_details);
	}
	$client_details = get_post_meta($post_id, 'client_details', true);
	$client_details['client_tax_name'] = isset($_POST['client_tax_name']) ? $_POST['client_tax_name'] : '';
	$client_details['client_stax_name'] = isset($_POST['client_stax_name']) ? $_POST['client_stax_name'] : '';
	$client_details['client_tax_reg'] = isset($_POST['client_tax_reg']) ? $_POST['client_tax_reg'] : '';
	$client_details['client_stax_reg'] = isset($_POST['client_stax_reg']) ? $_POST['client_stax_reg'] : '';
	update_post_meta($post_id, 'client_details', $client_details);	
	if(isset($_POST['tax_disabled'])) {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$tax_disabled = $_POST['tax_disabled'];
		$client_details['tax_disabled'] = $tax_disabled;
		update_post_meta($post_id, 'client_details', $client_details);	
	} else {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$client_details['tax_disabled'] = 0;
		update_post_meta($post_id, 'client_details', $client_details);
	}
	if(isset($_POST['stax_disabled'])) {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$stax_disabled = $_POST['stax_disabled'];
		$client_details['stax_disabled'] = $stax_disabled;
		update_post_meta($post_id, 'client_details', $client_details);	
	} else {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$client_details['stax_disabled'] = 0;
		update_post_meta($post_id, 'client_details', $client_details);
	}
}