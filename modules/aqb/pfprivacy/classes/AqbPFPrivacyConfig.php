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

class AqbPFPrivacyConfig extends BxDolConfig {
    var $_oDb;

    var $_bViaPedit;

    var $_aHtmlIds;
    var $_aJsClasses;
	var $_aJsObjects;
    var $_sAnimationEffect;
    var $_iAnimationSpeed;

	/**
	 * Constructor
	 */
	function __construct($aModule) {
	    parent::__construct($aModule);
	}

	function init(&$oDb) {
	    $this->_oDb = &$oDb;

	    $this->_bViaPedit = $this->_oDb->getParam('aqb_pfprivacy_via_pedit') == 'on';

		$this->_aHtmlIds = array(
			'form_edit' => 'aqb-pfprivacy-edit',
		);

		$this->_aJsClasses = array(
			'main' => 'AqbPFPrivacyMain'
		);
		$this->_aJsObjects = array(
			'main' => 'oAqbPFPrivacyMain'
		);

	    $this->_sAnimationEffect = 'fade';
	    $this->_iAnimationSpeed = 'slow';
	}
	function isViaPedit() {
		return $this->_bViaPedit;
	}
	function getJsClass($sType = '') {
		if(empty($sType))
			return $this->_aJsClasses;

		return $this->_aJsClasses[$sType];
	}
	function getJsObject($sType = '') {
		if(empty($sType))
			return $this->_aJsObjects;

		return $this->_aJsObjects[$sType];
	}
	function getHtmlId($sType = '') {
		if(empty($sType))
			return $this->_aHtmlIds;

		return $this->_aHtmlIds[$sType];
	}
	function getAnimationEffect() {
	    return $this->_sAnimationEffect;
	}
	function getAnimationSpeed() {
	    return $this->_iAnimationSpeed;
	}
}
?>