<?php
if ( !defined( 'ABSPATH' ) ) exit;

function etivite_bp_restrictgroups_can_create_groups( $can_create, $restricted ) {

	//don't do diddley if bp setting is set to allowed - default 0 (allowed)
	if ( !$restricted )
		return $can_create;

	$rules = maybe_unserialize( get_option( 'etivite_bp_restrictgroups' ) );
	
	$can_create = false;
	
	//user needs to meet just one rule requirement to gain access.
	foreach ($rules as $key => $value) {
		if ( current_user_can( $key ) && etivite_bp_restrictgroups_user_threshold_check( $value ) )
			$can_create = true;
	}
	
	return $can_create;
}
add_filter( 'bp_user_can_create_groups', 'etivite_bp_restrictgroups_can_create_groups', 99, 2 );

//total_group_count

//Come and see the violence inherent in the system. Help! Help! I'm being repressed!
function etivite_bp_restrictgroups_user_threshold_check( $rule ) {
	global $bp;
	
//redo,stack n loop it
	
	if ( $rule['bp_restrictgroups_days_count']['enabled'] ) {
		if ( etivite_bp_restrictgroups_days_since( $bp->loggedin_user->userdata->user_registered ) < $rule['bp_restrictgroups_days_count']['count'] ) {
if ( $rule['display_error'] ) bp_core_add_message( __( 'Sorry, you are not allowed to create groups.', 'buddypress' ) .' '. __( 'Reason: membership duration', 'bp-restrictgroups' ), 'warning' );
			return false;
		}
	}

	if ( $rule['bp_restrictgroups_friends_count']['enabled'] && bp_is_active( 'friends' ) ) {
		if ( friends_get_total_friend_count() < $rule['bp_restrictgroups_friends_count']['count'] ) {
if ( $rule['display_error'] ) bp_core_add_message( __( 'Sorry, you are not allowed to create groups.', 'buddypress' ) .' '. __( 'Reason: friend count', 'bp-restrictgroups' ), 'warning' );
			return false;
		}
	}

	if ( $rule['bp_restrictgroups_status_count']['enabled'] && bp_is_active( 'activity' ) ) {
		if ( etivite_bp_restrictgroups_status_count() < $rule['bp_restrictgroups_status_count']['count'] ) {
if ( $rule['display_error'] ) bp_core_add_message( __( 'Sorry, you are not allowed to create groups.', 'buddypress' ) .' '. __( 'Reason: activity updates', 'bp-restrictgroups' ), 'warning' );
			return false;
		}
	}

    if ( $rule['bp_restrictgroups_created_count']['enabled'] ) {
		if ( etivite_bp_restrictgroups_created_count() >= $rule['bp_restrictgroups_created_count']['count'] ) {
if ( $rule['display_error'] ) bp_core_add_message( __( 'Sorry, you are not allowed to create groups.', 'buddypress' ) .' '. __( 'Reason: groups created', 'bp-restrictgroups' ), 'warning' );
			return false;
		}
	}

    if ( $rule['bp_restrictgroups_admin_count']['enabled'] ) {
		$admin_total = BP_Groups_Member::get_is_admin_of( $bp->loggedin_user->id );
		if ( $admin_total["total"] >= $rule['bp_restrictgroups_admin_count']['count'] ) {
if ( $rule['display_error'] ) bp_core_add_message( __( 'Sorry, you are not allowed to create groups.', 'buddypress' ) .' '. __( 'Reason: group adminstrator', 'bp-restrictgroups' ), 'warning' );
			return false;
		}
	}
    if ( $rule['bp_restrictgroups_mod_count']['enabled'] ) {
		$mod_total = BP_Groups_Member::get_is_mod_of( $bp->loggedin_user->id );
		if ( $mod_total["total"] >= $rule['bp_restrictgroups_mod_count']['count'] ) {
if ( $rule['display_error'] ) bp_core_add_message( __( 'Sorry, you are not allowed to create groups.', 'buddypress' ) .' '. __( 'Reason: group moderator', 'bp-restrictgroups' ), 'warning' );
			return false;
		}
	}

	if ( $rule['bp_restrictgroups_post_count']['enabled'] && bp_is_active( 'forums' ) ) {
		if ( etivite_bp_restrictgroups_post_count() <  $rule['bp_restrictgroups_post_count']['count'] ) {
if ( $rule['display_error'] ) bp_core_add_message( __( 'Sorry, you are not allowed to create groups.', 'buddypress' ) .' '. __( 'Reason: group forum posting', 'bp-restrictgroups' ), 'warning' );
			return false;
		}
	}

	//if achievements is installed
	if ( defined('ACHIEVEMENTS_IS_INSTALLED') && ACHIEVEMENTS_IS_INSTALLED == 1 ) {

		if ($rule['bp_restrictgroups_dpa_count']['enabled'] ) {
			if ( dpa_get_total_achievement_count_for_user() < $rule['bp_restrictgroups_dpa_count']['count'] ) {
if ( $rule['display_error'] ) bp_core_add_message( __( 'Sorry, you are not allowed to create groups.', 'buddypress' ) .' '. __( 'Reason: achievements', 'bp-restrictgroups' ), 'warning' );
				return false;
		}
		}
	
		if ( $rule['bp_restrictgroups_dpa_score']['enabled'] ) {
			if ( dpa_get_member_achievements_score() < $rule['bp_restrictgroups_dpa_score']['count'] ) {
if ( $rule['display_error'] ) bp_core_add_message( __( 'Sorry, you are not allowed to create groups.', 'buddypress' ) .' '. __( 'Reason: achievements', 'bp-restrictgroups' ), 'warning' );
				return false;
			}
		}
	
	}
	
	return apply_filters( 'etivite_bp_restrictgroups_user_threshold_check', true );

}

