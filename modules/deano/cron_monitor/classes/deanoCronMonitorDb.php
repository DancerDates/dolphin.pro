<?php
/***************************************************************************
* Date				: Sunday January 26, 2014
* Copywrite			: (c) 2014 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Cron Monitor
* Product Version	: 1.0.2
*
* IMPORTANT: This is a commercial product made by Dean J. Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean J. Bassett Jr.
*
***************************************************************************/

bx_import('BxDolModuleDb');

class deanoCronMonitorDb extends BxDolModuleDb {

	function deanoCronMonitorDb(&$oConfig) {
		parent::__construct();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }

    function getSettingsCategory() {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Deano - Cron Monitor' LIMIT 1");
    }

	function getCronJobs() {
		$sQuery = "SELECT * FROM `sys_cron_jobs`";
		return $this->getAll($sQuery);
	}

	function setNextRun($iId, $iTime) {
		$sQuery = "UPDATE `sys_cron_jobs` SET `nextRun` = '$iTime' WHERE `id` = '$iId'";
		return $this->query($sQuery);
	}


}

?>