<?php
/*
    Plugin Name: Buddypress Xprofile Custom Fields Type
    Plugin URI: https://github.com/donmik/buddypress-xprofile-custom-fields-type/
    Description: Buddypress installation required!! Add more custom fields type to extended profiles in buddypress: Birthdate, Email, Web, Datepicker. If you need more fields type, you are free to add them yourself or request us at miguel@donmik.com.
    Version: 1.5.9.4
    Author: donmik
    Author URI: http://donmik.com
*/
//load text domain
function bxcft_load_textdomain() {
    $locale = apply_filters( 'bxcft_load_load_textdomain_get_locale', get_locale() );
	// if load .mo file
	if ( !empty( $locale ) ) {
		$mofile_default = sprintf( '%slang/%s.mo', plugin_dir_path(__FILE__), $locale );
		$mofile = apply_filters( 'bxcft_load_textdomain_mofile', $mofile_default );

        if ( file_exists( $mofile ) ) 
			load_textdomain( "bxcft", $mofile );
	}
}
add_action ( 'bp_init', 'bxcft_load_textdomain', 2 );

function bxcft_add_new_xprofile_field_type($field_types){
   $new_field_types = array('birthdate', 'email', 'web', 'datepicker',
       'select_custom_post_type', 'multiselect_custom_post_type', 
       'checkbox_acceptance', 'image', 'file', 'color', 'number');
   $field_types = array_merge($field_types, $new_field_types);
   return $field_types;
}
add_filter( 'xprofile_field_types', 'bxcft_add_new_xprofile_field_type' );

