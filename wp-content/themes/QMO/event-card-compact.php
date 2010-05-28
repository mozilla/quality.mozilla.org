<div class="event-date compact">
  <p><strong>When:</strong>
  <?php // All day, single day
    if ( get_post_meta( $post->ID, '_EventAllDay' ) && 
         (the_event_start_date($post->ID) == the_event_end_date($post->ID)) ) : ?>
      <abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr>
  <?php // All day, multiple days
    elseif ( get_post_meta( $post->ID, '_EventAllDay' ) && 
             (the_event_start_date($post->ID) != the_event_end_date($post->ID)) ) : ?>
      <abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr>
      &ndash;
      <abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_end_date( $post->ID, false, $date_format ); ?></abbr>
  <?php // Not all day, but the time spans more than one date (e.g., runs past midnight)
    elseif ( !get_post_meta( $post->ID, '_EventAllDay' ) && 
             (the_event_start_date($post->ID, false, $date_format) < the_event_end_date($post->ID, false, $date_format)) ) : ?>
      <abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ).", ".the_event_start_date( $post->ID, false, $time_format ); ?></abbr>
      &ndash;
      <abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_end_date( $post->ID, false, $date_format ).", ".the_event_end_date( $post->ID, false, $time_format ); ?></abbr>
  <?php // Just a normal event.
    else : ?>
      <abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr>, 
      <?php echo the_event_start_date( $post->ID, false, $time_format ); ?>
      &ndash;
      <abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_end_date( $post->ID, false, $time_format ); ?></abbr>
  <?php endif; ?>
  <?php // If there's more info to show, link to the event page
    if ( the_event_venue($post->ID) || the_event_cost($post->ID) || the_event_address($post->ID) || the_event_phone($post->ID) ) : ?>
    <a class="more-link" href="<?php echo get_permalink($post->ID) ?>">More info</a>
  <?php endif; ?>
  </p>
  <?php // If the current datetime is greater than event datetime, the event is in the past
    if ( date('c') > the_event_end_date($post->ID, false, 'c') ) : ?>
    <p class="passed description"><strong>This event has passed.</strong></p>
  <?php endif; ?>
</div>
