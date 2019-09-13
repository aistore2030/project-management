<?php get_header(); ?>
<div class="cqpim-login">
	<div class="cqpim_block">
		<div class="cqpim_block_title">
			<div class="caption">
				<?php echo $post->post_title; ?>
			</div>
		</div>
		<br />
		<?php if(empty($_GET['h'])) { ?>
			<form id="cqpim-reset-pass">
				<?php wp_nonce_field('ajax-register-nonce', 'signonsecurity'); ?> 
				<input type="text" id="username" placeholder="<?php _e('Email Address', 'cqpim'); ?>" />
				<input type="submit" class="op cqpim_button right bg-blue font-white rounded_2" value="<?php _e('Reset Password', 'cqpim'); ?>" />
				<div class="clear"></div>
			</form>
			<br />
			<?php $reset = get_option('cqpim_login_page'); 
			if(!empty($reset)) { ?>
				<a style="display:block; text-align:center" href="<?php echo get_the_permalink($reset); ?>" id="forgot" class="op cqpim_button cqpim_button bg-grey-cascade font-white rounded_2"><?php _e('Back to Login', 'cqpim'); ?></a>
			<?php } ?>
		<?php } else { ?>
			<form id="reset_pass_conf">
				<?php wp_nonce_field('ajax-register-nonce', 'signonsecurity'); ?> 
				<input type="hidden" id="hash" value="<?php echo $_GET['h']; ?>" />
				<input type="password" id="password" placeholder="<?php _e('New Password', 'cqpim'); ?>" />
				<input type="password" id="password2" placeholder="<?php _e('Repeat Password', 'cqpim'); ?>" />
				<input type="submit" class="op cqpim_button right bg-blue font-white rounded_2" value="<?php _e('Reset Password', 'cqpim'); ?>" />
				<div class="clear"></div>
			</form>
			<br />
			<?php $reset = get_option('cqpim_login_page'); 
			if(!empty($reset)) { ?>
				<a style="display:block; text-align:center" href="<?php echo get_the_permalink($reset); ?>" id="forgot" class="op cqpim_button cqpim_button bg-grey-cascade font-white rounded_2"><?php _e('Back to Login', 'cqpim'); ?></a>
			<?php } ?>					
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