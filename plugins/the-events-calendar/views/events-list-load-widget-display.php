<?php /**
 * This is the template for the output of the events list widget. 
 * All the items are turned on and off through the widget admin.
 * There is currently no default styling, which is highly needed.
 * @return string
 */
$EventStartDate	= get_post_meta( $post->ID, '_EventStartDate', true );
$EventEndDate	= get_post_meta( $post->ID, '_EventEndDate', true );
$EventVenue		= get_post_meta( $post->ID, '_EventVenue', true );
$EventAddress	= get_post_meta( $post->ID, '_EventAddress', true );
$EventCity		= get_post_meta( $post->ID, '_EventCity', true );
$EventCountry	= get_post_meta( $post->ID, '_EventCountry', true );
$EventState		= get_post_meta( $post->ID, '_EventState', true );
$EventZip		= get_post_meta( $post->ID, '_EventZip', true );
$EventProvince	= get_post_meta( $post->ID, '_EventProvince', true );
$EventPhone		= get_post_meta( $post->ID, '_EventPhone', true );
$EventCost		= get_post_meta( $post->ID, '_EventCost', true );
?>

<li class="<?php echo $alt_text ?>">
	<div class="when">
		<?php
			if ( $start == 'on' && $EventStartDate != '' ) {
				$time = $startTime == 'on' ? true : false;
				echo the_event_start_date( $post->ID, $time );
			}
			if ( $end == 'on' && $EventEndDate != '' ) {
				if( $start == 'on' && $EventStartDate != '' ) echo ' to ';
				$time = $endTime == 'on' ? true : false;
				echo the_event_end_date( $post->ID, $time );
			}
		?>
	</div>
	<div class="event">
		<a href="<?php echo get_permalink($post->ID) ?>"><?php echo $post->post_title ?></a>
	</div>
	<div class="loc"><?php
		$output = '';
		if ($venue == 'on' && $EventVenue != '') {
			$output = $EventVenue . ', ';
		}
		if ($address == 'on' && $EventAddress != '') {
			$output .= $EventAddress . ', ';
		}
		if ($city == 'on' && $EventCity != '') {
			$output .= $EventCity . ', ';
		}
		if ($state == 'on' || $province == 'on') {
			if ( $EventCountry == "United States" &&  $EventState != '') {
				$output .= $EventState . ', ';
			} elseif  ( $EventProvince != '' ) {
				$output .= $EventProvince . ', ';
			}
		}
		if ($zip == 'on' && $EventZip != '') {
			$output .= $EventZip;
		} else {
			$output = rtrim( $output, ', ' );
		}
		if ($country == 'on' && $EventCountry != '') {
			$output .= '<br />' . $EventCountry;
		}
		if ($phone == 'on' && $EventPhone != '') {
			$output .= '<br />' . $EventPhone;
		}
		if ($cost == 'on' && $EventCost != '') {
			$output .= '<br />' . $EventCost;
		}
		echo $output;
	?>
	</div>
	<a class="more-link" href="<?php echo get_permalink($post->ID) ?>"><?php _e('More Info', $this->pluginDomain); ?></a>
</li>
<?php $alt_text = ( empty( $alt_text ) ) ? 'alt' : '';