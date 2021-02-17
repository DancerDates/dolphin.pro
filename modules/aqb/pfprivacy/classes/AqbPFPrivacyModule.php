<?php
/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*      
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY. 
* To be able to use this product for another domain names you have to order another copy of this product (license).
* 
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
* 
* This notice may not be removed from the source code.
* 
***************************************************************************/

bx_import('BxDolModule');

define('AQB_PFP_ACTION_VIEW', 'view');

class AqbPFPrivacyModule extends BxDolModule {
	var $_oPrivacy;

	//--- Constructor ---//
	function __construct($aModule) {
	    parent::__construct($aModule);

	    $this->_oConfig->init($this->_oDb);

	    bx_import('Privacy', $this->_aModule);
		$this->_oPrivacy = new AqbPFPrivacyPrivacy($this);
	}

	function getBlockEdit($sRedirect = '') {
		if(empty($sRedirect))
			$sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home';

		bx_import('FormEdit', $this->_aModule);
	    $oForm = new AqbPFPrivacyFormEdit();

	    $oForm->initChecker();
        if($oForm->isSubmittedAndValid()) {
        	$iUserId = $this->getUserId();

        	$sAction = AQB_PFP_ACTION_VIEW;
			$sFieldAction = $this->_oPrivacy->getFieldAction($sAction);

        	$aBlocks = $this->_oDb->getProfileFieldsBlocks();
	        foreach($aBlocks as $aBlock) {
	        	$aFields = $this->_oDb->getProfileFieldsByBlock($aBlock['id']);
				foreach($aFields as $aField) {
					$sInputName = $sFieldAction . '_' . $aField['id'];
					$iInputValue = (int)$oForm->getCleanValue($sInputName);

					$this->_oDb->replaceEntry($iUserId, $aField['id'], $iInputValue);
				}
	        }

        	header('Location: ' . $sRedirect);
            exit;
        }

	    return $oForm->getCode();
	}
	//--- Admin Settings Methods ---//
	function getSettings($mixedResult) {
	    $iId = (int)$this->_oDb->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name`='" . $this->_oConfig->getUri() . "'");
	    if(empty($iId))
	       return MsgBox('_aqb_pfprivacy_msg_no_results');

		bx_import('BxDolAdminSettings');
        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();

        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;

        return $sResult;
	}

	function setSettings($aData) {
	    $iId = (int)$this->_oDb->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name`='" . $this->_oConfig->getUri() . "'");
	    if(empty($iId))
	       return MsgBox(_t('_aqb_pfprivacy_msg_no_results'));

		bx_import('BxDolAdminSettings');
	    $oSettings = new BxDolAdminSettings($iId);
	    return $oSettings->saveChanges($_POST);
	}

	//--- Service Methods ---//
	function serviceGetBlockPrivacyEdit() {
		if(!$this->_oConfig->isViaPedit())
			return '';

		return $this->getBlockEdit(BX_DOL_URL_ROOT . 'pedit.php?ID=' . $this->getUserId());
	}

	function serviceGetBlockProfileFields($iProfileId, $iBlockId, $iPFBlockId) {
		$iUserId = $this->getUserId();

		$aTopMenu = array();
		if((isMember() || isAdmin()) && ($iProfileId == $iUserId))
			$aTopMenu = array(
				_t('_Edit') => array(
					'href' => 'pedit.php?ID=' . $iProfileId,
					'dynamicPopup' => false,
					'active' => false,
				),
			);

		return array($this->_getViewFieldsTable($iProfileId, $iBlockId, $iPFBlockId), $aTopMenu, array(), '');
	}

	//--- Action Methods ---//
	function actionHome() {
		bx_import('PageHome', $this->_aModule);
	    $oPage = new AqbPFPrivacyPageHome($this);

		$aParams = array(
			'index' => 1,
			'title' => array(
				'page' => _t('_aqb_pfprivacy_page_home'),
			),
			'js' => '',
			'css' => '',
			'breadcrumb' => array(),
			'content' => array(
				'page_main_code' => $oPage->getCode() 
			)
		);
		$this->_oTemplate->getPageCode($aParams);
	}

