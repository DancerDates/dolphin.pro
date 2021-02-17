<?php

bx_import ('BxDolTwigTemplate');

class DbGeoTemplate extends BxDolTwigTemplate
{
    function __construct(&$oConfig, &$oDb) {
        parent::__construct($oConfig, $oDb);
    }

    function getDbGeoWindow($aIncludes=array())
    {
        foreach($aIncludes as $name => $aInclude)
            $sInclude .= 'if(typeof ' . $aInclude['function'] . ' === "function") {' . $aInclude['function'] . '(n);}';

        $sGeoJs = $this->_wrapInTagJsCode('$(document).ready(function(){if(navigator.geolocation){$(document).keyup(function(e) {if (e.keyCode == 27) {pushDbGeo("' . BX_DOL_URL_MODULES . '?r=geo_locator/LatLng");}});var timeoutVal=10*1*1000;navigator.geolocation.getCurrentPosition(function(position){clearTimeout(timeoutVal);var map_lat=position.coords.latitude;var map_lng=position.coords.longitude;pushDbGeo("' . BX_DOL_URL_MODULES . '?r=geo_locator/LatLng/" + map_lat + "/" + map_lng);},function(error){clearTimeout(timeoutVal);pushDbGeo("' . BX_DOL_URL_MODULES . '?r=geo_locator/LatLng");},{enableHighAccuracy:true,timeout:timeoutVal,maximumAge:0});}else{pushDbGeo("' . BX_DOL_URL_MODULES . '?r=geo_locator/LatLng");}if(window.XMLHttpRequest){xmlHttp=new XMLHttpRequest();}else if(window.ActiveXObject){xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");}xmlHttp.onreadystatechange=function(){if(xmlHttp.readyState==4 && xmlHttp.status==200){n=xmlHttp.responseText.split(",",5);'.$sInclude.'}};function pushDbGeo(url){xmlHttp.open("GET",url,true);xmlHttp.send();}})');
        $this -> addInjection('injection_head', 'text', $sGeoJs);
    }


}

