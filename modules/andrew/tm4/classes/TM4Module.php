<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by AndrewP. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
* This product cannot be redistributed for free or a fee without written permission from AndrewP.
* This notice may not be removed from the source code.
*
***************************************************************************/
bx_import('BxDolModule');

class TM4Module extends BxDolModule {

    // variables
    var $aPageTmpl;
    var $sModuleUrl;
    var $_iVisitorID;

    // constructor
    function TM4Module($aModule) {
        parent::__construct($aModule);

        $this->aPageTmpl = array(
            'name_index' => 100, 
            'header' => $GLOBALS['site']['title'],
            'header_text' => '',
        );

        $this->sModuleUrl = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();
        $this->_iVisitorID = $this->getUserId();
    }

    function actionAdministration($sAction = '') {
        if (! isAdmin()) {
            $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $this->_oTemplate->displayAccessDenied()));
            return;
        }

        $sSettings = $this->getAdministrationSettings();
        $sCode = DesignBoxAdmin(_t('_atm4_settings'), $sSettings, '');

        $this->aPageTmpl['name_index'] = 9;
        $this->aPageTmpl['header'] = _t('_atm4_settings');
        $this->_oTemplate->pageCode($this->aPageTmpl, array('page_main_code' => $sCode), array(), array(), true);
    }

    // get administration settings
    function getAdministrationSettings() {
        $iId = $this->_oDb->getSettingsCategory();
        if(empty($iId))
            return MsgBox(_t('_sys_request_page_not_found_cpt'));

        bx_import('BxDolAdminSettings');

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) {
            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);

            $sNewSkin = getParam('tm4_skin');
            $this->_oDb->updateSkin($sNewSkin);
        }

        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();

        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;

        return $sResult;
    }
}
