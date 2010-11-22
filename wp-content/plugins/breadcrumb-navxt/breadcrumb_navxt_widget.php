<?php
/*  
	Copyright 2009-2010  John Havlik  (email : mtekkmonkey@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
class bcn_widget extends WP_Widget
{
	//Default constructor
	function __construct()
	{
		$ops = array('classname' => 'widget_breadcrumb_navxt', 'description' => __('Adds a breadcrumb trail to your sidebar'));
		//We're going to be a bit evil here and do things the PHP5 way
		parent::__construct('bcn_widget', 'Breadcrumb NavXT', $ops);
	}
	function widget($args, $instance)
	{
		extract($args);
		//If we are on the front page and don't display on the front, return early
		if($instance['front'] && is_front_page())
		{
			return;
		}
		//Manditory before widget junk
		echo $before_widget;
		if(!empty($instance['title']))
		{
			echo $before_title . $instance['title'] . $after_title;
		}
		//We'll want to switch between the two breadcrumb output types
		if($instance['list'] == true)
		{
			//Display the list output breadcrumb
			echo '<ul class="breadcrumb_trail">';
			bcn_display_list(false, $instance['linked'], $instance['reverse']);
			echo '</ul>';
		}
		else
		{
			//Display the regular output breadcrumb
			bcn_display(false, $instance['linked'], $instance['reverse']);
		}
		//Manditory after widget junk
		echo $after_widget;
	}
	function update($new_instance, $old_instance)
	{
		//Filter out anything that could be invalid
		$old_instance['title'] = strip_tags($new_instance['title']);
		$old_instance['list'] = isset($new_instance['list']);
		$old_instance['linked'] = isset($new_instance['linked']);
		$old_instance['reverse'] = isset($new_instance['reverse']);
		$old_instance['front'] = isset($new_instance['front']);
		return $old_instance;
	}
	function form($instance)
	{
		$instance = wp_parse_args((array) $instance, array('title' => '', 'list' => false, 'linked' => true, 'reverse' => false));?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"> <?php _e('Title:'); ?></label>
			<input class="widefat" type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo esc_attr($instance['title']);?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" name="<?php echo $this->get_field_name('list'); ?>" id="<?php echo $this->get_field_id('list'); ?>" value="true" <?php checked(true, $instance['list']);?> />
			<label for="<?php echo $this->get_field_id('list'); ?>"> <?php _e('Output trail as a list'); ?></label><br />
			<input class="checkbox" type="checkbox" name="<?php echo $this->get_field_name('linked'); ?>" id="<?php echo $this->get_field_id('linked'); ?>" value="true" <?php checked(true, $instance['linked']);?> />
			<label for="<?php echo $this->get_field_id('linked'); ?>"> <?php _e('Link the breadcrumbs'); ?></label><br />
			<input class="checkbox" type="checkbox" name="<?php echo $this->get_field_name('reverse'); ?>" id="<?php echo $this->get_field_id('reverse'); ?>" value="true" <?php checked(true, $instance['reverse']);?> />
			<label for="<?php echo $this->get_field_id('reverse'); ?>"> <?php _e('Reverse the order of the trail'); ?></label><br />
			<input class="checkbox" type="checkbox" name="<?php echo $this->get_field_name('front'); ?>" id="<?php echo $this->get_field_id('front'); ?>" value="true" <?php checked(true, $instance['front']);?> />
			<label for="<?php echo $this->get_field_id('front'); ?>"> <?php _e('Hide the trail on the front page'); ?></label><br />
		</p>
		<?php
	}
}