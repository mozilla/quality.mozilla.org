<?php
/*
Plugin Name: Bugzilla Statistics
Plugin URI: https://github.com/Osmose/wp-bugzilla-stats
Description: Provides functions for retrieving bugzilla user statistics
Version: 0.1
Author: Michael Kelly
Author URI: https://github.com/Osmose
License: MPL
*/
/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is the Bugzilla User Statistics Wordpress plugin.
 *
 * The Initial Developer of the Original Code is
 * Mozilla Corporation.
 * Portions created by the Initial Developer are Copyright (C) 2011
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *  Michael Kelly <mkelly@mozilla.com>
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */
require_once('class.BugzillaStatisticsService.php');

$bugzilla_stats_options = get_option('bzstats_settings');
$bugzilla_stats_service = false;
if ($bugzilla_stats_options !== false) {
    $bugzilla_stats_service = new BugzillaStatisticsService(
        $bugzilla_stats_options['bugzilla_url'], array(
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        )
    );
}

/**
 * Retrieves Bugzilla statistics for the given Wordpress user. Data is cached
 *
 * @param string $user User object returned from get_userdata
 * @return array Statistics for the given email
 *
 * @uses update_bugzilla_stats_for_user
 */
function get_bugzilla_stats_for_user($user) {
    global $bugzilla_stats_options;
    $curtime = time();

    // Default delay is 24 hours
    $delay = 60 * 60 * 24;
    if (isset($bugzilla_stats_options['delay'])) {
        $delay = $bugzilla_stats_options['delay'];
    }

    // Check the cache (if any) against the stored invalidation time
    $stats = get_user_meta($user->ID, 'bugzilla_stats', true);
    if (($stats === "") || ($stats['updated_at'] + $delay < $curtime)) {
        try {
            $stats = get_bugzilla_stats_for_email($user->user_email);
            $stats['updated_at'] = $curtime;
            update_user_meta($user->ID, 'bugzilla_stats', $stats);
        } catch (Exception $e) {
            // Pass exception up only if there's no cache
            if ($stats === "") {
                throw $e;
            }
        }
    }

    return $stats;
}

/**
 * Retrieves the latest statistics from Bugzilla for the given email.
 *
 * @param string $user_email Email address of Bugzilla user
 * @return array Statistics for the given email
 *
 * @throws BugzillaConnectionException An error has occurred connecting to Bugzilla
 * @throws BugzillaUserNotFoundException The specified email was not found in Bugzilla
 */
function get_bugzilla_stats_for_email($user_email) {
    $service = bzstats_get_service();

    if (!$service->check_user_exists($user_email)) {
        throw new BugzillaUserNotFoundException("No bugzilla user was found with email: {$user_email}", 10);
    }

    $stats = array(
        'bug_count' => $service->get_user_bug_count($user_email),
        'recent_bug_count' => $service->get_user_recent_bug_count($user_email),
        'updated_at' => time()
    );

    return $stats;
}

function bzstats_get_service() {
    global $bugzilla_stats_service;

    if ($bugzilla_stats_service === false) {
        throw new BugzillaConnectionException("No service URL configured.", 20);
    }

    return $bugzilla_stats_service;
}

/*
 * Exceptions
 */
class BugzillaUserNotFoundException extends Exception { }

/*
 * Admin Settings Page
 */

add_action('admin_menu', 'bzstats_admin_menu');
add_action('admin_init', 'bzstats_admin_init');

function bzstats_admin_menu() {
    add_options_page('Bugzilla Stats Settings', 'Bugzilla Stats',
                     'manage_options', 'bugzilla-stats-settings',
                     'bzstats_settings');
}

function bzstats_admin_init() {
    register_setting('bzstats_settings', 'bzstats_settings',
                     'bzstats_settings_validate');

    add_settings_section('bzstats_main', 'Main Settings', 'bzstats_section_text', 'bzstats');
    add_settings_field('bzstats_url', 'Bugzilla URL',
                       'bzstats_url_input', 'bzstats', 'bzstats_main');
    add_settings_field('bzstats_delay', 'Update Interval (seconds)',
                       'bzstats_delay_input', 'bzstats', 'bzstats_main');
}

function bzstats_settings_validate($input) {
    $newinput = array();
    $newinput['bugzilla_url'] = esc_url_raw($input['bugzilla_url'], array('http', 'https'));
    $newinput['delay'] = (int) $input['delay'];

    return $newinput;
}

function bzstats_url_input() {
    $settings = get_option('bzstats_settings');
?>
<input id="bzstats_url" name="bzstats_settings[bugzilla_url]" size="40" type="text" value="<?php echo $settings['bugzilla_url']; ?>" />
<span class="description">E.g. https://bugzilla.mozilla.org</span>
<?php
}

function bzstats_delay_input() {
    $settings = get_option('bzstats_settings');
?>
<input id="bzstats_delay" name="bzstats_settings[delay]" size="8" type="text" value="<?php echo $settings['delay']; ?>" />
<?php
}

function bzstats_section_text() {
    echo '<p>Bugzilla Stats connection settings.</p>';
}

function bzstats_settings() {
    if (!current_user_can('manage_options'))  {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
?>
<div class="wrap">
  <h2>Bugzilla Stats Configuration</h2>
  <form action="options.php" method="post">
    <?php settings_fields('bzstats_settings'); ?>
    <?php do_settings_sections('bzstats'); ?>

    <p class="submit">
      <input name="submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </p>
  </form>
</div>
<?php
}