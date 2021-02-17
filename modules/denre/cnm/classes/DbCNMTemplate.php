<?php
bx_import ('BxDolTwigTemplate');

class DbCNMTemplate extends BxDolTwigTemplate
{    
    function __construct(&$oConfig, &$oDb)
    {
        parent::__construct($oConfig, $oDb);
    }

    function getMessageBlock($aOptions)
    {
        $sHeaderTag = $this->_wrapInTagJs('modules/denre/cnm/templates/base/js/jquery.cookiebar.js');
        $this->addInjection('injection_header', 'text', $sHeaderTag);

        $this->addInjection('injection_footer', 'text', $this->_wrapOptions($aOptions));

        $this->addCss('jquery.cookiebar.css');
    }

    function _wrapOptions($aOptions)
    {
        foreach($aOptions as $sKey => $sValue)
            $sOptions .= $sKey . ': ' . $sValue . ', ';

        return $this->_wrapInTagJsCode('$(document).ready(function(){$.cookieBar({' . $sOptions . '});});');
    }

}

