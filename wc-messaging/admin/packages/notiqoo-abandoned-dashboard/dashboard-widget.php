<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="custom-status-dashboard">
    <div class="date-filters">
        <button class="button" data-filter="today">Today</button>
        <button class="button" data-filter="yesterday">Yesterday</button>
        <button class="button" data-filter="last_week">Last Week</button>
        <button class="button button-primary" data-filter="last_month">Last Month</button>
    </div>

    <div class="status-grid">
        <div class="status-box">
            <h3 class="status-box-title">Recoverable Orders</h3>
            <p class="status-value" id="recoverable_orders">-</p>
            <p class="status-desc">Total Recoverable Orders.</p>
        </div>
        <div class="status-box">
            <h3 class="status-box-title">Recovered Orders</h3>
            <p class="status-value" id="recovered_orders">-</p>
            <p class="status-desc">Total Recovered Orders.</p>
        </div>
        <div class="status-box">
            <h3 class="status-box-title">Lost Orders</h3>
            <p class="status-value" id="lost_orders">-</p>
            <p class="status-desc">Total Lost Orders.</p>
        </div>
        <div class="status-box">
            <h3 class="status-box-title">Recoverable Revenue</h3>
            <p class="status-value" id="recoverable_revenue">-</p>
            <p class="status-desc">Total Recoverable Revenue.</p>
        </div>
        <div class="status-box">
            <h3 class="status-box-title">Recovered Revenue</h3>
            <p class="status-value" id="recovered_revenue">-</p>
            <p class="status-desc">Total Recovered Revenue.</p>
        </div>
        <div class="status-box">
            <h3 class="status-box-title">Recovery Rate</h3>
            <p class="status-value" id="recovery_rate">-</p>
            <p class="status-desc">Total Percentage Of Recovered Orders After Abandoned.</p>
        </div>
    </div>
</div>
