<?php
/*
Plugin Name: Breadcrumb NavXT
Plugin URI: http://mtekk.weblogs.us/code/breadcrumb-navxt/
Description: Adds a breadcrumb navigation showing the visitor&#39;s path to their current location. For details on how to use this plugin visit <a href="http://mtekk.weblogs.us/code/breadcrumb-navxt/">Breadcrumb NavXT</a>. 
Version: 3.4.1
Author: John Havlik
Author URI: http://mtekk.weblogs.us/
*/
/*  Copyright 2007-2009  John Havlik  (email : mtekkmonkey@gmail.com)

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
//Include the breadcrumb class
require_once(dirname(__FILE__) . '/breadcrumb_navxt_class.php');
//Include the supplemental functions
require_once(dirname(__FILE__) . '/breadcrumb_navxt_api.php');
/**
 * The administrative interface class 
 * 
 * @since 3.0.0
 */
class bcn_admin
{
	/**
	 * local store for breadcrumb version
	 * 
	 * Example: String '3.1.0' 
	 * 
	 * @var   string
	 * @since 3.1.0
	 */
	private $version = '3.4.1';
	
	/**
	 * wether or not this administration page has contextual help
	 * 
	 * @var bool
	 * @since 3.2
	 */
	private $_has_contextual_help = false;	
	
