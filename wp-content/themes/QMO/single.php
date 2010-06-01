<?php
// Fetch the formats
$date_format = get_option("date_format");
$time_format = get_option("time_format");

// Fetch some IDs
$events_cat = get_category_by_slug('events')->cat_ID;
$synd_cat = get_category_by_slug('syndicated')->cat_ID;
$twitter_cat = get_category_by_slug('twitter')->cat_ID;

get_header(); ?>
<div id="content-main" class="hfeed" role="main">
<?php if (have_posts()) : while (have_posts()) : the_post(); // The Loop ?>

  <div id="post-<?php the_ID(); ?>" 
    <?php if ( function_exists('is_event') && is_event() ) : post_class('vevent'); 
      elseif (in_category('twitter')) : post_class('tweet');
      elseif ( function_exists('is_syndicated') && is_syndicated() ) : post_class('syndicated');
      else : post_class(); endif; ?> role="article">
  <?php if (!in_category('twitter')) : ?>
    <h1 class="entry-title <?php if ( function_exists('is_event') && is_event() ) : echo 'summary'; endif; ?>"><?php the_title(); ?></a></h1>
  <?php endif; ?>
  <?php if ( ( function_exists('is_event') && is_event() ) || in_category($events_cat) ) : ?>
    <div class="entry-meta">
      <p class="event-flag">Event</p>
      <p class="vcard">Posted by <a class="fn url author" title="See all <?php the_author_posts() ?> posts by <?php the_author(); ?>" href="<?php echo get_author_posts_url($authordata->ID, $authordata->user_nicename); ?>"><?php the_author(); ?></a> 
      on <?php the_time($date_format); ?> at <abbr class="updated" title="<?php the_time('Y-m-d\TH:i:sP'); ?>"><?php the_time(); ?></abbr>.
      <?php if ( current_user_can( 'edit_page', $post->ID ) ) : ?><span class="edit"><?php edit_post_link('Edit', '', ''); ?></span><?php endif; ?>
      </p>
    </div>
  <?php elseif ( in_category('twitter') ) : ?>
    <p class="tweet-flag">Tweet</p>
  <?php elseif ( function_exists('is_syndicated') && is_syndicated() ) : ?>
    <div class="entry-meta">
      <p class="entry-posted">
        <a class="posted-month" href="<?php echo get_month_link(get_the_time('Y'), get_the_time('m')); ?>" title="See all posts from <?php echo get_the_time('F, Y'); ?>"><?php the_time('M'); ?></a>
        <span class="posted-date"><?php the_time('j'); ?></span>
        <span class="posted-year"><?php the_time('Y'); ?></span>
        <abbr class="updated" title="<?php the_time('Y-m-d\TH:i:sP'); ?>"><?php the_time(); ?></abbr>
      </p>
      <p>Syndicated from <a href="<?php the_syndication_source_link(); ?>" rel="nofollow external"><?php the_syndication_source(); ?></a></p>
    </div>
  <?php else : ?>
    <div class="entry-meta">
      <p class="entry-posted">
        <a class="posted-month" href="<?php echo get_month_link(get_the_time('Y'), get_the_time('m')); ?>" title="See all posts from <?php echo get_the_time('F, Y'); ?>"><?php the_time('M'); ?></a>
        <span class="posted-date"><?php the_time('j'); ?></span>
        <span class="posted-year"><?php the_time('Y'); ?></span>
        <abbr class="updated" title="<?php the_time('Y-m-d\TH:i:sP'); ?>"><?php the_time(); ?></abbr>
      </p>
      <p class="vcard"><?php _e('Posted by','qmo') ?> <a class="fn url author" title="See all <?php the_author_posts(); ?> posts by <?php the_author(); ?>" href="<?php echo get_author_posts_url($authordata->ID, $authordata->user_nicename); ?>"><?php the_author(); ?></a> 
      <?php if(!in_category('community')) : ?>in <?php the_category(', ', ''); ?><?php endif; ?>
      <?php if ( current_user_can( 'edit_page', $post->ID ) ) : ?><span class="edit"><?php edit_post_link(__('Edit','qmo'), '', ''); ?></span><?php endif; ?>
      </p>
    </div>
  <?php endif; ?>

    <div class="entry-content <?php if ( function_exists('is_event') && is_event() ) : echo 'description'; endif; ?>">
    <?php if ( function_exists('is_event') && is_event($post->ID) ) :
      include (TEMPLATEPATH . '/event-card.php');
    endif; ?>
    <?php if(in_category('twitter')) : ?>
      <?php the_content(); ?>
      <p class="tweet-meta">Posted on <?php the_time($date_format); ?> at <abbr class="updated" title="<?php the_time('Y-m-d\TH:i:sP'); ?>"><?php the_time(); ?></abbr>.</p>
    <?php else : ?>
      <?php if (has_post_thumbnail()) : the_post_thumbnail('thumbnail', array('alt' => "", 'title' => "")); endif; ?>
      <?php the_content(__('Read more&hellip;', 'qmo')); ?>
      <?php wp_link_pages(array('before' => '<p class="pages"><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'next', 'link_before' => '<b>', 'link_after' => '</b>')); ?>
    <?php endif; ?>
    </div>

    <?php if (get_the_tags()) : ?>
      <?php the_tags('<p class="entry-tags"><strong>'.__('Tags:','qmo').'</strong> ',', ',''); ?>
    <?php endif; ?>
  </div><!-- /post -->

  <?php endwhile; ?>

  <?php comments_template(); ?>

  <?php else : // if there are no posts ?>

  <h1 class="section-title"><?php _e('Sorry, there&#8217;s nothing to see here.','qmo'); ?></h1>

<?php endif; ?>

</div><?php /* end #content-main */ ?>

<div id="content-sub">
  <?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-home') ) : else : endif; ?>
</div>

<?php get_footer(); ?>
