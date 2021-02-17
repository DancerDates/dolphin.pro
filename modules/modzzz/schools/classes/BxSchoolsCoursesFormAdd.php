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

bx_import ('BxDolProfileFields');
bx_import ('BxDolFormMedia');

class BxSchoolsCoursesFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function __construct ($oMain, $iProfileId, $iSchoolId = 0, $iEntryId = 0, $iThumb = 0) { 
        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb; 
		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableCoursesMediaPrefix;
 
		$aInstructors = $this->_oDb->getFormInstructors(); 
		$aInstructors[''] = _t('_Select');
		asort($aInstructors);

		$aSemester = $this->_oDb->getSemesters();
		$aSemester[''] = _t('_Select');
		asort($aSemester);

		bx_import ('SubPrivacy', $this->_oMain->_aModule);
		$GLOBALS['oBxSchoolsModule']->_oSubPrivacy = new BxSchoolsSubPrivacy($this->_oMain, $this->_oDb->_sTableCourses); 

        $this->_aMedia = array ();

        if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            $this->_aMedia['images'] = array (
                'post' => 'ready_images',
                'upload_func' => 'uploadPhotos',
                'tag' => BX_SCHOOLS_PHOTOS_TAG,
                'cat' => BX_SCHOOLS_PHOTOS_CAT,
                'thumb' => 'thumb',
                'module' => 'photos',
                'title_upload_post' => 'images_titles',
                'title_upload' => _t('_modzzz_schools_form_caption_file_title'),
                'service_method' => 'get_photo_array',
            );
  
        // privacy