	/**
	 * local store for the breadcrumb object
	 * 
	 * @see   bcn_admin()
	 * @var   bcn_breadcrumb
	 * @since 3.0
	 */
	public $breadcrumb_trail;
	/**
	 * bcn_admin
	 * 
	 * Administrative interface class default constructor
	 */
	function bcn_admin()
	{
		//We'll let it fail fataly if the class isn't there as we depend on it
		$this->breadcrumb_trail = new bcn_breadcrumb_trail;
		//Installation Script hook
		add_action('activate_breadcrumb-navxt/breadcrumb_navxt_admin.php', array($this, 'install'));
		//Initilizes l10n domain	
		$this->local();		
		//WordPress Admin interface hook
		add_action('admin_menu', array($this, 'add_page'));
		//WordPress Hook for the widget
		add_action('plugins_loaded', array($this, 'register_widget'));
		//Admin Options update hook
		if(isset($_POST['bcn_admin_options']))
		{
			//Temporarily add update function on init if form has been submitted
			add_action('init', array($this, 'update'));
		}
		//Admin Options reset hook
		if(isset($_POST['bcn_admin_reset']))
		{
			//Temporarily add reset function on init if reset form has been submitted
			add_action('init', array($this, 'reset'));
		}
		//Admin Options export hook
		else if(isset($_POST['bcn_admin_export']))
		{
			//Temporarily add export function on init if export form has been submitted
			add_action('init', array($this, 'export'));
		}
		//Admin Options import hook
		else if(isset($_FILES['bcn_admin_import_file']) && !empty($_FILES['bcn_admin_import_file']['name']))
		{
			//Temporarily add import function on init if import form has been submitted
			add_action('init', array($this, 'import'));
		}
		//Admin Init Hook
		add_action('admin_init', array($this, 'admin_init'));
	}
	/**
	 * admin initialisation callback function
	 * 
	 * is bound to wpordpress action 'admin_init' on instantiation
	 * 
	 * @since  3.2.0
	 * @return void
	 */
	public function admin_init()
	{
		// Register options.
		register_setting($option_group = 'bcn_admin', $option_name = 'bcn_options', $sanitize_callback = '');
		//Add in the nice "settings" link to the plugins page
		add_filter('plugin_action_links', array($this, 'filter_plugin_actions'), 10, 2);
		//Add javascript enqeueing callback
		add_action('wp_print_scripts', array($this, 'javascript'));
	}
	/**
	 * security
	 * 
	 * Makes sure the current user can manage options to proceed
	 */
	function security()
	{
		//If the user can not manage options we will die on them
		if(!current_user_can('manage_options'))
		{
			_e('Insufficient privileges to proceed.', 'breadcrumb_navxt');
			die();
		}
	}
	/**
	 * install
	 * 
	 * This sets up and upgrades the database settings, runs on every activation
	 */
	function install()
	{
		//Call our little security function
		$this->security();
		//Initilize the options
		$this->breadcrumb_trail = new bcn_breadcrumb_trail;
		//Reduce db queries by saving this
		$db_version = $this->get_option('bcn_version');
		//If our version is not the same as in the db, time to update
		if($db_version !== $this->version)
		{
			//Split up the db version into it's components
			list($major, $minor, $release) = explode('.', $db_version);
			//For upgrading from 2.x.x
			if($major == 2)
			{
				//Delete old options
				$delete_options = array
				(
					'bcn_preserve', 'bcn_static_frontpage', 'bcn_url_blog', 
					'bcn_home_display', 'bcn_home_link', 'bcn_title_home', 
					'bcn_title_blog', 'bcn_separator', 'bcn_search_prefix', 
					'bcn_search_suffix', 'bcn_author_prefix', 'bcn_author_suffix', 
					'bcn_author_display', 'bcn_singleblogpost_prefix', 
					'bcn_singleblogpost_suffix', 'bcn_page_prefix', 'bcn_page_suffix', 
					'bcn_urltitle_prefix', 'bcn_urltitle_suffix', 
					'bcn_archive_category_prefix', 'bcn_archive_category_suffix', 
					'bcn_archive_date_prefix', 'bcn_archive_date_suffix', 
					'bcn_archive_date_format', 'bcn_attachment_prefix', 
					'bcn_attachment_suffix', 'bcn_archive_tag_prefix', 
					'bcn_archive_tag_suffix', 'bcn_title_404', 'bcn_link_current_item', 
					'bcn_current_item_urltitle', 'bcn_current_item_style_prefix', 
					'bcn_current_item_style_suffix', 'bcn_posttitle_maxlen', 
					'bcn_paged_display', 'bcn_paged_prefix', 'bcn_paged_suffix', 
					'bcn_singleblogpost_taxonomy', 'bcn_singleblogpost_taxonomy_display', 
					'bcn_singleblogpost_category_prefix', 'bcn_singleblogpost_category_suffix', 
					'bcn_singleblogpost_tag_prefix', 'bcn_singleblogpost_tag_suffix'
				);
				foreach ($delete_options as $option)
				{
					$this->delete_option($option);	
				}
			}
			else if($major == 3 && $minor == 0)
			{
				//Update our internal settings
				$this->breadcrumb_trail->opt = $this->get_option('bcn_options');
				//Update our internal settings
				$this->breadcrumb_trail->opt = $this->get_option('bcn_options');
				$this->breadcrumb_trail->opt['search_anchor'] = __('<a title="Go to the first page of search results for %title%." href="%link%">','breadcrumb_navxt');
			}
			else if($major == 3 && $minor < 3)
			{
				//Update our internal settings
				$this->breadcrumb_trail->opt = $this->get_option('bcn_options');
				//Update our internal settings
				$this->breadcrumb_trail->opt = $this->get_option('bcn_options');
				$this->breadcrumb_trail->opt['blog_display'] = true;
			}
			else if($major == 3 && $minor < 4)
			{
				//Update our internal settings
				$this->breadcrumb_trail->opt = $this->get_option('bcn_options');
				//Inline upgrade of the tag setting
				if($this->breadcrumb_trail->opt['post_taxonomy_type'] === 'tag')
				{
					$this->breadcrumb_trail->opt['post_taxonomy_type'] = 'post_tag';
				}
				//Fix our tag settings
				$this->breadcrumb_trail->opt['archive_post_tag_prefix'] = $this->breadcrumb_trail->opt['archive_tag_prefix'];
				$this->breadcrumb_trail->opt['archive_post_tag_suffix'] = $this->breadcrumb_trail->opt['archive_tag_suffix'];
				$this->breadcrumb_trail->opt['post_tag_prefix'] = $this->breadcrumb_trail->opt['tag_prefix'];
				$this->breadcrumb_trail->opt['post_tag_suffix'] = $this->breadcrumb_trail->opt['tag_suffix'];
				$this->breadcrumb_trail->opt['post_tag_anchor'] = $this->breadcrumb_trail->opt['tag_anchor'];
			}
			//Always have to update the version
			$this->update_option('bcn_version', $this->version);
			//Store the options
			$this->add_option('bcn_options', $this->breadcrumb_trail->opt);
		}
		//Check if we have valid anchors
		if($temp = $this->get_option('bcn_options'))
		{
			//Missing the blog anchor is a bug from 3.0.0/3.0.1 so we soft error that one
			if(strlen($temp['blog_anchor']) == 0)
			{
				$temp['blog_anchor'] = $this->breadcrumb_trail->opt['blog_anchor'];
				$this->update_option('bcn_options', $temp);
			}
			else if(strlen($temp['home_anchor']) == 0 || 
				strlen($temp['blog_anchor']) == 0 || 
				strlen($temp['page_anchor']) == 0 || 
				strlen($temp['post_anchor']) == 0 || 
				strlen($temp['tag_anchor']) == 0 ||
				strlen($temp['date_anchor']) == 0 ||
				strlen($temp['category_anchor']) == 0)
			{
				$this->delete_option('bcn_options');
				$this->add_option('bcn_options', $this->breadcrumb_trail->opt);
			}
		}
	}
	/**
	 * uninstall
	 * 
	 * This removes database settings upon deletion of the plugin from WordPress
	 */
	function uninstall()
	{
		//Call our little security function
		$this->security();
		//Remove the option array setting
		$this->delete_option('bcn_options');
		//Remove the version setting
		$this->delete_option('bcn_version');
	}
	/**
	 * reset
	 * 
	 * Resets the options to the default values
	 */
	function reset()
	{
		$this->security();
		//Do a nonce check, prevent malicious link/form problems
		check_admin_referer('bcn_admin_upload');
		//Only needs this one line, will load in the hard coded default option values
		$this->update_option('bcn_options', $this->breadcrumb_trail->opt);
		//Reset successful, let the user know
		add_action('admin_notices', array($this, 'notify_reset'));
	}
	/**
	 * export
	 * 
	 * Exports the database settings to a XML document
	 */
	function export()
	{
		$this->security();
		//Do a nonce check, prevent malicious link/form problems 
		check_admin_referer('bcn_admin_upload');
		//Update our internal settings
		$this->breadcrumb_trail->opt = $this->get_option('bcn_options', true);
		//Create a DOM document
		$dom = new DOMDocument('1.0', 'UTF-8');
		//Adds in newlines and tabs to the output
		$dom->formatOutput = true;
		//We're not using a DTD therefore we need to specify it as a standalone document
		$dom->xmlStandalone = true;
		//Add an element called options
		$node = $dom->createElement('options');
		$parnode = $dom->appendChild($node);
		//Add a child element named plugin
		$node = $dom->createElement('plugin');
		$plugnode = $parnode->appendChild($node);
		//Add some attributes that identify the plugin and version for the options export
		$plugnode->setAttribute('name', 'breadcrumb_navxt');
		$plugnode->setAttribute('version', $this->version);
		//Change our headder to text/xml for direct save
		header('Cache-Control: public');
		//The next two will cause good browsers to download instead of displaying the file
		header('Content-Description: File Transfer');
		header('Content-disposition: attachemnt; filename=bcn_settings.xml');
		header('Content-Type: text/xml');
		//Loop through the options array
		foreach($this->breadcrumb_trail->opt as $key=>$option)
		{
			//Add a option tag under the options tag, store the option value
			$node = $dom->createElement('option', $option);
			$newnode = $plugnode->appendChild($node);
			//Change the tag's name to that of the stored option
			$newnode->setAttribute('name', $key);
		}
		//Prepair the XML for output
		$output = $dom->saveXML();
		//Let the browser know how long the file is
		header('Content-Length: ' . strlen($output)); // binary length
		//Output the file
		echo $output;
		//Prevent WordPress from continuing on
		die();
	}
	/**
	 * import
	 * 
	 * Imports a XML options document
	 */
	function import()
	{
		//Our quick and dirty error supressor
		function error($errno, $errstr, $eerfile, $errline)
		{
			return true;
		}
		$this->security();
		//Do a nonce check, prevent malicious link/form problems
		check_admin_referer('bcn_admin_upload');
		//Create a DOM document
		$dom = new DOMDocument('1.0', 'UTF-8');
		//We want to catch errors ourselves
		set_error_handler('error');
		//Load the user uploaded file, handle failure gracefully
		if($dom->load($_FILES['bcn_admin_import_file']['tmp_name']))
		{
			//Have to use an xpath query otherwise we run into problems
			$xpath = new DOMXPath($dom);  
			$option_sets = $xpath->query('plugin');
			//Loop through all of the xpath query results
			foreach($option_sets as $options)
			{
				//We only want to import options for Breadcrumb NavXT
				if($options->getAttribute('name') === 'breadcrumb_navxt')
				{
					//Do a quick version check
					list($plug_major, $plug_minor, $plug_release) = explode('.', $this->version);
					list($major, $minor, $release) = explode('.', $options->getAttribute('version'));
					//We don't support using newer versioned option files in older releases
					if($plug_major == $major && $plug_minor >= $minor)
					{
						//Loop around all of the options
						foreach($options->getelementsByTagName('option') as $child)
						{
							//Place the option into the option array, DOMDocument decodes html entities for us
							$this->breadcrumb_trail->opt[$child->getAttribute('name')] = $child->nodeValue;
						}
					}
				}
			}
			//Commit the loaded options to the database
			$this->update_option('bcn_options', $this->breadcrumb_trail->opt);
			//Everything was successful, let the user know
			add_action('admin_notices', array($this, 'notify_import_success'));
		}
		else
		{
			//Throw an error since we could not load the file for various reasons
			add_action('admin_notices', array($this, 'notify_import_failure'));
		}
		//Reset to the default error handler after we're done
		restore_error_handler();
	}
	/**
	 * update
	 * 
	 * Updates the database settings from the webform
	 */
	function update()
	{
		global $wp_taxonomies;
		$this->security();
		//Do a nonce check, prevent malicious link/form problems
		check_admin_referer('bcn_admin-options');
		
		//Grab the options from the from post
		//Home page settings
		$this->breadcrumb_trail->opt['home_display'] = str2bool(bcn_get('home_display', 'false'));
		$this->breadcrumb_trail->opt['blog_display'] = str2bool(bcn_get('blog_display', 'false'));
		$this->breadcrumb_trail->opt['home_title'] = bcn_get('home_title');
		$this->breadcrumb_trail->opt['home_anchor'] = bcn_get('home_anchor', $this->breadcrumb_trail->opt['home_anchor']);
		$this->breadcrumb_trail->opt['blog_anchor'] = bcn_get('blog_anchor', $this->breadcrumb_trail->opt['blog_anchor']);
		$this->breadcrumb_trail->opt['home_prefix'] = bcn_get('home_prefix');
		$this->breadcrumb_trail->opt['home_suffix'] = bcn_get('home_suffix');
		$this->breadcrumb_trail->opt['separator'] = bcn_get('separator');
		$this->breadcrumb_trail->opt['max_title_length'] = (int) bcn_get('max_title_length');
		//Current item settings
		$this->breadcrumb_trail->opt['current_item_linked'] = str2bool(bcn_get('current_item_linked', 'false'));
		$this->breadcrumb_trail->opt['current_item_anchor'] = bcn_get('current_item_anchor', $this->breadcrumb_trail->opt['current_item_anchor']);
		$this->breadcrumb_trail->opt['current_item_prefix'] = bcn_get('current_item_prefix');
		$this->breadcrumb_trail->opt['current_item_suffix'] = bcn_get('current_item_suffix');
		//Paged settings
		$this->breadcrumb_trail->opt['paged_prefix'] = bcn_get('paged_prefix');
		$this->breadcrumb_trail->opt['paged_suffix'] = bcn_get('paged_suffix');
		$this->breadcrumb_trail->opt['paged_display'] = str2bool(bcn_get('paged_display', 'false'));
		//Page settings
		$this->breadcrumb_trail->opt['page_prefix'] = bcn_get('page_prefix');
		$this->breadcrumb_trail->opt['page_suffix'] = bcn_get('page_suffix');
		$this->breadcrumb_trail->opt['page_anchor'] = bcn_get('page_anchor', $this->breadcrumb_trail->opt['page_anchor']);
		//Post related options
		$this->breadcrumb_trail->opt['post_prefix'] = bcn_get('post_prefix');
		$this->breadcrumb_trail->opt['post_suffix'] = bcn_get('post_suffix');
		$this->breadcrumb_trail->opt['post_anchor'] = bcn_get('post_anchor', $this->breadcrumb_trail->opt['post_anchor']);
		$this->breadcrumb_trail->opt['post_taxonomy_display'] = str2bool(bcn_get('post_taxonomy_display', 'false'));
		$this->breadcrumb_trail->opt['post_taxonomy_type'] = bcn_get('post_taxonomy_type');
		//Attachment settings
		$this->breadcrumb_trail->opt['attachment_prefix'] = bcn_get('attachment_prefix');
		$this->breadcrumb_trail->opt['attachment_suffix'] = bcn_get('attachment_suffix');
		//404 page settings
		$this->breadcrumb_trail->opt['404_prefix'] = bcn_get('404_prefix');
		$this->breadcrumb_trail->opt['404_suffix'] = bcn_get('404_suffix');
		$this->breadcrumb_trail->opt['404_title'] = bcn_get('404_title');
		//Search page settings
		$this->breadcrumb_trail->opt['search_prefix'] = bcn_get('search_prefix');
		$this->breadcrumb_trail->opt['search_suffix'] = bcn_get('search_suffix');
		$this->breadcrumb_trail->opt['search_anchor'] = bcn_get('search_anchor', $this->breadcrumb_trail->opt['search_anchor']);
		//Tag settings
		$this->breadcrumb_trail->opt['post_tag_prefix'] = bcn_get('post_tag_prefix');
		$this->breadcrumb_trail->opt['post_tag_suffix'] = bcn_get('post_tag_suffix');
		$this->breadcrumb_trail->opt['post_tag_anchor'] = bcn_get('post_tag_anchor', $this->breadcrumb_trail->opt['post_tag_anchor']);
		//Author page settings
		$this->breadcrumb_trail->opt['author_prefix'] = bcn_get('author_prefix');
		$this->breadcrumb_trail->opt['author_suffix'] = bcn_get('author_suffix');
		$this->breadcrumb_trail->opt['author_display'] = bcn_get('author_display');
		//Category settings
		$this->breadcrumb_trail->opt['category_prefix'] = bcn_get('category_prefix');
		$this->breadcrumb_trail->opt['category_suffix'] = bcn_get('category_suffix');
		$this->breadcrumb_trail->opt['category_anchor'] = bcn_get('category_anchor', $this->breadcrumb_trail->opt['category_anchor']);
		//Archive settings
		$this->breadcrumb_trail->opt['archive_category_prefix'] = bcn_get('archive_category_prefix');
		$this->breadcrumb_trail->opt['archive_category_suffix'] = bcn_get('archive_category_suffix');
		$this->breadcrumb_trail->opt['archive_post_tag_prefix'] = bcn_get('archive_post_tag_prefix');
		$this->breadcrumb_trail->opt['archive_post_tag_suffix'] = bcn_get('archive_post_tag_suffix');
		//Archive by date settings
		$this->breadcrumb_trail->opt['date_anchor'] = bcn_get('date_anchor', $this->breadcrumb_trail->opt['date_anchor']);
		$this->breadcrumb_trail->opt['archive_date_prefix'] = bcn_get('archive_date_prefix');
		$this->breadcrumb_trail->opt['archive_date_suffix'] = bcn_get('archive_date_suffix');
		//Loop through all of the taxonomies in the array
		foreach($wp_taxonomies as $taxonomy)
		{
			//We only want custom taxonomies
			if($taxonomy->object_type == 'post' && ($taxonomy->name != 'post_tag' && $taxonomy->name != 'category'))
			{
				$this->breadcrumb_trail->opt[$taxonomy->name . '_prefix'] = bcn_get($taxonomy->name . '_prefix');
				$this->breadcrumb_trail->opt[$taxonomy->name . '_suffix'] = bcn_get($taxonomy->name . '_suffix');
				$this->breadcrumb_trail->opt[$taxonomy->name . '_anchor'] = bcn_get($taxonomy->name . '_anchor', $this->breadcrumb_trail->opt['post_tag_anchor']);
				$this->breadcrumb_trail->opt['archive_' . $taxonomy->name . '_prefix'] = bcn_get('archive_' . $taxonomy->name . '_prefix');
				$this->breadcrumb_trail->opt['archive_' . $taxonomy->name . '_suffix'] = bcn_get('archive_' . $taxonomy->name . '_suffix');
			}
		}
		//Commit the option changes
		$this->update_option('bcn_options', $this->breadcrumb_trail->opt);
	}
	/**
	 * display
	 * 
	 * Outputs the breadcrumb trail
	 * 
	 * @param  (bool)   $return Whether to return or echo the trail.
	 * @param  (bool)   $linked Whether to allow hyperlinks in the trail or not.
	 * @param  (bool)	$reverse Whether to reverse the output or not.
	 */
	function display($return = false, $linked = true, $reverse = false)
	{
		//Update our internal settings
		$this->breadcrumb_trail->opt = $this->get_option('bcn_options');
		//Generate the breadcrumb trail
		$this->breadcrumb_trail->fill();
		return $this->breadcrumb_trail->display($return, $linked, $reverse);
	}
	/**
	 * display_list
	 * 
	 * Outputs the breadcrumb trail
	 * 
	 * @since  3.2.0
	 * @param  (bool)   $return Whether to return or echo the trail.
	 * @param  (bool)   $linked Whether to allow hyperlinks in the trail or not.
	 * @param  (bool)	$reverse Whether to reverse the output or not.
	 */
	function display_list($return = false, $linked = true, $reverse = false)
	{
		//Update our internal settings
		$this->breadcrumb_trail->opt = $this->get_option('bcn_options');
		//Generate the breadcrumb trail
		$this->breadcrumb_trail->fill();
		return $this->breadcrumb_trail->display_list($return, $linked, $reverse);
	}
	/**
	 * widget
	 *
	 * The sidebar widget 
	 */
	function widget($args)
	{
		extract($args);
		//Manditory before widget junk
		echo $before_widget;
		//Display the breadcrumb trial
		if($this->breadcrumb_trail->trail[0] != NULL)
		{
			$this->breadcrumb_trail->display();
		}
		else
		{
			$this->display();
		}
		//Manditory after widget junk
		echo $after_widget;
	}
	/**
	 * register_widget
	 *
	 * Registers the sidebar widget 
	 */
	function register_widget()
	{
		register_sidebar_widget('Breadcrumb NavXT', array($this, 'widget'));
	}
	/**
	 * filter_plugin_actions
	 * 
	 * Places in a link to the settings page in the plugins listing entry
	 * 
	 * @param  (array)  $links An array of links that are output in the listing
	 * @param  (string) $file The file that is currently in processing
	 * @return (array)  Array of links that are output in the listing.
	 */
	function filter_plugin_actions($links, $file)
	{
		static $this_plugin;
		if(!$this_plugin)
		{
			$this_plugin = plugin_basename(__FILE__);
		}
		//Make sure we are adding only for Breadcrumb NavXT
		if($file == $this_plugin)
		{
			//Setup the link string
			$settings_link = '<a href="options-general.php?page=breadcrumb-navxt">' . __('Settings') . '</a>';
			//Add it to the end of the array to better integrate into the WP 2.8 plugins page
			$links[] = $settings_link;
		}
		return $links;
	}
	/**
	 * javascript
	 *
	 * Enqueues JS dependencies (jquery) for the tabs
	 * 
	 * @see admin_init()
	 * @return void
	 */
	function javascript()
	{
		//Enqueue ui-tabs
		wp_enqueue_script('jquery-ui-tabs');
	}
	/**
	 * local
	 *
	 * Initilizes localization textdomain for translations (if applicable)
	 * 
	 * normally there is no need to load it because it is already loaded with 
	 * the breadcrumb class. if not, then it will be loaded.
	 * 
	 * @return void
	 */
	function local()
	{
		// the global and the check might become obsolete in
		// further wordpress versions
		// @see https://core.trac.wordpress.org/ticket/10527
		global $l10n;		
		$domain = 'breadcrumb_navxt';				
		if (!isset( $l10n[$domain] ))
		{
			load_plugin_textdomain($domain, false, 'breadcrumb-navxt/languages');
		}	
	}
	/**
	 * add_page
	 * 
	 * Adds the adminpage the menue and the nice little settings link
	 *
	 * @return void
	 */
	function add_page()
	{
		// check capability of user to manage options (access control)
		if(current_user_can('manage_options'))
		{
			//Add the submenu page to "settings" menu
			$hookname = add_submenu_page('options-general.php', __('Breadcrumb NavXT Settings', 'breadcrumb_navxt'), 'Breadcrumb NavXT', 'manage_options', 'breadcrumb-navxt', array($this, 'admin_panel'));		
			//Register admin_head-$hookname callback
			add_action('admin_head-'.$hookname, array($this, 'admin_head'));			
			//Register Help Output
			add_action('contextual_help', array($this, 'contextual_help'), 10, 2);
		}
	}
	
