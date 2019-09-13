<?php
function pto_project_details_metabox_callback( $post ) {
 	wp_nonce_field( 
	'project_details_metabox', 
	'project_details_metabox_nonce' );
	$project_details = get_post_meta($post->ID, 'project_details', true);
	$project_elements = get_post_meta($post->ID, 'project_elements', true);
	$project_extras = get_post_meta($post->ID, 'project_extras', true);
	$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	$client_contract = get_post_meta($client_id, 'client_contract', true);
	$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
	$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
	$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
	$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
	$quote_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
	$start_date = isset($project_details['start_date']) ? $project_details['start_date'] : '';
	$project_contract = isset($project_details['default_contract_text']) ? $project_details['default_contract_text'] : '';
	$terms = get_post($project_contract);
	$finish_date = isset($project_details['finish_date']) ? $project_details['finish_date'] : '';
	$signoff = isset($project_details['signoff']) ? $project_details['signoff'] : '';
	$deposit_amount = isset($project_details['deposit_amount']) ? $project_details['deposit_amount'] : '';
	$confirmed = isset($project_details['confirmed']) ? $project_details['confirmed'] : '';
	$pm_name = isset($project_details['pm_name']) ? $project_details['pm_name'] : '';
	$sent = isset($project_details['sent']) ? $project_details['sent'] : '';
	$deposit_invoice_id = isset($project_details['deposit_invoice_id']) ? $project_details['deposit_invoice_id'] : '';
	$completion_invoice_id = isset($project_details['completion_invoice_id']) ? $project_details['completion_invoice_id'] : '';
	$deposit_invoice_details = get_post_meta($deposit_invoice_id, 'invoice_details', true);
	$completion_invoice_details = get_post_meta($completion_invoice_id, 'invoice_details', true);
	$deposit_sent = isset($deposit_invoice_details['sent']) ? $deposit_invoice_details['sent'] : '';
	$deposit_paid = isset($deposit_invoice_details['paid']) ? $deposit_invoice_details['paid'] : '';
	$completion_sent = isset($completion_invoice_details['sent']) ? $completion_invoice_details['sent'] : '';
	$completion_paid = isset($completion_invoice_details['paid']) ? $completion_invoice_details['paid'] : '';
	$contract_link = get_the_permalink($post->ID) . '?page=contract-print';
	$summary_link = get_the_permalink($post->ID) . '?page=summary&sub=updates';
	$contract_status = get_post_meta($post->ID, 'contract_status', true); 
	if(!empty($client_contact)) {
		if(!empty($client_details['user_id']) && $client_details['user_id'] == $client_contact) {
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
	$project_contributors = get_post_meta($post->ID, 'project_contributors', true);
	$project_colours = get_post_meta($post->ID, 'project_colours', true);
	$project_colour = isset($project_colours['project_colour']) ? $project_colours['project_colour'] : '#3B3F51';
	$ms_colour = isset($project_colours['ms_colour']) ? $project_colours['ms_colour'] : '#337ab7'; 
	$task_colour = isset($project_colours['task_colour']) ? $project_colours['task_colour'] : '#36c6d3'; ?>	
	<?php if(current_user_can('cqpim_edit_project_colours')) { ?>
		<p class="underline"><strong><?php _e('Project Colours', 'cqpim'); ?></strong> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php _e('You can set colours for project elements here, these colours will be used in the calendar to make projects easier to identify. NOTE: Overdue Tasks will always display in red', 'cqpim'); ?>"></i></p>
		<?php _e('Project', 'cqpim'); ?>
		<input type="text" class="cqpim_picker" name="project_colour" id="project_colour" value="<?php echo $project_colour; ?>" />
		<?php _e('Milestone', 'cqpim'); ?>
		<input type="text" class="cqpim_picker" name="ms_colour" id="ms_colour" value="<?php echo $ms_colour; ?>" />
		<?php _e('Task', 'cqpim'); ?>
		<input type="text" class="cqpim_picker" name="task_colour" id="task_colour" value="<?php echo $task_colour; ?>" />
	<?php } ?>
	<?php echo '<p class="underline"><strong>' . __('Project Manager', 'cqpim') . '</strong></p>';
	if(!is_array($project_contributors)) {
		echo '<p class="underline"><strong>' . __('No project manager added, add one in the Team Members section.', 'cqpim') . '</strong></p>';
	} else {
		$i = 0;
		foreach($project_contributors as $key => $contributor) {
			if(!empty($contributor['pm']) && $contributor['pm'] == 1) {
				$i++;
				$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
				$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
				if(current_user_can('edit_cqpim_teams')) {
					$team_url = get_edit_post_link($contributor['team_id']);
					$team_name = '<a href="' . $team_url . '" target="_blank">' . $team_name . '</a>';
				}
				$team_job = isset($team_details['team_job']) ? $team_details['team_job'] : '';
				$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
				$team_telephone = isset($team_details['team_telephone']) ? $team_details['team_telephone'] : '';
				echo '<div style="padding-top:5px">';
				echo '<strong>' . __('Name:', 'cqpim') . ' </strong>' . $team_name . "<br />";
				echo '<strong>' . __('Job Title:', 'cqpim') . ' </strong>' . $team_job . "<br />";
				echo '<strong>' . __('Email:', 'cqpim') . ' </strong>' . $team_email . "<br />";
				echo '<strong>' . __('Telephone:', 'cqpim') . ' </strong>' . $team_telephone . "<br />";
				echo '<div class="clear"></div>';
				echo '</div>';
			}
		}
		if($i == 0) {
			echo '<p>' . __('No project manager added, add one in the Team Members section.', 'cqpim') . '</p>';
		}
		echo '<div class="clear"></div>';
	}
	if(!empty($client_id) && current_user_can('cqpim_view_project_client_info')) { ?>
		<p class="underline"><strong><?php _e('Client Info', 'cqpim'); ?></strong></p>
		<p><?php echo '<strong>' . __('Company Name:', 'cqpim') . ' </strong>' . $client_company_name . '<br />
		<strong>' . __('Contact Name:', 'cqpim') . '</strong> ' . $client_contact_name . '<br />
		<strong>' . __('Email:', 'cqpim') . ' </strong><a href="mailto:' . $client_email . '">' . $client_email . '</a>
		<br /><strong>' . __('Phone:', 'cqpim') . ' </strong>' . $client_telephone; ?><br /></p>
	<?php } ?>
	<?php if(!empty($quote_ref)) { 
		$checked = get_post_meta($post->ID, 'contract_status', true); ?>
		<p class="underline"><strong><?php _e('Project Info', 'cqpim'); ?></strong></p>
		<p><strong><?php _e('Project Ref:', 'cqpim'); ?> </strong> <?php echo $quote_ref; ?><br />
		<?php if(!empty($checked) && $checked == 1) { ?>
		<strong><?php _e('Contract Terms:', 'cqpim'); ?> </strong> <a href="<?php echo get_edit_post_link($project_contract); ?>" target="_blank"><?php echo $terms->post_title; ?></a><br />
		<?php } ?>
		<?php if($start_date) { 
		if(is_numeric($start_date)) { $start_date = date(get_option('cqpim_date_format'), $start_date); } else { $start_date = $start_date; } ?>
		<strong><?php _e('Start Date:', 'cqpim'); ?> </strong><?php echo $start_date; ?><br />
		<?php } ?>
		<?php if($finish_date) { 
		if(is_numeric($finish_date)) { $finish_date = date(get_option('cqpim_date_format'), $finish_date); } else { $finish_date = $finish_date; } ?>
		<strong><?php _e('Launch Date:', 'cqpim'); ?> </strong><?php echo $finish_date; ?>
		<?php } ?>		
		<?php if(current_user_can('cqpim_edit_project_dates')) { ?>
			<button id="edit-quote-details" class="cqpim_button font-white bg-blue block mt-10 rounded_2"><?php _e('Edit Project Details / Dates', 'cqpim'); ?></button>
			<div class="clear"></div>
		<?php } ?>
	<?php } ?>
	</p>
	<p class="underline"><strong><?php _e('Project Status', 'cqpim'); ?></strong></p>
	<table class="quote_status">
	<?php if(current_user_can('cqpim_view_project_contract') && !empty($client_id)) { 
		$checked = get_option('enable_project_contracts'); ?>
		<?php if($contract_status == 1 || !empty($deposit_amount) && $deposit_amount != 'none') { ?>
			<tr>
				<td class="title"><span class="sbold"><?php _e('Prerequisites', 'cqpim'); ?></span></td>
				<td></td>
			</tr>
		<?php } ?>
		<?php if($contract_status == 1) { ?>
			<tr>
				<?php $classes = ( !empty( $sent ) ) ? 'green' : 'red'; ?>
				<td class="title"><?php _e('Contract Sent', 'cqpim'); ?></td>
				<td class="<?php echo $classes; ?>"></td>
			</tr>
			<tr>
				<?php $classes = ( !empty( $confirmed ) ) ? 'green' : 'red'; ?>
				<td class="title"><?php _e('Contract Signed', 'cqpim'); ?></td>
				<td class="<?php echo $classes; ?>"></td>
			</tr>
		<?php } ?>
	<?php } ?>
		<?php if(current_user_can('edit_cqpim_invoices') && !empty($client_id) && get_option('disable_invoices') != 1) { ?>
			<?php if($deposit_amount && $deposit_amount != 'none') { ?>
				<tr>
					<?php $classes = ( !empty( $deposit_invoice_id ) ) ? 'green' : 'red'; ?>
					<td class="title"><?php _e('Deposit Invoice Created', 'cqpim'); ?></td>
					<td class="<?php echo $classes; ?>"></td>
				</tr>
				<tr>
					<?php $classes = ( !empty( $deposit_sent ) ) ? 'green' : 'red'; ?>
					<td class="title"><?php _e('Deposit Invoice Sent', 'cqpim'); ?></td>
					<td class="<?php echo $classes; ?>"></td>
				</tr>
				<tr>
					<?php $classes = ( !empty( $deposit_paid ) ) ? 'green' : 'red'; ?>
					<td class="title"><?php _e('Deposit Paid', 'cqpim'); ?></td>
					<td class="<?php echo $classes; ?>"></td>
				</tr>
				<?php if($contract_status == 2 && empty($deposit_invoice_id)) { ?>
					<tr>
						<td colspan="2"><button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="send_deposit" data-id="<?php echo $post->ID; ?>"><?php _e('Generate Deposit Invoice', 'cqpim'); ?></button></td>
					</tr>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		<tr>
			<td class="title"><span class="sbold"><?php _e('Milestones', 'cqpim'); ?></span></td>
			<td></td>
		</tr>
		<?php
		if(!empty($project_elements)) {
			$ordered = array();
			$i = 0;
			$mi = 0;
			foreach($project_elements as $key => $element) {
				$weight = isset($element['weight']) ? $element['weight'] : $mi;
				$ordered[$weight] = $element;
				$mi++;
			}
			ksort($ordered);
			foreach($ordered as $element) {
			$status = isset($element['status']) ? $element['status'] : '';
			if(!empty($status) && $status == 'complete') {
				$classes = 'green';
			} else {
				$classes = 'red';
				$no_sign = true;
			} ?>
			<tr>
				<td class="title"><?php echo $element['title']; ?></td>
				<td class="<?php echo $classes; ?>"></td>
			</tr>				
		<?php }
		} else { ?>
			<tr>
				<td class="title"><?php _e('No milestones added', 'cqpim'); ?></td>
				<td class="red"></td>
			</tr>				
		<?php }
		?>
		<?php if(!empty($client_id)) { ?>
			<tr>
				<td class="title"><span class="sbold"><?php _e('Completion', 'cqpim'); ?></span></td>
				<td></td>
			</tr>
			<tr>
				<?php $classes = ( !empty( $signoff ) ) ? 'green' : 'red'; ?>
				<td class="title"><?php _e('Signed Off / Launched', 'cqpim'); ?></td>
				<td class="<?php echo $classes; ?>"></td>
			</tr>
			<?php 
			$checked = get_option('invoice_workflow');			
			$project_elements = get_post_meta($post->ID, 'project_elements', true);
			$project_elements = $project_elements&&is_array($project_elements)?$project_elements:array();
			$project_total = 0;
			foreach($project_elements as $element) {
				$element_cost = isset($element['cost']) ? $element['cost'] : 0;
				$cost = preg_replace("/[^\\d.]+/","", $element_cost);
				if(empty($cost)) {
					$cost = 0;
				}
				$project_total = $project_total + $cost;
			}
			if(current_user_can('edit_cqpim_invoices') && $checked != 1 && !empty($client_id) && $project_total > 0) { if(get_option('disable_invoices') != 1) {?>
			<tr>
				<?php $classes = ( !empty( $completion_invoice_id ) ) ? 'green' : 'red'; ?>
				<td class="title"><?php _e('Completion Invoice Created', 'cqpim'); ?></td>
				<td class="<?php echo $classes; ?>"></td>
			</tr>
			<tr>
				<?php $classes = ( !empty( $completion_sent ) ) ? 'green' : 'red'; ?>
				<td class="title"><?php _e('Completion Invoice Sent', 'cqpim'); ?></td>
				<td class="<?php echo $classes; ?>"></td>
			</tr>
			<tr>
				<?php $classes = ( !empty( $completion_paid ) ) ? 'green' : 'red'; ?>
				<td class="title"><?php _e('Completion Invoice Paid', 'cqpim'); ?></td>
				<td class="<?php echo $classes; ?>"></td>
			</tr>
		<?php } ?>
		<?php } } ?>
	</table>
	<?php
	$project_sent = isset($project_details['sent']) ? $project_details['sent'] : '';
	$project_accepted = isset($project_details['confirmed']) ? $project_details['confirmed'] : '';
	$signoff = isset($project_details['signoff']) ? $project_details['signoff'] : '';
	$closed = isset($project_details['closed']) ? $project_details['closed'] : '';
	$checked = get_option('enable_project_contracts'); 
	$contract_status = get_post_meta($post->ID, 'contract_status', true); 
	if($client_id) {
		if(!$closed) {
			if(!$signoff) {
				if($contract_status == 1) {
					if(!$project_accepted) {
						if(empty($project_sent)) {
							echo '<div class="cqpim-alert cqpim-alert-danger alert-display">';
							_e('The contract has not yet been sent to the client.', 'cqpim');
							echo '</div>';
							if(current_user_can('cqpim_view_project_contract') && !empty($client_id)) {
								echo '<button id="send_contract" class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" data-id="' . $post->ID . '">' . __('Send Contract', 'cqpim') . '</button>';
							}
						}
						if($project_sent) {
							$project_sent = $project_details['sent_details'];
							$to = isset($project_sent['to']) ? $project_sent['to'] : '';
							$by = isset($project_sent['by']) ? $project_sent['by'] : '';
							$at = isset($project_sent['date']) ? $project_sent['date'] : '';
							if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
							echo '<div class="cqpim-alert cqpim-alert-warning alert-display">';
							printf(__('This contract was sent to %1$s on %2$s by %3$s', 'cqpim'), $to, $at, $by);
							echo '</div>';
							if(current_user_can('cqpim_view_project_contract') && !empty($client_id)) {
								echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="send_contract" data-id="' . $post->ID . '">' . __('Resend Contract', 'cqpim') . '</button>';
							}
						}
					} else {
						if(current_user_can('cqpim_view_project_contract') && !empty($client_id)) { ?>
							<p class="underline"><strong><?php _e('Resend Contract', 'cqpim'); ?></strong> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php _e('If you have made changes to this project, such as adding milestones, changing dates or costs, you should resend it to the client for acceptance', 'cqpim'); ?>"></i></p>
							<?php echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="send_contract" data-id="' . $post->ID . '">' . __('Resend Contract', 'cqpim') . '</button>';
						}
						$project_accepted = $project_details['confirmed_details'];
						$ip = isset($project_accepted['ip']) ? $project_accepted['ip'] : '';
						$by = isset($project_accepted['by']) ? $project_accepted['by'] : '';
						$at = isset($project_accepted['date']) ? $project_accepted['date'] : '';
						if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
						echo '<div class="cqpim-alert cqpim-alert-success alert-display">';
						printf(__('The contract was accepted by %1$s on %2$s from IP Address %3$s', 'cqpim'), $by, $at, $ip);
						echo '</div>';
						if(!$signoff) {
							if(current_user_can('cqpim_mark_project_signedoff')) {
								if(!empty($no_sign)) {
									$disabled = 'disabled="disabled"';
								} else {
									$disabled = '';
								}
								echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="signed_off" ' . $disabled . ' data-id="' . $post->ID . '">' . __('Mark as Signed Off', 'cqpim') . '</button>';
								if(!empty($no_sign)) {
									echo '<div class="cqpim-alert cqpim-alert-warning alert-display">';
									_e('The project cannot be signed off until all Milestones are complete.', 'cqpim');
									echo '</div>';									
								}
							}
						}
					}	
				} else {
					echo '<div class="cqpim-alert cqpim-alert-success alert-display">';
					_e('Project in Progress', 'cqpim');
					echo '</div>';	
					if(!$signoff) {
						if(!empty($no_sign)) {
							$disabled = 'disabled="disabled"';
						} else {
							$disabled = '';
						}
						if(current_user_can('cqpim_mark_project_signedoff')) {
							echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="signed_off" ' . $disabled . ' data-id="' . $post->ID . '">' . __('Mark as Signed Off', 'cqpim') . '</button>';
						}
						if(!empty($no_sign)) {
							echo '<div class="cqpim-alert cqpim-alert-warning alert-display">';
							_e('The project cannot be signed off until all Milestones are complete.', 'cqpim');
							echo '</div>';									
						}
					}						
				}
			} else {
				$project_signedoff = $project_details['signoff_details'];
				$by = isset($project_signedoff['by']) ? $project_signedoff['by'] : '';
				$at = isset($project_signedoff['at']) ? $project_signedoff['at'] : '';	
				if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
				echo '<div class="cqpim-alert cqpim-alert-success alert-display">';
				printf(__('This project was signed off by %1$s on %2$s', 'cqpim'), $by, $at);
				echo '</div>';
				if(!$closed) {
					$value = get_option('invoice_workflow');
					if($value != 1) {
						if(current_user_can('cqpim_mark_project_signedoff')) {
							echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="unsigned_off" data-id="' . $post->ID . '">' . __('Remove Signed-Off Status', 'cqpim') . '</button>';
						}
					}
					if(current_user_can('cqpim_mark_project_closed')) {
						echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="close_off" data-id="' . $post->ID . '">' . __('Close Project', 'cqpim') . '</button>';
					}
				}			
			}
		} else {
			$project_closed = $project_details['closed_details'];
			$by = isset($project_closed['by']) ? $project_closed['by'] : '';
			$at = isset($project_closed['at']) ? $project_closed['at'] : '';
			if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }				
			echo '<div class="cqpim-alert cqpim-alert-info alert-display">';
			printf(__('This project was closed by %1$s on %2$s', 'cqpim'),$by, $at);
			echo '</div>';	
			if(current_user_can('cqpim_mark_project_closed')) {
				echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="unclose_off" data-id="' . $post->ID . '">' . __('Re-open Project', 'cqpim') . '</button>';
			}
		} 
	} else {
		if(!$closed) {
			echo '<div class="alert-display cqpim-alert cqpim-alert-info">';
			_e('This is not a client project, no contract needs to be signed.', 'cqpim');
			echo '</div>';
			if(!$closed) {
				if(current_user_can('cqpim_mark_project_closed')) {
					echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="close_off" data-id="' . $post->ID . '">' . __('Close Project', 'cqpim') . '</button>';
				}
			}
		} else {
			$project_closed = $project_details['closed_details'];
			$by = isset($project_closed['by']) ? $project_closed['by'] : '';
			$at = isset($project_closed['at']) ? $project_closed['at'] : '';
			if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }				
			echo '<div class="cqpim-alert cqpim-alert-info alert-display">';
			printf(__('This project was closed by %1$s on %2$s', 'cqpim'),$by, $at);
			echo '</div>';	
			if(current_user_can('cqpim_mark_project_closed')) {
				echo '<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block" id="unclose_off" data-id="' . $post->ID . '">' . __('Re-open Project', 'cqpim') . '</button>';
			}	
		}
	}
	if(!empty($checked)) {
		if(current_user_can('cqpim_view_project_contract') && !empty($client_id) && empty($client_contract)) { ?>
			<a class="cqpim_button cqpim_button_link font-white bg-blue mt-10 rounded_2 block" href="<?php echo $contract_link; ?>" target="_blank"><?php _e('Preview Contract', 'cqpim'); ?></a>
	<?php } 
	} ?>
	<input type="hidden" id="project_ref_for_basics" value="<?php echo $quote_ref; ?>" />
	<button class="cqpim_button font-white bg-blue block mt-10 rounded_2 block save"><?php _e('Update Project', 'cqpim'); ?></button>
	<div id="messages"></div>	
	<div id="quote_unsign_container" style="display:none">
		<div id="quote_unsign">
			<div style="padding:12px">
				<h3><?php _e('Remove Signed-off Status', 'cqpim'); ?></h3>
				<p><?php _e('Removing Signed-off status will also delete the completion invoice, if one is present. The invoice will be regenerated when the project is signed off again.', 'cqpim'); ?>
				<br /><br />
				<div id="unsign-error"></div>
				<button class="cancel-colorbox cqpim_button font-red border-red op"><?php _e('Cancel', 'cqpim'); ?></button>
				<button class="save-unsigned cqpim_button font-green border-green op right"><?php _e('Remove Signed-off Status', 'cqpim'); ?></button>
			</div>
		</div>
	</div>			
	<div id="quote_basics_container" style="display:none">
		<div id="quote_basics">
			<div style="padding:12px">
				<h3><?php _e('Project Details', 'cqpim'); ?></h3>
				<p><?php _e('Project Title: ', 'cqpim'); ?></p>
				<input type="text" name="ptitle" value="<?php echo $post->post_title; ?>" />
				<?php
				$args = array(
					'post_type' => 'cqpim_client',
					'posts_per_page' => -1,
					'post_status' => 'private',
				);
				$clients = get_posts($args);
				echo '<p>' . __('Please choose a client to assign this project to. Leave this blank if you do not want to assign this project to a Client', 'cqpim') . '</p>';
				echo '<select class="quote_client_dropdown" name="quote_client" id="quote_client" required >';
				echo '<option value="0">' . __('Select a Client... ', 'cqpim') . '</option>';
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
				$quote_details = get_post_meta($post->ID, 'project_details', true);
				$client_id = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
				$client_details = get_post_meta($client_id, 'client_details', true);
				$client_contact = isset($quote_details['client_contact']) ? $quote_details['client_contact'] : '';
				$client_contacts = get_post_meta($client_id, 'client_contacts', true);
				$client_contacts = $client_contacts&&is_array($client_contacts)?$client_contacts:array();
				if(!empty($client_id) && !empty($client_contact)) { ?>
					<select name="client_contact" id="client_contact">
						<option value=""><?php _e('Choose a Contact', 'cqpim'); ?></option>
						<?php if(!empty($client_details['user_id']) && !empty($client_contact)) { ?>
							<option value="<?php echo $client_details['user_id']; ?>" <?php if($client_contact == $client_details['user_id']) { echo 'selected="selected"'; } ?>><?php echo $client_details['client_contact']; ?> <?php _e('(Main Contact)', 'cqpim'); ?></option>
							<?php 
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
				<p><?php _e('Project Ref: ', 'cqpim'); ?></p>
				<?php if(!$quote_ref) {
					$quote_ref = $post->ID;
				} ?>
				<input type="text" name="quote_ref" id="quote_ref" value="<?php echo $quote_ref; ?>" required />
				<p><?php _e('Proposed Start/Launch Dates:', 'cqpim'); ?> </p>
				<input class="datepicker" type="text" name="start_date" id="start_date" value="<?php echo $start_date; ?>" />
				<input class="datepicker" type="text" name="finish_date" id="finish_date" value="<?php echo $finish_date; ?>" />	
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
				<?php $contract = isset($project_details['default_contract_text']) ? $project_details['default_contract_text'] : ''; 
				$default = get_option( 'default_contract_text' );
				$checked = get_option('enable_project_contracts');
				if(!empty($checked)) { ?>
				<p><?php _e('Project Contract Terms & Conditions Template', 'cqpim'); ?> </p>
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
				<?php } ?>
				<br /><br />
				<div id="basics-error"></div>
				<a class="cancel-creation cqpim_button border-red font-red op" href="<?php echo admin_url(); ?>admin.php?page=pto-dashboard"><?php _e('Cancel', 'cqpim'); ?></a>
				<button class="save-basics cqpim_button border-green font-green op right"><?php _e('Save', 'cqpim'); ?></button>
			</div>
		</div>
	</div>	
	<?php
}
add_action( 'save_post', 'save_pto_project_details_metabox_data' );
function save_pto_project_details_metabox_data( $post_id ){
	if ( ! isset( $_POST['project_details_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['project_details_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'project_details_metabox' ) )
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
	$project_colours = array();
	$project_colours['project_colour'] = isset($_POST['project_colour']) ? $_POST['project_colour'] : '';
	$project_colours['ms_colour'] = isset($_POST['ms_colour']) ? $_POST['ms_colour'] : '';
	$project_colours['task_colour'] = isset($_POST['task_colour']) ? $_POST['task_colour'] : '';
	update_post_meta($post_id, 'project_colours', $project_colours);
	
	$quote_details = get_post_meta($post_id, 'project_details', true);
	$quote_details = $quote_details&&is_array($quote_details)?$quote_details:array();
	if(isset($_POST['start_date'])) {
		$start_date = $_POST['start_date'];
		$timestamp = pto_convert_date($start_date);
		$quote_details['start_date'] = $timestamp;
	}
	if(isset($_POST['finish_date'])) {
		$finish_date = $_POST['finish_date'];
		$timestamp = pto_convert_date($finish_date);
		$quote_details['finish_date'] = $timestamp;
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
	if(isset($_POST['client_contact'])) {
		$client_contact = $_POST['client_contact'];
		$quote_details['client_contact'] = $_POST['client_contact'];
	}
	if(isset($_POST['default_contract_text'])) {
		$default_contract_text = $_POST['default_contract_text'];
		$quote_details['default_contract_text'] = $_POST['default_contract_text'];
	} else {
		$quote_details['default_contract_text'] = get_option( 'default_contract_text' );
	}
	if(isset($_POST['quote_ref'])) {
		$quote_details['quote_ref'] = $_POST['quote_ref'];
	}
	if(isset($_POST['deposit_amount'])) {
		$deposit = $_POST['deposit_amount'];
		$quote_details['deposit_amount'] = $deposit;
	}
	update_post_meta($post_id, 'project_details', $quote_details);
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
	if(!empty($_POST['ptitle'])) {
		$quote_updated = array(
			'ID' => $post_id,
			'post_title' => $_POST['ptitle'],
			'post_name' => $post_id,
		);
		if ( ! wp_is_post_revision( $post_id ) ){
			remove_action('save_post', 'save_pto_project_details_metabox_data');
			wp_update_post( $quote_updated );
			add_action('save_post', 'save_pto_project_details_metabox_data');
		}	
	}
	$contract_status = get_post_meta($post_id, 'contract_status', true);
	if(empty($contract_status)) {
		$contract = pto_get_contract_status($post_id);
		update_post_meta($post_id, 'contract_status', $contract);
	}
}