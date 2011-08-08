<?php get_header(); ?>

<?php do_action( 'bp_before_directory_forums_content' ); ?>

<section id="content-main" class="hfeed" role="main">

    <form action="" method="post" id="forums-search-form" class="dir-form">
      <h1 class="page-title"><?php _e( 'Team Forums', 'qmo' ) ?></h1>
      <?php if ( is_user_logged_in() ) : ?> 
       <p id="new-button"><a class="button" href="#new-topic" id="new-topic-button"><?php _e( 'New Topic', 'buddypress' ) ?></a></p>
      <?php endif; ?>
      <div id="forums-dir-search" class="dir-search">
        <?php bp_directory_forums_search_form(); ?>
      </div>
    </form>

    <div id="new-topic-post">
    <?php if ( is_user_logged_in() ) : ?>
      <?php if ( bp_has_groups( 'user_id=' . bp_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100' ) ) : ?>

      <form action="" method="post" id="forum-topic-form" class="standard-form full">
        <?php do_action( 'groups_forum_new_topic_before' ) ?>
        <fieldset id="post-new-topic">
          <legend><?php _e( 'Post a New Topic', 'qmo' ); ?></legend>
          <ul>
            <li>
              <label for="topic_title"><?php _e( 'Title:', 'buddypress' ) ?></label>
              <input type="text" name="topic_title" id="topic_title" value="" />
            </li>
            <li>
              <label for="topic_text"><?php _e( 'Content:', 'buddypress' ) ?></label>
              <textarea name="topic_text" id="topic_text" rows="8" cols="60"></textarea>
            </li>
            <li>
              <label for="topic_tags"><?php _e( 'Tags (comma separated):', 'buddypress' ) ?></label>
              <input type="text" name="topic_tags" id="topic_tags" value="" />
            </li>
            <li>
              <label for="topic_group_id"><?php _e( 'Post In Group Forum:', 'buddypress' ) ?></label>
              <select id="topic_group_id" name="topic_group_id">
                <?php while ( bp_groups() ) : bp_the_group(); ?>
                  <?php if ( 'public' == bp_get_group_status() ) : ?>
                    <option value="<?php bp_group_id() ?>"><?php bp_group_name() ?></option>
                  <?php endif; ?>
                <?php endwhile; ?>
              </select>
            </li>
          </ul>
        <?php do_action( 'groups_forum_new_topic_after' ); ?>
          <p class="submit">
            <button type="submit" name="submit_topic" id="submit"><?php _e( 'Post Topic', 'buddypress' ); ?></button>
            <input type="button" name="submit_topic_cancel" id="submit_topic_cancel" value="<?php _e( 'Cancel', 'buddypress' ); ?>"/>
          </p>
        </fieldset>
        <?php wp_nonce_field( 'bp_forums_new_topic' ); ?>
      </form>

      <?php else : ?>
          <div id="message" class="info">
            <p><?php printf( __( "You are not a member of any groups so you don't have any group forums you can post in. To start posting, first find a group that matches the topic subject you'd like to start. If this group does not exist, why not <a href='%s'>create a new group</a>? Once you have joined or created the group you can post your topic in that group's forum.", 'buddypress' ), site_url( BP_GROUPS_SLUG . '/create/' ) ) ?></p>
          </div>
      <?php endif; ?>
    <?php endif; ?>
    </div>

    <form action="" method="post" id="forums-directory-form" class="dir-form full">

      <div class="item-list-tabs" id="subnav">
        <ul>
          <li class="selected" id="forums-all"><a href="<?php bp_root_domain() ?>"><?php printf( __( 'All Topics (%s)', 'buddypress' ), bp_get_forum_topic_count() ) ?></a></li>

          <?php if ( is_user_logged_in() && bp_get_forum_topic_count_for_user( bp_loggedin_user_id() ) ) : ?>
            <li id="forums-personal"><a href="<?php echo bp_loggedin_user_domain() . BP_GROUPS_SLUG . '/' ?>"><?php printf( __( 'My Topics (%s)', 'buddypress' ), bp_get_forum_topic_count_for_user( bp_loggedin_user_id() ) ) ?></a></li>
          <?php endif; ?>

          <?php do_action( 'bp_forums_directory_group_types' ) ?>

          <li id="forums-order-select" class="last filter">

            <?php _e( 'Order By:', 'buddypress' ) ?>
            <select>
              <option value="active"><?php _e( 'Last Active', 'buddypress' ) ?></option>
              <option value="popular"><?php _e( 'Most Posts', 'buddypress' ) ?></option>
              <option value="unreplied"><?php _e( 'Unreplied', 'buddypress' ) ?></option>

              <?php do_action( 'bp_forums_directory_order_options' ) ?>
            </select>
          </li>
        </ul>
      </div>

      <div id="forums-dir-list" class="forums dir-list">
        <?php locate_template( array( 'forums/forums-loop.php' ), true ) ?>
      </div>

      <?php do_action( 'bp_directory_forums_content' ) ?>

      <?php wp_nonce_field( 'directory_forums', '_wpnonce-forums-filter' ) ?>

      <?php do_action( 'bp_after_directory_forums_content' ) ?>

    </form>

</section>

<section id="content-sub" role="complementary">
<?php include (TEMPLATEPATH . '/user-state.php'); ?>
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-community') ) : else : endif; ?>
</section>
<?php get_footer(); ?>
