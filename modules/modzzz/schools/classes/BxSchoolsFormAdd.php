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

bx_import ('BxDolProfileFields');
bx_import ('BxDolFormMedia');

class BxSchoolsFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function __construct ($oMain, $iProfileId, $iEntryId = 0, $iThumb = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
        $this->_oTemplate = $oMain->_oTemplate;
	  
		if($iEntryId){
			$aDataEntry = $this->_oDb->getEntryById($iEntryId);
			  
 			$iLocationId = ($_REQUEST['location_id']) ? $_REQUEST['location_id']  : $aDataEntry['location_id'];

			$sSelState =  ($_POST['state']) ? $_POST['state'] : $aDataEntry['state']; 
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : $aDataEntry['country'];  
			$aStates = $this->_oDb->getStateArray($sSelCountry);  
		}else {
			$iLocationId = ($_REQUEST['location_id']) ? $_REQUEST['location_id'] : $_REQUEST['location'];
			$iLocationId = ($iLocationId) ? $iLocationId : $aDataEntry['location_id'];
  
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : ''; 
			$sSelState = ($_POST['state']) ? $_POST['state'] : ''; 
			$aStates = $this->_oDb->getStateArray($sSelCountry);  
		}
 
		//[begin] location integration - modzzz
		if($iLocationId) { 
			$oLocation = BxDolModule::getInstance('BxLocationModule'); 
			$aLocationEntry = $oLocation->_oDb->getEntryById($iLocationId);
			$sLocationName = $aLocationEntry[$oLocation->_oDb->_sFieldTitle];  
		}
		//[end] location integration - modzzz

		$sStateUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home/?ajax=state&country=' ; 
 
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

        if (BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            $this->_aMedia['videos'] = array (
                'post' => 'ready_videos',
                'upload_func' => 'uploadVideos',
                'tag' => BX_SCHOOLS_VIDEOS_TAG,
                'cat' => BX_SCHOOLS_VIDEOS_CAT,
                'thumb' => false,
                'module' => 'videos',
                'title_upload_post' => 'videos_titles',
                'title_upload' => _t('_modzzz_schools_form_caption_file_title'),
                'service_method' => 'get_video_array',
            );

        if (BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            $this->_aMedia['sounds'] = array (
                'post' => 'ready_sounds',
                'upload_func' => 'uploadSounds',
                'tag' => BX_SCHOOLS_SOUNDS_TAG,
                'cat' => BX_SCHOOLS_SOUNDS_CAT,
                'thumb' => false,
                'module' => 'sounds',
                'title_upload_post' => 'sounds_titles',
                'title_upload' => _t('_modzzz_schools_form_caption_file_title'),
                'service_method' => 'get_music_array',
            );

        if (BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            $this->_aMedia['files'] = array (
                'post' => 'ready_files',
                'upload_func' => 'uploadFiles',
                'tag' => BX_SCHOOLS_FILES_TAG,
                'cat' => BX_SCHOOLS_FILES_CAT,
                'thumb' => false,
                'module' => 'files',
                'title_upload_post' => 'files_titles',
                'title_upload' => _t('_modzzz_schools_form_caption_file_title'),
                'service_method' => 'get_file_array',
            );
 

        bx_import('BxDolCategories');
        $oCategories = new BxDolCategories();        

        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
        $aCountries = array_merge (array('' => _t('_modzzz_schools_all_countries')), $aCountries);

        $aEthnicity = $oProfileFields->convertValues4Input('#!Ethnicity');
        $aEthnicity = array(''=>_t('_Select')) + $aEthnicity;

        $aSchoolType = $oProfileFields->convertValues4Input('#!SchoolType');
        //asort($aSchoolType);
 
        $aSchoolLevel = $oProfileFields->convertValues4Input('#!SchoolLevel');
        //asort($aSchoolLevel);

        $aSchoolQualifications = $oProfileFields->convertValues4Input('#!SchoolQualifications');
		//asort($aSchoolQualifications);

        $aSchoolClubs = $oProfileFields->convertValues4Input('#!SchoolClubs');
		//asort($aSchoolClubs);

        $aSchoolSports = $oProfileFields->convertValues4Input('#!SchoolSports');
        //asort($aSchoolSports);
 
        // generate templates for custom form's elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($oMain->_iProfileId, $iEntryId, $iThumb);

        $aCustomYoutubeTemplates = $this->generateCustomYoutubeTemplate ($oMain->_iProfileId, $iEntryId);


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

        $aInputPrivacyViewFans = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'view_fans');
        $aInputPrivacyViewFans['values'] = array_merge($aInputPrivacyViewFans['values'], $aInputPrivacyCustom);

        $aInputPrivacyComment = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'comment');
        $aInputPrivacyComment['values'] = array_merge($aInputPrivacyComment['values'], $aInputPrivacyCustom);
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'rate');
        $aInputPrivacyRate['values'] = array_merge($aInputPrivacyRate['values'], $aInputPrivacyCustom);
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyForum = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'post_in_forum');
        $aInputPrivacyForum['values'] = array_merge($aInputPrivacyForum['values'], $aInputPrivacyCustom);
        $aInputPrivacyForum['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyUploadPhotos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'upload_photos');
        $aInputPrivacyUploadPhotos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadPhotos['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadVideos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'upload_videos');
        $aInputPrivacyUploadVideos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadVideos['db'] = $aInputPrivacyCustom2Pass;        

        $aInputPrivacyUploadSounds = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'upload_sounds');
        $aInputPrivacyUploadSounds['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadSounds['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadFiles = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'upload_files');
        $aInputPrivacyUploadFiles['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadFiles['db'] = $aInputPrivacyCustom2Pass;

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_schools',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_schools_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_form_header_info')
                ),  
					
				//[begin] location integration - modzzz 
				'location_id' => array(
					'type' => 'hidden',
					'name' => 'location_id',
					'value' => $iLocationId,   
					'db' => array (
						'pass' => 'Xss', 
					) 
				),  
				'location_name' => array(
					'name' => 'location_name',
					'type' => 'custom', 
					'caption' => _t('_modzzz_schools_caption_location_for_school'),
					'content' => $sLocationName,    
				),
				//[end] location integration - modzzz

                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_schools_form_caption_title'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_modzzz_schools_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),                
                'desc' => array(
                    'type' => 'textarea',
                    'name' => 'desc',
                    'caption' => _t('_modzzz_schools_form_caption_desc'),
                    'required' => true,
                    'html' => 2,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(20,64000),
                        'error' => _t ('_modzzz_schools_form_err_desc'),
                    ),                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
                'tags' => array(
                    'type' => 'text',
                    'name' => 'tags',
                    'caption' => _t('_Tags'),
                    'info' => _t('_sys_tags_note'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_schools_form_err_tags'),
                    ),
                    'db' => array (
                        'pass' => 'Tags', 
                    ),
                ),     
                'categories' => $oCategories->getGroupChooser ('modzzz_schools', (int)$iProfileId, true), 
 
                'header_campus' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_form_header_main_campus')
                ),    
                'country' => array(
                    'type' => 'select',
                    'name' => 'country',
					'listname' => 'Country',
                    'caption' => _t('_modzzz_schools_form_caption_country'),
                    'values' => $aCountries,
 					'value' => $sSelCountry,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					),	
                    'required' => true, 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,50),
                        'error' => _t ('_modzzz_schools_form_err_country'),
                    ),  
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{2})/'),
                    ),
					'display' => 'parsePreValues', 
                ),
 				'state' => array(
					'type' => 'select',
					'name' => 'state',
					'value' => $sSelState,  
					'values'=> $aStates,
					'caption' => _t('_modzzz_schools_caption_state'),
					'attrs' => array(
						'id' => 'substate',
					), 
				    'db' => array (
					'pass' => 'Preg', 
					'params' => array('/([a-zA-Z]+)/'),
					), 
					'display' => 'getStateName',
				), 
                'city' => array(
                    'type' => 'text',
                    'name' => 'city',
                    'caption' => _t('_modzzz_schools_form_caption_city'),
                    'required' => true,
                     'checker' => array (
                        'func' => 'length',
                        'params' => array(2,50),
                        'error' => _t ('_modzzz_schools_form_err_city'),
                    ), 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'street' => array(
                    'type' => 'text',
                    'name' => 'street',
                    'caption' => _t('_modzzz_schools_form_caption_street'),
                    'required' => false,
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'zip' => array(
                    'type' => 'text',
                    'name' => 'zip',
                    'caption' => _t('_modzzz_schools_form_caption_zip'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),                                
 
                // additional details 
                'header_details' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_form_header_details'),
                    'collapsable' => true,
                    'collapsed' => false,
                ), 
                'motto' => array(
                    'type' => 'text',
                    'name' => 'motto',
                    'caption' => _t('_modzzz_schools_form_caption_motto'), 
                     'required' => false, 
                    'db' => array (
                        'pass' => 'Xss',  
                    ),
                    'display' => true,
                ),
                'mascot' => array(
					'type' => 'text',
                    'name' => 'mascot',
                    'caption' => _t('_modzzz_schools_form_caption_mascot'), 
                     'required' => false, 
                    'db' => array (
                        'pass' => 'Xss',  
                    ),
                    'display' => true,
                ),
                'nickname' => array(
                    'type' => 'text',
                    'name' => 'nickname',
                    'caption' => _t('_modzzz_schools_form_caption_nickname'), 
                     'required' => false, 
                    'db' => array (
                        'pass' => 'Xss',  
                    ),
                    'display' => true,
                ),
                'colors' => array(
                    'type' => 'text',
                    'name' => 'colors',
                    'caption' => _t('_modzzz_schools_form_caption_colors'), 
                     'required' => false, 
                    'db' => array (
                        'pass' => 'Xss',  
                    ),
                    'display' => true,
                ), 
                'affiliations' => array(
                    'type' => 'text',
                    'name' => 'affiliations',
                    'caption' => _t('_modzzz_schools_form_caption_affiliations'), 
                     'required' => false, 
                    'db' => array (
                        'pass' => 'Xss',  
                    ),
                    'display' => true,
                ),  
 				'school_type' => array(
					'type' => 'select',
					'name' => 'school_type',
					'values'=> $aSchoolType,
					'caption' => _t('_modzzz_schools_caption_type'), 
				  'db' => array (
						'pass' => 'Preg', 
                        'params' => array('/([0-9]+)/'),
					), 
					'display' => 'parsePreValues',  
					'listname' => 'SchoolType',    
				),
 				'school_level' => array(
					'type' => 'select',
					'name' => 'school_level',
					'values'=> $aSchoolLevel,
					'caption' => _t('_modzzz_schools_caption_level'), 
				  'db' => array (
						'pass' => 'Preg', 
                        'params' => array('/([0-9]+)/'),
					), 
					'display' => 'parsePreValues',  
					'listname' => 'SchoolLevel',    
				),
 				'school_qualifications' => array(
					'type' => 'select_box',
					'name' => 'school_qualifications',
					'values'=> $aSchoolQualifications,
					'caption' => _t('_modzzz_schools_caption_qualifications'),  
					'db' => array (
						'pass' => 'Categories', 
 					),  
					'display' => 'parseMultiPreValues',  
					'listname' => 'SchoolQualifications',   
				), 
 				'school_sports' => array(
					'type' => 'select_box',
					'name' => 'school_sports',
					'values'=> $aSchoolSports,
					'caption' => _t('_modzzz_schools_caption_sports'), 
				  'db' => array (
						'pass' => 'Categories', 
 					), 
					'display' => 'parseMultiPreValues',  
					'listname' => 'SchoolSports',   
				
				),
 				'school_clubs' => array(
					'type' => 'select_box',
					'name' => 'school_clubs',
					'values'=> $aSchoolClubs,
					'caption' => _t('_modzzz_schools_caption_clubs'), 
				  'db' => array (
						'pass' => 'Categories', 
 					), 
					'display' => 'parseMultiPreValues',  
					'listname' => 'SchoolClubs',   

				),

                // stats 
                'header_stats' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_form_header_stats'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'enrolled_students' => array(
                    'type' => 'text',
                    'name' => 'enrolled_students',
                    'caption' => _t('_modzzz_schools_form_caption_enrolled_students'), 
                     'required' => false, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([0-9,]+)/'),
                    ),
                    'display' => true,
                ),
                'academic_staff_count' => array(
                    'type' => 'text',
                    'name' => 'academic_staff_count',
                    'caption' => _t('_modzzz_schools_form_caption_academic_staff_count'), 
                     'required' => false, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([0-9,]+)/'),
                    ),
                    'display' => true,
                ),
                'admin_staff_count' => array(
                    'type' => 'text',
                    'name' => 'admin_staff_count',
                    'caption' => _t('_modzzz_schools_form_caption_admin_staff_count'), 
                     'required' => false, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([0-9,]+)/'),
                    ),
                    'display' => true,
                ),  
                'year_established' => array(
                    'type' => 'text',
                    'name' => 'year_established',
                    'caption' => _t('_modzzz_schools_form_caption_year_established'), 
                     'required' => false, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([0-9]+)/'),
                    ),
                    'display' => true,
                ), 
                'founder' => array(
                    'type' => 'text',
                    'name' => 'founder',
                    'caption' => _t('_modzzz_schools_form_caption_founder'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  
                'president' => array(
                    'type' => 'text',
                    'name' => 'president',
                    'caption' => _t('_modzzz_schools_form_caption_president'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
 				'ethnicity' => array(
					'type' => 'select',
					'name' => 'ethnicity',
					'values'=> $aEthnicity,
					'caption' => _t('_modzzz_schools_caption_ethnicity'), 
				  'db' => array (
						'pass' => 'Preg', 
                        'params' => array('/([0-9]+)/'),
					),
					'display' => 'parsePreValues', 
					'listname' => 'Ethnicity',    
				),

                // contact

                'header_contact' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_form_header_contact_info'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'phone' => array(
                    'type' => 'text',
                    'name' => 'phone',
                    'caption' => _t('_modzzz_schools_form_caption_phone'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'fax' => array(
                    'type' => 'text',
                    'name' => 'fax',
                    'caption' => _t('_modzzz_schools_form_caption_fax'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'website' => array(
                    'type' => 'text',
                    'name' => 'website',
                    'caption' => _t('_modzzz_schools_form_caption_website'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => 'parseLink',
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
 
                // youtube videos
               'header_youtube' => array(
                   'type' => 'block_header',
                   'caption' => _t('_modzzz_schools_form_header_youtube'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
               'youtube_choice' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['choice'],
                   'name' => 'youtube_choice[]',
                   'caption' => _t('_modzzz_schools_form_caption_youtube_choice'),
                   'info' => _t('_modzzz_schools_form_info_youtube_choice'),
                   'required' => false,
               ), 
               'youtube_attach' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['upload'],
                   'name' => 'youtube_upload[]',
                   'caption' => _t('_modzzz_schools_form_caption_youtube_attach'),
                   'info' => _t('_modzzz_schools_form_info_youtube_attach'),
                   'required' => false,
               ),
 
                // videos

                'header_videos' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_form_header_videos'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'videos_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['choice'],
                    'name' => 'videos_choice[]',
                    'caption' => _t('_modzzz_schools_form_caption_videos_choice'),
                    'info' => _t('_modzzz_schools_form_info_videos_choice'),
                    'required' => false,
                ),
                'videos_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['upload'],
                    'name' => 'videos_upload[]',
                    'caption' => _t('_modzzz_schools_form_caption_videos_upload'),
                    'info' => _t('_modzzz_schools_form_info_videos_upload'),
                    'required' => false,
                ),

                // sounds

                'header_sounds' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_form_header_sounds'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'sounds_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['choice'],
                    'name' => 'sounds_choice[]',
                    'caption' => _t('_modzzz_schools_form_caption_sounds_choice'),
                    'info' => _t('_modzzz_schools_form_info_sounds_choice'),
                    'required' => false,
                ),
                'sounds_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['upload'],
                    'name' => 'sounds_upload[]',
                    'caption' => _t('_modzzz_schools_form_caption_sounds_upload'),
                    'info' => _t('_modzzz_schools_form_info_sounds_upload'),
                    'required' => false,
                ),

                // files

                'header_files' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_form_header_files'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'files_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['choice'],
                    'name' => 'files_choice[]',
                    'caption' => _t('_modzzz_schools_form_caption_files_choice'),
                    'info' => _t('_modzzz_schools_form_info_files_choice'),
                    'required' => false,
                ),
                'files_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['upload'],
                    'name' => 'files_upload[]',
                    'caption' => _t('_modzzz_schools_form_caption_files_upload'),
                    'info' => _t('_modzzz_schools_form_info_files_upload'),
                    'required' => false,
                ),

                // privacy
                
                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_form_header_privacy'),
                ),

                'allow_view_school_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'view_school'),

                'allow_view_fans_to' => $aInputPrivacyViewFans,

                'allow_comment_to' => $aInputPrivacyComment,

                'allow_rate_to' => $aInputPrivacyRate, 

                'allow_post_in_forum_to' => $aInputPrivacyForum, 

                'allow_join_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'join'),

                'join_confirmation' => array (
                    'type' => 'select',
                    'name' => 'join_confirmation',
                    'caption' => _t('_modzzz_schools_form_caption_join_confirmation'),
                    'info' => _t('_modzzz_schools_form_info_join_confirmation'),
                    'values' => array(
                        0 => _t('_modzzz_schools_form_join_confirmation_disabled'),
                        1 => _t('_modzzz_schools_form_join_confirmation_enabled'),
                    ),
                    'checker' => array (
                        'func' => 'int',
                        'error' => _t ('_modzzz_schools_form_err_join_confirmation'),
                    ),                                        
                    'db' => array (
                        'pass' => 'Int', 
                    ),                    
                ),

                'allow_upload_photos_to' => $aInputPrivacyUploadPhotos, 

                'allow_upload_videos_to' => $aInputPrivacyUploadVideos, 

                'allow_upload_sounds_to' => $aInputPrivacyUploadSounds, 

                'allow_upload_files_to' => $aInputPrivacyUploadFiles, 

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

        if (!$aCustomForm['inputs']['videos_choice']['content'])
            unset ($aCustomForm['inputs']['videos_choice']);

        if (!$aCustomForm['inputs']['sounds_choice']['content'])
            unset ($aCustomForm['inputs']['sounds_choice']);

        if (!$aCustomForm['inputs']['files_choice']['content'])
            unset ($aCustomForm['inputs']['files_choice']);

		//[begin] added 7.1
       if (!isset($this->_aMedia['images'])) {
            unset ($aCustomForm['inputs']['header_images']);
            unset ($aCustomForm['inputs']['thumb']);
            unset ($aCustomForm['inputs']['images_choice']);
            unset ($aCustomForm['inputs']['images_upload']);
            unset ($aCustomForm['inputs']['allow_upload_photos_to']);
        }

        if (!isset($this->_aMedia['videos'])) {
            unset ($aCustomForm['inputs']['header_videos']);
            unset ($aCustomForm['inputs']['videos_choice']);
            unset ($aCustomForm['inputs']['videos_upload']);
            unset ($aCustomForm['inputs']['allow_upload_videos_to']);
        }

        if (!isset($this->_aMedia['sounds'])) {
            unset ($aCustomForm['inputs']['header_sounds']);
            unset ($aCustomForm['inputs']['sounds_choice']);
            unset ($aCustomForm['inputs']['sounds_upload']);
            unset ($aCustomForm['inputs']['allow_upload_sounds_to']);
        }

        if (!isset($this->_aMedia['files'])) {
            unset ($aCustomForm['inputs']['header_files']);
            unset ($aCustomForm['inputs']['files_choice']);
            unset ($aCustomForm['inputs']['files_upload']);
            unset ($aCustomForm['inputs']['allow_upload_files_to']);
        }

        if (!$aCustomForm['inputs']['youtube_choice']['content'])
            unset ($aCustomForm['inputs']['youtube_choice']);

        $oModuleDb = new BxDolModuleDb();
        if (!$oModuleDb->getModuleByUri('forum'))
            unset ($aCustomForm['inputs']['allow_post_in_forum_to']);
		//[end] added 7.1

		//[begin] location integration - modzzz
		if(!$iLocationId){
			unset ($aCustomForm['inputs']['location_id']);
			unset ($aCustomForm['inputs']['location_name']);
		}
		//[end] location integration - modzzz
 
        $this->processMembershipChecksForMediaUploads ($aCustomForm['inputs']);

        parent::__construct ($aCustomForm);
    }

   function processMembershipChecksForMediaUploads (&$aInputs) {

        $isAdmin = $GLOBALS['logged']['admin'] && isProfileActive($this->_iProfileId);

        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'schools photos add', 'schools sounds add', 'schools videos add', 'schools files add', 'schools allow embed'));

		if (defined("BX_PHOTOS_ADD")){ 
			$aCheck = checkAction($_COOKIE['memberID'], BX_PHOTOS_ADD);
			if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED && !$isAdmin) {
				unset($aInputs['thumb']);
			}
		}

        $a = array ('images' => 'PHOTOS', 'videos' => 'VIDEOS', 'sounds' => 'SOUNDS', 'files' => 'FILES');
        foreach ($a as $k => $v) {
			if (defined("BX_{$v}_ADD"))
				$aCheck = checkAction($_COOKIE['memberID'], constant("BX_{$v}_ADD"));
            if ((!defined("BX_{$v}_ADD") || $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) && !$isAdmin) {
                unset($this->_aMedia[$k]);
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']); 
            }        
        }

        $a = array ('images' => 'PHOTOS', 'videos' => 'VIDEOS', 'sounds' => 'SOUNDS', 'files' => 'FILES');
        foreach ($a as $k => $v) {
			if (defined("BX_SCHOOLS_{$v}_ADD"))
				$aCheck = checkAction($_COOKIE['memberID'], constant("BX_SCHOOLS_{$v}_ADD"));
            if ((!defined("BX_SCHOOLS_{$v}_ADD") || $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) && !$isAdmin) {
                unset($this->_aMedia[$k]);
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']); 
            }        
        } 

		$aCheck = checkAction($_COOKIE['memberID'],  BX_SCHOOLS_ALLOW_EMBED);
		if ( $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED && !$isAdmin) { 
			unset($aInputs['header_youtube']);
			unset($aInputs['youtube_choice']); 
			unset($aInputs['youtube_attach']); 
 		} 

    }

	function generateCustomYoutubeTemplate ($iProfileId, $iEntryId) {
	 
		$aTemplates = array ();
	
		$aYoutubes = $this->_oDb->getYoutubeVideos ($iEntryId); 
 
		$aFeeds = array();
		foreach ($aYoutubes as $k => $r) {
			$aFeeds[$k] = array();
			$aFeeds[$k]['id'] = $r['id'];
			$aFeeds[$k]['video_id'] = $this->_oTemplate->youtubeId($r['url']);
			$aFeeds[$k]['video_title'] = $r['title'];
		}

		if(!empty($aFeeds)){
			$aVarsChoice = array ( 
				'bx_if:empty' => array(
					'condition' => empty($aFeeds),
					'content' => array ()
				),

				'bx_repeat:videos' => $aFeeds,
			);                               
			$aTemplates['choice'] =  $this->_oMain->_oTemplate->parseHtmlByName('form_field_youtube_choice', $aVarsChoice);
		}

		// upload form
		$aVarsUpload = array ();            
		$aTemplates['upload'] = $this->_oMain->_oTemplate->parseHtmlByName('form_field_youtube', $aVarsUpload);
 
		return $aTemplates;
	} 






}
