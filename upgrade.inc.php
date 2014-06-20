<?php
/**
*   Upgrade the plugin
*   @author     Lee Garner <lee@leegarner.com>
*   @copyright  Copyright (c) 2009 Lee Garner <lee@leegarner.com>
*   @package    external
*   @version    1.0
*   @license    http://opensource.org/licenses/gpl-2.0.php 
*   GNU Public License v2 or later
*   @filesource
*/

global $_CONF, $_CONF_EXP, $_DB_dbms;

/** Include default values for new config items */
//require_once "{$_CONF['path']}plugins/{$_CONF_EXP['pi_name']}/install_defaults.php";

/**
*   Sequentially perform version upgrades.
*   @param current_ver string Existing installed version to be upgraded
*   @return integer Error code, 0 for success
*/
function EXP_upgrade($current_ver)
{
    $error = 0;

    // < 1.0 -> 1.0 : Nothing to do
    return $error;

}


/**
*   Execute the SQL statement to perform a version upgrade.
*   An empty SQL parameter will return success.
*
*   @param string   $version  Version being upgraded to
*   @param array    $sql      SQL statement to execute
*   @return integer Zero on success, One on failure.
*/
function EXP_upgrade_sql($version='Undefined', $sql='')
{
    global $_TABLES, $_CONF_EXP;

    // We control this, so it shouldn't happen, but just to be safe...
    if ($version == 'Undefined') {
        COM_errorLog("Error updating {$_CONF_EXP['pi_name']} - Undefined Version");
        return 1;
    }

    // If no sql statements passed in, return success
    if (!is_array($sql))
        return 0;

    // Execute SQL now to perform the upgrade
    COM_errorLOG("--Updating External Pages to version $version");
    for ($i = 1; $i <= count($sql); $i++) {
        COM_errorLOG("External Pages Plugin $version update: Executing SQL => " . current($sql));
        DB_query(current($sql),'1');
        if (DB_error()) {
            COM_errorLog("SQL Error during External Pages plugin update",1);
            return 1;
            break;
        }
        next($sql);
    }

    return 0;

}


?>
