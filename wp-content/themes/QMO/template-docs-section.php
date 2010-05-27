<?php 
/*
Template Name: Docs Section Landing Page
*/
get_header(); ?>
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
    
    <?php comments_template(); ?>

  <?php else : ?>

  <h1 class="page-title"><?php _e('Sorry, there&#8217;s nothing to see here.','qmo'); ?></h1>

<?php endif; ?>

</div><?php /* end #content-main */ ?>

<div id="content-sub" role="complementary">
<?php $children = wp_list_pages('title_li=&child_of='.$post->ID.'&echo=0&sort_column=menu_order');
  if ($children) : ?>
  <div class="widget related_pages">
    <h3 class="widgettitle">Related Docs</h3>
    <ul class="page-tree">
    <?php echo $children; ?>
    </ul>
  </div>
<?php endif; ?>
      
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-docs-section') ) : else : endif; ?>
</div>

<?php get_footer(); ?>
