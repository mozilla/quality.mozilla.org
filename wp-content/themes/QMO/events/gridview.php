<?php
	global $spEvents;
	if (function_exists('loadDomainStylesScripts')) : $spEvents->loadDomainStylesScripts();
	else : $spEvents->loadStylesAndScripts();
	endif;
	
	get_header();
?>
<div id="content-main" class="hfeed vcalendar full" role="main">
	<div id="events-calendar-header">
		<h1 class="section-title"><?php _e('Events Calendar', $spEvents->pluginDomain) ?></h1>
    <div class="calendar-control">
		<?php get_jump_to_date_calendar( "tec-" ); ?>
		</div>
		<p class="calendar-switch"> 
			<a class="button" href="<?php echo events_get_listview_link(); ?>"><?php _e('Event List', $spEvents->pluginDomain)?></a>
			<a class="button on" href="<?php echo events_get_gridview_link(); ?>"><?php _e('Calendar', $spEvents->pluginDomain)?></a>
		</p>
	</div><!--#events-calendar-header-->

	<?php event_grid_view(); // See the plugins/the-events-calendar/views/table.php template for customization ?>	
</div>

<?php get_footer(); ?>