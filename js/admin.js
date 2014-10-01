function bxcft_show_options(forWhat) {
	document.getElementById( 'radio'                            ).style.display = 'none';
	document.getElementById( 'selectbox'                        ).style.display = 'none';
	document.getElementById( 'multiselectbox'                   ).style.display = 'none';
	document.getElementById( 'checkbox'                         ).style.display = 'none';
    document.getElementById( 'birthdate'                        ).style.display = 'none';
    document.getElementById( 'select_custom_post_type'          ).style.display = 'none';
    document.getElementById( 'multiselect_custom_post_type'     ).style.display = 'none';
    document.getElementById( 'checkbox_acceptance'              ).style.display = 'none';

	if ( forWhat == 'radio' )
		document.getElementById( 'radio' ).style.display = "";

	if ( forWhat == 'selectbox' )
		document.getElementById( 'selectbox' ).style.display = "";

	if ( forWhat == 'multiselectbox' )
		document.getElementById( 'multiselectbox' ).style.display = "";

	if ( forWhat == 'checkbox' )
		document.getElementById( 'checkbox' ).style.display = "";

	if ( forWhat == 'birthdate' )
		document.getElementById( 'birthdate' ).style.display = "";

	if ( forWhat == 'select_custom_post_type' )
		document.getElementById( 'select_custom_post_type' ).style.display = "";

	if ( forWhat == 'multiselect_custom_post_type' )
		document.getElementById( 'multiselect_custom_post_type' ).style.display = "";

	if ( forWhat == 'checkbox_acceptance' )
		document.getElementById( 'checkbox_acceptance' ).style.display = "";
}

function bxcft_divide_textfield() {
    // Delete old options fields.
    jQuery('input[name^="checkbox_acceptance_option"]').remove();
    var text = encodeURIComponent(jQuery('#checkbox_acceptance_text').val());
    if (text.length > 150) {
        var text_divided = text;
        var i = 1;
        while (text_divided.length > 150) {
            var fragment = text_divided.substring(0, 150);
            
            // Create an option hidden input.
            if (jQuery('#checkbox_acceptance_option'+i).val()) {
                jQuery('#checkbox_acceptance_option'+i).val(fragment);
            } else {
                var new_input = '<input type="hidden" name="checkbox_acceptance_option[' + i + ']" ' +
                                'id="checkbox_acceptance_option' + i + '" value="' + fragment + '" />';
                jQuery('#checkbox_acceptance.postbox').append(new_input);
            }
            
            text_divided = text_divided.substring(150);
            i += 1;
        }
        if (text_divided.length > 0) {
            // Create the last option hidden input.
            if (jQuery('#checkbox_acceptance_option'+i).val()) {
                jQuery('#checkbox_acceptance_option'+i).val(text_divided);
            } else {
                var new_input = '<input type="hidden" name="checkbox_acceptance_option[' + i + ']" ' +
                                'id="checkbox_acceptance_option' + i + '" value="' + text_divided + '" />';
                jQuery('#checkbox_acceptance.postbox').append(new_input);
            }
        }
    } else {
        if (jQuery('#checkbox_acceptance_option1').val()) {
            jQuery('#checkbox_acceptance_option1').val(text);
        } else {
            var new_input = '<input type="hidden" name="checkbox_acceptance_option[1]" ' +
                            'id="checkbox_acceptance_option1" value="' + text + '" />';
            jQuery('#checkbox_acceptance.postbox').append(new_input);
        }
    }
}

jQuery(document).ready(function() {
    jQuery('.postbox select#fieldtype').on('change', function() {
        bxcft_show_options(jQuery(this).val());
    });
    
    jQuery('#bp-xprofile-add-field #saveField').on('click', function() {
        if (jQuery('.postbox select#fieldtype').val() == 'checkbox_acceptance') {
            bxcft_divide_textfield();
        }
    });
});
