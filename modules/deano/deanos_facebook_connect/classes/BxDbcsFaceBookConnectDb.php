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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php');

class BxDbcsFaceBookConnectDb extends BxDolModuleDb
{
    var $_oConfig;
    var $sTablePrefix;
    
    /**
     * Constructor.
     */
    function BxDbcsFaceBookConnectDb(&$oConfig)
    {
        parent::BxDolModuleDb();
        
        $this->_oConfig     = $oConfig;
        $this->sTablePrefix = $oConfig->getDbPrefix();
    }
    
    function getProfileId($iFbUid)
    {
        $iFbUid = (int) $iFbUid;
        $sQuery = "SELECT `ID` FROM `Profiles` WHERE `dbcsFacebookProfile` = '{$iFbUid}' LIMIT 1";
        return $this->getOne($sQuery);
    }
    
    /**
     * Function will create new profile;
     *
     * @param  : (array) $aProfileFields    - `Profiles` table's fields;
     *                   [NickName]    - profile's nick name;
     *                   [Email]       - profile's email;
     *                   [Sex]         - profile's sex;
     *                   [DateOfBirth] - profile's birthday date;
     *                   [Password]    - profile's password;
     *                   [Role]        - profile's role type;
     * @return : (integer)  - profile's Id;
     */
    function createProfile($aProfileFields)
    {
        $sFields   = null;
        $sSalt     = $aProfileFields['Salt'];
        $sPassword = $aProfileFields['Password'];
        // procces all recived fields;
        foreach ($aProfileFields as $sKey => $mValue) {
            $mValue = $this->escape($mValue);
            $sFields .= "`{$sKey}` = '{$mValue}', ";
        }
        
        $sFields = preg_replace('/,$/', '', trim($sFields));
        
        $sQuery = "INSERT IGNORE INTO `Profiles` SET {$sFields}";
        $this->query($sQuery);
        
        $iProfileId = db_last_id();
        
        if ($iProfileId) {
            /*
            // set salt value ;
            $sQuery = 'UPDATE `Profiles` SET `Salt` = CONV(FLOOR(RAND() * 99999999999999), 10, 36) WHERE `ID` = ' . (int) $iProfileId;
            $this -> query($sQuery);
            
            // update password 
            $sQuery = 'UPDATE `Profiles` SET `Password` = SHA1( CONCAT(`Password`, `Salt`) ) WHERE `ID` = ' . (int) $iProfileId;
            $this -> query($sQuery);
            */
            $sQuery = "UPDATE `Profiles` SET `Salt` = '$sSalt' WHERE `ID` = " . (int) $iProfileId;
            $this->query($sQuery);
            
            // update password 
            $sQuery = "UPDATE `Profiles` SET `Password` = '$sPassword' WHERE `ID` = " . (int) $iProfileId;
            $this->query($sQuery);
            
            $this->saveOldNick($iProfileId, $aProfileFields['NickName']);
        }
        return $iProfileId;
    }
    
    /**
     * Function will update  profile's status;
     *
     * @param  : $iProfileId (integer) - profile's Id;
     * @param  : $sStatus    (string)  - profile's status;
     * @return : void;
     */
    function updateProfileStatus($iProfileId, $sStatus)
    {
        $iProfileId = (int) $iProfileId;
        $sQuery     = "UPDATE `Profiles` SET `Status` = '{$sStatus}' WHERE `ID` = {$iProfileId}";
        return $this->query($sQuery);
    }
    
    /**
     * Function will check member profile existing;
     *
     * @return boolean;
     */
    function isRegisteredMember($sNickName)
    {
        $sNickName = $this->escape($sNickName);
        $sQuery    = "SELECT COUNT(*) FROM `Profiles` WHERE `NickName` = '{$sNickName}' LIMIT 1";
        return ($this->getOne($sQuery)) ? true : false;
    }
    function isEmailExist($sEmail)
    {
        $sQuery = "SELECT COUNT(*) FROM `Profiles` WHERE `Email` = '$sEmail' LIMIT 1";
        return ($this->getOne($sQuery)) ? true : false;
    }
    
