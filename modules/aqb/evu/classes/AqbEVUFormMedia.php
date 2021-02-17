<?php
/***************************************************************************
*
*     copyright            : (C) 2009 AQB Soft
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

bx_import ('BxDolFormMedia');

/**
 * Base class for form which is using a lot of media uploads
 */ 
class AqbEVUFormMedia extends BxDolFormMedia {
	var $_oVideoUploader = null; 
	
	function __construct (&$oTemplate) {
		$this -> _oTmpl = $oTemplate;
		$this -> _oVideoUploader = BxDolModule::getInstance('BxVideosUploader');
		parent::__construct (array());
    }

	function getYoutubeArray($sUrl){
		if (is_null($this -> _oVideoUploader)) $this -> _oVideoUploader = BxDolModule::getInstance('BxVideosUploader');
		
 		$sVideoId = $this -> _oVideoUploader -> embedGetStringPart(trim($sUrl) . "/", "=", "/");
		
		if(empty($sVideoId)) return false;
		
		$sVideoData = $this -> _oVideoUploader -> embedReadUrl(str_replace("#video#", $sVideoId, YOUTUBE_VIDEO_RSS));
		$sVideoData = $this -> _oVideoUploader -> embedGetTagContents($sVideoData, "entry");
		if(empty($sVideoData)) return false;
				
		$sTitle = $this -> _oVideoUploader -> embedGetTagContents($sVideoData, "media:title");
		$sDesc = $this -> _oVideoUploader -> embedGetTagContents($sVideoData, "media:description");
		$sTags = $this -> _oVideoUploader -> embedGetTagContents($sVideoData, "media:keywords");
		$sImage = $this -> _oVideoUploader -> embedGetTagAttributes($sVideoData, "media:thumbnail", "url");
		$iDuration = $this -> _oVideoUploader -> embedGetTagAttributes($sVideoData, "yt:duration", "seconds");
		
		if(empty($sTitle)) return false;
		
		$sAuthorCheck = $this -> _oVideoUploader -> checkAuthorBeforeAdd();
		
		if(empty($sAuthorCheck)) {
			
			$sEmbedThumbUrl = getEmbedThumbnail($this -> _oVideoUploader -> _getAuthorId(), $sImage);
			
			if($sEmbedThumbUrl){
				return array('video' => $sVideoId, 'title' => $sTitle, 'description' => $sDesc, 'tags' => $sTags, 'duration' => $iDuration, 'image' => $sEmbedThumbUrl, 'type' => "embed");
			}
	
		}
		
		return false;
	}
	
	function uploadVideos ($sTag, $sCat, $sName = 'videos', $sTitle = 'videos_titles', $sTitleAlt = 'title')  {
		global $sModule;
		$sModule = 'video';
		$aRet = array ();
		
		$aTitles = $this -> getCleanValue($sTitle);

		if (isset($_POST['you_tube_videos']) && is_array($_POST['you_tube_videos']))
		{	
			foreach($_POST['you_tube_videos'] as $k => $v){	
				$aResult = $this -> getYoutubeArray($v);
				
				if ($aResult !== false) {
					$iId = embedVideo($this -> _oVideoUploader -> _getAuthorId(), $aResult['video'], $aResult['duration']);
					if ($iId) $this -> _oVideoUploader -> initVideoFile($iId, isset($aResult['title']) ? $aResult['title'] : '', $sCat, isset($aResult['tags']) ? $aResult['tags'].','. $sTag : $sTag, isset($aResult['desc']) ? $aResult['desc'] : '');
				
				    $sAlbum = $this -> _oVideoUploader -> oModule -> _oDb -> getParam('sys_album_default_name');
		            $this -> _oVideoUploader -> addObjectToAlbum($this -> _oVideoUploader -> oModule -> oAlbums, $sAlbum, $iId, false);
					
					if ($iId){	
						$aRet[] = $iId;
					}	 
				}
			}	
		}	
       return $aRet; 		
    }
}

?>