<?php 
if(empty($_GET['page'])) {
	include('invoice_cd.php');
} else if(!empty($_GET['page']) && $_GET['page'] == 'print') {
	$invoice_template = get_option('cqpim_invoice_template');
	if($invoice_template == 1) {
		include('invoice.php');
	}
	if($invoice_template == 2) {
		include('invoice2.php');
	}
	if($invoice_template == 3) {
		include('invoice3.php');
	}
}