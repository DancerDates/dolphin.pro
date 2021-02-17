<?php
/***************************************************************************
*                            Dolphin Smart Schools Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Schools
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

bx_import ('BxDolProfileFields');
bx_import ('BxDolFormMedia');

class BxSchoolsEventsFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function __construct ($oMain, $iProfileId, $iSchoolId = 0, $iEventId = 0, $iThumb = 0) { 
        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventsMediaPrefix;

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
/*
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
*/
        $oProfileFields = new BxDolProfileFields(0);
  
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
 
		if($iEventId){
			$aDataEntry = $this->_oDb->getEventsEntryById($iEventId);
			
			$sSelState = $aDataEntry['state']; 
			$sSelCountry = $aDataEntry['country'];  
			$aStates = $this->_oDb->getStateArray($sSelCountry); 
		}else{
			$aProfile = getProfileInfo($this->_oMain->_iProfileId);
			$sSelCountry = $aProfile['Country'];  
			$aStates = $this->_oDb->getStateArray($sSelCountry);  
		}
	 
		$sStateUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home/?ajax=state&country=' ; 



        // generate templates for form custom elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($this->_oMain->_iProfileId, $iEventId, $iThumb);

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
  
        $aInputPrivacyViewParticipants = $GLOBALS['oBxSchoolsModule']->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'view_participants');
        $aInputPrivacyViewParticipants['values'] = array_merge($aInputPrivacyViewParticipants['values'], $aInputPrivacyCustom);
         $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyJoin = $GLOBALS['oBxSchoolsModule']->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'join');
        $aInputPrivacyJoin['values'] = array_merge($aInputPrivacyJoin['values'], $aInputPrivacyCustom);
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyComment = $GLOBALS['oBxSchoolsModule']->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'comment');
        $aInputPrivacyComment['values'] = array_merge($aInputPrivacyComment['values'], $aInputPrivacyCustom);
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $GLOBALS['oBxSchoolsModule']->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'rate');
        $aInputPrivacyRate['values'] = array_merge($aInputPrivacyRate['values'], $aInputPrivacyCustom);
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;
 
        $aInputPrivacyUploadPhotos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'upload_photos');
        $aInputPrivacyUploadPhotos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadPhotos['db'] = $aInputPrivacyCustom2Pass;
