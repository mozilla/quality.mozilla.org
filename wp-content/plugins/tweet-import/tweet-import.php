<?php
/*
Plugin Name: Tweet Import
Plugin URI: http://skinju.com/wordpress/tweet-import
Description: A WordPress plugin that imports twitter posts from multiple twitter accounts, favorites, and lists to WordPress. It allows importing each account, favorite or list to different categories, and allows tagging imported tweets using the tweet content #hashtags. Requires no authentication and can be used to import the user posted tweets and retweets from the specified twitter accounts and lists.
Version: 1.3.1
Author: Khaled Afiouni
Author URI: http://www.afiouni.com/
Lincense: Released under the GPL license (http://www.opensource.org/licenses/gpl-license.php)
*/

/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

//-- Global skinju functions
if (!function_exists('skinju_valid_privileges_or_die')):
function skinju_valid_privileges_or_die ($r) {if (!current_user_can($r)) wp_die ('You do not have sufficient permissions to perform the requested action.');}
endif; //skinju_valid_privileges_or_die

if (!function_exists('skinju_get_tag_link')):
function skinju_get_tag_link( $tag )
{
  global $wp_rewrite;
  $taglink = $wp_rewrite->get_tag_permastruct();
	
  if (empty($taglink ))
  {
    $file = get_option( 'home' ) . '/';
    $taglink = $file . '?tag=' . $tag;
  }
  else
  {
    $taglink = str_replace( '%tag%', $tag, $taglink );
    $taglink = get_option( 'home' ) . user_trailingslashit( $taglink, 'category' );
  }
  return apply_filters( 'tag_link', $taglink, $tag_id );
}
endif; //skinju_get_tag_link

//-- Add Admin Menus
if (!has_action ('admin_menu', 'skinju_add_admin_menus')) {add_action('admin_menu', 'skinju_add_admin_menus');}
if (!function_exists('skinju_add_admin_menus')):
function skinju_add_admin_menus()
{
  if (function_exists('add_menu_page'))
  {
    add_menu_page('skinju', 'skinju', 0, 'skinju', 'skinju_options_page');
    do_action ('skinju_add_admin_menus');
  }
}
endif; //skinju_add_admin_menus

if (!function_exists('skinju_options_page')):
function skinju_options_page()
{
  echo '<div class="wrap">';
  echo '<div id="icon-plugins" class="icon32"><br /></div>';
  echo '<h2>About the skinju Wordpress Plugin Package</h2>';
  echo '<h2>Background</h2>';
  echo '<p><a href="http://skinju.com/" target="_blank">skinju</a> is a suite of add-ons, extensions, and plugins for well known open source packages such as WordPress.</p>';
  echo '<p>This package is primarily developed by <a href="http://www.afiouni.com/" target="_blank">Khaled Afiouni</a>. The WordPress plugins released under this package are released under the GNU GPL version 3 which is basically the requirement of the folks at <a href="http://wordpress.org/" target="_blank">WordPress.org</a>. For other wordpress plugins please check the <a href="http://skinju.com/wordpress" target="_blank">skinju wordpress plugins page</a></p>';

  echo '<h2>Like those plugins?</h2>';
  echo '<p>If you like this work, you can keep up to date with the latest news and releases of <a href="http://skinju.com/" target="_blank">skinju</a> packages and plugins. You can:</p>';
  echo '<p>- Follow <a href="http://twitter.com/skinju" target="_blank">skinju on Twitter</a> to keep up to date on bug fixes and releases</p>';
  echo '<br />';
  echo '<p>You can also help spread the word and let others know about it. You can:</p>';
  echo '<p>- Link to skinju website http://skinju.com/</p>';
  echo '<p>- Give the plugins good ratings on the plugin pages on <a href="http://wordpress.org/extend/plugins/" target="_blank">WordPress.org</a></p>';

  echo '<h2>Disclaimer</h2>';
  echo '<p>This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.</p>';



  echo '</div> <!-- end wrap -->';
}
endif; //skinju_options_page


//-- Tweet Import Functions
define('TWEETIMPORT_VERSION', '1.3.1');
define('TWEETIMPORT_API_FAVORITES_URL', 'http://twitter.com/favorites/#=#USER#=#.atom');
define('TWEETIMPORT_API_USER_TIMELINE_URL', 'http://twitter.com/statuses/user_timeline/#=#USER#=#.atom');
define('TWEETIMPORT_API_LIST_URL', 'http://api.twitter.com/1/#=#USER#=#/lists/#=#LIST#=#/statuses.atom');

//-- add the schedule hook 
if (!has_action ('tweet_import_scheduled_hook', 'tweetimport_import_feeds')) {add_action('tweet_import_scheduled_hook', 'tweetimport_import_feeds');}

