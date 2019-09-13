<?php get_header(); ?>	 
<div class="cqpim-login">
	<div class="cqpim_block">
		<div class="cqpim_block_title">
			<div class="caption">
				<?php echo $post->post_title; ?>
			</div>
		</div>
		<br />
		<form id="cqpim-login">
			<?php wp_nonce_field('ajax-register-nonce', 'signonsecurity'); ?> 
			<input type="text" id="username" placeholder="<?php _e('Email Address', 'cqpim'); ?>" />
			<input type="password" id="password" placeholder="<?php _e('Password', 'cqpim'); ?>" />
			<input type="submit" class="op cqpim_button right bg-blue font-white rounded_2" value="<?php _e('Log In', 'cqpim'); ?>" />
			<div class="clear"></div>
		</form>
		<br />
		<?php $reset = get_option('cqpim_reset_page'); 
		if(!empty($reset)) { ?>
			<a style="display:block; text-align:center" href="<?php echo get_the_permalink($reset); ?>" id="forgot" class="op cqpim_button cqpim_button bg-grey-cascade font-white rounded_2"><?php _e('Forgotten Password?', 'cqpim'); ?></a>
		<?php } ?>
		<?php $register = get_option('cqpim_login_reg');
		$register_page = get_option('cqpim_register_page'); 
		if(!empty($register) && !empty($register_page)) { ?>
			<br />
			<a style="display:block; text-align:center" href="<?php echo get_the_permalink($register_page); ?>" id="register" class="op cqpim_button cqpim_button bg-grey-cascade font-white rounded_2"><?php _e('Create Account', 'cqpim'); ?></a>
		<?php } ?>
		<div class="clear"></div>
		<div id="login_messages" style="display:none"></div>
		<?php include_once(ABSPATH.'wp-admin/includes/plugin.php');
		if(pto_check_addon_status('envato')) {
			$register_page = get_option('cqpim_envato_register_page'); ?>
			<br />
			<p><?php _e('Envato Buyer? Need to Register?', 'cqpim'); ?> <a class="cqpim-link" href="<?php echo get_the_permalink($register_page); ?>"><?php _e('Register Here', 'cqpim'); ?></a></p>
		<?php } ?>
	</div>
</div>
<?php get_footer(); ?>