	/**
	 * contextual_help action hook function
	 * 
	 * @param  string $contextual_help
	 * @param  string $screen
	 * @return string
	 */
	function contextual_help($contextual_help, $screen)
	{
		// add contextual help on current screen		
		if ($screen == 'settings_page_breadcrumb-navxt')
		{
			$contextual_help = $this->_get_contextual_help();
			$this->_has_contextual_help = true;
		}
		return $contextual_help;
	}
	
	/**
	 * get contextual help
	 * 
	 * @return string
	 */
	private function _get_contextual_help()
	{
		$t = $this->_get_help_text();	
		$t = sprintf('<div class="metabox-prefs">%s</div>', $t);	
		$title = __('Breadcrumb NavXT Settings', 'breadcrumb_navxt');	
		$t = sprintf('<h5>%s</h5>%s', sprintf(__('Get help with "%s"'), $title), $t);
		return $t;
	}	
	
	/**
	 * get help text
	 * 
	 * @return string
	 */
	private function _get_help_text()
	{
		return sprintf(__('Tips for the settings are located below select options. Please refer to the %sdocumentation%s for more information.', 'breadcrumb_navxt'), 
			'<a title="' . __('Go to the Breadcrumb NavXT online documentation', 'breadcrumb_navxt') . '" href="http://mtekk.weblogs.us/code/breadcrumb-navxt/breadcrumb-navxt-doc/">', '</a>');
	}
	