function bxcft_admin_render_new_xprofile_field_type($field, $echo = true) {
   $html = '';
   switch ( $field->type ) {
       case 'number':
           $html .= '<input type="number" name="field_'.$field->id.'" id="'.$field->id.'" class="input-number" />';
           break;
       
       case 'color':
           $html .= '<input type="color" name="field_'.$field->id.'" id="'.$field->id.'" class="input-color" />';
           break;
       
       case 'image':
       case 'file':
           $html .= '<input type="file" name="field_'.$field->id.'" id="'.$field->id.'" class="input-file" /> ';
           break;
       
       case 'checkbox_acceptance':
           $html .= '<input type="checkbox" name="field_'.$field->id.'" id="'.$field->id.'" class="input-checkbox" value="" /> ';
           $html .= $field->description;
           break;
       
       case 'select_custom_post_type':
           $childs = $field->get_children();
           if (isset($childs) && count($childs) > 0 && is_object($childs[0])) {
               // Get the name of custom post type.
               $custom_post_type = $childs[0]->name;
               // Get the posts of custom post type.
               $loop = new WP_Query(array('posts_per_page' => -1, 'post_type' => $custom_post_type, 'orderby' => 'title', 'order' => 'ASC' ));
           }
           $select_custom_post_type = BP_XProfile_ProfileData::get_value_byid( $field->id );
           $html .= '<select name="field_'.$field->id.'" id="field_'.$field->id.'">';
           if (isset($loop)) {
           foreach ($loop->posts as $post) {
                $html .= '<option value="'.$post->ID.'">'.$post->post_title.'</option>';
           }
           }
           $html .= '</select>';
           break;
           
       case "multiselect_custom_post_type":
           $childs = $field->get_children();
           if (isset($childs) && count($childs) > 0 && is_object($childs[0])) {
               // Get the name of custom post type.
               $custom_post_type = $childs[0]->name;
               // Get the posts of custom post type.
               $loop = new WP_Query(array('posts_per_page' => -1, 'post_type' => $custom_post_type, 'orderby' => 'title', 'order' => 'ASC' ));
           }
           $select_custom_post_type = BP_XProfile_ProfileData::get_value_byid( $field->id );
           $html .= '<select name="field_'.$field->id.'" id="field_'.$field->id.'" multiple="multiple">';
           if (isset($loop)) {
           foreach ($loop->posts as $post) {
                $html .= '<option value="'.$post->ID.'">'.$post->post_title.'</option>';
           }
           }
           $html .= '</select>';
           break;
       
       case "datepicker":
           $html .= '<input type="date" name="field_'.$field->id.'" id="'.$field->id.'" class="input" value="" />';
           break;
       
       case "web":
           $html .= '<input type="url" name="field_'.$field->id.'" id="'.$field->id.'" class="input" placeholder="'.__('yourwebsite.com', 'bxcft').'" value="" />';
           break;
       
       case "email":
           $html .= '<input type="email" name="field_'.$field->id.'" id="'.$field->id.'" class="input" placeholder="'.__('example@mail.com', 'bxcft').'" value="" />';
           break;
       
       case "birthdate":
           $date = BP_XProfile_ProfileData::get_value_byid( $field->id );

           // Set day, month, year defaults
           $day   = '';
           $month = '';
           $year  = '';

           if ( !empty( $date ) ) {

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

           // Check for updated posted values, and errors preventing
           // them from being saved first time.
           if ( !empty( $_POST['field_' . $field->id . '_day'] ) ) {
               if ( $day != $_POST['field_' . $field->id . '_day'] ) {
                   $day = $_POST['field_' . $field->id . '_day'];
               }
           }

           if ( !empty( $_POST['field_' . $field->id . '_month'] ) ) {
               if ( $month != $_POST['field_' . $field->id . '_month'] ) {
                   $month = $_POST['field_' . $field->id . '_month'];
               }
           }

           if ( !empty( $_POST['field_' . $field->id . '_year'] ) ) {
               if ( $year != date( "j", $_POST['field_' . $field->id . '_year'] ) ) {
                   $year = $_POST['field_' . $field->id . '_year'];
               }
           }

           // Day.
           $html .= '<select name="field_'.$field->id.'_day" id="field_'.$field->id.'_day">';
           $html .= '<option value=""' . selected( $day, '', false ) . '>--</option>';
           for ( $i = 1; $i < 32; ++$i ) {
               $html .= '<option value="' . $i .'"' . selected( $day, $i, false ) . '>' . $i . '</option>';
           }
           $html .= '</select>';

           // Month.
           $html .= '<select name="field_'.$field->id.'_month" id="field_'.$field->id.'_month">';
           $eng_months = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
           $months = array(
               __( 'January', 'buddypress' ),
               __( 'February', 'buddypress' ),
               __( 'March', 'buddypress' ),
               __( 'April', 'buddypress' ),
               __( 'May', 'buddypress' ),
               __( 'June', 'buddypress' ),
               __( 'July', 'buddypress' ),
               __( 'August', 'buddypress' ),
               __( 'September', 'buddypress' ),
               __( 'October', 'buddypress' ),
               __( 'November', 'buddypress' ),
               __( 'December', 'buddypress' )
           );
           $html .= '<option value=""' . selected( $month, '', false ) . '>------</option>';
           for ( $i = 0; $i < 12; ++$i ) {
               $html .= '<option value="' . $eng_months[$i] . '"' . selected( $month, $eng_months[$i], false ) . '>' . $months[$i] . '</option>';
           }
           $html .= '</select>';

           // Year.
           $html .= '<select name="field_'.$field->id.'_year" id="field_'.$field->id.'_year">';
           $html .= '<option value=""' . selected( $year, '', false ) . '>----</option>';
           for ( $i = date('Y')-1; $i > 1901; $i-- ) {
               $html .= '<option value="' . $i .'"' . selected( $year, $i, false ) . '>' . $i . '</option>';
           }
           $html .= '</select>';
           break;

       default:
           // Field type unrecognized.
           // $html = "<p>".__('Field type unrecognized', 'bxcft')."</p>";
           break;
   }

   if ($echo){
       echo $html;
       return;
   } else {
       return $html;
   }

}
add_filter( 'xprofile_admin_field', 'bxcft_admin_render_new_xprofile_field_type' );

function bxcft_edit_render_new_xprofile_field($echo = true) {
   global $field;
   if (!is_bool($echo)) {
       $echo = true;
   }

   $uploads = wp_upload_dir();

   ob_start();
       if ( bp_get_the_profile_field_type() == 'birthdate' ) {
           $date = BP_XProfile_ProfileData::get_value_byid( $field->id );
           // Set day, month, year defaults
           $day   = '';
           $month = '';
           $year  = '';

           if ( !empty( $date ) ) {

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

           // Check for updated posted values, and errors preventing
           // them from being saved first time.
           if ( !empty( $_POST['field_' . $field->id . '_day'] ) ) {
               if ( $day != $_POST['field_' . $field->id . '_day'] ) {
                   $day = $_POST['field_' . $field->id . '_day'];
               }
           }

           if ( !empty( $_POST['field_' . $field->id . '_month'] ) ) {
               if ( $month != $_POST['field_' . $field->id . '_month'] ) {
                   $month = $_POST['field_' . $field->id . '_month'];
               }
           }

           if ( !empty( $_POST['field_' . $field->id . '_year'] ) ) {
               if ( $year != date( "j", $_POST['field_' . $field->id . '_year'] ) ) {
                   $year = $_POST['field_' . $field->id . '_year'];
               }
           }
       ?>
           <div class="datebox">
            <?php
                $label = sprintf('<label class="label-form%s" for="%s_day">%s%s</label>',
                                bp_get_the_profile_field_is_required()?' required':'',
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_name(),
                                bp_get_the_profile_field_is_required()?' '.__('(required)', 'buddypress'):'');
                
                // Dropdown => Day of month.
                $options_day = '';
                for ($i=1; $i<32; ++$i) {
                    $options_day .= sprintf('<option value="%s"%s>%s</option>',
                                            $i,
                                            selected($day, $i, false),
                                            $i);
                } 
                $input = sprintf('<select name="%s_day" id="%s_day"%s><option value=""%s>--</option>%s</select>',
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_is_required()?'aria-required="true"':'',
                                        selected($day, '', false),
                                        $options_day); 
                
                // Dropdown => Month.
                $eng_months = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
                $months = array(
                    __( 'January', 'buddypress' ),
                    __( 'February', 'buddypress' ),
                    __( 'March', 'buddypress' ),
                    __( 'April', 'buddypress' ),
                    __( 'May', 'buddypress' ),
                    __( 'June', 'buddypress' ),
                    __( 'July', 'buddypress' ),
                    __( 'August', 'buddypress' ),
                    __( 'September', 'buddypress' ),
                    __( 'October', 'buddypress' ),
                    __( 'November', 'buddypress' ),
                    __( 'December', 'buddypress' )
                );
                $options_month = '';
                for ($i=0; $i<12; ++$i) {
                    $options_month .= sprintf('<option value="%s"%s>%s</option>',
                                            $eng_months[$i],
                                            selected( $month, $eng_months[$i], false ),
                                            $months[$i]);
                }
                $input .= sprintf('<select name="%s_month" id="%s_month"%s><option value=""%s>------</option>%s</select>',
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_is_required()?'aria-required="true"':'',
                                        selected($month, '', false),
                                        $options_month);
                
                // Dropdown => Year.
                $birthdate_start_year = date('Y')-1;
                $options_year = '';
                for ($i=$birthdate_start_year; $i>1901; $i--) {
                    $options_year .= sprintf('<option value="%s"%s>%s</option>',
                                            $i,
                                            selected($year, $i, false),
                                            $i);
                }
                $input .= sprintf('<select name="%s_year" id="%s_year"%s><option value=""%s>----</option>%s</select>',
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_is_required()?'aria-required="true" required="required"':'',
                                        selected($year, '', false),
                                        $options_year);
                
                echo apply_filters('bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required());
                
                do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );
                
                echo apply_filters('bxcft_field_input', $input, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_is_required());
            ?>       
           </div>
       <?php
       } 
       elseif ( bp_get_the_profile_field_type() == 'email' ) {
       ?>
        <div class="input-email">
            <?php
                $label = sprintf('<label class="label-form%s" for="%s">%s%s</label>',
                                bp_get_the_profile_field_is_required()?' required':'',
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_name(),
                                bp_get_the_profile_field_is_required()?' '.__('(required)', 'buddypress'):'');
                
                $input = sprintf('<input type="email" name="%s" id="%s"%s class="input" value="%s" placeholder="%s" />',
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_is_required()?' aria-required="true" required="required"':'',
                                bp_get_the_profile_field_edit_value(),
                                __('example@mail.com', 'bxcft'));
                
                echo apply_filters('bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required());
                
                do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );
                
                echo apply_filters('bxcft_field_input', $input, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_is_required());
            ?>
       </div>
       <?php
       } 
       elseif ( bp_get_the_profile_field_type() == 'web' ) {
       ?>
        <div class="input-web">
            <?php
                $label = sprintf('<label class="label-form%s" for="%s">%s%s</label>',
                                bp_get_the_profile_field_is_required()?' required':'',
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_name(),
                                bp_get_the_profile_field_is_required()?' '.__('(required)', 'buddypress'):'');
                
                $input = sprintf('<input type="url" name="%s" id="%s"%s class="input" value="%s" placeholder="%s" />',
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_is_required()?' aria-required="true" required="required"':'',
                                bp_get_the_profile_field_edit_value(),
                                __('http://yourwebsite.com', 'bxcft'));
                
                echo apply_filters('bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required());
                
                do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );
                
                echo apply_filters('bxcft_field_input', $input, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_is_required());
            ?>
       </div>
       <?php
       }
       elseif ( bp_get_the_profile_field_type() == 'datepicker' ) {
       ?>
        <div class="input-datepicker">
            <?php
                $label = sprintf('<label class="label-form%s" for="%s">%s%s</label>',
                                bp_get_the_profile_field_is_required()?' required':'',
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_name(),
                                bp_get_the_profile_field_is_required()?' '.__('(required)', 'buddypress'):'');
                
                $input = sprintf('<input type="date" name="%s" id="%s"%s class="input" value="%s" />',
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_is_required()?' aria-required="true" required="required"':'',
                                bp_get_the_profile_field_edit_value());
                
                echo apply_filters('bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required());
                
                do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );
                
                echo apply_filters('bxcft_field_input', $input, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_is_required());
            ?>
       </div>
       <?php
       }
       elseif ( bp_get_the_profile_field_type() == 'select_custom_post_type' ) {
       ?>
        <div class="input-custom-post-type">
       <?php
           $custom_post_type_selected = BP_XProfile_ProfileData::get_value_byid( $field->id );
           if ( isset( $_POST['field_' . $field->id] ) && $_POST['field_' . $field->id] != '' ) {
                if ( !empty( $_POST['field_' . $field->id] ) ) {
                    $custom_post_type_selected = $_POST['field_' . $field->id];
                }
           }
           if (is_null($custom_post_type_selected)) {
               $custom_post_type_selected = array();
           }
           // Get field's data.
           $field_data = new BP_XProfile_Field($field->id);
           // Get the childs of field
           $childs = $field_data->get_children();
           if (isset($childs) && count($childs) > 0 && is_object($childs[0])) {
               // Get the name of custom post type.
               $custom_post_type = $childs[0]->name;
               // Get the posts of custom post type.
               $loop = new WP_Query(array('posts_per_page' => -1, 'post_type' => $custom_post_type, 'orderby' => 'title', 'order' => 'ASC' ));
               
                $label = sprintf('<label class="label-form%s" for="%s">%s%s</label>',
                                bp_get_the_profile_field_is_required()?' required':'',
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_name(),
                                bp_get_the_profile_field_is_required()?' '.__('(required)', 'buddypress'):'');
                
                $options_posts = '';
                foreach ($loop->posts as $post) {
                    $options_posts .= sprintf('<option value="%s"%s>%s</option>',
                                            $post->ID,
                                            ($custom_post_type_selected==$post->ID)?' selected="selected"':'',
                                            $post->post_title);
                }
                $input = sprintf('<select name="%s" id="%s"%s class="select"><option value="">%s</option>%s</select>',
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_is_required()?' aria-required="true" required="required"':'',
                                __('Select...', 'bxcft'),
                                $options_posts);
                
                echo apply_filters('bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required());
                
                do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );
                
                echo apply_filters('bxcft_field_input', $input, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_is_required());
           } else {
                _e('There is no custom post type selected.', 'bxcft');
           }
        ?>
        </div>
    <?php
       }
       elseif ( bp_get_the_profile_field_type() == 'multiselect_custom_post_type' ) {
    ?>
        <div class="input-custom-post-type-multi">
    <?php
           $custom_post_type_selected = maybe_unserialize(BP_XProfile_ProfileData::get_value_byid( $field->id ));
           if ( isset( $_POST['field_' . $field->id] ) && $_POST['field_' . $field->id] != '' ) {
                if ( !empty( $_POST['field_' . $field->id] ) ) {
                    $custom_post_type_selected = $_POST['field_' . $field->id];
                }
           }
           // Get field's data.
           $field_data = new BP_XProfile_Field($field->id);
           // Get the childs of field
           $childs = $field_data->get_children();
           if (isset($childs) && $childs && count($childs) > 0 && is_object($childs[0])) {
               // Get the name of custom post type.
               $custom_post_type = $childs[0]->name;
               // Get the posts of custom post type.
               $loop = new WP_Query(array('posts_per_page' => -1, 'post_type' => $custom_post_type, 'orderby' => 'title', 'order' => 'ASC' ));
               
                $label = sprintf('<label class="label-form%s" for="%s">%s%s</label>',
                                bp_get_the_profile_field_is_required()?' required':'',
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_name(),
                                bp_get_the_profile_field_is_required()?' '.__('(required)', 'buddypress'):'');
                
                $options_posts = '';
                foreach ($loop->posts as $post) {
                    $options_posts .= sprintf('<option value="%s"%s>%s</option>',
                                            $post->ID,
                                            (!empty($custom_post_type_selected) &&in_array($post->ID, $custom_post_type_selected))?' selected="selected"':'',
                                            $post->post_title);
                }
                $input = sprintf('<select name="%s[]" id="%s"%s class="select" multiple="multiple"><option value="">%s</option>%s</select>',
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_input_name(),
                                bp_get_the_profile_field_is_required()?' aria-required="true" required="required"':'',
                                __('Select...', 'bxcft'),
                                $options_posts);
                
                echo apply_filters('bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required());
                
                do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );
                
                echo apply_filters('bxcft_field_input', $input, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_is_required());
           } else {
                _e('There is no custom post type selected.', 'bxcft');
           }
        ?>
        </div>    
    <?php
       }
       elseif (bp_get_the_profile_field_type() == 'checkbox_acceptance') {
        ?>
        <div class="checkbox-acceptance">
        <?php
            $label = sprintf('<label class="label-form%s" for="%s">%s%s</label>',
                            bp_get_the_profile_field_is_required()?' required':'',
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_name(),
                            bp_get_the_profile_field_is_required()?' '.__('(required)', 'buddypress'):'');

            $input = sprintf('<input type="checkbox" name="%s" id="%s"%s class="checkbox" value="1"%s />%s',
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_is_required()?' aria-required="true" required="required"':'',
                            bp_get_the_profile_field_edit_value()?' checked="checked"':'',
                            bp_get_the_profile_field_description());

            echo apply_filters('bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required());

            do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );

            echo apply_filters('bxcft_field_input', $input, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_is_required());
        ?>
        </div>
        <script>
            jQuery(function() {
                jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>').parent().parent().find('> p.description').hide().remove();
            });
        </script>
       <?php
       }
       elseif (bp_get_the_profile_field_type() == 'image') {
        ?>
        <div class="input-image">
        <?php
            $label = sprintf('<label class="label-form%s" for="%s">%s%s</label>',
                            bp_get_the_profile_field_is_required()?' required':'',
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_name(),
                            bp_get_the_profile_field_is_required()?' '.__('(required)', 'buddypress'):'');

            if (bp_get_the_profile_field_edit_value() != '') {
                $actual_image = sprintf('<img src="%s" alt="%s" /><label for="%s_deleteimg"><input type="checkbox" name="%s_deleteimg" id="%s_deleteimg" value="1" />%s</label><input type="hidden" name="%s_hiddenimg" id="%s_hiddenimg" value="%s" />',
                                        $uploads['baseurl'].bp_get_the_profile_field_edit_value(),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        __('Check this to delete this image', 'bxcft'),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_edit_value());
            } elseif (bp_get_profile_field_data(array('field' => bp_get_the_profile_field_id())) != '') {
                $actual_file = sprintf('%s<label for="%s_deletefile"><input type="checkbox" name="%s_deleteimg" id="%s_deleteimg" value="1" />%s</label><input type="hidden" name="%s_hiddenimg" id="%s_hiddenimg" value="%s" />',
                                        strip_tags(bp_get_profile_field_data(array('field' => bp_get_the_profile_field_id()))),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        __('Check this to delete this image', 'bxcft'),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        $_POST['field_'.bp_get_the_profile_field_id().'_hiddenimg']);
            } else {
                $actual_image = '';
            }
            $input = sprintf('<input type="hidden" name="%s" id="%s" value="%s" /><input type="file" name="%s" id="%s"%s />',
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_input_name(),
                            (bp_get_the_profile_field_edit_value()!=''&&bp_get_the_profile_field_edit_value()!='-')?bp_get_the_profile_field_edit_value():'-',
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_input_name(),
                            (bp_get_the_profile_field_is_required()&&(bp_get_the_profile_field_edit_value()==''||bp_get_the_profile_field_edit_value()=='-'))?' aria-required="true" required="required"':'');

            echo apply_filters('bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required());

            do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );

            echo apply_filters('bxcft_field_input', $input, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_is_required());
            
            echo apply_filters('bxcft_field_actual_image', $actual_image, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_edit_value());
        ?>
            <script type="text/javascript">
                jQuery('#profile-edit-form').attr('enctype', 'multipart/form-data');
            <?php if (bp_get_the_profile_field_edit_value() != '' && bp_get_the_profile_field_edit_value() != '-'): ?>
                jQuery('#field_<?php echo bp_get_the_profile_field_id(); ?>_deleteimg').change(function() {
                    if (jQuery(this).is(':checked') && jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=file]').val() == '') {
                        jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=hidden]').val('');
                    } else {
                        jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=hidden]').val('<?php echo bp_get_the_profile_field_edit_value(); ?>');
                    }
                });
            <?php endif; ?>
                jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=file]').change(function() {
                    if (jQuery(this).val() != '') {
                        jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=hidden]').val('-');
                    } else {
                        jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=hidden]').val('');
                    }
                });
            </script>
        </div>
       <?php
       }
       elseif (bp_get_the_profile_field_type() == 'file') {
       ?>
        <div class="input-file">
        <?php
            $label = sprintf('<label class="label-form%s" for="%s">%s%s</label>',
                            bp_get_the_profile_field_is_required()?' required':'',
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_name(),
                            bp_get_the_profile_field_is_required()?' '.__('(required)', 'buddypress'):'');

            if (bp_get_the_profile_field_edit_value() != ''
                    && bp_get_the_profile_field_edit_value() != '-') {
                $actual_file = sprintf('%s<label for="%s_deletefile"><input type="checkbox" name="%s_deletefile" id="%s_deletefile" value="1" />%s</label><input type="hidden" name="%s_hiddenfile" id="%s_hiddenfile" value="%s" />',
                                        apply_filters('bxcft_show_download_file_link', '<a href="' . $uploads['baseurl'] . bp_get_the_profile_field_edit_value() . '" title="' . bp_get_the_profile_field_input_name() . '">' . __('Download file', 'bxcft') . '</a>', bp_get_the_profile_field_type(), bp_get_the_profile_field_id(), bp_get_the_profile_field_edit_value()),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        __('Check this to delete this file', 'bxcft'),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_edit_value());
            } elseif (bp_get_profile_field_data(array('field' => bp_get_the_profile_field_id())) != '') {
                $actual_file = sprintf('%s<label for="%s_deletefile"><input type="checkbox" name="%s_deletefile" id="%s_deletefile" value="1" />%s</label><input type="hidden" name="%s_hiddenfile" id="%s_hiddenfile" value="%s" />',
                                        apply_filters('bxcft_show_download_file_link', bp_get_profile_field_data(array('field' => bp_get_the_profile_field_id())), bp_get_the_profile_field_type(), bp_get_the_profile_field_id(), bp_get_the_profile_field_edit_value()),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        __('Check this to delete this file', 'bxcft'),
                                        bp_get_the_profile_field_input_name(),
                                        bp_get_the_profile_field_input_name(),
                                        $_POST['field_'.bp_get_the_profile_field_id().'_hiddenfile']);
            } else {
                $actual_file = '';
            }
            $input = sprintf('<input type="hidden" name="%s" id="%s" value="%s" /><input type="file" name="%s" id="%s"%s />',
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_input_name(),
                            (bp_get_the_profile_field_edit_value()!=''&&bp_get_the_profile_field_edit_value()!='-')?bp_get_the_profile_field_edit_value():'-',
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_input_name(),
                            (bp_get_the_profile_field_is_required()&&(bp_get_the_profile_field_edit_value()==''||bp_get_the_profile_field_edit_value()=='-'))?' aria-required="true" required="required"':'');

            echo apply_filters('bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required());

            do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );

            echo apply_filters('bxcft_field_input', $input, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_is_required());
            
            echo apply_filters('bxcft_field_actual_file', $actual_file, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_edit_value());
        ?>
            <script type="text/javascript">
                jQuery('#profile-edit-form').attr('enctype', 'multipart/form-data');
            <?php if (bp_get_the_profile_field_edit_value() != '' && bp_get_the_profile_field_edit_value() != '-'): ?>
                jQuery('#field_<?php echo bp_get_the_profile_field_id(); ?>_deletefile').change(function() {
                    if (jQuery(this).is(':checked') && jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=file]').val() == '') {
                        jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=hidden]').val('');
                    } else {
                        jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=hidden]').val('<?php echo bp_get_the_profile_field_edit_value(); ?>');
                    }
                });
            <?php endif; ?>
                jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=file]').change(function() {
                    if (jQuery(this).val() != '') {
                        jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=hidden]').val('-');
                    } else {
                        jQuery('input#field_<?php echo bp_get_the_profile_field_id(); ?>[type=hidden]').val('');
                    }
                });
            </script>
        </div>
       <?php
       }
       elseif (bp_get_the_profile_field_type() == 'color') {
           $color_selected = bp_get_the_profile_field_edit_value();
           if (strpos($color_selected, '#') === false) {
               $color_selected = '#'.$color_selected;
           }
       ?>
       <div class="input-color">
        <?php
            $label = sprintf('<label class="label-form%s" for="%s">%s%s</label>',
                            bp_get_the_profile_field_is_required()?' required':'',
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_name(),
                            bp_get_the_profile_field_is_required()?' '.__('(required)', 'buddypress'):'');

            $input = sprintf('<input type="color" name="%s" id="%s"%s class="input" value="%s" />',
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_is_required()?' aria-required="true" required="required"':'',
                            $color_selected);

            echo apply_filters('bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required());

            do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );

            echo apply_filters('bxcft_field_input', $input, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_is_required());
        ?>
       </div>    
        <script>
            if (!Modernizr.inputtypes.color) {
                // No html5 field colorpicker => Calling jscolor.
                jQuery('input#<?php bp_the_profile_field_input_name() ?>').addClass('color');
            }
        </script>
       <?php
       }
       elseif (bp_get_the_profile_field_type() == 'number') {
       ?>
       <div class="input-number">
        <?php
            $label = sprintf('<label class="label-form%s" for="%s">%s%s</label>',
                            bp_get_the_profile_field_is_required()?' required':'',
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_name(),
                            bp_get_the_profile_field_is_required()?' '.__('(required)', 'buddypress'):'');

            $input = sprintf('<input type="number" name="%s" id="%s"%s class="input" value="%s" />',
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_input_name(),
                            bp_get_the_profile_field_is_required()?' aria-required="true" required="required"':'',
                            bp_get_the_profile_field_edit_value());

            echo apply_filters('bxcft_field_label', $label, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_name(), bp_get_the_profile_field_is_required());

            do_action( 'bp_' . bp_get_the_profile_field_input_name() . '_errors' );

            echo apply_filters('bxcft_field_input', $input, bp_get_the_profile_field_id(), bp_get_the_profile_field_type(), bp_get_the_profile_field_input_name(), bp_get_the_profile_field_is_required());
        ?>
       </div>   
       <?php
       }

       $output = ob_get_contents();
   ob_end_clean();

   if ($echo) {
       echo $output;
       return;
   }
   return $output;
}

