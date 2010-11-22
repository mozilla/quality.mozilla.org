<?php do_action( 'bp_before_group_header' ); ?>
<div id="team-head">
  <div class="intro">
  	<h1><?php bp_group_name(); ?> <?php bp_group_avatar( 'width=100&height=100' ); ?></h1>
    <div class="team-desc">
      <?php bp_group_description(); ?>
    </div>
  </div>
  
  <div class="data">
    <div class="team-meta">
      <?php do_action( 'bp_before_group_header_meta' ); ?>
      <p class="mem-count"><?php bp_group_member_count(); ?></p> 
      <p class="activity"><?php printf( __( 'Last activity %s ago', 'buddypress' ), bp_get_group_last_active() ); ?></p>
      <?php do_action( 'bp_group_header_meta' ); ?>
      <?php bp_group_join_button(); ?>
    </div>

    <div id="team-leaders">
    <?php if ( bp_group_is_visible() ) : ?>
      <h3><?php _e( 'Team Admins', 'qmo' ); ?></h3>
      <?php qmo_group_list_admins();
        do_action( 'bp_after_group_menu_admins' ); ?>  
    		
      <?php if ( bp_group_has_moderators() ) :
        do_action( 'bp_before_group_menu_mods' ); ?>
      <h3><?php _e( 'Team Moderators' , 'qmo' ); ?></h3>
      <?php qmo_group_list_mods();
        do_action( 'bp_after_group_menu_mods' ); ?>
      <?php endif; ?>
    <?php endif; ?>
    </div>
  </div>
</div>
<?php do_action( 'bp_after_group_header' ); ?>

<?php do_action( 'template_notices' ); ?>
