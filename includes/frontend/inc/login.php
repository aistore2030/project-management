<?php
$user = wp_get_current_user();
if(in_array('cqpim_client', $user->roles)) {
	$login_page = get_option('cqpim_client_page');
	$url = get_the_permalink($login_page);
	wp_redirect($url, 302);
	exit();	
} ?>

<!DOCTYPE html>
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html class="ie ie9" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title><?php wp_title(); ?></title>   
	<?php wp_head(); ?>
	<?php echo '<style>' . get_option('cqpim_dash_css') . '</style>'; ?>
</head>
<?php $bg = get_option('cqpim_dash_bg'); ?>
<?php if(empty($bg)) { ?>
	<body style="background:#3B3F51; height:100vh">
<?php } else { ?>
	<body style="background:url(<?php echo $bg['cqpim_dash_bg']; ?>) center top no-repeat; background-size:cover; height:100vh">
<?php } ?>
<div id="overlay" style="display:none">
	<div id="spinner">
		<img src="<?php echo PTO_PLUGIN_URL . '/img/loading_spinner.gif'; ?>" />
	</div>
</div>
<div id="content" role="main">	
	<br /><br />
	<?php
	$logo = get_option('cqpim_dash_logo'); 
	if($logo) { ?>
		<div style="text-align:center; max-width:400px; margin:0 auto">
			<img style="max-width:100%; margin:20px 0 0" src="<?php echo $logo['cqpim_dash_logo']; ?>" />
		</div>
	<?php } ?>
	<br /><br />
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
</div>
<?php wp_footer(); ?>
</body>
</html>	