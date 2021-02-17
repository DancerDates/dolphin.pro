<?php
bx_import('BxDolConfig');

class SkSocialInviterConfig extends BxDolConfig {

	private $_iApiAppId;
	private	$_sApiServiceUrl;
	private $_sApiLoginUrl;

	function SkSocialInviterConfig($aModule) {
	    parent::__construct($aModule);
		$this->_sApiLoginUrl = 'https://api2.socialall.io/login/';
		$this->_sApiServiceUrl = 'https://api2.socialall.io/';
		$this->_sAppId = getParam('sk_socialall_settings_app_id');
		$this->_sSecretKey = getParam('sk_socialall_settings_secret_key'); 
	}
	
	function getAppId(){
		return $this->_sAppId;
	}
	function getSecretKey(){
		return $this->_sSecretKey;
	}
	function getApiLoginUrl(){
		return $this->_sApiLoginUrl;
	}
	function getApiServiceUrl(){
		return $this->_sApiServiceUrl;
	}
}

?>
