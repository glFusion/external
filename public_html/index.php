<?php
//  $Id$
/**
*   Public entry point for External pages.
*   Displays a list of pages for the user to select.
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
require_once('../lib-common.php');

if (!$_CONF_EXP['show']) {
    $display .= COM_refresh($_CONF['site_url']);
    echo $display;
    exit;
}

$display = COM_siteHeader();
$display .= COM_startBlock($LANG_EX00['header']);
$T = new Template($_CONF['path'] . 'plugins/external/templates');
$T->set_file('index', 'index.thtml');
$T->set_block('index','Newpage','APage');

$recs = DB_query("SELECT * FROM {$_TABLES['external']} 
            ORDER BY title ASC");
$numrecs = DB_numRows($recs);
if ($numrecs < 1)  // Can $numrecs ever be -1?  Better safe than sorry
    $T->set_var('notavail', $LANG_EX00['header'] . " " . $LANG_EX00['notavail']);
else {
    while ($A = DB_fetchArray($recs)) {
        if (SEC_hasAccess($A['owner_id'], $A['group_id'], $A['perm_owner'],
                $A['perm_group'], $A['perm_members'], $A['perm_anon']) > 0) {
            $T->set_var('exid',$A['exid']);
            if (preg_match("/^(http:\/\/)/i",$A['url']) == 1) {
                $T->set_var('page',$A['url']);
            } else {
                $T->set_var('page',$_CONF['site_url'] . '/' . $A['url']);
            }
            $T->set_var('title',$A['title']);
            $T->Parse('APage','Newpage',true);
        }
    }
}

$T->parse('output','index');
$display .= $T->finish($T->get_var('output'));
$display .= COM_endBlock ();
$display .= COM_siteFooter(false);

echo $display;
?>
