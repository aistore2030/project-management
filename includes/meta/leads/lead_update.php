<?php
// Contact Details Metabox
function lead_pto_update_metabox_callback( $post ) {
	// Add an nonce field so we can check for it later.
 	wp_nonce_field( 
 	 	'lead_update_metabox', 
 	 	'lead_update_metabox_nonce' ); ?>
		<a class="save cqpim_button cqpim_button_link font-white bg-blue rounded_2 block" href="#"><?php _e('Update Lead', 'cqpim-expenses'); ?></a>
		<?php if(current_user_can('delete_cqpim_suppliers')) { ?>
			<a class="delete cqpim_button cqpim_button_link font-white bg-red rounded_2 block mt-10" href="<?php echo get_delete_post_link($post->ID); ?>"><?php _e('Delete Lead', 'cqpim-expenses'); ?></a>
		<?php } ?>
	<?php
}