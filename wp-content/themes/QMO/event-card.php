<?php
// Fetch the formats
$date_format = get_option("date_format");
$time_format = get_option("time_format");
?>
<div class="event-date">
  <h3>When</h3>
  <?php // All day, single day
    if ( get_post_meta( $post->ID, '_EventAllDay' ) && 
         (the_event_start_date($post->ID) == the_event_end_date($post->ID)) ) : ?>
      <p><abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr></p>
  <?php // All day, multiple days
    elseif ( get_post_meta( $post->ID, '_EventAllDay' ) && 
             (the_event_start_date($post->ID) != the_event_end_date($post->ID)) ) : ?>
      <p>
      <span class="start"><em>Start:</em> <abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr></span>
      <span class="end"><em>End:</em> <abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_end_date( $post->ID, false, $date_format ); ?></abbr></span>
      </p>
  <?php // Not all day, but the time spans more than one date (e.g., runs past midnight)
    elseif ( !get_post_meta( $post->ID, '_EventAllDay' ) && 
             (the_event_start_date($post->ID, false, $date_format) < the_event_end_date($post->ID, false, $date_format)) ) : ?>
      <p>
      <span class="start"><em>Start:</em> <abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format )."<br>".the_event_start_date( $post->ID, false, $time_format ); ?></abbr></span>
      <span class="end"><em>End:</em> <abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_end_date( $post->ID, false, $date_format )."<br>".the_event_end_date( $post->ID, false, $time_format ); ?></abbr></span>
      </p>
  <?php // Just a normal event.
    else : ?>
      <p><abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr>
      <span class="start"><em>Start:</em> <?php echo the_event_start_date( $post->ID, false, $time_format ); ?></span>
      <span class="end"><em>End:</em> <abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_end_date( $post->ID, false, $time_format ); ?></abbr></span>
      </p>
  <?php endif; ?>

  <?php // If there's more info to show, link to the event page
    if ( the_event_venue($post->ID) || the_event_cost($post->ID) || the_event_address($post->ID) || the_event_phone($post->ID) ) : ?>
    <p><a class="more-link" href="<?php echo get_permalink($post->ID) ?>">More info</a></p>
  <?php endif; ?>

  <?php // If the current datetime is greater than event datetime, the event is in the past
    if ( date('c') > the_event_end_date($post->ID, false, 'c') ) : ?>
    <p class="passed description"><strong>This event has passed.</strong></p>
  <?php endif; ?>
</div>
