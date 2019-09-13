<?php 
	include('header.php');
	$_SESSION['last_invoice'] = $post->ID;
	$user = wp_get_current_user(); 
	$user_id = $user->ID;
	$inv_logo = get_option('cqpim_invoice_logo');
	$logo = get_option('company_logo');
	$logo_url = isset($logo['company_logo']) ? $logo['company_logo'] : '';
	$title = get_the_title();
	$title = str_replace('Protected: ', '', $title);
	$company_name = get_option('company_name');
	$company_address = get_option('company_address');
	$company_number = get_option('company_number');
	$company_address = str_replace(',', '<br />', $company_address);
	$company_postcode = get_option('company_postcode');
	$company_telephone = get_option('company_telephone');
	$company_accounts_email = get_option('company_accounts_email');
	$currency = get_option('currency_symbol');
	$vat_rate = get_post_meta($post->ID, 'tax_rate', true);
	$svat_rate = get_post_meta($post->ID, 'stax_rate', true);
	$tax_name = get_option('sales_tax_name');
	$tax_reg = get_option('sales_tax_reg');
	$stax_name = get_option('secondary_sales_tax_name');
	$stax_reg = get_option('secondary_sales_tax_reg');
	$invoice_terms = get_option('company_invoice_terms');
	if($vat_rate) {
		$vat_string = '';
	} else {
		$vat_string = '';
	} 
	$invoice_details = get_post_meta($post->ID, 'invoice_details', true);
	$invoice_payments = get_post_meta($post->ID, 'invoice_payments', true);
	if(empty($invoice_payments)) {
		$invoice_payments = array();
	}
	$received = 0;
	foreach($invoice_payments as $payment) {
		$amount = isset($payment['amount']) ? $payment['amount'] : 0;
		$received = $received + $amount;
	}
	$invoice_id = get_post_meta($post->ID, 'invoice_id', true);
	$client_contact = get_post_meta($post->ID, 'client_contact', true);
	$owner = get_user_by('id', $client_contact);
	$project_id = isset($invoice_details['project_id']) ? $invoice_details['project_id'] : '';
	$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
	$invoice_date_stamp = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
	$allow_partial = isset($invoice_details['allow_partial']) ? $invoice_details['allow_partial'] : '';
	if(is_numeric($invoice_date)) { $invoice_date = date(get_option('cqpim_date_format'), $invoice_date); } else { $invoice_date = $invoice_date; }
	$deposit = isset($invoice_details['deposit']) ? $invoice_details['deposit'] : '';
	$due = isset($invoice_details['due']) ? $invoice_details['due'] : '';
	if(is_numeric($due)) { $due = date(get_option('cqpim_date_format'), $due); } else { $due = $due; }
	if(!empty($project_id)) {
		$project_details = get_post_meta($project_id, 'project_details', true);
		$project_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
	} else {
		$project_ref = __('N/A', 'cqpim');
	}
	$client_id = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_ids = get_post_meta($client_id, 'client_ids', true);
	$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
	$client_company = isset($client_details['client_company']) ? $client_details['client_company'] : '';
	$client_address = isset($client_details['client_address']) ? $client_details['client_address'] : '';
	$client_postcode = isset($client_details['client_postcode']) ? $client_details['client_postcode'] : '';
	$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
	$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
	$client_tax_name = isset($client_details['client_tax_name']) ? $client_details['client_tax_name']: '';
	$client_stax_name = isset($client_details['client_stax_name']) ? $client_details['client_stax_name']: '';
	$client_tax_reg = isset($client_details['client_tax_reg']) ? $client_details['client_tax_reg']: '';
	$client_stax_reg = isset($client_details['client_stax_reg']) ? $client_details['client_stax_reg']: '';
	$client_invoice_prefix = isset($client_details['client_invoice_prefix']) ? $client_details['client_invoice_prefix']: '';
	$system_invoice_prefix = get_option('cqpim_invoice_prefix');
	$line_items = get_post_meta($post->ID, 'line_items', true);
	$totals = get_post_meta($post->ID, 'invoice_totals', true);
	$sub = isset($totals['sub']) ? $totals['sub'] : '';
	$vat = isset($totals['tax']) ? $totals['tax'] : '';
	$svat = isset($totals['stax']) ? $totals['stax'] : '';
	$total = isset($totals['total']) ? $totals['total'] : '';
	$invoice_footer = get_option('client_invoice_footer');
	$invoice_footer = pto_replacement_patterns($invoice_footer, $post->ID, 'invoice');
	$terms_over = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
	$on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';
	$pass = isset($_GET['pwd']) ? $_GET['pwd'] : '';
	$looper = get_post_meta($post->ID, 'looper', true);
	$looper = $looper?$looper:0;
	if(time() - $looper > 5 && in_array('cqpim_client', $user->roles)) {
		$user = wp_get_current_user();
		$client_logs = get_post_meta($client_id, 'client_logs', true);
		if(empty($client_logs)) {
			$client_logs = array();
		}
		$now = current_time('timestamp');
		$client_logs[$now] = array(
			'user' => $user->ID,
			'page' => sprintf(__('Invoice - %1$s', 'cqpim'), $title)
		);
		update_post_meta($client_id, 'client_logs', $client_logs);
		update_post_meta($post->ID, 'looper', time());
	}
