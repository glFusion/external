<?php
/**
*   Upgrade the External Pages plugin
*
*   @author     Lee Garner <lee@leegarner.com>
*   @copyright  Copyright (c) 2009-2016 Lee Garner <lee@leegarner.com>
*   @package    external
*   @version    1.0.2
*   @license    http://opensource.org/licenses/gpl-2.0.php 
*               GNU Public License v2 or later
*   @filesource
*/

global $_CONF, $_CONF_EXP, $_DB_dbms;

/** Include default values for new config items */
require_once "{$_CONF['path']}plugins/{$_CONF_EXP['pi_name']}/install_defaults.php";

/**
*   Sequentially perform version upgrades.
*
*   @param  string  $current_ver    Existing installed version to be upgraded
*   @return integer                 Error code, 0 for success
*/
function EXP_upgrade($current_ver)
{
    global $_CONF_EXP, $_EXP_DEFAULT;

    $error = 0;

    $c = config::get_instance();

    // < 1.0 -> 1.0 : Nothing to do
    if ($current_ver < '1.0.2') {
        $c->add('defuser', $_EXP_DEFAULT['defuser'],
                'select', 0, 4, 0, 80, true, $_CONF_EXP['pi_name']);
    }

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
