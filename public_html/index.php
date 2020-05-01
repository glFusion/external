<?php
/**
 * Public entry point for External pages.
 * Displays a list of pages for the user to select.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @author      Tom Willett <tomw@pigstye.net>
 * @copyright   Copyright (c) 2009-2018 Lee Garner <lee@leegarner.com>
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

/** Import core glFusion libraries */
require_once('../lib-common.php');

if (!$_CONF_EXP['show']) {
    COM_404();
    exit;
}

$display = COM_siteHeader();
$display .= COM_startBlock($LANG_EX00['header']);
$T = new Template($_CONF['path'] . 'plugins/external/templates');
$T->set_file('index', 'index.thtml');
$T->set_block('index','Newpage','APage');

$Pages = External\Page::getAll();
if (count($Pages) < 1) {
    $T->set_var('notavail', $LANG_EX00['header'] . " " . $LANG_EX00['notavail']);
} else {
    foreach ($Pages as $Page) {
        $T->set_var(array(
            //'exid'      => $Page->getID(),
            'page'      => $Page->getUrl(),
            'title'     => $Page->getTitle(),
        ) );
        $T->parse('APage', 'Newpage', true);
    }
}
$T->parse('output','index');
$display .= $T->finish($T->get_var('output'));
$display .= COM_endBlock ();
$display .= COM_siteFooter(false);
echo $display;
?>
