<?php
function pto_support_notes_metabox_callback( $post ) {
 	wp_nonce_field( 
 	 	'support_notes_metabox', 
 	 	'support_notes_metabox_nonce' );
		$ticket_notes = get_post_meta($post->ID, 'ticket_notes', true);
		$editor_id = 'ticketnotes';
		$settings  = array(
			'textarea_name' => 'ticket_notes',
			'textarea_rows' => 12,
			'media_buttons' => FALSE,
			'tinymce' => true,
		);
		wp_editor( $ticket_notes, $editor_id, $settings );		
}
add_action( 'save_post', 'save_pto_support_notes_metabox_data' );
function save_pto_support_notes_metabox_data( $post_id ){
	if ( ! isset( $_POST['support_notes_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['support_notes_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'support_notes_metabox' ) )
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
	if(!empty($_POST['ticket_notes'])) {
		update_post_meta($post_id, 'ticket_notes', $_POST['ticket_notes']);
	}
}