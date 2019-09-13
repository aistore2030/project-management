<?php
function pto_project_contributors_metabox_callback( $post ) {
	$meta = get_post_meta($post->ID, 'project_contributors', true);
	$current_user = wp_get_current_user();
 	wp_nonce_field( 
	'project_contributors_metabox', 
	'project_contributors_metabox_nonce' );
	$project_contributors = get_post_meta($post->ID, 'project_contributors', true);
	if(!$project_contributors) {
		echo '<p>' . __('There are no team members assigned to this project', 'cqpim') . '</p>';
	} else {
		foreach($project_contributors as $key => $contributor) {
			$contributor['pm'] = isset($contributor['pm']) ? $contributor['pm'] : '';
			$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
			$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
			$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
			if(current_user_can('edit_cqpim_teams')) {
				$team_url = get_edit_post_link($contributor['team_id']);
				$team_name = '<a href="' . $team_url . '" target="_blank">' . $team_name . '</a>';
			}
			$team_job = isset($team_details['team_job']) ? $team_details['team_job'] : '';
			$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
			$team_telephone = isset($team_details['team_telephone']) ? $team_details['team_telephone'] : '';
			echo '<div class="team_member">';
			if(!empty($contributor['pm']) && $contributor['pm'] == 1) {
				$pm = '<strong>' . __('Project Manager', 'cqpim') . '</strong>';
			} else {
				$pm = '';
			}
			if(!empty($pm)) {
				echo '<div class="ppm cqpim_ribbon_left cqpim_button cqpim_small_button nolink bg-blue font-white op">' . $pm . '</div>';
			}
			$value = get_option('cqpim_disable_avatars');
			if(empty($value)) {
				echo '<div class="cqpim_gravatar">';
					echo get_avatar( $user_id, 80, '', false, array('force_display' => true) ); 
				echo '</div>';
			} else {
				echo '<div style="height:40px"></div>';				
			}
			echo '<div class="team_details">';
			echo '<span class="team_name block">' . $team_name . '</span>';
			echo '<a href="mailto:' . $team_email . '" class="cqpim_tooltip" title="' . $team_email . '"><i class="fa fa-envelope" aria-hidden="true"></i></a><br />';
			echo '<i class="fa fa-phone" aria-hidden="true"></i> ' . $team_telephone . '<br />';
			echo '</div><br />';
			echo '<table>';
			if(current_user_can('cqpim_edit_project_members') || $current_user->ID == $user_id) {
				echo '<tr><td><input type="checkbox" class="disable_email" id="dn-' . $contributor['team_id'] . '" data-team="' . $contributor['team_id'] . '" data-key="' . $key . '" ' . checked(isset($contributor['demail']) ? $contributor['demail'] : 0, 1, false) . ' value="1" /></td>';
				echo '<td style="text-align:left">' . __('Disable Emails', 'cqpim') . ' <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="' . __('Check this box to disable task email notifications for this project. You will still receive notifications in the dashboard.', 'cqpim') . '"></i></td></tr>';
			}
			if(current_user_can('cqpim_edit_project_members')) {
				echo '<tr><td><input type="checkbox" class="project_manager" id="pm-' . $contributor['team_id'] . '" data-team="' . $contributor['team_id'] . '" data-key="' . $key . '" ' . checked($contributor['pm'], 1, false) . ' value="1" /></td>';
				echo '<td style="text-align:left">' . __('Project Manager', 'cqpim') . '</td></tr>';
			}
			echo '</table>';
			if(current_user_can('cqpim_edit_project_members')) {
				echo '<br />';
				echo '<div class="team_delete">';
				echo '<button class="delete_team cqpim_button cqpim_small_button border-red font-red" data-team="' . $contributor['team_id'] . '" value="' . $key . '"><i class="fa fa-trash" aria-hidden="true"></i></div>';
			}
			echo '<div class="clear"></div>';
			echo '</div>'; ?>
		<?php }
		echo '<div class="clear"></div>';
	}
	if(current_user_can('cqpim_edit_project_members')) {
		echo '<button id="add_team_member" class="mt-20 cqpim_button cqpim_small_button border-blue font-blue right" value="">' . __('Add Team Member', 'cqpim') . '</button>';
	}
	echo '<div class="clear"></div>';
	?>
	<div id="add_team_member_div_container" style="display:none">
		<div id="add_team_member_div">
			<div style="padding:12px">
				<h3><?php _e('Add Team Member', 'cqpim'); ?></h3>
				<p><?php _e('Adding a team member to this project will give them access <br />
				to the project tasks and allow them to edit, assign and complete tasks.', 'cqpim'); ?></p>
				<p><strong><?php _e('Choose team member to add:', 'cqpim'); ?></strong></p>
				<?php 
					$project_contributors = get_post_meta($post->ID, 'project_contributors', true);
					$project_contributors = $project_contributors&&is_array($project_contributors)?$project_contributors:array();
					$args = array(
						'post_type' => 'cqpim_teams',
						'posts_per_page' => -1,
						'post_status' => 'private'
					);
					$team_members = get_posts($args);
					echo '<select id="team_members">';
					if($team_members) {
						echo '<option value="">' . __('Choose a Team Member', 'cqpim') . '</option>';
						foreach($team_members as $team_member) {
							$team_details = get_post_meta($team_member->ID, 'team_details', true);
							$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
							$team_job = isset($team_details['team_job']) ? $team_details['team_job'] : '';
							if(in_array($team_member->ID, $project_contributors)) {
								$added = 'style="display:none"';
							} else {
								$added = '';
							}
							echo '<option value="' . $team_member->ID . '" ' . $added . '>' . $team_name . ' - ' . $team_job . '</option>';
						}
					} else {
						echo '<option value="">' . __('No Team Members Available', 'cqpim') . '</option>';
					}
					echo '</select>';
				?>
				<br /><br />
				<input type="checkbox" id="pm" /> <?php _e('Add this team member as project manager on this project', 'cqpim'); ?>
				<br /><br />
				<div id="add_team_messages"></div>
				<button id="add_team_member_ajax" class="cqpim_button font-green border-green right op" value=""><?php _e('Add Team Member', 'cqpim'); ?></button>
				<button class="cancel-colorbox cqpim_button font-red border-red op"><?php _e('Cancel', 'cqpim'); ?></button>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<?php
}