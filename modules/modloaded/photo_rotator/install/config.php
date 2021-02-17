<?
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.modloaded.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@modloaded.com
***************************************************************************/

$aConfig = array(
	/**
	 * Main Section.
	 */
	'title' => 'Photo Rotator',
    'version' => '1.1.0',
	'vendor' => 'Modloaded',
	'update_url' => '',
	
	'compatible_with' => array(
        '7.1.0', '7.1.1', '7.1.2', '7.1.3', '7.1.4', '7.1.5', '7.1.6', '7.1.7', '7.1.8', '7.1.9','7.2.1'
    ),

    /**
	 * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
	 */
	'home_dir' => 'modloaded/photo_rotator/',
	'home_uri' => 'photo_rotator',
	
	'db_prefix' => 'ml_photo_rotator_',
	'class_prefix' => 'MlPhotoR',
	/**
	 * Installation/Uninstallation Section.
	 */
    'install' => array(
        'check_dependencies' => 1,
		'show_introduction' => 0,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_main_menu' => 1,
		'recompile_member_menu' => 0,
        'recompile_site_stats' => 1,		
		'recompile_page_builder' => 1,
		'recompile_profile_fields' => 0,
		'recompile_comments' => 1,
		'recompile_member_actions' => 1,
		'recompile_tags' => 1,
		'recompile_votes' => 1,
		'recompile_categories' => 1,
		'clear_db_cache' => 1,
		'recompile_injections' => 0,
		'recompile_permalinks' => 1,
		'recompile_alerts' => 1,
        'clear_db_cache' => 1,
		'show_conclusion' => 1,
	),
    'uninstall' => array (
        'check_dependencies' => 0,
		'show_introduction' => 0,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_main_menu' => 1,
		'recompile_member_menu' => 0,
		'recompile_site_stats' => 1,
		'recompile_page_builder' => 1,
		'recompile_profile_fields' => 0,
		'recompile_comments' => 1,
		'recompile_member_actions' => 1,
		'recompile_tags' => 1,
		'recompile_votes' => 1,
		'recompile_categories' => 1,
		'clear_db_cache' => 1,
		'recompile_injections' => 0,
		'recompile_permalinks' => 1,
		'recompile_alerts' => 1,
        'clear_db_cache' => 1,
		'show_conclusion' => 1,
    ),

	/**
	 * Dependencies Section
	 */
    'dependencies' => array(
	),

	/**
	 * Category for language keys.
	 */
	'language_category' => 'Modloaded Photo Rotator',
    'install_permissions' => array(
	
    ), 
	/**
	 * Permissions Section
	 */
	//'install_permissions' => array(),
    'uninstall_permissions' => array(),

	/**
	 * Introduction and Conclusion Section.
	 */
	'install_info' => array(
		'introduction' => '',
		'conclusion' => 'inst_concl.html'
	),
	'uninstall_info' => array(
		'introduction' => '',
		'conclusion' => 'uninst_concl.html'
	)
);
?>
