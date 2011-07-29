<?php
/*********
* Add jQuery
*/
function fc_add_jquery() {
  wp_enqueue_script('jquery');
}
add_action( 'init', 'fc_add_jquery' );


/*********
* Allow uploading some additional MIME types
*/
function fc_add_mimes( $mimes=array() ) {
  $mimes['ogv'] = 'video/ogg';
  $mimes['mp4'] = 'video/mp4';
  $mimes['m4v'] = 'video/mp4';
  $mimes['flv'] = 'video/x-flv';
  return $mimes;
}
add_filter('upload_mimes', 'fc_add_mimes');


/*********
* Customize the login screen
*/
function fc_custom_login() { 
  echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('template_directory') . '/css/login.css" />'; 
}
add_action('login_head', 'fc_custom_login');


/*********
* Activate post thumbnails
*/
add_theme_support( 'post-thumbnails' );


/*********
* Thumbnail size for small Team icons
*/
add_image_size( 'team-icon-small', 80, 80, true );


/*********
* Add default posts and comments RSS feed links to head
*/
add_theme_support( 'automatic-feed-links' );


/*********
* Remove WP version from head
*/
remove_action('wp_head', 'wp_generator');


/*********
* Style the visual editor to match the theme styles
*/
add_filter('mce_css', 'my_editor_style');
function my_editor_style($url) {
  if ( !empty($url) ) {
    $url .= ',';
  }
  $url .= trailingslashit( get_stylesheet_directory_uri() ) . '/css/editor-style.css';
  return $url;
}


/*********
* Add more-links to excerpts
*/
function qmo_excerpt_more($post) {
  return '&hellip; <a class="more-link" href="'.get_permalink($post->ID).'" title="Read the rest of &#8220;'.get_the_title_rss($post->ID).'&#8221;">'.'Read more'.'</a>';
}
add_filter('excerpt_more', 'qmo_excerpt_more');


/*********
* Make theme available for translation
* Translations can be filed in the /languages/ directory
*/
load_theme_textdomain( 'qmo', TEMPLATEPATH . '/languages' );

$locale = get_locale();
$locale_file = TEMPLATEPATH . "/languages/$locale.php";
if ( is_readable( $locale_file ) )
  require_once( $locale_file );


/*********
* Register widget areas
*/
if ( function_exists('register_sidebars') ) :

  /** Home page */
  register_sidebar(array(
  'name' => 'Home/News Sidebar',
  'id' => 'sidebar-home',
  'description' => 'Shown on the QMO home page (featuring QMO news and events).',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>'
  ));
  
  /** Teams index page */
  register_sidebar(array(
  'name' => 'Teams Index Sidebar',
  'id' => 'sidebar-teams',
  'description' => 'Shown on the Teams index page.',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>'
  ));
  
  /** Team pages */
  register_sidebar(array(
  'name' => 'Team Page Sidebar',
  'id' => 'sidebar-team-single',
  'description' => 'Shown on individual Team pages. Use Widget Logic to match specific widgets to specific teams.',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>'
  ));
  
  /** Community page */
  register_sidebar(array(
  'name' => 'Community Sidebar',
  'id' => 'sidebar-community',
  'description' => 'Shown on Community pages.',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>'
  ));
  
  /** Event post */
  register_sidebar(array(
  'name' => 'Events Sidebar',
  'id' => 'sidebar-event',
  'description' => 'Shown on the events list, calendar, and the details page for a single event.',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>'
  ));
  
  /** Doc landing page */
  register_sidebar(array(
  'name' => 'Main Docs Landing Sidebar',
  'id' => 'sidebar-docs-landing',
  'description' => 'Shown on the main Docs page.',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>'
  ));
  
  /** Doc section pages */
  register_sidebar(array(
  'name' => 'Docs Section Overview Sidebar',
  'id' => 'sidebar-docs-section',
  'description' => 'Shown on section overview pages within Docs.',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>'
  ));

  /** Single doc pages */
  register_sidebar(array(
  'name' => 'Single Doc Sidebar',
  'id' => 'sidebar-doc-single',
  'description' => 'Shown on individual Doc pages. You can use Widget Logic to match specific widgets to specific docs.',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>'
  ));
  
  /** Archive pages */
  register_sidebar(array(
  'name' => 'Archive Page Sidebar',
  'id' => 'sidebar-archive',
  'description' => 'Shown on blog archive pages (category, date, author, tag).',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>'
  ));
  
  /** Search results */
  register_sidebar(array(
  'name' => 'Search Results Sidebar',
  'id' => 'sidebar-search',
  'description' => 'Shown on search results.',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>'
  ));
  
  /** Member pages */
  register_sidebar(array(
  'name' => 'Member Sidebar',
  'id' => 'sidebar-member',
  'description' => 'Shown on member pages (single member profiles and the members directory).',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>'
  ));

endif;


/*********
* Prints the page number currently being browsed, with a pipe before it.
* Used in header.php to add the page number to the <title>.
*/
if ( !function_exists( 'qmo_page_number' ) ) :
function qmo_page_number() {
  global $paged; // Contains page number.
  if ( $paged >= 2 ) {
    echo ' | ' . sprintf( __( 'Page %s' , 'qmo' ), $paged );
  }
}
endif;


