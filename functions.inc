<?php
/**
*   Common functions for the External Pages plugin.
*
*   @author     Lee Garner <lee@leegarner.com>
*   @author     Tom Willett <tomw@pigstye.net>
*   @copyright  Copyright (c) 2009 Lee Garner <lee@leegarner.com>
*   @copyright  Copyright (c) 2002 Tom Willett <tomw@pigstye.net>
*   @package    external
*   @version    1.0.2
*   @license    http://opensource.org/licenses/gpl-2.0.php
*               GNU Public License v2 or later
*   @filesource
*
*   Based on the External Pages Plugin 1.0 for Geeklog 1.3.6
*   by Tom Willett.  Updated for glFusion 1.1.5 by Lee Garner.
*/

if (!defined('GVERSION')) {
    die('This file can not be used on its own.');
}

/** Global array of plugin configuration values
*   @global array $_CONF_EXP
*/
global $_CONF_EXP;

//  Check and see if we need to load the plugin configuration
if (!isset($_CONF_EXP['show'])) {
    /** Import configuration class */
    require_once $_CONF['path_system'] . 'classes/config.class.php';

    $external_config = config::get_instance();
    $_CONF_EXP = $external_config->get_config('external');
}

/** Import plugin static variables */
require_once "{$_CONF['path']}plugins/external/external.php";
define('EXP_PI_PATH', $_CONF['path'].'/plugins/'.$_CONF_EXP['pi_name']);
define('EXP_URL', $_CONF['site_url'].'/'.$_CONF_EXP['pi_name']);
define('EXP_ADMIN_URL',
        $_CONF['site_admin_url'] . '/plugins/' . $_CONF_EXP['pi_name']);
$_CONF_EXP['_is_uikit'] = $_SYSTEM['framework'] == 'uikit' ? true : false;

if (file_exists(EXP_PI_PATH . '/config.php')) {
    /** Include local configuration overrides */
    require_once EXP_PI_PATH . '/config.php';
}

