<?php
if (!defined('ABSPATH')) {
    exit;
}
$features_unlocked_percentage = "20%";

$nq_features = array(
    array('label' => 'WooCommerce WhatsApp Order Notifications ', 'free' => 1, 'pro' => 1),
    array('label' => 'Custom Trigger Buttons	', 'free' => 1, 'pro' => 1),
    array('label' => 'Abandoned Cart Recovery	', 'free' => 1, 'pro' => 1),
    array('label' => 'Two-Way Messaging', 'free' => 0, 'pro' => 1),
    array('label' => 'Receive Customer Messages (Webhook)', 'free' => 0, 'pro' => 1),
    array('label' => 'WooCommerce Bookings Support', 'free' => 0, 'pro' => 1),
    array('label' => 'Quick Reply', 'free' => 0, 'pro' => 1),
    array('label' => 'Mark as Read', 'free' => 0, 'pro' => 1),
    array('label' => 'Block Users', 'free' => 0, 'pro' => 1),
    array('label' => 'Add New Contact from Chat', 'free' => 0, 'pro' => 1),
    array('label' => '24-Hour Messaging Window Timer', 'free' => 0, 'pro' => 1),
    array('label' => 'Quick Notes', 'free' => 0, 'pro' => 1),
    array('label' => 'Message Templates', 'free' => 0, 'pro' => 1),
    array('label' => 'Customer Labels', 'free' => 0, 'pro' => 1),
    array('label' => 'Dedicated Chat Menu', 'free' => 0, 'pro' => 1),
    array('label' => 'WhatsApp Interactive Flows', 'free' => 0, 'pro' => 1),
    array('label' => 'Document Send/Receive', 'free' => 0, 'pro' => 1),
    array('label' => 'WhatsApp Chat Widget (Customizable)', 'free' => 0, 'pro' => 1),
    array('label' => 'Click-to-Chat Link', 'free' => 0, 'pro' => 1),
    array('label' => 'WhatsApp QR Code', 'free' => 0, 'pro' => 1),
    array('label' => 'WhatsApp Chat Icon', 'free' => 0, 'pro' => 1),
    array('label' => 'Shortcode Support', 'free' => 0, 'pro' => 1),
);

