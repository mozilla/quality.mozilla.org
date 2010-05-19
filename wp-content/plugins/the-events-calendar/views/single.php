<?php 
	The_Events_Calendar::loadStylesAndScripts();
	include (TEMPLATEPATH.'/header.php'); ?>
	
	<div id="tec-content" class="tec-event widecolumn">
	<?php the_post(); global $post, $spEvents; ?>
			<div id="post-<?php the_ID() ?>" <?php post_class() ?>>
				<span class="back"><a href="<?php echo events_get_gridview_link(); ?>"><?php _e('&laquo; Back to Events', $spEvents->pluginDomain); ?></a></span>
				<h2 class="entry-title"><?php the_title() ?></h2>
				<?php if (the_event_end_date() > time()  ) { ?><small><?php  _e('This event has passed.', $spEvents->pluginDomain) ?></small> <?php } ?>
				<div id="tec-event-meta">
					<dl class="column">
						<dt><?php _e('Start:', $spEvents->pluginDomain) ?></dt> 
							<dd><?php echo the_event_start_date(); ?></dd>
						<?php if (the_event_start_date() !== the_event_end_date() ) { ?>
							<dt><?php _e('End:', $spEvents->pluginDomain) ?></dt>
							<dd><?php echo the_event_end_date();  ?></dd>						
						<?php } ?>
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
						<?php if(the_event_address()) : ?>
						<dt><?php _e('Address:', $spEvents->pluginDomain) ?><br /><a class="gmap" href="<?php event_google_map_link() ?>" title="<?php _e('Click to view a Google Map', $spEvents->pluginDomain); ?>" target="_blank"><?php _e('Google Map', $spEvents->pluginDomain ); ?></a></dt>
							<dd>
							<?php $address = the_event_address();
							$address .= (the_event_city())?  ', ' . the_event_city() : '';
							$address .= (the_event_region()) ? ', ' . the_event_region() : '';
							$address .= (the_event_country()) ? ', ' . the_event_country() : '';
							$address .= (the_event_zip()) ? ', ' . the_event_zip() : '';

							$address = str_replace(' ,', ',', $address);
							echo $address;

							$googleaddress = str_replace(' ', '+', $address);
							 ?>
							</dd>
						<?php endif; ?>
					</dl>
				</div>
				<?php if(the_event_address()) { event_google_map_embed(); } ?>
				<div class="entry">
					<?php the_content() ?>	
					<?php if (function_exists('the_event_ticket_form')) { the_event_ticket_form(); } ?>		
				</div>
				<?php edit_post_link('Edit', '<span class="edit-link">', '</span>'); ?>
			</div><!-- .post -->

		<?php if(eventsGetOptionValue('showComments','no') == 'yes'){ comments_template();} ?>

	</div><!-- #tec-content -->
	
<?php
	include (TEMPLATEPATH.'/footer.php');