//Activation Hook
register_activation_hook (__FILE__, 'tweetimport_activate');
if (!function_exists('tweetimport_activate')):
function tweetimport_activate()
{
  $role = get_role('administrator');
  if (!$role->has_cap('manage_tweetimport'))
  {
    $role->add_cap('manage_tweetimport');
  }
  add_option ('skinju_tweet_import', array('version'=>TWEETIMPORT_VERSION, 'interval'=>'hourly'), '', 'no');
  $tweetimport_options = get_option ('skinju_tweet_import');
  $tweetimport_options = array_merge (array('version'=>TWEETIMPORT_VERSION, 'interval'=>'hourly'), $tweetimport_options);
  update_option ('skinju_tweet_import', $tweetimport_options);
  wp_schedule_event(time(), $tweetimport_options['interval'], 'tweet_import_scheduled_hook');
}
endif; //tweetimport_activate

register_deactivation_hook(__FILE__, 'tweetimport_deactivate');
if (!function_exists('tweetimport_deactivate')):
function tweetimport_deactivate()
{
  wp_clear_scheduled_hook('tweet_import_scheduled_hook');
}
endif; //tweetimport_deactivate
//-- Add Menu Item
if (!has_action ('skinju_add_admin_menus', 'tweetimport_add_menu')) {add_action('skinju_add_admin_menus', 'tweetimport_add_menu');}
if (!function_exists('tweetimport_add_menu')):
function tweetimport_add_menu()
{
    add_submenu_page('skinju', 'Tweet Import', 'Tweet Import', 'manage_tweetimport', 'tweetimport', 'tweetimport_options');
}
endif; //tweetimport_add_menu

//-- Add Schedule Items
if (!has_filter ('cron_schedules', 'skinju_tweetimport_add_schedule_intervals')) {add_filter('cron_schedules', 'skinju_tweetimport_add_schedule_intervals');}
if (!function_exists('skinju_tweetimport_add_schedule_intervals')):
function skinju_tweetimport_add_schedule_intervals()
{
  return array( 'everyquarter' => array('interval' => 900, 'display' => 'Once Every 15 Minutes'),
                '4timesaday' => array('interval' => 21600, 'display' => 'Four Times a Day'));
}
endif; //skinju_tweetimport_add_schedule_intervals


//-- The Checking and Importing Function
if (!function_exists('tweetimport_import_feeds')):
function tweetimport_import_feeds()
{
  $tweetimport_options = get_option ('skinju_tweet_import');
  if (!isset ($tweetimport_options['twitter_accounts'])) return;

  foreach ($tweetimport_options['twitter_accounts'] as $key=>$account)
  {
    if ($account['active']):
      $imported_count = tweetimport_import_twitter_feed($account);
      if (is_numeric ($imported_count)) :
        $tweetimport_options['twitter_accounts'][$key]['imported_count'] += $imported_count;
        $tweetimport_options['twitter_accounts'][$key]['last_checked'] = current_time('mysql'); //date ('Y-m-d H:i:s');
        update_option ('skinju_tweet_import', $tweetimport_options);
      else :
        $tweetimport_options['twitter_accounts'][$key]['last_checked'] = $imported_count;
        update_option ('skinju_tweet_import', $tweetimport_options);        
      endif;
    endif;
  }
}
endif; //tweetimport_import_feeds


//-- Display CSS for plugin elements
if (!has_action ('admin_head', 'tweetimport_display_head_style_css')) {add_action('admin_head', 'tweetimport_display_head_style_css');}
if (!function_exists('tweetimport_display_head_style_css')):
function tweetimport_display_head_style_css()
{
  echo '<style type="text/css">';
  echo 'tfoot.tweetimport th, tfoot.tweetimport td {border-top-style: solid; border-top-width: 1px;}';
  echo 'fieldset.tweetimport {border: 1px solid #dfdfdf;}';
  echo 'legend.tweetimport {margin-left: 10px; padding-left: 10px; padding-right: 10px;}';
  echo 'ul.tweetimport  {list-style: disc inside none;}';
  echo '</style>';
}
endif; //tweetimport_display_head_style_css

if (!function_exists('tweetimport_display_message')):
function tweetimport_display_message()
{
  global $message;

  if (isset ($message))
  {
    if (is_array ($message))
    {
      foreach ($message as $msg)
      {
      echo '<div id="message" class="updated fade">';
      echo '<p>' . $msg . '</p>';
      echo '</div>';      
      }
    }
    else
    {
      echo '<div id="message" class="updated fade">';
      echo '<p>' . $message . '</p>';
      echo '</div>';
    }
  }
}
endif; //tweetimport_display_message

