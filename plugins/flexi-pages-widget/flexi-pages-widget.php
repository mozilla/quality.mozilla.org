<?php
/*
Plugin Name: Flexi Pages Widget
Plugin URI: http://srinig.com/wordpress/plugins/flexi-pages/
Description: A highly configurable WordPress sidebar widget to list pages and sub-pages. User friendly widget control comes with various options. 
Version: 1.6.10
Author: Srini G
Author URI: http://srinig.com/wordpress
License: GPL2
*/

/*  Copyright 2007-2011 Srini G (email : srinig.com@gmail.com)

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



function flexipages_init()
{

	if(function_exists('load_plugin_textdomain'))
		load_plugin_textdomain('flexipages', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


	function flexipages_options_default()
	{
		return array(
		'title' => __('Pages', 'flexipages'), 
		'sort_column' => 'menu_order', 
		'sort_order' => 'ASC', 
		'exclude' => '',
		'include' => '',
		'exinclude' => 'exclude', 
		'hierarchy' => 'on', 
		'depth' => 0, 
		'show_subpages_check' => 'on', 
		'show_subpages' => -2, 
		'show_home_check' => 'on',
		'show_home' => __('Home', 'flexipages'), 
		'show_date' => 'off',
		'date_format' => '',
		'dropdown' => 'off'		);
	}
	
	function flexipages_get_currpage_hierarchy()
	{
		if(is_home() && !is_front_page()) {
			if($curr_page_id = get_option('page_for_posts'))
				$curr_page = &get_post($curr_page_id);
		}
		else if( is_page() ) {
			global $wp_query;
			if($curr_page_id = $wp_query->get_queried_object_id())
				$curr_page = &get_post($curr_page_id);
		}
		else
			return array();


		// get parents, grandparents of the current page
		$hierarchy[] = $curr_page->ID;
	
		while($curr_page->post_parent) {
			$curr_page = &get_post($curr_page->post_parent);
			$hierarchy[] = $curr_page->ID;
		}
		return $hierarchy;
	}
	
	function flexipages_list($page_array, $level = 0)
	{
		if(!$page_array)
			return;
		
		$pagelist = "";
		
		foreach($page_array as $page) {
			
			$date = "";			
			if(isset($page['date']) && $page['date']) $date = " ".$page['date'];
			
			$pagelist .= str_repeat("\t", $level+1).'<li class="'.$page['class'].'"><a href="'.$page['link'].'" title="'.$page['title'].'">'.$page['title'].'</a>'.$date;
			if($page['children'])
				$pagelist .= flexipages_list($page['children'], $level+1);
			$pagelist.= "</li>\n";
		}
		if($pagelist)
			$pagelist = str_repeat("\t", $level)."<ul>\n{$pagelist}".str_repeat("\t", $level)."</ul>";
		return $pagelist;
	}
	
	function flexipages_dropdown($page_array, $level = 0)
	{
		if(!$page_array)
			return;

		$page_dropdown = "";
		$depth = 0;
		
		foreach($page_array as $page) {
			if($page['date']) $date = " ".$page['date'];
			if(is_page($page['ID'])) $selected = ' selected="selected"';
			else $selected = '';
			$page_dropdown .= str_repeat("\t", $depth+1).'<option class="level-'.$level.'" value="'.$page['ID'].'"'.$selected.'>'.str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $level).$page['title'].$date.'</option>'."\n";
			if($page['children'])
				$page_dropdown .= flexipages_dropdown($page['children'], $level+1);
		}
		return $page_dropdown;
	}
	
	function flexipages_get_pages($args = '', $level = 1)
	{
//		echo $args;
		$key_value = explode('&', $args);
		$options = array();
		foreach($key_value as $value) {
			$x = explode('=', $value);
			$options[$x[0]] = $x[1]; // $options['key'] = 'value';
		}
		
		extract($options);
		
		if($show_home && $show_home != 'off') {
			$class = "home_page";
			$class .= is_home()?" current_page_item":"";			
			$page_array[] = array(
				'ID' => 'home', 
				'title' => $show_home, 
				'link' => get_bloginfo('url'), 
				'children' => array(),
				'class' => $class
			);
		}	
		else
			$page_array = array();
			
		if($show_subpages == -2) $show_subpages = 2;
		if($show_subpages == -3) $show_subpages = 3;

			
		if(isset($hierarchy) && ($hierarchy == '0' || $hierarchy == 'off'))
			$depth = -1;

		$parent = ($depth == -1)?"-1":$child_of;		
		
		$pages = get_pages("child_of={$child_of}&parent={$parent}&exclude={$exclude}&include={$include}&sort_column={$sort_column}&sort_order={$sort_order}");
		
//		echo "<pre>";print_r($pages);echo "</pre>";

		$currpage_hierarchy = flexipages_get_currpage_hierarchy();
//		echo "<pre>"; print_r($currpage_hierarchy); echo "</pre>";
		
		
		if($show_date && !$date_format)
			$date_format = get_option('date_format');
		
		if($pages) {
			foreach($pages as $page) {
				if($show_subpages == 3 && !in_array($page->ID, $currpage_hierarchy) && $page->post_parent != $currpage_hierarchy[0] && $page->post_parent != $currpage_hierarchy[1] && $page->post_parent != 0)
					continue;
				
					
				$children = array();

				if( !($depth == -1 || $depth == $level)  &&
					!($show_subpages == 2 && !in_array($page->ID, $currpage_hierarchy)) &&
					!$include)
					$children = flexipages_get_pages("child_of={$page->ID}&parent={$page->ID}&sort_column={$sort_column}&sort_order={$sort_order}&exclude={$exclude}&include={$include}&show_subpages={$show_subpages}&depth={$depth}&show_date={$show_date}&date_format={$date_format}", $level+1);
				
				if($show_date) {
					$x = explode(" ", $page->post_date);
					$y = explode("-", $x[0]);
					$date = date($date_format, mktime(0, 0, 0, $y[1], $y[2], $y[0]));
				}
				$class = "page_item page-item-".$page->ID;
				if(is_page($page->ID))
					$class .= " current_page_item";
				else if(isset($currpage_hierarchy[1]) && $page->ID == $currpage_hierarchy[1])
					$class .= " current_page_ancestor current_page_parent";
				else if(in_array($page->ID, $currpage_hierarchy))
					$class .= " current_page_ancestor";
					
				$page_array[] = array (
					'ID' => $page->ID,
					'title' => $page->post_title,
					'link' => get_page_link($page->ID),
					'date' => $date,
					'children' => $children,
					'class' => $class
				);
			}
		}
		
		
		return $page_array;
		
	}
	
	function flexipages_pageids()
	{	
		global $wpdb;
		$page_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish'" );
		return $page_ids;
	}
	
	function flexipages($args='', $level = 0)
	{
		
//		echo $args;
		
		$key_value = explode('&', $args);
		$options = array();
		foreach($key_value as $value) {
			$x = explode('=', $value);
			$options[$x[0]] = $x[1]; // $options['key'] = 'value';
		}
		
		extract($options);
		
		if(!isset($child_of) || !is_numeric($child_of))
			$child_of = 0;
		
		
		if(!$sort_column)
			$sort_column = 'post_title';
		
		if($show_subpages == 0)
			$depth = 1;
			
		if($include && ($hierarchy == '1' || $hierarchy == 'on')) {
			$inc_array = explode(',', $include);
			if($exclude) $exc_array = explode(',', $exclude); else $exc_array = array();
			$page_ids = flexipages_pageids();
			foreach($page_ids as $page_id) {
				if(!in_array($page_id, $inc_array) && !in_array($page_id, $exc_array))
					$exc_array[] = $page_id;
			}
			$exclude = implode(',', $exc_array);
			$include = '';		
		}
		
		$page_array = flexipages_get_pages("sort_column={$sort_column}&sort_order={$sort_order}&exclude={$exclude}&include={$include}&show_subpages={$show_subpages}&hierarchy={$hierarchy}&depth={$depth}&show_home={$show_home}&child_of={$child_of}&parent={$child_of}&show_date={$show_date}&date_format={$date_format}");
		
//		echo "<pre>"; print_r($page_array); echo "</pre>";

		if(!$page_array) return "";
		
		if($dropdown == 'on' || $dropdown == 1) {
			$pages = "<form action=\"". get_bloginfo('url') ."\" method=\"get\">\n<select name=\"page_id\" id=\"page_id\" onchange=\"top.location.href='".get_bloginfo('url')."?page_id='+this.value\">";
			$pages .= flexipages_dropdown($page_array);
			$pages .= "</select><noscript><input type=\"submit\" name=\"submit\" value=\"".__('Go', 'flexipages')."\" /></noscript></form>";
		}
		else
			$pages = flexipages_list($page_array);
		
		if(isset($echo) && $echo == 0)
			return $pages;
		
		echo $pages;
	}


	/* Functions for the widget */
		
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;



	function flexipages_widget($args, $widget_args = 1)
	{
//		echo "here";
		extract( $args, EXTR_SKIP );
		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );
		
		$options = get_option('flexipages_widget');
		if ( !isset($options[$number]) )
			$options[$number] = flexipages_options_default();
		
