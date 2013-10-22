=== Buddypress Xprofile Custom Fields Type ===
Contributors: donmik, romik jan, dabesa, Branco Radenovich, @per4mance
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donmik%40gmail%2ecom&lc=GB&item_name=donmik%20%2d%20Plugin%20Buddypress%20Xprofile%20Custom%20Fields%20Type&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHostedGuest
Tags: buddypress, xprofile, fields
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 1.5.7.8

Add more custom fields type to extended profiles in Buddypress: Birthdate, Email, Web, Datepicker, ...

== Description ==

Buddypress installation required!! With Buddypress 1.7, I'm using a new hook "bp_custom_profile_edit_fields_pre_visibility" if you don't have it in your edit profile form or register page, the fields should not appear. Check this if the fields don't appear and you are using Buddypress 1.7.
Add more custom fields type to extended profiles in buddypress: Birthdate, Email, Web, Datepicker, Custom post type, Multi custom post type, checkbox acceptance, image, file, colorpicker, number. 
We add now a new visibility setting 'Nobody' to create fields hidden to all members of buddypress.
Works with <a href="http://wordpress.org/plugins/buddypress-xprofile-custom-fields-type/" title="BP Profile Search">BP Profile Search plugin</a> searching birthdate and age range.
If you need more fields type, you are free to add them yourself or request me at miguel@donmik.com.
I've moved this plugin to <a href="https://github.com/donmik/buddypress-xprofile-custom-fields-type">github</a>, you can contribute now. 

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

= Can I modify how the fields value are showned? =

Yes, you can, since version 1.5.4, I have added a filter called "bxcft_show_field_value". You can use it and modify the value of your fields. For example, if you need to show only the day or month of the birthdate you need to add this code below in the functions.php of your theme:

`add_filter( 'bxcft_show_field_value', 'my_show_field', 15, 4);
function my_show_field($value_to_return, $type, $id, $value) {
    $value_to_return = $value;
    if ($value_to_return == '')
        return $value_to_return;
    $value_to_return = $value;
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

= I've removed the filter that make clickable the profile fields, but with this plugin the links are still there ? =

With my plugin, you need to use this code to hide the links of profile fields:

`function remove_xprofile_links() {
    remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2 );
}
add_action('bp_setup_globals', 'remove_xprofile_links');`

= How to limit the size of image or file uploads ? =
In the function "bxcft_updated_profile", after this code:
`// Handles image field type saving.
if (isset($_FILES['field_'.$field_id]) && $_FILES['field_'.$field_id]['size'] > 0 .......here......... ) {...`
Thanks to borisnov for this tip.

== Changelog ==

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

= 1.5.5.4 =
* Just added a FAQ section in readme to help with frequently asked questions.

= 1.5.5.3 =
* Solved the notice with array_merge.

= 1.5.5.2 =
* New visibility setting "nobody" was wrong. Now it should work.

= 1.5.5.1 =
* Solved an error with the new filter created in version 1.5.4, change the position value_to_return to first argument.

= 1.5.5 =
* Solved a problem while checking the hook "bp_custom_profile_edit_fields_pre_visibility". We check now for version of buddypress, if it's prior to 1.7, we load the fields in the other hook "bp_custom_profile_edit_fields". The description will still appear after the visibility settings. You need to change this manually in your templates.
* Updated spanish translation.

= 1.5.4 =
* Created a new filter to show field value. Now you can add a filter in your functions.php and customize the way the field value will appear to the user.

= 1.5.3 =
* Changed the hook on bp_custom_profile_edit_fields to bp_custom_profile_edit_fields_pre_visibility because the fields will appear now before visibility settings. We check before the new tag exists because it's new to Buddypress 1.7. If it does not existe we will use then the other tag.
* Changed the hook on bp_init to bp_setup_globals.

= 1.5.2 = 
* Removed default case in switch bxcft_admin_render_new_xprofile_field_type in case other plugins add more fields.

= 1.5.1 =
* Solved an error with WP_CONTENT_URL or WP_CONTENT_DIR when upload dir was customized.
* Deleted http:// from the placeholder of Website field.

= 1.5 =
* Added checkbox acceptance for terms and conditions.
* Added image field (jpg, jpeg, png, gif). Created a filter 'images_ext_allowed' you can use to accept more images types. User can delete the image.
* Added file field (doc, docx, pdf). Created a filter 'files_ext_allowed' you can use to accept more files types. User can delete the file.
* Added new visibility 'Nobody' which hide field to all members.

= 1.4.9.3 =
* Added Russian translation thanks to Romik Jan.
* Class required added now and the "*" required asterisk is translatable. All this update is thanks to Romik Jan!

= 1.4.9.2 =
* Displaying all custom post type instead of only first ten in all cases...

= 1.4.9.1 =
* Displaying all custom post type instead of only first ten. Thanks to <a href="http://wordpress.org/support/topic/custom-post-type-multiselector?replies=3">dabesa</a>

= 1.4.9 =
* Added Slovak translation thanks to Branco Radenovich <a href="http://webhostinggeeks.com/user-reviews/">WebHostingGeeks.com</a>

= 1.4.8 =
* Added German translation thanks to @per4mance <a href="http://buddypress.org/community/members/per4mance/">http://buddypress.org/community/members/per4mance/</a>.

= 1.4.7 =
* Updated pot file.

= 1.4.6 =
* Updated Installation instructions.

= 1.4.5 =
* Solved bug with Wordpress 3.4

= 1.4.4 =
* Solved bug when using bp_get_profile_field_data in buddypress. Added new filter bxcft_get_field_data.

= 1.4.3 =
* Solved compatibility with Wordpress 3.5

= 1.4.2 =
* Trying to solve issue with bp profile search (there is no date field).

= 1.4.1 =
* Solved a bug with function is_plugin_active in previous Wordpress versions.

= 1.4 =
* Now works with BP Profile Search plugin.

= 1.3 =
* Solved bugs when deleting custom post type and multi custom post type.
* Added option to show age instead of birthdate.

= 1.2 =
* Changed start year of birthdate to one year before now, for people who are not major.
* Solved issue with buddypress searck links.
* Solved issue with birthdate.

= 1.1.1 =
* Solved a bug with buddypress 1.5.7 when adding js file.
* Solved a js error when adding a new field.

= 1.1.0 =
* Added Custom post type selector.
* Added Custom post type multiselector.
* Added donate link.

= 1.0.0 =
* Initial release version
