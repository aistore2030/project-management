<?php
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if(empty($client_logs)) {
	$client_logs = array();
}
$now = current_time('timestamp');
$client_logs[$now] = array(
	'user' => $user->ID,
	'page' => __('Client Dashboard Messages Page', 'cqpim')
);
update_post_meta($assigned, 'client_logs', $client_logs);
$users = pto_retrieve_messageble_users($user->ID);
$search = array();
if(!empty($users)) {
	foreach($users as $key => $suser) {
		$search[] = array(
			'id' => $key,
			'name' => $suser,
		);
	}
}	
$search = json_encode($search);
$conversations = pto_fetch_conversations($user->ID);
$text = __('Search for team member name...', 'cqpim');
$conversation = isset($_GET['conversation']) ? $_GET['conversation'] : '';
$args = array(
	'post_type' => 'cqpim_conversations',
	'posts_per_page' => 1,
	'post_status' => 'private',
	'meta_query'    => array(
		array(
			'key'       => 'conversation_id',
			'value'     => $conversation,
			'compare'   => '=',
		),
	),
);
$conversation = get_posts($args); 
$conversation = isset($conversation[0]) ? $conversation[0] : ''; 
?>
<br />
<div>
	<div id="cqpim-new-message" class="cqpim_block" style="display:none">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-envelope-open font-green-sharp" aria-hidden="true"></i>
				<span class="caption-subject font-green-sharp sbold"><?php _e('New Conversation', 'cqpim'); ?></span>
			</div>
			<div class="actions">
				<button id="send" style="margin-right:10px" class="cqpim_button cqpim_small_button border-green font-green rounded_2 op"><i class="fa fa-paper-plane" aria-hidden="true"></i> <?php _e('Send', 'cqpim'); ?></button>	
				<button id="cancel" class="cqpim_button cqpim_small_button border-red font-red rounded_2 op"><i class="fa fa-times" aria-hidden="true"></i> <?php _e('Cancel', 'cqpim'); ?></button>					
			</div>
		</div>
		<form id="cqpim-create-new-message">
			<p><span class="cqpim-heading"><?php _e('Recipients:', 'cqpim'); ?></span><input type="text" id="to" name="to" placeholder="<?php echo $text; ?>" /></p>
			<div class="clear"></div>
			<p><span class="cqpim-heading"><?php _e('Subject:', 'cqpim'); ?></span><input type="text" id="subject" name="subject" /></p>
			<p><span class="cqpim-heading"><?php _e('Message:', 'cqpim'); ?></span><textarea id="message" name="message" /></textarea></p>
			<div class="clear"></div>
			<p><span class="cqpim-heading"><?php _e('Attachments:', 'cqpim'); ?></span>
				<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" multiple />
				<div id="upload_attachments"></div>
				<div class="clear"></div>
				<input type="hidden" name="image_id" id="upload_attachment_ids">
				<input type="hidden" name="action" value="image_submission">						
			</p>
			<div class="clear"></div>
			<div id="message-ajax-response"></div>
		</form>			
	</div>
	<div id="cqpim-reply-message" class="cqpim_block" style="display:none">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-envelope-open font-green-sharp" aria-hidden="true"></i>
				<span class="caption-subject font-green-sharp sbold"><?php _e('Reply to Conversation', 'cqpim'); ?></span>
			</div>
			<div class="actions">
				<button id="send-reply" style="margin-right:10px" class="cqpim_button cqpim_small_button border-green font-green rounded_2 op" data-conversation="<?php echo isset($conversation->ID) ? $conversation->ID : ''; ?>"><i class="fa fa-paper-plane" aria-hidden="true"></i> <?php _e('Send', 'cqpim'); ?></button>	
				<button id="cancel-reply" class="cqpim_button cqpim_small_button border-red font-red rounded_2 op"><i class="fa fa-times" aria-hidden="true"></i> <?php _e('Cancel', 'cqpim'); ?></button>					
			</div>
		</div>
		<form id="rcqpim-create-new-message">
			<p><span class="cqpim-heading"><?php _e('Message:', 'cqpim'); ?></span><textarea id="rmessage" name="message" /></textarea></p>
			<div class="clear"></div>
			<p><span class="cqpim-heading"><?php _e('Attachments:', 'cqpim'); ?></span>
				<input type="file" class="rcqpim-file-upload" name="async-upload" id="attachments" multiple />
				<div id="rupload_attachments"></div>
				<div class="clear"></div>
				<input type="hidden" name="rimage_id" id="rupload_attachment_ids">
				<input type="hidden" name="action" value="image_submission">						
			</p>
			<div class="clear"></div>
		</form>
	</div>
	<div class="cqpim_block">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-envelope-open font-green-sharp" aria-hidden="true"></i>
				<?php if(!empty($conversation)) { ?>
					<span class="caption-subject font-green-sharp sbold"><?php echo str_replace('Private: ', '', get_the_title($conversation->ID)); ?></span>
				<?php } else { ?>
					<span class="caption-subject font-green-sharp sbold"><?php _e('My Messages', 'cqpim'); ?></span>
				<?php } ?>
			</div>
			<div class="actions">
				<button id="send-message" class="cqpim_button cqpim_small_button border-green font-green rounded_2 sbold op"><i class="fa fa-paper-plane" aria-hidden="true"></i> <?php _e('New Conversation', 'cqpim'); ?></button>
			</div>
		</div>
		<?php if(!empty($_GET['convdeleted'])) { ?>
			<br />
			<div class="malert cqpim-alert cqpim-alert-success">
				<i class="fa fa-check" aria-hidden="true"></i> <?php _e('The conversation was successfully deleted.', 'cqpim'); ?>
			</div>
		<?php } ?>
		<?php if(!empty($_GET['convcreated'])) { ?>
			<br />
			<div class="malert cqpim-alert cqpim-alert-success">
				<i class="fa fa-check" aria-hidden="true"></i> <?php _e('The conversation was successfully created.', 'cqpim'); ?>
			</div>
		<?php } ?>
		<?php if(!empty($_GET['convleft'])) { ?>
			<br />
			<div class="malert cqpim-alert cqpim-alert-success">
				<i class="fa fa-check" aria-hidden="true"></i> <?php _e('You have been removed from the conversation.', 'cqpim'); ?>
			</div>
		<?php } ?>
		<?php if(!empty($_GET['convremoved'])) { ?>
			<br />
			<div class="malert cqpim-alert cqpim-alert-success">
				<i class="fa fa-check" aria-hidden="true"></i> <?php _e('The user has been removed.', 'cqpim'); ?>
			</div>
		<?php } ?>
		<?php if(!empty($_GET['convadded'])) { ?>
			<br />
			<div class="malert cqpim-alert cqpim-alert-success">
				<i class="fa fa-check" aria-hidden="true"></i> <?php _e('The user has been added.', 'cqpim'); ?>
			</div>
		<?php } ?>
		<?php if(empty($conversations)) { ?>
			<div id="cqpim-no-messages">
				<p><?php _e('You do not have any messages.', 'cqpim'); ?></p>				
			</div>
		<?php } else { ?>
			<?php $conversation = isset($_GET['conversation']) ? $_GET['conversation'] : '';
			if(!empty($conversation)) {
				$args = array(
					'post_type' => 'cqpim_conversations',
					'posts_per_page' => 1,
					'post_status' => 'private',
					'meta_query'    => array(
						array(
							'key'       => 'conversation_id',
							'value'     => $conversation,
							'compare'   => '=',
						),
					),
				);
				$conversation = get_posts($args); 
				$conversation = $conversation[0]; 
				$conversation_id = get_post_meta($conversation->ID, 'conversation_id', true);
				$recipients = get_post_meta($conversation->ID, 'recipients', true);
				if(!in_array($user->ID, $recipients)) {
					echo '<h1>' . __('ACCESS DENIED', 'cqpim') . '</h1>';
					return;
				}
				$args = array(
					'post_type' => 'cqpim_messages',
					'posts_per_page' => -1,
					'post_status' => 'private',
					'meta_query' => array(
						array(
							'key'       => 'conversation_id',
							'value'     => $conversation_id,
							'compare'   => '=',
						),
					),
					'order' => 'DESC',
					'orderby' => 'meta_value',
					'meta_key' => 'stamp'
				);
				$messages = get_posts($args); ?>
				<input type="text" id="cqpim-title-editable-field" value="<?php echo get_the_title($conversation->ID); ?>" />
				<input type="hidden" id="jq-user-id" value="<?php echo $user->ID; ?>" />
				<input type="hidden" id="jq-conv-id" value="<?php echo $conversation->ID; ?>" />
				<div id="cqpim-messaging-buttons">
					<button id="cqpim-convo-reply" class="cqpim_button cqpim_small_button font-white bg-green rounded_2 op right"><i class="fa fa-reply" aria-hidden="true"></i><span class="desktop_only"> <?php _e('Reply', 'cqpim'); ?></span></button>
					<button id="cqpim-convo-leave" class="cqpim_button cqpim_small_button font-white bg-amber rounded_2 op right"><i class="fa fa-sign-out" aria-hidden="true"></i><span class="desktop_only"> <?php _e('Leave', 'cqpim'); ?></span></button>
					<?php if($user->ID == $conversation->post_author || current_user_can('cqpim_do_all')) { ?>
						<button id="cqpim-convo-delete" class="cqpim_button cqpim_small_button font-white bg-red rounded_2 op right"><i class="fa fa-trash" aria-hidden="true"></i><span class="desktop_only"> <?php _e('Delete', 'cqpim'); ?></span></button>
					<?php } ?>
					<div class="clear"></div>
				</div>
				<div id="delete-confirm" style="display:none" title="<?php _e('Delete Conversation', 'cqpim'); ?>">
					<p><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php _e('This conversation and all messages will be permanently deleted. Are you sure?', 'cqpim'); ?></p>
				</div>
				<div id="leave-confirm" style="display:none" title="<?php _e('Leave Conversation', 'cqpim'); ?>">
					<p><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php _e('Are you sure you want to leave the conversation?', 'cqpim'); ?></p>
				</div>
				<div id="remove-confirm" style="display:none" title="<?php _e('Remove User', 'cqpim'); ?>">
					<p><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php _e('Choose which user you would like to remove and click Remove User.', 'cqpim'); ?></p>
					<select id="cqpim-remove-user">
						<?php foreach($recipients as $recipient) { 
							$recip = get_user_by('id', $recipient); ?>
							<option value="<?php echo $recip->ID; ?>"><?php echo $recip->display_name; ?></option>
						<?php } ?>
					</select>
				</div>
				<div id="add-confirm" style="display:none" title="<?php _e('Add User', 'cqpim'); ?>">
					<p><i class="fa fa-check" aria-hidden="true"></i> <?php _e('Search for a user to add to the conversation.', 'cqpim'); ?></p>
					<input type="text" id="ato" />
				</div>
				<div id="cqpim-dmessage-container">
					<?php foreach($messages as $message) { 
						$sender = get_post_meta($message->ID, 'sender', true);
						$system = get_post_meta($message->ID, 'system', true);
						$sender_obj = get_user_by('id', $sender);
						if($user->ID != $sender) {
							$read = get_post_meta($message->ID, 'read', true);
							if(!empty($read) && is_array($read) && !in_array($user->ID, $read)) {
								$read[] = $user->ID;
							}
							update_post_meta($message->ID, 'read', $read);
						}
						$update = get_post_meta($message->ID, 'message', true);
						$stamp = get_post_meta($message->ID, 'stamp', true);
						if($sender == $user->ID) {
							$class = ' own';
						} else {
							$class = '';
						}
						if(!empty($system)) {
							$class = ' system';
						}
						if(!empty($system)) {
							echo '<div style="text-align:center; clear:both">';
						} ?>
						<div class="cqpim-dmessage-bubble<?php echo $class; ?>">
							<div class="cqpim-messagelist-avatar" style="float:right; margin-left:20px;">
								<?php echo get_avatar( $sender_obj->ID, 40, '', $sender_obj->display_name, array('force_display' => true)); ?>
							</div>
							<?php echo $update; ?>
							<div class="clear"></div>
							<?php $all_attached_files = get_attached_media( '', $message->ID ); 
							if(!empty($all_attached_files)) { ?>
								<div class="cqpim-dmessage-attachments<?php echo $class; ?>">
									<div><strong><i class="fa fa-paperclip" aria-hidden="true"></i> <?php _e('Attachments', 'cqpim'); ?></strong></div>
									<ul>
										<?php foreach($all_attached_files as $file) { ?>
											<li><a href="<?php echo $file->guid; ?>" target="_blank"><?php echo $file->post_title; ?></a> | <i class="fa fa-download" aria-hidden="true"></i>  <a href="<?php echo $file->guid; ?>" download ><?php _e('Download', 'cqpim'); ?></a></li>
										<?php } ?>
									</ul>
								</div>
							<?php } ?>
							<div class="cqpim-dmessage-date<?php echo $class; ?>">
								<i class="fa fa-paper-plane" aria-hidden="true"></i>
								<?php 
								$date = date(get_option('cqpim_date_format') . ' H:i', $stamp);
								printf(__('Posted by %1$s on %2$s', 'cqpim'), $sender_obj->display_name, $date);
								$read = get_post_meta($message->ID, 'read', true);
								if(!empty($read)) {
									echo '&nbsp;&nbsp;';
									echo '<i class="fa fa-envelope-open" aria-hidden="true"></i> ';
									_e('Seen by:', 'cqpim') . ' ';
									$count = count($read);
									$i = 0;
									foreach($read as $p) {
										$i++;
										$po = get_user_by('id', $p);
										echo ' ' . $po->display_name;
										if($i != $count) { echo ','; }
									}
								}
								$piping = get_post_meta($message->ID, 'piping', true);
								if(!empty($piping)) {
									echo ' - ' . __('Sent via email', 'cqpim');
								}
								?>
							</div>
						</div>	
						<?php if(!empty($system)) {
							echo '</div>';
						} ?>							
					<?php } ?>	
				</div>
			<?php } else { ?>
				<table class="dataTable datatable_style milestones">
					<thead>
						<tr>
							<th><?php _e('Subject', 'cqpim'); ?></th>
							<th><?php _e('Created', 'cqpim'); ?></th>
							<th><?php _e('Updated', 'cqpim'); ?></th>
							<th><?php _e('Members', 'cqpim'); ?></th>
							<th style="display:none"><?php _e('Stamp', 'cqpim'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($conversations as $conversation) { 
							$id = get_post_meta($conversation->ID, 'conversation_id', true);
							$created = get_post_meta($conversation->ID, 'created', true);
							$updated = get_post_meta($conversation->ID, 'updated', true);
							$update_user = get_user_by('id', $updated['by']);
							$timestamp = strtotime(date('Y-m-d H:i', $updated['at']));
							$update_user = $update_user->display_name;
							$members = get_post_meta($conversation->ID, 'recipients', true);
							$args = array(
								'post_type' => 'cqpim_messages',
								'posts_per_page' => -1,
								'post_status' => 'private',
								'meta_query' => array(
									array(
										'key'       => 'conversation_id',
										'value'     => $id,
										'compare'   => '=',
									),
								),
							);
							$messages = get_posts($args);
							$read_val = true;
							foreach($messages as $message) {
								$read = get_post_meta($message->ID, 'read', true);
								if(!in_array($user->ID, $read)) {
									$read_val = false;
								}
							}
							?>
							<tr<?php if(empty($read_val)) { echo ' class="cqpim-unread"'; } ?>>
								<td><?php if(empty($read_val)) { echo '<i class="fa fa-envelope" aria-hidden="true"></i> '; } else { echo '<i class="fa fa-envelope-open" aria-hidden="true"></i> '; } ?>&nbsp;&nbsp;<a href="<?php echo get_the_permalink($client_dash) . '?page=messages&conversation=' . $id; ?>"><?php echo str_replace('Private: ','', get_the_title($conversation->ID)); ?></a></td>
								<td><?php echo date(get_option('cqpim_date_format') . ' H:i', $created); ?></td>
								<td><?php echo date(get_option('cqpim_date_format') . ' H:i', $updated['at']); ?></td>
								<td>
									<?php foreach($members as $member) {
										$recip = get_user_by('id', $member);
										echo '<div class="cqpim-messagelist-avatar">';
										echo get_avatar( $recip->ID, 80, '', $recip->display_name, array('force_display' => true));
										echo '</div>';
									} ?>
								</td>
								<td style="display:none"><?php echo $timestamp; ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		<?php } ?>
	</div>
</div>
<?php 
add_action( 'wp_footer', 'pto_print_cd_token_scripts', 51 );
function pto_print_cd_token_scripts() {
$dash_page = get_option('cqpim_client_page');
if(is_page($dash_page)) {
	$user = wp_get_current_user();
	$users = pto_retrieve_messageble_users($user->ID);
	$search = array();
	if(!empty($users)) {
		foreach($users as $key => $suser) {
			$search[] = array(
				'id' => $key,
				'name' => $suser,
			);
		}
	}	
	$search = json_encode($search);
	$conversations = pto_fetch_conversations($user->ID);
	$text = __('Search for team member name...', 'cqpim'); ?>
		<script>
			jQuery(document).ready(function() {
				jQuery('#to').tokenInput(<?php echo $search; ?>, {
					hintText: "<?php echo $text; ?>",
					noResultsText: '<?php _e('No Results', 'cqpim'); ?>',
					searchingText: '<?php _e('Searching', 'cqpim'); ?>'
				});						
			});
		</script>
		<script>
			jQuery(document).ready(function() {
				jQuery('#ato').tokenInput(<?php echo $search; ?>, {
					hintText: "<?php echo $text; ?>",
					noResultsText: '<?php _e('No Results', 'cqpim'); ?>',
					searchingText: '<?php _e('Searching', 'cqpim'); ?>'
				});						
			});
		</script>
<?php }
} ?>