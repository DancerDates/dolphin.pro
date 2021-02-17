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

bx_import('BxDolModuleDb');
bx_import('BxDolAlbums');

class AqbProfileMP3PlayerDb extends BxDolModuleDb {
	/*
	 * Constructor.
	 */
	function __construct(&$oConfig) {
		parent::__construct();
	}

	function getFilesOfAlbum($iProfileID, $iAlbumID, $bWithKey = false) {
		$oAlbums = new BxDolAlbums('bx_sounds', $iProfileID);
		$aFiles = $oAlbums->getAlbumObjList($iAlbumID);

		if ($aFiles) {
			$sIds = implode(', ', $aFiles);
			$aTempResult = $this->getAllWithKey("SELECT `ID`, `Title` FROM `RayMp3Files` WHERE `ID` IN ({$sIds}) AND `Status` = 'approved'", 'ID');
			if ($aTempResult) {
				$aProfile = getProfileInfo($iProfileID);

				$aResult = array();
				if ($aProfile['AqbMP3PlayerOrder']) {
					$aOrder = unserialize($aProfile['AqbMP3PlayerOrder']);
					foreach ($aOrder as $iMediaID) {
						if (!$aTempResult[$iMediaID]) continue;
						if ($bWithKey)
							$aResult[$iMediaID] = $aTempResult[$iMediaID];
						else
							$aResult[] = $aTempResult[$iMediaID];
						$aTempResult[$iMediaID]['added'] = 1;
					}

					foreach ($aTempResult as $aItem) {
						if (!$aItem['added']) {
							if ($bWithKey)
								$aResult[$aItem['ID']] = $aItem;
							else
								$aResult[] = $aItem;
						}
					}
				} else {
					$aResult = !$bWithKey ? array_values($aTempResult) : $aTempResult;
				}

				return $aResult;
			}
		} else return false;
	}

	function checkAlbumAuthor($iProfileID, $iAlbumID) {
		return $this->getOne("SELECT COUNT(*) FROM `sys_albums` WHERE `ID` = {$iAlbumID} AND `Owner` = {$iProfileID} LIMIT 1");
	}

	function saveSettings($iProfileID, $iAlbumID, $bAutoplay) {
		$this->query("UPDATE `Profiles` SET `AqbMP3PlayerPlayAlbumID` = {$iAlbumID}, `AqbMP3PlayerAutoPlay` = {$bAutoplay} WHERE `ID` = {$iProfileID} LIMIT 1");
		createUserDataFile($iProfileID);
	}

	function saveOrder($iProfileID, $sOrder) {
		$sOrder = addslashes($sOrder);
		$this->query("UPDATE `Profiles` SET `AqbMP3PlayerOrder` = '{$sOrder}' WHERE `ID` = {$iProfileID} LIMIT 1");
		createUserDataFile($iProfileID);
	}
}
