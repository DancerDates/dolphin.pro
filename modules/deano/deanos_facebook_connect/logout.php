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

ob_start();
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
ob_end_clean();

$_page['name_index'] = 150;
$_page['css_name'] = '';

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = MsgBox(_t('_Please Wait'));
$_page_cont[$_ni]['url_relocate'] = BX_DOL_URL_ROOT . $_GET['redirect'];

send_headers_page_changed();
PageCode();
