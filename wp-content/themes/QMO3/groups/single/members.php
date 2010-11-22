<?php if ( bp_group_has_members( 'exclude_admins_mods=0' ) ) : ?>

	<?php do_action( 'bp_before_group_members_content' ) ?>
  
	<div class="member-pagination no-ajax">
		<p class="pag-count"><?php bp_group_member_pagination_count(); ?></p>
  <?php if ( bp_group_member_needs_pagination() ) : ?>
	  <p class="pages"><?php bp_group_member_pagination(); ?></p>
	<?php endif; ?>
	</div>

	<?php do_action( 'bp_before_group_members_list' ) ?>

	<ul id="member-list" class="item-list">
  <?php global $members_template; 
		while ( bp_group_members() ) : bp_group_the_member(); ?>

		<li class="item member vcard">
		  <a href="<?php bp_group_member_domain(); ?>profile/" class="fn url"><?php bp_group_member_avatar_thumb(); ?> <strong><?php bp_group_member_name(); ?></strong></a>
		  <br><span class="joined"><?php bp_group_member_joined_since(); ?></span>
		  <br><span class="activity"><?php bp_last_activity( bp_get_group_member_id() ); ?></span>

      <?php if ( bp_is_active( 'friends' ) ) : ?>
  			<div class="action">
  				<?php bp_add_friend_button( bp_get_group_member_id(), bp_get_group_member_is_friend() ); ?>
  				<?php do_action( 'bp_directory_members_actions' ); ?>
  			</div>
      <?php endif; ?>
		</li>

  <?php endwhile; ?>
	</ul>
	
  <?php if ( bp_group_member_needs_pagination() ) : ?>
  <div class="member-pagination">
	 <p class="pages"><?php bp_group_member_pagination(); ?></p>
	</div>
	<?php endif; ?>
		
	<?php do_action( 'bp_after_group_members_content' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'This group has no members.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>
