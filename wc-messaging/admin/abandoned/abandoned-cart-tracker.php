<?php
if (!defined('ABSPATH')) exit;

class  Notiqoo_Abandoned_Cart_Tracker
{

    /**
     * LOOKUP OPTION
     *
     * Stores relation between:user/session => cart transient id
     *
     * Example:
     * array(
     *   'user_1' => 'nq_ac_17465920451234',
     *   'guest_abcd' => 'nq_ac_17465920457891'
     * )
     */
    private $lookup_option = 'notiqoo_cart_lookup';
    private $stats_option = 'notiqoo_abandoned_stats';
    private $phone_sanitizer;

    public function init()
    {

        $this->phone_sanitizer = new WCWhatsapp();
        add_action('woocommerce_add_to_cart', array($this, 'nq_track_cart'), 12, 0);
        add_action('woocommerce_cart_updated', array($this, 'nq_track_cart'), 10, 0);
        add_action('woocommerce_before_checkout_form', array($this, 'nq_capture_checkout_direct'), 15, 0);
        // add_action('woocommerce_checkout_update_order_review', array($this, 'nq_capture_checkout_live'), 15, 0);
        add_action('woocommerce_order_status_changed', array($this, 'nq_mark_recovered'), 10, 3);
        add_action('wp_loaded', array($this, 'nq_restore_cart'), 10, 0);

        add_action('wp_ajax_nopriv_nq_capture_guest_checkout', array($this, 'nq_capture_guest_checkout_ajax'), 15, 0);
        add_action('wp_ajax_nq_capture_guest_checkout', array($this, 'nq_capture_guest_checkout_ajax'), 15, 0);
    }

    /**
     * GENERATE CART ID
     * Generate unique transient cart id.
     *
     * eg:nq_ac_17465920451234
     */
    private function nq_generate_cart_id()
    {
        return 'nq_ac_' . str_replace('.', '', microtime(true));
    }

    /* 
     * LOOKUP KEY
     * Get lookup key.
     *
     * Logged in:
     *   user_1
     * Guest:
     *   guest_xxxxx
     */
    private function nq_get_lookup_key()
    {

        if (is_user_logged_in()) {
            return 'user_' . get_current_user_id();
        }

        if (WC()->session) {
            return 'guest_' . WC()->session->get_customer_id();
        }

        return null;
    }

    /* 
     * CART EXPIRY
     * Get transient expiry.
     *
     * Admin setting:
     * woom_abandoned_expiry_time
     */
    private function nq_get_cart_expiry()
    {
        $days = (int)get_option('woom_abandoned_expiry_time', 1);
        return $days * DAY_IN_SECONDS;
    }

    /* 
     * GET OR CREATE CART ID
     * Get existing cart id
     * OR create new cart id.
     */
    private function nq_get_or_create_cart_id()
    {
        $lookup_key = $this->nq_get_lookup_key();
        if (!$lookup_key) {
            return null;
        }
        $lookup = get_option($this->lookup_option, array());
        // Existing cart found
        if (!empty($lookup[$lookup_key])) {
            return $lookup[$lookup_key];
        }
        //Create new cart id
        $cart_id = $this->nq_generate_cart_id();
        $lookup[$lookup_key] = $cart_id;
        update_option($this->lookup_option, $lookup);
        return $cart_id;
    }

    /* 
     * Remove lookup relation.
     */
    private function nq_remove_lookup($cart_id)
    {
        $lookup = get_option($this->lookup_option, array());
        foreach ($lookup as $key => $value) {
            if ($value === $cart_id) {
                unset($lookup[$key]);
            }
        }
        update_option($this->lookup_option, $lookup);
    }

    /**
     * Save cart in transient.
     */
    private function nq_save_cart($cart_id, $cart)
    {
        set_transient($cart_id, $cart, $this->nq_get_cart_expiry());
    }
    /**
     * Get single cart.
     */
    public function nq_get_cart($cart_id)
    {
        return get_transient($cart_id);
    }

    /**
     * Delete cart transient.
     */
    public function nq_delete_cart($cart_id)
    {
        delete_transient($cart_id);
        $this->nq_remove_lookup($cart_id);
    }

