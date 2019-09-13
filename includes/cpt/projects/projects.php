<?php
if ( ! function_exists('pto_projects_cpt') ) {
	function pto_projects_cpt() {
		if(current_user_can('cqpim_create_new_project') && current_user_can('publish_cqpim_projects')) {
			$project_caps = array();
		} else {
			$project_caps = array('create_posts' => false);
		}	
		$labels = array(
			'name'                => _x( 'Projects', 'Post Type General Name', 'cqpim' ),
			'singular_name'       => _x( 'Project', 'Post Type Singular Name','cqpim' ),
			'menu_name'           => __( 'Projects', 'cqpim' ),
			'parent_item_colon'   => __( 'Parent Project:', 'cqpim' ),
			'all_items'           => __( 'Projects', 'cqpim' ),
			'view_item'           => __( 'View Project', 'cqpim' ),
			'add_new_item'        => __( 'Add New Project', 'cqpim' ),
			'add_new'             => __( 'New Project', 'cqpim' ),
			'edit_item'           => __( 'Edit Project', 'cqpim' ),
			'update_item'         => __( 'Update Project', 'cqpim' ),
			'search_items'        => __( 'Search Projects', 'cqpim' ),
			'not_found'           => __( 'No projects found', 'cqpim' ),
			'not_found_in_trash'  => __( 'No projects found in trash', 'cqpim' ),
		);
		$args = array(
			'label'               => __( 'project', 'cqpim' ),
			'description'         => __( 'Projects', 'cqpim' ),
			'labels'              => $labels,			'capabilities'        => $project_caps,			'map_meta_cap' => true, 
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
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
			'capability_type'     => array('cqpim_project', 'cqpim_projects'),
			'map_meta_cap'        => true,
			'query_var'			  => true,
			'rewrite'			  => array('slug' => get_option('cqpim_project_slug')),
		);
		register_post_type( 'cqpim_project', $args );
	}
	add_action( 'init', 'pto_projects_cpt', 10 );
}
	add_action( 'init', 'pto_project_cats', 0 );
	function pto_project_cats() {
		$labels = array(
			'name'              => __( 'Project Type', 'cqpim' ),
			'singular_name'     => __( 'Project Type', 'cqpim' ),
			'search_items'      => __( 'Search Project Types', 'cqpim' ),
			'all_items'         => __( 'All Project Types', 'cqpim' ),
			'parent_item'       => __( 'Parent Project Type', 'cqpim' ),
			'parent_item_colon' => __( 'Parent Project Type:', 'cqpim' ),
			'edit_item'         => __( 'Edit Project Type', 'cqpim' ),
			'update_item'       => __( 'Update Project Type', 'cqpim' ),
			'add_new_item'      => __( 'Add New Project Type', 'cqpim' ),
			'new_item_name'     => __( 'New Genre Project Type', 'cqpim' ),
			'menu_name'         => __( 'Project Types', 'cqpim' ),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => false,
		);
		register_taxonomy( 'cqpim_project_cat', array( 'cqpim_project' ), $args );
	}
