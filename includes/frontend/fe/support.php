<?php 
include('header.php');
$client_details = get_post_meta($assigned, 'client_details', true);
$client_ids = get_post_meta($assigned, 'client_ids', true);
$client_ids_untouched = $client_ids;
if(empty($client_ids_untouched)) {
	$client_ids_untouched = array();
}
$looper = get_post_meta($post->ID, 'looper', true);
$looper = $looper?$looper:0;
if(time() - $looper > 5) {
	$user = wp_get_current_user();
	$client_logs = get_post_meta($assigned, 'client_logs', true);
	if(empty($client_logs)) {
		$client_logs = array();
	}
	$now = current_time('timestamp');
	$title = get_the_title();
	$title = str_replace('Private:', '', $title);
	$client_logs[$now] = array(
		'user' => $user->ID,
		'page' => sprintf(__('Support Ticket - %1$s', 'cqpim'), $title)
	);
	update_post_meta($assigned, 'client_logs', $client_logs);
	update_post_meta($post->ID, 'looper', time());
}
$user = wp_get_current_user(); 
$user_id = $user->ID;
$dash_page = get_option('cqpim_client_page');
$dash_url = get_the_permalink($dash_page);
$ticket_author = $post->post_author;
$author_details = get_user_by('id', $ticket_author);
$client_name = $author_details->display_name;
$ticket_status = get_post_meta($post->ID, 'ticket_status', true);
$ticket_priority = get_post_meta($post->ID, 'ticket_priority', true);
$ticket_updated = get_post_meta($post->ID, 'last_updated', true);
$ticket_client = get_post_meta($post->ID, 'ticket_client', true);
if(is_numeric($ticket_updated)) { $ticket_updated = date(get_option('cqpim_date_format') . ' H:i', $ticket_updated); } else { $ticket_updated = $ticket_updated; }
if($ticket_status == 'open') {
	$status = '<span class="cqpim_button cqpim_small_button font-amber border-amber op nolink">' . __('Open', 'cqpim') . '</span>';
} else if($ticket_status == 'resolved') {
	$status = '<span class="cqpim_button cqpim_small_button font-green border-green op nolink">' . __('Resolved', 'cqpim') . '</span>';
} else if($ticket_status == 'hold') {
	$status = '<span class="cqpim_button cqpim_small_button font-red border-red op nolink">' . __('On Hold', 'cqpim') . '</span>';
} else if($ticket_status == 'waiting') {
	$status = '<span class="cqpim_button cqpim_small_button font-purple border-purple op nolink">' . __('Awaiting Response', 'cqpim') . '</span>';
}
if($ticket_priority == 'low') {
	$priority = '<span class="cqpim_button cqpim_small_button font-blue-sharp border-blue-sharp op nolink">' . __('Low', 'cqpim') . '</span>';
} else if($ticket_priority == 'normal') {
	$priority = '<span class="cqpim_button cqpim_small_button font-green border-green op nolink">' . __('Normal', 'cqpim') . '</span>';
} else if($ticket_priority == 'high') {
	$priority = '<span class="cqpim_button cqpim_small_button font-red border-red op nolink">' . __('High', 'cqpim') . '</span>';
} else if($ticket_priority == 'immediate') {
	$priority = '<span class="cqpim_button cqpim_small_button font-red border-red op nolink sbold">' . __('Immediate', 'cqpim') . '</span>';
}
if($post->post_author == $user_id OR $assigned == $ticket_client) {
	if($post->post_author == $user_id OR in_array($user_id, $client_ids_untouched)) {
		update_post_meta($post->ID, 'unread', 0);
	} ?>
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
	<div class="masonry-grid">
		<div class="grid-sizer"></div>
		<div class="cqpim-dash-item-double grid-item">
			<div id="ticket_container" class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<span class="caption-subject font-green-sharp sbold"><?php _e('Ticket Details', 'cqpim'); ?></span>
					</div>
				</div>
				<table class="cqpim_table dash">
					<thead>
						<tr>
							<th><strong><?php _e('Ticket ID', 'cqpim'); ?></strong></th>
							<th><?php echo $post->ID; ?></th>
						</td>
					</thead>
					<tbody>
						<tr>
							<td><strong><?php _e('Ticket Title', 'cqpim'); ?></strong></td>
							<td><?php echo $post->post_title; ?></td>
						</td>
						<tr>
							<td><strong><?php _e('Ticket Created', 'cqpim'); ?></strong></td>
							<td><?php echo get_the_date('d/m/Y H:i'); ?></td>
						</td>
						<tr>
							<td><strong><?php _e('Last Updated', 'cqpim'); ?></strong></td>
							<td><?php echo $ticket_updated; ?></td>
						</td>
						<tr>
							<td><strong><?php _e('Ticket Priority', 'cqpim'); ?></strong></td>
							<td><?php echo $priority; ?></td>
						</td>
						<tr>
							<td><strong><?php _e('Ticket Status', 'cqpim'); ?></strong></td>
							<td><?php echo $status; ?></td>
						</td>
					</tbody>
				</table>
			</div>
		</div>
		<div class="cqpim-dash-item-triple grid-item">
			<div id="ticket_container" class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<span class="caption-subject font-green-sharp sbold"><?php _e('Ticket Updates', 'cqpim'); ?></span>
					</div>
				</div>
				<?php
				$ticket_updates = get_post_meta($post->ID, 'ticket_updates', true);
				$ticket_status = get_post_meta($post->ID, 'ticket_status', true);
				$ticket_priority = get_post_meta($post->ID, 'ticket_priority', true);
				if(empty($ticket_updates)) {
					$ticket_updates = array();
				} ?>
				<div style="max-height:900px" class="project_messages">
					<ul class="project_summary_progress" style="margin:0; overflow:auto; max-height:600px">
						<?php $ticket_updates = array_reverse($ticket_updates);
						foreach($ticket_updates as $update) {
							if($update['type'] == 'client') {
								$user = get_post_meta($update['user'], 'client_details', true);
								$email = isset($user['client_email']) ? $user['client_email'] : '';
								$name = isset($user['client_contact']) ? $user['client_contact'] : '';
							} else {
								$user_obj = wp_get_current_user();
								$user = get_post_meta($update['user'], 'team_details', true);
								$email = isset($user['team_email']) ? $user['team_email'] : '';
								$name = isset($user['team_name']) ? $user['team_name'] : '';
								if(empty($ticket_updates[$key]['seen'])) {
									$ticket_updates[$key]['seen'] = array(
										'time' => current_time('timestamp'),
										'user' => $user_obj->ID
									);
								}
								update_post_meta($post->ID, 'ticket_updates', array_reverse($ticket_updates));
							}
							$changes = isset($update['changes']) ? $update['changes'] : array();
							$size = 80;
							if(isset($update['email'])) {
								$email = $update['email'];
							}			
							?>
							<li style="margin-bottom:0">
								<div class="timeline-entry">
									<?php 
									$avatar = get_option('cqpim_disable_avatars');
									if(empty($avatar)) {
										$user = get_user_by('email', $email);
										echo '<div class="update-who">';
										echo get_avatar( $user->ID, 60, '', false, array('force_display' => true) );
										echo '</div>';
									} ?>
									<?php if(empty($avatar)) { ?>
										<div class="update-data">
									<?php } else { ?>
										<div style="width:100%; float:none" class="update-data">
									<?php } ?>
										<div class="timeline-body-arrow"> </div>
										<div class="timeline-by font-blue-madison sbold">
											<?php echo $update['name']; ?>
										</div>
										<div class="clear"></div>
										<div class="timeline-update font-grey-cascade"><?php echo wpautop($update['details']); ?></div>
										<div class="clear"></div>
										<div class="timeline-date font-grey-cascade">
											<?php echo date(get_option('cqpim_date_format') . ' H:i', $update['time']); ?>
											<span>&nbsp;</span>
											<span>
												<?php
												if($changes) {
													foreach($changes as $change) {
														echo ' | ' . $change;
													}
												}
												?>
											</span>
										</div>
									</div>
									<div class="clear"></div>
								</div>
							</li>
							<?php			
						} ?>
					</ul>
				</div>
				<?php $string = pto_random_string(10);
				$_SESSION['upload_ids'] = array();
				$_SESSION['ticket_changes'] = array();
				?>
				<div id="add_ticket_update">
					<div class="cqpim_block_title">
						<div class="caption">
							<span class="caption-subject font-green-sharp sbold"><?php _e('Update Ticket', 'cqpim'); ?></span>
						</div>
					</div>
					<input type="hidden" name="action" value="update_ticket" />
					<input type="hidden" id="post_id" name="post_id" value="<?php echo $post->ID; ?>" />
					<div class="cqpim-meta-left">
						<h4><?php _e('Ticket Status:', 'cqpim'); ?></h4>
						<select id="ticket_status_new" name="ticket_status_new">
							<option value="open" <?php if($ticket_status == 'open') { echo 'selected="selected"'; } ?>><?php _e('Open', 'cqpim'); ?></option>
							<option value="resolved" <?php if($ticket_status == 'resolved') { echo 'selected="selected"'; } ?>><?php _e('Resolved', 'cqpim'); ?></option>
							<option value="hold" <?php if($ticket_status == 'hold') { echo 'selected="selected"'; } ?>><?php _e('On Hold', 'cqpim'); ?></option>
							<option value="waiting" disabled <?php if($ticket_status == 'waiting') { echo 'selected="selected"'; } ?>><?php _e('Awaiting Response', 'cqpim'); ?></option>
						</select>
					</div>
					<div class="cqpim-meta-right">			
						<h4><?php _e('Ticket Priority:', 'cqpim'); ?></h4>
						<select id="ticket_priority_new" name="ticket_priority_new">
							<option value="low" <?php if($ticket_priority == 'low') { echo 'selected="selected"'; } ?>><?php _e('Low', 'cqpim'); ?></option>
							<option value="normal" <?php if($ticket_priority == 'normal') { echo 'selected="selected"'; } ?>><?php _e('Normal', 'cqpim'); ?></option>
							<option value="high" <?php if($ticket_priority == 'high') { echo 'selected="selected"'; } ?>><?php _e('High', 'cqpim'); ?></option>
							<option value="immediate" <?php if($ticket_priority == 'immediate') { echo 'selected="selected"'; } ?>><?php _e('Immediate', 'cqpim'); ?></option>
						</select>
					</div>
					<div class="clear"></div>
					<div>
						<h4><?php _e('Upload Files', 'cqpim'); ?></h4>
						<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
						<div id="upload_attachments"></div>
						<div class="clear"></div>
						<input type="hidden" name="image_id" id="upload_attachment_ids">
					</div>
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
							$classname = isset($field->className) ? $field->className : '';
							$value = isset($values[$field->name]) ? $values[$field->name] : '';
							$id = strtolower($field->label);
							$id = str_replace(' ', '_', $id);
							$id = str_replace('-', '_', $id);
							$id = preg_replace('/[^\w-]/', '', $id);
							if(!empty($field->required)) {
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
								echo '<' . $field->subtype . ' class="cqpim-custom ' . $classname . '">' . $field->label . '</' . $field->subtype . '>';
							} elseif($field->type == 'text') {			
								echo '<input type="text" class="cqpim-custom ' . $classname . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
							} elseif($field->type == 'website') {
								echo '<input type="url" class="cqpim-custom ' . $classname . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
							} elseif($field->type == 'number') {
								echo '<input type="number" class="cqpim-custom ' . $classname . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
							} elseif($field->type == 'textarea') {
								echo '<textarea class="cqpim-custom ' . $classname . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '">' . $value . '</textarea>';
							} elseif($field->type == 'date') {
								echo '<input class="cqpim-custom ' . $classname . ' datepicker" type="text" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
							} elseif($field->type == 'email') {
								echo '<input type="email" class="cqpim-custom ' . $classname . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '" value="' . $value . '" />';
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
									echo '<input type="checkbox" class="cqpim-custom ' . $classname . '" value="' . $option->value . '" name="' . $field->name . '" ' . $checked . ' /> ' . $option->label . '<br />';
								}
							} elseif($field->type == 'radio-group') {
								$options = $field->values;
								foreach($options as $option) {
									if($value == $option->value) {
										$checked = 'checked="checked"';
									} else {
										$checked = '';
									}
									echo '<input type="radio" class="cqpim-custom ' . $classname . '" value="' . $option->value . '" name="' . $field->name . '" ' . $required . ' ' . $checked . ' /> ' . $option->label . '<br />';
								}
							} elseif($field->type == 'select') {
								$options = $field->values;
								echo '<select class="cqpim-custom ' . $classname . '" id="' . $id . '" ' . $required . ' name="' . $field->name . '">';
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
					<h4><?php _e('Message', 'cqpim'); ?></h4>
					<textarea id="ticket_update_new" required ></textarea>
					<div class="clear"></div>
					<br />
					<a href="#" id="update_support" class="cqpim_button op right font-white bg-blue rounded_2 mt-20"><?php _e('Update Ticket', 'cqpim'); ?></a>
					<div class="clear"></div>
				</div>					
			</div>
		</div>
		<div class="cqpim-dash-item-double grid-item">
			<div id="ticket_container" class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<span class="caption-subject font-green-sharp sbold"><?php _e('Ticket Files', 'cqpim'); ?></span>
					</div>
				</div>
				<?php 
				$all_attached_files = get_attached_media( '', $post->ID );
				if(!$all_attached_files) {
					echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('There are no files uploaded to this ticket.', 'cqpim') . '</div>';
				} else {
					echo '<br /><table class="cqpim_table dash"><thead><tr>';
					echo '<th>' . __('File Name', 'cqpim') . '</th><th>' . __('Actions', 'cqpim') . '</th>';
					echo '</tr></thead><tbody>';
					foreach($all_attached_files as $file) {
						$file_object = get_post($file->ID);
						$link = get_the_permalink($file->ID);
						$user = get_user_by( 'id', $file->post_author );
						echo '<tr>';
						echo '<td style="text-align:left"><a class="cqpim-link" href="' . $file->guid . '" download="' . $file->post_title . '">' . $file->post_title . '</a><p style="margin:0; text-align:left; padding-left:0; padding-bottom:0">' . __('Uploaded on', 'cqpim') . ' ' . $file->post_date . ' ' . __('by', 'cqpim') . ' ' . $user->display_name . '</p></td>';
						echo '<td><a href="' . $file->guid . '" download="' . $file->post_title . '" class="cqpim_button cqpim_small_button border-green font-green op" value="' . $file->ID . '"><i class="fa fa-download" aria-hidden="true"></i></a></td>';
						echo '</tr>';
					}
					echo '</tbody></table>';
				}
				?>
			</div>
		</div>
	</div>
<?php } else { ?>
	<h1><?php _e('Access Denied', 'cqpim'); ?></h1>
<?php } ?>
<?php include('footer.php'); ?>