//-- Tweet Import Options
if (!function_exists('tweetimport_options')):
function tweetimport_options()
{
  skinju_valid_privileges_or_die ('manage_tweetimport');

  $tweetimport_options = get_option ('skinju_tweet_import');

  echo '<div class="wrap">';

  //echo print_r (get_option ('skinju_tweet_import'));

  echo '<div id="icon-options-general" class="icon32"><br /></div>';
  echo '<h2>Tweet Import settings</h2>';

  tweetimport_display_message();

  echo '<div id="col-container">';
  echo '<div id="col-right">';
  echo '<div class="col-wrap">';
  tweetimport_display_account_list_table();
  tweetimport_display_global_configuration_form();

  echo '<div class="wrap">';
  echo '<h2>Notes</h2>';
  echo '<p>If the same tweet appears in multiple feeds, it will be imported only once and will follow the rules of the feed processed first depending on an internal processing sequence and not as per the sequence the accounts appear in the Feed table above.</p>';
  echo '<p>Deleting a post imported using Tweet Import will have it imported again if it still appears on the twitter feed imported. Please note that twitter only returns the last 20 tweets so if the deleted tweet is still on the list, it will be imported again.</p>';
  echo '<p>Deleting a feed does not delete its imported tweets and if the same feed is added again the tweets imported will not get duplicated. Tweet Import will check the imported tweets against previously imported ones.</p>';

  echo '<h2>Like this plugin?</h2>';
  echo '<p>If you like this plugin and would like to tell others about it, you can help spread the word:</p>';
  echo '<ul class="tweetimport"><li>Link to the plugin page http://skinju.com/wordpress/tweet-import</li>';
  echo '<li>Give it a good rating on the plugin <a href="http://wordpress.org/extend/plugins/tweet-import/">WordPress.org page</a></li>';
  echo '</ul>';

  echo '<h2>About</h2>';
  echo '<p><a href="http://skinju.com/wordpress/tweet-import" target="_blank">Tweet Import</a> is developed by <a href="http://www.afiouni.com/" target="_blank">Khaled Afiouni</a>. It\'s released under the GNU GPL version 3. For other wordpress plugins please check the <a href="http://skinju.com/wordpress" target="_blank">skinju wordpress plugins page</a></p>';
  echo '</div> <!-- wrap -->';

  echo '</div> <!-- col-wrap (right)-->';
  echo '</div> <!-- col-right -->';
  echo '<div id="col-left">';
  echo '<div class="col-wrap">';

  if (isset($_GET['edit_account'])):
      tweetimport_display_account_add_edit_form('edit', $tweetimport_options['twitter_accounts'][$_GET['edit_account']]);
  else :
      tweetimport_display_account_add_edit_form('add');
  endif;

  echo '</div> <!-- col-wrap (left)-->';
  echo '</div> <!-- col-left -->';
  echo '</div> <!-- col-container -->';
  echo '</div> <!-- wrap -->';
}
endif; //tweetimport_options

//-- Display Global Configuration Form
if (!function_exists('tweetimport_display_global_configuration_form')):
function tweetimport_display_global_configuration_form()
{
  $tweetimport_options = get_option ('skinju_tweet_import');

  echo '<br /><br /><br />';
  echo '<div class="form-wrap">';
  echo '<h3>Tweet Import Global Configuration</h3>';
  echo '<form name="options-tweet-import" id="options-tweet-import" method="post">';
  echo '<input type="hidden" name="tweetimport_action" value="global_configuration">';
  echo '<div class="form-field form-required">';
  echo '<label for="tweetimport_options_schedule">Update Schedule</label>';
  echo '<select name="tweetimport_options_schedule" id="tweetimport_options_schedule">';

  $intervals = wp_get_schedules();

  foreach ($intervals as $interval=>$description)
  {
    $selected = ($tweetimport_options['interval'] == $interval ? 'selected="selected"' : ''); 
    echo '<option value="' . $interval . '" ' . $selected . '>' . $description['display'] . '</option>';
  }
  echo '</select>';
  echo '<p>How often do you want WordPress to check for tweets and import new ones?</p>';
  echo '</div>';
  echo '<p class="submit">';
  echo '<input type="submit" class="button" name="submit" value="Update Configuration" />';
  echo '</p>';
  echo '</form>';
  echo '</div> <!-- end form-wrap -->';
}
endif; //tweetimport_display_global_configuration_form


