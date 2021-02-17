<?
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
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
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

$aConfig = array(
	/**
	 * Main Section.
	 */	
	'title' => 'DbAdverts', // module title, this name will be displayed in the modules list
	'version' => '1.1.1', // module version, change this number everytime you publish your mod
	'vendor' => 'Denre', // vendor name, also it is a folder name in modules folder
	'update_url' => 'http://www.boonex.com/market/update_ckeck?product=simple-ads-manager', // url to get info about available module updates
	
	'compatible_with' => array( // module compatibility
        '7.x.x'  // it tells that the module can be installed on Dolphin 7.0.0 only.
    ),

    /**
	 * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
	 */
	'home_dir' => 'denre/dbadverts/',
	'home_uri' => 'dbadverts',
	'db_prefix' => 'db_adverts_',
        'class_prefix' => 'DbAdverts',

        'install' => array(
            'show_introduction' => 1,
            'check_dependencies' => 1,
            'execute_sql' => 1,
            'recompile_page_builder' => 1,
            'update_languages' => 1, // add languages
            'recompile_global_paramaters' => 1,
            'recompile_alerts' => 1,
            'recompile_injections' => 1,
            'clear_db_cache' => 1,
            'show_conclusion' => 1,
        ),
        /**
         * Uninstallation instructions, for complete list refer to BxDolInstaller Dolphin class
         */
        'uninstall' => array (
            'check_dependencies' => 1,
            'execute_sql' => 1,
            'recompile_page_builder' => 1,
            'update_languages' => 1, // remove added languages
            'recompile_alerts' => 1,
            'recompile_injections' => 1,
            'clear_db_cache' => 1,
            'show_conclusion' => 1,
        ),

	/**
	 * Category for language keys, all language keys will be places to this category, but it is still good practive to name each language key with module prefix, to avoid conflicts with other mods.
	 */
	'language_category' => 'DbAdvert',

	/**
	 * Permissions Section, list all permissions here which need to be changed before install and after uninstall, see examples in other BoonEx modules
	 */
	'install_permissions' => array(),
    'uninstall_permissions' => array(),

	/**
	 * Introduction and Conclusion Section, reclare files with info here, see examples in other BoonEx modules
	 */
	'install_info' => array(
		'introduction' => '',
		'conclusion' => ''
	),
	'uninstall_info' => array(
		'introduction' => '',
		'conclusion' => ''
	)
);

?>
