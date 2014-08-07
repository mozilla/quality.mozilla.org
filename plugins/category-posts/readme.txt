=== Category Posts Widget ===
Contributors: mkrdip
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=mkrdip%40yahoo%2ecom&lc=US&item_name=Category%20Posts%20Widget&item_number=02&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: category, posts, widget, single category widget, posts widget, category recent posts
Requires at least: 2.8
Tested up to: 3.9.1
Stable tag: 4.0
License: GPLv2 or later 
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a widget that shows the most recent posts from a single category.

== Description ==

Category Posts Widget is a light widget designed to do one thing and do it well: display the most recent posts from a certain category.

= Features =

* Option to change ordering of posts.
* Option to show post thumbnail & set dimension by width & height.
* Set how many posts to show.
* Set which category the posts should come form.
* Option to show the post excerpt and how long the excerpt should be.
* Option to show the post date.
* Option to show the comment count.
* Option to make the widget title link to the category page.
* Multiple widgets.

= Contribute =
While using this plugin if you find any bug or any conflict, please submit an issue at 
[Github](https://github.com/mkrdip/category-posts-widget) (If possible with a pull request). 

== Installation ==

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of Category Posts Widget, 

1. log in to your WordPress dashboard, navigate to the Plugins menu and click Add New. 
2. In the search field type “Category Posts Widget” and click Search Plugins. 
3. Once you’ve found plugin, you can install it by simply clicking “Install Now”. 
4. Then, go to plugins page of WordPress admin activate the plugin. 
5. Now, goto the Widgets page of the Appearance section and configure the Category Posts widget.

= Manual installation =

1. Download the plugin.
2. Upload it to the plugins folder of your blog.
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Now, goto the Widgets page of the Appearance section and configure the Category Posts widget.

== Upgrade Notice ==

* Please consider to re-configure the widget as the latest version has numerous changes from previous.
* Version 4.0 uses CSS file for styling the widget in front end.
* Version 3.0 or later version uses WordPress 2.9's built in post thumbnail functionality.


== Screenshots ==

1. The widget configuration dialog.
2. Front end of the widget using a default WordPress Theme.

== Changelog ==

4.0 

* Added CSS file for post styling 
* Now compaitable with latest versions of WordPress

3.3

* Fixed random sort bug.

3.2

* Added option to change ordering of posts. Defaults to showing newest posts first.

3.1

* Fixed a bug in the thumbnail size registration routine.

3.0

* Added support for WP 2.9's post thumbnail feature.
* Removed support for Simple Post Thumbnails plugin.
* Added option to show the post date.
* Added option to set the excerpt length.
* Added option to show the number of comments.

2.3

* Really tried to fix bug where wp_query global was getting over written by manually instantiating a WP_Query object

2.1

* Fixed bug where wp_query global was getting over written.

2.0

* Updated to use the WP 2.8 widget API.
* Added support for [Simple Post Thumbnails plugin](http://wordpress.org/extend/plugins/simple-post-thumbnails/).
