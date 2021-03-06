<?php /* This template is only used on multisite installations */ ?>

<?php get_header(); ?>

<section id="content-main" role="main">

  <?php do_action( 'bp_before_activation_page' ); ?>

  <div class="page" id="activate-page">

    <?php if ( bp_account_was_activated() ) : ?>
      <h1 class="page-title"><?php _e( 'Account Activated', 'buddypress' ) ?></h1>

      <?php do_action( 'bp_before_activate_content' ); ?>
      <?php if ( isset( $_GET['e'] ) ) : ?>
        <p><?php _e( 'Your account was activated successfully! Your account details have been sent to you in a separate email.', 'buddypress' ) ?></p>
      <?php else : ?>
        <p><?php _e( 'Your account was activated successfully! You can now log in with the username and password you provided when you signed up.', 'buddypress' ) ?></p>
      <?php endif; ?>

    <?php else : ?>

      <h1 class="page-title"><?php _e( 'Activate your Account', 'buddypress' ); ?></h1>
      <?php do_action( 'bp_before_activate_content' ); ?>
      <p><?php _e( 'Please provide a valid activation key.', 'buddypress' ); ?></p>
      <form action="" method="get" class="standard-form" id="activation-form">
        <p>
         <label for="key"><?php _e( 'Activation Key:', 'buddypress' ) ?></label>
         <input type="text" name="key" id="key" value="" />
        </p>
        <p class="submit"><button type="submit" name="submit"><?php _e( 'Activate', 'buddypress' ); ?></button></p>
      </form>

    <?php endif; ?>

    <?php do_action( 'bp_after_activate_content' ); ?>

  </div><!-- .page -->

  <?php do_action( 'bp_after_activation_page' ); ?>

</section>

<section id="content-sub" role="complementary">
<?php include (TEMPLATEPATH . '/user-state.php'); ?>
</section>

<?php get_footer(); ?>