/*********
* Comment Template for QMO theme
*/
if ( ! function_exists( 'qmo_comment' ) ) :
function qmo_comment($comment, $args, $depth) {
  $GLOBALS['comment'] = $comment;
  $comment_type = get_comment_type();
?>

 <li id="comment-<?php comment_ID(); ?>" <?php comment_class('hentry'); ?>>
  <?php if ( $comment_type == 'trackback' ) : ?>
    <h5 class="entry-title"><?php _e( 'Trackback from ', 'qmo' ); ?> <cite><?php comment_author_link(); ?></cite>
      <span class="comment-meta">on <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>" rel="bookmark" title=" <?php _e('Permanent link to this comment by ','qmo'); comment_author(); ?>"><abbr class="published" title="<?php comment_date('Y-m-d'); ?>"><?php comment_date('F jS, Y'); ?></abbr> at <?php comment_time(); ?></a>:</span>
    </h5>
  <?php elseif ( $comment_type == 'pingback' ) : ?>
    <h5 class="entry-title"><?php _e( 'Pingback from ', 'qmo' ); ?> <cite><?php comment_author_link(); ?></cite>
      <span class="comment-meta">on <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>" rel="bookmark" title="<?php _e('Permanent link to this comment by ','qmo'); comment_author(); ?>"><abbr class="published" title="<?php comment_date('Y-m-d'); ?>"><?php comment_date('F jS, Y'); ?></abbr> at <?php comment_time(); ?></a>:</span>
    </h5>
  <?php else : ?>
    <?php if ( ( $comment->comment_author_url != "http://" ) && ( $comment->comment_author_url != "" ) ) : // if author has a link ?>
     <h5 class="entry-title vcard">
       <a href="<?php comment_author_url(); ?>" class="url" rel="nofollow external" title="<?php comment_author_url(); ?>">
         <?php if (function_exists('get_avatar')) : echo ('<span class="photo">'.get_avatar( $comment, 48 ).'</span>'); endif; ?>
         <cite class="author fn"><?php comment_author(); ?></cite>
       </a>
       <span class="comment-meta">wrote on <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>" rel="bookmark" title="<?php _e('Permanent link to this comment by ','qmo'); comment_author(); ?>"><abbr class="published" title="<?php comment_date('Y-m-d'); ?>"><?php comment_date('F jS, Y'); ?></abbr> at <?php comment_time(); ?></a>:</span>
     </h5>
    <?php else : // author has no link ?>
      <h5 class="entry-title vcard">
        <?php if (function_exists('get_avatar')) : echo ('<span class="photo">'.get_avatar( $comment, 48 ).'</span>'); endif; ?>
        <cite class="author fn"><?php comment_author(); ?></cite>
        <span class="comment-meta">wrote on <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>" rel="bookmark" title="<?php _e('Permanent link to this comment by ','qmo'); comment_author(); ?>"><abbr class="published" title="<?php comment_date('Y-m-d'); ?>"><?php comment_date('F jS, Y'); ?></abbr> at <?php comment_time(); ?></a>:</span>
      </h5>
    <?php endif; ?>
  <?php endif; ?>

    <?php if ($comment->comment_approved == '0') : ?>
      <p class="mod"><strong><?php _e('Your comment is awaiting moderation.'); ?></strong></p>
    <?php endif; ?>

    <blockquote class="entry-content">
      <?php comment_text(); ?>
    </blockquote>

  <?php if ( (get_option('thread_comments') == true) || (current_user_can('edit_post', $comment->comment_post_ID)) ) : ?>
    <p class="comment-util"><?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?> <?php if ( current_user_can('edit_post', $comment->comment_post_ID) ) : ?><span class="edit">[<?php edit_comment_link(__('Edit Comment','qmo'),'',''); ?>]</span><?php endif; ?></p>
  <?php endif; ?>
<?php
} /* end qmo_comment */
endif;


/*********
* Removes the embedded style when adding galleries to posts
*/
add_filter('gallery_style', create_function('$a', 'return preg_replace("%<style type=\'text/css\'>(.*?)</style>%s", "", $a);'));


/*********
* Removes the default styles that are packaged with the Recent Comments widget.
*/
function qmo_remove_recent_comments_style() {
  global $wp_widget_factory;
  remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'qmo_remove_recent_comments_style' );


/**********
* Determine if the page is paged and should show posts navigation
*/
function fc_show_posts_nav() {
 global $wp_query;
 return ($wp_query->max_num_pages > 1) ? TRUE : FALSE;
}


/*********
* Determine if a previous post exists (i.e. that this isn't the first one)
*
* @param bool $in_same_cat Optional. Whether link should be in same category.
* @param string $excluded_categories Optional. Excluded categories IDs.
*/
function fc_previous_post($in_same_cat = false, $excluded_categories = '') {
  if ( is_attachment() )
    $post = & get_post($GLOBALS['post']->post_parent);
  else
    $post = get_previous_post($in_same_cat, $excluded_categories);
  if ( !$post )
    return false;
  else
    return true;
}


/*********
* Determine if a next post exists (i.e. that this isn't the last post)
*
* @param bool $in_same_cat Optional. Whether link should be in same category.
* @param string $excluded_categories Optional. Excluded categories IDs.
*/
function fc_next_post($in_same_cat = false, $excluded_categories = '') {
  $post = get_next_post($in_same_cat, $excluded_categories);
  if ( !$post )
    return false;
  else
    return true;
}


