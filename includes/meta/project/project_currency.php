<?php
function pto_project_currency_metabox_callback( $post ) {
 	wp_nonce_field( 
	'project_currency_metabox', 
	'project_currency_metabox_nonce' );
	$quote_details = get_post_meta($post->ID, 'project_details', true);
	$client_id = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
	$currency_override = get_option('allow_client_currency_override');
	$currency = get_option('currency_symbol');
	$currency_code = get_option('currency_code');
	$currency_position = get_option('currency_symbol_position');
	$currency_space = get_option('currency_symbol_space'); 
	$client_currency = get_post_meta($client_id, 'currency_symbol', true);
	$client_currency_code = get_post_meta($client_id, 'currency_code', true);
	$client_currency_space = get_post_meta($client_id, 'currency_space', true);		
	$client_currency_position = get_post_meta($client_id, 'currency_position', true);	
	$quote_currency = get_post_meta($post->ID, 'currency_symbol', true);
	$quote_currency_code = get_post_meta($post->ID, 'currency_code', true);
	$quote_currency_space = get_post_meta($post->ID, 'currency_space', true);		
	$quote_currency_position = get_post_meta($post->ID, 'currency_position', true);	
	?>
	<p><?php _e('If you would like to override the currency on this project you can do so here. By default the project will use the system currency settings, however if the client that the project is assigned to has custom settings these will be used instead.', 'cqpim'); ?></p>
	<div class="cqpim-alert cqpim-alert-info alert-display">
		<p><strong><?php _e('System Currency Settings', 'cqpim'); ?></strong></p>
		<p>
			<?php _e('Currency Symbol:', 'cqpim'); ?> <?php echo $currency; ?><br />
			<?php _e('Currency Code:', 'cqpim'); ?> <?php echo $currency_code; ?><br />
			<?php _e('Currency Position:', 'cqpim'); ?> <?php if($currency_position == 'l') { _e('Before Amount', 'cqpim'); } else { _e('After Amount', 'cqpim'); } ?><br />
			<?php _e('Currency Space:', 'cqpim'); ?> <?php if($currency_space == '1') { _e('Yes', 'cqpim'); } else { _e('No', 'cqpim'); } ?>
		</p>
	</div>
	<?php if(!empty($client_id)) { 
		if(!empty($client_currency_space)) {
			if($client_currency_space == 'l') { 
				$sstring = __('Yes', 'cqpim'); 
			} else { 
				$sstring = __('No', 'cqpim'); 
			}
		} else {
			if($currency_space == 'l') { 
				$sstring = __('Yes (System Setting)', 'cqpim'); 
			} else { 
				$sstring = __('No (System Setting)', 'cqpim'); 
			}			
		}
		if(!empty($client_currency_position)) {
			if($client_currency_position == 'l') { 
				$string = __('Before Amount', 'cqpim'); 
			} else { 
				$string = __('After Amount', 'cqpim'); 
			}
		} else {
			if($currency_position == 'l') { 
				$string = __('Before Amount (System Setting)', 'cqpim'); 
			} else { 
				$string = __('After Amount (System Setting)', 'cqpim'); 
			}			
		}
	?>
		<div class="cqpim-alert cqpim-alert-info alert-display">
			<p><strong><?php _e('Client Currency Settings', 'cqpim'); ?></strong></p>
			<p>
				<?php _e('Currency Symbol:', 'cqpim'); ?> <?php if(!empty($client_currency)) { echo $client_currency; } else { echo $currency . ' ' . __('(System Setting)', 'cqpim'); } ?><br />
				<?php _e('Currency Code:', 'cqpim'); ?> <?php if(!empty($client_currency_code)) { echo $client_currency_code; } else { echo $currency_code . ' ' . __('(System Setting)', 'cqpim'); } ?><br />
				<?php _e('Currency Position:', 'cqpim'); ?> <?php echo $string; ?><br />
				<?php _e('Currency Space:', 'cqpim'); ?> <?php echo $sstring; ?>
			</p>
		</div>
	<?php } ?>
	<p><strong><?php _e('Active Currency Settings', 'cqpim'); ?></strong></p>
	<table style="width:100%">
		<tr>
			<td>
				<?php _e('Project Client Currency Symbol:', 'cqpim'); ?> <br />
				<input type="text" name="currency_symbol" value="<?php echo $quote_currency; ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<?php _e('Project Currency Code:', 'cqpim'); ?> <br />				
				<select name="currency_code" id="currency_code">
					<option value="0"><?php _e('Choose a currency', 'cqpim'); ?></option>
					<?php $codes = pto_return_currency_select();
					foreach($codes as $key => $code) {
						if($key == $quote_currency_code) { $checked = 'selected="selected"'; } else { $checked = ''; };
						echo '<option value="' . $key . '" ' . $checked . '>' . $code . '</option>';
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e('Project Currency Symbol Position: ', 'cqpim'); ?> <br />
				<select name="currency_position">
					<option value=""><?php _e('Choose...', 'cqpim'); ?></option>
					<option value="l" <?php if($quote_currency_position == 'l') { echo 'selected'; } ?>><?php _e('Before Amount', 'cqpim'); ?></option>
					<option value="r" <?php if($quote_currency_position == 'r') { echo 'selected'; } ?>><?php _e('After Amount', 'cqpim'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<?php _e('Add a space between the currency symbol and amount.', 'cqpim'); ?> <br />
				<input type="checkbox" id="currency_space" name="currency_space" value="1" <?php if($quote_currency_space == '1') { echo 'checked'; } ?> />
			</td>
		</tr>
	</table>		
	<?php
}
add_action( 'save_post', 'save_pto_project_currency_metabox_data' );
function save_pto_project_currency_metabox_data( $post_id ){
	if ( ! isset( $_POST['project_currency_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['project_currency_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'project_currency_metabox' ) )
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
}