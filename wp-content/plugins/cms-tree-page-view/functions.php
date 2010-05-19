<?php

function cms_tpv_admin_head() {
	global $cms_tpv_view;
	if (isset($_GET["cms_tpv_view"])) {
		$cms_tpv_view = $_GET["cms_tpv_view"];
	} else {
		$cms_tpv_view = "all";
	}
	?>
	<script type="text/javascript">
		var CMS_TPV_URL = "<?php echo CMS_TPV_URL ?>";
		var CMS_TPV_AJAXURL = "?action=cms_tpv_get_childs&view=";
		var CMS_TPV_VIEW = "<?php echo $cms_tpv_view ?>";
	</script>

    <!--[if IE 6]>
    	<style>
    		#cms_tree_view_search_form {
    			display: none !important;
    		}
			#cms_tpv_dashboard_widget .subsubsub li {
			}
    	</style>
    <![endif]-->
	<?php
}

function cms_tpv_admin_init() {
	wp_enqueue_style( "cms_tpv_styles", CMS_TPV_URL . "styles/styles.css", false, CMS_TPV_VERSION );
	wp_enqueue_script( "jquery-cookie", CMS_TPV_URL . "scripts/jquery.cookie.js", array("jquery"));
	wp_enqueue_script( "jquery-jstree", CMS_TPV_URL . "scripts/jquery.tree.js", false, CMS_TPV_VERSION);
	wp_enqueue_script( "jquery-jstree-plugin-cookie", CMS_TPV_URL . "scripts/plugins/jquery.tree.cookie.js", false, CMS_TPV_VERSION);
	wp_enqueue_script( "cms_tree_page_view", CMS_TPV_URL . "scripts/cms_tree_page_view.php?wp-abspath=" . urlencode(ABSPATH), false, CMS_TPV_VERSION);
	load_plugin_textdomain('cms-tree-page-view', WP_CONTENT_DIR . "/plugins/languages", "/cms-tree-page-view/languages");

}

function cms_tpv_wp_dashboard_setup() {
	if ( cms_tpv_show_on_dashboard() ) {
		wp_add_dashboard_widget('cms_tpv_dashboard_widget', 'CMS Tree Page View', 'cms_tpv_dashboard');
	}
}

