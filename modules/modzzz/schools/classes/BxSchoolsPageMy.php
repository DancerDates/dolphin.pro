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

bx_import('BxDolPageView');

class BxSchoolsPageMy extends BxDolPageView {	

    var $_oMain;
    var $_oTemplate;
    var $_oDb;
    var $_oConfig;
    var $_aProfile;

	function __construct(&$oMain, &$aProfile) {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->_aProfile = $aProfile;
		parent::__construct('modzzz_schools_my');
	}

    function getBlockCode_Owner() {        
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch (bx_get('filter')) {
        case 'add_school':
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_schools':
            $sContent = $this->getBlockCode_My ();
            break;            
        case 'pending_schools':
            $sContent = $this->getBlockCode_Pending ();
            break;            
        default:
            $sContent = $this->getBlockCode_Main ();
        }

        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aMenu = array(
            _t('_modzzz_schools_block_submenu_main') => array('href' => $sBaseUrl, 'active' => !bx_get('filter')),
            _t('_modzzz_schools_block_submenu_add_school') => array('href' => $sBaseUrl . '&filter=add_school', 'active' => 'add_school' == bx_get('filter')),
            _t('_modzzz_schools_block_submenu_manage_schools') => array('href' => $sBaseUrl . '&filter=manage_schools', 'active' => 'manage_schools' == bx_get('filter')),
            _t('_modzzz_schools_block_submenu_pending_schools') => array('href' => $sBaseUrl . '&filter=pending_schools', 'active' => 'pending_schools' == bx_get('filter')),
        );
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse() {

        modzzz_schools_import ('SearchResult');
        $o = new BxSchoolsSearchResult('user', process_db_input ($this->_aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION));
        $o->aCurrent['rss'] = 0;

        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_modzzz_schools_page_title_my_schools');

        if ($o->isError) {
            return DesignBoxContent(_t('_modzzz_schools_block_users_schools'), MsgBox(_t('_Empty')), 1);
        }

        if ($s = $o->processing()) {
            $this->_oTemplate->addCss ('unit.css');
            $this->_oTemplate->addCss ('main.css');            
            return $s;
        } else {
            return DesignBoxContent(_t('_modzzz_schools_block_users_schools'), MsgBox(_t('_Empty')), 1);
        }
    }

    function getBlockCode_Main() {
        $iActive = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        if ($iPending)
            $aVars['msg'] = sprintf(_t('_modzzz_schools_msg_you_have_pending_approval_schools'), $sBaseUrl . '&filter=pending_schools', $iPending);
        elseif (!$iActive)
            $aVars['msg'] = sprintf(_t('_modzzz_schools_msg_you_have_no_schools'), $sBaseUrl . '&filter=add_school');
        else
            $aVars['msg'] = sprintf(_t('_modzzz_schools_msg_you_have_some_schools'), $sBaseUrl . '&filter=manage_schools', $iActive, $sBaseUrl . '&filter=add_school');
        return $this->_oTemplate->parseHtmlByName('my_schools_main', $aVars);
    }

    function getBlockCode_Add() {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
        $this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my'); 
        $aVars = array ('form' => ob_get_clean(), 'id' => '');
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_schools_create_school', $aVars);
    }

    function getBlockCode_Pending() {
        $sForm = $this->_oMain->_manageEntries ('my_pending', '', false, 'modzzz_schools_pending_user_form', array(
            'action_delete' => '_modzzz_schools_admin_delete',
        ), 'modzzz_schools_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_schools_my_pending');
        return $this->_oTemplate->parseHtmlByName('my_schools_manage', $aVars); 
    }

	function getBlockCode_My() {
        $sForm = $this->_oMain->_manageEntries ('user', process_db_input ($this->_aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION), false, 'modzzz_schools_user_form', array(
            'action_delete' => '_modzzz_schools_admin_delete',
        ), 'modzzz_schools_my_active', true, 7);
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_schools_my_active');
        return $this->_oTemplate->parseHtmlByName('my_schools_manage', $aVars);
    }    
}
