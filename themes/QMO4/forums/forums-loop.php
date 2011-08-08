<?php if ( bp_has_forum_topics( bp_ajax_querystring( 'forums' ) ) ) : ?>

  <div class="list-head">
  <?php if ( bp_is_group_forum() && is_user_logged_in() ) : ?>
    <p class="new-topic"><a href="#new-topic" class="button"><?php _e( 'New Topic', 'qmo' ) ?></a></p>
  <?php endif; ?>
    <p id="pag-count-head" class="pag-count"><?php bp_forum_pagination_count(); ?></p>
  <?php if (bp_get_forum_pagination()) : ?>
    <p id="pages-head" class="pages"><?php bp_forum_pagination(); ?></p>
  <?php endif; ?>
  </div>
  
  <?php do_action( 'bp_before_directory_forums_list' ) ?>

  <table class="topiclist">
    <thead>
      <tr>
        <th class="info" scope="col"><?php _e( 'Topic', 'qmo' ); ?></th>
        <th class="posts" scope="col"><?php _e( 'Posts', 'qmo' ); ?></th>
        <th class="lastpost" scope="col"><?php _e( 'Latest Post', 'qmo' ); ?></th>
        <?php do_action( 'bp_directory_forums_extra_cell_head' ); ?>
      </tr>
    </thead>
    <tbody>

    <?php while ( bp_forum_topics() ) : bp_the_forum_topic(); ?>

    <tr class="<?php bp_the_topic_css_class(); ?>">
      <td class="info">
        <h3 class="title">
          <a class="view" href="<?php bp_the_topic_permalink(); ?>" title="Permanent link to &ldquo;<?php bp_the_topic_title(); ?>&rdquo;">
            <?php bp_the_topic_title(); ?>
          </a>
        </h3>
        <?php if ( !bp_is_group_forum() ) : ?>
        <p class="meta">In <em><a href="<?php bp_the_topic_object_permalink(); ?>"><?php bp_the_topic_object_name(); ?></a></em></p>
        <?php endif; ?>
      </td>
      <td class="posts"><?php bp_the_topic_total_posts(); ?></td>
      <td class="lastpost">
        <?php bp_the_topic_last_poster_name(); ?>
        <span class="freshness"><?php bp_the_topic_time_since_last_post(); ?> ago</span>
      </td>
      <?php do_action( 'bp_directory_forums_extra_cell' ); ?>
    </tr>
    <?php do_action( 'bp_directory_forums_extra_row' ); ?>
    <?php endwhile; ?>
    </tbody>
  </table>

  <?php do_action( 'bp_after_directory_forums_list' ); ?>
  
  <?php if (bp_get_forum_pagination()) : ?>
  <p id="pages-foot" class="pages"><?php bp_forum_pagination(); ?></p>
  <?php endif; ?>

<?php else: ?>

  <div id="message" class="info">
    <p><?php _e( 'Sorry, there were no forum topics found.', 'qmo' ); ?></p>
  </div>

<?php endif;?>