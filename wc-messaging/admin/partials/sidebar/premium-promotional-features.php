<?php
if (!defined('ABSPATH')) {
    exit;
}

?>
<div class="woom-settings-sidebar">

    <div class="woom-promo-features-container">
        <h3 class=""><?php echo esc_html('Explore more from Notiqoo', 'wc-messaging'); ?></h3>
        <p class=""></p>
        <div class="woom-promo-features">
            <ul>
                <li><a href="https://notiqoo.com/docs/notiqoo/?utm_source=dashboard&utm_medium=settings-sidebar&utm_campaign=wc-messaging" target="_blank"><img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'img/docs.svg'); ?>" alt="View plugin docs">
                        <h4><?php echo esc_html('View documentation', 'wc-messaging'); ?></h4>
                    </a></li>
                <li><a href="https://notiqoo.com/contact/?utm_source=dashboard&utm_medium=settings-sidebar&utm_campaign=wc-messaging" target="_blank"><img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'img/request-feature.svg'); ?>" alt="Suggest feature for our future development">
                        <h4><?php echo esc_html('Suggest a feature', 'wc-messaging'); ?></h4>
                    </a></li>
                <li>
                    <a href="https://wordpress.org/support/plugin/wc-messaging/reviews/" target="_blank" title="<?php esc_html_e('Like this plugin? please leave a review and support us!', 'wc-messaging'); ?>">
                        <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'img/review.svg'); ?>" alt="notiqoo-rating">
                        <h4><?php echo esc_html('Love this plugin? Rate us', 'wc-messaging'); ?></h4>
                    </a></li>
            </ul>
        </div>
        <div class="cards-section">
            <div class="card" style="background-image: url(<?php echo esc_url(plugin_dir_url(__FILE__) . 'img/offer-promotion.svg'); ?>);">
                <div class="card-content">
                    <h3 class="title"><?php esc_html_e('Go Pro & Save Big', 'wc-messaging'); ?></h3>
                    <div class="action"><a href="https://notiqoo.com/pricing/?utm_source=dashboard&utm_medium=settings-sidebar&utm_campaign=wc-messaging" class="button"><img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'img/ticket-sale.svg'); ?>" alt=""><?php esc_html_e('Buy Notiqoo Pro', 'wc-messaging'); ?></a></div>
                </div>
            </div>
            <div class="card is-dismissable" style="background-image: url(<?php echo esc_url(plugin_dir_url(__FILE__) . 'img/qr.jpg'); ?>);">
                <div class="card-content">
                    <?php
                    printf('<h3 class="title">%1$s</h3>', esc_html__('Launch Premium Version Demo', 'wc-messaging'));
                    printf(
                        '<p>%1$s <a href="%2$s" target="_blank">%3$s</a></p>',
                        esc_html__('Scan this QR code and start whatsapp chat to get instance demo', 'wc-messaging'),
                        esc_url('https://notiqoo.com/launch-demo/?utm_source=dashboard&utm_medium=settings-sidebar&utm_campaign=wc-messaging'),
                        esc_html__('Launch a Demo', 'wc-messaging')
                    );
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>