<?php
// Frontend Quote Form
function pto_frontend_lead_form($atts) {
	$form_id = isset($atts['id']) ? $atts['id'] : '';
	if(empty($form_id)) {
		return _e('You have not added a Form ID to the shortcode!', 'cqpim');
	}
	$form_type = get_post_meta($form_id, 'form_type', true);
	if(!empty($form_type) && $form_type == 'gf') {
		$gravity_form = get_post_meta($form_id, 'gravity_form', true);
		if(empty($gravity_form)) {
			return _e('This form has been configured to use a Gravity Form, but you have not selected a Gravity Form to use.', 'cqpim');
		} else {
			return do_shortcode('[gravityform id="' . $gravity_form . '"]');
		}
		return;
	} elseif(!empty($form_type) && $form_type == 'cqpim') {
		wp_enqueue_script('pto_form_upload');
		wp_localize_script('pto_form_upload', 'localisation', pto_return_localisation());
		global $post;
		update_option('cqpim_form_page', $post->ID, true);
		$form_data = get_post_meta($form_id, 'builder_data', true);
		if(is_array($form_data)) {
			$form_data = '';
		}
		__('I am a Human (SPAM check)', 'cqpim');
		$form_data = json_decode($form_data);
		$fields = $form_data;
		$code =  '<div id="cqpim_frontend_form_cont">';
		$code .= '<form id="cqpim_frontend_leadform">';
		if(!empty($fields)) {
			$i = 0;
			foreach($fields as $field) {
				$id = strtolower($field->label);
				$id = str_replace(' ', '_', $id);
				$id = str_replace('-', '_', $id);
				$id = preg_replace('/[^\w-]/', '', $id);
				if(!empty($field->required) && $field->required == 1) {
					$required = 'required';
					$ast = ' <span style="color:#F00">*</span>';
				} else {
					$required = '';
					$ast = '';
				}
				$code .= '<input id="leadform_id" type="hidden" value="' . $form_id . '" />';
				$code .= '<div style="padding-bottom:12px" class="cqpim_form_item">';
				if($field->type != 'header') {
					$code .= '<label style="display:block; padding-bottom:5px" for="' . $id . '">' . $field->label . ' ' . $ast . '</label>';
				}
				if($field->type == 'header') {
					$code .= '<' . $field->subtype . ' class="' . $field->className . '">' . $field->label . '</' . $field->subtype . '>';
				} elseif($field->type == 'text') {
					$code .= '<input style="width:100%" class="' . $field->className . '" type="text" id="' . $id . '" ' . $required . ' />';
				} elseif($field->type == 'website') {
					$code .= '<input style="width:100%" class="' . $field->className . '" type="url" id="' . $id . '" ' . $required . ' />';
				} elseif($field->type == 'number') {
					$code .= '<input style="width:100%" class="' . $field->className . '" type="number" id="' . $id . '" ' . $required . ' />';
				} elseif($field->type == 'textarea') {
					$code .= '<textarea class="' . $field->className . '" style="width:100%; height:140px" id="' . $id . '" ' . $required . '></textarea>';
				} elseif($field->type == 'date') {
					$code .= '<input class="' . $field->className . '" style="width:100%" type="date" id="' . $id . '" ' . $required . ' />';
				} elseif($field->type == 'email') {
					$code .= '<input class="' . $field->className . '" style="width:100%" type="email" id="' . $id . '" ' . $required . ' />';
				} elseif($field->type == 'file') {	
					$multiple = isset($field->multiple) ? 'true' : 'false';
					$code .= '<input type="file" class="cqpim-file-upload-form" name="async-upload" id="' . $id . '" data-multiple="' . $multiple . '"/>';
					$code .= '<div id="upload_messages_' . $id . '"></div>';
					$code .= '<input type="hidden" name="image_id" id="upload_' . $id . '">';
					$code .= '<div class="clear"></div>';
				} elseif($field->type == 'checkbox-group') {
					$options = $field->values;
					foreach($options as $option) {
						$code .= '<input class="' . $field->className . '" type="checkbox" value="' . $option->value . '" name="' . $id . '" /> ' . $option->label . '<br />';
					}
				} elseif($field->type == 'radio-group') {
					$options = $field->values;
					foreach($options as $option) {
						$code .= '<input class="' . $field->className . '" type="radio" value="' . $option->value . '" name="' . $id . '" ' . $required . ' /> ' . $option->label . '<br />';
					}
				} elseif($field->type == 'select') {
					$options = $field->values;
					$code .= '<select class="' . $field->className . '" id="' . $id . '" ' . $required . '>';
						foreach($options as $option) {
							$code .= '<option value="' . $option->value . '">' . $option->label . '</option>';
						}
					$code .= '</select>';
				}
				if(!empty($field->other) && $field->other == 1) {
					echo '<br />';
					$code .= __('Other:', 'cqpim') . '<input style="width:100%" type="text" id="' . $id . '_other" />';
				}
				if(!empty($field->description)) {
					$code .= '<p>' . $field->description . '</p>';
				}
				$code .= '</div>';
				$i++;
			}
		} else {
			$code .= '';
		}
		$code .= '<input type="submit" id="cqpim_submit_frontend" value="' . __('Submit', 'cqpim') . '" /><br /><div id="form_spinner" style="clear:both; display:none; background:url(' . PTO_PLUGIN_URL . '/img/ajax-loader.gif) center center no-repeat; width:16px; height:16px; padding:10px 0 0 5px; margin-top:15px"></div>';
		$code .= wp_nonce_field('image-submission');
		$code .= '<div style="margin-top:20px" id="cqpim_submit_frontend_messages"></div>';
		$code .= '</form>';
		$code .= '</div>';
		pto_frontend_leadform_scripts($form_id);
		return $code;
	} else {
		return _e('Form ID does not exist!', 'cqpim');
	}
}
add_shortcode('projectopia_lead_form', 'pto_frontend_lead_form');
function pto_frontend_leadform_scripts($form_id) {
	$form_data = get_post_meta($form_id, 'builder_data', true);
	if(empty($form_data)) {
		$form_data = '';
	}
	$form_data = json_decode($form_data);
	$fields = $form_data;
	if(empty($fields)) {
		$fields = array();
	}
	?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#cqpim_submit_frontend').before('<div style="padding-bottom:12px" class="cqpim_form_item"> <?php _e('I am a Human (SPAM check)', 'cqpim'); ?><span style="color:#F00">*</span> <input type="checkbox" id="human_conf" required /></div>');
			jQuery('#cqpim_frontend_leadform').on('submit', function(e) {
				e.preventDefault();
				var spinner = jQuery('#form_spinner');
				var leadform_id = jQuery('#leadform_id').val();
				<?php
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
					} elseif($field->type == 'checkbox-group') {
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
					'action' : 'pto_frontend_lead_submission',
					'leadform_id' : leadform_id,
					<?php
					foreach($fields as $field) {
						$id = strtolower($field->label);
						$id = str_replace(' ', '_', $id);
						$id = str_replace('-', '_', $id);
						$id = preg_replace('/[^\w-]/', '', $id);
						if($field->type != 'header') {
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
						jQuery('#cqpim_submit_frontend').prop('disabled', true);
					},
				}).always(function(response) {
					console.log(response);
				}).done(function(response){
					if(response.error == true) {
						spinner.hide();
						jQuery('#cqpim_submit_frontend').prop('disabled', false);
						jQuery('#cqpim_submit_frontend_messages').html(response.message);
					} else {
						spinner.hide();
						jQuery('#cqpim_submit_frontend').prop('disabled', false);
						jQuery('#cqpim_submit_frontend_messages').html(response.message);
					}
				});
			});
		});
	</script>	
	<?php
}