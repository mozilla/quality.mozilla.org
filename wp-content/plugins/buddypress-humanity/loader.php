<?php
/*
Plugin Name: Buddypress Humanity
Plugin URI: http://hyperspatial.com/wordpress-development/plugins/buddypress-humanity
Description: Add a question to the Buddypress registration form to combat spam blogs. You choose the question and answer.  Multiple answers are allowed.
Version: 1.1
Requires at least: 2.9.2 / 1.2.4
Tested up to: 3.0 / 1.2.5.2
Author: Adam J Nowak
Author URI: http://hyperspatial.com
License: GPL2
*/

/*
Copyright 2010  Adam J Nowak  (email : adam@hyperspatial.com)

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

//Init 
function bp_humanity_init() {
    require('buddypress-humanity.php');
}
add_action('bp_init','bp_humanity_init');
?>