    /**
     * Get all valid carts from transients.
     *
     * This function:
     * 1. Reads all user/session => cart mappings
     * 2. Loads each cart transient
     * 3. Removes expired/invalid mappings automatically
     * 4. Returns only active valid carts
     *
     * Example lookup mapping:
     * user_1     => nq_ac_123
     * guest_abcd => nq_ac_456
     *
     * Unmapping happens here:
     * If a transient/cart no longer exists,
     * its lookup relation is removed automatically.
     */
    public function nq_get_all_carts()
    {
        $lookup = get_option($this->lookup_option, array());
        $carts = array();
        foreach ($lookup as $lookup_key => $cart_id) {
            $cart = get_transient($cart_id);
            //Transient expired automatically
            if (!$cart) {
                unset($lookup[$lookup_key]);
                continue;
            }
            $carts[$cart_id] = $cart;
        }
        //Cleanup invalid lookups
        update_option($this->lookup_option, $lookup);
        return $carts;
    }

    /**
     * GENERATE RECOVERY KEY
     * Generate short unique recovery key for cart restore URL.
     *
     * 8-character random string (base64, url-safe chars only)
     * instead of full UUID4 — keeps checkout/cart URLs short
     * for WhatsApp messages.
     *
     * Collision-checked against existing carts before returning.
     */
    private function nq_generate_recovery_key()
    {
        do {
            $key = substr(str_replace(array('+', '/', '='), '', base64_encode(random_bytes(8))), 0, 8);
        } while ($this->nq_recovery_key_exists($key));
        return $key;
    }

    /**
     * RECOVERY KEY EXISTS
     * Check if a recovery key is already in use
     * by any active cart, to avoid collisions.
     *
     * NOTE: Uses raw lookup option (not nq_get_all_carts) to avoid
     * side-effect of pruning the lookup entry currently being created.
     */
    private function nq_recovery_key_exists($key)
    {
        $lookup = get_option($this->lookup_option, array());
        foreach ($lookup as $lookup_key => $cart_id) {
            $cart = get_transient($cart_id);
            if ($cart && !empty($cart['recovery_key']) && $cart['recovery_key'] === $key) {
                return true;
            }
        }
        return false;
    }
    /**
     * Default cart structure.
     */
    private function nq_empty_cart($cart_id)
    {
        $recovery_key = $this->nq_generate_recovery_key();
        return array(
            'cart_id' => $cart_id,
            'user_id' => get_current_user_id(),
            'session_id' => WC()->session
                ? WC()->session->get_customer_id()
                : '',

            'recovery_key' => $recovery_key,

            'cart_url' => add_query_arg(
                'nqrc',
                $recovery_key,
                wc_get_cart_url()
            ),

            'checkout_url' => add_query_arg(
                'nqrc',
                $recovery_key,
                wc_get_checkout_url()
            ),
            'customer_phone' => '',
            'customer_email' => '',
            'customer_name' => '',
            'cart_items' => array(),
            'cart_total' => 0,
            'status' => 'active',
            'checkout_started' => false,
            'order_id' => 0,
            'created_at' => time(),
            'last_activity' => time(),
            'abandoned_at' => 0,
        );
    }

    /**
     * Get WooCommerce cart items.
     */
    private function nq_get_cart_items()
    {
        $items = array();
        if (!WC()->cart) {
            return $items;
        }
        foreach (WC()->cart->get_cart() as $item) {
            if (empty($item['data'])) {
                continue;
            }
            $price = $item['data']->get_price();
            $qty = isset($item['quantity']) ? $item['quantity'] : 1;
            $items[] = array(
                'product_id' => $item['product_id'],
                'variation_id' => isset($item['variation_id'])
                    ? $item['variation_id']
                    : 0,

                'variation' => isset($item['variation'])
                    ? $item['variation']
                    : array(),

                'name' => $item['data']->get_name(),
                'qty' => $qty,
                'price' => $price,
                'subtotal' => $price * $qty,
            );
        }
        return $items;
    }

