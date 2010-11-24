<?php if ( bp_has_forum_topic_posts() ) : ?>

	<form action="<?php bp_forum_topic_action() ?>" method="post" id="forum-topic-form" class="standard-form full">

		<div id="subnav" class="list-head item-list-tabs no-ajax">
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

		<?php if ( bp_group_is_member() ) : ?>

			<?php if ( bp_is_edit_topic() ) : ?>

				<div id="edit-topic">
					<?php do_action( 'bp_group_before_edit_forum_topic' ); ?>

					<p><strong><?php _e( 'Edit Topic:', 'buddypress' ); ?></strong></p>
					<p><label for="topic_title"><?php _e( 'Title:', 'buddypress' ); ?></label>
					<input type="text" name="topic_title" id="topic_title" value="<?php bp_the_topic_title() ?>" /></p>
					<p><label for="topic_text"><?php _e( 'Content:', 'buddypress' ); ?></label>
					<textarea name="topic_text" id="topic_text" rows="6" cols="60"><?php bp_the_topic_text() ?></textarea></p>
					<?php do_action( 'bp_group_after_edit_forum_topic' ) ?>

					<p class="submit"><button type="submit" name="save_changes" id="save_changes"><?php _e( 'Save Changes', 'buddypress' ); ?></button></p>

					<?php wp_nonce_field( 'bp_forums_edit_topic' ); ?>

				</div>

			<?php else : ?>

				<div id="edit-post">
					<?php do_action( 'bp_group_before_edit_forum_post' ); ?>

					<p><label for="post_text"><?php _e( 'Edit Post:', 'buddypress' ); ?></label>
          <textarea name="post_text" id="post_text" rows="6" cols="60"><?php bp_the_topic_post_edit_text(); ?></textarea></p>
					<?php do_action( 'bp_group_after_edit_forum_post' ); ?>
					<p class="submit"><button type="submit" name="save_changes" id="save_changes"><?php _e( 'Save Changes', 'buddypress' ); ?></button></p>

					<?php wp_nonce_field( 'bp_forums_edit_post' ); ?>
				</div>

			<?php endif; ?>

		<?php endif; ?>

	</form>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'This topic does not exist.', 'buddypress' ) ?></p>
	</div>

<?php endif;?>
