<?php
bx_import('BxDolModuleDb');

class DbCNMDb extends BxDolModuleDb
{
    var $_oConfig;

    function __construct(&$oConfig)
    {
        parent::__construct();
    }

    function getSettingsCategory()
    {
        $ret = $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'DbCNM' LIMIT 1");
        if(isset($ret))
            return $ret;
        else
            return false;
    }

}

