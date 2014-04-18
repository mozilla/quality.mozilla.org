    </div><!-- /#content -->

  <footer id="site-info" role="contentinfo" class="section">
    <p id="copyright">Copyright &copy; <?php echo date('Y'); ?> Mozilla. All rights reserved. | <a href="http://www.mozilla.org/privacy/websites/" rel="external">Privacy Policy</a> | <a href="http://www.mozilla.org/about/legal.html" rel="external">Legal Notices</a></p>
    <p>Portions of QMO content are &copy; 1998&ndash;<?php echo date('Y');?> by individual mozilla.org contributors.</p>
    <p>Some content available under a <a href="http://www.mozilla.org/foundation/licensing/website-content.html" rel="external license">Creative Commons license</a></p>
    <p><a href="https://github.com/mozilla/quality.mozilla.org" rel="external">Our code is on Github</a></p>
  <?php if (get_page_by_path('about')) : ?>
    <p><a href="<?php echo get_permalink(get_page_by_path('about')->ID); ?>"><?php echo get_page_by_path('about')->post_title; ?></a></p>
  <?php endif; ?>
  </footer>

<?php wp_footer(); ?>
</div>
</body>
</html>
