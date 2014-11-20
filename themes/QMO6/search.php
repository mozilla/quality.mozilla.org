<?php
// Count search results
global $wp_query;
$total_results = $wp_query->found_posts;

get_header(); ?>
<?php get_sidebar('top'); ?>

<main id="content-main" class="main" role="main">

  <?php if ( have_posts() ) : ?>

  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
  <h1 class="page-title"><?php printf( _n('We found one result for “%2$s”', 'We found %1$s results for “%2$s”', $total_results, 'qmo'), $total_results, esc_html(get_search_query()) ); ?></h1>

    <?php if (fc_show_posts_nav()) : ?>
    <nav class="nav-paging top">
      <ul role="navigation">
        <?php if ( $paged < $wp_query->max_num_pages ) : ?><li class="prev"><?php next_posts_link(__('Older posts','qmo')); ?></li><?php endif; ?>
        <?php if ( $paged > 1 ) : ?><li class="next"><?php previous_posts_link(__('Newer posts','qmo')); ?></li><?php endif; ?>
      </ul>
    </nav>
    <?php endif; ?>

    <?php /* Start the Loop */ ?>
    <?php while ( have_posts() ) : the_post(); ?>

        <?php get_template_part( 'content', 'summary' ); ?>

    <?php endwhile; ?>

    <?php if (fc_show_posts_nav()) : ?>
    <nav class="nav-paging bottom">
      <ul role="navigation">
        <?php if ( $paged < $wp_query->max_num_pages ) : ?><li class="prev"><?php next_posts_link(__('Older posts','qmo')); ?></li><?php endif; ?>
        <?php if ( $paged > 1 ) : ?><li class="next"><?php previous_posts_link(__('Newer posts','qmo')); ?></li><?php endif; ?>
      </ul>
    </nav>
    <?php endif; ?>

  <?php else : ?>

    <h1 class="page-title"><?php _e( 'Nothing Found', 'qmo' ); ?></h1>

    <div class="entry-content">
      <p><?php printf( __( 'Sorry, we didn\'t find anything for “%s.” Try another search.', 'qmo' ), esc_html(get_search_query()) ); ?></p>
      <?php get_search_form(); ?>
    </div><!-- .entry-content -->

  <?php endif; ?>

  </main><!-- #content-main -->

<?php get_sidebar('bottom'); ?>
<?php get_footer(); ?>
