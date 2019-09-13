<?php
function pto_client_fields_metabox_callback( $post ) {
 	wp_nonce_field( 
	'client_fields_metabox', 
	'client_fields_metabox_nonce' );
	$data = get_option('cqpim_custom_fields_client');
	$data = str_replace('\"', '"', $data);
	if(!empty($data)) {
		$form_data = json_decode($data);
		$fields = $form_data;
	}
	$values = get_post_meta($post->ID, 'custom_fields', true);
	$frontend = get_post_meta($post->ID, 'field_frontend', true);
	if(!empty($fields)) {
		echo '<div id="cqpim-custom-fields">';
		echo '<table class="cqpim_table">';
		echo '<tr><th style="width:80px">' . __('Frontend', 'cqpim') . ' <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="' . __('Make this field available in the Client Dashboard so that the client can view / edit it.', 'cqpim') . '"></i></th><th>' . __('Field Value', 'cqpim') . '</th></tr>';
		$i = 0;
		foreach($fields as $key => $field) {
			$value = isset($values[$field->name]) ? $values[$field->name] : '';
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
			if($field->type == 'header') {
				$field_fe = isset($frontend['header-' . $i]) ? $frontend['header-' . $i] : 0;
				echo '<tr><td><input type="checkbox" name="field_frontend[header-' . $i . ']" value="1" ' . checked($field_fe, 1, false) . ' /></td>';
			} else {
				$field_fe = isset($frontend[$field->name]) ? $frontend[$field->name] : 0;
				echo '<tr><td><input type="checkbox" name="field_frontend[' . $field->name . ']" value="1" ' . checked($field_fe, 1, false) . ' /></td>';
			}		
			
			echo '<td>';
			echo '<div style="padding-bottom:12px" class="cqpim_form_item">';
			if($field->type != 'header') {
				echo '<label style="display:block; padding-bottom:5px" for="' . $id . '">' . $field->label . ' ' . $ast . '</label>';
			}
			if($field->type == 'header') {
				echo '<' . $field->subtype . ' class="' . $field->className . '">' . $field->label . '</' . $field->subtype . '>';
				echo '<input type="hidden" class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[header-' . $i . ']" value="' . $field->label. '" />';
				$i++;
			} elseif($field->type == 'text') {			
				echo '<input type="text" class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'website') {
				echo '<input type="url" class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'number') {
				echo '<input type="number" class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'textarea') {
				echo '<textarea class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']">' . $value . '</textarea>';
			} elseif($field->type == 'date') {
				echo '<input class="' . $field->className . ' datepicker" type="text" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'email') {
				echo '<input type="email" class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']" value="' . $value . '" />';
			} elseif($field->type == 'checkbox-group') {
				if(!is_array($value)) {
					$value = array($value);
				}
				$options = $field->values;
				foreach($options as $option) {
					if(in_array($option->value, $value)) {
						$checked = 'checked="checked"';
					} else {
						$checked = '';
					}
					echo '<input type="checkbox" class="' . $field->className . '" value="' . $option->value . '" name="custom-field[' . $field->name . '][]" ' . $checked . ' /> ' . $option->label . '<br />';
				}
			} elseif($field->type == 'radio-group') {
				$options = $field->values;
				foreach($options as $option) {
					if($value == $option->value) {
						$checked = 'checked="checked"';
					} else {
						$checked = '';
					}
					echo '<input type="radio" class="' . $field->className . '" value="' . $option->value . '" name="custom-field[' . $field->name . ']" ' . $required . ' ' . $checked . ' /> ' . $option->label . '<br />';
				}
			} elseif($field->type == 'select') {
				$options = $field->values;
				echo '<select class="' . $field->className . '" id="' . $id . '" ' . $required . ' name="custom-field[' . $field->name . ']">';
					foreach($options as $option) {	
						if($value == $option->value) {
							$checked = 'selected="selected"';
						} else {
							$checked = '';
						}
						echo '<option value="' . $option->value . '" ' . $checked . '>' . $option->label . '</option>';
					}
				echo '</select>';
			}
			if(!empty($field->other) && $field->other == 1) {
				echo '<br />';
				echo __('Other:', 'cqpim') . '<input style="width:100%" type="text" id="' . $id . '_other" name="custom-field[' . $field->name . '_other]" />';
			}
			if(!empty($field->description)) {
				echo '<span class="cqpim-field-description">' . $field->description . '</span>';
			}
			echo '</div>';
			echo '</td></tr>';
		}
		echo '</table></div>';
	}
}
add_action( 'save_post', 'save_pto_client_fields_metabox_data' );
function save_pto_client_fields_metabox_data( $post_id ){
	if ( ! isset( $_POST['client_fields_metabox_nonce'] ) )
	    return $post_id;
	$nonce = $_POST['client_fields_metabox_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'client_fields_metabox' ) )
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
	$data = $_POST;
	if(!empty($data['custom-field'])) {
		update_post_meta($post_id, 'custom_fields', $data['custom-field']);
	}
	update_post_meta($post_id, 'field_frontend', $data['field_frontend']);
}