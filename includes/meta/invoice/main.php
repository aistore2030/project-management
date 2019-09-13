<?php
add_action( 'add_meta_boxes', 'add_pto_invoice_cpt_metaboxes' );
function add_pto_invoice_cpt_metaboxes(){
	add_meta_box( 
		'invoice_payments', 
		__('Payments / Deductions', 'cqpim'),
		'pto_invoice_payments_metabox_callback', 
		'cqpim_invoice',
		'normal'
	);
	add_meta_box( 
		'invoice_client_project', 
		__('Invoice Details', 'cqpim'),
		'pto_invoice_client_project_metabox_callback', 
		'cqpim_invoice',
		'side',
		'high'
	);
	$setting = get_option('allow_invoice_currency_override');
	if($setting == 1) {
		add_meta_box( 
			'invoice_currency', 
			__('Invoice Currency Settings', 'cqpim'), 
			'pto_invoice_currency_metabox_callback', 
			'cqpim_invoice',
			'side',
			'high'
		);		
	}
	add_meta_box( 
		'invoice_line_items', 
		__('Line Items', 'cqpim'),
		'pto_invoice_items_metabox_callback', 
		'cqpim_invoice',
		'normal',
		'high'
	);
	$data = get_option('cqpim_custom_fields_invoice');
	$data = str_replace('\"', '"', $data);
	if(!empty($data)) {
		$form_data = json_decode($data);
		$fields = $form_data;
	}
	if(!empty($fields)) {
		add_meta_box( 
			'invoice_fields', 
			__('Custom Fields', 'cqpim'),
			'pto_invoice_fields_metabox_callback', 
			'cqpim_invoice', 
			'normal'
		);				
	}
	if(!current_user_can('publish_cqpim_invoices')) {
		remove_meta_box( 'submitdiv', 'cqpim_invoice', 'side' );
	}
}
require_once( 'payments.php' );
require_once( 'fields.php' );
require_once( 'invoice_currency.php' );
require_once( 'client_details.php' );
require_once( 'line_items.php' );