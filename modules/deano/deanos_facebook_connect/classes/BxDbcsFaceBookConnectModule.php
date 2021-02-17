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
	@session_start();
    require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );

    bx_import('BxDolModuleDb');
    bx_import('BxDolModule');
    bx_import('BxDolInstallerUtils');

	require_once (BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/include.php');

    class BxDbcsFaceBookConnectModule extends BxDolModule 
    {
        // contain some module information ;
        var $aModuleInfo;

        // contain path for current module;
        var $sModulePath;	
		var $sModuleUrl;
        var $sHomeUrl;
        var $oFacebook;
        var $iFacebookUid;
        var $sAccessToken;

        var $iMemberId;
		var $logoutUrl;
		var $_oFunctions;
		var $sTopMsg;
		var $newNickName;
		var $newPassword;

		var $bAutoAvatar;
		var $bImportAlbums;

		var $sDolphinMajor;
		var $sDolphinMinor;

		var $sSiteDomain;
		var $sBaseDomain;

		var $iPassCount;

		var $fbMeData;
		var $fbLikesData;
		var $fbPermissionsData;

    	function BxDbcsFaceBookConnectModule(&$aModule) 
        {
            parent::BxDolModule($aModule);
			$this -> sDolphinMajor = $GLOBALS['site']['ver'];
			$this -> sDolphinMinor = $GLOBALS['site']['build'];

			// Include my new function class.
			require_once( BX_DIRECTORY_PATH_MODULES . $aModule['path'] . 'classes/BxDbcsFaceBookConnectFunctions.php' );
			$this -> _oFunctions = new BxDbcsFaceBookConnectFunctions($this->_oTemplate);

			// Include the facebook SDK
			require_once( BX_DIRECTORY_PATH_MODULES . $aModule['path'] . '/inc/fbsdk/dbcs_fbc_facebook.php' );
			// Include API for looking up base domain name.
			require_once( BX_DIRECTORY_PATH_MODULES . $aModule['path'] . '/inc/domain/effectiveTLDs.inc.php' );
			require_once( BX_DIRECTORY_PATH_MODULES . $aModule['path'] . '/inc/domain/regDomain.inc.php' );
			$this -> sSiteDomain = $this -> getSiteDomain( );
			$this -> sBaseDomain = $this -> getBaseDomain( $this -> sSiteDomain );

            // prepare the location link ;
            $this -> sModuleUrl  = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();
			$this -> sModulePath = BX_DIRECTORY_PATH_MODULES . $aModule['path'];

            $this -> aModuleInfo    = $aModule;

            $this -> sHomeUrl       = $this ->_oConfig -> _sHomeUrl;
			
			$this -> oFacebook = new dbcs_fbc_Facebook(array(
				'appId'  => (string)$this -> _oConfig -> mApiKey,
				'secret' => (string)$this -> _oConfig -> mApiSecret,
			));

            // define member id;
            $this -> iMemberId = getLoggedId();


			$this -> sTopMsg = '';
			$this -> bImportAlbums = false;
            $this -> _oTemplate -> addCss('face_book_connect.css');
            $this -> _oTemplate -> addJs('dbcsFaceBookConnect.js');
        }

		function actionAdministration($sAction = '', $sSecond = '')
        {
        	$GLOBALS['iAdminPage'] = 1;

            if( !isAdmin() ) {
                header('location: ' . BX_DOL_URL_ROOT);
				exit;
            }

			$this->_oConfig->getMembershipLevels();

			$sb = $_POST['sb'];
			$sr = $_POST['sr'];
			if($sb == 1) {
				$fbIDs = $this->_oDb->getFacebookIDs();
				$this -> sTopMsg = $this -> _oFunctions -> saveBackup($fbIDs);
			}
			if($sr == 1 && $_POST['delete'] == '') {
				$this -> sTopMsg = $this -> _oFunctions -> restoreBackup();
			}
			if($sr == 1 && $_POST['delete'] == 'Delete') {
				$this -> sTopMsg = $this -> _oFunctions -> deleteBackup();
			}

			// get sys_option's category id;
			$iCatId = $this-> _oDb -> getSettingsCategoryId('dbcs_facebook_connect_api_key');
			if(!$iCatId) {
				$sOptions = MsgBox( _t('_Empty') );
			}
			else {
				bx_import('BxDolAdminSettings');

				$oSettings = new BxDolAdminSettings($iCatId);
				
				$mixedResult = '';
				if(isset($_POST['save']) && isset($_POST['cat'])) {
					$mixedResult = $oSettings -> saveChanges($_POST);
				}

				// get option's form;
				$sOptions = $oSettings -> getForm();

				// Add unique ID fields to form.
				$sOptions = str_replace('<tr >','********<tr>',$sOptions);
				$aRows = explode('********', $sOptions);
				$pattern = '/name="(.*?)"/';
				foreach ($aRows as $id => $value) {
					preg_match($pattern, $value, $matches);
					$sOnClickInput = '';
					$sOnClickSelect = '';
					if(strpos($value, 'dbcs_facebook_connect_use_join_form') !== false) $sOnClickInput = ' onclick = "javascript: join_form_click();"';
					if(strpos($value, 'dbcs_facebook_connect_use_popup') !== false) $sOnClickInput = ' onclick = "javascript: use_popup_click();"';
					$value = str_replace('<tr', '<tr id="t_' . $matches[1] . '"', $value);
					$value = str_replace('<input', '<input id="i_' . $matches[1] . '"' . $sOnClickInput, $value);
					$value = str_replace('<select', '<select id="s_' . $matches[1] . '"' . $sOnClickSelect, $value);
					if(strpos($value, 'input_wrapper_submit') === false)
						$value = $this -> str_lreplace('<div class="clear_both"', '<img style = "padding-left: 10px; padding-top: 2px; cursor: help;" src="' . getTemplateIcon('info.gif') . '" onclick="pophelp(\'' . $matches[1] . '\')" title="Click icon for help with this setting."><div class="clear_both"', $value);
					$aRows[$id] = $value;
				}
				$sOptions = implode('', $aRows);

				if($mixedResult !== true && !empty($mixedResult)) {
					$sOptions = $mixedResult . $sOptions;
				}
			}

            $sCssStyles = $this -> _oTemplate -> addCss('forms_adv.css', true);
            $sCssStyles .= $this -> _oTemplate -> addCss('face_book_connect.css', true);
            $sCssStyles .= $this -> _oTemplate -> addJs('dbcsFaceBookConnect.js', true);

			$aTopItems = array(
				'config' => array('href' => $this -> sModuleUrl . 'administration/', 'title' => _t('_dbcs_fbc_mconfig'), 'active' => ($sAction == '' ? 1 : 0)),
				'button_configuration' => array('href' => $this -> sModuleUrl . 'administration/button_config/', 'title' => _t('_dbcs_fbc_bconfig'), 'active' => ($sAction == 'button_config' ? 1 : 0)),
			);


            $this -> _oTemplate-> pageCodeAdminStart();

			echo DesignBoxAdmin( _t('_dbcs_facebook_information')
				, $GLOBALS['oSysTemplate'] -> parseHtmlByName('default_padding.html', array('content' => _t('_dbcs_facebook_information_block'))) );
			echo DesignBoxAdmin( _t('_Settings')
				, $this -> sTopMsg . $GLOBALS['oSysTemplate'] -> parseHtmlByName('default_padding.html', array('content' => $sCssStyles . $sOptions)), $sAction);
			echo DesignBoxAdmin( _t('_dbcs_fbc_Restore')
				, $GLOBALS['oSysTemplate'] -> parseHtmlByName('default_padding.html', array('content' => $this -> _oFunctions -> genRestoreForm()) ));
			echo DesignBoxAdmin( _t('_dbcs_fbc_Backup')
				, $GLOBALS['oSysTemplate'] -> parseHtmlByName('default_padding.html', array('content' => $this -> _oFunctions -> genBackupForm()) ));
			echo '<script>join_form_click(false);</script>';

			if(!$this -> _oDb -> isProfileTypeSplitter()) {
				echo '<script>hide_profile_type();</script>';
			}

            $this -> _oTemplate->pageCodeAdmin( _t('_dbcs_facebook_settings'));
        }

        function actionLoginForm($sOption = '')
        {
			if((int)$_COOKIE['memberID']) {
				header( 'Location: ' . BX_DOL_URL_ROOT);
				exit;
			}

			$sError = $_GET['error'];
			$iErrorCode = (int)$_GET['error_code'];
			if($sError == 'access_denied' && $iErrorCode >= 200) {
				echo $this -> _oTemplate -> getPage('Connect Error', MsgBox('User appears to have cancled permission request dialog.<br><br>Signup aborted.') );
				exit;
			}
			//if($this -> _oConfig -> bDebugEnabled) $this -> _oConfig -> debugLog(__LINE__, 'Finding Nickname');

			$this -> checkRequired( );

			$bLoginOk = $this -> doFbLogin($sOption);

			if ($bLoginOk) {
				// Successful, so delete the pass count cookie.
				setcookie( 'dbcs_pass_count', $this -> iPassCount, time( ) - 120, '/' );
				unset( $_COOKIE['dbcs_pass_count'] );

				$this->logoutUrl = $this -> oFacebook -> getLogoutUrl(array('next' => BX_DOL_URL_ROOT . 'logout.php?action=member_logout'));
				$aFacebookProfileInfo = $this -> _oFunctions -> setFacebookProfileInfo($this -> fbMeData, $this -> fbLikesData);
				$aDolphinProfileInfo  = array();
				// See if this facebook id is already in db.
				$iProfileId = $this -> _oFunctions -> findProfileID($this -> iFacebookUid,$aFacebookProfileInfo['email']);
				if ($iProfileId > 0) {
					// Found, so log member in.
					$aDolphinProfileInfo = getProfileInfo($iProfileId);
					$sRedirect2 = getParam('dbcs_facebook_connect_redirect2');
					$sRedirect2 = str_replace("{memberid}",$iProfileId,$sRedirect2);
					$sRedirect2 = str_replace("{nickname}",$this -> dbGetNickName($iProfileId),$sRedirect2);
					$this ->_oDb ->saveLogoutURL($iProfileId,$this->logoutUrl);
					$this ->_oDb ->saveExtraData($iProfileId, $this -> fbMeData['link'], $this -> fbMeData['username']);

					if(getParam('dbcs_facebook_connect_set_status_active_oc')) {
						if($aDolphinProfileInfo['Status'] == 'Unconfirmed' && $aDolphinProfileInfo['dbcsFacebookProfile'] != '') {
							$this ->_oDb ->updateProfileStatus($iProfileId, 'Active');
							$sCacheFile = BX_DIRECTORY_PATH_DBCACHE . 'user' . $iProfileId . '.php';
							if(file_exists($sCacheFile)) unlink($sCacheFile);
						}
					}

					$sCallbackUrl = BX_DOL_URL_ROOT . $sRedirect2;
					$this -> setLogged($iProfileId, $aDolphinProfileInfo['Password'], $sCallbackUrl);
					$bProfileFound = true;
				} else {
					setcookie('dbcsfbid', $this -> iFacebookUid, 0, '/' );
					$_COOKIE['dbcsfbid'] = $this -> iFacebookUid;

					if(getParam('dbcs_facebook_connect_import_albums')) {
						$meAlbums = $this -> oFacebook -> api('/me/albums/');
						file_put_contents($GLOBALS['dir']['tmp'] . $this -> iFacebookUid . '_albums.tmp', serialize($meAlbums));
						foreach ($meAlbums['data'] as $key => $value) {
							$sAlbumID = $meAlbums['data'][$key]['id'];
							$mePhotos = $this -> oFacebook -> api('/' . $sAlbumID . '/photos/');
							file_put_contents($GLOBALS['dir']['tmp'] . $this -> iFacebookUid . '_photos_' . $sAlbumID . '.tmp', serialize($mePhotos));
						}
					}

					// If no matches found, we need to create a new account.
					// find an available user name to use.
					$this -> newNickName = $this -> _oFunctions -> findNickName($aFacebookProfileInfo['username'], $aFacebookProfileInfo['first_name'],$aFacebookProfileInfo['last_name']);


					// New prompting method.
					$aFacebookProfileInfo['NickName'] = $this -> newNickName;
					$aFacebookProfileInfo['Salt'] = genRndSalt();
					$aFacebookProfileInfo['Password'] = genRndPwd();
					$aFacebookProfileInfo['ClearPassword'] = $aFacebookProfileInfo['Password'];
					$aFacebookProfileInfo['Password'] = encryptUserPwd($aFacebookProfileInfo['ClearPassword'], $aFacebookProfileInfo['Salt']);
					$aFacebookProfileInfo['LogoutUrl'] = $this->logoutUrl;


					// Country lookup by IP address.
					if(getParam('dbcs_facebook_connect_use_geo_ip')) {
						$sIP = $this -> getRealIpAddr();
						$sJsonIpData = bx_file_get_contents('http://ipinfo.io/' . $sIP . '/json');
						$aIpData = json_decode($sJsonIpData);
						$sCountryCode = $aIpData->country;
						if ($sCountryCode == '') $sCountryCode = getParam('dbcs_facebook_connect_dcnty');
					} else {
						$sCountryCode = getParam('dbcs_facebook_connect_dcnty');
					}
					$aFacebookProfileInfo['Country'] = $sCountryCode;



					$dbhometown = explode(",",$aFacebookProfileInfo['hometown']['name']);
					$dblocation = explode(",",$aFacebookProfileInfo['location']['name']);
					$dbcity = trim($dblocation[0]);
					if ($dbcity == '') $dbcity = trim($dbhometown[0]);
					$aFacebookProfileInfo['City'] = $dbcity;

					$dbcsSex = ($aFacebookProfileInfo['sex'] == '') ? 'male' : $aFacebookProfileInfo['sex'];
					$aFacebookProfileInfo['Sex'] = $dbcsSex;

					foreach($this -> fbPermissionsData['data'] as $id => $value) {
						$aFacebookProfileInfo['permissions'][$value['permission']] = $value['status'];
					}

					//print_r($this -> fbMeData);
					//echo '<br>';
					//print_r($aFacebookProfileInfo['permissions']);
					//exit;

					$this -> saveProfileData($aFacebookProfileInfo);
					$this -> promptExtra();
				}
			} 
		}


		function doFbLogin($sOption) {
			// Generate and store the pass count for this connection.
			// Set to expire in 2 minutes
			if( !isset( $_COOKIE['dbcs_pass_count'] ) ) {
				$this -> iPassCount = 1;
				setcookie( 'dbcs_pass_count', $this -> iPassCount, time( ) + 120, '/' );
			}
			else {
				$this -> iPassCount = intval( $_COOKIE['dbcs_pass_count'] );
				$this -> iPassCount++;
				setcookie( 'dbcs_pass_count', $this -> iPassCount, time( ) + 120, '/' );
			}

			if ($this -> iPassCount > 6) {
				echo $this -> _oTemplate -> getPage('Connect Error', MsgBox('Error validating access token. Attempts to auto correct failed.<br>Clear your browsers cookies and cache and try again.') );
				exit;
			}


			$this -> iFacebookUid = $this -> oFacebook -> getUser();
			$this -> sAccessToken = $this -> oFacebook -> getAccessToken();

			// Redirect to standard login if popup is not used.
			$bPopup = (getParam('dbcs_facebook_connect_use_popup') != '' ? true : false);
			if($sOption != 'popup') $bPopup = false;	// Popup param not specified. Popup style button code not used. Shut off popup.
			if(!$bPopup) {
				$sLoginUrl = $this -> oFacebook -> getLoginUrl($this -> _oConfig -> aFacebookParams);
				if (!$this -> iFacebookUid) {
					header( 'Location: ' . $sLoginUrl);
					exit;
				}
			}

			if ($this -> iFacebookUid) {
				$this -> fbMeData = null;
				$this -> fbLikesData = null;
				$this -> fbPermissionsData = null;					
				
				try {
					$this -> fbMeData = $this -> oFacebook -> api('/me');
					$mephoto = $this -> oFacebook -> api('/me/picture?type=large&redirect=false&access_token=' . $this -> sAccessToken);
					$this -> fbMeData['picture'] = $mephoto['data']['url'];
				} catch (dbcs_fbc_FacebookApiException $e) {
					$pos = strpos(strtolower($e), 'validating access token');
					if ($pos > 0) {
						// We have a access token error.
						// Delete cookies, increment pass count and redirect to facebook logon
						$this -> deleteCookies( );
						$this -> deleteOtherCookies( );
						$this -> iPassCount++;
						setcookie( 'dbcs_pass_count', $this -> iPassCount, time( ) + 120, '/' );
						header( 'Location: ' . $sLoginUrl);
						exit;
						//$this -> showError($e, __LINE__);
					} else {
						$this -> showError($e, __LINE__);
						return false;
					}
				}
				try {
					$this -> fbLikesData = $this -> oFacebook -> api('/me/likes/');
				} catch (dbcs_fbc_FacebookApiException $e) {
					$this -> showError($e, __LINE__);
					return false;
				}

				$this -> fbPermissionsData = $this -> oFacebook -> api('/me/permissions');

				return true;
			} else {
				return false;
			}
		}

		function showOneMoment() {
			// This displays a page indicating the profile is being created
			// That page will redirect to create the profile.
			// This page is only shown if promptExtra does not prompt for any
			// additional information and the popup logon method is not used.
			// Page is needed to get around a problem with what i believe is a
			// issue with the php sdk and the session data. Without it, a loop to
			// reauth with facebook occures which prevents this script from properly
			// reaching the point where it prompts to import photos or to show the completed page.
			if(getParam('dbcs_facebook_connect_use_popup')) {
				// Popup logon method being used. This intrim page is not needed, so just create profile.
				$this -> _createProfile();
			} else {
				// Standard logon method used. Show this page.
				$aVars = array(
					'redirect' => $this -> sModuleUrl . 'create_profile/' . session_id(),
					'message' => _t('_dbcs_fbc_onemoment'),
				);
				$sCode = $this->_oTemplate -> parseHtmlByName('onemoment.html', $aVars);
				echo $this -> _oTemplate -> getPage('One Moment', $sCode);
				exit;
			}
		}


		function promptExtra() {

			// This feature not available on dolphin 7.0.0
			// If we are passing to the join form, no need to prompt for anything.
			// so just continue to the next step.
			if (getParam('dbcs_facebook_connect_use_join_form')) {
				$this -> showOneMoment();
			}

			$bProfileTypesSplitter = $this -> _oDb -> isProfileTypeSplitter();

			$aProfileData = $this -> getProfileData();
			//print_r($aProfileData);
			//exit;
			$bAutoPromptNick = false;
			$bAutoPromptEmail = false;

			$aPrompts = array('ProfileType' => false, 'nick' => false, 'pass' => false, 'email' => false, 'sex' => false, 'country' => false, 'city' => false, 'zip' => false);

			if($bProfileTypesSplitter) {
				if(getParam('dbcs_facebook_connect_prompt_profile_type')) $aPrompts['ProfileType'] = true;
			}

			if(getParam('dbcs_facebook_connect_prompt_nick')) $aPrompts['nick'] = true;
			if(getParam('dbcs_facebook_connect_prompt_pass')) $aPrompts['pass'] = true;
			if(getParam('dbcs_facebook_connect_prompt_email')) $aPrompts['email'] = true;
			if(getParam('dbcs_facebook_connect_prompt_sex')) $aPrompts['sex'] = true;
			if(getParam('dbcs_facebook_connect_prompt_dob')) $aPrompts['dob'] = true;
			if(getParam('dbcs_facebook_connect_prompt_country')) $aPrompts['country'] = true;
			if(getParam('dbcs_facebook_connect_prompt_city')) $aPrompts['city'] = true;
			if(getParam('dbcs_facebook_connect_prompt_zip')) $aPrompts['zip'] = true;

			if($aProfileData['NickName'] == 'none') {
				if(getParam('dbcs_facebook_connect_auto_prompt_nick')) {
					$aPrompts['nick'] = true;
					$aPrompts['pass'] = true;
					$bAutoPromptNick = true;
				} else {
					if($aPrompts['nick'] == false) {
						// A free nick name could not be found and admin disabled auto prompt,
						// so abort with nickname exists error.
						$this -> deleteProfileData();
						echo $this -> _oTemplate -> getPage( _t('_dbcs_facebook_error_occured') , MsgBox( _t('_dbcs_facebook_profile_exist', $aProfileData['first_name'])));
						exit;
					}
				}
			}

		if($aProfileData['birthday'] == '') {
			if(getParam('dbcs_facebook_connect_auto_prompt_dob')) {
				$aPrompts['dob'] = true;
				$bAutoPromptDob = true;
			}
		} 

			if(!$this -> isEmailValid($aProfileData['email'])) {
				if(getParam('dbcs_facebook_connect_auto_prompt_email')) {
					$aPrompts['email'] = true;
					$bAutoPromptEmail = true;
				} else {
					if($aPrompts['email'] == false) {
						// Email is not valid, and admin disabled auto prompt, so abort with invalid email error.
						$this -> deleteProfileData();
						echo $this -> _oTemplate -> getPage(_t('_dbcs_facebook_error_occured') , MsgBox(_t('_dbcs_fb_invalid_email', $aProfileData['Email'])));
						exit;
					}
				}
			} 

			if(in_array(true, $aPrompts)) {
				// One of the prompts is on, so generate prompt form and display it.
				if($bProfileTypesSplitter) {
					if($aPrompts['ProfileType'] == true) {
						bx_import('BxDolProfileFields');
						$oProfileFields = new BxDolProfileFields(0);
						//$aProfileTypes = $oProfileFields->convertValues4Input('#!ProfileType');
						$aProfileTypes = $this -> _oDb -> getActiveProfileTypes();
						//asort($aProfileTypes);
					}
				}

				if($aPrompts['country'] == true) {
					bx_import('BxDolProfileFields');
					$oProfileFields = new BxDolProfileFields(0);
					$aCountries = $oProfileFields->convertValues4Input('#!Country');
					asort($aCountries);
				}
				if($aPrompts['sex'] == true) {
					bx_import('BxDolProfileFields');
					$oProfileFields = new BxDolProfileFields(0);
					$aSex = $oProfileFields->convertValues4Input('#!Sex');
					asort($aSex);
				}

				$aForm = array(
					'form_attrs' => array(
						'name'     => 'prompt_form',
						'action'   => $this -> sModuleUrl . 'return_prompt/' . session_id(),
						'method'   => 'post',
		                'onsubmit' => 'return validatePromptForm(this);',
					),
					'inputs' => array(),
				);
				if($bProfileTypesSplitter) {
					if($aPrompts['ProfileType'] == true) {
						$aForm['inputs'][] = array(
							'type' => 'select',
							'name' => 'ProfileType',
							'caption' => _t('_dbcs_fbc_profile_type'), 
							'info' =>	_t('_dbcs_fbc_FieldDesc_Profiletype_Join'),
							'values' => $aProfileTypes,
							'value' => $aProfileData['ProfileType'],
						);
					}
				}
				if($aPrompts['nick'] == true) {
					$aForm['inputs'][] = array(
						'type' => 'text',
						'name' => 'NickName',
						'caption' => _t('_dbcs_fbc_nickname'),
						'info' =>	_t('_dbcs_fbc_FieldDesc_NickName_Join'),
						'value' => $aProfileData['NickName'],
					);
				}
				if($aPrompts['pass'] == true) {
					$aForm['inputs'][] = array(
						'type' => 'password',
						'name' => 'Password',
						'caption' => _t('_dbcs_fbc_password'),
						'info' =>	_t('_dbcs_fbc_FieldDesc_Password_Join'),
						//'value' => $aProfileData['ClearPassword'],
						'value' => '',
					);
					$aForm['inputs'][] = array(
						'type' => 'password',
						'name' => 'Password_confirm[0]',
						'caption' => _t('_dbcs_fbc_confirm_password'),
						'info' =>	_t('_dbcs_fbc_FieldDesc_Password_Join_Confirm'),
						//'value' => $aProfileData['ClearPassword'],
						'value' => '',
					);
				}
				if($aPrompts['email'] == true) {
					$aForm['inputs'][] = array(
						'type' => 'text',
						'name' => 'Email',
						'caption' => _t('_dbcs_fbc_email'),
						'info' =>	_t('_dbcs_fbc_FieldDesc_Email_Join'),
						'value' => $aProfileData['email'],
					);
				}

				if($aPrompts['dob'] == true) {
					$aForm['inputs'][] = array(
						'type' => 'date',
						'name' => 'DateOfBirth',
						'caption' => _t('_dbcs_fbc_dob'),
						'info' =>	_t('_dbcs_fbc_FieldDesc_DateOfBirth_Join'),
						'value' => $aProfileData['DateOfBirth'],
					);
				}

				if($aPrompts['sex'] == true) {
					$aForm['inputs'][] = array(
						'type' => 'radio_set',
						'name' => 'Sex',
						'caption' => _t('_dbcs_fbc_sex'),
						'info' =>	_t('_dbcs_fbc_FieldDesc_Sex_Join'),
						'value' => $aProfileData['Sex'],
						//'values' => $this -> _oDb -> getSexList(),
						'values' => $aSex,
					);
				}

				if($aPrompts['country'] == true) {
					$aForm['inputs'][] = array(
						'type' => 'select',
						'name' => 'Country',
						'caption' => _t('_dbcs_fbc_country'),
						'info' =>	_t('_dbcs_fbc_FieldDesc_Country_Join'),
						'values' => $aCountries,
						'value' => $aProfileData['Country'],
					);
				}

				if($aPrompts['city'] == true) {
					$aForm['inputs'][] = array(
						'type' => 'text',
						'name' => 'City',
						'caption' => _t('_dbcs_fbc_city'), 
						'info' =>	_t('_dbcs_fbc_FieldDesc_City_Join'),
						'value' => $aProfileData['City'],
					);
				}

				if($aPrompts['zip'] == true) {
					$aForm['inputs'][] = array(
						'type' => 'text',
						'name' => 'zip',
						'caption' => _t('_dbcs_fbc_zip'), 
						'info' =>	_t('_dbcs_fbc_FieldDesc_zip_Join'),
						'value' => '',
					);
				}

				// Add terms as a hidden field. Seems dolphin 7.0.1 requires it. Perhaps other versions also.
				$aForm['inputs'][] = array(
					'type' => 'hidden',
					'name' => 'TermsOfUse',
					'label' => _t('_FieldCaption_TermsOfUse_Join'), 
					'colspan' => false,
					'value' => 'yes',
				);

				$aForm['inputs'][] = array(
					'type' => 'submit',
					'name' => 'btnsubmit',
					'value' => _t('_dbcs_fbc_submit'),
				);

				$oForm = new BxTemplFormView($aForm);

				$sCode = $this -> _oTemplate -> addCss('face_book_connect.css', true);
				$sCode .= $this -> _oTemplate -> addJs('dbcsFaceBookConnect.js', true);
				if($bAutoPromptNick) {
					$sCode .= '<div class="fbprompt_msg">' . _t('_dbcs_fbc_prompt_nick_msg') . '</div>';
				}
				if($bAutoPromptEmail) {
					$sCode .= '<div class="fbprompt_msg">' . _t('_dbcs_fbc_prompt_email_msg') . '</div>';
				}
				$sCode .= $oForm->getCode();

				$sForm = $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sCode));

				// Add unique ID fields to form.
				$sForm = str_replace('<tr >','********<tr>',$sForm);
				$aRows = explode('********', $sForm);
				$pattern = '/name="(.*?)"/';
				foreach ($aRows as $id => $value) {
					preg_match($pattern, $value, $matches);
					if(strpos($matches[1], '_confirm') !== false) {
						$sMatch = str_replace('[0]','', $matches[1]);
					} else {
						$sMatch = $matches[1];
					}
					$sOnClickInput = '';
					$sOnClickSelect = '';
					$value = str_replace('<tr', '<tr id="tr_' . $sMatch . '"', $value);

					if($GLOBALS['site']['ver'] == '7.1') {
						/*
						$value = preg_replace('/<i .*?<\/i>/', '<div style="display: inline-block;"><div id="error_' . $sMatch . '" class="fbprompt_error"></div></div>', $value);
						*/
						$value = str_replace('<i class="warn', '<i id="i_' . $sMatch . '" class="warn', $value);
					} else {
						/*
						$value = preg_replace('/<img class="warn" .*?>/', '<div style="display: inline-block;"><div id="error_' . $sMatch . '" class="fbprompt_error"></div></div>', $value);
						*/
						$value = str_replace('<img class="warn', '<img id="i_' . $sMatch . '" class="warn', $value);
					}

					$value = str_replace('<input', '<input id="input_' . $sMatch . '"' . $sOnClickInput, $value);
					$value = str_replace('<select', '<select id="select_' . $sMatch . '"' . $sOnClickSelect, $value);
					$aRows[$id] = $value;
				}
				$sForm = implode('', $aRows);
				echo $this -> _oTemplate -> getPage(_t('_dbcs_fbc_prompt'), $sForm);
				exit;
			} else {
				// No data to be prompted for, so just proceed to create profile.
				$this -> showOneMoment();
			}
		}

		function isEmailValid($sEmail) {
			$bEmailValid = true;
			if(strpos($sEmail,"proxymail.facebook.com") === true) $bEmailValid = false;
			if(strpos($sEmail,"@") === false) $bEmailValid = false;
			return $bEmailValid;
		}


		function actionReturnPrompt($sExtra = '') {
			if($sExtra != session_id()) return;
			$aProfileData = $this -> getProfileData();
			foreach ($_POST as $id => $value) {
				if($id != 'csrf_token' && $id != 'btnsubmit' && $id != 'Password_confirm') {
					//echo $id . ' - ' . $value . '<br>';
					$aProfileData[$id] = $value;
					if($id == 'Email') $aProfileData['email'] = $value;
					if($id == 'Password') {
						// Re-Encrypt the password.
						$aProfileData['ClearPassword'] = $aProfileData['Password'];
						$aProfileData['Password'] = encryptUserPwd($aProfileData['ClearPassword'], $aProfileData['Salt']);
					}
				}
			}

			$this -> saveProfileData($aProfileData);
			//print_r($aProfileData);
			//exit;
	        $iProfileId = $this -> _createProfile();
		}

		function actionCheckFormErrors() {
			$bErrors = false;
			bx_import('BxDolProfileFields');
			$oPF = new BxDolProfileFields(1);
			$aProfileData = $this -> getProfileData();
			// Check these fields if set. NickName, Password, Password_confirm, Email
			foreach ($_POST as $id => $value) {
				if($id != 'csrf_token' && $id != 'btnsubmit' && $id != 'Password_confirm') {
					$iHuman = 0;
					$iProfileID = 0;
					$mValue = $value;
					$aItem = $this -> _oDb -> getPfByName($id);

					$aChecks = array(
						'text' => array( 'Mandatory', 'Min', 'Max', 'Unique', 'Check' ),
						'area' => array( 'Mandatory', 'Min', 'Max', 'Unique', 'Check' ),
						'html_area' => array( 'Mandatory', 'Min', 'Max', 'Unique', 'Check' ),
						'pass' => array( 'Mandatory', 'Min', 'Max', 'Check', 'PassConfirm' ),
						'date' => array( 'Mandatory', 'Min', 'Max', 'Check' ),
						'select_one' => array( 'Min', 'Max', 'Mandatory', 'Values', 'Check' ),
						'select_set' => array( 'Min', 'Max', 'Mandatory', 'Values', 'Check' ),
						'num'    => array( 'Mandatory', 'Min', 'Max', 'Unique', 'Check' ),
						'range'  => array( 'Mandatory', 'RangeCorrect', 'Min', 'Max', 'Check' ),
						'system' => array( 'System' ),
						'bool'   => array( 'Mandatory' )
					);

					$aMyChecks = $aChecks[ $aItem['Type'] ];

					foreach ($aMyChecks as $sCheck ) {
						$sFunc = 'checkPostValueFor' . $sCheck;

						$mRes = $oPF -> $sFunc( $aItem, $mValue, $iHuman, $iProfileID );

						if( $mRes !== true ) {
							$bErrors = true;
							if(is_bool($mRes)) {
								if($aErrors[$id] == '') $aErrors[$id] = _t('_dbcs_fbc_FieldError_' . $id . '_' . $sCheck, $aItem[$sCheck]);
							} else {
								if($aErrors[$id] == '') $aErrors[$id] = $mRes;
								if($id == 'Password' && $sCheck == 'PassConfirm') {
									$aErrors['Password_confirm'] = $mRes;
									if($aErrors[$id] == $aErrors['Password_confirm']) $aErrors[$id] = '';
								}
							}
						}
					}
				}
			}
			$aErrors['Errors'] = $bErrors;
			echo json_encode($aErrors);
			//echo $aErrors;
		}

		function actionCreateProfile($sExtra = '') {
			if($sExtra != session_id()) return;
			$this -> _createProfile();
		}

        function _createProfile()
        {

			$aProfileInfo = $this -> getProfileData();
			$sSalt = $aProfileInfo['Salt'];
			$sPassword = $aProfileInfo['Password'];
			$this->logoutUrl = $aProfileInfo['LogoutUrl'];

            //if(!$aProfileInfo || !$aProfileInfo['first_name']) {
            //    return;
            //}

			//echo 'test';
			//exit;


			// Before creating profile, see if member has been banned.
			// Functions available since dolphin 7.0.4.
			// So only do for versions above and equal to 7.0.4
			if ($this -> sDolphinMajor == '7.0' && intval($this -> sDolphinMinor) >= 4) {
	            if ((2 == getParam('ipBlacklistMode') && bx_is_ip_blocked()) || ('on' == getParam('sys_dnsbl_enable') && bx_is_ip_dns_blacklisted('', 'join'))) {
					$this -> deleteProfileData();
					echo $this -> _oTemplate -> getPage( _t('_dbcs_facebook_error_occured'), MsgBox( _t('_dbcs_fb_ip_banned')));
					exit;
				}
			}

			// procces the date of birth;
			$iAge = 0;
            if( isset($aProfileInfo['birthday']) ) {
                $aProfileInfo['DateOfBirth'] =  date('Y-m-d', strtotime($aProfileInfo['birthday']) );
				$iAge1 = age($aProfileInfo['DateOfBirth']);
				$iAge2 = (int)getParam('search_start_age');
				if($iAge1 < $iAge2) {
					// Age obtained from facebook not within range for this site.
					$this -> deleteProfileData();
					echo $this -> _oTemplate -> getPage( _t('_dbcs_facebook_error_occured'), MsgBox( _t('_dbcs_fb_min_age_error', $iAge2)));
					exit;
				}
	        } else {
				// No date of birth set. Default to a data of birth of today - min site age.
				$iMinAge = (int) 30;//getParam('search_start_age');
				$sNewdob = strtotime('-' . $iMinAge . ' years');
				$aProfileInfo['DateOfBirth'] = date('Y-m-d', $sNewdob);
			}

			$sRelationshipStatus = $aProfileInfo['relationship_status'];
			if ($sRelationshipStatus == 'Widowed' || $sRelationshipStatus == 'Separated' || $sRelationshipStatus == 'Divorced') $sRelationshipStatus = 'Single';

			$aProfileInfo['Headline'] = _t('_dbcs_fbc_default_headline');
			if($aProfileInfo['about_me'] == '') $aProfileInfo['about_me'] = _t('_dbcs_fbc_default_about_me');

			$this -> saveProfileData($aProfileInfo);

			//echo 'test';
			//exit;

            $aProfileFields = array(
                'NickName'      		=> $aProfileInfo['NickName'],
                'Email'         		=> $aProfileInfo['email'],
                'FirstName'           	=> $aProfileInfo['first_name'],
                'LastName'           	=> $aProfileInfo['last_name'],
				'Sex'           		=> $aProfileInfo['Sex'],
                'DateOfBirth'   		=> $aProfileInfo['DateOfBirth'],
                'Headline'   			=> $aProfileInfo['Headline'],
                'Password'      		=> $sPassword,
                'DescriptionMe' 		=> $aProfileInfo['about_me'],
                'FavoriteBooks' 		=> $aProfileInfo['books'],
                'Interests'     		=> $aProfileInfo['interests'],
                'FavoriteFilms' 		=> $aProfileInfo['movies'],
                'FavoriteMusic' 		=> $aProfileInfo['music'],
                'Religion'      		=> $aProfileInfo['religion'],
				'RelationshipStatus'	=> $sRelationshipStatus,
                'City'          		=> $aProfileInfo['City'],
                'Country'       		=> $aProfileInfo['Country'],
                'zip'           		=> $aProfileInfo['zip'],
				);


            // check fields existence;
            foreach($aProfileFields as $sKey => $mValue) {
                if( !$this -> _oDb -> isFieldExist($sKey)) {
                    // (field not existence) remove from array;
                    unset($aProfileFields[$sKey]);
                }
            }

			if($this -> _oDb -> isProfileTypeSplitter()) {
				if(getParam('dbcs_facebook_connect_prompt_profile_type')) {
					$aProfileFields['ProfileType'] = $aProfileInfo['ProfileType'];
				}
			}


            // add some system values;
            $aProfileFields['Role'] = 1;
	        $aProfileFields['dbcsFacebookProfile'] = $_COOKIE['dbcsfbid'];
            $aProfileFields['DateReg'] = date( 'Y-m-d H:i:s' ); // set current date;


			// This feature not available on dolphin 7.0.0
			if (getParam('dbcs_facebook_connect_use_join_form')) {
				return $this -> _getJoinPage($aProfileFields, $this -> iFacebookUid);
			}




            // create new profile;
            $iProfileId = $this -> _oDb -> createProfile($aProfileFields);

			if(!$iProfileId) {
				// Error occured when creating profile. Abort.
				header('location: ' . BX_DOL_URL_ROOT);
				exit;
			}

			$this -> bImportAlbums = true;


			// Auto friends from config.
			$sAutoFriendList = getParam('dbcs_facebook_connect_autofriend_list');
			if ($sAutoFriendList != '') {
				$aAutoFriendList = explode(",",$sAutoFriendList);
				foreach($aAutoFriendList as $iAutoFriend) {
					if($iAutoFriend > 0) {
						$this -> _oDb -> addFriend($iProfileId, $iAutoFriend);
					}
				}
			}
            //Auto-friend members if they are already friends on Facebook
			if (getParam('dbcs_facebook_connect_facebook_friends')) {
	            $this -> setFacebookFriends($iProfileId);
			}


			// Figure out what the profile status should be set to.
			if (getParam('dbcs_facebook_connect_das') == 'Use Dolphins Settings' || getParam('dbcs_facebook_connect_das') == '') {
				if ( getParam('autoApproval_ifNoConfEmail') == 'on' ) {
					if ( getParam('autoApproval_ifJoin') == 'on' ) {
						$sProfileStatus = 'Active';
					}    
					else {
						$sProfileStatus = 'Approval';
					}
				} else {
					$sProfileStatus = 'Unconfirmed';
				}
			} else {
				$sProfileStatus = getParam('dbcs_facebook_connect_das');
			}



			// Update password.
			//$this -> newPassword = $sNewPass;
			$this -> _oDb -> setPassword($iProfileId, $sPassword, $sSalt);
			$this -> newPassword = $aProfileInfo['ClearPassword'];

            // update profile's status;
            $this -> _oDb -> updateProfileStatus($iProfileId, $sProfileStatus);


			// Set the membership per the membership setting.
			$sMembershipName = getParam('dbcs_facebook_connect_default_membership');
			if ($sMembershipName != 'Dolphins Default') {
				$iMembershipId = $this->_oDb->getMembershipId($sMembershipName);
				// Set the membership.
				if($iMembershipId > 3) {
					$aMemShip = getMembershipInfo($iMembershipId);
					if($aMemShip['Active'] == 'yes') {
						$this->_oDb->setMembershipLevel($iProfileId, $iMembershipId);
					}
				}
			}

			// Check here for valid account.
			$aProfile = getProfileInfo($iProfileId);
			$aProfile['NickName'];
			if(trim($aProfile['NickName']) == '') {
				// Error occured when creating profile. Abort.
				profile_delete($iProfileId);
				header('location: ' . BX_DOL_URL_ROOT);
				exit;
			}
			if(trim($aProfile['Email']) == '') {
				// Error occured when creating profile. Abort.
				profile_delete($iProfileId);
				header('location: ' . BX_DOL_URL_ROOT);
				exit;
			}


			// create system event
            bx_import('BxDolAlerts');
            $oZ = new BxDolAlerts('profile', 'join', $iProfileId);
            $oZ -> alert();
			
			// If zip code is prompted for, and map module is installed, then add entry to map.
            if(getParam('dbcs_facebook_connect_prompt_zip')) {
                if (BxDolModule::getInstance('BxWmapModule')) {
                    BxDolService::call('wmap', 'response_entry_add', array('profiles', $iProfileId));
                }
            }

/*
// I no longer have my friend inviter, so remove this section.

			// See if joining member was previously invited via the friend inviter.
			if ($this -> _oDb -> checkInvite($this -> iFacebookUid) == $this -> iFacebookUid) {
				$oZ = null;
				$oZ = new BxDolAlerts('fbfriendinviter', 'inviteaccepted', $iProfileId);
				$oZ -> alert();
			}
*/



			// Send out email notices.
			$sUseEmailForID = getParam('dbcs_facebook_connect_show_email');
			bx_import('BxDolEmailTemplates');
			$oP = new BxDolEmailTemplates();
			if ($sProfileStatus == 'Active') {
				if($sUseEmailForID) {
					$aPlus = array('NewPassword' => $aProfileInfo['ClearPassword'], 'NickName' => $aProfileFields['Email']);
				} else {
					$aPlus = array('NewPassword' => $aProfileInfo['ClearPassword'], 'NickName' => $aProfileInfo['NickName']);
				}
				$aTemplate = $oP -> getTemplate( 't_dbcs_FaceBookJoined' ) ;
				sendMail( $aProfileFields['Email'], $aTemplate['Subject'], $aTemplate['Body'], $iProfileId, $aPlus);
			} elseif ($sProfileStatus == 'Unconfirmed') {
				$sConfCode = base64_encode( base64_encode( crypt( $aProfileFields['Email'], CRYPT_EXT_DES ? 'secret_ph' : 'se' ) ) );
				$sConfLink = BX_DOL_URL_ROOT . "profile_activate.php?ConfID={$iProfileId}&ConfCode=" . urlencode( $sConfCode );
				if($sUseEmailForID) {
					$aPlus = array('ConfCode' => $sConfCode, 'ConfirmationLink' => $sConfLink, 'NewPassword' => $aProfileInfo['ClearPassword'], 'NickName' => $aProfileFields['Email']);
				} else {
					$aPlus = array('ConfCode' => $sConfCode, 'ConfirmationLink' => $sConfLink, 'NewPassword' => $aProfileInfo['ClearPassword'], 'NickName' => $aProfileInfo['NickName']);
				}
				$aTemplate = $oP -> getTemplate('t_dbcs_FaceBookUnconfirmed') ;
				sendMail( $aProfileFields['Email'], $aTemplate['Subject'], $aTemplate['Body'], $iProfileId, $aPlus);
			}


            // check avatar module;
            if( BxDolInstallerUtils::isModuleInstalled('avatar') ) {
				$sRedirect1 = getParam('dbcs_facebook_connect_redirect1');
				$sRedirect1 = str_replace("{memberid}",$iProfileId,$sRedirect1);
				$sRedirect1 = str_replace("{nickname}",$this -> dbGetNickName($iProfileId),$sRedirect1);

				if (strpos($sRedirect1,'avatar') > 0 || $sRedirect1 == '') {
					// check profile's logo;
					$this -> bAutoAvatar = false;
					if($aProfileInfo['picture']) {
			            BxDolService::call('avatar', 'set_image_for_cropping', array ($iProfileId, $aProfileInfo['picture'])); 
				    }
					if (BxDolService::call('avatar', 'join', array ($iProfileId, '_Join complete'))) {
	                    exit;
	                }
				} else {
					$iAvaID = $this -> _oDb -> getNextAvaID();
					if ($aProfileInfo['picture'] != '') {
						$iAvatarSaved = $this -> SaveAvatar($iAvaID,$aProfileInfo['picture'],$iProfileId);
						if ($iAvatarSaved == 1) {
							$this -> bAutoAvatar = true;
							$this -> _oDb -> setAvatar($iProfileId,$iAvaID);
						}
					}
					$this ->_oDb ->saveLogoutURL($iProfileId,$this->logoutUrl);
					$this ->_oDb ->saveExtraData($iProfileId, $aProfileInfo['link'], $aProfileInfo['username']);

		            $sCallbackUrl = BX_DOL_URL_ROOT . $sRedirect1;
					$this -> setLoggedJoin($iProfileId, $aProfileInfo['Password'], $sCallbackUrl);
				}

            } else {
				if(getParam('dbcs_facebook_connect_copy_photo') == 'on') {
					$this -> bAutoAvatar = true;
				} else {
					$this -> bAutoAvatar = false;
				}
                // set logged and redirect on home page;
                $aProfileInfo = getProfileInfo($iProfileId);
				$sRedirect1 = getParam('dbcs_facebook_connect_redirect1');
				$sRedirect1 = str_replace("{memberid}",$iProfileId,$sRedirect1);
				$sRedirect1 = str_replace("{nickname}",$this -> dbGetNickName($iProfileId),$sRedirect1);
				$this ->_oDb ->saveLogoutURL($iProfileId,$this->logoutUrl);
		        $sCallbackUrl = BX_DOL_URL_ROOT . $sRedirect1;
				$this -> setLoggedJoin($iProfileId, $aProfileInfo['Password'], $sCallbackUrl);
            }
        }
		
	function SaveAvatar($iAvaID,$url,$iProfileId) {
        bx_import('BxDolImageResize');
		$sTmpDir = BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/data/tmp/';
		$sImageName = $iAvaID . BX_AVA_EXT;
		$sTempImageName = time() . BX_AVA_EXT;
		$sImageNameSmall = $iAvaID . "i" . BX_AVA_EXT;
		$iAvatarSaved = 0;
		//echo $url;
		//exit;

		// Get Facebook Photo
        $s = bx_file_get_contents ($url);

		// incase of damaged file from facebook. Turn off warnings.
		ini_set('gd.jpeg_ignore_warning', 1);

		file_put_contents($sTmpDir . $sTempImageName, $s);
		// Save Avatar if image exists.
		if (file_exists($sTmpDir . $sTempImageName)) {
	        $o =& BxDolImageResize::instance(BX_AVA_W, BX_AVA_H);
	        $o->setJpegOutput (true);
	        $o->removeCropOptions ();
			$aSize = $o->getImageSize($sTmpDir . $sTempImageName);
			if ($aSize['w'] > $aSize['h']) {
				$s = $aSize['h'];
			} else {
				$s = $aSize['w'];
			}
			$o->setAutoCrop(true);
			//$o->setCropOptions(0,0,$s,$s);
	        $o->setSize(BX_AVA_W, BX_AVA_H);
	        $o->setSquareResize (true);
	        $o->resize($sTmpDir . $sTempImageName, $sTmpDir . $sImageName);
			unlink($sTmpDir . $sTempImageName);
			imageResize($sTmpDir . $sImageName, BX_AVA_DIR_USER_AVATARS . $sImageName, BX_AVA_W, BX_AVA_H, true);
			imageResize($sTmpDir . $sImageName, BX_AVA_DIR_USER_AVATARS . $sImageNameSmall, BX_AVA_ICON_W, BX_AVA_ICON_H, true);
			if (file_exists($sTmpDir . $sImageName)) {
				unlink($sTmpDir . $sImageName);
				$iAvatarSaved = 1;
			}
		}
		return $iAvatarSaved;
	}

	function serviceCheckProfile () {
		$iMemID = intval($_COOKIE['memberID']);
		$r = '';
		$iRequired = 0;
		if($iMemID > 0) {
			// see if any required profile fields are missing.
			$aProfileFields = $this -> _oDb -> getProfileFields();
			foreach ($aProfileFields as $iID => $dbData) {
				$sLName = $dbData['Name'];
				$sValue = $this -> _oDb -> getProfileValue($iMemID,$sLName);
				if ($sValue == '') $iRequired++;
			}
			if($iRequired > 0) {
				$iNagValue = (int)getParam('dbcs_facebook_connect_nag_time');
				if ($iNagValue > 0) {
					$iNagTime = $this -> _oDb -> getNagTime($iMemID);
					$iCurTime = time();
					$iNagValueSeconds = 60 * 60 * $iNagValue;
					if ($iCurTime - $iNagTime > $iNagValueSeconds) {
						//$this -> _oDb -> updateNagTime($iMemID);
						$r = '
							<SCRIPT language="JavaScript">
							<!--
							window.location="' . BX_DOL_URL_ROOT . 'profile_info_required.php";
							//-->
							</SCRIPT>
						';
						return $r;
						exit;
						}
					}
				}
			}
		return $r;
	}


	function deleteCookies( ) {
		// Loop through all cookies. Delete those having to do with facebook.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

		foreach( $_COOKIE as $key => $value ) {
			if( strstr($key, 'user_' ) || strstr($key, 'session_key_' ) || strstr($key, 'expires_' ) || strstr($key, 'ss_' ) || strstr($key, 'fbsetting_' ) || strstr($key, 'fbs_' ) || strstr($key, '_user' ) || strstr($key, '_session_key' ) || strstr($key, '_expires' ) || strstr($key, '_ss' ) || strstr($key, '_fbsetting' ) || strstr($key, 'base_domain' ) || strstr($key,'fbcsrf') || strstr($key, (string) $this -> _oConfig -> mApiKey ) ) {
				$bResult = setcookie($key,'',time() - 96 * 3600, '/', $this -> sSiteDomain );
				$bResult = setcookie($key,'',time() - 96 * 3600, '/', $this -> sBaseDomain );
				$bResult = setcookie($key,'',time() - 96 * 3600);
				unset($_COOKIE[$key]);
			}
		}
	}

	function deleteOtherCookies( ) {
		// Loop through all cookies.
		// Some of these cookies are cookies i have found used by other
		// mods that interfear with facebook connect. So i check for those as well.
		// These others currently are LoggedPages,LoggedTime,LoggedId,LoggedIp,PHPSESSID
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
		foreach( $_COOKIE as $key => $value ) {
			if( strstr($key, "LoggedPages" ) || strstr($key, "LoggedTime" ) || strstr($key, "LoggedId" ) || strstr($key, "LoggedIp" ) || strstr($key, "PHPSESSID" ) ) {
				setcookie( $key, '', time( ) - 96 * 3600, '/', $this -> sSiteDomain );
				setcookie( $key, '', time( ) - 96 * 3600, '/', $this -> sBaseDomain );
				unset( $_COOKIE[$key] );
			}
		}
	}

        function _getJoinPage($aProfileFields, $iFacebookUserId)
        {
        	bx_import('BxDolSession');
			$oSession = BxDolSession::getInstance();
			$oSession -> setValue($this -> _oConfig -> sFacebookSessionUid, $iFacebookUserId);

        	bx_import("BxDolJoinProcessor");

			$GLOBALS['oSysTemplate']->addJsTranslation('_Errors in join form');
			$GLOBALS['oSysTemplate']->addJs(array('join.js', 'jquery.form.js'));
	
			$oJoin = new BxDolJoinProcessor();

			//process recived fields
			foreach($aProfileFields as $sFieldName => $sValue) {
				$oJoin -> aValues[0][$sFieldName] = $sValue;
			}

			$this -> _oTemplate -> getPage( _t( '_JOIN_H' ), $oJoin->process());
			exit;
        }

		function copyPhoto($iProfileId, $url, $sAlbumName = '', $sTitle = '', $sDesc = '', $sTags = '', $aCategories = '') {
			if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader')) {
				$sTempImageName = time() . ".jpg";
		        $s = bx_file_get_contents ($url);

				// incase of damaged file from facebook. Turn off warnings.
				ini_set('gd.jpeg_ignore_warning', 1);

		        file_put_contents($GLOBALS['dir']['tmp'] . $sTempImageName, $s);
				if ($sAlbumName == '') $sAlbumName = str_replace('{nickname}', $this -> dbGetNickName($iProfileId), getParam('bx_photos_profile_album_name'));
				if ($sTitle == '') $sTitle = _t('_dbcs_fbc_profile_photo');
				if ($sDesc == '') $sDesc = _t('_dbcs_fbc_profile_photo');
				if ($sTags == '') $sTags = _t('_ProfilePhotos');
				if ($aCategories == '') $aCategories = array(_t('_ProfilePhotos'));
				$aFileInfo = array (
                    'medTitle' => $sTitle,
                    'medDesc' => $sDesc,
                    'medTags' => $sTags,
                    'Categories' => $aCategories,
                    'album' => $sAlbumName,
                );
				BxDolService::call('photos', 'perform_photo_upload', array($GLOBALS['dir']['tmp'] . $sTempImageName, $aFileInfo, false), 'Uploader');
			}
		}


		function actionImportFacebookAlbums($sExtra = '') {
			if($sExtra != session_id()) return;

			$iProfileId = getLoggedId();
			echo '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>';
			for($x = 1; $x <= 1000; $x++) {
				echo $x . '<br>';
			}
			// to get the photos in the album, loop through the albums and
			// get https://graph.facebook.com/albumID/photos/
			if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader')) {
				// Import profile photos first.
				$this -> importAlbums(true);
				// Now import the rest of the albums.
				$this -> importAlbums(false);
			}
			$sCode = '
				<script>
				window.top.location = "' . $this -> sModuleUrl . 'facebook_signup_finish/show_finish"
				</script>
			';
			echo $sCode;
		}

		function importAlbums($bProfileOnly = false) {
			$iProfileId = (int)$_COOKIE['memberID'];
			$sOldActivation = getParam('bx_photos_activation');
			$sOldCatAct = getParam('category_auto_app_bx_photos');
			if(!$sOldActivation || !$sOldCatAct) {
				// Turn on auto activation and clear cache.
				setParam('bx_photos_activation', 'on');
				setParam('category_auto_app_bx_photos', 'on');
				// Clear for dolphin 7.0.3 and up
				$files = glob(BX_DIRECTORY_PATH_CACHE . 'sys_options_*.php');
				array_map('unlink', $files);
				// Clear for dolphin versions below 7.0.3
				$sFileName = BX_DIRECTORY_PATH_CACHE . 'sys_options.php';
				if (file_exists($sFileName)) unlink($sFileName);
			}
			$this -> iFacebookUid = $_COOKIE['dbcsfbid'];
			//$meAlbums = unserialize(file_get_contents($GLOBALS['dir']['tmp'] . $this -> iFacebookUid . '_albums.tmp'));
			if ($this -> iFacebookUid) {
				$meAlbums = unserialize(file_get_contents($GLOBALS['dir']['tmp'] . $this -> iFacebookUid . '_albums.tmp'));
				foreach ($meAlbums['data'] as $key => $value) {
					set_time_limit(120); 
					$sAlbumID = $meAlbums['data'][$key]['id'];
					$sAlbumName =  $meAlbums['data'][$key]['name'];
					$sAlbumPrivacy =  strtolower($meAlbums['data'][$key]['privacy']);
					$bImportOk = false;
					if((getParam('dbcs_facebook_connect_import_privacy') == 'Import profile pictures only.') && ($sAlbumName == 'Profile Pictures')) $bImportOk = true;
					if((getParam('dbcs_facebook_connect_import_privacy') == 'Import public albums only.') && ($sAlbumPrivacy == 'everyone')) $bImportOk = true;
					if(getParam('dbcs_facebook_connect_import_privacy') == 'Import all albums. Preserve privacy.') $bImportOk = true;
					if(getParam('dbcs_facebook_connect_import_privacy') == 'Import all albums. Force to public.') {
						$bImportOk = true;
						$sAlbumPrivacy = 'everyone';
					}
					if(getParam('dbcs_facebook_connect_import_privacy') == 'Import all albums. Force to members.') {
						$bImportOk = true;
						$sAlbumPrivacy = 'members';
					}
					if(getParam('dbcs_facebook_connect_import_privacy') == 'Import all albums. Force to me only.') {
						$bImportOk = true;
						$sAlbumPrivacy = 'meonly';
					}
					if(getParam('dbcs_facebook_connect_import_privacy') == 'Import all albums. Force to friends.') {
						$bImportOk = true;
						$sAlbumPrivacy = 'friends';
					}
					if($bImportOk) {
						if($bProfileOnly && $sAlbumName != 'Profile Pictures') $bImportOk = false;			
					}
					if($bImportOk) {
						if(!$bProfileOnly && $sAlbumName == 'Profile Pictures') $bImportOk = false;
					}

					if($bImportOk) {
						// Create the album here.
						// If the album name = Profile Pictures then do not create a album.
						if ($sAlbumName != 'Profile Pictures') {
							$this -> _createAlbum($iProfileId, $sAlbumName, $sAlbumPrivacy);
							echo '<script>window.parent.$(\'#album_status\').html(\'' . $sAlbumName . '\')</script>' . "\r\n";
							ob_flush();
							flush();
						} else {
							// Clear the album name so the photos go into the default album.
							$sAlbumName = '';
							echo '<script>window.parent.$(\'#album_status\').html(\'Profile Pictures\')</script>' . "\r\n";
							ob_flush();
							flush();
						}
						$mePhotos = unserialize(file_get_contents($GLOBALS['dir']['tmp'] . $this -> iFacebookUid . '_photos_' . $sAlbumID . '.tmp'));
						foreach ($mePhotos['data'] as $key => $value) {
							$sPhotoID = $mePhotos['data'][$key]['id'];
							$sPhotoURL = $mePhotos['data'][$key]['source'];
							$sPhotoName = $mePhotos['data'][$key]['name'];
							if($sPhotoName != '') {
								if(strpos($sPhotoName,'http:') !== false) $sPhotoName = '';
								if(strpos($sPhotoName,'https:') !== false) $sPhotoName = '';
								$sPhotoName = ereg_replace("[^A-Za-z0-9 ]", "", $sPhotoName);
							}
							if($sPhotoName == '') {
								$aPhotoName = explode('/', $sPhotoURL);
								$sPhotoName = $aPhotoName[count($aPhotoName)-1];
							}
							$sTitle = $sPhotoName;
							if ($sTitle == '') $sTitle = 'Untitled';
							$sDesc = 'Facebook imported photo. - ' . $sPhotoName;
							$sTags = $sPhotoName;
							$sTags = str_replace(' ', ',', $sTags);
							$aCategories = array('Facebook Member Photos');
							// Upload this photo to the album
							$this -> copyPhoto($iProfileId, $sPhotoURL, $sAlbumName, $sTitle, $sDesc, $sTags, $aCategories);
							echo '<script>window.parent.$(\'#photo_status\').html(\'' . $sPhotoURL . '\')</script>' . "\r\n";
							ob_flush();
							flush();
						}
						// For some reason description is not getting set when album is created.
						// Added this to force the description. I need to look into it further
						// but seems something in the photos module is causing it.
						if ($sAlbumName != 'Profile Pictures') {
							$albumURI = uriFilter($sAlbumName);
							$this->_oDb->setAlbumDescription($albumURI, $sAlbumName);
						}
					}
				}
			}
			if(!$sOldActivation || !$sOldCatAct) {
				// Reset auto activation to origional setting and clear cache.
				setParam('bx_photos_activation', $sOldActivation);
				setParam('category_auto_app_bx_photos', $sOldCatAct);
				// Clear for dolphin 7.0.3 and up
				$files = glob(BX_DIRECTORY_PATH_CACHE . 'sys_options_*.php');
				array_map('unlink', $files);
				// Clear for dolphin versions below 7.0.3
				$sFileName = BX_DIRECTORY_PATH_CACHE . 'sys_options.php';
				if (file_exists($sFileName)) unlink($sFileName);
				// Set all imported photos to pending.
				$this->_oDb->setPending((int)$_COOKIE['memberID']);
			}
		}

		function _createAlbum($iProfileId, $albumName, $sAlbumPrivacy) {
			if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader')) {
				$albumURI = uriFilter($albumName);
				$iPrivacy = 5;	// Default for albums imported from facebook if facebook did not specify everyone or custom

				// These are the override options.
				if ($sAlbumPrivacy == 'friends') $iPrivacy = 5;
				if ($sAlbumPrivacy == 'members') $iPrivacy = 4;
				if ($sAlbumPrivacy == 'meonly') $iPrivacy = 2;

				// If facebook privacy is set to everyone then dolphin needs to be set to public
				if ($sAlbumPrivacy == 'everyone') $iPrivacy = 3;
				// If facebook privacy is set to custom then dolphin needs to be set to me only
				if ($sAlbumPrivacy == 'custom') $iPrivacy = 2;

				$this->_oDb->createAlbum($iProfileId, $albumName, $albumURI, $iPrivacy);
			}
		}

        function setFacebookFriends($iProfileId)
        {
       		try {
				$aFacebookFriends = $this -> oFacebook -> api('/me/friends/');
			} catch (dbcs_fbc_FacebookApiException $e) {
				//$this -> showError($e, 926);
				return;
			}
        	//process friends
        	if( !empty($aFacebookFriends) && is_array($aFacebookFriends) ) {
        		$aFacebookFriends = array_shift($aFacebookFriends);

        		foreach($aFacebookFriends as $iKey => $aFriend)
        		{
        			$iFriendId = $this -> _oDb -> getProfileId($aFriend['id']);
        			if($iFriendId && !is_friends($iProfileId, $iFriendId) ) {
        				//add to friends list
						$this -> _oDb -> addFriend($iProfileId, $iFriendId);
        			}
        		}
        	}
        }

        function setLogged($iProfileId, $sPassword, $sCallbackUrl = null)
        {
            bx_login($iProfileId);
			check_logged();
            $sCallbackUrl = ($sCallbackUrl) ? $sCallbackUrl : BX_DOL_URL_ROOT;			
			// moved this to after the bx_login. Was not working where it was on dolphin 7.0.1
			//$this ->_oDb ->saveLogoutURL($iProfileId,$this->logoutUrl);

            header('location: ' . $sCallbackUrl);
			exit;
        }

        function setLoggedJoin($iProfileId, $sPassword, $sCallbackUrl = null) {
            bx_login($iProfileId);
			check_logged();
            $sCallbackUrl = ($sCallbackUrl) ? $sCallbackUrl : BX_DOL_URL_ROOT;			

			if ($this -> bAutoAvatar) $sAutoAvatar = 'true'; else $sAutoAvatar = 'false';

			$aProfileData = $this -> getProfileData();
			$aProfileData['redirect'] = $sCallbackUrl;
			$aProfileData['memberid'] = $iProfileId;
			$aProfileData['autoavatar'] = $sAutoAvatar;

			$this -> saveProfileData($aProfileData);

			$sImportOption = getParam('dbcs_facebook_connect_import_albums');
			// If permissions to access photos was not granted, then we can't import albums. So shut import off.
			if($aProfileData['permissions']['user_photos'] != 'granted') $sImportOption = '';

			if ($sImportOption && $this -> bImportAlbums) {
				$this -> actionFacebookSignupFinish('ask_import_albums');
			} else {
				if ($this -> bImportAlbums) {
					// if we are able to copy albums but the option is just off
					// then we still copy the main profile photo.
					$this -> actionFacebookSignupFinish('show_finish','copy_photo');
				} else {
					$this -> actionFacebookSignupFinish('show_finish');
				}
			}
		}

    function actionFacebookSignupFinish($sStage,$sCopyPhoto = '') {
		$iMemID = getLoggedId();
		$aProfileData = $this -> getProfileData();


		if ($sStage == 'ask_import_albums') {

			$aVars = array (
				'yes_url' => $this -> sModuleUrl . 'facebook_signup_finish/import_albums',
				'no_url' => $this -> sModuleUrl . 'facebook_signup_finish/show_finish/copy_photo',
				'key_yes' => _t('_dbcs_fbc_yes'),
				'key_no' => _t('_dbcs_fbc_no'),
			);

			if($GLOBALS['site']['ver'] == '7.1') {
				$sCode = $this->_oTemplate -> parseHtmlByName('askimport_v71x.html', $aVars);
			} else {
				$sCode = $this->_oTemplate -> parseHtmlByName('askimport_v70x.html', $aVars);
			}

			$this->_oTemplate->getPage(_t('_dbcs_fb_ask_import_header'), $sCode);
			exit();

		}

		if ($sStage == 'show_finish') {
			// If we are not importing albums, then at least copy the main photo to
			// the members profile. Otherwise we skip this.
			if($sCopyPhoto == 'copy_photo') {
				if($aProfileData['autoavatar'] == 'true' && $aProfileData['picture'] != '' && getParam('dbcs_facebook_connect_copy_photo') == 'on') {
					$this -> copyPhoto($iMemID, $aProfileData['picture']);
				}
			}
			// Set the privacy of the default album to config default.
			if($GLOBALS['site']['ver'] == '7.1') {
				$sAlbumName = str_replace('{nickname}', getUsername($iMemID), getParam('bx_photos_profile_album_name'));
			} else {
				$sAlbumName = str_replace('{nickname}', getNickName($iMemID), getParam('bx_photos_profile_album_name'));
				//$sAlbum = getNickName($iProfileId) . "'s photos";
			}

			$sAlbumUri = uriFilter($sAlbumName);
			$this -> _oDb -> setPrivacy($sAlbumUri, $this -> _oConfig -> iDefaultPrivacy);
			// Remove any empty albums.
			$this -> _oDb -> deleteEmptyAlbums($iMemID);
			$sUseEmailForID = getParam('dbcs_facebook_connect_show_email');
			if($sUseEmailForID) {
				$sLoginID = $aProfileData['email'];
			} else {
				$sLoginID = $aProfileData['NickName'];
			}

			$aVars = array (
				'login_id' => $sLoginID,
				'password' => $aProfileData['ClearPassword'],
				'callback_url' => $aProfileData['redirect'],
				'button_text' => _t('_dbcs_fbc_button_continue'),

			);

			$this -> deleteProfileData();

			if($GLOBALS['site']['ver'] == '7.1') {
				$sCode = $this->_oTemplate -> parseHtmlByName('finish_v71x.html', $aVars);
			} else {
				$sCode = $this->_oTemplate -> parseHtmlByName('finish_v70x.html', $aVars);
			}
			$this->_oTemplate->getPage(_t('_dbcs_fb_finish_header'), $sCode);
		}

		if ($sStage == 'import_albums') {
			// This template will have a hidden div with a ajax loading
			$aVars = array (
				'message' => _t('_dbcs_fb_importing_msg'),
				'flash_path' => BX_DOL_URL_ROOT . 'modules/deano/deanos_facebook_connect/templates/base/flash/',
				'load_url' => $this -> sModuleUrl . 'import_facebook_albums/' . session_id(),

			);
			if($GLOBALS['site']['ver'] == '7.1') {
				$sCode = $this->_oTemplate -> parseHtmlByName('importing_v71x.html', $aVars);
			} else {
				$sCode = $this->_oTemplate -> parseHtmlByName('importing_v70x.html', $aVars);
			}
			$this->_oTemplate->getPage(_t('_dbcs_fb_importing_header'), $sCode);
		}
    }

	function showError($e, $iLine) {
		$sErrorMsg =  MsgBox($e . '<br><br>On line ' . $iLine);
		$this -> deleteProfileData();
		echo $this -> _oTemplate -> getPage( _t('_dbcs_facebook_error_occured'), $sErrorMsg);
		exit;
	}

	function getSiteDomain( ) {
		$sURL = $GLOBALS['site']['url'];
		$sURL = str_replace( "http://", '', $sURL );
		$aURL = explode( "/", $sURL );
		return $aURL[0];
	}

	function getBaseDomain( $sSiteDomain ) {
		return getRegisteredDomain( $sSiteDomain, $tldTree );
	}

	function checkRequired( ) {
		// check Fb api keys;
		if( !$this -> _oConfig -> mApiKey || !$this -> _oConfig -> mApiSecret ) {
			$this -> deleteProfileData();
			echo $this -> _oTemplate -> getPage( _t( '_dbcs_facebook_error_occured' ), MsgBox( _t( '_dbcs_facebook_profile_error_api_keys' ) ) );
			exit;
		}
		if( getParam( 'dbcs_facebook_connect_use_join_form' ) ) {
			if( $this -> sDolphinMajor == '7.0' && $this -> sDolphinMinor == '0' ) {
				$this -> deleteProfileData();
				echo $this -> _oTemplate -> getPage( _t( '_dbcs_facebook_error_occured' ), MsgBox( _t( '_dbcs_facebook_join_feature_unavailable' ) ) );
				exit;
			}
		}
		// Check to make sure site is not in Invite Only Mode.
		// Add support for refaqb cookie. That is the Forced Matrix (referral) Mod by AntonLV
		if( getParam('reg_by_inv_only') == 'on' && (!isset($_COOKIE['idFriend']) ||  getID($_COOKIE['idFriend']) == 0) ){
			$this -> deleteProfileData();
			$this -> _oTemplate -> getPage( _t('_dbcs_facebook_error_occured'), MsgBox(_t('_registration by invitation only')));
           	exit;
       	}
		// check tmp folder
		if( !is_writable( BX_DIRECTORY_PATH_ROOT . 'tmp' ) ) {
			// tmp folder not writable. Show error.
			echo $this -> _oTemplate -> getPage( _t( '_dbcs_permissions_error' ), MsgBox( _t( '_dbcs_permissions_error_msg' ) ) );
			exit;
		}
		// check curl
		if (!function_exists('curl_init')) {
			echo $this -> _oTemplate -> getPage( _t( '_dbcs_fb_erh' ), MsgBox( _t( '_dbcs_fb_erc' ) ) );
			exit;
		}
		// check jason
		if (!function_exists('json_decode')) {
			echo $this -> _oTemplate -> getPage( _t( '_dbcs_fb_erh' ), MsgBox( _t( '_dbcs_fb_erj' ) ) );
			exit;
		}
		// Check for proper operation of curl
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL,'https://graph.facebook.com' );
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch,CURLOPT_VERBOSE,false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$sPage = curl_exec($ch);
		$sCurlError = curl_error($ch);
		if ($sPage === false) {
			echo $this -> _oTemplate -> getPage( _t( '_dbcs_fb_erh' ), MsgBox( _t( '_dbcs_fb_curl_test_failed', $sCurlError ) ) );
			exit;
		} 
		// Check for proper operation of gethostbyname
		$host = 'graph.facebook.com';
		$ip = gethostbyname($host);
		if ($ip == $host) {
			echo $this -> _oTemplate -> getPage( _t( '_dbcs_fb_erh' ), MsgBox( _t( '_dbcs_fb_dns_test_failed' ) ) );
			exit;
		} 
	}

	function getRealIpAddr() {
		// This current version was pulled from dolphin 7.1.3. This version works well
		// so i replaced mine with this. Included here in my own function for older
		// versions of dolphin rather than using dolphins built in getVisitorIP which
		// is not very good on older versions of dolphin.
		$ip = "0.0.0.0";
		if( ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) && ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif( ( isset( $_SERVER['HTTP_CLIENT_IP'])) && (!empty($_SERVER['HTTP_CLIENT_IP'] ) ) ) {
			$ip = explode(".",$_SERVER['HTTP_CLIENT_IP']);
			$ip = $ip[3].".".$ip[2].".".$ip[1].".".$ip[0];
		} elseif((!isset( $_SERVER['HTTP_X_FORWARDED_FOR'])) || (empty($_SERVER['HTTP_X_FORWARDED_FOR']))) {
			if ((!isset( $_SERVER['HTTP_CLIENT_IP'])) && (empty($_SERVER['HTTP_CLIENT_IP']))) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		}

		if (!preg_match("/^\d+\.\d+\.\d+\.\d+$/", $ip))
			$ip = $_SERVER['REMOTE_ADDR'];

		return $ip;
	}

	function dbGetNickName($iProfileId) {
		if($GLOBALS['site']['ver'] == '7.1') {
			$sNickName = getUsername($iProfileId);
		} else {
			$sNickName = getNickName($iProfileId);
		}
		return $sNickName;
	}

	function str_lreplace($search, $replace, $subject)
	{
		// Similar to str_replace with a count of 1, but replaces first match from right to left.
		$pos = strrpos($subject, $search);
		if($pos !== false)
		{
			$subject = substr_replace($subject, $replace, $pos, strlen($search));
		}
		return $subject;
	}

	function saveProfileData($aProfileInfo) {
		$iFbId = $_COOKIE['dbcsfbid'];
		file_put_contents($GLOBALS['dir']['tmp'] . $iFbId . '.tmp', serialize($aProfileInfo));
	}

	function getProfileData() {
		$iFbId = $_COOKIE['dbcsfbid'];
		$aProfileInfo = unserialize(file_get_contents($GLOBALS['dir']['tmp'] . $iFbId . '.tmp'));
		return $aProfileInfo;
	}

	function deleteProfileData() {
		$iFbId = $_COOKIE['dbcsfbid'];
		if(file_exists($GLOBALS['dir']['tmp'] . $iFbId . '.tmp')) unlink($GLOBALS['dir']['tmp'] . $iFbId . '.tmp');
		setcookie('dbcsfbid', null, -1, '/');
	}

	function getFbJsCode() {
		$sApiKey = $this -> _oConfig -> mApiKey;
		$sModuleUrl = $this -> sModuleUrl;
		$sChannelUrl = str_replace(array('http://', 'https://'), '', $sModuleUrl) . 'inc/';
		$sScope = $this -> _oConfig -> aFacebookParams['scope'];

		$sScript = <<<CODE
			<div id="fb-root"></div>
			<script>
				window.fbAsyncInit = function () {
					FB.init({
						appId: '{$sApiKey}', // App ID
						channelUrl: '//{$sChannelUrl}channel.html', // Channel File
						status: true, // check login status
						cookie: true, // enable cookies to allow the server to access the session
						xfbml: true // parse XFBML
					});

					FB.Event.subscribe('auth.authResponseChange', function (response) {
						if (response.status === 'connected') {
							//testAPI();
							//window.location="http://www.deanbassett.com/modules/?r=deanos_facebook_connect/login_form";
						} else if (response.status === 'not_authorized') {
							//FB.login();
							dblogon();
						} else {
							//FB.login();
							dblogon();
						}
					});
				};

				// Load the SDK asynchronously
				(function (d) {
					var js, id = 'facebook-jssdk',
						ref = d.getElementsByTagName('script')[0];
					if (d.getElementById(id)) {
						return;
					}
					js = d.createElement('script');
					js.id = id;
					js.async = true;
					js.src = "//connect.facebook.net/en_US/all.js";
					ref.parentNode.insertBefore(js, ref);
				}(document));

				function dblogon() {
					FB.login(function (response) {
						if (response.status === 'connected') {
							$('#msgBox0BackGround').show();
							$('#msgBox0').show();
							window.location = "{$sModuleUrl}login_form/popup/"; // popup param must be passed on url so script knows login via popup was done.
						} else {
							//alert(response.status);
							$('#msgBox0BackGround').hide();
							$('#msgBox0').hide();
						}
					}, {
						scope: '{$sScope}'
					});
				}
			</script>
CODE;

		return $sScript;
	}

	function serviceGetButton($sSize = 'large', $sDivStyle = '', $bScript = false) {
		if((int)$_COOKIE['memberID']) return;
		$iLangCat = $this->_oDb->getLangCat();
		if(getParam('dbcs_facebook_connect_use_popup')) {
			$sOnClick = 'dblogon();';
		} else {
			$sOnClick = 'window.open (\'' . $this -> sModuleUrl . 'login_form\',\'_self\');';
		}
		if($sSize == 'large') {
			$aVars = array(
				'onclick' => $sOnClick,
				'text' => _t('_dbcs_fbc_button_text_lg'),
			);
			$sButtonCode = $this->_oTemplate -> parseHtmlByName('button_large.html', $aVars);
		} else {
			$aVars = array(
				'onclick' => $sOnClick,
				'text' => _t('_dbcs_fbc_button_text_sm'),
			);
			$sButtonCode = $this->_oTemplate -> parseHtmlByName('button_small.html', $aVars);
		}

		$sStart = '<div style="' . $sDivStyle . '">';
		$sEnd = '</div>';
		$sButtonCode = $sStart . $sButtonCode . $sEnd;

		if($bScript) {
			$sLgScr = '
				<style>
					.boxContent .form_advanced_wrapper {margin: 9px;}
				</style>
			';
			$sButtonCode = $sLgScr . $sButtonCode;
		}

		return $sButtonCode;
	}

	function getFbWaitPop() {
		$sScript = '
<style>
.dbcs_fog { display: none; top:0; left:0; position:fixed; padding:0; margin:0; width:100%; height:2000px; background-color:#000000; opacity:0.5; z-index:999; }
.dbcs_content { display: none; border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; border: 2px solid #4F0000; width: 350px; background-color: #FFFFFF; margin-left: -175px; z-index: 1000; position: fixed; -webkit-box-shadow: 8px 8px 5px 0px rgba(0, 0, 0, 0.55); -moz-box-shadow: 8px 8px 5px 0px rgba(0, 0, 0, 0.55); box-shadow: 8px 8px 5px 0px rgba(0, 0, 0, 0.55); left: 50%; top: 50%; margin-top: -60px; }
.dbcs_inner { padding: 16px; text-align: center; font-size: 18px; z-index: 1001; color: #000000; }
</style>
<div id="msgBox0BackGround" class="dbcs_fog"></div>
<div id="msgBox0" class="dbcs_content">
  <div class="dbcs_inner">' . _t('_dbcs_fbc_login_popup_text') . '</div>
</div>
		';
		return $sScript;

	}


	function serviceGetFbJsCode() {
		if(getParam('dbcs_facebook_connect_use_popup')) {
			$sScript = $this -> getFbJsCode();
			$sScript .= $this -> getFbWaitPop();
			return $sScript;
		}
	}

// *******************************************************
    }
?>