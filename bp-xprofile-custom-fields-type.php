<?php
/*
    Plugin Name: Buddypress Xprofile Custom Fields Type
    Description: Buddypress installation required!! Add more custom fields type to extended profiles in buddypress: Birthdate, Email, Web, Datepicker. If you need more fields type, you are free to add them yourself or request us at info@atallos.com.
    Version: 1.4.1
    Author: Atallos Cloud
    Author URI: http://www.atallos.com/
    Plugin URI: http://www.atallos.com/portfolio/buddypress-xprofile-custom-fields-type/
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
       'select_custom_post_type', 'multiselect_custom_post_type');
   $field_types = array_merge($field_types, $new_field_types);
   return $field_types;
}
add_filter( 'xprofile_field_types', 'bxcft_add_new_xprofile_field_type' );

function bxcft_admin_render_new_xprofile_field_type($field, $echo = true) {
   switch ( $field->type ) {
       case 'select_custom_post_type':
           $childs = $field->get_children();
           if (isset($childs) && count($childs) > 0) {
               // Get the name of custom post type.
               $custom_post_type = $childs[0]->name;
               // Get the posts of custom post type.
               $loop = new WP_Query(array('post_type' => $custom_post_type));
           }
           $select_custom_post_type = BP_XProfile_ProfileData::get_value_byid( $field->id );
           $html .= '<select name="field_'.$field->id.'" id="field_'.$field->id.'">';
           foreach ($loop->posts as $post) {
                $html .= '<option value="'.$post->ID.'">'.$post->post_title.'</option>';
           }
           $html .= '</select>';
           break;
           
       case "multiselect_custom_post_type":
           $childs = $field->get_children();
           if (isset($childs) && count($childs) > 0) {
               // Get the name of custom post type.
               $custom_post_type = $childs[0]->name;
               // Get the posts of custom post type.
               $loop = new WP_Query(array('post_type' => $custom_post_type));
           }
           $select_custom_post_type = BP_XProfile_ProfileData::get_value_byid( $field->id );
           $html .= '<select name="field_'.$field->id.'" id="field_'.$field->id.'" multiple="multiple">';
           foreach ($loop->posts as $post) {
                $html .= '<option value="'.$post->ID.'">'.$post->post_title.'</option>';
           }
           $html .= '</select>';
           break;
       
       case "datepicker":
           $datepicker = BP_XProfile_ProfileData::get_value_byid( $field->id );
           $html .= '<input type="date" name="field_'.$field->id.'" id'.$field->id.'" class="input" value="" />';
           break;
       
       case "web":
           $web = BP_XProfile_ProfileData::get_value_byid( $field->id );
           $html .= '<input type="url" name="field_'.$field->id.'" id'.$field->id.'" class="input" placeholder="'.__('http://yourwebsite.com', 'bxcft').'" value="" />';
           break;
       
       case "email":
           $email = BP_XProfile_ProfileData::get_value_byid( $field->id );
           $html .= '<input type="email" name="field_'.$field->id.'" id'.$field->id.'" class="input" placeholder="'.__('example@mail.com', 'bxcft').'" value="" />';
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

           // Día.
           $html .= '<select name="field_'.$field->id.'_day" id="field_'.$field->id.'_day">';
           $html .= '<option value=""' . selected( $day, '', false ) . '>--</option>';
           for ( $i = 1; $i < 32; ++$i ) {
               $html .= '<option value="' . $i .'"' . selected( $day, $i, false ) . '>' . $i . '</option>';
           }
           $html .= '</select>';

           // Mes.
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

           // Año.
           $html .= '<select name="field_'.$field->id.'_year" id="field_'.$field->id.'_year">';
           $html .= '<option value=""' . selected( $year, '', false ) . '>----</option>';
           for ( $i = date('Y')-1; $i > 1901; $i-- ) {
               $html .= '<option value="' . $i .'"' . selected( $year, $i, false ) . '>' . $i . '</option>';
           }
           $html .= '</select>';
           break;

       default:
           $html = "<p>".__('Field type unrecognized', 'cc')."</p>";
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
   if(empty ($echo)){
       $echo = true;
   }

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
               <label class="label-form" for="<?php bp_the_profile_field_input_name(); ?>_day"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?> *<?php endif; ?></label>

               <select name="<?php bp_the_profile_field_input_name(); ?>_day" id="<?php bp_the_profile_field_input_name(); ?>_day" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>
                   <option value=""<?=selected( $day, '', false )?>>--</option>
               <?php
                   for ( $i = 1; $i < 32; ++$i ) {
                       echo '<option value="' . $i .'"' . selected( $day, $i, false ) . '>' . $i . '</option>';
                   } 
               ?>

               </select>

               <select name="<?php bp_the_profile_field_input_name() ?>_month" id="<?php bp_the_profile_field_input_name(); ?>_month" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>
               <?php
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
               ?>
               <option value=""<?=selected( $month, '', false )?>>------</option>
               <?php
                   for ( $i = 0; $i < 12; ++$i ) {
                       echo '<option value="' . $eng_months[$i] . '"' . selected( $month, $eng_months[$i], false ) . '>' . $months[$i] . '</option>';
                   }
               ?>
               </select>
               <?php
               $birthdate_start_year = date('Y')-1;
               ?>               

               <select name="<?php bp_the_profile_field_input_name() ?>_year" id="<?php bp_the_profile_field_input_name(); ?>_year" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true" required="required"<?php endif; ?>>
               <option value=""<?=selected( $year, '', false )?>>----</option>
               <?php
                   for ( $i = $birthdate_start_year; $i > 1901; $i-- ) {
                       echo '<option value="' . $i .'"' . selected( $year, $i, false ) . '>' . $i . '</option>';
                   }
               ?>
               </select>
           </div>
       <?php
       } 
       elseif ( bp_get_the_profile_field_type() == 'email' ) {
       ?>
        <div class="input-email">
            <label class="label-form" for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?> *<?php endif; ?></label>
            <input type="email" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true" required="required"<?php endif; ?> class="input" value="<?php bp_the_profile_field_edit_value() ?>" placeholder="<?php _e('example@mail.com', 'bxcft'); ?>" />
       </div>
       <?php
       } 
       elseif ( bp_get_the_profile_field_type() == 'web' ) {
       ?>
        <div class="input-web">
            <label class="label-form" for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?> *<?php endif; ?></label>
            <input type="url" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true" required="required"<?php endif; ?> class="input" value="<?php bp_the_profile_field_edit_value() ?>" placeholder="<?php _e('http://yourwebsite.com', 'bxcft'); ?>" />
       </div>
       <?php
       }
       elseif ( bp_get_the_profile_field_type() == 'datepicker' ) {
       ?>
        <div class="input-web">
            <label class="label-form" for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?> *<?php endif; ?></label>
            <input type="date" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true" required="required"<?php endif; ?> class="input" value="<?php bp_the_profile_field_edit_value() ?>" />
       </div>
       <?php
       }
       elseif ( bp_get_the_profile_field_type() == 'select_custom_post_type' ) {
           $custom_post_type_selected = BP_XProfile_ProfileData::get_value_byid( $field->id );
           if ( isset( $_POST['field_' . $field->id] ) && $_POST['field_' . $field->id] != $option_value ) {
                if ( !empty( $_POST['field_' . $field->id] ) ) {
                    $custom_post_type_selected = $_POST['field_' . $field->id];
                }
           }
           // Get field's data.
           $field_data = new BP_XProfile_Field($field->id);
           // Get the childs of field
           $childs = $field_data->get_children();
           if (isset($childs) && count($childs) > 0) {
               // Get the name of custom post type.
               $custom_post_type = $childs[0]->name;
               // Get the posts of custom post type.
               $loop = new WP_Query(array('post_type' => $custom_post_type));
       ?>
       <label class="label-form" for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?> *<?php endif; ?></label>
       <select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true" required="required"<?php endif; ?> class="select">
           <option value=""><?php _e('Select...', 'bxcft'); ?></option>
       <?php foreach ($loop->posts as $post): ?>
           <option value="<?php echo $post->ID; ?>" <?php if ($custom_post_type_selected == $post->ID): ?>selected="selected"<?php endif; ?>><?php echo $post->post_title; ?></option>
       <?php endforeach; ?>
       </select>
       <?php
           }
       }
       elseif ( bp_get_the_profile_field_type() == 'multiselect_custom_post_type' ) {
           $custom_post_type_selected = maybe_unserialize(BP_XProfile_ProfileData::get_value_byid( $field->id ));
           if ( isset( $_POST['field_' . $field->id] ) && $_POST['field_' . $field->id] != $option_value ) {
                if ( !empty( $_POST['field_' . $field->id] ) ) {
                    $custom_post_type_selected = $_POST['field_' . $field->id];
                }
           }
           // Get field's data.
           $field_data = new BP_XProfile_Field($field->id);
           // Get the childs of field
           $childs = $field_data->get_children();
           if (isset($childs) && count($childs) > 0) {
               // Get the name of custom post type.
               $custom_post_type = $childs[0]->name;
               // Get the posts of custom post type.
               $loop = new WP_Query(array('post_type' => $custom_post_type));
       ?>
       <label class="label-form" for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?> *<?php endif; ?></label>
       <select name="<?php bp_the_profile_field_input_name(); ?>[]" id="<?php bp_the_profile_field_input_name(); ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true" required="required"<?php endif; ?> class="select" multiple="multiple">
       <?php foreach ($loop->posts as $post): ?>
           <option value="<?php echo $post->ID; ?>" <?php if (in_array($post->ID, $custom_post_type_selected)): ?>selected="selected"<?php endif; ?>><?php echo $post->post_title; ?></option>
       <?php endforeach; ?>
       </select>
       <?php
           }
       }

       $output = ob_get_contents();
   ob_end_clean();

   if($echo){
       echo $output;
       return;
   }
   else{
       return $output;
   }

}
add_action( 'bp_custom_profile_edit_fields', 'bxcft_edit_render_new_xprofile_field' );

function bxcft_get_field_value( $value='', $type='', $id='') {

    if ($type == 'birthdate') {
        $value = str_replace("<p>", "", $value);
        $value = str_replace("</p>", "", $value);
        $field = new BP_XProfile_Field($id);
        // Get children.
        $childs = $field->get_children();
        $show_age = false;
        if (isset($childs) && count($childs) > 0) {
            // Get the name of custom post type.
            if ($childs[0]->name == 'show_age') 
                $show_age = true;
        }
        if ($show_age) {
            return '<p>'.floor((time() - strtotime($value))/31556926).'</p>';
        }
        return '<p>'.date_i18n(get_option('date_format') ,strtotime($value) ).'</p>';
    }
    elseif ($type == 'datepicker') {
        $value = str_replace("<p>", "", $value);
        $value = str_replace("</p>", "", $value);
        return '<p>'.date_i18n(get_option('date_format') ,strtotime($value) ).'</p>';
    }
    elseif ($type == 'email') {
        if (strpos($value, 'mailto') === false) {
            $value = str_replace("<p>", "", $value);
            $value = str_replace("</p>", "", $value);
            return '<p><a href="mailto:'.$value.'">'.$value.'</a></p>';
        }
    }
    elseif ($type == 'web') {
        if (strpos($value, 'href=') === false) {
            $value = str_replace("<p>", "", $value);
            $value = str_replace("</p>", "", $value);
            return '<p><a href="'.$value.'">'.$value.'</a></p>';      
        }
    }
    elseif ($type == 'select_custom_post_type') {
        $value = str_replace("<p>", "", $value);
        $value = str_replace("</p>", "", $value);
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
            return '<p>'.$post->post_title.'</p>';
        } else {
            // Custom post type is not the same.
            return '<p>--</p>';
        }
    }
    elseif ($type == 'multiselect_custom_post_type') {
        $value = str_replace("<p>", "", $value);
        $value = str_replace("</p>", "", $value);
        // Get field's data.
        $field = new BP_XProfile_Field($id);
        // Get children.
        $childs = $field->get_children();
        if (isset($childs) && count($childs) > 0) {
            // Get the name of custom post type.
            $custom_post_type = $childs[0]->name;
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
            return '<p>'.$cad.'</p>';
        } else {
            return '<p>--</p>';
        }
    }
    
    return $value;
}
add_filter( 'bp_get_the_profile_field_value', 'bxcft_get_field_value', 15, 3);

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
add_action( 'bp_init', 'bxcft_remove_xprofile_links', 9999 );

/**
 * Function replacing the original buddypress filter.
 * @param type $field_value
 * @param type $field_type
 * @return string
 */
