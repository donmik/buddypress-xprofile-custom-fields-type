=== Buddypress Xprofile Custom Fields Type ===
Contributors: donmik, romik jan, dabesa, Branco Radenovich, @per4mance, Laszlo Espadas, Michael Yunat, briannaorg
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donmik%40gmail%2ecom&lc=GB&item_name=donmik%20%2d%20Plugin%20Buddypress%20Xprofile%20Custom%20Fields%20Type&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHostedGuest
Tags: buddypress, xprofile, fields
Requires at least: 3.0
Tested up to: 3.9
Stable tag: 2.0.3

Add more custom fields type to extended profiles in Buddypress: Birthdate, Email, Web, Datepicker, ...

== Description ==

Buddypress installation required!! With Buddypress 1.7, I'm using a new hook "bp_custom_profile_edit_fields_pre_visibility" if you don't have it in your edit profile form or register page, the fields should not appear. Check this if the fields don't appear and you are using Buddypress 1.7.
Add more custom fields type to extended profiles in buddypress: Birthdate, Email, Web, Datepicker, Custom post type, Multi custom post type, checkbox acceptance, image, file, colorpicker, number. 
We add now a new visibility setting 'Nobody' to create fields hidden to all members of buddypress.
Works with <a href="http://wordpress.org/plugins/buddypress-xprofile-custom-fields-type/" title="BP Profile Search">BP Profile Search plugin</a> searching birthdate and age range.
If you need more fields type, you are free to add them yourself or request me at miguel@donmik.com. I've moved this plugin to <a href="https://github.com/donmik/buddypress-xprofile-custom-fields-type">github</a>, you can contribute now.

Tested with Buddypress 2.0!

BE CAREFUL! Version 2.0 should work ONLY with Buddypress 2.0 at least.

= Features =
* Add Birthdate field.
* Add Email field (HTML5).
* Add Web field (HTML5).
* Add Datepicker field (HTML5).
* Add Custom post type selector.
* Add Custom post type multiselector.
* Add Checkbox acceptance.
* Add Image Field (jpg, jpeg, gif, png).
* Add File field (doc, docx, pdf).
* Add Colorpicker field.
* Add Number field.
* Add new visibility setting "Nobody". Hide the field to all members.
* Works with BP Profile Search plugin.

== Installation ==

