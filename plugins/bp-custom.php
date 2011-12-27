<?php

/*********
 * Activate custom language files
 */
define( 'BPLANG', 'qmo' );

// For Buddypress
if ( file_exists( WP_LANG_DIR . '/buddypress-' . BPLANG . '.mo' ) ) {
    load_textdomain( 'buddypress', WP_LANG_DIR . '/buddypress-' . BPLANG . '.mo' );
}


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
