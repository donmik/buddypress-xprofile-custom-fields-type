=== Buddypress Xprofile Custom Fields Type ===
Contributors: donmik, dabesa, briannaorg
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donmik%40gmail%2ecom&lc=GB&item_name=donmik%20%2d%20Plugin%20Buddypress%20Xprofile%20Custom%20Fields%20Type&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHostedGuest
Tags: buddypress, xprofile, fields
Requires at least: 3.0
Tested up to: 4.1
Stable tag: 2.1.5
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

Works with [BP Profile Search](https://wordpress.org/plugins/bp-profile-search/ "BP Profile Search plugin"). [Available on Github](https://github.com/donmik/buddypress-xprofile-custom-fields-type "Feel free to contribute"). If you need more fields type, you are free to add them yourself or request me at miguel@donmik.com. Follow me: [donmik.com](http://donmik.com "Follow me") or [@kimnod](http://twitter.com/kimnod "Follow me on Twitter")

== Installation ==

1. Upload the plugin to your 'wp-content/plugins' directory
2. Activate the plugin
3. Go to Users > Profile Fields
4. Create or Edit a field (default buddypress field Name don't allow changing type, it will not work here).
5. In Field Type select, you can see new field's type.
6. Enjoy!

== Frequently Asked Questions ==

<http://donmik.com/en/buddypress-xprofile-custom-fields-type/#faq>
 
== Changelog ==

= 2.1.5 =
* Finally, a filter to limit file upload size and image upload size ("bxcft_files_max_filesize" and "bxcft_images_max_filesize"). See [FAQ for more info](http://donmik.com/en/buddypress-xprofile-custom-fields-type/#faq "FAQ")
* I have moved the initialization of variables that control the types of files and images allowed inside the "init" method because it was not working from the constructor. The filters "images_ext_allowed" and "files_ext_allowed" should work now as expected.
* When the extension of file or image is wrong or the size is greater than maximum, I display now a custom message error and not the default message from buddypress. This message can be customized in language files.
* Spanish, English and French translations updated. The rest is pending, any contribution is welcome.

= Previous versions =
* <http://donmik.com/en/buddypress-xprofile-custom-fields-type/#changelog>

== Upgrade Notice ==
* Added new filters. Translations updated. Read changes <http://donmik.com/en/buddypress-xprofile-custom-fields-type/#changelog>