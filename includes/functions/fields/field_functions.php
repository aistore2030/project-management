<?php
add_action( "wp_ajax_pto_save_custom_fields", "pto_save_custom_fields");
function pto_save_custom_fields() {
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	$builder = isset($_POST['builder']) ? $_POST['builder'] : ''; 
	if(empty($type) || empty($builder)) {	
		$return =  array( 
			'error' 	=> true,
		);
		header('Content-type: application/json');
		echo json_encode($return);			
		exit();			
	} else {
		update_option('cqpim_custom_fields_' . $type, $builder);
		$return =  array( 
			'error' 	=> false,
		);
		header('Content-type: application/json');
		echo json_encode($return);			
		exit();			
	}
}