<?php
function etivite_bp_member_profile_stats_header_meta() { 
?>
	<div id="item-member-meta-stats">
		<?php etivite_bp_member_profile_stats_member_since(); ?>
		<?php if ( etivite_bp_member_profile_stats_displaycounts_check('status') ) etivite_bp_member_profile_stats_member_status_daysince(); ?>
		<?php if ( etivite_bp_member_profile_stats_displaycounts_check('topics') ) etivite_bp_member_profile_stats_member_topics_daysince(); ?>
		<?php if ( etivite_bp_member_profile_stats_displaycounts_check('posts') ) etivite_bp_member_profile_stats_member_posts_daysince(); ?>
		<?php if ( etivite_bp_member_profile_stats_displaycounts_check('comments') ) etivite_bp_member_profile_stats_member_comments_daysince(); ?>
		<?php if ( is_multisite() && etivite_bp_member_profile_stats_displaycounts_check('userblogs') ) etivite_bp_member_profile_stats_member_userblogs_daysince(); ?>
		<?php if ( defined( 'ACHIEVEMENTS_IS_INSTALLED' ) && etivite_bp_member_profile_stats_displaycounts_check('dpa') ) etivite_bp_member_profile_stats_member_dpa_daysince(); ?>
		<?php do_action('etivite_bp_member_profile_stats_header_meta'); ?>
	</div>
	<?php
}

function etivite_bp_member_profile_stats_sidebar_me() { 
	global $bp;
	
	//don't insert into sidebar_me if already viewing a profile
	if ( $bp->displayed_user->id )
		return;
?>
	<ul id="item-member-sidebar-stats">
		<?php etivite_bp_member_profile_stats_member_days(); ?>
		<?php if ( etivite_bp_member_profile_stats_displaycounts_check('status') ) etivite_bp_member_profile_stats_member_status(); ?>
		<?php if ( etivite_bp_member_profile_stats_displaycounts_check('topics') ) etivite_bp_member_profile_stats_member_topics(); ?>
		<?php if ( etivite_bp_member_profile_stats_displaycounts_check('posts') ) etivite_bp_member_profile_stats_member_posts(); ?>
		<?php if ( etivite_bp_member_profile_stats_displaycounts_check('comments') ) etivite_bp_member_profile_stats_member_comments(); ?>
		<?php if ( is_multisite() && etivite_bp_member_profile_stats_displaycounts_check('userblogs') ) etivite_bp_member_profile_stats_member_userblogs(); ?>
		<?php if ( defined( 'ACHIEVEMENTS_IS_INSTALLED' ) && etivite_bp_member_profile_stats_displaycounts_check('dpa') ) etivite_bp_member_profile_stats_member_dpa(); ?>
		<?php do_action('etivite_bp_member_profile_stats_sidebar_me'); ?>
	</ul>
	<?php
}


//simple templatetags

//TODO - clean this all up - such a mess


function etivite_bp_member_profile_stats_member_since() {
	echo etivite_bp_member_profile_stats_get_member_since();
}
	function etivite_bp_member_profile_stats_get_member_since() {
		return '<div><em>'. bp_get_displayed_user_username() .'</em>'. __( ' has been a member for ', 'bp-member-profile-stats' ) .'<span class="member-since profile-count">'. etivite_bp_member_profile_stats_get_member_registered() .'.</span></div>';
	}


function etivite_bp_member_profile_stats_member_days() {
	echo etivite_bp_member_profile_stats_get_member_days();
}
	function etivite_bp_member_profile_stats_get_member_days() {
		
		$total_count = etivite_bp_member_profile_stats_days_since();

		if ( $total_count == 0 ) {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' days registered ', 'bp-member-profile-stats' ) . '</li>';
		} else {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' days registered ', 'bp-member-profile-stats' ) . '</li>';
		}

		return apply_filters( 'etivite_bp_member_profile_stats_get_member_days', $content, $total_count );
	}


