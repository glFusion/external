<?php
/**
*   Installation defaults for the External Pages plugin
*   @author     Lee Garner <lee@leegarner.com>
*   @copyright  Copyright (c) 2009 Lee Garner <lee@leegarner.com>
*   @package    external
*   @version    1.0
*   @license    http://opensource.org/licenses/gpl-2.0.php 
*               GNU Public License v2 or later
*   @filesource
*/

if (!defined('GVERSION')) {
    die('This file can not be used on its own!');
}

/**
 *  External Pages default settings
 *
 *  Initial Installation Defaults used when loading the online configuration
 *  records. These settings are only used during the initial installation
 *  and not referenced any more once the plugin is installed
 *  @global array $_EXP_DEFAULT
 *
 */
global $_EXP_DEFAULT, $_CONF_EXP;
$_EXP_DEFAULT = array();

$_EXP_DEFAULT['show'] = true;

// Set the default permissions
$_EXP_DEFAULT['default_permissions'] =  array (3, 3, 2, 2);

// Set the default group ID for page access
$_EXP_DEFAULT['defgrp'] = 13;

/**
 *  Initialize External Pages plugin configuration
 *
 *  Creates the database entries for the configuation if they don't already
 *  exist. Initial values will be taken from $_CONF_EXP if available (e.g. from
 *  an old config.php), uses $_EXP_DEFAULT otherwise.
 *
 *  @param  integer $group_id   Group ID to use as the plugin's admin group
 *  @return boolean             true: success; false: an error occurred
 */
function plugin_initconfig_external($group_id = 0)
{
    global $_CONF, $_CONF_EXP, $_EXP_DEFAULT;

    if (is_array($_CONF_EXP) && (count($_CONF_EXP) > 1)) {
        $_EXP_DEFAULT = array_merge($_EXP_DEFAULT, $_CONF_EXP);
    }

    // Use configured default if a valid group ID wasn't presented
    if ($group_id == 0)
        $group_id = $_EXP_DEFAULT['defgrp'];

    $c = config::get_instance();

    if (!$c->group_exists($_CONF_EXP['pi_name'])) {

        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, $_CONF_EXP['pi_name']);
        $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, $_CONF_EXP['pi_name']);

        $c->add('show', $_EXP_DEFAULT['show'], 
                'select', 0, 0, 0, 10, true, $_CONF_EXP['pi_name']);

        $c->add('fs_permissions', NULL, 'fieldset', 0, 4, NULL, 0, true, $_CONF_EXP['pi_name']);
        $c->add('defgrp', $group_id,
                'select', 0, 4, 0, 90, true, $_CONF_EXP['pi_name']);
        $c->add('default_permissions', $_EXP_DEFAULT['default_permissions'],
                '@select', 0, 4, 12, 100, true, $_CONF_EXP['pi_name']);

    }

    return true;
}

?>
