<?php
/*
 Plugin Name: BuddyPress Restrict Group Creation
 Plugin URI: http://wordpress.org/extend/plugins/buddypress-restrict-group-creation/
 Description: Restrict group creation and settings to certain WP Role/Capabilities.
 Author: rich fuller - rich! @ etiviti
 Author URI: http://buddypress.org/developers/nuprn1/
 License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
 Version: 0.3.0
 Text Domain: bp-restrictgroups
 Site Wide Only: true
*/

//
// if you have checked 'demote group creator to mod' enabled
// You may auto-assign an user_id to all groups as group admin and/or group mod
//
//define( 'BP_RESTRICTGROUP_AUTOADD_ADMIN_USER_ID', 1 );
//define( 'BP_RESTRICTGROUP_AUTOADD_MOD_USER_ID', 2 );

//
// TODO'ing
//

//- gui to select users for auto join

//not cool but we do a lot of funky chicken steps to work around group creation
require( dirname( __FILE__ ) . '/bp-restrict-group-creation.php' );

/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
function bp_restrictgroups_init() {

    
	
}
add_action( 'bp_init', 'bp_restrictgroups_init' );

//add admin_menu page
function bp_restrictgroups_add_admin_menu() {
	global $bp;
	
	if ( !is_super_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/admin/bp-restrict-group-creation-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Restrict Group Creation Admin', 'bp-restrictgroups' ), __( 'Restrict Group Creation', 'bp-restrictgroups' ), 'manage_options', 'bp-restrictgroups-settings', 'bp_restrictgroups_admin' );	

	//set up defaults
	add_option('bp_restrictgroups_cap_low', 'edit_posts' ); //contributor
	
	add_option('bp_restrictgroups_cap_enable_forum', 'upload_files' ); //author
	add_option('bp_restrictgroups_cap_enable_status', maybe_serialize(array( 'private' => 'upload_files', 'hidden' => 'upload_files' )) );

	add_option('bp_restrictgroups_demote_creator_id', true );
	
	add_option('bp_restrictgroups_css_button', '#previous-next { display:none; } form#groups-directory-form .button { display: none; }' );
}

//loader file never works - as it doesn't hook the admin_menu
if ( defined( 'BP_VERSION' ) ) {
	add_action( 'admin_menu', 'bp_restrictgroups_admin_init' );
} else {
	add_action( 'bp_init', 'bp_restrictgroups_admin_init');
}

function bp_restrictgroups_admin_init() {
	add_action( 'admin_menu', 'bp_restrictgroups_add_admin_menu', 20 );
}
?>