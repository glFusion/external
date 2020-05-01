<?php
/**
 * Installation defaults for the External Pages plugin
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2009-2020 Lee Garner <lee@leegarner.com>
 * @package     external
 * @version     v1.0.0
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
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
 *  @global array $extConfigData
 *
 */
global $extConfigData;
$extConfigData = array(
    array(
        'name' => 'sg_main',
        'default_value' => NULL,
        'type' => 'subgroup',
        'subgroup' => 0,
        'fieldset' => 0,
        'selection_array' => NULL,
        'sort' => 0,
        'set' => true,
        'group' => 'external',
    ),
    array(
        'name' => 'fs_main',
        'default_value' => NULL,
        'type' => 'fieldset',
        'subgroup' => 0,
        'fieldset' => 0,
        'selection_array' => NULL,
        'sort' => 0,
        'set' => true,
        'group' => 'external',
    ),
    array(
        'name' => 'show',
        'default_value' => true,
        'type' => 'select',
        'subgroup' => 0,
        'fieldset' => 0,
        'selection_array' => 0,
        'sort' => 10,
        'set' => true,
        'group' => 'external',
    ),
    array(
        'name' => 'fs_permissions',
        'default_value' => NULL,
        'type' => 'fieldset',
        'subgroup' => 0,
        'fieldset' => 10,
        'selection_array' => NULL,
        'sort' => 10,
        'set' => true,
        'group' => 'external',
    ),
    array(
        'name' => 'defuser',
        'default_value' => 2,
        'type' => 'select',
        'subgroup' => 0,
        'fieldset' => 10,
        'selection_array' => 0,
        'sort' => 10,
        'set' => true,
        'group' => 'external',
    ),
    array(
        'name' => 'defgrp',
        'default_value' => 13
        'type' => 'select',
        'subgroup' => 0,
        'fieldset' => 10,
        'selection_array' => 0,
        'sort' => 20,
        'set' => true,
        'group' => 'external',
    ),
    array(
        'name' => 'default_permissions',
        'default_value' => array(3,3,2,2),
        'type' => 'select',
        'subgroup' => 0,
        'fieldset' => 10,
        'selection_array' => 0,
        'sort' => 30,
        'set' => true,
        'group' => 'external',
    ),
    array(
        'name' => 'show',
        'default_value' => true,
        'type' => 'select',
        'subgroup' => 0,
        'fieldset' => 0,
        'selection_array' => 0,
        'sort' => 10,
        'set' => true,
        'group' => 'external',
    ),
);

/**
 * Initialize External Pages plugin configuration.
 *
 * Creates the database entries for the configuation if they don't already
 * exist. Initial values will be taken from $_CONF_EXP if available (e.g. from
 * an old config.php), uses $_EXP_DEFAULT otherwise.
 *
 * @param  integer $group_id   Group ID to use as the plugin's admin group
 * @return boolean             true: success; false: an error occurred
 */
function plugin_initconfig_external($group_id = 0)
{
    global $extConfigData;

    $c = config::get_instance();
    if (!$c->group_exists('external')) {
        USES_lib_install();
        foreach ($extConfigData AS $cfgItem) {
            _addConfigItem($cfgItem);
        }
    }
    return true;
}

?>
