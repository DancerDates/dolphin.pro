<?
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by MChristiaan and cannot be modified for other than personal usage. 
* This product cannot be redistributed for free or a fee without written permission from MChristiaan. 
* This notice may not be removed from the source code.
*
***************************************************************************/

bx_import ('BxDolModuleTemplate');

class MChrisMToolsTemplate extends BxDolModuleTemplate {
    var $_oConfig;
    var $_oDb;
	/**
	 * Constructor
	 */    
	function MChrisMToolsTemplate(&$oConfig, &$oDb) {
	    parent::__construct($oConfig, $oDb);
    }
	
	function init(&$oDb) {
        $this->_oDb = &$oDb;
	}
	
	function pageCode ($aPage = array(), $aPageCont = array(), $bAdminMode) {
        if (!empty($aPage)) {
            foreach ($aPage as $sKey => $sValue)
                $GLOBALS['_page'][$sKey] = $sValue;
        }
        if (!empty($aPageCont)) {
            foreach ($aPageCont as $sKey => $sValue)
                $GLOBALS['_page_cont'][$aPage['name_index']][$sKey] = $sValue;
        }
        if (!$bAdminMode)
            PageCode($this);
        else
            PageCodeAdmin();
    }

    function displayAccessDenied() {
        return MsgBox(_t('_Access denied'));
    }
}

?>
