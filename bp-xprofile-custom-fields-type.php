<?php
/*
    Plugin Name: BuddyPress Xprofile Custom Fields Type
    Plugin URI: http://donmik.com/en/buddypress-xprofile-custom-fields-type/
    Description: BuddyPress installation required!! This plugin add custom field types to BuddyPress Xprofile extension. Field types are: Birthdate, Email, Url, Datepicker, ...
    Version: 2.1.6
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

        public function __construct ()
        {
            $this->version = "2.1.5";

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

            /** Filters **/
            add_filter( 'bp_xprofile_get_field_types', array($this, 'bxcft_get_field_types'), 10, 1 );
            add_filter( 'xprofile_get_field_data', array($this, 'bxcft_get_field_data'), 10, 2 );
            add_filter( 'bp_get_the_profile_field_value', array($this, 'bxcft_get_field_value'), 10, 3 );
            /** BP Profile Search Filters **/
            add_filter ('bps_field_validation_type', array($this, 'bxcft_map'), 10, 2);
            add_filter ('bps_field_html_type', array($this, 'bxcft_map'), 10, 2);
            add_filter ('bps_field_criteria_type', array($this, 'bxcft_map'), 10, 2);
            add_filter ('bps_field_query_type', array($this, 'bxcft_map'), 10, 2);
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

            $locale = apply_filters( 'bxcft_load_load_textdomain_get_locale', get_locale() );
            if ( !empty( $locale ) ) {
                $mofile_default = sprintf( '%slang/%s.mo', plugin_dir_path(__FILE__), $locale );
                $mofile = apply_filters( 'bxcft_load_textdomain_mofile', $mofile_default );

                if ( file_exists( $mofile ) ) {
                    load_textdomain( "bxcft", $mofile );
                }
            }

            /** Includes **/
            require_once( 'classes/Bxcft_Field_Type_Birthdate.php' );
            require_once( 'classes/Bxcft_Field_Type_Email.php' );
            require_once( 'classes/Bxcft_Field_Type_Web.php' );
            require_once( 'classes/Bxcft_Field_Type_Datepicker.php' );
            require_once( 'classes/Bxcft_Field_Type_SelectCustomPostType.php' );
            require_once( 'classes/Bxcft_Field_Type_MultiSelectCustomPostType.php' );
            require_once( 'classes/Bxcft_Field_Type_CheckboxAcceptance.php' );
            require_once( 'classes/Bxcft_Field_Type_Image.php' );
            require_once( 'classes/Bxcft_Field_Type_File.php' );
            require_once( 'classes/Bxcft_Field_Type_Color.php' );

            if (bp_is_user_profile_edit() || bp_is_register_page()) {
                wp_enqueue_script('bxcft-modernizr', plugin_dir_url(__FILE__) . 'js/modernizr.js', array(), '2.6.2', false);
                wp_enqueue_script('bxcft-jscolor', plugin_dir_url(__FILE__) . 'js/jscolor/jscolor.js', array(), '1.4.1', true);
            }
        }

        public function admin_init()
        {
            if (is_admin() && get_option('bxcft_activated') == 1) {
                delete_option('bxcft_activated');
                // Check if BuddyPress 2.0 is installed.
                $version_bp = 0;
                if (function_exists('is_plugin_active') && is_plugin_active('buddypress/bp-loader.php')) {
                    // BuddyPress loaded.
                    $data = get_file_data(WP_PLUGIN_DIR . '/buddypress/bp-loader.php', array('Version'));
                    if (isset($data) && count($data) > 0 && $data[0] != '') {
                        $version_bp = (float)$data[0];
                    }
                }
                if ($version_bp < 2) {
                    $notices = get_option('bxcft_notices');
                    $notices[] = __('BuddyPress Xprofile Custom Fields Type plugin needs <b>BuddyPress 2.0</b>, please install or upgrade BuddyPress.', 'bxcft');
                    update_option('bxcft_notices', $notices);
                }

                // Enqueue javascript.
                wp_enqueue_script('bxcft-js', plugin_dir_url(__FILE__) . 'js/admin.js', array(), $this->version, true);
            }
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
            $new_fields = array(
                'birthdate'                     => 'Bxcft_Field_Type_Birthdate',
                'email'                         => 'Bxcft_Field_Type_Email',
                'web'                           => 'Bxcft_Field_Type_Web',
                'datepicker'                    => 'Bxcft_Field_Type_Datepicker',
                'select_custom_post_type'       => 'Bxcft_Field_Type_SelectCustomPostType',
                'multiselect_custom_post_type'  => 'Bxcft_Field_Type_MultiSelectCustomPostType',
                'checkbox_acceptance'           => 'Bxcft_Field_Type_CheckboxAcceptance',
                'image'                         => 'Bxcft_Field_Type_Image',
                'file'                          => 'Bxcft_Field_Type_File',
                'color'                         => 'Bxcft_Field_Type_Color',
            );
            $fields = array_merge($fields, $new_fields);

            return $fields;
        }

        public function bxcft_get_field_data($value, $field_id)
        {
            $field = new BP_XProfile_Field($field_id);
            $value_to_return = strip_tags($value);
            if ($value_to_return !== '') {
                // Birthdate.
                if ($field->type == 'birthdate') {
                    $show_age = false;
                    if ($field) {
                        $childs = $field->get_children();
                        if (isset($childs) && $childs && count($childs) > 0
                                && is_object($childs[0]) && $childs[0]->name == 'show_age') {
                            $show_age = true;
                        }
                    }
                    if ($show_age) {
                        $value_to_return = floor((time() - strtotime($value_to_return))/31556926);
                    } else {
                        $value_to_return = date_i18n(get_option('date_format') ,strtotime($value_to_return) );
                    }
                }
                // Email.
                elseif ($field->type == 'email') {
                    if (strpos($value_to_return, 'mailto') === false) {
                        $value_to_return = sprintf('<a href="mailto:%s">%s</a>',
                                                $value_to_return,
                                                $value_to_return);
                    }
                }
                // Web.
                elseif ($field->type == 'web') {
                    if (strpos($value_to_return, 'href=') === false) {
                        $value_to_return = sprintf('<a href="%s">%s</a>',
                            $value_to_return,
                            $value_to_return);
                    }
                }
                // Datepicker.
                elseif ($field->type == 'datepicker') {
                    $value_to_return = date_i18n(get_option('date_format') ,strtotime($value_to_return) );
                }
                // Select custom post type.
                elseif ($field->type == 'select_custom_post_type') {
                    if ($field) {
                        $childs = $field->get_children();
                        if (isset($childs) && $childs && count($childs) > 0
                                && is_object($childs[0])) {
                            $post_type_selected = $childs[0]->name;
                        }
                        $post = get_post($value_to_return);
                        if ($post->post_type == $post_type_selected) {
                            $value_to_return = $post->post_title;
                        } else {
                            // Custom post type is not the same.
                            $value_to_return = '--';
                        }
                    } else {
                        $value_to_return = '--';
                    }
                }
                // Multi select custom post type.
                elseif ($field->type == 'multiselect_custom_post_type') {
                    if ($field) {
                        $values = explode(",", $value_to_return);
                        $childs = $field->get_children();
                        if (isset($childs) && $childs && count($childs) > 0
                                && is_object($childs[0])) {
                            $post_type_selected = $childs[0]->name;
                            $cad = '';
                            foreach ($values as $v) {
                                $post = get_post($v);
                                if ($post->post_type == $post_type_selected) {
                                    if ($cad == '')
                                        $cad .= '<ul class="list_custom_post_type">';
                                    $cad .= '<li>'.$post->post_title.'</li>';
                                }
                            }
                            if ($cad != '') {
                                $cad .= '</ul>';
                            }
                        }
                        $value_to_return = $cad;
                    } else {
                        $value_to_return = '--';
                    }
                }
                // Checkbox acceptance.
                elseif ($field->type == 'checkbox_acceptance') {
                    $value_to_return = (((int)$value_to_return==1)?__('yes', 'bxcft'):__('no', 'bxcft'));
                }
                // Image.
                elseif ($field->type == 'image') {
                    $uploads = wp_upload_dir();
                    if (strpos($value_to_return, $uploads['baseurl']) === false) {
                        $value_to_return = $uploads['baseurl'].$value_to_return;
                    }
                    $value_to_return = '<img src="'.$value_to_return.'" alt="" />';
                }
                // File.
                elseif ($field->type == 'file') {
                    $uploads = wp_upload_dir();
                    if (strpos($value_to_return, $uploads['baseurl']) === false) {
                        $value_to_return = $uploads['baseurl'].$value_to_return;
                    }
                    $value_to_return = '<a href="'.$value_to_return.'">'.__('Download file', 'bxcft').'</a>';
                }
                // Color.
                elseif ($field->type == 'color') {
                    if (strpos($value_to_return, '#') === false) {
                        $value_to_return = '#'.$value_to_return;
                    }
                } else {
                    // Not stripping tags.
                    $value_to_return = $value;
                }
            }

            return apply_filters('bxcft_show_field_value', $value_to_return, $field->type, $field_id, $value);
        }

        public function bxcft_get_field_value($value='', $type='', $id='')
        {
            $value_to_return = strip_tags($value);
            if ($value_to_return !== '') {
                // Birthdate.
                if ($type == 'birthdate') {
                    $show_age = false;
                    $field = new BP_XProfile_Field($id);
                    if ($field) {
                        $childs = $field->get_children();
                        if (isset($childs) && $childs && count($childs) > 0
                                && is_object($childs[0]) && $childs[0]->name == 'show_age') {
                            $show_age = true;
                        }
                    }
                    if ($show_age) {
                        $value_to_return = floor((time() - strtotime($value_to_return))/31556926);
                    } else {
                        $value_to_return = date_i18n(get_option('date_format') ,strtotime($value_to_return) );
                    }
                }
                // Email.
                elseif ($type == 'email') {
                    if (strpos($value_to_return, 'mailto') === false) {
                        $value_to_return = sprintf('<a href="mailto:%s">%s</a>',
                                                $value_to_return,
                                                $value_to_return);
                    }
                }
                // Web.
                elseif ($type == 'web') {
                    if (strpos($value_to_return, 'href=') === false) {
                        $value_to_return = sprintf('<a href="%s">%s</a>',
                            $value_to_return,
                            $value_to_return);
                    }
                }
                // Datepicker.
                elseif ($type == 'datepicker') {
                    $value_to_return = date_i18n(get_option('date_format') ,strtotime($value_to_return) );
                }
                // Select custom post type.
                elseif ($type == 'select_custom_post_type') {
                    $field = new BP_XProfile_Field($id);
                    if ($field) {
                        $childs = $field->get_children();
                        if (isset($childs) && $childs && count($childs) > 0
                                && is_object($childs[0])) {
                            $post_type_selected = $childs[0]->name;
                        }
                        $post = get_post($value_to_return);
                        if ($post->post_type == $post_type_selected) {
                            $value_to_return = $post->post_title;
                        } else {
                            // Custom post type is not the same.
                            $value_to_return = '--';
                        }
                    } else {
                        $value_to_return = '--';
                    }
                }
                // Multi select custom post type.
                elseif ($type == 'multiselect_custom_post_type') {
                    $field = new BP_XProfile_Field($id);
                    if ($field) {
                        $values = explode(",", $value_to_return);
                        $childs = $field->get_children();
                        if (isset($childs) && $childs && count($childs) > 0
                                && is_object($childs[0])) {
                            $post_type_selected = $childs[0]->name;
                            $cad = '';
                            foreach ($values as $v) {
                                $post = get_post($v);
                                if ($post->post_type == $post_type_selected) {
                                    if ($cad == '')
                                        $cad .= '<ul class="list_custom_post_type">';
                                    $cad .= '<li>'.$post->post_title.'</li>';
                                }
                            }
                            if ($cad != '') {
                                $cad .= '</ul>';
                            }
                        }
                        $value_to_return = $cad;
                    } else {
                        $value_to_return = '--';
                    }
                }
                // Checkbox acceptance.
                elseif ($type == 'checkbox_acceptance') {
                    $value_to_return = (((int)$value_to_return==1)?__('yes', 'bxcft'):__('no', 'bxcft'));
                }
                // Image.
                elseif ($type == 'image') {
                    $uploads = wp_upload_dir();
                    if (strpos($value_to_return, $uploads['baseurl']) === false) {
                        $value_to_return = $uploads['baseurl'].$value_to_return;
                    }
                    $value_to_return = '<img src="'.$value_to_return.'" alt="" />';
                }
                // File.
                elseif ($type == 'file') {
                    $uploads = wp_upload_dir();
                    if (strpos($value_to_return, $uploads['baseurl']) === false) {
                        $value_to_return = $uploads['baseurl'].$value_to_return;
                    }
                    $value_to_return = '<a href="'.$value_to_return.'">'.__('Download file', 'bxcft').'</a>';
                }
                // Color.
                elseif ($type == 'color') {
                    if (strpos($value_to_return, '#') === false) {
                        $value_to_return = '#'.$value_to_return;
                    }
                } else {
                    // Not stripping tags.
                    $value_to_return = $value;
                }
            }

            return apply_filters('bxcft_show_field_value', $value_to_return, $type, $id, $value);
        }

        public function bxcft_profile_upload_dir( $user_id = 0 )
        {
            if ($user_id == 0 && empty($this->user_id))
            {
                $this->user_id = bp_displayed_user_id();
            }
            $profile_subdir = '/profiles/' . $this->user_id;

            $upload_dir = array(
                'path'    => bp_core_get_upload_dir()       . $profile_subdir,
                'url'     => bp_core_get_upload_dir('url')  . $profile_subdir,
                'subdir'  => bp_core_get_upload_dir()       . $profile_subdir,
                'basedir' => bp_core_get_upload_dir()       . $profile_subdir,
                'baseurl' => bp_core_get_upload_dir('url')  . $profile_subdir,
                'error'   => false,
            );

            return $upload_dir;
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
                    } // End foreach...
                } // End if ( isset...
            } // End if ( bp_is_active(...
        }

        function bxcft_xprofile_data_before_save($data)
        {
            global $bp;

            $field_id = $data->field_id;
            $field = new BP_XProfile_Field($field_id);

            if ($field->type == 'image' || $field->type == 'file')
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

        public function bxcft_map($field_type, $field)
        {
            switch($field_type) {
                case 'birthdate':
                case 'datepicker':
                    $field_type = 'datebox';
                    break;

                case 'email':
                case 'web':
                case 'image':
                case 'file':
                case 'color':
                    $field_type = 'textbox';
                    break;

                case 'select_custom_post_type':
                case 'multiselect_custom_post_type':
                case 'checkbox_acceptance':
                    $field_type = 'selectbox';
                    break;
            }

            return $field_type;
        }

        public function bxcft_update()
        {
            if (!get_option('bxcft_activated')) {
                add_option('bxcft_activated', 1);
            }
            if (!get_option('bxcft_notices')) {
                add_option('bxcft_notices');
            }
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