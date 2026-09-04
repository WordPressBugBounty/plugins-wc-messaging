<?php

if (!defined('ABSPATH')) {
    exit;
}
if (empty($links) || !is_array($links)) {
    exit;
}
global $current_section;
$headings = array(
    'title' => __('Order status', 'wc-messaging'),
    'enable_switch' => __('Enable / disable', 'wc-messaging'),
    'template' => __('Template name', 'wc-messaging'),
    'header_params' => __('Header variables', "wc-messaging"),
    'body_params' => __('Body variables', "wc-messaging"),
    'button_params' => __('Action button', "wc-messaging"),
    'actions' => __('Actions', "wc-messaging")
);
if (!is_array($links['fields'])) {
    return;
}
if (isset($links['custom_templates_only'])) {
    $headings = $links['template_titles'];
} else {
    if (isset($links['template_titles'])) {
        foreach ($links['template_titles'] as $field => $title) {
            if (array_key_exists($field, $headings)) {
                $headings[$field] = $title;
            }
        }
    }
}
?>
<table class="wc_emails widefat woom-template-settings <?php echo esc_attr(str_replace("_", "-", $links['type'])); ?>" cellspacing="0">
    <thead>
        <tr>
            <?php
            foreach ($headings as $head_id => $heading) {
                echo '<th class="wc-email-settings-table-' . esc_attr($head_id) . '">' . esc_html($heading) . '</th>';
            }
            ?>
        </tr>
    </thead>
    <tbody class="woom-table-body">
        <?php
        foreach ($links['fields'] as $index => $fields) :
            $has_admin_row = isset($fields[0]['custom_attributes']['rowspan']);
        ?>
            <tr class="woom-field-row woom-field-row-<?php echo esc_attr($index + 1); ?> <?php echo ($has_admin_row) ? 'has-admin-row' : ''; ?>">
                <?php
                foreach ($fields as $field) {
                    if ($field['type'] !== 'label') {
                        $field['value'] = '';
                        if (array_key_exists('default', $field) && $field['value'] === '') {
                            $field['value'] = $field['default'];
                        }
                        $field['value'] = get_option($field['id'], '');
                        if (!array_key_exists('placeholder', $field)) {
                            $field['placeholder'] = '';
                        }
                    }
                    $custom_attributes = array();
                    if (!empty($field['custom_attributes'])) {
                        foreach ($field['custom_attributes'] as $attr_key => $attr_val) {
                            $custom_attributes[] = $attr_key . "=" . $attr_val;
                        }
                    }
                    $custom_attributes = implode(' ', $custom_attributes);
                    switch ($field['type']) {
                        case 'label':
                ?>
                            <td <?php echo esc_html($custom_attributes); ?> class="wc-email-settings-table-name-with-tooltip <?php echo (strpos($custom_attributes, 'rowspan') !== false) ? 'allign-baseline' : ''; ?>">
                                <?php
                                printf('<span>%s</span>', wp_kses_post($field['name']));
                                if (isset($field['desc']) && !empty($field['desc'])) {
                                    if (isset($field['desc_tip'])) {
                                        if ($field['desc_tip'] === true) {
                                            printf(wp_kses_post(wc_help_tip($field['desc'])));
                                        } else {
                                            printf('<span>%s</span>', wp_kses_post($field['desc']));
                                        }
                                    } else {
                                        printf('<span>%s</span>', wp_kses_post($field['desc']));
                                    }
                                }
                                break;
                            case 'switch':
                                ?>
                            <td class="wc-email-settings-table-switch woom-switch-checkbox" <?php echo esc_html($custom_attributes); ?>>
                                <?php
                                if ($field['has_admin_row']) {
                                    printf('<span>%1$s</span>', esc_html($field['label']));
                                }

                                if ($this->woom_checkbox_valid($field['value'])) {
                                    printf('<input type="checkbox" id="%1$s" value="yes" name="%1$s" checked %2$s>', esc_attr($field['id']), esc_html($custom_attributes));
                                } else {
                                    printf('<input type="checkbox" id="%1$s" value="yes" name="%1$s" %2$s>', esc_attr($field['id']), esc_attr($custom_attributes));
                                }
                                printf('<label for="%s" class="switch"></label>', esc_attr($field['id']));
                                if (isset($field['desc']) && !empty($field['desc'])) {
                                    if (isset($field['desc_tip'])) {
                                        if ($field['desc_tip'] === true) {
                                            printf(wp_kses_post(wc_help_tip($field['desc'])));
                                        } else {
                                            printf('<span>%s</span>', wp_kses_post($field['desc']));
                                        }
                                    } else {
                                        printf('<span>%s</span>', wp_kses_post($field['desc']));
                                    }
                                }
                                break;
                            case 'checkbox':
                                if ($this->woom_checkbox_valid($field['value'])) {
                                    printf('<td class="woom-field-checkbox" %2$s><input type="checkbox" id="%1$s" name="%1$s" value="yes" checked>', esc_attr($field['id']), esc_html($custom_attributes));
                                } else {
                                    printf('<td class="woom-field-checkbox" %2$s><input type="checkbox" id="%1$s" value="yes" name="%1$s">', esc_attr($field['id']), esc_html($custom_attributes));
                                }

                                break;
                            case 'text':

                                ?>
                            <td class="woom-field-text" <?php echo esc_html($custom_attributes); ?>>
                                <?php
                                printf('<input type="text" id="%1$s" name="%1$s" placeholder="%2$s" value="%3$s" %4$s>', esc_attr($field['id']), esc_attr($field['placeholder']), esc_attr($field['value']), esc_html($custom_attributes));
                                if (isset($field['desc']) && !empty($field['desc'])) {
                                    if (isset($field['desc_tip'])) {
                                        if ($field['desc_tip'] === true) {
                                            printf(wp_kses_post(wc_help_tip($field['desc'])));
                                        } else {
                                            printf('<span>%s</span>', wp_kses_post($field['desc']));
                                        }
                                    } else {
                                        printf('<span>%s</span>', wp_kses_post($field['desc']));
                                    }
                                }
                                break;
                            case 'textarea':

                                ?>
                            <td class=" with-tooltip" <?php echo esc_html($custom_attributes); ?>>
                                <?php
                                printf('<textarea id="%1$s" name="%1$s" placeholder="%2$s" value="%3$s"></textarea>', esc_attr($field['id']), esc_attr($field['placeholder']), esc_attr(($field['value'])));
                                if (isset($field['desc']) && !empty($field['desc'])) {
                                    if (isset($field['desc_tip'])) {
                                        if ($field['desc_tip'] === true) {
                                            printf(wp_kses_post(wc_help_tip($field['desc'])));
                                        } else {
                                            printf('<span>%s</span>', wp_kses_post($field['desc']));
                                        }
                                    } else {
                                        printf('<span>%s</span>', wp_kses_post($field['desc']));
                                    }
                                }
                                break;
                            case 'select':
                                $field_value = isset($field['default']) ? $field['default'] : '';
                                $field_value = get_option($field['id'], $field_value);
                                $custom_attributes = '';
                                if (isset($field['custom_attributes'])) {
                                    foreach ($field['custom_attributes'] as $attr_key => $attr_val) {
                                        if ($custom_attributes !== '') {
                                            $custom_attributes .= ' ';
                                        }
                                        $custom_attributes .= sprintf('%1$s=%2$s', $attr_key, $attr_val);
                                    }
                                }
                                if (isset($field['disabled'])) {
                                    if ($custom_attributes !== '') {
                                        $custom_attributes .= ' ';
                                    }
                                    if ($field['disabled']) {
                                        $custom_attributes .= 'disabled="true"';
                                    }
                                }
                                ?>
                            <td class="woom-field-select" <?php echo esc_html($custom_attributes) ?>>
                                <?php
                                printf('<select name="%1$s" id="%1$s" %2$s>', esc_attr($field['id']), esc_attr($custom_attributes));
                                foreach ($field['options'] as $option_value => $option_text) {
                                    if (trim($option_value) === trim($field_value) && !empty(trim($field_value))) {
                                        printf('<option value="%1$s" selected>%2$s</option>', esc_attr($option_value), esc_html($option_text));
                                    } else {
                                        printf('<option value="%1$s">%2$s</option>', esc_attr($option_value), esc_html($option_text));
                                    }
                                }
                                printf('</select>');
                                if (isset($field['desc']) && !empty($field['desc'])) {
                                    if (isset($field['desc_tip'])) {
                                        if ($field['desc_tip'] === true) {
                                            printf(wp_kses_post(wc_help_tip($field['desc'])));
                                        } else {
                                            printf('<span>%s</span>', wp_kses_post($field['desc']));
                                        }
                                    } else {
                                        printf('<span>%s</span>', wp_kses_post($field['desc']));
                                    }
                                }
                                break;
                            case 'actions':
                                printf('<td class="woom-field-actions" %1$s><div class="woom-template-cofig-actions">', esc_html($custom_attributes));
                                foreach ($field['options'] as $field_opt) {
                                    switch ($field_opt['type']) {
                                        case 'button':
                                            if (isset($field_opt['show_only_if_editable']) && $field_opt['show_only_if_editable']) {
                                                $attr = "";

                                                if (isset($field_opt['data'])) {
                                                    foreach ($field_opt['data'] as $key => $value) {
                                                        $attr .= 'data-' . $key . '="' . $value . '"';
                                                    }
                                                }
                                                if (strpos($field_opt['id'], '_1_') === false) {
                                                    echo '<button ' . esc_attr($attr) . ' id="' . esc_html($field_opt['id']) . '" class="button remove-opt button-small btn button-secondary" onClick="woom_remove_field_trigger(event, this)">' . esc_html($field_opt['name']) . '</button>';
                                                }
                                            }
                                            break;
                                        case 'link':

                                            $template_id = get_option(str_replace('actions', 'template', $field['id']), '');
                                            $attr = sprintf('data-id=%s', esc_attr('woom_template_table_' . $template_id));
                                            printf('<a href="#" class="button button-small link preview-btn" %1$s title="%2$s" onclick="woom_toggle_template_popup(event, this)">%2$s</a>', esc_attr($attr), esc_html($field_opt['name']));

                                            break;
                                        case 'header_attachment':
                                            $template_id = get_option(str_replace('actions', 'template', $field['id']), '');
                                            $attr = sprintf('data-prefix=%s', esc_attr($field_opt['data']['prefix']));
                                            $title = (isset($field_opt['field_title'])) ? $field_opt['field_title'] : $field_opt['name'];
                                            printf('<button class="button button-small link" %1$s title="%3$s" onclick="woom_toggle_header_attachment(event, this)">%2$s</button>', esc_attr($attr), esc_html($field_opt['name']), esc_attr($title));

                                            break;
                                        case 'coupon_code':
                                            $template_id = get_option(str_replace('actions', 'template', $field['id']), '');
                                            $attr = sprintf('data-prefix=%s', esc_attr($field_opt['data']['prefix']));
                                            $title = (isset($field_opt['field_title'])) ? $field_opt['field_title'] : $field_opt['name'];
                                            printf('<button class="button button-small link" %1$s title="%3$s" onclick="woom_toggle_coupon_popup(event, this)">%2$s</button>', esc_attr($attr), esc_html($field_opt['name']), esc_attr($title));

                                            break;

                                        default:
                                            break;
                                    }
                                }
                                echo "</div>";
                                break;
                            case 'chosen-select':
                                $custom_attributes = '';
                                if (isset($field['custom_attributes'])) {
                                    foreach ($field['custom_attributes'] as $attr_key => $attr_val) {
                                        if ($custom_attributes !== '') {
                                            $custom_attributes .= ' ';
                                        }
                                        $custom_attributes .= sprintf('%1$s="%2$s"', esc_attr($attr_key), $attr_val);
                                    }
                                }
                                if (isset($field['disabled'])) {
                                    if ($custom_attributes !== '') {
                                        $custom_attributes .= ' ';
                                    }
                                    if ($field['disabled']) {
                                        $custom_attributes .= 'disabled="true"';
                                    }
                                }
                                printf('<td class="woom-field-%1$s" %2$s>', esc_attr($field['type']), esc_html($custom_attributes));
                                printf('<select data-placeholder="%1$s" class="chosen chosen-select" name="%2$s[]" id="%2$s" %3$s >', esc_attr($field['placeholder']), esc_attr($field['id']), wp_kses($custom_attributes, array()));
                                if (!is_array(get_option($field['id'], array()))) {
                                    delete_option($field['id']);
                                }
                                if (is_array(get_option($field['id'], array())) && !empty(get_option($field['id'], array()))) {
                                    foreach (get_option($field['id'], array()) as $option) {

                                        printf('<option value="%1$s" selected>%1$s</option>', esc_attr($option));
                                    }
                                }
                                foreach ($field['options'] as $option) {
                                    if (is_array(get_option($field['id'], array()))) {
                                        if (!in_array($option, get_option($field['id'], array()))) {
                                            printf('<option value="%1$s">%1$s</option>', esc_attr($option));
                                        }
                                    }
                                }

                                echo '</select>';

                                if (isset($field['desc']) && !empty($field['desc'])) {
                                    if (isset($field['desc_tip'])) {
                                        if ($field['desc_tip'] === true) {
                                            printf(wp_kses_post(wc_help_tip($field['desc'])));
                                        } else {
                                            printf('<span>%s</span>', wp_kses_post($field['desc']));
                                        }
                                    } else {
                                        printf('<span>%s</span>', wp_kses_post($field['desc']));
                                    }
                                }
                                echo '<p class="woom-warning-message" style="display:none;"></p>';

                                break;

                            case 'woom_trigger_inline_options':
                                ?>
                            <td class="woom-field-text" <?php echo esc_html($custom_attributes); ?>>
                                <div class="inline-inputs">
                                    <?php
                                    foreach ($field['options'] as $woom_field) {

                                        $field_value = isset($woom_field['default']) ? $field['default'] : '';
                                        $field_value = get_option($woom_field['id'], $field_value);
                                        switch ($woom_field['type']) {
                                            case 'select':
                                                $custom_attributes = '';
                                                if (isset($woom_field['custom_attributes'])) {
                                                    foreach ($woom_field['custom_attributes'] as $attr_key => $attr_val) {
                                                        if ($custom_attributes !== '') {
                                                            $custom_attributes .= ' ';
                                                        }
                                                        $custom_attributes .= sprintf('%1$s=%2$s', $attr_key, $attr_val);
                                                    }
                                                }
                                                if (isset($woom_field['disabled'])) {
                                                    if ($custom_attributes !== '') {
                                                        $custom_attributes .= ' ';
                                                    }
                                                    if ($woom_field['disabled']) {
                                                        $custom_attributes .= 'disabled="true"';
                                                    }
                                                }
                                    ?>
                                                <?php
                                                printf('<select name="%1$s" id="%1$s" %2$s>', esc_attr($woom_field['id']), esc_attr($custom_attributes));
                                                foreach ($woom_field['options'] as $option_value => $option_text) {
                                                    if (trim($option_value) === trim($field_value) && !empty(trim($field_value))) {
                                                        printf('<option value="%1$s" selected>%2$s</option>', esc_attr($option_value), esc_html($option_text));
                                                    } else {
                                                        printf('<option value="%1$s">%2$s</option>', esc_attr($option_value), esc_html($option_text));
                                                    }
                                                }
                                                printf('</select>');
                                                if (isset($woom_field['desc']) && !empty($woom_field['desc'])) {
                                                    if (isset($woom_field['desc_tip'])) {
                                                        if ($woom_field['desc_tip'] === true) {
                                                            printf(wp_kses_post(wc_help_tip($woom_field['desc'])));
                                                        } else {
                                                            printf('<span>%s</span>', wp_kses_post($woom_field['desc']));
                                                        }
                                                    } else {
                                                        printf('<span>%s</span>', wp_kses_post($woom_field['desc']));
                                                    }
                                                }
                                                ?>
                                    <?php
                                                break;

                                            default:
                                                printf('<input type="%1$s" id="%2$s" name="%2$s" value="%3$s">',  esc_attr($woom_field['type']), esc_attr($woom_field['id']), esc_attr($field_value));
                                                break;
                                        }
                                    }
                                    ?>
                                </div>
                <?php
                                break;
                            default:
                                break;
                        }
                        echo "</td>";
                    }
                    echo "</tr>";
                    if (isset($links['toggle_settings'])) {
                        printf('<tr class="woom-collapse-table"><td colspan="10" style="padding: 0px !important;"><table class="form-table woom-toggle-content-table">');
                        printf('<tr class="woom-toggle-row woom-toggle-title" style="display:none;"><th>Coupon settings</th></tr>');
                        foreach ($links['toggle_settings'] as $settings_index => $toggle_settings) {
                            if (isset($links['toggle_prefixes'][$index])) {

                                printf('<tr class="woom-toggle-row %1$s" style="display:none;">', esc_attr($links['toggle_prefixes'][$index] . '_row'));
                                if (isset($links['toggle_prefixes'])) {
                                    $class_list = "forminp woom-input-" . $toggle_settings['type'];
                                    if (isset($toggle_settings['data-toggler'])) {
                                        $class_list .= " woom-switch-checkbox";
                                    }
                                    $data_label = '';
                                    if (isset($toggle_settings['data-labels'])) {
                                        foreach ($toggle_settings['data-labels'] as $label_key => $label_name) {
                                            $data_label .= 'data-' . $label_key . '="' . $label_name . '" ';
                                        }
                                        if (get_option($links['toggle_prefixes'][$index] . '_' . $toggle_settings['id'], 'no') === 'yes') {
                                            $toggle_settings['name'] = ($toggle_settings['data-labels']['dynamic']);
                                        } else {
                                            $toggle_settings['name'] = ($toggle_settings['data-labels']['fixed']);
                                        }
                                    } else {
                                        $data_label = '';
                                    }

                                    printf('<td scope="row" class="woom-label" %1$s>%2$s</td>', (!empty($data_label) ? wp_kses($data_label, array()) : ''), esc_html($toggle_settings['name']));
                                    printf('<td class="%2$s">%1$s</td>', wp_kses($this->woom_settings_field_template($toggle_settings, sprintf('%1$s_', esc_attr($links['toggle_prefixes'][$index]))), array(
                                        'input' => array('type' => array(), 'name' => array(), 'id' => array(), 'value' => array(), 'class' => array(), 'checked' => array(), 'onchange' => array(), 'style' => array()),
                                        'label' => array('for' => array(), 'class' => array()),
                                        'select' => array(
                                            'name' => array(),
                                            'id' => array(),
                                            'class' => array(),
                                        ),
                                        'option' => array(
                                            'id' => array(),
                                            'value' => array(),
                                            'selected' => array()
                                        )
                                    )), esc_attr($class_list));
                                }
                                echo "</tr>";
                            }
                        }
                        printf("</table></td></tr>");
                    }
                    printf('<tr class="woom-collapse-table ' . esc_html($links['toggle_prefixes'][$index]) . '_attachment_section"><td colspan="10" style="padding: 0px !important;"><table class="form-table woom-attachment-content-table" >');
                    printf('<tr style="display:none;" class="woom-attachment-row woom-attachment-title"><th>Header Options</th></tr>');
                    printf('<tr style="display:none;" class="woom-attachment-row ' . esc_html($links['toggle_prefixes'][$index]) . '_attachment_row">
  <td scope="row" class="woom-label">Header attachemnt url<br/><span style="font-size: 12px;">(Image, Video, Document, Location)</span></td>
  <td class="forminp woom-input-text">
    <input type="text" class="woom-input" name="' . esc_html($links['toggle_prefixes'][$index]) . '_attachment_url" id="' . esc_html($links['toggle_prefixes'][$index]) . '_attachment" value="' . esc_html(get_option($links['toggle_prefixes'][$index] . '_attachment_url', '')) . '" />
    <br/><span style="font-size: 12px;">Find allowed formats in documentation. <a href="' . esc_url('https://notiqoo.com/docs/notiqoo/setting-page/template-settings/?utm_source=free-plugin&utm_medium=settings') . '" target="_blank">click here</a></span>
    </td></tr>');

                    printf("</table></td></tr>");
                endforeach;
                ?>
    </tbody>
    <?php
    if (array_key_exists("add_new_row", $links) && $links['add_new_row']) {
    ?>
        <tfoot>
            <tr>
                <td colspan="10">
                    <a data-row="<?php echo esc_html(substr($links['id'], 0, -1)); ?>" onclick="woom_addnew_row(this)" class="button">Add new</a>
                </td>
            </tr>
        </tfoot>
    <?php
    }
    echo sprintf('</table>');
