
// @todo: add prefix to treeOptions, div_actions
var cms_tpv_tree, treeOptions, div_actions, cms_tpv_current_li_id = null;
jQuery(function($) {
	
	cms_tpv_tree = $(".cms_tpv_container");
	div_actions = $(".cms_tpv_page_actions");

	treeOptions = {
		xplugins: ["cookie","ui","crrm","themes","json_data","search","types","dnd"],
		plugins: ["themes","json_data","cookies","search","dnd", "types"],
		core: {
			"html_titles": true
		},
		"json_data": {
			"ajax": {
				"url": ajaxurl + CMS_TPV_AJAXURL + CMS_TPV_VIEW,
				// this function is executed in the instance's scope (this refers to the tree instance)
				// the parameter is the node being loaded (may be -1, 0, or undefined when loading the root nodes)
				"data" : function (n) { 
					// the result is fed to the AJAX request `data` option
					if (n.data) {
						var post_id = n.data("post_id");
						return {
							"id": post_id
						}
					}
				}

			}
			/*
			// data can be initially set like this
			// but it has to be set by type...
			"data": [{
				"data": {
					"title": "I am a new page",
					"attr": {
						"href": "http://localhost/wp-admin/post.php?post=1060&action=edit",
						"xid": "cms-tpv-1060"
					},
					"xicon": "http://localhost/wp-content/plugins/cms-tree-page-view/images/page_white_text.png"
				},
				"attr": {
					"xhref": "http://localhost/wp-admin/post.php?post=1060&action=edit",
					"id": "cms-tpv-1060",
					"xtitle": "Click to edit. Drag to move.",
					"class": "cms_tpv_user_can_edit_page_yes"
				},
								"metadata": {
					"id": "cms-tpv-1060",
					"post_id": "1060",
					"post_type": "page",
					"post_status": "publish",
					"rel": "publish",
					"childCount": 0,
					"permalink": "http://localhost/i-am-a-new-page/",
					"editlink": "http://localhost/wp-admin/post.php?post=1060&action=edit",
					"modified_time": "August 15, 2010",
					"modified_author": "admin",
					"columns": "%3Cdl%3E%3Cdt%3EComments%3C%2Fdt%3E%3Cdd%3E%3Cdiv%20class%3D%22post-com-count-wrapper%22%3E%3Ca%20href%3D%27edit-comments.php%3Fp%3D1060%27%20title%3D%270%20pending%27%3E%3Cspan%3E0%3C%2Fspan%3E%3C%2Fa%3E%3C%2Fdiv%3E%3C%2Fdd%3E%3C%2Fdl%3E",
					"user_can_edit_page": "1"
				}
				
			}
		]*/
		},
		"themes": {
			"theme": "wordpress"
		},
		"search": {
			"ajax" : {
				"url": ajaxurl + CMS_TPV_AJAXURL + CMS_TPV_VIEW
			},
			"case_insensitive": true
		},
		"dnd": {
		}
	}

	if (cms_tpv_tree.length > 0) {
		cms_tpv_bind_clean_node(); // don't remember why I run this here.. :/
	}
	cms_tpv_tree.each(function(i, elm) {

		var $elm = $(elm);

		// init tree, with settings specific for each post type
		var treeOptionsTmp = jQuery.extend(true, {}, treeOptions); // make copy of object
		var post_type = cms_tpv_get_post_type(elm);
		treeOptionsTmp.json_data.ajax.url = treeOptionsTmp.json_data.ajax.url + "&post_type=" + post_type + "&lang=" + cms_tpv_get_wpml_selected_lang(elm);
		treeOptionsTmp.json_data.data = cms_tpv_jsondata[post_type]; // get from js
		
		var isHierarchical = $(elm).closest(".cms_tpv_wrapper").find("[name=cms_tpv_meta_post_type_hierarchical]").val();
		if (isHierarchical == 0) {
			// no move to children if not hierarchical
			treeOptionsTmp.types = {
				"types": {
					"default" : {
						"valid_children" : [ "none" ]
					}
				}
			}
		}
		
		$elm.bind("search.jstree", function (event, data) {
			if (data.rslt.nodes.length == 0) {
				// no hits. doh.
				$(this).closest(".cms_tpv_wrapper").find(".cms_tree_view_search_form_no_hits").fadeIn("fast");
			}
		});
		
		$elm.jstree(treeOptionsTmp);

	});
	

}); // end ondomready


