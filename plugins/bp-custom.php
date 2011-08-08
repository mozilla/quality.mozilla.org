<?php

/*********
 * Activate custom language files
 */
define( 'BPLANG', 'qmo' );

// For Buddypress
if ( file_exists( BP_PLUGIN_DIR . '/bp-languages/buddypress-' . BPLANG . '.mo' ) ) {
    load_textdomain( 'buddypress', BP_PLUGIN_DIR . '/bp-languages/buddypress-' . BPLANG . '.mo' );
}

// For BP Group Email Subscription
if ( file_exists( WP_PLUGIN_DIR . '/buddypress-group-email-subscription/languages/bp-ass-'.BPLANG.'.mo' ) ) {
    load_textdomain( 'bp-ass', WP_PLUGIN_DIR . '/buddypress-group-email-subscription/languages/bp-ass-'.BPLANG.'.mo' );
}

// Add Metrics tab if plugin is enabled
//if ( function_exists('get_bugzilla_stats_for_user') ) {
  function qmo_metrics_setup_nav() {
  	global $bp;
  	bp_core_new_nav_item( array( 
      'name' => __( 'Metrics' ), 
      'slug' => 'metrics', 
      'parent_url' => $bp->loggedin_user->domain . $bp->slug . '/', 'parent_slug' => $bp->slug, 
      'screen_function' => 'qmo_metrics_screen', 
      'position' => 40 ) 
    );
  
  	function qmo_metrics_screen() {
    	add_action( 'bp_template_title', 'my_profile_page_function_to_show_screen_title' );
      bp_core_load_template( apply_filters( 'bp_template_screen', 'members/single/metrics' ) );
  	}
  	
  	function my_profile_page_function_to_show_screen_title() {
  		echo 'Metrics';
  	}
  	
  }
  
  add_action( 'bp_setup_nav', 'qmo_metrics_setup_nav', 10 );
//}

/*********
 * Change the profile tab order so "Profile" comes first and "Settings" comes last.
 */
function qmo_profile_tab_order() {
  global $bp;

  $bp->bp_nav[BP_XPROFILE_SLUG]['position'] = 10;
  $bp->bp_nav[BP_ACTIVITY_SLUG]['position'] = 20;
  $bp->bp_nav[BP_GROUPS_SLUG]['position'] = 30;
  $bp->bp_nav['settings']['position'] = 99;
}
add_action( 'bp_setup_nav', 'qmo_profile_tab_order', 999 );

?>