//-- Display Account List Table
if (!function_exists('tweetimport_display_account_list_table')):
function tweetimport_display_account_list_table()
{
  $tweetimport_options = get_option ('skinju_tweet_import');

  echo '<table class="widefat fixed" cellspacing="0">';
  echo '<thead>';
  echo '<tr>';
  echo '<th scope="col" class="manage-column check-column" >&nbsp;</th>';
  echo '<th scope="col" class="manage-column">Feed Info</th>';
  echo '<th scope="col" class="manage-column">Import Options</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tfoot class="tweetimport">';
  echo '<tr>';
  echo '<th scope="col" class="manage-column check-column" >&nbsp;</th>';
  echo '<th scope="col" class="manage-column">Feed Info</th>';
  echo '<th scope="col" class="manage-column">Import Options</th>';
  echo '</tr>';
  echo '</tfoot>';

  echo '<tbody class="plugins">';

  if (empty ($tweetimport_options['twitter_accounts']))
  {
      echo '<tr class="active">';
      echo '<th scope="row" class="check-column" >&nbsp;</th>';
      echo '<td class="plugin-title" colspan="2"><p>No twitter feeds added yet. Please use the form on the left to add an account.<p></td>';
      echo '</tr>';    
  }
  else
  {
    foreach ($tweetimport_options['twitter_accounts'] as $key=>$account)
    {
      echo '<tr class="' . ($account['active']?'active':'inactive') . '">';
      echo '<th scope="row" class="check-column" >&nbsp;</th>';
      echo '<td class="plugin-title">';
      echo '<strong>' . $account['twitter_name'] . '</strong>';
      if ($account['account_type'] == 0)
        echo '&nbsp;&nbsp;&nbsp;&nbsp;*Importing User Public Timeline';
      if ($account['account_type'] == 1)
        echo '&nbsp;&nbsp;&nbsp;&nbsp;*Importing favorites';
      if ($account['account_type'] == 2)
        echo '&nbsp;&nbsp;&nbsp;&nbsp;*Importing List';
      echo '</td>';
      echo '<td class="desc"><p>';
      # The following two lines are a workaround for the PHP 4 lack of Method Chaining (allowed by PHP 5)
      $tmp_user_data = get_userdata($account['author']);
      $tmp_user_login = $tmp_user_data->user_login;
      echo 'Assign <strong>' . $tmp_user_login . '</strong> as the author<br />';
      echo 'Import tweets to <strong>' . get_cat_name($account['category']) . '</strong> category<br />';
      echo 'Tag tweets with: <strong>' . $account['add_tag'] . '</strong><br />';
      echo 'Make Names clickable: <strong>' . ($account['names_clickable']==1?'Yes':'No') . '</strong><br />';
      echo 'Tag with the tweets #hashtags as well: <strong>' . ($account['hash_tag']==1?'Yes':'No') . '</strong><br />';
      echo 'Make #hashtags clickable: <strong>' . ($account['hashtags_clickable']==1?'Yes':'No') . '</strong>, ';
      echo ' <strong>' . ($account['hashtags_clickable']==1?($account['hashtags_clickable_twitter']==1?'to twitter':'Locally'):'') . '</strong><br />';
      echo 'Remove @name Prefix: <strong>' . ($account['strip_name']==1?'Yes':'No') . '</strong><br />';
      echo '</p></td>';
      echo '</tr>';
      echo '<tr class="second ' . ($account['active']?'active':'inactive') . '">';
      echo '<td>&nbsp;</td>';
      echo '<td class="plugin-title">';
      echo '<div class="row-actions-visible">';
      echo '<span><a href="' . admin_url ('/admin.php?page=tweetimport&account=' . $key . '&tweetimport_action=' . ($account['active']?'deactivate':'activate')) . '">' . ($account['active']?'Deactivate':'Activate') . '</a>&nbsp;|&nbsp;</span>';
      echo '<span><a href="' . admin_url ('/admin.php?page=tweetimport&edit_account=' . $key) . '">Edit</a>&nbsp;|&nbsp;</span>';
      echo '<span><a href="' . admin_url ('/admin.php?page=tweetimport&account=' . $key . '&tweetimport_action=delete') . '">Delete</a>&nbsp;|&nbsp;</span>';
      echo '<span><a href="' . admin_url ('/admin.php?page=tweetimport&account=' . $key . '&tweetimport_action=reset_stats') . '">Reset Stats</a></span>';
      echo '</div>';
      echo '</td>';
      echo '<td class="desc">';
      echo $account['imported_count'] . ' tweets imported<br />';
      echo 'Last checked on '. $account['last_checked'];
      echo '</td>';
      echo '</tr>';
    }
  }
  echo '</tbody>';
  echo '</table>';
}
endif; //tweetimport_display_account_list_table


