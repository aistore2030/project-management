<?php
$user = wp_get_current_user(); 	
$assignment = pto_get_client_from_userid($user);
$assigned = isset($assignment['assigned']) ? $assignment['assigned'] : '';
$client_type = isset($assignment['type']) ? $assignment['type'] : '';
$client_contract = get_post_meta($assigned, 'client_contract', true);
$tickets = get_option('disable_tickets');
?>
<ul class="cqpim-dash-main-menu nomobile">
	<div class="mobile_items">
		<?php if(empty($avatar)) { echo '<div class="cqpim_avatar">' . get_avatar( $user->ID, 50, '', false, array('force_display' => true) ) . '</div>'; }; ?>
		<span class="cqpim_username rounded_2"><i class="fa fa-user-circle" aria-hidden="true"></i> <?php echo $user->display_name; ?></span>
		<span class="cqpim_role rounded_2"><i class="fa fa-users" aria-hidden="true"></i> <?php echo isset($client_details['client_company']) ? $client_details['client_company'] : ''; ?> <?php if($client_type == 'admin') { echo '&nbsp;' . __('(Main Contact)', 'cqpim'); } ?></span>
		<div class="clear"></div>
		<ul id="cd-head-actions-mobile">				
			<?php if(!empty($messaging)) { ?>
				<li class="desktop_items">
					<span class="cqpim_icon">
						<a <?php if(!empty($unread_qty)) { echo 'class="cqpim_active"'; } ?> href="<?php echo get_the_permalink($client_dash) . '?page=messages'; ?>"><i class="fa fa-envelope-open cqpim_tooltip" aria-hidden="true" title="<?php _e('Messages', 'cqpim'); ?>"></i></a>
						<?php if(!empty($unread_qty)) { ?>
							<span class="cqpim_counter"><?php echo $unread_qty; ?></span>
						<?php } ?>
					</span>
				</li>
			<?php } ?>
			<?php
			$client_settings = get_option('allow_client_settings');
			if($client_settings == 1) { ?>
				<li class="desktop_items">
					<span class="cqpim_icon">
						<a href="<?php echo get_the_permalink($client_dash) . '?page=settings'; ?>"><i class="fa fa-sliders cqpim_tooltip" aria-hidden="true" title="<?php _e('Edit my Profile', 'cqpim'); ?>"></i></a>
					</span>
				</li>
			<?php } ?>
			<li class="desktop_items">
				<span class="cqpim_icon">
					<a href="<?php echo get_the_permalink($client_dash) . '?page=client-files'; ?>"><i class="fa fa-file cqpim_tooltip" aria-hidden="true" title="<?php _e('Client Files', 'cqpim'); ?>"></i></a>
				</span>
			</li>
			<?php $client_settings = get_option('allow_client_users');
			if($client_settings == 1) { ?>
				<li class="contacts desktop_items">
					<span class="cqpim_icon">
						<a href="<?php echo get_the_permalink($client_dash) . '?page=contacts'; ?>"><i class="fa fa-users cqpim_tooltip" aria-hidden="true" title="<?php _e('Contacts', 'cqpim'); ?>"></i></a>
					</span>
				</li>
			<?php } ?>
			<li class="desktop_items">
				<span class="cqpim_icon">
					<?php
						$login_page_id = get_option('cqpim_login_page');
						$login_url = get_option('cqpim_logout_url');
						if(empty($login_url)) {
							$login_url = get_the_permalink($login_page_id);
						}
					?>
					<a href="<?php echo wp_logout_url($login_url); ?>"><i class="fa fa-sign-out cqpim_tooltip" aria-hidden="true" title="<?php _e('Log Out', 'cqpim'); ?>"></i></a>
				</span>
			</li>
		</ul>		
	</div>
	<?php $client_dash = get_option('cqpim_client_page'); ?>
	<li style="padding:10px"><?php _e('MENU', 'cqpim'); ?></li>
	<li class="link<?php if(empty($_GET['page']) && !is_singular('cqpim_tasks') && !is_singular('cqpim_support') && !is_singular('cqpim_bug')) { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($client_dash); ?>"><i class="fa fa-tachometer" aria-hidden="true"></i><?php _e('Dashboard', 'cqpim'); ?></a><span class="selected"></span></li>
	<?php if(get_option('cqpim_messages_allow_client') == 1) { ?>
	<li class="link<?php if(!empty($_GET['page']) && $_GET['page'] == 'messages') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($client_dash) . '?page=messages'; ?>"><i class="fa fa-envelope" aria-hidden="true"></i><?php _e('Messages', 'cqpim'); ?></a><span class="selected"></span></li>
	<?php } ?>
	<?php if(get_option('enable_quotes') == 1) { ?>
	<li class="link<?php if(!empty($_GET['page']) && $_GET['page'] == 'quotes' || is_singular('cqpim_quote')) { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($client_dash) . '?page=quotes'; ?>"><i class="fa fa-file-text" aria-hidden="true"></i><?php _e('Quotes / Estimates', 'cqpim'); ?></a><span class="selected"></span></li>
	<?php } ?>
	<li class="link<?php if(!empty($_GET['page']) && $_GET['page'] == 'projects' || is_singular('cqpim_project') || is_singular('cqpim_tasks') || is_singular('cqpim_bug')) { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($client_dash) . '?page=projects'; ?>"><i class="fa fa-th" aria-hidden="true"></i><?php _e('Projects', 'cqpim'); ?></a><span class="selected"></span></li>
	<?php if(empty($tickets)) { ?>
		<li class="link<?php if(!empty($_GET['page']) && $_GET['page'] == 'support' || is_singular('cqpim_support')) { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($client_dash) . '?page=support'; ?>"><i class="fa fa-life-ring" aria-hidden="true"></i><?php _e('Support Tickets', 'cqpim'); ?></a><span class="selected"></span></li>
	<?php } ?>
	<?php if(get_option('cqpim_enable_faq_dash')) { ?>
		<li class="link<?php if(!empty($_GET['page']) && $_GET['page'] == 'faq' || is_singular('cqpim_faq')) { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($client_dash) . '?page=faq'; ?>"><i class="fa fa-question-circle" aria-hidden="true"></i><?php _e('FAQ', 'cqpim'); ?></a><span class="selected"></span></li>	
	<?php } ?>
	<?php if(get_option('disable_invoices') != 1) { ?>
	<li class="link<?php if(!empty($_GET['page']) && $_GET['page'] == 'invoices' || is_singular('cqpim_invoice')) { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($client_dash) . '?page=invoices'; ?>"><i class="fa fa-credit-card-alt" aria-hidden="true"></i><?php _e('Invoices', 'cqpim'); ?></a><span class="selected"></span></li>
	<?php } ?>
	<?php if(!empty($quote_form) && get_option('enable_quotes') == 1) { ?>
		<li class="link<?php if(!empty($_GET['page']) && $_GET['page'] == 'quote_form') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($client_dash) . '?page=quote_form'; ?>"><i class="fa fa-angle-double-right" aria-hidden="true"></i><?php _e('Request a Quote', 'cqpim'); ?></a><span class="selected"></span></li>
	<?php } ?>		
	<?php
	include_once(ABSPATH.'wp-admin/includes/plugin.php');
	if(pto_check_addon_status('envato')) { ?>
		<li style="padding:10px"><?php _e('ENVATO', 'cqpim'); ?></li>	
		<li class="link<?php if(!empty($_GET['page']) && $_GET['page'] == 'add-envato-purchase') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($client_dash) . '?page=add-envato-purchase'; ?>"><i class="fa fa-sitemap" aria-hidden="true"></i><?php _e('My Items', 'cqpim'); ?></a><span class="selected"></span></li>
	<?php } ?>	
	<?php
	include_once(ABSPATH.'wp-admin/includes/plugin.php');
	if(pto_check_addon_status('subscriptions')) {
		echo pto_return_subs_cd_menu($client_dash);
	} ?>	
	<?php if($post->post_type == 'cqpim_project' || !empty($ppid)) { 
	if(!empty($ppid)) { ?>
		<li style="padding:10px"><?php _e('PROJECT MENU', 'cqpim'); ?></li>
		<?php $project_details = get_post_meta($ppid, 'show_project_info', true);
		if(!empty($project_details)) { ?>
			<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'info') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($ppid) . '?page=summary&sub=info'; ?>"><i class="fa fa-info-circle" aria-hidden="true"></i><?php _e('Project Information', 'cqpim'); ?></a><span class="selected"></span></li>
		<?php } ?>
		<?php $checked = get_post_meta($ppid, 'contract_status', true);
		if(!empty($checked) && $checked == 1) { ?>
			<li class="link<?php if(!empty($_GET['page']) && $_GET['page'] == 'contract') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($ppid) . '?page=contract'; ?>"><i class="fa fa-file-text" aria-hidden="true"></i><?php _e('View Contract', 'cqpim'); ?></a><span class="selected"></span></li>
		<?php } ?>
		<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'updates') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($ppid) . '?page=summary&sub=updates'; ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i><?php _e('Updates & Progress', 'cqpim'); ?></a><span class="selected"></span></li>
		<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'milestones' || is_singular('cqpim_tasks')) { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($ppid) . '?page=summary&sub=milestones'; ?>"><i class="fa fa-tasks" aria-hidden="true"></i><?php _e('Milestones & Tasks', 'cqpim'); ?></a><span class="selected"></span></li>
		<?php 
		include_once(ABSPATH.'wp-admin/includes/plugin.php');
		if(pto_check_addon_status('bugs') && get_post_meta($ppid, 'bugs_activated', true)) { ?>
			<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'bugs' || is_singular('cqpim_bug')) { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($ppid) . '?page=summary&sub=bugs'; ?>"><i class="fa fa-bug" aria-hidden="true"></i><?php _e('Bugs', 'cqpim'); ?></a><span class="selected"></span></li>			
		<?php } ?>
		<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'messages') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($ppid) . '?page=summary&sub=messages'; ?>"><i class="fa fa-envelope" aria-hidden="true"></i><?php _e('Messages', 'cqpim'); ?></a><span class="selected"></span></li>
		<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'files') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($ppid) . '?page=summary&sub=files'; ?>"><i class="fa fa-file" aria-hidden="true"></i><?php _e('Files', 'cqpim'); ?></a><span class="selected"></span></li>
		<?php if(get_option('disable_invoices') != 1) { ?>
		<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'invoices') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($ppid) . '?page=summary&sub=invoices'; ?>"><i class="fa fa-credit-card-alt" aria-hidden="true"></i><?php _e('Costs & Invoices', 'cqpim'); ?></a><span class="selected"></span></li>
		<?php } else { ?>
		<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'invoices') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($ppid) . '?page=summary&sub=invoices'; ?>"><i class="fa fa-credit-card-alt" aria-hidden="true"></i><?php _e('Costs', 'cqpim'); ?></a><span class="selected"></span></li>	
		<?php } ?>
	<?php } else { ?>
		<li style="padding:10px"><?php _e('PROJECT MENU', 'cqpim'); ?></li>
		<?php $project_details = get_post_meta($post->ID, 'show_project_info', true);
		if(!empty($project_details)) { ?>
			<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'info') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($post->ID) . '?page=summary&sub=info'; ?>"><i class="fa fa-info-circle" aria-hidden="true"></i><?php _e('Project Information', 'cqpim'); ?></a><span class="selected"></span></li>
		<?php } ?>
		<?php $checked = get_post_meta($post->ID, 'contract_status', true);
		if(!empty($checked) && $checked == 1) { ?>
			<li class="link<?php if(!empty($_GET['page']) && $_GET['page'] == 'contract') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($post->ID) . '?page=contract'; ?>"><i class="fa fa-file-text" aria-hidden="true"></i><?php _e('View Contract', 'cqpim'); ?></a><span class="selected"></span></li>
		<?php } ?>
		<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'updates') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($post->ID) . '?page=summary&sub=updates'; ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i><?php _e('Updates & Progress', 'cqpim'); ?></a><span class="selected"></span></li>
		<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'milestones') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($post->ID) . '?page=summary&sub=milestones'; ?>"><i class="fa fa-tasks" aria-hidden="true"></i><?php _e('Milestones & Tasks', 'cqpim'); ?></a><span class="selected"></span></li>
		<?php 
		include_once(ABSPATH.'wp-admin/includes/plugin.php');
		if(pto_check_addon_status('bugs') && get_post_meta($post->ID, 'bugs_activated', true)) { ?>
			<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'bugs' || is_singular('cqpim_bug')) { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($post->ID) . '?page=summary&sub=bugs'; ?>"><i class="fa fa-bug" aria-hidden="true"></i><?php _e('Bugs', 'cqpim'); ?></a><span class="selected"></span></li>			
		<?php } ?>			
		<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'messages') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($post->ID) . '?page=summary&sub=messages'; ?>"><i class="fa fa-envelope" aria-hidden="true"></i><?php _e('Messages', 'cqpim'); ?></a><span class="selected"></span></li>
		<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'files') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($post->ID) . '?page=summary&sub=files'; ?>"><i class="fa fa-file" aria-hidden="true"></i><?php _e('Files', 'cqpim'); ?></a><span class="selected"></span></li>
		<?php if(get_option('disable_invoices') != 1) { ?>
		<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'invoices') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($post->ID) . '?page=summary&sub=invoices'; ?>"><i class="fa fa-credit-card-alt" aria-hidden="true"></i><?php _e('Costs & Invoices', 'cqpim'); ?></a><span class="selected"></span></li>
		<?php } else { ?>
		<li class="link<?php if(!empty($_GET['sub']) && $_GET['sub'] == 'invoices') { echo ' active'; } ?>"><a href="<?php echo get_the_permalink($post->ID) . '?page=summary&sub=invoices'; ?>"><i class="fa fa-credit-card-alt" aria-hidden="true"></i><?php _e('Costs', 'cqpim'); ?></a><span class="selected"></span></li>
		<?php } ?>
	<?php } ?>	
	<?php } ?>
	<?php if(is_active_sidebar('cqpim_client_sidebar')) { ?>
			<?php dynamic_sidebar('cqpim_client_sidebar'); ?>
	<?php } ?>
</ul>