/*********
* Determines if the current page is the result of paged comments.
* This lets us prevent search engines from indexing lots of duplicate pages 
* (since the post is repeated on every paged comment page).
*/
function is_comments_paged_url() {
  $pos = strpos($_SERVER['REQUEST_URI'], "comment-page");
  if ($pos === false) { return false; }
  else { return true; }
}


/*********
* Tests if any of a post's assigned categories are descendants of target categories
*
* @param int|array $cats The target categories. Integer ID or array of integer IDs
* @param int|object $_post The post. Omit to test the current post in the Loop or main query
* @return bool True if at least 1 of the post's categories is a descendant of any of the target categories
* @see get_term_by() You can get a category by name or slug, then pass ID to this function
* @uses get_term_children() Passes $cats
* @uses in_category() Passes $_post (can be empty)
* @version 2.7
* @link http://codex.wordpress.org/Function_Reference/in_category#Testing_if_a_post_is_in_a_descendant_category
*/
function fc_in_descendant_category( $cats, $_post = null ) {
  foreach ( (array) $cats as $cat ) {
    $descendants = get_term_children( (int) $cat, 'category'); // get_term_children() accepts integer ID only
    if ( $descendants && in_category( $descendants, $_post ) )
      return true;
  }
  return false;
}


/*********
* Determines if a page is the child of another page
*/
function fc_is_subpage() {
  global $post;
  if ( is_page() && $post->post_parent ) {
    $parentID = $post->post_parent;
    return $parentID;
  } 
  else {
    return false;
  };
};


/*********
 * Tests whether the current post is a child of the given parent post. By
 * default it will test parent pages up to the root but this can be
 * disabled by setting depthTest to false.
 *
 * Originally by Christian Schenk http://www.christianschenk.org/blog/wordpress-is_child-function/
 * Embedded because the theme requires this function (disabling the plugin would break the theme)
 * Renamed to avoid conflicts.
 */
if (!function_exists('fc_is_child')) :

  function fc_is_child($parent, $depthTest = true) {
    if (empty($parent)) return false;

    $id = $parent;
    if (!is_numeric($id))
      $id = fc_get_parent_by_name($id);
    if (empty($id)) return false;

    global $post;
    if ($post->post_parent == $id)
      return true;

    if ($depthTest) {
      $parents = fc_get_all_parents($post->post_parent);
      return in_array($id, $parents);
    }

    return false;
  }

  // Returns the parent page for a given page.
  function fc_get_parent($id) {
    global $wpdb;
    return $wpdb->get_var('SELECT post_parent FROM '.$wpdb->posts.' WHERE ID = "'.$id.'"');
  }

   // In case the user supplied a post/page by name we'll retrieve the ID using this function.
  function fc_get_parent_by_name($name) {
    global $wpdb;
    return $wpdb->get_var('SELECT ID FROM '.$wpdb->posts.' WHERE post_name = "'.$name.'"');
  }

  // Returns all parents up to the root node for the given page.
  function fc_get_all_parents($id) {
    $parents = array();
    $curId = $id;
    while ($curId != 0) {
      $curId = fc_get_parent($curId);
      if ($curId != 0)
        $parents[] = $curId;
    }
    return $parents;
  }
endif;


/*********
 * Add an excerpt field to Pages
 */
function fc_add_page_excerpt_box() {
  add_meta_box( 'fc_page_excerpt', __( 'Excerpt' ), 'fc_page_excerpt_box', 'page', 'normal', 'high' );
}

function fc_page_excerpt_box() {
  global $post;
  $message = __( 'Excerpts are optional hand-crafted summaries of your content. You can <a href="http://codex.wordpress.org/Template_Tags/the_excerpt" target="_blank">use them in your template</a>' );
  print <<<EOF
  <div class="inside">
  <textarea rows="1" cols="40" name="excerpt" tabindex="3" id="excerpt">{$post->post_excerpt}</textarea>
  <p>{$message}</p>
  </div>
EOF;
}
add_action( 'admin_menu', 'fc_add_page_excerpt_box' );


/*********
* Retrieves a specific post or page to display outside a loop
*/
function fc_get_post($id='GETPOST') {
  global $post, $wpdb;
  $table = $wpdb->posts;
  $now = current_time('mysql');
  $name_or_id = '';
  $orderby = 'post_date';

  if( !$id || 'GETPOST' == $id ) {
    $query_suffix = "post_type = 'post' AND post_status = 'publish'";
  } 
  elseif('GETPAGE' == $id) {
    $query_suffix = "post_type = 'page' AND post_status = 'publish'";
  } 
  elseif('GETSTICKY' == $id) {
    $table .= ', ' . $wpdb->postmeta;
    $query_suffix = "ID = post_id AND meta_key = 'sticky' AND meta_value = 1";
  } 
  else {
    $query_suffix = "(post_status = 'publish' OR post_status = 'static')";
    if(is_numeric($id)) {
      $name_or_id = "ID = '$id' AND";
    } 
    else {
      $name_or_id = "post_name = '$id' AND";
    }
  }

  $post = $wpdb->get_row("SELECT * FROM $table WHERE $name_or_id post_date <= '$now' AND $query_suffix ORDER BY $orderby DESC LIMIT 1");
  get_post_custom($post->ID);
  setup_postdata($post);
}


