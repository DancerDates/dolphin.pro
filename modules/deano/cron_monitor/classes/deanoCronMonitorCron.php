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

require_once(BX_DIRECTORY_PATH_INC . 'db.inc.php');
bx_import('BxDolCron');
class deanoCronMonitorCron extends BxDolCron
{
    function deanoCronMonitorCron()
    {
    }
    function processing()
    {
		//$sTime = time();
		//file_put_contents(BX_DIRECTORY_PATH_CACHE . 'deano_cron_monitor.dat', $sTime);
		setParam('deano_cron_monitor_last_run3', getParam('deano_cron_monitor_last_run2'));
		setParam('deano_cron_monitor_last_run2', getParam('deano_cron_monitor_last_run'));
		setParam('deano_cron_monitor_last_run', time());

    }
}
