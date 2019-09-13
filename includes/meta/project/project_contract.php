<?php
function pto_project_contract_metabox_callback( $post ) {
 	wp_nonce_field( 
	'project_contract_metabox', 
	'project_contract_metabox_nonce' );
	$contract_status = get_post_meta($post->ID, 'contract_status', true); ?>
	<select name="contract_status">
		<option value="0"><?php _e('Choose...', 'cqpim'); ?></option>
		<option value="1" <?php selected($contract_status, 1, true); ?>><?php _e('Enabled', 'cqpim'); ?></option>
		<option value="2" <?php selected($contract_status, 2, true); ?>><?php _e('Disabled', 'cqpim'); ?></option>
	</select>

<?php }
add_action( 'save_post', 'save_pto_project_contract_metabox_data' );
function save_pto_project_contract_metabox_data( $post_id ){
	if ( ! isset( $_POST['project_contract_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['project_contract_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'project_contract_metabox' ) )
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
	update_post_meta($post_id, 'contract_status', $_POST['contract_status']);
}