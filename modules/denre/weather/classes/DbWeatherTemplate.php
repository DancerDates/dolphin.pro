<?php

bx_import ('BxDolTwigTemplate');

class DbWeatherTemplate extends BxDolTwigTemplate
{
    function __construct(&$oConfig, &$oDb) {
    parent::__construct($oConfig, $oDb);
    }

    function weatherBlock($sCity, $sCountryCode)
    {
        $sUnits = getParam('db_weather_units');
        $sAPIKey = getParam('db_weather_api_key');

        if($sAPIKey == '')
            return;

        if($sCity=='' || $sCountryCode=='')
        {
            //get default location
            $sCity = getParam('db_weather_city');
            $sCountryCode = getParam('db_weather_country');
        }

        $sCity = rawurlencode($sCity);
        $sUrl = "https://api.openweathermap.org/data/2.5/weather?q=".$sCity.",".$sCountryCode."&units=".$sUnits."&APPID=".$sAPIKey;
        $aData = file_get_contents($sUrl);

        $oParser = new Services_JSON();
        $json = $oParser->decode($aData, TRUE);

        if($json->cod!='200')
            return;

        $icon = $json->weather[0]->icon;
        $aVars = array(
            'city'        => $json->name,
            'day'         => date('l H:i', $json->dt),
            'min_temp'    => round($json->main->temp_min),
            'max_temp'    => round($json->main->temp_max),
            'temp'        => round($json->main->temp),
            'humidity'    => $json->main->humidity,
            'pressure'    => $json->main->pressure,
            'sunrise'     => date('H:i', $json->sys->sunrise),
            'sunset'      => date('H:i', $json->sys->sunset),
            'description' => $json->weather[0]->description,
            'icon'        => "https://openweathermap.org/img/w/$icon.png",
        );

        return $this->parseHtmlByName("block_main", $aVars);
    }

}

?>
