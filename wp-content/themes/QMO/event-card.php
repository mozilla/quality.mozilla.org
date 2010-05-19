<?php 
  the_post(); global $post, $spEvents;
  
  // Fetch the date and time format settings
  $date_format = get_option("date_format");
  $time_format = get_option("time_format"); 
?>
  <div class="event-date">
    <h3>When</h3>
    <?php // All day, single day
      if (get_post_meta( $post->ID, '_EventAllDay' ) && (the_event_start_date($post->ID) == the_event_end_date($post->ID)) ) : ?>
        <p><abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d\TH:i:sP' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr></p>
    <?php // All day, multiple days
      elseif (get_post_meta( $post->ID, '_EventAllDay' ) && (the_event_start_date($post->ID) != the_event_end_date($post->ID)) ) : ?>
        <p>
        <span class="start"><em>Start:</em> <abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d\TH:i:sP' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr></span>
        <span class="end"><em>End:</em> <abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d\TH:i:sP' ); ?>"><?php echo the_event_end_date( $post->ID, false, $date_format ); ?></abbr></span>
        </p>
    <?php // Not all day, but the time spans more than one date (e.g., runs past midnight)
      elseif (!get_post_meta( $post->ID, '_EventAllDay' ) && (the_event_start_date($post->ID, false, $date_format) < the_event_end_date($post->ID, false, $date_format)) ) : ?>
        <p>
        <span class="start"><em>Start:</em> <abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d\TH:i:sP' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format )."<br>".the_event_start_date( $post->ID, false, $time_format.' T' ); ?></abbr></span>
        <span class="end"><em>End:</em> <abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d\TH:i:sP' ); ?>"><?php echo the_event_end_date( $post->ID, false, $date_format )."<br>".the_event_end_date( $post->ID, false, $time_format.' T' ); ?></abbr></span>
        </p>
    <?php // Not all day and time doesn't span two dates. Just a normal event.
      else /* (!get_post_meta( $post->ID, '_EventAllDay' ) && (the_event_start_date($post->ID) != the_event_end_date($post->ID)) ) */ : ?>
        <p><abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d\TH:i:sP' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr>
        <span class="start"><em>Start:</em> <?php echo the_event_start_date( $post->ID, false, $time_format.' T' ); ?></span>
        <span class="end"><em>End:</em> <abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d\TH:i:sP' ); ?>"><?php echo the_event_end_date( $post->ID, false, $time_format.' T' ); ?></abbr></span>
        </p>
    <?php endif; ?>
    
    <?php // If there's more info to show, link to the event page
      if ( the_event_venue($post->ID) || the_event_cost($post->ID) || the_event_address($post->ID) ) : ?>
      <p><a class="more-link" href="<?php echo get_permalink($post->ID) ?>">More info</a></p>
    <?php endif; ?>
    
    <?php // If the current datetime is greater than event datetime, the event is in the past
      if ( date('c') > the_event_end_date($post->ID, false, 'c') ) : ?>
      <p class="passed"><strong>This event has passed.</strong></p>
    <?php endif; ?>
  </div><!-- /.event-date -->
  
  
        <?php if (the_event_venue() || the_event_city()) : ?>
        <div class="event-locale vcard">
          <h4>Where:</h4>
          <p class="adr">
          <?php $venue = the_event_venue();
  	        if ( !empty( $venue ) ) : ?>
  	        <span class="fn"><?php echo $venue; ?></span> 
  	      <?php endif; ?>
          <?php if (the_event_city()) :
            if (function_exists('tec_event_address')) : tec_event_address( $post->ID );
  					else :
  					the_event_address($post->ID);
  					$address = the_event_address();
  					$address .= (the_event_city())?  ', <span class="locality">'.the_event_city().'</span>' : '';
  					$address .= (the_event_region()) ? ', <span class="region">'.the_event_region().'</span>' : '';
  					$address .= (the_event_country()) ? ', <span class="country-name">'.the_event_country().'</span>' : '';
  					$address .= (the_event_zip()) ? ', <span class="postal-code">'.the_event_zip().'</span>' : '';
  					$address = str_replace(' ,', ',', $address);
  					
  					echo $address;
  
  					$googleaddress = str_replace(' ', '+', $address);
  					
  					endif; ?>
  					<a class="gmap" href="<?php event_google_map_link(); ?>" title="Click to view a Google Map" target="_blank"><?php _e('Google Map', $spEvents->pluginDomain ); ?></a>
  			<?php	endif; // if city ?>
  				<?php $phone = the_event_phone();
  	      if ( !empty( $phone ) ) : ?>
  				  <span class="tel"><abbr class="type" title="Telephone">Tel:</abbr> <?php echo $phone; ?></span>
  				<?php endif; ?>
  				</p>
  				<?php $cost = the_event_cost();
  	        if ( !empty( $cost ) ) : ?>
  	      <p>Cost: <?php echo $cost; ?></p>
  	      <?php endif; ?>
        </div>
      <?php endif; ?>