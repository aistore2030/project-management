<?php
add_action( 'add_meta_boxes', 'add_pto_tasks_cpt_metaboxes' );
function add_pto_tasks_cpt_metaboxes(){
	$screen = get_current_screen();
	if($screen->post_type == 'cqpim_tasks') {
		global $post;
		$user = wp_get_current_user();
		$assigned = pto_get_team_from_userid($user);
		$owner = get_post_meta($post->ID, 'owner', true);
		$task_watchers = get_post_meta($post->ID, 'task_watchers', true);
		if(empty($task_watchers)) {
			$task_watchers = array();
		}
		if(current_user_can('cqpim_dash_view_all_tasks') || $assigned == $owner|| in_array($assigned, $task_watchers) || pto_is_edit_page('new')) {
			add_meta_box( 
				'task_details', 
				__('Task Details', 'cqpim'),
				'pto_task_details_metabox_callback', 
				'cqpim_tasks', 
				'normal'
			);
			add_meta_box( 
				'task_files', 
				__('Task Files', 'cqpim'),
				'pto_task_files_metabox_callback', 
				'cqpim_tasks', 
				'normal'
			);
			add_meta_box( 
				'task_time', 
				__('Time Entries', 'cqpim'),
				'pto_task_time_metabox_callback', 
				'cqpim_tasks', 
				'side',
				'high'
			);
			add_meta_box( 
				'task_messages', 
				__('Task Messages', 'cqpim'),
				'pto_task_messages_metabox_callback', 
				'cqpim_tasks', 
				'normal',
				''
			);
		} else {
			add_meta_box( 
				'task_denied', 
				__('Access Denied', 'cqpim'),
				'pto_task_denied_metabox_callback', 
				'cqpim_tasks',
				'normal'
			);	
			remove_meta_box( 'submitdiv', 'cqpim_tasks', 'side' );	
		}
	}
}
require_once('access_denied.php');
require_once('task_details.php');
require_once('task_files.php');
require_once('task_time.php');
require_once('task_messages.php');