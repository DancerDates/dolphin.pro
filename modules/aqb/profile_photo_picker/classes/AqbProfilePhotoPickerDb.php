<?php
/***************************************************************************
*
*     copyright            : (C) 2015 AQB Soft
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
bx_import('BxDolEmailTemplates');

class AqbProfilePhotoPickerDb extends BxDolModuleDb {
	/*
	 * Constructor.
	 */
	var $_oConfig;
	function __construct(&$oConfig) {
		parent::__construct();
		$this->_oConfig = $oConfig;
	}

	function getPhotosOfAlbum($iAlbum) {
		return $this->getAll("
			SELECT `ID`, `Title`, `Hash`
			FROM `bx_photos_main`
			JOIN
				(SELECT `id_object`, `obj_order` FROM `sys_albums_objects` WHERE `id_album` = {$iAlbum}) AS `album_objects`
				ON `bx_photos_main`.`ID` = `album_objects`.`id_object`
			ORDER BY `obj_order` ASC
		");
	}

	function checkPhotoExistence($iOwner, $iPhoto) {
		return $this->getOne("SELECT `Hash` FROM `bx_photos_main` WHERE `ID` = {$iPhoto} AND `Owner` = {$iOwner} LIMIT 1");
	}

	function setProfilePhoto($iOwner, $sHash) {
		$this->query("UPDATE `Profiles` SET `aqb_profile_photo_id` = '{$sHash}' WHERE `ID` = {$iOwner} LIMIT 1");
		createUserDataFile($iOwner);
	}
}