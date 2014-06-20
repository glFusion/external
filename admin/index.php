<?php
/**
*   Administration entry point for the External Pages plugin
*   @author     Lee Garner <lee@leegarner.com>
*   @author     Tom Willett <tomw@pigstye.net>
*   @copyright  Copyright (c) 2009 Lee Garner <lee@leegarner.com>
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

// Make sure the plugin is installed and enabled
if (!in_array('external', $_PLUGINS)) {
    echo COM_refresh($_CONF['site_url'] . '/404.php');
}

// Only let admin users access this page
if (!SEC_hasRights('external.admin')) {
    // Someone is trying to illegally access this page
    COM_errorLog("Someone has tried to illegally access the external Admin page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
    $display = COM_siteHeader();
    $display .= COM_startBlock($LANG_EX00['access_denied']);
    $display .= $LANG_EX00['access_denied_msg'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
}


$content = '';
$exid = isset($_REQUEST['exid']) ? (int)$_REQUEST['exid'] : 0;

// Execute any action that was requested
switch ($_REQUEST['mode']) {
case 'delete':
    if ($exid > 0) {
        DB_delete($_TABLES['external'], 'exid', $exid);
    }
    $page = '';
    break;

default:
    $page = '';
    break; 
        
}

// After the action is carried out, display the appropriate page content
switch ($page) {
default:
    $content .= EXP_adminList();
    break;
}

// Now create the page content
$display = COM_siteHeader();
$display .= $content;
$display .= COM_siteFooter();
echo $display;


/**
*   Build the admin list of pages
*   @return string      HTML content
*/
function EXP_adminList()
{
    global $_CONF, $_TABLES, $LANG_ADMIN, $LANG_ACCESS, $_CONF_EXP, $LANG_EX00;

    USES_lib_admin();

    $retval = '';

    $header_arr = array(      # display 'text' and use table field 'field'
        array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
        array('text' => 'Page ID', 'field' => 'exid', 'sort' => true),
        array('text' => 'Title', 'field' => 'title', 'sort' => true),
        array('text' => 'URL', 'field' => 'url', 'sort' => true),
        array('text' => 'Hits', 'field' => 'hits', 'sort' => true),
    );

    $menu_arr = array (
        array('url' => $_CONF['site_admin_url'] . '/plugins/' .
                            $_CONF_EXP['pi_name'] . '/edit.php?exid=0',
              //'text' => $LANG_GEO['contrib_origin']),
                'text' => 'Add New'),
        array('url' => $_CONF['site_admin_url'],
              'text' => $LANG_ADMIN['admin_home']),
    );

    $defsort_arr = array('field' => 'exid', 'direction' => 'asc');

    $header_str = $LANG_EX00['header'] . ' ' . $LANG_EX00['version'] .
        ' ' . $_CONF_EXP['pi_version'];
    $retval .= COM_startBlock($header_str, '', 
                COM_getBlockTemplate('_admin_block', 'header'));

    $retval .= ADMIN_createMenu($menu_arr, 'Administer External Pages', 
            plugin_geticon_external());

    $text_arr = array(
        'has_extras' => true,
        'form_url' => "{$_CONF['site_admin_url']}/plugins/{$_CONF_EXP['pi_name']}/index.php"
    );

    $query_arr = array('table' => 'external',
        'sql' => "SELECT * FROM {$_TABLES['external']} ",
        'query_fields' => array('title', 'url'),
        //'default_filter' => 'WHERE 1=1'
        'default_filter' => COM_getPermSql()
    );

    $retval .= ADMIN_list('external', 'EXP_getAdminListField', 
                $header_arr, $text_arr, $query_arr, $defsort_arr, 
                '', '', '', $form_arr);
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    return $retval;
}


/**
*   Returns a formatted field to the admin list when managing general locations
*   @param  string  $fieldname  Name of field
*   @param  string  $fieldvalue Value of field
*   @param  array   $A          Array of all values
*   @param  array   $icon_arr   Array of icons
*   @return string  String to display for the selected field
*
*/
function EXP_getAdminListField($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $_CONF_EXP, $LANG24, $LANG_EX00;
    
    $retval = '';

    switch($fieldname) {
    case 'edit':
        $retval = COM_createLink(
            $icon_arr['edit'],
            "{$_CONF['site_admin_url']}/plugins/{$_CONF_EXP['pi_name']}/edit.php?exid={$A['exid']}"
            );
        $retval .= '&nbsp;&nbsp;' . COM_createLink(
            COM_createImage($_CONF['site_url'] . '/' .
                $_CONF_EXP['pi_name'] . '/images/deleteitem.png',
                'Delete this item',
                array('title' => 'Delete this item', 'class' => 'gl_mootip',
                    'onclick' => "return confirm('Do you really want to delete this item?');")),
                "{$_CONF['site_admin_url']}/plugins/{$_CONF_EXP['pi_name']}/index.php" .
                "?mode=delete&amp;exid={$A['exid']}"
            );
            break;

    case 'exid':
    case 'title':
    case 'hits':
        $retval = htmlspecialchars($fieldvalue);
        break;

    case 'url':
        $retval = COM_createLink($fieldvalue, $fieldvalue);
        break;

    }

    return $retval;
}


?>
