<?php


//kill the submit button
function bp_restrictgroups_insert_head() { 
	global $bp;
	
	if ( is_super_admin() )
		return;

	if ( !bp_restrictgroups_is_group_page_check() )
		return;

	if ( current_user_can( get_option('bp_restrictgroups_cap_low') ) && bp_restrictgroups_user_threshold_check() )
		return;

	echo '<style type="text/css">'. get_option('bp_restrictgroups_css_button') .'</style>';
}
add_action( 'bp_head', 'bp_restrictgroups_insert_head');



//axe the steps
function bp_restrictgroups_create_group( ) {
	global $bp;
	
	//need to add the slug to the list
	$bp->groups->forbidden_names[] = 'closed';
	
	if ( is_super_admin() )
		return;
		
	if ( !bp_restrictgroups_is_group_page_check() )
		return;
	
	if ( current_user_can( get_option('bp_restrictgroups_cap_low') ) && bp_restrictgroups_user_threshold_check() )
		return;
	
	//clear current steps
	unset($bp->groups->group_creation_steps);
	
	$bp->groups->group_creation_steps['closed'] = array( 'name' => __( 'Closed', 'bp-restrictgroups' ), 'position' => 0 );
	
	add_action( 'groups_custom_create_steps', 'bp_restrictgroups_custom_create_step_restrict', 1 );
}
add_action( 'groups_setup_globals', 'bp_restrictgroups_create_group', 1000 );

//wait, what, why... the above is called before other extensions may hook into the group extension api and declare a 'step' - so just tidy it up
function bp_restrictgroups_create_group_steps( ) {
	global $bp;
	
	if ( is_super_admin() )
		return;
	
	if ( !bp_restrictgroups_is_group_page_check() )
		return;
	
	if ( current_user_can( get_option('bp_restrictgroups_cap_low') ) && bp_restrictgroups_user_threshold_check() )
		return;
	
	//clear current steps
	unset($bp->groups->group_creation_steps);
	
	$bp->groups->group_creation_steps['closed'] = array( 'name' => __( 'Closed', 'bp-restrictgroups' ), 'position' => 0 );
}
add_action( 'bp_before_create_group', 'bp_restrictgroups_create_group_steps', 1000 );

//give the user some feedback
function bp_restrictgroups_custom_create_step_restrict( ) {
	global $bp;
	
	if ( bp_is_group_creation_step( 'closed' ) ) : ?>
		<h4><?php _e('New Group creation is closed', 'bp-restrictgroups') ?></h4>
	<?php endif;
	
}

//add our new step to replace the group settings
function bp_restrictgroups_create_group_settings( ) {
	global $bp;
	
	//need to add the slug to the list
	$bp->groups->forbidden_names[] = 'group-settings-restricted';
	
	if ( is_super_admin() )
		return;
	
	//clear current steps
	unset($bp->groups->group_creation_steps['group-settings']);
	
	$bp->groups->group_creation_steps['group-settings-restricted'] = array( 'name' => __( 'Settings', 'buddypress' ), 'position' => 10 );
	
	add_action( 'groups_custom_create_steps', 'bp_restrictgroups_custom_create_step_settings', 1 );
	
}
add_action( 'groups_setup_globals', 'bp_restrictgroups_create_group_settings', 1000 );

