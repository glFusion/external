<?php
/**
 * Common functions for the External Pages plugin.
 * Based on the External Pages Plugin 1.0 for Geeklog 1.3.6
 * by Tom Willett.  Updated for glFusion 1.1.5 by Lee Garner.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @author      Tom Willett <tomw@pigstye.net>
 * @copyright   Copyright (c) 2009-2022 Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2002 Tom Willett <tomw@pigstye.net>
 * @package     external
 * @version     v1.0.0
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */

if (!defined('GVERSION')) {
    die('This file can not be used on its own.');
}

/** Global array of plugin configuration values
 * @global array $_CONF_EXP
 */
global $_CONF_EXP;

//  Check and see if we need to load the plugin configuration
if (!isset($_CONF_EXP['show'])) {
    /** Import configuration class */
    require_once $_CONF['path_system'] . 'classes/config.class.php';

    $external_config = config::get_instance();
    $_CONF_EXP = $external_config->get_config('external');
}

spl_autoload_register(function ($class)
{
    if (strpos($class, 'External\\') === 0) {
        $class = str_replace('External\\', '', $class);
        $path = __DIR__ . '/classes/' . $class . '.class.php';
        if (file_exists($path)) {
            include $path;
        }
    }
});
use glFusion\Database\Database;

/** Import plugin static variables */
require_once __DIR__ . '/external.php';
define('EXP_PI_PATH', $_CONF['path'].'/plugins/'.$_CONF_EXP['pi_name']);
define('EXP_URL', $_CONF['site_url'].'/'.$_CONF_EXP['pi_name']);
define('EXP_ADMIN_URL',
        $_CONF['site_admin_url'] . '/plugins/' . $_CONF_EXP['pi_name']);

if (file_exists(EXP_PI_PATH . '/config.php')) {
    /** Include local configuration overrides */
    require_once EXP_PI_PATH . '/config.php';
}

/** Determine the correct language file */
$langpath = EXP_PI_PATH . '/language';
$langfile = "{$_CONF['language']}.php";
if (!file_exists("$langpath/$langfile")) {
    $langfile = 'english_utf-8.php';
}
/** Include the proper language file */
include_once "$langpath/$langfile";

if (file_exists("$langpath/custom/$langfile")) {
    /** Include the custom language file, if any */
    include_once "$langpath/custom/$langfile";
}


/**
 * Get the currently installed version.
 *
 * @return  string  Plugin version
 */
function plugin_chkVersion_external()
{
    global $_CONF_EXP;
    return $_CONF_EXP['pi_version'];
}


/**
 * Show the statistics for the external files plugin on stats.php.
 * If $showsitestats is 1 then we are to only print the overall stats .
 *
 * @param   integer showsitestats   Flag to let us know which stats to get
 */
function plugin_showstats_external($showsitestats)
{
    global $LANG_EX00, $_CONF, $_TABLES;

    $T = new Template($_CONF['path_layout'] . 'stats');
    $T->set_file(array('itemstats'=>'itemstatistics.thtml',
                            'statrow'=>'singlestat.thtml'));
    if ($showsitestats == 1) {
        $total_pages=DB_count($_TABLES['external']);
        $retval = "<table border = '0' width='100%' cellspacing='0' cellpadding='0'>";
        $retval .= "<tr><td>{$LANG_EX00['externpages']}</td>";
        $retval .= "<td align='right'>" . $total_pages . "&nbsp;&nbsp </td></tr></table>";
    } else {
        $db = Database::getInstance();
        try {
            $qb = Database::getInstance()->conn->createQueryBuilder();
            $qb->select('url', 'title', 'hits')
               ->from($_TABLES['external'])
               ->where('hits > 0')
               ->andWhere(COM_getPermSQL(''))
               ->orderBy('hits', 'ASC')
               ->setFirstResult(0)
               ->setMaxResults(10);
            $stmt = $qb->execute();
            $data = $stmt->fetchAll(Database::ASSOCIATIVE);
        } catch(\Throwable $e) {
            $data = array();
        }

        $nrows  = count($data);
        $retval = COM_startBlock($LANG_EX00['topten'] . $LANG_EX00['externpages']);
        if ($nrows > 0) {
            $T->set_var('item_label',$LANG_EX00['titlemsg']);
            $T->set_var('stat_name', $LANG_EX00['hitsmsg']);
            foreach ($data as $A) {
                if (preg_match("/^(http:\/\/)/i", $A['url']) == 1) {
                    $T->set_var('item_url', $A['url']);
                } else {
                    $T->set_var('item_url', $_CONF['site_url'] . '/' . $A['url']);
                }
                $T->set_var('item_text', stripslashes($A['title']));
                $T->set_var('item_stat', $A['hits']);
                $T->parse('stat_row','statrow',true);
            }
            $T->parse('output','itemstats');
            $retval .= $T->finish($T->get_var('output'));
        } else {
            $retval .= $LANG_EX00['noextern'];
        }	
        $retval .= COM_endBlock();
    }
    return $retval;
}


