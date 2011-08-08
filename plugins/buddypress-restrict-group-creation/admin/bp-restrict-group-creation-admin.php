<?php 

/**
 * Gets an array of capabilities according to each user role.  Each role will return its caps, 
 * which are then added to the overall $capabilities array.
 *
 * Note that if no role has the capability, it technically no longer exists.  Since this could be 
 * a problem with folks accidentally deleting the default WordPress capabilities, the 
 * members_default_capabilities() will return those all the defaults.
 *
 * @since 0.1
 * @return $capabilities array All the capabilities of all the user roles.
 * @global $wp_roles array Holds all the roles for the installation.
 */
function bp_restrictgroups_admin_get_role_capabilities() {
	global $wp_roles;

	$capabilities = array();

	/* Loop through each role object because we need to get the caps. */
	foreach ( $wp_roles->role_objects as $key => $role ) {

		/* Roles without capabilities will cause an error, so we need to check if $role->capabilities is an array. */
		if ( is_array( $role->capabilities ) ) {

			/* Loop through the role's capabilities and add them to the $capabilities array. */
			foreach ( $role->capabilities as $cap => $grant )
				$capabilities[$cap] = $cap;
		}
	}

	/* Return the capabilities array. */
	return $capabilities;
}

/**
 * Checks if a specific capability has been given to at least one role. If it has,
 * return true. Else, return false.
 *
 * @since 0.1
 * @uses members_get_role_capabilities() Checks for capability in array of role caps.
 * @param $cap string Name of the capability to check for.
 * @return true|false bool Whether the capability has been given to a role.
 */
function bp_restrictgroups_admin_check_for_cap( $cap = '' ) {

	/* Without a capability, we have nothing to check for.  Just return false. */
	if ( !$cap )
		return false;

	/* Gets capabilities that are currently mapped to a role. */
	$caps = bp_restrictgroups_admin_get_role_capabilities();

	/* If the capability has been given to at least one role, return true. */
	if ( in_array( $cap, $caps ) )
		return true;

	/* If no role has been given the capability, return false. */
	return false;
}

