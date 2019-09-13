<?php
add_action( 'admin_menu' , 'register_pto_profile_page', 9 ); 
function register_pto_profile_page() {
	$mypage = add_submenu_page(	
				'pto-dashboard',
				__('My Profile', 'cqpim'),		
				__('My Profile', 'cqpim'), 			
				'cqpim_team_edit_profile', 			
				'pto-manage-profile', 		
				'pto_team_profile'
	);
	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
}
function pto_team_profile() { 
	$user = wp_get_current_user(); 
	$assigned = pto_get_team_from_userid($user);
	$team_details = get_post_meta($assigned, 'team_details', true);
	?>
	<br />
	<div class="cqpim-dash-item-full">
		<input type="hidden" id="team_id" value="<?php echo $assigned; ?>" />
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<i class="fa fa-pencil-square-o font-green-sharp" aria-hidden="true"></i>
					<span class="caption-subject font-green-sharp sbold"><?php _e('My Profile', 'cqpim'); ?> </span>
				</div>
			</div>
			<p><?php _e('Name', 'cqpim'); ?></p>
			<input type="text" id="team_name" name="team_name" value="<?php echo isset($team_details['team_name']) ? $team_details['team_name'] : ''; ?>" required />
			<p><?php _e('Email', 'cqpim'); ?></p>
			<input type="text"  id="team_email" name="team_email" value="<?php echo isset($team_details['team_email']) ? $team_details['team_email'] : ''; ?>" required />
			<p><?php _e('Telephone', 'cqpim'); ?></p>
			<input type="text"  id="team_telephone" name="team_telephone" value="<?php echo isset($team_details['team_telephone']) ? $team_details['team_telephone'] : ''; ?>" />
			<p><?php _e('Job Title', 'cqpim'); ?></p>
			<input type="text"  id="team_job" name="team_job" value="<?php echo isset($team_details['team_job']) ? $team_details['team_job'] : ''; ?>" />
			<div class="clear"></div>
			<br />
			<button class="pto_update_details cqpim_button bg-blue font-white rounded_4" data-type="personal"><?php _e('Update Contact Details', 'cqpim'); ?></button>
			<div class="clear"></div>
			<br />
		</div>
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<i class="fa fa-camera font-green-sharp" aria-hidden="true"></i>
					<span class="caption-subject font-green-sharp sbold"><?php _e('My Photo', 'cqpim'); ?> </span>
				</div>
			</div>
			<p><?php _e('Upload new Photo', 'cqpim'); ?></p>
			<div class="cqpim_upload_wrapper">
				<input type="file" class="cqpim-file-upload-avatar" name="async-upload" id="attachments" />
				<div id="upload_attachments"></div>
				<div class="clear"></div>
				<input type="hidden" name="image_id" id="upload_attachment_ids">
			</div>
			<div id="pto_avatar_preview_cont" style="display:none; float:left; margin-right:30px">
				<p><?php _e('New Photo Preview', 'cqpim'); ?></p>
				<div id="pto_avatar_preview"></div>
			</div>
			<?php 
			$team_avatar = get_post_meta($assigned, 'team_avatar', true);
			if(!empty($team_avatar)) { ?>
				<div id="pto_avatar_current_cont" style="float:left">
					<p><?php _e('Current Photo', 'cqpim'); ?></p>
					<div id="pto_avatar_current"><?php echo wp_get_attachment_image($team_avatar, 'thumbnail', false, '' ); ?></div>
				</div>
			<?php }	?>
			<div class="clear"></div>
			<br />
			<button class="pto_update_details cqpim_button bg-blue font-white rounded_4" data-type="photo"><?php _e('Update Photo', 'cqpim'); ?></button>
			<?php if(!empty($team_avatar)) { ?>
				<button class="pto_remove_current_photo cqpim_button bg-red font-white rounded_4" data-type="photo"><?php _e('Remove Photo', 'cqpim'); ?></button>
			<?php } ?>
			<div class="clear"></div>
			<br />
		</div>		
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<i class="fa fa-lock font-green-sharp" aria-hidden="true"></i>
					<span class="caption-subject font-green-sharp sbold"><?php _e('Change Password', 'cqpim'); ?> </span>
				</div>
			</div>
			<p><?php _e('New Password', 'cqpim'); ?></p>
			<input type="password" id="password" name="password" value="" required />
			<p><?php _e('Repeat Password', 'cqpim'); ?></p>
			<input type="password"  id="password2" name="password2" value="" required />
			<div class="clear"></div>
			<br />
			<button class="pto_update_details cqpim_button bg-blue font-white rounded_4" data-type="password"><?php _e('Update Password', 'cqpim'); ?></button>
			<div class="clear"></div>
			<br />
		</div>	
		
		
	</div>
<?php }