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
		/* <![CDATA[ */
		var CMS_TPV_URL = "<?php echo CMS_TPV_URL ?>";
		var CMS_TPV_AJAXURL = "?action=cms_tpv_get_childs&view=";
		var CMS_TPV_VIEW = "<?php echo $cms_tpv_view ?>";
		/* ]]> */
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

	define( "CMS_TPV_PAGE_FILE", menu_page_url("cms-tpv-pages-page", false)); // this no longer feels nasty! :)
	wp_enqueue_style( "cms_tpv_styles", CMS_TPV_URL . "styles/styles.css", false, CMS_TPV_VERSION );
	wp_enqueue_style( "jquery-alerts", CMS_TPV_URL . "styles/jquery.alerts.css", false, CMS_TPV_VERSION );
	wp_enqueue_script( "jquery-cookie", CMS_TPV_URL . "scripts/jquery.biscuit.js", array("jquery")); // renamed from cookie to fix problems with mod_security
	wp_enqueue_script( "jquery-jstree", CMS_TPV_URL . "scripts/jquery.jstree.js", false, CMS_TPV_VERSION);
	wp_enqueue_script( "jquery-alerts", CMS_TPV_URL . "scripts/jquery.alerts.js", false, CMS_TPV_VERSION);

	wp_enqueue_script( "cms_tree_page_view", CMS_TPV_URL . "scripts/cms_tree_page_view.js", false, CMS_TPV_VERSION);

	load_plugin_textdomain('cms-tree-page-view', WP_CONTENT_DIR . "/plugins/languages", "/cms-tree-page-view/languages");
	$oLocale = array(
		"Enter_title_of_new_page" => __("Enter title of new page", 'cms-tree-page-view'),
		"child_pages"  => __("child pages", 'cms-tree-page-view'),
		"Edit_page"  => __("Edit page", 'cms-tree-page-view'),
		"View_page"  => __("View page", 'cms-tree-page-view'),
		"Edit"  => __("Edit", 'cms-tree-page-view'),
		"View"  => __("View", 'cms-tree-page-view'),
		"Add_page"  => __("Add page", 'cms-tree-page-view'),
		"Add_new_page_after"  => __("Add new page after", 'cms-tree-page-view'),
		"after"  => __("after", 'cms-tree-page-view'),
		"inside"  => __("inside", 'cms-tree-page-view'),
		"Add_new_page_inside"  => __("Add new page inside", 'cms-tree-page-view'),
		"Status_draft" => __("draft", 'cms-tree-page-view'),
		"Status_future" => __("future", 'cms-tree-page-view'),
		"Status_password" => __("protected", 'cms-tree-page-view'),	// is "protected" word better than "password" ?
		"Status_pending" => __("pending", 'cms-tree-page-view'),
		"Status_private" => __("private", 'cms-tree-page-view'),
		"Password_protected_page" => __("Password protected page", 'cms-tree-page-view')
	);
	wp_localize_script( "cms_tree_page_view", 'cmstpv_l10n', $oLocale);

}

function cms_tpv_wp_dashboard_setup() {
	if ( cms_tpv_show_on_dashboard() ) {
		wp_add_dashboard_widget('cms_tpv_dashboard_widget', 'CMS Tree Page View', 'cms_tpv_dashboard');
	}
}

