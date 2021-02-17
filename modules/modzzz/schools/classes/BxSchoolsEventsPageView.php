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

class BxSchoolsEventsPageView extends BxDolTwigPageView {	

    function __construct(&$oEventsMain, &$aEvents) {
        parent::__construct('modzzz_schools_events_view', $oEventsMain, $aEvents);
	
        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventsMediaPrefix; 
	}

   	function getBlockCode_Info() {
        return array($this->_oTemplate->blockSubProfileInfo ('event', $this->aDataEntry));
    }

	function getBlockCode_Desc() {
		$aData = $this->aDataEntry;

        $aVars = array (
            'description' => $this->aDataEntry['desc'], 
        );

        return array($this->_oTemplate->parseHtmlByName('block_description', $aVars)); 
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
        modzzz_schools_import('EventsVoting');
        $o = new BxSchoolsEventsVoting ('modzzz_schools_events', (int)$this->aDataEntry['id']);
    	if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRateSubProfile($this->_oDb->_sTableEvents, $this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_schools_import('EventsCmts');
        $o = new BxSchoolsEventsCmts ('modzzz_schools_events', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
			$aEventsEntry = $this->_oDb->getEventsEntryById($this->aDataEntry['id']);
			$iEntryId = $aEventsEntry['school_id'];
	
		    $aDataEntry = $this->_oDb->getEntryById($iEntryId);
   
   			$isFan = $this->_oDb->isEventFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 0) || $this->_oDb->isEventFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 1);

            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
 
                'TitleInvite' => $this->_oMain->isAllowedSendEventInvitation($this->aDataEntry) ? _t('_bx_events_action_title_invite') : '',
 
                'TitleJoin' => $this->_oMain->isAllowedEventJoin($this->aDataEntry) ? ($isFan ? _t('_modzzz_schools_action_title_leave') : _t('_modzzz_schools_action_title_join')) : '',
				'IconJoin' => $isFan ? 'sign-out' : 'sign-in', 

                'TitleManageFans' => $this->_oMain->isAllowedManageEventFans($this->aDataEntry) ? _t('_modzzz_schools_action_manage_fans') : '',

                'TitleEdit' => $this->_oMain->isAllowedEdit($aDataEntry) ? _t('_modzzz_schools_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($aDataEntry) ? _t('_modzzz_schools_action_title_delete') : '',
				'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotosSubProfile($this->_oDb->_sTableEvents,$this->aDataEntry) ? _t('_modzzz_schools_action_upload_photos') : '', 


            );
 
            if (!$this->aInfo['TitleEdit'] && !$this->aInfo['TitleDelete'] && !$this->aInfo['TitleUploadPhotos'] && !$aInfo['TitleInvite']  && !$aInfo['TitleJoin'] && !$aInfo['TitleManageFans'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'modzzz_schools_events');
        } 

        return '';
    }    
  
	function getBlockCode_Location() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'location');
    }
  
	function _blockCustomDisplay($aDataEntry, $sType) {
		
		switch($sType){ 
			case "location":
				$aAllow = array('place','address1','city','state','country','zip');
			break;  
		}
  
		$sFields = $this->_oTemplate->blockSubItemFields($aDataEntry, 'event', $aAllow);

		if(!$sFields) return;

		$aVars = array ( 
            'fields' => $sFields, 
        );
		 
        return array($this->_oTemplate->parseHtmlByName('custom_block_info', $aVars));   
    }
 
    function getBlockCode_Participants() {
        return $this->_blockFans ($this->_oDb->getParam('modzzz_schools_perpage_view_fans'), 'isAllowedViewEventParticipants', 'getEventFans');
    }  

    function getBlockCode_ParticipantsUnconfirmed() {
        return $this->_blockFansUnconfirmed (BX_SCHOOLS_MAX_FANS);
    }

    function _blockFans($iPerPage, $sFuncIsAllowed = 'isAllowedViewEventParticipants', $sFuncGetFans = 'getEventFans')
    {
        if (!$this->_oMain->$sFuncIsAllowed($this->aDataEntry))
            return '';

        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->$sFuncGetFans($aProfiles, $this->aDataEntry[$this->_oDb->_sFieldId], true, $iStart, $iPerPage);
        if (!$iNum || !$aProfiles)
            return MsgBox(_t("_Empty"));

        bx_import('BxTemplSearchProfile');
        $oBxTemplSearchProfile = new BxTemplSearchProfile();
        $sMainContent = '';
        foreach ($aProfiles as $aProfile) {
            $sMainContent .= $oBxTemplSearchProfile->displaySearchUnit($aProfile, array ('ext_css_class' => 'bx-def-margin-sec-top-auto'));
        }
        $ret .= $sMainContent;
        $ret .= '<div class="clear_both"></div>';

        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $iNum,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . bx_append_url_params(BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . "events/view/" . $this->aDataEntry[$this->_oDb->_sFieldUri], 'page={page}&per_page={per_page}') . '\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate('', -1, -1, false);

        return array($ret, array(), $sAjaxPaginate);
    }

    function _blockFansUnconfirmed($iFansLimit = 1000)
    {
        if (!$this->_oMain->isEventEntryAdmin($this->aDataEntry))
            return '';

        $aProfiles = array ();
        $iNum = $this->_oDb->getEventFans($aProfiles, $this->aDataEntry[$this->_oDb->_sFieldId], false, 0, $iFansLimit);
        if (!$iNum)
            return MsgBox(_t('_Empty'));

        $sActionsUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . "events/view/" . $this->aDataEntry[$this->_oDb->_sFieldUri] . '?ajax_action=';
        $aButtons = array (
            array (
                'type' => 'submit',
                'name' => 'fans_reject',
                'value' => _t('_sys_btn_fans_reject'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_unconfirmed_fans_content', '{$sActionsUrl}reject&ids=' + sys_manage_items_get_unconfirmed_fans_ids(), false, 'post'); return false;\"",
            ),
            array (
                'type' => 'submit',
                'name' => 'fans_confirm',
                'value' => _t('_sys_btn_fans_confirm'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_unconfirmed_fans_content', '{$sActionsUrl}confirm&ids=' + sys_manage_items_get_unconfirmed_fans_ids(), false, 'post'); return false;\"",
            ),
        );
        bx_import ('BxTemplSearchResult');
        $sControl = BxTemplSearchResult::showAdminActionsPanel('sys_manage_items_unconfirmed_fans', $aButtons, 'sys_fan_unit');
        $aVars = array(
            'suffix' => 'unconfirmed_fans',
            'content' => $this->_oMain->_profilesEdit($aProfiles),
            'control' => $sControl,
        );
        return $this->_oMain->_oTemplate->parseHtmlByName('manage_items_form', $aVars);
    }

    function getCode() { 

        $this->_oMain->_processEventFansActions ($this->aDataEntry, BX_SCHOOLS_MAX_FANS);

        return parent::getCode();
    }
	

}
