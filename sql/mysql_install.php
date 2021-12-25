<?php
/**
 * MySQL table creation statements for the External Pages plugin.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2009-2020 Lee Garner <lee@leegarner.com>
 * @package     external
 * @version     v1.0.0
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */


/**
 * Global array of new tables to be created
 * @global array $NEWTABLE
 */
global $_TABLES, $NEWTABLE;
$NEWTABLE = array();
$NEWTABLE['external'] = "CREATE TABLE {$_TABLES['external']} (
    exid int(8) NOT NULL auto_increment,
    title varchar(80) default '',
    url varchar(255) default '',
    hits mediumint(8) unsigned default 0,
    group_id mediumint(8) unsigned DEFAULT '1' NOT NULL,
    owner_id mediumint(8) unsigned DEFAULT '1' NOT NULL,
    perm_owner tinyint(1) unsigned DEFAULT '3' NOT NULL,
    perm_group tinyint(1) unsigned DEFAULT '3' NOT NULL,
    perm_members tinyint(1) unsigned DEFAULT '2' NOT NULL,
    perm_anon tinyint(1) unsigned DEFAULT '2' NOT NULL,
    PRIMARY KEY exid(exid)
    ) ENGINE=MyISAM";

global $UPG_SQL;
$UPG_SQL = array();

?>
