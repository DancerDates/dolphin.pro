<?php

/***************************************************************************
*                           
*    copyright            : (C) 2013 aneilaDesign
*    website              : http://www.aneiladesign.com
*	 mod Info             : This file is a Dolphin Module
*
* Licensed under the MIT license
* http://www.opensource.org/licenses/mit-license.php
*
*
* This notice may not be removed from the source code.

***************************************************************************/

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Ands - DesignBox Switcher',
    'version' => '1.0.2',
    'vendor' => 'aneilaDesign',
    'update_url' => 'http://www.boonex.com/aneilaDesign',
    'compatible_with' => array(
    '7.1.x'
    ),
	
	/**
	 * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
	 */
	'home_dir' => 'aneila_mods/designbox_switcher/',
	'home_uri' => 'designbox_switcher',
	
	'db_prefix' => 'designbox_switcher',
	'class_prefix' => 'AndsDesignBoxSwitcher',
	/**
	 * Installation/Uninstallation Section.
	 */

    'install' => array(
        'show_introduction' => 1,
        'change_permissions' => 0,
        'execute_sql' => 0,
        'update_languages' => 1,
        'recompile_global_paramaters' => 1,
        'recompile_main_menu' => 0,
        'recompile_member_menu' => 0,
        'recompile_site_stats' => 0,
        'recompile_page_builder' => 1,
        'recompile_profile_fields' => 0,
        'recompile_comments' => 0,
        'recompile_member_actions' => 0,
        'recompile_tags' => 0,
        'recompile_votes' => 0,
        'recompile_categories' => 0,
        'recompile_search' => 0,
        'recompile_injections' => 0,
        'recompile_permalinks' => 0,
        'recompile_alerts' => 0,
        'clear_db_cache' =>  1,
        'show_conclusion' => 1
    ),
    'uninstall' => array (
        'show_introduction' => 1,
        'change_permissions' => 0,
        'execute_sql' => 0,
        'update_languages' => 1,
        'recompile_global_paramaters' => 1,
        'recompile_main_menu' => 0,
        'recompile_member_menu' => 0,
        'recompile_site_stats' => 0,
        'recompile_page_builder' => 1,
        'recompile_profile_fields' => 0,
        'recompile_comments' => 0,
        'recompile_member_actions' => 0,
        'recompile_tags' => 0,
        'recompile_votes' => 0,
        'recompile_categories' => 0,
        'recompile_search' => 0,
        'recompile_injections' => 0,
        'recompile_permalinks' => 0,
        'recompile_alerts' => 0,
        'clear_db_cache' => 1,
        'show_conclusion' => 1
    ),
    /**
    * Dependencies Section
    */
    'dependencies' => array(),
    /**
     * Category for language keys.
     */
    'language_category' => 'Ands DesignBox Switcher',
    /**
     * Permissions Section
     */
    'install_permissions' => array(),
    'uninstall_permissions' => array(),
    /**
     * Introduction and Conclusion Section.
     */
    'install_info' => array(
        'introduction' => 'inst_intro.html',
        'conclusion' => 'inst_concl.html'
    ),
    'uninstall_info' => array(
        'introduction' => 'uninst_intro.html',
        'conclusion' => 'uninst_concl.html'
    )
);
?>