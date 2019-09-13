<?php
add_action( 'admin_menu' , 'register_pto_custom_fields_page', 29 ); 
function register_pto_custom_fields_page() {
	$mypage = add_submenu_page(	
				'pto-dashboard',
				__('Custom Fields', 'cqpim'),		
				__('Custom Fields', 'cqpim'), 			
				'edit_cqpim_settings', 			
				'pto-custom-fields', 		
				'pto_custom_fields'
	);
	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
}
function pto_custom_fields() {
	$post_type = isset($_SESSION['cqpim_cf_post_type']) ? $_SESSION['cqpim_cf_post_type'] : 'support'; ?>
	<div class="cqpim-dash-item-full tasks-box">
		<br />
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php _e('Custom Fields', 'cqpim'); ?> </span>
				</div>
			</div>
			<p>
				<?php _e('Currently managing:', 'cqpim'); ?> 
				<select id="post_type">
					<option value="support" <?php if($post_type == 'support') { echo 'selected="selected"'; } ?>><?php _e('Support Tickets', 'cqpim'); ?></option>
					<option value="client" <?php if($post_type == 'client') { echo 'selected="selected"'; } ?>><?php _e('Clients', 'cqpim'); ?></option>
					<option value="invoice" <?php if($post_type == 'invoice') { echo 'selected="selected"'; } ?>><?php _e('Invoices', 'cqpim'); ?></option>
					<option value="task" <?php if($post_type == 'task') { echo 'selected="selected"'; } ?>><?php _e('Tasks', 'cqpim'); ?></option>
				</select>
				<span class="ajax_spinner" style="display:none"></span>
			</p>
			<?php
			$data = get_option('cqpim_custom_fields_' . $post_type);	
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
						disableFields: ['autocomplete', 'button', 'file', 'hidden', 'checkbox', 'paragraph'],
						formData : "<?php echo $builder; ?>",
						dataType: 'json',
					};
					jQuery('#form_builder_container').formBuilder(options);
					var $fbEditor = jQuery(document.getElementById('form_builder_container'));
					var formBuilder2 = $fbEditor.data('formBuilder');
					jQuery(".form-builder-save").click(function(e) {
						e.preventDefault();
						jQuery('#builder_data').val(formBuilder2.formData.replace(/("[^"]*")|\s/g, "$1"));
						cqpim_save_custom_fields();
					});
				});
			</script>
			<div id="form_builder_container">
			</div>
			<input type="hidden" id="post_type" value="<?php echo $post_type; ?>" />
			<textarea style="display:none" name="builder_data" id="builder_data"><?php if(!empty($builder)) { echo $builder; } else { echo ''; } ?></textarea>
		</div>
	</div>
<?php }