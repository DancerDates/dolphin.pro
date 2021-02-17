<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx 
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

function modzzz_pcheck_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'pcheck') {
        $oMain = BxDolModule::getInstance('BxPCheckModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
   
 
/*
 * PCheck module
 * 
 * 
 *
 */
class BxPCheckModule extends BxDolTwigModule {
 
    var $_aQuickCache = array ();
    var $_oPrivacy; 
 
    function __construct(&$aModule) {

        parent::__construct($aModule);        
        $this->_sFilterName = 'filter';
        $this->_sPrefix = 'modzzz_pcheck';
        $this->_oDb->_sPrefix = 'modzzz_pcheck_';
 
        $GLOBALS['oBxPCheckModule'] = &$this; 
    }

    function actionHome ($sParam='') { 
        $this->_actionHome($sParam, _t('_modzzz_pcheck_page_title_home'));
    }
   
    function _actionHome ($sParam, $sTitle) {
    
		header('Location:'. BX_DOL_URL_ROOT .  'member.php'); 
    }

    // ================================== admin actions
  
	function serviceProgressBlock( $iProfileId=0 ){
		
		if($iProfileId && getParam('modzzz_pcheck_show_owner')=='on'){
			if($iProfileId != $this->_iProfileId) return;
		}

		if(!$this->isAllowedViewWidget()) return;

		$iProfileId = ($iProfileId) ? $iProfileId : $this->_iProfileId;

		$iPercent = $this->_oDb->getProfileFillPercent($iProfileId);
		$aNextField = $this->_oDb->getNextEmptyProfileField($iProfileId);

		if($iPercent>=100 && getParam('modzzz_pcheck_hide_complete')=='on') return;
 
		$aVars = array(
			'percent' => $iPercent,
		
			'bx_if:show_next' => array (
				'condition' => (($iPercent < 100) && !empty($aNextField)),
				'content' => array (
					'next_field_name' => ($aNextField['name']=='Avatar') ? _t('_modzzz_pcheck_field_avatar') : _t('_FieldCaption_'.$aNextField['name'].'_View'),
					'next_field_weight' => $aNextField['weight'],
				)
			),
			'bx_if:edit_profile' => array (
				'condition' => (($iPercent < 100) && ($iProfileId == $this->_iProfileId)),
				'content' => array (
					'id' => $iProfileId,
				)
			)
		);
		
		$this->_oTemplate->addCss(array('twig.css','unit.css')); 
		return $this->_oTemplate->parseHtmlByName('unit.html', $aVars);
	} 

	function actionAdministration ($sUrl = '', $sParam1 = '', $sParam2 = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        
  
        $this->_oTemplate->pageStart();

        $aMenu = array(   
            'update' => array(
                'title' => _t('_modzzz_pcheck_menu_admin_update'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/update',
                '_func' => array ('name' => 'actionAdministrationUpdate', 'params' => array()),
            ),  
			'manage' => array(
                'title' => _t('_modzzz_pcheck_menu_admin_manage_fields'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/manage', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array()),
            ),  
			'settings' => array(
                'title' => _t('_modzzz_pcheck_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ) 
        );

		if (empty($aMenu[$sUrl])){
            $sUrl = 'settings';
		}
  
        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_pcheck_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css')); 
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_pcheck_page_title_administration'));
    }
 
    function actionAdministrationUpdate () {
    
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

		$aForm = array(
            'form_attrs' => array(
                'name' => 'update_form',
                'method' => 'post', 
                'action' => '',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_update',
                ),
            ),
            'inputs' => array(  
				'title' => array(
                    'type' => 'custom',
                    'content' => _t('_modzzz_pcheck_form_caption_update_system'),
                 ),   
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_pcheck_continue'),
                    'name'  => 'submit_update',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid ()) {
			$this->_oDb->updateProfileTable();
			$sMsg = MsgBox(_t('_modzzz_pcheck_init_fields_success'));
		} 
		$sCode = $oForm->getCode(); 
	  
        $aVars = array (
            'content' => $sMsg . $sCode 
        );

        return $this->_oTemplate->parseHtmlByName('default_padding', $aVars);
    }
 
    function actionAdministrationManage () {
 
		if($_POST['field']) {   
  
			foreach($_POST['field'] as $iKey => $iValue){
	 
				$sWeight = process_db_input($_POST['weight'][$iKey]);
 				$bStatus = (int)process_db_input($_POST['active'][$iKey]);
  
				$this->_oDb->query("UPDATE `" . $this->_oDb->_sPrefix . "profile_fields` SET `Weight`='$sWeight', `Active`='$bStatus' WHERE `id`=$iKey");  
 
			}//end foreach

			$this->_oDb->initializeUpdateCron(); 
		}
 
		$aItems = array();
		$aFields = $this->_oDb->getProfileFields();
		 
		foreach ($aFields as $aEachField){ 
  			$sActive = ($aEachField['Active']) ? "checked='checked'" : "";
	 
			$aItems[] = array(
				'id' => $aEachField['id'],
				'field_name' => ($aEachField['Name']=='Avatar') ? _t('_modzzz_pcheck_field_avatar') : _t('_FieldCaption_'.$aEachField['Name'].'_View'),
				'weight' => $aEachField['Weight'],
				'active' => $sActive
			);
		}

		$aVars = array(
			'header_desc' => _t('_modzzz_pcheck_msg_manage_profile_fields'),  
			'bx_repeat:items' => $aItems,
		);

		$this->_oTemplate->addCss(array('unit.css', 'twig.css'));
 
		$sCode = $this->_oTemplate->parseHtmlByName('block_admin_manage_actions.html', $aVars);
 
		return $this->_oTemplate->parseHtmlByName('default_padding.html', array('content' => $sCode));
	}


    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('PCheck');
    }
   
    // ================================== permissions
  
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('modzzz_pcheck_permalinks') == 'on'));
		 
        return $bEnabled;
    }
   
    function serviceResponseProfileJoin ($oAlert) {
 
        if (!($iProfileId = (int)$oAlert->iObject))
            return false;
  
		$iPercent = $this->_oDb->getProfileFillPercent($iProfileId);

		$iFilled = ($iPercent >= 100) ? 1 : 0;
		$this->_oDb->updateProfileFillPercent($iProfileId, $iFilled);
        
		return true;
    }
 
    function serviceResponseProfileEdit ($oAlert) {
 
        if (!($iProfileId = (int)$oAlert->iObject))
            return false;
  
		$iPercent = $this->_oDb->getProfileFillPercent($iProfileId);

		$iFilled = ($iPercent >= 100) ? 1 : 0;
		$this->_oDb->updateProfileFillPercent($iProfileId, $iFilled);
        
		return true;
    }
 
    function serviceResponsePhotoAdd ($oAlert) {
 
        if (!($iProfileId = (int)$oAlert->iSender))
            return false;
  
		$iPercent = $this->_oDb->getProfileFillPercent($iProfileId);

		$iFilled = ($iPercent >= 100) ? 1 : 0;
		$this->_oDb->updateProfileFillPercent($iProfileId, $iFilled);
        
		return true;
    }

    function serviceResponsePhotoDelete ($oAlert) {
 
        if (!($iProfileId = (int)$oAlert->iSender))
            return false;
  
		$iPercent = $this->_oDb->getProfileFillPercent($iProfileId);

		$iFilled = ($iPercent >= 100) ? 1 : 0;
		$this->_oDb->updateProfileFillPercent($iProfileId, $iFilled);
        
		return true;
    }
  
	function serviceInitialize(){
		$this->_oDb->initialize(); 
	}

	function serviceCleanup(){
		$this->_oDb->cleanup(); 
	}

	function actionprocess() 
	{
		$this -> _oDb -> processProfiles();
	}

    function isAllowedViewWidget ($isPerformAction = false) {
   
		if( $this->isAdmin() ) return true;

        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_PCHECK_ALLOW_VIEW, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }
 
    function _defineActions () {
        defineMembershipActions(array('pcheck allow view'));
    }



}