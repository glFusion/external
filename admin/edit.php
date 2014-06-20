<?php
/**
*   Administrative edit functions.
*
*   @author     Lee Garner <lee@leegarner.com>
*   @author     Tom Willett <tomw@pigstye.net>
*   @copyright  Copyright (c) 2009 Lee Garner <lee@leegarner.com>
*   @package    external
*   @version    1.0.1
*   @license    http://opensource.org/licenses/gpl-2.0.php 
*   GNU Public License v2 or later
*   @filesource
*
*   Based on the External Pages Plugin 1.0 for Geeklog 1.3.6
*   by Tom Willett.  Updated for glFusion 1.1.5 by Lee Garner
*/

/** Import core glFusion libraries */
require_once('../../../lib-common.php');

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
 
/**
* Main 
*/
$exid = (int)$_REQUEST['exid'];

switch ($_REQUEST['action']) {
case $LANG_EX00['save']:
    list($perm_owner,$perm_group,$perm_members,$perm_anon) = 
        SEC_getPermissionValues(
            $_REQUEST['perm_owner'], $_REQUEST['perm_group'],
            $_REQUEST['perm_members'] ,$_REQUEST['perm_anon']);

    if ($exid == 0) {
        $sql = "INSERT INTO ";
    } else {
        $sql = "UPDATE ";
    }
    $sql .= " {$_TABLES['external']} SET
            title='" . DB_escapeString($_REQUEST['title']). "',
            url='" . DB_escapeString($_REQUEST['url']) . "',
            hits='" . DB_escapeString($_REQUEST['hits']) . "',
            group_id=" . (int)$_REQUEST['group_id'] . ",
            owner_id=" . (int)$_REQUEST['owner_id'] . ",
            perm_owner=$perm_owner,
            perm_group=$perm_group,
            perm_members=$perm_members,
            perm_anon=$perm_anon ";
    if ($exid != 0) {
        $sql .= " WHERE exid=$exid";
    }
    DB_query($sql);
    echo COM_refresh($_CONF['site_admin_url'] . "/plugins/external/index.php");
    exit;

case $LANG_EX00['delete']:
    DB_delete($_TABLES['external'], 'exid', $exid);
    echo COM_refresh($_CONF['site_admin_url'] . "/plugins/external/index.php");
    exit;

case $LANG_EX00['cancel']:
    echo COM_refresh($_CONF['site_admin_url'] . "/plugins/external/index.php");
    exit;
}

if ($exid > '0') {
    $rec=DB_query("SELECT * FROM {$_TABLES['external']} WHERE exid=$exid");
    $A=DB_fetchArray($rec);
    $title = $A['title'];
    $url = $A['url'];
    $hits = $A['hits'];
    $owner_id = $A['owner_id'];
    $group_id = $A['group_id'];
    $perm_owner = $A['perm_owner'];
    $perm_group = $A['perm_group'];
    $perm_members = $A['perm_members'];
    $perm_anon = $A['perm_anon'];
} else {
    $exid = '0';
    $title = '';
    $url = '';
    $hits = '';
    $owner_id = 1;
    $group_id = 0;
    $perm_owner = 3;
    $perm_group = 3;
    $perm_members = 2;
    $perm_anon = 2;
}

$display = COM_siteHeader();
$T = new Template($_CONF['path'] . 'plugins/external/templates');
$T->set_file('admin', 'edit.thtml');
$T->set_var('site_url',$_CONF['site_url']);
$T->set_var('site_admin_url', $_CONF['site_admin_url']);
$T->set_var('header', $LANG_EX00['externpages'] . ' ' . $LANG_EX00['admin']);
$T->set_var('plugin','external');

$T->set_var('exidmsg',$LANG_EX00['exidmsg']);
$T->set_var('titlemsg',$LANG_EX00['titlemsg']);
$T->set_var('urlmsg',$LANG_EX00['urlmsg']);
$T->set_var('hitsmsg',$LANG_EX00['hitsmsg']);
$T->set_var('save',$LANG_EX00['save']);
$T->set_var('delete',$LANG_EX00['delete']);
$T->set_var('cancel',$LANG_EX00['cancel']);

$T->set_var('exid',$exid);
$T->set_var('title',$title);
$T->set_var('url',$url);
$T->set_var('hits',$hits);
$T->set_var('perms',SEC_getPermissionsHTML ( $perm_owner, $perm_group, $perm_members, $perm_anon));
$T->set_var('lang_owner', $LANG_ACCESS[owner]);
if (SEC_inGroup('Root')) {
	$T->set_var('owner_username', '');
	$usrdd = '<SELECT name="owner_id">' . COM_optionList($_TABLES['users'],"uid,username",$owner_id) . "</SELECT>";
	$T->set_var('owner_id',$usrdd);
} else {
	$T->set_var('owner_username', '&nbsp;&nbsp;&nbsp' . DB_getItem($_TABLES['users'],'username',"uid = {$c_oid}"));
	$T->set_var('owner_id', "<input type='hidden' name='owner_id[]' value='" . $owner_id . "'>");
}
$T->set_var('lang_group', $LANG_ACCESS[group]);
$usergroups = SEC_getUserGroups();
$groupdd .= '<SELECT name="group_id">';
for ($i = 0; $i < count($usergroups); $i++) {
    $groupdd .= '<option value="' . $usergroups[key($usergroups)] . '"';
    if ($group_id == $usergroups[key($usergroups)]) {
        $groupdd .= ' SELECTED';
    }
    $groupdd .= '>' . key($usergroups) . '</option>';
    next($usergroups);
}
$groupdd .= '</SELECT>';
$T->set_var('group_dropdown', $groupdd);
$T->set_var('lang_permissions', $LANG_ACCESS[permissions]);
$T->set_var('lang_perm_key', $LANG_ACCESS[permissionskey]);
$T->set_var('permissions_msg', $LANG_ACCESS[permmsg]);
    
$T->parse('output','admin');
$display .= $T->finish($T->get_var('output'));
$display .= COM_siteFooter(true);

echo $display;
?>
