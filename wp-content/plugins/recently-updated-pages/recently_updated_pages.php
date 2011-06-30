<?php

    /*****
     *
     * Plugin name:     Recently Updated Pages
     * Description:     Purpose of this plugin is to display the list of pages (and optionally posts) on
     *                  Wordpress blog those have been recently updated.
     * Version:         1.0.2
     * Plugin URI:      http://resource.bdwebwork.com/WordpressPlugins/RecentlyUpdatedPages/
     * Author:          Ehsanul Haque
     * Author URI:      http://ehsan.bdwebwork.com/
     *
     */
    
    // Registering the widget
    function recently_updated_pages() {
        register_widget('Recently_Updated_Pages');
    }
    
    // Class Recently Updated Pages is extending WP_Widget class
    class Recently_Updated_Pages extends WP_Widget {
        function Recently_Updated_Pages() {
            $widgetSettings     = array (
                                        'classname'     => 'Recently_Updated_Pages',
                                        'description'   => 'Purpose of this plugin is to display the list of pages on Wordpress blog those have been recently updated.'
                                        );
            
            $controlSettings    = array (
                                        'width'         => 300,
                                        'height'        => 400,
                                        'id_base'       => 'recently_updated_pages'
                                        );
                                        
            $this->WP_Widget('recently_updated_pages', 'Recently Updated Pages', $widgetSettings, $controlSettings);
        }

        // Displaying the widget on the blog
        function widget($args, $instance) {
            extract($args);

            $title              = apply_filters('widget_title', $instance['title']);
            $totalPagesToShow   = (int) $instance['totalPagesToShow'];
            $showListWithPosts  = (int) $instance['showListWithPosts'];
	    $displayDate	= (int) $instance['displayDate'];
	    $dateFormat		= apply_filters('dateFormat', $instance['dateFormat']);

            $defaults           = array (
                                        'title'             => 'Recently Updated Pages',
                                        'totalPagesToShow'  => 3,
                                        'showListWithPosts' => 0,
                                        'displayDate'       => 1,
                                        'dateFormat'        => "jS F'y"
                                        );
                                    
            echo $before_widget;

            if ($title != "") {
                echo $before_title . $title . $after_title;
            } else {
                echo $before_title . $defaults['title'] . $after_title;
            }

            if ($totalPagesToShow != 0) {
                $pageList       = $this->getListOfPages($totalPagesToShow, $showListWithPosts);
            } else {
                $pageList       = $this->getListOfPages($defaults['totalPagesToShow'], $defaults['showListWithPosts']);
            }

            if (!empty($pageList)) {
                echo "<ul>";
                    foreach ($pageList as $obj) {
                        echo "<li class='page_item page-item-{$obj->ID}'><a href='{$obj->uri}' title='{$obj->post_title}'>{$obj->post_title}</a>";
			if ($displayDate == 1) {
                            echo " on " . date($dateFormat, strtotime($obj->post_modified));
                        }
                        echo "</li>";
                    }
                echo "</ul>";
            }
            echo $after_widget;
        }        

        // Updating the settings
        function update($new_instance, $old_instance) {
            $instance                       = $old_instance;
            $instance['title']              = strip_tags($new_instance['title']);
            $instance['totalPagesToShow']   = strip_tags($new_instance['totalPagesToShow']);
            $instance['showListWithPosts']  = strip_tags($new_instance['showListWithPosts']);
            $instance['displayDate']        = strip_tags($new_instance['displayDate']);
            $instance['dateFormat']         = strip_tags($new_instance['dateFormat']);

            return $instance;
        }

        // WP Admin panel form to modify the setting
        function form($instance) {

            $defaults       = array ( 
                                    'title'             => 'Recently Updated Pages', 
                                    'totalPagesToShow'  => 3,
                                    'showListWithPosts' => 0,
                                    'displayDate'       => 1,
                                    'dateFormat'        => "jS F'y"
                                    );
                                    
            $instance       = wp_parse_args((array) $instance, $defaults);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('totalPagesToShow'); ?>">Total Pages to Show:</label>
			<input id="<?php echo $this->get_field_id('totalPagesToShow'); ?>" name="<?php echo $this->get_field_name('totalPagesToShow'); ?>" value="<?php echo $instance['totalPagesToShow']; ?>" size="5" />
		</p>

                <p>
                        <label for="<?php echo $this->get_field_id('dateFormat'); ?>">Date Format:</label>
                        <input id="<?php echo $this->get_field_id('dateFormat'); ?>" name="<?php echo $this->get_field_name('dateFormat'); ?>" value="<?php echo $instance['dateFormat']; ?>" size="15" />
                </p>

                <p>
                        <label for="<?php echo $this->get_field_id('displayDate'); ?>">Display Date:</label>
                        <input id="<?php echo $this->get_field_id('displayDate'); ?>" name="<?php echo $this->get_field_name('displayDate'); ?>" value="1" type="checkbox"


                   <?php
                   if ($instance['displayDate'] == 1) {
                       echo " Checked";
                   }
                   ?>

                   />
                </p>

		<p>
			<label for="<?php echo $this->get_field_id('showListWithPosts'); ?>">Include blog Posts in the list:</label>
			<input id="<?php echo $this->get_field_id('showListWithPosts'); ?>" name="<?php echo $this->get_field_name('showListWithPosts'); ?>" value="1" type="checkbox"
                   <?php
                   if ($instance['showListWithPosts'] == 1) {
                       echo " Checked";
                   }
                   ?>
                   />
		</p>
                <p>
<hr/>
<b>Information on Date Format</b>
<hr/><small>
d - Day of the month, 2 digits with leading zeros (01 to 31)<br/>
D - 3 letter textual representation of a day (Mon through Sun)<br/>
j - Day of the month without leading zeros (1 to 31)<br/>
F - A full textual representation of a month (January through December)<br/>
m - Numeric representation of a month, with leading zeros (01 through 12)<br/>
M - A short textual representation of a month, three letters (Jan through Dec)<br/>
Y - A full numeric representation of a year, 4 digits (2000 or 2009)<br/>
y - A two digit representation of a year (98 or 09)<br/>
g - 12-hour format of an hour without leading zeros (1 through 12)<br/>
i - Minutes with leading zeros (00 to 59)<br/>
s - Seconds, with leading zeros (00 through 59)<br/>
<a href="http://www.php.net/date" target="_blank" title="More information on Date Format">More Info on Date Format</a></small>
</p>
<?php
        }

        // Getting the list of pages ( and posts) based on the option set by the user
        function getListOfPages($totalPagesToShow, $showListWithPosts) {
            GLOBAL $wpdb;

            if ($showListWithPosts == 1) {
                $postTypeWhere      = "post_type IN ('page', 'post')";
            } else {
                $postTypeWhere      = "post_type = 'page'";
            }
            $sql            = "SELECT ID, post_title, post_modified FROM
                                {$wpdb->posts} WHERE
                                post_status = 'publish' AND
                                {$postTypeWhere}
                                ORDER BY post_modified DESC
                                LIMIT {$totalPagesToShow}";

            $list           = (array) $wpdb->get_results($sql);

            if (!empty($list)) {
                foreach ($list as $key => $val) {
                    $val->uri = get_permalink($val->ID);
                }
            }

            return $list;
        }
    }

    // Adding the functions to the WP widget
    add_action('widgets_init', 'recently_updated_pages');
    
?>
