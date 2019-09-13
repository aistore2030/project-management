<?php
function pto_quote_client_metabox_callback( $post ) {
	wp_nonce_field( 
	'quote_client_metabox', 
	'quote_client_metabox_nonce' );
	$quote_details = get_post_meta($post->ID, 'quote_details', true);
	$quote_type = isset($quote_details['quote_type']) ? $quote_details['quote_type'] : '';
	$quote_ref = isset($quote_details['quote_ref']) ? $quote_details['quote_ref'] : '';
	$start_date = isset($quote_details['start_date']) ? $quote_details['start_date'] : '';
	$finish_date = isset($quote_details['finish_date']) ? $quote_details['finish_date'] : '';
	$client_id = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
	$client_contact = isset($quote_details['client_contact']) ? $quote_details['client_contact'] : '';
	$deposit_amount = isset($quote_details['deposit_amount']) ? $quote_details['deposit_amount'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	if(!empty($client_contact)) {
		if($client_details['user_id'] == $client_contact) {
			$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
			$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
			$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
			$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
		} else {
			$client_contact_name = isset($client_contacts[$client_contact]['name']) ? $client_contacts[$client_contact]['name'] : '';
			$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
			$client_telephone = isset($client_contacts[$client_contact]['telephone']) ? $client_contacts[$client_contact]['telephone'] : '';
			$client_email = isset($client_contacts[$client_contact]['email']) ? $client_contacts[$client_contact]['email'] : '';		
		}
	} else {
		$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
		$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
		$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
		$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';		
	}
	if(!$quote_type) {	
		echo '<p>' . __('This section is currently unavailable', 'cqpim') . '</p>';
	} else { ?>
		<p><strong><?php _e('Client Details', 'cqpim'); ?></strong></p>
		<p><?php echo '<strong>' . __('Company Name:', 'cqpim') . ' </strong>' . $client_company_name . '<br />
		<strong>' . __('Contact Name:', 'cqpim') . '</strong> ' . $client_contact_name . '<br />
		<strong>' . __('Email:', 'cqpim') . ' </strong><a href="mailto:' . $client_email . '">' . $client_email . '</a>
		<br /><strong>' . __('Phone:', 'cqpim') . ' </strong>' . $client_telephone . '</p>';
		?>
		<p><strong><?php _e('Quote Details', 'cqpim'); ?></strong></p>
		<p>
			<strong><?php _e('Start Date:', 'cqpim'); ?> </strong><?php if(is_numeric($start_date)) { echo date(get_option('cqpim_date_format'), $start_date); } else { echo $start_date; } ?><br />
			<strong><?php _e('Deadline:', 'cqpim'); ?> </strong><?php if(is_numeric($finish_date)) { echo date(get_option('cqpim_date_format'), $finish_date); } else { echo $finish_date; } ?><br />
			<strong><?php _e('Deposit Required:', 'cqpim'); ?> </strong><?php if(!$deposit_amount || $deposit_amount == 'none') { _e('Not Required', 'cqpim'); } else { echo $deposit_amount . '%'; } ?>
		</p>
		<?php if(current_user_can('publish_cqpim_quotes')) { ?>
			<button id="edit-quote-details" class="cqpim_button font-white bg-blue block mt-10 rounded_2"><?php printf(__('Edit %1$s Details', 'cqpim'), $quote_type == 'estimate' ? _x('Estimate' , 'Quote type', 'cqpim') : _x('Quote' , 'Quote type', 'cqpim')); ?></button>
		<?php } ?>
		<div class="clear separator"></div>			
	<?php } ?>
	<div id="quote_basics_container" style="display:none">
		<div id="quote_basics">
			<div style="padding:12px">
				<?php if(!$quote_type) { ?>
				<h3><?php _e('Quote / Estimate Basics', 'cqpim'); ?></h3>
				<p><?php _e('These initial questions will help you to get your Quote / Estimate set up properly', 'cqpim'); ?></p>
				<?php } else { ?>
				<h3><?php printf(__('%1$s Details', 'cqpim'), ucwords($quote_type)); ?></h3>
				<?php } ?>
				<p><?php _e('Quote or Estimate?', 'cqpim'); ?></p>
				<select id="quote_type" name="quote_type" required >
					<option value=""><?php _e('Choose...', 'cqpim'); ?></option>
					<option value="quote" <?php if($quote_type == 'quote') { echo 'selected'; } ?>><?php _e('Quote', 'cqpim'); ?></option>
					<option value="estimate" <?php if($quote_type == 'estimate') { echo 'selected'; } ?>><?php _e('Estimate', 'cqpim'); ?></option>
				</select>
				<?php
				$args = array(
					'post_type' => 'cqpim_client',
					'posts_per_page' => -1,
					'post_status' => 'private',
				);
				$clients = get_posts($args);
				echo '<p>' . __('Please choose a client and contact to assign this quote / estimate to.', 'cqpim') . '</p>';
				echo '<select class="quote_client_dropdown" name="quote_client" id="quote_client" required >';
				echo '<option value="0">' . __('Select a Client...', 'cqpim') . ' </option>';
				foreach($clients as $client) {
					setup_postdata($client);
					$client_details = get_post_meta($client->ID, 'client_details', true);
					if($client_id == $client->ID) {
						echo '<option value="' . $client->ID . '" selected="selected">' . $client_details['client_company'] . '</option>';					
					} else {
						echo '<option value="' . $client->ID . '">' . $client_details['client_company'] . '</option>';
					}
				}
				echo '</select>';
				$quote_details = get_post_meta($post->ID, 'quote_details', true);
				$client_id = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
				$client_details = get_post_meta($client_id, 'client_details', true);
				$client_contact = isset($quote_details['client_contact']) ? $quote_details['client_contact'] : '';
				$client_contacts = get_post_meta($client_id, 'client_contacts', true);
				if(!empty($client_id)) { ?>
					<select name="client_contact" id="client_contact">
						<option value=""><?php _e('Choose a Contact', 'cqpim'); ?></option>
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
					<select name="client_contact" id="client_contact" disabled >
						<option value=""><?php _e('Choose a Contact', 'cqpim'); ?></option>
					</select>
				<?php } ?>
				<br />
				<span id="client_spinner" style="display:none" class="ajax_spinner"></span>
				<p><?php _e('Quote / Estimate Ref:', 'cqpim'); ?> </p>
				<?php if(!$quote_ref) {
					$quote_ref = $post->ID;
				} ?>
				<input type="text" name="quote_ref" id="quote_ref" value="<?php echo $quote_ref; ?>" required />
				<p><?php _e('Proposed Start/Launch Dates:', 'cqpim'); ?> </p>
				<input class="datepicker" type="text" name="start_date" id="start_date" value="<?php if(is_numeric($start_date)) { echo date(get_option('cqpim_date_format'), $start_date); } else { echo $start_date; } ?>" />		
				<input style="border-top:0" class="datepicker" type="text" name="finish_date" id="finish_date" value="<?php if(is_numeric($finish_date)) { echo date(get_option('cqpim_date_format'), $finish_date); } else { echo $finish_date; } ?>" />	
				<p><?php _e('Deposit Amount', 'cqpim'); ?></p>
				<select id="deposit_amount" name="deposit_amount">
					<option value=""><?php _e('Choose an Option', 'cqpim'); ?></option>			
					<option value="none" <?php if($deposit_amount == 0) { echo 'selected'; } ?>><?php _e('No Deposit Required', 'cqpim'); ?></option>
					<option value="10" <?php if($deposit_amount == 10) { echo 'selected'; } ?>><?php _e('10%', 'cqpim'); ?></option>
					<option value="20" <?php if($deposit_amount == 20) { echo 'selected'; } ?>><?php _e('20%', 'cqpim'); ?></option>
					<option value="30" <?php if($deposit_amount == 30) { echo 'selected'; } ?>><?php _e('30%', 'cqpim'); ?></option>
					<option value="40" <?php if($deposit_amount == 40) { echo 'selected'; } ?>><?php _e('40%', 'cqpim'); ?></option>
					<option value="50" <?php if($deposit_amount == 50) { echo 'selected'; } ?>><?php _e('50%', 'cqpim'); ?></option>
					<option value="60" <?php if($deposit_amount == 60) { echo 'selected'; } ?>><?php _e('60%', 'cqpim'); ?></option>
					<option value="70" <?php if($deposit_amount == 70) { echo 'selected'; } ?>><?php _e('70%', 'cqpim'); ?></option>
					<option value="80" <?php if($deposit_amount == 80) { echo 'selected'; } ?>><?php _e('80%', 'cqpim'); ?></option>
					<option value="90" <?php if($deposit_amount == 90) { echo 'selected'; } ?>><?php _e('90%', 'cqpim'); ?></option>
					<option value="100" <?php if($deposit_amount == 100) { echo 'selected'; } ?>><?php _e('100%', 'cqpim'); ?></option>
				</select>
				<?php 
				$auto_terms = get_option('auto_contract'); 
				$quote_terms = get_option('enable_quote_terms'); 
				if($auto_terms == 1 || $quote_terms == 1) { 
					$contract = isset($quote_details['default_contract_text']) ? $quote_details['default_contract_text'] : ''; 
					$default = get_option( 'default_contract_text' ); ?>
					<p><?php _e('Project Terms & Conditions Template', 'cqpim'); ?> </p>
					<select name="default_contract_text">
						<?php 
						$args = array(
							'post_type' => 'cqpim_terms',
							'posts_per_page' => -1,
							'post_status' => 'private'
						);
						$terms = get_posts($args);
						foreach($terms as $term) {
							if(!empty($contract)) {
								if($term->ID == $contract) {
									$selected = 'selected="selected"';
								} else {
									$selected = '';
								}
							} else {
								if($term->ID == $default) {
									$selected = 'selected="selected"';
								} else {
									$selected = '';
								}								
							}
							echo '<option value="' . $term->ID . '" ' . $selected . '>' . $term->post_title . '</option>';
						}
						?>
					</select>
					<br /><br />
				<?php } ?>
				<div id="basics-error"></div>
				<a class="cancel-creation mt-10 cqpim_button font-red border-red op" href="<?php echo admin_url(); ?>admin.php?page=pto-dashboard"><?php _e('Cancel', 'cqpim'); ?></a>
				<button class="save-basics mt-10 cqpim_button font-green border-green right op"><?php _e('Save', 'cqpim'); ?></button>
			</div>
		</div>
	</div>		
	<?php	
}
add_action( 'save_post', 'save_pto_quote_client_metabox_data' );
function save_pto_quote_client_metabox_data( $post_id ){
	if ( ! isset( $_POST['quote_client_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['quote_client_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'quote_client_metabox' ) )
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
	$quote_details = get_post_meta($post_id, 'quote_details', true);
	$quote_details = $quote_details&&is_array($quote_details)?$quote_details:array();
	if(isset($_POST['quote_type'])) {
		$quote_client = $_POST['quote_type'];
		$quote_details['quote_type'] = $_POST['quote_type'];
	}
	if(isset($_POST['quote_client'])) {
		$quote_client = $_POST['quote_client'];
		$quote_details['client_id'] = $_POST['quote_client'];
	}
	$currency = get_option('currency_symbol');
	$currency_code = get_option('currency_code');
	$currency_position = get_option('currency_symbol_position');
	$currency_space = get_option('currency_symbol_space'); 
	$client_currency = get_post_meta($quote_client, 'currency_symbol', true);
	$client_currency_code = get_post_meta($quote_client, 'currency_code', true);
	$client_currency_space = get_post_meta($quote_client, 'currency_space', true);		
	$client_currency_position = get_post_meta($quote_client, 'currency_position', true);
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
	if(isset($_POST['default_contract_text'])) {
		$default_contract_text = $_POST['default_contract_text'];
		$quote_details['default_contract_text'] = $_POST['default_contract_text'];
	} else {
		$quote_details['default_contract_text'] = get_option( 'default_contract_text' );
	}
	if(isset($_POST['client_contact'])) {
		$client_contact = $_POST['client_contact'];
		$quote_details['client_contact'] = $_POST['client_contact'];
	}
	if(isset($_POST['quote_ref'])) {
		$quote_details['quote_ref'] = $_POST['quote_ref'];
	}
	if(isset($_POST['start_date'])) {
		$date = $_POST['start_date'];
		$timestamp = pto_convert_date($date);
		$quote_details['start_date'] = $timestamp;
	}
	if(isset($_POST['finish_date'])) {
		$date = $_POST['finish_date'];
		$timestamp = pto_convert_date($date);
		$quote_details['finish_date'] = $timestamp;
	}
	if(isset($_POST['deposit_amount'])) {
		$deposit = $_POST['deposit_amount'];
		$quote_details['deposit_amount'] = $deposit;
	}
	update_post_meta($post_id, 'quote_details', $quote_details);
	$tax_app = get_post_meta($post_id, 'tax_set', true);
	if(empty($tax_app)) {
		$client_details = get_post_meta($quote_client, 'client_details', true);
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
	$title = get_the_title($post_id);
	$client_token = '%%CLIENT_COMPANY%%';
	$ref_token = '%%QUOTE_REF%%';
	$type_token = '%%TYPE%%';
	$quote_details = get_post_meta($post_id, 'quote_details', true);
	$client_id = $quote_details['client_id'];
	$type = $quote_details['quote_type'];
	$upper_type = ucfirst($type);
	$client_details = get_post_meta($client_id, 'client_details', true);
	$quote_ref = $quote_details['quote_ref'];
	$client_company = $client_details['client_company'];
	$title = str_replace($client_token, $client_company, $title);
	$title = str_replace($ref_token, $quote_ref, $title);
	$title = str_replace($type_token, $type == 'estimate' ? __('Estimate', 'cqpim') : __('Quote', 'cqpim'), $title);
	$quote_updated = array(
		'ID' => $post_id,
		'post_title' => $title,
		'post_name' => $post_id,
	);
	if ( ! wp_is_post_revision( $post_id ) ){
		remove_action('save_post', 'save_pto_quote_client_metabox_data');
		wp_update_post( $quote_updated );
		add_action('save_post', 'save_pto_quote_client_metabox_data');
	}
}