/*
 * Buddypress v1.7 has better hook bp_custom_profile_edit_fields_pre_visibility which shows fields before visibility settings.
 * In case buddypress previous version is installed, we use the other tag bp_custom_profile_edit_fields.
 */
$version_bp = 0;
$data = get_file_data(WP_PLUGIN_DIR . '/buddypress/bp-loader.php', array('Version'));
if (isset($data) && count($data) > 0 && $data[0] != '') 
    $version_bp = (float)$data[0];
if ($version_bp >= 1.7)
    add_action( 'bp_custom_profile_edit_fields_pre_visibility', 'bxcft_edit_render_new_xprofile_field' );
else 
    add_action( 'bp_custom_profile_edit_fields', 'bxcft_edit_render_new_xprofile_field' );

function bxcft_get_field_value( $value='', $type='', $id='') {
    
    $value_to_return = $value;

    if ($value_to_return == '')
        return apply_filters('bxcft_show_field_value', $value_to_return, $type, $id, $value);
    
    $uploads = wp_upload_dir();
    
    $value = strip_tags($value);
    if ($type == 'birthdate') {
        $field = new BP_XProfile_Field($id);
        // Get children.
        $childs = $field->get_children();
        $show_age = false;
        if (isset($childs) && $childs && count($childs) > 0 && is_object($childs[0])) {
            // Get the name of custom post type.
            if ($childs[0]->name == 'show_age') 
                $show_age = true;
        }
        if ($value != '') {
            if ($show_age) {
                $value_to_return = floor((time() - strtotime($value))/31556926);
            } else {
                $value_to_return = date_i18n(get_option('date_format') ,strtotime($value) );
            }
        }
    }
    elseif ($type == 'datepicker') {
        $value_to_return = date_i18n(get_option('date_format') ,strtotime($value) );
    }
    elseif ($type == 'email') {
        if (strpos($value, 'mailto') === false) {
            $value_to_return = '<a href="mailto:'.$value.'">'.$value.'</a>';
        }
    }
    elseif ($type == 'web') {
        if (strpos($value, 'href=') === false) {
            $value_to_return = '<a href="'.$value.'">'.$value.'</a>';
        }
    }
    elseif ($type == 'select_custom_post_type') {
        // Get field's data.
        $field = new BP_XProfile_Field($id);
        // Get children.
        $childs = $field->get_children();
        if (isset($childs) && count($childs) > 0) {
            // Get the name of custom post type.
            $custom_post_type = $childs[0]->name;
        }
        $post = get_post($value);
        if ($post->post_type == $custom_post_type) {
            $value_to_return = $post->post_title;
        } else {
            // Custom post type is not the same.
            $value_to_return = '--';
        }
    }
    elseif ($type == 'multiselect_custom_post_type') {
        // Get field's data.
        $field = new BP_XProfile_Field($id);
        // Get children.
        $childs = $field->get_children();
        if (isset($childs) && count($childs) > 0 && is_object($childs[0])) {
            // Get the name of custom post type.
            $custom_post_type = $childs[0]->name;
        } else {
            $custom_post_type = '';
        }
        $values = explode(",", $value);
        $cad = '';
        foreach ($values as $v) {
            $post = get_post($v);
            if ($post->post_type == $custom_post_type) {
                if ($cad == '')
                    $cad .= '<ul class="list_custom_post_type">';
                $cad .= '<li>'.$post->post_title.'</li>';
            }
        }
        if ($cad != '') {
            $cad .= '</ul>';
            $value_to_return = $cad;
        } else {
            $value_to_return = '--';
        }
    } elseif ($type == 'checkbox_acceptance') {
        $value_to_return = (((int)$value==1)?__('yes', 'bxcft'):__('no', 'bxcft'));
    } elseif ($type == 'image') {
        if (strpos($value, $uploads['baseurl']) === false) {
            $value = $uploads['baseurl'].$value;
        } else {
            $value_to_return = $value;
        }
        $value_to_return = '<img src="'.$value.'" alt="" />';
    } elseif ($type == 'file') {
        if (strpos($value, $uploads['baseurl']) === false) {
            $value = $uploads['baseurl'].$value;
        }
        $value_to_return = '<a href="'.$value.'">'.__('Download file', 'bxcft').'</a>';
    } elseif ($type == 'color') {
        if (strpos($value, '#') === false) {
            $value = '#'.$value;
        }
        $value_to_return = $value;
    }
    // Number nothing to do.
    
    return apply_filters('bxcft_show_field_value', $value_to_return, $type, $id, $value);
}
add_filter( 'bp_get_the_profile_field_value', 'bxcft_get_field_value', 15, 3);