    function getNickCheck()
    {
        $sQuery = "SELECT `Min`,`Max`,`Check`,`Unique` FROM `sys_profile_fields` WHERE `Name` = 'NickName' LIMIT 1";
        return $this->getRow($sQuery);
    }
    function getPassCheck()
    {
        $sQuery = "SELECT `Min`,`Max`,`Check`,`Unique` FROM `sys_profile_fields` WHERE `Name` = 'Password' LIMIT 1";
        return $this->getRow($sQuery);
    }
    function getEmailCheck()
    {
        $sQuery = "SELECT `Min`,`Max`,`Check`,`Unique` FROM `sys_profile_fields` WHERE `Name` = 'Email' LIMIT 1";
        return $this->getRow($sQuery);
    }
    
    
    /**
     * Function will check field name in 'Profiles` table;
     *   
     * @return : (boolean);   
     */
    function isFieldExist($sFieldName)
    {
        $sFieldName = $this->escape($sFieldName);
        $sQuery     = "SELECT `ID` FROM `sys_profile_fields` WHERE `Name` = '{$sFieldName}' LIMIT 1";
        return ($this->getOne($sQuery)) ? true : false;
    }
    
    /**
     * Function will get the country's ISO code;
     *
     * @param : $sCountry (string) - country name;
     * @return: (string); - country ISO code;
     */
    function getCountryCode($sCountry)
    {
        $sCountry = $this->escape($sCountry);
        $sQuery   = "SELECT `ISO2` FROM `sys_countries` WHERE `Country` = '{$sCountry}' LIMIT 1";
        return $this->getOne($sQuery);
    }
    
    /**
     * Function will return category's id;
     *
     * @param  : $sCatName (string) - catregory's name;
     * @return : (integer) - category's id;
     */
    function getSettingsCategoryId($sCatName)
    {
        $sCatName = $this->escape($sCatName);
        return $this->getOne('SELECT `kateg` FROM `sys_options` WHERE `Name` = "' . $sCatName . '"');
    }
    
    function getFacebookIDs()
    {
        return $this->getAll("SELECT `ID`,`dbcsFacebookProfile` FROM `Profiles` WHERE `dbcsFacebookProfile` != 0");
    }
    
    function getLangCat()
    {
        return $this->getOne("SELECT `ID` FROM `sys_localization_categories` where `Name`='Deanos Facebook Connect'");
    }
    
    function getNextAvaID()
    {
        $result = mysqli_query("SHOW TABLE STATUS LIKE 'bx_avatar_images'");
        $row    = mysqli_fetch_array($result);
        $nextId = $row['Auto_increment'];
        mysqli_free_result($result);
        return $nextId;
    }
    
    function setAvatar($iMemID, $iAvaID)
    {
        $sQuery = "INSERT INTO `bx_avatar_images` (`author_id`) VALUES ('$iMemID')";
        $this->query($sQuery);
        $sQuery = "UPDATE `Profiles` SET `Avatar`='$iAvaID' WHERE `ID`='$iMemID'";
        $this->query($sQuery);
    }
    
    function getNagTime($iMemID)
    {
        return $this->getOne('SELECT `NagTime` FROM `dbcs_facebook_connect_data` WHERE `memberID` = "' . $iMemID . '"');
    }
    
    function updateNagTime($iMemID)
    {
        $iCurTime = time();
        $sQuery   = "INSERT INTO `dbcs_facebook_connect_data` (`memberID`, `NagTime`) VALUES ('$iMemID', '$iCurTime') ON DUPLICATE KEY UPDATE `NagTime`='$iCurTime'";
        $this->query($sQuery);
    }
    
    function getProfileFields()
    {
        return $this->getAll("SELECT `Name` FROM `sys_profile_fields` WHERE `Mandatory` >0 AND `EditOwnBlock` >0");
    }
    
    function getProfileValue($iMemID, $sField)
    {
        $sQuery = "SELECT `" . $sField . "` FROM `Profiles` WHERE `ID`='" . $iMemID . "'";
        return $this->getOne($sQuery);
    }
    
    function saveLogoutURL($iMemID, $sURL)
    {
        if ($sURL == '') {
            // Clearing a url, so do this only on a existing entry.
            $sQuery = "UPDATE `dbcs_facebook_connect_data` SET `LogoutURL`='$sURL' WHERE `memberID`='$iMemID'";
        } else {
            $sQuery = "INSERT INTO `dbcs_facebook_connect_data` (`memberID`, `LogoutURL`) VALUES ('$iMemID', '$sURL') ON DUPLICATE KEY UPDATE `LogoutURL`='$sURL'";
        }
        $this->query($sQuery);
    }
    
    function saveExtraData($iMemID, $sLink, $sUser)
    {
        if ($sLink == '')
            return;
        if ($sUser == '')
            return;
        $sLink  = $this->escape($sLink);
        $sUser  = $this->escape($sUser);
        $sQuery = "INSERT INTO `dbcs_facebook_connect_data` (`memberID`, `FacebookUrl`, `FacebookUserName`) VALUES ('$iMemID', '$sLink', '$sUser') ON DUPLICATE KEY UPDATE `FacebookUrl`='$sLink', `FacebookUserName`='$sUser'";
        $this->query($sQuery);
    }
    