//simple templatetag for displaying the registered since date
function etivite_bp_member_profile_stats_member_registered() {
	echo etivite_bp_member_profile_stats_get_member_registered();
}
	function etivite_bp_member_profile_stats_get_member_registered() {
		global $bp;

		return apply_filters( 'etivite_bp_member_profile_stats_get_member_registered', esc_attr( bp_core_time_since( $bp->displayed_user->userdata->user_registered ) ) );
	}


function etivite_bp_member_profile_stats_member_status_daysince() {
	echo etivite_bp_member_profile_stats_get_member_status_daysince();
}

	function etivite_bp_member_profile_stats_get_member_status_daysince() {
		
		if ( !bp_is_active( 'activity' ) )
			return;
			
		$daysince = etivite_bp_member_profile_stats_days_since();
		
		$total_count = etivite_bp_member_profile_stats_get_member_status_count();
		
		$content = '<div>';
		
		if ( $total_count == 0 ) {
			$content .= __( '<span class="profile-count profile-count-none">No</span> status updates yet.', 'bp-member-profile-stats' );
		} else if ( $total_count == 1 ) {
			$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' status update', 'bp-member-profile-stats' );
		} else {
			if ( $daysince > 0 ) {
				$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' status updates (', 'bp-member-profile-stats' ) . round( $total_count / $daysince, 2 ) . __( ' updates per day on average)', 'bp-member-profile-stats' );
			} else {
				$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' status updates', 'bp-member-profile-stats' );
			}
		}
		
		$content .= '</div>';
		
		return apply_filters( 'etivite_bp_member_profile_stats_get_member_status_daysince', $content, $total_count );
	}

function etivite_bp_member_profile_stats_member_status() {
	echo etivite_bp_member_profile_stats_get_member_status();
}
	function etivite_bp_member_profile_stats_get_member_status() {
	
		if ( !bp_is_active( 'activity' ) )
		return;
		
		$total_count = etivite_bp_member_profile_stats_get_member_status_count();
		
		if ( $total_count == 0 ) {
			$content = '<li>' . __( ' <span class="profile-count profile-count-none">No</span> updates', 'bp-member-profile-stats' ) . '</li>';
		} else if ( $total_count == 1 ) {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' update', 'bp-member-profile-stats' ) . '</li>';
		} else {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' updates', 'bp-member-profile-stats' ) . '</li>';
		}
		
		return apply_filters( 'etivite_bp_member_profile_stats_get_member_status', $content, $total_count );
	
	}

function etivite_bp_member_profile_stats_get_member_status_count( $user_id = false ) {
	global $bp, $wpdb;
	
	if ( !$user_id )
		$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;
	
	//if no cache is found
	if ( !$count = wp_cache_get( 'etivite_bp_member_profile_stats_get_member_status_'. $user_id, 'bp' ) ) {
		
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT count(a.id) FROM {$bp->activity->table_name} a WHERE a.user_id = {$user_id} AND type = 'activity_update' AND a.component = '{$bp->activity->id}'" ) );
	
		if ( !$count )
			$count == 0;
		
		/* Cache the count */
		if ( !empty( $count ) )
			wp_cache_set( 'etivite_bp_member_profile_stats_get_member_status_'. $user_id, $count, 'bp' );
	}
	
	return $count;
}

//delete cache when removing status update
function etivite_bp_member_profile_stats_status_count_delete_clear_cache( $args ) {

	if ( $args['type'] && $args['type'] == 'activity_update' ) {

		/* Check if the user's latest update has been deleted */
		if ( empty( $args['user_id'] ) )
			$user_id = $bp->loggedin_user->id;
		else
			$user_id = $args['user_id'];
	
		wp_cache_delete( 'etivite_bp_member_profile_stats_get_member_status_'. $user_id );
	
	}
		
}
add_action( 'bp_activity_delete', 'etivite_bp_member_profile_stats_status_count_delete_clear_cache' );

//delete cache when adding status update
function etivite_bp_member_profile_stats_status_count_posted_clear_cache( $content, $user_id, $activity_id ) {
	wp_cache_delete( 'etivite_bp_member_profile_stats_get_member_status_'. $user_id );
}
add_action( 'bp_activity_posted_update', 'etivite_bp_member_profile_stats_status_count_posted_clear_cache', 50, 3 );