/**
 * Filter for those who use xprofile_get_field_data instead of get_field_value.
 * @param type $value
 * @param type $field_id
 * @param type $user_id
 * @return string
 */
function bxcft_get_field_data($value, $field_id) {
    
    $value_to_return = $value;
    $field = new BP_XProfile_Field($field_id);

    if ($value_to_return == '')
        return apply_filters('bxcft_show_field_value', $value_to_return, $field->type, $field_id, $value);
    
    $uploads = wp_upload_dir();
    
    $value = strip_tags($value);
    if ($field->type == 'birthdate') {
        // Get children.
        $childs = $field->get_children();
        $show_age = false;
        if (isset($childs) && $childs && count($childs) > 0 && is_object($childs[0])) {
            if ($childs[0]->name == 'show_age') 
                $show_age = true;
        }
        if ($value != '') {
            if ($show_age) {
                $value_to_return = floor((time() - strtotime($value))/31556926);
            } else {
                $value_to_return = date_i18n(get_option('date_format') ,strtotime($value) );
            }
        }
    }
    elseif ($field->type == 'datepicker') {
        $value_to_return = date_i18n(get_option('date_format') ,strtotime($value) );
    }
    elseif ($field->type == 'email') {
        if (strpos($value, 'mailto') === false) {
            $value_to_return = '<a href="mailto:'.$value.'">'.$value.'</a>';
        }
    }
    elseif ($field->type == 'web') {
        if (strpos($value, 'href=') === false) {
            $value_to_return = '<a href="'.$value.'">'.$value.'</a>';      
        }
    }
    elseif ($field->type == 'select_custom_post_type') {
        // Get children.
        $childs = $field->get_children();
        if (isset($childs) && count($childs) > 0) {
            // Get the name of custom post type.
            $custom_post_type = $childs[0]->name;
        }
        $post = get_post($value);
        if ($post->post_type == $custom_post_type) {
            $value_to_return = $post->post_title;
        } else {
            // Custom post type is not the same.
            $value_to_return = '--';
        }
    }
    elseif ($field->type == 'multiselect_custom_post_type') {
        // Get children.
        $childs = $field->get_children();
        $values = explode(",", $value);
        $cad = '';
        if (isset($childs) && is_array($childs) && count($childs) > 0) {
            // Get the name of custom post type.
            $custom_post_type = $childs[0]->name;
            
            foreach ($values as $v) {
                $post = get_post($v);
                if ($post->post_type == $custom_post_type) {
                    if ($cad == '')
                        $cad .= '<ul class="list_custom_post_type">';
                    $cad .= '<li>'.$post->post_title.'</li>';
                }
            }
        }
        if ($cad != '') {
            $cad .= '</ul>';
            $value_to_return = $cad;
        } else {
            $value_to_return = '--';
        }
    } elseif ($field->type == 'checkbox_acceptance') {
        $value = strip_tags($value);
        $value_to_return = (((int)$value==1)?__('yes', 'bxcft'):__('no', 'bxcft'));
    } elseif ($field->type == 'image') {
        if (strpos($value, $uploads['baseurl']) === false) {
            $value = $uploads['baseurl'].$value;
        } else {
            $value_to_return = $value;
        }
        $value_to_return = '<img src="'.$value.'" alt="" />';
    } elseif ($field->type == 'file') {
        if (strpos($value, $uploads['baseurl']) === false) {
            $value = $uploads['baseurl'].$value;
        }
        $value_to_return = '<a href="'.$value.'">'.__('Download file', 'bxcft').'</a>';
    } elseif ($field->type == 'color') {
        if (strpos($value, '#') === false) {
            $value = '#'.$value;
        }
        $value_to_return = $value;
    }
    // Number nothing to do.
    
    return apply_filters('bxcft_show_field_value', $value_to_return, $field->type, $field_id, $value);
    
}
add_filter( 'xprofile_get_field_data', 'bxcft_get_field_data', 15, 2);

