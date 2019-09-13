<?php
function pto_contract_settings_metabox_callback( $post ) {
 	wp_nonce_field( 
 	 	'contract_settings_metabox', 
 	 	'contract_settings_metabox_nonce' ); 
		$client_contract = get_post_meta($post->ID, 'client_contract', true); ?>
		<input type="checkbox" name="disable_contracts" value="1" <?php if(!empty($client_contract)) { echo 'checked="checked"'; } ?> /> <?php _e('Disable project contracts for this client', 'cqpim'); ?>
		<?php
}
add_action( 'save_post', 'save_pto_contract_settings_metabox_data' );
function save_pto_contract_settings_metabox_data( $post_id ){
	if ( ! isset( $_POST['contract_settings_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['contract_settings_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'contract_settings_metabox' ) )
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
	$disable_contracts = isset($_POST['disable_contracts']) ? $_POST['disable_contracts'] : '';
	update_post_meta($post_id, 'client_contract', $disable_contracts);
}