<?php global $post; $post = $event; ?>
<div id='event_<?php echo $eventId; ?>' class="tec-event 
<?php
foreach((get_the_category()) as $category) { 
    echo 'cat_' . $category->cat_name . ' '; 
} 
?>
">
	<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>

	<div id='tooltip_<?php echo $eventId; ?>' class="tec-tooltip" style="display:none;">
		<h5 class="tec-event-title"><?php _e($event->post_title);?></h5>
		<div class="tec-event-body">
			<?php if ( !the_event_all_day($event->ID) ) : ?>
			<div class="tec-event-date">
				<?php if ( !empty( $start ) )	echo $start; ?>
				<?php if ( !empty( $end )  && $start !== $end )		echo " – " . $end . '<br />'; ?>
			</div>
			<?php endif; ?>
			<?php echo The_Events_Calendar::truncate($event->post_content, 30); ?>

		</div>
		<span class="tec-arrow"></span>
	</div>
	
</div>