<?php do_action( 'bp_before_group_forum_content' ) ?>

<?php if ( bp_is_group_forum_topic_edit() ) : ?>
  <?php locate_template( array( 'groups/single/forum/edit.php' ), true ); ?>

<?php elseif ( bp_is_group_forum_topic() ) : ?>
  <?php locate_template( array( 'groups/single/forum/topic.php' ), true ); ?>

<?php else : ?>

  <div class="forums single-forum">
    <?php locate_template( array( 'forums/forums-loop.php' ), true ); ?>
  </div><!-- .forums -->

  <?php do_action( 'bp_after_group_forum_content' ); ?>

  <?php if ( ( is_user_logged_in() && 'public' == bp_get_group_status() ) || bp_group_is_member() ) : ?>
    <script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.textarea-expander.js" type="text/javascript"></script>
    <form action="" method="post" id="new-topic" class="standard-form full">
      <fieldset id="post-new-topic">
        <legend><?php _e( 'Post a New Topic', 'qmo' ); ?></legend>
        <?php do_action( 'bp_before_group_forum_post_new' ); ?>
        <?php if ( !bp_group_is_member() ) : ?>
          <p><?php _e( 'You will automatically join this team when you start a new topic.', 'qmo' ) ?></p>
        <?php endif; ?>
            
        <?php if (is_user_logged_in()) : ?>
          <?php global $current_user; get_currentuserinfo(); 
            echo bp_core_fetch_avatar( 'width=40&height=40&item_id='.$current_user->ID ); ?>
        <?php endif; ?>

        <ul>
          <li>
            <label for="topic_title"><?php _e( 'Title:', 'buddypress' ) ?></label>
            <input type="text" name="topic_title" id="topic_title" value="" maxlength="100" />
          </li>
          <li>
            <label for="topic_text"><?php _e( 'Content:', 'buddypress' ) ?></label>
            <textarea name="topic_text" id="topic_text" rows="10" cols="60" class="expand100-400"></textarea>
          </li>
          <li>
            <label for="topic_tags"><?php _e( 'Tags (comma separated):', 'buddypress' ) ?></label>
            <input type="text" name="topic_tags" id="topic_tags" value="">
          </li>
        </ul>

        <?php do_action( 'bp_after_group_forum_post_new' ) ?>

        <p class="submit">
          <button type="submit" name="submit_topic" id="submit"><?php _e( 'Post Topic', 'buddypress' ); ?></button>
        </p>

        <?php wp_nonce_field( 'bp_forums_new_topic' ); ?>
      </fieldset>
    </form>

  <?php endif; ?>

<?php endif; ?>

