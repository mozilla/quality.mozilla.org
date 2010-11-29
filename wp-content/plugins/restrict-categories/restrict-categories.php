<?php
/*
Plugin Name: Restrict Categories
Description: Restrict the categories that users in defined roles can view, add, and edit in the admin panel.
Author: Matthew Muro
Version: 1.3
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
	
/* Setup global prefixes. */
$rc_name = 'Restrict Categories';
$rc_shortname = 'RestrictCats';

/* Only restrict the Edit Posts page. */
global $pagenow;
//if ( $pagenow == 'edit.php' )
	add_action( 'admin_head', 'RestrictCats_posts' );

/* Build options and settings pages. */
add_action('admin_init', 'RestrictCats_init');
add_action('admin_menu', 'RestrictCats_add_admin');

/**
 * Get all categories that will be used as options.
 * 
 * @since 1.0
 * @uses get_categories() Returns an array of category objects matching the query parameters.  
 * @return $cat array All category names.
 */
function RestrictCats_get_cats(){
	$categories = get_categories('hide_empty=0');
	
	foreach( $categories as $category ){
		$cat[] = $category->name;
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
	
	foreach( $roles as $name => $id ){
			$rc_options[] = 
				array(
				'name' => $name,
				'id' => $id . '_cats',
				'options' => array_unique($cats)
				);
	}
	
	return $rc_options;	
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
 * Register 
 * 
 * @since 1.0
 * @global $rc_shortname string The global plugin abbreviated name
 * @uses register_setting() Register a setting int he database
 */
function RestrictCats_init() {
	global $rc_shortname;
	
	register_setting( $rc_shortname.'_options_group', $rc_shortname.'_options', $rc_shortname.'_options_sanitize' );
}

/**
 * Sanitize input
 * 
 * @since 1.3
 */
function RestrictCats_options_sanitize($input){
	if( !is_array( $input ) )
		return $input;
}

/**
 * Performs the save.
 * 
 * @todo Improve with register_setting?
 * 
 * @since 1.0
 * @global $rc_name string The global plugin name.
 * @global $rc_shortname string The global plugin abbreviated name.
 * @global $rc_options array The global options array populated by RestrictCats_populate_opts().
 * @uses RestrictCats_populate_opts() Returns multidimensional array of roles and categories.
 * @uses update_option() A safe way to update a named option/value pair to the options database table.
 * @uses add_management_page() Creates a menu item under the Tools menu.
 */
function RestrictCats_add_admin() {
	global $rc_name, $rc_shortname, $rc_options;

	$rc_options = RestrictCats_populate_opts();
	
	if ( $_GET['page'] == plugin_basename(__FILE__) ) {
		$nonce = $_REQUEST['_wpnonce'];
		
		if ( 'save' == $_REQUEST['action'] ) {
			
			if (! wp_verify_nonce($nonce, 'rc-save-nonce') )
				die('Security check');
					
			foreach ( $rc_options as $value ) {
				$key = $value['id'];

				$settings[$key] = $_REQUEST[$key];
			}
			
			update_option( $rc_shortname.'_options', $settings );
			
			$_POST['saved'] = true;
		}
		elseif ( 'reset' == $_REQUEST['action'] ) {
			
			if ( ! wp_verify_nonce($nonce, 'rc-reset-nonce') )
				die('Security check');
			
			foreach ( $rc_options as $value ) {
				$new_options[$value['id']];
			}

			update_option( $rc_shortname.'_options', $new_options );
			
			$_POST['reset'] = true;
		}
	}
	   
	add_options_page( $rc_name, 'Restrict Categories', 'create_users', plugin_basename(__FILE__), 'RestrictCats_admin' );
}

/**
 * Builds the options settings page
 * 
 * @since 1.0
 * @global $rc_name string The global plugin name.
 * @global $rc_shortname string The global plugin abbreviated name.
 * @global $rc_options array The global options array populated by RestrictCats_populate_opts().
 * @uses get_option() A safe way to get options from the options database table.
 */
function RestrictCats_admin() {
	global $rc_name, $rc_shortname, $rc_options;
	
	if ( $_POST['saved'] )
		echo '<div id="message" class="updated fade"><p><strong>' . $rc_name . ' settings saved.</strong></p></div>';
	if ( $_POST['reset'] )
		echo '<div id="message" class="updated fade"><p><strong>' . $rc_name . ' settings reset.</strong></p></div>';
?>

	<div class="wrap">
        <div class="icon32" id="icon-options-general"><br></div>
        <h2><?php echo $rc_name; ?></h2>
        
        <form method="post">
        <table class="form-table">
        <tbody>
        <?php
        $settings = get_option( $rc_shortname.'_options' );
		
        foreach ( $rc_options as $value ) : 
            $id = $value['id'];
        ?>
            <tr valign="top"> 
                <th scope="row"><?php echo $value['name']; ?></th>
                <td>
                    <fieldset>
                        <?php
                        foreach ( $value['options'] as $option ) {
							if ( is_array( $settings[ $id ] ) && !empty( $settings[ $id ] ) ){		//determine if settings is an array and not empty before checking boxes
								if ( in_array( $option, $settings[ $id ] ) )	//check the box if there is a match from options
									$checked = 'checked="checked"';
								else
									$checked = '';
							}
                        ?>
                        <div style="overflow:hidden; margin:0 0 5px; float:left; width: 25%;">
                            <label for="<?php echo $id . '-' . $option; ?>">
                                <input id="<?php echo $id . '-' . $option; ?>" name="<?php echo $id; ?>[]" type="checkbox" value="<?php echo $option; ?>" <?php echo $checked; ?>/> <?php echo $option; ?>
                            </label>
                        </div>
                        <?php } ?>
                        <div style="clear:both"></div>
                    </fieldset>
                </td>
            </tr>	
        <?php 
        endforeach;
        
        wp_nonce_field( 'rc-save-nonce' );
        ?>
        </tbody>
        </table>
        <p class="submit">
        <input class="button-primary" name="save" type="submit" value="Save Changes" />    
        <input type="hidden" name="action" value="save" />
        </form>
        </p>
        <h3>Reset to Default Settings</h3>
        <p>This option will reset all changes you have made to the default configuration.  <strong>You cannot undo this process</strong>.</p>
        <form method="post">
        <input class="button-secondary" name="reset" type="submit" value="Reset" />
        <input type="hidden" name="action" value="reset" />
        <?php wp_nonce_field( 'rc-reset-nonce' ); ?>
        </form>
    </div>
<?php

}

/**
 * Rewrites the query to only display the selected categories from the settings page
 * 
 * @todo Allow restriction of categories based on username?
 * 
 * @since 1.0
 * @global $wp_query object The global WP_Query object.
 * @global $current_user object The global user object.
 * @global $rc_shortname string The global plugin abbreviated name.
 * @uses RestrictCats_populate_opts() Returns multidimensional array of roles and categories.
 * @uses get_user_meta() Retrieve user meta field for a user.
 * @uses get_option() A safe way to get options from the options database table.
 */
function RestrictCats_posts() {
	global $wp_query, $current_user, $rc_shortname, $cat_list;
	
	get_currentuserinfo();
	
	$user_id = $current_user->ID;
	$val = 'wp_capabilities';
	$single = true;
	$user_cap = get_user_meta( $current_user->ID, 'wp_capabilities', true );
	
	foreach ( $user_cap as $key => $value ) {
		$settings = get_option( $rc_shortname.'_options' );
		
		if ( is_array( $settings ) && !empty( $settings[ $key . '_cats' ] ) ){
			foreach ( $settings[ $key . '_cats' ] as $category ) {
				$cat_list .= get_cat_ID( $category ) . ',';
			}
		}
	
		$cat_list = rtrim( $cat_list, ',' );
		$cat_list_array = explode( ',', $cat_list );

		if ( $user_cap[$key] && $cat_list !== '' ) {
			
			add_filter('list_terms_exclusions',	'RestrictCats_exclusions');
			
			if( in_array( $_REQUEST['cat'], $cat_list_array ) ){
				$wp_query->query( 'cat=' . $_REQUEST['cat'] );
			}
			else
				$wp_query->query( 'cat=' . $cat_list );
		}
	}
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

}//endif is_admin
?>