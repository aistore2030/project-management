<?php
function pto_lead_notes_metabox_callback( $post ) {
 	wp_nonce_field( 
	'lead_notes_metabox', 
	'lead_notes_metabox_nonce' );
	$lead_summary = get_post_meta($post->ID, 'lead_notes', true);

	if($lead_summary) {
		$content = $lead_summary;
	} else {
		$content = '';
	}
	$editor_id = 'leadnotes';
	$settings  = array(
		'textarea_name' => 'lead_notes',
		'textarea_rows' => 12,
		'media_buttons' => FALSE,
		'tinymce' => true
	);
	wp_editor( $content, $editor_id, $settings );
	echo '<div class="clear"></div>';

			
}
add_action( 'save_post', 'save_pto_lead_notes_metabox_data' );
function save_pto_lead_notes_metabox_data( $post_id ){
	if ( ! isset( $_POST['lead_notes_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['lead_notes_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'lead_notes_metabox' ) )
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
	if(isset($_POST['lead_notes'])) {
		update_post_meta($post_id, 'lead_notes', $_POST['lead_notes']);
	}
}