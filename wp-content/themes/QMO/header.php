<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta http-equiv="imagetoolbar" content="no">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="Rating" content="General">
  <meta name="MSSmartTagsPreventParsing" content="true">
  <link rel="profile" href="http://gmpg.org/xfn/11">
  <link rel="shortcut icon" type="image/ico" href="<?php bloginfo('stylesheet_directory'); ?>/favicon.ico">
  <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> News and Events Feed" href="<?php bloginfo('rss2_url'); echo '?cat='.$news_cat.','.$events_cat; ?>">
  <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> Community Feed" href="<?php bloginfo('rss2_url'); ?>">
  <link rel="home" href="<?php echo bloginfo('url'); ?>">
  <link rel="copyright" href="#copyright">

  <link rel="stylesheet" type="text/css" media="screen,projection" href="<?php bloginfo('stylesheet_url'); ?>">
  <!--[if lte IE 7]><link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_directory'); ?>/css/ie7.css"><![endif]-->
  <!--[if lte IE 6]><link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_directory'); ?>/css/ie6.css"><![endif]-->
  <link rel="stylesheet" type="text/css" media="print,handheld" href="<?php bloginfo('stylesheet_directory'); ?>/css/print.css">

  <?php if (is_singular()) wp_enqueue_script( 'comment-reply' ); ?>
  <?php if (is_singular()) : ?><link rel="canonical" href="<?php echo the_permalink(); ?>"><?php endif; ?>
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
  <?php if (class_exists('The_Events_Calendar')) : ?><link rel="profile" href="http://microformats.org/profile/hcalendar"><?php endif; ?>

  <title><?php
    if ( is_single() ) { single_post_title(); echo ' | '; bloginfo('name'); } 
    elseif ( is_home() || is_front_page() ) { bloginfo('name'); echo ' | '; bloginfo('description'); qmo_page_number(); } 
    elseif ( is_page() ) { single_post_title(''); echo ' | '; bloginfo('name'); } 
    elseif ( is_search() ) { printf( __('Search results for "%s"', 'qmo'), esc_html( $s ) ); qmo_page_number(); echo ' | '; bloginfo('name'); }
    elseif ( is_day() ) { $post = $posts[0]; _e('Posts for ', 'qmo'); echo the_time('F jS, Y'); echo ' | '; bloginfo('name'); qmo_page_number(); }
    elseif ( is_month() ) { $post = $posts[0]; _e('Posts for ', 'qmo'); echo the_time('F, Y'); echo ' | '; bloginfo('name'); qmo_page_number(); }
    elseif ( is_year() ) { $post = $posts[0]; _e('Posts for ', 'qmo'); echo the_time('Y'); echo ' | '; bloginfo('name'); qmo_page_number(); }
    elseif ( is_404() ) { _e('Not Found', 'qmo'); echo ' | '; bloginfo('name'); } 
    else { wp_title(''); echo ' | '; bloginfo('name'); qmo_page_number(); } 
  ?></title>

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <div id="masthead" class="section">
    <div id="branding" role="banner">
    <?php if ( (is_front_page()) && ($paged < 1) ) : ?>
      <h1 id="logo"><img src="<?php bloginfo('stylesheet_directory'); ?>/img/Q.png" alt="" width="102" height="107"> <?php bloginfo('name'); ?></h1>
    <?php else : ?>
      <h4 id="logo"><a href="<?php echo bloginfo('url'); ?>" rel="home"><img src="<?php bloginfo('stylesheet_directory'); ?>/img/Q.png" alt="" width="102" height="107"> <?php bloginfo('name'); ?></a></h4>
    <?php endif; ?>
      <p id="tagline"><?php _e('The home of <strong>Mozilla QA</strong>', 'qmo'); ?></p>
    </div><!-- /#branding -->

    <div id="site-nav">
      <div class="section">
        <?php include (TEMPLATEPATH . '/main-nav.php'); 
          $events_cat = get_category_by_slug('events')->cat_ID;
          $news_cat = get_category_by_slug('qmo-news')->cat_ID;
        ?>
        <ul id="nav-extra">
          <li><a href="http://www.facebook.com/pages/Mozilla-QA/122167964300" class="navex-facebook" rel="external" title="Follow us on Facebook">Facebook</a></li>
          <li><a href="http://twitter.com/mozillaqa" class="navex-twitter" rel="external" title="Follow @mozillaqa on Twitter">Twitter</a></li>
          <li><a href="<?php bloginfo('rss2_url'); echo '?cat='.$news_cat.','.$events_cat; ?>" class="navex-feed" rel="alternate" title="Subscribe to our feed (News and Events)">RSS</a></li>
        </ul>
      </div>
    </div>

    <div id="site-utils">
      <?php include (TEMPLATEPATH . '/searchform.php'); ?>
      <?php 
        global $current_user;
        get_currentuserinfo();
        if (is_user_logged_in()) : ?>
      <ul id="nav-util">
        <li class="user-greet">Welcome, <a href="<?php echo admin_url('profile.php'); ?>" title="Edit your profile"><?php echo $current_user->display_name; ?></a></li>
        <li class="user-logout"><a href="<?php echo wp_logout_url(); ?>">Log Out</a></li>
        <?php wp_register(); ?>
      </ul>
      <?php endif; ?>
    </div>
  </div><!-- /#masthead -->

  <div id="content" class="section">
