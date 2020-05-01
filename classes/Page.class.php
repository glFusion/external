<?php
/**
 * Class to handle External Page objects.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2017-2019 Lee Garner <lee@leegarner.com>
 * @package     external
 * @version     v1.0.2
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */
namespace External;


/**
 * Class to support editing and managing stored external pages.
 * @package external
 */
class Page
{
    /** External page record ID.
     * @var integer */
    private $exid = 0;

    /** Owner ID.
     * @var integer */
    private $owner_id = 0;

    /** Group ID.
     * @var integer */
    private $group_id = 0;

    /** Number of page views.
     * @var integer */
    private $hits = 0;

    /** Owner permission.
     * @var integer */
    private $perm_owner = 3;

    /** Group permission.
     * @var integer */
    private $perm_group = 3;

    /** Members permission.
     * @var integer */
    private $perm_members = 2;

    /** Anonymous permission.
     * @var integer */
    private $perm_anon = 2;

    /** Page URL.
     * @var string */
    private $url = '';

    /** Page title.
     * @var string */
    private $title = '';


    /**
     * Read a record if an ID value is provided.
     *
     * @param   integer $exid   Optional record ID
     */
    public function __construct($exid=0)
    {
        global $_CONF_EXP;

        if (is_array($exid)) {
            $this->setVars($exid, true);
        } elseif ($exid > 0) {
            $this->Read($exid);
        } else {
            $this->owner_id = $_CONF_EXP['defuser'];
            $this->group_id = $_CONF_EXP['defgrp'];
            $this->perm_owner = $_CONF_EXP['default_permissions'][0];
            $this->perm_group = $_CONF_EXP['default_permissions'][1];
            $this->perm_members = $_CONF_EXP['default_permissions'][2];
            $this->perm_anon = $_CONF_EXP['default_permissions'][3];
        }
    }


    /**
     * Read a single record.
     *
     * @param   integer $exid   Page ID
     */
    public function Read($exid)
    {
        global $_TABLES;

        $exid = (int)$exid;
        if ($exid > 0) {
            $res = DB_query(
                "SELECT * FROM {$_TABLES['external']} WHERE exid=$exid"
            );
            if ($res) {
                $A=DB_fetchArray($res, false);
                $this->setVars($A, true);
            }
        }
    }


    /**
     * Set the record variables from a DB record or form.
     *
     * @param   array   $A          Array of field values
     * @param   boolean $from_db    True of reading from the DB, False if a form
     */
    public function setVars($A, $from_db=true)
    {
        foreach ($A as $key=>$value) {
            $this->$key = $value;
        }
        $this->exid = (int)$A['exid'];
        $this->owner_id = (int)$A['owner_id'];
        $this->group_id = (int)$A['group_id'];
        $this->setHits($A['hits']);
        $this->setUrl($A['url']);
        $this->setTitle($A['title']);
        if ($from_db) {
            $this->perm_owner = (int)$A['perm_owner'];
            $this->perm_group = (int)$A['perm_group'];
            $this->perm_members = (int)$A['perm_members'];
            $this->perm_anon = (int)$A['perm_anon'];
        } else {
            $P = SEC_getPermissionValues(
                $A['perm_owner'],
                $A['perm_group'],
                $A['perm_members'],
                $A['perm_anon']
            );
            $this->perm_owner = (int)$P[0];
            $this->perm_group = (int)$P[1];
            $this->perm_members = (int)$P[2];
            $this->perm_anon = (int)$P[3];
        }
    }


    /**
     * Get the page record ID
     *
     * @return  integer     Record ID
     */
    public function getID()
    {
        return (int)$this->exid;
    }


    /**
     * Get the URL to the page.
     *
     * @return  string      Page URL
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * Get the page title.
     *
     * @return  string  Page title
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * Set the page hits.
     *
     * @param   string  $hits   Page hit count
     * @return  object  $this
     */
    public function setHits($hits)
    {
        $this->hits = (int)$hits;
        return $this;
    }