function bxcft_xprofile_filter_link_profile_data( $field_value, $field_type = 'textbox' ) {
	if ( 'datebox' == $field_type || 'email' == $field_type || 'birthdate' == $field_type ||
            'datepicker' == $field_type || 'web' == $field_type || 'select_custom_post_type' == $field_type ||
            'multiselect_custom_post_type' == $field_type)
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

function bxcft_add_js($hook) {    
    if ('users_page_bp-profile-setup' != $hook && 'buddypress_page_bp-profile-setup' != $hook)
       return;

    wp_enqueue_script( 'bxcft-js', plugins_url('assets/js/addfields.js', __FILE__), array( 'jquery' ), '1.0' );
    $params = array(
        'birthdate' => __('Birthdate', 'bxcft'),
        'email' => __('Email', 'bxcft'),
        'web' => __('Website', 'bxcft'),
        'datepicker' => __('Datepicker', 'bxcft'),
        'select_custom_post_type' => __('Custom Post Type Selector', 'bxcft'),
        'multiselect_custom_post_type' => __('Custom Post Type Multiselector', 'bxcft')
    );
    wp_localize_script('bxcft-js', 'params', $params);
}
add_action( 'admin_enqueue_scripts', 'bxcft_add_js');

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
    if (isset($childs) && count($childs) > 0 && $field->type == 'select_custom_post_type') {
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
    if (isset($childs) && count($childs) > 0 && $field->type == 'multiselect_custom_post_type') {
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
    if (isset($childs) && count($childs) > 0 && $field->type == 'birthdate') {
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
 * This function only works if plugin bp-profile search is active.
 * @global type $bps_options
 * @param type $type
 * @return string
 */
function bxcft_profile_field_type($type) {
    if (function_exists(is_plugin_active) && is_plugin_active('bp-profile-search/bps-main.php')
            || in_array( 'bp-profile-search/bps-main.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
        global $bps_options;
        $id = bp_get_the_profile_field_id ();

        if ( ((is_admin() && $_SERVER['REQUEST_URI'] == '/wp-admin/admin.php?page=bp-profile-search') 
                || (isset($bps_options) && $bps_options['agerange'] == $id 
                        && isset($_POST['bp_profile_search']) && $_POST['bp_profile_search']))
                && $type == 'birthdate') {
            return 'datebox';
        }
    }
    
    return $type;
}
add_filter('bp_the_profile_field_type', 'bxcft_profile_field_type');