<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Wcm_Abandoned_Checker')) {
    class Wcm_Abandoned_Checker
    {

        private $woom_whatsapp_class;
        private $woom_abandoned_coupon_prefix;


        public function __construct()
        {
            $this->woom_whatsapp_class = new WCWhatsapp();
            $this->woom_abandoned_coupon_prefix = 'SG';
        }

        /**
         * Retrieves the prefix used for abandoned cart coupons.
         * This function returns the value of the woom_abandoned_coupon_prefix property,
         * which is used as a prefix for generating unique coupon codes for abandoned carts.
         * @return string The prefix used for abandoned cart coupons./
         **/
        public function woom_generate_coupon_prefix()
        {
            return $this->woom_abandoned_coupon_prefix;
        }

        /**
         * Triggers the abandoned messages for eligible orders.
         * This function retrieves pending orders that have been abandoned for a specified time,
         * processes them, and sends abandoned cart messages to the associated phone numbers.
         * It also updates order metadata and triggers for related actions.
         * @return mixed
         **/
        public function trigger_abandoned_offer()
        {
            // cutoff time from settings
            $time_in_minutes = get_option('woom_abandoned_cutoff_time', 20);

            // order status for get specific orders to trigger abandoned 
            $statuses = apply_filters('woom_abadonment_status', ['pending']);

            // Arguments for get specific orders to make abandoned
            $order_args = [
                'status' => $statuses,
                'date_created' =>  '<' . (time() - MINUTE_IN_SECONDS * $time_in_minutes),
                'limit' => -1,
                'return' => 'ids'
            ];
            $orders = wc_get_orders($order_args);

            foreach ($orders as $order_id) {
                $order = wc_get_order($order_id);
                if (!$order->get_meta('woom_abandoned_order_created', true)) {
                    // creating and save a meta data for initialise an order as abandoned 
                    $order->update_meta_data('woom_abandoned_order_created', current_time('timestamp'));
                    $order->update_meta_data('notiq_abandoned_status', 'true');
                    $order->save();
                    // Do something after initialise an order as abandoned 
                    do_action('woom_abandoned_order_created', $order_id);
                }
                if ($order->get_meta('woom_abandoned_order_created', true)) {
                    // Trigger abandoned message as per the rule of settings
                    $this->send_abandoned_message($order_id);
                    // Do something after trigger message for abandoned order
                } else {
                    // error logging if failed to initialise or get the order meta of woom_abandoned_order_created
                    // error_log(print_r(__("Failed to create meta data of woom_abandoned_order_created", 'wc-messaging'), true));
                }
            }
            return;
        }
        /**
         * get whatsapp number from order / user data
         *
         * @param [type] $order_id
         * @return mixed
         */
        public function get_user_whatsapp_number($order_id)
        {
            if (empty($order_id)) {
                return;
            }
            $user_data = get_userdata($order_id);
            if ($user_data) {
                $options = array('woom_whatsapp_number', 'billing_phone', 'shipping_phone');
                foreach ($options as $option) {
                    $phone_number = get_user_meta($user_data->ID, $option, true);
                    if ($phone_number) {
                        return $phone_number;
                    }
                }
            } else {
                $order = wc_get_order($order_id);
                $phone_number = null;

                if ($order) {
                    $billing_phone = $order->get_billing_phone();
                    $shipping_phone = $order->get_shipping_phone();

                    if (!empty($billing_phone)) {
                        $phone_number = $billing_phone;
                    } elseif (!empty($shipping_phone)) {
                        $phone_number = $shipping_phone;
                    }
                }
            }
            if ($phone_number !== null) {
                $phone_number = floatval(str_replace(" ", "", $phone_number));
            }
            return $phone_number;
        }
        /**
         * trigger abandoned whatsapp message by follow rules in settings function
         *
         * @param integer $order_id
         * @return mixed
         */
        public function send_abandoned_message($order_id = 0)
        {
            if (empty($order_id)) {
                return $result = array('success' => false, 'message' => __('Order ID is missing', 'wc-messaging'));
            }

            if (!class_exists('WCWhatsapp')) {
                return $result = array('success' => false, 'message' => __('class WCWhatsapp is not exists', 'wc-messaging'));
            }
            // get rows of abandoned settings table data of enabled list
            $abandoned_triggers = $this->get_abandoned_triggers_data();
            if (empty($abandoned_triggers)) {
                return $result = array('success' => false, 'message' => __('Abandoned trigger configuration pending', 'wc-messaging'));
            }

            $order = wc_get_order($order_id);
            foreach ($abandoned_triggers as $option_prefix => $option) :
                if (!empty($option['template'])) :
                    // time in seconds of initialized as abandoned order
                    $order_abandoned_created_time = floatval($order->get_meta('woom_abandoned_order_created', true));
                    // time in seconds of duration between current time from initialized time
                    $timestamp_diff = current_time('timestamp') - $order_abandoned_created_time;
                    // time to trigger offer by the settings table
                    $option_duration_in_seconds = (isset($option['duration'])) ? $option['duration'] : 0;
                    // converting time with time unit from settings table time to seconds 
                    if (!empty($option_duration_in_seconds) && $option_duration_in_seconds > 0) {
                        switch ($option['duration_unit']) {
                            case 'day':
                                $option_duration_in_seconds = $option_duration_in_seconds * DAY_IN_SECONDS;
                                break;

                            case 'hour':
                                $option_duration_in_seconds = $option_duration_in_seconds * HOUR_IN_SECONDS;
                                break;

                            case 'minute':
                                $option_duration_in_seconds = $option_duration_in_seconds * MINUTE_IN_SECONDS;
                                break;
                        }
                    }
                    /**
                     * Conditions to trigger message as per settings table and duration between abandoned create time and current time
                     * 
                     * condition 1
                     *  if not created a meta that, cretes when trigger message
                     * 
                     * condition 2
                     * if duration between abandoned create time and current time in seconds > settings table time in seconds
                     */
                    if (empty($order->get_meta($option_prefix . '_sent', true)) && $timestamp_diff > $option_duration_in_seconds) {
                        // getting whatsapp number to trigger message
                        $phone = $this->get_user_whatsapp_number($order_id);
                        if (empty($phone)) {
                            return array('success' => false, 'message' => __('Phone number is missing', 'wc-messaging'));
                        }
                        // trigger whatsapp template followed by the order
                        do_action('woom_trigger_wa_msg', $order_id, $option_prefix, '', array_unique(array($phone)));
                    }
                endif;
            endforeach;
            return;
        }

        /**
         * Get all row prefix of table of settings
         *
         * @param string $prefix: default is abandoned prefix. allowed options: woom_abandoned_cart_triggers, woom_triggers
         * @return iterable of prefixes for table of settings
         */
        function get_abandoned_trigger_actions($prefix = 'woom_abandoned_cart_triggers')
        {
            $result = array();
            if (empty($prefix)) {
                return $result;
            }
            $actions = get_option($prefix, array());
            if (!empty($actions)) {
                foreach ($actions as $action) {
                    $result[] = substr($prefix, 0, -1) . "_" . $action;
                }
            } else {
                $result[] = substr($prefix, 0, -1) . "_action_1";
            }
            return $result;
        }

        /**
         * Get all table settings keys with value
         * 
         * @param array $args = array('prefix' => 'woom_abandoned_cart_triggers', 'fields' => array())
         * default prefix is woom_abandoned_cart_triggers for abandoned settings table
         * available prefixes: woom_abandoned_cart_triggers, woom_triggers
         * if fields is not empty, selected keys with values will return; else all keys with values will return;
         *
         * @return iterable
         */
        function get_abandoned_triggers_data($args = array('prefix' => 'woom_abandoned_cart_triggers', 'fields' => array()))
        {
            $prefix = (!isset($args['prefix']) || empty($args['prefix'])) ? 'woom_abandoned_cart_triggers' : $args['prefix'];
            $fields = (!isset($args['fields'])) ? array() : $args['fields'];

            // get all prefixes for table of settings 
            $actions = $this->get_abandoned_trigger_actions($prefix);
            if (empty($actions) || !is_array($actions)) {
                return $actions;
            }

            if (empty($fields)) {
                $fields = array(
                    'label',
                    'enabled',
                    'template',
                    'duration',
                    'duration_unit',
                    'header_params',
                    'body_params',
                    'button_params',
                    'coupon_enabled',
                    'coupon_discount_type',
                    'coupon_amount',
                    'coupon_expiry_duration',
                    'coupon_expiry_duration_unit',
                    'coupon_individual',
                    'coupon_auto_apply'
                );
            }
            $result = array();
            foreach ($actions as $action) {
                $result_array = array();
                foreach ($fields as $field) {
                    if (get_option($action . '_enabled', '') === 'yes') {
                        $result_array[$field] = get_option(implode('_', array($action, $field)), '');
                    }
                }
                $result[$action] = $result_array;
            }

            return $result;
        }
        /**
         * GET Abandoned message template parameters
         *
         * @param integer $order_id: default is 0
         * @param string $option_prefix: deafult is empty
         * @return void
         */
        function get_coupon_code($order_id = 0, $option_prefix = '')
        {
            if (empty($order_id) || empty($option_prefix)) {
                return null;
            }
            if (get_option($option_prefix . '_coupon_enabled', false) !== 'yes') {
                return get_option($option_prefix . '_coupon_code', '');
            }
            $coupon_settings = array(
                'enabled' => get_option($option_prefix . '_coupon_enabled', false),
                'discount_type' => get_option($option_prefix . '_coupon_discount_type', 'percentage'),
                'amount' => get_option($option_prefix . '_coupon_amount', 0),
                'expiry_in_seconds' => (int)get_option($option_prefix . '_coupon_expiry_duration', 0),
                'individual' => get_option($option_prefix . '_coupon_individual', false),
                'auto_apply' => get_option($option_prefix . '_coupon_auto_apply', false),
                'expiry_duration_unit' => get_option($option_prefix . '_coupon_expiry_duration_unit', 'minutes'),
            );
            // creating expiry time in seconds for easy use
            switch ($coupon_settings['expiry_duration_unit']) {
                case 'days':
                    $coupon_settings['expiry_in_seconds'] = $coupon_settings['expiry_in_seconds'] * DAY_IN_SECONDS;
                    break;
                case 'hours':
                    $coupon_settings['expiry_in_seconds'] = $coupon_settings['expiry_in_seconds'] * HOUR_IN_SECONDS;
                    break;
                case 'minutes':
                    $coupon_settings['expiry_in_seconds'] = $coupon_settings['expiry_in_seconds'] * MINUTE_IN_SECONDS;
                    break;
            }
            // get generated abandoned coupon by using order and settings
            $abandoned_coupon = $this->woom_generate_coupon($coupon_settings, $order_id);

            return $abandoned_coupon;
        }

        /**
         * Create offer coupon for abandoned order programatically
         */
        function woom_generate_coupon($coupon_data = array(), $order_id = null)
        {
            if (empty($coupon_data) || empty($order_id)) {
                return array();
            }
            $order = wc_get_order($order_id);
            $email  = $order->get_billing_email();
            $coupon_code = uniqid($this->woom_generate_coupon_prefix());
            $coupon = new WC_Coupon();
            $coupon->set_code($coupon_code);
            $coupon->set_description(__('Notiqoo Pro abandoned coupon', 'wc-messaging'));
            // discount type can be 'fixed_cart', 'percent' or 'fixed_product', defaults to 'fixed_cart'
            $coupon->set_discount_type($coupon_data['discount_type']);
            $coupon->set_amount($coupon_data['amount']);
            // $coupon->set_free_shipping(true);

            // coupon expiry date
            $expire_timestamp = current_time('timestamp') + $coupon_data['expiry_in_seconds'];
            $coupon->set_date_expires($expire_timestamp);
            // Usage Restriction

            // minimum spend
            // $coupon->set_minimum_amount(5000);
            // maximum spend
            // $coupon->set_maximum_amount(50000);

            // individual use only
            $coupon->set_individual_use($coupon_data['individual']);
            // allowed emails
            if (!empty($email)) {
                $coupon->set_email_restrictions(array($email));
            }

            // usage limit per coupon
            $coupon->set_usage_limit(1);

            // usage limit per user
            $coupon->set_usage_limit_per_user(1);

            $coupon->save();
            return $coupon_code;
        }
    }
}
