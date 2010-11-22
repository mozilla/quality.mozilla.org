<?php get_header(); ?>

<section id="content-main" class="hfeed" role="main">
	<?php do_action( 'bp_before_directory_activity_content' ); ?>
	<?php do_action( 'template_notices' ); ?>
	
	<div class="item-list-tabs no-ajax" id="subnav">
		<ul>
			<li class="feed"><a href="<?php bp_sitewide_activity_feed_link(); ?>" title="<?php _e( 'RSS Feed', 'buddypress' ); ?>"><?php _e( 'RSS', 'buddypress' ) ?></a></li>

			<?php do_action( 'bp_activity_syndication_options' ); ?>

			<li id="activity-filter-select" class="last">
				<select>
					<option value="new_blog_post,new_forum_topic,new_forum_post"><?php _e( 'No Filter', 'buddypress' ); ?></option>
					<?php if ( bp_is_active( 'blogs' ) ) : ?>
						<option value="new_blog_post"><?php _e( 'Show Blog Posts', 'buddypress' ); ?></option>
					<?php endif; ?>
					<?php if ( bp_is_active( 'forums' ) ) : ?>
						<option value="new_forum_topic"><?php _e( 'Show New Forum Topics', 'buddypress' ); ?></option>
						<option value="new_forum_post"><?php _e( 'Show Forum Replies', 'buddypress' ); ?></option>
					<?php endif; ?>
					<?php do_action( 'bp_activity_filter_options' ); ?>
				</select>
			</li>
		</ul>
	</div>

	<div id="activity-main" class="activity">
		<?php locate_template( array( 'activity/activity-loop.php' ), true ); ?>
	</div><!-- .activity -->

	<?php do_action( 'bp_after_directory_activity_content' ); ?>
</section>

<section id="content-sub" role="complementary">
<?php include (TEMPLATEPATH . '/user-state.php'); ?>
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-community') ) : else : endif; ?>
</section>

<?php get_footer(); ?>
