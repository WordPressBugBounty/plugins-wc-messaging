jQuery(document).ready(function () {
	if (jQuery(".chosen-select")) {
		woom_generate_chosen(jQuery(".chosen-select"));

		jQuery('input[type="checkbox"]').on('change', (event) => {
			woom_update_button_toggle();
		});
	}
});

function woom_generate_chosen(el) {
	let chosen_select = el.chosen({ max_selected_options: el.data('params_count') });
	chosen_select.on('change', (event, params) => {
		const el = jQuery(event.target);
		if (el.data('params_count')) {
			reorder_multiselect(el, params);
			woom_validate_param_option(el, el.data('params_count'));
		}

	});
	jQuery('.woom-field-chosen-select .chosen-container').css({
		'width': "100%",
		'max-width': "calc(135px)",
		'margin-right': "5px"
	});
}
function reorder_multiselect(el, params) {

	let el_values = el.data('chosen_value');
	if (el_values !== '') {
		el_values = el_values.split(',');
	} else {
		el_values = [];
	}

	if (params.selected) {
		el_values.push(params.selected);
	} else if (params.deselected) {
		el_values = jQuery.grep(el_values, function (value) {
			return value != params.deselected;
		});
	}

	el.data('chosen_value', el_values.join(','));
	const no_sel_options = [];
	el.children().not(':selected').each((index, element) => no_sel_options.push(element.value));
	el.empty();
	el_values.forEach(value => el.append(jQuery('<option value="' + value + '" selected>' + value + '</option>')));
	no_sel_options.forEach(value => el.append(jQuery('<option value="' + value + '">' + value + '</option>')));
	el.trigger("chosen:updated");
}


function woom_update_button_toggle() {
	if (jQuery('.wc-email-settings-table-switch.woom-switch-checkbox input')) {
		jQuery('.wc-email-settings-table-switch.woom-switch-checkbox input').each((index, switchInput) => {
			const id_prefix = switchInput.id.replace('enabled', '');
			const header_params = jQuery('#' + id_prefix + 'header_params');
			const body_params = jQuery('#' + id_prefix + 'body_params');
			const button_params = jQuery('#' + id_prefix + 'button_params');
			const template = jQuery('#' + id_prefix + 'template');
			if (jQuery(switchInput).is(":checked")) {

				template.prop('disabled', false);

				if (header_params.data('params_count') > 0) {
					header_params.prop('disabled', false);
				}
				if (body_params.data('params_count') !== body_params.val().length) {
					if (jQuery(body_params).siblings('p').is(':hidden')) {
						jQuery(body_params).siblings('p').show();
					}

					body_params.prop('disabled', false).trigger('chosen:updated');
				}

				if (button_params.data('params_count') !== button_params.val().length) {
					if (jQuery(body_params).siblings('p').is(':hidden')) {
						jQuery(body_params).siblings('p').show();
					}

					button_params.prop('disabled', false).trigger('chosen:updated');
				}

			} else {
				template.prop('disabled', true);
				header_params.prop('disabled', true);
				body_params.prop('disabled', true).trigger('chosen:updated');
				button_params.prop('disabled', true).trigger('chosen:updated');
				if (jQuery(body_params).siblings('p').is(':visible')) {
					jQuery(body_params).siblings('p').hide();
				}

			}
			if (template.val()) {
				jQuery(template.parents('tr')).find('.woom-field-actions a').show();
			} else {
				jQuery(template.parents('tr')).find('.woom-field-actions a').hide();
			}
		})
	}

}

woom_update_button_toggle();



function woom_validate_param_option(el, max_params_count) {
	let error_label = el.siblings('p');

	if (max_params_count > el.val().length) {
		let count = el.val().length;
		let error_msg = jQuery(el).data('error_message').replace("{{count}}", (max_params_count - count));
		error_label.html(error_msg);
		if (error_label.hasClass('woom-success-text')) {
			error_label.removeClass('woom-success-text');
		}
		if (!error_label.hasClass('woom-error-text')) {
			error_label.addClass('woom-error-text');
		}
		if (error_label.is(':hidden')) {
			error_label.show();
		}
	} else if (el.val().length > max_params_count) {
		if (!error_label.hasClass('woom-error-text')) {
			error_label.removeClass('woom-success-text').addClass('woom-error-text');
		}
		if (error_label.is(':hidden')) {
			error_label.show();
		}
		error_label.html("no parameters allowed");
	} else {
		if (error_label.is(':visible')) {
			if (error_label.hasClass('woom-error-text')) {
				error_label.removeClass('woom-error-text');
			}
			if (!error_label.hasClass('woom-success-text')) {
				error_label.addClass('woom-success-text');
			}
			error_label.hide();
		}

	}
	woom_update_button_toggle();
}

