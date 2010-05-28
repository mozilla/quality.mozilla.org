<?php
  global $spEvents;
  $spEvents->loadDomainStylesScripts();

  // Fetch the formats
  $date_format = get_option("date_format");
  $time_format = get_option("time_format");

  get_header();
?>
<div id="content-main" class="hfeed vcalendar" role="main">
  <?php the_post(); global $post; ?>
      <div id="post-<?php the_ID() ?>" <?php post_class('vevent') ?> role="article">
        <h1 class="entry-title summary"><?php the_title() ?></h1>
        <div class="entry-meta">
          <p class="event-flag">Event</p>
          <p class="vcard">Posted by <a class="fn url author" title="See all <?php the_author_posts() ?> posts by <?php the_author(); ?>" href="<?php echo get_author_posts_url($authordata->ID, $authordata->user_nicename); ?>"><?php the_author(); ?></a> 
          on <?php the_time(get_option('date_format')); ?> at <abbr class="updated" title="<?php the_time('Y-m-d\TH:i:sP'); ?>"><?php the_time(); ?></abbr>.
          <?php if ( current_user_can( 'edit_page', $post->ID ) ) : ?><span class="edit"><?php edit_post_link('Edit', '', ''); ?></span><?php endif; ?>
          </p>
        </div>

        <?php // If the current datetime is greater than event datetime, the event is in the past
          if ( date('c') > the_event_end_date($post->ID, false, 'c') ) : ?>
          <p class="passed description"><strong><?php _e('This event has passed.', 'qmo') ?></strong></p>
        <?php endif; ?>

        <div id="event-meta">
          <dl class="column">
        <?php // All day, single day
          if ( get_post_meta( $post->ID, '_EventAllDay' ) && 
               (the_event_start_date($post->ID) == the_event_end_date($post->ID)) ) : ?>
            <dt><?php _e('Date:','qmo'); ?></dt>
            <dd><abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr></dd>
        <?php // All day, multiple days
          elseif ( get_post_meta( $post->ID, '_EventAllDay' ) && 
                   (the_event_start_date($post->ID) != the_event_end_date($post->ID)) ) : ?>
            <dt><?php _e('Start:','qmo'); ?></dt>
            <dd><abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ); ?></abbr></dd>
            <dt><?php _e('End:','qmo'); ?></dt>
            <dd><abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d' ); ?>"><?php echo the_event_end_date( $post->ID, false, $date_format ); ?></abbr></dd>
        <?php // Standard event shows start and end, date and time
          else : ?>
            <dt><?php _e('Start:','qmo'); ?></dt>
            <dd><abbr class="dtstart" title="<?php echo the_event_start_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_start_date( $post->ID, false, $date_format ).'<br>'.the_event_start_date( $post->ID, false, $time_format ); ?></abbr></dd>
            <dt><?php _e('End:','qmo'); ?></dt>
            <dd><abbr class="dtend" title="<?php echo the_event_end_date( $post->ID, false, 'Y-m-d\TH:i:s' ); ?>"><?php echo the_event_end_date( $post->ID, false, $date_format ).'<br>'.the_event_end_date( $post->ID, false, $time_format ); ?></abbr></dd>
        <?php endif; ?>
        <?php if ( the_event_cost() ) : ?>
            <dt><?php _e('Cost:', $spEvents->pluginDomain) ?></dt>
            <dd><?php echo the_event_cost(); ?></dd>
        <?php endif; ?>
          </dl>

          <dl class="column">
          <?php if(the_event_venue()) : ?>
            <dt><?php _e('Venue:', $spEvents->pluginDomain) ?></dt> 
            <dd><?php echo the_event_venue(); ?></dd>
          <?php endif; ?>
          <?php if(the_event_phone()) : ?>
            <dt><?php _e('Phone:', $spEvents->pluginDomain) ?></dt> 
            <dd><?php echo the_event_phone(); ?></dd>
          <?php endif; ?>
          <?php if( tec_address_exists( $post->ID ) ) : ?>
            <dt><?php _e('Address:', $spEvents->pluginDomain) ?><br />
              <?php if( get_post_meta( $post->ID, '_EventShowMapLink', true ) == 'true' ) : ?>
                <a class="gmap" href="<?php event_google_map_link() ?>" title="<?php _e('Click to view a Google Map', $spEvents->pluginDomain); ?>" target="_blank"><?php _e('Google Map', $spEvents->pluginDomain ); ?></a>
              <?php endif; ?>
            </dt>
            <dd><?php tec_event_address( $post->ID ); ?></dd>
          <?php endif; ?>
          </dl>
        </div>

        <?php if ( the_event_city($post->ID) ) : event_google_map_embed(); endif; ?>

        <div class="entry-content description">
          <?php the_content() ?>  
          <?php if (function_exists('the_event_ticket_form')) { the_event_ticket_form(); } ?>   
        </div>

      <?php if (get_the_tags()) : ?>
        <?php the_tags('<p class="entry-tags"><strong>'.__('Tags:','qmo').'</strong> ',', ',''); ?>
      <?php endif; ?>
      </div><!-- /post -->

    <?php if(eventsGetOptionValue('showComments','no') == 'yes'){ comments_template(); } ?>

</div>

<div id="content-sub">
  <?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-home') ) : else : endif; ?>
</div>

<?php get_footer(); ?>
