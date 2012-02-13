<?php
/*
Plugin Name: BuddyPress reCAPTCHA
Plugin URI: http://algorhythm.de
Description: This plugin utilizes reCAPTCHA to help your blog stay clear of spam-registrations.
Version: 0.1
Author: Martin Seysen
Author URI: http://algorhythm.de
Requires at least: WP 2.8, BuddyPress 1.2.9
Revision Date: 19.08.2011
License: GPL2
*/

/* Options */
$public_key = ''; // get keys ( https://www.google.com/recaptcha/admin/create )
$private_key = '';
$theme = 'white'; // possible values: 'red', 'white', 'blackglass', 'clean' ( http://code.google.com/intl/de-DE/apis/recaptcha/docs/customization.html )
$lang = 'en'; // possibble values: 'en', 'nl', 'fr', 'de', 'pt', 'ru', 'es', 'tr' ( http://code.google.com/intl/de-DE/apis/recaptcha/docs/customization.html )
$strError = __('Please check the CAPTCHA code. It\'s not correct.', 'buddypress-recaptcha');

require_once('recaptcha-php-1.11/recaptchalib.php');

function bp_add_code() {
	global $bp, $theme, $lang, $public_key;
	
	$script = "<script type=\"text/javascript\">
 			var RecaptchaOptions = {
    			theme : '".$theme."',
				lang: '".$lang."'
 			};
		</script>";
		
	$html = '<div class="register-section" id="security-section">';
	$html .= '<div class="editfield">';
	$html .= $script;
	$html .= '<label>CAPTCHA code</label>';
	if (!empty($bp->signup->errors['recaptcha_response_field'])) {
		$html .= '<div class="error">';
		$html .= $bp->signup->errors['recaptcha_response_field'];
		$html .= '</div>';
	}
	$html .= recaptcha_get_html($public_key);
	$html .= '</div>';
	$html .= '</div>';
	echo $html;
}

function bp_validate($errors) {
	global $bp, $strError, $private_key;

	if (function_exists('recaptcha_check_answer')) {
		$response = recaptcha_check_answer($private_key, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);

		if (!$response->is_valid) {
			$bp->signup->errors['recaptcha_response_field'] = $strError;
		}
	}

	return;
}

add_action( 'bp_before_registration_submit_buttons', 'bp_add_code' );
add_action( 'bp_signup_validate', 'bp_validate' );

?>