function bp_restrictgroups_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('bp_restrictgroups_admin') ) {
	
		//check for valid cap and update - if not keep old.
		if( isset($_POST['cap_low'] ) && !empty($_POST['cap_low']) ) {
			if ( bp_restrictgroups_admin_check_for_cap( $_POST['cap_low'] ) ) {
				update_option( 'bp_restrictgroups_cap_low', $_POST['cap_low'] );
			} else {
				echo '<div id="message" class="error"><p>Invalid capability for group creation - please see <a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table">WP Roles and Capabilities</a>.</p></div>';
			}
		} else {
			update_option( 'bp_restrictgroups_cap_low', 'edit_posts' );
			
			echo '<div id="message" class="updated fade"><p>Group creation capability was left blank - this is required - assuming \'edit_posts\'.</p></div>';
		}
	
		//check if enable a cap on the forum option
		if ( function_exists('bp_forums_is_installed_correctly') && bp_forums_is_installed_correctly() ) {
			if( isset($_POST['cap_enable_forum'] )  && !empty($_POST['cap_enable_forum']) ) {
				if ( bp_restrictgroups_admin_check_for_cap( $_POST['cap_enable_forum'] ) ) {
					update_option( 'bp_restrictgroups_cap_enable_forum', $_POST['cap_enable_forum'] );
				} else {
					echo '<div id="message" class="error"><p>Invalid capability for group forum creation - please see <a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table">WP Roles and Capabilities</a>.</p></div>';
				}
			} else {
				//assume our default contributor
				update_option( 'bp_restrictgroups_cap_enable_forum', false );
			}
		}
	
		if ( isset($_POST['cap_enable_status_private'] ) || isset($_POST['cap_enable_status_hidden'] ) ) {
			
			$data = array();
			
			if ( isset($_POST['cap_enable_status_private']) && !empty($_POST['cap_enable_status_private']) ) {
				if ( bp_restrictgroups_admin_check_for_cap( $_POST['cap_enable_status_private'] ) ) {
					$data['private'] = $_POST['cap_enable_status_private'];
				} else {
				
					echo '<div id="message" class="error"><p>Invalid capability for private group - please see <a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table">WP Roles and Capabilities</a>.</p></div>';
				
					if ( bp_restrictgroups_enabled_status( 'private' ) )
						$data['private'] = bp_restrictgroups_enabled_status( 'private' );
				}
			}

			if ( isset($_POST['cap_enable_status_hidden']) && !empty($_POST['cap_enable_status_hidden']) ) {
				if ( bp_restrictgroups_admin_check_for_cap( $_POST['cap_enable_status_hidden'] ) ) {
					$data['hidden'] = $_POST['cap_enable_status_hidden'];
				} else {
				
					echo '<div id="message" class="error"><p>Invalid capability for hidden group - please see <a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table">WP Roles and Capabilities</a>.</p></div>';
				
					if ( bp_restrictgroups_enabled_status( 'hidden' ) ) 
						$data['hidden'] = bp_restrictgroups_enabled_status( 'hidden' );
				}
			}
			
			if ($data && count($data) > 0) {
				update_option( 'bp_restrictgroups_cap_enable_status', maybe_serialize($data) );
			} else {
				update_option( 'bp_restrictgroups_cap_enable_status', false );
			}
		}
		
		//check for valid cap and update - if not keep old.
		if( isset($_POST['demote_creator_id'] ) && !empty($_POST['demote_creator_id']) && (int)$_POST['demote_creator_id'] == 1 ) {
			update_option( 'bp_restrictgroups_demote_creator_id', true );
		} else {
			update_option( 'bp_restrictgroups_demote_creator_id', false );
		}
		
		
		if( isset($_POST['rg_post_count'] ) && !empty($_POST['rg_post_count']) && (int)$_POST['rg_post_count'] == 1 ) {
			
			$p = (int)$_POST['rg_post_count_threshold'];
			
			if ( !$p || $p < 1 )
				$p = 0;
			
			update_option( 'bp_restrictgroups_post_count', array( 'enabled' => true, 'count' => $p) );
		} else {
			update_option( 'bp_restrictgroups_post_count', array( 'enabled' => false, 'count' => 0 ) );
		}
		
		if( isset($_POST['rg_status_count'] ) && !empty($_POST['rg_status_count']) && (int)$_POST['rg_status_count'] == 1 ) {

			$s = (int)$_POST['rg_status_count_threshold'];
			
			if ( !$s || $s < 1 )
				$s = 0;

			update_option( 'bp_restrictgroups_status_count', array( 'enabled' => true, 'count' => $s) );
		} else {
			update_option( 'bp_restrictgroups_status_count', array( 'enabled' => false, 'count' => 0 ) );
		}
		
		if( isset($_POST['rg_friends_count'] ) && !empty($_POST['rg_friends_count']) && (int)$_POST['rg_friends_count'] == 1 ) {

			$f = (int)$_POST['rg_friends_count_threshold'];
			
			if ( !$f || $f < 1 )
				$f = 0;

			update_option( 'bp_restrictgroups_friends_count', array( 'enabled' => true, 'count' => $f) );
		} else {
			update_option( 'bp_restrictgroups_friends_count', array( 'enabled' => false, 'count' => 0 ) );
		}
		
		if( isset($_POST['rg_days_count'] ) && !empty($_POST['rg_days_count']) && (int)$_POST['rg_days_count'] == 1 ) {

			$d = (int)$_POST['rg_days_count_threshold'];
			
			if ( !$d || $d < 1 )
				$d = 0;

			update_option( 'bp_restrictgroups_days_count', array( 'enabled' => true, 'count' => $d) );
		} else {
			update_option( 'bp_restrictgroups_days_count', array( 'enabled' => false, 'count' => 0 ) );
		}
		
		
		//if achievements is installed
		if ( ACHIEVEMENTS_IS_INSTALLED ) {
		
			if( isset($_POST['rg_dpa_count'] ) && !empty($_POST['rg_dpa_count']) && (int)$_POST['rg_dpa_count'] == 1 ) {

				$d = (int)$_POST['rg_dpa_count_threshold'];
				
				if ( !$d || $d < 1 )
					$d = 0;

				update_option( 'bp_restrictgroups_dpa_count', array( 'enabled' => true, 'count' => $d) );
			} else {
				update_option( 'bp_restrictgroups_dpa_count', array( 'enabled' => false, 'count' => 0 ) );
			}
			
			if( isset($_POST['rg_dpa_score'] ) && !empty($_POST['rg_dpa_score']) && (int)$_POST['rg_dpa_score'] == 1 ) {

				$d = (int)$_POST['rg_dpa_score_threshold'];
				
				if ( !$d || $d < 1 )
					$d = 0;

				update_option( 'bp_restrictgroups_dpa_score', array( 'enabled' => true, 'count' => $d) );
			} else {
				update_option( 'bp_restrictgroups_dpa_score', array( 'enabled' => false, 'count' => 0 ) );
			}
			
		}
		
		
		if( isset($_POST['rg_css_buttons'] ) && !empty($_POST['rg_css_buttons']) ) {
			update_option( 'bp_restrictgroups_css_button', $_POST['rg_css_buttons'] );
		} else {
			update_option( 'bp_restrictgroups_css_button', '' );
		}
		
		$updated = true;
	}

if ( bp_restrictgroups_enabled_status( 'hidden' ) ) 
	$rgh = bp_restrictgroups_enabled_status( 'hidden' );
	
