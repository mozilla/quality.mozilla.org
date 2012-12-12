<?php

/**
 * Use the ajax action-thingie to catch our form with new pages
 * Add pages and then redirect to...?
 */
function cms_tpv_add_pages() {

	#sf_d($_POST);exit;
	/*
	Array
	(
	    [action] => cms_tpv_add_pages
	    [cms_tpv_add_new_pages_names] => Array
	        (
	            [0] => xxxxx
	            [1] => yyyy
	            [2] => 
	        )

	    [cms_tpv_add_type] => inside
	    [cms_tpv_add_status] => draft
	    [lang] => de
	)
	*/

	$post_position 	= $_POST["cms_tpv_add_type"];
	$post_status 	= $_POST["cms_tpv_add_status"];
	$post_names 	= (array) $_POST["cms_tpv_add_new_pages_names"];
	$ref_post_id	= (int) $_POST["ref_post_id"];
	$lang 			= $_POST["lang"];

	// If lang variable is set, then set some more wpml-related post/get-variables
	if ($lang) {
		// post seems to fix creating new posts in selcted lang
		$_POST["icl_post_language"] = $lang;
		// $_GET["lang"] = $lang;
	}

	// make sure the status is publish and nothing else (yes, perhaps I named it bad elsewhere)
	if ("published" === $post_status) $post_status = "publish";

	// remove possibly empty posts
	$arr_post_names = array();
	foreach ($post_names as $one_post_name) {
		if ( trim($one_post_name) ) {
			$arr_post_names[] = $one_post_name;
		}
	}

	$arr_post_names_count = sizeof($arr_post_names);
	
	// check that there are pages left
	if (empty($arr_post_names)) die("Error: no pages to add.");

	$ref_post = get_post($ref_post_id);
	if (NULL === $ref_post) die("Error: could not load reference post.");
	
	// Make room for our new pages
	// Get all pages at a level level and loop until our reference page
	// and then all pages after that one will get it's menu_order 
	// increased by the same number as the number of new posts we're gonna add
	
	$post_parent = 0;
	if ("after" === $post_position) {
		$post_parent = $ref_post->post_parent;
	} elseif ("inside" === $post_position) {
		$post_parent = $ref_post->ID;
	}


	/*
	perhaps for wpml:
	suppress_filters=0

	*/

	$args = array(
		"post_status" => "any",
		"post_type" => $ref_post->post_type,
		"numberposts" => -1,
		"offset" => 0,
		"orderby" => 'menu_order',
		'order' => 'asc',
		'post_parent' => $post_parent,
		"suppress_filters" => FALSE
	);
	//if ($lang) $args["lang"] = $lang;
	$posts = get_posts($args);

	#sf_d($_GET["lang"]);sf_d($args);sf_d($posts);exit;

	// If posts exist at this level, make room for our new pages by increasing the menu order
	if (sizeof($posts) > 0)  {

		if ("after" === $post_position) {
		
			$has_passed_ref_post = FALSE;
			foreach ($posts as $one_post) {
				
				if ($has_passed_ref_post) {
					
					$post_update = array(
						"ID" => $one_post->ID,
						"menu_order" => $one_post->menu_order + $arr_post_names_count
					);
					$return_id = wp_update_post($post_update);
					if (0 ===$return_id) die("Error: could not update post with id " . $post_update->ID);

				}

				if ( ! $has_passed_ref_post && $ref_post->ID === $one_post->ID) {
					$has_passed_ref_post = TRUE;
				}			

			}
			
			$new_menu_order = $ref_post->menu_order;

		}  elseif ("inside" === $post_position) {

			// in inside, place at beginning
			// so just get first post and use that menu order as base
			$new_menu_order = $posts[0]->menu_order - $arr_post_names_count;
			
		}


	} else {

		// no posts, start at 0
		$new_menu_order = 0;

	}

	$post_parent_id = NULL;
	if ("after" === $post_position) {
		$post_parent_id = $ref_post->post_parent;
	} elseif ("inside" === $post_position) {
		$post_parent_id = $ref_post->ID;
	}

	// Done maybe updating menu orders, add the new pages
	$arr_added_pages_ids = array();
	foreach ($arr_post_names as $one_new_post_name) {

		$new_menu_order++;
		$newpost_args = array(
			"menu_order" => $new_menu_order,
			"post_parent" => $post_parent_id,
			"post_status" => $post_status,
			"post_title" => $one_new_post_name,
			"post_type" => $ref_post->post_type
		);
		$new_post_id = wp_insert_post($newpost_args);

		if (0 === $new_post_id) {
			die("Error: could not add post");
		}

		$arr_added_pages_ids[] = $new_post_id;


	}

	// Done. Redirect to the first page created.
	$first_post_edit_link = get_edit_post_link($arr_added_pages_ids[0], "");
	wp_redirect($first_post_edit_link);

	exit;

}

// for debug, remember to comment out (yes.. i *know* i will forget this later on...)
// require("FirePHPCore/FirePHP.class.php");
// $firephp = FirePHP::getInstance(true);

function cms_tpv_admin_head() {

	if (!cms_tpv_is_one_of_our_pages()) return;

	global $cms_tpv_view;
	if (isset($_GET["cms_tpv_view"])) {
		$cms_tpv_view = htmlspecialchars($_GET["cms_tpv_view"]);
	} else {
		$cms_tpv_view = "all";
	}
	?>
	<script type="text/javascript">
		/* <![CDATA[ */
		var CMS_TPV_URL = "<?php echo CMS_TPV_URL ?>";
		var CMS_TPV_AJAXURL = "?action=cms_tpv_get_childs&view=";
		var CMS_TPV_VIEW = "<?php echo $cms_tpv_view ?>";
		var cms_tpv_jsondata = {};
		/* ]]> */
	</script>

    <!--[if IE 6]>
    	<style>
    		.cms_tree_view_search_form {
    			display: none !important;
    		}
			.cms_tpv_dashboard_widget .subsubsub li {
			}
    	</style>
    <![endif]-->
	<?php
}

/**
 * Detect if we are on a page that use CMS Tree Page View
 */
function cms_tpv_is_one_of_our_pages() {

	$options = cms_tpv_get_options();
	$post_type = cms_tpv_get_selected_post_type();
	$current_screen = get_current_screen(); # sf_d($current_screen);
	$is_plugin_page = FALSE;

	// Check if current page is one of the ones defined in $options["menu"]
	foreach ($options["menu"] as $one_post_type) {
		if ( strpos($current_screen->id, "_page_cms-tpv-page-{$one_post_type}") !== FALSE) {
			$is_plugin_page = TRUE;
			break;
		}
	}

	if ($current_screen->id === "settings_page_cms-tpv-options") {
		// Is settings page for plugin
		$is_plugin_page = TRUE;
	} elseif ($current_screen->id === "dashboard" && !empty($options["dashboard"])) {
		$is_plugin_page = TRUE;
	}

	return $is_plugin_page;

}

