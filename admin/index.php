<?php
/**
 * Administration entry point for the External Pages plugin.
 * Based on the External Pages Plugin 1.0 for Geeklog 1.3.6
 * by Tom Willett. Updated for glFusion 1.1.5+ by Lee Garner.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @author      Tom Willett <tomw@pigstye.net>
 * @copyright   Copyright (c) 2009-2022 Lee Garner <lee@leegarner.com>
 * @package     external
 * @version     v1.0.0
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */

/** Import core glFusion libraries */
require_once('../../../lib-common.php');

// Make sure the plugin is installed and enabled
if (!in_array('external', $_PLUGINS) || !SEC_hasRights('external.admin')) {
    COM_404();
    exit;
}

$content = '';
$action = '';
$actionval = '';
$expected = array(
    'save', 'delete', 'cancel', 'edit',
    'list',
);
foreach ($expected as $provided) {
    if (isset($_POST[$provided])) {
        $action = $provided;
        $actionval = $_POST[$provided];
        break;
    } elseif (isset($_GET[$provided])) {
        $action = $provided;
        $actionval = $_GET[$provided];
        break;
    }
}
$exid = isset($_REQUEST['exid']) ? (int)$_REQUEST['exid'] : 0;

// Execute any action that was requested
switch ($action) {
case 'edit':
    $P = new External\Page($exid);
    $content .= $P->Edit();
    break;

case 'save':
    $P = new External\Page($exid);
    $P->Save($_POST);
    echo COM_refresh(EXP_ADMIN_URL);
    exit;

case 'delete':
    if ($exid > 0) {
        DB_delete($_TABLES['external'], 'exid', $exid);
    }
    echo COM_refresh(EXP_ADMIN_URL);
    break;

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
 * Build the admin list of pages.
 *
 * @return  string      HTML content
 */
function EXP_adminList()
{
    global $_CONF, $_TABLES, $LANG_ADMIN, $LANG_ACCESS, $_CONF_EXP, $LANG_EX00;

    USES_lib_admin();

    $retval = '';

    $header_arr = array(
        array(
            'text' => $LANG_ADMIN['edit'],
            'field' => 'edit',
            'sort' => false,
            'align' => 'center',
        ),
        array(
            'text' => $LANG_EX00['pageno'],
            'field' => 'exid',
            'sort' => true,
            'align' => 'right',
        ),
        array(
            'text' => $LANG_EX00['titlemsg'],
            'field' => 'title',
            'sort' => true,
        ),
        array(
            'text' => 'URL',
            'field' => 'url',
            'sort' => true,
        ),
        array(
            'text' => $LANG_EX00['hitsmsg'],
            'field' => 'hits',
            'sort' => true,
            'align' => 'right',
        ),
        array(
            'text' => $LANG_EX00['delete'],
            'field' => 'delete',
            'sort' => false,
            'align' => 'center',
        ),
    );

    $menu_arr = array (
        array(
            'url' => EXP_ADMIN_URL . '/index.php?edit=x&exid=0',
            'text' => $LANG_EX00['addnew'],
        ),
        array(
            'url' => $_CONF['site_admin_url'],
            'text' => $LANG_ADMIN['admin_home'],
        ),
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
        'form_url' => EXP_ADMIN_URL . '/index.php',
    );
    $query_arr = array(
        'table' => 'external',
        'sql' => "SELECT * FROM {$_TABLES['external']} ",
        'query_fields' => array('title', 'url'),
        'default_filter' => COM_getPermSql()
    );
    $form_arr = array();
    $retval .= ADMIN_list('external', 'EXP_getAdminListField',
                $header_arr, $text_arr, $query_arr, $defsort_arr,
                '', '', '', $form_arr);
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));
    return $retval;
}


/**
 * Returns a formatted field to the admin list when managing general locations.
 *
 * @param   string  $fieldname  Name of field
 * @param   string  $fieldvalue Value of field
 * @param   array   $A          Array of all values
 * @param   array   $icon_arr   Array of icons
 * @return  string  String to display for the selected field
 */
function EXP_getAdminListField($fieldname, $fieldvalue, $A, $icon_arr)
{
    $retval = '';

    switch($fieldname) {
    case 'edit':
        $retval = glFusion\FieldList::edit(array(
            'url' => EXP_ADMIN_URL . "/index.php?edit=x&exid={$A['exid']}",
        ) );
        break;

    case 'delete':
        $retval = glFusion\FieldList::delete(array(
            'delete_url' => EXP_ADMIN_URL . '/index.php?delete=x&exid=' . $A['exid'],
             array(
                'onclick' => "return confirm('Do you really want to delete this item?');",
            )
        ) );
        break;

    case 'hits':
        $retval = (int)$fieldvalue;
        break;

    case 'url':
        $retval = COM_createLink($fieldvalue, $fieldvalue);
        break;

    default:
        $retval = htmlspecialchars($fieldvalue);
        break;
    }
    return $retval;
}

