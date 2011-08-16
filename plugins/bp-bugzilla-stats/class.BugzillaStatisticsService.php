<?php
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

/**
 * Connects to a Bugzilla instance via XML-RPC and gathers statistics for users
 * based on their Wordpress email address.
 */
class BugzillaStatisticsService {
    private $curl_handle;

    /**
     * Create cURL connection to Bugzilla and configure.
     * @param string $bugzilla_url URL to Bugzilla. Must include "http://"
     *                             or "https://" prefix and trailing slash.
     * @param array $custom_options Array of custom cURL options to override
     *                              defaults
     */
    function __construct($bugzilla_url, $custom_options = array()) {
        $this->curl_handle = curl_init();
        $options = array(
            CURLOPT_URL => $bugzilla_url . '/xmlrpc.cgi',
            CURLOPT_FAILONERROR => true,
            CURLOPT_HTTPHEADER => array(
                'Content-type: text/xml;charset=UTF-8'
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true
        );

        curl_setopt_array($this->curl_handle, $options);
        curl_setopt_array($this->curl_handle, $custom_options);
    }

    function __destruct() {
        curl_close($this->curl_handle);
    }

    /**
     * Uses WebService::Bug::Search to count the number of bugs created
     * by a user.
     */
    public function get_user_bug_count($user_email) {
        $search_params = array(
            'creator' => $user_email
        );

        $search_result = $this->bugzilla_call('Bug.search', $search_params);
        return count($search_result['bugs']);
    }

    /**
     * Uses WebService::Bug::Search to count the number of bugs created
     * by a user within the last 30 days.
     */
    public function get_user_recent_bug_count($user_email) {
        $date = date('Y-m-d\TH:i:s', strtotime('-1 months'));
        $search_params = array(
            'creator' => $user_email,
            'creation_time' => $date
        );

        $search_result = $this->bugzilla_call('Bug.search', $search_params);
        return count($search_result['bugs']);
    }

    /**
     * Checks whether a user exists in Bugzilla.
     */
    public function check_user_exists($user_email) {
        $search_params = array(
            'names' => array($user_email),
        );

        $search_result = $this->bugzilla_call('User.get', $search_params);
        if (array_key_exists('faultCode', $search_result)) {
            return false;
        } else {
            return true;
        }
    }

    public function get_user_bugs_verified_count($user_email) {
        $search_params = array(
            'names' => array($user_email),
        );

        $search_result = $this->bugzilla_call('BMO.getBugsVerifier', $search_params);
        return count($search_result[$user_email]);
    }

    public function get_user_bugs_confirmed_count($user_email) {
        $search_params = array(
            'names' => array($user_email),
        );

        $search_result = $this->bugzilla_call('BMO.getBugsConfirmer', $search_params);
        return count($search_result[$user_email]);
    }

    /**
     * Calls a function in the Bugzilla XML-RPC API
     */
    private function bugzilla_call($function, $params) {
        $xmlrpc_request = xmlrpc_encode_request(
            $function,
            $params,
            array(
                'escaping' => array('markup'),
                'encoding' => 'utf-8'
            )
        );
        curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $xmlrpc_request);

        // TODO: Handle redirects explicitly
        $response = curl_exec($this->curl_handle);
        if ($response == false) {
            throw new BugzillaConnectionException("Bugzilla request failed: " .
                                                  "(" . curl_errno($this->curl_handle) . ") " .
                                                  curl_error($this->curl_handle));
        }

        return xmlrpc_decode($response);
    }
}

/**
 * Exceptions
 */
class BugzillaConnectionException extends Exception { }