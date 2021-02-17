<?php
require_once(BX_DIRECTORY_PATH_INC . 'admin_design.inc.php');

bx_import('Module', $aModule);

global $_page;
global $_page_cont;

$iIndex = 9;
$_page['name_index'] = $iIndex;
$_page['header'] = _t('_aqb_profile_mp3_player');

if(!@isAdmin()) {
    send_headers_page_changed();
	login_form("", 1);
	exit;
}

$oModule = new AqbProfileMP3PlayerModule($aModule);

//--- Process actions ---//
if (isset($_POST['save'])) {
	$oModule->saveSettings();
}
//--- Process actions ---//

$_page_cont[$iIndex]['page_main_code'] = DesignBoxAdmin(_t('_aqb_profile_mp3_player_settings'), $oModule->getSettingsForm(), '', '', 11);

PageCodeAdmin();
?>