<?php
/**
 * Upgrade the External Pages plugin
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2009-2020 Lee Garner <lee@leegarner.com>
 * @package     external
 * @version     v1.0.2
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */

global $_CONF, $_CONF_EXP, $_DB_dbms;


/**
 * Sequentially perform version upgrades.
 *
 * @param   string  $current_ver    Existing installed version to be upgraded
 * @param   boolean $dvlp       True if a development update, False if not
 * @return  boolean     True on success, False on error
 */
function EXP_upgrade($current_ver, $dvlp=false)
{
    global $_CONF_EXP, $_EXP_DEFAULT;

    $installed_ver = plugin_chkVersion_external();

    // Update the plugin configuration
    USES_lib_install();
    require_once __DIR__ . '/install_defaults.php';
    global $extConfigData;
    _update_config('external', $extConfigData);

    // Final extra check to catch code-only patch versions
    if (!COM_checkVersion($current_ver, $installed_ver)) {
        if (!EXP_do_update_version($installed_ver)) return false;
    }
    return true;
}


/**
 * Update the plugin version.
 * Done at each update step to keep the version up to date
 *
 * @param   string  $version    Version to set
 * @return  boolean     True on success, False on failure
 */
function EXP_do_update_version($version)
{
    global $_TABLES, $_CONF_EXP;

    // now update the current version number.
    DB_query("UPDATE {$_TABLES['plugins']} SET
            pi_version = '{$version}',
            pi_gl_version = '{$_CONF_EXP['gl_version']}',
            pi_homepage = '{$_CONF_EXP['pi_url']}'
        WHERE pi_name = 'external'");

    if (DB_error()) {
        COM_errorLog("Error updating the external Plugin version to $version",1);
        return false;
    } else {
        COM_errorLog("Succesfully updated the external Plugin version to $version!",1);
        return true;
    }
}



/**
 * Execute the SQL statement to perform a version upgrade.
 *
 * @param string   $version  Version being upgraded to
 * @param   boolean $dvlp       True if a development update, False if not
 * @return integer Zero on success, One on failure.
 */
function EXP_upgrade_sql($version='Undefined', $dvlp=false)
{
    global $_TABLES, $_CONF_EXP;

    require_once __DIR__ . '/sql/mysql_install.php';

    // We control this, so it shouldn't happen, but just to be safe...
    if ($version == 'Undefined') {
        COM_errorLog("Error updating {$_CONF_EXP['pi_name']} - Undefined Version");
        return false;
    }

    // Execute SQL now to perform the upgrade
    if (isset($UPG_SQL[$version])) {
        COM_errorLOG("--Updating External Pages to version $version");
        foreach ($UPG_SQL[$version] as $sql) {
            COM_errorLOG("External Pages Plugin $version update: Executing SQL => " . current($sql));
            DB_query($sql, '1');
            if (DB_error()) {
                COM_errorLog("SQL Error during External Pages plugin update",1);
                if (!$dvlp) {
                return false;
                }
            }
        }
    }
    return true;
}

?>
