<?php if ( bp_has_forum_topic_posts() ) : ?>

	<form action="<?php bp_forum_topic_action() ?>" method="post" class="standard-form full">

		<div id="subnav" class="forum-head item-list-tabs no-ajax">
  		<p class="forum-buttons">
        <a href="<?php bp_forum_permalink(); ?>"><?php printf(__( '%s Forum', 'qmo' ),bp_group_name()); ?></a> 
        <?php if ( !get_option('bp-disable-forum-directory') ) : ?>
        | <a href="<?php bp_forum_directory_permalink(); ?>"><?php _e( 'All Forums', 'qmo') ?></a>
        <?php endif; ?>
  		</p>
			<p class="pag-count"><?php bp_the_topic_pagination_count(); ?></p>
		</div>

		<div id="topic-meta">
		<?php if ( bp_group_is_admin() || bp_group_is_mod() || bp_get_the_topic_is_mine() ) : ?>
		  <p class="admin-links"><?php bp_the_topic_admin_links(); ?></p>
		<?php endif; ?>
			<h2><?php bp_the_topic_title(); ?> <span>(<?php bp_the_topic_total_post_count(); ?>)</span></h2>
		</div>

		<ul id="topic-post-list" class="item-list hfeed">
			<?php while ( bp_forum_topic_posts() ) : bp_the_forum_topic_post(); ?>

				<li id="post-<?php bp_the_topic_post_id() ?>" class="hentry">
					<div class="poster-meta">
						<a href="<?php bp_the_topic_post_poster_link(); ?>">
							<?php bp_the_topic_post_poster_avatar( 'width=40&height=40' ) ?>
						</a>
						<span class="entry-title"><?php echo sprintf( __( '%s said %s ago:', 'buddypress' ), bp_get_the_topic_post_poster_name(), bp_get_the_topic_post_time_since() ); ?></span>
				    <a href="#post-<?php bp_the_topic_post_id(); ?>" class="permalink" rel="bookmark" title="<?php _e( 'Permanent link to this post', 'buddypress' ) ?>">#</a>
					</div>

					<div class="entry-content">
						<?php bp_the_topic_post_content(); ?>
					</div>
				
		  <?php /* No admin links on the first post */
		    global $topic_template; 
		    if ( $topic_template->current_post != 0 && $topic_template->pag_page == 1 ) : ?>	
				<?php if ( bp_group_is_admin() || bp_group_is_mod() || bp_get_the_topic_post_is_mine() ) : ?>
					<p class="admin-links">
						<?php bp_the_topic_post_admin_links(); ?>
					</p>
				<?php endif; ?>
		  <?php endif; ?>
				</li>

			<?php endwhile; ?>
		</ul>
    
    <?php if (bp_get_the_topic_pagination()) : ?>
		<p class="pages no-ajax"><?php bp_the_topic_pagination(); ?></p>
		<?php endif; ?>

		<?php if ( ( is_user_logged_in() && 'public' == bp_get_group_status() ) || bp_group_is_member() ) : ?>

			<?php if ( bp_get_the_topic_is_last_page() ) : ?>

				<?php if ( bp_get_the_topic_is_topic_open() ) : ?>

					<div id="post-topic-reply">
						<?php if ( !bp_group_is_member() ) : ?>
							<p><?php _e( 'You will auto join this group when you reply to this topic.', 'buddypress' ); ?></p>
						<?php endif; ?>

						<?php do_action( 'groups_forum_new_reply_before' ); ?>
						<h3><?php _e( 'Add a reply', 'qmo' ) ?></h3>
        		<?php if (is_user_logged_in()) : ?>
    				  <?php global $current_user; get_currentuserinfo(); 
    				    echo bp_core_fetch_avatar( 'width=40&height=40&item_id='.$current_user->ID ); ?>
    				<?php endif; ?>
    				<p><textarea name="reply_text" id="reply_text" rows="6" cols="60"></textarea></p>
						<p class="submit"><button type="submit" name="submit_reply" id="submit"><?php _e( 'Post Reply', 'buddypress' ); ?></button></p>
						<?php do_action( 'groups_forum_new_reply_after' ); ?>

						<?php wp_nonce_field( 'bp_forums_new_reply' ); ?>
					</div>

				<?php else : ?>

					<div id="message" class="info">
						<p><?php _e( 'This topic is closed, replies are no longer accepted.', 'buddypress' ) ?></p>
					</div>

				<?php endif; ?>

			<?php endif; ?>

		<?php endif; ?>

	</form>
<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There are no posts for this topic.', 'buddypress' ) ?></p>
	</div>

<?php endif;?>