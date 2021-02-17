<?php
/***************************************************************************
*                                 GeoDistance
*                              -------------------
*     copyright (C) 2013 Dately
*
*     This is a commercial product made by Dately
*     Do not copy, reproduce, distribute, sell or offer it for sale, publish,
*     display, perform, modify, create derivative works, transmit, or in any
*     way exploit any content of this module without written permission by
*     the author. For each domain you want to install this module you need its own license!
*
*     Email: dolmods@gmail.com
*
***************************************************************************/

bx_import('BxDolModuleDb');

class DatelyGeoDDb extends BxDolModuleDb {

    function DatelyGeoDDb(&$oConfig) {
		parent::__construct(); 
        $this->_sPrefix = $oConfig->getDbPrefix();

    }

    function getSettingsCategory() {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Geo Distance' LIMIT 1");
    }

    function getProfileById($iProfileId = 0) {
        return $this->getRow("SELECT `m`.`id`, `m`.`part`, `p`.`Avatar`, `m`.`lat`, `m`.`lng`, `m`.`zoom`, `m`.`type`, `m`.`address`, `m`.`country`, `m`.`privacy` FROM `bx_wmap_locations` AS `m` INNER JOIN `Profiles` AS `p` ON (`p`.`ID` = `m`.`id`) WHERE `m`.`failed` = 0 AND `p`.`Status` = 'Active' AND `m`.`id` = '$iProfileId' AND `m`.`part` = 'profiles' LIMIT 1");
    }

    function getParameterFromSettings($sParameter = "")
    {
	return $this->getParam($sParameter);
    }

}

?>