/**
 * Function replacing the original buddypress filter.
 * @param type $field_value
 * @param type $field_type
 * @return string
 */
function bxcft_xprofile_filter_link_profile_data( $field_value, $field_type = 'textbox' ) {
	if ( 'datebox' == $field_type || 'email' == $field_type || 'birthdate' == $field_type ||
            'datepicker' == $field_type || 'web' == $field_type || 'select_custom_post_type' == $field_type ||
            'multiselect_custom_post_type' == $field_type || 'image' == $field_type || 'file' == $field_type ||
            'color' == $field_type || 'number' == $field_type)
		return $field_value;

	if ( !strpos( $field_value, ',' ) && ( count( explode( ' ', $field_value ) ) > 5 ) )
		return $field_value;

	$values = explode( ',', $field_value );

	if ( !empty( $values ) ) {
		foreach ( (array) $values as $value ) {
			$value = trim( $value );

			// If the value is a URL, skip it and just make it clickable.
			if ( preg_match( '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', $value ) ) {
				$new_values[] = make_clickable( $value );

			// Is not clickable
			} else {

				// More than 5 spaces
				if ( count( explode( ' ', $value ) ) > 5 ) {
					$new_values[] = $value;

				// Less than 5 spaces
				} else {
					$search_url   = add_query_arg( array( 's' => urlencode( $value ) ), bp_get_members_directory_permalink() );
					$new_values[] = '<a href="' . $search_url . '" rel="nofollow">' . $value . '</a>';
				}
			}
		}

		$values = implode( ', ', $new_values );
	}

	return $values;
}