$addon_features = array(
    array(
        'title' => 'Teams for Notiqoo',
        'features' => array(
            'Admin & Operator roles',
            'Role-based chat access',
            'Manual & auto chat assignment',
            'Random & first-serve routing',
            'Centralized chat inbox',
            'Performance & response tracking',
            'Web & mobile app access'
        )
    ),
    array(
        'title' => 'Automator for Notiqoo',
        'features' => array(
            'Automated WhatsApp workflows',
            'Order status-based triggers',
            'Custom rules & conditions',
            'Instant & scheduled messages',
            'Dynamic message variables',
            'Multiple workflows support',
            'Priority-based execution'
        )
    )
);
?>
<div class="columns nq-container">
    <div class="content">
        <div class="nq-panel-box">
            <?php
            echo sprintf('<h3>%1$s</h3>', esc_html__('Support & features', 'wc-messaging'));

            $diagnosis_content = '';
            $disabled = (isset($field['disabled']) && $field['disabled'] === true) ? 'readonly' : '';

            if (isset($field['content']) && !empty($field['content'])) {
                if (is_array($field['content'])) {
                    foreach ($field['content'] as $data) {
                        $diagnosis_content .= sprintf('<p>%1$s</p>', $data);
                    }
                } else {
                    $diagnosis_content = sprintf($field['content']);
                }
                $diagnosis_content = sprintf('<div data-html="' . $this->woom_html_to_plaintext($diagnosis_content) . '" class="woom-fullwidth woom_support_diagnostic_info" %2$s>%1$s</div>', $diagnosis_content, $disabled);
            }
            $buttons = '';
            if (isset($field['download_actions']) && !empty($field['download_actions'])) {
                foreach ($field['download_actions'] as $action) {
                    $button_text = (isset($action['button_text']) && !empty($action['button_text'])) ? $action['button_text'] : '';
                    $button_class = (isset($action['button_class']) && !empty($action['button_class'])) ? $action['button_class'] : '';
                    $onclick_func = '';
                    if (isset($action['button_action']) && !empty($action['button_action'])) {
                        switch ($action['button_action']) {
                            case 'download':
                                $onclick_func = 'woom_doc_download(event)';
                                break;
                            case 'copy':
                                $onclick_func = 'woom_doc_copy(event)';
                                break;
                        }
                    }
                    if ($onclick_func !== '') {
                        $buttons .= sprintf('<button class="%2$s" onclick="' . $onclick_func . '">%1$s</button>', $button_text, $button_class, $onclick_func);
                    } else {
                        $buttons .= sprintf('<button class="%2$s">%1$s</button>', $button_text, $button_class);
                    }
                }
                $woom_sidebar = '';
                if (isset($field['sidebar'])) {
                    $woom_sidebar = apply_filters('woom_settings_sidebar', $field['sidebar']);
                }
                $description = '';
                foreach ($field['introduction'] as $descripition) {
                    $description .= sprintf('<p>%1$s</p>', $descripition);
                }
                $diagnosis_content = sprintf(
                    '<div class="woom-support-settings-container"><div class="woom-support-settings">%1$s<div class="woom-info-container">%2$s <p class="woom-buttons-group">%3$s</p></div></div></div>',
                    wp_kses($description, array(
                        'div' => array('class' => array()),
                        'p' => array('class' => array()),
                        'a' => array('class' => array(), 'href' => array(), 'target' => array()),
                    )),
                    wp_kses($diagnosis_content, array(
                        'div' => array('class' => array()),
                        'p' => array('class' => array()),
                        'br' => array(),
                    )),
                    wp_kses(
                        $buttons,
                        array('button' => array(
                            'class' => array(),
                            'onclick' => array(),
                        ))
                    )
                );
            }
            echo wp_kses_post($diagnosis_content);
            ?>
        </div>

        <div class="nq-features-comparison">
            <div class="nq-features-comparison-table">
                <div class="nq-comparison-table-header header-list">
                    <div class="header-item">Feature</div>
                    <div class="header-item">Notiqoo - FREE</div>
                    <div class="header-item">Notiqoo - PRO</div>
                </div>
                <div class="nq-comparison-table-body body-list">
                    <?php
                    foreach ($nq_features as $feature) {
                        printf(
                            '<div class="body-item-row">
                            <div class="body-item">%1$s</div>
                            <div class="body-item">%2$s</div>
                            <div class="body-item">%3$s</div>
                        </div>',
                            esc_html($feature['label']),
                            ($feature['free'] === 1) ? '<i class="dashicons dashicons-yes"></i> YES' : '<i class="dashicons dashicons-no-alt"></i> NO',
                            ($feature['pro'] === 1) ? '<i class="dashicons dashicons-yes"></i> YES' : '<i class="dashicons dashicons-no-alt"></i> NO'
                        );
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar">
        <div class="nq-panel-box featured">
            <h3>Notiqoo Pro</h3>
            <div class="nq-box-tag">PRO</div>
            <p>Unlock all premium automation features and grow and faster with Notiqoo Pro.</p>
            <div class="nq-inline-content">
                <h6 class="nq-pricing">$99.00 <span>/ Year</span></h6>
                <a href="https://notiqoo.com/pricing/?utm_source=support-tab&utm_medium=free-plugin&utm_campaign=sidebar" class="full-width-button nq-btn round" target="_blank">Upgrade to Pro</a>
            </div>
        </div>
        <div class="nq-list-features">
            <div class="nq-panel-box feature">
                <div>
                    <img class="icon" src="<?php echo esc_url(woom_dir_url . 'assets/images/icons/lock.png'); ?>" alt="secure checkout icon">
                    <p>Secure Checkout</p>
                </div>
            </div>
            <div class="nq-panel-box feature">
                <div>
                    <img class="icon" src="<?php echo esc_url(woom_dir_url . 'assets/images/icons/tick.png'); ?>" alt="money back icon">
                    <p>Money-Back guarantee</p>
                </div>

            </div>
            <div class="nq-panel-box feature">
                <div>
                    <img class="icon" src="<?php echo esc_url(woom_dir_url . 'assets/images/icons/badge.png'); ?>" alt="activity security icon">
                    <p>Premium support</p>
                </div>
            </div>
        </div>
        <div class="nq-review-box">
            <span class="dashicons dashicons-format-quote"></span>
            <div class="star-rating">
                <?php
                $rating = 5;
                for ($i = 1; $i <= 5; $i++) {
                    if ($rating >= $i) {
                        echo '<span class="star dashicons dashicons-star-filled"></span>';
                    } elseif ($rating >= $i - 0.5) {
                        echo '<span class="star dashicons dashicons-star-half"></span>';
                    } else {
                        echo '<span class="star dashicons dashicons-star-empty"></span>';
                    }
                }
                ?>
            </div>
            <p class="review-content">"I've been using Notiqoo pro to manage communication with customers via WhatsApp on my woocommerce dashboard. Its been great so far! Just waiting for more features especially the WhatsApp catalog so we can take orders on whatsapp and sync to woocommerce dashboard automatically."</p>
            <div class="nq-review-user-card">
                <div class="img-responsive circle">
                    <img class="icon" src="<?php echo esc_url('https://secure.gravatar.com/avatar/ae66b12095bbd00ef1a9830f7f610dcf6652267c85a458ed632fc76266fdbcd6?s=300&d=retro&r=g'); ?>" alt="reviewed user icon from wordpress profile">

                </div>
                <div class="nq-review-user-info">
                    <h6>Osinkolu</h6>
                    <p>Notiqoo Customer</p>
                </div>
            </div>
        </div>
        <div class="nq-panel-box nq-features-unlocked-preview-box">
            <div class="nq-unlocked-preview-inline">
                <div class="nq-features-unlocked-preview-description">
                    <h4>Features Unlocked</h4>
                    <p>(Upgrade to 100%)</p>
                </div>
                <div class="nq-features-unlocked-preview-percentage">
                    <?php echo esc_html($features_unlocked_percentage); ?>
                </div>

            </div>
            <div style="--nq-unlocked-percentage:<?php echo esc_html($features_unlocked_percentage); ?>;" class="nq-features-unlocked-preview"></div>
        </div>
        <div class="nq-addons-inline">
            <div class="nq-panel-box addon">
                <h3>Teams</h3>
                <div class="nq-box-tag">ADD-ON</div>
                <p>Multi agent chat collaboration with full support.</p>
                <h6 class="nq-pricing">$59.00 <span>/ Year</span></h6>
                <a href="https://notiqoo.com/plugin/teams-notiqoo-pro/?utm_source=support-tab&utm_medium=free-plugin&utm_campaign=sidebar" class="full-width-button nq-btn round" target="_blank">Buy now</a>
            </div>
            <div class="nq-panel-box addon">
                <h3>Automator</h3>
                <div class="nq-box-tag">ADD-ON</div>
                <p>Automated WhatsApp workflows - Followups & triggers.</p>
                <h6 class="nq-pricing">$59.00 <span>/ Year</span></h6>
                <a href="https://notiqoo.com/plugin/automator-notiqoo-pro/?utm_source=support-tab&utm_medium=free-plugin&utm_campaign=sidebar" class="full-width-button nq-btn round" target="_blank">Buy now</a>
            </div>
        </div>
        <div class="nq-addons-bundle">
            <div class="nq-panel-box addon">
                <h3>Bundle pack</h3>
                <div class="nq-box-tag">10% OFF</div>
                <p>Grow foster with Notiqoo - automate order updates, cart recovery.</p>
                <div class="nq-inline-content">
                    <h6 class="nq-pricing">$199.00 <span>/ Year</span></h6>
                    <a href="https://notiqoo.com/bundle-pack/?utm_source=support-tab&utm_medium=free-plugin&utm_campaign=sidebar" class="full-width-button nq-btn round" target="_blank">Buy all</a>
                </div>
            </div>
        </div>
        <div class="nq-panel-box featured demo-explorer">
            <div class="nq-inline-content">
                <div>
                    <img src="<?php echo esc_url(woom_dir_url . 'assets/images/icons/notiqoo.png'); ?>" alt="notiqoo branding logo">
                    <p>See how Notiqoo works in real time.</p>
                </div>
                <a href="https://notiqoo.com/launch-demo/?utm_source=support-tab&utm_medium=free-plugin&utm_campaign=sidebar" class="full-width-button nq-btn round" target="_blank">Explore Demo</a>
            </div>
        </div>
    </div>
</div>