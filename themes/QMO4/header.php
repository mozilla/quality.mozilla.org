<?php 
  // Fetch the category IDs
  $events_cat = get_category_by_slug('events')->cat_ID;
  $news_cat = get_category_by_slug('qmo-news')->cat_ID; 
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <!--[if IE]>
  <meta http-equiv="imagetoolbar" content="no">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <meta name="Rating" content="General">
  <meta name="MSSmartTagsPreventParsing" content="true">
  <link rel="profile" href="http://gmpg.org/xfn/11">
  <link rel="shortcut icon" type="image/ico" href="<?php bloginfo('stylesheet_directory'); ?>/favicon.ico">
  <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> News and Events Feed" href="<?php bloginfo('rss2_url'); echo '?cat='.$news_cat.','.$events_cat; ?>">
  <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> Community Feed" href="<?php bloginfo('rss2_url'); ?>">
  <link rel="home" href="<?php echo bloginfo('url'); ?>">
  <link rel="copyright" href="#copyright">  
  <?php if ( function_exists( 'bp_sitewide_activity_feed_link' ) ) : ?>
  	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php _e('Site Wide Activity RSS Feed', 'buddypress' ) ?>" href="<?php bp_sitewide_activity_feed_link() ?>" />
  <?php endif; ?>
  <?php if ( function_exists( 'bp_member_activity_feed_link' ) && bp_is_member() ) : ?>
  	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php bp_displayed_user_fullname() ?> | <?php _e( 'Activity RSS Feed', 'buddypress' ) ?>" href="<?php bp_member_activity_feed_link() ?>" />
  <?php endif; ?>
  <?php if ( function_exists( 'bp_group_activity_feed_link' ) && bp_is_group() ) : ?>
  	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php bp_current_group_name() ?> | <?php _e( 'Group Activity RSS Feed', 'buddypress' ) ?>" href="<?php bp_group_activity_feed_link() ?>" />
  <?php endif; ?>

  <link rel="stylesheet" type="text/css" media="screen,projection" href="<?php bloginfo('stylesheet_url'); ?>">
  <!--[if lte IE 7]><link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_directory'); ?>/css/ie7.css"><![endif]-->
  <!--[if lte IE 6]><link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_directory'); ?>/css/ie6.css"><![endif]-->
  <link rel="stylesheet" type="text/css" media="print,handheld" href="<?php bloginfo('stylesheet_directory'); ?>/css/print.css">

  <?php if (is_singular()) wp_enqueue_script( 'comment-reply' ); ?>
  <?php if (is_singular()) : ?><link rel="canonical" href="<?php echo the_permalink(); ?>"><?php endif; ?>
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
  <?php if (class_exists('The_Events_Calendar')) : ?><link rel="profile" href="http://microformats.org/profile/hcalendar"><?php endif; ?>

  <title><?php
    if ( is_single() ) { single_post_title(); echo ' &#124; '; bloginfo('name'); }
    elseif ( is_home() || is_front_page() ) { bloginfo('name'); echo ' &#124; '; bloginfo('description'); qmo_page_number(); }
    elseif ( is_day() ) { $post = $posts[0]; _e('Posts for ', 'qmo'); echo the_time('F jS, Y'); echo ' &#124; '; bloginfo('name'); qmo_page_number(); }
    elseif ( is_month() ) { $post = $posts[0]; _e('Posts for ', 'qmo'); echo the_time('F, Y'); echo ' &#124; '; bloginfo('name'); qmo_page_number(); }
    elseif ( is_year() ) { $post = $posts[0]; _e('Posts for ', 'qmo'); echo the_time('Y'); echo ' &#124; '; bloginfo('name'); qmo_page_number(); }
    else {
      if ( function_exists('bp_is_active') ) :
        qmo_page_title('&#124;',1,'right'); bloginfo('name'); qmo_page_number(); 
      else : 
        wp_title('&#124;',1,'right'); bloginfo('name'); qmo_page_number();
      endif;
      }
  ?></title>
  <?php if ( function_exists('bp_is_active') ) { do_action( 'bp_head' ); } ?>
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php do_action( 'bp_before_header' ); ?>
  <header id="masthead" class="section">
  
    <div id="header">
      <a href="http://www.mozilla.org/" class="mozilla" title="The Mozilla Foundation">visit <span>Mozilla</span></a>
    </div><!-- end #header -->

    <div id="branding" role="banner">
    <?php if ( (is_front_page()) && ($paged < 1) ) : ?>
      <h1 id="logo"><img src="<?php bloginfo('stylesheet_directory'); ?>/img/head-logo.png" alt="<?php bloginfo('name'); ?>"></h1>
    <?php else : ?>
      <h4 id="logo"><a href="<?php echo bloginfo('url'); ?>" rel="home"><img src="<?php bloginfo('stylesheet_directory'); ?>/img/head-logo.png" alt="<?php bloginfo('name'); ?>"></a></h4>
    <?php endif; ?>
    </div><!-- /#branding -->

    <nav id="site-nav">
      <ul id="nav-access" role="navigation">
        <li><a href="#content">Skip to main content</a></li>
        <li><a href="#search">Skip to search</a></li>
        <?php if (!is_user_logged_in()) : ?>
        <li><a href="<?php echo wp_login_url(); ?>">Log in</a></li>
        <?php endif; ?>
      </ul>
      
      <div class="section">
        <?php include (TEMPLATEPATH . '/main-nav.php'); ?>
      </div>
    </nav>
    <?php include (TEMPLATEPATH . '/searchform.php'); ?>
  </header><!-- /#masthead -->

  <section id="content" class="section">