/**
 * Adding js for admin.
 * @param type $hook
 * @return type
 */
function bxcft_add_js($hook) {    
    if ('users_page_bp-profile-setup' != $hook && 'buddypress_page_bp-profile-setup' != $hook)
       return;

    wp_enqueue_script( 'bxcft-js', plugins_url('assets/js/addfields.js', __FILE__), array( 'jquery' ), '1.5.7' );
    $params = array(
        'birthdate' => __('Birthdate', 'bxcft'),
        'email' => __('Email', 'bxcft'),
        'web' => __('Website', 'bxcft'),
        'datepicker' => __('Datepicker', 'bxcft'),
        'select_custom_post_type' => __('Custom Post Type Selector', 'bxcft'),
        'multiselect_custom_post_type' => __('Custom Post Type Multiselector', 'bxcft'),
        'checkbox_acceptance' => __('Checkbox acceptance', 'bxcft'),
        'image' => __('Image', 'bxcft'),
        'file' => __('File', 'bxcft'),
        'color' => __('Color', 'bxcft'),
        'number' => __('Number', 'bxcft')
    );
    wp_localize_script('bxcft-js', 'params', $params);
}
add_action( 'admin_enqueue_scripts', 'bxcft_add_js');

/**
 * Adding js for edit form.
 */
function bxcft_add_js_public() {
    // Modernizr to test html5 fields.
    wp_enqueue_script( 'bxcft-modernizr', plugins_url('assets/js/modernizr.js', __FILE__), array( 'jquery' ), '2.6.2' );
    // Plugin jscolor fallback colorpicker.
    wp_enqueue_script( 'bxcft-jscolor', plugins_url('assets/js/jscolor/jscolor.js', __FILE__), array( 'bxcft-modernizr' ), '1.4.1' );
}
add_action( 'wp_enqueue_scripts', 'bxcft_add_js_public');

function bxcft_save_custom_option($field) {
    global $bp, $wpdb;
    
    if ( 'select_custom_post_type' == $field->type ) {
        $post_option  = !empty( $_POST['select_custom_post_type_option']) ? $_POST['select_custom_post_type_option'] : '';
        if ( '' != $post_option ) {
            if ( !empty( $field->id ) ) {
				$field_id = $field->id;
			} else {
				$field_id = $wpdb->insert_id;
			}
            if ( !$wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->profile->table_name_fields} (group_id, parent_id, type, name, description, is_required, option_order, is_default_option) VALUES (%d, %d, 'custom_post_type_option', %s, '', 0, %d, %d)", $field->group_id, $field_id, $post_option, 1, 1 ) ) ) {
                return false;
            }
        }
    }
    elseif ( 'multiselect_custom_post_type' == $field->type) {
        $post_option  = !empty( $_POST['multiselect_custom_post_type_option']) ? $_POST['multiselect_custom_post_type_option'] : '';
        if ( '' != $post_option ) {
            if ( !empty( $field->id ) ) {
				$field_id = $field->id;
			} else {
				$field_id = $wpdb->insert_id;
			}
            if ( !$wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->profile->table_name_fields} (group_id, parent_id, type, name, description, is_required, option_order, is_default_option) VALUES (%d, %d, 'multi_custom_post_type_option', %s, '', 0, %d, %d)", $field->group_id, $field_id, $post_option, 1, 1 ) ) ) {
                return false;
            }
        }
    }
    elseif ( 'birthdate' == $field->type) {
        $post_option  = !empty( $_POST['birthdate_option']) ? $_POST['birthdate_option'] : '';
        if ( '' != $post_option ) {
            if ( !empty( $field->id ) ) {
				$field_id = $field->id;
			} else {
				$field_id = $wpdb->insert_id;
			}
            if ( !$wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->profile->table_name_fields} (group_id, parent_id, type, name, description, is_required, option_order, is_default_option) VALUES (%d, %d, 'birthdate_option', %s, '', 0, %d, %d)", $field->group_id, $field_id, $post_option, 1, 1 ) ) ) {
                return false;
            }
        }
    }
        
}
add_action( 'xprofile_field_after_save', 'bxcft_save_custom_option');

