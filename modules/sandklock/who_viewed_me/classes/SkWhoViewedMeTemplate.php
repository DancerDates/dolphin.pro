<?php
bx_import('BxDolTwigTemplate');

class SkWhoViewedMeTemplate extends BxDolTwigTemplate {
	/*
	* Constructor.
	*/
	function __construct(&$oConfig, &$oDb) {
	    parent::__construct($oConfig, $oDb);
	}
}