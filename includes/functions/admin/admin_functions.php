<?php
function pto_admin_head_css() { ?>
	<style>
	#toplevel_page_pto-dashboard .wp-submenu li {display:none}
	#toplevel_page_pto-dashboard .wp-submenu li.wp-first-item {display:inline-block}	
	</style>
	<?php
	$screen = get_current_screen();
	if(strpos($screen->base,'cqpim') !== false || strpos($screen->post_type,'cqpim') !== false || strpos($screen->base,'pto') !== false || strpos($screen->post_type,'pto') !== false) { ?>
		<style>			
			#wpcontent {
				margin-left: 140px;
			}
			#wpbody {
				padding-left:20px;
			}
			.wrap h1 {
				display:none;
			}
			.wrap {
				clear:both;
				padding-top:10px;
			}
			@media only screen and (max-width: 960px) {
				#wpbody {
					padding-left:0px;
				}
			}			
		</style>
	<?php }
}
add_action( 'admin_head', 'pto_admin_head_css', 10 );
function pto_admin_header() {	
	$screen = get_current_screen();		
	if(!empty($screen->post_type)) {		
		$cpt = get_post_type_object( $screen->post_type );			
	}		
	if(strpos($screen->base,'pto') !== false || strpos($screen->post_type,'pto') !== false || strpos($screen->base,'cqpim') !== false || strpos($screen->post_type,'cqpim') !== false) {	
		$user = wp_get_current_user();
		$team_id = pto_get_team_from_userid($user);
		$user_name = $user->display_name;	
		$role = pto_get_user_role($user);
		$unread = pto_new_messages($user->ID);
		$unread_stat = isset($unread['read_val']) ? $unread['read_val'] : '';
		$unread_qty = isset($unread['new_messages']) ? $unread['new_messages'] : '';
		$avatar = get_option('cqpim_disable_avatars');	
		$messaging = get_option('cqpim_enable_messaging');		
		$notification_count = pto_check_unread_team_notifications($team_id);
		$notifications = pto_get_team_notifications($team_id);
		if(!empty($notifications) && is_array($notifications)) {
			$notifications = array_reverse($notifications);
		}
		$tickets = get_option('disable_tickets');
		?>
		<div style="display:none" id="cqpim_overlay">	
			<div id="cqpim_spinner">
				<img src="<?php echo PTO_PLUGIN_URL . '/img/loading_spinner.gif'; ?>" />
			</div>
		</div>			
		<?php if(!empty($avatar)) { ?>			
			<style>				
				#cqpim_admin_head {height:55px !important}				
			</style>			
		<?php } ?>	
		<div id="cqpim_admin_head">			
			<ul>							
				<?php if(empty($avatar)) { echo '<li class="cqpim_avatar">' . get_avatar( $user->ID, 50, '', false, array('force_display' => true) ) . '</li>'; } else { echo '<li style="height:50px; width:1px;margin-left:-1px">&nbsp;</li>'; } ?>					
				<li><span class="cqpim_username rounded_2"><i class="fa fa-user-circle" aria-hidden="true"></i> <?php echo $user_name; ?></span></li>					
				<li><span class="cqpim_role rounded_2"><i class="fa fa-users" aria-hidden="true"></i> <?php echo $role; ?></span></li>					
				<?php if(!empty($messaging)) { ?>					
					<li>						
						<span class="cqpim_icon">						
							<a <?php if(!empty($unread_qty)) { echo 'class="cqpim_active"'; } ?> href="<?php echo admin_url() . 'admin.php?page=pto-messages'; ?>"><i class="fa fa-envelope-open" aria-hidden="true" title="<?php _e('Messages', 'cqpim'); ?>"></i></a>								
							<?php if(!empty($unread_qty)) { ?>								
								<span class="cqpim_counter"><?php echo $unread_qty; ?></span>								
							<?php } ?>							
						</span>						
					</li>					
				<?php } ?>
				<li>
					<span class="cqpim_icon">
						<a class="cqpim_notifications <?php if(!empty($notification_count)) { ?>cqpim_active<?php } ?>" href="#"><i class="fa fa-bell" aria-hidden="true" title="<?php _e('Notifications', 'cqpim'); ?>"></i></a>
						<?php if(!empty($notification_count)) { ?>								
							<span id="nf_counter" class="cqpim_counter"><?php echo $notification_count; ?></span>								
						<?php } ?>	
					</span>
					<div id="cqpim_notifications" style="display:none">
						<div id="notification_up">
							<i class="fa fa-caret-up"></i>
						</div>
						<div class="inner rounded_4">
							<h3 class="font-white"><?php _e('Notifications', 'cqpim'); ?></h3>
							<div id="notification_list" class="rounded_4">
								<?php if(!empty($notifications)) { ?>
									<ul id="notifications_ul">
										<?php foreach($notifications as $key => $notification) { ?>
											<li <?php if(empty($notification['read'])) { ?>class="unread"<?php } ?>>
												<?php if(empty($avatar)) { ?>
													<div class="notification_avatar">
														<?php echo get_avatar( $notification['from'], 25, '', false, array('force_display' => true) ) ?>
													</div>
												<?php } ?>
												<div class="notification_message" <?php if(empty($avatar)) { ?>style="width:calc(100% - 50px)"<?php } ?>>
													<a href="#" class="notification_item" data-item="<?php echo $notification['item']; ?>" data-key="<?php echo $key; ?>"><?php echo $notification['message']; ?></a><br />
													<span class="notification_time"><?php echo date(get_option('cqpim_date_format') . ' H:i', $notification['time']); ?></span>
													<div class="notification_remove"><a class="nf_remove_button" href="#" data-key="<?php echo $key; ?>" title="<?php _e('Clear Notification', 'cqpim'); ?>"><i class="fa fa-times-circle"></i></a></div>
												</div>
												<div class="clear"></div>
											</li>
										<?php } ?>
									</ul>
								<?php } else { ?>
									<p style="padding:0 10px"><?php _e('You do not have any notifications', 'cqpim'); ?></p>
								<?php } ?>
							</div>
							<div id="notification_actions">
								<a id="mark_all_read_nf" class="cqpim_button cqpim_small_button font-black rounded_2" href="#"><?php _e('Mark All as Read', 'cqpim'); ?></a>
								<a id="clear_all_read_nf" class="cqpim_button cqpim_small_button font-black rounded_2" href="#"><?php _e('Clear All Read', 'cqpim'); ?></a>
								<a id="clear_all_nf" class="cqpim_button cqpim_small_button font-black rounded_2" href="#"><?php _e('Clear All', 'cqpim'); ?></a>
							</div>
						</div>
					</div>
				</li>				
				<?php if(current_user_can('cqpim_team_edit_profile')) { ?>					
					<li>						
						<span class="cqpim_icon">						
							<a href="<?php echo admin_url() . 'admin.php?page=pto-manage-profile'; ?>"><i class="fa fa-sliders" aria-hidden="true" title="<?php _e('Edit my Profile', 'cqpim'); ?>"></i></a>							
						</span>
					</li>
				<?php } ?>
				<li>
					<span class="cqpim_icon">
						<?php
							$login_page_id = get_option('cqpim_login_page');
							$login_url = get_option('cqpim_logout_url');
							if(empty($login_url)) {
								$login_url = get_the_permalink($login_page_id);
							}
						?>
						<a href="<?php echo wp_logout_url($login_url); ?>"><i class="fa fa-sign-out" aria-hidden="true" title="<?php _e('Log Out', 'cqpim'); ?>"></i></a>
					</span>
				</li>
			</ul>
		</div>
		<?php $screen = get_current_screen(); ?>
		<div id="pto_admin_menu">
			<select id="pto_admin_menu_mobile" class="cqpim_mobile">
				<option value="<?php echo admin_url(); ?>admin.php?page=pto-dashboard" <?php if($screen->id == 'toplevel_page_pto-dashboard') { echo 'selected="selected"'; } ?>><?php _e('Dashboard', 'cqpim'); ?></option>
				<optgroup label="<?php _e('My Work', 'cqpim'); ?>">
					<?php if(get_option('cqpim_enable_messaging')) { ?>
						<option value="<?php echo admin_url(); ?>admin.php?page=pto-messages" <?php if(strpos($screen->id, 'pto-messages') !== false) { echo 'selected="selected"'; } ?>><?php _e('My Messages', 'cqpim'); ?></option>
					<?php } ?>
					<?php if(get_option('cqpim_enable_messaging') && current_user_can('access_cqpim_messaging_admin')) { ?>
						<option value="<?php echo admin_url(); ?>admin.php?page=pto-messages-admin" <?php if(strpos($screen->id, 'pto-messages-admin') !== false) { echo 'selected="selected"'; } ?>><?php _e('All Messages (Admin)', 'cqpim'); ?></option>
					<?php } ?>
					<option value="<?php echo admin_url(); ?>admin.php?page=pto-tasks" <?php if(strpos($screen->id, 'pto-tasks') !== false) { echo 'selected="selected"'; } ?>><?php _e('My Tasks', 'cqpim'); ?></option>
					<option value="<?php echo admin_url(); ?>admin.php?page=pto-calendar" <?php if(strpos($screen->id, 'pto-calendar') !== false) { echo 'selected="selected"'; } ?>><?php _e('My Calendar', 'cqpim'); ?></option>
					<?php if(current_user_can('cqpim_dash_view_all_tasks')) { ?>
						<option value="<?php echo admin_url(); ?>admin.php?page=pto-alltasks" <?php if(strpos($screen->id, 'pto-alltasks') !== false) { echo 'selected="selected"'; } ?>><?php _e('All Tasks (Admin)', 'cqpim'); ?></option>
					<?php } ?>
					<?php if(current_user_can('cqpim_view_all_files')) { ?>
						<option value="<?php echo admin_url(); ?>admin.php?page=pto-files-admin" <?php if(strpos($screen->id, 'pto-files-admin') !== false) { echo 'selected="selected"'; } ?>><?php _e('All Files (Admin)', 'cqpim'); ?></option>
					<?php } ?>					
				</optgroup>
				<?php if(current_user_can('edit_cqpim_leads') || current_user_can('edit_cqpim_leadforms')) { ?>
					<optgroup label="<?php _e('Leads', 'cqpim'); ?>">
						<?php if(current_user_can('edit_cqpim_leads')) { ?>
							<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_lead" <?php if($screen->id == 'edit-cqpim_lead') { echo 'selected="selected"'; } ?>><?php _e('Leads', 'cqpim'); ?></option>
						<?php } ?>
						<?php if(current_user_can('edit_cqpim_leadforms')) { ?>
							<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_leadform" <?php if($screen->id == 'edit-cqpim_leadform') { echo 'selected="selected"'; } ?>><?php _e('Lead Forms', 'cqpim'); ?></option>
						<?php } ?>
					</optgroup>
				<?php } ?>
				<?php if(current_user_can('edit_cqpim_clients')) { ?>
					<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_client" <?php if($screen->id == 'edit-cqpim_client') { echo 'selected="selected"'; } ?>><?php _e('Clients', 'cqpim'); ?></option>
				<?php } ?>
				<?php if(get_option('enable_quotes') == 1) { ?>
					<?php if(current_user_can('edit_cqpim_quotes') || current_user_can('edit_cqpim_forms') || current_user_can('edit_cqpim_templates') || get_option('enable_quotes') == 1 && current_user_can('edit_cqpim_quote_template')) { ?>
						<optgroup label="<?php _e('Quotes', 'cqpim'); ?>">
							<?php if(current_user_can('edit_cqpim_quotes')) { ?>
								<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_quote" <?php if($screen->id == 'edit-cqpim_quote') { echo 'selected="selected"'; } ?>><?php _e('Quotes', 'cqpim'); ?></option>
							<?php } ?>
							<?php if(current_user_can('edit_cqpim_forms')) { ?>
								<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_forms" <?php if($screen->id == 'edit-cqpim_forms') { echo 'selected="selected"'; } ?>><?php _e('Quote Forms', 'cqpim'); ?></option>
							<?php } ?>
							<?php if(get_option('enable_quotes') == 1 && current_user_can('edit_cqpim_quote_template') && !empty($hide)) { ?>
								<option value="<?php echo admin_url(); ?>admin.php?page=pto-quote-template" <?php if(strpos($screen->id, 'pto-quote-template') !== false) { echo 'selected="selected"'; } ?>><?php _e('Quote Template', 'cqpim'); ?></option>							
							<?php } ?>	
							<?php if(current_user_can('edit_cqpim_templates')) { ?>
								<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_templates" <?php if($screen->id == 'edit-cqpim_templates') { echo 'selected="selected"'; } ?>><?php _e('Milestone / Task Templates', 'cqpim'); ?></option>
							<?php } ?>						
						</optgroup>
					<?php } ?>
				<?php } ?>
				<?php if(current_user_can('edit_cqpim_projects') || current_user_can('edit_cqpim_terms') || current_user_can('edit_cqpim_templates') || get_option('enable_project_contracts') == 1 && current_user_can('edit_cqpim_contract_template')) { ?>
					<optgroup label="<?php _e('Projects', 'cqpim'); ?>">
						<?php if(current_user_can('edit_cqpim_projects')) { ?>
							<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_project" <?php if($screen->id == 'edit-cqpim_project') { echo 'selected="selected"'; } ?>><?php _e('Projects', 'cqpim'); ?></option>
						<?php } ?>
						<?php if(current_user_can('edit_cqpim_terms')) { ?>
							<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_terms" <?php if($screen->id == 'edit-cqpim_terms') { echo 'selected="selected"'; } ?>><?php _e('Terms Templates', 'cqpim'); ?></option>
						<?php } ?>
						<?php if(get_option('enable_project_contracts') == 1 && current_user_can('edit_cqpim_contract_template') && !empty($hide)) { ?>
							<option value="<?php echo admin_url(); ?>admin.php?page=pto-contract-template" <?php if(strpos($screen->id, 'pto-contract-template') !== false) { echo 'selected="selected"'; } ?>><?php _e('Contract Template', 'cqpim'); ?></option>							
						<?php } ?>
						<?php if(current_user_can('edit_cqpim_templates')) { ?>
							<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_templates" <?php if($screen->id == 'edit-cqpim_templates') { echo 'selected="selected"'; } ?>><?php _e('Milestone / Task Templates', 'cqpim'); ?></option>
						<?php } ?>
					</optgroup>
				<?php } ?>
				<?php if(get_option('disable_invoices') != 1) { ?>
					<?php if(current_user_can('edit_cqpim_invoices')) { ?>
						<optgroup label="<?php _e('Invoices', 'cqpim'); ?>">
							<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_invoice" <?php if($screen->id == 'edit-cqpim_invoice') { echo 'selected="selected"'; } ?>><?php _e('Invoices', 'cqpim'); ?></option>
							<option value="<?php echo admin_url(); ?>admin.php?page=pto-recinvoices" <?php if($screen->id == 'pto-recinvoices') { echo 'selected="selected"'; } ?>><?php _e('Recurring Invoices', 'cqpim'); ?></option>
						</optgroup>
					<?php } ?>
				<?php } ?>
				<?php if(pto_check_addon_status('subscriptions')) { ?>
					<?php if(current_user_can('edit_cqpim_subscriptions') || current_user_can('edit_cqpim_plans')) { ?>
						<optgroup label="<?php _e('Subscriptions', 'cqpim'); ?>">
							<?php if(current_user_can('edit_cqpim_subscriptions')) { ?>
								<option value="<?php echo admin_url(); ?>admin.php?page=pto-subscriptions" <?php if($screen->id == 'edit-cqpim_subscription') { echo 'selected="selected"'; } ?>><?php _e('Subscriptions Dashboard', 'cqpim'); ?></option>				
							<?php } ?>
							<?php if(current_user_can('edit_cqpim_plans')) { ?>
								<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_plan" <?php if($screen->id == 'edit-cqpim_plan') { echo 'selected="selected"'; } ?>><?php _e('Subscription Plans', 'cqpim'); ?></option>				
							<?php } ?>
						</optgroup>
					<?php } ?>
				<?php } ?>
				<?php if(pto_check_addon_status('woocommerce') && current_user_can('view_cqpim_woocommerce')) { ?>
					<option value="<?php echo admin_url(); ?>admin.php?page=pto-woocommerce" <?php if(strpos($screen->id, 'pto-woocommerce') !== false) { echo 'selected="selected"'; } ?>><?php _e('WooCommerce', 'cqpim'); ?></option>
				<?php } ?>
				<?php if(current_user_can('edit_cqpim_teams') || current_user_can('edit_cqpim_permissions')) { ?>
					<optgroup label="<?php _e('Teams', 'cqpim'); ?>">
						<?php if(current_user_can('edit_cqpim_teams')) { ?>
							<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_teams" <?php if($screen->id == 'edit-cqpim_teams') { echo 'selected="selected"'; } ?>><?php _e('Team Members', 'cqpim'); ?></option>
						<?php } ?>
						<?php if(current_user_can('edit_cqpim_permissions')) { ?>
							<option value="<?php echo admin_url(); ?>admin.php?page=pto-permissions" <?php if(strpos($screen->id, 'pto-permissions') !== false) { echo 'selected="selected"'; } ?>><?php _e('Roles & Permissions', 'cqpim'); ?></option>
						<?php } ?>				
					</optgroup>
				<?php } ?>
				<?php if(pto_check_addon_status('expenses')) { ?>
					<?php if(current_user_can('edit_cqpim_suppliers') || current_user_can('edit_cqpim_expenses') || current_user_can('cqpim_view_expenses_admin')) { ?>
						<optgroup label="<?php _e('Expenses', 'cqpim'); ?>">
							<?php if(current_user_can('edit_cqpim_suppliers')) { ?>
								<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_supplier" <?php if($screen->id == 'edit-cqpim_supplier') { echo 'selected="selected"'; } ?>><?php _e('Suppliers', 'cqpim'); ?></option>
							<?php } ?>
							<?php if(current_user_can('edit_cqpim_expenses')) { ?>
								<option value="<?php echo admin_url(); ?>admin.php?page=pto-expenses" <?php if(strpos($screen->id, 'pto-expenses') !== false) { echo 'selected="selected"'; } ?>><?php _e('My Expenses', 'cqpim'); ?></option>
							<?php } ?>
							<?php if(current_user_can('cqpim_view_expenses_admin')) { ?>
								<option value="<?php echo admin_url(); ?>admin.php?page=pto-allexpenses" <?php if(strpos($screen->id, 'pto-allexpenses') !== false) { echo 'selected="selected"'; } ?>><?php _e('All Expenses (Admin)', 'cqpim'); ?></option>
							<?php } ?>			
						</optgroup>
					<?php } ?>
				<?php } ?>
				<?php if(current_user_can('cqpim_view_tickets') && empty($tickets) || get_option('cqpim_enable_faq') && current_user_can('edit_cqpim_faqs')) { ?>
					<optgroup label="<?php _e('Support', 'cqpim'); ?>">
						<?php if(current_user_can('cqpim_view_tickets') && empty($tickets)) { ?>
							<option value="<?php echo admin_url(); ?>admin.php?page=pto-tickets" <?php if(strpos($screen->id, 'pto-tickets') !== false) { echo 'selected="selected"'; } ?>><?php _e('Support Tickets', 'cqpim'); ?></option>
						<?php } ?>
						<?php if(get_option('cqpim_enable_faq') && current_user_can('edit_cqpim_faqs')) { ?>
							<option value="<?php echo admin_url(); ?>edit.php?post_type=cqpim_faq" <?php if(strpos($screen->id, 'edit-pto_faq') !== false) { echo 'selected="selected"'; } ?>><?php _e('FAQ', 'cqpim'); ?></option>
						<?php } ?>						
					</optgroup>
				<?php } ?>
				<?php if(pto_check_addon_status('reporting') && current_user_can('cqpim_access_reporting')
					|| current_user_can('edit_cqpim_settings')
					|| pto_check_addon_status('envato') && current_user_can('edit_cqpim_settings')
					|| get_option('cqpim_show_docs_link') && current_user_can('edit_cqpim_help')) { ?>
					<optgroup label="<?php _e('Settings', 'cqpim'); ?>">
						<?php if(pto_check_addon_status('reporting') && current_user_can('cqpim_access_reporting')) { ?>
							<option value="<?php echo admin_url(); ?>admin.php?page=pto-reporting" <?php if(strpos($screen->id, 'pto-reporting') !== false) { echo 'selected="selected"'; } ?>><?php _e('Reporting', 'cqpim'); ?></option>
						<?php } ?>
						<?php if(current_user_can('edit_cqpim_settings')) { ?>
							<option value="<?php echo admin_url(); ?>admin.php?page=pto-settings" <?php if(strpos($screen->id, 'pto-settings') !== false) { echo 'selected="selected"'; } ?>><?php _e('Settings', 'cqpim'); ?></option>
							<option value="<?php echo admin_url(); ?>admin.php?page=pto-custom-fields" <?php if(strpos($screen->id, 'pto-custom-fields') !== false) { echo 'selected="selected"'; } ?>><?php _e('Custom Fields', 'cqpim'); ?></option>
						<?php } ?>
						<?php if(pto_check_addon_status('envato') && current_user_can('edit_cqpim_settings')) { ?>
							<option value="<?php echo admin_url(); ?>admin.php?page=pto-envato-settings" <?php if(strpos($screen->id, 'pto-envato-settings') !== false) { echo 'selected="selected"'; } ?>><?php _e('Envato Settings', 'cqpim'); ?></option>
						<?php } ?>
						<?php if(get_option('cqpim_show_docs_link') && current_user_can('edit_cqpim_help')) { ?>
							<option value="http://projectopia.io" target="_blank"><?php _e('Documentation', 'cqpim'); ?></option>
						<?php } ?>			
					</optgroup>
				<?php } ?>			
			</select>
			<ul id="pto_admin_menu_cont" class="desktop_only">
				<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-dashboard"><?php _e('Dashboard', 'cqpim'); ?></a></li>
				<li class="drop"><span><?php _e('My Work', 'cqpim'); ?> <i class="fa fa-chevron-circle-down" aria-hidden="true"></i></span>
					<ul class="pto_sub">
						<?php if(get_option('cqpim_enable_messaging')) { ?>
							<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-messages"><?php _e('My Messages', 'cqpim'); ?></a></li>
						<?php } ?>
						<?php if(get_option('cqpim_enable_messaging') && current_user_can('access_cqpim_messaging_admin')) { ?>
							<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-messages-admin"><?php _e('All Messages (Admin)', 'cqpim'); ?></a></li>
						<?php } ?>
						<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-tasks"><?php _e('My Tasks', 'cqpim'); ?></a></li>
						<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-calendar"><?php _e('My Calendar', 'cqpim'); ?></a></li>
						<?php if(current_user_can('cqpim_dash_view_all_tasks')) { ?>
							<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-alltasks"><?php _e('All Tasks (Admin)', 'cqpim'); ?></a></li>
						<?php } ?>
						<?php if(current_user_can('cqpim_view_all_files')) { ?>
							<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-files-admin"><?php _e('All Files (Admin)', 'cqpim'); ?></a></li>
						<?php } ?>	
					</ul>
				</li>
				<?php if(current_user_can('edit_cqpim_leads') || current_user_can('edit_cqpim_leadforms')) { ?>
					<li class="drop"><span><?php _e('Leads', 'cqpim'); ?> <i class="fa fa-chevron-circle-down" aria-hidden="true"></i></span>
						<ul class="pto_sub">
							<?php if(current_user_can('edit_cqpim_leads')) { ?>
								<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_lead"><?php _e('Leads', 'cqpim'); ?></a></li>
							<?php } ?>
							<?php if(current_user_can('edit_cqpim_leadforms')) { ?>
								<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_leadform"><?php _e('Lead Forms', 'cqpim'); ?></a></li>
							<?php } ?>
						</ul>
					</li>
				<?php } ?>
				<?php if(current_user_can('edit_cqpim_clients')) { ?>
					<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_client"><?php _e('Clients', 'cqpim'); ?></a></li>
				<?php } ?>
				<?php if(get_option('enable_quotes') == 1) { ?>
					<?php if(current_user_can('edit_cqpim_quotes') || current_user_can('edit_cqpim_forms') || current_user_can('edit_cqpim_templates') || get_option('enable_quotes') == 1 && current_user_can('edit_cqpim_quote_template')) { ?>
						<li class="drop"><span><?php _e('Quotes', 'cqpim'); ?> <i class="fa fa-chevron-circle-down" aria-hidden="true"></i></span>
							<ul class="pto_sub">
								<?php if(current_user_can('edit_cqpim_quotes')) { ?>
									<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_quote"><?php _e('Quotes', 'cqpim'); ?></a></li>
								<?php } ?>
								<?php if(current_user_can('edit_cqpim_forms')) { ?>
									<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_forms"><?php _e('Quote Forms', 'cqpim'); ?></a></li>
								<?php } ?>
								<?php if(get_option('enable_quotes') == 1 && current_user_can('edit_cqpim_quote_template') && !empty($hide)) { ?>
									<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-quote-template"><?php _e('Quote Template', 'cqpim'); ?></a></li>						
								<?php } ?>	
								<?php if(current_user_can('edit_cqpim_templates')) { ?>
									<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_templates"><?php _e('Milestone / Task Templates', 'cqpim'); ?></a></li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
				<?php } ?>
				<?php if(current_user_can('edit_cqpim_projects') || current_user_can('edit_cqpim_terms') || current_user_can('edit_cqpim_templates') || get_option('enable_project_contracts') == 1 && current_user_can('edit_cqpim_contract_template')) { ?>
					<li class="drop"><span><?php _e('Projects', 'cqpim'); ?> <i class="fa fa-chevron-circle-down" aria-hidden="true"></i></span>
						<ul class="pto_sub">
							<?php if(current_user_can('edit_cqpim_projects')) { ?>
								<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_project"><?php _e('Projects', 'cqpim'); ?></a></li>
							<?php } ?>
							<?php if(current_user_can('edit_cqpim_terms')) { ?>
								<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_terms"><?php _e('Terms Templates', 'cqpim'); ?></a></li>
							<?php } ?>
							<?php if(get_option('enable_project_contracts') == 1 && current_user_can('edit_cqpim_contract_template') && !empty($hide)) { ?>
								<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-contract-template"><?php _e('Contract Template', 'cqpim'); ?></a></li>							
							<?php } ?>
							<?php if(current_user_can('edit_cqpim_templates')) { ?>
								<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_templates"><?php _e('Milestone / Task Templates', 'cqpim'); ?></a></li>
							<?php } ?>
						</ul>
					</li>
				<?php } ?>
				<?php if(get_option('disable_invoices') != 1) { ?>
					<?php if(current_user_can('edit_cqpim_invoices')) { ?>
						<li class="drop"><span><?php _e('Invoices', 'cqpim'); ?> <i class="fa fa-chevron-circle-down" aria-hidden="true"></i></span>
							<ul class="pto_sub">					
								<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_invoice"><?php _e('Invoices', 'cqpim'); ?></a></li>
								<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-recinvoices"><?php _e('Recurring Invoices', 'cqpim'); ?></a></li>
							</ul>
						</li>
					<?php } ?>
				<?php } ?>
				<?php if(pto_check_addon_status('subscriptions')) { ?>
					<?php if(current_user_can('edit_cqpim_subscriptions') || current_user_can('edit_cqpim_plans')) { ?>
						<li class="drop"><span><?php _e('Subscriptions', 'cqpim'); ?> <i class="fa fa-chevron-circle-down" aria-hidden="true"></i></span>
							<ul class="pto_sub">					
							<?php if(current_user_can('edit_cqpim_subscriptions')) { ?>
								<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-subscriptions"><?php _e('Subscriptions Dashboard', 'cqpim'); ?></a></li>
							<?php } ?>
							<?php if(current_user_can('edit_cqpim_plans')) { ?>	
								<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_plan"><?php _e('Subscription Plans', 'cqpim'); ?></a></li>
							<?php } ?>
							</ul>
						</li>
					<?php } ?>
				<?php } ?>
				<?php if(pto_check_addon_status('woocommerce') && current_user_can('view_cqpim_woocommerce')) { ?>
					<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-woocommerce"><?php _e('WooCommerce', 'cqpim'); ?></a></li>
				<?php } ?>
				<?php if(current_user_can('edit_cqpim_teams') || current_user_can('edit_cqpim_permissions')) { ?>
					<li class="drop"><span><?php _e('Teams', 'cqpim'); ?> <i class="fa fa-chevron-circle-down" aria-hidden="true"></i></span>
						<ul class="pto_sub">
							<?php if(current_user_can('edit_cqpim_teams')) { ?>
								<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_teams"><?php _e('Team Members', 'cqpim'); ?></a></li>
							<?php } ?>
							<?php if(current_user_can('edit_cqpim_permissions')) { ?>
								<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-permissions"><?php _e('Roles & Permissions', 'cqpim'); ?></a></li>
							<?php } ?>
						</ul>					
					</li>
				<?php } ?>
				<?php if(pto_check_addon_status('expenses')) { ?>
					<?php if(current_user_can('edit_cqpim_suppliers') || current_user_can('edit_cqpim_expenses') || current_user_can('cqpim_view_expenses_admin')) { ?>
						<li class="drop"><span><?php _e('Expenses', 'cqpim'); ?> <i class="fa fa-chevron-circle-down" aria-hidden="true"></i></span>
							<ul class="pto_sub">
								<?php if(current_user_can('edit_cqpim_suppliers')) { ?>
									<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_supplier"><?php _e('Suppliers', 'cqpim'); ?></a></li>
								<?php } ?>
								<?php if(current_user_can('edit_cqpim_expenses')) { ?>
									<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-expenses"><?php _e('My Expenses', 'cqpim'); ?></a></li>
								<?php } ?>
								<?php if(current_user_can('cqpim_view_expenses_admin')) { ?>
									<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-allexpenses"><?php _e('All Expenses (Admin)', 'cqpim'); ?></a></li>
								<?php } ?>
							</ul>				
						</li>
					<?php } ?>
				<?php } ?>
				<?php if(current_user_can('cqpim_view_tickets') && empty($tickets) || get_option('cqpim_enable_faq') && current_user_can('edit_cqpim_faqs')) { ?>
					<li class="drop"><span><?php _e('Support', 'cqpim'); ?> <i class="fa fa-chevron-circle-down" aria-hidden="true"></i></span>
						<ul class="pto_sub">
							<?php if(current_user_can('cqpim_view_tickets') && empty($tickets)) { ?>
								<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-tickets"><?php _e('Support Tickets', 'cqpim'); ?></a></li>
							<?php } ?>
							<?php if(get_option('cqpim_enable_faq') && current_user_can('edit_cqpim_faqs')) { ?>
								<li><a href="<?php echo admin_url(); ?>edit.php?post_type=cqpim_faq"><?php _e('FAQ', 'cqpim'); ?></a></li>
							<?php } ?>
						</ul>
					</li>
				<?php } ?>
				<?php if(pto_check_addon_status('reporting') && current_user_can('cqpim_access_reporting')
					|| current_user_can('edit_cqpim_settings')
					|| pto_check_addon_status('envato') && current_user_can('edit_cqpim_settings')
					|| get_option('cqpim_show_docs_link') && current_user_can('edit_cqpim_help')) { ?>
					<li class="drop"><span><?php _e('Settings', 'cqpim'); ?> <i class="fa fa-chevron-circle-down" aria-hidden="true"></i></span>
						<ul class="pto_sub">
							<?php if(pto_check_addon_status('reporting') && current_user_can('cqpim_access_reporting')) { ?>
								<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-reporting"><?php _e('Reporting', 'cqpim'); ?></a></li>
							<?php } ?>
							<?php if(current_user_can('edit_cqpim_settings')) { ?>
								<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-settings"><?php _e('Settings', 'cqpim'); ?></a></li>
								<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-custom-fields"><?php _e('Custom Fields', 'cqpim'); ?></a></li>
							<?php } ?>
							<?php if(pto_check_addon_status('envato') && current_user_can('edit_cqpim_settings')) { ?>
								<li><a href="<?php echo admin_url(); ?>admin.php?page=pto-envato-settings"><?php _e('Envato Settings', 'cqpim'); ?></a></li>
							<?php } ?>
							<?php if(get_option('cqpim_show_docs_link') && current_user_can('edit_cqpim_help')) { ?>
								<li><a href="http://projectopia.io" target="_blank"><?php _e('Documentation', 'cqpim'); ?></a></li>
							<?php } ?>
						</ul>				
					</li>
				<?php } ?>
			</ul>
		</div>
		<div id="cqpim_admin_title">
			<?php if($screen->base == 'toplevel_page_pto-dashboard') {
				_e('Dashboard', 'cqpim');
				echo '<div class="clear"></div>';
			} else {
				if($screen->id == 'cqpim_client') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_client' . '">' . __('Clients', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_lead') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_lead' . '">' . __('Leads', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';
				} elseif($screen->id == 'cqpim_leadform') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_leadform' . '">' . __('Lead Forms', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';
				} elseif($screen->id == 'cqpim_tasks') {
					$id = get_the_ID();
					$post = get_post($id);	
					$project_id = get_post_meta($post->ID, 'project_id', true);
					$project = get_post($project_id);
					$project_link = get_edit_post_link($project);
					if(!empty($project_id)) {
						echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
						echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_project' . '">' . __('Projects', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
						echo '<a href="' . $project_link . '">' . $project->post_title . '</a> <i class="fa fa-circle"></i> ';
						echo $post->post_title;						
					} else {
						echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
						echo '<a href="' . admin_url() . 'admin.php?page=pto-tasks' . '">' . __('My Tasks', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
						echo $post->post_title;
					}
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_bug') {
					$id = get_the_ID();
					$post = get_post($id);
					$project = get_post_meta($id, 'bug_project', true);
					$project = get_post($project);
					$project_link = get_edit_post_link($project);
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_project' . '">' . __('Projects', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . $project_link . '">' . $project->post_title . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_project') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_project' . '">' . __('Projects', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_plan') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_plan' . '">' . __('Subscription Plans', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_subscription') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'admin.php?page=pto-subscriptions' . '">' . __('Subscriptions Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_forms') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_forms' . '">' . __('Quote Forms', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_invoice') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_invoice' . '">' . __('Invoices', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_teams') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_teams' . '">' . __('Team Members', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_support') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'admin.php?page=pto-tickets' . '">' . __('Support Tickets', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_templates') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_templates' . '">' . __('Milestone Templates', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_faq') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_faq' . '">' . __('FAQ', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_quote') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_quote' . '">' . __('Quotes', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_supplier') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'edit.php?post_type=cqpim_supplier' . '">' . __('Suppliers', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_expense') {
					$id = get_the_ID();
					$post = get_post($id);		
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo '<a href="' . admin_url() . 'admin.php?page=pto-expenses' . '">' . __('Expenses', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo $post->post_title;
					echo '<div class="clear"></div>';						
				} elseif($screen->id == 'cqpim_page_cqpim-messages') {
					$conversation = isset($_GET['conversation']) ? $_GET['conversation'] : '';
					if(!empty($conversation)) {
						$args = array(
							'post_type' => 'cqpim_conversations',
							'post_status' => 'private',
							'posts_per_page' => 1,
							'meta_key' => 'conversation_id',
							'meta_value' => $conversation
						);
						$conversations = get_posts($args);
						$conversation = isset($conversations[0]) ? $conversations[0] : array();
						echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
						echo '<a href="' . admin_url() . 'admin.php?page=pto-messages' . '">' . __('My Messages', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
						echo esc_html( $conversation->post_title ) ;
						echo '<div class="clear"></div>';							
					} else {
						echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
						echo esc_html( get_admin_page_title() ) ;
						echo '<div class="clear"></div>';							
					}
				} else {
					echo '<a href="' . admin_url() . 'admin.php?page=pto-dashboard' . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ';
					echo esc_html( get_admin_page_title() ) ;
					echo '<div class="clear"></div>';
				}
			} ?>
		</div>
<?php }
}
add_action('in_admin_header', 'pto_admin_header', 10);
add_filter( 'default_title', 'pto_set_default_quote_post_title', 10, 2 );
function pto_set_default_quote_post_title( $title, $post ){
	if( $post->post_type == 'cqpim_quote' ){
		$id = $post->ID;
		$title = "%%CLIENT_COMPANY%% - %%TYPE%%: %%QUOTE_REF%%";
		return $title;
	}
	if( $post->post_type == 'cqpim_client' ){
		$id = $post->ID;
		$title = '%%CLIENT_COMPANY%%';
		return $title;
	}
	if( $post->post_type == 'cqpim_invoice' ){
		$id = $post->ID;
		$title = pto_get_invoice_id();
		return $title;
	}
	if( $post->post_type == 'cqpim_teams' ){
		$id = $post->ID;
		$title = "%%NAME%%";
		return $title;
	}
	if( $post->post_type == 'cqpim_support' ){
		$id = $post->ID;
		$title = "$id";
		return $title;
	}
	if( $post->post_type == 'cqpim_subscription' ){
		$id = $post->ID;
		$title = "$id";
		return $title;
	}
}
add_action( "wp_ajax_pto_filter_calendar", "pto_filter_calendar");
function pto_filter_calendar() {
	$filters = isset($_POST['filters']) ? $_POST['filters'] : '';
	$_SESSION['cal_filters'] = $filters;	
	exit();
}
add_action( 'edit_post', 'pto_assign_post_visibility', 50 );
function pto_assign_post_visibility( $post_id ){	
	$post = get_post($post_id);
	$password = isset($post->post_password) ? $post->post_password : '';
	if(empty($password)) {
		$password = pto_random_string(10);
	}
	if($post->post_status != 'trash' && $post->post_status != 'draft') {
		if($post->post_type == 'cqpim_invoice' || $post->post_type == 'cqpim_tasks') {
			$post_updated = array(
				'ID' => $post_id,
				'post_status' => 'publish',
				'post_password' => $password,
			);
			remove_action('edit_post', 'pto_assign_post_visibility', 50 );
			wp_update_post( $post_updated );
			add_action('edit_post', 'pto_assign_post_visibility', 50 );		
		}
		if($post->post_type == 'cqpim_templates' || 
		$post->post_type == 'cqpim_quote' || 
		$post->post_type == 'cqpim_terms' || 
		$post->post_type == 'cqpim_forms' || 
		$post->post_type == 'cqpim_project' || 
		$post->post_type == 'cqpim_client' || 
		$post->post_type == 'cqpim_teams' || 
		$post->post_type == 'cqpim_support' || 
		$post->post_type == 'cqpim_supplier' || 
		$post->post_type == 'cqpim_expense' || 
		$post->post_type == 'cqpim_bug' ||
		$post->post_type == 'cqpim_plan' ||
		$post->post_type == 'cqpim_subscription' ||
		$post->post_type == 'cqpim_leadform' ||
		$post->post_type == 'cqpim_lead'
		) {
			$post_updated = array(
				'ID' => $post_id,
				'post_status' => 'private',
			);
			remove_action('edit_post', 'pto_assign_post_visibility', 50 );
			wp_update_post( $post_updated );
			add_action('edit_post', 'pto_assign_post_visibility', 50 );			
		}
	}
}
function  pto_user_online_update(){
	if ( is_user_logged_in()) {
		$logged_in_users = get_transient('online_status');
		$user = wp_get_current_user();
		$no_need_to_update = isset($logged_in_users[$user->ID])
			&& $logged_in_users[$user->ID] >  (time() - (1 * 60));
		if(!$no_need_to_update){
		  $logged_in_users[$user->ID] = time();
		  set_transient('online_status', $logged_in_users, (1*60));
		}
	}
}
add_action( 'admin_init', 'pto_user_online_update' );
function pto_display_logged_in_users(){
	$logged_in_users = get_transient('online_status');
	if ( !empty( $logged_in_users ) ) {
		echo '<h3>' . __('Clients', 'cqpim') . '</h3>';
		$i = 0;
		foreach ( $logged_in_users as $key => $value) {
			$user = get_user_by( 'id', $key );
			if(in_array('cqpim_client', $user->roles)) {
				// Assignment
				$assigned = pto_get_client_from_userid($user);
				echo '<div class="online-user"><a href="' . get_edit_post_link($assigned) . '" title="' . $user->display_name . '">';
						echo get_avatar( $user->ID, 80, '', false, array('force_display' => true) ); 							
				echo '</a></div>';
				$i++;
			}
		}
		echo '<div class="clear"></div>';
		if($i == 0) {
			echo '<p>' . __('There are no clients online', 'cqpim') . '</p>';
		}
		echo '<h3>' . __('Team Members', 'cqpim') . '</h3>';
		$i = 0;
		foreach ( $logged_in_users as $key => $value) {
			$user = get_user_by( 'id', $key );
				if(!in_array('cqpim_client', $user->roles)) {
				// Assignment
				$assigned = pto_get_team_from_userid($user);
				echo '<div class="online-user"><a href="' . get_edit_post_link($assigned) . '" title="' . $user->display_name . '">';
						echo get_avatar( $user->ID, 80, '', false, array('force_display' => true) ); 							
				echo '</a></div>';
				$i++;
			}
		}
		if($i == 0) {
			echo '<p>' . __('There are no team members online', 'cqpim') . '</p>';
		}
		echo '<div class="clear"></div>';
	} else {
		echo '<p><strong>' . __('There are currently no users online', 'cqpim') . '</strong></p>';
	}
}
function pto_clear_transient_on_logout() {
	$user_id = get_current_user_id();
	$users_transient_id = get_transient('online_status');
	if(is_array($users_transient_id)){
		foreach($users_transient_id as $id => $value ){
			if ( $id == $user_id ) {
				unset($users_transient_id[$user_id]);
				set_transient('online_status', $users_transient_id, (2*60));
				break;
			}
		}
	} else {
		delete_transient('online_status');
	}
}
add_action('clear_auth_cookie', 'pto_clear_transient_on_logout');
add_action( "wp_ajax_pto_remove_logo", "pto_remove_logo");	
function pto_remove_logo() {
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	update_option($type, '');
	exit();
}
function pto_return_currency_select() {
	$codes = array(	
		'AUD' => __('Australian Dollar (AUD)', 'cqpim'),
		'BRL' => __('Brazilian Real (BRL)', 'cqpim'),
		'CAD' => __('Canadian Dollar (CAD)', 'cqpim'),
		'CZK' => __('Czech Koruna (CZK)', 'cqpim'),
		'DKK' => __('Danish Krone (DKK)', 'cqpim'),
		'EUR' => __('Euro (EUR)', 'cqpim'),
		'HKD' => __('Hong Kong Dollar (HKD)', 'cqpim'),
		'ILS' => __('Israeli New Sheqel (ILS)', 'cqpim'),
		'MXN' => __('Mexican Peso (MXN)', 'cqpim'),
		'NOK' => __('Norwegian Krone (NOK)', 'cqpim'),
		'NZD' => __('New Zealand Dollar (NZD)', 'cqpim'),
		'PHP' => __('Philippine Peso (PHP)', 'cqpim'),
		'PLN' => __('Polish Zloty (PLN)', 'cqpim'),
		'GBP' => __('Pound Sterling (GBP)', 'cqpim'),
		'RUB' => __('Russian Ruble (RUB)', 'cqpim'),
		'SGD' => __('Singapore Dollar (SGD)', 'cqpim'),
		'SEK' => __('Swedish Krona (SEK)', 'cqpim'),
		'CHF' => __('Swiss Franc (CHF)', 'cqpim'),
		'THB' => __('Thai Baht (THB)', 'cqpim'),
		'USD' => __('U.S. Dollar (USD)', 'cqpim'),		
	);
	return $codes;
}
function pto_get_user_id_by_display_name( $display_name ) {
	global $wpdb;
	if ( ! $user = $wpdb->get_row( $wpdb->prepare(
		"SELECT `ID` FROM $wpdb->users WHERE `display_name` = %s", $display_name
	) ) )
		return false;
	return $user->ID;
}
function pto_remove_date_filters() {
	$screen = get_current_screen();
	global $typenow;
	if(strpos($typenow, 'cqpim') !== false) {
		return array();
	}
}
add_action( "wp_ajax_nopriv_pto_ajax_login", 
		"pto_ajax_login");