	/**
	 * admin_head
	 *
	 * Adds in the JavaScript and CSS for the tabs in the adminsitrative 
	 * interface
	 * 
	 */
	function admin_head()
	{	
		// print style and script element (should go into head element) 
		?>
<style type="text/css">
	/**
	 * Tabbed Admin Page (CSS)
	 * 
	 * @see Breadcrumb NavXT (Wordpress Plugin)
	 * @author Tom Klingenberg 
	 * @colordef #c6d9e9 light-blue (older tabs border color, obsolete)
	 * @colordef #dfdfdf light-grey (tabs border color)
	 * @colordef #f9f9f9 very-light-grey (admin standard background color)
	 * @colordef #fff    white (active tab background color)
	 */
#hasadmintabs ul.ui-tabs-nav {border-bottom:1px solid #dfdfdf; font-size:12px; height:29px; list-style-image:none; list-style-position:outside; list-style-type:none; margin:13px 0 0; overflow:visible; padding:0 0 0 8px;}
#hasadmintabs ul.ui-tabs-nav li {display:block; float:left; line-height:200%; list-style-image:none; list-style-position:outside; list-style-type:none; margin:0; padding:0; position:relative; text-align:center; white-space:nowrap; width:auto;}
#hasadmintabs ul.ui-tabs-nav li a {background:transparent none no-repeat scroll 0 50%; border-bottom:1px solid #dfdfdf; display:block; float:left; line-height:28px; padding:1px 13px 0; position:relative; text-decoration:none;}
#hasadmintabs ul.ui-tabs-nav li.ui-tabs-selected a{-moz-border-radius-topleft:4px; -moz-border-radius-topright:4px;border:1px solid #dfdfdf; border-bottom-color:#f9f9f9; color:#333333; font-weight:normal; padding:0 12px;}
#hasadmintabs ul.ui-tabs-nav a:focus, a:active {outline-color:-moz-use-text-color; outline-style:none; outline-width:medium;}
#screen-options-wrap p.submit {margin:0; padding:0;}
</style>
<script type="text/javascript">
/* <![CDATA[ */
	/**
	 * Breadcrumb NavXT Admin Page (javascript/jQuery)
	 *
	 * unobtrusive approach to add tabbed forms into
	 * the wordpress admin panel and various other 
	 * stuff that needs javascript with the Admin Panel.
	 *
	 * @see Breadcrumb NavXT (Wordpress Plugin)
	 * @author Tom Klingenberg
	 * @author John Havlik
	 * @uses jQuery
	 * @uses jQuery.ui.tabs
	 */		
	jQuery(function()
	{
		bcn_context_init();
		bcn_tabulator_init();		
	 });
	function bcn_confirm(type)
	{
		if(type == 'reset'){
			var answer = confirm("<?php _e('All of your current Breadcrumb NavXT settings will be overwritten with the default values. Are you sure you want to continue?', 'breadcrumb_navxt'); ?>");
		}
		else{
			var answer = confirm("<?php _e('All of your current Breadcrumb NavXT settings will be overwritten with the imported values. Are you sure you want to continue?', 'breadcrumb_navxt'); ?>");
		}
		if(answer)
			return true;
		else
			return false;
	}
	/**
	 * Tabulator Bootup
	 */
	function bcn_tabulator_init(){
		/* if this is not the breadcrumb admin page, quit */
		if (!jQuery("#hasadmintabs").length) return;		
		/* init markup for tabs */
		jQuery('#hasadmintabs').prepend("<ul><\/ul>");
		jQuery('#hasadmintabs > fieldset').each(function(i){
		    id      = jQuery(this).attr('id');
		    caption = jQuery(this).find('h3').text();
		    jQuery('#hasadmintabs > ul').append('<li><a href="#'+id+'"><span>'+caption+"<\/span><\/a><\/li>");
		    jQuery(this).find('h3').hide();					    
	    });	
		/* init the tabs plugin */
		var jquiver = undefined == jQuery.ui ? [0,0,0] : undefined == jQuery.ui.version ? [0,1,0] : jQuery.ui.version.split('.');
		switch(true){
			// tabs plugin has been fixed to work on the parent element again.
			case jquiver[0] >= 1 && jquiver[1] >= 7:
				jQuery("#hasadmintabs").tabs();
				break;
			// tabs plugin has bug and needs to work on ul directly.
			default:
				jQuery("#hasadmintabs > ul").tabs(); 
		}
		/* handler for opening the last tab after submit (compability version) */
		jQuery('#hasadmintabs ul a').click(function(i){
			var form   = jQuery('#bcn_admin_options');
			var action = form.attr("action").split('#', 1) + jQuery(this).attr('href');
			// an older bug pops up with some jQuery version(s), which makes it
			// necessary to set the form's action attribute by standard javascript 
			// node access:						
			form.get(0).setAttribute("action", action);
		});
	}
	/**
	 * context screen options for import/export
	 */
	 function bcn_context_init(){
		if (!jQuery("#bcn_import_export_relocate").length) return;
		var jqver = undefined == jQuery.fn.jquery ? [0,0,0] : jQuery.fn.jquery.split('.');
		jQuery('#screen-meta').prepend(
				'<div id="screen-options-wrap" class="hidden"></div>'
		);
		jQuery('#screen-meta-links').append(
				'<div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle">' +
				'<a class="show-settings" id="show-settings-link" href="#screen-options"><?php printf('%s/%s/%s', __('Import', 'breadcrumb_navxt'), __('Export', 'breadcrumb_navxt'), __('Reset', 'breadcrumb_navxt')); ?></a>' + 
				'</div>'
		);
		// jQuery Version below 1.3 (common for WP 2.7) needs some other style-classes
		// and jQuery events
		if (jqver[0] <= 1 && jqver[1] < 3){
			// hide-if-no-js for WP 2.8, not for WP 2.7
			jQuery('#screen-options-link-wrap').removeClass('hide-if-no-js');
			// screen settings tab (WP 2.7 legacy)
			jQuery('#show-settings-link').click(function () {
				if ( ! jQuery('#screen-options-wrap').hasClass('screen-options-open') ) {
					jQuery('#contextual-help-link-wrap').addClass('invisible');
				}
				jQuery('#screen-options-wrap').slideToggle('fast', function(){
					if ( jQuery(this).hasClass('screen-options-open') ) {
						jQuery('#show-settings-link').css({'backgroundImage':'url("images/screen-options-right.gif")'});
						jQuery('#contextual-help-link-wrap').removeClass('invisible');
						jQuery(this).removeClass('screen-options-open');
					} else {
						jQuery('#show-settings-link').css({'backgroundImage':'url("images/screen-options-right-up.gif")'});
						jQuery(this).addClass('screen-options-open');
					}
				});
				return false;
			});			
		}
		var code = jQuery('#bcn_import_export_relocate').html();
		jQuery('#bcn_import_export_relocate').html('');
		code = code.replace(/h3>/gi, 'h5>');		
		jQuery('#screen-options-wrap').prepend(code);		
	 }
/* ]]> */
</script>
<?php
	} //function admin_head()