/**
 * Add styles and scripts to pages that use the plugin
 */
function cms_admin_enqueue_scripts() {


	if (cms_tpv_is_one_of_our_pages()) {

		// renamed from cookie to fix problems with mod_security
		wp_enqueue_script( "jquery-cookie", CMS_TPV_URL . "scripts/jquery.biscuit.js", array("jquery"));
		wp_enqueue_script( "jquery-ui-sortable");
		wp_enqueue_script( "jquery-jstree", CMS_TPV_URL . "scripts/jquery.jstree.js", false, CMS_TPV_VERSION);
		wp_enqueue_script( "jquery-alerts", CMS_TPV_URL . "scripts/jquery.alerts.js", false, CMS_TPV_VERSION);
		// wp_enqueue_script( "hoverIntent");
		wp_enqueue_script( "cms_tree_page_view", CMS_TPV_URL . "scripts/cms_tree_page_view.js", false, CMS_TPV_VERSION);	

		wp_enqueue_style( "cms_tpv_styles", CMS_TPV_URL . "styles/styles.css", false, CMS_TPV_VERSION );
		wp_enqueue_style( "jquery-alerts", CMS_TPV_URL . "styles/jquery.alerts.css", false, CMS_TPV_VERSION );

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
			"Can_not_add_sub_page_when_status_is_draft"  => __("Sorry, can't create a sub page to a page with status \"draft\".", 'cms-tree-page-view'),
			"Can_not_add_sub_page_when_status_is_trash"  => __("Sorry, can't create a sub page to a page with status \"trash\".", 'cms-tree-page-view'),
			"Can_not_add_page_after_when_status_is_trash"  => __("Sorry, can't create a page after a page with status \"trash\".", 'cms-tree-page-view'),
			"Add_new_page_inside"  => __("Add new page inside", 'cms-tree-page-view'),
			"Status_draft" => __("draft", 'cms-tree-page-view'),
			"Status_future" => __("future", 'cms-tree-page-view'),
			"Status_password" => __("protected", 'cms-tree-page-view'),	// is "protected" word better than "password" ?
			"Status_pending" => __("pending", 'cms-tree-page-view'),
			"Status_private" => __("private", 'cms-tree-page-view'),
			"Status_trash" => __("trash", 'cms-tree-page-view'),
			"Status_draft_ucase" => ucfirst( __("draft", 'cms-tree-page-view') ),
			"Status_future_ucase" => ucfirst( __("future", 'cms-tree-page-view') ),
			"Status_password_ucase" => ucfirst( __("protected", 'cms-tree-page-view') ),	// is "protected" word better than "password" ?
			"Status_pending_ucase" => ucfirst( __("pending", 'cms-tree-page-view') ),
			"Status_private_ucase" => ucfirst( __("private", 'cms-tree-page-view') ),
			"Status_trash_ucase" => ucfirst( __("trash", 'cms-tree-page-view') ),
			"Password_protected_page" => __("Password protected page", 'cms-tree-page-view'),
			"Adding_page" => __("Adding page...", 'cms-tree-page-view'),
			"Adding" => __("Adding ...", 'cms-tree-page-view'),
		);
		wp_localize_script( "cms_tree_page_view", 'cmstpv_l10n', $oLocale);

	}

}


function cms_tpv_admin_init() {
	
	// DEBUG
	//wp_enqueue_script( "jquery-hotkeys" );

	load_plugin_textdomain('cms-tree-page-view', WP_CONTENT_DIR . "/plugins/languages", "/cms-tree-page-view/languages");

	// add row to plugin page
	add_filter( 'plugin_row_meta', 'cms_tpv_set_plugin_row_meta', 10, 2 );

}

/**
 * Add settings link to plugin page
 * Hopefully this helps some people to find the settings page quicker
 */
function cms_tpv_set_plugin_row_meta($links, $file) {

	if ($file === "cms-tree-page-view/index.php") {
		return array_merge(
			$links,
			array( sprintf( '<a href="options-general.php?page=%s">%s</a>', "cms-tpv-options", __('Settings') ) )
		);
	}
	return $links;

}


// save settings
function cms_tpv_save_settings() {
	if (isset($_POST["cms_tpv_action"]) && $_POST["cms_tpv_action"] == "save_settings") {
		$options = array();
		$options["dashboard"] = (array) $_POST["post-type-dashboard"];
		$options["menu"] = (array) $_POST["post-type-menu"];
		update_option('cms_tpv_options', $options); // enable this to show box
	}
}

/**
 * Add widget to dashboard
 */
function cms_tpv_wp_dashboard_setup() {
	// add dashboard to capability edit_pages only
	if (current_user_can("edit_pages")) {
		$options = cms_tpv_get_options();
		foreach ($options["dashboard"] as $one_dashboard_post_type) {
			$post_type_object = get_post_type_object($one_dashboard_post_type);
			$new_func_name = create_function('', "cms_tpv_dashboard('$one_dashboard_post_type');");
			$widget_name = _x(sprintf('%1$s Tree', $post_type_object->labels->name), "name of dashboard", "cms-tree-page-view");
			wp_add_dashboard_widget( "cms_tpv_dashboard_widget_{$one_dashboard_post_type}", $widget_name, $new_func_name );
		}
	}
}


/**
 * Output on dashboard
 */
function cms_tpv_dashboard($post_type = "") {
	//cms_tpv_show_annoying_box();
	cms_tpv_print_common_tree_stuff($post_type);
}

function cms_tpv_admin_menu() {

	// add 
	$options = cms_tpv_get_options();
	foreach ($options["menu"] as $one_menu_post_type) {
		// post is a special one.
		if ($one_menu_post_type == "post") {
			$slug = "edit.php";
		} else {
			$slug = "edit.php?post_type=$one_menu_post_type";
		}
		$post_type_object = get_post_type_object($one_menu_post_type);
		
		$menu_name = _x("Tree View", "name in menu", "cms-tree-page-view");
		$page_title = _x(sprintf('%1$s Tree View', $post_type_object->labels->name), "title on page with tree", "cms-tree-page-view");
		add_submenu_page($slug, $page_title, $menu_name, $post_type_object->cap->edit_posts, "cms-tpv-page-$one_menu_post_type", "cms_tpv_pages_page");
	}

	add_submenu_page( 'options-general.php' , CMS_TPV_NAME, CMS_TPV_NAME, "administrator", "cms-tpv-options", "cms_tpv_options");
	
}


