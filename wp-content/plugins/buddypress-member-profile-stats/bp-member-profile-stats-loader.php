<?php
/*
 Plugin Name: BuddyPress Member Profile Stats
 Plugin URI: http://wordpress.org/extend/plugins/buddypress-member-profile-stats/
 Description: Adds a few basic count stats and per day avg under member's profile
 Author: rich fuller - rich! @ etiviti
 Author URI: http://buddypress.org/developers/nuprn1/
 License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
 Version: 0.4.0
 Text Domain: bp-member-profile-stats
 Site Wide Only: true
*/

//admin page
//options for
//Blog Posts Count
//friends/following

/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
function bp_member_profile_stats_init() {

    require( dirname( __FILE__ ) . '/bp-member-profile-stats.php' );
	
	//hook on to profile page
	add_action( 'bp_before_member_header_meta', 'bp_member_profile_stats_header_meta', 1);
	
	if ( get_option( 'bp_member_profile_stats_display_sidebarme' ) )
		add_action( 'bp_after_sidebar_me', 'bp_member_profile_stats_sidebar_me', 1);
	
}
add_action( 'bp_init', 'bp_member_profile_stats_init' );


//add admin_menu page
function bp_member_profile_stats_admin_add_admin_menu() {
	global $bp;
	
	if ( !is_super_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/admin/bp-member-profile-stats-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Member Profile Stats', 'bp-member-profile-stats' ), __( 'Profile Stats', 'bp-member-profile-stats' ), 'manage_options', 'bp-member-profile-stats-settings', 'bp_member_profile_stats_admin' );	

	//set up defaults

}

//loader file never works - as it doesn't hook the admin_menu
if ( defined( 'BP_VERSION' ) ) {
	add_action( 'admin_menu', 'bp_member_profile_stats_admin_init' );
} else {
	add_action( 'bp_init', 'bp_member_profile_stats_admin_init');
}

function bp_member_profile_stats_admin_init() {
	add_action( 'admin_menu', 'bp_member_profile_stats_admin_add_admin_menu', 25 );
}

?>