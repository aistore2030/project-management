<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-question-circle font-green-sharp" aria-hidden="true"></i> <?php _e('FAQ', 'cqpim'); ?>
		</div>
	</div>
	<div class="cqpim-dash-item-inside">	
		<?php 
		$cats = get_option('cqpim_enable_faq_dash_cats');
		$terms = get_terms([
			'taxonomy' => 'cqpim_faq_cat',
			'hide_empty' => false,
		]);
		if(!empty($cats)) {
			foreach($terms as $term) { 
				$args = array(
					'post_type' => 'cqpim_faq',
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'tax_query' => [
						[
							'taxonomy' => 'cqpim_faq_cat',
							'terms' => $term->term_id,
							'include_children' => true
						],
					],
					'meta_key' => 'faq_order',
					'orderby' => 'meta_value_num',
					'order' => 'ASC',		
				);
				$faq = get_posts($args);
				?>
				<h3><?php echo $term->name; ?></h3>
				<?php foreach($faq as $f) { ?>
					<p><a href="<?php echo get_the_permalink($f->ID); ?>"><?php echo $f->post_title; ?></a></p>
				<?php } ?>
			<?php } ?>
		<?php } else { ?>	
			<?php $args = array(
				'post_type' => 'cqpim_faq',
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'meta_key' => 'faq_order',
				'orderby' => 'meta_value_num',
				'order' => 'ASC',		
			);
			$faq = get_posts($args);
			?>
			<h3><?php echo $term->name; ?></h3>
			<?php foreach($faq as $f) { ?>
				<p><a href="<?php echo get_the_permalink($f->ID); ?>"><?php echo $f->post_title; ?></a></p>
			<?php } ?>	
		<?php } ?>
		<div class="clear"></div>
	</div>
</div>	