<?php
/*
Plugin Name: Restrict Categories
Description: Restrict the categories that users can view, add, and edit in the admin panel.
Author: Matthew Muro
Version: 2.3
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

/* Instantiate new class */
$rc = new RestrictCategories();

/* Restrict Categories class */
class RestrictCategories{
	
	private $cat_list = NULL;
	
	public function __construct(){
		/* Make sure we are in the admin before proceeding. */
		if ( is_admin() ) {
			$post_type = ( isset( $_GET['post_type'] ) ) ? $_GET['post_type'] : false;

  			/* If the page is the Posts screen, do our thing, otherwise chill */
			if ( $post_type == false || $post_type == 'post' )
				add_action( 'admin_init', array( &$this, 'posts' ) );
			
			/* Build options and settings pages. */
			add_action( 'admin_init', array( &$this, 'init' ) );
			add_action( 'admin_menu', array( &$this, 'add_admin' ) );
			
			/* Adds a Settings link to the Plugins page */
			add_filter( 'plugin_action_links', array( &$this, 'rc_plugin_action_links' ), 10, 2 );
		}
		
		/* Make sure XML-RPC requests are filtered to match settings */
		if ( defined ( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST )
			add_action( 'xmlrpc_call', array( &$this, 'posts' ) );
	}
	
	/**
	 * Add Settings link to Plugins page
	 * 
	 * @since 1.8 
	 * @return $links array Links to add to plugin name
	 */
	public function rc_plugin_action_links($links, $file){
		if ( $file == plugin_basename(__FILE__) )
			$links[] = '<a href="options-general.php?page=restrict-categories">'.__('Settings').'</a>';
	
		return $links;
	}
	
	/**
	 * Get all categories that will be used as options.
	 * 
	 * @since 1.0
	 * @uses get_categories() Returns an array of category objects matching the query parameters.  
	 * @return $cat array All category slugs.
	 */
	public function get_cats(){
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
	 * @uses get_roles() Returns an array of all user roles.
	 * @uses get_cats() Returns an array of all categories.
	 * @return $rc_options array Multidimensional array with options.
	 */
	public function populate_opts(){
		$roles = $this->get_roles();
		$cats = $this->get_cats();
		
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
	 * @uses get_logins() Returns an array of all user logins.
	 * @uses get_cats() Returns an array of all categories.
	 * @return $rc_user_options array Multidimensional array with options.
	 */
	public function populate_user_opts(){
		$logins = $this->get_logins();
		$cats = $this->get_cats();
		
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
	public function get_roles(){
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
	public function get_logins(){
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
	 * Register database options and set defaults, which are blank
	 * 
	 * @since 1.0
	 * @uses register_setting() Register a setting in the database
	 */
	public function init() {
		register_setting( 'RestrictCats_options_group', 'RestrictCats_options', array( &$this, 'options_sanitize' ) );
		register_setting( 'RestrictCats_user_options_group', 'RestrictCats_user_options', array( &$this, 'options_sanitize' ) );
				
		/* Set the options to a variable */
		$options = get_option( 'RestrictCats_options' );
		$user_options = get_option( 'RestrictCats_user_options' );
		
		/* If the options are empty, add the defaults */
		if ( !$options )
			update_option( 'RestrictCats_options', array() );
			
		if ( !$user_options )
			update_option( 'RestrictCats_user_options', array() );	
	}
	
	/**
	 * Sanitize input
	 * 
	 * @since 1.3
	 * @return $input array Returns array of input if available
	 */
	public function options_sanitize( $input ){
		if ( is_array( $input ) )
			return $input;
	}
	
	/**
	 * Add options page and handle data reset
	 * 
	 * 
	 * @since 1.0
	 * @uses add_options_page() Creates a menu item under the Settings menu.
	 */
	public function add_admin() {
		/* Resets the options */
		if ( $_GET['page'] == 'restrict-categories' && 'reset' == $_REQUEST['action'] ) {
			$nonce = $_REQUEST['_wpnonce'];
			
			/* Security check to verify the nonce */
			if ( ! wp_verify_nonce($nonce, 'rc-reset-nonce') )
				die(__('Security check'));
			
			/* Reset Roles and Users options */
			update_option( 'RestrictCats_options', array() );
			update_option( 'RestrictCats_user_options', array() );
			
			/* Set submitted action to display success message */
			$_POST['reset'] = true;
		}
		
		/* Add menu to Settings */				   
		add_options_page( __('Restrict Categories', 'restrict-categories'), __('Restrict Categories', 'restrict-categories'), 'create_users', 'restrict-categories', array( &$this, 'admin' ) );
	}
	
	/**
	 * Builds the options settings page
	 * 
	 * @since 1.0
	 * @global $rc_options array The global options array populated by populate_opts().
	 * @global $rc_user_options array The global options array populated by populate_user_opts().
	 * @uses get_option() A safe way to get options from the options database table.
	 * @uses wp_list_categories() Displays a list of categories
	 */
	public function admin() {
		$rc_options = $this->populate_opts();
		$rc_user_options = $this->populate_user_opts();
		
		/* Display message for resetting form */
		if ( $_POST['reset'] )
			_e('<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Settings reset.</strong></p></div>', 'restrict-categories');
		
		/* Default main tab is Roles */
		$tab = 'roles';
		
		/* Set variables if the Users tab is selected */
		if ( isset( $_GET['type'] ) && $_GET['type'] == 'users' )
			$tab = 'users';
            
		/* Setup links for Roles/Users tabs */
		$roles_tab = esc_url( admin_url( 'options-general.php?page=restrict-categories' ) );
		$users_tab = add_query_arg( 'type', 'users', $roles_tab );
	?>
	
		<div class="wrap">
			<?php screen_icon( 'options-general' ); ?>
			<h2><?php _e('Restrict Categories', 'restrict-categories'); ?></h2>
            <h2 class="nav-tab-wrapper">
            	<a href="<?php echo $roles_tab; ?>" class="nav-tab <?php echo ( $tab == 'roles' ) ? 'nav-tab-active' : ''; ?>">Roles</a>
                <a href="<?php echo $users_tab; ?>" class="nav-tab <?php echo ( $tab == 'users' ) ? 'nav-tab-active' : ''; ?>">Users</a>
            </h2>
			
			<form method="post" action="options.php">
			<?php
                /* Create a new instance of our user/roles boxes class */
                $boxes = new RestrictCats_User_Role_Boxes();

                if ( $tab == 'roles' ) :
            ?>
                <fieldset>
                    <?php
                        settings_fields( 'RestrictCats_options_group' );
                        
                        /* Create boxes for Roles */
                        $boxes->start_box( get_option( 'RestrictCats_options' ), $rc_options, 'RestrictCats_options' );
                    ?> 
                </fieldset>
			<?php
				elseif ( $tab == 'users' ) :
					settings_fields( 'RestrictCats_user_options_group' );
            ?>
                <fieldset>
                    <p>Selecting categories for a user will <em>override</em> the categories you have chosen for that user's role.</p>
                    <?php
                        /* Create boxes for Users */
                        $boxes->start_box( get_option( 'RestrictCats_user_options' ), $rc_user_options, 'RestrictCats_user_options' );
                    ?> 
                </fieldset>
                <?php endif; ?>
                
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                </p>
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
	public function posts() {
		global $wp_query, $current_user;
		
		/* Get the current user in the admin */
		$user = new WP_User( $current_user->ID );
				
		/* Get the user role */
		$user_cap = $user->roles;
		
		/* Get the user login name/ID */
		if ( function_exists( 'get_users' ) )
			$user_login = $user->user_nicename;
		elseif ( function_exists( 'get_users_of_blog' ) )
			$user_login = $user->ID;
		
		/* Get selected categories for Roles */
		$settings = get_option( 'RestrictCats_options' );
		
		/* Get selected categories for Users */
		$settings_user = get_option( 'RestrictCats_user_options' );

		/* Selected categories for User overwrites Roles selection */
		if ( is_array( $settings_user ) && !empty( $settings_user[ $user_login . '_user_cats' ] ) ) {
			
			/* Build the category list */
			foreach ( $settings_user[ $user_login . '_user_cats' ] as $category ) {
				$term_id = get_term_by( 'slug', $category, 'category' )->term_id;
				
				/* If WPML is installed, return the translated ID */
				if ( function_exists( 'icl_object_id' ) )
					$term_id = icl_object_id( $term_id, 'category', true );
				
				$this->cat_list .= $term_id . ',';
			}

			$this->cat_filters( $this->cat_list );
		}
		else {
			foreach ( $user_cap as $key ) {
				/* Make sure the settings from the DB isn't empty before building the category list */
				if ( is_array( $settings ) && !empty( $settings[ $key . '_cats' ] ) ) {
					
					/* Build the category list */
					foreach ( $settings[ $key . '_cats' ] as $category ) {
						$term_id = get_term_by( 'slug', $category, 'category' )->term_id;
						
						/* If WPML is installed, return the translated ID */
						if ( function_exists( 'icl_object_id' ) )
							$term_id = icl_object_id( $term_id, 'category', true );
						
						$this->cat_list .= $term_id . ',';
					}
				}

				$this->cat_filters( $this->cat_list );
			}
		}
	}
	
	/**
	 * Adds filters for category restriction
	 * 
	 * @since 1.6
	 * @global $cat_list string The global comma-separated list of restricted categories.
	 */
	public function cat_filters( $categories ){
		/* Clean up the category list */
		$this->cat_list = rtrim( $categories, ',' );
		
		/* If there are no categories, don't do anything */
		if ( $this->cat_list !== '' ) {
			global $pagenow;
			
			/* Only restrict the posts query if we're on the Posts screen */
			if ( $pagenow == 'edit.php' || ( defined ( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) )
				add_filter( 'pre_get_posts', array( &$this, 'posts_query' ) );
			
			/* Allowed pages for term exclusions */
			$pages = array( 'edit.php', 'post-new.php', 'post.php' );
			
			/* Make sure to exclude terms from $pages array as well as the Category screen */
			if ( in_array( $pagenow, $pages ) || ( $pagenow == 'edit-tags.php' && $_GET['taxonomy'] == 'category' ) || ( defined ( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) )
				add_filter( 'list_terms_exclusions', array( &$this, 'exclusions' ) );
		}	
	}
	
	/**
	 * Remove posts from edit.php with restricted categories
	 * 
	 * @since 1.6
	 * @global $cat_list string The global comma-separated list of restricted categories.
	 * @return $query array Sets 'category__in' query_var with an array of category IDs
	 */
	public function posts_query( $query ){
		if ( $this->cat_list !== '' ) {
			/* Build an array for the categories */
			$cat_list_array = explode( ',', $this->cat_list );
			
			/* Make sure the posts are removed by default or if filter category is ran */
			if ( ! isset( $_REQUEST['cat'] ) )
				$query->set( 'category__in', $cat_list_array );
			elseif( isset( $_REQUEST['cat'] ) && $_REQUEST['cat'] == '0' )
				$query->set( 'category__in', $cat_list_array );
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
	public function exclusions(){
		$excluded = " AND ( t.term_id IN ( $this->cat_list ) OR tt.taxonomy NOT IN ( 'category' ) )";
		
		return $excluded;
	}
}

/**
 * Creates each box for users and roles.
 * 
 * @since 1.8
 */
class RestrictCats_User_Role_Boxes {
	
	public function start_box($settings, $options, $options_name){
			
			/* Create a new instance of our custom walker class */
			$walker = new RestrictCats_Walker_Category_Checklist();
			
			/* Loop through each role and build the checkboxes */
			foreach ( $options as $value ) : 
				$id = $value['id'];

				/* Get selected categories from database, if available */
				if ( is_array( $settings[ $id ] ) )
					$selected = $settings[ $id ];
				else
					$selected = array();
				
				/* Setup links for Roles/Users tabs in this class */
				$roles_tab = esc_url( admin_url( 'options-general.php?page=restrict-categories' ) );
				$users_tab = add_query_arg( $id . '-tab', 'popular', $roles_tab );
				
				/* If the Users tab is selected, setup query_arg for checkbox tabs */
				if ( isset( $_REQUEST['type'] ) && $_REQUEST['type'] == 'users' ) {
					$roles_tab = add_query_arg( array( 'type' => 'users', $id . '-tab' => 'all' ), $roles_tab );
					$users_tab = add_query_arg( array( 'type' => 'users', $id . '-tab' => 'popular' ), $roles_tab );
				}
				
				/* View All tab is default */
				$current_tab = 'all';
				
				/* Check which checkbox tab is selected */
				if ( isset( $_REQUEST[ $id . '-tab' ] ) && in_array( $_REQUEST[ $id . '-tab' ], array( 'all', 'popular' ) ) )
					$current_tab = $_REQUEST[ $id . '-tab' ];			
			?>
				<div id="side-sortables" class="metabox-holder" style="float:left; padding:5px;">
					<div class="postbox">
						<h3 class="hndle"><span><?php echo $value['name']; ?></span></h3>
						
                        <div class="inside" style="padding:0 10px;">
							<div class="taxonomydiv">
                            	<ul id="taxonomy-category-tabs" class="taxonomy-tabs add-menu-item-tabs">
                                	<li<?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>><a href="<?php echo add_query_arg( $id . '-tab', 'all', $roles_tab ); ?>" class="nav-tab-link">View All</a></li>
                                    <li<?php echo ( 'popular' == $current_tab ? ' class="tabs"' : '' ); ?>><a href="<?php echo $users_tab; ?>" class="nav-tab-link">Most Used</a></li>
                                </ul>	
								<div id="<?php echo $id; ?>-all" class="tabs-panel <?php echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' ); ?>">
									<ul class="categorychecklist form-no-clear">
									<?php
										wp_list_categories(
											array(
											'admin' => $id,
											'selected_cats' => $selected,
											'options_name' => $options_name,
											'hide_empty' => 0,
											'title_li' => '',
											'disabled' => ( 'all' == $current_tab ? false : true ),
											'walker' => $walker
											)
										);
									?>
									</ul>
								</div>
                                <div id="<?php echo $id; ?>-popular" class="tabs-panel <?php echo ( 'popular' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' ); ?>">
                                	<ul class="categorychecklist form-no-clear">
									<?php
										wp_list_categories(
											array(
											'admin' => $id,
											'selected_cats' => $selected,
											'options_name' => $options_name,
											'hide_empty' => 0,
											'title_li' => '',
											'orderby' => 'count',
											'order' => 'DESC',
											'disabled' => ( 'popular' == $current_tab ? false : true ),
											'walker' => $walker
											)
										);
									?>
									</ul>
								</div>
							</div>
							
							<p style="padding-left:10px;"><strong><?php echo count( $selected ); ?></strong> <?php echo ( count( $selected ) > 1 || count( $selected ) == 0 ) ? 'categories' : 'category'; ?> selected</p>
							
						</div>
					</div>
				</div>
			<?php 
			endforeach;	
	}
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
		$output .= "$indent<ul class='children'>\n";
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
		'<label class="selectit"><input value="' . $category->slug . '" type="checkbox" name="' . $options_name . '['. $admin .'][]" ' . checked( in_array( $category->slug, $selected_cats ), true, false ) . ( $disabled === true ? 'disabled="disabled"' : '' ) . ' /> ' . esc_html( apply_filters('the_category', $category->name ) ) . '</label>';
	}

	function end_el(&$output, $category, $depth, $args) {
		$output .= "</li>\n";
	}
}

/**
 * Delete options from the database
 * 
 * @since 1.8
 */
if ( isset ( $rc ) )
	register_uninstall_hook( __FILE__, 'RestrictCats_uninstall' );

function RestrictCats_uninstall(){
	delete_option( 'RestrictCats_options' );
	delete_option( 'RestrictCats_user_options' );
}
?>