=== BuddyPress reCAPTCHA ===
Contributors: algorhythm
Donate link: http://algorhythm.de
Tags: spam, google, captcha, buddypress, anti-spam, recaptcha, registration, user
Requires at least: 3.2
Tested up to: 3.2
Stable tag: 0.1

This plugin utilizes reCAPTCHA to help your blog stay clear of spam-registrations.

== Description ==
This Plugin integrates the Google reCAPTCHA Service Modul in the BuddyPress Template Page 'register.php'. The file is located in '/wp-content/themes/{name}/registration/register.php'. The plugin adds the new feature by adding an action on 'bp_before_registration_submit_buttons'. Therefore the 'register.php' must contain the line 'do_action( 'bp_before_registration_submit_buttons' )'. My one was generated with the famous plugin [BuddyPress Template Pack](http://wordpress.org/extend/plugins/bp-template-pack/). It makes a normal WordPress-Template ready for use with [BuddyPress](http://wordpress.org/extend/plugins/buddypress/). The missing files in the template will be added.

= Some Features =
* not yet, the plugin has no admin-page, but it will follow
* at the beginning of the 'bp-recaptcha.php' are some variables to add/change the [reCAPTCHA Api keys](https://www.google.com/recaptcha/admin/create), style of reCAPTCHA, language and texts

= Languages =
* English, but can be switched very simple

Notice: The installation of this plugin will require additional work. Edit the 'bp-recaptcha.php' file to make your settings.

== Installation ==

1. Upload the 'bp-recaptcha' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the Plugins menu in WordPress.
4. Edit the 'bp-recaptcha.php' and add your [reCAPTCHA Api keys](https://www.google.com/recaptcha/admin/create). You must have API keys for the current domain for this plugin to work. You can get it [here](https://www.google.com/recaptcha/admin/create).

== Frequently Asked Questions ==

== Screenshots ==
1. screenshot-1.png

== Changelog ==

= 0.1 =
* Initial Release

== Upgrade Notice ==

= 0.1 =
* Enjoy the plugin!
