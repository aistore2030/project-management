<?php
add_action( 'admin_menu' , 'register_pto_recinvoice_page', 28 ); 
function register_pto_recinvoice_page() {
	$mypage = add_submenu_page(	
				'pto-dashboard',
				__('Recurring Invoices', 'cqpim'), 			
				__('Recurring Invoices', 'cqpim'),  			
				'edit_cqpim_invoices', 			
				'pto-recinvoices', 		
				'pto_recinvoices'
	);
	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
}
function pto_recinvoices() { ?>
	<div class="tasks-box" style="padding-right:20px">
		<br />
		<div class="cqpim_block cqpim_roles">
			<div class="cqpim_block_title">
				<div class="caption">
					<i class="fa fa-credit-card-alt font-green-sharp" aria-hidden="true"></i>
					<span class="caption-subject font-green-sharp sbold"><?php _e('Recurring Invoices', 'cqpim'); ?> </span>
				</div>
				<div class="actions"></div>
			</div>
			<?php $recurring_invoices = pto_get_recurring_invoices();
			if(empty($recurring_invoices)) {
				echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('No recurring invoices found...', 'cqpim') . '</div>';
			} else {
				echo '<table class="datatable_style dataTable-RIV">';
				echo '<thead>';
				echo '<tr><th>' . __('Title', 'cqpim') . '</th><th>' . __('Client', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th><th>' . __('Start', 'cqpim') . '</th><th>' . __('End', 'cqpim') . '</th><th>' . __('Frequency', 'cqpim') . '</th><th>' . __('Last Issue', 'cqpim') . '</th><th>' . __('Next Issue', 'cqpim') . '</th><th>' . __('Actions', 'cqpim') . '</th></tr>';
				echo '</thead>';
				echo '<tbody>';
				foreach($recurring_invoices as $key => $invoice) {
					$start = isset($invoice['start']) ? $invoice['start'] : '';
					if(is_numeric($start)) { $start = date(get_option('cqpim_date_format') . ' H:i', $start); } else { $start = $start; }
					if(empty($start)) {
						$start = __('N/A', 'cqpim');
					}
					if($invoice['auto'] == 1) {
						$auto = '<span class="task_complete">' . __('Yes', 'cqpim') . '</span>';
					} else {
						$auto = '<span class="task_pending">' . __('No', 'cqpim') . '</span>';
					}
					if($invoice['status'] == 1) {
						$status = '<span class="cqpim_button cqpim_xs_button border-green font-green op nolink">' . __('Active', 'cqpim') . '</span>';
					} else {
						$status = '<span class="cqpim_button cqpim_xs_button border-red font-red op nolink">' . __('Disabled', 'cqpim') . '</span>';
					} 
					if(empty($invoice['end'])) {
						$end = 'Ongoing';
					} else {
						$end = $invoice['end'];
					} 
					if(is_numeric($end)) { $end = date(get_option('cqpim_date_format') . ' H:i', $end); } else { $end = $end; }
					if($invoice['next_run'] != '<span class="task_over">' . __('Finished', 'cqpim') . '</div>') {
						$next = date(get_option('cqpim_date_format') . ' H:i', $invoice['next_run']);
					} else {
						$next = '<span class="cqpim_button cqpim_xs_button border-amber font-amber op nolink">' . __('Finished', 'cqpim') . '</div>';
					} 
					$client_obj = get_post($invoice['client_id']);?>
					<tr>
						<td><span class="cqpim_mobile"><?php _e('Title:', 'cqpim'); ?></span> <?php echo $invoice['title']; ?></td>
						<td><span class="cqpim_mobile"><?php _e('Client:', 'cqpim'); ?></span> <a href="<?php echo get_edit_post_link($invoice['client_id']); ?>"><?php echo $client_obj->post_title; ?></a></td>
						<td><span class="cqpim_mobile"><?php _e('Status:', 'cqpim'); ?></span> <?php echo $status; ?></td>
						<td data-order="<?php echo $invoice['start']; ?>"><span class="cqpim_mobile"><?php _e('Start:', 'cqpim'); ?></span> <?php echo $start; ?></td>
						<td data-order="<?php echo $invoice['end']; ?>"><span class="cqpim_mobile"><?php _e('End:', 'cqpim'); ?></span> <?php echo $end; ?></td>
						<td><span class="cqpim_mobile"><?php _e('Frequency:', 'cqpim'); ?></span> <?php echo ucfirst(isset($invoice['frequency']) ? $invoice['frequency'] : ''); ?></td>
						<td data-order="<?php echo isset($invoice['last_run']) ? $invoice['last_run'] : 0; ?>"><span class="cqpim_mobile"><?php _e('Last Issue:', 'cqpim'); ?></span> <?php echo isset($invoice['last_run']) ? date(get_option('cqpim_date_format'), $invoice['last_run']) : 'N/A'; ?></td>
						<td data-order="<?php echo $invoice['next_run']; ?>"><span class="cqpim_mobile"><?php _e('Next Issue:', 'cqpim'); ?></span> <?php echo $next; ?></td>
						<td><?php if($invoice['status'] == 1) { ?><button class="edit_rec cqpim_button cqpim_small_button font-amber border-amber" value="<?php echo $key; ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button> <?php } ?><button class="delete_rec cqpim_button cqpim_small_button font-red border-red" value="<?php echo $invoice['invoice_key']; ?>" data-client="<?php echo $invoice['client_id']; ?>"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
					</tr>
				<?php }
				echo '</tbody>';
				echo '</table>';
			}
			if(empty($recurring_invoices)) {
				$recurring_invoices = array();
			}
			foreach($recurring_invoices as $key => $invoice) { 
			$start = isset($invoice['start']) ? $invoice['start'] : '';
			if(is_numeric($start)) { $start = date(get_option('cqpim_date_format'), $start); } else { $start = $start; }
			$end = isset($invoice['end']) ? $invoice['end'] : ''; 
			if(is_numeric($end)) { $end = date(get_option('cqpim_date_format'), $end); } else { $end = $end; }?>
			<div style="display:none" id="edit-recurring-invoice-container-<?php echo $key; ?>">
				<div id="edit-recurring-invoice-<?php echo $key; ?>" class="edit-rec-inv" style="width:830px">
					<div style="padding:12px">
						<h3><?php printf(__('Edit %1$s', 'cqpim'), $invoice['title']); ?></h3>
						<div style="float:left; width:390px">
						<p><strong><?php _e('Title', 'cqpim'); ?></strong></p>
						<input type="text" id="rec-inv-title-<?php echo $key; ?>" value="<?php echo isset($invoice['title']) ? $invoice['title'] : ''; ?>" />
						<p><strong><?php _e('Contact', 'cqpim'); ?></strong></p>
						<select id="client_contact_select_<?php echo $key; ?>">
							<option value="">Choose a contact...</option>
							<?php 
							$client_details = get_post_meta($invoice['client_id'], 'client_details', true);
							if($client_details['user_id'] == $invoice['contact']) {
								$selected = 'selected="selected"';
							} else {
								$selected = '';
							}
							echo '<option value="' . $client_details['user_id'] . '" ' . $selected . '>' . $client_details['client_contact'] . ' ' . __('(Main contact)', 'cqpim') . '</option>';
							$selected = '';
							$contacts = get_post_meta($invoice['client_id'], 'client_contacts', true);
							foreach($contacts as $contact) {
								if($contact['user_id'] == $invoice['contact']) {
									$selected = 'selected="selected"';
								} else {
									$selected = '';
								}								
								echo '<option value="' . $contact['user_id'] . '" ' . $selected . '>' . $contact['name'] . '</option>';
							}
							?>
						</select>
						<p><strong><?php _e('Start/End', 'cqpim'); ?></strong> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php _e('You can specify a Start/End date for this invoice. If you leave the start blank then the invoice will start sending today. If you leave End blank, then the invoice will send according to the frequency until you delete or disable it.', 'cqpim'); ?>"></i></p>
						<input style="width:48%; margin-right:2%" type="text" class="datepicker" id="rec-inv-start-<?php echo $key; ?>" value="<?php echo $start; ?>" placeholder="Start" />
						<input style="width:48%;" type="text" class="datepicker" id="rec-inv-end-<?php echo $key; ?>" value="<?php echo $end; ?>" placeholder="End" />
						<div style="float:left; width:51%">
							<p><strong><?php _e('Frequency', 'cqpim'); ?></strong></p>
							<select id="rec-inv-frequency-<?php echo $key; ?>">
								<option value=""><?php _e('Choose a frequency', 'cqpim'); ?></option>
								<option value="daily" <?php if($invoice['frequency'] == 'daily') { echo 'selected="selected"'; } ?>><?php _e('Daily', 'cqpim'); ?></option>
								<option value="weekly" <?php if($invoice['frequency'] == 'weekly') { echo 'selected="selected"'; } ?>><?php _e('Weekly', 'cqpim'); ?></option>
								<option value="biweekly" <?php if($invoice['frequency'] == 'biweekly') { echo 'selected="selected"'; } ?>><?php _e('Fortnightly', 'cqpim'); ?></option>
								<option value="monthly" <?php if($invoice['frequency'] == 'monthly') { echo 'selected="selected"'; } ?>><?php _e('Monthly', 'cqpim'); ?></option>
								<option value="bimonthly" <?php if($invoice['frequency'] == 'bimonthly') { echo 'selected="selected"'; } ?>><?php _e('Bi Monthly', 'cqpim'); ?></option>
								<option value="threemonthly" <?php if($invoice['frequency'] == 'threemonthly') { echo 'selected="selected"'; } ?>><?php _e('Every Three Months', 'cqpim'); ?></option>
								<option value="sixmonthly" <?php if($invoice['frequency'] == 'sixmonthly') { echo 'selected="selected"'; } ?>><?php _e('Every Six Months', 'cqpim'); ?></option>
								<option value="yearly" <?php if($invoice['frequency'] == 'yearly') { echo 'selected="selected"'; } ?>><?php _e('Yearly', 'cqpim'); ?></option>
								<option value="biyearly" <?php if($invoice['frequency'] == 'biyearly') { echo 'selected="selected"'; } ?>><?php _e('Biyearly', 'cqpim'); ?></option>
							</select>
						</div>
						<div style="float:left; width:48%">
							<p><strong><?php _e('Status', 'cqpim'); ?></strong></p>
							<select id="rec-inv-status-<?php echo $key; ?>">
								<option value="1" <?php if($invoice['status'] == 1) { echo 'selected="selected"'; } ?>><?php _e('Active', 'cqpim'); ?></option>
								<option value="0" <?php if($invoice['status'] == 0) { echo 'selected="selected"'; } ?>><?php _e('Disabled', 'cqpim'); ?></option>
							</select>
						</div>
						<div class="clear"></div>
						<br />
						<input type="checkbox" id="rec-inv-auto-<?php echo $key; ?>" <?php if($invoice['auto'] == 1) { echo 'checked="checked"'; } ?> /> <?php _e('Send the recurring invoices to the client on creation.', 'cqpim'); ?><br />
						<input type="checkbox" id="rec-inv-partial-<?php echo $key; ?>" <?php if($invoice['partial'] == 1) { echo 'checked="checked"'; } ?> /> <?php _e('Allow partial payments on this invoice.', 'cqpim'); ?>
						</div>
						<div style="float:right; width:390px">
						<p><strong><?php _e('Invoice Items', 'cqpim'); ?></strong></p>
						<div class="repeater">	
							<?php if(!empty($invoice['items'])) { ?>
							<div data-repeater-list="group<?php echo $key; ?>-a">
								<?php foreach($invoice['items'] as $item) { ?>
									<div class="line_item" data-repeater-item>
										<table style="table-layout: fixed" class="milestones invoice-items">
											<tbody>
												<tr>
													<td style="width:10%"><input data-row="0" data-key="<?php echo $key; ?>" id="edit_<?php echo $key; ?>_invoice_qty" class="edit_<?php echo $key; ?>_invoice_qty" type="text" name="qty" value="<?php echo $item['qty']; ?>" placeholder="<?php _e('Qty', 'cqpim'); ?>" /></td>
													<td style="width:50%"><input data-row="0" data-key="<?php echo $key; ?>" id="edit_<?php echo $key; ?>_invoice_desc" class="edit_<?php echo $key; ?>_invoice_desc" type="text" name="desc" value="<?php echo $item['desc']; ?>" placeholder="<?php _e('Description', 'cqpim'); ?>" /></td>
													<td style="width:15%"><input data-row="0" data-key="<?php echo $key; ?>" id="edit_<?php echo $key; ?>_invoice_price" class="edit_<?php echo $key; ?>_invoice_price" type="text" name="price" value="<?php echo $item['price']; ?>" placeholder="<?php _e('Price', 'cqpim'); ?>" /></td>
													<td style="width:10%"><input data-row="0" data-key="<?php echo $key; ?>" class="line_delete bg-red cqpim_button cqpim_small_button rounded_2" data-repeater-delete type="button" value=""/></td>
												</tr>
											</tbody>
										</table>
									</div>
								<?php } ?>
							</div>
							<?php } else { ?>
							<div data-repeater-list="group-a">
								<div class="line_item" data-repeater-item>
									<table style="table-layout: fixed" class="milestones invoice-items">
										<tbody>
											<tr>
												<td style="width:10%"><input data-row="0" id="invoice_qty" class="invoice_qty" type="text" name="qty" value="" placeholder="<?php _e('Qty', 'cqpim'); ?>" /></td>
												<td style="width:50%"><input data-row="0" id="invoice_desc" class="invoice_desc" type="text" name="desc" value="" placeholder="<?php _e('Description', 'cqpim'); ?>" /></td>
												<td style="width:15%"><input data-row="0" id="invoice_price" class="invoice_price" type="text" name="price" value="" placeholder="<?php _e('Price', 'cqpim'); ?>" /></td>
												<td style="width:10%"><input data-row="0" class="line_delete bg-red cqpim_button cqpim_small_button rounded_2" data-repeater-delete type="button" value=""/></td>
											</tr>
										</tbody>
									</table>
								</div>				
							</div>							
							<?php } ?>
							<input class="add_line_item_row cqpim_button" data-repeater-create type="button" value=""/>
						</div>
						<br />
						<div class="edit-inv-messages"></div>
						<div class="clear"></div>
						<button class="cancel-colorbox cqpim_button font-red border-red op"><?php _e('Cancel', 'cqpim'); ?></button>
						<button class="edit-rec-inv-btn cqpim_button font-green border-green op right" value="<?php echo $invoice['client_id']; ?>" data-key="<?php echo $invoice['invoice_key']; ?>"><?php _e('Edit', 'cqpim'); ?><span id="edit-rec-inv-spinner-<?php echo $key; ?>" class="ajax_loader" style="display:none"></span></button>
						</div>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="clear"></div>
<?php } 