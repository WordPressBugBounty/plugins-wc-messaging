<?php
if (!defined('ABSPATH')) {
    exit;
}
$settings =  array(
    'title' => __('Upgrade', 'wc-messaging'),
    'description' => __('Get a bundle of features with an upgrade to Notiqoo Pro', 'wc-messaging'),
    'features' => array(
        __('Email support is available exclusively for Notiqoo Pro users', 'wc-messaging'),
        __('All features of the free version', 'wc-messaging'),
        __('Enable two-way messaging with customers', 'wc-messaging'),
        __('Compatibility with WooCommerce Booking plugins', 'wc-messaging'),
        __('WhatsApp Interactive Flows', 'wc-messaging'),
        __('Document Send/Receive', 'wc-messaging'),
        __('Quick Reply', 'wc-messaging'),
        __('New Contact Addition from Chat Screen', 'wc-messaging'),
        __('24-Hour Messaging Window Timer', 'wc-messaging'),
        __('Quick Note Feature', 'wc-messaging'),
        __('Message Templates', 'wc-messaging'),
        __('Labels', 'wc-messaging'),
        __('Dedicated Chat Menu', 'wc-messaging'),
        __('Customized Widget', 'wc-messaging'),
        __('Mark as Read', 'wc-messaging')

    ),
    'footer' => array(
        'label' => __('Check demos and premium details', 'wc-messaging'),
        'action' => array(
            'name' => __('Premium Details', 'wc-messaging'),
            'url' => esc_url('https://notiqoo.com/?utm_source=dashboard&utm_medium=support-tab-sidebar&utm_campaign=wc-messaging')
        )
    )
);
$content .= sprintf('<h3 class="">%1$s</h3>', $settings['title']);
$content .= sprintf('<p class="">%1$s</p>', $settings['description']);

$list = '';
foreach ($settings['features'] as $item) {
    $list .= sprintf('<li>%1$s</li>', $item);
}
$list = sprintf('<ul>%1$s</ul>', $list);
$content .= sprintf('<div class="woom-features">%1$s</div>', $list);
$footer = $settings['footer'];
$action_container = sprintf('<div class="woom-action-section"><div class="woom-action-label">%1$s</div><div class="woom-action-button"><a href="%3$s" target="_blank" class="button-primary">%2$s</a></div></div>', $footer['label'], $footer['action']['name'], $footer['action']['url']);
$content = sprintf('<div class="woom-features-container">%1$s</div>%2$s', $content, $action_container);