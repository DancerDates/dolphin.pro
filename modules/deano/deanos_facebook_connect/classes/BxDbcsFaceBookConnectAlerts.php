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
    class BxDbcsFaceBookConnectAlerts extends BxDolAlertsResponse 
    {
        var $oModule;
        var $aModule;
        var $iFacebookUid;

        /**
         * Class constructor;
         */
        function BxDbcsFaceBookConnectAlerts() {
            $this -> oModule = BxDolModule::getInstance('BxDbcsFaceBookConnectModule');            
        }

		function response(&$o) 
    	{
			// If module is not configured, then we do not do any of these.
			if( !getParam('dbcs_facebook_connect_api_key') || !getParam('dbcs_facebook_connect_secret_key') ) {
				return;
			}
            if ( $o -> sUnit == 'profile' ) {
                switch ( $o -> sAction ) {
                    case 'logout' :
/*
						$iMemID = (int)$o -> iObject;
						$sLogoutRedirect = getParam('dbcs_facebook_connect_logout_redirect');
						if(getParam('dbcs_facebook_connect_fb_logout')) {
							// Get the facebook logout url from database
							if ($iMemID > 0) {
								$sLogoutUrl = $this -> oModule -> _oDb ->getLogoutURL($iMemID);
								// Remove Facebook logout url from database.
								$this -> oModule -> _oDb ->saveLogoutURL($iMemID,'');
							}
							$this -> oModule -> deleteCookies();
							@session_destroy();
							// If the logout url was stored, then we had a facebook logon, so log out of facebook.
							// If not, then skip the facebook logout.
							if ($sLogoutUrl != '') {
								if($sLogoutRedirect) {
									// Logout of fb with redirect to different page.
									$aUrl1 = parse_url($sLogoutUrl);
									$aUrl2 = parse_str($aUrl1['query'], $output);
									$sAccessToken = $output['access_token'];
									$sNext = urlencode(BX_DOL_URL_ROOT . 'modules/deano/deanos_facebook_connect/logout.php?redirect=' . $sLogoutRedirect);
									$sNewUrl = 'https://www.facebook.com/logout.php?next=' . $sNext . '&access_token=' . $sAccessToken;
									setcookie('memberID', '', time() - 96 * 3600, '/');
									setcookie('memberPassword', '', time() - 96 * 3600, '/');
									unset($_COOKIE['memberID']);
									unset($_COOKIE['memberPassword']);
									header("Location: " . $sNewUrl);
									exit;
								} else {
									// Logout of fb with normal redirect.
									header("Location: " . $sLogoutUrl);
									exit;
								}
							} else {
								setcookie('memberID', '', time() - 96 * 3600, '/');
								setcookie('memberPassword', '', time() - 96 * 3600, '/');
								unset($_COOKIE['memberID']);
								unset($_COOKIE['memberPassword']);
								if($sLogoutRedirect) {
									// Normal logout with redirect.
									header("Location: " . BX_DOL_URL_ROOT . 'modules/deano/deanos_facebook_connect/logout.php?redirect=' . $sLogoutRedirect);
									exit;
								}
							}

						} else {
							// Normal logout with redirect.
							@session_destroy();
							setcookie('memberID', '', time() - 96 * 3600, '/');
							setcookie('memberPassword', '', time() - 96 * 3600, '/');
							unset($_COOKIE['memberID']);
							unset($_COOKIE['memberPassword']);
							if($sLogoutRedirect) {
								header("Location: " . BX_DOL_URL_ROOT . 'modules/deano/deanos_facebook_connect/logout.php?redirect=' . $sLogoutRedirect);
								exit;
							}
						}

*/


						$iMemID = (int)$o -> iObject;
						$sLogoutRedirect = getParam('dbcs_facebook_connect_logout_redirect');
						if(getParam('dbcs_facebook_connect_fb_logout')) {
							// Get the facebook logout url from database
							if ($iMemID > 0) {
								$sLogoutUrl = $this -> oModule -> _oDb ->getLogoutURL($iMemID);
								// Remove Facebook logout url from database.
								$this -> oModule -> _oDb ->saveLogoutURL($iMemID,'');
							}
							if ($sLogoutUrl != '') {
								if($sLogoutRedirect) {
									// Logout of fb with redirect to different page.
									$aUrl1 = parse_url($sLogoutUrl);
									$aUrl2 = parse_str($aUrl1['query'], $output);
									$sAccessToken = $output['access_token'];
									$sNext = urlencode(BX_DOL_URL_ROOT . 'modules/deano/deanos_facebook_connect/logout.php?redirect=' . $sLogoutRedirect);
									$sNewUrl = 'https://www.facebook.com/logout.php?next=' . $sNext . '&access_token=' . $sAccessToken;
									setcookie('memberID', '', time() - 3600, '/');
									setcookie('memberPassword', '', time() - 3600, '/');
									unset($_COOKIE['memberID']);
									unset($_COOKIE['memberPassword']);
									$this -> oModule -> deleteCookies();
									@session_destroy();
									header("Location: " . $sNewUrl);
									exit;
								} else {
									// Logout of fb with normal redirect.
									$aUrl1 = parse_url($sLogoutUrl);
									$aUrl2 = parse_str($aUrl1['query'], $output);
									$sAccessToken = $output['access_token'];
									$sNext = urlencode(BX_DOL_URL_ROOT);
									$sNewUrl = 'https://www.facebook.com/logout.php?next=' . $sNext . '&access_token=' . $sAccessToken;
									setcookie('memberID', '', time() - 3600, '/');
									setcookie('memberPassword', '', time() - 3600, '/');
									unset($_COOKIE['memberID']);
									unset($_COOKIE['memberPassword']);
									$this -> oModule -> deleteCookies();
									@session_destroy();
									header("Location: " . $sNewUrl);
									exit;
								}
							}

						} else {
							// We are not logging out of facebook. 
							if($sLogoutRedirect) {
								setcookie('memberID', '', time() - 3600, '/');
								setcookie('memberPassword', '', time() - 3600, '/');
								unset($_COOKIE['memberID']);
								unset($_COOKIE['memberPassword']);
								$this -> oModule -> deleteCookies();
								@session_destroy();
								header("Location: " . BX_DOL_URL_ROOT . $sLogoutRedirect);
								exit;
							}
						}




                    break;
                    case 'delete' :
						// Only do a logout of facebook if member is unregistering their own account.
						$iMemID = (int)$o -> iObject;
						if($_SERVER['REQUEST_URI'] == '/unregister.php') {
							$sUnregisterRedirect = getParam('dbcs_facebook_connect_unregister_redirect');
							if(getParam('dbcs_facebook_connect_fb_logout')) {
								// Get the facebook logout url from database
								if ($iMemID > 0) {
									$sLogoutUrl = $this -> oModule -> _oDb ->getLogoutURL($iMemID);
									// Remove Facebook logout url from database.
									$this -> oModule -> _oDb ->saveLogoutURL($iMemID,'');
								}
								if ($sLogoutUrl != '') {
									if($sUnregisterRedirect) {
										// Logout of fb with redirect to different page.
										$aUrl1 = parse_url($sLogoutUrl);
										$aUrl2 = parse_str($aUrl1['query'], $output);
										$sAccessToken = $output['access_token'];
										$sNext = urlencode(BX_DOL_URL_ROOT . 'modules/deano/deanos_facebook_connect/logout.php?redirect=' . $sUnregisterRedirect);
										$sNewUrl = 'https://www.facebook.com/logout.php?next=' . $sNext . '&access_token=' . $sAccessToken;
										setcookie('memberID', '', time() - 3600, '/');
										setcookie('memberPassword', '', time() - 3600, '/');
										unset($_COOKIE['memberID']);
										unset($_COOKIE['memberPassword']);
										$this -> oModule -> deleteCookies();
										@session_destroy();
										$this -> oModule -> _oDb ->deleteExtraData($iMemID);
										header("Location: " . $sNewUrl);
										exit;
									} else {
										// Logout of fb with normal redirect.
										$aUrl1 = parse_url($sLogoutUrl);
										$aUrl2 = parse_str($aUrl1['query'], $output);
										$sAccessToken = $output['access_token'];
										$sNext = urlencode(BX_DOL_URL_ROOT);
										$sNewUrl = 'https://www.facebook.com/logout.php?next=' . $sNext . '&access_token=' . $sAccessToken;
										setcookie('memberID', '', time() - 3600, '/');
										setcookie('memberPassword', '', time() - 3600, '/');
										unset($_COOKIE['memberID']);
										unset($_COOKIE['memberPassword']);
										$this -> oModule -> deleteCookies();
										@session_destroy();
										header("Location: " . $sNewUrl);
										$this -> oModule -> _oDb ->deleteExtraData($iMemID);
										exit;
									}
								}

							} else {
								// We are not logging out of facebook. 
								if($sUnregisterRedirect) {
									setcookie('memberID', '', time() - 3600, '/');
									setcookie('memberPassword', '', time() - 3600, '/');
									unset($_COOKIE['memberID']);
									unset($_COOKIE['memberPassword']);
									$this -> oModule -> deleteCookies();
									@session_destroy();
									$this -> oModule -> _oDb ->deleteExtraData($iMemID);
									header("Location: " . BX_DOL_URL_ROOT . $sUnregisterRedirect);
									exit;
								}
							}
						}
						// Member not unregistering their own account. Either deleted by admin or cron.
						// Just remove the data from the database.
						$this -> oModule -> _oDb ->deleteExtraData($iMemID);
                    break;

                    case 'join' :

					
					break;

/*
No longer going to do this.
					case 'edit' :
						// here we will check to make sure the members id matches the photo album.
						// there is a bug in dolphin that causes loss of the photos in the photo album 
						// if the nickname changes.
						// this is intended to get around that problem.
						$iMemID = getLoggedId();
						if($iMemID > 0) {
							$sNickName = getNickName($iMemID);
							$sOldNickName = $this -> oModule -> _oDb ->getOldNick($iMemID);
							if ($sNickName != $sOldNickName && $sOldNickName != '') {
								$sCaption = $sNickName . "'s photos";
								$sURI = $sNickName . "-s-photos";
								$sOldCaption = $sOldNickName . "'s photos";
								$this -> oModule -> _oDb ->updateAlbumName($iMemID, $sCaption, $sURI, $sOldCaption);
								$this -> oModule -> _oDb -> saveOldNick($iMemID, $sNickName);
							}
						}
					break;
*/
                    default :
                }
            }

            if ( $o -> sUnit == 'module' ) {
                switch ( $o -> sAction ) {
                    case 'install' :




                        break;

                    default :
                }
            }

        }
    }
?>