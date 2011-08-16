<?php
/**
  * @package TimesToCome_Stop_Bot_Registration
  * @version 3.0
**/ 
/*
Plugin Name: TimesToCome Stop Bot Registration
Version: 3.0
Plugin URI:  http://herselfswebtools.com/2008/06/wordpress-plugin-to-prevent-bot-registrations.html
Description: Stop bots from registering as users. Many thanks to <a href="http://eric.clst.org">Eric Celeste</a> for the new admin page - you'll find it under 'Users' in the admin menu.
Author: Linda MacPhee-Cobb
Author: Eric Celeste
Author URI: http://timestocome.com 
Author URI: http://eric.clst.org
*/


	
// version 1.9 is a security fix
// ******************************************************************************************	
// version 2.0 sends rejected bots back to main site page
//         if you'd rather send them to a custom error page as before uncomment the custom error page
	//         and comment out 	// send rejections back to main site page
	//$host  = $_SERVER['HTTP_HOST'];
	//$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	//header("Location: http://$host$uri");
//	        which is in here twice starting around line 265
// *******************************************************************************************	
// 2.1 minor fixes
// 2.2 update menu options to work w/ 3.0
// 2.4 adds improved user administration page created by Eric Celeste http://eric.clst.org
// Aug 2011 3.0 improves user interface and cleans up old code, adds install/unistall functions  
	
	
    
//  *********   user comments page changes
/* Changes:
110121 (efc) fixed add_users_page for WPv3
110121 (efc) slight highlight to spam rows
110121 (efc) check boxes to facilitate deletion
110126 (efc) add order by to users_with_no_comments and change sort to desc for both
110126 (efc) change date format to make sort order easier to see
110126 (efc) reverse table order to make no comments appear at top of page
110126 (efc) add delete boxes to users who comment just for consistency
110126 (efc) change order of columns for users who comment for consistency
110126 (efc) use h3 for table titles to make them stand out a bit more
*/


