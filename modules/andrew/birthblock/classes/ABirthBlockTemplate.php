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
bx_import('BxDolModuleTemplate');

class ABirthBlockTemplate extends BxDolModuleTemplate {

    /**
     * Constructor
     */
    function ABirthBlockTemplate(&$oConfig, &$oDb) {
        parent::__construct($oConfig, $oDb);
    }

    // Output
    function pageCode ($aPage = array(), $aPageCont = array(), $aCss = array(), $aJs = array(), $bAdminMode = false, $isSubActions = true) {
        if (!empty($aPage)) {
            foreach ($aPage as $sKey => $sValue)
                $GLOBALS['_page'][$sKey] = $sValue;
        }
        if (!empty($aPageCont)) {
            foreach ($aPageCont as $sKey => $sValue)
                $GLOBALS['_page_cont'][$aPage['name_index']][$sKey] = $sValue;
        }
        if (!empty($aCss))
            $this->addCss($aCss);
        if (!empty($aJs))
            $this->addJs($aJs);

        if (!$bAdminMode)
            PageCode($this);
        else
            PageCodeAdmin();
    }

    function adminBlock($sContent, $sTitle, $aMenu = array()) {
        return DesignBoxAdmin($sTitle, $sContent, $aMenu);
    }
}