    /**
     * Set the page URL.
     *
     * @param   string  $url    Page URL
     * @return  object  $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }


    /**
     * Set the page title.
     *
     * @param   string  $title  Page title
     * @return  object  $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }


    /**
     * Display the edit form.
     *
     * @return  string  HTML for the edit form
     */
    public function Edit()
    {
        global $LANG_EX00, $_CONF_EXP, $LANG_ACCESS, $_TABLES;

        $T = new \Template(__DIR__ . '/../templates');
        $T->set_file('edit', 'edit.thtml');
        $T->set_var(array(
            'header'    => $LANG_EX00['externpages'] . ' ' . $LANG_EX00['admin'],
            'plugin'    => $_CONF_EXP['pi_name'],
            'pi_icon'   => plugin_geticon_external(),
            'exid'      => $this->exid,
            'title'     => $this->title,
            'url'       => $this->url,
            'hits'      => $this->hits,
            'perms'     => SEC_getPermissionsHTML(
                $this->perm_owner,
                $this->perm_group,
                $this->perm_members,
                $this->perm_anon
            ),
            'owner_dd'  => COM_optionList(
                $_TABLES['users'],
                'uid,username',
                $this->owner_id
            ),
            'group_dd'  => COM_optionList(
                $_TABLES['groups'],
                'grp_id,grp_name',
                $this->group_id
            ),
        ) );
        $T->parse('output','edit');
        return $T->finish($T->get_var('output'));
    }


    /**
     * Save the current object.
     *
     * @param   array   $A  Optional array of field values
     * @return  boolean     True on success, False on error
     */
    public function Save($A=array())
    {
        global $_TABLES;

        if (is_array($A) && !empty($A)) {
            $this->setVars($A, false);
        }

        if ($this->exid == 0) {
            $sql1 = "INSERT INTO {$_TABLES['external']} SET ";
            $sql3 = '';
        } else {
            $sql1 = "UPDATE {$_TABLES['external']} SET ";
            $sql3 = " WHERE exid = {$this->exid}";
        }
        $sql2 = "title = '" . DB_escapeString($this->title). "',
            url = '" . DB_escapeString($this->url) . "',
            hits = '{$this->hits}',
            group_id = {$this->group_id},
            owner_id = {$this->owner_id},
            perm_owner = {$this->perm_owner},
            perm_group = {$this->perm_group},
            perm_members = {$this->perm_members},
            perm_anon = {$this->perm_anon}";
        $sql = $sql1 . $sql2 . $sql3;
        //echo $sql;die;
        DB_query($sql);
        return DB_error() ? false : true;
    }


    /**
     * Get the access.
     */
    public function getAccess()
    {
        $retval = SEC_hasAccess(
            $this->owner_id,
            $this->group_id,
            $this->perm_owner,
            $this->perm_group,
            $this->perm_members,
            $this->perm_anon
        );
        return $retval;
    }


    /**
     * Delete a single record.
     *
     * @param   integer $exid   Record ID of page
     */
    public static function Delete($exid)
    {
        global $_TABLES;
        DB_delete($_TABLES['external'], 'exid', (int)$exid);
    }


    /**
     * Get a page by title.
     *
     * @param   string  $title  Page title
     * @return  object      Page object
     */
    public static function getByTitle($title)
    {
        global $_TABLES;

        $Page = new self;
        $title = DB_escapeString($title);
        $sql = "SELECT * FROM {$_TABLES['external']} WHERE title='$title'";
        $res = DB_query($sql,1);
        if ($res && DB_numRows($res) > 0) {
            $A = DB_fetchArray($res, false);
            $Page->setVars($A);
        }
        return $Page;
    }


    /**
     * Update the hit counter.
     */
    public function updateHits()
    {
        global $_TABLES;

        $sql = "UPDATE {$_TABLES['external']} SET
            hits = hits+1
            WHERE exid={$A['exid']}";
        DB_query($sql,1);
    }


    /**
     * Public API function.
     * This is the public API function that's called directly by the external
     * page. It checks access for the page, or adds a new record.
     *
     * @param   string    $page   Page to check
     * @return  boolean           True if has access, False otherwise
     */
    public static function checkAccess($page)
    {
        global $_TABLES, $_CONF, $_CONF_EXP, $_USER;

        // Assume user has no access to this page
        $retval = false;

        $Page = self::getByTitle($page);
        if ($Page->getID() > 0) {
            $access = $Page->getAccess();
            if ($access == 0) {
                // No access, return false without updating hit counter
                $retval = false;
            } else {
                // Has access, update the hit counter and return true
                $Page->updateHits();
                $retval = true;
            }
        } else {
            // Page is not in the database, assume access is OK.
            $Page->setUrl(
                $_SERVER['REQUEST_SCHEME'] . '://' .
                $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']
            )
            ->setHits(1)
            ->setTitle($page)
            ->Save();
            $retval = true;
        }
        return $retval;
    }


    /**
     * Get all pages.
     *
     * @return  array   Array of Page objects
     */
    public static function getAll()
    {
        global $_TABLES;

        $retval = array();
        $res = DB_query(
            "SELECT * FROM {$_TABLES['external']} " .
            COM_getPermSQL('WHERE',0,2) .
            " ORDER BY title ASC"
        );
        while ($A = DB_fetchArray($res, false)) {
            $retval[] = new self($A);
        }
        return $retval;
    }

}

?>