/*  Copyright 2011  Linda MacPhee-Cobb timestocome@gmail.com Eric Celeste eric.clst.org

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.
     
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
     
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Linda MacPhee-Cobb
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
// log all requests to register on our blog
function ttc_add_to_log( $user, $error)
{
		
    global $wpdb;
    $registration_log_table_name = $wpdb->prefix . "ttc_user_registration_log";
    $request_time = $_SERVER['REQUEST_TIME'];
    $http_accept = $_SERVER['HTTP_ACCEPT'];
    $http_user_agent = $_SERVER['HTTP_USER_AGENT'];
    $http_remote_addr = $_SERVER['REMOTE_ADDR'];

					
    // wtf? accept statements coming in at over 255 chars?  Prevent sql errors and any funny business
    // by shortening anything from user to 200 chars if over 255 
    if ( strlen($email) > 200 ){ $email = substr ($email, 0, 200 ); }
    if ( strlen($http_accept ) > 200 ) { $http_accept = substr ( $http_accept, 0, 200 ); }
    if ( strlen($http_user_agent ) > 200 ) { $http_user_agent = substr ( $http_user_agent, 0, 200 ); }
			
    // clean input for database
    $http_accept = htmlentities($http_accept);
    $http_user_agent = htmlentities($http_user_agent);
    $http_remote_addr = htmlentities($http_remote_addr);
    $user = htmlentities($user);
		

			
    $sql = "INSERT INTO " . $registration_log_table_name . " ( ip, email, problem, accept, agent, day ) 
            VALUES ( '$http_remote_addr', '$user', '$error', '$http_accept', '$http_user_agent', NOW() )";
    $result = $wpdb->query( $sql );
}



// add an email to our email blacklist if we decide it is an bot
function ttc_add_to_blacklist( $email )
{
    global $wpdb;
    $blacklist_table_name = $wpdb->prefix . "ttc_user_registration_blacklist";
			
			
    // sanity check input
    if ( strlen($email) > 200 ){ $email = substr ($email, 0, 200 ); }				
    $email = htmlentities($email);
			
    // put the cleaned input into the database
    $sql = "INSERT INTO " . $blacklist_table_name . " ( blacklisted ) VALUES ( '$email' )";
    $result = $wpdb->query( $sql );
			
}


    
// add an ip to our ip blacklist if we decide it is a bot	
function ttc_add_to_ip_blacklist( $ip )
{
    global $wpdb;
    $ip_table_name = $wpdb->prefix . "ttc_ip_blacklist";
			
		
    // sanity check user input
    $ip = htmlentities($ip);
			
    // add cleaned input into the database
    $sql = "INSERT INTO " . $ip_table_name . " ( ip ) VALUES ( '$ip' )";
    $result = $wpdb->query( $sql );
}


    
    
//install tables if they are not already there to our wordpress db
// and use to store black listed users and log what we are doing
register_activation_hook(__FILE__, "ttc_wp_user_registration_install");
function ttc_wp_user_registration_install()
{
					
    global $wpdb;
    $blacklist_table_name = $wpdb->prefix . "ttc_user_registration_blacklist";
    $registration_log_table_name = $wpdb->prefix . "ttc_user_registration_log";
    $ip_table_name = $wpdb->prefix . "ttc_ip_blacklist";

    

    if($wpdb->get_var("SHOW TABLES LIKE '$blacklist_table_name'") != $blacklist_table_name ) {
				
        if($wpdb->get_var("SHOW TABLES LIKE '$blacklist_table_name'") != $blacklist_table_name ) {
            
            $sql = "CREATE TABLE ". $blacklist_table_name ." (
            blacklisted varchar(255) UNIQUE
            );";
                        
            require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
            dbDelta($sql);
        }

    }


    if($wpdb->get_var("SHOW TABLES LIKE '$registration_log_table_name'") != $registration_log_table_name) {
				
        $sql = "CREATE TABLE " . $registration_log_table_name . " (
					ip varchar(16),
					email varchar(255),
					problem int(3),
					accept varchar(255),
					agent varchar(255),
					day datetime
				);";

        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        dbDelta($sql);

    }



    if( $wpdb->get_var("SHOW TABLES LIKE '$ip_table_name'") != $ip_table_name ){

        $sql = "CREATE TABLE ". $ip_table_name ." (ip varchar(255) UNIQUE);";
        
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        dbDelta($sql);

    }
		
}


    
// remove tables from wp db if user deactives plugin
register_deactivation_hook( __FILE__, "ttc_wp_user_registration_uninstall");
function ttc_wp_user_registration_uninstall()
{
    global $wpdb;
 
    $blacklist_table_name = $wpdb->prefix . "ttc_user_registration_blacklist";
    $registration_log_table_name = $wpdb->prefix . "ttc_user_registration_log";
    $ip_table_name = $wpdb->prefix . "ttc_ip_blacklist";
   
    
    $wpdb->query("DROP TABLE IF EXISTS " . $blacklist_table_name);
    $wpdb->query("DROP TABLE IF EXISTS " . $registration_log_table_name);
    
    // also used by security plugin
  //  $wpdb->query("DROP TABLE IF EXISTS " . $ip_table_name);

}
    



// check out the email address and ip number of user requesting an account
function ttc_user_check()
{
	
    global $wpdb;
    $blacklisted = 0;
    $new_user = $_POST['user_email'];
		
    // check our email blacklist
    if ( $blacklisted == 0 ){
        $table = $wpdb->prefix . "ttc_user_registration_blacklist";
        $sql = "SELECT blacklisted FROM $table";
        $black_list = $wpdb->get_results( $sql );
						
        foreach ( $black_list as $blacklisted_user_email ){
            $bad_email = $blacklisted_user_email->blacklisted;
				
            // check full email
            if ( strcasecmp( $new_user, $bad_email ) == 0 ){

                $blacklisted = 1;

            // check parts of email address
            }else {
				
                $new_user_domain = explode( '@', $new_user);
                $new_user_domain = $new_user_domain[1];
	
                // check domain name
                if( strcasecmp ( $new_user_domain, $bad_email ) == 0){ 
                    $blacklisted = 2;
                }
		
                // check tld
                $new_user_endswith = strrchr( $new_user, '.' );
                if( strcasecmp ( $new_user_domain, $bad_email ) == 0){ 
                    $blacklisted = 3;
                }
            }// end if..else
            
        } // end foreach		
    }// end if blacklisted
			
			

    // check our ip blacklist
    if ( $blacklisted == 0 ){
        
        $ip_table = $wpdb->prefix . "ttc_ip_blacklist";
        $sql = "SELECT ip FROM $ip_table";
        $ip_black_list = $wpdb->get_results( $sql );
        $http_remote_addr = $_SERVER['REMOTE_ADDR'];
			
        foreach ( $ip_black_list as $blacklisted_ip ){
            $bad_ip = $blacklisted_ip->ip;				
            if ( strcasecmp( $http_remote_addr, $bad_ip ) == 0 ){
                $blacklisted = 16;
            }
        }// end for..each
    }// end if
			
			

    // check for multiple registrations from same ip address
    if ( $blacklisted == 0 ){
        
        $registration_table = $wpdb->prefix . "ttc_user_registration_log";
        $sql = "SELECT ip FROM $registration_table";
        $already_registered = $wpdb->get_results( $sql );
        
        foreach ( $already_registered as $duplicate_ip ){
			
            $dup_ip = $duplicate_ip->ip;		
            if ( strcasecmp( $http_remote_addr, $dup_ip ) == 0 ){
						$blacklisted = 17;
            }
            
        }// end for.. each
    }// end if
			
			
			
    //  if it walks like a bot....
    if ( $blacklisted == 0 ){
			
        $http_accept = $_SERVER['HTTP_ACCEPT'];
        $http_accept = trim ( $http_accept );
				
        if ( strcasecmp( $http_accept, '*/*' ) == 0 ){
            $blacklisted = 18;
        }
			
    }// end if 
			
	
    //  -----  done checking now register or bounce application ------
    // if not black listed allow registration
    if ( $blacklisted == 0 ){
				
        ttc_add_to_log( $new_user, $blacklisted );
				
        // do nothing else wp registration will finish things up
							
    }else if ( $blacklisted < 10 ){							// already blacklisted here add to log
				
        //  add to log
        ttc_add_to_log(  $new_user, $blacklisted );
				
				
        // send rejections back to main site page
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: http://$host$uri");
				
				
        // or print a custom error page if you prefer
        
        // print error page
    //    print "<html>\n";
    //    print "<head><title>Restricted email address</title></head>\n";
    //    print "<body>\n";
    //    print "<h2> The email address you entered has been banned from registering at this site </h2>\n";
    //    print "</body>\n";
    //    print "</html>\n";
    
        exit();

    }else{		// add to our blacklist and add to log
			
        // add to log
        ttc_add_to_log(  $new_user, $blacklisted );
        
        // add to our email blacklist anto our ip blacklist
        ttc_add_to_blacklist( $new_use);
        ttc_add_to_ip_blacklist( $ht_remote_addr );
			
        // send rejections back to main site page
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: http://$host$uri");
				
        // or send a custom error page if you prefer
        
        //print  error page
      //  print "<html>\n";
      //  print "<head><title>Restricted</title></head>\n";
      //  print "<body>\n";
      //  print "<h2> You have been restricted from registering at this site </h2>\n";
      //  print "</body>\n";
      //  print "</html>\n";
        
				 
				 
        exit();

    } // end if..else

}

		
		
		
		
