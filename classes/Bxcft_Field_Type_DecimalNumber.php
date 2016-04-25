<?php
/**
 * DecimalNumber Type
 */
if (!class_exists('Bxcft_Field_Type_DecimalNumber'))
{
    class Bxcft_Field_Type_DecimalNumber extends BP_XProfile_Field_Type
    {
        public function __construct() {
            parent::__construct();

            $this->name             = __( 'Decimal number (HTML5 field)', 'bxcft' );

            $this->accepts_null_value   = true;
            $this->supports_options     = true;

            $this->set_format( '/^\d+\.?\d*$/', 'replace' );

            do_action( 'bp_xprofile_field_type_decimal_number', $this );
        }

        public function admin_field_html (array $raw_properties = array ())
        {
            global $field;

            $options = $field->get_children( true );
            if (count($options) > 0) {
                $step = (1 / (pow(10, (int)$options[0]->name)));
            } else {
                $step = 0.01;
            }

            $html = $this->get_edit_field_html_elements( array_merge(
                array(
                    'type' => 'number',
                    'step' => $step,
                ),
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

            $field = new BP_XProfile_Field(bp_get_the_profile_field_id());

            $options = $field->get_children( true );
            if (count($options) > 0) {
                $step = (1 / (pow(10, (int)$options[0]->name)));
            } else {
                $step = 0.01;
            }

            $html = $this->get_edit_field_html_elements( array_merge(
                array(
                    'type'  => 'number',
                    'step' => $step,
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

        public function admin_new_field_html (\BP_XProfile_Field $current_field, $control_type = '')
        {
            $type = array_search( get_class( $this ), bp_xprofile_get_field_types() );
            if ( false === $type ) {
                return;
            }

            $class            = $current_field->type != $type ? 'display: none;' : '';
            $current_type_obj = bp_xprofile_create_field_type( $type );

            $options = $current_field->get_children( true );
            if ( ! $options ) {
                $options = array();
                $i       = 1;
                while ( isset( $_POST[$type . '_option'][$i] ) ) {
                    $is_default_option = true;

                    $options[] = (object) array(
                        'id'                => -1,
                        'is_default_option' => $is_default_option,
                        'name'              => sanitize_text_field( stripslashes( $_POST[$type . '_option'][$i] ) ),
                    );

                    ++$i;
                }

                if ( ! $options ) {
                    $options[] = (object) array(
                        'id'                => -1,
                        'is_default_option' => false,
                        'name'              => '2',
                    );
                }
            }
        ?>
            <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
                <h3><?php esc_html_e( 'Select max number of decimals:', 'bxcft' ); ?></h3>
                <div class="inside">
                    <p>
                        <select name="<?php echo esc_attr( "{$type}_option[1]" ); ?>" id="<?php echo esc_attr( "{$type}_option1" ); ?>">
                        <?php for ($j=1;$j<=6;$j++): ?>
                            <option value="<?php echo $j; ?>"<?php if ($j === (int)$options[0]->name): ?> selected="selected"<?php endif; ?>><?php echo $j; ?></option>
                        <?php endfor; ?>
                        </select>
                    </p>
                </div>
            </div>
        <?php
        }

        public function is_valid( $values ) {
            $this->validation_whitelist = null;
            return parent::is_valid($values);
        }

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
                if (!empty($field_id)) {
                    $field = BP_XProfile_Field::get_instance($field_id);
                    if ($field) {
                        $do_autolink = apply_filters('bxcft_do_autolink',
                            $field->get_do_autolink());
                        if ($do_autolink) {
                            $query_arg = bp_core_get_component_search_query_arg( 'members' );
                            $search_url = add_query_arg( array(
                                    $query_arg => urlencode( $field_value )
                                ), bp_get_members_directory_permalink() );
                            $new_field_value = '<a href="' . esc_url( $search_url ) .
                                '" rel="nofollow">' . $new_field_value . '</a>';
                        }
                    }
                }
            }

            /**
             * bxcft_decimal_number_display_filter
             *
             * Use this filter to modify the appearance of Decimal Number
             * field value.
             * @param  $new_field_value Value of field
             * @param  $field_id Id of field.
             * @return  Filtered value of field.
             */
            return apply_filters('bxcft_decimal_number_display_filter',
                $new_field_value, $field_id);
        }
    }
}
