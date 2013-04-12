=== Buddypress Xprofile Custom Fields Type ===
Contributors: atallos
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LRLW5AMCJGWQN
Tags: buddypress, xprofile, fields
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 1.5.1

Add more custom fields type to extended profiles in Buddypress: Birthdate, Email, Web, Datepicker, ...

== Description ==

Buddypress installation required!!
Add more custom fields type to extended profiles in buddypress: Birthdate, Email, Web, Datepicker, Custom post type, Multi custom post type, checkbox acceptance, image field and type field. 
We add now a new visibility setting 'Nobody' to create fields hidden to all members of buddypress.
Works with <a href="http://buddypress.org/community/groups/bp-profile-search/" title="BP Profile Search">BP Profile Search plugin</a> searching birthdate and age range.
If you need more fields type, you are free to add them yourself or request us at info@atallos.com.

<a href="http://www.atallos.com" title="Atallos Cloud">www.atallos.com</a>

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
* Add new visibility setting "Nobody". Hide the field to all members.
* Works with BP Profile Search plugin.

== Installation ==

1. Upload the plugin to your 'wp-content/plugins' directory
2. Activate the plugin
3. Go to Users > Profile Fields
4. Create or Edit a field (default buddypress field Name don't allow changing type, it will not work here).
5. In Field Type select, you can see new field's type.
6. Enjoy!

== Changelog ==

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
