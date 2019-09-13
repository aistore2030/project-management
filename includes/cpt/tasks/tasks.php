<?php
if ( ! function_exists('pto_tasks_cpt') ) {
	function pto_tasks_cpt() {
		$labels = array(
			'name'                => _x( 'Tasks', 'Post Type General Name', 'cqpim' ),
			'singular_name'       => _x( 'Task', 'Post Type Singular Name', 'cqpim' ),
			'menu_name'           => __( 'Tasks', 'cqpim' ),
			'parent_item_colon'   => __( 'Parent Task', 'cqpim' ),
			'all_items'           => __( 'Tasks', 'cqpim' ),
			'view_item'           => __( 'View Tasks', 'cqpim' ),
			'add_new_item'        => __( 'Add New Task', 'cqpim' ),
			'add_new'             => __( 'New Task', 'cqpim' ),
			'edit_item'           => __( 'Edit Task', 'cqpim' ),
			'update_item'         => __( 'Update Task', 'cqpim' ),
			'search_items'        => __( 'Search Tasks', 'cqpim' ),
			'not_found'           => __( 'No Tasks found', 'cqpim' ),
			'not_found_in_trash'  => __( 'No Tasks found in trash', 'cqpim' ),
		);
		$args = array(
			'label'               => __( 'tasks', 'cqpim' ),
			'description'         => __( 'Tasks', 'cqpim' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,			'show_in_menu'		  => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 32,
			'menu_icon'           => plugins_url() . "/img/contact.png",
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => array('cqpim_task', 'cqpim_tasks'),
			'map_meta_cap'        => true,
			'query_var'			  => true,
			'rewrite'			  => array('slug' => get_option('cqpim_task_slug')),
		);
		register_post_type( 'cqpim_tasks', $args );
	}
	add_action( 'init', 'pto_tasks_cpt', 0 );
}