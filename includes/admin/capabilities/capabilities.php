<?php
add_action( 'admin_menu' , 'register_pto_caps_page', 27 ); 
function register_pto_caps_page() {
	$mypage = add_submenu_page(	
				'pto-dashboard',
				__('Roles & Permissions', 'cqpim'), 			
				__('Roles & Permissions', 'cqpim'),			
				'edit_cqpim_permissions', 				
				'pto-permissions', 		
				'pto_permissions'
	);
	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
	add_action( 'admin_init', 'register_pto_permissions' );
}
function pto_validate_roles($plugin_options) {	
	$roles_sorted = array('cqpim_admin');	
	foreach($plugin_options as $role) {		
		if(!empty($role['cqpim_roles']) && $role['cqpim_roles'] != 'client' && $role['cqpim_roles'] != 'admin') {		
			$roles_sorted[] = $role['cqpim_roles'];			
		}		
	}	
	$plugin_options = $roles_sorted;
	return $plugin_options;
}
function register_pto_permissions() {
	register_setting( 'cqpim_permissions', 'cqpim_roles', 'pto_validate_roles' );
	register_setting( 'cqpim_permissions', 'cqpim_permissions' );
}
function pto_permissions_page_capability( $capability ) {
	return 'edit_cqpim_permissions';
}
add_filter( 'option_page_capability_cqpim_permissions', 'pto_permissions_page_capability' );
function pto_permissions() {
	$user = wp_get_current_user();
	?>
	<form method="post" action="options.php" enctype="multipart/form-data">
		<div id="main-container">
			<?php 
			$option_group = 'cqpim_permissions';
			settings_fields( $option_group ); ?>
			<div class="cqpim-dash-item-full tasks-box" style="padding-right:10px">
				<br />
				<div class="cqpim_block">
					<div class="cqpim_block_title">
						<div class="caption">
							<span class="caption-subject font-green-sharp sbold"><?php _e('Roles', 'cqpim'); ?> </span>
						</div>
					</div>
					<p><?php _e('Roles can be assigned to Team Members and control what they have permission to do within the plugin. A role is effectively a group of permissions that makes it easier to control who can do what. Once a role has been created, you will be able to assign permissions to the role in the permissions box below.', 'cqpim'); ?></p>
					<p><strong><?php _e('When adding a role, please use only lower case letters, and use underscores instead of spaces. Eg. support_role. You cannot add "admin" or "client" as these roles are built in and cannot be changed.', 'cqpim'); ?></strong></p>
					<?php 
					$roles = get_option('cqpim_roles'); 
					if(!is_array($roles)) {
						$roles = array(get_option('cqpim_roles'));
					}						
					?>
					<input type="hidden" name="cqpim_roles" value="cqpim_admin" />
					<?php 
					if($roles) { 
						$i = 0; 
						echo '<div class="repeater" style="text-align:left">';
						echo '<div data-repeater-list="cqpim_roles">';
						foreach($roles as $role) { 
							if($role != 'cqpim_admin') { ?>
							<div class="line_item" data-repeater-item>
								<table class="">
									<tbody>
										<tr>
											<td><input data-row="<?php echo $i; ?>" data-name="" id="role_name_<?php echo $i; ?>" name="cqpim_roles" class="invoice_qty" type="text" name="qty" value="<?php echo $role; ?>" placeholder="role_name" <?php if($role == 'cqpim_admin') { echo 'readonly'; } ?> /></td>
											<?php if($role != 'cqpim_admin') { ?>
											<td><input data-row="<?php echo $i; ?>" class="line_delete cqpim_button cqpim_small_button bg-red border-red op rounded_2" data-repeater-delete type="button" value=""/></td>
											<?php } ?>
										</tr>
									</tbody>
								</table>
							</div>							
						<?php $i++; } 
						}
						if($i == 0) { ?>
							<div class="line_item" data-repeater-item>
								<table class="cqpim_table">
									<tbody>
										<tr>
											<td><input data-row="0" data-name="" id="role_name_0" name="cqpim_roles" class="invoice_qty" type="text" name="qty" value="" placeholder="role_name" /></td>
											<td><input data-row="0" class="line_delete cqpim_button cqpim_small_button bg-red border-red op rounded_2" data-repeater-delete type="button" value=""/></td>
										</tr>
									</tbody>
								</table>
							</div>	
						<?php } 
						echo '</div>';
						echo '<input class="add_line_item_row cqpim_button cqpim_small_button bg-green border-green op rounded_2" data-repeater-create type="button" value=""/>';
						echo '</div>';
					} ?>
					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
					</p>
				</div>
			</div>
			<div class="cqpim-dash-item-full tasks-box" style="padding-right:10px">
				<br />
				<div class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<span class="caption-subject font-green-sharp sbold"><?php _e('Permissions', 'cqpim'); ?> </span>
					</div>
				</div>
				<?php
				$roles = get_option('cqpim_roles');
				$value = get_option('cqpim_permissions');
				if(count($roles) > 1) { ?>
					<table class="cqpim_table permissions">
						<thead>
							<th><?php _e('Permission', 'cqpim'); ?></th>
							<?php 
							$columns = 1;
							foreach($roles as $role) {
								if($role != 'cqpim_admin') {
									echo '<th>' . $role . '</th>';
									$columns++;
								}
							}
							?>
						</thead>
						<tbody>
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('General Plugin Permissions', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit Permissions', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[edit_cqpim_permissions][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['edit_cqpim_permissions']) ? $value['edit_cqpim_permissions'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[edit_cqpim_permissions][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit Settings', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[edit_cqpim_settings][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['edit_cqpim_settings']) ? $value['edit_cqpim_settings'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[edit_cqpim_settings][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Show Help Link', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[edit_cqpim_help][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['edit_cqpim_help']) ? $value['edit_cqpim_help'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[edit_cqpim_help][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View All Files Page', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_view_all_files][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_view_all_files']) ? $value['cqpim_view_all_files'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_view_all_files][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Dashboard', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View All Tasks in All Projects, from all Team Members (Useful for keeping track of all Team Members at once)', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_dash_view_all_tasks][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_dash_view_all_tasks']) ? $value['cqpim_dash_view_all_tasks'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_dash_view_all_tasks][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View the "Who\'s Online" widget in the admin dashboard', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_dash_view_whos_online][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_dash_view_all_tasks']) ? $value['cqpim_dash_view_whos_online'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_dash_view_whos_online][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Clients', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View Clients', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[read_cqpim_client][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['read_cqpim_client']) ? $value['read_cqpim_client'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_client][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit / Update Clients', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[publish_cqpim_clients][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['publish_cqpim_clients']) ? $value['publish_cqpim_clients'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_clients][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Create New Client (Requires \'Edit / Update Clients\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_client][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_create_new_client']) ? $value['cqpim_create_new_client'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_client][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Delete Clients', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_clients][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['delete_cqpim_clients']) ? $value['delete_cqpim_clients'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_clients][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Reset Client Passwords', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_reset_client_passwords][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_reset_client_passwords']) ? $value['cqpim_reset_client_passwords'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_reset_client_passwords][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Leads', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View Leads', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[read_cqpim_lead][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['read_cqpim_lead']) ? $value['read_cqpim_lead'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_lead][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit / Update Leads', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[publish_cqpim_leads][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['publish_cqpim_leads']) ? $value['publish_cqpim_leads'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_leads][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Create New Lead (Requires \'Edit / Update Leads\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_lead][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_create_new_lead']) ? $value['cqpim_create_new_lead'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_lead][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Delete Leads', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_leads][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['delete_cqpim_leads']) ? $value['delete_cqpim_leads'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_leads][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Lead Forms', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View Lead Forms', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[read_cqpim_leadform][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['read_cqpim_leadform']) ? $value['read_cqpim_leadform'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_leadform][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit / Update Lead Forms', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[publish_cqpim_leadforms][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['publish_cqpim_leadforms']) ? $value['publish_cqpim_leadforms'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_leadforms][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Create New Lead Form (Requires \'Edit / Update Lead Forms\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_leadform][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_create_new_leadform']) ? $value['cqpim_create_new_leadform'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_leadform][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Delete Lead Forms', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_leadforms][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['delete_cqpim_leadforms']) ? $value['delete_cqpim_leadforms'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_leadforms][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>	
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Team Members', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View Team Members', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[read_cqpim_team][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['read_cqpim_team']) ? $value['read_cqpim_team'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_team][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit / Update Team Members', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[publish_cqpim_teams][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['publish_cqpim_teams']) ? $value['publish_cqpim_teams'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_teams][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Create New Team Member (Requires \'Edit / Update Team Members\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_team][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_create_new_team']) ? $value['cqpim_create_new_team'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_team][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Delete Team Members', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_teams][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['delete_cqpim_teams']) ? $value['delete_cqpim_teams'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_teams][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Reset Team Member Passwords', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_reset_team_passwords][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_reset_team_passwords']) ? $value['cqpim_reset_team_passwords'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_reset_team_passwords][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Change Team Member\'s Roles (Be careful, this has security implications)', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_grant_admin_role][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_grant_admin_role']) ? $value['cqpim_grant_admin_role'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_grant_admin_role][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Allow Team Members to edit their own Profiles', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_team_edit_profile][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_team_edit_profile']) ? $value['cqpim_team_edit_profile'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_team_edit_profile][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Quotes & Estimates', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View Quotes & Estimates', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[read_cqpim_quote][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['read_cqpim_quote']) ? $value['read_cqpim_quote'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_quote][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit / Update Quotes & Estimates', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[publish_cqpim_quotes][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['publish_cqpim_quotes']) ? $value['publish_cqpim_quotes'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_quotes][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Create New Quotes & Estimates (Requires \'Edit / Update Quotes & Estimates\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_quote][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_create_new_quote']) ? $value['cqpim_create_new_quote'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_quote][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Delete Quotes & Estimates', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_quotes][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['delete_cqpim_quotes']) ? $value['delete_cqpim_quotes'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_quotes][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Send Quote / Estimate to Clients', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_send_quotes][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_send_quotes']) ? $value['cqpim_send_quotes'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_send_quotes][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Quote Forms', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View Quote Forms', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[read_cqpim_form][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['read_cqpim_form']) ? $value['read_cqpim_form'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_form][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit / Update Quote Forms', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[publish_cqpim_forms][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['publish_cqpim_forms']) ? $value['publish_cqpim_forms'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_forms][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Create New Quote Forms (Requires \'Edit / Update Quote Forms\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_form][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_create_new_form']) ? $value['cqpim_create_new_form'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_form][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Delete Quote Forms', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_forms][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['delete_cqpim_forms']) ? $value['delete_cqpim_forms'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_forms][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Project Templates', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View Project Templates', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[read_cqpim_templates][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['read_cqpim_templates']) ? $value['read_cqpim_templates'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_templates][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit / Update Project Templates', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[publish_cqpim_templates][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['publish_cqpim_templates']) ? $value['publish_cqpim_templates'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_templates][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Create New Project Templates (Requires \'Edit / Update Project Templates\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_templates][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_create_new_templates']) ? $value['cqpim_create_new_templates'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_templates][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Delete Project Templates', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_templates][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['delete_cqpim_templates']) ? $value['delete_cqpim_templates'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_templates][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Apply Project Templates to Quotes/Projects', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_apply_project_templates][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_apply_project_templates']) ? $value['cqpim_apply_project_templates'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_apply_project_templates][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>									
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Terms Templates', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View Terms Templates', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[read_cqpim_terms][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['read_cqpim_terms']) ? $value['read_cqpim_terms'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_terms][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit / Update Terms Templates', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[publish_cqpim_terms][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['publish_cqpim_terms']) ? $value['publish_cqpim_terms'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_terms][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Create New Terms Templates (Requires \'Edit / Update Terms Templates\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_terms][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_create_new_terms']) ? $value['cqpim_create_new_terms'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_terms][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Delete Terms Templates', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_terms][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['delete_cqpim_terms']) ? $value['delete_cqpim_terms'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_terms][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Projects', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left" colspan="<?php echo $columns; ?>"><?php _e('By default, all Team Members can view and update any Project that they are assigned to, this includes adding, editing and assigning tasks. You can however grant them extra permissions, such as the ability to view all projects regardless of assignment, assign team members, edit milestones, view financials, view and send contracts, sign-off projects and mark projects as closed.', 'cqpim'); ?></td>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View / Edit / Update ALL Projects (Even if not Assigned)', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_view_all_projects][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_view_all_projects']) ? $value['cqpim_view_all_projects'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_view_all_projects][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Create new Projects (Requires View / Edit / Update ALL Projects)', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_create_new_project][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_create_new_project']) ? $value['cqpim_create_new_project'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_project][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Delete Projects', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_projects][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['delete_cqpim_projects']) ? $value['delete_cqpim_projects'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_projects][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View and Edit General Project Info Page', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_view_project_client_page][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_view_project_client_page']) ? $value['cqpim_view_project_client_page'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_view_project_client_page][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View Client Information in Assigned Projects', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_view_project_client_info][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_view_project_client_info']) ? $value['cqpim_view_project_client_info'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_view_project_client_info][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View Project Financials Table', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_view_project_financials][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_view_project_financials']) ? $value['cqpim_view_project_financials'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_view_project_financials][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Add / Edit Milestones', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_edit_project_milestones][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_edit_project_milestones']) ? $value['cqpim_edit_project_milestones'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_edit_project_milestones][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Assign Team Members to Projects', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_edit_project_members][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_edit_project_members']) ? $value['cqpim_edit_project_members'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_edit_project_members][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit Project Brief', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_edit_project_brief][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_edit_project_brief']) ? $value['cqpim_edit_project_brief'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_edit_project_brief][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit Project Details (Dates, Ref, Client)', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_edit_project_dates][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_edit_project_dates']) ? $value['cqpim_edit_project_dates'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_edit_project_dates][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View / Send Contracts', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_view_project_contract][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_view_project_contract']) ? $value['cqpim_view_project_contract'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_view_project_contract][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Sign-Off Projects', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_mark_project_signedoff][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_mark_project_signedoff']) ? $value['cqpim_mark_project_signedoff'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_mark_project_signedoff][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Close Projects', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_mark_project_closed][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_mark_project_closed']) ? $value['cqpim_mark_project_closed'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_mark_project_closed][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit Project Colours', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_edit_project_colours][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_edit_project_colours']) ? $value['cqpim_edit_project_colours'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_edit_project_colours][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<?php if(pto_check_addon_status('bugs')) { ?>
								<tr>
									<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Bug Tracker', 'cqpim'); ?></th>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('View Assigned Bugs in Projects', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_view_bugs][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['cqpim_view_bugs']) ? $value['cqpim_view_bugs'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_view_bugs][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('View All Bugs in Projects', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_view_all_bugs][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['cqpim_view_all_bugs']) ? $value['cqpim_view_all_bugs'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_view_all_bugs][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Update Bugs', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[publish_cqpim_bugs][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['publish_cqpim_bugs']) ? $value['publish_cqpim_bugs'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_bugs][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Delete Bugs (Requires \'Update Bugs\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_bugs][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['delete_cqpim_bugs']) ? $value['delete_cqpim_bugs'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_bugs][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Create New Bug in Project (Requires \'View Assigned Bugs in Projects\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_bug][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['cqpim_create_new_bug']) ? $value['cqpim_create_new_bug'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_bug][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Activate / Deactivate Bug Tracker per Project', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_activate_bugs][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['cqpim_activate_bugs']) ? $value['cqpim_activate_bugs'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_activate_bugs][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
							<?php } ?>
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Invoices', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View Invoices', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[read_cqpim_invoice][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['read_cqpim_invoice']) ? $value['read_cqpim_invoice'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_invoice][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Edit / Update Invoices', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[publish_cqpim_invoices][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['publish_cqpim_invoices']) ? $value['publish_cqpim_invoices'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_invoices][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Create New Invoices (Requires \'Edit / Update Invoices\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_invoice][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_create_new_invoice']) ? $value['cqpim_create_new_invoice'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_invoice][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Delete Invoices', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_invoices][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['delete_cqpim_invoices']) ? $value['delete_cqpim_invoices'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_invoices][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Send Invoices', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_send_invoices][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_send_invoices']) ? $value['cqpim_send_invoices'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_send_invoices][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Mark Invoices Paid / Add Manual Payments', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_mark_invoice_paid][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_mark_invoice_paid']) ? $value['cqpim_mark_invoice_paid'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_mark_invoice_paid][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Support Tickets', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View Support Tickets', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[cqpim_view_tickets][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_view_tickets']) ? $value['cqpim_view_tickets'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_view_tickets][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Update Support Tickets', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[publish_cqpim_supports][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['publish_cqpim_supports']) ? $value['publish_cqpim_supports'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_supports][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Delete Support Tickets (Requires \'Update Support Tickets\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_supports][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['delete_cqpim_supports']) ? $value['delete_cqpim_supports'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_supports][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Create Support Tickets (Requires \'View Support Tickets\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_supports][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_create_new_supports']) ? $value['cqpim_create_new_supports'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_supports][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('FAQ', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('View FAQ', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[read_cqpim_faq][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['read_cqpim_faq']) ? $value['read_cqpim_faq'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_faq][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Update FAQ', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[publish_cqpim_faqs][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['publish_cqpim_faqs']) ? $value['publish_cqpim_faqs'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_faqs][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Delete FAQ (Requires \'Update FAQ\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_faqs][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['delete_cqpim_faqs']) ? $value['delete_cqpim_faqs'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_faqs][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Create FAQ (Requires \'View FAQ\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_faqs][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_create_new_faqs']) ? $value['cqpim_create_new_faqs'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_faqs][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>							
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Tasks', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Team Members that are not Admins can only access tasks that have been assigned to them or that they are "watching". These permissions control what they can access in those tasks. Team Members can also create ad-hoc or personal tasks which are not related to any projects.', 'cqpim'); ?></td>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Delete Assigned Tasks', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_delete_assigned_tasks][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_delete_assigned_tasks']) ? $value['cqpim_delete_assigned_tasks'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_delete_assigned_tasks][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Messaging System', 'cqpim'); ?></th>
							</tr>
							<tr>
								<td style="text-align:left" colspan="<?php echo $columns; ?>"><?php _e('By default, team members can only message other team members. You can use these settings to enable them to compose messages to other people such as clients.', 'cqpim'); ?></td>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Allow messages to clients from assigned projects', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_message_clients_from_projects][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_message_clients_from_projects']) ? $value['cqpim_message_clients_from_projects'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_message_clients_from_projects][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Allow messages to all clients', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_message_all_clients][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['cqpim_message_all_clients']) ? $value['cqpim_message_all_clients'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_message_all_clients][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<tr>
								<td style="text-align:left"><?php _e('Access all messages (Even if not in conversation)', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[access_cqpim_messaging_admin][]" value="cqpim_admin" /></td>
								<?php 
								$permission = isset($value['access_cqpim_messaging_admin']) ? $value['access_cqpim_messaging_admin'] : array() ;
								$columns = 1;
								foreach($roles as $role) {
									if(in_array($role, $permission)) {
										$checked = 'checked';
									} else {
										$checked = '';
									}
									if($role != 'cqpim_admin') {
										echo '<td><input type="checkbox" name="cqpim_permissions[access_cqpim_messaging_admin][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
										$columns++;
									}
								}
								?>
							</tr>
							<?php if(pto_check_addon_status('expenses')) { ?>
								<tr>
									<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Suppliers', 'cqpim'); ?></th>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('View Suppliers', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[read_cqpim_supplier][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['read_cqpim_supplier']) ? $value['read_cqpim_supplier'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_supplier][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Edit / Update Suppliers', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[publish_cqpim_suppliers][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['publish_cqpim_suppliers']) ? $value['publish_cqpim_suppliers'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_suppliers][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Create New Supplier (Requires \'Edit / Update Suppliers\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_supplier][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['cqpim_create_new_supplier']) ? $value['cqpim_create_new_supplier'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_supplier][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Delete Suppliers', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_suppliers][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['delete_cqpim_suppliers']) ? $value['delete_cqpim_suppliers'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_suppliers][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Expenses', 'cqpim'); ?></th>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('View Own Expenses', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[read_cqpim_expense][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['read_cqpim_expense']) ? $value['read_cqpim_expense'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_expense][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Edit / Update Own Expenses', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[publish_cqpim_expenses][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['publish_cqpim_expenses']) ? $value['publish_cqpim_expenses'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_expenses][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Create New Expense (Requires \'Edit / Update Expenses\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_expense][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['cqpim_create_new_expense']) ? $value['cqpim_create_new_expense'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_expense][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Delete Expenses', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_expenses][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['delete_cqpim_expenses']) ? $value['delete_cqpim_expenses'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_expenses][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>	
								<tr>
									<td style="text-align:left"><?php _e('View ALL Expenses (Admin Page & Individual Expense. Also shows Expenses Tables in Team Members and in Project Financials, if the user has access to those features)', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_view_expenses_admin][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['cqpim_view_expenses_admin']) ? $value['cqpim_view_expenses_admin'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_view_expenses_admin][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Bypass Admin Authorisation for Expenses', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_bypass_expense_auth][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['cqpim_bypass_expense_auth']) ? $value['cqpim_bypass_expense_auth'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_bypass_expense_auth][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>											
								<tr>
									<td style="text-align:left"><?php _e('Authorise Expense Requests', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_auth_expense][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['cqpim_auth_expense']) ? $value['cqpim_auth_expense'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_auth_expense][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
							<?php } ?>	
							<?php if(pto_check_addon_status('reporting')) { ?>
								<tr>
									<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Reporting', 'cqpim'); ?></th>
								</tr>	
								<tr>
									<td style="text-align:left"><?php _e('Access Reporting', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_access_reporting][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['cqpim_access_reporting']) ? $value['cqpim_access_reporting'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_access_reporting][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>										
							<?php } ?>
							<?php if(pto_check_addon_status('subscriptions')) { ?>							
								<tr>
									<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Subscriptions', 'cqpim'); ?></th>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('View Subscriptions', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[read_cqpim_subscription][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['read_cqpim_subscription']) ? $value['read_cqpim_subscription'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_subscription][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Edit / Update Subscriptions', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[publish_cqpim_subscriptions][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['publish_cqpim_subscriptions']) ? $value['publish_cqpim_subscriptions'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_subscriptions][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Create New Subscription (Requires \'Edit / Update Subscriptions\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_subscription][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['cqpim_create_new_subscription']) ? $value['cqpim_create_new_subscription'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_subscription][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Delete Subscriptions', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_subscriptions][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['delete_cqpim_subscriptions']) ? $value['delete_cqpim_subscriptions'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_subscriptions][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('Subscription Plans', 'cqpim'); ?></th>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('View Subscription Plans', 'cqpim'); ?> <input type="hidden" name="cqpim_permissions[read_cqpim_plan][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['read_cqpim_plan']) ? $value['read_cqpim_plan'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[read_cqpim_plan][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Edit / Update Subscription Plans', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[publish_cqpim_plans][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['publish_cqpim_plans']) ? $value['publish_cqpim_plans'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[publish_cqpim_plans][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Create New Subscription Plan (Requires \'Edit / Update Subscription Plans\')', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[cqpim_create_new_plan][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['cqpim_create_new_plan']) ? $value['cqpim_create_new_plan'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[cqpim_create_new_plan][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Delete Subscription Plans', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[delete_cqpim_plans][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['delete_cqpim_plans']) ? $value['delete_cqpim_plans'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[delete_cqpim_plans][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>
							<?php } ?>
							<?php if(pto_check_addon_status('woocommerce')) { ?>							
								<tr>
									<th style="font-weight:bold; border-left: 1px solid #ececec; text-align:left" colspan="<?php echo $columns; ?>"><?php _e('WooCommerce', 'cqpim'); ?></th>
								</tr>
								<tr>
									<td style="text-align:left"><?php _e('Access WooCommerce Dashboard', 'cqpim'); ?><input type="hidden" name="cqpim_permissions[view_cqpim_woocommerce][]" value="cqpim_admin" /></td>
									<?php 
									$permission = isset($value['view_cqpim_woocommerce']) ? $value['view_cqpim_woocommerce'] : array() ;
									$columns = 1;
									foreach($roles as $role) {
										if(in_array($role, $permission)) {
											$checked = 'checked';
										} else {
											$checked = '';
										}
										if($role != 'cqpim_admin') {
											echo '<td><input type="checkbox" name="cqpim_permissions[view_cqpim_woocommerce][]" value="' . $role . '" ' . $checked . ' /><span style="font-weight:normal" class="mobile">' . ucwords($role) . '</span></td>';
											$columns++;
										}
									}
									?>
								</tr>								
							<?php } ?>
						</tbody>
					</table>
				<?php } else {
					echo '<p>' . _e('No custom roles found.', 'cqpim') . '</p>';
				} ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cqpim'); ?>" />
				</p>
			</div>
			</div>
		</div>
	</form>
<?php } 