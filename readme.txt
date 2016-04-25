=== Buddypress Xprofile Custom Fields Type ===
Contributors: donmik, dabesa, briannaorg
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donmik%40gmail%2ecom&lc=GB&item_name=donmik%20%2d%20Plugin%20Buddypress%20Xprofile%20Custom%20Fields%20Type&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHostedGuest
Tags: buddypress, xprofile, fields
Requires at least: 3.0
Tested up to: 4.4
Stable tag: 2.4.6
License: GLPv2 or later

Buddypress 2.0 required! This plugin add custom field types to Buddypress Xprofile extension. Field types are: Birthdate, Email, Url, ...

== Description ==

= Buddypress required! (v2.0 at least) =

This plugin add more fields type to Buddypress extension: Xprofile. The fields type added are:

* Birthdate.
* [Email](http://www.w3.org/TR/html-markup/input.email.html "Input type email - HTML5").
* [Web](http://www.w3.org/TR/html-markup/input.url.html "Input type url - HTML5").
* [Datepicker](http://www.w3.org/TR/2013/NOTE-html-markup-20130528/input.date.html "Input type date - HTML5").
* Custom post type selector.
* Custom post type multiselector.
* Checkbox acceptance.
* Image.
* File.
* [Colorpicker](http://www.w3.org/TR/2013/NOTE-html-markup-20130528/input.color.html "Input type color - HTML5").
* Decimal number.
* Number within min/max values.
* Custom taxonomy selector.
* Custom taxonomy multiselector.
* Range input (slider)
* [Select2 javascript plugin](https://select2.github.io/) for select boxes.

Works with [BP Profile Search](https://wordpress.org/plugins/bp-profile-search/ "BP Profile Search plugin"). [Available on Github](https://github.com/donmik/buddypress-xprofile-custom-fields-type "Feel free to contribute"). If you need more fields type, you are free to add them yourself or request me at miguel@donmik.com. Follow me: [donmik.com](http://donmik.com "Follow me") or [@kimnod](http://twitter.com/kimnod "Follow me on Twitter")

== Installation ==

1. Upload the plugin to your 'wp-content/plugins' directory
2. Activate the plugin
3. Go to Users > Profile Fields
4. Create or Edit a field (default buddypress field Name don't allow changing type, it will not work here).
5. In Field Type select, you can see new field's type.
6. For select2, you can see a new box below submit button only with selectbox, multiselectbox,
custom post type selector, custom post type multiselector, custom taxonomy selector and
custom taxonomy multiselector.
6. Enjoy!

== Frequently Asked Questions ==

<http://donmik.com/en/buddypress-xprofile-custom-fields-type/#faq>

== Changelog ==

= 2.4.6 =
* Emails, Images, Files links were not working since last update 2.4.5. This is because
Autolink feature makes no sense for this type of fields.
https://wordpress.org/support/topic/images-field-broken-on-front-end?replies=2#post-8273463
* Added a new method to all Field Type classes overriding "display_filter" method.
This method will handle the way the field values are displayed.
* Email: When you enable autolink, you will see the buddypress search link. If you
disable autolink, you will see the "mailto" link.
* Image, File, Web: For this type of field, Autolink is not working. It does not matter you
enable/disable it, you will always see the same value displayed.
* Added new filters to change the way the fields are displayed. You can use now
'bxcft_NAMEOFTYPE_display_filter' to change the way the field are displayed. Replace
NAMEOFTYPE with the name of the type like "birthdate", "email", "web", ...
Please stop using 'bxcft_show_field_value'. This filter will be removed in version 3.0.
* Added a filter to change the upload dir for images and files. 'bxcft_upload_dir'.
Override this filter to change the folder where files and images are saved.
* Translation file and spanish language updated.

= Previous versions =
* <http://donmik.com/en/buddypress-xprofile-custom-fields-type/#changelog>

== Upgrade Notice ==
* Emails, Images, Files links were not working since last update 2.4.5. This is because
Autolink feature makes no sense for this type of fields.
https://wordpress.org/support/topic/images-field-broken-on-front-end?replies=2#post-8273463
* Added a new method to all Field Type classes overriding "display_filter" method.
This method will handle the way the field values are displayed.
* Email: When you enable autolink, you will see the buddypress search link. If you
disable autolink, you will see the "mailto" link.
* Image, File, Web: For this type of field, Autolink is not working. It does not matter you
enable/disable it, you will always see the same value displayed.
* Added new filters to change the way the fields are displayed. You can use now
'bxcft_NAMEOFTYPE_display_filter' to change the way the field are displayed. Replace
NAMEOFTYPE with the name of the type like "birthdate", "email", "web", ...
Please stop using 'bxcft_show_field_value'. This filter will be removed in version 3.0.
* Added a filter to change the upload dir for images and files. 'bxcft_upload_dir'.
Override this filter to change the folder where files and images are saved.
* Translation file and spanish language updated.