<?php
/**
 * Image Type
 */
if (!class_exists('Bxcft_Field_Type_Image')) 
{
    class Bxcft_Field_Type_Image extends BP_XProfile_Field_Type
    {
        public function __construct() {
            parent::__construct();

            $this->name             = _x( 'Image', 'xprofile field type', 'bxcft' );
            
            $this->set_format( '/^.+$/', 'replace' );
            do_action( 'bp_xprofile_field_type_image', $this );
        }

        public function admin_field_html (array $raw_properties = array ())
        {
            $html = $this->get_edit_field_html_elements( array_merge(
                array( 'type' => 'file' ),
                $raw_properties
            ) );
        ?>
            <input <?php echo $html; ?>>
        <?php
        }

        public function edit_field_html (array $raw_properties = array ())
        {
            if ( isset( $raw_properties['user_id'] ) ) {
                unset( $raw_properties['user_id'] );
            }
            $html = $this->get_edit_field_html_elements( array_merge(
                array(
                    'type'  => 'file',
                ),
                $raw_properties
            ) );
            
            $uploads = wp_upload_dir();
            
            // Label.
            $label = sprintf('<label for="%s">%s%s</label>',
                        bp_get_the_profile_field_input_name(),
                        bp_get_the_profile_field_name(),
                        (bp_get_the_profile_field_is_required()) ?
                            esc_html( '(required)', 'buddypress') : '');
            // Input file.
            $input = sprintf('<input type="hidden" name="%1$s" id="%1$s" value="%2$s" /><input %3$s />',
                        bp_get_the_profile_field_input_name(),
                        (bp_get_the_profile_field_edit_value() != '' && bp_get_the_profile_field_edit_value() != '-') ?
                            bp_get_the_profile_field_edit_value() : '-',
                        $html);
            // Actual image.
            if (bp_get_the_profile_field_edit_value() != '' && bp_get_the_profile_field_edit_value() != '-') {
                $actual_image = sprintf('<img src="%1$s" alt="%2$s" /><label for="%2$s_deleteimg"><input type="checkbox" name="%2$s_deleteimg" id="%2$s_deleteimg" value="1" /> %3$s</label><input type="hidden" name="%2$s_hiddenimg" id="%2$s_hiddenimg" value="%4$s" />',
                                        $uploads['baseurl'].bp_get_the_profile_field_edit_value(),
                                        bp_get_the_profile_field_input_name(),
                                        __('Check this to delete this image', 'bxcft'),
                                        bp_get_the_profile_field_edit_value());
            } elseif (bp_get_profile_field_data(array('field' => bp_get_the_profile_field_id())) != '' &&
                        bp_get_profile_field_data(array('field' => bp_get_the_profile_field_id())) != '-') {
                $actual_image = sprintf('%1$s<label for="%2$s_deleteimg"><input type="checkbox" name="%2$s_deleteimg" id="%2$s_deleteimg" value="1" /> %3$s</label><input type="hidden" name="%2$s_hiddenimg" id="%2$s_hiddenimg" value="%4$s" />',
                                        strip_tags(bp_get_profile_field_data(array('field' => bp_get_the_profile_field_id()))),
                                        bp_get_the_profile_field_input_name(),
                                        __('Check this to delete this image', 'bxcft'),
                                        (isset($_POST['field_'.bp_get_the_profile_field_id().'_hiddenimg']))?$_POST['field_'.bp_get_the_profile_field_id().'_hiddenimg']:'');
            } else {
                $actual_image = '';
            }
            
            echo apply_filters( 'bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required() );
            do_action( bp_get_the_profile_field_errors_action() );
            echo apply_filters( 'bxcft_field_input', $input, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_is_required() );
            echo apply_filters('bxcft_field_actual_image', $actual_image, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_edit_value());
        ?>
            <script type="text/javascript">
                if (jQuery('#profile-edit-form').length > 0) {
                    jQuery('#profile-edit-form').attr('enctype', 'multipart/form-data');
                }
                if (jQuery('#your-profile').length > 0) {
                    jQuery('#your-profile').attr('enctype', 'multipart/form-data');
                }
            <?php if (bp_get_the_profile_field_edit_value() != '' && bp_get_the_profile_field_edit_value() != '-'): ?>
                jQuery('#field_<?php echo bp_get_the_profile_field_id(); ?>_deleteimg').change(function() {
                    if (jQuery(this).is(':checked') && jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=file]').val() === '') {
                        jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=hidden]').val('');
                    } else {
                        jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=hidden]').val('<?php echo bp_get_the_profile_field_edit_value(); ?>');
                    }
                });
            <?php endif; ?>
                jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=file]').change(function() {
                    if (jQuery(this).val() !== '') {
                        jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=hidden]').val('-');
                    } else {
                        jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=hidden]').val('');
                    }
                });
            </script>
        <?php
        }
        
        public function admin_new_field_html( BP_XProfile_Field $current_field, $control_type = '' ) {}

    }
}
