<?php
if ( ! function_exists('pto_clients_cpt') ) {
	function pto_clients_cpt() {
		if(current_user_can('cqpim_create_new_client') && current_user_can('publish_cqpim_clients')) {
			$form_caps = array();
		} else {
			$form_caps = array('create_posts' => false);
		}	
		$labels = array(
			'name'                => _x( 'Clients', 'Post Type General Name', 'cqpim' ),
			'singular_name'       => _x( 'Client', 'Post Type Singular Name', 'cqpim' ),
			'menu_name'           => __( 'Clients', 'cqpim' ),
			'parent_item_colon'   => __( 'Parent Client:', 'cqpim' ),
			'all_items'           => __( 'Clients', 'cqpim' ),
			'view_item'           => __( 'View Client', 'cqpim' ),
			'add_new_item'        => __( 'Add New Client', 'cqpim' ),
			'add_new'             => __( 'New Client', 'cqpim' ),
			'edit_item'           => __( 'Edit Client', 'cqpim' ),
			'update_item'         => __( 'Update Client', 'cqpim' ),
			'search_items'        => __( 'Search Clients', 'cqpim' ),
			'not_found'           => __( 'No clients found', 'cqpim' ),
			'not_found_in_trash'  => __( 'No clients found in trash', 'cqpim' ),
		);
		$args = array(
			'label'               => __( 'client', 'cqpim' ),
			'description'         => __( 'Clients', 'cqpim' ),
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
			'capability_type'     => array('cqpim_client', 'cqpim_clients'),
			'map_meta_cap'        => true,
			'query_var'			  => true,
		);
		register_post_type( 'cqpim_client', $args );
	}
	add_action( 'init', 'pto_clients_cpt', 10 );
}
	add_action( 'init', 'pto_client_cats', 0 );
	function pto_client_cats() {
		$labels = array(
			'name'              => __( 'Client Type', 'cqpim' ),
			'singular_name'     => __( 'Client Type', 'cqpim' ),
			'search_items'      => __( 'Search Client Types', 'cqpim' ),
			'all_items'         => __( 'All Client Types', 'cqpim' ),
			'parent_item'       => __( 'Parent Client Type', 'cqpim' ),
			'parent_item_colon' => __( 'Parent Client Type:', 'cqpim' ),
			'edit_item'         => __( 'Edit Client Type', 'cqpim' ),
			'update_item'       => __( 'Update Client Type', 'cqpim' ),
			'add_new_item'      => __( 'Add New Client Type', 'cqpim' ),
			'new_item_name'     => __( 'New Genre Client Type', 'cqpim' ),
			'menu_name'         => __( 'Client Types', 'cqpim' ),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => false,
		);
		register_taxonomy( 'cqpim_client_cat', array( 'cqpim_client' ), $args );
	}
add_filter( 'map_meta_cap', 'map_pto_client_caps', 10, 4 );
function map_pto_client_caps( $caps, $cap, $user_id, $args ) {
	if ( 'edit_cqpim_client' == $cap || 'delete_cqpim_client' == $cap || 'read_cqpim_client' == $cap ) {
		$post = get_post( $args[0] );
		$post_type = get_post_type_object( $post->post_type );
		$caps = array();
	}
	if ( 'edit_cqpim_client' == $cap ) {
		if ( $user_id == $post->post_author )
			$caps[] = $post_type->cap->edit_posts;
		else
			$caps[] = $post_type->cap->edit_others_posts;
	}
	elseif ( 'delete_cqpim_client' == $cap ) {
		if ( $user_id == $post->post_author )
			$caps[] = $post_type->cap->delete_posts;
		else
			$caps[] = $post_type->cap->delete_others_posts;
	}
	elseif ( 'read_cqpim_client' == $cap ) {
		if ( 'private' != $post->post_status )
			$caps[] = 'read';
		elseif ( $user_id == $post->post_author )
			$caps[] = 'read';
		else
			$caps[] = $post_type->cap->read_private_posts;
	}
	return $caps;
}
if ( ! function_exists( 'pto_client_cpt_custom_columns' )){
	function pto_client_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title' 			=> __('Title', 'cqpim'),
			'avatar'      => '',
			'user_account'      => __('Associated User', 'cqpim'),
			'contact_details'	=> __('Contact Details', 'cqpim')
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_client_posts_columns' , 'pto_client_cpt_custom_columns', 10, 1 );
}
add_action( 'manage_cqpim_client_posts_custom_column', 'content_pto_client_cpt_columns', 10, 2 );
function content_pto_client_cpt_columns( $column, $post_id ) {
	global $post;
	$client_details = get_post_meta( $post_id, 'client_details', true );
	switch( $column ) {
		case 'avatar' :
			$user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
			$avatar = get_option('cqpim_disable_avatars');	
			?>
			<div style="text-align:center;float:left; padding-right:10px;">
				<?php if(empty($avatar)) { ?>
					<div class="cqpim_avatar">
						<?php echo get_avatar( $user_id, 57, '', false, array('force_display' => true) ); ?>
					</div>
				<?php } ?>
			</div>
			<?php	
			break;
		case 'user_account' :
			$user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
			$company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
			$user_object = get_user_by('id', $user_id);
			$user_edit_link = get_edit_user_link( $user_id );
			$pending = get_post_meta($post_id, 'pending', true);
			if(!empty($user_object)) {
				echo '<strong>' . __('User ID:', 'cqpim') . ' </strong><a href="' . $user_edit_link . '">' . $user_object->ID . '</a><br />';
				echo '<strong>' . __('Username:', 'cqpim') . ' </strong>' . $user_object->user_email . '<br />';
				echo '<strong>' . __('Company Name:', 'cqpim') . ' </strong>' . $company_name;
			}
			if(empty($user_id) && empty($pending)) {
				echo '<div class="cqpim-alert cqpim-alert-danger">' . __('This client does not have a user account linked to it and will not work correctly. Update this client to find out why', 'cqpim') . '</div>';
			}
			if(empty($user_id) && !empty($pending)) {
				echo '<div class="cqpim-alert cqpim-alert-info">' . __('This client is pending approval. Update the client to activate the account and send login details.', 'cqpim') . '</div>';
			}
		break;
		case 'contact_details' :
			$company_contact = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
			$company_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
			$company_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
			echo '<strong>' . __('Contact Name:', 'cqpim') . ' </strong>' . $company_contact . "<br />";
			echo '<strong>' . __('Email:', 'cqpim') . ' </strong>' . $company_email . "<br />";
			echo '<strong>' . __('Telephone:', 'cqpim') . ' </strong>' . $company_telephone . "<br />";
		break;
		default :
			break;
	}
} 

add_action('admin_head', 'pto_avatar_column_width');

function pto_avatar_column_width() {
    echo '<style type="text/css">';
	echo '.column-taxonomy-cqpim_client_cat { width:120px !important; overflow:hidden }';
    echo '.column-avatar { text-align: center; width:60px !important; overflow:hidden }';
    echo '</style>';
}