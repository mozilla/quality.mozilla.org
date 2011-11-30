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
function etivite_bp_restrictgroups_admin_get_role_capabilities() {
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
function etivite_bp_restrictgroups_admin_check_for_cap( $cap = '' ) {

	/* Without a capability, we have nothing to check for.  Just return false. */
	if ( !$cap )
		return false;

	/* Gets capabilities that are currently mapped to a role. */
	$caps = etivite_bp_restrictgroups_admin_get_role_capabilities();

	/* If the capability has been given to at least one role, return true. */
	if ( in_array( $cap, $caps ) )
		return true;

	/* If no role has been given the capability, return false. */
	return false;
}

function etivite_bp_restrictgroups_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['remove'] ) && check_admin_referer('etivite_bp_restrictgroups_admin_remove') ) {
		
		$data = maybe_unserialize( get_option( 'etivite_bp_restrictgroups' ) );
		
		if ($data) {
			
			if( isset($_POST['cap_remove'] ) && !empty($_POST['cap_remove']) ) {
				foreach ( (array) $_POST['cap_remove'] as $id ) {
					unset( $data[$id] );
				}
			}
			//$data = array_values($data);
			update_option( 'etivite_bp_restrictgroups', $data );
			$removed = true;
		}
		
	} elseif ( isset( $_POST['submit'] ) && check_admin_referer('etivite_bp_restrictgroups_admin_new') ) {
			
		$data = maybe_unserialize( get_option( 'etivite_bp_restrictgroups' ) );

		$newrule = Array();	
	
		//check for valid cap and update - if not keep old.
		if( isset($_POST['cap_low'] ) && !empty($_POST['cap_low']) ) {
			if ( etivite_bp_restrictgroups_admin_check_for_cap( $_POST['cap_low'] ) ) {
		
		
//redo,stack n loop it
		
				if( isset($_POST['rg_post_count'] ) && !empty($_POST['rg_post_count']) && (int)$_POST['rg_post_count'] == 1 ) {
					
					$p = (int)$_POST['rg_post_count_threshold'];
					
					$enabled = true;
					if ( !$p || $p < 1 ) {
						$p = 0;
						$enabled = false;
					}
					
					$newrule['bp_restrictgroups_post_count'] = array( 'enabled' => $enabled, 'count' => $p);
				} else {
					$newrule['bp_restrictgroups_post_count'] = array( 'enabled' => false, 'count' => 0 );
				}
				
				if( isset($_POST['rg_status_count'] ) && !empty($_POST['rg_status_count']) && (int)$_POST['rg_status_count'] == 1 ) {
		
					$s = (int)$_POST['rg_status_count_threshold'];
					
					$enabled = true;
					if ( !$s || $s < 1 ) {
						$s = 0;
						$enabled = false;
					}
		
					$newrule['bp_restrictgroups_status_count'] = array( 'enabled' => $enabled, 'count' => $s);
				} else {
					$newrule['bp_restrictgroups_status_count'] = array( 'enabled' => false, 'count' => 0 );
				}
				
				if( isset($_POST['rg_friends_count'] ) && !empty($_POST['rg_friends_count']) && (int)$_POST['rg_friends_count'] == 1 ) {
		
					$f = (int)$_POST['rg_friends_count_threshold'];
					
					$enabled = true;
					if ( !$f || $f < 1 ) {
						$f = 0;
						$enabled = false;
					}
		
					$newrule['bp_restrictgroups_friends_count'] = array( 'enabled' => $enabled, 'count' => $f);
				} else {
					$newrule['bp_restrictgroups_friends_count'] = array( 'enabled' => false, 'count' => 0 );
				}
				
				if( isset($_POST['rg_days_count'] ) && !empty($_POST['rg_days_count']) && (int)$_POST['rg_days_count'] == 1 ) {
		
					$d = (int)$_POST['rg_days_count_threshold'];
					
					$enabled = true;
					if ( !$d || $d < 1 ) {
						$d = 0;
						$enabled = false;
					}
		
					$newrule['bp_restrictgroups_days_count'] = array( 'enabled' => $enabled, 'count' => $d);
				} else {
					$newrule['bp_restrictgroups_days_count'] = array( 'enabled' => false, 'count' => 0 );
				}



				if( isset($_POST['rg_admin_count'] ) && !empty($_POST['rg_admin_count']) && (int)$_POST['rg_admin_count'] == 1 ) {
		
					$d = (int)$_POST['rg_admin_count_threshold'];
					
					$enabled = true;
					if ( !$d || $d < 1 ) {
						$d = 0;
						$enabled = false;
					}
		
					$newrule['bp_restrictgroups_admin_count'] = array( 'enabled' => $enabled, 'count' => $d);
				} else {
					$newrule['bp_restrictgroups_admin_count'] = array( 'enabled' => false, 'count' => 0 );
				}


				if( isset($_POST['rg_mod_count'] ) && !empty($_POST['rg_mod_count']) && (int)$_POST['rg_mod_count'] == 1 ) {
		
					$d = (int)$_POST['rg_mod_count_threshold'];
					
					$enabled = true;
					if ( !$d || $d < 1 ) {
						$d = 0;
						$enabled = false;
					}
		
					$newrule['bp_restrictgroups_mod_count'] = array( 'enabled' => $enabled, 'count' => $d);
				} else {
					$newrule['bp_restrictgroups_mod_count'] = array( 'enabled' => false, 'count' => 0 );
				}

				if( isset($_POST['rg_created_count'] ) && !empty($_POST['rg_created_count']) && (int)$_POST['rg_created_count'] == 1 ) {
		
					$d = (int)$_POST['rg_created_count_threshold'];
					
					$enabled = true;
					if ( !$d || $d < 1 ) {
						$d = 0;
						$enabled = false;
					}
		
					$newrule['bp_restrictgroups_created_count'] = array( 'enabled' => $enabled, 'count' => $d);
				} else {
					$newrule['bp_restrictgroups_created_count'] = array( 'enabled' => false, 'count' => 0 );
				}

				//if achievements is installed
				//if ( ACHIEVEMENTS_IS_INSTALLED ) {
					if( isset($_POST['rg_dpa_count'] ) && !empty($_POST['rg_dpa_count']) && (int)$_POST['rg_dpa_count'] == 1 ) {
		
						$d = (int)$_POST['rg_dpa_count_threshold'];
						
					$enabled = true;
					if ( !$d || $d < 1 ) {
						$d = 0;
						$enabled = false;
					}
		
						$newrule['bp_restrictgroups_dpa_count'] = array( 'enabled' => $enabled, 'count' => $d);
					} else {
						$newrule['bp_restrictgroups_dpa_count'] = array( 'enabled' => false, 'count' => 0 );
					}
					
					if( isset($_POST['rg_dpa_score'] ) && !empty($_POST['rg_dpa_score']) && (int)$_POST['rg_dpa_score'] == 1 ) {
		
						$d = (int)$_POST['rg_dpa_score_threshold'];
						
					$enabled = true;
					if ( !$d || $d < 1 ) {
						$d = 0;
						$enabled = false;
					}
		
						$newrule['bp_restrictgroups_dpa_score'] = array( 'enabled' => $enabled, 'count' => $d);
					} else {
						$newrule['bp_restrictgroups_dpa_score'] = array( 'enabled' => false, 'count' => 0 );
					}
				//}
				
				if( isset($_POST['rg_display_error'] ) && !empty($_POST['rg_display_error']) && (int)$_POST['rg_display_error'] == 1 ) {
					$newrule['display_error'] = true;
				} else {
					$newrule['display_error'] = false;
				}
				
				$newrule['date_created'] = bp_core_current_time();
				
				unset( $data[ $_POST['cap_low'] ] );
				$data[ $_POST['cap_low'] ] = $newrule;
				
				update_option( 'etivite_bp_restrictgroups', $data );
		
				$updated = true;
				
			} else {
				$error[] = '<div id="message" class="error"><p>Invalid user wp capability - please see <a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table">WP Roles and Capabilities</a>.</p></div>';
			}
		} else {
			$error[] = '<div id="message" class="updated fade"><p>User capability was left blank - this is required.</p></div>';
		}
	}
	
	// Get the proper URL for submitting the settings form. (Settings API workaround) - boone
	$url_base = function_exists( 'is_network_admin' ) && is_network_admin() ? network_admin_url( 'admin.php?page=bp-restrictgroups-settings' ) : admin_url( 'admin.php?page=bp-restrictgroups-settings' );
	