1. Upload the plugin to your 'wp-content/plugins' directory
2. Activate the plugin
3. Go to Users > Profile Fields
4. Create or Edit a field (default buddypress field Name don't allow changing type, it will not work here).
5. In Field Type select, you can see new field's type.
6. Enjoy!

== Frequently Asked Questions ==

= Why my fields are not showing ? =

If you are using Buddypress 1.7, you need to check if you have the new hook "bp_custom_profile_edit_fields_pre_visibility". Check in your edit.php (/your-theme/members/single/profile/edit.php, if this page is not in your theme, check in buddypress plugin in bp-themes/bp-default folder) and register page (/your-theme/registration/register.php, if this page is not in your theme, check bp-default theme of buddypress plugin) if this line of code: `<?php do_action ( 'bp_custom_profile_edit_fields_pre_visibility' ); ?>`. If you don't see it, you must add it just before the code of visibility settings.

With Buddypress 2.0, things changed. The new template used by buddypress in edit.php is:
`
<div<?php bp_field_css_class( 'editfield' ); ?>>
<?php
    $field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
    $field_type->edit_field_html();

    do_action( 'bp_custom_profile_edit_fields_pre_visibility' );
?>`
If your file edit.php don't have this code, you should not see the fields created with this plugin, so you need to update your template edit.php and also register.php with this code. You only need replace all the content mainly php between this line "<div<?php bp_field_css_class( 'editfield' ); ?>>" and this line "do_action( 'bp_custom_profile_edit_fields_pre_visibility' );". 
The old templates should look like this:
`
<div<?php bp_field_css_class( 'editfield' ); ?>>
<?php if ( 'textbox' == bp_get_the_profile_field_type() ) : ?>

					<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
					<input type="text" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" value="<?php bp_the_profile_field_edit_value(); ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>/>

				<?php endif; ?>

				<?php if ( 'textarea' == bp_get_the_profile_field_type() ) : ?>

					<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
					<textarea rows="5" cols="40" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>><?php bp_the_profile_field_edit_value(); ?></textarea>

				<?php endif; ?>

[...]
[...]
<?php do_action( 'bp_custom_profile_edit_fields_pre_visibility' ); ?>`

= Can I modify how the fields value are showned? =

Yes, you can, since version 1.5.4, I have added a filter called "bxcft_show_field_value". You can use it and modify the value of your fields. For example, if you need to show only the day or month of the birthdate you need to add this code below in the functions.php of your theme:

`add_filter( 'bxcft_show_field_value', 'my_show_field', 15, 4);
function my_show_field($value_to_return, $type, $id, $value) {
    $value_to_return = $value;
    if ($value_to_return == '')
        return $value_to_return;
    if ($type == 'birthdate') {
        $value = str_replace("<p>", "", $value);
        $value = str_replace("</p>", "", $value);
        $field = new BP_XProfile_Field($id);
        // Get children.
        $childs = $field->get_children();
        $show_age = false;
        if (isset($childs) && $childs && count($childs) > 0) {
            // Get the name of custom post type.
            if ($childs[0]->name == 'show_age')
                $show_age = true;
        }
        if ($show_age) {
            return '<p>'.floor((time() - strtotime($value))/31556926).'</p>';
        }
        return '<p>'.date_i18n( 'F j' , strtotime($value) ).'</p>';
    }
    return $value_to_return;
}`

= Where are my images or files uploaded? =

Your files are uploaded in "YOUR_UPLOAD_DIR / profiles / ID_OF_USER" folder.

= How can I put description text below my field's label ? =

For now, you can use javascript to do this. Write this javascript at the end of your template file (register.php for example).

`// For each description we have.
jQuery('p.description').each(function() {
    // Clone description.
    // Looking for parent div.
    // Looking for label of checkbox field or radio field first.
    var desc = jQuery(this).clone(),
        parent = jQuery(this).parent(),
        label = parent.find('span.label');
    // If there is no label of checkbox field or radio field, we look for normal labels.
    if (!label.length) {
        label = parent.find('label');
    }
    // If there is a label.
    if (label.length) {
        // Putting the description after the label.
        label.after(desc);
        // Removing the original description.
        jQuery(this).remove();
    }
});`

= Can I modify the way label and input fields are shown? =
Yes, you can. Since version 1.5.8, you have new filters to modify the way labels and filters are shown. The filters are:
- bxcft_field_label. The arguments are: id of field, type of field, name of input, name of field, field is required? (this is a boolean value).
- bxcft_field_input. The arguments are: id of field, type of field, name of input, field is required? (this is a boolean value).
For image and file fields, This filter exists only for image and file field.
- bxcft_field_actual_image. The arguments are: id of field, type of field, name of input, value of field (url of image).
- bxcft_field_actual_file. The arguments are: id of field, type of field, name of input, value of field (url of file).
 
== Changelog ==

= 2.0.3 =
* Search links on profile fields are back now.
* FAQ updated.

= 2.0.2 =
* Solved error with birthdate month selector. It was displaying only january to november. https://wordpress.org/support/topic/birthdate-selector-off-by-1-month

= 2.0.1 =
* Solved error with checkbox acceptance field. https://wordpress.org/support/topic/checkbox-aceptance-field-doesnt-work

= 2.0 =
* UPDATE CAREFULLY !!! Completely rewritten using new features of Buddypress 2.0. Maybe something from previous versions stop working in 2.0.
* This version should work ONLY with Buddypress 2.0 or later.
* Checkbox Acceptance: I’ve added a field where you can write the text of this field. You can also add links to the text. You should not use the description field like previous releases.
* Filter “bxcft_field_input” has been removed for all types except image and file. You can use Buddypress filter “bp_xprofile_field_edit_html_elements” to add properties.

= 1.5.9.6 =
* Solving error in registration and edit profile form with Image field.

= 1.5.9.5 =
* Removed number field type. Thanks @briannaorg. https://github.com/donmik/buddypress-xprofile-custom-fields-type/pull/11

= 1.5.9.4 =
* Solving warning errors when uploading image in registration form.
* Added Ukranian translation thanks to Michael Yunat, Ukranian <a href="http://getvoip.com/blog">http://getvoip.com</a>

= 1.5.9.3 =
* Resolving changelog issues.

= 1.5.9.2 =
* Nothing new!

= 1.5.9.1 =
* Solving error when rendering field values like birthdate, age or datepicker.
* Solving error when more than one image field. https://github.com/donmik/buddypress-xprofile-custom-fields-type/issues/9

= 1.5.9 =
* Working with Buddypress 2.0!
* Changed FAQ.
* Rewritten validation and use of fields of type: file and image. This is working now when marked as required field in registration form.
* Deleted p tags from values. You will get the value of field.
* Added Brazilian translation. Thanks to https://github.com/espellcaste

= 1.5.8.7 =
* Remove the default description field in case the field is checkbox acceptance also in registration form.

= 1.5.8.6 =
* Remove the default description field in case the field is checkbox acceptance.

= 1.5.8.5 =
* Solving this issue https://github.com/donmik/buddypress-xprofile-custom-fields-type/issues/5

= 1.5.8.4 =
* Solving this issue https://github.com/donmik/buddypress-xprofile-custom-fields-type/issues/2#issuecomment-31181714

= 1.5.8.3 =
* Solving this issue https://github.com/donmik/buddypress-xprofile-custom-fields-type/issues/3

= 1.5.8.2 =
* Hungarian translation added thanks to Laszlo Espadas.

= 1.5.8.1 =
* Changed priority of my custom filter: bxcft_xprofile_get_hidden_fields_for_user.

= 1.5.8 =
* Added new filters for labels and inputs. See <a href="http://wordpress.org/plugins/buddypress-xprofile-custom-fields-type/faq/">FAQ</a> for more information.

= 1.5.7.9 =
* Bug in setting up add_filter. It requires a priority parameter first before the accepted_args so it only send the hidden fields. The other 2 fields $display_user_id and $current_user_id is set to 0 and the function returns erratic or no results because of that. Thanks to moggedb.

= 1.5.7.8 =
* Updated FAQ.
* Moved to <a href="https://github.com/donmik/buddypress-xprofile-custom-fields-type">github</a>.


= 1.5.7.7 =
* When a field is empty, my plugin add "p" tags and this is wrong. Now when a field is empty, it will return empty...
* Updated FAQ.

= 1.5.7.6 =
* Solving a bug caused by me solving another bug...

= 1.5.7.5 =
* Solved a bug in bxcft_edit_render_new_xprofile_field function. Thanks to thomaslhotta.
* Updated FAQ.

= 1.5.7.4 =
* Updated German translation. Thanks to Thorsten Wollenhöfer.
* Added hook for errors like buddypress registration template: bp_fieldname_errors.

= 1.5.7.3 =
* Updated FAQ with javascript snippet to change location of description.
* Updated all translations files.
* Changed "*" with "(required)" string from buddypress files. Now the required word is the same for all fields. Fields from this plugin and original fields from buddypress.

= 1.5.7.2 =
* Added a new field type: Number.

= 1.5.7.1 =
* Solved bug displaying today date when user don't fill birthdate field.

= 1.5.7 =
* Added a new field type: Colorpicker.
* Added Modernizr plugin for testing support in browsers.
* Added Jscolor plugin fallback for browser with no support for colorpicker html5 field.

= 1.5.6.5 =
* Added a new filter for displaying "Download file" link. Filter named "bxcft_show_download_file_link" and send text link, type of field, id of field, value of field. Thanks to kmb@deam.org for suggesting this.

= 1.5.6.4 =
* Updated russian translation thanks to Romik Jan.

= 1.5.6.3 =
* Updated the link of BP Profile Search plugin.

= 1.5.6.2 =
* Solved a warning when you use custom post type multiselect and have no choices.
* Solved a warning with queries changing ASC with 'ASC'.
* Improving the faq.

= 1.5.6.1 =
* Changed FAQ.

= 1.5.6 =
* Solved errors when uploading images or files in register page.
* Revised code responsible of uploading files or images in edit profile.

= 1.5.5.5 =
* Solved error when showing age instead of birthdate.
