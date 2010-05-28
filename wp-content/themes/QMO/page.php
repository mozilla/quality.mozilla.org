<?php get_header(); ?>
<div id="content-main" class="hfeed" role="main">
<?php if (have_posts()) : while (have_posts()) : the_post(); // The Loop ?>

    <?php if (fc_is_subpage()) : ?>
      <?php if(function_exists('bcn_display')) : ?>
      <p class="crumbs">
      <?php bcn_display(); ?>
      </p>
      <?php else : ?>
      <p class="crumbs"><a href="<?php echo get_permalink($post->post_parent); ?>" title="<?php _e('Return to &ldquo;'.get_the_title($post->post_parent).'&rdquo;','qmo'); ?>">&larr; <?php echo get_the_title($post->post_parent); ?></a></p>
      <?php endif; ?>    
    <?php endif;?>

    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
      <h1 class="entry-title section-title"><?php the_title(); ?></h1>
      <div class="entry-content">
        <?php if (has_post_thumbnail()) : the_post_thumbnail('thumbnail', array('alt' => "", 'title' => "")); endif; ?>
        <?php the_content('Read the rest of this entry &hellip;'); ?>
        <?php wp_link_pages(array('before' => '<p class="pages"><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number', 'link_before' => '<b>', 'link_after' => '</b>')); ?>
      </div>

      <?php if (get_the_tags()) : ?>
        <?php the_tags('<p class="entry-tags"><strong>Tags:</strong> ',', ',''); ?>
      <?php endif; ?>

      <div class="entry-meta">
        <p class="vcard">Last modified by <a class="fn url author" title="See all <?php the_author_posts() ?> posts by <?php the_author(); ?>" href="<?php echo get_author_posts_url($authordata->ID, $authordata->user_nicename); ?>"><?php the_author(); ?></a>
        on <?php the_modified_time(get_option('date_format')); ?>
        at <abbr class="updated" title="<?php the_modified_time('Y-m-d\TH:i:sP'); ?>"><?php the_modified_time(); ?></abbr>.
        <?php if ( current_user_can( 'edit_page', $post->ID ) ) : ?><span class="edit"><?php edit_post_link('Edit', '', ''); ?></span><?php endif; ?>
        </p>
      </div>
    </div>
    <?php endwhile; ?>

    <?php comments_template(); ?>

  <?php else : ?>

  <h1 class="section-title"><?php _e('Sorry, there&#8217;s nothing to see here.','qmo'); ?></h1>

<?php endif; ?>

</div><?php /* end #content-main */ ?>

<?php get_footer(); ?>
