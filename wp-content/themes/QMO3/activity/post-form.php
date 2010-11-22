<form action="<?php bp_activity_post_form_action() ?>" method="post" id="whats-new-form" name="whats-new-form">

	<?php do_action( 'bp_before_activity_post_form' ) ?>

	<?php if ( isset( $_GET['r'] ) ) : ?>
		<div id="message" class="info">
			<p><?php printf( __( 'You are mentioning %s in a new update. We&#8217;ll send them a notification of your message.', 'buddypress' ), bp_get_mentioned_user_display_name( $_GET['r'] ) ) ?></p>
		</div>
	<?php endif; ?>

	<div id="whats-new-avatar">
		<a href="<?php echo bp_loggedin_user_domain() ?>">
			<?php bp_loggedin_user_avatar( 'width=60&height=60' ) ?>
		</a>
	</div>

	<h3>
		<?php if ( bp_is_group() ) : ?>
			<?php printf( __( "What&#8217;s new in %s, %s?", 'buddypress' ), bp_get_group_name(), bp_get_user_firstname() ) ?>
		<?php else : ?>
			<?php printf( __( "What&#8217;s new %s?", 'buddypress' ), bp_get_user_firstname() ) ?>
		<?php endif; ?>
	</h3>

	<div id="whats-new-content">
		<textarea name="whats-new" id="whats-new" value="" rows="4" cols="60"><?php if ( isset( $_GET['r'] ) ) : ?>@<?php echo esc_attr( $_GET['r'] ) ?> <?php endif; ?></textarea>

		<div id="whats-new-options">
			<p id="whats-new-submit">
				<span class="ajax-loader"></span> &nbsp;
				<button type="submit" name="aw-whats-new-submit" id="aw-whats-new-submit"><?php _e( 'Post Update', 'buddypress' ) ?></button>
			</p>

			<?php if ( function_exists('bp_has_groups') && !bp_is_my_profile() && !bp_is_group() ) : ?>
				<p id="whats-new-post-in-box">
					<?php _e( 'Post in', 'buddypress' ) ?>:

					<select id="whats-new-post-in" name="whats-new-post-in">
						<option selected="selected" value="0"><?php _e( 'My Profile', 'buddypress' ) ?></option>
						<?php if ( bp_has_groups( 'user_id=' . bp_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100&populate_extras=0' ) ) : while ( bp_groups() ) : bp_the_group(); ?>
							<option value="<?php bp_group_id() ?>"><?php bp_group_name() ?></option>
						<?php endwhile; endif; ?>
					</select>
				</p>
				<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
			<?php elseif ( bp_is_group_home() ) : ?>
				<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
				<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id() ?>" />
			<?php endif; ?>

			<?php do_action( 'bp_activity_post_form_options' ) ?>

		</div><?php /* #whats-new-options */ ?>
	</div><?php /* #whats-new-content */ ?>

	<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
	<?php do_action( 'bp_after_activity_post_form' ) ?>

</form><!-- #whats-new-form -->