function cms_tpv_show_on_dashboard() {
	// @todo: fix this
	return false;
	if ( get_option('cms_tpv_show_on_dashboard', 1) == 1 && current_user_can("edit_pages") ) {
		return true;
	} else {
		return false;
	}
}
function cms_tpv_show_under_pages() {
	if ( get_option('cms_tpv_show_under_pages', 1) == 1 && current_user_can("edit_pages") ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Output on dashboard
 */
function cms_tpv_dashboard() {

	cms_tpv_print_common_tree_stuff();

}

function cms_tpv_admin_menu() {
	if ( cms_tpv_show_under_pages() ) {
		add_pages_page( CMS_TPV_NAME, CMS_TPV_NAME, "edit_pages", "cms-tpv-pages-page", "cms_tpv_pages_page" );
	}
	add_submenu_page( 'options-general.php' , CMS_TPV_NAME, CMS_TPV_NAME, "administrator", "cms-tpv-options", "cms_tpv_options");
}


/**
 * Output options page
 */
function cms_tpv_options() {
	?>
	
	<div class="wrap">
		<h2><?php echo CMS_TPV_NAME ?> <?php _e("settings", 'cms-tree-page-view') ?></h2>
		
		<form method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<?php _e("Show tree", 'cms-tree-page-view') ?>
					</th>
					<td>
						<!--
						@todo: fix this
						<input type="checkbox" name="cms_tpv_show_on_dashboard" id="cms_tpv_show_on_dashboard" value="1" <?php echo get_option('cms_tpv_show_on_dashboard', 1) ? " checked='checked'" : "" ?> />
						<label for="cms_tpv_show_on_dashboard"><?php _e("on the dashboard", 'cms-tree-page-view') ?></label>
						<br />
						-->
						<input type="checkbox" name="cms_tpv_show_under_pages" id="cms_tpv_show_under_pages" value="1" <?php echo get_option('cms_tpv_show_under_pages', 1) ? " checked='checked'" : "" ?> />
						<label for="cms_tpv_show_under_pages"><?php _e("under the pages menu", 'cms-tree-page-view') ?></label>
					</td>
				</tr>
			</table>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="cms_tpv_show_on_dashboard,cms_tpv_show_under_pages" />
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cms-tree-page-view') ?>" />
			</p>
		</form>
</div>
	
	<?php
}


/**
 * Print tree stuff that is common for both dashboard and page
 */
function cms_tpv_print_common_tree_stuff() {
	$pages = cms_tpv_get_pages();
	if (empty($pages)) {
		echo '<div class="updated fade below-h2"><p>No pages found. Maybe you want to <a href="page-new.php">add a new page</a>?</p></div>';
	} else {
		// start the party!
		global $cms_tpv_view;
		?>

		<ul class="cms-tpv-subsubsub">
			<li><a id="cms_tvp_view_all" class="<?php echo ($cms_tpv_view=="all") ? "current" : "" ?>" href="<?php echo CMS_TPV_PAGE_FILE ?>&amp;cms_tpv_view=all"><?php _e("All", 'cms-tree-page-view') ?></a> |</li>
			<li><a id="cms_tvp_view_public" class="<?php echo ($cms_tpv_view=="public") ? "current" : "" ?>" href="<?php echo CMS_TPV_PAGE_FILE ?>&amp;cms_tpv_view=public"><?php _e("Public", 'cms-tree-page-view') ?></a></li>

			<li><a href="#" id="cms_tpv_open_all"><?php _e("Expand", 'cms-tree-page-view') ?></a> |</li>
			<li><a href="#" id="cms_tpv_close_all"><?php _e("Collapse", 'cms-tree-page-view') ?></a></li>
			
			<li>
				<form id="cms_tree_view_search_form">
					<input type="text" name="search" id="cms_tree_view_search" />
					<input type="submit" id="cms_tree_view_search_submit" value="<?php _e("Search", 'cms-tree-page-view') ?>" />
					<span id="cms_tree_view_search_form_working"><?php _e("Searching...", 'cms-tree-page-view') ?></span>
				</form>
			</li>
		</ul>
			
		<div class="" id="cms_tpv_working"><?php _e("Loading...", 'cms-tree-page-view') ?></div>
		
		<div class="updated below-h2 hidden" id="cms_tpv_search_no_hits"><p><?php _e("Search: no pages found", 'cms-tree-page-view') ?></p></div>
		
		<div id="cms_tpv_container" class="tree-default"><?php _e("Loading tree", 'cms-tree-page-view') ?></div>
		<div style="clear: both;"></div>
	
		<?php
	}
} // func


/**
 * Pages page
 * A page with the tree. Good stuff.
 */
function cms_tpv_pages_page() {
	?>
	<div class="wrap">

		<h2><?php echo CMS_TPV_NAME ?></h2>
		
		<?php
		cms_tpv_print_common_tree_stuff();
		?>
	
	</div>
	<?php
}

/**
 * Stripped down code from get_pages. Modified to get drafts and some other stuff too.
 *
 * @todo: cache, check permissions for private pages
 */
function cms_tpv_get_pages($args = null) {

	global $wpdb;

    $defaults = array(
		"parent" => -1,
		"view" => "all" // all | public
    );
    $r = wp_parse_args( $args, $defaults );
	extract($r, EXTR_SKIP);

	$where = "";
	if ($parent >= 0) {
		$where = $wpdb->prepare(' AND post_parent = %d ', $parent);
	}
	
	$whereView = "";
	if ($view == "all") {
	} else {
		// list of statuses:
		// http://wordpress.org/support/topic/314325
		$whereView = " AND ( post_status NOT IN ('pending', 'private', 'future', 'draft') ) ";
	}
	
	$where_post_type = $wpdb->prepare( "post_type = '%s' AND post_status <> '%s'", "page", "trash");	
	$query = "SELECT * FROM $wpdb->posts WHERE ($where_post_type) $where $whereView";
	$query .= " ORDER BY menu_order ASC, post_title ASC" ;
	#echo $query;
	$pages = $wpdb->get_results($query);

	return $pages;

}




/**
 * Output JSON for the children of a node
 * $arrOpenChilds = array with id of pages to open children on
 */
function cms_tpv_print_childs($pageID, $view = "all", $arrOpenChilds = null) {

	$arrPages = cms_tpv_get_pages("parent=$pageID&view=$view");
	if ($arrPages) {
		?>[<?php
		for ($i=0, $pagesCount = sizeof($arrPages); $i<$pagesCount; $i++) {
			$onePage = $arrPages[$i];
			$editLink = get_edit_post_link($onePage->ID, 'display');
			$content = wp_specialchars($onePage->post_content);
			$content = str_replace(array("\n","\r"), "", $content);
			$hasChildren = false;
			$arrChildPages = cms_tpv_get_pages("parent={$onePage->ID}&view=$view");

			if ($arrChildPages) {
				$hasChildren = true;
			}
			// if no children, output no state
			$strState = '"state": "closed",';
			if (!$hasChildren) {
				$strState = '';
			}
			
			// type of node
			$rel = $onePage->post_status;
			if ($onePage->post_password) {
				$rel = "password";
			}
			
			$title = get_the_title($onePage->ID); // so hooks and stuff will do their work
			if (empty($title)) {
				$title = __("<Untitled page>", 'cms-tree-page-view');
			}
			$title = wp_specialchars($title);
			?>
			{
				"data": {
					"title": "<?php echo $title ?>",
					"attributes": {
						"href": "<?php echo $editLink ?>"
					}
				},
				<?php echo $strState ?>
				"attributes": {
					"id": "cms-tpv-<?php echo $onePage->ID ?>",
					"rel": "<?php echo $rel ?>",
					"childCount": <?php echo sizeof($arrChildPages) ?>,
					"title": "<?php _e("Click to edit. Drag to move.", 'cms-tree-page-view') ?>",
					"permalink": "<?php echo get_permalink($onePage->ID) ?>"
				}
				<?php
				// if id is in $arrOpenChilds then also output children on this one
				if ($hasChildren && isset($arrOpenChilds) && in_array($onePage->ID, $arrOpenChilds)) {
					?>, "children": <?php
					cms_tpv_print_childs($onePage->ID, $view, $arrOpenChilds);
					?><?php
				}
				?>

			}
			<?php
			// no comma for last page
			if ($i < $pagesCount-1) {
				?>,<?php
			}
		}
		?>]<?php
	}
}

// Act on AJAX-call
function cms_tpv_get_childs() {

	$action = $_GET["action"];
	$view = $_GET["view"]; // all | public
	$search = trim($_GET["search"]); // exits if we're doing a search
	if ($action) {
	
		if ($search) {
			
			// find all pages that contains $search
			// collect all post_parent
			// for each parent id traverse up until post_parent is 0, saving all ids on the way
			
			// what to search: since all we see in the GUI is the title, just search that
			global $wpdb;
			$sqlsearch = "%{$search}%";
			// fells bad to leave out the "'" in the query, but prepare seems to add it..??
			$sql = $wpdb->prepare("SELECT id, post_parent FROM $wpdb->posts WHERE post_type = 'page' AND post_title LIKE %s", $sqlsearch);
			$hits = $wpdb->get_results($sql);
			$arrNodesToOpen = array();
			foreach ($hits as $oneHit) {
				$arrNodesToOpen[] = $oneHit->post_parent;
			}
			
			$arrNodesToOpen = array_unique($arrNodesToOpen);
			$arrNodesToOpen2 = array();
			// find all parents to the arrnodestopen
			foreach ($arrNodesToOpen as $oneNode) {
				if ($oneNode > 0) {
					// not at top so check it out
					$parentNodeID = $oneNode;
					while ($parentNodeID != 0) {
						$hits = $wpdb->get_results($sql);
						$sql = "SELECT id, post_parent FROM $wpdb->posts WHERE id = $parentNodeID";
						$row = $wpdb->get_row($sql);
						$parentNodeID = $row->post_parent;
						$arrNodesToOpen2[] = $parentNodeID;
					}
				}
			}
			
			$arrNodesToOpen = array_merge($arrNodesToOpen, $arrNodesToOpen2);
			$sReturn = "";
			foreach ($arrNodesToOpen as $oneNodeID) {
				$sReturn .= "cms-tpv-{$oneNodeID},";
			}
			$sReturn = preg_replace("/,$/", "", $sReturn);
			
			if ($sReturn) {
				echo $sReturn;
			} else {
				// if no hits
				echo 0;
			}

			exit;

		} else {	
			$id = $_GET["id"];
			$id = (int) str_replace("cms-tpv-", "", $id);

			$jstree_open = array();
			if ( isset( $_COOKIE["jstree_open"] ) ) {
				$jstree_open = $_COOKIE["jstree_open"]; // like this: [jstree_open] => cms-tpv-1282,cms-tpv-1284,cms-tpv-3
				$jstree_open = explode( ",", $jstree_open );
				for( $i=0; $i<sizeof( $jstree_open ); $i++ ) {
					$jstree_open[$i] = (int) str_replace("cms-tpv-", "", $jstree_open[$i]);
				}
			}
			
			cms_tpv_print_childs($id, $view, $jstree_open);
			exit;
		}
	}

	exit;
}

function cms_tpv_add_page() {
	global $wpdb;

	/*
	(
	[action] => cms_tpv_add_page 
	[pageID] => cms-tpv-1318
	type
	)
	*/
	$type = $_POST["type"];
	$pageID = $_POST["pageID"];
	$pageID = str_replace("cms-tpv-", "", $pageID);
	$page_title = trim($_POST["page_title"]);
	if (!$page_title) { $page_title = __("New page", 'cms-tree-page-view'); }

	$ref_post = get_post($pageID);
	
	if ("after" == $type) {

		/*
			add page under/below ref_post
		*/

		// update menu_order of all pages below our page
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET menu_order = menu_order+2 WHERE post_parent = %d AND menu_order >= %d AND id <> %d ", $ref_post->post_parent, $ref_post->menu_order, $ref_post->ID ) );		
		
		// create a new page and then goto it
		$post_new = array();
		$post_new["menu_order"] = $ref_post->menu_order+1;
		$post_new["post_parent"] = $ref_post->post_parent;
		$post_new["post_type"] = "page";
		$post_new["post_status"] = "draft";
		$post_new["post_title"] = $page_title;
		$post_new["post_content"] = "";
		$newPostID = wp_insert_post($post_new);

	} else if ( "inside" == $type ) {

		/*
			add page inside ref_post
		*/

		// update menu_order, so our new post is the only one with order 0
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET menu_order = menu_order+1 WHERE post_parent = %d", $ref_post->ID) );		

		$post_new = array();
		$post_new["menu_order"] = 0;
		$post_new["post_parent"] = $ref_post->ID;
		$post_new["post_type"] = "page";
		$post_new["post_status"] = "draft";
		$post_new["post_title"] = $page_title;
		$post_new["post_content"] = "";
		$newPostID = wp_insert_post($post_new);

	}

	if ($newPostID) {
		// return editlink for the newly created page
		$editLink = get_edit_post_link($newPostID, '');
		echo $editLink;
	} else {
		// fail, tell js
		echo "0";
	}
	
	
	exit;
}


// AJAX: perform move of article
function cms_tpv_move_page() {
	/*
	 the node that was moved,
	 the reference node in the move,
	 the new position relative to the reference node (one of "before", "after" or "inside"), 
	 	inside = man placerar den under en sida som inte har några barn?
	*/

	global $wpdb;
	
	$node_id = $_POST["node_id"]; // the node that was moved
	$ref_node_id = $_POST["ref_node_id"];
	$type = $_POST["type"];

	$node_id = str_replace("cms-tpv-", "", $node_id);
	$ref_node_id = str_replace("cms-tpv-", "", $ref_node_id);
	
	if ($node_id && $ref_node_id) {
		#echo "\nnode_id: $node_id";
		#echo "\ntype: $type";	
		
		$post_node = get_post($node_id);
		$post_ref_node = get_post($ref_node_id);
		
		if ( "inside" == $type ) {
			
			// post_node is moved inside ref_post_node
			// add ref_post_node as parent to post_node and set post_nodes menu_order to 0
			$post_to_save = array(
				"ID" => $post_node->ID,
				"menu_order" => 0,
				"post_parent" => $post_ref_node->ID
			);
			wp_update_post( $post_to_save );
			
			echo "did inside";
			
		} elseif ( "before" == $type ) {
		
			// post_node is placed before ref_post_node
			// @todo: check how this works with revisions

			// update menu_order of all pages with a meny order more than or equal ref_node_post and with the same parent as ref_node_post
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET menu_order = menu_order+1 WHERE post_parent = %d", $post_ref_node->post_parent ) );

			// update menu_order of $post_node to the menu_order that ref_post_node had, and update post_parent to the same as ref_post
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET menu_order = %d, post_parent = %d WHERE ID = %d", $post_ref_node->menu_order, $post_ref_node->post_parent, $post_node->ID ) );

			echo "did before";

		} elseif ( "after" == $type ) {
		
			// post_node is placed after ref_post_node
			
			// update menu_order of all posts with the same parent ref_post_node and with a menu_order of the same as ref_post_node, but do not include ref_post_node
			// +2 since multiple can have same menu order and we want our moved post to have a unique "spot"
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET menu_order = menu_order+2 WHERE post_parent = %d AND menu_order >= %d AND id <> %d ", $post_ref_node->post_parent, $post_ref_node->menu_order, $post_ref_node->ID ) );

			// update menu_order of post_node to the same that ref_post_node_had+1
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET menu_order = %d, post_parent = %d WHERE ID = %d", $post_ref_node->menu_order+1, $post_ref_node->post_parent, $post_node->ID ) );
			
			echo "did after";
		}
		
		#echo "ok"; // I'm done here!
		
		
	} else {
		// error
		
	}
	
	exit;
}

function bonny_d($var) {
	echo "<pre>";
	print_r($var);
	echo "</pre>";
}

/**
 * Shortcode for videos
 * Usage: [video source="<youtube url>" w=<width, default 480> h=<height, default 385>]
 * Example: [video source="http://www.youtube.com/watch?v=QMGVWCPRNLI&feature=related" w=650]
 */
function shortcode_pb_video($options) {

	$defaults = array(
		"source" => "",
		"w" => 480,
		"h" => 385
	);
	$options = polarbear_extend($defaults, $options);
	$source = $options["source"];

	// if opnly one option and no source, that first one is probably the source...
	if (!$source && isset($options[0])) {
		$source = $options[0];
	}
	
	$return = "";

	/*
	Array
	(
	    [source] => http://www.youtube.com/watch?v=UiIxRxG39KY&feature=related
	)
	*/
	// check for youtube
	if (strpos($source, "youtube.com/") !== false) {
		preg_match("/v=([a-zA-Z0-9_]+)/", $source, $matches);
		if (isset($matches[1])) {
			$youtube_videoID = $matches[1];
			#$return .= "<br>youtube_videoID: $youtube_videoID";
			$return .= "<object width='{$options["w"]}' height='{$options["h"]}'><param name='movie' value='http://www.youtube.com/v/{$youtube_videoID}'></param><param name='allowFullScreen' value='true'></param><param name='allowscriptaccess' value='always'></param><embed src='http://www.youtube.com/v/{$youtube_videoID}' type='application/x-shockwave-flash' allowscriptaccess='always' allowfullscreen='true' width='{$options["w"]}' height='{$options["h"]}'></embed></object>";
		}
	}
	
	return $return;
}

?>