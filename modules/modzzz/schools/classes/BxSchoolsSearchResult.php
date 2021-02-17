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

bx_import('BxDolTwigSearchResult');

class BxSchoolsSearchResult extends BxDolTwigSearchResult {

    var $aCurrent = array(
        'name' => 'modzzz_schools',
        'title' => '_modzzz_schools_page_title_browse',
        'table' => 'modzzz_schools_main', 
			'ownFields' => array('id', 'title', 'uri', 'created', 'author_id', 'thumb', 'rate', 'fans_count', 'country', 'city','state', 'street', 'fax', 'phone', 'website', 'categories', 'tags','comments_count', 'views', 'desc', 'enrolled_students', 'student_count', 'alumni_count','year_established','founder','ethnicity','school_level','school_type','school_sports','school_clubs','school_qualifications' 
		), 
 
 		'searchFields' => array('title','desc','tags','categories'),
        'join' => array(
            'profile' => array(
                    'type' => 'left',
                    'table' => 'Profiles',
                    'mainField' => 'author_id',
                    'onField' => 'ID',
                    'joinFields' => array('NickName'),
            ),
        ),
        'restriction' => array(
            'activeStatus' => array('value' => 'approved', 'field'=>'status', 'operator'=>'='),
            'owner' => array('value' => '', 'field' => 'author_id', 'operator' => '='),
            'tag' => array('value' => '', 'field' => 'tags', 'operator' => 'against'),
            'category' => array('value' => '', 'field' => 'Category', 'operator' => '=', 'table' => 'sys_categories'),
            'public' => array('value' => '', 'field' => 'allow_view_school_to', 'operator' => '='),
        ),
        'paginate' => array('perPage' => 14, 'page' => 1, 'totalNum' => 0, 'totalPages' => 1),
        'sorting' => 'last',
        'rss' => array( 
            'title' => '',
            'link' => '',
            'image' => '',
            'profile' => 0,
            'fields' => array (
                'Link' => '',
                'Title' => 'title',
                'DateTimeUTS' => 'created',
                'Desc' => 'desc',
                'Photo' => '',
            ),
        ),
        'ident' => 'id'
    );
    
    
    //function BxSchoolsSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '') {        
    function __construct($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '', $sValue4 = '', $sValue5 = '', $sValue6 = '') {

		$oMain = $this->getMain();

        switch ($sMode) {

            case 'pending':
                if (false !== bx_get('filter'))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input(bx_get('filter'), BX_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "administration";
                $this->aCurrent['title'] = _t('_modzzz_schools_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break;

            case 'my_pending':
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_schools_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break;

            case 'search':
				//[begin] - ultimate schools mod from modzzz 
				if ($sValue)
					$this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');

				if (is_array($sValue2) && count($sValue2)) {  
					foreach($sValue2 as $val){
						if(trim($val))
							$bSetCategory = true;
					}
					if($bSetCategory){  
						$this->aCurrent['join']['category'] = array(
							'type' => 'inner',
							'table' => 'sys_categories',
							'mainField' => 'id',
							'onField' => 'ID',
							'joinFields' => '',
						);
						$this->aCurrent['restriction']['category_type'] = array('value' => 'modzzz_schools', 'field' => 'type', 'operator' => '=', 'table' => 'sys_categories'); 

						$this->aCurrent['restriction']['category']['value'] = $sValue2;
						$this->aCurrent['restriction']['category']['operator'] = 'in';
					}
				}

				if($sValue3){
					$this->aCurrent['restriction']['country'] = array('value' => $sValue3, 'field' => 'Country', 'operator' => '='); 
				}
 
				if ($sValue4)
					$this->aCurrent['restriction']['state'] = array('value' => $sValue4,'field' => 'state','operator' => '=');

				if ($sValue5)
					$this->aCurrent['restriction']['city'] = array('value' => $sValue5,'field' => 'city','operator' => '=');
			  

				$sValue = $GLOBALS['MySQL']->unescape($sValue);
				$sValue2 = $GLOBALS['MySQL']->unescape($sValue2);
				$sValue3 = $GLOBALS['MySQL']->unescape($sValue3);
				$sValue4 = $GLOBALS['MySQL']->unescape($sValue4);
				$sValue5 = $GLOBALS['MySQL']->unescape($sValue5);
			 
				 
				$this->sBrowseUrl = "search/$sValue/" . (is_array($sValue2) ? implode(',',$sValue2) : $sValue2);
				$this->aCurrent['title'] = _t('_modzzz_schools_page_title_search_results') . ' ' . (is_array($sValue2) ? implode(', ',$sValue2) : $sValue2) . ' ' . $sValue;
				unset($this->aCurrent['rss']);
				break;

				case 'quick': 
					$sKeyword = ($sValue && ($sValue!='-')) ? ' - ' . $sValue : '';
					if ($sValue && ($sValue!='-'))
						$this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');
					if ($sValue2)
						$this->aCurrent['restriction']['city'] = array('value' => $sValue2,'field' => 'city','operator' => '=');  
					
					$this->sBrowseUrl = "browse/quick/" . $sValue .'/'. $sValue2;
					$this->aCurrent['title'] = _t('_modzzz_schools_page_title_browse') . $sKeyword .' - '. $sValue2; 				
				break;
 

/*
                if ($sValue)
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');

                if ($sValue2) {

                    $this->aCurrent['join']['category'] = array(
                        'type' => 'inner',
                        'table' => 'sys_categories',
                        'mainField' => 'id',
                        'onField' => 'ID',
                        'joinFields' => '',
                    );

                    $this->aCurrent['restriction']['category']['value'] = $sValue2;
                    if (is_array($sValue2)) {
                        $this->aCurrent['restriction']['category']['operator'] = 'in';
                    } 
                }

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $sValue2 = $GLOBALS['MySQL']->unescape($sValue2);
                $this->sBrowseUrl = "search/$sValue/" . (is_array($sValue2) ? implode(',',$sValue2) : $sValue2);
                $this->aCurrent['title'] = _t('_modzzz_schools_page_title_search_results') . ' ' . (is_array($sValue2) ? implode(', ',$sValue2) : $sValue2) . ' ' . $sValue;
                unset($this->aCurrent['rss']);
                break;
*/

            case 'attend':
                $iProfileId = $GLOBALS['oBxSchoolsModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/user/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_modzzz_schools_page_title_browse_by_author');
                if (bx_get('rss')) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['Avatar']) {
                        $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
                        $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    } 
                }
                break;

            case 'user':
                $iProfileId = $GLOBALS['oBxSchoolsModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/user/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_modzzz_schools_page_title_browse_by_author');
                if (bx_get('rss')) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['Avatar']) {
                        $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
                        $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    } 
                }
                break;

