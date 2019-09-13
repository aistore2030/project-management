<?php
add_action( 'admin_menu' , 'register_pto_files_page', 10 ); 
function register_pto_files_page() {
	$mypage = add_submenu_page(	
				'pto-dashboard',
				__('All Files (Admin)', 'cqpim'), 			
				__('All Files (Admin)', 'cqpim'),		
				'cqpim_view_all_files', 			
				'pto-files-admin', 		
				'pto_files_admin'
	);
	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
}
function pto_files_admin() { 
	$user = wp_get_current_user(); 
	$roles = $user->roles;
	$assigned = pto_get_team_from_userid();
	?>
	<div class="masonry-grid">
		<div class="cqpim-dash-item-full grid-item tasks-box">
			<div class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<i class="fa fa-file font-green-sharp" aria-hidden="true"></i>
						<span class="caption-subject font-green-sharp sbold"> <?php _e('All Files (Admin)', 'cqpim'); ?></span>
					</div>
				</div>
				<?php
				$all_attached_files = array();
				$args = array(
					'post_type' => 'cqpim_tasks',
					'posts_per_page' => -1,
					'post_status' => 'publish'
				);
				$tasks = get_posts($args);
				foreach($tasks as $task) {
					$args = array(
						'post_parent' => $task->ID,
						'post_type' => 'attachment',
						'numberposts' => -1
					);
					$children = get_children($args);
					foreach($children as $child) {
						$all_attached_files[] = $child;
					}
				}
				if(!$all_attached_files) {
					echo '<p>' . __('There are no files uploaded', 'cqpim') . '</p>';
				} else {
					echo '<table class="datatable_style dataTable"><thead><tr>';
					echo '<th>' . __('File Name', 'cqpim') . '</th><th>' . __('Related Project', 'cqpim') . '</th><th>' . __('Related Task', 'cqpim') . '</th><th>' . __('File Type', 'cqpim') . '</th><th>' . __('Uploaded', 'cqpim') . '</th><th>' . __('Uploaded By', 'cqpim') . '</th><th>' . __('Actions', 'cqpim') . '</th>';
					echo '</tr></thead><tbody>';
					foreach($all_attached_files as $file) {
						$file_object = get_post($file->ID);
						$url = get_the_permalink($file->ID);
						$parent = $file->post_parent;
						$project = get_post_meta($parent, 'project_id', true);
						$project_object = get_post($project);
						$parent_object = get_post($parent);
						$parent_url = get_edit_post_link($parent_object->ID);
						$project_url = get_edit_post_link($project_object->ID);
						$user = get_user_by( 'id', $file->post_author );
						echo '<tr>';
						echo '<td><span class="cqpim_mobile">' . __('File Name:', 'cqpim') . '</span> <a class="cqpim-link" href="' . $file->guid . '">' . $file->post_title . '</a></td>';
						echo '<td><span class="cqpim_mobile">' . __('Project:', 'cqpim') . '</span> <a class="cqpim-link" href="' . $project_url . '">' . $project_object->post_title . '</a></td>';
						echo '<td><span class="cqpim_mobile">' . __('Task:', 'cqpim') . '</span> <a class="cqpim-link" href="' . $parent_url . '">' . $parent_object->post_title . '</a></td>';
						echo '<td><span class="cqpim_mobile">' . __('Type:', 'cqpim') . '</span> ' . $file->post_mime_type . '</td>';
						echo '<td><span class="cqpim_mobile">' . __('Uploaded:', 'cqpim') . '</span> ' . $file->post_date . '</td>';
						echo '<td><span class="cqpim_mobile">' . __('User:', 'cqpim') . '</span> ' . $user->display_name . '</td>';
						echo '<td><a href="' . $file->guid . '" download="' . $file->post_title . '" class="cqpim_button cqpim_small_button border-green font-green op" value="' . $file->ID . '"><i class="fa fa-download" aria-hidden="true"></i></a></td>';
						echo '</tr>';
					}
					echo '</tbody></table>'; ?>
					<div class="clear"></div>
				<?php } ?>
			</div>
		</div>
	</div>	
<?php }