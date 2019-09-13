<?php
if ( ! function_exists('pto_templates_cpt') ) {
	function pto_templates_cpt() {
		if(current_user_can('cqpim_create_new_templates') && current_user_can('publish_cqpim_templates')) {
			$team_caps = array();
		} else {
			$team_caps = array('create_posts' => false);
		}
		$labels = array(
			'name'                => _x( 'Milestone Templates', 'Post Type General Name', 'cqpim' ),
			'singular_name'       => _x( 'Milestone Template', 'Post Type Singular Name', 'cqpim' ),
			'menu_name'           => __( 'Milestone Templates', 'cqpim' ),
			'parent_item_colon'   => __( 'Parent Milestone Template:', 'cqpim' ),
			'all_items'           => __( 'Milestone Templates', 'cqpim' ),
			'view_item'           => __( 'View Milestone Template', 'cqpim' ),
			'add_new_item'        => __( 'Add New Milestone Template', 'cqpim' ),
			'add_new'             => __( 'New Milestone Template', 'cqpim' ),
			'edit_item'           => __( 'Edit Milestone Template', 'cqpim' ),
			'update_item'         => __( 'Update Milestone Template', 'cqpim' ),
			'search_items'        => __( 'Search Milestone Templates', 'cqpim' ),
			'not_found'           => __( 'No Milestone Templates found', 'cqpim' ),
			'not_found_in_trash'  => __( 'No Milestone Templates found in trash', 'cqpim' ),
		);
		$args = array(
			'label'               => __( 'templates', 'cqpim' ),
			'description'         => __( 'Milestone Templates', 'cqpim' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'capabilities'        => $team_caps,
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
			'capability_type'     => array('cqpim_template', 'cqpim_templates'),
			'map_meta_cap'        => true,
			'query_var'			  => true,
		);
		register_post_type( 'cqpim_templates', $args );
	}
	add_action( 'init', 'pto_templates_cpt', 10 );
}
if ( ! function_exists( 'pto_templates_cpt_custom_columns' )){
	function pto_templates_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title' 			=> __('Title', 'cqpim'),
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_templates_posts_columns' , 'pto_templates_cpt_custom_columns', 10, 1 );
}
function pto_screen_layout_cqpim_templates() {
    return 1;
}
add_filter( 'get_user_option_screen_layout_cqpim_templates', 'pto_screen_layout_cqpim_templates' );