/**
 * Add the external page plugin to the available search types.
 *
 * @return  array   pi_name => title
 */
function plugin_searchtypes_external()
{
    global $LANG_EX00;

    $tmp['external'] = $LANG_EX00['externpages'];
    return $tmp;
}


/**
 * Provides the getItemInfo function for external pages.
 *
 * @param   string  $id         Product ID
 * @param   string  $what       Comma-separated list of fields to return
 * @param   integer $uid        User ID
 * @param   array   $options    Not used
 * @return  array           Array of product information
 */
function plugin_getiteminfo_external($id, $what, $uid=0, $options=array())
{
    global $_TABLES, $_CONF, $LANG_EX00;

    $properties = explode(',', $what);
    $fields = array();
    $retval = array();
    $buildingSearchIndex = false;
    $getContent = false;
    foreach ($properties as $p) {
        switch ($p) {
        case 'search_index':
            $buildingSearchIndex = true;
            $fields[] = 'url';
            break;
        case 'id':
            $fields[] = 'exid';
            break;
        case 'title':
            $fields[] = 'title';
            break;
        case 'content':
        case 'description':
            $fields[] = 'url';
            $buildingSearchIndex = true;
            $getContent = true;
            break;
        case 'searchidx':
            $fields[] = 'title';
            $fields[] = 'url';
            $buildingSearchIndex = true;
            $getContent = true;
            break;
        case 'url':
            $fields[] = 'url';
            break;
        case 'author':
        case 'author_name':
            $fields[] = 'owner_id';
            break;
        default:
            $fields[] = $p;
            break;
        }
    }
    if (count($fields) == 0) {
        if (!$buildingSearchIndex) {
            return $retval;
        } else {
            $fields[] = 'exid';
        }
    }
    $fields = array_unique($fields);
    //$fields = implode(',', $fields);
    $retval = array();

    $db = Database::getInstance();
    $queryBuilder = $db->conn->createQueryBuilder();
    foreach ($fields as $field) {
        $queryBuilder->addSelect($field);
    }
    $queryBuilder->from($_TABLES['external']);
    if ($id != '*') {
        $queryBuilder->andWhere('exid = ' . $queryBuilder->createNamedParameter($id));
    }
    if ($buildingSearchIndex) {
        $queryBuilder->andWhere('perm_anon > 1');
    }
    $stmt = $queryBuilder->execute();
    $data = $stmt->fetchAll(Database::ASSOCIATIVE);
    foreach ($data as $A) {
        $contents = '';
        if (isset($A['url'])) {
            if (strpos($A['url'], $_CONF['site_url']) === false) {
                if ($A['url'][0] != '/') $A['url'] = '/' . $A['url'];
                $A['url'] = $_CONF['site_url'] . $A['url'];
            }
            if ($getContent) {
                $ch = curl_init();
                if ($ch) {
                    curl_setopt($ch, CURLOPT_URL, $A['url']);
                    // we want to get the contents of the URL and store it in a variable
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    // specify the useragent: this is a required courtesy to site owners
                    curl_setopt($ch, CURLOPT_USERAGENT, 'cURL');
                    // ignore SSL errors
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    // Do not follow redirects
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $contents = strip_tags($result);;
                }
            }
        }
        foreach ($properties as $p) {
            switch ($p) {
            case 'id':
                $props[$p] = $A['exid'];
                break;
            case 'excerpt':
            case 'description':
            case 'content':
                $props[$p] = $contents;
                break;
            case 'title':
                $props[$p] = empty($A['title']) ? $LANG_DQ['untitled'] : $A['title'];
                break;
            case 'author':
                $props[$p] = $A['owner_id'];
                break;
            case 'author_name':
                $props[$p] = COM_getDisplayName($A['owner_id']);
                break;
            case 'searchidx':
                $props[$p] = $A['title'] . ' ' . $contents;
                break;
            case 'url':
                $props[$p] = $A['url'];
                break;
            default:
                $props[$p] = isset($A[$p]) ? $A[$p] : NULL;
                break;
            }
        }
        $mapped = array();
        foreach ($props as $key => $value) {
            if ($id == '*') {
                if ($value != '') {
                    $mapped[$key] = $value;
                }
            } else {
                $mapped[$key] = $value;
            }
        }

        if ($id == '*') {
            $retval[] = $mapped;
        } else {
            $retval = $mapped;
            break;
        }
    }

    if (($id != '*') && (count($retval) == 1)) {
        $tRet = array_values($retval);
        $retval = $tRet[0];
    }
    if ($retval === '' || (is_array($retval) && count($retval) == 0)) {
        return NULL;
    } else {
        return $retval;
    }
}


