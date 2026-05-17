<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Wcm_Send_Abandoned_Status')) {
    class Wcm_Send_Abandoned_Status
    {

        public function sample_trigger_hook()
        {
            $data = [
                'event' => 'abandoned/recovery ',
                'whatsappNumber' => '+1234567890',
                'name' => 'John Doe',
                'orderId' => 'ORD123456',
                'orderTime' => '2024-11-30T14:30:00Z',
            ];

            // Return as a JSON string
            return json_encode($data);
        }

        public function send_trigger_sample_to_url()
        {
            $url = get_option('woom_abandoned_cart_webhook', true);
            $data = $this->sample_trigger_hook();
            $response = wp_remote_post($url, [
                'body' => $data,
            ]);

            if (is_wp_error($response)) {
                return wp_send_json_error(['message' => $response->get_error_message()]);
            } else {
                $body = wp_remote_retrieve_body($response);
                return wp_send_json_success(['response' => $body]);
            }
            return;
        }

        public function send_abandoned_data($event, $order_id, $whatsapp, $name, $order_date)
        {
            $url = get_option('woom_abandoned_cart_webhook', true);
            $message = [
                'event' => $event,
                'whatsappNumber' => $whatsapp,
                'name' => $name,
                'orderId' => $order_id,
                'orderTime' => $order_date
            ];

            $response = wp_remote_post($url, [
                'body' => $message,
            ]);

            if (is_wp_error($response)) {
                return [
                    'success' => false,
                    'message' => $response->get_error_message(),
                ];
            } else {
                $body = wp_remote_retrieve_body($response);
                return [
                    'success' => true,
                    'response' => $body,
                ];
            }
            return;
        }
        public function abandoned_created_data($order_id = null)
        {
            if(empty($order_id)) {
                return;
            }
            $order = wc_get_order($order_id);
            if ($order) {
                $abandoned_checker = new Wcm_Abandoned_Checker();
                $whatsapp =  $abandoned_checker->get_user_whatsapp_number($order_id);
                $name = $order->get_billing_first_name();
                // Get Order Dates
                $order_date = $order->get_date_created();
                $data = $this->send_abandoned_data('abandoned', $order_id, $whatsapp, $name, $order_date);
                return $data;
            }
            return;
        }
        public function abadonment_recovery_data($order_id = null)
        {
            if (empty($order_id)) {
                return;
            }
            $order = wc_get_order($order_id);
            $abandoned_checker = new Wcm_Abandoned_Checker();
            $whatsapp =  $abandoned_checker->get_user_whatsapp_number($order_id);
            $name = '';

            if (!empty($order)) {
                $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                if (empty($name)) {
                    $name = $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
                }
            }
            if ($order) {
                // Get Order Dates
                $order_date = $order->get_date_completed();
                $data = $this->send_abandoned_data('recovery', $order_id, $whatsapp, $name, $order_date);
                return $data;
            }
            return;
        }
    }
}
