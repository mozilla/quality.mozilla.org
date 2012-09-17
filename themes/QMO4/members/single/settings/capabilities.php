<?php get_header( 'buddypress' ); ?>

<section id="content-main" role="main">

			<?php do_action( 'bp_before_member_settings_template' ); ?>

			<div id="item-header">

				<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>

						<?php bp_get_displayed_user_nav(); ?>

						<?php do_action( 'bp_member_options_nav' ); ?>

					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body" role="main">

				<?php do_action( 'bp_before_member_body' ); ?>

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>

						<?php bp_get_options_nav(); ?>

						<?php do_action( 'bp_member_plugin_options_nav' ); ?>

					</ul>
				</div><!-- .item-list-tabs -->

				<h3><?php _e( 'Capabilities', 'buddypress' ); ?></h3>

				<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/capabilities/'; ?>" name="account-capabilities-form" id="account-capabilities-form" class="standard-form" method="post">

					<?php do_action( 'bp_members_capabilities_account_before_submit' ); ?>

					<p><label for="user-spammer">
						<input type="checkbox" name="user-spammer" id="user-spammer" value="1" <?php checked( bp_is_user_spammer( bp_displayed_user_id() ) ); ?> />
						 <?php _e( 'This user is a spammer.', 'buddypress' ); ?>
					</label></p>

					<p class="submit">
						<button type="submit" id="capabilities-submit" name="capabilities-submit"><?php _e( 'Save', 'buddypress' ); ?></button>
					</p>

					<?php do_action( 'bp_members_capabilities_account_after_submit' ); ?>

					<?php wp_nonce_field( 'capabilities' ); ?>

				</form>

				<?php do_action( 'bp_after_member_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_settings_template' ); ?>

</section>

<section id="content-sub" role="complementary">
<?php include (TEMPLATEPATH . '/user-state.php'); ?>
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-member') ) : else : endif; ?>
</section>
<?php get_footer(); ?>
