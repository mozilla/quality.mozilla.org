<?php
/*
Plugin Name: Breadcrumb NavXT
Plugin URI: http://mtekk.weblogs.us/code/breadcrumb-navxt/
Description: Adds a breadcrumb navigation showing the visitor&#39;s path to their current location. For details on how to use this plugin visit <a href="http://mtekk.weblogs.us/code/breadcrumb-navxt/">Breadcrumb NavXT</a>. 
Version: 3.5.1
Author: John Havlik
Author URI: http://mtekk.weblogs.us/
*/
/*  Copyright 2007-2010  John Havlik  (email : mtekkmonkey@gmail.com)

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
//Do a PHP version check
$phpVersion = explode('.', phpversion());
if($phpVersion[0] < 5)
{
	sprintf(__('Your PHP version is too old, please upgrade to a newer version. Your version is %s, this plugin requires %s', 'breadcrumb_navxt'), phpversion(), '5.0.0');
	die();
}
//Include the breadcrumb class
require_once(dirname(__FILE__) . '/breadcrumb_navxt_class.php');
//Include the WP 2.8+ widget class
require_once(dirname(__FILE__) . '/breadcrumb_navxt_widget.php');
//Include admin base class
if(!class_exists('mtekk_admin'))
{
	require_once(dirname(__FILE__) . '/mtekk_admin_class.php');
}
/**
 * The administrative interface class 
 * 
 */
