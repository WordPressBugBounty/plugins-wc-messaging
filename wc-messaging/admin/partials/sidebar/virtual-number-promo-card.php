<?php
if (!defined('ABSPATH')) {
    exit;
}
$price = 12.00;
$url = esc_url("https://notiqoo.com/virtual-phone-number-for-whatsapp/?utm_source=tab&utm_medium=free&utm_campaign=premium&utm_id=moremin");
$is_page_support = isset($_GET['section']) && $_GET['section'] === 'support';
if(!$is_page_support) {
    $url = esc_url("https://notiqoo.com/virtual-phone-number-for-whatsapp/?utm_source=sidebar&utm_medium=free&utm_id=moremin");
}
?>

<div class="nq-panel-box card virtual-number-promotional-card">
    <div class="card-section card-content-inline">
        <div class="card-content-left">
            <h3>Get a Virtual Number for WhatsApp</h3>
            <?php if (!$is_page_support) : ?>
                <div class="card-content-center">
                    <img src="<?php echo esc_url(woom_dir_url . 'assets/images/icons/virtual-number-promo.png'); ?>" width="148" alt="Virtual Number for WhatsApp">
                </div>
            <?php endif; ?>
            <h6 class="nq-pricing">$<?php echo number_format($price, 2); ?> <span>/ Year</span></h6>
            <p><?php echo esc_html__('Try Notiqoo without changing your current WhatsApp business number. Get a dedicated virtual number to test Notiqoo safely before connecting your live business number.', 'wc-messaging'); ?></p>
            <a href="<?php echo esc_url($url); ?>" class="nq-btn round" target="_blank">Know More</a>
        </div>
        <?php if ($is_page_support) : ?>
            <div class="card-content-right">
                <img src="<?php echo esc_url(woom_dir_url . 'assets/images/icons/virtual-number-promo.png'); ?>" width="148" alt="Virtual Number for WhatsApp">
            </div>
        <?php endif; ?>
    </div>

</div>