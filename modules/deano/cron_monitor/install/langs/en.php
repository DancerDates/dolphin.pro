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

$sLangCategory = 'Deano - Cron Monitor';

$aLangContent = array(
    '_deano_cron_monitor' => 'Cron Monitor',
    '_deano_cron_monitor_msg_1' => 'Your cron is currently running. Last run time was {0}. Everything seems ok.',
    '_deano_cron_monitor_msg_2' => 'Cron is not running. If it has been less than 24 hours since you installed the module, then wait at least 24 hours. Other wise you most likley have a problem with the cron settings.',
	'_deano_cron_monitor_msg_3' => 'Your cron jobs appear to be running at 1 minute intervals. Ok.',
	'_deano_cron_monitor_msg_4' => 'Your cron jobs appear to be running at {0} minute intervals. Cron jobs for dolphin should be setup to run every minute. Some cron jobs that are scheduled to be run more often than {0} minutes apart will be skipped. You should change your cron job run time to 1 minute intervals if possible.',
	'_deano_cron_monitor_name' => '<b>Cron Job Name</b>',
	'_deano_cron_monitor_exp' => '<b>Cron Expression</b>',
	'_deano_cron_monitor_run_in' => '<b>Next Run In...</b>',
	'_deano_cron_monitor_run_time' => '<b>Next Run Date</b>',

);

?>