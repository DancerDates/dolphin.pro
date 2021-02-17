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

class BxSchoolsStudentPageView extends BxDolTwigPageView {	

    function __construct(&$oStudentMain, &$aStudent) {
        parent::__construct('modzzz_schools_student_view', $oStudentMain, $aStudent);
	
        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableStudentMediaPrefix; 
	}
   
    function getBlockCode_Info() {
        return array($this->_oTemplate->blockSubProfileInfo ('student', $this->aDataEntry));
    }

	function getBlockCode_Desc() {
 
		if($this->aDataEntry['use_profile_desc'] && $this->aDataEntry['profile_id']){
			$aProfile = getProfileInfo($this->aDataEntry['profile_id']);
			$sDesc = $aProfile['DescriptionMe'];
		}elseif(trim($this->aDataEntry['desc'])){
			$sDesc = trim($this->aDataEntry['desc']);
		}else{
			$sDesc = _t('_modzzz_schools_msg_desc_none'); 
		}

        $aVars = array (
 
            'bx_if:sitemember' => array (
                'condition' => $this->aDataEntry['profile_id'],
                'content' => array (
                    'profile_nick' => getNickName($this->aDataEntry['profile_id']),
                    'profile_link' => $this->aDataEntry['profile_id'] ? getProfileLink($this->aDataEntry['profile_id']) : 'javascript:void(0);',
                ),
            ),  
            'title' => $this->aDataEntry['title'],
            'description' => $sDesc,
			'year_entered' => $this->aDataEntry['year_entered'],  
			'year_left' => ($this->aDataEntry['year_left']) ? $this->aDataEntry['year_left'] : _t('_modzzz_schools_present'),    

        );

        return array($this->_oTemplate->parseHtmlByName('block_student_description', $aVars)); 
    }
 
	function getBlockCode_Photos() {
 
		if($this->aDataEntry['use_profile_photo'] && $this->aDataEntry['profile_id']){
			return BxDolService::call('photos', 'profile_photo_block', array(array('PID' => $this->aDataEntry['profile_id'])), 'Search');
		}else{
			return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);  
		}
    }    
   
    function getBlockCode_Rate() {
        modzzz_schools_import('StudentVoting');
        $o = new BxSchoolsStudentVoting ('modzzz_schools_student', (int)$this->aDataEntry['id']);
    	if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRateSubProfile($this->_oDb->_sTableStudent, $this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_schools_import('StudentCmts');
        $o = new BxSchoolsStudentCmts ('modzzz_schools_student', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
			$aStudentEntry = $this->_oDb->getStudentEntryById($this->aDataEntry['id']);
			$iEntryId = $aStudentEntry['school_id'];
	
		    $aDataEntry = $this->_oDb->getEntryById($iEntryId);
   
            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                 'TitleEdit' => $this->_oMain->isAllowedEdit($aDataEntry) ? _t('_modzzz_schools_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($aDataEntry) ? _t('_modzzz_schools_action_title_delete') : '',
				'TitleUploadPhotos' => (!$this->aDataEntry['use_profile_photo']) && $this->_oMain->isAllowedUploadPhotosSubProfile($this->_oDb->_sTableStudent,$this->aDataEntry) ? _t('_modzzz_schools_action_upload_photos') : '', 


            );
 
            if (!$this->aInfo['TitleEdit'] && !$this->aInfo['TitleDelete'] && !$this->aInfo['TitleUploadPhotos'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'modzzz_schools_student');
        } 

        return '';
    }    
  
    function getCode() { 
        return parent::getCode();
    }    
}
