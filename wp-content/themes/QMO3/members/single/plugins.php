<?php get_header(); ?>

<section id="content-main" role="main">

<?php do_action( 'bp_before_member_plugin_template' ) ?>

  <div id="item-header">
    <?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
  </div>

  <div id="item-nav">
    <div class="item-list-tabs no-ajax" id="object-nav">
      <ul>
        <?php bp_get_displayed_user_nav(); ?>
        <?php do_action( 'bp_members_directory_member_types' ); ?>
      </ul>
    </div>
  </div>

  <div id="item-body">
    <div class="item-list-tabs no-ajax" id="subnav">
      <ul>
        <?php bp_get_options_nav(); ?>
      </ul>
    </div>

    <?php do_action( 'bp_template_content' ); ?>
    <?php do_action( 'bp_directory_members_content' ); ?>

  </div><!-- #item-body -->

<?php do_action( 'bp_after_member_plugin_template' ); ?>

</section>

<section id="content-sub" role="complementary">
<?php include (TEMPLATEPATH . '/user-state.php'); ?>
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-member') ) : else : endif; ?>
</section>
<?php get_footer(); ?>