/**
 * Get the contents from an external URL.
 *
 * @param   string  $url    URL to retrieve
 * @return  string      Contents from URL
 */
function EXT_getContents($url)
{
    if (preg_match("/^(http:\/\/)/i",$url) == 1) {
        $pth = $url;
        $url = $url;
    } else {
        $pth = $_CONF['path_html'] . $url;
        $url = $_CONF['site_url'] . '/' . $url;
    }
    $cnts = @file_get_contents($pth);
}


/**
 * This will put an option for the plugin in command and control.
 * Add the plugin name, icon and link to the command and control
 * block in moderation.php.
 *
 * @return  array   Array (plugin name, admin url, url of plugin icon)
 */
function plugin_cclabel_external()
{
    global $_CONF, $LANG_EX00;

    if (SEC_hasRights('external.admin')) {
        return array(
            $LANG_EX00['externpages'],
            EXP_ADMIN_URL . '/index.php',
            plugin_geticon_external()
        );
    }
}


/**
 * Returns the URL of the plugin's icon.
 *
 * @return  string      URL of the icon
 */
function plugin_geticon_external()
{
    return EXP_URL . '/images/external.png';
}


/**
 * Returns the administrative option for this plugin.
 * Adds the plugin to the Admin menu.
 *
 * @return  array   (plugin name, plugin admin url, # of items in plugin or '')
 */
function plugin_getadminoption_external()
{
    global $_TABLES, $LANG_EX00;

    if (SEC_hasRights('external.admin')) {
        return array(
            $LANG_EX00['externpages'],
            EXP_ADMIN_URL . '/index.php',
            DB_count($_TABLES['external'])
        );
    }
}


/**
 * Get an array of items to remove during plugin removal.
 *
 * @return  array   Array of elements to be removed
 */
function plugin_autouninstall_external()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('external'),
        /* give the full name of the group, as in the db */
        'groups' => array('external Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('external.admin'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars'=> array('external_gid')
    );
    return $out;
}


/**
 * Upgrade the plugin.
 *
 * @return  boolean     True on success, error code on failure
 */
function plugin_upgrade_external()
{
    require_once __DIR__ . '/upgrade.inc.php';
    return EXP_upgrade(false);
}


/**
 * Group selection for the default group assigned to new pages.
 *
 * @return  array   name => id array of groups
 */
function plugin_configmanager_select_defgrp_external()
{
    global $_TABLES;

    $retval = array();
    $query = "SELECT grp_id, grp_name FROM {$_TABLES['groups']}";
    $db = Database::getInstance();
    try {
        $stmt = $db->conn->executeQuery($query);
        $data = $stmt->fetchAll(Database::ASSOCIATIVE);
        foreach ($data as $A) {
            $retval[$A['grp_name']] = $row['grp_id'];
        }
    } catch(\Throwable $e) {
    }
    return $retval;
}


/**
 * User selection for the default user assigned to new pages.
 *
 * @return  array   name => id array of users
 */
function plugin_configmanager_select_defuser_external()
{
    global $_TABLES;

    $retval = array();
    $query = "SELECT uid, username FROM {$_TABLES['users']}";
    $db = Database::getInstance();
    try {
        $stmt = $db->conn->executeQuery($query);
        $data = $stmt->fetchAll(Database::ASSOCIATIVE);
        foreach ($data as $A) {
            $retval[$A['username']] = $row['uid'];
        }
    } catch(\Throwable $e) {
    }
    return $retval;
}


////////////////////////////////////////////////////////////
//
//  Plugin-specific API functions
//
////////////////////////////////////////////////////////////

/**
 * Get an array of groups for option lists.
 *
 * @param   string  $grplist    Comma-separated list of groups, SQL format
 * @param   string  $NOT        "NOT" to exclude $grplist, blank to include it.
 * @return  array           name => grp_id array of groups
 */
function EXP_getGroups($grplist='', $NOT='')
{
    global $_TABLES;

    $retval = array();
    $query = "SELECT grp_id, grp_name FROM {$_TABLES['groups']}";

    // If a group list is specified, add it as a condition with the
    // specified keyword.
    if ($grplist!= '') {
        $query .= " WHERE grp_name $NOT IN ($grplist)";
    }
    $db = Database::getInstance();
    try {
        $stmt = $db->conn->executeQuery($query);
        $data = $stmt->fetchAll(Database::ASSOCIATIVE);
        foreach ($data as $A) {
            $retval[$A['grp_name']] = $row['grp_id'];
        }
    } catch(\Throwable $e) {
    }
    return $retval;
}


/**
 * Public API function.
 * This is the public API function that's called directly by the external
 * page. It checks access for the page, or adds a new record.
 *
 * @uses    External\Page::checkAccess()
 * @param   string    $page   Page to check
 * @return  boolean           True if has access, False otherwise
 */
function EXP_externalAccess($page)
{
    return External\Page::checkAccess($page);
}

