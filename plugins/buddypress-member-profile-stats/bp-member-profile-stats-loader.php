<?php
/*
Plugin Name: BuddyPress Member Profile Stats
Plugin URI: http://wordpress.org/extend/plugins/buddypress-member-profile-stats/
Description: Adds a few basic count stats and per day avg under member's profile
Author: rich @etiviti
Author URI: http://etivite.com
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.5.0
Text Domain: bp-member-profile-stats
Network: true
*/

//pull in sitewide forums count

/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
function etivite_bp_member_profile_stats_init() {

	if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) )
		load_textdomain( 'bp-member-profile-stats', dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' );

    require( dirname( __FILE__ ) . '/bp-member-profile-stats.php' );
	
	//hook on to profile page
	add_action( 'bp_before_member_header_meta', 'etivite_bp_member_profile_stats_header_meta', 1);
	
	if ( get_option( 'bp_member_profile_stats_display_sidebarme' ) )
		add_action( 'bp_after_sidebar_me', 'etivite_bp_member_profile_stats_sidebar_me', 1);
		
	add_action( bp_core_admin_hook(), 'etivite_bp_member_profile_stats_admin_add_admin_menu' );
	
}
add_action( 'bp_include', 'etivite_bp_member_profile_stats_init', 88 );

//add admin_menu page
function etivite_bp_member_profile_stats_admin_add_admin_menu() {
	global $bp;
	
	if ( !is_super_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/admin/bp-member-profile-stats-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Member Profile Stats', 'bp-member-profile-stats' ), __( 'Profile Stats', 'bp-member-profile-stats' ), 'manage_options', 'bp-member-profile-stats-settings', 'etivite_bp_member_profile_stats_admin' );	

	//set up defaults

}

/* Stolen from Welcome Pack - thanks, Paul! then stolen from boone*/
function etivite_bp_member_profile_stats_admin_add_action_link( $links, $file ) {
	if ( 'buddypress-member-profile-stats/bp-member-profile-stats-loader.php' != $file )
		return $links;

	if ( function_exists( 'bp_core_do_network_admin' ) ) {
		$settings_url = add_query_arg( 'page', 'bp-member-profile-stats-settings', bp_core_do_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	} else {
		$settings_url = add_query_arg( 'page', 'bp-member-profile-stats-settings', is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	}

	$settings_link = '<a href="' . $settings_url . '">' . __( 'Settings', 'bp-activity-extras' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'etivite_bp_member_profile_stats_admin_add_action_link', 10, 2 );

?>
