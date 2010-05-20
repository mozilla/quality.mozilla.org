<?php
$events_cat = get_category_by_slug('events')->cat_ID;
$news_cat = get_category_by_slug('qmo-news')->cat_ID;
$home_intro = get_page_by_path('home-intro')->ID;

get_header(); ?>
<div id="content-main" class="hfeed vcalendar" role="main">
<?php if ( is_front_page() && ($paged < 1) && $home_intro ) :
  fc_get_post($home_intro); ?>
  <div id="home-head">
    <h2 class="section-title"><?php the_title(); ?></h2>

    <?php $groups_page = get_page_by_path('groups')->ID;
    $groups = new WP_Query(array('post_type' => 'page','post_status' => 'publish','post_parent' => $groups_page, 'order' => 'ASC', 'orderby' => 'menu_order'));
    
    if ( $groups->have_posts() ) : ?>
      <ul class="groups-list">
      <?php while ( $groups->have_posts() ) : $post = $groups->next_post(); ?>
        <li><a href="<?php the_permalink() ?>">
          <?php if (function_exists('the_post_thumbnail') && has_post_thumbnail($post->ID) ) : ?>
            <?php echo get_the_post_thumbnail( $post->ID, 'thumbnail', array('alt' => "", 'title' => "") ); ?>
          <?php endif; ?>
            <?php the_title(); ?>
        </a></li>
      <?php endwhile; ?>
      </ul>
    <?php endif; ?>

    <?php the_content(); ?>
  </div>

  <h2 class="section-title">Latest News</h2>
<?php endif; ?>

