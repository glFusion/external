<?php
/**
 * This is the Polish language page for the External Pages Plug-in.
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
 *
 * Based on the External Pages Plugin 1.0 for Geeklog 1.3.6
 * by Tom Willett.  Updated for glFusion 1.1.5 by Lee Garner
 */

$LANG_EX00 = array (
    'results'           => 'Wyniki',
    'version'           => 'Wersja',
    'noextern'          => 'Wygl�da na to, �e w serwisie nia ma �adnych stron external albo nikt ich jeszcze nie ogl�da�.',
    'topten'            => 'Dziesi�� Najpopularniejszych',
    'save'              => 'Zapisz',
    'delete'            => 'Usu�',
    'cancel'            => 'Anuluj',
    'exidmsg'           => 'EXID',
    'titlemsg'          => 'Tytu�',
    'urlmsg'            => 'URL',
    'hitsmsg'           => 'Ods�on',
    'info'              => 'Info',
    'addnew'            => 'Dodaj Now�',
    'adminhome'         => 'Centrum Admina',
    'pageno'            => 'Strona #',
    'pageurl'           => 'URL strony',
    'external'          => 'External',
    'externpages'       => 'Strony Zewn�trzne',
    'plugin'            => 'Plugin',
	'access_denied'     => 'Brak Dost�pu',
	'access_msg'        => 'Nie masz uprawnie� do ogl�dania tej strony',
	'access_denied_msg' => 'Tylko Uprawnieni U�ytkownicy maj� Dost�p do tej Strony. Tw�j login i adres IP zosta�y zarejestrowane.',
	'admin'		        => 'Plugin Administrator',
	'install_header'	=> 'Instaluj/Odinstaluj Plugin',
	'installed'         => 'Plugin zosta� Zainstalowany',
	'uninstalled'       => 'Plugin nie jest Zainstalowany',
	'install_success'	=> 'Instalacja Zako�czona Pomy�lnie',
	'install_failed'	=> 'Instalacja Nie Powiod�a Si� -- Sprawd� przyczyn� w error logu.',
	'uninstall_msg'		=> 'Plugin zosta� Pomy�lnie Odinstalowany',
	'install'           => 'Instaluj',
	'uninstall'         => 'Odinstaluj',
    'warning'           => 'Uwaga! Plugin jest W��czony',
    'enabled'           => 'Wy��cz plugin przed odinstalowaniem.',
    'notavail'          => 'Niedostepne',
);

// Localization of the Admin Configuration UI
$LANG_configsubgroups['external'] = array(
    'sg_main' => 'Ustawienia g��wne',
);

$LANG_fs['external'] = array(
    'fs_main' => 'Ustawienia Og�lne',
    'fs_permissions' => 'Domy�lne Uprawnienia',
);

$LANG_configsections['external'] = array(
    'label' => 'Strony Zewn�trzne',
    'title' => 'Konfiguracja Stron Zewn�trznych',
);

$LANG_confignames['external'] = array(
    'show' => 'Poka� list� stron zewn�trznych u�ytkownik�w',
    'defgrp' => 'Domy�lna',
    'defuser' => 'Default User',
    'default_permissions' => 'Domy�lne uprawnienia',
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['external'] = array(
    0 => array('Prawdziwy' => 1, 'Fa�szywy' => 0),
    12 => array('Brak dost�pu' => 0, 'Tylko do odczytu' => 2, 'Odczyt Zapis' => 3),
);


?>
