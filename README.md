# External Pages plugin for glFusion
Copyright 2009 - 2018 by Lee Garner  lee@leegarner.com

Based on the External Pages plugin for Geeklog 1.3.6, version 1.0.
Copyright 2002 by Tom Willett tom2@pigsty.net

The External Pages Plugin allows you to include external pages in
the glFusion security model, search and statistics tracking.  This is done by
adding a few lines of code to the top of your external pages.

Features:
* External Pages can participate fully in glFusion security and search functions.
* Access to external pages is recorded in a hit counter and External pages are added to the glFusion
  Statistics Page.
* External Pages that have the code added to them are automatically added to the system the first time they
  are accessed.
* Templates are provided for creating pages that contain php or php and html and for pages that contain
  the glFusion header and footer and those that do not.

At the least each External Page must contain the following code at the top of the page.
For your convenience, some templates are included in the `private/plugins/exterla/externalpages_templates` directory.

```php
// Change to the location of your lib-common.php
require_once('lib-common.php');

// $page_name is a unique ID for this page. The default is the name of the script, but
// you can use another value, e.g.:
// $page_name = $_SERVER['REQUEST_SCHEME'] . '://' .
//            $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
$page_name = substr($_SERVER['SCRIPT_NAME'],1);
if (!EXP_externalAccess($page_name)) {
    echo COM_404();
    exit;
}
```
If your page is to be wrapped in the glFusion site, add a call to `echo COM_siteHeader()` in the above code,
and add a call to `echo COM_siteFooter();` at the end of the content. Check the example templates for details.

Notes:
* The title field (`$page_name` in the above example) is used as the key
for looking up pages or creating new database entries. This should be unique.
* For each External Page the plugin creates a database record that contains the name of the page,
  a title used to identify it, a hit counter, and the security info.
* A page is automatically added if it is called and is not already in the system. The administrator
  can edit the record that is created to change the security.
* For a normal page just the url to the page would be used, for a php page that has dynamic content
  then the full url should be used (e.g. `http://www.your.site/custompage.php`). If the full url is used then
  the php is executed and a search would be performed on the result.
* You can use this with a pure html page, but it would have to be converted to a php page to handle
  the header. See the phphtml.php template. Alternatively you can configure your
web server to render all HTML pages using PHP, which entails a performance hit.
