<?php

bx_import('BxDolModuleDb');
bx_import('BxDolCacheUtilities');

class DbWeatherDb extends BxDolModuleDb
{
    var $oCacheUtilities;

        
    function __construct(&$oConfig)
    {
        parent::__construct();
        $this->_sPrefix = $oConfig->getDbPrefix();
        $this->oCacheUtilities = new BxDolCacheUtilities();
        
    }

    function getSettingsCategory()
    {
        $iId = $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Weather Admin' LIMIT 1");
        if(isset($iId))
            return $iId;
        else
            return false;
    }

    function InstallSupport($sModuleUri)
    {
        if($sSupportedModule = $this->getOne("SELECT `m`.`uri` FROM `sys_modules` `m` RIGHT JOIN `bx_wmap_parts` `w` ON `m`.`uri` = `w`.`part` WHERE `m`.`id` IS NOT NULL AND `m`.`uri` = '$sModuleUri'"))
        {
            $sSupportedModules = $this->getOne("SELECT `AvailableValues` FROM `sys_options` WHERE `name` = 'db_weather_modules' LIMIT 1");

            $aSupportedModules = explode(',', $sSupportedModules);
            $aSupportedModules[] = $sSupportedModule;

            $aSupportedModules = array_unique($aSupportedModules);
            asort($aSupportedModules);
            $sSupportedModules = implode(',', $aSupportedModules);
            $this->query("UPDATE `sys_options` SET `AvailableValues` = '$sSupportedModules' WHERE `name` = 'db_weather_modules' LIMIT 1");

            $this->oCacheUtilities->clear('db');
        }        
    }

    function RemoveSupport($sModuleUri)
    {
        $sSupportedModules = $this->getOne("SELECT `AvailableValues` FROM `sys_options` WHERE `name` = 'db_weather_modules' LIMIT 1");
        $aSupportedModules = explode(',', $sSupportedModules);

        if(($key = array_search($sModuleUri, $aSupportedModules)) !== false)
            unset($aSupportedModules[$key]);

        $aSupportedModules = array_unique($aSupportedModules);
        asort($aSupportedModules);
        $sSupportedModules = implode(',', $aSupportedModules);
        $this->query("UPDATE `sys_options` SET `AvailableValues` = '$sSupportedModules' WHERE `name` = 'db_weather_modules' LIMIT 1");

        $this->oCacheUtilities->clear('db');
    }

    function GeoSupported()
    {
        return $this->getOne("SHOW TABLES LIKE '%db_geo_includes%'");
    }

    function addGeoSupport()
    {
        $this->query("INSERT INTO `db_geo_includes` (`name`, `function`) VALUES ('weather', 'geo_weather')");

    }

    function removeGeoSupport()
    {
        $this->query("DELETE FROM `db_geo_includes` WHERE `name` = 'weather'");
    }

    function CreateBlock($sModuleUri)
    {
        $sModuleUri = trim($sModuleUri);

        $oModuleDb = new BxDolModuleDb();
        $aModuleInfo = $oModuleDb->getModuleByUri($sModuleUri);
        $sModulePrefix = rtrim($aModuleInfo['db_prefix'], '_');

        if($sModulePrefix=='')
            return;

        if($sModuleUri=='events')
            $this->query("INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES ('{$sModulePrefix}_view', '1140px', 'Weather block', '_db_weather', '0', '0', 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''weather'', ''weather_integration'', array(''{$sModuleUri}'', \$this->aDataEntry[''ID'']));', '1', '28.1', 'non,memb', '0')");    
        else if($sModuleUri=='ads')
            $this->query("INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES ('ads', '1140px', 'Weather block', '_db_weather', '0', '0', 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''weather'', ''weather_integration'', array(''{$sModuleUri}'', \$this->aDataEntry[''ID'']));', '1', '28.1', 'non,memb', '0')");
        else
            $this->query("INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES ('{$sModulePrefix}_view', '1140px', 'Weather block', '_db_weather', '0', '0', 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''weather'', ''weather_integration'', array(''{$sModuleUri}'', \$this->aDataEntry[''id'']));', '1', '28.1', 'non,memb', '0')");   

        $this->oCacheUtilities->clear('db');
    }

    function RemoveBlock($sModuleUri)
    {
        $sModuleUri = trim($sModuleUri);

        $oModuleDb = new BxDolModuleDb();
        $aModuleInfo = $oModuleDb->getModuleByUri($sModuleUri);
        $sModuleDbPrefix = rtrim($aModuleInfo['db_prefix'], '_');

        if($sModuleUri=='ads')
            $this->query("DELETE FROM `sys_page_compose` WHERE `Page`='ads' AND `Caption` = '_db_weather'");
        else
            $this->query("DELETE FROM `sys_page_compose` WHERE `Page`='{$sModuleDbPrefix}_view' AND `Caption` = '_db_weather'");

        $this->oCacheUtilities->clear('db');
    }

}

?>
