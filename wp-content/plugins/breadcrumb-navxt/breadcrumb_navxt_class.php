<?php
/*  
	Copyright 2007-2009  John Havlik  (email : mtekkmonkey@gmail.com)

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

//The breadcrumb class
class bcn_breadcrumb
{
	//Our member variables
	//The main text that will be shown
	public $title;
	//Boolean, is this element linked
	public $linked;
	//Linked anchor contents, null if $linked == false
	public $anchor;
	//Global prefix, outside of link tags
	public $prefix;
	//Global suffix, outside of link tags
	public $suffix;
	/**
	 * bcn_breadcrumb
	 * 
	 * The enhanced default constructor
	 * 
	 * @return 
	 * @param object $title[optional]
	 * @param object $prefix[optional]
	 * @param object $suffix[optional]
	 * @param object $anchor[optional]
	 * @param object $linked[optional]
	 */
	function bcn_breadcrumb($title = '', $prefix = '', $suffix = '', $anchor = NULL, $linked = false)
	{
		//Set the title
		$this->title = $title;
		//Set the prefix
		$this->prefix = $prefix;
		//Set the suffix
		$this->suffix = $suffix;
		//Default state of unlinked
		$this->linked = $linked;
		//Always NULL if unlinked
		$this->anchor = $anchor;
	}
	/**
	 * set_anchor
	 * 
	 * Sets the anchor attribute for the breadcrumb, will set $linked to true
	 * 
	 * @param string $template the anchor template to use
	 * @param string $url the url to replace the %link% tag in the anchor
	 * 
	 */
	function set_anchor($template, $url)
	{
		//Set the anchor, we strip tangs from the title to prevent html validation problems
		$this->anchor = str_replace('%title%', strip_tags($this->title), str_replace('%link%', $url, $template));
		//Set linked to true since we called this function
		$this->linked = true;
	}
	/**
	 * title_trim
	 * 
	 * This function will intelligently trim the title to the value passed in through $max_length.
	 * 
	 * @param int $max_length of the title.
	 */
	function title_trim($max_length)
	{
		//Make sure that we are not making it longer with that ellipse
		if((strlen($this->title) + 3) > $max_length)
		{
			//Trim the title
			$this->title = substr($this->title, 0, $max_length - 1);
			//Make sure we can split at a space, but we want to limmit to cutting at max an additional 25%
			if(strpos($this->title, ' ', .75 * $max_length) > 0)
			{
				//Don't split mid word
				while(substr($this->title,-1) != ' ')
				{
					$this->title = substr($this->title, 0, -1);
				}
			}
			//Remove the whitespace at the end and add the hellip
			$this->title = rtrim($this->title) . '&hellip;';
		}
	}
	/**
	 * assemble
	 * 
	 * Assembles the parts of the breadcrumb into a html string
	 * 
	 * @return string The compiled breadcrumb string
	 * @param bool $linked[optional] Allow the output to contain anchors?
	 */
	function assemble($linked = true)
	{
		//Place in the breadcrumb's elements
		$breadcrumb_str = $this->prefix;
		//If we are linked we'll need to do up the link
		if($this->linked && $linked && $this->anchor)
		{
			$breadcrumb_str .= $this->anchor . $this->title . '</a>';
		}
		//Otherwise we just slip in the title
		else
		{
			$breadcrumb_str .= $this->title;
		}
		//Return the assembled string
		return $breadcrumb_str . $this->suffix;
	}
}

