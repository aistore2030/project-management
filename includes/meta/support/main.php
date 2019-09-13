<?php
add_action( 'add_meta_boxes', 'add_pto_support_cpt_metaboxes' );
function add_pto_support_cpt_metaboxes(){
	add_meta_box( 
		'support_details', 
		__('Ticket Details', 'cqpim'),
		'pto_support_details_metabox_callback', 
		'cqpim_support',
		'side',
		'high'
	);
	add_meta_box( 
		'support_notes', 
		__('Ticket Notes (Clients cannot see this)', 'cqpim'),
		'pto_support_notes_metabox_callback', 
		'cqpim_support',
		'normal',
		'high'
	);
	global $post;
	$activate_ms = get_post_meta($post->ID, 'activate_ms', true);
	if(!empty($activate_ms)) {
		add_meta_box( 
			'support_elements', 
			__('Milestones & Tasks', 'cqpim'),
			'pto_support_elements_metabox_callback', 
			'cqpim_support',
			'normal',
			'high'
		);
		add_meta_box( 
			'support_invoices', 
			__('Invoices', 'cqpim'),
			'pto_support_invoices_metabox_callback', 
			'cqpim_support',
			'normal',
			'high'
		);
	}
	add_meta_box( 
		'support_history', 
		__('Ticket Updates', 'cqpim'),
		'pto_support_history_metabox_callback', 
		'cqpim_support',
		'normal',
		'low'
	);
	if(current_user_can('publish_cqpim_supports')) {
	add_meta_box( 
		'support_update', 
		__('Update Ticket', 'cqpim'),
		'pto_support_update_metabox_callback', 
		'cqpim_support',
		'normal',
		'low'
	);
	}
	add_meta_box( 
		'support_files', 
		__('Uploaded Files', 'cqpim'),
		'pto_support_files_metabox_callback', 
		'cqpim_support',
		'normal',
		'high'
	);
	if(!current_user_can('publish_cqpim_supports')) {
		remove_meta_box( 'submitdiv', 'cqpim_support', 'side' );
	}
}
require_once('ticket_notes.php');
require_once('milestones.php');
require_once('ticket_invoices.php');
require_once('ticket_files.php');
require_once('ticket_details.php');
require_once('ticket_updates.php');
require_once('update_ticket.php');