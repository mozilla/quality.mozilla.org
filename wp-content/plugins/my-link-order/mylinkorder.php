<?php
/*
Plugin Name: My Link Order
Plugin URI: http://www.geekyweekly.com/mylinkorder
Description: My Link Order allows you to set the order in which links and link categories will appear in the sidebar. Uses a drag and drop interface for ordering. Adds a widget with additional options for easy installation on widgetized themes.
Version: 3.1.4
Author: Andrew Charlton
Author URI: http://www.geekyweekly.com
Author Email: froman118@gmail.com
*/

function mylinkorder_menu()
{    
	add_links_page(__('My Link Order', 'mylinkorder'), __('My Link Order', 'mylinkorder'), 'manage_links', 'mylinkorder', 'mylinkorder');
}

function mylinkorder_js_libs() {
	if ( isset($_GET['page']) && $_GET['page'] == "mylinkorder" ) {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
	}
}

function mylinkorder_getTarget() {
	return "link-manager.php?page=mylinkorder";
}

function mylinkorder_set_plugin_meta($links, $file) {
	$plugin = plugin_basename(__FILE__);
	// create link
	if ($file == $plugin) {
		return array_merge( $links, array( 
			'<a href="' . mylinkorder_getTarget() . '">' . __('Order Links') . '</a>',
			'<a href="http://wordpress.org/tags/my-link-order?forum_id=10#postform">' . __('Support Forum') . '</a>',
			'<a href="http://geekyweekly.com/gifts-and-donations">' . __('Donate') . '</a>' 
		));
	}
	return $links;
}

add_filter('plugin_row_meta', 'mylinkorder_set_plugin_meta', 10, 2 );
add_action('admin_menu', 'mylinkorder_menu');
add_action('admin_print_scripts', 'mylinkorder_js_libs');

