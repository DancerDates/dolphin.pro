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

bx_import('BxDolModule');
bx_import('BxDolPrivacy');
if (!defined('BX_DOL_PG_HIDDEN')) define('BX_DOL_PG_HIDDEN', '8');

class AqbProfilePhotoPickerModule extends BxDolModule {
	/**
	 * Constructor
	 */
	function __construct($aModule) {
	    parent::__construct($aModule);
	}

    function serviceGetProfileActionButton($iProfile, $iViewer) {
    	if (($iProfile == $iViewer || isAdmin()) && defined('BX_PROFILE_PAGE')) {
            $this->_oTemplate->addJs('main.js');
            return _t('_aqb_ppp_action_button');
        }
        return false;
    }

    function actionGetPhotoPicker($iProfile) {
        if (!getLoggedId()) return;
        $aProfile = getProfileInfo(isAdmin() ? $iProfile : getLoggedId());
        return PopupBox('aqb_ppp_popup', _t('_aqb_ppp_popup_header'), $this->_oTemplate->parseHtmlByName('default_padding.html', array('content' => $this->_oTemplate->getAlbumsList($aProfile['ID'], $aProfile['aqb_profile_photo_id']))).'<style>#aqb_ppp_popup{display:none;}</style>');
    }

    function actionSetPhoto($iProfile, $iPhoto) {
        $iPhoto = intval($iPhoto);

        if (!getLoggedId()) return 'Must be logged member';

        if (!$iPhoto) return 'Photo not found';

        $aProfile = getProfileInfo(isAdmin() ? $iProfile : getLoggedId());
        $sHash = $this->_oDb->checkPhotoExistence($aProfile['ID'], $iPhoto);
        if ($sHash) {
            $this->_oDb->setProfilePhoto($aProfile['ID'], $sHash);
        }
    }

    function serviceProfilePhotoBlock($iProfile) {
        require_once(BX_DIRECTORY_PATH_MODULES.'boonex/photos/classes/BxPhotosSearch.php');
        $oPhotosSearch = new BxPhotosSearch();

        $aProfile = getProfileInfo($iProfile);
        if ($aProfile['aqb_profile_photo_id']) {
            $sImgUrl = $oPhotosSearch->getImgUrl($aProfile['aqb_profile_photo_id'], 'file');
            $sOwner = getUsername($iProfile);
            $sCaption = str_replace('{nickname}', $sOwner, $oPhotosSearch->oModule->_oConfig->getGlParam('profile_album_name'));
            $sAlbumLink = $oPhotosSearch->getCurrentUrl('album', 0, uriFilter($sCaption)) . '/owner/' . $sOwner;

            return $this->_oTemplate->getProfilePhotoBlock($sImgUrl, $sAlbumLink);
        } else {
            return $oPhotosSearch->serviceProfilePhotoBlock(array('PID' => $iProfile));
        }
    }

    function serviceReplaceProfilePhotoRestriction(&$aRestriction) {
        $iProfile = $aRestriction['owner']['value'];
        if ($iProfile) {
            $aProfile = getProfileInfo($iProfile);
            if ($aProfile['aqb_profile_photo_id']) {
                $aRestriction['album'] = array(
                    'value' => $aProfile['aqb_profile_photo_id'], 'field' => 'Hash', 'operator' => '=', 'paramName' => 'Hash', 'table' => 'bx_photos_main'
                );
            }
        }
    }
}