/*
        $aInputPrivacyUploadVideos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'upload_videos');
        $aInputPrivacyUploadVideos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadVideos['db'] = $aInputPrivacyCustom2Pass;        

        $aInputPrivacyUploadSounds = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'upload_sounds');
        $aInputPrivacyUploadSounds['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadSounds['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadFiles = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'upload_files');
        $aInputPrivacyUploadFiles['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadFiles['db'] = $aInputPrivacyCustom2Pass;
*/
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_events',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_schools_events_main',
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
                'event_start' => array(
                    'type' => 'datetime',
                    'name' => 'event_start',
                    'caption' => _t('_modzzz_schools_form_caption_event_start'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'DateTime',
                        'error' => _t('_modzzz_schools_form_err_event_start'),
                    ),
                    'db' => array (
                        'pass' => 'DateTime', 
                    ),    
                    'display' => 'filterDate',
                ),                                
                'event_end' => array(
                    'type' => 'datetime',
                    'name' => 'event_end',
                    'caption' => _t('_modzzz_schools_form_caption_event_end'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'DateTime',
                        'error' => _t('_modzzz_schools_form_err_event_end'),
                    ),
                    'db' => array (
                        'pass' => 'DateTime', 
                    ),                    
                    'display' => 'filterDate',
                ),                                
                 
                'header_location' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_form_header_location')
                ), 
                'place' => array(
                    'type' => 'text',
                    'name' => 'place',
                    'caption' => _t('_modzzz_schools_form_caption_place'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_schools_form_err_place'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
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
                        'func' => 'preg',
                        'params' => array('/^[a-zA-Z]{2}$/'),
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
					'caption' => _t('_modzzz_schools_form_caption_state'),
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
                        'params' => array(3,50),
                        'error' => _t ('_modzzz_schools_form_err_city'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),  
                    'display' => true,
                ),  
                'address1' => array(
                    'type' => 'text',
                    'name' => 'address1',
                    'caption' => _t('_modzzz_schools_form_caption_address1'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_schools_form_err_address1'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                
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
/*
                // videos

                'header_videos' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_subprofile_form_header_videos'),
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
                    'caption' => _t('_modzzz_schools_subprofile_form_header_sounds'),
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
                    'caption' => _t('_modzzz_schools_subprofile_form_header_files'),
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
*/
                // privacy
                
                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_schools_form_header_privacy'),
                ),

                'allow_view_to' => $GLOBALS['oBxSchoolsModule']->_oPrivacy->getGroupChooser($iProfileId, 'schools', 'view'),
 
                'allow_view_participants_to' => $aInputPrivacyViewParticipants,
  
                'allow_join_to' => $aInputPrivacyJoin,
  
                'join_confirmation' => array (
                    'type' => 'select',
                    'name' => 'join_confirmation',
                    'caption' => _t('_modzzz_schools_form_caption_join_confirmation'),
                    'info' => _t('_modzzz_schools_form_info_event_join_confirmation'),
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
 
                'allow_comment_to' => $aInputPrivacyComment,

                'allow_rate_to' => $aInputPrivacyRate, 
   
                'allow_upload_photos_to' => $aInputPrivacyUploadPhotos, 
/*
                'allow_upload_videos_to' => $aInputPrivacyUploadVideos, 

                'allow_upload_sounds_to' => $aInputPrivacyUploadSounds, 

                'allow_upload_files_to' => $aInputPrivacyUploadFiles,
*/					
				'Submit' => array (
					'type' => 'submit',
					'name' => 'submit_form',
					'value' => _t('_Submit'),
					'colspan' => false
				),  

            ),            
        );

        if (!$aCustomForm['inputs']['images_choice']['content']) {
            unset ($aCustomForm['inputs']['thumb']);
            unset ($aCustomForm['inputs']['images_choice']);
        }
/*
        if (!$aCustomForm['inputs']['videos_choice']['content'])
            unset ($aCustomForm['inputs']['videos_choice']);

        if (!$aCustomForm['inputs']['sounds_choice']['content'])
            unset ($aCustomForm['inputs']['sounds_choice']);

        if (!$aCustomForm['inputs']['files_choice']['content'])
            unset ($aCustomForm['inputs']['files_choice']);
 */
       if (!isset($this->_aMedia['images'])) {
            unset ($aCustomForm['inputs']['header_images']);
            unset ($aCustomForm['inputs']['thumb']);
            unset ($aCustomForm['inputs']['images_choice']);
            unset ($aCustomForm['inputs']['images_upload']);
            unset ($aCustomForm['inputs']['allow_upload_photos_to']);
        }
/*
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
  */
        $this->processMembershipChecksForMediaUploads ($aCustomForm['inputs']);

        parent::__construct ($aCustomForm);
    }

    /**
     * @access private
     */ 
    function _getFilesInEntry ($sModuleName, $sServiceMethod, $sName, $sMediaType, $iIdProfile, $iEventId)
    {             

        $aReadyMedia = array ();
        if ($iEventId)
            $aReadyMedia = $this->_oDb->getMediaIds($iEventId, $sMediaType);
        
        if (!$aReadyMedia)
            return array();

        $aDataEntry = $this->_oDb->getEventsEntryById($iEventId);

        $aFiles = array ();
        foreach ($aReadyMedia as $iMediaId)
        {
            switch ($sModuleName) {
            case 'photos':
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId, 'icon'), 'Search');
                break;
            case 'sounds':
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId, 'browse'), 'Search');
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
     * @param $iEventId associated entry id
     * @return nothing
     */ 
    function processMedia ($iEventId, $iProfileId) { 

        $aDataEntry = $this->_oDb->getEventsEntryById($iEventId);
		
		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventsMediaPrefix;

        foreach ($this->_aMedia as $sName => $a) {
 
            $aFiles = $this->_getFilesInEntry ($a['module'], $a['service_method'], $a['post'], $sName, (int)$iProfileId, $iEventId);
            foreach ($aFiles as $aRow)
                $aFiles2Delete[$aRow['id']] = $aRow['id'];

            if (is_array($_REQUEST[$a['post']]) && $_REQUEST[$a['post']] && $_REQUEST[$a['post']][0]) {
                $this->updateMedia ($iEventId, $_REQUEST[$a['post']], $aFiles2Delete, $sName);
            } else {
                $this->deleteMedia ($iEventId, $aFiles2Delete, $sName);
            }

            $sUploadFunc = $a['upload_func'];
            if ($aMedia = $this->$sUploadFunc($a['tag'], $a['cat'])) {
                $this->_oDb->insertMedia ($iEventId, $aMedia, $sName);
                if ($a['thumb'] && !$aDataEntry[$a['thumb']] && !$_REQUEST[$a['thumb']]) 
                    $this->_oDb->setThumbnail ($iEventId, 0);
            }

            $aMediaIds = $this->_oDb->getMediaIds($iEventId, $sName);

            if ($a['thumb']) { // set thumbnail to another one if current thumbnail is deleted                
                $sThumbFieldName = $a['thumb'];
                if ($aDataEntry[$sThumbFieldName] && !isset($aMediaIds[$aDataEntry[$sThumbFieldName]])) {
                    $this->_oDb->setThumbnail ($iEventId, 0);
                } 
            }

            // process all deleted media - delete actual file
			if(is_array($aFiles2Delete) && is_array($aMediaIds)){
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

 

}
