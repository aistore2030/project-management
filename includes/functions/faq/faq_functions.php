<?php
add_action( "wp_ajax_cqpim_update_faq_order", "cqpim_update_faq_order");
function cqpim_update_faq_order() {
	$data = isset($_POST) ? $_POST : array();
	update_post_meta($data['post'], 'faq_order', $data['order']);	
	$return =  array( 
		'error' 	=> false
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();	
}
add_filter( 'the_content', 'pto_replace_faq_content' );
function pto_replace_faq_content( $content ) {
    if(is_singular('cqpim_faq')) {
		global $post;
		$content = get_post_meta($post->ID, 'terms', true);
    }
    return $content;
}