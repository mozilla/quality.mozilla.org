<?php 
// Fetch the formats
$date_format = get_option("date_format");
$time_format = get_option("time_format");

$search_count = 0;
$search = new WP_Query("s=$s & showposts=-1");
if($search->have_posts()) : while($search->have_posts()) : $search->the_post();
$search_count++;
endwhile; endif;

get_header(); ?>
<div id="content-main" class="hfeed vcalendar" role="main">
<?php if (have_posts()) : ?>

<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
  <h1 class="section-title">
  <?php if (is_category()) : ?>Posts in <?php single_cat_title(); ?>
  <?php elseif (is_tag()) : ?>Posts and pages tagged &#8220;<?php single_tag_title(); ?>&#8221;
  <?php elseif (is_day()) : ?>Posts for <?php the_time('F jS, Y'); ?>
  <?php elseif (is_month()) : ?>Posts for <?php the_time('F, Y'); ?>
  <?php elseif (is_year()) : ?>Posts for <?php the_time('Y'); ?>
  <?php elseif (is_author()) : ?>Posts and pages by <?php echo get_userdata(intval($author))->display_name; ?>
  <?php elseif (is_search()) : ?>We found <?php echo $wp_query->found_posts; ?> results for &#8220;<?php the_search_query(); ?>&#8221;
  <?php else : ?>Posts
  <?php endif; ?>
  </h1>

<?php while (have_posts()) : the_post(); ?>
  <div id="post-<?php the_ID(); ?>"
    <?php if ( function_exists('is_event') && is_event() ) : post_class('vevent');
      elseif (in_category('twitter')) : post_class('tweet');
      elseif ( function_exists('is_syndicated') && is_syndicated() ) : post_class('syndicated');
      else : post_class(); endif; ?> role="article">
  <?php if (!in_category('twitter')) : ?>
    <h3 class="entry-title <?php if ( function_exists('is_event') && is_event() ) : echo 'summary'; endif; ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent link to &#8220;<?php the_title_attribute(); ?>&#8221;" <?php if ( function_exists('is_event') && is_event() ) : echo 'class="url"'; endif; ?>><?php the_title(); ?></a></h3>
  <?php endif; ?>
  <?php if ( ( function_exists('is_event') && is_event() ) || in_category('events') ) : ?>
    <div class="entry-meta">
      <p class="event-flag">Event</p>
      <p class="vcard">Posted by <a class="fn url author" title="See all <?php the_author_posts() ?> posts by <?php the_author(); ?>" href="<?php echo get_author_posts_url($authordata->ID, $authordata->user_nicename); ?>"><?php the_author(); ?></a> 
      on <?php the_time($date_format); ?> at <abbr class="updated" title="<?php the_time('Y-m-d\TH:i:sP'); ?>"><?php the_time(); ?></abbr>.
      <?php if ( current_user_can( 'edit_page', $post->ID ) ) : ?><span class="edit"><?php edit_post_link('Edit', '', ''); ?></span><?php endif; ?>
      </p>
    </div>
  <?php elseif ( fc_is_child('docs') ) : ?>
    <p class="doc-flag">Doc</p>
  <?php elseif ( fc_is_child('groups') ) : ?>
    <p class="group-flag">Group</p>
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

    <div class="entry-summary <?php if ( function_exists('is_event') && is_event() ) : echo 'description'; endif; ?>">
    <?php if ( function_exists('is_event') && is_event() ) :
      include (TEMPLATEPATH . '/event-card-compact.php');
    endif; ?>
    
    <?php if(in_category('twitter')) : ?>
      <?php the_content(); ?>
      <p class="tweet-meta">Posted on <?php the_time($date_format); ?> at <abbr class="updated" title="<?php the_time('Y-m-d\TH:i:sP'); ?>"><?php the_time(); ?></abbr>.</p>
    <?php else : ?>
      <?php if (has_post_thumbnail()) : the_post_thumbnail('thumbnail', array('alt' => "", 'title' => "")); endif; ?>
      <?php the_excerpt(__('Read more&hellip;', 'qmo')); ?>
    <?php endif; ?>
    </div>
    
    <?php if (get_the_tags()) : ?>
      <?php the_tags('<p class="entry-tags"><strong>'.__('Tags:','qmo').'</strong> ',', ',''); ?>
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

</div><?php /* end #content-main */ ?>

<div id="content-sub" class="vcalendar" role="complementary">
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-archive') ) : else : endif; ?>
</div>
<?php get_footer(); ?>
