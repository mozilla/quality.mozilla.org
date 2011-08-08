    </section><!-- /#content -->

<?php do_action( 'bp_after_container' ); ?>
<?php do_action( 'bp_before_footer' ); ?>
    
  <footer id="site-info" role="contentinfo" class="section">
    <p id="copyright">Copyright &copy; <?php echo date('Y'); ?> Mozilla. All rights reserved. | <a href="http://www.mozilla.com/privacy-policy.html" rel="external">Privacy Policy</a> | <a href="http://www.mozilla.com/about/legal.html" rel="external">Legal Notices</a></p>
    <p>Portions of QMO content are &copy; 1998&ndash;<?php echo date('Y');?> by individual mozilla.org contributors.</p>
    <p>Some content available under a <a href="http://www.mozilla.org/foundation/licensing/website-content.html" rel="external license">Creative Commons license</a></p>
  <?php if (get_page_by_path('about')) : ?>
    <p><a href="<?php echo get_permalink(get_page_by_path('about')->ID); ?>"><?php echo get_page_by_path('about')->post_title; ?></a></p>
  <?php endif; ?>
  </footer>

<?php do_action( 'bp_footer' ); ?>
<?php do_action( 'bp_after_footer' ); ?>
<?php wp_footer(); ?>

<!-- START OF SmartSource Data Collector TAG -->
<!-- Copyright (c) 1996-2011 Webtrends Inc.  All rights reserved. -->
<!-- Version: 9.4.0 -->
<!-- Tag Builder Version: 3.2  -->
<!-- Created: 5/27/2011 3:47:00 PM -->
<script src="<?php bloginfo('stylesheet_directory'); ?>/js/webtrends.js" type="text/javascript"></script>
<script type="text/javascript">
//<![CDATA[
var _tag=new WebTrends();
_tag.dcsGetId();
//]]>
</script>
<script type="text/javascript">
//<![CDATA[
_tag.dcsCustom=function(){
// Add custom parameters here.
//_tag.DCSext.param_name=param_value;
}
_tag.dcsCollect();
//]]>
</script>
<noscript>
<div><img alt="" id="DCSIMG" width="1" height="1" src="//statse.webtrendslive.com/dcsq1jg2dvz5bdyykhwhwt8pm_3c6l/njs.gif?dcsuri=/nojavascript&amp;WT.js=No&amp;WT.tv=9.4.0&amp;dcssip=www.quality.mozilla.org"/></div>
</noscript>
<!-- END OF SmartSource Data Collector TAG -->

</body>
</html>