// get post type
// elm must be within .cms_tpv_wrapper to work
function cms_tpv_get_post_type(elm) {
	return jQuery(elm).closest(".cms_tpv_wrapper").find("[name=cms_tpv_meta_post_type]").val();
}
// get selected lang
function cms_tpv_get_wpml_selected_lang(elm) {
	return jQuery(elm).closest(".cms_tpv_wrapper").find("[name=cms_tpv_meta_wpml_language]").val();
}

function cms_tpv_get_page_actions_div(elm) {
	return jQuery(elm).closest(".cms_tpv_wrapper").find(".cms_tpv_page_actions");
}
function cms_tpv_get_wrapper(elm) {
	var $wrapper = jQuery(elm).closest(".cms_tpv_wrapper");
	return $wrapper;
}


// add page after
jQuery(".cms_tpv_action_add_page_after").live("click", function() {
	var $this = jQuery(this);
	var post_type = cms_tpv_get_post_type(this);
	var selected_lang = cms_tpv_get_wpml_selected_lang(this);

	var post_status = $this.closest("li").data("post_status");

	// not allowed when status is trash
	if (post_status == "trash") {
		jAlert(cmstpv_l10n.Can_not_add_page_after_when_status_is_trash);
		return false;
	}

	jPrompt(cmstpv_l10n.Enter_title_of_new_page, "", "CMS Tree Page View", function(new_page_title) {
		if (new_page_title) {
			var pageID = $this.parents("li:first").attr("id");
			jQuery(".cms_tpv_message").html("<p>"+cmstpv_l10n.Adding_page+"</p>").slideDown("fast");
			jQuery.post(ajaxurl, {
				"action": "cms_tpv_add_page",
				"pageID": pageID,
				"type": "after",
				"page_title": new_page_title,
				"post_type": post_type,
				"wpml_lang": selected_lang
			}, function(data, textStatus) {
				document.location = data;
			});
		}
	});

	return false;
});

// add page inside
jQuery(".cms_tpv_action_add_page_inside").live("click", function() {
	var $this = jQuery(this);
	var post_type = cms_tpv_get_post_type(this);
	var selected_lang = cms_tpv_get_wpml_selected_lang(this);
	
	var post_status = $this.closest("li").data("post_status");

	// check page status, because we cant add a page inside a page with status draft or status trash
	// if we edit the page wordpress will forget the parent
	//$li.data("jstree").permalink;
	//var post_status = li.data("jstree").post_status;
	if (post_status == "draft") {
		jAlert(cmstpv_l10n.Can_not_add_sub_page_when_status_is_draft);
		return false;
	}

	// not allowed when status is trash
	if (post_status == "trash") {
		jAlert(cmstpv_l10n.Can_not_add_sub_page_when_status_is_trash);
		return false;
	}
	
	jPrompt(cmstpv_l10n.Enter_title_of_new_page, "", "CMS Tree Page View", function(new_page_title) {
		if (new_page_title) {
			var pageID = $this.parents("li:first").attr("id");
			jQuery(".cms_tpv_message").html("<p>" + cmstpv_l10n.Adding_page + "</p>").slideDown("fast");
			jQuery.post(ajaxurl, {
				"action": "cms_tpv_add_page",
				"pageID": pageID,
				"type": "inside",
				"page_title": new_page_title,
				"post_type": post_type,
				"wpml_lang": selected_lang
			}, function(data, textStatus) {
				document.location = data;
			});
		}
	});
	return false;
});



// check if tree is beging dragged
function cms_tpv_is_dragging() {
	var eDrag = jQuery("#vakata-dragged");
	return eDrag.is(":visible");
}