	/**
	 * admin_panel
	 * 
	 * The administrative panel for Breadcrumb NavXT
	 * 
	 */
	function admin_panel()
	{
		global $wp_taxonomies;
		$this->security();
		//Update our internal options array, use form safe function
		$this->breadcrumb_trail->opt = $this->get_option('bcn_options', true);
		?>
		<div class="wrap"><h2><?php _e('Breadcrumb NavXT Settings', 'breadcrumb_navxt'); ?></h2>		
		<p<?php if ($this->_has_contextual_help): ?> class="hide-if-js"<?php endif; ?>><?php 
			print $this->_get_help_text();			 
		?></p>
		<form action="options-general.php?page=breadcrumb-navxt" method="post" id="bcn_admin_options">
			<?php
				settings_fields('bcn_admin'); 
			?>
			<div id="hasadmintabs">
			<fieldset id="general" class="bcn_options">
				<h3><?php _e('General', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="separator"><?php _e('Breadcrumb Separator', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="separator" id="separator" value="<?php echo $this->breadcrumb_trail->opt['separator']; ?>" size="32" /><br />
							<span class="setting-description"><?php _e('Placed in between each breadcrumb.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="max_title_length"><?php _e('Breadcrumb Max Title Length', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="max_title_length" id="max_title_length" value="<?php echo $this->breadcrumb_trail->opt['max_title_length'];?>" size="10" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e('Home Breadcrumb', 'breadcrumb_navxt'); ?>						
						</th>
						<td>
							<label>
								<input name="home_display" type="checkbox" id="home_display" value="true" <?php checked(true, $this->breadcrumb_trail->opt['home_display']); ?> />
								<?php _e('Place the home breadcrumb in the trail.', 'breadcrumb_navxt'); ?>				
							</label><br />
							<ul>
								<li>
									<label for="home_title">
										<?php _e('Home Title: ','breadcrumb_navxt');?>
										<input type="text" name="home_title" id="home_title" value="<?php echo $this->breadcrumb_trail->opt['home_title']; ?>" size="20" />
									</label>
								</li>
							</ul>							
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="blog_display"><?php _e('Blog Breadcrumb', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>	
							<label>
								<input name="blog_display" <?php if($this->get_option('show_on_front') !== "page"){echo 'disabled="disabled" class="disabled"';} ?> type="checkbox" id="blog_display" value="true" <?php checked(true, $this->breadcrumb_trail->opt['blog_display']); ?> />
								<?php _e('Place the blog breadcrumb in the trail.', 'breadcrumb_navxt'); ?>				
							</label>				
						</td>
					</tr> 
					<tr valign="top">
						<th scope="row">
							<label for="home_prefix"><?php _e('Home Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="home_prefix" id="home_prefix" value="<?php echo $this->breadcrumb_trail->opt['home_prefix']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="home_suffix"><?php _e('Home Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="home_suffix" id="home_suffix" value="<?php echo $this->breadcrumb_trail->opt['home_suffix']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="home_anchor"><?php _e('Home Anchor', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="home_anchor" id="home_anchor" value="<?php echo $this->breadcrumb_trail->opt['home_anchor']; ?>" size="60" /><br />
							<span class="setting-description"><?php _e('The anchor template for the home breadcrumb.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="blog_anchor"><?php _e('Blog Anchor', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>				
							<input type="text" <?php if($this->get_option('show_on_front') !== "page"){echo 'disabled="disabled" class="disabled"';} ?> name="blog_anchor" id="blog_anchor" value="<?php echo $this->breadcrumb_trail->opt['blog_anchor']; ?>" size="60" /><br />
							<span class="setting-description"><?php _e('The anchor template for the blog breadcrumb, used only in static front page environments.', 'breadcrumb_navxt'); ?></span>			
						</td>
					</tr> 
				</table>
			</fieldset>
			<fieldset id="current" class="bcn_options">
				<h3><?php _e('Current Item', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="current_item_linked"><?php _e('Link Current Item', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<label>
								<input name="current_item_linked" type="checkbox" id="current_item_linked" value="true" <?php checked(true, $this->breadcrumb_trail->opt['current_item_linked']); ?> />
								<?php _e('Yes'); ?>							
							</label>					
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="current_item_prefix"><?php _e('Current Item Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="current_item_prefix" id="current_item_prefix" value="<?php echo $this->breadcrumb_trail->opt['current_item_prefix']; ?>" size="32" /><br />
							<span class="setting-description"><?php _e('This is always placed in front of the last breadcrumb in the trail, before any other prefixes for that breadcrumb.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="current_item_suffix"><?php _e('Current Item Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="current_item_suffix" id="current_item_suffix" value="<?php echo $this->breadcrumb_trail->opt['current_item_suffix']; ?>" size="32" /><br />
							<span class="setting-description"><?php _e('This is always placed after the last breadcrumb in the trail, and after any other prefixes for that breadcrumb.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="current_item_anchor"><?php _e('Current Item Anchor', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="current_item_anchor" id="current_item_anchor" value="<?php echo $this->breadcrumb_trail->opt['current_item_anchor']; ?>" size="60" /><br />
							<span class="setting-description"><?php _e('The anchor template for current item breadcrumbs.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="paged_display"><?php _e('Paged Breadcrumb', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<label>
								<input name="paged_display" type="checkbox" id="paged_display" value="true" <?php checked(true, $this->breadcrumb_trail->opt['paged_display']); ?> />
								<?php _e('Include the paged breadcrumb in the breadcrumb trail.', 'breadcrumb_navxt'); ?>
							</label><br />
							<span class="setting-description"><?php _e('Indicates that the user is on a page other than the first on paginated posts/pages.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="paged_prefix"><?php _e('Paged Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="paged_prefix" id="paged_prefix" value="<?php echo $this->breadcrumb_trail->opt['paged_prefix']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="paged_suffix"><?php _e('Paged Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="paged_suffix" id="paged_suffix" value="<?php echo $this->breadcrumb_trail->opt['paged_suffix']; ?>" size="32" />
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset id="single" class="bcn_options">
				<h3><?php _e('Posts &amp; Pages', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="post_prefix"><?php _e('Post Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="post_prefix" id="post_prefix" value="<?php echo $this->breadcrumb_trail->opt['post_prefix']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="post_suffix"><?php _e('Post Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="post_suffix" id="post_suffix" value="<?php echo $this->breadcrumb_trail->opt['post_suffix']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="post_anchor"><?php _e('Post Anchor', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="post_anchor" id="post_anchor" value="<?php echo $this->breadcrumb_trail->opt['post_anchor']; ?>" size="60" /><br />
							<span class="setting-description"><?php _e('The anchor template for post breadcrumbs.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e('Post Taxonomy Display', 'breadcrumb_navxt'); ?>
						</th>
						<td>
							<label for="post_taxonomy_display">
								<input name="post_taxonomy_display" type="checkbox" id="post_taxonomy_display" value="true" <?php checked(true, $this->breadcrumb_trail->opt['post_taxonomy_display']); ?> />
								<?php _e('Show the taxonomy leading to a post in the breadcrumb trail.', 'breadcrumb_navxt'); ?>
							</label>							
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e('Post Taxonomy', 'breadcrumb_navxt'); ?>
						</th>
						<td>
							<label>
								<input name="post_taxonomy_type" type="radio" value="category" class="togx" <?php checked('category', $this->breadcrumb_trail->opt['post_taxonomy_type']); ?> />
								<?php _e('Categories'); ?>
							</label>
							<br/>
							<label>
								<input name="post_taxonomy_type" type="radio" value="date" class="togx" <?php checked('date', $this->breadcrumb_trail->opt['post_taxonomy_type']); ?> />
								<?php _e('Dates'); ?>								
							</label>
							<br/>
							<label>
								<input name="post_taxonomy_type" type="radio" value="post_tag" class="togx" <?php checked('post_tag', $this->breadcrumb_trail->opt['post_taxonomy_type']); ?> />
								<?php _e('Tags'); ?>								
							</label>
							<br/>
							<?php
								//Loop through all of the taxonomies in the array
								foreach($wp_taxonomies as $taxonomy)
								{
									//We only want custom taxonomies
									if($taxonomy->object_type == 'post' && ($taxonomy->name != 'post_tag' && $taxonomy->name != 'category'))
									{
										?>
							<label>
								<input name="post_taxonomy_type" type="radio" value="<?php echo $taxonomy->name; ?>" class="togx" <?php checked($taxonomy->name, $this->breadcrumb_trail->opt['post_taxonomy_type']); ?> />
								<?php echo ucwords(__($taxonomy->label)); ?>							
							</label>
							<br/>
										<?php
									}
								}
							?>
							<span class="setting-description"><?php _e('The taxonomy which the breadcrumb trail will show.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="page_prefix"><?php _e('Page Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="page_prefix" id="page_prefix" value="<?php echo $this->breadcrumb_trail->opt['page_prefix']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="page_suffix"><?php _e('Page Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="page_suffix" id="page_suffix" value="<?php echo $this->breadcrumb_trail->opt['page_suffix']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="page_anchor"><?php _e('Page Anchor', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="page_anchor" id="page_anchor" value="<?php echo $this->breadcrumb_trail->opt['page_anchor']; ?>" size="60" /><br />
							<span class="setting-description"><?php _e('The anchor template for page breadcrumbs.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="attachment_prefix"><?php _e('Attachment Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="attachment_prefix" id="attachment_prefix" value="<?php echo $this->breadcrumb_trail->opt['attachment_prefix']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="attachment_suffix"><?php _e('Attachment Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="attachment_suffix" id="attachment_suffix" value="<?php echo $this->breadcrumb_trail->opt['attachment_suffix']; ?>" size="32" />
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset id="category" class="bcn_options">
				<h3><?php _e('Categories', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="category_prefix"><?php _e('Category Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="category_prefix" id="category_prefix" value="<?php echo $this->breadcrumb_trail->opt['category_prefix']; ?>" size="32" /><br />
							<span class="setting-description"><?php _e('Applied before the anchor on all category breadcrumbs.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="category_suffix"><?php _e('Category Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="category_suffix" id="category_suffix" value="<?php echo $this->breadcrumb_trail->opt['category_suffix']; ?>" size="32" /><br />
							<span class="setting-description"><?php _e('Applied after the anchor on all category breadcrumbs.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="category_anchor"><?php _e('Category Anchor', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="category_anchor" id="category_anchor" value="<?php echo $this->breadcrumb_trail->opt['category_anchor']; ?>" size="60" /><br />
							<span class="setting-description"><?php _e('The anchor template for category breadcrumbs.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="archive_category_prefix"><?php _e('Archive by Category Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="archive_category_prefix" id="archive_category_prefix" value="<?php echo $this->breadcrumb_trail->opt['archive_category_prefix']; ?>" size="32" /><br />
							<span class="setting-description"><?php _e('Applied before the title of the current item breadcrumb on an archive by cateogry page.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="archive_category_suffix"><?php _e('Archive by Category Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="archive_category_suffix" id="archive_category_suffix" value="<?php echo $this->breadcrumb_trail->opt['archive_category_suffix']; ?>" size="32" /><br />
							<span class="setting-description"><?php _e('Applied after the title of the current item breadcrumb on an archive by cateogry page.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset id="post_tag" class="bcn_options">
				<h3><?php _e('Tags', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="post_tag_prefix"><?php _e('Tag Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="post_tag_prefix" id="post_tag_prefix" value="<?php echo $this->breadcrumb_trail->opt['post_tag_prefix']; ?>" size="32" /><br />
							<span class="setting-description"><?php _e('Applied before the anchor on all tag breadcrumbs.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="post_tag_suffix"><?php _e('Tag Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="post_tag_suffix" id="post_tag_suffix" value="<?php echo $this->breadcrumb_trail->opt['post_tag_suffix']; ?>" size="32" /><br />
							<span class="setting-description"><?php _e('Applied after the anchor on all tag breadcrumbs.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="post_tag_anchor"><?php _e('Tag Anchor', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="post_tag_anchor" id="post_tag_anchor" value="<?php echo $this->breadcrumb_trail->opt['post_tag_anchor']; ?>" size="60" /><br />
							<span class="setting-description"><?php _e('The anchor template for tag breadcrumbs.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="archive_post_tag_prefix"><?php _e('Archive by Tag Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="archive_post_tag_prefix" id="archive_post_tag_prefix" value="<?php echo $this->breadcrumb_trail->opt['archive_post_tag_prefix']; ?>" size="32" /><br />
							<span class="setting-description"><?php _e('Applied before the title of the current item breadcrumb on an archive by tag page.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="archive_post_tag_suffix"><?php _e('Archive by Tag Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="archive_post_tag_suffix" id="archive_post_tag_suffix" value="<?php echo $this->breadcrumb_trail->opt['archive_post_tag_suffix']; ?>" size="32" /><br />
							<span class="setting-description"><?php _e('Applied after the title of the current item breadcrumb on an archive by tag page.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
				</table>
			</fieldset>
			<?php 
			//Loop through all of the taxonomies in the array
			foreach($wp_taxonomies as $taxonomy)
			{
				//We only want custom taxonomies
				if($taxonomy->object_type == 'post' && ($taxonomy->name != 'post_tag' && $taxonomy->name != 'category'))
				{
				?>
			<fieldset id="<?php echo $taxonomy->name; ?>" class="bcn_options">
				<h3><?php echo ucwords(__($taxonomy->label)); ?></h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="<?php echo $taxonomy->name; ?>_prefix"><?php printf(__('%s Prefix', 'breadcrumb_navxt'), ucwords(__($taxonomy->label))); ?></label>
						</th>
						<td>
							<input type="text" name="<?php echo $taxonomy->name; ?>_prefix" id="<?php echo $taxonomy->name; ?>_prefix" value="<?php echo $this->breadcrumb_trail->opt[$taxonomy->name . '_prefix']; ?>" size="32" /><br />
							<span class="setting-description"><?php printf(__('Applied before the anchor on all %s breadcrumbs.', 'breadcrumb_navxt'), strtolower(__($taxonomy->label))); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="<?php echo $taxonomy->name; ?>_suffix"><?php printf(__('%s Suffix', 'breadcrumb_navxt'), ucwords(__($taxonomy->label))); ?></label>
						</th>
						<td>
							<input type="text" name="<?php echo $taxonomy->name; ?>_suffix" id="<?php echo $taxonomy->name; ?>_suffix" value="<?php echo $this->breadcrumb_trail->opt[$taxonomy->name . '_suffix']; ?>" size="32" /><br />
							<span class="setting-description"><?php printf(__('Applied after the anchor on all %s breadcrumbs.', 'breadcrumb_navxt'), strtolower(__($taxonomy->label))); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="<?php echo $taxonomy->name; ?>_anchor"><?php printf(__('%s Anchor', 'breadcrumb_navxt'), ucwords(__($taxonomy->label))); ?></label>
						</th>
						<td>
							<input type="text" name="<?php echo $taxonomy->name; ?>_anchor" id="<?php echo $taxonomy->name; ?>_anchor" value="<?php echo $this->breadcrumb_trail->opt[$taxonomy->name . '_anchor']; ?>" size="60" /><br />
							<span class="setting-description"><?php printf(__('The anchor template for %s breadcrumbs.', 'breadcrumb_navxt'), strtolower(__($taxonomy->label))); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="archive_<?php echo $taxonomy->name; ?>_prefix"><?php printf(__('Archive by %s Prefix', 'breadcrumb_navxt'), ucwords(__($taxonomy->label))); ?></label>
						</th>
						<td>
							<input type="text" name="archive_<?php echo $taxonomy->name; ?>_prefix" id="archive_<?php echo $taxonomy->name; ?>_prefix" value="<?php echo $this->breadcrumb_trail->opt['archive_' . $taxonomy->name . '_prefix']; ?>" size="32" /><br />
							<span class="setting-description"><?php printf(__('Applied before the title of the current item breadcrumb on an archive by %s page.', 'breadcrumb_navxt'), strtolower(__($taxonomy->label))); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="archive_<?php echo $taxonomy->name; ?>_suffix"><?php printf(__('Archive by %s Suffix', 'breadcrumb_navxt'), ucwords(__($taxonomy->label))); ?></label>
						</th>
						<td>
							<input type="text" name="archive_<?php echo $taxonomy->name; ?>_suffix" id="archive_<?php echo $taxonomy->name; ?>_suffix" value="<?php echo $this->breadcrumb_trail->opt['archive_' . $taxonomy->name . '_suffix']; ?>" size="32" /><br />
							<span class="setting-description"><?php printf(__('Applied after the title of the current item breadcrumb on an archive by %s page.', 'breadcrumb_navxt'), strtolower(__($taxonomy->label))); ?></span>
						</td>
					</tr>
				</table>
			</fieldset>
				<?php
				}
			}
			?>
			<fieldset id="date" class="bcn_options">
				<h3><?php _e('Date Archives', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="archive_date_prefix"><?php _e('Archive by Date Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="archive_date_prefix" id="archive_date_prefix" value="<?php echo $this->breadcrumb_trail->opt['archive_date_prefix']; ?>" size="32" /><br />
							<span class="setting-description"><?php _e('Applied before the anchor on all date breadcrumbs.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="archive_date_suffix"><?php _e('Archive by Date Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="archive_date_suffix" id="archive_date_suffix" value="<?php echo $this->breadcrumb_trail->opt['archive_date_suffix']; ?>" size="32" /><br />
							<span class="setting-description"><?php _e('Applied after the anchor on all date breadcrumbs.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="date_anchor"><?php _e('Date Anchor', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="date_anchor" id="date_anchor" value="<?php echo $this->breadcrumb_trail->opt['date_anchor']; ?>" size="60" /><br />
							<span class="setting-description"><?php _e('The anchor template for date breadcrumbs.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset id="miscellaneous" class="bcn_options">
				<h3><?php _e('Miscellaneous', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="author_prefix"><?php _e('Author Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="author_prefix" id="author_prefix" value="<?php echo $this->breadcrumb_trail->opt['author_prefix']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="author_suffix"><?php _e('Author Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="author_suffix" id="author_suffix" value="<?php echo $this->breadcrumb_trail->opt['author_suffix']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="author_display"><?php _e('Author Display Format', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<select name="author_display" id="author_display">
								<?php $this->select_options('author_display', array("display_name", "nickname", "first_name", "last_name")); ?>
							</select><br />
							<span class="setting-description"><?php _e('display_name uses the name specified in "Display name publicly as" under the user profile the others correspond to options in the user profile.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="search_prefix"><?php _e('Search Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="search_prefix" id="search_prefix" value="<?php echo $this->breadcrumb_trail->opt['search_prefix']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="search_suffix"><?php _e('Search Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="search_suffix" id="search_suffix" value="<?php echo $this->breadcrumb_trail->opt['search_suffix']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="search_anchor"><?php _e('Search Anchor', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="search_anchor" id="search_anchor" value="<?php echo $this->breadcrumb_trail->opt['search_anchor']; ?>" size="60" /><br />
							<span class="setting-description"><?php _e('The anchor template for search breadcrumbs, used only when the search results span several pages.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="id404_title"><?php _e('404 Title', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="404_title" id="id404_title" value="<?php echo $this->breadcrumb_trail->opt['404_title']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="id404_prefix"><?php _e('404 Prefix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="404_prefix" id="id404_prefix" value="<?php echo $this->breadcrumb_trail->opt['404_prefix']; ?>" size="32" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="id404_suffix"><?php _e('404 Suffix', 'breadcrumb_navxt'); ?></label>
						</th>
						<td>
							<input type="text" name="404_suffix" id="id404_suffix" value="<?php echo $this->breadcrumb_trail->opt['404_suffix']; ?>" size="32" />
						</td>
					</tr>
				</table>
			</fieldset>
			</div>
			<p class="submit"><input type="submit" class="button-primary" name="bcn_admin_options" value="<?php _e('Save Changes') ?>" /></p>
		</form>
		<div id="bcn_import_export_relocate">
			<form action="options-general.php?page=breadcrumb-navxt" method="post" enctype="multipart/form-data" id="bcn_admin_upload">
				<?php wp_nonce_field('bcn_admin_upload');?>
					<fieldset id="import_export" class="bcn_options">
						<h3><?php _e('Import/Export/Reset Settings', 'breadcrumb_navxt'); ?></h3>
						<p><?php _e('Import Breadcrumb NavXT settings from a XML file, export the current settings to a XML file, or reset to the default Breadcrumb NavXT settings.', 'breadcrumb_navxt');?></p>
						<table class="form-table">
							<tr valign="top">
								<th scope="row">
									<label for="bcn_admin_import_file"><?php _e('Settings File', 'breadcrumb_navxt'); ?></label>
								</th>
								<td>
									<input type="file" name="bcn_admin_import_file" id="bcn_admin_import_file" size="32"/><br />
									<span class="setting-description"><?php _e('Select a XML settings file to upload and import settings from.', 'breadcrumb_navxt'); ?></span>
								</td>
							</tr>
						</table>
						<p class="submit">
							<input type="submit" class="button" name="bcn_admin_import" value="<?php _e('Import', 'breadcrumb_navxt') ?>" onclick="return bcn_confirm('import')" />
							<input type="submit" class="button" name="bcn_admin_export" value="<?php _e('Export', 'breadcrumb_navxt') ?>" />
							<input type="submit" class="button" name="bcn_admin_reset" value="<?php _e('Reset', 'breadcrumb_navxt') ?>" onclick="return bcn_confirm('reset')" />
						</p>
					</fieldset>
			</form>
		</div>
		</div>
		<?php
	}
	/**
	 * select_options
	 *
	 * Displays wordpress options as <seclect> options defaults to true/false
	 *
	 * @param string $optionname name of wordpress options store
	 * @param array $options array of names of options that can be selected
	 * @param array $exclude[optional] array of names in $options array to be excluded
	 */
	function select_options($optionname, $options, $exclude = array())
	{
		$value = $this->breadcrumb_trail->opt[$optionname];
		//First output the current value
		if($value)
		{
			printf('<option>%s</option>', $value);
		}
		//Now do the rest
		foreach($options as $option)
		{
			//Don't want multiple occurance of the current value
			if($option != $value && !in_array($option, $exclude))
			{
				printf('<option>%s</option>', $option);
			}
		}
	}
	/**
	 * add_option
	 *
	 * This inserts the value into the option name, WPMU safe
	 *
	 * @param (string) key name where to save the value in $value
	 * @param (mixed) value to insert into the options db
	 * @return (bool)
	 */
	function add_option($key, $value)
	{
		return add_option($key, $value);
	}
	/**
	 * delete_option
	 *
	 * This removes the option name, WPMU safe
	 *
	 * @param (string) key name of the option to remove
	 * @return (bool)
	 */
	function delete_option($key)
	{
		return delete_option($key);
	}
	/**
	 * update_option
	 *
	 * This updates the value into the option name, WPMU safe
	 *
	 * @param (string) key name where to save the value in $value
	 * @param (mixed) value to insert into the options db
	 * @return (bool)
	 */
	function update_option($key, $value)
	{
		return update_option($key, $value);
	}
	/**
	 * get_option
	 *
	 * This grabs the the data from the db it is WPMU safe and can place the data 
	 * in a HTML form safe manner.
	 *
	 * @param  (string) key name of the wordpress option to get
	 * @param  (bool)   safe output for HTML forms (default: false)
	 * @return (mixed)  value of option
	 */
	function get_option($key, $safe = false)
	{
		$db_data = get_option($key);
		if($safe)
		{
			//If we get an array, we should loop through all of its members
			if(is_array($db_data))
			{
				//Loop through all the members
				foreach($db_data as $key=>$item)
				{
					//We ignore anything but strings
					if(is_string($item))
					{
						$db_data[$key] = htmlentities($item, ENT_COMPAT, 'UTF-8');
					}
				}
			}
			else
			{
				$db_data = htmlentities($db_data, ENT_COMPAT, 'UTF-8');
			}
		}
		return $db_data;
	}
	/**
	 * notify
	 * 
	 * Output a 'notify' box with a message after an event occurs
	 * 
	 * @param $message string the message to deliver
	 * @param $error bool[optional] is the message an error?
	 */
	function notify($message, $error = false)
	{
		//If the message is an error use the appropriate class
		$class = $error ? 'error' : 'updated fade';
		printf('<div class="%s"><p>%s</p></div>', $class, $message);		
	}
	
	/**
	 * callback function for admin_notices
	 * 
	 * @return void
	 */	
	function notify_import_failure()
	{
		$this->notify(__('Importing settings from file failed.', 'breadcrumb_navxt'), true);
	}
	
	/**
	 * callback function for admin_notices
	 * 
	 * @return void
	 */	
	function notify_import_success()
	{
		$this->notify(__('The Breadcrumb NavXT settings were successfully imported from file.', 'breadcrumb_navxt'));
	}

	/**
	 * callback function for admin_notices
	 * 
	 * @return void
	 */	
	function notify_reset()
	{
		$this->notify(__('The Breadcrumb NavXT settings were reset to the default values.', 'breadcrumb_navxt'));
	}
}
//Let's make an instance of our object takes care of everything
$bcn_admin = new bcn_admin;
/**
 * A wrapper for the internal function in the class
 * 
 * @param bool $return Whether to return or echo the trail. (optional)
 * @param bool $linked Whether to allow hyperlinks in the trail or not. (optional)
 * @param bool $reverse Whether to reverse the output or not. (optional)
 */
function bcn_display($return = false, $linked = true, $reverse = false)
{
	global $bcn_admin;
	return $bcn_admin->display($return, $linked, $reverse);
}
/**
 * A wrapper for the internal function in the class
 * 
 * @param  bool $return  Whether to return or echo the trail. (optional)
 * @param  bool $linked  Whether to allow hyperlinks in the trail or not. (optional)
 * @param  bool $reverse Whether to reverse the output or not. (optional)
 */
function bcn_display_list($return = false, $linked = true, $reverse = false)
{
	global $bcn_admin;
	return $bcn_admin->display_list($return, $linked, $reverse);
}
