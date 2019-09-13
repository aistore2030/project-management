<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-cog font-green-sharp" aria-hidden="true"></i> <?php _e('My Profile', 'cqpim'); ?>
		</div>
	</div>
	<div class="cqpim-dash-item-inside">
		<?php 
		$user = wp_get_current_user();
		$client_logs = get_post_meta($assigned, 'client_logs', true);
		if(empty($client_logs)) {
			$client_logs = array();
		}
		$now = time();
		$client_logs[$now] = array(
			'user' => $user->ID,
			'page' => __('Client Dashboard Settings Page', 'cqpim')
		);
		update_post_meta($assigned, 'client_logs', $client_logs);
		$client_settings = get_option('allow_client_settings');
		$data = get_option('cqpim_custom_fields_client');
		$data = str_replace('\"', '"', $data);
		if(!empty($data)) {
			$form_data = json_decode($data);
			$fields = $form_data;
		}
		$values = get_post_meta($assigned, 'custom_fields', true);
		$frontend = get_post_meta($assigned, 'field_frontend', true);
		if($client_settings == 1) { ?>
			<div class="cqpim-dash-item-inside">
				<div id="cqpim_backend_quote">
					<?php
						if($client_type == 'admin') {
							$client_details = get_post_meta($assigned, 'client_details', true);
						} else {
							$client_details = get_post_meta($assigned, 'client_details', true);
							$client_contacts = get_post_meta($assigned, 'client_contacts', true);
							foreach($client_contacts as $key => $contact) {
								if($key == $user_id) {
									$client_details['client_telephone'] = $contact['telephone'];
									$client_details['client_contact'] = $contact['name'];
									$client_details['client_email'] = $contact['email'];
								}
							}
						}
					?>
					<form id="client_settings">
						<label for="client_email"><?php _e('Email Address', 'cqpim'); ?></label>
						<input style="width:98%; padding:1%" type="text" id="client_email" name="client_email" value="<?php echo $user->user_email; ?>" required />
						<label for="client_phone"><?php _e('Telephone', 'cqpim'); ?></label>
						<input style="width:98%; padding:1%" type="text" id="client_phone" name="client_phone" value="<?php echo isset($client_details['client_telephone']) ? $client_details['client_telephone'] : ''; ?>" required />
						<label for="client_email"><?php _e('Display Name', 'cqpim'); ?></label>
						<input style="width:98%; padding:1%" type="text" id="client_name" name="client_name" value="<?php echo $user->display_name; ?>" required />
						<label for="company_name"><?php _e('Company Name', 'cqpim'); ?></label>
						<input style="width:98%; padding:1%" type="text" id="company_name" name="company_name" value="<?php echo isset($client_details['client_company']) ? $client_details['client_company'] : ''; ?>" required />
						<label for="company_address"><?php _e('Company Address', 'cqpim'); ?></label>
						<textarea style="width:98%; padding:1%; height:100px" id="company_address" name="company_address" required ><?php echo isset($client_details['client_address']) ? $client_details['client_address'] : ''; ?></textarea>
						<label for="company_postcode"><?php _e('Company Postcode', 'cqpim'); ?></label>
						<input style="width:98%; padding:1%" type="text" id="company_postcode" name="company_postcode" value="<?php echo isset($client_details['client_postcode']) ? $client_details['client_postcode'] : ''; ?>" required />							
						
						<?php
						if(!empty($fields)) {
							$i = 0;
							foreach($fields as $field) {
								if($field->type == 'header') {
									$field->name = 'header-' . $i;
									$i++;
								}
							}
							echo '<div id="cqpim-custom-fields">';
							foreach($fields as $field) {
								if(!empty($frontend[$field->name])) {
									$value = isset($values[$field->name]) ? $values[$field->name] : '';
									$id = strtolower($field->label);
									$id = str_replace(' ', '_', $id);
									$id = str_replace('-', '_', $id);
									$id = preg_replace('/[^\w-]/', '', $id);
									if(!empty($field->required) && $field->required == 1) {
										$required = 'required';
										$ast = '<span style="color:#F00">*</span>';
									} else {
										$required = '';
										$ast = '';
									}
									echo '<div style="padding-bottom:12px" class="cqpim_form_item">';
									if($field->type != 'header') {
										echo '<label style="display:block; padding-bottom:5px" for="' . $id . '">' . $field->label . ' ' . $ast . '</label>';
									}
									if($field->type == 'header') {
										echo '<' . $field->subtype . ' class="' . $field->className . '">' . $field->label . '</' . $field->subtype . '>';
									} elseif($field->type == 'text') {			
										echo '<input type="text" class="cqpim-custom ' . $field->className . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
									} elseif($field->type == 'website') {
										echo '<input type="url" class="cqpim-custom ' . $field->className . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
									} elseif($field->type == 'number') {
										echo '<input type="number" class="cqpim-custom ' . $field->className . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
									} elseif($field->type == 'textarea') {
										echo '<textarea class="cqpim-custom ' . $field->className . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '">' . $value . '</textarea>';
									} elseif($field->type == 'date') {
										echo '<input class="cqpim-custom ' . $field->className . ' datepicker" type="text" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
									} elseif($field->type == 'email') {
										echo '<input type="email" class="cqpim-custom ' . $field->className . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
									} elseif($field->type == 'checkbox-group') {
										if(!is_array($value)) {
											$value = array($value);
										}
										$options = $field->values;
										foreach($options as $option) {
											if(in_array($option->value, $value)) {
												$checked = 'checked="checked"';
											} else {
												$checked = '';
											}
											echo '<input type="checkbox" class="cqpim-custom ' . $field->className . '" value="' . $option->value . '" name="' . $field->name . '" ' . $checked . ' /> ' . $option->label . '<br />';
										}
									} elseif($field->type == 'radio-group') {
										$options = $field->values;
										foreach($options as $option) {
											if($value == $option->value) {
												$checked = 'checked="checked"';
											} else {
												$checked = '';
											}
											echo '<input type="radio" class="cqpim-custom ' . $field->className . '" value="' . $option->value . '" name="' . $field->name . '" ' . $required . ' ' . $checked . ' /> ' . $option->label . '<br />';
										}
									} elseif($field->type == 'select') {
										$options = $field->values;
										echo '<select class="cqpim-custom ' . $field->className . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '">';
											foreach($options as $option) {	
												if($value == $option->value) {
													$checked = 'selected="selected"';
												} else {
													$checked = '';
												}
												echo '<option value="' . $option->value . '" ' . $checked . '>' . $option->label . '</option>';
											}
										echo '</select>';
									}
									if(!empty($field->other) && $field->other == 1) {
										echo '<br />';
										echo __('Other:', 'cqpim') . '<input class="cqpim-custom" style="width:100%" type="text" id="' . $id . '_other" name="' . $field->name . '_other" />';
									}
									if(!empty($field->description)) {
										echo '<span class="cqpim-field-description">' . $field->description . '</span>';
									}
									echo '</div>';
								}
							}
							echo '</div>';
						}
						?>						
						
						
						
						<h4 style="margin-top:20px"><?php _e('Change Password', 'cqpim'); ?></h3>
						<label for="client_pass"><?php _e('New Password', 'cqpim'); ?></label>
						<input style="width:98%; padding:1%" type="password" id="client_pass" name="client_pass" value="" />
						<label for="client_pass_rep"><?php _e('Repeat New Password', 'cqpim'); ?></label>
						<input style="width:98%; padding:1%" type="password" id="client_pass_rep" name="client_pass_rep" value=""  />
						<h4 style="margin-top:20px"><?php _e('Email Notification Preferences', 'cqpim'); ?></h4>
						<p><strong><?php _e('Tasks', 'cqpim'); ?></strong></p>
						<?php 
						if($client_type == 'admin') { 
							$notifications = get_post_meta($assigned, 'client_notifications', true);
						} else {
							$client_contacts = get_post_meta($assigned, 'client_contacts', true);
							$notifications = $client_contacts[$user->ID]['notifications'];
						}
						$no_tasks = isset($notifications['no_tasks']) ? $notifications['no_tasks']: 0;
						$no_tasks_comment = isset($notifications['no_tasks_comment']) ? $notifications['no_tasks_comment']: 0;
						$no_tickets = isset($notifications['no_tickets']) ? $notifications['no_tickets']: 0;
						$no_tickets_comment = isset($notifications['no_tickets_comment']) ? $notifications['no_tickets_comment']: 0;
						?>
						<input type="checkbox" name="no_tasks" id="no_tasks" value="1" <?php if($no_tasks == 1) { echo 'checked="checked"'; } ?> /> <?php _e('Disable all task notification emails.', 'cqpim'); ?>
						<br />
						<input type="checkbox" name="no_tasks_comment" id="no_tasks_comment" value="1" <?php if($no_tasks_comment == 1) { echo 'checked="checked"'; } ?>  /> <?php _e('Only notify me if a task has a new comment added.', 'cqpim'); ?>
						<br />
						<p><strong><?php _e('Support Tickets', 'cqpim'); ?></strong></p>
						<input type="checkbox" name="no_tickets" id="no_tickets" value="1" <?php if($no_tickets == 1) { echo 'checked="checked"'; } ?>  /> <?php _e('Disable all ticket notification emails.', 'cqpim'); ?>
						<br />
						<input type="checkbox" name="no_tickets_comment" id="no_tickets_comment" value="1" <?php if($no_tickets_comment == 1) { echo 'checked="checked"'; } ?>  /> <?php _e('Only notify me if a ticket has a new comment added.', 'cqpim'); ?>
						<br />
						<input style="width:100%" type="hidden" id="client_type" name="client_type" value="<?php echo $client_type; ?>" />
						<input style="width:100%" type="hidden" id="client_object" name="client_object" value="<?php echo $assigned; ?>" />
						<input style="width:100%" type="hidden" id="client_user_id" name="client_user_id" value="<?php echo $user->ID; ?>" />
						<br />
						<input style="float:left; margin-left:0" type="submit" id="client_settings_submit" class="cqpim_button bg-blue font-white mt-20 rounded_2" value="<?php _e('Update Settings', 'cqpim'); ?>" />
						<div class="clear"></div>
						<div id="settings_spinner" style="clear:both; display:none; background:url(<?php echo PTO_PATH; ?>includes/css/img/ajax-loader.gif) center center no-repeat; width:16px; height:16px; padding:10px 0 0 5px; margin-top:15px"></div>
						<div class="clear"></div>
						<br />
						<div id="settings_messages"></div>
					</form>
				</div>
			</div>
		<?php } ?>	
	</div>
</div>	