    /**
     * Track and store current WooCommerce cart.
     *
     * Creates a new cart if not exists,
     * otherwise updates existing cart data.
     *
     * Updates:
     * - cart items
     * - totals
     * - activity timestamp
     */
    public function nq_track_cart()
    {
        //Get/create cart id
        $cart_id = $this->nq_get_or_create_cart_id();
        if (!$cart_id) {
            return;
        }
        //Existing cart
        $cart = $this->nq_get_cart($cart_id);
        //Create fresh cart
        if (!$cart) {
            $cart = $this->nq_empty_cart($cart_id);
        }
        //Update cart
        $cart['cart_items'] = $this->nq_get_cart_items();
        $cart['cart_total'] = WC()->cart
            ? WC()->cart->get_cart_contents_total()
            : 0;

        // $cart['status'] = 'active';
        $cart['last_activity'] = time();
        // Don't reset abandoned/recovered/expired carts
        if (!in_array($cart['status'], array('abandoned', 'recovered', 'expired'), true)) {
            $cart['status'] = 'active';
        }
        $this->nq_save_cart($cart_id, $cart);
    }

    /**
     * Capture checkout data.
     */
    public function nq_capture_checkout_direct()
    {
        $this->nq_save_checkout_data($_POST);
    }

    /**
     * Saving checkout data
     */
    private function nq_save_checkout_data($data)
    {

        $cart_id = $this->nq_get_or_create_cart_id();

        if (! $cart_id) {
            return;
        }

        $cart = $this->nq_get_cart($cart_id);

        if (! $cart) {
            return;
        }

        if (! empty($data['billing_email'])) {

            $cart['customer_email'] = sanitize_email(
                wp_unslash($data['billing_email'])
            );
        } elseif (is_user_logged_in()) {

            $email = WC()->customer->get_billing_email();

            if (! empty($email)) {
                $cart['customer_email'] = sanitize_email($email);
            }
        }

        if (! empty($data['billing_phone'])) {

            $cart['customer_phone'] = $this->phone_sanitizer->nq_sanitise_phone_number(
                wp_unslash($data['billing_phone'])
            );
        } elseif (is_user_logged_in()) {

            $phone = WC()->customer->get_billing_phone();

            if (! empty($phone)) {
                $cart['customer_phone'] = $this->phone_sanitizer->nq_sanitise_phone_number(
                    $phone
                );
            }
        }

        $first = ! empty($data['billing_first_name'])
            ? sanitize_text_field(wp_unslash($data['billing_first_name']))
            : '';

        $last = ! empty($data['billing_last_name'])
            ? sanitize_text_field(wp_unslash($data['billing_last_name']))
            : '';

        if (empty($first) && is_user_logged_in()) {
            $first = WC()->customer->get_billing_first_name();
        }

        if (empty($last) && is_user_logged_in()) {
            $last = WC()->customer->get_billing_last_name();
        }

        $name = trim($first . ' ' . $last);

        if (! empty($name)) {
            $cart['customer_name'] = $name;
        }

        $cart['checkout_started'] = true;
        $cart['last_activity']    = time();

        $this->nq_save_cart($cart_id, $cart);
    }
    public function nq_capture_guest_checkout_ajax()
    {
        check_ajax_referer('nq_guest_checkout', 'nonce');
        $this->nq_save_checkout_data($_POST);
        wp_send_json_success();
    }
    /**
     * Process abandonment.
     */
    public function nq_process_cart_abandonment()
    {
        $carts = $this->nq_get_all_carts();
        foreach ($carts as $cart_id => $cart) {
            //Ignore empty carts
            if (empty($cart['cart_items'])) {
                $this->nq_delete_cart($cart_id);
                continue;
            }
            //Ignore recovered/expired
            if (
                in_array(
                    $cart['status'],
                    array('recovered', 'expired')
                )
            ) {
                continue;
            }
            $cutoff = get_option('woom_abandoned_cutoff_time', 30) * 60;

            if (
                (time() - $cart['last_activity'])
                < $cutoff
            ) {
                continue;
            }

            if ($cart['status'] !== 'abandoned') {
                $cart['status'] = 'abandoned';
                $cart['abandoned_at'] = time();
                $this->nq_update_stats('abandoned');
                do_action('notiqoo_abandoned_cart_created', $cart);
                $this->nq_save_cart($cart_id, $cart);
            }
        }
    }

