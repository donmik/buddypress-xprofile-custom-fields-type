<?php
/**
 * BuddyPress XProfile Classes
 *
 * @package BuddyPress
 * @subpackage XProfileClasses
 */

/**
 * Gender xprofile field type.
 *
 * @since BuddyPress (2.0.0)
 */
 if (!class_exists('Bxcft_Field_Type_Gender')) 
{
class Bxcft_Field_Type_Gender extends BP_XProfile_Field_Type {

	/**
	 * Constructor for the gender field type
	 *
	 * @since BuddyPress (2.0.0)
 	 */
	public function __construct() {
		parent::__construct();

		$this->name             = __( 'Gender Selector', 'bxcft' );
		
		$this->supports_options = true;

		$this->set_format( '/^.+$/', 'replace' );

		/**
		 * Fires inside __construct() method for BP_XProfile_Field_Type_Gender class.
		 *
		 * @since BuddyPress (2.0.0)
		 *
		 * @param BP_XProfile_Field_Type_Gender $this Current instance of
		 *                                               the field type select box.
		 */
		do_action( 'bp_xprofile_field_type_gender', $this );
	}

	/**
	 * Output the edit field HTML for this field type.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @param array $raw_properties Optional key/value array of {@link http://dev.w3.org/html5/markup/select.html permitted attributes} that you want to add.
	 * @since BuddyPress (2.0.0)
	 */
	public function edit_field_html( array $raw_properties = array() ) {

		// user_id is a special optional parameter that we pass to
		// {@link bp_the_profile_field_options()}.
		if ( isset( $raw_properties['user_id'] ) ) {
			$user_id = (int) $raw_properties['user_id'];
			unset( $raw_properties['user_id'] );
		} else {
			$user_id = bp_displayed_user_id();
		} ?>

		<label for="<?php bp_the_profile_field_input_name(); ?>">
			<?php bp_the_profile_field_name(); ?>
			<?php if ( bp_get_the_profile_field_is_required() ) : ?>
				<?php esc_html_e( '(required)', 'buddypress' ); ?>
			<?php endif; ?>
		</label>

		<?php

		/** This action is documented in bp-xprofile/bp-xprofile-classes */
		do_action( bp_get_the_profile_field_errors_action() ); ?>

		<select <?php echo $this->get_edit_field_html_elements( $raw_properties ); ?>>
			<?php bp_the_profile_field_options( array( 'user_id' => $user_id ) ); ?>
		</select>

		
		<?php
	}

	/**
	 * Output the edit field options HTML for this field type.
	 *
	 * BuddyPress considers a field's "options" to be, for example, the items in a gender.
	 * These are stored separately in the database, and their templating is handled separately.
	 *
	 * This templating is separate from {@link BP_XProfile_Field_Type::edit_field_html()} because
	 * it's also used in the wp-admin screens when creating new fields, and for backwards compatibility.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @param array $args Optional. The arguments passed to {@link bp_the_profile_field_options()}.
	 * @since BuddyPress (2.0.0)
	 */
	public function edit_field_options_html( array $args = array() ) {
		$original_option_values = maybe_unserialize( BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] ) );

		$options = $this->field_obj->get_children();
		$html    = '<option value="">' . /* translators: no option picked in select box */ esc_html__( '----', 'buddypress' ) . '</option>';
		
		if ( empty( $original_option_values ) && !empty( $_POST['field_' . $this->field_obj->id] ) ) {
			// This code is never reached
			$original_option_values = sanitize_text_field(  $_POST['field_' . $this->field_obj->id] );
		}

		$option_values = ( $original_option_values ) ? (array) $original_option_values : array();

		for ( $k = 0, $count = count( $options ); $k < $count; ++$k ) {
			
			$selected = '';

			// Check for updated posted values, but errors preventing them from
			// being saved first time
			foreach( $option_values as $i => $option_value ) {
				if ( isset( $_POST['field_' . $this->field_obj->id] ) && $_POST['field_' . $this->field_obj->id] != $option_value ) {
					if ( ! empty( $_POST['field_' . $this->field_obj->id] ) ) {
						$option_values[$i] = sanitize_text_field( $_POST['field_' . $this->field_obj->id] );
					}
				}
			}
			
			// Run the allowed option name through the before_save filter, so
			// we'll be sure to get a match
			$allowed_options = xprofile_sanitize_data_value_before_save( $options[$k]->name, false, false );

			// First, check to see whether the user-entered value matches
			if ( in_array( $allowed_options, $option_values ) ) {
				$selected = ' selected="selected"';
			}

			// Then, if the user has not provided a value, check for defaults
			if ( ! is_array( $original_option_values ) && empty( $option_values ) && $options[$k]->is_default_option ) {
				$selected = ' selected="selected"';
			}

			/**
			 * Filters the HTML output for options in a select input.
			 *
			 * @since BuddyPress (1.1.0)
			 *
			 * @param string $value    Option tag for current value being rendered.
			 * @param object $value    Current option being rendered for.
			 * @param int    $id       ID of the field object being rendered.
			 * @param string $selected Current selected value.
			 * @param string $k        Current index in the foreach loop.
			 */
			$html .= apply_filters( 'bp_get_the_profile_field_options_select', '<option' . $selected . ' value="' . esc_attr( stripslashes( $options[$k]->name ) ) . '">' . esc_html__( stripslashes( $options[$k]->name ), 'bxcft' ) . '</option>', $options[$k], $this->field_obj->id, $selected, $k );			
		}

		echo $html;
	}

	/**
	 * Output HTML for this field type on the wp-admin Profile Fields screen.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @param array $raw_properties Optional key/value array of permitted attributes that you want to add.
	 * @since BuddyPress (2.0.0)
	 */
	public function admin_field_html( array $raw_properties = array() ) {
		?>

		<select <?php echo $this->get_edit_field_html_elements( $raw_properties ); ?>>
			<?php bp_the_profile_field_options(); ?>
		</select>

		<?php
	}

	/**
	 * Output HTML for this field type's children options on the wp-admin Profile Fields "Add Field" and "Edit Field" screens.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @param BP_XProfile_Field $current_field The current profile field on the add/edit screen.
	 * @param string $control_type Optional. HTML input type used to render the current field's child options.
	 * @since BuddyPress (2.0.0)
	 */
	public function admin_new_field_html( BP_XProfile_Field $current_field, $control_type = '' ) {
		$control_type = 'radio';
		
		$type = array_search( get_class( $this ), bp_xprofile_get_field_types() );
		if ( false === $type ) {
			return;
		}

		$class            = $current_field->type != $type ? 'display: none;' : '';
		$current_type_obj = bp_xprofile_create_field_type( $type );
		
		$gender_values = array(
			0   => 'Male',
			1   => 'Female',
		);
		
		?>

		<div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
			<h3><?php esc_html_e( 'Options for this Field:', 'buddypress' ); ?></h3>
			<div class="inside">
				<?php

				// Does option have children?
				$options = $current_field->get_children( true );

				// If no children options exists for this field, check in $_POST
				// for a submitted form (e.g. on the "new field" screen).
				if ( empty( $options ) ) {

					$options = array();
					$i       = 1;

					while ( isset( $_POST[$type . '_option'][$i] ) ) {

						// Multiselectbox and checkboxes support MULTIPLE default options; all other core types support only ONE.
						if ( $current_type_obj->supports_options && ! $current_type_obj->supports_multiple_defaults && isset( $_POST["isDefault_{$type}_option"][$i] ) && (int) $_POST["isDefault_{$type}_option"] === $i ) {
							$is_default_option = true;
						} elseif ( isset( $_POST["isDefault_{$type}_option"][$i] ) ) {
							$is_default_option = (bool) $_POST["isDefault_{$type}_option"][$i];
						} else {
							$is_default_option = false;
						}

						// Grab the values from $_POST to use as the form's options
						$options[] = (object) array(
							'id'                => -1,
							'is_default_option' => $is_default_option,
							'name'              => sanitize_text_field( stripslashes( $_POST[$type . '_option'][$i] ) ),
						);

						++$i;
					}

					// If there are still no children options set, this must be the "new field" screen, so add one new/empty option.
					if ( empty( $options ) ) {
						$options[] = (object) array(
							'id'                => -1,
							'is_default_option' => false,
							'name'              => '',
						);
					}
				}

				// Render the markup for the children options
				if ( ! empty( $options ) ) {
					$default_name = '';

					for ( $i = 0, $count = count( $options ); $i < $count; ++$i ) :
						$j = $i + 1;

						// Multiselectbox and checkboxes support MULTIPLE default options; all other core types support only ONE.
						if ( $current_type_obj->supports_options && $current_type_obj->supports_multiple_defaults ) {
							$default_name = '[' . $j . ']';
						}
						?>

						<div id="<?php echo esc_attr( "{$type}_div{$j}" ); ?>" class="bp-option sortable">
							<span class="bp-option-icon grabber"></span>
							<input type="text" name="<?php echo esc_attr( "{$type}_option[{$j}]" ); ?>" id="<?php echo esc_attr( "{$type}_option{$j}" ); ?>" value="<?php echo esc_attr( stripslashes( $options[$i]->name ) ); ?>" />
							<label>
								<input type="<?php echo esc_attr( $control_type ); ?>" name="<?php echo esc_attr( "isDefault_{$type}_option{$default_name}" ); ?>" <?php checked( $options[$i]->is_default_option, true ); ?> value="<?php echo esc_attr( $j ); ?>" />
								<?php _e( 'Default Value', 'buddypress' ); ?>
							</label>

							<?php if ( 1 !== $j ) : ?>
								<div class ="delete-button">
									<a href='javascript:hide("<?php echo esc_attr( "{$type}_div{$j}" ); ?>")' class="delete"><?php esc_html_e( 'Delete', 'buddypress' ); ?></a>
								</div>
							<?php endif; ?>

						</div>

					<?php endfor; ?>

					<input type="hidden" name="<?php echo esc_attr( "{$type}_option_number" ); ?>" id="<?php echo esc_attr( "{$type}_option_number" ); ?>" value="<?php echo esc_attr( $j + 1 ); ?>" />
				<?php } ?>

				<div id="<?php echo esc_attr( "{$type}_more" ); ?>"></div>				
				
				<script>
					if (!document.getElementById('gender_option2')) {
						add_option('<?php echo esc_js( $type ); ?>');
						
						document.getElementById('gender_option1').value = "<?php echo $gender_values[0] ?>";
						document.getElementById('gender_option2').value = "<?php echo $gender_values[1] ?>";
					}					
				</script>
				
				<?php

				/**
				 * Fires at the end of the new field additional settings area.
				 *
				 * @since BuddyPress (2.3.0)
				 *
				 * @param BP_XProfile_Field $current_field Current field being rendered.
				 */
				do_action( 'bp_xprofile_admin_new_field_additional_settings', $current_field ) ?>
			</div>
		</div>

		<?php 
	}

}
}
