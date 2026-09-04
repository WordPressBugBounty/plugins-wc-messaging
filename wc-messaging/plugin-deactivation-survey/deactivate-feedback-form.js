(function ($) {

    if (!window.codecabin)
        window.codecabin = {};

    if (codecabin.DeactivateFeedbackForm)
        return;

    codecabin.DeactivateFeedbackForm = function (plugin) {
        var self = this;
        var strings = sgits_deactivate_feedback_form_strings;
        this.plugin = plugin;

        // Dialog HTML
        var element = $('\
			<div class="sgits-deactivate-dialog" data-remodal-id="' + plugin.slug + '">\
            <form>\
            <div class="sgits-deactivate-dialog-logo"><img src="'+ sgits_deactivate_feedback_logo.url + '"></div>\
					<input type="hidden" name="plugin"/>\
					<h2>' + strings.quick_feedback + '</h2>\
					<p>\
						' + strings.foreword + '\
					</p>\
					<ul class="sgits-deactivate-reasons"></ul>\
                    <label>' + strings.brief_description + '\
					<textarea rows="3" name="message" placeholder=""></textarea>\
                    </label><br>\
                    <div class="nq-data-collect-info-container nq-data-collect-box"><span class="dashicons dashicons-shield-alt"></span><div class="nq-data-collect-info-content nq-data-collect-box-content"><h3 class="nq-data-collect-box-title">Your privacy matters</h3><p class="nq-data-collect-box-description">We never collect or store sensitive information such as emails, domain names, user data, or WhatsApp messages. Any diagnostic data collected is anonymous and used only to improve compatibility and performance.</p></div></div><div class="nq-data-collect-consent-container nq-data-collect-box"><input type="checkbox" name="consent_for_diagnostics" id="consent_for_diagnostics" checked><label for="consent_for_diagnostics"><div class="nq-data-collect-consent-content nq-data-collect-box-content"><h3 class="nq-data-collect-box-title">Allow diagnostic data collection (recommended)</h3><div class="nq-data-collect-box-description">Share anonymous information about your WordPress environment and plugin usage to help us fix issues faster and improve compatibility. <a href="https://notiqoo.com/privacy-policy/?utm_source=free-plugin&utm_medium=popup&utm_campaign=deactivation" target="_blank" class="nq-data-collect-box-action">Learn more about what we collect <i class="dashicons dashicons-external"></i></a></div></div></label><textarea name="nq_diagnosis_data" style="display:none;" id="nq_diagnosis_data">'+ plugin.diagnosis_data + '</textarea></div>\
					<p class="sgits-deactivate-dialog-buttons">\
						<input type="submit" class="button confirm" value="' + strings.skip_and_deactivate + '"/>\
						<button data-remodal-action="cancel" class="button button-primary">' + strings.cancel + '</button>\
					</p>\
				</form>\
			</div>\
		')[0];
        this.element = element;

        $(element).find("input[name='plugin']").val(JSON.stringify(plugin));
        $(element).on("change", "input[name='reason']", function (event) {

            $(element).find("input[type='submit']").val(
                strings.submit_and_deactivate
            );
        });

        $(element).find("form").on("submit", function (event) {
            self.onSubmit(event);
        });

        // Reasons list
        var ul = $(element).find("ul.sgits-deactivate-reasons");
        for (var key in plugin.reasons) {
            var li = $("<li><label><input type='radio' name='reason'/> <span></span></label></li>");

            $(li).find("input").val(key);
            $(li).find("span").html(plugin.reasons[key]);

            $(ul).append(li);
        }

        // Listen for deactivate
        $("#the-list [data-slug='" + plugin.slug + "'] .deactivate>a").on("click", function (event) {
            self.onDeactivateClicked(event);
        });
    }

    codecabin.DeactivateFeedbackForm.prototype.onDeactivateClicked = function (event) {
        this.deactivateURL = event.target.href;

        event.preventDefault();

        if (!this.dialog)
            this.dialog = $(this.element).remodal();
        this.dialog.open();
    }

    codecabin.DeactivateFeedbackForm.prototype.onSubmit = function (event) {
        var element = this.element;
        var strings = sgits_deactivate_feedback_form_strings;
        var self = this;
        var formData = $(element).find("form").serializeArray();
        formData.push({
            name: 'domain',
            value: location.origin
        });
        var data = $.param(formData);
        data += '&domain=' + encodeURIComponent(location.origin);

        $(element).find("button, input[type='submit']").prop("disabled", true);

        const submit_btn = $(element).find("input[type='submit']");
        if ($(element).find("input[name='reason']:checked").length) {
            event.preventDefault();
            submit_btn.siblings().hide();
            submit_btn.val(strings.please_wait);
            const params = new URLSearchParams(data);
            if (!params.get('consent_for_diagnostics')) {
                params.delete('nq_diagnosis_data');
            }
            $.ajax({
                type: "POST",
                url: "https://notiqoo.com/wp-json/deactivate-survey/v1/plugin-feedback",
                data: data,
                complete: function () {
                    submit_btn.val(strings.thank_you);
                    window.location.href = self.deactivateURL;
                }
            });
        } else {
            submit_btn.val(strings.please_wait);
            window.location.href = self.deactivateURL;
        }

        event.preventDefault();
        return false;
    }

    $(document).ready(function () {

        for (var i = 0; i < sgits_deactivate_feedback_form_plugins.length; i++) {
            var plugin = sgits_deactivate_feedback_form_plugins[i];
            new codecabin.DeactivateFeedbackForm(plugin);
        }

    });

})(jQuery);