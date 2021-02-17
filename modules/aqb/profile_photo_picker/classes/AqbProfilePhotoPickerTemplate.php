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

bx_import('BxDolModuleTemplate');
bx_import('BxDolAlbums');

class AqbProfilePhotoPickerTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function __construct(&$oConfig, &$oDb) {
	    parent::__construct($oConfig, $oDb);
	}

	function getAlbumsList($iProfile, $sCurrentPhoto) {
		$oAlbums = new BxDolAlbums('bx_photos', $iProfile);
		$iAlbums = $oAlbums->getAlbumCount(array(
			'owner' => $iProfile,
			'album_status' => 'active',
			'show_empty' => false,
			'hide_default' => true,
		));
		if (!$iAlbums) return MsgBox(_t('_aqb_ppp_no_albums'));

		$aAlbums = $oAlbums->getAlbumList(array(
			'owner' => $iProfile,
			'album_status' => 'active',
			'show_empty' => false,
			'hide_default' => true,
		));

		require_once(BX_DIRECTORY_PATH_MODULES.'boonex/photos/classes/BxPhotosSearch.php');
		$oPhotosSearch = new BxPhotosSearch();

		foreach ($aAlbums as $iKey => $aAlbum) {
			$aPhotos = $this->_oDb->getPhotosOfAlbum($aAlbum['ID']);
			$aPhotosTmpl = array();
			foreach ($aPhotos as $aPhoto) {
				$aPhotosTmpl[] = array(
					'id' => $aPhoto['ID'],
					'title' => htmlspecialchars($aPhoto['Title']),
					'img' => $oPhotosSearch->getImgUrl($aPhoto['Hash'], 'browse'),
					'current' => $sCurrentPhoto == $aPhoto['Hash'] ? 'aqb_ppp_selected_photo' : '',
					'profile' => $iProfile,
				);
			}
			$aAlbums[$iKey]['photos_list'] = $this->parseHtmlByName('photos.html', array(
				'bx_repeat:photos' => $aPhotosTmpl,
			));
		}

		return $this->parseHtmlByName('albums.html', array(
			'bx_repeat:albums' => $aAlbums,
		));
	}

	function getProfilePhotoBlock($sPhotoImg, $sAlbumLink) {
		return $this->parseHtmlByName('profile_photo.html', array('img' => $sPhotoImg, 'link' => $sAlbumLink));
	}
}