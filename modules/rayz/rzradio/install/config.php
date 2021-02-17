<?php
/**
 * @version 1.0
 * @copyright Copyright (C) 2014 rayzzz.com. All rights reserved.
 * @license GNU/GPL2, see LICENSE.txt
 * @website http://rayzzz.com
 * @twitter @rayzzzcom
 * @email rayzexpert@gmail.com
 */ 
$sFile = dirname(__FILE__) . '/../include/init.php';
if(file_exists($sFile))
	require_once($sFile);
else
	die("Init file is not found");

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => RzradioInit::$aRzInfo['title'],
    'version' => '1.0.0',
    'vendor' => 'Rayz',
    'update_url' => '',

    'compatible_with' => array(
        '7.0.0',
        '7.0.1',
        '7.0.2',
        '7.0.3',
        '7.0.4',
        '7.0.5',
        '7.0.6',
        '7.0.7',
        '7.0.8',
        '7.0.9',
        '7.1.0',
        '7.1.1',
        '7.1.2',
        '7.1.3',
        '7.1.4',
        '7.1.5',
        '7.1.6',
        '7.2.0',
        '7.2.1'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'rayz/rzradio/',
    'home_uri' => 'rzradio',

    'db_prefix' => 'rzradio_',
    'class_prefix' => 'Rzradio',
    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
        'show_introduction' => 1,
		'execute_sql_queries' => 1,
        'change_permissions' => 0,
        'execute_sql' => 0,
        'update_languages' => 1,
        'recompile_global_paramaters' => 1,
        'recompile_main_menu' => 1,
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
        'recompile_injections' => 0,
        'recompile_permalinks' => 1,
        'recompile_alerts' => 0,
        'show_conclusion' => 1
    ),
    'uninstall' => array (
        'show_introduction' => 1,
		'execute_sql_queries' => 1,
        'change_permissions' => 0,
        'execute_sql' => 0,
        'update_languages' => 1,
        'recompile_global_paramaters' => 1,
        'recompile_main_menu' => 1,
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
        'recompile_injections' => 0,
        'recompile_permalinks' => 1,
        'recompile_alerts' => 0,
        'show_conclusion' => 1
    ),
    /**
     * Category for language keys.
     */
    'language_category' => RzradioInit::$aRzInfo['title'],
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