function bxcft_delete_field_custom($field) {
    if ($field->type == 'select_custom_post_type' || $field->type == 'multiselect_custom_post_type'
             || $field->type == 'birthdate') {
        $field->delete_children();
    }
}
add_action( 'xprofile_fields_deleted_field', 'bxcft_delete_field_custom');

function bxcft_selected_field($field) {
    $childs = $field->get_children();
    if (isset($childs) && count($childs) > 0 && is_object($childs[0]) && $field->type == 'select_custom_post_type') {
        $selected_option = $childs[0]->name;
    } else {
        $selected_option = null;
    }
    
    if (!empty($_POST['select_custom_post_type_option'])) {
        $selected_option = $_POST['select_custom_post_type_option'];
    }
?>
<div style="display: none; margin-left: 15px;" class="options-box" id="select_custom_post_type">
<h4><?php _e('Please, select custom post type', 'bxcft'); ?></h4>
<p>
    <?php _e('Custom Post Type:', 'bxcft'); ?>
    <select name="select_custom_post_type_option" id="select_custom_post_type_option">
<?php
    $args = array(
        'public'   => true,
        '_builtin' => false
    ); 
    $custom_post_types = get_post_types($args);
    foreach ($custom_post_types as $post_type) :
?>
        <option value="<?php echo $post_type; ?>" <?php if ($selected_option == $post_type): ?>selected="selected"<?php endif; ?>>
            <?php echo $post_type; ?>
        </option>
<?php endforeach; ?>
    </select>
</p>
</div>
<?php
    if (isset($childs) && count($childs) > 0 && is_object($childs[0]) && $field->type == 'multiselect_custom_post_type') {
        $selected_option = $childs[0]->name;
    } else {
        $selected_option = null;
    }
    
    if (!empty($_POST['multiselect_custom_post_type_option'])) {
        $selected_option = $_POST['multiselect_custom_post_type_option'];
    }
?>
<div style="display: none; margin-left: 15px;" class="options-box" id="multiselect_custom_post_type">
<h4><?php _e('Please, select custom post type', 'bxcft'); ?></h4>
<p>
    <?php _e('Custom Post Type:', 'bxcft'); ?>
    <select name="multiselect_custom_post_type_option" id="multiselect_custom_post_type_option">
<?php
    foreach ($custom_post_types as $post_type) :
?>
        <option value="<?php echo $post_type; ?>" <?php if ($selected_option == $post_type): ?>selected="selected"<?php endif; ?>>
            <?php echo $post_type; ?>
        </option>
<?php endforeach; ?>
    </select>
</p>
</div>     
<?php
    if (isset($childs) && $childs && count($childs) > 0 && is_object($childs[0]) && $field->type == 'birthdate') {
        $selected_option = $childs[0]->name;
    } else {
        $selected_option = null;
    }
    
    if (!empty($_POST['birthdate_option'])) {
        $selected_option = $_POST['birthdate_option'];
    }
?>
<div style="display: none; margin-left: 15px;" class="options-box" id="birthdate">
<h4><?php _e('Show age (hide birthdate)', 'bxcft'); ?></h4>
<p>
    <?php _e('Check this if you want to show age instead of birthdate:', 'bxcft'); ?>
    <input type="checkbox" name="birthdate_option" value="show_age" id="birthdate_option"
           <?php if ($selected_option == 'show_age') : ?>checked="checked"<?php endif; ?>/>
</p>
</div>
<?php if (!is_null($field->type)) : ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        bxcft.select('<?php echo $field->type; ?>');
    });    
</script>
<?php
    endif;
}
add_action('xprofile_field_additional_options', 'bxcft_selected_field');

/**
 * This filter add a new level of visibility. Hide this field for everyone. Nobody can see.
 * @param type $visibility_levels
 * @return type
 */
function bxcft_xprofile_get_visibility_levels($visibility_levels) {
    $visibility_levels['nobody'] = array('id' => 'nobody',  'label' => __('Nobody', 'bxcft'));
    return $visibility_levels;
}
add_filter('bp_xprofile_get_visibility_levels', 'bxcft_xprofile_get_visibility_levels', 1);

/**
 * This filter is necessary to hide fields when 'nobody' new visibility is selected.
 * @param type $hidden_fields
 * @param type $displayed_user_id
 * @param type $current_user_id
 * @return type
 */
function bxcft_xprofile_get_hidden_fields_for_user($hidden_fields, $displayed_user_id=0, $current_user_id=0) {
    if ( !$displayed_user_id ) {
		$displayed_user_id = bp_displayed_user_id();
	}

	if ( !$displayed_user_id ) {
		return array();
	}

	if ( !$current_user_id ) {
		$current_user_id = bp_loggedin_user_id();
	}

	// @todo - This is where you'd swap out for current_user_can() checks
    $new_hidden_fields = array();

	if ( $current_user_id ) {
		// Current user is logged in
		if ( $displayed_user_id == $current_user_id ) {
			// If you're viewing your own profile, nothing's private

		} elseif ( bp_is_active( 'friends' ) && friends_check_friendship( $displayed_user_id, $current_user_id ) ) {
			// If the current user and displayed user are friends, so exclude nobody
            $hidden_levels[] = 'nobody';
			$new_hidden_fields = bp_xprofile_get_fields_by_visibility_levels( $displayed_user_id, $hidden_levels );

		} else {
			// current user is logged-in but not friends, so exclude friends-only
			$hidden_levels[] = 'nobody';
            
			$new_hidden_fields = bp_xprofile_get_fields_by_visibility_levels( $displayed_user_id, $hidden_levels );
		}

	} else {
		// Current user is not logged in, so exclude friends-only and loggedin
		$hidden_levels[] = 'nobody';
        
		$new_hidden_fields = bp_xprofile_get_fields_by_visibility_levels( $displayed_user_id, $hidden_levels );
	}
    
    if (is_array($new_hidden_fields))
        $hidden_fields = array_merge($hidden_fields, $new_hidden_fields);
    
    return $hidden_fields;
}
add_filter('bp_xprofile_get_hidden_fields_for_user', 'bxcft_xprofile_get_hidden_fields_for_user', 10, 3);

