<?php
/**
 * Select Custom Post Type Type
 */
if (!class_exists('Bxcft_Field_Type_CheckboxMailPoetLists'))
{
	class Bxcft_Field_Type_CheckboxMailPoetLists extends BP_XProfile_Field_Type
	{
		
		public function __construct() {
			parent::__construct();

			$this->name				= _x( 'MailPoet Newsletter Lists', 'xprofile field type', 'bxcft' );
			$this->supports_options	= true;

			$this->set_format( '/^.+$/', 'replace' );
			
			do_action( 'bp_xprofile_field_type_checkbox_mailpoet_lists', $this );
			
			// Add the update_subscription function to the after save trigger
			// This function updates the MailPoet newsletter subscriptions
			add_action( 'xprofile_data_after_save', array( $this, 'update_subscriptions' ) );
		}
		
		public static function isMailPoetActive() {
			// Check if the MailPoet plugin is active
			return is_plugin_active( 'wysija-newsletters/index.php' );
		}
		
		public function admin_field_html(array $raw_properties = array()) {
			bp_the_profile_field_options();
		}

		public function admin_new_field_html(\BP_XProfile_Field $current_field, $control_type = '') {
			
			$type = array_search( get_class( $this ), bp_xprofile_get_field_types() );
			if ( false === $type ) {
				return;
			}

			$class            = $current_field->type != $type ? 'display: none;' : '';
			$current_type_obj = bp_xprofile_create_field_type( $type );

			$options = $current_field->get_children( true );
			
			if (!$options) {
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
			
			?>
				<div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
			<?php
			
			if (Bxcft_Field_Type_CheckboxMailPoetLists::isMailPoetActive()) {
				
				$optionids = array();
				foreach ($options as $options) {
					$optionids[] = (int)$options->name;
				}
				$model_list = WYSIJA::get('list', 'model');
				$listsdata = $model_list->get(array('list_id', 'name'), array('is_enabled' => '1'));
		
				if (!$listsdata || count($listsdata) == 0) :
				?>
					<h3><?php _e('There are no newsletter lists. You need to create and/or enable  at least one list to use this field.', 'bxcft'); ?></h3>
				<?php else : ?>
					<h3><?php esc_html_e( 'Select one or more lists:', 'bxcft' ); ?></h3>
					<div class="inside">
						<p>
						<?php for ($i = 0, $count = count($listsdata); $i < $count; $i++) { ?>
							<label for="<?php echo esc_attr( "{$type}_option_{$i}" ); ?>" style="margin-right:20px"><input type="checkbox" name="<?php echo esc_attr( "{$type}_option" ); ?>[]" id="<?php echo esc_attr( "{$type}_option_{$i}" ); ?>" value="<?php echo $listsdata[$i]['list_id']; ?>" <?php if (in_array((int)$listsdata[$i]['list_id'], $optionids)) echo ' checked'; ?> /><?php echo $listsdata[$i]['name']; ?></label>
						<?php } ?>
						</p>
					</div>
				<?php endif; ?>
				
				<?php
			
			} else {
				
				?>
					<h3><?php _e('You need to install and/or activate MailPoet to use this field.', 'bxcft'); ?></h3>
				<?php
				
			}
			?>
				</div>
			<?php
		}

		public function edit_field_html (array $raw_properties = array ())
		{
			if (!Bxcft_Field_Type_CheckboxMailPoetLists::isMailPoetActive()) {
				return;
			}
			
			$user_id = bp_displayed_user_id();

			if ( isset( $raw_properties['user_id'] ) ) {
				$user_id = (int) $raw_properties['user_id'];
				//unset( $raw_properties['user_id'] );
			} else {
				$raw_properties['user_id'] = $user_id;
			}

			// HTML5 required attribute.
			if ( bp_get_the_profile_field_is_required() ) {
				$raw_properties['required'] = 'required';
			}

		?>
			<label id="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php esc_html_e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
			<?php do_action( bp_get_the_profile_field_errors_action() ); ?>
			<?php bp_the_profile_field_options($raw_properties); ?>
		<?php
		}

		public function edit_field_options_html( array $args = array() ) {
			
			$user_id		= bp_displayed_user_id();
			$attr			= $this->get_edit_field_html_elements($args);
			$field_name		= esc_attr( "field_{$this->field_obj->id}[]" );
			$labelledby		= bp_get_the_profile_field_input_name();
			
			$options        = $this->field_obj->get_children();
			
			// get the enabled lists
			list($listidsfromuser, $listnames) = Bxcft_Field_Type_CheckboxMailPoetLists::_getUserLists($user_id);
			//wh_print_r($listidsfromuser);
			
			$option_values = array();
			foreach ($listidsfromuser as $listdt) {
				$option_values[] = (string)$listdt['list_id'];
			}
			
			// Check for updated posted values, but errors preventing them from
			// being saved first time.
			if ( isset( $_POST['field_' . $this->field_obj->id] ) && $option_values != maybe_serialize( $_POST['field_' . $this->field_obj->id] ) ) {
				if ( ! empty( $_POST['field_' . $this->field_obj->id] ) ) {
					$option_values = array_map( 'sanitize_text_field', $_POST['field_' . $this->field_obj->id] );
				}
			}

			$html = '';
			
			if ($options) {
				
				for ( $k = 0, $count = count( $options ); $k < $count; ++$k ) {
					$listname = $listnames[$options[$k]->name];
					if (!$listname) continue;
					
					$selected = '';
					if (in_array($options[$k]->name, $option_values)) {
						$selected = ' checked="checked"';
					}
			
					$field_id = esc_attr( "field_{$options[$k]->id}_{$k}" );
					$new_html = sprintf( '<label for="%3$s" id="%3$s_lbl" style="margin-right:20px"><input %1$s type="checkbox" name="%2$s" id="%3$s" value="%4$s" aria-labelledby="%6$s %3$s_lbl">%5$s</label>',
						$selected,
						esc_attr( "field_{$this->field_obj->id}[]" ),
						esc_attr( "field_{$options[$k]->id}_{$k}" ),
						esc_attr( stripslashes( $options[$k]->name ) ),
						esc_html( stripslashes( $listname ) ),
						$labelledby
					);
					
					/**
					 * Filters the HTML output for an individual field options checkbox.
					 *
					 * @since 1.1.0
					 *
					 * @param string $new_html Label and checkbox input field.
					 * @param object $value    Current option being rendered for.
					 * @param int    $id       ID of the field object being rendered.
					 * @param string $selected Current selected value.
					 * @param string $k        Current index in the foreach loop.
					 */
					$html .= apply_filters( 'bp_get_the_profile_field_checkbox_mailpoet_lists', $new_html, $options[$k], $this->field_obj->id, $selected, $k );
							
				}
				
			}

			echo $html;
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
		 * Modify the appearance of value. Apply autolink if enabled. NOT IMPLEMENTED
		 *
		 * @param  string   $value      Original value of field
		 * @param  int      $field_id   Id of field
		 * @return string   Value formatted
		 */
		public static function display_filter($field_value, $field_id = '') {
			
			$user_id						= bp_displayed_user_id();
			list($userlists, $listnames)	= Bxcft_Field_Type_CheckboxMailPoetLists::_getUserLists($user_id);
			
			$values		= array();
			foreach ($userlists as $list) {
				$values[] = $list['name'];
			}
			
			return implode(', ', $values);
		}
		
		public function update_subscriptions($data_field) {
	
			$field = xprofile_get_field( $data_field->field_id );
			
			if ($field->type != 'checkbox_mailpoet_lists' || !Bxcft_Field_Type_CheckboxMailPoetLists::isMailPoetActive())
				return ;
		
			$user_id = $data_field->user_id;
			
			// Allow plugins to filter the field's child options (i.e. the items in a selectbox).
			$post_option = maybe_unserialize( $data_field->value );


			/**
				* Filters the submitted field option value before saved.
				*
				* @since 1.5.0
				*
				* @param string            $post_option Submitted option value.
				* @param BP_XProfile_Field $type        Current field type being saved for.
				*/
			$options = apply_filters( 'xprofile_field_options_before_save', $post_option,  $this->type );
			$options = (array)$options;
				
			// get the enabled lists
			$model_list		= WYSIJA::get('list', 'model');
			$listsdata		= $model_list->get(array('list_id', 'name'), array('is_enabled' => '1'));
			$enabled_list_ids = array();
			foreach ($listsdata as $listdt) {
				$enabled_list_ids[] = $listdt['list_id'];
			}
			
			// get list_id from user
			$model_user_list = WYSIJA::get('user_list', 'model');
			$listidsfromuser = $model_user_list->get(array('list_id'), array('user_id' => $user_id, 'list_id' => $enabled_list_ids));
			
			$option_values	= array();
			$removeFromList = array();
			$addToList		= array();
			
			$changed		= false;
			foreach ($listidsfromuser as $listdt) {
				$option_values[] = (string)$listdt['list_id'];
				if (!in_array((string)$listdt['list_id'], $options)) $removeFromList[] = $listdt['list_id'];
			}
			foreach ($options as $option) {
				if (!in_array($option, $option_values)) $addToList[] = (int)$option;
			}
			$userHelper = WYSIJA::get('user', 'helper'); //new WYSIJA_help_user();
			
			if (count($removeFromList)) {
					$userHelper->removeFromLists($removeFromList, array($user_id));
			}
			if (count($addToList)) {
				$userHelper->addToLists($addToList, $user_id);
			}

		}
		
		public static function _getUserLists($user_id) {
			// get the enabled lists
			$model_list		= WYSIJA::get('list', 'model');
			$listsdata		= $model_list->get(array('list_id', 'name'), array('is_enabled' => '1'));
			$enabled_list_ids	= array();
			$listnames			= array();
			foreach ($listsdata as $listdt) {
				$enabled_list_ids[] = $listdt['list_id'];
				$listnames[(string)$listdt['list_id']] = $listdt['name'];
			}
			
			// get list_id from user
			$model_user_list = WYSIJA::get('user_list', 'model');
			$listidsfromuser = $model_user_list->get(array('list_id'), array('user_id' => $user_id, 'list_id' => $enabled_list_ids));
			foreach ($listidsfromuser as &$l) {
				$l['name'] = $listnames[$l['list_id']];
			}
			return array($listidsfromuser, $listnames);
		}
	}
}
