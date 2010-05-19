<?php
	global $spEvents;
	$spEvents->loadStylesAndScripts();
	
	include (TEMPLATEPATH.'/header.php'); ?>
	
	<div id="tec-content" class="grid">
		<div id='tec-events-calendar-header' class="clearfix">
			<h2 class="tec-cal-title"><?php _e('Calendar of Events', $spEvents->pluginDomain) ?></h2>

			<?php get_jump_to_date_calendar( "tec-" ); ?>

			<span class='tec-calendar-buttons'> 
				<a class='tec-button-off' href='<?php echo events_get_listview_link(); ?>'><?php _e('Event List', $spEvents->pluginDomain)?></a>
				<a class='tec-button-on' href='<?php echo events_get_gridview_link(); ?>'><?php _e('Calendar', $spEvents->pluginDomain)?></a>
			</span>

		</div><!--#tec-events-calendar-header-->

		<?php event_grid_view( ); // See the plugins/the-events-calendar/views/table.php template for customization ?>	
	</div>

<?php
	include (TEMPLATEPATH.'/footer.php');