<?php

bx_import('BxDolModuleDb');
bx_import('BxDolModule');

require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

class DbWeatherModule extends BxDolModule
{
    function DbWeatherModule(&$aModule) 
    {
        parent::__construct($aModule);

        if(php_sapi_name() == "cli" || getParam('db_geo_activated') != 'on')
            return;
    }

    function actionWeatherBlock()
    {
        $sCity = bx_get('city');
        $sCountryCode = bx_get('country_code');
        if($sCity!='' && $sCountryCode!='')
            echo $this->serviceWeatherBlock($sCity, $sCountryCode, true);
    }

    function serviceWeatherBlock($sCity='', $sCountryCode='', $bAjax=false)
    {
        if(($sCity=='' || $sCountry=='') && getParam('db_weather_locator') == 'on')
        {
            if(file_exists(BX_DIRECTORY_PATH_CLASSES . 'BxDolDbGeo.php'))
                bx_import('BxDolDbGeo');

            $oDbGeo = new BxDolDbGeo();
            $sCity = $oDbGeo->city;
            $sCountryCode = $oDbGeo->country_code;
        }

        if($sCity=='' || $sCountryCode=='')
        {
            $aProfile = getProfileInfo(getLoggedId());

            $sCity = $aProfile['City']; 
            $sCountryCode = $aProfile['Country'];
        }

        if(!$bAjax)
        {
            $this->_oTemplate->addJs ('weather.js');
            $this->_oTemplate->addCss ('main.css');
        }

        return $this->_oTemplate->weatherBlock($sCity, $sCountryCode);

    }

    function serviceWeatherIntegration($sModuleUri='', $iObjectId=0, $bAjax=false)
    {
        if($sModuleUri=='' || $iObjectId==0)
            return;

        if('NoUri' == $sModuleUri)
        {
            $aProfile = getProfileInfo($iObjectId);

            $sCity = $aProfile['City'];
            $sCountryCode = $aProfile['Country'];
        } else
        {
            $oModuleDb = new BxDolModuleDb();
            $aModuleInfo = $oModuleDb->getModuleByUri($sModuleUri);

            $sModuleClass = $aModuleInfo['class_prefix'] . 'Module';

            if(!$oModule = BxDolModule::getInstance($sModuleClass))
                return;

            if(!$aData = $oModule->_oDb->getEntryById($iObjectId))
                return;

            if('events' == $sModuleUri)
            {
                $sCity = $aData['City'];
                $sCountryCode = $aData['Country'];
            }else
            {
                $sCity = $aData['city'];
                $sCountryCode = $aData['country'];
             }
        }

        $this->_oTemplate->addJs ('weather.js');
        $this->_oTemplate->addCss ('main.css');
        return $this->_oTemplate->weatherBlock($sCity, $sCountryCode);

    }

    function serviceModuleInstall($sModuleUri)
    {
        $this->_oDb->InstallSupport($sModuleUri);
    }

    function serviceModuleUninstall($sModuleUri)
    {
        $aSupportedModules = explode(',', getParam('db_weather_modules'));

        if(($key = array_search($sModuleUri, $aSupportedModules)) !== false)
            unset($aSupportedModules[$key]);

        $aSupportedModules = array_unique($aSupportedModules);
        asort($aSupportedModules);
        $sSupportedModules = implode(',', $aSupportedModules);
        setParam('db_weather_modules', $sSupportedModules);

        $this->_oDb->RemoveBlock($sModule);
        $this->_oDb->RemoveSupport($sModuleUri);
    }

    function actionAdministration ()
    {
        if (!isAdmin())
        {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $iId = $this->_oDb->getSettingsCategory();
        if(empty($iId))
        {
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_db_weather'));
            return;
        }

        bx_import('BxDolAdminSettings');

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat']))
        {
            $aPrevSupported = array_filter(explode(',', getParam('db_weather_modules')));
            $aPrevLocator = getParam('db_weather_locator');

            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);

            $aNewSupported = array_filter(explode(',', getParam('db_weather_modules')));
            $aNewLocator = getParam('db_weather_locator');

            if($aNewLocator != $aPrevLocator)
            {
                if($this->_oDb->GeoSupported())
                {
                    if($aNewLocator == 'on')
                        $this->_oDb->addGeoSupport();
                    elseif($aNewLocator == '')
                        $this->_oDb->removeGeoSupport();
                }
            }

            if(!empty($aPrevSupported))
            {
                $aSupportRemoved = array_diff($aPrevSupported, $aNewSupported);
                foreach($aSupportRemoved as $sModule)
                    $this->_oDb->RemoveBlock($sModule);
            }

            if(!empty($aNewSupported))
            {
                $aSupportAdded = array_diff($aNewSupported, $aPrevSupported);
                foreach($aSupportAdded as $sModule)
                    $this->_oDb->CreateBlock($sModule);
            }

        }

        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();

        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;

        echo DesignBoxAdmin (_t('_db_weather'), $sResult);

        $this->_oTemplate->pageCodeAdmin (_t('_db_weather'));
    }
}

?>
