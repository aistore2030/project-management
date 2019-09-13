<!DOCTYPE html>
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html class="ie ie9" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php wp_title(); ?></title>   
    <?php wp_head(); ?>
	<?php echo '<style>' . get_option('cqpim_dash_css') . '</style>'; ?>
</head>
<body <?php body_class(); ?> style="background:#fff">
	<div style="background: #fff;margin: 0px auto;width: 760px;padding: 20px;text-align: left" id="content" role="main">
		<?php
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
		$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
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
		if(current_user_can( 'edit_cqpim_invoices' ) OR $client_user_id == $user_id OR $pass == md5($post->post_password) OR in_array($user->ID, $client_ids)) { ?>
			<div style="width:350px; margin:0" class="quote_logo">
					<?php if(!empty($inv_logo['cqpim_invoice_logo'])) { ?>
						<img style="max-width:100%" src="<?php echo isset($inv_logo['cqpim_invoice_logo']) ? $inv_logo['cqpim_invoice_logo'] :''; ?>" />
					<?php } else { ?>
						<img style="max-width:100%" src="<?php echo isset($logo['company_logo']) ? $logo['company_logo'] : ''; ?>" />
					<?php } ?>
			</div>
			<div style="margin:0; padding:0;font-size:30px" class="quote_contacts">
				<?php _e('INVOICE', 'cqpim'); ?>
			</div>
			<div class="clear"></div>
			<?php
			$now = time();
			if(empty($on_receipt)) {
				if(empty($invoice_details['paid'])) {
					if($terms_over) {
						if($now > $terms_over) {
							echo '<div style="background:#fff; text-align:center; padding:0;color:#000; font-weight:bold">' . __('THIS INVOICE IS OVERDUE', 'cqpim') . '</div><br />';		
						}
					}
				}
			}
			?>
			<?php if(!empty($invoice_details['paid']) && $invoice_details['paid'] == true) { ?>	
				<div class="contract-confirmed">
					<div style="background:#fff; text-align:center; padding:0;color:#000; font-weight:bold"><?php _e('THIS INVOICE HAS BEEN PAID', 'cqpim'); ?></div>
				</div>
				<br /><br />
			<?php } ?>
			<div>
				<div style="float:left;width:42%;background:#f9f9f9;padding:10px;text-align:left" class="invoice_company">
					<h3 style="margin:0"><?php _e('Invoice From:', 'cqpim'); ?></h3>
					<br />
						<?php echo $company_name; ?> <br />
						<?php echo wpautop($company_address); ?>
						<?php echo wpautop($company_postcode); ?>
					<p>
						<?php _e('Tel:', 'cqpim'); ?> <?php echo $company_telephone; ?><br />
						<?php _e('Email:', 'cqpim'); ?> <a href="<?php echo $company_accounts_email; ?>"><?php echo $company_accounts_email; ?></a><br />
						<?php if($company_number) { ?>
							<?php _e('Company Reg Number:', 'cqpim'); ?> <?php echo $company_number; ?>
						<?php } ?>
					</p>
				</div>
				<div style="float:right;width:42%;background:#f9f9f9;padding:10px;text-align:left" class="invoice_client">
					<h3 style="margin:0"><?php _e('Invoice To:', 'cqpim'); ?></h3>
					<br />
						<?php if(!empty($owner)) { echo __('FAO:', 'cqpim') . ' ' . $owner->display_name . '<br />'; } ?>
						<?php echo $client_company; ?> <br />
						<?php echo wpautop($client_address); ?>
						<?php echo wpautop($client_postcode); ?>
					<p>
						<?php if(!empty($owner)) { ?>
							<?php _e('Email:', 'cqpim'); ?> <a href="<?php echo $owner->user_email; ?>"><?php echo $owner->user_email; ?></a>
						<?php } else { ?>
							<?php _e('Email:', 'cqpim'); ?> <a href="<?php echo $client_email; ?>"><?php echo $client_email; ?></a>
						<?php } ?>
					</p>
					<?php if(!empty($client_tax_name)) { ?>
						<?php if(!empty($client_tax_name)) { ?>
							<?php printf(__('%1$s Reg Number: ', 'cqpim'), $client_tax_name); ?> <?php echo $client_tax_reg; ?>
						<?php } ?>
						<?php if(!empty($client_stax_name)) { ?>
							<br /><?php printf(__('%1$s Reg Number: ', 'cqpim'), $client_stax_name); ?> <?php echo $client_stax_reg; ?>
						<?php } ?>
					<?php } ?>
				</div>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
			<br />
			<div style="background:#f9f9f9;padding:10px;text-align:left">
				<?php
				$data = get_option('cqpim_custom_fields_invoice');
				$data = str_replace('\"', '"', $data);
				if(!empty($data)) {
					$form_data = json_decode($data);
					$fields = $form_data;
				}
				$values = get_post_meta($post->ID, 'custom_fields', true);
				if(!empty($fields)) {
					echo '<div id="cqpim-custom-fields">';
					foreach($fields as $field) {
						$value = isset($values[$field->name]) ? $values[$field->name] : '';
						$id = strtolower($field->label);
						$id = str_replace(' ', '_', $id);
						$id = str_replace('-', '_', $id);
						$id = preg_replace('/[^\w-]/', '', $id);
						echo '<div class="cqpim_form_item">';
						if($field->type != 'header') {
							echo '<span style="color:' . $main_colour . '; font-size:14px; font-weight:bold">' . $field->label . ': </span>';
						}
						if($field->type == 'header') {
							echo '<' . $field->subtype . ' class="cqpim-custom ' . $field->className . '">' . $field->label . '</' . $field->subtype . '>';
						} elseif($field->type == 'text') {	
							echo '<span style="font-size:12px">' . $value . '</span><br />';
						} elseif($field->type == 'website') {
							echo '<span style="font-size:12px">' . $value . '</span><br />';
						} elseif($field->type == 'number') {
							echo '<span style="font-size:12px">' . $value . '</span><br />';
						} elseif($field->type == 'textarea') {
							echo '<span style="font-size:12px">' . $value . '</span><br />';
						} elseif($field->type == 'date') {
							echo '<span style="font-size:12px">' . $value . '</span><br />';
						} elseif($field->type == 'email') {
							echo '<span style="font-size:12px">' . $value . '</span><br />';
						} elseif($field->type == 'checkbox-group') {
							$options = $field->values;
							foreach($options as $option) {
								if(!empty($option->selected)) {
									echo '<span style="font-size:12px">' . $option->label . '</span><br />';
								}
							}
						} elseif($field->type == 'radio-group') {
							$options = $field->values;
							foreach($options as $option) {
								if(!empty($option->selected)) {
									echo '<span style="font-size:12px">' . $option->label . '</span><br />';
								}
							}
						} elseif($field->type == 'select') {
							$options = $field->values;
							foreach($options as $option) {
								if(!empty($option->selected)) {
									echo '<span style="font-size:12px">' . $option->label . '</span><br />';
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
			<div class="clear"></div>
			<br />
			<table style="width:100%; border: 1px solid #fff; border-collapse:collapse" id="invoice_info">
				<thead>
					<tr style="background:#f9f9f9; border:1px solid #fff">
						<?php if($vat_rate) { ?>
						<th style="border:1px solid #fff; padding:8px"><?php echo get_option('sales_tax_name'); ?> <?php _e('Number', 'cqpim'); ?></th>
						<?php } ?>
						<?php if($svat_rate) { ?>
						<th style="border:1px solid #fff; padding:8px"><?php echo get_option('secondary_sales_tax_name'); ?> <?php _e('Number', 'cqpim'); ?></th>
						<?php } ?>
						<th style="border:1px solid #fff; padding:8px"><?php _e('Date', 'cqpim'); ?></th>
						<th style="border:1px solid #fff; padding:8px"><?php _e('Invoice #', 'cqpim'); ?></th>
						<th style="border:1px solid #fff; padding:8px"><?php _e('Due Date', 'cqpim'); ?></th>
						<th style="border:1px solid #fff; padding:8px"><?php _e('Project', 'cqpim'); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr style="border:1px solid #fff">
						<?php if($vat_rate) { ?>
						<td style="border:1px solid #fff; padding:8px"><?php echo $tax_reg; ?></td>
						<?php } ?>
						<?php if($svat_rate) { ?>
						<td style="border:1px solid #fff; padding:8px"><?php echo $stax_reg; ?></td>
						<?php } ?>
						<td style="border:1px solid #fff; padding:8px"><?php echo $invoice_date; ?></td>						
						<td style="border:1px solid #fff; padding:8px">
							<?php if(!empty($client_invoice_prefix)) {
								echo $client_invoice_prefix . $invoice_id; 
							} else if(empty($client_invoice_prefix) && !empty($system_invoice_prefix)) {
								echo $system_invoice_prefix . $invoice_id;
							} else {
								echo $invoice_id;
							} ?>					
						</td>						
							<td style="border:1px solid #fff; padding:8px">
								<?php if(empty($on_receipt)) { ?>							
									<?php if($due) { echo $due; } else { ?>
										<?php echo date(get_option('cqpim_date_format'), $due); ?>
									<?php } ?>						
								<?php } else { ?>
									<?php _e('Due on Receipt', 'cqpim'); ?>
								<?php } ?>	
							</td>
						<td style="border:1px solid #fff; padding:8px"><?php echo $project_ref; ?></td>
					</tr>
				</tbody>
			</table>
			<br />
			<table style="width:100%; border: 1px solid #fff; border-collapse:collapse;" id="invoice_items">
				<thead>
					<tr style="background:#f9f9f9">
						<th style="border:1px solid #fff; padding:8px"><?php _e('Qty', 'cqpim'); ?></th>
						<th style="border:1px solid #fff; padding:8px"><?php _e('Description', 'cqpim'); ?></th>
						<th style="border:1px solid #fff; padding:8px"><?php _e('Rate', 'cqpim'); ?></th>
						<th style="border:1px solid #fff; padding:8px"><?php _e('Total', 'cqpim'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php 
				if(empty($line_items)) {
					$line_items = array();
				}				
				foreach($line_items as $item) { ?>
					<tr>
						<td style="border:1px solid #fff; padding:8px"><?php echo $item['qty']; ?></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo $item['desc']; ?></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo pto_calculate_currency($post->ID, $item['price']); ?></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo pto_calculate_currency($post->ID, $item['sub']); ?></td>
					</tr>
				<?php } ?>
					<tr>
						<td style="border:1px solid #fff; padding:8px" class="no_border" colspan="2"></td>
						<td class="no_border" style="text-align:right; border:1px solid #fff; padding:8px"><strong><?php if($vat_rate) { ?><?php _e('Subtotal:', 'cqpim'); ?><?php } else { ?><?php _e('TOTAL:', 'cqpim'); ?><?php } ?></strong></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo pto_calculate_currency($post->ID, $sub); ?></td>
					</tr>
				<?php 
				$outstanding = $sub;
				if($vat_rate) { 
					$outstanding = $total;
					$tax_name = get_option('sales_tax_name'); ?>
					<tr>
						<td style="border:1px solid #fff; padding:8px" class="no_border" colspan="2"></td>
						<td class="no_border" style="text-align:right; border:1px solid #fff; padding:8px"><strong><?php echo $tax_name; ?>:</strong></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo pto_calculate_currency($post->ID, $vat); ?></td>
					</tr>
					<?php if(!empty($svat_rate)) { ?>
						<tr>
							<td style="border:1px solid #fff; padding:8px" class="no_border" colspan="2"></td>
							<td class="no_border" style="text-align:right; border:1px solid #fff; padding:8px"><strong><?php echo $stax_name; ?>:</strong></td>
							<td style="border:1px solid #fff; padding:8px"><?php echo pto_calculate_currency($post->ID, $svat); ?></td>
						</tr>					
					<?php } ?>
					<tr>
						<td style="border:1px solid #fff; padding:8px" class="no_border" colspan="2"></td>
						<td class="no_border" style="text-align:right; border:1px solid #fff; padding:8px"><strong><?php _e('TOTAL:', 'cqpim'); ?></strong></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo pto_calculate_currency($post->ID, $total); ?></td>
					</tr>
				<?php } ?>
					<tr>
						<td style="border:1px solid #fff; padding:8px" class="no_border" colspan="2"></td>
						<td class="no_border" style="text-align:right; border:1px solid #fff; padding:8px"><strong><?php _e('Received:', 'cqpim'); ?></strong></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo pto_calculate_currency($post->ID, $received); ?></td>
					</tr>
					<tr>
						<td style="border:1px solid #fff; padding:8px" class="no_border" colspan="2"></td>
						<td class="no_border" style="text-align:right; border:1px solid #fff; padding:8px"><strong><?php _e('Outstanding:', 'cqpim'); ?></strong></td>
						<?php $outstanding = $outstanding - $received; ?>
						<td style="border:1px solid #fff; padding:8px"><?php echo pto_calculate_currency($post->ID, $outstanding); ?></td>
					</tr>
				</tbody>
			</table>
			<br />
			<div style="text-align:left" class="invoice_footer">
				<p><?php echo wpautop($invoice_footer); ?></p>
				<div class="clear"></div>
			</div>
		<?php } else { ?>
			<h1><?php _e('Access Denied', 'cqpim'); ?></h1>
		<?php } ?>
	</div><!-- #content -->
<?php wp_footer(); ?>
</body>
</html>
<?php exit; ?>