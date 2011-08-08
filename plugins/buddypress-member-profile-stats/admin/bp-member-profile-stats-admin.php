<?php 

function bp_member_profile_stats_admin_counts( ) {
	
	$counts = array( 'status','topics','posts','comments','userblogs' );
	
	return $counts;
}

function bp_member_profile_stats_admin_count_check( $type, $currenttypes ) {
	if ( in_array( $type, $currenttypes) )
		echo 'checked';
		
	return;
}

function bp_member_profile_stats_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('bp_member_profile_stats_admin') ) {
	
		if( isset($_POST['ab_profile_counts'] ) && !empty($_POST['ab_profile_counts']) ) {
			update_option( 'bp_member_profile_stats_displaycounts', $_POST['ab_profile_counts'] );
		} else {
			update_option( 'bp_member_profile_stats_displaycounts', '' );
		}
		
		if( isset($_POST['ab_profile_sidebarme'] ) && !empty($_POST['ab_profile_sidebarme']) && (int)$_POST['ab_profile_sidebarme'] == 1 ) {
			update_option( 'bp_member_profile_stats_display_sidebarme', true );
		} else {
			update_option( 'bp_member_profile_stats_display_sidebarme', false );
		}
		
		$updated = true;
	}
?>	
	<div class="wrap">
		<h2><?php _e( 'Member Profile Stats', 'bp-member-profile-stats' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-member-profile-stats' ) . "</p></div>"; endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-member-profile-stats-settings' ?>" name="bp-member-profile-stats-settings-form" id="bp-member-profile-stats-settings-form" method="post">

			<h4><?php _e( 'Display total counts for:', 'bp-member-profile-stats' ); ?></h4>

			<table class="form-table">
				<?php

				$enabledcounts = (array) get_option( 'bp_member_profile_stats_displaycounts');
				$totalcounts = bp_member_profile_stats_admin_counts();

				foreach ($totalcounts as $count) { ?>
					<tr>
						<th><label for="type-<?php echo $count ?>"><?php echo $count ?></label></th>
						<td><input id="type-<?php echo $count ?>" type="checkbox" <?php bp_member_profile_stats_admin_count_check( $count, $enabledcounts ); ?> name="ab_profile_counts[]" value="<?php echo $count ?>" /></td>
					</tr>
				<?php } ?>
				
				<?php if ( defined( 'ACHIEVEMENTS_IS_INSTALLED' ) ) { ?>
					<tr>
						<th><label for="type-dpa">Achievements</label></th>
						<td><input id="type-dpa" type="checkbox" <?php bp_member_profile_stats_admin_count_check( 'dpa', $enabledcounts ); ?> name="ab_profile_counts[]" value="dpa" /></td>
					</tr>				
				<?php } ?>
				
				<tr>
					<th><label for="ab_profile_sidebarme"><?php _e('Display under login sidebar?','bp-community-stats') ?></label></th>
					<td><input type="checkbox" name="ab_profile_sidebarme" id="ab_profile_sidebarme" value="1"<?php if ( get_option( 'bp_member_profile_stats_display_sidebarme' ) ) { ?> checked="checked"<?php } ?> /></td>
				</tr>
				
			</table>
			
			<?php wp_nonce_field( 'bp_member_profile_stats_admin' ); ?>
			
			<p class="description">Please note: Comments is only for the main buddypress blog - if multisite is used - this does not count comments across the network. (nothing available within wordpress for this). Posts is forum posts within Groups.</p>
			
			<p class="submit"><input type="submit" name="submit" value="Save Settings"/></p>
		
		</form>
		
		<h3>About:</h3>
		<div id="plugin-about" style="margin-left:15px;">
		
			<div class="plugin-author">
				<strong>Author:</strong> <a href="http://profiles.wordpress.org/users/nuprn1/"><img style="height: 24px; width: 24px;" class="photo avatar avatar-24" src="http://www.gravatar.com/avatar/9411db5fee0d772ddb8c5d16a92e44e0?s=24&amp;d=monsterid&amp;r=g" alt=""> rich! @ etiviti</a>
				<a href="http://twitter.com/etiviti">@etiviti</a>
			</div>
		
			<p>
			<a href="http://blog.etiviti.com/2010/05/buddypress-member-profile-stats-plugin/">BuddyPress Member Profile Stats About Page</a><br/> 
			<a href="http://buddypress.org/community/groups/buddypress-member-profile-stats/">BuddyPress.org Plugin Page</a> (with donation link)
			</p>
			<p>
			<a href="http://blog.etiviti.com">Author's Blog</a><br/>
			<a href="http://blog.etiviti.com/tag/buddypress-plugin/">My BuddyPress Plugins</a><br/>
			<a href="http://blog.etiviti.com/tag/buddypress-hack/">My BuddyPress Hacks</a><br/>
			</p>
			<p>
			<a href="http://etivite.com">Author's Demo BuddyPress site</a><br/>
			<a href="http://etivite.com/groups/buddypress/hacks-and-tips/">BuddyPress Hacks and Tips</a><br/>
			<a href="http://etivite.com/groups/buddypress/hooks/">Developer Hook and Filter API Reference</a>
			</p>
		</div>
		
		
	</div>
<?php
}

?>