function etivite_bp_member_profile_stats_member_posts_daysince() {
	echo etivite_bp_member_profile_stats_get_member_posts_daysince();
}

	function etivite_bp_member_profile_stats_get_member_posts_daysince() {
		
		if ( !bp_is_active( 'forums' ) )
			return;
			
		$daysince = etivite_bp_member_profile_stats_days_since();
		
		$total_count = etivite_bp_member_profile_stats_get_member_post_count();
		
		$content = '<div>';
		
		if ( $total_count == 0 ) {
			$content .= __( '<span class="profile-count profile-count-none">No</span> forum posts yet.', 'bp-member-profile-stats' );
		} else if ( $total_count == 1 ) {
			$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' forum post', 'bp-member-profile-stats' );
		} else {
			if ( $daysince > 0 ) {
				$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' forum posts (', 'bp-member-profile-stats' ) . round( $total_count / $daysince, 2 ) . __( ' posts per day on average)', 'bp-member-profile-stats' );
			} else {
				$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' forum posts', 'bp-member-profile-stats' );
			}
		}
		
		$content .= '</div>';
		
		return apply_filters( 'etivite_bp_member_profile_stats_get_member_posts_daysince', $content, $total_count );
	}

function etivite_bp_member_profile_stats_member_posts() {
	echo etivite_bp_member_profile_stats_get_member_posts();
}
	function etivite_bp_member_profile_stats_get_member_posts() {
	
		if ( !bp_is_active( 'forums' ) )
			return;
		
		$total_count = etivite_bp_member_profile_stats_get_member_post_count();
		
		if ( $total_count == 0 ) {
			$content = '<li>' . __( ' <span class="profile-count profile-count-none">No</span> forum posts', 'bp-member-profile-stats' ) . '</li>';
		} else if ( $total_count == 1 ) {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' forum post', 'bp-member-profile-stats' ) . '</li>';
		} else {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' forum posts', 'bp-member-profile-stats' ) . '</li>';
		}
		
		return apply_filters( 'etivite_bp_member_profile_stats_get_member_posts', $content, $total_count );
	
	}

function etivite_bp_member_profile_stats_get_member_post_count( $user_id = false ) {
	global $bp, $wpdb, $bbdb;
	
	do_action( 'bbpress_init' );
			
	if ( !$user_id )
		$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;
			
	$total_count = $wpdb->get_var( $wpdb->prepare( "SELECT count(post_id) FROM {$bbdb->posts} WHERE poster_id = {$user_id} AND post_status = 0" ) );
		
	if ( !$total_count )
		$total_count == 0;
	
	return $total_count;
}
	
	
	
function etivite_bp_member_profile_stats_member_topics_daysince() {
	echo etivite_bp_member_profile_stats_get_member_topics_daysince();
}

	function etivite_bp_member_profile_stats_get_member_topics_daysince() {
		
		if ( !bp_is_active( 'forums' ) )
			return;
			
		$daysince = etivite_bp_member_profile_stats_days_since();
		
		$total_count = bp_forums_total_topic_count_for_user();
		
		$content = '<div>';
		
		if ( $total_count == 0 ) {
			$content .= __( '<span class="profile-count profile-count-none">No</span> forum topics yet.', 'bp-member-profile-stats' );
		} else if ( $total_count == 1 ) {
			$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' forum topic', 'bp-member-profile-stats' );
		} else {
			if ( $daysince > 0 ) {
				$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' forum topics (', 'bp-member-profile-stats' ) . round( $total_count / $daysince, 2 ) . __( ' topics per day on average)', 'bp-member-profile-stats' );
			} else {
				$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' forum topics', 'bp-member-profile-stats' );
			}
		}
		
		$content .= '</div>';
		
		return apply_filters( 'etivite_bp_member_profile_stats_get_member_topics_daysince', $content, $total_count );
	}

