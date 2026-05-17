<?php
if (!defined('ABSPATH')) {
    exit;
}
$pages = array();
foreach (get_pages() as $page) {
    $pages[$page->post_name] = $page->post_title;
}
$new_settings = array(
    array(
        'id'    => 'woom_widget_tab',
        'type' => 'title',
        // 'name' => sprintf('%1$s & %2$s', __('Support', 'wc-messaging'), __('Features', 'wc-messaging')),
        'desc'    => '',
    ),
    array(
        'id'    => 'woom_support_diagnostic_info',
        'type'  => 'woom_info_downloader',
        'name'  => __('Diagnostic info', 'wc-messaging'),
        'content'  => $this->woom_get_site_diagnostic_info(),
        'disabled' => true,
        'introduction' => array(
            sprintf(
                '%1$s <a href="%3$s" target="_blank">%2$s</a> %4$s',
                __('This is a free plugin. You can request support in the official WordPress plugin directory', 'wc-messaging'),
                __('here', 'wc-messaging'),
                esc_url('https://wordpress.org/support/plugin/wc-messaging/'),
                __("We are always looking for suggestions and feature requests. If you've found a bug, report it immediately so we can fix it as soon as possible.", 'wc-messaging'),
            ),
            sprintf('%1$s, <a href="%3$s" target="_blank">%2$s</a> ', __('For a timely response via email from the developers who work on this plugin', 'wc-messaging'), __('upgrade to premium version', 'wc-messaging'), esc_url('https://notiqoo.com/?utm_source=dashboard&utm_medium=support-tab-description&utm_campaign=Free-plugin'))
        ),
        'download_actions' => array(
            'option-1' => array(
                'button_text' => __('Download', 'wc-messaging'),
                'button_class' => 'woom-button curved',
                'button_action' => 'download'
            ),
            'option-2' => array(
                'button_text' => __('Copy', 'wc-messaging'),
                'button_class' => 'woom-button button-bordered curved',
                'button_action' => 'copy'
            )

        ),
        'sidebar' => 'premium-features'
    ),
    array(
        'id'    => 'woom_widget_config_end',
        'type'    => 'sectionend',
        'name'    => 'end_section',
    ),
);