/*********
* Add reCAPTCHA to registration form (requires WP reCAPTCHA)
* Out of the box, WP reCAPCTCHA can't insert into the registration form because it's expecting 
* a different set of hooks (BP's signup form differs from WP's). This simply copies the same 
* functions from recaptcha.php and inserts them into a BP register form.
*/
if ( function_exists('bp_is_active') && get_option('users_can_register') && class_exists('reCAPTCHA') ) :
  class BP_reCAPTCHA_Connector {
  	var $data;
  
  	function check_recaptcha_result( $result ) {
  		global $recaptcha;
  		$this->data = $recaptcha->validate_recaptcha_response($errors);
  		return $this->data;
  	}
  	
  	function display_recaptcha_box() {
  		global $recaptcha;
  		$errors = new WP_Error();
  
  		if(isset($this->data['errors'])) {
  			if($this->data['errors']->get_error_message('blank_captcha') != '') {
  				$errors->add('captcha',$this->data['errors']->get_error_message('blank_captcha'));
  			} else if($this->data['errors']->get_error_message('captcha_wrong') != '') {
  				$errors->add('captcha',$this->data['errors']->get_error_message('captcha_wrong'));
  			}
  		}
  
  		if($errors->get_error_message('captcha') != '') {
  			echo '<div class="error">' . $errors->get_error_message('captcha') . '</div>';
  		}
  		$recaptcha->show_recaptcha_in_registration( $errors );
  	}
  }
  $connector = new BP_reCAPTCHA_Connector;
  add_action( 'bp_core_validate_user_signup', array($connector,'check_recaptcha_result') );
  add_action( 'bp_before_registration_submit_buttons', array($connector,'display_recaptcha_box') );
endif;


/**********
* Customize the feeds
*/
function qmo_feed_content( $content ) {
  global $post, $id;
  $blog_key = substr( md5( get_bloginfo('url') ), 0, 16 );
  if ( ! is_feed() ) return $content;

// Fetch the formats
  $date_format = get_option("date_format");
  $time_format = get_option("time_format");
  $datetime = $date_format.', '.$time_format;

// Fetch the custom fields ***
  $is_event = get_post_meta($post->ID, '_isEvent', true);
  $allday = get_post_meta( $post->ID, '_EventAllDay', true);
  $event_date = date ( $date_format, strtotime( get_post_meta( $post->ID, '_EventStartDate', true ) ) );
  $event_start = date ( $datetime, strtotime( get_post_meta( $post->ID, '_EventStartDate', true ) ) );
  $event_end = date( $datetime, strtotime( get_post_meta($post->ID, '_EventEndDate', true) ) );

// Display the content ***
  // If this is an event post, add the date
  if ( $is_event ) {
    // If it's an all-day event, only show the start date and no start time
    if ( $allday ) {
      $content = $content . '<p><strong>When:</strong> '.$event_date.'.</p>';    
    }
    else {
      $content = $content . '<p><strong>When:</strong><br /> Starts: '.$event_start.'<br /> Ends: '.$event_end.'</p>';
    }
  }
  else {
    $content = $content;
  }       
  return $content;
} // End function
add_filter('the_content', 'qmo_feed_content');


/*********
 * Create a special role for the Twitter Bot
 */
$botcando = array(
  'read' =>  true, 
  'edit_posts' => true,
  'publish_posts' => true,
  'edit_published_posts' =>  true, 
  'delete_posts' => true,
  'delete_published_posts' => true
);

remove_role('twitter_bot'); // remove it first to prevent duplicates, then add
add_role( 'twitter_bot', 'Twitter Bot', $botcando );


/** BEGIN BUDDYPRESS FUNCTIONS **/

/*********
 * Stop the theme from killing WordPress if BuddyPress is not enabled.
 */
if ( !class_exists( 'BP_Core_User' ) ) :
  return false;
endif;


/*********
 * Kill the BuddyPress admin bar
 */
remove_action( 'wp_head', 'bp_core_admin_bar_css', 1 );
remove_action( 'wp_footer', 'bp_core_admin_bar', 8 );

/*********
 * Kill the WordPress admin bar (new in 3.1)
 */
add_filter( 'show_admin_bar', '__return_false' );

/*********
 * Hide the admin bar settings on profile page
 */
function qmo_hide_admin_bar_settings() { ?>
<style type="text/css">.show-admin-bar { display: none; }</style>
<?php }
add_action( 'admin_print_scripts-profile.php', 'qmo_hide_admin_bar_settings' );


/*********
 * Load the AJAX functions for the theme
 */
require_once( TEMPLATEPATH . '/_inc/ajax.php' );


/*********
 * Load the javascript for the theme
 */
wp_enqueue_script( 'dtheme-ajax-js', get_template_directory_uri() . '/_inc/global.js', array( 'jquery' ) );


/*********
 * Rearrange and rename group tabs
 */
function qmo_bp_tabs() {
  global $bp;
  $bp->bp_options_nav['teams']['admin']['position'] = '100'; // Move admin to last
  $bp->bp_options_nav['teams']['home']['name'] = 'General'; // Rename 'Home' to 'General'
}
add_action('wp', 'qmo_bp_tabs');


/********* 
 * Add words that we need to use in JS to the end of the page so they can be translated and still used.
 */
