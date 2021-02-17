<?php
/***************************************************************************
*
*     copyright            : (C) 2015 AQB Soft
*     website              : http://www.aqbsoft.com
*
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
*
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
*
* This notice may not be removed from the source code.
*
***************************************************************************/

$aConfig = array(
	/**
	 * Main Section.
	 */
	'title' => 'Profile Photo Picker',
	'version' => '1.1.0',
	'vendor' => 'AQB Soft',
	'update_url' => '',

	'compatible_with' => array(
        '7.x.x'
    ),

	/**
	 * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
	 */
	'home_dir' => 'aqb/profile_photo_picker/',
	'home_uri' => 'aqb_profile_photo_picker',

	'db_prefix' => 'aqb_profile_photo_picker_',
	'class_prefix' => 'AqbProfilePhotoPicker',
	/**
	 * Installation/Uninstallation Section.
	 */
	'install' => array(
    	'check_dependencies' => 1,
		'show_introduction' => 1,
		'change_permissions' => 0,
		'execute_sql' => 1,
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
	'uninstall' => array (
		'check_dependencies' => 0,
		'show_introduction' => 0,
		'change_permissions' => 0,
		'execute_sql' => 1,
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
	'dependencies' => array(
		'photos' => 'Boonex photos module',
	),
	/**
	 * Category for language keys.
	 */
	'language_category' => 'AQB Profile Photo Picker',
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
		'conclusion' => $GLOBALS['site']['ver'] == '7.2' ? 'inst_concl_72.html' : 'inst_concl.html',
	),
	'uninstall_info' => array(
		'introduction' => 'uninst_intro.html',
		'conclusion' => $GLOBALS['site']['ver'] == '7.2' ? 'uninst_concl_72.html' : 'uninst_concl.html',
	)
);
?>