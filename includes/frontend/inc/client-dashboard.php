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
} ?>
<div id="cqpim-dash-sidebar-back"></div>
<div class="cqpim-dash-content">
	<div id="cqpim-dash-sidebar">
		<?php include('sidebar.php'); ?>
	</div>
	<div id="cqpim-dash-content">
		<div id="cqpim_admin_title">
			<?php
				if( isset( $_GET['page'] ) && $_GET['page'] == 'quotes'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . __('Quotes / Estimates', 'cqpim');
				} elseif( isset( $_GET['page'] ) && $_GET['page'] == 'projects'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . __('Projects', 'cqpim');
				} elseif( isset( $_GET['page'] ) && $_GET['page'] == 'faq'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . __('FAQ', 'cqpim');
				} elseif( isset( $_GET['page'] ) && $_GET['page'] == 'support'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . __('Support Tickets', 'cqpim');
				} elseif( isset( $_GET['page'] ) && $_GET['page'] == 'invoices'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . __('Invoices', 'cqpim');
				} elseif( pto_check_addon_status('subscriptions') && isset( $_GET['page'] ) && $_GET['page'] == 'subscriptions'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . pto_return_subs_text('subs');
				} elseif( pto_check_addon_status('subscriptions') && isset( $_GET['page'] ) && $_GET['page'] == 'subscription-plans'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . pto_return_subs_text('plans');
				} elseif( isset( $_GET['page'] ) && $_GET['page'] == 'quote_form'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . __('Request a Quote', 'cqpim');
				} elseif( isset( $_GET['page'] ) && $_GET['page'] == 'settings'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . __('Settings', 'cqpim');
				} elseif( isset( $_GET['page'] ) && $_GET['page'] == 'messages'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . __('Messages', 'cqpim');
				} elseif( isset( $_GET['page'] ) && $_GET['page'] == 'contacts'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . __('Contacts', 'cqpim');
				} elseif( isset( $_GET['page'] ) && $_GET['page'] == 'client-files'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . __('Client Files', 'cqpim');
				}  elseif( isset( $_GET['page'] ) && $_GET['page'] == 'add-support-ticket'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . __('Add Support Ticket', 'cqpim');
				}  elseif( isset( $_GET['page'] ) && $_GET['page'] == 'add-envato-purchase'  ) {
					echo '<a href="' . get_the_permalink($client_dash) . '">' . __('Dashboard', 'cqpim') . '</a> <i class="fa fa-circle"></i> ' . __('My Envato Items', 'cqpim');
				} elseif(empty($_GET['page'])) {
					_e('Dashboard', 'cqpim');
				}
			?>										
		</div>
		<div id="cqpim-cdash-inside">
			<?php if(!empty($payment) && $payment == true) { ?>
				<div class="cqpim-alert cqpim-alert-success alert-display">
				  <strong><?php _e('Payment Successful.', 'cqpim'); ?></strong> <?php _e('Your payment has been accepted, thank you.', 'cqpim'); ?>
				</div>			
			<?php } ?>
			<?php if(!empty($payment_error) && $payment_error == true) { ?>
				<div class="cqpim-alert cqpim-alert-danger alert-display">
				  <strong><?php _e('Payment Declined.', 'cqpim'); ?></strong> <?php echo $error_message; ?>
				</div>			
			<?php } ?>
			<?php
			if(isset( $_GET['page'] ) && $_GET['page'] == 'quotes' && get_option('enable_quotes') == 1 ) {
				include('client-dashboard/quotes-page.php');
			} if(isset( $_GET['page'] ) && $_GET['page'] == 'projects'  ) {
				include('client-dashboard/projects-page.php');
			} if(isset( $_GET['page'] ) && $_GET['page'] == 'faq'  ) {
				include('client-dashboard/faq-page.php');
			} if(isset( $_GET['page'] ) && $_GET['page'] == 'support'  ) {
				include('client-dashboard/ticket-page.php');
			} if(isset( $_GET['page'] ) && $_GET['page'] == 'invoices'  ) {
				include('client-dashboard/invoice-page.php');
			} if(pto_check_addon_status('subscriptions') && isset( $_GET['page'] ) && $_GET['page'] == 'subscriptions'  ) {
				include('client-dashboard/subs-page.php');
			} if(pto_check_addon_status('subscriptions') && isset( $_GET['page'] ) && $_GET['page'] == 'subscription-plans'  ) {
				include('client-dashboard/subs-plan-page.php');
			} if(isset( $_GET['page'] ) && $_GET['page'] == 'quote_form' && !empty($quote_form)  ) {
				include('client-dashboard/quote-form.php');
			} if(isset( $_GET['page'] ) && $_GET['page'] == 'add-envato-purchase'  ) { 
				include('client-dashboard/add-envato-purchase.php');
			} if(isset( $_GET['page'] ) && $_GET['page'] == 'add-support-ticket'  ) { 
				include('client-dashboard/add-ticket.php');
			} if(isset( $_GET['page'] ) && $_GET['page'] == 'settings'  ) {
				include('client-dashboard/client-settings.php');
			} if(isset( $_GET['page'] ) && $_GET['page'] == 'client-files'  ) {
				include('client-dashboard/client-files.php');
			} if(isset( $_GET['page'] ) && $_GET['page'] == 'contacts'  ) {
				include('client-dashboard/client-contacts.php');
			} if(isset( $_GET['page'] ) && $_GET['page'] == 'messages'  ) {
				include('client-dashboard/messages.php');
			} if(empty($_GET['page'])) {
				include('client-dashboard/dashboard.php');
			} ?>
		</div>			
	</div>
	<div class="clear"></div>
</div>
<?php include('footer_inc.php'); ?>