// --------------------------------------------------------------------------------------------------------------------------------------
// user page for handling ip and email banning   -------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------------------------------------------------

	
function ttc_add_user_blacklist_menu_page()
{	
    add_options_page('Registration logs', 'Registration logs', 'edit_users', 'RegistrationLogs', 'ttc_add_user_registration_menu');
}
		
		
    
// allow user to easily edit ( add or remove ) from blacklist
// allow user to easily read what we've done and to purge log files
function ttc_add_user_registration_menu()
{
        global $wpdb;
				
        if (!current_user_can('manage_options'))  {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }

        // how many log entries do we want?
            print "<table><tr><td>";
            print "<form method=\"post\">";
            print "<strong><i>Number of log entries to view: </i></strong>";
            print "</td><td><input type=\"text\" name=\"log_lines\" maxlength=\"4\" size=\"4\">";
            print "</td><td><input type=\"submit\" value=\"Show Entries\">";
            print "<td><input type=\"hidden\" name=\"submit_check\" value=\"1\"></td>";
            print "</form>";
            print "</td></tr></table>";
				
            $log_count = 25;
				
            if ( $_POST['submit_check'] == 1 ){
                $log_count = $_POST['log_lines'];
            }
			
				
            $registration_log_table_name = $wpdb->prefix . "ttc_user_registration_log";
            $blacklist_table_name = $wpdb->prefix . "ttc_user_registration_blacklist";
            $ip_table_name = $wpdb->prefix . "ttc_ip_blacklist";

								

            // clean out logs and remove entries older than 8 days
            $sql = "DELETE FROM $registration_log_table_name WHERE day < (CURRENT_DATE - INTERVAL 8 DAY )";
            $deleted = $wpdb->get_results ( $sql );


            //fetch log information
            $sql = "SELECT ip, email, problem, accept, agent, date_format( day, '%M %d %Y %H:%i:%s') AS time_stamp FROM $registration_log_table_name ORDER BY day DESC LIMIT $log_count";
            $log = (array)$wpdb->get_results ( $sql );

            // print log files to the admin
            print "<br><strong><i>Most recent log entries</i></strong><br>";
			
            foreach ( $log as $log_entry ){
			
            $code = "";
					
            if( $log_entry->problem == 0 ){
                $code = "<font color=\"blue\">Registered: No known problems</font>";
            }else if( $log_entry->problem  == 1 ){
                $code = "<font color=\"red\"> Banned: Blacklisted email address</font>";
            }else if ( $log_entry->problem == 2 ){
                $code = "<font color=\"red\"> Banned: Blacklisted domain</font>";
            }else if ( $log_entry->problem == 3 ){
                $code = "<font color=\"red\"> Banned: Blacklisted email extension</font>";
            }else if ( $log_entry->problem == 13 ){
                $code = "<font color=\"red\"> Banned: Stop forum spam listed</font>";
            }else if ( $log_entry->problem == 14 ){
                $code = "<font color=\"red\"> Banned: Spamhaus verified spammer</font>";
            }else if ( $log_entry->problem == 15 ){
                $code = "<font color=\"red\"> Banned: Spamhaus known exploiter</font>";
            }else if ( $log_entry->problem == 16 ){
                $code = "<font color=\"red\"> Banned: Blacklisted ip address</font>";
            }else if ( $log_entry->problem == 17 ){
                $code = "<font color=\"red\"> Banned: Multiple registrations from same ip</font>";
            }else if ( $log_entry->problem == 18 ){
                $code = "<font color=\"red\"> Banned: Looks like a bot</font>";
            }
					
	
            print "<br>Email: <font color=\"darkblue\">$log_entry->email</font>";
            print "&nbsp;&nbsp;&nbsp;IP: <font color=\"olive\">$log_entry->ip</font>";
            print "<br>Accept: <font color=\"darkgreen\">$log_entry->accept</font>";
            print "<br>Agent: $log_entry->agent";
            print "<br>$code";
            print "&nbsp; &nbsp; &nbsp; <font color=\"olive\">$log_entry->time_stamp</font>";
            print "<br><hr>";
        }

        print "<br>";
        print "<table border=\"6\">";
				
        // print the email black list for editing and review to admin
        if ( isset( $_POST['ttc_blacklist_update'])){
            if( $emailblacklist = $_POST['emailblacklist'] ){
            
                $wpdb->query ( "DELETE FROM $blacklist_table_name WHERE 1=1" );
                $emailblacklist = explode( "\n", $emailblacklist );
            
                foreach ( $emailblacklist as $email ){
                    $email = trim ( $email );
                    if ( $email != "" ){
                        $sql = "INSERT INTO " . $blacklist_table_name . " ( blacklisted ) VALUES ( '$email' ) ";
                        $wpdb->query ( $sql );
                    }
                } // end for..each
                
            } // end if blacklist
        } // end if update
				
        print "<tr><td><form method=\"post\">";
        print "<table border=\"1\"><tr><tr><strong>This is your email banished list:  </strong></td>
                <tr><td>Add or remove emails as you wish, one per line </td><tr>
                <tr><td>.info<br>googlemail.com<br>muraskiken@gmail.com</td></tr>";
        print "<tr><td><textarea name='emailblacklist' cols='50' rows='21' >";
				
        $sql = "SELECT blacklisted FROM $blacklist_table_name ORDER BY blacklisted";
        $blacklisted = (array)$wpdb->get_results( $sql );
				
        foreach( $blacklisted as $emails ){
            echo  $emails->blacklisted . "\n";
        }
				
        print "</textarea></td></tr><td>";
        print "<input type=\"submit\" style=\"height:30px; width:365px;\" name=\"ttc_blacklist_update\" value=\"Update blacklist\">";
        print "</form>";
        print "</td></tr></table>";
			
        if ( isset( $_POST['ttc_blacklist_update'])){
            if( $emailblacklist = $_POST['emailblacklist'] ){
            
                $wpdb->query ( "DELETE FROM $blacklist_table_name WHERE 1=1" );
                $emailblacklist = explode( "\n", $emailblacklist );
					
                foreach ( $emailblacklist as $email ){
                    $email = trim ( $email );
                    if ( $email != "" ){
                        $sql = "INSERT INTO " . $blacklist_table_name . " ( blacklisted ) VALUES ( '$email' ) ";
                        $wpdb->query ( $sql );
                    }
                }// end for..each
            
            } // end if blacklist
        } // end if update
				

        print "</td><td>";
			
				
				
        // print the ip black list for editing and review to admin
        if( $ipblacklist = $_POST['ipblacklist'] ){
            $wpdb->query ( "DELETE FROM $ip_table_name WHERE 1=1" );
            $ipblacklist = explode( "\n", $ipblacklist );
					
            foreach ( $ipblacklist as $ip ){
                $ip = trim ( $ip );
                if( $ip != "" ){
                    $sql = "INSERT INTO " . $ip_table_name . " ( ip ) VALUES ( '$ip' ) ";
                    $wpdb->query ( $sql );
                }
            } // end for.. each
        }// end if
				
        print "<form method=\"post\">";
        print "<table border=\"1\"><tr><td><strong>This is your ip banished list:  </strong></td></tr><tr><td>Add or remove ips as you wish, one per line</th><tr><td>77.10.106.4<br>78.129.208.100<br>10.10.255.255</td></tr>";
        print "<tr><td><textarea name='ipblacklist' cols='50' rows='21' >";
				
        $sql = "SELECT ip FROM $ip_table_name ORDER BY ip";
        $blacklisted_ips = (array)$wpdb->get_results( $sql );
				
        foreach( $blacklisted_ips as $ips ){
            echo  $ips->ip . "\n";
        }
				
        print "</textarea></td></tr><td>";
        
        print "<input type=\"submit\" style=\"height:30px; width:365px;\" name=\"ttc_ip_blacklist_update\" value=\"Update IP blacklist\">";
        print "</form>";
        print "</td></tr></table>";

        print "</td></tr></table>";

}			
				
    
// WP hooks       
add_action( 'register_post', 'ttc_user_check' );                        // calls ttc_check_user when a new user registers
add_action( 'admin_menu', 'ttc_add_user_manager_pages' );               // user Hook for adding admin menus 
add_action( 'admin_menu', 'ttc_add_user_blacklist_menu_page' );         // add admin menu to user what we are doing

		
		
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Eric Celeste
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// action function for above hook
function ttc_add_user_manager_pages() {
    // Add a new submenu under Users:
    add_users_page('Comment Count', 'Comment Count', 'edit_users', 'comment_count', 'ttc_manage_users_page');
}

