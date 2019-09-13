<?php
function pto_get_invoice_id() {
	$args = array(
		'post_type' => 'cqpim_invoice',
		'posts_per_page' => 1,
		'orderby' => 'date',
		'order' => 'desc',
		'post_status' => 'publish'
	);
	$invoices = get_posts($args);
	if(!empty($invoices)) {
		foreach($invoices as $invoice) {
			$invoice_id = get_post_meta($invoice->ID, 'invoice_id', true);
		}
		$invoice_id++;
	} else {
		$invoice_id = 1;
	}
	return $invoice_id;
}
add_action( "wp_ajax_pto_edit_income_graph", "pto_edit_income_graph");
function pto_edit_income_graph() {
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	if($type == 'type') {
		$_SESSION['invoice_payments'] = isset($_POST['date']) ? $_POST['date'] : 'invoice';
	} else {
		$_SESSION['invoice_year'] = isset($_POST['date']) ? $_POST['date'] : date('Y');
	}
	$return =  array( 
		'error' 	=> false,
		'errors' 	=> ''
	);
	header('Content-type: application/json');
	echo json_encode($return);		
	exit;
}
add_action('pto_check_recurring_invoices', 'pto_check_recurring_invoices_hourly');
function pto_check_recurring_invoices_hourly() {
	if(pto_check_addon_status('subscriptions')) {
		$subs_check = pto_check_subscriptions();
	}
	$args = array(
		'post_type' => 'cqpim_client',
		'posts_per_page' => -1,
		'post_status' => 'private'
	);
	$now = time();
	$clients = get_posts($args);
	foreach($clients as $client) {
		$recurring_invoices = get_post_meta($client->ID, 'recurring_invoices', true);
		$client_details = get_post_meta($client->ID, 'client_details', true);
		$invoice_terms = isset($client_details['invoice_terms']) ? $client_details['invoice_terms']: '';
		$system_invoice_terms = get_option('company_invoice_terms');
		if(!empty($invoice_terms)) {
			$terms = $invoice_terms;
		} else {
			$terms = $system_invoice_terms;
		}
		if(empty($recurring_invoices)) {
			$recurring_invoices = array();
		}
		foreach($recurring_invoices as $key => $invoice) {
			if(empty($invoice['end'])) {
				$end = strtotime('+365 days', $now);
			} else {
				$end = $invoice['end'];
				if(!is_numeric($end)) {
					$end = str_replace('/','-',$end);
					$end = str_replace('.','-',$end);
					$end = strtotime($end);
				} else {
						$end = $end;
				}
			}
			if($invoice['next_run'] < $now && $invoice['status'] == 1 && $end > strtotime('-2 days', $now)) {
				$invoice_id = pto_get_invoice_id();
				$new_invoice = array(
					'post_type' => 'cqpim_invoice',
					'post_status' => 'publish',
					'post_content' => '',
					'post_title' =>  $invoice_id,
					'post_password' => pto_random_string(10),
				);
				$invoice_pid = wp_insert_post( $new_invoice, true );
				if( ! is_wp_error( $invoice_pid ) ){
					$date = time();
					$terms_over = strtotime('+' . $terms . ' days', $date);
					if($terms == 1) {
						$invoice_details = array(
							'client_id' => $client->ID,
							'due' => __('Due on Receipt', 'cqpim'),
							'on_receipt' => true,
							'terms_over' => $terms_over,
							'invoice_date' => current_time('timestamp'),
							'allow_partial' => $invoice['partial'],
						);														
					} else {
						$invoice_details = array(
							'client_id' => $client->ID,
							'due' => $terms_over,
							'terms_over' => $terms_over,
							'invoice_date' => current_time('timestamp'),
							'allow_partial' => $invoice['partial'],
						);
					}
					update_post_meta($invoice_pid, 'invoice_id', $invoice_id);
					if(isset($invoice['contact'])) {
						update_post_meta($invoice_pid, 'client_contact', $invoice['contact']);
					}
					update_post_meta($invoice_pid, 'invoice_client', $client->ID);
					$currency = get_option('currency_symbol');
					$currency_code = get_option('currency_code');
					$currency_position = get_option('currency_symbol_position');
					$currency_space = get_option('currency_symbol_space'); 
					$client_currency = get_post_meta($client->ID, 'currency_symbol', true);
					$client_currency_code = get_post_meta($client->ID, 'currency_code', true);
					$client_currency_space = get_post_meta($client->ID, 'currency_space', true);		
					$client_currency_position = get_post_meta($client->ID, 'currency_position', true);
					if(!empty($client_currency)) {
						update_post_meta($invoice_pid, 'currency_symbol', $client_currency);
					} else {
						update_post_meta($invoice_pid, 'currency_symbol', $currency);
					}
					if(!empty($client_currency_code)) {
						update_post_meta($invoice_pid, 'currency_code', $client_currency_code);
					} else {
						update_post_meta($invoice_pid, 'currency_code', $currency_code);
					}
					if(!empty($client_currency_space)) {
						update_post_meta($invoice_pid, 'currency_space', $client_currency_space);
					} else {
						update_post_meta($invoice_pid, 'currency_space', $currency_space);
					}
					if(!empty($client_currency_position)) {
						update_post_meta($invoice_pid, 'currency_position', $client_currency_position);
					} else {
						update_post_meta($invoice_pid, 'currency_position', $currency_position);
					}
					update_post_meta($invoice_pid, 'invoice_details', $invoice_details);
					$line_items = array();
					$items = $invoice['items'];
					$subtotal = 0;
					foreach($items as $item) {
						$line_items[] = array(
							'qty' => $item['qty'],
							'desc' => $item['desc'],
							'price' => $item['price'],
							'sub' => $item['price'] * $item['qty'],
						);
						$subtotal = $item['price'] * $item['qty'] + $subtotal;
					}
					update_post_meta($invoice_pid, 'line_items', $line_items);
					$tax_app = get_post_meta($invoice_pid, 'tax_set', true);
					$system_tax = get_option('sales_tax_rate');
					$system_stax = get_option('secondary_sales_tax_rate');
					if(empty($tax_app)) {
						$client_details = get_post_meta($client->ID, 'client_details', true);
						$client_tax = isset($client_details['tax_disabled']) ? $client_details['tax_disabled'] : '';
						$client_stax = isset($client_details['stax_disabled']) ? $client_details['stax_disabled'] : '';
						if(!empty($system_tax) && empty($client_tax)) {
							update_post_meta($invoice_pid, 'tax_applicable', 1);
							update_post_meta($invoice_pid, 'tax_set', 1);	
							update_post_meta($invoice_pid, 'tax_rate', $system_tax);	
							if(!empty($system_stax) && empty($client_stax)) {
								update_post_meta($invoice_pid, 'stax_applicable', 1);
								update_post_meta($invoice_pid, 'stax_set', 1);	
								update_post_meta($invoice_pid, 'stax_rate', $system_stax);			
							} else {
								update_post_meta($invoice_pid, 'stax_applicable', 0);
								update_post_meta($invoice_pid, 'stax_set', 1);
								update_post_meta($invoice_pid, 'stax_rate', 0);				
							}
						} else {
							update_post_meta($invoice_pid, 'tax_applicable', 0);
							update_post_meta($invoice_pid, 'tax_set', 1);
							update_post_meta($invoice_pid, 'tax_rate', 0);			
						}
					}
					if(!empty($system_tax) && empty($client_tax)) {
						$tax = $subtotal / 100 * $system_tax;
						if(!empty($system_stax) && empty($client_stax)) {
							$stax = $subtotal / 100 * $system_stax;
							$total = $subtotal + $tax + $stax;
						} else {
							$stax = 0;
							$total = $subtotal + $tax;
						}
					} else {
						$tax = 0;
						$total = $subtotal;
					}
					$invoice_totals = array(
						'sub' => number_format((float)$subtotal, 2, '.', ''),
						'tax' => number_format((float)$tax, 2, '.', ''),
						'stax' => number_format((float)$stax, 2, '.', ''),
						'total' => number_format((float)$total, 2, '.', '')
					);
					update_post_meta($invoice_pid, 'invoice_totals', $invoice_totals);
					$auto_invoice = $invoice['auto'];
					if($auto_invoice == 1) {
						$deposit = false;
						$pm_name = false;
						pto_process_invoice_emails($invoice_pid, $pm_name, $deposit);
					}						
				}
				$frequency = $invoice['frequency'];
				$days = 86400;
				$weeks = 604800;
				$biweeks = 1209600;
				$months = 2592000;
				$bimonths = 5184000;
				$threemonths = 7884000;
				$sixmonths = 15768000;
				$years = 31536000;
				$biyears = 31536000;
				if($frequency == 'daily') {
					$next_run = $invoice['next_run'] + $days;
				} else if($frequency == 'weekly') {
					$next_run = $invoice['next_run'] + $weeks;
				} else if($frequency == 'biweekly') {
					$next_run = $invoice['next_run'] + $biweeks;
				} else if($frequency == 'monthly') {
					$next_run = $invoice['next_run'] + $months;
				} else if($frequency == 'bimonthly') {
					$next_run = $invoice['next_run'] + $bimonths;
				} else if($frequency == 'threemonthly') {
					$next_run = $invoice['next_run'] + $threemonths;
				} else if($frequency == 'sixmonthly') {
					$next_run = $invoice['next_run'] + $sixmonths;
				} else if($frequency == 'yearly') {
					$next_run = $invoice['next_run'] + $years;
				} else if($frequency == 'biyearly') {
					$next_run = $invoice['next_run'] + $biyears;
				}
				$recurring_invoices[$key]['next_run'] = $next_run;
				$recurring_invoices[$key]['last_run'] = time();					
			}
			if($end < $now) {
				$recurring_invoices[$key]['next_run'] = '<span class="task_over">' . __('Finished', 'cqpim') . '</div>';
				$recurring_invoices[$key]['status'] = 0;
			}
		}
		update_post_meta($client->ID, 'recurring_invoices', $recurring_invoices);
	}
}
add_action( "wp_ajax_pto_add_new_recurring_invoice", "pto_add_new_recurring_invoice");
function pto_add_new_recurring_invoice() {
	$client_id = isset($_POST['client_id']) ? $_POST['client_id'] : '';
	$title = isset($_POST['title']) ? $_POST['title'] : '';
	$start = isset($_POST['start']) ? $_POST['start'] : '';
	$start = pto_convert_date($start);
	$end = isset($_POST['end']) ? $_POST['end'] : '';
	$end = pto_convert_date($end);
	$frequency = isset($_POST['frequency']) ? $_POST['frequency'] : '';
	$status = isset($_POST['status']) ? $_POST['status'] : '';
	$contact = isset($_POST['contact']) ? $_POST['contact'] : '';
	$auto = isset($_POST['auto']) ? $_POST['auto'] : '';
	$partial = isset($_POST['partial']) ? $_POST['partial'] : '';
	$items = isset($_POST['items']) ? $_POST['items'] : '';
	$i = 1;
	$i2 = 0;
	$keys = array();
	foreach($items as $item) {
		$keys[$i2][] = $item;
		if($i % 3 == 0) {
			$i2++;
		}
		$i++;
	}
	$items = array();
	foreach($keys as $key) {
		$items[] = array(
			'qty' => $key[0],
			'desc' => $key[1],
			'price' => $key[2]		
		);
	}
	if(!empty($client_id)) {
		if(!empty($start)) {
			if(!is_numeric($start)) {
				$start_str = str_replace('/','-',$start);
				$start_str = str_replace('.','-',$start);
				$next = strtotime($start_str);
			} else {
				$next = $start;
			}
		} else {
			$start = current_time('timestamp');
			$next = time();
		}
		$recurring_invoices = get_post_meta($client_id, 'recurring_invoices', true);
		if(empty($recurring_invoices)) {
			$recurring_invoices = array();
		}		
		$recurring_invoices[] = array(
			'title' => $title,
			'start' => $start,
			'end' => $end,
			'frequency' => $frequency,
			'status' => $status,
			'contact' => $contact,
			'auto' => $auto,
			'partial' => $partial,
			'items' => $items,
			'next_run' => $next,
		);
		update_post_meta($client_id, 'recurring_invoices', $recurring_invoices);
		$return =  array( 
			'error' 	=> false,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('The Recurring Invoice has been added.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();			
	} else {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('The Client ID is missing and the Recurring Invoice could not be added. Please try again.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();			
	}
}
add_action( "wp_ajax_pto_edit_recurring_invoice", "pto_edit_recurring_invoice");
function pto_edit_recurring_invoice() {
	$key = isset($_POST['key']) ? $_POST['key'] : '';
	$client_id = isset($_POST['client_id']) ? $_POST['client_id'] : '';
	$title = isset($_POST['title']) ? $_POST['title'] : '';
	$start = isset($_POST['start']) ? $_POST['start'] : '';
	$start = pto_convert_date($start);
	$end = isset($_POST['end']) ? $_POST['end'] : '';
	$end = pto_convert_date($end);
	$frequency = isset($_POST['frequency']) ? $_POST['frequency'] : '';
	$status = isset($_POST['status']) ? $_POST['status'] : '';
	$contact = isset($_POST['contact']) ? $_POST['contact'] : '';
	$auto = isset($_POST['auto']) ? $_POST['auto'] : '';
	$partial = isset($_POST['partial']) ? $_POST['partial'] : '';
	$items = isset($_POST['items']) ? $_POST['items'] : '';
	$i = 1;
	$i2 = 0;
	$keys = array();
	foreach($items as $item) {
		$keys[$i2][] = $item;
		if($i % 3 == 0) {
			$i2++;
		}
		$i++;
	}
	$items = array();
	foreach($keys as $key_bob) {
		$items[] = array(
			'qty' => $key_bob[0],
			'desc' => $key_bob[1],
			'price' => $key_bob[2]		
		);
	}
	if($client_id) {
		if(!empty($start)) {
			if(!is_numeric($start)) {
				$start_str = str_replace('/','-',$start);
				$start_str = str_replace('.','-',$start);
				$next = strtotime($start_str);
			} else {
				$next = $start;
			}
		} else {
			$start = current_time('timestamp');
			$next = current_time('timestamp');
		}
		$recurring_invoices = get_post_meta($client_id, 'recurring_invoices', true);		
		$recurring_invoices[$key] = array(
			'title' => $title,
			'start' => $start,
			'end' => $end,
			'frequency' => $frequency,
			'status' => $status,
			'contact' => $contact,
			'auto' => $auto,
			'partial' => $partial,
			'items' => $items,
			'next_run' => $next,
		);
		update_post_meta($client_id, 'recurring_invoices', $recurring_invoices);
		$return =  array( 
			'error' 	=> false,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('The Recurring Invoice has been Updated.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();			
	} else {
		$return =  array( 
			'error' 	=> true,
			'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('The Client ID or Invoice Key is missing and the Recurring Invoice could not be edited. Please try again.', 'cqpim') . '</div>',
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit();			
	}
}
add_action( "wp_ajax_pto_delete_recurring_invoice", "pto_delete_recurring_invoice");
function pto_delete_recurring_invoice() {
	$client_id = $_POST['client_id'];
	$key = $_POST['key'];
	$recurring_invoices = get_post_meta($client_id, 'recurring_invoices', true);
	if(!empty($recurring_invoices[$key])) {
		unset($recurring_invoices[$key]);
	}
	$recurring_invoices = array_filter($recurring_invoices);
	update_post_meta($client_id, 'recurring_invoices', $recurring_invoices);
	$return =  array( 
		'error' 	=> false,
		'message' 	=> '',
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit();	
}
add_action( "wp_ajax_pto_create_deposit_invoice", "pto_create_deposit_invoice");
function pto_create_deposit_invoice($project_id, $pm_name = NULL) {
	if(get_option('disable_invoices') != 1) {
	if(!empty($_POST['project_id'])) {
		$project_id = $_POST['project_id'];
		$ajax = true;
	}
	$project_details = get_post_meta($project_id, 'project_details', true);
	$tax_app = get_post_meta($project_id, 'tax_applicable', true);	
	$tax_rate = get_post_meta($project_id, 'tax_rate', true);
	$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
	$project_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
	$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
	$start = isset($project_details['start_date']) ? $project_details['start_date'] : '';
	$deposit = isset($project_details['deposit_amount']) ? $project_details['deposit_amount'] : '';
	$invoice_id = pto_get_invoice_id();
	$new_invoice = array(
		'post_type' => 'cqpim_invoice',
		'post_status' => 'publish',
		'post_content' => '',
		'post_title' =>  $invoice_id,
		'post_password' => pto_random_string(10),
	);
	$invoice_pid = wp_insert_post( $new_invoice, true );
	if( ! is_wp_error( $invoice_pid ) ){	
		if(!is_numeric($start)) {
			$start_unix = str_replace('/', '-', $start);
			$start_unix = strtotime($start_unix);
		} else {
			$start_unix = $start;
		}
		$invoice_details = array(
			'client_id' => $client_id,
			'deposit' => true,
			'due' => $start,
			'project_id' => $project_id,
			'terms_over' => $start_unix,
			'invoice_date' => current_time('timestamp'),
			'allow_partial' => false,
		);
		update_post_meta($invoice_pid, 'invoice_project', $project_id);
		update_post_meta($invoice_pid, 'invoice_client', $client_id);
		update_post_meta($invoice_pid, 'client_contact', $client_contact);
		update_post_meta($invoice_pid, 'invoice_id', $invoice_id);
		update_post_meta($invoice_pid, 'invoice_details', $invoice_details);			
		$currency = get_option('currency_symbol');
		$currency_code = get_option('currency_code');
		$currency_position = get_option('currency_symbol_position');
		$currency_space = get_option('currency_symbol_space'); 
		$client_currency = get_post_meta($client_id, 'currency_symbol', true);
		$client_currency_code = get_post_meta($client_id, 'currency_code', true);
		$client_currency_space = get_post_meta($client_id, 'currency_space', true);		
		$client_currency_position = get_post_meta($client_id, 'currency_position', true);
		$quote_currency = get_post_meta($project_id, 'currency_symbol', true);
		$quote_currency_code = get_post_meta($project_id, 'currency_code', true);
		$quote_currency_space = get_post_meta($project_id, 'currency_space', true);	
		$quote_currency_position = get_post_meta($project_id, 'currency_position', true);
		if(!empty($quote_currency)) {
			update_post_meta($invoice_pid, 'currency_symbol', $quote_currency);
		} else {
			if(!empty($client_currency)) {
				update_post_meta($invoice_pid, 'currency_symbol', $client_currency);
			} else {
				update_post_meta($invoice_pid, 'currency_symbol', $currency);
			}
		}
		if(!empty($quote_currency_code)) {
			update_post_meta($invoice_pid, 'currency_code', $quote_currency_code);
		} else {
			if(!empty($client_currency_code)) {
				update_post_meta($invoice_pid, 'currency_code', $client_currency_code);
			} else {
				update_post_meta($invoice_pid, 'currency_code', $currency_code);
			}
		}
		if(!empty($quote_currency_space)) {
			update_post_meta($invoice_pid, 'currency_space', $quote_currency_space);
		} else {
			if(!empty($client_currency_space)) {
				update_post_meta($invoice_pid, 'currency_space', $client_currency_space);
			} else {
				update_post_meta($invoice_pid, 'currency_space', $currency_space);
			}
		}
		if(!empty($quote_currency_position)) {
			update_post_meta($invoice_pid, 'currency_position', $quote_currency_position);
		} else {
			if(!empty($client_currency_position)) {
				update_post_meta($invoice_pid, 'currency_position', $client_currency_position);
			} else {
				update_post_meta($invoice_pid, 'currency_position', $currency_position);
			}
		}			
		$project_details = get_post_meta($project_id, 'project_details', true);
		$project_details['deposit_invoice_id'] = $invoice_pid;
		update_post_meta($project_id, 'project_details', $project_details);
		$project_elements = get_post_meta($project_id, 'project_elements', true);
		$project_total = 0;
		foreach($project_elements as $element) {
			$element_cost = isset($element['cost']) ? $element['cost'] : 0;
			$cost = preg_replace("/[^\\d.]+/","", $element_cost);
			$element_total = $cost;
			$project_total = $project_total + $element_total;
		}
		$deposit_to_pay = $project_total / 100 * $deposit;
		$deposit_to_pay = number_format((float)$deposit_to_pay, 2, '.', '');
		$description = sprintf(__('Deposit Payment. Project Ref %1$s', 'cqpim'), $project_ref);
		$line_items = array();
		$line_items[] = array(
			'qty' => 1,
			'desc' => $description,
			'price' => $deposit_to_pay,
			'sub' => $deposit_to_pay				
		);
		update_post_meta($invoice_pid, 'line_items', $line_items);
		$tax_app = get_post_meta($invoice_pid, 'tax_set', true);
		$system_tax = get_option('sales_tax_rate');
		$system_stax = get_option('secondary_sales_tax_rate');
		if(empty($tax_app)) {
			$client_details = get_post_meta($client_id, 'client_details', true);
			$client_tax = isset($client_details['tax_disabled']) ? $client_details['tax_disabled'] : '';
			$client_stax = isset($client_details['stax_disabled']) ? $client_details['stax_disabled'] : '';
			if(!empty($system_tax) && empty($client_tax)) {
				update_post_meta($invoice_pid, 'tax_applicable', 1);
				update_post_meta($invoice_pid, 'tax_set', 1);	
				update_post_meta($invoice_pid, 'tax_rate', $system_tax);	
				if(!empty($system_stax) && empty($client_stax)) {
					update_post_meta($invoice_pid, 'stax_applicable', 1);
					update_post_meta($invoice_pid, 'stax_set', 1);	
					update_post_meta($invoice_pid, 'stax_rate', $system_stax);			
				} else {
					update_post_meta($invoice_pid, 'stax_applicable', 0);
					update_post_meta($invoice_pid, 'stax_set', 1);
					update_post_meta($invoice_pid, 'stax_rate', 0);				
				}
			} else {
				update_post_meta($invoice_pid, 'tax_applicable', 0);
				update_post_meta($invoice_pid, 'tax_set', 1);
				update_post_meta($invoice_pid, 'tax_rate', 0);			
			}
		}	
		$subtotal = $deposit_to_pay;
		if(!empty($system_tax) && empty($client_tax)) {
			$tax = $subtotal / 100 * $system_tax;
			if(!empty($system_stax) && empty($client_stax)) {
				$stax = $subtotal / 100 * $system_stax;
				$total = $subtotal + $tax + $stax;
			} else {
				$stax = 0;
				$total = $subtotal + $tax;
			}
		} else {
			$tax = 0;
			$total = $subtotal;
		}
		$invoice_totals = array(
			'sub' => number_format((float)$subtotal, 2, '.', ''),
			'tax' => number_format((float)$tax, 2, '.', ''),
			'stax' => number_format((float)$stax, 2, '.', ''),
			'total' => number_format((float)$total, 2, '.', '')
		);
		update_post_meta($invoice_pid, 'invoice_totals', $invoice_totals);			
		$auto_invoice = get_option('auto_send_invoices');
		$project_details = get_post_meta($project_id, 'project_details', true);
		$pm_name = isset($project_details['pm_name']) ? $project_details['pm_name'] : '';
		$project_progress = get_post_meta($project_id, 'project_progress', true);
		$project_progress[] = array(
			'update' => __('Deposit Invoice created for Project', 'cqpim') . ': ' . $project_ref,
			'date' => current_time('timestamp'),
			'by' => 'System'
		);
		update_post_meta($project_id, 'project_progress', $project_progress );
		if($auto_invoice) {
			$deposit = true;
			pto_process_invoice_emails($invoice_pid, $pm_name, $deposit);
		}
		if(!empty($ajax)) {
			$return =  array( 
				'error' 	=> false,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('Deposit Invoice Created', 'cqpim') . '</div>',
			);
			header('Content-type: application/json');
			echo json_encode($return);	
			exit();				
		}
	} else {
		exit();
	}	
	} else {
		return;
	}
}
function pto_create_completion_invoice($project_id) {
	if(get_option('disable_invoices') != 1) {
	$project_details = get_post_meta($project_id, 'project_details', true);
	$tax_app = get_post_meta($project_id, 'tax_applicable', true);	
	$tax_rate = get_post_meta($project_id, 'tax_rate', true);
	$deposit_invoice = isset($project_details['deposit_invoice_id']) ? $project_details['deposit_invoice_id'] : '';
	$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
	$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
	$project_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
	$type = isset($project_details['quote_type']) ? $project_details['quote_type'] : '';
	$project_elements = get_post_meta($project_id, 'project_elements', true);
	$project_total = 0;
	foreach($project_elements as $element) {
		$cost = preg_replace("/[^\\d.]+/","", $element['acost']);
		$element_total = $cost;
		$project_total = $project_total + $element_total;
	}		
	$line_items = array();
	foreach($project_elements as $element) {
		$cost = preg_replace("/[^\\d.]+/","", $element['acost']);
		$line_items[] = array(
			'qty' => 1,
			'desc' => $element['title'],
			'price' => number_format((float)$cost, 2, '.', ''),
			'sub' => number_format((float)$cost, 2, '.', '')				
		);
	}
	if(!empty($deposit_invoice)) {
		$deposit_invoice = get_post($deposit_invoice);
		$invoice_details = get_post_meta($deposit_invoice->ID, 'invoice_details', true);
		$invoice_id = get_post_meta($deposit_invoice->ID, 'invoice_id', true);	
		if(!empty($invoice_details['paid'])) {
			$line_items_dep = get_post_meta($deposit_invoice->ID, 'line_items', true);
			foreach($line_items_dep as $item) {
				$line_items[] = array(
					'qty' => $item['qty'],
					'desc' => sprintf(__('LESS ALREADY RECEIVED - Invoice: %1$s', 'cqpim'),  $invoice_id),
					'price' => '-' . $item['price'],
					'sub' => '-' . $item['sub']				
				);
			}
		}
	}
	$invoice_id = pto_get_invoice_id();
	$new_invoice = array(
		'post_type' => 'cqpim_invoice',
		'post_status' => 'publish',
		'post_content' => '',
		'post_title' =>  $invoice_id,
		'post_password' => pto_random_string(10),
	);
	$invoice_pid = wp_insert_post( $new_invoice, true );
	if( ! is_wp_error( $invoice_pid ) ){
		$client_details = get_post_meta($client_id, 'client_details', true);
		$invoice_terms = isset($client_details['invoice_terms']) ? $client_details['invoice_terms']: '';
		$system_invoice_terms = get_option('company_invoice_terms');
		if(!empty($invoice_terms)) {
			$terms = $invoice_terms;
		} else {
			$terms = $system_invoice_terms;
		}
		$date = time();
		$terms_over = strtotime('+' . $terms . ' days', $date);
		$allow_partial = get_option('client_invoice_allow_partial');
		$allow_partial = isset($allow_partial) ? $allow_partial : 0;
		if($terms != 1) {
			$invoice_details = array(
				'client_id' => $client_id,
				'project_id' => $project_id,
				'completion' => true,
				'terms_over' => $terms_over,
				'invoice_date' => current_time('timestamp'),
				'allow_partial' => $allow_partial,
			);
		} else {
			$invoice_details = array(
				'client_id' => $client_id,
				'project_id' => $project_id,
				'on_receipt' => true,
				'completion' => true,
				'terms_over' => $terms_over,
				'invoice_date' => current_time('timestamp'),
				'allow_partial' => $allow_partial,
			);
		}
		update_post_meta($invoice_pid, 'invoice_project', $project_id);
		update_post_meta($invoice_pid, 'invoice_id', $invoice_id);
		update_post_meta($invoice_pid, 'client_contact', $client_contact);
		update_post_meta($invoice_pid, 'invoice_details', $invoice_details);
		update_post_meta($invoice_pid, 'invoice_client', $client_id);
		$currency = get_option('currency_symbol');
		$currency_code = get_option('currency_code');
		$currency_position = get_option('currency_symbol_position');
		$currency_space = get_option('currency_symbol_space'); 
		$client_currency = get_post_meta($client_id, 'currency_symbol', true);
		$client_currency_code = get_post_meta($client_id, 'currency_code', true);
		$client_currency_space = get_post_meta($client_id, 'currency_space', true);		
		$client_currency_position = get_post_meta($client_id, 'currency_position', true);
		$quote_currency = get_post_meta($project_id, 'currency_symbol', true);
		$quote_currency_code = get_post_meta($project_id, 'currency_code', true);
		$quote_currency_space = get_post_meta($project_id, 'currency_space', true);	
		$quote_currency_position = get_post_meta($project_id, 'currency_position', true);
		if(!empty($quote_currency)) {
			update_post_meta($invoice_pid, 'currency_symbol', $quote_currency);
		} else {
			if(!empty($client_currency)) {
				update_post_meta($invoice_pid, 'currency_symbol', $client_currency);
			} else {
				update_post_meta($invoice_pid, 'currency_symbol', $currency);
			}
		}
		if(!empty($quote_currency_code)) {
			update_post_meta($invoice_pid, 'currency_code', $quote_currency_code);
		} else {
			if(!empty($client_currency_code)) {
				update_post_meta($invoice_pid, 'currency_code', $client_currency_code);
			} else {
				update_post_meta($invoice_pid, 'currency_code', $currency_code);
			}
		}
		if(!empty($quote_currency_space)) {
			update_post_meta($invoice_pid, 'currency_space', $quote_currency_space);
		} else {
			if(!empty($client_currency_space)) {
				update_post_meta($invoice_pid, 'currency_space', $client_currency_space);
			} else {
				update_post_meta($invoice_pid, 'currency_space', $currency_space);
			}
		}
		if(!empty($quote_currency_position)) {
			update_post_meta($invoice_pid, 'currency_position', $quote_currency_position);
		} else {
			if(!empty($client_currency_position)) {
				update_post_meta($invoice_pid, 'currency_position', $client_currency_position);
			} else {
				update_post_meta($invoice_pid, 'currency_position', $currency_position);
			}
		}
		$project_details = get_post_meta($project_id, 'project_details', true);
		$project_details['completion_invoice_id'] = $invoice_pid;
		update_post_meta($project_id, 'project_details', $project_details);
		update_post_meta($invoice_pid, 'line_items', $line_items);
		$subtotal = 0;
		foreach($line_items as $item) {
			$subtotal = $subtotal + $item['sub'];
		}
		$tax_app = get_post_meta($invoice_pid, 'tax_set', true);
		$system_tax = get_option('sales_tax_rate');
		$system_stax = get_option('secondary_sales_tax_rate');
		if(empty($tax_app)) {
			$client_details = get_post_meta($client_id, 'client_details', true);
			$client_tax = isset($client_details['tax_disabled']) ? $client_details['tax_disabled'] : '';
			$client_stax = isset($client_details['stax_disabled']) ? $client_details['stax_disabled'] : '';
			if(!empty($system_tax) && empty($client_tax)) {
				update_post_meta($invoice_pid, 'tax_applicable', 1);
				update_post_meta($invoice_pid, 'tax_set', 1);	
				update_post_meta($invoice_pid, 'tax_rate', $system_tax);	
				if(!empty($system_stax) && empty($client_stax)) {
					update_post_meta($invoice_pid, 'stax_applicable', 1);
					update_post_meta($invoice_pid, 'stax_set', 1);	
					update_post_meta($invoice_pid, 'stax_rate', $system_stax);			
				} else {
					update_post_meta($invoice_pid, 'stax_applicable', 0);
					update_post_meta($invoice_pid, 'stax_set', 1);
					update_post_meta($invoice_pid, 'stax_rate', 0);				
				}
			} else {
				update_post_meta($invoice_pid, 'tax_applicable', 0);
				update_post_meta($invoice_pid, 'tax_set', 1);
				update_post_meta($invoice_pid, 'tax_rate', 0);			
			}
		}
		if(!empty($system_tax) && empty($client_tax)) {
			$tax = $subtotal / 100 * $system_tax;
			if(!empty($system_stax) && empty($client_stax)) {
				$stax = $subtotal / 100 * $system_stax;
				$total = $subtotal + $tax + $stax;
			} else {
				$stax = 0;
				$total = $subtotal + $tax;
			}
		} else {
			$tax = 0;
			$stax = 0;
			$total = $subtotal;
		}
		$invoice_totals = array(
			'sub' => number_format((float)$subtotal, 2, '.', ''),
			'tax' => number_format((float)$tax, 2, '.', ''),
			'stax' => number_format((float)$stax, 2, '.', ''),
			'total' => number_format((float)$total, 2, '.', '')
		);
		update_post_meta($invoice_pid, 'invoice_totals', $invoice_totals);			
		$auto_invoice = get_option('auto_send_invoices');
		$project_details = get_post_meta($project_id, 'project_details', true);
		$project_progress = get_post_meta($project_id, 'project_progress', true);
		$project_progress[] = array(
			'update' => __('Completion Invoice created for Project', 'cqpim') . ': ' . $project_details['quote_ref'],
			'date' => current_time('timestamp'),
			'by' => 'System'
		);
		update_post_meta($project_id, 'project_progress', $project_progress );
		if($auto_invoice == 1) {
			$completion = true;
			pto_process_invoice_emails($invoice_pid);
		}
	} else {
		exit();
	}	
	} else {
		return;
	}
}
function pto_create_ms_completion_invoice($project_id, $milestone) {	
	if(get_option('disable_invoices') != 1) {
		$project_details = get_post_meta($project_id, 'project_details', true);
		$tax_app = get_post_meta($project_id, 'tax_applicable', true);	
		$tax_rate = get_post_meta($project_id, 'tax_rate', true);
		$project_elements = get_post_meta($project_id, 'project_elements', true);		
		$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
		$project_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
		$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
		$deposit = isset($project_details['deposit_amount']) ? $project_details['deposit_amount'] : '';
		$invoice_id = pto_get_invoice_id();
		$new_invoice = array(
			'post_type' => 'cqpim_invoice',
			'post_status' => 'publish',
			'post_content' => '',
			'post_title' =>  $invoice_id,
			'post_password' => pto_random_string(10),
		);
		remove_action('save_post', 'save_pto_project_elements_metabox_data');
		$invoice_pid = wp_insert_post( $new_invoice, true );
		add_action('save_post', 'save_pto_project_elements_metabox_data');	
		if( ! is_wp_error( $invoice_pid ) ){
			$client_details = get_post_meta($client_id, 'client_details', true);
			$invoice_terms = isset($client_details['invoice_terms']) ? $client_details['invoice_terms']: '';
			$system_invoice_terms = get_option('company_invoice_terms');
			if(!empty($invoice_terms)) {
				$terms = $invoice_terms;
			} else {
				$terms = $system_invoice_terms;
			}
			$date = time();
			$terms_over = strtotime('+' . $terms . ' days', $date);
			$allow_partial = get_option('client_invoice_allow_partial');
			$allow_partial = isset($allow_partial) ? $allow_partial : 0;
			if($terms != 1) {
				$invoice_details = array(
					'client_id' => $client_id,
					'project_id' => $project_id,
					'completion' => true,
					'terms_over' => $terms_over,
					'invoice_date' => current_time('timestamp'),
					'allow_partial' => $allow_partial,
				);
			} else {
				$invoice_details = array(
					'client_id' => $client_id,
					'project_id' => $project_id,
					'on_receipt' => true,
					'completion' => true,
					'terms_over' => $terms_over,
					'invoice_date' => current_time('timestamp'),
					'allow_partial' => $allow_partial,
				);
			}
			update_post_meta($invoice_pid, 'invoice_project', $project_id);
			update_post_meta($invoice_pid, 'invoice_id', $invoice_id);
			update_post_meta($invoice_pid, 'client_contact', $client_contact);
			update_post_meta($invoice_pid, 'invoice_details', $invoice_details);
			update_post_meta($invoice_pid, 'invoice_client', $client_id);	
			$currency = get_option('currency_symbol');
			$currency_code = get_option('currency_code');
			$currency_position = get_option('currency_symbol_position');
			$currency_space = get_option('currency_symbol_space'); 
			$client_currency = get_post_meta($client_id, 'currency_symbol', true);
			$client_currency_code = get_post_meta($client_id, 'currency_code', true);
			$client_currency_space = get_post_meta($client_id, 'currency_space', true);		
			$client_currency_position = get_post_meta($client_id, 'currency_position', true);
			$quote_currency = get_post_meta($project_id, 'currency_symbol', true);
			$quote_currency_code = get_post_meta($project_id, 'currency_code', true);
			$quote_currency_space = get_post_meta($project_id, 'currency_space', true);	
			$quote_currency_position = get_post_meta($project_id, 'currency_position', true);
			if(!empty($quote_currency)) {
				update_post_meta($invoice_pid, 'currency_symbol', $quote_currency);
			} else {
				if(!empty($client_currency)) {
					update_post_meta($invoice_pid, 'currency_symbol', $client_currency);
				} else {
					update_post_meta($invoice_pid, 'currency_symbol', $currency);
				}
			}
			if(!empty($quote_currency_code)) {
				update_post_meta($invoice_pid, 'currency_code', $quote_currency_code);
			} else {
				if(!empty($client_currency_code)) {
					update_post_meta($invoice_pid, 'currency_code', $client_currency_code);
				} else {
					update_post_meta($invoice_pid, 'currency_code', $currency_code);
				}
			}
			if(!empty($quote_currency_space)) {
				update_post_meta($invoice_pid, 'currency_space', $quote_currency_space);
			} else {
				if(!empty($client_currency_space)) {
					update_post_meta($invoice_pid, 'currency_space', $client_currency_space);
				} else {
					update_post_meta($invoice_pid, 'currency_space', $currency_space);
				}
			}
			if(!empty($quote_currency_position)) {
				update_post_meta($invoice_pid, 'currency_position', $quote_currency_position);
			} else {
				if(!empty($client_currency_position)) {
					update_post_meta($invoice_pid, 'currency_position', $client_currency_position);
				} else {
					update_post_meta($invoice_pid, 'currency_position', $currency_position);
				}
			}
			$total = 0;	
			$cost = preg_replace("/[^\\d.]+/","", $milestone['acost']);
			$element_total = $cost;
			$project_total = $project_total + $element_total;					
			$line_items = array();		
			$line_items[] = array(
				'qty' => 1,
				'desc' => __('Milestone', 'cqpim') . ': ' . $milestone['title'],
				'price' => number_format((float)$cost, 2, '.', ''),
				'sub' => number_format((float)$cost, 2, '.', '')				
			);	
			if($deposit != 'none') {
				$deductions = $cost / 100 * $deposit;
				$line_items[] = array(
					'qty' => 1,
					'desc' => sprintf(__('LESS DEPOSIT PERCENTAGE - %1$s ', 'cqpim'),  $deposit),
					'price' => '-' . $deductions,
					'sub' => '-' . $deductions,				
				);	
			}
			update_post_meta($invoice_pid, 'line_items', $line_items);
			$subtotal = 0;
			foreach($line_items as $item) {
				$subtotal = $subtotal + $item['sub'];
			}
			$tax_app = get_post_meta($invoice_pid, 'tax_set', true);
			$system_tax = get_option('sales_tax_rate');
			$system_stax = get_option('secondary_sales_tax_rate');
			if(empty($tax_app)) {
				$client_details = get_post_meta($client_id, 'client_details', true);
				$client_tax = isset($client_details['tax_disabled']) ? $client_details['tax_disabled'] : '';
				$client_stax = isset($client_details['stax_disabled']) ? $client_details['stax_disabled'] : '';
				if(!empty($system_tax) && empty($client_tax)) {
					update_post_meta($invoice_pid, 'tax_applicable', 1);
					update_post_meta($invoice_pid, 'tax_set', 1);	
					update_post_meta($invoice_pid, 'tax_rate', $system_tax);	
					if(!empty($system_stax) && empty($client_stax)) {
						update_post_meta($invoice_pid, 'stax_applicable', 1);
						update_post_meta($invoice_pid, 'stax_set', 1);	
						update_post_meta($invoice_pid, 'stax_rate', $system_stax);			
					} else {
						update_post_meta($invoice_pid, 'stax_applicable', 0);
						update_post_meta($invoice_pid, 'stax_set', 1);
						update_post_meta($invoice_pid, 'stax_rate', 0);				
					}
				} else {
					update_post_meta($invoice_pid, 'tax_applicable', 0);
					update_post_meta($invoice_pid, 'tax_set', 1);
					update_post_meta($invoice_pid, 'tax_rate', 0);			
				}
			}
			if(!empty($system_tax) && empty($client_tax)) {
				$tax = $subtotal / 100 * $system_tax;
				if(!empty($system_stax) && empty($client_stax)) {
					$stax = $subtotal / 100 * $system_stax;
					$total = $subtotal + $tax + $stax;
				} else {
					$stax = 0;
					$total = $subtotal + $tax;
				}
			} else {
				$tax = 0;
				$total = $subtotal;
			}
			$invoice_totals = array(
				'sub' => number_format((float)$subtotal, 2, '.', ''),
				'tax' => number_format((float)$tax, 2, '.', ''),
				'stax' => number_format((float)$stax, 2, '.', ''),
				'total' => number_format((float)$total, 2, '.', '')
			);
			update_post_meta($invoice_pid, 'invoice_totals', $invoice_totals);	
			$current_user = wp_get_current_user();
			$current_user = $current_user->display_name;
			$project_progress = get_post_meta($project_id, 'project_progress', true);
			$project_progress[] = array(
				'update' => __('Milestone Invoice Created', 'cqpim') . ': ' . $milestone['title'],
				'date' => current_time('timestamp'),
				'by' => $current_user,
			);
			update_post_meta($project_id, 'project_progress', $project_progress );
			$auto_invoice = get_option('auto_send_invoices');
			$project_details = get_post_meta($project_id, 'project_details', true);
			if($auto_invoice == 1) {
				pto_process_invoice_emails($invoice_pid);
			}			
		}
	} else {
		return;
	}
}
add_action( "wp_ajax_pto_send_ticket_invoice", "pto_send_ticket_invoice");
function pto_send_ticket_invoice($ticket_id) {
	if(get_option('disable_invoices') != 1) {
		$ticket_id = isset($_POST['pid']) ? $_POST['pid'] : '';
		if(empty($ticket_id)) {
			$return =  array( 
				'error' 	=> true,
				'errors' 	=> __('Ticket Id not present, please try again', 'cqpim'),
			);
			header('Content-type: application/json');
			echo json_encode($return);
			exit;
		}
		$ticket_object = get_post($ticket_id);
		$client_id = get_post_meta($ticket_id, 'ticket_client', true);			
		$project_elements = get_post_meta($ticket_id, 'quote_elements', true);
		foreach($project_elements as $element) {
			$cost = isset($element['acost']) ? $element['acost'] : '';
			$status = isset($element['status']) ? $element['status'] : '';
			$cost = preg_replace("/[^\\d.]+/","", $element['acost']);
			if(empty($cost) || $status != 'complete') {
				$fail = true;
			}
		}
		if(!empty($fail)) {
			$return =  array( 
				'error' 	=> true,
				'errors' 	=> __('At least one milestone does not have a finished cost or is not marked as complete!', 'cqpim'),
			);
			header('Content-type: application/json');
			echo json_encode($return);
			exit;			
		}			
		$project_total = 0;
		foreach($project_elements as $element) {
			$cost = preg_replace("/[^\\d.]+/","", $element['acost']);
			$element_total = $cost;
			$project_total = $project_total + $element_total;
		}		
		$line_items = array();
		foreach($project_elements as $element) {
			$cost = preg_replace("/[^\\d.]+/","", $element['acost']);
			$line_items[] = array(
				'qty' => 1,
				'desc' => __('Ticket: ', 'cqpim') . $ticket_object->ID . ' - ' . $element['title'],
				'price' => number_format((float)$cost, 2, '.', ''),
				'sub' => number_format((float)$cost, 2, '.', '')				
			);
		}
		$invoice_id = pto_get_invoice_id();
		$new_invoice = array(
			'post_type' => 'cqpim_invoice',
			'post_status' => 'publish',
			'post_content' => '',
			'post_title' =>  $invoice_id,
			'post_password' => pto_random_string(10),
		);
		$invoice_pid = wp_insert_post( $new_invoice, true );
		if( ! is_wp_error( $invoice_pid ) ){
			$client_details = get_post_meta($client_id, 'client_details', true);
			$invoice_terms = isset($client_details['invoice_terms']) ? $client_details['invoice_terms']: '';
			$system_invoice_terms = get_option('company_invoice_terms');
			if(!empty($invoice_terms)) {
				$terms = $invoice_terms;
			} else {
				$terms = $system_invoice_terms;
			}
			$date = time();
			$terms_over = strtotime('+' . $terms . ' days', $date);
			$allow_partial = get_option('client_invoice_allow_partial');
			$allow_partial = isset($allow_partial) ? $allow_partial : 0;
			if($terms != 1) {
				$invoice_details = array(
					'client_id' => $client_id,
					'project_id' => $ticket_id,
					'completion' => true,
					'terms_over' => $terms_over,
					'invoice_date' => current_time('timestamp'),
					'ticket' => true,
					'allow_partial' => $allow_partial,
				);
			} else {
				$invoice_details = array(
					'client_id' => $client_id,
					'project_id' => $ticket_id,
					'on_receipt' => true,
					'completion' => true,
					'terms_over' => $terms_over,
					'invoice_date' => current_time('timestamp'),
					'ticket' => true,
					'allow_partial' => $allow_partial,
				);
			}
			update_post_meta($invoice_pid, 'invoice_project', $ticket_id);
			update_post_meta($invoice_pid, 'invoice_id', $invoice_id);
			update_post_meta($invoice_pid, 'invoice_details', $invoice_details);
			update_post_meta($invoice_pid, 'invoice_client', $client_id);
			update_post_meta($invoice_pid, 'client_contact', $ticket_object->post_author);
			$currency = get_option('currency_symbol');
			$currency_code = get_option('currency_code');
			$currency_position = get_option('currency_symbol_position');
			$currency_space = get_option('currency_symbol_space'); 
			$client_currency = get_post_meta($client_id, 'currency_symbol', true);
			$client_currency_code = get_post_meta($client_id, 'currency_code', true);
			$client_currency_space = get_post_meta($client_id, 'currency_space', true);		
			$client_currency_position = get_post_meta($client_id, 'currency_position', true);
			$quote_currency = get_post_meta($ticket_id, 'currency_symbol', true);
			$quote_currency_code = get_post_meta($ticket_id, 'currency_code', true);
			$quote_currency_space = get_post_meta($ticket_id, 'currency_space', true);	
			$quote_currency_position = get_post_meta($ticket_id, 'currency_position', true);
			if(!empty($quote_currency)) {
				update_post_meta($invoice_pid, 'currency_symbol', $quote_currency);
			} else {
				if(!empty($client_currency)) {
					update_post_meta($invoice_pid, 'currency_symbol', $client_currency);
				} else {
					update_post_meta($invoice_pid, 'currency_symbol', $currency);
				}
			}
			if(!empty($quote_currency_code)) {
				update_post_meta($invoice_pid, 'currency_code', $quote_currency_code);
			} else {
				if(!empty($client_currency_code)) {
					update_post_meta($invoice_pid, 'currency_code', $client_currency_code);
				} else {
					update_post_meta($invoice_pid, 'currency_code', $currency_code);
				}
			}
			if(!empty($quote_currency_space)) {
				update_post_meta($invoice_pid, 'currency_space', $quote_currency_space);
			} else {
				if(!empty($client_currency_space)) {
					update_post_meta($invoice_pid, 'currency_space', $client_currency_space);
				} else {
					update_post_meta($invoice_pid, 'currency_space', $currency_space);
				}
			}
			if(!empty($quote_currency_position)) {
				update_post_meta($invoice_pid, 'currency_position', $quote_currency_position);
			} else {
				if(!empty($client_currency_position)) {
					update_post_meta($invoice_pid, 'currency_position', $client_currency_position);
				} else {
					update_post_meta($invoice_pid, 'currency_position', $currency_position);
				}
			}			
			update_post_meta($invoice_pid, 'line_items', $line_items);
			$subtotal = 0;
			foreach($line_items as $item) {
				$subtotal = $subtotal + $item['sub'];
			}
			$tax_app = get_post_meta($invoice_pid, 'tax_set', true);
			$system_tax = get_option('sales_tax_rate');
			$system_stax = get_option('secondary_sales_tax_rate');
			if(empty($tax_app)) {
				$client_details = get_post_meta($client_id, 'client_details', true);
				$client_tax = isset($client_details['tax_disabled']) ? $client_details['tax_disabled'] : '';
				$client_stax = isset($client_details['stax_disabled']) ? $client_details['stax_disabled'] : '';
				if(!empty($system_tax) && empty($client_tax)) {
					update_post_meta($invoice_pid, 'tax_applicable', 1);
					update_post_meta($invoice_pid, 'tax_set', 1);	
					update_post_meta($invoice_pid, 'tax_rate', $system_tax);	
					if(!empty($system_stax) && empty($client_stax)) {
						update_post_meta($invoice_pid, 'stax_applicable', 1);
						update_post_meta($invoice_pid, 'stax_set', 1);	
						update_post_meta($invoice_pid, 'stax_rate', $system_stax);			
					} else {
						update_post_meta($invoice_pid, 'stax_applicable', 0);
						update_post_meta($invoice_pid, 'stax_set', 1);
						update_post_meta($invoice_pid, 'stax_rate', 0);				
					}
				} else {
					update_post_meta($invoice_pid, 'tax_applicable', 0);
					update_post_meta($invoice_pid, 'tax_set', 1);
					update_post_meta($invoice_pid, 'tax_rate', 0);			
				}
			}
			if(!empty($system_tax) && empty($client_tax)) {
				$tax = $subtotal / 100 * $system_tax;
				if(!empty($system_stax) && empty($client_stax)) {
					$stax = $subtotal / 100 * $system_stax;
					$total = $subtotal + $tax + $stax;
				} else {
					$stax = 0;
					$total = $subtotal + $tax;
				}
			} else {
				$tax = 0;
				$total = $subtotal;
			}
			$invoice_totals = array(
				'sub' => number_format((float)$subtotal, 2, '.', ''),
				'tax' => number_format((float)$tax, 2, '.', ''),
				'stax' => number_format((float)$stax, 2, '.', ''),
				'total' => number_format((float)$total, 2, '.', '')
			);
			update_post_meta($invoice_pid, 'invoice_totals', $invoice_totals);			
			$auto_invoice = get_option('auto_send_invoices');
			if($auto_invoice == 1) {
				$completion = true;
				pto_process_invoice_emails($invoice_pid);
			}	
			$ticket_details = get_post_meta($ticket_id, 'project_details', true);
			$ticket_details['invoice_sent'] = true;
			update_post_meta($ticket_id, 'project_details', $ticket_details);				
			$return =  array( 
				'error' 	=> false,
				'errors' 	=> '',
			);
			header('Content-type: application/json');
			echo json_encode($return);
			exit;				
		} else {
			$return =  array( 
				'error' 	=> true,
				'errors' 	=> __('Whoops, something went wrong!', 'cqpim'),
			);
			header('Content-type: application/json');
			echo json_encode($return);
			exit;
		}	
	} else {
		return;
	}
}
add_action( "wp_ajax_pto_populate_invoice_projects", "pto_populate_invoice_projects");
function pto_populate_invoice_projects() {
	$client_id = isset($_POST['ID']) ? $_POST['ID']: '';
	$args = array(
		'post_type' => 'cqpim_project',
		'posts_per_page' => -1,
		'post_status' => 'private',
	);
	$projects = get_posts($args);
	$projects_to_display = '';
	foreach($projects as $project) {
		$project_details = get_post_meta($project->ID, 'project_details', true);
		$project_client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
		if($project_client_id == $client_id) {
			$projects_to_display .= '<option value="' . $project->ID . '">' . $project_details['quote_ref'] . ' - ' . $project->post_title . '</option>';
		}
	}
	if(empty($projects_to_display)) {
		$return =  array( 
			'error' 	=> true,
			'options' 	=> '<option value="">' . __('No projects available', 'cqpim') . '</option>'
		);
		header('Content-type: application/json');
		echo json_encode($return);	
	} else {
		$return =  array( 
			'error' 	=> false,
			'options' 	=> '<option value="">' . __('Choose a Project', 'cqpim') . '</option>' . $projects_to_display
		);
		header('Content-type: application/json');
		echo json_encode($return);		
	}
	exit();
}
add_action( "wp_ajax_pto_process_invoice_emails", "pto_process_invoice_emails");
function pto_process_invoice_emails($invoice_id, $pm_name = 0, $deposit = 0) {
	if(!empty($_POST['invoice_id'])) {
		$invoice_id = $_POST['invoice_id'];
		$type = $_POST['type'];
		$ajax_post = true;
	} else {
		$type = 'send';
	}
	$invoice_details = get_post_meta($invoice_id, 'invoice_details', true);
	$invoice_project_id = get_post_meta($invoice_id, 'invoice_project', true);
	$project_details = get_post_meta($invoice_project_id, 'project_details', true);
	$client_contact = get_post_meta($invoice_id, 'client_contact', true);
	$invoice_no = get_post_meta($invoice_id, 'invoice_id', true);
	$client_id = $invoice_details['client_id'];
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	$client_details = get_post_meta($client_id, 'client_details', true);
	$terms = isset($client_details['invoice_terms']) ? $client_details['invoice_terms'] : '';
	if(empty($terms)) {
		$terms = get_option('company_invoice_terms');
	}
	$client_main_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
	if(empty($client_contacts)) {
		$client_contacts = array();
	}
	if(!empty($client_contact)) {
		if($client_contact == $client_main_id) {
			$to = $client_details['client_email'];
		} else {
			$to = $client_contacts[$client_contact]['email'];
		}
	} else {
		$to = $client_details['client_email'];
	}
	if(!empty($client_details['billing_email'])) {
		$to = $client_details['billing_email'];
	}
	if($deposit == true) {
		$email_content = get_option('client_deposit_invoice_email');
	} else {
		$email_content = get_option('client_invoice_email');
	}
	if($deposit) {
		$subject = get_option('client_deposit_invoice_subject');		
	} else {
		$subject = get_option('client_invoice_subject');
	}
	if($client_contact == $client_main_id) {
		$email_content = str_replace('%%CLIENT_NAME%%', $client_details['client_contact'], $email_content);
		$email_content = str_replace('%%CLIENT_EMAIL%%', $client_details['client_email'], $email_content);
	} else {
		$email_content = str_replace('%%CLIENT_NAME%%', isset($client_contacts[$client_contact]['name']) ? $client_contacts[$client_contact]['name'] : '', $email_content);
		$email_content = str_replace('%%CLIENT_EMAIL%%', isset($client_contacts[$client_contact]['name']) ? $client_contacts[$client_contact]['email'] : '', $email_content);
	}
	$subject = pto_replacement_patterns($subject, $invoice_id, 'invoice');
	$message = pto_replacement_patterns($email_content, $invoice_id, 'invoice');
	$attachments = array();
	$pdf_attach = get_option('client_invoice_email_attach');
	if(!empty($pdf_attach)) {
		$pdf = pto_generate_pdf_invoice($invoice_id, $invoice_no);
		$attachments[] = $pdf;
	}
	if(!empty($invoice_project_id)) {
		$current_user = wp_get_current_user();
		$current_user = $current_user->display_name;
		$project_progress = get_post_meta($invoice_project_id, 'project_progress', true);
		$project_progress[] = array(
			'update' => __('Invoice Sent', 'cqpim') . ': ' . $invoice_no,
			'date' => current_time('timestamp'),
			'by' => $current_user,
		);
		update_post_meta($invoice_project_id, 'project_progress', $project_progress );
	}
	if( $to && $subject && $message ){
		if( pto_send_emails( $to, $subject, $message, '', $attachments, 'accounts' ) ) :
			if(!empty($pdf)) {
				unlink($pdf);
			}
			if(!empty($ajax_post)) {
				$current_user = wp_get_current_user();
				$current_user_obj = $current_user->display_name;
			} else {
				$current_user_obj = __('System', 'cqpim');
				$current_user = get_user_by('id', 1);
			}
			$invoice_details = get_post_meta($invoice_id, 'invoice_details', true);
			$invoice_details['sent_details'] = array(
					'date' 	=> current_time('timestamp'),
					'by'	=> $current_user_obj,
					'to'    => $to,
			);
			$invoice_details['sent'] = true;
			update_post_meta($invoice_id, 'invoice_details', $invoice_details );
			pto_add_team_notification($client_id, $current_user->ID, $invoice_id, 'invoice_sent', $ctype = 'invoice');
			if($type == 'send' && $terms != 1) {
				$after_send_reminder = get_option('client_invoice_after_send_remind_days');
				$before_due_reminder = get_option('client_invoice_before_terms_remind_days');
				$overdue_reminder = get_option('client_invoice_after_terms_remind_days');
				if(!empty($after_send_reminder)) {
					$invoice_date_string = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
					$send_date = strtotime('+' . $after_send_reminder . ' days', $invoice_date_string);
					$args = array(
						'invoice_id' => $invoice_id,
						'type' => 'reminder',
						'which' => 'after_send'
					);
					wp_schedule_single_event( $send_date, 'pto_invoice_reminders', $args );
				}
				if(!empty($before_due_reminder)) {
					$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
					$send_date = strtotime('-' . $before_due_reminder . ' days', $due);
					$now = time();
					if($send_date > $now) {
						$args = array(
							'invoice_id' => $invoice_id,
							'type' => 'reminder',
							'which' => 'before_due'
						);
						wp_schedule_single_event( $send_date, 'pto_invoice_reminders', $args );	
					}						
				}
				if(!empty($overdue_reminder)) {
					$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
					$send_date = strtotime('+' . $overdue_reminder . ' days', $due);
					$args = array(
						'invoice_id' => $invoice_id,
						'type' => 'overdue'
					);
					wp_schedule_single_event( $send_date, 'pto_invoice_reminders', $args );					
				}
			}
			if(!empty($ajax_post)) {
				$return =  array( 
					'error' 	=> false,
					'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Email sent successfully...', 'cqpim') . '</div>'
				);
				header('Content-type: application/json');
				echo json_encode($return);	
				exit();
			};			
		else :
			if(!empty($ajax_post)) {
				$return =  array( 
					'error' 	=> true,
					'errors' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('There was a problem sending the email, please try again.', 'cqpim') . '</div>'
				);
				header('Content-type: application/json');
				echo json_encode($return);
				exit();
			};
		endif;	
	} else {
		if(!empty($ajax_post)) {
			$return =  array( 
				'error' 	=> true,
				'errors' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('There was a problem sending the email, please try again.', 'cqpim') . '</div>'
			);
			header('Content-type: application/json');
			echo json_encode($return);
			exit();
		};
	}
	if(!empty($ajax_post)) {
		exit();
	};
}
function pto_generate_pdf_invoice($invoice_id, $invoice_no) {
	$invoice = get_post($invoice_id);
	$url = get_the_permalink($invoice_id) . '?pwd=' . md5($invoice->post_password) . '&page=print';
	$url = urldecode($url);
	$ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_USERAGENT, $ua);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$html = curl_exec($ch);	
	curl_close($ch);
	require_once(PTO_PATH . '/assets/mpdf/autoload.php');
	$upload_dir = wp_upload_dir();
	$invoice_template = get_option('cqpim_invoice_template');
	if($invoice_template == 1) {
		$mpdf = new \Mpdf\Mpdf(['tempDir' => trailingslashit( $upload_dir['basedir'] ) . 'pto-uploads', 'mode' => 'c']);
	}
	if($invoice_template == 2 || $invoice_template == 3) {
		$mpdf = new \Mpdf\Mpdf([
			'tempDir' => trailingslashit( $upload_dir['basedir'] ) . 'pto-uploads', 
			'mode' => 'c', 
			'format' => 'A4'
		]);
	}
	$mpdf->AddPageByArray(array(
		'orientation' => 'P',
		'mgl' => '0',
		'mgr' => '0',
		'mgt' => '0',
		'mgb' => '0',
		'mgh' => '0',
		'mgf' => '0',
	));
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->allow_charset_conversion=true;
	$mpdf->charset_in='UTF-8';
	$mpdf->setBasePath($url);
	$mpdf->WriteHTML($html);
	$invoice_no = str_replace('/','-', $invoice_no);
	$filename = trailingslashit( $upload_dir['basedir'] ) . "pto-uploads/invoice_$invoice_no.pdf";
	$mpdf->Output($filename,'F');
	return $filename;
}
add_action( 'wp_trash_post', 'pto_clear_invoice_reminders_on_trash' );
function pto_clear_invoice_reminders_on_trash( $post_id ){
	global $post;
	$id = isset($post->ID) ? $post->ID : '';
	$type = isset($post->post_type) ? $post->post_type : '';
	if($type == 'cqpim_invoice') {
		$args = array(
			'invoice_id' => "$id",
			'type' => 'reminder',
			'which' => 'before_due'
		);
		wp_clear_scheduled_hook( 'pto_invoice_reminders', $args );
		$args = array(
			'invoice_id' => "$id",
			'type' => 'reminder',
			'which' => 'after_send'
		);
		wp_clear_scheduled_hook( 'pto_invoice_reminders', $args );
		$args = array(
			'invoice_id' => "$id",
			'type' => 'overdue'
		);
		wp_clear_scheduled_hook( 'pto_invoice_reminders', $args );
	}
}
add_action( "wp_ajax_pto_mark_invoice_paid", "pto_mark_invoice_paid");
function pto_mark_invoice_paid($invoice_id, $current_user = 0, $amount = 0) {
	if(!empty($_POST['invoice_id'])) {
		$invoice_id = $_POST['invoice_id'];
		$amount = $_POST['amount'];
		$notes = $_POST['notes'];
		$date = $_POST['date'];
		$date = pto_convert_date($date);
		$ajax_post = true;
	}
	if(empty($date)) {
		$date = current_time('timestamp');
	}
	$amount = preg_replace('/[^0-9\.]/', '', $amount);
	if(empty($current_user)) {
		$user = wp_get_current_user();				
		$current_user = $user->display_name;
	}
	$invoice_totals = get_post_meta($invoice_id, 'invoice_totals', true);
	$vat_rate = get_option('sales_tax_rate');
	$total = isset($invoice_totals['total']) ? $invoice_totals['total'] : 0;
	$invoice_payments = get_post_meta($invoice_id, 'invoice_payments', true);
	$invoice_payments = $invoice_payments&&is_array($invoice_payments)?$invoice_payments:array();
	$invoice_details = get_post_meta($invoice_id, 'invoice_details', true);
	$invoice_project = isset($invoice_details['project_id']) ? $invoice_details['project_id'] : '';
	if(empty($invoice_details['paid'])) {
		if(empty($invoice_payments)) {
			$invoice_payments = array();
		}
		if(empty($notes)) {
			$notes = '';
		}
		$invoice_payments[] = array(
			'date' => $date,
			'amount' => $amount,
			'notes' => $notes,
			'by' => $current_user,
		);
		if(!empty($invoice_project)) {
			$invoice_object = get_post($invoice_id);
			$project_progress = get_post_meta($invoice_project, 'project_progress', true);
			$project_progress[] = array(
				'update' => __('Invoice payment received', 'cqpim') . ': ' . $invoice_object->post_title,
				'date' => current_time('timestamp'),
				'by' => $current_user,
			);
			update_post_meta($invoice_project, 'project_progress', $project_progress );
		}
		foreach($invoice_payments as $key => $payment) {
			$amount = isset($payment['amount']) ? $payment['amount'] : 0;
			$amount = preg_replace("/[^0-9\.]/", '', $amount);
			$total = $total - $amount;
			$invoice_payments[$key]['amount'] = $amount;
		}
		update_post_meta($invoice_id, 'invoice_payments', $invoice_payments);
		if($total <= 0 && empty($ajax_post)) {
			$invoice_details = get_post_meta($invoice_id, 'invoice_details', true);
			$invoice_details['paid_details'] = array(
					'date' 	=> current_time('timestamp'),
					'by'	=> $current_user,
			);
			$invoice_details['paid'] = true;
			update_post_meta($invoice_id, 'invoice_details', $invoice_details );
			$invoice_project = get_post_meta($invoice_id, 'invoice_project', true);
			if($invoice_project) {
				$invoice_object = get_post($invoice_id);
				$project_progress = get_post_meta($invoice_project, 'project_progress', true);
				$project_progress[] = array(
					'update' => __('Invoice marked as paid', 'cqpim') . ': ' . $invoice_object->post_title,
					'date' => current_time('timestamp'),
					'by' => $current_user,
				);
				update_post_meta($invoice_project, 'project_progress', $project_progress );
			}
		}
		$email_subject = get_option('client_invoice_receipt_subject');
		$email_content = get_option('client_invoice_receipt_email');
		$attachments = array();		
		$invoice_details = get_post_meta($invoice_id, 'invoice_details', true);
		$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
		$client_id = $invoice_details['client_id'];
		$client_details = get_post_meta($client_id, 'client_details', true);
		$client_main_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
		$client_contacts = get_post_meta($client_id, 'client_contacts', true);
		$client_contact = get_post_meta($invoice_id, 'client_contact', true);
		$to = array();
		if(empty($client_contacts)) {
			$client_contacts = array();
		}
		if(!empty($client_details['billing_email'])) {
			$to[] = $client_details['billing_email'];
		} else {
			if(!empty($client_contact)) {
				if($client_contact == $client_main_id) {
					$to[] = $client_details['client_email'];
				} else {
					$to[] = $client_contacts[$client_contact]['email'];
				}
			} else {
				$to[] = $client_details['client_email'];
			}
		}
		$to[] = get_option('company_sales_email');
		$to[] = get_option('company_accounts_email');
		$to = array_unique($to);
		if($client_contact == $client_main_id) {
			$email_content = str_replace('%%CLIENT_NAME%%', $client_details['client_contact'], $email_content);
			$email_content = str_replace('%%CLIENT_EMAIL%%', $client_details['client_email'], $email_content);
		} else {
			$email_content = str_replace('%%CLIENT_NAME%%', isset($client_contacts[$client_contact]['name']) ? $client_contacts[$client_contact]['name'] : '', $email_content);
			$email_content = str_replace('%%CLIENT_EMAIL%%', isset($client_contacts[$client_contact]['name']) ? $client_contacts[$client_contact]['email'] : '', $email_content);
		}		
		$email_content = pto_replacement_patterns($email_content, $invoice_id, 'invoice');
		$email_subject = pto_replacement_patterns($email_subject, $invoice_id, 'invoice');	
		$amount_tag = '%%AMOUNT%%';
		$email_content = str_replace($amount_tag, pto_calculate_currency($invoice_id, $amount), $email_content);
		foreach($to as $email) {
			pto_send_emails($email, $email_subject, $email_content, '', $attachments, 'accounts' );
		}
		if(!empty($ajax_post)) {
			$return =  array( 
				'error' 	=> false,
				'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('Sucessfully updated invoice status.', 'cqpim') . '</div>'
			);
			header('Content-type: application/json');
			echo json_encode($return);	
		};				
		if(!empty($ajax_post)) {
			exit();
		};
	}
}
add_action( "wp_ajax_pto_send_invoice_reminders", "pto_send_invoice_reminders");
add_action( "cqpim_invoice_reminders", "pto_send_invoice_reminders");
add_action( "pto_invoice_reminders", "pto_send_invoice_reminders");
function pto_send_invoice_reminders($invoice_id, $type = NULL) {
	if(isset($_POST['invoice_id'])) {
		$invoice_id = $_POST['invoice_id'];
		$type = $_POST['type'];
		$ajax_call = true;
	}
	$invoice_no = get_post_meta($invoice_id, 'invoice_id', true);
	$invoice_object = get_post($invoice_id);
	if(empty($invoice_object) || !empty($invoice_object) && $invoice_object->post_status == 'trash') {
		die;
	}
	if($type == 'overdue') {
		$email_content = get_option('client_invoice_overdue_email');
		$email_subject = get_option('client_invoice_overdue_subject');
	} else {
		$email_content = get_option('client_invoice_reminder_email');
		$email_subject = get_option('client_invoice_reminder_subject');	
	}
	$priority = get_option('client_invoice_high_priority');
	if($priority == 1) {
		add_filter('phpmailer_init','pto_update_priority_mailer');
	}
	$attachments = array();
	$pdf_attach = get_option('client_invoice_email_attach');
	if(!empty($pdf_attach)) {
		$pdf = pto_generate_pdf_invoice($invoice_id, $invoice_no);
		$attachments[] = $pdf;
	}
	$invoice_details = get_post_meta($invoice_id, 'invoice_details', true);
	$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
	$client_id = $invoice_details['client_id'];
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_main_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	$client_contact = get_post_meta($invoice_id, 'client_contact', true);
	if(empty($client_contacts)) {
		$client_contacts = array();
	}
	if(!empty($client_contact)) {
		if($client_contact == $client_main_id) {
			$to = $client_details['client_email'];
		} else {
			$to = $client_contacts[$client_contact]['email'];
		}
	} else {
		$to = $client_details['client_email'];
	}
	if($client_contact == $client_main_id) {
		$email_content = str_replace('%%CLIENT_NAME%%', $client_details['client_contact'], $email_content);
		$email_content = str_replace('%%CLIENT_EMAIL%%', $client_details['client_email'], $email_content);
	} else {
		$email_content = str_replace('%%CLIENT_NAME%%', isset($client_contacts[$client_contact]['name']) ? $client_contacts[$client_contact]['name'] : '', $email_content);
		$email_content = str_replace('%%CLIENT_EMAIL%%', isset($client_contacts[$client_contact]['name']) ? $client_contacts[$client_contact]['email'] : '', $email_content);
	}		
	$email_content = pto_replacement_patterns($email_content, $invoice_id, 'invoice');
	$email_subject = pto_replacement_patterns($email_subject, $invoice_id, 'invoice');
	if(empty($paid)) {
		if($to && $email_content && $email_subject) {
			if( pto_send_emails( $to, $email_subject, $email_content, '', $attachments, 'accounts' ) ) {			
				if(!empty($ajax_call)) {
					$return =  array( 
						'error' 	=> false,
						'message' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('The Email was sent successfully.', 'cqpim') . '</div>'
					);
					header('Content-type: application/json');
					echo json_encode($return);				
				}			
			} else {
				if(!empty($ajax_call)) {
					$return =  array( 
						'error' 	=> true,
						'errors' 	=> '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('There was a problem sending the email. Please try again.', 'cqpim') . '</div>'
					);
					header('Content-type: application/json');
					echo json_encode($return);					
				}			
			}
		} else {
			if(!empty($ajax_call)) {
				$return =  array( 
					'error' 	=> true,
					'errors' 	=> '<div class="cqpim-alert cqpim-alert-success alert-display">' . __('There was a problem sending the email. Some data is missing. Please check and try again.', 'cqpim') . '</div>'
				);
				header('Content-type: application/json');
				echo json_encode($return);			
			}
		}
	} else {
		if(!empty($ajax_call)) {
			$return =  array( 
				'error' 	=> true,
				'errors' 	=> __('ALREADY PAID!', 'cqpim')
			);
			header('Content-type: application/json');
			echo json_encode($return);			
		}		
	}
	if(!empty($ajax_call)) {
		exit();
	}
}
function pto_update_priority_mailer($mailer){
	$mailer->Priority = 1;
	return $mailer;
}
add_action( "wp_ajax_pto_delete_payment", "pto_delete_payment");
function pto_delete_payment() {
	$invoice_id = isset($_POST['invoice_id']) ? $_POST['invoice_id'] : '';
	$payment_id = isset($_POST['payment_id']) ? $_POST['payment_id'] : '';
	$payments = get_post_meta($invoice_id, 'invoice_payments', true);
	unset($payments[$payment_id]);
	update_post_meta($invoice_id, 'invoice_payments', $payments);
	$return =  array( 
		'error' 	=> false
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit;
}
add_action( "wp_ajax_pto_edit_payment", "pto_edit_payment");
function pto_edit_payment() {
	$invoice_id = isset($_POST['invoice_id']) ? $_POST['invoice_id'] : '';
	$payment_id = isset($_POST['payment_id']) ? $_POST['payment_id'] : '';
	$date = isset($_POST['date']) ? $_POST['date'] : '';
	$date = pto_convert_date($date);
	$amount = isset($_POST['amount']) ? $_POST['amount'] : '';
	$amount = preg_replace("/[^0-9\.]/","", $amount);
	$notes = isset($_POST['notes']) ? $_POST['notes'] : '';
	$payments = get_post_meta($invoice_id, 'invoice_payments', true);
	$payments[$payment_id]['amount'] = $amount;
	$payments[$payment_id]['notes'] = $notes;
	$payments[$payment_id]['date'] = $date;
	update_post_meta($invoice_id, 'invoice_payments', $payments);
	$return =  array( 
		'error' 	=> false,
		'errors' => 'none'
	);
	header('Content-type: application/json');
	echo json_encode($return);	
	exit;
}
add_action( 'current_screen', 'pto_change_invoice_status' );
function pto_change_invoice_status() {
	$screen = get_current_screen();
	$action = isset($_GET['action']) ? $_GET['action'] : '';
	if($screen->post_type == 'cqpim_invoice' && $action == 'edit') {
		$post_id = $_GET['post'];
		$post = get_post($post_id);
		$line_items = get_post_meta($post->ID, 'line_items', true);
		$tax_app = get_post_meta($post->ID, 'tax_applicable', true);
		$invoice_totals = get_post_meta($post->ID, 'invoice_totals', true);
		if($tax_app == 1) {
			$total = isset($invoice_totals['total']) ? $invoice_totals['total'] : 0;		
		} else {
			$total = isset($invoice_totals['sub']) ? $invoice_totals['sub'] : 0;		
		}
		$invoice_payments = get_post_meta($post->ID, 'invoice_payments', true);
		$invoice_details = get_post_meta($post->ID, 'invoice_details', true);
		if(empty($invoice_payments)) {
			$invoice_payments = array();
		}
		foreach($invoice_payments as $key => $payment) {
			$amount = isset($payment['amount']) ? $payment['amount'] : 0;
			$amount = preg_replace('/[^a-zA-Z0-9\.]/', '', $amount);
			$total = $total - $amount;
		}
		if($total <= 0 && empty($invoice_details['paid_details']['date']) && !empty($line_items)) {
			$user = wp_get_current_user();				
			$current_user = $user->display_name;		
			$invoice_details = get_post_meta($post->ID, 'invoice_details', true);
			$invoice_details['paid_details'] = array(
					'date' 	=> current_time('timestamp'),
					'by'	=> $current_user,
			);
			$invoice_details['paid'] = true;
			update_post_meta($post->ID, 'invoice_details', $invoice_details );
			$invoice_project = get_post_meta($post->ID, 'invoice_project', true);
			if($invoice_project) {
				$invoice_object = get_post($post->ID);
				$project_progress = get_post_meta($invoice_project, 'project_progress', true);
				$project_progress[] = array(
					'update' => __('Invoice marked as paid', 'cqpim') . ': ' . $invoice_object->post_title,
					'date' => current_time('timestamp'),
					'by' => $current_user,
				);
				update_post_meta($invoice_project, 'project_progress', $project_progress );
			}
		}
		if($total > 0) {
			$invoice_details = get_post_meta($post->ID, 'invoice_details', true);
			unset($invoice_details['paid_details']);
			unset($invoice_details['paid']);
			update_post_meta($post->ID, 'invoice_details', $invoice_details );			
		}
		if(!empty($_GET['download_pdf'])) {
			$invoice_id = get_post_meta($post->ID, 'invoice_id', true);
			$invoice = pto_generate_pdf_invoice($post->ID, $invoice_id);
			$invoice_name = basename($invoice);
			header("Content-Type: application/octet-stream");
			header("Content-Transfer-Encoding: Binary");
			header("Content-disposition: attachment; filename=\"$invoice_name\""); 
			echo readfile($invoice);
		}
	}
}
add_action( "wp_ajax_pto_update_invoice_due", "pto_update_invoice_due");
function pto_update_invoice_due() {
	$invoice_id = isset($_POST['post']) ? $_POST['post'] : '';
	$date = isset($_POST['date']) ? $_POST['date'] : '';
	if(empty($invoice_id) || empty($date)) {
		$return =  array( 
			'error' 	=> true,
			'errors' => __('Unable to edit the due date, some either the date or the Invoice ID are missing', 'cqpim'),
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit;	
	} else {
		$invoice_details = get_post_meta($invoice_id, 'invoice_details', true);
		$due = pto_convert_date($date);
		$invoice_details['terms_over'] = $due;
		$invoice_details['custom_terms'] = true;
		update_post_meta($invoice_id, 'invoice_details', $invoice_details);
		$return =  array( 
			'error' 	=> false,
		);
		header('Content-type: application/json');
		echo json_encode($return);	
		exit;	
	}	
}
function pto_get_recurring_invoices() {
	$invoices = array();
	$args = array(
		'post_type' => 'cqpim_client',
		'posts_per_page' => -1,
		'post_status' => 'private',
	);
	$clients = get_posts($args);
	foreach($clients as $client) {
		$recurring_invoices = get_post_meta($client->ID, 'recurring_invoices', true);
		if(!empty($recurring_invoices) && is_array($recurring_invoices)) {
			foreach($recurring_invoices as $key => $invoice) {
				$invoice['client_id'] = $client->ID;
				$invoice['invoice_key'] = $key;
				$invoices[] = $invoice;
			}
		}
	}
	return $invoices;
}