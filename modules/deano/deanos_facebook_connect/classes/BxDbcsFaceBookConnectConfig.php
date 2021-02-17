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

    require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolConfig.php');

    class BxDbcsFaceBookConnectConfig extends BxDolConfig 
    {
        var $mApiKey;
        var $mApiSecret;
        var $sPageReciver;
        var $sSessionKey;
		var $sCurrentModuleVersion;
		var $sInstalledModuleVersion;
		var $iDefaultPrivacy;
		var $bDebugEnabled;
		var $sFacebookSessionUid = 'facebook_session';

        /**
    	 * Class constructor;
    	 */
    	function BxDbcsFaceBookConnectConfig($aModule) 
        {
    	    parent::BxDolConfig($aModule);

            $this -> mApiKey      = getParam('dbcs_facebook_connect_api_key'); 
            $this -> mApiSecret   = getParam('dbcs_facebook_connect_secret_key');

            $this -> sPageReciver = BX_DOL_URL_ROOT . $this -> getBaseUri() . 'login';
            $this -> sSessionKey  = md5( time() );
			//$this -> aFacebookParams = array('scope' => 'email,user_hometown,user_birthday,user_about_me,user_interests,user_likes,user_location,user_photos');
			$this -> aFacebookParams = array('scope' => 'email,user_hometown,user_birthday,user_about_me,user_interests,user_likes,user_photos,user_friends,user_relationships');

			// Default privacy setting for accounts default photo album.
			$this -> iDefaultPrivacy = 3;
			// Debug Mode.
			$this -> bDebugEnabled = true;
        }

		function getMembershipLevels() {
			$aMemberships = getMemberships();
			$sLevels = 'Dolphins Default';
			foreach($aMemberships as $iID => $sValue) {
				if($iID > 3) {
					$aData = getMembershipInfo($iID);
					if($aData['Active'] == 'yes') {
						$sLevels .= ',' . $sValue;
					}
				}
			}
			// Update the membership facebook connect option.
			$sLevels = process_db_input($sLevels, BX_TAGS_STRIP, BX_SLASHES_NO_ACTION);
			db_res("UPDATE `sys_options` SET `AvailableValues` = '$sLevels' WHERE `Name` = 'dbcs_facebook_connect_default_membership'");
			// Clear options cache
			@unlink(BX_DIRECTORY_PATH_DBCACHE . 'sys_options*.php');
		}

		function debugLog($sLine, $sMessage) {
			$sDebugFile = BX_DIRECTORY_PATH_MODULES . 'deano/deanos_facebook_connect/backup/debug.log';
			file_put_contents($sDebugFile, date("m-d-Y h:m:s") . ' - ' . $sLine . ' - ' . $sMessage . "\n",  FILE_APPEND);
		}

    }
?>