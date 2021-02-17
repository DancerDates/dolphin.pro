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
bx_import('BxTemplFormView');

class AqbPFPrivacyFormEdit extends BxTemplFormView {
    var $_oModule;

    function __construct() {
        $this->_oModule = BxDolModule::getInstance('AqbPFPrivacyModule');

        $iUserId = $this->_oModule->getUserId();

        $sJsObjMain = $this->_oModule->_oConfig->getJsObject('main');
        $sFormHtmlId = $this->_oModule->_oConfig->getHtmlId('form_edit');

        $aForm = array(
            'form_attrs' => array(
                'id' => $sFormHtmlId,
                'name' => $sFormHtmlId,
                'action' => '',
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            ),
            'params' => array (
                'db' => array(
                    'table' => $this->_oModule->_oDb->getPrefix() . 'entries',
                    'key' => 'id',
                    'uri' => '',
                    'uri_title' => '',
                    'submit_name' => 'submit'
                ),
            ),
            'inputs' => array ()
        );

		$sModuleUri = $this->_oModule->_oConfig->getUri();

		$sAction = AQB_PFP_ACTION_VIEW;
		$sFieldAction = $this->_oModule->_oPrivacy->getFieldAction($sAction);

		$aInputValues = array();
		$this->_oModule->_oDb->getEntries(array('type' => 'all_by_user', 'value' => $iUserId), $aInputValues);

		$bFirst = true;
        $aBlocks = $this->_oModule->_oDb->getProfileFieldsBlocks();
        foreach($aBlocks as $aBlock) {
        	$sBlock = strtolower($aBlock['name']);
			$sBlockCaption = $this->_getFieldCaption($aBlock['name']);

        	$aForm['inputs']['block_' . $sBlock . '_beg'] = array(
				'type' => 'block_header',
				'caption' => $sBlockCaption,
				'collapsable' => true,
				'collapsed' => !$bFirst
			);

			$aFields = $this->_oModule->_oDb->getProfileFieldsByBlock($aBlock['id']);
			foreach($aFields as $aField) {
				$sInput = $sFieldAction . '_' . $aField['id'];
				$aForm['inputs'][$sInput] = $this->_oModule->_oPrivacy->getGroupChooser($iUserId, $sModuleUri, $sAction);

				$aForm['inputs'][$sInput]['name'] = $sInput;
				if(!empty($aInputValues[$aField['id']]))
					$aForm['inputs'][$sInput]['value'] = $aInputValues[$aField['id']][$sFieldAction];

				$FieldCaption = $this->_getFieldCaption($aField['name']);
				$aForm['inputs'][$sInput]['caption'] = sprintf($aForm['inputs'][$sInput]['caption'], $FieldCaption);
			}

			$aForm['inputs']['block_' . $sBlock . '_end'] = array(
				'type' => 'block_end',
			);

			$bFirst = false;
        }

        $aForm['inputs']['submit'] = array(
			'type' => 'submit',
			'name' => 'submit',
			'value' => _t('_aqb_pfprivacy_form_btn_save'),
		);
			
        parent::__construct($aForm);
    }

    private function _getFieldCaption($sName) {
    	$sBlockCaptionKey = '_FieldCaption_' . $sName . '_View';
		$sBlockCaptionText = _t($sBlockCaptionKey);
		if($sBlockCaptionKey == $sBlockCaptionText) {
			$sBlockCaptionKey = '_FieldCaption_' . $sName . '_Join';
			$sBlockCaptionText = _t($sBlockCaptionKey);
		}

		return $sBlockCaptionText;
    }
}
?>