add_action( "wp_ajax_pto_ajax_login", 
		"pto_ajax_login");	
function pto_ajax_login() {
	$username = isset($_POST['username']) ? $_POST['username'] : '';
	$password = isset($_POST['password']) ? $_POST['password'] : '';
	$dash_page = get_option('cqpim_client_page');
	if(empty($username) || empty($password)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('Please enter a username and password', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();			
	} else {
		$creds = array();
		$creds['user_login'] = $username;
		$creds['user_password'] = $password;
		$creds['remember'] = is_ssl();
		$login = wp_signon( $creds, is_ssl() );
		if(is_wp_error($login)) {
			$return =  array( 
				'error' 	=> true,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('Login Failed. Please try again.', 'cqpim') . '</div>',
			);
			header('Content-type: application/json');
			echo json_encode($return);
			exit();			
		} else {
			$roles = $login->roles;
			if(in_array('cqpim_client', $roles)) {
				$redirect = get_the_permalink($dash_page);
			} else {
				$redirect = admin_url() . 'admin.php?page=pto-dashboard';
			}
			$return =  array( 
				'error' 	=> false,
				'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Login Successful. Redirecting to your dashboard.', 'cqpim') . '</div>',
				'redirect' 	=> $redirect,
			);
			header('Content-type: application/json');
			echo json_encode($return);
			exit();			
		}
	}	
}
add_action( "wp_ajax_nopriv_pto_ajax_reset", 
		"pto_ajax_reset");
