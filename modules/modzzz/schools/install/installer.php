<?php

/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx School
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolInstaller');

class BxSchoolsInstaller extends BxDolInstaller {

    function __construct($aConfig) {
        parent::__construct($aConfig);
    }

    function install($aParams) {
        $aResult = parent::install($aParams);

        if($aResult['result'] && BxDolRequest::serviceExists('wall', 'update_handlers'))
            BxDolService::call('wall', 'update_handlers', array($this->_aConfig['home_uri'], true));

        if($aResult['result'] && BxDolRequest::serviceExists('spy', 'update_handlers'))
            BxDolService::call('spy', 'update_handlers', array($this->_aConfig['home_uri'], true));
 
		BxDolService::call($this->_aConfig['home_uri'], 'map_install');
 		BxDolService::call($this->_aConfig['home_uri'], 'event_map_install'); 

        return $aResult;
    }

    function uninstall($aParams) {

        if(BxDolRequest::serviceExists('wall', 'update_handlers'))
            BxDolService::call('wall', 'update_handlers', array($this->_aConfig['home_uri'], false));

        if(BxDolRequest::serviceExists('spy', 'update_handlers'))
            BxDolService::call('spy', 'update_handlers', array($this->_aConfig['home_uri'], false));
 
	    $aResult = parent::uninstall($aParams);

        if ($aResult['result'] && BxDolModule::getInstance('BxWmapModule')){
            BxDolService::call('wmap', 'part_uninstall', array($this->_aConfig['home_uri']));
            BxDolService::call('wmap', 'part_uninstall', array($this->_aConfig['home_uri'].'_event'));
		}

        return $aResult;
    }    
}
