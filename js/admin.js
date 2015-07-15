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

jQuery(document).ready(function($) {
    $('#bp-xprofile-add-field').on('submit', function(e) {
        if ($('select#fieldtype').val() == 'checkbox_acceptance') {
            bxcft_divide_textfield($);
        }
        else if ($('select#fieldtype').val() == 'birthdate') {
            bxcft_remove_empty_checkbox($);
        }
    });
});
