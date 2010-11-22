    </section><!-- /#content -->
  <footer id="site-info" role="contentinfo" class="section">
    <p id="copyright">Copyright &copy; <?php echo date('Y'); ?> Mozilla. All rights reserved. | <a href="http://www.mozilla.com/privacy-policy.html" rel="external">Privacy Policy</a> | <a href="http://www.mozilla.com/about/legal.html" rel="external">Legal Notices</a></p>
    <p>Portions of QMO content are &copy; 1998&ndash;<?php echo date('Y');?> by individual mozilla.org contributors.</p>
    <p>Some content available under a <a href="http://www.mozilla.org/foundation/licensing/website-content.html" rel="external license">Creative Commons license</a></p>
  <?php if (get_page_by_path('about')) : ?>
    <p><a href="<?php echo get_permalink(get_page_by_path('about')->ID); ?>"><?php echo get_page_by_path('about')->post_title; ?></a></p>
  <?php endif; ?>
  </footer>

<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/superfish.js"></script>
<script type="text/javascript">
// <![CDATA[
  jQuery(document).ready(function(){
    // Animate the social media links
    jQuery("#nav-extra").addClass("js"); // style hook for graceful degradation
    jQuery("#nav-extra a").hover(
      function() { jQuery(this).animate({ marginTop: '-8px' },{ queue:false, duration:150}); }, // stand up fast
      function() { jQuery(this).animate({ marginTop: '0' },{ queue:false, duration:500 }); } // sit down slow
    );
    
    // Set up the superfish menus	
  	jQuery("#wp-admin-bar .main-nav").superfish({
      animation:   {height:'show'}, 
      speed:       100,
      delay:       400,
      autoArrows:  false, 
      dropShadows: false
  	});
  });
// ]]>
</script>

<?php wp_footer(); ?>
</body>
</html>
