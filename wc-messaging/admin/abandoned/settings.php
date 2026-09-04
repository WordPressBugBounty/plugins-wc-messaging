<?php

if (!defined('ABSPATH')) {
    exit;
}
$abandoned_checker = new Wcm_Abandoned_Checker();

$new_settings = array(
    array(
        'id'    => 'woom_abandoned_cart_tab',
        'type' => 'title',
        'name' => __('Abandoned Cart', 'wc-messaging'),
        'desc'    => __('Notiqoo Abandoned Cart Settings', 'wc-messaging') . sprintf('<a href="%s" target="_blank">%s</a>', esc_url('https://notiqoo.com/docs/notiqoo-pro/setting-page/abandoned-cart-settings/?utm_source=plugin&utm_medium=free-settings&utm_campaign=free-settings'), __("\t\tRead documentation.", 'wc-messaging')),
    ),
    array(
        'id'    => 'woom_abandoned_enable',
        'type' => "woom_with_note",
        'field_type' => "checkbox",
        'name' => __('Enable tracking', 'wc-messaging'),
        'desc'    => __('Start capturing abandoned carts.', 'wc-messaging'),
        'note'    => __('Cart will be considered abandoned if order is not completed in cart abandoned cut-off time.', 'wc-messaging')
    ),
    array(
        'id'    => 'woom_abandoned_order_enable',
        'type' => "woom_with_note",
        'field_type' => "checkbox",
        'name' => __('Enable pending order tracking', 'wc-messaging'),
        'desc'    => __('Start capturing pending order as abandoned carts.', 'wc-messaging'),
        'note'    => __('Tracks WooCommerce orders remaining in pending payment status.', 'wc-messaging')
    ),
    array(
        'id'    => 'woom_abandoned_method',
        'name'  => __('Abandonment tracking method', 'wc-messaging'),
        'type'  => 'select',
        'default'  => 'builtin',
        'options' => array(
            'builtin' => __('Default (Built-in)', 'wc-messaging'),
        ),
    ),
    array(
        'id' => 'woom_abandoned_cutoff_time',
        'type' => "woom_with_note",
        'field_type' => "number",
        'name' => __('Cart abandoned cut-off time', 'wc-messaging'),
        'desc'    => __('Minutes.', 'wc-messaging'),
        'note' => __('Treat the cart as abandoned if the order remains in a pending status and is not completed within the specified number of minutes', 'wc-messaging')
    ),
    array(
        'id' => 'woom_abandoned_expiry_time',
        'type' => "woom_with_note",
        'field_type' => "number",
        'name' => __('Recovery window period', 'wc-messaging'),
        'desc'    => __('Days.', 'wc-messaging'),
        'note' => __('Treat the cart as expired if the order remains in a pending checkout and is not completed within the specified number of days', 'wc-messaging')
    ),
    array(
        'id'    => 'woom_abandoned_cart_tab_general_settings_end',
        'type'    => 'sectionend',
        'name'    => 'end_section',
    ),
    array(
        'id'    => 'woom_abandoned_cart_tab_coupon_settings',
        'type' => 'title',
        'name' => __('Coupon Settings', 'wc-messaging'),
    ),
    array(
        'id' => 'woom_abandoned_auto_delete_coupon',
        'type' => "woom_with_note",
        'field_type' => "checkbox",
        'name' => __('Delete Coupons Automatically', 'wc-messaging'),
        'desc' => __('Delete coupons automatically on weekly basis.', 'wc-messaging'),
        'note' => __('This option will set a weekly cron to delete all expired and used coupons automatically in the background.', 'wc-messaging')
    ),
    array(
        'id' => 'woom_abandoned_manual_delete_coupon',
        'type' => "woom_with_note",
        'field_type' => "button",
        'name' => __('Delete Coupons Manually', 'wc-messaging'),
        'button_text' => __('Delete', 'wc-messaging'),
        'note' => __('This will delete all expired and used coupons that were created by Woo Cart Abandoned Recovery.', 'wc-messaging'),
        'custom_attributes' => array(
            'onclick' => 'woom_delete_abandoned_coupons(event)',
        )
    ),
    array(
        'id'    => 'woom_abandoned_cart_tab_coupon_settings_end',
        'type'    => 'sectionend',
        'name'    => 'end_section',
    ),
    /**array(
        'id'    => 'woom_abandoned_cart_webhook_settings',
        'type' => 'title',
        'name' => __('Abandoned Webhook Settings', 'wc-messaging'),
    ),

    array(
        'id' => 'woom_abandoned_cart_enable_webhook',
        'type' => 'checkbox',
        'name' => __('Enable webhook', 'wc-messaging'),
        'desc' => __('Allows you to trigger webhook automatically upon cart abandoned and recovery.', 'wc-messaging')
    ),
    array(
        'id' => 'woom_abandoned_cart_webhook',
        'type' => 'woom_inline_fields',
        'name' => __('Webhook URL', 'wc-messaging'),
        'fields' => array(
            array(
                'id' => 'woom_abandoned_cart_webhook',
                'type' => "text"
            ),
            array(
                'id' => 'woom_abandoned_cart_webhook_action',
                'name' => __('Trigger Sample', 'wc-messaging'),
                'type' => "button",
                'custom_attributes' => array(
                    'onclick' => 'woom_sample_abandoned_webhook_action(event)',
                )
            )
        ),
        'note' => __('Save the webhook URL, then click "trigger sample".', 'wc-messaging')
    ),

    array(
        'id'    => 'woom_abandoned_cart_webhook_settings_end',
        'type'    => 'sectionend',
        'name'    => 'end_section',
    ),*/
    array(
        'id'    => 'woom_abandoned_cart_templates',
        'type' => 'title',
        'name' => __('Abandoned messages', 'wc-messaging'),
        'desc' => sprintf('<a href="https://business.facebook.com/latest/whatsapp_manager/" target="_blank">Go to Facebook Whatsapp Manager</a>')
    ),
    array(
        'id' => 'woom_abandoned_cart_triggers',
        'type' => 'woom_config_template_settings',
        'name' => __('Abandoned cart templates', 'wc-messaging'),
        'template_titles' => array(
            'title' => __('Trigger name', 'wc-messaging'),
            'enable_switch' => __('Enable / disable', 'wc-messaging'),
            'template' => __('Template name', 'wc-messaging'),
            'trigger' => __('Trigger within', 'wc-messaging'),
            'header_params' => __('Header parameters', 'wc-messaging'),
            'body_params' => __('Body parameters', 'wc-messaging'),
            'actions' => __('Actions', 'wc-messaging')
        ),
        'custom_templates_only' => true,
        'fields' => $this->get_settings_statuses(
            'woom_abandoned_cart_trigger',
            $this->woom_custom_options('abandoned_cart_triggers'),
            $is_editable = true,
            $actions = array('preview', 'remove', 'coupon'),
            $has_admin_row = false
        ),
        'toggle_prefixes' => $this->woom_get_trigger_actions('woom_abandoned_cart_triggers', $has_admin_row = false),
        'toggle_settings' => array(
            array(
                'id' => 'coupon_enabled',
                'type' => 'checkbox',
                'name' => __('Fixed coupon code', 'wc-messaging'),
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
        'label_editable' => true,
        'add_new_row' => true,
        'desc_tip'    => true
    ),

    array(
        'id'    => 'woom_abandoned_cart_tab_settings_end',
        'type'    => 'sectionend',
        'name'    => 'end_section',
    ),
);
