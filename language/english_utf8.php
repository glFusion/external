<?php
/**
 * This is the English language page for the External Pages Plug-in.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @author      Tom Willett <tomw@pigstye.net>
 * @copyright   Copyright (c) 2009-2016 Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2002 Tom Willett <tomw@pigstye.net>
 * @package     external
 * @version     v1.0.2
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 *
 * Based on the External Pages Plugin 1.0 for Geeklog 1.3.6
 * by Tom Willett.  Updated for glFusion 1.1.5 by Lee Garner
 */

$LANG_EX00 = array (
    'header'            => 'External Pages',
    'version'           => 'Version',
    'results'           => ' Results',
    'noextern'          => 'It appears that there are no external pagess on this site or no one has ever viewed them.',
    'topten'            => 'Top Ten ',
    'save'              => 'Save',
    'delete'            => 'Delete',
    'cancel'            => 'Cancel',
    'exidmsg'           => 'EXID',
    'titlemsg'          => 'Title',
    'urlmsg'            => 'URL',
    'hitsmsg'           => 'Hits',
    'info'              => 'Info',
    'addnew'            => 'Add New',
    'adminhome'         => 'Admin Home',
    'pageno'            => 'Page #',
    'pageurl'           => 'Page URL',
    'external'          => 'External',
    'externpages'       => 'External Pages',
    'plugin'            => 'Plugin',
	'access_denied'     => 'Access Denied',
	'access_msg'        => 'You do not have permission to access this page',
	'access_denied_msg' => 'Only Root Users have Access to this Page.  Your user name and IP have been recorded.',
	'admin'		        => 'Plugin Admin',
	'install_header'	=> 'Install/Uninstall Plugin',
	'installed'         => 'The Plugin is Installed',
	'uninstalled'       => 'The Plugin is Not Installed',
	'install_success'	=> 'Installation Successful',
	'install_failed'	=> 'Installation Failed -- See your error log to find out why.',
	'uninstall_msg'		=> 'Plugin Successfully Uninstalled',
	'install'           => 'Install',
	'uninstall'         => 'UnInstall',
    'warning'           => 'Warning! Plugin is still Enabled',
    'enabled'           => 'Disable plugin before uninstalling.',
    'notavail'          => 'Not available',
);

// Localization of the Admin Configuration UI
$LANG_configsubgroups['external'] = array(
    'sg_main' => 'Main Settings',
);

$LANG_fs['external'] = array(
    'fs_main' => 'General Settings',
    'fs_permissions' => 'Default Permissions',
);

$LANG_configsections['external'] = array(
    'label' => 'External Pages',
    'title' => 'External Pages Configuration',
);

$LANG_confignames['external'] = array(
    'show' => 'Show the external page list to users',
    'defgrp' => 'Default Group',
    'defuser' => 'Default User',
    'default_permissions' => 'Default Permission',
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['external'] = array(
    0 => array('True' => 1, 'False' => 0),
    12 => array('No access' => 0, 'Read-Only' => 2, 'Read-Write' => 3),
);

?>
