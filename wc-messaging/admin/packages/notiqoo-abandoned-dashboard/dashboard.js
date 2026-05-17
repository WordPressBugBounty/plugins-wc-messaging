jQuery(document).ready(function($){
    function fetchStats(filter) {
        // Show loading state
        $('#recoverable_orders, #recovered_orders, #lost_orders, #recoverable_revenue, #recovered_revenue, #recovery_rate').text('Loading...');

        $.post(abdnDashboardAjax.ajax_url, {   // Use localized ajax_url
            action: 'notiqoo_fetch_abandoned_cart_stats',
            nq_abandoned_filter: filter,
            abdn_security: abdnDashboardAjax.nonce        // Pass the nonce as "security"
        }, function(response) {
            if(response.success) {
                const stats = response.data;

                $('#recoverable_orders').text(stats.recoverable_orders);
                $('#recovered_orders').text(stats.recovered_orders);
                $('#lost_orders').text(stats.lost_orders);
                $('#recoverable_revenue').html(stats.recoverable_revenue);
                $('#recovered_revenue').html(stats.recovered_revenue);
                $('#recovery_rate').text(`${stats.recovery_rate}%`);
            } else {
                $('#recoverable_orders, #recovered_orders, #lost_orders, #recoverable_revenue, #recovered_revenue, #recovery_rate').text('Error');
            }
        });
    }

    // Initialize - load "Last Month" data by default because button has button-primary
    let initialFilter = $('.date-filters .button-primary').data('filter') || 'today';
    fetchStats(initialFilter);

    // Button click event for filters
    $('.date-filters .button').on('click', function() {
        const filter = $(this).data('filter');

        // Update button styles
        $('.date-filters .button').removeClass('button-primary');
        $(this).addClass('button-primary');

        fetchStats(filter);
    });
});
