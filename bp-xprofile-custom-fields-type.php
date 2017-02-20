<?php
/*
    Plugin Name: BuddyPress Xprofile Custom Fields Type
    Plugin URI: http://donmik.com/en/buddypress-xprofile-custom-fields-type/
    Description: BuddyPress installation required!! This plugin add custom field types to BuddyPress Xprofile extension. Field types are: Birthdate, Email, Url, Datepicker, ...
    Version: 2.6.3
    Author: donmik
    Author URI: http://donmik.com
*/
if (!class_exists('Bxcft_Plugin'))
{
    class Bxcft_Plugin
    {
        CONST BXCFT_MAX_FILESIZE = 8;

        private $version;
        private $user_id = null;
        private $images_ext_allowed;
        private $images_max_filesize;
        private $files_ext_allowed;
        private $files_max_filesize;
        private $fields_type_with_select2;
        private $fields_type_multiple;

        private $autolink_filter_removed = false;

        private $bxcft_field_types = array(
            'birthdate'                     => 'Bxcft_Field_Type_Birthdate',
            'email'                         => 'Bxcft_Field_Type_Email',
            'web'                           => 'Bxcft_Field_Type_Web',
            'datepicker'                    => 'Bxcft_Field_Type_Datepicker',
            'select_custom_post_type'       => 'Bxcft_Field_Type_SelectCustomPostType',
            'multiselect_custom_post_type'  => 'Bxcft_Field_Type_MultiSelectCustomPostType',
            'select_custom_taxonomy'        => 'Bxcft_Field_Type_SelectCustomTaxonomy',
            'multiselect_custom_taxonomy'   => 'Bxcft_Field_Type_MultiSelectCustomTaxonomy',
            'checkbox_acceptance'           => 'Bxcft_Field_Type_CheckboxAcceptance',
            'image'                         => 'Bxcft_Field_Type_Image',
            'file'                          => 'Bxcft_Field_Type_File',
            'color'                         => 'Bxcft_Field_Type_Color',
            'decimal_number'                => 'Bxcft_Field_Type_DecimalNumber',
            'number_minmax'                 => 'Bxcft_Field_Type_NumberMinMax',
            'slider'                        => 'Bxcft_Field_Type_Slider',
        );

        public function __construct ()
        {
            $this->version = "2.6";

            /** Main hooks **/
            add_action( 'plugins_loaded', array($this, 'bxcft_update') );

            /** Admin hooks **/
            add_action( 'admin_init', array($this, 'admin_init') );
            add_action( 'admin_notices', array($this, 'admin_notices') );

            /** Buddypress hook **/
            add_action( 'bp_init', array($this, 'init') );
            add_action( 'bp_signup_validate', array($this, 'bxcft_signup_validate') );
            add_action( 'xprofile_data_before_save', array($this, 'bxcft_xprofile_data_before_save') );
            add_action( 'xprofile_data_after_delete', array($this, 'bxcft_xprofile_data_after_delete') );

            /** Select2 fields */
            add_action( 'xprofile_field_after_submitbox', array($this, 'bxcft_show_select2_box') );
            add_action( 'xprofile_fields_saved_field', array($this, 'bxcft_save_do_select2') );
            add_action( 'bp_custom_profile_edit_fields_pre_visibility', array($this, 'bxcft_enable_select2_field') );

            /** Filters **/
            add_filter( 'bp_xprofile_get_field_types', array($this, 'bxcft_get_field_types'), 10, 1 );
            add_filter( 'xprofile_get_field_data', array($this, 'bxcft_get_field_data'), 10, 2 );
            add_filter( 'bp_get_the_profile_field_value', array($this, 'bxcft_remove_autolink_filter_from_buddypress'), 7, 2);
            add_filter( 'bp_get_the_profile_field_value', array($this, 'bxcft_get_field_value'), 10, 3);
            add_filter( 'bp_get_the_profile_field_value', array($this, 'bxcft_restore_autolink_filter_from_buddypress'), 11, 2);
            add_filter( 'bxcft_do_autolink', array($this, 'bxcft_enabled_autolink'), 10, 1 );
            /** BP Profile Search Filters **/
            add_filter( 'bps_field_validation_type', array($this, 'bxcft_standard_fields_validation_type' ) );
            add_filter( 'bps_field_type_for_search_form', array($this, 'bxcft_standard_fields_type_for_search_form' ) );
            add_filter( 'bps_field_type_for_query', array($this, 'bxcft_standard_fields_for_query' ) );
            // Special fields.
            add_filter( 'bps_field_validation', array($this, 'bxcft_special_fields_search_validation' ), 10, 2);
            add_filter( 'bps_field_data_for_search_form', array($this, 'bxcft_special_fields_data_for_search_form' ) );
            add_filter( 'bps_field_query', array($this, 'bxcft_special_fields_query' ), 10, 4);
            // Pre validate multiselect custom taxonomy.
            add_filter( 'bp_xprofile_set_field_data_pre_validate', array( $this, 'bxcft_xprofile_set_field_data_pre_validate' ), 10, 3 );
        }

        public function init()
        {
            $this->images_ext_allowed   = apply_filters('bxcft_images_ext_allowed', array(
                'jpg', 'jpeg', 'gif', 'png'
            ));
            $this->images_max_filesize = apply_filters('bxcft_images_max_filesize', Bxcft_Plugin::BXCFT_MAX_FILESIZE);
            $this->files_ext_allowed   = apply_filters('bxcft_files_ext_allowed', array(
                'doc', 'docx', 'pdf'
            ));
            $this->files_max_filesize = apply_filters('bxcft_files_max_filesize', Bxcft_Plugin::BXCFT_MAX_FILESIZE);
            $this->fields_type_with_select2 = apply_filters('bxcft_field_types_with_select2', array(
                'selectbox', 'multiselectbox', 'select_custom_post_type',
                'multiselect_custom_post_type', 'select_custom_taxonomy',
                'multiselect_custom_taxonomy'
            ));
            $this->fields_type_multiple = apply_filters('bxcft_field_types_multiple', array(
                'multiselectbox', 'multiselect_custom_post_type',
                'multiselect_custom_taxonomy'
            ));

            /** Includes **/
            if ( bp_is_active( 'xprofile' ) ) {
                require_once( 'classes/Bxcft_Field_Type_Birthdate.php' );
                require_once( 'classes/Bxcft_Field_Type_Email.php' );
                require_once( 'classes/Bxcft_Field_Type_Web.php' );
                require_once( 'classes/Bxcft_Field_Type_Datepicker.php' );
                require_once( 'classes/Bxcft_Field_Type_SelectCustomPostType.php' );
                require_once( 'classes/Bxcft_Field_Type_MultiSelectCustomPostType.php' );
                require_once( 'classes/Bxcft_Field_Type_SelectCustomTaxonomy.php' );
                require_once( 'classes/Bxcft_Field_Type_MultiSelectCustomTaxonomy.php' );
                require_once( 'classes/Bxcft_Field_Type_CheckboxAcceptance.php' );
                require_once( 'classes/Bxcft_Field_Type_Image.php' );
                require_once( 'classes/Bxcft_Field_Type_File.php' );
                require_once( 'classes/Bxcft_Field_Type_Color.php' );
                require_once( 'classes/Bxcft_Field_Type_DecimalNumber.php' );
                require_once( 'classes/Bxcft_Field_Type_NumberMinMax.php' );
                require_once( 'classes/Bxcft_Field_Type_Slider.php' );
            }

            if (bp_is_user_profile_edit() || bp_is_register_page()) {
                $this->load_js();
            }
        }

        public function load_js() {
            wp_enqueue_script('bxcft-modernizr', plugin_dir_url(__FILE__) . 'js/modernizr.js', array(), '2.6.2', false);
            wp_enqueue_script('bxcft-jscolor', plugin_dir_url(__FILE__) . 'js/jscolor/jscolor.js', array(), '1.4.1', true);
            wp_enqueue_script('bxcft-public', plugin_dir_url(__FILE__) . 'js/public.js', array('jquery'), $this->version, true);

            // Select 2.
            wp_enqueue_script('bxcft-select2', plugin_dir_url(__FILE__) . 'js/select2/select2.min.js', array('jquery'), '4.0.2', true);
            $locale = get_locale();
            if (file_exists(plugin_dir_path(__FILE__) . 'js/select2/i18n/' . $locale . '.js')) {
                wp_enqueue_script('bxcft-select2-i18n', plugin_dir_url(__FILE__) . 'js/select2/i18n/' . get_locale() . '.js', array('bxcft-select2'), '4.0.2', true);
            }
            wp_enqueue_style('bxcft-select2', plugin_dir_url(__FILE__) . 'css/select2/select2.min.css', array(), '4.0.2');
        }

        public function admin_init()
        {
            if (is_admin() && get_option('bxcft_activated') == 1) {
                // Check if BuddyPress 2.5 is installed.
                $version_bp = array( '0' );
                if (function_exists('is_plugin_active') && is_plugin_active('buddypress/bp-loader.php')) {
                    // BuddyPress loaded.
                    $version_bp = get_file_data( WP_PLUGIN_DIR . '/buddypress/bp-loader.php', array( 'Version' ) );
                }
                if ( ! $this->compare_versions( array_pop( $version_bp ), '2.5' ) ) {
                    $notices = get_option('bxcft_notices');
                    $notices[] = __('BuddyPress Xprofile Custom Fields Type plugin needs <b>BuddyPress 2.5</b>, please install or upgrade BuddyPress.', 'bxcft');
                    update_option('bxcft_notices', $notices);
                    delete_option('bxcft_activated');
                }
                else if ( ! bp_is_active( 'xprofile' ) ) {
                    $notices = get_option('bxcft_notices');
                    $notices[] = __('BuddyPress Xprofile Custom Fields Type plugin needs Buddypress Xprofile Component. Please enable Xprofile first.', 'bxcft');
                    update_option('bxcft_notices', $notices);
                    delete_option('bxcft_activated');
                }

                // Enqueue javascript.
                wp_enqueue_script('bxcft-js', plugin_dir_url(__FILE__) . 'js/admin.js', array(), $this->version, false);
                wp_localize_script('bxcft-js', 'fields_type_with_select2', array('types' => $this->fields_type_with_select2));

                if (isset($_GET['page']) && $_GET['page'] === 'bp-profile-edit') {
                    $this->load_js();
                }
            }
        }

        /**
         * Compare buddypress version with needed version.
         *
         * @since 2.6
         *
         * @param  Array $version_actual Actual version.
         * @param  Array $version_needed Needed version.
         * @return boolean
         */
        private function compare_versions( $version_actual, $version_needed ) {
            $components_version_actual = explode( '.', $version_actual );
            $components_version_needed = explode( '.', $version_needed );

            foreach ( $components_version_needed as $key => $element ) {
                if ( isset( $components_version_actual[ $key ] ) &&
                (int) $components_version_actual[ $key ] < (int) $element ) {
                    return false;
                }
            }

            return true;
        }

        public function admin_notices()
        {
            $notices = get_option('bxcft_notices');
            if ($notices) {
                foreach ($notices as $notice)
                {
                    echo "<div class='error'><p>$notice</p></div>";
                }
                delete_option('bxcft_notices');
            }
        }

        public function bxcft_get_field_types($fields)
        {
            $fields = array_merge($fields, $this->bxcft_field_types);
            return $fields;
        }

        /**
         * Remove `xprofile_filter_link_profile_data` on `bp_get_the_profile_field_value`
         *
         * @since  2.4.6
         * @param  mixed $value Value of field
         * @return mixed        Same value of field
         */
        public function bxcft_remove_autolink_filter_from_buddypress($value, $type='') {
            if (in_array($type, array_keys($this->bxcft_field_types))
                && !Bxcft_Plugin::is_autolink_filter_removed()) {
                $this->autolink_filter_removed = true;
                remove_filter( 'bp_get_the_profile_field_value',
                    'xprofile_filter_link_profile_data', 9 );
            }

            return $value;
        }

        /**
         * Restore `xprofile_filter_link_profile_data` on `bp_get_the_profile_field_value`
         *
         * @since  2.4.6
         * @param  mixed $value Value of field
         * @return mixed        Same value of field
         */
        public function bxcft_restore_autolink_filter_from_buddypress($value, $type='') {
            if ($this->autolink_filter_removed) {
                add_filter( 'bp_get_the_profile_field_value',
                    'xprofile_filter_link_profile_data', 9, 3 );
            }

            return $value;
        }

        /**
         * Check if autolink is enabled. If it is enabled for this type of field and
         * if it was removed or it isn't removed by the user in functions.php.
         *
         * @since  2.4.6
         * @param  boolean $do_autolink Actual value of do_autolink
         * @return boolean              Filtered value of do_autolink
         */
        public function bxcft_enabled_autolink($do_autolink) {
            return ($do_autolink &&
                ($this->autolink_filter_removed
                    || !Bxcft_Plugin::is_autolink_filter_removed()));
        }

        /**
         * This method is now useless, it will be removed on version 3.0.
         *
         * @since  2.4.6
         * @param  mixed $value     Value of field
         * @param  int $field_id    Id of field
         * @return mixed            Same value of field
         */
        public function bxcft_get_field_data($value, $field_id)
        {
            $field = BP_XProfile_Field::get_instance($field_id);
            if (!$field) {
                return;
            }

            /**
             * @deprecated 3.0 This filter will be removed in version 3.0. Please
             * stop using it, use instead 'bxcft_TYPENAME_display_filter'
             */
            return apply_filters('bxcft_show_field_value', $value, $field->type,
                $field_id, $value);
        }

        /**
         * This method is now useless, it will be removed on version 3.0.
         *
         * @since  2.4.6
         * @param  mixed $value     Value of field
         * @param  int $field_id    Id of field
         * @param  string $type     Type of field
         * @return mixed            Same value of field
         */
        public function bxcft_get_field_value($value='', $type='', $id='')
        {
            /**
             * @deprecated 3.0 This filter will be removed in version 3.0. Please
             * stop using it, use instead 'bxcft_TYPENAME_display_filter'
             */
            return apply_filters('bxcft_show_field_value', $value, $type, $id, $value);
        }

        /**
         * Returns the folder where files and images will be saved.
         *
         * @since  2.4.6
         * @param  integer $user_id Id of current displayed user.
         * @return array            array of upload_dir
         */
        public function bxcft_profile_upload_dir( $user_id = 0 )
        {
            if ($user_id == 0 && empty($this->user_id))
            {
                $this->user_id = bp_displayed_user_id();
            }

            /**
             * bxcft_upload_dir
             *
             * Use this filter if you want to change the folder
             * where files and images are saved.
             *
             * @since  2.4.6
             * @var string
             */
            $profile_subdir = apply_filters( 'bxcft_upload_dir', '/profiles/' . $this->user_id,
                $this->user_id);

            $upload_dir = array(
                'path'    => bp_core_get_upload_dir()       . $profile_subdir,
                'url'     => bp_core_get_upload_dir('url')  . $profile_subdir,
                'subdir'  => bp_core_get_upload_dir()       . $profile_subdir,
                'basedir' => bp_core_get_upload_dir()       . $profile_subdir,
                'baseurl' => bp_core_get_upload_dir('url')  . $profile_subdir,
                'error'   => false,
            );

            return apply_filters( 'bxcft_profile_upload_dir', $upload_dir );
        }

        public function bxcft_signup_validate()
        {
            global $bp;
            if ( bp_is_active( 'xprofile' ) )
            {
                if ( isset( $_POST['signup_profile_field_ids'] ) && !empty( $_POST['signup_profile_field_ids'] ) )
                {
                    $profile_field_ids = explode(',', $_POST['signup_profile_field_ids']);
                    foreach ($profile_field_ids as $field_id)
                    {
                        $field = new BP_XProfile_Field($field_id);
                        if ( ($field->type == 'image' || $field->type == 'file') &&
                                isset($_FILES['field_'.$field_id])) {
                            // Delete required field error.
                            unset($bp->signup->errors['field_'.$field_id]);

                            $filesize = round($_FILES['field_'.$field_id]['size'] / (1024 * 1024), 2);
                            if ($field->is_required && $filesize <= 0) {
                                $bp->signup->errors['field_' . $field_id] = __( 'This is a required field', 'buddypress' );
                            } elseif ($filesize > 0) {
                                // Check extensions.
                                $ext = strtolower(substr($_FILES['field_'.$field_id]['name'], strrpos($_FILES['field_'.$field_id]['name'],'.')+1));
                                if ($field->type == 'image') {
                                    if (!in_array($ext, $this->images_ext_allowed)) {
                                        $bp->signup->errors['field_'.$field_id] = sprintf(__('Image type not allowed: (%s).', 'bxcft'), implode(',', $this->images_ext_allowed));
                                    }
                                    elseif ($filesize > $this->images_max_filesize) {
                                        $bp->signup->errors['field_'.$field_id] = sprintf(__('Max image upload size: %s MB.', 'bxcft'), $this->images_max_filesize);
                                    }
                                } elseif ($field->type == 'file') {
                                    if (!in_array($ext, $this->files_ext_allowed)) {
                                        $bp->signup->errors['field_'.$field_id] = sprintf(__('File type not allowed: (%s).', 'bxcft'), implode(',', $this->files_ext_allowed));
                                    }
                                    elseif ($filesize > $this->files_max_filesize) {
                                        $bp->signup->errors['field_'.$field_id] = sprintf(__('Max file upload size: %s MB.', 'bxcft'), $this->files_max_filesize);
                                    }
                                }
                            }
                        }
                        elseif ($field->type == 'checkbox_acceptance' && $field->is_required) {
                            if (isset($_POST['field_' . $field_id])
                                    && $_POST['field_' . $field_id] != 1) {
                                $bp->signup->errors['field_' . $field_id] = __( 'This is a required field', 'buddypress' );
                            }
                        }
                        elseif ($field->type == 'birthdate') {
                            $max_age = 0;
                            $options = $field->get_children();
                            foreach ($options as $o) {
                                if ($o->name != 'show_age' && $o->name != 'show_birthdate') {
                                    $max_age = (int)$o->name;
                                    break;
                                }
                            }

                            if ($max_age > 0) {
                                // Check birthdate.
                                if (class_exists(DateTime)) {
                                    $now = new DateTime();
                                    $birthdate = new DateTime(sprintf("%s-%s-%s",
                                        $_POST['field_'.$field_id.'_year'],
                                        $_POST['field_'.$field_id.'_month'],
                                        $_POST['field_'.$field_id.'_day']));
                                    $age = $now->diff($birthdate);
                                    if ($age->y < $max_age) {
                                        $bp->signup->errors['field_' . $field_id] = sprintf(__( 'You have to be at least %s years old.', 'bxcft' ), $max_age);
                                    }
                                }
                            }
                        }
                    } // End foreach...
                } // End if ( isset...
            } // End if ( bp_is_active(...
        }

        function bxcft_xprofile_data_before_save($data)
        {
            global $bp;

            $field_id = $data->field_id;
            $field = new BP_XProfile_Field($field_id);

            if ($field->type == 'image' || $field->type == 'file' && isset($_FILES['field_'.$field_id]))
            {
                $uploads = wp_upload_dir();
                $filesize = round($_FILES['field_'.$field_id]['size'] / (1024 * 1024), 2);
                if (isset($_FILES['field_'.$field_id]) && $filesize > 0)
                {
                    $ext = strtolower(substr($_FILES['field_'.$field_id]['name'], strrpos($_FILES['field_'.$field_id]['name'],'.')+1));
                    if ($field->type == 'image')
                    {
                        $ext_allowed = $this->images_ext_allowed;
                        if (!in_array($ext, $ext_allowed))
                        {
                            bp_core_add_message( sprintf(__('Image type not allowed: (%s).', 'bxcft'), implode(',', $this->images_ext_allowed)), 'error' );
                            bp_core_redirect( trailingslashit( bp_displayed_user_domain() . $bp->profile->slug . '/edit/group/' . bp_action_variable( 1 ) ) );
                        }
                        elseif ($filesize > $this->images_max_filesize) {
                            bp_core_add_message( sprintf(__('Max image upload size: %s MB.', 'bxcft'), $this->images_max_filesize), 'error' );
                            bp_core_redirect( trailingslashit( bp_displayed_user_domain() . $bp->profile->slug . '/edit/group/' . bp_action_variable( 1 ) ) );
                        } else {
                            // Delete previous image.
                            if (isset($_POST['field_'.$field_id.'_hiddenimg'])      &&
                                !empty($_POST['field_'.$field_id.'_hiddenimg'])     &&
                                file_exists($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenimg']))
                            {
                                unlink($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenimg']);
                            }
                        }
                    }
                    elseif ($field->type == 'file')
                    {
                        $ext_allowed = $this->files_ext_allowed;
                        if (!in_array($ext, $ext_allowed)) {
                            bp_core_add_message( sprintf(__('File type not allowed: (%s).', 'bxcft'), implode(',', $this->files_ext_allowed)), 'error' );
                            bp_core_redirect( trailingslashit( bp_displayed_user_domain() . $bp->profile->slug . '/edit/group/' . bp_action_variable( 1 ) ) );
                        }
                        elseif ($filesize > $this->files_max_filesize) {
                            bp_core_add_message( sprintf(__('Max file upload size: %s MB.', 'bxcft'), $this->files_max_filesize), 'error' );
                            bp_core_redirect( trailingslashit( bp_displayed_user_domain() . $bp->profile->slug . '/edit/group/' . bp_action_variable( 1 ) ) );
                        } else {
                            // Delete previous file.
                            if (isset($_POST['field_'.$field_id.'_hiddenfile'])     &&
                                !empty($_POST['field_'.$field_id.'_hiddenfile'])    &&
                                file_exists($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenfile']))
                            {
                                unlink($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenfile']);
                            }
                        }
                    }

                    if (in_array($ext, $ext_allowed))
                    {
                        require_once( ABSPATH . '/wp-admin/includes/file.php' );
                        $this->user_id = $data->user_id;
                        add_filter( 'upload_dir', array($this, 'bxcft_profile_upload_dir'), 10, 0 );
                        $_POST['action'] = 'wp_handle_upload';
                        $uploaded_file = wp_handle_upload( $_FILES['field_'.$field_id] );
                        remove_filter('upload_dir', array($this, 'bxcft_profile_upload_dir'), 10 );
                        $value = str_replace($uploads['baseurl'], '', $uploaded_file['url']);
                    }
                } else {
                    // Handles delete checkbox.
                    if ($field->type == 'image' && isset($_POST['field_'.$field_id.'_deleteimg']) &&
                            $_POST['field_'.$field_id.'_deleteimg'])
                    {
                        if (isset($_POST['field_'.$field_id.'_hiddenimg'])      &&
                            !empty($_POST['field_'.$field_id.'_hiddenimg'])     &&
                            file_exists($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenimg']))
                        {
                            unlink($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenimg']);
                        }
                        $value = array();
                    }
                    elseif ($field->type == 'image')
                    {
                        $value = (isset($_POST['field_'.$field_id.'_hiddenimg'])) ?
                                    $_POST['field_'.$field_id.'_hiddenimg'] : '';
                    }

                    if ($field->type == 'file' && isset($_POST['field_'.$field_id.'_deletefile']) &&
                            $_POST['field_'.$field_id.'_deletefile'])
                    {
                        if (isset($_POST['field_'.$field_id.'_hiddenfile'])     &&
                            !empty($_POST['field_'.$field_id.'_hiddenfile'])    &&
                            file_exists($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenfile']))
                        {
                            unlink($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenfile']);
                        }
                        $value = array();
                    }
                    elseif ($field->type == 'file')
                    {
                        $value = isset($_POST['field_'.$field_id.'_hiddenfile']) ?
                                    $_POST['field_'.$field_id.'_hiddenfile'] : '';
                    }
                }

                $data->value = (isset($value))?$value:'';
            }
            elseif ($field->type == 'birthdate') {
                $max_age = 0;
                $options = $field->get_children();
                foreach ($options as $o) {
                    if ($o->name != 'show_age' && $o->name != 'show_birthdate') {
                        $max_age = (int)$o->name;
                        break;
                    }
                }

                if ($max_age > 0) {
                    // Check birthdate.
                    if (class_exists(DateTime)) {
                        $now = new DateTime();
                        $birthdate = new DateTime(sprintf("%s-%s-%s",
                            $_POST['field_'.$field_id.'_year'],
                            $_POST['field_'.$field_id.'_month'],
                            $_POST['field_'.$field_id.'_day']));
                        $age = $now->diff($birthdate);
                        if ($age->y < $max_age) {
                            bp_core_add_message( sprintf(__( 'You have to be at least %s years old.', 'bxcft' ), $max_age), 'error' );
                            bp_core_redirect( trailingslashit( bp_displayed_user_domain() . $bp->profile->slug . '/edit/group/' . bp_action_variable( 1 ) ) );
                        }
                    }
                }
            }
        }

        public function bxcft_xprofile_data_after_delete($data)
        {
            $field_id = $data->field_id;
            $field = new BP_XProfile_Field($field_id);
            $uploads = wp_upload_dir();
            if ($field->type == 'image' && isset($_POST['field_'.$field_id.'_deleteimg']) &&
                $_POST['field_'.$field_id.'_deleteimg'])
            {
                if (isset($_POST['field_'.$field_id.'_hiddenimg']) &&
                        !empty($_POST['field_'.$field_id.'_hiddenimg']) &&
                        file_exists($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenimg']))
                {
                    unlink($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenimg']);
                }
            }

            if ($field->type == 'file' && isset($_POST['field_'.$field_id.'_deletefile']) &&
                    $_POST['field_'.$field_id.'_deletefile'])
            {
                if (isset($_POST['field_'.$field_id.'_hiddenfile']) &&
                        !empty($_POST['field_'.$field_id.'_hiddenfile']) &&
                        file_exists($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenfile']))
                {
                    unlink($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenfile']);
                }
            }
        }

        public function bxcft_xprofile_set_field_data_pre_validate( $value, $field, $field_type_obj ) {
            if ($field->type === 'multiselect_custom_taxonomy') {
                $options = $field->get_children();
                $allow_new_tags = false;
                $taxonomy_selected = '';
                foreach ($options as $option) {
                    if ( Bxcft_Field_Type_MultiSelectCustomTaxonomy::ALLOW_NEW_TAGS === $option->name) {
                        $allow_new_tags = true;
                    } else {
                        $taxonomy_selected = $option->name;
                    }
                }

                if ( $allow_new_tags && ! empty( $taxonomy_selected ) ) {
                    // Add new tags if needed.
                    $value_to_return = array();
                    foreach ( $value as $tag ) {
                        if ( ! term_exists( (int) $tag, $taxonomy_selected ) &&
                            ! term_exists( $tag, $taxonomy_selected ) ) {
                            // Create tag.
                            $res = wp_insert_term( $tag, $taxonomy_selected );
                            if (is_array($res)) {
                                $value_to_return[] = "{$res['term_id']}";
                            } else {
                                $value_to_return[] = $tag;
                            }
                        } else {
                            $value_to_return[] = $tag;
                        }
                    }
                } else {
	                $value_to_return = $value;
                }
            } else {
                $value_to_return = $value;
            }

            return $value_to_return;
        }

        public function bxcft_standard_fields_validation_type($field_type)
        {
            switch($field_type) {
                case 'birthdate':
                    // Search by age.
                    $field_type = 'datebox';
                    break;

                case 'email':
                case 'web':
                    $field_type = 'textbox';
                    break;

                case 'number_minmax':
                case 'slider':
                    $field_type = 'number';
                    break;
            }

            return $field_type;
        }

        public function bxcft_standard_fields_for_query($field_type) {
            switch($field_type) {
                case 'birthdate':
                case 'datepicker':
                    // Search by age.
                    $field_type = 'datebox';
                    break;

                case 'email':
                case 'web':
                case 'color':
                case 'decimal_number':
                    $field_type = 'textbox';
                    break;

                case 'number_minmax':
                case 'slider':
                    $field_type = 'number';
                    break;

                case 'select_custom_post_type':
                case 'select_custom_taxonomy':
                    $field_type = 'selectbox';
                    break;

                case 'multiselect_custom_post_type':
                case 'multiselect_custom_taxonomy':
                    $field_type = 'multiselectbox';
                    break;

                case 'checkbox_acceptance':
                    $field_type = 'radio';
                    break;
            }

            return $field_type;
        }

        public function bxcft_standard_fields_type_for_search_form($field_type) {
            switch($field_type) {
                case 'birthdate':
                case 'datepicker':
                    // Search by age.
                    $field_type = 'datebox';
                    break;

                case 'email':
                case 'web':
                case 'color':
                case 'decimal_number':
                    $field_type = 'textbox';
                    break;

                case 'number_minmax':
                case 'slider':
                    $field_type = 'number';
                    break;
            }

            return $field_type;
        }

        public function bxcft_special_fields_search_validation($settings, $field) {
            list($value, $description, $range) = $settings;
            switch ($field->type) {
                case 'decimal_number':
                    $range = true;
                    break;

                case 'datepicker':
                case 'select_custom_post_type':
                case 'multiselect_custom_post_type':
                case 'select_custom_taxonomy':
                case 'multiselect_custom_taxonomy':
                case 'checkbox_acceptance':
                case 'image':
                case 'file':
                case 'color':
                    $range = false;
                    break;
            }

            return array($value, $description, $range);
        }

        public function bxcft_special_fields_data_for_search_form($f) {
            $request = stripslashes_deep ($_REQUEST);
            switch ($f->type) {
                case 'select_custom_post_type':
                case 'multiselect_custom_post_type':
                    $f->values = isset ($request[$f->code])? (array)$request[$f->code]: array ();
                    $field = new BP_XProfile_Field($f->id);
                    $array_options = array();
                    if ($field) {
                        $childs = $field->get_children();
                        if (isset($childs) && $childs && count($childs) > 0
                                && is_object($childs[0])) {
                            $post_type_selected = $childs[0]->name;
                            $posts = new WP_Query(array(
                                'posts_per_page'    => -1,
                                'post_type'         => $post_type_selected,
                                'orderby'           => 'title',
                                'order'             => 'ASC'
                            ));
                            if ($posts) {
                                foreach ($posts->posts as $p) {
                                    $array_options[$p->ID] = $p->post_title;
                                }
                            }
                        }
                    }
                    $f->options = $array_options;
                    if ($f->type === 'select_custom_post_type') {
                        $f->display = 'selectbox';
                    } else {
                        $f->display = 'multiselectbox';
                    }
                    break;

                case 'select_custom_taxonomy':
                case 'multiselect_custom_taxonomy':
                    $f->values = isset ($request[$f->code])? (array)$request[$f->code]: array ();
                    $field = new BP_XProfile_Field($f->id);
                    $array_options = array();
                    if ($field) {
                        $childs = $field->get_children();
                        if (isset($childs) && $childs && count($childs) > 0
                                && is_object($childs[0])) {
                            $taxonomy_selected = $childs[0]->name;
                            $terms = get_terms($taxonomy_selected, array(
                                'hide_empty' => false
                            ));
                            if ($terms) {
                                foreach ($terms as $t) {
                                    $array_options[$t->term_id] = $t->name;
                                }
                            }
                        }
                    }
                    $f->options = $array_options;
                    if ($f->type === 'select_custom_taxonomy') {
                        $f->display = 'selectbox';
                    } else {
                        $f->display = 'multiselectbox';
                    }
                    break;

                case 'checkbox_acceptance':
                    $f->values = isset ($request[$f->code])? (array)$request[$f->code]: array ();
                    $f->options = array(
                        0 => __('no', 'bxcft'),
                        1 => __('yes', 'bxcft')
                    );
                    $f->display = 'radio';
                    break;

                case 'image':
                case 'file':
                    $f->values = isset ($request[$f->code])? (array)$request[$f->code]: array ();
                    $f->options = array(
                        1 => __('Not empty', 'bxcft')
                    );
                    $f->display = 'checkbox';
                    break;
            }

            return $f;
        }

        public function bxcft_special_fields_query($results, $field, $key, $value) {
            global $bp, $wpdb;
            switch ($field->type) {
                case 'image':
                case 'file':
                    $sql = $wpdb->prepare ("SELECT user_id FROM {$bp->profile->table_name_data} WHERE field_id = %d AND value != ''", $field->id);
                    $results = $wpdb->get_col ($sql);
                    break;
            }

            return $results;
        }

        public function bxcft_show_select2_box($field) {
            $do_select2 = bp_xprofile_get_meta( $field->id, 'field', 'do_select2');
            $hidden = true;
            if (in_array($field->type, $this->fields_type_with_select2)) {
                $hidden = false;
            }
        ?>
        <div id="select2-box" class="postbox<?php if ($hidden): ?> hidden<?php endif; ?>">
            <h2><?php esc_html_e('Select2', 'bxft'); ?></h2>
            <div class="inside">
                <p class="description">Enable select2 javascript code.</p>

                <p>
                    <label for="do-select2" class="screen-reader-text">Select2 status for this field</label>
                    <select name="do_select2" id="do-select2">
                        <option value="on" <?php if ($do_select2 === 'on'): ?> selected="selected"<?php endif; ?>>Enabled</option>
                        <option value=""<?php if ($do_select2 !== 'on'): ?> selected="selected"<?php endif; ?>>Disabled</option>
                    </select>
                </p>
            </div>
        </div>
        <?php
        }

        public function bxcft_save_do_select2($field) {
            $field_id = $field->id;

            if (!in_array($field->type, $this->fields_type_with_select2)) {
                bp_xprofile_update_field_meta($field_id, 'do_select2', '' );
                return;
            }

            // Save select2 settings.
            if ( 1 != $field_id ) {
                if ( isset( $_POST['do_select2'] ) && 'on' === wp_unslash( $_POST['do_select2'] ) ) {
                    bp_xprofile_update_field_meta( $field_id, 'do_select2', 'on' );
                } else {
                    bp_xprofile_update_field_meta( $field_id, 'do_select2', 'off' );
                }
            }
        }

        public function bxcft_enable_select2_field() {
            global $field;

            if (!in_array($field->type, $this->fields_type_with_select2)) {
                return;
            }

            $do_select2 = bp_xprofile_get_meta($field->id, 'field', 'do_select2');
            if ($do_select2 === 'on') {
                $field_name_id = bp_get_the_profile_field_input_name();
                if (in_array($field->type, $this->fields_type_multiple)) {
                    $field_name_id .= '[]';
                }

                $allow_new_tags = false;
                $options = $field->get_children();
                foreach ($options as $option) {
                    if (Bxcft_Field_Type_MultiSelectCustomTaxonomy::ALLOW_NEW_TAGS === $option->name) {
                        $allow_new_tags = true;
                    }
                }

                if ($allow_new_tags) {
            ?>
                <script>
                    jQuery(function($) {
                        $('select[name="<?php echo $field_name_id; ?>"]').select2({
                            tags: true,
                            tokenSeparators: [',']
                        });
                    });
                </script>
            <?php
                } else {
            ?>
                <script>
                    jQuery(function($) {
                        $('select[name="<?php echo $field_name_id; ?>"]').select2();
                    });
                </script>
            <?php
                }
            }
        }

        public function bxcft_update()
        {
            $locale = apply_filters( 'bxcft_load_load_textdomain_get_locale', get_locale() );
            if ( !empty( $locale ) ) {
                $mofile_default = sprintf( '%slang/%s.mo', plugin_dir_path(__FILE__), $locale );
                $mofile = apply_filters( 'bxcft_load_textdomain_mofile', $mofile_default );

                if ( file_exists( $mofile ) ) {
                    load_textdomain( "bxcft", $mofile );
                }
            }

            if (!get_option('bxcft_activated')) {
                add_option('bxcft_activated', 1);
            }
            if (!get_option('bxcft_notices')) {
                add_option('bxcft_notices');
            }
        }

        /**
         * Returns true if autolink filter is removed,
         * false if it exists.
         *
         * @since  2.4.6
         * @return boolean
         */
        public static function is_autolink_filter_removed() {
            return !(has_filter('bp_get_the_profile_field_value',
                'xprofile_filter_link_profile_data', 9));
        }

        public static function activate()
        {
            add_option('bxcft_activated', 1);
            add_option('bxcft_notices', array());
        }

        public static function deactivate()
        {
            delete_option('bxcft_activated');
            delete_option('bxcft_notices');
        }
    }
}

if (class_exists('Bxcft_Plugin')) {
    register_activation_hook(__FILE__, array('Bxcft_Plugin', 'activate'));
    register_deactivation_hook(__FILE__, array('Bxcft_Plugin', 'deactivate'));
    $bxcft_plugin = new Bxcft_Plugin();
}