<?php if ( !is_active_sidebar('sidebar-bottom') ) : ?>
<aside id="content-sub-bottom" class="sub sidebar widgets" role="complementary">

  <?php if ( is_single() || is_author() ) : ?>
  <section class="widget vcard author-bio">
    <h3 class="widget-title">
    <?php if (get_the_author_meta('description')) : ?><?php _e('About','qmo'); ?><?php endif; ?>
    <?php if (get_the_author_meta('user_url')) : ?>
      <a class="url fn author" rel="external me" href="<?php the_author_meta('user_url'); ?>"><?php esc_html(the_author()); ?>
      <?php if (function_exists('get_avatar')) : echo ('<span class="photo">'.get_avatar( get_the_author_meta('user_email'), 68 ).'</span>'); endif; ?>
      </a>
    <?php else : ?>
      <a class="url fn author" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php esc_html(the_author()); ?>
      <?php if (function_exists('get_avatar')) : echo ('<span class="photo">'.get_avatar( get_the_author_meta('user_email'), 68 ).'</span>'); endif; ?>
      </a>
    <?php endif; ?>
    <?php if (get_the_author_meta('twitter_username')) : ?>
      <?php echo '<span><a href="http://twitter.com/'.get_the_author_meta('twitter_username').'" class="url" rel="external me">@'.get_the_author_meta('twitter_username').'</a></span>'; ?>
    <?php endif; ?>
    </h3>

    <?php if (get_the_author_meta('description', $author)) : ?>
    <p><?php esc_html(the_author_meta('description', $author)); ?></p>
    <?php endif; ?>

    <?php if (!is_author()) :
      if (get_the_author_meta('first_name')) :
        $name = esc_html(get_the_author_meta('first_name')); // Use the first name if there is one
      else :
        $name = esc_html(the_author()); // Fall back to the display name
      endif;
    ?>
    <p><a class="url go" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php printf(__('More from %s', 'qmo'), $name); ?></a></p>
    <?php endif; ?>
  </section>
  <?php endif; ?>

  <section class="widget">
  <?php include (TEMPLATEPATH . '/searchform.php'); ?>
  </section>
</aside>

<?php else : ?>

<aside id="content-sub-bottom" class="sub sidebar widgets" role="complementary">

  <?php if ( is_single() || is_author() ) : ?>
  <section class="widget vcard author-bio">
    <h3 class="widget-title">
    <?php if (get_the_author_meta('user_url')) : ?>
      <a class="url fn author" rel="external me" href="<?php the_author_meta('user_url'); ?>">
        <?php if (function_exists('get_avatar')) : echo ('<span class="photo">'.get_avatar( get_the_author_meta('user_email'), 68 ).'</span>'); endif; ?>
        <?php esc_html(the_author()); ?>
      </a>
    <?php else : ?>
      <a class="url fn author" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
        <?php if (function_exists('get_avatar')) : echo ('<span class="photo">'.get_avatar( get_the_author_meta('user_email'), 68 ).'</span>'); endif; ?>
        <?php esc_html(the_author()); ?>
      </a>
    <?php endif; ?>

    <?php if (get_the_author_meta('twitter_username')) : ?>
      <?php echo '<span><a href="http://twitter.com/'.get_the_author_meta('twitter_username').'" class="url" rel="external me">@'.get_the_author_meta('twitter_username').'</a></span>'; ?>
    <?php endif; ?>
    </h3>

    <?php if (get_the_author_meta('description')) : ?>
    <p><?php esc_html(the_author_meta('description')); ?></p>
    <?php endif; ?>

    <?php if (!is_author()) :
      if (get_the_author_meta('first_name')) :
        $name = esc_html(get_the_author_meta('first_name')); // Use the first name if there is one
      else :
        $name = esc_html(the_author()); // Fall back to the display name
      endif;
    ?>
    <p><a class="url go" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php printf(__('More from %s', 'qmo'), $name); ?></a></p>
    <?php endif; ?>
  </section>
  <?php endif; ?>

  <?php dynamic_sidebar('sidebar-bottom'); ?>

</aside><!-- #content-sub-bottom -->
<?php endif; ?>
