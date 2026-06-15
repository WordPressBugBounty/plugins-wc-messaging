<?php

/**
 * @link              https://notiqoo.com/
 * @since             1.0.0
 * @package           Woom_Messaging
 *
 * @wordpress-plugin
 * Plugin Name:       Notiqoo
 * Plugin URI:        https://notiqoo.com/
 * Description:       Send WhatsApp notifications for Woocommerce orders using  official WhatsApp Cloud APIs.
 * Version:           1.4.11
 * Author:            Notiqoo
 * Author URI:        https://notiqoo.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-messaging
 * Domain Path:       /languages
 * Requires Plugins: woocommerce
 * WC Requires at least: 3.7
 * WC Tested up to:      10.8
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

if (function_exists('is_plugin_active') && is_plugin_active('notiqoo-pro/notiqoo-pro.php')) {
	wp_die(sprintf('%1$s <a href="%2$s">%3$s</a>', esc_html__('Please deactivate Notiqoo PRO and activate the free version.', 'wc-messaging'), esc_url(admin_url('/') . 'plugins.php'), esc_html__('Back to plugins', 'wc-messaging')), 'wc-messaging');
}
if (function_exists('is_plugin_active') && is_plugin_active('notiqoo/notiqoo.php')) {
	wp_die(sprintf('%1$s <a href="%2$s">%3$s</a>', esc_html__('Please deactivate Notiqoo and activate the free version.', 'wc-messaging'), esc_url(admin_url('/') . 'plugins.php'), esc_html__('Back to plugins', 'wc-messaging')), 'wc-messaging');
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if (!defined('woom_plugin_name')) {
	define('woom_plugin_name', 'WC Messaging');
}
if (!defined('woom_version')) {
	define('woom_version', '1.4.11');
}

if (!defined('woom_basename')) {
	define('woom_basename', plugin_basename(__FILE__));
}

if (!defined('woom_dir_url')) {
	define( 'woom_dir_url', plugin_dir_url( __FILE__ ) );

}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-messaging-activator.php
 */

if (!function_exists('woom_activate')) {
	function woom_activate()
	{
		require_once plugin_dir_path(__FILE__) . 'includes/class-wc-messaging-activator.php';
		Woom_Messaging_Activator::activate();
	}
	register_activation_hook(__FILE__, 'woom_activate');
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-messaging-deactivator.php
 */
if (!function_exists('woom_deactivate')) {
	function woom_deactivate()
	{
		require_once plugin_dir_path(__FILE__) . 'includes/class-wc-messaging-deactivator.php';
		Woom_Messaging_Deactivator::deactivate();
	}
	register_activation_hook(__FILE__, 'woom_deactivate');
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wc-messaging.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

if (!function_exists('woom_run')) {
	function woom_run()
	{

		$plugin = new Woom_Messaging();
		$plugin->run();
	}

	if (
		function_exists('woom_run') &&
		in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))
	) {
		woom_run();
	}
}


/**
 * Feedback survey when deactivate the plugin
 */
require plugin_dir_path(__FILE__) . 'plugin-deactivation-survey/deactivate-feedback-form.php';
add_filter('sgits_deactivate_feedback_form_plugins', 'woom_deactivate_feedback');
function woom_deactivate_feedback($plugins)
{
	$plugins[] = (object)array(
		'slug'		=> 'wc-messaging',
		'version'	=> woom_version
	);
	return $plugins;
}

/**
 * Custom function to declare compatibility with cart_checkout_blocks feature
 * @since 1.0.0
 */
if (!function_exists('woom_declare_cart_checkout_blocks_compatibility')) {

	function woom_declare_cart_checkout_blocks_compatibility()
	{
		// Check if the required class exists
		if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
		}
	}

	add_action('before_woocommerce_init', 'woom_declare_cart_checkout_blocks_compatibility');
}

add_action('before_woocommerce_init', function () {
	if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
	}
});
