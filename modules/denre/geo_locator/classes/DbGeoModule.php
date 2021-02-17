<?php

    bx_import('BxDolModuleDb');
    bx_import('BxDolModule');
    bx_import('BxDolDbGeo');

    require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

    class DbGeoModule extends BxDolModule
    {
        var $_sProto = 'http';
        var $aDbGeoParts;
        var $DbGeo;
        var $oDbGeo;

        function __construct(&$aModule) 
        {
            parent::__construct($aModule);

            // prepare the location link ;
            $this->sPathToModule  = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();
            $this->aModuleInfo    = $aModule;

            if(!isset($_SERVER['REMOTE_ADDR']) || getParam('db_geo_activated') != 'on')
                return;

            $this->oDbGeo = new BxDolDbGeo($aModule);
            $this->aDbGeoParts = $this->oDbGeo->getParts('part');

            if (0 == strncmp('https', BX_DOL_URL_ROOT, 5))
                $this->_sProto = 'https';
        }

        function actionLatLng($lat=FALSE, $lng=FALSE)
        {
            //lets get the ip address
            $iIp = sprintf("%u", ip2long(getVisitorIP()));

            if (is_numeric($lat) && is_numeric($lng))
            {
                //this is a real request, lets get GEO info
                $geo = $lat.','.$lng;
                $DbGeo = $this->oDbGeo->getGeo($geo, 'update');

                //Change the value for ip in the array to correct ip
                $DbGeo["ip"] = $iIp;
            } else if(!isset($this->oDbGeo->lat) || ($this->oDbGeo->lat == '0.000000' && $this->oDbGeo->lng == '0.000000'))
                //lets get location from ip
                $DbGeo = $this->oDbGeo->getGeo($iIp, 'update');
            else
                $DbGeo = (array) $this->oDbGeo;

            //lets send the requested information back to the client
            echo $DbGeo["latitude"].','.$DbGeo["longitude"].','.$DbGeo["country_code"].','.$DbGeo["region_code"].','.$DbGeo["city"].','.$DbGeo["zipcode"];
        }

        function serviceResponse()
        {
            if(php_sapi_name() == "cli" || getParam('db_geo_activated') != 'on')
                return;

            //lets make sure we don't know the address and that this member is not logging into the system
            if((!isset($this->oDbGeo->latitude) || ($this->oDbGeo->latitude == '0.000000' && $this->oDbGeo->longitude == '0.000000')) && $_SERVER['REQUEST_URI'] != '/member.php')
            {
                //set cookie so we don't have to ask again
                setcookie("DbGeo", 'checkDone2');

                $this->_oTemplate->pageStart();
                $aVars = array ();

                //get includes
                $aIncludes = $this->_oDb->getIncludes();

                $this->_oTemplate->getDbGeoWindow($aIncludes); //this is a new session
            }
            $this->_oDb->oParams->_aParams["default_country"] = $this->DbGeo["country"];
        } // endd function serviceResponse

        function serviceHomepageBlock ()
        {
            //lets make sure we have a centre to display
            $fLat = ($this->oDbGeo->latitude + 0 == 0 || getParam('db_geo_map') != 'on') ? getParam('bx_wmap_homepage_lat') : $this->oDbGeo->latitude;
            $fLng = ($this->oDbGeo->longitude + 0 == 0 || getParam('db_geo_map') != 'on') ? getParam('bx_wmap_homepage_lng') : $this->oDbGeo->longitude;

            $iZoom = getParam('bx_wmap_homepage_zoom');
            $bPartsSelector = (getParam('db_geo_part_selector') == 'on') ? TRUE : FALSE;

            $p = array
            (
                'fLat'     => $fLat,
                'fLng'     => $fLng,
                'iZoom'    => $iZoom,
                'sPartsCustom' => '',
                'sCustom' => '',
                'sSubclass' => 'bx_wmap_homepage',
                'sParamPrefix' => 'bx_wmap_homepage',
                'sSuffix' => 'Home',
                'sSaveLocationSuffix' => 'homepage',
                'isPartsSelector' => $bPartsSelector,
            );

            $this->_oTemplate->addJs ('home.js');
            $this->_oTemplate->addJs ($this->_sProto . '://www.google.com/jsapi?key=' . getParam('bx_wmap_key'));
            $this->_oTemplate->addJs (BX_DOL_URL_MODULES.'boonex/world_map/js/BxWmap.js');
            $this->_oTemplate->addCss (BX_DOL_URL_MODULES.'boonex/world_map/templates/base/css/main.css');

            return BxDolService::call('wmap', 'SeparatePageBlock', $p);
        }

        function serviceHomepagePartBlock ($sPart)
        {
            //lets make sure we have a centre to display
            $fLat = ($this->oDbGeo->latitude + 0 == 0 || getParam('db_geo_map') != 'on') ? getParam('bx_wmap_homepage_lat') : $this->oDbGeo->latitude;
            $fLng = ($this->oDbGeo->longitude + 0 == 0 || getParam('db_geo_map') != 'on') ? getParam('bx_wmap_homepage_lng') : $this->oDbGeo->longitude;

            $iZoom = false; //getParam('bx_wmap_home_'.$sPart);

            $p = array
            (
                'fLat'     => $fLat,
                'fLng'     => $fLng,
                'iZoom'    => $iZoom,
                'sPartsCustom' => $sPart,
                'sCustom' => '',
                'sSubclass' => 'bx_wmap_homepage',
                'sParamPrefix' => 'bx_wmap_home_'.$sPart,
                'sSuffix' => 'PartHome',
                'sSaveLocationSuffix' => 'part_home/'.$sPart,
                'isPartsSelector' => false
            );

            $this->_oTemplate->addJs ('part.js');
            $this->_oTemplate->addJs ($this->_sProto . '://www.google.com/jsapi?key=' . getParam('bx_wmap_key'));
            $this->_oTemplate->addJs (BX_DOL_URL_MODULES.'boonex/world_map/js/BxWmap.js');
            $this->_oTemplate->addCss (BX_DOL_URL_MODULES.'boonex/world_map/templates/base/css/main.css');

            return BxDolService::call('wmap', 'SeparatePageBlock', $p);
        }

        function serviceLocationBlock ($sPart, $iEntryId)
        {
            if (!isset($this->aDbGeoParts[$sPart]))
                return '';

            $iEntryId = (int)$iEntryId;


            if($r = $this->oDbGeo->getDirectLocation($iEntryId, $this->aDbGeoParts[$sPart]))
            {
                $this->_oTemplate->addLocation('dbgeo', BX_DIRECTORY_PATH_MODULES.'denre/geo_locator/', BX_DOL_URL_MODULES.'denre/geo_locator/');

                if($aRouteTxt = $this->oDbGeo->getMyRoute($r['lat'], $r['lng']))
                {
                    foreach($aRouteTxt['route'] as $iId => $sRouteTxt)
                        $aVars['bx_repeat:route_txt'][] = array (
                            'line' => $sRouteTxt,
                        );

                    $aVars['distance'] = $aRouteTxt['distance']['text'];
                    $aVars['db_geo_distance'] = _t('db_geo_distance');

                    $tRouteTxt = $this->_oTemplate->parseHtmlByName('routetxt.html', $aVars);
                } else
                {
                    $aVars['bx_repeat:route_txt'][] = array (
                        'line' => _t('db_geo_no_route_available'),
                    );

                    $aVars['distance'] = _t('db_geo_distance_unknown');
                    $aVars['db_geo_distance'] = _t('db_geo_distance');
                }

                // worldmap gives conflict with routemap
                $aWorldMap = BxDolService::call('wmap', 'location_block', array($sPart, $iEntryId));
                $aTemplateKeys = array(
                    'db_geo_map_title' => _t('db_geo_map_title'),
                    'db_geo_map_map' => $aWorldMap[0],
                    'db_geo_route_map_title' => _t('db_geo_route_map_title'),
                    //'db_geo_route_map' => $this->oDbGeo->getMyRouteMap($r['lat'], $r['lng']),
                    'db_geo_route_title' => _t('db_geo_route_title'),
                    'db_geo_route' => $tRouteTxt,
                );
               
                $this->_oTemplate->addJs ('home.js');
                $this->_oTemplate->addCss('default.css');
                $this->_oTemplate->addCss('tabs.css');
                return $this->_oTemplate->parseHtmlByName('tabs.html', $aTemplateKeys);
            }
        }

        function serviceGetMyDistance($iObjectId, $sPart)
        {
            (int) $iObjectId;

            $aTableOptions = array(
                'part' => $sPart,
            );

            $aObjectInfo = $this->oDbGeo->getDirectLocation($iObjectId, $aTableOptions);

            //set variables for latitude and longitude
            if($aObjectInfo['lat'] == '' || $aObjectInfo['lng'] == '' || $aObjectInfo['lat'] == 0 || $aObjectInfo['lng'] == 0)
                $iDistance = _t('db_geo_distance_unknown');
            else
            {
                $iLatitude = $aObjectInfo['lat'];
                $iLongitude = $aObjectInfo['lng'];

                //get the distance
                $sUnit = getParam('db_geo_units') == 'kilometers' ? 'K' : '';
                $iDistance = number_format($this->oDbGeo->getMyDistance($iLatitude, $iLongitude, $sUnit), 1) . ' ' ._t(getParam('db_geo_units'));
            }
            return $iDistance;
        }

        function serviceShow($inp)
        {
            if(getParam('db_geo_activated') != 'on' || getParam('db_geo_form') != 'on')
                return;

            $aProfile['Country'] = $this->oDbGeo->country_code;

            if(isset($inp->aExtras['form_object']->aInputs))
            {
                foreach($inp->aExtras['form_object']->aInputs as $key=>$value)
                {
                    if($value['type'] == 'text' && ($value['name'] == 'City[0]' || strtolower($value['name']) == 'city') && !$value['value'])
                        $inp->aExtras['form_object']->aInputs[$key]['value'] = $this->oDbGeo->city;
                    if($value['type'] == 'text' && $value['name'] == 'Region[0]' && !$value['value'])
                        $inp->aExtras['form_object']->aInputs[$key]['value'] = $this->oDbGeo->region;
                    if($value['type'] == 'select' && $value['name'] == 'Country[0]' && !$value['value'])
                        $inp->aExtras['form_object']->aInputs[$key]['value'] = $this->oDbGeo->country_code;
                    if(($value['type'] == 'select_box' || $value['type'] == 'select') && strtolower($value['name']) == 'country')
                        $inp->aExtras['form_object']->aInputs[$key]['value'] = $this->oDbGeo->country_code;
                }
            }
            $this->_oTemplate->addJs ('form.js');
        }

        function serviceGeoUpdate($inp)
        {
            if($inp->aExtras['part'] == 'profiles')
            {
                $latlng = $inp->aExtras['location']['lat'].','.$inp->aExtras['location']['lng'];
                $ip = sprintf("%u", ip2long(getVisitorIP()));

                $this->oDbGeo->getGeo($latlng, 'update');
            }
        }

        function serviceModuleInstall($sModuleUri)
        {
            $this->_oDb->InstallSupport($sModuleUri);
        }

        function serviceModuleUninstall($sModuleUri)
        {
            $aSupportedModules = explode(',', getParam('db_geo_modules'));

            if(($key = array_search($sModuleUri, $aSupportedModules)) !== false)
                unset($aSupportedModules[$key]);

            $aSupportedModules = array_unique($aSupportedModules);
            asort($aSupportedModules);
            $sSupportedModules = implode(',', $aSupportedModules);
            setParam('db_geo_modules', $sSupportedModules);

            $this->_oDb->RemoveBlock($sModuleUri);
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
                $this->_oTemplate->pageCodeAdmin (_t('_db_geo'));
                return;
            }

            bx_import('BxDolAdminSettings');

            $mixedResult = '';
            if(isset($_POST['save']) && isset($_POST['cat']))
            {
                $aPrevSupported = array_filter(explode(',', getParam('db_geo_modules')));

                $oSettings = new BxDolAdminSettings($iId);
                $mixedResult = $oSettings->saveChanges($_POST);

                $aNewSupported = array_filter(explode(',', getParam('db_geo_modules')));

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

            echo DesignBoxAdmin (_t('_db_geo'), $sResult);
            
            $this->_oTemplate->pageCodeAdmin (_t('_db_geo'));
        }   

        function isAdmin ()
        {
            return $GLOBALS['logged']['admin'] || $GLOBALS['logged']['moderator'];
        }

    }