add_action( "wp_ajax_pto_ajax_reset", 
		"pto_ajax_reset");
function pto_ajax_reset() {
	$username = isset($_POST['username']) ? $_POST['username'] : '';
	if(empty($username)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('You must enter an email address.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();			
	} else {
		$user = get_user_by('email', $username);
		if(empty($user)) {
			$return =  array( 
				'error' 	=> true,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('User not found.', 'cqpim') . '</div>',
			);
			header('Content-type: application/json');
			echo json_encode($return);
			exit();				
		} else {
			$string = pto_random_string(10);
			$hash = md5($string);
			update_user_meta( $user->ID, 'reset_hash', $hash );
			$reset = get_option('cqpim_reset_page');
			$reset = get_the_permalink($reset);
			$reset = $reset . '?h=' . $hash;
			$to = $user->user_email;
			$telephone = get_option('company_telephone');
			$sender_name = get_option('company_name');
			$sender_email = get_option('company_sales_email');
			$subject = get_option('client_password_reset_subject');
			$content = get_option('client_password_reset_content');
			$content = str_replace('%%CLIENT_NAME%%', $user->display_name, $content);
			$content = str_replace('%%PASSWORD_RESET_LINK%%', $reset, $content);
			$content = str_replace('%%COMPANY_NAME%%', $sender_name, $content);
			$content = str_replace('%%COMPANY_TELEPHONE%%', $telephone, $content);
			$content = str_replace('%%COMPANY_SALES_EMAIL%%', $sender_email, $content);
			$subject = str_replace('%%COMPANY_NAME%%', $sender_name, $subject);
			$attachments = array();
			if(pto_send_emails( $to, $subject, $content, '', $attachments, 'sales' )) {
				$return =  array( 
					'error' 	=> false,
					'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Further instructions have been sent to your registered address.', 'cqpim') . '</div>',
				);
				header('Content-type: application/json');
				echo json_encode($return);
				exit();	
			} else {
				$return =  array( 
					'error' 	=> true,
					'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('The email failed to send. Please try again.', 'cqpim') . '</div>',
				);
				header('Content-type: application/json');
				echo json_encode($return);
				exit();					
			}
		}
	}	
}
add_action( "wp_ajax_nopriv_pto_ajax_reset_conf", 
		"pto_ajax_reset_conf");
