<?php
/*
Plugin Name: CMS Tree Page View
Plugin URI: http://eskapism.se/code-playground/cms-tree-page-view/
Description: Adds a CMS-like tree view of all your pages, like the view often found in a page-focused CMS. Use the tree view to edit, view, add pages and search pages (very useful if you have many pages). And with drag and drop you can rearrange the order of your pages. Page management won't get any easier than this!
Version: 0.8.4
Author: Pär Thernström
Author URI: http://eskapism.se/
License: GPL2
*/

/*  Copyright 2010  Pär Thernström (email: par.thernstrom@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

#require("functions.php");
require(dirname(__FILE__)."/functions.php");

define( "CMS_TPV_VERSION", "0.8.4");
define( "CMS_TPV_NAME", "CMS Tree Page View");

// This gives the full URL including http. Apparently is does not work with https (gives http-link instead)
// On my system it will be: http://localhost/wp/wp-content/plugins/cms-tree-page-view/
// define( "CMS_TPV_URL", WP_PLUGIN_URL . '/cms-tree-page-view/');

// Define path to this plugin. This needs to be done in a kinda wierd way because of the fact that I use symblinks on my system.
$plugin_dir_url = plugin_dir_url( __FILE__ ); // Gives wrong path on my system
$arr_authors_wierd_local_paths = array(
	"/Users/bonny/Dropbox/Webb/"
);
$plugin_dir_url = str_replace($arr_authors_wierd_local_paths, "/", $plugin_dir_url);

// Now we have http://localhost/wp/wp-content/pluginscms-tree-page-view/trunk/
// So replace last /trunk/ part
$plugin_dir_url = preg_replace("/\/trunk\/$/", "/", $plugin_dir_url);

// There! Now we should have it.
define( "CMS_TPV_URL", $plugin_dir_url);

// on admin init: add styles and scripts
add_action( 'admin_init', 'cms_tpv_admin_init' );
add_action( 'admin_init', 'cms_tpv_save_settings' );

// Hook onto dashboard and admin menu
add_action( 'wp_dashboard_setup', "cms_tpv_wp_dashboard_setup" );
add_action( 'admin_menu', "cms_tpv_admin_menu" );
add_action( 'admin_head', "cms_tpv_admin_head" );

// Ajax hooks
add_action('wp_ajax_cms_tpv_get_childs', 'cms_tpv_get_childs');
add_action('wp_ajax_cms_tpv_move_page', 'cms_tpv_move_page');
add_action('wp_ajax_cms_tpv_add_page', 'cms_tpv_add_page');

// activation
register_activation_hook( WP_PLUGIN_DIR . "/cms-tree-page-view/index.php" , 'cms_tpv_install' );

// catch upgrade
add_action('plugins_loaded', 'cms_tpv_plugins_loaded' , 1);

// hook onto query
#add_action( 'parse_query', 'cms_tpv_parse_query' );

