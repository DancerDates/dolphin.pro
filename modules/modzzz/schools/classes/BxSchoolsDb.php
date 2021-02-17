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

bx_import('BxDolTwigModuleDb');

/*
 * Schools module Data
 */
class BxSchoolsDb extends BxDolTwigModuleDb {	

	/*
	 * Constructor.
	 */
	function __construct(&$oConfig) {
        parent::__construct($oConfig);

 		$this->_oConfig = $oConfig;
   
		/*******Instructors ************/
		$this->_sTableInstructors = 'instructors_main'; 
        $this->_sInstructorsPrefix = 'modzzz_schools_instructors';
	    $this->_sTableInstructorsMediaPrefix = 'instructors_';

		/*******Courses ************/
		$this->_sTableCourses = 'courses_main'; 
        $this->_sCoursesPrefix = 'modzzz_schools_courses';
	    $this->_sTableCoursesMediaPrefix = 'courses_';

		/*******Events ************/
		$this->_sTableEvents = 'events_main'; 
        $this->_sEventsPrefix = 'modzzz_schools_events';
	    $this->_sTableEventsMediaPrefix = 'events_';

		/*******News ************/
		$this->_sTableNews = 'news_main'; 
        $this->_sNewsPrefix = 'modzzz_schools_news';
	    $this->_sTableNewsMediaPrefix = 'news_';

		/*******Student ************/
		$this->_sTableStudent = 'student_main'; 
        $this->_sStudentPrefix = 'modzzz_schools_student';
	    $this->_sTableStudentMediaPrefix = 'student_';
 
        $this->_sTableMain = 'main';
        $this->_sTableMediaPrefix = '';
        $this->_sFieldId = 'id';
        $this->_sFieldAuthorId = 'author_id';
        $this->_sFieldUri = 'uri';
        $this->_sFieldTitle = 'title';
        $this->_sFieldDescription = 'desc';
        $this->_sFieldTags = 'tags';
        $this->_sFieldThumb = 'thumb';
        $this->_sFieldStatus = 'status';
        $this->_sFieldFeatured = 'featured';
        $this->_sFieldCreated = 'created';
        $this->_sFieldJoinConfirmation = 'join_confirmation';
        $this->_sFieldFansCount = 'fans_count';
        $this->_sFieldStudentCount = 'student_count';
        $this->_sFieldAlumniCount = 'alumni_count';
        $this->_sFieldEventsCount = 'events_count';
        $this->_sFieldNewsCount = 'news_count';
        $this->_sFieldCoursesCount = 'courses_count';
        $this->_sFieldInstructorsCount = 'instructors_count';
     
        $this->_sTableFans = 'fans';
        $this->_sTableEventFans = 'events_fans';
        $this->_sTableAdmins = 'admins';
        $this->_sFieldAllowViewTo = 'allow_view_school_to';  
	}

    function getAutoCompleteList($sQuery, $iLimit = 10 )
	{
		// init some needed variables ;
		$iLimit = (int) $iLimit;
		$sQuery = process_db_input($sQuery, BX_TAGS_STRIP);

		$sOutputHtml = null;

		$aList = $this->getAll("SELECT `NickName` FROM `Profiles` WHERE `NickName` LIKE '%{$sQuery}%' LIMIT {$iLimit}");

		foreach($aList as $aRow) {
			$sOutputHtml .= $aRow['NickName'] . "\n";
		}

		return $sOutputHtml;
	}
 
	function getBoonexEventsCount($iId){ 
		return $this->getOne("SELECT COUNT(`ID`) FROM `bx_events_main` WHERE `school_id`='$iId' AND `Status`='approved'");  
	}