function mylinkorder()
{
	global $wpdb;
	$success = "";
	$catID = 0;
	
	if (isset($_POST['btnCats']))
		$catID = $_POST['cats'];
	elseif (isset($_POST['hdnCatID'])) 
		$catID = $_POST['hdnCatID'];

	if (isset($_POST['btnReturnParent']))
		$catID = 0;

	if(isset($_GET['hideNote']))
		update_option('mylinkorder_hideNote', '1');
	
	$wpdb->show_errors();
	
	$query1 = $wpdb->query("SHOW COLUMNS FROM $wpdb->terms LIKE 'term_order'");
	
	if ($query1 == 0)
		$wpdb->query("ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'");
	
	$query2 = $wpdb->query("SHOW COLUMNS FROM $wpdb->links LIKE 'link_order'");
	
	if ($query2 == 0)
		$wpdb->query("ALTER TABLE $wpdb->links ADD `link_order` INT( 4 ) NULL DEFAULT '0'");
	
	if (isset($_POST['btnOrderCats'])) { 
		$idString = $_POST['hdnMyLinkOrder'];
		$catIDs = explode(",", $idString);
		$result = count($catIDs);
		for($i = 0; $i <= $result; $i++)
		{
			$str = str_replace("id_", "", $catIDs[$i]);
			$wpdb->query("UPDATE $wpdb->terms SET term_order = '$i' WHERE term_id ='$str'");
		}
			
		$success = '<div id="message" class="updated fade"><p>'. __('Link Categories updated successfully.', 'mylinkorder').'</p></div>';
	}
	
	if (isset($_POST['btnOrderLinks'])) { 
		$idString = $_POST['hdnMyLinkOrder'];
		$linkIDs = explode(",", $idString);
		$result = count($linkIDs);
		for($i = 0; $i <= $result; $i++)
		{
			$str = str_replace("id_", "", $linkIDs[$i]);
			$wpdb->query("UPDATE $wpdb->links SET link_order = '$i' WHERE link_id ='$str'");
		}
		
		$success = '<div id="message" class="updated fade"><p>'. __('Links updated successfully.', 'mylinkorder').'</p></div>';
	}
?>

<div class='wrap'>
	<form name="frmMyLinkOrder" method="post" action="">
		<h2><?php _e('My Link Order', 'mylinkorder') ?></h2>
		<?php
		echo $success; 
		
		if (get_option("mylinkorder_hideNote") != "1")
		{	?>
			<div class="updated">
				<strong><p><?php _e('If you like my plugin please consider donating. Every little bit helps me provide support and continue development.','mylinkorder'); ?> <a href="http://geekyweekly.com/gifts-and-donations"><?php _e('Donate', 'mylinkorder'); ?></a>&nbsp;&nbsp;<small><a href="<?php echo mylinkorder_getTarget(); ?>&hideNote=true"><?php _e('No thanks, hide this', 'mylinkorder'); ?></a></small></p></strong>
			</div>
		<?php
		}
		
	if($catID != 0)
	{
		$results=$wpdb->get_results("SELECT * FROM $wpdb->links l inner join $wpdb->term_relationships tr on l.link_id = tr.object_id inner join $wpdb->term_taxonomy tt on tt.term_taxonomy_id = tr.term_taxonomy_id inner join $wpdb->terms t on t.term_id = tt.term_id WHERE t.term_id = $catID ORDER BY link_order ASC");
		$cat_name = $wpdb->get_var("SELECT name FROM $wpdb->terms WHERE term_id=$catID");
	?>
		<h3><?php _e('Order Links for', 'mylinkorder') ?> <?php _e($cat_name) ?></h3>

		<p><?php _e('Order the links by dragging and dropping them into the desired order.', 'mylinkorder') ?></p>
		<ul id="myLinkOrderList"><?php
		foreach($results as $row)
		{
			echo "<li id='id_$row->link_id' class='lineitem'>".__($row->link_name)."</li>";
		}?>
		</ul>
	
		<input type="submit" id="btnOrderLinks" name="btnOrderLinks" class="button-primary" value="<?php _e('Click to Order Links', 'mylinkorder') ?>" onclick="javascript:orderLinks(); return true;" />
		&nbsp;&nbsp;<input type="submit" class="button" id="btnReturnParent" name="btnReturnParent" value="<?php _e('Go Back', 'mylinkorder') ?>" />
	<?php
	}
	else
	{
		$results=$wpdb->get_results("SELECT DISTINCT t.term_id, name FROM $wpdb->term_taxonomy tt inner join $wpdb->term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join $wpdb->terms t on t.term_id = tt.term_id where taxonomy = 'link_category' ORDER BY t.term_order ASC");
		?>
		
		<p><?php _e('Choose a category from the drop down to order the links in that category or order the categories by dragging and dropping them.', 'mylinkorder') ?></p>
	
		<h3><?php _e('Order Links', 'mylinkorder') ?></h3>
	
		<select id="cats" name="cats"><?php
		foreach($results as $row)
		{
			echo "<option value='$row->term_id'>".__($row->name)."</option>";
		}?>
		</select>
		&nbsp;<input type="submit" name="btnCats" id="btnCats" class="button" value="<?php _e('Order Links in this Category', 'mylinkorder') ?>" />
	
		<h3><?php _e('Order Link Categories', 'mylinkorder') ?></h3>
	
		<ul id="myLinkOrderList"><?php
		foreach($results as $row)
		{
			echo "<li id='id_$row->term_id' class='lineitem'>".__($row->name)."</li>";
		}?>
		</ul>
		<input type="submit" name="btnOrderCats" id="btnOrderCats" class="button-primary" value="<?php _e('Click to Order Categories', 'mylinkorder') ?>" onclick="javascript:orderLinkCats(); return true;" />
		
	<?php
	}
	?>
	&nbsp;&nbsp;<strong id="updateText"></strong>
	<br /><br />
		<p>
			<a href="http://geekyweekly.com/mylinkorder"><?php _e('Plugin Homepage', 'mylinkorder') ?></a>&nbsp;|&nbsp;<a href="http://geekyweekly.com/gifts-and-donations"><?php _e('Donate', 'mylinkorder') ?></a>&nbsp;|&nbsp;<a href="http://wordpress.org/tags/my-link-order?forum_id=10"><?php _e('Support Forum', 'mylinkorder') ?></a>
		</p>
		<input type="hidden" id="hdnMyLinkOrder" name="hdnMyLinkOrder" />
		<input type="hidden" id="hdnCatID" name="hdnCatID" value="<?php echo $catID; ?>" />
		</form>
	</div>
	
	<style type="text/css">
		#myLinkOrderList {
			width: 90%; 
			border:1px solid #B2B2B2; 
			margin:10px 10px 10px 0px;
			padding:5px 10px 5px 10px;
			list-style:none;
			background-color:#fff;
			-moz-border-radius:3px;
			-webkit-border-radius:3px;
		}

		li.lineitem {
			border:1px solid #B2B2B2;
			-moz-border-radius:3px;
			-webkit-border-radius:3px;
			background-color:#F1F1F1;
			color:#000;
			cursor:move;
			font-size:13px;
			margin-top:5px;
			margin-bottom:5px;
			padding: 2px 5px 2px 5px;
			height:1.5em;
			line-height:1.5em;
		}
		
		.sortable-placeholder{ 
			border:1px dashed #B2B2B2;
			margin-top:5px;
			margin-bottom:5px; 
			padding: 2px 5px 2px 5px;
			height:1.5em;
			line-height:1.5em;	
		}
	</style>
	
	<script language="JavaScript" type="text/javascript">
	
		function mylinkorderaddloadevent(){
			jQuery("#myLinkOrderList").sortable({ 
				placeholder: "sortable-placeholder", 
				revert: false,
				tolerance: "pointer" 
			});
		};
	
		addLoadEvent(mylinkorderaddloadevent);
	
		function orderLinkCats() {
			jQuery("#updateText").html("<?php _e('Updating Link Category Order...', 'mylinkorder') ?>");
			jQuery("#hdnMyLinkOrder").val(jQuery("#myLinkOrderList").sortable("toArray"));
		}
	
		function orderLinks() {
			jQuery("#updateText").html("<?php _e('Updating Link Order...', 'mylinkorder') ?>");
			jQuery("#hdnMyLinkOrder").val(jQuery("#myLinkOrderList").sortable("toArray"));
		}
		
	</script>

	<?php
}