    function deleteExtraData($iMemID)
    {
        $sQuery = "DELETE FROM `dbcs_facebook_connect_data` WHERE `memberID`='$iMemID'";
        $this->query($sQuery);
    }
    
    function getLogoutURL($iMemID)
    {
        $sQuery = "SELECT `LogoutURL` FROM `dbcs_facebook_connect_data` WHERE `memberID`='" . $iMemID . "'";
        return $this->getOne($sQuery);
    }
    
    function saveOldNick($iMemID, $sNick)
    {
        if ($sNick == '')
            return;
        $sQuery = "INSERT INTO `dbcs_facebook_connect_data` (`memberID`, `OldNickName`) VALUES ('$iMemID', '$sNick') ON DUPLICATE KEY UPDATE `OldNickName`='$sNick'";
        $this->query($sQuery);
    }
    
    function getOldNick($iMemID)
    {
        $sQuery = "SELECT `OldNickName` FROM `dbcs_facebook_connect_data` WHERE `memberID`='" . $iMemID . "'";
        return $this->getOne($sQuery);
    }
    
    function setPassword($iMemID, $sPass, $sSalt)
    {
        /*
        $sQuery = "UPDATE `Profiles` SET `Salt` = '$sSalt' WHERE `ID`='$iMemID'";
        $this -> query($sQuery);
        $sQuery = "UPDATE `Profiles` SET `Password` = SHA1(CONCAT(md5('$sPass'), `Salt`)) WHERE `ID`='$iMemID'";
        $this -> query($sQuery);
        */
        $sQuery = "UPDATE `Profiles` SET `Salt` = '$sSalt' WHERE `ID` = " . (int) $iMemID;
        $this->query($sQuery);
        
        // update password 
        $sQuery = "UPDATE `Profiles` SET `Password` = '$sPass' WHERE `ID` = " . (int) $iMemID;
        $this->query($sQuery);
        
    }
    function getPassword($iMemID)
    {
        
        
    }
    function createAlbum($iProfileId, $albumName, $albumURI, $iPrivacy)
    {
        if ($iProfileId > 0) {
            $albumName = $this->escape($albumName);
            $sQuery    = "INSERT IGNORE INTO `sys_albums` (`Caption` ,`Uri` ,`Location` ,`Description` ,`Type` ,`Owner` ,`Status` ,`Date` ,`ObjCount` ,`LastObjId` ,`AllowAlbumView`) VALUES ('$albumName', '$albumURI', 'Undefined', '$albumName', 'bx_photos', '$iProfileId', 'active', UNIX_TIMESTAMP( ) , '0', '0', '$iPrivacy');";
            $this->query($sQuery);
        }
    }
    
    function setPrivacy($sUri, $iPrivacy)
    {
        $sQuery = "UPDATE `sys_albums` SET `AllowAlbumView`='$iPrivacy' WHERE `Uri`='$sUri'";
        $this->query($sQuery);
    }
    
    function deleteEmptyAlbums($iMemID)
    {
        $sQuery = "DELETE FROM `sys_albums` WHERE `Owner` = '$iMemID' AND `ObjCount` = 0 AND `Type` = 'bx_photos'";
        $this->query($sQuery);
    }
    
    function setAlbumDescription($sUri, $sDesc)
    {
        $sDesc  = $this->escape($sDesc);
        $sQuery = "UPDATE `sys_albums` SET `Description`='$sDesc' WHERE `Uri`='$sUri'";
        $this->query($sQuery);
    }
    
    function addFriend($iProfileId, $iAutoFriend)
    {
        $iProfileId  = (int) $iProfileId;
        $iAutoFriend = (int) $iAutoFriend;
        
        $sQuery = "INSERT INTO `sys_friend_list` SET `ID` = '{$iProfileId}', `Profile` = '{$iAutoFriend}', `Check` = 1";
        $this->query($sQuery);
    }
    
    function updateAlbumName($iMemID, $sCaption, $sURI, $sOldCaption)
    {
        $sCaption    = $this->escape($sCaption);
        $sOldCaption = $this->escape($sOldCaption);
        $sQuery      = "UPDATE `sys_albums` SET `Caption`='$sCaption', `Uri`='$sURI' WHERE `Owner`='$iMemID' AND `Caption`='$sOldCaption'";
        $this->query($sQuery);
    }
    
