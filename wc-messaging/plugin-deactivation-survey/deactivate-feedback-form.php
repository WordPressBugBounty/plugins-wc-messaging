<?php

namespace codecabin;

if (!defined('ABSPATH')) {
	exit;
}
if (!is_admin())
	return;

global $pagenow;

if ($pagenow != "plugins.php")
	return;

if (defined('SGITS_DEACTIVATE_FEEDBACK_FORM_INCLUDED'))
	return;
define('SGITS_DEACTIVATE_FEEDBACK_FORM_INCLUDED', true);

add_action('admin_enqueue_scripts', function () {

	// Enqueue scripts
	if (!wp_script_is('sgits-remodal-js', 'enqueued')) {
		wp_enqueue_script('sgits-remodal-js', plugin_dir_url(__FILE__) . 'remodal.min.js', array(), '1.0.0', true);
	}

	if (!wp_style_is('sgits-remodal-css', 'enqueued')) {
		wp_enqueue_style('sgits-remodal-css', plugin_dir_url(__FILE__) . 'remodal.css', array(), '1.0.0', 'all');
	}

	if (!wp_style_is('remodal-default-theme', 'enqueued'))
		wp_enqueue_style('remodal-default-theme', plugin_dir_url(__FILE__) . 'remodal-default-theme.css', array(), '1.0.0', 'all');

	if (!wp_script_is('sgits-deactivate-feedback-form-js', 'enqueued'))
		wp_enqueue_script('sgits-deactivate-feedback-form-js', plugin_dir_url(__FILE__) . 'deactivate-feedback-form.js', array(), '1.0.0', true);

	if (!wp_script_is('sgits-deactivate-feedback-form-css', 'enqueued'))
		wp_enqueue_style('sgits-deactivate-feedback-form-css', plugin_dir_url(__FILE__) . 'deactivate-feedback-form.css', array(), '1.0.0', 'all');

	// Localized strings
	wp_localize_script('sgits-deactivate-feedback-form-js', 'sgits_deactivate_feedback_form_strings', array(
		'quick_feedback'			=> __('Quick Feedback', 'wc-messaging'),
		'foreword'					=> __('If you would be kind enough, please tell us why you\'re deactivating?', 'wc-messaging'),
		'better_plugins_name'		=> __('Please tell us which plugin?', 'wc-messaging'),
		'please_tell_us'			=> __('Please tell us the reason so we can improve the plugin', 'wc-messaging'),
		'do_not_attach_email'		=> __('Do not send my e-mail address with this feedback', 'wc-messaging'),

		'brief_description'			=> __('Please give us any feedback that could help us improve', 'wc-messaging'),

		'cancel'					=> __('Cancel', 'wc-messaging'),
		'skip_and_deactivate'		=> __('Skip &amp; Deactivate', 'wc-messaging'),
		'submit_and_deactivate'		=> __('Submit &amp; Deactivate', 'wc-messaging'),
		'please_wait'				=> __('Please wait', 'wc-messaging'),
		'thank_you'					=> __('Thank you!', 'wc-messaging')
	));

	// Plugins
	$plugins = apply_filters('sgits_deactivate_feedback_form_plugins', array());

	// Reasons
	$defaultReasons = array(
		'connection-setup-difficulty'	=> __('WhatsApp connection/setup is difficult', 'wc-messaging'),
		'whatsapp-messages-not-sending' => __('WhatsApp messages are not sending', 'wc-messaging'),
		'plugin-stopped-working'	=> __('Plugin stopped working', 'wc-messaging'),
		'plugin-conflicts'			=> __('Plugin conflicts with my website', 'wc-messaging'),
		'meta-config-complexity'		=> __('I couldn\'t configure Meta/WhatsApp', 'wc-messaging'),
		'missing-feature'			=> __('Missing a feature I need', 'wc-messaging'),
		'too-complicated'			=> __('Too complicated', 'wc-messaging'),
		'found-better-plugin'		=> __('I found another plugin', 'wc-messaging'),
		'no-longer-needed'			=> __('I don\'t need WhatsApp anymore', 'wc-messaging'),
		'temporary-troubleshooting'	=> __('I\'m temporarily troubleshooting', 'wc-messaging'),
		'other'						=> __('Other - please tell us', 'wc-messaging')
	);
	
	// Server Info
	$web_server = (!empty(sanitize_text_field(wp_unslash($_SERVER['SERVER_SOFTWARE'])))) ? sanitize_text_field(wp_unslash($_SERVER['SERVER_SOFTWARE'])) : 'Not available';

	// WordPress Info
	$wp_version = get_bloginfo('version');
	$multisite = is_multisite();
	$multisite_site_count = function_exists('get_blog_count') ? get_blog_count() : 'N/A';
	$wp_locale = get_locale();

	// PHP Info
	$php_version = phpversion();
	$php_memory_limit = ini_get('memory_limit');
	$wp_memory_limit = WP_MEMORY_LIMIT;

	// Construct the diagnostic info
	$diagnosis_info = array();
	$diagnosis_info['diagnosis'] = array(
		'Web Server' => $web_server,
		'WordPress' => $wp_version . ($multisite ? "Multisite (subdirectory)" : ""),
		'Multisite Site Count' => $multisite_site_count,
	);
	$diagnosis_info['site_info'] = array(
		'WP Locale' => $wp_locale,
		'PHP' => $php_version,
		'PHP Memory Limit' => $php_memory_limit,
		'WP Memory Limit' => $wp_memory_limit
	);
	$active_theme_data = array(get_template() => array(
		'name' => wp_get_theme()->name,
	));
	if (!empty(wp_get_theme()->parent_theme)) {
		$active_theme_data[get_template()]['Parent'] = wp_get_theme()->parent_theme;
	}
	$active_theme_data[get_template()]['version'] = wp_get_theme()->version;
	$diagnosis_info['theme'] = $active_theme_data;
	$diagnosis_info['active_plugins'] = array_reduce(get_option('active_plugins', []), function ($result, $plugin) {
		$data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);

		$result[$data['TextDomain']] = [
			'name' => $data['Name'],
			'version' => $data['Version'],
		];

		return $result;
	}, []);
	foreach ($plugins as $plugin) {
		$plugin->reasons = apply_filters('sgits_deactivate_feedback_form_reasons', $defaultReasons, $plugin);
		$plugin->diagnosis_data = json_encode($diagnosis_info);
	}

	// Send plugin data
	wp_localize_script('sgits-deactivate-feedback-form-js', 'sgits_deactivate_feedback_form_plugins', $plugins);
	wp_localize_script('sgits-deactivate-feedback-form-js', 'sgits_deactivate_feedback_logo', array(
		'url' => esc_url(plugin_dir_url(__FILE__) . 'notiqoo.png')
	));
});

/**
 * Hook for adding plugins, pass an array of objects in the following format:
 *  'slug'		=> 'plugin-slug'
 *  'version'	=> 'plugin-version'
 * @return array The plugins in the format described above
 */
add_filter('sgits_deactivate_feedback_form_plugins', function ($plugins) {
	return $plugins;
});
