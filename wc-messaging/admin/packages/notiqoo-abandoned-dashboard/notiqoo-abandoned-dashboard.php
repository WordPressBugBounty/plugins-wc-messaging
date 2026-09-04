<?php
if (!defined('ABSPATH')) exit;
class notiqoo_abandoned_dashboard
{
    public function __construct()
    {
        // Register dashboard widget
        add_action('wp_dashboard_setup', array($this, 'register_dashboard_widget'));

        // Enqueue assets only on dashboard page
        add_action('admin_enqueue_scripts', array($this, 'enqueue_dashboard_assets'));

        // AJAX actions for logged-in and guests (if needed)
        add_action('wp_ajax_notiqoo_fetch_abandoned_cart_stats', array($this, 'fetch_abandoned_cart_stats'));
        // Disabled nopriv registration for security hotfix. Only logged-in AJAX allowed.
        // add_action('wp_ajax_nopriv_notiqoo_fetch_abandoned_cart_stats', array($this, 'fetch_abandoned_cart_stats'));
    }
    public function register_dashboard_widget()
    {
        wp_add_dashboard_widget(
            'abdn_dashboard_widget',
            'Notiqoo Abandoned Status',
            array($this, 'render_dashboard_widget')
        );
    }

    public function render_dashboard_widget()
    {
        include plugin_dir_path(__FILE__) . 'dashboard-widget.php';
    }

    public function enqueue_dashboard_assets($hook)
    {
        if ('index.php' !== $hook) {
            return;
        }

        $this->enqueue_styles();
        $this->enqueue_scripts();
        $this->localize_script();
    }

    private function enqueue_styles()
    {
        wp_enqueue_style(
            'abdn-dashboard-style',
            plugin_dir_url(__FILE__) . 'style.css',
            array(),
            '1.0.0'
        );
    }

    private function enqueue_scripts()
    {
        wp_enqueue_script(
            'abdn-dashboard-script',
            plugin_dir_url(__FILE__) . 'dashboard.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }

    private function localize_script()
    {
        wp_localize_script('abdn-dashboard-script', 'abdnDashboardAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('abdn_action'),
        ]);
    }

    public function fetch_abandoned_cart_stats()
    {
        if (!isset($_POST['abdn_security'])) {
            return wp_send_json(array('success' => false, 'message' => __('Verification token missing', 'wc-messaging')), 403);
        }
        // Capability check: only allow administrators (manage_options) to perform this action
        if (! current_user_can('manage_woocommerce')) {
            return wp_send_json(array('success' => false, 'message' => __('Insufficient permissions', 'wc-messaging')), 403);
        }
        $security = isset($_POST['abdn_security']) ? sanitize_text_field(wp_unslash($_POST['abdn_security'])) : '';
        if (! $security || ! wp_verify_nonce($security, 'abdn_action')) {
            wp_send_json_error(['message' => 'Nonce verification failed.'], 403);
            exit;
        }

        $filter = isset($_POST['nq_abandoned_filter']) ? sanitize_text_field(wp_unslash($_POST['nq_abandoned_filter'])) : 'last_month';

        $dates = $this->get_date_range($filter);
        // Filter orders by date range (HPOS compatible)
        $start_ts = strtotime($dates['start']);
        $end_ts   = strtotime($dates['end']);
        $args = [
            'limit'      => -1,
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Reason: This query is acceptable for our use case.
            'meta_query' => array(
                'key'     => 'nq_abandoned_order_created',
                'compare' => 'EXISTS',
            ),
            'date_created'  => $start_ts . '...' . $end_ts,
            'return'     => 'objects',
        ];

        $orders = wc_get_orders($args);


        $orders = array_filter($orders, function ($order) use ($start_ts, $end_ts) {
            $created = $order->get_date_created();
            if (! $created) {
                return false;
            }
            $created_ts = $created->getTimestamp();
            return $created_ts >= $start_ts && $created_ts <= $end_ts;
        });

        // Initialize counters and revenue totals
        $recoverable_count = 0;
        $recovered_count   = 0;
        $lost_count        = 0;

        $recoverable_revenue = 0.0;
        $recovered_revenue   = 0.0;

        foreach ($orders as $order) {
            if (!empty($order->get_meta('nq_abandoned_order_recovered', true))) {
                $recovered_count++;
                $recovered_revenue += (float) $order->get_total();
            } elseif (!empty($order->get_meta('nq_abandoned_order_lost', true))) {
                $lost_count++;
            } else {
                $recoverable_count++;
                $recoverable_revenue += (float) $order->get_total();
            }
        }

        $recovery_rate = 0;
        if ($recoverable_count + $recovered_count > 0) {
            $recovery_rate = round(($recovered_count / ($recoverable_count + $recovered_count)) * 100, 2);
        }

        wp_send_json_success([
            'recoverable_orders'  => $recoverable_count,
            'recovered_orders'    => $recovered_count,
            'lost_orders'         => $lost_count,
            'recoverable_revenue' => wc_price($recoverable_revenue),
            'recovered_revenue'   => wc_price($recovered_revenue),
            'recovery_rate'       => $recovery_rate,
        ]);
    }


    private function get_date_range($filter)
    {
        $now = current_time('timestamp');

        switch ($filter) {
            case 'today':
                $start = gmdate('Y-m-d 00:00:00', $now);
                $end   = gmdate('Y-m-d 23:59:59', $now);
                break;

            case 'yesterday':
                $yesterday = strtotime('-1 day', $now);
                $start = gmdate('Y-m-d 00:00:00', $yesterday);
                $end   = gmdate('Y-m-d 23:59:59', $yesterday);
                break;

            case 'last_week':
                $mondayThisWeek = strtotime('monday this week', $now);
                $startRaw = strtotime('-7 days', $mondayThisWeek);
                $endRaw   = strtotime('+6 days', $startRaw);
                $start = gmdate('Y-m-d 00:00:00', $startRaw);
                $end   = gmdate('Y-m-d 23:59:59', $endRaw);
                break;

            case 'last_month':
            default:
                $start = gmdate('Y-m-01 00:00:00', strtotime('first day of last month', $now));
                $end   = gmdate('Y-m-t 23:59:59', strtotime('last day of last month', $now));
                break;
        }

        return [
            'start' => $start,
            'end'   => $end,
        ];
    }
}
