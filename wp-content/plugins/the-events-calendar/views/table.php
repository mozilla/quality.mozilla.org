<table class="tec-calendar" id="big">
	<thead>
			<tr>
				<?php
				for( $n = $startOfWeek; $n < count($spEvents->daysOfWeek) + $startOfWeek; $n++ ) {
					$dayOfWeek = ( $n >= 7 ) ? $n - 7 : $n;
					echo '<th id="tec-' . strtolower($spEvents->daysOfWeek[$dayOfWeek]) . '" abbr="' . $spEvents->daysOfWeek[$dayOfWeek] . '">' . $spEvents->daysOfWeekShort[$dayOfWeek] . '</th>';
				}
				?>
			</tr>
	</thead>

	<tbody>
		<tr>
		<?php
			// skip last month
			for( $i = 1; $i <= $offset; $i++ ){ 
				echo "<td class='tec-othermonth'></td>";
			}
			// output this month
			for( $day = 1; $day <= $daysInMonth; $day++ ) {
			    if( ($day + $offset - 1) % 7 == 0 && $day != 1) {
			        echo "</tr>\n\t<tr>";
			        $rows++;
			    }
			
				// Var'ng up days, months and years
				$current_day = date_i18n( 'd' );
				$current_month = date_i18n( 'm' );
				$current_year = date_i18n( 'Y' );
				
				if ( $current_month == $month && $current_year == $year) {
					// Past, Present, Future class
					if ($current_day == $day ) {
						$ppf = ' tec-present';
					} elseif ($current_day > $day) {
						$ppf = ' tec-past';
					} elseif ($current_day < $day) {
						$ppf = ' tec-future';
					}
				} elseif ( $current_month > $month && $current_year == $year || $current_year > $year ) {
					$ppf = ' tec-past';
				} elseif ( $current_month < $month && $current_year == $year || $current_year < $year ) {
					$ppf = ' tec-future';
				} else { $ppf = false; }
				
			    echo "<td class='tec-thismonth" . $ppf . "'><div class='daynum'>" . $day . "</div>\n";
				echo display_day( $day, $monthView );
				echo "</td>";
			}
			// skip next month
			while( ($day + $offset) <= $rows * 7)
			{
			    echo "<td class='tec-othermonth'></td>";
			    $day++;
			}
		?>
		</tr>
	</tbody>
</table>
<?php
function display_day( $day, $monthView ) {
	$output = '';
	for( $i = 0; $i < count( $monthView[$day] ); $i++ ) {
		$event 		= $monthView[$day][$i];
		$eventId	= $event->ID.'-'.$day;
		$start		= the_event_start_date( $event->ID );
		$end		= the_event_end_date( $event->ID );
		$cost		= the_event_cost( $event->ID );
		$address	= the_event_address( $event->ID );
		$city		= the_event_city( $event->ID );
		$state		= the_event_state( $event->ID );
		$province	= the_event_province( $event->ID );
		$country	= the_event_country( $event->ID );
		include( dirname( __FILE__ ) . '/gridview-day.php' );
		if( $i < count( $monthView[$day] ) - 1 ) { 
			echo "<hr />";
		}
	}
}
?>