/**
 * Output options page
 */
function cms_tpv_options() {

	?>
	<div class="wrap">
	
		<?php cms_tpv_show_annoying_box(); ?>
		
		<h2><?php echo CMS_TPV_NAME ?> <?php _e("settings", 'cms-tree-page-view') ?></h2>

		<form method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
					
			<h3><?php _e("Select where to show a tree for pages and custom post types", 'cms-tree-page-view')?></h3>
			
			<?php
			$options = cms_tpv_get_options();

			$post_types = get_post_types(array(
				"show_ui" => TRUE
			), "objects");
			$arr_page_options = array();
			foreach ($post_types as $one_post_type) {
				$name = $one_post_type->name;
				
				if ($name == "post") {
					// no support for pages. you could show them.. but since we can't reorder them there is not idea to show them.. or..?
					// 14 jul 2011: ok, let's enable it for posts too. some people says it useful
					// http://wordpress.org/support/topic/this-plugin-should-work-also-on-posts
					// continue;
				}

				$arr_page_options[] = "post-type-dashboard-$name";
				$arr_page_options[] = "post-type-menu-$name";
				echo "<p>";
				echo "<strong>".$one_post_type->label."</strong>";
				
				$checked = (in_array($name, $options["dashboard"])) ? " checked='checked' " : "";
				echo "<br />";
				echo "<input $checked type='checkbox' name='post-type-dashboard[]' value='$name' id='post-type-dashboard-$name' /> <label for='post-type-dashboard-$name'>" . __("On dashboard", 'cms-tree-page-view') . "</label>";
				
				$checked = (in_array($name, $options["menu"])) ? " checked='checked' " : "";
				echo "<br />";
				echo "<input $checked type='checkbox' name='post-type-menu[]' value='$name' id='post-type-menu-$name' /> <label for='post-type-menu-$name'>" . __("In menu", 'cms-tree-page-view') . "</label>";
				echo "</p>";

			}

			?>
			
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="cms_tpv_action" value="save_settings" />
			<input type="hidden" name="page_options" value="<?php echo join($arr_page_options, ",") ?>" />
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cms-tree-page-view') ?>" />
			</p>
		</form>
	</div>
	
	<?php
}

function cms_tpv_get_options() {
	$arr_options = (array) get_option('cms_tpv_options');
	$arr_options["dashboard"] = (array) $arr_options["dashboard"];
	$arr_options["menu"] = (array) $arr_options["menu"];
	return $arr_options;
}

function cms_tpv_get_selected_post_type() {
	// fix for Ozh' Admin Drop Down Menu that does something with the urls
	// movies funkar:
	// http://localhost/wp-admin/edit.php?post_type=movies&page=cms-tpv-page-xmovies
	// movies funkar inte:
	// http://localhost/wp-admin/admin.php?page=cms-tpv-page-movies
	$post_type = NULL;
	if (isset($_GET["post_type"])) {
		$post_type = $_GET["post_type"];
	}
	if (!$post_type) {
		// no post type, happens with ozh admin drop down, so get it via page instead
		$page = isset($_GET["page"]) ? $_GET["page"] : "";
		$post_type = str_replace("cms-tpv-page-", "", $page);
	}
	
	if (!$post_type) { $post_type = "post"; }
	return $post_type;
}

/**
 * Determine if a post type is considered hierarchical
 */
function cms_tpv_is_post_type_hierarchical($post_type_object) {
	$is_hierarchical = $post_type_object->hierarchical;	
	// special case for posts, fake-support hierachical
	if ("post" == $post_type_object->name) {
		$is_hierarchical = true;
	}
	return $is_hierarchical;
}


function cms_tpv_get_wpml_post_counts($post_type) {

	global $wpdb;

	$arr_statuses = array("publish", "draft", "trash");
	$arr_counts = array();

	foreach ($arr_statuses as $post_status) {

		$extra_cond = "";
		if ($post_status){
			$extra_cond .= " AND post_status = '" . $post_status . "'";
		}
		if ($post_status != 'trash'){
			$extra_cond .= " AND post_status <> 'trash'";
		}
		$extra_cond .= " AND post_status <> 'auto-draft'";
		$sql = "
			SELECT language_code, COUNT(p.ID) AS c FROM {$wpdb->prefix}icl_translations t
			JOIN {$wpdb->posts} p ON t.element_id=p.ID
			JOIN {$wpdb->prefix}icl_languages l ON t.language_code=l.code AND l.active = 1
			WHERE p.post_type='{$post_type}' AND t.element_type='post_{$post_type}' {$extra_cond}
			GROUP BY language_code
		";
		$res = $wpdb->get_results($sql);

		$langs['all'] = 0;
		foreach($res as $r) {
			$langs[$r->language_code] = $r->c;
			$langs['all'] += $r->c;
		}

		$arr_counts[$post_status] = $langs;

	}

	return $arr_counts;

}


/**
 * Print tree stuff that is common for both dashboard and page
 */
