<?php
/***************************************************************************
*
*     copyright            : (C) 2013 AQB Soft
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

bx_import('BxDolModule');
bx_import('BxDolAdminSettings');

class AqbProfileMP3PlayerModule extends BxDolModule {
	/**
	 * Constructor
	 */
	function __construct($aModule) {
	    parent::__construct($aModule);
	}

	function getSettingsForm() {
		$iCat = $this->_oDb->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name`='aqb_profile_mp3_player'");
        $oSettings = new BxDolAdminSettings($iCat, BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri().'admin/');
        return $oSettings->getForm();
	}

	function saveSettings() {
		$iId = (int)$this->_oDb->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name`='aqb_profile_mp3_player'");
	    $oSettings = new BxDolAdminSettings($iId);
	    $oSettings->saveChanges($_POST);
	}

	function serviceGetProfileBlock($iProfileID) {
		$aProfile = getProfileInfo(intval($iProfileID));

		$iAlbumToPlay = $aProfile['AqbMP3PlayerPlayAlbumID'];
		$aMP3Files = $this->_oDb->getFilesOfAlbum($aProfile['ID'], $iAlbumToPlay);

		$iViewer = getLoggedId();

		return $this->_oTemplate->getMP3Player($aProfile['ID'], $iAlbumToPlay, $aMP3Files, $aProfile['AqbMP3PlayerAutoPlay'], $aProfile['ID'] == $iViewer);
	}

	function actionSaveSettings() {
		$iProfileID = getLoggedId();
		if (!$iProfileID) die('Must be logged in');

		$iAlbumID = intval($_REQUEST['aqb_mp3player_album']);
		$bAutoplay = $_REQUEST['aqb_mp3player_autoplay'] ? 1 : 0;

		if ($iAlbumID && !$this->_oDb->checkAlbumAuthor($iProfileID, $iAlbumID)) die('Not an album owner');

		$aProfile = getProfileInfo($iProfileID);

		$this->_oDb->saveSettings($iProfileID, $iAlbumID, $bAutoplay);

		$aProfile['AqbMP3PlayerPlayAlbumID'] = $iAlbumID;
		$aProfile['AqbMP3PlayerAutoPlay'] = $bAutoplay;

		$aFiles = $this->_oDb->getFilesOfAlbum($aProfile['ID'], $aProfile['AqbMP3PlayerPlayAlbumID']);

		return $this->_oTemplate->getPlayerObject($iProfileID, $aFiles, $aProfile['AqbMP3PlayerAutoPlay']);
	}

	function actionGetFile($iFileID) {
		$sFile = BX_DIRECTORY_PATH_ROOT."flash/modules/mp3/files/".$iFileID.".mp3";
		$sType = "audio/mp3";

		if(!empty($iFileID) && file_exists($sFile)) {
		    require_once(BX_DIRECTORY_PATH_ROOT."flash/modules/global/inc/functions.inc.php");
		    smartReadFile($sFile, $iFileID.".mp3", $sType);
		}
		exit;
	}

	function actionPopoutPlayer($iProfileID) {
		$iProfileID = intval($iProfileID);
		$aProfile = getProfileInfo($iProfileID);

		$iAlbumToPlay = $aProfile['AqbMP3PlayerPlayAlbumID'];
		$aMP3Files = $this->_oDb->getFilesOfAlbum($aProfile['ID'], $iAlbumToPlay);

		if (empty($aMP3Files)) return MsgBox(_t('_Empty'));

		return $this->_oTemplate->getMP3PlayerEmbed($iProfileID, $aMP3Files, $aProfile['AqbMP3PlayerAutoPlay']);
	}

	function actionSaveOrder() {
		if (!$_POST['order']) return;

		$iProfileID = getLoggedId();

		if (!$iProfileID) return;

		$aProfile = getProfileInfo($iProfileID);

		$iAlbumToPlay = $aProfile['AqbMP3PlayerPlayAlbumID'];
		$aMP3Files = $this->_oDb->getFilesOfAlbum($aProfile['ID'], $iAlbumToPlay, true);

		$aOrder = array();
		foreach ($_POST['order'] as $iMediaID) {
			$iMediaID = intval($iMediaID);
			if (!$aMP3Files[$iMediaID]) continue;
			$aOrder[] = $iMediaID;
		}

		$sOrder = serialize($aOrder);
		$this->_oDb->saveOrder($iProfileID, $sOrder);
	}
}