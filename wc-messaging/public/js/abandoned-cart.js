(function ($) {
    'use strict';

    $(function () {

        $(document).on('change', 'input[name="billing_phone"], #billing_phone', function () {

            var phone = $.trim($(this).val());

            if (phone === '') {
                return;
            }

            $.post(nq_guest_capture.ajax_url, {
                action: 'nq_capture_guest_checkout',
                nonce: nq_guest_capture.nonce,
                billing_phone: phone,
                billing_email: $('#billing_email').val(),
                billing_first_name: $('#billing_first_name').val(),
                billing_last_name: $('#billing_last_name').val()
            });

        });

    });

})(jQuery);