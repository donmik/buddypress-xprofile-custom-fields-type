<?php
/**
 * Datepicker Type
 */
if (!class_exists('Bxcft_Field_Type_Datepicker'))
{
    class Bxcft_Field_Type_Datepicker extends BP_XProfile_Field_Type
    {
        public function __construct() {
            parent::__construct();

            $this->name             = _x( 'Datepicker (HTML5 field)', 'xprofile field type', 'bxcft' );

            $this->set_format( '/^\d{4}-\d{1,2}-\d{1,2}$/', 'replace' );  // "Y-m-d 00:00:00"
            do_action( 'bp_xprofile_field_type_datepicker', $this );
        }

        public function admin_field_html (array $raw_properties = array ())
        {
            $html = $this->get_edit_field_html_elements( array_merge(
                array( 'type' => 'date' ),
                $raw_properties
            ) );
        ?>
            <input <?php echo $html; ?> />
        <?php
        }

        public function edit_field_html (array $raw_properties = array ())
        {
            if ( isset( $raw_properties['user_id'] ) ) {
                unset( $raw_properties['user_id'] );
            }

            // HTML5 required attribute.
            if ( bp_get_the_profile_field_is_required() ) {
                $raw_properties['required'] = 'required';
            }

            $html = $this->get_edit_field_html_elements( array_merge(
                array(
                    'type'  => 'date',
                    'value' => bp_get_the_profile_field_edit_value(),
                ),
                $raw_properties
            ) );

            $label = sprintf(
                '<label for="%s">%s%s</label>',
                    bp_get_the_profile_field_input_name(),
                    bp_get_the_profile_field_name(),
                    (bp_get_the_profile_field_is_required()) ?
                        ' ' . esc_html__( '(required)', 'buddypress' ) : ''
            );
            // Label.
            echo apply_filters('bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required());
            // Errors.
            do_action( bp_get_the_profile_field_errors_action() );
            // Input.
        ?>
            <input <?php echo $html; ?> />
        <?php
        }

        public function admin_new_field_html( BP_XProfile_Field $current_field, $control_type = '' ) {}

        /**
         * Modify the appearance of value. Apply autolink if enabled.
         *
         * @param  string   $value      Original value of field
         * @param  int      $field_id   Id of field
         * @return string   Value formatted
         */
        public static function display_filter($field_value, $field_id = '') {

            $new_field_value = $field_value;

            if (!empty($field_value)) {
                $new_field_value = date_i18n(get_option('date_format'),
                    strtotime($field_value));

                if (!empty($field_id)) {
                    $field = BP_XProfile_Field::get_instance($field_id);
                    if ($field) {
                        $do_autolink = apply_filters('bxcft_do_autolink',
                            $field->get_do_autolink());
                        if ($do_autolink) {
                            $query_arg = bp_core_get_component_search_query_arg( 'members' );
                            $search_url = add_query_arg( array( $query_arg => urlencode( $field_value ) ),
                                bp_get_members_directory_permalink() );
                            $new_field_value = '<a href="' . esc_url( $search_url ) .
                                '" rel="nofollow">' . $new_field_value . '</a>';
                        }
                    }
                }
            }

            /**
             * 'bxcft_datepicker_display_filter'
             *
             * Use this filter to modify the appearance of Color
             * field value.
             *
             * @param  $value Value
             * @param  $field_id Id of field
             * @return  Filtered value of field
             */
            return apply_filters('bxcft_datepicker_display_filter', $new_field_value,
                $field_id);
        }
    }
}
