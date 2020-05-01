<?php
/**
 * Integrated PHP-HTML template for the External Pages Plugin.
 * Use this template for HTML content that is to be rendered within
 * the glFusion framework.  This requires that your web server be able
 * to execute PHP within HTML pages.
 *
 * @author     Lee Garner <lee@leegarner.com>
 * @author     Tom Willett <tomw@pigstye.net>
 * @copyright  Copyright (c) 2009 Lee Garner <lee@leegarner.com>
 * @copyright  Copyright (c) 2002 Tom Willett <tomw@pigstye.net>
 * @package    external
 * @version    0.1
 * @license    http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */

/**
 * Make sure this points to your lib-common.php wherever you have this page.
 * Include the full filesystem path, if needed.
 * e.g. /var/sites/glfusion/public_html/lib-common.php
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

// Use the menu line if you want left blocks or the none line for no
// left blocks
echo COM_siteHeader('menu');
//echo COM_siteHeader('none');
?>

<center><b>Put your HTML or combined HTML and PHP code here</b></center>

<?php
// Use the first line for right Blocks
// Use the second for no right blocks
echo COM_siteFooter(true);
// echo COM_siteFooter();
?>
