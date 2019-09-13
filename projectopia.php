<?php
/**
* Plugin Name: Projectopia
* Description: Projectopia is a solution for freelancers and small agencies who want to manage their clients, quotes, projects and invoices more efficiently. Work individually or as part of a team, and start streamlining your processes!
* Version: 4.1.3
* Author: Projectopia
* Author URI: http://www.projectopia.io
* Text Domain: cqpim
* Domain Path: /languages
*/

define('PTO_VERSION', '4.1.3');
define('PTO_PHP_VERSION', phpversion());
// Paths

define('PTO_PATH', plugin_dir_path( __FILE__ ));
define('PTO_FILE', plugin_dir_path( __FILE__ ) . 'projectopia.php');
define('PTO_FUNCTIONS_PATH', plugin_dir_path( __FILE__ ) . 'includes/functions');	
define('PTO_ADMIN_PATH', plugin_dir_path( __FILE__ ) . 'includes/admin');
define('PTO_CAPS_PATH', plugin_dir_path( __FILE__ ) . 'includes/capabilities');
define('PTO_CONTENT_PATH', plugin_dir_path( __FILE__ ) . 'includes/cpt');
define('PTO_CSS_PATH', plugin_dir_path( __FILE__ ) . 'includes/css');
define('PTO_ENQ_PATH', plugin_dir_path( __FILE__ ) . 'includes/enqueing');
define('PTO_FE_PATH', plugin_dir_path( __FILE__ ) . 'includes/frontend');
define('PTO_INSTALL_PATH', plugin_dir_path( __FILE__ ) . 'includes/install');
define('PTO_META_PATH', plugin_dir_path( __FILE__ ) . 'includes/meta');
define('PTO_SCRIPTS_PATH', plugin_dir_path( __FILE__ ) . 'includes/scripts');
define('PTO_SC_PATH', plugin_dir_path( __FILE__ ) . 'includes/shortcodes');
define('PTO_SETTINGS_PATH', plugin_dir_path( __FILE__ ) . 'includes/settings');
define('PTO_UNINSTALL_PATH', plugin_dir_path( __FILE__ ) . 'includes/uninstall');
define('PTO_DIRNAME', basename( plugin_basename( PTO_PATH ) ));
define('PTO_PLUGIN_URL', plugins_url() . '/' . PTO_DIRNAME);

// Includes
require_once(PTO_PATH . '/languages/languages.php');
require_once(PTO_SETTINGS_PATH . '/settings.php');
require_once(PTO_INSTALL_PATH . '/install.php');
require_once(PTO_UNINSTALL_PATH . '/uninstall.php');
require_once(PTO_ADMIN_PATH . '/admin.php');	
require_once(PTO_FUNCTIONS_PATH . '/functions.php');
require_once(PTO_ENQ_PATH . '/enqueing.php');
require_once(PTO_CAPS_PATH . '/capabilities.php');
require_once(PTO_CONTENT_PATH . '/cpt.php');
require_once(PTO_META_PATH . '/metaboxes.php');
require_once(PTO_SC_PATH . '/shortcodes.php');

// Back Compat

add_action('admin_init', 'pto_v4_compat');
function pto_v4_compat() {
	$checked = get_option('v4_compat_complete');
	if(empty($checked)) {
		$args = array(
			'post_type' => 'cqpim_project',
			'posts_per_page' => -1,
			'post_status' => 'private',
		);
		$projects = get_posts($args);
		foreach($projects as $project) {
			$contract_status = get_post_meta($project->ID, 'contract_status', true);
			if(empty($contract_status)) {
				$contract = pto_get_contract_status($project->ID);
				update_post_meta($project->ID, 'contract_status', $contract);
			}			
		}
		update_option('v4_compat_complete', true);
	}
}

add_action('admin_init', 'pto_v4_1_compat');
function pto_v4_1_compat() {
	$checked = get_option('v4_1_compat_complete');
	if(empty($checked)) {
		update_option('new_lead_email_subject', 'A new Lead has been submitted at %%COMPANY_NAME%%');
		update_option('new_lead_email_content', 'Dear %%TEAM_NAME%%

A new lead has been submitted at %%COMPANY_NAME%%

You can view the lead by clicking this link - %%LEAD_URL%%

Best Regards

%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%');
		$new_page_title = 'Client Register';
		$new_page_content = '';
		$new_page_template = '';
		$page_check = get_option('cqpim_register_page');
		$new_page = array(
				'post_type' => 'page',
				'post_title' => $new_page_title,
				'post_content' => $new_page_content,
				'post_excerpt' => '',
				'post_status' => 'publish',
				'post_author' => 1,
				'post_name' => 'client-register',
				'comment_status' => 'closed'
		);
		if(empty($page_check)) {
			$new_page_id = wp_insert_post($new_page);
			update_option( 'cqpim_register_page', $new_page_id, true );
			if(!empty($new_page_template)){
					update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
			}
		}
		update_option('cqpim_enable_faq', 1);
		update_option('cqpim_faq_slug', 'pto_faq');
		$role = get_role('cqpim_client');
		if(!empty($role)) {
			$role->add_cap('read_private_cqpim_faqs');
		}
		update_option('v4_1_compat_complete', true);
	}
}

function pto_languages_setup() {
	load_plugin_textdomain('cqpim', false, basename( dirname( __FILE__ ) ) . '/languages/frontend');
	load_plugin_textdomain('cqpim', false, basename( dirname( __FILE__ ) ) . '/languages/admin');	
}
add_action('plugins_loaded', 'pto_languages_setup');