function mylinkorder_applyorderfilter($orderby, $args)
{
	if($args['orderby'] == 'order')
		return 't.term_order';
	else
		return $orderby;
}

add_filter('get_terms_orderby', 'mylinkorder_applyorderfilter', 10, 2);
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
		mylinkorder_list_bookmarks(apply_filters('widget_links_args', array('title_before' => $before_title, 'title_after' => $after_title, 'class' => 'linkcat widget',
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

function mylinkorder_list_bookmarks($args = '') {
	$defaults = array(
		'orderby' => 'name', 'order' => 'ASC',
		'limit' => -1, 'category' => '', 'exclude_category' => '',
		'category_name' => '', 'hide_invisible' => 1,
		'show_updated' => 0, 'echo' => 1,
		'categorize' => 1, 'title_li' => __('Bookmarks'),
		'title_before' => '<h2>', 'title_after' => '</h2>',
		'category_orderby' => 'name', 'category_order' => 'ASC',
		'class' => 'linkcat', 'category_before' => '<li id="%id" class="%class">',
		'category_after' => '</li>'
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$output = '';

	if ( $categorize ) {
		//Split the bookmarks into ul's for each category
		$cats = get_terms('link_category', array('name__like' => $category_name, 'include' => $category, 'exclude' => $exclude_category, 'orderby' => $category_orderby, 'order' => $category_order, 'hierarchical' => 0));

		foreach ( (array) $cats as $cat ) {
			$params = array_merge($r, array('category'=>$cat->term_id));
			$bookmarks = mylinkorder_get_bookmarks($params);
			if ( empty($bookmarks) )
				continue;
			$output .= str_replace(array('%id', '%class'), array("linkcat-$cat->term_id", $class), $category_before);
			$catname = apply_filters( "link_category", $cat->name );
			$output .= "$title_before$catname$title_after\n\t<ul class='xoxo blogroll'>\n";
			$output .= _walk_bookmarks($bookmarks, $r);
			$output .= "\n\t</ul>\n$category_after\n";
		}
	} else {
		//output one single list using title_li for the title
		$bookmarks = mylinkorder_get_bookmarks($r);

		if ( !empty($bookmarks) ) {
			if ( !empty( $title_li ) ){
				$output .= str_replace(array('%id', '%class'), array("linkcat-$category", $class), $category_before);
				$output .= "$title_before$title_li$title_after\n\t<ul class='xoxo blogroll'>\n";
				$output .= _walk_bookmarks($bookmarks, $r);
				$output .= "\n\t</ul>\n$category_after\n";
			} else {
				$output .= _walk_bookmarks($bookmarks, $r);
			}
		}
	}

	$output = apply_filters( 'wp_list_bookmarks', $output );

	if ( !$echo )
		return $output;
	echo $output;
}

function mylinkorder_get_bookmarks($args = '') {
	global $wpdb;

	$defaults = array(
		'orderby' => 'name', 'order' => 'ASC',
		'limit' => -1, 'category' => '',
		'category_name' => '', 'hide_invisible' => 1,
		'show_updated' => 0, 'include' => '',
		'exclude' => '', 'search' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$cache = array();
	$key = md5( serialize( $r ) );
	if ( $cache = wp_cache_get( 'get_bookmarks', 'bookmark' ) ) {
		if ( is_array($cache) && isset( $cache[ $key ] ) )
			return apply_filters('get_bookmarks', $cache[ $key ], $r );
	}

	if ( !is_array($cache) )
		$cache = array();

	$inclusions = '';
	if ( !empty($include) ) {
		$exclude = '';  //ignore exclude, category, and category_name params if using include
		$category = '';
		$category_name = '';
		$inclinks = preg_split('/[\s,]+/',$include);
		if ( count($inclinks) ) {
			foreach ( $inclinks as $inclink ) {
				if (empty($inclusions))
					$inclusions = ' AND ( link_id = ' . intval($inclink) . ' ';
				else
					$inclusions .= ' OR link_id = ' . intval($inclink) . ' ';
			}
		}
	}
	if (!empty($inclusions))
		$inclusions .= ')';

	$exclusions = '';
	if ( !empty($exclude) ) {
		$exlinks = preg_split('/[\s,]+/',$exclude);
		if ( count($exlinks) ) {
			foreach ( $exlinks as $exlink ) {
				if (empty($exclusions))
					$exclusions = ' AND ( link_id <> ' . intval($exlink) . ' ';
				else
					$exclusions .= ' AND link_id <> ' . intval($exlink) . ' ';
			}
		}
	}
	if (!empty($exclusions))
		$exclusions .= ')';

	if ( !empty($category_name) ) {
		if ( $category = get_term_by('name', $category_name, 'link_category') ) {
			$category = $category->term_id;
		} else {
			$cache[ $key ] = array();
			wp_cache_set( 'get_bookmarks', $cache, 'bookmark' );
			return apply_filters( 'get_bookmarks', array(), $r );
		}
	}

	if ( ! empty($search) ) {
		$search = like_escape($search);
		$search = " AND ( (link_url LIKE '%$search%') OR (link_name LIKE '%$search%') OR (link_description LIKE '%$search%') ) ";
	}

	$category_query = '';
	$join = '';
	if ( !empty($category) ) {
		$incategories = preg_split('/[\s,]+/',$category);
		if ( count($incategories) ) {
			foreach ( $incategories as $incat ) {
				if (empty($category_query))
					$category_query = ' AND ( tt.term_id = ' . intval($incat) . ' ';
				else
					$category_query .= ' OR tt.term_id = ' . intval($incat) . ' ';
			}
		}
	}
	if (!empty($category_query)) {
		$category_query .= ") AND taxonomy = 'link_category'";
		$join = " INNER JOIN $wpdb->term_relationships AS tr ON ($wpdb->links.link_id = tr.object_id) INNER JOIN $wpdb->term_taxonomy as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id";
	}

	if ( $show_updated && get_option('links_recently_updated_time') ) {
		$recently_updated_test = ", IF (DATE_ADD(link_updated, INTERVAL " . get_option('links_recently_updated_time') . " MINUTE) >= NOW(), 1,0) as recently_updated ";
	} else {
		$recently_updated_test = '';
	}

	$get_updated = ( $show_updated ) ? ', UNIX_TIMESTAMP(link_updated) AS link_updated_f ' : '';

	$orderby = strtolower($orderby);
	$length = '';
	switch ( $orderby ) {
		case 'length':
			$length = ", CHAR_LENGTH(link_name) AS length";
			break;
		case 'rand':
			$orderby = 'rand()';
			break;
		case 'link_id':
			$orderby = "$wpdb->links.link_id";
			break;
		default:
			$orderparams = array();
			foreach ( explode(',', $orderby) as $ordparam ) {
				$ordparam = trim($ordparam);
				if ( in_array( $ordparam, array( 'order', 'name', 'url', 'visible', 'rating', 'owner', 'updated' ) ) )
					$orderparams[] = 'link_' . $ordparam;
			}
			$orderby = implode(',', $orderparams);
	}

	if ( empty( $orderby ) )
		$orderby = 'link_name';

	$order = strtoupper( $order );
	if ( '' !== $order && !in_array( $order, array( 'ASC', 'DESC' ) ) )
		$order = 'ASC';

	$visible = '';
	if ( $hide_invisible )
		$visible = "AND link_visible = 'Y'";

	$query = "SELECT * $length $recently_updated_test $get_updated FROM $wpdb->links $join WHERE 1=1 $visible $category_query";
	$query .= " $exclusions $inclusions $search";
	$query .= " ORDER BY $orderby $order";
	if ($limit != -1)
		$query .= " LIMIT $limit";

	$results = $wpdb->get_results($query);

	$cache[ $key ] = $results;
	wp_cache_set( 'get_bookmarks', $cache, 'bookmark' );

	return apply_filters('get_bookmarks', $results, $r);
}

?>
