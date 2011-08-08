<?php
/*
Plugin Name: User Spam Remover
Plugin URI: http://lyncd.com/user-spam-remover/
Description: Automatically removes spam user registrations and other old, never-used user accounts. Blocks annoying e-mail to administrator after every new registration. Full logging and backup of deleted data. Requires PHP5. After activating, go to <a href="users.php?page=user_spam_remover">settings page</a> to enable.
Version: 0.9.1
Author: Joel Hardi
Author URI: http://lyncd.com/
License: GPL2
*/

/*  Copyright 2010-2011 Joel Hardi

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

/* class UserSpamRemover
 *
 * For now, implement as a giant singleton class that acts as registry
 *
 */

class UserSpamRemover {
  // User-configurable plugin settings
  protected $config = array();
  protected static $defaults = array(
    'enabled' => FALSE,
    'noAdminEmails' => TRUE,
    'daysGrace' => 10,
    'userWhitelist' => '',
    'logDir' => NULL,
    'activityLog' => TRUE,
    'restoreLog' => TRUE,
    'logFilename' => 'userspamremover.log',
    'restoreFilename' => 'userspamremover.restore.sql',
  );
  // callback methods to sanitize form input
  protected static $sanitizeCallbacks = array(
    'enabled' => 'sanitizeBool',
    'noAdminEmails' => 'sanitizeBool',
    'daysGrace' => 'sanitizePosInt',
    'userWhitelist' => 'sanitizeWhitelist',
    'logDir' => 'sanitizeTrim',
    'activityLog' => 'sanitizeBool',
    'restoreLog' => 'sanitizeBool',
    'logFilename' => 'sanitizeTrim',
    'restoreFilename' => 'sanitizeTrim',
  );

  // URL-safe plugin name used as:
  //  * settings page URL 
  //  * name of WordPress hook added for scheduled removal
  //  * pseudo namespace for various WordPress functions like nonce generation
  public static $pluginURLName = 'user_spam_remover';

  // Option group and prefix (i.e. pseudo namespace) for settings saved 
  // thru WP Options mechanism
  protected static $wpOptGroup = 'USRemover';

  // Whether to enable debug mode with additional logging to activity log
  protected static $debug = FALSE;

  // canonical timestamp for a deletion action (so that logs are consistent)
  protected $timestamp;
  protected $timeFormat = DATE_ISO8601;
  protected $timezone; // standard tz string such as "America/Los_Angeles"

  // whether manual remove has been requested (regardless of $config['enabled'])
  protected $manualRun = FALSE;

  // SQL query fragment cached during runtime since mysql_real_escape_string
  // is expensive. See getUserWhitelistSQL() for details
  protected $whitelistSQL;

  // wpdb object
  protected $wpdb;

  private static $instance;

  // Constructor runs once only, since private and called only by getInstance()
  private function __construct(wpdb $wpdb) {
    self::$defaults['logDir'] = dirname(__FILE__).'/log';

    $this->wpdb = $wpdb;

    // initialize config vars from WordPress options table
    $this->timezone = get_option('timezone_string');
    foreach (self::$defaults as $key => $default) {
      $this->config[$key] = get_option(self::$wpOptGroup.$key, $default);
    }
  }

  // Getter to be used for all access to config options in $this->config
  public function getOption($optName) {
    return $this->config[$optName];
  }

  // Returns singleton object instance of UserSpamRemover
  public static function getInstance() {
    if (empty(self::$instance)) {
      if (array_key_exists('wpdb', $GLOBALS))
        self::$instance = new self($GLOBALS['wpdb']);
      else
        self::$instance = new self(new wpdb);
    }
    return self::$instance;
  }

  // Static function to execute remove regardless of 'enabled' setting
  public static function manualRemove() {
    $usr = self::getInstance();
    $usr->manualRun = TRUE;
    return $usr->remove(1000);
  }

  // Static function for WordPress to use to execute remove
  public static function scheduledRemove() {
    $usr = self::getInstance();
    $usr->logDebug('scheduledRemove() called');
    return $usr->remove(1000);
  }

  // Add to WordPress schedule, runs on plugin activation only
  public static function activate() {
    $usr = self::getInstance();
    if (self::$debug) {
      $usr->logDebug('activate() called');
      // (can't log result of wp_schedule_event(), it doesn't return anything)
      wp_schedule_event(time() + 1800, 'hourly', self::$pluginURLName);
    } else {
      wp_schedule_event(time() + 1800, 'daily', self::$pluginURLName);
    }
    $usr->checkMySQL();
  }

