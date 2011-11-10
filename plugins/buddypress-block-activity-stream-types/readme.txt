=== Plugin Name ===
Contributors: nuprn1, etivite
Donate link: http://etivite.com/donate/
Tags: buddypress, activity stream, activity, block activity
Requires at least: PHP 5.2, WordPress 3.2.1, BuddyPress 1.5.1
Tested up to: PHP 5.3.x, WordPress 3.2.1, BuddyPress 1.5.1
Stable tag: 0.5.1

This plugin will "block" an activity record from being saved to the stream/database. Such as new member registration, joining groups, friendships created.

== Description ==

** IMPORTANT **
This plugin has been updated for BuddyPress 1.5.1

This plugin will "block" an activity record from being saved to the stream/database. Such as new member registration, joining groups, friendships created.

Please note, this will not allow an activity record to be saved into the database at all. You will need to know the "type" of activity record. It is advised NOT to block activity_comment and activity_update activities (will cause errors in buddypress)

What are activity types? BP Core includes several and plugins may register their own when hooking into the activity_record functions. This plugin will scan the activity table for distinct types already logged but will be ever changing due to new plugins.

= Related Links: = 

* <a href="http://etivite.com" title="Plugin Demo Site">Author's Site</a>
* <a href="http://etivite.com/wordpress-plugins/buddypress-block-activity-stream-types/">BuddyPress Block Activity Stream Types - About Page</a>
* <a href="http://etivite.com/api-hooks/">BuddyPress and bbPress Developer Hook and Filter API Reference</a>


== Installation ==

1. Upload the full directory into your wp-content/plugins directory
2. Activate the plugin at the plugin administration page
3. Adjust settings via the Activity Block admin page

== Frequently Asked Questions ==

= How do I exclude a certain activity types? =

You may scan the bp-core code, plugin code, or attempt to view the types already entered into the database.

= How does it work? =

When bp-core or a plugin attempts to record a new activity - this plugin will block the database insert (by nulling out the type prior to saving - bp_activity_type_before_save )

= I blocked a type but the filter still appears in my theme in the dropdown select box =

Certain types are hardcoded into the theme file bp-default/activity/index.php - you may need to remove the html select option

Some plugins may register the do_action( 'bp_activity_filter_options' ) - there is no way to filter these out automatically and may require editing the plugin's core files - remove the action hook.

= I blocked a type but still see the old activity records =

This plugin does not remove previous logged activity items - you'll need to manually delete these.

= I want to block a certain blog on my network from sending activity stream updates =

You may hook on the filter `bp_activity_block_denied_activity_type_check` which provides all activity level data. From here you can provide your own logic to block an activity record. ie, blog_id

See this forum thread for details: http://etivite.com/groups/buddypress/forum/topic/quick-tip-hooking-block-activity-stream-types-plugin-on-a-granular-level/

= I want to unblock a type and see all the old activity records =

Sorry, since you blocked certain types from the database - nothing was ever saved to begin with.


= My question isn't answered here =

Please contact me on http://etivite.com


== Changelog ==

= 0.5.1 =

* BUG: fix network admin settings page on multisite
* FEATURE: support for locale mo files

= 0.5.0 =

* BUG: updated for BuddyPress 1.5.1

= 0.3.0 =

* Feature: New filter hook to allow granular blocking (ie block on a per blog_id)

= 0.2.0 =

* Bug: Invalid reference on PHP 5.3.x

= 0.1.0 =

* First [BETA] version


== Upgrade Notice ==

= 0.5.0 =
* BuddyPress 1.5.1 and higher - required.

== Extra Configuration ==

See this forum thread for details on hooking the type check: http://etivite.com/groups/buddypress/forum/topic/quick-tip-hooking-block-activity-stream-types-plugin-on-a-granular-level/
