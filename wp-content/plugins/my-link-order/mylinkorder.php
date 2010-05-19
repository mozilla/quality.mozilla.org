<?php
/*
Plugin Name: My Link Order
Plugin URI: http://www.geekyweekly.com/mylinkorder
Description: My Link Order allows you to set the order in which links and link categories will appear in the sidebar. Uses a drag and drop interface for ordering. Adds a widget with additional options for easy installation on widgetized themes.
Version: 2.9.1
Author: Andrew Charlton
Author URI: http://www.geekyweekly.com
Author Email: froman118@gmail.com
*/

function mylinkorder_init() {

function mylinkorder_menu()
{   if (function_exists('add_submenu_page'))
        add_submenu_page(mylinkorder_getTarget(), 'My Link Order', __('My Link Order', 'mylinkorder'), 5, "mylinkorder", 'mylinkorder');
}

function mylinkorder_js_libs() {
	if ( $_GET['page'] == "mylinkorder" ) {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
	}
}

function mylinkorder_getTarget() {
	return "link-manager.php";
}

function mylinkorder_set_plugin_meta($links, $file) {
	$plugin = plugin_basename(__FILE__);
	// create link
	if ($file == $plugin) {
		return array_merge( $links, array( 
			'<a href="link-manager.php?page=mylinkorder">' . __('Order Links') . '</a>',
			'<a href="http://wordpress.org/tags/my-link-order?forum_id=10#postform">' . __('Support Forum') . '</a>',
			'<a href="http://geekyweekly.com/gifts-and-donations">' . __('Donate') . '</a>' 
		));
	}
	return $links;
}

add_filter( 'plugin_row_meta', 'mylinkorder_set_plugin_meta', 10, 2 );
add_action('admin_menu', 'mylinkorder_menu');
add_action('admin_menu', 'mylinkorder_js_libs');

function mylinkorder()
{
	global $wpdb;
	$mode = "";
	$mode = $_GET['mode'];
	$success = "";
	$catID = "";
	
	if(isset($_GET['hideNote']))
		update_option('mylinkorder_hideNote', '1');
	
	$wpdb->show_errors();
	
	$query1 = $wpdb->query("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_order'");
	
	if ($query1 == 0)
		$wpdb->query("ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'");
	
	$query2 = $wpdb->query("SHOW COLUMNS FROM $wpdb->links LIKE 'link_order'");
	
	if ($query2 == 0)
		$wpdb->query("ALTER TABLE $wpdb->links ADD `link_order` INT( 4 ) NULL DEFAULT '0'");
	
	if($mode == "act_OrderCategories")
	{
		$idString = $_GET['idString'];
		$catIDs = explode(",", $idString);
		$result = count($catIDs);
		for($i = 0; $i <= $result; $i++)
			$wpdb->query("UPDATE $wpdb->terms SET term_order = '$i' WHERE term_id ='$catIDs[$i]'");
			
		$success = '<div id="message" class="updated fade"><p>'. __('Link Categories updated successfully.', 'mylinkorder').'</p></div>';
	}
	
	if($mode == "act_OrderLinks")
	{
		$idString = $_GET['idString'];
		$linkIDs = explode(",", $idString);
		$result = count($linkIDs);
		for($i = 0; $i <= $result; $i++)
			$wpdb->query("UPDATE $wpdb->links SET link_order = '$i' WHERE link_id ='$linkIDs[$i]'");
		
		$success = '<div id="message" class="updated fade"><p>'. __('Links updated successfully.', 'mylinkorder').'</p></div>';
		$mode = "dsp_OrderLinks";
	}

	if($mode == "dsp_OrderLinks")
	{
		$catID = $_GET['catID'];
		$results=$wpdb->get_results("SELECT * FROM $wpdb->links l inner join $wpdb->term_relationships tr on l.link_id = tr.object_id inner join $wpdb->term_taxonomy tt on tt.term_taxonomy_id = tr.term_taxonomy_id inner join $wpdb->terms t on t.term_id = tt.term_id WHERE t.term_id = $catID ORDER BY link_order ASC");
		$cat_name = $wpdb->get_var("SELECT name FROM $wpdb->terms WHERE term_id=$catID");
	?>

	<div class='wrap'>
		<h2><?php _e('Order Links for', 'mylinkorder') ?> <?=$cat_name?></h2>
		
		<?php 
		echo $success; 
		if (get_option("mylinkorder_hideNote") != "1")
		{	?>
			<div class="updated">
				<strong><p><?php _e('If you like my plugin please consider donating. Every little bit helps me provide support and continue development.','mylinkorder'); ?> <a href="http://geekyweekly.com/gifts-and-donations"><?php _e('Donate', 'mylinkorder'); ?></a>&nbsp;&nbsp;<small><a href="link-manager.php?page=mylinkorder&hideNote=true"><?php _e('No thanks, hide this', 'mylinkorder'); ?></a></small></p></strong>
			</div>
		<?php
		}
		
		?>
		
		<p><?php _e('Order the links by dragging and dropping them into the desired order.', 'mylinkorder') ?></p>
		<ul id="order" style="width: 90%; margin:10px 10px 10px 0px; padding:10px; border:1px solid #B2B2B2; list-style:none;"><?php
		foreach($results as $row)
		{
			echo "<li id='$row->link_id' class='lineitem'>$row->link_name</li>";
		}?>
		</ul>
	
		<input type="button" id="orderButton" Value="<?php _e('Click to Order Links', 'mylinkorder') ?>" onclick="javascript:orderLinks();">&nbsp;&nbsp;<strong id="updateText"></strong>
		<br /><br />
		<a href='<?php echo mylinkorder_getTarget(); ?>?page=mylinkorder'><?php _e('Go Back', 'mylinkorder') ?></a>
	
	</div>

	<?php
	}
	else
	{
		$results=$wpdb->get_results("SELECT DISTINCT t.term_id, name FROM $wpdb->term_taxonomy tt inner join $wpdb->term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join $wpdb->terms t on t.term_id = tt.term_id where taxonomy = 'link_category' ORDER BY t.term_order ASC");
		?>
	<div class='wrap'>
		<h2><?php _e('My Link Order', 'mylinkorder') ?></h2>
		
		<?php 
		echo $success; 
		if (get_option("mylinkorder_hideNote") != "1")
		{	?>
			<div class="updated">
				<strong><p><?php _e('If you like my plugin please consider donating. Every little bit helps me provide support and continue development.','mylinkorder'); ?> <a href="http://geekyweekly.com/gifts-and-donations"><?php _e('Donate', 'mylinkorder'); ?></a>&nbsp;&nbsp;<small><a href="link-manager.php?page=mylinkorder&hideNote=true"><?php _e('No thanks, hide this', 'mylinkorder'); ?></a></small></p></strong>
			</div>
		<?php
		}
		
		?>
		
		<p><?php _e('Choose a category from the drop down to order the links in that category or order the categories by dragging and dropping them.', 'mylinkorder') ?></p>
	
		<h3><?php _e('Order Links', 'mylinkorder') ?></h3>
	
		<select id="cats" name='cats'><?php
		foreach($results as $row)
		{
			echo "<option value='$row->term_id'>$row->name</option>";
		}?>
		</select>
		&nbsp;<input type="button" name="edit" Value="<?php _e('Order Links in this Category', 'mylinkorder') ?>" onClick="javascript:goEdit();">
	
		<h3><?php _e('Order Link Categories', 'mylinkorder') ?></h3>
	
		<ul id="order" style="width: 90%; margin:10px 10px 10px 0px; padding:10px; border:1px solid #B2B2B2; list-style:none;"><?php
		foreach($results as $row)
		{
			echo "<li id='$row->term_id' class='lineitem'>$row->name</li>";
		}?>
		</ul>
		<input type="button" id="orderButton" Value="<?php _e('Click to Order Categories', 'mylinkorder') ?>" onclick="javascript:orderLinkCats();">&nbsp;&nbsp;<strong id="updateText"></strong>
		
		<p>
			<a href="http://geekyweekly.com/mylinkorder"><?php _e('Plugin Homepage', 'mylinkorder') ?></a>
			&nbsp;|&nbsp;
			<a href="http://geekyweekly.com/gifts-and-donations"><?php _e('Donate', 'mylinkorder') ?></a>
			&nbsp;|&nbsp;
			<a href="http://wordpress.org/tags/my-link-order?forum_id=10"><?php _e('Support Forum', 'mylinkorder') ?></a>
		</p>
	</div>
	<?php
	}
	?>
	<style>
		li.lineitem {
			margin: 3px 0px;
			padding: 2px 5px 2px 5px;
			background-color: #F1F1F1;
			border:1px solid #B2B2B2;
			cursor: move;
		}
	</style>
	
	<script language="JavaScript" type="text/javascript">
	
		function mylinkorderaddloadevent(){
			jQuery("#order").sortable({ 
				placeholder: "ui-selected", 
				revert: false,
				tolerance: "pointer" 
			});
		};
	
		addLoadEvent(mylinkorderaddloadevent);
	
		function orderLinkCats() {
			jQuery("#orderButton").css("display", "none");
			jQuery("#updateText").html("<?php _e('Updating Link Category Order...', 'mylinkorder') ?>");
			
			idList = jQuery("#order").sortable("toArray");
			location.href = '<?php echo mylinkorder_getTarget(); ?>?page=mylinkorder&mode=act_OrderCategories&idString='+idList;
		}
	
		function orderLinks() {
			jQuery("#orderButton").css("display", "none");
			jQuery("#updateText").html("<?php _e('Updating Link Order...', 'mylinkorder') ?>");
			
			idList = jQuery("#order").sortable("toArray");
			location.href = '<?php echo mylinkorder_getTarget(); ?>?page=mylinkorder&mode=act_OrderLinks&catID=<?php echo $catID; ?>&idString='+idList;
		}
	
		function goEdit ()
		{
			if(jQuery("#cats").val() != "")
				location.href="<?php echo mylinkorder_getTarget(); ?>?page=mylinkorder&mode=dsp_OrderLinks&catID="+jQuery("#cats").val();
		}
	</script>

	<?php
}
}