        $aInputPrivacyCustom = array ();
        $aInputPrivacyCustom[] = array ('key' => '', 'value' => '----');
        $aInputPrivacyCustom[] = array ('key' => 'f', 'value' => _t('_modzzz_schools_privacy_fans_only'));
        $aInputPrivacyCustomPass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9f]+)$/'),
        );
 

        $aInputPrivacyCustom2 = array (
            array('key' => 'f', 'value' => _t('_modzzz_schools_privacy_fans')),
            array('key' => 'a', 'value' => _t('_modzzz_schools_privacy_admins_only'))
        );
        $aInputPrivacyCustom2Pass = array (
            'pass' => 'Preg', 
            'params' => array('/^([fa]+)$/'),
        );
   
        $aInputPrivacyView = $GLOBALS['oBxSchoolsModule']->_oSubPrivacy->getGroupChooser($iProfileId, 'schools', 'view');
        $aInputPrivacyView['values'] = array_merge($aInputPrivacyView['values'], $aInputPrivacyCustom);
        $aInputPrivacyView['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyComment = $GLOBALS['oBxSchoolsModule']->_oSubPrivacy->getGroupChooser($iProfileId, 'schools', 'comment');
        $aInputPrivacyComment['values'] = array_merge($aInputPrivacyComment['values'], $aInputPrivacyCustom);
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $GLOBALS['oBxSchoolsModule']->_oSubPrivacy->getGroupChooser($iProfileId, 'schools', 'rate');
        $aInputPrivacyRate['values'] = array_merge($aInputPrivacyRate['values'], $aInputPrivacyCustom);
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;
 
        $aInputPrivacyUploadPhotos = $this->_oMain->_oSubPrivacy->getGroupChooser($iProfileId, 'schools', 'upload_photos');
        $aInputPrivacyUploadPhotos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadPhotos['db'] = $aInputPrivacyCustom2Pass;

	    // generate templates for form custom elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($this->_oMain->_iProfileId, $iEntryId, $iThumb);
  
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_courses',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_schools_courses_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_subprofile_form_header_info')
                ),                
                'school_id' => array(
                    'type' => 'hidden',
                    'name' => 'school_id', 
                    'value' => $iSchoolId,
                    'db' => array (
                        'pass' => 'Int' 
                    ) 
                 ), 
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_schools_form_caption_course_name'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_modzzz_schools_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ), 
                ), 
 				'instructors' => array(
					'type' => 'select_box',
					'name' => 'instructors',
					'values'=> $aInstructors,
					'caption' => _t('_modzzz_schools_form_caption_instructors'),  
					'db' => array (
						'pass' => 'Categories', 
 					),  
					'display' => 'parseInstructors',  
 				), 
                'course_code' => array(
                    'type' => 'text',
                    'name' => 'course_code',
                    'caption' => _t('_modzzz_schools_form_caption_course_code'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ), 
                ),
                'credits' => array(
                    'type' => 'text',
                    'name' => 'credits',
                    'caption' => _t('_modzzz_schools_form_caption_credits'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ), 
                ), 
 				'semester' => array(
					'type' => 'select_box',
					'name' => 'semester',
					'values'=> $aSemester,
					'caption' => _t('_modzzz_schools_form_caption_semester'),  
					'db' => array (
						'pass' => 'Categories', 
 					),  
  				),  
                'overview' => array(
                    'type' => 'textarea',
                    'name' => 'overview',
                    'caption' => _t('_modzzz_schools_form_caption_overview'),
                    'required' => true,
                    'html' => 2,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(20,64000),
                        'error' => _t ('_modzzz_schools_form_err_overview'),
                    ),                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
                'objectives' => array(
                    'type' => 'textarea',
                    'name' => 'objectives',
                    'caption' => _t('_modzzz_schools_form_caption_objectives'), 
                    'required' => false,
					'html' => 2, 
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
                'prerequisite' => array(
                    'type' => 'textarea',
                    'name' => 'prerequisite',
                    'caption' => _t('_modzzz_schools_form_caption_prerequisite'),
                    'required' => false,
                    'html' => 2, 
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
                'timetable' => array(
                    'type' => 'textarea',
                    'name' => 'timetable',
                    'caption' => _t('_modzzz_schools_form_caption_timetable'),
                    'required' => false,
                    'html' => 2, 
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
                'delivery_methods' => array(
                    'type' => 'textarea',
                    'name' => 'delivery_methods',
                    'caption' => _t('_modzzz_schools_form_caption_delivery_methods'),
                    'required' => false,
                    'html' => 2, 
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
                'content' => array(
                    'type' => 'textarea',
                    'name' => 'content',
                    'caption' => _t('_modzzz_schools_form_caption_content'),
                    'required' => false,
                    'html' => 2, 
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
                'assessment' => array(
                    'type' => 'textarea',
                    'name' => 'assessment',
                    'caption' => _t('_modzzz_schools_form_caption_assessment'),
                    'required' => false,
                    'html' => 2, 
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
                'materials' => array(
                    'type' => 'textarea',
                    'name' => 'materials',
                    'caption' => _t('_modzzz_schools_form_caption_materials'),
                    'required' => false,
                    'html' => 2, 
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),  
                'specialization' => array(
                    'type' => 'textarea',
                    'name' => 'specialization',
                    'caption' => _t('_modzzz_schools_form_caption_specialization'), 
                    'required' => false, 
					'html' => 2, 
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
  

                // images

                'header_images' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_form_header_images'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'thumb' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['thumb_choice'],
                    'name' => 'thumb',
                    'caption' => _t('_modzzz_schools_form_caption_thumb_choice'),
                    'info' => _t('_modzzz_schools_form_info_thumb_choice'),
                    'required' => false,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ),                
                'images_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['choice'],
                    'name' => 'images_choice[]',
                    'caption' => _t('_modzzz_schools_form_caption_images_choice'),
                    'info' => _t('_modzzz_schools_form_info_images_choice'),
                    'required' => false,
                ),
                'images_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['upload'],
                    'name' => 'images_upload[]',
                    'caption' => _t('_modzzz_schools_form_caption_images_upload'),
                    'info' => _t('_modzzz_schools_form_info_images_upload'),
                    'required' => false,
                ),
 
                // privacy
                
                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_form_header_privacy'),
                ),

                'allow_view_to' => $aInputPrivacyView,
 
                'allow_comment_to' => $aInputPrivacyComment,

                'allow_rate_to' => $aInputPrivacyRate, 
   
                'allow_upload_photos_to' => $aInputPrivacyUploadPhotos, 
  
				'Submit' => array (
					'type' => 'submit',
					'name' => 'submit_form',
					'value' => _t('_Submit'),
					'colspan' => false,
				),  

            ),            
        );
  
		if (!$aCustomForm['inputs']['images_choice']['content']) {
			unset ($aCustomForm['inputs']['thumb']);
			unset ($aCustomForm['inputs']['images_choice']);
		}

		//[begin] added 7.1
	   if (!isset($this->_aMedia['images'])) {
			unset ($aCustomForm['inputs']['header_images']);
			unset ($aCustomForm['inputs']['thumb']);
			unset ($aCustomForm['inputs']['images_choice']);
			unset ($aCustomForm['inputs']['images_upload']);
			unset ($aCustomForm['inputs']['allow_upload_photos_to']);
		}


		$this->processMembershipChecksForMediaUploads ($aCustomForm['inputs']);
 

        parent::__construct ($aCustomForm);
    }

    /**
     * @access private
     */ 
    function _getFilesInEntry ($sModuleName, $sServiceMethod, $sName, $sMediaType, $iIdProfile, $iEntryId)
    {             

        $aReadyMedia = array ();
        if ($iEntryId)
            $aReadyMedia = $this->_oDb->getMediaIds($iEntryId, $sMediaType);
        
        if (!$aReadyMedia)
            return array();

        $aDataEntry = $this->_oDb->getCoursesEntryById($iEntryId);

        $aFiles = array ();
        foreach ($aReadyMedia as $iMediaId)
        {
            switch ($sModuleName) {
            case 'photos':
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId, 'icon'), 'Search');
                break; 
            default:
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId), 'Search');
            }
    
            if (!$this->_oMain->isEntryAdmin($aDataEntry, $iIdProfile) && $aRow['owner'] != $iIdProfile)
                continue;

            $aFiles[] = array (
                'name' => $sName,
                'id' => $iMediaId,
                'title' => $aRow['title'],
                'icon' => $aRow['file'],
                'owner' => $aRow['owner'],
                'checked' => 'checked',
            );
        }
        return $aFiles;
    }        

    /**
     * process media upload updates
     * call it after successful call $form->insert/update functions 
     * @param $iEntryId associated entry id
     * @return nothing
     */ 
    function processMedia ($iEntryId, $iProfileId) { 

        $aDataEntry = $this->_oDb->getCoursesEntryById($iEntryId);
		
		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableCoursesMediaPrefix;

        foreach ($this->_aMedia as $sName => $a) {
 
            $aFiles = $this->_getFilesInEntry ($a['module'], $a['service_method'], $a['post'], $sName, (int)$iProfileId, $iEntryId);
            foreach ($aFiles as $aRow)
                $aFiles2Delete[$aRow['id']] = $aRow['id'];

            if (is_array($_REQUEST[$a['post']]) && $_REQUEST[$a['post']] && $_REQUEST[$a['post']][0]) {
                $this->updateMedia ($iEntryId, $_REQUEST[$a['post']], $aFiles2Delete, $sName);
            } else {
                $this->deleteMedia ($iEntryId, $aFiles2Delete, $sName);
            }

            $sUploadFunc = $a['upload_func'];
            if ($aMedia = $this->$sUploadFunc($a['tag'], $a['cat'])) {
                $this->_oDb->insertMedia ($iEntryId, $aMedia, $sName);
                if ($a['thumb'] && !$aDataEntry[$a['thumb']] && !$_REQUEST[$a['thumb']]) 
                    $this->_oDb->setThumbnail ($iEntryId, 0);
            }

            $aMediaIds = $this->_oDb->getMediaIds($iEntryId, $sName);

            if ($a['thumb']) { // set thumbnail to another one if current thumbnail is deleted                
                $sThumbFieldName = $a['thumb'];
                if ($aDataEntry[$sThumbFieldName] && !isset($aMediaIds[$aDataEntry[$sThumbFieldName]])) {
                    $this->_oDb->setThumbnail ($iEntryId, 0);
                } 
            }

            // process all deleted media - delete actual file
            $aDeletedMedia = array_diff ($aFiles2Delete, $aMediaIds);
            if ($aDeletedMedia) {
                foreach ($aDeletedMedia as $iMediaId) {
                    if (!$this->_oDb->isMediaInUse($iMediaId, $sName))
                        BxDolService::call($a['module'], 'remove_object', array($iMediaId));
                }
            }
        }

    }

 

}
