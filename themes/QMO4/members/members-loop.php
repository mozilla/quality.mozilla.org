<?php global $members_template;
 /* Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter() */ ?>

<?php do_action( 'bp_before_members_loop' ); ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) : ?>

  <div class="list-head">
    <p id="pag-count-head" class="pag-count"><?php bp_members_pagination_count(); ?></p>
  <?php if ( bp_get_members_pagination_links() ) : ?>
    <p id="pages-head" class="pages"><?php bp_members_pagination_links(); ?></p>
  <?php endif; ?>
  </div>

  <?php do_action( 'bp_before_directory_members_list' ); ?>

  <ul id="member-list" class="item-list">

  <?php while ( bp_members() ) : bp_the_member(); ?>
    <li class="item member vcard">
      <a href="<?php bp_member_permalink(); ?>profile/" class="fn url"><?php bp_member_avatar('width=50&height=50'); ?> <strong><?php bp_displayed_user_fullname(); ?></strong></a>
      <?php if (bp_get_member_profile_data('field=IRC Nickname') != "") : ?>
        <span class="nickname">IRC: <?php bp_member_profile_data( 'field=IRC Nickname' ); ?></span>
      <?php endif; ?>
      <br><span class="activity"><?php bp_last_activity( $members_template->member->id ); ?></span>

      <?php if ( bp_is_active( 'friends' ) ) : ?>
        <div class="action">
          <?php bp_member_add_friend_button(); ?>
          <?php do_action( 'bp_directory_members_actions' ); ?>
        </div>
      <?php endif; ?>
    </li>
  <?php endwhile; ?>

  </ul>

  <?php do_action( 'bp_after_directory_members_list' ); ?>
  <?php bp_member_hidden_fields(); ?>

  <?php if ( bp_get_members_pagination_links() ) : ?>
  <p id="pages-foot" class="pages"><?php bp_members_pagination_links(); ?></p>
  <?php endif; ?>

<?php else: ?>

  <div id="message" class="info">
    <p><?php _e( "Sorry, no members were found.", 'buddypress' ); ?></p>
  </div>

<?php endif; ?>

<?php do_action( 'bp_after_members_loop' ); ?>