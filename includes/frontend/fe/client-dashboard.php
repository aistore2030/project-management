<?php include('header.php');
$payment_status = isset($_POST['payment_status']) ? strtolower($_POST['payment_status']) : '';	
$tx = isset($_GET['tx']) ? strtolower($_GET['tx']) : '';
$st = isset($_GET['st']) ? strtolower($_GET['st']) : '';
if($payment_status == 'completed' || !empty($tx) && !empty($st) && $st == 'completed') {
	$last = $_SESSION['last_invoice'];
	pto_mark_invoice_paid($last, 'PayPal', $_SESSION['payment_amount_' . $last]);
	$payment = true;
}
$twocheck = isset($_GET['credit_card_processed']) ? $_GET['credit_card_processed'] : '';
if($twocheck == 'Y') {
	$last = $_SESSION['last_invoice'];
	pto_mark_invoice_paid($last, '2Checkout', $_SESSION['payment_amount_' . $last]);
	$payment = true;
}
$stripe_token = isset($_POST['stripeToken']) ? $_POST['stripeToken'] : '';
if(!empty($stripe_token)) {
	$last = $_SESSION['last_invoice'];
	$spost_id = $_SESSION['last_invoice'];
	$spost_obj = get_post($spost_id);
	require_once(PTO_FE_PATH . '/stripe/init.php');
	\Stripe\Stripe::setApiKey(get_option('client_invoice_stripe_secret'));
	$token = $_POST['stripeToken'];
	try {		
		$charge = \Stripe\Charge::create(array(
			"amount" => $_SESSION['payment_amount_' . $last] * 100,
			"currency" => get_post_meta($spost_obj->ID, 'currency_code', true),
			"source" => $token,
			"description" => __('Invoice', 'cqpim') . ' - ' . $spost_obj->post_title)
		);
	} catch(\Stripe\Error\Card $e) {
		$stripe_response = json_decode($e->httpBody);
		$stripe_error = $stripe_response->error;
	}	
	if(!empty($stripe_error->code) && $stripe_error->code == 'card_declined') {
		$payment_error = true;
		$error_message = $stripe_error->message;
	} else {
		pto_mark_invoice_paid($last, 'Stripe', $_SESSION['payment_amount_' . $last]);			
		$payment = true;
	}
}
$stripe_source = isset($_GET['source']) ? $_GET['source'] : '';
if(!empty($stripe_source)) {
	$last = $_SESSION['last_invoice'];
	$spost_id = $_SESSION['last_invoice'];
	$spost_obj = get_post($spost_id);
	require_once(PTO_FE_PATH . '/stripe/init.php');
	\Stripe\Stripe::setApiKey(get_option('client_invoice_stripe_secret'));
	$source = $_GET['source'];
	try {
		$charge = \Stripe\Charge::create(array(
		  "amount" => $_SESSION['payment_amount_' . $last] * 100,
		  "currency" => "eur",
		  "source" => $source,
		  "description" => __('Invoice', 'cqpim') . ' - ' . $spost_obj->post_title)
		);
	} catch(\Stripe\Error\InvalidRequest $e) {
		$stripe_response = json_decode($e->httpBody);
		$stripe_error = $stripe_response->error;
	}	
	if(!empty($stripe_error->code)) {
		$payment_error = true;
		$error_message = $stripe_error->message;
	} else {
		pto_mark_invoice_paid($last, 'iDEAL', $_SESSION['payment_amount_' . $last]);			
		$payment = true;
	}
}
$client_logs = get_post_meta($assigned, 'client_logs', true);
if(empty($client_logs)) {
	$client_logs = array();
}
$now = time();
$client_logs[$now] = array(
	'user' => $user->ID,
	'page' => __('Client Dashboard', 'cqpim')
);
update_post_meta($assigned, 'client_logs', $client_logs); 
$stickets = get_option('disable_tickets');
if(empty($_GET['page'])) {
	include('client-dashboard/alerts.php');
	if(get_option('enable_quotes') == 1) {
		include('client-dashboard/quotes.php');
	}
	include('client-dashboard/projects.php');
	if(empty($stickets)) {
		include('client-dashboard/tickets.php');
	}
	if(get_option('disable_invoices') != 1) {
		include('client-dashboard/invoices.php');
	}
}
if(!empty($messaging) && isset( $_GET['page'] ) && $_GET['page'] == 'messages' ) {
	include('client-dashboard/messages.php');	
}
if(pto_check_addon_status('subscriptions') && isset( $_GET['page'] ) && $_GET['page'] == 'subscriptions'  ) {
	include('client-dashboard/subs-page.php');
} if(pto_check_addon_status('subscriptions') && isset( $_GET['page'] ) && $_GET['page'] == 'subscription-plans'  ) {
	include('client-dashboard/subs-plan-page.php');
}
if(!empty($quote_form) && isset( $_GET['page'] ) && $_GET['page'] == 'quote_form' ) {
	include('client-dashboard/form.php');	
}
if(isset( $_GET['page'] ) && $_GET['page'] == 'faq'  ) {
	include('client-dashboard/faq-page.php');
}
if(pto_check_addon_status('envato') && isset( $_GET['page'] ) && $_GET['page'] == 'add-envato-purchase'  ) {
	include('client-dashboard/envato.php');	
}
if(isset( $_GET['page'] ) && $_GET['page'] == 'add-support-ticket' && empty($stickets) ) { 
	include('client-dashboard/add-ticket.php');	
}
if(get_option('allow_client_settings') && isset( $_GET['page'] ) && $_GET['page'] == 'settings') { 
	include('client-dashboard/settings.php');	
}
if(get_option('allow_client_settings') && isset( $_GET['page'] ) && $_GET['page'] == 'client-files') { 
	include('client-dashboard/client-files.php');	
}
if(get_option('allow_client_users') && isset( $_GET['page'] ) && $_GET['page'] == 'contacts') { 
	include('client-dashboard/contacts.php');	
}
include('footer.php'); ?>