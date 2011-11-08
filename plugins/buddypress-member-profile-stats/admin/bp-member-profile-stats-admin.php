<?php 

function etivite_bp_member_profile_stats_admin_counts( ) {
	
	$counts = array( 'status','topics','posts','comments','userblogs' );
	
	return $counts;
}

function etivite_bp_member_profile_stats_admin_count_check( $type, $currenttypes ) {
	if ( is_multisite() && ( $type == 'comments' || $type == 'posts' ) ) {
		echo 'disabled';
		return;
	}

	if ( !is_multisite() && $type == 'userblogs' ) {
		echo 'disabled';
		return;
	}

	if ( in_array( $type, $currenttypes) )
		echo 'checked';
		
	return;
}

function etivite_bp_member_profile_stats_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('etivite_bp_member_profile_stats_admin') ) {
	
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
	
	// Get the proper URL for submitting the settings form. (Settings API workaround) - boone
	$url_base = function_exists( 'is_network_admin' ) && is_network_admin() ? network_admin_url( 'admin.php?page=bp-member-profile-stats-settings' ) : admin_url( 'admin.php?page=bp-member-profile-stats-settings' );
	
?>	
	<div class="wrap">
		<h2><?php _e( 'Member Profile Stats', 'bp-member-profile-stats' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-member-profile-stats' ) . "</p></div>"; endif; ?>

		<form action="<?php echo $url_base; ?>" name="bp-member-profile-stats-settings-form" id="bp-member-profile-stats-settings-form" method="post">

			<h4><?php _e( 'Display total counts for:', 'bp-member-profile-stats' ); ?></h4>

			<table class="form-table">
				<?php

				$enabledcounts = (array) get_option( 'bp_member_profile_stats_displaycounts');
				$totalcounts = etivite_bp_member_profile_stats_admin_counts();

				foreach ($totalcounts as $count) { ?>
					<tr>
						<th><label for="type-<?php echo $count ?>"><?php echo $count ?></label></th>
						<td><input id="type-<?php echo $count ?>" type="checkbox" <?php etivite_bp_member_profile_stats_admin_count_check( $count, $enabledcounts ); ?> name="ab_profile_counts[]" value="<?php echo $count ?>" /></td>
					</tr>
				<?php } ?>
				
				<?php if ( defined( 'ACHIEVEMENTS_IS_INSTALLED' ) ) { ?>
					<tr>
						<th><label for="type-dpa">Achievements</label></th>
						<td><input id="type-dpa" type="checkbox" <?php etivite_bp_member_profile_stats_admin_count_check( 'dpa', $enabledcounts ); ?> name="ab_profile_counts[]" value="dpa" /></td>
					</tr>				
				<?php } ?>
				
				<tr>
					<th><label for="ab_profile_sidebarme"><?php _e('Display under login sidebar?','bp-community-stats') ?></label></th>
					<td><input type="checkbox" name="ab_profile_sidebarme" id="ab_profile_sidebarme" value="1"<?php if ( get_option( 'bp_member_profile_stats_display_sidebarme' ) ) { ?> checked="checked"<?php } ?> /></td>
				</tr>
				
			</table>
			
			<?php wp_nonce_field( 'etivite_bp_member_profile_stats_admin' ); ?>
			
			<p class="description">Please note: Comments is only for the main buddypress blog - if multisite is used - this does not count comments across the network. (nothing available within wordpress for this). Posts is forum posts within Groups.</p>
			
			<p class="submit"><input type="submit" name="submit" value="Save Settings"/></p>
		
		</form>
		
		<h3>About:</h3>
		<div id="plugin-about" style="margin-left:15px;">
			
			<p>
			<a href="http://etivite.com/wordpress-plugins/buddypress-member-profile-stats/">Activity Member Profile Stats - About Page</a><br/> 
			</p>
		
			<div class="plugin-author">
				<strong>Author:</strong> <a href="http://profiles.wordpress.org/users/etivite/"><img style="height: 24px; width: 24px;" class="photo avatar avatar-24" src="http://www.gravatar.com/avatar/9411db5fee0d772ddb8c5d16a92e44e0?s=24&amp;d=monsterid&amp;r=g" alt=""> rich @etivite</a><br/>
				<a href="http://twitter.com/etivite">@etivite</a> <a href="https://plus.google.com/114440793706284941584">+etivite</a>
			</div>
		
			<p>
			<a href="http://etivite.com">Author's site</a><br/>
			<a href="http://etivite.com/api-hooks/">Developer Hook and Filter API Reference</a><br/>
			<a href="http://etivite.com/wordpress-plugins/">WordPress Plugins</a><br/>
			</p>
		</div>
		
		
	</div>
<?php
}

?>
