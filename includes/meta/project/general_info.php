<?php
function pto_general_project_info_metabox_callback( $post ) {
 	wp_nonce_field( 
	'general_project_info_metabox', 
	'general_project_info_metabox_nonce' );
	$ticked = get_post_meta($post->ID, 'show_project_info', true);
	?>
	<p><?php _e('Information entered here will be shown on a "General Project Information" page on the client dashboard for this project.', 'cqpim'); ?></p>
	<input type="checkbox" name="show_project_info" value="1" <?php if(!empty($ticked)) { ?>checked="checked"<?php } ?>/> <?php _e('Show this page on the client dashboard.', 'cqpim'); ?>
	<br />
	<?php
	$project_details = get_post_meta($post->ID, 'general_project_notes', true);
	$user = wp_get_current_user();
	$content = isset($project_details['general_project_notes']) ? $project_details['general_project_notes'] : '';
	$editor_id = 'generalprojectnotes';
	$settings  = array(
		'textarea_name' => 'general_project_notes',
		'textarea_rows' => 15,
		'media_buttons' => FALSE,
	);
	wp_editor( $content, $editor_id, $settings );		
}
add_action( 'save_post', 'save_pto_general_project_notes_metabox_data' );
function save_pto_general_project_notes_metabox_data( $post_id ){
	if ( ! isset( $_POST['general_project_info_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['general_project_info_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'general_project_info_metabox' ) )
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
	if(!empty($_POST['general_project_notes'])) {
		$user = wp_get_current_user();
		$project_notes = $_POST['general_project_notes'];
		$project_details = get_post_meta($post_id, 'general_project_notes', true);
		$project_details = $project_details&&is_array($project_details)?$project_details:array();
		$project_details['general_project_notes'] = $project_notes;
		update_post_meta($post_id, 'general_project_notes', $project_details);
	}
	$project_notes = isset($_POST['show_project_info']) ? $_POST['show_project_info'] : '';		
	update_post_meta($post_id, 'show_project_info', $project_notes);
}