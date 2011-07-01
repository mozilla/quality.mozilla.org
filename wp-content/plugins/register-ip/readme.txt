=== Register IP ===
Contributors: Johnny White
Donate link: [Please Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=WR5QY82GJRSJU&lc=US&item_name=Johnny%20White&item_number=JohnnyWhite2007&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted "Make a donatation")
Tags: IP, log, signup, register, new user
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: 0.1

Logs the IP of the user when they register a new account. View the signup IP by going to the Users menu or by editing that user.

== Description ==

Register IP logs the new user's signup IP in the database under wp_usermeta 
with the key of signup_ip. You would be able to see it in WordPress by 
logging into WordPress with an account that has access to the users menu. 
You will be able to see the IP listed with each user and in their profile 
when you go to edit it. For security purposes their IP is not displayed to 
them when they see their profile. This plugin was made to assist those that 
are tired of trying to match a IP up with the persons registered date so that 
the administrator can block the IP from making more accounts or access the 
site. Especially useful if you block a problematic person that spams 
your blog.

== Installation ==

1. Upload the `register-ip` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Why does some users say "No IP Recorded"? =
This is because the user was registered before the plugin was installed and/or activated.

== Screenshots ==
N/A

== Changelog ==

= 0.1 =
* Plugin Creation
* Added Functionality to record the IP address.
* Made the IP show up when you go to edit the user. 
* Made a custom column to show the IP's when listing the users.

== Upgrade Notice ==
N/A