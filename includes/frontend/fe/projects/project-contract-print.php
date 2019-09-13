<!DOCTYPE html>
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html class="ie ie9" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php wp_title(); ?></title>   
    <?php wp_head(); ?>
	<?php echo '<style>' . get_option('cqpim_dash_css') . '</style>'; ?>
</head>
	<?php
	$user = wp_get_current_user(); 
	$user_id = $user->ID;
	$logo = get_option('company_logo');
	$logo_url = isset($logo['company_logo']) ? $logo['company_logo'] : '';
	$title = get_the_title();
	$title = str_replace('Private: ', '', $title);
	$company_name = get_option('company_name');
	$company_address = get_option('company_address');
	$company_postcode = get_option('company_postcode');
	$contract_text = get_option('default_contract_text');
	$currency = get_option('currency_symbol');
	$vat = get_post_meta($post->ID, 'tax_applicable', true);
	if(!empty($vat)) {
		$vat = get_post_meta($post->ID, 'tax_rate', true);
	}
	$tax_name = get_option('sales_tax_name');
	if(!empty($vat)) {
		$vat_string = '';
	} else {
		$vat_string = '';
	}
	$project_details = get_post_meta($post->ID, 'project_details', true);
	$project_elements = get_post_meta($post->ID, 'project_elements', true);
	$type = isset($project_details['quote_type']) ? $project_details['quote_type'] : '';
	$upper_type = ucfirst($type);
	$quote_id = isset($project_details['quote_id']) ? $project_details['quote_id'] : '';
	$quote_details = get_post_meta($quote_id, 'quote_details', true);
	$project_summary = isset($project_details['project_summary']) ? $project_details['project_summary'] : '';
	$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_ids = get_post_meta($client_id, 'client_ids', true);
	if(empty($client_ids)) {
		$client_ids = array();
	}
	$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
	$client_terms = isset($client_details['invoice_terms']) ? $client_details['invoice_terms'] : '';
	$client_address = isset($client_details['client_address']) ? $client_details['client_address'] : '';
	$client_postcode = isset($client_details['client_postcode']) ? $client_details['client_postcode'] : '';
	$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
	$start_date = isset($project_details['start_date']) ? $project_details['start_date'] : '';
	$finish_date = isset($project_details['finish_date']) ? $project_details['finish_date'] : '';
	$deposit = isset($project_details['deposit_amount']) ? $project_details['deposit_amount'] : '';
	if($client_terms) {
		$invoice_terms = $client_terms;
	} else {
		$invoice_terms = get_option('company_invoice_terms');
	}
	if(in_array('cqpim_client', $user->roles)) {
	$user = wp_get_current_user();
	$client_logs = get_post_meta($client_id, 'client_logs', true);
	if(empty($client_logs)) {
		$client_logs = array();
	}
	$now = current_time('timestamp');
	$client_logs[$now] = array(
		'user' => $user->ID,
		'page' => sprintf(__('Project %1$s - %2$s (Contract Page)', 'cqpim'), get_the_ID(), $title)
	);
	update_post_meta($client_id, 'client_logs', $client_logs);
	}
	?>
