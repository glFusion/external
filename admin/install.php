<?php
/**
*   Manual installation for the External Pages plugin
*   @author     Lee Garner <lee@leegarner.com>
*   @author     Tom Willett <tomw@pigstye.net>
*   @copyright  Copyright (c) 2009 Lee Garner <lee@leegarner.com>
*   @copyright  Copyright (c) 2002 Tom Willett <tomw@pigstye.net>
*   @package    external
*   @version    1.0
*   @license    http://opensource.org/licenses/gpl-2.0.php 
*   GNU Public License v2 or later
*   @filesource
*
*   Based on the External Pages Plugin 1.0 for Geeklog 1.3.6
*   by Tom Willett.  Updated for glFusion 1.1.5 by Lee Garner
*/

/** Import core glFusion libraries */
require_once('../../../lib-common.php');

// Only let Root users access this page
if (!SEC_inGroup('Root')) {
    // Someone is trying to illegally access this page
    COM_errorLog("Someone has tried to illegally access the external install/uninstall page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
    $display = COM_siteHeader();
    $display .= COM_startBlock($LANG_EX00['access_denied']);
    $display .= $LANG_EX00['access_denied_msg'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
}

$base_path = "{$_CONF['path']}plugins/external";

global $_DB_dbms;
/** Import plugin main functions file */
require_once "$base_path/functions.inc";
/** Import plugin database definition */
require_once "$base_path/sql/{$_DB_dbms}_install.php";
/** Import config class for online configuration */
require_once $_CONF['path_system'] . 'classes/config.class.php';
/** Import plugin default values */
require_once "$base_path/install_defaults.php";

/**
*   $pi_name gets used in a lot of places and embedded in a lot of strings,
*   so we'll create a shorthand variable for it.
*   @global string $pi_name
*/
global $pi_name;
$pi_name = $_CONF_EXP['pi_name'];

/** global array $NEWFEATURE */
$NEWFEATURE = array();
$NEWFEATURE['external.admin']="external Admin";


/**
*   Puts the datastructures for this plugin into the Geeklog database
*   Note: Corresponding uninstall routine is in functions.inc
*   @return   boolean True if successful False otherwise
*   @ignore
*/
function plugin_install_external()
{
    global $pi_name, $NEWTABLE, $_CONF_EXP,
            $DEFVALUES, $NEWFEATURE, $_TABLES, $_CONF;

    COM_errorLog("Attempting to install the $pi_name Plugin",1);

    // Create the Plugins Tables
    foreach ($NEWTABLE as $table => $sql) {
        COM_errorLog("Creating $table table",1);
        DB_query($sql,1);
        if (DB_error()) {
            COM_errorLog("Error Creating $table table",1);
            PLG_uninstall($pi_name);
            return false;
            exit;
        }
        COM_errorLog("Success - Created $table table",1);
    }
    
    // Insert Default Data
    /*foreach ($DEFVALUES as $table => $sql) {
        COM_errorLog("Inserting default data into $table table",1);
        DB_query($sql,1);
        if (DB_error()) {
            COM_errorLog("Error inserting default data into $table table",1);
            PLG_uninstall($pi_name);
            return false;
            exit;
        }
        COM_errorLog("Success - inserting data into $table table",1);
    }*/
    
    // Create the plugin admin security group
    COM_errorLog("Attempting to create $pi_name admin group", 1);
    DB_query("INSERT INTO {$_TABLES['groups']} (
            grp_name, 
            grp_descr) 
        VALUES (
            '$pi_name Admin', 
            'Users in this group can administer the $pi_name plugin')",1);
    if (DB_error()) {
        PLG_uninstall($pi_name);
        return false;
        exit;
    }
    COM_errorLog('...success',1);
    $group_id = DB_insertId();
    
    // Save the grp id for later uninstall
    COM_errorLog('About to save group_id to vars table for use during uninstall',1);
    DB_query("INSERT INTO {$_TABLES['vars']} 
            VALUES ('{$pi_name}_gid', $group_id)",1);
    if (DB_error()) {
        PLG_uninstall($pi_name);
        return false;
        exit;
    }
    COM_errorLog('...success',1);
    
    // Add plugin Features
    foreach ($NEWFEATURE as $feature => $desc) {
        COM_errorLog("Adding $feature feature",1);
        DB_query("INSERT INTO {$_TABLES['features']} 
                (ft_name, ft_descr) 
            VALUES 
                ('$feature','$desc')",1);
        if (DB_error()) {
            COM_errorLog("Failure adding $feature feature",1);
            PLG_uninstall($pi_name);
            return false;
            exit;
        }
        $feat_id = DB_insertId();
        COM_errorLog("Success",1);
        COM_errorLog("Adding $feature feature to admin group",1);
        DB_query("INSERT INTO {$_TABLES['access']} 
                (acc_ft_id, acc_grp_id) 
            VALUES 
                ($feat_id, $group_id)");
        if (DB_error()) {
            COM_errorLog("Failure adding $feature feature to admin group",1);
            PLG_uninstall($pi_name);
            return false;
            exit;
        }
        COM_errorLog("Success",1);
    }        
    
    // OK, now give Root users access to this plugin now! NOTE: Root group should always be 1
    COM_errorLog("Attempting to give all users in Root group access to $pi_name admin group",1);
    DB_query("INSERT INTO {$_TABLES['group_assignments']} 
            VALUES ($group_id, NULL, 1)");
    if (DB_error()) {
        PLG_uninstall($pi_name);
        return false;
        exit;
    }

    // Load the online configuration records
    if (!plugin_initconfig_external($group_id)) {
        PLG_uninstall($pi_name);
        return false;
    }

    // Register the plugin with Geeklog
    COM_errorLog("Registering $pi_name plugin with Geeklog", 1);
    DB_delete($_TABLES['plugins'],'pi_name','external');
    DB_query("INSERT INTO {$_TABLES['plugins']} (
            pi_name, 
            pi_version, 
            pi_gl_version, 
            pi_homepage, 
            pi_enabled) 
        VALUES (
            '{$_CONF_EXP['pi_name']}', 
            '{$_CONF_EXP['pi_version']}', 
            '{$_CONF_EXP['gl_version']}', 
            '{$_CONF_EXP['pi_url']}', 
            1)");

    if (DB_error()) {
        PLG_uninstall($pi_name);
        return false;
        exit;
    }

    COM_errorLog("Succesfully installed the $pi_name Plugin!",1);
    return true;
}

/* 
* Main Function
*/

/*$display = COM_siteHeader();
$T = new Template($_CONF['path'] . 'plugins/external/templates');
$T->set_file('install', 'install.thtml');
$T->set_var('install_header', $LANG_EX00['install_header']);
$T->set_var('img',$_CONF['site_url'] . '/external/images/external.gif');
$T->set_var('cgiurl', $_CONF['site_admin_url'] . '/plugins/external/install.php');
$T->set_var('admin_url', $_CONF['site_admin_url'] . '/plugins/external/index.php');
*/

switch ($_REQUEST['action']) {
case 'install':
    if (plugin_install_external()) {
        echo COM_refresh($_CONF['site_admin_url'] . '/plugins.php?msg=44');
        //$T->set_var('installmsg1',$LANG_EX00['install_success']);
    } else {
        echo COM_refresh($_CONF['site_admin_url'] . '/plugins.php?msg=72');
        //$T->set_var('installmsg1',$LANG_EX00['install_failed']);
    }
    break;
case 'uninstall':
    if (PLG_uninstall($pi_name)) {
        echo COM_refresh($_CONF['site_admin_url'] . '/plugins.php?msg=45');
    } else {
        echo COM_refresh($_CONF['site_admin_url'] . '/plugins.php?msg=72');
    }
    break;
}

/*if (DB_count($_TABLES['plugins'], 'pi_name', $pi_name) == 0) {
    $T->set_var('installmsg2', $LANG_EX00['uninstalled']);
	$T->set_var('btnmsg', $LANG_EX00['install']);
	$T->set_var('action','install');
} else {
    $T->set_var('installmsg2', $LANG_EX00['installed']);
	$T->set_var('btnmsg', $LANG_EX00['uninstall']);
	$T->set_var('action','uninstall');
}
$T->parse('output','install');
$display .= $T->finish($T->get_var('output'));
$display .= COM_siteFooter(true);

echo $display;
*/
?>
