<?php get_header() ?>

<section id="content-main" role="main">

			<?php do_action( 'bp_before_member_home_content' ) ?>

			<div id="item-header">
				<?php dpa_load_template( array( 'members/single/member-header.php' ) ) ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>

						<?php do_action( 'bp_member_options_nav' ) ?>
					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">
				<?php do_action( 'bp_before_member_body' ) ?>

				<?php if ( dpa_is_member_my_achievements_page() ) : ?>
					<?php dpa_load_template( array( 'members/single/achievements/unlocked.php' ) ) ?>
				<?php endif; ?>

				<?php do_action( 'bp_after_member_body' ) ?>

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_home_content' ) ?>

	</section><!-- #content -->

<section id="content-sub" role="complementary">
<?php include (TEMPLATEPATH . '/user-state.php'); ?>
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-member') ) : else : endif; ?>
</section>

<?php get_footer() ?>