function cms_tpv_show_on_dashboard() {
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

	cms_tpv_show_annoying_box();
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
						<input type="checkbox" name="cms_tpv_show_on_dashboard" id="cms_tpv_show_on_dashboard" value="1" <?php echo get_option('cms_tpv_show_on_dashboard', 1) ? " checked='checked'" : "" ?> />
						<label for="cms_tpv_show_on_dashboard"><?php _e("on the dashboard", 'cms-tree-page-view') ?></label>
						<br />
						
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
		echo '<div class="updated fade below-h2"><p>' . __("No pages found. Maybe you want to <a href='post-new.php?post_type=page'>add a new page</a>?", 'cms-tree-page-view') . '</p></div>';
	} else {
		// start the party!
		global $cms_tpv_view;
		?>

		<ul class="cms-tpv-subsubsub">
			<li><a id="cms_tvp_view_all" class="<?php echo ($cms_tpv_view=="all") ? "current" : "" ?>" href="#"><?php _e("All", 'cms-tree-page-view') ?></a> |</li>
			<li><a id="cms_tvp_view_public" class="<?php echo ($cms_tpv_view=="public") ? "current" : "" ?>" href="#"><?php _e("Public", 'cms-tree-page-view') ?></a></li>

			<li><a href="#" id="cms_tpv_open_all"><?php _e("Expand", 'cms-tree-page-view') ?></a> |</li>
			<li><a href="#" id="cms_tpv_close_all"><?php _e("Collapse", 'cms-tree-page-view') ?></a></li>
			
			<li>
				<form id="cms_tree_view_search_form" method="get" action="">
					<input type="text" name="search" id="cms_tree_view_search" />
					<a title="<?php _e("Clear search", 'cms-tree-page-view') ?>" id="cms_tree_view_search_form_reset" href="#">x</a>
					<input type="submit" id="cms_tree_view_search_submit" value="<?php _e("Search", 'cms-tree-page-view') ?>" />
					<span id="cms_tree_view_search_form_working"><?php _e("Searching...", 'cms-tree-page-view') ?></span>
				</form>
			</li>
		</ul>
			
		<div class="" id="cms_tpv_working"><?php _e("Loading...", 'cms-tree-page-view') ?></div>
		
		<div class="updated below-h2 hidden" id="cms_tpv_search_no_hits"><p><?php _e("Search: no pages found", 'cms-tree-page-view') ?></p></div>
		
		<div id="cms_tpv_container" class="tree-default"><?php _e("Loading tree", 'cms-tree-page-view') ?></div>
		<div style="clear: both;"></div>
		<div id="cms_tpv_page_actions">
			<p>
				<a href="#" title='<?php _e("Edit page", "cms-tree-page-view")?>' class='cms_tpv_action_edit'><?php _e("Edit", "cms-tree-page-view")?></a> | 
				<a href="#" title='<?php _e("View page", "cms-tree-page-view")?>' class='cms_tpv_action_view'><?php _e("View", "cms-tree-page-view")?></a>
			</p>
			<p>
				<span class='cms_tpv_action_add_page'><?php _e("Add page", "cms-tree-page-view")?></span>
				<a href="#" title='<?php _e("Add new page after", "cms-tree-page-view")?>' class='cms_tpv_action_add_page_after'><?php _e("after", "cms-tree-page-view")?></a> | 
				<a href="#" title='<?php _e("Add new page inside", "cms-tree-page-view")?>' class='cms_tpv_action_add_page_inside'><?php _e("inside", "cms-tree-page-view")?></a>
			</ul>
			<dl>
				<dt><?php  _e("Last modified", "cms-tree-page-view") ?></dt>
				<dd><span id="cms_tpv_page_actions_modified_time"></span> <?php _e("by", "cms-tree-page-view") ?> <span id="cms_tpv_page_actions_modified_by"></span></dd>
				<dt><?php  _e("Page ID", "cms-tree-page-view") ?></dt>
				<dd><span id="cms_tpv_page_actions_page_id"></span></dd>
			</dl>
			<span id="cms_tpv_page_actions_arrow"></span>
		</div>
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
		cms_tpv_show_annoying_box();

		cms_tpv_print_common_tree_stuff();
		?>
	</div>
	<?php
}

