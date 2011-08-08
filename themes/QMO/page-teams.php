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
        <?php the_content('Read the rest of this entry &hellip;'); ?>
        <?php wp_link_pages(array('before' => '<p class="pages"><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number', 'link_before' => '<b>', 'link_after' => '</b>')); ?>
      </div>
    </div>

  <?php endwhile; ?>

    <?php $teams_page = get_page_by_path('teams')->ID;
    $teams = new WP_Query(array('post_type' => 'page','post_status' => 'publish','post_parent' => $teams_page, 'order' => 'ASC', 'orderby' => 'menu_order'));
    if ( $teams->have_posts() ) : ?>
      <div id="teams-list">
      <?php while ( $teams->have_posts() ) : $post = $teams->next_post(); ?>
        <div id="page-<?php echo $post->ID; ?>" class="team hentry">
          <h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent link to &#8220;<?php the_title_attribute(); ?>&#8221;">
          <?php if (function_exists('the_post_thumbnail') && has_post_thumbnail($post->ID) ) : ?>
            <?php echo get_the_post_thumbnail( $post->ID, 'team-icon-small', array('alt' => "", 'title' => "") ); ?>
          <?php endif; ?>
            <?php the_title(); ?></a>
          </h2>
          <p><?php echo $post->post_excerpt; ?></p>
        </div>
      <?php endwhile; ?>
      </div>
    <?php endif; ?>

  <?php else : ?>

  <h1 class="section-title"><?php _e('Sorry, there&#8217;s nothing to see here.','qmo'); ?></h1>

<?php endif; ?>

</div><?php /* end #content-main */ ?>

<?php get_footer(); ?>
