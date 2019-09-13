<?php
function pto_quote_status_metabox_callback( $post ) {
 	wp_nonce_field( 
	'quote_status_metabox', 
	'quote_status_metabox_nonce' );
	$quote_details = get_post_meta($post->ID, 'quote_details', true);
	$quote_elements = get_post_meta($post->ID, 'quote_elements', true);
	$quote_client = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
	$quote_ref = isset($quote_details['quote_ref']) ? $quote_details['quote_ref'] : '';
	$quote_summary = isset($quote_details['quote_summary']) ? $quote_details['quote_summary'] : '';
	$start_date = isset($quote_details['start_date']) ? $quote_details['start_date'] : '';
	$finish_date = isset($quote_details['finish_date']) ? $quote_details['finish_date'] : '';
	$quote_header = isset($quote_details['quote_header']) ? $quote_details['quote_footer'] : '';
	$quote_footer = isset($quote_details['quote_footer']) ? $quote_details['quote_footer'] : '';
	$quote_deposit = isset($quote_details['deposit_amount']) ? $quote_details['deposit_amount'] : '';
	?>
	<table class="quote_status">
		<tr>
			<td class="title underline"><?php _e('Quote / Estimate Details', 'cqpim'); ?></td>
			<td></td>
		</tr>
		<tr>
			<?php $classes = ( !empty( $quote_client ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php _e('Client', 'cqpim'); ?></td>
			<td class="<?php echo $classes; ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( !empty( $quote_ref ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php _e('Quote / Estimate Ref', 'cqpim'); ?></td>
			<td class="<?php echo $classes; ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( !empty( $quote_summary ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php _e('Project Brief', 'cqpim'); ?></td>
			<td class="<?php echo $classes; ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( !empty( $start_date ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php _e('Start Date', 'cqpim'); ?></td>
			<td class="<?php echo $classes; ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( !empty( $finish_date ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php _e('Deadline', 'cqpim'); ?></td>
			<td class="<?php echo $classes; ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( !empty( $quote_header ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php _e('Quote / Estimate Header', 'cqpim'); ?></td>
			<td class="<?php echo $classes; ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( !empty( $quote_footer ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php _e('Quote / Estimate Footer', 'cqpim'); ?></td>
			<td class="<?php echo $classes; ?>"></td>
		</tr>
		<tr>
			<td class="title">&nbsp;</td>
			<td></td>
		</tr>
		<tr>
			<td class="title underline"><?php _e('Milestones', 'cqpim'); ?></td>
			<td></td>
		</tr>
		<?php if(!$quote_elements) { ?>
			<tr>
				<td class="title"><?php _e('No Milestones Added', 'cqpim'); ?></td>
				<td class="red"></td>
			</tr>
		<?php } else {
			$ordered = array();
			$i = 0;
			$mi = 0;
			foreach($quote_elements as $key => $element) {
				$weight = isset($element['weight']) ? $element['weight'] : $mi;
				$ordered[$weight] = $element;
				$mi++;
			}
			ksort($ordered);
			foreach($ordered as $element) { ?>
				<tr>
					<?php $classes = ( !empty( $element['title'] ) && !empty( $element['cost'] ) ) ? 'green' : 'red'; ?>
					<td class="title"><?php echo $element['title']; ?></td>
					<td class="<?php echo $classes; ?>"></td>
				</tr>				
			<?php $i++; }					
		}
		?>
		<tr>
			<td class="title">&nbsp;</td>
			<td></td>
		</tr>
		<tr>
			<td class="title underline"><?php _e('Deposit', 'cqpim'); ?></td>
			<td></td>
		</tr>
		<tr>
			<?php $classes = ( !empty( $quote_deposit ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php _e('Initial Deposit', 'cqpim'); ?></td>
			<td class="<?php echo $classes; ?>"></td>
		</tr>
	</table>
	<?php
	$url = get_the_permalink($post->ID);
	$quote_sent = isset($quote_details['sent']) ? $quote_details['sent'] : '';
	$quote_accepted = isset($quote_details['confirmed']) ? $quote_details['confirmed'] : '';
	$quote_type = isset($quote_details['quote_type']) ? $quote_details['quote_type'] : '';
	if(!$quote_accepted) {
		if(empty($quote_sent)) {
			echo '<div class="cqpim-alert cqpim-alert-danger alert-display">';
			$quote_type == 'estimate' ? _e('This estimate has not yet been sent to the client.', 'cqpim') : _e('This quote has not yet been sent to the client.', 'cqpim');
			echo '</div>';
			if(current_user_can('cqpim_send_quotes')) { 
				echo '<button id="send_quote" class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" data-id="' . $post->ID . '">' . __('Send Quote / Estimate', 'cqpim') . '</button>';
				echo '<a class="cqpim_button cqpim_button_link font-white bg-blue block mt-10 rounded_2 block" href="' . $url . '?page=print" target="_blank">' . __('Preview Quote / Estimate', 'cqpim') . '</a>';
			}
		}
		if($quote_sent) {
			$quote_sent = $quote_details['sent_details'];
			$to = isset($quote_sent['to']) ? $quote_sent['to'] : '';
			$by = isset($quote_sent['by']) ? $quote_sent['by'] : '';
			$at = isset($quote_sent['date']) ? $quote_sent['date'] : '';
			if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $start_date; }
			echo '<div class="cqpim-alert cqpim-alert-warning alert-display">';
			$quote_type == 'estimate' ? printf(__('This estimate was sent to %1$s on %2$s by %3$s', 'cqpim'), $to, $at, $by) : printf(__('This quote was sent to %1$s on %2$s by %3$s', 'cqpim'), $to, $at, $by);
			echo '</div>';
			if(current_user_can('cqpim_send_quotes')) { 
				echo '<button id="send_quote" class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" data-id="' . $post->ID . '">' . __('Resend Quote / Estimate', 'cqpim') . '</button>';
				echo '<a class="cqpim_button cqpim_button_link font-white bg-blue block mt-10 rounded_2 block" href="' . $url . '?page=print" target="_blank">' . __('Preview Quote / Estimate', 'cqpim') . '</a>';
			}
		}
	} else {
		$quote_accepted = $quote_details['confirmed_details'];
		$ip = isset($quote_accepted['ip']) ? $quote_accepted['ip'] : '';
		$by = isset($quote_accepted['by']) ? $quote_accepted['by'] : '';
		$at = isset($quote_accepted['date']) ? $quote_accepted['date'] : '';
		if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
		echo '<div class="cqpim-alert cqpim-alert-success alert-display">';
		$quote_type == 'estimate' ? printf(__('This estimate was accepted by %1$s on %2$s from IP address %3$s', 'cqpim'), $by, $at, $ip) : printf(__('This quote was accepted by %1$s on %2$s from IP address %3$s', 'cqpim'), $by, $at, $ip);
		echo '</div>';	
		if(current_user_can('cqpim_send_quotes')) { 
			echo '<button id="send_quote" class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" data-id="' . $post->ID . '">' . __('Resend Quote / Estimate', 'cqpim') . '</button>';
			echo '<a class="cqpim_button cqpim_button_link font-white bg-blue block mt-10 rounded_2 block" href="' . $url . '?page=print" target="_blank">' . __('Preview Quote / Estimate', 'cqpim') . '</a>';
		}
	}
	if(current_user_can('publish_cqpim_quotes')) {
		echo '<button class="save cqpim_button font-white bg-blue block mt-10 rounded_2 block" data-id="' . $post->ID . '">' . __('Update Quote / Estimate', 'cqpim') . '</button>';
		echo '<button class="convert_to_project cqpim_button font-white bg-blue block mt-10 rounded_2 block" data-id="' . $post->ID . '">' . __('Convert Quote to Project', 'cqpim') . '</button>';
		echo '<div id="messages"></div>';
	} ?>
	<div id="quote_convert_container" style="display:none">
		<div id="quote_convert">
			<div style="padding:12px">
				<h3><?php _e('Convert to Project', 'cqpim'); ?></h3>
				<p><?php _e('Quotes / Estimates are converted to projects automatically when they are accepted by the client. Are you sure you want to manually convert this quote / estimate?', 'cqpim'); ?></p>
				<div id="convert-error"></div>
				<button class="cancel-colorbox mt-10 cqpim_button font-red border-red op"><?php _e('Cancel', 'cqpim'); ?></button>
				<button class="convert_confirm mt-10 cqpim_button font-green border-green right op" value="<?php echo $post->ID; ?>"><?php _e('Convert to Project', 'cqpim'); ?></button>
			</div>
		</div>
	</div>
	<?php
}