if($assigned == $client_id) { ?>
	<div class="masonry-grid">
		<div class="grid-sizer"></div>
		<div class="cqpim-dash-item-triple grid-item">
			<div class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<span class="caption-subject font-green-sharp sbold"><?php _e('Line Items', 'cqpim'); ?></span>
					</div>
				</div>
				<br />
				<table class="cqpim_table">
					<thead>
						<tr>
							<th><?php _e('Qty', 'cqpim'); ?></th>
							<th><?php _e('Description', 'cqpim'); ?></th>
							<th><?php _e('Rate', 'cqpim'); ?></th>
							<th><?php _e('Total', 'cqpim'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					if(empty($line_items)) {
						$line_items = array();
					}				
					foreach($line_items as $item) { ?>
						<tr>
							<td><?php echo $item['qty']; ?></td>
							<td><?php echo $item['desc']; ?></td>
							<td><?php echo pto_calculate_currency($post->ID, $item['price']); ?></td>
							<td><?php echo pto_calculate_currency($post->ID, $item['sub']); ?></td>
						</tr>
					<?php } ?>
						<tr>
							<td colspan="3"><?php if($vat_rate) { ?><?php _e('Subtotal:', 'cqpim'); ?><?php } else { ?><?php _e('TOTAL:', 'cqpim'); ?><?php } ?></td>
							<td><?php echo pto_calculate_currency($post->ID, $sub); ?></td>
						</tr>
					<?php 
					$outstanding = $sub;
					if($vat_rate) { 
						$outstanding = $total;
						$tax_name = get_option('sales_tax_name'); ?>
						<tr>
							<td colspan="3"><?php echo $tax_name; ?>:</td>
							<td><?php echo pto_calculate_currency($post->ID, $vat); ?></td>
						</tr>
						<?php if(!empty($svat_rate)) { ?>
							<tr>
							<td colspan="3"><?php echo $stax_name; ?>:</td>
							<td><?php echo pto_calculate_currency($post->ID, $svat); ?></td>
							</tr>					
						<?php } ?>
						<tr>
							<td colspan="3"><?php _e('TOTAL:', 'cqpim'); ?></td>
							<td><?php echo pto_calculate_currency($post->ID, $total); ?></td>
						</tr>
					<?php } ?>
						<tr>
							<td colspan="3"><?php _e('Received:', 'cqpim'); ?></td>
							<td><?php echo pto_calculate_currency($post->ID, $received); ?></td>
						</tr>
						<tr>
							<td colspan="3"><?php _e('Outstanding:', 'cqpim'); ?></td>
							<?php $outstanding = $outstanding - $received; ?>
							<td><?php echo pto_calculate_currency($post->ID, $outstanding); ?></td>
						</tr>
					</tbody>
				</table>
				<br />
				<?php echo wpautop($invoice_footer); ?>
				<div class="clear"></div>							
			</div>
		</div>
		<div class="cqpim-dash-item-double grid-item">
			<div class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<span class="caption-subject font-green-sharp sbold"><?php _e('Invoice Details', 'cqpim'); ?> </span>
					</div>
				</div>
				<?php $now = time();
				if(empty($on_receipt)) {
					if(empty($invoice_details['paid'])) {
						if($terms_over) {
							if($now > $terms_over) {
								echo '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('THIS INVOICE IS OVERDUE', 'cqpim') . '</div>';		
							}
						}
					}
				} 
				if(!empty($invoice_details['paid'])) {
					echo '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Invoice Paid', 'cqpim') . '</div>';		
				}
				?>
				<span class="invoice_to">
					<?php if($company_number) { ?>
						<p><span><?php _e('Company Reg Number:', 'cqpim'); ?></span> <span><?php echo $company_number; ?></span></p>
					<?php } ?>
					<p><span><?php _e('Invoice Number:', 'cqpim'); ?></span> <span>							
						<?php if(!empty($client_invoice_prefix)) {
							echo $client_invoice_prefix . $invoice_id; 
						} else if(empty($client_invoice_prefix) && !empty($system_invoice_prefix)) {
							echo $system_invoice_prefix . $invoice_id;
						} else {
							echo $invoice_id;
						} ?>
					</span></p>
					<?php if($vat_rate) { ?>
						<p><span><?php echo get_option('sales_tax_name'); ?> <?php _e('Number', 'cqpim'); ?>:</span> <span><?php echo $tax_reg; ?></span></p>					
					<?php } ?>						
					<?php if($svat_rate) { ?>
					<p><span><?php echo get_option('secondary_sales_tax_name'); ?> <?php _e('Number', 'cqpim'); ?>:</span> <span><?php echo $stax_reg; ?></span></p>						
						<?php } ?>
					<p><span><?php _e('Invoice #:', 'cqpim'); ?></span> <span><?php echo $invoice_id; ?></span></p>
					<p><span><?php _e('Invoice Date:', 'cqpim'); ?></span> <span><?php echo $invoice_date; ?></span></p>
					<p><span><?php _e('Due Date:', 'cqpim'); ?></span> <span>
						<?php if(empty($on_receipt)) { ?>							
							<?php if($due) { echo $due; } else { ?>
								<?php $due_date = strtotime('+ ' . $invoice_terms . ' days', $invoice_date_stamp);
									echo date(get_option('cqpim_date_format'), $due_date); ?>
							<?php } ?>						
						<?php } else { ?>
							<?php _e('Due on Receipt', 'cqpim'); ?>
						<?php } ?>						
					</span></p>
					<p><span><?php _e('Project:', 'cqpim'); ?></span> <span><?php echo $project_ref; ?></span></p>
				</span>
				<span class="invoice_to">
					<p><strong><?php _e('INVOICE TO:', 'cqpim'); ?></strong></p>
					<?php if(!empty($owner)) { echo $owner->display_name . ' - '; } ?>
					<?php echo $client_company; ?> <br />
					<p><?php echo $client_address; ?> </p>
					<?php echo wpautop($client_postcode); ?>
					<?php if(!empty($client_tax_name)) { ?>
						<?php if(!empty($client_tax_name)) { ?>
							<p><span><?php printf(__('%1$s Reg Number: ', 'cqpim'), $client_tax_name); ?></span> <?php echo $client_tax_reg; ?></p>
						<?php } ?>
						<?php if(!empty($client_stax_name)) { ?>
							<p><span><?php printf(__('%1$s Reg Number: ', 'cqpim'), $client_stax_name); ?></span> <?php echo $client_stax_reg; ?></p>
						<?php } ?>
					<?php } ?>
				</span>
				<div class="clear"></div>
				<div>
					<?php
					$data = get_option('cqpim_custom_fields_invoice');
					$data = str_replace('\"', '"', $data);
					if(!empty($data)) {
						$form_data = json_decode($data);
						$fields = $form_data;
					}
					$values = get_post_meta($post->ID, 'custom_fields', true);
					if(!empty($fields)) {
						echo '<div style="border-top:1px solid #eef1f5; border-bottom:1px solid #eef1f5" id="cqpim-custom-fields">';
						foreach($fields as $field) {
							$value = isset($values[$field->name]) ? $values[$field->name] : '';
							$id = strtolower($field->label);
							$id = str_replace(' ', '_', $id);
							$id = str_replace('-', '_', $id);
							$id = preg_replace('/[^\w-]/', '', $id);
							echo '<div class="cqpim_form_item">';
							if($field->type != 'header') {
								echo '<p><span>' . $field->label . ': </span>';
							}
							if($field->type == 'header') {
								echo '<' . $field->subtype . ' class="cqpim-custom ' . $field->className . '">' . $field->label . '</' . $field->subtype . '>';
							} elseif($field->type == 'text') {	
								echo '<span>' . $value . '</span></p>';
							} elseif($field->type == 'website') {
								echo '<span>' . $value . '</span></p>';
							} elseif($field->type == 'number') {
								echo '<span>' . $value . '</span></p>';
							} elseif($field->type == 'textarea') {
								echo '<span>' . $value . '</span></p>';
							} elseif($field->type == 'date') {
								echo '<span>' . $value . '</span></p>';
							} elseif($field->type == 'email') {
								echo '<span>' . $value . '</span></p>';
							} elseif($field->type == 'checkbox-group') {
								$options = $field->values;
								foreach($options as $option) {
									if(!empty($option->selected)) {
										echo '<span>' . $option->label . '</span></p>';
									}
								}
							} elseif($field->type == 'radio-group') {
								$options = $field->values;
								foreach($options as $option) {
									if(!empty($option->selected)) {
										echo '<span>' . $option->label . '</span></p>';
									}
								}
							} elseif($field->type == 'select') {
								$options = $field->values;
								foreach($options as $option) {
									if(!empty($option->selected)) {
										echo '<span>' . $option->label . '</span></p>';
									}
								}
							}
							echo '</div>';
						}
						echo '</div>';
					}
					?>
					<div class="clear"></div>
				</div>
				<div>
					<?php if(empty($invoice_details['paid'])) { 
						if(!empty($_GET['atp'])) {
							$_SESSION['payment_amount_' . $post->ID] = $_GET['atp'];
						} else {
							$_SESSION['payment_amount_' . $post->ID] = number_format((float)$outstanding, 2, '.', '');
						}
						$stripe = get_option('client_invoice_stripe_key');
						$ideal = get_option('client_invoice_stripe_ideal');
						$paypal = get_option('client_invoice_paypal_address');
						if(function_exists('pto_twocheck_return_sid')) {
							$twocheck = pto_twocheck_return_sid();
						}
						$vat = get_option('sales_tax_rate');
						if(empty($vat)) {
							$total = $sub;
						}
						$partial = get_option('client_invoice_allow_partial');
						$user = wp_get_current_user();
						$return = get_option('cqpim_client_page');
						$return = get_the_permalink($return);
							if(!empty($stripe) && !empty($user->ID) || !empty($twocheck) && !empty($user->ID) || !empty($paypal) && !empty($user->ID)) {	
								echo '<div id="stripe-pay" style="display:none">';
									if(!empty($allow_partial)) {
										echo '<div id="payment-amount">';
										echo '<span style="font-weight:bold">' . __('Amount to pay', 'cqpim') . ' (' . pto_calculate_currency($post->ID) . '): </span> ';
										echo '<input style="padding:5px; width:100px; margin:0 10px;" type="text" id="amount_to_pay" value="' . $_SESSION['payment_amount_' . $post->ID] . '" />';
										echo ' <button class="cqpim_button cqpim_small_button rounded_2 bg-blue font-white" id="save_amount">' . __('Update', 'cqpim') . '</button><span id="amount_spinner" class="ajax_spinner" style="display:none"></span><br />';
										echo '</div>';							
									}	
									echo '<button style="font-size:14px" class="cqpim_button cqpim_button_link font-white bg-blue block mt-10 rounded_2 block" id="cqpim_pay_now">' . sprintf(__('Pay %1$s Now', 'cqpim'), pto_calculate_currency($post->ID, $_SESSION['payment_amount_' . $post->ID])) . '</button>';
								echo '</div>'; ?>
								<div id="cqpim_payment_methods_container" style="display:none">
									<div id="cqpim_payment_methods" style="padding:10px">
										<h3><?php _e('Payment Methods', 'cqpim'); ?></h3>
										<ul>
											<?php if(!empty($paypal)) { ?>
												<li>
													<form action="https://www.paypal.com/cgi-bin/webscr" method="post" class="paypal">
														<input type="hidden" name="cmd" value="_xclick">
														<input type="hidden" name="business" value="<?php echo get_option('client_invoice_paypal_address'); ?>">
														<input type="hidden" name="item_name" value="<?php echo get_option('company_name'); ?> - <?php _e('Invoice', 'cqpim'); ?> #<?php echo $invoice_id ?>">
														<input type="hidden" id="paypal-amount" name="amount" value="<?php echo $_SESSION['payment_amount_' . $post->ID]; ?>">
														<input type="hidden" name="quantity" value="1">
														<input type="hidden" name="currency_code" value="<?php echo get_post_meta($post->ID, 'currency_code', true); ?>">
														<input type="hidden" name="first_name" value="">
														<input type="hidden" name="no_shipping" value="1">
														<input type="hidden" name="rm" value="2">
														<input type="hidden" name="return" value="<?php echo $return; ?>">
														<input type="hidden" name="cancel_return" value="<?php echo $return; ?>">
														<input type="hidden" name="notify_url" value="<?php echo $return; ?>">
														<input style="max-width:100%" type="image" src="<?php echo PTO_PLUGIN_URL . '/img/ec-button.png'; ?>" name="submit" alt="<?php _e('Pay with Paypal', 'cqpim'); ?>">
													</form>										
												</li>
											<?php } ?>
											<?php if(!empty($stripe)) { ?>
												<li>
													<form action="<?php echo $return; ?>" method="POST">
													  <script
														src="https://checkout.stripe.com/checkout.js" class="stripe-button"
														data-zip-code="true"
														data-email="<?php echo $client_email; ?>"
														data-label="<?php _e('Pay with Stripe', 'cqpim'); ?>"
														data-currency="<?php echo get_post_meta($post->ID, 'currency_code', true); ?>"
														data-allow-remember-me="false"
														data-key="<?php echo get_option('client_invoice_stripe_key'); ?>"
														data-name="<?php echo get_option('company_name'); ?>"
														data-description="<?php _e('Invoice', 'cqpim'); ?> #<?php echo $invoice_id; ?>"
														data-amount="<?php echo $_SESSION['payment_amount_' . $post->ID] * 100; ?>">
														</script>
													</form>										
												</li>
											<?php } ?>
											<?php if(!empty($ideal) && !empty($stripe)) { ?>
												<li>
													<br />
													<img style="max-width:60px" src="<?php echo PTO_PLUGIN_URL . '/img/ideal_logo.png'; ?>" id="ideal_trigger" />													
												</li>
											<?php } ?>
											<?php if(!empty($twocheck)) { ?>
												<li>
													<?php
														if(function_exists('pto_twocheck_return_button')) {
															$name = __('Invoice', 'cqpim') . ' #' . $invoice_id;
															$price = $_SESSION['payment_amount_' . $post->ID];
															echo pto_twocheck_return_button($post->ID, $name, $price, $return, $client_email); 
														}
													?>
												</li>
											<?php } ?>
										</ul>
									</div>
								</div>
							<?php }	
					} ?>
					<div class="clear"></div>
				</div>
				<a class="cqpim_button cqpim_button_link font-white bg-blue block mt-10 rounded_2 block" href="<?php echo get_the_permalink(); ?>?page=print" target="_blank"><?php _e('View Printable Invoice', 'cqpim') ; ?></a>
			</div>
		</div>		
	</div>
	<?php if(!empty($ideal)) { 
		$user = wp_get_current_user(); ?>
		<div id="cqpim_payment_ideal_container" style="display:none">
			<div id="cqpim_payment_ideal" style="padding:10px">			
				<div id="ideal_form">
					<?php 
					$return = get_option('cqpim_client_page');
					$return = get_the_permalink($return);							
					?>
					<br />
					<form id="payment-form">
						<div class="form-row">
							<label for="name">
								<?php _e('Name', 'cqpim'); ?>
							</label>
							<input type="text" name="ideal_name" value="<?php echo $user->display_name; ?>">
						</div>
						<br />
						<div class="form-row">
							<label for="ideal-bank-element">
								<?php _e('iDEAL Bank', 'cqpim'); ?>
							</label>
							<br />
							<div id="ideal-bank-element"></div>
						</div>
						<br />
						<input type="hidden" name="ideal_amount" value="<?php echo $_SESSION['payment_amount_' . $post->ID] * 100; ?>" />
						<input type="hidden" name="ideal_return" value="<?php echo $return; ?>" />
						<input type="hidden" name="ideal_descriptor" value="<?php _e('Invoice', 'cqpim'); ?> #<?php echo $invoice_id; ?>" />
						<button class="cqpim_button bg-blue font-white rounded_4 op"><?php _e('Submit Payment', 'cqpim'); ?></button>
						<div id="ideal-error-message" role="alert"></div>
					</form>
				</div>				
			</div>
		</div>
	<?php } ?>
<?php } else {
	echo '<h1 style="margin-top:0">' . __('ACCESS DENIED', 'cqpim') . '</h1>';
}
?>
<?php include('footer.php'); ?>