add_action( "wp_ajax_pto_ajax_reset_conf", 
		"pto_ajax_reset_conf");
function pto_ajax_reset_conf() {
	$hash = isset($_POST['hash']) ? $_POST['hash'] : '';
	$pass = isset($_POST['pass']) ? $_POST['pass'] : '';
	$pass2 = isset($_POST['pass2']) ? $_POST['pass2'] : '';
	if(empty($pass) || empty($pass2)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('You must fill in both fields.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();			
	} else {
		if($pass != $pass2) {
			$return =  array( 
				'error' 	=> true,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('Passwords do not match.', 'cqpim') . '</div>',
			);
			header('Content-type: application/json');
			echo json_encode($return);
			exit();			
		} else {
			if (strlen($pass) < 8 || !preg_match("#[0-9]+#", $pass) || !preg_match("#[a-zA-Z]+#", $pass)) {
				$return =  array( 
					'error' 	=> true,
					'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('Passwords should be at least 8 characters and should contain at least one letter and one number.', 'cqpim') . '</div>',
				);
				header('Content-type: application/json');
				echo json_encode($return);
				exit();					
			} else {
				$args = array(
					'meta_key' => 'reset_hash',
					'meta_value' => $hash,
					'number' => 1,
				);
				$users = get_users($args);
				if(empty($users[0])) {
					$return =  array( 
						'error' 	=> true,
						'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('Invalid User or reset link', 'cqpim') . '</div>',
					);
					header('Content-type: application/json');
					echo json_encode($return);
					exit();					
				} else {
					$user = $users[0];
					wp_set_password($pass, $user->ID);
					delete_user_meta($user->ID, 'reset_hash');
					$return =  array( 
						'error' 	=> false,
						'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Your password has been reset, you can now log in with your email address and new password.', 'cqpim') . '</div>',
					);
					header('Content-type: application/json');
					echo json_encode($return);
					exit();	
				}
			}
		}
	}	
}
add_action( "wp_ajax_nopriv_pto_ajax_register", 
		"pto_ajax_register");
