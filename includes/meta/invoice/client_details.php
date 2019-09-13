<?php
function pto_invoice_client_project_metabox_callback( $post ) {
 	wp_nonce_field( 
	'invoice_client_project_metabox', 
	'invoice_client_project_metabox_nonce' );
		$invoice_details = get_post_meta($post->ID, 'invoice_details', true);
		$invoice_id = get_post_meta($post->ID, 'invoice_id', true);
		$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
		$on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';
		$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
		$allow_partial = isset($invoice_details['allow_partial']) ? $invoice_details['allow_partial'] : '';
		$deposit = isset($invoice_details['deposit']) ? $invoice_details['deposit'] : '';
		if(!$invoice_date) {
			$invoice_date = date(get_option('cqpim_date_format'));
		}
		if(!empty($invoice_id)) {
			echo '<p>' . __('Invoice Number:', 'cqpim') . ' </p>';
			echo '<input type="text" name="invoice_number" id="invoice_number" value="' . $invoice_id . '" />';	
		} else {
			$invoice_id = pto_get_invoice_id();
			echo '<p>' . __('Invoice Number:', 'cqpim') . ' </p>';
			echo '<input type="text" name="invoice_number" id="invoice_number" value="' . $invoice_id . '" />';
		}
		echo '<p>' . __('Invoice Date:', 'cqpim') . ' </p>';
		if(is_numeric($invoice_date)) { $invoice_date = date(get_option('cqpim_date_format'), $invoice_date); } else { $invoice_date = $invoice_date; }
		echo '<input type="text" name="invoice_date" id="invoice_date" class="datepicker" value="' . $invoice_date . '" />';
		$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
		$now = time();
		if(empty($on_receipt)) {
			if(!$paid) {
				if($due) {
					if($now > $due) {
						echo '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('THIS INVOICE IS OVERDUE', 'cqpim') . '</div>';		
					}
				}
			}
		}
		$args = array(
			'post_type' => 'cqpim_client',
			'posts_per_page' => -1,
			'post_status' => 'private',
		);
		$clients = get_posts($args);
		echo '<p>' . __('Client:', 'cqpim') . '</p>';
		echo '<select id="invoice_client" name="invoice_client" required>';
		echo '<option value="">' . __('Select a Client:', 'cqpim') . '</option>';
		foreach($clients as $client) {
			$selected = '';
			$client_details = get_post_meta($client->ID, 'client_details', true);
			if(!empty($invoice_details['client_id']) && $invoice_details['client_id'] == $client->ID) {
				$selected = 'selected';
			}
			echo '<option value="' . $client->ID . '" ' . $selected . '>' . $client_details['client_company'] . '</option>';
		}
		echo '</select>';
		$client_id = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
		$client_details = get_post_meta($client_id, 'client_details', true);
		$client_contacts = get_post_meta($client_id, 'client_contacts', true);
		$client_contact = get_post_meta($post->ID, 'client_contact', true);
		if(!empty($client_id)) { ?>
			<select style="border-top:0" name="client_contact" id="client_contact">
				<option value=""><?php _e('Select a Contact', 'cqpim'); ?></option>
				<option value="<?php echo $client_details['user_id']; ?>" <?php if($client_contact == $client_details['user_id']) { echo 'selected="selected"'; } ?>><?php echo $client_details['client_contact']; ?> <?php _e('(Main Contact)', 'cqpim'); ?></option>
				<?php 
				if(!empty($client_contacts)) {
					foreach($client_contacts as $contact) { ?>
						<option value="<?php echo $contact['user_id']; ?>" <?php if($client_contact == $contact['user_id']) { echo 'selected="selected"'; } ?>><?php echo $contact['name']; ?></option>							
					<?php }
				}
				?>
			</select>					
		<?php } else { ?>
			<select style="border-top:0" name="client_contact" id="client_contact" disabled >
				<option value=""><?php _e('Select a Contact', 'cqpim'); ?></option>
			</select>
		<?php } ?>
		<div class="project_dd">
			<?php if(!empty($invoice_details['ticket'])) { ?>
				<input type="hidden" name="invoice_project" value="<?php echo $invoice_details['project_id']; ?>" />
			<?php } else { ?>
				<p><?php _e('Project:', 'cqpim'); ?></p>
				<select id="invoice_project" name="invoice_project">
				<?php if(empty($invoice_details['project_id'])) { ?>
					<option value=""><?php _e('Choose a Project', 'cqpim'); ?></option>
				<?php } else { 
					$args = array(
						'post_type' => 'cqpim_project',
						'posts_per_page' => -1,
						'post_status' => 'private',					
					);
					$projects = get_posts($args);
					if($projects) {
						foreach($projects as $project) {
							$project_details = get_post_meta($project->ID, 'project_details', true); 
							$invoice_client_id = $invoice_details['client_id'];
							$project_client_id = $project_details['client_id'];
							if($project->ID == $invoice_details['project_id']) {
								$selected = 'selected';
							} else {
								$selected = '';
							}
							if($invoice_client_id == $project_client_id) {
								echo "<option value='" .  $project->ID . "' " . $selected . ">" . $project_details['quote_ref'] . " - " . $project->post_title . "</option>";
							}
						}
					}			
				} ?>
				</select>
			<?php } ?>
			<br /><br />
		</div>
		<?php if($deposit != 1) { ?>
		<input type="checkbox" name="allow_partial" <?php if($allow_partial == 1) { echo 'checked="checked"'; } ?> value="1" /> <?php _e('Allow partial payments on this invoice', 'cqpim'); ?><br />
	<?php }
	$invoice_paid = isset($invoice_details['paid_details']) ? $invoice_details['paid_details'] : '';
	$invoice_sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
	$client = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
	$project = isset($invoice_details['project_id']) ? $invoice_details['project_id'] : '';
	$line_items = get_post_meta($post->ID, 'line_items', true);
	?>
	<p class="underline"><?php _e('Invoice Status', 'cqpim'); ?></p>
	<table class="quote_status">
		<tr>
			<td class="title"><?php _e('Invoice Date', 'cqpim'); ?></td>
			<?php if(is_numeric($invoice_date)) { $invoice_date = date(get_option('cqpim_date_format'), $invoice_date); } else { $invoice_date = $invoice_date; } ;?>
			<td><?php echo $invoice_date; ?></td>
		</tr>
		<tr>
			<td class="title"><?php _e('Invoice Due', 'cqpim'); ?></td>
			<?php if(empty($on_receipt)) { ?>
				<?php if($due) { ?>
				<td><?php echo date(get_option('cqpim_date_format'), $due); ?></td>
				<?php } else { ?>
				<td></td>
				<?php } ?>
			<?php } else { ?>
				<td><?php _e('On Receipt', 'cqpim'); ?></td>
			<?php } ?>
		</tr>
		<tr>
			<?php $classes = ( !empty( $client ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php _e('Client', 'cqpim'); ?></td>
			<td class="<?php echo $classes; ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( !empty( $project ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php _e('Project', 'cqpim'); ?></td>
			<td class="<?php echo $classes; ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( !empty( $line_items ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php _e('Line Items', 'cqpim'); ?></td>
			<td class="<?php echo $classes; ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( !empty( $invoice_sent ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php _e('Invoice Sent', 'cqpim'); ?></td>
			<td class="<?php echo $classes; ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( !empty( $invoice_paid ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php _e('Invoice Paid', 'cqpim'); ?></td>
			<td class="<?php echo $classes; ?>"></td>
		</tr>
	</table>
	<?php
	$invoice_sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
	$invoice_paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
	if(current_user_can('publish_cqpim_invoices')) {
		echo '<a class="cqpim_button cqpim_button_link font-white bg-blue block mt-10 rounded_2 block save" href="#">' . __('Update Invoice', 'cqpim') . '</a>';
		echo '<a id="edit_due" class="cqpim_button cqpim_button_link font-white bg-blue block mt-10 rounded_2 block" href="#">' . __('Edit Due Date', 'cqpim') . '</a>'; ?>	
		<div id="invoice_due_container" style="display:none">
			<div id="invoice_due">
				<div style="padding:12px">
					<h3><?php _e('Edit Invoice Due Date', 'cqpim'); ?></h3>
					<p><?php _e('Due Date:', 'cqpim'); ?></p>
					<input style="width:270px" class="datepicker" type="text" id="due_date" name="due_date" value="<?php echo date(get_option('cqpim_date_format'), $due); ?>" />
					<div class="clear"></div>
					<br />
					<?php echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="edit_date_conf" data-id="' . $post->ID . '">' . __('Confirm Due Date', 'cqpim') . '</button>'; ?>								
				</div>
			</div>
		</div>		
		<?php
	}
	if($post->post_name) {
		$url = get_the_permalink($post->ID);
		$pdf_url = add_query_arg( 'download_pdf', '1', get_edit_post_link($post->ID) );
		echo '<a class="cqpim_button cqpim_button_link font-white bg-blue block mt-10 rounded_2 block" href="' . $url . '?page=print" target="_blank">' . __('View Printable Invoice', 'cqpim') . '</a>';
		echo '<a class="cqpim_button cqpim_button_link font-white bg-blue block mt-10 rounded_2 block" href="' . $pdf_url . '">' . __('Download PDF Invoice', 'cqpim') . '</a>';
	}	
	if(!$invoice_paid) {
		if(empty($invoice_sent)) {
			echo '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('The invoice has not yet been sent to the client.', 'cqpim') . '</div>';
			if(current_user_can('cqpim_mark_invoice_paid')) {
				echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="mark_paid_trigger" data-id="' . $post->ID . '">' . __('Add Payment', 'cqpim') . '</button>'; ?>
				<div id="invoice_payment_container" style="display:none">
					<div id="invoice_payment">
						<div style="padding:12px">
							<h3><?php _e('Add Payment', 'cqpim'); ?></h3>
							<p><?php _e('Payment Amount:', 'cqpim'); ?></p>
							<input style="width:270px" type="text" id="payment_amount" value="<?php echo get_option('currency_symbol'); ?>" />
							<p><?php _e('Payment Date:', 'cqpim'); ?></p>
							<input style="width:270px" class="datepicker" type="text" id="payment_date" value="<?php echo date(get_option('cqpim_date_format'), time()); ?>" />
							<p><?php _e('Payment Notes:', 'cqpim'); ?></p>
							<textarea style="width:400px; height:100px" id="payment_notes" name="payment_notes"></textarea>
							<div class="clear"></div>
							<?php echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="mark_paid" data-id="' . $post->ID . '">' . __('Add Payment', 'cqpim') . '</button>'; ?>								
						</div>
					</div>
				</div>
				<?php
			}
			if(current_user_can('cqpim_send_invoices')) {
				echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="send_invoice" data-type="send" data-id="' . $post->ID . '">' . __('Send Invoice', 'cqpim') . '</button>';
			}
		}
		if($invoice_sent) {
			$invoice_sent = $invoice_details['sent_details'];
			$to = isset($invoice_sent['to']) ? $invoice_sent['to'] : '';
			$by = isset($invoice_sent['by']) ? $invoice_sent['by'] : '';
			$at = isset($invoice_sent['date']) ? $invoice_sent['date'] : '';
			if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
			echo '<div class="cqpim-alert cqpim-alert-warning alert-display">';
			printf(__('The invoice was sent to %1$s on %2$s by %3$s', 'cqpim'), $to, $at, $by);
			echo '</div>';
			if(current_user_can('cqpim_send_invoices')) {
				echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="send_invoice" data-type="resend" data-id="' . $post->ID . '">' . __('Resend Invoice', 'cqpim') . '</button>';
				if(!$paid) {
					if($due) {
						if($now > $due) {
							echo '<button class="send_reminder cqpim_button font-white bg-blue block mt-10 rounded_2 block" data-type="overdue" data-id="' . $post->ID . '">' . __('Send Overdue Email', 'cqpim') . '</button>';		
						} else {
							echo '<button class="send_reminder cqpim_button font-white bg-blue block mt-10 rounded_2 block" data-type="reminder" data-id="' . $post->ID . '">' . __('Send Reminder Email', 'cqpim') . '</button>';							
						}
					} 
				}
			}
			if(current_user_can('cqpim_mark_invoice_paid')) {
				echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="mark_paid_trigger" data-id="' . $post->ID . '">' . __('Add Payment', 'cqpim') . '</button>'; ?>
				<div id="invoice_payment_container" style="display:none">
					<div id="invoice_payment">
						<div style="padding:12px">
							<h3><?php _e('Add Payment', 'cqpim'); ?></h3>
							<p><?php _e('Payment Amount:', 'cqpim'); ?></p>
							<input style="width:270px" type="text" id="payment_amount" />
							<p><?php _e('Payment Date:', 'cqpim'); ?></p>
							<input style="width:270px" class="datepicker" type="text" id="payment_date" value="<?php echo date(get_option('cqpim_date_format'), time()); ?>" />
							<p><?php _e('Payment Notes:', 'cqpim'); ?></p>
							<textarea style="width:400px; height:100px" id="payment_notes" name="payment_notes"></textarea>
							<div class="clear"></div>
							<?php echo '<button class="cqpim_button font-green border-green mt-10 right op" id="mark_paid" data-id="' . $post->ID . '">' . __('Add Payment', 'cqpim') . '</button><div style="display:none" class="ajax_spinner"></div>'; ?>								
							<div class="clear"></div>
							<br />
						</div>
					</div>
				</div>
				<?php
			}
		}
	} else {
		$invoice_paid = $invoice_details['paid_details'];
		$by = isset($invoice_paid['by']) ? $invoice_paid['by'] : '';
		$at = isset($invoice_paid['date']) ? $invoice_paid['date'] : '';
		if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
		echo '<div class="cqpim-alert cqpim-alert-success alert-display">';
		printf(__('The invoice was marked as paid by %1$s at %2$s', 'cqpim'), $by, $at);
		echo '</div>';
	}
	if(current_user_can('publish_cqpim_invoices')) {
		echo '<div id="messages"></div>';
	}
}
add_action( 'save_post', 'save_pto_invoice_client_project_metabox_data' );
function save_pto_invoice_client_project_metabox_data( $post_id ){
	if ( ! isset( $_POST['invoice_client_project_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['invoice_client_project_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'invoice_client_project_metabox' ) )
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
	if(isset($_POST['invoice_number'])) {
		$invoice_id = $_POST['invoice_number'];
		update_post_meta($post_id, 'invoice_id', $invoice_id);
	}
	if(isset($_POST['invoice_project'])) {
		$project_id = $_POST['invoice_project'];
		$invoice_details = get_post_meta($post_id, 'invoice_details', true);
		$invoice_details = $invoice_details&&is_array($invoice_details)?$invoice_details:array();
		$invoice_details['project_id'] = $project_id;
		update_post_meta($post_id, 'invoice_details', $invoice_details);
		update_post_meta($post_id, 'invoice_project', $project_id);
	}
	$invoice_details = get_post_meta($post_id, 'invoice_details', true);
	$invoice_details = $invoice_details&&is_array($invoice_details)?$invoice_details:array();
	$invoice_details['allow_partial'] = isset($_POST['allow_partial']) ? $_POST['allow_partial'] : 0;
	update_post_meta($post_id, 'invoice_details', $invoice_details);
	if(isset($_POST['client_contact'])) {
		$client_contact = $_POST['client_contact'];
		update_post_meta($post_id, 'client_contact', $client_contact);
	}
	if(isset($_POST['invoice_client'])) {
		$client_id = $_POST['invoice_client'];
		$invoice_details = get_post_meta($post_id, 'invoice_details', true);
		$invoice_details = $invoice_details&&is_array($invoice_details)?$invoice_details:array();
		$invoice_details['client_id'] = $client_id;
		update_post_meta($post_id, 'invoice_details', $invoice_details);
		update_post_meta($post_id, 'invoice_client', $client_id);	
	}
	$currency = get_option('currency_symbol');
	$currency_code = get_option('currency_code');
	$currency_position = get_option('currency_symbol_position');
	$currency_space = get_option('currency_symbol_space'); 
	$client_currency = get_post_meta($client_id, 'currency_symbol', true);
	$client_currency_code = get_post_meta($client_id, 'currency_code', true);
	$client_currency_space = get_post_meta($client_id, 'currency_space', true);		
	$client_currency_position = get_post_meta($client_id, 'currency_position', true);
	$quote_currency = isset($_POST['currency_symbol']) ? $_POST['currency_symbol'] : '';
	$quote_currency_code = isset($_POST['currency_code']) ? $_POST['currency_code'] : '';
	$quote_currency_space = isset($_POST['currency_space']) ? $_POST['currency_space'] : '';
	$quote_currency_position = isset($_POST['currency_position']) ? $_POST['currency_position'] : '';
	if(!empty($quote_currency)) {
		update_post_meta($post_id, 'currency_symbol', $quote_currency);
	} else {
		if(!empty($client_currency)) {
			update_post_meta($post_id, 'currency_symbol', $client_currency);
		} else {
			update_post_meta($post_id, 'currency_symbol', $currency);
		}
	}
	if(!empty($quote_currency_code)) {
		update_post_meta($post_id, 'currency_code', $quote_currency_code);
	} else {
		if(!empty($client_currency_code)) {
			update_post_meta($post_id, 'currency_code', $client_currency_code);
		} else {
			update_post_meta($post_id, 'currency_code', $currency_code);
		}
	}
	if(!empty($quote_currency_space)) {
		update_post_meta($post_id, 'currency_space', $quote_currency_space);
	} else {
		if(!empty($client_currency_space)) {
			update_post_meta($post_id, 'currency_space', $client_currency_space);
		} else {
			update_post_meta($post_id, 'currency_space', $currency_space);
		}
	}
	if(!empty($quote_currency_position)) {
		update_post_meta($post_id, 'currency_position', $quote_currency_position);
	} else {
		if(!empty($client_currency_position)) {
			update_post_meta($post_id, 'currency_position', $client_currency_position);
		} else {
			update_post_meta($post_id, 'currency_position', $currency_position);
		}
	}
	if(isset($_POST['invoice_date'])) {
		$invoice_details = get_post_meta($post_id, 'invoice_details', true);
		$invoice_details = $invoice_details&&is_array($invoice_details)?$invoice_details:array();
		$client_id = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
		$client_details = get_post_meta($client_id, 'client_details', true);
		$client_terms = isset($client_details['invoice_terms']) ? $client_details['invoice_terms'] : '';
		$submitted = pto_convert_date($_POST['invoice_date']);
		if($client_terms) {
			$terms = $client_terms;
		} else {
			$terms = get_option('company_invoice_terms');
		}
		if($terms == 1) {
			$invoice_details['on_receipt'] = true;
		}
		$invoice_details['invoice_date'] = $submitted;
		if(empty($invoice_details['custom_terms'])) {
			$invoice_details['terms_over'] = strtotime('+' . $terms . ' days', $submitted);
		}
		update_post_meta($post_id, 'invoice_details', $invoice_details);
	}
	$tax_app = get_post_meta($post_id, 'tax_set', true);
	if(empty($tax_app)) {
		$client_details = get_post_meta($client_id, 'client_details', true);
		$client_tax = isset($client_details['tax_disabled']) ? $client_details['tax_disabled'] : '';
		$client_stax = isset($client_details['stax_disabled']) ? $client_details['stax_disabled'] : '';
		$system_tax = get_option('sales_tax_rate');
		$system_stax = get_option('secondary_sales_tax_rate');
		if(!empty($system_tax) && empty($client_tax)) {
			update_post_meta($post_id, 'tax_applicable', 1);
			update_post_meta($post_id, 'tax_set', 1);	
			update_post_meta($post_id, 'tax_rate', $system_tax);	
			if(!empty($system_stax) && empty($client_stax)) {
				update_post_meta($post_id, 'stax_applicable', 1);
				update_post_meta($post_id, 'stax_set', 1);	
				update_post_meta($post_id, 'stax_rate', $system_stax);			
			} else {
				update_post_meta($post_id, 'stax_applicable', 0);
				update_post_meta($post_id, 'stax_set', 1);
				update_post_meta($post_id, 'stax_rate', 0);				
			}
		} else {
			update_post_meta($post_id, 'tax_applicable', 0);
			update_post_meta($post_id, 'tax_set', 1);
			update_post_meta($post_id, 'tax_rate', 0);			
		}
	}
	remove_action( 'save_post', 'save_pto_invoice_client_project_metabox_data' );
	wp_update_post( array(
		'ID' => $post_id,
		'post_name' => $invoice_id
	));
	add_action( 'save_post', 'save_pto_invoice_client_project_metabox_data' );
}