<?php
function pto_task_time_metabox_callback( $post ) {
 	wp_nonce_field( 
	'task_time_metabox', 
	'task_time_metabox_nonce' );
	$time_spent = get_post_meta($post->ID, 'task_time_spent', true);
	$pid = get_post_meta($post->ID, 'project_id', true);
	$hide_front = get_post_meta($post->ID, 'hide_front', true);
	if(!empty($pid)) { ?>
		<input type="checkbox" name="hide_front" value="1" <?php checked($hide_front, 1); ?> /> <?php _e('Hide time entries in client dashboard', 'cqpim'); ?>
		<br />
	<?php }
	$pid = get_post_meta($post->ID, 'project_id', true);
	$parent_object = get_post($pid);
	$parent_type = isset($parent_object->post_type) ? $parent_object->post_type : '';
	if($time_spent) {
		$total = 0;
		echo '<ul class="time_spent">';
		foreach($time_spent as $key => $time) {
			$user = wp_get_current_user();
			$args = array(
				'post_type' => 'cqpim_teams',
				'posts_per_page' => -1,
				'post_status' => 'private'
			);
			$members = get_posts($args);
			foreach($members as $member) {
				$team_details = get_post_meta($member->ID, 'team_details', true);
				if($team_details['user_id'] == $user->ID) {
					$assigned = $member->ID;
				}
			}
			if($assigned == $time['team_id'] || current_user_can('cqpim_dash_view_all_tasks')) {
				$delete = '<a class="time_remove" href="#" data-key="'. $key .'" data-task="'. $post->ID .'"><i class="fa fa-trash font-red"></i></a>';
			} else {
				$delete = '';
			}
			echo '<li>' . $time['team'] . ' <span style="float:right" class="right"><strong>' . number_format((float)$time['time'], 2, '.', '') . ' ' . __('HOURS', 'cqpim') . '</strong> ' . $delete . '</span></li>';
			$total = $total + $time['time'];
		}
		echo '</ul>';
		$total = str_replace(',','.', $total);
		$time_split = explode('.', $total);
		if(!empty($time_split[1])) {
			$minutes = '0.' . $time_split[1];
			$minutes = $minutes * 60;
			$minutes = number_format((float)$minutes, 0, '.', '');
		} else {
			$minutes = '0';
			$time_split[1] = '0';
		}
		echo '<span><strong>' . __('TOTAL:', 'cqpim') . ' ' . number_format((float)$total, 2, '.', '') . ' ' . __('HOURS', 'cqpim') . '</strong><br>(' . $time_split[0] . ' ' . _n('hour', 'hours', $time_split[0], 'cqpim') . ' + ' . $minutes . ' ' . _n('minute', 'minutes', $time_split[1], 'cqpim') . ')</span> <div id="ajax_spinner_remove_time_'. $post->ID .'" class="ajax_spinner" style="display:none"></div>';
	} else {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . __('This task does not have any time assigned to it', 'cqpim') . '</div>';
	}
	?>
	<p><strong><?php _e('Manually Add Time', 'cqpim'); ?></strong></p>
	<?php $hours_range = range(0,100); ?>
	<?php $minutes_range = range(0,60); ?>
	<select style="display:inline-block; width:auto" name="add_time_hours" id="add_time_hours">
		<option value="0"><?php _e('Hours', 'cqpim'); ?></option>
		<?php foreach($hours_range as $hours) { ?>
			<option value="<?php echo $hours; ?>"><?php echo $hours; ?></option>
		<?php } ?>
	</select>
	<select style="display:inline-block; width:auto" name="add_time_minutes" id="add_time_minutes">
		<option value="0"><?php _e('Minutes', 'cqpim'); ?></option>
		<?php foreach($minutes_range as $minutes) { ?>
			<option value="<?php echo $minutes; ?>"><?php echo $minutes; ?></option>
		<?php } ?>	
	</select><br />
	<button class="s_button2 mt-10 cqpim_button cqpim_small_button border-blue font-blue op" id="add_mtime_ajax"><?php _e('Add Time', 'cqpim'); ?></button>
	<p><strong><?php _e('Add Time Using Timer', 'cqpim'); ?></strong></p>
	<input style="width:60%; display:block; float:left; margin-right:10px;" id="task_time_value" type="text" name="timer" class="form-control timer" placeholder="<?php _e('0 sec', 'cqpim'); ?>" />
	<button class="cqpim_button cqpim_small_button border-green font-green start-timer-btn"><i class="fa fa-play" aria-hidden="true" title="<?php _e('Start Timer', 'cqpim'); ?>"></i></button>
	<button class="cqpim_button cqpim_small_button border-green font-green resume-timer-btn hidden"><i class="fa fa-play" aria-hidden="true" title="<?php _e('Resume Timer', 'cqpim'); ?>"></i></button>
	<button class="cqpim_button cqpim_small_button border-amber font-amber pause-timer-btn hidden"><i class="fa fa-pause" aria-hidden="true" title="<?php _e('Pause Timer', 'cqpim'); ?>"></i></button>
	<button class="cqpim_button cqpim_small_button border-red font-red remove-timer-btn hidden"><i class="fa fa-trash" aria-hidden="true" title="<?php _e('Remove Timer', 'cqpim'); ?>"></i></button>					
	<input type="hidden" id="task_time_task" value="<?php echo $post->ID; ?>" />
	<div class="clear"></div>
	<button class="s_button2 mt-10 cqpim_button cqpim_small_button border-blue font-blue op" id="add_time_ajax"><?php _e('Add Time from Timer', 'cqpim'); ?></button>
	<br />
	<?php if(current_user_can('cqpim_delete_assigned_tasks')) { ?>
		<button class="s_button2 cqpim_button font-white bg-red block mt-10 rounded_2 block" data-id="<?php echo $post->ID; ?>" id="delete_task"><?php _e('DELETE TASK', 'cqpim'); ?></button>
	<?php } ?>
	<button class="s_button cqpim_button font-white bg-blue block mt-10 rounded_2 block" data-id="<?php echo $post->ID; ?>"><?php _e('Update Task', 'cqpim'); ?></button>
	<?php if(!empty($pid) && $parent_type == 'cqpim_project') { ?>
		<a class="cqpim_button_link cqpim_button font-white bg-blue block mt-10 rounded_2 block" href="<?php echo get_edit_post_link($pid); ?>"><?php _e('Back to Project', 'cqpim'); ?></a>
	<?php } ?>
	<?php if(!empty($pid) && $parent_type == 'cqpim_support') { ?>
		<a class="cqpim_button_link cqpim_button font-white bg-blue block mt-10 rounded_2 block" href="<?php echo get_edit_post_link($pid); ?>"><?php _e('Back to Support Ticket', 'cqpim'); ?></a>
	<?php } ?>
	<?php
}
add_action( 'save_post', 'save_pto_task_time_metabox_data' );
function save_pto_task_time_metabox_data( $post_id ){
	if ( ! isset( $_POST['task_time_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['task_time_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'task_time_metabox' ) )
	    return $post_id;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	    return $post_id;
	if ( 'page' == $_POST['post_type'] ) {
	    if ( ! current_user_can( 'edit_page', $post_id ) )
	        return $post_id;
	  	} else {
	    if ( ! current_user_can( 'edit_post', $post_id ) )
	        return $post_id;
	}
	$hide_front = isset($_POST['hide_front']) ? $_POST['hide_front'] : '';
	update_post_meta($post_id, 'hide_front', $hide_front);
}