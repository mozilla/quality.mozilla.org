<?php do_action( 'bp_before_member_activity_content' ) ?>

<div class="activity">
  <?php locate_template( array( 'members/single/activity/member-activity-loop.php' ), true ) ?>
</div><!-- .activity -->

<?php do_action( 'bp_after_member_activity_content' ) ?>