$params = array(
  'my_favs'           => __( 'My Favorites', 'buddypress' ),
  'accepted'          => __( 'Accepted', 'buddypress' ),
  'rejected'          => __( 'Rejected', 'buddypress' ),
  'show_all_comments' => __( 'Show all comments for this thread', 'buddypress' ),
  'show_all'          => __( 'Show all', 'buddypress' ),
  'comments'          => __( 'comments', 'buddypress' ),
  'close'             => __( 'Close', 'buddypress' ),
  'mention_explain'   => sprintf( __( "%s is a unique identifier for %s that you can type into any message on this site. %s will be sent a notification and a link to your message any time you use it.", 'buddypress' ), '@' . bp_get_displayed_user_username(), bp_get_user_firstname( bp_get_displayed_user_fullname() ), bp_get_user_firstname( bp_get_displayed_user_fullname() ) )
);
wp_localize_script( 'dtheme-ajax-js', 'BP_DTheme', $params );


/*********
 * Add the JS needed for blog comment replies
 *
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_add_blog_comments_js() {
  if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
}
add_action( 'template_redirect', 'bp_dtheme_add_blog_comments_js' );


/*********
 * Filter the dropdown for selecting the page to show on front to include "Activity Stream"
 *
 * @param string $page_html A list of pages as a dropdown (select list)
 * @see wp_dropdown_pages()
 * @return string
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_wp_pages_filter( $page_html ) {
  if ( !bp_is_active( 'activity' ) )
    return $page_html;

  if ( 'page_on_front' != substr( $page_html, 14, 13 ) )
    return $page_html;

  $selected = false;
  $page_html = str_replace( '</select>', '', $page_html );

  if ( bp_dtheme_page_on_front() == 'activity' )
    $selected = ' selected="selected"';

  $page_html .= '<option class="level-0" value="activity"' . $selected . '>' . __( 'Activity Stream', 'buddypress' ) . '</option></select>';
  return $page_html;
}
add_filter( 'wp_dropdown_pages', 'bp_dtheme_wp_pages_filter' );


/*********
 * Hijack the saving of page on front setting to save the activity stream setting
 *
 * @param $string $oldvalue Previous value of get_option( 'page_on_front' )
 * @param $string $oldvalue New value of get_option( 'page_on_front' )
 * @return string
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_page_on_front_update( $oldvalue, $newvalue ) {
  if ( !is_admin() || !is_super_admin() )
    return false;

  if ( 'activity' == $_POST['page_on_front'] )
    return 'activity';
  else
    return $oldvalue;
}
add_action( 'pre_update_option_page_on_front', 'bp_dtheme_page_on_front_update', 10, 2 );


/*********
 * Load the activity stream template if settings allow
 *
 * @param string $template Absolute path to the page template 
 * @return string
 * @global WP_Query $wp_query WordPress query object
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_page_on_front_template( $template ) {
  global $wp_query;

  if ( empty( $wp_query->post->ID ) )
    return locate_template( array( 'activity/index.php' ), false );
  else
    return $template;
}
add_filter( 'page_template', 'bp_dtheme_page_on_front_template' );


/*********
 * Return the ID of a page set as the home page.
 *
 * @return false|int ID of page set as the home page
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_page_on_front() {
  if ( 'page' != get_option( 'show_on_front' ) )
    return false;

  return apply_filters( 'bp_dtheme_page_on_front', get_option( 'page_on_front' ) );
}


/*********
 * Force the page ID as a string to stop the get_posts query from kicking up a fuss.
 *
 * @global WP_Query $wp_query WordPress query object
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_fix_get_posts_on_activity_front() {
  global $wp_query;

  if ( !empty($wp_query->query_vars['page_id']) && 'activity' == $wp_query->query_vars['page_id'] )
    $wp_query->query_vars['page_id'] = '"activity"';
}
add_action( 'pre_get_posts', 'bp_dtheme_fix_get_posts_on_activity_front' );


/*********
 * WP 3.0 requires there to be a non-null post in the posts array
 *
 * @param array $posts Posts as retrieved by WP_Query
 * @global WP_Query $wp_query WordPress query object
 * @return array
 * @package BuddyPress Theme
 * @since 1.2.5
 */
function bp_dtheme_fix_the_posts_on_activity_front( $posts ) {
  global $wp_query;

  // NOTE: the double quotes around '"activity"' are thanks to our previous function bp_dtheme_fix_get_posts_on_activity_front()
  if ( empty( $posts ) && !empty( $wp_query->query_vars['page_id'] ) && '"activity"' == $wp_query->query_vars['page_id'] )
    $posts = array( (object) array( 'ID' => 'activity' ) );

  return $posts;
}
add_filter( 'the_posts', 'bp_dtheme_fix_the_posts_on_activity_front' );


/*********
 * Add secondary avatar image to this activity stream's record, if supported
 *
 * @param string $action The text of this activity
 * @param BP_Activity_Activity $activity Activity object
 * @return string
 * @package BuddyPress Theme
 * @since 1.2.6
 */
