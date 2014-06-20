<?php
/**
*   Automatically install the External Pages plugin
*   @author     Lee Garner <lee@leegarner.com>
*   @copyright  Copyright (c) 2009 Lee Garner <lee@leegarner.com>
*   @package    external
*   @version    1.0
*   @license    http://opensource.org/licenses/gpl-2.0.php 
*               GNU Public License v2 or later
*   @filesource
*/

if (!defined ('GVERSION')) {
    die ('This file can not be used on its own.');
}

global $_DB_dbms;
/** Import core glFusion libraries */
require_once $_CONF['path'].'plugins/external/functions.inc';
/** Import plugin database definition */
require_once $_CONF['path'].'plugins/external/sql/'. $_DB_dbms. '_install.php';


/** Plugin installation options
*   @global array $INSTALL_plugin['external']
*/
$INSTALL_plugin['external'] = array(
    'installer' => array('type' => 'installer', 
            'version' => '1', 
            'mode' => 'install'),

    'plugin' => array('type' => 'plugin', 
            'name' => $_CONF_EXP['pi_name'],
            'ver' => $_CONF_EXP['pi_version'], 
            'gl_ver' => $_CONF_EXP['gl_version'],
            'url' => $_CONF_EXP['pi_url'], 
            'display' => $_CONF_EXP['pi_display_name']),

    array('type' => 'table', 
            'table' => $_TABLES['external'], 
            'sql' => $NEWTABLE['external']),

    array('type' => 'group', 
            'group' => 'external Admin', 
            'desc' => 'Users in this group can administer the External Pages plugin',
            'variable' => 'admin_group_id', 
            'admin' => true,
            'addroot' => true),

    array('type' => 'feature', 
            'feature' => 'external.admin', 
            'desc' => 'External Admin',
            'variable' => 'admin_feature_id'),

    array('type' => 'mapping', 
            'group' => 'admin_group_id', 
            'feature' => 'admin_feature_id',
            'log' => 'Adding feature to the admin group'),

);


/**
* Puts the datastructures for this plugin into the glFusion database
* Note: Corresponding uninstall routine is in functions.inc
* @return   boolean True if successful False otherwise
*/
function plugin_install_external()
{
    global $INSTALL_plugin, $_CONF_EXP;

    $pi_name            = $_CONF_EXP['pi_name'];
    $pi_display_name    = $_CONF_EXP['pi_display_name'];
    $pi_version         = $_CONF_EXP['pi_version'];

    COM_errorLog("Attempting to install the $pi_display_name plugin", 1);

    $ret = INSTALLER_install($INSTALL_plugin[$pi_name]);
    if ($ret > 0) {
        return false;
    }

    return true;
}


/**
* Loads the configuration records for the Online Config Manager
* @return   boolean     true = proceed with install, false = an error occured
*/
function plugin_load_configuration_external()
{
    global $_CONF, $_CONF_EXP, $_TABLES;

    COM_errorLog("Loading the configuration for the External plugin",1);

    require_once $_CONF['path'].'plugins/'.$_CONF_EXP['pi_name'].'/install_defaults.php';

    // Get the admin group ID that was saved previously.
    $group_id = (int)DB_getItem($_TABLES['groups'], 'grp_id', 
            "grp_name='{$_CONF_EXP['pi_name']} Admin'");

    return plugin_initconfig_external($group_id);
}

?>
