=== Register IP - Multiste ===
Contributors: Ipstenu, JohnnyWhite2007
Tags: IP, log, register, multisite, wpmu
Requires at least: 3.1
Tested up to: 3.2.1
Stable tag: 1.2
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5227973

When a new user registers, their IP address is logged for the admins.

== Description ==

Spam is one thing, but trolls and sock puppets are another.  Sometimes people just decide they're going to be jerks and create multiple accounts with which to harass your honest users.  This plugin helps you fight back by logging the IP address used at the time of creation.

When a user registers, their IP is logged in the `wp_usermeta` under the signup_ip key.  Log into your WP install as an Admin and you can look at their profile or the users table to see what it is. For security purposes their IP is not displayed to them when they see their profile.

**Misc**

* [Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5227973)
* [Plugin Site](http://code.ipstenu.org/register-ip-ms/)

== Installation ==

= Single Site =
1. Upload the `register-ip-multisite` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

= MultiSite = 
1. Upload the file `register-ip.php` to the `/wp-content/mu-plugins` directory

OR

1. Upload the `register-ip-multisite` folder to the `/wp-content/plugins/` directory
2. Network Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Why do some users say "None Recorded"? =
This is because the user was registered before the plugin was installed and/or activated.

= Who can see the IP? =
Admins and Network Admins. 

= Does this work on MultiSite? =
Yep! I'd stick it in the `mu-plugins` folder, personally, so it's active no matter where you try and view the users list, but you don't have to.

= If this works on SingleSite why the name? =
There's already a plugin called "Register IP", but it didn't work on MultiSite.  I was originally just going to make this a MultiSite-only install, but then I thought 'Why not just go full bore!'  Of course, I decided that AFTER I requested the name. So you can laugh.

= Does this work with BuddyPress? =
It works with BuddyPress on Multisite, so I presume single-site as well. If not, let me know!

= This makes my screen too wide! =
Sorry about that, but that's what happens when you add in more columns.

= What's the difference between MultiSite and SingleSite installs? =
On multisite only the Network admins who have access to Network Admin -> Users can see the IPs on the user list.


== Screenshots ==
1. Single Site (regular users menu)
2. Multisite (Network Admin -> Users menu)

== Changelog ==

= 1.2 (15 July 2011) =
* Dropping support for 3.0.x 

= 1.1 (3 Dec 2010) =
* Critical fix to correct issue with 3.0.2!

= 1.0 (24 Nov 2010) =
* Forward and backward compatibility with WordPress 3.1! Yay!

= 0.2.1 (08 Nov 2010) =
* Critical Bugfix!  Typo 'wiped out' old IPs listed!

= 0.2 (08 Nov 2010) =
* Internationalization
* Generated POT file so you could do what you want!

= 0.1 =
* Initial fork
* Change to work in MultiSite AND Single Site
* BuddyPress Tested

== Upgrade Notice ==
If you are using 3.0.x DO NOT upgrade. I have made this plugin 3.1 and up only.  I strongly suggest you upgrade WordPress, though.
