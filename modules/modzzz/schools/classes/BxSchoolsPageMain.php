<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx School
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolTwigPageMain');

class BxSchoolsPageMain extends BxDolTwigPageMain {

    function __construct(&$oMain) {
        $this->sSearchResultClassName = 'BxSchoolsSearchResult';
        $this->sFilterName = 'filter';
		parent::__construct('modzzz_schools_main', $oMain);
	}
 
    function getBlockCode_LatestFeaturedSchool() {
        
        $aDataEntry = $this->oDb->getLatestFeaturedItem (); 
        if (!$aDataEntry) {
            return MsgBox(_t('_Empty'));
        }

        $aAuthor = getProfileInfo($aDataEntry['author_id']);

        $sImageUrl = ''; 
        $sImageTitle = ''; 
        $a = array ('ID' => $aDataEntry['author_id'], 'Avatar' => $aDataEntry['thumb']);
        $aImage = BxDolService::call('photos', 'get_image', array($a, 'file'), 'Search');

        modzzz_schools_import('Voting');
        $oRating = new BxSchoolsVoting ('modzzz_schools', $aDataEntry['id']);

        $aVars = array (
            'image_url' => !$aImage['no_image'] && $aImage['file'] ? $aImage['file'] : $this->oTemplate->getIconUrl('no-photo-110.png'),
            'image_title' => !$aImage['no_image'] && $aImage['title'] ? $aImage['title'] : '',            
            'school_url' => BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
            'school_title' => $aDataEntry['title'],
            'author_title' => _t('_From'),
            'author_username' => $aAuthor['NickName'],
            'author_url' => getProfileLink($aAuthor['ID']),
            'rating' => $oRating->isEnabled() ? $oRating->getJustVotingElement (true, $aDataEntry['id']) : '',
        );
        return $this->oTemplate->parseHtmlByName('latest_featured_school', $aVars);
    }

    function getBlockCode_Recent() { 
        return $this->ajaxBrowse('recent', $this->oDb->getParam('modzzz_schools_perpage_main_recent'));
    }    
 
    function getBlockCode_Tags($iBlockId) { 
        bx_import('BxTemplTagsModule');
        $aParam = array(
            'type' => 'modzzz_schools',
            'orderby' => 'popular',
			'pagination' => getParam('tags_perpage_browse')
        );

		$sUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'tags';
  
        $oTags = new BxTemplTags();
        $oTags->getTagObjectConfig();
    
        return array(
            $oTags->display($aParam, $iBlockId, '', $sUrl),
            array(),
            array(),
            _t('_Tags')
        ); 

    }  
    
    function getBlockCode_Categories() {
		bx_import('BxTemplCategories');
  		
		$sType = 'modzzz_schools';
		
		$oCateg = new BxTemplCategories();
		$oCateg->getTagObjectConfig();

	    $aAllEntries = $this->oDb->getCategories($sType);
    
        $aResult['bx_repeat:entries'] = array();        
 		foreach($aAllEntries as $aEntry)
		{	 
			$iNumCategory = $this->oDb->getCategoryCount($sType,$aEntry['Category']);	
	
			$sHrefTmpl = $oCateg->getHrefWithType($sType);  
			$sCategory = $aEntry['Category'];
            $sCatHref = str_replace( '{tag}', urlencode(title2uri($sCategory)), $sHrefTmpl);
 
	        $aResult['bx_repeat:entries'][] = array(
                'cat_url' => $sCatHref, 
                'cat_name' => $sCategory,
			    'num_items' => $iNumCategory, 
            );	        
	    } 
 
	    return $this->oTemplate->parseHtmlByName('block_categories', $aResult);  
	}
 
	function getBlockCode_Activities() {
		$iNumEntries = getParam("modzzz_schools_perpage_main_feed"); 
		$aActivity = $this->oDb->getActivityFeed($iNumEntries);
        
		if(!count($aActivity))
			return; 

		$aResult['bx_repeat:entries'] = array();  
 		foreach($aActivity as $aEntry){
			 
			$iSchoolId = $aEntry['school_id'];
			$sLangKey = _t($aEntry['lang_key']);
			$sParams = $aEntry['params'];
			$iActionDate = $aEntry['date'];

			$aDbParams = explode(";", $sParams);
			$aParams = array();
			foreach($aDbParams as $aEachParam) {
			
				$aParamItems = explode("|", $aEachParam);
				$sKey = $aParamItems[0];
				$sValue = $aParamItems[1];
				$aParams[$sKey] = $sValue;
			
				$sLangKey = str_replace('{'.$sKey.'}', $sValue, $sLangKey); 
			}
		  
			$aResult['bx_repeat:entries'][] = array(
			    'thumbnail' => $GLOBALS['oFunctions']->getMemberIcon($aParams['profile_id'], 'left'), 
 			    'description' => $sLangKey, 
 			    'date' => defineTimeInterval($iActionDate),  
			);	  
	    }

	    return $this->oTemplate->parseHtmlByName('block_activities', $aResult);  
	}