  // Remove from WordPress schedule, runs on plugin deactivation
  public static function deactivate() {
    if (self::$debug) {
      $usr = self::getInstance();
      $usr->logDebug('deactivate() called');
      // (can't log result of wp_clear_scheduled_hook(), it doesn't return)
    }
    wp_clear_scheduled_hook(self::$pluginURLName);
  }

  // Register WordPress options page
  public static function adminMenu() {
    // It's OK to use $pluginURLName here instead of a real filename because a 
    // callback is specified. See explanation of "function" parameter under
    // http://codex.wordpress.org/Adding_Administration_Menus#Using_add_submenu_page
    add_submenu_page('users.php', 'User Spam Remover settings', 
      'User Spam Remover', 'remove_users', self::$pluginURLName, 
      array(get_class(), 'adminOptions'));
    add_action('admin_init', array(get_class(), 'adminRegisterSettings'));
  }

  // Register WordPress options
  public static function adminRegisterSettings() {
    foreach (self::$defaults as $key => $default) {
      register_setting(self::$wpOptGroup, self::$wpOptGroup.$key, 
        array(get_class(), self::$sanitizeCallbacks[$key]));
    }
  }

  // WordPress options page
  public static function adminOptions() {
    if (!current_user_can('manage_options') or 
        !current_user_can('remove_users'))
      wp_die(__('You do not have sufficient permissions to access this page.'));
    $usr = self::getInstance();
    $usr->optionsPage();
  }

  // Saves record of new user's registration to activity log
  // i.e., replaces annoying e-mails that are normally sent to administrator
  // public method because called from our modified version of 
  // wp_new_user_notification() (see end of this file)
  public function logNewUser(WP_User $user) {
    $id = $user->ID;
    $login = stripslashes($user->user_login);
    $blog = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    // (could also log things like email, password and name but leaving them
    // out for privacy reasons, especially since this log could be saved to 
    // publicly accessible location.)
    return $this->logAction("New user added with ID: $id, login: $login on blog: $blog");
  }

  // Logs $str to activity log if debugging and logging are enabled
  // (and log file is writable)
  public function logDebug($str) {
    if (self::$debug)
      return $this->logAction('debug: '.$str);
    else
      return FALSE;
  }

