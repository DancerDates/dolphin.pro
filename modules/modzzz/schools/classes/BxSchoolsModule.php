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

function modzzz_schools_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'schools') {
        $oMain = BxDolModule::getInstance('BxSchoolsModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');

define ('BX_SCHOOLS_PHOTOS_CAT', 'Schools');
define ('BX_SCHOOLS_PHOTOS_TAG', 'schools');

define ('BX_SCHOOLS_VIDEOS_CAT', 'Schools');
define ('BX_SCHOOLS_VIDEOS_TAG', 'schools');

define ('BX_SCHOOLS_SOUNDS_CAT', 'Schools');
define ('BX_SCHOOLS_SOUNDS_TAG', 'schools');

define ('BX_SCHOOLS_FILES_CAT', 'Schools');
define ('BX_SCHOOLS_FILES_TAG', 'schools');

define ('BX_SCHOOLS_MAX_FANS', 10000);
 

/*
 * Schools module
 *
 * This module allow users to create user's schools, 
 * users can rate, comment and discuss school.
 * School can have photos, videos, sounds and files, uploaded
 * by school's fans and/or admins.
 *
 * 
 *
 * Profile's Wall:
 * 'add school' event is displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new school was created
 * change - school was chaned
 * join - somebody joined school
 * rate - somebody rated school
 * commentPost - somebody posted comment in school
 *
 *
 *
 * Memberships/ACL:
 * schools view school - BX_SCHOOLS_VIEW_SCHOOL
 * schools browse - BX_SCHOOLS_BROWSE
 * schools search - BX_SCHOOLS_SEARCH
 * schools add school - BX_SCHOOLS_ADD_SCHOOL
 * schools comments delete and edit - BX_SCHOOLS_COMMENTS_DELETE_AND_EDIT
 * schools edit any school - BX_SCHOOLS_EDIT_ANY_SCHOOL
 * schools delete any school - BX_SCHOOLS_DELETE_ANY_SCHOOL
 * schools mark as featured - BX_SCHOOLS_MARK_AS_FEATURED
 * schools approve schools - BX_SCHOOLS_APPROVE_SCHOOLS
 * schools broadcast message - BX_SCHOOLS_BROADCAST_MESSAGE
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different schools
 * @see BxSchoolsModule::serviceHomepageBlock
 * BxDolService::call('schools', 'homepage_block', array());
 *
 * Profile block with user's schools
 * @see BxSchoolsModule::serviceProfileBlock
 * BxDolService::call('schools', 'profile_block', array($iProfileId));
 *
 * School's forum permissions (for internal usage only)
 * @see BxSchoolsModule::serviceGetForumPermission
 * BxDolService::call('schools', 'get_forum_permission', array($iMemberId, $iForumId));
 *
 * Member menu item for schools (for internal usage only)
 * @see BxSchoolsModule::serviceGetMemberMenuItem
 * BxDolService::call('schools', 'get_member_menu_item', array());
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'modzzz_schools'
 * The following alerts are rised
 *
 *  join - user joined a school
 *      $iObjectId - school id
 *      $iSenderId - joined user
 *
 *  join_request - user want to join a school
 *      $iObjectId - school id
 *      $iSenderId - user id which want to join a school
 *
 *  join_reject - user was rejected to join a school
 *      $iObjectId - school id
 *      $iSenderId - regected user id
 *
 *  fan_remove - fan was removed from a school
 *      $iObjectId - school id
 *      $iSenderId - fan user if which was removed from admins
 *
 *  fan_become_admin - fan become school's admin
 *      $iObjectId - school id
 *      $iSenderId - nerw school's fan user id
 *
 *  admin_become_fan - school's admin become regular fan
 *      $iObjectId - school id
 *      $iSenderId - school's admin user id which become regular fan
 *
 *  join_confirm - school's admin confirmed join request
 *      $iObjectId - school id
 *      $iSenderId - condirmed user id
 *
 *  add - new school was added
 *      $iObjectId - school id
 *      $iSenderId - creator of a school
 *      $aExtras['Status'] - status of added school
 *
 *  change - school's info was changed
 *      $iObjectId - school id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed school
 *
 *  delete - school was deleted
 *      $iObjectId - school id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - school was marked/unmarked as featured
 *      $iObjectId - school id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if school was marked as featured and 0 - if school was removed from featured 
 *
 */
class BxSchoolsModule extends BxDolTwigModule {

    var $_oPrivacy;
    var $_oSubPrivacy; 
    var $_aQuickCache = array ();

    function __construct(&$aModule) {

        parent::__construct($aModule);        
        $this->_sFilterName = 'filter';
        $this->_sPrefix = 'modzzz_schools';

        bx_import ('Privacy', $aModule); 
		bx_import ('SubPrivacy', $aModule); 
		$this->_oPrivacy = new BxSchoolsPrivacy($this);
 
 
		if($_GET['ajax']=='state'){ 
			$sCountryCode = $_GET['country'];
			echo $this->_oDb->getStateOptions($sCountryCode);
			exit;
		}		
  
        $GLOBALS['oBxSchoolsModule'] = &$this;
    }

    function actionHome () {
        parent::_actionHome(_t('_modzzz_schools_page_title_home'));
    }

    function actionFiles ($sUri) {
        parent::_actionFiles ($sUri, _t('_modzzz_schools_page_title_files'));
    }

    function actionSounds ($sUri) {
        parent::_actionSounds ($sUri, _t('_modzzz_schools_page_title_sounds'));
    }

    function actionVideos ($sUri) {
        parent::_actionVideos ($sUri, _t('_modzzz_schools_page_title_videos'));
    }

    function actionPhotos ($sUri) {
        parent::_actionPhotos ($sUri, _t('_modzzz_schools_page_title_photos'));
    }

    function actionComments ($sUri) {
        parent::_actionComments ($sUri, _t('_modzzz_schools_page_title_comments'));
    }

    function actionBrowseFans ($sUri) {
        parent::_actionBrowseFans ($sUri, 'isAllowedViewFans', 'getFansBrowse', $this->_oDb->getParam('modzzz_schools_perpage_browse_fans'), 'browse_fans/', _t('_modzzz_schools_page_title_fans'));
    }

    function actionBrowseStudents ($sUri) {
        parent::_actionBrowseFans ($sUri, 'isAllowedViewFans', 'getStudentsBrowse', $this->_oDb->getParam('modzzz_schools_perpage_browse_fans'), 'browse_students/', _t('_modzzz_schools_page_title_students'));
    }

    function actionBrowseAlumni ($sUri) {
        parent::_actionBrowseFans ($sUri, 'isAllowedViewFans', 'getAlumniBrowse', $this->_oDb->getParam('modzzz_schools_perpage_browse_fans'), 'browse_alumni/', _t('_modzzz_schools_page_title_alumni'));
    }

    function actionView ($sUri) {
        parent::_actionView ($sUri, _t('_modzzz_schools_msg_pending_approval'));
    }

    function actionUploadPhotos ($sUri) {        
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadPhotos', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_schools_page_title_upload_photos'));
    }

    function actionUploadVideos ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadVideos', 'videos', array ('videos_choice', 'videos_upload'), _t('_modzzz_schools_page_title_upload_videos'));
    }

    function actionUploadSounds ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadSounds', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_modzzz_schools_page_title_upload_sounds')); 
    }

    function actionUploadFiles ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadFiles', 'files', array ('files_choice', 'files_upload'), _t('_modzzz_schools_page_title_upload_files')); 
    }
 
    function _getInviteParams ($aDataEntry, $aInviter) {
        
		if($aInviter){ 
			$sInviterNickName = ($aInviter['FirstName']) ? $aInviter['FirstName'] .' '. $aInviter['LastName'] : $aInviter['NickName'];
		}else{
			$sInviterNickName = _t('_modzzz_schools_friend'); 
		}

		return array (
                'SchoolName' => $aDataEntry['title'],
                'SchoolLocation' => _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']) . (trim($aDataEntry['city']) ? ', '.$aDataEntry['city'] : '') . ', ' . $aDataEntry['zip'],
                'SchoolUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'].'?&idRef='.$aInviter['ID'].'&idCode='.time(), 
                'InviterUrl' => $aInviter ? getProfileLink($aInviter['ID']) : 'javascript:void(0);',
                'InviterNickName' => $sInviterNickName,
                'InvitationText' => stripslashes(strip_tags($_REQUEST['inviter_text'])),
				'Item' => 'modzzz_schools',
				'ItemID' => $aDataEntry['id'], 
				'Ref' => $aInviter['ID'], 
				'Code' => time(), 
            );        
    }  
  
    function actionAdd () {
        parent::_actionAdd (_t('_modzzz_schools_page_title_add'));
    }

    function _addForm ($sRedirectUrl) {

        bx_import ('FormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {

            $sStatus = $this->_oDb->getParam($this->_sPrefix.'_autoapproval') == 'on' || $this->isAdmin() ? 'approved' : 'pending';
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
            );                        
            $aValsAdd[$this->_oDb->_sFieldAuthorId] = $this->_iProfileId;

            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 
  				
				$this->_oDb->addYoutube($iEntryId);

                $oForm->processMedia($iEntryId, $this->_iProfileId);

                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());
                $this->onEventCreate($iEntryId, $sStatus, $aDataEntry);
                if (!$sRedirectUrl)
                    $sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                echo MsgBox(_t('_Error Occured'));
            }
                         
        } else {
            
            echo $oForm->getCode (); 
        }
    }
   
    function actionEdit ($iEntryId) {
        $this->_actionEdit ($iEntryId, _t('_modzzz_schools_page_title_edit'));
    }

    function _actionEdit ($iEntryId, $sTitle)
    {
        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));

        if (!$this->isAllowedEdit($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        bx_import ('FormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormEdit';
        $oForm = new $sClass ($this, $aDataEntry[$this->_oDb->_sFieldAuthorId], $iEntryId, $aDataEntry);
        if (isset($aDataEntry[$this->_oDb->_sFieldJoinConfirmation]))
            $aDataEntry[$this->_oDb->_sFieldJoinConfirmation] = (int)$aDataEntry[$this->_oDb->_sFieldJoinConfirmation];
 
		
		if(!$aDataEntry['school_qualifications'])
			 unset($aDataEntry['school_qualifications']);
		
		if(!$aDataEntry['school_sports'])
			 unset($aDataEntry['school_sports']);		
		
		if(!$aDataEntry['school_clubs'])
			 unset($aDataEntry['school_clubs']);

        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {

            $sStatus = $this->_oDb->getParam($this->_sPrefix . '_autoapproval') == 'on' || $this->isAdmin() ? 'approved' : 'pending';
            $aValsAdd = array ($this->_oDb->_sFieldStatus => $sStatus);
            if ($oForm->update ($iEntryId, $aValsAdd)) {

				//[begin] youtube
				$aYoutubes2Keep = array(); 
				if( is_array($_POST['prev_video']) && count($_POST['prev_video'])){ 
					foreach ($_POST['prev_video'] as $iYoutubeId){
						$aYoutubes2Keep[$iYoutubeId] = $iYoutubeId;
					}
				}
					
				$aYoutubeIds = $this->_oDb->getYoutubeIds($iEntryId); 
				$aDeletedYoutube = array_diff ($aYoutubeIds, $aYoutubes2Keep);

				if ($aDeletedYoutube) {
					foreach ($aDeletedYoutube as $iYoutubeId) {
						$this->_oDb->removeYoutube($iEntryId, $iYoutubeId);
					}
				} 
 
				$this->_oDb->addYoutube($iEntryId);
 				//[end] youtube 
 
                $oForm->processMedia($iEntryId, $this->_iProfileId);

                $this->isAllowedEdit($aDataEntry, true); // perform action

                $this->onEventChanged ($iEntryId, $sStatus);
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }

        } else { 
            echo $oForm->getCode (); 
        }

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode($sTitle);
    }
 
    function actionDelete ($iEntryId) {
        parent::_actionDelete ($iEntryId, _t('_modzzz_schools_msg_school_was_deleted'));
    }

    function actionMarkFeatured ($iEntryId) {
        parent::_actionMarkFeatured ($iEntryId, _t('_modzzz_schools_msg_added_to_featured'), _t('_modzzz_schools_msg_removed_from_featured'));
    }
 
    function actionSharePopup ($iEntryId) {
        parent::_actionSharePopup ($iEntryId, _t('_modzzz_schools_caption_share_school'));
    }

    function actionManageFansPopup ($iEntryId) {
        parent::_actionManageFansPopup ($iEntryId, _t('_modzzz_schools_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', BX_SCHOOLS_MAX_FANS);
    }
 
    function actionTags() {
        parent::_actionTags (_t('_modzzz_schools_page_title_tags'));
    }    

    function actionCategories() {
        parent::_actionCategories (_t('_modzzz_schools_page_title_categories'));
    }    

    function actionDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'files');

        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getEntryByIdAndOwner((int)$iEntryId, 0, true))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }

        if (!$this->isAllowedView ($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

    // ================================== external actions


     /**
     * forum permissions
     * @param $iMemberId profile id
     * @param $iForumId forum id
     * @return array with permissions
     */ 
    function serviceGetForumPermission($iMemberId, $iForumId) {

        $iMemberId = (int)$iMemberId;
        $iForumId = (int)$iForumId;

        $aFalse = array ( // default permissions, for visitors for example
            'admin' => 0,
            'read' => 0,
            'post' => 0,
        );

        if (!($aForum = $this->_oDb->getForumById ($iForumId))) {    
			return $aFalse;
        }
  
        if (!($aDataEntry = $this->_oDb->getEntryById ($aForum['entry_id']))){
 			return $aFalse;
		}
 
        $aTrue = array (
            'admin' => $aDataEntry[$this->_oDb->_sFieldAuthorId] == $iMemberId || $this->isAdmin() ? 1 : 0, // author is admin
            'read' => $this->isAllowedView ($aDataEntry) ? 1 : 0,
            'post' => $this->isAllowedPostInForum ($aDataEntry, $iMemberId) ? 1 : 0,
        );
  
        return $aTrue;
    }


    /**
     * Homepage block with different schools
     * @return html to display on homepage in a block
     */     
    function serviceHomepageBlock () {

        if (!$this->_oDb->isAnyPublicContent())
            return '';
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        bx_import ('PageMain', $this->_aModule);
        $o = new BxSchoolsPageMain ($this);
        $o->sUrlStart = BX_DOL_URL_ROOT . 'index.php?';
 
        $sDefaultHomepageTab = $this->_oDb->getParam('modzzz_schools_homepage_default_tab');
        $sBrowseMode = $sDefaultHomepageTab;
        switch ($_GET['filter']) {            
            case 'featured':
            case 'recent':
            case 'top':
            case 'popular':
            case $sDefaultHomepageTab:            
                $sBrowseMode = $_GET['filter'];
                break;
        }

        return $o->ajaxBrowse(
            $sBrowseMode,
            $this->_oDb->getParam('modzzz_schools_perpage_homepage'), 
            array(
                _t('_modzzz_schools_tab_featured') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=featured', 'active' => 'featured' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_schools_tab_recent') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=recent', 'active' => 'recent' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_schools_tab_top') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=top', 'active' => 'top' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_schools_tab_popular') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=popular', 'active' => 'popular' == $sBrowseMode, 'dynamic' => true),
            )
        );
    }

    /**
     * Profile block with user's schools
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceJoinedBlock ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxSchoolsPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']);
        $o->sUrlStart .= (false === strpos($o->sUrlStart, '?') ? '?' : '&');  

		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'joined', 
            $this->_oDb->getParam('modzzz_schools_perpage_profile'), 
            array(),
            process_db_input ($aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            true,
            false 
        );
    }

    /**
     * Profile block with user's schools
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlock ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxSchoolsPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']);
        $o->sUrlStart .= (false === strpos($o->sUrlStart, '?') ? '?' : '&');  

		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_schools_perpage_profile'), 
            array(),
            process_db_input ($aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            true,
            false 
        );
    }

    /**
     * Profile block with user's schools
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceSchoolMates ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxSchoolsPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']);
        $o->sUrlStart .= (false === strpos($o->sUrlStart, '?') ? '?' : '&');  

		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxMatesBrowse(
            'school_mates', 
            $this->_oDb->getParam('modzzz_schools_perpage_profile'), 
            array(),
            process_db_input ($aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            true,
            false 
        );
    }

    /**
     * Profile block with schools user joied
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlockJoined ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxSchoolsPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']);
        $o->sUrlStart .= (false === strpos($o->sUrlStart, '?') ? '?' : '&');  

		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'joined', 
            $this->_oDb->getParam('modzzz_schools_perpage_profile'), 
            array(),
            process_db_input ($aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            true,
            false 
        );
    }
  
    function serviceGetMemberMenuItem () {
        parent::_serviceGetMemberMenuItem (_t('_modzzz_schools'), _t('_modzzz_schools'), 'pencil');
    }
 
    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        return parent::_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_modzzz_schools_spy_post',
            'change' => '_modzzz_schools_spy_post_change',
            'join' => '_modzzz_schools_spy_join',
            'rate' => '_modzzz_schools_spy_rate',
            'commentPost' => '_modzzz_schools_spy_comment',
        ));
    }

    function serviceGetSubscriptionParams ($sAction, $iEntryId) {

        $a = array (
            'change' => _t('_modzzz_schools_sbs_change'),
            'commentPost' => _t('_modzzz_schools_sbs_comment'),
            'rate' => _t('_modzzz_schools_sbs_rate'),
            'join' => _t('_modzzz_schools_sbs_join'),
        );

        return parent::_serviceGetSubscriptionParams ($sAction, $iEntryId, $a);
    }

    // ================================== admin actions

    function actionAdministration ($sUrl = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();

        $aMenu = array(
            'pending_approval' => array(
                'title' => _t('_modzzz_schools_menu_admin_pending_approval'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_approval', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false)),
            ),
            'admin_entries' => array(
                'title' => _t('_modzzz_schools_menu_admin_entries'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ), 
			'claims' => array(
                'title' => _t('_modzzz_schools_menu_manage_claims'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/claims',
                '_func' => array ('name' => 'actionAdministrationClaims', 'params' => array($sParam1)),
            ),  
            'create' => array(
                'title' => _t('_modzzz_schools_menu_admin_add_entry'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
            'settings' => array(
                'title' => _t('_modzzz_schools_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'pending_approval';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_schools_page_title_administration'), $aMenu);

        $this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css'));
     
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_schools_page_title_administration'));
    }
  
    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('Schools');
    }

    function actionAdministrationManage ($isAdminEntries = false) {
        return parent::_actionAdministrationManage ($isAdminEntries, '_modzzz_schools_admin_delete', '_modzzz_schools_admin_activate');
    }

    // ================================== events


    function onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_schools_join_request', BX_SCHOOLS_MAX_FANS);
    }

    function onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_schools_join_reject');
    }

    function onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_schools_fan_remove');
    }

    function onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_schools_fan_become_admin');
    }

    function onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_schools_admin_become_fan');
    }

    function onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_schools_join_confirm');
    }

    // ================================== permissions
    
    function isAllowedView ($aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_VIEW_SCHOOL, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;

        // check user school 
	    return $this->_oPrivacy->check('view_school', $aDataEntry['id'], $this->_iProfileId); 
    }

    function isAllowedBrowse ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_ADD_SCHOOL, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedEdit ($aDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry) || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;

        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_EDIT_ANY_SCHOOL, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedMarkAsFeatured ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function isAllowedBroadcast ($aDataEntry, $isPerformAction = false) {
        if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_BROADCAST_MESSAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function isAllowedDelete (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry) || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_DELETE_ANY_SCHOOL, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }     

    function isAllowedJoin (&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;
        return $this->_oPrivacy->check('join', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedSendInvitation (&$aDataEntry) {
        return getLoggedId();
    }
 
    function isAllowedShare (&$aDataEntry)
    {
    	return ($aDataEntry[$this->_oDb->_sFieldAllowViewTo] == BX_DOL_PG_ALL);
    }

    function isAllowedPostInForum(&$aDataEntry, $iProfileId = -1) {
        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) || $this->_oPrivacy->check('post_in_forum', $aDataEntry['id'], $iProfileId);
    }

    function isAllowedRate(&$aDataEntry) {        
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        return $this->_oPrivacy->check('rate', $aDataEntry['id'], $this->_iProfileId);        
    }

    function isAllowedComments(&$aDataEntry) {
       
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        return $this->_oPrivacy->check('comment', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedViewFans(&$aDataEntry) {
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        return $this->_oPrivacy->check('view_fans', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadPhotos(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            return false;
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        return $this->_oPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadVideos(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            return false;

        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        if (!$this->isMembershipEnabledForVideos())
            return false;        
        return $this->_oPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadSounds(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            return false;

        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        if (!$this->isMembershipEnabledForSounds())
            return false;                
        return $this->_oPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFiles(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            return false;

        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        if (!$this->isMembershipEnabledForFiles())
            return false;                        
        return $this->_oPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedCreatorCommentsDeleteAndEdit (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (getParam('modzzz_schools_author_comments_admin') && $this->isEntryAdmin($aDataEntry))
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedManageAdmins($aDataEntry) {
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))
            return true;
        return false;
    }

    function isAllowedManageFans($aDataEntry) {
        return $this->isEntryAdmin($aDataEntry);
    }

    function isFan($aDataEntry, $iProfileId = 0, $isConfirmed = true) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isFan ($aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }

    function isEntryAdmin($aDataEntry, $iProfileId = 0) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;
        return $this->_oDb->isGroupAdmin ($aDataEntry['id'], $iProfileId) && isProfileActive($iProfileId);
    }
 
    function _defineActions () {
        defineMembershipActions(array('schools view school', 'schools browse', 'schools search', 'schools add school', 'schools comments delete and edit', 'schools edit any school', 'schools delete any school', 'schools mark as featured', 'schools approve schools', 'schools broadcast message', 'schools make claim', 'schools add student', 'schools add instructor'));
    }

    function _browseMy (&$aProfile) {        
        parent::_browseMy ($aProfile, _t('_modzzz_schools_page_title_my_schools'));
    } 
	 
    function actionBroadcast ($iEntryId) {
        $this->_actionBroadcast ($iEntryId, _t('_modzzz_schools_page_title_broadcast'), _t('_modzzz_schools_msg_broadcast_no_recipients'), _t('_modzzz_schools_msg_broadcast_message_sent'));
    }

    function actionInvite ($iEntryId) {
        $this->_actionInvite ($iEntryId, 'modzzz_schools_invitation', $this->_oDb->getParam('modzzz_schools_max_email_invitations'), _t('_modzzz_schools_msg_invitation_sent'), _t('_modzzz_schools_msg_no_users_to_invite'), _t('_modzzz_schools_page_title_invite'));
    }

    function actionCalendar ($iYear = '', $iMonth = '') {
 
        parent::_actionCalendar ($iYear, $iMonth, _t('_modzzz_schools_calendar'));
    }

    function actionSearch ($sKeyword = '', $sCategory = '', $sCountry = '', $sState = '', $sCity = '', $sSchoolStart = '', $sSchoolEnd = '') {

        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        if ($sKeyword) 
            $_GET['keyword'] = $sKeyword;
        if ($sCategory)
            $_GET['categories'] = explode(',', $sCategory);
        if ($sCountry)
            $_GET['country'] = explode(',', $sCountry);
		if ($sState) 
            $_GET['state'] = $sState;
		if ($sCity) 
            $_GET['city'] = $sCity;
 


        if (is_array($_GET['country']) && 1 == count($_GET['country']) && !$_GET['country'][0]) {
            unset($_GET['country']);
            unset($sCountry);
        }

        if (is_array($_GET['categories']) && 1 == count($_GET['categories']) && !$_GET['categories'][0]) {
            unset($_GET['categories']);
            unset($sCategory);
        }
 
        if ($sCountry || $sCategory || $sKeyword || $sState || $sCity ) {
            $_GET['submit_form'] = 1;  
        }
        
        modzzz_schools_import ('FormSearch');
        $oForm = new BxSchoolsFormSearch ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
            modzzz_schools_import ('SearchResult');
            $o = new BxSchoolsSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('Country'), $oForm->getCleanValue('State'), $oForm->getCleanValue('City'));

            if ($o->isError) {
                $this->_oTemplate->displayPageNotFound ();
                return;
            }

            if ($s = $o->processing()) {
                echo $s;
            } else {
                $this->_oTemplate->displayNoData ();
                return;
            }

            $this->isAllowedSearch(true); // perform search action 

			$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

            $this->_oTemplate->pageCode($o->aCurrent['title'], false, false);
            return;

        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode(_t('_modzzz_schools_page_title_search'));
    } 
    
    /**
     * Account block with different schools
     * @return html to display on homepage in a block
     */ 
    function serviceAccountPageBlock () {
 
        if (!$this->_oDb->isAnyPublicContent())
            return '';

		$aProfileInfo = getProfileInfo($this->_iProfileId);
		$sCity = $aProfileInfo['City'];

		if(!$sCity) return;

        bx_import ('PageMain', $this->_aModule);
        $o = new BxSchoolsPageMain ($this);        
        $o->sUrlStart = BX_DOL_URL_ROOT . '?';
 
        return $o->ajaxBrowse(
            'local',
            $this->_oDb->getParam('modzzz_schools_perpage_accountpage'),
			array(),
			$sCity
        );
    }
  
    function onEventMarkAsFeatured ($iEntryId, $aDataEntry) {
		
		$this->_oDb->flagActivity('mark_as_featured', $iEntryId, $this->_iProfileId, array('Featured' => $aDataEntry[$this->_oDb->_sFieldFeatured]));

			// arise alert
		$oAlert = new BxDolAlerts($this->_sPrefix, 'mark_as_featured', $iEntryId, $this->_iProfileId, array('Featured' => $aDataEntry[$this->_oDb->_sFieldFeatured]));
		$oAlert->alert();
    }        
 
    function actionJoin ($iEntryId, $iProfileId) {
 
        parent::_actionJoin ($iEntryId, $iProfileId, _t('_modzzz_schools_msg_joined_already'), _t('_modzzz_schools_msg_joined_request_pending'), _t('_modzzz_schools_msg_join_success'), _t('_modzzz_schools_msg_join_success_pending'), _t('_modzzz_schools_msg_leave_success'));
    }   
 
    function actionLocal ($sCountry='', $sState='') { 
        $this->_oTemplate->pageStart();
        bx_import ('PageLocal', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageLocal';
        $oPage = new $sClass ($this, $sCountry, $sState);
        echo $oPage->getCode();
		
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

         $this->_oTemplate->pageCode(_t('_modzzz_schools_page_title_local'), false, false);
    } 

    function _actionBroadcast ($iEntryId, $sTitle, $sMsgNoRecipients, $sMsgSent) {
		global $tmpl;
		require_once( BX_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $tmpl . '/scripts/BxTemplMailBox.php');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if (!$this->isAllowedBroadcast($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$sTitle => '',
		));

        $aRecipients = $this->_oDb->getBroadcastRecipients ($iEntryId);
        if (!$aRecipients) {
            echo MsgBox ($sMsgNoRecipients);
            $this->_oTemplate->pageCode($sMsgNoRecipients, true, true);
            return;
        }

        bx_import ('FormBroadcast', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormBroadcast';
        $oForm = new $sClass ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
            
            $oEmailTemplate = new BxDolEmailTemplates();
            if (!$oEmailTemplate) {
                $this->_oTemplate->displayErrorOccured();
                return;
            }
            $aTemplate = $oEmailTemplate->getTemplate($this->_sPrefix . '_broadcast'); 
            $aTemplateVars = array (
                'BroadcastTitle' => $oForm->getCleanValue ('title'),
                'BroadcastMessage' => $oForm->getCleanValue ('message'),
                'EntryTitle' => $aDataEntry[$this->_oDb->_sFieldTitle],
                'EntryUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],                
            );
  
            $iSentMailsCounter = 0;            
            foreach ($aRecipients as $aProfile) {		        
       	        $iSentMailsCounter += sendMail($aProfile['Email'], $aTemplate['Subject'], $aTemplate['Body'], $aProfile['id'], $aTemplateVars);

				$this->broadCastToInbox($aProfile, $aTemplate, $aTemplateVars);  
            }
            if (!$iSentMailsCounter) {
                $this->_oTemplate->displayErrorOccured();
                return;
            }

            echo MsgBox ($sMsgSent);

            $this->isAllowedBroadcast($aDataEntry, true); // perform send broadcast message action             
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode($sMsgSent, true, true);
            return;
        } 

        echo $oForm->getCode ();

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode($sTitle);
    }

    function _actionInvite ($iEntryId, $sEmailTemplate, $iMaxEmailInvitations, $sMsgInvitationSent, $sMsgNoUsers, $sTitle) {
		global $tmpl;
		require_once( BX_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $tmpl . '/scripts/BxTemplMailBox.php');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$sTitle . $aDataEntry[$this->_oDb->_sFieldTitle] => '',
		));

        bx_import('BxDolTwigFormInviter');
        $oForm = new BxDolTwigFormInviter ($this, $sMsgNoUsers);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        

            $aInviter = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getInviteParams ($aDataEntry, $aInviter);
            
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;

            // send invitation to registered members
            if (false !== bx_get('inviter_users') && is_array(bx_get('inviter_users'))) {
				$aInviteUsers = bx_get('inviter_users');
                foreach ($aInviteUsers as $iRecipient) {
                    $aRecipient = getProfileInfo($iRecipient);
                    $aPlus = array_merge (array ('NickName' => ' ' . $aRecipient['NickName']), $aPlusOriginal);
                    $iSuccess += sendMail(trim($aRecipient['Email']), $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus) ? 1 : 0;

					$this->inviteToInbox($aRecipient, $aTemplate, $aPlusOriginal); 
                }
            }

            // send invitation to additional emails
            $iMaxCount = $iMaxEmailInvitations;
            $aEmails = preg_split ("#[,\s\\b]+#", bx_get('inviter_emails'));
            $aPlus = array_merge (array ('NickName' => ''), $aPlusOriginal);
            if ($aEmails && is_array($aEmails)) {
                foreach ($aEmails as $sEmail) {
                    if (strlen($sEmail) < 5) 
                        continue;
                    $iRet = sendMail(trim($sEmail), $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus) ? 1 : 0;
                    $iSuccess += $iRet;
                    if ($iRet && 0 == --$iMaxCount) 
                        break;
                }             
            }

            $sMsg = sprintf($sMsgInvitationSent, $iSuccess);
            echo MsgBox($sMsg);
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode ($sMsg, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle . $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

    function  inviteToInbox($aProfile, $aTemplate, $aPlusOriginal){
	
	$aMailBoxSettings = array
	(
		'member_id'	 =>  $this->_iProfileId, 
		'recipient_id'	 => $aProfile['ID'], 
		'messages_types'	 =>  'mail',  
	);

	$aComposeSettings = array
	(
		'send_copy' => false , 
		'send_copy_to_me' => false , 
		'notification' => false ,
	);
	$oMailBox = new BxTemplMailBox('mail_page', $aMailBoxSettings);

	$sMessageBody = $aTemplate['Body'];
	$sMessageBody = str_replace("<SchoolName>", $aPlusOriginal['SchoolName'] , $sMessageBody);
	$sMessageBody = str_replace("<SchoolLocation>", $aPlusOriginal['SchoolLocation'] , $sMessageBody);
	$sMessageBody = str_replace("<SchoolUrl>", $aPlusOriginal['SchoolUrl'] , $sMessageBody);
	$sMessageBody = str_replace("<InviterUrl>", $aPlusOriginal['InviterUrl'] , $sMessageBody);
	$sMessageBody = str_replace("<InviterNickName>", $aPlusOriginal['InviterNickName'] , $sMessageBody);
	$sMessageBody = str_replace("<InvitationText>", $aPlusOriginal['InvitationText'] , $sMessageBody);

	$oMailBox -> sendMessage($aTemplate['Subject'], $sMessageBody, $aProfile['ID'], $aComposeSettings); 

   }

   function  broadCastToInbox($aProfile, $aTemplate, $aTemplateVars){

	$aMailBoxSettings = array
	(
		'member_id'	 =>  $this->_iProfileId, 
		'recipient_id'	 => $aProfile['ID'], 
		'messages_types'	 =>  'mail',  
	);

	$aComposeSettings = array
	(
		'send_copy' => false , 
		'send_copy_to_me' => false , 
		'notification' => false ,
	);
	$oMailBox = new BxTemplMailBox('mail_page', $aMailBoxSettings);

	$sMessageBody = $aTemplate['Body'];
	$sMessageBody = str_replace("<NickName>", getNickName($this->_iProfileId), $sMessageBody);
	$sMessageBody = str_replace("<EntryUrl>", $aTemplateVars['EntryUrl'], $sMessageBody);
	$sMessageBody = str_replace("<EntryTitle>", $aTemplateVars['EntryTitle'], $sMessageBody);
	$sMessageBody = str_replace("<BroadcastMessage>", $aTemplateVars['BroadcastMessage'], $sMessageBody);

	$oMailBox -> sendMessage($aTemplateVars['BroadcastTitle'], $sMessageBody, $aProfile['ID'], $aComposeSettings); 

    }

    function parseTags($s)
    {
        return $this->_parseAnything($s, ',', BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/tag/');
    }

    function parseCategories($s)
    {
        return $this->_parseAnything($s, CATEGORIES_DIVIDER, BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/category/');
    }

    function _parseAnything($s, $sDiv, $sLinkStart, $sClassName = '')
    {
        $sRet = '';
        $a = explode ($sDiv, $s);
        $sClass = $sClassName ? 'class="'.$sClassName.'"' : '';
        
        foreach ($a as $sName)
            $sRet .= '<a '.$sClass.' href="' . $sLinkStart . urlencode(title2uri($sName)) . '">'.$sName.'</a>&#160';
        
        return $sRet;
    } 
 
	/*[begin] claim*/
    function actionMakeClaimPopup ($iEntryId) {
        parent::_actionMakeClaimPopup ($iEntryId, _t('_modzzz_schools_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', modzzz_schools_MAX_FANS);
    }

    function _actionMakeClaimPopup ($iEntryId, $sTitle, $sFuncGetFans = 'getFans', $sFuncIsAllowedManageFans = 'isAllowedManageFans', $sFuncIsAllowedManageAdmins = 'isAllowedManageAdmins', $iMaxFans = 1000) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById ($iEntryId))) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }

        if (!$this->$sFuncIsAllowedManageFans($aDataEntry)) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Access denied')));
            exit;
        }

        $aProfiles = array ();
        $iNum = $this->_oDb->$sFuncGetFans($aProfiles, $iEntryId, true, 0, $iMaxFans);
        if (!$iNum) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }

        $sActionsUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "view/" . $aDataEntry[$this->_oDb->_sFieldUri] . '?ajax_action=';
        $aButtons = array (
            array (
                'type' => 'submit',
                'name' => 'fans_remove',
                'value' => _t('_sys_btn_fans_remove'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_fans_content', '{$sActionsUrl}remove&ids=' + sys_manage_items_get_manage_fans_ids()); return false;\"",
            ),
        );

        if ($this->$sFuncIsAllowedManageAdmins($aDataEntry)) {

            $aButtons = array_merge($aButtons, array (
                array (
                    'type' => 'submit',
                    'name' => 'fans_add_to_admins',
                    'value' => _t('_sys_btn_fans_add_to_admins'),
                    'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_fans_content', '{$sActionsUrl}add_to_admins&ids=' + sys_manage_items_get_manage_fans_ids()); return false;\"",
                ),
                array (
                    'type' => 'submit',
                    'name' => 'fans_move_admins_to_fans',
                    'value' => _t('_sys_btn_fans_move_admins_to_fans'),
                    'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_fans_content', '{$sActionsUrl}admins_to_fans&ids=' + sys_manage_items_get_manage_fans_ids()); return false;\"",
                ),            
            ));
        };
        bx_import ('BxTemplSearchResult');
        $sControl = BxTemplSearchResult::showAdminActionsPanel('sys_manage_items_manage_fans', $aButtons, 'sys_fan_unit');

        $aVarsContent = array (            
            'suffix' => 'manage_fans',
            'content' => $this->_profilesEdit($aProfiles, false, $aDataEntry),
            'control' => $sControl,
        );
        $aVarsPopup = array (
            'title' => $sTitle,
            'content' => $this->_oTemplate->parseHtmlByName('manage_items_form', $aVarsContent),
        );        
        echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true);
        exit;
    }
 
    function actionClaim ($iEntryId) {
        $this->_actionClaim ($iEntryId, 'modzzz_schools_claim', _t('_modzzz_schools_caption_make_claim'), _t('_modzzz_schools_claim_sent'), _t('_modzzz_schools_claim_not_sent'));
    }

    function _actionClaim ($iEntryId, $sEmailTemplate, $sTitle, $sMsgSuccess, $sMsgFail) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));

        bx_import ('ClaimForm', $this->_aModule);
		$oForm = new BxSchoolsClaimForm ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
 
			$aClaimer = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getClaimParams ($aDataEntry, $aClaimer);
		  
			$iRecipient = $aDataEntry['author_id'];
 
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;
  
			$bNewRequest = $this->_oDb->saveClaimRequest($iEntryId, $this->_iProfileId, $oForm->getCleanValue('claim_text'));

            // send message to administrator
            if ($bNewRequest && $oForm->getCleanValue('claim_text')) { 
				 
				$arrAdmins = $this->_oDb->getSiteAdmins();

				foreach($arrAdmins as $aEachAdmin) { 

					$iRecipient = (int)$aEachAdmin['ID'];
					$aRecipient = getProfileInfo($iRecipient); 

					$sSubject = str_replace("<NickName>",$aClaimer['NickName'], $aTemplate['Subject']);
					$sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $sSubject);

					$aPlus = array_merge (array ('RecipientName' => ' ' . $aRecipient['NickName']), $aPlusOriginal);

					$iSuccess += sendMail(trim($aRecipient['Email']), $sSubject, $aTemplate['Body'], $iRecipient, $aPlus) ? 1 : 0;  
				}
			}
			
            $sMsg = ($bNewRequest) ? $sMsgSuccess : $sMsgFail;
            echo MsgBox($sMsg);
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode ($sMsg, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle . ' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

    function _getClaimParams ($aDataEntry, $aClaimer) {
        return array (
                'ListTitle' => $aDataEntry['title'], 
                'ListUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'SenderLink' => $aClaimer ? getProfileLink($aClaimer['ID']) : 'javascript:void(0);',
                'SenderName' => $aClaimer ? $aClaimer['NickName'] : _t('_modzzz_schools_user_unknown'),
                'Message' => stripslashes(strip_tags($_REQUEST['claim_text'])),
            );        
    }

	function actionAdministrationClaims () {

        if ($_POST['action_assign'] && is_array($_POST['entry'])) {
 
            foreach ($_POST['entry'] as $iId) {  
                $this->_oDb->assignClaim($iId, $this->isAdmin()); 
            } 
        }

        if ($_POST['action_delete'] && is_array($_POST['entry'])) {
  
            foreach ($_POST['entry'] as $iId) { 
                $this->_oDb->deleteClaim($iId, $this->isAdmin()); 
            }
        }
 
		$sContent = $this->_manageClaims ('claim', '', true, 'bx_twig_admin_form', array(
 			'action_assign' => '_modzzz_schools_admin_assign',
 			'action_delete' => '_modzzz_schools_admin_delete',
		));
     
        return $sContent;
    }
 

    function _manageClaims ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 14, $bActionsPanel = true) {

        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = $sMode . '_admin';
 

        if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;

        $sPagination = $sActionsPanel = '';
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
        } elseif (!($sContent = $o->displayClaimResultBlock($sMode))) { 
            if ($isMsgBoxIfEmpty)
                $sContent = MsgBox(_t('_Empty'));
            else
                return '';
        } else { 
            $sPagination = $sAjaxPaginationBlockId ? $o->showPaginationAjax($sAjaxPaginationBlockId) : $o->showPagination();
			if($bActionsPanel)
				$sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons);
        }

        $aVars = array (
            'form_name' => $sFormName,
            'content' => $sContent,
            'pagination' => $sPagination,
            'filter_panel' => $isFilter ? $o->showAdminFilterPanel(false !== bx_get($this->_sFilterName) ? bx_get($this->_sFilterName) : '', 'filter_input_id', 'filter_checkbox_id', $this->_sFilterName) : '',
            'actions_panel' => $sActionsPanel,
        );        
        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }

    function isAllowedClaim (&$aDataEntry, $isPerformAction = false) {
		if ($this->isEntryAdmin($aDataEntry))
            return false;
  
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_MAKE_CLAIM, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    }  
	/*[end] claim*/
  
	/******[BEGIN] Instructors functions **************************/ 
    function isAllowedAddInstructor ($iSchoolId, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;

        if (!$GLOBALS['logged']['member']) 
            return false;

        if ($this->_oDb->isInstructor($iSchoolId, $this->_iProfileId)) 
            return false;
 
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_ADD_INSTRUCTOR, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function actionInstructors ($sAction, $sInstructorsIdUri, $sExtraParam='') {
		switch($sAction){
			case 'add': 
				$this->actionInstructorsAdd ($sInstructorsIdUri, '_modzzz_schools_page_title_instructors_add', $sExtraParam);
			break;
			case 'edit':
				$this->actionInstructorsEdit ($sInstructorsIdUri, '_modzzz_schools_page_title_instructors_edit');
			break;
			case 'delete':
				$this->actionInstructorsDelete ($sInstructorsIdUri, _t('_modzzz_schools_msg_school_instructor_was_deleted'));
			break;
			case 'view':
				$this->actionInstructorsView ($sInstructorsIdUri, _t('_modzzz_schools_msg_pending_instructors_approval')); 
			break; 
			case 'browse':
				return $this->actionInstructorsBrowse ($sInstructorsIdUri, '_modzzz_schools_page_title_instructors_browse'); 
			break;  
		}
	}
    
    function actionInstructorsBrowse ($sUri, $sTitle) {
      
        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_schools_menu_view_instructors') => '',
        ));

        bx_import ('InstructorsPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'InstructorsPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]), false, false);  
    }
 
    function actionInstructorsView ($sUri, $sMsgPendingApproval) {

		$aInstructorsEntry = $this->_oDb->getInstructorsEntryByUri($sUri);
		$iEntryId = (int)$aInstructorsEntry['school_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aInstructorsEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aInstructorsEntry[$this->_oDb->_sFieldTitle] => '',
        ));
 
        if ((!$this->_iProfileId || $aInstructorsEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableInstructors, $aInstructorsEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   
 
        $this->_oTemplate->pageStart();

        bx_import ('InstructorsPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'InstructorsPageView';
        $oPage = new $sClass ($this, $aInstructorsEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('InstructorsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'InstructorsCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aInstructorsEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aInstructorsEntry['title'], false, false); 
    }
 
    function actionInstructorsEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aInstructorsEntry = $this->_oDb->getInstructorsEntryById($iEntryId);
		$iInstructorsId = (int)$aInstructorsEntry['school_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iInstructorsId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aInstructorsEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aInstructorsEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aInstructorsEntry[$this->_oDb->_sFieldTitle] => '',
        ));

        bx_import ('InstructorsFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'InstructorsFormEdit';
        $oForm = new $sClass ($this, $aInstructorsEntry['uri'], $iInstructorsId,  $iEntryId, $aInstructorsEntry);
  
        $oForm->initChecker($aInstructorsEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'instructors_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableInstructorsMediaPrefix;
			
			$aValsAdd = array();
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
     
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'instructors/view/' . $aInstructorsEntry['uri']);
                exit;

            } else {

                echo MsgBox(_t('_Error Occured'));
                
            }            

        } else {

            echo $oForm->getCode ();

        }

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aInstructorsEntry['title']));  
    }

    function actionInstructorsDelete ($iInstructorId, $sMsgSuccess) {

		$aInstructorEntry = $this->_oDb->getInstructorsEntryById($iInstructorId);
		$iSchoolId = (int)$aInstructorEntry['school_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iSchoolId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iInstructorId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete ($aDataEntry, $aInstructorEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iInstructorId, 'ajaxy_popup_result_div');
            exit;
        }
 
        if ($this->_oDb->deleteInstructorsByIdAndOwner($iInstructorId, $iSchoolId, $this->_iProfileId, $this->isAdmin())) {
 
 			$this->onEventSubItemDeleted ('instructor', $iInstructorId, $iSchoolId, $aDataEntry);

            $this->onEventInstructorsDeleted ($iInstructorId, $aInstructorEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
 
            $sJQueryJS = genAjaxyPopupJS($iInstructorId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS; 
            exit;
        }
 
        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iInstructorId, 'ajaxy_popup_result_div');
        exit;
    }
  
    function actionInstructorsAdd ($iSchoolId, $sTitle, $sExtraParam='') {
  
		if (!($aDataEntry = $this->_oDb->getEntryById($iSchoolId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],  
        ));

        if ($sExtraParam=='exist' || $_POST['submit_form']) {
 
			if (!$this->isAllowedAddInstructor($iSchoolId)) {
				$this->_oTemplate->displayAccessDenied ();
				return;
			} 
 
			$this->_addInstructorsForm($iSchoolId, 0);

		}else{
			if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) {
				$this->_oTemplate->displayAccessDenied ();
				return;
			}

			bx_import ('InstructorFormSelect', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'InstructorFormSelect';
			$oForm = new $sClass ($this);
			$oForm->initChecker();

			if ($oForm->isSubmittedAndValid ()) {
				$iProfileId = $this->_oDb->getProfileId($oForm->getCleanValue('profile_nick'));
				
				if($oForm->getCleanValue('type')=='internal' && !$iProfileId){
					echo MsgBox(_t('_modzzz_schools_msg_invalid_instructor')) . $oForm->getCode (); 
				}else{
					$this->_addInstructorsForm($iSchoolId, $iProfileId);
				}
			} else { 

				$sPageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]; 
				$aVars = array ('url' => $sPageUrl); 
				
				echo $this->_oTemplate->parseHtmlByName ('autocomplete_js', $aVars);
				echo $oForm->getCode (); 
			}
 
		}
 
        $this->_oTemplate->addJs (array('main.js',BX_DOL_URL_PLUGINS . 'jquery/jquery.autocomplete.js'));
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]));  
    }
   
    function _addInstructorsForm ($iSchoolId, $iProfileId=0) { 
 
        bx_import ('InstructorsFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'InstructorsFormAdd';
        $oForm = new $sClass ($this, $iProfileId, $iSchoolId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'instructors_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableInstructorsMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
			$sType = $oForm->getCleanValue('type');
			if($sType=='site'){
				$sNickName = $oForm->getCleanValue('title');
				$iProfileId = getID($sNickName);

				if (!$iProfileId) {
					echo MsgBox(_t('_modzzz_schools_form_instructor_invalid_instructor')) . $oForm->getCode (); 
					return;
				} 

				$aValsAdd['profile_id'] = $iProfileId;
			}
 
			$iEntryId = $oForm->insert ($aValsAdd);
 
			if ($iEntryId) {
				  
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
				$aDataEntry = $this->_oDb->getInstructorsEntryById($iEntryId);
	 
				$this->onEventSubItemCreate ('instructor', $iEntryId, $iSchoolId, $aDataEntry);
 
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'instructors/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
				header ('Location:' . $sRedirectUrl);
				exit; 
			} else { 
				MsgBox(_t('_Error Occured'));
			}  
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
  
    function onEventInstructorsDeleted ($iEntryId, $aDataEntry = array()) {
 
        // delete votings
        bx_import('InstructorsVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'InstructorsVoting';
        $oVoting = new $sClass ($this->_oDb->_sInstructorsPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('InstructorsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'InstructorsCmts';
        $oCmts = new $sClass ($this->_oDb->_sInstructorsPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sInstructorsPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    /*******[END - Instructors Functions] ******************************/

	/******[BEGIN] Courses functions **************************/ 
    function actionCourses ($sAction, $sCoursesIdUri) {
		switch($sAction){
			case 'add': 
				$this->actionCoursesAdd ($sCoursesIdUri, '_modzzz_schools_page_title_courses_add');
			break;
			case 'edit':
				$this->actionCoursesEdit ($sCoursesIdUri, '_modzzz_schools_page_title_courses_edit');
			break;
			case 'delete':
				$this->actionCoursesDelete ($sCoursesIdUri, _t('_modzzz_schools_msg_school_course_was_deleted'));
			break;
			case 'view':
				$this->actionCoursesView ($sCoursesIdUri, _t('_modzzz_schools_msg_pending_courses_approval')); 
			break; 
			case 'browse':
				return $this->actionCoursesBrowse ($sCoursesIdUri, '_modzzz_schools_page_title_courses_browse'); 
			break;  
		}
	}
    
    function actionCoursesBrowse ($sUri, $sTitle) {
      
        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_schools_menu_view_courses') => '',
        ));

        bx_import ('CoursesPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'CoursesPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]), false, false);  
    }
 
    function actionCoursesView ($sUri, $sMsgPendingApproval) {

		$aCourseEntry = $this->_oDb->getCoursesEntryByUri($sUri);
		$iEntryId = (int)$aCourseEntry['school_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aCourseEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aCourseEntry[$this->_oDb->_sFieldTitle] => '',
        ));

        if ((!$this->_iProfileId || $aCourseEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableCourses, $aCourseEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   
 
        $this->_oTemplate->pageStart();

        bx_import ('CoursesPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'CoursesPageView';
        $oPage = new $sClass ($this, $aCourseEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('CoursesCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'CoursesCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aCourseEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aCourseEntry['title'], false, false); 
    }
 
    function actionCoursesEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aCourseEntry = $this->_oDb->getCoursesEntryById($iEntryId);
		$iCourseId = (int)$aCourseEntry['school_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iCourseId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aCourseEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aCourseEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aCourseEntry[$this->_oDb->_sFieldTitle] => '',
        ));

        bx_import ('CoursesFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'CoursesFormEdit';
        $oForm = new $sClass ($this, $aCourseEntry['uri'], $iCourseId,  $iEntryId, $aCourseEntry);
  
        $oForm->initChecker($aCourseEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'courses_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableCoursesMediaPrefix;
			
			$aValsAdd = array();
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'courses/view/' . $aCourseEntry['uri']);
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        }

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aCourseEntry['title']));  
    }

    function actionCoursesDelete ($iCourseId, $sMsgSuccess) {

		$aCourseEntry = $this->_oDb->getCoursesEntryById($iCourseId);
		$iSchoolId = (int)$aCourseEntry['school_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iSchoolId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iCourseId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete ($aDataEntry, $aCourseEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iCourseId, 'ajaxy_popup_result_div');
            exit;
        }
 
        if ($this->_oDb->deleteCoursesByIdAndOwner($iCourseId, $iSchoolId, $this->_iProfileId, $this->isAdmin())) {
 
 			$this->onEventSubItemDeleted ('course', $iCourseId, $iSchoolId, $aDataEntry);

            $this->onEventCoursesDeleted ($iCourseId, $aCourseEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
 
            $sJQueryJS = genAjaxyPopupJS($iCourseId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS; 
            exit;
        }
 
        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iCourseId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionCoursesAdd ($iCourseId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iCourseId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],  
        ));

        $this->_addCoursesForm($iCourseId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]));  
    }
 
    function _addCoursesForm ($iSchoolId) { 
 
        bx_import ('CoursesFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'CoursesFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iSchoolId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'courses_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableCoursesMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
  
			$iEntryId = $oForm->insert ($aValsAdd);
 
			if ($iEntryId) {
				  
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
				$aDataEntry = $this->_oDb->getCoursesEntryById($iEntryId);
	
				$this->onEventSubItemCreate ('course', $iEntryId, $iSchoolId, $aDataEntry);
 
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'courses/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
				header ('Location:' . $sRedirectUrl);
				exit; 
			} else { 
				MsgBox(_t('_Error Occured'));
			}  
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventCoursesDeleted ($iEntryId, $aDataEntry = array()) {
 
        // delete votings
        bx_import('CoursesVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'CoursesVoting';
        $oVoting = new $sClass ($this->_oDb->_sCoursesPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('CoursesCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'CoursesCmts';
        $oCmts = new $sClass ($this->_oDb->_sCoursesPrefix, $iEntryId);
        $oCmts->onObjectDelete ();
 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    /*******[END - Courses Functions] ******************************/
	
	
	/******[BEGIN] Events functions **************************/ 


    function serviceEventMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('schools_event', array(
            'part' => 'schools_event',
            'title' => '_modzzz_schools_event',
            'title_singular' => '_modzzz_schools_event_single',
            'icon' => 'modules/modzzz/schools/|map_marker.png',
            'icon_site' => 'calendar',
            'join_table' => 'modzzz_schools_events_main',
            'join_where' => "AND `p`.`status` = 'approved'",
            'join_field_id' => 'id',
            'join_field_country' => 'country',
            'join_field_city' => 'city',
            'join_field_state' => 'state',
            'join_field_zip' => 'zip',
            'join_field_address' => 'address1',
            'join_field_title' => 'title',
            'join_field_uri' => 'uri',
            'join_field_author' => 'author_id',
            'join_field_privacy' => 'allow_view_to',
            'permalink' => 'modules/?r=schools/event/view/',
        )));
    }
  
    function actionEvents ($sAction, $sEventsIdUri) {
		switch($sAction){
			case 'add': 
				$this->actionEventsAdd ($sEventsIdUri, '_modzzz_schools_page_title_events_add');
			break;
			case 'edit':
				$this->actionEventsEdit ($sEventsIdUri, '_modzzz_schools_page_title_events_edit');
			break;
			case 'delete':
				$this->actionEventsDelete ($sEventsIdUri, _t('_modzzz_schools_msg_school_event_was_deleted'));
			break;
			case 'view':
				$this->actionEventsView ($sEventsIdUri, _t('_modzzz_schools_msg_pending_events_approval')); 
			break; 
			case 'browse':
				return $this->actionEventsBrowse ($sEventsIdUri, '_modzzz_schools_page_title_events_browse'); 
			break;  
		}
	}
    
    function actionEventsBrowse ($sUri, $sTitle) {
      
        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_schools_menu_view_events') => '',
        ));

        bx_import ('EventsPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventsPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]), false, false);  
    }
 
    function actionEventsView ($sUri, $sMsgPendingApproval) {

		$aEventEntry = $this->_oDb->getEventsEntryByUri($sUri);
		$iEntryId = (int)$aEventEntry['school_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aEventEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);


        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aEventEntry[$this->_oDb->_sFieldTitle] => '',
        ));

        if ((!$this->_iProfileId || $aEventEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableEvents, $aEventEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   
 
        $this->_oTemplate->pageStart();

        bx_import ('EventsPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventsPageView';
        $oPage = new $sClass ($this, $aEventEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('EventsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventsCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aEventEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->addCss ('unit_fan.css');

        $this->_oTemplate->pageCode($aEventEntry['title'], false, false); 
    }
 
    function actionEventsEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aEventEntry = $this->_oDb->getEventsEntryById($iEntryId);
		$iEventId = (int)$aEventEntry['school_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iEventId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aEventEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aEventEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aEventEntry[$this->_oDb->_sFieldTitle] => '',
        ));

        bx_import ('EventsFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventsFormEdit';
        $oForm = new $sClass ($this, $aEventEntry['uri'], $iEventId,  $iEntryId, $aEventEntry);
  
        $oForm->initChecker($aEventEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'events_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventsMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
   
  				$this->onEventSubItemChanged ('event', $iEntryId, $sStatus, $aDataEntry);
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'events/view/' . $aEventEntry['uri']);
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        }

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aEventEntry['title']));  
    }

    function actionEventsDelete ($iEventId, $sMsgSuccess) {

		$aEventEntry = $this->_oDb->getEventsEntryById($iEventId);
		$iSchoolId = (int)$aEventEntry['school_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iSchoolId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete ($aDataEntry, $aEventEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
            exit;
        }
 
        if ($this->_oDb->deleteEventsByIdAndOwner($iEventId, $iSchoolId, $this->_iProfileId, $this->isAdmin())) {
  
 			$this->onEventSubItemDeleted ('event', $iEventId, $iSchoolId, $aDataEntry);

            $this->onEventEventsDeleted ($iEventId, $aEventEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
 
            $sJQueryJS = genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS; 
            exit;
        }
 
        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionEventsAdd ($iSchoolId, $sTitle) {
 
		//[begin] event integration - modzzz
		if(getParam('modzzz_schools_boonex_events')=='on'){ 
			$oEvent = BxDolModule::getInstance('BxEventsModule');
			$sRedirectUrl = BX_DOL_URL_ROOT . $oEvent->_oConfig->getBaseUri() . 'browse/my&bx_events_filter=add_event&school=' . $iSchoolId;
		  
			header ('Location:' . $sRedirectUrl);
			exit;
		}
 		//[end] event integration - modzzz


        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iSchoolId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],  
        ));
 

        $this->_addEventsForm($iSchoolId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]));  
    }
 
    function _addEventsForm ($iSchoolId) { 
 
        bx_import ('EventsFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventsFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iSchoolId);
        $oForm->initChecker();
 
        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'events_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventsMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
			$iEntryId = $oForm->insert ($aValsAdd);
 
			if ($iEntryId) {
				 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
				$aDataEntry = $this->_oDb->getEventsEntryById($iEntryId);
	 
				$this->onEventSubItemCreate ('event', $iEntryId, $iSchoolId, $aDataEntry);
 
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'events/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
				header ('Location:' . $sRedirectUrl);
				exit; 
			} else { 
				MsgBox(_t('_Error Occured'));
			}  
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 
    function onEventEventsDeleted ($iEntryId, $aDataEntry = array()) {
 
        // delete votings
        bx_import('EventsVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventsVoting';
        $oVoting = new $sClass ($this->_oDb->_sEventsPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('EventsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventsCmts';
        $oCmts = new $sClass ($this->_oDb->_sEventsPrefix, $iEntryId);
        $oCmts->onObjectDelete ();
 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    /*******[END - Events Functions] ******************************/
 

	/******[BEGIN] Student functions **************************/ 

    function isAllowedAddStudent ($iSchoolId, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;

        if (!$GLOBALS['logged']['member']) 
            return false;

        if ($this->_oDb->isStudent($iSchoolId, $this->_iProfileId)) 
            return false;
 
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_ADD_STUDENT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function actionAlumni ($sAction, $sStudentIdUri) {
		switch($sAction){ 
			case 'browse':
				return $this->actionAlumniBrowse ($sStudentIdUri, '_modzzz_schools_page_title_alumni_browse'); 
			break;  
		}
	}
 
    function actionStudent ($sAction, $sStudentIdUri, $sExtraParam='') {
		switch($sAction){
			case 'add': 
				$this->actionStudentAdd ($sStudentIdUri, '_modzzz_schools_page_title_student_add', $sExtraParam);
			break;
			case 'edit':
				$this->actionStudentEdit ($sStudentIdUri, '_modzzz_schools_page_title_student_edit');
			break;
			case 'delete':
				$this->actionStudentDelete ($sStudentIdUri, _t('_modzzz_schools_msg_school_student_was_deleted'));
			break;
			case 'view':
				$this->actionStudentView ($sStudentIdUri, _t('_modzzz_schools_msg_pending_student_approval')); 
			break; 
			case 'browse':
				return $this->actionStudentBrowse ($sStudentIdUri, '_modzzz_schools_page_title_student_browse'); 
			break;  
		}
	}
        
	function actionAlumniBrowse ($sUri, $sTitle) {
      
        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_schools_menu_view_student') => '',
        ));

        bx_import ('AlumniPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'AlumniPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]), false, false);  
    }

    function actionStudentBrowse ($sUri, $sTitle) {
      
        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_schools_menu_view_student') => '',
        ));

        bx_import ('StudentPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StudentPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]), false, false);  
    }
 
    function actionStudentView ($sUri, $sMsgPendingApproval) {

		$aStudentEntry = $this->_oDb->getStudentEntryByUri($sUri);
		$iEntryId = (int)$aStudentEntry['school_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aStudentEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aStudentEntry[$this->_oDb->_sFieldTitle] => '',
        ));

        if ((!$this->_iProfileId || $aStudentEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableStudent, $aStudentEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   
 
        $this->_oTemplate->pageStart();

        bx_import ('StudentPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StudentPageView';
        $oPage = new $sClass ($this, $aStudentEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('StudentCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StudentCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aStudentEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aStudentEntry['title'], false, false); 
    }
 
    function actionStudentEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aStudentEntry = $this->_oDb->getStudentEntryById($iEntryId);
		$iStudentId = (int)$aStudentEntry['school_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iStudentId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aStudentEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aStudentEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aStudentEntry[$this->_oDb->_sFieldTitle] => '',
        ));

        bx_import ('StudentFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StudentFormEdit';
        $oForm = new $sClass ($this, $aStudentEntry['uri'], $iStudentId,  $iEntryId, $aStudentEntry);
  
        $oForm->initChecker($aStudentEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'student_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableStudentMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
    
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'student/view/' . $aStudentEntry['uri']);
                exit;

            } else {

                echo MsgBox(_t('_Error Occured'));
                
            }            

        } else {

            echo $oForm->getCode ();

        }

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aStudentEntry['title']));  
    }

    function actionStudentDelete ($iStudentId, $sMsgSuccess) {

		$aStudentEntry = $this->_oDb->getStudentEntryById($iStudentId);
		$iSchoolId = (int)$aStudentEntry['school_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iSchoolId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iStudentId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete ($aDataEntry, $aStudentEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iStudentId, 'ajaxy_popup_result_div');
            exit;
        }
 
        if ($this->_oDb->deleteStudentByIdAndOwner($iStudentId, $iSchoolId, $this->_iProfileId, $this->isAdmin())) {
           
			if($aDataEntry['membership_type']=='student')
				$this->onEventSubItemDeleted ('student', $iStudentId, $iSchoolId, $aDataEntry);
			else
				$this->onEventSubItemDeleted ('alumni', $iStudentId, $iSchoolId, $aDataEntry);

            $this->onEventStudentDeleted ($iStudentId, $aStudentEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
 
            $sJQueryJS = genAjaxyPopupJS($iStudentId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS; 
            exit;
        }
 
        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iStudentId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionStudentAdd ($iSchoolId, $sTitle, $sExtraParam='') {
  
		if (!($aDataEntry = $this->_oDb->getEntryById($iSchoolId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],  
        ));

        if ($sExtraParam=='exist' || $_POST['submit_form']) {
 
			if (!$this->isAllowedAddStudent($iSchoolId)) {
				$this->_oTemplate->displayAccessDenied ();
				return;
			} 
 
			$this->_addStudentForm($iSchoolId, 0);

		}else{
			if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) {
				$this->_oTemplate->displayAccessDenied ();
				return;
			}

			bx_import ('StudentFormSelect', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'StudentFormSelect';
			$oForm = new $sClass ($this);
			$oForm->initChecker();

			if ($oForm->isSubmittedAndValid ()) {
				$iProfileId = $this->_oDb->getProfileId($oForm->getCleanValue('profile_nick'));
				
				if($oForm->getCleanValue('type')=='internal' && !$iProfileId){
					echo MsgBox(_t('_modzzz_schools_msg_invalid_student')) . $oForm->getCode (); 
				}else{
					$this->_addStudentForm($iSchoolId, $iProfileId);
				}
			} else { 

				$sPageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]; 
				$aVars = array ('url' => $sPageUrl); 
				
				echo $this->_oTemplate->parseHtmlByName ('autocomplete_js', $aVars);
				echo $oForm->getCode (); 
			}
 
		}
 
        $this->_oTemplate->addJs (array('main.js',BX_DOL_URL_PLUGINS . 'jquery/jquery.autocomplete.js'));
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]));  
    }
 
    function _addStudentForm ($iSchoolId, $iProfileId) { 
 
        bx_import ('StudentFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StudentFormAdd';
        $oForm = new $sClass ($this, $iProfileId, $iSchoolId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'student_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableStudentMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
  
			$iEntryId = $oForm->insert ($aValsAdd);
 
			if ($iEntryId) {
		 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
				$aDataEntry = $this->_oDb->getStudentEntryById($iEntryId);
	
				if($aDataEntry['membership_type']=='student') 
					$this->onEventSubItemCreate ('student', $iEntryId, $iSchoolId, $aDataEntry);
				else
					$this->onEventSubItemCreate ('alumni', $iEntryId, $iSchoolId, $aDataEntry);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'student/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
				header ('Location:' . $sRedirectUrl);
				exit; 
			} else { 
				MsgBox(_t('_Error Occured'));
			}  
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
  
    function onEventStudentDeleted ($iEntryId, $aDataEntry = array()) {
 
        // delete votings
        bx_import('StudentVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StudentVoting';
        $oVoting = new $sClass ($this->_oDb->_sStudentPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('StudentCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StudentCmts';
        $oCmts = new $sClass ($this->_oDb->_sStudentPrefix, $iEntryId);
        $oCmts->onObjectDelete ();
 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    /*******[END - Student Functions] ******************************/
 
	/******[BEGIN] News functions **************************/ 
    function actionNews ($sAction, $sNewsIdUri) {
		switch($sAction){
			case 'add': 
				$this->actionNewsAdd ($sNewsIdUri, '_modzzz_schools_page_title_news_add');
			break;
			case 'edit':
				$this->actionNewsEdit ($sNewsIdUri, '_modzzz_schools_page_title_news_edit');
			break;
			case 'delete':
				$this->actionNewsDelete ($sNewsIdUri, _t('_modzzz_schools_msg_school_news_was_deleted'));
			break;
			case 'view':
				$this->actionNewsView ($sNewsIdUri, _t('_modzzz_schools_msg_pending_news_approval')); 
			break; 
			case 'browse':
				return $this->actionNewsBrowse ($sNewsIdUri, '_modzzz_schools_page_title_news_browse'); 
			break;  
		}
	}
    
    function actionNewsBrowse ($sUri, $sTitle) {
      
        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_schools_menu_view_news') => '',
        ));

        bx_import ('NewsPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]), false, false);  
    }
 
    function actionNewsView ($sUri, $sMsgPendingApproval) {

		$aNewsEntry = $this->_oDb->getNewsEntryByUri($sUri);
		$iEntryId = (int)$aNewsEntry['school_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aNewsEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aNewsEntry[$this->_oDb->_sFieldTitle] => '',
        ));

        if ((!$this->_iProfileId || $aNewsEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableNews, $aNewsEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   
 
        $this->_oTemplate->pageStart();

        bx_import ('NewsPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsPageView';
        $oPage = new $sClass ($this, $aNewsEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('NewsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aNewsEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aNewsEntry['title'], false, false); 
    }
 
    function actionNewsEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aNewsEntry = $this->_oDb->getNewsEntryById($iEntryId);
		$iNewsId = (int)$aNewsEntry['school_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iNewsId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aNewsEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aNewsEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aNewsEntry[$this->_oDb->_sFieldTitle] => '',
        ));

        bx_import ('NewsFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsFormEdit';
        $oForm = new $sClass ($this, $aNewsEntry['uri'], $iNewsId,  $iEntryId, $aNewsEntry);
  
        $oForm->initChecker($aNewsEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'news_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
    
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/view/' . $aNewsEntry['uri']);
                exit;

            } else {

                echo MsgBox(_t('_Error Occured'));
                
            }            

        } else {

            echo $oForm->getCode ();

        }

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aNewsEntry['title']));  
    }

    function actionNewsDelete ($iNewsId, $sMsgSuccess) {

		$aNewsEntry = $this->_oDb->getNewsEntryById($iNewsId);
		$iSchoolId = (int)$aNewsEntry['school_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iSchoolId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete ($aDataEntry, $aNewsEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
            exit;
        }
 
        if ($this->_oDb->deleteNewsByIdAndOwner($iNewsId, $iSchoolId, $this->_iProfileId, $this->isAdmin())) {  
			$this->onEventSubItemDeleted ('news', $iNewsId, $iSchoolId, $aDataEntry);
 
            $this->onEventNewsDeleted ($iNewsId, $aNewsEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
 
            $sJQueryJS = genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS; 
            exit;
        }
 
        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionNewsAdd ($iNewsId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iNewsId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],  
        ));

        $this->_addNewsForm($iNewsId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]));  
    }
 
    function _addNewsForm ($iSchoolId) { 
 
        bx_import ('NewsFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iSchoolId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'news_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
  
			$iEntryId = $oForm->insert ($aValsAdd);
 
			if ($iEntryId) {
	 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
				$aDataEntry = $this->_oDb->getNewsEntryById($iEntryId);
	
				$this->onEventSubItemCreate ('news', $iEntryId, $iSchoolId, $aDataEntry);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
				header ('Location:' . $sRedirectUrl);
				exit; 
			} else { 
				MsgBox(_t('_Error Occured'));
			}  
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
  
    function onEventNewsDeleted ($iEntryId, $aDataEntry = array()) {
   
        // delete votings
        bx_import('NewsVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsVoting';
        $oVoting = new $sClass ($this->_oDb->_sNewsPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('NewsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsCmts';
        $oCmts = new $sClass ($this->_oDb->_sNewsPrefix, $iEntryId);
        $oCmts->onObjectDelete ();
 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    /*******[END - News Functions] ******************************/
  
    function onEventDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        $this->reparseTags ($iEntryId);
        $this->reparseCategories ($iEntryId);

        // delete votings
        bx_import('Voting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Voting';
        $oVoting = new $sClass ($this->_sPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass ($this->_sPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        bx_import ('BxDolViews');
        $oViews = new BxDolViews($this->_sPrefix, $iEntryId, false);
        $oViews->onObjectDelete();

        // delete forum
        $this->_oDb->deleteForum ($iEntryId);

		//[begin] delete instructors 
		$aInstructor = $this->_oDb->getAllSubItems('instructors', $iEntryId);
		foreach($aInstructor as $aEachInstructor){
			
			$iId = (int)$aEachInstructor['id'];

			// delete votings
			bx_import('InstructorsVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'InstructorsVoting';
			$oVoting = new $sClass ($this->_oDb->_sInstructorsPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);

			// delete comments 
			bx_import('InstructorsCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'InstructorsCmts';
			$oCmts = new $sClass ($this->_oDb->_sInstructorsPrefix, $iId);
			$oCmts->onObjectDelete ();
		} 
 
		$this->_oDb->deleteInstructors($iEntryId,  $this->_iProfileId, $this->isAdmin());
 		//[end] delete instructors 
 
		//[begin] delete courses 
		$aCourse = $this->_oDb->getAllSubItems('courses', $iEntryId);
		foreach($aCourse as $aEachCourse){
			
			$iId = (int)$aEachCourse['id'];

			// delete votings
			bx_import('CoursesVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'CoursesVoting';
			$oVoting = new $sClass ($this->_oDb->_sCoursesPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);

			// delete comments 
			bx_import('CoursesCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'CoursesCmts';
			$oCmts = new $sClass ($this->_oDb->_sCoursesPrefix, $iId);
			$oCmts->onObjectDelete ();
		} 
 
		$this->_oDb->deleteCourses($iEntryId,  $this->_iProfileId, $this->isAdmin());
 		//[end] delete courses 
 
		//[begin] delete student 
		$aStudent = $this->_oDb->getAllSubItems('student', $iEntryId);
		foreach($aStudent as $aEachStudent){
			
			$iId = (int)$aEachStudent['id'];

			// delete votings
			bx_import('StudentVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'StudentVoting';
			$oVoting = new $sClass ($this->_oDb->_sStudentPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);

			// delete comments 
			bx_import('StudentCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'StudentCmts';
			$oCmts = new $sClass ($this->_oDb->_sStudentPrefix, $iId);
			$oCmts->onObjectDelete ();
		} 
 
		$this->_oDb->deleteStudent($iEntryId,  $this->_iProfileId, $this->isAdmin());
 		//[end] delete student 

		//[begin] delete news 
		$aNews = $this->_oDb->getAllSubItems('news', $iEntryId);
		foreach($aNews as $aEachNews){
			
			$iId = (int)$aEachNews['id'];

			// delete votings
			bx_import('NewsVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'NewsVoting';
			$oVoting = new $sClass ($this->_oDb->_sNewsPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);

			// delete comments 
			bx_import('NewsCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'NewsCmts';
			$oCmts = new $sClass ($this->_oDb->_sNewsPrefix, $iId);
			$oCmts->onObjectDelete ();
		} 
 
		$this->_oDb->deleteNews($iEntryId,  $this->_iProfileId, $this->isAdmin());
 		//[end] delete news 

		//[begin] delete events  
		if(getParam('modzzz_schools_boonex_events')=='on'){ 
			$oEvent = BxDolModule::getInstance('BxEventsModule'); 
			$aEvent = $this->_oDb->getBoonexEvents($iEntryId);
			foreach($aEvent as $aEachEvent){ 
				if ($oEvent->_oDb->deleteEntryByIdAndOwner($aEachEvent['ID'], 0, 0)) {
					$oEvent->isAllowedDelete($aEachEvent, true); // perform action
					$oEvent->onEventDeleted ($aEachEvent['ID'], $aEachEvent);
				} 
			} 
		}else{

			$aEvent = $this->_oDb->getAllSubItems('events', $iEntryId);
			foreach($aEvent as $aEachEvent){
				
				$iId = (int)$aEachEvent['id'];

				BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri().'_event', $iId)); 
	 
				// delete votings
				bx_import('EventsVoting', $this->_aModule);
				$sClass = $this->_aModule['class_prefix'] . 'EventsVoting';
				$oVoting = new $sClass ($this->_oDb->_sEventsPrefix, 0, 0);
				$oVoting->deleteVotings ($iId);

				// delete comments 
				bx_import('EventsCmts', $this->_aModule);
				$sClass = $this->_aModule['class_prefix'] . 'EventsCmts';
				$oCmts = new $sClass ($this->_oDb->_sEventsPrefix, $iId);
				$oCmts->onObjectDelete ();
			} 
	 
			$this->_oDb->deleteEvents($iEntryId,  $this->_iProfileId, $this->isAdmin());
		}
 		//[end] delete events 

		// delete associated locations
		if (BxDolModule::getInstance('BxWmapModule')){ 
			BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri(), $iEntryId));  
		}
 
        // arise alert
		$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		$oAlert->alert();
    }       
	
    function isAllowedViewSubProfile ($sTable, $aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_VIEW_SCHOOL, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
  
        $this->_oSubPrivacy = new BxSchoolsSubPrivacy($this, $sTable); 
	    return $this->_oSubPrivacy->check('view', $aDataEntry['id'], $this->_iProfileId); 
    }

    function isAllowedRateSubProfile($sTable, &$aDataEntry) {       
        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;
        
		$this->_oSubPrivacy = new BxSchoolsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('rate', $aDataEntry['id'], $this->_iProfileId);        
    }

    function isAllowedCommentsSubProfile($sTable, &$aDataEntry) {
    
        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;

        $this->_oSubPrivacy = new BxSchoolsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('comment', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadPhotosSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        
		$this->_oSubPrivacy = new BxSchoolsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function actionUploadPhotosSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadPhotosSubProfile', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_schools_page_title_upload_photos'));
    }

    function _actionUploadMediaSubProfile ($sType, $sUri, $sIsAllowedFuncName, $sMedia, $aMediaFields, $sTitle) {
   
		switch($sType){ 
			case 'instructors':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableInstructorsMediaPrefix;
				$sTable = $this->_oDb->_sTableInstructors ;
				$sDataFuncName = 'getInstructorsEntryByUri';
			break; 
			case 'courses':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableCoursesMediaPrefix;
				$sTable = $this->_oDb->_sTableCourses ;
				$sDataFuncName = 'getCoursesEntryByUri';
			break; 
			case 'student':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableStudentMediaPrefix;
				$sTable = $this->_oDb->_sTableStudent ;
				$sDataFuncName = 'getStudentEntryByUri';
			break; 	
			case 'news':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix;
				$sTable = $this->_oDb->_sTableNews ;
				$sDataFuncName = 'getNewsEntryByUri';
			break; 		
			case 'events':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventsMediaPrefix;
				$sTable = $this->_oDb->_sTableEvents ;
				$sDataFuncName = 'getEventsEntryByUri';
			break; 
		}
 
        if (!($aDataEntry = $this->_oDb->$sDataFuncName($sUri)))
            return;

        if (!$this->$sIsAllowedFuncName($sTable, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $aSchoolEntry = $this->_oDb->getEntryById($aDataEntry['school_id']);
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aSchoolEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aSchoolEntry[$this->_oDb->_sFieldUri]);
  
        $iEntryId = $aDataEntry[$this->_oDb->_sFieldId];

        bx_import (ucwords($sType) . 'FormUploadMedia', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . ucwords($sType) . 'FormUploadMedia';
        $oForm = new $sClass ($this, $aDataEntry[$this->_oDb->_sFieldAuthorId],$aDataEntry['school_id'], $iEntryId, $aDataEntry, $sMedia, $aMediaFields);
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {

            $oForm->processMedia($iEntryId, $this->_iProfileId);

            $this->$sIsAllowedFuncName($sTable, $aDataEntry, true); // perform action

            header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . $sType . '/view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
            exit;

         } else {

            echo $oForm->getCode ();

        }

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');            
        $this->_oTemplate->pageCode($sTitle);
    }
 

	/* functions added for v7.1 */

    function serviceGetMemberMenuItemAddContent ()
    {
        if (!$this->isAllowedAdd())
            return '';
        return parent::_serviceGetMemberMenuItem (_t('_modzzz_schools_single'), _t('_modzzz_schools_single'), 'pencil', false, '&filter=add_school');
    }


    /**
     * Install map support
     */
    function serviceMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('schools', array(
            'part' => 'schools',
            'title' => '_modzzz_schools',
            'title_singular' => '_modzzz_schools_single',
            'icon' => 'modules/modzzz/schools/|map_marker.png',
            'icon_site' => 'pencil',
            'join_table' => 'modzzz_schools_main',
            'join_where' => "AND `p`.`status` = 'approved'",
            'join_field_id' => 'id',
            'join_field_country' => 'country',
            'join_field_city' => 'city',
            'join_field_state' => 'state',
            'join_field_zip' => 'zip',
            'join_field_address' => '',
            'join_field_title' => 'title',
            'join_field_uri' => 'uri',
            'join_field_author' => 'author_id',
            'join_field_privacy' => 'allow_view_school_to',
            'permalink' => 'modules/?r=schools/view/',
        )));
    }
 
	//remove old one first
    function serviceGetWallPost ($aEvent)
    {
        $aParams = array(
            'txt_object' => '_modzzz_schools_wall_object',
            'txt_added_new_single' => '_modzzz_schools_wall_added_new',
            'txt_added_new_plural' => '_modzzz_schools_wall_added_new_items',
            'txt_privacy_view_event' => 'view_school',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallPost ($aEvent, $aParams);
    }
 
    function serviceGetWallPostComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_school',
            'obj_privacy' => $this->_oPrivacy
        );
        return $this->_serviceGetWallPostComment($aEvent, $aParams);
    }

    function _serviceGetWallPostComment($aEvent, $aParams)
    {
        $iId = (int)$aEvent['object_id'];
        if(!$aParams['obj_privacy']->check($aParams['txt_privacy_view_event'], $iId, $this->_iProfileId))
            return '';

        $iOwner = (int)$aEvent['owner_id'];
        $sOwner = getNickName($iOwner);

        $aContent = unserialize($aEvent['content']);
        if(empty($aContent) || !isset($aContent['comment_id']))
            return '';

        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass($this->_sPrefix, $iId);
        if(!$oCmts->isEnabled())
            return '';

        $aItem = $this->_oDb->getEntryByIdAndOwner($iId, $iOwner, 1);
        $aComment = $oCmts->getCommentRow((int)$aContent['comment_id']);

        $sImage = '';
        if($aItem[$this->_oDb->_sFieldThumb]) {
            $a = array('ID' => $aItem[$this->_oDb->_sFieldAuthorId], 'Avatar' => $aItem[$this->_oDb->_sFieldThumb]);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        }

        $sCss = '';
        $sUri = $this->_oConfig->getUri();
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/';
        $sNoPhoto = $this->_oTemplate->getIconUrl('no-photo.png');
        if($aEvent['js_mode'])
            $sCss = $this->_oTemplate->addCss(array('wall_post.css', 'unit.css', 'twig.css'), true);
        else
            $this->_oTemplate->addCss(array('wall_post.css', 'unit.css', 'twig.css'));

        bx_import('Voting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Voting';
        $oVoting = new $sClass ($this->_sPrefix, 0, 0);

        $sTextAddedNew = _t('_modzzz_' . $sUri . '_wall_added_new_comment');
        $sTextWallObject = _t('_modzzz_' . $sUri . '_wall_object');
        $aTmplVars = array(
            'cpt_user_name' => $sOwner,
            'cpt_added_new' => $sTextAddedNew,
            'cpt_object' => $sTextWallObject,
            'cpt_item_url' => $sBaseUrl . $aItem[$this->_oDb->_sFieldUri],
            'cnt_comment_text' => $aComment['cmt_text'],
            'unit' => $this->_oTemplate->unit($aItem, 'unit', $oVoting),
            'post_id' => $aEvent['id'],
        );
        return array(
            'title' => $sOwner . ' ' . $sTextAddedNew . ' ' . $sTextWallObject,
            'description' => $aComment['cmt_text'],
            'content' => $sCss . $this->_oTemplate->parseHtmlByName('wall_post_comment', $aTmplVars)
        );
    }
  
    function serviceGetWallPostOutline($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_school',
            'obj_privacy' => $this->_oPrivacy,
            'templates' => array(
                'grouped' => 'wall_outline_grouped'
            )
        );
        return parent::_serviceGetWallPostOutline($aEvent, 'pencil', $aParams);
    }
 
    function _formatLocation (&$aDataEntry, $isCountryLink = false, $isFlag = false)
    {
        $sFlag = $isFlag ? ' ' . genFlag($aDataEntry['country']) : '';
        $sCountry = _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']);
        if ($isCountryLink)
            $sCountry = '<a href="' . $this->_oConfig->getBaseUri() . 'browse/country/' . strtolower($country['Country']) . '">' . $sCountry . '</a>';
        return (trim($aDataEntry['city']) ? $aDataEntry['city'] . ', ' : '') . $sCountry . $sFlag;
    }

    function _formatSnippetTextForOutline($aEntryData)
    {
        return $this->_oTemplate->parseHtmlByName('wall_outline_extra_info', array(
            'desc' => $this->_formatSnippetText($aEntryData, 200),
            'location' => $this->_formatLocation($aEntryData, false, false),
            'fans_count' => $aEntryData['fans_count'],
        ));
    }

    function _formatSnippetText ($aEntryData, $iMaxLen = 300, $sField='')
    {  $sField = ($sField) ? $sField : $aEntryData[$this->_oDb->_sFieldDescription];
        return strmaxtextlen($sField, $iMaxLen);
    }
 
    function isAllowedEmbed(&$aDataEntry) { 
        return ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)); 
    }
 
    function actionEmbed ($sUri) {
 
        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri], 
        ));


        bx_import ('EmbedForm', $this->_aModule);
		$oForm = new BxSchoolsEmbedForm ($this, $aDataEntry);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid()) {        
  
			$iEntryId = $aDataEntry[$this->_oDb->_sFieldId];
			
			$aYoutubes2Keep = array(); 
 			if( is_array($_POST['prev_video']) && count($_POST['prev_video'])){
 
				foreach ($_POST['prev_video'] as $iYoutubeId){
					$aYoutubes2Keep[$iYoutubeId] = $iYoutubeId;
				}
			}
				
			$aYoutubeIds = $this->_oDb->getYoutubeIds($iEntryId);
		
			$aDeletedYoutube = array_diff ($aYoutubeIds, $aYoutubes2Keep);

			if ($aDeletedYoutube) {
				foreach ($aDeletedYoutube as $iYoutubeId) {
					$this->_oDb->removeYoutube($iEntryId, $iYoutubeId);
				}
			} 
			 

			$this->_oDb->addYoutube($iEntryId);
 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			header ('Location:' . $sRedirectUrl);
            return;
        } 

        echo $oForm->getCode (); 
        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css'); 
        $this->_oTemplate->pageCode(_t('_modzzz_schools_page_title_embed_video') . $aDataEntry[$this->_oDb->_sFieldTitle]);
    }
	//[end modzzz] embed video modification



	//[begin modzzz] add student modification 
/*
    function actionAddStudent ($iEntryId) {
 
        $this->_oTemplate->pageStart();

        bx_import ('StudentForm', $this->_aModule);
		$oForm = new BxSchoolsStudentForm ($this, $iEntryId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
  
			$iEntryId = (int)$iEntryId;

			$aDataEntry = $this->_oDb->getEntryById($iEntryId);
			$sUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

			$sStudents = trim($oForm->getCleanValue('student'));
			if($sStudents){
				$aStudent = explode(',',$sStudents);
				foreach($aStudent as $sEachStudent){
					$sEachStudent = trim($sEachStudent);
					$iStudent = ($sEachStudent) ? getID($sEachStudent) : 0;
  
					if($iStudent && !$this->_oDb->isStudent($iEntryId, $iStudent)){
 						$this->_oDb->addStudent($iEntryId, $iStudent);
					}
				}
			}
 
			header ('Location:' . $sUrl);
			exit; 
        } 

        $sForm = $oForm->getCode ();
 
        $aVarsPopup = array (
            'title' => _t('_modzzz_schools_title_add_students'),
            'content' => $sForm,
        );        
        
		echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true); 
 	}

	//[end modzzz] add student modification
*/

    function onEventSubItemCreate ($sType, $iEntryId, $iSchoolId, $aDataEntry = array())
    {
        if ('event' == $sType) {
			if (BxDolModule::getInstance('BxWmapModule'))
				BxDolService::call('wmap', 'response_entry_add', array($this->_oConfig->getUri().'_event', $iEntryId)); 
        }

		$this->_oDb->updateItemCount($iSchoolId, $sType, '+');  
    }

    function onEventSubItemDeleted ($sType, $iEntryId, $iSchoolId, $aDataEntry = array())
    {
        if ('event' == $sType) {
			if (BxDolModule::getInstance('BxWmapModule'))
				BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri().'_event', $iEntryId)); 
		} 
 
		$this->_oDb->updateItemCount($iSchoolId, $sType, '-');  
    } 

    function onEventSubItemChanged ($sType, $iEntryId, $iSchoolId, $aDataEntry = array())
    {
        if ('event' == $sType) {
			if (BxDolModule::getInstance('BxWmapModule'))
				BxDolService::call('wmap', 'response_entry_change', array($this->_oConfig->getUri().'_event', $iEntryId)); 
		} 
    }

	//[BEGIN] EVENT FANS

    function onEventEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry)
    {
        $this->_onEventEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_schools_event_join_request', BX_SCHOOLS_MAX_FANS);
    }

    function onEventEventJoinReject ($iEntryId, $iProfileId, $aDataEntry)
    {
        $this->_onEventEventJoinReject ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_schools_event_join_reject');
    }

    function onEventEventFanRemove ($iEntryId, $iProfileId, $aDataEntry)
    {
        $this->_onEventEventFanRemove ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_schools_event_fan_remove');
    }
  
    function onEventEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry)
    {
        $this->_onEventEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_schools_event_join_confirm');
    }
 
    function isAllowedSendEventInvitation (&$aDataEntry) {
        return $this->isAdmin() || $this->isEventEntryAdmin($aDataEntry) ? true : false;
    }

    function actionEventInvite ($iEntryId) {
        $this->_actionEventInvite ($iEntryId, 'modzzz_schools_event_invitation', $this->_oDb->getParam('modzzz_schools_max_email_invitations'), _t('_modzzz_schools_msg_invitation_sent'), _t('_modzzz_schools_msg_no_users_to_invite'), _t('_modzzz_schools_page_title_invite'));
    }

    function _actionEventInvite ($iEntryId, $sEmailTemplate, $iMaxEmailInvitations, $sMsgInvitationSent, $sMsgNoUsers, $sTitle) {
		global $tmpl;
		require_once( BX_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $tmpl . '/scripts/BxTemplMailBox.php');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEventEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();
 
        $aSchoolEntry = $this->_oDb->getEntryById($aDataEntry['school_id']);

        $GLOBALS['oTopMenu']->setCustomSubHeader($aSchoolEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aSchoolEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aSchoolEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aSchoolEntry[$this->_oDb->_sFieldUri],
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'events/view/' . $aDataEntry[$this->_oDb->_sFieldUri], 
			_t('_modzzz_schools_title_send_invitation') => '',
		));
  
        bx_import('BxDolTwigFormInviter');
        $oForm = new BxDolTwigFormInviter ($this, $sMsgNoUsers);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        

            $aInviter = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getEventInviteParams ($aDataEntry, $aInviter);
            
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;

            // send invitation to registered members
            if (false !== bx_get('inviter_users') && is_array(bx_get('inviter_users'))) {
				$aInviteUsers = bx_get('inviter_users');
                foreach ($aInviteUsers as $iRecipient) {
                    $aRecipient = getProfileInfo($iRecipient);
                    $aPlus = array_merge (array ('NickName' => ' ' . $aRecipient['NickName']), $aPlusOriginal);
                    $iSuccess += sendMail(trim($aRecipient['Email']), $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus) ? 1 : 0;

					$this->eventInviteToInbox($aRecipient, $aTemplate, $aPlusOriginal); 
                }
            }

            // send invitation to additional emails
            $iMaxCount = $iMaxEmailInvitations;
            $aEmails = preg_split ("#[,\s\\b]+#", bx_get('inviter_emails'));
            $aPlus = array_merge (array ('NickName' => ''), $aPlusOriginal);
            if ($aEmails && is_array($aEmails)) {
                foreach ($aEmails as $sEmail) {
                    if (strlen($sEmail) < 5) 
                        continue;
                    $iRet = sendMail(trim($sEmail), $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus) ? 1 : 0;
                    $iSuccess += $iRet;
                    if ($iRet && 0 == --$iMaxCount) 
                        break;
                }             
            }

            $sMsg = sprintf($sMsgInvitationSent, $iSuccess);
 
			$sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'events/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
 
            $sJQueryJS = genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);

            echo MsgBox ($sMsg) . $sJQueryJS;
 
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode ($sMsg, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle . $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

   function _getEventInviteParams ($aDataEntry, $aInviter) {
        
		if($aInviter){ 
			$sInviterNickName = ($aInviter['FirstName']) ? $aInviter['FirstName'] .' '. $aInviter['LastName'] : $aInviter['NickName'];
		}else{
			$sInviterNickName = _t('_modzzz_schools_friend'); 
		}

		return array (
                'EventName' => $aDataEntry['title'],
                'EventLocation' => _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']) . (trim($aDataEntry['city']) ? ', '.$aDataEntry['city'] : '') . ', ' . $aDataEntry['zip'],
                'EventUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'events/view/' . $aDataEntry['uri'], 
                'InviterUrl' => $aInviter ? getProfileLink($aInviter['ID']) : 'javascript:void(0);',
                'InviterNickName' => $sInviterNickName,
                'InvitationText' => stripslashes(strip_tags($_REQUEST['inviter_text'])), 
            );        
    }  


    function eventInviteToInbox($aProfile, $aTemplate, $aPlusOriginal){
	
		$aMailBoxSettings = array
		(
			'member_id'	 =>  $this->_iProfileId, 
			'recipient_id'	 => $aProfile['ID'], 
			'messages_types'	 =>  'mail',  
		);

		$aComposeSettings = array
		(
			'send_copy' => false , 
			'send_copy_to_me' => false , 
			'notification' => false ,
		);
		$oMailBox = new BxTemplMailBox('mail_page', $aMailBoxSettings);

		$sMessageBody = $aTemplate['Body'];
		$sMessageBody = str_replace("<EventName>", $aPlusOriginal['EventName'] , $sMessageBody);
		$sMessageBody = str_replace("<EventLocation>", $aPlusOriginal['EventLocation'] , $sMessageBody);
		$sMessageBody = str_replace("<EventUrl>", $aPlusOriginal['EventUrl'] , $sMessageBody);
		$sMessageBody = str_replace("<InviterUrl>", $aPlusOriginal['InviterUrl'] , $sMessageBody);
		$sMessageBody = str_replace("<InviterNickName>", $aPlusOriginal['InviterNickName'] , $sMessageBody);
		$sMessageBody = str_replace("<InvitationText>", $aPlusOriginal['InvitationText'] , $sMessageBody);

		$oMailBox -> sendMessage($aTemplate['Subject'], $sMessageBody, $aProfile['ID'], $aComposeSettings); 
    }
 
    function isAllowedViewEventParticipants (&$aEvent)
    {
        if (($aEvent['author_id'] == $this->_iProfileId && $GLOBALS['logged']['member'] && isProfileActive($this->_iProfileId)) || $this->isAdmin ())
            return true;

        $this->_oSubPrivacy = new BxSchoolsSubPrivacy($this, $this->_oDb->_sTableEvents); 
	    return $this->_oSubPrivacy->check('view_participants', $aEvent['id'], $this->_iProfileId);  
    }

    function isAllowedEventJoin (&$aDataEntry)
    {
        if (!$this->_iProfileId)
            return false;
        if ($aDataEntry['event_end'] < time())
            return false;

        $this->_oSubPrivacy = new BxSchoolsSubPrivacy($this, $this->_oDb->_sTableEvents); 
	    return $this->_oSubPrivacy->check('join', $aDataEntry['id'], $this->_iProfileId);    
    }

    function isAllowedManageEventFans($aDataEntry)
    {
        return $this->isEventEntryAdmin($aDataEntry);
    }

    function isEventFan($aDataEntry, $iProfileId = 0, $isConfirmed = true)
    {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isEventFan ($aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }

    function isEventEntryAdmin($aDataEntry, $iProfileId = 0)
    {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;
        return $this->_oDb->isGroupAdmin ($aDataEntry['school_id'], $iProfileId) && isProfileActive($iProfileId);
    }

    function actionEventJoin ($iEntryId, $iProfileId)
    {
        $this->_actionEventJoin ($iEntryId, $iProfileId, _t('_modzzz_schools_event_joined_already'), _t('_modzzz_schools_event_joined_already_pending'), _t('_modzzz_schools_event_join_success'), _t('_modzzz_schools_event_join_success_pending'), _t('_modzzz_schools_event_leave_success'));
    }
  
    function _actionEventJoin ($iEntryId, $iProfileId, $sMsgAlreadyJoined, $sMsgAlreadyJoinedPending, $sMsgJoinSuccess, $sMsgJoinSuccessPending, $sMsgLeaveSuccess)
    {
        header('Content-type:text/html;charset=utf-8');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEventEntryByIdAndOwner($iEntryId, 0, true))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedEventJoin($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        $isEventFan = $this->_oDb->isEventFan ($iEntryId, $this->_iProfileId, true) || $this->_oDb->isEventFan ($iEntryId, $this->_iProfileId, false);

        if ($isEventFan) {

            if ($this->_oDb->leaveEventEntry($iEntryId, $this->_iProfileId)) {
                $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'events/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
                echo MsgBox($sMsgLeaveSuccess) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);
                exit;
            }

        } else {

            $isConfirmed = ($this->isEventEntryAdmin($aDataEntry) || !$aDataEntry[$this->_oDb->_sFieldJoinConfirmation] ? true : false);

            if ($this->_oDb->joinEventEntry($iEntryId, $this->_iProfileId, $isConfirmed)) {
                if ($isConfirmed) {
                    $this->onEventEventJoin ($iEntryId, $this->_iProfileId, $aDataEntry);
                    $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'events/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
                } else {
                    $this->onEventEventJoinRequest ($iEntryId, $this->_iProfileId, $aDataEntry);
                    $sRedirect = '';
                }
                echo MsgBox($isConfirmed ? $sMsgJoinSuccess : $sMsgJoinSuccessPending) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);
                exit;
            }
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
        exit;
    }

    function actionEventManageFansPopup ($iEntryId) {
        $this->_actionEventManageFansPopup ($iEntryId, _t('_modzzz_schools_caption_manage_fans'), 'getEventFans', 'isAllowedManageEventFans', 'isAllowedManageEventAdmins', BX_SCHOOLS_MAX_FANS);
    }

    function _actionEventManageFansPopup ($iEntryId, $sTitle, $sFuncGetFans = 'getEventFans', $sFuncIsAllowedManageFans = 'isAllowedManageEventFans', $sFuncIsAllowedManageAdmins = 'isAllowedManageEventAdmins', $iMaxFans = 1000)
    {
        header('Content-type:text/html;charset=utf-8');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEventEntryByIdAndOwner ($iEntryId, 0, true))) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }

        if (!$this->$sFuncIsAllowedManageFans($aDataEntry)) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Access denied')));
            exit;
        }

        $aProfiles = array ();
        $iNum = $this->_oDb->$sFuncGetFans($aProfiles, $iEntryId, true, 0, $iMaxFans);
        if (!$iNum) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }

        $sActionsUrl = bx_append_url_params(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "events/view/" . $aDataEntry[$this->_oDb->_sFieldUri],  'ajax_action=');
        $aButtons = array (
            array (
                'type' => 'submit',
                'name' => 'fans_remove',
                'value' => _t('_sys_btn_fans_remove'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_fans_content', '{$sActionsUrl}remove&ids=' + sys_manage_items_get_manage_fans_ids(), false, 'post'); return false;\"",
            ),
        );
 
        bx_import ('BxTemplSearchResult');
        $sControl = BxTemplSearchResult::showAdminActionsPanel('sys_manage_items_manage_fans', $aButtons, 'sys_fan_unit');

        $aVarsContent = array (
            'suffix' => 'manage_fans',
            'content' => $this->_profilesEdit($aProfiles, false, $aDataEntry),
            'control' => $sControl,
        );
        $aVarsPopup = array (
            'title' => $sTitle,
            'content' => $this->_oTemplate->parseHtmlByName('manage_items_form', $aVarsContent),
        );
        echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true);
        exit;
    }

    function onEventEventJoin ($iEntryId, $iProfileId, $aDataEntry)
    {
        // we do not need to send any notofication mail here because it will be part of standard subscription process
        $oAlert = new BxDolAlerts($this->_sPrefix, 'event_join', $iEntryId, $iProfileId);
        $oAlert->alert();
    }

    function _onEventEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry, $sEmailTemplate, $iMaxFans = 1000)
    {
        $iNum = $this->_oDb->getAdmins($aGroupAdmins, $iEntryId, 0, $iMaxFans);
        $aGroupAdmins[] = getProfileInfo($aDataEntry[$this->_oDb->_sFieldAuthorId]);
        foreach ($aGroupAdmins as $aProfile)
            $this->_notifyEmail ($sEmailTemplate, $aProfile['ID'], $aDataEntry);

        $oAlert = new BxDolAlerts($this->_sPrefix, 'event_join_request', $iEntryId, $iProfileId);
        $oAlert->alert();
    }

    function _onEventEventJoinReject ($iEntryId, $iProfileId, $aDataEntry, $sEmailTemplate)
    {
        $this->_notifyEmail ($sEmailTemplate, $iProfileId, $aDataEntry);
        $oAlert = new BxDolAlerts($this->_sPrefix, 'event_join_reject', $iEntryId, $iProfileId);
        $oAlert->alert();
    }

    function _onEventEventFanRemove ($iEntryId, $iProfileId, $aDataEntry, $sEmailTemplate)
    {
        $this->_notifyEmail ($sEmailTemplate, $iProfileId, $aDataEntry);
        $oAlert = new BxDolAlerts($this->_sPrefix, 'event_fan_remove', $iEntryId, $iProfileId);
        $oAlert->alert();
    }

    function _onEventEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry, $sEmailTemplate)
    {
        $this->_notifyEmail ($sEmailTemplate, $iProfileId, $aDataEntry);
        $oAlert = new BxDolAlerts($this->_sPrefix, 'event_join_confirm', $iEntryId, $iProfileId);
        $oAlert->alert();
    }
 
    function _processEventFansActions ($aDataEntry, $iMaxFans = 1000)
    {
        header('Content-type:text/html;charset=utf-8');

        if (false !== bx_get('ajax_action') && $this->isAllowedManageEventFans($aDataEntry) && 0 == strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {

            $iEntryId = $aDataEntry[$this->_oDb->_sFieldId];
            $aIds = array ();
            if (false !== bx_get('ids'))
                $aIds = $this->_getCleanIdsArray (bx_get('ids'));

            $isShowConfirmedFansOnly = false;
            switch (bx_get('ajax_action')) {
                case 'remove':
                    $isShowConfirmedFansOnly = true;
                    if ($this->_oDb->removeEventFans($iEntryId, $aIds)) {
                        foreach ($aIds as $iProfileId)
                            $this->onEventEventFanRemove ($iEntryId, $iProfileId, $aDataEntry);
                    }
                    break; 
                case 'confirm':
                    if ($this->_oDb->confirmEventFans($iEntryId, $aIds)) {
                        echo '<script type="text/javascript" language="javascript">
                            document.location = "' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "events/view/" . $aDataEntry[$this->_oDb->_sFieldUri] . '";
                        </script>';
                        $aProfiles = array ();
                        $iNum = $this->_oDb->getEventFans($aProfiles, $iEntryId, true, 0, $iMaxFans, $aIds);
                        foreach ($aProfiles as $aProfile) {
                            $this->onEventEventJoin ($iEntryId, $aProfile['ID'], $aDataEntry);
                            $this->onEventEventJoinConfirm ($iEntryId, $aProfile['ID'], $aDataEntry);
                        }
                    }
                    break;
                case 'reject':
                    if ($this->_oDb->rejectEventFans($iEntryId, $aIds)) {
                        foreach ($aIds as $iProfileId)
                            $this->onEventEventJoinReject ($iEntryId, $iProfileId, $aDataEntry);
                    }
                    break;
                case 'list':
                    break;
            }

            $aProfiles = array ();
            $iNum = $this->_oDb->getEventFans($aProfiles, $iEntryId, $isShowConfirmedFansOnly, 0, $iMaxFans);
            if (!$iNum) {
                echo MsgBox(_t('_Empty'));
            } else {
                echo $this->_profilesEdit ($aProfiles, true, $aDataEntry);
            }
            exit;
        }
    }
  
    function isAllowedSubDelete ($aDataEntry, $aSubDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;
 
        if ($GLOBALS['logged']['member'] && $aSubDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true;

        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_DELETE_ANY_SCHOOL, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
   }  





	//[begin] [subprofile]
    function isSubProfileFan($sTable, $aDataEntry, $iProfileId = 0, $isConfirmed = true) {
 		
		if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isSubProfileFan ($sTable, $aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }
 
    function isAllowedUploadVideosSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;
        if (!$this->isMembershipEnabledForVideos())
            return false;
        
		$this->_oSubPrivacy = new BxSchoolsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }
 
    function isAllowedUploadSoundsSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;
        if (!$this->isMembershipEnabledForSounds())
            return false;
        
		$this->_oSubPrivacy = new BxSchoolsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFilesSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;
        if (!$this->isMembershipEnabledForFiles())
            return false;
        
		$this->_oSubPrivacy = new BxSchoolsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }

    function actionUploadVideosSubProfile ($sType, $sUri) {
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadVideosSubProfile', 'videos', array ('videos_choice', 'videos_upload'), _t('_modzzz_schools_page_title_upload_videos'));
    }

    function actionUploadSoundsSubProfile ($sType, $sUri) {
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadSoundsSubProfile', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_modzzz_schools_page_title_upload_sounds')); 
    }

    function actionUploadFilesSubProfile ($sType, $sUri) {
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadFilesSubProfile', 'files', array ('files_choice', 'files_upload'), _t('_modzzz_schools_page_title_upload_files')); 
    }

    function isAllowedSubAdd ($aDataEntry, $isPerformAction = false) {

		if ($this->isAdmin())
            return true;
  
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_ADD_SCHOOL, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedSubEdit ($aDataEntry, $isPerformAction = false) {

        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;
  
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_SCHOOLS_EDIT_ANY_SCHOOL, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }  
	//[end] [subprofile]

    function isAllowedReadForum(&$aDataEntry, $iProfileId = -1)
    {
        return true;
    }
 
    function isSubEntryAdmin($aSubEntry, $iProfileId = 0) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;

		$aDataEntry = $this->_oDb->getEntryById ((int)$aSubEntry['school_id']);  

        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aSubEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;

        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;

        return $this->_oDb->isGroupAdmin ($aDataEntry['id'], $iProfileId) && isProfileActive($iProfileId);
    }

	function getSchoolLink($iEntryId){

		$iEntryId = (int)$iEntryId;
		$aDataEntry = $this->_oDb->getEntryById($iEntryId);
		
		$sUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

		return '<a href="'.$sUrl.'">'.$aDataEntry[$this->_oDb->_sFieldTitle].'</a>';
	}
 
}