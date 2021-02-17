<?php
$aConfig = array(
	/**
	 * Main Section.
	 */	
	'title' => '<font color="orange">Who Viewed Me</font>',
    'version' => '7.4.0',
	'vendor' => 'Sandklock',
	'update_url' => '',
	
	'compatible_with' => array(
		'7.4.x'
    ),

    /**
	 * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
	 */
	'home_dir' => 'sandklock/who_viewed_me/',
	'home_uri' => 'who_viewed_me',
	
	'db_prefix' => 'sk_who_viewed_me_',
	'class_prefix' => 'SkWhoViewedMe',
	/**
	 * Installation/Uninstallation Section.
	 */
    'install' => array(
        'check_dependencies' => 0,
		'show_introduction' => 1,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
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
		'recompile_permalinks' => 1,
		'recompile_alerts' => 0,
        'clear_db_cache' => 0,
		'show_conclusion' => 1,
	),
    'uninstall' => array (
        'check_dependencies' => 0,
		'show_introduction' => 1,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
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
		'recompile_permalinks' => 1,
		'recompile_alerts' => 0,
        'clear_db_cache' => 0,
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
	'language_category' => 'Sandklock Who Viewed Me',

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