if ( bp_restrictgroups_enabled_status( 'private' ) )
	$rgp = bp_restrictgroups_enabled_status( 'private' );

?>	
	<div class="wrap">
		<h2><?php _e( 'Restrict Group Creation', 'bp-restrictgroups' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-restrictgroups' ) . "</p></div>"; endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-restrictgroups-settings' ?>" name="restrictgroups-settings-form" id="restrictgroups-settings-form" method="post">

		<h4><?php _e( 'Roles and Capabilities', 'bp-restrictgroups' ); ?></h4>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="cap_low"><?php _e( 'Create group capability', 'bp-restrictgroups' ) ?></label></th>
					<td><input type="text" name="cap_low" id="cap_low" value="<?php echo get_option( 'bp_restrictgroups_cap_low'); ?>" /></td>
				</tr>
				<?php if ( function_exists('bp_forums_is_installed_correctly') && bp_forums_is_installed_correctly() ) { ?>
				<tr>
					<th><label for="cap_enable_forum"><?php _e('Create group forum capability','bp-restrictgroups') ?></label></th>
					<td><input type="text" name="cap_enable_forum" id="cap_enable_forum" value="<?php echo get_option( 'bp_restrictgroups_cap_enable_forum'); ?>" /> <?php if (!get_option( 'bp_restrictgroups_cap_enable_forum')) echo '(<em>currently disabled</em>)'; ?></td>
				</tr>
				<?php } ?>
				<tr>
					<th><label for="cap_enable_status_private"><?php _e('Create private group capability','bp-restrictgroups') ?></label></th>
					<td><input type="text" name="cap_enable_status_private" id="cap_enable_status_private" value="<?php echo $rgp; ?>" /> <?php if (!$rgp) echo '(<em>currently disabled</em>)'; ?></td>
				</tr>

				<tr>
					<th><label for="cap_enable_status_hidden"><?php _e('Create hidden group capability','bp-restrictgroups') ?></label></th>
					<td><input type="text" name="cap_enable_status_hidden" id="cap_enable_status_hidden" value="<?php echo $rgh; ?>" /> <?php if (!$rgh) echo '(<em>currently disabled</em>)'; ?></td>
				</tr>

				<tr>
					<th><label for="demote_creator_id"><?php _e('Auto demote group creator to group mod','bp-restrictgroups') ?></label></th>
					<td><input type="checkbox" name="demote_creator_id" id="demote_creator_id" value="1"<?php if ( get_option( 'bp_restrictgroups_demote_creator_id') ) { ?> checked="checked"<?php } ?> /></td>
				</tr>
			</table>
			
			<div class="description">
				<p>*Create group capability is required (assumes 'upload_files' - editor - if left blank)</p>
				<p>*If others are left blank, then those features are removed from the group creation steps (ie, no option to select forums)</p>
				<p>*Auto Demote to Mod will ensure the user can not change group settings after group creation. You may auto assign an admin or mod user_id - please check out the readme.txt</p>
				<p>Please refer to the <a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table">Codex for WP Caps</a> Defaults: upload_files => author</p>
			</div>
			
<h4><?php _e( 'Member Registered Since (Days)', 'bp-restrictgroups' ); ?></h4>
			
<?php 
$rgdc = get_option( 'bp_restrictgroups_days_count');
?>
			
			<table class="form-table">
				<tr>
					<th><label for="rg_days_count"><?php _e('Enable Days Since Threshold','bp-restrictgroups') ?></label></th>
					<td><input type="checkbox" name="rg_days_count" id="rg_days_count" value="1"<?php if ( $rgdc['enabled'] ) { ?> checked="checked"<?php } ?> /> Days: <input type="text" name="rg_days_count_threshold" id="rg_days_count_threshold" value="<?php echo $rgdc['count']; ?>" /> </td>
				</tr>			
			</table>
			
			<div class="description">
				<p>*If thresholds are enabled - applicable to any member meeting the wp_cap levels set above. Number of <strong>days</strong> registered.</p>
			</div>
			
		<h4><?php _e( 'Count Threshold Settings', 'bp-restrictgroups' ); ?></h4>

<?php 
$rgpc = get_option( 'bp_restrictgroups_post_count');
$rgsc = get_option( 'bp_restrictgroups_status_count');
$rgfc = get_option( 'bp_restrictgroups_friends_count');
?>

			<table class="form-table">

				<?php if ( bp_is_active( 'friends' ) ) { ?>
				<tr>
					<th><label for="rg_friends_count"><?php _e('Enable Friends Count Threshold','bp-restrictgroups') ?></label></th>
					<td><input type="checkbox" name="rg_friends_count" id="rg_friends_count" value="1"<?php if ( $rgfc['enabled'] ) { ?> checked="checked"<?php } ?> /> Count: <input type="text" name="rg_friends_count_threshold" id="rg_friends_count_threshold" value="<?php echo $rgfc['count']; ?>" /> </td>
				</tr>
				<?php } ?>

				<?php if ( bp_is_active( 'activity' ) ) { ?>
				<tr>
					<th><label for="rg_status_count"><?php _e('Enable Status Count Threshold','bp-restrictgroups') ?></label></th>
					<td><input type="checkbox" name="rg_status_count" id="rg_status_count" value="1"<?php if ( $rgsc['enabled'] ) { ?> checked="checked"<?php } ?> /> Count: <input type="text" name="rg_status_count_threshold" id="rg_status_count_threshold" value="<?php echo $rgsc['count']; ?>" /> </td>
				</tr>
				<?php } ?>

				<?php if ( function_exists('bp_forums_is_installed_correctly') && bp_forums_is_installed_correctly() ) { ?>
				<tr>
					<th><label for="rg_post_count"><?php _e('Enable Forum Post Count Threshold','bp-restrictgroups') ?></label></th>
					<td><input type="checkbox" name="rg_post_count" id="rg_post_count" value="1"<?php if ( $rgpc['enabled'] ) { ?> checked="checked"<?php } ?> /> Count: <input type="text" name="rg_post_count_threshold" id="rg_post_count_threshold" value="<?php echo $rgpc['count']; ?>" /> </td>
				</tr>
				<?php } ?>
				
				
				<?php		
				//if achievements is installed
				if ( ACHIEVEMENTS_IS_INSTALLED == 1 ) {
				
					$rgac = get_option( 'bp_restrictgroups_dpa_count');
					$rgas = get_option( 'bp_restrictgroups_dpa_score');
					?>
				
					<tr>
						<th><label for="rg_dpa_count"><?php _e('Enable Achievement Member Count Threshold','bp-restrictgroups') ?></label></th>
						<td><input type="checkbox" name="rg_dpa_count" id="rg_dpa_count" value="1"<?php if ( $rgac['enabled'] ) { ?> checked="checked"<?php } ?> /> Count: <input type="text" name="rg_dpa_count_threshold" id="rg_dpa_count_threshold" value="<?php echo $rgac['count']; ?>" /> </td>
					</tr>
					
					<tr>
						<th><label for="rg_dpa_score"><?php _e('Enable Achievement Member Score Threshold','bp-restrictgroups') ?></label></th>
						<td><input type="checkbox" name="rg_dpa_score" id="rg_dpa_score" value="1"<?php if ( $rgas['enabled'] ) { ?> checked="checked"<?php } ?> /> Score: <input type="text" name="rg_dpa_score_threshold" id="rg_dpa_score_threshold" value="<?php echo $rgas['count']; ?>" /> </td>
					</tr>
				
				<?php } ?>
				
			</table>
			
			<div class="description">
				<p>*If thresholds are enabled - applicable to any member meeting the wp_cap levels set above.</p>
			</div>
			
			
			<h4><?php _e( 'CSS to Remove Group Create Buttons', 'bp-restrictgroups' ); ?></h4>

			<table class="form-table">

				<tr>
					<th><label for="rg_css_buttons"><?php _e('CSS Style','bp-restrictgroups') ?></label></th>
					<td><textarea rows="5" cols="50" name="rg_css_buttons" id="rg_css_buttons"><?php echo get_option( 'bp_restrictgroups_css_button') ?></textarea></td>
				</tr>

			</table>
			
			<div class="description">
				<p>*If you do not use the default theme or the create group buttons do not show up. Adjust the css style to fit your theme.</p>
			</div>
			
			<?php wp_nonce_field( 'bp_restrictgroups_admin' ); ?>
			
			<p class="submit"><input type="submit" name="submit" value="Save Settings"/></p>
			
		</form>
		
		<h3>About:</h3>
		<div id="plugin-about" style="margin-left:15px;">
		
			<div class="plugin-author">
				<strong>Author:</strong> <a href="http://profiles.wordpress.org/users/nuprn1/"><img style="height: 24px; width: 24px;" class="photo avatar avatar-24" src="http://www.gravatar.com/avatar/9411db5fee0d772ddb8c5d16a92e44e0?s=24&amp;d=monsterid&amp;r=g" alt=""> rich! @ etiviti</a>
				<a href="http://twitter.com/etiviti">@etiviti</a>
			</div>
		
			<p>
			<a href="http://blog.etiviti.com/2010/03/buddypress-restrict-group-creation/">Restrict Group Creation Plugin About Page</a><br/>
			<a href="http://buddypress.org/community/groups/buddypress-restrict-group-creation/">BuddyPress.org Plugin Page</a> (with donation link)
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