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
		'suddenly-stopped-working'	=> __('The plugin suddenly stopped working', 'wc-messaging'),
		'plugin-broke-site'			=> __('The plugin broke my site', 'wc-messaging'),
		'no-longer-needed'			=> __('I don\'t need this plugin any more', 'wc-messaging'),
		'found-better-plugin'		=> __('I found a better plugin', 'wc-messaging'),
		'temporary-deactivation'	=> __('It\'s a temporary deactivation, I\'m troubleshooting', 'wc-messaging'),
		'other'						=> __('Other', 'wc-messaging')
	);

	foreach ($plugins as $plugin) {
		$plugin->reasons = apply_filters('sgits_deactivate_feedback_form_reasons', $defaultReasons, $plugin);
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