    function checkInvite($iFacebookID)
    {
        // checks the dbcsFBFriendInvites table if it exists for passed facebook id.
        // if found returns facebook else null
        // Only do this if my inviter is installed
        $sQuery = "SELECT `id` FROM `sys_modules` WHERE `title`='FB Friend Inviter' AND `version` > '2.0.0'";
        $iID    = $this->getOne($sQuery);
        if ($iID > 0) {
            $sQuery = "SELECT `FacebookID` FROM `dbcsFBFriendInvites` WHERE `FacebookID`='" . $iFacebookID . "'";
            return $this->getOne($sQuery);
        }
    }
    
    function getMembershipId($sMembershipName)
    {
        $sQuery = "SELECT `ID` FROM `sys_acl_levels` WHERE `Name` = '$sMembershipName'";
        return $this->getOne($sQuery);
    }
    
    function setMembershipLevel($iMemberId, $iMembershipId)
    {
        $aMembership = $this->getRow("SELECT `IDLevel`, `Days` FROM `sys_acl_level_prices` WHERE `IDLevel`='$iMembershipId'");
        if ((int) $aMembership['Days'] == 0) {
            $sQuery = "INSERT INTO `sys_acl_levels_members` (`IDMember`, `IDLevel`, `DateStarts`, `DateExpires`, `TransactionID`) VALUES ('$iMemberId', '$iMembershipId', NOW(), NULL, '')";
        } else {
            $sQuery = "INSERT INTO `sys_acl_levels_members` (`IDMember`, `IDLevel`, `DateStarts`, `DateExpires`, `TransactionID`) VALUES ('$iMemberId', '$iMembershipId', NOW(), DATE_ADD(NOW(), INTERVAL " . $aMembership['Days'] . " DAY), '')";
        }
        $this->query($sQuery);
    }
    
    function getPfByName($sName)
    {
        $sQuery = "SELECT * FROM `sys_profile_fields` WHERE `Name` = '$sName'";
        return $this->getRow($sQuery);
    }
    
    function getSexList()
    {
        $sQuery = "SELECT * FROM `sys_pre_values` WHERE `Key` = 'Sex' ORDER BY `Order`";
        $aList  = $this->getAll($sQuery);
        foreach ($aList as $id => $value) {
            $aRet[] = _t($value['LKey']);
        }
        return $aRet;
    }
    
    function setInjection($sName, $sValue)
    {
        $sQuery = "UPDATE `sys_injections` SET `active` = '$sValue' WHERE `name` = '$sName'";
        $this->query($sQuery);
        
        if ($GLOBALS['site']['ver'] == '7.0' && $GLOBALS['site']['build'] < 3) {
            $sFileName = BX_DIRECTORY_PATH_CACHE . 'sys_injections.inc';
            if (file_exists($sFileName))
                unlink($sFileName);
        } else {
            $files = glob(BX_DIRECTORY_PATH_CACHE . 'db_sys_injections.inc*.php');
            array_map('unlink', $files);
        }
    }
    
    function setPending($iMemID)
    {
        $sQuery = "UPDATE `bx_photos_main` SET `Status` = 'pending' WHERE `Owner`='$iMemID'";
        $this->query($sQuery);
    }
    

    function recompile()
    {
        $iCategoryId = $this->getLangCat();
        $sPath = BX_DIRECTORY_PATH_MODULES . 'deano/deanos_facebook_connect/install/langs/en.php';
        $sQuery = "SELECT * FROM `sys_localization_languages`";
        $aLanguages = $this->getAll($sQuery);
        include($sPath);
		foreach($aLanguages as $aLanguage) {
			if($aLanguage['Name'] != 'en') {
				foreach($aLangContent as $sKey => $sValue) {
					$iLangKeyId = (int)$this->getOne("SELECT `ID` FROM `sys_localization_keys` WHERE `IDCategory`='" . $iCategoryId . "' AND `Key`='" . $sKey . "' LIMIT 1");
					$this->query("INSERT IGNORE INTO `sys_localization_strings`(`IDKey`, `IDLanguage`, `String`) VALUES('" . $iLangKeyId . "', '" . $aLanguage['ID'] . "', '" . addslashes($sValue) . "')");
				}
				compileLanguage($aLanguage['ID']);
			}
		}
    }

    function isProfileTypeSplitter()
    {
        $sQuery = "SELECT COUNT(*) FROM `sys_modules` WHERE `uri` = 'aqb_pts' LIMIT 1";
        return ($this->getOne($sQuery)) ? true : false;
    }
	
	function getActiveProfileTypes() {
        $aPt = $this->getAll("SELECT * FROM `aqb_pts_profile_types` WHERE `Obsolete` = 0");
		foreach($aPt as $id => $value) {
			$a[$value['ID']] = _t('__' . $value['Name']);
		}
		return $a;
	}

}
?>