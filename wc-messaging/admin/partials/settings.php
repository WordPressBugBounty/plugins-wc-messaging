<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$settings = array(
	array(
		'id'	=> 'woom_tab',
		'type' => 'title',
		'name' => __('WC Messaging', 'wc-messaging'),
		'desc'	=>	__('WC Messaging configuration', 'wc-messaging'),
	),
	array(
		'id' => 'woom_whatsapp_api',
		'type' => 'text',
		'name' => __('Whatsapp Access Token', 'wc-messaging'),
		'desc' => sprintf('%s <a target="_blank" href="%s">Learn more</a>', __('Access token will be get from WhatsApp API setup.', 'wc-messaging'), 'https://sevengits.com/docs/wc-messaging-pro/'),
		'desc_tip'	=> false
	),
	array(
		'id' => 'woom_whatsapp_number',
		'type' => 'text',
		'placeholder' => __('91xxxxxxxxxx', 'wc-messaging'),
		'name' => __('Whatsapp number', 'wc-messaging'),
		'desc' => __('Whatsapp number of used in meta account.', 'wc-messaging'),
		'desc_tip'	=> false
	),


	array(
		'id' => 'woom_whatsapp_number_id',
		'type' => 'text',
		'name' => __('Phone number ID', 'wc-messaging'),
		'placeholder' => __('112xxxxxxxxx112', 'wc-messaging'),
		'desc' => sprintf('%s <a target="_blank" href="%s">Learn more</a>', __('Unique phone number id given by whatspp.', 'wc-messaging'), 'https://sevengits.com/docs/wc-messaging-pro/'),
		'desc_tip'	=> false,
		'default' => ''
	),

	array(
		'id'	=> 'woom_validate_phone_number',
		'type'	=> 'checkbox',
		"name" => __("Phone number validation", 'wc-messaging'),
		'default'	=> 'yes',
		'options' => array('no' => 'False', 'yes' => 'true'),
		'desc' => __('enable phone number validation at checkout', 'wc-messaging'),
		'desc_tip'	=> false,
	),
	array(
		'id' => 'woom_enable_whatsapp_updates',
		'type' => 'woom_inline',
		'name' => __('Enable opt-in checkbox', 'wc-messaging'),
		'desc' => __('Add an opt-in checkbox for WhatsApp notifications during checkout.', 'wc-messaging'),
		'desc_tip'	=> true,
		'options' => array(
			array(
				'id' => 'woom_enable_checkbox_status',
				'type' => 'checkbox',
				'name' => __('on/off', 'wc-messaging'),
				'default' => true,

			),
			array(
				'id' => 'woom_enable_checkbox_label',
				'type' => 'text',
				'name' => __('checkout field name', 'wc-messaging'),
				'placeholder' => __('checkout field name', 'wc-messaging'),
				'default' => 'Get order updates on whatsapp',

			),
			array(
				'id' => 'woom_enable_checkbox_priority',
				'type' => 'number',
				'name' => __('Priority', 'wc-messaging'),
				'placeholder' => __('enable notification field priority', 'wc-messaging'),
				'default' => 101,

			),
		)
	),

	array(
		'id'	=> 'woom_api',
		'type'	=> 'sectionend',
		'name'	=> 'end_section',
	),
	array(
		'id' => 'woom_woocommerce',
		'type' => 'woom_config_per_status',
		'name' => __('Woocommerce', 'wc-messaging'),
		'fields' => $this->get_settings_statuses('woom_woocommerce_config_per_status', wc_get_order_statuses()),
		'desc_tip'	=> true
	),

	array(
		'id' => 'woom_settings_nonce',
		'type' => 'hidden',
		'value' => wp_create_nonce('woom-settings-post-data'),
		'desc_tip'	=> false
	),
	array(
		'type'	=> 'sectionend',
		'name'	=> 'end_section',
		'id'	=> 'woom_settings'
	),
);