// fired when mouse is over li
function cms_tpv_mouseover_li(li) {

	//console.log("show actions div");
	$li = jQuery(li);

	var div_actions_for_post_type = cms_tpv_get_page_actions_div(li);

	if (cms_tpv_is_dragging() == false) {
	
		if (div_actions_for_post_type.is(":visible")) {
			// do nada
		} else {

			$li.find("a:first").addClass("hover");
			
			// setup link for view page
			$view = div_actions_for_post_type.find(".cms_tpv_action_view");
			var permalink = $li.data("permalink");
			$view.attr("href", permalink);

			// setup link for edit page
			$edit = div_actions_for_post_type.find(".cms_tpv_action_edit");
			var editlink = $li.data("editlink");
			$edit.attr("href", editlink);
			
			// check if user is allowed to edit page
			var $cms_tpv_action_add_and_edit_page = div_actions_for_post_type.find(".cms_tpv_action_add_and_edit_page");
			if ($li.data("user_can_edit_page") == 0) {
				// nooope
				$edit.hide();
				$cms_tpv_action_add_and_edit_page.hide();
			} else {
				$edit.show();
				$cms_tpv_action_add_and_edit_page.show();
			}
			
			// ..and some extras
			div_actions_for_post_type.find(".cms_tpv_page_actions_modified_time").text($li.data("modified_time"));
			div_actions_for_post_type.find(".cms_tpv_page_actions_modified_by").text($li.data("modified_author"));
			div_actions_for_post_type.find(".cms_tpv_page_actions_page_id").text($li.data("post_id"));		
			
			div_actions_for_post_type.find(".cms_tpv_page_actions_columns").html( unescape($li.data("columns")) );
			
			// position and show action div
			var $a = $li.find("a");
			var width = $a.outerWidth(true);
			$li.append(div_actions_for_post_type);
			left_pos = width+28;
			top_pos = -8;
			div_actions_for_post_type.css("left", left_pos);
			div_actions_for_post_type.css("top", top_pos);
			div_actions_for_post_type.show();
		}
	}

}

// fired when mouse leaves li
function cms_tpv_mouseout_li(li) {
	$li = jQuery(li);
	$li.find("a:first").removeClass("hover");
	div_actions.hide();
}

// mouse over, show actions
// but only if the mouse not already is over the li (don't know why it fires multiple times, but it does)
// 29 August, 2010 this worked nice but it had problems with child-lis...
/*
jQuery(".jstree li").live("mouseenter", function(e) {

	//console.log("mouseenter");
	var $li = jQuery(this);
	var li_id = $li.attr("id");
	
	// add hoverIntent, if not prev. attached
	if ($li.data("hasHoverIntent")) {
		// already got it
	} else {
		$li.data("hasHoverIntent", true);
		$li.hoverIntent(function() {
			// console.log("over");
			cms_tpv_mouseover_li(this);
		}, function() {
			console.log("out");
			//cms_tpv_mouseout_li(this);
		});
		// lastlt trigger mouseenter again so the popup will show
		$li.trigger("mouseover");
	}

});
*/

jQuery(".jstree li").live("mouseover", function(e) {
	var $li = jQuery(this);
	var li_id = $li.attr("id");
	cms_tpv_mouseover_li(this);
});
// ..and hide them again
jQuery(".jstree li").live("mouseout", function() {
	//cms_tpv_current_li_id = null;
	//console.log("out");
	cms_tpv_mouseout_li(this);
});


// hide action links on drag
jQuery.jstree.drag_start = function() {
	jQuery(".cms_tpv_action_view, .cms_tpv_action_edit, .cms_tpv_action_add_page, .cms_tpv_action_add_page_after, .cms_tpv_action_add_page_inside").hide();
}

/**
 * add childcount and other things to each li
 */
