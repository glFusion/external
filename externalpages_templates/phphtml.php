<?php
/**
*   Non-integrated PHP template for the External Pages Plugin.
*   This template creates a PHP page which will register itself with 
*   the Exteral Pages plugin, and increment the hit count, but will *not*
*   include any glFusion display elements.

*   @author     Lee Garner <lee@leegarner.com>
*   @author     Tom Willett <tomw@pigstye.net>
*   @copyright  Copyright (c) 2009 Lee Garner <lee@leegarner.com>
*   @copyright  Copyright (c) 2002 Tom Willett <tomw@pigstye.net>
*   @package    external
*   @version    0.1
*   @license    http://opensource.org/licenses/gpl-2.0.php 
*               GNU Public License v2 or later
*   @filesource
*/

/** Make sure this points to your lib-common.php wherever you have this page.
*   Include the full filesystem path, if needed.
*   i.e. /var/sites/glfusion/public_html/lib-common.php
*/
require_once 'lib-common.php';

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<?php
/** You should change the "$page_name" to the actual file name
*   or full URL here.  
*   If this page is outside of your glFusion webroot, then use the 
*   full URL or you'll have to edit it in glFusion later.
*/
$page_name = substr($_SERVER['SCRIPT_NAME'], 1);

if (!EXP_externalAccess($page_name)) {
    $display = "<title>{$LANG_EX00['access_denied']}</title></head>\n<body>\n";
    $display .= '<div align="center"><b>' . 
                $LANG_EX00['access_msg'] . 
                '</b></div>';
    $display .= "</body></html>";
    echo $display;
    exit;
}

// Here is a generic header and footer you can modify it to suit your purposes
?>  
<title>My Non-Geeklog Page</title>
</head><body>

<center><b>Put your HTML or combined HTML and PHP code here</b></center>

</body></html>