if ( ! function_exists( 'pto_project_cpt_custom_columns' )){
	function pto_project_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title' 			=> __('Title', 'cqpim'),
			'client_details'      => __('Client Details', 'cqpim'),
			'status'	=> __('Status', 'cqpim')
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_project_posts_columns' , 'pto_project_cpt_custom_columns', 10, 1 );
}
add_action( 'manage_cqpim_project_posts_custom_column', 'content_pto_project_cpt_columns', 10, 2 );
function content_pto_project_cpt_columns( $column, $post_id ) {
	global $post;
	$project_contributors = get_post_meta($post->ID, 'project_contributors', true);
	if(!$project_contributors) {
		$project_contributors = array();
	}
	$user = wp_get_current_user();
	$args = array(
		'post_type' => 'cqpim_teams',
		'posts_per_page' => -1,
		'post_status' => 'private'
	);
	$assigned = 0;
	$members = get_posts($args);
	foreach($members as $member) {
		$team_details = get_post_meta($member->ID, 'team_details', true);
		if($team_details['user_id'] == $user->ID) {
			$assigned = $member->ID;
		}
	}
	foreach($project_contributors as $contributor) {
		if(!empty($contributor['team_id']) && $assigned == $contributor['team_id']) {
			$access = true;
		}
	}
	$project_details = get_post_meta( $post_id, 'project_details', true );
	$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
	$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	$client_contract = get_post_meta($client_id, 'client_contract', true);
	if(!empty($client_contact)) {
		if(!empty($client_details['user_id']) && $client_details['user_id']  == $client_contact) {
			$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
			$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
			$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
			$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
		} else {
			$client_contact_name = isset($client_contacts[$client_contact]['name']) ? $client_contacts[$client_contact]['name'] : '';
			$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
			$client_telephone = isset($client_contacts[$client_contact]['telephone']) ? $client_contacts[$client_contact]['telephone'] : '';
			$client_email = isset($client_contacts[$client_contact]['email']) ? $client_contacts[$client_contact]['email'] : '';		
		}
	} else {
		$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
		$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
		$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
		$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';		
	}
	$project_sent = isset($project_details['sent']) ? $project_details['sent'] : '';
	$project_accepted = isset($project_details['confirmed']) ? $project_details['confirmed'] : '';
	$signoff = isset($project_details['signoff']) ? $project_details['signoff'] : '';
	$closed = isset($project_details['closed']) ? $project_details['closed'] : '';
	$start_date = isset($project_details['start_date']) ? $project_details['start_date'] : '';
	$contract_status = get_post_meta($post->ID, 'contract_status', true);
	switch( $column ) {
		case 'client_details' :
			if(!empty($client_id) && !empty($access) && current_user_can('cqpim_view_project_client_info') || !empty($client_id) && in_array('administrator', $user->roles) || !empty($client_id) && in_array('cqpim_admin', $user->roles)) {
				echo '<strong>' . _e('Company Name:', 'cqpim') . '</strong> ' . $client_company_name . '<br />';
				echo '<strong>' . _e('Contact Name:', 'cqpim') . '</strong> ' . $client_contact_name . '<br />';
				echo '<strong>' . _e('Email:', 'cqpim') . '</strong> ' . $client_email . '<br />';
				echo '<strong>' . _e('Telephone:', 'cqpim') . '</strong> ' . $client_telephone . '<br />';
			} else {
				echo '<p>' . __('Client Details Not Available', 'cqpim') . '</p>';
			}
		break;
		case 'status' :
		$checked = get_option('enable_project_contracts'); 
		if($client_id) {
			if(!$closed) {
				if(!$signoff) {
					if($contract_status == 1) {
						if(!$project_accepted) {
							if(empty($project_sent)) {
								echo '<div class="cqpim-alert cqpim-alert-danger">';
								_e('The contract has not yet been sent to the client.', 'cqpim');
								echo '</div>';
							}
							if($project_sent) {
								$project_sent = $project_details['sent_details'];
								$to = isset($project_sent['to']) ? $project_sent['to'] : '';
								$by = isset($project_sent['by']) ? $project_sent['by'] : '';
								$at = isset($project_sent['date']) ? $project_sent['date'] : '';
								if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
								echo '<div class="cqpim-alert cqpim-alert-warning">';
								printf(__('This contract was sent to %1$s on %2$s by %3$s', 'cqpim'), $to, $at, $by);
								echo '</div>';
							}
						} else {
							$project_accepted = $project_details['confirmed_details'];
							$ip = isset($project_accepted['ip']) ? $project_accepted['ip'] : '';
							$by = isset($project_accepted['by']) ? $project_accepted['by'] : '';
							$at = isset($project_accepted['date']) ? $project_accepted['date'] : '';
							if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
							echo '<div class="cqpim-alert cqpim-alert-success">';
							printf(__('The contract was accepted by %1$s on %2$s from IP Address %3$s', 'cqpim'), $by, $at, $ip);
							echo '</div>';
						}	
					} else {
						echo '<div class="cqpim-alert cqpim-alert-success">';
						_e('Project in Progress', 'cqpim');
						echo '</div>';					
					}
				} else {
					$project_signedoff = $project_details['signoff_details'];
					$by = isset($project_signedoff['by']) ? $project_signedoff['by'] : '';
					$at = isset($project_signedoff['at']) ? $project_signedoff['at'] : '';	
					if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
					echo '<div class="cqpim-alert cqpim-alert-success">';
					printf(__('This project was signed off by %1$s on %2$s', 'cqpim'), $by, $at);
					echo '</div>';
				}
			} else {
				$project_closed = $project_details['closed_details'];
				$by = isset($project_closed['by']) ? $project_closed['by'] : '';
				$at = isset($project_closed['at']) ? $project_closed['at'] : '';
				if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $start_date; }				
				echo '<div class="cqpim-alert cqpim-alert-info">';
				printf(__('This project was closed by %1$s on %2$s', 'cqpim'),$by, $at);
				echo '</div>';			
			}
		} else {
			if(!$closed) {
				echo '<div class="cqpim-alert cqpim-alert-success">';
				_e('This is not a client project, no contract needs to be signed.', 'cqpim');
				'</div>';
			} else {
				$project_closed = $project_details['closed_details'];
				$by = isset($project_closed['by']) ? $project_closed['by'] : '';
				$at = isset($project_closed['at']) ? $project_closed['at'] : '';
				if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $start_date; }
				echo '<div class="cqpim-alert cqpim-alert-info">';
				printf(__('This project was closed by %1$s on %2$s', 'cqpim'), $by, $at);
				echo '</div>';		
			}		
		}
		break;
		default :
			break;
	}
} 