<?php 
/* Events widget template */
// let's make time
$start_time   = strtotime(get_post_meta( $post->ID, '_EventStartDate', true )); 
$EventCity    = get_post_meta( $post->ID, '_EventCity', true );
$EventCountry = get_post_meta( $post->ID, '_EventCountry', true );
$EventState   = get_post_meta( $post->ID, '_EventState', true );
$EventProvince  = get_post_meta( $post->ID, '_EventProvince', true );
?>
<li class="vevent <?php echo $alt_text ?>">
  <abbr class="dtstart" title="<?php echo date('Y-m-j', $start_time); ?>">
    <span class="month"><?php echo date('M', $start_time); ?></span>
    <span class="date"><?php echo date('j', $start_time); ?></span>
  </abbr>
  <div class="summary"><a class="url" href="<?php echo get_permalink($post->ID) ?>"><?php echo $post->post_title ?></a></div>
  <div class="location"><?php
    $space = false;
    $output = '';
    if ($city == true && $EventCity != '') {
      $space = true;
      $output = $EventCity . ', ';
    }
    if ($state == true || $province == true){
      if ( $EventCountry == "United States" &&  $EventState != '') {
        $space = true;
        $output .= $EventState;
      } elseif  ( $EventProvince != '' ) {
        $space = true;
        $output .= $EventProvince;
      }
    } else {
      $output = rtrim( $output, ', ' );
    }
    if ( $space ) {
      $output .=  '<br />';
    }
    if ($country == true && $EventCountry != '') {
      $output .= $EventCountry; 
    }
    echo $output;
  ?>
  </div>
</li>
<?php $alt_text = ( empty( $alt_text ) ) ? 'alt' : '';
