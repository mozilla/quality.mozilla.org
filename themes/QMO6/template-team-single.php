<?php
/*
Template Name: Team Page
*/

get_header(); ?>
<?php get_sidebar('top'); ?>

<main id="content-main" class="main hfeed" role="main">
<?php if (have_posts()) : while (have_posts()) : the_post(); // The Loop ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class('team-page'); ?>>

      <h1 class="entry-title"><?php the_title(); ?></h1>

      <div class="entry-content">
        <?php if (has_post_thumbnail()) : the_post_thumbnail('thumbnail', array('alt' => "", 'title' => "")); endif; ?>
        <?php the_content('Read the rest of this entry &hellip;'); ?>
      </div>

    <?php if (is_user_logged_in()) : ?>
      <div class="entry-meta">
        <p class="vcard">Last modified by <a class="fn url author" title="See all <?php the_author_posts() ?> posts by <?php the_author(); ?>" href="<?php echo get_author_posts_url($authordata->ID, $authordata->user_nicename); ?>"><?php the_author(); ?></a>
        on <?php the_modified_time(get_option('date_format')); ?>
        at <time class="updated" pubdate datetime="<?php the_modified_time('Y-m-d\TH:i:sP'); ?>"><?php the_modified_time(); ?></time>.
        <?php if ( current_user_can( 'edit_page', $post->ID ) ) : ?><span class="edit"><?php edit_post_link('Edit', '', ''); ?></span><?php endif; ?>
        </p>
      </div>
    <?php endif; ?>
    </article>

    <?php endwhile; ?>

    <?php comments_template(); ?>

  <?php else : ?>

  <h1 class="section-title"><?php _e('Sorry, there&#8217;s nothing to see here.','qmo'); ?></h1>

<?php endif; ?>

</main><?php /* end #content-main */ ?>

<?php get_sidebar('bottom'); ?>
<?php get_footer(); ?>
