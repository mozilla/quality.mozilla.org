<?php
/*
Plugin Name: CMS Tree Page View
Plugin URI: http://eskapism.se/code-playground/cms-tree-page-view/
Description: Adds a CMS-like tree view of all your pages, like the view often found in a page-focused CMS. By using the tree you can edit, view, add pages and even search pages (useful if you have many pages). And with drag and drop you can rearrange the order of your pages. Page management won't get any easier than this!
Version: 0.6
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

require("functions.php");

define( "CMS_TPV_VERSION", "0.6");
define( "CMS_TPV_URL", WP_PLUGIN_URL . '/cms-tree-page-view/');
define( "CMS_TPV_NAME", "CMS Tree Page View"); 

// on admin init: add styles and scripts
add_action( 'admin_init', 'cms_tpv_admin_init' );

// Hook onto dashboard and admin menu
add_action( 'wp_dashboard_setup', "cms_tpv_wp_dashboard_setup" );
add_action( 'admin_menu', "cms_tpv_admin_menu" );
add_action( 'admin_head', "cms_tpv_admin_head" );

// Ajax hooks
add_action('wp_ajax_cms_tpv_get_childs', 'cms_tpv_get_childs');
add_action('wp_ajax_cms_tpv_move_page', 'cms_tpv_move_page');
add_action('wp_ajax_cms_tpv_add_page', 'cms_tpv_add_page');

?>