function cms_tpv_bind_clean_node() {
	
	cms_tpv_tree.bind("move_node.jstree", function (event, data) {
		var nodeBeingMoved = data.rslt.o; // noden vi flyttar
		var nodeNewParent = data.rslt.np;
		var nodePosition = data.rslt.p;
		var nodeR = data.rslt.r;
		var nodeRef = data.rslt.or; // noden som positionen gäller versus
		var selected_lang = cms_tpv_get_wpml_selected_lang(nodeBeingMoved);
		/*

		// om ovanför
		o ovanför or
		
		// om efter
		o efter r
		
		// om inside
		o ovanför or
		

		drop_target		: ".jstree-drop",
		drop_check		: function (data) { return true; },
		drop_finish		: $.noop,
		drag_target		: ".jstree-draggable",
		drag_finish		: $.noop,
		drag_check		: function (data) { return { after : false, before : false, inside : true }; }
		
		Gets executed after a valid drop, you get one parameter, which is as follows:
		data.o - the object being dragged
		data.r - the drop target
		*/
		
		if (nodePosition == "before") {
			var node_id = jQuery( nodeBeingMoved ).attr( "id" );
			ref_node_id = jQuery( nodeRef ).attr( "id" );
		} else if (nodePosition == "after") {
			var node_id = jQuery( nodeBeingMoved ).attr( "id" );
			ref_node_id = jQuery( nodeR ).attr( "id" );
		} else if (nodePosition == "inside") {
			var node_id = jQuery( nodeBeingMoved ).attr( "id" );
			ref_node_id = jQuery( nodeR ).attr( "id" );
		}
		
		// Update parent or menu order
		jQuery.post(ajaxurl, {
				action: "cms_tpv_move_page", 
				"node_id": node_id, 
				"ref_node_id": ref_node_id, 
				"type": nodePosition,
				"icl_post_language": selected_lang
			}, function(data, textStatus) {
		});

	});
	
	cms_tpv_tree.bind("clean_node.jstree", function(event, data) {
		var obj = (data.rslt.obj);
		if (obj && obj != -1) {
			obj.each(function(i, elm) {
				var li = jQuery(elm);
				var aFirst = li.find("a:first");

				// check that we haven't added our stuff already
				if (li.data("done_cms_tpv_clean_node")) {
					return;
				} else {
					li.data("done_cms_tpv_clean_node", true);
				}
				// new way:
				// console.log(li.data("childCount"));
				// add number of children
				//if (li.data("jstree")) {
					var childCount = li.data("childCount");
					if (childCount > 0) {
						aFirst.append("<span title='" + childCount + " " + cmstpv_l10n.child_pages + "' class='child_count'>("+childCount+")</span>");
					}
					
					// add protection type
					var rel = li.data("rel");
					if(rel == "password") {
						aFirst.find("ins").after("<span class='post_protected' title='" + cmstpv_l10n.Password_protected_page + "'>&nbsp;</span>");
					}
	
					// add page type
					var post_status = li.data("post_status");
					// post_status can be any value because of plugins like Edit flow
					// check if we have an existing translation for the string, otherwise use the post status directly
					var post_status_to_show = "";
					if (post_status_to_show = cmstpv_l10n["Status_"+post_status]) {
						// it's ok
					} else {
						post_status_to_show = post_status;
					}
					if (post_status != "publish") {
						aFirst.find("ins").first().after("<span class='post_type post_type_"+post_status+"'>" + post_status_to_show + "</span>");
					}
				//}
				
			});
		}
	});
}

// search: perform
jQuery(".cms_tree_view_search_form").live("submit", function() {
	var $wrapper = jQuery(this).closest(".cms_tpv_wrapper");
	$wrapper.find(".cms_tpv_search_no_hits").hide();
	var s = $wrapper.find(".cms_tree_view_search").attr("value");
	s = jQuery.trim( s );
	// search, oh the mighty search!
	if (s) {
		$wrapper.find(".cms_tree_view_search_form_no_hits").fadeOut("fast");
		$wrapper.find(".cms_tree_view_search_form_working").fadeIn("fast");
		$wrapper.find(".cms_tree_view_search_form_reset")
		$wrapper.find(".cms_tpv_container").jstree("search", s);
		$wrapper.find(".cms_tree_view_search_form_reset").fadeIn("fast");
	} else {
		$wrapper.find(".cms_tree_view_search_form_no_hits").fadeOut("fast");
		$wrapper.find(".cms_tpv_container").jstree("clear_search");
		$wrapper.find(".cms_tree_view_search_form_reset").fadeOut("fast");
	}
	$wrapper.find(".cms_tree_view_search_form_working").fadeOut("fast");
	return false;
});

