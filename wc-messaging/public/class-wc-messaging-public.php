<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woom_Messaging
 * @subpackage Woom_Messaging/public
 * @author     Sevengits <sevengits@gmail.com>
 */
class Woom_Messaging_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woom_Messaging_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woom_Messaging_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wc-messaging-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woom_Messaging_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woom_Messaging_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wc-messaging-public.js', array('jquery'), $this->version, false);
	}


	function woom_enable_notification_wc_classic_checkouts($fields)
	{
		if (get_option('woom_order_notification_permission', 'enable') !== 'disable') {
			if (in_array(get_option('woom_send_order_notification', 'all'), array('all', 'billing'))) {
				$fields['billing']['billing_woom_notification'] = array(
					"label" => esc_html(get_option('woom_opt_in_checkbox_label', 'Get order updates on whatsapp')),
					"type" => "checkbox",
					"required" => false,
					"class" => array("form-row-wide"),
					"default" => true,
					"priority" => get_option('woom_enable_checkbox_priority', 101)
				);
			}
			if (in_array(get_option('woom_send_order_notification', 'all'), array('all', 'shipping'))) {
				$fields['shipping']['shipping_woom_notification'] = array(
					"label" => esc_html(get_option('woom_opt_in_checkbox_label', 'Get order updates on whatsapp')),
					"type" => "checkbox",
					"required" => false,
					"class" => array("form-row-wide"),
					"default" => true,
					"priority" => get_option('woom_enable_checkbox_priority', 101)
				);
			}
		}
		return $fields;
	}


	function woom_enable_notification_wc_block_checkouts()
	{
		$func_name = 'woocommerce_register_additional_checkout_field';
		if (function_exists($func_name)) {
			if (get_option('woom_order_notification_permission', 'enable') !== 'disable' && get_option('woom_send_order_notification', 'all') !== 'disable') {
				$is_notify_order_updates = get_option('woom_send_order_notification', 'all');
				woocommerce_register_additional_checkout_field(
					array(
						'id'       => 'namespace/woom_notification',
						'label'    => esc_html(get_option('woom_opt_in_checkbox_label', 'Get order updates on whatsapp')),
						'location' => 'address',
						'type'     => 'checkbox',
						'optionalLabel' => esc_html(get_option('woom_opt_in_checkbox_label', 'Get order updates on whatsapp'))
					)
				);
			}
		}
	}

	function woom_verify_phone_number($fields, $errors)
	{
		if (get_option('woom_validate_phone_number', 'yes') !== 'no') {
			$calling_code = WC()->countries->get_country_calling_code($fields['billing_country']);
			$billing_number = $fields['billing_phone'];
			$is_invalid = $calling_code === str_replace(" ", "", $billing_number);
			$is_invalid = $is_invalid || !str_starts_with($billing_number, $calling_code);
			$is_invalid = $is_invalid || strlen($calling_code) >= strlen($billing_number);

			if ($is_invalid) {
				$errors->add('billing_phone_field_empty', __('Please enter valid phone number', 'wc-messaging'));
			}
			if (str_contains($billing_number, ' ')) {
				$errors->add('billing_phone_field_whitespaces', __('Whitespaces not allowed in phone number', 'wc-messaging'));
			}
		}
	}


	function woom_wa_noitfy_action($order_id)
	{

		$order = wc_get_order($order_id);
		$woom_send_order_notification = get_option('woom_send_order_notification', 'all'); // billing | shipping | all | disable
		$is_admin_notification_disabled = ($woom_send_order_notification === 'disable');
		$is_customer_billing_notification_disabled = true;
		$is_customer_shipping_notification_disabled = true;
		$is_customer_billing_notification_disabled = true;
		$is_customer_shipping_notification_disabled = true;
		if ($is_admin_notification_disabled) {
			$order->add_order_note(__("Notiqoo: Notification disabled by Admin", 'wc-messaging'), $is_customer_note = 0, $added_by_user = false);
		} else if (get_option('woom_order_notification_permission', 'enable') !== 'disable') {
			if (in_array($woom_send_order_notification, array('all', 'billing'))) {
				if (!empty($order->get_meta('_billing_woom_notification'))) {
					$is_customer_billing_notification_disabled = false;
				}
				if ($order->get_meta('_additional_billing_fields', true)) {
					if (!empty($order->get_meta('_additional_billing_fields')['namespace/woom_notification'])) {
						$is_customer_billing_notification_disabled =  false;
					}
				}
			}
			if (in_array($woom_send_order_notification, array('all', 'shipping'))) {
				if (!empty($order->get_meta('_shipping_woom_notification'))) {
					$is_customer_shipping_notification_disabled = false;
				}
				if ($order->get_meta('_additional_shipping_fields', true)) {
					if (!empty($order->get_meta('_additional_shipping_fields')['namespace/woom_notification'])) {
						$is_customer_shipping_notification_disabled =  false;
					}
				}
			}
			if ($is_customer_billing_notification_disabled) {
				$order->add_order_note(esc_html__("Notiqoo: Notification to billing number disabled by Customer", 'wc-messaging'), $is_customer_note = 0, $added_by_user = false);
			}
			if ($is_customer_shipping_notification_disabled) {
				$order->add_order_note(esc_html__("Notiqoo: Notification to shipping number disabled by Customer", 'wc-messaging'), $is_customer_note = 0, $added_by_user = false);
			}
		}
	}
}
?>