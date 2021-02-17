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

bx_import('BxDolConfig');

class AqbEventRecConfig extends BxDolConfig {
    var $_sUri;
	var $_oDb;
	var $_sFormat;
	
	function __construct($aModule) {
	    parent::__construct($aModule);
		$this -> _sUri = $this->getUri();
		$this -> _sFormat = "m/d/Y";
	}
	
	function init(&$oDb) {
		$this -> _oDb = &$oDb;
	}	

	function isRecurringOptionEnabled(){
		return $this -> _oDb -> getParam($this -> _sUri . '_enable_rec_option') == 'on';
	}
	
	function updateEventsClean(){
		return (int)$this -> _oDb -> getParam($this -> _sUri . '_enable_rec_clean_part') == 'on';
	}	
	
	function formatDate($i, $iShort = BX_DOL_LOCALE_DATE_SHORT){
		return getLocaleDate($i, $iShort) . ' ('.defineTimeInterval($i) . ')';
	}
}
?>