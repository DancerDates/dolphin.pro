<?
/***************************************************************************
*                                 GeoDistance
*                              -------------------
*     copyright (C) 2013 Dately
*
*     This is a commercial product made by Dately
*     Do not copy, reproduce, distribute, sell or offer it for sale, publish,
*     display, perform, modify, create derivative works, transmit, or in any
*     way exploit any content of this module without written permission by
*     the author. For each domain you want to install this module you need its own license!
*
*     Email: dolmods@gmail.com
*
***************************************************************************/

$aConfig = array(

	'title' => 'NavigateMe',
	'version' => '1.0.0',
	'vendor' => 'LordJason',
	'update_url' => '',
	'compatible_with' => array(
        	'7.2.x'
	),

	'home_dir' => 'Jason/NavigateMe/',
	'home_uri' => 'navigateme',
	
	'db_prefix' => 'NavigateMe_',
        'class_prefix' => 'JasonNavigateMe',

	'install' => array(
	'check_dependencies' => 1,
        'update_languages' => 1,
	'execute_sql' => 1,
	),

	'uninstall' => array (
	'check_dependencies' => 0,
        'update_languages' => 1,
        'execute_sql' => 1,
        ),

	'language_category' => 'Navigate Me',

	'install_permissions' => array(),
        'uninstall_permissions' => array(),

	'install_info' => array(
		'introduction' => '',
		'conclusion' => ''
	),
	'uninstall_info' => array(
		'introduction' => '',
		'conclusion' => ''
	),

	'dependencies' => array(
	        'wmap' => 'World Map',
        )

);

?>
