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

bx_import('BxDolModule');
bx_import('BxDolPageView');

class AqbEventRecModule extends BxDolModule {
	
	/**
	 * Constructor
	 */
	function __construct($aModule) {
		parent::__construct($aModule);
		$this -> iUserId = $GLOBALS['logged']['member'] || $GLOBALS['logged']['admin'] ? $_COOKIE['memberID'] : 0;
	}
	
	function isAdmin(){
		return isAdmin($this->iUserId);
	}
	
	function actionAdministration () {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        
		
		$this -> _oTemplate -> addAdminCss('main.css');
		
        $this -> _oTemplate -> pageStart();      
        $sContent = $this -> _oTemplate -> getSettingsPanel();
        echo $this -> _oTemplate -> adminBlock ($sContent, _t('_aqb_eventrec_admin'));
        $this -> _oTemplate -> pageCodeAdmin(_t('_aqb_eventrec_admin'));
    }	
	
	
	function serviceGetRecurringArea($iEntryId = 0, $aFields){
		$aArea = $this -> _oTemplate -> getRecurringPanel($iEntryId, $aFields);
		$aFields = array_merge(array_slice($aFields, 0, 10), $aArea, array_slice($aFields, 10));
		unset($aFields['EventStart']);
		unset($aFields['EventEnd']);
	
	
		$this -> _oTemplate -> addAdminJs(array('jquery-ui-timepicker-addon.min.js', 'main.js'));
		$this -> _oTemplate -> addAdminCss(array('jquery-ui-timepicker-addon.css', 'main.css'));
		$this -> _oTemplate -> addCss(array('jquery-ui-timepicker-addon.css','main.css'));
		$this -> _oTemplate -> addJs(array('jquery-ui-timepicker-addon.min.js', 'main.js'));

		return $aFields;
	}
	
	
	function serviceGetRecurringBlock($iEventID){				
		return array($this -> _oTemplate -> getRecurInfoBlock($iEventID), array(), '');
	}	
}
?>