//		echo "<pre>"; print_r ($options[$number]); echo "</pre>";
			
		extract($options[$number]);
		
		$title = apply_filters('widget_title', $options[$number]['title']);
		
		$include = $exclude = '';
		if(isset($exinclude) && isset($exinclude_values)) {
			if($exinclude == 'include')
				$include = $exinclude_values;
			else
				$exclude = $exinclude_values;
		}
		
		if($show_subpages_check == 'off' || !$show_subpages_check) {
			$depth = 1;
			$show_subpages = '';
		}
		else if ($show_subpages_check == 'on' && $show_subpages == 0) {
			$show_subpages = 1;
		}
		
		if($hierarchy == 'off' || !$hierarchy)
			$depth = -1;
		
		if(isset($home_link) && $home_link)
			$show_home = $home_link;
		else if ($show_home_check != 'on')
			$show_home = '';
		else if ($show_home_check == 'on' && !$show_home)
			$show_home = __('Home');
			
		
		if($pagelist = flexipages("echo=0&sort_column={$sort_column}&sort_order={$sort_order}&exclude={$exclude}&include={$include}&show_subpages={$show_subpages}&hierarchy={$hierarchy}&depth={$depth}&show_home={$show_home}&show_date={$show_date}&date_format={$date_format}&dropdown={$dropdown}")){
		
			echo $before_widget;

			if($title && $pagelist)
				echo $before_title . $title . $after_title . "\n";
	
			/* 	$before_pagelist and $after_pagelist are widget arguments that 
				can be defined in the functions.php of your theme.
				These arguments can be used, for example, if you want to enclose
				the	pagelist within a <div>.
			*/
			if(isset($before_pagelist) && isset($after_pagelist)) 
				echo $before_pagelist . $pagelist . $after_pagelist . "\n";
			else echo $pagelist . "\n";

			echo $after_widget;
		}
	}
	
	function flexipages_exinclude_options(
		$sort_column = "menu_order",
		$sort_order = "ASC",
		$selected = array(),
		$parent = 0,
		$level = 0 )
	{
		global $wpdb;
		$items = get_pages("child_of={$parent}&parent={$parent}sort_column={$sort_column}&sort_order={$sort_order}" );
		if ( $items ) {
			foreach ( $items as $item ) {
				$pad = str_repeat( '&nbsp;', $level * 3 );
				if ( in_array($item->ID, $selected))
					$current = ' selected="selected"';
				else
					$current = '';
		
				echo "\n\t<option value='$item->ID'$current>$pad $item->post_title</option>";
				flexipages_exinclude_options( $sort_column, $sort_order, $selected, $item->ID,  $level +1 );
			}
		} else {
			return false;
		}
	}
	

	
	function flexipages_widget_control($widget_args)
	{
		global $wp_registered_widgets;
		static $updated = false;

		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );

		$options = get_option('flexipages_widget');
		if ( !is_array($options) )
			$options = array();

		if ( !$updated && !empty($_POST['sidebar']) ) {
			$sidebar = (string) $_POST['sidebar'];

			$sidebars_widgets = wp_get_sidebars_widgets();
			if ( isset($sidebars_widgets[$sidebar]) )
				$this_sidebar =& $sidebars_widgets[$sidebar];
			else
				$this_sidebar = array();

			foreach ( (array) $this_sidebar as $_widget_id ) {
				if ( 'flexipages_widget' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
					$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
					if ( !in_array( "flexipages-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed.
						unset($options[$widget_number]);
				}
			}

			foreach ( (array) $_POST['flexipages_widget'] as $widget_number => $flexipages_widget ) {
				if ( !isset($flexipages_widget['title']) && isset($options[$widget_number]) ) // user clicked cancel
					continue;
				$title = strip_tags(stripslashes($flexipages_widget['title']));
				$sort_column = strip_tags(stripslashes($flexipages_widget['sort_column']));
				$sort_order = strip_tags(stripslashes($flexipages_widget['sort_order']));
				$exinclude = strip_tags(stripslashes($flexipages_widget['exinclude']));
				$exinclude_values = $flexipages_widget['exinclude_values']?implode(',', $flexipages_widget['exinclude_values']):'';
				$show_subpages_check = strip_tags(stripslashes($flexipages_widget['show_subpages_check']));
				$show_subpages = strip_tags(stripslashes($flexipages_widget['show_subpages']));
				$hierarchy = strip_tags(stripslashes($flexipages_widget['hierarchy']));
				$depth = strip_tags(stripslashes($flexipages_widget['depth']));
				$show_home_check = strip_tags(stripslashes($flexipages_widget['show_home_check']));
				$show_home = strip_tags(stripslashes($flexipages_widget['show_home']));
				$show_date = strip_tags(stripslashes($flexipages_widget['show_date']));
				$date_format = strip_tags(stripslashes($flexipages_widget['date_format']));
				$dropdown = strip_tags(stripslashes($flexipages_widget['dropdown']));
				
				$options[$widget_number] = compact('title', 'sort_column', 'sort_order', 'exinclude', 'exinclude_values', 'show_subpages_check', 'show_subpages', 'hierarchy', 'depth', 'show_home_check', 'show_home', 'show_date', 'date_format', 'dropdown');
			}

			update_option('flexipages_widget', $options);
			$updated = true;
		}

		if ( -1 == $number ) {
			$number = '%i%';
			$options[$number] = flexipages_options_default();
		}
		
		$title = esc_attr($options[$number]['title']);
		$sort_column_select[$options[$number]['sort_column']] = " selected=\"selected\"";
		$sort_order_select[$options[$number]['sort_order']] = " selected=\"selected\"";
		$exinclude_select[$options[$number]['exinclude']] = ' selected="selected"';
		$show_subpages_check_check = ($options[$number]['show_subpages_check'] == 'on')?' checked="checked"':'';
		if($options[$number]['depth'] == -2)
			$show_subpages_select[-2] = ' selected="selected"';
		else if($options[$number]['depth'] == -3)
			$show_subpages_select[-3] = ' selected="selected"';
		else
			$show_subpages_select[$options[$number]['show_subpages']] = ' selected="selected"';
		$show_subpages_display = $show_subpages_check_check?'':' style="display:none;"';
		$hierarchy_check = ($options[$number]['hierarchy'] == 'on')?' checked="checked"':'';
		if(in_array($options[$number]['depth'], array(0, 2, 3, 4, 5)))
			$depth_select[$options[$number]['depth']] = ' selected="selected"';
		else
			$depth_select[0] = ' selected="selected"';
		$depth_display = $hierarchy_check?'':' style="display:none;"';
		$show_home_check_check = ((isset($options[$number]['home_link']) && $options[$number]['home_link']) || $options[$number]['show_home_check'] == 'on')?' checked="checked"':'';
		$show_home_display = $show_home_check_check?'':' style="display:none;"';
		$show_home = isset($options[$number]['home_link'])?esc_attr($options[$number]['home_link']):esc_attr($options[$number]['show_home']);
		$show_date_check = ($options[$number]['show_date'] == 'on')?' checked="checked"':'';
		$date_format_display = $show_date_check?'':' style="display:none;"';
		$date_format_select[$options[$number]['date_format']] = ' selected="selected"';
		$date_format_options = array('j F Y', 'F j, Y', 'Y/m/d', 'd/m/Y', 'm/d/Y');
		$dropdown_check = ($options[$number]['dropdown'] == 'on')?' checked="checked"':'';
		
				?>
		<table cellpadding="10px" cellspacing="10px">
			<tr>
				<td><label for="flexipages-title-<?php echo $number; ?>"><?php _e('Title', 'flexipages'); ?></label></td>
				<td><input class="widefat" id="flexipages-title-<?php echo $number; ?>" name="flexipages_widget[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" /></td>
			</tr>
			
			<tr>
				<td valign="top"><label for="flexipages-sort_column-<?php echo $number; ?>"><?php _e('Sort by', 'flexipages'); ?></label></td>
				<td><select class="widefat" style="display:inline;width:auto;" name="flexipages_widget[<?php echo $number; ?>][sort_column]" id="flexipages-sort_column-<?php echo $number; ?>">
					<option value="post_title"<?php echo $sort_column_select['post_title']; ?>><?php _e('Page title', 'flexipages'); ?></option>
					<option value="menu_order"<?php echo $sort_column_select['menu_order']; ?>><?php _e('Menu order', 'flexipages'); ?></option>
					<option value="post_date"<?php echo $sort_column_select['post_date']; ?>><?php _e('Date created', 'flexipages'); ?></option>
					<option value="post_modified"<?php echo $sort_column_select['post_modified']; ?>><?php _e('Date modified', 'flexipages'); ?></option>
					<option value="ID"<?php echo $sort_column_select['ID']; ?>><?php _e('Page ID', 'flexipages'); ?></option>	
					<option value="post_author"<?php echo $sort_column_select['post_author']; ?>><?php _e('Page author ID', 'flexipages'); ?></option>
					<option value="post_name"<?php echo $sort_column_select['post_name']; ?>><?php _e('Page slug', 'flexipages'); ?></option>
				</select>
				<select class="widefat" style="display:inline;width:auto;" name="flexipages_widget[<?php echo $number; ?>][sort_order]" id="flexipages-sort_order-<?php echo $number; ?>">
					<option value="ASC"<?php echo $sort_order_select['ASC']; ?>><?php _e('ASC', 'flexipages'); ?></option>
					<option value="DESC"<?php echo $sort_order_select['DESC']; ?>><?php _e('DESC', 'flexipages'); ?></option>
				</select></td>
			</tr>
			<tr>			
				<td valign="top"><select class="widefat" style="display:inline;width:auto;" name="flexipages_widget[<?php echo $number; ?>][exinclude]" id="flexipages-exinclude-<?php echo $number; ?>">
					<option value="exclude"<?php echo $exinclude_select['exclude']; ?>><?php _e('Exclude', 'flexipages'); ?></option>
					<option value="include"<?php echo $exinclude_select['include']; ?>><?php _e('Include', 'flexipages'); ?></option>
				</select><?php _e('pages', 'flexipages'); ?></td>
				<td><select name="flexipages_widget[<?php echo $number; ?>][exinclude_values][]" id="flexipages-exinclude_values-<?php echo $number; ?>" class="widefat" style="height:auto;max-height:6em" multiple="multiple" size="4">
					<?php flexipages_exinclude_options($options[$number]['sort_column'], $options[$number]['sort_order'], explode(',', $options[$number]['exinclude_values']),0,0) ?>
				</select><br />
				<small class="setting-description"><?php _e('use &lt;Ctrl&gt; key to select multiple pages', 'flexipages'); ?></small>
				</td>
			</tr>
			<tr>
				<td  style="padding:5px 0;"><label for="flexipages-show_subpages_check-<?php echo $number; ?>"><input type="checkbox" class="checkbox" id="flexipages-show_subpages_check-<?php echo $number; ?>" name="flexipages_widget[<?php echo $number; ?>][show_subpages_check]" onchange="if(this.checked) { getElementById('flexipages-show_subpages-<?php echo $number; ?>').style.display='block'; } else { getElementById('flexipages-show_subpages-<?php echo $number; ?>').style.display='none'; }"<?php echo $show_subpages_check_check; ?> /> <?php _e('Show sub-pages', 'flexipages'); ?></label></td>
				<td><select<?php echo $show_subpages_display; ?> class="widefat" id="flexipages-show_subpages-<?php echo $number; ?>" name="flexipages_widget[<?php echo $number; ?>][show_subpages]">
						<option value="0"<?php echo $show_subpages_select[0]; ?>><?php _e('Show all sub-pages', 'flexipages'); ?></option>
						<option value="-2"<?php echo $show_subpages_select[-2]; ?>><?php _e('Only related sub-pages', 'flexipages'); ?></option>
						<option value="-3"<?php echo $show_subpages_select[-3]; ?>><?php _e('Only strictly related sub-pages', 'flexipages'); ?></option>
						
					</select>
				</td>
			</tr>	
			<tr>
				<td style="padding:5px 0;"><label for="flexipages-hierarchy-<?php echo $number; ?>"><input type="checkbox" class="checkbox" id="flexipages-hierarchy-<?php echo $number; ?>" name="flexipages_widget[<?php echo $number; ?>][hierarchy]" onchange="if(this.checked) { getElementById('flexipages-depth-<?php echo $number; ?>').style.display='block'; } else { getElementById('flexipages-depth-<?php echo $number; ?>').style.display='none'; }"<?php echo $hierarchy_check; ?> /> <?php _e('Show hierarchy', 'flexipages'); ?></label></td>
				<td>
					<select<?php echo $depth_display; ?> class="widefat" id="flexipages-depth-<?php echo $number; ?>" name="flexipages_widget[<?php echo $number; ?>][depth]">
					<?php for($i=2;$i<=5;$i++) { ?>
						<option value="<?php echo $i; ?>"<?php echo $depth_select[$i]; ?>><?php printf(__('%d levels deep', 'flexipages'), $i); ?></option>
					<?php } ?>
					<option value="0"<?php echo $depth_select[0]; ?>><?php _e('Unlimited depth', 'flexipages'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td style="padding:5px 0;"><label for="flexipages-show_home_check-<?php echo $number; ?>"><input type="checkbox" class="checkbox" id="flexipages-show_home_check-<?php echo $number; ?>" name="flexipages_widget[<?php echo $number; ?>][show_home_check]" onchange="if(this.checked) { getElementById('flexipages-show_home-<?php echo $number; ?>').style.display='block'; } else { getElementById('flexipages-show_home-<?php echo $number; ?>').style.display='none'; }"<?php echo $show_home_check_check; ?> /> <?php _e('Show home page', 'flexipages'); ?></label></td>
				<td><input<?php echo $show_home_display; ?> class="widefat" type="text" name="flexipages_widget[<?php echo $number; ?>][show_home]" id ="flexipages-show_home-<?php echo $number; ?>" value="<?php echo htmlspecialchars($show_home, ENT_QUOTES); ?>" /></td>	
			</tr>
			<tr>
			<td style="padding:5px 0;"><label for="flexipages-show_date-<?php echo $number; ?>"><input type="checkbox" class="checkbox" id="flexipages-show_date-<?php echo $number; ?>" name="flexipages_widget[<?php echo $number; ?>][show_date]" onchange="if(this.checked) { getElementById('flexipages-date_format-<?php echo $number; ?>').style.display='block'; } else { getElementById('flexipages-date_format-<?php echo $number; ?>').style.display='none'; }"<?php echo $show_date_check; ?> /> <?php _e('Show date', 'flexipages'); ?></label></td>
			<td><select<?php echo $date_format_display; ?> class="widefat" id="flexipages-date_format-<?php echo $number; ?>" name="flexipages_widget[<?php echo $number; ?>][date_format]" text="Select format">
				<option value=""><?php _e('Choose Format', 'flexipages'); ?></option>
				<?php foreach($date_format_options as $date_format_option) { ?>
					<option value="<?php echo $date_format_option; ?>"<?php echo $date_format_select[$date_format_option]; ?>><?php echo date($date_format_option); ?></option>
				<?php } ?>
			</select>
			</td>
			</tr>
			<tr><td colspan="2" style="padding:5px 0;">
				<input name="flexipages_widget[<?php echo $number; ?>][dropdown]" id="flexipages-dropdown-<?php echo $number; ?>" type="checkbox"<?php echo $dropdown_check; ?>" />
				<label for="flexipages-dropdown-<?php echo $number; ?>"><?php _e('Show as dropdown', 'flexipages'); ?></label>
			</td></tr>			
		</table>
			<p>	
				<input type="hidden" name="flexipages_widget[<?php echo $number; ?>][submit]" value="1" />
			</p>
		<?php

	} 


	function flexipages_widget_register()
	{
		if ( !$options = get_option('flexipages_widget') )
			$options = array();
		$widget_ops = array('classname' => 'flexipages_widget', 'description' => __('A highly configurable widget to list pages and sub-pages.', 'flexipages'));
		$control_ops = array('width' => '380', 'height' => '', 'id_base' => 'flexipages');
		$name = 'Flexi Pages';

		$id = false;
		foreach ( (array) array_keys($options) as $o ) {
			if ( !isset($options[$o]['title']) )
				continue;
			$id = "flexipages-$o";
			wp_register_sidebar_widget($id, $name, 'flexipages_widget', $widget_ops, array( 'number' => $o ));
			wp_register_widget_control($id, $name, 'flexipages_widget_control', $control_ops, array( 'number' => $o ));
		}

		if ( !$id ) {
			wp_register_sidebar_widget( 'flexipages-1', $name, 'flexipages_widget', $widget_ops, array( 'number' => -1 ) );
			wp_register_widget_control( 'flexipages-1', $name, 'flexipages_widget_control', $control_ops, array( 'number' => -1 ) );
		}	
	}
	
	flexipages_widget_register();	
}

add_action('plugins_loaded', 'flexipages_init');

?>
