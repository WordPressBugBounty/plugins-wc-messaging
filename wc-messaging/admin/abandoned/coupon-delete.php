<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class WCM_Abandoned_Coupon_Delete
{

    /**
     * Schedule the weekly coupon deletion event.
     */
    public function schedule_coupon_deletion()
    {
        if (!wp_next_scheduled('wcm_delete_coupons_weekly')) {
            wp_schedule_event(time(), 'weekly', 'wcm_delete_coupons_weekly');
        }
    }

    /**
     * Delete expired coupons weekly.
     */
    public function delete_coupons($coupon_prefix)
    {
        $target_date = current_time('Y-m-d'); // Current date
        $result = array('success' => false, 'message' => __('Something went wrong', 'wc-messaging'));
        $args = [
            'post_type'      => 'shop_coupon',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Reason: This query is acceptable for our use case.
            'meta_query'     => [
                [
                    'key'     => 'date_expires', // WooCommerce expiration date meta key
                    'value'   => strtotime($target_date), // Compare against the target date
                    'compare' => '<=',
                    'type'    => 'NUMERIC',
                ],
            ],
        ];

        $coupons = get_posts($args);
        if (!empty($coupons)) {
            $coupon_ids = array_map(function ($coupon) use ($coupon_prefix) {
                if (strpos(strtolower($coupon->post_title), strtolower($coupon_prefix)) === 0) {
                    return $coupon->ID;
                }
                return null;
            }, $coupons);

            $coupon_ids = array_filter($coupon_ids); // Remove null values

            if (!empty($coupon_ids)) {
                foreach ($coupon_ids as $coupon_id) {
                    wp_delete_post($coupon_id, true);
                    $result = array(
                        'success' => true,
                        'message' => sprintf('%1$s: %2$s', __('Deleted Coupon ID', 'wc-messaging'), esc_attr($coupon_id))
                    );
                }
            } else {
                $result = array(
                    'success' => false,
                    'message' => sprintf('%1$s %2$s %3$s %4$s', __('No valid coupons found with prefix', 'wc-messaging'), esc_attr($coupon_prefix), __('expiring before', 'wc-messaging'), $target_date)
                );
            }
        } else {
            $result = array(
                'success' => false,
                'message' => __("Nothing to delete", 'wc-messaging')
            );
        }
        return $result;
    }
}