function etivite_bp_member_profile_stats_member_topics() {
	echo etivite_bp_member_profile_stats_get_member_topics();
}
	function etivite_bp_member_profile_stats_get_member_topics() {
	
		if ( !bp_is_active( 'forums' ) )
			return;
		
		$total_count = bp_forums_total_topic_count_for_user();
		
		if ( $total_count == 0 ) {
			$content = '<li>' . __( ' <span class="profile-count profile-count-none">No</span> forum topics', 'bp-member-profile-stats' ) . '</li>';
		} else if ( $total_count == 1 ) {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' forum topic', 'bp-member-profile-stats' ) . '</li>';
		} else {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' forum topics', 'bp-member-profile-stats' ) . '</li>';
		}
		
		return apply_filters( 'etivite_bp_member_profile_stats_get_member_topics', $content, $total_count );
	
	}



function etivite_bp_member_profile_stats_member_comments_daysince() {
	echo etivite_bp_member_profile_stats_get_member_comments_daysince();
}

	function etivite_bp_member_profile_stats_get_member_comments_daysince() {
			
		$daysince = etivite_bp_member_profile_stats_days_since();
		
		$total_count = etivite_bp_member_profile_stats_get_member_comment_count();
		
		$content = '<div>';
		
		if ( $total_count == 0 ) {
			$content .= __( '<span class="profile-count profile-count-none">No</span> blog comments yet.', 'bp-member-profile-stats' );
		} else if ( $total_count == 1 ) {
			$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' blog comment', 'bp-member-profile-stats' );
		} else {
			if ( $daysince > 0 ) {
				$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' blog comments (', 'bp-member-profile-stats' ) . round( $total_count / $daysince, 2 ) . __( ' comments per day on average)', 'bp-member-profile-stats' );
			} else {
				$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' blog comments', 'bp-member-profile-stats' );
			}
		}
		
		$content .= '</div>';
		
		return apply_filters( 'etivite_bp_member_profile_stats_get_member_comments_daysince', $content, $total_count );
	}

function etivite_bp_member_profile_stats_member_comments() {
	echo etivite_bp_member_profile_stats_get_member_comments();
}
	function etivite_bp_member_profile_stats_get_member_comments() {
		
		$total_count = etivite_bp_member_profile_stats_get_member_comment_count();
		
		if ( $total_count == 0 ) {
			$content = '<li>' . __( ' <span class="profile-count profile-count-none">No</span> blog comments', 'bp-member-profile-stats' ) . '</li>';
		} else if ( $total_count == 1 ) {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' blog comment', 'bp-member-profile-stats' ) . '</li>';
		} else {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' blog comments', 'bp-member-profile-stats' ) . '</li>';
		}
		
		return apply_filters( 'etivite_bp_member_profile_stats_get_member_comments', $content, $total_count );
	
	}

function etivite_bp_member_profile_stats_get_member_comment_count( $user_id = false ) {
	global $bp, $wpdb;
	
	if ( !$user_id )
		$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;
			
	$total_count = $wpdb->get_var( $wpdb->prepare( "SELECT count( comment_ID ) FROM {$wpdb->comments} WHERE comment_approved = 1 AND user_id = {$user_id}" ) );
		
	if ( !$total_count )
		$total_count == 0;
	
	return $total_count;
}


function etivite_bp_member_profile_stats_member_userblogs_daysince() {
	echo etivite_bp_member_profile_stats_get_member_userblogs_daysince();
}

	function etivite_bp_member_profile_stats_get_member_userblogs_daysince() {
		
		if ( !bp_is_active( 'blogs' ) )
			return;
			
		$daysince = etivite_bp_member_profile_stats_days_since();
		
		$total_count = bp_blogs_total_blogs_for_user();
		
		$content = '<div>';
		
		if ( $total_count == 0 ) {
			$content .= __( '<span class="profile-count profile-count-none">No</span> user blogs yet.', 'bp-member-profile-stats' );
		} else if ( $total_count == 1 ) {
			$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' user blog', 'bp-member-profile-stats' );
		} else {
			if ( $daysince > 0 ) {
				$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' user blogs (', 'bp-member-profile-stats' );
			} else {
				$content .= '<span class="profile-count">'. $total_count .'</span>'. __( ' user blogs', 'bp-member-profile-stats' );
			}
		}
		
		$content .= '</div>';
		
		return apply_filters( 'etivite_bp_member_profile_stats_get_member_userblogs_daysince', $content, $total_count );
	}

