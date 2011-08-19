<?php get_header() ?>

<section id="content-main" role="main">

			<?php do_action( 'bp_before_member_home_content' ) ?>

			<div id="item-header">
				<?php dpa_load_template( array( 'members/single/member-header.php' ) ) ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav(); ?>
						<?php do_action( 'bp_member_options_nav' ); ?>
					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">
		  <?php do_action( 'bp_before_member_body' ); ?>
				<h2>Bugzilla Statistics</h2>

      <?php        
        global $bp;           
        $user = get_userdata($bp->displayed_user->id);
        $user_email = $user->user_email;
        $bz_settings = get_option('bzstats_settings');
        
        try {  $stats = get_bugzilla_stats_for_user($user);  } 
        catch (Exception $e) {  }
      ?>

      <?php if ($stats) : ?>
        <dl class="bz-stats">
          <dt>Total bugs opened</dt>
          <dd><a title="See this buglist" rel="external" href="<?php echo esc_html($bz_settings[bugzilla_url]); ?>/buglist.cgi?emailtype1=exact&emailreporter1=1&email1=<?php echo urlencode($user_email); ?>">
            <?php echo $stats['bug_count']; ?></a></dd>
          
          <dt>Bugs opened in the last 30 days</dt> 
          <dd><a title="See this buglist" rel="external" href="<?php echo esc_html($bz_settings[bugzilla_url]); ?>/buglist.cgi?emailtype1=exact&emailreporter1=1&email1=<?php echo urlencode($user_email); ?>&chfield=[Bug%20creation]&chfieldto=Now&chfieldfrom=<?php echo urlencode(date('Y-m-d', strtotime('-30 days'))); ?>">
            <?php echo $stats['recent_bug_count']; ?></a></dd>
          
          <dt>Total bugs verified</dt>
          <dd><?php echo $stats['bugs_verified_count']; ?></dd>
          
          <dt>Total bugs confirmed</dt>
          <dd><?php echo $stats['bugs_confirmed_count']; ?></dd>
        </dl>
      
      <?php elseif ( $e->getCode() == '10' ) : ?>
        <?php if ( bp_is_my_profile() ) : ?>
        <p id="message" class="error">We couldn't find your e-mail address in Bugzilla. <a href="<?php echo esc_html($bz_settings[bugzilla_url]); ?>/createaccount.cgi">Register now</a> or <a href="<?php echo $bp->loggedin_user->domain . $bp->slug ?>settings/general/">update your e-mail address</a> (you should use the same address for both QMO and Bugzilla).</p>
        <?php else : ?>
        <p id="message" class="error">This member isn't registered with Bugzilla.</p>
        <?php endif; ?>
      
      <?php elseif ( $e->getCode() == '20' ) : ?>
        <p id="message" class="alert">Sorry, we couldn't connect to Bugzilla. Try again later.</p>
        
      <?php else : ?>
        <p id="message" class="alert">Sorry, Bugzilla stats aren't available right now.</p>
      
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