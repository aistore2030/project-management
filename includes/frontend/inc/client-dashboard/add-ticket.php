<br />
<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-life-ring font-green-sharp" aria-hidden="true"></i>
			<span class="caption-subject font-green-sharp sbold"><?php _e('Add Support Ticket', 'cqpim'); ?></span>
		</div>
	</div>
	<?php 
	$user = wp_get_current_user();
	$client_logs = get_post_meta($assigned, 'client_logs', true);
	if(empty($client_logs)) {
		$client_logs = array();
	}
	$now = current_time('timestamp');
	$client_logs[$now] = array(
		'user' => $user->ID,
		'page' => __('Client Dashboard Add Support Ticket Page', 'cqpim')
	);
	update_post_meta($assigned, 'client_logs', $client_logs);
	$string = pto_random_string(10);
	?>
	<?php 
	$show_open_warning = get_option('pto_support_opening_warning');
	if(!empty($show_open_warning)) {
		$open = pto_return_open();
		if($open == 1) {
			$message = get_option('pto_support_closed_message');
			echo '<div class="cqpim-alert cqpim-alert-warning alert-display">' . $message . '</div>';
		} else if($open == 2) {
			$message = get_option('pto_support_open_message');
			echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . $message . '</div>';
		}
	}
	?>
	<div id="cqpim_backend_quote">
		<br />
		<input type="hidden" name="action" value="new_ticket" />
		<?php
		include_once(ABSPATH.'wp-admin/includes/plugin.php');
		if(pto_check_addon_status('envato')) {	
			$messages = get_option('raise_support_messages');
			$messages = str_replace('%%TIME%%', current_time('l dS F, g:iA'), $messages);
			echo $messages;
			$items = get_option('cqpim_envato_items'); ?>
			<h4><?php _e('Which product do you need help with?', 'cqpim'); ?></h4>
			<select name="ticket_item" id="ticket_item" required >
				<option value=""><?php _e('Choose a product...', 'cqpim'); ?></option>
				<?php
				foreach($items as $item) {
					echo '<option value="' . $item['id'] . '">' . $item['name'] . '</option>';
				}
				?>
			</select>
			<div style="margin-top:20px" id="login_messages"></div>
			<input type="hidden" id="reject_reason" value="" />
			<?php
		}
		?>
		<h4><?php _e('Ticket Title:', 'cqpim'); ?></h4>
		<input type="text" id="ticket_title" required />
		<h4><?php _e('Ticket Priority:', 'cqpim'); ?></h4>
		<select id="ticket_priority_new" name="ticket_priority_new">
			<option value="low"><?php _e('Low', 'cqpim'); ?></option>
			<option value="normal"><?php _e('Normal', 'cqpim'); ?></option>
			<option value="high"><?php _e('High', 'cqpim'); ?></option>
			<option value="immediate"><?php _e('Immediate', 'cqpim'); ?></option>
		</select>	
		<?php
			$_SESSION['upload_ids'] = array();
			$_SESSION['ticket_changes'] = array();
		?>
		<h4><?php _e('Upload Files', 'cqpim'); ?></h4>
		<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
		<div id="upload_attachments"></div>
		<div class="clear"></div>
		<input type="hidden" name="image_id" id="upload_attachment_ids">
		<div class="clear"></div>
		<?php
		$data = get_option('cqpim_custom_fields_support');
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
				if($field->required == 1) {
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
					echo '<' . $field->subtype . ' class="cqpim-custom ' . $field->className . '">' . $field->label . '</' . $field->subtype . '>';
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
					echo __('Other:', 'cqpim') . '<input class="cqpim-custom " style="width:100%" type="text" id="' . $id . '_other" name="custom-field[' . $field->name . '_other]" />';
				}
				if(!empty($field->description)) {
					echo '<span class="cqpim-field-description">' . $field->description . '</span>';
				}
				echo '</div>';
			}
			echo '</div>';
		}
		?>
		<div class="clear"></div>
		<h4><?php _e('Details', 'cqpim'); ?></h4>				
		<textarea id="ticket_update_new" name="ticket_update_new" required ></textarea>
		<div class="clear"></div>
		<br /><br />
		<a id="support-submit" href="#" class="cqpim_button font-white bg-blue rounded_2 op" style="margin-right:0"><?php _e('Create Ticket', 'cqpim'); ?></a>
		<div class="clear"></div>
	<br />
</div>
</div>