function bxcft_xprofile_data_before_save($data) {
    $field_id = $data->field_id;
    $field = new BP_XProfile_Field($field_id);

    if ($field->type == 'image' || $field->type == 'file') {
        
        $uploads = wp_upload_dir();
            
        // Handles image field type saving.
        if (isset($_FILES['field_'.$field_id]) && $_FILES['field_'.$field_id]['size'] > 0) {
            $ext = strtolower(substr($_FILES['field_'.$field_id]['name'], strrpos($_FILES['field_'.$field_id]['name'],'.')+1));
            if ($field->type == 'image') {
                $ext_allowed = array('jpg','jpeg','gif','png');
                apply_filters('images_ext_allowed', $ext_allowed);
                if (!in_array($ext, $ext_allowed)) {
                    bp_core_add_message( __('Image type not allowed: (jpg, jpeg, gif, png).', 'bxcft'), 'error' );
                    $data->field_id = 0;
                } else {
                    // Delete previous image.
                    if (file_exists($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenimg'])) {
                        unlink($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenimg']);
                    }
                }
            } elseif ($field->type == 'file') {
                $ext_allowed = array('doc','docx','pdf');
                apply_filters('files_ext_allowed', $ext_allowed);
                if (!in_array($ext, $ext_allowed)) {
                    bp_core_add_message( __('File type not allowed: (doc, docx, pdf).', 'bxcft'), 'error' );
                    $data->field_id = 0;
                } else {
                    // Delete previous file.
                    if (isset($_POST['field_'.$field_id.'_hiddenfile'])     &&
                        !empty($_POST['field_'.$field_id.'_hiddenfile'])    && 
                        file_exists($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenfile'])) {
                        unlink($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenfile']);
                    }
                }
            }

            if (in_array($ext, $ext_allowed)) {
                require_once( ABSPATH . '/wp-admin/includes/file.php' );
                global $bxcft_user_id;
                $bxcft_user_id = $data->user_id;
                add_filter( 'upload_dir', 'bxcft_profile_upload_dir', 10, 0 );
                $_POST['action'] = 'wp_handle_upload';
                $uploaded_file = wp_handle_upload( $_FILES['field_'.$field_id] );
                remove_filter('upload_dir', 'bxcft_profile_upload_dir');
                $value = str_replace($uploads['baseurl'], '', $uploaded_file['url']);
            }
        } else {  
            // Handles delete checkbox.
            if ($field->type == 'image' && isset($_POST['field_'.$field_id.'_deleteimg']) && $_POST['field_'.$field_id.'_deleteimg']) {
                if (isset($_POST['field_'.$field_id.'_hiddenimg'])      &&
                        !empty($_POST['field_'.$field_id.'_hiddenimg'])     && 
                        file_exists($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenimg'])) {
                    unlink($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenimg']);
                } 
                $value = array();
            } elseif ($field->type == 'image') {
                $value = $_POST['field_'.$field_id.'_hiddenimg'];
            }
            
            if ($field->type == 'file' && isset($_POST['field_'.$field_id.'_deletefile']) && $_POST['field_'.$field_id.'_deletefile']) {
                if (isset($_POST['field_'.$field_id.'_hiddenfile'])     &&
                        !empty($_POST['field_'.$field_id.'_hiddenfile'])    && 
                        file_exists($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenfile'])) {
                    unlink($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenfile']);
                } 
                $value = array();
            } elseif ($field->type == 'file') {
                $value = $_POST['field_'.$field_id.'_hiddenfile'];
            }
        }
        
        $data->value = $value;
    }
}
add_action('xprofile_data_before_save', 'bxcft_xprofile_data_before_save');

function bxcft_xprofile_data_after_delete($data) 
{
    $field_id = $data->field_id;
    $field = new BP_XProfile_Field($field_id);
    $uploads = wp_upload_dir();
    // Handles delete checkbox.
    if ($field->type == 'image' && isset($_POST['field_'.$field_id.'_deleteimg']) && $_POST['field_'.$field_id.'_deleteimg']) {
        if (file_exists($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenimg'])) {
            unlink($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenimg']);
        } 
    }

    if ($field->type == 'file' && isset($_POST['field_'.$field_id.'_deletefile']) && $_POST['field_'.$field_id.'_deletefile']) {
        if (file_exists($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenfile'])) {
            unlink($uploads['basedir'] . $_POST['field_'.$field_id.'_hiddenfile']);
        } 
    }
}
add_action('xprofile_data_after_delete', 'bxcft_xprofile_data_after_delete');

function bxcft_signup_validate() {
    global $bp;
    if ( bp_is_active( 'xprofile' ) ) {
        if ( isset( $_POST['signup_profile_field_ids'] ) && !empty( $_POST['signup_profile_field_ids'] ) ) {
            // Let's compact any profile field info into an array
            $profile_field_ids = explode( ',', $_POST['signup_profile_field_ids'] );
            foreach ($profile_field_ids as $field_id) {
                $field = new BP_XProfile_Field($field_id);
                if ($field->type == 'image' || $field->type == 'file'
                        && isset($_FILES['field_'.$field_id])) {
                    // Delete required field error.
                    unset($bp->signup->errors['field_'.$field_id]);

                    // Check extensions.
                    $ext = strtolower(substr($_FILES['field_'.$field_id]['name'], strrpos($_FILES['field_'.$field_id]['name'],'.')+1));
                    if ($field->type == 'image') {
                        $ext_allowed = array('jpg','jpeg','gif','png');
                        apply_filters('images_ext_allowed', $ext_allowed);
                        if (!in_array($ext, $ext_allowed)) {
                            $bp->signup->errors['field_'.$field_id] = __('Image type not allowed: (jpg, jpeg, gif, png).', 'bxcft');
                        }
                    } elseif ($field->type == 'file') {
                        $ext_allowed = array('doc','docx','pdf');
                        apply_filters('files_ext_allowed', $ext_allowed);
                        if (!in_array($ext, $ext_allowed)) {
                            $bp->signup->errors['field_'.$field_id] = __('File type not allowed: (doc, docx, pdf).', 'bxcft');
                        }
                    }
                }
            }
        }
    }
}
add_action('bp_signup_validate', 'bxcft_signup_validate');

function bxcft_profile_upload_dir( $user_id=0 ) {  
    global $bxcft_user_id;
    if ($user_id == 0 && empty($bxcft_user_id))
        $bxcft_user_id = bp_displayed_user_id();
    $profile_subdir = '/profiles/' . $bxcft_user_id;
    
    $upload_dir = array(
        'path'    => bp_core_get_upload_dir().$profile_subdir,
        'url'     => bp_core_get_upload_dir('url').$profile_subdir,
        'subdir'  => bp_core_get_upload_dir().$profile_subdir,
        'basedir' => bp_core_get_upload_dir().$profile_subdir,
        'baseurl' => bp_core_get_upload_dir('url').$profile_subdir,
        'error'   => false,
    );
    
    return $upload_dir;
}

/**
 * Replacing the buddypress filter link profile is it has the filter.
 * If user deactivated the filter, we don't add another filter.
 */
function bxcft_remove_xprofile_links() {
    if (has_filter('bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data')) {
        remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2 );
        add_filter( 'bp_get_the_profile_field_value', 'bxcft_xprofile_filter_link_profile_data', 9, 2);
    }
}
add_action( 'bp_setup_globals', 'bxcft_remove_xprofile_links', 9999 );

/**
 * This function only works if plugin bp-profile search is active.
 * @global type $bps_options
 * @param type $type
 * @return string
 */
function bxcft_profile_field_type($type) {
    if (function_exists('is_plugin_active') && is_plugin_active('bp-profile-search/bps-main.php')
            || in_array( 'bp-profile-search/bps-main.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
        global $bps_options;
        $id = bp_get_the_profile_field_id ();

        if ( ((is_admin() && strpos($_SERVER['REQUEST_URI'], 'page=bp-profile-search'))
                || (isset($bps_options) && $bps_options['agerange'] == $id 
                        && isset($_POST['bp_profile_search']) && $_POST['bp_profile_search']))
                && $type == 'birthdate') {
            return 'datebox';
        }
    }
    
    return $type;
}
add_filter('bp_the_profile_field_type', 'bxcft_profile_field_type', 1);
