<?php

if (!defined('ABSPATH')) {
	exit;
}
$new_settings = array(
	array(
		'id'	=> 'woom_template_wc_tab',
		'type' => 'title',
		'name' => __('Woocommerce', 'wc-messaging'),
	),
	array(
		'id' => 'woom_woocommerce',
		'type' => 'woom_config_template_settings',
		'name' => __('Woocommerce', 'wc-messaging'),
		'fields' => $this->get_settings_statuses('woom_woocommerce_config_per_status', wc_get_order_statuses()),
		'desc_tip'	=> true,
		'toggle_prefixes' => $this->woom_get_trigger_actions('woocommerce'),
		'toggle_settings' => array(
			array(
				'id' => 'coupon_enabled',
				'type' => 'checkbox',
				'name' => __('Create coupon', 'wc-messaging'),
				'desc' => __('Allows you to send new coupon only for this template.', 'wc-messaging'),
				'desc_tip' => false,
				'data-toggler' => true,
				'data-labels' => array('fixed' => __('Fixed coupon code', 'wc-messaging'), 'dynamic' => __('Dynamic coupon code', 'wc-messaging')),
				'custom_attributes' => array(
					'onchange' => 'woom_toggle_coupon_type(event)',
				)
			),
			array(
				'id' => 'coupon_code',
				'type' => 'text',
				'name' => __('Coupon code', 'wc-messaging'),
				'custom_attributes' => array(
					'style' => 'display: none',
				)
			),
			array(
				'id' => 'coupon_discount_type',
				'type' => 'select',
				'name' => __('Discount type', 'wc-messaging'),
				'options' => array(
					'percent' => __('Percentage discount', 'wc-messaging'),
					'fixed_cart' => __('Fixed discount', 'wc-messaging'),
				)
			),
			array(
				'id' => 'coupon_amount',
				'type' => 'number',
				'name' => __('Coupon amount', 'wc-messaging'),
			),
			array(
				'id' => 'coupon_expiry',
				'type' => 'woom_inline_fields',
				'fields' => array(
					array(
						'id' => 'coupon_expiry_duration',
						'type' => 'number',
					),
					array(
						'id' => 'coupon_expiry_duration_unit',
						'type' => 'select',
						'default' => 'minutes',
						'options' => array(
							'minutes' => __('Minute(s)', 'wc-messaging'),
							'hours' => __('Hour(s)', 'wc-messaging'),
							'days' => __('Day(s)', 'wc-messaging'),
						),
						'desc' => __('Enter zero to restrict coupon from expiring', 'wc-messaging'),
						'desc_tip' => false
					)
				),
				'name' => __('Coupon expiry date', 'wc-messaging')
			),
			array(
				'id' => 'coupon_individual',
				'name' => __('Individual use only', 'wc-messaging'),
				'type' => 'checkbox',
				'desc' => __('Check this box if the coupon can not be used in conjuction with other coupons.', 'wc-messaging')
			),
			array(
				'id' => 'coupon_auto_apply',
				'name' => __('Auto apply coupon', 'wc-messaging'),
				'type' => 'checkbox',
				'desc' => __('Automatically add the coupon to the cart at the checkout.', 'wc-messaging')
			)
		),

	),
	array(
		'id' => 'woom_nonce',
		'type' => 'woom_hidden',
		'value' => wp_create_nonce('woom-template-settings')
	),
	array(
		'id'	=> 'woom_general_settings',
		'type'	=> 'sectionend',
		'name'	=> 'end_section',
	),
);
