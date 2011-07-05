<?php do_action( 'bp_before_member_header' ); ?>
<div id="member-head" class="hcard">
  <div class="member-info">
    <h1 class="fn"><a class="url" href="<?php bp_user_link(); ?>"><?php bp_displayed_user_fullname(); ?> <?php bp_displayed_user_avatar( 'type=full&width=100&height=100' ); ?></a></h1>
    <?php if (bp_get_profile_field_data('field=IRC Nickname') != "") : ?>
    <h2 class="nickname"><?php bp_profile_field_data( 'field=IRC Nickname' ); ?></h2>
    <?php endif; ?>

   <p class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></p>
  </div>

  <?php if ( qmo_show_user_meta() ) : ?>
  <div class="member-meta">
    <?php do_action( 'bp_before_member_header_meta' ); ?>
    <?php do_action( 'bp_profile_header_meta' ); ?>
  </div>
  <?php endif; ?>
  
  <?php global $bp;
    if ( is_super_admin() ) :
  ?>
		<ul class="admin-links">
			<li><a href="<?php echo $bp->displayed_user->domain . $bp->profile->slug ?>/edit/"><?php _e( "Edit Profile", 'qmo' ); ?></a></li>
			<li><a href="<?php echo $bp->displayed_user->domain . $bp->profile->slug ?>/change-avatar/"><?php _e( "Change Avatar", 'qmo' ); ?></a></li>
			<?php if ( !bp_core_is_user_spammer( $bp->displayed_user->id ) ) : ?>
				<li><a href="<?php echo wp_nonce_url( $bp->displayed_user->domain . 'admin/mark-spammer/', 'mark-unmark-spammer' ) ?>" class="confirm"><?php _e( "Mark as Spammer", 'qmo' ) ?></a></li>
			<?php else : ?>
				<li><a href="<?php echo wp_nonce_url( $bp->displayed_user->domain . 'admin/unmark-spammer/', 'mark-unmark-spammer' ) ?>" class="confirm"><?php _e( "Not a Spammer", 'qmo' ) ?></a></li>
			<?php endif; ?>
			<li><a href="<?php echo wp_nonce_url( $bp->displayed_user->domain . 'admin/delete-user/', 'delete-user' ) ?>" class="confirm"><?php printf( __( "Delete %s", 'qmo' ), esc_attr( $bp->displayed_user->fullname ) ) ?></a></li>
		</ul>
  <?php endif; ?>
  
</div>
<?php do_action( 'bp_after_member_header' ); ?>
<?php do_action( 'template_notices' ); ?>
