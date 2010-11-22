<?php global $current_user; get_currentuserinfo(); ?>
<div id="user-state">
  <?php if (is_user_logged_in()) : ?>
  <ul class="howdy">
    <li class="user-greet"><a href="<?php echo bp_loggedin_user_domain(); ?>" title="Your profile"><?php echo bp_core_fetch_avatar( 'item_id='.$current_user->ID ); ?><?php echo $current_user->display_name; ?></a></li>
    <?php if( current_user_can('level_5') ) : ?>
    <li class="user-admin"><a href="<?php echo get_admin_url(); ?>">Site Admin</a></li>
    <?php endif; ?>
    <li class="user-logout"><a href="<?php echo wp_logout_url($_SERVER['REQUEST_URI']); ?>">Log Out</a></li>
  </ul>
  <?php elseif ( get_option('users_can_register') ) : ?>
	<form action="<?php bloginfo('url') ?>/wp-login.php" method="post">
		<ul class="login">
		  <li><label for="log">Username</label> <input type="text" name="log" id="log" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>"></li>
		  <li><label for="pwd">Password</label> <input type="password" name="pwd" id="pwd"></li>
		  <li class="check"><label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" value="forever"> Remember me</label></li>
		  <li class="submit"><button type="submit" name="submit" class="button">Log in</button></li>
		  <li class="signup"><a href="<?php echo bp_get_signup_page(false); ?>"><?php _e('Sign up', 'qmo'); ?></a></li>
		</ul>
		<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
	</form>
  <?php endif; ?>
</div>
