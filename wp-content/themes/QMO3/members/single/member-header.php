<?php do_action( 'bp_before_member_header' ); ?>
<div id="member-head" class="hcard">
  <div class="member-info">
    <h1 class="fn"><a class="url" href="<?php bp_user_link(); ?>"><?php bp_displayed_user_fullname(); ?> <?php bp_displayed_user_avatar( 'width=100&height=100' ); ?></a></h1>
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
</div>

<?php do_action( 'bp_after_member_header' ); ?>
<?php do_action( 'template_notices' ); ?>
