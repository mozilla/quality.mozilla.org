<?php 
$events_cat = get_category_by_slug('events')->cat_ID;
$news_cat = get_category_by_slug('qmo-news')->cat_ID;
?>
<div id="user-state">
<?php if ( is_user_logged_in() ) : 
global $current_user; 
get_currentuserinfo(); ?>
  <ul class="howdy">
  <?php if ( function_exists('bp_is_active') ) : ?>
    <li class="user-greet"><a href="<?php echo bp_loggedin_user_domain(); ?>" title="Your profile"><?php echo bp_core_fetch_avatar( 'item_id='.$current_user->ID ); ?><?php echo $current_user->display_name; ?></a></li>
  <?php else : ?>
    <li class="user-greet"><a href="<?php echo admin_url('profile.php'); ?>" title="Your profile"><?php echo get_avatar( $current_user->ID, 50 ); ?><?php echo $current_user->display_name; ?></a></li>
  <?php endif; ?>
  <?php if( current_user_can('publish_posts') ) : ?>
    <li class="user-admin"><a href="<?php echo get_admin_url(); ?>">Site Admin</a></li>
  <?php endif; ?>
    <li class="user-logout"><a href="<?php echo wp_logout_url($_SERVER['REQUEST_URI']); ?>">Log Out</a></li>
  </ul>
<?php else : ?>
  <form action="<?php bloginfo('url') ?>/wp-login.php" method="post">
    <ul class="login">
      <li><label for="log">Username</label> <input type="text" name="log" id="log" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>"></li>
      <li><label for="pwd">Password</label> <input type="password" name="pwd" id="pwd"></li>
      <li class="check"><label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" value="forever"> Remember me</label></li>
      <li class="submit"><button type="submit" name="submit" class="button">Log in</button></li>
    </ul>
    <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
  </form>
<?php endif; ?>
</div>

<div id="site-follow">
  <ul>
    <li><a href="http://www.facebook.com/pages/Mozilla-QA/122167964300" class="flw-facebook" rel="external" title="Follow us on Facebook">Facebook</a></li>
    <li><a href="http://twitter.com/mozillaqa" class="flw-twitter" rel="external" title="Follow @mozillaqa on Twitter">Twitter</a></li>
    <li><a href="<?php bloginfo('rss2_url'); echo '?cat='.$news_cat.','.$events_cat; ?>" class="flw-feed" rel="alternate" title="Subscribe to our feed (News and Events)">RSS</a></li>
  </ul>
</div>