function cms_tpv_print_common_tree_stuff($post_type = "") {

	global $sitepress, $cms_tpv_view, $wpdb;

	if (!$post_type) {
		$post_type = cms_tpv_get_selected_post_type();
	}
	#echo "post_type: $post_type";
	$post_type_object = get_post_type_object($post_type);
	$get_pages_args = array("post_type" => $post_type);

	$pages = cms_tpv_get_pages($get_pages_args);

	// check if wpml is active and if this post type is one of its enabled ones
	$wpml_current_lang = "";
	$wmpl_active_for_post = FALSE;
	if (defined("ICL_SITEPRESS_VERSION")) {

		$wpml_post_types = $sitepress->get_translatable_documents();
		if (array_key_exists($post_type, $wpml_post_types)) {
			$wmpl_active_for_post = TRUE;
			$wpml_current_lang = $sitepress->get_current_language();
		}
	
	}

	$status_data_attributes = array("all" => "", "publish" => "", "trash" => "");

	// Calculate post counts
	if ($wpml_current_lang) {

		// Count code for WPML, mostly taken/inspired from  WPML Multilingual CMS, sitepress.class.php
		$langs = array();
		
		$wpml_post_counts = cms_tpv_get_wpml_post_counts($post_type);
		
		$post_count_all = @$wpml_post_counts["publish"][$wpml_current_lang] + @$wpml_post_counts["draft"][$wpml_current_lang];
		$post_count_publish	= @$wpml_post_counts["publish"][$wpml_current_lang];
		$post_count_trash	= @$wpml_post_counts["trash"][$wpml_current_lang];
	
		foreach ($wpml_post_counts["publish"] as $one_wpml_lang => $one_wpml_lang_count) {
			if ("all" === $one_wpml_lang) continue;
			$lang_post_count_all 		= $wpml_post_counts["publish"][$one_wpml_lang] + $wpml_post_counts["draft"][$one_wpml_lang];
			$lang_post_count_publish	= $wpml_post_counts["publish"][$one_wpml_lang];
			$lang_post_count_trash		= $wpml_post_counts["trash"][$one_wpml_lang];
			$status_data_attributes["all"] 		.= " data-post-count-{$one_wpml_lang}='{$lang_post_count_all}' ";
			$status_data_attributes["publish"] 	.= " data-post-count-{$one_wpml_lang}='{$lang_post_count_publish}' ";
			$status_data_attributes["trash"] 	.= " data-post-count-{$one_wpml_lang}='{$lang_post_count_trash}' ";
		}

	} else {
		$post_count = wp_count_posts($post_type);
		$post_count_all = $post_count->publish + $post_count->future + $post_count->draft + $post_count->pending + $post_count->private;
		$post_count_publish = $post_count->publish;
		$post_count_trash = $post_count->trash;
	}

	// output js for the root/top level
	// function cms_tpv_print_childs($pageID, $view = "all", $arrOpenChilds = null, $post_type) {
	// @todo: make into function since used at other places
	$jstree_open = array();
	if ( isset( $_COOKIE["jstree_open"] ) ) {
		$jstree_open = $_COOKIE["jstree_open"]; // like this: [jstree_open] => cms-tpv-1282,cms-tpv-1284,cms-tpv-3
		$jstree_open = explode( ",", $jstree_open );
		for( $i=0; $i<sizeof( $jstree_open ); $i++ ) {
			$jstree_open[$i] = (int) str_replace("#cms-tpv-", "", $jstree_open[$i]);
		}
	}
	ob_start();
	cms_tpv_print_childs(0, $cms_tpv_view, $jstree_open, $post_type);
	$json_data = ob_get_clean();
	if (! $json_data) $json_data = '{}';
	?>
	<script type="text/javascript">
		cms_tpv_jsondata.<?php echo $post_type ?> = <?php echo $json_data ?>;
	</script>
	
	<div class="cms_tpv_wrapper">
		<input type="hidden" name="cms_tpv_meta_post_type" value="<?php echo $post_type ?>" />
		<input type="hidden" name="cms_tpv_meta_post_type_hierarchical" value="<?php echo (int) cms_tpv_is_post_type_hierarchical($post_type_object) ?>" />
		<input type="hidden" name="cms_tpv_meta_wpml_language" value="<?php echo $wpml_current_lang ?>" />
		<?php

		// check if WPML is activated and show a language-menu
		if ($wmpl_active_for_post) {

			$wpml_langs = icl_get_languages();
			$wpml_active_lang = null;
			if (sizeof($wpml_langs)>=1) {
				$lang_out = "";
				$lang_out .= "<ul class='cms-tpv-subsubsub cms_tvp_switch_langs'>";
				foreach ($wpml_langs as $one_lang) {
					$one_lang_details = $sitepress->get_language_details($one_lang["language_code"]); // english_name | display_name
					$selected = "";
					if ($one_lang["active"]) {
						$wpml_active_lang = $one_lang;
						$selected = "current";
					}

					$lang_count = @$wpml_post_counts["publish"][$one_lang["language_code"]] + @$wpml_post_counts["draft"][$one_lang["language_code"]];

					$lang_out .= "
						<li>
							<a class='cms_tvp_switch_lang $selected cms_tpv_switch_language_code_{$one_lang["language_code"]}' href='#'>
								$one_lang_details[display_name]
								<span class='count'>(" . $lang_count . ")</span>
							</a> | </li>";
				}
				$lang_out = preg_replace('/ \| <\/li>$/', "</li>", $lang_out);
				$lang_out .= "</ul>";
				echo $lang_out;
			}

		}

		if (empty($pages)) {
		
			echo '<div class="updated fade below-h2"><p>' . __("No posts found.", 'cms-tree-page-view') . '</p></div>';
		
		}

		if (true) {

			// start the party!

			?>
			<ul class="cms-tpv-subsubsub cms-tpv-subsubsub-select-view">
				<li class="cms_tvp_view_is_status_view">
					<a class="cms_tvp_view_all  <?php echo ($cms_tpv_view=="all") ? "current" : "" ?>" href="#" <?php echo $status_data_attributes["all"] ?>>
						<?php _e("All", 'cms-tree-page-view') ?>
						<span class="count">(<?php echo $post_count_all ?>)</span>
					</a> |
				</li>
				<li class="cms_tvp_view_is_status_view">
					<a class="cms_tvp_view_public <?php echo ($cms_tpv_view=="public") ? "current" : "" ?>" href="#" <?php echo $status_data_attributes["publish"] ?>>
						<?php _e("Public", 'cms-tree-page-view') ?>
						<span class="count">(<?php echo $post_count_publish ?>)</span>
					</a> |
				</li>
				<li class="cms_tvp_view_is_status_view">
					<a class="cms_tvp_view_trash <?php echo ($cms_tpv_view=="trash") ? "current" : "" ?>" href="#" <?php echo $status_data_attributes["trash"] ?>>
						<?php _e("Trash", 'cms-tree-page-view') ?>
						<span class="count">(<?php echo $post_count_trash ?>)</span>
					</a>
				</li>
	
				<?php
				if (cms_tpv_is_post_type_hierarchical($post_type_object)) {
					?>
					<li><a href="#" class="cms_tpv_open_all"><?php _e("Expand", 'cms-tree-page-view') ?></a> |</li>
					<li><a href="#" class="cms_tpv_close_all"><?php _e("Collapse", 'cms-tree-page-view') ?></a></li>
					<?php
				}
				?>

				<li>
					<form class="cms_tree_view_search_form" method="get" action="">
						<input type="text" name="search" class="cms_tree_view_search" />
						<a title="<?php _e("Clear search", 'cms-tree-page-view') ?>" class="cms_tree_view_search_form_reset" href="#">x</a>
						<input type="submit" class="cms_tree_view_search_submit" value="<?php _e("Search", 'cms-tree-page-view') ?>" />
						<span class="cms_tree_view_search_form_working"><?php _e("Searching...", 'cms-tree-page-view') ?></span>
						<span class="cms_tree_view_search_form_no_hits"><?php _e("Nothing found.", 'cms-tree-page-view') ?></span>
					</form>
				</li>

			</ul>
				
			<div class="cms_tpv_working">
				<?php _e("Loading...", 'cms-tree-page-view') ?>
			</div>

			<div class="cms_tpv_message updated below-h2 hidden"><p>Message goes here.</p></div>
			
			<div class="updated below-h2 hidden cms_tpv_search_no_hits"><p><?php _e("Search: no pages found", 'cms-tree-page-view') ?></p></div>
			
			<div class="cms_tpv_container tree-default">
				<?php _e("Loading tree", 'cms-tree-page-view') ?>
			</div>

			<div style="clear: both;"></div>

			<!-- template forpopup with actions -->
			<div class="cms_tpv_page_actions">

				<!-- cms_tpv_page_actions_page_id -->
				<h4 class="cms_tpv_page_actions_headline"></h4>

				<p class="cms_tpv_action_edit_and_view">
					<a href="#" title='<?php _e("Edit page", "cms-tree-page-view")?>' class='cms_tpv_action_edit'><?php _e("Edit", "cms-tree-page-view")?></a>
					<a href="#" title='<?php _e("View page", "cms-tree-page-view")?>' class='cms_tpv_action_view'><?php _e("View", "cms-tree-page-view")?></a>
				</p>

				<!-- links to add page -->
				<p class="cms_tpv_action_add_and_edit_page">

					<span class='cms_tpv_action_add_page'><?php echo $post_type_object->labels->add_new_item ?></span>
					
					<a href="#" title='<?php _e("Add new page after", "cms-tree-page-view")?>' class='cms_tpv_action_add_page_after'><?php _e("After", "cms-tree-page-view")?></a>
					
					<?php
					// if post type is hierarchical we can add pages inside
					if (cms_tpv_is_post_type_hierarchical($post_type_object)) {
						?><a href="#" title='<?php _e("Add new page inside", "cms-tree-page-view")?>' class='cms_tpv_action_add_page_inside'><?php _e("Inside", "cms-tree-page-view")?></a><?php
					}
					// if post status = draft then we can not add pages inside because wordpress currently can not keep its parent if we edit the page
					?>
					<!-- <span class="cms_tpv_action_add_page_inside_disallowed"><?php _e("Can not create page inside of a page with draft status", "cms-tree-page-view")?></span> -->

				</p>

				<div class="cms_tpv_action_add_doit">
					
					<form method="post" action="<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>">

						<input type="hidden" name="action" value="cms_tpv_add_pages">
						<input type="hidden" name="ref_post_id" value="">
						
						<!-- lang for wpml -->
						<input type="hidden" name="lang" value="">

						<!-- <fieldset> -->

							<h4><?php _e("Add page(s)", "cms-tree-page-view") ?></h4>

							<div>
								<!-- Pages<br> -->
								<ul class="cms_tpv_action_add_doit_pages">
									<li><span></span><input placeholder="<?php _e("Enter title here") ?>" type="text" name="cms_tpv_add_new_pages_names[]"></li>
								</ul>
							</div>

							<div>
								Position<br>
								<label><input type="radio" name="cms_tpv_add_type" value="after"> <?php _e("After", "cms-tree-page-view") ?></label>
								<label><input type="radio" name="cms_tpv_add_type" value="inside"> <?php _e("Inside", "cms-tree-page-view") ?></label>
							</div>


							<div>
								Status<br>
								<label><input type="radio" name="cms_tpv_add_status" value="draft" checked> <?php _e("Draft", "cms-tree-page-view") ?></label>
								<label><input type="radio" name="cms_tpv_add_status" value="published"> <?php _e("Published", "cms-tree-page-view") ?></label>
							</div>

							<div>
								<input type="submit" value="Add" class="button-primary">
								<?php _e("or", "cms-tree-page-view") ?>
								<a href="#" class="cms_tpv_add_cancel"><?php _e("cancel", "cms-tree-page-view") ?></a>
							</div>

						<!-- </fieldset> -->

					</form>

				</div>

				<dl>
					<dt><?php  _e("Last modified", "cms-tree-page-view") ?></dt>
					<dd>
						<span class="cms_tpv_page_actions_modified_time"></span> <?php _e("by", "cms-tree-page-view") ?> 
						<span class="cms_tpv_page_actions_modified_by"></span>
					</dd>
					<dt><?php  _e("Page ID", "cms-tree-page-view") ?></dt>
					<dd><span class="cms_tpv_page_actions_page_id"></span></dd>
				</dl>

				<div class="cms_tpv_page_actions_columns"></div>
				<span class="cms_tpv_page_actions_arrow"></span>
			</div>
			<?php
		}
		?>
		
	</div>
	<?php
} // func


