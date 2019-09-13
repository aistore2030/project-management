<?php
if ( ! function_exists('pto_forms_cpt') ) {
	function pto_forms_cpt() {
		if(current_user_can('cqpim_create_new_form') && current_user_can('publish_cqpim_forms')) {
			$form_caps = array();
		} else {
			$form_caps = array('create_posts' => false);
		}
		$labels = array(
			'name'                => _x( 'Quote Forms', 'Post Type General Name', 'cqpim' ),
			'singular_name'       => _x( 'Quote Form', 'Post Type Singular Name', 'cqpim' ),
			'menu_name'           => __( 'Quote Forms', 'cqpim' ),
			'parent_item_colon'   => __( 'Parent Quote Form', 'cqpim' ),
			'all_items'           => __( 'Quote Forms', 'cqpim' ),
			'view_item'           => __( 'View Quote Form', 'cqpim' ),
			'add_new_item'        => __( 'Add New Quote Form', 'cqpim' ),
			'add_new'             => __( 'New Quote Form', 'cqpim' ),
			'edit_item'           => __( 'Edit Quote Form', 'cqpim' ),
			'update_item'         => __( 'Update Quote Form', 'cqpim' ),
			'search_items'        => __( 'Search Quote Forms', 'cqpim' ),
			'not_found'           => __( 'No Quote Forms found', 'cqpim' ),
			'not_found_in_trash'  => __( 'No Quote Forms found in trash', 'cqpim' ),
		);
		$args = array(
			'label'               => __( 'form', 'cqpim' ),
			'description'         => __( 'Quote Forms', 'cqpim' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'capabilities'        => $form_caps,
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,			'show_in_menu'		  => 'cqpim-dashboard',	
			'show_in_admin_bar'   => true,
			'menu_position'       => 1,
			'menu_icon'           => plugins_url() . "/img/contact.png",
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => array('cqpim_form', 'cqpim_forms'),
			'map_meta_cap'        => true,
			'query_var'			  => true,
		);
		register_post_type( 'cqpim_forms', $args );
	}
	add_action( 'init', 'pto_forms_cpt', 10 );
}
if ( ! function_exists( 'pto_forms_cpt_custom_columns' )){
	function pto_forms_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title' 			=> __('Title', 'cqpim')
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_forms_posts_columns' , 'pto_forms_cpt_custom_columns', 10, 1 );
}
function pto_forms_single_column() {
    return 1;
}
add_filter( 'get_user_option_screen_layout_cqpim_forms', 'pto_forms_single_column' );