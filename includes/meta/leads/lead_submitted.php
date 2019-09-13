<?php
// Contact Details Metabox
function pto_lead_submitted_metabox_callback( $post ) {
 	wp_nonce_field( 
	'lead_submitted_metabox', 
	'lead_submitted_metabox_nonce' );
	$submitted = get_post_meta($post->ID, 'lead_date', true);
	if(!empty($submitted)) {
		echo '<h3 style="text-align:center">' . date(get_option('cqpim_date_format') . ' H:i', $submitted) . '</h3>';
	} else {
		echo '<h3 style="text-align:center">' . __('Not Published', 'cqpim') . '</h3>';
	}
}
add_action( 'save_post', 'save_pto_lead_submitted_metabox_data' );
function save_pto_lead_submitted_metabox_data( $post_id ){
	if ( ! isset( $_POST['lead_submitted_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['lead_submitted_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'lead_submitted_metabox' ) )
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
	$submitted = get_post_meta($post_id, 'lead_date', true);
	if(empty($submitted)) {
		update_post_meta($post_id, 'lead_date', current_time('timestamp'));
	}
}