	function actionAdmin($sName = '') {
		$GLOBALS['iAdminPage'] = 1;
		require_once(BX_DIRECTORY_PATH_INC . 'admin_design.inc.php');

		$sUri = $this->_oConfig->getUri();
		$sUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'admin';

		check_logged();
		if(!@isAdmin()) {
		    send_headers_page_changed();
			login_form("", 1);
			exit;
		}

		//--- Process actions ---//
		$mixedResultSettings = '';
		if(isset($_POST['save']) && isset($_POST['cat']))
		    $mixedResultSettings = $this->setSettings($_POST);

		$this->_oTemplate->addAdminJs(array('main.js'));
		$this->_oTemplate->addAdminCss(array('main.css'));

		$aParams = array(
			'title' => array(
				'page' => _t('_aqb_pfprivacy_page_admin')
			),
			'content' => array(
				'page_main_code' => DesignBoxAdmin(_t('_aqb_pfprivacy_block_admin_settings'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $this->getSettings($mixedResultSettings))))
			)
		);
		$this->_oTemplate->getPageCodeAdmin($aParams);
	}

	//--- Private Methods ---//
	function _getViewFieldsTable($iProfileId, $iPageBlockID, $iPFBlockID)
    {
    	$iUserId = $this->getUserId();

		bx_import('BxBaseProfileView');
		$oProfileGenerator = new BxBaseProfileGenerator($iProfileId);

        if(!isset($oProfileGenerator->aPFBlocks[$iPFBlockID]) || empty($oProfileGenerator->aPFBlocks[$iPFBlockID]['Items']))
			return '';

        // get parameters
        $bCouple = $oProfileGenerator->bCouple;
        $aItems = $oProfileGenerator->aPFBlocks[$iPFBlockID]['Items'];

        // collect inputs
        $aInputs = array();
        $aInputsSecond = array();

        foreach($aItems as $iItem => $aItem) {
        	if(!$this->_isViewAllowed($iItem, $iUserId))
        		continue;

            $sItemName = $aItem['Name'];
            $sValue1 = $oProfileGenerator->_aProfile[$sItemName];
            $sValue2 = $oProfileGenerator->_aCouple[$sItemName];

            if ($aItem['Name'] == 'Age') {
                $sValue1 = $oProfileGenerator->_aProfile['DateOfBirth'];
                $sValue2 = $oProfileGenerator->_aCouple['DateOfBirth'];
            }

            if ($oProfileGenerator->bPFEditable) {
                $aParams = array(
                    'couple' => $oProfileGenerator->bCouple,
                    'values' => array(
                        $sValue1,
                        $sValue2
                    ),
                    'profile_id' => $oProfileGenerator->_iProfileID,
                );

                $aInputs[] = $oProfileGenerator->oPF->convertEditField2Input($aItem, $aParams, 0);

                if ($aItem['Type'] == 'pass') {
                    $aItem_confirm = $aItem;

                    $aItem_confirm['Name']    .= '_confirm';
                    $aItem_confirm['Caption']  = '_Confirm password';
                    $aItem_confirm['Desc']     = '_Confirm password descr';

                    $aInputs[] = $oProfileGenerator->oPF->convertEditField2Input($aItem_confirm, $aParams, 0);

                    if ($oProfileGenerator->bCouple and !in_array($sItemName, $oProfileGenerator->aCoupleMutualItems))
                        $aInputsSecond[] = $oProfileGenerator->oPF->convertEditField2Input($aItem_confirm, $aInputParams, 1);
                }

                if ($oProfileGenerator->bCouple and !in_array($sItemName, $oProfileGenerator->aCoupleMutualItems) and $sValue2) {
                    $aInputsSecond[] = $oProfileGenerator->oPF->convertEditField2Input($aItem, $aParams, 1);
                }
            } else {
                if ($sValue1 || $aItem['Type'] == 'bool') { //if empty, do not draw
                    $aInputs[] = array(
                        'type'    => 'value',
                        'name'    => $aItem['Name'],
                        'caption' => _t($aItem['Caption']),
                        'value'   => $oProfileGenerator->oPF->getViewableValue($aItem, $sValue1),
                    );
                }

                if ($oProfileGenerator->bCouple and !in_array($sItemName, $oProfileGenerator->aCoupleMutualItems) and ($sValue2 || $aItem['Type'] == 'bool')) {
                    $aInputsSecond[] = array(
                        'type'    => 'value',
                        'name'    => $aItem['Name'],
                        'caption' => _t($aItem['Caption']),
                        'value'   => $oProfileGenerator->oPF->getViewableValue($aItem, $sValue2),
                    );
                }
            }
        }

        // merge with couple
        if (!empty($aInputsSecond)) {
            $aHeader1 = array( // wrapper for merging
                array( // input itself
                    'type' => 'block_header',
                    'caption' => _t('_First Person')
                )
            );

            $aHeader2 = array(
                array(
                    'type' => 'block_header',
                    'caption' => _t('_Second Person'),
                )
            );

            $aInputs = array_merge($aHeader1, $aInputs, $aHeader2, $aInputsSecond);
        }

        if (empty($aInputs))
            return '';

        if ($oProfileGenerator->bPFEditable) {
            // add submit button
            $aInputs[] = array(
                'type' => 'submit',
                'colspan' => 'true',
                'value' => _t('_Save'),
            );

            // add hidden inputs
            // profile id
            $aInputs[] = array(
                'type' => 'hidden',
                'name' => 'ID',
                'value' => $oProfileGenerator->_iProfileID,
            );

            $aInputs[] = array(
                'type' => 'hidden',
                'name' => 'force_ajax_save',
                'value' => '1',
            );

            $aInputs[] = array(
                'type' => 'hidden',
                'name' => 'pf_block',
                'value' => $iPFBlockID,
            );

            $aInputs[] = array(
                'type' => 'hidden',
                'name' => 'do_submit',
                'value' => '1',
            );

            $aFormAttrs = array(
                'method' => 'post',
                'action' => BX_DOL_URL_ROOT . 'pedit.php',
                'onsubmit' => "submitViewEditForm(this, $iPageBlockID, " . bx_html_attribute($_SERVER['PHP_SELF']) . "'?ID={$oProfileGenerator->_iProfileID}'); return false;",
                'name' => 'edit_profile_form',
            );

            $aFormParams = array();
        } else {
            $aFormAttrs = array(
                'name' => 'view_profile_form',
            );

            $aFormParams = array(
                'remove_form'    => true,
            );
        }

        // generate form array
        $aForm = array(
            'form_attrs' => $aFormAttrs,
            'params'     => $aFormParams,
            'inputs'     => $aInputs,
        );

        $oForm = new BxTemplFormView($aForm);

        return $oForm->getCode();
    }

	function _isViewAllowed($iFieldId, $iViewerId)
    {
        return $this->_oPrivacy->check('view', $iFieldId, $iViewerId);
    }
}
?>