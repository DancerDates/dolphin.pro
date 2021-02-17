<?php
bx_import('BxDolConfig');

class SkSocialLoginConfig extends BxDolConfig {

	var $_sApiAppId;
	var	$_sApiServiceUrl;
	var $_sApiLoginUrl;
	var $_sApiSecretKey;

	function SkSocialLoginConfig($aModule) {
	    parent::BxDolConfig($aModule);
				
		$this->_sApiLoginUrl = 'https://api2.socialall.io/login/';
		$this->_sApiServiceUrl = 'https://api2.socialall.io/';
		$this->_sApiAppId = getParam('sk_social_login_app_id');
		$this->_sApiSecretKey = getParam('sk_social_login_secret_key');
			
	}
	
	function getAppId(){
		return $this->_sApiAppId;
	}
	function getSecretKey(){
		return $this->_sApiSecretKey;
	}
	function getApiLoginUrl(){
		return $this->_sApiLoginUrl;
	}
	function getApiServiceUrl(){
		return $this->_sApiServiceUrl;
	}
}

?>