function bp_dtheme_activity_secondary_avatars( $action, $activity ) {
  switch ( $activity->component ) {
    case 'groups' :
    case 'blogs' :
    case 'friends' :
      // Only insert avatar if one exists
      if ( $secondary_avatar = bp_get_activity_secondary_avatar() ) {
        $reverse_content = strrev( $action );
        $position        = strpos( $reverse_content, 'a<' );
        $action          = substr_replace( $action, $secondary_avatar, -$position - 2, 0 );
      }
      break;
  }

  return $action;
}
add_filter( 'bp_get_activity_action_pre_meta', 'bp_dtheme_activity_secondary_avatars', 10, 2 );


/*********
 * Show a notice when the theme is activated - workaround by Ozh (http://old.nabble.com/Activation-hook-exist-for-themes--td25211004.html)
 *
 * @package BuddyPress Theme
 * @since 1.2
 */
function bp_dtheme_show_notice() { ?>
  <div id="message" class="updated fade">
    <p><?php printf( __( 'Theme activated! This theme supports <a href="%s">sidebar widgets</a>.', 'qmo' ), admin_url( 'widgets.php' ) ) ?></p>
  </div>

  <style type="text/css">#message2, #message0 { display: none; }</style>
  <?php
}
if ( is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) :
  add_action( 'admin_notices', 'bp_dtheme_show_notice' );
endif;


/*********
 * Member Buttons
 */
if ( bp_is_active( 'friends' ) ) :
  add_action( 'bp_member_header_actions',    'bp_add_friend_button' );
endif;
if ( bp_is_active( 'activity' ) ) :
  add_action( 'bp_member_header_actions',    'bp_send_public_message_button' );
endif;
if ( bp_is_active( 'messages' ) ) :
  add_action( 'bp_member_header_actions',    'bp_send_private_message_button' );
endif;


/*********
 * Group Buttons
 */
if ( bp_is_active( 'groups' ) ) {
  add_action( 'bp_group_header_actions',     'bp_group_join_button' );
  add_action( 'bp_group_header_actions',     'bp_group_new_topic_button' );
  add_action( 'bp_directory_groups_actions', 'bp_group_join_button' );
}


/*********
 * Blog Buttons
 */
if ( bp_is_active( 'blogs' ) ) :
  add_action( 'bp_directory_blogs_actions',  'bp_blogs_visit_blog_button' );
endif;
  

/*********
 * Remove "Visit" menu
 */
remove_action( 'bp_adminbar_menus', 'bp_adminbar_random_menu', 100 );


/*********
 * Customize "My Account" menu
 */
function qmo_adminbar_account_menu() {
  global $bp;

  if ( !$bp->bp_nav || !is_user_logged_in() )
    return false;

  echo '<li id="bp-adminbar-account-menu"><a href="' . bp_loggedin_user_domain() . '">';
  echo __( 'My Account', 'qmo' ) . '</a>';
  echo '<ul>';
?>
 
    <li><a id="bp-admin-profile" href="<?php echo bp_loggedin_user_domain() . BP_XPROFILE_SLUG ?>/"><?php _e('Profile', 'qmo'); ?></a>
      <ul>
        <li><a id="bp-admin-public" href="<?php echo bp_loggedin_user_domain() . BP_XPROFILE_SLUG ?>/public/"><?php _e('Public', 'qmo'); ?></a></li>
        <li><a id="bp-admin-edit" href="<?php echo bp_loggedin_user_domain() . BP_XPROFILE_SLUG ?>/edit/"><?php _e('Edit Profile', 'qmo'); ?></a></li>
        <li><a id="bp-admin-change-avatar" href="<?php echo bp_loggedin_user_domain() . BP_XPROFILE_SLUG ?>/change-avatar/"><?php _e('Change Avatar', 'qmo'); ?></a></li>
      </ul>
    </li>
    <li><a id="bp-admin-activity" href="<?php echo bp_loggedin_user_domain() . BP_ACTIVITY_SLUG ?>/"><?php _e('Activity', 'qmo'); ?></a></li>
    <li><a id="bp-admin-groups" href="<?php echo bp_loggedin_user_domain() . BP_GROUPS_SLUG ?>/"><?php _e('Teams', 'qmo'); ?> <span class="ct">(<?php echo bp_get_total_group_count_for_user( bp_loggedin_user_id() ); ?>)</span></a></li>
    <li><a id="bp-admin-settings" href="<?php echo bp_loggedin_user_domain(); ?>settings/"><?php _e('Settings', 'qmo'); ?></a>
      <ul>
        <li><a id="bp-admin-general" href="<?php echo bp_loggedin_user_domain(); ?>settings/general/"><?php _e('General', 'qmo'); ?></a></li>
        <li><a id="bp-admin-notifications" href="<?php echo bp_loggedin_user_domain(); ?>settings/notifications/"><?php _e('Notifications', 'qmo'); ?></a></li>
      </ul>
    </li>

<?php
  echo '<li><a id="bp-admin-logout" class="logout" href="' . wp_logout_url( site_url() ) . '">' . __( 'Log Out', 'qmo' ) . '</a></li>';
  echo '</ul>';
  echo '</li>';
}

remove_action( 'bp_adminbar_menus', 'bp_adminbar_account_menu', 4 );
add_action( 'bp_adminbar_menus', 'qmo_adminbar_account_menu', 4 );


/*********
 * Remove the logo from the admin bar
 */
remove_action( 'bp_adminbar_logo', 'bp_adminbar_logo' );