function etivite_bp_member_profile_stats_member_userblogs() {
	echo etivite_bp_member_profile_stats_get_member_userblogs();
}
	function etivite_bp_member_profile_stats_get_member_userblogs() {
	
		if ( !bp_is_active( 'blogs' ) )
			return;
		
		$total_count = bp_blogs_total_blogs_for_user();
		
		if ( $total_count == 0 ) {
			$content = '<li>' . __( ' <span class="profile-count profile-count-none">No</span> user blogs', 'bp-member-profile-stats' ) . '</li>';
		} else if ( $total_count == 1 ) {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' user blog', 'bp-member-profile-stats' ) . '</li>';
		} else {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' user blogs', 'bp-member-profile-stats' ) . '</li>';
		}
		
		return apply_filters( 'etivite_bp_member_profile_stats_get_member_userblogs', $content, $total_count );
	
	}



function etivite_bp_member_profile_stats_member_dpa_daysince() {
	echo etivite_bp_member_profile_stats_get_member_dpa_daysince();
}

	function etivite_bp_member_profile_stats_get_member_dpa_daysince() {
		global $bp;
		
		$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;
		
		$total_count = dpa_get_total_achievement_count_for_user( $user_id );
		$score_count = dpa_get_member_achievements_score( $user_id );
		
		$content = '<div>';
		
		if ( $total_count == 0 ) {
			$content .= __( ' <span class="profile-count profile-count-none">No</span> unlocked achievements', 'bp-member-profile-stats' );
		} else if ( $total_count == 1 ) {
			$content .= '<span class="profile-count">'. $total_count .'</span>' . __( ' achievement for ', 'bp-member-profile-stats' ) .'<span class="profile-count">'. $score_count .'</span>'. __( ' points', 'bp-member-profile-stats' );
		} else {
			$content .= '<span class="profile-count">'. $total_count .'</span>' . __( ' achievements for ', 'bp-member-profile-stats' ) .'<span class="profile-count">'. $score_count .'</span>'. __( ' points', 'bp-member-profile-stats' );
		}
		
		$content .= '</div>';
		
		return apply_filters( 'etivite_bp_member_profile_stats_get_member_dpa_daysince', $content, $total_count, $score_count );
	}


function etivite_bp_member_profile_stats_member_dpa() {
	echo etivite_bp_member_profile_stats_get_member_dpa();
}
	function etivite_bp_member_profile_stats_get_member_dpa() {
		global $bp;
		
		$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;
		
		$total_count = dpa_get_total_achievement_count_for_user( $user_id );
		$score_count = dpa_get_member_achievements_score( $user_id );
		
		if ( $total_count == 0 ) {
			$content = '<li>' . __( ' <span class="profile-count profile-count-none">No</span> unlocked achievements', 'bp-member-profile-stats' ) . '</li>';
		} else if ( $total_count == 1 ) {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' achievement for ', 'bp-member-profile-stats' ) .'<span class="profile-count">'. $score_count .'</span>'. __( ' points', 'bp-member-profile-stats' ) .'</li>';
		} else {
			$content = '<li><span class="profile-count">'. $total_count .'</span>' . __( ' achievements for ', 'bp-member-profile-stats' ) .'<span class="profile-count">'. $score_count .'</span>'. __( ' points', 'bp-member-profile-stats' ) .'</li>';
		}
		
		return apply_filters( 'etivite_bp_member_profile_stats_get_member_dpa', $content, $total_count, $score_count );
	
	}


function etivite_bp_member_profile_stats_displaycounts_check( $optioncount ) {
	$enabledcounts = (array) maybe_unserialize( get_option( 'bp_member_profile_stats_displaycounts') );
	return in_array( $optioncount, $enabledcounts );
}

//helper - internal bp_since includes extra fat we don't need.
function etivite_bp_member_profile_stats_days_since( $newer_date = false ) {
	global $bp;

	$older_date = ( $bp->displayed_user->id ) ? $bp->displayed_user->userdata->user_registered : $bp->loggedin_user->userdata->user_registered;

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
