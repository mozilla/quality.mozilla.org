<?php 
$events_cat = get_category_by_slug('events')->cat_ID;
$news_cat = get_category_by_slug('qmo-news')->cat_ID;
?>

<?php if ( is_user_logged_in() ) : 
global $current_user; 
get_currentuserinfo(); ?>
<div id="user-state">
  <ul class="howdy">
    <li class="user-greet"><a href="<?php echo admin_url('profile.php'); ?>" title="Your profile"><?php echo get_avatar( $current_user->ID, 50 ); ?><?php echo $current_user->display_name; ?></a></li>
  <?php if( current_user_can('publish_posts') ) : ?>
    <li class="user-admin"><a href="<?php echo get_admin_url(); ?>">Site Admin</a></li>
  <?php endif; ?>
    <li class="user-logout"><a href="<?php echo wp_logout_url($_SERVER['REQUEST_URI']); ?>">Log Out</a></li>
  </ul>
</div>
<?php endif; ?>

<div id="site-follow">
  <ul>
    <li><a href="//www.facebook.com/pages/Mozilla-QA/122167964300" class="flw-facebook" rel="external" title="Follow us on Facebook">Facebook</a></li>
    <li><a href="//twitter.com/mozillaqa" class="flw-twitter" rel="external" title="Follow @mozillaqa on Twitter">Twitter</a></li>
    <li><a href="<?php bloginfo('rss2_url'); echo '?cat='.$news_cat.','.$events_cat; ?>" class="flw-feed" rel="alternate" title="Subscribe to our feed (News and Events)">RSS</a></li>
  </ul>
</div>
