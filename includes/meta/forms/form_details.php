<?php
function pto_form_builder_metabox_callback( $post ) {
 	wp_nonce_field( 
	'form_builder_metabox', 
	'form_builder_metabox_nonce' );
	$type = get_post_meta($post->ID, 'form_type', true);
	?>
	<h4><strong><?php _e('Form Type', 'cqpim'); ?>: </strong> <?php $type == 'client_dashboard' ? _e('Client Dashboard Form', 'cqpim') : _e('Anonymous Frontend Form', 'cqpim') ?> (<a href="#" id="edit_form_type"><?php _e('EDIT', 'cqpim'); ?></a>)</h4>
	<?php 
	$type = get_post_meta($post->ID, 'form_type', true);
	if($type == 'anonymous_frontend') { ?>
		<p><?php _e('The Anonymous Frontend form will include default fields as well as those added here. These are Name, Company Name, Address, Postcode, Telephone and Email. This is so that the plugin can create a client from the submission. Fields created here will be inserted into the Project Brief field in the quote.', 'cqpim'); ?></p>		
	<?php } ?>
	<div id="form_basics_container" style="display:none">
		<div id="form_basics">
			<div style="padding:12px">
				<h3><?php _e('Form Settings', 'cqpim'); ?></h3>
				<?php if(empty($type)) { ?>
					<p><?php _e('These initial settings will ensure that your form is created correctly with the required minimum fields.', 'cqpim'); ?></p>
					<p><strong><?php _e('Form Title', 'cqpim'); ?></strong></p>
					<input type="text" name="form_title" id="form_title" />
					<br /><br />
				<?php } ?>
				<p><strong><?php _e('Form Type', 'cqpim'); ?></strong></p>
				<p><?php _e('There are two types of forms, Anonymous Frontend and Client Dashboard.', 'cqpim'); ?> <br /><?php _e('Please refer to the Forms tab in the CQPIM settings for usage.', 'cqpim'); ?></p>
				<?php if(!empty($type)) { ?>
					<p><strong><?php _e('NOTE:', 'cqpim'); ?></strong> <?php _e('Changing the type of form will revert the fields to a default required set.', 'cqpim'); ?></p>
				<?php } ?>
				<?php $type = get_post_meta($post->ID, 'form_type', true); ?>
				<select id="form_type" name="form_type">
					<option value=""><?php _e('Choose an option...', 'cqpim'); ?></option>
					<option value="anonymous_frontend" <?php if($type == 'anonymous_frontend') { echo 'selected'; } ?>><?php _e('Anonymous Frontend Form', 'cqpim'); ?></option>
					<option value="client_dashboard" <?php if($type == 'client_dashboard') { echo 'selected'; } ?>><?php _e('Client Dashboard Form', 'cqpim'); ?></option>
				</select>
				<br /><br />
				<div id="basics-error"></div>
				<a class="cancel-creation mt-20 cqpim_button border-red font-red op" href="<?php echo admin_url(); ?>admin.php?page=pto-dashboard"><?php _e('Cancel', 'cqpim'); ?></a>
				<button class="save-basics mt-20 cqpim_button border-green font-green right op"><?php _e('Save', 'cqpim'); ?></button>
				<div class="clear"></div>
			</div>
		</div>
	</div>			
	<?php		
}
add_action( 'save_post', 'save_pto_form_builder_metabox_data' );
function save_pto_form_builder_metabox_data( $post_id ){
	if ( ! isset( $_POST['form_builder_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['form_builder_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'form_builder_metabox' ) )
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
	$type = get_post_meta($post_id, 'form_type', true);
	if(isset($_POST['form_type'])) {
		if(isset($type) && $type != $_POST['form_type']) {
			update_post_meta($post_id, 'builder_data', '');
			update_post_meta($post_id, 'form_type', $_POST['form_type']);
		} else {
			update_post_meta($post_id, 'form_type', $_POST['form_type']);
		}
	}
	if(isset($_POST['form_title'])) {
		$form_updated = array(
			'ID' => $post_id,
			'post_title' => $_POST['form_title'],
			'post_name' => $post_id,
		);
		if ( ! wp_is_post_revision( $post_id ) ){
			remove_action('save_post', 'save_pto_form_builder_metabox_data');
			wp_update_post( $form_updated );
			add_action('save_post', 'save_pto_form_builder_metabox_data');
		}
	}
}