/*********
* Custom page title because Buddypress doesn't give us what we want.
* This function just duplicates wp_title and adds some of the extra bits from BP.
*/
function qmo_page_title($sep = '&#124;', $display = true, $seplocation = '') {
  global $wpdb, $wp_locale, $wp_query, $current_blog, $bp, $post;

  $cat = get_query_var('cat');
  $tag = get_query_var('tag_id');
  $category_name = get_query_var('category_name');
  $author = get_query_var('author');
  $author_name = get_query_var('author_name');
  $m = get_query_var('m');
  $year = get_query_var('year');
  $monthnum = get_query_var('monthnum');
  $day = get_query_var('day');
  $search = get_query_var('s');
  $title = '';

  $t_sep = '%WP_TITILE_SEP%'; // Temporary separator, for accurate flipping, if necessary
  
  if ( defined( 'BP_ENABLE_MULTIBLOG' ) ) {
    $blog_title = get_blog_option( $current_blog->blog_id, 'blogname' );
  } else {
    $blog_title = get_blog_option( BP_ROOT_BLOG, 'blogname' );
  }

  // If there's a category
  if ( !empty($cat) ) {
      // category exclusion
      if ( !stristr($cat,'-') )
        $title = apply_filters('single_cat_title', get_the_category_by_ID($cat)) . " $sep ";
  } elseif ( !empty($category_name) ) {
    if ( stristr($category_name,'/') ) {
        $category_name = explode('/',$category_name);
        if ( $category_name[count($category_name)-1] )
          $category_name = $category_name[count($category_name)-1]; // no trailing slash
        else
          $category_name = $category_name[count($category_name)-2]; // there was a trailling slash
    }
    $cat = get_term_by('slug', $category_name, 'category', OBJECT, 'display');
    if ( $cat )
      $title = apply_filters('single_cat_title', $cat->name);
  }

  if ( !empty($tag) ) {
    $tag = get_term($tag, 'post_tag', OBJECT, 'display');
    if ( is_wp_error( $tag ) )
      return $tag;
    if ( ! empty($tag->name) )
      $title = sprintf( __( 'Posts tagged &ldquo;%s&rdquo;', 'qmo' ), apply_filters('single_tag_title', $tag->name)) . " $sep ";
  }

  // If there's an author
  if ( !empty($author) ) {
    $title = get_userdata($author);
    $title = $title->display_name;
  }
  if ( !empty($author_name) ) {
    // We do a direct query here because we don't cache by nicename.
    $title = sprintf( __( 'Posts by %s', 'qmo' ), $wpdb->get_var($wpdb->prepare("SELECT display_name FROM $wpdb->users WHERE user_nicename = %s", $author_name)) ) . " $sep ";
  }

  // If there's a month
  if ( !empty($m) ) {
    $my_year = substr($m, 0, 4);
    $my_month = $wp_locale->get_month(substr($m, 4, 2));
    $my_day = intval(substr($m, 6, 2));
    $title = $my_year . ($my_month ? $t_sep . $my_month : "") . ($my_day ? $t_sep . $my_day : "");
  }

  if ( !empty($year) ) {
    $title = $year;
    if ( !empty($monthnum) )
      $title .= $t_sep . $wp_locale->get_month($monthnum);
    if ( !empty($day) )
      $title .= $t_sep . zeroise($day, 2);
  }

  // If there is a post
  if ( is_single() || ( is_home() && !is_front_page() ) || ( is_page() && !is_front_page() ) ) {
    $post = $wp_query->get_queried_object();
    $title = apply_filters( 'single_post_title', $post->post_title ) . " $sep ";
  }

  // If there's a taxonomy
  if ( is_tax() ) {
    $taxonomy = get_query_var( 'taxonomy' );
    $tax = get_taxonomy( $taxonomy );
    $term = $wp_query->get_queried_object();
    $term = $term->name;
    $title = $tax->labels->name . $t_sep . $term;
  }

  //If it's a search
  if ( is_search() ) {
    /* translators: 1: search phrase */
    $title = sprintf(__('Search results for &ldquo;%1$s&rdquo;'), strip_tags($search));
  }
  
  //If it's a 404
  if ( is_404() ) {
    $title = __('Not Found') . " $sep ";
  }
  
  $prefix = '';
  if ( !empty($title) )
    $prefix = " $sep ";

  // Determines position of the separator and direction of the breadcrumb
  if ( 'right' == $seplocation ) { // sep on right, so reverse the order
    $title_array = explode( $t_sep, $title );
    $title_array = array_reverse( $title_array );
    $title = implode( " $sep ", $title_array );
  } 
  else {
    $title_array = explode( $t_sep, $title );
    $title = $prefix . implode( " $sep ", $title_array );
  }
  
  if ( !empty( $bp->displayed_user->fullname ) ) {
    $title = strip_tags( $bp->displayed_user->fullname . " $sep " . ucwords( $bp->current_component ) . " $sep " );
  } 
  else if ( $bp->is_single_item ) {
    $title = $bp->bp_options_title . " $sep " . ucwords( $bp->current_component ) . " $sep ";
  } 
  else if ( $bp->is_directory ) {
    if ( !$bp->current_component )
      $title = ucwords( BP_MEMBERS_SLUG ) . " $sep ";
    else
      $title = ucwords( $bp->current_component ) . " $sep ";
  } 
  else if ( bp_is_register_page() ) {
    $title = __( 'Create an Account', 'qmo' ) . " $sep ";
  } 
  else if ( bp_is_activation_page() ) {
    $title = __( 'Activate Your Account', 'qmo' ) . " $sep ";
  } 
  else if ( bp_is_group_create() ) {
    $title = __( 'Create a Team', 'qmo' ) . " $sep ";
  } 
  else if ( bp_is_create_blog() ) {
    $title = __( 'Create a Blog', 'qmo' ) . " $sep ";
  }
  
  // Forum pages
  if ( $bp->current_action == 'forum' && $bp->action_variables[0] == 'topic' ) {
    if ( bp_has_forum_topic_posts() ) {
      $topic_title = bp_get_the_topic_title();
      $title = $topic_title . " $sep ";
    }
  }

  $title = apply_filters('qmo_page_title', $title, $sep, $seplocation);

  // Send it out
  if ( $display )
    echo $title;
  else
    return $title;
}


