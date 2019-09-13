<?php
add_action( 'add_meta_boxes', 'add_pto_terms_cpt_metaboxes' );
function add_pto_terms_cpt_metaboxes(){
	add_meta_box( 
		'terms_template', 
		__('Terms & Conditions', 'cqpim'),
		'pto_terms_metabox_callback', 
		'cqpim_terms', 
		'normal',
		'high'
	);
	if(!current_user_can('publish_cqpim_terms')) {
		remove_meta_box( 'submitdiv', 'cqpim_terms', 'side' );
	}
}
function pto_terms_metabox_callback( $post ) {
 	wp_nonce_field( 
	'terms_metabox', 
	'terms_metabox_nonce' );
	$terms = get_post_meta($post->ID, 'terms', true);
	if(empty($terms)) {
		$terms = '';
	}
	$editor_id = 'terms';  
	$settings  = array(
		'textarea_name' => 'terms',
		'textarea_rows' => 80,
		'media_buttons' => FALSE
	);
	echo '<input type="submit" class="save cqpim_button font-blue border-blue right op" value="' . __('Update Terms Template', 'cqpim') . '"/><div class="clear"></div><br />';
	wp_editor( $terms, $editor_id, $settings );
	echo '<input type="submit" class="save cqpim_button mt-20 font-blue border-blue right op" value="' . __('Update Terms Template', 'cqpim') . '"/><div class="clear"></div>';
}
add_action( 'save_post', 'save_pto_terms_metabox_data' );
function save_pto_terms_metabox_data( $post_id ){
	if ( ! isset( $_POST['terms_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['terms_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'terms_metabox' ) )
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
	if(!empty($_POST['terms'])) {
		update_post_meta($post_id, 'terms', $_POST['terms']);
	}
}