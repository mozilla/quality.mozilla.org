=== Plugin Name ===
Contributors: nuprn1, etivite
Donate link: http://etivite.com/wordpress-plugins/donate/
Tags: buddypress, groups, group, group creation, restrict groups, limit groups, limit buddypress groups
Requires at least: PHP 5.2, WordPress 3.2.1, BuddyPress 1.5.1
Tested up to: PHP 5.2.x, WordPress 3.2.1, BuddyPress 1.5.1
Stable tag: 0.5.2

Extend restricting group creation with mappings to WordPress Capabilities and various thresholds

== Description ==

** IMPORTANT **
This plugin has been updated for BuddyPress 1.5.1 (required)

Some features removed: auto join admins and wp cap/role levels for group settings



This plugin extends the default "Restrict group creation to Site Admins?" settings within BuddyPress. Rules for restricting group creation can be attached to each wp capability giving multiple levels of control. The rules are made up of member thresholds: forum posts, friends, status updates, days since registered, total groups created, total group admin and total group mod; third-party plugin: achievements.

= Related Links: = 

* <a href="http://etivite.com" title="Plugin Demo Site">Author's Site</a>
* <a href="http://etivite.com/wordpress-plugins/buddypress-restrict-group-creation/">BuddyPress Restrict Group Creation - About Page</a>
* <a href="http://etivite.com/api-hooks/">BuddyPress and bbPress Developer Hook and Filter API Reference</a>
* <a href="http://twitter.com/etivite">@etivite</a> <a href="https://plus.google.com/114440793706284941584?rel=author">etivite+</a>

== Installation ==

1. Upload the full directory into your wp-content/plugins directory
2. Activate the plugin at the plugin administration page
3. Open the plugin configuration page, which is located under BuddyPress and map the wp-capabilities to overall group creation, forum option, and group status level.

== Frequently Asked Questions ==

= My question isn't answered here =

Please contact me on http://etivite.com


== Changelog ==

= 0.5.2 =

* BUG: tidy up php notices

= 0.5.1 =

* BUG: fix network admin settings page on multisite
* BUG: deleting a rules causes data corruption
* FEATURE: support for locale mo files

= 0.5.0 =

* REMOVED: group setting level restrictions options
* REMOVED: auto join admin/mod and demote creator (see new plugin in repo)
* ADDED: attach "rules" to each wp capability
* BUG: minor updates for 1.5

= 0.3.0 =

* Added Hook for Achievements plugin - total member count and total member score threshold
* Added Hook for third-party to disable group creation -> filter on bp_restrictgroups_user_threshold_check and return false if wanting to block

= 0.2.2 =

* Added CSS field on admin page to remove groupt create buttons (if not using default theme)

= 0.2.1 =

* Added Threshold limits: Post Count, Friends Count, Activity Status Counts, Days Since Registered

= 0.1.6 =

* Removed loader file as some instances did not load the admin menu
* (always hooks on BP_VERSION or bp_init or other bp_only hooks for safe deactivation

= 0.1.5 =

* Add loader file for better if buddypress is disabled

= 0.1.3 =

* Fixed BuddyPress deactivate fatal error

= 0.1.2 =

* Adding CSS to remove Create a Group button

= 0.1.1 =

* First [BETA] version


== Upgrade Notice ==

= 0.5.0 =
* BuddyPress 1.5.1 and higher - required; removed auto join groups (admin/mod - new plugin created in repo); removed group settings restrictions
