<?php get_header(); ?>
<?php get_sidebar('top'); ?>

  <main id="content-main" class="main" role="main">

    <?php while ( have_posts() ) : the_post(); ?>

      <?php get_template_part( 'content', 'page' ); ?>

      <?php comments_template( '', true ); ?>

    <?php endwhile; // end of the loop. ?>

  </main><!-- #content-main -->

<?php get_sidebar('bottom'); ?>
<?php get_footer(); ?>
