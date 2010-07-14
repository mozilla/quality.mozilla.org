<?php
  global $spEvents;
  $spEvents->loadDomainStylesScripts(); 

  get_header();
?>
<div id="content-main" class="full" role="main">
  <div id="events-calendar-header">
    <h1 class="section-title"><?php _e('Events Calendar', $spEvents->pluginDomain) ?></h1>
    <div class="calendar-control nav-paging">
      <?php if (events_get_previous_month_link()) : ?>
        <span class="prev">
          <a href="<?php echo events_get_previous_month_link(); ?>"><?php echo events_get_previous_month_text(); ?></a>
        </span>
      <?php endif; ?>

      <div class="cal-jump">
        <?php get_jump_to_date_calendar( "tec-" ); ?>
      </div>

      <?php if (events_get_next_month_link()) : ?>
        <span class="next">
          <a href="<?php echo events_get_next_month_link(); ?>"><?php echo events_get_next_month_text(); ?></a>
        </span>
      <?php endif; ?>
    </div>
    <p class="calendar-switch">
      <a class="ical" href="<?php bloginfo('url'); ?>/?ical">Download iCal</a>
      <a class="button" href="<?php echo events_get_listview_link(); ?>"><?php _e('Event List', $spEvents->pluginDomain)?></a>
      <a class="button on" href="<?php echo events_get_gridview_link(); ?>"><?php _e('Calendar', $spEvents->pluginDomain)?></a>
    </p>
  </div><!--#events-calendar-header-->
  <?php event_grid_view(); // See the plugins/the-events-calendar/views/table.php template for customization ?>
</div>
<?php get_footer(); ?>
