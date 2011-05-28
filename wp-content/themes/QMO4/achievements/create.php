<?php get_header(); ?>

<section id="content-main" role="main">

		<form class="achievement-edit-form standard-form" method="post" action="<?php dpa_achievements_permalink() ?>/<?php echo DPA_SLUG_CREATE ?>">

			<?php do_action( 'dpa_before_create_achievement' ) ?>

			<h1 class="section-title"><?php _e( 'Create Achievement', 'dpa' ) ?></h1>
			<p><a href="<?php dpa_achievements_permalink() ?>"><?php _e( 'Achievements Directory', 'dpa' ) ?></a></p>

    <?php if ( !dpa_is_create_achievement_page() ) : ?>
			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="achievement-single">
					<ul>
				    <?php bp_get_options_nav() ?>
						<?php do_action( 'achievement_options_nav' ) ?>
					</ul>
				</div>
			</div>
		<?php endif; ?>

			<div class="item-body" id="achievements-create-body">
				<?php do_action( 'dpa_before_create_achievement_body' ) ?>

				<?php if ( bp_is_active( 'groups' ) || is_multisite() && bp_is_active( 'blogs' ) ) : ?>
					<noscript><p><?php _e( "Some of the Action options below may not be relevant to the type or event of the Achievement.", 'dpa' ) ?></p></noscript>
				<?php endif; ?>
				<p><?php _e( "After you create the Achievement, you'll be able to choose a picture for it.", 'dpa' ) ?></p>

				<?php do_action( 'template_notices' ) ?>

				<?php dpa_load_template( array( 'achievements/_addedit.php' ) ) ?>

				<?php wp_nonce_field( 'achievement-create' ) ?>

				<?php do_action( 'dpa_after_create_achievement_body' ); ?>

			</div><!-- .item-body -->

			<?php do_action( 'dpa_after_create_achievement' ) ?>

		</form>

</section><!-- #content -->

<section id="content-sub" role="complementary">
<?php include (TEMPLATEPATH . '/user-state.php'); ?>
</section>

<?php get_footer(); ?>