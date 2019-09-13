<?php
if ( ! function_exists('pto_messages_cpt') ) {
	function pto_messages_cpt() {
		$labels = array(
			'name'                => _x( 'Messages', 'Post Type General Name', 'cqpim' ),
			'singular_name'       => _x( 'Messages', 'Post Type Singular Name', 'cqpim' ),
			'menu_name'           => __( 'Messages', 'cqpim' ),
			'parent_item_colon'   => __( 'Messages:', 'cqpim' ),
			'all_items'           => __( 'Messages', 'cqpim' ),
			'view_item'           => __( 'Messages', 'cqpim' ),
			'add_new_item'        => __( 'Messages', 'cqpim' ),
			'add_new'             => __( 'Messages', 'cqpim' ),
			'edit_item'           => __( 'Messages', 'cqpim' ),
			'update_item'         => __( 'Messages', 'cqpim' ),
			'search_items'        => __( 'Messages', 'cqpim' ),
			'not_found'           => __( 'Messages', 'cqpim' ),
			'not_found_in_trash'  => __( 'Messages', 'cqpim' ),
		);
		$args = array(
			'label'               => __( 'Messages', 'cqpim' ),
			'description'         => __( 'Messages', 'cqpim' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => false,
			'show_in_nav_menus'   => false,			'show_in_menu'		  => false,	
			'show_in_admin_bar'   => false,
			'menu_position'       => 22,
			'menu_icon'           => plugins_url() . "/img/contact.png",
			'can_export'          => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
		);
		register_post_type( 'cqpim_messages', $args );
	}
	add_action( 'init', 'pto_messages_cpt', 11 );
}
if ( ! function_exists('pto_conversations_cpt') ) {
	function pto_conversations_cpt() {
		$labels = array(
			'name'                => _x( 'Conversations', 'Post Type General Name', 'cqpim' ),
			'singular_name'       => _x( 'Conversations', 'Post Type Singular Name', 'cqpim' ),
			'menu_name'           => __( 'Conversations', 'cqpim' ),
			'parent_item_colon'   => __( 'Conversations:', 'cqpim' ),
			'all_items'           => __( 'Conversations', 'cqpim' ),
			'view_item'           => __( 'Conversations', 'cqpim' ),
			'add_new_item'        => __( 'Conversations', 'cqpim' ),
			'add_new'             => __( 'Conversations', 'cqpim' ),
			'edit_item'           => __( 'Conversations', 'cqpim' ),
			'update_item'         => __( 'Conversations', 'cqpim' ),
			'search_items'        => __( 'Conversations', 'cqpim' ),
			'not_found'           => __( 'Conversations', 'cqpim' ),
			'not_found_in_trash'  => __( 'Conversations', 'cqpim' ),
		);
		$args = array(
			'label'               => __( 'Conversations', 'cqpim' ),
			'description'         => __( 'Conversations', 'cqpim' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => false,
			'show_in_nav_menus'   => false,
			'show_in_menu'		  => false,	
			'show_in_admin_bar'   => false,
			'menu_position'       => 5,
			'menu_icon'           => plugins_url() . "/img/contact.png",
			'can_export'          => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
		);
		register_post_type( 'cqpim_conversations', $args );
	}
	add_action( 'init', 'pto_conversations_cpt', 11 );
}