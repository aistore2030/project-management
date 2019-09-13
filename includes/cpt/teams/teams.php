<?php
if ( ! function_exists('pto_teams_cpt') ) {
	function pto_teams_cpt() {
		if(current_user_can('cqpim_create_new_team') && current_user_can('publish_cqpim_teams')) {
			$team_caps = array();
		} else {
			$team_caps = array('create_posts' => false);
		}
		$labels = array(
			'name'                => _x( 'Team Members', 'Post Type General Name', 'cqpim' ),
			'singular_name'       => _x( 'Team Member', 'Post Type Singular Name', 'cqpim' ),
			'menu_name'           => __( 'Team Members', 'cqpim' ),
			'parent_item_colon'   => __( 'Parent Team Member:', 'cqpim' ),
			'all_items'           => __( 'Team Members', 'cqpim' ),
			'view_item'           => __( 'View Team Member', 'cqpim' ),
			'add_new_item'        => __( 'Add New Team Member', 'cqpim' ),
			'add_new'             => __( 'New Team Member', 'cqpim' ),
			'edit_item'           => __( 'Edit Team Member', 'cqpim' ),
			'update_item'         => __( 'Update Team Member', 'cqpim' ),
			'search_items'        => __( 'Search Team Members', 'cqpim' ),
			'not_found'           => __( 'No Team Members found', 'cqpim' ),
			'not_found_in_trash'  => __( 'No Team Members found in trash', 'cqpim' ),
		);
		$args = array(
			'label'               => __( 'team', 'cqpim' ),
			'description'         => __( 'Team Members', 'cqpim' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'capabilities'        => $team_caps,
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,			'show_in_menu'		  => 'cqpim-dashboard',	
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => plugins_url() . "/img/contact.png",
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => array('cqpim_team', 'cqpim_teams'),
			'map_meta_cap'        => true,
			'query_var'			  => true,
		);
		register_post_type( 'cqpim_teams', $args );
	}
	add_action( 'init', 'pto_teams_cpt', 15 );
}
if ( ! function_exists( 'pto_teams_cpt_custom_columns' )){
	function pto_teams_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title' 			=> __('Title', 'cqpim'),
			'user_account'      => __('Associated User', 'cqpim'),
			'contact_details'	=> __('Contact Details', 'cqpim')
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_teams_posts_columns' , 'pto_teams_cpt_custom_columns', 10, 1 );
}
add_action( 'manage_cqpim_teams_posts_custom_column', 'content_pto_teams_cpt_columns', 10, 2 );
function content_pto_teams_cpt_columns( $column, $post_id ) {
	global $post;
	$team_details = get_post_meta( $post_id, 'team_details', true );
	switch( $column ) {
		case 'user_account' :
			$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
			$user_object = get_user_by('id', $user_id);
			$user_edit_link = get_edit_user_link( $user_id );
			$roles = $user_object->roles;
			$role_name = isset($roles[0]) ? $roles[0] : __('No Role', 'cqpim');
			$role_name = str_replace('_', ' ', $role_name);
			$role_name = str_replace('cqpim', 'PTO', $role_name);
			$role_name = ucwords($role_name);
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
			echo '<strong>' . __('User ID:', 'cqpim') . ' </strong>' . $user_object->ID . '<br />';
			echo '<strong>' . __('Login:', 'cqpim') . ' </strong>' . $user_object->user_email . '<br />';
			echo '<strong>' . __('Permission Level:', 'cqpim') . ' </strong>' . $role_name;
			if(empty($user_id)) {
				echo '<div class="cqpim-alert cqpim-alert-danger">' . __('This Team Member does not have a user account linked to it and will not work correctly. Update this Team Member to find out why', 'cqpim') . '</div>';
			}			
		break;
		case 'contact_details' :
			$company_contact = isset($team_details['team_name']) ? $team_details['team_name'] : '';
			$company_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
			$company_telephone = isset($team_details['team_telephone']) ? $team_details['team_telephone'] : '';
			echo '<strong>' . __('Contact Name:', 'cqpim') . ' </strong>' . $company_contact . '<br />';
			echo '<strong>' . __('Email:', 'cqpim') . ' </strong>' . $company_email . '<br />';
			echo '<strong>' . __('Telephone:', 'cqpim') . ' </strong>' . $company_telephone . '<br />';
		break;
		default :
			break;
	}
}
function pto_post_classes($classes) {
	if(is_admin()) {
		global $post;
		if($post->post_type == 'cqpim_project') {
			$user = wp_get_current_user();
			$args = array(
				'post_type' => 'cqpim_teams',
				'posts_per_page' => -1,
				'post_status' => 'private'
			);
			$members = get_posts($args);
			foreach($members as $member) {
				$team_details = get_post_meta($member->ID, 'team_details', true);
				if($team_details['user_id'] == $user->ID) {
					$assigned = $member->ID;
				}
			}
			if(!current_user_can('cqpim_view_all_projects')) {
				$project_contributors = get_post_meta($post->ID, 'project_contributors', true);
				if(empty($project_contributors)) {
					$project_contributors = array();
				}
				foreach($project_contributors as $contributor) {
					if($assigned == $contributor['team_id']) {
						$access = true;
					}
				}
				if(empty($access)) {
					$classes[] = 'no_access';
				} else {
					$classes[] = 'can_access';
				}
			} else {
				$classes[] = 'can_access';
			}
		}
	}
	return $classes;
}
if(is_admin()) {
	add_filter('post_class', 'pto_post_classes'); 
}