<?php
add_action( 'add_meta_boxes', 'add_pto_forms_cpt_metaboxes' );
function add_pto_forms_cpt_metaboxes(){
	add_meta_box( 
		'form_builder', 
		__('Form Details', 'cqpim'),
		'pto_form_builder_metabox_callback', 
		'cqpim_forms',
		'normal',
		'high'
	);
	add_meta_box( 
		'form_builder_builder', 
		__('Form Builder', 'cqpim'),
		'pto_form_builder_builder_metabox_callback', 
		'cqpim_forms',
		'normal'
	);
	if(!current_user_can('publish_cqpim_forms')) {
		remove_meta_box( 'submitdiv', 'cqpim_forms', 'side' );
	}
}
require_once( 'form_details.php' );
require_once( 'form_builder.php' );