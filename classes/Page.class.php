<?php
/**
*   Class to handle External Page objects.
*
*   @author     Lee Garner <lee@leegarner.com>
*   @copyright  Copyright (c) 2017-2018 Lee Garner <lee@leegarner.com>
*   @package    external
*   @version    1.0.2
*   @license    http://opensource.org/licenses/gpl-2.0.php
*               GNU Public License v2 or later
*   @filesource
*/
namespace External;

class Page
{
    private $properties = array();


    /**
    *   Constructor
    *   Read a record if an ID value is provided
    *
    *   @param  integer $exid   Optional record ID
    */
    public function __construct($exid=0)
    {
        global $_CONF_EXP;

        if ($exid > 0) {
            $this->Read($exid);
        } else {
            $this->exid = 0;
            $this->title = '';
            $this->url = '';
            $this->hits = 0;
            $this->owner_id = $_CONF_EXP['defuser'];
            $this->group_id = $_CONF_EXP['defgrp'];
            $this->perm_owner = $_CONF_EXP['default_permissions'][0];
            $this->perm_group = $_CONF_EXP['default_permissions'][1];
            $this->perm_members = $_CONF_EXP['default_permissions'][2];
            $this->perm_anon = $_CONF_EXP['default_permissions'][3];
        }
    }
 

    /**
    *   Setter function. Sets the value of a property
    *
    *   @param  string  $key    Name of property
    *   @param  mixed   $value  Value to set
    */
    public function __set($key, $value)
    {
        switch ($key) {
        case 'exid':
        case 'owner_id':
        case 'group_id':
        case 'hits':
        case 'perm_owner':
        case 'perm_group':
        case 'perm_members':
        case 'perm_anon':
            $this->properties[$key] = (int)$value;
            break;
        case 'url':
        case 'title':
            $this->properties[$key] = trim($value);
            break;
        }
    }


    /**
    *   Getter function.
    *   Returns a property value, or NULL if undefined.
    *
    *   @param  string  $key    Name of property
    *   @return mixed       Value of property, or NULL
    */
    public function __get($key)
    {
        if (array_key_exists($key, $this->properties)) {
            return $this->properties[$key];
        } else {
            return NULL;
        }
    }


    /**
    *   Read a single record
    *
    *   @param  integer $exid   Page ID
    */
    public function Read($exid)
    {
        global $_TABLES;

        $exid = (int)$exid;
        if ($exid > 0) {
            $res = DB_query("SELECT * FROM {$_TABLES['external']} WHERE exid=$exid");
            if ($res) {
                $A=DB_fetchArray($res, false);
                $this->setVars($A, true);
            }
        }
    }


    /**
    *   Set the record variables from a DB record or form
    *
    *   @param  array   $A          Array of field values
    *   @param  boolean $from_db    True of reading from the DB, False if a form
    */
    public function setVars($A, $from_db=true)
    {
        foreach ($A as $key=>$value) {
            $this->$key = $value;
        }
        if (!$from_db) {    // revise permissions for form input
            $P = SEC_getPermissionValues($A['perm_owner'], $A['perm_group'],
                $A['perm_members'] ,$A['perm_anon']);
            $this->perm_owner = $P[0];
            $this->perm_group = $P[1];
            $this->perm_members = $P[2];
            $this->perm_anon = $P[3];
        }
    }


    /**
    *   Display the edit form
    *
    *   @return string  HTML for the edit form
    */
    public function Edit()
    {
        global $LANG_EX00, $_CONF_EXP, $LANG_ACCESS, $_TABLES;

        $T = EXP_getTemplate('edit', 'admin');
        $T->set_var(array(
            'header'    => $LANG_EX00['externpages'] . ' ' . $LANG_EX00['admin'],
            'plugin'    => $_CONF_EXP['pi_name'],
            'pi_icon'   => plugin_geticon_external(),
            'exid'      => $this->exid,
            'title'     => $this->title,
            'url'       => $this->url,
            'hits'      => $this->hits,
            'perms'     => SEC_getPermissionsHTML(
                    $this->perm_owner, $this->perm_group,
                    $this->perm_members, $this->perm_anon),
            'owner_dd'  => COM_optionList($_TABLES['users'],"uid,username",$this->owner_id),
            'group_dd'  => COM_optionList($_TABLES['groups'], 'grp_id,grp_name', $this->group_id),
        ) );
        $T->parse('output','admin');
        return $T->finish($T->get_var('output'));
    }


    /**
    *   Save the current object.
    *
    *   @param  array   $A  Optional array of field values
    *   @return boolean     True on success, False on error
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
    *   Delete a single record
    *
    *   @param  integer $exid   Record ID of page
    */
    public static function Delete($exid)
    {
        global $_TABLES;
        DB_delete($_TABLES['external'], 'exid', (int)$exid);
    }

}

?>
