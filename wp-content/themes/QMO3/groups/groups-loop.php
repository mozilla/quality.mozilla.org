<?php /* Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter() */ ?>

<?php do_action( 'bp_before_groups_loop' ); ?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

  <?php while ( bp_groups() ) : bp_the_group(); ?>
    <div class="team">
      <div class="team-info">
        <h2 class="entry-title"><a href="<?php bp_group_permalink(); ?>"><?php bp_group_name(); ?> <?php bp_group_avatar( 'width=80&height=80' ); ?></a></h2>
        <?php bp_group_description(); ?>
      </div>
      <div class="team-meta">
        <p class="mem-count"><?php bp_group_member_count(); ?></p>
        <p class="activity"><?php printf( __( 'Last activity %s ago', 'buddypress' ), bp_get_group_last_active() ); ?></p>
        <?php do_action( 'bp_directory_groups_actions' ); ?>
      </div>
      <?php do_action( 'bp_directory_groups_item' ); ?>
    </div>
  <?php endwhile; ?>

  <?php do_action( 'bp_after_groups_loop' ); ?>

<?php else: ?>

<p><?php _e( 'Sorry, there are no teams.', 'qmo' ) ?></p>

<?php endif; ?>
