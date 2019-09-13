<?php
function pto_project_notes_metabox_callback( $post ) {
 	wp_nonce_field( 
	'project_notes_metabox', 
	'project_notes_metabox_nonce' ); ?>
	<p><?php _e('This is your personal project notes section. Nobody else can see this, including clients and other team members.', 'cqpim'); ?></p>
	<?php
	$project_details = get_post_meta($post->ID, 'project_details', true);
	$user = wp_get_current_user();
	$content = isset($project_details['project_notes'][$user->ID]) ? $project_details['project_notes'][$user->ID] : '';
	$editor_id = 'projectnotes'; 
	$settings  = array(
		'textarea_name' => 'project_notes',
		'textarea_rows' => 15,
		'media_buttons' => FALSE,
	);
	wp_editor( $content, $editor_id, $settings );		
}
add_action( 'save_post', 'save_pto_project_notes_metabox_data' );
function save_pto_project_notes_metabox_data( $post_id ){
	if ( ! isset( $_POST['project_notes_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['project_notes_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'project_notes_metabox' ) )
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
	if(isset($_POST['project_notes'])) {
		$user = wp_get_current_user();
		$project_notes = $_POST['project_notes'];
		$project_details = get_post_meta($post_id, 'project_details', true);
		$project_details['project_notes'][$user->ID] = $project_notes;
		update_post_meta($post_id, 'project_details', $project_details);
	}
}