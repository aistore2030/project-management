<?php
add_action( 'admin_init', 
	'pto_import_default_settings');	
function pto_import_default_settings() {
	include('config_template.php');
	$installed = get_option('cqpim_settings_imported');
	$company = get_option('company_name');
	$welcome = get_option('auto_welcome_subject');
	if(empty($installed) && empty($company) && empty($welcome)) {
		$settings = pto_settings_values();
		foreach($settings as $element) {
			foreach($element as $key => $setting) {
				update_option($key, $setting);
			}
		}
	}
}