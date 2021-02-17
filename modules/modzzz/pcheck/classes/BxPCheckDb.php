<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Confession
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolTwigModuleDb');
bx_import('BxPhotosSearch');
bx_import('BxDolAlerts');

/*
 *  module Data
 */
class BxPCheckDb extends BxDolTwigModuleDb {	

	var $_oConfig;

	/*
	 * Constructor.
	 */
	function __construct(&$oConfig) {
        parent::__construct($oConfig);
		$this->_oConfig = $oConfig;
  
		$this->_sTableFans = '';      
	}
 
	function isPointsAwarded($iMemberId, $sUnit, $sAction) { 
	 
		$iMemberId = (int)$iMemberId;
    
		$iActionId = (int)$this->getOne("SELECT `id` FROM `modzzz_point_main` WHERE `unit`='$sUnit' AND `action`='$sAction' AND `active`=1 LIMIT 1"); 
 
		return (int)$this->getOne("SELECT `action_id` FROM `modzzz_point_history` WHERE `action_id`=$iActionId AND `member_id`=$iMemberId LIMIT 1"); 
	}
 
	function processProfiles() {
 
        $oEmailTemplate = new BxDolEmailTemplates();

  		$iNotifyCount = (int)getParam('modzzz_pcheck_notify_count');

		$this->query("UPDATE `" . $this->_sPrefix . "cron` SET `day`=`day`+1"); 
  
		$iNotifyDays = (int)getParam('modzzz_pcheck_notify_days');

		$iProgress = (int)$this->getOne("SELECT `day` FROM `" . $this->_sPrefix . "cron` LIMIT 1"); 
 
		if($iProgress < $iNotifyDays) 
			return;
		else
			$this->query("UPDATE `" . $this->_sPrefix . "cron` SET `day`=0"); 
 
		if(getParam('modzzz_pcheck_notify_complete') == 'on'){

 			$aNoFillProfiles = $this->getPairs ("SELECT `ID` FROM `Profiles` WHERE `Status` = 'Active' AND `modzzz_pcheck_filled`<100 AND `modzzz_pcheck_complete_notify` < $iNotifyCount", 'ID', 'ID');
 
			foreach ($aNoFillProfiles as $iKey=>$iProfileId){

				$aProfile = getProfileInfo($iProfileId);
				$sRecipientEmail = $aProfile['Email'];
	  
				$aTemplateVars = array (
					'NickName' => getNickName($iProfileId),
					'ProfileUpdateUrl' => BX_DOL_URL_ROOT .  'pedit.php?ID='.$iProfileId,
 				);
 
				$aTemplate = $oEmailTemplate->parseTemplate('modzzz_pcheck_incomplete', $aTemplateVars, $iProfileId);
 
				$this->queueMessage(trim($sRecipientEmail), $aTemplate['subject'], $aTemplate['body']); 

				$this->updateProfileCompleteNotify($iProfileId);
			} 
		}
 
		if(getParam('modzzz_pcheck_notify_photo') == 'on'){
			$aAllProfiles = $this->getPairs ("SELECT `ID` FROM `Profiles` WHERE `Status` = 'Active' AND `Avatar`=0", 'ID', 'ID');

			$sDefaultAlbumName = process_db_input(getParam('bx_photos_profile_album_name'));

			if($iNotifyCount){
				$sCheckNotifyCount = " AND `Profiles`.`modzzz_pcheck_photo_notify` < $iNotifyCount ";
			}

			$aPhotoProfiles = $this->getPairs ("
				SELECT DISTINCT `bx_photos_main`.`Owner`
				FROM `Profiles`
				INNER JOIN `bx_photos_main` ON `bx_photos_main`.`Owner` = `Profiles`.`ID`
				INNER JOIN `sys_albums_objects` ON `sys_albums_objects`.`id_object` = `bx_photos_main`.`ID`
				INNER JOIN `sys_albums` ON `sys_albums`.`ID` = `sys_albums_objects`.`id_album`
				WHERE 
				`sys_albums`.`Type` = 'bx_photos'
				AND `bx_photos_main`.`Status` = 'approved'
				AND `Profiles`.`Status` = 'Active'
				AND `Profiles`.`Avatar`=0
			    {$sCheckNotifyCount}
				AND `sys_albums`.`Caption` = REPLACE('{$sDefaultAlbumName}', '{nickname}', `Profiles`.`NickName`)  
				", 'Owner', 'Owner'); 
 
			$aNoPhotoProfiles = array_diff($aAllProfiles, $aPhotoProfiles);
	   
 
			$oAvatar = BxDolModule::getInstance('BxAvaModule');
			if($oAvatar)
				$sUploadAvatarUrl = BX_DOL_URL_ROOT . $oAvatar -> _oConfig -> getBaseUri();
			 
			$oPhotoModule = BxDolModule::getInstance('BxPhotosModule'); 
			bx_import('Search', $oPhotoModule->_aModule); 
			$oPhoto = new BxPhotosSearch();

			foreach ($aNoPhotoProfiles as $iKey=>$iProfileId){

				$aProfile = getProfileInfo($iProfileId);
				$sRecipientEmail = $aProfile['Email'];
	   
				$sOwner = getUsername($iProfileId);
				$sDefaultAlbumName = process_db_input(getParam('bx_photos_profile_album_name')); 
				$sCaption = str_replace('{nickname}', $sOwner, $sDefaultAlbumName); 
 
				if($oPhoto)
					$sUploadPhotoUrl = $oPhoto->getCurrentUrl('album', 0, uriFilter($sCaption)) . '/owner/' . $sOwner;
 
				$sUploadAvatarUrl = ($sUploadAvatarUrl) ? $sUploadAvatarUrl : $sUploadPhotoUrl;
				$sUploadPhotoUrl = ($sUploadPhotoUrl) ? $sUploadPhotoUrl : $sUploadAvatarUrl;
 

				$aTemplateVars = array (
					'PhotoUploadUrl' => $sUploadPhotoUrl,
					'AvatarUrl' => $sUploadAvatarUrl
 				);
 
				$aTemplate = $oEmailTemplate->parseTemplate('modzzz_pcheck_no_photo', $aTemplateVars, $iProfileId);
 
				$this->queueMessage(trim($sRecipientEmail), $aTemplate['subject'], $aTemplate['body']); 

				$this->updateProfilePhotoNotify($iProfileId);
			}
		}
	}
 
 	function queueMessage($sEmail, $sSubject, $sMessage){
		$this->query("INSERT INTO `sys_sbs_queue`(`email`, `subject`, `body`) VALUES('" . $sEmail . "', '" . process_db_input($sSubject) . "', '" . process_db_input($sMessage) . "')"); 
	}
 
	function hasProfilePhoto($iProfileId) {
		$iProfileId = (int)$iProfileId;

		$sDefaultAlbumName = process_db_input(getParam('bx_photos_profile_album_name'));

		return (int)$this->getOne ("
			SELECT `bx_photos_main`.`Owner`
			FROM `Profiles`
			INNER JOIN `bx_photos_main` ON `bx_photos_main`.`Owner` = `Profiles`.`ID`
			INNER JOIN `sys_albums_objects` ON `sys_albums_objects`.`id_object` = `bx_photos_main`.`ID`
			INNER JOIN `sys_albums` ON `sys_albums`.`ID` = `sys_albums_objects`.`id_album`
			WHERE 
			`sys_albums`.`Type` = 'bx_photos'
			AND `bx_photos_main`.`Status` = 'approved'
			AND `Profiles`.`Status` = 'Active'
			AND `sys_albums`.`Caption` = REPLACE('{$sDefaultAlbumName}', '{nickname}', `Profiles`.`NickName`) 
			AND `bx_photos_main`.`Owner` = $iProfileId 
			LIMIT 1
			"); 
	}

	function getNextEmptyProfileField($iProfileId) {
	
		$iProfileId = (int)$iProfileId;

		$aFields = $this->getAll("SELECT mpf.`Name`, mpf.`Weight`, mpf.`Active` FROM `sys_profile_fields` spf INNER JOIN `modzzz_pcheck_profile_fields` mpf ON spf.`Name`=mpf.`Name` WHERE ((spf.`ViewMembBlock` != 0 AND spf.`Type` != 'system') OR spf.`Name`='Avatar') AND spf.`Mandatory`=0 AND mpf.`Active`=1 ORDER BY mpf.`id` ASC");
 
		foreach($aFields as $sKey => $aFieldName) { 
		 
			if($aFieldName['Name'] == 'Avatar'){
				$sValue = $this->getFieldValue($iProfileId, $aFieldName['Name']);
				if(!$sValue)
					$sValue = $this->hasProfilePhoto($iProfileId);
			}else{
				$sValue = $this->getFieldValue($iProfileId, $aFieldName['Name']);
			}
 
			if(!$sValue) return array('weight'=>$aFieldName['Weight'], 'name'=>$aFieldName['Name']); 
		}
	  
		return array();
	}

	function getProfileFillPercent($iProfileId) {
	
		$iProfileId = (int)$iProfileId;

		$aFields = $this->getAll("SELECT mpf.`Name`, mpf.`Weight`, mpf.`Active` FROM `sys_profile_fields` spf INNER JOIN `modzzz_pcheck_profile_fields` mpf ON spf.`Name`=mpf.`Name` WHERE ((spf.`ViewMembBlock` != 0 AND spf.`Type` != 'system') OR spf.`Name`='Avatar') AND spf.`Mandatory`=0 AND mpf.`Active`=1");

		$iPercent = (int)getParam('modzzz_pcheck_default_percent');
		foreach($aFields as $sKey => $aFieldName) { 

			if($aFieldName['Name'] == 'Avatar'){
				$sValue = $this->getFieldValue($iProfileId, $aFieldName['Name']);
				if(!$sValue)
					$sValue = $this->hasProfilePhoto($iProfileId);
			}else{
				$sValue = $this->getFieldValue($iProfileId, $aFieldName['Name']);
			}

			if($sValue) {
				$iPercent += $aFieldName['Weight'];
			}
		}
	  
		return $iPercent;
	}

	function getProfileFields() {
 
		return $this->getAll("SELECT mpf.`id`, mpf.`Name`, mpf.`Weight`, mpf.`Active` FROM `sys_profile_fields` spf INNER JOIN `modzzz_pcheck_profile_fields` mpf ON spf.`Name`=mpf.`Name` WHERE ((spf.`ViewMembBlock` != 0 AND spf.`Type` != 'system') OR spf.`Name`='Avatar') AND spf.`Mandatory`=0"); 
	}
 
	function getFieldValue($iProfileId, $sField) {
	
		$sField = process_db_input($sField, BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION);
		$iProfileId = (int) $iProfileId;

		return $this->getOne("SELECT `{$sField}` FROM `Profiles` WHERE `ID` = $iProfileId");
	}

	function getProfiles($iLastProfileId, $iLimit, $iUpdateTime) {
	
		$iLastProfileId = (int) $iLastProfileId;
		$iLimit = (int) $iLimit;
		$iUpdateTime = (int) $iUpdateTime;

		$sQuery = "SELECT `ID`, `Email`, `Avatar` FROM `Profiles` WHERE 
			`Status` = 'Active' AND `ID` > {$iLastProfileId} 
			AND (UNIX_TIMESTAMP() - `pcheck_notify`) >= {$iUpdateTime} LIMIT {$iLimit}";

		return $this->getAll($sQuery);
	}
 
	function updateProfileFillPercent($iProfileId, $iFilled=0) {
	 
		$iProfileId = (int)$iProfileId;
 		$this->query("UPDATE `Profiles` SET `modzzz_pcheck_filled` = $iFilled WHERE `ID` = $iProfileId");
		
		if($iFilled){
			$oAlert = new BxDolAlerts("modzzz_pcheck", 'complete', $iProfileId, $iProfileId, array('Status' => $sStatus));
			$oAlert->alert();

			bx_import('BxDolModuleDb'); 
			$oModuleDb = new BxDolModuleDb();
			if( $oModuleDb->isModule('point') ){ 
				$oPoint = BxDolModule::getInstance('BxPointModule'); 
				if( !$this->isPointsAwarded($iProfileId, "modzzz_pcheck", "complete") )
					$oPoint->assignPoints($iProfileId, "modzzz_pcheck", "complete", "add", 0);   
			} 
		}
	}

	function updateProfilePhotoNotify($iProfileId) {
	 
		$iProfileId = (int) $iProfileId;
 		$this->query("UPDATE `Profiles` SET `modzzz_pcheck_photo_notify` = `modzzz_pcheck_photo_notify` + 1 WHERE `ID` = $iProfileId");
	}

	function updateProfileCompleteNotify($iProfileId) {
	 
		$iProfileId = (int) $iProfileId;
 		$this->query("UPDATE `Profiles` SET `modzzz_pcheck_complete_notify` = `modzzz_pcheck_complete_notify` + 1 WHERE `ID` = $iProfileId");
	}
 
	function initialize () { 

		if (!$this->isFieldExists('Profiles', 'modzzz_pcheck_filled')) {   
			$this->query("ALTER TABLE `Profiles` ADD `modzzz_pcheck_filled` int(10) unsigned NOT NULL default '0'"); 
		} 
		if (!$this->isFieldExists('Profiles', 'modzzz_pcheck_photo_notify')) {   
			$this->query("ALTER TABLE `Profiles` ADD `modzzz_pcheck_photo_notify` int(10) unsigned NOT NULL default '0'"); 
		}  
		if (!$this->isFieldExists('Profiles', 'modzzz_pcheck_complete_notify')) {   
			$this->query("ALTER TABLE `Profiles` ADD `modzzz_pcheck_complete_notify` int(10) unsigned NOT NULL default '0'"); 
		} 
	}
 
	function cleanup () { 

		if ($this->isFieldExists('Profiles', 'modzzz_pcheck_filled')) {   
			$this->query("ALTER TABLE `Profiles` DROP `modzzz_pcheck_filled`"); 
		} 		
		if ($this->isFieldExists('Profiles', 'modzzz_pcheck_photo_notify')) {   
			$this->query("ALTER TABLE `Profiles` DROP `modzzz_pcheck_photo_notify`"); 
		} 		
		if ($this->isFieldExists('Profiles', 'modzzz_pcheck_complete_notify')) {   
			$this->query("ALTER TABLE `Profiles` DROP `modzzz_pcheck_complete_notify`"); 
		} 
	}
 
	function initializeProfiles(){
  
		$aAllProfiles = $this->getAll ("SELECT `ID` FROM `Profiles` WHERE `Status` = 'Active'");

		foreach ($aAllProfiles as $aProfile){
			$iPercent = $this->getProfileFillPercent($aProfile['ID']);

			$iFilled = ($iPercent >= 100) ? 1 : 0;
			$this->updateProfileFillPercent($iProfileId, $iFilled); 
		}

		//remove cron job
		$this->query("DELETE FROM `sys_cron_jobs` WHERE `name` = 'modzzz_pcheck_init_cron'");

        $GLOBALS['MySQL']->cleanCache('sys_cron_jobs');  
	}

	function initializeUpdateCron(){

		$this->query("DELETE FROM `sys_cron_jobs` WHERE `name` = 'modzzz_pcheck_init_cron'");

		$this->query("INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
	                 ('modzzz_pcheck_init_cron', '* * * * *', 'BxPCheckInitCron', 'modules/modzzz/pcheck/classes/BxPCheckInitCron.php', '')");
        
		$GLOBALS['MySQL']->cleanCache('sys_cron_jobs');  
	}

	function updateProfileTable(){
 
		$aFields = array();
		$aRows = $this->getAll("SELECT `Name` FROM `modzzz_pcheck_profile_fields`"); 
		foreach($aRows as $aEachRow) { 
			$aFields[] = $aEachRow['Name'];
		}
		
		$sFields = implode("','", $aFields);

		$aCheckRows = $this->getAll("SELECT `Name` FROM `sys_profile_fields` WHERE `ViewMembBlock` != 0 AND `Type` != 'system'  AND `Name` NOT IN ('$sFields') AND `Name`!='Avatar'"); 

		foreach($aCheckRows as $aEachCheckRow) {  
			$sName = $aEachCheckRow['Name']; 
			$this->query("INSERT INTO `modzzz_pcheck_profile_fields` (`Name`, `Active`) VALUES ('$sName', 0)"); 
		}

	}



}