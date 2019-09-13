<?php
add_action( 'add_meta_boxes', 'add_pto_teams_cpt_metaboxes' );
function add_pto_teams_cpt_metaboxes(){
	add_meta_box( 
		'team_details', 
		__('Team Member Details', 'cqpim'),
		'pto_team_details_metabox_callback', 
		'cqpim_teams', 
		'side',
		'high'
	);
	add_meta_box( 
		'team_tasks', 
		__('Open Tasks', 'cqpim'),
		'pto_team_tasks_metabox_callback', 
		'cqpim_teams', 
		'normal'
	);
	add_meta_box( 
		'team_projects', 
		__('Projects', 'cqpim'),
		'pto_team_projects_metabox_callback', 
		'cqpim_teams', 
		'normal'
	);
	add_meta_box( 
		'team_calendar', 
		__('Calendar', 'cqpim'),
		'pto_team_calendar_metabox_callback', 
		'cqpim_teams', 
		'normal',
		'high'
	);
	if(pto_check_addon_status('expenses') && current_user_can('cqpim_view_expenses_admin')) {
		add_meta_box( 
			'team_expenses', 
			__('Expenses', 'cqpim'),
			'pto_team_expenses_metabox_callback', 
			'cqpim_teams', 
			'normal'
		);			
	}
	if(!current_user_can('publish_cqpim_teams')) {
		remove_meta_box( 'submitdiv', 'cqpim_teams', 'side' );
	}
}
require_once('team_details.php');
require_once('team_calendar.php');
require_once('team_tasks.php');
require_once('team_projects.php');
require_once('team_expenses.php');