// search: reset
jQuery(".cms_tree_view_search_form_reset").live("click", function() {
	var $wrapper = jQuery(this).closest(".cms_tpv_wrapper");
	$wrapper.find(".cms_tree_view_search").val("")
	$wrapper.find(".cms_tpv_container").jstree("clear_search");
	$wrapper.find(".cms_tree_view_search_form_reset").fadeOut("fast");
	$wrapper.find(".cms_tree_view_search_form_no_hits").fadeOut("fast");
	return false;
});

// open/close links
jQuery(".cms_tpv_open_all").live("click", function() {
	var $wrapper = jQuery(this).closest(".cms_tpv_wrapper");
	$wrapper.find(".cms_tpv_container").jstree("open_all");
	return false;
});
jQuery(".cms_tpv_close_all").live("click", function() {
	var $wrapper = jQuery(this).closest(".cms_tpv_wrapper");
	$wrapper.find(".cms_tpv_container").jstree("close_all");
	return false;
});

// view all or public or trash
jQuery(".cms_tvp_view_all").live("click", function() {
	cms_tvp_set_view("all", this);
	return false;
});
jQuery(".cms_tvp_view_public").live("click", function() {
	cms_tvp_set_view("public", this);
	return false;
});
jQuery(".cms_tvp_view_trash").live("click", function() {
	cms_tvp_set_view("trash", this);
	return false;
});


// change lang
jQuery("a.cms_tvp_switch_lang").live("click", function(e) {
	$wrapper = cms_tpv_get_wrapper(this);
	$wrapper.find("ul.cms_tvp_switch_langs a").removeClass("current");
	jQuery(this).addClass("current");

	var re = /cms_tpv_switch_language_code_([\w-]+)/;
	var matches = re.exec( jQuery(this).attr("class") );
	var lang_code = matches[1];
	$wrapper.find("[name=cms_tpv_meta_wpml_language]").val(lang_code);

	var current_view = cms_tpv_get_current_view(this);
	cms_tvp_set_view(current_view, this);
	
	return false;

});

function cms_tpv_get_current_view(elm) {
	$wrapper = cms_tpv_get_wrapper(elm);
	if ($wrapper.find(".cms_tvp_view_all").hasClass("current")) {
		return "all";
	} else if ($wrapper.find(".cms_tvp_view_public").hasClass("current")) {
		return "public";
	} else {
		return false; // like unknown 
	}

}

function cms_tvp_set_view(view, elm) {

	var $wrapper = jQuery(elm).closest(".cms_tpv_wrapper");

	var div_actions_for_post_type = cms_tpv_get_page_actions_div(elm);
	$wrapper.append(div_actions_for_post_type);

	$wrapper.find(".cms_tvp_view_all, .cms_tvp_view_public, .cms_tvp_view_trash").removeClass("current");
	$wrapper.find(".cms_tpv_container").jstree("destroy").html("");
	cms_tpv_bind_clean_node();

	if (view == "all") {
		$wrapper.find(".cms_tvp_view_all").addClass("current");
	} else if (view == "public") {
		$wrapper.find(".cms_tvp_view_public").addClass("current");
	} else if (view == "trash") {
		$wrapper.find(".cms_tvp_view_trash").addClass("current");
	} else {
		
	}
	
	var treeOptionsTmp = jQuery.extend(true, {}, treeOptions);
	treeOptionsTmp.json_data.ajax.url = ajaxurl + CMS_TPV_AJAXURL + view + "&post_type=" + cms_tpv_get_post_type(elm) + "&lang=" + cms_tpv_get_wpml_selected_lang(elm);
	$wrapper.find(".cms_tpv_container").jstree(treeOptionsTmp);
}
