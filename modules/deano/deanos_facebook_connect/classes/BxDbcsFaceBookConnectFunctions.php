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


    class BxDbcsFaceBookConnectFunctions
    {

		var $_oTemplate;

    	function BxDbcsFaceBookConnectFunctions($template) 
        {
			$this -> _oTemplate = $template;
		}

		function genBackupForm() {
			if($GLOBALS['site']['ver'] == '7.1') {
				$sTemplate = 'backup_form_v71x.html';
			} else {
				$sTemplate = 'backup_form_v70x.html';
			}

			$aVars = array (
				'backup_file_name' => date("Y-m-d-H-i-s") . '-fb.bak',
			);
			$sCode = $this->_oTemplate -> parseHtmlByName($sTemplate, $aVars);

			return $sCode;
		}

		function genRestoreForm() {
			if($GLOBALS['site']['ver'] == '7.1') {
				$sTemplate = 'restore_form_v71x.html';
			} else {
				$sTemplate = 'restore_form_v70x.html';
			}
			$tmpDir = BX_DIRECTORY_PATH_MODULES . 'deano/deanos_facebook_connect/backup/';
			$aFiles = glob($tmpDir . "*-fb.bak");
			if ($aFiles) {
				$sOptions = '';
				foreach (glob($tmpDir . "*-fb.bak") as $filename) {
					$file = str_replace($tmpDir,"",$filename);
					$sOptions .= '<option value="' . $file . '">' . $file . '</option>' . "\r\n";
				}
				$aVars = array (
					'options' => $sOptions,
				);
				$sCode = $this->_oTemplate -> parseHtmlByName($sTemplate, $aVars);
			} else {
				$sCode = MsgBox(_t('_dbcs_fb_No Backups Found'));
			}
			return $sCode;
		}

	function saveBackup($fbIDs) {
		$sOut = '';
		$tmpDir = BX_DIRECTORY_PATH_MODULES . 'deano/deanos_facebook_connect/backup/';
		$fn = $_POST['backupfn'];
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_api_key') . "' WHERE `Name` = 'dbcs_facebook_connect_api_key';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_secret_key') . "' WHERE `Name` = 'dbcs_facebook_connect_secret_key';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_use_popup') . "' WHERE `Name` = 'dbcs_facebook_connect_use_popup';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_button') . "' WHERE `Name` = 'dbcs_facebook_connect_button';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_allow_spaces') . "' WHERE `Name` = 'dbcs_facebook_connect_allow_spaces';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_permalinks') . "' WHERE `Name` = 'dbcs_facebook_connect_permalinks';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_option1') . "' WHERE `Name` = 'dbcs_facebook_connect_option1';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_option2') . "' WHERE `Name` = 'dbcs_facebook_connect_option2';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_option3') . "' WHERE `Name` = 'dbcs_facebook_connect_option3';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_option4') . "' WHERE `Name` = 'dbcs_facebook_connect_option4';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_match_email') . "' WHERE `Name` = 'dbcs_facebook_connect_match_email';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_redirect1') . "' WHERE `Name` = 'dbcs_facebook_connect_redirect1';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_redirect2') . "' WHERE `Name` = 'dbcs_facebook_connect_redirect2';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_logout_redirect') . "' WHERE `Name` = 'dbcs_facebook_connect_logout_redirect';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_unregister_redirect') . "' WHERE `Name` = 'dbcs_facebook_connect_unregister_redirect';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_fb_logout') . "' WHERE `Name` = 'dbcs_facebook_connect_fb_logout';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_nag_time') . "' WHERE `Name` = 'dbcs_facebook_connect_nag_time';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_autofriend_list') . "' WHERE `Name` = 'dbcs_facebook_connect_autofriend_list';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_use_join_form') . "' WHERE `Name` = 'dbcs_facebook_connect_use_join_form';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_prompt_pass') . "' WHERE `Name` = 'dbcs_facebook_connect_prompt_pass';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_prompt_profile_type') . "' WHERE `Name` = 'dbcs_facebook_connect_prompt_profile_type';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_prompt_nick') . "' WHERE `Name` = 'dbcs_facebook_connect_prompt_nick';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_prompt_email') . "' WHERE `Name` = 'dbcs_facebook_connect_prompt_email';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_prompt_sex') . "' WHERE `Name` = 'dbcs_facebook_connect_prompt_sex';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_prompt_dob') . "' WHERE `Name` = 'dbcs_facebook_connect_prompt_dob';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_prompt_country') . "' WHERE `Name` = 'dbcs_facebook_connect_prompt_country';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_prompt_city') . "' WHERE `Name` = 'dbcs_facebook_connect_prompt_city';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_prompt_zip') . "' WHERE `Name` = 'dbcs_facebook_connect_prompt_zip';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_import_albums') . "' WHERE `Name` = 'dbcs_facebook_connect_import_albums';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_facebook_friends') . "' WHERE `Name` = 'dbcs_facebook_connect_facebook_friends';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_copy_photo') . "' WHERE `Name` = 'dbcs_facebook_connect_copy_photo';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_set_status_active_oc') . "' WHERE `Name` = 'dbcs_facebook_connect_set_status_active_oc';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_das') . "' WHERE `Name` = 'dbcs_facebook_connect_das';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_default_membership') . "' WHERE `Name` = 'dbcs_facebook_connect_default_membership';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_auto_prompt_nick') . "' WHERE `Name` = 'dbcs_facebook_connect_auto_prompt_nick';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_auto_prompt_email') . "' WHERE `Name` = 'dbcs_facebook_connect_auto_prompt_email';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_auto_prompt_dob') . "' WHERE `Name` = 'dbcs_facebook_connect_auto_prompt_dob';" . "\r\n";

		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_use_geo_ip') . "' WHERE `Name` = 'dbcs_facebook_connect_use_geo_ip';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_dcnty') . "' WHERE `Name` = 'dbcs_facebook_connect_dcnty';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_import_privacy') . "' WHERE `Name` = 'dbcs_facebook_connect_import_privacy';" . "\r\n";
		$sOut .= "UPDATE `sys_options` SET `VALUE` = '" . getParam('dbcs_facebook_connect_show_email') . "' WHERE `Name` = 'dbcs_facebook_connect_show_email';" . "\r\n";

		foreach ($fbIDs as $iID => $fbData) {
			$dID = (int)$fbData['ID'];
			$fbID = $fbData['dbcsFacebookProfile'];
			$sOut .= "UPDATE `Profiles` SET `dbcsFacebookProfile` = '$fbID' WHERE `ID` = '$dID';" . "\r\n";
		}
		file_put_contents($tmpDir . $fn, $sOut);
		return MsgBox(_t('_dbcs_fb_Backup Saved'),4);
	}

	function restoreBackup() {
		$tmpDir = BX_DIRECTORY_PATH_MODULES . 'deano/deanos_facebook_connect/backup/';
		$fn = $_POST['restorefn'];
		// convert old backup file format to new file format.
		$sConv = file_get_contents($tmpDir . $fn);
		$sConv = str_replace('`FacebookProfile`','`dbcsFacebookProfile`',$sConv);
		$sConv = str_replace('bx_facebook_connect','dbcs_facebook_connect',$sConv);
		file_put_contents($tmpDir . $fn, $sConv);
		// end convert. Execute sql to restore.
		execSqlFile($tmpDir . $fn);
		// Clear for dolphin 7.0.3 - 7.0.4
		$files = glob(BX_DIRECTORY_PATH_CACHE . 'sys_options_*.php');
		array_map('unlink', $files);
		// Clear for dolphin versions below 7.0.3
		$sFileName = BX_DIRECTORY_PATH_CACHE . 'sys_options.php';
		if (file_exists($sFileName)) unlink($sFileName);
		return MsgBox(_t('_dbcs_fb_Backup Restored'),4);
	}

	function deleteBackup() {
		// delete backup.
		$tmpDir = BX_DIRECTORY_PATH_MODULES . 'deano/deanos_facebook_connect/backup/';
		$fn = $_POST['restorefn'];
		unlink($tmpDir . $fn);
		return MsgBox(_t('_dbcs_fb_Backup Deleted'),4);
	}

	function setFacebookProfileInfo($me,$meLikes) {
		$aFacebookProfileInfo['link'] = $me['link'];
		$aFacebookProfileInfo['username'] = $me['username'];
		$aFacebookProfileInfo['first_name'] = $me['first_name'];
		$aFacebookProfileInfo['last_name'] = $me['last_name'];
		$aFacebookProfileInfo['name'] = $me['name'];
		$aFacebookProfileInfo['birthday'] = $me['birthday'];
		$aFacebookProfileInfo['sex'] = $me['gender'];
		$aFacebookProfileInfo['proxied_email'] = $me['email'];
		$aFacebookProfileInfo['email'] = $me['email'];
		$sDescMe = str_replace("\n", "<br />",$me['about']);
		$sDescMe = str_replace("\r", "",$sDescMe);
		$aFacebookProfileInfo['about_me'] = $sDescMe;
		$aFacebookProfileInfo['hometown'] = $me['hometown'];
		$aFacebookProfileInfo['location'] = $me['location'];
		$aFacebookProfileInfo['relationship_status'] = $me['relationship_status'];
		$aFacebookProfileInfo['picture'] = $me['picture'];
		$aFacebookProfileInfo['country'] = $me['locale'];
		// Now pull the likes from facebook. New section added on Oct 28, 2010
		$aMovies = array();
		$aMusic = array();
		$aInterests = array();
		$aBooks = array();
		foreach ($meLikes['data'] as $key => $value) {
			if ($meLikes['data'][$key]['category'] == 'Movie') $aMovies[] = $meLikes['data'][$key]['name'];
			if ($meLikes['data'][$key]['category'] == 'Music') $aMusic[] = $meLikes['data'][$key]['name'];
			if ($meLikes['data'][$key]['category'] == 'Interest') $aInterests[] = $meLikes['data'][$key]['name'];
			if ($meLikes['data'][$key]['category'] == 'Book') $aBooks[] = $meLikes['data'][$key]['name'];
		}
		$aFacebookProfileInfo['books'] = implode(",", $aBooks);
		$aFacebookProfileInfo['interests'] = implode(",", $aInterests);
		$aFacebookProfileInfo['movies'] = implode(",", $aMovies);
		$aFacebookProfileInfo['music'] = implode(",", $aMusic);
		return $aFacebookProfileInfo;
	}
	function findNickName($UserName, $sFirstName, $sLastName) {
		// Function finds a free nickname to use for facebook connect
		$sAllowSpaces = getParam('dbcs_facebook_connect_allow_spaces');
		// If all possible fields that are available for username creation are
		// empty then we can't create a username.
		if($UserName == '' && $sFirstName == '' && $sLastName == '') {
			$sNickName = 'none';
			return $sNickName;
		}
		for($iPass = 1; $iPass <= 4; $iPass++) {
			$nickType = getParam('dbcs_facebook_connect_option' . $iPass);
			$dbcheck = '';
			switch ($nickType) {
				case "Username":
				if($UserName != '') {
					if ($sAllowSpaces == '') {
						$sNickName = $this -> _proccesNickName($UserName, true);
						$dbcheck = getID($sNickName);
					} else {
						$sNickName = $this -> _proccesNickName($UserName, false);
						$dbcheck = getID($sNickName);					
					}
				} else {
					$dbcheck = 99999999999;
				}
				break;

				case "FirstName":
				if($sFirstName != '') {
					if ($sAllowSpaces == '') {
						$sNickName = $this -> _proccesNickName($sFirstName, true);
						$dbcheck = getID($sNickName);
					} else {
						$sNickName = $this -> _proccesNickName($sFirstName, false);
						$dbcheck = getID($sNickName);					
					}
				} else {
					$dbcheck = 99999999999;
				}
				break;
				case "FirstName_LastName":
				if($sFirstName != '' && $sLastName != '') {
					if ($sAllowSpaces == '') {
						$sNickName = $this -> _proccesNickName($sFirstName, true) . "_" . $this -> _proccesNickName($sLastName, true);
						$dbcheck = getID($sNickName);
					} else {
						$sNickName = $this -> _proccesNickName($sFirstName, false) . " " . $this -> _proccesNickName($sLastName, false);
						$dbcheck = getID($sNickName);
					}
				} else {
					$dbcheck = 99999999999;
				}
				break;
				case "LastName":
				if($sLastName != '') {
					if ($sAllowSpaces == '') {							
						$sNickName = $this -> _proccesNickName($sLastName, true);
						$dbcheck = getID($sNickName);
					} else {
						$sNickName = $this -> _proccesNickName($sLastName, false);
						$dbcheck = getID($sNickName);					
					}
				} else {
					$dbcheck = 99999999999;
				}
				break;
			}
			if ($dbcheck == 0) {
				return $sNickName;
			}
		}
		// If we made it here, then all nick combinations were in use or not valid.
		$sNickName = 'none';
		return $sNickName;
	}
	
       /**
         * Function will clear all unnecessary sybmols from profile's nickname;
         *
         * @param  : $sProfileName (string) - profile's nickname;
         * @return : (string) - cleared nickname;
         */
        function _proccesNickName($sProfileName, $bIgnoreSpaces = false)
        {
            $sProfileName = preg_replace("/^http:\/\/|^https:\/\/|\/$/", '', $sProfileName);
            $sProfileName = str_replace('/', '_', $sProfileName);
            if(!getParam('dbcs_facebook_connect_allow_spaces') || !$bIgnoreSpaces) $sProfileName = str_replace(' ', '_', $sProfileName);
            $sProfileName = str_replace('.', '', $sProfileName);
            $sProfileName = str_replace("'", '', $sProfileName);

			// convert accented characters.
            $sProfileName = $this->_normalize($sProfileName);


            return $sProfileName;
        }

		function _normalize ($string) {
			$table = array(
		       'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
				'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
				'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
				'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
				'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
				'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
				'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
				'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
				);
		    return strtr($string, $table);
		}
		
	function findProfileID($iFacebookID,$sEmail) {
		// Checks to see if there is an existing profile matching passed facebook ID.
		// If not found will try a match on email address if that option is turned on.
		$q = "SELECT * FROM `Profiles` WHERE `dbcsFacebookProfile`='" . $iFacebookID . "' limit 1";
		$dbr = db_res($q);
		$dbrow = mysqli_fetch_array($dbr);
		$iProfileId = intval($dbrow['ID']);
		if ($iProfileId == 0) {
			// Try for a match on email address.
			if (getParam('dbcs_facebook_connect_match_email') == 'on') {
				// Make sure a email was passed. If not then skip.
				if (strstr($sEmail,'@')) { 
					$q = "SELECT `ID` FROM `Profiles` WHERE `Email`='" . $sEmail . "' limit 1";
					$dbr = db_res($q);
					$dbrow = mysqli_fetch_array($dbr);
					$iProfileId = intval($dbrow['ID']);
					if($iProfileId > 0) {
						// We matched on email, so update members profile with passed facebook ID.
						$q = "UPDATE `Profiles` SET `dbcsFacebookProfile`='" . $iFacebookID . "' WHERE `ID`='" . $iProfileId . "'";
						$dbr = db_res($q);
					}
				}
			}
		}
		// Return the found profile ID or 0 if not found.
		return $iProfileId;
	}

	function getSelect($sName, $sOptions) {
		if($GLOBALS['site']['ver'] == '7.1') {
$sCode = <<<CODE
<div class="input_wrapper input_wrapper_select ">
  <select name="{$sName}" class="form_input_select bx-def-font">
{$sOptions}
  </select>
</div>
CODE;
		} else {
$sCode = <<<CODE
<div class="input_wrapper input_wrapper_select">
  <select name="{$sName}" class="form_input_select">
{$sOptions}
  </select>
  <div class="input_close input_close_select"></div>
</div>
CODE;
		}
		return $sCode;
	}

	function getInput($sType, $sName, $sValue = '', $sStyle = '') {
		// There are other types if inputs. But only the ones i use are processed.
		if($GLOBALS['site']['ver'] == '7.1') {
			switch ($sType) {
			    case 'text':
				case 'password':
$sCode = <<<CODE
<div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
  <input type="text" value="{$sValue}" name="{$sName}" class="form_input_text bx-def-font">
</div>
CODE;
			        break;
			    case 'submit':
$sCode = <<<CODE
<div class="input_wrapper input_wrapper_submit" style="{$sStyle}">
  <div class="button_wrapper">
    <input type="submit" value="{$sValue}" name="{$sName}" class="form_input_submit bx-btn">
  </div>
</div>
CODE;
			        break;
			    case 'hidden':
$sCode = <<<CODE
<input type="hidden" value="{$sValue}" name="{$sName}" class="form_input_hidden bx-def-font">
CODE;
			        break;
			}			
		} else {
			switch ($sType) {
			    case 'text':
				case 'password':
$sCode = <<<CODE
<div class="input_wrapper input_wrapper_text">
  <input type="text" value="{$sValue}" name="{$sName}" class="form_input_text">
  <div class="input_close input_close_text"></div>
</div>
CODE;
			        break;
			    case 'submit':
$sCode = <<<CODE
<div class="input_wrapper input_wrapper_submit" style="{$sStyle}">
  <div class="button_wrapper">
    <input type="submit" value="{$sValue}" name="{$sName}" class="form_input_submit">
    <div class="button_wrapper_close"></div>
  </div>
  <div class="input_close input_close_submit"></div>
</div>
CODE;
			        break;
			    case 'hidden':
$sCode = <<<CODE
<input type="hidden" value="{$sValue}" name="{$sName}" class="form_input_hidden">
CODE;
			        break;
			}			
		}
		return $sCode;
	}
/********************************************************/
    }
?>