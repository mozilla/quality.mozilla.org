<?php get_header(); ?>
<section id="content-main" class="hfeed" role="main">

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

    <h1 class="section-title"><?php the_title(); ?></h1>

  <?php endwhile; ?>

    <?php $teams_page = get_page_by_path('teams')->ID;
    $teams = new WP_Query(array('post_type' => 'page','post_status' => 'publish','post_parent' => $teams_page, 'posts_per_page' => -1, 'order' => 'ASC', 'orderby' => 'menu_order'));
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

</section><?php /* end #content-main */ ?>

<section id="content-sub" role="complementary">
<?php include (TEMPLATEPATH . '/user-state.php'); ?>
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-teams') ) : else : endif; ?>
</section>


<?php get_footer(); ?>
