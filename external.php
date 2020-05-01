<?php
/**
 *   Table definitions and other static config variables.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2009-2020 Lee Garner <lee@leegarner.com>
 * @package     external
 * @version     v1.0.0
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 *
 * Based on the External Pages Plugin 1.0 for Geeklog 1.3.6
 * by Tom Willett.  Updated for glFusion 1.1.5 by Lee Garner
 */

/**
 * Global array of table names from glFusion.
 * @global array $_TABLES
 */
global $_TABLES;

/**
 * Global table name prefix.
 * @global string $_DB_table_prefix
 */
global $_DB_table_prefix;

$_EXP_table_prefix = $_DB_table_prefix;

// Add Plugin tables to $_TABLES array
$_TABLES['external'] = $_EXP_table_prefix . 'external';

$_CONF_EXP['pi_name'] = 'external';
$_CONF_EXP['pi_version'] = '1.0.0';
$_CONF_EXP['gl_version'] = '1.7.0';
$_CONF_EXP['pi_url'] = 'http://www.leegarner.com';
$_CONF_EXP['pi_display_name'] = 'External Pages';

?>
