<?php do_action( 'bp_before_activity_loop' ) ?>

<?php if ( bp_has_activities( 'action=new_blog_post,new_forum_topic,new_forum_post' ) ) : ?>

		<div class="list-pagination">
			<p class="pag-count"><?php bp_activity_pagination_count(); ?></p>
		<?php if ( bp_get_activity_pagination_links() ) : ?>
			<p class="pages"><?php bp_activity_pagination_links(); ?></p>
		<?php endif; ?>
		</div>

	<?php if ( empty( $_POST['page'] ) ) : ?>
		<ul id="activity-stream" class="member activity-list item-list hfeed">
	<?php endif; ?>

	<?php while ( bp_activities() ) : bp_the_activity(); ?>

		<?php include( locate_template( array( 'members/single/activity/member-activity-entry.php' ), false ) ) ?>

	<?php endwhile; ?>

	<?php if ( empty( $_POST['page'] ) ) : ?>
		</ul>
	<?php endif; ?>
	
	<?php if ( bp_get_activity_pagination_links() ) : ?>
	<div class="list-pagination">
		<p class="pages"><?php bp_activity_pagination_links(); ?></p>
  </div>
	<?php endif; ?>

<?php else : ?>
	<div id="message" class="info">
		<p><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ) ?></p>
	</div>
<?php endif; ?>

<?php do_action( 'bp_after_activity_loop' ) ?>