add_action( "wp_ajax_pto_ajax_register", 
		"pto_ajax_register");
function pto_ajax_register() {
	$username = isset($_POST['username']) ? $_POST['username'] : '';
	$name = isset($_POST['name']) ? $_POST['name'] : '';
	$company = isset($_POST['company']) ? $_POST['company'] : $name;
	$password = isset($_POST['password']) ? $_POST['password'] : '';
	$rpassword = isset($_POST['rpassword']) ? $_POST['rpassword'] : '';
	$company_req = get_option('cqpim_login_reg_company');
	if(empty($username) || !empty($company_req) && empty($company) || empty($name) || empty($password) || empty($rpassword)) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('You must complete all fields.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();		
	}
	if($password != $rpassword) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('The passwords do not match.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();
	}
	
	if ( username_exists( $username ) || email_exists( $username ) ) {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('The email address entered is already in our system, please try again with a different email address or contact us.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();				
	} else {	
		$new_client = array(
			'post_type' => 'cqpim_client',
			'post_status' => 'private',
			'post_content' => '',
			'post_title' => $company,
		);
		$client_pid = wp_insert_post( $new_client, true );
		if( ! is_wp_error( $client_pid ) ){
			$client_updated = array(
				'ID' => $client_pid,
				'post_name' => $client_pid,
			);						
			wp_update_post( $client_updated );
			$client_details = array(
				'client_ref' => $client_pid,
				'client_company' => $company,
				'client_contact' => $name,
				'client_email' => $username,
			);
			update_post_meta($client_pid, 'client_details', $client_details);				
			$require_approval = get_option('pto_dcreg_approve');
			if($require_approval == 1) {
				update_post_meta($client_pid, 'pending', 1);
				$args = array(
					'post_type' => 'cqpim_teams',
					'posts_per_page' => -1,
					'post_status' => 'private'
				);
				$team_members = get_posts($args); 
				foreach($team_members as $member) {
					$team_details = get_post_meta($member->ID, 'team_details', true);
					$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
					$user_obj = get_user_by('id', $user_id);
					if(!empty($user_obj) && user_can($user_obj, 'edit_cqpim_clients')) {
						pto_add_team_notification($member->ID, 'system', $client_pid, 'creg_auth');
					}
				}
				$return =  array( 
					'error' 	=> false,
					'message' 	=> '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('The account has been created, but it must be approved by an admin. You will receive login details via email once the account has been approved.', 'cqpim') . '</div>',
				);
				header('Content-type: application/json');
				echo json_encode($return);	
				exit();
			} else {
				$args = array(
					'post_type' => 'cqpim_teams',
					'posts_per_page' => -1,
					'post_status' => 'private'
				);
				$team_members = get_posts($args); 
				foreach($team_members as $member) {
					$team_details = get_post_meta($member->ID, 'team_details', true);
					$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
					$user_obj = get_user_by('id', $user_id);
					if(!empty($user_obj) && user_can($user_obj, 'edit_cqpim_clients')) {
						pto_add_team_notification($member->ID, 'system', $client_pid, 'creg_noauth');
					}
				}
				$login = $username;
				$user_id = wp_create_user( $login, $password, $username );
				$user = new WP_User( $user_id );
				$user->set_role( 'cqpim_client' );
				$client_details = get_post_meta($client_pid, 'client_details', true);
				$client_details['user_id'] = $user_id;
				update_post_meta($client_pid, 'client_details', $client_details);
				$client_ids = array();
				$client_ids[] = $user_id;				
				update_post_meta($client_pid, 'client_ids', $client_ids);
				$user_data = array(
					'ID' => $user_id,
					'display_name' => $name,
					'first_name' => $name,
				);
				wp_update_user($user_data);	
				$form_auto_welcome = get_option('form_reg_auto_welcome');
				if($form_auto_welcome == 1) {
					send_pto_welcome_email($client_pid, $password);
				}	
			}
			$return =  array( 
				'error' 	=> false,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('Account created, please check your email for your password.', 'cqpim') . '</div>',
			);
			header('Content-type: application/json');
			echo json_encode($return);	
			exit();						
		} else {
			$return =  array( 
				'error' 	=> true,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('Unable to create client entry, please try again or contact us.', 'cqpim') . '</div>',
			);
			header('Content-type: application/json');
			echo json_encode($return);	
			exit();	
		}
	}
}
function pto_client_no_admin_access() {
	$redirect = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : home_url( '/' );
	$user = wp_get_current_user();
	if ( in_array('cqpim_client', $user->roles) && strpos($_SERVER['PHP_SELF'], '/admin-ajax.php') === false && strpos($_SERVER['PHP_SELF'], '/async-upload.php') === false || 
		in_array('ptouploader', $user->roles) && strpos($_SERVER['PHP_SELF'], '/async-upload.php') === false && strpos($_SERVER['PHP_SELF'], '/admin-ajax.php') === false
	)
		exit( wp_redirect( $redirect ) );
}
add_action( 'admin_init', 'pto_client_no_admin_access', 100 );
function pto_hide_admin_bar(){
	$client_login = get_option('cqpim_login_page');
	$client_dash = get_option('cqpim_client_page');
	$client_reset = get_option('cqpim_reset_page');
	$client_reset = get_option('cqpim_register_page');
	$user = wp_get_current_user();
	$roles = $user->roles;
	if(is_page($client_login) || is_page($client_dash) || is_page($client_reset) || in_array('cqpim_client', $roles) || in_array('ptouploader', $roles)) {
		show_admin_bar(false);
	}
}
add_action( 'wp', 'pto_hide_admin_bar', 100 );
function pto_client_login_redirect( $redirect_to, $request, $user ) {
	$client_dash = get_option('cqpim_client_page');
	if(strpos($request,'?redirect=') !== false) {
		$redirect = substr($request, strpos($request, "?redirect"));
		$client_dash_link = get_the_permalink($client_dash) . '' . $redirect;		
	} else {
		$client_dash_link = get_the_permalink($client_dash);
	}
	$user_dash_link = admin_url() . 'admin.php?page=pto-dashboard';
	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		if ( in_array('administrator', $user->roles ) ) {
			return $redirect_to;
		}
		if ( in_array('cqpim_admin', $user->roles ) ) {
			return $user_dash_link;
		}
		foreach($user->roles as $role) {
			if (strpos($role, 'cqpim_') !== false) {
				return $user_dash_link;
			}
		}
		return $redirect_to;				
	} else {
		return $redirect_to;
	}
}
add_filter( 'login_redirect', 'pto_client_login_redirect', 10, 3 );
function pto_restrict_dash() {
	$user = wp_get_current_user();
	$roles = $user->roles;
	if(!is_array($roles)) {
		$roles = array($roles);
	}
	foreach($roles as $role) {
		if (strpos($role, 'cqpim_') !== false) {
			$restrict = true;
		}			
	}
	if(!empty($restrict) && !empty($GLOBALS['menu'])) {
		$plugin_name = get_option('cqpim_plugin_name');
		if(empty($plugin_name)) {
			$plugin_name = 'Projectopia';
		}
		foreach($GLOBALS['menu'] as $key => $item) {
			if($item[0] != $plugin_name) {
				unset($GLOBALS['menu'][$key]);
			}
		}
	}
}
add_action('admin_init', 'pto_restrict_dash');
add_filter('authenticate', 'pto_allow_email_login', 20, 3);
function pto_allow_email_login( $user, $username, $password ) {
	if ( is_email( $username ) ) {
		$user = get_user_by('email',  $username );
		if ( $user ) $username = $user->user_login;
	}
	return wp_authenticate_username_password( null, $username, $password );
}
add_action( 'wp_before_admin_bar_render', 'pto_add_all_node_ids_to_toolbar' );
function pto_add_all_node_ids_to_toolbar() {
	$user = wp_get_current_user();
	$roles = $user->roles;
	if(!is_array($roles)) {
		$roles = array($roles);
	}
	foreach($roles as $role) {
		if (strpos($role, 'cqpim_') !== false) {
			$restrict = true;
		}			
	}
	if(!empty($restrict)) {
		global $wp_admin_bar;
		$all_toolbar_nodes = $wp_admin_bar->get_nodes();
		if ( $all_toolbar_nodes ) {
			foreach ( $all_toolbar_nodes as $node  ) {
				if($node->id != 'menu-toggle') {
					$wp_admin_bar->remove_node($node->id);
				}
			}
		}
	}
}
add_filter( 'bulk_actions-edit-cqpim_project', 'pto_remove_from_bulk_actions' );
function pto_remove_from_bulk_actions( $actions ){
	unset( $actions[ 'edit' ] );
	return $actions;
}