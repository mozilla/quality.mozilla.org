<?php

$wp_abspath = $_GET["wp-abspath"];
define('WP_USE_THEMES', false);
require($wp_abspath.'/wp-blog-header.php');
load_plugin_textdomain('cms-tree-page-view', WP_CONTENT_DIR . "/plugins/languages", "/cms-tree-page-view/languages");

?>

// Case Insensitive :contains()
// Source: http://ericphan.info/blog/2009/3/4/jquery-13-case-insensitive-contains.html
jQuery.extend(jQuery.expr[":"], {
    "containsNC": function(elem, i, match, array) {
        return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
    }
});

// check if tree is beging dragged
function cms_tpv_is_dragging() {
	return (jQuery("#jstree-dragged").length == 1) ? true : false;
}

// mouse over, show actions
jQuery(".tree li a").live("mouseover", function() {
	$t = jQuery(this);
	$actions = $t.find(".cms_tpv_action_view, .cms_tpv_action_edit, .cms_tpv_action_add_page, .cms_tpv_action_add_page_after, .cms_tpv_action_add_page_inside");
	if (cms_tpv_is_dragging() == false) {
		$actions.show();
		// remove possible timeoutID
		/*
		var timeoutID = $t.data("timeoutID");
		if (timeoutID) {
			clearTimeout(timeoutID);
			$t.data("timeoutID", null);
		}
		*/
	}
});
// ..and hide them again
// @todo: hide after a short delay. fitts law stuff
jQuery(".tree li a").live("mouseout", function() {
	$t = jQuery(this);
	$actions = $t.find(".cms_tpv_action_view, .cms_tpv_action_edit, .cms_tpv_action_add_page, .cms_tpv_action_add_page_after, .cms_tpv_action_add_page_inside");
	$actions.hide();
	/*
	var func = test($actions);
	var timeoutID = setTimeout(func, 500);
	$t.data("timeoutID", timeoutID);
	*/
});


jQuery(".tree li .cms_tpv_action_view").live("mouseover", function() {
	return true;
});
jQuery(".tree li .cms_tpv_action_view").live("click", function() {
	return true;
});



// go to page on click
jQuery(".tree li a .cms_tpv_action_view").live("click", function() {
	var $li = jQuery(this).closest("li");
	var permalink = $li.attr("permalink");
	if (permalink) {
		document.location = permalink;
	}
	return false;
});
// edit page on click
jQuery(".tree li a .cms_tpv_action_edit").live("click", function() {
	var $a = jQuery(this).closest("a");
	var editlink = $a.attr("href");
	if (editlink) {
		document.location = editlink;
	}
	return false;
});

// add page after
jQuery(".tree li a .cms_tpv_action_add_page_after").live("click", function() {
	var new_page_title = prompt("<?php _e("Enter title of new page", 'cms-tree-page-view') ?>", "");
	if (new_page_title) {
		var pageID = jQuery(this).closest("li").attr("id");
		jQuery.post(ajaxurl, {
			action: "cms_tpv_add_page",
			pageID: pageID,
			type: "after",
			page_title: new_page_title
		}, function(data, textStatus) {
			document.location = data;
		});
	}
	return false;
});

// add page inside
jQuery(".tree li a .cms_tpv_action_add_page_inside").live("click", function() {
	var new_page_title = prompt("<?php _e("Enter title of new page", 'cms-tree-page-view') ?>", "");
	if (new_page_title) {
		var pageID = jQuery(this).closest("li").attr("id");
		jQuery.post(ajaxurl, {
			action: "cms_tpv_add_page",
			pageID: pageID,
			type: "inside",
			page_title: new_page_title
		}, function(data, textStatus) {
			document.location = data;
		});
	}
	return false;
});


// hide action links on drag
jQuery.tree.drag_start = function() {
	jQuery(".cms_tpv_action_view, .cms_tpv_action_edit, .cms_tpv_action_add_page, .cms_tpv_action_add_page_after, .cms_tpv_action_add_page_inside").hide();
}

