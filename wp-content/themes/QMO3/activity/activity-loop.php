<?php do_action( 'bp_before_activity_loop' ); ?>

<?php if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) : ?>

	<div class="list-pagination">
		<p id="pag-count-head" class="pag-count"><?php bp_activity_pagination_count(); ?></p>
	<?php if ( bp_get_activity_pagination_links() ) : ?>
		<p id="pages-head" class="pages"><?php bp_activity_pagination_links(); ?></p>
	<?php endif; ?>
	</div>

	<?php if ( empty( $_POST['page'] ) ) : ?>
		<ul id="activity-stream" class="activity-list item-list hfeed">
	<?php endif; ?>

	<?php while ( bp_activities() ) : bp_the_activity(); ?>

		<?php include( locate_template( array( 'activity/entry.php' ), false ) ); ?>

	<?php endwhile; ?>

	<?php if ( empty( $_POST['page'] ) ) : ?>
		</ul>
	<?php endif; ?>

	<?php if ( bp_get_activity_pagination_links() ) : ?>
	<div class="list-pagination">
		<p id="pages-foot" class="pages"><?php bp_activity_pagination_links(); ?></p>
  </div>
	<?php endif; ?>
	
<?php else : ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_activity_loop' ); ?>

<form action="" name="activity-loop-form" id="activity-loop-form" method="post">
	<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ) ?>
</form>