<?php
function pto_project_updates_metabox_callback( $post ) {
	$project_progress = get_post_meta($post->ID, 'project_progress', true); ?>
	<div style="max-height:500px; overflow:auto" class="content">
		<?php if($project_progress) {
			$project_progress = array_reverse($project_progress);
			?>
			<ul class="project_summary_progress" style="margin:0">
				<?php foreach($project_progress as $pupdate) {
					$project_details = get_post_meta($post->ID, 'project_details', true);
					$url = get_edit_post_link($post->ID);
					$project_ref = get_the_title($post->ID);
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
								<div class="timeline-update font-grey-cascade"><?php echo $pupdate['update']; ?></div>
								<div class="clear"></div>
								<div class="timeline-date font-grey-cascade"><?php echo $pupdate['date']; ?></div>
							</div>
							<div class="clear"></div>
						</div>
					</li>
				<?php } ?>
			</ul>
		<?php } ?>
	</div>
	<?php
}