function etivite_bp_restrictgroups_status_count( ) {
	global $bp, $wpdb;
	
	return $wpdb->get_var( $wpdb->prepare( "SELECT count(a.id) FROM {$bp->activity->table_name} a WHERE a.user_id = {$bp->loggedin_user->id} AND type = 'activity_update' AND a.component = '{$bp->activity->id}'" ) );
}

function etivite_bp_restrictgroups_post_count( ) {
	global $bp, $wpdb, $bbdb;
	
	do_action( 'bbpress_init' );
		
	return $wpdb->get_var( $wpdb->prepare( "SELECT count(post_id) FROM {$bbdb->posts} WHERE poster_id = {$bp->loggedin_user->id} AND post_status = 0" ) );
}

function etivite_bp_restrictgroups_created_count( ) {
	global $bp, $wpdb;

	return $wpdb->get_var( $wpdb->prepare( "SELECT count(id) FROM {$bp->groups->table_name} WHERE creator_id = %d", $bp->loggedin_user->id ) );
}

function etivite_bp_restrictgroups_days_since( $older_date, $newer_date = false ) {

	if ( !is_numeric( $older_date ) ) {
		$time_chunks = explode( ':', str_replace( ' ', ':', $older_date ) );
		$date_chunks = explode( '-', str_replace( ' ', '-', $older_date ) );

		$older_date = gmmktime( (int)$time_chunks[1], (int)$time_chunks[2], (int)$time_chunks[3], (int)$date_chunks[1], (int)$date_chunks[2], (int)$date_chunks[0] );
	}

	/* $newer_date will equal false if we want to know the time elapsed between a date and the current time */
	/* $newer_date will have a value if we want to work out time elapsed between two known dates */
	$newer_date = ( !$newer_date ) ? gmmktime( gmdate( 'H' ), gmdate( 'i' ), gmdate( 's' ), gmdate( 'n' ), gmdate( 'j' ), gmdate( 'Y' ) ) : $newer_date;

	/* Difference in seconds */
	$since = $newer_date - $older_date;

	/* Something went wrong with date calculation and we ended up with a negative date. */
	if ( 0 > $since )
		return 0;

	$count = floor( $since / 86400 );

	return $count;
}

?>
