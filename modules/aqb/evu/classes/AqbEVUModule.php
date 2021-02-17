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

bx_import('BxDolModule');
bx_import('BxDolPageView');
bx_import('BxDolAlbums');

require_once( BX_DIRECTORY_PATH_MODULES . 'boonex/videos/classes/BxVideosUploader.php' );

class AqbEVUModule extends BxDolModule {
	
	/**
	 * Constructor
	 */
	function __construct($aModule) {
	    parent::__construct($aModule);	
		$this -> _oVideoUploader = BxDolModule::getInstance('BxVideosUploader');
		$this -> iUserId = $GLOBALS['logged']['member'] || $GLOBALS['logged']['admin'] ? $_COOKIE['memberID'] : 0;
	}

	function serviceGetExtendedTemplate($aVarsUpload){
		return $this -> _oTemplate -> getExtendedVideoTemplate($aVarsUpload);
	}
	
	function getYoutubeArray($sUrl){
		global $sModulesPath, $sModulesUrl, $sFilesDir, $sFilesPath, $sFilesUrl;

		$GLOBALS['sModuleUrl'] = $GLOBALS['sModulesUrl'] . $GLOBALS['sModule'] . "/";		
		$GLOBALS['sFilesUrl'] = $GLOBALS['sModuleUrl'] . $GLOBALS['sFilesDir'];
		$GLOBALS['sFilesPath'] = $GLOBALS['sModulesPath'] . $GLOBALS['sModule'] . "/" . $GLOBALS['sFilesDir'];

		if (is_null($this -> _oVideoUploader)) 
			$this -> _oVideoUploader = BxDolModule::getInstance('BxVideosUploader');
		
 		$aVideoId = preg_match('/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i', trim($sUrl), $aMatches);
		if ($aMatches[2]) 
			$sVideoId = $aMatches[2];
		
		if(empty($sVideoId)) return false;
		
		$aSiteInfo = $this -> getSiteInfo('http://www.youtube.com/watch?v=' . $sVideoId, array(
            'duration' => array(),
            'thumbnailUrl' => array('tag' => 'link', 'content_attr' => 'href'),
        ));
		
        $aSiteInfo['duration'] = $this -> parseDuration($aSiteInfo['duration']);

        $sTitle = $aSiteInfo['title'];
        $sDesc = $aSiteInfo['description'];
        $sTags = $aSiteInfo['keywords'];
        $sImage = $aSiteInfo['thumbnailUrl'];
        $iDuration = (int)$aSiteInfo['duration'];

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
	
	private function getSiteInfo($sSourceUrl, $aProcessAdditionalTags = array())
	{
	    $aResult = array();
	    $sContent = bx_file_get_contents($sSourceUrl);

	    if ($sContent) {
	        $sCharset = '';
	        preg_match("/<meta.+charset=([A-Za-z0-9-]+).+>/i", $sContent, $aMatch);
	        if (isset($aMatch[1]))
	            $sCharset = $aMatch[1];

	        preg_match("/<title>(.*)<\/title>/i", $sContent, $aMatch);
	        $aResult['title'] = $aMatch[1];

	        $aResult['description'] = $this -> actionParseHtmlTag($sContent, 'meta', 'name', 'description', 'content', $sCharset);
	        $aResult['keywords'] = $this -> actionParseHtmlTag($sContent, 'meta', 'name', 'keywords', 'content', $sCharset);

	        if ($aProcessAdditionalTags) {

	            foreach ($aProcessAdditionalTags as $k => $a) {
	                $aResult[$k] = $this -> actionParseHtmlTag(
	                    $sContent, 
	                    isset($a['tag']) ? $a['tag'] : 'meta', 
	                    isset($a['name_attr']) ? $a['name_attr'] : 'itemprop', 
	                    isset($a['name']) ? $a['name'] : $k, 
	                    isset($a['content_attr']) ? $a['content_attr'] : 'content', 
	                    $sCharset); 
	            }

	        }
	    }

	    return $aResult;
	}

	private function actionParseHtmlTag ($sContent, $sTag, $sAttrNameName, $sAttrNameValue, $sAttrContentName, $sCharset = false)
	{
	    if (!preg_match("/<{$sTag}\s+{$sAttrNameName}[='\" ]+{$sAttrNameValue}['\"]\s+{$sAttrContentName}[='\" ]+([^<]*)['\"][\/\s]*>/i", $sContent, $aMatch) || !isset($aMatch[1]))
	        preg_match("/<{$sTag}\s+{$sAttrContentName}[='\" ]+([^<]*)['\"]\s+{$sAttrNameName}[='\" ]+{$sAttrNameValue}['\"][\/\s]*>/i", $sContent, $aMatch);

	    $s = isset($aMatch[1]) ? $aMatch[1] : '';

	    if ($s && $sCharset)
	        $s = mb_convert_encoding($s, 'UTF-8', $sCharset);

	    return $s;
	}

	private function parseDuration($sContent)
	{	
		if (!$sContent || !is_string($sContent) || 'P' != strtoupper($sContent[0])) 
			return false;

	    $a = array('D' => 86400, 'H' => 3600, 'M' => '60', 'S' => 1);
	    $iTotal = 0;
	    foreach ($a as $sLetter => $iSec)
	        if (preg_match('/(\d+)[' . $sLetter . ']{1}/i', $sContent, $aMatch) && $aMatch[1])
	            $iTotal += (int)$aMatch[1] * $iSec;
	    return $iTotal;
	}
	
	function serviceUploadVideos ($oParent, $sTag, $sCat, $sName = 'videos', $sTitle = 'videos_titles', $sTitleAlt = 'title')  {
		global $sModule;
		$sModule = 'video';
		$aRet = array ();
		
		$aTitles = $oParent -> getCleanValue($sTitle);

		if (isset($_POST['you_tube_videos']) && is_array($_POST['you_tube_videos']))
		{	
			foreach($_POST['you_tube_videos'] as $k => $v){	
				$aResult = $this -> getYoutubeArray($v);
				
				if ($aResult !== false) {
					$iId = embedVideo($this -> _oVideoUploader -> _getAuthorId(), $aResult['video'], $aResult['duration']);
					if ($iId) $this -> _oVideoUploader -> initFile($iId, isset($aResult['title']) ? $aResult['title'] : '', $sCat, isset($aResult['tags']) ? $aResult['tags'].','. $sTag : $sTag, isset($aResult['desc']) ? $aResult['desc'] : '');
				
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