=== Flexi Pages Widget ===
Contributors: SriniG
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8110402
Tags: pages, subpages, menu, hierarchy, sidebar, widget, navigation
Requires at least: 2.7
Tested up to: 3.6-beta3-24407
Stable tag: trunk

A highly configurable WordPress sidebar widget to list pages and sub-pages. User friendly widget control comes with various options. 

== Description ==

Flexi Pages Widget is a highly configurable WordPress sidebar widget to list pages and sub-pages. Can be used as an alternative to the default 'Pages' widget.

Features:

* Option to display sub-pages only in parent page and related pages.
* Option to select and exclude certain pages from getting displayed in the list. Alternatively, only certain pages can be displayed by using the 'include' option.
* Option to include a link to the home page.
* Other options include title, sort column/order, hierarchical/flat format, show date, show as dropdown.
* Multiple instances of the widget. Unlimited number of instances of the widget can be added to the sidebar.
* Instead of using the widget, the function flexipages() can be called from anywhere in the template. For the list of parameters that can be passed on to this function, refer [Other Notes](http://wordpress.org/extend/plugins/flexi-pages-widget/other_notes/).
* Widget options menu is internationalized. Please refer [Other Notes](http://wordpress.org/extend/plugins/flexi-pages-widget/other_notes/) for the full list of languages in which the plugin is localized and translation credits.

== Installation ==

1. Unzip the compressed file and upload the `flexi-pages-widget.php` file (or `flexi-pages-widget` directory) to the `/wp-content/plugins/` directory
1. Activate the plugin 'Flexi Pages' through the 'Plugins' menu in WordPress admin
1. Go to WP admin -> Appearance -> Widgets, add the 'Flexi Pages' widget into the sidebar and choose your options. Multiple instances of the widget can be added to the sidebar.

== Frequently Asked Questions ==

= After selecting a few pages for exclusion, isn't it possible to deselect all pages? There is always one page selected for exclusion. =

It is possible to deselect all pages. Hold the 'Ctrl' key in your keyboard and click on the name of the page that's not getting deselected.

= What does 'Show only related subpages' and 'Show only strictly related subpages' mean? =

When the option 'Show only related subpages' is selected, a subpage is listed only when the user visits the parent and sibling pages of the subpage. Thus, choosing this option will display the top level pages, children and siblings of the current page, and siblings of the parent page.

'Show only strictly related subpages' is same as the above except that siblings of parent page won't be displayed when on a subpage

= Is there an option to list only subpages and hide the parent pages? =

Although such an option does not exist, the 'Include' option can be used to achieve this. Select 'Include' instead of 'Exclude' and select all the pages you want to be listed. Pages left out won't be displayed.

= Is it possible to display only the child-pages of a particular page? =
Yes. In order to achieve this, select the 'Include' option, select just the child-pages to be listed (leave out all other pages), enable the 'Show subpages' option and select 'Show all sub-pages'.

= How to customize the styling of items in the flexipages list? For example, how to indent the sub-pages? =

The flexipages widget inherits the styling of the sidebar elements as defined in the style.css of your theme. But you can customize the widget. The items in the flexipages list go within the class 'flexipages_widget'. So if you want to display the links in green color, try putting the code `.flexipages_widget a { color: #0f0; }` in the style.css of your theme. To indent the sub-pages, try `.flexipages_widget ul ul { margin-left:10px; }`.

= The widget treats a password protected page as any other page. Is there were a way to restrict the widget from showing password protected items until the password has been entered? =

The built-in WP function `get_pages()` treat password protected pages as any other page, and don't have an option to hide password protected pages until the password is entered. Flexi Pages Widget plugin depends on this function, and until the functions provide an option to hide password protected pages, we can't have it.

= The widget doesn't list private pages at all. Is there a way to show private pages when the admin is logged in? =

The built-in WP function `get_pages()` doesn't list private pages and doesn't have an option to show private pages. Flexi Pages Widget plugin depends on this functions, and until thes functions provide an option to show private pages when the admin is logged in, it's not possible for us to show private pages.

= Where do I ask a question about the plugin? =

Leave your questions, suggestions, bug reports, etc., as a comment at the [plugin page](http://srinig.com/wordpress/plugins/flexi-pages/ "Flexi Pages Widget") or through [contact form](http://srinig.com/wordpress/contact/) at the author's website. Questions frequently asked will be incorporated into the FAQ section in future versions of the plugin.

== Screenshots ==

1. Controls for the Flexi Pages Widget

== Localization ==

Versions 1.5.5 and above supports localization. The localization template file (flexipages.pot) can be found in the 'languages' folder of the plugin. The resulting PO and MO files should go in the 'flexi-pages-widget/languages/' directory, and should be named in the format `flexipages-xx_YY.po` and `flexipages-xx_YY.mo` files respectively. Where xx refers to the language code and YY to the locale. For example, the German translation files will have the name `flexipages-de_DE.po` and `flexipages-de_DE.mo`. This xx_YY should be the same as the value you define for WPLANG in wp-config.php.

An application like [poEdit](http://www.poedit.net/) can be used to translate the plugin, or just translate the strings in the flexipages.pot file and send it to the plugin author. All translations sent to the author will be bundled with the next version of the plugin.

As of the current version, Flexi Pages Widget is translated into the following languages:

* Bulgarian (`bg_BG`) by [Team Ajoft](http://www.ajoft.com/)
* Belorussian (`be_BY`) by [Alexander Ovsov](http://webhostinggeeks.com/)
* Catalan (`ca`) by Robert Buj Gelonch
* Czech (`cs_CZ`) by Tomáš Hubka
* Danish (`da_DK`) by [Morten Elm](http://www.dubaifan.dk/)
* German (`de_DE`) by [Frank W. Hempel](http://frank-hempel.de/)
* Filipino (`fil_PH`) by [Morten Elm](http://www.storbyfan.dk/)
* French (`fr_FR`) by Pierre Sudarovich
* Irish (`ga_IE`) by [Ajeet](http://www.apoto.com/)
* Hindi (`hi_IN`) by Ashish Jha, [Outshine Solutions](http://outshinesolutions.com/)
* Bahasa Indonesia (`id_ID`) by [Bejana](http://bejana.com)
* Italian (`it_IT`) by [Gianni Diurno](http://gidibao.net/)
* Lithuanian (`lt_LT`) by [Nata Strazda](www.designcontest.com/)
* Norwegian Bokmål (`nb_NO) by [Tore Johnny Bråtveit](http://www.punktlig-ikt.no/)
* Dutch (`nl_NL`) by [Rene](http://wordpresspluginguide.com/)
* Polish (`pl_PL`) by Mariusz Jackiewicz
* Brazilian Portugese (`pt_BR`) by Tzor More
* Romanian (`ro_RO`) by [Michail Bogdanov](http://www.webhostinghub.com/)
* Russian (`ru_RU`) by [Fat Cow](http://www.fatcow.com)
* Serbian (`sr_RS`) by [Mike Arias](http://www.inmotionhosting.com/)
* Swedish (`sv_SE`) by Ove Kaufeldt
* Turkish (`tr_TR`) by [Hakan Demiray](http://www.dmry.net/)
* Ukrainian (`uk_UA`) by [wpp.pp.ua](http://wpp.pp.ua/)
* Chinese (`zh_CN`) by Kaijia Feng


==flexipages() template function==

Instead of using the widget, the function flexipages() can be called from anywhere in the template.

= Parameteres =

**sort_column** 

(string) Sorts the list of Pages in a number of different ways. The default setting is sort alphabetically by Page Order.

* 'post_title' - Sort Pages alphabetically (by title) - default
* 'menu_order' - Sort Pages by Page Order. N.B. Note the difference between Page Order and Page ID. The Page ID is a unique number assigned by WordPress to every post or page. The Page Order can be set by the user in the Write>Pages administrative panel.
* 'post_date' - Sort by creation time.
* 'post_modified' - Sort by time last modified.
* 'ID' - Sort by numeric Page ID.
* 'post_author' - Sort by the Page author's numeric ID.
* 'post_name' - Sort alphabetically by Post slug. 

**sort_order** 

(string) Change the sort order of the list of Pages (either ascending or descending). The default is ascending. Valid values:

* 'asc' - Sort from lowest to highest (Default).
* 'desc' - Sort from highest to lowest. 

**exclude** 

(string) Define a comma-separated list of Page IDs to be excluded from the list (example: 'exclude=3,7,31'). There is no default value. 

**include**

(string) Only include certain Pages in the list generated by get_pages. Like exclude, this parameter takes a comma-separated list of Page IDs. There is no default value. 

**child_of**

(integer) Displays the sub-pages of a single Page only; uses the ID for a Page as the value. Defaults to 0 (displays all Pages). Note that the child_of parameter will also fetch "grandchildren" of the given ID, not just direct descendants.

* 0 - default, no child_of restriction 

**parent** 
    
(integer) Displays those pages that have this ID as a parent. Defaults to -1 (displays all Pages regardless of parent). Note that this can be used to limit the 'depth' of the child_of parameter, so only one generation of descendants might be retrieved. You must use this in conjuction with the child_of parameter. Feed it the same ID.

* -1 - default, no parent restriction
* 0 - returns all top level pages 

**show_subpages**

* 0 - Do not show sub-pages. List only top level pages.
* 1 - Show sub-pages.
* 2 - Show only related sub-pages. A sub-page is listed only when the user visits the parent and sibling pages of the sub-page. Thus, this will display the top level pages, children and siblings of the current page, and siblings of the parent page.
* 3 - Show only strictly related sub-pages. Similar to '2' above except that siblings of parent page won't be displayed when on a sub-page.

**hierarchy**

(boolean) Display sub-Pages in an indented manner below their parent or list the Pages inline. The default is true (display sub-Pages indented below the parent list item). Valid values:

* 1 (true) - default
* 0 (false) 

**depth**

(integer) This parameter controls how many levels in the hierarchy of pages are to be included in the list generated by wp_list_pages. The default value is 0 (display all pages, including all sub-pages).

* 0 - Pages and sub-pages displayed in hierarchical (indented) form (Default).
* -1 - Pages and sub-pages displayed in flat (no indent) form.
* 1 - Show only top level Pages. Equivalent to 'show_subpages=0'.
* 2 - Value of 2 (or greater) specifies the depth (or level) to descend in displaying Pages. 

**dropdown**

(boolean) Whether to display the items in the widget as list or dropdown.

* 1 (True) - will display the items in dropdown format.
* 0 (False) - default - will display the items as list.

**echo**

(boolean) Toggles the display of the generated list of links or return the list as an HTML text string to be used in PHP. The default value is 1 (display the generated list items). Valid values:

* 1 (True) - default
* 0 (False) 

= Examples =

* `flexipages()` will display the list with default options.

* `flexipages('echo=0')` will *return* the list with default options for other parameters.

* `flexipages('echo=0&show_subpages=0')` will return only top level pages.

* `flexipages('sort_column=ID&exclude=2,10,14')` will display the list with items sorted in order of ID. The page IDs 2, 10 and 14 will be excluded.

== Changelog ==

= v1.6.13 (2013-06-04) =
* Fixes the bug introduced in previous update where an empty home page listing is introduced even when the home page link is disabled.

= v1.6.12 (2013-06-04) =
* Added localization in Filipino and Danish languages.
* Minor fixes.

= v1.6.11.1 (2012-12-13) =
* Re-adding localization files that somehow missed the last update
* Updating WP compatibility info in readme

= v1.6.11 (2012-11-18) =
* Added localization in Serbian, Bulgarian, Belorussian, Chinese and Irish languages.
* Minor fixes.

= v1.6.10 (2011-08-30) =
* 'Go' button for the dropdown display is removed. Selecting a page automatically takes the user to the page.
* Added localization in Bahasa Indonesia and Lithuanian language.
* Minor fixes.

= v1.6.6 (2011-06-17) =
* Hindi localization added.

= v1.6.5 (2011-06-14) =
* Localization in Romanian language added.

= v1.6.4 (2011-06-09) =
* Localization in Czech, Norwegian Bokmål and Polish languages added

= v1.6.3 (2010-04-09) =
* Localization in Italian language added.

= v1.6.2 (2010-03-02) =
* Localization in French language added.

= v1.6.1 (2010-01-30) =
* Fixed the include/hierarchy issue
* 'current_page_ancestor' and 'current_page_parent' class shown.

= v1.6 (2010-01-09) =
* New feature to show the items in the widget as dropdown.
* Core functions have been rewritten with a better logic.
* Localization in Catalan and Dutch languages added.

= v1.5.10 (2009-11-03) =
* Minor fix (closing quote for 'exinclude-values' in line 408)

= v1.5.9 (2009-10-01) =
* Localization in Brazilian Portugese, Swedish and Turkish languages added.

= v1.5.7 (2009-09-22) =
* Ukrainian localization added
* Support for user defined widget arguments before_pagelist and after_pagelist

= v1.5.6 (2009-09-14) =
* German localization added.

= v1.5.5 (2009-09-10) =
* Support for localization added. Russian localization included.
* Roll back to `wp_list_pages()` function. Because `wp_page_menu()` seems not to work properly in some themes.

= v1.5.3 (2009-08-03) =
* Bug fix: Fixed the behaviour where the list won't appear in the posts page if it's chosen as a sub page (front page as static page)

= v1.5.2 (2009-06-30) =
* Bug fix (thanks to John J. Camilleri). Must upgrade.

= v1.5.1 (2009-04-18) =
* Bug fix. Title now doesn't show when there is no items in the list.
* Frequently asked queries about private pages and password protected pages answered in FAQ.

= v1.5 (2009-04-07) =
* *Unlimited* instances of the Flexi Pages Widget can be added to the sidebar.
* New option to show date. This option, when selected displays creation or last modified date next to each page.
* The widgets options gets an overhaul. The list of options in the widget control page as of version 1.5.
	* Title
	* Sort column and sort order
	* Exclude/Include a list of pages
	* Show subpages (or list only top level pages). Show all subpages or only related subpages.
	* List the pages in hierarchical or flat format. If hierarchical, choose depth.
	* Show link to the home page
	* Show date, and choose date format.
* The plugin references `wp_page_menu()` function instead of `wp_list_pages()`. Consequently, version 1.5 will work only with WordPress versions 2.7 and above.

= v1.4.1 (2008-05-21) =
* Bug fixes (issues regarding include/exclude sub-pages only with 'List sub-pages only in parent and related pages in hierarchy' option selected.)

= v1.4 (2008-04-06) =
* Fixed the odd behaviour when the widget is placed below the recent posts widget.
* Removed the redundant check box for home page link in widget controls
* Tested with WordPress 2.5; widget control box styling compatible with WP 2.5

= v1.3 (2008-02-19) =
* Multiple instances of the widget
* Added 'Include pages' option
* `flexipages()` template function
* Other minor improvements

= v1.2 (2007-08-31) =
* Added option to provide a custom text for the home page link
* Custom depth of '-3' will display only parents, siblings and children along with top level pages. Parents' siblings wont be displayed.
* Few other improvements and some optimization.
* Tested with WordPress 2.3-beta1.

= v1.1.2 (2007-08-22) =
* Fixed the missing `</li>` tag for home link
* Added class name (`page_item`, `current_page_item`) for home link

= v1.1.1 (2007-08-17) =
* bug fix
* tested with WordPress 2.2.2

= v1.1 (2007-08-12) =
* bug fix

= v1.0 (2007-08-08) =
* Initial release

== Upgrade Notice ==

= 1.6.13 =
Fixes a little bug introduced in v1.6.12. Upgrade recommended.

= 1.6 =
New feature to display as dropdown added, translation in Catalan and Dutch languages added. Core rewritten. Upgrade recommended.
