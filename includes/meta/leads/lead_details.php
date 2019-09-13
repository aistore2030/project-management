<?php
function pto_lead_details_metabox_callback( $post ) {
 	wp_nonce_field( 
	'lead_details_metabox', 
	'lead_details_metabox_nonce' );
	$lead_summary = get_post_meta($post->ID, 'lead_summary', true);
	$leadform_id = get_post_meta($post->ID, 'leadform_id', true);
	$leadform_type = get_post_meta($post->ID, 'form_type', true);
	$leadform_obj = get_post($leadform_id);
	if(!empty($leadform_id)) {
		echo '<p style="font-size:16px">' . __('This lead was generated from form:', 'cqpim') . ' <a href="' . get_edit_post_link($leadform_id) . '">' . $leadform_obj->post_title . '</a></p>';
	} else {
		echo '<p style="font-size:16px">' . __('This lead was added manually', 'cqpim') . '</p>';
	}
	if(!empty($leadform_type) && $leadform_type == 'cqpim' || empty($leadform_type) || !empty($leadform_type) && $leadform_type == 'manual') {
		if($lead_summary) {
			$content = $lead_summary;
		} else {
			$content = '';
		}
		$editor_id = 'leadsummary';
		$settings  = array(
			'textarea_name' => 'lead_summary',
			'textarea_rows' => 12,
			'media_buttons' => FALSE,
			'tinymce' => true
		);
		wp_editor( $content, $editor_id, $settings );
		echo '<div class="clear"></div>';
	} else if(!empty($leadform_type) && $leadform_type == 'gf'){
		$gf_submission = get_post_meta($post->ID, 'gf_submission_id', true);
		$url = admin_url() . 'admin.php?page=gf_entries&view=entry&id=2&lid=' . $gf_submission . '&order=ASC&filter&paged=1&pos=0&field_id&operator';
		echo '<p style="font-size:16px">' . __('This lead was generated from a Gravity Form, click here to view the submission:', 'cqpim') . ' ';
		echo '<a href="' . admin_url() . 'admin.php?page=gf_entries&view=entry&id=2&lid=' . $gf_submission . '&order=ASC&filter&paged=1&pos=0&field_id&operator' . '">' . __('View Submission', 'cqpim') . '</a></p>';
	}
			
}
add_action( 'save_post', 'save_pto_lead_details_metabox_data' );
function save_pto_lead_details_metabox_data( $post_id ){
	if ( ! isset( $_POST['lead_details_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['lead_details_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'lead_details_metabox' ) )
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
	if(isset($_POST['lead_summary'])) {
		update_post_meta($post_id, 'lead_summary', $_POST['lead_summary']);
	}
	$leadform_type = get_post_meta($post_id, 'form_type', true);
	if(empty($leadform_type)) {
		update_post_meta($post_id, 'form_type', 'manual');
	}
}