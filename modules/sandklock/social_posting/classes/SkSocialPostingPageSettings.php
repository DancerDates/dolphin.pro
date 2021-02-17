<?php

bx_import('BxDolPageView');

class SkSocialPostingPageSettings extends BxDolPageView
{

    var $_oMain;
    var $_sUrl;

    function __construct(&$oMain, $sUrl)
    {
        $this->_oMain = $oMain;
        $this->_sUrl = $sUrl;
        parent::__construct('sk_posting_setting');
    }
    
    function getBlockCode_Settings()
    {
    	return $this->_oMain->genBlockSettings($this->_sUrl);
    }

}

?>
