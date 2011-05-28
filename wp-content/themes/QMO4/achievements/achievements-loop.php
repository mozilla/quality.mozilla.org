<?php do_action( 'dpa_before_achievements_loop' ) ?>

<?php if ( dpa_has_achievements( bp_ajax_querystring( 'achievements' ) ) ) : ?>

	<div class="list-head">
		<p id="pag-count-head" class="pag-count"><?php dpa_achievements_pagination_count(); ?></p>
  <?php if ( dpa_achievements_pagination_links() ) : ?>
		<p id="pages-head" class="pages"><?php dpa_achievements_pagination_links() ?></p>
  <?php endif; ?>
	</div>

	<ul id="achievements-list" class="item-list">
	
	<?php while ( dpa_achievements() ) : dpa_the_achievement(); ?>
		<li class="item <?php dpa_achievement_directory_class() ?>">
			<div class="item-avatar">
				<a href="<?php dpa_achievement_slug_permalink() ?>"><?php dpa_achievement_picture() ?></a>
				<span class="points" title="<?php printf( __( "This Achievement is worth %s points.", 'dpa' ), bp_core_number_format( dpa_get_achievement_points() ) ) ?>"><?php dpa_achievement_points(); ?></span>
				<span class="meta">
					<?php if ( !dpa_get_achievement_is_active() ) : ?>
						<?php _e( 'Inactive', 'dpa' ) ?>
					<?php else : ?>
						<?php dpa_achievement_type() ?>
					<?php endif; ?>
					<?php do_action( 'dpa_directory_achievements_actions_meta' ) ?>
				</span>
			</div>

		  <h3 class="item-title"><a href="<?php dpa_achievement_slug_permalink() ?>"><?php dpa_achievement_name() ?></a></h3>
		  <div class="item-desc"><?php dpa_achievement_description_excerpt(); ?></div>
		  <?php if ( dpa_is_achievement_unlocked() ) : ?>
		  <p class="item-meta"><?php printf( __( "Unlocked %s", 'dpa' ), dpa_get_achievement_unlocked_ago() ) ?></p>
		  <?php endif; ?>
		  <?php do_action( 'dpa_directory_achievements_item' ) ?>

			<div class="action">
				<?php do_action( 'dpa_directory_achievements_actions_top' ) ?>
				<p><?php dpa_achievements_quickadmin() ?></p>
				<?php do_action( 'dpa_directory_achievements_actions_bottom' ) ?>
			</div>
		</li>
	<?php endwhile; ?>

	</ul>

	<?php do_action( 'dpa_after_achievements_loop' ); ?>

  <?php if ( dpa_achievements_pagination_links() ) : ?>
		<p id="pages-foot" class="pages"><?php dpa_achievements_pagination_links() ?></p>
  <?php endif; ?>

<?php else: ?>

	<div id="message" class="info">
		<?php if ( !empty( $_REQUEST['search_terms'] ) ) : ?>
			<p><?php echo sprintf( __( 'There were no Achievements found matching &ldquo;%s.&rdquo;', 'dpa' ), apply_filters( 'dpa_get_achievements_search_query', stripslashes( $_REQUEST['search_terms'] ) ) ) ?></p>
		<?php elseif ( dpa_is_member_my_achievements_page() && bp_is_my_profile() ) : ?>
			<p><?php _e( "You haven't unlocked any Achievements yet", 'dpa' ) ?></p>
		<?php elseif ( dpa_is_member_my_achievements_page() && !bp_is_my_profile() ) : ?>
			<p><?php printf( __( "%s hasn't unlocked any Achievements yet", 'dpa' ), bp_get_displayed_user_fullname() ) ?></p>
		<?php else : ?>
			<p><?php _e( 'Oops, no Achievements were found!', 'dpa' ) ?></p>
		<?php endif; ?>
	</div>

<?php endif; ?>