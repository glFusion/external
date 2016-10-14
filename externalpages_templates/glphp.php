<?php
/**
*   Integrated PHP template for the External Pages Plugin.
*   This template creates an HTML page which will register itself with 
*   the Exteral Pages plugin, increment the hit count, and take on the
*   look and feel of the glFusion site.
*
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

// $page_name is a unique ID for this page. The default is the name of the script, but
// you can use another value, e.g.:
// $page_name = $_SERVER['REQUEST_SCHEME'] . '://' .
//            $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
$page_name = substr($_SERVER['SCRIPT_NAME'], 1);

if (!EXP_externalAccess($page_name)) {
    echo COM_404();
    exit;
}
  
// Use the menu line if you want left blocks or the none line 
// for no left blocks
$display = COM_siteHeader('menu');
// $display = COM_siteHeader('none');

$display .= "<center><b>Put your PHP code here</b></center>";
$display .= "<p>Everything you want displayed add to the display variable like this</p>";

// Use the first line for right Blocks
// Use the second for no right blocks
$display .= COM_siteFooter(true);
// $display .= COM_siteFooter();
echo $display;
?>