var cms_tpv_tree;
jQuery(function($) {

	var treeOptions = {
		data: {
			async: true,
			type: "json",
			opts: {
				url: ajaxurl + CMS_TPV_AJAXURL + CMS_TPV_VIEW
			}
		},
		ui: {
			theme_path: CMS_TPV_URL + "scripts/themes/default/style.css", // this setting seems a bit bananas. be sure to check that it works when jstree 1.0 is released
			theme_name: "default",
			animation: 200
		},
		plugins: { 
			cookie: {
				prefix: "jstree_",
				types : {
					selected: false
				}
			}
		},
		types: {
			"default": {
				icon: {
					image: CMS_TPV_URL + "images/page_white_text.png"
				}
			},
			"draft": {
			},
			"pending": {
			},
			"password": {
			},
			"future": {
			}
		},
		callback: {
			// data = array of objects
			ondata: function(DATA, TREE_OBJ) {
			
				jQuery.each(DATA, function(index, value) {
					
					// add number of children and page type and span-actions
					cms_tree_page_view_add_spans_to_tree_ondata(DATA[index]);

				});
				return DATA;
			},

			// select = go and edit
			// hm..or not...let me think about this... use actions-popup for now
			// @todo: check if jquery live's click and dblclick can make 1 click = edit, 2 clicks = open
			// @todo: currently this swallows to many clicks (on links outside the tree for example). 
			// newer version of jquery + next version of jstree is supposed to fix this
			onselect: function(NODE, TREE_OBJ) {
				$selected = $(cms_tpv_tree.selected);
				
				// jquery < 1.4 has problems
				//if (parseFloat(jQuery.fn.jquery)>=1.4) {
					// @todo: 1.4 gives us problems too... fix later when jstree v1 is released
					// live on the action-links does not work here...
					//return true;
				//} else {
					var editLink = $selected.find("a").attr("href");
					document.location = editLink;
				//}
			},
			
			onmove: function(NODE, REF_NODE, TYPE, TREE_OBJ, RB) {
				// get ids of our friends
				var node_id = $( NODE ).attr( "id" );
				ref_node_id = $( REF_NODE ).attr( "id" );
				
				// Update parent or menu order
				$.post(ajaxurl, {
					action: "cms_tpv_move_page", 
					"node_id": node_id, 
					"ref_node_id": ref_node_id, 
					type: TYPE 
				}, function(data, textStatus) {
				});
				
			},
			
			onsearch: function(NODES, TREE_OBJ) {
				// if empy nodes = no hits
				if (NODES.length == 0) {
					$("#cms_tpv_search_no_hits").show();
				}
				NODES.addClass("search");
				$("#cms_tree_view_search_form_working").fadeOut("fast");
			}

		}
	}

	if ($("#cms_tpv_container").length == 1) {
		$("#cms_tpv_container").tree(treeOptions);
		cms_tpv_tree = jQuery.tree.reference("#cms_tpv_container");
	}

	jQuery("#cms_tree_view_search_form").submit(function() {
		$("#cms_tpv_search_no_hits").hide();
		var s = jQuery("#cms_tree_view_search").attr("value");
		s = jQuery.trim( s );
		// search, oh the mighty search!
		if (s) {
			$("#cms_tree_view_search_form_working").fadeIn("fast");
		}
		cms_tpv_tree.search(s, "containsNC");
		return false;
	});


	// open/close links
	jQuery("#cms_tpv_open_all").click(function() {
		cms_tpv_tree.open_all();
		return false;
	});
	jQuery("#cms_tpv_close_all").click(function() {
		cms_tpv_tree.close_all();
		return false;
	});

	// view all or public
	jQuery("#cms_tvp_view_all").click(function() {
		cms_tvp_set_view("all");
		jQuery(this).addClass("current");
		return false;
	});
	jQuery("#cms_tvp_view_public").click(function() {
		cms_tvp_set_view("public");
		jQuery(this).addClass("current");
		return false;
	});
	
	function cms_tvp_set_view(view) {
		jQuery("#cms_tpv_working").fadeIn("slow");
		jQuery("#cms_tvp_view_all,#cms_tvp_view_public").removeClass("current");
		cms_tpv_tree.settings.data.opts.url = ajaxurl + CMS_TPV_AJAXURL + view;
		cms_tpv_tree.refresh();
		jQuery("#cms_tpv_working").fadeOut("slow");
	}


}); // end ondomready


/**
 * Add type, children count and span-actions
 * data is one DATA[index]
 */
function cms_tree_page_view_add_spans_to_tree_ondata(data) {

	var childCount = data.attributes.childCount;
	if (childCount > 0) {
		data.data.title += "<span title='" + childCount + " <?php _e("child pages", 'cms-tree-page-view') ?>" + "' class='child_count'>("+childCount+")</span>";
	}
	
	// add page type
	var rel = data.attributes.rel;
	if (rel != "publish") {
		data.data.title = "<span class='post_type post_type_"+rel+"'>"+rel+"</span>" + data.data.title;
	}

	// add actions that are revealed on mouse over
	data.data.title += " <span title='<?php _e("Edit page", 'cms-tree-page-view') ?>' class='cms_tpv_action_edit'><?php _e("Edit", 'cms-tree-page-view') ?></span>";
	data.data.title += " <span title='<?php _e("View page", 'cms-tree-page-view') ?>' class='cms_tpv_action_view'><?php _e("View", 'cms-tree-page-view') ?></span>";

	data.data.title += " <span class='cms_tpv_action_add_page'><?php _e("Add page", 'cms-tree-page-view') ?>:</span>";
	data.data.title += " <span title='<?php _e("Add new page after", 'cms-tree-page-view') ?>' class='cms_tpv_action_add_page_after'><?php _e("after", 'cms-tree-page-view') ?></span> ";
	data.data.title += " <span title='<?php _e("Add new page inside", 'cms-tree-page-view') ?>' class='cms_tpv_action_add_page_inside'><?php _e("inside", 'cms-tree-page-view') ?></span>";
	
	// check if children exists. id they do: update their data too
	// DATA[index][children] is an array that may exists. in that case we must do this on all kids to...
	if (data.children) {
		jQuery.each(data.children, function(index, value) {
			cms_tree_page_view_add_spans_to_tree_ondata(data.children[index]);
		});
		
	}
	
	return data;
}