    function deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin) {
        if ($iRet = parent::deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin)) {
            $this->query ("DELETE FROM `" . $this->_sPrefix . "fans` WHERE `id_entry` = $iId");
            $this->query ("DELETE FROM `" . $this->_sPrefix . "admins` WHERE `id_entry` = $iId");
            $this->query ("DELETE FROM `" . $this->_sPrefix . "activity` WHERE `school_id` = $iId");
			$this->query("DELETE FROM `" . $this->_sPrefix . "claim` WHERE `listing_id`='$iId'");  
 
			$this->deleteEntryMediaAll ($iId, 'images');
            $this->deleteEntryMediaAll ($iId, 'videos');
            $this->deleteEntryMediaAll ($iId, 'sounds');
            $this->deleteEntryMediaAll ($iId, 'files');

			$this->removeEntryYoutube($iId);   
        }
        return $iRet;
    }
 
 	function getItemCount($iId, $sType){
		$iId = (int)$iId;

		switch($sType){ 
			case 'student':
				$sField = $this->_sFieldStudentCount;
			break;
			case 'alumni':
				$sField = $this->_sFieldAlumniCount;
			break;
			case 'event':
				$sField = $this->_sFieldEventsCount;
			break;
			case 'news':
				$sField = $this->_sFieldNewsCount;
			break;
			case 'course':
				$sField = $this->_sFieldCoursesCount;
			break;
			case 'instructor':
				$sField = $this->_sFieldInstructorsCount;
			break;
		}

		return (int)$this->getOne("SELECT `{$sField}` FROM `" . $this->_sPrefix . "main` WHERE `id`=$iId"); 
	}

 	function updateItemCount($iId, $sType, $sAction='+'){
		$iId = (int)$iId;

		switch($sType){ 
			case 'student':
				$sField = $this->_sFieldStudentCount;
			break;
			case 'alumni':
				$sField = $this->_sFieldAlumniCount;
			break;
			case 'event':
				$sField = $this->_sFieldEventsCount;
			break;
			case 'news':
				$sField = $this->_sFieldNewsCount;
			break;
			case 'course':
				$sField = $this->_sFieldCoursesCount;
			break;
			case 'instructor':
				$sField = $this->_sFieldInstructorsCount;
			break;
		}
		
		if($sAction=='+')
			$this->query("UPDATE `" . $this->_sPrefix . "main`  SET  `{$sField}` = `{$sField}` +1 WHERE `id`=$iId");  
		else
			$this->query("UPDATE `" . $this->_sPrefix . "main`  SET  `{$sField}` = `{$sField}` -1 WHERE `id`=$iId AND `{$sField}` > 0");   
	}
   
 	function getProfileId($sNickName){
		return $this->getOne("SELECT `ID` FROM `Profiles` WHERE `NickName`='$sNickName' AND `Status`='Active' LIMIT 1"); 
	}
 
 	function getStateName($sCountry, $sState=''){
		$sState = $this->getOne("SELECT `State` FROM `States` WHERE `CountryCode`='{$sCountry}' AND `StateCode`='{$sState}' LIMIT 1");
 
		return $sState;
	}
 
	function getStateOptions($sCountry='') {
		$aStates = $this->getStateArray ($sCountry);
			
		$sOptions = "<option value=''></option>";
		foreach($aStates as $aEachCode=>$aEachState){ 
			$sOptions .= "<option value='{$aEachCode}'>{$aEachState}</option>";
		}

		return $sOptions;
	}

 	function getStateArray($sCountry=''){
 
		$aStates = array();
		$aDbStates = $this->getAll("SELECT * FROM `States` WHERE `CountryCode`='{$sCountry}'  ORDER BY `State` ");
		 
		foreach ($aDbStates as $aEachState){
			$sState = $aEachState['State'];
			$sStateCode = $aEachState['StateCode'];
			
			$aStates[$sStateCode] = $sState;
  		} 
		return $aStates;
	}
 
	function getCategories($sType)
	{ 
 		$aAllEntries = $this->getAll("SELECT `Category` FROM `sys_categories` WHERE `Type` = '{$sType}' AND `Status`='active' AND Owner=0 ORDER BY `Category`"); 
		
		return $aAllEntries; 
	}

	function getCategoryCount($sType,$sCategory)
	{ 
		$sCategory = process_db_input($sCategory);
		$iNumCategory = $this->getOne("SELECT count(`" . $this->_sPrefix . "main`.`id`) FROM `" . $this->_sPrefix . "main`  inner JOIN `sys_categories` ON `sys_categories`.`ID`=`" . $this->_sPrefix . "main`.`id` WHERE 1 AND  `sys_categories`.`Category` IN('{$sCategory}') AND `sys_categories`.`Type` = '{$sType}' AND `" . $this->_sPrefix . "main`.`status`='approved'"); 
		
		return $iNumCategory;
	}

	function flagActivity($sType, $iEntryId, $iProfileId, $aParams=array()){
 
		if(!$iEntryId)
			return;

		switch($sType){ 
			case 'mark_as_featured':
				foreach($aParams as $sKey=>$iValue){ 
					if($sKey=='Featured'){
						if($iValue)
							$sType = "unfeatured";
						else
							$sType = "featured"; 
					}
				}
			break; 
		}

		$aTypes = array(
			'add' => '_modzzz_schools_feed_post',
			'delete' => '_modzzz_schools_feed_delete',
			'change' => '_modzzz_schools_feed_post_change',
			'join' => '_modzzz_schools_feed_join',
			'unjoin' => '_modzzz_schools_feed_unjoin',
			'remove' => '_modzzz_schools_feed_remove',
			'rate' => '_modzzz_schools_feed_rate',
			'commentPost' => '_modzzz_schools_feed_comment',
			'featured' => '_modzzz_schools_feed_featured',
			'unfeatured' => '_modzzz_schools_feed_unfeatured',
			'makeAdmin' => '_modzzz_schools_feed_make_admin',
			'removeAdmin' => '_modzzz_schools_feed_remove_admin'  
		);
   
		$aDataEntry = $this->getEntryById($iEntryId);
		
		$sProfileNick = process_db_input(getNickName($iProfileId));
		$sProfileLink = getProfileLink($iProfileId);
		$sSchoolUri = $aDataEntry['uri'];
		$sSchoolTitle = process_db_input($aDataEntry['title']);
		$sSchoolUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $sSchoolUri;
	
		$sParams = "profile_id|{$iProfileId};profile_link|{$sProfileLink};profile_nick|{$sProfileNick};entry_url|{$sSchoolUrl};entry_title|{$sSchoolTitle}";

		$sLangKey = $aTypes[$sType];
 
		$this->query("INSERT INTO modzzz_schools_activity(`school_id`,`lang_key`,`params`,`type`) VALUES ($iEntryId,'$sLangKey','$sParams','$sType')");

	}

	function getActivityFeed($iLimit=5){

		return $this->getAll("SELECT `school_id`,`lang_key`,`params`,`type`,UNIX_TIMESTAMP(`date`) AS `date` FROM `modzzz_schools_activity` ORDER BY `date` DESC LIMIT $iLimit"); 
	}
 
	function getLatestComments($iLimit=5){

		return $this->getAll("SELECT `cmt_object_id`,`cmt_author_id`,`cmt_text`, UNIX_TIMESTAMP(`cmt_time`) AS `date` FROM `" . $this->_sPrefix . "cmts` WHERE `cmt_text` NOT LIKE '<object%' ORDER BY `cmt_time` DESC LIMIT $iLimit"); 
	} 

	function getLatestForumPosts($iLimit=5, $iEntryId=0){

		if($iEntryId)
			$sQueryId = "t.`forum_id`=$iEntryId AND ";

		return $this->getAll("SELECT e.`title`,  e.`thumb`, f.`forum_uri`, p.`user`, p.`post_text`, t.`topic_uri`, t.`topic_title`,p.`when` FROM `" . $this->_sPrefix . "forum` f, `" . $this->_sPrefix . "forum_topic` t, `" . $this->_sPrefix . "forum_post` p, `" . $this->_sPrefix . "main` e WHERE {$sQueryId}  p.`topic_id`=t.`topic_id` AND t.`forum_id`=f.`forum_id` AND e.`ID`=f.`entry_id` ORDER BY  p.`when` LIMIT $iLimit");  
	} 
  
    function isFan($iEntryId, $iProfileId, $isConfirmed) {
        $isConfirmed = $isConfirmed ? 1 : 0;
        return $this->getOne ("SELECT `when` FROM `" . $this->_sPrefix . $this->_sTableFans . "` WHERE `id_entry` = '$iEntryId' AND `id_profile` = '$iProfileId' AND `confirmed` = '$isConfirmed' LIMIT 1");
    }
  
    //[begin] claim
	function saveClaimRequest($iEntryId, $iProfileId, $sClaimText) {
		$sClaimText = process_db_input($sClaimText);
 		$iTime = time();
 
		$bExists = (int)$this->getOne ("SELECT `listing_id` FROM `" . $this->_sPrefix . "claim` WHERE `listing_id`='$iEntryId' AND `member_id`='$iProfileId' LIMIT 1"); 
		if(!$bExists){ 
			$this->query("INSERT INTO `" . $this->_sPrefix . "claim` SET `listing_id`='$iEntryId', `member_id`='$iProfileId', `message`='$sClaimText', `processed`=0, `claim_date`=$iTime"); 
		}  

		return $bExists;
	}

 	function getClaimById($iEntryId) { 
		$aClaim = $this->getRow("SELECT `id`,`listing_id`,`member_id`,`message`,`processed`,`claim_date`,`assign_date` FROM `" . $this->_sPrefix . "claim` WHERE `id`='$iEntryId'");   
	
		return $aClaim;
	}
 
	function deleteClaim($iEntryId, $isAdmin=false) { 

		if(!$isAdmin)
			return;
 
		$this->query("DELETE FROM `" . $this->_sPrefix . "claim` WHERE `id`='$iEntryId'");   
	}
  
	function assignClaim($iClaimId, $isAdmin=false) { 

		if(!$isAdmin)
			return;

 		$iTime = time();

 		$aClaim = $this->getClaimById($iClaimId);
		$iClaimantId = (int)$aClaim['member_id'];
 		$iEntryId = (int)$aClaim['listing_id'];

		$this->query("UPDATE `" . $this->_sPrefix . "main` SET `author_id`=$iClaimantId WHERE `id`='$iEntryId'");   

		$this->query("UPDATE `" . $this->_sPrefix . "claim` SET `assign_date`=$iTime, `processed`=1 WHERE `id`='$iClaimId'");   

		$this->alertOnAction('modzzz_schools_claim_assign', $iEntryId, $iClaimantId );  
	} 

 	function getSiteAdmins() { 
	
 		$aAllAdmins = $this->getAll("SELECT `ID` FROM `Profiles` WHERE `Role` & " . BX_DOL_ROLE_ADMIN . " OR `Role` & " . BX_DOL_ROLE_MODERATOR . " AND `Status`='Active'"); 
		
		return $aAllAdmins; 
	} 

	function alertOnAction($sTemplate, $iListingId, $iRecipientId=0, $iDays=0, $bAdmin=false) {
	   
		$aPlus = array();

		if($iListingId){
			$aDataEntry = $this->getEntryById($iListingId);
			$aPlus['ListTitle'] = $aDataEntry['title']; 
			$aPlus['ListLink'] = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri']; 
		}

		if($iRecipientId){
			$aRecipient = getProfileInfo($iRecipientId); 
			$aPlus['NickName'] = $aRecipient['NickName']; 
			$aPlus['NickLink'] = getProfileLink($iRecipientId);
			$sNotifyEmail = $aRecipient['Email']; 
		}

		$aPlus['Days'] = $iDays; 
		$aPlus['SiteName'] = isset($GLOBALS['site']['title']) ? $GLOBALS['site']['title'] : getParam('site_title');
		$aPlus['SiteUrl'] = isset($GLOBALS['site']['url']) ;
 
		$oEmailTemplate = new BxDolEmailTemplates(); 

		$aTemplate = $oEmailTemplate->getTemplate($sTemplate, $iRecipientId);
		$sMessage = $aTemplate['Body'];
		$sSubject = $aTemplate['Subject'];   
		$sSubject = str_replace("<SiteName>", $aPlus['SiteName'], $sSubject);

		if($bAdmin){
			$sNotifyEmail = $GLOBALS['site']['email_notify'];
			$iRecipientId = 0;
		}
 
		sendMail($sNotifyEmail, $sSubject, $sMessage, $iRecipientId, $aPlus, 'html' );   
	} 
	//[end] claim
  
	/***** INSTRUCTORS **************************************/
    function isInstructor($iSchoolId, $iProfileId) {
         return $this->getOne ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableInstructors . "` WHERE `school_id` = '$iSchoolId' AND `profile_id` = '$iProfileId' LIMIT 1");
    }

	function getFormInstructors(){
		return $this->getPairs("SELECT `id`, `title` FROM `" . $this->_sPrefix . "instructors_main` WHERE `status`='approved'", 'id', 'title'); 
	}

    function getInstructorMediaIds ($iEntryId, $sMediaType) {
        return $this->getPairs ("SELECT `id` FROM `" . $this->_sPrefix . "{$sMediaType}` WHERE `id_entry` = '$iEntryId'", 'id', 'id');
    }
  
    function getInstructorEntryById($iId) {
		$iId = (int)$iId;

         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableInstructors . "` WHERE `{$this->_sFieldId}` = $iId LIMIT 1");
    }
 
    function getInstructorEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableInstructors . "` WHERE `{$this->_sFieldUri}` = '$sUri' LIMIT 1");
    }

 	function getInstructors($iEntryId, $iLimit=0){
		$iEntryId = (int)$iEntryId;

		if($iLimit)
			$sQuery = "LIMIT 0, {$iLimit}";

		return $this->getAll("SELECT `id`, `school_id`, `uri`, `title`, `desc`, `position`,  `thumb` FROM `" . $this->_sPrefix . $this->_sTableInstructors . "` WHERE `school_id`={$iEntryId} {$sQuery}");
	}

    function getInstructorsEntryById($iId) {
		$iId = (int)$iId;

         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableInstructors . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getInstructorsEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableInstructors . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteInstructorsByIdAndOwner ($iId, $iSchoolId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableInstructors . "` WHERE `id` = $iId AND `school_id`=$iSchoolId $sWhere LIMIT 1")))
            return false;
 
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableInstructorsMediaPrefix . "images` WHERE `entry_id` = $iId");
 
        return true;
    } 
 
    function deleteInstructors ($iSchoolId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        
		$aInstructors = $this->getAllSubItems('instructors', $iSchoolId);
  
		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableInstructors . "` WHERE `school_id`=$iSchoolId $sWhere")))
            return false;

		foreach($aInstructors as $aEachInstructors){
			
			$iId = (int)$aEachInstructors['id'];
 
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableInstructorsMediaPrefix . "images` WHERE `entry_id` = $iId"); 
		}

        return true;
    } 
	/*****[END] INSTRUCTORS **************************************/
  
	/***** COURSES **************************************/
     function getCourseMediaIds ($iEntryId, $sMediaType) {
        return $this->getPairs ("SELECT `id` FROM `" . $this->_sPrefix . "{$sMediaType}` WHERE `id_entry` = '$iEntryId'", 'id', 'id');
    }
  
    function getCourseEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableCourses . "` WHERE `{$this->_sFieldId}` = $iId LIMIT 1");
    }
 
    function getCourseEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableCourses . "` WHERE `{$this->_sFieldUri}` = '$sUri' LIMIT 1");
    }

 	function getCourses($iEntryId, $iLimit=0){
		
		if($iLimit)
			$sQuery = "LIMIT 0, {$iLimit}";

		return $this->getAll("SELECT `id`, `school_id`, `uri`, `title`, `desc`, `thumb` FROM `" . $this->_sPrefix . $this->_sTableCourses . "` WHERE `school_id`={$iEntryId} {$sQuery}");
	}

    function getCoursesEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableCourses . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getCoursesEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableCourses . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteCoursesByIdAndOwner ($iId, $iSchoolId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableCourses . "` WHERE `id` = $iId AND `school_id`=$iSchoolId $sWhere LIMIT 1")))
            return false;
 
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableCoursesMediaPrefix . "images` WHERE `entry_id` = $iId");
 
        return true;
    } 
 
    function deleteCourses ($iSchoolId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        
		$aCourses = $this->getAllSubItems('courses', $iSchoolId);
  
		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableCourses . "` WHERE `school_id`=$iSchoolId $sWhere")))
            return false;

		foreach($aCourses as $aEachCourses){
			
			$iId = (int)$aEachCourses['id'];
 
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableCoursesMediaPrefix . "images` WHERE `entry_id` = $iId"); 
		}

        return true;
    } 
	/*****[END] COURSES **************************************/
     

	/***** EVENTS **************************************/
     function getEventMediaIds ($iEntryId, $sMediaType) {
        return $this->getPairs ("SELECT `id` FROM `" . $this->_sPrefix . "{$sMediaType}` WHERE `id_entry` = '$iEntryId'", 'id', 'id');
    }
  
    function getEventEntryByIdAndOwner ($iId, $iOwner, $isAdmin)
    {
        $sWhere = '';
        if (!$isAdmin)
            $sWhere = " AND `{$this->_sFieldAuthorId}` = '$iOwner' ";
        return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableEvents . "` WHERE `{$this->_sFieldId}` = $iId $sWhere LIMIT 1");
    }

    function getEventsEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableEvents . "` WHERE `{$this->_sFieldId}` = $iId LIMIT 1");
    }
 
    function getEventsEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableEvents . "` WHERE `{$this->_sFieldUri}` = '$sUri' LIMIT 1");
    }
   
    function deleteEventsByIdAndOwner ($iId, $iSchoolId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEvents . "` WHERE `id` = $iId AND `school_id`=$iSchoolId $sWhere LIMIT 1")))
            return false;
 
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventsMediaPrefix . "images` WHERE `entry_id` = $iId");
 
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventFans . "` WHERE `id_entry` = $iId");

        return true;
    } 
 
    function deleteEvents ($iSchoolId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        
		$aEvents = $this->getAllSubItems('events', $iSchoolId);
  
		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEvents . "` WHERE `school_id`=$iSchoolId $sWhere")))
            return false;

		foreach($aEvents as $aEachEvents){
			
			$iId = (int)$aEachEvents['id'];
 
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventsMediaPrefix . "images` WHERE `entry_id` = $iId"); 

			$this->query ("DELETE FROM `" . $this->_sPrefix . "events_fans` WHERE `id_entry` = $iId"); 
		}

        return true;
    } 
	/*****[END] EVENTS **************************************/ 


	/***** Student **************************************/ 

	function getMySchools($iProfileId){
        $aSchools = $this->getAll ("SELECT DISTINCT `school_id` as `id` FROM `" . $this->_sPrefix . $this->_sTableStudent . "` WHERE `profile_id` = '$iProfileId'");
		 
		$aList = array();
		$aList[0] = -1; 
		foreach($aSchools as $aEachSchool){
			$aList[] = $aEachSchool['id']; 
		}
 
		return $aList; 
	}

    function getStudentMediaIds ($iEntryId, $sMediaType) {
        return $this->getPairs ("SELECT `id` FROM `" . $this->_sPrefix . "{$sMediaType}` WHERE `id_entry` = '$iEntryId'", 'id', 'id');
    }
    
    function getStudentEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableStudent . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getStudentEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableStudent . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function isStudent($iSchoolId, $iProfileId) {
         return $this->getOne ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableStudent . "` WHERE `school_id` = '$iSchoolId' AND `profile_id` = '$iProfileId' LIMIT 1");
    }
 
    function deleteStudentByIdAndOwner ($iId, $iSchoolId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStudent . "` WHERE `id` = $iId AND `school_id`=$iSchoolId $sWhere LIMIT 1")))
            return false;
 
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStudentMediaPrefix . "images` WHERE `entry_id` = $iId");
 
        return true;
    } 
 
    function deleteStudent ($iSchoolId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        
		$aStudent = $this->getAllSubItems('student', $iSchoolId);
  
		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStudent . "` WHERE `school_id`=$iSchoolId $sWhere")))
            return false;

		foreach($aStudent as $aEachStudent){
			
			$iId = (int)$aEachStudent['id'];
 
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStudentMediaPrefix . "images` WHERE `entry_id` = $iId"); 
		}

        return true;
    } 
	/*****[END] Student **************************************/


	/***** NEWS **************************************/ 
    function getNewsMediaIds ($iEntryId, $sMediaType) {
        return $this->getPairs ("SELECT `id` FROM `" . $this->_sPrefix . "{$sMediaType}` WHERE `id_entry` = '$iEntryId'", 'id', 'id');
    }
    
    function getNewsEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getNewsEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteNewsByIdAndOwner ($iId, $iSchoolId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `id` = $iId AND `school_id`=$iSchoolId $sWhere LIMIT 1")))
            return false;
 
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableNewsMediaPrefix . "images` WHERE `entry_id` = $iId");
 
        return true;
    } 
 
    function deleteNews ($iSchoolId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        
		$aNews = $this->getAllSubItems('news', $iSchoolId);
  
		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `school_id`=$iSchoolId $sWhere")))
            return false;

		foreach($aNews as $aEachNews){
			
			$iId = (int)$aEachNews['id'];
 
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableNewsMediaPrefix . "images` WHERE `entry_id` = $iId"); 
		}

        return true;
    } 
	/*****[END] NEWS **************************************/

	function getAllSubItems($sSubItem, $iSchoolId){
		$aSubItems = array();
		$iSchoolId = (int)$iSchoolId;

		switch($sSubItem){
			case 'instructors':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableInstructors . "` WHERE `school_id`=$iSchoolId");
			break; 
			case 'courses':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableCourses . "` WHERE `school_id`=$iSchoolId");
			break; 
			case 'events':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableEvents . "` WHERE `school_id`=$iSchoolId");
			break; 
			case 'student':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableStudent . "` WHERE `school_id`=$iSchoolId");
			break; 
			case 'news':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `school_id`=$iSchoolId");
			break; 

		}
 
		return $aSubItems;	
	}

	//BEGIN Youtube 
	function getYoutubeVideos($iEntryId, $iLimit=0){
		$iEntryId = (int)$iEntryId;
		
		if($iLimit)
			$sQuery = "LIMIT 0, {$iLimit}";

		return $this->getAll("SELECT `id`, `id_entry`, `title`, `url` FROM `" . $this->_sPrefix . "youtube` WHERE `id_entry`={$iEntryId} {$sQuery}");
	}

 	function addYoutube($iEntryId){
		if(is_array($_POST['video_link'])){ 
			foreach($_POST['video_link'] as $iKey=>$sValue){
			
				$sVideoLink = process_db_input($sValue);
 				$sVideoTitle = process_db_input($_POST['video_title'][$iKey]);
	 
				if(trim($sVideoLink)){  
					$this->query("INSERT INTO `" . $this->_sPrefix . "youtube` SET `id_entry`=$iEntryId, `url`='$sVideoLink', `title`='$sVideoTitle'");
				}
			} 
		}
	}

	function removeEntryYoutube($iEntryId){ 
	   
		$this->query("DELETE FROM `" . $this->_sPrefix . "youtube` WHERE `id_entry`='$iEntryId'"); 
	}

	function removeYoutube($iEntryId, $iYoutubeId){ 
	   
		$this->query("DELETE FROM `" . $this->_sPrefix . "youtube` WHERE `id_entry`='$iEntryId' AND `id`='$iYoutubeId'");   
	}

    function getYoutubeIds ($iEntryId) {
		return $this->getPairs ("SELECT `id` FROM `" . $this->_sPrefix . "youtube`  WHERE `id_entry` = '$iEntryId'", 'id', 'id');
    }
	//END Youtube
 
	function getSemesters($sVal=''){
		$aVars = array(
			1 => _t('_modzzz_schools_one'),
			2 => _t('_modzzz_schools_two'),
			3 => _t('_modzzz_schools_three'),
			4 => _t('_modzzz_schools_four'),
		);

		return ($sVal) ? $aVars[$sVal] : $aVars;
	}
 
    function joinEventEntry($iEntryId, $iProfileId, $isConfirmed)
    {
        $isConfirmed = $isConfirmed ? 1 : 0;
        $iRet = $this->query ("INSERT IGNORE INTO `" . $this->_sPrefix . $this->_sTableEventFans . "` SET `id_entry` = '$iEntryId', `id_profile` = '$iProfileId', `confirmed` = '$isConfirmed', `when` = '" . time() . "'");
        if ($iRet && $isConfirmed)
            $this->query ("UPDATE `" . $this->_sPrefix . "events_main` SET `" . $this->_sFieldFansCount . "` = `" . $this->_sFieldFansCount . "` + 1 WHERE `id` = '$iEntryId'");
        return $iRet;
    }

    function leaveEventEntry ($iEntryId, $iProfileId)
    {
        $isConfirmed = $this->getOne ("SELECT `confirmed` FROM `" . $this->_sPrefix . $this->_sTableEventFans . "` WHERE `id_entry` = '$iEntryId' AND `id_profile` = '$iProfileId' LIMIT 1");
        $iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventFans . "` WHERE `id_entry` = '$iEntryId' AND `id_profile` = '$iProfileId'");
        if ($iRet && $isConfirmed)
            $this->query ("UPDATE `" . $this->_sPrefix . "events_main` SET `" . $this->_sFieldFansCount . "` = `" . $this->_sFieldFansCount . "` - 1 WHERE `id` = '$iEntryId'");
        return $iRet;
    }

    function isEventFan($iEntryId, $iProfileId, $isConfirmed)
    {
        $isConfirmed = $isConfirmed ? 1 : 0;
        return $this->getOne ("SELECT `when` FROM `" . $this->_sPrefix . $this->_sTableEventFans . "` WHERE `id_entry` = '$iEntryId' AND `id_profile` = '$iProfileId' AND `confirmed` = '$isConfirmed' LIMIT 1");
    }

    function getEventFansBrowse(&$aProfiles, $iEntryId, $iStart, $iMaxNum)
    {
        return $this->getEventFans($aProfiles, $iEntryId, true, $iStart, $iMaxNum);
    }

    function getEventFans(&$aProfiles, $iEntryId, $isConfirmed, $iStart, $iMaxNum, $aFilter = array())
    {
        $isConfirmed = $isConfirmed ? 1 : 0;
        $sFilter = '';
        if ($aFilter) {
            $s = implode (' OR `f`.`id_profile` = ', $aFilter);
            $sFilter = ' AND (`f`.`id_profile` = ' . $s . ') ';
        }
        $aProfiles = $this->getAll ("SELECT SQL_CALC_FOUND_ROWS `p`.* FROM `Profiles` AS `p` INNER JOIN `" . $this->_sPrefix . $this->_sTableEventFans . "` AS `f` ON (`f`.`id_entry` = '$iEntryId' AND `f`.`id_profile` = `p`.`ID` AND `f`.`confirmed` = $isConfirmed AND `p`.`Status` = 'Active' $sFilter) ORDER BY `f`.`when` DESC LIMIT $iStart, $iMaxNum");
        return $this->getOne("SELECT FOUND_ROWS()");
    }

    function confirmEventFans ($iEntryId, $aProfileIds)
    {
        if (!$aProfileIds)
            return false;
        $s = implode (' OR `id_profile` = ', $aProfileIds);
        $iRet = $this->query ("UPDATE `" . $this->_sPrefix . $this->_sTableEventFans . "` SET `confirmed` = 1 WHERE `id_entry` = '$iEntryId' AND `confirmed` = 0 AND (`id_profile` = $s)");
        if ($iRet)
            $this->query ("UPDATE `" . $this->_sPrefix . "events_main` SET `" . $this->_sFieldFansCount . "` = `" . $this->_sFieldFansCount . "` + $iRet WHERE `id` = '$iEntryId'");
        return $iRet;
    }

    function removeEventFans ($iEntryId, $aProfileIds)
    {
        if (!$aProfileIds)
            return false;
        $s = implode (' OR `id_profile` = ', $aProfileIds);
        $iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventFans . "` WHERE `id_entry` = '$iEntryId' AND `confirmed` = 1 AND (`id_profile` = $s)");
        if ($iRet)
            $this->query ("UPDATE `" . $this->_sPrefix . "events_main` SET `" . $this->_sFieldFansCount . "` = `" . $this->_sFieldFansCount . "` - $iRet WHERE `id` = '$iEntryId'");
 
        return $iRet;
    }

    function removeEventFanFromAllEntries ($iProfileId)
    {
        $iProfileId = (int)$iProfileId;
        if (!$iProfileId || !$this->_sTableEventFans)
            return false;

        // delete unconfirmed fans
        $iDeleted = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventFans . "` WHERE `confirmed` = 0 AND `id_profile` = " . $iProfileId);

        // delete confirmed fans
        $aEntries = $this->getColumn("SELECT DISTINCT `id_entry` FROM `" . $this->_sPrefix . $this->_sTableEventFans . "` WHERE `id_profile` = " . $iProfileId);
        foreach ($aEntries as $iEntryId) {
            $iDeleted += $this->leaveEventEntry ($iEntryId, $iProfileId) ? 1 : 0;
        }

        return $iDeleted;
    }

    function rejectEventFans ($iEntryId, $aProfileIds)
    {
        if (!$aProfileIds)
            return false;
        $s = implode (' OR `id_profile` = ', $aProfileIds);
        return $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventFans . "` WHERE `id_entry` = '$iEntryId' AND `confirmed` = 0 AND (`id_profile` = $s)");
    }
 
    function isSubProfileFan($sTable, $iSubEntryId, $iProfileId, $isConfirmed) {
        $isConfirmed = $isConfirmed ? 1 : 0;

        $iEntryId = (int)$this->getOne ("SELECT `school_id` FROM `" . $this->_sPrefix . $sTable . "` WHERE `id` = '$iSubEntryId' LIMIT 1");
 
        return $this->getOne ("SELECT `when` FROM `" . $this->_sPrefix . $this->_sTableFans . "` WHERE `id_entry` = '$iEntryId' AND `id_profile` = '$iProfileId' AND `confirmed` = '$isConfirmed' LIMIT 1");
    }

    function isAnyPublicContent() {
    
		if($iLoggedId = getLoggedId())
			return $this->getOne ("SELECT `{$this->_sFieldId}` FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `{$this->_sFieldStatus}` = 'approved' AND `{$this->_sFieldAllowViewTo}` = '" . BX_DOL_PG_ALL . "' OR `{$this->_sFieldAllowViewTo}` = '" . BX_DOL_PG_MEMBERS . "' LIMIT 1"); 
		else
			return $this->getOne ("SELECT `{$this->_sFieldId}` FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `{$this->_sFieldStatus}` = 'approved' AND `{$this->_sFieldAllowViewTo}` = '" . BX_DOL_PG_ALL . "' LIMIT 1");
    }

	function getBoonexEvents($iId){
		return $this->getAll("SELECT * FROM `bx_events_main` WHERE `school_id`='$iId'"); 
	}


}