	function getBlockCode_Comments() { 
	  
		$iNumComments = getParam("modzzz_schools_perpage_main_comment");
		$aAllEntries = $this->oDb->getLatestComments($iNumComments);

		if(!count($aAllEntries)) return; 
			
		$aVars = array (
			'bx_repeat:comments' => array (),
		);

		foreach($aAllEntries as $aEntry) {
		   
			$iMemberID = $aEntry['cmt_author_id'];
			$sNickName = getNickName($iMemberID);
			$sNickLink = getProfileLink($iMemberID);
			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail($iMemberID);
			$sMessage = $aEntry['cmt_text']; 
			$dtSent = defineTimeInterval($aEntry['date']);
			$iSchoolsId = $aEntry['cmt_object_id']; 
	 
			$sImage = '';
			if ($aEntry['thumb']) {
				$a = array ('ID' => $aEntry['author_id'], 'Avatar' => $aEntry['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}
 
			$iLimitChars = (int)getParam('modzzz_schools_comments_max_preview');

			$sMessage = $this->oMain->_formatSnippetText($aEntry, $iLimitChars, $sMessage);
 
			$aSchools = $this->oDb->getEntryById($iSchoolsId);
			$sSchoolsUri = $aSchools['uri'];
			$sSchoolsTitle = $aSchools['title'];
 
			$sSchoolsUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sSchoolsUri;
   
			$aVars['bx_repeat:comments'][] = array (
				'thumb_url' => $sMemberThumb,
				'author_url' => $sNickLink,
				'author' => $sNickName,
				'created' => $dtSent,
				'snippet_text' => $sMessage,
				'item_url' => $sSchoolsUrl,
				'item_title' => $sSchoolsTitle,
 			);  
		}
 
		return $this->oTemplate->parseHtmlByName('block_comments', $aVars); 
	}
 
	function getBlockCode_Forum() {
  		 
		$iNumComments = getParam("modzzz_schools_perpage_main_comment");
		$aPosts = $this->oDb->getLatestForumPosts($iNumComments);
  
		if(empty($aPosts))
			return;

		$aVars['bx_repeat:entries'] = array();
  		foreach($aPosts as $aEachPost){

			$sForumUri = $aEachPost['forum_uri'];
			$sTopic = $aEachPost['topic_title']; 
			$sTopicUri = $aEachPost['topic_uri'];
			$sPostText = $aEachPost['post_text']; 
			$sDate = defineTimeInterval($aEachPost['when']); 
			$sSchoolsName = $aEachPost['title']; 
 			$sPoster = $aEachPost['user']; 

			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail(getID($sPoster));

			$iLimitChars = (int)getParam('modzzz_schools_forum_max_preview');
			$sPostText = $this->oMain->_formatSnippetText($aEachPost, $iLimitChars, $sPostText);
 
			$sImage = '';
			if ($aEachPost['thumb']) {
				$a = array ('ID' => $aEachPost['author_id'], 'Avatar' => $aEachPost['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}

			$sSchoolsUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sForumUri;
			$sTopicUrl = BX_DOL_URL_ROOT . 'forum/schools/forum/'.$sForumUri.'-0.htm#topic/'.$sTopicUri.'.htm';
	
			$aVars['bx_repeat:entries'][] = array( 
							'topic_url' => $sTopicUrl, 
							'topic' => $sTopic, 
							'snippet_text' => $sPostText, 
							'item_title' => $sSchoolsName, 
							'item_url' => $sSchoolsUrl, 
							'created' => $sDate,
							'author_url' => getProfileLink(getID($sPoster)),
							'author' => $sPoster,
							'thumb_url' => $sMemberThumb,
						);
		}

		$sCode = $this->oTemplate->parseHtmlByName('block_forum', $aVars);  

		return $sCode;
	}
  

	function getBlockCode_Create() {
   		
		$sAskUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/my&filter=add_school'; 
    
		$aVars = array( 
			'create_url' => $sAskUrl, 
  		);
 
		$sCode = $this->oTemplate->parseHtmlByName('create_school', $aVars);  

		return $sCode;
	}

	function getBlockCode_Featured() { 
		return $this->ajaxBrowse('featured', $this->oDb->getParam('modzzz_schools_perpage_main_featured'));
	} 

	function getBlockCode_PopularList() { 
		return $this->ajaxBrowse('popular', $this->oDb->getParam('modzzz_schools_perpage_main_popular'));
	}     

	function getBlockCode_TopList() { 
		return $this->ajaxBrowse('top', $this->oDb->getParam('modzzz_schools_perpage_main_top'));
	}     
 
	function getBlockCode_Search() {
		
		$sSearchUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/quick/'; 
	
		$aVars = array( 
			'search_url' => $sSearchUrl, 
		);
 
		$sCode = $this->oTemplate->parseHtmlByName('search_schools', $aVars);  

		return $sCode;
	} 

    function ajaxMatesBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true) {

        bx_import ('SearchResult', $this->oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);

        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => getTemplateIcon('rss.png'))));

        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);

        if (!($s = $o->displayMatesResultBlock())) 
            return $isPublicOnly ? array(MsgBox(_t('_Empty')), $aMenu) : '';


        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->oConfig->getBaseUri() . $o->sBrowseUrl);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    } 



}