// mt_manage_page() displays the page content for the Test Manage submenu
function ttc_manage_users_page() {

	global $wpdb;
	$table_prefix = $wpdb->prefix;
	
	
	$users_with_comments = (array)$wpdb->get_results("select count(*) user_login, ID, comment_author, user_email, 
			date_format( user_registered, '%d %M %Y') as registration_date,  date_format( max(comment_date), '%d %M %Y' ) as last_comment_date from {$table_prefix}users, {$table_prefix}comments where user_login = comment_author group by comment_author order by user_registered desc;");

	$users_with_no_comments = (array)$wpdb->get_results("select ID, user_login, user_email, date_format( user_registered, '%d %M %Y' ) as user_registration_date from {$table_prefix}users where {$table_prefix}users.user_login not in ( select comment_author from {$table_prefix}comments ) order by user_registered desc;");
	
	print '<div class="wrap"><h2>User Comment Count</h2><br class="clear" />';

	print "<form action=".site_url('/wp-admin/users.php')." method='get' name='updateusers' id='updateusers'>";
	print '<input type="hidden" name="action" value="delete" />';
	wp_nonce_field('bulk-users');
	echo $referer;
	print '<p><input type="submit" value="Delete Checked Users" name="doaction" id="doaction" class="button-secondary action" /></p>';


	print "<h3>Users with no comments</h3>";
	print "<table class='widefat'><thead><tr><th>&nbsp;</th><th>User Name</th><th>User Email</th><th>Date Registered</th><th>Known Spammer?</th></tr></thead>";
		
	foreach ( $users_with_no_comments as $users ) {
		$user_name = $users->user_login;
		$user_email = $users->user_email;
		$date_registered = $users->user_registration_date;
		$check = file_get_contents ( "http://www.stopforumspam.com/api?email=$user_email" );	
		$test = "<appears>yes</appears>";
		
		$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
		
		if ( strpos($check, $test) > 0 ) { 
			$check = '<b>yes</b>';
			$background = ' style="background-color:#F8F2F2;"';
		} else { 
			$check = 'no';
			$background = '';
		}
		
		$checkbox = $users->ID;
		$checkbox = "<input type='checkbox' name='users[]' id='user_{$users->ID}' value='{$users->ID}' />";
		
		print "\n<tr$style$background><td>$checkbox</td><td>$user_name</td><td><a href=\"mailto:$user_email\">$user_email</a></td><td>$date_registered</td><td>$check</td></tr>";
			
	}
	
	print "</table>";

	print "<br />";
	print "<h3>Users who comment</h3>";
	print "<table class='widefat'><thead><tr><th>&nbsp;</th><th>User Name</th><th>User Email</th><th>Date Registered</th><th>Most recent comment</th><th>Comment Count</th></tr></thead>";
	foreach ( $users_with_comments as $users ){
		$number_of_posts = $users->user_login;
		$user_name = $users->comment_author;
		$user_email = $users->user_email;
		$date_registered = $users->registration_date;
		$last_comment = $users->last_comment_date;
		
		$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
		
		$checkbox = $users->ID;
		$checkbox = "<input type='checkbox' name='users[]' id='user_{$users->ID}' value='{$users->ID}' />";
			
		print "\n<tr$style><td>$checkbox</td><td>$user_name</td><td><a href=\"mailto:$user_email\">$user_email</a></td><td>$date_registered</td><td>$last_comment</td><td>$number_of_posts</td></tr>";
			
	}
	print "</table>";
	print "</div>";

	print "</form>";
	
}


    

	
	
?>