<?php
/*
Plugin Name: BuddyPress Restrict Group Creation
Plugin URI: http://wordpress.org/extend/plugins/buddypress-restrict-group-creation/
Description: Extend restricting group creation with mappings to WordPress Capabilities and various thresholds
Author: rich @etivite
Author URI: http://etivite.com
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.5.2
Text Domain: bp-restrictgroups
Network: true
*/

//TODO - really really clean up the admin code page

function etivite_bp_restrictgroups_init() {

	if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) )
		load_textdomain( 'bp-restrictgroups', dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' );
		
	require( dirname( __FILE__ ) . '/bp-restrict-group-creation.php' );
	
	add_action( bp_core_admin_hook(), 'etivite_bp_restrictgroups_add_admin_menu' );
	
}
add_action( 'bp_include', 'etivite_bp_restrictgroups_init', 88 );
//add_action( 'bp_init', 'etivite_bp_restrictgroups_init', 88 );

//add admin_menu page
function etivite_bp_restrictgroups_add_admin_menu() {
	global $bp;
	
	if ( !is_super_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/admin/bp-restrict-group-creation-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Restrict Group Creation Admin', 'bp-restrictgroups' ), __( 'Restrict Group Creation', 'bp-restrictgroups' ), 'manage_options', 'bp-restrictgroups-settings', 'etivite_bp_restrictgroups_admin' );	
}

/* Stolen from Welcome Pack - thanks, Paul! then stolen from boone*/
function etivite_bp_restrictgroups_admin_add_action_link( $links, $file ) {
	if ( 'buddypress-restrict-group-creation/bp-restrict-group-creation-loader.php' != $file )
		return $links;

	if ( function_exists( 'bp_core_do_network_admin' ) ) {
		$settings_url = add_query_arg( 'page', 'bp-restrictgroups-settings', bp_core_do_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	} else {
		$settings_url = add_query_arg( 'page', 'bp-restrictgroups-settings', is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	}

	$settings_link = '<a href="' . $settings_url . '">' . __( 'Settings', 'bp-restrictgroups' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'etivite_bp_restrictgroups_admin_add_action_link', 10, 2 );
?>
