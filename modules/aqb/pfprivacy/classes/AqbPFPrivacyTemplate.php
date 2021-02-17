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

bx_import('BxDolModuleTemplate');

class AqbPFPrivacyTemplate extends BxDolModuleTemplate {
	function __construct(&$oConfig, &$oDb) {
	    parent::__construct($oConfig, $oDb);
	}

	function getPageCode(&$aParams) {
		global $_page;
		global $_page_cont;

		$iIndex = isset($aParams['index']) ? (int)$aParams['index'] : 0;
		$_page['name_index'] = $iIndex;
		$_page['js_name'] = isset($aParams['js']) ? $aParams['js'] : '';
		$_page['css_name'] = isset($aParams['css']) ? $aParams['css'] : '';
		$_page['extra_js'] = isset($aParams['extra_js']) ? $aParams['extra_js'] : '';

		check_logged();

		if(isset($aParams['content']))
			foreach($aParams['content'] as $sKey => $sValue)
				$_page_cont[$iIndex][$sKey] = $sValue;

		if(isset($aParams['title']['page']))
			$this->setPageTitle($aParams['title']['page']);
        if(isset($aParams['title']['header']))
            $GLOBALS['oTopMenu']->setCustomSubHeader($aParams['title']['header']);
		if(isset($aParams['title']['block']))
			$this->setPageMainBoxTitle($aParams['title']['block']);

		if(isset($aParams['breadcrumb']) && method_exists($GLOBALS['oTopMenu'], 'setCustomBreadcrumbs'))
			$GLOBALS['oTopMenu']->setCustomBreadcrumbs($aParams['breadcrumb']);

        if(isset($aParams['actions']) && method_exists($GLOBALS['oTopMenu'], 'setCustomSubActions')) {
            $aParams = array(
            	'BaseUri' => $this->_oConfig->getBaseUri()
            );
            $GLOBALS['oTopMenu']->setCustomSubActions($aParams, $this->_oConfig->getUri() . '-header');
        }

		PageCode($this);
	}

	function getPageCodeAdmin(&$aParams) {
		global $_page;
		global $_page_cont;

		$iIndex = isset($aParams['index']) ? (int)$aParams['index'] : 9;
		$_page['name_index'] = $iIndex;
		$_page['js_name'] = isset($aParams['js']) ? $aParams['js'] : '';
		$_page['css_name'] = isset($aParams['css']) ? $aParams['css'] : '';
		$_page['header'] = isset($aParams['title']['page']) ? $aParams['title']['page'] : '';

		if(isset($aParams['content']))
			foreach($aParams['content'] as $sKey => $sValue)
				$_page_cont[$iIndex][$sKey] = $sValue;

		PageCodeAdmin();
	}

	function getJsInclude($sType, $bWrapped = false) {
		$sJsClass = $this->_oConfig->getJsClass($sType);
		$sJsObject = $this->_oConfig->getJsObject($sType);

		$aParams = array(
			'sActionUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri(),
			'sObjName' => $sJsObject,
			'sAnimationEffect' => $this->_oConfig->getAnimationEffect(),
			'iAnimationSpeed' => $this->_oConfig->getAnimationSpeed(),
			'oHtmlIds' => $this->_oConfig->getHtmlId()
		);

		$sContent = "var " . $sJsObject . " = new " . $sJsClass . "(" . json_encode($aParams) . ")";
		return $bWrapped ? $this->_wrapInTagJsCode($sContent) : $sContent;
	}
}
?>