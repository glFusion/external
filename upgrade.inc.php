<?php
/**
 * Upgrade the External Pages plugin
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2009-2022 Lee Garner <lee@leegarner.com>
 * @package     external
 * @version     v1.0.0
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */

global $_CONF, $_CONF_EXP, $_DB_dbms;
use glFusion\Database\Database;
use glFusion\Log\Log;


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
    $db = Database::getInstance();
    try {
        $stmt = $db->conn->executeQuery(
            "UPDATE {$_TABLES['plugins']} SET
                pi_version = ?,
                pi_gl_version = ?,
                pi_homepage = ?
            WHERE pi_name = 'external'",
            array($version, $_CONF_EXP['gl_version'], $_CONF_EXP['pi_url']),
            array(Database::STRING, Database::STRING, Database::STRING)
        );
        Log::write('system', Log::INFO, "Succesfully updated the external Plugin version to $version!");
        return true;
    } catch (Throwable $e) {
        Log::write('system', Log::ERROR, "Error updating the external Plugin version to $version");
        return false;
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
        Log::write(system, Log::ERROR, "Error updating {$_CONF_EXP['pi_name']} - Undefined Version");
        return false;
    }

    // Execute SQL now to perform the upgrade
    if (isset($UPG_SQL[$version])) {
        Log::write(system, Log::INFO, "--Updating External Pages to version $version");
        $db = Database::getInstance();
       foreach ($UPG_SQL[$version] as $sql) {
            try {
                $db->conn->executeQuery($sql);
            } catch (Throwable $e) {
                Log::write('system', Log::ERROR, "External Plugin SQL Error: $sql");
                if (!$dvlp) {
                    return false;
                }
            }
        }
    }
    return true;
}

