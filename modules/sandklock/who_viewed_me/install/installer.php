<?php
bx_import('BxDolInstaller');

class SkWhoViewedMeInstaller extends BxDolInstaller {

	function __construct($aConfig) {
		parent::__construct($aConfig);
	}

	function install($aParams) {
		$aResult = parent::install($aParams);
		return $aResult;
	}
}