function woom_copy_field_val(item) {
	item.preventDefault();
	var input = jQuery(item.target).siblings('input')[0];
	var copyText = document.getElementById(input.id);
	copyText.select();
	copyText.setSelectionRange(0, 99999);
	navigator.clipboard.writeText(copyText.value);
	const inactive_text = jQuery(item.target).text();
	jQuery(item.target).text(jQuery(item.target).attr('data-activeText'));
	setTimeout(() => {
		jQuery(item.target).text(inactive_text);
	}, 3000);
}

function woom_toggle_coupon_type(event) {
	const static_fields = ['coupon_code'];
	const dynamic_fields = ['coupon_discount_type', 'coupon_amount', 'coupon_expiry_duration', 'coupon_individual', 'coupon_auto_apply'];
	const target = jQuery(event.target).closest('tr');

	const dynamic_label_row = target.find('td[data-fixed]');
	let new_label = dynamic_label_row.text();

	if (jQuery(event.target).is(':checked')) {
		new_label = dynamic_label_row.data('dynamic');

		dynamic_fields.forEach(field => {
			target.siblings('tr').find(`[id$='${field}']`).closest('tr').show();
		});
		static_fields.forEach(field => {
			target.siblings('tr').find(`[id$='${field}']`).closest('tr').hide();
		});
	} else {
		new_label = dynamic_label_row.data('fixed');

		dynamic_fields.forEach(field => {
			target.siblings('tr').find(`[id$='${field}']`).closest('tr').hide();
		});
		static_fields.forEach(field => {
			target.siblings('tr').find(`[id$='${field}']`).closest('tr').show();
		});
	}
	dynamic_label_row.text(new_label);

}


function woom_addnew_row(target) {
	const id_prefix = jQuery(target).data('row');
	let table = jQuery(target).parents('table');
	let last_row = table.find('tbody tr:last');
	let row_count = table.find('tbody tr.woom-field-row').length;
	let new_row = jQuery(last_row).clone();
	let new_id = '';

	for (const input of jQuery(new_row).find(':input')) {
		let field_id = jQuery(input).attr('id');
		if (field_id) {
			// key_end = key_end.replace(id_prefix + '_', '');
			field_id = field_id.split('_');
			field_id = field_id.map(item => {
				if (!isNaN(parseFloat(item))) {
					item = row_count + 1;
				}
				return item;
			});
			new_id = field_id.join('_');
			jQuery(input).attr({
				'name': new_id,
				'id': new_id,
			});

			if (new_id.includes('_label')) {
				if (new_id.includes('hook')) {
					jQuery(input).val('hook_' + (row_count + 1));
				} else if (new_id.includes('action')) {
					jQuery(input).val('Button ' + (row_count + 1));
				} else {
					jQuery(input).val('Option ' + (row_count + 1));
				}
			} else if (input.type) {
				if (jQuery(input).siblings('label').length > 0) {
					jQuery(input).siblings('label').attr('for', new_id)
				}
				switch (input.type) {
					case 'checkbox':
						jQuery(input).prop('checked', false);

						break;
					case 'text':
					case 'select-one':
						jQuery(input).val('');

						break;
					case 'select-multiple':
						jQuery(input).val([]);

						break;
					default:
						if (jQuery(input).prop("tagName").toLowerCase() === 'button' && jQuery(input).attr("disabled")) {
							jQuery(input).attr("disabled", false);
						}
						if (jQuery(input).siblings('a')) {
							jQuery(input).siblings('a').hide();
						}
						if (input.closest('a')) {
						}
						break;
				}
			}
			jQuery.each(input.attributes, function () {
				if (this.value.includes(row_count)) {
					this.value = this.value.replace(row_count, (row_count + 1))
				}
			});
		}
	}

	jQuery.ajax({
		type: "post",
		dataType: "json",
		url: woom_ajax.url,
		data: {
			action: "woom_autosave_manual_trigger_actions",
			data: {
				key: 'action_' + (row_count + 1),
				actions_key: id_prefix + 's',
				woom_nonce: woom_ajax.woom_post_nonce
			}
		},
		success: function (response) {
			if (response?.status === 'success') {
				location.reload();
			}
		}
	});
}
function woom_trigger_button(event, item) {

	event.preventDefault();
	const action_btn_label = jQuery(item).text();
	const action_btn_loader = '<span class="dashicons dashicons-update woom-rotate rotate"></span>';
	jQuery(item).html(action_btn_label + action_btn_loader);
	jQuery(item).parent().find('button').prop('disabled', true);

	jQuery.ajax({
		type: "post",
		dataType: "json",
		url: woom_ajax.url,
		data: {
			action: "woom_manual_trigger_action",
			data: {
				order_id: jQuery(item).data('order-id'),
				slug_prefix: jQuery(item).data('prefix'),
				woom_nonce: woom_ajax.woom_post_nonce
			}
		},
		success: function (response) {
			jQuery(item).parent().find('button').prop('disabled', false);
			jQuery(item).html(action_btn_label);
			let response_message = '';
			if (response?.success && response?.message) {
				response_message = '<div id="message" class="updated notice notice-success"><p>' + response?.message + '</p></div>';
			} else {
				if (response?.message) {
					response_message = '<div id="message" class="notice notice-error"><p>' + response?.message + '</p></div>';
				} else {
					console.log(response);
				}
			}

			if (response_message !== '') {
				const status_el = jQuery(response_message).insertAfter(jQuery('#woom_trigger_status'));
				setTimeout(() => {
					jQuery(status_el).fadeOut('slow');
				}, 5000);
			}

		}
	});
}