  // Print options page
  protected function optionsPage() {
    echo "<div class=\"wrap\">\n";
    screen_icon();
    echo "<h2>User Spam Remover</h2>\n";
    settings_errors();
    $removeNowBool = 'remove_users_now';
    $nonceRemoveUsersNow = self::$pluginURLName . $removeNowBool;

    // Execute "Remove spam/unused accounts now" button if pushed
    if (array_key_exists($removeNowBool, $_POST) and 
        $_POST[$removeNowBool] and 
        check_admin_referer($nonceRemoveUsersNow)) {
      try {
        $result = self::manualRemove();
        if ($result) {
          echo '<div class="updated" style="margin-bottom: 0;"><p><strong>'.$result.'</strong></p></div>';
        } else {
          // Unclear whether this edge branch will arise. Any raised exception
          // in manualRemove() would either filter up or be caught below
          self::errorMsg('There was a problem removing using accounts. Check the most recent line in the log below for details.');
        }
      } catch (UserSpamRemoverException $e) {
        self::errorMsg('User accounts could not be removed. '.$e->getMessage());
      }
    }

    // Print "Remove spam/unused accounts now" button if settings consistent
    try {
      $this->checkEnabled(TRUE);
?>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" style="float: right; text-align: right; width: 22em; overflow: hidden;"><input type="hidden" name="<?php echo $removeNowBool; ?>" value="1" />
    <p class="submit" style="margin: 0.8em 0 0; padding: 0;">
      <input type="submit" class="button-primary" value="Remove spam/unused accounts now" />
    </p><?php wp_nonce_field($nonceRemoveUsersNow); ?>
  </form>
<?php
    } catch (UserSpamRemoverException $e) {
      // if checkEnabled raises Exception, bury it and just don't show button
      echo "\n";
    }

    // Print last X lines in activity log if they exist
    $linesToPrint = 10;
    $pathname = realpath($this->getOption('logDir').'/'.
                         $this->getOption('logFilename'));
    if (is_readable($pathname)) {
      // emulate "tail -X" of log file
      $fh = @fopen($pathname, "r");
      if ($fh) {
        $lines = array();
        while (!feof($fh)) {
          $lines[] = fgets($fh, 4096);
          if (count($lines) > ($linesToPrint + 1))
            array_shift($lines);
        }
        fclose($fh);

        if (count($lines) > 0) {
          // Be sure to protect against XSS and injection attacks on logfile
          // Here, using restrictive character whitelist, better than escaping
          if (preg_match('#[\s\w=\-\.\+\:\'\\\(\),;/"\#]+$#', 
                         trim(implode('', $lines)), $matches)) {
            echo "<h4>Latest $linesToPrint lines in activity log</h4>\n";
            echo '<pre style="background: white; overflow: auto; max-height: 5em; padding: 0.5em;">';
            echo esc_html($matches[0])."</pre>\n";
          }
        }
      }
    }

    // Preview users pending deletion
    $maxShow = 100;
    $days = self::sanitizePosInt($this->getOption('daysGrace'));
    if ($this->getOption('enabled')) {
      if ($days > 0)
        $days = $days - 1;
      echo "<h4>Unused accounts pending deletion</h4>\n";
      echo "<p>These unused user accounts are within 24 hours of the age threshold you've set below and will be automatically deleted in the next 48 hours.</p>\n";
    } else {
      echo "<h4>Unused accounts over the age threshold</h4>\n";
      echo "<p>These unused user accounts are older than the age threshold you've set below. To remove them, either enable automatic deletion or click the \"Remove spam/unused accounts now\" button above.</p>\n";
    }
    try {
      $users = $this->getIDList($days, 10000, TRUE);
    } catch (UserSpamRemoverException $e) {
      self::errorMsg($e->getMessage());
    }
    if (isset($users)) {
      echo '<p style="background: white; overflow: auto; max-height: 5em; padding: 0.5em;">';
      if (count($users) > 0) {
        $shown = 0;
        foreach ($users as $id => $login) {
          // for $edit_link, see WP_Users_List_Table::single_row() in 
          // wp-admin/includes/class-wp-users-list-table.php
          $edit_link = esc_url(
            add_query_arg('wp_http_referer', 
                          urlencode(stripslashes($_SERVER['REQUEST_URI'])), 
                          "user-edit.php?user_id=$id"));
          echo '<a href="'.$edit_link.'">'.$login.'</a> ';
          $shown++;
          if ($shown == $maxShow) {
            $leftover = count($users) - $shown;
            if ($leftover > 0)
              echo " and $leftover more.";
            break;
          }
        }
      } else {
        echo "no matching accounts found";
      }
      echo "</p>\n";
    }
?>
  <h3>Settings</h3>
  <form method="post" action="options.php">
    <?php settings_fields(self::$wpOptGroup); ?>
    <h4>Automatic user deletion</h4>
<?php
  try {
    $this->checkEnabled();
  } catch (UserSpamRemoverException $e) {
    self::errorMsg(str_replace('was not', 'will not be', $e->getMessage()). ' See below for details.');
  } 
?>
    <p>Set <strong>User Spam Remover</strong> to automatically delete all unused user accounts (those users who have never commented, posted or added a link) older than the age threshold. The main target is user registration spam, but <strong>all</strong> orphaned, never-used accounts are included. Optionally, you can whitelist specific usernames to protect them from deletion (i.e., your boss' account that he has never used), but we recommend <strong>not</strong> using this feature because dormant, neglected accounts are often those used in backdoor attacks.</p>
    <table class="form-table">
      <tr valign="top"><?php $o = self::$wpOptGroup.'enabled'; ?>
        <th scope="row"><label for="<?php echo $o; ?>">Enable</label></th>
        <td>
          <input type="checkbox" id="<?php echo $o; ?>" name="<?php echo $o; ?>" value="1" <?php if ($this->getOption('enabled')) { echo 'checked="checked" '; } ?>/>
          <span class="description">Check to enable automatic removal of never-used user accounts</span>
        </td>
      </tr>
      <tr valign="top"><?php $o = self::$wpOptGroup.'daysGrace'; ?>
        <th scope="row"><label for="<?php echo $o; ?>">Age threshold (in days)</label></th>
        <td>
          <input type="text" id="<?php echo $o; ?>" name="<?php echo $o; ?>" value="<?php echo (int) $this->getOption('daysGrace'); ?>" size="3" />
          <span class="description">Only unused accounts older than this are removed (gives new users a chance to post!)</span>
        </td>
      </tr>
      <tr valign="top"><?php $o = self::$wpOptGroup.'userWhitelist'; ?>
        <th scope="row"><label for="<?php echo $o; ?>">User whitelist</label></th>
        <td>
          <input type="text" id="<?php echo $o; ?>" name="<?php echo $o; ?>" value="<?php echo esc_attr($this->getOption('userWhitelist')); ?>" size="75" /><br />
          <span class="description">Comma-separated list of usernames to protect from deletion</span>
        </td>
      </tr>
    </table>

    <h4>Suppress 'new user' e-mail to administrator</h4>
    <p>Stops that annoying e-mail that is sent out to the administrator account every time a new user registers. (If logging is enabled below, new registrations are still logged.)</p>
    <table class="form-table">
      <tr valign="top"><?php $o = self::$wpOptGroup.'noAdminEmails'; ?>
        <th scope="row"><label for="<?php echo $o; ?>">Enable</label></th>
        <td>
          <input type="checkbox" id="<?php echo $o; ?>" name="<?php echo $o; ?>" value="1" <?php if ($this->getOption('noAdminEmails')) { echo 'checked="checked" '; } ?>/>
          <span class="description">Check to block new user e-mail notifications to administrator</span>
        </td>
      </tr>
    </table>

    <h4>Logging and user backups</h4>
    <p>Records all <strong>User Spam Remover</strong> actions to an activity log file, so that you can see if the plugin is doing anything. Also, backs up all deleted user accounts and metadata to a restore log in SQL format, in case you ever need to restore a deleted account.</p>
    <p><strong>Note:</strong> The default log directory is the "log" subdirectory of this plugin, but we <strong>strongly recommend</strong> that you change it to a location that isn't visible over the web (for example, one directory up from your website root). No passwords or e-mail addresses are exposed in the activity log, but e-mail addresses of deleted users are visible in the restore log. Wherever you put the log directory, be sure to make it webserver-writable!</p>
    <table class="form-table">
      <tr valign="top"><?php $o = self::$wpOptGroup.'logDir'; ?>
        <th scope="row"><label for="<?php echo $o; ?>">Log directory</label></th>
        <td>
          <input type="text" id="<?php echo $o; ?>" name="<?php echo $o; ?>" value="<?php echo esc_attr($this->getOption('logDir')); ?>" size="75" /><br />
          <span class="description">Filesystem directory where logs are saved (do not use trailing slash)</span>
<?php
  try {
    $dirOK = TRUE;
    if ($this->getOption('activityLog') or $this->getOption('restoreLog'))
      $this->checkLogDir();
  } catch (UserSpamRemoverException $e) {
    $dirOK = FALSE;
    self::errorMsg($e->getMessage());
  } 
?>
        </td>
      </tr>
      <tr valign="top"><?php $o = self::$wpOptGroup.'activityLog'; ?>
        <th scope="row"><label for="<?php echo $o; ?>">Activity log</label></th>
        <td>
          <input type="checkbox" id="<?php echo $o; ?>" name="<?php echo $o; ?>" value="1" <?php if ($this->getOption('activityLog')) { echo 'checked="checked" '; } ?>/>
          <span class="description">Check to enable activity log</span>
        </td>
      </tr>
      <tr valign="top"><?php $o = self::$wpOptGroup.'logFilename'; ?>
        <th scope="row"><label for="<?php echo $o; ?>">Activity log filename</label></th>
        <td>
          <input type="text" id="<?php echo $o; ?>" name="<?php echo $o; ?>" value="<?php echo esc_attr($this->getOption('logFilename')); ?>" size="35" />
<?php
  try {
    if ($dirOK and $this->getOption('activityLog'))
      $this->checkFilePathname($this->getOption('logFilename'));
  } catch (UserSpamRemoverException $e) {
    self::errorMsg($e->getMessage());
  } 
?>
        </td>
      </tr>
      <tr valign="top"><?php $o = self::$wpOptGroup.'restoreLog'; ?>
        <th scope="row"><label for="<?php echo $o; ?>">User backup log</label></th>
        <td>
          <input type="checkbox" id="<?php echo $o; ?>" name="<?php echo $o; ?>" value="1" <?php if ($this->getOption('restoreLog')) { echo 'checked="checked" '; } ?>/>
          <span class="description">Check to enable user backup log</span>
        </td>
      </tr>
      <tr valign="top"><?php $o = self::$wpOptGroup.'restoreFilename'; ?>
        <th scope="row"><label for="<?php echo $o; ?>">Backup log filename</label></th>
        <td>
          <input type="text" id="<?php echo $o; ?>" name="<?php echo $o; ?>" value="<?php echo esc_attr($this->getOption('restoreFilename')); ?>" size="35" />
<?php
  try {
    if ($dirOK and $this->getOption('restoreLog'))
      $this->checkFilePathname($this->getOption('restoreFilename'));
  } catch (UserSpamRemoverException $e) {
    self::errorMsg($e->getMessage());
  } 
?>
        </td>
      </tr>
    </table>

    <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
  </form>
</div>
<?php
  }

  // Remove either all old empty user accounts up to (int) $limit accounts
  protected function remove($limit = FALSE) {
    $pre = $this->wpdb->prefix;
    $db = $this->wpdb->dbh;
    $days = self::sanitizePosInt($this->getOption('daysGrace'));
    $daysPlural = ($days == 1) ? '' : 's';
    $ids = array();
    if ($limit !== FALSE)
      $limit = (int) $limit;

    try {
      // Check pruning is enabled and log files are writable if they're enabled
      if (!$this->checkEnabled($this->manualRun))
        return FALSE;
      // Fetch list of IDs to remove
      $ids = $this->getIDList($days, $limit);
    } catch (UserSpamRemoverException $e) {
      $this->logAction($e->getMessage());
      if ($this->manualRun)
        throw $e; // rethrow so error will show onscreen
      return FALSE;
    }

    // Remove identified unused user records. Cancel remove and roll back 
    // database if there's a problem writing to restore log
    $idCnt = count($ids);
    if ($idCnt > 0) {
      // Format list of users to delete
      $idList = implode(', ', $ids);
      $this->timestamp = time();
      $error = FALSE;

      // Begin SQL transation
      mysql_query("START TRANSACTION", $db);

      // Back up users about to be deleted
      if (!$this->saveBackupData($pre.'users', 
            "SELECT * FROM ${pre}users WHERE ID IN (${idList});") or 
          !$this->saveBackupData($pre.'usermeta', 
            "SELECT * FROM ${pre}usermeta WHERE user_id IN (${idList});",
            TRUE)) {
        $error = "Problem backing up old users. User removal aborted.";
      }
      // Remove users
      if (!$error) {
        if (!mysql_query(
            "DELETE FROM ${pre}users WHERE ID IN (${idList});", $db)) {
          $error = "Problem deleting from the ${pre}users table. ".
                   "User removal aborted.";
        }
      }
      // Remove usermeta
      if (!$error) {
        if (!mysql_query(
            "DELETE FROM ${pre}usermeta WHERE user_id IN (${idList});", $db)) {
          $error = "Problem deleting from the ${pre}usermeta table. ".
                   "User removal aborted.";
        }
      }

      // If errors, roll back transaction. Otherwise, commit and log action
      if ($error) {
        mysql_query("ROLLBACK", $db);
        $this->logAction($error);
        return FALSE;
      } else {
        mysql_query("COMMIT", $db);
        $idPlural = ($idCnt == 1) ? '' : 's';
        $result = "Removed $idCnt unused user account$idPlural older than $days day$daysPlural.";
        $this->logAction($result);
        return $result;
      }
    }
    return "No unused user accounts older than $days day$daysPlural were found to delete.";
  }

  // Returns array of user IDs to delete that are older than $daysGrace 
  // with a max of $limit records
  //  * if $returnNames is TRUE, returns assoc array(ID => user_login)
  protected function getIDList($daysGrace, $limit = FALSE, $returnNames = FALSE) {
    $this->checkMySQL();
    $db = $this->wpdb->dbh;
    $ids = array();

    // Identify unused user records older than $daysGrace and populate $ids
    $sql = $this->getListSQL($daysGrace, $limit, $returnNames);
    $result = mysql_query($sql, $db);
    if ($result) {
      if (mysql_num_rows($result) > 0) {
        if ($returnNames) {
          while ($row = mysql_fetch_row($result))
            $ids[$row[0]] = $row[1];
        } else {
          while ($row = mysql_fetch_row($result))
            $ids[] = $row[0];
        }
        $sql = $this->getPostedListSQL($daysGrace, $limit);
        $result = mysql_query($sql, $db);
        if ($result and mysql_num_rows($result) > 0) {
          if ($returnNames) {
            while ($row = mysql_fetch_row($result))
              unset($ids[$row[0]]);
          } else {
            while ($row = mysql_fetch_row($result)) {
              $key = array_search($row[0], $ids, TRUE);
              if ($key !== FALSE)
                unset($ids[$key]);
            }
          }
        }
      }
      mysql_free_result($result);
    } else {
      throw new UserSpamRemoverException(
        "Could not retrieve user list. ".mysql_error());
    }

    return $ids;
  }

  // Returns SQL SELECT statement to fetch user IDs older than $daysGrace
  //  * if $returnNames is TRUE, query SELECTs ID, user_login
  protected function getListSQL($daysGrace, $limit = FALSE, $returnNames = FALSE) {
    $pre = $this->wpdb->prefix;
    $select = 'u.ID';
    if ($returnNames)
      $select .= ', u.user_login';
    if ($limit)
      $limit = " LIMIT $limit";
    else
      $limit = '';
    $sql = "SELECT $select FROM ${pre}users AS u ".
           "LEFT OUTER JOIN ${pre}comments AS c ON u.ID = c.user_id ".
           "LEFT OUTER JOIN ${pre}posts AS p ON u.ID = p.post_author ".
           "LEFT OUTER JOIN ${pre}links AS l ON u.ID = l.link_owner ".
           "WHERE c.user_id IS NULL ".
           "AND p.post_author IS NULL AND l.link_owner IS NULL ".
           $this->getUserWhitelistSQL()." ".
           "AND u.user_registered < DATE_ADD(NOW(), INTERVAL -$daysGrace DAY) ".
           "GROUP BY u.ID${limit};";
    return $sql;
  }

  // Returns SQL SELECT statement to fetch user IDs older than $daysGrace
  // that should be *protected* because they have a usermeta record where
  // meta_key = 'last_posted'.
  // Database-integrated bbPress installations set/update this value when a
  // user posts, so we will protect database-integrated bbPress users
  // from deletion if they have written a post.
  protected function getPostedListSQL($daysGrace, $limit) {
    $pre = $this->wpdb->prefix;
    $sql = $this->getListSQL($daysGrace, $limit);
    $sql = str_replace('WHERE ', 
      "LEFT OUTER JOIN ${pre}usermeta AS m ON u.ID = m.user_id WHERE ".
      "m.meta_key = 'last_posted' AND ", 
      $sql);
    return $sql;
  }

  // If user has set userWhitelist option, returns mysql_real_escaped SQL
  // query fragment for insertion into WHERE clause of a query.
  // Otherwise, returns empty string. Caches result in $this->whitelistSQL
  protected function getUserWhitelistSQL() {
    if (!isset($this->whitelistSQL)) {
      // Format escaped SQL for username whitelist if whitelist is non-empty
      $sql = '';
      if ($whitelist = $this->getOption('userWhitelist')) {
        $ns = preg_split("#[\s,]+#", trim($whitelist));
        $us = array();
        foreach ($ns as $n) {
          if (strlen($n) > 0)
            $us[] = "'".mysql_real_escape_string($n)."'";
        }
        if (count($us) > 0)
          $sql = 'AND u.user_login NOT IN ('.implode(', ', $us).')';
      }
      $this->whitelistSQL = $sql;
    }
    return $this->whitelistSQL;
  }

  // Given MySQL $tableName and $selectSQL for SELECTing some rows, returns
  // mysqldump-style SQL to INSERT data. Intended to be used with "SELECT *"
  // on a single table -- if column names are omitted, obviously the backup
  // will be incomplete.
  //
  // If $trueIfEmpty is TRUE, then will return TRUE if $selectSQL returns no
  // rows. Otherwise, returns FALSE in this case.
  protected function saveBackupData($tableName, $selectSQL, $trueIfEmpty = FALSE) {
    $fh = @fopen($this->getOption('logDir').'/'.
                 $this->getOption('restoreFilename'), 'a');
    if (!$fh)
      return FALSE;

    $db = $this->wpdb->dbh;
    $result = mysql_query($selectSQL, $db);
    if ($result && mysql_num_rows($result) > 0) {
      fwrite($fh, "-- Begin backup of deleted records from $tableName on ".
                   $this->date($this->timeFormat)." --\n");

      // Get column names and types
      $i = 0;
      while ($i < mysql_num_fields($result)) {
        $field = mysql_fetch_field($result, $i);
        $fields[] = $field;
        $names[] = "`".$field->name."`";
        $i++;
      }
      $colNames = implode(', ', $names);

      // Loop through results and produce separate INSERT statement per row
      // (least efficient from perspective of re-inserting lots of data, but
      // best for ability to identify and re-insert individual records.)
      while ($row = mysql_fetch_row($result)) {
        $vals = array();
        foreach ($fields as $col => $field) {
          if (is_null($row[$col]))
            $vals[] = 'NULL';
          elseif ($field->type == 'int')
            $vals[] = $row[$col];
          else
            $vals[] = "'".mysql_real_escape_string($row[$col])."'";
        }
        fwrite($fh, "INSERT INTO `$tableName` ($colNames) VALUES (".
                     implode(', ', $vals).");\n");
      }

      fwrite($fh, "-- End backup of $tableName --\n\n");
      fclose($fh);
      mysql_free_result($result);

    } else {
      // if SQL query returned empty set, return val depends on $trueIfEmpty
      if ($result && mysql_num_rows($result) == 0 and $trueIfEmpty)
        return TRUE;
      else
        return FALSE;
    }
    return TRUE;
  }

  // Prepend date and save $str (should be a single line only) to activity log.
  // Returns TRUE on success or FALSE on failure (i.e. log file not writable)
  protected function logAction($str) {
    $fh = @fopen($this->getOption('logDir').'/'.
                 $this->getOption('logFilename'), 'a');
    if (!$fh)
      return FALSE;

    $str = $this->date($this->timeFormat).' '.$str;

    $numbytes = fwrite($fh, $str."\n");
    fclose($fh);

    return (bool) $numbytes;
  }

  /*
   * Helper methods
   */

  // lcfirst() equivalent. lcfirst only in PHP 5.3+
  protected static function lcfirst($str) {
    $str[0] = strtolower($str[0]);
    return $str;
  }

  // Prints WordPress <div class="error"><p> style msg inline
  protected static function errorMsg($str) {
    echo '<div class="error inline"><p><strong>'.$str."</strong></p></div>\n";
  }

  // WordPress tz-aware replacement for PHP date(). 
  // Also uses $this->timestamp if set.
  protected function date($format, $timestamp = NULL) {
    if (is_null($timestamp)) {
      if ($this->timestamp)
        $timestamp = $this->timestamp;
      else
        $timestamp = time();
    }

    if ($this->timezone) {
      $cur = date_default_timezone_get();
      date_default_timezone_set($this->timezone);
      $return = date($format, $timestamp);
      date_default_timezone_set($cur);
      return $return;
    } else {
      return date($format, $timestamp);
    }
  }

  /*
   * Validation and sanitization methods
   */

  // Determines whether user pruning is enabled and, when requested, 
  // log files are writable
  protected function checkEnabled($ignoreEnabledSetting = FALSE) {
    if (!$this->getOption('enabled') and !$ignoreEnabledSetting)
      return FALSE;

    try {
      if ($this->getOption('activityLog'))
        $this->checkFilePathname($this->getOption('logFilename'));
      if ($this->getOption('restoreLog'))
        $this->checkFilePathname($this->getOption('restoreFilename'));
    } catch (UserSpamRemoverException $e) {
      throw new UserSpamRemoverException("User removal was not performed because logging is requested, but ".self::lcfirst($e->getMessage()));
    }

    return TRUE;
  }

  // Tests whether log dir exists and is writable
  protected function checkLogDir($filename = NULL) {
    if (is_null($filename))
      $filename = $this->getOption('logDir');
    $filename = realpath($filename);
    if (!$filename or !file_exists($filename))
      throw new UserSpamRemoverException("The log directory $filename does not exist. You must create it on the server.");
    if (!is_dir($filename))
      throw new UserSpamRemoverException("The log directory $filename is a regular file, not a directory.");
    if (!is_writable($filename))
      throw new UserSpamRemoverException("The log directory $filename is not writable by the webserver.");
    return TRUE;
  }

  // Tests whether file exists or can be created, and is writable.
  // returns full file pathname or FALSE
  protected function checkFilePathname($filename) {
    $this->checkLogDir($this->getOption('logDir'));
    $pathname = realpath($this->getOption('logDir').'/'.$filename);
    if (file_exists($pathname)) {
      if (!is_file($pathname))
        throw new UserSpamRemoverException("The log file $filename is not a regular file.");
      if (!is_writable($pathname))
        throw new UserSpamRemoverException("The log file $filename is not writable by the webserver.");
    }
    return $pathname;
  }

  // Checks MySQL execution environment and modifies if necessary
  // To function efficiently, User Spam Remover needs indexes to be on these
  // two table columns:
  //   * wp_comments.user_id
  //   * wp_links.link_owner
  // These indexes are created if they do not exist. SQL_BIG_SELECTS is also 
  // set to TRUE.
  protected function checkMySQL() {
    $this->logDebug('checkMySQL() called');
    $db = $this->wpdb->dbh;
    mysql_query('SET SQL_BIG_SELECTS = 1;', $db);
    $this->checkMySQLIndex('comments', 'user_id');
    $this->checkMySQLIndex('links', 'link_owner');
  }

  // Verifies that there is a regular index on $column of $wptable.
  // Adds the index if it does not exist.
  protected function checkMySQLIndex($wptable, $column) {
    $table = $this->wpdb->prefix . $wptable;
    $db = $this->wpdb->dbh;
    if ($result = mysql_query("SHOW INDEX FROM $table;")) {
      $found = FALSE;
      while ($row = mysql_fetch_assoc($result)) {
        if ($row['Column_name'] == $column) {
          $found = TRUE;
          break;
        }
      }
      if (!$found) {
        if (!mysql_query("ALTER TABLE $table ADD INDEX($column);")) {
          throw new UserSpamRemoverException(
            "Could not ADD INDEX $column on table $table. ".mysql_error());
        }
      }
    } else {
      throw new UserSpamRemoverException(
        "Could not SHOW INDEX on database table $table. ".mysql_error());
    }
  }

  /*
   * Form input sanitization callbacks
   * (must be public since registered thru register_setting()
   */

  public static function sanitizeBool($v) {
    settype($v, 'bool');
    return $v;
  }

  public static function sanitizePosInt($v) {
    if ($v < 0)
      return 0;
    return intval($v);
  }

  public static function sanitizeWhitelist($v) {
    $ns = preg_split("#[\s,'\";/\\\<\>%&\]\[\(\)]+#", trim($v));
    $us = array();
    foreach ($ns as $n) {
      if (strlen($n) > 0)
        $us[] = sanitize_user($n);
    }
    return implode(', ', $us);
  }

  public static function sanitizeTrim($v) {
    return trim($v);
  }

}

// Exception class for plugin errors
class UserSpamRemoverException extends Exception { }

// Override wp_new_user_notification() to cancel admin e-mail notifications

// This is simply copy/pasted from /wp-includes/pluggable.php from 
// WP 3.0.1 - 3.1 (no changes during that time from r15380 to r17517 of trunk) 
// and the section that sends admin email is commented out. Unfortunately, 
// WordPress doesn't do something sane like a config option or overloadable 
// class method to avoid this copy-and-paste coding result.
if ( !function_exists('wp_new_user_notification') ) :
/**
 * Notify the blog admin of a new user, normally via email.
 *
 * @since 2.0
 *
 * @param int $user_id User ID
 * @param string $plaintext_pass Optional. The user's plaintext password
 */
function wp_new_user_notification($user_id, $plaintext_pass = '') {
	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

// Start edits -- Override admin email notification if option is enabled
$usr = UserSpamRemover::getInstance();
if (!$usr->getOption('noAdminEmails')) {
	$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);
}
// Log addition of new user if logging enabled
  if ($usr->getOption('activityLog')) {
    $usr->logNewUser($user);
  }
// end edits

	if ( empty($plaintext_pass) )
		return;

	$message  = sprintf(__('Username: %s'), $user_login) . "\r\n";
	$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
	$message .= wp_login_url() . "\r\n";

	wp_mail($user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);

}
endif;
// end copy/pasted (and edited) code


// Plugin hooks
if (is_admin()) {
  add_action('admin_menu', array('UserSpamRemover', 'adminMenu'));
}
add_action(UserSpamRemover::$pluginURLName, 
           array('UserSpamRemover', 'scheduledRemove'));
register_activation_hook(__FILE__, array('UserSpamRemover', 'activate'));
register_deactivation_hook(__FILE__, array('UserSpamRemover', 'deactivate'));

?>
