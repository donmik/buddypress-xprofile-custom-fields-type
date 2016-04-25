<?php
/**
 * Select Custom Post Type Type
 */
if (!class_exists('Bxcft_Field_Type_SelectCustomPostType'))
{
    class Bxcft_Field_Type_SelectCustomPostType extends BP_XProfile_Field_Type
    {
        public function __construct() {
            parent::__construct();

            $this->name             = _x( 'Custom Post Type Selector', 'xprofile field type', 'bxcft' );

            $this->supports_options = true;

            $this->set_format( '/^.+$/', 'replace' );
            do_action( 'bp_xprofile_field_type_select_custom_post_type', $this );
        }

        public function admin_field_html( array $raw_properties = array() ) {
            $html = $this->get_edit_field_html_elements( $raw_properties );
        ?>
            <select <?php echo $html; ?>>
                <?php bp_the_profile_field_options(); ?>
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
                    if ( $current_type_obj->supports_options && ! $current_type_obj->supports_multiple_defaults && isset( $_POST["isDefault_{$type}_option"][$i] ) && (int) $_POST["isDefault_{$type}_option"] === $i ) {
                        $is_default_option = true;
                    } elseif ( isset( $_POST["isDefault_{$type}_option"][$i] ) ) {
                        $is_default_option = (bool) $_POST["isDefault_{$type}_option"][$i];
                    } else {
                        $is_default_option = false;
                    }

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

            $post_types = get_post_types(array(
                'public'    => true,
                '_builtin'  => false,
            ));
        ?>
            <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
        <?php
            if (!$post_types):
        ?>
                <h3><?php _e('There is no custom post type. You need to create at least one to use this field.', 'bxcft'); ?></h3>
        <?php else : ?>
                <h3><?php esc_html_e( 'Select a custom post type:', 'bxcft' ); ?></h3>
                <div class="inside">
                    <p>
                        <?php _e('Select a custom post type:', 'bxcft'); ?>
                        <select name="<?php echo esc_attr( "{$type}_option[1]" ); ?>" id="<?php echo esc_attr( "{$type}_option[1]" ); ?>">
                            <option value=""><?php _e('Select...', 'bxcft'); ?></option>
                        <?php foreach($post_types as $k=>$v): ?>
                            <option value="<?php echo $k; ?>"<?php if ($options[0]->name == $k): ?> selected="selected"<?php endif; ?>><?php echo $v; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </p>
                </div>
        <?php endif; ?>
            </div>
        <?php
        }

        public function edit_field_html (array $raw_properties = array ())
        {
            $user_id = bp_displayed_user_id();

            if ( isset( $raw_properties['user_id'] ) ) {
                $user_id = (int) $raw_properties['user_id'];
                unset( $raw_properties['user_id'] );
            }

            // HTML5 required attribute.
            if ( bp_get_the_profile_field_is_required() ) {
                $raw_properties['required'] = 'required';
            }

            $html = $this->get_edit_field_html_elements( $raw_properties );
        ?>
            <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php esc_html_e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
            <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
            <select <?php echo $html; ?>>
                <option value=""><?php _e('Select...', 'bxcft'); ?></option>
                <?php bp_the_profile_field_options( "user_id={$user_id}" ); ?>
            </select>
        <?php
        }

        public function edit_field_options_html( array $args = array() ) {
            $options        = $this->field_obj->get_children();
            $post_selected  = BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] );

            $html = '';
            if ($options) {
                $post_type_selected = $options[0]->name;
                if ( !empty($_POST['field_' . $this->field_obj->id]) ) {
                    $new_post_selected = (int) $_POST['field_' . $this->field_obj->id];
                    $post_selected = ( $post_selected != $new_post_selected ) ? $new_post_selected : $post_selected;
                }
                // Get posts of custom post type selected.
                $posts = new WP_Query(array(
                    'posts_per_page'    => -1,
                    'post_type'         => $post_type_selected,
                    'orderby'           => 'title',
                    'order'             => 'ASC'
                ));
                if ($posts) {
                    foreach ($posts->posts as $post) {
                        $html .= sprintf('<option value="%s"%s>%s</option>',
                                    $post->ID,
                                    ($post_selected==$post->ID)?' selected="selected"':'',
                                    $post->post_title);
                    }
                }
            }

            echo apply_filters( 'bp_get_the_profile_field_select_custom_post_type', $html, $args['type'], $post_type_selected, $this->field_obj->id );
        }

        /**
         * Overriden, we cannot validate against the whitelist.
         * @param type $values
         * @return type
         */
        public function is_valid( $values ) {
            $validated = false;

            // Some types of field (e.g. multi-selectbox) may have multiple values to check
            foreach ( (array) $values as $value ) {

                // Validate the $value against the type's accepted format(s).
                foreach ( $this->validation_regex as $format ) {
                    if ( 1 === preg_match( $format, $value ) ) {
                        $validated = true;
                        continue;

                    } else {
                        $validated = false;
                    }
                }
            }

            // Handle field types with accepts_null_value set if $values is an empty array
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
                    $childs = $field->get_children();
                    if (!empty($childs) && isset($childs[0])) {
                        $post_type_selected = $childs[0]->name;
                    }
                    $field_value = trim($field_value);
                    $post = get_post($field_value);
                    if ($post && $post->post_type == $post_type_selected) {
                        $new_field_value = $post->post_title;
                    } else {
                        $new_field_value = __('--', 'bxcft');
                    }

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

            /**
             * bxcft_select_custom_post_type_display_filter
             *
             * Use this filter to modify the appearance of Selector
             * Custom Post Type field value.
             * @param  $new_field_value Value of field
             * @param  $field_id Id of field.
             * @return  Filtered value of field.
             */
            return apply_filters('bxcft_select_custom_post_type_display_filter',
                $new_field_value, $field_id);
        }
    }
}