/*********
 * Disable automatic links in member profiles
 */
remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 50 );


/*********
 * Determine whether to show user meta box on member pages.
 * This is a bit of a hack.
 */
function qmo_show_user_meta() {
  global $bp;
  
  // If BuddyPress Member Profile Stats is turned on
  if ( function_exists('bp_member_profile_stats_header_meta') )
    return true;
  
  // If BuddyPress Rate Forum Posts is turned on
  if ( function_exists('rfp_show_poster_karma') ) :
    $karma = get_usermeta( $bp->displayed_user->id, 'rfp_post_karma' );
    $relative_karma = rfp_calculate_relative_karma( $karma, $bp->displayed_user->id );
    // If the member has karma to display
    if ( get_option( 'rfp_karma_hide' ) || $relative_karma == 0 || get_option( 'rfp_karma_never_minus' ) && $karma < 0 ) :
      return false;
    else :
      return true;
    endif;
  endif;
}


/*********
 * Customize group admin list to include the display name in a title
 */
function qmo_group_list_admins( $group = false ) {
  global $bp;
  global $groups_template;
  if ( !$group ) :
    $group =& $groups_template->group;
  endif;
  if ( $group->admins ) : ?>
    <ul id="group-admins">
      <?php foreach( (array)$group->admins as $admin ) { ?>
        <li>
          <a title="<?php echo bp_core_get_user_displayname( $admin->user_id ); ?>" href="<?php echo bp_core_get_user_domain( $admin->user_id, $admin->user_nicename, $admin->user_login ) ?>"><?php echo bp_core_fetch_avatar( array( 'item_id' => $admin->user_id, 'email' => $admin->user_email ) ); ?></a>
        </li>
      <?php } ?>
    </ul>
  <?php endif; ?>
<?php
}


/*********
 * Customize group mod list include the display name in a title
 */
function qmo_group_list_mods( $group = false ) {
  global $groups_template;
  if ( !$group ) :
    $group =& $groups_template->group;
  endif;
  if ( $group->mods ) : ?>
    <ul id="group-mods">
      <?php foreach( (array)$group->mods as $mod ) { ?>
        <li>
          <a title="<?php echo bp_core_get_user_displayname( $mod->user_id ); ?>" href="<?php echo bp_core_get_user_domain( $mod->user_id, $mod->user_nicename, $mod->user_login ) ?>"><?php echo bp_core_fetch_avatar( array( 'item_id' => $mod->user_id, 'email' => $mod->user_email ) ); ?></a>
        </li>
      <?php } ?>
    </ul>
  <?php endif; ?>
<?php
}


/*********
 * Exclude certain types of activities from showing up in streams
 */
function qmo_activity_filter( $a, $activities ) {
  global $bp; 

  /* Only run the filter on activity streams where you want blog comments filtered out. 
   * For example, the following will only filter them on the main activity page.
   * Member activity streams have their own loop where we're already excluding unwanted actions.
   */
  if ( $bp->current_component != $bp->activity->slug )
    return $activities;

  /* Filter out unwanted actions */
  foreach( $activities->activities as $key => $activity ) {
  /* HACK: Checking types might be better as an array. So many ORs seems sloppy. */
    if ( 
          $activity->type === 'joined_group' 
          || $activity->type === 'created_group'
          || $activity->type === 'new_blog_comment'
          || $activity->type === 'new_status'
          || $activity->type === 'new_wire_post'
          || $activity->type === 'friendship_created'
          || $activity->type === 'new_member'
          || $activity->type === 'new_achievement'
          || $activity->type === 'achievement_created'
        ) {
      unset( $activities->activities[$key] );
      $activities->total_activity_count = $activities->total_activity_count - 1;
      $activities->activity_count = $activities->activity_count - 1;
    }
  }

  /* Renumber the array keys to account for missing items */
  $activities_new = array_values( $activities->activities );
  $activities->activities = $activities_new;
  return $activities;
}
add_action( 'bp_has_activities', 'qmo_activity_filter', 10, 2 );

/*********
 * Turn off all achievement notification emails
 */
remove_action( 'dpa_achievement_unlocked', 'dpa_achievement_unlocked_notification', 10, 2 );

/*********
 * Limit avatar uploads to 100k (102,400 bytes)
 */
define('BP_AVATAR_ORIGINAL_MAX_FILESIZE', 102400);

/** END BUDDYPRESS FUNCTIONS **/
