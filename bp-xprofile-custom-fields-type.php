<?php
/*
    Plugin Name: Buddypress Xprofile Custom Fields Type
    Description: Buddypress installation required!! Add more custom fields type to extended profiles in buddypress: Birthdate, Email, Web, Datepicker.
    Version: 1.0
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
   $new_field_types = array('birthdate', 'email', 'web', 'datepicker');
   $field_types = array_merge($field_types, $new_field_types);
   return $field_types;
}
add_filter( 'xprofile_field_types', 'bxcft_add_new_xprofile_field_type' );

function bxcft_admin_render_new_xprofile_field_type($field, $echo = true) {
   switch ( $field->type ) {
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
           for ( $i = date('Y')-17; $i > 1901; $i-- ) {
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

               <select name="<?php bp_the_profile_field_input_name() ?>_year" id="<?php bp_the_profile_field_input_name(); ?>_year" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true" required="required"<?php endif; ?>>
               <option value=""<?=selected( $year, '', false )?>>----</option>
               <?php
                   for ( $i = date('Y')-17; $i > 1901; $i-- ) {
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

function bxcft_get_fecha_nacimiento_value( $value='', $type='', $id='') {

    if ($type == 'birthdate' || $type == 'datepicker') {
        $value = str_replace("<p>", "", $value);
        $value = str_replace("</p>", "", $value);
        list($fecha, $hora) = explode(" ", $value);
        list($ano, $mes, $dia) = explode("-", $fecha);
        return "<p>".$dia."/".$mes."/".$ano."</p>";
    }
    
    return $value;
}
add_filter( 'bp_get_the_profile_field_value', 'bxcft_get_fecha_nacimiento_value', 15, 3);

function bxcft_add_js($hook) {    
    if ('users_page_bp-profile-setup' != $hook)
       return;

    wp_enqueue_script( 'bxcft-js', plugins_url('assets/js/addfields.js', __FILE__), array( 'jquery' ), '1.0' );
    $params = array(
        'birthdate' => __('Birthdate', 'bxcft'),
        'email' => __('Email', 'bxcft'),
        'web' => __('Website', 'bxcft'),
        'datepicker' => __('Datepicker', 'bxcft')
    );
    wp_localize_script('bxcft-js', 'params', $params);
}
add_action( 'admin_enqueue_scripts', 'bxcft_add_js');

function bxcft_selected_field($field) {
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        bxcft.select('<?php echo $field->type; ?>');
    });    
</script>
<?php
}
add_action('xprofile_field_additional_options', 'bxcft_selected_field');