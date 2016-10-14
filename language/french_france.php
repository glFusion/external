<?php
/**
*   This is the French language page for the External Pages Plug-in.
*   @author     Lee Garner <lee@leegarner.com>
*   @author     Tom Willett <tomw@pigstye.net>
*   @copyright  Copyright (c) 2009-2016 Lee Garner <lee@leegarner.com>
*   @copyright  Copyright (c) 2002 Tom Willett <tomw@pigstye.net>
*   @package    external
*   @version    1.0.2
*   @license    http://opensource.org/licenses/gpl-2.0.php 
*               GNU Public License v2 or later
*   @filesource
*
*   Based on the External Pages Plugin 1.0 for Geeklog 1.3.6
*   by Tom Willett.  Updated for glFusion 1.1.5 by Lee Garner
*/

$LANG_EX00 = array (
    'results'           => ' Résultats',
    'version'           => 'Version',
    'noextern'          => 'Il ne semble pas avoir de pages externes sur ce site, ou personne ne les a visualisé.',
    'topten'            => 'Top 10 ',
    'save'              => 'Sauvegarder',
    'delete'            => 'Supprimer',
    'cancel'            => 'Annuler',
    'exidmsg'           => 'EXID',
    'titlemsg'          => 'Titre',
    'urlmsg'            => 'URL',
    'hitsmsg'           => 'Affichages',
    'info'              => 'Info',
    'addnew'            => 'Nouvelle Page',
    'adminhome'         => 'Administration',
    'pageno'            => 'Page #',
    'pageurl'           => 'URL de la Page',
    'external'          => 'Externe',
    'externpages'       => 'Pages Externes',
    'plugin'            => 'Plugin',
    'access_denied'     => 'Accès Refusé',
    'access_msg'        => 'Vous n\'avez pas la permission d\'accéder à cette page',
    'access_denied_msg' => 'Seuls les Utilisateurs Root y ont accès.  Votre nom d\'utilisateur et adresse IP ont été enregistrés.',
    'admin'             => 'Administration Plugin',
    'install_header'    => 'Installer/Désinstaller Plugin',
    'installed'         => 'Le Plugin est Installé',
    'uninstalled'       => 'Le Plugin n\'est pas Installé',
    'install_success'   => 'Installation avec succès',
    'install_failed'    => 'Echec de l\'Installation -- Voyez votre fichier d\'erreur pour plus d\'informations.',
    'uninstall_msg'     => 'Plugin Désinstallé avec succès',
    'install'           => 'Installer',
    'uninstall'         => 'Désinstaller',
    'warning'           => 'Attention! Le Plugin est encore Activé',
    'enabled'           => 'Désactivez le plugin avant de déinstaller.',
    'notavail'          => 'Non disponible'    
);

// Localization of the Admin Configuration UI
$LANG_configsubgroups['external'] = array(
    'sg_main' => 'Paramètres Principaux'
);

$LANG_fs['external'] = array(
    'fs_main' => 'Paramètres Eénéraux',
    'fs_permissions' => 'Autorisations par défaut',
);

$LANG_configsections['external'] = array(
    'label' => 'Pages Externes',
    'title' => 'Configuration des Pages Externes',
);

$LANG_confignames['external'] = array(
    'show' => 'Afficher la liste des pages externes aux usagers',
    'defgrp' => 'Groupe par défaut',
    'defuser' => 'Default User',
    'default_permissions' => 'D\'autorisations par défaut',
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['external'] = array(
    0 => array('Vrai' => 1, 'Faux' => 0),
    12 => array('Pas d\'accès' => 0, 'Seuls Lire' => 2, 'Lire Ecrire' => 3)
);


?>
