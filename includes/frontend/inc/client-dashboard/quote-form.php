<br />
<div class="cqpim_block">
<div class="cqpim_block_title">
	<div class="caption">
		<i class="fa fa-angle-double-right font-green-sharp" aria-hidden="true"></i>
		<span class="caption-subject font-green-sharp sbold"><?php _e('Request a Quote', 'cqpim'); ?></span>
	</div>
</div>
<br />
<?php 
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if(empty($client_logs)) {
	$client_logs = array();
}
$now = current_time('timestamp');
$client_logs[$now] = array(
	'user' => $user->ID,
	'page' => __('Client Dashboard Quote Form', 'cqpim')
);
update_post_meta($assigned, 'client_logs', $client_logs);	
$form = get_option('cqpim_backend_form');
$form_data = get_post_meta($form, 'builder_data', true);
if(!empty($form_data)) {
	$form_data = json_decode($form_data);
	$fields = $form_data;
}
echo '<form id="cqpim_backend_form">';
if(!empty($fields)) {
	echo '<div id="cqpim_backend_quote">';
	foreach($fields as $field) {
		$id = strtolower($field->label);
		$id = str_replace(' ', '_', $id);
		$id = str_replace('-', '_', $id);
		$id = preg_replace('/[^\w-]/', '', $id);
		if(!empty($field->required) && $field->required == 1) {
			$required = 'required';
			$ast = '<span style="color:#F00">*</span>';
		} else {
			$required = '';
			$ast = '';
		}
		echo '<div style="padding-bottom:12px" class="cqpim_form_item">';
		if($field->type != 'header') {
			echo '<label style="display:block; padding-bottom:5px" for="' . $id . '">' . $field->label . ' ' . $ast . '</label>';
		}
		if($field->type == 'header') {
			echo '<' . $field->subtype . ' class="' . $field->className . '">' . $field->label . '</' . $field->subtype . '>';
		} elseif($field->type == 'text') {
			echo '<input type="text" class="' . $field->className . '" id="' . $id . '" ' . $required . ' />';
		} elseif($field->type == 'website') {
			echo '<input type="url" class="' . $field->className . '" id="' . $id . '" ' . $required . ' />';
		} elseif($field->type == 'number') {
			echo '<input type="number" class="' . $field->className . '" id="' . $id . '" ' . $required . ' />';
		} elseif($field->type == 'textarea') {
			echo '<textarea class="' . $field->className . '" id="' . $id . '" ' . $required . '></textarea>';
		} elseif($field->type == 'date') {
			echo '<input class="' . $field->className . '" type="date" id="' . $id . '" ' . $required . ' />';
		} elseif($field->type == 'email') {
			echo '<input type="email" class="' . $field->className . '" id="' . $id . '" ' . $required . ' />';
		} elseif($field->type == 'checkbox') {
			if(!empty($field->toggle) && $field->toggle == true) {
				$toggle = 'toggle="true"';
			} else {
				$toggle = '';
			}
			echo '<input type="checkbox" ' . $toggle . ' class="' . $field->className . '" value="' . $option->value . '" name="' . $id . '" /> ' . $option->label . '<br />';
		} elseif($field->type == 'checkbox-group') {
			$options = $field->values;
			foreach($options as $option) {
				echo '<input type="checkbox" class="' . $field->className . '" value="' . $option->value . '" name="' . $id . '" /> ' . $option->label . '<br />';
			}
		} elseif($field->type == 'file') {
			$multiple = isset($field->multiple) ? 'true' : 'false';
			echo '<input type="file" class="cqpim-file-upload-form" name="async-upload" id="' . $id . '" data-multiple="' . $multiple . '"/>';
			echo '<div id="upload_messages_' . $id . '"></div>';
			echo '<input type="hidden" name="image_id" id="upload_' . $id . '">';
			echo '<div class="clear"></div>';
		} elseif($field->type == 'radio-group') {
			$options = $field->values;
			foreach($options as $option) {
				echo '<input type="radio" class="' . $field->className . '" value="' . $option->value . '" name="' . $id . '" ' . $required . ' /> ' . $option->label . '<br />';
			}
		} elseif($field->type == 'select') {
			$options = $field->values;
			echo '<select class="' . $field->className . '" id="' . $id . '" ' . $required . '>';
				foreach($options as $option) {	
					echo '<option value="' . $option->value . '">' . $option->label . '</option>';
				}
			echo '</select>';
		}
		if(!empty($field->other) && $field->other == 1) {
			echo '<br />';
			echo __('Other:', 'cqpim') . '<input style="width:100%" type="text" id="' . $id . '_other" />';
		}
		if(!empty($field->description)) {
			echo '<p>' . $field->description . '</p>';
		}
		echo '</div>';
	}
	echo '<div class="clear"></div><br />';
	echo '<input type="submit" id="cqpim_submit_backend" class="cqpim_button font-white bg-blue op mt-20 rounded_2" value="' . __('Submit Quote Request', 'cqpim') . '" />';
	echo '<div style="margin-top:20px" id="cqpim_submit_backend_messages"></div>';
	echo '</div>';
} else {
	echo '<p>' . __('You have not added any fields to the selected form', 'cqpim') . '</p>';
}
echo '</form>';	
echo '<input type="hidden" id="client" value="' . $assigned . '" />';
?>
<?php add_action( 'wp_footer', 'pto_backend_form_scripts', 50 );
function pto_backend_form_scripts() {
$form = get_option('cqpim_backend_form');
$form_data = get_post_meta($form, 'builder_data', true);
if(empty($form_data)) {
	$form_data = '';
}
$form_data = json_decode($form_data);
$fields = $form_data;
if(empty($fields)) {
	$fields = array();
} ?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#cqpim_backend_form').on('submit', function(e) {
			e.preventDefault();
			var spinner = jQuery('#overlay');
			var client = jQuery('#client').val();
			<?php
			if(empty($fields)) {
				$fields = array();
			}
			foreach($fields as $field) {
				$id = strtolower($field->label);
				$id = str_replace(' ', '_', $id);
				$id = str_replace('-', '_', $id);
				$id = preg_replace('/[^\w-]/', '', $id);	
				if($field->type != 'header') {
					if($field->type == 'text' || 
						$field->type == 'number' || 
						$field->type == 'email' || 
						$field->type == 'textarea' ||
						$field->type == 'website' || 
						$field->type == 'select' || 
						$field->type == 'date' || 
						$field->type == 'number') {
						echo 'var ' . $id . ' = jQuery("#' . $id . '").val();';
						echo 'if(!' . $id . ') { ' . $id . ' = ""; };';
					} elseif($field->type == 'checkbox-group' || $field->type == 'checkbox') {
						echo 'var ' . $id . ' = jQuery("input[name=' . $id . ']:checked").map(function() { return jQuery(this).val(); }).get();';
						echo 'if(!' . $id . ') { ' . $id . ' = ""; };';
					} elseif($field->type == 'radio-group') {
						echo 'var ' . $id . ' = jQuery("input[name=' . $id . ']:checked").val();';
						echo 'if(!' . $id . ') { ' . $id . ' = ""; };';
					} elseif($field->type == 'file') {
						echo 'var ' . $id . ' = jQuery("#upload_' . $id . '").val();';
						echo 'if(!' . $id . ') { ' . $id . ' = ""; };';
					}
					if(!empty($field->other) && $field->other == 1) {
						echo 'var ' . $id . '_other = jQuery("#' . $id . '_other").val();';
					}
				}
			}
			?>
			var data = {
				'action' : 'pto_backend_quote_submission',
				'client' : client,
				<?php
				foreach($fields as $field) {
					if($field->type != 'header') {
						$id = strtolower($field->label);
						$id = str_replace(' ', '_', $id);
						$id = str_replace('-', '_', $id);
						$id = preg_replace('/[^\w-]/', '', $id);
						if($field->type == 'file') {
							echo "'cqpimuploader_" . $id . "' : " . $id . ",";
						} else {
							echo "'" . $id . "' : " . $id . ",";
						}
						if(!empty($field->other) && $field->other == 1) {
							echo "'" . $id . "_other' : " . $id . "_other,";
						}	
					}
				}
				?>
			};
			jQuery.ajax({
				url: '<?php echo admin_url() . 'admin-ajax.php'; ?>',
				data: data,
				type: 'POST',
				dataType: 'json',
				beforeSend: function(){
					spinner.show();
					jQuery('#cqpim_submit_backend').prop('disabled', true);
				},
			}).done(function(response){
				if(response.error == true) {
					spinner.hide();
					jQuery('#cqpim_submit_backend').prop('disabled', false);
					jQuery('#cqpim_submit_backend_messages').html(response.message);
				} else {
					spinner.hide();
					jQuery('#cqpim_submit_backend').prop('disabled', false);
					jQuery('#cqpim_submit_backend_messages').html(response.message);
				}
			});
		});
	});
</script>
<?php } ?>
</div>