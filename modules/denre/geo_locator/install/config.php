<?php

$aConfig = array(
	/**
	 * Main Section.
	 */	
	'title' => 'GEO Locator', // module title, this name will be displayed in the modules list
	'version' => '2.3.0', // module version, change this number everytime you publish your mod
	'vendor' => 'Denre', // vendor name, also it is a folder name in modules folder
	'update_url' => 'http://www.boonex.com/market/update_ckeck?product=geo-locator', // url to get info about available module updates
	
	'compatible_with' => array( // module compatibility
		'7.4.x'  // it tells that the module can be installed on Dolphin 7.1.x and above.
	),

	/**
	 * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
	 */
	'home_dir' => 'denre/geo_locator/', // folder where module files are located, it describes path from /modules/ folder.
	'home_uri' => 'geo_locator', // module URI, so the module will be accessable via the following urls: m/bloggie/ or modules/?r=bloggie/
	
	'db_prefix' => 'db_geo_', // database prefix for all module tables, it is better to compose it from vendor prefix + module prefix, in out case it is me (vendor prefix) and blgg(Bloggie module prefix)
	'class_prefix' => 'DbGeo', // class prefix for all module classes, it is better to compose it from vendor prefix + module prefix, in out case it is Me (vendor prefix) and Blgg(Bloggie module prefix)

	/**
	 * Installation instructions, for complete list refer to BxDolInstaller Dolphin class
	 */
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
	'language_category' => 'db_geo_loc',

    /**
     * Dependencies Section
     */
    'dependencies' => array(
        'wmap' => 'BoonEx WorldMap Module',
    ),

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