function woom_remove_field_trigger(event, target) {
	event.preventDefault();
	let data_options = {
		prefix: jQuery(target).closest('table').find('a[data-row]').data('row'),
	};
	data_options = {
		prefix: data_options.prefix,
		container: data_options.prefix + 's',
		item: target.id.replace(data_options.prefix + '_', '').replace('_remove', '')
	}

	data_options.woom_nonce = woom_ajax.woom_post_nonce;
	jQuery.ajax({
		type: "post",
		dataType: "json",
		url: woom_ajax.url, //URL to your wordpress install's admin-ajax.php file
		data: {
			action: "woom_clear_option",
			data: data_options
		},
		success: function (response) {
			if (response?.status === 'success') {
				jQuery(target).closest('tr').remove();
			}
		}
	});
}

function woom_regenerate_templates(event, target) {
	event.preventDefault();
	jQuery(target).append(' <span class="dashicons rotate dashicons-update"></span>');
	const token = jQuery(target).data('access-token');
	jQuery.ajax({
		type: "post",
		dataType: "json",
		url: woom_ajax.url, // URL to your wordpress install's admin-ajax.php file
		data: {
			action: "woom_regenerate_wa_templates",
			data: { woom_nonce: woom_ajax.woom_post_nonce, woom_access_token: token }
		},
		success: function (response) {
			jQuery(target).children(jQuery('.dashicon.rotate')).remove();
			if (response.success) {
				jQuery('#woom-ajax-result').html(response.data);
				setTimeout(() => {
					location.reload();
				}, 800);
			} else {
				if (response.data) {
					jQuery('#woom-ajax-result').html(response.data);
				}

			}
		}
	});
}

function woom_prevent_element(event, trigger = false) {
	if (!trigger) {
		event.stopPropagation();
	}

}

