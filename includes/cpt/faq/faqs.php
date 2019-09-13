<?php
if ( ! function_exists('pto_faqs_cpt') ) {
	function pto_faqs_cpt() {
		if(current_user_can('cqpim_create_new_faqs') && current_user_can('publish_cqpim_faqs')) {
			$form_caps = array();
		} else {
			$form_caps = array('create_posts' => false);
		}	
		$labels = array(
			'name'                => _x( 'FAQ', 'Post Type General Name', 'cqpim' ),
			'singular_name'       => _x( 'FAQ', 'Post Type Singular Name', 'cqpim' ),
			'menu_name'           => __( 'FAQ', 'cqpim' ),
			'parent_item_colon'   => __( 'Parent FAQ:', 'cqpim' ),
			'all_items'           => __( 'FAQ', 'cqpim' ),
			'view_item'           => __( 'View FAQ', 'cqpim' ),
			'add_new_item'        => __( 'Add New FAQ', 'cqpim' ),
			'add_new'             => __( 'New FAQ', 'cqpim' ),
			'edit_item'           => __( 'Edit FAQ', 'cqpim' ),
			'update_item'         => __( 'Update FAQ', 'cqpim' ),
			'search_items'        => __( 'Search FAQ', 'cqpim' ),
			'not_found'           => __( 'No FAQ found', 'cqpim' ),
			'not_found_in_trash'  => __( 'No FAQ found in trash', 'cqpim' ),
		);
		$args = array(
			'label'               => __( 'faq', 'cqpim' ),
			'description'         => __( 'FAQ', 'cqpim' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'capabilities'        => $form_caps,
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'		  => 'cqpim-dashboard',	
			'show_in_admin_bar'   => true,
			'menu_position'       => 1,
			'menu_icon'           => plugins_url() . "/img/contact.png",
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => array('cqpim_faq', 'cqpim_faqs'),
			'map_meta_cap'        => true,
			'query_var'			  => true,
			'rewrite'			  => array('slug' => get_option('cqpim_faq_slug')),
		);
		register_post_type( 'cqpim_faq', $args );
	}
	add_action( 'init', 'pto_faqs_cpt', 14 );
}
add_action( 'init', 'pto_faq_cats', 0 );
function pto_faq_cats() {
	$labels = array(
		'name'              => __( 'FAQ Category', 'cqpim' ),
		'singular_name'     => __( 'FAQ Category', 'cqpim' ),
		'search_items'      => __( 'Search FAQ Categorys', 'cqpim' ),
		'all_items'         => __( 'All FAQ Categorys', 'cqpim' ),
		'parent_item'       => __( 'Parent FAQ Category', 'cqpim' ),
		'parent_item_colon' => __( 'Parent FAQ Category:', 'cqpim' ),
		'edit_item'         => __( 'Edit FAQ Category', 'cqpim' ),
		'update_item'       => __( 'Update FAQ Category', 'cqpim' ),
		'add_new_item'      => __( 'Add New FAQ Category', 'cqpim' ),
		'new_item_name'     => __( 'New Genre FAQ Category', 'cqpim' ),
		'menu_name'         => __( 'FAQ Categorys', 'cqpim' ),
	);
	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => false,
	);
	register_taxonomy( 'cqpim_faq_cat', array( 'cqpim_faq' ), $args );
}
if ( ! function_exists( 'pto_faq_cpt_custom_columns' )){
	function pto_faq_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title' 			=> __('Title', 'cqpim'),
			'order' 			=> __('Order', 'cqpim'),
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_faq_posts_columns' , 'pto_faq_cpt_custom_columns', 10, 1 );
}
add_action( 'manage_cqpim_faq_posts_custom_column', 'content_pto_faq_cpt_columns', 10, 2 );
function content_pto_faq_cpt_columns( $column, $post_id ) {
	global $post;
	switch( $column ) {
		case 'order' :
			$order = get_post_meta($post->ID, 'faq_order', true);
			$ranges = range(0,500);
			echo '<select class="faq_order" data-id="' . $post->ID . '">';
				foreach($ranges as $range) {
					echo '<option value="' . $range . '" ' . selected($order, $range, false) . '>' . $range . '</option>';
				}
			echo '</select>';
			break;
		default :
			break;
	}
}
function pto_screen_layout_cqpim_faq() {
    return 1;
}
add_filter( 'get_user_option_screen_layout_cqpim_faq', 'pto_screen_layout_cqpim_faq' );