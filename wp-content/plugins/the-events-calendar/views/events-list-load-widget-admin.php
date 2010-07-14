<script type="text/javascript">
jQuery(document).ready(function() {
// show / hide date time boxes
<?php foreach( array('start','end') as $val ) : ?>
	var <?php echo $val; ?>Checkbox = jQuery('#<?php echo $this->get_field_id( $val ); ?>');
	var <?php echo $val; ?>TimeCheckbox = jQuery('#<?php echo $this->get_field_id( $val.'-time' ); ?>');
	jQuery('input[name="savewidget"]').click(function() {
		if( <?php echo $val; ?>TimeCheckbox.checked || <?php echo $val; ?>Checkbox.checked ) <?php echo $val; ?>TimeCheckbox.closest('li').show();
	});
	<?php echo $val; ?>Checkbox.click(function() {
		if(this.checked) jQuery('#<?php echo $this->get_field_id( $val.'-time' ); ?>-li').slideDown(200);
		else jQuery('#<?php echo $this->get_field_id( $val.'-time' ); ?>-li').slideUp(200);
	});
<?php endforeach; ?>
});
</script>
<style type="text/css">
<?php // show / hide date time boxes ?>
<?php if( $instance['start'] != 'on' && $instance['start-time'] != 'on' ) : ?>
	#<?php echo $this->get_field_id( 'start-time' ); ?>-li {display:none;}
<?php endif; ?>
<?php if( $instance['end'] != 'on' && $instance['end-time'] != 'on' ) : ?>
	#<?php echo $this->get_field_id( 'end-time' ); ?>-li {display:none;}
<?php endif; ?>
</style>
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:',$this->pluginDomain);?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e('Show:',$this->pluginDomain);?></label>
	<select id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" class="widefat">
	<?php for ($i=1; $i<=10; $i++)
	{?>
	<option <?php if ( $i == $instance['limit'] ) {echo 'selected="selected"';}?> > <?php echo $i;?> </option>
	<?php } ?>							
	</select>
</p>
	<label for="<?php echo $this->get_field_id( 'no_upcoming_events' ); ?>"><?php _e('Don\'t show the widget if there are no upcoming events:',$this->pluginDomain);?></label>
	<input id="<?php echo $this->get_field_id( 'no_upcoming_events' ); ?>" name="<?php echo $this->get_field_name( 'no_upcoming_events' ); ?>" type="checkbox" <?php checked( $instance['no_upcoming_events'], 1 ); ?> value="1" />
<p>

</p>

<p><?php _e( 'Display:', $this->pluginDomain ); ?><br/>

	<?php $displayoptions = array (
		"start" => __('Start Date', $this->pluginDomain),
		"start-time" => __('Start Time', $this->pluginDomain),
		"end" => __("End Date", $this->pluginDomain),
		"end-time" => __('End Time', $this->pluginDomain),
		"venue" => __("Venue", $this->pluginDomain),
		"address" => __("Address", $this->pluginDomain),
		"city" => __("City", $this->pluginDomain),
		"state" => __("State (US)", $this->pluginDomain),
		"province" => __("Province (Int)", $this->pluginDomain),
		"zip" => __("Postal Code", $this->pluginDomain),
		"country" => __("Country", $this->pluginDomain),
		"phone" => __("Phone", $this->pluginDomain),
		"cost" => __("Price", $this->pluginDomain),
	); ?>
<ul>
	<?php foreach ($displayoptions as $option => $label) : ?>
		<li id="<?php echo $this->get_field_id( $option ); ?>-li">
			<input class="checkbox" type="checkbox" <?php checked( $instance[$option], 'on' ); ?> id="<?php echo $this->get_field_id( $option ); ?>" name="<?php echo $this->get_field_name( $option ); ?>" style="margin-left:5px"/>
			<label for="<?php echo $this->get_field_id( $option ); ?>"><?php echo $label ?></label>
		</li>
	<?php endforeach; ?>
</ul>
</p>