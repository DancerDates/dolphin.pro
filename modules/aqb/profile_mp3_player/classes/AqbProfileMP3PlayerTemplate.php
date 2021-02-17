<?php
/***************************************************************************
*
*     copyright            : (C) 2013 AQB Soft
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

class AqbProfileMP3PlayerTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function __construct(&$oConfig, &$oDb) {
	    parent::__construct($oConfig, $oDb);
	}

	function getMP3Player($iProfileID, $iAlbumToPlay, $aFiles, $bAutoplay, $bShowSettingsForm) {
		if (!$aFiles && !$bShowSettingsForm) return ''; //nothing to show

		$aProfileAlbums = array();
		if ($bShowSettingsForm) {
			$oAlbums = new BxDolAlbums('bx_sounds', $iProfileID);
			$aAlbumParams = array('owner' => $iProfileID, 'show_empty' => TRUE, 'hide_default' => FALSE);
	        $aAlbumsList = $oAlbums->getAlbumList($aAlbumParams, 1, 100);
	        if ($aAlbumsList)
	        foreach ($aAlbumsList as $aAlbum) {
	        	$aProfileAlbums[] = array(
	        		'album_id' => $aAlbum['ID'],
	        		'album_name' => $aAlbum['Caption'],
	        		'selected' => $aAlbum['ID'] == $iAlbumToPlay ? 'selected="selected"' : '',
	        	);
	        }
		}

		$iEmbedWidth = $this->_oDb->getParam('aqb_profile_mp3_player_embed_width');
		$iEmbedHeight = $this->_oDb->getParam('aqb_profile_mp3_player_embed_height');

		return $this->parseHtmlByName('mp3player.html', array(
			'bx_if:settings_form' => array(
				'condition' => $bShowSettingsForm,
				'content' => array(
					'save_settings_url' => BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri().'action_save_settings/',
					'bx_repeat:albums' => $aProfileAlbums,
					'autoplay_checked' => $bAutoplay ? 'checked="checked"' : '',
					'embed_width' => $iEmbedWidth,
					'embed_height' => $iEmbedHeight,
					'profile_id' => $iProfileID,
				),
			),
			'loading_box' => LoadingBox('aqb_mp3_player_loading'),
			'player' => $this->getPlayerObject($iProfileID, $aFiles, $bAutoplay),
		));
	}

	function getPlayerObject($iProfileID, $aFiles, $bAutoplay) {
		$aFilesList = array();
		if (empty($aFiles)) return '';

		//$sSkin = 'blue.monday';
		$sSkin = $this->_oDb->getParam('aqb_profile_mp3_player_skin');
		$iPopupWidth = $this->_oDb->getParam('aqb_profile_mp3_player_popup_width');
		$iPopupHeight = $this->_oDb->getParam('aqb_profile_mp3_player_popup_height');

		for($i = 0; $i < count($aFiles) - 1; $i++) {
			$aFilesList[] = array(
				'id' => $aFiles[$i]['ID'],
				'title' => addslashes(htmlspecialchars($aFiles[$i]['Title'])),
			);
		}

		return $this->parseHtmlByName('player_object.html', array(
			'ui_js' => $this->addJs('jquery.ui.all.min.js', true),
			'sortable_js' => $this->addJs('jquery.ui.sortable.min.js', true),
			'profile_id' => $iProfileID,
			'jplayer_url' => $this->_oConfig->getHomeUrl().'jplayer/',
			'bx_repeat:files' => $aFilesList,
			'last_id' => $aFiles[count($aFiles) - 1]['ID'],
			'last_title' => addslashes(htmlspecialchars($aFiles[count($aFiles) - 1]['Title'])),
			'bx_if:autoplay' => array(
				'condition' => $bAutoplay,
				'content' => array(),
			),
			'bx_if:save_play_list_order' => array(
				'condition' => $iProfileID == getLoggedId(),
				'content' => array(),
			),
			'skinnable_content' => $this->parseHtmlByName('player_object_skinnable_'.$sSkin.'.html', array(
				'jplayer_url' => $this->_oConfig->getHomeUrl().'jplayer/',
				'profile_id' => $iProfileID,
			)),
			'popup_width' => $iPopupWidth,
			'popup_height' => $iPopupHeight,
		));
	}

	function getMP3PlayerEmbed($iProfileID, $aFiles, $bAutoplay) {
		return $this->parseHtmlByName('mp3player_popup.html', array(
			'page_header' => _t('_aqb_profile_mp3_player_block_caption'),
			'player' => $this->getPlayerObject($iProfileID, $aFiles, $bAutoplay),
			'site_url' => BX_DOL_URL_ROOT,
			'jquery_inline' => $this->addJs('jquery.js', true),
			'common_css_inline' => $this->addCss('common.css', true),
			'bx_if:iframe_embed' => array(
				'condition' => isset($_GET['embed']),
				'content' => array(),
			),
		));
	}
}