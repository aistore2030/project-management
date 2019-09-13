<?php
function pto_project_summary_metabox_callback( $post ) {
	$meta = get_post_meta($post->ID, 'project_details', true);
 	wp_nonce_field( 
	'project_summary_metabox', 
	'project_summary_metabox_nonce' );
	$quote_details = get_post_meta($post->ID, 'project_details', true);
	$quote_summary = isset($quote_details['project_summary']) ? $quote_details['project_summary']: '';
	if($quote_summary) {
		$content = $quote_summary;
	} else {
		$content = '';
	}
	$editor_id = 'projectsummary';
	$settings  = array(
		'textarea_name' => 'project_summary',
		'textarea_rows' => 12,
		'media_buttons' => FALSE,
		'tinymce' => true
	);
	if(current_user_can('cqpim_edit_project_brief')) {
		wp_editor( $content, $editor_id, $settings );
	} else {
		echo wpautop($content);
	}
	echo '<div class="clear"></div>';
}
add_action( 'save_post', 'save_pto_project_summary_metabox_data' );
function save_pto_project_summary_metabox_data( $post_id ){
	if ( ! isset( $_POST['project_summary_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['project_summary_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'project_summary_metabox' ) )
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
	if(isset($_POST['project_summary'])) {
		$quote_details = get_post_meta($post_id, 'project_details', true);
		$quote_details = $quote_details && is_array($quote_details)?$quote_details:array();
		$quote_summary = isset($_POST['project_summary']) ? $_POST['project_summary'] : '';
		$quote_details['project_summary'] = $quote_summary;
		update_post_meta($post_id, 'project_details', $quote_details);
	}
}