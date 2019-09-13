<?php
if ( ! function_exists('pto_leads_cpt') ) {
	function pto_leads_cpt() {
		if(current_user_can('cqpim_create_new_lead') && current_user_can('publish_cqpim_leads')) {
			$form_caps = array();
		} else {
			$form_caps = array('create_posts' => false);
		}	
		$labels = array(
			'name'                => _x( 'Leads', 'Post Type General Name', 'cqpim' ),
			'singular_name'       => _x( 'Lead', 'Post Type Singular Name', 'cqpim' ),
			'menu_name'           => __( 'Leads', 'cqpim' ),
			'parent_item_colon'   => __( 'Parent Lead:', 'cqpim' ),
			'all_items'           => __( 'Leads', 'cqpim' ),
			'view_item'           => __( 'View Lead', 'cqpim' ),
			'add_new_item'        => __( 'Add New Lead', 'cqpim' ),
			'add_new'             => __( 'New Lead', 'cqpim' ),
			'edit_item'           => __( 'Edit Lead', 'cqpim' ),
			'update_item'         => __( 'Update Lead', 'cqpim' ),
			'search_items'        => __( 'Search Leads', 'cqpim' ),
			'not_found'           => __( 'No leads found', 'cqpim' ),
			'not_found_in_trash'  => __( 'No leads found in trash', 'cqpim' ),
		);
		$args = array(
			'label'               => __( 'lead', 'cqpim' ),
			'description'         => __( 'Leads', 'cqpim' ),
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
			'capability_type'     => array('cqpim_lead', 'cqpim_leads'),
			'map_meta_cap'        => true,
			'query_var'			  => true,
		);
		register_post_type( 'cqpim_lead', $args );
	}
	add_action( 'init', 'pto_leads_cpt', 10 );
}
	add_action( 'init', 'pto_leads_cats', 0 );
	function pto_leads_cats() {
		$labels = array(
			'name'              => __( 'Lead Type', 'cqpim' ),
			'singular_name'     => __( 'Lead Type', 'cqpim' ),
			'search_items'      => __( 'Search Lead Types', 'cqpim' ),
			'all_items'         => __( 'All Lead Types', 'cqpim' ),
			'parent_item'       => __( 'Parent Lead Type', 'cqpim' ),
			'parent_item_colon' => __( 'Parent Lead Type:', 'cqpim' ),
			'edit_item'         => __( 'Edit Lead Type', 'cqpim' ),
			'update_item'       => __( 'Update Lead Type', 'cqpim' ),
			'add_new_item'      => __( 'Add New Lead Type', 'cqpim' ),
			'new_item_name'     => __( 'New Genre Lead Type', 'cqpim' ),
			'menu_name'         => __( 'Lead Types', 'cqpim' ),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => false,
		);
		register_taxonomy( 'cqpim_lead_cat', array( 'cqpim_lead' ), $args );
	}
add_filter( 'map_meta_cap', 'map_pto_client_caps', 10, 4 );


if ( ! function_exists( 'pto_lead_cpt_custom_columns' )){
	function pto_lead_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title' 			=> __('Title', 'cqpim'),
			'form' => __('Lead Form', 'cqpim'),
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_lead_posts_columns' , 'pto_lead_cpt_custom_columns', 10, 1 );
}


add_action( 'manage_cqpim_lead_posts_custom_column', 'content_pto_lead_cpt_columns', 10, 2 );
function content_pto_lead_cpt_columns( $column, $post_id ) {
	global $post;
	switch( $column ) {
		case 'form' :
			$leadform_id = get_post_meta($post->ID, 'leadform_id', true);
			$leadform_obj = get_post($leadform_id);
			if(!empty($leadform_id)) {
				echo '<a href="' . get_edit_post_link($leadform_id) . '">' . $leadform_obj->post_title . '</a>';
			} else {
				_e('Lead Added Manually', 'cqpim');
			}
			break;
		default :
			break;
	}
}