//our new group settings page
function bp_restrictgroups_custom_create_step_settings( ) { ?>

	<?php if ( bp_is_group_creation_step( 'group-settings-restricted' ) ) : ?>

		<?php do_action( 'bp_before_group_settings_creation_step' ); ?>

		<?php if ( function_exists('bp_wire_install') ) : ?>
			<div class="checkbox">
				<label><input type="checkbox" name="group-show-wire" id="group-show-wire" value="1"<?php if ( bp_get_new_group_enable_wire() ) { ?> checked="checked"<?php } ?> /> <?php _e('Enable comment wire', 'buddypress') ?></label>
			</div>
		<?php endif; ?>

		<?php if ( function_exists('bp_forums_is_installed_correctly') ) : ?>
			<?php if ( bp_forums_is_installed_correctly() ) : ?>
				<?php if ( current_user_can( get_option('bp_restrictgroups_cap_enable_forum') ) ) : ?>
					<div class="checkbox">
						<label><input type="checkbox" name="group-show-forum" id="group-show-forum" value="1"<?php if ( bp_get_new_group_enable_forum() ) { ?> checked="checked"<?php } ?> /> <?php _e('Enable discussion forum', 'buddypress') ?></label>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php if ( is_super_admin() ) : ?>
					<div class="checkbox">
						<label><input type="checkbox" disabled="disabled" name="disabled" id="disabled" value="0" /> <?php printf( __('<strong>Attention Site Admin:</strong> Group forums require the <a href="%s">correct setup and configuration</a> of a bbPress installation.', 'buddypress' ), bp_get_root_domain() . '/wp-admin/admin.php?page=bb-forums-setup' ) ?></label>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>

		<hr />

		<h4><?php _e( 'Privacy Options', 'buddypress' ); ?></h4>

		<div class="radio">

			<label><input type="radio" name="group-status" value="public"<?php if ( 'public' == bp_get_new_group_status() || !bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
				<strong><?php _e( 'This is a public group', 'buddypress' ) ?></strong>
				<ul>
					<li><?php _e( 'Any site member can join this group.', 'buddypress' ) ?></li>
					<li><?php _e( 'This group will be listed in the groups directory and in search results.', 'buddypress' ) ?></li>
					<li><?php _e( 'Group content and activity will be visible to any site member.', 'buddypress' ) ?></li>
				</ul>
			</label>

			<?php if ( bp_restrictgroups_enabled_status( 'private' ) ) : ?>
				<label><input type="radio" name="group-status" value="private"<?php if ( 'private' == bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
					<strong><?php _e( 'This is a private group', 'buddypress' ) ?></strong>
					<ul>
						<li><?php _e( 'Only users who request membership and are accepted can join the group.', 'buddypress' ) ?></li>
						<li><?php _e( 'This group will be listed in the groups directory and in search results.', 'buddypress' ) ?></li>
						<li><?php _e( 'Group content and activity will only be visible to members of the group.', 'buddypress' ) ?></li>
					</ul>
				</label>
			<?php endif; ?>
				
			<?php if ( bp_restrictgroups_enabled_status( 'hidden' ) ) : ?>
				<label><input type="radio" name="group-status" value="hidden"<?php if ( 'hidden' == bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
					<strong><?php _e('This is a hidden group', 'buddypress') ?></strong>
					<ul>
						<li><?php _e( 'Only users who are invited can join the group.', 'buddypress' ) ?></li>
						<li><?php _e( 'This group will not be listed in the groups directory or search results.', 'buddypress' ) ?></li>
						<li><?php _e( 'Group content and activity will only be visible to members of the group.', 'buddypress' ) ?></li>
					</ul>
				</label>
			<?php endif; ?>
			
		</div>

		<?php do_action( 'bp_after_group_settings_creation_step' ); ?>

		<?php wp_nonce_field( 'groups_create_save_group-settings-restricted' ) ?>

	<?php endif; ?>

<?php }

//our new step_save action - also verify the data against caps
function bp_restrictgroups_groups_create_group_step_save( ) {
	global $bp;
	
	$group_status = 'public';
	$group_enable_forum = 1;

	if ( !isset($_POST['group-show-forum']) ) {
		$group_enable_forum = 0;
	} else {
		/* Create the forum if enable_forum = 1 */
		if ( function_exists( 'bp_forums_setup' ) && current_user_can( get_option('bp_restrictgroups_cap_enable_forum') ) && '' == groups_get_groupmeta( $bp->groups->new_group_id, 'forum_id' ) ) {
			groups_new_group_forum();
		}
	}

	if ( 'private' == $_POST['group-status'] && bp_restrictgroups_enabled_status( 'private' ))
		$group_status = 'private';
	else if ( 'hidden' == $_POST['group-status'] && bp_restrictgroups_enabled_status( 'hidden' ))
		$group_status = 'hidden';

	//we need to hook in the user_id change
	if (get_option('bp_restrictgroups_demote_creator_id')) add_action( 'groups_created_group', 'bp_restrictgroups_add_admin', 1, 1);

	if ( !$bp->groups->new_group_id = groups_create_group( array( 'group_id' => $bp->groups->new_group_id, 'status' => $group_status, 'enable_forum' => $group_enable_forum ) ) ) {
		bp_core_add_message( __( 'There was an error saving group details, please try again.', 'buddypress' ), 'error' );
		bp_core_redirect( $bp->root_domain . '/' . $bp->groups->slug . '/create/step/' . $bp->groups->current_create_step . '/' );
	}
	
}
add_action( 'groups_create_group_step_save_group-settings-restricted', 'bp_restrictgroups_groups_create_group_step_save', 1 );

//if a new group is created - lets always make sure our default admin is attached
function bp_restrictgroups_add_admin( $args ) {
	global $bp;
	
	if (!$args)
		return;
	
	if ( is_super_admin() )
		return;
	
	$add = get_option('bp_restrictgroups_demote_creator_id');
	if ($add) {

		$member = new BP_Groups_Member($bp->loggedin_user->id, $args);
		$member->is_admin = 0;
		$member->is_mod = 1;
		$member->user_title = __( 'Group Mod', 'buddypress' );
		$member->date_modified = bp_core_current_time();
		$member->save();

		if ( defined( 'BP_RESTRICTGROUP_AUTOADD_ADMIN_USER_ID' ) ) {
			$member = new BP_Groups_Member(BP_RESTRICTGROUP_AUTOADD_ADMIN_USER_ID, $args);
			$member->is_admin = 1;
			$member->is_mod = 0;
			$member->user_title = __( 'Group Admin', 'buddypress' );
			$member->is_confirmed = 1;
			$member->date_modified = bp_core_current_time();
			$member->save();
		}
		
		if ( defined( 'BP_RESTRICTGROUP_AUTOADD_MOD_USER_ID' ) ) {
			$member = new BP_Groups_Member(BP_RESTRICTGROUP_AUTOADD_MOD_USER_ID, $args);
			$member->is_admin = 0;
			$member->is_mod = 1;
			$member->user_title = __( 'Group Mod', 'buddypress' );
			$member->is_confirmed = 1;
			$member->date_modified = bp_core_current_time();
			$member->save();
		}
	}

}


//simple lookup function
function bp_restrictgroups_enabled_status( $key ) {

	$status = maybe_unserialize( get_option('bp_restrictgroups_cap_enable_status') );
	
	if ($status && $status[$key]) {
		if ( current_user_can( $status[$key] ) )
			return $status[$key];
	}

	return false;
}

//Come and see the violence inherent in the system. Help! Help! I'm being repressed!
function bp_restrictgroups_user_threshold_check() {
	global $bp;
	
	$rgdc = maybe_unserialize(get_option( 'bp_restrictgroups_days_count'));
	if ( $rgdc['enabled'] ) {
		if ( bp_restrictgroups_days_since( $bp->loggedin_user->userdata->user_registered ) < $rgdc['count'] )
			return false;
	}

	$rgfc = maybe_unserialize(get_option( 'bp_restrictgroups_friends_count'));
	if ( $rgfc['enabled'] && bp_is_active( 'friends' ) ) {
		if ( friends_get_total_friend_count() < $rgfc['count'] )
			return false;
	}

	$rgsc = maybe_unserialize(get_option( 'bp_restrictgroups_status_count'));
	if ( $rgsc['enabled'] && bp_is_active( 'activity' ) ) {
		if ( bp_restrictgroups_status_count() < $rgsc['count'] )
			return false;
	}
	
	$rgpc = maybe_unserialize(get_option( 'bp_restrictgroups_post_count'));
	if ( $rgpc['enabled'] && bp_is_active( 'forums' ) ) {
		if ( bp_restrictgroups_post_count() <  $rgpc['count'] )
			return false;
	}

	//if achievements is installed
	if ( ACHIEVEMENTS_IS_INSTALLED == 1 ) {

		$rgac = maybe_unserialize(get_option( 'bp_restrictgroups_dpa_count'));
		if ( $rgac['enabled'] ) {
			if ( dpa_get_total_achievement_count_for_user() < $rgac['count'] )
				return false;
		}
	
		$rgas = maybe_unserialize(get_option( 'bp_restrictgroups_dpa_score'));
		if ( $rgas['enabled'] ) {
			if ( dpa_get_member_achievements_score() < $rgas['count'] )
				return false;
		}
	
	}
	
	return apply_filters( 'bp_restrictgroups_user_threshold_check', true );

}

function bp_restrictgroups_is_group_page_check() {
	
	if ( bp_is_groups_component() )
		return true;
	
	if ( bp_is_group_create() )
		return true;
	
	return false;

}

function bp_restrictgroups_status_count( ) {
	global $bp, $wpdb;
	
	$count = $wpdb->get_var( $wpdb->prepare( "SELECT count(a.id) FROM {$bp->activity->table_name} a WHERE a.user_id = {$bp->loggedin_user->id} AND type = 'activity_update' AND a.component = '{$bp->activity->id}'" ) );
	
	return $count;
}

function bp_restrictgroups_post_count( ) {
	global $bp, $wpdb, $bbdb;
	
	do_action( 'bbpress_init' );
		
	$count = $wpdb->get_var( $wpdb->prepare( "SELECT count(post_id) FROM {$bbdb->posts} WHERE poster_id = {$bp->loggedin_user->id} AND post_status = 0" ) );
	
	return $count;
}

function bp_restrictgroups_days_since( $older_date, $newer_date = false ) {

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