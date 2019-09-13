<?php
if ( ! function_exists('pto_support_cpt') ) {
	function pto_support_cpt() {
		if(current_user_can('cqpim_create_new_supports') && current_user_can('publish_cqpim_supports')) {
			$support_caps = array();
		} else {
			$support_caps = array('create_posts' => false);
		}
		$labels = array(
			'name'                => _x( 'Support Tickets', 'Post Type General Name', 'cqpim' ),
			'singular_name'       => _x( 'Support Ticket', 'Post Type Singular Name', 'cqpim' ),
			'menu_name'           => __( 'Support Tickets', 'cqpim' ),
			'parent_item_colon'   => __( 'Parent Support Ticket:', 'cqpim' ),
			'all_items'           => __( 'Support Tickets', 'cqpim' ),
			'view_item'           => __( 'View Support Tickets', 'cqpim' ),
			'add_new_item'        => __( 'Add New Support Ticket', 'cqpim' ),
			'add_new'             => __( 'New Support Ticket', 'cqpim' ),
			'edit_item'           => __( 'Edit Support Ticket', 'cqpim' ),
			'update_item'         => __( 'Update Support Ticket', 'cqpim' ),
			'search_items'        => __( 'Search Support Tickets', 'cqpim' ),
			'not_found'           => __( 'No Support Tickets found', 'cqpim' ),
			'not_found_in_trash'  => __( 'No Support Tickets found in trash', 'cqpim' ),
		);
		$args = array(
			'label'               => __( 'support', 'cqpim' ),
			'description'         => __( 'Support Tickets', 'cqpim' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'capabilities'        => $support_caps,			
			'show_in_menu'		  => 'admin.php?page=support-tickets',
			'show_in_admin_bar'   => false,
			'menu_position'       => 5,
			'menu_icon'           => plugins_url() . "/img/contact.png",
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => array('cqpim_support', 'cqpim_supports'),
			'map_meta_cap'        => true,
			'query_var'			  => true,
			'rewrite'			  => array('slug' => get_option('cqpim_support_slug')),
		);
		register_post_type( 'cqpim_support', $args );
	}
	add_action( 'init', 'pto_support_cpt', 12 );
}