//-- Display Account List Table
if (!function_exists('tweetimport_display_account_add_edit_form')):
function tweetimport_display_account_add_edit_form($mode="add", $account_details=NULL)
{
  echo '<h3>' . ($mode=='add'?'Add':'Update') . ' Twitter Feed</h3>';
  echo '<div class="form-wrap">';
  echo '<form name="twitter-account-form" id="twitter-account-form" method="post">';
  echo '<input type="hidden" name="tweetimport_action" value="' . ($mode=="add"?'add':'update') .'">';

  echo '<fieldset class="tweetimport">';
  echo '<legend class="tweetimport">Feed Info</legend>';
  if ($mode == 'add')
  {
    echo '<div class="form-field form-required">';
    echo '<label for="account_type">Twitter Feed Type</label>';
    echo '<select name="account_type" id="account_type">';
    if (!isset ($account_details['account_type'])) $account_details['account_type'] = 0;
    echo '<option value="0" ' . ($account_details['account_type']==0?'selected="selected"':'') . '>User Public Timeline</option>';
    echo '<option value="1" ' . ($account_details['account_type']==1?'selected="selected"':'') . '>Favorites</option>';
    echo '<option value="2" ' . ($account_details['account_type']==2?'selected="selected"':'') . '>List</option>';
    echo '</select>';
    echo '<p>Define the feed source type and identifiers. For a normal user feed, please select User Public Timeline. Favorites allows importing the tweets favorited by a user. A List feed will import the tweets that appear on a list as shown on the list page.</p>';
    echo '</div>';

    echo '<div class="form-field form-required">';
    echo '<label for="account_name">Twitter Feed User Name or List</label>';
    echo '<input name="account_name" id="account_name" type="text" value="' . $account_details['twitter_name'] . '" size="40" aria-required="true" />';
    echo '<p>For a User Public Timeline or a Favorites feed, please enter the twitter user name only. For a List feed, please input the user/list-name as it appears on the list page on twitter.</p>';
    echo '</div>';
  }
  else
  {
    echo '<div class="form-field form-required">';
    echo 'Updating Account information for<br />Twitter Feed Type: <strong>';
    if ($account_details['account_type'] == 0) echo 'User Public Timeline';
    elseif ($account_details['account_type'] == 1) echo 'Favorites';
    elseif ($account_details['account_type'] == 2) echo 'List';
    echo '</strong><br />Twitter Feed User Name or List: <strong>' . $account_details['twitter_name'] . '</strong>';
    echo '<p>Twitter feed information cannot be changed after creation. If for any reason you would like to change the feed user name or list, please deactivate or delete the account and create a new one. Please note that deleting an account does not delete the posts created through this account.</p>';
    echo '</div>';
  }

  echo '</fieldset>';
 
  echo '<br /><br />';
  echo '<fieldset class="tweetimport">';
  echo '<legend class="tweetimport">Import Options</legend>';

  echo '<div class="form-field form-required">';
  echo '<label for="tweet_author">Assign Author for tweets</label>';
  echo '<select name="tweet_author" id="tweet_author" >';

  $blog_authors = get_users_of_blog();
  foreach ($blog_authors as $blog_author)
  {
    $blog_author_info = get_userdata($blog_author->user_id);
    if (!isset ($account_details['author'])) $account_details['author'] = 1;
    if ($blog_author->user_id == $account_details['author']) {$selected = 'selected="selected"';} else {$selected = '';}
    echo '<option value="' . $blog_author->user_id . '" ' . $selected . '>' . $blog_author_info->user_login . '</option>';
  }
  echo '</select>';
  echo '<p>Assign an author to your imported tweets.</p>';
  echo '</div>';

  echo '<div class="form-field form-required">';
  echo '<label for="tweet_category">Category for imported tweets</label>';
  echo '<select name="tweet_category" id="tweet_category" >';

  $categories = get_categories ('hide_empty=0');
  foreach ($categories as $category)
  {
    !empty($category->term_id) ? $cat_id = $category->term_id : $cat_id = $category->cat_ID;
    !empty($category->name) ? $cat_name = $category->name : $cat_name = $category->cat_name;
    if (!isset ($account_details['category'])) $account_details['category'] = 1;
    if ($cat_id == $account_details['category']) {$selected = 'selected="selected"';} else {$selected = '';}
    echo '<option value="' . $cat_id . '" ' . $selected . '>' . $cat_name . '</option>';
  }
  echo '</select>';
  echo '<p>Import this account tweets to the selected category.</p>';
  echo '</div>';

  echo '<div class="form-field">';
  echo '<label for="account_tags">Tag Tweets</label>';
  echo '<input name="account_tags" id="account_tags" type="text" value="'. $account_details['add_tag'] .'" size="40" />';
  echo '<p>You can add multiple tags. Make sure to separate tags with a comma (,)</p>';
  echo '</div>';

  echo '<div class="form-field form-required">';
  echo '<label for="names_clickable">Make Twitter names clickable</label>';
  echo '<select name="names_clickable" id="names_clickable">';
  if (!isset ($account_details['names_clickable'])) $account_details['names_clickable'] = 0;
  echo '<option value="0" ' . ($account_details['names_clickable']!=1?'selected="selected"':'') . '>No</option>';
  echo '<option value="1" ' . ($account_details['names_clickable']==1?'selected="selected"':'') . '>Yes</option>';
  echo '</select>';
  echo '<p>Would you like Tweet Import to try identify Twitter user names in tweets and make them clickable leading to their Twitter pages?</p>';
  echo '</div>';

  echo '<div class="form-field form-required">';
  echo '<label for="account_hashtags">#hashtags</label>';
  echo '<select name="account_hashtags" id="account_hashtags">';
  if (!isset ($account_details['hash_tag'])) $account_details['hash_tag'] = 0;
  echo '<option value="0" ' . ($account_details['hash_tag']!=1?'selected="selected"':'') . '>No</option>';
  echo '<option value="1" ' . ($account_details['hash_tag']==1?'selected="selected"':'') . '>Yes</option>';
  echo '</select>';
  echo '<p>For the imported tweets, would you like to have #hashtags added as tags to the created wordpress posts?</p>';
  echo '</div>';

  echo '<div class="form-field form-required">';
  echo '<label for="hashtags_clickable">Make #hashtags clickable</label>';
  echo '<select name="hashtags_clickable" id="hashtags_clickable">';
  if (!isset ($account_details['hashtags_clickable'])) $account_details['hashtags_clickable'] = 0;
  echo '<option value="0" ' . ($account_details['hashtags_clickable']!=1?'selected="selected"':'') . '>No</option>';
  echo '<option value="1" ' . ($account_details['hashtags_clickable']==1?'selected="selected"':'') . '>Yes</option>';
  echo '</select>';
  echo '&nbsp;&nbsp;&nbsp;<select name="hashtags_clickable_twitter" id="hashtags_clickable_twitter">';
  if (!isset ($account_details['hashtags_clickable_twitter'])) $account_details['hashtags_clickable_twitter'] = 0;
  echo '<option value="0" ' . ($account_details['hashtags_clickable_twitter']!=1?'selected="selected"':'') . '>Locally</option>';
  echo '<option value="1" ' . ($account_details['hashtags_clickable_twitter']==1?'selected="selected"':'') . '>to twitter</option>';
  echo '</select>';
  echo '<p>Would you like Tweet Import to make #hashtags clickable? If so, would you like the link to point to the tag page locally, or the search page on twitter?</p>';
  echo '</div>';

  echo '<div class="form-field form-required">';
  echo '<label for="account_hashtags">Remove Prefixed @name</label>';
  echo '<select name="strip_name" id="strip_name">';
  if (!isset ($account_details['strip_name'])) $account_details['hash_tag'] = 0;
  echo '<option value="0" ' . ($account_details['strip_name']!=1?'selected="selected"':'') . '>No</option>';
  echo '<option value="1" ' . ($account_details['strip_name']==1?'selected="selected"':'') . '>Yes</option>';
  echo '</select>';
  echo '<p>By default Twitter includes the @name of the Twitter user at the beginning of the tweet as [ @name: ]. Would you like Tweet Import to remove this name from the beginning of the imported tweets?</p>';
  echo '</div>';

  echo '</fieldset>';

  echo '<p class="submit">';
  echo '<input type="submit" class="button" name="submit" value="' . ($mode == 'add' ? 'Add Account' : 'Update Account') .'" />';
  echo '</p>';
  echo '</form>';
  echo '</div> <!-- end form-wrap -->';
}
endif; //tweetimport_display_account_add_edit_form

