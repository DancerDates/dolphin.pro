<?php
/**
 * @version 1.0
 * @copyright Copyright (C) 2014 rayzzz.com. All rights reserved.
 * @license GNU/GPL2, see LICENSE.txt
 * @website http://rayzzz.com
 * @twitter @rayzzzcom
 * @email rayzexpert@gmail.com
 */
require_once( BX_DIRECTORY_PATH_MODULES . $aModule['path'] . 'classes/' . $aModule['class_prefix'] . 'Module.php');

global $_page;
global $_page_cont;

$iId = (int) $_COOKIE['memberID'];
$iIndex = 57;

$_page['name_index']	= $iIndex;
$_page['css_name']		= '';

$_page['header'] = _t('_rzradio_page_caption');
$_page['header_text'] = _t('_rzradio_box_caption', $site['title']);

$sClassName = $aModule['class_prefix'] . 'Module';
$oModule = new $sClassName($aModule);
$_page_cont[$iIndex]['page_main_code'] = $oModule->getContent($iId);

PageCode($oModule->_oTemplate);
