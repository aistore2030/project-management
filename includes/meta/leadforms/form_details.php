<?php
function pto_leadform_builder_metabox_callback( $post ) {
 	wp_nonce_field( 
	'leadform_builder_metabox', 
	'leadform_builder_metabox_nonce' );	
	$form_type = get_post_meta($post->ID, 'form_type', true); 
	$gravity_form = get_post_meta($post->ID, 'gravity_form', true); ?>	
	<h4><?php _e('Form Type', 'cqpim'); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php _e('You can use either the Projectopia Form Builder or link a Gravity Form, if you have Gravity Forms installed.','cqpim'); ?>"></i></h4>
	<select name="builder_type" id="builder_type" required>
		<option value="0"><?php _e('Choose...', 'cqpim'); ?></option>
		<option value="cqpim" <?php selected('cqpim', $form_type); ?>><?php _e('Use Projectopia Form Builder', 'cqpim'); ?></option>
		<?php if(is_plugin_active('gravityforms/gravityforms.php')) { ?>
			<option value="gf" <?php selected('gf', $form_type); ?>><?php _e('Use a Gravity Form', 'cqpim'); ?></option>
		<?php } else { ?>
			<option value="0" disabled><?php _e('Gravity Forms is not available', 'cqpim'); ?></option>
		<?php } ?>
	</select>
	<?php if(is_plugin_active('gravityforms/gravityforms.php')) { 
		$gfapi = new GFAPI(); 
		$forms = $gfapi->get_forms(); ?>
		<div id="gravity_form_cont" <?php if(empty($form_type) || !empty($form_type) && $form_type == 'cqpim') { ?>style="display:none"<?php } ?>>
			<h4><?php _e('Choose a Gravity Form', 'cqpim'); ?></h4>
			<select name="gravity_form" id="gravity_form">
				<option value="0"><?php _e('Choose...', 'cqpim'); ?></option>
				<?php if(!empty($forms)) { ?>
					<?php foreach($forms as $key => $form) { ?>
						<option value="<?php echo $form['id']; ?>" <?php selected($form['id'], $gravity_form); ?>><?php echo $form['title']; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
		</div>
	<?php } ?>
	<?php if(!empty($post->ID)) { ?>
		<h4><?php _e('Form Shortcode', 'cqpim'); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php _e('Use this shortcode to embed this form anywhere in your site.','cqpim'); ?>"></i></h4>
		<div class="lead_form_sc_preview">[projectopia_lead_form id="<?php echo $post->ID; ?>"]</div>
	<?php } ?>
	<button class="save mt-20 cqpim_button bg-blue font-white right op rounded_4"><?php _e('Save Form Details', 'cqpim'); ?></button>
	<div class="clear"></div>
	<?php
}
add_action( 'save_post', 'save_pto_leadform_builder_metabox_data' );
function save_pto_leadform_builder_metabox_data( $post_id ){
	if ( ! isset( $_POST['leadform_builder_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['leadform_builder_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'leadform_builder_metabox' ) )
	    return $post_id;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	    return $post_id;
	if ( 'page' == $_POST['post_type'] ) {
	    if ( ! current_user_can( 'edit_page', $post_id ) )
	        return $post_id;
	  	} else {
	    if ( ! current_user_can( 'edit_post', $post_id ) )
	        return $post_id;
	}
	if(isset($_POST['builder_type'])) {
		update_post_meta($post_id, 'form_type', $_POST['builder_type']);
	}
	if(isset($_POST['gravity_form'])) {
		update_post_meta($post_id, 'gravity_form', $_POST['gravity_form']);
	}
}