/**
 * Pages page
 * A page with the tree. Good stuff.
 */
function cms_tpv_pages_page() {

	$post_type = cms_tpv_get_selected_post_type();
	$post_type_object = get_post_type_object($post_type);

	if ( 'post' != $post_type ) {
		$post_new_file = "post-new.php?post_type=$post_type";
	} else {
		$post_new_file = 'post-new.php';
	}
	
	?>
	<div class="wrap">
		<?php echo get_screen_icon(); ?>
		<h2><?php
			$page_title = _x(sprintf('%1$s Tree View', $post_type_object->labels->name), "headline of page with tree", "cms-tree-page-view");
			echo $page_title;

			// Add "add new" link the same way as the regular post page has
			if ( current_user_can( $post_type_object->cap->create_posts ) ) {
				echo ' <a href="' . esc_url( $post_new_file ) . '" class="add-new-h2">' . esc_html( $post_type_object->labels->add_new ) . '</a>';
			}




		?></h2>
		
		<?php
		/*
		Not so much nagging, since I'm a nice guy.
		<div class="bonny-plugins-inner-sidebar">
			<h3>Support the author</h3>
			<p>
				If you like this plugin you are welcome to support the author by donating:
			</p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="text-align: center">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>

			<p style="text-align: center">
				<a href="http://flattr.com/thing/117882/CMS-Tree-Page-View-Plugin-for-WordPress" target="_blank">
				<img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" /></a>
			</p>

			<p>
				You can also show your appreciation 
				by giving the plugin a <a href="http://wordpress.org/extend/plugins/cms-tree-page-view/">good rating over at the plugin directory</a>
				or blog or tweet something nice about this plugin.
			</p>

			<h3>Support</h3>
			<p>If you have any problems with this plugins please check out the <a href="http://wordpress.org/tags/cms-tree-page-view?forum_id=10">support forum</a>.</p>
			
			<h3>More CMS related plugins</h3>
			<p>These are some more plugins that enhance the CMS functionality of WordPress. Please check them out!</p>
			<ul>
				<li><a href="http://wordpress.org/extend/plugins/admin-menu-tree-page-view/">Admin Menu Tree Page View</a></li>
				<li><a href="http://wordpress.org/extend/plugins/simple-history/">Simple History</a></li>
				<li><a href="http://wordpress.org/extend/plugins/simple-fields/">Simple Fields</a></li>
				<li><a href="http://wordpress.org/extend/plugins/simple-front-end-edit-buttons/">Simple Front End Edit Buttons</a></li>
			</ul>
			
			
		</div>
		*/
		?>

		<?php
		// cms_tpv_show_annoying_box();
		cms_tpv_print_common_tree_stuff($post_type);
		?>

	</div>
	<?php
}