/**
 * Stripped down code from get_pages. Modified to get drafts and some other stuff too.
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
	
	$where_post_type = $wpdb->prepare( "post_type = '%s' AND post_status <> '%s' AND post_status <> '%s' ", "page", "trash", "auto-draft");	
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
			$editLink = get_edit_post_link($onePage->ID, 'notDisplay');
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
			
			// modified time
			$post_modified_time = get_post_modified_time('U', false, $onePage, false);
			$post_modified_time =  date_i18n(get_option('date_format'), $post_modified_time, false);
			
			// last edited by
			global $post;
			$tmpPost = $post;
			$post = $onePage;
			$post_author = get_the_modified_author();
			if (empty($post_author)) {
				$post_author = __("Unknown user", 'cms-tree-page-view');
			}
			$post = $tmpPost;
			
			$title = get_the_title($onePage->ID); // so hooks and stuff will do their work
			if (empty($title)) {
				$title = __("<Untitled page>", 'cms-tree-page-view');
			}
			$title = wp_specialchars($title);
			#$title = html_entity_decode($title, ENT_COMPAT, "UTF-8");
			#$title = html_entity_decode($title, ENT_COMPAT);
			?>
			{
				"data": {
					"title": "<?php echo $title ?>",
					"attr": {
						"href": "<?php echo $editLink ?>",
						"xid": "cms-tpv-<?php echo $onePage->ID ?>"
					},
					"xicon": "<?php echo CMS_TPV_URL . "images/page_white_text.png" ?>"
				},
				"attr": {
					"xhref": "<?php echo $editLink ?>",
					"id": "cms-tpv-<?php echo $onePage->ID ?>",
					"xtitle": "<?php _e("Click to edit. Drag to move.", 'cms-tree-page-view') ?>"
				},
				<?php echo $strState ?>
				"metadata": {
					"id": "cms-tpv-<?php echo $onePage->ID ?>",
					"post_id": "<?php echo $onePage->ID ?>",
					"post_type": "<?php echo $onePage->post_type ?>",
					"post_status": "<?php echo $onePage->post_status ?>",
					"rel": "<?php echo $rel ?>",
					"childCount": <?php echo sizeof($arrChildPages) ?>,
					"permalink": "<?php echo htmlspecialchars_decode(get_permalink($onePage->ID)) ?>",
					"editlink": "<?php echo htmlspecialchars_decode($editLink) ?>",
					"modified_time": "<?php echo $post_modified_time ?>",
					"modified_author": "<?php echo $post_author ?>"
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

	header("Content-type: application/json");

	$action = $_GET["action"];
	$view = $_GET["view"]; // all | public
	$search = trim($_GET["search_string"]); // exits if we're doing a search
	if ($action) {
	
		if ($search) {
			
			// find all pages that contains $search
			// collect all post_parent
			// for each parent id traverse up until post_parent is 0, saving all ids on the way
			
			// what to search: since all we see in the GUI is the title, just search that
			global $wpdb;
			$sqlsearch = "%{$search}%";
			// feels bad to leave out the "'" in the query, but prepare seems to add it..??
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
			#foreach ($arrNodesToOpen as $oneNodeID) {
			#	$sReturn .= "cms-tpv-{$oneNodeID},";
			#}
			#$sReturn = preg_replace("/,$/", "", $sReturn);
			
			foreach ($arrNodesToOpen as $oneNodeID) {
				$sReturn .= "\"#cms-tpv-{$oneNodeID}\",";
			}
			$sReturn = preg_replace('/,$/', "", $sReturn);
			if ($sReturn) {
				$sReturn = "[" . $sReturn . "]";
			}
			
			if ($sReturn) {
				echo $sReturn;
			} else {
				// if no hits
				echo "[]";
			}

			exit;

		} else {	
			$id = $_GET["id"];
			$id = (int) str_replace("cms-tpv-", "", $id);

			$jstree_open = array();
			if ( isset( $_COOKIE["jstree_open"] ) ) {
				$jstree_open = $_COOKIE["jstree_open"]; // like this: [jstree_open] => cms-tpv-1282,cms-tpv-1284,cms-tpv-3
				#var_dump($jstree_open); string(22) "#cms-tpv-14,#cms-tpv-2"
				$jstree_open = explode( ",", $jstree_open );
				for( $i=0; $i<sizeof( $jstree_open ); $i++ ) {
					$jstree_open[$i] = (int) str_replace("#cms-tpv-", "", $jstree_open[$i]);
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
			// @todo: shouldn't menu order of existing items be changed?
			$post_to_save = array(
				"ID" => $post_node->ID,
				"menu_order" => 0,
				"post_parent" => $post_ref_node->ID
			);
			wp_update_post( $post_to_save );
			
			echo "did inside";
			
		} elseif ( "before" == $type ) {
		
			// post_node is placed before ref_post_node

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


/**
 * Show a box with some dontate-links and stuff
 */
function cms_tpv_show_annoying_box() {
	#update_option('cms_tpv_show_annoying_little_box', 1); // enable this to show box
	if ( "cms_tpv_remove_annoying_box" == $_GET["action"] ) {
		$show_box = 0;
		update_option('cms_tpv_show_annoying_little_box', $show_box);
	} else {
		$show_box = get_option('cms_tpv_show_annoying_little_box', 1);
	}
	if ($show_box) {
		?>
		<div id="cms_tpv_annoying_little_box">
			<p id="cms_tpv_annoying_little_box_close"><a href="<?php echo CMS_TPV_PAGE_FILE ?>&action=cms_tpv_remove_annoying_box">Close</a></p>
			<p><strong>Thank you for using my plugin!</strong> If you need help please check out the <a href="http://eskapism.se/code-playground/cms-tree-page-view/?utm_source=wordpress&utm_medium=banner&utm_campaign=promobox">plugin homepage</a> or the <a href="http://wordpress.org/tags/cms-tree-page-view?forum_id=10">support forum</a>.</p>
			<p>If you like this plugin, please <a href="http://eskapism.se/sida/donate/?utm_source=wordpress&utm_medium=banner&utm_campaign=promobox">support my work by donating</a> - or at least say something nice about this plugin in a blog post or tweet.</p>
			<!-- <p>Thank you</p>
			<p><img src="<?php echo CMS_TPV_URL ?>/images/signature.gif" alt="Pär Thernström's signature" /></p>
			<p>Pär Thernström
			<br /><a href="mailto:par.thernstrom@gmail.com">par.thernstrom@gmail.com</a>
			<br /><a href="twitter.com/eskapism">twitter.com/eskapism</a>
			</p> -->
		</div>
		<?php
	}
}


if (!function_exists("bonny_d")) {
function bonny_d($var) {
	echo "<pre>";
	print_r($var);
	echo "</pre>";
}
}

?>