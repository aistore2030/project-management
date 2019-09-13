<?php
function pto_custom_template($page_template) {
	$login_page = get_option('cqpim_login_page');
	$dash_page = get_option('cqpim_client_page');
	$reset_page = get_option('cqpim_reset_page');
	$register_page = get_option('cqpim_register_page');
	$dash_type = get_option('client_dashboard_type');
	if($dash_type == 'inc') {
		if (!empty($login_page) && is_page($login_page)) {
			$page_template = PTO_FE_PATH . '/inc/login.php';
		}
		if (!empty($dash_page) && is_page($dash_page)) {
			$page_template = PTO_FE_PATH . '/inc/client-dashboard.php';
		}
		if (!empty($reset_page) && is_page($reset_page)) {
			$page_template = PTO_FE_PATH . '/inc/reset.php';
		}
		if (!empty($register_page) && is_page($register_page)) {
			$page_template = PTO_FE_PATH . '/inc/register.php';
		}
	} else {
		if (!empty($login_page) && is_page($login_page)) {
			$page_template = PTO_FE_PATH . '/fe/login.php';
		}
		if (!empty($dash_page) && is_page($dash_page)) {
			$page_template = PTO_FE_PATH . '/fe/client-dashboard.php';
		}
		if (!empty($reset_page) && is_page($reset_page)) {
			$page_template = PTO_FE_PATH . '/fe/reset.php';
		}
		if (!empty($register_page) && is_page($register_page)) {
			$page_template = PTO_FE_PATH . '/fe/register.php';
		}		
	}
	return $page_template;
}
add_filter( 'page_template', 'pto_custom_template', 50 ); 
function pto_custom_single_cpt_template($single_template) {
	global $post;
	$type = get_option('client_dashboard_type');	
	if($type == 'inc') {
		if ($post->post_type == 'cqpim_bug') {
			$single_template = PTO_FE_PATH . '/inc/bug.php';
		}
		if ($post->post_type == 'cqpim_support') {
			$single_template = PTO_FE_PATH . '/inc/support.php';
		}
		if ($post->post_type == 'cqpim_tasks') {
			$single_template = PTO_FE_PATH . '/inc/task.php';
		}
		if ($post->post_type == 'cqpim_quote') {
			$single_template = PTO_FE_PATH . '/inc/quote.php';
		}
		if ($post->post_type == 'cqpim_project') {
			$single_template = PTO_FE_PATH . '/inc/project.php';
		}
		if ($post->post_type == 'cqpim_invoice') {
			$single_template = PTO_FE_PATH . '/inc/invoice_redirect.php';
		}
		if ($post->post_type == 'cqpim_subscription') {
			$single_template = PTO_FE_PATH . '/inc/subscription.php';
		}
		if ($post->post_type == 'cqpim_faq') {
			$user = wp_get_current_user();
			if (in_array('cqpim_client', $user->roles)) {
				$single_template = PTO_FE_PATH . '/inc/faq.php';
			}
		}
		if ($post->post_type == 'attachment') {
			$attachment = get_post_meta($post->ID, 'cqpim', true);
			if($attachment == true) {
				$single_template = PTO_FE_PATH . '/inc/file.php';
			}
		}
	} else {
		if ($post->post_type == 'cqpim_bug') {
			$single_template = PTO_FE_PATH . '/fe/bug.php';
		}
		if ($post->post_type == 'cqpim_support') {
			$single_template = PTO_FE_PATH . '/fe/support.php';
		}
		if ($post->post_type == 'cqpim_tasks') {
			$single_template = PTO_FE_PATH . '/fe/task.php';
		}
		if ($post->post_type == 'cqpim_quote') {
			$single_template = PTO_FE_PATH . '/fe/quote.php';
		}
		if ($post->post_type == 'cqpim_project') {
			$single_template = PTO_FE_PATH . '/fe/project.php';
		}
		if ($post->post_type == 'cqpim_invoice') {
			$single_template = PTO_FE_PATH . '/fe/invoice_redirect.php';
		}
		if ($post->post_type == 'cqpim_subscription') {
			$single_template = PTO_FE_PATH . '/fe/subscription.php';
		}
		if ($post->post_type == 'cqpim_faq') {
			$user = wp_get_current_user();
			if (in_array('cqpim_client', $user->roles)) {
				$single_template = PTO_FE_PATH . '/fe/faq.php';
			}
		}
		if ($post->post_type == 'attachment') {
			$attachment = get_post_meta($post->ID, 'cqpim', true);
			if($attachment == true) {
				$single_template = PTO_FE_PATH . '/fe/file.php';
			}
		}		
	}
	return $single_template;
}
add_filter( 'single_template', 'pto_custom_single_cpt_template', 30 );
$args = array(
	'name'          => __( 'Client Dashboard Sidebar', 'cqpim' ),
	'id'            => 'cqpim_client_sidebar',
	'description'   => '',
	'class'         => 'cqpim-sidebar',
	'before_widget' => '<li id="%1$s" class="widget">',
	'after_widget'  => '</li>',
	'before_title'  => '<h2 class="widgettitle">',
	'after_title'   => '</h2>' 
);
register_sidebar( $args );
add_action( "wp_ajax_nopriv_pto_clear_client_alert", "pto_clear_client_alert");
add_action( "wp_ajax_pto_clear_client_alert", "pto_clear_client_alert");
function pto_clear_client_alert() {
	$data = isset($_POST) ? $_POST : '';
	$client = isset($data['client']) ? $data['client'] : '';
	$alert_id = isset($data['alert']) ? $data['alert'] : '0';
	$custom_alerts = get_post_meta($client, 'custom_alerts', true);
	$custom_alerts[$alert_id]['cleared'] = current_time('timestamp');
	update_post_meta($client, 'custom_alerts', $custom_alerts);	
	$return =  array( 
		'error' 	=> false,
		'message' => 1
	);
	header('Content-type: application/json');
	echo json_encode($return);
	exit;
}
add_action( "wp_ajax_nopriv_pto_add_client_files", "pto_add_client_files");
add_action( "wp_ajax_pto_add_client_files", "pto_add_client_files");
function pto_add_client_files() {
	$data = isset($_POST) ? $_POST : '';
	$client = isset($data['client']) ? $data['client'] : '';
	$files = isset($data['files']) ? $data['files'] : '';
	if(empty($files)) {
		$return =  array( 
			'error' 	=> 1
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit;		
	}
	$files = explode(',', $files);
	foreach($files as $attachment) {
		global $wpdb;
		$wpdb->query(
			"
			UPDATE $wpdb->posts 
			SET post_parent = $client
			WHERE ID = $attachment
			AND post_type = 'attachment'
			"
		);
		update_post_meta($attachment, 'cqpim', true);
		$fe_files = get_post_meta($client, 'fe_files', true);
		$fe_files[$attachment] = 1;
		update_post_meta($client, 'fe_files', $fe_files);
	}	
	$return =  array( 
		'error' 	=> false
	);
	header('Content-type: application/json');
	echo json_encode($return);
	exit;
}
add_action( "wp_ajax_nopriv_pto_add_task_files", "pto_add_task_files");
add_action( "wp_ajax_pto_add_task_files", "pto_add_task_files");
function pto_add_task_files() {
	$current_user = wp_get_current_user();
	$data = isset($_POST) ? $_POST : '';
	$task = isset($data['task']) ? $data['task'] : '';
	$files = isset($data['files']) ? $data['files'] : '';
	if(empty($files) || empty($task)) {
		$return =  array( 
			'error' 	=> 1
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit;		
	}
	$files = explode(',', $files);
	$ticket_changes = array();
	foreach($files as $attachment) {
		global $wpdb;
		$wpdb->query(
			"
			UPDATE $wpdb->posts 
			SET post_parent = $task
			WHERE ID = $attachment
			AND post_type = 'attachment'
			"
		);
		update_post_meta($attachment, 'cqpim', true);
		$filename = basename( get_attached_file( $attachment ) );
		$ticket_changes[] = sprintf(__('Uploaded file: %1$s', 'cqpim'), $filename);
	}
	$task_messages = get_post_meta($task, 'task_messages', true);
	$task_messages = $task_messages&&is_array($task_messages)?$task_messages:array();
	$date = current_time('timestamp');
	if(empty($message)) {
		$message = '';
	}
	$task_messages[] = array(
		'date' => $date,
		'message' => '',
		'by' => $current_user->display_name,
		'author' => $current_user->ID,
		'changes' => $ticket_changes,
	);		
	update_post_meta($task, 'task_messages', $task_messages);
	$project_id = get_post_meta($task, 'project_id', true);
	$project_progress = get_post_meta($project_id, 'project_progress', true);
	$task_object = get_post($task);
	if(!empty($files)) {
		foreach($files as $attachment) {
			$post = get_post($attachment);
			$project_progress[] = array(
				'update' => sprintf(__('File "%1$s" uploaded to task: %2$s', 'cqpim'), $post->post_title, $task_object->post_title),
				'date' => current_time('timestamp'),
				'by' => $current_user->display_name
			);
		}
	}
	update_post_meta($project_id, 'project_progress', $project_progress );
	$project_contributors = get_post_meta($project_id, 'project_contributors', true);
	if(empty($project_contributors)) {
		$project_contributors = array();
	}
	foreach($project_contributors as $contrib) {
		if($contrib['pm'] == 1) {
			$emails_to_send[] = $contrib['team_id'];
		}
	}	
	if(empty($task_watchers)) {
		$task_watchers = array();
	} else {
		$task_watchers = $task_watchers;
	}
	foreach($task_watchers as $watcher) {
		$emails_to_send[] = $watcher;
	}
	$emails_to_send = array_unique($emails_to_send);
	foreach($emails_to_send as $email) {
		$team_details = get_post_meta($email, 'team_details', true);
		$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
		if($current_user->user_email != $team_email) {
			pto_add_team_notification($email, $current_user->ID, $task, 'task');
		}
	}
	$return =  array( 
		'error' 	=> false
	);
	header('Content-type: application/json');
	echo json_encode($return);
	exit;
}