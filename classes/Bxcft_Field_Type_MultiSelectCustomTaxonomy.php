<?php
/**
 * Multiselect Custom Post Type Type
 */
if (!class_exists('Bxcft_Field_Type_MultiSelectCustomTaxonomy'))
{
    class Bxcft_Field_Type_MultiSelectCustomTaxonomy extends BP_XProfile_Field_Type
    {
        const ALLOW_NEW_TAGS = "allow_new_tags";

        public function __construct() {
            parent::__construct();

            $this->name = _x( 'Custom Taxonomy Multiselector', 'xprofile field type', 'bxcft' );

            $this->supports_multiple_defaults   = true;
            $this->supports_options             = true;

            $this->set_format( '/^.+$/', 'replace' );
            do_action( 'bp_xprofile_field_type_multiselect_custom_taxonomy', $this );
        }

        public function admin_field_html( array $raw_properties = array() ) {
            $html = $this->get_edit_field_html_elements( array_merge(
                array(
                    'multiple' => 'multiple',
                    'id'       => bp_get_the_profile_field_input_name() . '[]',
                    'name'     => bp_get_the_profile_field_input_name() . '[]',
                ),
                $raw_properties
            ) );
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

            $taxonomies = get_taxonomies(array(
                'public'    => true,
                '_builtin'  => false,
            ));
        ?>
            <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
        <?php
            if (!$taxonomies):
        ?>
                <h3><?php _e('There is no custom taxonomy. You need to create at least one to use this field.', 'bxcft'); ?></h3>
        <?php else : ?>
                <h3><?php esc_html_e( 'Select a custom taxonomy:', 'bxcft' ); ?></h3>
                <div class="inside">
                    <p>
                        <?php _e('Select a custom taxonomy:', 'bxcft'); ?>
                        <select name="<?php echo esc_attr( "{$type}_option[1]" ); ?>" id="<?php echo esc_attr( "{$type}_option[1]" ); ?>">
                            <option value=""><?php _e('Select...', 'bxcft'); ?></option>
                        <?php foreach($taxonomies as $k=>$v): ?>
                            <option value="<?php echo $k; ?>"<?php if ($options[0]->name == $k): ?> selected="selected"<?php endif; ?>><?php echo $v; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <?php _e('Allow users to add new tags:', 'bxcft'); ?>
                        <input type="checkbox" name="<?php echo esc_attr( "{$type}_option[2]" ); ?>"
                            id="<?php echo esc_attr( "{$type}_option[2]" ); ?>" value="<?php echo Bxcft_Field_Type_MultiSelectCustomTaxonomy::ALLOW_NEW_TAGS; ?>"
                            <?php if (is_array($options) && isset($options[1]) && $options[1]->name === Bxcft_Field_Type_MultiSelectCustomTaxonomy::ALLOW_NEW_TAGS): ?> checked="checked"<?php endif; ?> />
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

            $html = $this->get_edit_field_html_elements( array_merge(
                array(
                    'multiple' => 'multiple',
                    'id'       => bp_get_the_profile_field_input_name() . '[]',
                    'name'     => bp_get_the_profile_field_input_name() . '[]',
                ),
                $raw_properties
            ) );
        ?>
            <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php esc_html_e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
            <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
            <select <?php echo $html; ?>>
                <?php bp_the_profile_field_options( "user_id={$user_id}" ); ?>
            </select>
        <?php
        }

        public function edit_field_options_html( array $args = array() ) {
            $options        = $this->field_obj->get_children();
            $terms_selected  = maybe_unserialize(BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] ));

            $html = '';
            if ($options) {
                foreach ($options as $option) {
                    if (Bxcft_Field_Type_MultiSelectCustomTaxonomy::ALLOW_NEW_TAGS !== $option->name) {
                        $taxonomy_selected = $option->name;
                        break;
                    }
                }
                if ( !empty($_POST['field_' . $this->field_obj->id]) ) {
                    $new_terms_selected = $_POST['field_' . $this->field_obj->id];
                    $terms_selected = ( $terms_selected != $new_terms_selected ) ? $new_terms_selected : $terms_selected;
                }
                // Get terms of custom taxonomy selected.
                $terms = get_terms($taxonomy_selected, array(
                    'hide_empty' => false
                ));
                if ($terms) {
                    foreach ($terms as $term) {
                        $html .= sprintf('<option value="%s"%s>%s</option>',
                                    $term->term_id,
                                    (!empty($terms_selected) && (in_array($term->term_id, $terms_selected)))?'selected="selected"':'',
                                    $term->name);
                    }
                }
            }

            echo apply_filters( 'bp_get_the_profile_field_multiselect_custom_taxonomy', $html, $args['type'], $terms_selected, $this->field_obj->id );
        }

        public function set_whitelist_values( $values ) {
            foreach ($values as $value) {
                if (Bxcft_Field_Type_MultiSelectCustomTaxonomy::ALLOW_NEW_TAGS !== $value) {
                    $taxonomy_to_check = $value;
                    break;
                }
            }

            $terms = get_terms( array(
                'taxonomy' => $taxonomy_to_check,
                'hide_empty' => false,
                'fields' => 'id=>name',
            ) );

            if ( $terms ) {
                $this->validation_whitelist = array_merge(
                    // Convert ids to strings for validation.
                    array_map( function($v) {
                        return "$v";
                    }, array_keys( $terms ) ),
                    array_values( $terms )
                );
            }

            return $this;
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
                    $do_autolink = apply_filters('bxcft_do_autolink',
                        $field->get_do_autolink());
                    if ($do_autolink) {
                        $query_arg = bp_core_get_component_search_query_arg( 'members' );
                    }
                    $childs = $field->get_children();
                    if (!empty($childs) && isset($childs[0])) {
                        $taxonomy_selected = $childs[0]->name;
                    }
                    $aux = '';
                    $term_ids = explode(',', $field_value);
                    foreach ($term_ids as $tid) {
                        $tid = trim($tid);
                        $term = get_term_by('id', $tid, $taxonomy_selected);
                        if ($term && $term->taxonomy == $taxonomy_selected) {
                            if (empty($aux)) {
                                $aux .= '<ul class="list_custom_taxonomy">';
                            }
                            $aux .= '<li>';
                            if ($do_autolink) {
                                $search_url = add_query_arg( array(
                                    $query_arg => urlencode( $tid )
                                ), bp_get_members_directory_permalink() );
                                $aux .= '<a href="' . esc_url( $search_url ) .
                                    '" rel="nofollow">' . $term->name . '</a>';
                            } else {
                                $aux .= $term->name;
                            }
                            $aux .= '</li>';
                        }
                    }
                    if (!empty($aux)) {
                        $aux .= '</ul>';
                    }
                    $new_field_value = $aux;
                }
            }

            /**
             * bxcft_multiselect_custom_taxonomy_display_filter
             *
             * Use this filter to modify the appearance of Multiselector
             * Custom Taxonomy field value.
             * @param  $new_field_value Value of field
             * @param  $field_id Id of field.
             * @return  Filtered value of field.
             */
            return apply_filters('bxcft_multiselect_custom_taxonomy_display_filter',
                $new_field_value, $field_id);
        }
    }
}
