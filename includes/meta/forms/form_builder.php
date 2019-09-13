<?php
function pto_form_builder_builder_metabox_callback( $post ) {
 	wp_nonce_field( 
	'form_builder_builder_metabox', 
	'form_builder_builder_metabox_nonce' );
	$data = get_post_meta($post->ID, 'builder_data', true);	
	if(!empty($data)) {	
		$builder = $data;		
	} else {
		$builder = '';
	}
	?>
	<script>
		jQuery(document).ready(function() {
			var options = {
				editOnAdd: false,
				fieldRemoveWarn: true,
				disableFields: ['autocomplete', 'button', 'hidden', 'checkbox', 'paragraph'],
				formData : "<?php echo str_replace('"', '\"', $builder); ?>",
				dataType: 'json',
			};
			jQuery('#form_builder_container').formBuilder(options);
			var $fbEditor = jQuery(document.getElementById('form_builder_container'));
			var formBuilder2 = $fbEditor.data('formBuilder');
			jQuery(".form-builder-save").click(function(e) {
				e.preventDefault();
				jQuery('#builder_data').val(formBuilder2.formData.replace(/("[^"]*")|\s/g, "$1"));
				jQuery('#publish').click();
			});
		});
	</script>
	<div id="form_builder_container">
	</div>
	<textarea style="display:none" name="builder_data" id="builder_data"><?php if(!empty($builder)) { echo $builder; } else { echo ''; } ?></textarea>
	<?php
}
add_action( 'save_post', 'save_pto_form_builder_builder_metabox_data' );
function save_pto_form_builder_builder_metabox_data( $post_id ){
	if ( ! isset( $_POST['form_builder_builder_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['form_builder_builder_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'form_builder_builder_metabox' ) )
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
	$builder_data = get_post_meta($post_id, 'builder_data', true);
	if(empty($builder_data)) {
		$builder_data = array();
	}
	if(!empty($_POST['builder_data'])) {
		$builder_data = $_POST['builder_data'];
	}
	update_post_meta($post_id, 'builder_data', $builder_data);
}