/**
 * Get the pages
 */
function cms_tpv_get_pages($args = null) {

	global $wpdb;

    $defaults = array(
    	"post_type" => "post",
		"parent" => "",
		"view" => "all" // all | public | trash
    );
    $r = wp_parse_args( $args, $defaults );

	$get_posts_args = array(
		"numberposts" => "-1",
		"orderby" => "menu_order",
		"order" => "ASC",
		// "caller_get_posts" => 1, // get sticky posts in natural order (or so I understand it anyway). Deprecated since 3.1
		"ignore_sticky_posts" => 1,
		// "post_type" => "any",
		"post_type" => $r["post_type"],
		"xsuppress_filters" => "0"
	);
	if ($r["parent"]) {
		$get_posts_args["post_parent"] = $r["parent"];
	} else {
		$get_posts_args["post_parent"] = "0";
	}
	if ($r["view"] == "all") {
		$get_posts_args["post_status"] = "any"; // "any" seems to get all but auto-drafts
	} elseif ($r["view"] == "trash") {
		
		$get_posts_args["post_status"] = "trash";

		// if getting trash, just get all pages, don't care about parent?
		// because otherwise we have to mix trashed pages and pages with other statuses. messy.
		$get_posts_args["post_parent"] = null;
		
	} else {
		$get_posts_args["post_status"] = "publish";
	}

	// does not work with plugin role scoper. don't know why, but this should fix it
	remove_action("get_pages", array('ScoperHardway', 'flt_get_pages'), 1, 2);

	// does not work with plugin ALO EasyMail Newsletter
	remove_filter('get_pages','ALO_exclude_page');
	
	#do_action_ref_array('parse_query', array(&$this));
	#print_r($get_posts_args);
	$pages = get_posts($get_posts_args);

	// filter out pages for wpml, by applying same filter as get_pages does
	// only run if wpml is available or always?
	$pages = apply_filters('get_pages', $pages, $get_posts_args);
	
	return $pages;

}

function cms_tpv_parse_query($q) {
}

function cms_tpv_firedebug($var) {
	global $firephp;
	$firephp->log($var);
}

/**
 * Output JSON for the children of a node
 * $arrOpenChilds = array with id of pages to open children on
 */
