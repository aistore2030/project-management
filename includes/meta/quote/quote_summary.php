<?php
function pto_quote_summary_metabox_callback( $post ) {
 	wp_nonce_field( 
	'quote_summary_metabox', 
	'quote_summary_metabox_nonce' );
	$quote_details = get_post_meta($post->ID, 'quote_details', true);
	$quote_summary = isset($quote_details['quote_summary']) ? $quote_details['quote_summary']: '';
	if($quote_summary) {
		$content = $quote_summary;
	} else {
		$content = '';
	}
	$editor_id = 'quotesummary';
	$settings  = array(
		'textarea_name' => 'quote_summary',
		'textarea_rows' => 12,
		'media_buttons' => FALSE,
		'tinymce' => true
	);
	wp_editor( $content, $editor_id, $settings );
	echo '<div class="clear"></div>';
}
add_action( 'save_post', 'save_pto_quote_summary_metabox_data' );
function save_pto_quote_summary_metabox_data( $post_id ){
	if ( ! isset( $_POST['quote_summary_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['quote_summary_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'quote_summary_metabox' ) )
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
	if(isset($_POST['quote_summary'])) {
		$quote_details = get_post_meta($post_id, 'quote_details', true);
		$quote_summary = isset($_POST['quote_summary']) ? $_POST['quote_summary'] : '';
		$quote_details['quote_summary'] = $quote_summary;
		update_post_meta($post_id, 'quote_details', $quote_details);
	}
}