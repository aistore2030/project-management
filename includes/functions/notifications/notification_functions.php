<?php
function pto_check_unread_team_notifications($team_id) {
	$notifications = get_post_meta($team_id, 'notifications', true);
	$notifications = $notifications && is_array($notifications) ? $notifications : array();
	$unread = array();
	foreach($notifications as $notification) {
		if(empty($notification['read'])) {
			$unread[] = $notification;
		}
	}
	$notifications = count($unread);
	return $notifications;
}
function pto_check_unread_client_notifications($team_id) {
	$notifications = get_post_meta($team_id, 'notifications', true);
	$notifications = $notifications && is_array($notifications) ? $notifications : array();
	$unread = array();
	foreach($notifications as $notification) {
		if(empty($notification['read'])) {
			$unread[] = $notification;
		}
	}
	$notifications = count($unread);
	return $notifications;
}
function pto_get_team_notifications($team_id, $unread = false) {
	$notifications = get_post_meta($team_id, 'notifications', true);
	if(!empty($unread && $unread == true)) {
		$unread = array();
		foreach($notifications as $notification) {
			if(empty($notification['read'])) {
				$unread[] = $notification;
			}
		}
		return $unread;
	} else {
		return $notifications;
	}
}
function pto_get_client_notifications($team_id, $unread = false) {
	$notifications = get_post_meta($team_id, 'notifications', true);
	if(!empty($unread && $unread == true)) {
		$unread = array();
		foreach($notifications as $notification) {
			if(empty($notification['read'])) {
				$unread[] = $notification;
			}
		}
		return $unread;
	} else {
		return $notifications;
	}
}
function pto_add_team_notification($team_id, $from, $item, $type, $ctype = '') {
	$from = get_user_by('id', $from);
	$item_link = get_edit_post_link($item);
	$item_obj = get_post($item);
	// Team Notifications
	if(!empty($type) && $type == 'task') {
		$message = sprintf(__('%1$s has updated a task: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'task_assignee') {
		$message = sprintf(__('%1$s has assigned a task to you: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'new_ticket') {
		$message = sprintf(__('%1$s has raised a new support ticket: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'support_assignee') {
		$message = sprintf(__('%1$s has assigned a support ticket to you: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'support_update') {
		$message = sprintf(__('%1$s has updated a support ticket: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'new_lead') {
		$message = sprintf(__('A new Lead has been submitted: %1$s', 'cqpim'), $item_obj->post_title);
	}
	if(!empty($type) && $type == 'bug_assigned') {
		$message = sprintf(__('%1$s has assigned a bug to you: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'bug_updated') {
		$message = sprintf(__('%1$s has updated a bug: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'project_message') {
		$message = sprintf(__('%1$s has sent a message in project: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'quote_accepted') {
		$message = sprintf(__('%1$s has accepted a quote: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'contract_accepted') {
		$message = sprintf(__('%1$s has confirmed a contract: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'team_project') {
		$message = sprintf(__('%1$s has added you to a project: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'subscription_cancelled') {
		$message = sprintf(__('%1$s has cancelled a subscription: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'subscription_activated') {
		$message = sprintf(__('%1$s has activated a subscription: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'expense_auth') {
		$message = sprintf(__('%1$s has requested an expense authorisation: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'expense_approve') {
		$message = sprintf(__('%1$s has approved an expense request: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'expense_declined') {
		$message = sprintf(__('%1$s has declined an expense request: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'new_quote') {
		$message = sprintf(__('%1$s has requested a new quote: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'creg_auth') {
		$message = sprintf(__('A new client has registered, this client needs approval: %1$s', 'cqpim'), $item_obj->post_title);
	}
	if(!empty($type) && $type == 'creg_noauth') {
		$message = sprintf(__('A new client has registered: %1$s', 'cqpim'), $item_obj->post_title);
	}
	if(!empty($type) && $type == 'subscription_failed') {
		$message = sprintf(__('A subscription has failed to renew: %1$s', 'cqpim'), $item_obj->post_title);
	}
	if(!empty($type) && $type == 'subscription_renewed') {
		$message = sprintf(__('A subscription has renewed successfully: %1$s', 'cqpim'), $item_obj->post_title);
	}
	if(!empty($type) && $type == 'bug_new') {
		$project_id = get_post_meta($item_obj->ID, 'bug_project', true);
		$project_obj = get_post($project_id);
		$message = sprintf(__('%1$s has reported a new bug in project %3$s: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title, $project_obj->post_title);
	}
	// Client Notifications
	if(!empty($type) && $type == 'quote_sent') {
		$message = sprintf(__('%1$s has sent you a new quote: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}	
	if(!empty($type) && $type == 'invoice_sent') {
		$message = sprintf(__('%1$s has sent you a new invoice: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'contract_sent') {
		$message = sprintf(__('%1$s has sent you a new contract: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	if(!empty($type) && $type == 'subscription_sent') {
		$message = sprintf(__('%1$s has sent you a new subscription: %2$s', 'cqpim'), $from->display_name, $item_obj->post_title);
	}
	$notifications = get_post_meta($team_id, 'notifications', true);
	$notifications = $notifications && is_array($notifications) ? $notifications : array();
	$notifications[] = array(
		'time' => current_time('timestamp'),
		'item' => $item_obj->ID,	
		'from' => $from->ID,
		'read' => 0,
		'message' => $message,
		'type' => $ctype,
	);
	update_post_meta($team_id, 'notifications', $notifications);
}

add_action( "wp_ajax_pto_notifications_remove_nf", "pto_notifications_remove_nf");
function pto_notifications_remove_nf() {
	$user = wp_get_current_user();
	$key = isset($_POST['key']) ? $_POST['key'] : '';
	$team = pto_get_team_from_userid($user);
	$notifications = get_post_meta($team, 'notifications', true);
	$notifications = array_reverse($notifications);
	unset($notifications[$key]);
	$notifications = array_reverse($notifications);
	update_post_meta($team, 'notifications', $notifications);
	exit;
}

add_action( "wp_ajax_pto_notifications_client_remove_nf", "pto_notifications_client_remove_nf");
function pto_notifications_client_remove_nf() {
	$user = wp_get_current_user();
	$key = isset($_POST['key']) ? $_POST['key'] : '';
	$team = pto_get_client_from_userid($user);
	$notifications = get_post_meta($team['assigned'], 'notifications', true);
	$notifications = array_reverse($notifications);
	unset($notifications[$key]);
	$notifications = array_reverse($notifications);
	update_post_meta($team['assigned'], 'notifications', $notifications);
	$return =  array( 
		'error' => false,
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();
}

add_action( "wp_ajax_pto_notifications_item", "pto_notifications_item");
function pto_notifications_item() {
	$user = wp_get_current_user();
	$item = isset($_POST['item']) ? $_POST['item'] : '';
	$key = isset($_POST['key']) ? $_POST['key'] : '';
	if(empty($item)) {
		$return =  array( 
			'error' 	=> true,
			'message' => __('The item ID is missing. Unable to redirect.', 'cqpim'),
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();					
	} else {
		$team = pto_get_team_from_userid($user);
		$notifications = get_post_meta($team, 'notifications', true);
		$notifications = array_reverse($notifications);
		$notifications[$key]['read'] = 1;
		$notifications = array_reverse($notifications);
		update_post_meta($team, 'notifications', $notifications);
		$return =  array( 
			'error' 	=> false,
			'redirect' => get_edit_post_link($item),
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();		
	}
	exit;
}
add_action( "wp_ajax_pto_notifications_client_item", "pto_notifications_client_item");
function pto_notifications_client_item() {
	$user = wp_get_current_user();
	$item = isset($_POST['item']) ? $_POST['item'] : '';
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	$key = isset($_POST['key']) ? $_POST['key'] : '';
	if(empty($item)) {
		$return =  array( 
			'error' 	=> true,
			'message' => __('The item ID is missing. Unable to redirect.', 'cqpim'),
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();					
	} else {
		$team = pto_get_client_from_userid($user);
		$team = $team['assigned'];
		$notifications = get_post_meta($team, 'notifications', true);
		$notifications = array_reverse($notifications);
		$notifications[$key]['read'] = 1;
		$notifications = array_reverse($notifications);
		update_post_meta($team, 'notifications', $notifications);
		$link = get_the_permalink($item);
		if(!empty($type) && $type == 'quote') {
			$link = $link . '?page=quote';
		}
		if(!empty($type) && $type == 'contract') {
			$link = $link . '?page=contract';
		}
		$return =  array( 
			'error' 	=> false,
			'redirect' => $link,
		);
		header('Content-type: application/json');
		echo json_encode($return);
		exit();		
	}
	exit;
}
add_action( "wp_ajax_pto_clear_all_read_nf", "pto_clear_all_read_nf");
function pto_clear_all_read_nf() {
	$user = wp_get_current_user();
	$team = pto_get_team_from_userid($user);
	$notifications = get_post_meta($team, 'notifications', true);
	$notifications = array_reverse($notifications);
	foreach($notifications as $key => $notification) {
		if(!empty($notification['read'])) {
			unset($notifications[$key]);
		}
	}
	$notifications = array_reverse($notifications);
	update_post_meta($team, 'notifications', $notifications);
	exit;	
}
add_action( "wp_ajax_pto_clear_all_read_client_nf", "pto_clear_all_read_client_nf");
function pto_clear_all_read_client_nf() {
	$user = wp_get_current_user();
	$team = pto_get_client_from_userid($user);
	$notifications = get_post_meta($team['assigned'], 'notifications', true);
	$notifications = array_reverse($notifications);
	foreach($notifications as $key => $notification) {
		if(!empty($notification['read'])) {
			unset($notifications[$key]);
		}
	}
	$notifications = array_reverse($notifications);
	update_post_meta($team['assigned'], 'notifications', $notifications);
	$return =  array( 
		'error' => false,
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();	
}
add_action( "wp_ajax_pto_mark_all_read_nf", "pto_mark_all_read_nf");
function pto_mark_all_read_nf() {
	$user = wp_get_current_user();
	$team = pto_get_team_from_userid($user);
	$notifications = get_post_meta($team, 'notifications', true);
	$notifications = array_reverse($notifications);
	foreach($notifications as $key => $notification) {
		$notifications[$key]['read'] = 1;
	}
	$notifications = array_reverse($notifications);
	update_post_meta($team, 'notifications', $notifications);
	exit;
}
add_action( "wp_ajax_pto_mark_all_read_client_nf", "pto_mark_all_read_client_nf");
function pto_mark_all_read_client_nf() {
	$user = wp_get_current_user();
	$team = pto_get_client_from_userid($user);
	$notifications = get_post_meta($team['assigned'], 'notifications', true);
	$notifications = array_reverse($notifications);
	foreach($notifications as $key => $notification) {
		$notifications[$key]['read'] = 1;
	}
	$notifications = array_reverse($notifications);
	update_post_meta($team['assigned'], 'notifications', $notifications);
	$return =  array( 
		'error' => false,
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();
}
add_action( "wp_ajax_pto_clear_all_nf", "pto_clear_all_nf");
function pto_clear_all_nf() {
	$user = wp_get_current_user();
	$team = pto_get_team_from_userid($user);
	delete_post_meta($team, 'notifications');
	$return =  array( 
		'html' 	=> '<p style="padding:0 10px">' . __('You do not have any notifications', 'cqpim') . '</p>',
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();		
}
add_action( "wp_ajax_pto_clear_all_client_nf", "pto_clear_all_client_nf");
function pto_clear_all_client_nf() {
	$user = wp_get_current_user();
	$team = pto_get_client_from_userid($user);
	delete_post_meta($team['assigned'], 'notifications');
	$return =  array( 
		'html' 	=> '<p style="padding:0 10px">' . __('You do not have any notifications', 'cqpim') . '</p>',
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();		
}