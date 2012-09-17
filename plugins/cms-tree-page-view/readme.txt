=== CMS Tree Page View ===
Contributors: eskapism, MarsApril
Donate link: http://eskapism.se/sida/donate/
Tags: page, pages, posts, custom posts, tree, cms, dashboard, overview, drag-and-drop, rearrange, management, manage, admin
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 0.8.14

Adds a tree of all your pages or custom posts. Use drag & drop to reorder your pages, and edit, view, add, and search your pages.

== Description ==

Adds a CMS-like tree overview of all your pages and custom posts to WordPress - much like the view often found in a page-focused CMS.
Within this tree you can edit pages, view pages, add pages, search pages, and drag and drop pages to rearrange the order.

CMS Tree Page View is a good alternative to plugins such as pageMash, WordPress Page Tree
and My Page Order.

Page management in WordPress won't get any easier than this!

#### Features and highlights:

* View your pages & posts in a tree-view, like you view files in Windows Explorer or the Finder in OS X
* Drag and drop to rearrange/order your pages
* Add pages after or inside a page
* Edit pages
* View pages
* Search pages
* Available for both regular pages and custom posts
* View your site hierarchy directly from the WordPress dashboard
* Support for translation plugin [WPML](http://wordpress.org/extend/plugins/sitepress-multilingual-cms/), so you can manage all the languages of your site

#### Screencast
Watch this screencast to see how easy you could be managing your pages:
[youtube http://www.youtube.com/watch?v=H4BGomLi_FU]

#### Translations/Languages
This plugin is available in the following languages:

* English
* German
* French
* Spanish
* Russian
* Belorussian
* Swedish
* Czech
* Italian
* Dutch
* Hungarian
* Norwegian
* Polish
* Greek
* Danish

#### Making the tree available for your vistors
If you're looking for a version of this page tree that the vistors of your site can use, then check out
this navigation widget called [Nice Navigation](http://wordpress.org/extend/plugins/nice-navigation/).

#### Always show your pages in the admin area
If you want to always have a list of your pages available in your WordPress admin area, please check out the plugin
[Admin Menu Tree Page View](http://wordpress.org/extend/plugins/admin-menu-tree-page-view/).


#### Donation and more plugins
* If you like this plugin don't forget to [donate to support further development](http://eskapism.se/sida/donate/).
* More [WordPress CMS plugins](http://wordpress.org/extend/plugins/profile/eskapism) by the same author.

== Installation ==

1. Upload the folder "cms-tree-page-view" to "/wp-content/plugins/"
1. Activate the plugin through the "Plugins" menu in WordPress
1. Done!

Now the tree with the pages will be visible both on the dashboard and in the menu under pages.

== Screenshots ==

1. The page tree in action
2. Edit, view and add pages (choices visible upon mouse over).
3. Search pages.
4. Drag-and-drop to rearrange/change the order of the pages.
5. The tree is also available on the dashboard and therefore available immediately after you login.
6. The settings page - choose where you want the tree to show up
7. Users of WPML can find all their languages in the tree

== Changelog ==

= 0.8.14 =
- Added Estonian translation

= 0.8.13 =
- Updated Lithuanian language

= 0.8.12 =
- Fix for forever loading tree
- No plus-sign on nodes that has no children

= 0.8.11 =
- Changed the way to find the plugin url. Hopefully works better now. Thanks https://twitter.com/windyjonas for the patch.

= 0.8.10 =
- Updated Polish translation, including .mo-file

= 0.8.9 =
- Added Belarusian translation. thanks Web Geek Science  (<a href="http://webhostinggeeks.com/">Web Hosting Geeks</a>)
- Fixed XSS vulnerability as described here: https://www.htbridge.com/advisory/HTB23083

= 0.8.8 =
- Fix for tree not remembering state
- Fix for tree not opening on first click

= 0.8.7 =
- Updated German translation
- Fixed PHP notice messages
- Updated swedish translation
- Changed the way scripts and styles load, so it won't add scripts and styles to pages it shouldn't add scripts and styles to

= 0.8.6 =
- Ops, forgot the .mo-file for the Danish translation. Hopefully I did it correct this time...

= 0.8.5 =
- Added Danish translation

= 0.8.4 =
- Hopefully fixed so scripts and styles can be loaded over https, if WP is accessed over https.

= 0.8.3 =
- Added Lithuanian translation by www.kerge.lt. Thank you!

= 0.8.2 =
- Celebrating more than 100.000 downloads and as a gift to you, the user of this plugin, I have removed the "Support the author"-text from the right column. No more nagging! Donations are still welcome though...

= 0.8.1 =
- Polish translation added.

= 0.8 =
- Added: You can now show the tree for regular posts. Appearently there are som plugins that use the hierarchy on posts. 
- Fixed: The capability required to show the tree for a post type should now be correct. Previously it was hard-coded to "edit_pages". Thanks to Kevin Behrens, author of plugin Role Scoper, for solving this.

= 0.7.20 =
* Changed caller_get_posts (deprecated since 3.1) to ignore_sticky_posts
* Norwegian translation added by Eigil Moe (http://www.eimoe.com)

= 0.7.19 =
* Greek translation added by Mihalis Papanousis (http://aenaon.biz)
* Hopefully fixed some more problems with columns

= 0.7.18 =
* Second try: Hopefully fixed the problem that moving a page resulted in WPML losing the connection between the languages
* Hungarian translation added
* Small CSS fixes
* Fixed compatiblity issue with ALO EasyMail Newsletter

= 0.7.17 =
* Removed cookie.js
* Updated jstree
* If Keyboard Shortcuts was enabled for a user, title and content of a post could not be edited.
* Drag and drop is now a bit more accurate and less "jerky"
* Hopefully fixed the problem that moving a page resulted in WPML losing the connection between the languages
* Dutch translation added
* Hebrew translation added
* Updated POT-file. Translators may want to check for added or updated words and sentences.
* Fixed a notice-message

= 0.7.16 =
* Fix for wpml-languages with "-" in them, like chinese simplified or chinese traditional.
http://wordpress.org/support/topic/plugin-cms-tree-page-view-broken-for-languages-with-a-in
* Fixed some problems with columns and utf-encoding
* Moved adding page to a box above the tree, so you won't get the feeling that the tree has been deleted when you add a page.

= 0.7.15 =
* Czech translation added
* Italian translation added, by Andrea Bersi (http://www.andreabersi.com)
* require(dirname(__FILE__)."/functions.php"); instead of just require("functions.php");. Should fix problems with for example BackWPup.

= 0.7.14 =
- Added links to PayPal and Flattr, so users who like the plugin can donate.

= 0.7.13 =
- Upgraded jstree to rc2. This fixes the problems with drag & drop and mouse over that occured in WordPress 3.1 beta.

= 0.7.12 =
- Readme-fix...

= 0.7.11 =
- If a post has a custom post status, that status will be shown instead of "undefined". So now CMS Tree Page View works better together with plugins like "Edit flow".

= 0.7.10 =
- CSS images loaded from google via https instead of http. Does this solve the problems you guys with https-sites had?
- Users of IE could not add pages at the right place. All pages where added at the top instead of after or inside another page. Only tested in IE 8, please let me know of the other version..

= 0.7.9 =
- changed so some icons are loaded from ajax.googleapis.com instead of Google Code. Google Code was a bit slow.

= 0.7.8 =
- Something went wrong with last update at wordpress.org, people got 404-error when trying to download plugin. Let's see if this update helps..

= 0.7.7 =
- Added Portuguese translation by Ricardo Tomasi. Thank you!
- Celebration Edition: over 25.000 downloads of this plugin at WordPress.org!

= 0.7.6 =
- You can now view items in the trash. A bit closer to a complete take over of the pages-page :)

= 0.7.5 =
- fixed some notice-errors and switched some deprecated functions
- updated swedish translation
- fixed some strings that where untranslatable and updated POT-file (if I missed any, please let me know)
- no longer allowed to add sub pages to a page with status draft, because if you edit the page and save it, wordpress will forget about the parent (and you will get confused)
- started using hoverIntent for popup instead of regular mouseover, so the popups won't feel so aggressive - or no.. reverted this :(
- when adding a page a text comes up so you know that something is going on
- possible fix for magic fields and other plugins that deal with post columns

= 0.7.4 =
- Updated POT-file, so translators may wan't to check their translations.
- Added Spanish translation by Carlos Janini. Thank you!

= 0.7.3 =
- a page can now be moved above a page with the same menu order. moved page will get the menu order of the page that it's moved aboved, and the other page will get a menu order of previous menu order + 1. i think/hope this is finally solved now!
- using wp_update_post when moving pages (instead of sql directly). this should make this plugin work better with some cache plugins, for example DB Cache Reloaded
- root of tree is added initially, without the need to run an ajax query. loading the root of the tree = super fast! child nodes that are not previosly open are still loaded with ajax, because I want to be sure that the plugin does not hang if there is a page with super-mega-lots of children.

= 0.7.2 =
- pages that the user is not allowed to edit now get "dimmed". they will still be visible becuase a page a user is not allowed to edit, may have a child-page that they are allowed to edit, so the sub-pages must still be accessible
- some problems with Ozh' Admin Drop Down Menu fixed (tree showed posts instead of pages)

= 0.7.1 =
- quick fix: capability edit_pages required to view the tree menu, instead of editor (which led to administrators not being able to view the tree...)

= 0.7 =
- added comment count to pop up
- added support for custom columns in pop up = now you have the same information available in CMS Tree Vage View as in the normal page/post edit screen
- fixed some colors to better match wordpress own style
- editor capability required to view tree. previosly only administators chould see the tree  in the menu, while everyone could view the tree on the dashboard.
- no more infinite loops with role scoper installed
- tested on WordPress Multisite

= 0.6.3 =
- tree is activated for pages during install, so the user does not need to set up anything during first run

= 0.6.2 =
- Was released only as a public beta together with wpml.org, to test the wpml-integration
- Now supports custom post types.
- Now compatible with WPML Multilangual CMS (wpml.org). 
- Uses WordPress own functions at some more places.
- When searching and no posts found you now get a message so you know that there were no matches.
- German translation added, by Thomas Dullnig (www.sevenspire.com). Thank you!
- Lots of code rewritten for this update of CMS Tree Page View, so please let me know if it works or if I broke something!

= 0.6.1 =
- Forgot to close a p-tag correctly. Now it should validate again!
- Fixed a problem where move could seem to not work when trying to move pages when several pages had the same menu_order, so they where sorted by alpha instead.
- fixed a problem with qtranslate that resulted in endless "loading tree..."
- the thank you/need help/please donate-box is re-enabled upon upgrade/re-activation of the plugin. Just so you won't forget that you can donate! :)

= 0.6 =
- updated french translation
- new box for mouse-over/pop-up - please let me know what you think about it
- new box: it's bigger so it's less likely that you slide out of it with your mouse (happend to me all the time! very annoying...) . 
- new box: more information can be fitted there. let me know if there is any information you would like to see in the popup (right now it will show you the last modified date + the id of the page)
- new box: edit and view links are real links now, so you can edit or view pages in for example a new tab
- new box: oh.. and it's much better looking! :)

= 0.5.7 =
- jquery.cookie.js renamed to jquery.biscuit.js to fix problems with apache module mod_security. let me know if it actually works! :)
- updated .pot-file, so translators out there may want to check if everything is up to date

= 0.5.6 =
- password protected posts now show a lock icon (thanks to [Seebz](http://seebz.net) for contributing)

= 0.5.5 =
- ok, now the texts should be translated. for real! thanks for the bug report!

= 0.5.4 =
- when mouse over the litte arrow the cursor is now a hand again. it just feels a little bit better that way.
- some texts where not translated due to wp_localize_script being called before load_plugin_textdomain. thanks for reporting this.

= 0.5.3 =
- link to "add new page" when there were no pages now work
- changed native js prompt to http://abeautifulsite.net/2008/12/jquery-alert-dialogs/ (mostly because you can use your other browser tabs while the dialog/prompt is open)
- added a thank-you-please-donate-box. please do what it says! :)
- started using menu_page_url instead of hard-coding path to plugin
- now requires WordPress 3

= 0.5.2 =
- you could get an error if used together with the "Simple Fields" WordPress plugin (yes, I used the same function name in both plugin! Fool me twice, shame on me.)

= 0.5.1 =
- forgot to add styles to svn

= 0.5 =
- Uses wp_localize_script to translate script. Previous method could lead to 404-error, although the file did exist.
- More valid output
- jsTree upgraded to 1.0rc
- Code rewritten for upgraded jsTree
- Added a "clear search"-button to the search box
- Dashboard widget added again! Hooray!
- Requires WordPress 3 because of jquery 1.4.2. If you are using WP 2.x you can try version 0.4.9 instead: http://downloads.wordpress.org/plugin/cms-tree-page-view.0.4.9.zip

= 0.4.9 =
- added French translation by Bertrand Andres

= 0.4.8 =
- added russian translation by Alexufo (www.serebniti.ru)
- fixed a link that didn't change color on mouse over

= 0.4.7 =
- remove some code that did not belong...
- does not show auto-draft-posts in wp3

= 0.4.6 =
- could get database error because post_content had no default value
- removed usage of console.log and one alert. ouch!
- when adding page inside, several posts could get menu_order = 0, which led to sorting problems

= 0.4.5 =
- added Belorussian translation by [Marcis G.](http://pc.de/)
- settings page did not check checkboxes by default
- tree removed from dashboard due some problems with event bubbling (will be re-added later when problem is fixed)

= 0.4.4 =
- translation now works in javascript (forgot to use load_plugin_textdomain)
- added swedish translation by Måns Jonasson

= 0.4.3 =
- forgot the domain for _e at some places

= 0.4.2 =
- added .pot-file

= 0.4.1 =
- more prepare for translation
- fixed some <? into <?php

= 0.4 =
- uses strict json (fix for jquery 1.4)
- pages with no title now show "untitled" instead of just disappearing
- uses get_the_title instead of getting the title direct from the db, making plugins such as qtranslate work
- preparing for translation, using __ and _e

= 0.3 =
- all | public: works on the dasboard
- all | public: are now loaded using ajax. no more reloads!
- added options page so you can choose where to show the tree (i.e. the dasboard or under "pages"...or both, of course!). only available for admins.
- capability "edit_pages" required to view the tree

= 0.2 =
- Possible fix for Fluency Admin

= 0.1a =
- First public version.


== Still on WordPress 2? ==
If you are using WordPress 2.x you can try this old version instead:
http://downloads.wordpress.org/plugin/cms-tree-page-view.0.4.9.zip

