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

bx_import('BxDolModule');

class deanoCronMonitorModule extends BxDolModule {
    var $sModuleUrl;
    var $sModulePath;
    var $sPadStart;
    var $sPadEnd;

    function deanoCronMonitorModule(&$aModule) {
	parent::__construct($aModule);
        if($GLOBALS['site']['ver'] == '7.1' || $GLOBALS['site']['ver'] == '7.2') {
            $this->sPadStart = '<div class="bx-def-bc-margin">';
			$this->sLKeySuffix = '_d71';
        } else {
            $this->sPadStart = '<div class="bx_sys_default_padding">';
			$this->sLKeySuffix = '_d70';
        }
        $this->sPadEnd = '</div>';
        $this->sModulePath = BX_DIRECTORY_PATH_MODULES . $aModule['path'];
        $this->sModuleUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

    }

    function actionAdministration () {

        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();


	    $iId = $this->_oDb->getSettingsCategory();
	    if(empty($iId)) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_deano_cron_monitor'));
            return;
        }

        bx_import('BxDolAdminSettings');

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) {
	        $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();

		$iDateInstalled = (int)getParam('deano_cron_monitor_install_date');
		if($iDateInstalled == 0) {
			$iDateInstalled = time();
			setParam('deano_cron_monitor_install_date', $iDateInstalled);
		}
		$aCronJobs = $this->_oDb->getCronJobs();
		$sDateFormat = getParam('deano_cron_monitor_date_format');
		$sResult .= '<div style="height: 24px;"></div>';

		$iRunning = (int)getParam('deano_cron_monitor_last_run');
		$iCurrentTime = time();

		$iLastRun1 = (int)getParam('deano_cron_monitor_last_run');
		$iLastRun2 = (int)getParam('deano_cron_monitor_last_run2');

		if($iLastRun1 != 0 && $iLastRun2 != 0) {
			$iRunDiff = floor($iLastRun1-$iLastRun2);
			$iRunDiff = floor($iRunDiff/60);
		} else {
			$iRunDiff = 0;
		}

		if($iRunning > $iCurrentTime-960) { // Allow for cron runs of no more than 16 minutes apart.
			$sResult .= '<div style="padding-bottom: 20px; color: DarkGreen">' . _t('_deano_cron_monitor_msg_1', date($sDateFormat, $iRunning)) . '</div>';
			if($iRunDiff < 2) {
				$sResult .= '<div style="padding-bottom: 20px; color: DarkGreen">' . _t('_deano_cron_monitor_msg_3') . '</div>';
			} 
			if($iRunDiff > 1) {
				$sResult .= '<div style="padding-bottom: 20px; color: DarkRed">' . _t('_deano_cron_monitor_msg_4', $iRunDiff) . '</div>';
			}
		} else {
			$sResult .= '<div style="padding-bottom: 20px; color: DarkRed;">' . _t('_deano_cron_monitor_msg_2') . '</div>';
		}

		$sResult .= '
<table class="form_advanced_table" cellspacing="0" cellpadding="0">
	<tr>
		<td>' . _t('_deano_cron_monitor_name') . '</td>
		<td>' . _t('_deano_cron_monitor_exp') . '</td>
		<td>' . _t('_deano_cron_monitor_run_in') . '</td>
		<td>' . _t('_deano_cron_monitor_run_time') . '</td>
	</tr>
		';

	    require_once('class.tdcron.php');
		require_once('class.tdcron.entry.php');

		foreach($aCronJobs as $aRow) {
			if($aRow['name'] != 'deano_cron_monitor') {
				// Make sure all 5 cron fields have something in them.
				// Boonex specifies a incorrect format for bx_sounds and bx_videos.
				// This must be corrected before it can be passed to my function to 
				// calculate next cron run time.
				$aRefTime = explode(' ', $aRow['time']);
				if($aRefTime[0] == '') $aRefTime[0] = '*';
				if($aRefTime[1] == '') $aRefTime[1] = '*';
				if($aRefTime[2] == '') $aRefTime[2] = '*';
				if($aRefTime[3] == '') $aRefTime[3] = '*';
				if($aRefTime[4] == '') $aRefTime[4] = '*';
				$aRow['time'] = implode(' ', $aRefTime);
				$sNextRun = $aRow['time'];
				$sNextRun = tdCron::getNextOccurrence($sNextRun, $iCurrentTime+60);
				$sNextRun2 = date($sDateFormat, $sNextRun);

				$iSecondsLeft = $sNextRun-$iCurrentTime;
				if($iSecondsLeft < 60) $sMessage = $iSecondsLeft . ' seconds';

				if($iSecondsLeft >= 60 && $iSecondsLeft < 3600) $sMessage = number_format($iSecondsLeft/60, 2) . ' minutes';

				if($iSecondsLeft >= 3600) $sMessage = number_format($iSecondsLeft/3600, 2) . ' hours';

				$sResult .= '
	<tr>
		<td>' . $aRow['name'] . '</td>
		<td>' . $aRow['time'] . '</td>
		<td>' . $sMessage . '</td>
		<td>' . $sNextRun2 . '</td>
	</tr>
				';
			}
		}
		$sResult .= '
</table>
		';

        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;

		$this->_oTemplate->addCssAdmin('forms_adv.css'); 

		$sResult = $this->sPadStart . $sResult . $this->sPadEnd;
        echo DesignBoxAdmin (_t('_deano_cron_monitor'), $sResult);

        $this->_oTemplate->pageCodeAdmin (_t('_deano_cron_monitor'));
    }

	function serviceCheck() {
		// Dummy service. Just here as a check to determine if the module in installed
		// via the BxDolRequest::serviceExists function.

	}

}

?>