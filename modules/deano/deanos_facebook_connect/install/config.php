<?php
/***************************************************************************
* Date				: Feb 21, 2013
* Copywrite			: (c) 2013 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Deanos Facebook Connect
* Product Version	: 4.2.7
*
* IMPORTANT: This is a commercial product made by Dean Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean Bassett Jr.
*
***************************************************************************/
    $aConfig = array(
    	/**
    	 * Main Section.
    	 */
    	'title' => 'Deanos Facebook Connect',
    	'version' => '4.2.7',
    	'vendor' => 'Deano',
    	'update_url' => '',

    	'compatible_with' => array(
            '7.0.x',
			'7.1.x',
			'7.2.x'
        ),

    	/**
    	 * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
    	 */
    	'home_dir' => 'deano/deanos_facebook_connect/',
    	'home_uri' => 'deanos_facebook_connect',
    	
    	'db_prefix' => 'bx_dbcs_facebook_',
    	'class_prefix' => 'BxDbcsFaceBookConnect',
    	/**
    	 * Installation/Uninstallation Section.
    	 */
    	'install' => array(
            'check_requirements' => 1,
            'check_dependencies' => 0,
    		'show_introduction' => 1,
    		'change_permissions' => 1,
    		'execute_sql' => 1,
    		'update_languages' => 1,
    		'recompile_main_menu' => 0,
    		'recompile_member_menu' => 0,
    		'recompile_site_stats' => 0,
    		'recompile_page_builder' => 1,
    		'recompile_profile_fields' => 1,
    		'recompile_comments' => 0,
    		'recompile_member_actions' => 0,
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
            'check_dependencies' => 0,
    		'show_introduction' => 0,
    		'change_permissions' => 0,
    		'execute_sql' => 1,
    		'update_languages' => 1,
    		'recompile_main_menu' => 0,
    		'recompile_member_menu' => 0,
    		'recompile_site_stats' => 0,
    		'recompile_page_builder' => 1,
    		'recompile_profile_fields' => 1,
    		'recompile_comments' => 0,
    		'recompile_member_actions' => 0,
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
        /**
         * Dependencies Section
         */
        'dependencies' => array(
        ),
        /**
    	 * Category for language keys.
    	 */
    	'language_category' => 'Deanos Facebook Connect',
    	/**
    	 * Permissions Section
    	 */
	    'install_permissions' => array(
	        'writable' => array(
	            'backup/',
	        ),
	    ),
    	'uninstall_permissions' => array(),
    	/**
    	 * Introduction and Conclusion Section.
    	 */
    	'install_info' => array(
    		'introduction' => 'inst_intro.html',
    		'conclusion' => 'inst_concl.html'
    	),
    	'uninstall_info' => array(
    		'introduction' => '',
    		'conclusion' => 'uninst_concl.html'
    	)
    );
