<?php /* This template is used by activity-loop.php and AJAX functions to show each activity */ ?>

<?php do_action( 'bp_before_activity_entry' ) ?>

<li class="<?php bp_activity_css_class(); ?> hentry item" id="activity-<?php bp_activity_id() ?>">
  <div class="activity-avatar">
    <a href="<?php bp_activity_user_link(); ?>profile/">
      <?php bp_activity_avatar( 'width=30&height=30' ); ?>
    </a>
  </div>

  <div class="activity-content">
    <div class="activity-header entry-title">
      <?php bp_activity_action(); ?>
    </div>

    <?php if ( bp_get_activity_content_body() ) : ?>
      <div class="activity-inner entry-content">
        <?php bp_activity_content_body(); ?>
      </div>
    <?php endif; ?>

    <?php do_action( 'bp_activity_entry_content' ); ?>
  </div>
</li>

<?php do_action( 'bp_after_activity_entry' ) ?>

