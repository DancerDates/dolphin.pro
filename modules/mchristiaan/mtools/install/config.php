<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by MChristiaan and cannot be modified for other than personal usage. 
* This product cannot be redistributed for free or a fee without written permission from MChristiaan. 
* This notice may not be removed from the source code.
*
***************************************************************************/

$aConfig = array(
	/**
	 * Main Section.
	 */	
	'title' => 'MTools Page Editor', // module title, this name will be displayed in the modules list
    'version' => '1.0.1', // module version, change this number everytime you publish your mod
	'vendor' => 'M.Christiaan', // vendor name, also it is a folder name in modules folder
	'update_url' => '', // url to get info about available module updates
	
	'compatible_with' => array( // module compatibility
        '7.x.x'  // it tells that the module can be installed on Dolphin 7.0.0 only.
    ),

    /**
	 * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
	 */
	'home_dir' => 'mchristiaan/mtools/', // folder where module files are located, it describes path from /modules/ folder.
	'home_uri' => 'mtools', // module URI, so the module will be accessable via the following urls: m/mtools/ or modules/?r=mtools/
	
	'db_prefix' => 'sys_localization_', // database prefix for all module tables, it is better to compose it from vendor prefix + module prefix
    'class_prefix' => 'MChrisMTools', // class prefix for all module classes, it is better to compose it from vendor prefix + module prefix

	/**
	 * Installation/Uninstallation Section.
	 */
	'install' => array(
		'show_introduction' => 1,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_global_paramaters' => 1,
		'recompile_main_menu' => 0,
		'recompile_member_menu' => 0,
		'recompile_site_stats' => 0,
		'recompile_page_builder' => 0,
		'recompile_profile_fields' => 0,
		'recompile_comments' => 0,
		'recompile_member_actions' => 0,
		'recompile_tags' => 0,
		'recompile_votes' => 0,
		'recompile_categories' => 0,
		'recompile_search' => 0,
		'recompile_injections' => 1,
		'recompile_permalinks' => 0,
		'recompile_alerts' => 0,
		'clear_db_cache' => 0,
		'show_conclusion' => 1
	),
	'uninstall' => array (
		'show_introduction' => 1,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_global_paramaters' => 1,
		'recompile_main_menu' => 0,
		'recompile_member_menu' => 0,
		'recompile_site_stats' => 0,
		'recompile_page_builder' => 0,
		'recompile_profile_fields' => 0,
		'recompile_comments' => 0,
		'recompile_member_actions' => 0,
		'recompile_tags' => 0,
		'recompile_votes' => 0,
		'recompile_categories' => 0,
		'recompile_search' => 0,
		'recompile_injections' => 1,
		'recompile_permalinks' => 0,
		'recompile_alerts' => 0,
		'clear_db_cache' => 0,
		'show_conclusion' => 1
	),
	/**
	* Dependencies Section
	*/
	'dependencies' => array(),
	/**

	/**
	 * Category for language keys, all language keys will be places to this category, but it is still good practive to name each language key with module prefix, to avoid conflicts with other mods.
	 */
	'language_category' => 'MChrisMTools',

	/**
	 * Permissions Section, list all permissions here which need to be changed before install and after uninstall, see examples in other BoonEx modules
	 */
	'install_permissions' => array(),
    'uninstall_permissions' => array(),

	/**
	 * Introduction and Conclusion Section, reclare files with info here, see examples in other BoonEx modules
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
