function bxcft_divide_textfield($) {
    // Delete old options fields.
    $('input[name^="checkbox_acceptance_option"]').remove();
    var text = $('#checkbox_acceptance_text').val();
    var new_input;
    if (text.length > 150) {
        var text_divided = text;
        var i = 1;
        while (text_divided.length > 150) {
            var fragment = text_divided.substring(0, 150);

            // Create an option hidden input.
            if ($('#checkbox_acceptance_option'+i).val()) {
                $('#checkbox_acceptance_option'+i).val(fragment);
            } else {
                new_input = '<input type="hidden" name="checkbox_acceptance_option[' + i + ']" ' +
                                'id="checkbox_acceptance_option' + i + '" value="' + fragment + '" />';
                $('#checkbox_acceptance.postbox').append(new_input);
            }

            text_divided = text_divided.substring(150);
            i += 1;
        }
        if (text_divided.length > 0) {
            // Create the last option hidden input.
            if ($('#checkbox_acceptance_option'+i).val()) {
                $('#checkbox_acceptance_option'+i).val(text_divided);
            } else {
                new_input = '<input type="hidden" name="checkbox_acceptance_option[' + i + ']" ' +
                                'id="checkbox_acceptance_option' + i + '" value="' + text_divided + '" />';
                $('#checkbox_acceptance.postbox').append(new_input);
            }
        }
    } else {
        if ($('#checkbox_acceptance_option1').length > 0) {
            $('#checkbox_acceptance_option1').val(text);
        } else {
            new_input = '<input type="hidden" name="checkbox_acceptance_option[1]" ' +
                            'id="checkbox_acceptance_option1" value="' + text + '" />';
            $('#checkbox_acceptance.postbox').append(new_input);
        }
    }
}

function bxcft_remove_empty_checkbox($) {
    if ($('#birthdate_option1').is(':checked')) {
        $('#birthdate_option0').remove();
    }
}

function bxcft_save_range($, e) {
    var min = $('#number_minmax_option1').val();
    var max = $('#number_minmax_option2').val();
    if (min === '' && max === '') {
        alert(error_msg_number_minmax_empty);
        e.preventDefault();
        return false;
    }
    else if (parseInt(min) >= parseInt(max)) {
        alert(error_msg_number_minmax);
        e.preventDefault();
        return false;
    }

    if (min !== '') {
        $('#number_minmax_option1').parent().hide();
        $('#number_minmax_option1').val('min_' + min);
    }
    if (max !== '') {
        $('#number_minmax_option2').parent().hide();
        $('#number_minmax_option2').val('max_' + max);
    }
}

function bxcft_save_range_slider($, e) {
    var min = $('#slider_option1').val();
    var max = $('#slider_option2').val();
    if (min === '' || max === '') {
        alert(error_msg_slider_empty);
        e.preventDefault();
        return false;
    }
    else if (parseInt(min) >= parseInt(max)) {
        alert(error_msg_slider);
        e.preventDefault();
        return false;
    }

    if (min !== '') {
        $('#slider_option1').parent().hide();
        $('#slider_option1').val('min_' + min);
    }
    if (max !== '') {
        $('#slider_option2').parent().hide();
        $('#slider_option2').val('max_' + max);
    }
}

function show_hide_select2box($, selected_type) {
    if (selected_type !== '' && selected_type !== undefined &&
        $.inArray(selected_type, fields_type_with_select2.types) >= 0) {
        $('#select2-box').show();
    } else {
        $('#select2-box').hide();
    }
}

jQuery(document).ready(function($) {
    $('#bp-xprofile-add-field').on('submit', function(e) {
        if ($('select#fieldtype').val() == 'checkbox_acceptance') {
            bxcft_divide_textfield($);
        }
        else if ($('select#fieldtype').val() == 'birthdate') {
            bxcft_remove_empty_checkbox($);
        }
        else if ($('select#fieldtype').val() == 'number_minmax') {
            bxcft_save_range($, e);
        }
        else if ($('select#fieldtype').val() == 'slider') {
            bxcft_save_range_slider($, e);
        }
    });

    $('select#fieldtype').on('change', function() {
        show_hide_select2box($, $(this).val());
    });

    show_hide_select2box($, $('select#fieldtype').val());
});
