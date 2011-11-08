<?php /* This template is used by activity-loop.php and AJAX functions to show each activity */ 

// Fetch the formats
$date_format = get_option("date_format");
$time_format = get_option("time_format");

// Fetch some IDs
$events_cat = get_category_by_slug('events')->cat_ID;
$synd_cat = get_category_by_slug('syndicated')->cat_ID;
$twitter_cat = get_category_by_slug('twitter')->cat_ID;

global $authordata;
?>

<?php do_action( 'bp_before_activity_entry' ); ?>
<li class="item <?php bp_activity_css_class(); ?>" id="activity-<?php bp_activity_id(); ?>">

<?php /* If the activity is a blog post, we'll get the full post */
  if ( bp_get_activity_type() == 'new_blog_post' ) :

    $post_id = bp_get_activity_secondary_item_id();
    $post = get_post($post_id); 
    setup_postdata($post); ?>

    <article id="post-<?php the_ID(); ?>" 
      <?php if ( function_exists('is_event') && is_event() ) : post_class('vevent'); 
        elseif (in_category('twitter')) : post_class('tweet');
        elseif ( function_exists('is_syndicated') && is_syndicated() ) : post_class('syndicated');
        else : post_class(); endif; ?> role="article">
    <?php if (!in_category('twitter')) : ?>
      <h1 class="entry-title <?php if ( function_exists('is_event') && is_event() ) : echo 'summary'; endif; ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent link to &#8220;<?php the_title_attribute(); ?>&#8221;" <?php if ( function_exists('is_event') && is_event() ) : echo 'class="url"'; endif; ?>><?php the_title(); ?></a></h1>
    <?php endif; ?>
    <?php if ( ( function_exists('is_event') && is_event() ) || in_category('events') ) : ?>
      <div class="entry-meta">
        <p class="event-flag">Event</p>
        <p class="vcard">Posted by <a class="fn url author" title="See all <?php the_author_posts(); ?> posts by <?php the_author(); ?>" href="<?php echo get_author_posts_url($authordata->ID, $authordata->user_nicename); ?>"><?php the_author(); ?></a> 
        on <?php the_time($date_format); ?> at <abbr class="updated" title="<?php the_time('Y-m-d\TH:i:sP'); ?>"><?php the_time(); ?></abbr>.
        <?php if ( current_user_can( 'edit_page', $post->ID ) ) : ?><span class="edit"><?php edit_post_link('Edit', '', ''); ?></span><?php endif; ?>
        </p>
      </div>
    <?php elseif ( fc_is_child('docs') ) : ?>
      <p class="doc-flag">Doc</p>
    <?php elseif ( fc_is_child('teams') ) : ?>
      <p class="team-flag">Team</p>
    <?php elseif ( in_category('twitter') ) : ?>
      <p class="tweet-flag">Tweet</p>
    <?php elseif ( function_exists('is_syndicated') && is_syndicated() ) : ?>
      <div class="entry-meta">
        <p class="entry-posted">
          <a class="posted-month" href="<?php echo get_month_link(get_the_time('Y'), get_the_time('m')); ?>" title="See all posts from <?php echo get_the_time('F, Y'); ?>"><?php the_time('M'); ?></a>
          <span class="posted-date"><?php the_time('j'); ?></span>
          <span class="posted-year"><?php the_time('Y'); ?></span>
          <time class="updated" pubdate datetime="<?php the_time('Y-m-d\TH:i:sP'); ?>"><?php the_time(); ?></time>
        </p>
        <p>Syndicated from <a href="<?php the_syndication_source_link(); ?>" rel="nofollow external"><?php the_syndication_source(); ?></a></p>
      </div>
    <?php else : ?>
      <div class="entry-meta">
        <p class="entry-posted">
          <a class="posted-month" href="<?php echo get_month_link(get_the_time('Y'), get_the_time('m')); ?>" title="See all posts from <?php echo get_the_time('F, Y'); ?>"><?php the_time('M'); ?></a>
          <span class="posted-date"><?php the_time('j'); ?></span>
          <span class="posted-year"><?php the_time('Y'); ?></span>
          <time class="updated" pubdate datetime="<?php the_time('Y-m-d\TH:i:sP'); ?>"><?php the_time(); ?></time>
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
    </article><!-- /post -->

<?php else : // if it's not a blog post, it's regular activity ?>

    <div class="activity-avatar">
      <a href="<?php bp_activity_user_link(); ?>">
        <?php bp_activity_avatar( 'width=48&height=48' ); ?>
      </a>
    </div>

    <div class="activity-content">
      <div class="activity-header entry-title">
        <?php bp_activity_action(); ?>
      </div>

      <?php if ( bp_get_activity_content_body() ) : ?>
        <div class="activity-inner entry-content">
          <?php bp_activity_content_body(); ?>
        </div>
      <?php endif; ?>

      <?php do_action( 'bp_activity_entry_content' ); ?>
    </div>

<?php endif; ?>

</li>

<?php do_action( 'bp_after_activity_entry' ); ?>
