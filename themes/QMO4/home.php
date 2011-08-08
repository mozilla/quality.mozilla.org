<?php
$events_cat = get_category_by_slug('events')->cat_ID;
$news_cat = get_category_by_slug('qmo-news')->cat_ID;
$home_intro = get_page_by_path('home-intro')->ID;

get_header(); ?>
<section id="content-main" class="hfeed vcalendar" role="main">
<?php if ( is_front_page() && ($paged < 1) && $home_intro ) :
  fc_get_post($home_intro); ?>
  <div id="home-head">
    <h2 id="tagline"><?php _e('The home of <strong>Mozilla QA</strong>', 'qmo'); ?></h2>
    <h3 class="section-title"><?php the_title(); ?></h3>

  <?php if ( function_exists('bp_is_active') && bp_is_active( 'groups' ) && bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>
    <ul class="teams-list">
    <?php while ( bp_groups() ) : bp_the_group(); ?>
      <li>
        <a href="<?php bp_group_permalink(); ?>">
          <?php bp_group_avatar( 'width=100&height=100' ); ?>
          <?php bp_group_name(); ?>
        </a>
      </li>
    <?php endwhile; ?>
    </ul>
  <?php endif; ?>

    <?php the_content(); ?>
  </div>

  <h2 class="section-title">Latest News</h2>
<?php endif; ?>

<?php $wp_query->query('cat='.$news_cat.','.$events_cat.'&posts_per_page=4&paged='.$paged); // Only show news and events on the home page
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
  <?php elseif ( fc_is_child('teams') ) : ?>
    <p class="team-flag">Team</p>
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
      include (TEMPLATEPATH . '/event-card.php');
    endif; ?>

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

  <h1 class="section-title"><?php _e('Sorry, there&#8217;s nothing to see here.','qmo'); ?></h1>

<?php endif; ?>

</section><?php /* end #content-main */ ?>

<section id="content-sub" class="vcalendar" role="complementary">
<?php include (TEMPLATEPATH . '/user-state.php'); ?>
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-home') ) : else : endif; ?>
</section>
<?php get_footer(); ?>
