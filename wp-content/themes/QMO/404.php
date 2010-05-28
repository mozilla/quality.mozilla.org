<?php get_header(); ?>
<div id="content-main" class="error" role="main">
  <h1 class="section-title">Sorry, we couldn&#8217;t find that</h1>
  <div class="entry-content">
    <p><img src="<?php bloginfo('stylesheet_directory'); ?>/img/qmo-bug.png" width="200" height="200" alt="" class="alignright"> 
    We looked everywhere, but we couldn&#8217;t find the page or file you were looking for. A few possible explanations:</p>
    <ul>
      <li>You may have followed an out-dated link or bookmark.</li>
      <li>If you entered the address by hand, you might have mistyped it.</li>
      <li>Maybe you found a bug. Good work!</li>
    </ul>
  </div>
</div><?php /* end #content-main */ ?>

<div id="content-sub" class="vcalendar" role="complementary">
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-search') ) : else : endif; ?>
</div>

<?php get_footer(); ?>
