external
========

External Pages plugin for glFusion
August 2009
Lee Garner  lee@leegarner.com

Based on the External Pages plugin for Geeklog 1.3.6, version 1.0.  The
original README file follows

============================================================================

// +---------------------------------------------------------------------------+
// | External Pages Plugin 1.0 for Geeklog - The Ultimate Weblog               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2002 by the following authors:                              |
// |                                                                           |
// | Author:       Tom Willett -- tomw@pigstye.net                             |
// | Constructed with the Universal Plugin                                     |
// | Copyright (C) 2002 by the following authors:                              |
// | Tom Willett                 -    tomw@pigstye.net                         |
// | Blaine Lang                 -    langmail@sympatico.ca                    |
// | The Universal Plugin is based on prior work by:                           |
// | Tony Bibbs                  -    tony@tonybibbs.com                       |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

============================================================================
Geeklog External Pages Plugin
November 2002
Tom Willett   tomw@pigstye.net

The Geeklog External Pages Plugin allows you to include external pages in 
the Geeklog Security, search and statistics tracking.  This is done by 
adding a few lines of code to the top of your external pages.

Features:

* External Pages can participate fully in Geeklog Security.
* External Pages are search-able through Geeklogs Search -- even dynamic pages.
* Access to external pages is recorded in a hit counter and External pages are added to the Geeklog 
  Statistics Page.
* External Pages that have the code added to them are automatically added to the system the first time they
  are accessed.
* Templates are provided for creating pages that contain php or php and html and for pages that contain
  the Geeklog header and footer and those that do not.

At the least each External Page must contain the following code at the top of the page:

----------------------- Start Code ------------------------------
require_once('lib-common.php');
//
// Its best to change the substr($SCRIPT_NAME,1) to the actual file name or 
// relative url here
//
if (!EXP_externalAccess(substr($_SERVER['SCRIPT_NAME'],1))) {
    $display = COM_siteHeader('menu');
    $display .= COM_startBlock($LANG_EX00['access_denied']);
    $display .= '<div align="center"><b>' . 
            $LANG_EX00['access_msg'] . 
            '</b></div>';
    $display .= COM_endBlock();
    $display .= COM_siteFooter(yes);
    echo $display;
    exit;
}
----------------------- End Code --------------------------------

Notes:

* For each External Page the plugin creates a database record that contains the name of the page,
  a title used to identify it, a hit counter, and the security info.
* A page is automatically added if it is called and is not already in the system.  The administrator
  can edit the record that is created to change the title, security and whether or not it is treated
  as dynamic.
* For a normal page just the url to the page would be used, for a php page that has dynamic content
  then the full url should be used (e.g. http://www.your.site/page.php).  If the full url is used then 
  the php is executed and a search would be performed on the result.
* You can use this with a pure html page, but it would have to be converted to a php page to handle
  the header.  See the phphtml.php template.

Enjoy
============================================================================

