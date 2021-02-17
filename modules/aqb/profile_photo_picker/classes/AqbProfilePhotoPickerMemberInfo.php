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
bx_import('BxDolMemberInfo');
require_once(BX_DIRECTORY_PATH_MODULES.'boonex/photos/classes/BxPhotosSearch.php');
require_once(BX_DIRECTORY_PATH_MODULES.'boonex/photos/classes/BxPhotosMemberInfo.php');

class AqbProfilePhotoPickerMemberInfo extends BxDolMemberInfo {
    var $_oPhotosSearch;
    var $_oPhotosMemberInfo;

    public function __construct($aObject) {
        parent::__construct($aObject);
        $this->_oPhotosSearch = new BxPhotosSearch();
        $this->_oPhotosMemberInfo = new BxPhotosMemberInfo($aObject);
    }

    public function get($aData) {
        switch ($this->_sObject) {
            case 'aqb_profile_photo_picker_thumb':
                $aProfile = getProfileInfo($aData['ID']);
                if ($aProfile['aqb_profile_photo_id']) {
                    return $this->_oPhotosSearch->getImgUrl($aProfile['aqb_profile_photo_id'], 'thumb');
                } else {
                    $this->_oPhotosMemberInfo->_sObject = 'bx_photos_thumb';
                    return $this->_oPhotosMemberInfo->get($aData);
                }
            case 'aqb_profile_photo_picker_icon':
                $aProfile = getProfileInfo($aData['ID']);
                if ($aProfile['aqb_profile_photo_id']) {
                    return $this->_oPhotosSearch->getImgUrl($aProfile['aqb_profile_photo_id'], 'icon');
                } else {
                    $this->_oPhotosMemberInfo->_sObject = 'bx_photos_icon';
                    return $this->_oPhotosMemberInfo->get($aData);
                }
        }
        return parent::get($aData);
    }

    public function isAvatarSearchAllowed() {
        return false;
    }
}