<body <?php body_class(); ?>>
	<div class="quote_print" style="width:800px; margin:0 auto; background:#fff; padding:0 20px 20px;" id="content" role="main">
		<?php if(current_user_can( 'cqpim_view_project_contract' ) OR $client_user_id == $user_id OR in_array($user->ID, $client_ids)) { ?>
			<div class="cqpim-dash-item-full grid-item">
				<div>				
					<div class="quote_logo">
						<img src="<?php echo $logo_url; ?>" />
					</div>
					<div class="quote_contacts">
						<?php echo get_option('company_name'); ?><br />
						<?php _e('Tel:', 'cqpim'); ?><?php echo get_option('company_telephone'); ?><br />
						<?php _e('Email:', 'cqpim'); ?> <a href="<?php echo get_option('company_sales_email'); ?>"><?php echo get_option('company_sales_email'); ?></a>
					</div>
					<div class="clear"></div>
					<h1><?php echo $title; ?></h1>
					<div class="contract-specifics">
						<h2><?php _e('CONTRACT DOCUMENTATION', 'cqpim'); ?></h2>
						<p><strong><?php _e('This is an agreement between "us"', 'cqpim'); ?></strong></p>
						<p><?php echo $company_name; ?></p>
						<p><?php echo $company_address; ?> <?php echo $company_postcode; ?></p>
						<p><strong><?php _e('and "you"', 'cqpim'); ?></strong></p>
						<p><?php echo $client_company_name; ?></p>
						<p><?php echo $client_address; ?> <?php echo $client_postcode; ?></p>
					</div>
					<h2><?php _e('ABOUT THE PROJECT', 'cqpim'); ?></h2>
					<?php
					if($project_summary) {
						echo '<h2>' . __('Summary', 'cqpim') . '</h2>';
						echo wpautop($project_summary);
					}
					?>
					<?php 
					if($start_date || $finish_date) {
						echo '<h2>' . __('Project Dates', 'cqpim') . '</h2>';
					}
					if($start_date) {
						if(is_numeric($start_date)) { $start_date = date(get_option('cqpim_date_format'), $start_date); } else { $start_date = $start_date; }
						echo '<p>' . __('Start Date', 'cqpim') . ' - ' . $start_date . '</p>';
					}
					if($finish_date) {
						if(is_numeric($finish_date)) { $finish_date = date(get_option('cqpim_date_format'), $finish_date); } else { $finish_date = $finish_date; }
						echo '<p>' . __('Completion/Launch Date', 'cqpim') . ' - ' . $finish_date . '</p>';
					}
					?>
					<h2><?php _e('Milestones', 'cqpim'); ?></h2>
					<?php
					if(!empty($project_elements)) {
						$msordered = array();
						$i = 0;
						$mi = 0;
						foreach($project_elements as $key => $element) {
							$weight = isset($element['weight']) ? $element['weight'] : $mi;
							$msordered[$weight] = $element;
							$mi++;
						}
						ksort($msordered);
						foreach($msordered as $element) { ?>
							<div class="dd-milestone">
								<div class="dd-milestone-title">
									<span class="cqpim_button cqpim_small_button font-white bg-blue-madison nolink op rounded_2"><?php _e('Milestone', 'cqpim'); ?></span>  <span class="ms-title"><?php echo $element['title']; ?></span>
									<div class="dd-milestone-info">
										<?php if(!empty($element['cost'])) { ?>
												<?php echo pto_calculate_currency($post->ID, $element['cost']); ?>
										<?php } ?>
										<?php if(!empty($element['start'])) { ?>
											<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Start Date:', 'cqpim'); ?></strong> <?php echo date(get_option('cqpim_date_format'), $element['start']); ?>
										<?php } ?>
										<?php if(!empty($element['deadline'])) { ?>
											<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Deadline:', 'cqpim'); ?></strong> <?php echo date(get_option('cqpim_date_format'), $element['deadline']); ?>
										<?php } ?>
									</div>
									<div class="clear"></div>											
									<div class="dd-tasks">
										<?php
										$args = array(
											'post_type' => 'cqpim_tasks',
											'posts_per_page' => -1,
											'meta_key' => 'milestone_id',
											'meta_value' => $element['id'],
											'orderby' => 'date',
											'order' => 'ASC'
										);
										$tasks = get_posts($args);
										if($tasks) {
											$ti = 0;
											$ordered = array();
											$wi = 0;
											foreach($tasks as $task) {
												$task_details = get_post_meta($task->ID, 'task_details', true);
												$weight = isset($task_details['weight']) ? $task_details['weight'] : $wi;
												if(empty($task->post_parent)) {
													$ordered[$weight] = $task;
												}
												$wi++;
											}
											ksort($ordered);
											foreach($ordered as $task) {
												$task_details = get_post_meta($task->ID, 'task_details', true);
												$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
												$start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
												$description = isset($task_details['task_description']) ? $task_details['task_description'] : '';
												$weight = isset($task_details['weight']) ? $task_details['weight'] : 0;
												$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : ''; ?>
												<div class="dd-task">
													<span class="table-task cqpim_button cqpim_small_button font-white bg-grey-cascade op nolink rounded_2"><?php _e('Task', 'cqpim'); ?></span> <span class="ms-title"><?php echo $task->post_title; ?></span>
													<div class="dd-task-info">
														<?php if(!empty($start)) { ?>
															<strong><?php _e('Start Date:', 'cqpim') ?></strong> <?php echo date(get_option('cqpim_date_format'), $start); ?>
														<?php } ?>
														<?php if(!empty($task_deadline)) { ?>
															<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Deadline:', 'cqpim') ?></strong> <?php echo date(get_option('cqpim_date_format'), $task_deadline); ?>
														<?php } ?>	
														<?php if(!empty($task_est_time)) { ?>
															<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Est. Time:', 'cqpim') ?></strong> <?php echo $task_est_time; ?>
														<?php } ?>										
													</div>
													<div class="clear"></div>
													<div class="dd-subtasks">
														<?php $ti++;
														$args = array(
															'post_type' => 'cqpim_tasks',
															'posts_per_page' => -1,
															'meta_key' => 'milestone_id',
															'meta_value' => $element['id'],
															'post_parent' => $task->ID,
															'orderby' => 'date',
															'order' => 'ASC'
														);
														$subtasks = get_posts($args);
														if(!empty($subtasks)) {
															$subordered = array();
															$sti = 0;
															$ssti = 0;
															foreach($subtasks as $subtask) {
																$task_details = get_post_meta($subtask->ID, 'task_details', true);
																$weight = isset($task_details['weight']) ? $task_details['weight'] : $sti;
																$subordered[$weight] = $subtask;
																$sti++;
															}
															ksort($subordered);
															foreach($subordered as $subtask) {
																$task_details = get_post_meta($subtask->ID, 'task_details', true);
																$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
																$start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
																$description = isset($task_details['task_description']) ? $task_details['task_description'] : '';
																$sweight = isset($task_details['weight']) ? $task_details['weight'] : 0;
																$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : ''; ?>
																<div class="dd-task">
																	<span class="table-task cqpim_button cqpim_small_button font-white bg-grey-cascade op nolink rounded_2"><?php _e('Subtask', 'cqpim'); ?></span> <span class="ms-title"><?php echo $subtask->post_title; ?></span>
																	<div class="dd-task-info">
																		<?php if(!empty($start)) { ?>
																			<strong><?php _e('Start Date:', 'cqpim') ?></strong> <?php echo date(get_option('cqpim_date_format'), $start); ?>
																		<?php } ?>
																		<?php if(!empty($task_deadline)) { ?>
																			<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Deadline:', 'cqpim') ?></strong> <?php echo date(get_option('cqpim_date_format'), $task_deadline); ?>
																		<?php } ?>	
																		<?php if(!empty($task_est_time)) { ?>
																			<i class="fa fa-circle dd-circle"></i> <strong><?php _e('Est. Time:', 'cqpim') ?></strong> <?php echo $task_est_time; ?>
																		<?php } ?>										
																	</div>	
																</div>												
																<?php $ssti++;
															}
														} ?>
													</div>
												</div>
											<?php }
										}
										?>
									</div>
								</div>
							</div>
						<?php $i++; }
					}
					?>
					<h2><?php _e('Cost Breakdown', 'cqpim'); ?></h2>
					<?php
					if($project_elements) {
						echo '<table class="cqpim_table"><thead><tr>';
						echo '<th>' . __('Milestone', 'cqpim') . '</th>';
						if($type == 'estimate') {
							echo '<th>' . __('Estimated Cost', 'cqpim') . '</th>';
						} else {
							echo '<th>' . __('Cost', 'cqpim') . '</th>';
						}
						echo '</tr></thead>';
						echo '<tbody>';
						$subtotal = 0;
						foreach($msordered as $key => $element) {
							$cost = preg_replace("/[^\\d.]+/","", $element['cost']);
							if(!empty($cost)) {
							$subtotal = $subtotal + $cost;
							}
							echo '<tr><td class="qtitle">' . $element['title'] . '</td>';
							echo '<td class="qcost">' . pto_calculate_currency($post->ID, $cost) . '</td></tr>';
						}
						$project_details = get_post_meta($post->ID, 'project_details', true);
						$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
						$client_details = get_post_meta($client_id, 'client_details', true);
						$client_tax = isset($client_details['tax_disabled']) ? $client_details['tax_disabled'] : '';
						$client_stax = isset($client_details['stax_disabled']) ? $client_details['stax_disabled'] : '';	
						if(!empty($vat) && empty($client_tax)) {
							$stax_rate = get_option('secondary_sales_tax_rate');
							$total_vat = $subtotal / 100 * $vat;
							if(!empty($stax_rate)) {
								$total_stax = $subtotal / 100 * $stax_rate;
							}
							if(!empty($stax_rate) && empty($client_stax)) {
								$total = $subtotal + $total_vat + $total_stax;
							} else {
								$total = $subtotal + $total_vat;
							}
							$tax_name = get_option('sales_tax_name');
							$stax_name = get_option('secondary_sales_tax_name');
							$span = '';
							echo '<tr><td '.$span.' align="right" class="align-right"><strong>' . __('Subtotal', 'cqpim') . ': </strong></td><td class="subtotal">' . pto_calculate_currency($post->ID, $subtotal) . '</td></tr>';
							echo '<tr><td '.$span.' align="right" class="align-right"><strong>' . $tax_name . ': </strong></td><td class="subtotal">' . pto_calculate_currency($post->ID, $total_vat) . '</td></tr>';
							if(!empty($stax_rate) && empty($client_stax)) {
								echo '<tr><td '.$span.' align="right" class="align-right"><strong>' . $stax_name . ': </strong></td><td class="subtotal">' . pto_calculate_currency($post->ID, $total_stax) . '</td></tr>';
							}
							echo '<tr><td '.$span.' align="right" class="align-right"><strong>' . __('TOTAL', 'cqpim') . ': </strong></td><td class="subtotal">' . pto_calculate_currency($post->ID, $total) . '</td></tr>';
						} else {
							$span = '';
							echo '<tr><td '.$span.' align="right" class="align-right"><strong>' . __('TOTAL', 'cqpim') . ': </strong></td><td class="subtotal">' . pto_calculate_currency($post->ID, $subtotal) . '</td></tr>';	
						}
						echo '</tbody></table>'; 
					}
					if($type == 'estimate') { ?>
					<br />
					<h4><strong><?php _e('NOTE:', 'cqpim'); ?> </strong><?php _e('THIS IS AN ESTIMATE, SO THESE PRICES MAY NOT REFLECT THE FINAL PROJECT COST.', 'cqpim'); ?></h4>
					<?php } ?>
					<h2><?php _e('Payment Plan', 'cqpim'); ?></h2>
					<p><strong><?php _e('Deposit', 'cqpim'); ?></strong></p>
					<?php
					if($deposit == 'none') {
						echo '<p>' . __('We do not require an up-front deposit payment on this project. The full balance will be due on completion.', 'cqpim') . '</p>';
					} else {
						if(empty($subtotal)) {
							$subtotal = 0;
						}
						$deposit_amount = $subtotal / 100 * $deposit;
						echo '<p>';
						printf(__('We require an initial deposit payment of %1$s percent on this project which will be invoiced on acceptance.', 'cqpim'), $deposit);
						echo '</p>';
					}	
					?>
					<h2><?php _e('TERMS &amp; CONDITIONS', 'cqpim'); ?></h2>
					<?php 
					$contract = isset($project_details['default_contract_text']) ? $project_details['default_contract_text'] : '';
					$terms = get_post_meta($post->ID, 'terms', true);
					if(empty($terms)) {
						if(empty($contract)) {
							$text = get_post_meta($contract_text, 'terms', true);
							$text = pto_replacement_patterns($text, $post->ID, 'project');
							echo wpautop($text);
						} else {
							$text = get_post_meta($contract, 'terms', true);
							$text = pto_replacement_patterns($text, $post->ID, 'project');
							echo wpautop($text);			
						}
					} else {
						echo wpautop($terms);
					}
					?>
					<div id="acceptance">
						<?php
						$is_confirmed = isset( $project_details['confirmed'] ) ? $project_details['confirmed'] : '';
						if(!$is_confirmed) { ?>
						<h2><?php _e('CONTRACT ACCEPTANCE', 'cqpim'); ?></h2>
						<?php echo wpautop(get_option('contract_acceptance_text')); ?>
						<div class="quote_acceptance">
							<form id="submit-quote-conf">
								<input type="hidden" id="project_id" value="<?php the_ID(); ?>" />
								<input type="hidden" id="pm_name" value="<?php echo get_the_author_meta( 'display_name' ); ?>" />
								<input type="text" id="conf_name" name="conf_name" placeholder="<?php _e('Enter your name', 'cqpim'); ?>" required /><br />
								<input type="submit" id="accept_contract" class="cqpim_button font-white bg-blue mt-20 rounded_2" value="<?php _e('Confirm Contract', 'cqpim'); ?>" />
								<div id="messages"></div>
							</form>	
						</div>
						<?php } else { 
							$conf_by = isset($project_details['confirmed_details']['by']) ? $project_details['confirmed_details']['by'] : '';
							$conf_date = isset($project_details['confirmed_details']['date']) ? $project_details['confirmed_details']['date'] : '';
							if(is_numeric($conf_date)) { $conf_date = date(get_option('cqpim_date_format') . ' H:i', $conf_date); } else { $conf_date = $conf_date; }
							$conf_ip = isset($project_details['confirmed_details']['ip']) ? $project_details['confirmed_details']['ip'] : '';				
							?>
							<div class="cqpim-alert cqpim-alert-success alert-display">
								<h5><?php _e('THIS CONTRACT HAS BEEN CONFIRMED', 'cqpim'); ?></h5>
								<p><?php printf(__('Confirmed by %1$s @ %2$s from IP Address %3$s', 'cqpim'), $conf_by, $conf_date, $conf_ip); ?></p>
							</div>
						<?php } ?>
					</div>
					<div class="clear"></div>	
				</div>
			</div>	
		<?php } else { ?>
		<h1><?php _e('Access Denied', 'cqpim'); ?></h1>
		<?php } ?>
	</div><!-- #content -->
<?php wp_footer(); ?>
</body>
</html>
<?php exit; ?>