//-- Process tweetimport_action requests
if (!has_action ('init', 'tweetimport_handle_request')) {add_action('init', 'tweetimport_handle_request');}
if (!function_exists('tweetimport_handle_request')):
function tweetimport_handle_request()
{
  global $message;

  if (!empty ($_GET['tweetimport_action']) || !empty ($_POST['tweetimport_action']))
    skinju_valid_privileges_or_die ('manage_tweetimport');

  $tweetimport_options = get_option ('skinju_tweet_import');
  if (!empty ($_GET['tweetimport_action']))
  {
    switch ($_GET['tweetimport_action'])
    {
      case 'activate':
        if (!empty ($_GET['account']))
        {
          $tweetimport_options['twitter_accounts'][trim($_GET['account'])]['active']=true;
          update_option ('skinju_tweet_import', $tweetimport_options);
          $message = 'Account ' . $_GET['account'] . ' activated successfully';
        }
        wp_redirect (admin_url ('/admin.php?page=tweetimport'));
        exit();
        break;
      case 'deactivate':
        if (!empty ($_GET['account']))
        {
          $tweetimport_options['twitter_accounts'][trim($_GET['account'])]['active']=false;        
          update_option ('skinju_tweet_import', $tweetimport_options);
          $message = 'Account ' . $_GET['account'] . ' deactivated successfully';
        }
        wp_redirect (admin_url ('/admin.php?page=tweetimport'));
        exit();
        break;
      case 'delete':
        if (!empty ($_GET['account']))
        {
          unset ($tweetimport_options['twitter_accounts'][trim($_GET['account'])]);
          update_option ('skinju_tweet_import', $tweetimport_options);
          $message = 'Account ' . $_GET['account'] . ' deleted successfully';
        }
        wp_redirect (admin_url ('/admin.php?page=tweetimport'));
        exit();
        break;
      case 'reset_stats':
        $tweetimport_options['twitter_accounts'][trim($_GET['account'])]['imported_count']='No';        
        update_option ('skinju_tweet_import', $tweetimport_options);
        $message = 'Account ' . $_GET['account'] . ' stats cleared successfully';
        wp_redirect (admin_url ('/admin.php?page=tweetimport'));
        exit();
        break;
    }
  }
  if (!empty ($_POST['tweetimport_action']))
  {
    switch ($_POST['tweetimport_action'])
    {
      case 'global_configuration':
        if (!empty ($_POST['tweetimport_options_schedule']))
        {
          $tweetimport_options['interval'] = $_POST['tweetimport_options_schedule'];
          update_option ('skinju_tweet_import', $tweetimport_options);
          $message = 'Configuration saved successfully';
          wp_clear_scheduled_hook('tweet_import_scheduled_hook');
          wp_schedule_event(time(), $tweetimport_options['interval'], 'tweet_import_scheduled_hook');
        }  
        wp_redirect (admin_url ('/admin.php?page=tweetimport'));
        exit();
        break;
      case 'add':
        $new_account_name = trim(trim(trim($_POST['account_name']), '@'));
        if (!$new_account_name)
        {
          $message = 'Feed User or List Information cannot be blank';
          break;
        }

        $account_parts = explode ('/', $new_account_name);
        if (trim($_POST['account_type']) == 2 && count($account_parts) != 2) //Account is list but not well formatted
        {
          $message = 'List information is not correct. Please make sure the list name is correct.';
          break;
        }

        $new_account_name = trim($_POST['account_type']) . '-' . $new_account_name;
        if (isset ($tweetimport_options['twitter_accounts'][$new_account_name]))
        {
          $message = 'A feed with the same name and type already exists';
          break;
        }

        $new_account = array();
        $new_account['twitter_name'] = trim(trim(trim($_POST['account_name']), '@'));
        $new_account['account_type'] = trim($_POST['account_type']);
        $new_account['author'] = trim($_POST['tweet_author']);
        $new_account['category'] = trim($_POST['tweet_category']); 
        $new_account['add_tag'] = trim($_POST['account_tags']); 
        $new_account['hash_tag'] = trim($_POST['account_hashtags']); 
        $new_account['names_clickable'] = trim($_POST['names_clickable']); 
        $new_account['hashtags_clickable'] = trim($_POST['hashtags_clickable']); 
        $new_account['hashtags_clickable_twitter'] = trim($_POST['hashtags_clickable_twitter']);
        $new_account['strip_name'] = trim($_POST['strip_name']);
        $new_account['imported_count'] = 'No'; 
        $new_account['active'] = true; 
        $new_account['last_checked'] = '(Never)'; 
        $tweetimport_options['twitter_accounts'][$new_account_name] = $new_account;
        update_option ('skinju_tweet_import', $tweetimport_options);
        $message = 'Account ' . $new_account['twitter_name'] . ' created successfully';            

        wp_redirect (admin_url ('/admin.php?page=tweetimport'));
        exit();
        break;
      case 'update':
        $new_account_name = trim(trim(trim($_GET['edit_account']), '@'));
        $new_account = array();
        $new_account['author'] = trim($_POST['tweet_author']);
        $new_account['category'] = trim($_POST['tweet_category']); 
        $new_account['add_tag'] = trim($_POST['account_tags']); 
        $new_account['hash_tag'] = trim($_POST['account_hashtags']); 
        $new_account['names_clickable'] = trim($_POST['names_clickable']); 
        $new_account['hashtags_clickable'] = trim($_POST['hashtags_clickable']); 
        $new_account['hashtags_clickable_twitter'] = trim($_POST['hashtags_clickable_twitter']);
        $new_account['strip_name'] = trim($_POST['strip_name']);
        $new_account['active'] = true; 
        $tweetimport_options['twitter_accounts'][$new_account_name] = array_merge ($tweetimport_options['twitter_accounts'][$new_account_name], $new_account);
        update_option ('skinju_tweet_import', $tweetimport_options);

        wp_redirect (admin_url ('/admin.php?page=tweetimport'));
        exit();
        break;
    }
  }
}
endif; //tweetimport_handle_request

