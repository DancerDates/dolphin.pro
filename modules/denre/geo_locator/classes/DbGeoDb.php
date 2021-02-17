<?php

bx_import('BxDolModuleDb');
bx_import('BxDolCacheUtilities');

class DbGeoDb extends BxDolModuleDb
{
    var $oCacheUtilities;
    var $_sTableMain;

    function __construct(&$oConfig)
    {
        parent::__construct();
        $this->_sPrefix = $oConfig->getDbPrefix();
        $this->oCacheUtilities = new BxDolCacheUtilities();
        $this->_sTableMain = 'db_geo_locations';
    }

    function getSettingsCategory()
    {
        $ret = $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'DB GEO Admin' LIMIT 1");
        if(isset($ret))
            return $ret;
        else
            return false;
    }

    function getIncludes()
    {
        return $this->getAll("SELECT `function` FROM `db_geo_includes`");
    }

    function InstallSupport($sModuleUri)
    {
        if($sSupportedModule = $this->getOne("SELECT `m`.`uri` FROM `sys_modules` `m` RIGHT JOIN `bx_wmap_parts` `w` ON `m`.`uri` = `w`.`part` WHERE `m`.`id` IS NOT NULL AND `m`.`uri` = '$sModuleUri'"))
        {
            $sSupportedModules = $this->getOne("SELECT `AvailableValues` FROM `sys_options` WHERE `name` = 'db_geo_modules' LIMIT 1");

            $aSupportedModules = explode(',', $sSupportedModules);
            $aSupportedModules[] = $sSupportedModule;

            $aSupportedModules = array_unique($aSupportedModules);
            asort($aSupportedModules);
            $sSupportedModules = implode(',', $aSupportedModules);
            $this->query("UPDATE `sys_options` SET `AvailableValues` = '$sSupportedModules' WHERE `name` = 'db_geo_modules' LIMIT 1");

            $this->oCacheUtilities->clear('db');
        }
    }

    function RemoveSupport($sModuleUri)
    {
        $sSupportedModules = $this->getOne("SELECT `AvailableValues` FROM `sys_options` WHERE `name` = 'db_geo_modules' LIMIT 1");
        $aSupportedModules = explode(',', $sSupportedModules);

        if(($key = array_search($sModuleUri, $aSupportedModules)) !== false)
            unset($aSupportedModules[$key]);

        $aSupportedModules = array_unique($aSupportedModules);
        asort($aSupportedModules);
        $sSupportedModules = implode(',', $aSupportedModules);
        $this->query("UPDATE `sys_options` SET `AvailableValues` = '$sSupportedModules' WHERE `name` = 'db_geo_modules' LIMIT 1");

        $this->oCacheUtilities->clear('db');
    }

    function CreateBlock($sModuleUri)
    {
        $sModuleUri = trim($sModuleUri);

        if($sModuleUri=='ads')
        {
            $this->query("INSERT INTO `sys_page_compose` SELECT NULL, `Page`, `PageWidth`, 'DB GEO', '_db_geo', 0, 0, 'PHP', REPLACE(`Content`, 'wmap', 'geo_locator'), `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, 0 FROM `sys_page_compose` WHERE `Page` = 'ads' AND `Content` LIKE '%call(''wmap''%'");
            $this->query("INSERT INTO `sys_page_compose` SELECT NULL, `Page`, `PageWidth`, 'DB GEO', '_db_geo', 0, 0, 'PHP', REPLACE(`Content`, 'wmap', 'geo_locator'), `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, 0 FROM `sys_page_compose` WHERE `Page` = 'ads_home' AND `Content` LIKE '%call(''wmap''%'");
        }else
        {
            $oModuleDb = new BxDolModuleDb();
            $aModuleInfo = $oModuleDb->getModuleByUri($sModuleUri);
            $sModulePrefix = rtrim($aModuleInfo['db_prefix'], '_');

            if($sModulePrefix=='')
                return;

            $this->query("INSERT INTO `sys_page_compose` SELECT NULL, `Page`, `PageWidth`, 'DB GEO', '_db_geo', 0, 0, 'PHP', REPLACE(`Content`, 'wmap', 'geo_locator'), `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, 0 FROM `sys_page_compose` WHERE `Page` LIKE '{$sModulePrefix}%' AND `Content` LIKE '%call(''wmap''%'");
        }

        $this->oCacheUtilities->clear('db');
    }

    function RemoveBlock($sModuleUri)
    {
        $sModuleUri = trim($sModuleUri);

        if($sModuleUri=='ads')
        {
            $this->query("DELETE FROM `sys_page_compose` WHERE `Page` = 'ads' AND `Caption` = '_db_geo'");
            $this->query("DELETE FROM `sys_page_compose` WHERE `Page` = 'ads_home' AND `Caption` = '_db_geo'");
        }else
        {
            $oModuleDb = new BxDolModuleDb();
            $aModuleInfo = $oModuleDb->getModuleByUri($sModuleUri);
            $sModulePrefix = rtrim($aModuleInfo['db_prefix'], '_');

            if($sModulePrefix=='')
                return;

             $this->query("DELETE FROM `sys_page_compose` WHERE `Page` LIKE '{$sModulePrefix}%' AND `Caption` = '_db_geo'");
        }

        $this->oCacheUtilities->clear('db');
    }

}

