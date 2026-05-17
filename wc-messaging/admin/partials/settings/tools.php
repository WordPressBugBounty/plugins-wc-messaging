<?php
if (!defined('ABSPATH')) {
    exit;
}
$widget_link_val = get_option('woom_widget_link', sprintf('https://wa.me/%s?text=hai', floatval(get_option('nq_business_whatsapp_number', '+910000000000'))));
$widget_shortcode_val = get_option('woom_widget_shortcode', '[woom-chat-widget]');
$widget_link_val_arr = explode('/', $widget_link_val);
foreach ($widget_link_val_arr as $widget_link) {
    if (floatval($widget_link) > 0) {
        if (floatval($widget_link) === floatVal('+910000000000')) {
            $widget_link_val = sprintf('https://wa.me/%s?text=hai', floatval(get_option('nq_business_whatsapp_number', '+910000000000')));
            update_option('woom_widget_link', $widget_link_val);
        }
    }
}
$pages = array();
foreach (get_pages() as $page) {
    $pages[$page->post_name] = $page->post_title;
}
$new_settings = array(
    array(
        'id'    => 'woom_widget_tab',
        'type' => 'title',
        'name' => __('Notiqoo', 'wc-messaging'),
        'desc'    => __('Notiqoo configuration', 'wc-messaging'). sprintf('<a href="%s" target="_blank">%s</a>', esc_url('https://notiqoo.com/docs/notiqoo-pro/setting-page/template-settings/?utm_source=plugin&utm_medium=free-settings&utm_campaign=free-settings'), __("\t\tRead documentation.", 'wc-messaging')),
    ),
    array(
        'id'    => 'woom_tools_update_wa_templates',
        'type'  => 'woom_trigger_button',
        'name'  => __('Synchronize whatsapp message templates from Facebook', 'wc-messaging'),
        'desc'  => __('When you add new message templates in the Facebook WhatsApp message template section, they will not appear in the select box until you synchronize using this button.', 'wc-messaging'),
        'option' => array(
            'type' => "button",
            'name' => __('Sync now', 'wc-messaging'),
            'classname' => 'woom-button-flex',
            'custom_attributes' => array(
                'onclick' => 'woom_regenerate_templates(event,this)',
                'data-access-token' => get_option('woom_whatsapp_api', ''),
            )
        ),
        'disabled' => true
    ),
    array(
        'id'    => 'woom_widget_config_end',
        'type'    => 'sectionend',
        'name'    => 'end_section',
    ),
);