function cms_tpv_print_childs($pageID, $view = "all", $arrOpenChilds = null, $post_type) {

	$arrPages = cms_tpv_get_pages("parent=$pageID&view=$view&post_type=$post_type");

	if ($arrPages) {
	
		global $current_screen;
		$screen = convert_to_screen("edit");
		$screen->post_type = null;

		ob_start(); // some plugins, for example magic fields, return javascript and things here. we're not compatible with that, so just swallow any output
		$posts_columns = get_column_headers($screen);
		ob_get_clean();

		unset($posts_columns["cb"], $posts_columns["title"], $posts_columns["author"], $posts_columns["categories"], $posts_columns["tags"], $posts_columns["date"]);

		global $post;
		
		// Translated post statuses
		$post_statuses = get_post_statuses();

		#cms_tpv_firedebug(timer_stop());
		
		?>[<?php
		for ($i=0, $pagesCount = sizeof($arrPages); $i<$pagesCount; $i++) {
	
			#cms_tpv_firedebug(timer_stop());
			$onePage = $arrPages[$i];
			$tmpPost = $post;
			$post = $onePage;
			$page_id = $onePage->ID;
			$arrChildPages = NULL;

			$editLink = get_edit_post_link($onePage->ID, 'notDisplay');
			$content = esc_html($onePage->post_content);
			$content = str_replace(array("\n","\r"), "", $content);
			$hasChildren = false;
			
			// if viewing trash, don't get children. we watch them "flat" instead
			if ($view == "trash") {
			} else {
				$arrChildPages = cms_tpv_get_pages("parent={$onePage->ID}&view=$view&post_type=$post_type");
			}

			if ( !empty($arrChildPages) ) {
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
			$post_modified_time = strtotime($onePage->post_modified);
			$post_modified_time =  date_i18n(get_option('date_format'), $post_modified_time, false);

			// last edited by
			setup_postdata($post);
			$post_author = get_the_modified_author();
			if (empty($post_author)) {
				$post_author = __("Unknown user", 'cms-tree-page-view');
			}
			
			$title = get_the_title($onePage->ID); // so hooks and stuff will do their work
			if (empty($title)) {
				$title = __("<Untitled page>", 'cms-tree-page-view');
			}
			$title = esc_html($title);
			#$title = html_entity_decode($title, ENT_COMPAT, "UTF-8");
			#$title = html_entity_decode($title, ENT_COMPAT);

			// can edit?
			if ( current_user_can( 'edit_page', $page_id ) ) {
				$user_can_edit_page = true;
				$user_can_edit_page_css = "cms_tpv_user_can_edit_page_yes";
			} else {
				$user_can_edit_page = false;
				$user_can_edit_page_css = "cms_tpv_user_can_edit_page_no";
			}

			// fetch columns
			$str_columns = "";
			foreach ( $posts_columns as $column_name => $column_display_name ) {
				$col_name = $column_display_name;
				if ($column_name == "comments") {
					$col_name = __("Comments");
				}
				$str_columns .= "<dt>$col_name</dt>";
				$str_columns .= "<dd>";
				if ($column_name == "comments") {
					$str_columns .= '<div class="post-com-count-wrapper">';
					$left = get_pending_comments_num( $onePage->ID );
					$pending_phrase = sprintf( __('%s pending'), number_format( $left ) );
					$pending_phrase2 = "";
					if ($left) {
						$pending_phrase2 = " + $left " . __("pending");
					}

					if ( $left ) {
						$str_columns .= '<strong>';
					}
					ob_start();
					comments_number("<a href='edit-comments.php?p=$page_id' title='$pending_phrase'><span>" . _x('0', 'comment count') . "$pending_phrase2</span></a>", "<a href='edit-comments.php?p=$page_id' title='$pending_phrase' class=''><span class=''>" . _x('1', 'comment count') . "$pending_phrase2</span></a>", "<a href='edit-comments.php?p=$page_id' title='$pending_phrase' class=''><span class=''>" . _x('%', 'comment count') . "$pending_phrase2</span></a>");
					$str_columns .= ob_get_clean();
					if ( $left ) {
						$str_columns .=  '</strong>';
					}
					$str_columns .= "</div>";
				} else {
					ob_start();
					do_action('manage_pages_custom_column', $column_name, $onePage->ID);
					$str_columns .= ob_get_clean();
				}
				$str_columns .= "</dd>";
			}

			if ($str_columns) {
				$str_columns = "<dl>$str_columns</dl>";
			}
			$str_columns = json_encode($str_columns);
			?>
			{
				"data": {
					"title": "<?php echo $title ?>",
					"attr": {
						"href": "<?php echo $editLink ?>"
						<?php /* , "xid": "cms-tpv-<?php echo $onePage->ID ?>" */ ?>
					}<?php /*,
					"xicon": "<?php echo CMS_TPV_URL . "images/page_white_text.png" ?>"*/?>
				},
				"attr": {
					<?php /* "xhref": "<?php echo $editLink ?>", */ ?>
					"id": "cms-tpv-<?php echo $onePage->ID ?>",
					<?php /* "xtitle": "<?php _e("Click to edit. Drag to move.", 'cms-tree-page-view') ?>", */ ?>
					"class": "<?php echo $user_can_edit_page_css ?>"
				},
				<?php echo $strState ?>
				"metadata": {
					"id": "cms-tpv-<?php echo $onePage->ID ?>",
					"post_id": "<?php echo $onePage->ID ?>",
					"post_type": "<?php echo $onePage->post_type ?>",
					"post_status": "<?php echo $onePage->post_status ?>",
					"post_status_translated": "<?php echo isset($post_statuses[$onePage->post_status]) ? $post_statuses[$onePage->post_status] : $onePage->post_status  ?>",
					"rel": "<?php echo $rel ?>",
					"childCount": <?php echo ( !empty( $arrChildPages ) ) ? sizeof( $arrChildPages ) : 0 ; ?>,
					"permalink": "<?php echo htmlspecialchars_decode(get_permalink($onePage->ID)) ?>",
					"editlink": "<?php echo htmlspecialchars_decode($editLink) ?>",
					"modified_time": "<?php echo $post_modified_time ?>",
					"modified_author": "<?php echo $post_author ?>",
					"columns": <?php echo $str_columns ?>,
					"user_can_edit_page": "<?php echo (int) $user_can_edit_page ?>",
					"post_title": "<?php echo $title ?>"
				}
				<?php
				// if id is in $arrOpenChilds then also output children on this one
				// TODO: if only "a few" (< 100?) pages then load all, but keep closed, so we don't have to do the ajax thingie
				if ($hasChildren && isset($arrOpenChilds) && in_array($onePage->ID, $arrOpenChilds)) {
					?>, "children": <?php
					cms_tpv_print_childs($onePage->ID, $view, $arrOpenChilds, $post_type);
					?><?php
				}
				?>

			}
			<?php
			// no comma for last page
			if ($i < $pagesCount-1) {
				?>,<?php
			}
			
			// return orgiginal post
			$post = $tmpPost;
			
		}
		?>]<?php
	}
}

// Act on AJAX-call
function cms_tpv_get_childs() {

	header("Content-type: application/json");

	$action = $_GET["action"];
	$view = $_GET["view"]; // all | public | trash
	$post_type = (isset($_GET["post_type"])) ? $_GET["post_type"] : null;
	$search = (isset($_GET["search_string"])) ? trim($_GET["search_string"]) : ""; // exits if we're doing a search
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
		
			// regular get

			$id = (isset($_GET["id"])) ? $_GET["id"] : null;
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
			cms_tpv_print_childs($id, $view, $jstree_open, $post_type);
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
	$post_type = $_POST["post_type"];
	$wpml_lang = $_POST["wpml_lang"];
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
		$post_new["post_type"] = $post_type;
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
		$post_new["post_type"] = $post_type;
		$newPostID = wp_insert_post($post_new);

	}

	if ($newPostID) {
		// return editlink for the newly created page
		$editLink = get_edit_post_link($newPostID, '');
		if ($wpml_lang) {
			$editLink = add_query_arg("lang", $wpml_lang, $editLink);
		}
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
	
	$_POST["skip_sitepress_actions"] = true; // sitepress.class.php->save_post_actions
	
	if ($node_id && $ref_node_id) {
		#echo "\nnode_id: $node_id";
		#echo "\ntype: $type";	
		
		$post_node = get_post($node_id);
		$post_ref_node = get_post($ref_node_id);
		
		// first check that post_node (moved post) is not in trash. we do not move them
		if ($post_node->post_status == "trash") {
			exit;
		}

		if ( "inside" == $type ) {
			
			// post_node is moved inside ref_post_node
			// add ref_post_node as parent to post_node and set post_nodes menu_order to 0
			// @todo: shouldn't menu order of existing items be changed?
			$post_to_save = array(
				"ID" => $post_node->ID,
				"menu_order" => 0,
				"post_parent" => $post_ref_node->ID,
				"post_type" => $post_ref_node->post_type
			);
			wp_update_post( $post_to_save );
			
			echo "did inside";
			
		} elseif ( "before" == $type ) {
		
			// post_node is placed before ref_post_node
			// update menu_order of all pages with a menu order more than or equal ref_node_post and with the same parent as ref_node_post
			// we do this so there will be room for our page if it's the first page
			// so: no move of individial posts yet
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET menu_order = menu_order+1 WHERE post_parent = %d", $post_ref_node->post_parent ) );

			// update menu order with +1 for all pages below ref_node, this should fix the problem with "unmovable" pages because of
			// multiple pages with the same menu order (...which is not the fault of this plugin!)
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET menu_order = menu_order+1 WHERE menu_order >= %d", $post_ref_node->menu_order+1) );
			
			$post_to_save = array(
				"ID" => $post_node->ID,
				"menu_order" => $post_ref_node->menu_order,
				"post_parent" => $post_ref_node->post_parent,
				"post_type" => $post_ref_node->post_type
			);
			wp_update_post( $post_to_save );

			echo "did before";

		} elseif ( "after" == $type ) {
		
			// post_node is placed after ref_post_node
			
			// update menu_order of all posts with the same parent ref_post_node and with a menu_order of the same as ref_post_node, but do not include ref_post_node
			// +2 since multiple can have same menu order and we want our moved post to have a unique "spot"
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET menu_order = menu_order+2 WHERE post_parent = %d AND menu_order >= %d AND id <> %d ", $post_ref_node->post_parent, $post_ref_node->menu_order, $post_ref_node->ID ) );

			// update menu_order of post_node to the same that ref_post_node_had+1
			#$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET menu_order = %d, post_parent = %d WHERE ID = %d", $post_ref_node->menu_order+1, $post_ref_node->post_parent, $post_node->ID ) );

			$post_to_save = array(
				"ID" => $post_node->ID,
				"menu_order" => $post_ref_node->menu_order+1,
				"post_parent" => $post_ref_node->post_parent,
				"post_type" => $post_ref_node->post_type
			);
			wp_update_post( $post_to_save );
			
			echo "did after";
		}
		
		#echo "ok"; // I'm done here!
		
	} else {
		// error
	}
	
	// ok, we have updated the order of the pages
	// but we must tell wordpress that we have done something
	// other plugins (cache plugins) will not know to clear the cache otherwise
	// edit_post seems like the most appropriate action to fire
	// fire for the page that was moved? can not fire for all.. would be crazy, right?
	#wp_update_post(array("ID" => $node_id));
	#wp_update_post(array("ID" => $post_ref_node));
	#clean_page_cache($node_id); clean_page_cache($post_ref_node); // hmpf.. db cache reloaded don't care
	
	
	exit;
}


/**
 * Show a box with some dontate-links and stuff
 */
function cms_tpv_show_annoying_box() {
	
	// update_option('cms_tpv_show_annoying_little_box', 1); // enable this to show box while testing

	if ( isset($_GET["action"]) && "cms_tpv_remove_annoying_box" == $_GET["action"] ) {
		$show_box = 0;
		update_option('cms_tpv_show_annoying_little_box', $show_box);
	} else {
		$show_box = get_option('cms_tpv_show_annoying_little_box', 1);
	}
	if ($show_box) {
		?>
		<div class="cms_tpv_annoying_little_box">

			<h3><?php _e('Thanks for using my plugin', 'cms-tree-page-view') ?></h3>
			<p class="cms_tpv_annoying_little_box_gravatar"><a href="https://twitter.com/eskapism"><?php echo get_avatar("par.thernstrom@gmail.com", '64'); ?></a></p>
			<p><?php _e('Hi there! I just wanna says thanks for using my plugin. I hope you like it as much as I do.', 'cms-tree-page-view') ?></p>
			<p class="cms_tpv_annoying_little_box_author"><a href="https://twitter.com/eskapism"><?php _e('/Pär Thernström - plugin creator', 'cms-tree-page-view') ?></a></p>

			<h3><?php _e('I like this plugin<br>– how can I thank you?', 'cms-tree-page-view') ?></h3>
			<p><?php _e('There are serveral ways for you to show your appreciation:', 'cms-tree-page-view') ?></p>
			<ul>
				<li><?php printf(__('<a href="%1$s">Give it a nice review</a> over at the WordPress Plugin Directory', 'cms-tree-page-view'), "http://wordpress.org/support/view/plugin-reviews/cms-tree-page-view") ?></li>
				<li><?php printf(__('<a href="%1$s">Give a donation</a> – any amount will make me happy', 'cms-tree-page-view'), "http://eskapism.se/sida/donate/?utm_source=wordpress&utm_medium=banner&utm_campaign=promobox") ?></li>
				<li><?php printf(__('<a href="%1$s">Post a nice tweet</a> or make a nice blog post about the plugin', 'cms-tree-page-view'), "https://twitter.com/intent/tweet?text=I really like the CMS Tree Page View plugin for WordPress http://wordpress.org/extend/plugins/cms-tree-page-view/") ?></li>
			</ul>

			<h3><?php _e('Support', 'cms-tree-page-view') ?></h3>
			<p><?php printf(__('Plese see the <a href="%1$s">support forum</a> for help.', 'cms-tree-page-view'), "http://wordpress.org/support/plugin/cms-tree-page-view") ?></p>

			<p class="cms_tpv_annoying_little_box_close">
				<a href="<?php echo add_query_arg("action", "cms_tpv_remove_annoying_box")?>">
					<?php _e("Hide until next upgrade", 'cms-tree-page-view') ?>
				</a>
			</p>
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


function cms_tpv_install() {

	// after upgrading/re-enabling the plugin, also re-enable the little please-donate-box
	update_option('cms_tpv_show_annoying_little_box', 1);
	
	// first install or pre custom posts version:
	// make sure pages are enabled by default
	// run on admin_init so most themes and plugins have time to setup their things. late prio too.
	add_action("admin_init", "cms_tpv_setup_defaults", 999);

	// set to current version
	update_option('cms_tpv_version', CMS_TPV_VERSION);
}

/**
 * setup some defaults
 */
function cms_tpv_setup_defaults() {

	// check and update version
	$version = get_option('cms_tpv_version', 0);

	//$version = 0;

	if ($version <= 0) {

		$options = array();

		// Add pages to both dashboard and menu
		$options["dashboard"] = array("page");
		$options["menu"] = array("page");

		// since 0.10.1 enable menu for all hierarchical custom post types
		$post_types = get_post_types(array(
			"show_ui" 		=> TRUE,
			"hierarchical" 	=> TRUE
		), "objects");

		foreach ($post_types as $one_post_type) {
			$options["menu"][] = $one_post_type->name;
		}

		$options["menu"] = array_unique($options["menu"]);

		update_option('cms_tpv_options', $options);
	}

}

// when plugins are loaded, check if current plugin version is same as stored
// if not = it's an upgrade. right?
function cms_tpv_plugins_loaded($a) {
	$installed_version = get_option('cms_tpv_version', 0);
	#echo "installed_version: $installed_version";
	#echo "<br>" . CMS_TPV_VERSION;
	if ($installed_version != CMS_TPV_VERSION) {
		// new version!
		// upgrade stored version to current version + show that annoying litte box again
		update_option('cms_tpv_version', CMS_TPV_VERSION);	
		update_option('cms_tpv_show_annoying_little_box', 1);
	}

}
