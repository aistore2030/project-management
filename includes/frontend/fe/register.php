<?php get_header(); ?>	 
<div class="cqpim-login">
	<div class="cqpim_block">
		<div class="cqpim_block_title">
			<div class="caption">
				<?php echo $post->post_title; ?>
			</div>
		</div>
		<br />
		<?php $register_allow = get_option('cqpim_login_reg'); ?>
		<?php if(!empty($register_allow)) { ?>
			<form id="cqpim-register">
				<?php wp_nonce_field('ajax-register-nonce', 'signonsecurity'); ?>
				<input type="text" id="name" placeholder="<?php _e('Name', 'cqpim'); ?>" />
				<input type="text" id="username" placeholder="<?php _e('Email Address', 'cqpim'); ?>" />
				<?php $company = get_option('cqpim_login_reg_company');
				if(!empty($company)) { ?>
					<input type="text" id="company" placeholder="<?php _e('Company Name', 'cqpim'); ?>" />
				<?php } ?>
				<input type="password" id="password" placeholder="<?php _e('Password', 'cqpim'); ?>" />
				<input type="password" id="rpassword" placeholder="<?php _e('Repeat Password', 'cqpim'); ?>" />
				<input type="submit" class="op cqpim_button right bg-blue font-white rounded_2" value="<?php _e('Create Account', 'cqpim'); ?>" />
				<div class="clear"></div>
			</form>
			<br />
			<?php $reset = get_option('cqpim_login_page'); 
			if(!empty($reset)) { ?>
				<a style="display:block; text-align:center" href="<?php echo get_the_permalink($reset); ?>" id="forgot" class="op cqpim_button cqpim_button bg-grey-cascade font-white rounded_2"><?php _e('Back to Login', 'cqpim'); ?></a>
			<?php } ?>
			<br />
			<div class="clear"></div>
			<div id="login_messages" style="display:none"></div>
			<?php include_once(ABSPATH.'wp-admin/includes/plugin.php');
			if(pto_check_addon_status('envato')) {
				$register_page = get_option('cqpim_envato_register_page'); ?>
				<br />
				<p><?php _e('Envato Buyer? Need to Register?', 'cqpim'); ?> <a class="cqpim-link" href="<?php echo get_the_permalink($register_page); ?>"><?php _e('Register Here', 'cqpim'); ?></a></p>
			<?php } ?>
		<?php } else { ?>
			<p><?php _e('Registration is Disabled', 'cqpim'); ?></p>
		<?php } ?>
	</div>
</div>
<?php get_footer(); ?>