//-- Import Twitter Feed Functionality
if (!function_exists('tweetimport_import_twitter_feed')):
function tweetimport_import_twitter_feed($twitter_account)
{
  require_once (ABSPATH . WPINC . '/class-feed.php');

  $feed = new SimplePie();

  $account_parts = explode ('/', $twitter_account['twitter_name'], 2);



  if ($twitter_account['account_type'] == 1): //Account is Favorites
    $feed->set_feed_url(str_replace('#=#USER#=#', $account_parts[0], TWEETIMPORT_API_FAVORITES_URL));
  elseif ($twitter_account['account_type'] == 0 && count($account_parts) == 1): //User timeline
      $feed->set_feed_url(str_replace('#=#USER#=#', $account_parts[0], TWEETIMPORT_API_USER_TIMELINE_URL));
  elseif ($twitter_account['account_type'] == 2 && count($account_parts) == 2): //Account is list
      $feed_url = str_replace('#=#USER#=#', $account_parts[0], TWEETIMPORT_API_LIST_URL);
      $feed_url = str_replace('#=#LIST#=#', $account_parts[1], $feed_url);
      $feed->set_feed_url($feed_url);
  else :
      return '<strong>ERROR: Account information not correct. Account type wrong?</strong>';
  endif;

  $feed->set_useragent('Tweet Import http://skinju.com/wordpress/tweetimport');
  $feed->set_cache_class('WP_Feed_Cache');
  $feed->set_file_class('WP_SimplePie_File');
  $feed->enable_cache(true);
  $feed->set_cache_duration (apply_filters('tweetimport_cache_duration', 880));
  $feed->enable_order_by_date(false);
  $feed->init();
  $feed->handle_content_type();

  if ($feed->error()):
   return '<strong>ERROR: Feed Reading Error.</strong>';
  endif;

  $rss_items = $feed->get_items();

  $imported_count = 0;
  foreach ($rss_items as $item)
  {
    $item = apply_filters ('tweetimport_tweet_before_new_post', $item); //return false to stop processing an item.
    if (!$item) continue;

    $processed_description = $item->get_description();

    //Get the twitter author from the beginning of the tweet text
    $twitter_author = trim(preg_replace("~^(\w+):(.*?)$~", "\\1", $processed_description));

    if ($twitter_account['strip_name'] == 1):
      $processed_description = preg_replace("~^(\w+):(.*?)~i", "\\2", $processed_description);
    endif;

    if ($twitter_account['names_clickable'] == 1):
      $processed_description = preg_replace("~@(\w+)~", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $processed_description);
      $processed_description = preg_replace("~^(\w+):~", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>:", $processed_description);
    endif;

    if ($twitter_account['hashtags_clickable'] == 1):
      if ($twitter_account['hashtags_clickable_twitter'] == 1):
          $processed_description = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $processed_description);
      else:
        $processed_description = preg_replace("/#(\w+)/", "<a href=\"" . skinju_get_tag_link("\\1") . "\">#\\1</a>", $processed_description);
      endif;
    endif;

  $processed_description = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $processed_description);
  $processed_description = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $processed_description);

    $new_post = array('post_title' => trim (substr (preg_replace("~{$account_parts[0]}: ~i", "", $item->get_title()), 0, 25) . '...'),
                      'post_content' => trim ($processed_description),
                      'post_date' => $item->get_date('Y-m-d H:i:s'),
                      'post_author' => $twitter_account['author'],
                      'post_category' => array($twitter_account['category']),
                      'post_status' => 'publish');

    $new_post = apply_filters('tweetimport_new_post_before_create', $new_post); // Offer the chance to manipulate new post data. return false to skip
    if (!$new_post) continue;
    $new_post_id = wp_insert_post($new_post);

    $imported_count++;

    add_post_meta ($new_post_id, 'tweetimport_twitter_author', $twitter_author, true); 
    add_post_meta ($new_post_id, 'tweetimport_date_imported', date ('Y-m-d H:i:s'), true);
    add_post_meta ($new_post_id, 'tweetimport_twitter_id', $item->get_id(), true);
    add_post_meta ($new_post_id, 'tweetimport_twitter_id', $item->get_id(), true);
    add_post_meta ($new_post_id, '_tweetimport_twitter_id_hash', $item->get_id(true), true);
    add_post_meta ($new_post_id, 'tweetimport_twitter_post_uri', $item->get_link(0));
    add_post_meta ($new_post_id, 'tweetimport_author_avatar', $item->get_link(0, 'image'));

    preg_match_all ('~#([A-Za-z0-9_]+)(?=\s|\Z)~', $item->get_description(), $out);
    if ($twitter_account['add_tag']) $out[0][] = $twitter_account['add_tag'];
    wp_set_post_tags($new_post_id, implode (',', $out[0]));
  }

  return $imported_count;
}
endif; //tweetimport_import_twitter_feed

if (!has_action ('tweetimport_tweet_before_new_post', 'tweetimport_stop_duplicates')) {add_action('tweetimport_tweet_before_new_post', 'tweetimport_stop_duplicates');}
if (!function_exists('tweetimport_stop_duplicates')):
function tweetimport_stop_duplicates($item)
{
  global $wpdb;

  $posts = $wpdb->get_var ($wpdb->prepare ("SELECT COUNT(*) FROM $wpdb->postmeta 
                                            WHERE meta_key = '_tweetimport_twitter_id_hash'
                                            AND meta_value = '%s'", $item->get_id(true)));


  if ($posts > 0)  return false;
  else return $item;
}
endif; //tweetimport_stop_duplicates
?>