<?php $wp_query->query('cat='.$news_cat.','.$events_cat.'&paged='.$paged); // Only show news and events on the home page
if (have_posts()) : while (have_posts()) : the_post(); // The Loop ?>

  <div id="post-<?php the_ID(); ?>" <?php if ( function_exists('is_event') && is_event() ) : post_class('vevent'); else : post_class(); endif; ?> role="article">
    <h3 class="entry-title <?php if ( function_exists('is_event') && is_event() ) : echo 'summary'; endif; ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent link to &#8220;<?php the_title_attribute(); ?>&#8221;" <?php if ( function_exists('is_event') && is_event() ) : echo 'class="url"'; endif; ?>><?php the_title(); ?></a></h3>
  
  <?php if ( ( function_exists('is_event') && is_event() ) || in_category('events') ) : ?>
    <div class="entry-meta">
      <p class="event-flag">Event</p>
      <p class="vcard">Posted by <a class="fn url author" title="See all <?php the_author_posts() ?> posts by <?php the_author(); ?>" href="<?php echo get_author_posts_url($authordata->ID, $authordata->user_nicename); ?>"><?php the_author(); ?></a> 
      on <?php the_time(get_option('date_format')); ?> at <abbr class="updated" title="<?php the_time('Y-m-d\TH:i:sP'); ?>"><?php the_time(); ?></abbr>.
      <?php if ( current_user_can( 'edit_page', $post->ID ) ) : ?><span class="edit"><?php edit_post_link('Edit', '', ''); ?></span><?php endif; ?>
      </p>
    </div>
  <?php elseif ( fc_is_child('docs') ) : ?>
    <p class="doc-flag">Doc</p>
  <?php elseif ( fc_is_child('groups') ) : ?>
    <p class="group-flag">Group</p>
  <?php else : ?>
    <div class="entry-meta">
      <p class="entry-posted">
        <a class="posted-month" href="<?php echo get_month_link(get_the_time('Y'), get_the_time('m')); ?>" title="See all posts from <?php echo get_the_time('F, Y'); ?>"><?php the_time('M'); ?></a>
        <span class="posted-date"><?php the_time('j'); ?></span>
        <span class="posted-year"><?php the_time('Y'); ?></span>
        <abbr class="updated" title="<?php the_time('Y-m-d\TH:i:sP'); ?>"><?php the_time(); ?></abbr>
      </p>
      <p class="vcard"><?php _e('Posted by','qmo') ?> <a class="fn url author" title="See all <?php the_author_posts(); ?> posts by <?php the_author(); ?>" href="<?php echo get_author_posts_url($authordata->ID, $authordata->user_nicename); ?>"><?php the_author(); ?></a> 
      in <?php the_category(', ', ''); ?>.
      <?php if ( current_user_can( 'edit_page', $post->ID ) ) : ?><span class="edit"><?php edit_post_link(__('Edit','qmo'), '', ''); ?></span><?php endif; ?>
      </p>
    </div>
  <?php endif; ?>

    <div class="entry-content <?php if ( function_exists('is_event') && is_event() ) : echo 'description'; endif; ?>">
    <?php if ( function_exists('is_event') && is_event($post->ID) ) : 
      // Fetch the formats
      $date_format = get_option("date_format");
      $time_format = get_option("time_format"); ?>
      <div class="event-date">
        <h3>When</h3>
        <?php // All day, single day
          if (get_post_meta( $post->ID, '_EventAllDay' ) && (the_event_start_date($post->ID) == the_event_end_date($post->ID)) ) : ?>
            <p><abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr></p>
        <?php // All day, multiple days
          elseif (get_post_meta( $post->ID, '_EventAllDay' ) && (the_event_start_date($post->ID) != the_event_end_date($post->ID)) ) : ?>
            <p>
            <span class="start"><em>Start:</em> <abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr></span>
            <span class="end"><em>End:</em> <abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_end_date( $post->ID, false, $date_format ); ?></abbr></span>
            </p>
        <?php // Not all day, but the time spans more than one date (e.g., runs past midnight)
          elseif (!get_post_meta( $post->ID, '_EventAllDay' ) && (the_event_start_date($post->ID, false, $date_format) < the_event_end_date($post->ID, false, $date_format)) ) : ?>
            <p>
            <span class="start"><em>Start:</em> <abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format )."<br>".the_event_start_date( $post->ID, false, $time_format ); ?></abbr></span>
            <span class="end"><em>End:</em> <abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_end_date( $post->ID, false, $date_format )."<br>".the_event_end_date( $post->ID, false, $time_format ); ?></abbr></span>
            </p>
        <?php // Just a normal event.
          else : ?>
            <p><abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr>
            <span class="start"><em>Start:</em> <?php echo the_event_start_date( $post->ID, false, $time_format ); ?></span>
            <span class="end"><em>End:</em> <abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_end_date( $post->ID, false, $time_format ); ?></abbr></span>
            </p>
        <?php endif; ?>
        
        <?php // If there's more info to show, link to the event page
          if ( the_event_venue($post->ID) || the_event_cost($post->ID) || the_event_address($post->ID) || the_event_phone($post->ID) ) : ?>
          <p><a class="more-link" href="<?php echo get_permalink($post->ID) ?>">More info</a></p>
        <?php endif; ?>
        
        <?php // If the current datetime is greater than event datetime, the event is in the past
          if ( date('c') > the_event_end_date($post->ID, false, 'c') ) : ?>
          <p class="passed description"><strong>This event has passed.</strong></p>
        <?php endif; ?>
      </div>
    <?php endif; /* is_event */ ?>
    
      <?php the_content(__('Read more&hellip;', 'qmo')); ?>
      <?php wp_link_pages(array('before' => '<p class="pages"><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'next', 'link_before' => '<b>', 'link_after' => '</b>')); ?>
    </div>
    
    <?php if (get_the_tags()) : ?>
      <?php the_tags('<p class="entry-tags"><strong>'.__('Tags:','qmo').'</strong> ',', ',''); ?>
    <?php endif; ?>
  
  <?php $comment_count = get_comment_count($post->ID);
  if ( comments_open() || $comment_count['approved'] > 0 ) : ?>
    <ul class="discuss">
      <li class="comment-count"><a href="<?php comments_link() ?>"><?php comments_number(__('No comments yet', 'qmo'),__('1 comment', 'qmo'),__('% comments', 'qmo')); ?></a></li>
    <?php if ( comments_open() ) : ?>
      <li class="comment-post"><a href="<?php the_permalink() ?>#respond"><?php _e('Post a comment', 'qmo'); ?></a></li>
    <?php else : ?>
      <li class="comment-closed"><em><?php _e('Comments closed', 'qmo'); ?></em></li>
    <?php endif; ?>
    </ul>
  <?php endif; ?>
  </div><!-- /post -->

  <?php endwhile; ?>
  
    <?php if (fc_show_posts_nav()) : ?>
      <?php if (function_exists('fc_pagination') ) : fc_pagination(); else: ?>
        <ul class="nav-paging">
          <?php if ( $paged < $wp_query->max_num_pages ) : ?><li class="prev"><?php next_posts_link(__('Previous','qmo')); ?></li><?php endif; ?>
          <?php if ( $paged > 1 ) : ?><li class="next"><?php previous_posts_link(__('Next','qmo')); ?></li><?php endif; ?>
        </ul>
      <?php endif; ?>
    <?php endif; ?>
  
  <?php else : // if there are no posts ?>

  <h1 class="page-title"><?php _e('Sorry, nothing to display here.','qmo'); ?></h1>

<?php endif; ?>

</div><?php /* end #content-main */ ?>

<div id="content-sub" class="vcalendar" role="complementary">
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-home') ) : else : endif; ?>
</div>
<?php get_footer(); ?>