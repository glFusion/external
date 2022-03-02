<?php
/**
*   Manual installation for the External Pages plugin
*   @author     Lee Garner <lee@leegarner.com>
*   @author     Tom Willett <tomw@pigstye.net>
*   @copyright  Copyright (c) 2009-2018 Lee Garner <lee@leegarner.com>
*   @copyright  Copyright (c) 2002 Tom Willett <tomw@pigstye.net>
*   @package    external
*   @version    1.0.2
*   @license    http://opensource.org/licenses/gpl-2.0.php
*               GNU Public License v2 or later
*   @filesource
*
*   Based on the External Pages Plugin 1.0 for Geeklog 1.3.6
*   by Tom Willett.  Updated for glFusion 1.1.5 by Lee Garner
*/

/** Import core glFusion libraries */
require_once('../../../lib-common.php');
use glFusion\Log\Log;

// Only let Root users access this page
if (!SEC_inGroup('Root')) {
    // Someone is trying to illegally access this page
    Log::write('system', Log::ERROR, "Someone has tried to illegally access the external install/uninstall page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR");
    COM_404();
    exit;
}

$base_path = "{$_CONF['path']}plugins/external";

/** Import plugin functions.  Not included since plugin isn't installed yet */
require_once $base_path . '/functions.inc';
/** Import auto-installation routines */
require_once $base_path . '/autoinstall.php';

USES_lib_install();

/*
*   MAIN
*/
if (SEC_checkToken()) {
    if ($_GET['action'] == 'install') {
        if (plugin_install_external()) {
            echo COM_refresh($_CONF['site_admin_url'] . '/plugins.php?msg=44');
            exit;
        } else {
            echo COM_refresh($_CONF['site_admin_url'] . '/plugins.php?msg=72');
            exit;
        }
    } else if ($_GET['action'] == "uninstall") {
        USES_lib_plugin();
        if (PLG_uninstall('external')) {
            echo COM_refresh($_CONF['site_admin_url'] . '/plugins.php?msg=45');
            exit;
        } else {
            echo COM_refresh($_CONF['site_admin_url'] . '/plugins.php?msg=73');
            exit;
        }
    }
}

?>
