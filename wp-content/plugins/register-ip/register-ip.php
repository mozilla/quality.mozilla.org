<?php

/*
Plugin Name: Register IP
Version: 0.1
Description: Logs the IP of the user when they register a new account. View the signup IP by clicking on the Users menu or by editing their profile.
Author: Johnny White
Author URI: http://www.johnnytwhite.com
Plugin URI: http://wphelpme.com/2010/09/register-ip/
License: GPLv2
*/

/* Version Check */
global $wp_version;

$exit_msg = 'Register IP requires WordPress 3.0 or newer.';

if(version_compare($wp_version, "3.0", "<"))
{
	exit($exit_msg);
}

function log_ip($user_id){
	//Get the IP of the person registering
	$ip = $_SERVER['REMOTE_ADDR'];

	//Add user metadata to the usermeta table
	update_user_meta($user_id, 'signup_ip', $ip);

}

// Hook into when the user is registered.
add_action('user_register', 'log_ip');

add_action('edit_user_profile', 'show_ip_on_profile');

function show_ip_on_profile() {
	$user_id = $_GET['user_id'];
?>
	<h3>Signup IP Address</h3>
	<p style="text-indent:15px;"><?php
	$ip_address = get_user_meta($user_id, 'signup_ip', true);
	echo $ip_address;
	?></p>
<?php
}

add_filter('manage_users_columns', 'signup_ip');
function signup_ip($columns) {
    $columns['signup_ip'] = 'Signup IP Address';
    return $columns;
}

add_action('manage_users_custom_column',  'my_ip_columns', 10, 3);
function my_ip_columns($value, $column_name, $user_id) {
	if ( $column_name == 'signup_ip' ) {
		$ip = get_user_meta($user_id, 'signup_ip', true);
		if($ip != ""){
			$ret = '<i>'.__($ip, 'signup_ip').'</i>';
			return $ret;
		}else{
			$ret = '<i>'.__('No IP Recorded', 'signup_ip').'</i>';
			return $ret;
		}
	}
	return $value;
}

?>