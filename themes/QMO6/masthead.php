<header id="masthead" role="banner">
  <?php if ( (is_front_page()) && ($paged < 1) ) : ?>
    <img class="site-logo" src="<?php bloginfo('stylesheet_directory'); ?>/img/head-logo.png" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
  <?php else : ?>
    <a href="<?php echo bloginfo('url'); ?>" rel="home" title="<?php _e('Go to the front page', 'qmo'); ?>"><img class="site-logo" src="<?php bloginfo('stylesheet_directory'); ?>/img/head-logo.png" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"></a>
  <?php endif; ?>

  <?php if (is_front_page()) : ?>
    <h1 id="site-title"><?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?></h1>
    <?php if (get_bloginfo('description','display')) : ?>
    <h2 id="site-description"><?php echo esc_attr( get_bloginfo('description', 'display') ); ?></h2>
    <?php endif; ?>
  <?php endif; ?>

  <?php wp_nav_menu( array( 'theme_location' => 'main', 'container' => 'nav', 'container_id' => 'nav-main', 'fallback_cb' => 'false', 'items_wrap' => '<span class="toggle">Menu</span><ul id="nav-main-list">%3$s</ul>' ) ); ?>

  <a href="https://www.mozilla.org/" id="tabzilla">Mozilla</a>
</header><!-- #masthead -->