    /**
     * Mark abandoned cart as recovered.
     *
     * Runs when WooCommerce order becomes
     * processing/completed.
     *
     * Matches carts using:
     * - phone number
     * - user id
     */
    public function nq_mark_recovered($order_id, $old_status, $new_status)
    {
        if (!in_array($new_status, array('processing', 'completed'))) {
            return;
        }
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }
        $phone = $this->phone_sanitizer->nq_sanitise_phone_number($order->get_billing_phone());
        $user_id = $order->get_user_id();
        $carts = $this->nq_get_all_carts();
        foreach ($carts as $cart_id => $cart) {
            if ((!empty($phone) && $cart['customer_phone'] === $phone) || (!empty($cart['user_id']) && $cart['user_id'] == $user_id)) {
                $cart['status'] = 'recovered';
                $cart['order_id'] = $order_id;
                $cart['closed_at'] = time();
                $this->nq_update_stats('recovered');
                do_action('notiqoo_abandoned_cart_recovered', $cart, $order_id);
                set_transient('nq_recovered_' . $cart_id, true, DAY_IN_SECONDS);
                $this->nq_delete_cart($cart_id);
            }
        }
    }
    /**
     * Cleanup expired abandoned carts.
     */
    public function nq_cleanup_expired_carts()
    {
        $carts = $this->nq_get_all_carts();
        $expiry = $this->nq_get_cart_expiry();
        foreach ($carts as $cart_id => $cart) {
            if ($cart['status'] !== 'abandoned') {
                continue;
            }
            if ((time() - $cart['abandoned_at']) > $expiry) {
                $cart['status'] = 'expired';
                $cart['closed_at'] = time();
                $this->nq_update_stats('lost');
                do_action('notiqoo_abandoned_cart_expired', $cart);
                $this->nq_delete_cart($cart_id);
            }
        }
    }

    /**
     * Update abandoned cart analytics counters (lost/recovered/abandoned)
     *
     * Stores:
     * - daily stats
     * - monthly stats
     * - yearly stats
     */
    public function nq_update_stats($type)
    {
        $stats = get_option($this->stats_option, array());
        $day = current_time('Y-m-d');
        $month = current_time('Y-m');
        $year = current_time('Y');
        $groups = array(
            'daily' => $day,
            'monthly' => $month,
            'yearly' => $year,
        );
        foreach ($groups as $group => $date) {
            if (!isset($stats[$group][$date])) {
                $stats[$group][$date] = array(
                    'abandoned' => 0,
                    'recovered' => 0,
                    'lost' => 0,
                );
            }
            $stats[$group][$date][$type]++;
        }
        update_option($this->stats_option, $stats);
    }

    /**
     * Get analytics stats.
     */
    public function nq_get_stats()
    {
        return get_option($this->stats_option, array());
    }

    /**
     * Restore abandoned WooCommerce cart using unique recovery key from URL.
     *
     * Works for:
     * - guest users
     * - logged in users
     * - mobile/laptop switching
     * - expired WooCommerce sessions
     *
     * Example:
     * site.com/cart/?nqrc=xxxx
     *
     * Process:
     * 1. Read recovery key from URL
     * 2. Find matching saved abandoned cart
     * 3. Empty current WooCommerce cart
     * 4. Re-add saved cart items
     * 5. Restore customer cart session
     */
    public function nq_restore_cart()
    {
        if (empty($_GET['nqrc'])) {
            return;
        }

        $recovery_key = sanitize_text_field(
            $_GET['nqrc']
        );

        $carts = $this->nq_get_all_carts();

        foreach ($carts as $cart_id => $cart) {

            if (
                empty($cart['recovery_key']) ||
                $cart['recovery_key'] !== $recovery_key
            ) {
                continue;
            }

            if (!WC()->cart) {
                return;
            }

            WC()->cart->empty_cart();

            foreach ($cart['cart_items'] as $item) {

                WC()->cart->add_to_cart(
                    $item['product_id'],
                    $item['qty'],
                    $item['variation_id'],
                    $item['variation']
                );
            }

            $cart['last_activity'] = time();

            $this->nq_save_cart($cart_id, $cart);

            break;
        }
    }
}
