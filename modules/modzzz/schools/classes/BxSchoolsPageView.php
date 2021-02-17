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

bx_import('BxDolTwigPageView');

class BxSchoolsPageView extends BxDolTwigPageView {	

	function BxSchoolsPageView(&$oMain, &$aDataEntry) {
		parent::__construct('modzzz_schools_view', $oMain, $aDataEntry);

        $this->sSearchResultClassName = 'BxSchoolsSearchResult';
        $this->sFilterName = 'filter';
 
        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/'. $this->aDataEntry['uri'];
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  

		if ( isset($_GET['ajax_mode']) and false !== bx_get('action') ) {
	  
		   // contain query from js for autocomplete;
		   $sAutoCompleteQ = ( isset($_GET['q']) ) ? $_GET['q'] : '';
			 
			if(bx_get('action')=='auto_complete' && $sAutoCompleteQ) { 
				$sOutputHtml = $this->_oDb -> getAutoCompleteList($sAutoCompleteQ); 
			}

			header('Content-Type: text/html; charset=utf-8');
			echo $sOutputHtml;
			exit;
		}

	}
		
	function getBlockCode_Info() {

		$iId = $this->aDataEntry['id'];
		$aData = $this->aDataEntry;

        $aAuthor = getProfileInfo($aData['author_id']);

		if(getParam('modzzz_schools_boonex_events')=='on'){
			$iEventsCount = $this->_oDb->getBoonexEventsCount($iId);
		}else{
			$iEventsCount = (int)$this->aDataEntry['events_count'];
		}

        $aVars = array (
            'author_unit' => get_member_thumbnail($aAuthor['ID'], 'none'),
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
            'cats' => $this->_oTemplate->parseCategories($aData['categories']),
            'tags' => $this->_oTemplate->parseTags($aData['tags']),
			'views' => (int)$aData['views'],
            'fans_count' => (int)$this->aDataEntry['fans_count'],
            'students_count' => (int)$this->aDataEntry['student_count'],
            'alumni_count' => (int)$this->aDataEntry['alumni_count'],
            'events_count' => $iEventsCount,
            'news_count' => (int)$this->aDataEntry['news_count'],
            'courses_count' => (int)$this->aDataEntry['courses_count'],
            'instructors_count' => (int)$this->aDataEntry['instructors_count'],
            'fields' => '',
        );
        return array($this->_oTemplate->parseHtmlByName('entry_view_block_info', $aVars)); 
    }
 