function mylinkorder_applyorderfilter($orderby, $args)
{
	if($args['orderby'] == 'order')
		return 't.term_order';
	else
		return $orderby;
}

add_filter('get_terms_orderby', 'mylinkorder_applyorderfilter', 10, 2);

add_action('plugins_loaded', 'mylinkorder_init');

/* Load Translations */
add_action('init', 'mylinkorder_loadtranslation');

function mylinkorder_loadtranslation() {
	load_plugin_textdomain('mylinkorder', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
}

class mylinkorder_Widget extends WP_Widget {

	function mylinkorder_Widget() {
		$widget_ops = array('classname' => 'widget_mylinkorder', 'description' => __( 'Enhanced Link widget provided by My Link Order') );
		$this->WP_Widget('mylinkorder', __('My Link Order'), $widget_ops);	}

	function widget( $args, $instance ) {
		extract( $args );

		$title_li = apply_filters('widget_title', empty( $instance['title_li'] ) ? __( 'Bookmarks' ) : $instance['title_li']);
		$category_orderby = empty( $instance['category_orderby'] ) ? 'order' : $instance['category_orderby'];
		$category_order = empty( $instance['category_order'] ) ? 'asc' : $instance['category_order'];
		$orderby = empty( $instance['orderby'] ) ? 'order' : $instance['orderby'];
		$order = empty( $instance['order'] ) ? 'asc' : $instance['order'];
		
		$exclude = empty( $instance['exclude'] ) ? '' : $instance['exclude'];
		$include = empty( $instance['include'] ) ? '' : $instance['include'];
		$category = empty( $instance['category'] ) ? '' : $instance['category'];
		$exclude_category = empty( $instance['exclude_category'] ) ? '' : $instance['exclude_category'];
		$limit = empty( $instance['limit'] ) ? '-1' : $instance['limit'];
		$categorize = empty( $instance['categorize'] ) ? '1' : '0';
		$show_images = empty( $instance['show_images'] ) ? '0' : $instance['show_images'];
		$show_description = empty( $instance['show_description'] ) ? '0' : $instance['show_description'];
		$show_name = empty( $instance['show_name'] ) ? '0' : $instance['show_name'];
		$show_rating = empty( $instance['show_rating'] ) ? '0' : $instance['show_rating'];
		$show_updated = empty( $instance['show_updated'] ) ? '0' : $instance['show_updated'];
		$hide_invisible = empty( $instance['hide_invisible'] ) ? '1' : '0';
		$between = empty( $instance['between'] ) ? "\n" : $instance['between'];
		$link_before = empty( $instance['link_before'] ) ? '' : $instance['link_before'];
		$link_after = empty( $instance['link_after'] ) ? '' : $instance['link_after'];

		$before_widget = preg_replace('/id="[^"]*"/','id="%id"', $before_widget);
		wp_list_bookmarks(apply_filters('widget_links_args', array('title_before' => $before_title, 'title_after' => $after_title, 'class' => 'linkcat widget',
			'category_before' => $before_widget, 'category_after' => $after_widget, 'exclude' => $exclude, 'include' => $include,
			'title_li' => $title_li, 'category_orderby' => $category_orderby, 'category_order' => $category_order, 'orderby' => $orderby, 'order' => $order,
			'category' => $category, 'exclude_category' => $exclude_category, 'limit' => $limit, 'categorize' => $categorize,
			'show_images' => $show_images, 'show_description' => $show_description, 'show_name' => $show_name,
			'show_rating' => $show_rating, 'show_updated' => $show_updated, 'hide_invisible' => $hide_invisible, 'between' => $between,
			'link_before' => $link_before, 'link_after' => $link_after
		)));
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( in_array( $new_instance['category_orderby'], array( 'order', 'name', 'count', 'ID', 'slug' ) ) ) {
			$instance['category_orderby'] = $new_instance['category_orderby'];
		} else {
			$instance['category_orderby'] = 'order';
		}
		
		if ( in_array( $new_instance['category_order'], array( 'asc', 'desc' ) ) ) {
			$instance['category_order'] = $new_instance['category_order'];
		} else {
			$instance['category_order'] = 'asc';
		}
		
		if ( in_array( $new_instance['orderby'], array( 'order', 'name', 'rand', 'ID', 'description', 'length', 'notes', 'owner', 'rel', 'rss', 'target', 'updated', 'url' ) ) ) {
			$instance['orderby'] = $new_instance['orderby'];
		} else {
			$instance['orderby'] = 'order';
		}
		
		if ( in_array( $new_instance['order'], array( 'asc', 'desc' ) ) ) {
			$instance['order'] = $new_instance['order'];
		} else {
			$instance['order'] = 'asc';
		}
		$instance['title_li'] = strip_tags( $new_instance['title_li'] );
		$instance['exclude'] = strip_tags( $new_instance['exclude'] );
		$instance['include'] = strip_tags( $new_instance['include'] );
		$instance['category'] = strip_tags( $new_instance['category'] );
		$instance['exclude_category'] = strip_tags( $new_instance['exclude_category'] );
		$instance['limit'] = strip_tags( $new_instance['limit'] );
		$instance['categorize'] = strip_tags( $new_instance['categorize'] );
		$instance['show_images'] = strip_tags( $new_instance['show_images'] );
		$instance['show_description'] = strip_tags( $new_instance['show_description'] );
		$instance['show_name'] = strip_tags( $new_instance['show_name'] );
		$instance['show_rating'] = strip_tags( $new_instance['show_rating'] );
		$instance['show_updated'] = strip_tags( $new_instance['show_updated'] );
		$instance['hide_invisible'] = strip_tags( $new_instance['hide_invisible'] );
		$instance['between'] = $new_instance['between'];
		$instance['link_before'] = $new_instance['link_before'];
		$instance['link_after'] = $new_instance['link_after'];

		return $instance;
	}
	
	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'categorize' => '','category' => '', 'category_orderby' => 'order', 'category_order' => 'asc', '_orderby' => 'order', 'order' => 'asc', 'exclude' => '', 'exclude_category' => '', 'include' => '', 'limit' => '', 'title_li' => '', 'link_before' => '', 'link_after' => '', 'between' => '', 'show_images' => '', 'show_description' => '', 'show_name' => '', 'show_rating' => '', 'show_updated' => '', 'hide_invisible' => '' ) );
		
		$categorize = esc_attr( $instance['categorize'] );
		$category = esc_attr( $instance['category'] );
		$category_orderby = esc_attr( $instance['category_orderby'] );
		$category_order = esc_attr( $instance['category_order'] );
		$orderby = esc_attr( $instance['orderby'] );
		$order = esc_attr( $instance['order'] );
		$exclude_category = esc_attr( $instance['exclude_category'] );
		$title_li = esc_attr( $instance['title_li'] );
		$include = esc_attr( $instance['include'] );
		$exclude = esc_attr( $instance['exclude'] );
		$limit = esc_attr( $instance['limit'] );
		$link_before = esc_attr( $instance['link_before'] );
		$link_after = esc_attr( $instance['link_after'] );
		$between = esc_attr( $instance['between'] );
		$show_images = esc_attr( $instance['show_images'] );
		$show_description  = esc_attr( $instance['show_description'] );
		$show_name = esc_attr( $instance['show_name'] );
		$show_rating = esc_attr( $instance['show_rating'] );
		$show_updated = esc_attr( $instance['show_updated'] );
		$hide_invisible = esc_attr( $instance['hide_invisible'] );

	?>	
		<p>
			<label for="<?php echo $this->get_field_id('category_orderby'); ?>"><?php _e( 'Category Order By:', 'mylinkorder' ); ?></label>
			<select name="<?php echo $this->get_field_name('category_orderby'); ?>" id="<?php echo $this->get_field_id('category_orderby'); ?>" class="widefat">
				<option value="order"<?php selected( $instance['category_orderby'], 'order' ); ?>><?php _e('My Order', 'mylinkorder'); ?></option>
				<option value="name"<?php selected( $instance['category_orderby'], 'name' ); ?>><?php _e('Name', 'mylinkorder'); ?></option>
				<option value="count"<?php selected( $instance['category_orderby'], 'count' ); ?>><?php _e( 'Count', 'mylinkorder' ); ?></option>
				<option value="ID"<?php selected( $instance['category_orderby'], 'ID' ); ?>><?php _e( 'ID', 'mylinkorder' ); ?></option>
				<option value="slug"<?php selected( $instance['category_orderby'], 'slug' ); ?>><?php _e( 'Slug', 'mylinkorder' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('category_order'); ?>"><?php _e( 'Category Order:', 'mylinkorder' ); ?></label>
			<select name="<?php echo $this->get_field_name('category_order'); ?>" id="<?php echo $this->get_field_id('category_order'); ?>" class="widefat">
				<option value="asc"<?php selected( $instance['category_order'], 'asc' ); ?>><?php _e('Ascending', 'mylinkorder'); ?></option>
				<option value="desc"<?php selected( $instance['category_order'], 'desc' ); ?>><?php _e('Descending', 'mylinkorder'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e( 'Link Order By:', 'mylinkorder' ); ?></label>
			<select name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" class="widefat">
				<option value="order"<?php selected( $instance['orderby'], 'order' ); ?>><?php _e('My Order', 'mylinkorder'); ?></option>
				<option value="name"<?php selected( $instance['orderby'], 'name' ); ?>><?php _e('Name', 'mylinkorder'); ?></option>
				<option value="rand"<?php selected( $instance['orderby'], 'rand' ); ?>><?php _e( 'Random', 'mylinkorder' ); ?></option>
				<option value="description"<?php selected( $instance['orderby'], 'description' ); ?>><?php _e( 'Description' ); ?></option>
				<option value="length"<?php selected( $instance['orderby'], 'length' ); ?>><?php _e( 'Length', 'mylinkorder' ); ?></option>
				<option value="ID"<?php selected( $instance['orderby'], 'ID' ); ?>><?php _e( 'ID' ); ?></option>
				<option value="notes"<?php selected( $instance['orderby'], 'notes' ); ?>><?php _e( 'Notes', 'mylinkorder' ); ?></option>
				<option value="owner"<?php selected( $instance['orderby'], 'owner' ); ?>><?php _e( 'Owner', 'mylinkorder' ); ?></option>
				<option value="rel"<?php selected( $instance['orderby'], 'rel' ); ?>><?php _e( 'Relationship (XFN)', 'mylinkorder' ); ?></option>
				<option value="rss"<?php selected( $instance['orderby'], 'rss' ); ?>><?php _e( 'RSS', 'mylinkorder' ); ?></option>
				<option value="target"<?php selected( $instance['orderby'], 'target' ); ?>><?php _e( 'Target', 'mylinkorder' ); ?></option>
				<option value="updated"<?php selected( $instance['orderby'], 'updated' ); ?>><?php _e( 'Updated', 'mylinkorder' ); ?></option>
				<option value="url"<?php selected( $instance['orderby'], 'url' ); ?>><?php _e( 'URL', 'mylinkorder' ); ?></option>	
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e( 'Link Order:', 'mylinkorder' ); ?></label>
			<select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>" class="widefat">
				<option value="asc"<?php selected( $instance['order'], 'asc' ); ?>><?php _e('Ascending', 'mylinkorder'); ?></option>
				<option value="desc"<?php selected( $instance['order'], 'desc' ); ?>><?php _e('Descending', 'mylinkorder'); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('exclude_category'); ?>"><?php _e( 'Exclude Category:', 'mylinkorder' ); ?></label> <input type="text" value="<?php echo $exclude_category; ?>" name="<?php echo $this->get_field_name('exclude_category'); ?>" id="<?php echo $this->get_field_id('exclude_category'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Link Category IDs, separated by commas.', 'mylinkorder' ); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e( 'Include Category:', 'mylinkorder' ); ?></label> <input type="text" value="<?php echo $category; ?>" name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Link Category IDs, separated by commas.', 'mylinkorder' ); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e( 'Exclude Link:', 'mylinkorder' ); ?></label> <input type="text" value="<?php echo $exclude; ?>" name="<?php echo $this->get_field_name('exclude'); ?>" id="<?php echo $this->get_field_id('exclude'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Link IDs, separated by commas.', 'mylinkorder' ); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('include'); ?>"><?php _e( 'Include Link:', 'mylinkorder' ); ?></label> <input type="text" value="<?php echo $include; ?>" name="<?php echo $this->get_field_name('include'); ?>" id="<?php echo $this->get_field_id('include'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Link IDs, separated by commas. Use with Include Category.', 'mylinkorder' ); ?></small>
		</p>
		
		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['categorize'], true) ?> id="<?php echo $this->get_field_id('categorize'); ?>" name="<?php echo $this->get_field_name('categorize'); ?>" />
			<label for="<?php echo $this->get_field_id('categorize'); ?>"><?php _e('Show Uncategorized', 'mylinkorder'); ?></label><br />
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_images'], true) ?> id="<?php echo $this->get_field_id('show_images'); ?>" name="<?php echo $this->get_field_name('show_images'); ?>" />
			<label for="<?php echo $this->get_field_id('show_images'); ?>"><?php _e('Show Link Image', 'mylinkorder'); ?></label><br />
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_name'], true) ?> id="<?php echo $this->get_field_id('show_name'); ?>" name="<?php echo $this->get_field_name('show_name'); ?>" />
			<label for="<?php echo $this->get_field_id('show_name'); ?>"><?php _e('Show Link Name', 'mylinkorder'); ?></label><br />
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_description'], true) ?> id="<?php echo $this->get_field_id('show_description'); ?>" name="<?php echo $this->get_field_name('show_description'); ?>" />
			<label for="<?php echo $this->get_field_id('show_description'); ?>"><?php _e('Show Link Description', 'mylinkorder'); ?></label><br />
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_rating'], true) ?> id="<?php echo $this->get_field_id('show_rating'); ?>" name="<?php echo $this->get_field_name('show_rating'); ?>" />
			<label for="<?php echo $this->get_field_id('show_rating'); ?>"><?php _e('Show Link Rating', 'mylinkorder'); ?></label><br />
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_updated'], true) ?> id="<?php echo $this->get_field_id('show_updated'); ?>" name="<?php echo $this->get_field_name('show_updated'); ?>" />
			<label for="<?php echo $this->get_field_id('show_updated'); ?>"><?php _e('Show Update Date', 'mylinkorder'); ?></label><br />
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['hide_invisible'], true) ?> id="<?php echo $this->get_field_id('hide_invisible'); ?>" name="<?php echo $this->get_field_name('hide_invisible'); ?>" />
			<label for="<?php echo $this->get_field_id('hide_invisible'); ?>"><?php _e('Show Invisible', 'mylinkorder'); ?></label><br />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('title_li'); ?>"><?php _e( 'Title:', 'mylinkorder' ); ?></label> <input type="text" value="<?php echo $title_li; ?>" name="<?php echo $this->get_field_name('title_li'); ?>" id="<?php echo $this->get_field_id('title_li'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Used when Uncategorized is checked.', 'mylinkorder' ); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('link_before'); ?>"><?php _e( 'Link Before:', 'mylinkorder' ); ?></label> <input type="text" value="<?php echo $link_before; ?>" name="<?php echo $this->get_field_name('link_before'); ?>" id="<?php echo $this->get_field_id('link_before'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Text inside link, before link text.', 'mylinkorder' ); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('link_after'); ?>"><?php _e( 'Link After:', 'mylinkorder' ); ?></label> <input type="text" value="<?php echo $link_after; ?>" name="<?php echo $this->get_field_name('link_after'); ?>" id="<?php echo $this->get_field_id('link_after'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Text inside link, after link text.', 'mylinkorder' ); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e( 'Limit:', 'mylinkorder' ); ?></label> <input type="text" value="<?php echo $limit; ?>" name="<?php echo $this->get_field_name('limit'); ?>" id="<?php echo $this->get_field_id('limit'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Maximum number of links to display.', 'mylinkorder' ); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('between'); ?>"><?php _e( 'Between:', 'mylinkorder' ); ?></label> <input type="text" value="<?php echo $between; ?>" name="<?php echo $this->get_field_name('between'); ?>" id="<?php echo $this->get_field_id('between'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Text between link and its description, defaults to newline.', 'mylinkorder' ); ?></small>
		</p>

<?php
	}
}

function mylinkorder_widgets_init() {
	register_widget('mylinkorder_Widget');
}

add_action('widgets_init', 'mylinkorder_widgets_init');

?>
