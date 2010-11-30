=== Restrict Categories ===
Contributors: mmuro
Tags: restrict, admin, administration, cms, categories, category
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 1.4

Restrict the categories that users in defined roles can view, add, and edit in the admin panel.

== Description ==

*Restrict Categories* is a plugin that allows you to select which categories users can view, add, and edit in the Posts edit screen.

== Installation ==

1. Upload `restrict-categories` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to <em>Settings > Restrict Categories</em> to configure which roles and categories will be restricted.

== Frequently Asked Questions ==

= Does this work with custom roles I have created? =

Yes!  Roles created through plugins like Members will be listed on <em>Settings > Restrict Categories</em>

= Will this prevent my regular visitors from seeing posts? =

No.  This plugin only affects logged in users in the admin panel.

= I messed up and somehow prevented the Administrator account from seeing certain categories! =

Restrict Categories is an opt-in plugin.  By default, every role has access to every category, depending on the capabilites.
If you check a category box in a certain role, such as Administrator, you will <em>restrict</em> that role to viewing only those categories.

To fix this, go to <em>Settings > Restrict Categories</em>, uncheck <em>all</em> boxes under the Administrator account and save your changes.  You can also click the Reset button to reset all changes to the default configuration.

== Screenshots ==

1. A custom role with selected categories to restrict
2. The Posts edit screen with restricted categories
3. The Categories selection on the Add New Post screen with restricted categories

== Changelog ==

**Version 1.4**

* Fix for bug assuming database table prefix
* Improve compatability with PHP 5.2 and empty array checking
* Added string localization

**Version 1.3**

* Update that removes restricted categories from all terms lists (Category management page, Posts dropdown filter, and New/Edit post category list)
* Fix for "Wrong datatype" bug on checkboxes

**Version 1.2**

* Fix for a bug that would allow restricted users to use the category dropdown filter to gain access to categories

**Version 1.1**

* Updated list of categories to include those that are unassigned
* Fixed a small HTML bug
* Now storing options as an array instead of converting to a string

**Version 1.0**

* Plugin launch!

== Upgrade Notice ==

= 1.4 =
This version fixes problems with error messages.

= 1.3 =
Upgrade for compatibility with WordPress 3.1.

= 1.2 =
Recommended upgrade to correct bug which would allow restricted users to bypass category restriction.

= 1.1 =
This version adds the ability to select unassigned categories.
