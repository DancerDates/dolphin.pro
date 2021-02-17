<?php
/***************************************************************************
* Date				: Sunday January 26, 2014
* Copywrite			: (c) 2014 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Cron Monitor
* Product Version	: 1.0.2
*
* IMPORTANT: This is a commercial product made by Dean J. Bassett Jr.
* and cannot be modified other than personal use.
*
* This product cannot be redistributed for free or a fee without written
* permission from Dean J. Bassett Jr.
*
***************************************************************************/

$aConfig = array(
	'title' => 'Cron Monitor',
    'version' => '1.0.2',
	'vendor' => 'Deano',
	'update_url' => '',

	'compatible_with' => array(
        '7.0.x',
        '7.1.x',
        '7.2.x',
    ),

	'home_dir' => 'deano/cron_monitor/',
	'home_uri' => 'cron_monitor',

	'db_prefix' => 'deano_cron_monitor_',
    'class_prefix' => 'deanoCronMonitor',

	'install' => array(
		'check_requirements' => 0,
		'check_dependencies' => 0,
		'show_introduction' => 1,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_main_menu' => 1,
		'recompile_member_menu' => 0,
		'recompile_site_stats' => 0,
		'recompile_page_builder' => 1,
		'recompile_profile_fields' => 1,
		'recompile_comments' => 0,
		'recompile_member_actions' => 1,
		'recompile_tags' => 0,
		'recompile_votes' => 0,
		'recompile_categories' => 0,
		'recompile_search' => 0,
		'recompile_browse' => 0,
		'recompile_injections' => 1,
		'recompile_permalinks' => 1,
		'recompile_alerts' => 1,
		'show_conclusion' => 1,
		'recompile_global_paramaters' => 1,
		'clear_db_cache'  => 1,
	),
	'uninstall' => array (
		'check_requirements' => 0,
		'check_dependencies' => 0,
		'show_introduction' => 1,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_main_menu' => 1,
		'recompile_member_menu' => 0,
		'recompile_site_stats' => 0,
		'recompile_page_builder' => 1,
		'recompile_profile_fields' => 1,
		'recompile_comments' => 0,
		'recompile_member_actions' => 1,
		'recompile_tags' => 0,
		'recompile_votes' => 0,
		'recompile_categories' => 0,
		'recompile_search' => 0,
		'recompile_browse' => 0,
		'recompile_injections' => 1,
		'recompile_permalinks' => 1,
		'recompile_alerts' => 1,
		'show_conclusion' => 1,
		'recompile_global_paramaters' => 1,
		'clear_db_cache'  => 1,
    ),

	'language_category' => 'Deano - Cron Monitor',

	'install_permissions' => array(),
    'uninstall_permissions' => array(),

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