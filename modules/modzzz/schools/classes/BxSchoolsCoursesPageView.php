<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
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

class BxSchoolsCoursesPageView extends BxDolTwigPageView {	

    function __construct(&$oCoursesMain, &$aCourses) {
        parent::__construct('modzzz_schools_courses_view', $oCoursesMain, $aCourses);
	
        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableCoursesMediaPrefix; 
	}
   
    function getBlockCode_Info() {
        return array($this->_oTemplate->blockSubProfileInfo ('courses', $this->aDataEntry));
    }
 
	function getBlockCode_Desc() {
		$aData = $this->aDataEntry;

        $aVars = array (
            'overview' => $this->aDataEntry['overview'],

            'bx_if:credits' => array (
                'condition' => $aData['credits'],
                'content' => array (
                    'credits' => $aData['credits'],
                 ),
            ),
            'bx_if:course_code' => array (
                'condition' => $aData['course_code'],
                'content' => array (
                    'course_code' => $aData['course_code'],
                 ),
            ),
            'bx_if:semester' => array (
                'condition' => trim($aData['semester']),
                'content' => array (
                    'semester' => $this->_oTemplate->parseSemesters($aData['semester']),
                 ),
            ),
            'bx_if:instructors' => array (
                'condition' => trim($aData['instructors']),
                'content' => array (
                    'instructors' => $this->_oTemplate->parseInstructors($aData['instructors']),
                 ),
            ),
             'bx_if:overview' => array (
                'condition' => $aData['overview'],
                'content' => array (
                    'overview' => $aData['overview'],
                 ),
            ),
            'bx_if:objectives' => array (
                'condition' => $aData['objectives'],
                'content' => array (
                    'objectives' => $aData['objectives'],
                 ),
            ),
            'bx_if:prerequisite' => array (
                'condition' => $aData['prerequisite'],
                'content' => array (
                    'prerequisite' => $aData['prerequisite'],
                 ),
            ),
            'bx_if:timetable' => array (
                'condition' => $aData['timetable'],
                'content' => array (
                    'timetable' => $aData['timetable'],
                 ),
            ),
            'bx_if:delivery_methods' => array (
                'condition' => $aData['delivery_methods'],
                'content' => array (
                    'delivery_methods' => $aData['delivery_methods'],
                 ),
            ),
            'bx_if:content' => array (
                'condition' => $aData['content'],
                'content' => array (
                    'content' => $aData['content'],
                 ),
            ),
            'bx_if:assessment' => array (
                'condition' => $aData['assessment'],
                'content' => array (
                    'assessment' => $aData['assessment'],
                 ),
            ),
            'bx_if:materials' => array (
                'condition' => $aData['materials'],
                'content' => array (
                    'materials' => $aData['materials'],
                 ),
            ),				
            'bx_if:specialization' => array (
                'condition' => $aData['specialization'],
                'content' => array (
                    'specialization' => $aData['specialization'],
                 ),
            ), 
        );

        return array($this->_oTemplate->parseHtmlByName('block_course_description', $aVars)); 
    }
 
	function getBlockCode_Photos() {
 
		if($this->aDataEntry['icon'] && (!$this->aDataEntry['thumb']))
			return $this->getPhoto(); 
		else
			return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);  
    }    
  
	function getPhoto() {
 
        $sImage = $this->_oMain->_oDb->getIcon($this->aDataEntry['icon']);
   
        $aVars = array (
            'image_url' => $sImage,
        );
        return $this->_oTemplate->parseHtmlByName('block_photo', $aVars);
    }
 
    function getBlockCode_Rate() {
        modzzz_schools_import('CoursesVoting');
        $o = new BxSchoolsCoursesVoting ('modzzz_schools_courses', (int)$this->aDataEntry['id']);
    	if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRateSubProfile($this->_oDb->_sTableCourses, $this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_schools_import('CoursesCmts');
        $o = new BxSchoolsCoursesCmts ('modzzz_schools_courses', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
			$aCoursesEntry = $this->_oDb->getCoursesEntryById($this->aDataEntry['id']);
			$iEntryId = $aCoursesEntry['school_id'];
	
		    $aDataEntry = $this->_oDb->getEntryById($iEntryId);
   
            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                 'TitleEdit' => $this->_oMain->isAllowedEdit($aDataEntry) ? _t('_modzzz_schools_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($aDataEntry) ? _t('_modzzz_schools_action_title_delete') : '',
				'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotosSubProfile($this->_oDb->_sTableCourses,$this->aDataEntry) ? _t('_modzzz_schools_action_upload_photos') : '', 


            );
 
            if (!$this->aInfo['TitleEdit'] && !$this->aInfo['TitleDelete'] && !$this->aInfo['TitleUploadPhotos'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'modzzz_schools_courses');
        } 

        return '';
    }    
  
    function getCode() { 
        return parent::getCode();
    }    
}
