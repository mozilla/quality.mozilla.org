<?php
// Count search results
global $wp_query;
$total_results = $wp_query->found_posts;

get_header(); ?>
<?php get_sidebar('top'); ?>

  <main id="content-main" class="main" role="main">

  <?php if ( have_posts() ) : ?>

  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
  <h1 class="page-title">
  <?php if (is_category()) : ?><?php printf( __( 'Posts in “%s”', 'qmo' ), single_cat_title('',false) ); ?>
  <?php elseif (is_tag()) : ?><?php printf( __('Posts tagged with “%s”','qmo'), single_tag_title('',false) ); ?>
  <?php elseif (is_day()) : ?><?php printf( __('Posts from %s', 'qmo'), get_the_date() ); ?>
  <?php elseif (is_month()) : ?><?php printf( __('Posts from %s', 'qmo'), get_the_date('F, Y') ); ?>
  <?php elseif (is_year()) : ?><?php printf( __('Posts from %s', 'qmo'), get_the_date('Y') ); ?>
  <?php elseif (is_author()) : ?><?php printf( __('Posts by %s','qmo'), esc_html(get_userdata(intval($author))->display_name) ); ?></span>
  <?php elseif (is_search()) : ?><?php printf( _n('We found one result for “%2$s”', 'We found %1$s results for “%2$s”', $total_results, 'qmo'), $total_results, esc_html(get_search_query()) ); ?>
  <?php else : ?><?php _e('Archives','qmo'); ?>
  <?php endif; ?>
  </h1>

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

    <article id="post-0" class="post no-results not-found">
      <header class="entry-header">
        <h1 class="entry-title"><?php _e( 'Nothing Found', 'qmo' ); ?></h1>
      </header><!-- .entry-header -->

      <div class="entry-content">
        <p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'qmo' ); ?></p>
        <?php get_search_form(); ?>
      </div><!-- .entry-content -->
    </article><!-- #post-0 -->

  <?php endif; ?>

  </main><!-- #content-main -->

<?php get_sidebar('bottom'); ?>
<?php get_footer(); ?>
