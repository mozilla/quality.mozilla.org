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


/*********
 * Change the profile tab order so "Profile" comes first.
 */
function qmo_profile_tab_order() {
  global $bp;

  $bp->bp_nav[BP_XPROFILE_SLUG]['position'] = 10;
  $bp->bp_nav[BP_ACTIVITY_SLUG]['position'] = 20;
  $bp->bp_nav[BP_GROUPS_SLUG]['position'] = 30;
  $bp->bp_nav['settings']['position'] = 40;
}
add_action( 'bp_setup_nav', 'qmo_profile_tab_order', 999 );

?>