/** Determine the correct language file */
$langpath = EXP_PI_PATH . '/language';
$langfile = "{$_CONF['language']}.php";
if (!file_exists("$langpath/$langfile")) {
    $langfile = 'english.php';
}
/** Include the proper language file */
include_once "$langpath/$langfile";
if (file_exists("$langpath/custom/$langfile")) {
    /** Include the custom language file, if any */
    include_once "$langpath/custom/$langfile";
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


function USES_external_upgrade()
{
    require_once EXP_PI_PATH .'/upgrade.inc.php';
}


/**
*   Get the currently installed version
*
*   @return string  Plugin version
*/
function plugin_chkVersion_external()
{
    global $_CONF_EXP;
    return $_CONF_EXP['pi_version'];
}


/**
*   Show the statistics for the external files plugin on stats.php. 
*   If $showsitestats is 1 then we are to only print the overall stats .
*
*   @param  integer showsitestats   Flag to let us know which stats to get
*/
function plugin_showstats_external($showsitestats)
{
    global $LANG_EX00, $_CONF, $_TABLES;

    $stat_templates = new Template($_CONF['path_layout'] . 'stats');
    $stat_templates->set_file(array('itemstats'=>'itemstatistics.thtml',
                            'statrow'=>'singlestat.thtml'));
    if ($showsitestats == 1) {
        $total_pages=DB_count($_TABLES['external']);
        $retval = "<table border = '0' width='100%' cellspacing='0' cellpadding='0'>";
        $retval .= "<tr><td>{$LANG_EX00['externpages']}</td>";
        $retval .= "<td align='right'>" . $total_pages . "&nbsp;&nbsp </td></tr></table>";
    } else {
        $result = DB_query("SELECT url, title, hits
                FROM {$_TABLES['external']}
                WHERE hits > 0
                ORDER BY hits DESC
                LIMIT 10");
        $nrows  = DB_numRows($result);
        $retval .= COM_startBlock($LANG_EX00['topten'] . $LANG_EX00['externpages']);
        if ($nrows > 0) {
            $stat_templates->set_var('item_label',$LANG_EX00['titlemsg']);
            $stat_templates->set_var('stat_name', $LANG_EX00['hitsmsg']);
            for ($i = 0; $i < $nrows; $i++) {
                $A = DB_fetchArray($result);
                if (preg_match("/^(http:\/\/)/i",$A['url']) == 1) {
                    $stat_templates->set_var('item_url', $A['url']);
                } else {
                    $stat_templates->set_var('item_url', $_CONF['site_url'] . '/' . $A['url']);
                }
                $stat_templates->set_var('item_text', stripslashes($A['title']));
                $stat_templates->set_var('item_stat', $A['hits']);
                $stat_templates->parse('stat_row','statrow',true);
            }
            $stat_templates->parse('output','itemstats');
            $retval .= $stat_templates->finish($stat_templates->get_var('output'));
        } else {
            $retval .= $LANG_EX00['noextern'];
        }	
        $retval .= COM_endBlock();
    }
    return $retval;
}


/**
*   Add the external page plugin to the available search types
*
*   @return array   pi_name => title
*/
function plugin_searchtypes_external()
{
    global $LANG_EX00;

    $tmp['external'] = $LANG_EX00['externpages'];
    return $tmp;
}


/**
*   Provides the getItemInfo function for daily quotes
*
*   @param  string  $id         Product ID
*   @param  string  $fields     Comma-separated list of fields to return
*   @param  integer $uid        User ID
*   @param  array   $options    Not used
*   @return array           Array of product information
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
    $fields = implode(',', $fields);
    $retval = array();
    $sql = "SELECT $fields FROM {$_TABLES['external']} WHERE 1=1 ";
    if ($id != '*') {
        $sql .= " AND (exid = '" . DB_escapeString($id) . "')";
    }
    if ($buildingSearchIndex) {
        $sql .= " AND perm_anon > 1";
    }

    //COM_errorLog($sql);
    $res = DB_query($sql);
    while ($A = DB_fetchArray($res, false)) {
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
*   Searches for pages matching the user query and returns an array of
*   for the header and table rows back to search.php where it will be
*   formated and printed.
*
*   @param  string  query       Keywords user is looking for
*   @param  string  datestart   Start date to get results for
*   @param  string  dateend     End date to get results for
*   @param  string  topic       The topic they were searching in
*   @param  string  type        Type of items they are searching
*   @param  string  author      Get all results by this author
*/
function plugin_dopluginsearch_external(
    $query,
    $datestart,
    $dateend,
    $topic,
    $type,
    $author
) {
	global $_TABLES, $_CONF, $LANG_EX00;

    if (empty($type)) {
        $type = 'all';
    }

    // Bail if we aren't supppose to do our search.  Also, we can't handle
    // searches without a query, such as "all postings by..."
    if (($type <> 'all' && $type <> 'external') || empty($query)) {
        $plugin_results = new Plugin();
        $plugin_results->plugin_name = 'external';
        $plugin_results->searchlabel = $LANG_EX00['externpages'] . $LANG_EX00['results'];
        return $plugin_results;
    }

    // Build search SQL - Modified to exclude static PHP pages from search.
    $sql = "SELECT * from {$_TABLES['external']}";
    $result = DB_query($sql);
   
    // OK, now create new plugin object and insert table header labels
    require_once($_CONF['path_system'] . 'classes/plugin.class.php');
    $plugin_results = new Plugin();
    $plugin_results->plugin_name = 'external';
    $plugin_results->searchlabel = $LANG_EX00['externpages'] . $LANG_EX00['results'];
    $plugin_results->addSearchHeading($LANG_EX00['titlemsg']);
    $plugin_results->addSearchHeading($LANG_EX00['urlmsg']);
    $plugin_results->addSearchHeading($LANG_EX00['hitsmsg']);
    $mycount = DB_numRows($result);

    // NOTE if any of your data items need to be links then add them here!
    // make sure data elements are in an array and in the same order as your
    // headings above!
    $rcnt = 0;
	for ($i = 1; $i <= $mycount; $i++) {
        $A = DB_fetchArray($result);

        if(SEC_hasAccess($A['owner_id'], $A['group_id'], $A['perm_owner'],
                $A['perm_group'], $A['perm_members'], $A['perm_anon'])){
            if (preg_match("/^(http:\/\/)/i",$A['url']) == 1) {
                $pth = $A['url'];
                $url = $A['url'];
            } else {
                $pth = $_CONF['path_html'] . $A['url'];
                $url = $_CONF['site_url'] . '/' . $A['url'];
            }
            $cnts = @file_get_contents($pth);
            if (empty($cnts)) continue;
            if (stristr($cnts,$query) != '') {
                $rcnt++;
                $A['title'] = stripslashes($A['title']);
                $row = array($A['title'],
                      $url,
                     $A['hits']);
                /*$row = array($A['title'],
                      '<a href="' . $url . '">' . $A['url'] . "</a>",
                     $A['hits']);*/
                $plugin_results->addSearchResult($row);
                $rcnt++;
            }
  	    }
	
	}
    $plugin_results->num_searchresults = $rcnt;
    $plugin_results->num_itemssearched = DB_count($_TABLES['external']);
    return $plugin_results;
}


/**
*   This will put an option for the plugin in command and control.
*   Add the plugin name, icon and link to the command and control
*   block in moderation.php.
*
*   @return   array   Array (plugin name, admin url, url of plugin icon)
*/
function plugin_cclabel_external()
{
    global $_CONF, $LANG_EX00;

    if (SEC_hasRights('external.admin')) {
        return array($LANG_EX00['externpages'],
            EXP_ADMIN_URL . '/index.php',
            plugin_geticon_external()
        );
    }
}


/**
*   Returns the URL of the plugin's icon
*
*   @return string      URL of the icon
*/
function plugin_geticon_external()
{
    return EXP_URL . '/images/external.png';
}


/**
*   Returns the administrative option for this plugin
*   Adds the plugin to the Admin menu
*
*   @return array   (plugin name, plugin admin url, # of items in plugin or '')
*/
function plugin_getadminoption_external()
{
    global $_TABLES, $LANG_EX00;

    if (SEC_hasRights('external.admin')) {
        return array($LANG_EX00['externpages'],
                EXP_ADMIN_URL . '/index.php',
                DB_count($_TABLES['external']));
    }
}


/**
*   Get an array of items to remove during plugin removal.
*
*   @return array
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
*   Upgrade the plugin
*
*   @return boolean     True on success, error code on failure
*/
function plugin_upgrade_external()
{
    global $_TABLES, $_CONF_EXP;

    $pi_name = $_CONF_EXP['pi_name'];

    // Get the current version, compare to the version in the plugin code
    $current_ver = DB_getItem($_TABLES['plugins'], 'pi_version',
        "pi_name = '$pi_name'");
    if (!$current_ver) {
        COM_errorLog("Error getting the $pi_name Plugin version",1);
        return '03';
    }

    // if we're at the current version, what are we doing here?
    if ($current_ver >= $_CONF_EXP['pi_version']) {
        COM_errorLog("Current version $current_ver is already up to date");
        return '06';
    }

    // If we're still here, import the upgrade functions.
    USES_external_upgrade();
    $error = EXP_upgrade($current_ver);
    if ($error) {
        COM_errorLog("Error updating the $pi_name Plugin version",1);
        return '04';
    }

    // now update the current version number.
    DB_query("UPDATE
        {$_TABLES['plugins']}
    SET
        pi_version = '{$_CONF_EXP['pi_version']}',
        pi_gl_version = '{$_CONF_EXP['gl_version']}',
        pi_homepage = '{$_CONF_EXP['pi_url']}'
    WHERE
        pi_name = '$pi_name';
    ");

    if (DB_error()) {
        COM_errorLog("Error updating the $pi_name Plugin version",1);
        return '05';
    }

    COM_errorLog("Succesfully updated the $pi_name Plugin!",1);
    return true;
}


/**
*   Group selection for the default group assigned to new pages
*
*   @return array   name => id array of groups
*/
function plugin_configmanager_select_defgrp_external()
{
    return EXP_getGroups('\'All Users\'', 'NOT');
}


/**
*   User selection for the default user assigned to new pages
*
*   @return array   name => id array of users
*/
function plugin_configmanager_select_defuser_external()
{
    global $_TABLES;

    $A = array();

    $query = "SELECT uid, username FROM {$_TABLES['users']}";
    $result = DB_query($query);
    if ($result && DB_numRows($result) > 0) {
        while ($row = DB_fetchArray($result)) {
            $A[$row['username']] = $row['uid'];
        }
    }
    return $A;
}


////////////////////////////////////////////////////////////
//
//  Plugin-specific API functions
//
////////////////////////////////////////////////////////////

/**
*   Get an array of groups for option lists.
*
*   @param  string  $grplist    Comma-separated list of groups, SQL format
*   @param  string  $NOT        "NOT" to exclude $grplist, blank to include it.
*   @return array           name => grp_id array of groups
*/
function EXP_getGroups($grplist='', $NOT='')
{
    global $_TABLES;

    $A = array();

    $query = "SELECT grp_id, grp_name FROM {$_TABLES['groups']}";

    // If a group list is specified, add it as a condition with the
    // specified keyword.
    if ($grplist!= '') {
        $query .= " WHERE grp_name $NOT IN ($grplist)";
    }

    $result = DB_query($query);
    if ($result && DB_numRows($result) > 0) {
        while ($row = DB_fetchArray($result)) {
            $A[$row['grp_name']] = $row['grp_id'];
        }
    }

    return $A;

}


/**
*   Public API function.
*   This is the public API function that's called directly by the external
*   page. It checks access for the page, or adds a new record.
*
*   @param    string    $page   Page to check
*   @return   boolean           True if has access, False otherwise
*/
function EXP_externalAccess($page)
{
    global $_TABLES, $_CONF, $_CONF_EXP, $_USER;

    // Assume user has no access to this page
    $retval = false;

    $page = DB_escapeString($page);
    $sql = "SELECT * FROM {$_TABLES['external']} WHERE title='$page'";
    $recs = DB_query($sql,1);
    if (DB_numRows($recs) > 0) {
        // Have to check access from the returned record rather than as part
        // of the query. If a user doesn't have access, the query would return
        // an empty set which would act like a new page being added with default
        // access.
        $A = DB_fetchArray($recs);
        $access = SEC_hasAccess($A['owner_id'], $A['group_id'],
                $A['perm_owner'], $A['perm_group'],
                $A['perm_members'], $A['perm_anon']);
        if ($access == 0)
            // No access, return false without updating hit counter
            return false;
        else
            $retval = true;
        $sql = "UPDATE {$_TABLES['external']} SET
                hits = hits+1
                WHERE exid={$A['exid']}";
        DB_query($sql,1);
    } else {
        $url = $_SERVER['REQUEST_SCHEME'] . '://' .
            $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
        $sql = "INSERT INTO {$_TABLES['external']}
                SET
                    title='$page',
                    url='" . DB_escapeString($url) . "',
                    hits=1,
                    owner_id=".(int)$_CONF_EXP['defuser'].",
                    group_id=".(int)$_CONF_EXP['defgrp'].",
                    perm_owner=".(int)$_CONF_EXP['default_permissions'][0].",
                    perm_group=".(int)$_CONF_EXP['default_permissions'][1].",
                    perm_members=".(int)$_CONF_EXP['default_permissions'][2]. ",
                    perm_anon=".(int)$_CONF_EXP['default_permissions'][3];
        DB_query($sql);
        $retval = true;
    }
    return $retval;
}

/**
*   Helper function to get a template object.
*
*   @param  string  $name   Base name of template, e.g. "editor"
*   @param  string  $id     ID of template, e.g. "editform"
*   @param  string  $path   Optional path if not "templates/"
*   @return string      Full filename, e.g. "editor.uikit.thtml"
*/
function EXP_getTemplate($name, $id, $path='')
{
    global $_CONF_EXP;

    // May use "switch" later, for now there are only two options
    $tpltype = $_CONF_EXP['_is_uikit'] ? '.uikit' : '';
    $filename = "$name$tpltype.thtml";
    if (substr($path, 0, 9) != 'templates') {
        $path = 'templates/' . $path;
    }
    if (!file_exists(__DIR__ . "/$path/$filename")) {
        $filename = "$name.thtml";
    }
    $T = new Template(__DIR__ . '/' . $path);
    $T->set_file($id, $filename);
    return $T;
}

?>