            case 'joined':
                $iProfileId = $GLOBALS['oBxSchoolsModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                

                if (!$iProfileId) {

                    $this->isError = true;

                } else {

					$this->aCurrent['join']['fans'] = array(
						'type' => 'inner',
						'table' => 'modzzz_schools_fans',
						'mainField' => 'id',
						'onField' => 'id_entry',
						'joinFields' => array('id_profile'),
					);
					$this->aCurrent['restriction']['fans'] = array(
						'value' => $iProfileId, 
						'field' => 'id_profile', 
						'operator' => '=', 
						'table' => 'modzzz_schools_fans',
					);
					$this->aCurrent['restriction']['confirmed_fans'] = array(
						'value' => 1, 
						'field' => 'confirmed', 
						'operator' => '=', 
						'table' => 'modzzz_schools_fans',
					);
				}

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/joined/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . ' - ' . _t('_modzzz_schools_page_title_browse_by_author_joined_schools');

                if (bx_get('rss')) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['Avatar']) {
                        $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
                        $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    } 
                }
                break;
 
            case 'school_mates': 
                $iProfileId = $GLOBALS['oBxSchoolsModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
				$aMySchools = $GLOBALS['oBxSchoolsModule']->_oDb->getMySchools($iProfileId);
          
				if (!$iProfileId) {

                    $this->isError = true;

                } else {

					$this->aCurrent['join']['student'] = array(
						'type' => 'inner',
						'table' => 'modzzz_schools_student_main',
						'mainField' => 'id',
						'onField' => 'school_id',
						'joinFields' => array('profile_id','year_entered','year_left'),
					);
					$this->aCurrent['restriction']['school'] = array(
						'value' => $aMySchools, 
						'field' => 'id', 
						'operator' => 'in', 
						'table' => 'modzzz_schools_main', 
					);
					$this->aCurrent['restriction']['no_me'] = array(
						'value' => $iProfileId, 
						'field' => 'profile_id', 
						'operator' => '!=', 
						'table' => 'modzzz_schools_student_main', 
					);  
					$this->aCurrent['restriction']['member'] = array(
						'value' => 1, 
						'field' => 'profile_id', 
						'operator' => '>=', 
						'table' => 'modzzz_schools_student_main', 
					);  
				}

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/school_mates/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . ' - ' . _t('_modzzz_schools_page_title_browse_by_author_school_mates');

                if (bx_get('rss')) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['Avatar']) {
                        $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
                        $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    } 
                }
                break;

            case 'admin':
                $this->aCurrent['restriction']['owner']['value'] = 0;
                $this->sBrowseUrl = "browse/admin";
                $this->aCurrent['title'] = _t('_modzzz_schools_page_title_admin_schools');
                break;

            case 'category':
                $this->aCurrent['join']['category'] = array(
                    'type' => 'inner',
                    'table' => 'sys_categories',
                    'mainField' => 'id',
                    'onField' => 'ID',
                    'joinFields' => '',
                );

				$this->aCurrent['restriction']['category_type'] = array('value' => 'modzzz_schools', 'field' => 'type', 'operator' => '=', 'table' => 'sys_categories'); 

                $this->aCurrent['restriction']['category']['value'] = $sValue;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/category/" . title2uri($sValue);
                $this->aCurrent['title'] = _t('_modzzz_schools_page_title_browse_by_category') . ' ' . $sValue;
                break;

            case 'tag':
                $this->aCurrent['restriction']['tag']['value'] = $sValue;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/tag/" . title2uri($sValue);
                $this->aCurrent['title'] = _t('_modzzz_schools_page_title_browse_by_tag') . ' ' . $sValue;
                break;
		  
			case 'local_country':         
			$this->aCurrent['restriction']['local_country'] = array('value' => $sValue, 'field' => 'country', 'operator' => '='); 
			$this->sBrowseUrl = "browse/local_country/$sValue";
			$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_local', $sValue);
			break; 

			case 'local_state':             
			$this->aCurrent['restriction']['local_state'] = array('value' => $sValue, 'field' => 'state', 'operator' => '='); 
			$this->sBrowseUrl = "browse/local_state/$sValue/$sValue2";
			$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_local', $sValue);
			break; 

			case 'local': 
			$this->aCurrent['restriction']['local'] = array('value' => $sValue, 'field' => 'city', 'operator' => '='); 
			$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

			$this->sBrowseUrl = "browse/local/$sValue/$sValue2";
			$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_local', $sValue);
			break;

			case 'other':
 
			$sNickName = getNickName($sValue);

			$this->aCurrent['restriction']['other'] = array('value' => $sValue, 'field' => 'author_id', 'operator' => '=');  
			$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

			$this->sBrowseUrl = "browse/other/$sValue/$sValue2";
			$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_other', $sNickName);
			break;
 
            case 'recent':
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_modzzz_schools_page_title_browse_recent');
                break;

            case 'top':
                $this->sBrowseUrl = 'browse/top';
                $this->aCurrent['sorting'] = 'top';
                $this->aCurrent['title'] = _t('_modzzz_schools_page_title_browse_top_rated');
                break;

            case 'popular':
                $this->sBrowseUrl = 'browse/popular';
                $this->aCurrent['sorting'] = 'popular';
                $this->aCurrent['title'] = _t('_modzzz_schools_page_title_browse_popular');
                break;                

            case 'featured':
				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured';
                $this->aCurrent['title'] = _t('_modzzz_schools_page_title_browse_featured');
                break;                                

            case 'calendar':
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 00:00:00')", 'field' => 'created', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 23:59:59')", 'field' => 'created', 'operator' => '<=', 'no_quote_value' => true);
                $this->sEventsBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->aCurrent['title'] = _t('_modzzz_schools_page_title_browse_by_day') . sprintf("%04u-%02u-%02u", $sValue, $sValue2, $sValue3);
                break;                                

            case 'claim':  
 
                 if (isset($_REQUEST['filter'])) 
 					$this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
 
				$this->aCurrent['ownFields'] = array('title', 'uri', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'categories', 'tags', 'comments_count' );
  
                //$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/claim/";
                $this->aCurrent['title'] = _t('_modzzz_schools_page_title_claim');
                
				$this->aCurrent['join']['claims'] = array(
					'type' => 'inner',
					'table' => 'modzzz_schools_claim',
					'mainField' => 'id',
					'onField' => 'listing_id',
					'joinFields' => array('id', 'listing_id', 'member_id', 'claim_date', 'assign_date', 'message', 'processed'),   
				);	 
				$this->aCurrent['restriction']['processed'] = array( 'value' => 1,'field' => 'processed','operator' => '!=', 'table' =>'modzzz_schools_claim');

				unset($this->aCurrent['restriction']['activeStatus']);  

				unset($this->aCurrent['rss']);  
            break;
  
            case 'instructors':
						 
				$aSchoolEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iSchoolId = (int)$aSchoolEntry['id'];
				$sTitle = $aSchoolEntry['title'];
 
				$this->aCurrent['join']['instructors'] = array(
					'type' => 'inner',
					'table' => 'modzzz_schools_instructors_main',
					'mainField' => 'id',
					'onField' => 'school_id',
					'joinFields' => array('id', 'school_id', 'profile_id', 'type', 'title', 'uri', 'position', 'use_profile_desc','use_profile_photo', 'created', 'icon', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
				);
 
				$this->aCurrent['ownFields'] = array();

 				$this->aCurrent['sorting'] = 'instructor';

				$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'modzzz_schools_instructors_main'); 
				$this->sBrowseUrl = "instructors/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_school_instructors', $sTitle);
				break;

            case 'view_instructors':
						 
				$aSchoolEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iSchoolId = (int)$aSchoolEntry['id'];
				$sTitle = $aSchoolEntry['title'];
 
				$this->aCurrent['join']['instructors'] = array(
					'type' => 'inner',
					'table' => 'modzzz_schools_instructors_main',
					'mainField' => 'id',
					'onField' => 'school_id',
					'joinFields' => array('id', 'school_id', 'profile_id', 'type', 'title', 'uri', 'position', 'created', 'icon', 'thumb', 'rate', 'desc', 'use_profile_desc','use_profile_photo', 'comments_count', 'author_id'),
				);
 
				$this->aCurrent['ownFields'] = array();
                
				$this->aCurrent['sorting'] = 'instructor';
 
				$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'modzzz_schools_instructors_main'); 
				$this->sBrowseUrl = "instructors/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_school_instructors', $sTitle);
				break;
 
            case 'courses':
						 
				$aSchoolEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iSchoolId = (int)$aSchoolEntry['id'];
				$sTitle = $aSchoolEntry['title'];
 
				$this->aCurrent['join']['courses'] = array(
					'type' => 'inner',
					'table' => 'modzzz_schools_courses_main',
					'mainField' => 'id',
					'onField' => 'school_id',
					'joinFields' => array('id', 'school_id', 'title', 'uri', 'created', 'icon', 'thumb', 'rate',  'comments_count','credits','course_code','semester','instructors','overview','objectives','prerequisite','timetable','delivery_methods','content','assessment','materials','specialization', 'author_id'),
				);
  
				$this->aCurrent['ownFields'] = array();

 				$this->aCurrent['sorting'] = 'course';

				$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'modzzz_schools_courses_main'); 
				
				$this->sBrowseUrl = "courses/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_school_courses', $sTitle);
				break;

            case 'view_courses':
						 
				$aSchoolEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iSchoolId = (int)$aSchoolEntry['id'];
				$sTitle = $aSchoolEntry['title'];
 
				$this->aCurrent['join']['courses'] = array(
					'type' => 'inner',
					'table' => 'modzzz_schools_courses_main',
					'mainField' => 'id',
					'onField' => 'school_id',
					'joinFields' => array('id', 'school_id', 'title', 'uri', 'created', 'icon', 'thumb', 'rate',  'comments_count','credits','course_code','semester','instructors','overview','objectives','prerequisite','timetable','delivery_methods','content','assessment','materials','specialization', 'author_id'),
				);
 
				$this->aCurrent['ownFields'] = array();
                
				$this->aCurrent['sorting'] = 'course';
 
				$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'modzzz_schools_courses_main'); 
				
				$this->sBrowseUrl = "courses/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_school_courses', $sTitle);
				break;

            case 'events':
						 
				$aSchoolEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iSchoolId = (int)$aSchoolEntry['id'];
				$sTitle = $aSchoolEntry['title'];
 
				//[begin] event integration - modzzz
				if(getParam('modzzz_schools_boonex_events')=='on'){ 
					$oEvent = BxDolModule::getInstance('BxEventsModule');

					$this->aCurrent['sorting'] = 'boonex_event';

					$this->aCurrent['table'] = 'bx_events_main';
					
					$this->aCurrent['ownFields'] = array('ID', 'Title', 'Description', 'EntryUri', 'Country', 'City', 'Place', 'EventStart',  'EventEnd', 'Tags', 'Categories', 'ResponsibleID', 'PrimPhoto', 'Views', 'Rate', 'RateCount','CommentsCount','FansCount', 'school_id');
    
					$this->aCurrent['join'] = array(
						'profile' => array(
								'type' => 'left',
								'table' => 'Profiles',
								'mainField' => 'ResponsibleID',
								'onField' => 'ID',
								'joinFields' => array('NickName'),
						),
					);

					$this->aCurrent['restriction']['activeStatus']['field'] = 'Status';
					$this->aCurrent['restriction']['owner']['field'] = 'ResponsibleID';
					$this->aCurrent['restriction']['public']['field'] = 'allow_view_event_to';
					
					if(getParam('bx_events_currency_code')){
						$this->aCurrent['restriction']['membership_filter']['field'] = 'EventMembershipViewFilter'; 
					}else{
						unset($this->aCurrent['restriction']['membership_filter']);
					}

					$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'bx_events_main');
					
					$this->sBrowseUrl = "events/browse/$sValue"; 

				}else{
					//[end] event integration - modzzz
 
					$this->aCurrent['join']['events'] = array(
						'type' => 'inner',
						'table' => 'modzzz_schools_events_main',
						'mainField' => 'id',
						'onField' => 'school_id',
						'joinFields' => array('id', 'school_id', 'title', 'uri', 'created', 'icon', 'thumb', 'rate', 'desc', 'event_start', 'event_end', 'author_id'),
					);
	  
					$this->aCurrent['ownFields'] = array();

	  				$this->aCurrent['sorting'] = 'event';

					$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'modzzz_schools_events_main'); 
					
					$this->sBrowseUrl = "events/browse/$sValue";
				}

				$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_school_events', $sTitle);

				break;

            case 'view_events':
						 
				$aSchoolEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iSchoolId = (int)$aSchoolEntry['id'];
				$sTitle = $aSchoolEntry['title'];
 
				//[begin] event integration - modzzz
				if(getParam('modzzz_schools_boonex_events')=='on'){ 
					$oEvent = BxDolModule::getInstance('BxEventsModule');

					$this->aCurrent['sorting'] = 'boonex_event';

					$this->aCurrent['table'] = 'bx_events_main';
					
					$this->aCurrent['ownFields'] = array('ID', 'Title', 'Description', 'EntryUri', 'Country', 'City', 'Place', 'EventStart',  'EventEnd', 'Tags', 'Categories', 'ResponsibleID', 'PrimPhoto', 'Views', 'Rate', 'RateCount','CommentsCount','FansCount', 'school_id');
    
					$this->aCurrent['join'] = array(
						'profile' => array(
								'type' => 'left',
								'table' => 'Profiles',
								'mainField' => 'ResponsibleID',
								'onField' => 'ID',
								'joinFields' => array('NickName'),
						),
					);

					$this->aCurrent['restriction']['activeStatus']['field'] = 'Status';
					$this->aCurrent['restriction']['owner']['field'] = 'ResponsibleID';
					$this->aCurrent['restriction']['public']['field'] = 'allow_view_event_to';
					
					if(getParam('bx_events_currency_code')){
						$this->aCurrent['restriction']['membership_filter']['field'] = 'EventMembershipViewFilter'; 
					}else{
						unset($this->aCurrent['restriction']['membership_filter']);
					}

					$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'bx_events_main');
					
					$this->sBrowseUrl = "events/browse/$sValue"; 
				
				}else{
					$this->aCurrent['join']['events'] = array(
						'type' => 'inner',
						'table' => 'modzzz_schools_events_main',
						'mainField' => 'id',
						'onField' => 'school_id',
						'joinFields' => array('id', 'school_id', 'title', 'uri', 'created', 'icon', 'thumb', 'rate', 'desc', 'event_start', 'event_end', 'author_id'),
					);
	 
					$this->aCurrent['ownFields'] = array();
					
					$this->aCurrent['sorting'] = 'event';
	 
					$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'modzzz_schools_events_main'); 
					
					$this->sBrowseUrl = "events/browse/$sValue";
				}

				$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_school_events', $sTitle);
				break;

            case 'alumni':
						 
				$aSchoolEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iSchoolId = (int)$aSchoolEntry['id'];
				$sTitle = $aSchoolEntry['title'];
 
				$this->aCurrent['join']['student'] = array(
					'type' => 'inner',
					'table' => 'modzzz_schools_student_main',
					'mainField' => 'id',
					'onField' => 'school_id',
					'joinFields' => array('id', 'school_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'author_id','year_entered','year_left','use_profile_desc','use_profile_photo','profile_id'),
				);
  
				$this->aCurrent['ownFields'] = array();

  				$this->aCurrent['sorting'] = 'student';

				$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'modzzz_schools_student_main'); 
				$this->aCurrent['restriction']['type'] = array('value' => 'alumni', 'field' => 'membership_type', 'operator' => '=', 'table' => 'modzzz_schools_student_main'); 
				
				$this->sBrowseUrl = "alumni/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_school_alumni', $sTitle);
				break;

            case 'student':
						 
				$aSchoolEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iSchoolId = (int)$aSchoolEntry['id'];
				$sTitle = $aSchoolEntry['title'];
 
				$this->aCurrent['join']['student'] = array(
					'type' => 'inner',
					'table' => 'modzzz_schools_student_main',
					'mainField' => 'id',
					'onField' => 'school_id',
					'joinFields' => array('id', 'school_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'author_id', 'year_entered','year_left','use_profile_desc','use_profile_photo','profile_id'),
				);
  
				$this->aCurrent['ownFields'] = array();

 				$this->aCurrent['sorting'] = 'student';

				$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'modzzz_schools_student_main'); 
				$this->aCurrent['restriction']['type'] = array('value' => 'student', 'field' => 'membership_type', 'operator' => '=', 'table' => 'modzzz_schools_student_main'); 

				$this->sBrowseUrl = "student/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_school_student', $sTitle);
				break;

            case 'view_alumni':
						 
				$aSchoolEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iSchoolId = (int)$aSchoolEntry['id'];
				$sTitle = $aSchoolEntry['title'];
 
				$this->aCurrent['join']['student'] = array(
					'type' => 'inner',
					'table' => 'modzzz_schools_student_main',
					'mainField' => 'id',
					'onField' => 'school_id',
					'joinFields' => array('id', 'school_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'author_id', 'year_entered', 'year_left','use_profile_desc','use_profile_photo','profile_id'),
				);
 
				$this->aCurrent['ownFields'] = array();
                
				$this->aCurrent['sorting'] = 'student';
 
				$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'modzzz_schools_student_main'); 
				$this->aCurrent['restriction']['type'] = array('value' => 'alumni', 'field' => 'membership_type', 'operator' => '=', 'table' => 'modzzz_schools_student_main'); 

				$this->sBrowseUrl = "alumni/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_school_alumni', $sTitle);
				break;

            case 'view_student':
						 
				$aSchoolEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iSchoolId = (int)$aSchoolEntry['id'];
				$sTitle = $aSchoolEntry['title'];
 
				$this->aCurrent['join']['student'] = array(
					'type' => 'inner',
					'table' => 'modzzz_schools_student_main',
					'mainField' => 'id',
					'onField' => 'school_id',
					'joinFields' => array('id', 'school_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'author_id', 'year_entered', 'year_left','use_profile_desc','use_profile_photo','profile_id'),
				);
 
				$this->aCurrent['ownFields'] = array();
                
				$this->aCurrent['sorting'] = 'student';
 
				$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'modzzz_schools_student_main'); 
				$this->aCurrent['restriction']['type'] = array('value' => 'student', 'field' => 'membership_type', 'operator' => '=', 'table' => 'modzzz_schools_student_main'); 

				$this->sBrowseUrl = "student/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_school_student', $sTitle);
				break;
            case 'news':
						 
				$oMain = $this->getMain();
				$aSchoolEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iSchoolId = (int)$aSchoolEntry['id'];
				$sTitle = $aSchoolEntry['title'];
 
				$this->aCurrent['join']['news'] = array(
					'type' => 'inner',
					'table' => 'modzzz_schools_news_main',
					'mainField' => 'id',
					'onField' => 'school_id',
					'joinFields' => array('id', 'school_id', 'title', 'uri', 'created', 'icon', 'thumb', 'rate', 'desc', 'author_id'),
				);
  
				$this->aCurrent['ownFields'] = array();

 				$this->aCurrent['sorting'] = 'news';

				$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'modzzz_schools_news_main'); 
				
				$this->sBrowseUrl = "news/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_school_news', $sTitle);
				break;

			case 'view_news':
						 
				$oMain = $this->getMain();
				$aSchoolEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iSchoolId = (int)$aSchoolEntry['id'];
				$sTitle = $aSchoolEntry['title'];
 
				$this->aCurrent['join']['news'] = array(
					'type' => 'inner',
					'table' => 'modzzz_schools_news_main',
					'mainField' => 'id',
					'onField' => 'school_id',
					'joinFields' => array('id', 'school_id', 'title', 'uri', 'created', 'icon', 'thumb', 'rate', 'desc', 'author_id'),
				);
 
				$this->aCurrent['ownFields'] = array();
                
				$this->aCurrent['sorting'] = 'news';
 
				$this->aCurrent['restriction']['school_id'] = array('value' => $iSchoolId, 'field' => 'school_id', 'operator' => '=', 'table' => 'modzzz_schools_news_main'); 
				
				$this->sBrowseUrl = "news/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_schools_caption_browse_school_news', $sTitle);
				break;
 
            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_modzzz_schools');
                break;

            default:
                $this->isError = true;
        }

		if(in_array($sMode, array('events','view_events')) && getParam('modzzz_schools_boonex_events')=='on'){
			
			$oEvent = BxDolModule::getInstance('BxEventsModule');

			$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('bx_events_perpage_browse');

			if (isset($this->aCurrent['rss']))
				$this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oEvent->_oConfig->getBaseUri() . $this->sBrowseUrl;

			if (bx_get('rss')) {
				$this->aCurrent['ownFields'][] = 'desc';
				$this->aCurrent['ownFields'][] = 'created';
				$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('bx_events_max_rss_num');
			}

			bx_import('Voting', $oEvent->_aModule);
			$oVotingView = new BxEventsVoting ('bx_events', 0);
			$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

			$this->sFilterName = 'bx_events_filter';

		}else{ 

			$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_schools_perpage_browse');

			if (isset($this->aCurrent['rss']))
				$this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . $this->sBrowseUrl;

			if (bx_get('rss')) {
				$this->aCurrent['ownFields'][] = 'desc';
				$this->aCurrent['ownFields'][] = 'created';
				$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_schools_max_rss_num');
			}

			bx_import('Voting', $oMain->_aModule);
			$oVotingView = new BxSchoolsVoting ('modzzz_schools', 0);
			$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

			$this->sFilterName = 'filter';
		}

        parent::__construct();
    }

    function getAlterOrder() {
		if ($this->aCurrent['sorting'] == 'last') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_schools_main`.`created` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'top') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_schools_main`.`rate` DESC, `modzzz_schools_main`.`rate_count` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'popular') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_schools_main`.`views` DESC";
			return $aSql;
		//added instructors modification
		} elseif ($this->aCurrent['sorting'] == 'instructor') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_schools_instructors_main`.`created` DESC";
			return $aSql;  
		//added courses modification
		} elseif ($this->aCurrent['sorting'] == 'course') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_schools_courses_main`.`created` DESC";
			return $aSql; 
		//added news modification
		} elseif ($this->aCurrent['sorting'] == 'news') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_schools_news_main`.`created` DESC";
			return $aSql; 
		//added student modification
		} elseif ($this->aCurrent['sorting'] == 'student') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_schools_student_main`.`created` DESC";
			return $aSql;  
		//added events modification

		} elseif ($this->aCurrent['sorting'] == 'event') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_schools_events_main`.`created` DESC";
			return $aSql;   

		//[begin] event integration - modzzz
		} elseif ($this->aCurrent['sorting'] == 'boonex_event') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `bx_events_main`.`Date` DESC";
			return $aSql;
		//[end] event integration - modzzz

		}

	    return array();
    }

    function displayResultBlock () {
        global $oFunctions;
        $s = parent::displayResultBlock ();
        if ($s) {
            $oMain = $this->getMain();
            $GLOBALS['oSysTemplate']->addDynamicLocation($oMain->_oConfig->getHomePath(), $oMain->_oConfig->getHomeUrl());
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
        }
        return '';
    }

    function getMain() {
        return BxDolModule::getInstance('BxSchoolsModule');
    }

    function getRssUnitLink (&$a) {
        $oMain = $this->getMain();
        return BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'view/' . $a['uri'];
    }
    
    function _getPseud () {
        return array(    
            'id' => 'id',
            'title' => 'title',
            'uri' => 'uri',
            'created' => 'created',
            'author_id' => 'author_id',
            'NickName' => 'NickName',
            'thumb' => 'thumb', 
        );
    }
 
    function displayMatesResultBlock () { 
        $aData = $this->getSearchData();
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $s = $this->addCustomParts();
            foreach ($aData as $aValue) {
                $s .= $this->displayMatesSearchUnit($aValue);
            }
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
        }
        return '';
    }

    function displayMatesSearchUnit ($aData) {
        $oMain = $this->getMain();
		return $oMain->_oTemplate->mate_unit($aData, $this->sUnitTemplate);
    }
 
	//[begin] claim
    function displayClaimResultBlock ($sType) { 
        $aData = $this->getSearchData();
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $s = $this->addCustomParts();
            foreach ($aData as $aValue) {
                $s .= $this->displayClaimSearchUnit($sType, $aValue);
            }
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
        }
        return '';
    }

    function displayClaimSearchUnit ($sType, $aData) {
        $oMain = $this->getMain();
	 
		return $oMain->_oTemplate->claim_unit($aData, $this->sUnitTemplate, $this->oVotingView); 
	}
    //[end] claim


	//instructor etc.
     function displaySubProfileResultBlock ($sType) { 
        $aData = $this->getSearchData();  
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $s = $this->addCustomParts();
            foreach ($aData as $aValue) {
                $s .= $this->displaySubProfileSearchUnit($sType, $aValue);
            }
 
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
        }
        return '';
    }

    function displaySubProfileSearchUnit ($sType, $aData) {
        $oMain = $this->getMain();
		
		$sResult = '';

		switch($sType){  
			case 'instructors':
 
				bx_import('InstructorsVoting', $oMain->_aModule);
				$oVotingView = new BxSchoolsInstructorsVoting ('modzzz_schools_instructors', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->instructors_unit($aData, 'instructors_unit', $this->oVotingView); 
			break; 
			case 'courses':
 
				bx_import('CoursesVoting', $oMain->_aModule);
				$oVotingView = new BxSchoolsCoursesVoting ('modzzz_schools_courses', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->courses_unit($aData, 'courses_unit', $this->oVotingView); 
			break;
			case 'news':
 
				bx_import('NewsVoting', $oMain->_aModule);
				$oVotingView = new BxSchoolsNewsVoting ('modzzz_schools_news', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->news_unit($aData, 'news_unit', $this->oVotingView); 
			break; 
			case 'student':
 
				bx_import('StudentVoting', $oMain->_aModule);
				$oVotingView = new BxSchoolsStudentVoting ('modzzz_schools_student', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->student_unit($aData, 'student_unit', $this->oVotingView); 
			break;  
			case 'events':
 
 				if(getParam('modzzz_schools_boonex_events')=='on'){  
					$oEvents = BxDolModule::getInstance('BxEventsModule');
					bx_import('Voting', $oEvents->_aModule);
					$oVotingView = new BxEventsVoting ('bx_events', 0);
					$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
	 
					$sResult = $oEvents->_oTemplate->unit($aData, 'unit', $this->oVotingView); 
				}else{
					bx_import('EventsVoting', $oMain->_aModule);
					$oVotingView = new BxSchoolsEventsVoting ('modzzz_schools_events', 0);
					$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
	 
					$sResult = $oMain->_oTemplate->events_unit($aData, 'events_unit', $this->oVotingView);  
				}
			break;  
		}

		return $sResult;
	}
 

    function showPagination($sUrlAdmin = false)
    {
        $oMain = $this->getMain();
        $oConfig = $oMain->_oConfig;
        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ($sUrlAdmin ? $sUrlAdmin : $this->sBrowseUrl);
 
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');
		
		//[begin] added keyword modification (modzzz)
		$sKeyWord = bx_get('keyword');
		if ($sKeyWord !== false)
			$sLink = 'keyword=' . clear_xss($sKeyWord) . '&';
   
		$sUrlStart .= $sLink;
		//[end] added keyword modification (modzzz)

        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sUrlStart . 'page={page}&per_page={per_page}' . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) : ''),
            'count' => $this->aCurrent['paginate']['totalNum'],
            'per_page' => $this->aCurrent['paginate']['perPage'],
            'page' => $this->aCurrent['paginate']['page'],
            'per_page_changer' => true,
            'page_reloader' => true,
            'on_change_page' => '',
            'on_change_per_page' => "document.location='" . $sUrlStart . "page=1&per_page=' + this.value + '" . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) ."';": "';"),
        ));

        return '<div class="clear_both"></div>'.$oPaginate->getPaginate();
    }

    function setPublicUnitsOnly($isPublic) {
		if($isPublic){
			 $iLogged = getLoggedId();
			 if($iLogged){
				$this->aCurrent['restriction']['public']['operator'] = 'in';
				$this->aCurrent['restriction']['public']['value'] = array(BX_DOL_PG_ALL,BX_DOL_PG_MEMBERS);
			 }else{
				$this->aCurrent['restriction']['public']['value'] = BX_DOL_PG_ALL;
			 }
		}else{
			 $this->aCurrent['restriction']['public']['value'] = false;
		}
    }

    function getCount11 ()
    {
        $aJoins = $this->getJoins(false);
        $sqlQuery =  "SELECT COUNT(*) FROM `{$this->aCurrent['table']}` {$aJoins['join']} " . $this->getRestriction() . " {$aJoins['groupBy']}";

		echo $sqlQuery;exit;
        return (int)db_value($sqlQuery);
    }


  
}

