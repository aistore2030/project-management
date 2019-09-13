<?php
if ( ! function_exists('pto_invoices_cpt') ) {
	function pto_invoices_cpt() {
		if(current_user_can('cqpim_create_new_invoice') && current_user_can('publish_cqpim_invoices')) {
			$invoice_caps = array();
		} else {
			$invoice_caps = array('create_posts' => false);
		}
		$labels = array(
			'name'                => _x( 'Invoices', 'Post Type General Name', 'cqpim' ),
			'singular_name'       => _x( 'Invoice', 'Post Type Singular Name', 'cqpim' ),
			'menu_name'           => __( 'Invoices', 'cqpim' ),
			'parent_item_colon'   => __( 'Parent Invoice:', 'cqpim' ),
			'all_items'           => __( 'Invoices', 'cqpim' ),
			'view_item'           => __( 'View Invoice', 'cqpim' ),
			'add_new_item'        => __( 'Add New Invoice', 'cqpim' ),
			'add_new'             => __( 'New Invoice', 'cqpim' ),
			'edit_item'           => __( 'Edit Invoice', 'cqpim' ),
			'update_item'         => __( 'Update Invoice', 'cqpim' ),
			'search_items'        => __( 'Search Invoices', 'cqpim' ),
			'not_found'           => __( 'No invoices found', 'cqpim' ),
			'not_found_in_trash'  => __( 'No invoices found in trash', 'cqpim' ),
		);
		$args = array(
			'label'               => __( 'invoice', 'cqpim' ),
			'description'         => __( 'Invoices', 'cqpim' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'capabilities'        => $invoice_caps,
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
			'capability_type'     => array('cqpim_invoice', 'cqpim_invoices'),
			'map_meta_cap'        => true,
			'query_var'			  => true,
			'rewrite'			  => array('slug' => get_option('cqpim_invoice_slug')),
		);
		register_post_type( 'cqpim_invoice', $args );
	}
	add_action( 'init', 'pto_invoices_cpt', 14 );
}
if ( ! function_exists( 'pto_invoice_cpt_custom_columns' )){
	function pto_invoice_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title' 			=> __('Title', 'cqpim'),
			'client_details'      => __('Client Details', 'cqpim'),
			'dates'      => __('Issue / Due Dates', 'cqpim'),
			'amount'      => __('Amount', 'cqpim'),
			'status'	=> __('Status', 'cqpim')
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_invoice_posts_columns' , 'pto_invoice_cpt_custom_columns', 10, 1 );
}
add_action( 'manage_cqpim_invoice_posts_custom_column', 'content_pto_invoice_cpt_columns', 10, 2 );
function content_pto_invoice_cpt_columns( $column, $post_id ) {
	global $post;
	$invoice_details = get_post_meta( $post_id, 'invoice_details', true );
	$invoice_totals = get_post_meta( $post_id, 'invoice_totals', true );
	$client_id = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_edit = get_edit_post_link($client_id);
	$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
	$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
	$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
	$invoice_sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
	$invoice_paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
	$on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';
	if($on_receipt == true) {
		$due_date = __('On Receipt', 'cqpim');
	} else {
		if(is_numeric($due)) {
			$due_date = date(get_option('cqpim_date_format'), $due);
		} else {
			$due_date = __('Due date not set', 'cqpim');
		}
	}
	if(!$invoice_date) {
		$invoice_date = date(get_option('cqpim_date_format'));
	}
	if(is_numeric($invoice_date)) { $invoice_date = date(get_option('cqpim_date_format'), $invoice_date); } else { $invoice_date = $invoice_date; }
	$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
	$now = time();
	switch( $column ) {
		case 'client_details' :
			echo '<a href="' . $client_edit . '" target="_blank">' . $client_company_name . '</a>';
		break;
		case 'dates' :
			echo '<span><strong>' . __('Invoice Date:', 'cqpim') . '</strong> ' . $invoice_date . '</span><br />';
			echo '<span><strong>' . __('Due Date:', 'cqpim') . '</strong> ' . $due_date . '</span><br />';
		break;
		case 'amount' :
			$currency = get_option('currency_symbol');
			$tax_rate = get_post_meta($post_id, 'tax_rate', true);
			if(!empty($invoice_totals['total']) && !empty($invoice_totals['sub'])) {
				if(!empty($tax_rate)) {
					echo pto_calculate_currency($post_id, $invoice_totals['total']);
				} else {
					echo pto_calculate_currency($post_id, $invoice_totals['sub']);
				}
			}
		break;
		case 'status' :
			$on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';
			if($on_receipt != true) {
				if(!$paid) {
					if($due) {
						if($now > $due) {
							echo '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('THIS INVOICE IS OVERDUE', 'cqpim') . '</div>';		
						}
					}
				}
			}
			if(!$invoice_paid) {
				if(empty($invoice_sent)) {
					echo '<div class="cqpim-alert cqpim-alert-danger">' . __('The invoice has not yet been sent to the client.', 'cqpim') . '</div>';
				}
				if($invoice_sent) {
					$invoice_sent = $invoice_details['sent_details'];
					$to = isset($invoice_sent['to']) ? $invoice_sent['to'] : '';
					$by = isset($invoice_sent['by']) ? $invoice_sent['by'] : '';
					$at = isset($invoice_sent['date']) ? $invoice_sent['date'] : '';
					if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
					echo '<div class="cqpim-alert cqpim-alert-warning">';
					printf(__('The invoice was sent to %1$s on %2$s by %3$s', 'cqpim'),$to, $at, $by);
					echo '</div>';					
				}
			} else {
				$invoice_paid = $invoice_details['paid_details'];
				$by = isset($invoice_paid['by']) ? $invoice_paid['by'] : '';
				$at = isset($invoice_paid['date']) ? $invoice_paid['date'] : '';
				if(is_numeric($at)) { $at = date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
				echo '<div class="cqpim-alert cqpim-alert-success">';
				printf(__('This invoice was marked as paid by %1$s on %2$s', 'cqpim'),$by, $at);
				echo '</div>';				
			}
		break;
		default :
			break;
	}
} 