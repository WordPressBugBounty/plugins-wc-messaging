<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woom_Messaging
 * @subpackage Woom_Messaging/includes
 * @author     Sevengits <sevengits@gmail.com>
 */
class Woom_Messaging
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Woom_Messaging_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('woom_version')) {
			$this->version = woom_version;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wc-messaging';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woom_Messaging_Loader. Orchestrates the hooks of the plugin.
	 * - Woom_Messaging_i18n. Defines internationalization functionality.
	 * - Woom_Messaging_Admin. Defines all hooks for the admin area.
	 * - Woom_Messaging_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wc-messaging-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wc-messaging-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wc-messaging-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-wc-messaging-public.php';

		/**
		 * Whatspp Class 
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-whatsapp.php';

		/**
		 * Wcm_Abandoned_Minutes_Checker 
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/abandoned/class-wcm-abandoned-checker.php';
		/**
		 * Delete expired abandoned coupons
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/abandoned/coupon-delete.php';
		/**
		 * Wcm_Send_Abandoned_Status 
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/abandoned/class-wcm-send-abandoned-status.php';

		/**
		 * Notiqoo abandoned dashboard 
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/packages/notiqoo-abandoned-dashboard/notiqoo-abandoned-dashboard.php';


		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/packages/admin-notices/AdminNotice.php';

		/**
		 * Abandoned carts tracking before checkout
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/abandoned/abandoned-cart-tracker.php';


		$this->loader = new Woom_Messaging_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woom_Messaging_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new Woom_Messaging_i18n();
		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Woom_Messaging_Admin($this->get_plugin_name(), $this->get_version());
		if (get_option('woom_abandoned_enable', 'no') === 'yes') {
			$abadonned_status = new notiqoo_abandoned_dashboard();
		}

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_filter('woocommerce_settings_tabs_array', $plugin_admin, 'woom_tab', 50);
		$this->loader->add_filter('woom_tab_subsections', $plugin_admin, 'woom_add_tab_subsections');
		$this->loader->add_action('woocommerce_sections_woom_settings', $plugin_admin, 'woom_action_woocommerce_sections_woom_settings_tab');
		$this->loader->add_filter('woom_subsection_settings', $plugin_admin, 'woom_general_settings');
		$this->loader->add_filter('woom_subsection_settings_templates', $plugin_admin, 'woom_template_settings');
		$this->loader->add_filter('woom_subsection_settings_abandoned_cart', $plugin_admin, 'woom_abandoned_cart_settings');
		$this->loader->add_filter('woom_additional_settings', $plugin_admin, 'woom_tools_settings');
		$this->loader->add_filter('woom_subsection_settings_support', $plugin_admin, 'woom_support_settings');
		$this->loader->add_action('woom_trigger_wa_msg', $plugin_admin, 'woom_trigger_msg', 100, 4);

		$this->loader->add_filter('woom_additional_settings', $plugin_admin, 'woom_custom_actions_settings');
		$this->loader->add_action('wp_ajax_woom_autosave_manual_trigger_actions', $plugin_admin, 'woom_save_custom_template_options');

		$this->loader->add_filter('woom_settings_sidebar', $plugin_admin, 'woom_sidebar_config');

		$this->loader->add_action('wp_ajax_woom_clear_option', $plugin_admin, 'woom_remove_custom_template_options');

		$this->loader->add_action('wp_ajax_woom_manual_trigger_action', $plugin_admin, 'woom_send_manual_msg');

		$this->loader->add_action('wp_dashboard_setup', $plugin_admin, 'woom_add_wc_messaging_overview_widget');

		$this->loader->add_action('wp_ajax_woom_regenerate_wa_templates', $plugin_admin, 'woom_update_wa_templates');

		$this->loader->add_action('wp_ajax_woom_ajax_popup', $plugin_admin, 'woom_callback_popup');

		$this->loader->add_action('woocommerce_settings_woom_settings', $plugin_admin, 'woom_action_woocommerce_settings_woom_settings_tab', 10);
		$this->loader->add_action('woocommerce_settings_save_woom_settings', $plugin_admin, 'woom_action_woocommerce_settings_save_woom_settings_tab', 10);
		$this->loader->add_action('woocommerce_admin_field_woom_config_template_settings', $plugin_admin, 'woom_config_settings', 500);
		$this->loader->add_action('woocommerce_admin_field_woom_hidden', $plugin_admin, 'woom_hidden_settings', 500);
		$this->loader->add_action('woocommerce_admin_field_woom_trigger_button', $plugin_admin, 'woom_trigger_opt_settings', 100);
		$this->loader->add_action('woocommerce_admin_field_woom_info_downloader', $plugin_admin, 'woom_info_downlaoder', 100);
		$this->loader->add_action('woocommerce_admin_field_woom_inline_fields', $plugin_admin, 'woom_inline_fields', 100);
		$this->loader->add_action('woocommerce_admin_field_woom_with_note', $plugin_admin, 'woom_fields_with_note', 100);
		$this->loader->add_action('woocommerce_admin_field_nq_promotion_sidebar', $plugin_admin, 'woom_promotional_sidebar', 500);

		$this->loader->add_action('add_meta_boxes', $plugin_admin, 'woom_action_buttons_meta_box');

		$this->loader->add_action('woocommerce_order_status_changed', $plugin_admin, 'woom_send', 10, 3);

		$this->loader->add_filter('plugin_action_links_' . woom_basename, $plugin_admin, 'woom_links_below_title_begin');
		// # below the plugin title in plugins page. add custom links at the end of list
		$this->loader->add_filter('plugin_action_links_' . woom_basename, $plugin_admin, 'woom_links_below_title_end');
		# below the plugin description in plugins page. add custom links at the end of list
		$this->loader->add_filter('plugin_row_meta', $plugin_admin, 'woom_plugin_description_below_end', 10, 2);


		$this->loader->add_filter('admin_footer_text', $plugin_admin, 'nq_settings_footer_left_text');

		$this->loader->add_filter('update_footer', $plugin_admin, 'nq_settings_footer_version', 99);

		// Abandoned cart hooks
		$this->loader->add_action('woocommerce_add_to_cart', $plugin_admin, 'nq_schedule_abandoned_cart');
		$this->loader->add_action('nq_messaging_check_abandoned', $plugin_admin, 'nq_check_abandoned');
		$this->loader->add_action('init', $plugin_admin, 'nq_remove_old_cron');


		$this->loader->add_action('wp_ajax_send_trigger_sample_to_url', $plugin_admin, 'abandoned_trigger_sample');

		$this->loader->add_action('woom_abandoned_order_created', $plugin_admin, 'woom_abandoned_created_data', 10, 1);
		$this->loader->add_action('woocommerce_order_status_completed', $plugin_admin, 'woom_abandoned_recovery_complete', 15, 2);
		$this->loader->add_action('woom_abandoned_order_recovered', $plugin_admin, 'woom_abadonment_recovery_data', 15, 1);
		$this->loader->add_action('woocommerce_order_status_failed', $plugin_admin, 'woom_abandoned_recovery_lost', 15, 2);

		//hook for delete existing system user
		$this->loader->add_action('admin_init', $plugin_admin, 'woom_delete_wa_system_user', 25);


		$this->loader->add_action('admin_init', $plugin_admin, 'nq_review_admin_notice', 20);
		$this->loader->add_action('wp_ajax_woom_dismiss_review_notice', $plugin_admin, 'nq_review_notice_dismiss');

		// auto delete cron callback
		$this->loader->add_action('wcm_delete_coupons_weekly', $plugin_admin, 'delete_abandoned_coupons');
		$this->loader->add_action('wp_ajax_delete_abandoned_coupons', $plugin_admin, 'woom_delete_abandoned_coupons');

		//$this->loader->add_action('admin_notices', $plugin_admin, 'woom_branding_updated_notice');

		$this->loader->add_action('admin_menu', $plugin_admin, 'woom_wc_submenu');
		// // trigger updates info
		$this->loader->add_action('upgrader_process_complete', $plugin_admin, 'nq_check_in_update', 10, 2);
		$this->loader->add_action('plugins_loaded', $plugin_admin, 'nq_redirect_after_update');

		//Count messages send by Notiqoo
		$this->loader->add_action('woom_whatsapp_msg_sent_admin_success', $plugin_admin, 'nq_update_message_count', 45, 1);
		$this->loader->add_action('woom_whatsapp_msg_sent_success', $plugin_admin, 'nq_update_message_count', 45, 1);
		$this->loader->add_action('nq_message_send', $plugin_admin, 'nq_update_message_count', 45, 4);

		//Hook for update +sign in setting page
		$this->loader->add_action('woom_whatsapp_msg_sent_success', $plugin_admin, 'nq_update_whatsapp_numbers_with_plus', 40, 1);

		// Abandoned cart
		$this->loader->add_action('notiqoo_abandoned_cart_created', $plugin_admin, 'nq_abandoned_notification_init', 50, 1);
		$this->loader->add_action('nq_process_abandoned_scheduled', $plugin_admin, 'nq_trigger_abandoned_scheduled');
	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Woom_Messaging_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_filter('woocommerce_checkout_fields', $plugin_public, 'woom_enable_notification_wc_classic_checkouts');
		$this->loader->add_action('woocommerce_init', $plugin_public, 'woom_enable_notification_wc_block_checkouts');
		$this->loader->add_action('woocommerce_after_checkout_validation', $plugin_public, 'woom_verify_phone_number', 50, 2);
		$this->loader->add_action('woocommerce_init', new Notiqoo_Abandoned_Cart_Tracker(), 'init');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Woom_Messaging_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
