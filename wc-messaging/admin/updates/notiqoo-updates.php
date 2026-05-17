<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="column nq-page-releases-container">
    <h2>Recent Releases</h2>
    <?php
    include(plugin_dir_path(__FILE__) . 'release-updates.php');
    if (!empty($release_notes)) {
        foreach ($release_notes as $release) {
            echo '<p><strong>Version ' . esc_html($release['version']) . '</strong> ' . wp_kses_post($release['description']) . ' For more information, see <a href="' .  esc_url($release['source']) . '" target="_blank">the release notes</a></p>';
        }
    }
    ?>
</div>

<div class="about__section">
    <div class="column">
        <h2>Welcome to Notiqoo <?php echo esc_html(woom_version); ?></h2>
        <p class="is-subheading">
            Notiqoo is built to transform how your WooCommerce store communicates with customers.
            Automate order notifications, keep customers informed in real time, and enable smooth, two-way messaging — all from one intuitive dashboard.
            With Notiqoo, you reduce manual effort, improve customer trust, and deliver a faster, more engaging shopping experience at every stage of the order journey.
        </p>
    </div>
</div>
<div class="about__section has-2-columns">
    <div class="column is-vertically-aligned-center">
        <div class="nq-product-title">
            <h3>Notiqoo PRO</h3>
        </div>
        <p>
            Notiqoo is a WhatsApp Cloud API-powered WooCommerce plugin that helps WooCommerce stores send automated WhatsApp order alerts, enable two-way messaging, recover carts, and provide support — all via the official WhatsApp Cloud API.
        </p>
    </div>
    <div class="column is-vertically-aligned-center">
        <div class="about__image has-tag">
            <span class="product-tag">PRO</span>
            <img
                src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'img/notiqoo-pro.png'); ?>"
                alt="" style="--border-radius: 18px" height="436" width="436" />
        </div>
    </div>
</div>


<div class="about__section has-2-columns">
    <div class="column is-vertically-aligned-center">
        <div class="about__image has-tag addon">
            <img
                src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'img/teams-for-notiqoo.png'); ?>"
                alt=""
                style="--border-radius: 18px"
                height="436"
                width="436" />
            <span class="product-tag">ADDON</span>
        </div>
    </div>
    <div class="column is-vertically-aligned-center">
        <div class="nq-product-title">
            <h3>Teams for Notiqoo</h3>
        </div>
        <p>Teams for Notiqoo is a collaboration add-on built exclusively for Notiqoo Pro that helps businesses manage WhatsApp conversations with multiple team members. It offers a shared inbox, chat assignment, and role-based access, enabling admins to organize chats and monitor performance while operators handle customer conversations efficiently. With a responsive web app for desktop and mobile, Teams for Notiqoo delivers faster, well-organized WhatsApp customer support from a single, centralized inbox.</p>
    </div>
</div>


<div class="about__section has-2-columns">
    <div class="column is-vertically-aligned-center">
        <div class="nq-product-title">
            <h3>Automator for Notiqoo</h3>
        </div>
        <p>Automator for Notiqoo is a powerful add-on for Notiqoo Pro that helps you automate WooCommerce WhatsApp notifications with ease. It allows you to create custom workflows to send instant or scheduled WhatsApp messages based on order status changes, ensuring timely and personalized customer communication. By automating order confirmations, shipping updates, and reminders, Automator for Notiqoo saves time while delivering a smooth, engaging customer experience.
        </p>
    </div>
    <div class="column is-vertically-aligned-center">
        <div class="about__image has-tag addon">
            <img
                src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'img/automator-for-notiqoo.png'); ?>"
                alt="" style="--border-radius: 18px" height="436" width="436" />
            <span class="product-tag">ADDON</span>
        </div>
    </div>
</div>

<hr class="is-invisible is-large" style="margin-bottom: calc(2 * var(--gap))" />

<div
    class="about__section has-2-columns is-wider-left is-feature"
    style="background-color: var(--background);border-radius: var(--border-radius);">
    <h3 class="is-section-header">Discover what Notiqoo can do for your store</h3>
    <div class="column">
        <p>Explore powerful features and best practices to improve your WooCommerce customer communication.
            Visit the Notiqoo website to get the most out of Notiqoo</p>
    </div>
    <div class="column aligncenter">
        <div class="about__image">
            <a target="_blank" href="https://notiqoo.com/?utm_source=wp&utm_medium=free-plugin&utm_campaign=update-screen" class="button button-primary button-hero">Visit Notiqoo Pro</a>
        </div>
    </div>
</div>


<hr class="is-large" />

<div class="return-to-dashboard">
    <a href="<?php echo esc_url(admin_url()); ?>">Go to Dashboard → Home</a>
</div>
</div>

<style>
    .nq-page-releases-container {
        background-color: #eef0fd;
        border-radius: 12px
    }

    .nq-page-releases-container p {
        margin: auto;
        line-height: 32px;
        font-size: 16px;
    }

    .has-tag {
        position: relative;
    }

    .has-tag .product-tag {
        position: absolute;
        top: 12px;
        right: 12px;
        font-weight: 500;
        background: #1E1740;
        color: #ffffff;
        font-size: 12px;
        line-height: 0px;
        border-radius: 100%;
        width: 24px;
        height: 24px;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .has-tag.addon .product-tag {
        background: #8D79B7;

    }
</style>