function woom_update_template_preview(target, params_counts) {

	let link = jQuery(target).parents().closest('tr').find('.woom-field-actions a.link');
	let params_count = {
		header: 0, body: 0, footer: 0
	};
	if (jQuery(target).val()) {
		params_count = params_counts[jQuery(target).val()].params_count;
		link.data('id', 'woom_template_table_' + jQuery(target).val());
		if (jQuery(link).is(':hidden')) {
			jQuery(link).show();
		}
	}
	let header_params_disabled = (params_count.header === 0);
	let body_params_disabled = (params_count.body === 0);
	let button_params_disabled = (params_count.button === 0);
	let prefix = (target.id).replace('_template', '');

	let header_params = jQuery('#' + prefix + '_header_params');
	header_params.prop('disabled', header_params_disabled);
	header_params.val('');
	header_params.data('params_count', params_count.header);
	let body_params = jQuery('#' + prefix + '_body_params');
	body_params.prop('disabled', body_params_disabled);
	body_params.data('params_count', params_count.body);
	if (body_params.data('chosen')) {
		body_params.data('chosen_value', '');
		body_params.data('chosen').max_selected_options = params_count.body;
	}
	body_params.val([]).trigger('chosen:updated');

	let button_params = jQuery('#' + prefix + '_button_params');
	button_params.prop('disabled', button_params_disabled);
	button_params.data('params_count', params_count.button);
	if (button_params.data('chosen')) {
		button_params.data('chosen_value', '');
		button_params.data('chosen').max_selected_options = params_count.button;
	}
	button_params.val([]).trigger('chosen:updated');

	let header_empty_label = body_params.data('params_label_empty');
	let header_avail_label = body_params.data('params_label');

	header_params.find('option').each((index, item) => {
		if (item.value == '') {
			jQuery(item).html((params_count.header > 0) ? header_avail_label : header_empty_label);
		}
	})

	woom_update_button_toggle();
	woom_validate_param_option(body_params, params_count.body);
	woom_validate_param_option(button_params, params_count.button);
}

function woom_handle_templates() {
	woom_update_button_toggle();
}
function woom_doc_download(event) {
	event.preventDefault();
	var btn = event.target;
	let textarea = jQuery(btn).parents('.woom-info-container').children('.woom_support_diagnostic_info')[0];
	textarea = jQuery(textarea).html();
	textarea = textarea.replace(/<br>/g, "\n");
	var blob = new Blob([textarea], { type: 'text/plain' });

	var link = document.createElement('a');
	link.download = 'wc-messaging-status.txt';
	link.href = window.URL.createObjectURL(blob);
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);
}
function woom_doc_copy(event) {
	event.preventDefault();
	var btn = event.target;
	let textarea = jQuery(btn).parents('.woom-info-container').children('.woom_support_diagnostic_info')[0];
	textarea = jQuery(textarea).html();
	textarea = textarea.replace(/<br>/g, "\n");
	navigator.clipboard.writeText(textarea).then(function () {
	}).catch(function (err) {
		console.error('Failed to copy text: ', err);
	});
}

// Abandoned event triggers

function woom_delete_abandoned_coupons(event) {
	event.preventDefault();
	alert("Abandoned coupons removed manually");
}

function woom_sample_abandoned_webhook_action(event) {
	event.preventDefault();
	alert("Sample abandoned webhook action");

}


/**
 * Deletes abandoned coupons
 *
 * @param {Event} event - The event object that triggered this function.
 * @returns {void}
 */
function woom_delete_abandoned_coupons(event) {
	event.preventDefault();
	jQuery.ajax({
		url: ajaxurl, // WordPress AJAX endpoint
		method: 'POST',
		data: {
			action: 'delete_abandoned_coupons', // Action name defined in PHP
			woom_nonce: woom_ajax.woom_post_nonce
		},
		success: function (response) {
			let message = '';
			if (response.success) {
				message = '<div id="message" class="updated inline"><p><strong>' + response?.message + '</strong></p></div>';
			} else {
				message = '<div id="message" class="error inline"><p><strong>' + response?.message + '</strong></p></div>';
			}
			if (message) {
				jQuery('#woom-ajax-result').after(message);
				window.scrollTo({
					top: 0,
					left: 0,
					behavior: 'smooth' // This enables smooth scrolling
				});
			}
		},
		error: function (xhr, status, error) {
			console.error("AJAX Error:", error);
			alert("Failed to delete coupons.");
		}
	});
}


/**
 * Sends a sample abandoned webhook action to the server.
 * 
 * This function prevents the default event behavior, then sends an AJAX request
 * to the WordPress backend to trigger a sample abandoned webhook action.
 * It displays an alert based on the success or failure of the request.
 *
 * @param {Event} event - The event object that triggered this function.
 * @returns {void} This function doesn't return a value.
 */
