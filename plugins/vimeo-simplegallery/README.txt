=== Vimeo SimpleGallery ===
Contributors: stiand
Donate link:
Tags: vimeo, gallery
Requires at least: 2.5
Tested up to: 3.0.1
Stable tag: 0.2

A Vimeo Plugin, that let's you add a gallery of videos to any Page or Post. Use [vimeogallery]{vimeo-links}[/vimeogallery], with a list of Vimeo-links separated with linebreaks to add a gallery. See also <a href="http://www.stianandreassen.com/plugins/youtube-simplegallery/">YouTube SimpleGallery</a>.

== Installation ==
1. Unzip the archive
2. Upload the folder "vimeo-simplegallery" to "/wp-content/plugins/"
3. Activate the Plugin in the WordPress Dashboard
4. Change settings on the Options page (or use standard settings)
5. Download and install <a href="http://wordpress.org/extend/plugins/thickbox/">Thickbox</a> or <a href="http://wordpress.org/extend/plugins/shadowbox-js/">Shadowbox JS</a> if you want to show videos in a box on your site.

To add a Vimeo SimpleGallery to a page or a post, simply use the following code:

	[vimeogallery]
	http://vimeo.com/14815990
	http://vimeo.com/13470805
	http://vimeo.com/14844291
	http://vimeo.com/14736551
	[/vimeogallery]

Titles/description is optional. Add it before the link and separate with | (pipe).

Note that each URI must be separated with a linebreak.

== Description ==
This plugin let's you add a gallery of Vimeo-videos to your site, displaying thumbnails for each video. With <a href="http://wordpress.org/extend/plugins/thickbox/">Thickbox</a>  or <a href="http://wordpress.org/extend/plugins/shadowbox-js/">Shadowbox JS</a> installed you can chose to open videos in a box on your site, rather than going to Vimeo.com

To add a Vimeo SimpleGallery to a page or a post, simply use the following code:

	[vimeogallery]
	http://vimeo.com/14815990
	http://vimeo.com/13470805
	http://vimeo.com/14844291
	http://vimeo.com/14736551
	[/vimeogallery]

The plugin fetches titles from Vimeo automatically, as well as height and width for the video.

== Screenshots ==
1. Settings let's you chose if Thickbox or Shadowbox should be used, if titles should be fetched automatically, and to use included CSS.
2. The gallery will show on your page like this
3. If Thickbox or Shadowbox is active, the embedded video will show in a box on your site

== Changelog ==

= 0.2 BETA =
* Improved embedding of videos for non-Flash devices. 
* Added option to open links in new window/tab when going directly to Vimeo.com

= 0.1 BETA =
* First release