//The trail class
class bcn_breadcrumb_trail
{
	//Our member variables
	public $version = '3.4.1';
	//An array of breadcrumbs
	public $trail = array();
	//The options
	public $opt;
	//Default constructor
	function bcn_breadcrumb_trail()
	{
		//Load the translation domain as the next part needs it		
		load_plugin_textdomain($domain = 'breadcrumb_navxt', false, 'breadcrumb-navxt/languages');
		//Initilize with default option values
		$this->opt = array
		(
			//Should the home page be shown
			'home_display' => true,
			//Title displayed when is_home() returns true
			'home_title' => __('Blog', 'breadcrumb_navxt'),
			//The anchor template for the home page, this is global, two keywords are available %link% and %title%
			'home_anchor' => __('<a title="Go to %title%." href="%link%">', 'breadcrumb_navxt'),
			//Should the home page be shown
			'blog_display' => true,
			//The anchor template for the blog page only in static front page mode, this is global, two keywords are available %link% and %title%
			'blog_anchor' => __('<a title="Go to %title%." href="%link%">', 'breadcrumb_navxt'),
			//The prefix for page breadcrumbs, place on all page elements and inside of current_item prefix
			'home_prefix' => '',
			//The suffix for page breadcrumbs, place on all page elements and inside of current_item suffix
			'home_suffix' => '',
			//Separator that is placed between each item in the breadcrumb trial, but not placed before
			//the first and not after the last breadcrumb
			'separator' => ' &gt; ',
			//The maximum title lenght
			'max_title_length' => 0,
			//Current item options, really only applies to static pages and posts unless other current items are linked
			'current_item_linked' => false,
			//The anchor template for current items, this is global, two keywords are available %link% and %title%
			'current_item_anchor' => __('<a title="Reload the current page." href="%link%">', 'breadcrumb_navxt'),
			//The prefix for current items allows separate styling of the current location breadcrumb
			'current_item_prefix' => '',
			//The suffix for current items allows separate styling of the current location breadcrumb
			'current_item_suffix' => '',
			//Static page options
			//The prefix for page breadcrumbs, place on all page elements and inside of current_item prefix
			'page_prefix' => '',
			//The suffix for page breadcrumbs, place on all page elements and inside of current_item suffix
			'page_suffix' => '',
			//The anchor template for page breadcrumbs, two keywords are available %link% and %title%
			'page_anchor' => __('<a title="Go to %title%." href="%link%">', 'breadcrumb_navxt'),
			//Paged options
			//The prefix for paged breadcrumbs, place on all page elements and inside of current_item prefix
			'paged_prefix' => '',
			//The suffix for paged breadcrumbs, place on all page elements and inside of current_item suffix
			'paged_suffix' => '',
			//Should we try filling out paged information
			'paged_display' => false,
			//The post options previously singleblogpost
			//The prefix for post breadcrumbs, place on all page elements and inside of current_item prefix
			'post_prefix' => '',
			//The suffix for post breadcrumbs, place on all page elements and inside of current_item suffix
			'post_suffix' => '',
			//The anchor template for post breadcrumbs, two keywords are available %link% and %title%
			'post_anchor' => __('<a title="Go to %title%." href="%link%">', 'breadcrumb_navxt'),
			//Should the trail include the taxonomy of the post
			'post_taxonomy_display' => true,
			//What taxonomy should be shown leading to the post, tag or category
			'post_taxonomy_type' => 'category',
			//Attachment settings
			//The prefix for attachment breadcrumbs, place on all page elements and inside of current_item prefix
			'attachment_prefix' => '',
			//The suffix for attachment breadcrumbs, place on all page elements and inside of current_item suffix
			'attachment_suffix' => '',
			//404 page settings
			//The prefix for 404 breadcrumbs, place on all page elements and inside of current_item prefix
			'404_prefix' => '',
			//The suffix for 404 breadcrumbs, place on all page elements and inside of current_item suffix
			'404_suffix' => '',
			//The text to be shown in the breadcrumb for a 404 page
			'404_title' => __('404', 'breadcrumb_navxt'),
			//Search page options
			//The prefix for search breadcrumbs, place on all page elements and inside of current_item prefix
			'search_prefix' => __('Search results for &#39;', 'breadcrumb_navxt'),
			//The suffix for search breadcrumbs, place on all page elements and inside of current_item suffix
			'search_suffix' => '&#39;',
			//The anchor template for search breadcrumbs, two keywords are available %link% and %title%
			'search_anchor' => __('<a title="Go to the first page of search results for %title%." href="%link%">', 'breadcrumb_navxt'),
			//Tag related stuff
			//The prefix for tag breadcrumbs, place on all page elements and inside of current_item prefix
			'post_tag_prefix' => '',
			//The suffix for tag breadcrumbs, place on all page elements and inside of current_item suffix
			'post_tag_suffix' => '',
			//The anchor template for tag breadcrumbs, two keywords are available %link% and %title%
			'post_tag_anchor' => __('<a title="Go to the %title% tag archives." href="%link%">', 'breadcrumb_navxt'),
			//Author page stuff
			//The prefix for author breadcrumbs, place on all page elements and inside of current_item prefix
			'author_prefix' => __('Articles by: ', 'breadcrumb_navxt'),
			//The suffix for author breadcrumbs, place on all page elements and inside of current_item suffix
			'author_suffix' => '',
			//The anchor template for author breadcrumbs, two keywords are available %link% and %title%
			'author_anchor' => __('<a title="Go to the first page of posts by %title%." href="%link%">', 'breadcrumb_navxt'),
			//Which of the various WordPress display types should the author crumb display
			'author_display' => 'display_name',
			//Category stuff
			//The prefix for category breadcrumbs, place on all page elements and inside of current_item prefix
			'category_prefix' => '',
			//The suffix for category breadcrumbs, place on all page elements and inside of current_item suffix
			'category_suffix' => '',
			//The anchor template for category breadcrumbs, two keywords are available %link% and %title%
			'category_anchor' => __('<a title="Go to the %title% category archives." href="%link%">', 'breadcrumb_navxt'),
			//Archives related settings
			//Prefix for category archives, place inside of both the current_item prefix and the category_prefix
			'archive_category_prefix' => __('Archive by category &#39;', 'breadcrumb_navxt'),
			//Suffix for category archives, place inside of both the current_item suffix and the category_suffix
			'archive_category_suffix' => '&#39;',
			//Prefix for tag archives, place inside of the current_item prefix
			'archive_post_tag_prefix' => __('Archive by tag &#39;', 'breadcrumb_navxt'),
			//Suffix for tag archives, place inside of the current_item suffix
			'archive_post_tag_suffix' => '&#39;',
			'date_anchor' => __('<a title="Go to the %title% archives." href="%link%">', 'breadcrumb_navxt'),
			//Prefix for date archives, place inside of the current_item prefix
			'archive_date_prefix' => '',
			//Suffix for date archives, place inside of the current_item suffix
			'archive_date_suffix' => ''
		);
	}
	/**
	 * add
	 * 
	 * Adds a breadcrumb to the breadcrumb trail
	 * 
	 * @return pointer to the just added Breadcrumb
	 * @param bcn_breadcrumb $object Breadcrumb to add to the trail
	 */
	function &add(bcn_breadcrumb $object)
	{
		$this->trail[] = $object;
		return $this->trail[count($this->trail) - 1];
	}
	/**
	 * do_search
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This functions fills a breadcrumb for a search page.
	 */
	function do_search()
	{
		global $s;
		//Place the breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
		$breadcrumb = $this->add(new bcn_breadcrumb(wp_specialchars($s, 1), $this->opt['search_prefix'], $this->opt['search_suffix']));
		//If we're paged, let's link to the first page
		if(is_paged() && $this->opt['paged_display'])
		{
			//Figure out the hyperlink for the anchor
			$url = get_settings('home'). '?s=' . str_replace(' ', '+', wp_specialchars($s, 1));
			//Figure out the anchor for the search
			$breadcrumb->set_anchor($this->opt['search_anchor'], $url);
		}
	}
	/**
	 * do_attachment
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This functions fills a breadcrumb for an attachment page.
	 */
	function do_attachment()
	{
		global $post;
		//Place the breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
		$this->trail[] = new bcn_breadcrumb(get_the_title(), $this->opt['attachment_prefix'], $this->opt['attachment_suffix']);
		//Get the parent page/post of the attachment
		$parent_id = $post->post_parent;
		//Get the parent's information
		$parent = get_post($parent_id);
		//We need to treat post and page attachment hierachy differently
		if($parent->post_type == 'page')
		{
			//Grab the page on front ID for page_parents
			$frontpage = get_option('page_on_front');
			//Place the rest of the page hierachy
			$this->page_parents($parent_id, $frontpage);
		}
		else
		{
			//Place the breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
			$breadcrumb = $this->add(new bcn_breadcrumb(apply_filters('the_title', $parent->post_title),
				$this->opt['post_prefix'], $this->opt['post_suffix']));
			//Assign the anchor properties
			$breadcrumb->set_anchor($this->opt['post_anchor'], get_permalink($parent_id));
			//Handle the post's taxonomy
			$this->post_taxonomy($parent_id);
		}
	}
	/**
	 * do_author
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This functions fills a breadcrumb for an author page.
	 */
	function do_author()
	{
		global $author;
		//Get the Author name, note it is an object
		$curauth = (isset($_GET['author_name'])) ? get_userdatabylogin($author_name) : get_userdata(intval($author));
		//Setup array of valid author_display values
		$valid_author_display = array('display_name', 'nickname', 'first_name', 'last_name');
		//This translation allows us to easily select the display type later on
		$author_display = $this->opt['author_display'];
		//Make sure user picks only safe values
		if(in_array($author_display, $valid_author_display))
		{
			//Place the breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
			$breadcrumb = $this->add(new bcn_breadcrumb(apply_filters('the_author', $curauth->$author_display),
				$this->opt['author_prefix'], $this->opt['author_suffix']));
			if(is_paged() && $this->opt['paged_display'])
			{
				$breadcrumb->set_anchor($this->opt['author_anchor'], get_author_posts_url($curauth->ID));
			}
		}
	}
	/**
	 * page_parents
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This recursive functions fills the trail with breadcrumbs for parent pages.
	 * @param int $id The id of the parent page.
	 * @param int $frontpage The id of the front page.
	 */
	function page_parents($id, $frontpage)
	{
		//Use WordPress API, though a bit heavier than the old method, this will ensure compatibility with other plug-ins
		$parent = get_post($id);
		//Place the breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
		$breadcrumb = $this->add(new bcn_breadcrumb(apply_filters('the_title', $parent->post_title), $this->opt['page_prefix'], 
			$this->opt['page_suffix']));
		//Assign the anchor properties
		$breadcrumb->set_anchor($this->opt['page_anchor'], get_permalink($id));
		//Make sure the id is valid, and that we won't end up spinning in a loop
		if($parent->post_parent >= 0 && $parent->post_parent != false && $id != $parent->post_parent && $frontpage != $parent->post_parent)
		{
			//If valid, recursively call this function
			$this->page_parents($parent->post_parent, $frontpage);
		}
	}
	/**
	 * do_page
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This functions fills a breadcrumb for a atatic page.
	 */
	function do_page()
	{
		global $post;
		//Place the breadcrumb in the trail, uses the bcn_breadcrumb constructor to set the title, prefix, and suffix
		$this->trail[] = new bcn_breadcrumb(get_the_title(), $this->opt['page_prefix'], $this->opt['page_suffix']);
		//Done with the current item, now on to the parents
		$bcn_frontpage = get_option('page_on_front');
		//If there is a parent page let's find it
		if($post->post_parent && $post->ID != $post->post_parent && $bcn_frontpage != $post->post_parent)
		{
			$this->page_parents($post->post_parent, $bcn_frontpage);
		}
	}
	/**
	 * post_taxonomy
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This function fills breadcrumbs for any post taxonomy.
	 * @param int $id The id of the post to figure out the taxonomy for.
	 */
	function post_taxonomy($id)
	{
		//Check to see if breadcrumbs for the taxonomy of the post needs to be generated
		if($this->opt['post_taxonomy_display'])
		{
			//Check if we have a date 'taxonomy' request
			if($this->opt['post_taxonomy_type'] == 'date')
			{
				$this->do_archive_by_date();
			}
			//Handle all hierarchical taxonomies, including categories
			else if(is_taxonomy_hierarchical($this->opt['post_taxonomy_type']))
			{
				//Fill a temporary object with the terms
				$bcn_object = get_the_terms($id, $this->opt['post_taxonomy_type']);
				if(is_array($bcn_object))
				{
					//Now find which one has a parent, pick the first one that does
					$bcn_use_term = key($bcn_object);
					foreach($bcn_object as $key=>$object)
					{
						//We want the first term hiearchy
						if($object->parent > 0)
						{
							$bcn_use_term = $key;
							//We found our first term hiearchy, can exit loop now
							break;
						}
					}
					//Fill out the term hiearchy
					$this->term_parents($bcn_object[$bcn_use_term]->term_id, $this->opt['post_taxonomy_type']);
				}
			}
			//Handle the rest of the taxonomies, including tags
			else
			{
				$this->post_terms($id, $this->opt['post_taxonomy_type']);
			}
		}
	}
	/**
	 * post_terms
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This functions fills a breadcrumb for the terms of a post
	 * @param int $id The id of the post to find the terms for.
	 * @param string $taxonomy The name of the taxonomy that the term belongs to
	 * 
	 * @TODO	Need to implement this cleaner, fix up the entire tag_ thing, as this is now generic
	 */
	function post_terms($id, $taxonomy)
	{
		//Add new breadcrumb to the trail
		$this->trail[] = new bcn_breadcrumb();
		//Figure out where we placed the crumb, make a nice pointer to it
		$bcn_breadcrumb = &$this->trail[count($this->trail) - 1];
		//Fills a temporary object with the terms for the post
		$bcn_object = get_the_terms($id, $taxonomy);
		//Only process if we have tags
		if(is_array($bcn_object))
		{
			$is_first = true;
			//Loop through all of the term results
			foreach($bcn_object as $term)
			{
				//Run through a filter for good measure
				$term->name = apply_filters("get_$taxonomy", $term->name);
				//Everything but the first term needs a comma separator
				if($is_first == false)
				{
					$bcn_breadcrumb->title .= ', ';
				}
				//This is a bit hackish, but it compiles the tag anchor and appends it to the current breadcrumb title
				$bcn_breadcrumb->title .= $this->opt[$taxonomy . '_prefix'] . str_replace('%title%', $term->name, str_replace('%link%', get_term_link($term, $taxonomy), $this->opt[$taxonomy . '_anchor'])) .
					$term->name . '</a>' . $this->opt[$taxonomy . '_suffix'];
				$is_first = false;
			}
		}
		else
		{
			//If there are no tags, then we set the title to "Untagged"
			$bcn_breadcrumb->title = __('Untagged', 'breadcrumb_navxt');
		}
	}
	/**
	 * term_parents
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This recursive functions fills the trail with breadcrumbs for parent terms.
	 * @param int $id The id of the term.
	 * @param string $taxonomy The name of the taxonomy that the term belongs to
	 */
	function term_parents($id, $taxonomy)
	{
		global $post;
		//Get the current category object, filter applied within this call
		$term = &get_term($id, $taxonomy);
		//Place the breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
		$breadcrumb = $this->add(new bcn_breadcrumb($term->name, $this->opt[$taxonomy . '_prefix'], $this->opt[$taxonomy . '_suffix']));
		//Figure out the anchor for the term
		$breadcrumb->set_anchor($this->opt[$taxonomy . '_anchor'], get_term_link($term, $taxonomy));
		//Make sure the id is valid, and that we won't end up spinning in a loop
		if($term->parent && $term->parent != $id)
		{
			//Figure out the rest of the term hiearchy via recursion
			$this->term_parents($term->parent, $taxonomy);
		}
	}
	/**
	 * do_archive_by_term_hierarchical
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This functions fills a breadcrumb for a hierarchical taxonomy (e.g. category) archive.
	 */
	function do_archive_by_term_hierarchical()
	{
		global $wp_query;
		//Simmilar to using $post, but for things $post doesn't cover
		$term = $wp_query->get_queried_object();
		//Run through a filter for good measure
		$term->name = apply_filters('get_' . $term->taxonomy, $term->name);
		//Place the breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
		$breadcrumb = $this->add(new bcn_breadcrumb($term->name, $this->opt[$term->taxonomy . '_prefix'] . $this->opt['archive_' . $term->taxonomy . '_prefix'],
			$this->opt['archive_' . $term->taxonomy . '_suffix'] . $this->opt[$term->taxonomy . '_suffix']));
		//If we're paged, let's link to the first page
		if(is_paged() && $this->opt['paged_display'])
		{
			//Figure out the anchor for current category
			$breadcrumb->set_anchor($this->opt[$term->taxonomy . '_anchor'], get_term_link($term, $term->taxonomy));
		}
		//Get parents of current category
		if($term->parent)
		{
			$this->term_parents($term->parent, $term->taxonomy);
		}
	}
	/**
	 * do_archive_by_term
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This functions fills a breadcrumb for a flat taxonomy (e.g. tag) archive.
	 */
	function do_archive_by_term_flat()
	{
		global $wp_query;
		//Simmilar to using $post, but for things $post doesn't cover
		$term = $wp_query->get_queried_object();
		//Run through a filter for good measure
		$term->name = apply_filters('get_' . $term->taxonomy, $term->name);
		//Place the breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
		$breadcrumb = $this->add(new bcn_breadcrumb($term->name, $this->opt['archive_' . $term->taxonomy . '_prefix'] . $this->opt[$term->taxonomy . '_prefix'], 
			$this->opt[$term->taxonomy . '_suffix'] . $this->opt['archive_' . $term->taxonomy . '_suffix']));
		//If we're paged, let's link to the first page
		if(is_paged() && $this->opt['paged_display'])
		{
			//Figure out the anchor for current category
			$breadcrumb->set_anchor($this->opt[$term->taxonomy . '_anchor'], get_term_link($term, $term->taxonomy));
		}
	}
	/**
	 * do_archive_by_date
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This functions fills a breadcrumb for a date archive.
	 */
	function do_archive_by_date()
	{
		global $wp_query;
		//First deal with the day breadcrumb
		if(is_day() || is_single())
		{
			//Place the breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
			$breadcrumb = $this->add(new bcn_breadcrumb(get_the_time('d'), $this->opt['archive_date_prefix'], $this->opt['archive_date_suffix']));
			//If we're paged, let's link to the first page
			if(is_paged() && $this->opt['paged_display'] || is_single())
			{
				//Deal with the anchor
				$breadcrumb->set_anchor($this->opt['date_anchor'], get_day_link(get_the_time('Y'), get_the_time('m'), get_the_time('d')));
			}
		}
		//Now deal with the month breadcrumb
		if(is_month() || is_day() || is_single())
		{
			//Place the breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
			$breadcrumb = $this->add(new bcn_breadcrumb(get_the_time('F'), $this->opt['archive_date_prefix'], $this->opt['archive_date_suffix']));
			//If we're paged, or not in the archive by month let's link to the first archive by month page
			if(is_day() || is_single() || (is_month() && is_paged() && $this->opt['paged_display']))
			{
				//Deal with the anchor
				$breadcrumb->set_anchor($this->opt['date_anchor'], get_month_link(get_the_time('Y'), get_the_time('m')));
			}
		}
		//Place the year breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
		$breadcrumb = $this->add(new bcn_breadcrumb(get_the_time('Y'), $this->opt['archive_date_prefix'], $this->opt['archive_date_suffix']));
		//If we're paged, or not in the archive by year let's link to the first archive by year page
		if(is_day() || is_month() || is_single() || (is_paged() && $this->opt['paged_display']))
		{
			//Deal with the anchor
			$breadcrumb->set_anchor($this->opt['date_anchor'], get_year_link(get_the_time('Y')));
		}
	}
	/**
	 * do_post
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This functions fills a breadcrumb for a post.
	 */
	function do_post()
	{
		global $post;
		//Place the breadcrumb in the trail, uses the bcn_breadcrumb constructor to set the title, prefix, and suffix
		$this->trail[] = new bcn_breadcrumb(get_the_title(), $this->opt['post_prefix'], $this->opt['post_suffix']);
		//Handle the post's taxonomy
		$this->post_taxonomy($post->ID);
	}
	/**
	 * do_front_page
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This functions fills a breadcrumb for the front page.
	 */
	function do_front_page()
	{
		global $post;
		//Place the breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
		$breadcrumb = $this->add(new bcn_breadcrumb($this->opt['home_title'], $this->opt['home_prefix'], $this->opt['home_suffix']));
		//If we're paged, let's link to the first page
		if(is_paged() && $this->opt['paged_display'])
		{
			//Figure out the anchor for home page
			$breadcrumb->set_anchor($this->opt['home_anchor'], get_option('home'));
		}
	}
	/**
	 * do_home
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This functions fills a breadcrumb for the home page.
	 */
	function do_home()
	{
		global $post;
		//We only need the "blog" portion on members of the blog, and only if we're in a static frontpage environment
		if($this->opt['blog_display'] && get_option('show_on_front') == 'page' && (is_single() || is_archive() || is_author() || is_home()))
		{
			//We'll have to check if this ID is valid, e.g. user has specified a posts page
			$posts_id = get_option('page_for_posts');
			$frontpage_id = get_option('page_on_front');
			if($posts_id != NULL)
			{
				//Get the blog page
				$bcn_post = get_post($posts_id);
				//Place the breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
				$breadcrumb = $this->add(new bcn_breadcrumb(apply_filters('the_title', $bcn_post->post_title), $this->opt['page_prefix'],
					$this->opt['page_suffix']));
				//If we're not on the current item we need to setup the anchor
				if(!is_home() || (is_paged() && $this->opt['paged_display']))
				{
					//Deal with the anchor
					$breadcrumb->set_anchor($this->opt['blog_anchor'], get_permalink($bcn_post->ID));
				}
				//Done with the current item, now on to the parents
				//If there is a parent page let's find it
				if($bcn_post->post_parent && $bcn_post->ID != $bcn_post->post_parent && $frontpage_id != $bcn_post->post_parent)
				{
					$this->page_parents($bcn_post->post_parent, $frontpage_id);
				}
			}
		}
		//On everything else we need to link, but no current item (pre/suf)fixes
		if($this->opt['home_display'])
		{
			//Place the breadcrumb in the trail, uses the constructor to set the title, prefix, and suffix, get a pointer to it in return
			$breadcrumb = $this->add(new bcn_breadcrumb($this->opt['home_title'], $this->opt['home_prefix'], $this->opt['home_suffix']));
			//Deal with the anchor
			$breadcrumb->set_anchor($this->opt['home_anchor'], get_option('home'));
		}
	}
	/**
	 * do_404
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This functions fills a breadcrumb for 404 pages.
	 */
	function do_404()
	{
		//Place the breadcrumb in the trail, uses the bcn_breadcrumb constructor to set the title, prefix, and suffix
		$this->trail[] = new bcn_breadcrumb($this->opt['404_title'], $this->opt['404_prefix'], $this->opt['404_suffix']);
	}
	/**
	 * do_paged
	 * 
	 * A Breadcrumb Trail Filling Function
	 * 
	 * This functions fills a breadcrumb for paged pages.
	 */
	function do_paged()
	{
		global $paged;
		//Place the breadcrumb in the trail, uses the bcn_breadcrumb constructor to set the title, prefix, and suffix
		$this->trail[] = new bcn_breadcrumb($paged, $this->opt['paged_prefix'], $this->opt['paged_suffix']);
	}
	/**
	 * fill
	 * 
	 * Breadcrumb Trail Filling Function
	 * 
	 * This functions fills the breadcrumb trail.
	 */
	function fill()
	{
		global $wpdb, $post, $wp_query, $paged;
		//Check to see if the trail is already populated
		if(count($this->trail) > 0)
		{
			//Exit early since we have breadcrumbs in the trail
			return null;
		}
		//Do specific opperations for the various page types
		//Check if this isn't the first of a multi paged item
		if(is_paged() && $this->opt['paged_display'])
		{
			$this->do_paged();
		}
		//For the front page, as it may also validate as a page
		if(is_front_page())
		{
			//Must have two seperate branches so that we don't evaluate it as a page
			if($this->opt['home_display'])
			{
				$this->do_front_page();
			}
		}
		//For searches
		else if(is_search())
		{
			$this->do_search();
		}
		//For pages
		else if(is_page())
		{
			$this->do_page();
		}
		//For post/page attachments
		else if(is_attachment())
		{
			$this->do_attachment();
		}
		//For blog posts
		else if(is_single())
		{
			$this->do_post();
		}
		//For author pages
		else if(is_author())
		{
			$this->do_author();
		}
		//For archives
		else if(is_archive())
		{
			//For taxonomy based archives, had to add the two specifics in to overcome WordPress bug
			if(is_tax() || is_category() || is_tag())
			{
				$term = $wp_query->get_queried_object();
				//For hierarchical taxonomy based archives
				if(is_taxonomy_hierarchical($term->taxonomy))
				{
					$this->do_archive_by_term_hierarchical();
				}
				//For flat taxonomy based archives
				else
				{
					$this->do_archive_by_term_flat();
				}
			}
			//For date based archives
			else
			{
				$this->do_archive_by_date();
			}
		}
		//For 404 pages
		else if(is_404())
		{
			$this->do_404();
		}
		//We always do the home link last, unless on the frontpage
		if(!is_front_page())
		{
			$this->do_home();
		}
	}
	/**
	 * order
	 * 
	 * This function will either set the order of the trail to reverse key 
	 * order, or make sure it is forward key ordered.
	 * 
	 * @param bool $reverse[optional] Whether to reverse the trail or not.
	 */
	function order($reverse = false)
	{
		if($reverse)
		{
			//Since there may be multiple calls our trail may be in a non-standard order
			ksort($this->trail);
		}
		else
		{
			//For normal opperation we must reverse the array by key
			krsort($this->trail);
		}
	}
	/**
	 * current_item
	 * 
	 * Performs actions specific to current item breadcrumbs. It will wrap the prefix/suffix
	 * with the current_item_prefix and current_item_suffix. Additionally, it will link the
	 * current item if current_item_linked is set to true.
	 * 
	 * @return 
	 * @param bcn_breadrumb $breadcrumb pointer to a bcn_breadcrumb object to opperate on
	 */
	function current_item($breadcrumb)
	{
		//Prepend the current item prefix
		$breadcrumb->prefix = $this->opt['current_item_prefix'] . $breadcrumb->prefix;
		//Append the current item suffix
		$breadcrumb->suffix .= $this->opt['current_item_suffix'];
		//Link the current item, if required
		if($this->opt['current_item_linked'])
		{
			$breadcrumb->set_anchor($this->opt['current_item_anchor'], '');
		}
	}
	/**
	 * display
	 * 
	 * Breadcrumb Creation Function
	 * 
	 * This functions outputs or returns the breadcrumb trail in string form.
	 *
	 * @return void Void if Option to print out breadcrumb trail was chosen.
	 * @return string String-Data of breadcrumb trail.
	 * @param bool $return Whether to return data or to echo it.
	 * @param bool $linked[optional] Whether to allow hyperlinks in the trail or not.
	 * @param bool $reverse[optional] Whether to reverse the output or not. 
	 */
	function display($return = false, $linked = true, $reverse = false)
	{
		//Set trail order based on reverse flag
		$this->order($reverse);
		//Initilize the string which will hold the assembled trail
		$trail_str = '';
		//The main compiling loop
		foreach($this->trail as $key=>$breadcrumb)
		{
			//Must branch if we are reversing the output or not
			if($reverse)
			{
				//Add in the separator only if we are the 2nd or greater element
				if($key > 0)
				{
					$trail_str .= $this->opt['separator'];
				}
			}
			else
			{
				//Only show the separator when necessary
				if($key < count($this->trail) - 1)
				{
					$trail_str .= $this->opt['separator'];
				}
			}
			//Trim titles, if needed
			if($this->opt['max_title_length'] > 0)
			{
				//Trim the breadcrumb's title
				$breadcrumb->title_trim($this->opt['max_title_length']);
			}
			//If we are on the current item there are some things that must be done
			if($key === 0)
			{
				$this->current_item($breadcrumb);
			}
			//Place in the breadcrumb's assembled elements
			$trail_str .= $breadcrumb->assemble($linked);
		}
		//Should we return or echo the assembled trail?
		if($return)
		{
			return $trail_str;
		}
		else
		{
			//Giving credit where credit is due, please don't remove it
			$tag = "<!-- Breadcrumb NavXT " . $this->version . " -->\n";
			echo $tag . $trail_str;
		}
	}
	/**
	 * display_list
	 * 
	 * Breadcrumb Creation Function
	 * 
	 * This functions outputs or returns the breadcrumb trail in list form.
	 *
	 * @return void Void if Option to print out breadcrumb trail was chosen.
	 * @return string String-Data of breadcrumb trail.
	 * @param bool $return Whether to return data or to echo it.
	 * @param bool $linked[optional] Whether to allow hyperlinks in the trail or not.
	 * @param bool $reverse[optional] Whether to reverse the output or not. 
	 */
	function display_list($return = false, $linked = true, $reverse = false)
	{
		//Set trail order based on reverse flag
		$this->order($reverse);
		//Initilize the string which will hold the assembled trail
		$trail_str = '';
		//The main compiling loop
		foreach($this->trail as $key=>$breadcrumb)
		{
			$trail_str .= '<li';
			//Trim titles, if needed
			if($this->opt['max_title_length'] > 0)
			{
				//Trim the breadcrumb's title
				$breadcrumb->title_trim($this->opt['max_title_length']);
			}
			//On the first run we need to add in a class for the home breadcrumb
			if($trail_str === '<li')
			{
				$trail_str .= ' class="home" ';
			}
			//If we are on the current item there are some things that must be done
			if($key === 0)
			{
				$this->current_item($breadcrumb);
				//Add in a class for current_item
				$trail_str .= ' class="current_item" ';
			}
			//Place in the breadcrumb's assembled elements
			$trail_str .= '>' . $breadcrumb->assemble($linked);
			$trail_str .= "</li>\n";
		}
		//Should we return or echo the assembled trail?
		if($return)
		{
			return $trail_str;
		}
		else
		{
			//Giving credit where credit is due, please don't remove it
			$tag = "<!-- Breadcrumb NavXT " . $this->version . " -->\n";
			echo $tag . $trail_str;
		}
	}
}