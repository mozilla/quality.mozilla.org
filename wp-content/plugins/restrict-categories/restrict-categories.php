<?php
/*
Plugin Name: Restrict Categories
Description: Restrict the categories that users can view, add, and edit in the admin panel.
Author: Matthew Muro
Version: 1.7
*/

/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/


/* Make sure we are in the admin before proceeding. */
if ( is_admin() ) {

$post_type = ( isset($_GET['post_type']) ) ? $_GET['post_type'] : false;

/* If the page is the Posts screen, do our thing, otherwise chill */
if ( $post_type == false || $post_type == 'post' ) {
	/* Where the magic happens */
	add_action( 'admin_init', 'RestrictCats_posts' );
}

/* Build options and settings pages. */
add_action( 'admin_init', 'RestrictCats_init' );
add_action( 'admin_menu', 'RestrictCats_add_admin' );

/**
 * Get all categories that will be used as options.
 * 
 * @since 1.0
 * @uses get_categories() Returns an array of category objects matching the query parameters.  
 * @return $cat array All category slugs.
 */
function RestrictCats_get_cats(){
	$categories = get_terms( 'category','hide_empty=0' );

	foreach ( $categories as $category ) {
		$cat[] = array(
			'slug' => $category->slug
			);
	}

	return $cat;
}

/**
 * Set up the options array which will output all roles with categories.
 * 
 * @since 1.0
 * @uses RestrictCats_get_roles() Returns an array of all user roles.
 * @uses RestrictCats_get_cats() Returns an array of all categories.
 * @return $rc_options array Multidimensional array with options.
 */
function RestrictCats_populate_opts(){
	$roles = RestrictCats_get_roles();
	$cats = RestrictCats_get_cats();
	
	foreach ( $roles as $name => $id ) {
			$rc_options[] = 
				array(
				'name' => $name,
				'id' => $id . '_cats',
				'options' => $cats
				);
	}
	
	return $rc_options;	
}

/**
 * Set up the user options array which will output all users with categories.
 * 
 * @since 1.6
 * @uses RestrictCats_get_logins() Returns an array of all user logins.
 * @uses RestrictCats_get_cats() Returns an array of all categories.
 * @return $rc_user_options array Multidimensional array with options.
 */
function RestrictCats_populate_user_opts(){
	$logins = RestrictCats_get_logins();
	$cats = RestrictCats_get_cats();
	
	foreach ( $logins as $name => $id ) {
			$rc_user_options[] = 
				array(
				'name' => $name,
				'id' => $id . '_user_cats',
				'options' => $cats
				);
	}

	return $rc_user_options;	
}


/**
 * Set up the roles array which uses similar code to wp_dropdown_roles().
 * 
 * @since 1.0
 * @uses get_editable_roles() Fetch a filtered list of user roles that the current user is allowed to edit.
 * @return $roles array Returns array of user roles with the "pretty" name and the slug.
 */
function RestrictCats_get_roles(){
	$editable_roles = get_editable_roles();

	foreach ( $editable_roles as $role => $name ) {
		$roles[ $name['name'] ] = $role;
	}

	return $roles;
}

/**
 * Set up the user logins array.
 * 
 * @since 1.6
 * @uses get_users Returns an array filled with information about the blog's users. WP 3.1
 * @uses get_users_of_blog() Returns an array filled with information about the blog's users. WP 3.0
 * @return $users array Returns array of user logins.
 */
function RestrictCats_get_logins(){
	if ( function_exists( 'get_users' ) ){
		$blogusers = get_users();
		
		foreach ( $blogusers as $login ) {
			$users[ $login->user_login ] = $login->user_nicename;
		}
	}
	elseif ( function_exists( 'get_users_of_blog' ) ){
		$blogusers = get_users_of_blog();
		
		foreach ( $blogusers as $login ) {
			$users[ $login->user_login ] = $login->user_id;
		}
	}

	return $users;
}

/**
 * Register 
 * 
 * @since 1.0
 * @uses register_setting() Register a setting in the database
 */
function RestrictCats_init() {
	register_setting( 'RestrictCats_options_group', 'RestrictCats_options', 'RestrictCats_options_sanitize' );
	register_setting( 'RestrictCats_options_group', 'RestrictCats_user_options', 'RestrictCats_options_sanitize' );
}

/**
 * Sanitize input
 * 
 * @since 1.3
 * @return $input array Returns array of input if available
 */
function RestrictCats_options_sanitize( $input ){
	if ( !is_array( $input ) )
		return $input;
	
	foreach ( $input as $value ) {
		$input[$value] = ( $input[$value] == 1 ? 1 : 0 );
	}
}

/**
 * Performs the save.
 * 
 * @todo Improve with register_setting?
 * 
 * @since 1.0
 * @global $rc_options array The global options array populated by RestrictCats_populate_opts().
 * @uses RestrictCats_populate_opts() Returns multidimensional array of roles and categories.
 * @uses update_option() A safe way to update a named option/value pair to the options database table.
 * @uses add_management_page() Creates a menu item under the Tools menu.
 */
function RestrictCats_add_admin() {
	global $rc_options, $rc_user_options;

	$rc_options = RestrictCats_populate_opts();
	$rc_user_options = RestrictCats_populate_user_opts();
	
	/* Check if the page has been submitted */
	if ( $_GET['page'] == plugin_basename(__FILE__) ) {
		$nonce = $_REQUEST['_wpnonce'];
		
		/* Check if the Save Changes button has been pressed */
		if ( 'save' == $_REQUEST['action'] ) {
			
			/* Security check to verify the nonce */
			if (! wp_verify_nonce($nonce, 'rc-save-nonce') )
				die(__('Security check'));
			
			/* Loop through all options and add/remove new values */	
			foreach ( $rc_options as $value ) {
				$key = $value['id'];

				$settings[ $key ] = $_REQUEST[ $key ];
			}
			
			update_option( 'RestrictCats_options', $settings );
			
			/* Loop through all options and add/remove new values */	
			foreach ( $rc_user_options as $value_user ) {
				$key_user = $value_user['id'];

				$settings_user[ $key_user ] = $_REQUEST[ $key_user ];
			}
			
			update_option( 'RestrictCats_user_options', $settings_user );
			
			/* Set submitted action to display success message */
			$_POST['saved'] = true;
		}
		/* Check if the Reset button has been pressed */
		elseif ( 'reset' == $_REQUEST['action'] ) {
			
			/* Security check to verify the nonce */
			if ( ! wp_verify_nonce($nonce, 'rc-reset-nonce') )
				die(__('Security check'));
			
			/* Loop through all options and reset values */
			foreach ( $rc_options as $value ) {
				$new_options[ $value['id'] ];
			}

			update_option( 'RestrictCats_options', $new_options );
			
			/* Loop through all options and reset values */
			foreach ( $rc_user_options as $value_user ) {
				$new_user_options[ $value_user['id'] ];
			}

			update_option( 'RestrictCats_user_options', $new_user_options );
			
			/* Set submitted action to display success message */
			$_POST['reset'] = true;
		}
	}
	   
	add_options_page( __('Restrict Categories', 'restrict-categories'), __('Restrict Categories', 'restrict-categories'), 'create_users', plugin_basename(__FILE__), 'RestrictCats_admin' );
}

/**
 * Builds the options settings page
 * 
 * @since 1.0
 * @global $rc_options array The global options array populated by RestrictCats_populate_opts().
 * @global $rc_user_options array The global options array populated by RestrictCats_populate_user_opts().
 * @uses get_option() A safe way to get options from the options database table.
 * @uses wp_list_categories() Displays a list of categories
 */
function RestrictCats_admin() {
	global $rc_options, $rc_user_options;
	
	/* Success messages for completing the form */
	if ( $_POST['saved'] )
		_e('<div id="message" class="updated fade"><p><strong>Restrict Categories settings saved.</strong></p></div>', 'restrict-categories');
	if ( $_POST['reset'] )
		_e('<div id="message" class="updated fade"><p><strong>Restrict Categories settings reset.</strong></p></div>', 'restrict-categories');
?>

	<div class="wrap">
        <div class="icon32" id="icon-options-general"><br></div>
        <h2><?php _e('Restrict Categories', 'restrict-categories'); ?></h2>
        
        <form method="post">
        <fieldset>
        <h3>Roles</h3>
        <?php
        $settings = get_option( 'RestrictCats_options' );
		
		/* Create a new instance of our custom walker class */
		$walker = new RestrictCats_Walker_Category_Checklist();

		/* Loop through each role and build the checkboxes */
        foreach ( $rc_options as $value ) : 
            $id = $value['id'];
			
			/* Get selected categories from database, if available */
			if ( is_array( $settings[ $id ] ) )
				$selected = $settings[ $id ];
			else
				$selected = array();
		?>
            <div class="metabox-holder" style="float:left; padding:5px;">
                <div class="postbox">
                <h3><span><?php echo $value['name']; ?></span></h3>
                    <div class="inside">
                        <div class="taxonomydiv">
                            <div class="tabs-panel">
                                <ul>
                                <?php
									wp_list_categories(
										array(
										'admin' => $id,
										'selected_cats' => $selected,
										'hide_empty' => 0,
										'title_li' => '',
										'walker' => $walker
										)
									);
                                ?>
                                </ul>
                            </div>
                        </div>
                    	
                        <p style="padding-left:10px;"><strong><?php echo count($selected); ?></strong> <?php echo (count($selected) > 1 || count($selected) == 0) ? 'categories' : 'category'; ?> selected</p>
                        
                    </div>
                </div>
            </div>
        <?php 
        endforeach;
        ?>
		</fieldset>
        
        <fieldset>
        <h3>Users</h3>
        <p>Selecting categories for a user will <em>override</em> the categories you have chosen for that user's role.</p>
        <?php
        $settings_user = get_option( 'RestrictCats_user_options' );

		/* Loop through each user and build the checkboxes */
        foreach ( $rc_user_options as $value ) : 
            $id = $value['id'];

			/* Get selected categories from database, if available */
			if ( is_array( $settings_user[ $id ] ) )
				$selected = $settings_user[ $id ];
			else
				$selected = array();
		?>
            <div class="metabox-holder" style="float:left; padding:5px;">
                <div class="postbox">
                <h3><span><?php echo $value['name']; ?></span></h3>
                    <div class="inside">
                        <div class="taxonomydiv">
                            <div class="tabs-panel">
                                <ul>
                                <?php
									wp_list_categories(
										array(
										'admin' => $id,
										'selected_cats' => $selected,
										'hide_empty' => 0,
										'title_li' => '',
										'walker' => $walker
										)
									);
                                ?>
                                </ul>
                            </div>
                        </div>
                    	
                        <p style="padding-left:10px;"><strong><?php echo count($selected); ?></strong> <?php echo (count($selected) > 1 || count($selected) == 0) ? 'categories' : 'category'; ?> selected</p>
                        
                    </div>
                </div>
            </div>
        <?php 
        endforeach;
		
		wp_nonce_field( 'rc-save-nonce' );

		?>
        </fieldset>
        
        <input class="button-primary" name="save" type="submit" value="<?php _e('Save Changes', 'restrict-categories'); ?>" />   
        
        <input type="hidden" name="action" value="save" />
        </form>
        
        <h3><?php _e('Reset to Default Settings', 'restrict-categories'); ?></h3>
        <p><?php _e('This option will reset all changes you have made to the default configuration.  <strong>You cannot undo this process</strong>.', 'restrict-categories'); ?></p>
        <form method="post">
        <input class="button-secondary" name="reset" type="submit" value="<?php _e('Reset', 'restrict-categories'); ?>" />
        <input type="hidden" name="action" value="reset" />
        <?php wp_nonce_field( 'rc-reset-nonce' ); ?>
        </form>
    </div>
<?php

}

/**
 * Rewrites the query to only display the selected categories from the settings page
 * 
 * @since 1.0
 * @global $wp_query object The global WP_Query object.
 * @global $current_user object The global user object.
 * @uses WP_User() Retrieve user object.
 * @uses get_option() A safe way to get options from the options database table.
 */
function RestrictCats_posts() {
	global $wp_query, $current_user, $cat_list;
	
	/* Get the current user in the admin */
	$user = new WP_User( $current_user->ID );
	
	/* Get the user role */
	$user_cap = $user->roles;
	
	/* Get the user login name/ID */
	if ( function_exists( 'get_users' ) )
		$user_login = $user->user_login;
	elseif ( function_exists( 'get_users_of_blog' ) )
		$user_login = $user->ID;
	
	$settings_user = get_option( 'RestrictCats_user_options' );
	
	/* Selected categories for user overwrites Roles selection */
	if ( is_array( $settings_user ) && !empty( $settings_user[ $user_login . '_user_cats' ] ) ) {
			
		/* Build the category list */
		foreach ( $settings_user[ $user_login . '_user_cats' ] as $category ) {
			$cat_list .= get_term_by( 'slug', $category, 'category' )->term_id . ',';
		}

		RestrictCats_cat_filters( $cat_list );
	}
	else {
		foreach ( $user_cap as $key ) {
			$settings = get_option( 'RestrictCats_options' );
			
			/* Make sure the settings from the DB isn't empty before building the category list */
			if ( is_array( $settings ) && !empty( $settings[ $key . '_cats' ] ) ) {
				
				/* Build the category list */
				foreach ( $settings[ $key . '_cats' ] as $category ) {
					$cat_list .= get_term_by( 'slug', $category, 'category' )->term_id . ',';
				}
			}
	
			RestrictCats_cat_filters( $cat_list );
		}
	}
}

/**
 * Adds filters for category restriction
 * 
 * @since 1.6
 * @global $cat_list string The global comma-separated list of restricted categories.
 */
function RestrictCats_cat_filters( $cat_list ){
	global $cat_list;
	
	/* Clean up the category list */
	$cat_list = rtrim( $cat_list, ',' );
		
	/* If there are no categories, don't do anything */
	if ( $cat_list !== '' ) {	
		add_filter( 'pre_get_posts','RestrictCats_posts_query' );
		add_filter( 'list_terms_exclusions', 'RestrictCats_exclusions' );
	}	
}

/**
 * Remove posts from edit.php with restricted categories
 * 
 * @since 1.6
 * @global $cat_list string The global comma-separated list of restricted categories.
 * @return $query array Sets 'category__in' query_var with an array of category IDs
 */
function RestrictCats_posts_query( $query ){
	global $cat_list;
	
	if ( $cat_list !== '' ) {
		
		/* Build an array for the categories */
		$cat_list_array = explode( ',', $cat_list );
		
		/* Only restrict the query for posts_types that aren't attachments */
		if ( $query->get( 'post_type' ) != 'attachment' ){
			
			/* Make sure the posts are removed by default or if filter category is ran */
			if ( ! isset( $_REQUEST['cat']) )
				$query->set( 'category__in', $cat_list_array );
			elseif( isset( $_REQUEST['cat'] ) && $_REQUEST['cat'] == '0' )
				$query->set( 'category__in', $cat_list_array );
		}
	}

    return $query;
}

/**
 * Explicitly remove extra categories from view that user can manage
 * Will affect Category management page, Posts dropdown filter, and New/Edit post category list
 * 
 * @since 1.3
 * @global $cat_list string The global comma-separated list of restricted categories.
 * @return $excluded string Appended clause on WHERE of get_taxonomy
 */
function RestrictCats_exclusions(){
	global $cat_list;
	
	$excluded = ' AND t.term_id IN (' . $cat_list . ')';
	
	return $excluded;
}

/**
 * Custom walker class to create a category checklist
 * 
 * @since 1.5
 */
class RestrictCats_Walker_Category_Checklist extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

	function start_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children' style='padding-left:15px;'>\n";
	}

	function end_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el(&$output, $category, $depth, $args) {
		extract($args);
		if ( empty($taxonomy) )
			$taxonomy = 'category';

		if ( $taxonomy == 'category' )
			$name = 'post_category';
		else
			$name = 'tax_input['.$taxonomy.']';

		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'>" . 
		'<label class="selectit"><input value="' . $category->slug . '" type="checkbox" name="'. $admin .'[]" id="' . $admin . '-' . $category->slug . '"' . checked( in_array( $category->slug, $selected_cats ), true, false ) . ' /> ' . esc_html( apply_filters('the_category', $category->name )) . '</label>';
	}

	function end_el(&$output, $category, $depth, $args) {
		$output .= "</li>\n";
	}
}

}/* endif is_admin */
?>