function woom_sample_abandoned_webhook_action(event) {
	event.preventDefault();
	// alert("Sample abandoned webhook action");

	// AJAX request to WordPress backend
	jQuery.ajax({
		url: ajaxurl, // WordPress AJAX endpoint
		method: 'POST',
		data: {
			action: 'send_trigger_sample_to_url', // Action name defined in PHP
		},
		success: function (response) {
			if (response.success) {
				alert("Data sent successfully! ");
			} else {
				alert("Error: " + response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("AJAX Error:", error);
			alert("Failed to send data to the server.");
		}
	});

}


function woom_toggle_template_popup(event, target, show = true) {
	event.preventDefault();
	let attr_data = {}
	if (jQuery(target).data()) {
		attr_data = jQuery(target).data();
	}

	if (show) {
		if (jQuery("#woom-wa-templates")) {
			jQuery("#woom-wa-templates").remove();
		}

		let data = { woom_nonce: woom_ajax.woom_post_nonce };

		Object.values(attr_data).filter(attr => {
			if (attr.includes('woom_template_table')) {
				data.template = parseFloat(attr.replace('woom_template_table_', ''));
				data.coupon_prefix = null;
				jQuery(document).ready(function ($) {
					jQuery.ajax({
						type: "post",
						dataType: "json",
						url: woom_ajax.url,
						data: {
							action: "woom_ajax_popup",
							data: data
						},
						success: function (res) {
							if (res.success) {
								const content = res.content;
								jQuery('body').append(content);
							} else {
								console.log(res);
							}

						}, error: function (err) {
							console.log('error', err);

						}
					});
				});
			}
			if (attr.includes('woom_abandoned_cart_trigger_action')) {
				if (attr_data?.prefix) {
					jQuery("." + attr_data?.prefix + "_row").toggle();
				}
			}
		});

	} else {
		if (attr_data?.prefix) {
			jQuery("." + attr_data?.prefix + "_row").hide();
		} else {
			jQuery("#woom-wa-templates").remove();
		}
	}
}


function woom_toggle_header_attachment(event, target) {
	event.preventDefault();
	let attr_data = {}

	if (jQuery(target).data()) {
		attr_data = jQuery(target).data();
	}

	if (attr_data?.prefix) {
		const section = `${attr_data?.prefix}_attachment_section`;
		jQuery(`.${section} tr`).toggle('slide');


	}
}
function woom_toggle_coupon_popup(event, target) {
	event.preventDefault();
	let attr_data = {}
	if (jQuery(target).data()) {
		attr_data = jQuery(target).data();
	}


	Object.values(attr_data).filter(attr => {

		if (attr_data?.prefix) {
			jQuery(target).closest('tr').next('tr.woom-collapse-table').find('tr.woom-toggle-title').toggle();

			if (jQuery("#" + attr_data?.prefix + "_coupon_enabled").is(':hidden')) {
				jQuery("." + attr_data?.prefix + "_row").show();
				const static_fields = ['_coupon_code'];
				const dynamic_fields = ['_coupon_discount_type', '_coupon_amount', '_coupon_expiry_duration', '_coupon_individual', '_coupon_auto_apply'];
				if (jQuery("#" + attr_data?.prefix + "_coupon_enabled").is(':checked')) {
					static_fields.forEach(input_id => {
						jQuery("#" + attr_data?.prefix + input_id).closest('tr').hide();
					});
				} else {
					dynamic_fields.forEach(input_id => {
						jQuery("#" + attr_data?.prefix + input_id).closest('tr').hide();
					});
				}
			} else {
				jQuery("." + attr_data?.prefix + "_row").hide();
			}

		}
	});

}
function nq_dismiss_notice(event) {
	event.preventDefault();
	let params = jQuery(event.target).data();
	params.woom_nonce = woom_ajax.woom_post_nonce;
	jQuery.ajax({
		type: "post",
		dataType: "json",
		url: woom_ajax.url, //URL to your wordpress install's admin-ajax.php file
		data: {
			action: "woom_dismiss_review_notice",
			data: params
		},
		success: function (response) {
			console.log(response);

			if (response?.success) {
				jQuery(event.target).parents('.notice').fadeOut();
				if (!(/#$/.test(event.target.href))) {
					window.open(event.target.href, "_blank");
				}
			}
		}
	});
}