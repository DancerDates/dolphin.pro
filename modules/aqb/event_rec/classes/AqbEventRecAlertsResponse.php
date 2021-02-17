<?php
/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*      
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY. 
* To be able to use this product for another domain names you have to order another copy of this product (license).
* 
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
* 
* This notice may not be removed from the source code.
* 
***************************************************************************/

bx_import('BxDolAlerts');

class AqbEventRecAlertsResponse extends BxDolAlertsResponse {
	var $_oDb = null;
	function __construct() {
	    parent::__construct();
		$this -> _oDb = BxDolModule::getInstance("AqbEventRecModule") -> _oDb;
	}

    function response($oAlert) {
		$sMethodName = '_process' . ucfirst($oAlert->sUnit) . ucfirst($oAlert->sAction);
		if((stristr($_SERVER['PHP_SELF'], $GLOBALS['admin_dir']) === FALSE) && method_exists($this, $sMethodName)) $this -> $sMethodName($oAlert);		
    }
	
	function _processBx_eventsAdd(&$oAlert){
		$this -> _oDb -> updateEventSettings($oAlert -> iObject, $_POST);
	}
	
	function _processBx_eventsChange(&$oAlert){
		$this -> _oDb -> updateEventSettings($oAlert -> iObject, $_POST);
	}
	
	function _processBx_eventsDelete(&$oAlert){
		$this -> _oDb -> deleteEventInfo($oAlert -> iObject);
	}
}
?>