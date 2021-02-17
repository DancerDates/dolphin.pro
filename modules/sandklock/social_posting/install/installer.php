<?php

bx_import("BxDolInstaller");

class SkSocialPostingInstaller extends BxDolInstaller
{

    function __construct($aConfig)
    {
        parent::__construct($aConfig);
    }

    function install($aParams)
    {
        $aResult = parent::install($aParams);
        if ($aResult['result']) {
            BxDolService::call($this->_aConfig['home_uri'], 'update_handlers');
        }
        return $aResult;
    }
}

?>