?>	
	<div class="wrap">
		<h2><?php _e( 'Restrict Group Creation', 'bp-restrictgroups' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Rule Added.', 'bp-restrictgroups' ) . "</p></div>"; endif;
		if ( isset($removed) && !isset($error) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Removed rule.', 'bp-restrictgroups' ) . "</p></div>"; endif;
		if ( isset($error) ) { 
			foreach( $error as $err) { 
				echo $err;
			} 
		}
		if ( bp_get_option( 'bp_restrict_group_creation', '0' ) == 0 ) : echo "<div id='error' class='error fade'><p>Warning: Please enable the \"Restrict group creation to Site Admins?\" <a href=\"". network_admin_url('/admin.php?page=bp-settings') ."\">setting</a>; otherwise the options below will be ignored.</p></div>"; endif; ?>

		<form action="<?php echo $url_base ?>" name="restrictgroups-rules-form" id="restrictgroups-rules-form" method="post">

			<div class="tablenav">
				<div class="alignleft actions">
					<input type="submit" class="button-secondary action" id="remove" name="remove" value="Remove Selected">
				</div>
				<br class="clear">
			</div>

			<table cellspacing="0" class="widefat fixed">			
				<thead>
				<tr class="thead">
					<th class="manage-column column-cb check-column" id="cb" scope="col"></th>
					<th class="manage-column column-wpcap" id="wpcap" scope="col" style="width:10%">Capabilities</th>
					<th class="manage-column column-restrictions" id="restrictions" scope="col">Restrictions</th>
					<th class="manage-column column-date" id="date" scope="col" style="width:10%">Display Error</th>
					<th class="manage-column column-date" id="date" scope="col" style="width:15%">Date Created</th>
				</tr>
				</thead>
	
				<tbody class="list:user user-list" id="users">
				<?php
				$rules = maybe_unserialize( get_option( 'etivite_bp_restrictgroups' ) );
				if ($rules) {
					foreach ($rules as $key => $value) { ?>
						<tr class="alternate">
						 <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $key; ?>"  name="cap_remove[]"></th>
						 <td class="username column-wpcap"><?php echo $key; ?></td>
						 <td class="username column-restrictions">
							<?php
							echo "<p>Days member registered: ", ($value['bp_restrictgroups_days_count'][enabled] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," Days: ". $value['bp_restrictgroups_days_count'][count] ."</p>";
							
							echo "<p>Min Number of friends: ", ($value['bp_restrictgroups_friends_count'][enabled]? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". $value['bp_restrictgroups_friends_count'][count] ."</p>";
							
							echo "<p>Min Number of activity updates: ", ($value['bp_restrictgroups_status_count'][enabled] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". $value['bp_restrictgroups_status_count'][count] ."</p>";
							
							echo "<p>Min Number of group forum posts: ", ($value['bp_restrictgroups_post_count'][enabled] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". $value['bp_restrictgroups_post_count'][count] ."</p>";

							echo "<p>Max Groups Admin: ", ($value['bp_restrictgroups_admin_count'][enabled] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". $value['bp_restrictgroups_admin_count'][count] ."</p>";
							
							echo "<p>Max Group Moderator: ", ($value['bp_restrictgroups_mod_count'][enabled] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". $value['bp_restrictgroups_mod_count'][count] ."</p>";
							
							echo "<p>Max Groups Created: ", ($value['bp_restrictgroups_created_count'][enabled] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". $value['bp_restrictgroups_created_count'][count] ."</p>";

							//if achievements is installed
							if ( defined('ACHIEVEMENTS_IS_INSTALLED') && ACHIEVEMENTS_IS_INSTALLED == 1 ) {
								echo "<p>Achievement Count: ", ($value['bp_restrictgroups_dpa_count'][enabled] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". $value['bp_restrictgroups_dpa_count'][count] ."</p>";
								
								echo "<p>Achievement Score: ", ($value['bp_restrictgroups_dpa_score'][enabled] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". $value['bp_restrictgroups_dpa_score'][count] ."</p>";
							} ?>
						 </td>
						 <?php echo "<td class=\"date column-date\">", ($value['display_error'] ? "<span style=\"color:green\">enabled</span>" : "<span style=\"color:red\">disabled</span>") ,"</td>"; ?>
						 <td class="date column-date"><?php echo $value['date_created']; ?></td>
						</tr>
					<?php } 
				} else {?>
					<tr>
						<th></th>
						<td colspan="3">no rules found</td>
					</tr>
				<?php } ?>
				</tbody>

			</table>
		
		<?php wp_nonce_field( 'etivite_bp_restrictgroups_admin_remove' ); ?>

		</form>

		<h3>Add New</h3>

		<form action="<?php echo network_admin_url('/admin.php?page=bp-restrictgroups-settings') ?>" name="restrictgroups-settings-form" id="restrictgroups-settings-form" method="post">

		<h4><?php _e( 'WP User Capabilities', 'bp-restrictgroups' ); ?></h4>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="cap_low"><?php _e( 'User capability level', 'bp-restrictgroups' ) ?></label></th>
					<td><input type="text" name="cap_low" id="cap_low" value="" /></td>
				</tr>
			</table>
			
			<div class="description">
				<p>*User capability is required (ie: edit_posts).</p>
				<p>Please refer to the <a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table">Codex for WP Caps</a></p>
			</div>
			
			
		<h4><?php _e( 'Member Registered Since (Days)', 'bp-restrictgroups' ); ?></h4>	
	
			<table class="form-table">
				<tr>
					<th><label for="rg_days_count"><?php _e('Enable Days Since Threshold','bp-restrictgroups') ?></label></th>
					<td><input type="checkbox" name="rg_days_count" id="rg_days_count" value="1" /> Days: <input type="text" name="rg_days_count_threshold" id="rg_days_count_threshold" /> </td>
				</tr>			
			</table>
			
			<div class="description">
				<p>Number of <strong>days</strong> registered.</p>
			</div>
			
		<h4><?php _e( 'Count Threshold Settings', 'bp-restrictgroups' ); ?></h4>

			<table class="form-table">

				<?php if ( bp_is_active( 'friends' ) ) { ?>
				<tr>
					<th><label for="rg_friends_count"><?php _e('Enable Min Friends Count','bp-restrictgroups') ?></label></th>
					<td><input type="checkbox" name="rg_friends_count" id="rg_friends_count" value="1" /> Count: <input type="text" name="rg_friends_count_threshold" id="rg_friends_count_threshold" value="" /> </td>
				</tr>
				<?php } ?>

				<?php if ( bp_is_active( 'activity' ) ) { ?>
				<tr>
					<th><label for="rg_status_count"><?php _e('Enable Min Status Count','bp-restrictgroups') ?></label></th>
					<td><input type="checkbox" name="rg_status_count" id="rg_status_count" value="1" /> Count: <input type="text" name="rg_status_count_threshold" id="rg_status_count_threshold" value="" /> </td>
				</tr>
				<?php } ?>

				<?php if ( function_exists('bp_forums_is_installed_correctly') && bp_forums_is_installed_correctly() ) { ?>
				<tr>
					<th><label for="rg_post_count"><?php _e('Enable Min Forum Post Count','bp-restrictgroups') ?></label></th>
					<td><input type="checkbox" name="rg_post_count" id="rg_post_count" value="1" /> Count: <input type="text" name="rg_post_count_threshold" id="rg_post_count_threshold" value="" /> </td>
				</tr>
				<?php } ?>
				
				
				<tr>
					<th><label for="rg_admin_count"><?php _e('Enable Max Groups Admin','bp-restrictgroups') ?></label></th>
					<td><input type="checkbox" name="rg_admin_count" id="rg_admin_count" value="1" /> Count: <input type="text" name="rg_admin_count_threshold" id="rg_admin_count_threshold" value="" /> </td>
				</tr>

				<tr>
					<th><label for="rg_mod_count"><?php _e('Enable Max Groups Mod','bp-restrictgroups') ?></label></th>
					<td><input type="checkbox" name="rg_mod_count" id="rg_mod_count" value="1" /> Count: <input type="text" name="rg_mod_count_threshold" id="rg_mod_count_threshold" value="" /> </td>
				</tr>

				<tr>
					<th><label for="rg_created_count"><?php _e('Enable Max Groups Created','bp-restrictgroups') ?></label></th>
					<td><input type="checkbox" name="rg_created_count" id="rg_created_count" value="1" /> Count: <input type="text" name="rg_created_count_threshold" id="rg_created_count_threshold" value="" /> </td>
				</tr>				
				
				<?php		
				//if achievements is installed
				if ( ACHIEVEMENTS_IS_INSTALLED == 1 ) { ?>
					<tr>
						<th><label for="rg_dpa_count"><?php _e('Enable Achievement Member Count Threshold','bp-restrictgroups') ?></label></th>
						<td><input type="checkbox" name="rg_dpa_count" id="rg_dpa_count" value="1" /> Count: <input type="text" name="rg_dpa_count_threshold" id="rg_dpa_count_threshold" value="" /> </td>
					</tr>
					
					<tr>
						<th><label for="rg_dpa_score"><?php _e('Enable Achievement Member Score Threshold','bp-restrictgroups') ?></label></th>
						<td><input type="checkbox" name="rg_dpa_score" id="rg_dpa_score" value="1" /> Score: <input type="text" name="rg_dpa_score_threshold" id="rg_dpa_score_threshold" value="" /> </td>
					</tr>
				<?php } ?>
				
			</table>
			
			<div class="description">
				<p>*<strong>Thresholds</strong>: applicable to members who meet the user capability level.</p>
			</div>
			
			<tr>
				<th><label for="rg_display_error"><?php _e('Display error message/reason on group directory page','bp-restrictgroups') ?></label></th>
				<td><input type="checkbox" name="rg_display_error" id="rg_display_error" value="1" /></td>
			</tr>
			
			<?php wp_nonce_field( 'etivite_bp_restrictgroups_admin_new' ); ?>
			
			<p class="submit"><input type="submit" name="submit" value="Add New Rule"/></p>
			
		</form>
		
		<h3>About:</h3>
		<div id="plugin-about" style="margin-left:15px;">
			
			<p>
			<a href="http://etivite.com/wordpress-plugins/buddypress-restrict-group-creation/">Restrict Group Creation- About Page</a><br/> 
			</p>
		
			<div class="plugin-author">
				<strong>Author:</strong> <a href="http://profiles.wordpress.org/users/etivite/"><img style="height: 24px; width: 24px;" class="photo avatar avatar-24" src="http://www.gravatar.com/avatar/9411db5fee0d772ddb8c5d16a92e44e0?s=24&amp;d=monsterid&amp;r=g" alt=""> rich @etivite</a><br/>
				<a href="http://twitter.com/etivite">@etivite</a>
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
