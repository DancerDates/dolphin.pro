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

bx_import ('BxDolTwigTemplate');

class deanoCronMonitorTemplate extends BxDolTwigTemplate {
    
	function deanoCronMonitorTemplate(&$oConfig, &$oDb) {
	    parent::__construct($oConfig, $oDb);
    }
}

?>