class bcn_admin extends mtekk_admin
{
	/**
	 * local store for breadcrumb version
	 * 
	 * @var   string
	 */
	protected $version = '3.5.1';
	protected $full_name = 'Breadcrumb NavXT Settings';
	protected $short_name = 'Breadcrumb NavXT';
	protected $access_level = 'manage_options';
	protected $identifier = 'breadcrumb_navxt';
	protected $unique_prefix = 'bcn';
	protected $plugin_basename = 'breadcrumb-navxt/breadcrumb_navxt_admin.php';
	/**
	 * local store for the breadcrumb object
	 * 
	 * @see   bcn_admin()
	 * @var   bcn_breadcrumb
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
		//Sync up our option array (not with db however)
		$this->opt = $this->breadcrumb_trail->opt;
		//We set the plugin basename here, could manually set it, but this is for demonstration purposes
		//$this->plugin_base = plugin_basename(__FILE__);
		//Register the WordPress 2.8 Widget
		add_action('widgets_init', create_function('', 'return register_widget("'. $this->unique_prefix . '_widget");'));
		//We're going to make sure we load the parent's constructor
		parent::__construct();
	}
	/**
	 * admin initialisation callback function
	 * 
	 * is bound to wpordpress action 'admin_init' on instantiation
	 * 
	 * @since  3.2.0
	 * @return void
	 */
	function init()
	{
		//We're going to make sure we run the parent's version of this function as well
		parent::init();	
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
		if(!current_user_can($this->access_level))
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
		global $wp_taxonomies;
		//Call our little security function
		$this->security();
		//Reduce db queries by saving this
		$db_version = $this->get_option('bcn_version');
		//If our version is not the same as in the db, time to update
		if($db_version !== $this->version)
		{
			//Split up the db version into it's components
			list($major, $minor, $release) = explode('.', $db_version);
			$opts = $this->get_option('bcn_options');
			//Upgrading from 3.0
			if($major == 3 && $minor == 0)
			{
				$opts['search_anchor'] = __('<a title="Go to the first page of search results for %title%." href="%link%">','breadcrumb_navxt');
			}
			else if($major == 3 && $minor < 3)
			{
				$opts['blog_display'] = true;
			}
			else if($major == 3 && $minor < 4)
			{
				//Inline upgrade of the tag setting
				if($opts['post_taxonomy_type'] === 'tag')
				{
					$opts['post_taxonomy_type'] = 'post_tag';
				}
				//Fix our tag settings
				$opts['archive_post_tag_prefix'] = $this->breadcrumb_trail->opt['archive_tag_prefix'];
				$opts['archive_post_tag_suffix'] = $this->breadcrumb_trail->opt['archive_tag_suffix'];
				$opts['post_tag_prefix'] = $this->breadcrumb_trail->opt['tag_prefix'];
				$opts['post_tag_suffix'] = $this->breadcrumb_trail->opt['tag_suffix'];
				$opts['post_tag_anchor'] = $this->breadcrumb_trail->opt['tag_anchor'];
			}
			//If it was never installed, copy over default settings
			else if(!$opts)
			{
				$opts = $this->opt;
			}
			//We'll add our custom taxonomy stuff at this time
			foreach($wp_taxonomies as $taxonomy)
			{
				//We only want custom taxonomies
				if(($taxonomy->object_type == 'post' || is_array($taxonomy->object_type) && in_array('post', $taxonomy->object_type)) && ($taxonomy->name != 'post_tag' && $taxonomy->name != 'category'))
				{
					//If the taxonomy does not have settings in the options array yet, we need to load some defaults
					if(!array_key_exists($taxonomy->name . '_anchor', $opts))
					{
						$opts[$taxonomy->name . '_prefix'] = '';
						$opts[$taxonomy->name . '_suffix'] = '';
						$opts[$taxonomy->name . '_anchor'] = __(sprintf('<a title="Go to the %%title%% %s archives." href="%%link%%">',  ucwords(__($taxonomy->label))), 'breadcrumb_navxt');
						$opts['archive_' . $taxonomy->name . '_prefix'] = '';
						$opts['archive_' . $taxonomy->name . '_suffix'] = '';
					}
				}
			}
			//Always have to update the version
			$this->update_option('bcn_version', $this->version);
			//Store the options
			$this->add_option('bcn_options', $opts);
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
				strlen($temp['post_tag_anchor']) == 0 ||
				strlen($temp['date_anchor']) == 0 ||
				strlen($temp['category_anchor']) == 0)
			{
				$this->delete_option('bcn_options');
				$this->add_option('bcn_options', $this->breadcrumb_trail->opt);
			}
		}
	}
	/**
	 * ops_update
	 * 
	 * Updates the database settings from the webform
	 */
	function opts_update()
	{
		global $wp_taxonomies;
		//Do some security related thigns as we are not using the normal WP settings API
		$this->security();
		//Do a nonce check, prevent malicious link/form problems
		check_admin_referer('bcn_options-options');
		//We'll add our custom taxonomy stuff at this time
		foreach($wp_taxonomies as $taxonomy)
		{
			//We only want custom taxonomies
			if(($taxonomy->object_type == 'post' || is_array($taxonomy->object_type) && in_array('post', $taxonomy->object_type)) && ($taxonomy->name != 'post_tag' && $taxonomy->name != 'category'))
			{
				//If the taxonomy does not have settings in the options array yet, we need to load some defaults
				if(!array_key_exists($taxonomy->name . '_anchor', $this->opt))
				{
					$this->opt[$taxonomy->name . '_prefix'] = '';
					$this->opt[$taxonomy->name . '_suffix'] = '';
					$this->opt[$taxonomy->name . '_anchor'] = __(sprintf('<a title="Go to the %%title%% %s archives." href="%%link%%">',  ucwords(__($taxonomy->label))), 'breadcrumb_navxt');
					$this->opt['archive_' . $taxonomy->name . '_prefix'] = '';
					$this->opt['archive_' . $taxonomy->name . '_suffix'] = '';
				}
			}
		}
		//Grab our incomming array (the data is dirty)
		$input = $_POST['bcn_options'];
		//Loop through all of the existing options (avoids random setting injection)
		foreach($this->opt as $option => $value)
		{
			//Handle all of our boolean options first
			if(strpos($option, 'display') > 0 || $option == 'current_item_linked')
			{
				$this->opt[$option] = isset($input[$option]);
			}
			//Now handle anything that can't be blank
			else if(strpos($option, 'anchor') > 0)
			{
				//Only save a new anchor if not blank
				if(isset($input[$option]))
				{
					//Do excess slash removal sanitation
					$this->opt[$option] = stripslashes($input[$option]);
				}
			}
			//Now everything else
			else
			{
				$this->opt[$option] = stripslashes($input[$option]);
			}
		}
		//Commit the option changes
		$this->update_option('bcn_options', $this->opt);
		//Let the user know everything went ok
		$this->message['updated fade'][] = __('Settings successfully saved.', $this->identifier);
		add_action('admin_notices', array($this, 'message'));
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
	 * get help text
	 * 
	 * @return string
	 */
	protected function _get_help_text()
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
		jQuery("#hasadmintabs").tabs();
		/* handler for opening the last tab after submit (compability version) */
		jQuery('#hasadmintabs ul a').click(function(i){
			var form   = jQuery('#bcn_admin-options');
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
		jQuery('#screen-meta').prepend(
				'<div id="screen-options-wrap" class="hidden"></div>'
		);
		jQuery('#screen-meta-links').append(
				'<div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle">' +
				'<a class="show-settings" id="show-settings-link" href="#screen-options"><?php printf('%s/%s/%s', __('Import', 'breadcrumb_navxt'), __('Export', 'breadcrumb_navxt'), __('Reset', 'breadcrumb_navxt')); ?></a>' + 
				'</div>'
		);
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
	 * admin_page
	 * 
	 * The administrative page for Breadcrumb NavXT
	 * 
	 */
	function admin_page()
	{
		global $wp_taxonomies;
		$this->security();
		//Grab the current settings from the DB
		$this->opt = $this->get_option('bcn_options');?>
		<div class="wrap"><h2><?php _e('Breadcrumb NavXT Settings', 'breadcrumb_navxt'); ?></h2>		
		<p<?php if($this->_has_contextual_help): ?> class="hide-if-js"<?php endif; ?>><?php 
			print $this->_get_help_text();
		?></p>
		<form action="options-general.php?page=breadcrumb_navxt" method="post" id="bcn_admin-options">
			<?php settings_fields('bcn_options');?>
			<div id="hasadmintabs">
			<fieldset id="general" class="bcn_options">
				<h3><?php _e('General', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<?php
						$this->input_text(__('Breadcrumb Separator', 'breadcrumb_navxt'), 'separator', '32', false, __('Placed in between each breadcrumb.', 'breadcrumb_navxt'));
						$this->input_text(__('Breadcrumb Max Title Length', 'breadcrumb_navxt'), 'max_title_length', '10');
					?>
					<tr valign="top">
						<th scope="row">
							<?php _e('Home Breadcrumb', 'breadcrumb_navxt'); ?>						
						</th>
						<td>
							<label>
								<input name="bcn_options[home_display]" type="checkbox" id="home_display" value="true" <?php checked(true, $this->opt['home_display']); ?> />
								<?php _e('Place the home breadcrumb in the trail.', 'breadcrumb_navxt'); ?>				
							</label><br />
							<ul>
								<li>
									<label for="home_title">
										<?php _e('Home Title: ','breadcrumb_navxt');?>
										<input type="text" name="bcn_options[home_title]" id="home_title" value="<?php echo htmlentities($this->opt['home_title'], ENT_COMPAT, 'UTF-8'); ?>" size="20" />
									</label>
								</li>
							</ul>							
						</td>
					</tr>
					<?php
						$this->input_check(__('Blog Breadcrumb', 'breadcrumb_navxt'), 'blog_display', __('Place the blog breadcrumb in the trail.', 'breadcrumb_navxt'), ($this->get_option('show_on_front') !== "page"));
						$this->input_text(__('Home Prefix', 'breadcrumb_navxt'), 'home_prefix', '32');
						$this->input_text(__('Home Suffix', 'breadcrumb_navxt'), 'home_suffix', '32');
						$this->input_text(__('Home Anchor', 'breadcrumb_navxt'), 'home_anchor', '64', false, __('The anchor template for the home breadcrumb.', 'breadcrumb_navxt'));
						$this->input_text(__('Blog Anchor', 'breadcrumb_navxt'), 'blog_anchor', '64', ($this->get_option('show_on_front') !== "page"), __('The anchor template for the blog breadcrumb, used only in static front page environments.', 'breadcrumb_navxt'));
					?>
				</table>
			</fieldset>
			<fieldset id="current" class="bcn_options">
				<h3><?php _e('Current Item', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<?php
						$this->input_check(__('Link Current Item', 'breadcrumb_navxt'), 'current_item_linked', __('Yes'));
						$this->input_text(__('Current Item Prefix', 'breadcrumb_navxt'), 'current_item_prefix', '32', false, __('This is always placed in front of the last breadcrumb in the trail, before any other prefixes for that breadcrumb.', 'breadcrumb_navxt'));
						$this->input_text(__('Current Item Suffix', 'breadcrumb_navxt'), 'current_item_suffix', '32', false, __('This is always placed after the last breadcrumb in the trail, and after any other prefixes for that breadcrumb.', 'breadcrumb_navxt'));
						$this->input_text(__('Current Item Anchor', 'breadcrumb_navxt'), 'current_item_anchor', '64', false, __('The anchor template for current item breadcrumbs.', 'breadcrumb_navxt'));
						$this->input_check(__('Paged Breadcrumb', 'breadcrumb_navxt'), 'paged_display', __('Include the paged breadcrumb in the breadcrumb trail.', 'breadcrumb_navxt'), false, __('Indicates that the user is on a page other than the first on paginated posts/pages.', 'breadcrumb_navxt'));
						$this->input_text(__('Paged Prefix', 'breadcrumb_navxt'), 'paged_prefix', '32');
						$this->input_text(__('Paged Suffix', 'breadcrumb_navxt'), 'paged_suffix', '32');
					?>
				</table>
			</fieldset>
			<fieldset id="single" class="bcn_options">
				<h3><?php _e('Posts &amp; Pages', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<?php
						$this->input_text(__('Post Prefix', 'breadcrumb_navxt'), 'post_prefix', '32');
						$this->input_text(__('Post Suffix', 'breadcrumb_navxt'), 'post_suffix', '32');
						$this->input_text(__('Post Anchor', 'breadcrumb_navxt'), 'post_anchor', '64', false, __('The anchor template for post breadcrumbs.', 'breadcrumb_navxt'));
						$this->input_check(__('Post Taxonomy Display', 'breadcrumb_navxt'), 'post_taxonomy_display', __('Show the taxonomy leading to a post in the breadcrumb trail.', 'breadcrumb_navxt'));
					?>
					<tr valign="top">
						<th scope="row">
							<?php _e('Post Taxonomy', 'breadcrumb_navxt'); ?>
						</th>
						<td>
							<?php
								$this->input_radio('post_taxonomy_type', 'category', __('Categories'));
								$this->input_radio('post_taxonomy_type', 'date', __('Dates'));
								$this->input_radio('post_taxonomy_type', 'post_tag', __('Tags'));
								$this->input_radio('post_taxonomy_type', 'page', __('Pages'));
								//Loop through all of the taxonomies in the array
								foreach($wp_taxonomies as $taxonomy)
								{
									//We only want custom taxonomies
									if(($taxonomy->object_type == 'post' || is_array($taxonomy->object_type) && in_array('post', $taxonomy->object_type)) && ($taxonomy->name != 'post_tag' && $taxonomy->name != 'category'))
									{
										$this->input_radio('post_taxonomy_type', $taxonomy->name, ucwords(__($taxonomy->label)));
									}
								}
							?>
							<span class="setting-description"><?php _e('The taxonomy which the breadcrumb trail will show.', 'breadcrumb_navxt'); ?></span>
						</td>
					</tr>
					<?php
						$this->input_text(__('Page Prefix', 'breadcrumb_navxt'), 'page_prefix', '32');
						$this->input_text(__('Page Suffix', 'breadcrumb_navxt'), 'page_suffix', '32');
						$this->input_text(__('Page Anchor', 'breadcrumb_navxt'), 'page_anchor', '64', false, __('The anchor template for page breadcrumbs.', 'breadcrumb_navxt'));
						$this->input_text(__('Attachment Prefix', 'breadcrumb_navxt'), 'attachment_prefix', '32');
						$this->input_text(__('Attachment Suffix', 'breadcrumb_navxt'), 'attachment_suffix', '32');
					?>
				</table>
			</fieldset>
			<fieldset id="category" class="bcn_options">
				<h3><?php _e('Categories', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<?php
						$this->input_text(__('Category Prefix', 'breadcrumb_navxt'), 'category_prefix', '32', false, __('Applied before the anchor on all category breadcrumbs.', 'breadcrumb_navxt'));
						$this->input_text(__('Category Suffix', 'breadcrumb_navxt'), 'category_suffix', '32', false, __('Applied after the anchor on all category breadcrumbs.', 'breadcrumb_navxt'));
						$this->input_text(__('Category Anchor', 'breadcrumb_navxt'), 'category_anchor', '64', false, __('The anchor template for category breadcrumbs.', 'breadcrumb_navxt'));
						$this->input_text(__('Archive by Category Prefix', 'breadcrumb_navxt'), 'archive_category_prefix', '32', false, __('Applied before the title of the current item breadcrumb on an archive by cateogry page.', 'breadcrumb_navxt'));
						$this->input_text(__('Archive by Category Suffix', 'breadcrumb_navxt'), 'archive_category_suffix', '32', false, __('Applied after the title of the current item breadcrumb on an archive by cateogry page.', 'breadcrumb_navxt'));
					?>
				</table>
			</fieldset>
			<fieldset id="post_tag" class="bcn_options">
				<h3><?php _e('Tags', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<?php
						$this->input_text(__('Tag Prefix', 'breadcrumb_navxt'), 'post_tag_prefix', '32', false, __('Applied before the anchor on all tag breadcrumbs.', 'breadcrumb_navxt'));
						$this->input_text(__('Tag Suffix', 'breadcrumb_navxt'), 'post_tag_suffix', '32', false, __('Applied after the anchor on all tag breadcrumbs.', 'breadcrumb_navxt'));
						$this->input_text(__('Tag Anchor', 'breadcrumb_navxt'), 'post_tag_anchor', '64', false, __('The anchor template for tag breadcrumbs.', 'breadcrumb_navxt'));
						$this->input_text(__('Archive by Tag Prefix', 'breadcrumb_navxt'), 'archive_post_tag_prefix', '32', false, __('Applied before the title of the current item breadcrumb on an archive by tag page.', 'breadcrumb_navxt'));
						$this->input_text(__('Archive by Tag Suffix', 'breadcrumb_navxt'), 'archive_post_tag_suffix', '32', false, __('Applied after the title of the current item breadcrumb on an archive by tag page.', 'breadcrumb_navxt'));
					?>
				</table>
			</fieldset>
			<?php 
			//Loop through all of the taxonomies in the array
			foreach($wp_taxonomies as $taxonomy)
			{
				//We only want custom taxonomies
				if(($taxonomy->object_type == 'post' || is_array($taxonomy->object_type) && in_array('post', $taxonomy->object_type)) && ($taxonomy->name != 'post_tag' && $taxonomy->name != 'category'))
				{
					//If the taxonomy does not have settings in the options array yet, we need to load some defaults
					if(!array_key_exists($taxonomy->name . '_anchor', $this->opt))
					{
						//Add the necessary option array members
						$this->opt[$taxonomy->name . '_prefix'] = '';
						$this->opt[$taxonomy->name . '_suffix'] = '';
						$this->opt[$taxonomy->name . '_anchor'] = __(sprintf('<a title="Go to the %%title%% %s archives." href="%%link%%">', ucwords(__($taxonomy->label))), 'breadcrumb_navxt');
						$this->opt['archive_' . $taxonomy->name . '_prefix'] = '';
						$this->opt['archive_' . $taxonomy->name . '_suffix'] = '';
						//Let's make sure that the newly available options are "registered" in our db
						$this->update_option('bcn_options', $this->opt);
					}
				?>
			<fieldset id="<?php echo $taxonomy->name; ?>" class="bcn_options">
				<h3><?php echo ucwords(__($taxonomy->label)); ?></h3>
				<table class="form-table">
					<?php
						$this->input_text(sprintf(__('%s Prefix', 'breadcrumb_navxt'), ucwords(__($taxonomy->label))), $taxonomy->name . '_prefix', '32', false, sprintf(__('Applied before the anchor on all %s breadcrumbs.', 'breadcrumb_navxt'), strtolower(__($taxonomy->label))));
						$this->input_text(sprintf(__('%s Suffix', 'breadcrumb_navxt'), ucwords(__($taxonomy->label))), $taxonomy->name . '_suffix', '32', false, sprintf(__('Applied after the anchor on all %s breadcrumbs.', 'breadcrumb_navxt'), strtolower(__($taxonomy->label))));
						$this->input_text(sprintf(__('%s Anchor', 'breadcrumb_navxt'), ucwords(__($taxonomy->label))), $taxonomy->name . '_anchor', '64', false, sprintf(__('The anchor template for %s breadcrumbs.', 'breadcrumb_navxt'), strtolower(__($taxonomy->label))));
						$this->input_text(sprintf(__('Archive by %s Prefix', 'breadcrumb_navxt'), ucwords(__($taxonomy->label))), 'archive_' . $taxonomy->name . '_prefix', '32', false, sprintf(__('Applied before the title of the current item breadcrumb on an archive by %s page.', 'breadcrumb_navxt'), strtolower(__($taxonomy->label))));
						$this->input_text(sprintf(__('Archive by %s Suffix', 'breadcrumb_navxt'), ucwords(__($taxonomy->label))), 'archive_' . $taxonomy->name . '_suffix', '32', false, sprintf(__('Applied after the title of the current item breadcrumb on an archive by %s page.', 'breadcrumb_navxt'), strtolower(__($taxonomy->label))));
					?>
				</table>
			</fieldset>
				<?php
				}
			}
			?>
			<fieldset id="date" class="bcn_options">
				<h3><?php _e('Date Archives', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<?php
						$this->input_text(__('Date Anchor', 'breadcrumb_navxt'), 'date_anchor', '64', false, __('The anchor template for date breadcrumbs.', 'breadcrumb_navxt'));
						$this->input_text(__('Archive by Date Prefix', 'breadcrumb_navxt'), 'archive_date_prefix', '32', false, __('Applied before the anchor on all date breadcrumbs.', 'breadcrumb_navxt'));
						$this->input_text(__('Archive by Date Suffix', 'breadcrumb_navxt'), 'archive_date_suffix', '32', false, __('Applied after the anchor on all date breadcrumbs.', 'breadcrumb_navxt'));
					?>
				</table>
			</fieldset>
			<fieldset id="miscellaneous" class="bcn_options">
				<h3><?php _e('Miscellaneous', 'breadcrumb_navxt'); ?></h3>
				<table class="form-table">
					<?php
						$this->input_text(__('Author Prefix', 'breadcrumb_navxt'), 'author_prefix', '32');
						$this->input_text(__('Author Suffix', 'breadcrumb_navxt'), 'author_suffix', '32');
						$this->input_select(__('Author Display Format', 'breadcrumb_navxt'), 'author_name', array("display_name", "nickname", "first_name", "last_name"), false, __('display_name uses the name specified in "Display name publicly as" under the user profile the others correspond to options in the user profile.', 'breadcrumb_navxt'));
						$this->input_text(__('Search Prefix', 'breadcrumb_navxt'), 'search_prefix', '32');
						$this->input_text(__('Search Suffix', 'breadcrumb_navxt'), 'search_suffix', '32');
						$this->input_text(__('Search Anchor', 'breadcrumb_navxt'), 'search_anchor', '64', false, __('The anchor template for search breadcrumbs, used only when the search results span several pages.', 'breadcrumb_navxt'));
						$this->input_text(__('404 Title', 'breadcrumb_navxt'), '404_title', '32');
						$this->input_text(__('404 Prefix', 'breadcrumb_navxt'), '404_prefix', '32');
						$this->input_text(__('404 Suffix', 'breadcrumb_navxt'), '404_suffix', '32');
					?>
				</table>
			</fieldset>
			</div>
			<p class="submit"><input type="submit" class="button-primary" name="bcn_admin_options" value="<?php esc_attr_e('Save Changes') ?>" /></p>
		</form>
		<?php $this->import_form(); ?>
		</div>
		<?php
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
	 * @return (mixed)  value of option
	 */
	function get_option($key)
	{
		return get_option($key);
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
		//Grab the current settings from the DB
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
		//Grab the current settings from the DB
		$this->breadcrumb_trail->opt = $this->get_option('bcn_options');
		//Generate the breadcrumb trail
		$this->breadcrumb_trail->fill();
		return $this->breadcrumb_trail->display_list($return, $linked, $reverse);
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
