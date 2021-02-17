<?php
/***************************************************************************
*                            Dolphin Smart Schools Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx School
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Schools Builder
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

bx_import('BxDolTwigTemplate');

/*
 * Schools module View
 */
class BxSchoolsTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    
	/**
	 * Constructor
	 */
	function __construct(&$oConfig, &$oDb) {
        parent::__construct($oConfig, $oDb);
		$this->oConfig = $oConfig;   
		$this->oDb = $oDb;   
    }
 
    // ======================= ppage compose block functions 

    function blockDesc (&$aDataEntry) {
        $aVars = array (
			'title' => $aDataEntry['title'],
			'description' => $aDataEntry['desc'],
        );
        return $this->parseHtmlByName('block_description', $aVars);
    }
  
    function blockFields (&$aDataEntry) {
        $sRet = '<table class="modzzz_schools_fields">';
        modzzz_schools_import ('FormAdd');        
        $oForm = new BxSchoolsFormAdd ($GLOBALS['oBxSchoolsModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display']) || !$aDataEntry[$k]) continue;
            $sRet .= '<tr><td class="modzzz_schools_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_schools_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display'])))
                $sRet .= call_user_func_array(array($this, $a['display']), array($aDataEntry[$k]));
            else if (0 == strcasecmp($k, 'country'))
                $sRet .= _t($GLOBALS['aPreValues']['Country'][$aDataEntry[$k]]['LKey']);
            else
                $sRet .= $aDataEntry[$k];
            $sRet .= '<td></tr>';
        }
        $sRet .= '</table>';
        return $sRet;
    }
 
    function unit ($aData, $sTemplateName, &$oVotingView, $isShort=false) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxSchoolsModule');

        if (!$this->_oMain->isAllowedView ($aData)) {            
            $aVars = array ('extra_css_class' => 'modzzz_schools_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }

        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('modzzz_schools_max_preview');
   
        $aVars = array(  
			'id' => $aData['id'],

            'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),
            
            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ),
 
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'),
			'school_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
			'school_title' => $aData['title'],
 			'comments_count' => $aData['comments_count'], 
			'fans_count' => $aData['fans_count'],
			'views_count' => $aData['views'],  
			'country_city' => _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']) . (trim($aData['city']) ? ', '.$aData['city'] : ''),
			'all_categories' => $this->_oMain->parseCategories($aData['categories']),  
			'post_tags' => $this->_oMain->parseTags($aData['tags']), 		
			'flag_image' => genFlag($aData['country']),  
		);        
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
    function mate_unit ($aData, $sTemplateName) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxSchoolsModule');
         
		$iAuthor = $aData['profile_id'];
		$aAuthor = getProfileInfo($iAuthor);  
		$sOwnerThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aAuthor['ID'], 'left'); 
 
        $aVars = array(  
			'id' => $aData['id'],
			'post_uthumb' => $sOwnerThumb, 
			'school_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
			'school_title' => $aData['title'],
			'author' => getNickName($iAuthor),
			'author_url' => $aAuthor['ID'] ? getProfileLink($aAuthor['ID']) : 'javascript:void(0);',
 			'country_city' => _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']) . (trim($aData['city']) ? ', '.$aData['city'] : ''), 
			'year_entered' => $aData['year_entered'],  
			'year_left' => ($aData['year_left']) ? $aData['year_left'] : _t('_modzzz_schools_present'),   
		);        
 
        return $this->parseHtmlByName('school_mates', $aVars);
    }

    function blockCustomFields (&$aDataEntry, $aShow=array()) {
        
		$bHasEntries = false;
	
		$sRet = '<table class="modzzz_schools_fields">';
        modzzz_schools_import ('FormAdd');        
        $oForm = new BxSchoolsFormAdd ($GLOBALS['oBxSchoolsModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            //if (!isset($a['display'])) continue;
 
            if (!in_array($a['name'],$aShow)) continue;
            
			if (!trim($aDataEntry[$k])) continue;
			
			$bHasEntries = true;

            $sRet .= '<tr><td class="modzzz_schools_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_schools_field_value">';
            
			if (is_string($a['display']) && is_callable(array($this, $a['display']))){
				//echo $a['listname'] .','.$aDataEntry[$k].'<br>';

				if($a['name'] == 'state'){
					$sRet .= $this->getStateName($aDataEntry['country'], $aDataEntry[$k]);
				}else{
					$sRet .= call_user_func_array(array($this, $a['display']), array($a['listname'],$aDataEntry[$k]));  
				}
			}else{ 

				if($a['name'] == 'website')
					$sRet .= "<a target=_blank href='".((substr($aDataEntry[$k],0,3)=="www") ? "http://".$aDataEntry[$k] : $aDataEntry[$k])."'>".$aDataEntry[$k]."</a>";
				else 
					$sRet .= $aDataEntry[$k];
			}
            $sRet .= '<td></tr>';
        } 
        $sRet .= '</table>';
		
		if(!$bHasEntries) 
			return;
		else
			return $sRet;
    }

    function parseLink ($sName='', $sVal='') { 

		$sLink = (substr($sVal,0,3)=="www") ? "http://" . $sVal : $sVal; 
 
		return '<a href="'.$sLink.'" target=_blank>'.$sLink.'</a>';
	}
 
    function parsePreValues ($sName, $sVal='') {  
 		return htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sName][$sVal]['LKey']) );
 	}
 
    function parseInstructors ($sVal='') {
		$aInstructors = array();
		$aVals = explode(';',trim($sVal));
		foreach($aVals as $iId){ 
			$aData = $this->oDb->getInstructorEntryById($iId);
			$sUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'instructors/view/' . $aData['uri']; 
		    $aInstructors[] = '<a href="'.$sUrl.'">' . $aData['title'] . '</a>'; 
 		}
		return implode(' :: ', $aInstructors);
	}
 
    function parseSemesters ($sVal) {
		$aSemesters = array();
		$aVals = explode(';', $sVal);
		foreach($aVals as $iId){
			$aSemesters[] = $this->oDb->getSemesters($iId); 
 		}
		return implode(' :: ', $aSemesters);
	}
 
    function parseMultiPreValues ($sName, $sVal='') {  
		$sStr = '';
		$aVals = explode(';',$sVal);
		foreach($aVals as $aEachVal){
			if($GLOBALS['aPreValues'][$sName][$aEachVal]['LKey'])
 				$sStr .= htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sName][$aEachVal]['LKey'])) . '<br>';
			else
 				$sStr .= $aEachVal . '<br>'; 
		}
 		return $sStr;
 	}
  
	//[begin] - claim
     function claim_unit ($aData, $sTemplateName, &$oVotingView, $isShort=false) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxSchoolsModule');
 
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
        modzzz_schools_import('Voting');
        $oRating = new BxSchoolsVoting ('modzzz_schools', $aData['id']);
   
   		$iLimitChars = (int)getParam('modzzz_schools_max_preview');

        $aVars = array (
 		    'id' => $aData['id'],  
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'), 
            'schools_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'schools_title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars),
            'comments_count' => $aData['comments_count'],
			'all_categories' => $this->_oMain->parseCategories($aData['categories']),  
            'post_tags' => $this->_oMain->parseTags($aData['tags']), 
  
            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ),
  
 			'claim_message' => $aData['message'],
			'claimant_url' => getProfileLink($aData['member_id']),
			'claimant_name' => getNickName($aData['member_id']),
			'claim_date' => $this->filterDate($aData['claim_date'], true), 

        );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
   } 
   //[end] - claim
     
    function getMemberThumbnail($iId)
    {
        $aProfile = getProfileInfo($iId);
        if (!$aProfile)
            return '';
 
        bx_import('BxDolMemberInfo');
        $o = BxDolMemberInfo::getObjectInstance(getParam($sType == 'small' ? 'sys_member_info_thumb_icon' : 'sys_member_info_thumb'));
        $sThumbUrl = $o ? $o->get($aProfile) : '';
        if (!$sThumbUrl)
            return '';
 
		return $sThumbUrl; 
    }

    function instructors_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxSchoolsModule');
 
		$aInstructorsEntry = $this->_oMain->_oDb->getInstructorsEntryById($aData['id']);
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableInstructors,$aInstructorsEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_schools_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }

  		$sDateTime = defineTimeInterval($aData['created']);
    
		$iLimitChars = (int)getParam('modzzz_schools_max_preview');
  
		if($aData['use_profile_photo'] && $aData['profile_id']){
			$sImage = $this->getMemberThumbnail($aData['profile_id']);
		}else{
			$sImage = '';
			if ($aData['thumb']) {
				$a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}elseif ($aData['icon']) {
				$sImage = $this->_oDb->getIcon($aData['icon']);
			}
		}

		if($aData['use_profile_desc'] && $aData['profile_id']){
			$aProfile = getProfileInfo($aData['profile_id']);
			$sDesc = ($aProfile['DescriptionMe']) ? $this->_oMain->_formatSnippetText($aData, $iLimitChars, $aProfile['DescriptionMe']) : '';
		}elseif(trim($aData['desc'])){
			$sDesc = $this->_oMain->_formatSnippetText($aData, $iLimitChars);
		} 
 
		$sPosition = $aData['position'];
		$iLimitChars = 29;
		if (strlen($sPosition) > $iLimitChars) {
 			$sLinkMore = '..';
			$sPosition = process_line_output(mb_substr(strip_tags($sPosition), 0, $iLimitChars-2)) . $sLinkMore;
		}
 
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-instructor.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'instructors/view/' . $aData['uri'],
            'title' => $aData['title'],
            'position' => $aData['position'],
            'position_display' => $sPosition,
            'snippet_text' => $sDesc,

            'bx_if:site' => array (
                'condition' => $aData['use_profile_photo'],
                'content' => array (
					'thumb_url' => $sImage,
					'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'instructors/view/' . $aData['uri'],
                ),
            ), 

            'bx_if:external' => array (
                'condition' => (!$aData['use_profile_photo']),
                'content' => array (
					'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-instructor.png'),
					'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'instructors/view/' . $aData['uri'],
                ),
            ), 

        ); 
 
        $aVars['rate'] = $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;';
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    function courses_unit ($aData, $sTemplateName, &$oVotingView, $isShort=false) {
  
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxSchoolsModule');
 
		$aCoursesEntry = $this->_oMain->_oDb->getCoursesEntryById($aData['id']);
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableCourses,$aCoursesEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_schools_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

  		$sDateTime = defineTimeInterval($aData['created']);
    
		$iLimitChars = (int)getParam('modzzz_schools_max_preview');
    
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-course.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'courses/view/' . $aData['uri'],
            'title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars, $aData['overview']),
 
            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 

        ); 

        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
  
    function events_unit ($aData, $sTemplateName, &$oVotingView, $isShort=false) {
  
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxSchoolsModule');
 
		$aEventsEntry = $this->_oMain->_oDb->getEventsEntryById($aData['id']);
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableEvents,$aEventsEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_schools_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

  		$sDateTime = defineTimeInterval($aData['created']);
    
		$iLimitChars = (int)getParam('modzzz_schools_max_preview');
    
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-event.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'events/view/' . $aData['uri'],
            'title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars, $aData['desc']),
       
            'event_start' => $this->filterDate($aData['event_start']),  
            'event_end' => $this->filterDate($aData['event_end']),  
            'participants' => $aData['fans_count'],
			'country_city' => $this->_oMain->_formatLocation($aData),

            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ),
		 );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    function student_unit ($aData, $sTemplateName, &$oVotingView, $isShort=false) {
  
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxSchoolsModule');
 
		$aStudentEntry = $this->_oMain->_oDb->getStudentEntryById($aData['id']);
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableStudent,$aStudentEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_schools_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
  		$sDateTime = defineTimeInterval($aData['created']);

		$iLimitChars = (int)getParam('modzzz_schools_max_preview');


		if($aData['use_profile_photo'] && $aData['profile_id']){
			$sImage = $this->getMemberThumbnail($aData['profile_id']);
		}else{ 
			$sImage = '';
			if ($aData['thumb']) {
				$a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			} 
		}

 		if($aData['use_profile_desc'] && $aData['profile_id']){
			$aProfile = getProfileInfo($aData['profile_id']);
			$sDesc = ($aProfile['DescriptionMe']) ? $this->_oMain->_formatSnippetText($aData, $iLimitChars, $aProfile['DescriptionMe']) : '';
		}elseif(trim($aData['desc'])){
			$sDesc = $this->_oMain->_formatSnippetText($aData, $iLimitChars);
		} 
   
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-student.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'student/view/' . $aData['uri'],
            'title' => $aData['title'],
            'snippet_text' => $sDesc,
 
			'year_entered' => $aData['year_entered'],  
			'year_left' => ($aData['year_left']) ? $aData['year_left'] : _t('_modzzz_schools_present'),   

            'bx_if:site' => array (
                'condition' => $aData['use_profile_photo'],
                'content' => array (
					'thumb_url' => $sImage,
					'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'student/view/' . $aData['uri'],
                ),
            ), 

            'bx_if:external' => array (
                'condition' => (!$aData['use_profile_photo']),
                'content' => array (
					'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-student.png'),
					'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'student/view/' . $aData['uri'],
                ),
            ), 
 
            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 

        ); 

        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

	function news_unit ($aData, $sTemplateName, &$oVotingView, $isShort=false) {
  
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxSchoolsModule');
 
		$aNewsEntry = $this->_oMain->_oDb->getNewsEntryById($aData['id']);
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableNews,$aNewsEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_schools_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

  		$sDateTime = defineTimeInterval($aData['created']);
    
		$iLimitChars = (int)getParam('modzzz_schools_max_preview');
    
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-news.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/view/' . $aData['uri'],
            'title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars, $aData['desc']),
 
            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 

        ); 

        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
    function blockSubProfileInfo ($sType, &$aData) {

		$this->_oMain = BxDolModule::getInstance('BxSchoolsModule');

        $aAuthor = getProfileInfo($aData['author_id']);
 
	    $aAllow = array('event_start', 'event_end');

		$sFields = $this->blockSubItemFields($aData, 'event', $aAllow);
 
        $aVars = array (
            'author_unit' => get_member_thumbnail($aAuthor['ID'], 'none', true),
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
            /*'cats' => $this->parseSubCategories($sType, $aData['categories']),*/
            'tags' => $this->parseSubTags($aData['tags']),
            'fields' => $sFields,
         );

        return $this->parseHtmlByName('block_subprofile_info', $aVars);
    }
  
    function parseSubCategories ($sType, $sVal='') {
		
		$sName = 'School'.ucwords($sType).'Categories';

		$sStr = '';
		$aVals = explode(';',$sVal);
		foreach($aVals as $aEachVal){
			$sStr .= htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sName][$aEachVal]['LKey'])) . '&#160';
 		}
 		return $sStr;
 	}
 
    function parseSubTags($s){ 
        $sRet = '';
        $a = explode (',', $s);
         
        foreach ($a as $sName)
            $sRet .= $sName.'&#160';
  
        return $sRet;
    }

	function youtubeId($url) {
		$v='';
		if (preg_match('%youtube\\.com/(.+)%', $url, $match)) {
			$match = $match[1];
			$replace = array("watch?v=", "v/", "vi/");
			$sQueryString = str_replace($replace, "", $match); 
			$aQueryParams = explode('&',$sQueryString);
			$v = $aQueryParams[0]; 
		}else{ 
			//.$url = parse_url($sVideoEmbed);
			//parse_str($url['query']);
			$video_id = substr( parse_url($url, PHP_URL_PATH), 1 );
			$v = ltrim( $video_id, '/' ); 
		} 

		return $v;  
	}
 
	function getStateName($sCountry, $sState=''){  
		return $this->oDb->getStateName($sCountry, $sState);
	}

   function blockSubItemFields (&$aDataEntry, $sType='', $aShow=array()) {
        
		$bHasValues = false;
	
		switch($sType){ 
			case 'event':
				$sRet = '<table class="modzzz_event_fields">';
				modzzz_schools_import ('EventsFormAdd');        
				$oForm = new BxSchoolsEventsFormAdd ($GLOBALS['oBxSchoolsModule'], $_COOKIE['memberID']);
			break; 
		}

        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
            if (!in_array($a['name'],$aShow)) continue;
            
			if (!trim($aDataEntry[$k])) continue;

            $sRet .= '<tr><td class="modzzz_schools_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_schools_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display']))){

				if($a['name'] == 'state'){
					$sRet .= $this->getStateName($aDataEntry['country'], $aDataEntry[$k]);
				}else{
					if($a['listname'])
						$sRet .= call_user_func_array(array($this, $a['display']), array($a['listname'],$aDataEntry[$k]));
					else
						$sRet .= call_user_func_array(array($this, $a['display']), array($aDataEntry[$k]));
				}
			}else{
				if($a['name'] == 'website')
					$sRet .= "<a target=_blank href='".((substr($aDataEntry[$k],0,3)=="www") ? "http://".$aDataEntry[$k] : $aDataEntry[$k])."'>".$aDataEntry[$k]."</a>";
				else
					$sRet .= $aDataEntry[$k];
			}
            $sRet .= '<td></tr>';

			$bHasValues = true; 
        }

		if(!$bHasValues)
			return;

        $sRet .= '</table>';
        return $sRet;
    }
 
    function filterDate ($i, $bLongFormat=true) {
		if($bLongFormat)
			return date('M d, Y', $i) . ' ('.defineTimeInterval($i) . ')';
		else
			return date('M d, Y', $i);
    } 
  

}
