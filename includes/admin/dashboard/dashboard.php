<?php
if(!function_exists('register_pto_dashboard')) {		
	add_action( 'admin_menu' , 'register_pto_dashboard', 9 ); 
	function register_pto_dashboard() {
		$plugin_name = get_option('cqpim_plugin_name');
		if(empty($plugin_name)) {
			$plugin_name = 'Projectopia';
		}
		$icon = get_option('cqpim_use_default_icon');
		if(empty($icon)) {
			$adicon = PTO_PLUGIN_URL . '/img/icon.png';
		} else {
			$adicon = '';
		}
		$mypage = add_menu_page(__('My Dashboard', 'cqpim'), $plugin_name, 'cqpim_view_dashboard', 'pto-dashboard', 'pto_dashboard', $adicon, 28);
		add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
	}	
}
if(!function_exists('pto_dashboard')) {
	function pto_dashboard() {
		$assigned = pto_get_team_from_userid(); 
		$user = wp_get_current_user(); ?>
		<div class="masonry-grid">
			<div class="grid-sizer"></div>
			<?php if(in_array('administrator', $user->roles)) { 
				if(pto_get_team_from_userid($user) == false) { ?>
					<div class="cqpim-dash-item-full grid-item">
						<div class="cqpim_block cqpim-alert cqpim-alert-warning">
							<div style="padding:20px">	
								<h3><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php _e('You need to link your account to a Team Member', 'cqpim'); ?></h3>
								<p><?php _e('It would appear that the Wordpress Administrator account that you are logged in with is not related to a Team Member. In order for the plugin to work correctly, you need to add a Team Member that is linked to your WP User Account.', 'cqpim'); ?></p>
								<p><?php _e('We can do this for you though, just click Create Linked Team Member. You will then be able to add other team members or just work with this account.', 'cqpim') ?></p>
								<button style="margin-left:0" id="create_linked_team" class="cqpim_button bg-amber font-white rounded_2 left" data-uid="<?php echo $user->ID; ?>"><?php $text = __('Create Linked Team Member'); _e('Create Linked Team Member'); ?></button>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				<?php }
			} ?>
			<?php if(current_user_can('edit_cqpim_invoices')) { 
				if(get_option('disable_invoices') != 1) { ?>
					<div id="income-bm" class="cqpim-dash-item-triple grid-item">
						<div class="cqpim_block">
							<div class="cqpim_block_title">
								<div class="caption">
									<i class="fa fa-credit-card-alt font-green-sharp" aria-hidden="true"></i>
									<?php if(pto_check_addon_status('expenses')) { ?>
										<span class="caption-subject font-green-sharp sbold"><?php _e('Income / Expenditure by Month', 'cqpim'); ?> </span>
									<?php } else { ?>
										<span class="caption-subject font-green-sharp sbold"><?php _e('Income by Month', 'cqpim'); ?> </span>
									<?php } ?>
								</div>
								<div class="actions">
									<div class="income_controls">
										<?php $selected = isset($_SESSION['invoice_year']) ? $_SESSION['invoice_year'] : date('Y'); ?>
										<select id="income_control_date">
											<?php $date = date('Y'); ?>
											<option value="<?php echo $date ?>" <?php if($date == $selected) { ?> selected="selected"<?php } ?>><?php echo $date ?></option>
											<?php $date = date('Y', strtotime("-1 year")); ?>
											<option value="<?php echo $date ?>" <?php if($date == $selected) { ?> selected="selected"<?php } ?>><?php echo $date ?></option>
											<?php $date = date('Y', strtotime("-2 years")); ?>
											<option value="<?php echo $date ?>" <?php if($date == $selected) { ?> selected="selected"<?php } ?>><?php echo $date ?></option>
											<?php $date = date('Y', strtotime("-3 years")); ?>
											<option value="<?php echo $date ?>" <?php if($date == $selected) { ?> selected="selected"<?php } ?>><?php echo $date ?></option>
										</select>
										<?php $selected = isset($_SESSION['invoice_payments']) ? $_SESSION['invoice_payments'] : 'invoice'; ?>
										<select id="income_control_type">
											<?php $type = 'invoice'; ?>
											<option value="<?php echo $type ?>" <?php if($type == $selected) { ?> selected="selected"<?php } ?>><?php _e('Show by Invoice Dates', 'cqpim'); ?></option>
											<?php $type = 'payment'; ?>
											<option value="<?php echo $type ?>" <?php if($type == $selected) { ?> selected="selected"<?php } ?>><?php _e('Show by Invoice Payment Dates', 'cqpim'); ?></option>
										</select>
									</div>
								</div>
							</div>
							<h3>
							</h3>
							<div class="cqpim-dash-item-inside">
								<div id="graph_container">
									<canvas id="income_graph"></canvas>
									<?php 
									if(empty($_SESSION['invoice_year'])) { $_SESSION['invoice_year'] = date('Y'); } 
									if(empty($_SESSION['invoice_payments'])) { $_SESSION['invoice_payments'] = 'invoice'; } 
									$invoices_generated = array();
									$args = array(
										'post_type' => 'cqpim_invoice',
										'posts_per_page' => -1,
										'post_status' => 'publish'
									);
									$invoices = get_posts($args);
									$invoices_generated = array();
									if($_SESSION['invoice_payments'] == 'invoice') {
										foreach($invoices as $invoice) {
											unset($invoice_date);
											$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
											$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
											$invoice_totals = get_post_meta($invoice->ID, 'invoice_totals', true);
											$invoice_total = isset($invoice_totals['total']) ? $invoice_totals['total'] : 0;
											if(is_numeric($invoice_date)) {
												$invoice_date = date('d,m,Y', $invoice_date);
												$invoice_date = explode(',', $invoice_date);
												if(empty($invoices_generated[$invoice_date[2]][$invoice_date[1]])) {
													$invoices_generated[$invoice_date[2]][$invoice_date[1]] = 0;
												}
												$invoices_generated[$invoice_date[2]][$invoice_date[1]] = $invoices_generated[$invoice_date[2]][$invoice_date[1]] + $invoice_total;
											}
										}
									} else {
										foreach($invoices as $invoice) {
											$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
											$invoice_payments = get_post_meta($invoice->ID, 'invoice_payments', true);
											if(!empty($invoice_payments)) {
												foreach($invoice_payments as $payment) {
													$invoice_date = date('d,m,Y', $payment['date']);
													$invoice_date = explode(',', $invoice_date);
													if(empty($invoices_generated[$invoice_date[2]][$invoice_date[1]])) {
														$invoices_generated[$invoice_date[2]][$invoice_date[1]] = 0;
													}
													$invoices_generated[$invoice_date[2]][$invoice_date[1]] = $invoices_generated[$invoice_date[2]][$invoice_date[1]] + $payment['amount'];
												}
											}
										}							
									}
									$data = isset($invoices_generated[$_SESSION['invoice_year']]) ? $invoices_generated[$_SESSION['invoice_year']] : '';
									$amounts = array();
									$months = array('01','02','03','04','05','06','07','08','09','10','11','12');
									foreach($months as $month) {
										if(empty($data[$month])) {
											$data[$month] = 0;
										}
									}
									$data = is_array($data)?$data:array();
									ksort($data);
									foreach($data as $key => $month) {
										$amounts[] = $month;
									}
									$data = implode(', ', $amounts);
									if(pto_check_addon_status('expenses')) {
										if(empty($_SESSION['invoice_year'])) { $_SESSION['invoice_year'] = date('Y'); } 
										$invoices_generated = array();
										$args = array(
											'post_type' => 'cqpim_expense',
											'posts_per_page' => -1,
											'post_status' => 'private',
										);
										$invoices = get_posts($args);
										$invoices_generated = array();
										foreach($invoices as $invoice) {
											unset($auth);
											$invoice_date = $expense_date = get_post_meta($invoice->ID, 'expense_date', true);
											$invoice_totals = get_post_meta($invoice->ID, 'invoice_totals', true); 
											$invoice_total = isset($invoice_totals['total']) ? $invoice_totals['total'] : 0;
											$invoice_date = date('d,m,Y', $invoice_date);
											$invoice_date = explode(',', $invoice_date);
											$auth = get_post_meta($invoice->ID, 'auth_active', true);
											$auth_limit = get_option('cqpim_expense_auth_limit');
											$authorised = get_post_meta($invoice->ID, 'authorised', true);
											if(empty($invoices_generated[$invoice_date[2]][$invoice_date[1]])) {
												$invoices_generated[$invoice_date[2]][$invoice_date[1]] = 0;
											}
											if(empty($auth) || !empty($auth) && !empty($authorised) && $authorised == 1 || !empty($auth) && empty($authorised) && !empty($auth_limit) && $auth_limit > $invoice_total) {							
												$invoices_generated[$invoice_date[2]][$invoice_date[1]] = $invoices_generated[$invoice_date[2]][$invoice_date[1]] + $invoice_total;
											}
										}
										$data2 = isset($invoices_generated[$_SESSION['invoice_year']]) ? $invoices_generated[$_SESSION['invoice_year']] : '';
										$amounts = array();
										$months = array('01','02','03','04','05','06','07','08','09','10','11','12');
										foreach($months as $month) {
											if(empty($data2[$month])) {
												$data2[$month] = 0;
											}
										}
										$data2 = is_array($data2)?$data2:array();
										ksort($data2);
										foreach($data2 as $key => $month) {
											$amounts[] = $month;
										}
										$data2 = implode(', ', $amounts);										
									}
									?>
									<script>
										jQuery(document).ready(function() {
											var ctx = document.getElementById("income_graph");
											ctx.height = 150;
											var income_graph = new Chart(ctx, {
												type: 'bar',
												responsive: false,
												maintainAspectRatio: false,
												data: {
													labels: ["<?php _e('January', 'cqpim'); ?>", "<?php _e('February', 'cqpim'); ?>", "<?php _e('March', 'cqpim'); ?>", "<?php _e('April', 'cqpim'); ?>", "<?php _e('May', 'cqpim'); ?>", "<?php _e('June', 'cqpim'); ?>", "<?php _e('July', 'cqpim'); ?>", "<?php _e('August', 'cqpim'); ?>", "<?php _e('September', 'cqpim'); ?>", "<?php _e('October', 'cqpim'); ?>", "<?php _e('November', 'cqpim'); ?>", "<?php _e('December', 'cqpim'); ?>"],
													datasets: [{
														label: '<?php _e('Income by Month', 'cqpim'); ?>: <?php echo $_SESSION['invoice_year']; ?>',
														data: [<?php echo $data; ?>],
														backgroundColor: [
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
														],
														borderColor: [
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
															'rgb(59,63,81)',
														],
														borderWidth: 1
												<?php if(pto_check_addon_status('expenses')) { ?>}, {
														label: '<?php _e('Expenses by Month', 'cqpim'); ?>: <?php echo $_SESSION['invoice_year']; ?>',
														data: [<?php echo $data2; ?>],
														backgroundColor: [
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
														],
														borderColor: [
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
															'rgb(54,198,211)',
														],
														borderWidth: 1
													<?php } ?>}]
												},
												options: {
													scales: {
														yAxes: [{
															ticks: {
																beginAtZero:true
															}
														}]
													}
												}
											});		
										});
									</script>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
			<div class="cqpim-dash-item-double grid-item tasks-box">
				<div class="cqpim_block">
					<div class="cqpim_block_title">
						<div class="caption">
							<i class="fa fa-th font-green-sharp" aria-hidden="true"></i>
							<span class="caption-subject font-green-sharp sbold"> <?php _e('My Active Projects', 'cqpim'); ?></span>
						</div>
						<?php if(current_user_can('cqpim_create_new_project') && current_user_can('publish_cqpim_projects')) { ?>
							<div class="actions">
								<a class="cqpim_button cqpim_small_button border-green-sharp font-green-sharp rounded_2 sbold" href="<?php echo admin_url(); ?>post-new.php?post_type=cqpim_project"><?php _e('Add Project', 'cqpim') ?></a>
							</div>
						<?php } ?>
					</div>
					<?php
					$args = array(
						'post_type' => 'cqpim_project',
						'posts_per_page' => -1,
						'post_status' => 'private'
					);
					$projects = get_posts($args);
					$index = 0;
					if(!empty($projects)) {
						echo '<ul id="dash-project-list">';
						foreach($projects as $project) {
							$edit_url = get_edit_post_link($project->ID);
							$title = get_the_title($project->ID); 
							$project_details = get_post_meta($project->ID, 'project_details', true);
							$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
							$closed = isset($project_details['closed']) ? $project_details['closed'] : '';
							$sent = isset($project_details['sent']) ? $project_details['sent'] : '';
							$confirmed = isset($project_details['confirmed']) ? $project_details['confirmed'] : '';
							$signoff = isset($project_details['signoff']) ? $project_details['signoff'] : '';
							$contract_status = get_post_meta($project->ID, 'contract_status', true);
							if(empty($closed)) {
								if(!empty($client_id)) {
									if(!empty($signoff)) {
										$project_status = '<span class="status font-white bg-green upper cqpim_button cqpim_xs_button nolink op rounded_2">' . __('Signed Off', 'cqpim') . '</span>';
									} else {
										if($contract_status == 1) {
											$checked = get_option('enable_project_contracts'); 
											if(!empty($checked)) {
												if(!empty($confirmed)) {
													$project_status = '<span class="status font-white bg-blue upper cqpim_button cqpim_xs_button nolink op rounded_2">' . __('In Progress', 'cqpim') . '</span>';
												} else {
													if(!empty($sent)) {
														$project_status = '<span class="status font-white bg-amber upper cqpim_button cqpim_xs_button nolink op rounded_2">' . __('Contract Sent', 'cqpim') . '</span>';
													} else {
														$project_status = '<span class="status font-white bg-red upper cqpim_button cqpim_xs_button nolink op rounded_2">' . __('Contract Not Sent', 'cqpim') . '</span>';
													}
												}
											} else {
												$project_status = '<span class="status font-white bg-blue upper cqpim_button cqpim_xs_button nolink op rounded_2">' . __('In Progress', 'cqpim') . '</span>';
											}
										} else {
											$project_status = '<span class="status font-white bg-blue upper cqpim_button cqpim_xs_button nolink op rounded_2">' . __('In Progress', 'cqpim') . '</span>';											
										}
									}
								} else {
									$project_status = '<span class="status font-white bg-blue upper cqpim_button cqpim_xs_button nolink op rounded_2">' . __('In Progress', 'cqpim') . '</span>';
								}
								$project_elements = get_post_meta($project->ID, 'project_elements', true);
								$project_contributors = get_post_meta($project->ID, 'project_contributors', true);
								$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
								$project_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
								$client_details = get_post_meta($client_id, 'client_details', true);
								$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
								$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
								if(!$client_company_name) {
									$client_company_name = $project->post_title;
								}
								$finish_date = isset($project_details['finish_date']) ? $project_details['finish_date'] : '';	
								if(!empty($finish_date)) {
									$finish_date = date(get_option('cqpim_date_format'), $finish_date);
								} else {
									$finish_date = __('No Deadline', 'cqpim');
								}
								$task_count = 0;
								$task_total_count = 0;
								$task_complete_count = 0;
								if(!empty($project_elements)) {
									foreach ($project_elements as $element) {
										$args = array(
											'post_type' => 'cqpim_tasks',
											'posts_per_page' => -1,
											'meta_key' => 'milestone_id',
											'meta_value' => $element['id'],
											'orderby' => 'date',
											'order' => 'ASC'
										);
										$tasks = get_posts($args);	
										foreach($tasks as $task) {
											$task_total_count++;
											$task_details = get_post_meta($task->ID, 'task_details', true);
											$status = isset($task_details['status']) ? $task_details['status']: '';
											if($status != 'complete') {
												$task_count++;
											}
											if($status == 'complete') {
												$task_complete_count++;
											}
										}
									}
								}
								if($task_total_count !== 0) {
									$pc_per_task = 100 / $task_total_count;
									$pc_complete = $pc_per_task * $task_complete_count;
								} else {
									$pc_complete = 0;
								}
								if(current_user_can('cqpim_view_all_projects')) { $index++; ?>
									<li class="project">
										<script>
											jQuery(document).ready(function() {
												jQuery( "#progressbar-<?php echo $project->ID; ?>" ).progressbar({
													value: <?php echo number_format((float)$pc_complete, 2, '.', ''); ?>
												});
											});
										</script>
										<div class="title">
											<a class="font-green-sharp sbold nodec" href="<?php echo $edit_url; ?>"><?php if(!empty($project->post_title)) { echo $project->post_title; } else { _e('Untitled', 'cqpim'); } ?></a>
											<?php echo $project_status; ?>
										</div>
										<div class="progress">
											<div class="border-green-sharp bg-white" id="progressbar-<?php echo $project->ID; ?>"></div>
										</div>
										<ul class="project_stats">
											<li><span class="project_stat_head"><?php _e('Open Tasks: ' , 'cqpim') ?></span><span class="project_stat"><?php echo $task_count; ?></span></li>
											<li><span class="project_stat_head"><?php _e('Complete: ' , 'cqpim') ?></span><span class="project_stat"><?php echo number_format((float)$pc_complete, 2, '.', ''); ?>%</span></li>
											<li><span class="project_stat_head"><?php _e('Deadline: ' , 'cqpim') ?></span><span class="project_stat"><?php echo $finish_date; ?></span></li>
										</ul>
									</li>
								<?php
								} else {
									$access = false;
									$project_contributors = get_post_meta($project->ID, 'project_contributors', true);
									if(empty($project_contributors)) {
										$project_contributors = array();
									}
									foreach($project_contributors as $contributor) {
										if(!empty($contributor['team_id']) && $assigned == $contributor['team_id']) {
											$access = true;
										}
									}								
									if($access == true) { $index++; ?>
										<li>
											<script>
												jQuery(document).ready(function() {
													jQuery( "#progressbar-<?php echo $project->ID; ?>" ).progressbar({
														value: <?php echo number_format((float)$pc_complete, 2, '.', ''); ?>
													});
												});
											</script>
											<div class="title">
												<a class="font-green-sharp sbold nodec" href="<?php echo $edit_url; ?>"><?php if(!empty($project->post_title)) { echo $project->post_title; } else { _e('Untitled', 'cqpim'); } ?></a>
												<?php echo $project_status; ?>
											</div>
											<div class="progress">
												<div class="border-green-sharp bg-white" id="progressbar-<?php echo $project->ID; ?>"></div>
											</div>
											<ul class="project_stats">
												<li><span class="project_stat_head"><?php _e('Open Tasks: ' , 'cqpim') ?></span><span class="project_stat"><?php echo $task_count; ?></span></li>
												<li><span class="project_stat_head"><?php _e('Complete: ' , 'cqpim') ?></span><span class="project_stat"><?php echo number_format((float)$pc_complete, 2, '.', ''); ?>%</span></li>
												<li><span class="project_stat_head"><?php _e('Deadline: ' , 'cqpim') ?></span><span class="project_stat"><?php echo $finish_date; ?></span></li>
											</ul>
										</li>
										<?php
									}
								}
							} 
						}
					}
					echo '</ul>';
						if($index == 0) { ?>
							<div class="cqpim-dash-item-inside">
								<div style="padding:20px">
									<h4 style="margin:0"><?php _e('Nothing Here!', 'cqpim'); ?></h4>
									<span><?php _e('You have not been assigned to any open projects...', 'cqpim'); ?></span>	
								</div>	
							</div>
						<?php }
					?>
				</div>
			</div>
			<?php if(pto_check_addon_status('bugs') && current_user_can('cqpim_view_bugs')) { ?>
				<div class="cqpim-dash-item-triple grid-item">
					<?php pto_populate_dashboard_bugs(); ?>
				</div>
			<?php } ?>
			<div class="cqpim-dash-item-triple grid-item">
				<div class="cqpim_block">
					<div class="cqpim_block_title">
						<div class="caption">
							<i class="fa fa-pencil-square-o font-green-sharp" aria-hidden="true"></i>
							<span class="caption-subject font-green-sharp sbold"><?php _e('My Open Tasks (Assigned or Watching)', 'cqpim'); ?> </span>
						</div>
						<div class="actions">
							<a class="cqpim_button cqpim_small_button border-green-sharp font-green-sharp rounded_2 sbold" href="<?php echo admin_url(); ?>post-new.php?post_type=cqpim_tasks"><?php _e('Add Task', 'cqpim') ?></a>
						</div>
					</div>
					<?php 
					$sess_status = isset($_SESSION['task_status']) ? $_SESSION['task_status'] : array('pending', 'progress');
					$args = array(
						'post_type' => 'cqpim_tasks',
						'posts_per_page' => -1,
						'meta_query'        => array(
							'relation'  => 'OR',
							array(
								'key'       => 'owner',
								'value'     => $assigned,
								'compare'   => '='
							),
							array(
								'key'       => 'task_watchers',
								'value'     => $assigned,
								'compare'   => 'LIKE'
							)
						)
					);				
					$tasks = get_posts($args);
					$own_tasks = array();
					foreach($tasks as $task) {
						$active = get_post_meta($task->ID, 'active', true);
						$task_details = get_post_meta($task->ID, 'task_details', true);
						$owner = get_post_meta($task->ID, 'owner', true);
						$watchers = get_post_meta($task->ID, 'task_watchers', true);
						if(empty($watchers)) {
							$watchers = array();
						}
						$task_status = isset($task_details['status']) ? $task_details['status'] : '';					
						if(!empty($active) && $task_status != 'complete' && $owner == $assigned || !empty($active) && $task_status != 'complete' && in_array($assigned, $watchers)) {
							$own_tasks[] = $task;
						}
					}
					if(!empty($own_tasks)) { 
						$ordered = array();
						foreach($own_tasks as $task) {
							$task_details = get_post_meta($task->ID, 'task_details', true);
							$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
							if(!empty($task_deadline)) {
								$ordered[$task_deadline] = $task;
							}
						}
						ksort($ordered);
						foreach($own_tasks as $task) {
							$task_details = get_post_meta($task->ID, 'task_details', true);
							$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
							if(empty($task_deadline)) {
								$ordered[] = $task;
							}
						}
						?>
						<table class="datatable_style dataTable" data-ordering="false" data-rows="10">
							<thead>
								<th><?php _e('Task Title', 'cqpim'); ?></th>
								<th><?php _e('Project / Ticket', 'cqpim'); ?></th>
								<th><?php _e('Deadline', 'cqpim'); ?></th>
							</thead>
							<tbody>
								<?php $styles = array(); foreach($ordered as $task) { 
									$task_details = get_post_meta($task->ID, 'task_details', true); 
									$owner = get_post_meta($task->ID, 'owner', true); 
									$task_owner = get_post_meta($task->ID, 'owner', true);
									$client_check = preg_replace('/[0-9]+/', '', $task_owner);
									if($client_check == 'C') {
										$client = true;
									} else {
										$client = false;
									}										
									if($task_owner) {
										if($client == true) {
											$id = preg_replace("/[^0-9,.]/", "", $task_owner);
											$client_object = get_user_by('id', $id);
											$task_owner = $client_object->display_name;
										} else {
											$team_details = get_post_meta($task_owner, 'team_details', true);
											$team_name = isset($team_details['team_name']) ? $team_details['team_name']: '';
											if(!empty($team_name)) {
												$task_owner = $team_name;
											}
										}
									} else {
										$task_owner = '';
									}
									$team_details = get_post_meta($owner, 'team_details', true);
									$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
									$project = get_post_meta($task->ID, 'project_id', true); 
									$active = get_post_meta($task->ID, 'active', true);
									$project_details = get_post_meta($project, 'project_details', true);
									$project_object = get_post($project);
									$project_ref = isset($project_object->post_title) ? $project_object->post_title : '';
									$project_url = get_edit_post_link($project);
									$task_status = isset($task_details['status']) ? $task_details['status'] : '';
									$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
									$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '0';
									if(!is_numeric($task_deadline)) {
										$str_deadline = str_replace('/','-', $task_deadline);
										$str_deadline = str_replace('.','-', $task_deadline);
										$deadline_stamp = strtotime($str_deadline);
									} else {
										$deadline_stamp = $task_deadline;
									}
									$time_spent = get_post_meta($task->ID, 'task_time_spent', true);
									$total = (int) 0;
									if($time_spent) {
										foreach($time_spent as $key => $time) {
											$total = $total + $time['time'];
										}
										$total = str_replace(',','.', $total);
										$time_split = explode('.', $total);
										if(!empty($time_split[1])) {
											$minutes = '0.' . $time_split[1];
										} else {
											$minutes = '0';
											$time_split[1] = '0';
										}
										$minutes = $minutes * 60;
										$minutes = number_format((float)$minutes, 0, '.', '');
										if($time_split[0] > 1) {
											$hours  = __('hours', 'cqpim');
										} else {
											$hours = __('hour', 'cqpim');
										}
										$time =  '<span><strong>' . number_format((float)$total, 2, '.', '') . ' ' . __('hours', 'cqpim') . '</strong> (' . $time_split[0] . ' ' . $hours . ' + ' . $minutes . ' ' . __('minutes', 'cqpim') . ')</span>';
									} else {
										$time =  '<span>0</span>';
									}
									$now = time();
									if($task_status != 'complete') {
										if($deadline_stamp && $now > $deadline_stamp) {
											$progress_class = 'red';
											$milestone_status_string = __('OVERDUE', 'cqpim') . ' - ' . $task_pc;
										} else {
											$milestone_status_string = isset($task_details['status']) ? $task_details['status'] : '';
											if(!$milestone_status_string || $milestone_status_string == 'pending') {
												$progress_class = 'amber';
												$milestone_status_string = __('Pending', 'cqpim') . ' - ' . $task_pc;
											} else if($milestone_status_string == 'on_hold') {
												$progress_class = 'green';
												$milestone_status_string = __('On Hold', 'cqpim') . ' - ' . $task_pc;
											} else if($milestone_status_string == 'progress') {
												$progress_class = 'green';
												$milestone_status_string = __('In Progress', 'cqpim') . ' - ' . $task_pc;
											}
										}
									} else {
										$milestone_status_string = __('Complete', 'cqpim') . ' - ' . $task_pc;
									}	
									if(empty($progress_class)) {
										$progress_class = 'green';
									}
									if(!empty($task->post_parent)) {
										$parent_object = get_post($task->post_parent);
									}
									?>
									<tr<?php if(pto_is_task_overdue($task->ID) == 1) { ?> class="overdue"<?php } ?>>
										<td><span class="table-task font-blue-madison border-blue-madison cqpim_button cqpim_xs_button nolink op"><?php if(empty($task->post_parent)) { _e('Task', 'cqpim'); } else { _e('Subtask', 'cqpim'); } ?></span>&nbsp;&nbsp;&nbsp;<span class="cqpim_mobile"><?php _e('Title:', 'cqpim'); ?></span> <a href="<?php echo get_edit_post_link($task->ID); ?>"><?php echo $task->post_title; ?></a> <?php if(!empty($task->post_parent)) { ?> <br /> <?php _e('Parent Task:', 'cqpim'); ?> <a href="<?php echo get_edit_post_link($parent_object->ID); ?>"><?php echo get_the_title($parent_object->ID); ?></a><?php } ?></td>
										<?php if(empty($project_ref)) { ?>
											<td><span class="cqpim_mobile"><?php _e('Project / Ticket:', 'cqpim'); ?></span> <?php _e('Ad-Hoc Task', 'cqpim'); ?></td>
										<?php } else { 
											$type = isset($project_object->post_type) ? $project_object->post_type : ''; ?>
											<td><span class="cqpim_mobile"><?php _e('Project / Ticket:', 'cqpim'); ?></span> <?php if($type == 'cqpim_project') { _e('Project: ', 'cqpim'); } else { _e('Ticket: ', 'cqpim'); } ?><a href="<?php echo $project_url; ?>"><?php echo $project_ref; ?></td>
										<?php } ?>
										<td><span class="cqpim_mobile"><?php _e('Deadline:', 'cqpim'); ?></span> <?php if(is_numeric($task_deadline)) { echo date(get_option('cqpim_date_format'), $task_deadline); } else { echo $task_deadline; } ?></td>
									</tr>
								<?php 
								} ?>
							</tbody>
						</table>
					<?php } else { ?> 
						<div style="padding:20px">						
								<h4 style="margin:0"><?php _e('Nothing Here!', 'cqpim'); ?></h4>
								<span><?php _e('No tasks to show...', 'cqpim'); ?></span>						
						</div>				
					<?php } ?>
				</div>
			</div>
			<div class="cqpim-dash-item-double grid-item tasks-box">
				<div class="cqpim_block">
					<div class="cqpim_block_title">
						<div class="caption">
							<i class="fa fa-tasks font-green-sharp" aria-hidden="true"></i>
							<span class="caption-subject font-green-sharp sbold"><?php _e('Project Updates', 'cqpim'); ?> </span>
						</div>
					</div>
					<?php
					$avatar = get_option('cqpim_disable_avatars');
					$args = array(
						'post_type' => 'cqpim_project',
						'posts_per_page' => -1,
						'post_status' => 'private'
					);
					$projects = get_posts($args);
					$index = 0;
					$updates = array();
					if(!empty($projects)) {
						foreach($projects as $project) {
							$access = false;
							$edit_url = get_edit_post_link($project->ID);
							$title = get_the_title($project->ID); 
							$project_details = get_post_meta($project->ID, 'project_details', true);
							$closed = isset($project_details['closed']) ? $project_details['closed'] : '';
							$project_progress = get_post_meta($project->ID, 'project_progress', true);
							if(empty($project_progress)) {
								$project_progress = array();
							}
							$project_contributors = get_post_meta($project->ID, 'project_contributors', true);
							if(current_user_can('cqpim_view_all_projects') && empty($closed)) { $index++;
								foreach($project_progress as $progress) {
									$date = isset($progress['date']) ? $progress['date'] : '';
									$updates[$date] = array(
										'pid' => $project->ID,
										'by' => $progress['by'],
										'date' => $progress['date'],
										'update' => $progress['update'],
									);
								}
							} else {
								if(!is_array($project_contributors)) {
									$project_contributors = array($project_contributors);
								}
								foreach($project_contributors as $contributor) {
									if(!empty($contributor['team_id']) && $assigned == $contributor['team_id']) {
										$access = true;
									}
								}
								if(!empty($access) && empty($closed)) { $index++; 
									foreach($project_progress as $progress) {
										$date = isset($progress['date']) ? $progress['date'] : '';
										$updates[$date] = array(
											'pid' => $project->ID,
											'by' => $progress['by'],
											'date' => $progress['date'],
											'update' => $progress['update'],
										);
									}						
								}
							}
						}
						ksort($updates);
						$updates = array_reverse($updates);	?>
						<div style="max-height:800px; overflow:auto">
							<ul class="project_summary_progress" style="margin:0">
								<?php foreach($updates as $pupdate) {
									$project_details = get_post_meta($pupdate['pid'], 'project_details', true);
									$url = get_edit_post_link($pupdate['pid']);
									$project_ref = get_the_title($pupdate['pid']);
									if(is_numeric($pupdate['date'])) { $pupdate['date'] = date(get_option('cqpim_date_format') . ' H:i', $pupdate['date']); } else { $pupdate['date'] = $pupdate['date']; } ?>
									<li style="margin-bottom:0">
										<div class="timeline-entry">
											<?php if(empty($avatar)) {
												echo '<div class="update-who">';
												echo get_avatar( pto_get_user_id_by_display_name($pupdate['by']), 60, '', false, array('force_display' => true) );
												echo '</div>';
											} ?>
											<?php if(empty($avatar)) { ?>
												<div class="update-data">
											<?php } else { ?>
												<div style="width:100%; float:none" class="update-data">
											<?php } ?>
												<div class="timeline-body-arrow"> </div>
												<div class="timeline-by font-blue-madison sbold"><?php echo $pupdate['by']; ?></div>
												<div class="clear"></div>
												<div class="timeline-update font-grey-cascade"><a class="cqpim-link font-grey-cascade" href="<?php echo $url; ?>"><?php echo $project_ref; ?></a> - <?php echo $pupdate['update']; ?></div>
												<div class="clear"></div>
												<div class="timeline-date font-grey-cascade"><?php echo $pupdate['date']; ?></div>
											</div>
											<div class="clear"></div>
										</div>
									</li>
							<?php } ?>
							</ul>
						</div>
					<?php }
					if($index == 0) { ?>
							<div style="padding:20px">
								<h4 style="margin:0"><?php _e('Nothing Here!', 'cqpim'); ?></h4>
								<span><?php _e('You have not been assigned to any open projects...', 'cqpim'); ?></span>	
							</div>					
					<?php } ?>			
				</div>
			</div>
			<?php 
			$quotes_enabled = get_option('enable_quotes');
			if(!empty($quotes_enabled) && current_user_can('edit_cqpim_quotes')) { ?>
				<div class="cqpim-dash-item-triple grid-item tasks-box">
					<div class="cqpim_block">
						<div class="cqpim_block_title">
							<div class="caption">
								<i class="fa fa-file-text font-green-sharp" aria-hidden="true"></i>
								<span class="caption-subject font-green-sharp sbold"><?php _e('Pending Quotes / Estimates', 'cqpim'); ?> </span>
							</div>
							<div class="actions">
								<a class="cqpim_button cqpim_small_button border-green-sharp font-green-sharp rounded_2 sbold" href="<?php echo admin_url(); ?>post-new.php?post_type=cqpim_quote"><?php _e('Add Quote', 'cqpim') ?></a>
							</div>
						</div>
						<?php
						$user = wp_get_current_user();
						$args = array(
							'post_type' => 'cqpim_quote',
							'posts_per_page' => -1,
							'post_status' => 'private',
						); 
						$quotes = get_posts($args);
						$i = 0; ?>
						<table class="datatable_style dataTable" data-ordering="false" data-rows="5">
							<thead>
								<tr>
									<th><?php _e('Title', 'cqpim'); ?></th>
									<th><?php _e('Client', 'cqpim'); ?></th>
									<th><?php _e('Status', 'cqpim'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($quotes as $quote) {
									$quote_details = get_post_meta($quote->ID, 'quote_details', true);
									$client = get_post_meta($quote_details['client_id'], 'client_details', true);
									$sent = isset($quote_details['sent']) ? $quote_details['sent'] : '';
									$confirmed = isset($quote_details['confirmed']) ? $quote_details['confirmed'] : '';
									if(empty($confirmed)) {
										if(empty($sent)) {
											$status = __('New / Not Sent', 'cqpim');
											$class = 'font-red border-red nolink cqpim_button cqpim_xs_button op';
										} else {
											$status = __('Awaiting Client Approval', 'cqpim');
											$class = 'font-amber border-amber nolink cqpim_button cqpim_xs_button op';
										}
										echo '<tr>';
										echo '<td><span class="cqpim_mobile">' . __('Title:', 'cqpim') . '</span> <a href="' . get_edit_post_link($quote->ID) . '">' . $quote->post_title . '</a></td>';
										echo '<td><span class="cqpim_mobile">' . __('Client:', 'cqpim') . '</span> <a href="' . get_edit_post_link($quote_details['client_id']) . '">' . $client['client_company'] . '</a></td>';
										echo '<td><span class="' . $class . '">' . $status . '</span></td>';	
										$i++;
										echo '</tr>';
									}
								}
								if(empty($i)) {
									echo '<tr><td>' . __('There are no pending quotes or estimates', 'cqpim') . '</td><td></td><td></td></tr>';
								} ?>								
							</tbody>
						</table>
					</div>				
				</div>
			<?php } ?>
			<?php $tickets = get_option('disable_tickets'); if(current_user_can('edit_cqpim_supports') && empty($tickets)) { ?>
			<div class="cqpim-dash-item-triple grid-item">
				<div class="cqpim_block">
					<div class="cqpim_block_title">
						<div class="caption">
							<i class="fa fa-life-ring font-green-sharp" aria-hidden="true"></i>
							<span class="caption-subject font-green-sharp sbold"><?php _e('My Open Support Tickets', 'cqpim'); ?> </span>
						</div>
						<div class="actions">
							<a class="cqpim_button cqpim_small_button border-green-sharp font-green-sharp rounded_2 sbold" href="<?php echo admin_url(); ?>post-new.php?post_type=cqpim_support"><?php _e('Add Ticket', 'cqpim') ?></a>
						</div>
					</div>
					<?php
					$user = wp_get_current_user();
					$args = array(
						'post_type' => 'cqpim_support',
						'posts_per_page' => -1,
						'post_status' => 'private',
						'meta_query'        => array(
							'relation'  => 'OR',
							array(
								'key'       => 'ticket_owner',
								'value'     => $assigned,
								'compare'   => '='
							),
							array(
								'key'       => 'ticket_watchers',
								'value'     => $assigned,
								'compare'   => 'LIKE'
							)
						)
					); 
					$tickets = get_posts($args);
					$total_tickets = count($tickets);
					$open_tickets = array();
					foreach($tickets as $ticket) {
						$ticket_status = get_post_meta($ticket->ID, 'ticket_status', true);
						if($ticket_status != 'resolved') {
							$open_tickets[] = $ticket;
						}
					}
					$ordered = array();
					foreach($open_tickets as $ticket) {
						$ticket_updated = get_post_meta($ticket->ID, 'last_updated', true);
						if(!empty($ticket_updated)) {
							$ordered[$ticket_updated] = $ticket;
						}
					}
					krsort($ordered);
					if(!empty($ordered)) {
						$ordering = "";
						echo '<table class="datatable_style dataTable" data-ordering="false" data-rows="10">';
						echo '<thead><tr><th>' . __('Client', 'cqpim') . '</th><th>' . __('Ticket Title', 'cqpim') . '</th><th>' . __('Assigned To', 'cqpim') . '</th><th>' . __('Priority', 'cqpim') . '</th><th>' . __('Last Updated', 'cqpim') . '</th></tr></thead>';
						echo '<tbody>';
						foreach($ordered as $ticket) {
							$ticket_author = $ticket->post_author;
							$author_details = get_user_by('id', $ticket_author);
							$ticket_owner = get_post_meta($ticket->ID, 'ticket_owner', true);
							$owner_details = get_post_meta($ticket_owner, 'team_details', true);
							$owner_name = isset($owner_details['team_name']) ? $owner_details['team_name'] : '';
							$ticket_client = get_post_meta($ticket->ID, 'ticket_client', true);
							$client_details = get_post_meta($ticket_client, 'client_details', true);
							$client_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
							$ticket_status = get_post_meta($ticket->ID, 'ticket_status', true);
							$ticket_watchers = get_post_meta($ticket->ID, 'ticket_watchers', true);
							if(empty($ticket_watchers)) {
								$ticket_watchers = array();
							}
							if(in_array($assigned, $ticket_watchers)) {
								$watching = '<img title="' . __('Watched Support Ticket', 'cqpim') . '" src="' . PTO_PATH . '/img/watching.png" />';
							} else {
								$watching = '';
							}
							$ticket_priority = get_post_meta($ticket->ID, 'ticket_priority', true);
							$ticket_updated = get_post_meta($ticket->ID, 'last_updated', true);
							if(is_numeric($ticket_updated)) { $ticket_updated = date(get_option('cqpim_date_format') . ' H:i', $ticket_updated); } else { $ticket_updated = $ticket_updated; }
							if($ticket_priority == 'low') {
								$priority = '<span class="low_priority font-green border-green cqpim_button cqpim_xs_button nolink op">' . __('Low', 'cqpim') . '</span>';
							} else if($ticket_priority == 'normal') {
								$priority = '<span class="normal_priority font-amber border-amber cqpim_button cqpim_xs_button nolink op">' . __('Normal', 'cqpim') . '</span>';
							} else if($ticket_priority == 'high') {
								$priority = '<span class="high_priority font-red border-red cqpim_button cqpim_xs_button nolink op">' . __('High', 'cqpim') . '</span>';
							} else if($ticket_priority == 'immediate') {
								$priority = '<span class="immediate_priority font-red border-red cqpim_button cqpim_xs_button nolink op sbold upper">' . __('Immediate', 'cqpim') . '</span>';
							}					
							echo '<tr>';
							echo '<td><span class="cqpim_mobile">' . __('Client:', 'cqpim') . '</span> <a href="' . get_edit_post_link($ticket_client) . '">' . $client_name . '</a></td>';
							echo '<td><span class="cqpim_mobile">' . __('Title:', 'cqpim') . '</span> <a href="' . get_edit_post_link($ticket->ID) . '">' . $ticket->post_title . '</a></td>';
							echo '<td><span class="cqpim_mobile">' . __('Assignee:', 'cqpim') . '</span> ' . $owner_name . ' ' . $watching . '</td>';
							echo '<td><span class="cqpim_mobile">' . __('Priority:', 'cqpim') . '</span> ' . $priority . '</td>';
							echo '<td><span class="cqpim_mobile">' . __('Last Updated:', 'cqpim') . '</span> ' . $ticket_updated . '</td>';
							echo '</tr>';
						}
						echo '</tbody>';
						echo '</table>';
					} else { ?>
						<div style="padding:20px">						
								<h4 style="margin:0"><?php _e('Nothing Here!', 'cqpim'); ?></h4>
								<span><?php _e('No open tickets to show...', 'cqpim'); ?></span>						
						</div>	
					<?php }
					?>
				</div>
			</div>
			<?php } ?>
			<?php if(current_user_can('edit_cqpim_invoices')) { 
			if(get_option('disable_invoices') != 1) { ?>
				<div class="cqpim-dash-item-triple grid-item tasks-box">
					<div class="cqpim_block">
						<div class="cqpim_block_title">
							<div class="caption">
								<i class="fa fa-credit-card-alt font-green-sharp" aria-hidden="true"></i>
								<span class="caption-subject font-green-sharp sbold"><?php _e('Outstanding Invoices', 'cqpim'); ?> </span>
							</div>
							<div class="actions">
								<a class="cqpim_button cqpim_small_button border-green-sharp font-green-sharp rounded_2 sbold" href="<?php echo admin_url(); ?>post-new.php?post_type=cqpim_invoice"><?php _e('Add Invoice', 'cqpim') ?></a>
							</div>
						</div>
						<?php
						$args = array(
							'post_type' => 'cqpim_invoice',
							'posts_per_page' => -1,
							'post_status' => 'publish'
						);
						$invoices = get_posts($args);
						$this_client = array();
						$total_value = 0;
						$total_out = 0;
						foreach($invoices as $invoice) {
							$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
							$paid = isset($invoice_details['paid']) ? $invoice_details['paid']: '';
							if($paid != 1) {
								$this_client[] = $invoice;
							}
						}
						if(!empty($this_client)) {
							echo '<table class="datatable_style dataTable" data-ordering="false" data-rows="10">';
							echo '<thead>';
							echo '<tr><th>ID</th><th>' . __('Due', 'cqpim') . '</th><th>' . __('Status', 'cqpim') . '</th><th>' . __('Total', 'cqpim') . '</th><th>' . __('Outstanding', 'cqpim') . '</th></tr>';
							echo '</thead>';
							foreach($this_client as $invoice) {
								$outstanding = 0;
								$currency = get_option('currency_symbol');
								$invoice_link = get_edit_post_link($invoice->ID);
								$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);	
								$invoice_totals = get_post_meta($invoice->ID, 'invoice_totals', true);
								$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
								$total = isset($invoice_totals['total']) ? $invoice_totals['total'] : '';
								$project_id = isset($invoice_details['project_id']) ? $invoice_details['project_id'] : '';
								$invoice_id = get_post_meta($invoice->ID, 'invoice_id', true);
								$project_details = get_post_meta($project_id, 'project_details', true);
								$project_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
								$project_link = get_edit_post_link($project_id);
								$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
								$on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';
								if(empty($on_receipt)) {
									$due_string = date(get_option('cqpim_date_format'), $due);
								} else {
									$due_string = __('Due on Receipt', 'cqpim');
								}
								$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
								$sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
								$invoice_payments = get_post_meta($invoice->ID, 'invoice_payments', true);
								$total_paid = 0;
								if(empty($invoice_payments)) {
									$invoice_payments = array();
								}
								foreach($invoice_payments as $payment) {
									$amount = isset($payment['amount']) ? $payment['amount'] : 0;
									$total_paid = $total_paid + $amount;
								}
								if(!empty($total_paid)) {
									$outstanding = $total - $total_paid;
								} else {
									$outstanding = $total;
								}
								$now = time();
								if(empty($paid)) {
									if(!empty($due)) {
										if($now > $due) {
											$class = 'overdue';
											$status = '<span class="task_over font-red border-red upper sbold nolink cqpim_button cqpim_xs_button op"><strong>' . __('OVERDUE', 'cqpim') . '</strong></span>';		
										} else {
											if(!empty($sent)) {
												$class = 'sent';
												$status = '<span class="task_pending font-amber border-amber nolink cqpim_button cqpim_xs_button op">' . __('Sent', 'cqpim') . '</span>';							
											} else {
												$class = 'not-sent';
												$status = '<span class="task_over font-red border-red nolink cqpim_button cqpim_xs_button op">' . __('Not Sent', 'cqpim') . '</span>';							
											}
										}
									}
								} else {
									$class = 'paid';
									$status = '<span class="task_complete">' . __('PAID', 'cqpim') . '</span>';
								}
								echo '<tr>';
								echo '<td><span class="cqpim_mobile">' . __('Invoice ID:', 'cqpim') . '</span> <a href="' . $invoice_link . '" >' . $invoice_id . '</a></td>';
								echo '<td><span class="cqpim_mobile">' . __('Due Date:', 'cqpim') . '</span> ' . $due_string . '</td>';
								echo '<td class="' . $class . '"><span class="cqpim_mobile">' . __('Status:', 'cqpim') . '</span> ' . $status . '</td>';
								echo '<td><span class="cqpim_mobile">' . __('Total:', 'cqpim') . '</span> ' . pto_calculate_currency($invoice->ID, $total) . '</td>';
								echo '<td><span class="cqpim_mobile">' . __('Outstanding:', 'cqpim') . '</span> ' . pto_calculate_currency($invoice->ID, $outstanding) . '</td>';
								echo '</tr>';
							}
							echo '</table>';
						} else { ?>
							<div style="padding:20px">
									<h4 style="margin:0"><?php _e('Nothing Here!', 'cqpim'); ?></h4>
									<span><?php _e('No outstanding invoices to show...', 'cqpim'); ?></span>
							</div>
						<?php
						} ?>
					</div>
				</div>
			<?php } }?>	
			<?php if(current_user_can('cqpim_dash_view_whos_online')) { ?>
				<div class="cqpim-dash-item-double grid-item tasks-box">
					<div class="cqpim_block">
						<div class="cqpim_block_title">
							<div class="caption">
								<i class="fa fa-users font-green-sharp" aria-hidden="true"></i>
								<span class="caption-subject font-green-sharp sbold"><?php _e('Who\'s Online?', 'cqpim'); ?> </span>
							</div>
						</div>
						<div id="cqpim_online_users">
							<?php pto_display_logged_in_users(); ?>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>		
	<?php }
}