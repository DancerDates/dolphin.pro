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

bx_import('BxTemplFormView');
bx_import('BxTemplSearchResult');
bx_import('BxTemplSearchProfile');
bx_import('BxDolTwigTemplate');
bx_import('BxDolParams');


class AqbEVUTemplate extends BxDolModuleTemplate {
	
	/**
	 * Constructor
	 */
	
	function __construct(&$oConfig, &$oDb) {
	    parent::__construct($oConfig, $oDb);
	}
	
	function getExtendedVideoTemplate(&$aVarsUpload){
		return $this -> parseHtmlByName('form_video_upload.html', $aVarsUpload);
	}
}
?>