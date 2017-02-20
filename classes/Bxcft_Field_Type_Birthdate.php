<?php
/**
 * Birthdate type
 */
if (!class_exists('Bxcft_Field_Type_Birthdate'))
{
    class Bxcft_Field_Type_Birthdate extends BP_XProfile_Field_Type
    {
        const OPTION_SHOW_AGE = 'show_age';

        private $_englishMonths = array();
        private $_months = array();

        public function __construct() {
            parent::__construct();

            $this->name             = _x( 'Birthdate Selector', 'xprofile field type', 'bxcft' );
            $this->supports_options = true;

            $this->set_format( '/^\d{4}-\d{1,2}-\d{1,2} 00:00:00$/', 'replace' );  // "Y-m-d 00:00:00"
            do_action( 'bp_xprofile_field_type_birthdate', $this );
        }

        private function getEnglishMonths() {
            if (!$this->_englishMonths) {
                $this->_englishMonths = array(
                    'January', 'February', 'March', 'April', 'May', 'June', 'July',
                    'August', 'September', 'October', 'November', 'December'
                );
            }

            return $this->_englishMonths;
        }

        private function getMonths() {
            if (!$this->_months) {
                $this->_months = array(
                    0   => __( 'January', 'buddypress' ),
                    1   => __( 'February', 'buddypress' ),
                    2   => __( 'March', 'buddypress' ),
                    3   => __( 'April', 'buddypress' ),
                    4   => __( 'May', 'buddypress' ),
                    5   => __( 'June', 'buddypress' ),
                    6   => __( 'July', 'buddypress' ),
                    7   => __( 'August', 'buddypress' ),
                    8   => __( 'September', 'buddypress' ),
                    9   => __( 'October', 'buddypress' ),
                    10  => __( 'November', 'buddypress' ),
                    11  => __( 'December', 'buddypress' )
                );
            }

            return $this->_months;
        }

        public function admin_field_html (array $raw_properties = array ())
        {
            $day_html = $this->get_edit_field_html_elements( array_merge(
                array(
                    'id'   => bp_get_the_profile_field_input_name() . '_day',
                    'name' => bp_get_the_profile_field_input_name() . '_day',
                    'class' => 'bxcft-birthdate-day'
                ),
                $raw_properties
            ) );

            $month_html = $this->get_edit_field_html_elements( array_merge(
                array(
                    'id'   => bp_get_the_profile_field_input_name() . '_month',
                    'name' => bp_get_the_profile_field_input_name() . '_month',
                    'class' => 'bxcft-birthdate-month'
                ),
                $raw_properties
            ) );

            $year_html = $this->get_edit_field_html_elements( array_merge(
                array(
                    'id'   => bp_get_the_profile_field_input_name() . '_year',
                    'name' => bp_get_the_profile_field_input_name() . '_year',
                    'class' => 'bxcft-birthdate-year'
                ),
                $raw_properties
            ) );
        ?>
            <select <?php echo $day_html; ?>>
                <?php bp_the_profile_field_options( 'type=day' ); ?>
            </select>

            <select <?php echo $month_html; ?>>
                <?php bp_the_profile_field_options( 'type=month' ); ?>
            </select>

            <select <?php echo $year_html; ?>>
                <?php bp_the_profile_field_options( 'type=year' ); ?>
            </select>
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
                        'name'              => '',
                    );
                }
            }
        ?>
            <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
                <h3><?php esc_html_e( 'Show age (hide birthdate):', 'bxcft' ); ?></h3>
                <div class="inside">
                    <p>
                        <?php _e('Check this if you want to show age instead of birthdate:', 'bxcft'); ?>
                        <input type="hidden"
                            name="<?php echo esc_attr( "{$type}_option[0]" ); ?>"
                            id="<?php echo esc_attr( "{$type}_option0" ); ?>"
                            value="show_birthdate" />
                        <input type="checkbox"
                            name="<?php echo esc_attr( "{$type}_option[1]" ); ?>"
                            id="<?php echo esc_attr( "{$type}_option1" ); ?>"
                            value="<?php echo Bxcft_Field_Type_Birthdate::OPTION_SHOW_AGE; ?>"
                            <?php if ($options[0]->name == Bxcft_Field_Type_Birthdate::OPTION_SHOW_AGE) : ?>checked="checked"<?php endif; ?>/>
                    </p>
                </div>

                <h3><?php esc_html_e( 'Define a minimum age:', 'bxcft' ); ?></h3>
                <div class="inside">
                    <p>
                        <?php _e('Minimum age:', 'bxcft'); ?>
                        <input type="number"
                            name="<?php echo esc_attr( "{$type}_option[2]" ); ?>"
                            id="<?php echo esc_attr( "{$type}_option2" ); ?>"
                            min="1" max="100"
                            value="<?php if (count($options) > 0) { echo $options[1]->name; } ?>" />
                    </p>
                </div>
            </div>
        <?php
        }

        public function edit_field_html (array $raw_properties = array ())
        {
            $user_id = bp_displayed_user_id();

            // user_id is a special optional parameter that we pass to {@link bp_the_profile_field_options()}.
            if ( isset( $raw_properties['user_id'] ) ) {
                $user_id = (int) $raw_properties['user_id'];
                unset( $raw_properties['user_id'] );
            }

            // HTML5 required attribute.
            if ( bp_get_the_profile_field_is_required() ) {
                $raw_properties['required'] = 'required';
            }

            $day_html = $this->get_edit_field_html_elements( array_merge(
                array(
                    'id'   => bp_get_the_profile_field_input_name() . '_day',
                    'name' => bp_get_the_profile_field_input_name() . '_day',
                    'class' => 'bxcft-birthdate-day'
                ),
                $raw_properties
            ) );

            $month_html = $this->get_edit_field_html_elements( array_merge(
                array(
                    'id'   => bp_get_the_profile_field_input_name() . '_month',
                    'name' => bp_get_the_profile_field_input_name() . '_month',
                    'class' => 'bxcft-birthdate-month'
                ),
                $raw_properties
            ) );

            $year_html = $this->get_edit_field_html_elements( array_merge(
                array(
                    'id'   => bp_get_the_profile_field_input_name() . '_year',
                    'name' => bp_get_the_profile_field_input_name() . '_year',
                    'class' => 'bxcft-birthdate-year'
                ),
                $raw_properties
            ) );

            $label = sprintf(
                '<label for="%s_day">%s%s</label>',
                    bp_get_the_profile_field_input_name(),
                    bp_get_the_profile_field_name(),
                    (bp_get_the_profile_field_is_required()) ?
                        ' ' . esc_html__( '(required)', 'buddypress') : ''
            );
            // Label.
            echo apply_filters('bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required());
            // Errors
            do_action( bp_get_the_profile_field_errors_action() );
            // Input.
        ?>
            <select <?php echo $day_html; ?>>
                <?php bp_the_profile_field_options( array( 'type' => 'day', 'user_id' => $user_id ) ); ?>
            </select>

            <select <?php echo $month_html; ?>>
                <?php bp_the_profile_field_options( array( 'type' => 'month', 'user_id' => $user_id ) ); ?>
            </select>

            <select <?php echo $year_html; ?>>
                <?php bp_the_profile_field_options( array( 'type' => 'year', 'user_id' => $user_id ) ); ?>
            </select>
            <script>
                if (bxcft_months === undefined) {
                    var bxcft_months = [];
                    <?php
                        $months = $this->getMonths();
                        foreach ($months as $k => $m):
                            printf("bxcft_months[%s] = '%s';", $k, $m);
                        endforeach;
                    ?>
                }
            </script>
        <?php
        }

        public function edit_field_options_html( array $args = array() ) {
            $options = $this->field_obj->get_children();
            $date    = BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] );

            $day   = 0;
            $month = '';
            $year  = 0;
            $html  = '';

            // Set day, month, year defaults
            if ( ! empty( $date ) ) {

                // If Unix timestamp
                if ( is_numeric( $date ) ) {
                    $day   = date( 'j', $date );
                    $month = date( 'F', $date );
                    $year  = date( 'Y', $date );

                // If MySQL timestamp
                } else {
                    $day   = mysql2date( 'j', $date );
                    $month = mysql2date( 'F', $date, false ); // Not localized, so that selected() works below
                    $year  = mysql2date( 'Y', $date );
                }
            }

            // Check for updated posted values, and errors preventing them from being saved first time.
            if ( ! empty( $_POST['field_' . $this->field_obj->id . '_day'] ) ) {
                $new_day = (int) $_POST['field_' . $this->field_obj->id . '_day'];
                $day     = ( $day != $new_day ) ? $new_day : $day;
            }

            if ( ! empty( $_POST['field_' . $this->field_obj->id . '_month'] ) ) {
                $new_month = $_POST['field_' . $this->field_obj->id . '_month'];
                $month     = ( $month != $new_month ) ? $new_month : $month;
            }

            if ( ! empty( $_POST['field_' . $this->field_obj->id . '_year'] ) ) {
                $new_year = (int) $_POST['field_' . $this->field_obj->id . '_year'];
                $year     = ( $year != $new_year ) ? $new_year : $year;
            }

            // $type will be passed by calling function when needed
            switch ( $args['type'] ) {
                case 'day':
                    $html = sprintf( '<option value="" %1$s>%2$s</option>', selected( $day, 0, false ), /* translators: no option picked in select box */ __( '----', 'buddypress' ) );

                    for ( $i = 1; $i < 32; ++$i ) {
                        $html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', (int) $i, selected( $day, $i, false ), (int) $i );
                    }
                break;

                case 'month':
                    $eng_months = $this->getEnglishMonths();
                    $months = $this->getMonths();

                    $html = sprintf( '<option value="" %1$s>%2$s</option>', selected( $month, 0, false ), /* translators: no option picked in select box */ __( '----', 'buddypress' ) );

                    for ( $i = 0; $i < 12; ++$i ) {
                        $html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $eng_months[$i] ), selected( $month, $eng_months[$i], false ), $months[$i] );
                    }
                break;

                case 'year':
                    $html = sprintf( '<option value="" %1$s>%2$s</option>', selected( $year, 0, false ), /* translators: no option picked in select box */ __( '----', 'buddypress' ) );

                    for ( $i = date('Y', time()-60*60*24); $i > 1901; $i-- ) {
                        $html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', (int) $i, selected( $year, $i, false ), (int) $i );
                    }
                break;
            }

            echo apply_filters( 'bp_get_the_profile_field_birthdate', $html, $args['type'], $day, $month, $year, $this->field_obj->id, $date );
        }

        /**
         * Overriden, we cannot validate against the whitelist.
         * @param type $values
         * @return type
         */
        public function is_valid( $values ) {
            $validated = false;

            foreach ( (array) $values as $value ) {

                foreach ( $this->validation_regex as $format ) {
                    if ( 1 === preg_match( $format, $value ) ) {
                        $validated = true;
                        continue;

                    } else {
                        $validated = false;
                    }
                }
            }

            if ( ! $validated && is_array( $values ) && empty( $values ) && $this->accepts_null_value ) {
                $validated = true;
            }

            return (bool) apply_filters( 'bp_xprofile_field_type_is_valid', $validated, $values, $this );
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

            if (!empty($field_value) && !empty($field_id)) {
                $field = BP_XProfile_Field::get_instance($field_id);

                if ($field) {
                    $show_age = false;

                    // Looking for "show_age" flag.
                    $childs = $field->get_children();
                    if (!empty($childs)) {
                        foreach ($childs as $c) {
                            if ($c->name == Bxcft_Field_Type_Birthdate::OPTION_SHOW_AGE) {
                                $show_age = true;
                                break;
                            }
                        }
                    }

                    if ($show_age) {
                        // Calculate age.
                        $new_field_value = floor((time() - strtotime($field_value)) / 31556926);
                    } else {
                        // Display birthdate with WP Settings Date Format.
                        $new_field_value = date_i18n( get_option('date_format'),
                            strtotime($field_value) );
                    }

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

            /**
             * bxcft_birthdate_display_filter
             *
             * Use this filter to modify the appearance of Birthdate field value.
             * @param  $new_field_value Value of field
             * @param  $field_id Id of field.
             * @return  Filtered value of field.
             */
            return apply_filters('bxcft_birthdate_display_filter', $new_field_value, $field_id);
        }
    }
}
