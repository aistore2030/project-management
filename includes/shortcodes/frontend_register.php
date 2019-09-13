<?php
// Frontend Quote Form
function pto_frontend_register_form() {
	add_action( 'wp_footer', 'pto_register_form_scripts', 50 );
	__('Address', 'cqpim');
	__('Postcode', 'cqpim');
	__('I am a Human (SPAM check)', 'cqpim');
	$code =  '<div id="cqpim_frontend_form_cont">';
	$code .= '<form id="cqpim_frontend_form_register">';
	$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
	$code .= '<label style="display:block; padding-bottom:5px" for="full_name">' . __('Full Name', 'cqpim') . ' <span style="color:#F00">*</span></label>';
	$code .= '<input style="width:100%" type="text" id="full_name" required />';
	$code .= '</div>';
	$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
	$code .= '<label style="display:block; padding-bottom:5px" for="company_name">' . __('Company Name', 'cqpim') . ' <span style="color:#F00">*</span></label>';
	$code .= '<input style="width:100%" type="text" id="company_name" required />';
	$code .= '</div>';
	$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
	$code .= '<label style="display:block; padding-bottom:5px" for="address">' . __('Address', 'cqpim') . ' <span style="color:#F00">*</span></label>';
	$code .= '<textarea style="width:100%; height:140px" id="address" required></textarea>';
	$code .= '</div>';
	$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
	$code .= '<label style="display:block; padding-bottom:5px" for="postcode">' . __('Postcode', 'cqpim') . ' <span style="color:#F00">*</span></label>';
	$code .= '<input style="width:100%" type="text" id="postcode" required />';
	$code .= '</div>';
	$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
	$code .= '<label style="display:block; padding-bottom:5px" for="telephone">' . __('Telephone', 'cqpim') . ' <span style="color:#F00">*</span></label>';
	$code .= '<input style="width:100%" type="text" id="telephone" required />';
	$code .= '</div>';
	$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
	$code .= '<label style="display:block; padding-bottom:5px" for="email">' . __('Email Address', 'cqpim') . ' <span style="color:#F00">*</span></label>';
	$code .= '<input style="width:100%" type="email" id="email" required />';
	$code .= '</div>';
	$tc = get_option('gdpr_tc_page_check');
	$pp = get_option('gdpr_pp_page_check');
	$tcp = get_option('gdpr_tc_page');
	$ppp = get_option('gdpr_pp_page');
	if(!empty($tc)) {
		$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
		$link = '<a href="' . get_the_permalink($tcp) . '" target="_blank">' . __('I have read and accept the Terms & Conditions', 'cqpim') . '</a> <span style="color:#F00">*</span>';
		$code .= '<input type="checkbox" id="tc_conf" name="tc_conf" required /> ' . $link;
		$code .= '</div>';
	}
	if(!empty($pp)) {
		$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
		$link = '<a href="' . get_the_permalink($ppp) . '" target="_blank">' . __('I have read and accept the Privacy Policy', 'cqpim') . '</a> <span style="color:#F00">*</span>';
		$code .= '<input type="checkbox" id="tc_conf" name="tc_conf" required /> ' . $link;
		$code .= '</div>';
	}
	$code .= '<input type="submit" id="cqpim_submit_frontend_register" value="' . __('Register', 'cqpim') . '" /><br /><div id="form_spinner" style="clear:both; display:none; background:url(' . PTO_PATH . '/img/ajax-loader.gif) center center no-repeat; width:16px; height:16px; padding:10px 0 0 5px; margin-top:15px"></div>';
	$code .= '<div style="margin-top:20px" id="cqpim_submit_frontend_messages"></div>';
	$code .= '</form>';
	$code .= '</div>';
	return $code;
}
add_shortcode('cqpim_registration_form', 'pto_frontend_register_form');
function pto_register_form_scripts() {
	?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#cqpim_submit_frontend_register').before('<div style="padding-bottom:12px" class="cqpim_form_item"> <?php _e('I am a Human (SPAM check)', 'cqpim'); ?><span style="color:#F00">*</span> <input type="checkbox" id="human_conf" required /></div>');
			jQuery('#cqpim_frontend_form_register').on('submit', function(e) {
				e.preventDefault();
				var spinner = jQuery('#form_spinner');
				var name = jQuery('#full_name').val();
				var company = jQuery('#company_name').val();
				var address = jQuery('#address').val();
				var postcode = jQuery('#postcode').val();
				var telephone = jQuery('#telephone').val();
				var email = jQuery('#email').val();
				var data = {
					'action' : 'pto_frontend_register_submission',
					'name' : name,
					'company' : company,
					'address' : address,
					'postcode' : postcode,
					'telephone' : telephone,
					'email' : email,
				};
				jQuery.ajax({
					url: '<?php echo admin_url() . 'admin-ajax.php'; ?>',
					data: data,
					type: 'POST',
					dataType: 'json',
					beforeSend: function(){
						// show spinner
						spinner.show();
						// disable form elements while awaiting data
						jQuery('#cqpim_submit_frontend').prop('disabled', true);
					},
				}).always(function(response) {
					console.log(response);
				}).done(function(response){
					if(response.error == true) {
						spinner.hide();
						// re-enable form elements so that new enquiry can be posted
						jQuery('#cqpim_submit_frontend').prop('disabled', false);
						jQuery('#cqpim_submit_frontend_messages').html(response.message);
					} else {
						spinner.hide();
						// re-enable form elements so that new enquiry can be posted
						jQuery('#cqpim_submit_frontend').prop('disabled', false);
						jQuery('#cqpim_submit_frontend_messages').html(response.message);
					}
				});
			});
		});
	</script>	
	<?php
}