	function getBlockCode_Statistics() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'statistics');
    }

 	function getBlockCode_Contact() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'contact');
    }

 	function getBlockCode_Details() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'details');
    }
		
	function _blockCustomDisplay($aDataEntry, $sType) {
   
		switch($sType) { 
			case "location":
				$aAllow = array('street','city','state','country','zip');
			break;    
			case "statistics":
				$aAllow = array('president','founder','enrolled_students','academic_staff_count','admin_staff_count','year_established');
			break; 
			case "contact":
				$aAllow = array('fax', 'phone', 'website');
			break; 	
			case "details":
				$aAllow = array('motto','affiliations','mascot','nickname','colors','ethnicity','school_level','school_type','school_sports','school_clubs','school_qualifications');
			break; 				
		}
  
		$sFields = $this->_oTemplate->blockCustomFields($aDataEntry,$aAllow);

		if(!$sFields) return;

		$aVars = array ( 
            'fields' => $sFields, 
        );

        return array($this->_oTemplate->parseHtmlByName('custom_block_info', $aVars));   
    }
 
	function getBlockCode_Desc() {
        return array($this->_oTemplate->blockDesc ($this->aDataEntry));
    }

	function getBlockCode_Photo() {
        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
    }    

	function getBlockCode_VideoEmbed() {

		$aVideoUrls = $this->_oDb->getYoutubeVideos($this->aDataEntry['id']);
		
		$sFirstVideoId = '';
		$sFirstVideoTitle = '';
		$aVideos = array();
		if(empty($aVideoUrls))
			return;

		foreach($aVideoUrls as $aEachUrl){  
			$sFirstVideoId = ($sFirstVideoId) ? $sFirstVideoId : $this->_oTemplate->youtubeId($aEachUrl['url']);
			$sFirstVideoTitle = ($sFirstVideoTitle) ? $sFirstVideoTitle : $aEachUrl['title'];
			$aVideos[] = array ( 
				'video_id' => $this->_oTemplate->youtubeId($aEachUrl['url']), 
				'video_title' => $aEachUrl['title'], 
			);
		}

		$aVars = array(
			'video_id' => $sFirstVideoId,
			'video_title' => $sFirstVideoTitle,
			'bx_repeat:video' => $aVideos
		);
		 
        return $this->_oTemplate->parseHtmlByName('block_youtube_videos', $aVars);   
    }

    function getBlockCode_Video() {
        return $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Sound() {
        return $this->_blockSound ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'sounds'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Files() {
        return $this->_blockFiles ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'files'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Rate() {
        modzzz_schools_import('Voting');
        $o = new BxSchoolsVoting ('modzzz_schools', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_schools_import('Cmts');
        $o = new BxSchoolsCmts ('modzzz_schools', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
    }            
 
	function getBlockCode_Local() {    
		return $this->ajaxBrowse('local', $this->_oDb->getParam('modzzz_schools_perpage_main_recent'),array(),$this->aDataEntry['city'],$this->aDataEntry['id']); 
	}

	function getBlockCode_Other() {    
		return $this->ajaxBrowse('other', $this->_oDb->getParam('modzzz_schools_perpage_main_recent'),array(),$this->aDataEntry['author_id'],$this->aDataEntry['id']); 
	}

    function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $sValue2 = '', $isDisableRss = false, $isPublicOnly = true) {
        $oMain = BxDolModule::getInstance('BxSchoolsModule');

        bx_import ('SearchResult', $oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue, $sValue2);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);
 
        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);

        if (!($s = $o->displayResultBlock())) 
            return '';

        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => getTemplateIcon('rss.png'))));

        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->_oConfig->getBaseUri() . $o->sBrowseUrl);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    }   
 
    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

            $oSubscription = new BxDolSubscription();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'modzzz_schools', '', (int)$this->aDataEntry['id']);

			$isFan = $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 0) || $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 1);

            $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'TitleSubscribe' => $aSubscribeButton['title'], 
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_modzzz_schools_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_schools_action_title_delete') : '',

				'TitleJoin' => $this->_oMain->isAllowedJoin($this->aDataEntry) ? ($isFan ? _t('_modzzz_schools_action_title_leave') : _t('_modzzz_schools_action_title_join')) : '', 
				'IconJoin' => $isFan ? 'sign-out' : 'sign-in', 
                
			    'TitleAttend' => $this->_oMain->isAllowedJoin($this->aDataEntry) ? ($isFan ? '' : _t('_modzzz_schools_action_title_i_attend')) : '',
                'TitleLeave' => $isFan ? _t('_modzzz_schools_action_title_leave') : '',
				
				'TitleClaim' => $this->_oMain->isAllowedClaim($this->aDataEntry) ? _t('_modzzz_schools_action_title_claim') : '',

                'TitleInstructorsAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_schools_action_title_add_instructors') : '',
  
				'TitleInstructorsAttend' => $this->_oMain->isAllowedAddInstructor($this->aDataEntry['id']) ? _t('_modzzz_schools_action_title_i_am_instructor') : '',

                'TitleCoursesAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_schools_action_title_add_courses') : '',
 
                'TitleEventsAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_schools_action_title_add_events') : '',

                 'TitleNewsAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_schools_action_title_add_news') : '',

                'TitleStudentAttend' => $this->_oMain->isAllowedAddStudent($this->aDataEntry['id']) ? _t('_modzzz_schools_action_title_i_attend') : '',

                'TitleStudentAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_schools_action_title_add_student') : '',

                'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_modzzz_schools_action_title_invite') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_schools_action_title_share') : '',
                'TitleBroadcast' => $this->_oMain->isAllowedBroadcast($this->aDataEntry) ? _t('_modzzz_schools_action_title_broadcast') : '',
                'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_schools_action_remove_from_featured') : _t('_modzzz_schools_action_add_to_featured')) : '',
                'TitleManageFans' => $this->_oMain->isAllowedManageFans($this->aDataEntry) ? _t('_modzzz_schools_action_manage_fans') : '',
                'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_modzzz_schools_action_upload_photos') : '',
                'TitleEmbed' => $this->_oMain->isAllowedEmbed($this->aDataEntry) ? _t('_modzzz_schools_action_embed_video') : '',  
                'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_modzzz_schools_action_upload_videos') : '',
                'TitleUploadSounds' => $this->_oMain->isAllowedUploadSounds($this->aDataEntry) ? _t('_modzzz_schools_action_upload_sounds') : '',
                'TitleUploadFiles' => $this->_oMain->isAllowedUploadFiles($this->aDataEntry) ? _t('_modzzz_schools_action_upload_files') : '',
            );

            if (!$aInfo['TitleEdit'] && !$aInfo['TitleDelete'] && !$aInfo['TitleJoin'] && !$aInfo['TitleInvite'] && !$aInfo['TitleShare'] && !$aInfo['TitleBroadcast'] && !$aInfo['AddToFeatured'] && !$aInfo['TitleManageFans'] && !$aInfo['TitleUploadPhotos'] && !$aInfo['TitleUploadVideos'] && !$aInfo['TitleUploadSounds'] && !$aInfo['TitleUploadFiles'] && !$aInfo['TitleClaim'] && !$aInfo['TitleInstructorsAdd'] && !$aInfo['TitleCoursesAdd'] && !$aInfo['TitleStudentAdd'] && !$aInfo['TitleStudentAttend'] && !$aInfo['TitleNewsAdd'] && !$aInfo['TitleEventsAdd'] ) 
                return '';
 

            return $oSubscription->getData() . $oFunctions->genObjectsActions($aInfo, 'modzzz_schools');
        } 

        return '';
    }    

    function getBlockCode_Fans() {
        return $this->_blockFans ($this->_oDb->getParam('modzzz_schools_perpage_view_fans'), 'isAllowedViewFans', 'getFans');
    }  
	  
    function _blockFans($iPerPage, $sFuncIsAllowed = 'isAllowedViewFans', $sFuncGetFans = 'getFans') {

        if (!$this->_oMain->$sFuncIsAllowed($this->aDataEntry)) 
            return '';
        
        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->$sFuncGetFans($aProfiles, $this->aDataEntry[$this->_oDb->_sFieldId], true, $iStart, $iPerPage);


        if (!$iNum || !$aProfiles)
            return '';
        $iPages = ceil($iNum / $iPerPage);

        bx_import('BxTemplSearchProfile');
        $oBxTemplSearchProfile = new BxTemplSearchProfile();
        $sMainContent = '';

        foreach ($aProfiles as $aProfile) {
            $sMainContent .= $oBxTemplSearchProfile->displaySearchUnit($aProfile, array ('ext_css_class' => 'bx-def-margin-sec-top-auto'));
        }
/*
        foreach ($aProfiles as $aProfile) {
 
			$sTemplateName = 'search_profiles_sim.html';
			$aExtendedCss = array('ext_css_class' => 'bx-def-margin-sec-top-auto');
			if ($aProfile['Couple'] > 0) {
				$aProfileInfoC = getProfileInfo( $aProfile['Couple'] );
				$sMainContent .= $oBxTemplSearchProfile->PrintSearhResult( $aProfile, $aProfileInfoC, $aExtendedCss, $sTemplateName );
			} else {
				$sMainContent .= $oBxTemplSearchProfile->PrintSearhResult( $aProfile, array(), $aExtendedCss, $sTemplateName );
			} 
        }
*/

        $ret .= $sMainContent;
  
        $aDBBottomMenu = array();
        if ($iPages > 1) {
            $sUrlStart = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . "view/".$this->aDataEntry[$this->_oDb->_sFieldUri];
            $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');            
            if ($iPage > 1)
                $aDBBottomMenu[_t('_Back')] = array('href' => $sUrlStart . "page=" . ($iPage - 1), 'dynamic' => true, 'class' => 'backMembers', 'icon' => getTemplateIcon('sys_back.png'), 'icon_class' => 'left', 'static' => false);
            if ($iPage < $iPages) {                                
                $aDBBottomMenu[_t('_Next')] = array('href' => $sUrlStart . "page=" . ($iPage + 1), 'dynamic' => true, 'class' => 'moreMembers', 'icon' => getTemplateIcon('sys_next.png'), 'static' => false);
            }
        }
 
		$ret .= '<div class="clear_both"></div>';

		return array($ret, array(), $aDBBottomMenu);
    }    
 
    function getBlockCode_FansUnconfirmed() {
        return parent::_blockFansUnconfirmed (BX_SCHOOLS_MAX_FANS);
    }
 
    function getCode() {

        $this->_oMain->_processFansActions ($this->aDataEntry, BX_SCHOOLS_MAX_FANS);

        return parent::getCode();
    }

 	function getBlockCode_Location() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'location');
    }
 
    function getBlockCode_Instructors () {
 
        return $this->ajaxBrowseSubProfile(
            'instructors',
            'view_instructors',
            $this->_oDb->getParam('modzzz_schools_perpage_view_subitems'), 
            array(), $this->aDataEntry['uri'], true, false 
        ); 
    }
 
    function getBlockCode_Courses () {
   
        return $this->ajaxBrowseSubProfile(
            'courses',
            'view_courses',
            $this->_oDb->getParam('modzzz_schools_perpage_view_subitems'), 
            array(), $this->aDataEntry['uri'], true, false 
        ); 
    }

    function getBlockCode_News () {
 
        return $this->ajaxBrowseSubProfile(
            'news',
            'view_news',
            $this->_oDb->getParam('modzzz_schools_perpage_view_subitems'), 
            array(), $this->aDataEntry['uri'], true, false 
        ); 
    }

    function getBlockCode_Alumni () {
 
        return $this->ajaxBrowseSubProfile(
            'student',
            'view_alumni',
            $this->_oDb->getParam('modzzz_schools_perpage_view_subitems'), 
            array(), $this->aDataEntry['uri'], true, false 
        ); 
    }

    function getBlockCode_Students () {
 
        return $this->ajaxBrowseSubProfile(
            'student',
            'view_student',
            $this->_oDb->getParam('modzzz_schools_perpage_view_subitems'), 
            array(), $this->aDataEntry['uri'], true, false 
        ); 
    }

    function getBlockCode_Events () {
 
        return $this->ajaxBrowseSubProfile(
            'events',
            'view_events',
            $this->_oDb->getParam('modzzz_schools_perpage_view_subitems'), 
            array(), $this->aDataEntry['uri'], true, false 
        ); 
    }
 
    function ajaxBrowseSubProfile($sType, $sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true) {

        bx_import ('SearchResult', $this->_oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);
 
        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);
 
        if (!($s = $o->displaySubProfileResultBlock($sType))) {
             return '';
		} 

        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->_oConfig->getBaseUri() . $o->sBrowseUrl);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    }    
 

}
